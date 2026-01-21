@extends('layouts.admin')
@section('styles')

<link href="{{asset('assets/admin/css/product.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/admin/css/jquery.Jcrop.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/admin/css/Jcrop-style.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/admin/css/select2.css')}}" rel="stylesheet"/>

@endsection
@section('content')
	<div class="content-area">
		<div class="mr-breadcrumb">
			<div class="row">
				<div class="col-lg-12">
						<h4 class="heading"> {{ __("Edit Product") }}<a class="add-btn" href="{{ url()->previous() }}"><i class="fas fa-arrow-left"></i> {{ __("Back") }}</a></h4>
						<ul class="links">
							<li>
								<a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
							</li>
							<li>
								<a href="{{ route('admin-prod-index') }}">{{ __("Products") }} </a>
							</li>
							<li>
								<a href="javascript:;">{{ __("Digital Product") }}</a>
							</li>
							<li>
								<a href="{{ url()->previous() }}">{{ __("Edit") }}</a>
							</li>
						</ul>
				</div>
			</div>
		</div>

		<form id="geniusform" action="{{route('admin-prod-update',$data->id)}}" method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
			@include('alerts.admin.form-both')
			<div class="row">
				<div class="col-lg-8">
					<div class="add-product-content">
						<div class="row">
							<div class="col-lg-12">
								<div class="product-description">
									<div class="body-area">
										<div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
										

										<div class="row">
											<div class="col-lg-12">
												<div class="left-area">
														<h4 class="heading">{{ __("Product Name") }}* </h4>
														<p class="sub-heading">{{ __("(In Any Language)") }}</p>
												</div>
											</div>
											<div class="col-lg-12">
												<input type="text" class="input-field" placeholder="{{ __("Enter Product Name") }}" name="name" required="" value="{{ $data->name }}">
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
													@foreach($cats as $cat)
														<option data-href="{{ route('admin-subcat-load',$cat->id) }}" value="{{$cat->id}}" {{$cat->id == $data->category_id ? "selected":""}} >{{$cat->name}}</option>
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
														@if($data->subcategory_id == null)
														@foreach($data->category->subs as $sub)
														<option data-href="{{ route('admin-childcat-load',$sub->id) }}" value="{{$sub->id}}" >{{$sub->name}}</option>
														@endforeach
														@else
														@foreach($data->category->subs as $sub)
														<option data-href="{{ route('admin-childcat-load',$sub->id) }}" value="{{$sub->id}}" {{$sub->id == $data->subcategory_id ? "selected":""}} >{{$sub->name}}</option>
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
												<select id="childcat" name="childcategory_id" {{$data->subcategory_id == null ? "disabled":""}}>
														<option value="">{{ __('Select Child Category') }}</option>
														@if($data->subcategory_id != null)
														@if($data->childcategory_id == null)
														@foreach($data->subcategory->childs as $child)
														<option value="{{$child->id}}" >{{$child->name}}</option>
														@endforeach
														@else
														@foreach($data->subcategory->childs as $child)
														<option value="{{$child->id}} " {{$child->id == $data->childcategory_id ? "selected":""}}>{{$child->name}}</option>
														@endforeach
														@endif
														@endif
												</select>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-12">
												<div class="left-area">
														<h4 class="heading">{{ __("Select Upload Type") }}*</h4>
												</div>
											</div>
											<div class="col-lg-12">
													<select id="type_check" name="type_check">
													  <option value="1" {{ $data->file != null ? 'selected':'' }}>{{ __("Upload By File") }}</option>
													  <option value="2" {{ $data->link != null ? 'selected':'' }}>{{ __("Upload By Link") }}</option>
													</select>
											</div>
										</div>

										<div class="row file {{ $data->file != null ? '':'hidden' }}">
											<div class="col-lg-12">
												<div class="left-area">
														<h4 class="heading">{{ __("Select File") }}*</h4>
												</div>
											</div>
											<div class="col-lg-12">
													<input type="file" name="file">
											</div>
										</div>

										<div class="row link {{ $data->link != null ? '':'hidden' }}">
											<div class="col-lg-12">
												<div class="left-area">
														<h4 class="heading">{{ __("Link") }}*</h4>
												</div>
											</div>
											<div class="col-lg-12">
													<textarea class="input-field" rows="4" name="link" placeholder="{{ __("Link") }}" {{ $data->link != null ? 'required':'' }}>{{ $data->link }}</textarea> 
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
												<textarea name="details" class="nic-edit">{{$data->details}}</textarea>
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
												<textarea name="policy" class="nic-edit">{{$data->policy}}</textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="checkbox-wrapper">
												<input type="checkbox" name="seo_check" value="1" class="checkclick" id="allowProductSEO" {{ ($data->meta_tag != null || strip_tags($data->meta_description) != null) ? 'checked':'' }}>
												<label for="allowProductSEO">{{ __('Allow Product SEO') }}</label>
											  </div>
										</div>
									</div>

									<div class="{{ ($data->meta_tag == null && strip_tags($data->meta_description) == null) ? "showbox":"" }}">
										<div class="row">
										  <div class="col-lg-12">
											<div class="left-area">
												<h4 class="heading">{{ __('Meta Tags') }} *</h4>
											</div>
										  </div>
										  <div class="col-lg-12">
											<ul id="metatags" class="myTags">
												@if(!empty($data->meta_tag))
												  @foreach ($data->meta_tag as $element)
													<li>{{  $element }}</li>
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
													<div class="span4 cropme text-center" id="landscape" style="width: 100%; height: 285px; border: 1px dashed #ddd; background: #f1f1f1;">
														<a href="javascript:;" id="crop-image" class="d-inline-block mybtn1">
															<i class="icofont-upload-alt"></i> {{ __('Upload Image Here') }}
														</a>
													</div>
												</div>
											</div>
										  </div>

										  <input type="hidden" id="feature_photo" name="photo" value="{{ $data->photo }}" accept="image/*">
											<div class="row">
												<div class="col-lg-12">
													<div class="left-area">
														<h4 class="heading">
															{{ __('Product Gallery Images') }} *
														</h4>
													</div>
												</div>
												<div class="col-lg-12">
													<a href="javascript" class="set-gallery"  data-toggle="modal" data-target="#setgallery">
														<input type="hidden" value="{{$data->id}}">
															<i class="icofont-plus"></i> {{ __('Set Gallery') }}
													</a>
												</div>
											</div>

											<div class="row">
												<div class="col-lg-12">
													<div class="card">
														<div class="card-header" id="media-advanced-heading">
															<h5 class="mb-0">
																<button class="btn btn-link" type="button"
																	data-toggle="collapse" data-target="#media-advanced-collapse"
																	aria-expanded="false" aria-controls="media-advanced-collapse">
																	{{ __('Advanced Product Media (Optional)') }}
																</button>
															</h5>
														</div>
														<div id="media-advanced-collapse" class="collapse"
															aria-labelledby="media-advanced-heading">
															<div class="card-body">
																@php
																	$mediaExtra = json_decode($data->media_extra, true);
																	if (!is_array($mediaExtra)) { $mediaExtra = []; }
																	$v360 = isset($mediaExtra['v360']) && is_array($mediaExtra['v360']) ? $mediaExtra['v360'] : [];
																	$hotspots = isset($mediaExtra['hotspots']) && is_array($mediaExtra['hotspots']) ? $mediaExtra['hotspots'] : [];
																	$model3d = isset($mediaExtra['model3d']) && is_array($mediaExtra['model3d']) ? $mediaExtra['model3d'] : [];
																	$v360Count = isset($v360['frame_count']) ? (int) $v360['frame_count'] : 0;
																	$v360HasFrames = $v360Count > 0;
																	$hotspotItems = isset($hotspots['items']) && is_array($hotspots['items']) ? $hotspots['items'] : [];
																	$hotspotBase = isset($hotspots['target_image']) ? (string) $hotspots['target_image'] : '';
																@endphp
																<div style="display:none;">
																	<input type="hidden" name="media_3d_auto_rotate" value="{{ !empty($model3d['viewer']['auto_rotate']) ? 1 : 0 }}">
																	<input type="hidden" name="media_3d_exposure" value="{{ isset($model3d['viewer']['exposure']) ? $model3d['viewer']['exposure'] : '' }}">
																	<input type="hidden" name="media_3d_camera_orbit" value="{{ isset($model3d['viewer']['camera_orbit']) ? $model3d['viewer']['camera_orbit'] : '' }}">
																</div>
																<div class="accordion" id="media-advanced-accordion">
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
																			<div class="card-body">
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
																					<div class="col-lg-12">
																						<input type="file" class="input-field"
																							name="media_360_frames[]" id="media_360_frames" multiple>
																					</div>
																				</div>
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
																			<div class="card-body">
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
																							data-src="{{ empty($data->photo) ? asset('assets/images/noimage.png') : (filter_var($data->photo, FILTER_VALIDATE_URL) ? $data->photo : asset('assets/images/products/' . $data->photo)) }}">
																							{{ __('Feature Image') }}
																						</option>
																						@if ($data->galleries && $data->galleries->count() > 0)
																							@foreach ($data->galleries as $gallery)
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
																			<div class="card-body">
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
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-lg-12">
													<div class="left-area">
														<h4 class="heading">
															{{ __('Product Current Price') }}*
														</h4>
														<p class="sub-heading">
															({{ __('In') }} {{$sign->name}})
														</p>
													</div>
												</div>
												<div class="col-lg-12">
													<input name="price" type="number" class="input-field" placeholder="e.g 20" step="0.1" min="0" value="{{round($data->price * $sign->value , 2)}}" required="">
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
													<input name="previous_price" step="0.1" type="number" class="input-field" placeholder="e.g 20" value="{{round($data->previous_price * $sign->value , 2)}}" min="0">
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
													<input  name="youtube" type="text" class="input-field" placeholder="Enter Youtube Video URL" value="{{$data->youtube}}">
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
													@if(!empty($data->features))

														 @foreach($data->features as $key => $data1)

													<div class="feature-area">
														<span class="remove feature-remove"><i class="fas fa-times"></i></span>
														<div class="row">
															<div class="col-lg-6">
															<input type="text" name="features[]" class="input-field" placeholder="{{ __('Enter Your Keyword') }}" value="{{ $data->features[$key] }}">
															</div>

															<div class="col-lg-6">
																<div class="input-group colorpicker-component cp">
																  <input type="text" name="colors[]" value="{{ $data->colors[$key] }}" class="input-field cp"/>
																  <span class="input-group-addon"><i></i></span>
																</div>
															</div>
														</div>
													</div>

														@endforeach
													@else

													<div class="feature-area">
														<span class="remove feature-remove"><i class="fas fa-times"></i></span>
														<div class="row">
															<div class="col-lg-6">
															<input type="text" name="features[]" class="input-field" placeholder="{{ __('Enter Your Keyword') }}">
															</div>

															<div class="col-lg-6">
																<div class="input-group colorpicker-component cp">
																  <input type="text" name="colors[]" value="#000000" class="input-field cp"/>
																  <span class="input-group-addon"><i></i></span>
																</div>
															</div>
														</div>
													</div>

													@endif
												</div>

												<a href="javascript:;" id="feature-btn" class="add-fild-btn"><i class="icofont-plus"></i> {{ __('Add More Field') }}</a>
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
											  @if(!empty($data->tags))
												  @foreach ($data->tags as $element)
													<li>{{  $element }}</li>
												  @endforeach
											  @endif
										  </ul>
										</div>
									  </div>

									  <div class="row text-center">
										<div class="col-6 offset-3">
											<button class="addProductSubmit-btn" type="submit">{{ __('Save') }}</button>
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

		<div class="modal fade" id="setgallery" tabindex="-1" role="dialog" aria-labelledby="setgallery" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalCenterTitle">{{ __("Image Gallery") }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="top-area">
						<div class="row">
							<div class="col-sm-6 text-right">
								<div class="upload-img-btn">
									<form  method="POST" enctype="multipart/form-data" id="form-gallery">
										@csrf
									<input type="hidden" id="pid" name="product_id" value="">
									<input type="file" name="gallery[]" class="hidden" id="uploadgallery" accept="image/*" multiple>
											<label for="image-upload" id="prod_gallery"><i class="icofont-upload-alt"></i>{{ __("Upload File") }}</label>
									</form>
								</div>
							</div>
							<div class="col-sm-6">
								<a href="javascript:;" class="upload-done" data-dismiss="modal"> <i class="fas fa-check"></i> {{ __("Done") }}</a>
							</div>
							<div class="col-sm-12 text-center">( <small>{{ __("You can upload multiple Images.") }}</small> )</div>
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
	
    $(function($) {
		"use strict";

    $(document).on("click", ".set-gallery" , function(){
        var pid = $(this).find('input[type=hidden]').val();
        $('#pid').val(pid);
        $('.selected-image .row').html('');
            $.ajax({
                    type: "GET",
                    url:"{{ route('admin-gallery-show') }}",
                    data:{id:pid},
                    success:function(data){
                      if(data[0] == 0)
                      {
	                    $('.selected-image .row').addClass('justify-content-center');
	      				$('.selected-image .row').html('<h3>{{ __("No Images Found.") }}</h3>');
     				  }
                      else {
	                    $('.selected-image .row').removeClass('justify-content-center');
	      				$('.selected-image .row h3').remove();      
                          var arr = $.map(data[1], function(el) {
                          return el });

                          for(var k in arr)
                          {
        				$('.selected-image .row').append('<div class="col-sm-6">'+
                                        '<div class="img gallery-img">'+
                                            '<span class="remove-img"><i class="fas fa-times"></i>'+
                                            '<input type="hidden" value="'+arr[k]['id']+'">'+
                                            '</span>'+
                                            '<a href="'+'{{asset('assets/images/galleries').'/'}}'+arr[k]['photo']+'" target="_blank">'+
                                            '<img src="'+'{{asset('assets/images/galleries').'/'}}'+arr[k]['photo']+'" alt="gallery image">'+
                                            '</a>'+
                                        '</div>'+
                                  	'</div>');
                          }                         
                       }
 
                    }
                  });
      });


  $(document).on('click', '.remove-img' ,function() {
    var id = $(this).find('input[type=hidden]').val();
    $(this).parent().parent().remove();
	    $.ajax({
	        type: "GET",
	        url:"{{ route('admin-gallery-delete') }}",
	        data:{id:id}
	    });
  });

  $(document).on('click', '#prod_gallery' ,function() {
    $('#uploadgallery').click();
  });
                                        
                                
  $("#uploadgallery").change(function(){
    $("#form-gallery").submit();  
  });

  $(document).on('submit', '#form-gallery' ,function() {
		  $.ajax({
		   url:"{{ route('admin-gallery-store') }}",
		   method:"POST",
		   data:new FormData(this),
		   dataType:'JSON',
		   contentType: false,
		   cache: false,
		   processData: false,
		   success:function(data)
		   {
		    if(data != 0)
		    {
	                    $('.selected-image .row').removeClass('justify-content-center');
	      				$('.selected-image .row h3').remove();   
		        var arr = $.map(data, function(el) {
		        return el });
		        for(var k in arr)
		           {
        				$('.selected-image .row').append('<div class="col-sm-6">'+
                                        '<div class="img gallery-img">'+
                                            '<span class="remove-img"><i class="fas fa-times"></i>'+
                                            '<input type="hidden" value="'+arr[k]['id']+'">'+
                                            '</span>'+
                                            '<a href="'+'{{asset('assets/images/galleries').'/'}}'+arr[k]['photo']+'" target="_blank">'+
                                            '<img src="'+'{{asset('assets/images/galleries').'/'}}'+arr[k]['photo']+'" alt="gallery image">'+
                                            '</a>'+
                                        '</div>'+
                                  	'</div>');
		            }          
		    }
		                     
		                       }

		  });
		  return false;
 }); 


})(jQuery);

</script>

<script src="{{asset('assets/admin/js/jquery.Jcrop.js')}}"></script>
<script src="{{asset('assets/admin/js/jquery.SimpleCropper.js')}}"></script>
<script src="{{asset('assets/admin/js/select2.js')}}"></script>
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
            if (document.getElementById(id)) {
                mlog('script already loaded:', id);
                resolve();
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
            script.onload = function() {
                mlog('script loaded:', id);
                resolve();
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

        $('.custom_previous').bind('click', function(e) {
            pro_view.previous();
        });

        $('.custom_next').bind('click', function(e) {
            pro_view.next();
        });

        $('.custom_play').bind('click', function(e) {
            pro_view.play();
            $('.nav_bar').addClass('play-video');
        });

        $('.custom_stop').bind('click', function(e) {
            pro_view.stop();
            $('.nav_bar').removeClass('play-video');
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

    function loadMedia360Manifest(mode, callback) {
        $.get("{{ route('admin-prod-media-360-manifest', $data->id) }}", function(data) {
            if (data.frames) {
                mlog('manifest loaded:', { mode: mode, frames: (data.frames ? data.frames.length : 0) });
                setMedia360Frames(data.frames, mode);
                if (typeof callback === 'function') {
                    callback();
                }
            }
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
        $('#media_360_mode').on('change', function() {
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
            ensure360Script().then(function() {
                loadMedia360Manifest('append');
                if (pending360Init) {
                    init360Viewer();
                    pending360Init = false;
                }
            });
            ensureModelViewerScripts();
        });

        $('#media_360_upload_btn').on('click', function(e) {
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
            $.ajax({
                method: "POST",
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
                }
            });
        });

        $('#media_360_delete_btn').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                method: "POST",
                url: "{{ route('admin-prod-media-360-delete', $data->id) }}",
                success: function(data) {
                    loadMedia360Manifest('replace');
                    $('.product-images-item').html('');
                    $.notify('{{ __('360° frames deleted.') }}', 'success');
                }
            });
        });

        $('#media_360_preview_btn').on('click', function(e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
            }
        });

        $('#media_360_enabled').on('change', function() {
            update360Warning();
        });

        $('#view360').on('shown.bs.modal', function() {
            $(window).trigger('resize');
        });
    });
</script>

<style>
    .media-hotspot-preview .lookbook-block {
        position: relative;
        width: 100%;
    }

    .media-hotspot-preview .lookbook-dot {
        cursor: pointer;
        position: absolute;
        z-index: 2;
        width: 29px;
        height: 29px;
        line-height: 29px;
        border-radius: 50%;
        background-color: #ffffff;
        text-align: center;
    }

    .media-hotspot-preview .lookbook-dot span {
        font-size: 12px;
        color: #000000;
    }

    .media-hotspot-preview .lookbook-dot .dot-showbox {
        visibility: hidden;
        top: -98px;
        left: 150%;
        position: absolute;
        width: 180px;
        background-color: #ffffff;
        box-shadow: -3px -3px 13px rgba(48, 54, 61, 0.1);
        padding: 10px;
    }

    .media-hotspot-preview .lookbook-dot:hover .dot-showbox {
        visibility: visible;
    }

    .media-hotspot-group {
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 12px;
    }

    .media-hotspot-group-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .media-hotspot-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .media-hotspot-status {
        display: inline-block;
        margin-top: 6px;
        font-size: 12px;
    }

    .media-hotspot-item.is-hidden {
        opacity: 0.6;
    }

    .media-360-hotspots {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 3;
    }

    .media-360-hotspots .lookbook-dot {
        pointer-events: auto;
    }

    .media-360-hotspots.dragging .dot-showbox {
        visibility: hidden;
    }

    /* 360 view essentials to avoid stacked frames */
    .product-image-360 {
        position: relative;
        overflow: hidden;
        margin: 0 auto;
    }

    .product-image-360 .product-images-item {
        list-style: none;
        margin: 0;
        padding: 0;
        position: relative;
        width: 100%;
        height: 100%;
    }

    .product-image-360 .product-images-item li {
        list-style: none;
    }

    .product-image-360 .product-images-item img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 100%;
        height: auto;
    }

    .product-image-360 .product-images-item img.previous-image {
        visibility: hidden;
    }

    .product-image-360 .product-images-item img.current-image {
        visibility: visible;
    }

    /* BigDeal nav bar styling + play/stop behavior (admin) */
    .product-image-360 {
        cursor: pointer;
        min-height: 420px; /* prevents 0-height container when frames are absolute */
    }

    .product-image-360 .nav_bar {
        position: absolute;
        bottom: 40px;
        left: 50%;
        margin-left: -67.5px; /* 3 visible buttons (stop toggles play) */
        z-index: 11;
        background-color: #ffffff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
        border-radius: 2px;
    }

    .product-image-360 .nav_bar a {
        display: inline-block;
        width: 45px;
        height: 45px;
        line-height: 45px;
        text-align: center;
        text-decoration: none;
        color: #444444;
        font-size: 18px;
    }

    /* icon fallback (admin does not include themify icons) */
    .product-image-360 .nav_bar a i {
        display: none;
    }
    .product-image-360 .nav_bar a.custom_previous:before { content: '‹'; }
    .product-image-360 .nav_bar a.custom_next:before { content: '›'; }
    .product-image-360 .nav_bar a.custom_play:before { content: '▶'; }
    .product-image-360 .nav_bar a.custom_stop:before { content: '❚❚'; }

    .custom_stop {
        display: none !important;
    }
    .play-video .custom_play {
        display: none !important;
    }
    .play-video .custom_stop {
        display: inline-block !important;
    }
</style>

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
                imgSrc = URL.createObjectURL(imgInput.files[0]);
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

    function clampPercent(value) {
        return Math.max(0, Math.min(100, value));
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

    function applyDragPosition(e) {
        if (!hotspotDrag.active || !hotspotDrag.dot) {
            return;
        }
        var img = $('#media_hotspot_image')[0];
        if (!img) {
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

        $('#media_hotspot_image').on('click', function(e) {
            if (!$('#media_hotspot_base').val()) {
                $.notify('{{ __('Please select a base image first.') }}', 'warning');
                return;
            }

            var rect = this.getBoundingClientRect();
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
            item.remove();
            refreshHotspotNumbers();
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

    let html = `<img src="{{ empty($data->photo) ? asset('assets/images/noimage.png') : asset('assets/images/products/'.$data->photo) }}" alt="">`;
    $(".span4.cropme").html(html);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

  });


  $('.ok').on('click', function () {

 setTimeout(
    function() {


  	var img = $('#feature_photo').val();

      $.ajax({
        url: "{{route('admin-prod-upload-update',$data->id)}}",
        type: "POST",
        data: {"image":img},
        success: function (data) {
          if (data.status) {
            $('#feature_photo').val(data.file_name);
          }
          if ((data.errors)) {
            for(var error in data.errors)
            {
              $.notify(data.errors[error], "danger");
            }
          }
        }
      });

    }, 1000);



    });

})(jQuery);

  </script>

@include('partials.admin.product.product-scripts')
@endsection