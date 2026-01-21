@php
    $img = $product->thumbnail
        ? asset('assets/images/thumbnails/' . $product->thumbnail)
        : asset('assets/images/noimage.png');

    $rating = number_format((float) ($product->ratings_avg_rating ?? 0), 1);
    $ratingCount = (int) ($product->ratings_count ?? 0);
@endphp

<div class="t4-product">
    <a class="t4-product__link" href="{{ route('front.product', $product->slug) }}">
        <div class="t4-product__imgwrap">
            <img class="t4-product__img" src="{{ $img }}" alt="{{ $product->name ?? 'Product' }}" loading="lazy">
        </div>

        <div class="t4-product__meta">
            <div class="t4-product__rating">
                <span class="t4-product__rating-badge">{{ $rating }}★</span>
                <span class="t4-product__rating-count">({{ $ratingCount }} Reviews)</span>
            </div>

            <div class="t4-product__title">
                {{ method_exists($product, 'showName') ? $product->showName() : ($product->name ?? '') }}
            </div>

            <div class="t4-product__price">
                <span class="t4-product__price-now">{{ method_exists($product, 'showPrice') ? $product->showPrice() : '' }}</span>
                <span class="t4-product__price-old">{{ method_exists($product, 'showPreviousPrice') ? $product->showPreviousPrice() : '' }}</span>
                @if (method_exists($product, 'offPercentage') && $product->offPercentage())
                    <span class="t4-product__price-off">{{ round($product->offPercentage()) }}% OFF</span>
                @endif
            </div>
        </div>
    </a>
</div>

