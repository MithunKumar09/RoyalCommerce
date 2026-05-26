{{-- resources/views/admin/rfqs/_status_badge.blade.php --}}
@php
$colors = [
    'new' => 'secondary',
    'contacted' => 'info',
    'quoted' => 'warning',
    'closed' => 'success',
];
@endphp

<span class="badge badge-{{ $colors[$status] ?? 'secondary' }}">
    {{ ucfirst($status) }}
</span>
