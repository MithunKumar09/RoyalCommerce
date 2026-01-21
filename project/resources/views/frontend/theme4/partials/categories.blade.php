
{{-- categories.blade.php --}}
<section class="t4-section t4-categories">
    <div class="t4-categories">
        <div class="home-cate-slider">
            @foreach ($t4Categories as $cat)
                <div class="t4-cat">
                    <a class="t4-cat__link"
                       href="{{ $cat->slug ? route('front.category', $cat->slug) : '#' }}">
                        <div class="t4-cat__icon">
                            @if (!empty($cat->image))
                                <img src="{{ asset('assets/images/categories/' . $cat->image) }}" alt="{{ $cat->name }}">
                            @else
                                <span></span>
                            @endif
                        </div>
                        <div class="t4-cat__title">{{ $cat->name }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
