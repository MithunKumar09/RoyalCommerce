{{-- RFQ Sidebar --}}
<li class="sidebar-divider"></li>

<li class="sidebar-heading">
    {{ __('Sales & Leads') }}
</li>

<li class="{{ request()->routeIs('admin.rfqs.*') ? 'active' : '' }}">
    <a href="{{ route('admin.rfqs.index') }}" class="wave-effect">
        <i class="fas fa-file-invoice"></i>
        <span>{{ __('RFQ Requests') }}</span>
    </a>
</li>
