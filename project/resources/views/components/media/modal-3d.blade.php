@props([
  'id' => 'modal3DView',
  'title' => '3D View',
  'media' => null,
])

@php
  $available = is_array($media) && !empty($media['model3d']['available']);
  $modelUrlRaw = $available ? ($media['model3d']['src'] ?? '') : '';
  
  // Normalize URL for production (fix invalid URLs like https://assets/...)
  $modelUrl = $modelUrlRaw;
  if ($modelUrlRaw) {
    // If URL starts with https://assets/ or http://assets/ (invalid), convert to relative path
    if (preg_match('/^https?:\/\/assets\//', $modelUrlRaw)) {
      $modelUrl = preg_replace('/^https?:\/\/assets/', '/assets', $modelUrlRaw);
    }
    // If it's already a relative path starting with /assets, keep it
    // If it's a full valid URL, keep it
    // Otherwise, ensure it starts with /
    if (!filter_var($modelUrl, FILTER_VALIDATE_URL) && substr($modelUrl, 0, 1) !== '/') {
      $modelUrl = '/' . ltrim($modelUrl, '/');
    }
  }
  
  $viewer = $available ? ($media['model3d']['viewer'] ?? []) : [];
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
          class="viewer-3d-container js-media-3d"
          data-model-url="{{ $modelUrl }}"
          data-viewer='@json($viewer)'
          data-hotspots='@json($hotspots)'
        ></div>
      </div>
    </div>
  </div>
</div>
@endif

