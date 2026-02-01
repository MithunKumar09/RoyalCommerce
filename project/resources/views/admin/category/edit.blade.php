@extends('layouts.load')

@section('content')

            <div class="content-area">

              <div class="add-product-content1">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        @include('alerts.admin.form-error')
                      <form id="geniusformdata" action="{{route('admin-cat-update',$data->id)}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Name') }} *</h4>
                                <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="name" placeholder="{{ __('Enter Name') }}" required="" value="{{$data->name}}">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Slug') }} *</h4>
                                <p class="sub-heading">{{ __('(In English)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="slug" placeholder="{{ __('Enter Slug') }}" required="" value="{{$data->slug}}">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Description') }}</h4>
                                <p class="sub-heading">{{ __('(Optional - Max 2000 characters)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <textarea class="nic-edit" name="description" id="category-description" placeholder="{{ __('Enter Description') }}">{{ $data->description ?? '' }}</textarea>
                            <div class="char-counter" style="margin-top: 8px; font-size: 12px; color: #6b7280;">
                              <span id="char-count">{{ strlen(strip_tags($data->description ?? '')) }}</span> / 2000 characters
                            </div>
                            <div class="char-warning" style="display: none; margin-top: 4px; font-size: 12px; color: #e11d2e;">
                              {{ __('Character limit reached!') }}
                            </div>
                          </div>
                        </div>

                        


												<div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Current Image') }}*</h4>
														</div>
													</div>
                          <div class="col-lg-7">
                            <div class="img-upload">
                              <div id="image-preview" class="img-preview" style="background: url({{ $data->image ? asset('assets/images/categories/'.$data->image):asset('assets/images/noimage.png') }});">
                                <label for="image-upload" class="img-label"><i class="icofont-upload-alt"></i>{{ __('Upload Image') }}</label>
                                <input type="file" name="image" class="img-upload">
                              </div>
                              <p class="text">{{__('Prefered Size: (1230x267) or Square Sized Image')}}</p>
                            </div>
                          </div>

												</div>






                        <br>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">

                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Save') }}</button>
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
