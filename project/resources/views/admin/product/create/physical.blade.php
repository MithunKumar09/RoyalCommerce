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
                    <h4 class="heading">{{ __('Physical Product') }} <a class="add-btn"
                            href="{{ route('admin-prod-types') }}"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
                    </h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Products') }} </a>
                        </li>
                        <li>
                            <a href="{{ route('admin-prod-index') }}">{{ __('All Products') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin-prod-types') }}">{{ __('Add Product') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin-prod-create', 'physical') }}">{{ __('Physical Product') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <form id="geniusform" action="{{ route('admin-prod-store') }}" method="POST" enctype="multipart/form-data">
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


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Name') }}* </h4>
                                                    <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="input-field"
                                                    placeholder="{{ __('Enter Product Name') }}" name="name"
                                                    required="">
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
                                                    required=""
                                                    value="{{ Str::random(3) . substr(time(), 6, 8) . Str::random(3) }}">
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
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach ($cats as $cat)
                                                        <option data-href="{{ route('admin-subcat-load', $cat->id) }}"
                                                            value="{{ $cat->id }}">{{ $cat->name }}</option>
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
                                                <select id="subcat" name="subcategory_id" disabled="">
                                                    <option value="">{{ __('Select Sub Category') }}</option>
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
                                                <select id="childcat" name="childcategory_id" disabled="">
                                                    <option value="">{{ __('Select Child Category') }}</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div id="catAttributes"></div>
                                        <div id="subcatAttributes"></div>
                                        <div id="childcatAttributes"></div>



                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="product_condition_check"
                                                            type="checkbox" id="product_condition_check" value="1">
                                                        <label
                                                            for="product_condition_check">{{ __('Allow Product Condition') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Condition') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <select name="product_condition">
                                                        <option value="2">{{ __('New') }}</option>
                                                        <option value="1">{{ __('Used') }}</option>
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
                                                            type="checkbox" id="preorderedCheck" value="1">
                                                        <label
                                                            for="preorderedCheck">{{ __('Allow Product Preorder') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Preorder') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <select name="preordered">
                                                        <option value="1">{{ __('Sale') }}</option>
                                                        <option value="2">{{ __('Preordered') }}</option>
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
                                                            type="checkbox" id="check111" value="1">
                                                        <label for="check111">{{ __('Allow Minimum Order Qty') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Minimum Order Qty') }}* </h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="number" class="input-field" min="1"
                                                        placeholder="{{ __('Minimum Order Qty') }}" name="minimum_qty">
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
                                                            type="checkbox" id="check1" value="1">
                                                        <label
                                                            for="check1">{{ __('Allow Estimated Shipping Time') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>



                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Estimated Shipping Time') }}*
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="text" class="input-field"
                                                        placeholder="{{ __('Estimated Shipping Time') }}" name="ship">
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
                                                            id="whole_check" value="1">
                                                        <label
                                                            for="whole_check">{{ __('Allow Product Whole Sell') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">

                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="featured-keyword-area">
                                                        <div class="feature-tag-top-filds" id="whole-section">
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
                                                                        <input type="number" name="whole_sell_discount[]"
                                                                            class="input-field"
                                                                            placeholder="{{ __('Enter Discount Percentage') }}"
                                                                            min="0" />
                                                                    </div>
                                                                </div>
                                                            </div>
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

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <ul class="list">
                                                    <li>
                                                        <input class="checkclick1" name="measure_check" type="checkbox"
                                                            id="measure_check" value="1">
                                                        <label
                                                            for="measure_check">{{ __('Allow Product Measurement') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Product Measurement') }}*</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <select id="product_measure">
                                                        <option value="">{{ __('None') }}</option>
                                                        <option value="Gram">{{ __('Gram') }}</option>
                                                        <option value="Kilogram">{{ __('Kilogram') }}</option>
                                                        <option value="Litre">{{ __('Litre') }}</option>
                                                        <option value="Pound">{{ __('Pound') }}</option>
                                                        <option value="Custom">{{ __('Custom') }}</option>
                                                    </select>
                                                </div>
                                                {{-- <div class="col-lg-1"></div> --}}
                                                <div class="col-lg-12 hidden" id="measure">
                                                    <input name="measure" type="text" id="measurement"
                                                        class="input-field" placeholder="{{ __('Enter Unit') }}">
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
                                                            id="check3" value="1">
                                                        <label for="check3">{{ __('Allow Product Colors') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="showbox">

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">
                                                            {{ __('Product Colors') }}*
                                                        </h4>
                                                        <p class="sub-heading">
                                                            {{ __('(Choose Your Favorite Colors)') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="select-input-color" id="color-section">
                                                        <div class="size-area">
                                                            <span class="remove size-remove"><i
                                                                    class="fas fa-times"></i></span>
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12">
                                                                    <label>
                                                                        {{ __('Color') }} :
                                                                    </label>
                                                                    <div class="color-area">
                                                                        <div class="input-group colorpicker-component cp">
                                                                            <input type="text" name="color_all[]"
                                                                                value=""
                                                                                class="input-field cp tcolor" />
                                                                            <span class="input-group-addon"><i></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- <div class="col-md-6 col-sm-6">
                                                                    <label>
                                                                        {{ __('Color Price') }} :
                                                                        <span>
                                                                            {{ __('(Added with base price)') }}
                                                                        </span>
                                                                    </label>
                                                                    <input type="number" name="color_price[]" required
                                                                        class="input-field"
                                                                        placeholder="{{ __('Color Price') }}"
                                                                        value="" min="0">
                                                                </div> --}}


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="javascript:;" id="color-btn" class="add-more mt-4 mb-3"><i
                                                            class="fas fa-plus"></i>{{ __('Add More Color') }} </a>
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
                                                            id="size-check" value="1">
                                                        <label for="size-check"
                                                            class="stock-text">{{ __('Manage Stock') }}</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="showbox" id="size-display">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="product-size-details" id="size-section">
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
                                                                        value="" >
                                                                </div>
                                                                <div class="col-md-4 col-sm-4">
                                                                    <label>
                                                                        {{ __('Size Qty') }} :
                                                                        <span>
                                                                            {{ __('(Quantity of this size)') }}
                                                                        </span>
                                                                    </label>
                                                                    <input type="number" name="size_qty[]"
                                                                        class="input-field"
                                                                        placeholder="{{ __('Size Qty') }}" value="1"
                                                                        min="1">
                                                                </div>
                                                                <div class="col-md-4 col-sm-4">
                                                                    <label>
                                                                        {{ __('Size Price') }} :
                                                                        <span>
                                                                            {{ __('(Added with base price)') }}
                                                                        </span>
                                                                    </label>
                                                                    <input type="number" name="size_price[]"
                                                                        class="input-field"
                                                                        placeholder="{{ __('Size Price') }}"
                                                                        value="0" min="0">
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>

                                                    <a href="javascript:;" id="size-btn" class="add-more"><i
                                                            class="fas fa-plus"></i>{{ __('Add More') }} </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" id="default_stock">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Product Stock') }}*</h4>
                                                    <p class="sub-heading">
                                                        {{ __('(Leave Empty will Show Always Available)') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input name="stock" type="number" class="input-field"
                                                    placeholder="e.g 20" value="" min="0">
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
                                                    <textarea class="nic-edit" name="details"></textarea>
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
                                                    <textarea class="nic-edit" name="policy"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="checkbox-wrapper">
                                                    <input type="checkbox" name="seo_check" value="1"
                                                        class="checkclick" id="allowProductSEO" value="1">
                                                    <label for="allowProductSEO">{{ __('Allow Product SEO') }}</label>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="showbox">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="left-area">
                                                        <h4 class="heading">{{ __('Meta Tags') }} *</h4>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <ul id="metatags" class="myTags">
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
                                                        <textarea name="meta_description" class="input-field" placeholder="{{ __('Meta Description') }}"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="type" value="Physical">

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
                                                        <a href="javascript:;" id="crop-image" class=" mybtn1"
                                                            style="">
                                                            <i class="icofont-upload-alt"></i>
                                                            {{ __('Upload Image Here') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="feature_photo" name="photo" value="">

                                        <input type="file" name="gallery[]" class="hidden" id="uploadgallery"
                                            accept="image/*" multiple>
                                        <div class="row mb-4">
                                            <div class="col-lg-12 mb-2">
                                                <div class="left-area">
                                                    <h4 class="heading">
                                                        {{ __('Product Gallery Images') }} *
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <a href="#" class="set-gallery" data-toggle="modal"
                                                    data-target="#setgallery">
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
                                                    placeholder="{{ __('e.g 20') }}" step="0.1" required=""
                                                    min="0">
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
                                                    class="input-field" placeholder="{{ __('e.g 20') }}"
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
                                                    placeholder="{{ __('Enter Youtube Video URL') }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="featured-keyword-area">
                                                    <div class="heading-area">
                                                        <h4 class="title">{{ __('Feature Tags') }}</h4>
                                                    </div>

                                                    <div class="feature-tag-top-filds" id="feature-section">
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
                                                </ul>
                                            </div>
                                        </div>

                                        @php
                                            $product = null; $mode = 'create'; $productId = 0;
                                            $mediaExtra = []; $v360 = []; $hotspots = []; $model3d = [];
                                            $v360Count = 0; $v360HasFrames = false;
                                            $hotspotItems = []; $hotspotBase = '';
                                            $mediaVideos = collect(); $mediaVideoMap = [];
                                        @endphp
                                        @include('admin.product.partials.advanced-media-panel', [
                                            'product'       => $product,
                                            'mode'          => $mode,
                                            'productId'     => $productId,
                                            'mediaExtra'    => $mediaExtra,
                                            'v360'          => $v360,
                                            'hotspots'      => $hotspots,
                                            'model3d'       => $model3d,
                                            'mediaVideos'   => $mediaVideos,
                                            'mediaVideoMap' => $mediaVideoMap,
                                        ])

                                        <div class="row text-center">
                                            <div class="col-6 offset-3">
                                                <button class="addProductSubmit-btn"
                                                    type="submit">{{ __('Create Product') }}</button>
                                            </div>
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
                                    <label for="image-upload" id="prod_gallery"><i
                                            class="icofont-upload-alt"></i>{{ __('Upload File') }}</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <a href="javascript:;" class="upload-done" data-dismiss="modal"> <i
                                        class="fas fa-check"></i> {{ __('Done') }}</a>
                            </div>
                            <div class="col-sm-12 text-center">(
                                <small>{{ __('You can upload multiple Images.') }}</small> )
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/jquery.Jcrop.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.SimpleCropper.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select2.js') }}"></script>

    <script type="text/javascript">
window.productMediaRegistry = {
    featureImage: null,
    featureImageObjectUrl: null,

    galleryImages: [],
    galleryObjectUrls: [],

    model3dObjectUrl: null,
    model3dFile: null,
    hotspots: []
};

function syncHotspotBaseImages() {

    const $baseSelect = $('#media_hotspot_base');

    if (!$baseSelect.length) {
        return;
    }

    const currentValue = $baseSelect.val();

    const hasCurrentSelection =
    currentValue &&
    $baseSelect.find(
        'option[value="' + currentValue + '"]'
    ).length;

$baseSelect.find('.runtime-gallery-option').remove();

if (window.productMediaRegistry.featureImage) {

    let featurePreviewSrc =
        window.productMediaRegistry.featureImage;

    /*
    |--------------------------------------------------------------------------
    | Convert base64 -> blob URL
    |--------------------------------------------------------------------------
    */
    if (
        typeof featurePreviewSrc === 'string' &&
        featurePreviewSrc.indexOf('data:image') === 0
    ) {

        try {

            const arr =
                featurePreviewSrc.split(',');

            const mime =
                arr[0].match(/:(.*?);/)[1];

            const bstr =
                atob(arr[1]);

            let n = bstr.length;

            const u8arr =
                new Uint8Array(n);

            while (n--) {

                u8arr[n] =
                    bstr.charCodeAt(n);

            }

            const blob = new Blob(
                [u8arr],
                { type: mime }
            );

            featurePreviewSrc =
                URL.createObjectURL(blob);

            /*
            |--------------------------------------------------------------------------
            | Cleanup previous object URL
            |--------------------------------------------------------------------------
            */
            if (
                window.productMediaRegistry
                    .featureImageObjectUrl
            ) {

                URL.revokeObjectURL(
                    window.productMediaRegistry
                        .featureImageObjectUrl
                );

            }

            window.productMediaRegistry
                .featureImageObjectUrl =
                    featurePreviewSrc;

        } catch (e) {

            console.error(
                'feature image conversion failed',
                e
            );

        }

    }

    $baseSelect.append(
        '<option class="runtime-gallery-option" ' +
        'value="feature" ' +
        'data-src="' + featurePreviewSrc + '">' +
        'Feature Image' +
        '</option>'
    );

}

    window.productMediaRegistry.galleryImages.forEach(function(image, index) {

    if (!image) {
        return;
    }

        $baseSelect.append(
            '<option class="runtime-gallery-option" ' +
            'value="gallery_' + Date.now() + '_' + index + '" ' +
            'data-src="' + image + '">' +
            'Gallery Image ' + (index + 1) +
            '</option>'
        );

    });

if (hasCurrentSelection) {

    $baseSelect.val(currentValue);

} else {

    const firstRuntimeOption =
        $baseSelect.find(
            '.runtime-gallery-option'
        ).first();

    if (firstRuntimeOption.length) {

        $baseSelect.val(
            firstRuntimeOption.val()
        );

    }

}

syncHotspotPreviewImage();

}

function waitForImageReady(img, callback) {

    if (!img) {
        return;
    }

    if (
        img.complete &&
        img.naturalWidth > 0
    ) {

        callback();

        return;
    }

    $(img)
        .off('.apmImgReady')
        .one('load.apmImgReady', function() {

            callback();

        })
        .one('error.apmImgReady', function() {

            mlog('image failed to load', img.src);

        });

}

function syncHotspotPreviewImage() {

    const $baseSelect = $('#media_hotspot_base');

    const selectedSrc = $baseSelect
        .find(':selected')
        .data('src');

    const img =
        document.getElementById('media_hotspot_image');

    if (!img) {
        return;
    }

let previewSrc =
    selectedSrc || '/assets/images/noimage.png';

if (
    previewSrc &&
    previewSrc.indexOf('blob:') !== 0 &&
    previewSrc.indexOf('data:image') !== 0
) {

    previewSrc += (
        previewSrc.indexOf('?') >= 0
            ? '&'
            : '?'
    ) + '_ts=' + Date.now();

}

img.src = previewSrc;

    waitForImageReady(img, function() {

        $(img).trigger('apm:image-ready');
        renderHotspots();

    });

}

function renderHotspots() {

    const wrapper =
        document.getElementById(
            'media_hotspot_preview_wrapper'
        );

    const img =
        document.getElementById(
            'media_hotspot_image'
        );

    if (!wrapper || !img) {
        return;
    }

    wrapper
        .querySelectorAll('.apm-hotspot')
        .forEach(function(node) {

            node.remove();

        });

    window.productMediaRegistry.hotspots
        .forEach(function(hotspot, index) {

            const marker =
                document.createElement('div');

            marker.className =
                'apm-hotspot';

            marker.setAttribute(
                'data-index',
                index
            );

            marker.style.position = 'absolute';

            marker.style.left =
                hotspot.x + '%';

            marker.style.top =
                hotspot.y + '%';

            marker.style.transform =
                'translate(-50%, -50%)';

            marker.style.width = '18px';

            marker.style.height = '18px';

            marker.style.borderRadius =
                '50%';

            marker.style.background =
                '#2563eb';

            marker.style.border =
                '2px solid #fff';

            marker.style.cursor =
                'pointer';

            marker.style.zIndex = '50';

            wrapper.appendChild(marker);

        });

}

$(document)
.off('click.apmHotspotCreate')
.on(
    'click.apmHotspotCreate',
    '#media_hotspot_image',
    function(e) {

        const image =
            e.currentTarget;

        const rect =
            image.getBoundingClientRect();

        const x =
            (
                (e.clientX - rect.left)
                / rect.width
            ) * 100;

        const y =
            (
                (e.clientY - rect.top)
                / rect.height
            ) * 100;

        window.productMediaRegistry.hotspots.push({
            x: Number(x.toFixed(2)),
            y: Number(y.toFixed(2))
        });

        renderHotspots();

        syncHotspotInputs();

    }
);

function syncHotspotInputs() {

    $('#apm-hotspot-hidden-inputs').remove();

    const container =
        $('<div id="apm-hotspot-hidden-inputs"></div>');

    window.productMediaRegistry.hotspots
        .forEach(function(hotspot) {

            container.append(
                '<input type="hidden" ' +
                'name="media_hotspots[]" ' +
                'value=\'' +
                JSON.stringify(hotspot) +
                '\'>'
            );

        });

    $('#geniusform').append(container);

}

        (function($) {
            "use strict";
            if (
    typeof ensureModelViewerScripts === 'function'
) {

    ensureModelViewerScripts();

}

function refreshCreate3DPreview(objectUrl) {

    const $preview =
        $('#media_3d_preview');

    if (!$preview.length) {
        return;
    }

    if (!objectUrl) {

        $preview.html(
            '<p>No 3D model selected.</p>'
        );

        return;
    }

    $preview.html(
        '<model-viewer ' +
            'src="' + objectUrl + '" ' +
            'camera-controls ' +
            'auto-rotate ' +
            'shadow-intensity="1" ' +
            'style="width:100%;height:320px;">' +
        '</model-viewer>'
    );

}

$(document).on(
    'change',
    '#media_hotspot_base',
    function () {

        syncHotspotPreviewImage();

    }
);

$(document)
    .off('change.apm3dCreate')
    .on(
        'change.apm3dCreate',
        '#media_3d_file',
        function(e) {

            const file =
                e.target.files &&
                e.target.files[0];

            if (!file) {

                refreshCreate3DPreview(null);

                return;

            }

            if (
                window.productMediaRegistry
                    .model3dObjectUrl
            ) {

                URL.revokeObjectURL(
                    window.productMediaRegistry
                        .model3dObjectUrl
                );

            }

            const objectUrl =
                URL.createObjectURL(file);

            window.productMediaRegistry
                .model3dObjectUrl = objectUrl;

            window.productMediaRegistry
                .model3dFile = file;

            refreshCreate3DPreview(objectUrl);

        }
    );


            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "Select Products",
                    maximumSelectionLength: 4,
                });
            });

            // Gallery Section Insert

$(document)
    .off('click.apmRemoveGallery')
    .on(
        'click.apmRemoveGallery',
        '.remove-img',
        function() {

    var id = $(this).find('input[type=hidden]').val();

    $('#galval' + id).remove();

    $(this).closest('.col-sm-6').remove();

    const imageUrl =
    window.productMediaRegistry.galleryImages[id];

if (
    imageUrl &&
    imageUrl.indexOf('blob:') === 0
) {

    URL.revokeObjectURL(imageUrl);

}

window.productMediaRegistry.galleryImages[id] = null;

    syncHotspotBaseImages();

});

            $(document).on('click', '#prod_gallery', function() {
                $('#uploadgallery').click();
                $('.selected-image .row').html('');
                $('#geniusform .removegal').remove();
            });


            $(document)
    .off('change.apmGalleryUpload')
    .on(
        'change.apmGalleryUpload',
        '#uploadgallery',
        function(e) {
                var total_file = document.getElementById("uploadgallery").files.length;
const startIndex =
    window.productMediaRegistry.galleryImages.length;

for (var i = 0; i < total_file; i++) {

    const objectUrl = URL.createObjectURL(
        e.target.files[i]
    );

    window.productMediaRegistry.galleryObjectUrls.push(
    objectUrl
);

window.productMediaRegistry.galleryImages.push(
    objectUrl
);

    $('.selected-image .row').append(
        '<div class="col-sm-6">' +
            '<div class="img gallery-img">' +
                '<span class="remove-img">' +
                    '<i class="fas fa-times"></i>' +
                    '<input type="hidden" value="' + (startIndex + i) + '">' +
                '</span>' +
                '<a href="' + objectUrl + '" target="_blank">' +
                    '<img src="' + objectUrl + '" alt="gallery image">' +
                '</a>' +
            '</div>' +
        '</div>'
    );

    $('#geniusform').append(
        '<input type="hidden" ' +
        'name="galval[]" ' +
        'id="galval' + (startIndex + i) + '" ' +
        'class="removegal" ' +
        'value="' + (startIndex + i) + '">'
    );
}

syncHotspotBaseImages();

});

            // Gallery Section Insert Ends

        })(jQuery);
    </script>

    <script type="text/javascript">
        $('.cp').colorpicker();

        (function($) {
            "use strict";

            $('.cropme').simpleCropper();

            $(document)
    .off('change.apmFeaturePhoto')
    .on('change.apmFeaturePhoto', '#feature_photo', function() {

        const featureImage = $(this).val();

if (!featureImage) {

    window.productMediaRegistry.featureImage = null;

    syncHotspotBaseImages();

    return;
}

        if (
            window.productMediaRegistry.featureImage === featureImage
        ) {
            return;
        }

        window.productMediaRegistry.featureImage = featureImage;

        syncHotspotBaseImages();

    });



$(window).on('beforeunload', function () {

    if (
    window.productMediaRegistry
        .model3dObjectUrl
) {

    URL.revokeObjectURL(
        window.productMediaRegistry
            .model3dObjectUrl
    );

}

    window.productMediaRegistry.galleryObjectUrls.forEach(function(url) {
    URL.revokeObjectURL(url);
});

    window.productMediaRegistry.galleryObjectUrls = [];

});

        })(jQuery);


        $(document).on('click', '#size-check', function() {
            if ($(this).is(':checked')) {
                $('#default_stock').addClass('d-none')
            } else {
                $('#default_stock').removeClass('d-none');
            }
        })
    </script>


    @include('partials.admin.product.product-scripts')

    <script type="text/javascript">
        (function($) {
            "use strict";
            // Ensure CSRF token is sent with all AJAX calls (required by media panel if JS is added later).
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
        })(jQuery);
    </script>
@endsection
