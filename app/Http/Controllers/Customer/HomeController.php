<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Number of products rendered per "load more" batch.
     */
    private const PER_PAGE = 12;

    public function index()
    {
        $catalogue = $this->catalogue();

        $categories = \App\Models\MdxCategory::orderBy('name')->get();
        $heroSlides = HeroSlide::active()->ordered()->get();

        // Products currently on promo, used by the flash-sale / weekly-promo rails.
        $promoProducts = $catalogue
            ->filter(fn ($product) => (bool) $product->active_discount)
            ->values();

        $totalProducts = $catalogue->count();
        $products = $catalogue->take(self::PER_PAGE)->values();
        $hasMore = $totalProducts > $products->count();

        return view('customer.home', compact(
            'products',
            'promoProducts',
            'categories',
            'heroSlides',
            'totalProducts',
            'hasMore'
        ));
    }

    public function search(Request $request)
    {
        $products = $this->catalogue();

        // Filter by category ("all", "promo" or a category id)
        $category = $request->input('category', 'all');
        if ($category === 'promo') {
            $products = $products->filter(fn ($product) => (bool) $product->active_discount);
        } elseif ($category !== 'all' && $category !== null && $category !== '') {
            $products = $products->filter(
                fn ($product) => $product->categories->contains('id', (int) $category)
            );
        }

        // Filter by search query, with typo tolerance
        if (!empty(trim((string) $request->input('q')))) {
            $term = strtolower(trim($request->q));

            $products = $products->filter(function ($product) use ($term) {
                $name = strtolower($product->name);

                if (str_contains($name, $term)) {
                    return true;
                }

                similar_text($term, $name, $percent);
                if ($percent > 40) {
                    return true;
                }

                foreach (explode(' ', $name) as $word) {
                    if (strlen($word) > 2) {
                        similar_text($term, $word, $wordPercent);
                        if ($wordPercent > 50) {
                            return true;
                        }
                    }
                }

                return false;
            });
        }

        // Only in-stock products, when asked for
        if ($request->boolean('in_stock')) {
            $products = $products->filter(fn ($product) => $product->stock > 0);
        }

        $products = $this->sortProducts($products, $request->input('sort', 'popular'))->values();

        $total = $products->count();
        $page = max(1, (int) $request->input('page', 1));
        $paged = $products->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();
        $hasMore = $total > $page * self::PER_PAGE;

        $html = view('customer.partials.product_grid', [
            'products' => $paged,
            'bare' => $request->boolean('append'),
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $total,
            'has_more' => $hasMore,
            'page' => $page,
        ]);
    }

    /**
     * All sellable products with the relations the cards need.
     */
    private function catalogue()
    {
        return MdxProduct::with(['discounts', 'categories'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function sortProducts($products, string $sort)
    {
        return match ($sort) {
            'price_asc' => $products->sortBy(fn ($p) => (float) $p->discounted_price),
            'price_desc' => $products->sortByDesc(fn ($p) => (float) $p->discounted_price),
            'newest' => $products->sortByDesc('created_at'),
            'discount' => $products->sortByDesc(function ($p) {
                if (!$p->active_discount) {
                    return 0;
                }
                return $p->price > 0 ? (($p->price - $p->discounted_price) / $p->price) * 100 : 0;
            }),
            // "Terpopuler": in-stock and discounted products first, then newest.
            default => $products->sortByDesc(
                fn ($p) => ($p->stock > 0 ? 1000 : 0) + ($p->active_discount ? 100 : 0)
            ),
        };
    }

    public function show($id)
    {
        $product = MdxProduct::with(['discounts', 'categories'])->findOrFail($id);
        
        // Similar products in the same category
        $relatedProducts = MdxProduct::with(['discounts'])
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('mdx_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }

    public function suggest(Request $request)
    {
        if (!$request->has('q') || empty(trim($request->q))) {
            return response()->json([]);
        }

        $term = strtolower(trim($request->q));
        
        // Fetch all products with active discounts eager loaded
        $allProducts = MdxProduct::with('discounts')->get();

        // Score each product based on how closely it matches the search term
        $scoredProducts = $allProducts->map(function ($product) use ($term) {
            $name = strtolower($product->name);
            $score = 0;

            // 1. Exact match gets highest score
            if ($name === $term) {
                $score = 100;
            } 
            // 2. Starts with gets very high score (e.g. "ultr" -> "Ultra Milk")
            elseif (str_starts_with($name, $term)) {
                $score = 90;
            }
            // 3. Contains as a distinct word gets high score (e.g. "milk" in "Ultra Milk")
            elseif (str_contains(' ' . $name . ' ', ' ' . $term . ' ')) {
                $score = 85;
            }
            // 4. Contains substring gets medium-high score (e.g. "il" in "Ultra Milk")
            elseif (str_contains($name, $term)) {
                $score = 80;
            }
            // 5. Typo tolerance / Fuzzy match using similar_text (Levenshtein alternative)
            else {
                similar_text($term, $name, $percent);
                $score = $percent;
                
                // If it's a multi-word product name, check similarity against individual words
                $words = explode(' ', $name);
                foreach ($words as $word) {
                    if (strlen($word) > 2) {
                        similar_text($term, $word, $wordPercent);
                        if ($wordPercent > $score) {
                            $score = $wordPercent;
                        }
                    }
                }
            }

            $product->match_score = $score;
            return $product;
        });

        // Filter products with a score > 40% similarity, sort by score descending, take top 5
        $products = $scoredProducts->filter(function($p) {
            return $p->match_score > 40; 
        })->sortByDesc('match_score')->take(5)->values();

        $results = $products->map(function($product) {
            $price = $product->price;
            $hasDiscount = false;
            
            if ($product->active_discount) {
                $hasDiscount = true;
                if ($product->active_discount->discount_type === 'PERCENTAGE') {
                    $price = $price * (1 - ($product->active_discount->discount_value / 100));
                } else {
                    $price = max(0, $price - $product->active_discount->discount_value);
                }
            }
            
            if (!$product->image) {
                $image = 'https://placehold.co/80x80/f1f5f9/94a3b8?text=%20';
            } elseif (str_starts_with($product->image, 'http')) {
                $image = $product->image;
            } else {
                $image = asset(str_starts_with($product->image, 'storage/') ? $product->image : 'storage/' . $product->image);
            }

            // Only the fields the dropdown renders: this endpoint is public, so
            // internal columns such as cost_price must not be exposed.
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $image,
                'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
                'has_discount' => $hasDiscount,
                'original_price_formatted' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'in_stock' => $product->stock > 0,
            ];
        });

        return response()->json($results);
    }
}
