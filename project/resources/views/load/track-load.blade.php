@extends('layouts.front')

@section('content')
<div class="container py-5">

    {{-- Page Title --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold">Order Tracking</h2>
        <p class="text-muted">Track the status of your order in real time</p>
    </div>

    {{-- AUTH CHECK --}}
    @if (!Auth::check())

        {{-- 🔒 Unauthorized Placeholder --}}
        <div class="card shadow-sm border-0 mx-auto" style="max-width: 520px;">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none">
                        <path d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5Z" stroke="#D30000" stroke-width="2"/>
                        <path d="M4 22c0-4.418 3.582-8 8-8s8 3.582 8 8" stroke="#D30000" stroke-width="2"/>
                    </svg>
                </div>

                <h4 class="mb-2">Please login to track your order</h4>
                <p class="text-muted mb-4">
                    Order tracking is available only for registered users.
                    Login to view your order status and shipment updates.
                </p>

                <a href="{{ route('user.login') }}" class="btn btn-danger px-4">
                    Login to Continue
                </a>
            </div>
        </div>

    @else

        {{-- ✅ AUTHORIZED USER --}}
        @if (isset($order))

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <ul class="stepprogress">
                        @foreach ($order->tracks as $track)
                            <li class="stepprogress-item is-done mb-3">
                                <strong class="fs-5 d-block mb-1">
                                    {{ ucwords($track->title) }}
                                </strong>

                                <div class="track-date text-muted mb-1">
                                    {{ date('d M Y', strtotime($track->created_at)) }}
                                </div>

                                <div class="track-text">
                                    {{ $track->text }}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>

        @else

            {{-- 🧾 Logged in but invalid order --}}
            <div class="text-center py-5">
                <h4>No order found</h4>
                <p class="text-muted">
                    Please check your order number or try again later.
                </p>
                <a href="{{ route('front.index') }}" class="btn btn-outline-secondary">
                    Go to Home
                </a>
            </div>

        @endif

    @endif

</div>
@endsection
