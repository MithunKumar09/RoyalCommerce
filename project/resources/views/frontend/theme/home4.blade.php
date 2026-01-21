{{-- home4.blade.php --}}
@extends('layouts.front')

@php
    // Demo-safe fallbacks (so theme4 works even when DB has no sliders/categories/products yet)
    $t4Categories = isset($featured_categories) && $featured_categories->count()
        ? $featured_categories
        : collect([
            (object) ['name' => 'Electrical & Appliances', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Industrial Tools', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Office Supplies', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Medical & Lab', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Safety Supplies', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Agri & Gardening', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Construction', 'slug' => '#', 'image' => null],
            (object) ['name' => 'Automotive', 'slug' => '#', 'image' => null],
        ]);

    $t4HeroSlides = isset($sliders) && $sliders->count()
        ? $sliders
        : collect([
            (object) ['photo' => '1730868270Hero02-minpng.png', 'link' => '#', 'title_text' => 'Fast Track Your Orders', 'subtitle_text' => 'Deliver to 590008', 'details_text' => 'Demo hero slide'],
        ]);

    $t4Bestsellers = isset($best_products) && $best_products->count()
        ? $best_products->take(12)
        : collect();
@endphp

@section('content')
    <div class="theme4-home">
        {{-- Category icon strip (interactive / slick) --}}
        <section class="t4-section t4-categories">
            <div class="container">
                <div class="home-cate-slider t4-categories__slider">
                    @foreach ($t4Categories as $cat)
                        <div class="t4-cat">
                            <a class="t4-cat__link" href="{{ $cat->slug && $cat->slug !== '#' ? route('front.category', $cat->slug) : '#' }}">
                                <div class="t4-cat__icon">
                                    @if (!empty($cat->image))
                                        <img src="{{ asset('assets/images/categories/' . $cat->image) }}" alt="{{ $cat->name }}">
                                    @else
                                        <span aria-hidden="true"></span>
                                    @endif
                                </div>
                                <div class="t4-cat__title">{{ $cat->name }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Hero slider (interactive / slick) --}}
        <section class="t4-section t4-hero">
            <div class="container">
                <div class="hero-slider-wrapper t4-hero__slider">
                    @foreach ($t4HeroSlides as $slide)
                        <a class="t4-hero__slide" href="{{ $slide->link ?? '#' }}">
                            <div class="t4-hero__media"
                                style="background-image: url('{{ asset('assets/images/sliders/' . ($slide->photo ?? '')) }}')">
                                <div class="t4-hero__overlay">
                                    <div class="t4-hero__content">
                                        @if (!empty($slide->title_text))
                                            <h2 class="t4-hero__title">{{ $slide->title_text }}</h2>
                                        @endif
                                        @if (!empty($slide->details_text))
                                            <p class="t4-hero__sub">{{ $slide->details_text }}</p>
                                        @endif
                                        <span class="t4-hero__cta">Shop Now</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Offer strip under hero --}}
                <div class="t4-hero-offers">
                    <div class="t4-offer">SCHNEIDER DRIVES<br><span>Upto 50% OFF</span></div>
                    <div class="t4-offer">MUSCLE GRID<br><span>Upto 60% OFF</span></div>
                    <div class="t4-offer">EASTMAN SOLAR<br><span>Upto 40% OFF</span></div>
                    <div class="t4-offer">SWIFT<br><span>Upto 35% OFF</span></div>
                    <div class="t4-offer t4-offer--highlight">Next Day Delivery<br><span>Just 24 Hours</span></div>
                </div>

                {{-- Promo banners row (static demo images) --}}
                <div class="t4-promo-row">
                    <a class="t4-promo" href="#">
                        <img src="{{ asset('assets/images/arrival/1730868330Banner9-minpng.png') }}" alt="Promo">
                    </a>
                    <a class="t4-promo" href="#">
                        <img src="{{ asset('assets/images/arrival/1730868319Banner8-minpng.png') }}" alt="Promo">
                    </a>
                    <a class="t4-promo" href="#">
                        <img src="{{ asset('assets/images/arrival/1730868306Banner7-minpng.png') }}" alt="Promo">
                    </a>
                    <a class="t4-promo" href="#">
                        <img src="{{ asset('assets/images/arrival/1724559403partnerpng.png') }}" alt="Promo">
                    </a>
                </div>
            </div>
        </section>

        {{-- Bestsellers (interactive / slick) --}}
        <section class="t4-section t4-bestsellers">
            <div class="container">
                <div class="t4-title-row">
                    <h3 class="t4-title">BESTSELLERS</h3>
                </div>

                @if ($t4Bestsellers->count())
                    <div class="product-cards-slider t4-bestsellers__slider">
                        @foreach ($t4Bestsellers as $product)
                            @include('frontend.theme.partials.home4_product_card', ['product' => $product])
                        @endforeach
                    </div>
                @else
                    <div class="t4-empty">No products yet (demo). Add products and mark them as “Best” to populate.</div>
                @endif

                {{-- Bottom banner trio (demo) --}}
                <div class="t4-bottom-banners">
                    <a class="t4-bottom-banner" href="#">
                        <img src="{{ asset('assets/images/arrival/1724559395sliderimg1png.png') }}" alt="Banner">
                    </a>
                    <a class="t4-bottom-banner" href="#">
                        <img src="{{ asset('assets/images/arrival/1724559385sliderimg2png.png') }}" alt="Banner">
                    </a>
                    <a class="t4-bottom-banner" href="{{ route('front.track.search', 'DEMO') }}">
                        <div class="t4-track">
                            <div>
                                <div class="t4-track__title">Track Order</div>
                                <div class="t4-track__btn">TRACK NOW</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection

