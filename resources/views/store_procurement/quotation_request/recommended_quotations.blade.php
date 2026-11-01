@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 text-dark">
            <h4 class="mb-0 text-white">
                Purchase Order Approval Requests
            </h4>
        </div>

        <div class="card-body py-3 border-bottom">
            <p class="text-muted mb-0">Review and approve or reject PO approval requests assigned to you</p>
        </div>

        <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
            <style>
                .po-approval-tabs .nav-link {
                    border-radius: 20px;
                    font-weight: 600;
                    color: #ffffff;
                    background-color: #17675F;
                    border: 1px solid transparent;
                    padding: 0.65rem 1.5rem;
                    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
                }
                .po-approval-tabs .nav-link:not(.active):hover {
                    background-color: #2f3fad;
                    color: #ffffff;
                }
                .po-approval-tabs .nav-link.active {
                    background-color: #ffffff;
                    color: #17675F;
                    border-color: #d8dcf5;
                    box-shadow: 0 4px 12px rgba(69, 85, 195, 0.15);
A                }
                .po-approval-tabs .nav-link.active .badge {
                    background-color: #17675F !important;
                    color: #ffffff !important;
                }
                .po-approval-tabs .nav-link:not(.active) .badge {
                    background-color: rgba(255, 255, 255, 0.25) !important;
                    color: #ffffff !important;
                }
            </style>
            <ul class="nav nav-pills nav-fill po-approval-tabs" id="approvalTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">
                        <i class="fa fa-clock"></i> Pending
                    @if($pendingApprovals->total() > 0)
                        <span class="badge badge-light text-dark ml-1 badge-pill small">{{ $pendingApprovals->total() }}</span>
                    @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">
                        <i class="fa fa-check"></i> Approved
                    @if($approvedApprovals->total() > 0)
                        <span class="badge badge-light text-dark ml-1 badge-pill small">{{ $approvedApprovals->total() }}</span>
                    @endif
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab">
                        <i class="fa fa-times-circle"></i> Rejected
                        @if($rejectedApprovals->total() > 0)
                            <span class="badge badge-light text-dark ml-1 badge-pill small">{{ $rejectedApprovals->total() }}</span>
                        @endif
                    </a>
                </li> --}}

            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="approvalTabsContent">
                <script>
                    $(function(){
                        // Restore active tab based on pagination parameter
                        var activeTab = 'pending';
                        if (new URLSearchParams(window.location.search).has('approved_page')) {
                            activeTab = 'approved';
                        }
                        if (activeTab !== 'pending') {
                            $('#'+activeTab+'-tab').tab('show');
                        }
                    });
                </script>

                <!-- Pending Tab -->
                <div class="tab-pane fade show active p-3" id="pending" role="tabpanel">
                    @if($pendingApprovals->total() > 0)
            <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Requesting Dept</th>
                                    <th>Vendor</th>
                                    <th class="text-right">Total Price</th>
                                    <th class="text-center">Budget Type</th>
                                    <th class="text-center">Budget Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Comparative</th>
                                    
                        </tr>
                    </thead>
                    <tbody>
                                @foreach($pendingApprovals as $po)
                                {{-- @dd($po) --}}
                            @php
                                $vendorQuotation = $po->vendorQuotation ?? null;
                                $vendor = $vendorQuotation ? ($vendorQuotation->vendor ?? null) : null;
                                $qReq = $po->quotationRequest ?? ($vendorQuotation ? ($vendorQuotation->quotationRequest ?? null) : null);
                                $budgetItem = $qReq->budgetItem ?? null;
                                $budgetHead = $qReq->budgetHead ?? null;
                                        $requestDept = $qReq->requestDepartment ?? null;
                                        $vendorCategory = $budgetItem->vendorCategory ?? null;
                                        $parentDept = $vendorCategory->parentDepartment ?? null;
                            @endphp
                            <tr id="row-{{ $po->po_id }}">
                                <td>{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">{{ $budgetItem->item_title ?? 'N/A' }}</td>
                                        <td>{{ $requestDept->dept_name ?? '-' }}</td>
                                        <td>{{ $vendor->vendor_name ?? '-' }}</td>
                                        @php
                                            // Prefer values from SP_QUOTATION_REQUEST (qReq), then fall back to PO/vendorQuotation
                                            $displayUnitPrice = $po->unit_price ?? ($vendorQuotation->rate ?? null);
                                            $displayTotalPrice = $qReq->total_price ?? ($po->total_price ?? ($vendorQuotation->price ?? null));
                                            $displayCurrency = $po->currency ?? ($vendorQuotation->currency ?? null);
                                            $displayBudgetType = optional($budgetItem)->budget_type
                                                ?? optional($budgetItem)->requested_budget_type
                                                ?? ($po->budget_type ?? null);
                                            $itemQty = optional($budgetItem)->quantity;
                                            $itemRate = optional($budgetItem)->rate;
                                            $displayBudgetAmount = ($itemQty !== null && $itemRate !== null)
                                                ? ((float) $itemQty * (float) $itemRate)
                                                : ($po->budget_amount ?? null);
                                        @endphp
                                        <td class="text-right font-weight-bold">
                                            {{ $displayTotalPrice !== null ? number_format($displayTotalPrice, 2) : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ $displayBudgetType ? ucfirst($displayBudgetType) : 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">
                                                {{ $displayBudgetAmount !== null && $displayBudgetAmount !== '' ? number_format((float) $displayBudgetAmount, 2) : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-info btn-sm rounded-pill summary-btn"
                                                data-toggle="modal"
                                                data-target="#summaryModal"
                                                data-qr-id="{{ $qReq->qr_id ?? '' }}"
                                                data-po-id="{{ $po->po_id }}">
                                                <i class="fa fa-file-text"></i> Approve Now
                                            </button>
                                </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-info btn-sm comparative-report-btn"
                                                data-report-url="{{ $qReq ? route('quotation.comparative-report', ['id' => $qReq->qr_id]) : '' }}"
                                                {{ $qReq ? '' : 'disabled' }}>
                                                <i class="fa fa-bar-chart"></i> Report
                                            </button>
                                        </td>
                                        
                                
                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>No pending approvals</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end mt-3">
                        {{ $pendingApprovals->appends(['approved_page' => request('approved_page')])->links() }}
                    </div>
                </div>

                <!-- Approved Tab -->
                <div class="tab-pane fade p-3" id="approved" role="tabpanel">
                    @if($approvedApprovals->total() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Requesting Dept</th>
                                    <th>Vendor</th>
                                    <th class="text-right">Total Price</th>
                                    <th class="text-center">Budget Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Comparative</th>
                                    <th class="text-center">Approved On</th>
                                    
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedApprovals as $po)
                                    @php
                                        $vendorQuotation = $po->vendorQuotation ?? null;
                                        $vendor = $vendorQuotation ? ($vendorQuotation->vendor ?? null) : null;
                                        $qReq = $po->quotationRequest ?? ($vendorQuotation ? ($vendorQuotation->quotationRequest ?? null) : null);
                                        $budgetItem = $qReq->budgetItem ?? null;
                                        $budgetHead = $qReq->budgetHead ?? null;
                                        $requestDept = $qReq->requestDepartment ?? null;
                                        $vendorCategory = $budgetItem->vendorCategory ?? null;
                                        $parentDept = $vendorCategory->parentDepartment ?? null;
                                    @endphp
                                    <tr class="table-success">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $po->po_id }}</span>
                                        </td>
                                        <td class="font-weight-bold">{{ $budgetItem->item_title ?? 'N/A' }}</td>
                                        <td>{{ $requestDept->dept_name ?? '-' }}</td>
                                        <td>{{ $vendor->vendor_name ?? '-' }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($po->total_price ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">{{ ucfirst($po->budget_type ?? 'N/A') }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{-- <button type="button"
                                                class="btn btn-success btn-sm po-status-btn d-none"
                                                data-qr-id="{{ $qReq->qr_id ?? '' }}"
                                                data-po-id="{{ $po->po_id }}">
                                                <i class="fa fa-tasks"></i> View Status
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm view-status-details-btn"
                                                data-toggle="modal"
                                                data-target="#new_sttsua_model"
                                                data-qr-id="{{ $qReq->qr_id ?? '' }}">
                                                <i class="fa fa-eye"></i> View Status
                                            </button> --}}
                                            <button type="button"
                                                class="btn btn-outline-info btn-sm summary-btn mt-1"
                                                data-toggle="modal"
                                                data-target="#summaryModal"
                                                data-qr-id="{{ $qReq->qr_id ?? '' }}"
                                                data-po-id="{{ $po->po_id }}">
                                                <i class="fa fa-file-text"></i> Approve Now
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-info btn-sm comparative-report-btn"
                                                data-report-url="{{ $qReq ? route('quotation.comparative-report', ['id' => $qReq->qr_id]) : '' }}"
                                                {{ $qReq ? '' : 'disabled' }}>
                                                <i class="fa fa-bar-chart"></i> Report
                                            </button>
                                        </td>
                                        <td>{{ $po->approval_record->remarks ?? 'No remarks' }}</td>
                                        {{-- <td class="text-center">
                                            {{ isset($po->updated_at) && $po->updated_at ? \Carbon\Carbon::parse($po->updated_at)->format('d-M-Y') : '-' }}
                                        </td> --}}
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-secondary view-details-btn"
                                                data-id="{{ $po->po_id }}"
                                                data-item="{{ $budgetItem->item_title ?? '-' }}"
                                                data-category="{{ $budgetHead->head_title ?? '-' }}"
                                                data-req-dept="{{ $requestDept->dept_name ?? '-' }}"
                                                data-parent-dept="{{ $parentDept->dept_name ?? '-' }}"
                                                data-vendor="{{ $vendor->vendor_name ?? '-' }}"
                                                data-quantity="{{ $po->quantity ?? $budgetItem->quantity ?? '-' }}"
                                                data-unit-price="{{ number_format($po->unit_price ?? 0, 2) }}"
                                                data-total-price="{{ number_format($po->total_price ?? 0, 2) }}"
                                                data-currency="{{ $po->currency ?? '-' }}"
                                                data-budget-type="{{ $po->budget_type ?? '-' }}"
                                                data-purchase-mode="{{ ucfirst($budgetItem->purchase_mode ?? '-') }}"
                                                data-warranty="{{ $po->warranty ?? '-' }}"
                                                data-delivery="{{ $po->delivery_time ?? '-' }}"
                                                data-remarks="{{ $po->remarks ?? '-' }}">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                          </td>
                                        
                            </tr>
                                @endforeach
                    </tbody>
                </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>No approved orders</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end mt-3">
                        {{ $approvedApprovals->appends(['pending_page' => request('pending_page')])->links() }}
                    </div>
                </div>



{{--  --}}
<!-- Rejected Tab -->
<div class="tab-pane fade p-3" id="rejected" role="tabpanel">
    @if($rejectedApprovals->total() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Requesting Dept</th>
                    <th>Vendor</th>
                    <th class="text-right">Total Price</th>
                    <th class="text-center">Budget Type</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Comparative</th>
                    <th class="text-center">Rejected On</th>
                    
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rejectedApprovals as $po)
                {{-- @dd($po) --}}
                    @php
                        $vendorQuotation = $po->vendorQuotation ?? null;
                        $vendor = $vendorQuotation ? ($vendorQuotation->vendor ?? null) : null;
                        $qReq = $po->quotationRequest ?? ($vendorQuotation ? ($vendorQuotation->quotationRequest ?? null) : null);
                        $budgetItem = $qReq->budgetItem ?? null;
                        $budgetHead = $qReq->budgetHead ?? null;
                        $requestDept = $qReq->requestDepartment ?? null;
                        $vendorCategory = $budgetItem->vendorCategory ?? null;
                        $parentDept = $vendorCategory->parentDepartment ?? null;
                    @endphp
                    <tr class="table-danger">
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $budgetItem->item_title ?? 'N/A' }}</td>
                        <td>{{ $requestDept->dept_name ?? '-' }}</td>
                        <td>{{ $vendor->vendor_name ?? '-' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($po->total_price ?? 0, 2) }}</td>
                        <td class="text-center"><span class="badge badge-secondary">{{ ucfirst($po->budget_type ?? 'N/A') }}</span></td>
                        <td class="text-center">
                            <button type="button"
                                class="btn btn-danger btn-sm po-status-btn"
                                data-qr-id="{{ $qReq->qr_id ?? '' }}"
                                data-po-id="{{ $po->po_id }}">
                                <i class="fa fa-tasks"></i> View Status
                            </button>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm view-status-details-btn"
                                data-toggle="modal"
                                data-target="#new_sttsua_model"
                                data-qr-id="{{ $qReq->qr_id ?? '' }}">
                                <i class="fa fa-eye"></i> View Status
                            </button>
                            <button type="button"
                                class="btn btn-outline-info btn-sm summary-btn mt-1"
                                data-toggle="modal"
                                data-target="#summaryModal"
                                data-qr-id="{{ $qReq->qr_id ?? '' }}"
                                data-po-id="{{ $po->po_id }}">
                                <i class="fa fa-file-text"></i> Approve Now
                            </button>
                        </td>
                        <td class="text-center">
                            <button type="button"
                                class="btn btn-info btn-sm comparative-report-btn"
                                data-report-url="{{ $qReq ? route('quotation.comparative-report', ['id' => $qReq->qr_id]) : '' }}"
                                {{ $qReq ? '' : 'disabled' }}>
                                <i class="fa fa-bar-chart"></i> Report
                            </button>
                        </td>
                        <td class="text-center">
                            {{ isset($po->updated_at) && $po->updated_at ? \Carbon\Carbon::parse($po->updated_at)->format('d-M-Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-secondary view-details-btn"
                                data-id="{{ $po->po_id }}"
                                data-item="{{ $budgetItem->item_title ?? '-' }}"
                                data-category="{{ $budgetHead->head_title ?? '-' }}"
                                data-req-dept="{{ $requestDept->dept_name ?? '-' }}"
                                data-parent-dept="{{ $parentDept->dept_name ?? '-' }}"
                                data-vendor="{{ $vendor->vendor_name ?? '-' }}"
                                data-quantity="{{ $po->quantity ?? $budgetItem->quantity ?? '-' }}"
                                data-unit-price="{{ number_format($po->unit_price ?? 0, 2) }}"
                                data-total-price="{{ number_format($po->total_price ?? 0, 2) }}"
                                data-currency="{{ $po->currency ?? '-' }}"
                                data-budget-type="{{ $po->budget_type ?? '-' }}"
                                data-purchase-mode="{{ ucfirst($budgetItem->purchase_mode ?? '-') }}"
                                data-warranty="{{ $po->warranty ?? '-' }}"
                                data-delivery="{{ $po->delivery_time ?? '-' }}"
                                data-remarks="{{ $po->remarks ?? '-' }}">
                                <i class="fa fa-eye"></i> View
                            </button>
                        </td>
                        <td>{{ $po->approval_record->remarks ?? 'No remarks' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center text-muted py-5">
        <i class="fa fa-inbox fa-3x mb-3"></i>
        <p>No rejected orders</p>
    </div>
    @endif
    <div class="d-flex justify-content-end mt-3">
        {{ $rejectedApprovals->appends(['pending_page' => request('pending_page'), 'approved_page' => request('approved_page')])->links() }}
    </div>
</div>
{{--  --}}





            </div>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-info-circle mr-2"></i>Purchase Order Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="bg-light p-3 rounded mb-3">
                    <label class="d-block text-muted mb-1">PO ID</label>
                    <div class="font-weight-bold h5 mb-0" id="detailPoId"></div>
                </div>

                {{-- Department Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-building mr-2"></i>Department Information
                    </h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="d-block text-muted mb-1">Requesting Department</label>
                        <div class="font-weight-bold" id="detailReqDept"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="d-block text-muted mb-1">Parent Department</label>
                        <div class="font-weight-bold" id="detailParentDept"></div>
                    </div>
                </div>

                {{-- Item Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-box mr-2"></i>Item Information
                    </h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="d-block text-muted mb-1">Item Name</label>
                        <div class="font-weight-bold" id="detailItem"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="d-block text-muted mb-1">Category</label>
                        <div class="font-weight-bold" id="detailCategory"></div>
                    </div>
                </div>

                {{-- Budget Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-wallet mr-2"></i>Budget Information
                    </h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Budget Type</label>
                        <div class="font-weight-bold" id="detailBudgetType"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Purchase Mode</label>
                        <div class="font-weight-bold" id="detailPurchaseMode"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Quantity</label>
                        <div class="font-weight-bold" id="detailQuantity"></div>
                    </div>
                </div>

                {{-- Vendor Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-building mr-2"></i>Vendor Information
                    </h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Vendor Name</label>
                        <div class="font-weight-bold" id="detailVendor"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Delivery Time</label>
                        <div id="detailDelivery"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Warranty</label>
                        <div id="detailWarranty"></div>
                    </div>
                </div>

                {{-- Price Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-dollar-sign mr-2"></i>Price Information
                    </h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Unit Price</label>
                        <div class="font-weight-bold h5" id="detailUnitPrice"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Total Price</label>
                        <div class="font-weight-bold h5" id="detailTotalPrice"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="d-block text-muted mb-1">Currency</label>
                        <div class="font-weight-bold h5" id="detailCurrency"></div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="bg-light px-4 py-3 mb-3 rounded">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fa fa-comment mr-2"></i>Remarks
                    </h6>
                </div>
                <div class="mb-3">
                    <div id="detailRemarks" class="text-secondary"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PO Status Modal --}}
<div class="modal fade" id="poStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title font-weight-bold" id="poStatusTitle">
                    <i class="fa fa-tasks mr-2"></i>PO Approval Status
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="poStatusContent">
                <div class="text-center text-muted py-4">
                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                    <p class="mb-0">Select a purchase order to view approval status.</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Comparative Report Modal --}}
<div class="modal fade" id="comparativeReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-bar-chart mr-2"></i>Comparative Report
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="comparativeReportContent">
                <div class="py-5 text-center text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                    <p class="mb-0">Loading comparative report...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Approve Confirmation Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fa fa-check-circle"></i> Confirm Approval
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>Approve this purchase order?</h5>
                    <p class="text-muted">
                        PO ID: <strong id="approvePoId"></strong>
                    </p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">
                        Approval Remarks <span class="text-danger">*</span>
                        <small class="text-muted">(Min 5, Max 500 characters)</small>
                    </label>
                    <textarea id="approveRemarks" class="form-control" rows="3" maxlength="500" placeholder="Enter remarks for approval (minimum 5 characters)..." required></textarea>
                    <small class="form-text text-muted">
                        <span id="approveCharCount">0</span> / 500 characters
                        <span id="approveMinWarning" class="text-danger ml-2 d-none">Minimum 5 characters required</span>
                    </small>
                </div>
                <input type="hidden" id="approvePoIdHidden" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmApproveBtn">
                    <i class="fa fa-check"></i> Approve Purchase Order
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fa fa-times-circle"></i> Reject Purchase Order
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-0">PO ID: <strong id="rejectPoId"></strong></p>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Rejection Remarks <span class="text-danger">*</span>
                            <small class="text-muted">(Min 5, Max 500 characters)</small>
                        </label>
                        <textarea id="rejectRemarks" class="form-control" rows="4" maxlength="500" placeholder="Enter reason for rejection (minimum 5 characters)..." required></textarea>
                        <small class="form-text text-muted">
                            <span id="rejectCharCount">0</span> / 500 characters
                            <span id="rejectMinWarning" class="text-danger ml-2 d-none">Minimum 5 characters required</span>
                        </small>
                    </div>
                    <input type="hidden" id="rejectPoIdHidden" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Reject Purchase Order
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-ban"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('store_procurement.quotation_request.partials.new_status_modal')

{{-- Summary Modal --}}
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom-0" style="background-color: #17675F !important;">
                <h5 class="modal-title text-white" id="summaryModalLabel">
                    Summary
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="summaryModalBody">
                <div class="text-center text-muted py-4">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                    <p class="mb-0">Loading summary...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Logged-in employee id for filtering summary up to current approver
    const CURRENT_EMPLOYEE_ID = '{{ auth()->user()->employee_id }}';
    if (typeof window.jQuery === 'undefined') {
        console.error('jQuery is required for recommended quotations interactions.');
        return;
    }

    var $ = window.jQuery;

    // View PO Status - Approval trail
    $(document).on('click', '.po-status-btn', function () {
        const qrId = $(this).data('qr-id');
        const poId = $(this).data('po-id');

        if (!qrId) {
            showToast('warning', 'Quotation reference not found for this PO.', 'Unable to Load Status');
            return;
        }

        $('#poStatusTitle').html(`<i class="fa fa-tasks mr-2"></i>PO Approval Status`);

        $('#poStatusContent').html(`
            <div class="text-center text-muted py-4">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p class="mb-0">Loading approval status...</p>
            </div>
        `);
        $('#poStatusModal').modal('show');

        $.ajax({
            url: '{{ route("quotation.po-approvals") }}',
            method: 'GET',
            data: { qr_id: qrId },
            success: function (response) {
                if (!response.success) {
                    $('#poStatusContent').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-info-circle mr-2"></i>${response.message ?? 'No approval data found for this purchase order.'}
                        </div>
                    `);
                    return;
                }

                const approvals = response.approvals || [];

                if (!approvals.length) {
                    $('#poStatusContent').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-info-circle mr-2"></i>No approval trail available for this purchase order yet.
                        </div>
                    `);
                    return;
                }

                let tableRows = '';
                approvals.forEach(function (approval, index) {
                    let badgeClass = 'badge-warning';
                    let iconClass = 'fa-clock-o';
                    let statusLabel = 'Pending';
                    let rowClass = '';

                    if (approval.status === 'approved') {
                        badgeClass = 'badge-success';
                        iconClass = 'fa-check-circle';
                        statusLabel = 'Approved';
                    } else if (approval.status === 'rejected') {
                        badgeClass = 'badge-danger';
                        iconClass = 'fa-times-circle';
                        statusLabel = 'Rejected';
                        rowClass = 'table-danger';
                    }

                    tableRows += `
                        <tr class="${rowClass}">
                            <td width="5%">${approval.slab_order ?? index + 1}</td>
                            <td width="35%">
                                <strong>${approval.approver_name ?? 'Unknown Approver'}</strong>
                            </td>
                            <td width="20%">${approval.approval_date ?? '-'}</td>
                            <td width="25%">
                                ${approval.remarks ? `<small class="text-muted">${approval.remarks}</small>` : '<small class="text-muted">No remarks</small>'}
                            </td>
                            <td width="15%" class="text-center">
                                <span class="badge ${badgeClass}">
                                    <i class="fa ${iconClass} mr-1"></i>${statusLabel}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                $('#poStatusContent').html(`
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Approver</th>
                                    <th>Updated At</th>
                                    <th>Remarks</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                `);
            },
            error: function (xhr) {
                let errorMsg = 'Unable to load approval status right now.';
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#poStatusContent').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="fa fa-exclamation-triangle mr-2"></i>${errorMsg}
                    </div>
                `);
            }
        });
    });

    // Comparative Report Modal Handler
    $(document).on('click', '.comparative-report-btn', function () {
        const reportUrl = $(this).data('report-url');

        if (!reportUrl) {
            showToast('warning', 'Comparative report link is not available for this record.', 'Report Unavailable');
            return;
        }

        $('#comparativeReportContent').html(`
            <div class="py-5 text-center text-muted">
                <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                <p class="mb-0">Loading comparative report...</p>
            </div>
        `);

        $('#comparativeReportModal').modal('show');

        $.get(reportUrl, function (html) {
            $('#comparativeReportContent').html(html);
        }).fail(function () {
            $('#comparativeReportContent').html(`
                <div class="py-5 text-center text-danger">
                    <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                    <p class="mb-0">Unable to load comparative report.</p>
                </div>
            `);
        });
    });

    // Character counter for Approve Remarks
    $('#approveRemarks').on('input', function() {
        let length = $(this).val().length;
        $('#approveCharCount').text(length);

        if (length < 5) {
            $('#approveMinWarning').removeClass('d-none');
            $('#confirmApproveBtn').prop('disabled', true);
        } else {
            $('#approveMinWarning').addClass('d-none');
            $('#confirmApproveBtn').prop('disabled', false);
        }
    });

    // Character counter for Reject Remarks
    $('#rejectRemarks').on('input', function() {
        let length = $(this).val().length;
        $('#rejectCharCount').text(length);

        if (length < 5) {
            $('#rejectMinWarning').removeClass('d-none');
            $('#rejectForm button[type="submit"]').prop('disabled', true);
        } else {
            $('#rejectMinWarning').addClass('d-none');
            $('#rejectForm button[type="submit"]').prop('disabled', false);
        }
    });

    // View Details Button
    $(document).on('click', '.view-details-btn', function () {
        $('#detailPoId').text($(this).data('id'));
        $('#detailReqDept').text($(this).data('req-dept'));
        $('#detailParentDept').text($(this).data('parent-dept'));
        $('#detailItem').text($(this).data('item'));
        $('#detailCategory').text($(this).data('category'));
        $('#detailVendor').text($(this).data('vendor'));
        $('#detailQuantity').text($(this).data('quantity'));
        $('#detailUnitPrice').text($(this).data('unit-price'));
        $('#detailTotalPrice').text($(this).data('total-price'));
        $('#detailCurrency').text($(this).data('currency'));
        $('#detailBudgetType').text($(this).data('budget-type'));
        $('#detailPurchaseMode').text($(this).data('purchase-mode'));
        $('#detailWarranty').text($(this).data('warranty'));
        $('#detailDelivery').text($(this).data('delivery'));
        $('#detailRemarks').text($(this).data('remarks') || 'No remarks');

        $('#detailsModal').modal('show');
    });

    // Approve Button - Show confirmation modal
    $(document).on('click', '.approve-btn', function () {
        let poId = $(this).data("id");

        $('#approvePoIdHidden').val(poId);
        $('#approvePoId').text(poId);
        $('#approveRemarks').val('');
        $('#approveCharCount').text('0');
        $('#approveMinWarning').addClass('d-none');
        $('#confirmApproveBtn').prop('disabled', true);
        $('#approveModal').modal('show');
    });

    // Confirm Approve Button
    $('#confirmApproveBtn').on('click', function() {
        let poId = $('#approvePoIdHidden').val();
        let remarks = $('#approveRemarks').val().trim();
        let btn = $(this);
        let originalText = btn.html();

        if (!remarks || remarks.length < 5) {
            showToast('warning', 'Please provide at least 5 characters for approval remarks.', 'Remarks Too Short');
            return;
        }

        btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: "{{ route('quotation.approve') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                po_id: poId,
                remarks: remarks
            },
            success: function(res) {
                $('#approveModal').modal('hide');
                showToast('success', res.message || 'Purchase Order approved successfully!', 'Approved');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                btn.html(originalText).prop('disabled', false);
                let errorMsg = xhr.responseJSON?.message ?? 'Error approving purchase order. Please try again.';
                showToast('error', errorMsg, 'Error');
            }
        });
    });

    // Reject Button - Open Modal
    $(document).on('click', '.reject-btn', function () {
        let poId = $(this).data("id");
        $('#rejectPoIdHidden').val(poId);
        $('#rejectPoId').text(poId);
        $('#rejectRemarks').val('');
        $('#rejectCharCount').text('0');
        $('#rejectMinWarning').addClass('d-none');
        $('#rejectForm button[type="submit"]').prop('disabled', true);
        $('#rejectModal').modal('show');
    });

    // Reject Form Submit
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();

        let poId = $('#rejectPoIdHidden').val();
        let remarks = $('#rejectRemarks').val().trim();
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();

        if (!remarks || remarks.length < 5) {
            showToast('warning', 'Please provide at least 5 characters for rejection remarks.', 'Remarks Too Short');
            return;
        }

        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: "{{ route('quotation.reject') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                po_id: poId,
                remarks: remarks
            },
            success: function(res) {
                $('#rejectModal').modal('hide');
                showToast('info', res.message || 'Purchase Order rejected', 'Rejected');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                let errorMsg = xhr.responseJSON?.message ?? 'Error rejecting purchase order. Please try again.';
                showToast('error', errorMsg, 'Error');
            }
        });
    });

    // Reset modals on close
    $('#approveModal').on('hidden.bs.modal', function() {
        $('#approveRemarks').val('');
        $('#approveCharCount').text('0');
        $('#approveMinWarning').addClass('d-none');
        $('#confirmApproveBtn').html('<i class="fa fa-check"></i> Approve Purchase Order').prop('disabled', true);
    });

    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejectRemarks').val('');
        $('#rejectCharCount').text('0');
        $('#rejectMinWarning').addClass('d-none');
        $('#rejectForm button[type="submit"]').html('<i class="fa fa-times"></i> Reject Purchase Order').prop('disabled', true);
    });

    // View Status Details Button - Load recommendations/approvals in same format as static
    $(document).on('click', '.view-status-details-btn', function() {
        const qrId = $(this).data('qr-id');
        
        if (!qrId) {
            $('#statusModalBody').html(`
                <div class="alert alert-warning mb-0">
                    <i class="fa fa-exclamation-triangle mr-2"></i>Quotation reference not found.
                </div>
            `);
            return;
        }

        // Show loading
        $('#statusModalBody').html(`
            <div class="text-center text-muted py-4">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p class="mb-0">Loading approval details...</p>
            </div>
        `);

        // Fetch recommendations/approvals data
        $.ajax({
            url: '{{ route("quotation.recommendations.details") }}',
            type: 'GET',
            data: { qr_id: qrId },
            success: function(response) {
                if (response.success && response.data && response.data.recommendations) {
                    const recommendations = response.data.recommendations;
                    const referenceId = response.data.reference_id || 'N/A';

                    if (recommendations.length === 0) {
                        $('#statusModalBody').html(`
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle mr-2"></i>No approval details found.
                            </div>
                        `);
                        return;
                    }

                    // Group recommendations by role
                    const groupedByRole = {};
                    recommendations.forEach(function(rec) {
                        const role = rec.role || 'other';
                        if (!groupedByRole[role]) {
                            groupedByRole[role] = [];
                        }
                        groupedByRole[role].push(rec);
                    });

                    // Role display names mapping
                    const roleNames = {
                        'hod': 'Department Head Recommendation',
                        'gm': 'GM Procurement Recommendation',
                        'purchase_committee_member': 'Purchase Committee Recommendations',
                        'purchase_committee_line_department': 'Purchase Committee Line Department Recommendation',
                        'purchase_committee_chair_person': 'Purchase Committee Head Decision',
                        'vc': 'VC Approval',
                        'other': 'Other Recommendations'
                    };

                    // Process each role group (same format as static)
                    const roleOrder = ['hod', 'gm', 'purchase_committee_member', 'purchase_committee_line_department', 'purchase_committee_chair_person', 'vc', 'other'];
                    let html = '';
                    
                    // Reference ID
                    html += `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-1">Reference ID</h6>
                                <p class="mb-0 font-weight-semibold">${referenceId}</p>
                            </div>
                        </div>
                    `;

                    roleOrder.forEach(function(role) {
                        if (groupedByRole[role] && groupedByRole[role].length > 0) {
                            const recs = groupedByRole[role];
                            const roleName = roleNames[role] || role;
                            
                            // If multiple recommendations (like purchase_committee_member), show as list
                            if (recs.length > 1 || role === 'purchase_committee_member') {
                                html += `
                                    <div class="card border mb-3">
                                        <div class="card-body bg-white p-4">
                                            <h6 class="text-muted text-uppercase mb-2">${roleName}</h6>
                                            <ul class="list-group list-group-flush">
                                `;
                                
                                recs.forEach(function(rec) {
                                    const date = rec.created_at ? new Date(rec.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
                                    const statusBadge = getStatusBadge(rec.status);
                                    
                                    html += `
                                        <li class="list-group-item px-0">
                                            <span class="text-muted small d-block">Recommended ${date}</span>
                                            <span class="font-weight-semibold text-dark">${rec.person_name || 'N/A'}</span> &mdash; 
                                            <span class="text-primary font-weight-semibold">${rec.vendor_name || 'N/A'}</span>
                                            ${statusBadge}
                                            ${rec.remarks ? `<div class="text-secondary small mt-1">${rec.remarks}</div>` : ''}
                                        </li>
                                    `;
                                });
                                
                                html += `
                                            </ul>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // Single recommendation - show as card
                                const rec = recs[0];
                                const date = rec.created_at ? new Date(rec.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
                                const statusBadge = getStatusBadge(rec.status);
                                const approvedDate = rec.approved_at && rec.status === 'approved' ? 
                                    new Date(rec.approved_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : null;
                                
                                html += `
                                    <div class="card border mb-3">
                                        <div class="card-body bg-white p-4">
                                            <h6 class="text-muted text-uppercase mb-2">${roleName}</h6>
                                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                                <div>
                                                    <span class="font-weight-semibold text-dark d-block">${rec.person_name || 'N/A'}</span>
                                                    <span class="text-muted small">Recommended Date: ${date}</span>
                                                    ${approvedDate ? `<span class="text-muted small d-block">Approved Date: ${approvedDate}</span>` : ''}
                                                </div>
                                                <div class="text-md-right mt-2 mt-md-0">
                                                    <span class="text-muted small d-block">Vendor Recommended</span>
                                                    <strong>${rec.vendor_name || 'N/A'}</strong>
                                                    ${statusBadge}
                                                </div>
                                            </div>
                                            ${rec.remarks ? `
                                            <div class="p-3 bg-light rounded">
                                                <p class="mb-0 text-dark">${rec.remarks}</p>
                                            </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    });

                    // If no recommendations
                    if (html === '') {
                        html = `
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle mr-2"></i>No recommendations found.
                            </div>
                        `;
                    }

                    $('#statusModalBody').html(html);
                } else {
                    $('#statusModalBody').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-info-circle mr-2"></i>${response.message || 'No approval data found.'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Unable to load approval details.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#statusModalBody').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="fa fa-exclamation-triangle mr-2"></i>${errorMsg}
                    </div>
                `);
            }
        });
    });

    // Helper function to get status badge
    function getStatusBadge(status) {
        let statusClass = 'warning';
        let statusText = 'Pending';
        let statusIcon = 'fa-clock';

        if (status === 'approved') {
            statusClass = 'success';
            statusText = 'Approved';
            statusIcon = 'fa-check';
        } else if (status === 'rejected') {
            statusClass = 'danger';
            statusText = 'Rejected';
            statusIcon = 'fa-times';
        } else if (status === 'pending') {
            statusClass = 'warning';
            statusText = 'Pending';
            statusIcon = 'fa-clock';
        } else if (status === 'recommended') {
            statusClass = 'info';
            statusText = 'Recommended';
            statusIcon = 'fa-thumbs-up';
        }

        return `<span class="badge badge-${statusClass} ml-2"><i class="fa ${statusIcon}"></i> ${statusText}</span>`;
    }

    // Reset modal content when closed
    $('#new_sttsua_model').on('hidden.bs.modal', function() {
        $('#statusModalBody').html(`
            <div class="text-center text-muted py-4">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p class="mb-0">Loading approval details...</p>
            </div>
        `);
    });

    // Function to load summary data (reusable)
    function loadSummaryData(qrId) {
        if (!qrId) {
            $('#summaryModalBody').html(`
                <div class="alert alert-warning mb-0">
                    <i class="fa fa-exclamation-triangle mr-2"></i>QR ID not found.
                </div>
            `);
            return;
        }

        // Show loading
        $('#summaryModalBody').html(`
            <div class="text-center text-muted py-4">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p class="mb-0">Loading summary...</p>
            </div>
        `);

        // Fetch summary data from SP_PO_APPROVALS
        $.ajax({
            url: '{{ route("quotation.summary.data") }}',
            type: 'GET',
            data: { qr_id: qrId },
            success: function(response) {
                if (response.success && response.data) {
                    let approvals = response.data;

                    // Filter: include only entries up to current user's approval record
                    if (CURRENT_EMPLOYEE_ID) {
                        const idx = approvals.findIndex(function(a){
                            return String(a.approver_id) === String(CURRENT_EMPLOYEE_ID);
                        });
                        if (idx >= 0) {
                            approvals = approvals.slice(0, idx + 1);
                        }
                    }

                    if (approvals.length === 0) {
                        $('#summaryModalBody').html(`
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle mr-2"></i>No approval data found.
                            </div>
                        `);
                        return;
                    }

                    // Group approvals by role (same structure as new_status_modal)
                    const groupedByRole = {};
                    approvals.forEach(function(approval) {
                        const role = approval.role || 'unknown';
                        if (!groupedByRole[role]) {
                            groupedByRole[role] = [];
                        }
                        groupedByRole[role].push(approval);
                    });

                    // Build HTML with same card structure as new_status_modal
                    let html = '';
                    
                    // Sort roles by slab_order or role priority
                    const roleOrder = ['hod', 'gm', 'purchase_committee_member', 'purchase_committee_line_department', 'purchase_committee_chair_person', 'vc'];
                    const sortedRoles = Object.keys(groupedByRole).sort(function(a, b) {
                        const aIndex = roleOrder.indexOf(a);
                        const bIndex = roleOrder.indexOf(b);
                        if (aIndex === -1 && bIndex === -1) return a.localeCompare(b);
                        if (aIndex === -1) return 1;
                        if (bIndex === -1) return -1;
                        return aIndex - bIndex;
                    });

                    // Role name mapping
                    const roleNameMap = {
                        'hod': 'Department Head Recommendation',
                        'gm': 'General Manager Recommendation',
                        'purchase_committee_member': 'Purchase Committee Member Recommendation',
                        'purchase_committee_line_department': 'Purchase Committee Line Department Recommendation',
                        'purchase_committee_chair_person': 'Purchase Committee Chair Person Recommendation',
                        'vc': 'VC Recommendation'
                    };

                    sortedRoles.forEach(function(role) {
                        const recs = groupedByRole[role];
                        if (recs && recs.length > 0) {
                            // Get role name from map or format it
                            const roleName = roleNameMap[role] || role.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' Recommendation';
                            
                            // If multiple approvals for same role, show as list
                            if (recs.length > 1) {
                                html += `
                                    <div class="card border mb-3">
                                        <div class="card-body bg-white p-4">
                                            <h6 class="text-muted text-uppercase mb-2">${roleName}</h6>
                                            <ul class="list-group list-group-flush">
                                `;
                                
                                const isTargetRole = roleName === 'Purchase Committee Member Recommendation' || roleName === 'Purchase Committee Line Department Recommendation';
                                recs.forEach(function(approval) {
                                    // Use vendor_name if available, otherwise fallback to vendor_id, otherwise N/A (no category)
                                    let vendorDisplay = 'N/A';
                                    if (approval.vendor_name && String(approval.vendor_name).trim() !== '' && approval.vendor_name !== 'null') {
                                        vendorDisplay = String(approval.vendor_name).trim();
                                    } else if (approval.vendor_id) {
                                        // Fallback to vendor_id if vendor_name is not available
                                        vendorDisplay = approval.vendor_id;
                                    }
                                    
                                    const recommendedDate = approval.recommended_datetime || approval.updated_datetime || 'N/A';
                                    const approvedDate = approval.updated_datetime || null;
                                    const hasRemarks = approval.remarks && approval.remarks !== 'N/A' && approval.remarks.trim() !== '';
                                    const isApproved = approval.status === 'approved';
                                    const isRejected = approval.status === 'rejected';
                                    const statusBadge = isApproved
                                        ? '<span class="badge badge-success ml-2">Approved</span>'
                                        : (isRejected ? '<span class="badge badge-danger ml-2">Rejected</span>' : '');
                                    const canEdit = !isApproved && !isRejected && !hasRemarks;
                                    
                                    html += `
                                        <li class="list-group-item px-0 py-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="font-weight-semibold text-dark d-block">${approval.approver_name || 'N/A'}</span>
                                                    <span class="text-muted small">Recommended Date: ${recommendedDate} ${statusBadge}</span>
                                                    ${isApproved && approvedDate ? `<span class="text-muted small d-block">Approved Date: ${approvedDate}</span>` : ''}
                                                </div>
                                                <div class="col-md-6 mt-2 mt-md-0">
                                                    <span class="text-muted small d-block">Vendor Recommended</span>
                                                    ${canEdit ? `
                                                    <select class="form-control form-control-lg mt-1 vendor-select-${approval.id}" style="width: 100%;">
                                                        <option value="">Select Vendor</option>
                                                    </select>
                                                    <div class="vendor-quote-${approval.id} text-left mt-2"></div>
                                                    ` : `<strong class="d-block text-break">${vendorDisplay}</strong>`}
                                                </div>
                                            </div>
                                            ${canEdit ? ` 
                                            <div class="mt-3">
                                                <label class="text-muted small">Remarks: <span class="text-danger">*</span> <span class="remarks-counter-${approval.id} text-muted">(0/2000)</span></label>
                                                <textarea class="form-control remarks-input-${approval.id}" rows="3" placeholder="Enter remarks..." maxlength="2000"></textarea>
                                                <small class="text-muted">Maximum 2000 characters allowed</small>
                                                <button type="button" class="btn btn-outline-success btn-sm mt-2 mr-2 approve-remarks-btn" data-approval-id="${approval.id}">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-2 reject-remarks-btn" data-approval-id="${approval.id}">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            </div>
                                            ` : hasRemarks ? `
                                            <div class="mt-2">
                                                <div class="p-2 bg-light rounded">
                                                    <p class="mb-0 ${isTargetRole ? 'text-danger' : 'text-dark'} small">${approval.remarks}</p>
                                                </div>
                                            </div>
                                            ` : ''}
                                        </li>
                                    `;
                                });
                                
                                html += `
                                            </ul>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // Single approval - show as card (same format as example)
                                const approval = recs[0];
                                
                                // Use vendor_name if available, otherwise fallback to vendor_id, otherwise N/A (no category)
                                let vendorDisplay = 'N/A';
                                if (approval.vendor_name && String(approval.vendor_name).trim() !== '' && approval.vendor_name !== 'null') {
                                    vendorDisplay = String(approval.vendor_name).trim();
                                } else if (approval.vendor_id) {
                                    // Fallback to vendor_id if vendor_name is not available
                                    vendorDisplay = approval.vendor_id;
                                }
                                
                                const recommendedDate = approval.recommended_datetime || approval.updated_datetime || 'N/A';
                                const approvedDate = approval.updated_datetime || null;
                                const hasRemarks = approval.remarks && approval.remarks !== 'N/A' && approval.remarks.trim() !== '';
                                const isApproved = approval.status === 'approved';
                                const isRejected = approval.status === 'rejected';
                                const statusBadge = isApproved
                                    ? '<span class="badge badge-success ml-2">Approved</span>'
                                    : (isRejected ? '<span class="badge badge-danger ml-2">Rejected</span>' : '');
                                const canEdit = !isApproved && !isRejected && !hasRemarks;
                                const isTargetRole = roleName === 'Purchase Committee Member Recommendation' || roleName === 'Purchase Committee Line Department Recommendation';
                                
                                html += `
                                    <div class="card border mb-3" id="approval-card-${approval.id}">
                                        <div class="card-body bg-white p-4">
                                            <h6 class="text-muted text-uppercase mb-2">${roleName}</h6>
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <span class="font-weight-semibold text-dark d-block">${approval.approver_name || 'N/A'}</span>
                                                    <span class="text-muted small">Recommended Date: ${recommendedDate} ${statusBadge}</span>
                                                    ${isApproved && approvedDate ? `<span class="text-muted small d-block">Approved Date: ${approvedDate}</span>` : ''}
                                                </div>
                                                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                                    <span class="text-muted small d-block">Vendor Recommended</span>
                                                    ${canEdit ? `
                                                    <select class="form-control form-control-lg mt-1 vendor-select-${approval.id}" style="width: 100%;">
                                                        <option value="">Select Vendor</option>
                                                    </select>
                                                    <div class="vendor-quote-${approval.id} text-left mt-2"></div>
                                                    ` : `<strong class="d-block text-break">${vendorDisplay}</strong>`}
                                                </div>
                                            </div>
                                            ${canEdit ? `
                                            <div class="mt-3">
                                                <label class="text-muted small">Remarks: <span class="text-danger">*</span> <span class="remarks-counter-${approval.id} text-muted">(0/2000)</span></label>
                                                <textarea class="form-control remarks-input-${approval.id}" rows="3" placeholder="Enter remarks..." maxlength="2000"></textarea>
                                                <small class="text-muted">Maximum 2000 characters allowed</small>
                                                <button type="button" class="btn btn-outline-success btn-sm mt-2 mr-2 approve-remarks-btn" data-approval-id="${approval.id}">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm mt-2 reject-remarks-btn" data-approval-id="${approval.id}">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            </div>
                                            ` : hasRemarks ? `
                                            <div class="p-3 bg-light rounded">
                                                <p class="mb-0 ${isTargetRole ? 'text-danger' : 'text-dark'}">${approval.remarks}</p>
                                            </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    });

                    // If no approvals
                    if (html === '') {
                        html = `
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle mr-2"></i>No approvals found.
                            </div>
                        `;
                    }

                    $('#summaryModalBody').html(html);
                    
                    // Initialize character counters
                    $('[class*="remarks-input-"]').each(function() {
                        const classes = $(this).attr('class');
                        const match = classes.match(/remarks-input-(\d+)/);
                        if (match && match[1]) {
                            const approvalId = match[1];
                            const length = $(this).val().length;
                            $(`.remarks-counter-${approvalId}`).text(`(${length}/2000)`);
                        }
                    });
                    
                    // Load vendors for dropdowns (only for approvals without remarks and not approved)
                    const approvalIds = [];
                    approvals.forEach(function(approval) {
                        const hasRemarks = approval.remarks && approval.remarks !== 'N/A' && approval.remarks.trim() !== '';
                        const isApproved = approval.status === 'approved';
                        if (!hasRemarks && !isApproved) {
                            approvalIds.push(approval.id);
                        }
                    });
                    
                    if (approvalIds.length > 0) {
                        // Fetch vendors for dropdown
                        $.ajax({
                            url: '{{ route("quotation.summary.vendors") }}',
                            type: 'GET',
                            data: { qr_id: qrId },
                            success: function(vendorResponse) {
                                if (vendorResponse.success && vendorResponse.data) {
                                    const vendors = vendorResponse.data;
                                    
                                    // Populate all vendor dropdowns
                                    approvalIds.forEach(function(approvalId) {
                                        const $select = $(`.vendor-select-${approvalId}`);
                                        vendors.forEach(function(vendor) {
                                            const displayText = vendor.vendor_name || 'N/A';
                                            if (vendor.vc_rejected) {
                                                $select.append(`<option value="${vendor.vendor_id}" disabled style="color:#c62828;background:#fdecea;">${displayText} (Rejected by VC)</option>`);
                                            } else {
                                                $select.append(`<option value="${vendor.vendor_id}">${displayText}</option>`);
                                            }
                                        });
                                        if (vendors.length && vendors.every(function(v){ return v.vc_rejected; })) {
                                            $select.css({ color: '#c62828', borderColor: '#c62828', background: '#fdecea' });
                                        }
                                        $select.data('vendors', vendors);
                                    });
                                    $(document).off('change.vendorQuote').on('change.vendorQuote', '[class*="vendor-select-"]', function() {
                                        const classes = $(this).attr('class') || '';
                                        const match = classes.match(/vendor-select-(\d+)/);
                                        if (!match) return;
                                        const approvalId = match[1];
                                        const vendorId = String($(this).val() || '');
                                        const list = $(this).data('vendors') || [];
                                        const vendor = list.find(function(v){ return String(v.vendor_id) === vendorId; });
                                        const $box = $(`.vendor-quote-${approvalId}`);
                                        const $approveBtn = $(`.approve-remarks-btn[data-approval-id="${approvalId}"]`);
                                        if (!vendor) {
                                            $box.empty();
                                            $approveBtn.prop('disabled', false).show();
                                            return;
                                        }
                                        const blockApprove = !!(vendor.exceeds_budget && !vendor.vc_approved);
                                        $approveBtn.prop('disabled', blockApprove).toggle(!blockApprove);
                                        const money = function(n){ return Number(n || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); };
                                        let vcNote = '';
                                        if (vendor.exceeds_budget) {
                                            if (vendor.vc_approved) {
                                                vcNote = '<div class="mt-2 text-success"><strong>Exceeds budget.</strong> VC approval: Approved.</div>';
                                            } else {
                                                vcNote = '<div class="mt-2 text-danger"><strong>Exceeds budget.</strong> VC approval is ' + (vendor.vc_status || 'not sent') + '. Approve is blocked until VC approves.</div>';
                                            }
                                        } else {
                                            vcNote = '<div class="mt-2 text-success">Within budget. VC approval is not required.</div>';
                                        }
                                        $box.html(
                                            '<div class="border rounded p-2" style="background:#f7fbfa;">' +
                                            '<div><strong>Quotation:</strong> ' + (vendor.currency || '') + ' ' + money(vendor.price) + '</div>' +
                                            '<div>Qty: ' + (vendor.quantity ?? '-') + ' &nbsp; Rate: ' + money(vendor.rate) + '</div>' +
                                            '<div>Budget amount: ' + money(vendor.budget_amount) + '</div>' +
                                            '<div>Delivery: ' + (vendor.delivery_time || '-') + ' &nbsp; Warranty: ' + (vendor.warranty || '-') + '</div>' +
                                            vcNote +
                                            '</div>'
                                        );
                                    });
                                }
                            }
                        });
                    }
                } else {
                    $('#summaryModalBody').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-info-circle mr-2"></i>${response.message || 'No summary data found.'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Unable to load summary data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#summaryModalBody').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="fa fa-exclamation-triangle mr-2"></i>${errorMsg}
                    </div>
                `);
            }
        });
    }

    // Store current qrId for reload after save
    let currentQrId = null;

    // Summary Button - Open Summary Modal
    $(document).on('click', '.summary-btn', function() {
        const qrId = $(this).data('qr-id');
        currentQrId = qrId; // Store for later use
        
        loadSummaryData(qrId);
    });

    // Character counter for remarks
    $(document).on('input', '[class*="remarks-input-"]', function() {
        const classes = $(this).attr('class');
        const match = classes.match(/remarks-input-(\d+)/);
        if (match && match[1]) {
            const approvalId = match[1];
            const length = $(this).val().length;
            $(`.remarks-counter-${approvalId}`).text(`(${length}/2000)`);
            
            if (length > 2000) {
                $(this).val($(this).val().substring(0, 2000));
                $(`.remarks-counter-${approvalId}`).text('(2000/2000)');
            }
        }
    });

    function submitSummaryDecision(approvalId, status) {
        const remarks = $(`.remarks-input-${approvalId}`).val();
        const vendorId = $(`.vendor-select-${approvalId}`).val();
        if (!remarks || remarks.trim() === '') { alert('Please enter remarks'); $(`.remarks-input-${approvalId}`).focus(); return; }
        if (remarks.trim().length < 5) { alert('Remarks must be at least 5 characters'); $(`.remarks-input-${approvalId}`).focus(); return; }
        if (remarks.length > 2000) { alert('Remarks cannot exceed 2000 characters'); $(`.remarks-input-${approvalId}`).focus(); return; }
        if (!vendorId) { alert('Please select a vendor'); $(`.vendor-select-${approvalId}`).focus(); return; }
        const selectedOpt = $(`.vendor-select-${approvalId} option:selected`);
        if (selectedOpt.is(':disabled') || /Rejected by VC/.test(selectedOpt.text())) {
            alert('This vendor was rejected by VC and cannot be selected.');
            return;
        }
        if (status === 'approved') {
            const list = $(`.vendor-select-${approvalId}`).data('vendors') || [];
            const vendor = list.find(function(v){ return String(v.vendor_id) === String(vendorId); });
            if (vendor && vendor.exceeds_budget && !vendor.vc_approved) {
                alert('This quotation exceeds the budget. VC approval is required before it can be approved.');
                return;
            }
        }
        const $btns = $(`.approve-remarks-btn[data-approval-id="${approvalId}"], .reject-remarks-btn[data-approval-id="${approvalId}"]`);
        $btns.prop('disabled', true);
        $.ajax({
            url: '{{ route("quotation.approval.update-remarks") }}',
            type: 'POST',
            data: { approval_id: approvalId, vendor_id: vendorId, remarks: remarks, status: status, _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    if (currentQrId) { loadSummaryData(currentQrId); } else { location.reload(); }
                } else {
                    alert(response.message || 'Error saving'); $btns.prop('disabled', false);
                }
            },
            error: function(xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to save'); $btns.prop('disabled', false);
            }
        });
    }

    $(document).on('click', '.approve-remarks-btn', function() {
        submitSummaryDecision($(this).data('approval-id'), 'approved');
    });
    $(document).on('click', '.reject-remarks-btn', function() {
        submitSummaryDecision($(this).data('approval-id'), 'rejected');
    });

    // Reset summary modal when closed
    $('#summaryModal').on('hidden.bs.modal', function() {
        $('#summaryModalBody').html(`
            <div class="text-center text-muted py-4">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p class="mb-0">Loading summary...</p>
            </div>
        `);
    });
});
</script>

@endsection
