@extends('layouts.admin')

@section('content')
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">
                        {{ __('Edit Category Slider') }}
                        <a class="add-btn" href="{{ route('category-sliders.index') }}">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Manage Categories') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('category-sliders.index') }}">{{ __('Category Sliders') }}</a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Edit Category Slider') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="add-product-content1 add-product-content2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product-description">
                        <div class="body-area">
                            <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                            <form id="geniusform" action="{{ route('category-sliders.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                @include('alerts.admin.form-both')

                                <input type="hidden" name="_method" value="POST">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Category') }}</h4>
                                            <p class="sub-heading">{{ __('Optional') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <select name="category_id" class="input-field">
                                            <option value="">{{ __('All Categories') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ (string) $data->category_id === (string) $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Title') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="text" class="input-field" name="title" placeholder="{{ __('Title') }}" value="{{ $data->title }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Subtitle') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="text" class="input-field" name="subtitle" placeholder="{{ __('Subtitle') }}" value="{{ $data->subtitle }}">
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Current Image') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="img-upload full-width-img">
                                            <div id="image-preview" class="img-preview"
                                                 style="background: url({{ $data->photo ? asset('assets/images/category-sliders/' . $data->photo) : asset('assets/admin/images/upload.png') }});">
                                                <label for="image-upload" class="img-label" id="image-label">
                                                    <i class="icofont-upload-alt"></i>{{ __('Upload Image') }}
                                                </label>
                                                <input type="file" name="photo" class="img-upload" id="image-upload">
                                            </div>
                                            <p class="text">{{ __('Prefered Size: (1230x267) or wide banner image') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Link') }}</h4>
                                            <p class="sub-heading">{{ __('Optional') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="text" class="input-field" name="link" placeholder="{{ __('Link') }}" value="{{ $data->link }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Sort Order') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="number" class="input-field" name="sort_order" value="{{ $data->sort_order }}" min="0">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">{{ __('Status') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <select name="status" class="input-field">
                                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area"></div>
                                    </div>
                                    <div class="col-lg-7">
                                        <button class="addProductSubmit-btn" type="submit">{{ __('Update Slider') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
