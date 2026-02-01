{{-- index.blade.php --}}
@extends('layouts.front')

@php
    // Keep static assets for non-dynamic blocks on a single default path for now.
    // (Hero is backend-driven via $categoryHeroImage; product sections are backend-driven via $categorySections.)
    $assetBase = 'assets/front/images/theme4/category/electrical';

    // Featured banner block is backend-driven via $featuredBanners (admin-managed).
    $featuredBanners = $featuredBanners ?? collect();
    if (is_array($featuredBanners)) {
        $featuredBanners = collect($featuredBanners);
    }

    $featuredBanner = null;
    if ($featuredBanners instanceof \Illuminate\Support\Collection) {
        $catId = !empty($cat) ? (int) $cat->id : null;

        // Prefer category-specific banners when category_id is present.
        if ($catId) {
            $featuredBanner = $featuredBanners->first(function ($b) use ($catId) {
                return (string) ($b->category_id ?? '') === (string) $catId;
            });
        }

        // Fallback: global banner (no category_id or empty).
        if (!$featuredBanner) {
            $featuredBanner = $featuredBanners->first(function ($b) {
                return empty($b->category_id);
            });
        }

        // Last resort: first banner.
        if (!$featuredBanner) {
            $featuredBanner = $featuredBanners->first();
        }
    }

    // Explore tiles are backend-driven (from controller). Normalize to a collection for the view.
    $exploreTiles = $exploreTiles ?? collect();
    if (is_array($exploreTiles)) {
        $exploreTiles = collect($exploreTiles);
    }

    // Filter UI state is backend-driven (from controller). Use it to reflect checked/selected values.
    $filterState = $filterState ?? [];
    $selectedSort = $filterState['sort'] ?? null;
    $selectedPageby = $filterState['pageby'] ?? null;
    $selectedRatingMin = $filterState['rating_min'] ?? null;
    $selectedAvailability = (array) ($filterState['availability'] ?? []);
    $selectedMin = $filterState['min'] ?? null;
    $selectedMax = $filterState['max'] ?? null;
    $sliderMinValue = is_numeric($selectedMin) ? $selectedMin : $gs->min_price;
    $sliderMaxValue = is_numeric($selectedMax) ? $selectedMax : $gs->max_price;

    // Attribute filters are backend-driven (from controller). Normalize to a collection for the view.
    $filterAttributes = $filterAttributes ?? collect();
    if (is_array($filterAttributes)) {
        $filterAttributes = collect($filterAttributes);
    }

    // Partners are backend-driven (admin-managed). Normalize to a collection for the view.
    $partners = $partners ?? collect();
    if (is_array($partners)) {
        $partners = collect($partners);
    }

    // Category hero sliders are backend-driven (from controller). Normalize to a collection for the view.
    $categorySliders = $categorySliders ?? collect();
    if (is_array($categorySliders)) {
        $categorySliders = collect($categorySliders);
    }

    $heroSlides = $categorySliders instanceof \Illuminate\Support\Collection
        ? $categorySliders->filter(function ($slide) {
            return !empty($slide->photo);
        })->values()
        : collect();
    $heroSlidesCount = $heroSlides->count();
    $heroHasMultipleSlides = $heroSlidesCount > 1;
    // Category carousel exists only when we have real slider rows (admin-managed).
    $hasCategoryCarousel =
        isset($categorySliders)
        && $categorySliders instanceof \Illuminate\Support\Collection
        && $categorySliders->count() > 0;

    // Do not render hero unless actual carousel slide data exists (no placeholder hero).
    $heroShouldRender = $hasCategoryCarousel && $heroSlidesCount > 0;
    $mainNoHeroClass = !$heroShouldRender ? 't4-main--no-hero' : '';

    // Category sections are backend-driven (from controller). No hardcoded section titles in Blade.
    $categorySections = $categorySections ?? [];
    $sectionsData = collect();
    if ($categorySections instanceof \Illuminate\Support\Collection) {
        $sectionsData = $categorySections;
    } elseif (is_array($categorySections)) {
        $sectionsData = collect($categorySections);
    }

    $productsList = $prods instanceof \Illuminate\Pagination\LengthAwarePaginator ? $prods->getCollection() : $prods;

    // Named collections passed from controller (Theme4-only)
    $bestSellers = $t4BestSellers ?? collect();
    $topRecommendations = $t4TopRecommendations ?? collect();
    $featuredProducts = $t4FeaturedProducts ?? collect();
@endphp

@section('css')
    <style>
        /* Skeletons: shown only while a navigation is in-flight (body.t4-loading) */
        .t4-skeleton-only {
            display: none !important;
        }

        body.t4-loading .t4-skeleton-only {
            display: block !important;
        }

        body.t4-loading .t4-hide-while-loading {
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* Keep visual balance when hero is intentionally not rendered */
        .t4-category-page .t4-main.t4-main--no-hero {
            padding-top: 16px;
        }

        .t4-skeleton {
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.06) 25%, rgba(0, 0, 0, 0.12) 37%, rgba(0, 0, 0, 0.06) 63%);
            background-size: 400% 100%;
            animation: t4-skeleton-shimmer 1.2s ease-in-out infinite;
            border-radius: 10px;
        }

        @keyframes t4-skeleton-shimmer {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: 0 0;
            }
        }

        .t4-skel-line {
            height: 14px;
            border-radius: 8px;
        }

        .t4-skel-line--title {
            height: 20px;
            width: 260px;
            margin-bottom: 14px;
        }

        .t4-skel-tile {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .t4-skel-tile__img {
            height: 96px;
            border-radius: 12px;
        }

        .t4-skel-tile__text {
            height: 14px;
            width: 70%;
        }

        .t4-skel-card__img {
            height: 160px;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .t4-skel-card__line {
            height: 12px;
            width: 85%;
            margin-bottom: 10px;
        }

        .t4-skel-card__line--short {
            width: 55%;
        }

        .t4-skel-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        @media (max-width: 991px) {
            .t4-skel-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .t4-skel-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        .t4-skel-slider {
            display: grid;
            gap: 14px;
            grid-auto-flow: column;
            grid-auto-columns: minmax(0, 220px);
            overflow: hidden;
        }
    </style>
@endsection


@section('content')

<div class="t4-category-page">
    @php
    $activeCategorySlug = request()->segment(2);
    $activeSubSlug = request()->segment(3);
@endphp


    {{-- FULL WIDTH CATEGORY STRIP --}}
    @php
        $t4Categories = $categories ?? collect();
    @endphp
    {{-- Category strip (shared Theme 4 partial) --}}
    @include('frontend.theme4.partials.category_strip', [
        't4Categories' => $t4Categories,
        'activeCategorySlug' => $activeCategorySlug,
        'sectionClass' => 't4-categories--category-page',
        'sliderClass' => 't4-cat-strip-slider',
        'itemClass' => '',
        'linkClass' => '',
        'iconClass' => '',
        'titleClass' => '',
    ])
  
    <div class="t4-container">
      <div class="t4-category-grid">
  
                {{-- Sidebar --}}
                <aside class="t4-sidebar">
                    <div class="t4-card t4-filter" id="t4-filter-drawer" role="dialog" aria-modal="true" aria-label="Filters" tabindex="-1">
                        <div class="t4-filter__top">
                            <h4 class="t4-filter__title">Filters</h4>
                            <button type="button" class="t4-filter__close" data-t4-filter-close aria-label="Close filters">×</button>
                        </div>

                        <div class="t4-filter__section">
                            <h5 class="t4-filter__label">Product Category</h5>
                            <ul class="t4-filter__list">
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('front.category', $category->slug) }}"
                                            class="{{ Request::segment(2) === $category->slug ? 'is-active' : '' }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Attribute filters (render ONLY when attributes exist) --}}
                        @if ($filterAttributes instanceof \Illuminate\Support\Collection ? $filterAttributes->isNotEmpty() : count($filterAttributes))
                            @foreach ($filterAttributes as $attribute)
                                @php
                                    $inputName = $attribute->input_name ?? null;
                                    $options = $attribute->attribute_options ?? collect();
                                    if (is_array($options)) {
                                        $options = collect($options);
                                    }
                                    $selected = $inputName ? (array) request()->query($inputName, []) : [];
                                @endphp

                                @if (!empty($inputName) && ($options instanceof \Illuminate\Support\Collection ? $options->isNotEmpty() : count($options)))
                                    <div class="t4-filter__section">
                                        <h5 class="t4-filter__label">{{ $attribute->name }}</h5>
                                        @foreach ($options as $option)
                                            @php $value = $option->name ?? null; @endphp
                                            @if (!empty($value))
                                                <label class="t4-check">
                                                    <input class="attribute-input" type="checkbox" name="{{ $inputName }}[]"
                                                        value="{{ $value }}" {{ in_array($value, $selected, true) ? 'checked' : '' }}>
                                                    {{ $value }}
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <div class="t4-filter__section">
                            <h5 class="t4-filter__label">Price Range</h5>
                            <div class="price-range">
                                <div class="d-none">
                                    <input id="start_value" type="number" name="min"
                                        value="{{ $sliderMinValue }}">
                                    <input id="end_value" type="number"
                                        value="{{ $sliderMaxValue }}">
                                    <input id="max_value" type="number" name="max" value="{{ $gs->max_price }}">
                                </div>
                                <div id="slider-range"></div>
                                <input type="text" id="amount" readonly class="range_output">
                            </div>
                            <button class="t4-btn t4-btn--primary" id="price_filter">Apply Filter</button>
                            <a class="t4-btn t4-btn--ghost" href="{{ route('front.category') }}">Clear All Filters</a>
                        </div>

                        <div class="t4-filter__section">
                            <h5 class="t4-filter__label">Customer Ratings</h5>
                            <label class="t4-check">
                                <input class="t4-filter-change" type="checkbox" name="rating_min" value="4"
                                    {{ (string) $selectedRatingMin === '4' ? 'checked' : '' }}>
                                4★ & above
                            </label>
                            <label class="t4-check">
                                <input class="t4-filter-change" type="checkbox" name="rating_min" value="3"
                                    {{ (string) $selectedRatingMin === '3' ? 'checked' : '' }}>
                                3★ & above
                            </label>
                            <label class="t4-check">
                                <input class="t4-filter-change" type="checkbox" name="rating_min" value="2"
                                    {{ (string) $selectedRatingMin === '2' ? 'checked' : '' }}>
                                2★ & above
                            </label>
                        </div>

                        <div class="t4-filter__section">
                            <h5 class="t4-filter__label">Availability</h5>
                            <label class="t4-check">
                                <input class="t4-filter-change" type="checkbox" name="availability[]" value="in_stock"
                                    {{ in_array('in_stock', $selectedAvailability, true) ? 'checked' : '' }}>
                                In Stock
                            </label>
                            <label class="t4-check">
                                <input class="t4-filter-change" type="checkbox" name="availability[]" value="out_of_stock"
                                    {{ in_array('out_of_stock', $selectedAvailability, true) ? 'checked' : '' }}>
                                Out of Stock
                            </label>
                        </div>
                    </div>
                </aside>

                {{-- Main --}}
                <main class="t4-main {{ $mainNoHeroClass }}">
                    {{-- Mobile/tablet filter trigger (drawer) --}}
                    <div class="t4-filterbar">
                        <button type="button"
                            class="t4-btn t4-btn--outline t4-filter-toggle"
                            data-t4-filter-open
                            aria-controls="t4-filter-drawer"
                            aria-expanded="false">
                            Filters
                        </button>
                    </div>
                    <div class="t4-hero-card">
                        {{-- Category banner slider (Option B) - customer side, admin-managed via Category Sliders --}}
                        @if ($hasCategoryCarousel && $heroSlidesCount > 0)
                        {{-- Banner skeleton (only while loading) - same structure as carousel --}}
                        <div class="t4-cat-banner-skeleton t4-skeleton-only" aria-hidden="true">
                            <div class="t4-skeleton" style="height: 220px; border-radius: 16px; width: 100%;"></div>
                        </div>
                        
                        {{-- Actual carousel --}}
                        <div class="t4-cat-banner t4-hide-while-loading" aria-label="Category banner">
                            <div class="t4-cat-banner__viewport">
                                <div class="t4-cat-banner-slider">
                                    @foreach ($heroSlides as $slide)
                                        @php
                                            $slideImg = asset('assets/images/category-sliders/' . $slide->photo);
                                            $slideLink = !empty($slide->link) ? $slide->link : null;
                                        @endphp
                    
                                        @if ($slideLink)
                                            <a class="t4-cat-banner-slide" href="{{ $slideLink }}">
                                                <img src="{{ $slideImg }}"
                                                     alt="{{ $slide->title ?? 'Slide' }}"
                                                     class="t4-cat-banner-img"
                                                     loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                                     @if($loop->first) fetchpriority="high" @endif>
                                            </a>
                                        @else
                                            <div class="t4-cat-banner-slide">
                                                <img src="{{ $slideImg }}"
                                                     alt="{{ $slide->title ?? 'Slide' }}"
                                                     class="t4-cat-banner-img"
                                                     loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                                     @if($loop->first) fetchpriority="high" @endif>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                    
                            <div class="t4-cat-banner-arrows" aria-hidden="false">
                                <button type="button" class="t4-cat-banner-arrow t4-cat-banner-prev" aria-label="Previous">‹</button>
                                <button type="button" class="t4-cat-banner-arrow t4-cat-banner-next" aria-label="Next">›</button>
                            </div>
                        </div>
                    @endif

                        {{-- Explore section --}}
                        @if ($exploreTiles instanceof \Illuminate\Support\Collection ? $exploreTiles->isNotEmpty() : count($exploreTiles))
                            <div class="t4-explore-row t4-hide-while-loading">
                                <div class="t4-explore-row__header">
                                    <h4>Explore Products Categories</h4>
                                    <div class="t4-arrow-group">
                                        <button type="button" class="t4-circle-btn" data-scroll-target="#t4-explore-scroll" data-dir="prev" aria-label="Previous">‹</button>
                                        <button type="button" class="t4-circle-btn" data-scroll-target="#t4-explore-scroll" data-dir="next" aria-label="Next">›</button>
                                    </div>
                                </div>

                                <div class="t4-explore-scroll" id="t4-explore-scroll">
                                    @foreach ($exploreTiles as $tile)
                                        <a href="{{ $tile['url'] }}" class="t4-explore-item {{ $activeSubSlug === ($tile['slug'] ?? null) ? 'is-active' : '' }}">
                                            <img src="{{ $tile['image'] }}" alt="{{ $tile['label'] }}">
                                            <div class="t4-explore-text">
                                                <span class="t4-explore-name">{{ $tile['label'] }}</span>
                                                <span class="t4-explore-count">{{ (int) ($tile['count'] ?? 0) }} Products</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Explore skeleton (only while loading) --}}
                        <div class="t4-explore-row t4-skeleton-only" aria-hidden="true">
                            <div class="t4-skeleton t4-skel-line t4-skel-line--title"></div>
                            <div class="t4-explore__grid">
                                @for ($i = 0; $i < 8; $i++)
                                    <div class="t4-tile t4-skel-tile">
                                        <div class="t4-skeleton t4-skel-tile__img"></div>
                                        <div class="t4-skeleton t4-skel-tile__text"></div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                                        {{-- Main Category Spotlight Section --}}
                                        @if (!empty($cat))
                                        @php
                                            $mainCategoryImage = !empty($cat->image) 
                                                ? asset('assets/images/categories/' . $cat->image) 
                                                : (!empty($cat->photo) 
                                                    ? asset('assets/images/categories/' . $cat->photo) 
                                                    : asset('assets/images/noimage.png'));
                                            $mainCategoryName = $cat->name ?? 'Category';
                                            $mainCategoryUrl = route('front.category', $cat->slug);
                                        @endphp
                                        <div class="t4-main-category-spotlight t4-hide-while-loading">
                                            <div class="t4-main-category-spotlight__media">
                                                <img src="{{ $mainCategoryImage }}" alt="{{ $mainCategoryName }}">
                                            </div>
                                            <div class="t4-main-category-spotlight__content">
                                                <h3 class="t4-main-category-spotlight__title">{{ $mainCategoryName }}</h3>
                                                @if (!empty($cat->description))
                                                    <div class="t4-main-category-spotlight__description">
                                                        {!! $cat->description !!}
                                                    </div>
                                                @else
                                                    {{-- Fallback to hardcoded description if database description is empty --}}
                                                    <p class="t4-main-category-spotlight__description">
                                                        Havells is a leading electrical equipment and consumer appliances brand, known for reliable and energy-efficient solutions.
                                                    </p>
                                                    <p class="t4-main-category-spotlight__description">
                                                        Its product range includes fans, lighting, switches, cables, and home appliances for residential and commercial use.
                                                    </p>
                                                @endif
                                                <ul class="t4-main-category-spotlight__features">
                                                    <li>
                                                        <span class="t4-main-category-spotlight__icon">●</span>
                                                        Energy-efficient BLDC motors with 50% power savings
                                                    </li>
                                                    <li>
                                                        <span class="t4-main-category-spotlight__icon">●</span>
                                                        Aerodynamic blade design for superior air delivery
                                                    </li>
                                                    <li>
                                                        <span class="t4-main-category-spotlight__icon">●</span>
                                                        Silent operation below 45dB noise level
                                                    </li>
                                                    <li>
                                                        <span class="t4-main-category-spotlight__icon">●</span>
                                                        Remote control with timer and speed settings
                                                    </li>
                                                    <li>
                                                        <span class="t4-main-category-spotlight__icon">●</span>
                                                        5-year comprehensive warranty coverage
                                                    </li>
                                                </ul>
                                                <a href="{{ $mainCategoryUrl }}" class="t4-btn t4-btn--outline t4-main-category-spotlight__cta">
                                                    View Products →
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                    {{-- NEW DESIGN SECTIONS - Based on Figma Design --}}
                    
                    {{-- Brand Spotlight Section (Two-column layout with image and text) --}}
                    @php
                        $hasFeaturedBanner = !empty($featuredBanner) && !empty($featuredBanner->photo);
                    @endphp
                    @if ($hasFeaturedBanner)
                        @php
                            $fbTitle = $featuredBanner->title ?? ($featuredBanner->subtitle_text ?? null);
                            $fbSubtitle = $featuredBanner->text ?? ($featuredBanner->details_text ?? null);
                            $fbLink = $featuredBanner->link ?? null;
                            $fbImg = asset('assets/images/featuredbanner/' . $featuredBanner->photo);
                            $fbPointsRaw = strip_tags($featuredBanner->details_text ?? '');
                            $fbPoints = collect(preg_split("/\r\n|\n|\r/", $fbPointsRaw))
                                ->map(function ($line) {
                                    return trim($line);
                                })
                                ->filter();
                        @endphp

                        <div class="t4-brand-spotlight t4-hide-while-loading">
                            <div class="t4-brand-spotlight__media">
                                <img src="{{ $fbImg }}" alt="{{ $fbTitle ?? 'Featured' }}">
                            </div>
                            <div class="t4-brand-spotlight__content">
                                @if (!empty($fbTitle))
                                    <h3 class="t4-brand-spotlight__title">{{ $fbTitle }}</h3>
                                @endif
                                @if (!empty($fbSubtitle))
                                    <p class="t4-brand-spotlight__description">{{ $fbSubtitle }}</p>
                                @endif
                                @if ($fbPoints->count() > 0)
                                    <ul class="t4-brand-spotlight__features">
                                        @foreach ($fbPoints as $point)
                                            <li>
                                                <span class="t4-brand-spotlight__icon">●</span>
                                                {{ $point }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($fbLink))
                                    <a class="t4-btn t4-btn--primary t4-brand-spotlight__cta" href="{{ $fbLink }}">View Products →</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Brand Logos Strip (Horizontal Carousel) --}}
                    @if ($partners instanceof \Illuminate\Support\Collection ? $partners->isNotEmpty() : count($partners))
                        <div class="t4-brand-logos-strip t4-hide-while-loading">
                            <div class="t4-brand-logos-strip__slider">
                                @foreach ($partners as $index => $partner)
                                    @php
                                        $partnerImg = !empty($partner->photo)
                                            ? asset('assets/images/partner/' . $partner->photo)
                                            : asset('assets/images/noimage.png');
                                        $partnerLink = !empty($partner->link) ? $partner->link : null;
                                    @endphp
                                    <div class="t4-brand-logo-item {{ $index === 0 ? 'is-active' : '' }}">
                                        @if ($partnerLink)
                                            <a href="{{ $partnerLink }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $partnerImg }}" alt="Brand">
                                            </a>
                                        @else
                                            <img src="{{ $partnerImg }}" alt="Brand">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Hidden sort/pageby to preserve existing filter logic --}}
                    <div class="t4-hidden">
                        <select id="sortby" name="sort">
                            <option value="date_desc" {{ (string) $selectedSort === 'date_desc' ? 'selected' : '' }}>Latest Product</option>
                            <option value="date_asc" {{ (string) $selectedSort === 'date_asc' ? 'selected' : '' }}>Oldest Product</option>
                            <option value="price_asc" {{ (string) $selectedSort === 'price_asc' ? 'selected' : '' }}>Lowest Price</option>
                            <option value="price_desc" {{ (string) $selectedSort === 'price_desc' ? 'selected' : '' }}>Highest Price</option>
                        </select>
                        @if ($gs->product_page != null)
                            <select id="pageby" name="pageby">
                                @foreach (explode(',', $gs->product_page) as $element)
                                    <option value="{{ $element }}" {{ (string) $selectedPageby === (string) $element ? 'selected' : '' }}>{{ $element }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" id="pageby" name="paged" value="{{ $gs->page_count }}">
                        @endif
                        <a class="check_view active" data-shopview="grid-view" href="javascript:;"></a>
                    </div>

                    {{-- Product carousel skeletons (only while loading) --}}
                    <div class="t4-skeleton-only" aria-hidden="true">
                        @for ($s = 0; $s < 2; $s++)
                            <div class="t4-product-carousel-section">
                                <div class="t4-product-carousel-section__header">
                                    <div class="t4-skeleton t4-skel-line t4-skel-line--title"></div>
                                    <div class="t4-product-carousel-section__controls">
                                        <button type="button" class="t4-circle-btn" aria-label="Previous">‹</button>
                                        <button type="button" class="t4-circle-btn" aria-label="Next">›</button>
                                    </div>
                                </div>
                                <div class="t4-skel-slider">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div>
                                            <div class="t4-skeleton t4-skel-card__img"></div>
                                            <div class="t4-skeleton t4-skel-card__line"></div>
                                            <div class="t4-skeleton t4-skel-card__line t4-skel-card__line--short"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endfor
                    </div>

                    {{-- Featured Products Carousel --}}
                    @php
                        $featuredProductsList = $featuredProducts instanceof \Illuminate\Support\Collection ? $featuredProducts->take(8) : collect();
                    @endphp
                    @if ($featuredProductsList->isNotEmpty())
                        <div class="t4-product-carousel-section t4-hide-while-loading">
                            <div class="t4-product-carousel-section__header">
                                <h4 class="t4-product-carousel-section__title">Featured Havells Products</h4>
                                <div class="t4-product-carousel-section__controls">
                                    <button type="button" class="t4-circle-btn t4-product-carousel-prev" data-carousel-target="featured" aria-label="Previous">‹</button>
                                    <button type="button" class="t4-circle-btn t4-product-carousel-next" data-carousel-target="featured" aria-label="Next">›</button>
                                </div>
                            </div>
                            <div class="t4-product-carousel t4-product-carousel--featured" data-carousel-id="featured">
                                @foreach ($featuredProductsList as $product)
                                    <div class="t4-product-carousel__slide">
                                        @include('frontend.theme4.category.product_card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                            <div class="t4-carousel-progress">
                                <div class="t4-carousel-progress__bar" data-progress-target="featured"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Appliances and Utilities Carousel --}}
                    @php
                        $appliancesProducts = $bestSellers instanceof \Illuminate\Support\Collection ? $bestSellers->take(8) : collect();
                    @endphp
                    @if ($appliancesProducts->isNotEmpty())
                        <div class="t4-product-carousel-section t4-hide-while-loading">
                            <div class="t4-product-carousel-section__header">
                                <h4 class="t4-product-carousel-section__title">APPLIANCES AND UTILITIES</h4>
                                <div class="t4-product-carousel-section__controls">
                                    <button type="button" class="t4-circle-btn t4-product-carousel-prev" data-carousel-target="appliances" aria-label="Previous">‹</button>
                                    <button type="button" class="t4-circle-btn t4-product-carousel-next" data-carousel-target="appliances" aria-label="Next">›</button>
                                </div>
                            </div>
                            <div class="t4-product-carousel t4-product-carousel--appliances" data-carousel-id="appliances">
                                @foreach ($appliancesProducts as $product)
                                    <div class="t4-product-carousel__slide">
                                        @include('frontend.theme4.category.product_card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                            <div class="t4-carousel-progress">
                                <div class="t4-carousel-progress__bar" data-progress-target="appliances"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Main product listing grid (fills the page; avoids "empty right side" when carousels are empty) --}}
                    @php
                        $mainProducts = $productsList ?? collect();
                        if (is_array($mainProducts)) {
                            $mainProducts = collect($mainProducts);
                        } elseif ($mainProducts instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                            $mainProducts = $mainProducts->getCollection();
                        } elseif (!($mainProducts instanceof \Illuminate\Support\Collection)) {
                            $mainProducts = collect();
                        }
                    @endphp

                    @if ($prods->count() === 0)
                        @php
                            $hasQueryFilters = (bool) count(request()->query());
                            $isFilteredEmpty = ($prods->total() === 0) && $hasQueryFilters;
                        @endphp

                        <div class="t4-card t4-hide-while-loading" style="padding: 28px; text-align: center; margin-top: 18px;">
                            <div style="display: inline-flex; width: 52px; height: 52px; border-radius: 999px; background: #f3f4f6; align-items: center; justify-content: center; margin-bottom: 12px;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 10h4" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M3 6h18" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M7 6l1 14h8l1-14" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            @if ($isFilteredEmpty)
                                <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 6px; color: #111827;">No results match your filters</h3>
                                <p style="margin: 0 0 16px; color: #6B7280; font-size: 13px;">
                                    Try adjusting or clearing your filters to see more products.
                                </p>
                            @else
                                <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 6px; color: #111827;">No products available here yet</h3>
                                <p style="margin: 0 0 16px; color: #6B7280; font-size: 13px;">
                                    This category doesn’t have any published products at the moment.
                                </p>
                            @endif
                            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                <a class="t4-btn t4-btn--ghost"
                                   href="{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}">
                                    Clear Filters
                                </a>
                                @if (!$isFilteredEmpty)
                                    <a class="t4-btn t4-btn--primary" href="{{ route('front.categories') }}">
                                        Browse All Categories
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        @if ($mainProducts->isNotEmpty())
                            <div class="t4-product-grid t4-hide-while-loading">
                                @foreach ($mainProducts as $product)
                                    @include('frontend.theme4.category.product_card', ['product' => $product])
                                @endforeach
                            </div>
                        @endif

                        {{-- Optional pagination (hidden when empty) --}}
                        <div class="t4-pagination t4-hide-while-loading">
                            @php echo $__env->make('partials.front.pagination-links', ['paginator' => $prods])->render(); @endphp
                        </div>
                    @endif

                    {{-- Product grid skeleton placeholder (only while loading) --}}
                    <div class="t4-skeleton-only" aria-hidden="true" style="margin-top: 18px;">
                        <div class="t4-skeleton t4-skel-line t4-skel-line--title"></div>
                        <div class="t4-skel-grid">
                            @for ($i = 0; $i < 8; $i++)
                                <div>
                                    <div class="t4-skeleton t4-skel-card__img"></div>
                                    <div class="t4-skeleton t4-skel-card__line"></div>
                                    <div class="t4-skeleton t4-skel-card__line t4-skel-card__line--short"></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    {{-- Backdrop for mobile/tablet filter drawer --}}
    <div class="t4-filter-backdrop" data-t4-filter-close aria-hidden="true"></div>

@include('partials.front.filter-hidden-inputs')@endsection

@section('script')
    <script>
        // ------------------------------------------------------------
        // Dev-only diagnostics for Theme 4 Category page
        // ------------------------------------------------------------
        const __T4_DEBUG__ = {{ (app()->environment(['local', 'development']) || config('app.debug')) ? 'true' : 'false' }};

        // ------------------------------------------------------------
        // Mobile/Tablet Filter Drawer (Theme4 category)
        // - Reuses existing .t4-filter markup (no duplication)
        // - Uses transform-based hiding (no display:none) to keep price slider stable
        // ------------------------------------------------------------
        (function() {
            const body = document.body;
            if (!body) return;

            const drawer = document.getElementById('t4-filter-drawer');
            const backdrop = document.querySelector('.t4-filter-backdrop');
            const openBtn = document.querySelector('[data-t4-filter-open]');

            if (!drawer || !backdrop || !openBtn) return;

            function setExpanded(isExpanded) {
                openBtn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            }

            function openDrawer() {
                body.classList.add('t4-filter-open');
                drawer.classList.add('is-open');
                setExpanded(true);
                try { drawer.focus({ preventScroll: true }); } catch (e) {}
            }

            function closeDrawer() {
                body.classList.remove('t4-filter-open');
                drawer.classList.remove('is-open');
                setExpanded(false);
            }

            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openDrawer();
            });

            backdrop.addEventListener('click', function() {
                closeDrawer();
            });

            drawer.addEventListener('click', function(e) {
                const target = e.target;
                if (target && target.closest && target.closest('[data-t4-filter-close]')) {
                    e.preventDefault();
                    closeDrawer();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDrawer();
            });

            // Defensive: if user rotates / resizes back to desktop, ensure drawer is closed & scrolling restored
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1200) closeDrawer();
            });
        })();

        (function () {
            if (!__T4_DEBUG__) return;

            const prefix = '[T4 Category Debug]';
            const safeJson = (obj) => {
                try { return JSON.stringify(obj); } catch (e) { return String(obj); }
            };

            // Capture runtime JS errors / promise rejections
            window.addEventListener('error', function (event) {
                const payload = {
                    message: event && event.message,
                    source: event && event.filename,
                    line: event && event.lineno,
                    col: event && event.colno,
                };
                console.error(prefix, 'window.error', payload);
            });
            window.addEventListener('unhandledrejection', function (event) {
                console.error(prefix, 'unhandledrejection', event && event.reason ? event.reason : event);
            });

            // Snapshot key dependencies and DOM state
            const snapshot = (label) => {
                const hasJQ = typeof window.jQuery !== 'undefined';
                const $ = hasJQ ? window.jQuery : null;
                const hasSlick = !!($ && $.fn && $.fn.slick);
                const hasJQUI = !!($ && $.ui && $.ui.slider);

                const bodyClass = (document.body && document.body.className) ? document.body.className : '';
                const counts = {
                    t4Page: document.querySelectorAll('.t4-category-page').length,
                    bannerSlider: document.querySelectorAll('.t4-cat-banner-slider').length,
                    productSliders: document.querySelectorAll('.t4-product-slider').length,
                    brandSliders: document.querySelectorAll('.t4-brands__slider').length,
                    sliderRange: document.querySelectorAll('#slider-range').length,
                };

                console.groupCollapsed(prefix, label);
                console.log('url:', location.href);
                console.log('body.class:', bodyClass);
                console.log('depsJson:', safeJson({ hasJQ, hasSlick, hasJQUI }));
                console.log('domCountsJson:', safeJson(counts));
                console.log('queryJson:', safeJson(Object.fromEntries(new URLSearchParams(location.search))));
                console.groupEnd();
            };

            // Early snapshot (before DOMContentLoaded)
            snapshot('early');

            document.addEventListener('DOMContentLoaded', function () {
                snapshot('DOMContentLoaded');
            }, { once: true });

            window.addEventListener('load', function () {
                snapshot('window.load');
            }, { once: true });

            window.addEventListener('pageshow', function () {
                snapshot('pageshow');
            });

            // Post-load: verify banner slider presence + slick state
            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    try {
                        const el = document.querySelector('.t4-cat-banner-slider');
                        const isInit = !!(el && el.classList && el.classList.contains('slick-initialized'));
                        console.log(prefix, 'bannerSlickInit:', safeJson({ exists: !!el, isInit }));
                    } catch (e) {
                        console.log(prefix, 'bannerSlickInit: error');
                    }
                }, 200);
            }, { once: true });
        })();

        // Safety: never allow Theme 4 category to stay in "loading" state after navigation/reload.
        // If body.t4-loading remains, all .t4-hide-while-loading blocks are forced invisible.
        (function() {
            function clearT4Loading() {
                document.body && document.body.classList.remove('t4-loading');
                if (__T4_DEBUG__) {
                    console.log('[T4 Category Debug]', 'clearT4Loading()', 'body.class=', document.body ? document.body.className : '');
                }
            }
            // DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', clearT4Loading, { once: true });
            } else {
                clearT4Loading();
            }
            // Back/forward cache restore (common on mobile/Chromium)
            window.addEventListener('pageshow', function() {
                clearT4Loading();
            });
            // Fallback: clear shortly after paint
            window.setTimeout(clearT4Loading, 300);
        })();

        // Fallback: if Slick initializes but slides remain invisible/0-height, force grid mode.
        (function($) {
            function forceGridFallback($slider) {
                if (!$slider || !$slider.length) return;
                if ($slider.hasClass('slick-initialized') && typeof $slider.slick === 'function') {
                    try { $slider.slick('unslick'); } catch (e) {}
                }
                $slider.removeClass('slick-initialized slick-slider');
            }

            function ensureVisibleSliders() {
                $('.t4-product-slider, .t4-brands__slider').each(function() {
                    const $slider = $(this);
                    const hasContent = $slider.find('.t4-product-card, .t4-brand, .t4-slide').length > 0;
                    const rect = this.getBoundingClientRect();
                    const height = rect ? rect.height : 0;
                    const hasVisibleSlide =
                        $slider.find('.slick-slide:visible').length > 0 ||
                        $slider.find('.t4-slide:visible').length > 0 ||
                        $slider.find('.t4-brand:visible').length > 0;

                    if (hasContent && (height < 8 || !hasVisibleSlide)) {
                        if (__T4_DEBUG__) {
                            console.warn('[T4 Category Debug]', 'forcing grid fallback', {
                                className: this.className,
                                height,
                                hasVisibleSlide
                            });
                        }
                        forceGridFallback($slider);
                    }
                });
            }

            $(window).on('load', function() {
                setTimeout(ensureVisibleSliders, 100);
            });
        })(jQuery);

        // Rating filter checkboxes behave like a single-select (keep UX identical to "X & above")
        $(document).on('change', 'input[name="rating_min"]', function() {
            if ($(this).is(':checked')) {
                $('input[name="rating_min"]').not(this).prop('checked', false);
            }
            filter();
        });

        $(document).on("click", "#price_filter", function() {
            let amountString = $("#amount").val();
            amountString = amountString.replace(/\$/g, '');
            let amounts = amountString.split('-');
            let amount1 = amounts[0].trim();
            let amount2 = amounts[1].trim();

            $("#update_min_price").val(amount1);
            $("#update_max_price").val(amount2);
            filter();
        });

        $(".attribute-input, #sortby, #pageby, .t4-filter-change").on('change', function() {
            filter();
        });

        function filter() {
            let filterlink =
                '{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}';

            let params = new URLSearchParams();

            $(".attribute-input").each(function() {
                if ($(this).is(':checked')) {
                    params.append($(this).attr('name'), $(this).val());
                }
            });

            if ($("#sortby").val() != '') {
                params.append($("#sortby").attr('name'), $("#sortby").val());
            }

            if ($("#start_value").val() != '') {
                params.append($("#start_value").attr('name'), $("#start_value").val());
            }

            let check_view = $('.check_view.active').data('shopview');
            if (check_view) {
                params.append('view_check', check_view);
            }

            if ($("#update_min_price").val() != '') {
                params.append('min', $("#update_min_price").val());
            }
            if ($("#update_max_price").val() != '') {
                params.append('max', $("#update_max_price").val());
            }

            // rating_min (single-select checkbox)
            const $rating = $('input[name="rating_min"]:checked');
            if ($rating.length) {
                params.append('rating_min', $rating.val());
            }

            // availability[] (multi-select)
            $('input[name="availability[]"]:checked').each(function() {
                params.append('availability[]', $(this).val());
            });

            // Show skeletons immediately while navigating to the filtered page
            $('body').addClass('t4-loading');
            if (__T4_DEBUG__) {
                console.groupCollapsed('[T4 Category Debug]', 'filter() navigate');
                console.log('base:', filterlink);
                console.log('params:', params.toString());
                console.log('final:', filterlink + '?' + params.toString());
                console.log('body.class:', document.body ? document.body.className : '');
                console.groupEnd();
            }

            filterlink += '?' + params.toString();
            location.href = filterlink;
        }

        // Horizontal scroll controls for explore row
        $(document).on('click', '.t4-circle-btn', function() {
            const targetSelector = $(this).data('scroll-target');
            const dir = $(this).data('dir');
            const $target = $(targetSelector);
            if (!$target.length) {
                return;
            }
            const amount = 240;
            const nextLeft = dir === 'next'
                ? $target.scrollLeft() + amount
                : $target.scrollLeft() - amount;
            $target.animate({ scrollLeft: nextLeft }, 200);
        });

        // Initialize product carousels with horizontal scroll and progress bar
        (function($) {
            function updateProgressBar($carousel, carouselId) {
                const scrollLeft = $carousel.scrollLeft();
                const scrollWidth = $carousel[0].scrollWidth;
                const clientWidth = $carousel[0].clientWidth;
                const maxScroll = scrollWidth - clientWidth;
                
                let progress = 0;
                if (maxScroll > 0) {
                    progress = Math.min(100, (scrollLeft / maxScroll) * 100);
                } else {
                    progress = 100; // All items visible
                }
                
                const $progressBar = $('.t4-carousel-progress__bar[data-progress-target="' + carouselId + '"]');
                if ($progressBar.length) {
                    $progressBar.css('width', progress + '%');
                }
            }
            
            function initProductCarousels() {
                $('.t4-product-carousel').each(function() {
                    const $carousel = $(this);
                    const carouselId = $carousel.data('carousel-id');
                    
                    // Remove Slick if already initialized
                    if ($carousel.hasClass('slick-initialized')) {
                        try {
                            $carousel.slick('unslick');
                        } catch(e) {}
                        $carousel.removeClass('slick-initialized');
                    }
                    
                    // Remove any Slick-generated elements
                    $carousel.find('.slick-list, .slick-track').remove();
                    
                    // Ensure flex layout
                    $carousel.css({
                        'display': 'flex',
                        'flex-wrap': 'nowrap'
                    });
                    
                    // Bind scroll event
                    $carousel.off('scroll.carousel').on('scroll.carousel', function() {
                        updateProgressBar($carousel, carouselId);
                    });
                    
                    // Initialize progress bar
                    setTimeout(function() {
                        updateProgressBar($carousel, carouselId);
                    }, 100);
                });
                
                // Wire carousel navigation buttons
                $('.t4-product-carousel-prev, .t4-product-carousel-next').off('click.carousel').on('click.carousel', function() {
                    const target = $(this).data('carousel-target');
                    const $carousel = $('.t4-product-carousel[data-carousel-id="' + target + '"]');
                    if ($carousel.length) {
                        const cardWidth = $carousel.find('.t4-product-carousel__slide').first().outerWidth(true) || 296;
                        const currentScroll = $carousel.scrollLeft();
                        const newScroll = $(this).hasClass('t4-product-carousel-prev') 
                            ? currentScroll - cardWidth 
                            : currentScroll + cardWidth;
                        
                        $carousel.animate({ scrollLeft: newScroll }, 300, function() {
                            updateProgressBar($carousel, target);
                        });
                    }
                });
            }
            
            $(document).ready(function() {
                initProductCarousels();
            });
            
            $(window).on('load', function() {
                setTimeout(initProductCarousels, 200);
            });
            
            // Update progress on window resize
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    $('.t4-product-carousel').each(function() {
                        const $carousel = $(this);
                        const carouselId = $carousel.data('carousel-id');
                        updateProgressBar($carousel, carouselId);
                    });
                }, 250);
            });
        })(jQuery);
    </script>

    <script type="text/javascript">
        (function($) {
            "use strict";
            $(function() {
                const start_value = $("#start_value").val();
                const end_value = $("#end_value").val();
                const max_value = $("#max_value").val();

                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: max_value,
                    values: [start_value, end_value],
                    step: 10,
                    slide: function(event, ui) {
                        $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                    },
                });
                $("#amount").val(
                    "$" +
                    $("#slider-range").slider("values", 0) +
                    " - $" +
                    $("#slider-range").slider("values", 1)
                );
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";

            // Initialize on DOM ready
            $(function() {
                // CRITICAL: Remove loading state IMMEDIATELY to show carousel
                $('body').removeClass('t4-loading');
                
                // Force banner visibility
                $('.t4-cat-banner').css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });
                
                // Ensure first slide is visible even before Slick initializes
                $('.t4-cat-banner-slider:not(.slick-initialized) .t4-cat-banner-slide:first-child').css({
                    'display': 'block',
                    'width': '100%'
                });
                
                // Small delay to ensure DOM is fully ready
                setTimeout(function() {
                    initT4CategoryBanner();
                }, 100);
                
                // Fallback: Remove loading state after 1 second to prevent stuck state
                setTimeout(function() {
                    $('body').removeClass('t4-loading');
                }, 1000);
            });

            // Also on window load (defensive)
            $(window).on('load', function() {
                // Force banner visibility again
                $('.t4-cat-banner').css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });
                
                setTimeout(function() {
                    initT4CategoryBanner();
                    // Ensure loading state is removed after window load
                    $('body').removeClass('t4-loading');
                }, 50);
            });

            // Show skeletons when navigating away via pagination or links within main content
            $(document).on('click', '.t4-pagination a', function() {
                $('body').addClass('t4-loading');
            });

            function initT4CategoryBanner() {
                const $slider = $('.t4-cat-banner-slider');
                if (!$slider.length) {
                    // Remove loading state if no slider exists
                    $('body').removeClass('t4-loading');
                    return;
                }
                if ($slider.hasClass('slick-initialized')) {
                    // Already initialized; ensure layout is correct
                    if (typeof $slider.slick === 'function') {
                        try { $slider.slick('setPosition'); } catch (e) {}
                    }
                    $('body').removeClass('t4-loading');
                    return;
                }

                const $banner = $slider.closest('.t4-cat-banner');
                const $prev = $banner.find('.t4-cat-banner-prev');
                const $next = $banner.find('.t4-cat-banner-next');

                // Ensure slider container is visible BEFORE checking slide count
                $banner.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });

                const slideCount = $slider.find('.t4-cat-banner-slide').length;
                if (slideCount <= 1) {
                    // Keep first slide visible (CSS handles non-initialized state); hide arrows
                    $banner.removeClass('is-slick');
                    $slider.find('.t4-cat-banner-slide:first-child').css({
                        'display': 'block',
                        'width': '100%'
                    });
                    $slider.find('.t4-cat-banner-slide:first-child img').css({
                        'display': 'block',
                        'width': '100%'
                    });
                    // Remove loading state
                    $('body').removeClass('t4-loading');
                    return;
                }

                if (typeof $.fn.slick !== 'function') {
                    $banner.removeClass('is-slick');
                    $slider.find('.t4-cat-banner-slide:first-child').css({
                        'display': 'block',
                        'width': '100%'
                    });
                    $slider.find('.t4-cat-banner-slide:first-child img').css({
                        'display': 'block',
                        'width': '100%'
                    });
                    // Remove loading state
                    $('body').removeClass('t4-loading');
                    return;
                }

                // Ensure slider container is visible before initializing
                $banner.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });

                try {
                    // Bind handlers BEFORE initializing Slick (so init fires are captured)
                    $slider.off('.t4CatBanner');
                    $slider.on('init.t4CatBanner reInit.t4CatBanner afterChange.t4CatBanner', function() {
                        $banner.addClass('is-slick');

                        // Maintain height chain; don't force widths (Slick controls track width)
                        const $list = $slider.find('.slick-list');
                        const $track = $slider.find('.slick-track');
                        if ($list.length) $list.css({ height: '100%' });
                        if ($track.length) $track.css({ height: '100%' });

                        // Remove loading state after successful initialization/changes
                        $('body').removeClass('t4-loading');
                    });

                    $slider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        dots: true,
                        infinite: slideCount > 1,
                        autoplay: slideCount > 1,
                        autoplaySpeed: 4500,
                        pauseOnHover: true,
                        adaptiveHeight: false,
                        speed: 520,
                        cssEase: 'ease-in-out',
                        prevArrow: $prev.length ? $prev : null,
                        nextArrow: $next.length ? $next : null,
                        fade: true,
                        useTransform: true,
                        waitForAnimate: true
                    });
                } catch (e) {
                    console.error('[T4 Category] Banner slider init error:', e);
                    $banner.removeClass('is-slick');
                    const $firstSlide = $slider.find('.t4-cat-banner-slide:first-child');
                    $firstSlide.css({
                        'display': 'block',
                        'width': '100%',
                        'height': '100%'
                    });
                    $firstSlide.find('img').css({
                        'display': 'block',
                        'opacity': '1',
                        'visibility': 'visible',
                        'width': '100%',
                        'height': '100%'
                    });
                    // Remove loading state even on error
                    $('body').removeClass('t4-loading');
                }
            }
        })(jQuery);
    </script>
@endsection

