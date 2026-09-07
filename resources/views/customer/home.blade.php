@extends('layouts.customer')

@push('html_attr')
    translate="no"
@endpush

@section('content')
    @php
        $promoCount = $promoProducts->count();
        // Real deadline for the promo countdown: the soonest discount that expires.
        $promoDeadline = $promoProducts
            ->map(fn ($p) => optional($p->active_discount)->end_date)
            ->filter()
            ->sort()
            ->first();
    @endphp

    {{-- ==================== HERO ==================== --}}
    @if($heroSlides->count() > 0)
        <section id="hero-section" class="relative bg-white">
            <div class="hero-carousel relative max-w-7xl mx-auto md:px-6 lg:px-8 md:pt-6">
                @foreach($heroSlides as $slide)
                    <div class="hero-slide {{ $loop->first ? 'active' : '' }}" data-slide-index="{{ $loop->index }}">
                        @if($slide->type === 'text')
                            <div class="md:rounded-3xl overflow-hidden bg-gradient-to-br from-brand-600 to-brand-700 text-white py-12 md:py-20 px-6 md:px-12">
                                <div class="max-w-2xl">
                                    <h1 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight">
                                        {!! nl2br(e($slide->title ?: 'Susu segar, diantar hari ini')) !!}
                                    </h1>
                                    @if($slide->subtitle)
                                        <p class="mt-4 text-base md:text-lg text-brand-100 max-w-xl">{{ $slide->subtitle }}</p>
                                    @endif
                                    <a href="#products"
                                        class="mt-7 inline-flex items-center gap-2 h-12 px-7 rounded-xl bg-white text-brand-700 font-bold hover:bg-brand-50 transition-colors">
                                        Belanja Sekarang
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="relative h-[220px] sm:h-[320px] md:h-[420px] md:rounded-3xl overflow-hidden bg-slate-900">
                                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}"
                                    class="w-full h-full object-cover" {{ $loop->first ? '' : 'loading=lazy' }}>
                                @if($slide->title)
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/10 to-transparent flex items-end">
                                        <h2 class="p-6 md:p-10 text-2xl md:text-4xl font-extrabold text-white max-w-2xl leading-tight">
                                            {{ $slide->title }}
                                        </h2>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($heroSlides->count() > 1)
                    <button type="button" onclick="prevSlide()" aria-label="Slide sebelumnya"
                        class="hidden md:flex absolute left-6 lg:left-12 top-1/2 -translate-y-1/2 w-11 h-11 items-center justify-center bg-white/90 hover:bg-white text-slate-700 rounded-full shadow-lg z-10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" onclick="nextSlide()" aria-label="Slide berikutnya"
                        class="hidden md:flex absolute right-6 lg:right-12 top-1/2 -translate-y-1/2 w-11 h-11 items-center justify-center bg-white/90 hover:bg-white text-slate-700 rounded-full shadow-lg z-10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                        @foreach($heroSlides as $slide)
                            <button type="button" onclick="goToSlide({{ $loop->index }})"
                                aria-label="Ke slide {{ $loop->iteration }}"
                                class="slide-dot h-2 rounded-full bg-white/60 hover:bg-white transition-all {{ $loop->first ? 'active w-6 bg-white' : 'w-2' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <style>
                .hero-slide { display: none; }
                .hero-slide.active { display: block; animation: fadeInUp .4s ease-out; }
                .slide-dot.active { background: #fff; width: 1.5rem; }
            </style>
        </section>
    @endif

    {{-- ==================== TRUST STRIP ==================== --}}
    <section class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex gap-6 overflow-x-auto hide-scrollbar text-xs md:text-sm text-slate-600">
                <span class="flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Pengiriman cepat
                </span>
                <span class="flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Produk selalu segar
                </span>
                <span class="flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    QRIS, transfer &amp; COD
                </span>
                <span class="flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/></svg>
                    Gratis ongkir area layanan
                </span>
            </div>
        </div>
    </section>

    {{-- ==================== PROMO RAIL ==================== --}}
    @if($promoCount > 0)
        <section id="promo-section" class="py-6 md:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-gradient-to-br from-rose-600 to-rose-500 p-5 md:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                                <svg class="w-6 h-6 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                Promo Hari Ini
                            </h2>
                            @if($promoDeadline)
                                <div id="promo-countdown"
                                    data-deadline="{{ $promoDeadline->toIso8601String() }}"
                                    class="hidden sm:flex items-center gap-1 bg-white/20 px-3 py-1.5 rounded-lg text-white font-mono font-bold text-sm backdrop-blur-sm">
                                    <span data-unit="days" class="hidden"></span>
                                    <span data-unit="hours">00</span>:<span data-unit="mins">00</span>:<span data-unit="secs">00</span>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="selectCategory('promo', 'Promo')"
                            class="text-white/90 hover:text-white font-semibold text-sm flex items-center gap-1">
                            Lihat semua {{ $promoCount }} promo
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="flex overflow-x-auto gap-3 md:gap-4 pb-1 snap-x hide-scrollbar">
                        @foreach($promoProducts->take(8) as $product)
                            @php
                                $saving = $product->price - $product->discounted_price;
                                $percentOff = $product->price > 0 ? round(($saving / $product->price) * 100) : 0;
                                $stock = (int) $product->stock;
                                $lowStockAt = (int) ($product->low_stock_threshold ?: 5);
                            @endphp
                            <div class="flex-none w-[150px] md:w-[190px] bg-white rounded-2xl overflow-hidden snap-start flex flex-col">
                                <a href="{{ route('products.show', $product->id) }}" class="relative block aspect-square bg-slate-50 overflow-hidden group">
                                    <img src="{{ $product->image ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image) : 'https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image' }}"
                                        alt="{{ $product->name }}" loading="lazy" decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image';">
                                    <span class="absolute top-2 left-2 px-2 py-1 rounded-lg bg-rose-600 text-white text-[11px] font-bold shadow-sm">-{{ $percentOff }}%</span>
                                    @if($stock <= 0)
                                        <span class="absolute inset-0 flex items-center justify-center bg-white/60">
                                            <span class="px-3 py-1.5 rounded-lg bg-slate-900/85 text-white text-xs font-bold">Stok Habis</span>
                                        </span>
                                    @endif
                                </a>
                                <div class="p-3 flex flex-col flex-1">
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="text-[13px] font-semibold text-slate-900 line-clamp-2 leading-snug hover:text-brand-600">{{ $product->name }}</a>
                                    <p class="mt-1.5 text-[11px] text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <p class="text-base font-bold text-rose-600 leading-tight">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>

                                    @if($stock > 0 && $stock <= $lowStockAt)
                                        <p class="mt-1 text-[11px] font-semibold text-amber-600">Sisa {{ $stock }} item</p>
                                    @endif

                                    <div class="mt-auto pt-3">
                                        @if($stock <= 0)
                                            <button type="button" disabled class="w-full h-9 rounded-xl bg-slate-100 text-slate-400 text-sm font-semibold cursor-not-allowed">Habis</button>
                                        @else
                                            <button type="button" onclick="addToCart({{ $product->id }}, 1, this)"
                                                class="w-full h-9 rounded-xl bg-rose-600 text-white text-sm font-bold hover:bg-rose-700 active:scale-[0.98] transition-all">
                                                + Keranjang
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ==================== CATEGORIES ==================== --}}
    <section class="bg-white border-y border-slate-100 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-base md:text-lg font-bold text-slate-900 mb-4">Kategori</h2>
            <div class="flex overflow-x-auto gap-3 pb-1 hide-scrollbar snap-x" role="group" aria-label="Filter kategori">
                <button type="button" data-category="all" onclick="selectCategory('all', 'Semua Produk')"
                    class="category-pill active flex-none h-10 px-4 rounded-full border text-sm font-semibold transition-colors snap-start">
                    Semua
                </button>
                @if($promoCount > 0)
                    <button type="button" data-category="promo" onclick="selectCategory('promo', 'Promo')"
                        class="category-pill flex-none h-10 px-4 rounded-full border text-sm font-semibold transition-colors snap-start">
                        Promo ({{ $promoCount }})
                    </button>
                @endif
                @foreach($categories as $category)
                    <button type="button" data-category="{{ $category->id }}"
                        onclick="selectCategory('{{ $category->id }}', @js($category->name))"
                        class="category-pill flex-none h-10 px-4 rounded-full border text-sm font-semibold transition-colors snap-start whitespace-nowrap">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>
        <style>
            .category-pill { border-color: #e2e8f0; color: #475569; background: #fff; }
            .category-pill:hover { border-color: #bfdbfe; color: #2563eb; background: #eff6ff; }
            .category-pill.active { background: #2563eb; border-color: #2563eb; color: #fff; }
        </style>
    </section>

    {{-- ==================== PRODUCT GRID ==================== --}}
    <section id="products" class="py-8 md:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-4 mb-5">
                <div>
                    <h2 id="grid-title" class="text-xl md:text-2xl font-bold text-slate-900">Semua Produk</h2>
                    <p id="grid-count" class="text-sm text-slate-500 mt-1">{{ $totalProducts }} produk tersedia</p>
                </div>

                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-2 h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" id="in-stock-only" onchange="applyFilters()"
                            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Ready stok
                    </label>
                    <label for="sort-select" class="sr-only">Urutkan produk</label>
                    <select id="sort-select" onchange="applyFilters()"
                        class="h-10 pl-3 pr-8 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none">
                        <option value="popular">Terpopuler</option>
                        <option value="price_asc">Harga Terendah</option>
                        <option value="price_desc">Harga Tertinggi</option>
                        <option value="discount">Diskon Terbesar</option>
                        <option value="newest">Terbaru</option>
                    </select>
                </div>
            </div>

            <!-- Active filter chips -->
            <div id="active-filters" class="hidden flex-wrap items-center gap-2 mb-5"></div>

            <div id="product-grid-container" class="min-h-[400px]">
                @include('customer.partials.product_grid', ['products' => $products])
            </div>

            <!-- Skeleton shown while filtering -->
            <div id="grid-skeleton" class="hidden grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5">
                @for($i = 0; $i < 8; $i++)
                    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden">
                        <div class="aspect-square skeleton"></div>
                        <div class="p-4 space-y-2">
                            <div class="h-3 rounded skeleton"></div>
                            <div class="h-3 w-2/3 rounded skeleton"></div>
                            <div class="h-8 rounded-xl skeleton mt-3"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="text-center mt-10">
                <button type="button" id="load-more" onclick="loadMore()"
                    class="{{ $hasMore ? '' : 'hidden' }} px-8 h-12 rounded-xl bg-white text-brand-600 border-2 border-brand-600 font-bold hover:bg-brand-600 hover:text-white transition-colors">
                    Muat Lebih Banyak
                </button>
                <p id="all-loaded" class="{{ $hasMore ? 'hidden' : '' }} text-sm text-slate-400">Semua produk sudah ditampilkan</p>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    /* ---------------- Hero carousel ---------------- */
    (function () {
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slide-dot');
        if (slides.length === 0) return;

        let index = 0;
        let timer = null;

        window.showSlide = function (next) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => { dot.classList.remove('active', 'w-6'); dot.classList.add('w-2'); });
            slides[next].classList.add('active');
            if (dots[next]) { dots[next].classList.add('active', 'w-6'); dots[next].classList.remove('w-2'); }
            index = next;
        };
        window.nextSlide = () => { window.showSlide((index + 1) % slides.length); restart(); };
        window.prevSlide = () => { window.showSlide((index - 1 + slides.length) % slides.length); restart(); };
        window.goToSlide = (i) => { window.showSlide(i); restart(); };

        function start() { if (slides.length > 1) timer = setInterval(() => window.showSlide((index + 1) % slides.length), 6000); }
        function restart() { clearInterval(timer); start(); }

        const carousel = document.querySelector('.hero-carousel');
        carousel?.addEventListener('mouseenter', () => clearInterval(timer));
        carousel?.addEventListener('mouseleave', start);

        // Swipe on touch devices
        let startX = null;
        carousel?.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        carousel?.addEventListener('touchend', e => {
            if (startX === null) return;
            const delta = e.changedTouches[0].clientX - startX;
            if (Math.abs(delta) > 50) delta < 0 ? window.nextSlide() : window.prevSlide();
            startX = null;
        });

        start();
    })();

    /* ---------------- Promo countdown (real discount deadline) ---------------- */
    (function () {
        const el = document.getElementById('promo-countdown');
        if (!el) return;
        const deadline = new Date(el.dataset.deadline).getTime();
        if (Number.isNaN(deadline)) { el.remove(); return; }

        function tick() {
            const remaining = deadline - Date.now();
            if (remaining <= 0) { el.textContent = 'Berakhir'; clearInterval(interval); return; }

            const days = Math.floor(remaining / 86400000);
            const hours = Math.floor((remaining % 86400000) / 3600000);
            const mins = Math.floor((remaining % 3600000) / 60000);
            const secs = Math.floor((remaining % 60000) / 1000);

            const dayEl = el.querySelector('[data-unit="days"]');
            if (days > 0) { dayEl.classList.remove('hidden'); dayEl.textContent = `${days}h `; }
            el.querySelector('[data-unit="hours"]').textContent = String(hours).padStart(2, '0');
            el.querySelector('[data-unit="mins"]').textContent = String(mins).padStart(2, '0');
            el.querySelector('[data-unit="secs"]').textContent = String(secs).padStart(2, '0');
        }

        tick();
        const interval = setInterval(tick, 1000);
    })();

    /* ---------------- Filtering, sorting, pagination ---------------- */
    const filters = { category: 'all', categoryLabel: 'Semua Produk', q: '', sort: 'popular', inStock: false, page: 1 };
    let searchTimeout = null;

    function buildParams(page) {
        return new URLSearchParams({
            category: filters.category,
            q: filters.q,
            sort: filters.sort,
            in_stock: filters.inStock ? '1' : '0',
            page: String(page),
        });
    }

    function readControls() {
        filters.sort = document.getElementById('sort-select')?.value || 'popular';
        filters.inStock = document.getElementById('in-stock-only')?.checked || false;
    }

    function renderActiveFilters() {
        const bar = document.getElementById('active-filters');
        if (!bar) return;

        const chips = [];
        if (filters.category !== 'all') chips.push({ label: filters.categoryLabel, clear: () => selectCategory('all', 'Semua Produk') });
        if (filters.q.trim() !== '') chips.push({ label: `"${filters.q.trim()}"`, clear: clearSearch });
        if (filters.inStock) chips.push({ label: 'Ready stok', clear: () => { document.getElementById('in-stock-only').checked = false; applyFilters(); } });

        if (chips.length === 0) { bar.classList.add('hidden'); bar.innerHTML = ''; return; }

        bar.classList.remove('hidden');
        bar.classList.add('flex');
        bar.innerHTML = '';
        chips.forEach(chip => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'inline-flex items-center gap-1.5 h-8 pl-3 pr-2 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold border border-brand-100 hover:bg-brand-100 transition-colors';
            el.innerHTML = `${escapeHtml(chip.label)}<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`;
            el.onclick = chip.clear;
            bar.appendChild(el);
        });

        const reset = document.createElement('button');
        reset.type = 'button';
        reset.className = 'text-xs font-semibold text-slate-500 hover:text-slate-700 underline underline-offset-2';
        reset.textContent = 'Hapus semua filter';
        reset.onclick = resetFilters;
        bar.appendChild(reset);
    }

    async function applyFilters() {
        readControls();
        filters.page = 1;

        const container = document.getElementById('product-grid-container');
        const skeleton = document.getElementById('grid-skeleton');
        const loadMoreBtn = document.getElementById('load-more');
        const allLoaded = document.getElementById('all-loaded');

        container.classList.add('hidden');
        skeleton.classList.remove('hidden');
        loadMoreBtn.classList.add('hidden');
        allLoaded.classList.add('hidden');

        try {
            const response = await fetch(`/products/search?${buildParams(1)}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();

            container.innerHTML = data.html;
            document.getElementById('grid-title').textContent = filters.q.trim() !== ''
                ? `Hasil untuk "${filters.q.trim()}"`
                : filters.categoryLabel;
            document.getElementById('grid-count').textContent = `${data.total} produk ditemukan`;

            loadMoreBtn.classList.toggle('hidden', !data.has_more);
            allLoaded.classList.toggle('hidden', data.has_more || data.total === 0);
        } catch (error) {
            console.error('Gagal memuat produk:', error);
            showNotification('Gagal memuat produk, coba lagi', 'error');
        } finally {
            skeleton.classList.add('hidden');
            container.classList.remove('hidden');
            renderActiveFilters();
        }
    }

    async function loadMore() {
        const button = document.getElementById('load-more');
        const grid = document.getElementById('product-grid');
        if (!grid) return;

        const original = button.textContent;
        button.disabled = true;
        button.textContent = 'Memuat...';

        try {
            const params = buildParams(filters.page + 1);
            params.set('append', '1');
            const response = await fetch(`/products/search?${params}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();

            grid.insertAdjacentHTML('beforeend', data.html);
            filters.page += 1;

            button.classList.toggle('hidden', !data.has_more);
            document.getElementById('all-loaded').classList.toggle('hidden', data.has_more);
        } catch (error) {
            console.error(error);
            showNotification('Gagal memuat produk berikutnya', 'error');
        } finally {
            button.disabled = false;
            button.textContent = original;
        }
    }

    function selectCategory(id, name) {
        filters.category = String(id);
        filters.categoryLabel = name;

        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.dataset.category === String(id));
        });

        applyFilters();
        document.getElementById('products')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Called by the header search inputs (see the layout).
    function searchProducts(source = 'desktop') {
        const desktop = document.getElementById('product-search');
        const mobile = document.getElementById('mobile-product-search');
        const value = source === 'desktop' ? (desktop?.value ?? '') : (mobile?.value ?? '');

        filters.q = value;
        if (desktop) desktop.value = value;
        if (mobile) mobile.value = value;

        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 400);
    }

    function clearSearch() {
        const desktop = document.getElementById('product-search');
        const mobile = document.getElementById('mobile-product-search');
        if (desktop) desktop.value = '';
        if (mobile) mobile.value = '';
        filters.q = '';
        applyFilters();
    }

    function resetFilters() {
        filters.category = 'all';
        filters.categoryLabel = 'Semua Produk';
        filters.q = '';
        const stock = document.getElementById('in-stock-only');
        if (stock) stock.checked = false;
        const sort = document.getElementById('sort-select');
        if (sort) sort.value = 'popular';
        const desktop = document.getElementById('product-search');
        const mobile = document.getElementById('mobile-product-search');
        if (desktop) desktop.value = '';
        if (mobile) mobile.value = '';
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.dataset.category === 'all');
        });
        applyFilters();
    }

    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (event) {
            const target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
@endpush
