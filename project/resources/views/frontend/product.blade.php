@extends('layouts.front')

@section('content')
@php
    /**
     * Frontend normalization (no backend/DB changes)
     * Normalizes admin-stored `products.media_extra` to a consistent `$media` shape.
     *
     * Admin schema (current):
     * - v360: enabled, frame_count, manifest, ...
     * - model3d: enabled, src, viewer...
     * - hotspots: enabled, target_image, items[].position.x/y (0..1 floats), description, image.src...
     *
     * Video is stored outside media_extra at: products.youtube
     */
    $mediaExtra = json_decode($productt->media_extra, true);
    if (!is_array($mediaExtra)) { $mediaExtra = []; }

    $v360Raw = (isset($mediaExtra['v360']) && is_array($mediaExtra['v360'])) ? $mediaExtra['v360'] : [];
    $model3dRaw = (isset($mediaExtra['model3d']) && is_array($mediaExtra['model3d'])) ? $mediaExtra['model3d'] : [];
    $hotspotsRaw = (isset($mediaExtra['hotspots']) && is_array($mediaExtra['hotspots'])) ? $mediaExtra['hotspots'] : [];

    // 360°
    $v360Enabled = isset($v360Raw['enabled']) && (int)$v360Raw['enabled'] === 1;
    $v360FrameCount = isset($v360Raw['frame_count']) ? (int)$v360Raw['frame_count'] : 0;
    $v360ManifestUrl = isset($v360Raw['manifest']) && is_string($v360Raw['manifest']) ? $v360Raw['manifest'] : null;
    $has360 = $v360Enabled && $v360FrameCount > 0;

    // 3D
    $model3dEnabled = isset($model3dRaw['enabled']) && (int)$model3dRaw['enabled'] === 1;
    $model3dSrc = isset($model3dRaw['src']) && is_string($model3dRaw['src']) ? $model3dRaw['src'] : null;
    $model3dViewer = (isset($model3dRaw['viewer']) && is_array($model3dRaw['viewer'])) ? $model3dRaw['viewer'] : [];
    $has3D = $model3dEnabled && !empty($model3dSrc);

    // Video (legacy on product record + per-image media videos)
    $videoUrl = !empty($productt->youtube) ? (string) $productt->youtube : null;
    $mediaVideos = $productt->mediaVideos ?? collect();
    if (is_array($mediaVideos)) { $mediaVideos = collect($mediaVideos); }
    $videoMap = [];
    if ($mediaVideos instanceof \Illuminate\Support\Collection && $mediaVideos->isNotEmpty()) {
        foreach ($mediaVideos as $video) {
            if (empty($video->target_type)) { continue; }
            $src = null;
            if ($video->source_type === 'upload' && !empty($video->video_path)) {
                $src = asset($video->video_path);
            } elseif ($video->source_type === 'url' && !empty($video->video_url)) {
                $src = $video->video_url;
            }
            if ($src) {
                $key = $video->target_type . ':' . (string) $video->target_id;
                $videoMap[$key] = $src;
            }
        }
    }
    $mainVideoUrl = $videoMap['main:0'] ?? $videoUrl;
    $hasVideo = !empty($mainVideoUrl) || !empty($videoMap);

    // Hotspots
    $hotspotsEnabled = isset($hotspotsRaw['enabled']) && (int)$hotspotsRaw['enabled'] === 1;
    $hotspotsTargetImage = isset($hotspotsRaw['target_image']) ? (string) $hotspotsRaw['target_image'] : '';
    $hotspotItemsRaw = (isset($hotspotsRaw['items']) && is_array($hotspotsRaw['items'])) ? $hotspotsRaw['items'] : [];

    $hotspotItemsAll = [];
    if ($hotspotsEnabled && !empty($hotspotItemsRaw)) {
        foreach ($hotspotItemsRaw as $hs) {
            if (!is_array($hs)) { continue; }
            $target = isset($hs['target']) ? (string)$hs['target'] : 'image';
            if ($target !== 'image' && $target !== 'frame360' && $target !== 'model3d') {
                $target = 'image';
            }

            // Admin stores 0..1 floats at position.x/y. Fallback: accept legacy x/y (0..100) if present.
            $posX = isset($hs['position']['x']) ? (float)$hs['position']['x'] : null;
            $posY = isset($hs['position']['y']) ? (float)$hs['position']['y'] : null;
            if (!is_numeric($posX) || !is_numeric($posY)) {
                if (isset($hs['x']) && isset($hs['y']) && is_numeric($hs['x']) && is_numeric($hs['y'])) {
                    $posX = (float)$hs['x'] / 100;
                    $posY = (float)$hs['y'] / 100;
                }
            }
            if (!is_numeric($posX) || !is_numeric($posY)) { continue; }
            if ($posX < 0 || $posX > 1 || $posY < 0 || $posY > 1) { continue; }

            $imageUrl = null;
            if (isset($hs['image'])) {
                if (is_array($hs['image']) && !empty($hs['image']['src'])) {
                    $imageUrl = (string) $hs['image']['src'];
                } elseif (is_string($hs['image'])) {
                    $imageUrl = (string) $hs['image'];
                }
            }

            $frameValue = null;
            if ($target === 'frame360' && isset($hs['frame']) && is_numeric($hs['frame'])) {
                $frameValue = (int) $hs['frame'];
            }

            $hotspotItemsAll[] = [
                'id' => isset($hs['id']) ? (string) $hs['id'] : '',
                'label' => isset($hs['label']) ? (string) $hs['label'] : 'Hotspot',
                'description' => isset($hs['description']) ? (string) $hs['description'] : (isset($hs['desc']) ? (string) $hs['desc'] : ''),
                'image_url' => $imageUrl,
                // Admin stores 0..1 floats, UI uses 0..100%
                'x_percent' => round($posX * 100, 2),
                'y_percent' => round($posY * 100, 2),
                'target' => $target,
                'frame' => $frameValue,
            ];
        }
    }
    $hotspotItemsImage = array_values(array_filter($hotspotItemsAll, function ($i) { return isset($i['target']) && $i['target'] === 'image'; }));
    $hotspotItemsFrame360 = array_values(array_filter($hotspotItemsAll, function ($i) { return isset($i['target']) && $i['target'] === 'frame360'; }));
    $hotspotItemsModel3d = array_values(array_filter($hotspotItemsAll, function ($i) { return isset($i['target']) && $i['target'] === 'model3d'; }));

    $hasHotspots = $hotspotsEnabled && !empty($hotspotItemsAll);

    $media = [
        'v360' => [
            'enabled' => $v360Enabled,
            'frame_count' => $v360FrameCount,
            'manifest_url' => $v360ManifestUrl,
            'available' => $has360,
        ],
        'model3d' => [
            'enabled' => $model3dEnabled,
            'src' => $model3dSrc,
            'viewer' => $model3dViewer,
            'available' => $has3D,
        ],
        'video' => [
            'url' => $mainVideoUrl,
            'available' => $hasVideo,
        ],
        'hotspots' => [
            'enabled' => $hotspotsEnabled,
            'target_image' => $hotspotsTargetImage,
            'items' => $hotspotItemsAll,
            'items_image' => $hotspotItemsImage,
            'items_frame360' => $hotspotItemsFrame360,
            'items_model3d' => $hotspotItemsModel3d,
            'available' => $hasHotspots,
        ],
    ];
    
    // Get model info if exists
    $modelNumber = $productt->sku ?? '';
    $productSubtitle = $modelNumber ? "Model {$modelNumber} | Category: {$productt->category->name}" : "Category: {$productt->category->name}";
    
    // Extract key features (will be parsed from product details or use features array)
    $keyFeatures = [];
    if (!empty($productt->features) && is_array($productt->features) && count($productt->features) >= 4) {
        // Use first 4 features if available
        $keyFeatures = array_slice($productt->features, 0, 4);
    }

    // Category header strip (Theme 4 shared)
    $t4Categories = \App\Models\Category::where('status', 1)->orderBy('name')->take(20)->get();
    $activeCategoryId = $productt->category_id ?? null;
@endphp

<!-- Product Details Page - Redesigned to Match Figma -->
<div class="product-details-page-figma">
    @include('frontend.theme4.partials.category_strip', [
        't4Categories' => $t4Categories,
        'activeCategoryId' => $activeCategoryId,
        'sectionClass' => 'rc-item-category-strip t4-categories--product-page',
        'sliderClass' => 't4-cat-strip-slider rc-item-category-strip__slider',
        'itemClass' => 'rc-item-category-strip__item',
        'linkClass' => 'rc-item-category-strip__link',
        'iconClass' => 'rc-item-category-strip__icon',
        'titleClass' => 'rc-item-category-strip__title',
    ])
    <div class="container py-4">
        <!-- Simple Breadcrumb -->
        <nav aria-label="breadcrumb" class="product-breadcrumb-simple mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('front.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('front.category', $productt->category->slug) }}">{{ $productt->category->name }}</a></li>
                @if($productt->subcategory_id)
                <li class="breadcrumb-item"><a href="{{ route('front.category', [$productt->category->slug, $productt->subcategory->slug]) }}">{{ $productt->subcategory->name }}</a></li>
                @endif
                @if($productt->childcategory_id)
                <li class="breadcrumb-item"><a href="{{ route('front.category', [$productt->category->slug, $productt->subcategory->slug, $productt->childcategory->slug]) }}">{{ $productt->childcategory->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $productt->name }}</li>
            </ol>
        </nav>

        <!-- Product Image Gallery - Single Row (Full Width) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="product-gallery-wrapper">
                    <div class="product-gallery-hero-card">
                        <!-- View controls moved outside image container -->
                        <div class="product-image-view-controls">
                                @if($media['v360']['available'])
                                <button type="button" class="view-control-btn" data-bs-toggle="modal" data-bs-target="#modal360View" title="View 360°">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 0L10.463 5.861H16L11.268 9.278L13.731 15.139L8 11.722L2.269 15.139L4.732 9.278L0 5.861H5.537L8 0Z" fill="currentColor"/>
                                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    </svg>
                                    <span>View 360°</span>
                                </button>
                                @endif
                                @if($media['model3d']['available'])
                                <button type="button" class="view-control-btn" data-bs-toggle="modal" data-bs-target="#modal3DView" title="View 3D">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M0 0H5.333V5.333H0V0ZM10.667 0H16V5.333H10.667V0ZM0 10.667H5.333V16H0V10.667ZM10.667 10.667H16V16H10.667V10.667Z" fill="currentColor"/>
                                    </svg>
                                    <span>View 3D</span>
                                </button>
                                @endif
                                @if($media['video']['available'])
                                <button type="button" class="view-control-btn play-video-btn" data-video-url="{{ $media['video']['url'] }}" title="Play Video">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 4L12 8L6 12V4Z" fill="currentColor"/>
                                    </svg>
                                    <span>Play</span>
                                </button>
                                @endif
                        </div>

                        <div class="product-main-image-container" id="productMainImageContainer">

                            <!-- Main Image with Hotspots -->
                            <div class="main-image-wrapper position-relative">
                                <img src="{{ filter_var($productt->photo, FILTER_VALIDATE_URL) ? $productt->photo : asset('assets/images/products/' . $productt->photo) }}" 
                                     alt="{{ $productt->name }}" 
                                     class="main-product-image" 
                                     id="mainProductImage">
                                
                                <!-- Hotspots Overlay -->
                                @if($media['hotspots']['enabled'] && !empty($media['hotspots']['items_image']))
                                <div class="hotspots-overlay">
                                    @foreach($media['hotspots']['items_image'] as $index => $hotspot)
                                        <div class="hotspot-dot" 
                                             style="left: {{ $hotspot['x_percent'] }}%; top: {{ $hotspot['y_percent'] }}%;"
                                             data-hotspot-index="{{ $index }}"
                                             data-hotspot-id="{{ $hotspot['id'] ?? '' }}"
                                             data-hotspot-target="image"
                                             data-hotspot-source="{{ $media['hotspots']['target_image'] ?? '' }}"
                                             data-label="{{ $hotspot['label'] ?? 'Hotspot' }}"
                                             data-desc="{{ $hotspot['description'] ?? '' }}"
                                             data-image="{{ $hotspot['image_url'] ?? '' }}">
                                            <span class="dot-pulse"></span>
                                            <div class="hotspot-tooltip">
                                                <strong>{{ $hotspot['label'] ?? 'Hotspot' }}</strong>
                                                @if(!empty($hotspot['description']))
                                                <p>{{ $hotspot['description'] }}</p>
                                                @endif
                                                @if(!empty($hotspot['image_url']))
                                                <div class="hotspot-tooltip-media">
                                                    @php
                                                        $hotspotImageUrl = $hotspot['image_url'];
                                                        // Normalize URL: if it's a relative path, make it absolute
                                                        if (!filter_var($hotspotImageUrl, FILTER_VALIDATE_URL)) {
                                                            // If it starts with /, use asset helper; otherwise prepend /
                                                            $hotspotImageUrl = $hotspotImageUrl[0] === '/' 
                                                                ? asset(ltrim($hotspotImageUrl, '/')) 
                                                                : asset($hotspotImageUrl);
                                                        }
                                                    @endphp
                                                    <img src="{{ $hotspotImageUrl }}" alt="{{ $hotspot['label'] ?? 'Hotspot' }}">
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Image Navigation Arrows -->
                                <button class="img-nav-arrow img-nav-prev" type="button" aria-label="Previous image">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <button class="img-nav-arrow img-nav-next" type="button" aria-label="Next image">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Thumbnail Strip -->
                        <div class="product-thumbnails-wrapper">
                            <div class="thumbnail-scroll-container">
                                <div class="thumbnails-strip" id="thumbnailsStrip">
                                    @php
                                        $mainThumbVideo = $mainVideoUrl;
                                    @endphp
                                    <div class="thumbnail-item active" data-image-index="0" data-hotspot-source="feature" @if($mainThumbVideo) data-video-url="{{ $mainThumbVideo }}" @endif>
                                        @if($mainThumbVideo)
                                            <div class="video-thumb-overlay">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 5V19L19 12L8 5Z" fill="white"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <img src="{{ filter_var($productt->photo, FILTER_VALIDATE_URL) ? $productt->photo : asset('assets/images/products/' . $productt->photo) }}" alt="Thumbnail">
                                    </div>
                                    @foreach($productt->galleries as $index => $gal)
                                    @php
                                        $galleryVideo = $videoMap['gallery:' . $gal->id] ?? null;
                                    @endphp
                                    <div class="thumbnail-item" data-image-index="{{ $index + 1 }}" data-hotspot-source="gallery_{{ $gal->id }}" @if($galleryVideo) data-video-url="{{ $galleryVideo }}" @endif>
                                        @if($galleryVideo)
                                            <div class="video-thumb-overlay">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 5V19L19 12L8 5Z" fill="white"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <img src="{{ asset('assets/images/galleries/' . $gal->photo) }}" alt="Gallery {{ $index + 1 }}">
                                    </div>
                                    @endforeach
                                    @if($productt->youtube && empty($videoMap))
                                    <div class="thumbnail-item video-thumbnail" data-video-url="{{ $productt->youtube }}">
                                        <div class="video-thumb-overlay">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M8 5V19L19 12L8 5Z" fill="white"/>
                                            </svg>
                                        </div>
                                        <img src="{{ filter_var($productt->photo, FILTER_VALIDATE_URL) ? $productt->photo : asset('assets/images/products/' . $productt->photo) }}" alt="Video">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <button class="thumbnail-nav thumbnail-nav-prev" type="button" aria-label="Scroll thumbnails left">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="thumbnail-nav thumbnail-nav-next" type="button" aria-label="Scroll thumbnails right">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Content - Two Column Grid Layout -->
        <div class="row g-4 mb-5">
            <!-- Wide Column: Product Info, Tabs, Description, Key Features -->
            <div class="col-12 col-md-8 col-lg-8">
                <div class="product-info-content-wrapper">
                    <!-- Product Title & Subtitle -->
                    <div class="product-header-info">
                        <h1 class="product-title-figma">{{ $productt->name }}</h1>
                        <p class="product-subtitle-figma">{{ $productSubtitle }}</p>
                    </div>

                    <!-- Product Tabs -->
                    <ul class="nav product-tabs-figma" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-pane" type="button" role="tab">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specification-tab" data-bs-toggle="tab" data-bs-target="#specification-pane" type="button" role="tab">Specification</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button" role="tab">Reviews</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faqs-pane" type="button" role="tab">FAQ's</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rfq-tab" data-bs-toggle="tab" data-bs-target="#rfq-pane" type="button" role="tab">RFQ</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content product-tab-content-figma" id="productTabContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview-pane" role="tabpanel">
                            <!-- Product Description -->
                            <div class="product-description-section">
                                <h3 class="section-title">Product Description</h3>
                                <div class="product-description-text">
                                    {!! clean($productt->details, ['Attr.EnableID' => true]) !!}
                                </div>
                            </div>

                            <!-- Key Features Grid -->
                            @if(!empty($keyFeatures) || !empty($productt->details))
                            <div class="key-features-section">
                                <h3 class="section-title">Key Features</h3>
                                <div class="features-grid-2x2">
                                        @for($i = 0; $i < 4; $i++)
                                        <div class="feature-card">
                                            <div class="feature-icon-wrapper">
                                                @if($i == 0)
                                                    <!-- Energy Efficient Icon -->
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 2L15.09 8.26L22 9L17 14L18.18 21L12 17.77L5.82 21L7 14L2 9L8.91 8.26L12 2Z" fill="#D30000"/>
                                                    </svg>
                                                @elseif($i == 1)
                                                    <!-- Safety Icon -->
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 7.99H19V11C19 15.52 16.02 19.69 12 20.93C7.98 19.69 5 15.52 5 11V7.99H12ZM11 16H13V14H11V16ZM11 12H13V8H11V12Z" fill="#D30000"/>
                                                    </svg>
                                                @elseif($i == 2)
                                                    <!-- Rapid Heating Icon -->
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 6V2M12 22V18M6 12H2M22 12H18M7.757 7.757L5.636 5.636M18.364 18.364L16.243 16.243M7.757 16.243L5.636 18.364M18.364 5.636L16.243 7.757" stroke="#D30000" stroke-width="2" stroke-linecap="round"/>
                                                        <circle cx="12" cy="12" r="4" fill="#D30000"/>
                                                    </svg>
                                                @else
                                                    <!-- Build Quality Icon -->
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect x="3" y="4" width="18" height="16" rx="2" fill="#D30000"/>
                                                        <path d="M3 10H21" stroke="#fff" stroke-width="2"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <h4 class="feature-title">
                                                @if($i == 0) Energy Efficient
                                                @elseif($i == 1) Multi-Layer Safety
                                                @elseif($i == 2) Rapid Heating
                                                @else Superior Build Quality
                                                @endif
                                            </h4>
                                            <p class="feature-description">
                                                @if($i == 0) 5-Star BEE rated with high-density PUF insulation reducing standby heat loss by up to 20%.
                                                @elseif($i == 1) Thermal cutoff, pressure release valve, and anti-siphon protection for complete peace of mind
                                                @elseif($i == 2) 2000W Incoloy 800 heating element delivers hot water in just 25 minutes from cold start
                                                @else Vitreous enamel coated tank with magnesium anode ensures 2X longer tank life
                                                @endif
                                            </p>
                                        </div>
                                        @endfor
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- Specification Tab -->
                        <div class="tab-pane fade" id="specification-pane" role="tabpanel">
                            <div class="specification-content">
                                @if(!empty($productt->attributes))
                                    @php $attrArr = json_decode($productt->attributes, true); @endphp
                                    @if(!empty($attrArr))
                                    <table class="specification-table">
                                        <tbody>
                                            @foreach($attrArr as $attrKey => $attrVal)
                                                @if(isset($attrVal['values']) && is_array($attrVal['values']))
                                                <tr>
                                                    <td class="spec-label">{{ str_replace('_', ' ', ucwords($attrKey)) }}</td>
                                                    <td class="spec-value">{{ implode(', ', $attrVal['values']) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                @endif
                                @if(empty($attrArr))
                                <p class="text-muted">No specifications available.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews-pane" role="tabpanel">
                            <div class="reviews-content">
                                <h5 class="reviews-title">Ratings & Reviews</h5>
                                <div class="reviews-list">
                                    @forelse($productt->ratings()->orderBy('review_date', 'desc')->get() as $review)
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <img src="{{ $review->user->photo ? asset('assets/images/users/' . $review->user->photo) : asset('assets/images/' . $gs->user_image) }}" alt="{{ $review->user->name }}" class="reviewer-avatar">
                                                <div>
                                                    <strong>{{ $review->user->name }}</strong>
                                                    <div class="review-rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="{{ $i <= $review->rating ? '#EEAE0B' : '#E5E5E5' }}">
                                                                <path d="M8 0L9.8 5.9L16 6.1L11.5 9.8L13.3 15.7L8 12.1L2.7 15.7L4.5 9.8L0 6.1L6.2 5.9L8 0Z"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="review-date">{{ Carbon\Carbon::parse($review->review_date)->diffForHumans() }}</span>
                                        </div>
                                        <p class="review-text">{{ $review->review }}</p>
                                    </div>
                                    @empty
                                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                                    @endforelse
                                </div>

                                @if(Auth::check())
                                <div class="review-form-section mt-4">
                                    <h5>Write a Review</h5>
                                    <form action="{{ route('front.review.submit') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="product_id" value="{{ $productt->id }}">
                                        <input type="hidden" id="reviewRating" name="rating" value="5">
                                        
                                        <div class="rating-input mb-3">
                                            <label>Rating:</label>
                                            <div class="star-rating-selector">
                                                @for($i = 1; $i <= 5; $i++)
                                                <button type="button" class="star-btn" data-rating="{{ $i }}">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 2L14.69 8.79L22 10.24L17 15.14L18.18 22.52L12 19.27L5.82 22.52L7 15.14L2 10.24L9.31 8.79L12 2Z" fill="#E5E5E5"/>
                                                    </svg>
                                                </button>
                                                @endfor
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <textarea name="review" class="form-control" rows="4" placeholder="Write your review..." required></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                </div>
                                @else
                                <p class="text-center mt-4">
                                    <a href="{{ route('user.login') }}" class="btn btn-outline-primary">Login to Review</a>
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- FAQ's Tab -->
                        <div class="tab-pane fade" id="faqs-pane" role="tabpanel">
                            <div class="faqs-content">
                                <p class="text-muted">FAQs section content will be displayed here.</p>
                            </div>
                        </div>

                        <!-- RFQ Tab -->
                        <div class="tab-pane fade" id="rfq-pane" role="tabpanel">
                            <div class="rfq-content">
                                <p class="text-muted">Request for Quote form will be displayed here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Pricing & Actions Sidebar -->
            <div class="col-12 col-md-4 col-lg-4">
                <div class="product-pricing-actions-figma">
                    <!-- Purchase Card (single cohesive card like reference) -->
                    <div class="product-sidebar-card">
                        <!-- Pricing -->
                        <div class="pricing-section">
                            <div class="price-main">
                                <span class="current-price" id="sizeprice">{{ $productt->showPrice() }}</span>
                                @if($productt->previous_price && $productt->showPreviousPrice() != '0')
                                <span class="original-price"><del>{{ $productt->showPreviousPrice() }}</del></span>
                                @endif
                                @if($productt->offPercentage() && round($productt->offPercentage()) > 0)
                                <span class="discount-badge">{{ round($productt->offPercentage()) }}% OFF</span>
                                @endif
                            </div>
                            <p class="tax-info">Inclusive of all taxes</p>
                        </div>

                        <!-- Stock Status -->
                        <div class="stock-status-section">
                            @if($productt->type == 'Physical')
                                @if($productt->emptyStock())
                                <div class="stock-status out-of-stock">
                                    <span class="status-icon"></span>
                                    <span>Out of Stock</span>
                                </div>
                                @else
                                <div class="stock-status in-stock">
                                    <span class="status-icon"></span>
                                    <span>In Stock</span>
                                </div>
                                <p class="ready-ship">Ready to Ship</p>
                                @endif
                            @endif
                        </div>

                        <!-- Quantity Selector -->
                        @if($productt->type == 'Physical' && !$productt->emptyStock())
                        <div class="quantity-section">
                            <label>Quantity:</label>
                            <div class="quantity-input-group">
                                <button type="button" class="qty-btn qty-minus" id="qtyMinus">-</button>
                                <input type="text" class="qty-input" id="order-qty" value="{{ $productt->minimum_qty ?? 1 }}" readonly>
                                <button type="button" class="qty-btn qty-plus" id="qtyPlus">+</button>
                            </div>
                        </div>
                        @endif

                        <!-- Check Delivery -->
                        @if($productt->type == 'Physical')
                        <div class="delivery-check-section">
                            <label>Check Delivery</label>
                            <div class="delivery-input-group">
                                <input type="text" class="form-control" id="pinCodeInput" placeholder="Enter PIN code" maxlength="6">
                                <button type="button" class="btn-check-delivery" id="checkDeliveryBtn">Check</button>
                            </div>
                        </div>
                        @endif

                        <!-- CTA Buttons -->
                        <div class="cta-buttons-section">
                            <button type="button" class="btn-buy-now w-100" id="addtobycard">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="margin-right: 6px;">
                                    <path d="M9 0L11.6942 7.7918H18L12.3593 12.9164L15.0534 21.2082L9 16.0836L2.94658 21.2082L5.64074 12.9164L0 7.7918H6.30583L9 0Z" fill="currentColor"/>
                                </svg>
                                Buy Now
                            </button>
                            <button type="button" class="btn-add-cart w-100" id="addtodetailscart">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="margin-right: 6px;">
                                    <path d="M5 2H3C2.44772 2 2 2.44772 2 3V4M2 4L3 14H15L16 4M2 4H4M5 6H15M15 6L14 16H4L3 6M15 6V13C15 14.1046 14.1046 15 13 15H5C3.89543 15 3 14.1046 3 13V6" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                </svg>
                                Add to Cart
                            </button>
                            <button type="button" class="btn-add-wishlist" id="addToWishlist">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M10 3.5C7.5 1.5 4 1.5 2.5 4C1 6.5 1.5 10 4 12.5L10 18L16 12.5C18.5 10 19 6.5 17.5 4C16 1.5 12.5 1.5 10 3.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                                Add to Wishlist
                            </button>
                        </div>
                    </div>

                    <!-- Seller Information Card -->
                    <div class="seller-info-card">
                        <div class="seller-header">
                            <div class="seller-logo">
                                @if($productt->user_id != 0 && isset($productt->user) && $productt->user->shop_image)
                                    <img src="{{ asset('assets/images/vendorbanner/' . $productt->user->shop_image) }}" alt="{{ $productt->user->shop_name }}">
                                @else
                                    <span>{{ substr($productt->user_id != 0 && isset($productt->user) ? $productt->user->shop_name : 'Admin', 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="seller-details">
                                <h4>{{ $productt->user_id != 0 && isset($productt->user) ? $productt->user->shop_name : App\Models\Admin::find(1)->shop_name }}</h4>
                                <p class="seller-tagline">{{ $productt->user_id != 0 && isset($productt->user) ? ($productt->user->shop_details ?? 'Verified Seller') : 'Official Store' }}</p>
                                @if($productt->user_id != 0 && isset($productt->user) && $productt->user->shop_message)
                                <p class="seller-description">{{ $productt->user->shop_message }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="seller-features-grid">
                            <div class="seller-feature-badge">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#D30000">
                                    <path d="M8 1L10.163 4.735L14.5 5.328L11.677 8.058L12.359 12.5L8 10.472L3.641 12.5L4.323 8.058L1.5 5.328L5.837 4.735L8 1Z"/>
                                </svg>
                                <span>Verified</span>
                            </div>
                            <div class="seller-feature-badge">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#D30000">
                                    <path d="M8 0L10.163 4.735L14.5 5.328L11.677 8.058L12.359 12.5L8 10.472L3.641 12.5L4.323 8.058L1.5 5.328L5.837 4.735L8 0Z"/>
                                </svg>
                                <span>Quality</span>
                            </div>
                            <div class="seller-feature-badge">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#D30000">
                                    <path d="M8 0L10.163 4.735L14.5 5.328L11.677 8.058L12.359 12.5L8 10.472L3.641 12.5L4.323 8.058L1.5 5.328L5.837 4.735L8 0Z"/>
                                </svg>
                                <span>Service</span>
                            </div>
                            <div class="seller-feature-badge">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#D30000">
                                    <path d="M8 0L10.163 4.735L14.5 5.328L11.677 8.058L12.359 12.5L8 10.472L3.641 12.5L4.323 8.058L1.5 5.328L5.837 4.735L8 0Z"/>
                                </svg>
                                <span>Support</span>
                            </div>
                        </div>
                    </div>

                    <!-- Download Resources Section (Figma card) -->
                    @php
                        /**
                         * Security note:
                         * - `$productt->file` is used for DIGITAL product delivery (stored in /assets/files) and should NOT be exposed publicly here.
                         * - For this "documentation" card, only allow a public external link for PHYSICAL products.
                         */
                        $docUrl = null;
                        if ($productt->type === 'Physical' && !empty($productt->link)) {
                            $docUrl = $productt->link;
                        }
                    @endphp
                    <div class="download-resources-section">
                        <h4>Download Resources</h4>
                        <div class="download-item {{ $docUrl ? '' : 'is-disabled' }}">
                            <div class="download-info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div>
                                    <span>Product Documentation</span>
                                    <small>{{ $docUrl ? 'External link' : 'Not available' }}</small>
                                </div>
                            </div>
                            @if($docUrl)
                                <a class="download-btn" href="{{ $docUrl }}" target="_blank" rel="noopener" aria-label="Download Product Documentation">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M10 3V17M10 17L5 12M10 17L15 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @else
                                <button class="download-btn" type="button" disabled aria-label="Download not available">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M10 3V17M10 17L5 12M10 17L15 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Products Section -->
    <div class="similar-products-section">
        <div class="container py-5">
            <h2 class="section-heading">Similar In Stock Products</h2>
            <div class="similar-products-slider">
                @php
                    $similarProducts = App\Models\Product::where('category_id', $productt->category_id)
                        ->where('id', '!=', $productt->id)
                        ->where('status', 1)
                        // Keep consistent with Product::emptyStock() which only treats "0" as empty.
                        ->where(function($q){
                            $q->whereNull('stock')->orWhere('stock', '>', 0);
                        })
                        ->withCount('ratings')
                        ->withAvg('ratings', 'rating')
                        ->take(12)
                        ->get();
                @endphp
                @forelse($similarProducts as $similar)
                <div class="similar-product-card">
                    @if($similar->offPercentage() && round($similar->offPercentage()) > 0)
                    <span class="product-badge-best">Best Seller</span>
                    <span class="product-badge-discount">{{ round($similar->offPercentage()) }}% OFF</span>
                    @endif
                    <img src="{{ $similar->thumbnail ? asset('assets/images/thumbnails/' . $similar->thumbnail) : asset('assets/images/products/' . $similar->photo) }}" alt="{{ $similar->name }}">
                    <h5>{{ $similar->name }}</h5>
                    <div class="product-rating">
                        @php $avgRating = $similar->ratings_avg_rating ?? 0; @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="{{ $i <= round($avgRating) ? '#EEAE0B' : '#E5E5E5' }}">
                                <path d="M7 0L8.57 5.26L14 6.09L10 9.74L11.14 14.91L7 12.09L2.86 14.91L4 9.74L0 6.09L5.43 5.26L7 0Z"/>
                            </svg>
                        @endfor
                        <span>{{ number_format($avgRating, 1) }} ({{ $similar->ratings_count ?? 0 }})</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">{{ $similar->showPrice() }}</span>
                        @if($similar->previous_price)
                        <span class="price-original"><del>{{ $similar->showPreviousPrice() }}</del></span>
                        @endif
                    </div>
                    <div class="product-actions">
                        <button class="btn-add-cart-sm" data-product-id="{{ $similar->id }}">Add to Cart</button>
                        <button
    type="button"
    class="btn-buy-now-sm btn-view-product"
    data-product-id="{{ $similar->id }}"
    data-href="{{ route('front.product', $similar->slug) }}">
    View
</button>

                    </div>
                </div>
                @empty
                <div class="similar-products-empty">
                    <div class="similar-products-empty__card">
                        <div class="similar-products-empty__title">Products not available</div>
                        <div class="similar-products-empty__sub">No similar in-stock products found for this category right now.</div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Media Modals (reusable components) -->
<x-media.modal-360 :media="$media" />
<x-media.modal-3d :media="$media" />
<x-media.modal-video />

<!-- Hidden Inputs for Dynamic Data -->
<input type="hidden" id="product_price" value="{{ round($productt->vendorPrice() * $curr->value, 2) }}">
<input type="hidden" id="product_id" value="{{ $productt->id }}">
<input type="hidden" id="curr_pos" value="{{ $gs->currency_format }}">
<input type="hidden" id="curr_sign" value="{{ $curr->sign }}">
<input type="hidden" id="affilate_user" value="{{ $affilate_user ?? 0 }}">

@endsection

@section('script')
<script>
(function($) {
    'use strict';

    // Product Image Gallery Slider
    let currentImageIndex = 0;
    const mainImages = [
        @if($productt->photo)
        '{{ filter_var($productt->photo, FILTER_VALIDATE_URL) ? $productt->photo : asset("assets/images/products/" . $productt->photo) }}',
        @endif
        @foreach($productt->galleries as $gal)
        '{{ asset("assets/images/galleries/" . $gal->photo) }}',
        @endforeach
    ];

    // Hotspot base mapping: admin hotspots.target_image = "feature" or "gallery_{id}"
    const imageSources = [
        'feature',
        @foreach($productt->galleries as $gal)
        'gallery_{{ $gal->id }}',
        @endforeach
    ];

    const videoSources = [
        @json($mainVideoUrl ?? ''),
        @foreach($productt->galleries as $gal)
        @json($videoMap['gallery:' . $gal->id] ?? ''),
        @endforeach
    ];

    const hotspotTargetImage = @json($media['hotspots']['target_image'] ?? '');

    function applyImageHotspotVisibility(sourceKey) {
        const overlay = document.querySelector('.main-image-wrapper .hotspots-overlay');
        if (!overlay) return;

        // If admin didn't select a target image, show hotspots on all images.
        if (!hotspotTargetImage) {
            overlay.style.display = '';
            return;
        }

        overlay.style.display = (sourceKey && sourceKey === hotspotTargetImage) ? '' : 'none';
        // Hide any open tooltips when switching images
        $('.hotspot-tooltip').hide();
    }

    const $playBtn = $('.play-video-btn');

    function updateVideoControl(index) {
        if (!$playBtn.length) return;
        const url = videoSources[index] || '';
        if (url) {
            $playBtn.attr('data-video-url', url).removeClass('d-none').attr('aria-hidden', 'false');
        } else {
            $playBtn.attr('data-video-url', '').addClass('d-none').attr('aria-hidden', 'true');
        }
    }

    function updateMainImage(index) {
        if (mainImages[index]) {
            $('#mainProductImage').attr('src', mainImages[index]);
            $('.thumbnail-item').removeClass('active');
            $(`.thumbnail-item[data-image-index="${index === 0 ? 0 : index}"]`).addClass('active');
            currentImageIndex = index;
            applyImageHotspotVisibility(imageSources[index] || 'feature');
            updateVideoControl(index);
        }
    }

    $('.thumbnail-item').on('click', function() {
        const index = $(this).data('image-index');
        if ($(this).hasClass('video-thumbnail')) {
            const videoUrl = $(this).data('video-url');
            if (videoUrl) {
                // Open the page video modal (lazy-loaded)
                $('.play-video-btn').attr('data-video-url', videoUrl).trigger('click');
            }
        } else {
            updateMainImage(index);
        }
    });

    $('.img-nav-prev').on('click', function() {
        const newIndex = currentImageIndex > 0 ? currentImageIndex - 1 : mainImages.length - 1;
        updateMainImage(newIndex);
    });

    $('.img-nav-next').on('click', function() {
        const newIndex = currentImageIndex < mainImages.length - 1 ? currentImageIndex + 1 : 0;
        updateMainImage(newIndex);
    });

    // Apply initial hotspot visibility on first render
    applyImageHotspotVisibility(imageSources[currentImageIndex] || 'feature');
    updateVideoControl(currentImageIndex);

    // Hotspot interactions
    $('.hotspot-dot').on('click', function() {
        const tooltip = $(this).find('.hotspot-tooltip');
        $('.hotspot-tooltip').not(tooltip).hide();
        tooltip.toggle();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.hotspot-dot').length) {
            $('.hotspot-tooltip').hide();
        }
    });

    // Quantity controls
    $('#qtyPlus').on('click', function() {
        const current = parseInt($('#order-qty').val()) || 1;
        const stock = parseInt($('#stock').val()) || 999;
        if (current < stock) {
            $('#order-qty').val(current + 1);
        }
    });

    $('#qtyMinus').on('click', function() {
        const current = parseInt($('#order-qty').val()) || 1;
        if (current > 1) {
            $('#order-qty').val(current - 1);
        }
    });

    // Star rating selector
    $('.star-btn').on('click', function() {
        const rating = $(this).data('rating');
        $('#reviewRating').val(rating);
        $('.star-btn').each(function(index) {
            if (index < rating) {
                $(this).find('svg path').attr('fill', '#EEAE0B');
            } else {
                $(this).find('svg path').attr('fill', '#E5E5E5');
            }
        });
    });

    // Wishlist
    $('#addToWishlist').on('click', function() {
        @if(Auth::check())
            const url = '{{ route("user-wishlist-add", $productt->id) }}';
            $.get(url, function(data) {
                toastr.success('Added to wishlist');
            });
        @else
            window.location.href = '{{ route("user.login") }}';
        @endif
    });

    // Check delivery (placeholder)
    $('#checkDeliveryBtn').on('click', function() {
        const pinCode = $('#pinCodeInput').val();
        if (pinCode && pinCode.length === 6) {
            // Implement delivery check API call
            toastr.info('Checking delivery for PIN: ' + pinCode);
        } else {
            toastr.error('Please enter a valid 6-digit PIN code');
        }
    });

    $(document).on('click', '.btn-view-product', function () {
        const href = $(this).data('href');
        if (href) {
            window.location.href = href;
            return;
        }

        // Fallback (legacy): Navigate using product ID if provided.
        const productId = $(this).data('product-id');
        if (!productId) return;
        window.location.href = "{{ url('/item') }}/" + productId;
    });


    // Media viewers are initialized lazily in assets/front/js/product-media-modals.js

})(jQuery);
</script>
<script src="{{ asset('assets/front/js/product-media-modals.js') }}?v={{ time() }}"></script>
@endsection