@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header" style="background: none !important; color: #252525 !important;">
            <h4 class="mb-0">Store Items Quotation Requests</h4>
        </div>

        <div class="card-body p-4">
            @php
                $groupedQuotations = $quotations->groupBy(function($q) {
                    return $q->storeBudgetHead->head_title
                        ?? $q->storeBudgetItem->fin_acc01_title
                        ?? 'Uncategorized';
                });
            @endphp

            @forelse ($groupedQuotations as $category => $items)
                <div class="mb-4">
                    <div class="px-4 py-3 mb-3 rounded d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">{{ $category }}</h5>
                        <span class="badge badge-info border px-3 py-2">
                            {{ $items->count() }} {{ Str::plural('Item', $items->count()) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-5 custom-table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Serial</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Item Title</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Purchase Mode</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Remarks</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Quantity</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Head Approval</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Date</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Budget Type</th>
                                    <th class="font-weight-bold text-uppercase text-center text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $quotation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                {{ $quotation->storeBudgetItem->item_title ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ ucfirst($quotation->storeBudgetItem->purchase_mode ?? '-') }}</td>
                                        <td><small>{{ Str::limit($quotation->storeBudgetItem->remarks ?? '-', 50) }}</small></td>
                                        <td>{{ $quotation->requested_quantity ?? $quotation->storeBudgetItem->quantity ?? '-' }}</td>
                                        <td>
                                            @if($quotation->department_head_approval == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($quotation->department_head_approval == 'rejected')
                                                <span class="badge badge-danger"><i class="fa fa-times mr-1"></i>Rejected</span>
                                            @else
                                                <span class="badge badge-warning"><i class="fa fa-clock-o mr-1"></i>Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($quotation->created_at)->format('M d, Y') }}</td>
                                        <td>{{ ucfirst($quotation->storeBudgetItem->budget_type ?? '-') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('store-items.quotation.related.view', $quotation->qr_id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                Process
                                                @if($quotation->vendorQuotations->count() > 0)
                                                    <span class="text-white ml-1">({{ $quotation->vendorQuotations->count() }})</span>
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No store items quotation requests yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
