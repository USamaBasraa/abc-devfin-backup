@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Gate Pass Management</h4>
    <hr>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="gatePassTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="create-tab" data-toggle="tab" href="#create" role="tab">Create Gate Pass</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="created-tab" data-toggle="tab" href="#created" role="tab">Created Gate Passes</a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="gatePassTabsContent">
        <!-- Create Gate Pass Tab -->
        <div class="tab-pane fade show active" id="create" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>PO ID</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Delevery Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr>
                                {{-- @dd($po) --}}
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $po->po_id }}</td>
                                <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                                <td>{{ $po->delivery_status ?? 'Pending'}}</td>
                                {{-- <td>{{ number_format($po->total_price, 2) }} {{ $po->currency }}</td> --}}
                                <td><span class="badge badge-success">Approved</span></td>
                                <td>
                                    <a href="{{ route('gatepass.create', $po->po_id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-box-arrow-in-right"></i> Create Gate Pass
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No approved purchase orders available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Created Gate Passes Tab -->
        <div class="tab-pane fade" id="created" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>PO ID</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total Price</th>
                            <th>Gate Pass No</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($createdGatePasses as $po)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $po->po_id }}</td>
                                <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                                <td>{{ number_format($po->total_price, 2) }} {{ $po->currency }}</td>
                                <td>{{ $po->gatePass->gp_id ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('gatepass.show', $po->gatePass->gp_id) }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No created gate passes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

