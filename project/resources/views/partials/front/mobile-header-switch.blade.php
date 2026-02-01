    <!-- if route is user panel then show vendor.mobile-header else show frontend.mobile_menu -->

    @php
        $url = url()->current();
        $explodeUrl = explode('/',$url);

    @endphp

    @if(in_array('user',$explodeUrl))
    <!-- frontend mobile menu -->
    @include('includes.user.mobile-header')
    @elseif(in_array("rider",$explodeUrl))
    @include('includes.rider.mobile-header')
    @else 
    @include('includes.frontend.mobile_menu')
        <!-- user panel mobile sidebar -->

    @endif
   

