<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? $gs->title }}</title>
    @if (!empty($pageMetaDescription))
        <meta name="description" content="{{ $pageMetaDescription }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <!--Essential css files-->
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/icofont.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/slick.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/nice-select.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/datatables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets/front') }}/css/custom.css">
    <link rel="icon" href="{{ asset('assets/images/' . $gs->favicon) }}">
    @include('partials.global.extra-head')
    @yield('css')

</head>

<body>

    @php
        $categories = App\Models\Category::with('subs')->where('status', 1)->get();
        $pages = App\Models\Page::get();
        $currencies = App\Models\Currency::all();
        $languges = App\Models\Language::all();
    @endphp
    <!-- header area -->
    @include('includes.frontend.header')

@include('partials.front.mobile-header-switch')

    @include('partials.front.overlay')

    @yield('content')


@include('partials.front.footer')

    @php
        $debugTheme = $activeTheme ?? $gs->theme ?? 'unknown';
        $debugSource = $themeSource ?? 'db';
        $debugPreview = $themePreview ?? null;
    @endphp

    @if (app()->environment(['local', 'development']) || Auth::guard('admin')->check())
        <div style="
            position: fixed;
            right: 12px;
            bottom: 12px;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 6px;
            line-height: 1.4;
            max-width: 260px;
        ">
            Theme: {{ $debugTheme }} ({{ $debugSource }})
            @if ($debugPreview)
                <div>Preview: {{ $debugPreview }}</div>
            @endif
        </div>
    @endif

    <!--Esential Js Files-->
    <script src="{{ asset('assets/front') }}/js/jquery.min.js"></script>
        <script src="{{ asset('assets/front') }}/js/slick.js"></script>
    <script src="{{ asset('assets/front') }}/js/jquery-ui.js"></script>
    <script src="{{ asset('assets/front') }}/js/nice-select.js"></script>
    <script src="{{ asset('assets/front') }}/js/jquery.waypoints.min.js"></script>
    <script src="{{ asset('assets/front') }}/js/jquery.counterup.js"></script>
 
    <script src="{{ asset('assets/front') }}/js/wow.js"></script>
    <script src="{{ asset('assets/front') }}/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/front/js/toastr.min.js') }}"></script>
    
    <script src="{{ asset('assets/front') }}/js/script.js"></script>
    <script src="{{ asset('assets/front/js/myscript.js') }}"></script>


@include('partials.global.js-globals')

    <script src="{{ asset('assets/front/js/ecommerce-toggle.js') }}"></script>



    @php
        echo $__env->make('partials.global.flash-toasts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();
    @endphp

      
  @yield('script')

</body>

</html>
