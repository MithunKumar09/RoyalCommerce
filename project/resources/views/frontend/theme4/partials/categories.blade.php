
{{-- categories.blade.php (deprecated wrapper to shared partial) --}}
@include('frontend.theme4.partials.category_strip', [
    't4Categories' => $t4Categories ?? collect(),
    'sectionClass' => '',
    'sliderClass' => 'home-cate-slider t4-categories__slider',
    'itemClass' => '',
    'linkClass' => '',
    'iconClass' => '',
    'titleClass' => '',
])
