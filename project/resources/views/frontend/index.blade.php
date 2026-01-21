@if ($activeTheme === 'theme1')
    @include('frontend.theme.home1')
@elseif ($activeTheme === 'theme2')
    @include('frontend.theme.home2')
@elseif ($activeTheme === 'theme4')
    @include('frontend.theme.home4')
@else
    @include('frontend.theme.home3')
@endif
