@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- ✅ Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ✅ Gate Pass Header Card --}}
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-text"></i> Gate Pass #{{ $gatePass->gp_id }}
            </h4>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>PO Number:</strong> <span class="badge badge-secondary">{{ $gatePass->po_id }}</span></p>
                    <p><strong>Delivery Person:</strong> {{ $gatePass->delivery_person }}</p>
                    <p><strong>CNIC:</strong> {{ $gatePass->cnic }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Contact No:</strong> {{ $gatePass->contact_no }}</p>
                    <p><strong>Vehicle No:</strong> {{ $gatePass->vehicle_no }}</p>
                    <p><strong>Remarks:</strong> {{ $gatePass->remarks ?? 'N/A' }}</p>
                </div>
            </div>

            <hr>

            {{-- ✅ Items Table --}}
            <h5 class="mt-4 mb-3"><i class="bi bi-box-seam"></i> Delivered Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle shadow-sm">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Item ID</th>
                            <th>Quantity</th>
                            <th>Serial Numbers</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gatePass->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $item->item_id }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td>
                                    @foreach(explode(',', $item->serial_number) as $serial)
                                        <span class="badge badge-info">{{ trim($serial) }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if(strtolower($item->inspected_flag) === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif(strtolower($item->inspected_flag) === 'pending')
                                        <span class="badge badge-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($item->inspected_flag ?? 'N/A') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="bi bi-exclamation-circle"></i> No items found for this gate pass.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Back Button --}}
            <div class="text-right mt-3">
                <a href="{{ route('gatepass.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
