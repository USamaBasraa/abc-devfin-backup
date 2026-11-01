@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<style>
    .store-qr-page { background: transparent; }
    .store-qr-page .page-head {
        background: #fff !important;
        background-image: none !important;
        color: #17675F !important;
        border-bottom: 1px solid #e4eeec;
        padding: 18px 20px 12px;
    }
    .store-qr-page .page-title { color: #17675F !important; font-weight: 700; }
    .store-qr-page .total-chip,
    .store-qr-page .group-count {
        background: #e7f3f1;
        color: #17675F;
        border: 1px solid #c5ddd8;
        border-radius: 999px;
        font-weight: 600;
    }
    .store-qr-page .content-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e4eeec;
        box-shadow: 0 8px 24px rgba(23, 103, 95, .06);
        overflow: hidden;
    }
    .store-qr-page .filter-bar {
        background: #f7fbfa;
        border: 1px solid #d5e6e3;
        border-radius: 10px;
    }
    .store-qr-page .filter-bar label { color: #17675F; }
    .store-qr-page .form-control {
        border: 1px solid #d5e6e3;
        border-radius: 10px;
        background: #fff;
    }
    .store-qr-page .form-control:focus {
        border-color: #17675F;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, .15);
    }
    .store-qr-page .btn-primary {
        background: #17675F !important;
        border-color: #17675F !important;
        border-radius: 999px;
        font-weight: 600;
    }
    .store-qr-page .btn-primary:hover { background: #0f4d47 !important; border-color: #0f4d47 !important; }
    .store-qr-page .btn-outline-secondary {
        color: #17675F;
        border-color: #17675F;
        border-radius: 999px;
        background: #fff;
    }
    .store-qr-page .group-head h5 { color: #17675F; }
    .store-qr-page .custom-table thead th {
        background: #17675F !important;
        color: #fff !important;
        border: none !important;
        font-size: 12px;
        letter-spacing: .3px;
    }
    .store-qr-page .custom-table td { vertical-align: middle; border-color: #e4eeec; }
    .store-qr-page .custom-table tbody tr:hover { background: #f4faf9; }
    .store-qr-page .text-primary { color: #17675F !important; }
</style>

<div class="container-fluid py-4 store-qr-page">
    <div class="content-card">
        <div class="page-head d-flex flex-wrap justify-content-between align-items-center">
            <h4 class="page-title mb-2 mb-md-0">Store Items Quotation Requests</h4>
            <span class="total-chip px-3 py-2">Total: {{ $quotations->count() }}</span>
        </div>

        <div class="card-body p-4">
            <!-- Filter Bar -->
            <div class="filter-bar p-3 mb-4">
                <form method="GET" action="{{ route('store-items.quotation.request.create') }}" class="form-inline align-items-center">
                    <label class="font-weight-bold mr-2 text-dark" for="financial_year">
                        <i class="fa fa-filter mr-1 text-primary"></i> Financial Year:
                    </label>
                    <select name="financial_year" id="financial_year" class="form-control mr-2 mb-2 mb-sm-0" onchange="this.form.submit()" style="min-width: 230px;">
                        <option value="">-- All Financial Years --</option>
                        @foreach($academic_years as $year)
                            <option value="{{ $year->ay_id }}" {{ (string)$selectedYearId === (string)$year->ay_id ? 'selected' : '' }}>
                                {{ $year->academic_year ?? $year->year_title ?? $year->ay_name ?? ('Year #' . $year->ay_id) }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2 mb-sm-0">
                        <i class="fa fa-search mr-1"></i> Filter
                    </button>

                    @if(!empty($selectedYearId))
                        <a href="{{ route('store-items.quotation.request.create') }}" class="btn btn-outline-secondary btn-sm mb-2 mb-sm-0">
                            <i class="fa fa-times mr-1"></i> Clear Filter
                        </a>
                    @endif
                </form>
            </div>

            @php
                $groupedQuotations = $quotations->groupBy(function($q) {
                    return $q->storeBudgetHead->head_title
                        ?? $q->storeBudgetItem->fin_acc01_title
                        ?? 'Uncategorized';
                });
            @endphp

            @forelse ($groupedQuotations as $category => $items)
                <div class="mb-4">
                    <div class="group-head px-1 py-2 mb-2 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">{{ $category }}</h5>
                        <span class="group-count px-3 py-2">
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
