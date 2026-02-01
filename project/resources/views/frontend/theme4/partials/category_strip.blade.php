{{-- category_strip.blade.php (Theme 4 shared: home, category, product) --}}
@php
    $t4Categories = $t4Categories ?? collect();
    $activeCategorySlug = $activeCategorySlug ?? null;
    $activeCategoryId = $activeCategoryId ?? null;
    $sectionClass = $sectionClass ?? '';
    $sliderClass = $sliderClass ?? '';
    $itemClass = $itemClass ?? '';
    $linkClass = $linkClass ?? '';
    $iconClass = $iconClass ?? '';
    $titleClass = $titleClass ?? '';
@endphp

<section class="t4-section t4-categories {{ $sectionClass }}">
    <div class="container">
        <div class="js-t4-cat-strip {{ $sliderClass }}">
            @foreach ($t4Categories as $cat)
                @php
                    $isActiveCat = false;
                    if (!empty($activeCategorySlug) && !empty($cat->slug)) {
                        $isActiveCat = $cat->slug === $activeCategorySlug;
                    } elseif (!empty($activeCategoryId) && !empty($cat->id)) {
                        $isActiveCat = (string) $cat->id === (string) $activeCategoryId;
                    }
                @endphp
                <div class="t4-cat {{ $itemClass }}">
                    <a class="t4-cat__link {{ $linkClass }} {{ $isActiveCat ? 'is-active' : '' }}"
                       href="{{ $cat->slug ? route('front.category', $cat->slug) : '#' }}">
                        <div class="t4-cat__icon {{ $iconClass }}">
                            @if (!empty($cat->image))
                                <img src="{{ asset('assets/images/categories/' . $cat->image) }}" alt="{{ $cat->name }}">
                            @else
                                <span aria-hidden="true"></span>
                            @endif
                        </div>
                        <div class="t4-cat__title {{ $titleClass }}">{{ $cat->name }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

