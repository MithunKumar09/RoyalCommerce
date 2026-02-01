{{-- item_category_strip.blade.php (deprecated wrapper to shared partial) --}}
@php
    $t4Categories = $t4Categories ?? collect();
    $activeCategoryId = $activeCategoryId ?? null;
@endphp

@include('frontend.theme4.partials.category_strip', [
    't4Categories' => $t4Categories,
    'activeCategoryId' => $activeCategoryId,
    'sectionClass' => 'rc-item-category-strip t4-categories--product-page',
    'sliderClass' => 't4-cat-strip-slider rc-item-category-strip__slider',
    'itemClass' => 'rc-item-category-strip__item',
    'linkClass' => 'rc-item-category-strip__link',
    'iconClass' => 'rc-item-category-strip__icon',
    'titleClass' => 'rc-item-category-strip__title',
])

