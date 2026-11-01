@extends('layouts.app')
{{-- @dd("working"); --}}
@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        {{-- Header --}}
        <div class="border-0 py-3" style="padding: 20px !important;color: #000 !important;">
            <div class="d-flex align-items-center w-100">
                <div>
                    <h4 class="mb-0 font-weight-bold">
                        {{ $quotation->budgetItem->item_title ?? 'N/A' }} (#{{ $quotation->qr_id ?? '-' }})
                    </h4>
                </div>
                <div class="ml-auto d-flex align-items-center">
            @php
                $po = \App\Models\StoreProcurement\PurchaseOrder::where('qr_id', $quotation->qr_id)->first();
                $hasVendorResponses = $quotation->vendorQuotations->isNotEmpty();
            @endphp
            @if(!$po)
                        <span class="badge badge-warning px-3 py-2 mr-2" style="font-size: 14px;">
                            Pending Order
                        </span>
            @elseif(strtolower($po->po_status ?? '') === 'generated' && !empty($po->po_no))
                        <a href="{{ url('purchase-orders/' . $po->po_id) }}"
                           target="_blank"
                           class="btn btn-sm btn-success mr-2">
                            <i class="fa fa-external-link mr-1"></i>View PO: {{ $po->po_no }}
                        </a>
            @else
                        <span class="badge badge-info px-3 py-2 mr-2" style="font-size: 14px;">
                            PO Pending Generation
                        </span>
            @endif
                        @if($hasVendorResponses)
                        @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @php
                $selectedPO = \App\Models\StoreProcurement\PurchaseOrder::where('qr_id', $quotation->qr_id)->first();
                $displayBudgetType = $selectedPO && $selectedPO->budget_type
                    ? $selectedPO->budget_type
                    : ($quotation->budgetItem->budget_type ? ucfirst($quotation->budgetItem->budget_type) : '-');
                $groupedQuotations = $quotation->vendorQuotations->groupBy('vendor_id');
            @endphp
            {{-- Item Details Section --}}
            <div class="mb-4">
                <div class="bg-light px-4 py-3 mb-3 rounded" style="background: #4555C3 !important;color: #fff !important;border-radius: 0 !important;">
                    <h5 class="mb-0 font-weight-bold">
                        Item Information
                    </h5>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Item Name</label>
                        <div class="font-weight-bold">{{ $quotation->budgetItem->item_title ?? '-' }}</div>
                    </div>
                      <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Model</label>
                        <div class="font-weight-bold">{{ $quotation->budgetItem->model ?? '-' }}</div>
                    </div>
                      <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Brand</label>
                        <div class="font-weight-bold">{{ $quotation->budgetItem->brand ?? '-' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Category</label>
                        <div class="font-weight-bold">{{ $quotation->budgetHead->head_title ?? '-' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Quantity</label>
                        <div class="font-weight-bold">{{ $quotation->budgetItem->quantity ?? '-' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Budget Type</label>
                        <div class="font-weight-bold">{{ $displayBudgetType }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="d-block text-muted mb-1">Purchase Mode</label>
                        <div class="font-weight-bold">{{ ucfirst($quotation->budgetItem->purchase_mode ?? '-') }}</div>
                    </div>
                </div>
                @if($quotation->budgetItem->remarks)
                    <div class="mt-2">
                        <label class="d-block text-muted mb-1">Remarks</label>
                        <div>{{ $quotation->budgetItem->remarks }}</div>
                    </div>
                @endif
            </div>

            {{-- Vendor Quotations Section --}}
            <div class="mb-4">
                <div class="bg-light px-4 py-3 mb-3 rounded d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 font-weight-bold">
                        Vendor Quotations
                    </h5>
                    @if($hasVendorResponses)
                        <button type="button"
                                class="btn btn-md text-white"
                                style="background-color: #4555C3; padding: 8px 18px; font-size: 14px;"
                                onclick="openComparativeReportModal('{{ route('quotation.comparative-report', ['id' => $quotation->qr_id]) }}')">
                            Comparative Report
                        </button>
                    @endif
                </div>

                @php
                    $budgetItem = $quotation->budgetItem;
                    $vcAlertText = null;
                    $vcAlertClass = 'alert-info';
                    $vcAlertRemark = null;
                    $vcAlertShow = false;
                    $vcSuccessText = null;
                    $reasonSubmitted = null;
                    if ($budgetItem) {
                        $typeLabel = trim($budgetItem->requested_budget_type ?? '') ?: 'this request';
                        $statusRaw = strtoupper(trim($budgetItem->reappropriation_approval ?? ''));


                        $budgetFlagRaw = trim($budgetItem->vc_budget_update_flag ?? '');
                        $budgetUpdated = in_array(strtoupper($budgetFlagRaw), ['UPDATED', 'YES', '1','APPROVED']);
                        $vcRemarks = $budgetItem->reappropriation_approval_remarks ?? null;
                        $reasonSubmitted = $budgetItem->reason_for_approval ?? null;

                        if ($statusRaw === 'PENDING') {
                            $vcAlertText = 'Awaiting VC approval for ' . $typeLabel . '.';
                            $vcAlertRemark = $vcRemarks;
                            $vcAlertClass = 'alert-warning';
                            $vcAlertShow = true;
                        } elseif ($statusRaw === 'APPROVED') {
                            // if ($budgetUpdated) {
                                $vcSuccessText = 'VC approval and budget update complete for ' . $typeLabel . '. You can now submit the recommendation.';
                                $vcAlertRemark = $vcRemarks;
                            // } else {
                            //     $vcAlertText = 'VC approved ' . $typeLabel . '. Please complete the budget update before submitting.';
                            //     $vcAlertRemark = $vcRemarks;
                            //     $vcAlertClass = 'alert-warning';
                            //     $vcAlertShow = true;
                            // }
                        } elseif ($statusRaw === 'DECLINED') {
                            $vcAlertText = 'VC declined the ' . $typeLabel . ' request. Please review the remarks below.';
                            $vcAlertClass = 'alert-danger';
                            $vcAlertRemark = $vcRemarks;
                            $vcAlertShow = true;
                        }
                    }
                @endphp

                @if($vcSuccessText)
                    <div class="alert alert-success mb-3">
                        <strong>{{ $vcSuccessText }}</strong>
                        @if(!empty($vcAlertRemark))
                            <div class="mt-1 mb-0">VC remarks: {{ $vcAlertRemark }}</div>
                        @endif
                        @if(isset($budgetUpdated) && $budgetUpdated)
                            <div class="mt-1 text-muted">After VC approval, budget update is completed.</div>
                        @endif
                        @if(!empty($reasonSubmitted))
                            <div class="mt-1 mb-0">Submitted reason: {{ $reasonSubmitted }}</div>
                        @endif
                    </div>
                @endif

                @if($vcAlertShow && $vcAlertText)
                    <div class="alert {{ $vcAlertClass }} mb-3">
                        <strong>{{ $vcAlertText }}</strong>
                        @if(!empty($vcAlertRemark))
                            <div class="mt-1 mb-0">VC remarks: {{ $vcAlertRemark }}</div>
                        @endif
                        @if(isset($budgetItem) && ($budgetFlagRaw !== ''))
                            <div class="mt-1 text-muted">
                                @if($budgetUpdated)
                                    After VC approval, budget update is completed.
                                @else
                                    After VC approval, budget update is required.
                                @endif
                            </div>
                        @endif
                        @if(!empty($reasonSubmitted))
                            <div class="mt-1 mb-0">Submitted reason: {{ $reasonSubmitted }}</div>
                        @endif
                    </div>
                @endif

                @forelse ($groupedQuotations as $vendorId => $vendorQuotations)
                    @php
                        $firstQuotation = $vendorQuotations->first();
                        $vendor = $firstQuotation ? $firstQuotation->vendor : null;
                        $vendorName = $vendor->vendor_name ?? ($vendorId ? 'Vendor #' . $vendorId : 'Unknown Vendor');
                        $quotationCount = $vendorQuotations->count();
                        $collapseId = 'vendorCollapse' . $vendorId;

                        // Simple check - Is this vendor selected?
                        $vendorColor = 'bg-primary';
                        if ($selectedPO && $selectedPO->vendor_id == $vendorId) {
                            $poStatus = strtolower($selectedPO->po_status ?? '');
                            if ($poStatus === 'generated') {
                                $vendorColor = 'bg-success';
                            } elseif ($poStatus) {
                                $vendorColor = 'bg-warning text-dark';
                            } else {
                                $vendorColor = 'bg-primary';
                            }
                        }
                    @endphp

                    <div class="mb-3">
                        {{-- Vendor Header --}}
                        <div class="{{ $vendorColor }} text-white p-3 d-flex justify-content-between align-items-center"
                             style="cursor: pointer;"
                             data-toggle="collapse"
                             data-target="#{{ $collapseId }}">
                            <div>
                                <strong>{{ $vendorName }}</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light mr-2 text-dark">{{ $quotationCount }} Quotation{{ $quotationCount > 1 ? 's' : '' }}</span>
                                <i class="fa fa-chevron-down"></i>
                            </div>
                        </div>

                        {{-- Vendor Quotations Details (Collapsible) --}}
                        <div id="{{ $collapseId }}" class="collapse">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr style="font-size: 12px;">
                                            <th>#</th>
                                            <th>Version</th>
                                            <th>Item Title</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Unit Rate</th>
                                            <th class="text-right">Price</th>
                                            <th>Currency</th>
                                            <th>Advance Payment</th>
                                            <th class="text-right">Advance %</th>
                                            <th>Delivery</th>
                                            <th>Warranty</th>
                                            <th>Specifications</th>
                                            <th>Attachment</th>
                                            <th>Recommendation</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendorQuotations as $q)
                                            @php
                                                // Simple check - Is this version selected?
                                                $rowColor = '';
                                                if ($selectedPO && strtolower($selectedPO->po_status ?? '') === 'generated') {
                                                    if ($selectedPO->vendor_quotation_id == $q->vq_id) {
                                                        $rowColor = 'background-color: #c8e6c9 !important;';
                                                    }
                                                }
                                                $itemTitle = $q->item_title ?? '-';
                                                $brand = $q->brand ?? '-';
                                                $model = $q->model ?? '-';
                                                $quantityValue = $q->quantity;
                                                $rateValue = $q->rate;
                                                $priceValue = $q->price;
                                                $advanceRequired = ucfirst($q->advance_required ?? 'no');
                                                $advancePercentValue = $q->advance_percent;
                                                $createdAtFormatted = optional($q->created_at)->format('M d, Y') ?? '-';
                                                $latestRec = $q->recommendations->sortByDesc('created_at')->first();
                                                $rowVendorId = $q->vendor->vendor_id ?? $q->vendor_id ?? '';
                                                $rowVendorName = $q->vendor->vendor_name ?? $vendorName;
                                            @endphp
                                            <tr style="{{ $rowColor }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge badge-info">Version {{ $loop->iteration }}</span>
                                                </td>
                                                <td>{{ $itemTitle }}</td>
                                                <td>{{ $brand }}</td>
                                                <td>{{ $model }}</td>
                                                <td class="text-right">
                                                    @if(!is_null($quantityValue))
                                                        {{ number_format($quantityValue, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if(!is_null($rateValue))
                                                        {{ number_format($rateValue, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <span class="font-weight-bold">
                                                        {{ !is_null($priceValue) ? number_format($priceValue, 2) : '-' }}
                                                    </span>
                                                </td>
                                                <td>{{ $q->currency ?? '-' }}</td>
                                                <td>{{ $advanceRequired }}</td>
                                                <td class="text-right">
                                                    @if(!is_null($advancePercentValue))
                                                        {{ number_format($advancePercentValue, 2) }}%
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $q->delivery_time ?? '-' }}</td>
                                                <td>{{ $q->warranty ?? '-' }}</td>
                                                <td style="max-width: 200px;">{{ Str::limit($q->specifications ?? '-', 60) }}</td>
                                                <td>
                                                    @if($q->attachment)
                                                        <a href="{{ ('/' . $q->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa fa-paperclip"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($latestRec)
                                                        <div class="text-muted">{{ $latestRec->department->dept_name ?? '-' }}</div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $createdAtFormatted }}</td>
                                    <td class="text-center">
                                        @if(empty($quotation->po_id) && !$latestRec && empty($approvalAlreadySubmitted))
                                            <button class="btn btn-sm btn-primary"
                                                onclick="openCombinedModal(
                                                    {{ $q->quotation_request_id }},
                                                    {{ $q->vq_id }},
                                                    '{{ addslashes($rowVendorId) }}',
                                                    '{{ addslashes($rowVendorName) }}',
                                                    {{ $q->rate ?? 0 }},
                                                    '{{ addslashes($q->currency ?? '') }}',
                                                    '{{ addslashes($q->warranty ?? '') }}',
                                                    '{{ addslashes($q->delivery_time ?? '') }}',
                                                    '{{ number_format(($quotation->budgetItem->quantity * $quotation->budgetItem->rate) ?? 0, 2) }}',
                                                    '{{ $displayBudgetType }}',
                                                    false,
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '{{ addslashes($q->item_title ?? '') }}',
                                                    '{{ addslashes($q->brand ?? '') }}',
                                                    '{{ addslashes($q->model ?? '') }}',
                                                    '{{ $q->quantity ?? '' }}',
                                                    {{ $q->price ?? 0 }},
                                                    '{{ addslashes($q->advance_required ?? 'no') }}',
                                                    '{{ $q->advance_percent ?? '' }}',
                                                    '{{ addslashes($quotation->budgetItem->requested_budget_type ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reason_for_approval ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reappropriation_approval ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reappropriation_approval_remarks ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->vc_budget_update_flag ?? '') }}'
                                                )">
                                                <i class="fa fa-check mr-1"></i>Process
                                            </button>
                                        @elseif($latestRec)
                                            @php
                                                $po = $q->purchaseOrder;
                                            @endphp
                                            <button class="btn btn-sm btn-secondary"
                                                onclick="openCombinedModal(
                                                    {{ $q->quotation_request_id }},
                                                    {{ $q->vq_id }},
                                                    '{{ addslashes($rowVendorId) }}',
                                                    '{{ addslashes($rowVendorName) }}',
                                                    {{ $q->rate ?? 0 }},
                                                    '{{ addslashes($q->currency ?? '') }}',
                                                    '{{ addslashes($q->warranty ?? '') }}',
                                                    '{{ addslashes($q->delivery_time ?? '') }}',
                                                    '{{ number_format(($quotation->budgetItem->quantity * $quotation->budgetItem->rate) ?? 0, 2) }}',
                                                    '{{ $po->budget_type ?? $displayBudgetType }}',
                                                    true,
                                                    '{{ $latestRec->recommended_flag }}',
                                                    '{{ addslashes($latestRec->remarks ?? '') }}',
                                                    '{{ $po->quantity ?? '' }}',
                                                    '{{ $po->total_price ?? '' }}',
                                                    '{{ addslashes($po->remarks ?? '') }}',
                                                    '{{ addslashes($q->item_title ?? '') }}',
                                                    '{{ addslashes($q->brand ?? '') }}',
                                                    '{{ addslashes($q->model ?? '') }}',
                                                    '{{ $q->quantity ?? '' }}',
                                                    {{ $q->price ?? 0 }},
                                                    '{{ addslashes($q->advance_required ?? 'no') }}',
                                                    '{{ $q->advance_percent ?? '' }}',
                                                    '{{ addslashes($quotation->budgetItem->requested_budget_type ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reason_for_approval ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reappropriation_approval ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->reappropriation_approval_remarks ?? '') }}',
                                                    '{{ addslashes($quotation->budgetItem->vc_budget_update_flag ?? '') }}'
                                                )">
                                                <i class="fa fa-eye mr-1"></i>View
                            </button>
                        @elseif(!empty($approvalAlreadySubmitted))
                                            <span class="badge badge-secondary">Already Recommended</span>
                        @else
                                            <span class="badge badge-success">Completed</span>
                        @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                            <p class="mb-0">No vendor quotations available</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Recommendations Section - Hidden for now --}}
            <div style="display: none;">
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fa fa-star mr-2"></i>Recommendations History
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                <thead>
                            <tr class="border-bottom">
                                <th class="font-weight-bold">Department</th>
                                <th class="font-weight-bold">Status</th>
                                <th class="font-weight-bold">Remarks</th>
                                <th class="font-weight-bold">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotation->recommendations as $rec)
                        <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $rec->department->dept_name ?? '-' }}</div>
                                    </td>
                            <td>
                                @if($rec->recommended_flag == 'recommended')
                                    <span class="badge badge-success">Recommended</span>
                                @elseif($rec->recommended_flag == 'not_recommended')
                                    <span class="badge badge-danger">Not Recommended</span>
                                @else
                                    <span class="badge badge-warning">Conditional</span>
                                @endif
                            </td>
                            <td>{{ $rec->remarks ?? '-' }}</td>
                                    <td>
                                        <i class="fa fa-calendar-alt mr-1"></i>{{ $rec->created_at->format('M d, Y') }}
                                        <span class="mx-1">•</span>
                                        <i class="fa fa-clock mr-1"></i>{{ $rec->created_at->format('h:i A') }}
                                    </td>
                        </tr>
                    @empty
                        <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                            <p class="mb-0">No recommendations yet</p>
                                        </div>
                                    </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Comparative Report Modal --}}
<div class="modal fade" id="comparativeReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width: 92vw; width: 92vw;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3" style="background-color: #4555C3;">
                <h5 class="modal-title font-weight-bold" style="color: #FFFFFF;">
                    <i class="fa fa-bar-chart mr-2"></i>Comparative Report
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="comparativeReportContent">
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                    <p class="mb-0">Loading comparative report...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-sm btn-primary d-none" id="printComparativeReportBtn" onclick="printComparativeReport()">
                    <i class="fa fa-print mr-1"></i>Print / PDF
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Combined Modal - Create/View Mode --}}
<div class="modal fade" id="combinedModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 60%; width: 60%;">
        <div class="modal-content border-0 shadow-lg">
            <form id="combinedForm">
            @csrf
                <input type="hidden" name="qr_id" id="combQrId" value="{{ $quotation->qr_id }}">
                <input type="hidden" name="vq_id" id="combVqId">
                <input type="hidden" name="vendor_id" id="combVendorId">
                <input type="hidden" name="unit_price" id="combUnitPrice">
                <input type="hidden" name="total_price" id="combTotalPrice">
                <input type="hidden" name="budget_type" id="combBudgetType">
                <input type="hidden" name="currency" id="combCurrency">

                <div class="modal-header border-bottom py-3" style="background-color: #4555C3; color: #FFFFFF;">
                    <h5 class="modal-title font-weight-bold mb-0" id="modalTitle" style="color: #FFFFFF;">
                        <i class="fa fa-check-circle mr-2"></i>Approve & Create Purchase Order
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- Vendor Info --}}
                    <div class="bg-light p-3 rounded mb-4">
                        <label class="d-block text-muted mb-1">Selected Vendor</label>
                        <div class="font-weight-bold h5 mb-0">
                            <i class="fa fa-building mr-2"></i><span id="combVendorName"></span>
                        </div>
                    </div>

                    {{-- Quotation Summary --}}
                    <div class="mb-4">
                        <div class="bg-light px-4 py-3 mb-3 rounded">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fa fa-clipboard-list mr-2"></i>Quotation Summary
                            </h5>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Item Title</label>
                                <div class="font-weight-bold" id="combItemTitle">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Brand</label>
                                <div class="font-weight-bold" id="combBrand">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Model</label>
                                <div class="font-weight-bold" id="combModel">-</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1"> Quantity</label>
                                <div class="font-weight-bold" id="combVendorQuantity">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Unit Price</label>
                                <div class="font-weight-bold" id="combVendorUnitRate">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Grand Total</label>
                                <div class="font-weight-bold" id="combVendorTotal">-</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Advance Payment</label>
                                <div class="font-weight-bold" id="combAdvanceRequired">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Advance %</label>
                                <div class="font-weight-bold" id="combAdvancePercent">-</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Currency</label>
                                <div class="font-weight-bold" id="combCurrencyDisplay">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Recommendation Section --}}
                    <div class="mb-4">
                        <div class="bg-light px-4 py-3 mb-3 rounded">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fa fa-star mr-2"></i>Recommendation
                            </h5>
                        </div>
                    <div class="form-group">
                            {{-- <label class="font-weight-bold">Status <span class="text-danger req-indicator">*</span></label> --}}
                            <select class="form-control" name="recommended_flag" id="recStatus" required>
                                <option value="">Select status</option>
                            <option value="recommended">Recommended</option>
                            {{-- <option value="not_recommended">Not Recommended</option>
                            <option value="conditional">Conditional</option> --}}
                        </select>
                    </div>
                    <div class="form-group">
                            <label class="font-weight-bold">Remarks</label>
                            <textarea name="rec_remarks" class="form-control" id="recRemarks" rows="2" placeholder="Optional" maxlength="2000"></textarea>
                            <small class="text-muted float-right"><span id="recRemarksCount">0</span>/2000</small>
                        </div>
                    </div>

                    {{-- Purchase Order Section --}}
                    <div class="mb-3">
                        <div class="bg-light px-4 py-3 mb-3 rounded" id="poSectionTitle">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fa fa-shopping-cart mr-2"></i>Purchase Order Details
                            </h5>
                        </div>

                        {{-- Price Info --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Unit Rate</label>
                                <div class="font-weight-bold h5"><span id="combUnitPriceDisplay"></span></div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Available Budget</label>
                                <div class="font-weight-bold h5"><span id="combBudgetDisplay"></span></div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-muted mb-1">Budget Type</label>
                                <div class="font-weight-bold h5"><span id="combBudgetTypeDisplay">-</span></div>
                            </div>
                        </div>

                        {{-- Form Fields --}}
                        <div class="row">
                            <div class="col-md-4">
                        <div class="form-group" id="quantityField">
                            <label class="font-weight-bold">Quantity <span class="text-danger req-indicator">*</span></label>
                            <input type="number" name="quantity" class="form-control" id="combQuantity" required min="1" placeholder="Enter quantity">
                        </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Total Price</label>
                                    <input type="text" class="form-control bg-light font-weight-bold" id="combTotalPriceDisplay" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="combBudgetFlag" class="alert alert-danger d-none">
                            <i class="fa fa-exclamation-triangle mr-2"></i>Warning: This order exceeds available budget!
                        </div>

                        <div id="vcApprovalSection" class="alert alert-warning d-none">
                        <div class="row align-items-center">
                                <div class="col-lg-5 col-md-6 mb-2 mb-md-0 d-flex align-items-start">
                                    <i class="fa fa-exclamation-circle mr-2 mt-1"></i>
                                    <div>
                                        <strong id="vcApprovalHeading">Budget exceeded.</strong>
                                        <div id="vcApprovalStatusMessage" class="small">Send request to VC for approval.</div>
                                        <div id="vcApprovalRemarksMessage" class="small text-muted"></div>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-6">
                                    <div class="form-row">
                                        <div class="col-sm-7 mb-2 mb-sm-0">
                                            <select class="form-control" id="vcApprovalReason">
                                                <option value="">-- Select Reason --</option>
                                                <option value="partial_reappropriate">Partial Re-appropriate</option>
                                                <option value="partial_unbudgeted">Partial un-Budgeted</option>
                                            </select>
                                            <small class="text-muted d-block mt-1" id="vcReasonExplanation"></small>
                                        </div>
                                        <div class="col-sm-5 text-sm-right">
                                            <button type="button" class="btn btn-sm btn-primary btn-block btn-sm-custom" id="sendVcApprovalBtn">
                                                <i class="fa fa-paper-plane mr-1"></i>Send Request
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="vcReasonDetailsWrapper" class="form-group d-none">
                            <label class="font-weight-bold">Explain Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="vcReasonDetails" rows="3" maxlength="2000" placeholder="Provide details for requesting VC approval (max 2000 characters)"></textarea>
                            <small class="text-muted float-right"><span id="vcReasonDetailsCount">0</span>/2000</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Warranty</label>
                                    <input type="text" name="warranty" class="form-control bg-light" id="combWarranty" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery Time</label>
                                    <input type="text" name="delivery_time" class="form-control bg-light" id="combDeliveryTime" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Additional Remarks</label>
                            <textarea name="po_remarks" class="form-control" id="combPoRemarks" rows="2" placeholder="Optional remarks" maxlength="2000"></textarea>
                            <small class="text-muted float-right"><span id="poRemarksCount">0</span>/2000</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i><span id="cancelBtnText">Cancel</span>
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary" id="submitBtn">
                        <i class="fa fa-check mr-1"></i>Submit
                    </button>
                </div>
            </form>
            </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>




function openComparativeReportModal(reportUrl) {
    if (!reportUrl) {
        return;
    }

    var loadingHtml = '<div class="p-5 text-center text-muted">'
        + '<i class="fa fa-spinner fa-spin fa-2x mb-3"></i>'
        + '<p class="mb-0">Loading comparative report...</p>'
        + '</div>';

    $('#printComparativeReportBtn').addClass('d-none');
    $('#comparativeReportContent').html(loadingHtml);
    $('#comparativeReportModal').modal('show');

    $.get(reportUrl, function (html) {
        $('#comparativeReportContent').html(html);
        $('#printComparativeReportBtn').removeClass('d-none');
    }).fail(function () {
        var errorHtml = '<div class="p-5 text-center text-danger">'
            + '<i class="fa fa-exclamation-triangle fa-2x mb-3"></i>'
            + '<p class="mb-0">Unable to load comparative report.</p>'
            + '</div>';

        $('#comparativeReportContent').html(errorHtml);
        $('#printComparativeReportBtn').addClass('d-none');
    });
}

function printComparativeReport() {
    var reportContent = $('#comparativeReportContent').html();

    if (!reportContent || reportContent.indexOf('Loading comparative report') !== -1) {
        showToast('warning', 'Comparative report is still loading.', 'Please Wait');
        return;
    }

    if (reportContent.indexOf('Unable to load comparative report') !== -1) {
        showToast('error', 'Comparative report could not be loaded.', 'Print Failed');
        return;
    }

    var printWindow = window.open('', '_blank', 'width=1200,height=900');

    if (!printWindow) {
        showToast('error', 'Allow pop-ups to print or save the report as PDF.', 'Popup Blocked');
        return;
    }

    var printHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Comparative Report</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <style>
                body {
                    background: #ffffff;
                    margin: 0;
                    padding: 20px;
                    color: #252525;
                    font-family: Arial, sans-serif;
                }
                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            ${reportContent}
        </body>
        </html>
    `;

    printWindow.document.open();
    printWindow.document.write(printHtml);
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}

let currentAvailableBudget = 0;
const vcTypeMap = {
    partial_reappropriate: 'Partial Re-appropriate',
    partial_unbudgeted: 'Partial un-Budgeted'
};

let vcState = {
    typeKey: '',
    typeLabel: '',
    status: '',
    details: '',
    remarks: '',
    budgetUpdated: '',
    hasRequest: false
};

const vcReasonExplanations = {
    partial_reappropriate: 'Select this when a portion of the required funds will be re-appropriated from another approved budget head.',
    partial_unbudgeted: 'Select this when the shortfall must be covered through unbudgeted funds that require VC approval.',
    'Partial Re-appropriate': 'Select this when a portion of the required funds will be re-appropriated from another approved budget head.',
    'Partial un-Budgeted': 'Select this when the shortfall must be covered through unbudgeted funds that require VC approval.',
    'Partial Un-Budgeted': 'Select this when the shortfall must be covered through unbudgeted funds that require VC approval.'
};

function normalizeBudgetType(value) {
    if (!value) {
        return { key: '', label: '' };
    }
    const trimmed = value.toString().trim();
    const lower = trimmed.toLowerCase();
    for (const key in vcTypeMap) {
        if (key === lower || vcTypeMap[key].toLowerCase() === lower) {
            return { key, label: vcTypeMap[key] };
        }
    }
    return { key: '', label: trimmed };
}

function normalizeVcStatus(value) {
    const upper = (value || '').toString().trim().toUpperCase();
    if (!upper) return '';
    if (upper === 'APPROVED' || upper === 'APPROVE' || upper.includes('APPROVE')) return 'APPROVED';
    if (upper === 'DECLINED' || upper === 'DECLINE' || upper.includes('DECLINE')) return 'DECLINED';
    if (upper === 'PENDING' || upper === 'REQUESTED' || upper.includes('PEND') || upper.includes('REQUEST')) return 'PENDING';
    return upper;
}

function updateVcReasonExplanation(reason) {
    const normalized = normalizeBudgetType(reason);
    const explanation = vcReasonExplanations[normalized.key] || vcReasonExplanations[normalized.label] || '';
    $('#vcReasonExplanation').text(explanation);
}

function updateVcReasonDetailsCount() {
    const text = $('#vcReasonDetails').val() || '';
    $('#vcReasonDetailsCount').text(text.length);
}

updateVcReasonDetailsCount();

function setVcState(type, status, details, remarks, budgetUpdated) {
    const normalizedType = normalizeBudgetType(type);
    vcState.typeKey = normalizedType.key;
    vcState.typeLabel = normalizedType.label;
    vcState.status = normalizeVcStatus(status);
    vcState.details = details || '';
    vcState.remarks = remarks || '';
    vcState.budgetUpdated = (budgetUpdated || '').toString().trim().toUpperCase();
    vcState.hasRequest = !!(vcState.typeLabel || vcState.status || vcState.details || vcState.remarks);

    if (!vcState.status && vcState.hasRequest) {
        vcState.status = 'PENDING';
    }
}

function refreshVcSection() {


    const section = $('#vcApprovalSection');
    const reasonSelect = $('#vcApprovalReason');
    const detailsField = $('#vcReasonDetails');
    const sendBtn = $('#sendVcApprovalBtn');
    const statusMessage = $('#vcApprovalStatusMessage');
    const remarksMessage = $('#vcApprovalRemarksMessage');
    const reasonWrapper = $('#vcReasonDetailsWrapper');
    const heading = $('#vcApprovalHeading');
    const status = vcState.status;

    section.removeClass('d-none');
    reasonWrapper.removeClass('d-none');

    const typeKey = vcState.typeKey || '';
    const typeLabel = vcState.typeLabel || (typeKey ? vcTypeMap[typeKey] : '');

    reasonSelect.val(typeKey);
    updateVcReasonExplanation(typeKey || typeLabel);
    detailsField.val(vcState.details || '');
    updateVcReasonDetailsCount();
    remarksMessage.text(vcState.remarks ? 'Remark: ' + vcState.remarks : '');

    // Default state: waiting to send
    section.removeClass('alert-success alert-danger').addClass('alert-warning');
    heading.text('Budget exceeded.');
    statusMessage.text('Send request to VC for approval.');
    reasonSelect.prop('disabled', false);
    detailsField.prop('readonly', false);
    sendBtn.show().prop('disabled', false).text('Send Request');
    if (!$('#submitBtn').data('force-hidden')) {
        $('#submitBtn').hide();
    }

    if (status === 'PENDING') {
        const label = typeLabel || 'this request';
        heading.text('Budget request submitted.');
        statusMessage.text('Awaiting VC approval for ' + label + '. Submission will unlock once approved.');
        reasonSelect.prop('disabled', true).val(vcState.typeKey || '');
        updateVcReasonExplanation(vcState.typeKey || vcState.typeLabel);
        detailsField.prop('readonly', true).val(vcState.details || '');
        updateVcReasonDetailsCount();
        sendBtn.show().prop('disabled', true).text('Awaiting VC Approval');
        remarksMessage.text(vcState.remarks ? 'Remark: ' + vcState.remarks : '');
        return;
    }

    if (status === 'APPROVED') {
        const budgetUpdated = ['UPDATED', 'YES', '1','APPROVED'].includes(vcState.budgetUpdated);

        reasonSelect.prop('disabled', true).val(vcState.typeKey || '');
        updateVcReasonExplanation(vcState.typeKey || vcState.typeLabel);
        detailsField.prop('readonly', true).val(vcState.details || '');
        updateVcReasonDetailsCount();
        remarksMessage.text(vcState.remarks ? 'Remark: ' + vcState.remarks : '');
        vcState.budgetUpdated;
        // debugger;
        // if (budgetUpdated) {
            section.removeClass('alert-warning alert-danger').addClass('alert-success');
            heading.text('VC approval complete.');
            statusMessage.text('VC has approved and the budget is updated. You can now submit the vendor quotation.');
            sendBtn.show().prop('disabled', true).text('Budget Updated');
            $('#vcReasonDetailsWrapper').removeClass('d-none');
            if (!$('#submitBtn').data('force-hidden')) {
                $('#submitBtn').show();
            }
        // } else {
        //     section.removeClass('alert-success alert-danger').addClass('alert-warning');
        //     const label = typeLabel || 'this request';
        //     heading.text('VC approval received.');
        //     statusMessage.text('VC approved ' + label + '. Please complete the budget update before submitting.');
        //     sendBtn.show().prop('disabled', true).text('Awaiting Budget Update');
        //     if (!$('#submitBtn').data('force-hidden')) {
        //         $('#submitBtn').hide();
        //     }
        // }
        return;
    }

    if (status === 'DECLINED') {
        const label = typeLabel || 'the request';
        heading.text('VC request declined.');
        statusMessage.text('VC declined ' + label + '. Please review the remarks and update the details before sending again.');
        section.removeClass('alert-success').addClass('alert-danger');
        reasonSelect.prop('disabled', true).val(vcState.typeKey || '');
        updateVcReasonExplanation(vcState.typeKey || vcState.typeLabel);
        detailsField.prop('readonly', true).val(vcState.details || '');
        updateVcReasonDetailsCount();
        sendBtn.show().prop('disabled', true).text('VC Declined');
        remarksMessage.text(vcState.remarks ? 'Remark: ' + vcState.remarks : '');
        if (!$('#submitBtn').data('force-hidden')) {
            $('#submitBtn').hide();
        }
    }
}

function toggleBudgetControls(isOverBudget) {
    const isViewMode = $('#combinedModal').data('view-mode') === 1;
    const hasExistingVcFlow = !!(vcState.status || vcState.hasRequest);

    if (isViewMode) {
        $('#combBudgetFlag').addClass('d-none');
        $('#vcApprovalSection').addClass('d-none');
        $('#vcReasonDetailsWrapper').addClass('d-none');
        return;
    }

    if (isOverBudget || hasExistingVcFlow) {
        $('#combBudgetFlag').toggleClass('d-none', !isOverBudget);
        $('#vcApprovalSection').removeClass('d-none');
        $('#vcReasonDetailsWrapper').removeClass('d-none');
        refreshVcSection();
        return;
    }

    $('#combBudgetFlag').addClass('d-none');
    $('#vcApprovalSection')
        .addClass('d-none')
        .removeClass('alert-success')
        .removeClass('alert-danger')
        .addClass('alert-warning');
    $('#vcReasonDetailsWrapper').addClass('d-none');
    setVcState('', '', '', '', '');
    $('#vcApprovalHeading').text('Budget exceeded.');
    $('#vcApprovalStatusMessage').text('Send request to VC for approval.');
    $('#vcApprovalReason').prop('disabled', false).val('');
    updateVcReasonExplanation('');
    $('#vcReasonDetails').prop('readonly', false).val('');
    updateVcReasonDetailsCount();
    $('#vcApprovalRemarksMessage').text('');
    $('#sendVcApprovalBtn').show().prop('disabled', false).text('Send Request');
    if (!$('#submitBtn').data('force-hidden')) {
        $('#submitBtn').show();
    }
    vcState.hasRequest = false;
}

function formatNumberDisplay(value, decimals = 2) {
    const num = parseFloat(value);
    if (isNaN(num)) {
        return '-';
    }
    return num.toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function formatCurrencyDisplay(value, currencyCode) {
    const num = parseFloat(value);
    if (isNaN(num)) {
        return '-';
    }
    const code = (currencyCode || '').toString().toUpperCase();
    const symbol = code === 'USD' ? '$' : code === 'EUR' ? '€' : code === 'GBP' ? '£' : '₨';
    return `${symbol} ${num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

function setTextValue(selector, value) {
    $(selector).text(value && value !== '' ? value : '-');
}

function normalizeAdvance(value) {
    const adv = (value || '').toString().toLowerCase();
    if (adv === 'yes') return 'Yes';
    if (adv === 'no') return 'No';
    return '-';
}

// Combined Modal Function - Create or View Mode
function openCombinedModal(
    qrId,
    vqId,
    vendorId,
    vendorName,
    unitRate,
    currency,
    warranty,
    deliveryTime,
    budget,
    budgetType,
    viewMode = false,
    existingStatus = '',
    existingRemarks = '',
    poQuantity = '',
    poTotalPrice = '',
    poRemarks = '',
    itemTitle = '',
    brand = '',
    model = '',
    vendorQuantity = '',
    vendorQuotedTotal = '',
    advanceRequired = 'no',
    advancePercent = '',
    requestedBudgetType = '',
    reasonForApproval = '',
    reappropriationStatus = '',
    reappropriationRemarks = '',
    budgetUpdateFlag = ''
) {
    const currencyCode = (currency || '').toString().toUpperCase();
    const parsedUnitRate = parseFloat(unitRate) || 0;
    const parsedVendorQuantity = parseFloat(vendorQuantity);
    const parsedVendorTotal = vendorQuotedTotal !== '' ? parseFloat(vendorQuotedTotal) : (isNaN(parsedVendorQuantity) ? NaN : parsedVendorQuantity * parsedUnitRate);
    const parsedAdvancePercent = advancePercent !== '' ? parseFloat(advancePercent) : NaN;
    currentAvailableBudget = parseFloat(String(budget).replace(/,/g, '')) || 0;
    const originalBudgetType = budgetType || '-';

    $('#combinedModal').data('view-mode', viewMode ? 1 : 0);
    $('#submitBtn').data('force-hidden', !!viewMode);

    $('#combQrId').val(qrId);
    $('#combVqId').val(vqId);
    $('#combVendorId').val(vendorId);
    $('#combUnitPrice').val(parsedUnitRate);
    $('#combCurrency').val(currencyCode);
    $('#combBudgetType').val(originalBudgetType);
    $('#combTotalPrice').val('');

    // Header details
    $('#combVendorName').text(vendorName || '-');
    setTextValue('#combItemTitle', itemTitle);
    setTextValue('#combBrand', brand);
    setTextValue('#combModel', model);
    setTextValue('#combVendorQuantity', formatNumberDisplay(parsedVendorQuantity));
    setTextValue('#combVendorUnitRate', formatCurrencyDisplay(parsedUnitRate, currencyCode));
    setTextValue('#combVendorTotal', formatCurrencyDisplay(parsedVendorTotal, currencyCode));
    setTextValue('#combAdvanceRequired', normalizeAdvance(advanceRequired));
    setTextValue('#combAdvancePercent', !isNaN(parsedAdvancePercent) ? `${formatNumberDisplay(parsedAdvancePercent, 2)}%` : '-');
    setTextValue('#combCurrencyDisplay', currencyCode || '-');
    $('#combBudgetDisplay').text(budget || '-');
    setTextValue('#combBudgetTypeDisplay', originalBudgetType);
    $('#combWarranty').val(warranty || '');
    $('#combDeliveryTime').val(deliveryTime || '');

    setVcState(requestedBudgetType, reappropriationStatus, reasonForApproval, reappropriationRemarks, budgetUpdateFlag);

    if (viewMode) {
        $('#modalTitle').html('<i class="fa fa-eye mr-2"></i>View Recommendation Details');

        toggleBudgetControls(false);

        $('#recStatus').val(existingStatus).prop('disabled', true).addClass('bg-light');
        $('#recRemarks').val(existingRemarks).prop('readonly', true).addClass('bg-light');
        $('#combQuantity').val(poQuantity || '').prop('readonly', true).addClass('bg-light');
        $('#combTotalPriceDisplay').val(poTotalPrice ? formatNumberDisplay(poTotalPrice, 2) : '').prop('readonly', true).addClass('bg-light');
        $('#combTotalPrice').val(poTotalPrice || '');
        $('#combWarranty').prop('readonly', true).addClass('bg-light');
        $('#combDeliveryTime').prop('readonly', true).addClass('bg-light');
        $('#combPoRemarks').val(poRemarks || '').prop('readonly', true).addClass('bg-light');

        $('#submitBtn').hide();
        $('#cancelBtnText').text('Close');
        $('.req-indicator').hide();
        $('#vcApprovalSection').addClass('d-none');

        $('#quantityField').show();
        $('#poSectionTitle').show();
        $('textarea[name="po_remarks"]').closest('.form-group').show();
    } else {
        $('#modalTitle').html('<i class="fa fa-check-circle mr-2"></i>Approve & Create Purchase Order');

        toggleBudgetControls(false);

        $('#recStatus').val('').prop('disabled', false).removeClass('bg-light');
        $('#recRemarks').val('').prop('readonly', false).removeClass('bg-light');
        $('#combQuantity').val(!isNaN(parsedVendorQuantity) ? parsedVendorQuantity : '').prop('readonly', false).removeClass('bg-light');
        $('#combTotalPriceDisplay').val('').prop('readonly', true).removeClass('bg-light');
        $('#combTotalPrice').val('');
        $('#combWarranty').prop('readonly', true).removeClass('bg-light');
        $('#combDeliveryTime').prop('readonly', true).removeClass('bg-light');
        $('#combPoRemarks').val('').prop('readonly', false).removeClass('bg-light');

        $('#submitBtn').show();
        $('#cancelBtnText').text('Cancel');
        $('.req-indicator').show();
        $('#quantityField').show();
        $('#poSectionTitle').show();
        $('textarea[name="po_remarks"]').closest('.form-group').show();
        $('#combBudgetTypeDisplay').text(originalBudgetType || '-');

        $('#combQuantity').off('input').on('input', function() {
            const quantityVal = parseFloat($(this).val());
            const quantity = isNaN(quantityVal) ? 0 : quantityVal;
            const total = quantity * parsedUnitRate;

            $('#combTotalPriceDisplay').val(quantity ? formatNumberDisplay(total, 2) : '');
            $('#combTotalPrice').val(quantity ? total.toFixed(2) : '');

            const exceedsBudget = total > currentAvailableBudget;
            toggleBudgetControls(exceedsBudget);

            if (exceedsBudget && originalBudgetType && originalBudgetType.toLowerCase() === 'budgeted') {
                const partialLabel = 'Partial Budgeted';
                $('#combBudgetType').val(partialLabel);
                $('#combBudgetTypeDisplay').text(partialLabel);
                $('#combBudgetFlag').removeClass('d-none');
            } else {
                $('#combBudgetType').val(originalBudgetType);
                $('#combBudgetTypeDisplay').text(originalBudgetType || '-');
                $('#combBudgetFlag').addClass('d-none');
            }
        });

        if (!isNaN(parsedVendorQuantity) && parsedVendorQuantity > 0) {
            $('#combQuantity').trigger('input');
        } else {
            $('#combBudgetFlag').addClass('d-none');
        }
    }

    $('#combUnitPriceDisplay').text(formatCurrencyDisplay(parsedUnitRate, currencyCode));
    $('#combBudgetFlag').addClass('d-none');
    $('#combinedModal').modal('show');
}

// Character counter for Remarks fields
$('#recRemarks').on('input', function() {
    var length = $(this).val().length;
    $('#recRemarksCount').text(length);

    if (length > 2000) {
        $(this).val($(this).val().substring(0, 2000));
        $('#recRemarksCount').text(2000);
    }
});

$('#combPoRemarks').on('input', function() {
    var length = $(this).val().length;
    $('#poRemarksCount').text(length);

    if (length > 2000) {
        $(this).val($(this).val().substring(0, 2000));
        $('#poRemarksCount').text(2000);
    }
});

$('#vcReasonDetails').on('input', function() {
    if ($(this).val().length > 2000) {
        $(this).val($(this).val().substring(0, 2000));
    }
    updateVcReasonDetailsCount();
});

$('#sendVcApprovalBtn').on('click', function() {
    const currentStatus = normalizeVcStatus(vcState.status);
    if (currentStatus === 'PENDING' || currentStatus === 'APPROVED') {
        showToast('info', 'This item is already with VC. Wait for their response before sending again.', 'Request Already Sent');
        return;
    }

    const reasonKey = $('#vcApprovalReason').val();
    const totalPrice = $('#combTotalPrice').val();
    const quantity = $('#combQuantity').val();
    const reasonDetails = $('#vcReasonDetails').val().trim();

    if (!reasonKey) {
        showToast('warning', 'Please select a reason before sending the approval request.', 'Reason Required');
        return;
    }

    const normalized = normalizeBudgetType(reasonKey);
    const reasonLabel = normalized.label || normalized.key || reasonKey;

    if (!totalPrice) {
        showToast('warning', 'Please enter quantity to calculate total price before sending for approval.', 'Total Price Missing');
        return;
    }

    if (!reasonDetails) {
        showToast('warning', 'Please provide explanation for the approval request.', 'Explanation Required');
        return;
    }

    const payload = {
        _token: $('input[name="_token"]').val(),
        qr_id: $('#combQrId').val(),
        vq_id: $('#combVqId').val(),
        requested_budget_type: reasonLabel,
        total_price: totalPrice,
        available_budget: currentAvailableBudget,
        quantity: quantity,
        reason_for_approval: reasonDetails
    };

    const button = $(this);
    button.prop('disabled', true);

    $.ajax({
        url: '{{ route("quotation.request-vc-approval") }}',
        type: 'POST',
        data: payload,
        success: function(response) {
            showToast('success', response.message ?? 'Approval request sent successfully.', 'Sent for Approval');
            setVcState(reasonLabel, 'PENDING', reasonDetails, '', '0');
            refreshVcSection();
            button.prop('disabled', true).text('Awaiting VC Approval');
            setTimeout(function() {
                location.reload();
            }, 1200);
        },
        error: function(xhr) {
            let errorMsg = 'Unable to send approval request.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
            }
            showToast('error', errorMsg, 'Error!');
            button.prop('disabled', false);
        }
    });
});

$('#vcApprovalReason').on('change', function() {
    const selectedReason = $(this).val();
    updateVcReasonExplanation(selectedReason);
    if (!vcState.typeKey && !selectedReason) {
        $('#vcReasonDetails').val('');
        updateVcReasonDetailsCount();
    }
});

$('#combinedForm').on('submit', function(e) {
    e.preventDefault();

    // Don't submit if in view mode
    if ($('#submitBtn').is(':hidden')) {
        return false;
    }

    const submitBtn = $('#submitBtn');
    if (submitBtn.prop('disabled')) {
        return false;
    }

    // Combined data for both recommendation and purchase order
    const formData = {
        _token: $('input[name="_token"]').val(),
        qr_id: $('#combQrId').val(),
        vq_id: $('#combVqId').val(),
        recommended_flag: $('select[name="recommended_flag"]').val(),
        rec_remarks: $('textarea[name="rec_remarks"]').val(),
        vendor_id: $('#combVendorId').val(),
        quantity: $('#combQuantity').val(),
        unit_price: $('#combUnitPrice').val(),
        total_price: $('#combTotalPrice').val(),
        currency: $('#combCurrency').val(),
        budget_type: $('#combBudgetType').val(),
        budget_amount: currentAvailableBudget,
        warranty: $('#combWarranty').val(),
        delivery_time: $('#combDeliveryTime').val(),
        po_remarks: $('textarea[name="po_remarks"]').val()
    };

    const originalSubmitHtml = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

    // Single AJAX call - both actions in one function
    $.ajax({
        url: '{{ route("quotation.approve-and-order") }}',
        type: 'POST',
        data: formData,
        success: function(response) {
            showToast('success', response.message, 'Success!');
            $('#combinedForm')[0].reset();
            $('#combinedModal').modal('hide');
            setTimeout(() => location.reload(), 2000);
        },
        error: function(xhr) {
            let errorMsg = 'An error occurred';
            if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.already_submitted) {
                showToast('info', xhr.responseJSON.message, 'Already Recommended');
                $('#combinedModal').modal('hide');
                setTimeout(() => location.reload(), 1200);
                return;
            }
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
            }
            showToast('error', errorMsg, 'Error!');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalSubmitHtml);
        }
    });
});
</script>

<style>
.table {
    font-size: 0.95rem;
}
.table th {
    border-top: none;
    font-size: 0.95rem;
}
.table td {
    font-size: 0.95rem;
}
.modal-content {
    border-radius: 0.5rem;
}
.badge {
    padding: 0.35em 0.75em;
    font-size: 0.85rem;
}
#vcApprovalSection .form-row .btn {
    white-space: nowrap;
}
@media (max-width: 576px) {
    #vcApprovalSection .form-row > [class^="col-"] {
        margin-bottom: 0.5rem;
    }
    #vcApprovalSection .form-row > [class^="col-"]:last-child {
        margin-bottom: 0;
    }
    #vcApprovalSection .form-row .btn {
        width: 100%;
    }
}
</style>
@endsection
