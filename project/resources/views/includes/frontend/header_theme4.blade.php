@php
    $previewTheme = request()->query('theme');
    $activeThemeLocal = in_array($previewTheme, ['theme1', 'theme2', 'theme3', 'theme4'], true) ? $previewTheme : $gs->theme;
@endphp

<header class="t4-header">
    <div class="container">
        <div class="t4-header__row">
            <a class="t4-header__brand" href="{{ route('front.index') }}">
                <span class="t4-header__brand-text">eCommerce</span>
            </a>

            <nav class="t4-nav" aria-label="Primary">
                <ul class="t4-nav__list">
                    <li class="t4-nav__item t4-nav__item--dropdown">
                        <a class="t4-nav__link" href="{{ route('front.index') }}">HOME</a>
                        <span class="t4-nav__chev" aria-hidden="true"></span>
                        <ul class="t4-nav__menu">
                            <li><a class="t4-nav__menu-link {{ request()->query('theme') ? '' : 'is-active' }}" href="{{ route('front.index') }}">Default</a></li>
                            <li><a class="t4-nav__menu-link {{ $activeThemeLocal === 'theme1' ? 'is-active' : '' }}" href="{{ route('front.index') }}?theme=theme1">Theme 1</a></li>
                            <li><a class="t4-nav__menu-link {{ $activeThemeLocal === 'theme2' ? 'is-active' : '' }}" href="{{ route('front.index') }}?theme=theme2">Theme 2</a></li>
                            <li><a class="t4-nav__menu-link {{ $activeThemeLocal === 'theme3' ? 'is-active' : '' }}" href="{{ route('front.index') }}?theme=theme3">Theme 3</a></li>
                            <li><a class="t4-nav__menu-link {{ $activeThemeLocal === 'theme4' ? 'is-active' : '' }}" href="{{ route('front.index') }}?theme=theme4">Theme 4</a></li>
                        </ul>
                    </li>

                    <li class="t4-nav__item t4-nav__item--dropdown">
                        <a class="t4-nav__link" href="{{ route('front.category') }}">PRODUCTS</a>
                        <span class="t4-nav__chev" aria-hidden="true"></span>
                    </li>

                    <li class="t4-nav__item t4-nav__item--dropdown">
                        <a class="t4-nav__link" href="javascript:void(0)">PAGES</a>
                        <span class="t4-nav__chev" aria-hidden="true"></span>
                    </li>

                    @if ($ps->blog == 1)
                        <li class="t4-nav__item"><a class="t4-nav__link" href="{{ route('front.blog') }}">BLOG</a></li>
                    @endif
                    <li class="t4-nav__item"><a class="t4-nav__link" href="{{ route('front.faq') }}">FAQ</a></li>
                    <li class="t4-nav__item"><a class="t4-nav__link" href="{{ route('front.contact') }}">CONTACT US</a></li>
                </ul>
            </nav>

            <div class="t4-actions" aria-label="Header actions">
                <a class="t4-action" href="javascript:;" aria-label="Search">
                    <span class="t4-action__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L16.65 16.65" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 18C14.866 18 18 14.866 18 11C18 7.13401 14.866 4 11 4C7.13401 4 4 7.13401 4 11C4 14.866 7.13401 18 11 18Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </a>

                <a class="t4-action" href="javascript:;" aria-label="Compare">
                    <span class="t4-action__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 4V20" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6 4V20" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 9H14" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 15H14" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="t4-action__badge">0</span>
                </a>

                <a class="t4-action" href="{{ Auth::check() ? route('user-wishlists') : route('user.login') }}" aria-label="Wishlist">
                    <span class="t4-action__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.84 4.61C20.3292 4.09917 19.7228 3.69397 19.0555 3.4172C18.3882 3.14043 17.673 2.99768 16.95 2.99768C16.227 2.99768 15.5118 3.14043 14.8445 3.4172C14.1772 3.69397 13.5708 4.09917 13.06 4.61L12 5.67L10.94 4.61C9.9083 3.5783 8.50903 2.99821 7.05 2.99821C5.59097 2.99821 4.1917 3.5783 3.16 4.61C2.1283 5.6417 1.5482 7.04097 1.5482 8.5C1.5482 9.95903 2.1283 11.3583 3.16 12.39L4.22 13.45L12 21.23L19.78 13.45L20.84 12.39C21.3508 11.8792 21.756 11.2728 22.0328 10.6055C22.3096 9.93818 22.4523 9.22298 22.4523 8.5C22.4523 7.77702 22.3096 7.06182 22.0328 6.39447C21.756 5.72713 21.3508 5.12075 20.84 4.61Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="t4-action__badge">0</span>
                </a>

                <a class="t4-action" href="{{ route('front.cart-view') }}" aria-label="Cart">
                    <span class="t4-action__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 6H22L20 14H8L6 6Z" stroke="#111827" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M8 14L7 18H19" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" fill="#111827"/>
                            <path d="M18 22C18.5523 22 19 21.5523 19 21C19 20.4477 18.5523 20 18 20C17.4477 20 17 20.4477 17 21C17 21.5523 17.4477 22 18 22Z" fill="#111827"/>
                            <path d="M2 2H4L6 6" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="t4-action__badge">0</span>
                </a>
            </div>
        </div>
    </div>
</header>

