{{-- Advanced Product Media panel partial
    Params: $product (Product|null), $mode ('create'|'edit'), $productId (int),
    $mediaExtra, $v360, $hotspots, $model3d, $mediaVideos, $mediaVideoMap --}}
                                        <div class="modal fade" id="mediaVideoPreviewModal" tabindex="-1" role="dialog" aria-labelledby="mediaVideoPreviewLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mediaVideoPreviewLabel">{{ __('Media Preview') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <video id="mediaVideoPreviewPlayer" controls style="width: 100%; max-height: 60vh; background: #0f172a;"></video>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card apm-card">
                                                    <div class="card-header" id="media-advanced-heading">
                                                        <button class="apm-header-toggle" type="button"
                                                            data-toggle="collapse" data-target="#media-advanced-collapse"
                                                            aria-expanded="false" aria-controls="media-advanced-collapse">
                                                            <span class="apm-header-left">
                                                                <span class="apm-icon" aria-hidden="true">
                                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                </span>
                                                                <span>
                                                                    <span class="apm-title">{{ __('Advanced Product Media') }}</span>
                                                                    <span class="apm-subtitle">{{ __('Configure interactive media experiences') }}</span>
                                                                </span>
                                                            </span>
                                                            <span class="apm-subtitle">{{ __('Optional') }}</span>
                                                        </button>
                                                    </div>
                                                    <div id="media-advanced-collapse" class="collapse"
                                                        aria-labelledby="media-advanced-heading"
                                                        data-product-id="{{ $productId }}"
                                                        data-mode="{{ $mode }}">
                                                        <div class="card-body">
                                                            <div style="display:none;">
                                                                <input type="hidden" name="media_3d_auto_rotate" value="{{ !empty($model3d['viewer']['auto_rotate']) ? 1 : 0 }}">
                                                                <input type="hidden" name="media_3d_exposure" value="{{ isset($model3d['viewer']['exposure']) ? $model3d['viewer']['exposure'] : '' }}">
                                                                <input type="hidden" name="media_3d_camera_orbit" value="{{ isset($model3d['viewer']['camera_orbit']) ? $model3d['viewer']['camera_orbit'] : '' }}">
                                                            </div>
                                                            <div class="apm-toolbar">
                                                                <button type="button" class="apm-btn" id="apm-btn-360"
                                                                    data-toggle="collapse" data-target="#media-360-collapse"
                                                                    aria-expanded="false" aria-controls="media-360-collapse">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    <span>360°</span>
                                                                    <svg class="apm-chevron" id="apm-chevron-360" width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button" class="apm-btn" id="apm-btn-hotspots"
                                                                    data-toggle="collapse" data-target="#media-hotspot-collapse"
                                                                    aria-expanded="false" aria-controls="media-hotspot-collapse">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    <span>{{ __('Hotspots') }}</span>
                                                                    <svg class="apm-chevron" id="apm-chevron-hotspots" width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button" class="apm-btn" id="apm-btn-3d"
                                                                    data-toggle="collapse" data-target="#media-3d-collapse"
                                                                    aria-expanded="false" aria-controls="media-3d-collapse">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    <span>3D</span>
                                                                    <svg class="apm-chevron" id="apm-chevron-3d" width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button" class="apm-btn" id="apm-btn-media"
                                                                    data-toggle="collapse" data-target="#media-video-collapse"
                                                                    aria-expanded="false" aria-controls="media-video-collapse">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"
                                                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    <span>{{ __('Media') }}</span>
                                                                    <svg class="apm-chevron" id="apm-chevron-media" width="14" height="14"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <div class="accordion apm-accordion" id="media-advanced-accordion">
                                                                <div class="card">
                                                                    <div class="card-header" id="media-360-heading">
                                                                        <h6 class="mb-0">
                                                                            <button class="btn btn-link collapsed" type="button"
                                                                                data-toggle="collapse" data-target="#media-360-collapse"
                                                                                aria-expanded="false" aria-controls="media-360-collapse">
                                                                                {{ __('360° View') }}
                                                                            </button>
                                                                        </h6>
                                                                    </div>
                                                                    <div id="media-360-collapse" class="collapse"
                                                                        aria-labelledby="media-360-heading"
                                                                        data-parent="#media-advanced-accordion">
                                                                        <div class="card-body apm-panel">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="checkbox-wrapper">
                                                                                    <input type="checkbox" name="media_360_enabled"
                                                                                        value="1" id="media_360_enabled" {{ !empty($v360['enabled']) ? 'checked' : '' }}>
                                                                                        <label for="media_360_enabled">
                                                                                            {{ __('Enable 360° View') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('360° Frames') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Upload 24-36 images in sequence)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                @if($mode === 'edit')
                                                                                <div class="col-lg-12">
                                                                                    <input type="file" class="input-field"
                                                                                        name="media_360_frames[]" id="media_360_frames" multiple>
                                                                                </div>
                                                                                @endif
                                                                            </div>
                                                                            @if($mode === 'edit')
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Upload Mode') }}</h4>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <label for="media_360_mode" class="sr-only">{{ __('Upload Mode') }}</label>
                                                                                    <select class="input-field" id="media_360_mode">
                                                                                        <option value="append" selected>{{ __('Add frames') }}</option>
                                                                                        <option value="replace">{{ __('Replace all frames') }}</option>
                                                                                    </select>
                                                                                    <small class="text-danger" id="media_360_mode_warning" style="display:none;">
                                                                                        {{ __('Warning: replacing will remove all existing frames.') }}
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Preview') }}</h4>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <a href="javascript:;" class="mybtn1" id="media_360_upload_btn">
                                                                                        <i class="icofont-upload-alt"></i> {{ __('Upload 360 Frames') }}
                                                                                    </a>
                                                                                    <a href="javascript:;" class="mybtn1 {{ $v360HasFrames ? '' : 'disabled' }}" id="media_360_preview_btn"
                                                                                        data-toggle="modal" data-target="#view360" {{ $v360HasFrames ? '' : 'aria-disabled=true' }}>
                                                                                        <i class="icofont-eye-alt"></i> {{ __('View 360 Preview') }}
                                                                                    </a>
                                                                                    <a href="javascript:;" class="mybtn1" id="media_360_delete_btn">
                                                                                        <i class="fas fa-trash-alt"></i> {{ __('Delete 360 Frames') }}
                                                                                    </a>
                                                                                    <span class="text-muted" id="media_360_status">
                                                                                        {{ $v360HasFrames ? ($v360Count . ' ' . __('frames uploaded.')) : __('No frames uploaded yet.') }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            @else
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="alert alert-info small py-2 mb-0">
                                                                                        <i class="fas fa-info-circle"></i>
                                                                                        {{ __('Save the product first, then return to Edit to upload 360° frames.') }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card">
                                                                    <div class="card-header" id="media-hotspot-heading">
                                                                        <h6 class="mb-0">
                                                                            <button class="btn btn-link collapsed" type="button"
                                                                                data-toggle="collapse" data-target="#media-hotspot-collapse"
                                                                                aria-expanded="false" aria-controls="media-hotspot-collapse">
                                                                                {{ __('Hotspot View') }}
                                                                            </button>
                                                                        </h6>
                                                                    </div>
                                                                    <div id="media-hotspot-collapse" class="collapse"
                                                                        aria-labelledby="media-hotspot-heading"
                                                                        data-parent="#media-advanced-accordion">
                                                                        <div class="card-body apm-panel">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="checkbox-wrapper">
                                                                                    <input type="checkbox" name="media_hotspot_enabled"
                                                                                        value="1" id="media_hotspot_enabled" {{ !empty($hotspots['enabled']) ? 'checked' : '' }}>
                                                                                        <label for="media_hotspot_enabled">
                                                                                            {{ __('Enable Hotspots') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Hotspot Target') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Image or 360° frame)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <select class="input-field" id="media_hotspot_target_mode">
                                                                                        <option value="image" selected>{{ __('Image') }}</option>
                                                                                        <option value="frame360">{{ __('360° Frame') }}</option>
                                                                                        <option value="model3d">{{ __('3D Model') }}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row" id="media_hotspot_frame_row" style="display:none;">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Frame Selector') }}</h4>
                                                                                        <p class="sub-heading">
                                                                                            {{ __('(1 to') }} <span id="media_hotspot_frame_count">0</span>
                                                                                            {{ __('frames)') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <input type="number" class="input-field" id="media_hotspot_frame_number"
                                                                                        min="1" value="1">
                                                                                </div>
                                                                            </div>
                                                                            <div class="row" id="media_hotspot_base_row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Base Image') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Select feature or gallery image)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <select class="input-field" id="media_hotspot_base" name="media_hotspot_base">
                                                                                        <option value="">{{ __('Select image') }}</option>
                                                                                        <option value="feature"
                                                                                            {{ $hotspotBase === 'feature' ? 'selected' : '' }}
                                                                                            data-src="{{ empty(($product ? $product->photo : null)) ? asset('assets/images/noimage.png') : (filter_var(($product ? $product->photo : null), FILTER_VALIDATE_URL) ? ($product ? $product->photo : null) : asset('assets/images/products/' . ($product ? $product->photo : null))) }}">
                                                                                            {{ __('Feature Image') }}
                                                                                        </option>
                                                                                        @if (($product && $product->galleries && $product->galleries->count()) > 0)
                                                                                            @foreach (($product ? $product->galleries : collect()) as $gallery)
                                                                                                <option value="gallery_{{ $gallery->id }}"
                                                                                                    {{ $hotspotBase === 'gallery_' . $gallery->id ? 'selected' : '' }}
                                                                                                    data-src="{{ asset('assets/images/galleries/' . $gallery->photo) }}">
                                                                                                    {{ __('Gallery Image') }} #{{ $gallery->id }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Preview') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Click on image to add hotspots)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <div class="lookbook media-hotspot-preview" id="media_hotspot_preview">
                                                                                        <div class="lookbook-block" id="media_hotspot_block">
                                                                                            <img id="media_hotspot_image" src="{{ asset('assets/images/noimage.png') }}"
                                                                                                class="img-fluid bg-img" alt="">
                                                                                            @foreach ($hotspotItems as $item)
                                                                                                @php
                                                                                                    $itemId = !empty($item['id']) ? (string) $item['id'] : ('hs_' . $loop->index);
                                                                                                    $itemType = !empty($item['type']) ? (string) $item['type'] : 'text';
                                                                                                    $itemLabel = isset($item['label']) ? (string) $item['label'] : __('Hotspot');
                                                                                                    $itemDesc = isset($item['description']) ? (string) $item['description'] : '';
                                                                                                    $itemTarget = !empty($item['target']) ? (string) $item['target'] : 'image';
                                                                                                    $itemFrame = isset($item['frame']) ? (string) $item['frame'] : '';
                                                                                                    $posX = isset($item['position']['x']) ? (float) $item['position']['x'] : null;
                                                                                                    $posY = isset($item['position']['y']) ? (float) $item['position']['y'] : null;
                                                                                                    $xPercent = is_numeric($posX) ? round($posX * 100, 2) : 0;
                                                                                                    $yPercent = is_numeric($posY) ? round($posY * 100, 2) : 0;
                                                                                                    $imageSrc = '';
                                                                                                    if (isset($item['image'])) {
                                                                                                        if (is_array($item['image']) && !empty($item['image']['src'])) {
                                                                                                            $imageSrc = (string) $item['image']['src'];
                                                                                                        } elseif (is_string($item['image'])) {
                                                                                                            $imageSrc = (string) $item['image'];
                                                                                                        }
                                                                                                    }
                                                                                                    $showImage = !empty($imageSrc) && $itemType !== 'text';
                                                                                                    $showText = !$showImage || $itemType !== 'image';
                                                                                                @endphp
                                                                                                <div class="lookbook-dot media-hotspot-dot"
                                                                                                    data-key="{{ $itemId }}"
                                                                                                    data-target="{{ $itemTarget }}"
                                                                                                    data-frame="{{ $itemFrame }}"
                                                                                                    style="left:{{ number_format($xPercent, 2, '.', '') }}%; top:{{ number_format($yPercent, 2, '.', '') }}%;">
                                                                                                    <span>{{ $loop->iteration }}</span>
                                                                                                    <a href="javascript:void(0)">
                                                                                                        <div class="dot-showbox">
                                                                                                            <img class="dot-image img-fluid" alt=""
                                                                                                                src="{{ $imageSrc }}"
                                                                                                                style="{{ $showImage ? '' : 'display:none;' }}">
                                                                                                            <div class="dot-info" style="{{ $showText ? '' : 'display:none;' }}">
                                                                                                                <h5 class="title">{{ $itemLabel }}</h5>
                                                                                                                <h6 class="desc">{{ $itemDesc }}</h6>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Hotspot Items') }}</h4>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <div id="media_hotspot_items">
                                                                                        <div class="media-hotspot-group" data-target="image">
                                                                                            <div class="media-hotspot-group-title">{{ __('Image') }}</div>
                                                                                            <div class="media-hotspot-group-items" id="media_hotspot_group_image">
                                                                                                @foreach ($hotspotItems as $item)
                                                                                                    @php
                                                                                                        $itemTarget = !empty($item['target']) ? (string) $item['target'] : 'image';
                                                                                                        if ($itemTarget !== 'image') { continue; }
                                                                                                        $itemId = !empty($item['id']) ? (string) $item['id'] : ('hs_' . $loop->index);
                                                                                                        $itemType = !empty($item['type']) ? (string) $item['type'] : 'text';
                                                                                                        $itemLabel = isset($item['label']) ? (string) $item['label'] : '';
                                                                                                        $itemDesc = isset($item['description']) ? (string) $item['description'] : '';
                                                                                                        $itemFrame = isset($item['frame']) ? (string) $item['frame'] : '';
                                                                                                        $posX = isset($item['position']['x']) ? (float) $item['position']['x'] : null;
                                                                                                        $posY = isset($item['position']['y']) ? (float) $item['position']['y'] : null;
                                                                                                        $xPercent = is_numeric($posX) ? round($posX * 100, 2) : 0;
                                                                                                        $yPercent = is_numeric($posY) ? round($posY * 100, 2) : 0;
                                                                                                        $imageSrc = '';
                                                                                                        if (isset($item['image'])) {
                                                                                                            if (is_array($item['image']) && !empty($item['image']['src'])) {
                                                                                                                $imageSrc = (string) $item['image']['src'];
                                                                                                            } elseif (is_string($item['image'])) {
                                                                                                                $imageSrc = (string) $item['image'];
                                                                                                            }
                                                                                                        }
                                                                                                        $showImageWrap = in_array($itemType, ['image', 'image_text'], true);
                                                                                                    @endphp
                                                                                                    <div class="media-hotspot-item row" data-key="{{ $itemId }}">
                                                                                                        <div class="col-md-3">
                                                                                                            <select class="input-field media-hotspot-type" name="media_hotspot_type[]">
                                                                                                                <option value="text" {{ $itemType === 'text' ? 'selected' : '' }}>{{ __('Text') }}</option>
                                                                                                                <option value="image" {{ $itemType === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                                                                                                <option value="image_text" {{ $itemType === 'image_text' ? 'selected' : '' }}>{{ __('Image + Text') }}</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-label" name="media_hotspot_label[]" placeholder="{{ __('Label') }}" value="{{ $itemLabel }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-desc" name="media_hotspot_description[]" placeholder="{{ __('Description') }}" value="{{ $itemDesc }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-actions">
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-toggle" title="{{ __('Toggle visibility') }}"><i class="fas fa-eye"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-jump" title="{{ __('Jump to target') }}"><i class="fas fa-crosshairs"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-remove" title="{{ __('Remove hotspot') }}"><i class="fas fa-times"></i></a>
                                                                                                        </div>
                                                                                                        <div class="col-md-6 media-hotspot-image-wrap" style="{{ $showImageWrap ? '' : 'display:none;' }}">
                                                                                                            <input type="file" class="input-field media-hotspot-image" name="media_hotspot_image[]" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                                                                                                            <div class="media-hotspot-thumb-wrap">
                                                                                                                <img class="img-fluid media-hotspot-thumb" style="max-width:80px; margin-top:6px; {{ $imageSrc ? '' : 'display:none;' }}" alt="" src="{{ $imageSrc }}">
                                                                                                            </div>
                                                                                                            <small class="text-muted">{{ __('Max 2MB') }}</small>
                                                                                                            <div class="alert alert-danger media-hotspot-error" style="display:none; margin-top:6px;"></div>
                                                                                                            <div style="margin-top:6px;">
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-change-image"><i class="fas fa-image"></i> {{ __('Change image') }}</a>
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-remove-image"><i class="fas fa-times"></i> {{ __('Remove image') }}</a>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-x3d" name="media_hotspot_x3d[]" placeholder="x" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-y3d" name="media_hotspot_y3d[]" placeholder="y" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-z3d" name="media_hotspot_z3d[]" placeholder="z" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-orbit" name="media_hotspot_orbit[]" placeholder="{{ __('camera_orbit') }}" value="">
                                                                                                        </div>
                                                                                                        <input type="hidden" name="media_hotspot_id[]" value="{{ $itemId }}">
                                                                                                        <input type="hidden" name="media_hotspot_x[]" value="{{ number_format($xPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" name="media_hotspot_y[]" value="{{ number_format($yPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" class="media-hotspot-target" name="media_hotspot_target[]" value="{{ $itemTarget }}">
                                                                                                        <input type="hidden" class="media-hotspot-frame" name="media_hotspot_frame[]" value="{{ $itemFrame }}">
                                                                                                        <input type="hidden" name="media_hotspot_image_delete[]" value="0">
                                                                                                        <div class="col-12"><small class="text-muted media-hotspot-status"></small></div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="media-hotspot-group" data-target="frame360">
                                                                                            <div class="media-hotspot-group-title">{{ __('360 Frames') }}</div>
                                                                                            <div class="media-hotspot-group-items" id="media_hotspot_group_frame">
                                                                                                @foreach ($hotspotItems as $item)
                                                                                                    @php
                                                                                                        $itemTarget = !empty($item['target']) ? (string) $item['target'] : 'image';
                                                                                                        if ($itemTarget !== 'frame360') { continue; }
                                                                                                        $itemId = !empty($item['id']) ? (string) $item['id'] : ('hs_' . $loop->index);
                                                                                                        $itemType = !empty($item['type']) ? (string) $item['type'] : 'text';
                                                                                                        $itemLabel = isset($item['label']) ? (string) $item['label'] : '';
                                                                                                        $itemDesc = isset($item['description']) ? (string) $item['description'] : '';
                                                                                                        $itemFrame = isset($item['frame']) ? (string) $item['frame'] : '';
                                                                                                        $posX = isset($item['position']['x']) ? (float) $item['position']['x'] : null;
                                                                                                        $posY = isset($item['position']['y']) ? (float) $item['position']['y'] : null;
                                                                                                        $xPercent = is_numeric($posX) ? round($posX * 100, 2) : 0;
                                                                                                        $yPercent = is_numeric($posY) ? round($posY * 100, 2) : 0;
                                                                                                        $imageSrc = '';
                                                                                                        if (isset($item['image'])) {
                                                                                                            if (is_array($item['image']) && !empty($item['image']['src'])) {
                                                                                                                $imageSrc = (string) $item['image']['src'];
                                                                                                            } elseif (is_string($item['image'])) {
                                                                                                                $imageSrc = (string) $item['image'];
                                                                                                            }
                                                                                                        }
                                                                                                        $showImageWrap = in_array($itemType, ['image', 'image_text'], true);
                                                                                                    @endphp
                                                                                                    <div class="media-hotspot-item row" data-key="{{ $itemId }}">
                                                                                                        <div class="col-md-3">
                                                                                                            <select class="input-field media-hotspot-type" name="media_hotspot_type[]">
                                                                                                                <option value="text" {{ $itemType === 'text' ? 'selected' : '' }}>{{ __('Text') }}</option>
                                                                                                                <option value="image" {{ $itemType === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                                                                                                <option value="image_text" {{ $itemType === 'image_text' ? 'selected' : '' }}>{{ __('Image + Text') }}</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-label" name="media_hotspot_label[]" placeholder="{{ __('Label') }}" value="{{ $itemLabel }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-desc" name="media_hotspot_description[]" placeholder="{{ __('Description') }}" value="{{ $itemDesc }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-actions">
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-toggle" title="{{ __('Toggle visibility') }}"><i class="fas fa-eye"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-jump" title="{{ __('Jump to target') }}"><i class="fas fa-crosshairs"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-remove" title="{{ __('Remove hotspot') }}"><i class="fas fa-times"></i></a>
                                                                                                        </div>
                                                                                                        <div class="col-md-6 media-hotspot-image-wrap" style="{{ $showImageWrap ? '' : 'display:none;' }}">
                                                                                                            <input type="file" class="input-field media-hotspot-image" name="media_hotspot_image[]" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                                                                                                            <div class="media-hotspot-thumb-wrap">
                                                                                                                <img class="img-fluid media-hotspot-thumb" style="max-width:80px; margin-top:6px; {{ $imageSrc ? '' : 'display:none;' }}" alt="" src="{{ $imageSrc }}">
                                                                                                            </div>
                                                                                                            <small class="text-muted">{{ __('Max 2MB') }}</small>
                                                                                                            <div class="alert alert-danger media-hotspot-error" style="display:none; margin-top:6px;"></div>
                                                                                                            <div style="margin-top:6px;">
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-change-image"><i class="fas fa-image"></i> {{ __('Change image') }}</a>
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-remove-image"><i class="fas fa-times"></i> {{ __('Remove image') }}</a>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-x3d" name="media_hotspot_x3d[]" placeholder="x" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-y3d" name="media_hotspot_y3d[]" placeholder="y" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-z3d" name="media_hotspot_z3d[]" placeholder="z" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-orbit" name="media_hotspot_orbit[]" placeholder="{{ __('camera_orbit') }}" value="">
                                                                                                        </div>
                                                                                                        <input type="hidden" name="media_hotspot_id[]" value="{{ $itemId }}">
                                                                                                        <input type="hidden" name="media_hotspot_x[]" value="{{ number_format($xPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" name="media_hotspot_y[]" value="{{ number_format($yPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" class="media-hotspot-target" name="media_hotspot_target[]" value="{{ $itemTarget }}">
                                                                                                        <input type="hidden" class="media-hotspot-frame" name="media_hotspot_frame[]" value="{{ $itemFrame }}">
                                                                                                        <input type="hidden" name="media_hotspot_image_delete[]" value="0">
                                                                                                        <div class="col-12"><small class="text-muted media-hotspot-status"></small></div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="media-hotspot-group" data-target="model3d">
                                                                                            <div class="media-hotspot-group-title">{{ __('3D Model') }}</div>
                                                                                            <div class="media-hotspot-group-items" id="media_hotspot_group_model">
                                                                                                @foreach ($hotspotItems as $item)
                                                                                                    @php
                                                                                                        $itemTarget = !empty($item['target']) ? (string) $item['target'] : 'image';
                                                                                                        if ($itemTarget !== 'model3d') { continue; }
                                                                                                        $itemId = !empty($item['id']) ? (string) $item['id'] : ('hs_' . $loop->index);
                                                                                                        $itemType = !empty($item['type']) ? (string) $item['type'] : 'text';
                                                                                                        $itemLabel = isset($item['label']) ? (string) $item['label'] : '';
                                                                                                        $itemDesc = isset($item['description']) ? (string) $item['description'] : '';
                                                                                                        $itemFrame = isset($item['frame']) ? (string) $item['frame'] : '';
                                                                                                        $posX = isset($item['position']['x']) ? (float) $item['position']['x'] : null;
                                                                                                        $posY = isset($item['position']['y']) ? (float) $item['position']['y'] : null;
                                                                                                        $xPercent = is_numeric($posX) ? round($posX * 100, 2) : 0;
                                                                                                        $yPercent = is_numeric($posY) ? round($posY * 100, 2) : 0;
                                                                                                        $imageSrc = '';
                                                                                                        if (isset($item['image'])) {
                                                                                                            if (is_array($item['image']) && !empty($item['image']['src'])) {
                                                                                                                $imageSrc = (string) $item['image']['src'];
                                                                                                            } elseif (is_string($item['image'])) {
                                                                                                                $imageSrc = (string) $item['image'];
                                                                                                            }
                                                                                                        }
                                                                                                        $showImageWrap = in_array($itemType, ['image', 'image_text'], true);
                                                                                                    @endphp
                                                                                                    <div class="media-hotspot-item row" data-key="{{ $itemId }}">
                                                                                                        <div class="col-md-3">
                                                                                                            <select class="input-field media-hotspot-type" name="media_hotspot_type[]">
                                                                                                                <option value="text" {{ $itemType === 'text' ? 'selected' : '' }}>{{ __('Text') }}</option>
                                                                                                                <option value="image" {{ $itemType === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                                                                                                <option value="image_text" {{ $itemType === 'image_text' ? 'selected' : '' }}>{{ __('Image + Text') }}</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-label" name="media_hotspot_label[]" placeholder="{{ __('Label') }}" value="{{ $itemLabel }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-text-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-desc" name="media_hotspot_description[]" placeholder="{{ __('Description') }}" value="{{ $itemDesc }}">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-actions">
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-toggle" title="{{ __('Toggle visibility') }}"><i class="fas fa-eye"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-jump" title="{{ __('Jump to target') }}"><i class="fas fa-crosshairs"></i></a>
                                                                                                            <a href="javascript:;" class="mybtn1 media-hotspot-remove" title="{{ __('Remove hotspot') }}"><i class="fas fa-times"></i></a>
                                                                                                        </div>
                                                                                                        <div class="col-md-6 media-hotspot-image-wrap" style="{{ $showImageWrap ? '' : 'display:none;' }}">
                                                                                                            <input type="file" class="input-field media-hotspot-image" name="media_hotspot_image[]" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                                                                                                            <div class="media-hotspot-thumb-wrap">
                                                                                                                <img class="img-fluid media-hotspot-thumb" style="max-width:80px; margin-top:6px; {{ $imageSrc ? '' : 'display:none;' }}" alt="" src="{{ $imageSrc }}">
                                                                                                            </div>
                                                                                                            <small class="text-muted">{{ __('Max 2MB') }}</small>
                                                                                                            <div class="alert alert-danger media-hotspot-error" style="display:none; margin-top:6px;"></div>
                                                                                                            <div style="margin-top:6px;">
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-change-image"><i class="fas fa-image"></i> {{ __('Change image') }}</a>
                                                                                                                <a href="javascript:;" class="mybtn1 media-hotspot-remove-image"><i class="fas fa-times"></i> {{ __('Remove image') }}</a>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-x3d" name="media_hotspot_x3d[]" placeholder="x" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-y3d" name="media_hotspot_y3d[]" placeholder="y" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-z3d" name="media_hotspot_z3d[]" placeholder="z" value="">
                                                                                                        </div>
                                                                                                        <div class="col-md-3 media-hotspot-3d-wrap">
                                                                                                            <input type="text" class="input-field media-hotspot-3d-input media-hotspot-orbit" name="media_hotspot_orbit[]" placeholder="{{ __('camera_orbit') }}" value="">
                                                                                                        </div>
                                                                                                        <input type="hidden" name="media_hotspot_id[]" value="{{ $itemId }}">
                                                                                                        <input type="hidden" name="media_hotspot_x[]" value="{{ number_format($xPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" name="media_hotspot_y[]" value="{{ number_format($yPercent, 2, '.', '') }}">
                                                                                                        <input type="hidden" class="media-hotspot-target" name="media_hotspot_target[]" value="{{ $itemTarget }}">
                                                                                                        <input type="hidden" class="media-hotspot-frame" name="media_hotspot_frame[]" value="{{ $itemFrame }}">
                                                                                                        <input type="hidden" name="media_hotspot_image_delete[]" value="0">
                                                                                                        <div class="col-12"><small class="text-muted media-hotspot-status"></small></div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card">
                                                                    <div class="card-header" id="media-3d-heading">
                                                                        <h6 class="mb-0">
                                                                            <button class="btn btn-link collapsed" type="button"
                                                                                data-toggle="collapse" data-target="#media-3d-collapse"
                                                                                aria-expanded="false" aria-controls="media-3d-collapse">
                                                                                {{ __('3D Model View') }}
                                                                            </button>
                                                                        </h6>
                                                                    </div>
                                                                    <div id="media-3d-collapse" class="collapse"
                                                                        aria-labelledby="media-3d-heading"
                                                                        data-parent="#media-advanced-accordion">
                                                                        <div class="card-body apm-panel">
                                                                            @php
                                                                                $model3dEnabled = !empty($model3d['enabled']);
                                                                                $model3dSrc = !empty($model3d['src']) ? (string) $model3d['src'] : '';
                                                                                $model3dName = $model3dSrc ? basename(parse_url($model3dSrc, PHP_URL_PATH) ?: $model3dSrc) : '';
                                                                            @endphp
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="checkbox-wrapper">
                                                                                        <input type="checkbox" name="media_3d_enabled"
                                                                                            value="1" id="media_3d_enabled" {{ $model3dEnabled ? 'checked' : '' }}>
                                                                                        <label for="media_3d_enabled">
                                                                                            {{ __('Enable 3D Model') }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('3D Model File') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(GLB/GLTF)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <input type="file" class="input-field"
                                                                                        name="media_3d_model" id="media_3d_model"
                                                                                        accept=".glb,.gltf">
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Preview') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Admin-only preview)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <model-viewer id="media_3d_viewer"
                                                                                        style="width: 100%; height: 400px; background: #f8f8f8;"
                                                                                        @if(!empty($model3dSrc)) src="{{ $model3dSrc }}" @endif
                                                                                        camera-controls zoom fullscreen
                                                                                        loading="lazy">
                                                                                    </model-viewer>
                                                                                    <div class="text-muted" id="media_3d_status">
                                                                                        {{ !empty($model3dName) ? $model3dName : __('No 3D model selected.') }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Actions') }}</h4>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-12">
                                                                                    <a href="javascript:;" class="mybtn1" id="media_3d_clear">
                                                                                        <i class="fas fa-times"></i> {{ __('Clear 3D Preview') }}
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card">
                                                                    <div class="card-header" id="media-video-heading">
                                                                        <h6 class="mb-0">
                                                                            <button class="btn btn-link collapsed" type="button"
                                                                                data-toggle="collapse" data-target="#media-video-collapse"
                                                                                aria-expanded="false" aria-controls="media-video-collapse">
                                                                                {{ __('Media') }}
                                                                            </button>
                                                                        </h6>
                                                                    </div>
                                                                    <div id="media-video-collapse" class="collapse"
                                                                        aria-labelledby="media-video-heading"
                                                                        data-parent="#media-advanced-accordion">
                                                                        <div class="card-body apm-panel">
                                                                            @php
                                                                                $mainImageSrc = !empty(($product ? $product->photo : null))
                                                                                    ? asset('assets/images/products/' . ($product ? $product->photo : null))
                                                                                    : asset('assets/images/noimage.png');
                                                                                $galleryItems = ($product ? $product->galleries : collect()) ?? collect();
                                                                                if (is_array($galleryItems)) {
                                                                                    $galleryItems = collect($galleryItems);
                                                                                }
                                                                            @endphp
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="left-area">
                                                                                        <h4 class="heading">{{ __('Image Videos') }}</h4>
                                                                                        <p class="sub-heading">{{ __('(Upload a video or paste a URL per image)') }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="apm-media-list">
                                                                                @php
                                                                                    $items = collect([
                                                                                        [
                                                                                            'key' => 'main:0',
                                                                                            'type' => 'main',
                                                                                            'id' => 0,
                                                                                            'label' => __('Main Image'),
                                                                                            'src' => $mainImageSrc,
                                                                                        ],
                                                                                    ]);
                                                                                    if ($galleryItems instanceof \Illuminate\Support\Collection && $galleryItems->isNotEmpty()) {
                                                                                        foreach ($galleryItems as $gallery) {
                                                                                            $items->push([
                                                                                                'key' => 'gallery:' . $gallery->id,
                                                                                                'type' => 'gallery',
                                                                                                'id' => $gallery->id,
                                                                                                'label' => __('Gallery Image') . ' #' . $gallery->id,
                                                                                                'src' => asset('assets/images/galleries/' . $gallery->photo),
                                                                                            ]);
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                                @foreach ($items as $item)
                                                                                    @php
                                                                                        $video = $mediaVideoMap[$item['key']] ?? null;
                                                                                        $videoSrc = null;
                                                                                        $videoLabel = null;
                                                                                        $videoUrlValue = '';
                                                                                        if ($video) {
                                                                                            $videoLabel = $video->source_type === 'upload' ? basename($video->video_path ?? '') : $video->video_url;
                                                                                            $videoSrc = $video->source_type === 'upload'
                                                                                                ? asset($video->video_path)
                                                                                                : $video->video_url;
                                                                                            if ($video->source_type === 'url') {
                                                                                                $videoUrlValue = $video->video_url;
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="apm-media-item">
                                                                                        <div class="apm-media-thumb">
                                                                                            <img src="{{ $item['src'] }}" alt="{{ $item['label'] }}">
                                                                                        </div>
                                                                                        <div class="apm-media-body">
                                                                                            <div class="apm-media-title">{{ $item['label'] }}</div>
                                                                                            <input type="hidden" name="media_video_target_type[{{ $item['key'] }}]" value="{{ $item['type'] }}">
                                                                                            <input type="hidden" name="media_video_target_id[{{ $item['key'] }}]" value="{{ $item['id'] }}">
                                                                                            <div class="apm-media-fields">
                                                                                                <div class="apm-media-field">
                                                                                                    <label class="sub-heading">{{ __('Video File') }}</label>
                                                                                                    <input type="file" class="input-field apm-media-file"
                                                                                                        name="media_video_file[{{ $item['key'] }}]"
                                                                                                        accept="video/mp4,video/webm,video/ogg">
                                                                                                    <small class="text-muted">{{ __('MP4/WebM/OGG · Max 50MB') }}</small>
                                                                                                </div>
                                                                                                <div class="apm-media-field">
                                                                                                    <label class="sub-heading">{{ __('Video URL') }}</label>
                                                                                                    <input type="text" class="input-field apm-media-url"
                                                                                                        name="media_video_url[{{ $item['key'] }}]"
                                                                                                        value="{{ $videoUrlValue }}"
                                                                                                        placeholder="{{ __('https://...') }}">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="apm-media-actions">
                                                                                                <label class="apm-media-remove">
                                                                                                    <input type="checkbox" name="media_video_remove[{{ $item['key'] }}]" value="1">
                                                                                                    {{ __('Remove video') }}
                                                                                                </label>
                                                                                                <button type="button" class="mybtn1 apm-video-preview"
                                                                                                    data-video-src="{{ $videoSrc ?? '' }}">
                                                                                                    <i class="fas fa-eye"></i> {{ __('Preview') }}
                                                                                                </button>
                                                                                                @if ($videoLabel)
                                                                                                    <span class="text-muted apm-media-current">{{ $videoLabel }}</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="apm-footer">
                                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <span>{{ __('Select a media type to configure interactive product experiences') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
