<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get products from database with discounts eager loaded
        $products = MdxProduct::with(['discounts', 'categories'])->orderBy('created_at', 'desc')->get();
        // Get all categories
        $categories = \App\Models\MdxCategory::orderBy('name')->get();
        // Get active hero slides
        $heroSlides = HeroSlide::active()->ordered()->get();

        return view('customer.home', compact('products', 'categories', 'heroSlides'));
    }

    public function search(Request $request)
    {
        $query = MdxProduct::with(['discounts', 'categories'])->orderBy('created_at', 'desc');

        // Filter by Category
        if ($request->has('category') && $request->category !== 'all') {
            if ($request->category === 'promo') {
                $query->whereHas('discounts', function ($q) {
                    $q->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
                });
            } else {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('mdx_categories.id', $request->category);
                });
            }
        }

        $products = $query->get();

        // Filter by Search Query with Typo Tolerance
        if ($request->has('q') && !empty(trim($request->q))) {
            $term = strtolower(trim($request->q));
            
            $products = $products->filter(function ($product) use ($term) {
                $name = strtolower($product->name);
                
                if ($name === $term || str_starts_with($name, $term) || str_contains($name, $term)) {
                    return true;
                }
                
                similar_text($term, $name, $percent);
                if ($percent > 40) return true;
                
                $words = explode(' ', $name);
                foreach ($words as $word) {
                    if (strlen($word) > 2) {
                        similar_text($term, $word, $wordPercent);
                        if ($wordPercent > 50) return true;
                    }
                }
                
                return false;
            })->values();
        }

        return view('customer.partials.product_grid', compact('products'))->render();
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
        $allProducts = MdxProduct::with('discounts')->where('is_active', true)->get();

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
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => asset(str_starts_with($product->image, 'storage/') ? $product->image : 'storage/' . $product->image),
                'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
                'has_discount' => $hasDiscount,
                'original_price_formatted' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'product' => $product,
                'score' => $product->match_score // Optional, for debugging
            ];
        });

        return response()->json($results);
    }
}
