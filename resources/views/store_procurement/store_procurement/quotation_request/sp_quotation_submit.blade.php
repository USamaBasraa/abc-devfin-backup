@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<style>
    #itemDropdown {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 2px;
    }
    #itemDropdown .list-group-item {
        cursor: pointer;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 15px;
    }
    #itemDropdown .list-group-item i {
        margin-right: 8px;
    }
    #itemDropdown .list-group-item:hover {
        background-color: #3e5871;
        color: white;
    }
    #itemDropdown .list-group-item:hover i {
        color: white !important;
    }
    #itemDropdown .list-group-item:last-child {
        border-bottom: none;
    }

    .spinner-border.d-none {
        display: none !important;
    }

    /* Vendor Chips Styling */
    .vendor-chip {
        display: inline-block;
        background-color: #3e5871;
        color: white;
        padding: 6px 12px;
        margin: 4px;
        border-radius: 20px;
        font-size: 14px;
    }
    .vendor-chip i {
        margin-left: 8px;
        cursor: pointer;
    }
    .vendor-chip i:hover {
        color: #ffcccc;
    }
    [id^="excelUploadSection"] {
        display: none !important;
    }
    .selected-vendors-area {
        min-height: 40px;
        padding: 8px;
        border: 1px dashed #ddd;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
    .selected-vendors-area:empty::before {
        content: 'No vendors selected yet';
        color: #999;
        font-style: italic;
        font-size: 13px;
    }

    /* Email Sent Row Styling */
    .email-sent-row {
        /* background-color: #e8f5e9 !important; */
        position: relative;
    }

    /* Professional Button Badge - Top Right Corner */
    .btn-email-badge {
        position: relative;
        display: inline-block;
    }

    .btn-email-badge::after {
        content: '✉';
        position: absolute;
        top: -10px;
        right: -10px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.3);
        border: 2px solid white;
        z-index: 10;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    /* Readonly Field Styling */
        .readonly-field[readonly],
        .readonly-field[disabled] {
            background-color: #f0f0f0 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

    /* Modern Table Styling - Matching Second Screenshot */
    #quotationRequestsTable {
        border-collapse: separate;
        border-spacing: 0;
        border: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Dark Blue Header */
    #quotationRequestsTable thead th {
        background-color: #3F51B5 !important;
        color: #FFFFFF !important;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        padding: 15px 12px;
        border: none !important;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    /* Remove table borders */
    #quotationRequestsTable tbody tr {
        border: none !important;
    }

    #quotationRequestsTable tbody td {
        border: none !important;
        border-bottom: 1px solid #f0f0f0 !important;
        padding: 14px 12px;
        color: #333333;
        font-size: 14px;
        vertical-align: middle;
    }

    /* Alternating Row Colors - White and Light Gray */
    #quotationRequestsTable tbody tr:nth-child(odd) {
        background-color: #FFFFFF;
    }

    #quotationRequestsTable tbody tr:nth-child(even) {
        background-color: #F5F5F5;
    }

    /* Override email-sent-row to work with alternating colors */
    .email-sent-row {
        /* background-color: #e8f5e9 !important; */
    }

    /* Remove hover effect or make it subtle */
    #quotationRequestsTable tbody tr:hover {
        /* background-color: #E8EAF6 !important; */
    }

    /* Ensure email-sent rows maintain green on hover */
    #quotationRequestsTable tbody tr.email-sent-row:hover {
        /* background-color: #c8e6c9 !important; */
    }

    /* Clean table responsive wrapper */
    .table-responsive {
        border: none;
        box-shadow: none;
    }

    /* Filter Section Styling - Matching Second Screenshot */
    .select2-filter,
    #filterItemTitle {
        background-color: #F5F5F5 !important;
        border: none !important;
        border-radius: 4px;
        padding: 8px 12px;
    }

    .select2-container--default .select2-selection--single {
        background-color: #F5F5F5 !important;
        border: none !important;
        border-radius: 4px;
        height: 38px;
        padding: 4px 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #333;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    /* Action Column - Display buttons horizontally */
    #quotationRequestsTable tbody td:last-child {
        white-space: nowrap;
    }

    #quotationRequestsTable tbody td:last-child button,
    #quotationRequestsTable tbody td:last-child a {
        display: inline-block;
        vertical-align: middle;
    }

</style>




<div class="container-fluid mt-4">
    <input type="hidden" id="quotationRequestType" value="{{ $requestType ?? '' }}">
    <div class="card" style="box-shadow: none !important;">
        {{-- <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-file-text-o"></i> Submit Vendor Quotation</h5>
        </div> --}}
            <div class="card-header" style="background: none !important; color: #252525 !important;">
                <h4 class="mb-0">{{ $pageTitle ?? 'Submit Vendor Quotation' }}
                </h4>
            </div>
        <div class="card-body" style="padding-left: 0px !important; padding-right: 0px !important;">
            {{-- Filters Section --}}
            <div class="row mb-3" style="padding-left: 20px; padding-right: 20px;">
                <div class="col-md-3">
                    <label><strong>Department</strong></label>
                    <select id="filterDepartment" class="form-control select2-filter">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label><strong>Budget Head</strong></label>
                    <select id="filterBudgetHead" class="form-control select2-filter">
                        <option value="">-- All Budget Heads --</option>
                        @foreach($budgetHeads as $head)
                            <option value="{{ $head->category_id }}">{{ $head->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label><strong>Item Title</strong></label>
                    <div style="position: relative;">
                        <input type="text" id="filterItemTitle" class="form-control" placeholder="Type to search item title..." autocomplete="off">
                        <input type="hidden" id="selectedItemId">
                        <div id="itemDropdown" class="list-group" style="position: absolute; z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; display: none;"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label><br>
                    <div style="display: flex; gap: 10px;">
                        <button id="btnFilter" class="btn btn-sm" style="background-color: #4A69D9; color: white; border: none; border-radius: 4px; padding: 8px 20px;">
                            Search
                        </button>
                        <button id="btnReset" class="btn btn-sm" style="background-color: white; color: #333; border: 1px solid #999; border-radius: 4px; padding: 8px 20px;">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <hr>

            {{-- DataTable with Lazy Loading --}}
            <div class="table-responsive" style="padding-left: 20px; padding-right: 20px;">
                <table id="quotationRequestsTable" class="table table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>Sr.</th>
                            <th>QR ID</th>
                            <th>Item</th>
                            <th>Budget</th>
                            <th>Requested By</th>
                            <th>Purchase Mode</th>
                            <th>Quantity</th>
                            <th>Unit Rate</th>
                            <th>Est. Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="quotationTableBody"></tbody>
                </table>
            </div>

            {{-- Loading Indicator --}}
            <div id="loadingIndicator" class="text-center py-3 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading more records...</p>
            </div>

            {{-- No More Data --}}
            <div id="noMoreData" class="text-center py-3 d-none">
                <p class="text-muted">No more records to load.</p>
            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 1100px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3e5871; color: white;">
                <h5 class="modal-title"><i class="fa fa-info-circle"></i> Quotation Request Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                {{-- Content loaded via AJAX --}}
            </div>
        </div>
    </div>
</div>

{{-- PO Generate Approval Modal --}}
<div class="modal fade" id="poApprovalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3e5871; color: white;">
                <h5 class="modal-title"><i class="fa fa-check-circle"></i> PO Approval Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="poApprovalContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading approval details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-info d-none" id="btnViewApprovalDetails" style="display:none;">
                    <i class="fa fa-eye"></i> View Details
                </button>
                <button type="button" class="btn btn-success" id="confirmPOGenerate">
                    <i class="fa fa-check"></i> Generate PO
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function(){

    let currentPage = 1;
    let isLoading = false;
    let hasMoreData = true;
    let serialNumber = 1;
    const perPage = 20;
    const PO_FOCUS_KEY = 'sp_po_focus_qr_id';
    const PO_FOCUS_TS_KEY = 'sp_po_focus_qr_ts';

    function storePoFocusRow(qrId) {
        if (!qrId) return;
        sessionStorage.setItem(PO_FOCUS_KEY, String(qrId));
        sessionStorage.setItem(PO_FOCUS_TS_KEY, String(Date.now()));
    }

    function applyPoFocusHighlight() {
        const focusQrId = sessionStorage.getItem(PO_FOCUS_KEY);
        const focusTs = parseInt(sessionStorage.getItem(PO_FOCUS_TS_KEY) || "0", 10);

        if (!focusQrId || !focusTs) return;

        if ((Date.now() - focusTs) > 30000) {
            sessionStorage.removeItem(PO_FOCUS_KEY);
            sessionStorage.removeItem(PO_FOCUS_TS_KEY);
            return;
        }

        const $row = $(`#quotationTableBody tr[data-qr-id="${focusQrId}"]`);
        if (!$row.length) return;

        sessionStorage.removeItem(PO_FOCUS_KEY);
        sessionStorage.removeItem(PO_FOCUS_TS_KEY);

        if ($row[0] && typeof $row[0].scrollIntoView === "function") {
            $row[0].scrollIntoView({ behavior: "smooth", block: "center" });
        }

        let ticks = 0;
        const maxTicks = 6; // ~4.2s gentle blink
        $row.css("transition", "background-color 0.55s ease, box-shadow 0.55s ease");

        const timer = setInterval(function () {
            if (ticks >= maxTicks) {
                clearInterval(timer);
                $row.css({ backgroundColor: "", boxShadow: "", transition: "" });
                return;
            }

            if (ticks % 2 === 0) {
                $row.css({
                    backgroundColor: "#f0fff5",
                    boxShadow: "inset 0 0 0 2px rgba(40, 167, 69, 0.6)"
                });
            } else {
                $row.css({ backgroundColor: "", boxShadow: "", transition: "" });
            }

            ticks++;
        }, 700);
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/`/g, '&#96;');
    }

    function formatMultiline(value) {
        return escapeHtml(value).replace(/\r?\n/g, '<br>');
    }

    // Load Data Function
    function loadData(page = 1, append = false) {
        if (isLoading || (!hasMoreData && append)) return;

        isLoading = true;
        $('#loadingIndicator').removeClass('d-none');
        $('#noMoreData').addClass('d-none');

        // Reset serial number if not appending (new search/filter)
        if (!append) {
            serialNumber = 1;
        }

        $.ajax({
            url: '{{ route("quotation.requests.data") }}',
            type: 'GET',
            data: {
                page: page,
                per_page: perPage,
                department: $('#filterDepartment').val(),
                budget_head: $('#filterBudgetHead').val(),
                item_title: $('#filterItemTitle').val(),
                request_type: $('#quotationRequestType').val()
            },
            success: function(response) {
                if (response.data.length > 0) {
                    let rows = '';
                    response.data.forEach(function(item) {
                        let rowClass = item.email_sent ? 'email-sent-row' : '';
                        let buttonClass = item.email_sent ? 'btn btn-sm btn-info btnViewDetails btn-email-badge' : 'btn btn-sm btn-info btnViewDetails';

                        // PO Status - Show badge if generated, status button if PO exists
                        let poActionHtml = '';
                        if (item.po_generated) {
                            // Show button if PO already generated - redirect to PO detail page
                            poActionHtml = `<a href="{{ url('purchase-orders') }}/${item.po_id}" class="btn btn-sm btn-success ml-1" style="padding: 6px 12px; font-size: 0.85rem;">
                                <i class="fa fa-check-circle"></i> ${item.po_number}
                            </a>`;
                        } else if (item.po_approval_completed) {
                            // All approvals completed - Show green "Generate PO" button
                            poActionHtml = `<button class="btn btn-sm btn-success btnViewPOStatus ml-1" data-qr-id="${item.qr_id}">
                                <i class="fa fa-file-text-o"></i> Generate PO
                            </button>`;
                        } else {
                            // Placeholder: will show "View Status" only if approvals count > 0 (checked via AJAX)
                            poActionHtml = `<span class="po-status-placeholder" data-qr-id="${item.qr_id}"></span>`;
                        }

                    let unreadChatHtml = '';
                    if (item.unread_chat_count && Number(item.unread_chat_count) > 0) {
                        unreadChatHtml = `<span class="badge badge-danger ml-1 chat-unread chat-unread-${item.qr_id}">
                            <i class="fa fa-bell"></i> ${item.unread_chat_count}
                        </span>`;
                    }

                        // Format price values
                        let unitRate = item.rate ? Number(item.rate).toLocaleString() : '-';
                        let estimatedTotal = item.estimated_total ? Number(item.estimated_total).toLocaleString() : '-';

                        rows += `<tr class="${rowClass}" data-qr-id="${item.qr_id}">
                            <td>${serialNumber++}</td>
                            <td>${item.qr_id}</td>
                            <td>${item.item_title}</td>
                            <td>${item.budget_head}</td>
                            <td>${item.department}</td>
                            <td>${item.purchase_mode}</td>
                            <td>${item.quantity}</td>
                            <td class="text-right">${unitRate}</td>
                            <td class="text-right"><strong>${estimatedTotal}</strong></td>
                            <td>
                                <button class="${buttonClass}" data-id="${item.qr_id}">
                                    <i class="fa fa-eye"></i> View
                                    ${unreadChatHtml}
                                </button>
                                ${poActionHtml}
                            </td>
                        </tr>`;
                    });

                    if (append) {
                        $('#quotationTableBody').append(rows);
                    } else {
                        $('#quotationTableBody').html(rows);
                    }

                    // After rows render, populate status placeholders based on approvals count
                    $('.po-status-placeholder').each(function() {
                        const $holder = $(this);
                        if ($holder.data('processed')) return;
                        const qrId = $holder.data('qr-id');
                        $holder.data('processed', true);
                        $.ajax({
                            url: '{{ route("quotation.po-approvals") }}',
                            method: 'GET',
                            data: { qr_id: qrId },
                            success: function(resp) {
                                if (resp && resp.success && Array.isArray(resp.approvals) && resp.approvals.length > 0) {
                                    $holder.html(
                                        `<button class="btn btn-sm btn-primary btnViewPOStatus ml-1" data-qr-id="${qrId}">
                                            <i class="fa fa-tasks"></i> View Status
                                        </button>`
                                    );
                                } else {
                                    $holder.remove(); // no approvals -> keep empty
                                }
                            },
                            error: function() {
                                // On error, do nothing (leave placeholder empty)
                                $holder.remove();
                            }
                        });
                    });

                    applyPoFocusHighlight();

                    currentPage = page;
                    hasMoreData = response.has_more;

                    if (!response.has_more) {
                        $('#noMoreData').removeClass('d-none');
                    }
                } else {
                    if (!append) {
                        $('#quotationTableBody').html('<tr><td colspan="10" class="text-center">No records found.</td></tr>');
                    }
                    hasMoreData = false;
                    $('#noMoreData').removeClass('d-none');
                }

                isLoading = false;
                $('#loadingIndicator').addClass('d-none');
            },
            error: function() {
                showToast('error', 'Failed to load quotation requests data.', 'Loading Error');
                isLoading = false;
                $('#loadingIndicator').addClass('d-none');
            }
        });
    }

    function markChatMessagesRead(requestId) {
        $.ajax({
            url: '{{ route("sp.quotations.chat.read") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_id: requestId
            },
            success: function() {
                $('.chat-unread-' + requestId).remove();
                $('#chatUnreadBadge' + requestId).remove();
            }
        });
    }

    // Infinite Scroll on Window
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (!isLoading && hasMoreData) {
                loadData(currentPage + 1, true);
            }
        }
    });

    // Initial Load
    loadData(1, false);

    // Apply Filters
    $('#btnFilter').click(function(){
        currentPage = 1;
        hasMoreData = true;
        serialNumber = 1;
        $('#noMoreData').addClass('d-none');
        loadData(1, false);
    });

    // Reset Filters
    $('#btnReset').click(function(){
        $('#filterDepartment').val('');
        $('#filterBudgetHead').val('');
        $('#filterItemTitle').val('');
        currentPage = 1;
        hasMoreData = true;
        serialNumber = 1;
        $('#noMoreData').addClass('d-none');
        loadData(1, false);
    });

    // Live Search for Item Title with Debounce
    let searchTimeout;
    $('#filterItemTitle').on('input', function(){
        let searchText = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (searchText.length < 2) {
            $('#itemDropdown').hide().html('');
            return;
        }

        // Show loading in dropdown
        $('#itemDropdown').show().html('<div class="list-group-item text-center"><i class="fa fa-spinner fa-spin"></i> Searching...</div>');

        // Debounce: Wait 1.5 seconds after user stops typing
        searchTimeout = setTimeout(function(){
            $.ajax({
                url: '{{ route("quotation.search-items") }}',
                type: 'GET',
                data: { q: searchText },
                success: function(items) {
                    if (items.length > 0) {
                        let html = '';
                        items.forEach(function(item){
                            html += `<a href="#" class="list-group-item list-group-item-action item-select-option" data-id="${item.item_id}" data-title="${item.item_title}">
                                <i class="fa fa-tag text-primary"></i> ${item.item_title}
                            </a>`;
                        });
                        $('#itemDropdown').html(html);
                    } else {
                        $('#itemDropdown').html('<div class="list-group-item text-muted"><i class="fa fa-exclamation-circle"></i> No items found</div>');
                    }
                },
                error: function(){
                    $('#itemDropdown').html('<div class="list-group-item text-danger">Error searching items</div>');
                }
            });
        }, 1500); // 1.5 second delay
    });

    // Select Item from Dropdown
    $(document).on('click', '.item-select-option', function(e){
        e.preventDefault();
        let itemTitle = $(this).data('title');
        let itemId = $(this).data('id');

        $('#filterItemTitle').val(itemTitle);
        $('#selectedItemId').val(itemId);
        $('#itemDropdown').hide().html('');

        // Automatically trigger filter
        currentPage = 1;
        hasMoreData = true;
        serialNumber = 1;
        $('#noMoreData').addClass('d-none');
        loadData(1, false);
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e){
        if (!$(e.target).closest('#filterItemTitle, #itemDropdown').length) {
            $('#itemDropdown').hide();
        }
    });

    // Enter key support for Item Title search
    $('#filterItemTitle').keypress(function(e){
        if(e.which == 13) {
            $('#itemDropdown').hide();
            $('#btnFilter').click();
        }
    });

    // Auto-apply filter on dropdown change
    $('#filterDepartment, #filterBudgetHead').change(function(){
        currentPage = 1;
        hasMoreData = true;
        serialNumber = 1;
        $('#noMoreData').addClass('d-none');
        loadData(1, false);
    });

    // View Details Button - Load Modal Content via AJAX
    $(document).on('click', '.btnViewDetails', function(){
        let requestId = $(this).data('id');
        let modalBody = $('#modalBodyContent');

        modalBody.html('');
        $('#detailModal').modal('show');
        modalBody.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

        $.get('{{ route("quotation.requests.details", ["id" => 0]) }}'.replace('/0/', '/' + requestId + '/'), function(res){
            $('#modalBodyContent').html(res.html);

            // Initialize Select2 for vendor email dropdown
            $('.vendor-email-select').select2({
                placeholder: '-- Select a Vendor --',
                allowClear: true,
                width: '100%',
                templateResult: function (state) {
                    if (!state.id) {
                        return state.text;
                    }

                    const noEmail = $(state.element).data('no-email') == 1;
                    if (noEmail) {
                        return $('<span style="color:#dc3545;font-weight:600;">' + state.text + '</span>');
                    }

                    return state.text;
                },
                templateSelection: function (state) {
                    return state.text || '';
                }
            });

            // Initialize Select2 for vendor selection dropdown
            $('.selectVendor.select2-filter').select2({
                placeholder: '-- Select Vendor --',
                allowClear: true,
                width: '100%'
            });

            // Debug: Log available vendors after modal loads
            setTimeout(function() {
                let vendorSelect = $(`.selectVendor[data-request-id="${requestId}"]`);
                if(vendorSelect.length > 0) {
                    let availableVendors = [];
                    vendorSelect.find('option').each(function() {
                        if($(this).val() !== '') {
                            availableVendors.push($(this).text());
                        }
                    });
                    console.log('Available vendors for request', requestId, ':', availableVendors);
                }
            }, 1000);

            // Load submitted quotations when modal opens
            loadSubmittedQuotations(requestId);

            $('#quotationTabs a[data-toggle="tab"]').off('shown.bs.tab.chat').on('shown.bs.tab.chat', function (e) {
                if ($(this).attr('href') === '#chat') {
                    markChatMessagesRead(requestId);
                }
            });

            let chatBox = $('#chatBox' + requestId);
            if (chatBox.length) {
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }
        }).fail(function(){
            $('#modalBodyContent').html('<div class="alert alert-danger">Failed to load details.</div>');
        });
    });

    $('#detailModal').on('hidden.bs.modal', function () {
        $('#modalBodyContent').html('');
    });

    // Vendor Dropdown Selection - Add Chip
    $(document).on('change', '.vendor-email-select', function(){
        let requestId = $(this).data('request-id');
        let vendorId = $(this).val();
        let vendorName = $(this).find('option:selected').data('name');
        let chipsArea = $(`#selectedVendorsArea${requestId}`);
        let $this = $(this);

        if(!vendorId) return;

        // Check if already added
        if(chipsArea.find(`.vendor-chip[data-vendor-id="${vendorId}"]`).length > 0) {
            showToast('warning', 'This vendor is already selected.', 'Already Selected');
            $this.val(null).trigger('change'); // Reset select2
            return;
        }

        // Add chip
        let chip = `<span class="vendor-chip" data-vendor-id="${vendorId}" data-vendor-name="${vendorName}">
            <i class="fa fa-user"></i> ${vendorName}
            <i class="fa fa-times remove-vendor-chip"></i>
        </span>`;

        chipsArea.append(chip);
        $this.val(null).trigger('change'); // Reset select2 properly
    });

    // Remove Vendor Chip
    $(document).on('click', '.remove-vendor-chip', function(){
        $(this).closest('.vendor-chip').fadeOut(200, function(){
            $(this).remove();
        });
    });

    function syncSubmitVendorDropdown(requestId, selectedVendorMeta) {
        let submitSelect = $(`.selectVendor[data-request-id="${requestId}"]`);
        if (!submitSelect.length) {
            return;
        }

        let existingIds = new Set();
        submitSelect.find('option').each(function() {
            if ($(this).val()) {
                existingIds.add(String($(this).val()));
            }
        });

        if (submitSelect.find('option').length === 0) {
            submitSelect.append('<option value="">-- Select Vendor --</option>');
        } else {
            submitSelect.find('option:first').text('-- Select Vendor --');
        }

        selectedVendorMeta.forEach(function(vendor) {
            let id = String(vendor.id);
            if (!existingIds.has(id)) {
                submitSelect.append(new Option(vendor.name, id, false, false));
                existingIds.add(id);
            }
        });

        submitSelect.prop('disabled', false).trigger('change.select2');

        let submitPane = submitSelect.closest('.tab-pane');
        submitPane.find('.alert-warning').filter(function() {
            return $(this).text().toLowerCase().indexOf('please send the rfq email') !== -1;
        }).remove();
    }

    // Handle "Send Email to Selected Vendors" (from Modal)
    $(document).on('click', '.sendEmailBtn', function(){
        let $btn = $(this);

        // Guard: prevent duplicate requests on rapid multiple clicks.
        if ($btn.data('loading') || $btn.prop('disabled')) {
            return;
        }

        let requestId = $btn.data('request-id');
        let selectedVendors = [];
        let selectedVendorMeta = [];

        $(`#selectedVendorsArea${requestId} .vendor-chip`).each(function(){
            let vendorId = $(this).data('vendor-id');
            selectedVendors.push(vendorId);
            selectedVendorMeta.push({
                id: vendorId,
                name: $(this).data('vendor-name') || 'Vendor'
            });
        });

        if(selectedVendors.length === 0) {
            showToast('warning', 'Please select at least one vendor before sending emails.', 'No Vendor Selected');
            return;
        }

        let originalBtnHtml = $btn.html();
        $btn.data('loading', true)
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin mr-1"></i> Sending Email, please wait...');

        $.ajax({
            url: '{{ route("vendor.quotations.send-email") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                request_id: requestId,
                vendors: selectedVendors
            },
            success: function(res) {
                showToast('success', res.message, 'Email Sent Successfully!');

                // Update the specific row in table (add green background + email badge)
                updateRowAfterEmailSent(requestId);
                syncSubmitVendorDropdown(requestId, selectedVendorMeta);

                // Refresh Email History section in modal
                refreshEmailHistorySection(requestId);

                // Clear chips after sending
                $(`#selectedVendorsArea${requestId}`).html('');
                // Reset select2 dropdown
                $('.vendor-email-select').val(null).trigger('change');
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON.message ?? 'Failed to send emails to vendors.', 'Email Error');
            },
            complete: function() {
                $btn.data('loading', false)
                    .prop('disabled', false)
                    .html(originalBtnHtml);
            }
        });
    });

    // Update Quotation Button - Show Form to Update Existing Quotation
    function isStoreItemsRequestType() {
        return ($('#quotationRequestType').val() || '').toString().trim() === 'store_items';
    }

    function applyStoreItemFieldMode(formElement, requestId) {
        if (!isStoreItemsRequestType()) {
            return;
        }

        const vendorSelect = $(`.selectVendor[data-request-id="${requestId}"]`);
        const unit = (vendorSelect.data('item-unit') || '').toString().trim();
        const appUnit = (vendorSelect.data('item-app-unit') || '').toString().trim();

        const brandLabel = formElement.find('input[name="brand"]').closest('.col-md-3').find('strong').first();
        const modelLabel = formElement.find('input[name="model"]').closest('.col-md-3').find('strong').first();

        brandLabel.text('Unit');
        modelLabel.text('App Unit');

        formElement.find('input[name="brand"]')
            .attr('placeholder', 'Unit')
            .val(unit)
            .prop('readonly', true);

        formElement.find('input[name="model"]')
            .attr('placeholder', 'App Unit')
            .val(appUnit)
            .prop('readonly', true);
    }

    $(document).on('click', '.btnUpdateQuotation', function(){
        let vendorId = $(this).data('vendor-id');
        let requestId = $(this).data('request-id');
        let existingData = $(this).data() || {};
        let area = $('#quotationArea' + requestId);
        let excelUploadSection = $(`#excelUploadSection${requestId}`);

        // Excel uploads disabled per latest instructions
        excelUploadSection.hide();

        // Show the form (same as new quotation)
        area.html(`
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form class="quotationForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quotation_request_id" value="${requestId}">
                        <input type="hidden" name="vendor_id" value="${vendorId}">

                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> <strong>Updating Quotation:</strong> Update the details below to create a new version.
                        </div>

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> <strong>Note:</strong> Price is calculated automatically from Quantity × Rate.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-tag text-primary"></i> <strong>Item Title</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_title" class="form-control" placeholder="Enter item title" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-industry text-secondary"></i> <strong>Brand</strong>
                                </label>
                                <input type="text" name="brand" class="form-control" placeholder="Enter brand">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-cube text-muted"></i> <strong>Model</strong>
                                </label>
                                <input type="text" name="model" class="form-control" placeholder="Enter model">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-sort-numeric-up text-dark"></i> <strong>Quantity</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="quantity" class="form-control" placeholder="Enter quantity" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-balance-scale text-info"></i> <strong>Rate</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="rate" class="form-control" placeholder="Enter rate" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-money text-success"></i> <strong>Price</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Auto calculated" readonly required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-globe text-info"></i> <strong>Currency</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="currency" class="form-control" required>
                                    <option value="">-- Select Currency --</option>
                                    <option value="PKR">PKR (₨)</option>
                                    <option value="USD">USD ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-credit-card text-primary"></i> <strong>Advance Payment</strong> <span class="text-danger">*</span>
                                </label>
                                <select name="advance_required" class="form-control" required>
                                    <option value="">-- Select Option --</option>
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-percent text-success"></i> <strong>Advance %</strong>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="advance_percent" class="form-control" placeholder="Enter percentage">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Required when advance payment is Yes.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-calendar text-warning"></i> <strong>Delivery Time</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="delivery_time" class="form-control" placeholder="Enter delivery time" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-shield text-success"></i> <strong>Warranty</strong>
                                </label>
                                <input type="text" name="warranty" class="form-control" placeholder="Enter warranty">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-file-text text-secondary"></i> <strong>Specifications</strong>
                                </label>
                                <textarea name="specifications" class="form-control" rows="3" placeholder="Enter specifications"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-comment text-primary"></i> <strong>Remarks</strong>
                                </label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fa fa-paperclip text-muted"></i> <strong>Attachment</strong>
                                </label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                            </div>
                        </div>

                        <div class="response-box mt-3"></div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-success btn-lg submitBtn px-4">
                                <i class="fa fa-paper-plane"></i> Update Quotation
                                <span class="spinner-border spinner-border-sm ml-2 d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `);

        let formElement = area.find('.quotationForm');

        formElement.find('input[name="item_title"]').val(existingData.itemTitle || '');
        formElement.find('input[name="brand"]').val(existingData.brand || '');
        formElement.find('input[name="model"]').val(existingData.model || '');
        formElement.find('input[name="quantity"]').val(existingData.quantity || '');
        formElement.find('input[name="rate"]').val(existingData.rate || '');
        formElement.find('input[name="price"]').val(existingData.price || '');

        if(existingData.currency) {
            formElement.find('select[name="currency"]').val(existingData.currency);
        }

        formElement.find('input[name="delivery_time"]').val(existingData.deliveryTime || '');
        formElement.find('input[name="warranty"]').val(existingData.warranty || '');
        formElement.find('textarea[name="specifications"]').val(existingData.specifications || '');
        formElement.find('textarea[name="remarks"]').val(existingData.remarks || '');

        let advanceRequired = (existingData.advanceRequired || 'no').toString().toLowerCase();
        formElement.find('select[name="advance_required"]').val(advanceRequired);

        let advancePercentValue = existingData.advancePercent || '';
        if(typeof advancePercentValue === 'string') {
            advancePercentValue = advancePercentValue.replace('%', '');
        }
        formElement.find('input[name="advance_percent"]').val(advancePercentValue);

        applyStoreItemFieldMode(formElement, requestId);
        handleAdvanceFields(formElement);
        updatePriceField(formElement);
    });

    // Vendor Selection Dropdown (from Modal)
    $(document).on('change', '.selectVendor', function(){
        let vendorId = $(this).val();
        let requestId = $(this).data('request-id');
        let area = $('#quotationArea' + requestId);
        let excelUploadSection = $(`#excelUploadSection${requestId}`);

        if(!vendorId) {
            area.html('');
            excelUploadSection.hide();
            return;
        }

        // Excel uploads disabled per latest instructions
        excelUploadSection.hide();

        $.get('{{ route("vendor.quotations.check") }}', { vendor_id: vendorId, quotation_request_id: requestId }, function(res){
            if(res.exists) {
                let q = res.quotation;
                let currencySymbol = q.currency === 'PKR' ? '₨' : '$';
                let currencyName = q.currency === 'PKR' ? 'PKR' : 'USD';
                let versionCount = res.history_versions || 1;
                let versionText = versionCount > 1 ? ` (${versionCount} Versions)` : '';

                const rawPrice = q.price !== null && q.price !== undefined ? String(q.price).trim() : '';
                const rawItemTitle = (q.item_title || '').trim();
                const rawBrand = (q.brand || '').trim();
                const rawModel = (q.model || '').trim();
                const rawQuantity = q.quantity !== null && q.quantity !== undefined ? String(q.quantity).trim() : '';
                const rawRate = q.rate !== null && q.rate !== undefined ? String(q.rate).trim() : '';
                const rawAdvanceRequired = q.advance_required === 'yes' ? 'Yes' : (q.advance_required === 'no' ? 'No' : '');
                const rawAdvancePercent = q.advance_percent !== null && q.advance_percent !== undefined ? String(q.advance_percent).trim() : '';
                const rawDeliveryTime = (q.delivery_time || '').trim();
                const rawWarranty = (q.warranty || '').trim();
                const rawSpecifications = (q.specifications || '').trim();
                const rawRemarks = (q.remarks || '').trim();

                const itemTitleDisplay = rawItemTitle !== '' ? escapeHtml(rawItemTitle) : '-';
                const priceDisplay = rawPrice !== '' && !isNaN(rawPrice) ? parseFloat(rawPrice).toLocaleString() : '-';
                const brandDisplay = rawBrand !== '' ? escapeHtml(rawBrand) : '-';
                const modelDisplay = rawModel !== '' ? escapeHtml(rawModel) : '-';
                const quantityDisplay = rawQuantity !== '' ? escapeHtml(rawQuantity) : '-';
                const rateDisplay = rawRate !== '' ? escapeHtml(rawRate) : '-';
                const advanceRequiredDisplay = rawAdvanceRequired !== '' ? escapeHtml(rawAdvanceRequired) : '-';
                let advancePercentDisplay = '-';
                if (rawAdvancePercent !== '') {
                    advancePercentDisplay = rawAdvancePercent.includes('%')
                        ? escapeHtml(rawAdvancePercent)
                        : escapeHtml(rawAdvancePercent + '%');
                }
                const deliveryTimeDisplay = rawDeliveryTime !== '' ? escapeHtml(rawDeliveryTime) : '-';
                const warrantyDisplay = rawWarranty !== '' ? escapeHtml(rawWarranty) : '-';
                const specificationsDisplay = rawSpecifications !== '' ? formatMultiline(rawSpecifications) : '-';
                const remarksDisplay = rawRemarks !== '' ? formatMultiline(rawRemarks) : '-';

                let priceValueHtml = '<strong>-</strong>';
                if (priceDisplay !== '-') {
                    priceValueHtml = `<strong class="text-success">${currencySymbol} ${priceDisplay}</strong><small class="text-muted ml-1">${currencyName}</small>`;
                }

                area.html(`
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa fa-check-circle mr-2"></i> <strong>Quotation Already Submitted${versionText}</strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-light btnUpdateQuotation" data-vendor-id="${vendorId}" data-request-id="${requestId}">
                                <i class="fa fa-edit"></i> Update Quotation
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-tag fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Item Title</small>
                                            <strong>${itemTitleDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-money fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Price</small>
                                            ${priceValueHtml}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-industry fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Brand</small>
                                            <strong>${brandDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-cube fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Model</small>
                                            <strong>${modelDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-sort-numeric-up fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Quantity</small>
                                            <strong>${quantityDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-balance-scale fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Rate</small>
                                            <strong>${rateDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-credit-card fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Advance Payment</small>
                                            <strong>${advanceRequiredDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-percent fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Advance %</small>
                                            <strong>${advancePercentDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-calendar fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Delivery Time</small>
                                            <strong>${deliveryTimeDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-shield fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Warranty</small>
                                            <strong>${warrantyDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3"></div>
                                <div class="col-md-4 mb-3"></div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-cogs fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Specifications</small>
                                            <strong>${specificationsDisplay}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <span class="text-dark mr-3"><i class="fa fa-comment fa-lg"></i></span>
                                        <div>
                                            <small class="text-muted d-block">Remarks</small>
                                            <p class="mb-0">${remarksDisplay}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                let updateBtn = area.find('.btnUpdateQuotation');
                if(updateBtn.length) {
                    updateBtn.data({
                        itemTitle: q.item_title || '',
                        brand: q.brand || '',
                        model: q.model || '',
                        quantity: q.quantity || '',
                        rate: q.rate || '',
                        price: q.price || '',
                        currency: q.currency || '',
                        deliveryTime: q.delivery_time || '',
                        warranty: q.warranty || '',
                        specifications: q.specifications || '',
                        remarks: q.remarks || '',
                        advanceRequired: q.advance_required || 'no',
                        advancePercent: q.advance_percent || ''
                    });
                }
            } else {
                area.html(`
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form class="quotationForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="quotation_request_id" value="${requestId}">
                                <input type="hidden" name="vendor_id" value="${vendorId}">

                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> <strong>Note:</strong> Fill the form below. Price updates automatically from Quantity × Rate.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-tag text-primary"></i> <strong>Item Title</strong> <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="item_title" class="form-control" placeholder="Enter item title" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-industry text-secondary"></i> <strong>Brand</strong>
                                        </label>
                                        <input type="text" name="brand" class="form-control" placeholder="Enter brand">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-cube text-muted"></i> <strong>Model</strong>
                                        </label>
                                        <input type="text" name="model" class="form-control" placeholder="Enter model">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-sort-numeric-up text-dark"></i> <strong>Quantity</strong> <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" name="quantity" class="form-control" placeholder="Enter quantity" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-balance-scale text-info"></i> <strong>Rate</strong> <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" name="rate" class="form-control" placeholder="Enter rate" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-money text-success"></i> <strong>Price</strong> <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="Auto calculated" readonly required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-globe text-info"></i> <strong>Currency</strong> <span class="text-danger">*</span>
                                        </label>
                                        <select name="currency" class="form-control" required>
                                            <option value="">-- Select Currency --</option>
                                            <option value="PKR">PKR (₨)</option>
                                            <option value="USD">USD ($)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-credit-card text-primary"></i> <strong>Advance Payment</strong> <span class="text-danger">*</span>
                                        </label>
                                        <select name="advance_required" class="form-control" required>
                                            <option value="">-- Select Option --</option>
                                            <option value="no" selected>No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fa fa-percent text-success"></i> <strong>Advance %</strong>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" max="100" name="advance_percent" class="form-control" placeholder="Enter percentage">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Required when advance payment is Yes.</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-calendar text-primary"></i> <strong>Delivery Date</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="delivery_time" class="form-control" placeholder="Enter delivery date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-shield text-info"></i> <strong>Warranty</strong>
                                    </label>
                                    <input type="text" name="warranty" class="form-control" placeholder="Enter warranty">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-cogs text-warning"></i> <strong>Specifications</strong>
                                    </label>
                                    <textarea name="specifications" class="form-control" rows="3" placeholder="Enter specifications"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-comment text-secondary"></i> <strong>Remarks</strong>
                                    </label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Enter remarks"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-paperclip text-danger"></i> <strong>Attachment</strong> <small class="text-muted">(optional)</small>
                                    </label>
                                    <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted: PDF, DOC, DOCX, JPG, PNG (Max 2MB)</small>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg submitBtn px-5">
                                        <i class="fa fa-paper-plane"></i> Submit Quotation
                                        <span class="spinner-border spinner-border-sm ml-2 d-none" role="status"></span>
                                    </button>
                                </div>
                                <div class="quotationResponse mt-3"></div>
                            </form>
                        </div>
                    </div>
                `);

                let newForm = area.find('.quotationForm');
                applyStoreItemFieldMode(newForm, requestId);
                handleAdvanceFields(newForm);
                updatePriceField(newForm);
            }
        });
    });

    // Quotation Form Submission
    $(document).on('submit', '.quotationForm', function(e){
        e.preventDefault();

        let form = $(this)[0];
        let formData = new FormData(form);
        let responseBox = $(this).find('.quotationResponse');
        let submitBtn = $(this).find('.submitBtn');
        let spinner = submitBtn.find('.spinner-border');
        let btnIcon = submitBtn.find('.fa-paper-plane');

        // Disable button and show spinner
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnIcon.addClass('d-none');

        $.ajax({
            url: '{{ route("vendor.quotations.store") }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                showToast('success', res.message, 'Quotation Submitted!');
                responseBox.html(`<div class="alert alert-success"><i class="fa fa-check-circle"></i> ${res.message}</div>`);
                form.reset();

                // Re-enable button and hide spinner
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnIcon.removeClass('d-none');

                setTimeout(function(){
                    $('#detailModal').modal('hide');
                    loadData(1, false);
                }, 1500);
            },
            error: function (xhr) {
                // Re-enable button and hide spinner
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnIcon.removeClass('d-none');

                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let msg = '<ul class="mb-0">';
                    $.each(errors, function (key, val) {
                        msg += `<li>${val[0]}</li>`;
                    });
                    msg += '</ul>';
                    responseBox.html(`<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <strong>Validation Errors:</strong>${msg}</div>`);
                    showToast('error', 'Please check the form for errors.', 'Validation Failed');
                } else {
                    responseBox.html(`<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Failed to submit quotation.</div>`);
                    showToast('error', 'Failed to submit quotation. Please try again.', 'Submission Failed');
                }
            }
        });
    });

    function updatePriceField(formElement) {
        if(!formElement || formElement.length === 0) {
            return;
        }

        let quantityField = formElement.find('input[name="quantity"]');
        let rateField = formElement.find('input[name="rate"]');
        let priceField = formElement.find('input[name="price"]');

        if(priceField.length === 0) {
            return;
        }

        let quantity = parseFloat(quantityField.val());
        let rate = parseFloat(rateField.val());

        if(!isNaN(quantity) && !isNaN(rate)) {
            let calculated = (quantity * rate).toFixed(2);
            priceField.val(calculated);
        } else {
            priceField.val('');
        }
    }

    $(document).on('input', '.quotationForm input[name="quantity"], .quotationForm input[name="rate"]', function(){
        let formElement = $(this).closest('form');
        updatePriceField(formElement);
    });

    $(document).on('blur', '.quotationForm input[name="quantity"], .quotationForm input[name="rate"]', function(){
        let formElement = $(this).closest('form');
        updatePriceField(formElement);
    });

    $(document).on('input', '.quotationForm input[name="advance_percent"]', function(){
        let percentField = $(this);
        let rawValue = percentField.val();
        let value = parseFloat(rawValue);

        if(Number.isNaN(value)) {
            percentField.data('advance-warning-shown', false);
            return;
        }

        if(value > 100) {
            percentField.val(100);
            if(!percentField.data('advance-warning-shown')) {
                showToast('warning', 'Advance percentage cannot be more than 100.', 'Invalid Advance %');
                percentField.data('advance-warning-shown', true);
            }
        } else if(value < 0) {
            percentField.val(0);
        } else {
            percentField.data('advance-warning-shown', false);
        }
    });

    function handleAdvanceFields(formElement) {
        if(!formElement || formElement.length === 0) {
            return;
        }

        let advanceSelect = formElement.find('select[name="advance_required"]');
        let percentField = formElement.find('input[name="advance_percent"]');

        if(advanceSelect.length === 0 || percentField.length === 0) {
            return;
        }

        if(advanceSelect.val() === 'yes') {
            percentField.prop('disabled', false);
            percentField.attr('required', true);
        } else {
            percentField.prop('disabled', true);
            percentField.removeAttr('required');
            percentField.val('');
        }
    }

    $(document).on('change', '.quotationForm select[name="advance_required"]', function(){
        let formElement = $(this).closest('form');
        handleAdvanceFields(formElement);
    });

    $(document).on('submit', '.chatForm', function(e){
        e.preventDefault();

        let form = $(this);
        let requestId = form.data('request-id');
        let messageField = form.find('textarea[name="message_text"]');
        let message = $.trim(messageField.val());
        let responseBox = form.find('.chatResponse');
        let submitBtn = form.find('.chatSubmitBtn');

        if(message.length === 0) {
            responseBox.html('<span class="text-danger">Please write a message.</span>');
            return;
        }

        submitBtn.prop('disabled', true);
        responseBox.html('<span class="text-muted"><i class="fa fa-spinner fa-spin"></i> Sending...</span>');

        $.ajax({
            url: '{{ route("sp.quotations.chat.store") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                let chatBox = $('#chatBox' + requestId);
                let newHtml = '<div class="qr-chat-entry text-right">';
                newHtml += '<div class="small text-muted">' + (res.chat.entry_username || '');
                if (res.chat.created_at) {
                    newHtml += ' • ' + res.chat.created_at;
                }
                newHtml += '</div>';
                newHtml += '<div class="qr-chat-bubble qr-chat-bubble-self">' + (res.chat.message_text || '') + '</div>';
                newHtml += '</div>';

                chatBox.append(newHtml);
                messageField.val('');
                responseBox.html('<span class="text-success">Message sent.</span>');
                if (chatBox.length) {
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                }
            },
            error: function(xhr) {
                let msg = 'Unable to send message.';
                if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }
                responseBox.html('<span class="text-danger">'+msg+'</span>');
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // 🔹 Function to Update Row After Email Sent (Real-time UI Update)
    function updateRowAfterEmailSent(requestId) {
        // Find the row with matching QR ID in the table
        $('#quotationTableBody tr').each(function(){
            let rowQrId = $(this).find('td:eq(1)').text().trim(); // QR ID is in 2nd column

            if(rowQrId == requestId) {
                // Add green background class
                $(this).addClass('email-sent-row');

                // Find the View button and add email badge class
                let viewButton = $(this).find('.btnViewDetails');
                if(!viewButton.hasClass('btn-email-badge')) {
                    viewButton.addClass('btn-email-badge');
                }

                return false; // Break the loop once found
            }
        });
    }

    // 🔹 Function to Refresh Email History Section in Modal
    function refreshEmailHistorySection(requestId) {
        $.ajax({
            url: '{{ route("vendor.quotations.email-history") }}',
            method: 'GET',
            data: { qr_id: requestId },
            success: function(res) {
                // Replace the Email History section content
                $('#emailHistorySection').html(res.html);
            },
            error: function() {
                console.log('Failed to refresh email history.');
            }
        });
    }

    function formatDateTime(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            const cleaned = value.replace('T', ' ').split('.')[0];
            return cleaned !== '' ? cleaned : '-';
        }

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const day = String(date.getDate()).padStart(2, '0');
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();

        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const hoursFormatted = String(hours).padStart(2, '0');

        return `${day} ${month} ${year}, ${hoursFormatted}:${minutes} ${ampm}`;
    }

    // 🔹 Function to Load Submitted Quotations
    function loadSubmittedQuotations(requestId) {
        $.ajax({
            url: '{{ route("vendor.quotations.submitted") }}',
            type: 'GET',
            cache: false,
            data: {
                qr_id: requestId,
                _: Date.now()
            }
        }).done(function(response) {
            let container = $(`#submittedQuotationsList${requestId}`);
            let countBadge = $(`#submittedCount${requestId}`);

            console.log('Submitted quotations response:', response);

            if(response.quotations && response.quotations.length > 0) {
                let html = '<div id="quotationsAccordion' + requestId + '">';

                response.quotations.forEach(function(quotation, index) {
                    let accordionId = 'quotation' + quotation.vq_id;
                    let currencySymbol = quotation.currency === 'PKR' ? '₨' : '$';
                    let formattedPrice = parseFloat(quotation.price).toLocaleString();
                    let vendorName = quotation.vendor ? quotation.vendor.vendor_name : 'Unknown Vendor';

                    // Get version count from history
                    let versionCount = quotation.version_count || 1;
                    let versionBadge = versionCount > 1
                        ? `<span class="badge badge-info ml-2">${versionCount} Versions</span>`
                        : '';

                    html += `
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-link text-left p-0 text-decoration-none" type="button"
                                        data-toggle="collapse" data-target="#collapse${accordionId}"
                                        aria-expanded="false" aria-controls="collapse${accordionId}"
                                        style="width: 80%; text-align: left; border: none; background: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-user text-primary mr-2"></i>
                                        <strong>${vendorName}</strong>
                                        ${versionBadge}
                                    </div>
                                </button>
                                <small class="text-muted">${formatDateTime(quotation.created_at)}</small>
                            </div>
                            <div id="collapse${accordionId}" class="collapse" aria-labelledby="heading${accordionId}"
                                 data-parent="#quotationsAccordion${requestId}">
                                <div class="mt-3 pl-3">
                                    ${versionCount > 1 ? `
                                        <!-- Show Version History if multiple versions exist -->
                                        <div class="mb-3">
                                            <h6 class="text-primary"><i class="fa fa-history"></i> Version History (${versionCount} Versions)</h6>
                                            <div class="list-group">
                                    ` : ''}

                                    ${quotation.version_history && quotation.version_history.length > 0 ? quotation.version_history.map(function(version, vIndex) {
                                        let vCurrencySymbol = version.CURRENCY === 'PKR' ? '₨' : '$';
                                        let vFormattedPrice = parseFloat(version.PRICE).toLocaleString();
                                        let isCurrent = version.IS_CURRENT == 1;
                                        let currentBadge = isCurrent ? '<span class="badge badge-success ml-2">Current</span>' : '';
                                        let versionCreatedAt = version.CREATED_AT || version.created_at || null;
                                        let versionDate = formatDateTime(versionCreatedAt);

                                        return `
                                            <div class="list-group-item ${isCurrent ? 'border-success' : ''} mb-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            Version ${version.VERSION_NUMBER}
                                                            ${currentBadge}
                                                        </h6>
                                                        <small class="text-muted">${versionDate}</small>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Price:</strong> ${vCurrencySymbol} ${vFormattedPrice}</p>
                                                        <p class="mb-1"><strong>Currency:</strong> ${version.CURRENCY || '-'}</p>
                                                        <p class="mb-1"><strong>Quantity:</strong> ${version.QUANTITY_QUOTED || '-'}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Delivery:</strong> ${version.DELIVERY_TIME || '-'}</p>
                                                        <p class="mb-1"><strong>Warranty:</strong> ${version.WARRANTY || '-'}</p>
                                                        ${version.REMARKS ? `<p class="mb-1"><strong>Remarks:</strong> ${version.REMARKS}</p>` : ''}
                                                    </div>
                                                </div>
                                                ${version.SPECIFICATIONS ? `
                                                    <div class="mt-2">
                                                        <small><strong>Specifications:</strong> ${version.SPECIFICATIONS}</small>
                                                    </div>
                                                ` : ''}
                                            </div>
                                        `;
                                    }).join('') : `
                                        <!-- Current Version Only -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-money text-success"></i> Price Details</h6>
                                                <p><strong>Amount:</strong> ${currencySymbol} ${formattedPrice}</p>
                                                <p><strong>Currency:</strong> ${quotation.currency}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-calendar text-primary"></i> Delivery</h6>
                                                <p><strong>Date:</strong> ${quotation.delivery_time || 'Not specified'}</p>
                                                <p><strong>Warranty:</strong> ${quotation.warranty || 'Not specified'}</p>
                                            </div>
                                        </div>
                                        ${quotation.specifications ? `
                                            <div class="mt-3">
                                                <h6><i class="fa fa-cogs text-warning"></i> Specifications</h6>
                                                <p class="text-muted">${quotation.specifications}</p>
                                            </div>
                                        ` : ''}
                                        ${quotation.remarks ? `
                                            <div class="mt-3">
                                                <h6><i class="fa fa-comment text-info"></i> Remarks</h6>
                                                <p class="text-muted">${quotation.remarks}</p>
                                            </div>
                                        ` : ''}
                                    `}

                                    ${versionCount > 1 ? `
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                container.html(html);
                countBadge.text(response.quotations.length);
            } else {
                container.html(`
                    <div class="text-center p-4">
                        <i class="fa fa-inbox fa-3x text-muted"></i>
                        <p class="text-muted mt-2">No quotations submitted yet</p>
                        <small class="text-muted">Submitted quotations will appear here</small>
                    </div>
                `);
                countBadge.text('0');
            }
        }).fail(function() {
            $(`#submittedQuotationsList${requestId}`).html(`
                <div class="alert alert-danger m-3">
                    <i class="fa fa-exclamation-triangle"></i> Failed to load submitted quotations
                </div>
            `);
        });
    }

    // 🔹 Function to Clear File Input
    window.clearFileInput = function(requestId) {
        let fileInput = $(`#vendorExcelFile${requestId}`);
        let chooseButton = fileInput.siblings('button[onclick*="click()"]');
        fileInput.val('');
        chooseButton.html('<i class="fa fa-file-excel-o"></i> Choose Excel File');
    };

    // 🔹 Excel File Auto-Parse on Selection
    $(document).on('change', '.vendorExcelFile', function(){
        let requestId = $(this).data('request-id');
        let fileInput = this;
        let chooseButton = $(this).siblings('button[onclick*="click()"]');

        if(!fileInput.files || fileInput.files.length === 0) {
            return;
        }

        let file = fileInput.files[0];

        // Update button to show selected file name
        chooseButton.html(`<i class="fa fa-file-excel-o"></i> ${file.name}`);

        // Check file type
        if(!file.name.toLowerCase().endsWith('.xlsx') && !file.name.toLowerCase().endsWith('.xls')) {
            showToast('error', 'Please select an Excel file (.xlsx or .xls)', 'Invalid File Type');
            clearFileInput(requestId);
            return;
        }

        // Check file size (max 5MB)
        if(file.size > 5 * 1024 * 1024) {
            showToast('error', 'File size must be less than 5MB', 'File Too Large');
            clearFileInput(requestId);
            return;
        }

        let reader = new FileReader();

        reader.onload = function(e) {
            try {
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, {type: 'array'});
                let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                let jsonData = XLSX.utils.sheet_to_json(firstSheet, {header: 1});

                // Parse Excel data - Clean structure (no vendor name needed)
                // Row 0: Price       | [Value]
                // Row 1: Currency    | [Value]
                // Row 2: Delivery Date | [Value]
                // Row 3: Warranty    | [Value]
                // Row 4: Specifications | [Value]
                // Row 5: Remarks     | [Value]

                console.log('Excel data rows:', jsonData.length);
                console.log('Excel data:', jsonData);

                if(jsonData.length < 6) {
                    showToast('error', 'Invalid Excel format. Please use the template with all 6 fields.', 'Invalid Format');
                    return;
                }

                // Read values from column B (index 1)
                let price = jsonData[0][1]; // B1 (Row 1: Price)
                let currency = jsonData[1][1]; // B2 (Row 2: Currency)
                let deliveryDateRaw = jsonData[2][1]; // B3 (Row 3: Delivery Date)
                let warranty = jsonData[3][1] || ''; // B4 (Row 4: Warranty)
                let specifications = jsonData[4][1] || ''; // B5 (Row 5: Specifications)
                let remarks = jsonData[5][1] || ''; // B6 (Row 6: Remarks)

                // Convert Excel date number to proper date string
                let deliveryDate = deliveryDateRaw;
                if(typeof deliveryDateRaw === 'number') {
                    // Excel date serial number conversion
                    deliveryDate = convertExcelDateToDateString(deliveryDateRaw);
                } else if(typeof deliveryDateRaw === 'string' && deliveryDateRaw.includes('/')) {
                    // Handle date strings like "12/15/2025"
                    let dateParts = deliveryDateRaw.split('/');
                    if(dateParts.length === 3) {
                        deliveryDate = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
                    }
                }

                console.log('Parsed price:', price);
                console.log('Parsed currency:', currency);
                console.log('Parsed delivery date (raw):', deliveryDateRaw);
                console.log('Parsed delivery date (converted):', deliveryDate);

                // Check if template file was uploaded without filling
                if(!price || !currency) {
                    showToast('error', 'Please fill Price and Currency in the Excel file.', 'Required Fields Missing');
                    return;
                }

                // Check if vendor is already selected from dropdown
                let vendorSelect = $(`.selectVendor[data-request-id="${requestId}"]`);
                let currentVendorId = vendorSelect.val();
                let quotationArea = $(`#quotationArea${requestId}`);

                // Check if form already exists (vendor already selected)
                let formExists = quotationArea.find('.quotationForm').length > 0;

                if(!currentVendorId) {
                    showToast('warning', 'Please select a vendor from dropdown first, then upload Excel file.', 'Select Vendor First');
                    return;
                }

                if(!formExists) {
                    // Form doesn't exist, trigger change to load form
                    vendorSelect.trigger('change');

                    // Wait for form to load, then fill it
                    setTimeout(function(){
                        fillFormWithExcelData(requestId, price, currency, deliveryDate, warranty, specifications, remarks);
                        // Clear file input after successful processing
                        clearFileInput(requestId);
                    }, 1500);
                } else {
                    // Form already exists, just fill it directly
                    fillFormWithExcelData(requestId, price, currency, deliveryDate, warranty, specifications, remarks);
                    // Clear file input after successful processing
                    clearFileInput(requestId);
                }

            } catch(error) {
                console.error('Error parsing Excel:', error);
                showToast('error', 'Failed to parse Excel file. Please check the format.', 'Parsing Error');
                clearFileInput(requestId);
            }
        };

        reader.onerror = function() {
            showToast('error', 'Failed to read the file.', 'File Read Error');
            clearFileInput(requestId);
        };

        reader.readAsArrayBuffer(file);
    });

    // Helper function to fill form with Excel data
    function fillFormWithExcelData(requestId, price, currency, deliveryDate, warranty, specifications, remarks) {
        console.log('Filling form with data for request:', requestId);

        let quotationArea = $(`#quotationArea${requestId}`);

        // Check if form exists
        if(quotationArea.find('.quotationForm').length === 0) {
            console.error('Form not found in quotationArea');
            showToast('error', 'Form not found. Please select a vendor first.', 'Error');
            return;
        }

        console.log('Form found, removing readonly attributes');

        // Keep fields readonly but remove styling - data will be filled but user can't edit
        quotationArea.find('.readonly-field').removeClass('readonly-field');

        // Prevent readonly select fields from being changed
        quotationArea.find('select[readonly]').on('focus mousedown', function(e) {
            e.preventDefault();
            return false;
        });

        // Fill form fields
        console.log('Filling fields with:', {price, currency, deliveryDate, warranty, specifications, remarks});

        // Fill form fields
        let priceField = quotationArea.find('input[name="price"]');
        let currencyField = quotationArea.find('select[name="currency"]');
        let deliveryField = quotationArea.find('input[name="delivery_time"]');
        let warrantyField = quotationArea.find('input[name="warranty"]');
        let specsField = quotationArea.find('textarea[name="specifications"]');
        let remarksField = quotationArea.find('textarea[name="remarks"]');

        console.log('Fields found:', {
            price: priceField.length,
            currency: currencyField.length,
            delivery: deliveryField.length,
            warranty: warrantyField.length,
            specs: specsField.length,
            remarks: remarksField.length
        });

        priceField.val(price);
        currencyField.val(currency); // Keep readonly
        deliveryField.val(convertExcelDate(deliveryDate));
        warrantyField.val(warranty);
        specsField.val(specifications);
        remarksField.val(remarks);

        console.log('Fields filled. Checking if still readonly:', {
            price: priceField.attr('readonly'),
            currency: currencyField.attr('readonly'),
            delivery: deliveryField.attr('readonly')
        });

        // Update info message
        let alertBox = quotationArea.find('.alert-info');
        if(alertBox.length > 0) {
            alertBox.removeClass('alert-info').addClass('alert-success')
                .html('<i class="fa fa-check-circle"></i> <strong>Success!</strong> Excel data loaded automatically. Review and submit.');
        }

        let targetForm = quotationArea.find('.quotationForm');
        handleAdvanceFields(targetForm);
        updatePriceField(targetForm);

        console.log('Form filled successfully');
    }

    // Helper function to convert Excel date to YYYY-MM-DD
    function convertExcelDate(excelDate) {
        if(!excelDate) return '';

        // If already in YYYY-MM-DD format
        if(typeof excelDate === 'string' && excelDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return excelDate;
        }

        // If Excel serial date number
        if(typeof excelDate === 'number') {
            let date = XLSX.SSF.parse_date_code(excelDate);
            return `${date.y}-${String(date.m).padStart(2, '0')}-${String(date.d).padStart(2, '0')}`;
        }

        // Try to parse as date string
        try {
            let parsedDate = new Date(excelDate);
            if(!isNaN(parsedDate.getTime())) {
                let year = parsedDate.getFullYear();
                let month = String(parsedDate.getMonth() + 1).padStart(2, '0');
                let day = String(parsedDate.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
        } catch(e) {
            console.error('Date conversion error:', e);
        }

        return excelDate;
    }

    // 🔹 Generate Excel Template using JavaScript (SheetJS)
    $(document).on('click', '.downloadTemplateBtn', function(){
        try {
            // Create a new workbook
            let wb = XLSX.utils.book_new();

            // Create clean worksheet data with default values (no vendor name needed)
            let wsData = [
                ['Price', ''],
                ['Currency', 'PKR'],
                ['Delivery Date', new Date().toISOString().split('T')[0]],
                ['Warranty', '1 Year'],
                ['Specifications', ''],
                ['Remarks', '']
            ];

            // Create worksheet
            let ws = XLSX.utils.aoa_to_sheet(wsData);

            // Set column widths
            ws['!cols'] = [
                { wch: 20 },  // Column A - Labels
                { wch: 50 }   // Column B - Values
            ];

            // Add data validation for Currency dropdown
            let currencyValidation = {
                type: 'list',
                allowBlank: false,
                formula1: '"PKR,USD"',
                showDropDown: true,
                showErrorMessage: true,
                errorTitle: 'Invalid Currency',
                error: 'Please select PKR or USD',
                errorStyle: 'stop'
            };

            // Apply validation to Currency cell (B2)
            if (!ws['!dataValidation']) {
                ws['!dataValidation'] = [];
            }
            ws['!dataValidation'].push({
                sqref: 'B2',
                ...currencyValidation
            });

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Quotation');

            // Generate file name with current date
            let today = new Date().toISOString().split('T')[0];
            let fileName = 'Vendor_Quotation_' + today + '.xlsx';

            // Download the file
            XLSX.writeFile(wb, fileName);

            showToast('success', 'Excel template downloaded successfully!', 'Download Complete');

        } catch(error) {
            console.error('Error generating Excel:', error);
            showToast('error', 'Failed to generate Excel template.', 'Generation Error');
        }
    });

    // 🔹 View PO Status Button Click Handler - Show All Approval Statuses
    $(document).on('click', '.btnViewPOStatus', function(){
        let qrId = $(this).data('qr-id');

        // Store QR ID in modal for later use
        $('#confirmPOGenerate').data('qr-id', qrId);

        // Show modal and load approval details
        $('#poApprovalModal').modal('show');
        $('#poApprovalContent').html(`
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading approval status...</p>
            </div>
        `);

        // Load approval details via AJAX
        $.ajax({
            url: '{{ route("quotation.po-approvals") }}',
            method: 'GET',
            data: { qr_id: qrId },
            success: function(response) {
                if (response.success) {
                    let html = '';

                    if (response.approvals && response.approvals.length > 0) {
                        // Show "View Details" only when there are approvals
                        $('#btnViewApprovalDetails').addClass('d-none').hide();
                        html += `
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="30%">PO Approval Details</th>
                                            <th width="20%">Approval Date</th>
                                            <th width="30%">Remarks</th>
                                            <th width="20%" class="text-center">Current Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        // Helper to format role professionally
                        const formatRole = function(role) {
                            if (!role) return '';
                            const map = {
                                'hod': 'HOD',
                                'gm': 'GM',
                                'vc': 'VC',
                                'purchase_committee_member': 'Purchase Committee Member',
                                'purchase_committee_line_department': 'Purchase Committee Line Department',
                                'purchase_committee_chair_person': 'Purchase Committee Chair Person'
                            };
                            const key = String(role).toLowerCase();
                            if (map[key]) return map[key];
                            // Title case fallback
                            return String(role)
                                .replace(/_/g, ' ')
                                .toLowerCase()
                                .replace(/\b\w/g, s => s.toUpperCase());
                        };

                        response.approvals.forEach(function(approval, index) {
                            let badgeClass, iconClass, statusText, rowClass;

                            if (approval.status === 'approved') {
                                badgeClass = 'badge-success';
                                iconClass = 'fa-check-circle';
                                statusText = 'Approved';
                                rowClass = '';
                            } else if (approval.status === 'rejected') {
                                badgeClass = 'badge-danger';
                                iconClass = 'fa-times-circle';
                                statusText = 'Rejected';
                                rowClass = 'table-danger';
                            } else {
                                badgeClass = 'badge-warning';
                                iconClass = 'fa-clock-o';
                                statusText = 'Pending';
                                rowClass = 'table-warning';
                            }

                            html += `
                                <tr class="${rowClass}">
                                    <td>
                                        <strong>${index + 1}. ${approval.approver_name}</strong>
                                        ${approval.role ? `<small class="text-muted ml-1">(${formatRole(approval.role)})</small>` : ''}
                                    </td>
                                    <td>${approval.approval_date}</td>
                                    <td>
                                        ${approval.remarks ? `<small class="text-muted">${approval.remarks}</small>` : '<small class="text-muted">-</small>'}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge ${badgeClass}">
                                            <i class="fa ${iconClass}"></i> ${statusText}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        // Check if any approval is rejected
                        let hasRejected = response.approvals.some(approval => approval.status === 'rejected');

                        // Show appropriate message and button
                        if (response.po_generated) {
                            html += `
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle"></i> <strong>PO Already Generated!</strong>
                                </div>
                            `;
                            // Hide Generate button
                            $('#confirmPOGenerate').hide();
                        } else if (hasRejected) {
                            html += `
                                <div class="alert alert-danger mb-0">
                                    <i class="fa fa-times-circle"></i> <strong>PO Request Rejected!</strong> One or more approvers have rejected this request.
                                </div>
                            `;
                            // Hide Generate button
                            $('#confirmPOGenerate').hide();
                        } else if (response.all_approved) {
                            html += `
                                <div class="alert alert-success mb-0">
                                    <i class="fa fa-check-circle"></i> <strong>All approvals completed!</strong> Ready to generate PO.
                                </div>
                            `;
                            // Show Generate button
                            $('#confirmPOGenerate').show();
                        } else {
                            html += `
                                <div class="alert alert-warning mb-0">
                                    <i class="fa fa-hourglass-half"></i> <strong>Approval in progress.</strong> Waiting for pending approvals.
                                </div>
                            `;
                            // Hide Generate button
                            $('#confirmPOGenerate').hide();
                        }
                    } else {
                        // Hide "View Details" when there are no approvals
                        $('#btnViewApprovalDetails').addClass('d-none').hide();
                        html += '<div class="alert alert-warning">No approvals found.</div>';
                        $('#confirmPOGenerate').hide();
                    }

                    $('#poApprovalContent').html(html);
                } else {
                    $('#poApprovalContent').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> ${response.message}
                        </div>
                    `);
                    $('#confirmPOGenerate').hide();
                }
            },
            error: function(xhr) {
                $('#poApprovalContent').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-times-circle"></i> Failed to load approval details.
                    </div>
                `);
                $('#confirmPOGenerate').hide();
            }
        });
    });

    // 🔹 Confirm PO Generate from Modal
    $(document).on('click', '#confirmPOGenerate', function(){
        let qrId = $(this).data('qr-id');
        let button = $(this);
        let originalHtml = button.html();

        // Disable button and show loading
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: '{{ route("quotation.generate-po") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_id: qrId
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message, 'PO Generated Successfully!');

                    // Close modal
                    $('#poApprovalModal').modal('hide');

                    // Reset button state
                    button.prop('disabled', false).html(originalHtml);

                    // Reload data immediately
                    currentPage = 1;
                    hasMoreData = true;
                    serialNumber = 1;
                        storePoFocusRow(qrId);
                        loadData(1, false);
                } else {
                    showToast('error', response.message, 'Generation Failed');
                    button.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Failed to generate Purchase Order.';
                showToast('error', errorMsg, 'Error');
                button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // 🔹 Reset modal state when it's closed
    $('#poApprovalModal').on('hidden.bs.modal', function () {
        // Reset button state
        $('#confirmPOGenerate').prop('disabled', false).html('<i class="fa fa-check"></i> Generate PO');
        // Clear content
        $('#poApprovalContent').html('');
    });

});
</script>
@endsection











