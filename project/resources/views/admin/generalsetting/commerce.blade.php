@extends('layouts.admin')

@section('content')

<div class="content-area">
    <div class="mr-breadcrumb">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="heading">{{ __('Commerce Settings') }}</h4>
                <ul class="links">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                    </li>
                    <li>
                        <a href="javascript:;">{{ __('General Settings') }}</a>
                    </li>
                    <li>
                        <a href="javascript:;">{{ __('Commerce Settings') }}</a>
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
                        <div class="gocover"
                            style="background: url({{ asset('assets/images/' . $gs->admin_loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                        </div>

                        @include('alerts.admin.form-both')

                        @if (!$schemaReady)
                            <div class="alert alert-warning">
                                {{ __('Commerce settings are not available yet. Please run migrations to create the commerce_settings table.') }}
                            </div>
                        @endif

                        <form id="geniusform" action="{{ route('admin-commerce-settings-update') }}" method="POST">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Mode') }}</h4>
                                        <p class="sub-heading">{{ __('Controls overall store ordering behavior.') }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <select class="input-field" name="mode" {{ !$schemaReady ? 'disabled' : '' }}>
                                        @php
                                            $currentMode = $setting ? $setting->mode : ($state['mode'] ?? 'enabled');
                                        @endphp
                                        <option value="enabled" {{ $currentMode === 'enabled' ? 'selected' : '' }}>
                                            {{ __('Enabled') }}
                                        </option>
                                        <option value="disabled" {{ $currentMode === 'disabled' ? 'selected' : '' }}>
                                            {{ __('Disabled') }}
                                        </option>
                                        <option value="whatsapp_only" {{ $currentMode === 'whatsapp_only' ? 'selected' : '' }}>
                                            {{ __('WhatsApp Only') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <h4 class="text-center">{{ __('Capabilities') }}</h4>
                            <p class="text-center text-muted mb-4">{{ __('These are enforced server-side and updated live on frontend via SSE.') }}</p>

                            @php
                                $flags = $setting
                                    ? [
                                        'cart_enabled' => (int) $setting->cart_enabled,
                                        'checkout_enabled' => (int) $setting->checkout_enabled,
                                        'orders_enabled' => (int) $setting->orders_enabled,
                                        'wallet_enabled' => (int) $setting->wallet_enabled,
                                        'order_tracking_enabled' => (int) $setting->order_tracking_enabled,
                                    ]
                                    : ($state['flags'] ?? []);
                            @endphp

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Add To Cart') }}</h4>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="cart_enabled" value="1" {{ !empty($flags['cart_enabled']) ? 'checked' : '' }} {{ !$schemaReady ? 'disabled' : '' }}>
                                        <span>{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Checkout') }}</h4>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="checkout_enabled" value="1" {{ !empty($flags['checkout_enabled']) ? 'checked' : '' }} {{ !$schemaReady ? 'disabled' : '' }}>
                                        <span>{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Orders (Placement)') }}</h4>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="orders_enabled" value="1" {{ !empty($flags['orders_enabled']) ? 'checked' : '' }} {{ !$schemaReady ? 'disabled' : '' }}>
                                        <span>{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Wallet Usage') }}</h4>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="wallet_enabled" value="1" {{ !empty($flags['wallet_enabled']) ? 'checked' : '' }} {{ !$schemaReady ? 'disabled' : '' }}>
                                        <span>{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('Order Tracking') }}</h4>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="order_tracking_enabled" value="1" {{ !empty($flags['order_tracking_enabled']) ? 'checked' : '' }} {{ !$schemaReady ? 'disabled' : '' }}>
                                        <span>{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <h4 class="text-center">{{ __('WhatsApp Only Mode') }}</h4>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('WhatsApp Number') }}</h4>
                                        <p class="sub-heading">{{ __('Example: +15551234567') }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="input-field" name="whatsapp_number"
                                        value="{{ $setting ? $setting->whatsapp_number : ($state['whatsapp']['number'] ?? '') }}"
                                        placeholder="+15551234567" {{ !$schemaReady ? 'disabled' : '' }}>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area">
                                        <h4 class="heading">{{ __('WhatsApp Message Template') }}</h4>
                                        <p class="sub-heading">{{ __('Used by frontend when in WhatsApp Only mode.') }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <textarea class="input-field" name="whatsapp_message_template" rows="5" {{ !$schemaReady ? 'disabled' : '' }}>{{ $setting ? $setting->whatsapp_message_template : ($state['whatsapp']['template'] ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-lg-3">
                                    <div class="left-area"></div>
                                </div>
                                <div class="col-lg-6">
                                    <button class="addProductSubmit-btn" type="submit" {{ !$schemaReady ? 'disabled' : '' }}>
                                        {{ __('Save') }}
                                    </button>
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

