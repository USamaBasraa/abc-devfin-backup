@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .store-inv-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
        background: #fff;
        border: none;
        overflow: hidden;
    }
    .store-inv-header {
        background: #17675F;
        color: #fff;
        padding: 1.1rem 1.5rem;
        font-size: 18px;
        font-weight: 600;
    }
    .store-inv-table thead th {
        background: #17675F;
        color: #fff;
        font-size: 13px;
        white-space: nowrap;
        border: none;
    }
    .store-inv-table tbody td {
        font-size: 13px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .store-inv-table tbody tr:nth-child(even) { background: #f4faf9; }
    .container-fluid h4 { color: #17675F; }
    .btn-primary { background: #17675F !important; border-color: #17675F !important; color: #fff !important; border-radius: 999px; }
    .btn-primary:hover { background: #0f4d47 !important; border-color: #0f4d47 !important; }
    .btn-outline-primary { color: #17675F !important; border-color: #17675F !important; border-radius: 999px; }
    .btn-outline-primary:hover { background: #17675F !important; color: #fff !important; }
    .status-filters { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; }
    .status-filters .status-filter-btn {
        border-radius: 999px !important;
        margin: 0 !important;
        padding: 6px 14px !important;
    }
    .status-filters .btn-outline-info {
        color: #17675F !important;
        border-color: #17675F !important;
        background: #fff !important;
        border-radius: 999px !important;
    }
    .status-filters .btn-outline-info:hover,
    .status-filters .btn-outline-info.active,
    .status-filters .status-filter-btn.active {
        background: #17675F !important;
        border-color: #17675F !important;
        color: #fff !important;
    }
    
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        border-left: 4px solid #17675F;
        margin-bottom: 1rem;
    }
    .stat-card.pending { border-left-color: #ff9800; }
    .stat-card.issued { border-left-color: #00bcd4; }
    .stat-card.acknowledged { border-left-color: #4caf50; }
    .stat-card.rejected { border-left-color: #f44336; }

    .stat-val {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
    }
    .stock-badge {
        font-size: 12px;
        padding: .25rem .6rem;
        border-radius: 4px;
        font-weight: 600;
    }
    .stock-available {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .stock-empty {
        background: #ffebee;
        color: #c62828;
    }
    .stock-low {
        background: #fff3e0;
        color: #e65100;
    }
    .stock-order-alert {
        display: block;
        margin-top: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        background: #fff8e1;
        border: 1px solid #ffcc80;
        color: #e65100;
        font-size: 11px;
        font-weight: 700;
        white-space: normal;
        max-width: 200px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    .warning-qty-input {
        width: 72px;
        display: inline-block;
        padding: 2px 6px;
        font-size: 12px;
        text-align: center;
    }
    .warning-qty-hint {
        font-size: 10px;
        color: #6c757d;
        display: block;
        margin-top: 2px;
    }

    /* Modal Overlay & Visibility Styles */
    .modal.custom-modal-active {
        display: block !important;
        opacity: 1 !important;
        background: rgba(0, 0, 0, 0.65) !important;
        z-index: 99999 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .modal.custom-modal-active .modal-dialog {
        transform: none !important;
        -webkit-transform: none !important;
        margin-top: 60px;
        z-index: 100000 !important;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 font-weight-bold">Store Handover & Issue Management</h4>
            <p class="text-muted mb-0 small">Tamam departments ki store item requests ko manage aur handover / issue karein.</p>
        </div>
        <div>
            <a href="{{ route('store-items.inventory') }}" class="btn btn-outline-primary">
                <i class="fa fa-cubes mr-1"></i> View Stock Inventory
            </a>
        </div>
    </div>

    @php
        $totalCount = count($requests);
        $pendingCount = collect($requests)->where('status', 'pending')->count();
        $issuedCount = collect($requests)->where('status', 'issued')->count();
        $ackCount = collect($requests)->where('status', 'acknowledged')->count();
        $rejectedCount = collect($requests)->where('status', 'rejected')->count();
    @endphp

    <!-- Stats summary -->
    <div class="row mb-2">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-label">Total Requests</div>
                <div class="stat-val text-primary">{{ $totalCount }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card pending">
                <div class="stat-label">Pending Handover</div>
                <div class="stat-val text-warning">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card issued">
                <div class="stat-label">Issued / In Transit</div>
                <div class="stat-val text-info">{{ $issuedCount }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card acknowledged">
                <div class="stat-label">Acknowledged</div>
                <div class="stat-val text-success">{{ $ackCount }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card p-3 mb-3 border-0 shadow-sm" style="border-radius: 10px;">
        <div class="row align-items-center">
            <div class="col-md-12 text-md-right">
                <div class="status-filters" data-toggle="buttons">
                    <label class="btn btn-outline-secondary active btn-sm status-filter-btn" data-status="all">
                        <input type="radio" name="statusFilter" autocomplete="off" checked> All ({{ $totalCount }})
                    </label>
                    <label class="btn btn-outline-warning btn-sm status-filter-btn" data-status="pending">
                        <input type="radio" name="statusFilter" autocomplete="off"> Pending ({{ $pendingCount }})
                    </label>
                    <label class="btn btn-outline-info btn-sm status-filter-btn" data-status="issued">
                        <input type="radio" name="statusFilter" autocomplete="off"> Issued ({{ $issuedCount }})
                    </label>
                    <label class="btn btn-outline-success btn-sm status-filter-btn" data-status="acknowledged">
                        <input type="radio" name="statusFilter" autocomplete="off"> Acknowledged ({{ $ackCount }})
                    </label>
                    <label class="btn btn-outline-danger btn-sm status-filter-btn" data-status="rejected">
                        <input type="radio" name="statusFilter" autocomplete="off"> Rejected ({{ $rejectedCount }})
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Requests Table -->
    <div class="store-inv-card">
        <div class="store-inv-header d-flex justify-content-between align-items-center">
            <span><i class="fa fa-truck mr-2"></i> Store Items Handover & Requests List</span>
            <span class="badge badge-light text-dark" id="showingCountBadge">{{ $totalCount }}</span>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover mb-0 store-inv-table" id="issuedRequestsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Request #</th>
                        <th>Item</th>
                        <th>Department</th>
                        <th>Requested By</th>
                        <th class="text-center">Available Stock</th>
                        <th class="text-center" title="Alert when available stock falls below this quantity">Stock Alert At</th>
                        <th class="text-center">Req Qty</th>
                        <th class="text-center">Issued Qty</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-center no-export" style="min-width: 150px;">Action / Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        @php
                            $st = strtolower(trim((string) ($req->status ?? 'pending')));
                            $avail = (float) ($req->available_qty ?? 0);
                            $warningQty = (float) ($req->warning_qty ?? 0);
                            $reqQty = (float) ($req->requested_qty ?? 0);
                            $issuedQty = (float) ($req->issued_qty ?? 0);
                            $storeItemId = (int) ($req->store_item_id ?? 0);
                            $isLowStock = $warningQty > 0 && $avail <= $warningQty;
                        @endphp
                        <tr class="request-row"
                            data-status="{{ $st }}"
                            data-sir="{{ $req->sir_id }}"
                            data-store-item="{{ $storeItemId }}"
                            data-item="{{ strtolower($req->item_title ?? '') }}"
                            data-dept="{{ strtolower($req->request_dept_name ?? '') }}"
                            data-user="{{ strtolower($req->requested_by ?? '') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>SIR-{{ $req->sir_id }}</strong></td>
                            <td>
                                <div class="font-weight-bold">{{ $req->item_title ?? '-' }}</div>
                                @if(!empty($req->request_remarks))
                                    <small class="text-muted d-block" title="{{ $req->request_remarks }}">
                                        {{ \Illuminate\Support\Str::limit($req->request_remarks, 50) }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $req->request_dept_name ?? '-' }}</td>
                            <td>{{ $req->requested_by ?? '-' }}</td>
                            <td class="text-center stock-cell" data-available="{{ $avail }}" data-warning="{{ $warningQty }}" data-unit="{{ $req->unit ?? '' }}">
                                @if($avail <= 0)
                                    <span class="stock-badge stock-empty">
                                        <i class="fa fa-times-circle mr-1"></i>0.00 {{ $req->unit ?? '' }}
                                    </span>
                                @elseif($isLowStock)
                                    <span class="stock-badge stock-low">
                                        <i class="fa fa-exclamation-triangle mr-1"></i>{{ number_format($avail, 2) }} {{ $req->unit ?? '' }}
                                    </span>
                                @else
                                    <span class="stock-badge stock-available">
                                        <i class="fa fa-check-circle mr-1"></i>{{ number_format($avail, 2) }} {{ $req->unit ?? '' }}
                                    </span>
                                @endif
                                @if($isLowStock)
                                    <span class="stock-order-alert">
                                        <i class="fa fa-shopping-cart mr-1"></i>Order again — stock below alert level
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($storeItemId > 0)
                                    <div class="d-inline-flex align-items-center justify-content-center flex-wrap" style="gap:4px;">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               class="form-control form-control-sm warning-qty-input"
                                               data-store-item-id="{{ $storeItemId }}"
                                               value="{{ $warningQty > 0 ? number_format($warningQty, 2, '.', '') : '' }}"
                                               placeholder="0"
                                               title="Alert when available stock is at or below this quantity">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-save-warning-qty px-2"
                                                data-store-item-id="{{ $storeItemId }}"
                                                title="Save alert quantity">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                    <span class="warning-qty-hint warning-qty-status" data-store-item-id="{{ $storeItemId }}">Alert below this qty</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($reqQty, 2) }} {{ $req->unit ?? '' }}</td>
                            <td class="text-center">
                                @if(in_array($st, ['issued', 'acknowledged'], true))
                                    <strong class="text-success">{{ number_format($issuedQty, 2) }} {{ $req->unit ?? '' }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($st === 'acknowledged')
                                    <span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i> Acknowledged</span>
                                @elseif($st === 'issued')
                                    <span class="badge badge-info"><i class="fa fa-truck mr-1"></i> Issued</span>
                                @elseif($st === 'rejected')
                                    <span class="badge badge-danger"><i class="fa fa-times-circle mr-1"></i> Rejected</span>
                                @else
                                    <span class="badge badge-warning"><i class="fa fa-clock-o mr-1"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($req->created_at))
                                    {{ \Carbon\Carbon::parse($req->created_at)->format('d-M-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center no-export">
                                @if($st === 'pending')
                                    @if($avail > 0)
                                        <button type="button"
                                                class="btn btn-sm btn-success mr-1"
                                                onclick="openIssueModal({{ $req->sir_id }}, '{{ addslashes($req->item_title ?? '') }}', '{{ addslashes($req->request_dept_name ?? '') }}', {{ $reqQty }}, '{{ addslashes($req->unit ?? '') }}', {{ $avail }})">
                                            <i class="fa fa-handshake-o mr-1"></i> Handover / Issue
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary mr-1"
                                                onclick="alert('Cannot Issue: Available stock is 0.00. Please receive stock via Gate Pass first.')"
                                                title="Available stock is 0.00">
                                            <i class="fa fa-ban mr-1"></i> Out of Stock
                                        </button>
                                    @endif
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="openRejectModal({{ $req->sir_id }}, '{{ addslashes($req->item_title ?? '') }}', '{{ addslashes($req->request_dept_name ?? '') }}')"
                                            title="Reject request">
                                        <i class="fa fa-times"></i>
                                    </button>
                                @elseif($st === 'issued')
                                    <div class="small text-left">
                                        <span class="text-info font-weight-bold"><i class="fa fa-check mr-1"></i> Issued</span>
                                        @if(!empty($req->issued_by))
                                            <span class="text-muted d-block">by {{ $req->issued_by }}</span>
                                        @endif
                                        @if(!empty($req->issued_at))
                                            <span class="text-muted d-block">{{ \Carbon\Carbon::parse($req->issued_at)->format('d-M-Y H:i') }}</span>
                                        @endif
                                        <span class="badge badge-light border text-muted mt-1">Awaiting Dept Ack</span>
                                    </div>
                                @elseif($st === 'acknowledged')
                                    <div class="small text-left">
                                        <span class="text-success font-weight-bold"><i class="fa fa-check-circle mr-1"></i> Received</span>
                                        @if(!empty($req->acknowledged_by))
                                            <span class="text-muted d-block">Ack by {{ $req->acknowledged_by }}</span>
                                        @endif
                                        @if(!empty($req->acknowledged_at))
                                            <span class="text-muted d-block">{{ \Carbon\Carbon::parse($req->acknowledged_at)->format('d-M-Y H:i') }}</span>
                                        @endif
                                        @if(!empty($req->acknowledge_remarks))
                                            <span class="text-muted font-italic d-block">"{{ $req->acknowledge_remarks }}"</span>
                                        @endif
                                    </div>
                                @elseif($st === 'rejected')
                                    <div class="small text-danger">
                                        {{ $req->approval_remarks ?? 'Rejected' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Issue / Handover Item -->
<div class="modal fade" id="issueModal" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="issueModalLabel">
                    <i class="fa fa-handshake-o mr-2"></i> Issue / Handover Store Item
                </h5>
                <button type="button" class="close text-white" onclick="closeIssueModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="issueItemForm" onsubmit="submitIssueForm(event)">
                @csrf
                <input type="hidden" id="issueRequestId" name="request_id">
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <strong>Request # <span id="modalSirId"></span></strong>: <span id="modalItemTitle"></span>
                        <br>
                        <strong>Department:</strong> <span id="modalDeptName"></span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small mb-0">Requested Quantity</label>
                            <div class="font-weight-bold h5 text-dark" id="modalRequestedQty">0</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small mb-0">Available Stock</label>
                            <div class="font-weight-bold h5 text-success" id="modalAvailableStock">0</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Quantity to Issue <span class="text-danger">*</span></label>
                        <input type="number"
                               step="0.01"
                               min="0.01"
                               id="issueQtyInput"
                               name="issued_qty"
                               class="form-control form-control-lg"
                               required>
                        <small class="form-text text-danger font-weight-bold" id="issueStockHint"></small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Handover / Approval Remarks</label>
                        <textarea id="issueRemarksInput"
                                  name="approval_remarks"
                                  class="form-control"
                                  rows="2"
                                  maxlength="2000"
                                  placeholder="Handover notes / gatepass reference..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" onclick="closeIssueModal()">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" id="submitIssueBtn">
                        <i class="fa fa-check mr-1"></i> Confirm Issue & Deduct Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Reject Request -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="rejectModalLabel">
                    <i class="fa fa-times-circle mr-2"></i> Reject Handover Request
                </h5>
                <button type="button" class="close text-white" onclick="closeRejectModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectItemForm" onsubmit="submitRejectForm(event)">
                @csrf
                <input type="hidden" id="rejectRequestId" name="request_id">
                <div class="modal-body p-4">
                    <p class="text-muted">Are you sure you want to reject this store item handover request?</p>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Rejection Reason</label>
                        <textarea id="rejectRemarksInput"
                                  name="approval_remarks"
                                  class="form-control"
                                  rows="3"
                                  maxlength="2000"
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" id="submitRejectBtn">
                        <i class="fa fa-times mr-1"></i> Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('page-scripts')
<script>
window.currentMaxAvailable = 0;
window.storeItemsWarningQtyUrlTemplate = @json(route('store-items.warning-qty.update', ['storeItemId' => '__STORE_ITEM_ID__']));

window.openIssueModal = function(id, title, dept, reqQty, unit, available) {
    window.currentMaxAvailable = parseFloat(available) || 0;

    if (window.currentMaxAvailable <= 0) {
        alert('Cannot Issue: Available stock is 0.00. Please receive stock into store inventory first.');
        return;
    }

    document.getElementById('issueRequestId').value = id;
    document.getElementById('modalSirId').innerText = 'SIR-' + id;
    document.getElementById('modalItemTitle').innerText = title || '-';
    document.getElementById('modalDeptName').innerText = dept || '-';
    document.getElementById('modalRequestedQty').innerText = parseFloat(reqQty).toFixed(2) + ' ' + (unit || '');
    document.getElementById('modalAvailableStock').innerText = window.currentMaxAvailable.toFixed(2) + ' ' + (unit || '');

    var defaultIssue = Math.min(parseFloat(reqQty) || 0, window.currentMaxAvailable);
    var issueInput = document.getElementById('issueQtyInput');
    issueInput.value = defaultIssue.toFixed(2);
    issueInput.max = window.currentMaxAvailable;
    issueInput.min = '0.01';

    validateIssueQty();

    document.getElementById('issueRemarksInput').value = '';

    var modal = document.getElementById('issueModal');
    modal.classList.add('custom-modal-active');
};

window.validateIssueQty = function() {
    var input = document.getElementById('issueQtyInput');
    var hint = document.getElementById('issueStockHint');
    var btn = document.getElementById('submitIssueBtn');
    var val = parseFloat(input.value);

    if (isNaN(val) || val <= 0) {
        hint.className = 'form-text text-danger font-weight-bold';
        hint.innerHTML = '<i class="fa fa-exclamation-triangle mr-1"></i> Quantity must be greater than 0.00';
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        btn.disabled = true;
        return false;
    }

    if (val > window.currentMaxAvailable) {
        hint.className = 'form-text text-danger font-weight-bold';
        hint.innerHTML = '<i class="fa fa-ban mr-1"></i> Cannot issue ' + val.toFixed(2) + '! Available stock is only ' + window.currentMaxAvailable.toFixed(2);
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        btn.disabled = true;
        return false;
    }

    hint.className = 'form-text text-success font-weight-bold';
    hint.innerHTML = '<i class="fa fa-check mr-1"></i> Valid quantity (Remaining stock: ' + (window.currentMaxAvailable - val).toFixed(2) + ')';
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    btn.disabled = false;
    return true;
};

// Bind real-time input listener
document.addEventListener('DOMContentLoaded', function() {
    var issueInput = document.getElementById('issueQtyInput');
    if (issueInput) {
        issueInput.addEventListener('input', window.validateIssueQty);
        issueInput.addEventListener('change', window.validateIssueQty);
    }
});

window.closeIssueModal = function() {
    var modal = document.getElementById('issueModal');
    modal.classList.remove('custom-modal-active');
};

window.openRejectModal = function(id, title, dept) {
    document.getElementById('rejectRequestId').value = id;
    document.getElementById('rejectRemarksInput').value = '';

    var modal = document.getElementById('rejectModal');
    modal.classList.add('custom-modal-active');
};

window.closeRejectModal = function() {
    var modal = document.getElementById('rejectModal');
    modal.classList.remove('custom-modal-active');
};

window.submitIssueForm = function(e) {
    if (e) e.preventDefault();
    var id = document.getElementById('issueRequestId').value;
    var qty = parseFloat(document.getElementById('issueQtyInput').value) || 0;
    var remarks = document.getElementById('issueRemarksInput').value;

    if (window.currentMaxAvailable <= 0) {
        alert('Cannot issue: Available stock is 0.00.');
        return;
    }

    if (!window.validateIssueQty()) {
        return;
    }

    var btn = document.getElementById('submitIssueBtn');
    btn.disabled = true;
    btn.innerText = 'Issuing...';

    $.ajax({
        url: '{{ url("/store-items/requests") }}/' + id + '/issue',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            issued_qty: qty,
            approval_remarks: remarks
        },
        success: function (res) {
            window.closeIssueModal();
            alert(res.message || 'Item issued successfully.');
            window.location.reload();
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check mr-1"></i> Confirm Issue & Deduct Stock';
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || xhr.responseText || 'Failed to issue item';
            alert(msg);
        }
    });
};

window.submitRejectForm = function(e) {
    if (e) e.preventDefault();
    var id = document.getElementById('rejectRequestId').value;
    var remarks = document.getElementById('rejectRemarksInput').value || 'Rejected';

    var btn = document.getElementById('submitRejectBtn');
    btn.disabled = true;
    btn.innerText = 'Rejecting...';

    $.ajax({
        url: '{{ url("/store-items/requests") }}/' + id + '/reject',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            approval_remarks: remarks
        },
        success: function (res) {
            window.closeRejectModal();
            alert(res.message || 'Request rejected.');
            window.location.reload();
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-times mr-1"></i> Confirm Reject';
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || xhr.responseText || 'Failed to reject request';
            alert(msg);
        }
    });
};

// Close modal when clicking outside
document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('custom-modal-active')) {
        window.closeIssueModal();
        window.closeRejectModal();
    }
});

function refreshStockAlertCell($cell, available, warningQty, unit) {
    unit = unit || '';
    var isLow = warningQty > 0 && available <= warningQty;
    var badgeHtml;

    if (available <= 0) {
        badgeHtml = '<span class="stock-badge stock-empty"><i class="fa fa-times-circle mr-1"></i>0.00 ' + unit + '</span>';
    } else if (isLow) {
        badgeHtml = '<span class="stock-badge stock-low"><i class="fa fa-exclamation-triangle mr-1"></i>' + available.toFixed(2) + ' ' + unit + '</span>';
    } else {
        badgeHtml = '<span class="stock-badge stock-available"><i class="fa fa-check-circle mr-1"></i>' + available.toFixed(2) + ' ' + unit + '</span>';
    }

    var alertHtml = isLow
        ? '<span class="stock-order-alert"><i class="fa fa-shopping-cart mr-1"></i>Order again — stock below alert level</span>'
        : '';

    $cell.attr('data-available', available).attr('data-warning', warningQty);
    $cell.html(badgeHtml + alertHtml);
}

function getStoreItemIdFromEl($el) {
    var id = $el.attr('data-store-item-id');
    if (id === undefined || id === null || id === '') {
        id = $el.data('storeItemId');
    }
    return parseInt(id, 10) || 0;
}

function warningQtyFromInput($input) {
    var val = ($input.val() || '').trim();
    if (val === '') {
        return null;
    }
    var warningQty = parseFloat(val);
    if (isNaN(warningQty) || warningQty < 0) {
        return NaN;
    }
    return warningQty;
}

function previewStockAlertsForItem(storeItemId, warningQty) {
    $('tr.request-row[data-store-item="' + storeItemId + '"]').each(function () {
        var $cell = $(this).find('.stock-cell');
        var avail = parseFloat($cell.attr('data-available')) || 0;
        var unitText = $cell.attr('data-unit') || '';
        refreshStockAlertCell($cell, avail, warningQty, unitText);
    });
}

function saveWarningQtyForItem(storeItemId, warningQty, $input, $statusEl) {
    var url = (window.storeItemsWarningQtyUrlTemplate || '').replace('__STORE_ITEM_ID__', storeItemId);

    if ($statusEl && $statusEl.length) {
        $statusEl.text('Saving...').css('color', '#17675F');
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            warning_qty: warningQty === null ? '' : warningQty
        },
        success: function (res) {
            var w = parseFloat(res.warning_qty);
            if (isNaN(w)) {
                w = warningQty === null ? 0 : warningQty;
            }
            $('input.warning-qty-input[data-store-item-id="' + storeItemId + '"]').val(w > 0 ? w.toFixed(2) : '');
            previewStockAlertsForItem(storeItemId, w);
            if ($input && $input.length) {
                $input.addClass('is-valid');
                setTimeout(function () { $input.removeClass('is-valid'); }, 1500);
            }
            if ($statusEl && $statusEl.length) {
                $statusEl.text('Saved').css('color', '#2e7d32');
                setTimeout(function () {
                    $statusEl.text('Alert below this qty').css('color', '#6c757d');
                }, 2000);
            }
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save alert quantity';
            alert(msg);
            if ($statusEl && $statusEl.length) {
                $statusEl.text('Save failed').css('color', '#c62828');
            }
        }
    });
}

// Filters + DataTable
$(function () {
    var warningSaveTimer = null;
    var currentStatusFilter = 'all';
    var exportBodyFormat = {
        body: function (data, row, column, node) {
            var $node = $(node);
            var $input = $node.find('input.warning-qty-input');
            if ($input.length) {
                return $input.val() || '';
            }
            return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
        }
    };

    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (fn) {
        return !fn._storeIssuedStatusFilter;
    });
    var statusFilterFn = function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'issuedRequestsTable') {
            return true;
        }
        if (currentStatusFilter === 'all') {
            return true;
        }
        var row = settings.aoData[dataIndex].nTr;
        return $(row).attr('data-status') === currentStatusFilter;
    };
    statusFilterFn._storeIssuedStatusFilter = true;
    $.fn.dataTable.ext.search.push(statusFilterFn);

    if ($.fn.DataTable.isDataTable('#issuedRequestsTable')) {
        $('#issuedRequestsTable').DataTable().destroy();
    }
    var issuedTable = $('#issuedRequestsTable').DataTable({
        pageLength: 10,
        order: [],
        autoWidth: false,
        language: {
            search: 'Search:',
            emptyTable: 'No store item requests found.',
            zeroRecords: 'No matching requests found.'
        },
        dom: "<'d-flex justify-content-between align-items-center mb-3 flex-wrap'<'d-flex align-items-center'B l>f>rt<'d-flex justify-content-between align-items-center mt-3 flex-wrap'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success mr-2',
                title: 'Store Items Handover',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Store Items Handover',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            }
        ]
    });

    function updateShowingCount() {
        $('#showingCountBadge').text(issuedTable.rows({ filter: 'applied' }).count());
    }
    issuedTable.on('draw', updateShowingCount);
    updateShowingCount();

    function persistWarningQty($input) {
        var storeItemId = getStoreItemIdFromEl($input);
        if (!storeItemId) {
            return;
        }
        var warningQty = warningQtyFromInput($input);
        if (warningQty !== null && isNaN(warningQty)) {
            alert('Please enter a valid alert quantity (0 or greater).');
            return;
        }
        var $statusEl = $('.warning-qty-status[data-store-item-id="' + storeItemId + '"]').first();
        saveWarningQtyForItem(storeItemId, warningQty, $input, $statusEl);
    }

    $(document).on('click', '.btn-save-warning-qty', function () {
        var storeItemId = getStoreItemIdFromEl($(this));
        if (!storeItemId) {
            return;
        }
        var $input = $('input.warning-qty-input[data-store-item-id="' + storeItemId + '"]').first();
        persistWarningQty($input);
    });

    $(document).on('keydown', '.warning-qty-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            persistWarningQty($(this));
        }
    });

    $(document).on('input', '.warning-qty-input', function () {
        var $input = $(this);
        var storeItemId = getStoreItemIdFromEl($input);
        if (!storeItemId) {
            return;
        }
        clearTimeout(warningSaveTimer);
        warningSaveTimer = setTimeout(function () {
            var warningQty = warningQtyFromInput($input);
            if (warningQty === null || isNaN(warningQty)) {
                return;
            }
            previewStockAlertsForItem(storeItemId, warningQty);
        }, 200);
    });

    $('.status-filter-btn').on('click', function () {
        $('.status-filter-btn').removeClass('active');
        $(this).addClass('active');
        currentStatusFilter = $(this).data('status');
        issuedTable.draw();
    });
});
</script>
@endpush
