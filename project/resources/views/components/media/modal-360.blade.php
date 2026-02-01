@props([
  'id' => 'modal360View',
  'title' => '360° View',
  'media' => null,
])

@php
  $available = is_array($media) && !empty($media['v360']['available']);
  $manifestUrlRaw = $available ? ($media['v360']['manifest_url'] ?? '') : '';
  
  // Normalize URL for production (fix invalid URLs like https://assets/...)
  $manifestUrl = $manifestUrlRaw;
  if ($manifestUrlRaw) {
    // If URL starts with https://assets/ or http://assets/ (invalid), convert to relative path
    if (preg_match('/^https?:\/\/assets\//', $manifestUrlRaw)) {
      $manifestUrl = preg_replace('/^https?:\/\/assets/', '/assets', $manifestUrlRaw);
    }
    // If it's already a relative path starting with /assets, keep it
    // If it's a full valid URL, keep it
    // Otherwise, ensure it starts with /
    if (!filter_var($manifestUrl, FILTER_VALIDATE_URL) && substr($manifestUrl, 0, 1) !== '/') {
      $manifestUrl = '/' . ltrim($manifestUrl, '/');
    }
  }
  
  $frameCount = $available ? ($media['v360']['frame_count'] ?? 0) : 0;
  $hotspots = $available && is_array($media) ? ($media['hotspots']['items'] ?? []) : [];
@endphp

@if($available)
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $title }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div
          class="viewer-360-container js-media-360"
          data-manifest-url="{{ $manifestUrl }}"
          data-frame-count="{{ $frameCount }}"
          data-hotspots='@json($hotspots)'
        ></div>
      </div>
    </div>
  </div>
</div>
@endif

