@extends('layouts.admin')

@section('content')
    <input type="hidden" id="headerdata" value="{{ __('Category Sliders') }}">
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ __('Category Sliders') }}
                        <a class="add-btn" href="{{ route('category-sliders.create') }}">
                            <i class="fas fa-plus"></i> {{ __('Add New Slider') }}
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
                    </ul>
                </div>
            </div>
        </div>

        <div class="product-area">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mr-table allproduct">
                        @include('alerts.admin.form-success')
                        <div class="table-responsive">
                            <table class="table table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Sort Order') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sliders as $slider)
                                        <tr>
                                            <td>
                                                <img src="{{ $slider->photo ? asset('assets/images/category-sliders/' . $slider->photo) : asset('assets/images/noimage.png') }}"
                                                    alt="Slider" style="width: 80px; height: auto;">
                                            </td>
                                            <td>{{ optional($slider->category)->name ?? __('All Categories') }}</td>
                                            <td>{{ $slider->title ?? '-' }}</td>
                                            <td>{{ $slider->sort_order }}</td>
                                            <td>
                                                @if ($slider->status == 1)
                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-list">
                                                    <a class="edit" href="{{ route('category-sliders.edit', $slider->id) }}">
                                                        <i class="fas fa-edit"></i>{{ __('Edit') }}
                                                    </a>
                                                    <a class="status" href="{{ url('admin/category-sliders/status/' . $slider->id . '/' . ($slider->status ? 0 : 1)) }}">
                                                        <i class="fas fa-toggle-{{ $slider->status ? 'off' : 'on' }}"></i>
                                                        {{ $slider->status ? __('Deactivate') : __('Activate') }}
                                                    </a>
                                                    <a href="javascript:;" data-href="{{ route('category-sliders.destroy', $slider->id) }}"
                                                       data-toggle="modal" data-target="#confirm-delete" class="delete">
                                                        <i class="fas fa-trash-alt"></i>{{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('No category sliders found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-block text-center">
                    <h4 class="modal-title d-inline-block">{{ __('Confirm Delete') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">{{ __('You are about to delete this slider.') }}</p>
                    <p class="text-center">{{ __('Do you want to proceed?') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="" class="d-inline delete-form" method="POST">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- DELETE MODAL ENDS --}}
@endsection
