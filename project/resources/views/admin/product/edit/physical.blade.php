@extends('layouts.admin')
@section('styles')
    <link href="{{ asset('assets/admin/css/product.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/jquery.Jcrop.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/Jcrop-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/select2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/advanced-media.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading"> {{ __('Edit Product') }}<a class="add-btn" href="{{ url()->previous() }}"><i
                                class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="{{ route('admin-prod-index') }}">{{ __('Products') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Physical Product') }}</a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Edit') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <form id="geniusform" action="{{ route('admin-prod-update', $data->id) }}" method="POST"
            enctype="multipart/form-data">
            {{ csrf_field() }}
            @include('alerts.admin.form-both')
            <div class="row">
                <div class="col-lg-8">
                    <div class="add-product-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="product-description">
                                    <div class="body-area">
                                        <div class="gocover"
                                            style="background: url({{ asset('assets/images/' . $gs->admin_loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                                        </div>

                                        @php
                                            $mediaExtra = json_decode($data->media_extra, true);
                                            if (!is_array($mediaExtra)) { $mediaExtra = []; }
                                            $v360         = isset($mediaExtra['v360'])     && is_array($mediaExtra['v360'])     ? $mediaExtra['v360']     : [];
                                            $hotspots     = isset($mediaExtra['hotspots']) && is_array($mediaExtra['hotspots']) ? $mediaExtra['hotspots'] : [];
                                            $model3d      = isset($mediaExtra['model3d'])  && is_array($mediaExtra['model3d'])  ? $mediaExtra['model3d']  : [];
                                            $v360Count    = isset($v360['frame_count']) ? (int) $v360['frame_count'] : 0;
                                            // Also check filesystem for accurate frame count display (Issue #3 fix)
                                            $framesManifest = public_path('assets/products_media/' . $data->id . '/360/frames/manifest.json');
                                            $v360HasFrames = $v360Count > 0 || file_exists($framesManifest);
                                            $hotspotItems = isset($hotspots['items']) && is_array($hotspots['items']) ? $hotspots['items'] : [];
                                            $hotspotBase  = isset($hotspots['target_image']) ? (string) $hotspots['target_image'] : '';
                                            $mediaVideos  = \App\Models\ProductMediaVideo::where('product_id', $data->id)->get();
                                            $mediaVideoMap = [];
                                            foreach ($mediaVideos as $video) {
                                                $mediaVideoMap[$video->target_type . ':' . (string) $video->target_id] = $video;
                                            }
                                        @endphp
                                        @include('admin.product.partials.advanced-media-panel', [
                                            'product'      => $data,
                                            'mode'         => 'edit',
                                            'productId'    => $data->id,
                                            'mediaExtra'   => $mediaExtra,
                                            'v360'         => $v360,
                                            'hotspots'     => $hotspots,
                                            'model3d'      => $model3d,
                                            'mediaVideos'  => $mediaVideos,
                                            'mediaVideoMap'=> $mediaVideoMap,
                                        ])

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Name') }}* </h4>
                                                    <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="input-field"
                                                    placeholder="{{ __(" Enter Product
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            												Name") }}"
                                                    name="name" required="" value="{{ $data->name }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Sku') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="input-field"
                                                    placeholder="{{ __('Enter Product Sku') }}" name="sku"
                                                    required="" value="{{ $data->sku }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Category') }}*</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <select id="cat" name="category_id" required="">
                                                    <option>{{ __('Select Category') }}</option>
                                                    @foreach ($cats as $cat)
                                                        <option data-href="{{ route('admin-subcat-load', $cat->id) }}"
                                                            value="{{ $cat->id }}"
                                                            {{ $cat->id == $data->category_id ? 'selected' : '' }}>
                                                            {{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Sub Category') }}*</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <select id="subcat" name="subcategory_id">
                                                    <option value="">{{ __('Select Sub Category') }}</option>
                                                    @if ($data->subcategory_id == null)
                                                        @foreach ($data->category->subs as $sub)
                                                            <option
                                                                data-href="{{ route('admin-childcat-load', $sub->id) }}"
                                                                value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($data->category->subs as $sub)
                                                            <option
                                                                data-href="{{ route('admin-childcat-load', $sub->id) }}"
                                                                value="{{ $sub->id }}"
                                                                {{ $sub->id == $data->subcategory_id ? 'selected' : '' }}>
                                                                {{ $sub->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Child Category') }}*</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <select id="childcat" name="childcategory_id"
                                                    {{ $data->subcategory_id == null ? 'disabled' : '' }}>
                                                    <option value="">{{ __('Select Child Category') }}</option>
                                                    @if ($data->subcategory_id != null)
                                                        @if ($data->childcategory_id == null)
                                                            @foreach ($data->subcategory->childs as $child)
                                                                <option value="{{ $child->id }}">{{ $child->name }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($data->subcategory->childs as $child)
                                                                <option value="{{ $child->id }} "
                                                                    {{ $child->id == $data->childcategory_id ? 'selected' : '' }}>
                                                                    {{ $child->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        @php
                                            $selectedAttrs = json_decode($data->attributes, true);

                                        @endphp

                                        {{-- Attributes of category starts --}}
                                        <div id="catAttributes">
                                            @php
                                                $catAttributes = !empty($data->category->attributes)
                                                    ? $data->category->attributes
                                                    : '';
                                            @endphp
                                            @if (!empty($catAttributes))
                                                @foreach ($catAttributes as $catAttribute)
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="left-area">
                                                                <h4 class="heading">{{ $catAttribute->name }} *</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            @php
                                                                $i = 0;
                                                            @endphp
                                                            @foreach ($catAttribute->attribute_options as $optionKey => $option)
                                                                @php
                                                                    $inName = $catAttribute->input_name;
                                                                    $checked = 0;
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="col-lg-5">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                id="{{ $catAttribute->input_name }}{{ $option->id }}"
                                                                                name="{{ $catAttribute->input_name }}[]"
                                                                                value="{{ $option->name }}"
                                                                                class="custom-control-input attr-checkbox"
                                                                                @if (is_array($selectedAttrs) && array_key_exists($catAttribute->input_name, $selectedAttrs)) @if (is_array($selectedAttrs["$inName"]['values']) && in_array($option->name, $selectedAttrs["$inName"]['values']))
																		checked
																		@php
																			$checked = 1;
																		@endphp @endif
                                                                                @endif
                                                                            >
                                                                            <label class="custom-control-label"
                                                                                for="{{ $catAttribute->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="col-lg-7 {{ $catAttribute->price_status == 0 ? 'd-none' : '' }}">
                                                                        <div class="row">
                                                                            <div class="col-2">
                                                                                +
                                                                            </div>
                                                                            <div class="col-10">
                                                                                <div class="price-container">
                                                                                    <span
                                                                                        class="price-curr">{{ $sign->sign }}</span>
                                                                                    <input type="text"
                                                                                        class="input-field price-input"
                                                                                        id="{{ $catAttribute->input_name }}{{ $option->id }}_price"
                                                                                        data-name="{{ $catAttribute->input_name }}_price[]"
                                                                                        placeholder="0.00 (Additional Price)"
                                                                                        value="{{ !empty($selectedAttrs["$inName"]['prices'][$i]) && $checked == 1
                                                                                            ? round($selectedAttrs["$inName"]['prices'][$i] * $sign->value, 2)
                                                                                            : '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    if ($checked == 1) {
                                                                        $i++;
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        {{-- Attributes of category ends --}}

                                        {{-- Attributes of subcategory starts --}}
                                        <div id="subcatAttributes">
                                            @php
                                                $subAttributes = !empty($data->subcategory->attributes)
                                                    ? $data->subcategory->attributes
                                                    : '';
                                            @endphp
                                            @if (!empty($subAttributes))
                                                @foreach ($subAttributes as $subAttribute)
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-2">
                                                            <div class="left-area">
                                                                <h4 class="heading">{{ $subAttribute->name }} *</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            @php
                                                                $i = 0;
                                                            @endphp
                                                            @foreach ($subAttribute->attribute_options as $option)
                                                                @php
                                                                    $inName = $subAttribute->input_name;
                                                                    $checked = 0;
                                                                @endphp

                                                                <div class="row">
                                                                    <div class="col-lg-5">
                                                                        <div class="custom-control custom-checkbox">

                                                                            <input type="checkbox"
                                                                                id="{{ $subAttribute->input_name }}{{ $option->id }}"
                                                                                name="{{ $subAttribute->input_name }}[]"
                                                                                value="{{ $option->name }}"
                                                                                class="custom-control-input attr-checkbox"
                                                                                @if (is_array($selectedAttrs) && array_key_exists($subAttribute->input_name, $selectedAttrs)) @php
																	$inName = $subAttribute->input_name;
																	@endphp
																	@if (is_array($selectedAttrs["$inName"]['values']) && in_array($option->name, $selectedAttrs["$inName"]['values']))
																		checked
																		@php
																		$checked = 1;
																		@endphp @endif
                                                                                @endif
                                                                            >

                                                                            <label class="custom-control-label"
                                                                                for="{{ $subAttribute->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="col-lg-7 {{ $subAttribute->price_status == 0 ? 'd-none' : '' }}">
                                                                        <div class="row">
                                                                            <div class="col-2">
                                                                                +
                                                                            </div>
                                                                            <div class="col-10">
                                                                                <div class="price-container">
                                                                                    <span
                                                                                        class="price-curr">{{ $sign->sign }}</span>
                                                                                    <input type="text"
                                                                                        class="input-field price-input"
                                                                                        id="{{ $subAttribute->input_name }}{{ $option->id }}_price"
                                                                                        data-name="{{ $subAttribute->input_name }}_price[]"
                                                                                        placeholder="0.00 (Additional Price)"
                                                                                        value="{{ !empty($selectedAttrs["$inName"]['prices'][$i]) && $checked == 1
                                                                                            ? round($selectedAttrs["$inName"]['prices'][$i] * $sign->value, 2)
                                                                                            : '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    if ($checked == 1) {
                                                                        $i++;
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        {{-- Attributes of subcategory ends --}}

                                        {{-- Attributes of child category starts --}}
                                        <div id="childcatAttributes">
                                            @php
                                                $childAttributes = !empty($data->childcategory->attributes)
                                                    ? $data->childcategory->attributes
                                                    : '';
                                            @endphp
                                            @if (!empty($childAttributes))
                                                @foreach ($childAttributes as $childAttribute)
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-2">
                                                            <div class="left-area">
                                                                <h4 class="heading">{{ $childAttribute->name }} *</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            @php
                                                                $i = 0;
                                                            @endphp
                                                            @foreach ($childAttribute->attribute_options as $optionKey => $option)
                                                                @php
                                                                    $inName = $childAttribute->input_name;
                                                                    $checked = 0;
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="col-lg-5">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                id="{{ $childAttribute->input_name }}{{ $option->id }}"
                                                                                name="{{ $childAttribute->input_name }}[]"
                                                                                value="{{ $option->name }}"
                                                                                class="custom-control-input attr-checkbox"
                                                                                @if (is_array($selectedAttrs) && array_key_exists($childAttribute->input_name, $selectedAttrs)) @php
																	$inName = $childAttribute->input_name;
																	@endphp
																	@if (is_array($selectedAttrs["$inName"]['values']) && in_array($option->name, $selectedAttrs["$inName"]['values']))
																		checked
																		@php
																		$checked = 1;
																		@endphp @endif
                                                                                @endif
                                                                            >

                                                                            <label class="custom-control-label"
                                                                                for="{{ $childAttribute->input_name }}{{ $option->id }}">{{ $option->name }}</label>
                                                                        </div>
                                                                    </div>


                                                                    <div
                                                                        class="col-lg-7 {{ $childAttribute->price_status == 0 ? 'd-none' : '' }}">
                                                                        <div class="row">
                                                                            <div class="col-2">
                                                                                +
                                                                            </div>
                                                                            <div class="col-10">
                                                                                <div class="price-container">
                                                                                    <span
                                                                                        class="price-curr">{{ $sign->sign }}</span>
                                                                                    <input type="text"
                                                                                        class="input-field price-input"
                                                                                        id="{{ $childAttribute->input_name }}{{ $option->id }}_price"
                                                                                        data-name="{{ $childAttribute->input_name }}_price[]"
                                                                                        placeholder="0.00 (Additional Price)"
                                                                                        value="{{ !empty(
                                                                                            $selectedAttrs[
                                                                                                "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        																		$inName"
                                                                                            ]['prices'][$i]
                                                                                        ) && $checked == 1
                                                                                            ? round($selectedAttrs["$inName"]['prices'][$i] * $sign->value, 2)
                                                                                            : '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    if ($checked == 1) {
                                                                        $i++;
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        {{-- Attributes of child category ends --}}

                                        <div class="{{ !empty($data->size) ? ' showbox' : '' }}" id="stckprod">
                                            <div class="row">

                                                <div class="col-lg-12">
                                                    <div class="checkbox-wrapper">
                                                        <input type="checkbox" name="measure_check" class="checkclick1"
                                                            id="allowProductMeasurement" value="1"
                                                            {{ $data->measure == null ? '' : 'checked' }}>
                                                        <label
                                                            for="allowProductMeasurement">{{ __('Allow Product Measurement') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="product_condition_check"
                                                            type="checkbox" id="conditionCheck" value="1"
                                                            {{ $data->product_condition != 0 ? 'checked' : '' }}>
                                                        <label
                                                            for="conditionCheck">{{ __('Allow Product Condition') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="{{ $data->product_condition == 0 ? ' showbox' : '' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Condition') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <select name="product_condition">
                                                        <option value="2"
                                                            {{ $data->product_condition == 2 ? 'selected' : '' }}>
                                                            {{ __('New') }}</option>
                                                        <option value="1"
                                                            {{ $data->product_condition == 1 ? 'selected' : '' }}>
                                                            {{ __('Used') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="preordered_check"
                                                            type="checkbox" id="preorderedCheck" value="1"
                                                            {{ $data->preordered != 0 ? 'checked' : '' }}>
                                                        <label
                                                            for="preorderedCheck">{{ __('Allow Product Preorder') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="{{ $data->preordered == 0 ? ' showbox' : '' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Preorder') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <select name="preordered">
                                                        <option value="1"
                                                            {{ $data->preordered == 1 ? 'selected' : '' }}>
                                                            {{ __('Sale') }}</option>
                                                        <option value="2"
                                                            {{ $data->preordered == 2 ? 'selected' : '' }}>
                                                            {{ __('Preordered') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="minimum_qty_check"
                                                            type="checkbox" id="check111" value="1"
                                                            {{ $data->minimum_qty != null ? 'checked' : '' }}>
                                                        <label for="check111">{{ __('Allow Minimum Order Qty') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="{{ $data->minimum_qty != null ? '' : ' showbox' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Minimum Order Qty') }}* </h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="number" class="input-field" min="1"
                                                        placeholder="{{ __('Minimum Order Qty') }}" name="minimum_qty"
                                                        value="{{ $data->minimum_qty == null ? '' : $data->minimum_qty }}">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="shipping_time_check"
                                                            type="checkbox" id="check1" value="1"
                                                            {{ $data->ship != null ? 'checked' : '' }}>
                                                        <label
                                                            for="check1">{{ __('Allow Estimated Shipping Time') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="{{ $data->ship != null ? '' : ' showbox' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Estimated Shipping Time') }}*
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="text" class="input-field"
                                                        placeholder="{{ __('Estimated Shipping Time') }}" name="ship"
                                                        value="{{ $data->ship == null ? '' : $data->ship }}">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclickc" name="color_check" type="checkbox"
                                                            id="check3" value="1"
                                                            {{ is_array($data->color_all) ? 'checked' : '' }}>
                                                        <label for="check3">{{ __('Allow Product Colors') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        @php
                                            
                                            
                                           
                                           
                                        @endphp
                                        <div class="{{is_array($data->color_all) ? '' : ' showbox' }}">
                                            <div class="row">

                                                <div class="col-lg-12">
                                                    <div class="select-input-color" id="color-section">
                                                        @if (is_array($data->color_all))
                                                            
                                                        @foreach ($data->color_all as $key => $color)
                                                        <div class="size-area">
                                                            <span class="remove size-remove"><i
                                                                    class="fas fa-times"></i></span>
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12">
                                                                    <label>
                                                                        {{ __('Color') }} :
                                                                    </label>
                                                                    <div class="color-area">
                                                                        <div
                                                                            class="input-group colorpicker-component cp">
                                                                            <input type="text" name="color_all[]"
                                                                                value="{{ $color }}"
                                                                                class="input-field cp tcolor" />
                                                                            <span
                                                                                class="input-group-addon"><i></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    @endforeach

                                                        @endif
                                                        
                                                    </div>
                                                    <a href="javascript:;" id="color-btn" class="add-more mt-4 mb-3"><i
                                                            class="fas fa-plus"></i>{{ __('Add More Color') }} </a>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="{{ $data->measure == null ? 'showbox' : '' }}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Measurement') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <select id="product_measure">
                                                        <option value=""
                                                            {{ $data->measure == null ? 'selected' : '' }}>
                                                            {{ __('None') }}</option>
                                                        <option value="Gram"
                                                            {{ $data->measure == 'Gram' ? 'selected' : '' }}>
                                                            {{ __('Gram') }}</option>
                                                        <option value="Kilogram"
                                                            {{ $data->measure == 'Kilogram' ? 'selected' : '' }}>
                                                            {{ __('Kilogram') }}</option>
                                                        <option value="Litre"
                                                            {{ $data->measure == 'Litre' ? 'selected' : '' }}>
                                                            {{ __('Litre') }}</option>
                                                        <option value="Pound"
                                                            {{ $data->measure == 'Pound' ? 'selected' : '' }}>
                                                            {{ __('Pound') }}</option>
                                                        <option value="Custom"
                                                            {{ in_array($data->measure, explode(',', 'Gram,Kilogram,Litre,Pound')) ? '' : 'selected' }}>
                                                            {{ __('Custom') }}</option>
                                                    </select>
                                                </div>
                                                {{-- <div class="col-lg-1"></div> --}}
                                                <div class="col-lg-6 {{ in_array($data->measure, explode(',', 'Gram,Kilogram,Litre,Pound')) ? 'hidden' : '' }}"
                                                    id="measure">
                                                    <input name="measure" type="text" id="measurement"
                                                        class="input-field" placeholder="Enter Unit"
                                                        value="{{ $data->measure }}">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input name="stock_check" class="stock-check" type="checkbox"
                                                            id="size-check" value="1"
                                                            {{ !empty($data->size) ? 'checked' : '' }}>
                                                        <label for="size-check"
                                                            class="stock-text">{{ __('Manage Stock') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="row {{ !empty($data->size) ? ' d-none' : '' }}" id="default_stock">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Stock') }}*</h4>
                                                    <p class="sub-heading">
                                                        {{ __('(Leave Empty will Show Always Available)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input name="stock" type="number" class="input-field"
                                                    placeholder="e.g 20" value="{{ $data->stock }}" min="0">
                                            </div>
                                        </div>


                                        @php
                                        if(is_array($data->size)){
                                            $sizes = $data->size;
                                        }else{
                                            $sizes = array_filter(explode(',', $data->size));
                                        }
                                        @endphp

                                        <div class="{{ !empty($sizes) ? '' : ' showbox' }}" id="size-display">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="product-size-details" id="size-section">


                                                        @foreach ($sizes as $key => $size)
                                                            <div class="size-area">
                                                                <span class="remove size-remove"><i
                                                                        class="fas fa-times"></i></span>
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4">
                                                                        <label>
                                                                            {{ __('Size Name') }} :
                                                                            <span>
                                                                                {{ __('(eg. S,M,L,XL,3XL,4XL)') }}
                                                                            </span>
                                                                        </label>
                                                                        <input type="text" name="size[]"
                                                                            class="input-field tsize"
                                                                            placeholder="{{ __('Enter Product Size') }}"
                                                                            value="{{ $size }}" required="">
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-4">
                                                                        <label>
                                                                            {{ __('Size Qty') }} :
                                                                            <span>
                                                                                {{ __('(Quantity of this size)') }}
                                                                            </span>
                                                                        </label>
                                                                        <input type="number" name="size_qty[]" required
                                                                            class="input-field"
                                                                            placeholder="{{ __('Size Qty') }}"
                                                                            value="{{ $data->size_qty[$key] }}"
                                                                            min="1">
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-4">
                                                                        <label>
                                                                            {{ __('Size Price') }} :
                                                                            <span>
                                                                                {{ __('(Added with base price)') }}
                                                                            </span>
                                                                        </label>
                                                                        <input type="number" name="size_price[]" required
                                                                            class="input-field"
                                                                            placeholder="{{ __('Size Price') }}"
                                                                            value="{{ round($data->size_price[$key] * $curr->value, 2) }}"
                                                                            min="0">
                                                                    </div>


                                                                </div>
                                                            </div>
                                                        @endforeach


                                                    </div>

                                                    <a href="javascript:;" id="size-btn" class="add-more"><i
                                                            class="fas fa-plus"></i>{{ __('Add More') }} </a>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="whole_check" type="checkbox"
                                                            id="whole_check" value="1"
                                                            {{ !empty($data->whole_sell_qty) ? 'checked' : '' }}>
                                                        <label
                                                            for="whole_check">{{ __('Allow Product Whole Sell') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="{{ !empty($data->whole_sell_qty) ? '' : ' showbox' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">

                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="featured-keyword-area">
                                                        <div class="feature-tag-top-filds" id="whole-section">
                                                            @if (!empty($data->whole_sell_qty))

                                                                @foreach ($data->whole_sell_qty as $key => $data1)
                                                                    <div class="feature-area">
                                                                        <span class="remove whole-remove"><i
                                                                                class="fas fa-times"></i></span>
                                                                        <div class="row">
                                                                            <div class="col-lg-6">
                                                                                <input type="number"
                                                                                    name="whole_sell_qty[]"
                                                                                    class="input-field"
                                                                                    placeholder="{{ __('Enter Quantity') }}"
                                                                                    min="0"
                                                                                    value="{{ $data->whole_sell_qty[$key] }}"
                                                                                    required="">
                                                                            </div>

                                                                            <div class="col-lg-6">
                                                                                <input type="number"
                                                                                    name="whole_sell_discount[]"
                                                                                    class="input-field"
                                                                                    placeholder="{{ __('Enter Discount Percentage') }}"
                                                                                    min="0"
                                                                                    value="{{ $data->whole_sell_discount[$key] }}"
                                                                                    required="">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="feature-area">
                                                                    <span class="remove whole-remove"><i
                                                                            class="fas fa-times"></i></span>
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <input type="number" name="whole_sell_qty[]"
                                                                                class="input-field"
                                                                                placeholder="{{ __('Enter Quantity') }}"
                                                                                min="0">
                                                                        </div>

                                                                        <div class="col-lg-6">
                                                                            <input type="number"
                                                                                name="whole_sell_discount[]"
                                                                                class="input-field"
                                                                                placeholder="{{ __('Enter Discount Percentage') }}"
                                                                                min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @endif
                                                        </div>

                                                        <a href="javascript:;" id="whole-btn" class="add-fild-btn"><i
                                                                class="icofont-plus"></i> {{ __('Add More Field') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">
                                                        {{ __('Product Description') }}*
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-editor">
                                                    <textarea name="details" class="nic-edit">{{ $data->details }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">
                                                        {{ __('Product Buy/Return Policy') }}*
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-editor">
                                                    <textarea name="policy" class="nic-edit">{{ $data->policy }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="checkbox-wrapper">
                                                    <input type="checkbox" name="seo_check" value="1"
                                                        class="checkclick" id="allowProductSEO"
                                                        {{ $data->meta_tag != null || strip_tags($data->meta_description) != null ? 'checked' : '' }}>
                                                    <label for="allowProductSEO">{{ __('Allow Product SEO') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="{{ $data->meta_tag == null && strip_tags($data->meta_description) == null
                                                ? "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    										showbox"
                                                : '' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Meta Tags') }} *</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <ul id="metatags" class="myTags">
                                                        @if (!empty($data->meta_tag))
                                                            @foreach ($data->meta_tag as $element)
                                                                <li>{{ $element }}</li>
                                                            @endforeach
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">
                                                            {{ __('Meta Description') }} *
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="text-editor">
                                                        <textarea name="meta_description" class="input-field" placeholder="{{ __('Details') }}">{{ $data->meta_description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="add-product-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="product-description">
                                    <div class="body-area">

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Feature Image') }} *</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="panel panel-body">
                                                    <div class="span4 cropme text-center" id="landscape"
                                                        style="width: 100%; height: 285px; border: 1px dashed #ddd; background: #f1f1f1;">
                                                        <a href="javascript:;" id="crop-image"
                                                            class="d-inline-block mybtn1">
                                                            <i class="icofont-upload-alt"></i>
                                                            {{ __('Upload Image Here') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="feature_photo" name="photo"
                                            value="{{ $data->photo }}" accept="image/*">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">
                                                        {{ __('Product Gallery Images') }} *
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <a href="javascript" class="set-gallery" data-toggle="modal"
                                                    data-target="#setgallery">
                                                    <input type="hidden" value="{{ $data->id }}">
                                                    <i class="icofont-plus"></i> {{ __('Set Gallery') }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">
                                                        {{ __('Product Current Price') }}*
                                                    </h4>
                                                    <p class="sub-heading">
                                                        ({{ __('In') }} {{ $sign->name }})
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input name="price" type="number" class="input-field"
                                                    placeholder="e.g 20" step="0.1" min="0"
                                                    value="{{ round($data->price * $sign->value, 2) }}" required="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Discount Price') }}*</h4>
                                                    <p class="sub-heading">{{ __('(Optional)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input name="previous_price" step="0.1" type="number"
                                                    class="input-field" placeholder="e.g 20"
                                                    value="{{ round($data->previous_price * $sign->value, 2) }}"
                                                    min="0">
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Youtube Video URL') }}*</h4>
                                                    <p class="sub-heading">{{ __('(Optional)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input name="youtube" type="text" class="input-field"
                                                    placeholder="Enter Youtube Video URL" value="{{ $data->youtube }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="featured-keyword-area">
                                                    <div class="left-area">
                                                        <h4 class="title">{{ __('Feature Tags') }}</h4>
                                                    </div>

                                                    <div class="feature-tag-top-filds" id="feature-section">
                                                        @if (!empty($data->features))

                                                            @foreach ($data->features as $key => $data1)
                                                                <div class="feature-area">
                                                                    <span class="remove feature-remove"><i
                                                                            class="fas fa-times"></i></span>
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <input type="text" name="features[]"
                                                                                class="input-field"
                                                                                placeholder="{{ __('Enter Your Keyword') }}"
                                                                                value="{{ $data->features[$key] }}">
                                                                        </div>

                                                                        <div class="col-lg-6">
                                                                            <div
                                                                                class="input-group colorpicker-component cp">
                                                                                <input type="text" name="colors[]"
                                                                                    value="{{ $data->colors[$key] }}"
                                                                                    class="input-field cp" />
                                                                                <span
                                                                                    class="input-group-addon"><i></i></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="feature-area">
                                                                <span class="remove feature-remove"><i
                                                                        class="fas fa-times"></i></span>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <input type="text" name="features[]"
                                                                            class="input-field"
                                                                            placeholder="{{ __('Enter Your Keyword') }}">
                                                                    </div>

                                                                    <div class="col-lg-6">
                                                                        <div class="input-group colorpicker-component cp">
                                                                            <input type="text" name="colors[]"
                                                                                value="#000000" class="input-field cp" />
                                                                            <span class="input-group-addon"><i></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @endif
                                                    </div>

                                                    <a href="javascript:;" id="feature-btn" class="add-fild-btn"><i
                                                            class="icofont-plus"></i> {{ __('Add More Field') }}</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Tags') }} *</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul id="tags" class="myTags">
                                                    @if (!empty($data->tags))
                                                        @foreach ($data->tags as $element)
                                                            <li>{{ $element }}</li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="row text-center">
                                            <div class="col-6 offset-3">
                                                <button class="addProductSubmit-btn"
                                                    type="submit">{{ __('Save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>

    <div class="modal fade" id="setgallery" tabindex="-1" role="dialog" aria-labelledby="setgallery"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{ __('Image Gallery') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="top-area">
                        <div class="row">
                            <div class="col-sm-6 text-right">
                                <div class="upload-img-btn">
                                    <form method="POST" enctype="multipart/form-data" id="form-gallery">
                                        @csrf
                                        <input type="hidden" id="pid" name="product_id" value="">
                                        <input type="file" name="gallery[]" class="hidden" id="uploadgallery"
                                            accept="image/*" multiple>
                                        <label for="image-upload" id="prod_gallery"><i
                                                class="icofont-upload-alt"></i>{{ __('Upload File') }}</label>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <a href="javascript:;" class="upload-done" data-dismiss="modal"> <i
                                        class="fas fa-check"></i> {{ __('Done') }}</a>
                            </div>
                            <div class="col-sm-12 text-center">(
                                <small>{{ __('You can upload multiple Images.') }}</small>
                                )
                            </div>
                        </div>
                    </div>
                    <div class="gallery-images">
                        <div class="selected-image">
                            <div class="row">


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 360 view modal start -->
    <div class="modal fade" id="view360" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="product360_view" class="product360">
                        <div class="product-image-360">
                            <div class="nav_bar">
                                <a href="#" class="custom_previous">
                                    <i class="ti-angle-left"></i>
                                </a>
                                <a href="#" class="custom_play">
                                    <i class="ti-control-play"></i>
                                </a>
                                <a href="#" class="custom_stop">
                                    <i class="ti-control-pause"></i>
                                </a>
                                <a href="#" class="custom_next">
                                    <i class="ti-angle-right"></i>
                                </a>
                            </div>
                            <ul class="product-images-item" style="display: block;"></ul>
                            <div class="spinner"><span>0%</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 360 view modal end -->
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            function syncApmState(targetId, isOpen) {
                var btnMap = {
                    "media-360-collapse": "#apm-btn-360",
                    "media-hotspot-collapse": "#apm-btn-hotspots",
                    "media-3d-collapse": "#apm-btn-3d",
                    "media-video-collapse": "#apm-btn-media"
                };
                var chevMap = {
                    "media-360-collapse": "#apm-chevron-360",
                    "media-hotspot-collapse": "#apm-chevron-hotspots",
                    "media-3d-collapse": "#apm-chevron-3d",
                    "media-video-collapse": "#apm-chevron-media"
                };
                var btn = $(btnMap[targetId]);
                var chev = $(chevMap[targetId]);
                if (!btn.length) return;
                if (isOpen) {
                    btn.addClass("is-active");
                    chev.addClass("is-rotated");
                } else {
                    btn.removeClass("is-active");
                    chev.removeClass("is-rotated");
                }
            }

            ["media-360-collapse", "media-hotspot-collapse", "media-3d-collapse", "media-video-collapse"].forEach(function(id) {
                var $el = $("#" + id);
                if ($el.length && $el.hasClass("show")) {
                    syncApmState(id, true);
                }
                $el.on("show.bs.collapse", function() {
                    syncApmState(id, true);
                });
                $el.on("hide.bs.collapse", function() {
                    syncApmState(id, false);
                });
            });

            var apmPreviewUrl = null;
            $(document).on("click", ".apm-video-preview", function() {
                var $item = $(this).closest(".apm-media-item");
                var $fileInput = $item.find(".apm-media-file")[0];
                var urlValue = $.trim($item.find(".apm-media-url").val() || "");
                var dataSrc = $(this).data("video-src") || "";
                var src = "";

                if ($fileInput && $fileInput.files && $fileInput.files[0]) {
                    apmPreviewUrl = URL.createObjectURL($fileInput.files[0]);
                    src = apmPreviewUrl;
                } else if (urlValue) {
                    src = urlValue;
                } else if (dataSrc) {
                    src = dataSrc;
                }

                if (!src) {
                    return;
                }

                $("#mediaVideoPreviewPlayer").attr("src", src);
                $("#mediaVideoPreviewModal").modal("show");
            });

            $("#mediaVideoPreviewModal").on("hidden.bs.modal", function() {
                $("#mediaVideoPreviewPlayer").attr("src", "");
                if (apmPreviewUrl) {
                    URL.revokeObjectURL(apmPreviewUrl);
                    apmPreviewUrl = null;
                }
            });
        });

        // Gallery Section Update

var galleryRequestId = 0;

        $(document).on("click", ".set-gallery", function() {
            var pid = $(this).find('input[type=hidden]').val();
            $('#pid').val(pid);
            var $galleryRow = $('.selected-image .row');

$galleryRow.children().detach();
            var requestId = ++galleryRequestId;

$.ajax({
                type: "GET",
                url: "{{ route('admin-gallery-show') }}",
                data: {
                    id: pid
                },
                success: function(data) {

    if (requestId !== galleryRequestId) {
        return;
    }
                    if (data[0] == 0) {
                        $('.selected-image .row').addClass('justify-content-center');
                        $('.selected-image .row')
    .empty()
    .append('<h3>{{ __('No Images Found.') }}</h3>');
                    } else {
                        $('.selected-image .row').removeClass('justify-content-center');
                        $('.selected-image .row h3').remove();
                        var arr = $.map(data[1], function(el) {
                            return el
                        });

                        for (var k = 0; k < arr.length; k++) {
                            $('.selected-image .row').append('<div class="col-sm-6">' +
                                '<div class="img gallery-img">' +
                                '<span class="remove-img"><i class="fas fa-times"></i>' +
                                '<input type="hidden" value="' + arr[k]['id'] + '">' +
                                '</span>' +
                                '<a href="' + '{{ asset('assets/images/galleries') . '/' }}' +
                                arr[
                                    k]['photo'] + '" target="_blank">' +
                                '<img src="' + '{{ asset('assets/images/galleries') . '/' }}' +
                                arr[
                                    k]['photo'] + '" alt="gallery image">' +
                                '</a>' +
                                '</div>' +
                                '</div>');
                        }
                    }

                },
                                error: function(xhr) {

                    mlog('gallery fetch failed', xhr);

                    $('.selected-image .row')
                        .addClass('justify-content-center')
                        .html('<h3>{{ __('Failed to load gallery.') }}</h3>');

                    $.notify(
                        '{{ __('Unable to load gallery.') }}',
                        'danger'
                    );

                }
            });
        });


        $(document).on('click', '.remove-img', function() {
            var id = $(this).find('input[type=hidden]').val();
            $(document).on('click', '.remove-img', function() {

    var $card = $(this).parent().parent();
    var id = $(this).find('input[type=hidden]').val();

    $.ajax({
        type: "GET",
        timeout: 15000,
        url: "{{ route('admin-gallery-delete') }}",
        data: {
            id: id
        },

        success: function() {
            $card.remove();
        },

        error: function(xhr) {

            mlog('gallery delete failed', xhr);

            $.notify(
                '{{ __('Failed to delete image.') }}',
                'danger'
            );

        }
    });

});
            $.ajax({
                type: "GET",
                url: "{{ route('admin-gallery-delete') }}",
                data: {
                    id: id
                }
            });
        });

        $(document).on('click', '#prod_gallery', function() {
            $('#uploadgallery').click();
        });


        $("#uploadgallery").change(function() {
            $("#form-gallery").submit();
        });

        $('#form-gallery')
    .off('submit.apmGalleryUpload')
    .on('submit.apmGalleryUpload', function() {
            $.ajax({
                url: "{{ route('admin-gallery-store') }}",
                method: "POST",
                timeout: 30000,
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data != 0) {
                        $('.selected-image .row').removeClass('justify-content-center');
                        $('.selected-image .row h3').remove();
                        var arr = $.map(data, function(el) {
                            return el
                        });
                        for (var k = 0; k < arr.length; k++) {
                            $('.selected-image .row').append('<div class="col-sm-6">' +
                                '<div class="img gallery-img">' +
                                '<span class="remove-img"><i class="fas fa-times"></i>' +
                                '<input type="hidden" value="' + arr[k]['id'] + '">' +
                                '</span>' +
                                '<a href="' + '{{ asset('assets/images/galleries') . '/' }}' +
                                arr[
                                    k]['photo'] + '" target="_blank">' +
                                '<img src="' + '{{ asset('assets/images/galleries') . '/' }}' +
                                arr[
                                    k]['photo'] + '" alt="gallery image">' +
                                '</a>' +
                                '</div>' +
                                '</div>');
                        }
                    }

                },

                error: function(xhr) {

    $.notify('{{ __('Gallery upload failed.') }}', 'danger');

    mlog('gallery upload failed', xhr);

},
complete: function() {
    $('#uploadgallery').val('');
}

            });
            return false;
        });


        $('.cp').colorpicker();

        // Gallery Section Update Ends
    </script>

    <script src="{{ asset('assets/admin/js/jquery.Jcrop.js') }}"></script>

    <script src="{{ asset('assets/admin/js/jquery.SimpleCropper.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select2.js') }}"></script>
    <script type="text/javascript">
        var str = '';
        var img_array = [];
        var len_count = 0;
        var pro_view;
        var pending360Init = false;
        var mediaAdvancedLoaded = false;
        var last360Frame = 1;

        // Debug logger (enable with ?media_debug=1)
        var MEDIA_DEBUG = (function() {
            try {
                return /(?:\?|&)media_debug=1(?:&|$)/.test(window.location.search || '');
            } catch (e) {
                return false;
            }
        })();
        function mlog() {
            if (!MEDIA_DEBUG || !window.console || !console.log) return;
            try {
                var args = Array.prototype.slice.call(arguments);
                args.unshift('[adv-media]');
                console.log.apply(console, args);
            } catch (e) {}
        }

        function loadScriptOnce(id, src, attrs) {
            return new Promise(function(resolve, reject) {
var existing = document.getElementById(id);

if (existing) {

    if (existing.dataset.loaded === 'true') {
        mlog('script already loaded:', id);
        resolve();
        return;
    }

    // If script already exists but browser completed loading,
    // mark it loaded and resolve safely.
    if (
        existing.readyState === 'complete' ||
        existing.readyState === 'loaded'
    ) {
        existing.dataset.loaded = 'true';
        resolve();
        return;
    }

    var settled = false;

    function cleanup() {
        existing.removeEventListener('load', handleLoad);
        existing.removeEventListener('error', handleError);
    }

    function handleLoad() {

        if (settled) return;
        settled = true;

        existing.dataset.loaded = 'true';

        cleanup();

        resolve();
    }

    function handleError(e) {

        if (settled) return;
        settled = true;

        cleanup();

        reject(e);
    }

    existing.addEventListener('load', handleLoad);
    existing.addEventListener('error', handleError);

    return;
}
                mlog('loading script:', id, src);
                var script = document.createElement('script');
                script.id = id;
                script.src = src;
                if (attrs) {
                    Object.keys(attrs).forEach(function(key) {
                        script.setAttribute(key, attrs[key]);
                    });
                }
var settled = false;

script.onload = function() {

    if (settled) return;
    settled = true;

    script.dataset.loaded = 'true';

    mlog('script loaded:', id);

    resolve();
};

script.onerror = function(e) {

    if (settled) return;
    settled = true;

    mlog('script failed:', id, src, e);

    reject(e);
};
                script.onerror = function(e) {
                    mlog('script failed:', id, src, e);
                    reject(e);
                };
                document.head.appendChild(script);
            });
        }

        function ensure360Script() {
            return loadScriptOnce('admin-360view-js', "{{ asset('assets/admin/js/360view.js') }}");
        }

        function ensureModelViewerScripts() {
            var moduleScript = loadScriptOnce('admin-model-viewer-module',
                "https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js", {
                    type: 'module'
                });
            var nomoduleScript = loadScriptOnce('admin-model-viewer-nomodule',
                "https://unpkg.com/@google/model-viewer/dist/model-viewer-legacy.js", {
                    nomodule: ''
                });
            return Promise.all([moduleScript, nomoduleScript]);
        }

        function init360Viewer() {
            if (!img_array.length || !$.fn.ThreeSixty) {
                return;
            }

            if (pro_view && typeof pro_view.stop === 'function') {
    pro_view.stop();
}

            $('.product-images-item').html('');
            $('.spinner span').text('0%');

            var initialFrame = last360Frame || 1;
            if (initialFrame < 1) {
                initialFrame = 1;
            }
            if (initialFrame > len_count) {
                initialFrame = len_count;
            }

            pro_view = $('.product-image-360').ThreeSixty({
                totalFrames: len_count,
                endFrame: len_count,
                currentFrame: initialFrame,
                imgList: '.product-images-item',
                progress: '.spinner',
                imgArray: img_array,
                height: null,
                width: null,
                responsive: true,
                navigation: false,
                onDragStart: function() {
                    $('#media_360_hotspot_overlay').addClass('dragging');
                },
                onDragStop: function() {
                    $('#media_360_hotspot_overlay').removeClass('dragging');
                }
            });

$(document)
    .off('click.apm360Prev')
    .on('click.apm360Prev', '.custom_previous', function(e) {
        e.preventDefault();
        if (pro_view) {
            pro_view.previous();
        }
    });

$(document)
    .off('click.apm360Next')
    .on('click.apm360Next', '.custom_next', function(e) {
        e.preventDefault();

        if (pro_view) {
            pro_view.next();
        }
    });

$(document)
    .off('click.apm360Play')
    .on('click.apm360Play', '.custom_play', function(e) {
        e.preventDefault();

        if (pro_view) {
            pro_view.play();
            $('.nav_bar').addClass('play-video');
        }
    });

$(document)
    .off('click.apm360Stop')
    .on('click.apm360Stop', '.custom_stop', function(e) {
        e.preventDefault();

        if (pro_view) {
            pro_view.stop();
            $('.nav_bar').removeClass('play-video');
        }
    });

            $('.product-image-360')
                .off('frameIndexChanged.media360state')
                .on('frameIndexChanged.media360state', function(e, frameIndex) {
                    if (frameIndex) {
                        last360Frame = frameIndex;
                    }
                    if (typeof renderFrameHotspots === 'function') {
                        renderFrameHotspots(frameIndex);
                    }
                });
            if (typeof renderFrameHotspots === 'function') {
                renderFrameHotspots(initialFrame);
            }
        }

        function setMedia360Frames(frames, mode) {
            if (!frames || frames.length === 0) {
                img_array = [];
                len_count = 0;
                $('#media_360_status').text('{{ __('No frames uploaded yet.') }}');
                $('#media_360_preview_btn').addClass('disabled').attr('aria-disabled', 'true');
                $('#media_hotspot_frame_count').text('0');
                update360Warning();
                return;
            }

            str = frames.join(',');
            img_array = str.split(',');
            len_count = img_array.length;
            $('#media_360_status').text(len_count + ' {{ __('frames ready.') }}');
            $('#media_hotspot_frame_count').text(len_count);
            $('#media_360_preview_btn').removeClass('disabled').removeAttr('aria-disabled');
            if (mode === 'replace') {
                last360Frame = 1;
            } else if (last360Frame > len_count) {
                last360Frame = len_count;
            } else if (last360Frame < 1) {
                last360Frame = 1;
            }
            if ($.fn.ThreeSixty) {
                init360Viewer();
            } else {
                pending360Init = true;
            }
            update360Warning();
        }

        var media360ManifestRequestId = 0;

function loadMedia360Manifest(mode, callback) {

    var requestId = ++media360ManifestRequestId;

$.get("{{ route('admin-prod-media-360-manifest', $data->id) }}", function(data) {

    if (requestId !== media360ManifestRequestId) {
        return;
    }

    if (data && Array.isArray(data.frames)) {

        mlog('manifest loaded:', {
            mode: mode,
            frames: (data.frames ? data.frames.length : 0)
        });

        setMedia360Frames(data.frames, mode);

        if (typeof callback === 'function') {
            callback();
        }
    }

}).fail(function(xhr) {

    img_array = [];
    len_count = 0;

    $('#media_360_preview_btn')
        .addClass('disabled')
        .attr('aria-disabled', 'true');

    $('#media_hotspot_frame_count').text('0');

    mlog('manifest load failed', xhr);

    $.notify('{{ __('Unable to load 360 manifest.') }}', 'danger');

});
}

        function update360Warning() {
            if ($('#media_360_enabled').is(':checked') && len_count === 0) {
                $('#media_360_status').text('{{ __('Warning: 360° frames missing.') }}');
            }
        }

        function hasFrame360Hotspots() {
            return $('.media-hotspot-target').filter(function() {
                return ($(this).val() || '') === 'frame360';
            }).length > 0;
        }

        function confirmReplaceIfNeeded() {
            if ($('#media_360_mode').val() !== 'replace') {
                return true;
            }
            if (!hasFrame360Hotspots()) {
                return true;
            }
            return confirm('{{ __('Replace will remove frames used by existing 360 hotspots. Continue?') }}');
        }

        $(document).ready(function() {
            mlog('page ready (360 block)', {
                v360_status: ($('#media_360_status').text() || '').trim(),
                v360_enabled: $('#media_360_enabled').is(':checked'),
                preview_disabled: $('#media_360_preview_btn').hasClass('disabled') || $('#media_360_preview_btn').attr('aria-disabled')
            });
            $('#media_360_mode')
    .off('change.apm360Mode')
    .on('change.apm360Mode', function() {
                var mode = $(this).val() || 'append';
                if (mode === 'replace') {
                    if (!confirmReplaceIfNeeded()) {
                        $(this).val('append');
                        mode = 'append';
                    }
                    $('#media_360_mode_warning').show();
                } else {
                    $('#media_360_mode_warning').hide();
                }
            }).trigger('change');

$('#media-advanced-collapse').on('shown.bs.collapse', function() {

    if (mediaAdvancedLoaded) {
        return;
    }

    mediaAdvancedLoaded = true;

    ensure360Script()
        .then(function() {

            loadMedia360Manifest('append');

            if (pending360Init) {
                init360Viewer();
                pending360Init = false;
            }

            return ensureModelViewerScripts();

        })
        .catch(function(err) {

            mediaAdvancedLoaded = false;

            mlog('360 init failed', err);

            $.notify('{{ __('Failed to initialize advanced media viewer.') }}', 'danger');

        });

});

            $('#media_360_upload_btn')
    .off('click.apm360Upload')
    .on('click.apm360Upload', function(e) {
                e.preventDefault();

                var files = $('#media_360_frames')[0].files;
                if (!files || files.length === 0) {
                    $.notify('{{ __('Please select frames to upload.') }}', 'warning');
                    return;
                }

                var fd = new FormData();
                for (var i = 0; i < files.length; i++) {
                    fd.append('media_360_frames[]', files[i]);
                }
                fd.append('media_360_mode', $('#media_360_mode').val() || 'append');

                if (!confirmReplaceIfNeeded()) {
                    return;
                }

                var uploadMode = $('#media_360_mode').val() || 'append';
                var $uploadBtn = $('#media_360_upload_btn');
                $uploadBtn.addClass('disabled').text('{{ __('Uploading…') }}');
                $.ajax({
                    method: "POST",
                    timeout: 30000,
                    url: "{{ route('admin-prod-media-360-upload', $data->id) }}",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        loadMedia360Manifest(uploadMode, function() {
                            $('#view360').modal('show');
                        });
                        if ((data.errors)) {
                            for (var error in data.errors) {
                                $.notify(data.errors[error], "danger");
                            }
                        }
                    },
                                        error: function(xhr) {

                        mlog('360 upload failed', xhr);

                        $.notify(
                            '{{ __('360 upload failed.') }}',
                            'danger'
                        );

                    },
                    complete: function() {
                        $uploadBtn.removeClass('disabled').html('<i class="icofont-upload-alt"></i> {{ __('Upload 360 Frames') }}');
                    }
                });
            });

            $('#media_360_delete_btn')
    .off('click.apm360Delete')
    .on('click.apm360Delete', function(e) {
                e.preventDefault();
                $.ajax({
                    method: "POST",
                    timeout: 30000,
                    url: "{{ route('admin-prod-media-360-delete', $data->id) }}",
                    success: function(data) {
                        loadMedia360Manifest('replace');
                        $('.product-images-item').html('');
                        $.notify('{{ __('360° frames deleted.') }}', 'success');
                    },
                                        error: function(xhr) {

                        mlog('360 delete failed', xhr);

                        $.notify(
                            '{{ __('Failed to delete 360 frames.') }}',
                            'danger'
                        );

                    },
                });
            });

            $('#media_360_preview_btn')
    .off('click.apm360Preview')
    .on('click.apm360Preview', function(e) {
                if ($(this).hasClass('disabled')) {
                    e.preventDefault();
                }
            });

            $('#media_360_enabled')
    .off('change.apm360Enabled')
    .on('change.apm360Enabled', function() {
                update360Warning();
            });

            $('#view360').on('shown.bs.modal', function() {
                $(window).trigger('resize');
            });
            $('#view360').on('hidden.bs.modal', function() {

    hotspotDrag.active = false;
    hotspotDrag.dot = null;
    hotspotDrag.lastEvent = null;

    if (hotspotDrag.raf) {
        cancelAnimationFrame(hotspotDrag.raf);
        hotspotDrag.raf = null;
    }

    $('#media_360_hotspot_overlay').removeClass('dragging');

});
        });
    </script>

    <script type="text/javascript">
        (function($) {
            "use strict";

            var hotspotIndex = 0;

            function resetHotspots() {
                mlog('resetHotspots() called', {
                    base: $('#media_hotspot_base').val(),
                    dots_before: $('.media-hotspot-dot').length,
                    items_before: $('.media-hotspot-item').length
                });
                $('#media_hotspot_items .media-hotspot-group-items').html('');
                $('#media_hotspot_block .lookbook-dot').remove();
                hotspotIndex = 0;
                mlog('resetHotspots() done', {
                    dots_after: $('.media-hotspot-dot').length,
                    items_after: $('.media-hotspot-item').length
                });
            }

            function getHotspotGroup(target) {
                if (target === 'frame360') {
                    return $('#media_hotspot_group_frame');
                }
                if (target === 'model3d') {
                    return $('#media_hotspot_group_model');
                }
                return $('#media_hotspot_group_image');
            }

            function updateHotspotStatus(item) {
                var target = item.find('.media-hotspot-target').val() || 'image';
                var frame = item.find('.media-hotspot-frame').val() || '';
                var label = '{{ __('Image hotspot') }}';
                if (target === 'frame360') {
                    label = '{{ __('360 Frame') }}' + ' #' + (frame || '1') + ' {{ __('hotspot') }}';
                } else if (target === 'model3d') {
                    label = '{{ __('3D hotspot') }}';
                }
                item.find('.media-hotspot-status').text(label);
            }

            function moveHotspotGroup(item) {
                var target = item.find('.media-hotspot-target').val() || 'image';
                var group = getHotspotGroup(target);
                // IMPORTANT: Don't re-append the same node on every keystroke
                // (it causes focus/cursor to reset while typing).
                if (group.length && item.parent().length && item.parent().get(0) !== group.get(0)) {
                    group.append(item);
                }
                updateHotspotStatus(item);
            }

            function refreshHotspotGroups() {
                $('.media-hotspot-item').each(function() {
                    moveHotspotGroup($(this));
                });
            }

        function updateHotspotContent(key) {
            var item = $('.media-hotspot-item[data-key="' + key + '"]');
            var label = item.find('.media-hotspot-label').val() || '{{ __('Hotspot') }}';
            var desc = item.find('.media-hotspot-desc').val() || '';
            var type = item.find('.media-hotspot-type').val() || 'text';
            var imgInput = item.find('.media-hotspot-image')[0];
            var previewImg = item.find('.media-hotspot-image-wrap img').attr('src') || '';
            var target = item.find('.media-hotspot-target').val() || 'image';
            var frame = item.find('.media-hotspot-frame').val() || '';
            var dot = $('.media-hotspot-dot[data-key="' + key + '"]');
            var dotImage = dot.find('.dot-image');
            var dotTitle = dot.find('.dot-info .title');
            var dotDesc = dot.find('.dot-info .desc');

            dotTitle.text(label);
            dotDesc.text(desc);

            var imgSrc = '';
            if (imgInput && imgInput.files && imgInput.files[0]) {
if (item.data('blobUrl')) {

    URL.revokeObjectURL(item.data('blobUrl'));

    item.removeData('blobUrl');
}

imgSrc = URL.createObjectURL(imgInput.files[0]);

item.data('blobUrl', imgSrc);
            } else if (previewImg) {
                imgSrc = previewImg;
            }

            if (imgSrc) {
                dotImage.attr('src', imgSrc).show();
            } else {
                dotImage.hide().attr('src', '');
            }

            if (type === 'text') {
                dotTitle.show();
                dotDesc.show();
            } else if (type === 'image') {
                if (imgSrc) {
                    dotTitle.hide();
                    dotDesc.hide();
                } else {
                    dotTitle.show();
                    dotDesc.show();
                }
            } else {
                dotTitle.show();
                dotDesc.show();
            }

            dot.attr('data-target', target);
            dot.attr('data-frame', frame);
            updateHotspotVisibility();
            if (target === 'model3d') {
                renderModel3dHotspots();
            }
            moveHotspotGroup(item);
        }

            function refreshHotspotNumbers() {
                $('.media-hotspot-dot').each(function(index) {
                    $(this).find('> span').text(index + 1);
                });
            }

            function setHotspotTargetFields(item, target) {
                var wrap3d = item.find('.media-hotspot-3d-wrap');
                if (target === 'model3d') {
                    wrap3d.show();
                } else {
                    wrap3d.hide();
                }
            }

        function updateHotspotVisibility() {
            var targetMode = $('#media_hotspot_target_mode').val() || 'image';
            var currentFrame = $('#media_hotspot_frame_number').val() || '';
            $('.media-hotspot-dot').each(function() {
                var dotTarget = $(this).attr('data-target') || 'image';
                var dotFrame = $(this).attr('data-frame') || '';
                var key = $(this).attr('data-key');
                var item = $('.media-hotspot-item[data-key="' + key + '"]');
                if (item.length && item.hasClass('is-hidden')) {
                    $(this).hide();
                    return;
                }
                if (targetMode === 'frame360') {
                    if (dotTarget === 'frame360' && dotFrame === currentFrame) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else if (targetMode === 'model3d') {
                    $(this).hide();
                } else {
                    if (dotTarget === 'image') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            });
        }

        function clampPercent(value) {
            return Math.max(0, Math.min(100, value));
        }

        function waitForImageReady(img, callback) {
    if (!img) return;

    if (img.complete && img.naturalWidth > 0) {
        callback();
        return;
    }

    $(img)
        .off('.apmImageReady')
        .one('load.apmImageReady', function() {
            callback();
        })
        .one('error.apmImageReady', function() {
            console.warn('APM image failed to load');
        });
}

        function getPointer(e) {
            var evt = e.originalEvent || e;
            if (evt.touches && evt.touches.length) {
                return evt.touches[0];
            }
            if (evt.changedTouches && evt.changedTouches.length) {
                return evt.changedTouches[0];
            }
            return evt;
        }

        function canDragDot(dot) {
            var targetMode = $('#media_hotspot_target_mode').val() || 'image';
            var dotTarget = dot.attr('data-target') || 'image';
            var dotFrame = dot.attr('data-frame') || '';
            var currentFrame = $('#media_hotspot_frame_number').val() || '';

            if (targetMode === 'image') {
                return dotTarget === 'image';
            }
            if (targetMode === 'frame360') {
                return dotTarget === 'frame360' && String(dotFrame) === String(currentFrame);
            }
            return false;
        }

        function updateDotPosition(dot, xPercent, yPercent) {
            dot.css({
                left: xPercent.toFixed(2) + '%',
                top: yPercent.toFixed(2) + '%'
            });

            var key = dot.data('key');
            var item = $('.media-hotspot-item[data-key="' + key + '"]');
            item.find('input[name="media_hotspot_x[]"]').val(xPercent.toFixed(2));
            item.find('input[name="media_hotspot_y[]"]').val(yPercent.toFixed(2));
        }

        var hotspotDrag = {
            active: false,
            dot: null,
            raf: null,
            lastEvent: null
        };

        window.addEventListener('beforeunload', function() {

    $('.media-hotspot-item').each(function() {

        var blobUrl = $(this).data('blobUrl');

        if (blobUrl) {
            URL.revokeObjectURL(blobUrl);
        }

    });

});

        function applyDragPosition(e) {
            if (!hotspotDrag.active || !hotspotDrag.dot) {
                return;
            }
            var img = $('#media_hotspot_image')[0];
            if (!img) {
                return;
            }
if (!img.complete || !img.naturalWidth) {
    return;
}

var rect = img.getBoundingClientRect();

if (!rect.width || !rect.height) {
    return;
}

            var pointer = getPointer(e);
            var x = ((pointer.clientX - rect.left) / rect.width) * 100;
            var y = ((pointer.clientY - rect.top) / rect.height) * 100;

            x = clampPercent(x);
            y = clampPercent(y);
            updateDotPosition(hotspotDrag.dot, x, y);
        }

        function scheduleDrag(e) {
            hotspotDrag.lastEvent = e;
            if (hotspotDrag.raf) {
                return;
            }
            hotspotDrag.raf = requestAnimationFrame(function() {
                hotspotDrag.raf = null;
                if (hotspotDrag.lastEvent) {
                    applyDragPosition(hotspotDrag.lastEvent);
                }
            });
        }

        function renderFrameHotspots(frameIndex) {
            var overlay = $('#media_360_hotspot_overlay');
            overlay.empty();
            if (!frameIndex) {
                return;
            }

                $('.media-hotspot-item').each(function() {
                var item = $(this);
                    if (item.hasClass('is-hidden')) {
                        return;
                    }
                var target = item.find('.media-hotspot-target').val() || 'image';
                var frame = item.find('.media-hotspot-frame').val() || '';
                if (target !== 'frame360' || String(frame) !== String(frameIndex)) {
                    return;
                }

                var x = item.find('input[name="media_hotspot_x[]"]').val();
                var y = item.find('input[name="media_hotspot_y[]"]').val();
                var type = item.find('.media-hotspot-type').val() || 'text';
                var label = item.find('.media-hotspot-label').val() || '{{ __('Hotspot') }}';
                var desc = item.find('.media-hotspot-desc').val() || '';
                var imgSrc = item.find('.media-hotspot-thumb').attr('src') || '';

                var dot = $('<div/>', {
                    'class': 'lookbook-dot',
                    css: {
                        left: x + '%',
                        top: y + '%'
                    }
                });

                dot.append('<span>•</span>');
                var showbox = $('<div class="dot-showbox"></div>');
                var imageTag = $('<img class="dot-image img-fluid" style="display:none;" alt="">');
                if (imgSrc) {
                    imageTag.attr('src', imgSrc).show();
                }
                showbox.append(imageTag);
                var info = $('<div class="dot-info"></div>');
                info.append('<h5 class="title">' + label + '</h5>');
                info.append('<h6 class="desc">' + desc + '</h6>');
                showbox.append(info);
                dot.append($('<a href="javascript:void(0)"></a>').append(showbox));

                if (type === 'image' && imgSrc) {
                    info.find('.title, .desc').hide();
                } else if (type === 'image' && !imgSrc) {
                    info.find('.title, .desc').show();
                }

                overlay.append(dot);
            });
        }

        function clearModel3dHotspots() {
            var viewer = document.getElementById('media_3d_viewer');
            if (!viewer) {
                return;
            }
            $(viewer).find('.media-hotspot-3d').remove();
        }

        function renderModel3dHotspots() {
            var viewer = document.getElementById('media_3d_viewer');
            if (!viewer) {
                return;
            }
            clearModel3dHotspots();
            if ($('#media_hotspot_target_mode').val() !== 'model3d') {
                return;
            }

            $('.media-hotspot-item').each(function() {
                var item = $(this);
                if (item.hasClass('is-hidden')) {
                    return;
                }
                var target = item.find('.media-hotspot-target').val() || 'image';
                if (target !== 'model3d') {
                    return;
                }
                var x3d = item.find('.media-hotspot-x3d').val() || '0';
                var y3d = item.find('.media-hotspot-y3d').val() || '0';
                var z3d = item.find('.media-hotspot-z3d').val() || '0';
                var orbit = item.find('.media-hotspot-orbit').val() || '';
                var type = item.find('.media-hotspot-type').val() || 'text';
                var label = item.find('.media-hotspot-label').val() || '{{ __('Hotspot') }}';
                var desc = item.find('.media-hotspot-desc').val() || '';
                var imgSrc = item.find('.media-hotspot-thumb').attr('src') || '';
                var key = item.data('key') || ('hs_' + Date.now());

                var button = document.createElement('button');
                button.setAttribute('slot', 'hotspot-' + key);
                button.setAttribute('data-position', x3d + ' ' + y3d + ' ' + z3d);
                if (orbit) {
                    button.setAttribute('data-orbit', orbit);
                }
                button.className = 'lookbook-dot media-hotspot-3d';
                button.innerHTML = '<span>•</span>' +
                    '<div class="dot-showbox">' +
                    '<img class="dot-image img-fluid" style="display:none;" alt="">' +
                    '<div class="dot-info">' +
                    '<h5 class="title">' + label + '</h5>' +
                    '<h6 class="desc">' + desc + '</h6>' +
                    '</div>' +
                    '</div>';

                if (imgSrc) {
                    var img = button.querySelector('.dot-image');
                    img.src = imgSrc;
                    img.style.display = '';
                }

                if (type === 'image' && imgSrc) {
                    var info = button.querySelector('.dot-info');
                    if (info) {
                        info.style.display = 'none';
                    }
                }

                viewer.appendChild(button);
            });
        }

        function setHotspotType(item, type) {
            var imgWrap = item.find('.media-hotspot-image-wrap');
            var textWraps = item.find('.media-hotspot-text-wrap');
            if (type === 'text') {
                imgWrap.hide();
                textWraps.show();
            } else if (type === 'image') {
                imgWrap.show();
                textWraps.hide();
            } else {
                imgWrap.show();
                textWraps.show();
            }
        }

        function showHotspotError(item, message) {
            var error = item.find('.media-hotspot-error');
            error.text(message).show();
        }

        function clearHotspotError(item) {
            item.find('.media-hotspot-error').hide().text('');
        }

        function handleHotspotImageChange(input) {
            var item = $(input).closest('.media-hotspot-item');
            clearHotspotError(item);
            var file = input.files && input.files[0] ? input.files[0] : null;
            if (!file) {
                return;
            }

            var ext = (file.name.split('.').pop() || '').toLowerCase();
            if ($.inArray(ext, ['jpg', 'jpeg', 'png', 'webp']) === -1) {
                showHotspotError(item, '{{ __('Invalid image type. Use jpg, png, or webp.') }}');
                input.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showHotspotError(item, '{{ __('Image too large. Max 2MB.') }}');
                input.value = '';
                return;
            }

            var thumb = item.find('.media-hotspot-thumb');
            thumb.attr('src', URL.createObjectURL(file)).show();
            updateHotspotContent(item.data('key'));
        }

        $('#media_hotspot_base').on('change', function() {
                mlog('media_hotspot_base change', {
                    value: $(this).val(),
                    selected_src: $(this).find(':selected').data('src') || null
                });
                var src = $(this).find(':selected').data('src');
                if (src) {
                    $('#media_hotspot_image').attr('src', src);
                } else {
                    $('#media_hotspot_image').attr('src', '{{ asset('assets/images/noimage.png') }}');
                }
                resetHotspots();
            });

        $('#media_hotspot_target_mode').on('change', function() {
            var mode = $(this).val();
            if (mode === 'frame360') {
                $('#media_hotspot_frame_row').show();
                $('#media_hotspot_base_row').hide();
                var frame = $('#media_hotspot_frame_number').val();
                if (!frame || frame < 1) {
                    $('#media_hotspot_frame_number').val(1);
                }
                updateFramePreview();
            } else if (mode === 'model3d') {
                $('#media_hotspot_frame_row').hide();
                $('#media_hotspot_base_row').hide();
                renderModel3dHotspots();
            } else {
                $('#media_hotspot_frame_row').hide();
                $('#media_hotspot_base_row').show();
                clearModel3dHotspots();
            }

            $('.media-hotspot-item').each(function() {
                var item = $(this);
                var target = item.find('.media-hotspot-target').val() || 'image';
                setHotspotTargetFields(item, target);
            });

            updateHotspotVisibility();
        });

        $('#media_3d_viewer').on('load', function() {
            renderModel3dHotspots();
        });

refreshHotspotGroups();
function refreshHotspotRuntime() {

    refreshHotspotGroups();
    updateHotspotVisibility();

    var activeFrame = $('#media_hotspot_frame_number').val();

    if (typeof renderFrameHotspots === 'function') {
        renderFrameHotspots(activeFrame);
    }

}

hotspotIndex = $('.media-hotspot-item').length;

var initialHotspotSrc = $('#media_hotspot_base')
    .find(':selected')
    .data('src');

if (initialHotspotSrc) {

    $('#media_hotspot_image')
        .attr('src', initialHotspotSrc);

}

updateHotspotVisibility();

        $('#media_hotspot_frame_number').on('change', function() {
            updateFramePreview();
            updateHotspotVisibility();
        });

        function updateFramePreview() {
            var frameIndex = parseInt($('#media_hotspot_frame_number').val(), 10);
            if (!frameIndex || frameIndex < 1) {
                return;
            }
            if (!img_array || img_array.length === 0) {
                $('#media_hotspot_image').attr('src', '{{ asset('assets/images/noimage.png') }}');
                return;
            }
            var idx = frameIndex - 1;
            if (idx >= img_array.length) {
                idx = img_array.length - 1;
                $('#media_hotspot_frame_number').val(idx + 1);
            }
            $('#media_hotspot_image').attr('src', $.trim(img_array[idx]));
        }

$(document)
    .off('click.apmHotspotCreate', '#media_hotspot_image')
    .on('click.apmHotspotCreate', '#media_hotspot_image', function(e) {

        if (!$('#media_hotspot_base').val()) {
            $.notify('{{ __('Please select a base image first.') }}', 'warning');
            return;
        }

        var img = this;

        waitForImageReady(img, function() {

            var rect = img.getBoundingClientRect();

            if (!rect.width || !rect.height) {
                console.warn('APM hotspot image dimensions unavailable');
                return;
            }

            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;

            x = Math.max(0, Math.min(100, x));
            y = Math.max(0, Math.min(100, y));

            var key = 'hs_' + Date.now() + '_' + hotspotIndex;
            hotspotIndex += 1;

            var dotHtml = '<div class="lookbook-dot media-hotspot-dot" data-key="' + key + '" ' +
                'style="left:' + x.toFixed(2) + '%; top:' + y.toFixed(2) + '%;">' +
                '<span>' + hotspotIndex + '</span>' +
                '<a href="javascript:void(0)">' +
                '<div class="dot-showbox">' +
                '<img class="dot-image img-fluid" style="display:none;" alt="">' +
                '<div class="dot-info">' +
                '<h5 class="title">{{ __('Hotspot') }}</h5>' +
                '<h6 class="desc"></h6>' +
                '</div>' +
                '</div>' +
                '</a>' +
                '</div>';

            $('#media_hotspot_block').append(dotHtml);

            var targetMode = $('#media_hotspot_target_mode').val() || 'image';
            var frameValue = $('#media_hotspot_frame_number').val() || '';

            var itemHtml = '<div class="media-hotspot-item row" data-key="' + key + '">' +
                '<div class="col-md-3">' +
                '<select class="input-field media-hotspot-type" name="media_hotspot_type[]">' +
                '<option value="text" selected>{{ __('Text') }}</option>' +
                '<option value="image">{{ __('Image') }}</option>' +
                '<option value="image_text">{{ __('Image + Text') }}</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-text-wrap">' +
                '<input type="text" class="input-field media-hotspot-label" name="media_hotspot_label[]" placeholder="{{ __('Label') }}">' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-text-wrap">' +
                '<input type="text" class="input-field media-hotspot-desc" name="media_hotspot_description[]" placeholder="{{ __('Description') }}">' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-actions">' +
                '<a href="javascript:;" class="mybtn1 media-hotspot-toggle" title="{{ __('Toggle visibility') }}"><i class="fas fa-eye"></i></a>' +
                '<a href="javascript:;" class="mybtn1 media-hotspot-jump" title="{{ __('Jump to target') }}"><i class="fas fa-crosshairs"></i></a>' +
                '<a href="javascript:;" class="mybtn1 media-hotspot-remove" title="{{ __('Remove hotspot') }}"><i class="fas fa-times"></i></a>' +
                '</div>' +
                '<div class="col-md-6 media-hotspot-image-wrap" style="display:none;">' +
                '<input type="file" class="input-field media-hotspot-image" name="media_hotspot_image[]" accept=".jpg,.jpeg,.png,.webp" style="display:none;">' +
                '<div class="media-hotspot-thumb-wrap">' +
                '<img class="img-fluid media-hotspot-thumb" style="max-width:80px; margin-top:6px; display:none;" alt="">' +
                '</div>' +
                '<small class="text-muted">{{ __('Max 2MB') }}</small>' +
                '<div class="alert alert-danger media-hotspot-error" style="display:none; margin-top:6px;"></div>' +
                '<div style="margin-top:6px;">' +
                '<a href="javascript:;" class="mybtn1 media-hotspot-change-image"><i class="fas fa-image"></i> {{ __('Change image') }}</a>' +
                '<a href="javascript:;" class="mybtn1 media-hotspot-remove-image"><i class="fas fa-times"></i> {{ __('Remove image') }}</a>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">' +
                '<input type="text" class="input-field media-hotspot-3d-input media-hotspot-x3d" name="media_hotspot_x3d[]" placeholder="x">' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">' +
                '<input type="text" class="input-field media-hotspot-3d-input media-hotspot-y3d" name="media_hotspot_y3d[]" placeholder="y">' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">' +
                '<input type="text" class="input-field media-hotspot-3d-input media-hotspot-z3d" name="media_hotspot_z3d[]" placeholder="z">' +
                '</div>' +
                '<div class="col-md-3 media-hotspot-3d-wrap" style="display:none;">' +
                '<input type="text" class="input-field media-hotspot-3d-input media-hotspot-orbit" name="media_hotspot_orbit[]" placeholder="{{ __('camera_orbit') }}">' +
                '</div>' +
                '<input type="hidden" name="media_hotspot_x[]" value="' + x.toFixed(2) + '">' +
                '<input type="hidden" name="media_hotspot_y[]" value="' + y.toFixed(2) + '">' +
                '<input type="hidden" class="media-hotspot-target" name="media_hotspot_target[]" value="' + targetMode + '">' +
                '<input type="hidden" class="media-hotspot-frame" name="media_hotspot_frame[]" value="' + frameValue + '">' +
                '<div class="col-12"><small class="text-muted media-hotspot-status"></small></div>' +
                '</div>';

            var group = getHotspotGroup(targetMode);

            group.append(itemHtml);

            var newItem = $('.media-hotspot-item[data-key="' + key + '"]');

            setHotspotTargetFields(newItem, targetMode);

            updateHotspotContent(key);

            renderModel3dHotspots();

        });

    });


            $(document).on('input', '.media-hotspot-label, .media-hotspot-desc', function() {
                var key = $(this).closest('.media-hotspot-item').data('key');
                updateHotspotContent(key);
            });

            $(document).on('change', '.media-hotspot-type, .media-hotspot-image, .media-hotspot-3d-input', function() {
                var item = $(this).closest('.media-hotspot-item');
                var key = item.data('key');
                if ($(this).hasClass('media-hotspot-type')) {
                    setHotspotType(item, $(this).val());
                }
                if ($(this).hasClass('media-hotspot-image')) {
                    handleHotspotImageChange(this);
                }
                if ($(this).hasClass('media-hotspot-3d-input')) {
                    renderModel3dHotspots();
                }
                updateHotspotContent(key);
            });

            $(document).on('click', '.media-hotspot-change-image', function(e) {
                e.preventDefault();
                var item = $(this).closest('.media-hotspot-item');
                item.find('.media-hotspot-image').trigger('click');
            });

            $(document).on('click', '.media-hotspot-remove-image', function(e) {
                e.preventDefault();
                var item = $(this).closest('.media-hotspot-item');
                item.find('.media-hotspot-image').val('');
                item.find('.media-hotspot-thumb').hide().attr('src', '');
                item.find('.media-hotspot-type').val('text');
                setHotspotType(item, 'text');
                clearHotspotError(item);
                updateHotspotContent(item.data('key'));
            });

            $(document).on('click', '.media-hotspot-3d', function(e) {
                e.preventDefault();
                var showbox = $(this).find('.dot-showbox');
                var isVisible = showbox.css('visibility') === 'visible';
                showbox.css('visibility', isVisible ? 'hidden' : 'visible');
                var orbit = $(this).attr('data-orbit');
                if (orbit) {
                    var viewer = document.getElementById('media_3d_viewer');
                    if (viewer) {
                        viewer.cameraOrbit = orbit;
                    }
                }
            });

            $(document).on('click', '.media-hotspot-toggle', function(e) {
                e.preventDefault();
                var item = $(this).closest('.media-hotspot-item');
                var key = item.data('key');
                var dot = $('.media-hotspot-dot[data-key="' + key + '"]');
                var isHidden = item.hasClass('is-hidden');
                if (isHidden) {
                    item.removeClass('is-hidden');
                    $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                    updateHotspotVisibility();
                    renderFrameHotspots($('#media_hotspot_frame_number').val());
                    renderModel3dHotspots();
                } else {
                    item.addClass('is-hidden');
                    $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                    dot.hide();
                    renderFrameHotspots($('#media_hotspot_frame_number').val());
                    renderModel3dHotspots();
                }
            });

            $(document).on('click', '.media-hotspot-jump', function(e) {
                e.preventDefault();
                var item = $(this).closest('.media-hotspot-item');
                var target = item.find('.media-hotspot-target').val() || 'image';
                var frame = item.find('.media-hotspot-frame').val() || 1;
                if (target === 'frame360') {
                    $('#media_hotspot_target_mode').val('frame360').trigger('change');
                    $('#media_hotspot_frame_number').val(frame).trigger('change');
                } else if (target === 'model3d') {
                    $('#media_hotspot_target_mode').val('model3d').trigger('change');
                } else {
                    $('#media_hotspot_target_mode').val('image').trigger('change');
                }
                var anchor = target === 'model3d' ? $('#media_3d_viewer') : $('#media_hotspot_preview');
                if (anchor.length) {
                    $('html, body').animate({ scrollTop: anchor.offset().top - 120 }, 300);
                }
            });

            $(document).on('mousedown touchstart', '#media_hotspot_block .media-hotspot-dot', function(e) {
                if (e.type === 'mousedown' && e.which !== 1) {
                    return;
                }
                var dot = $(this);
                if (!canDragDot(dot)) {
                    return;
                }
                e.preventDefault();
                hotspotDrag.active = true;
                hotspotDrag.dot = dot;
                scheduleDrag(e);
            });

            $(document).on('mousemove touchmove', function(e) {
                if (!hotspotDrag.active) {
                    return;
                }
                scheduleDrag(e);
                if (e.type === 'touchmove') {
                    e.preventDefault();
                }
            });

            $(document).on('mouseup touchend touchcancel', function() {
                hotspotDrag.active = false;
                hotspotDrag.dot = null;
                hotspotDrag.lastEvent = null;
            });

            $(document).on('click', '.media-hotspot-remove', function() {
                var item = $(this).closest('.media-hotspot-item');
                var key = item.data('key');
                $('.media-hotspot-dot[data-key="' + key + '"]').remove();
                var blobUrl = item.data('blobUrl');

if (blobUrl) {
    URL.revokeObjectURL(blobUrl);
}
                item.remove();
                refreshHotspotNumbers();
            });

            $(window).on('beforeunload.apmCleanup', function() {

    $('.media-hotspot-item').each(function() {

        var blobUrl = $(this).data('blobUrl');

        if (blobUrl) {

            URL.revokeObjectURL(blobUrl);

            $(this).removeData('blobUrl');

        }

    });

});

        })(jQuery);
    </script>

    <script type="text/javascript">
        (function($) {
            "use strict";

            function update3dWarning() {
                var hasExistingSrc = !!$('#media_3d_viewer').attr('src');
                if ($('#media_3d_enabled').is(':checked') && !$('#media_3d_model').val() && !hasExistingSrc) {
                    $('#media_3d_status').text('{{ __('Warning: 3D model missing.') }}');
                }
            }

            function resetModelViewer() {
                $('#media_3d_viewer').removeAttr('src');
                $('#media_3d_status').text('{{ __('No 3D model selected.') }}');
            }

            $('#media_3d_model').on('change', function() {
                var file = this.files && this.files[0] ? this.files[0] : null;
                if (!file) {
                    resetModelViewer();
                    return;
                }

                var ext = file.name.split('.').pop().toLowerCase();
                if (ext !== 'glb' && ext !== 'gltf') {
                    $.notify('{{ __('Please select a .glb or .gltf file.') }}', 'warning');
                    $(this).val('');
                    resetModelViewer();
                    return;
                }

                var url = URL.createObjectURL(file);
                $('#media_3d_viewer').attr('src', url);
                $('#media_3d_status').text(file.name);
                update3dWarning();
            });

            $('#media_3d_clear').on('click', function(e) {
                e.preventDefault();
                $('#media_3d_model').val('');
                resetModelViewer();
                update3dWarning();
            });

            $('#media_3d_enabled').on('change', function() {
                update3dWarning();
            });

            update3dWarning();
        })(jQuery);
    </script>

    <script type="text/javascript">
        (function($) {
            "use strict";

            $('.cropme').simpleCropper();

            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "Select Products",
                    maximumSelectionLength: 4,
                });
            });

        })(jQuery);
    </script>


    <script type="text/javascript">
        (function($) {
            "use strict";

            $(document).ready(function() {

                let html =
                    `<img src="{{ empty($data->photo) ? asset('assets/images/noimage.png') : (filter_var($data->photo, FILTER_VALIDATE_URL) ? $data->photo : asset('assets/images/products/' . $data->photo)) }}" alt="">`;
                $(".span4.cropme").html(html);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

            });


            $('.ok').on('click', function() {

                setTimeout(
                    function() {


                        var img = $('#feature_photo').val();

                        $.ajax({
                            url: "{{ route('admin-prod-upload-update', $data->id) }}",
                            type: "POST",
                            data: {
                                "image": img
                            },
                            success: function(data) {
                                if (data.status) {
                                    $('#feature_photo').val(data.file_name);
                                }
                                if ((data.errors)) {
                                    for (var error in data.errors) {
                                        $.notify(data.errors[error], "danger");
                                    }
                                }
                            }
                        });

                    }, 1000);



            });

        })(jQuery);
    </script>

    <script type="text/javascript">
        (function($) {
            "use strict";

            $('#imageSource').on('change', function() {
                var file = this.value;
                if (file == "file") {
                    $('#f-file').show();
                    $('#f-link').hide();
                }
                if (file == "link") {
                    $('#f-file').hide();
                    $('#f-link').show();
                }
            });



            $(document).on('click', '#size-check', function() {
                if ($(this).is(':checked')) {
                    $('#default_stock').addClass('d-none')
                } else {
                    $('#default_stock').removeClass('d-none');
                }
            })

$(window).on('beforeunload.apmCleanup', function() {

    $('.media-hotspot-item').each(function() {

        var blobUrl = $(this).data('blobUrl');

        if (blobUrl) {
            URL.revokeObjectURL(blobUrl);
            $(this).removeData('blobUrl');
        }

    });

});

        })(jQuery);
    </script>


    @include('partials.admin.product.product-scripts')
@endsection
