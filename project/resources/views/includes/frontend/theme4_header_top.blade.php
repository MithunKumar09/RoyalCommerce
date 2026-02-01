@php
    // Demo location (can be wired to user/address later)
    $t4Pincode = '590008';
    $t4City = 'Belagavi';
    $cart = Session::has('cart') ? Session::get('cart')->items : [];
@endphp

<div class="t4-header-top">
    <div class="container custom-containerr">
        <div class="t4-header-top__row">
            <div class="t4-header-top__left">
                <button type="button" class="header-toggle mobile-menu-toggle t4-mobile-only t4-mobile-toggle"
                    aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 12H21M3 6H21M3 18H15" stroke="#111827" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <a class="t4-brand" href="{{ route('front.index') }}" aria-label="Home">
                    <img class="t4-brand__logo" src="{{ asset('assets/images/' . $gs->logo) }}" alt="logo">
                </a>

                <div class="t4-location">
                    <div class="t4-location__pin" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" fill="#E11D2E"/>
                        </svg>
                    </div>
                    <div class="t4-location__text">
                        <div class="t4-location__line1">Deliver to <strong>{{ $t4Pincode }}</strong></div>
                        <div class="t4-location__line2">{{ $t4City }} <span class="t4-location__chev" aria-hidden="true"></span></div>
                    </div>
                </div>
            </div>

            <div class="t4-header-top__center">
                <form class="t4-search" action="{{ route('front.category') }}" method="GET" role="search">
                    <input class="t4-search__input" type="text" name="search"
                        placeholder="Search Product, Category, Brand..." value="{{ request()->query('search') ?? '' }}">
                    <button class="t4-search__btn" type="submit" aria-label="Search">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L17.5 17.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.5 20C16.1944 20 20 16.1944 20 11.5C20 6.80558 16.1944 3 11.5 3C6.80558 3 3 6.80558 3 11.5C3 16.1944 6.80558 20 11.5 20Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="t4-header-top__right">
                <button id="searchIcon" class="t4-iconlink t4-iconlink--essential t4-mobile-only" type="button" aria-label="Search">
                    <span class="t4-iconlink__ico" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L16.65 16.65" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 18C14.866 18 18 14.866 18 11C18 7.13401 14.866 4 11 4C7.13401 4 4 7.13401 4 11C4 14.866 7.13401 18 11 18Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>
                <a class="t4-iconlink t4-iconlink--hide-mobile" href="{{ Auth::check() ? route('user-dashboard') : route('user.login') }}">
                    <span class="t4-iconlink__ico" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="t4-iconlink__label">Login Now</span>
                </a>

                <a class="t4-iconlink t4-iconlink--hide-mobile" href="{{ route('front.track.search', 'DEMO') }}">
                    <span class="t4-iconlink__ico" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 17l1-4 8-8 4 4-8 8-4 1Z" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 6l4 4" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 19h14" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="t4-iconlink__label">Track Order</span>
                </a>

                <a class="t4-iconlink t4-iconlink--essential" href="{{ route('front.cart') }}">
                    <span class="t4-iconlink__ico" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2h2l2 14h12l2-10H6" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" fill="#111827"/>
                            <path d="M18 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" fill="#111827"/>
                        </svg>
                    </span>
                    <span class="t4-iconlink__label">Cart</span>
                </a>

                <button type="button"
                    class="t4-kebab t4-iconlink--hide-mobile header-toggle"
                    aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        aria-hidden="true">
                        <path d="M3 12H21M3 6H21M3 18H15" stroke="#111827" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

