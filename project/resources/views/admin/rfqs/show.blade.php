{{-- resources/views/admin/rfqs/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
<div class="mr-breadcrumb">
    <h4 class="heading">RFQ #{{ $rfq->id }}</h4>
</div>
    <div class="row">
        <!-- Left -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">

                    <h5>Customer Details</h5>
                    <p><strong>Name:</strong> {{ $rfq->first_name }} {{ $rfq->last_name }}</p>
                    <p><strong>Email:</strong> {{ $rfq->email }}</p>
                    <p><strong>Phone:</strong> {{ $rfq->phone }}</p>
                    <p><strong>Company:</strong> {{ $rfq->company_name }}</p>
                    <p><strong>Country:</strong> {{ $rfq->country }}</p>

                    <hr>

                    <h5>RFQ Details</h5>
                    <p><strong>Product:</strong> {{ $rfq->product_name }}</p>
                    <p><strong>SKU:</strong> {{ $rfq->sku }}</p>
                    <p><strong>Product Type:</strong> {{ $rfq->product_type }}</p>
                    <p><strong>Budget:</strong> {{ $rfq->estimate_budget }}</p>
                    <p><strong>Message:</strong><br>{{ $rfq->message }}</p>

                    @if($rfq->attachment)
                        <p>
                            <strong>Attachment:</strong>
                            <a href="{{ asset('storage/' . $rfq->attachment) }}" target="_blank">
                                Download
                            </a>
                        </p>
                    @endif

                </div>
            </div>
        </div>

        <!-- Right -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">

                    <h5>Status</h5>

                    <form method="POST" action="{{ route('admin.rfqs.status', $rfq->id) }}">
                        @csrf

                        <select name="status" class="form-control mb-3">
                            @foreach(['new','contacted','quoted','closed'] as $status)
                                <option value="{{ $status }}"
                                    {{ $rfq->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>

                        <button class="btn btn-success btn-block">
                            Update Status
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
