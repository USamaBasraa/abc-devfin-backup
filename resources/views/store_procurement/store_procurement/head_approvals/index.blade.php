@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<div class="container-fluid mt-4">
    <div class="" style="background: none !important; color: #252525 !important;padding:20px !important;">
        <h4 class="mb-0">
            Department Head Approvals
        </h4>
    </div>
    <div class="card shadow-sm">
        <div class="card-body py-3">
          
            {{-- <h3 class="mb-1 font-weight-bold">
                <i class="fa fa-check-circle"></i> Department Head Approvals
            </h3> --}}
            <p class="text-muted mb-0">Approve or reject quotation requests to forward to Store & Procurement Department</p>
        </div>

        <div style="margin-left: 20px;">
            <style>
                /* Tab/Pills Styling */
                #quotationTabs {
                    gap: 0.75rem;
                    border-bottom: none !important;
                }
                #quotationTabs .nav-link {
                    border-radius: 999px;
                    padding: 0.6rem 1.6rem;
                    background: #f3f4f6 !important;
                    color: #6b7280 !important;
                    border: none !important;
                    transition: all 0.2s ease-in-out;
                }
                #quotationTabs .nav-link:not(.active):hover {
                    background: #e5e7eb !important;
                    color: #4b5563 !important;
                }
                #quotationTabs .nav-link.active {
                    background: #4562C3 !important;
                    color: #ffffff !important;
                }
                
                /* Custom table header styling */
                .table-header-blue {
                    background-color: #3F51B5 !important;
                }
                .table-header-blue th {
                    color: #ffffff !important;
                    font-weight: 600 !important;
                    padding: 12px 15px !important;
                    border: none !important;
                    text-align: center !important;
                    vertical-align: middle !important;
                    white-space: nowrap !important;
                }
                .table-header-blue th:first-child {
                    border-top-left-radius: 8px;
                }
                .table-header-blue th:last-child {
                    border-top-right-radius: 8px;
                }

                /* Actions column styling */
                .action-dropdown {
                    position: relative;
                }
                .action-dropdown .btn-action {
                    background: transparent;
                    border: none;
                    border-radius: 16px;
                    padding: 6px 10px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: none;
                    transition: background-color 0.2s ease, box-shadow 0.2s ease;
                    cursor: pointer;
                }
                .action-dropdown .btn-action:focus,
                .action-dropdown .btn-action:hover {
                    background: rgba(63, 81, 181, 0.12);
                    box-shadow: none;
                    outline: none;
                }
                .action-dropdown .btn-action span {
                    width: 6px;
                    height: 6px;
                    background-color: #3F51B5;
                    border-radius: 50%;
                    display: block;
                    margin: 0 1px;
                }
                .action-dropdown .dropdown-menu {
                    border: none;
                    border-radius: 12px;
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
                    padding: 0.5rem 0;
                    min-width: 180px;
                }
                .action-dropdown .dropdown-item {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-weight: 500;
                    padding: 0.45rem 1rem;
                }
                .action-dropdown .dropdown-item:hover {
                    background-color: #f4f6ff;
                }
            </style>
            <ul class="nav nav-pills" id="quotationTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">
                        <i class="fa fa-clock"></i> Pending
                    @if($pendingRequests->count() > 0)
                        <span class="badge badge-light text-dark ml-1">{{ $pendingRequests->count() }}</span>
                    @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">
                         Approved
                    @if($approvedRequests->count() > 0)
                        <span class="badge badge-light text-dark ml-1">{{ $approvedRequests->count() }}</span>
                    @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab">
                         Rejected
                    @if($rejectedRequests->count() > 0)
                        <span class="badge badge-light text-dark ml-1">{{ $rejectedRequests->count() }}</span>
                    @endif
                    </a>
                </li>
            </ul>
        </div>


        <div class="card-body p-0">
            <div class="tab-content" id="quotationTabsContent">
                <script>
                    $(function(){
                        // Restore active tab based on pagination parameter
                        var activeTab = 'pending';
                        if (new URLSearchParams(window.location.search).has('approved_page')) {
                            activeTab = 'approved';
                        } else if (new URLSearchParams(window.location.search).has('rejected_page')) {
                            activeTab = 'rejected';
                        }
                        if (activeTab !== 'pending') {
                            $('#'+activeTab+'-tab').tab('show');
                        }
                    });
                </script>

                <!-- Pending Tab -->
                <div class="tab-pane fade show active p-3" id="pending" role="tabpanel">
                    @if($pendingRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-header-blue">
                                <tr>
                                    <th>Item Title</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Budget Head</th>
                                    <th>Requested By</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $req)
                                @if($req->budgetItem)
                                <tr>
                                    <td class="font-weight-bold">{{ $req->budgetItem->item_title ?? 'N/A' }}</td>
                                    <td>{{ $req->budgetItem->brand ?? '-' }}</td>
                                    <td>{{ $req->budgetItem->model ?? '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ $req->budgetItem->quantity ?? '-' }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format(($req->budgetItem->quantity ?? 0) * ($req->budgetItem->rate ?? 0), 2) }}</td>
                                    <td>{{ $req->budgetHead->head_title ?? '-' }}</td>
                                    <td>{{ $req->requestDepartment->dept_name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ ucfirst($req->budgetItem->budget_type ?? 'N/A') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown d-inline-block">
                                            <button class="btn-action" type="button" id="actionMenu{{ $req->qr_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionMenu{{ $req->qr_id }}">
                                                <button type="button" class="dropdown-item approveBtn" data-id="{{ $req->qr_id }}" data-title="{{ $req->budgetItem->item_title ?? 'Item' }}">
                                                    <i class="fa fa-check text-success"></i>
                                                    Approve
                                                </button>
                                                <button type="button" class="dropdown-item rejectBtn" data-id="{{ $req->qr_id }}" data-title="{{ $req->budgetItem->item_title ?? 'Item' }}">
                                                    <i class="fa fa-times text-danger"></i>
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>No pending requests</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end mt-3">
                        {{-- Pagination removed since we now display simple arrays --}}
                    </div>
                </div>

                <!-- Approved Tab -->
                <div class="tab-pane fade p-3" id="approved" role="tabpanel">
                    @if($approvedRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-header-blue">
                                <tr>
                                    <th>Item Title</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Budget Head</th>
                                    <th>Requested By</th>
                                    <th>Type</th>
                                    <th>Approved On</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedRequests as $req)
                                @if($req->budgetItem)
                                <tr class="table-success">
                                    <td class="font-weight-bold">{{ $req->budgetItem->item_title ?? 'N/A' }}</td>
                                    <td>{{ $req->budgetItem->brand ?? '-' }}</td>
                                    <td>{{ $req->budgetItem->model ?? '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ $req->budgetItem->quantity ?? '-' }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format(($req->budgetItem->quantity ?? 0) * ($req->budgetItem->rate ?? 0), 2) }}</td>
                                    <td>{{ $req->budgetHead->head_title ?? '-' }}</td>
                                    <td>{{ $req->requestDepartment->dept_name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ ucfirst($req->budgetItem->budget_type ?? 'N/A') }}</span>
                                    </td>
                                    <td class="text-center">{{ $req->updated_at ? $req->updated_at->format('d-M-Y') : '-' }}</td>
                                    <td>{{ $req->head_approval_remarks ?? 'No remarks' }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>No approved requests</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end mt-3">
                        {{-- Pagination removed since we now display simple arrays --}}
                    </div>
                </div>

                <!-- Rejected Tab -->
                <div class="tab-pane fade p-3" id="rejected" role="tabpanel">
                    @if($rejectedRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-header-blue">
                                <tr>
                                    <th>Item Title</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Qty</th>
                                    <th>Budget Head</th>
                                    <th>Requested By</th>
                                    <th>Type</th>
                                    <th>Rejected On</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rejectedRequests as $req)
                                @if($req->budgetItem)
                                <tr class="table-danger">
                                    <td class="font-weight-bold">{{ $req->budgetItem->item_title ?? 'N/A' }}</td>
                                    <td>{{ $req->budgetItem->brand ?? '-' }}</td>
                                    <td>{{ $req->budgetItem->model ?? '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ $req->budgetItem->quantity ?? '-' }}</td>
                                    <td>{{ $req->budgetHead->head_title ?? '-' }}</td>
                                    <td>{{ $req->requestDepartment->dept_name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-danger">{{ ucfirst($req->budgetItem->budget_type ?? 'N/A') }}</span>
                                    </td>
                                    <td class="text-center">{{ $req->updated_at ? $req->updated_at->format('d-M-Y') : '-' }}</td>
                                    <td>{{ $req->head_approval_remarks ?? 'No remarks' }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <p>No rejected requests</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end mt-3">
                        {{-- Pagination removed since we now display simple arrays --}}
                    </div>
                </div>
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
                    <h5>Approve this quotation request?</h5>
                    <p class="text-muted">
                        Item: <strong id="approveItemTitle"></strong>
                    </p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">
                        Approval Remarks <span class="text-danger">*</span>
                        <small class="text-muted">(Min 5, Max 2000 characters)</small>
                    </label>
                    <textarea id="approveRemarks" class="form-control" rows="3" maxlength="2000" placeholder="Enter remarks for approval (minimum 5 characters)..." required></textarea>
                    <small class="form-text text-muted">
                        <span id="approveCharCount">0</span> / 2000 characters
                        <span id="approveMinWarning" class="text-danger ml-2" style="display: none;">Minimum 5 characters required</span>
                    </small>
                </div>
                <input type="hidden" id="approveRequestId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmApproveBtn">
                    <i class="fa fa-check"></i> Approve Request
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
                        <i class="fa fa-times-circle"></i> Reject Request
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-0">Item: <strong id="rejectItemTitle"></strong></p>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Rejection Remarks <span class="text-danger">*</span>
                            <small class="text-muted">(Min 5, Max 2000 characters)</small>
                        </label>
                        <textarea id="rejectRemarks" class="form-control" rows="4" maxlength="2000" placeholder="Enter reason for rejection (minimum 5 characters)..." required></textarea>
                        <small class="form-text text-muted">
                            <span id="rejectCharCount">0</span> / 2000 characters
                            <span id="rejectMinWarning" class="text-danger ml-2" style="display: none;">Minimum 5 characters required</span>
                        </small>
                    </div>
                    <input type="hidden" id="rejectRequestId" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Reject Request
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-ban"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('javascript')
<script>

    // Character counter for Approve Remarks
    $('#approveRemarks').on('input', function() {
        let length = $(this).val().length;
        $('#approveCharCount').text(length);

        if (length < 5) {
            $('#approveMinWarning').show();
            $('#confirmApproveBtn').prop('disabled', true);
        } else {
            $('#approveMinWarning').hide();
            $('#confirmApproveBtn').prop('disabled', false);
        }
    });

    // Character counter for Reject Remarks
    $('#rejectRemarks').on('input', function() {
        let length = $(this).val().length;
        $('#rejectCharCount').text(length);

        if (length < 5) {
            $('#rejectMinWarning').show();
            $('#rejectForm button[type="submit"]').prop('disabled', true);
        } else {
            $('#rejectMinWarning').hide();
            $('#rejectForm button[type="submit"]').prop('disabled', false);
        }
    });

    // Approve Button Click - Show confirmation modal
    $(document).on('click', '.approveBtn', function() {
        let requestId = $(this).data('id');
        let itemTitle = $(this).data('title');

        $('#approveRequestId').val(requestId);
        $('#approveItemTitle').text(itemTitle);
        $('#approveRemarks').val('');
        $('#approveCharCount').text('0');
        $('#approveMinWarning').hide();
        $('#confirmApproveBtn').prop('disabled', true);
        $('#approveModal').modal('show');
    });

    // Confirm Approve Button
    $('#confirmApproveBtn').on('click', function() {
        let requestId = $('#approveRequestId').val();
        let remarks = $('#approveRemarks').val().trim();
        let btn = $(this);
        let originalText = btn.html();

        if (!remarks || remarks.length < 5) {
            showToast('warning', 'Please provide at least 5 characters for approval remarks.', 'Remarks Too Short');
            return;
        }

        btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: '{{ route("head.quotation.approve") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_id: requestId,
                head_approval_remarks: remarks
            },
            success: function(res) {
                $('#approveModal').modal('hide');
                showToast('success', res.message, 'Approved');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                btn.html(originalText).prop('disabled', false);
                let errorMsg = xhr.responseJSON?.message ?? 'Error approving request. Please try again.';
                showToast('error', errorMsg, 'Error');
            }
        });
    });

    // Reject Button Click - Show reject modal
    $(document).on('click', '.rejectBtn', function() {
        let requestId = $(this).data('id');
        let itemTitle = $(this).data('title');

        $('#rejectRequestId').val(requestId);
        $('#rejectItemTitle').text(itemTitle);
        $('#rejectRemarks').val('');
        $('#rejectCharCount').text('0');
        $('#rejectMinWarning').hide();
        $('#rejectForm button[type="submit"]').prop('disabled', true);
        $('#rejectModal').modal('show');
    });

    // Reject Form Submit
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();

        let requestId = $('#rejectRequestId').val();
        let remarks = $('#rejectRemarks').val().trim();
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();

        if (!remarks || remarks.length < 5) {
            showToast('warning', 'Please provide at least 5 characters for rejection remarks.', 'Remarks Too Short');
            return;
        }

        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: '{{ route("head.quotation.reject") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_id: requestId,
                head_approval_remarks: remarks
            },
            success: function(res) {
                $('#rejectModal').modal('hide');
                showToast('success', res.message, 'Rejected');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                let errorMsg = xhr.responseJSON?.message ?? 'Error rejecting request. Please try again.';
                showToast('error', errorMsg, 'Error');
            }
        });
    });

    // Reset modals on close
    $('#approveModal').on('hidden.bs.modal', function() {
        $('#approveRemarks').val('');
        $('#approveCharCount').text('0');
        $('#approveMinWarning').hide();
        $('#confirmApproveBtn').html('<i class="fa fa-check"></i> Approve Request').prop('disabled', true);
    });

    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejectRemarks').val('');
        $('#rejectCharCount').text('0');
        $('#rejectMinWarning').hide();
        $('#rejectForm button[type="submit"]').html('<i class="fa fa-times"></i> Reject Request').prop('disabled', true);
    });

</script>
@stop
