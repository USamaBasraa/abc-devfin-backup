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
        padding: 1rem 1.5rem;
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
        font-size: 14px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .store-inv-table tbody tr:nth-child(even) { background: #f4faf9; }
    .container-fluid h4 { color: #17675F; }
    .btn-primary { background: #17675F !important; border-color: #17675F !important; border-radius: 999px; }
    .btn-primary:hover { background: #0f4d47 !important; border-color: #0f4d47 !important; }
    .store-req-filter select.form-control {
        height: 42px !important;
        min-height: 42px !important;
        line-height: 24px !important;
        padding: 8px 40px 8px 16px !important;
        font-size: 14px !important;
        border-radius: 10px !important;
        background-color: #f7fbfa !important;
        border: 1px solid #d5e6e3 !important;
        overflow: visible !important;
    }

    .custom-modal-active {
        display: block !important;
        opacity: 1 !important;
        background: rgba(0, 0, 0, 0.55);
        z-index: 99999 !important;
    }
</style>

<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 font-weight-bold">Store Item Requests</h4>
        <a href="{{ route('store-items.request.form') }}" class="btn btn-primary">
            <i class="fa fa-plus-circle mr-1"></i> New Request
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" action="{{ url()->current() }}" class="form-inline flex-wrap store-req-filter" style="gap:10px;">
                {{-- Financial Year --}}
                <div class="mr-2 mb-1">
                    <label class="font-weight-bold mr-1 mb-0">Financial Year:</label>
                    <select name="financial_year" class="form-control" style="min-width:180px;">
                        <option value="">-- All Years --</option>
                        @foreach($academic_years as $ay)
                            <option value="{{ $ay->ay_id }}"
                                {{ (string)($financial_year ?? '') === (string)$ay->ay_id ? 'selected' : '' }}>
                                {{ $ay->academic_year ?? $ay->year_title ?? $ay->ay_name ?? ('Year #'.$ay->ay_id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Status --}}
                <div class="mr-2 mb-1">
                    <label class="font-weight-bold mr-1 mb-0">Status:</label>
                    <select name="filter_status" class="form-control" style="min-width:150px;">
                        <option value="">-- All Statuses --</option>
                        <option value="pending"      {{ ($filter_status ?? '') === 'pending'      ? 'selected' : '' }}>Pending</option>
                        <option value="issued"       {{ ($filter_status ?? '') === 'issued'       ? 'selected' : '' }}>Issued</option>
                        <option value="acknowledged" {{ ($filter_status ?? '') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                        <option value="rejected"     {{ ($filter_status ?? '') === 'rejected'     ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                {{-- Department --}}
                <div class="mr-2 mb-1">
                    <label class="font-weight-bold mr-1 mb-0">Department:</label>
                    <select name="filter_dept" class="form-control" style="min-width:180px;">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}"
                                {{ (string)($filter_dept ?? '') === (string)$dept->dept_id ? 'selected' : '' }}>
                                {{ $dept->dept_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1" style="display:flex; gap:8px; align-items:flex-end;">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa fa-search mr-1"></i> Filter
                    </button>
                    @if($financial_year || $filter_status || $filter_dept)
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="store-inv-card">
        <div class="store-inv-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fa fa-list-alt mr-2"></i> Department Store Item Requests
                @if($financial_year || $filter_status || $filter_dept)
                    @php
                        $badges = [];
                        if ($financial_year) {
                            $ay = $academic_years->firstWhere('ay_id', $financial_year);
                            if ($ay) $badges[] = $ay->academic_year ?? $ay->year_title ?? $ay->ay_name ?? ('Year #'.$ay->ay_id);
                        }
                        if ($filter_status) $badges[] = ucfirst($filter_status);
                        if ($filter_dept) {
                            $dp = $departments->firstWhere('dept_id', $filter_dept);
                            if ($dp) $badges[] = $dp->dept_name;
                        }
                    @endphp
                    @foreach($badges as $badge)
                        <span class="badge badge-warning text-dark ml-1">{{ $badge }}</span>
                    @endforeach
                @endif
            </span>
            <span class="badge badge-light text-dark">{{ count($requests) }}</span>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover mb-0 store-inv-table" id="storeRequestsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Request #</th>
                        <th>Item</th>
                        <th>Department</th>
                        <th>Requested By</th>
                        <th class="text-center">Req Qty</th>
                        <th class="text-center">Issued Qty</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-center no-export" style="min-width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        @php 
                            $st = strtolower(trim((string) ($req->status ?? 'pending')));
                            $issuedQty = (float) ($req->issued_qty ?? $req->requested_qty ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>SIR-{{ $req->sir_id }}</strong></td>
                            <td>
                                <div class="font-weight-bold">{{ $req->item_title ?? '-' }}</div>
                                @if(!empty($req->request_remarks))
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($req->request_remarks, 60) }}</small>
                                @endif
                            </td>
                            <td>{{ $req->request_dept_name ?? '-' }}</td>
                            <td>{{ $req->requested_by ?? '-' }}</td>
                            <td class="text-center">{{ number_format((float) ($req->requested_qty ?? 0), 2) }} {{ $req->unit ?? '' }}</td>
                            <td class="text-center">
                                @if(in_array($st, ['issued', 'acknowledged'], true))
                                    <strong class="text-success">{{ number_format((float) ($req->issued_qty ?? 0), 2) }} {{ $req->unit ?? '' }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($st === 'acknowledged')
                                    <span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i> Acknowledged</span>
                                @elseif($st === 'issued')
                                    <span class="badge badge-info"><i class="fa fa-truck mr-1"></i> Issued by Store</span>
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
                                @if($st === 'issued')
                                    <button type="button"
                                            class="btn btn-sm btn-success px-3"
                                            onclick="openAckModal({{ $req->sir_id }}, '{{ addslashes($req->item_title ?? '') }}', '{{ number_format($issuedQty, 2) }} {{ addslashes($req->unit ?? '') }}')">
                                        <i class="fa fa-check mr-1"></i> Acknowledge
                                    </button>
                                @elseif($st === 'acknowledged')
                                    <div class="text-success font-weight-bold" style="font-size: 13px;">
                                        <i class="fa fa-check-circle mr-1"></i> Received
                                    </div>
                                    @if(!empty($req->acknowledged_at))
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($req->acknowledged_at)->format('d-M-Y H:i') }}</small>
                                    @endif
                                @elseif($st === 'rejected')
                                    <small class="text-danger">{{ $req->approval_remarks ?? 'Rejected' }}</small>
                                @else
                                    <small class="text-muted"><i class="fa fa-hourglass-half mr-1"></i> Waiting for Store</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Acknowledge Receipt -->
<div class="modal fade" id="ackModal" tabindex="-1" role="dialog" aria-labelledby="ackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="ackModalLabel">
                    <i class="fa fa-check-circle mr-2"></i> Acknowledge Item Receipt
                </h5>
                <button type="button" class="close text-white" onclick="closeAckModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ackForm" onsubmit="submitAckForm(event)">
                @csrf
                <input type="hidden" id="ackRequestId">
                <div class="modal-body p-4">
                    <div class="alert alert-success py-2 px-3 small mb-3">
                        <strong>Request:</strong> <span id="ackSirId"></span> — <span id="ackItemTitle"></span>
                        <br>
                        <strong>Issued Quantity:</strong> <span id="ackItemQty" class="font-weight-bold"></span>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Remarks / Condition (Optional)</label>
                        <textarea id="ackRemarksInput"
                                  class="form-control"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Received in good condition..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" onclick="closeAckModal()">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" id="submitAckBtn">
                        <i class="fa fa-check mr-1"></i> Confirm Received
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('page-scripts')
<script>
window.openAckModal = function(id, title, qty) {
    document.getElementById('ackRequestId').value = id;
    document.getElementById('ackSirId').innerText = 'SIR-' + id;
    document.getElementById('ackItemTitle').innerText = title || '-';
    document.getElementById('ackItemQty').innerText = qty || '-';
    document.getElementById('ackRemarksInput').value = 'Received in good condition';

    var modal = document.getElementById('ackModal');
    modal.classList.add('custom-modal-active');
};

window.closeAckModal = function() {
    var modal = document.getElementById('ackModal');
    modal.classList.remove('custom-modal-active');
};

window.submitAckForm = function(e) {
    if (e) e.preventDefault();
    var id = document.getElementById('ackRequestId').value;
    var remarks = document.getElementById('ackRemarksInput').value || 'Received in good condition';

    var btn = document.getElementById('submitAckBtn');
    btn.disabled = true;
    btn.innerText = 'Confirming...';

    $.ajax({
        url: '{{ url("/store-items/requests") }}/' + id + '/acknowledge',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            remarks: remarks
        },
        success: function (res) {
            window.closeAckModal();
            alert(res.message || 'Store items receipt acknowledged successfully.');
            window.location.reload();
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check mr-1"></i> Confirm Received';
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || xhr.responseText || 'Failed to acknowledge request';
            alert(msg);
        }
    });
};

document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('custom-modal-active')) {
        window.closeAckModal();
    }
});

$(function () {
    var exportBodyFormat = {
        body: function (data) {
            return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
        }
    };

    if ($.fn.DataTable.isDataTable('#storeRequestsTable')) {
        $('#storeRequestsTable').DataTable().destroy();
    }
    $('#storeRequestsTable').DataTable({
        pageLength: 10,
        order: [],
        autoWidth: false,
        language: {
            search: 'Search:',
            emptyTable: 'No store item requests found for your department.',
            zeroRecords: 'No matching requests found.'
        },
        dom: "<'d-flex justify-content-between align-items-center mb-3 flex-wrap'<'d-flex align-items-center'B l>f>rt<'d-flex justify-content-between align-items-center mt-3 flex-wrap'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success mr-2',
                title: 'Store Item Requests',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Store Item Requests',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            }
        ]
    });
});
</script>
@endpush
