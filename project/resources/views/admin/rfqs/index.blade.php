{{-- resources/views/admin/rfqs/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mr-breadcrumb">
        <h4 class="heading">RFQ Requests</h4>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- ✅ RESPONSIVE WRAPPER --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rfqs as $rfq)
                            <tr>
                                <td>{{ $rfq->id }}</td>
                                <td>
                                    <strong>{{ $rfq->product_name }}</strong><br>
                                    <small>{{ $rfq->sku }}</small>
                                </td>
                                <td>{{ $rfq->first_name }} {{ $rfq->last_name }}</td>
                                <td>{{ $rfq->email }}</td>
                                <td>
                                    @include('admin.rfqs._status_badge', ['status' => $rfq->status])
                                </td>
                                <td>{{ $rfq->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.rfqs.show', $rfq->id) }}"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No RFQs found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $rfqs->links() }}

        </div>
    </div>
</div>
@endsection
