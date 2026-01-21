@php
    $img = $product->thumbnail
        ? asset('assets/images/thumbnails/' . $product->thumbnail)
        : asset('assets/images/noimage.png');
    $rating = number_format((float) ($product->ratings_avg_rating ?? 0), 1);
    $ratingCount = (int) ($product->ratings_count ?? 0);
    $hasVideo = !empty($product->youtube);
    $hasMedia = !empty($product->media_extra);
    $badges = collect();
    if ($hasMedia) {
        $badges->push('3D view');
    }
    if ($hasVideo) {
        $badges->push('View 360°');
    }
@endphp

<div class="t4-card-product">
    @if ($badges->isNotEmpty())
        <div class="t4-card-product__badges">
            @foreach ($badges as $badge)
                <span class="t4-card-badge">{{ $badge }}</span>
            @endforeach
        </div>
    @endif

    <a href="{{ route('front.product', $product->slug) }}" class="t4-card-product__media">
        <img src="{{ $img }}" alt="{{ $product->name ?? 'Product' }}">
        @if ($hasVideo)
            <span class="t4-card-play">Play</span>
        @endif
        @if (method_exists($product, 'offPercentage') && $product->offPercentage())
            <span class="t4-card-discount">{{ round($product->offPercentage()) }}% OFF</span>
        @endif
    </a>

    <div class="t4-card-product__meta">
        <a href="{{ route('front.product', $product->slug) }}" class="t4-card-product__title">
            {{ method_exists($product, 'showName') ? $product->showName() : ($product->name ?? '') }}
        </a>
        <div class="t4-card-product__rating">
            <span class="t4-card-rating__score">{{ $rating }}</span>
            <span class="t4-card-rating__stars">★★★★★</span>
            <span class="t4-card-rating__count">({{ $ratingCount }})</span>
        </div>
        <div class="t4-card-product__price">
            <span class="t4-card-price__now">{{ method_exists($product, 'showPrice') ? $product->showPrice() : '' }}</span>
            <span class="t4-card-price__old">{{ method_exists($product, 'showPreviousPrice') ? $product->showPreviousPrice() : '' }}</span>
        </div>
        <div class="t4-card-product__actions">
            <a class="t4-btn t4-btn--ghost add_cart_click"
               href="javascript:;"
               data-href="{{ route('product.cart.add', $product->id) }}">
                Add to Cart
            </a>
            <a class="t4-btn t4-btn--primary" href="{{ route('front.product', $product->slug) }}">View</a>
        </div>
    </div>
</div>

