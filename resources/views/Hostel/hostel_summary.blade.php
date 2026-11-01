@extends('layouts.app')

@section('content')

<head>
    <script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
    <!-- DataTables libraries already included in app.blade.php -->
</head>
@include('components.toast-notifications')

<style>
    .summary-card {
        border-radius: 10px;
        border: none !important;
        background: white;
        transition: transform 0.3s;
        box-shadow: none !important;
    }

    .summary-card .card {
        border: none !important;
        box-shadow: none !important;
    }

    .summary-card:hover {
        transform: translateY(-5px);
    }

    .summary-card-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .table-container {
        max-height: 600px;
        overflow-y: auto;
    }

    .table thead {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
    }

    /* DataTables Buttons Styling */
    .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-button {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .dt-button:hover {
        opacity: 0.9;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card summary-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fa fa-bar-chart mr-2"></i>Hostel Summary Report
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="font-weight-bold">Academic Session *</label>
                                <select class="form-control" id="session_id">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $session)
                                    <option value="{{ $session->sess_id ?? $session->SESS_ID }}">
                                        {{ $session->sess_name ?? $session->SESS_NAME }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="font-weight-bold">Hostel Type</label>
                                <select class="form-control" id="hostel_type_filter">
                                    <option value="all">All Hostels</option>
                                    <option value="on_campus">On Campus</option>
                                    <option value="off_campus">Off Campus</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="font-weight-bold">Status</label>
                                <select class="form-control" id="status_filter">
                                    <option value="all">All Status</option>
                                    <option value="paid">Paid Only</option>
                                    <option value="reserved">Reserved Only</option>
                                    <option value="available">Available Only</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="font-weight-bold">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary btn-block" onclick="loadSummary()">
                                        <i class="fa fa-search mr-2"></i>Load Summary
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4" id="summary_cards" style="display: none;">
                        <div class="col-12">
                            <div class="card summary-card bg-white">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <i class="fa fa-bed summary-card-icon text-primary"></i>
                                            <h5 class="mt-2 text-primary">Total Seats</h5>
                                            <h2 id="total_seats" class="text-primary">0</h2>
                                        </div>
                                        <div class="col-md-3">
                                            <i class="fa fa-clock-o summary-card-icon text-warning"></i>
                                            <h5 class="mt-2 text-warning">Reserved</h5>
                                            <h2 id="total_reserved" class="text-warning">0</h2>
                                        </div>
                                        <div class="col-md-3">
                                            <i class="fa fa-check-circle summary-card-icon text-success"></i>
                                            <h5 class="mt-2 text-success">Paid</h5>
                                            <h2 id="total_paid" class="text-success">0</h2>
                                        </div>
                                        <div class="col-md-3">
                                            <i class="fa fa-home summary-card-icon text-info"></i>
                                            <h5 class="mt-2 text-info">Can Vacate</h5>
                                            <h2 id="total_vacant" class="text-info">0</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export buttons are now handled by DataTables -->

                    <!-- Loading Indicator -->
                    <div id="loading_indicator" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading summary data...</p>
                    </div>

                    <!-- Summary Table -->
                    <div id="summary_table_container" style="display: none;">
                        @include('Hostel.components.Summary.summary_report')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Simplified Confirmation Modal (Before Preview) -->
<div class="modal fade" id="vacateConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold text-white">
                    Confirm Vacate
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <h5 class="mb-2 text-dark">Vacate <span id="confirmHostelName" class="font-weight-bold">this
                            hostel</span>?</h5>
                    <p class="text-muted small">This will remove students and cancel unpaid challans.</p>
                </div>

                <div class="row border rounded mx-1 mb-3 bg-light">
                    <div class="col-6 border-right py-3 text-center">
                        <h4 class="mb-0 font-weight-bold text-dark" id="confirmPaidCount">-</h4>
                        <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Paid
                            (Keep)</small>
                    </div>
                    <div class="col-6 py-3 text-center">
                        <h4 class="mb-0 font-weight-bold text-dark" id="confirmReservedCount">-</h4>
                        <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Vacate
                            (Remove)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light border text-muted font-weight-bold px-4 mr-2"
                    data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger font-weight-bold px-4" id="confirmVacateProceed">
                    Vacate
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Logs View Modal -->
@include('Hostel.components.Summary.logs_view')

<!-- Drill-down Modal for Student List -->
<div class="modal fade" id="summaryDrillDownModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fa fa-users mr-2"></i> <span id="summaryDrillDownTitle" class="text-white">Student
                        List</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-hover mb-0" id="summaryDrillDownTable">
                        <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th class="pl-4">#</th>
                                <th>Student/Applicant ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="summaryDrillDownBody">
                            <!-- Students list will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div id="summaryDrillDownLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted font-weight-bold">Loading student list...</p>
                </div>
            </div>
            <div class="modal-footer border-top bg-light" style="border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary font-weight-bold px-4"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    #summaryDrillDownModal .modal-header * {
        color: white !important;
    }

    #summaryDrillDownModal .modal-body {
        padding: 0 !important;
    }

    #summaryDrillDownModal .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
    }

    #summaryDrillDownModal #summaryDrillDownTable thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
    }

    #summaryDrillDownModal #summaryDrillDownTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        position: sticky;
        top: 0;
    }
</style>

<script>
    var currentSummaryData = null;

    function loadSummary() {
        var sessionId = $('#session_id').val();
        var hostelType = $('#hostel_type_filter').val();
        var status = $('#status_filter').val();

        if (!sessionId) {
            showToast('error', 'Please select Academic Session');
            return;
        }

        $('#loading_indicator').show();
        $('#summary_table_container').hide();
        $('#summary_cards').hide();
        $('#action_buttons').hide();

        $.ajax({
            url: '/api/hostel-summary/get-summary',
            type: 'GET',
            data: {
                session_id: sessionId,
                hostel_type: hostelType,
                status: status
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                $('#loading_indicator').hide();

                if (response && response.success) {
                    currentSummaryData = response.summary;

                    var totals = response.totals;
                    if (totals) {
                        $('#total_seats').text(totals.total_seats || 0);
                        $('#total_reserved').text(totals.total_reserved || 0);
                        $('#total_paid').text(totals.total_paid || 0);
                        $('#total_vacant').text(totals.total_reserved || 0);
                    } else {
                        $('#total_seats').text(0);
                        $('#total_reserved').text(0);
                        $('#total_paid').text(0);
                        $('#total_vacant').text(0);
                    }

                    var summaryData = response.summary;
                    if (!summaryData) {
                        summaryData = [];
                    }
                    $('#summary_table_container').show();
                    renderSummaryTable(summaryData);

                    $('#summary_cards').show();
                    $('#action_buttons').show();
                } else {
                    var errorMsg = 'Unknown error occurred';
                    if (response && response.message) {
                        errorMsg = response.message;
                    }
                    showToast('error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                $('#loading_indicator').hide();
                var errorMsg = 'Error loading summary. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMsg = 'Server Error: ' + xhr.responseText.substring(0, 100);
                }
                showToast('error', errorMsg);
            }
        });
    }

    function renderSummaryTable(summaryData) {
        // Destroy existing DataTable FIRST to ensure we are working with a clean DOM
        if ($.fn.DataTable.isDataTable('#summary_table')) {
            $('#summary_table').DataTable().destroy();
        }

        var tbody = $('#summary_table_body');
        tbody.empty();

        if (!summaryData || summaryData.length === 0) {
            var noDataRow = '<tr><td colspan="7" class="text-center text-muted"><i class="fa fa-info-circle mr-2"></i>No data found</td></tr>';
            tbody.append(noDataRow);
            return;
        }

        $.each(summaryData, function(index, hostel) {
            var hostelName = hostel.hostel_name || 'N/A';
            var hostelLocationRaw = hostel.hostel_location || 'N/A';
            // Convert database values to human-readable text
            var hostelLocation = 'N/A';
            if (hostelLocationRaw === 'on_campus') {
                hostelLocation = 'On Campus';
            } else if (hostelLocationRaw === 'off_campus') {
                hostelLocation = 'Off Campus';
            } else {
                hostelLocation = hostelLocationRaw;
            }
            var totalSeats = hostel.total_seats || 0;
            var paid = hostel.paid || 0;
            var reserved = hostel.reserved || 0;
            var vacant = hostel.vacant || 0;
            var hostelId = hostel.hostel_id;

            var row = '<tr>';
            row += '<td class="font-weight-bold">' + hostelName + '</td>';
            row += '<td>' + hostelLocation + '</td>';
            row += '<td class="text-center">';
            row += '<a href="javascript:void(0)" class="summary-drill-down font-weight-bold" data-hostel-id="' + hostelId + '" data-type="total" data-hostel-name="' + hostelName + '" style="color: #007bff; text-decoration: underline; cursor: pointer;">' + totalSeats + '</a>';
            row += '</td>';
            row += '<td class="text-center">';
            row += '<a href="javascript:void(0)" class="summary-drill-down font-weight-bold" data-hostel-id="' + hostelId + '" data-type="paid" data-hostel-name="' + hostelName + '" style="color: #28a745; text-decoration: underline; cursor: pointer;">' + paid + '</a>';
            row += '</td>';
            row += '<td class="text-center">';
            row += '<a href="javascript:void(0)" class="summary-drill-down font-weight-bold" data-hostel-id="' + hostelId + '" data-type="reserved" data-hostel-name="' + hostelName + '" style="color: #f57c00; text-decoration: underline; cursor: pointer;">' + reserved + '</a>';
            row += '</td>';
            row += '<td class="text-center"><span class="badge badge-secondary">' + vacant + '</span></td>';
            row += '<td class="text-center">';
            row += '<button type="button" class="btn btn-sm btn-danger" onclick="showVacateModal(' + hostelId + ')"><i class="fa fa-trash"></i> Vacate</button>';
            row += '</td>';
            row += '</tr>';

            tbody.append(row);
        });

        // Initialize DataTable immediately after populating rows

        $('#summary_table').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                    title: 'Hostel Summary Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5] // Exclude Actions column
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                    title: 'Hostel Summary Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5] // Exclude Actions column
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.styles.tableHeader.fontWeight = 'bold';
                    }
                }
            ],
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });
    }

    window.showVacateModal = function(hostelId) {
        var sessionId = $('#session_id').val();
        if (!sessionId) {
            showToast('error', 'Please select Academic Session first');
            return;
        }

        // Show loading state or finding hostel name from existing data for UI title 
        var hostelName = 'this hostel';
        if (currentSummaryData) {
            var hostel = currentSummaryData.find(h => h.hostel_id == hostelId);
            if (hostel) {
                hostelName = hostel.hostel_name || 'this hostel';
            }
        }

        // Reset modal state
        $('#confirmHostelName').text(hostelName);
        $('#confirmPaidCount').text('Loading...');
        $('#confirmReservedCount').text('Loading...');
        $('#vacateConfirmModal').data('hostel-id', hostelId);

        // Open model immediately
        $('#vacateConfirmModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });

        // Fetch LIVE data
        $.ajax({
            url: '/api/hostel-summary/get-vacate-preview',
            type: 'GET',
            data: {
                session_id: sessionId,
                hostel_id: hostelId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // Update modal with LIVE data
                    $('#confirmPaidCount').text(response.paid_kept);
                    $('#confirmReservedCount').text(response.total_to_vacate);
                } else {
                    $('#confirmPaidCount').text('Error');
                    $('#confirmReservedCount').text('Error');
                    showToast('error', 'Failed to fetch live data: ' + response.message);
                }
            },
            error: function() {
                $('#confirmPaidCount').text('Error');
                $('#confirmReservedCount').text('Error');
                showToast('error', 'Failed to fetch live statistics');
            }
        });
    }

    $(document).on('click', '#confirmVacateProceed', function() {
        var hostelId = $('#vacateConfirmModal').data('hostel-id');
        var sessionId = $('#session_id').val();

        if (!sessionId) {
            showToast('error', 'Session ID is missing');
            return;
        }

        if (!hostelId) {
            showToast('error', 'Hostel ID is missing');
            return;
        }

        // Show loading
        var confirmBtn = $('#confirmVacateProceed');
        var originalText = confirmBtn.html();
        confirmBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

        // Close confirmation modal
        $('#vacateConfirmModal').modal('hide');

        // Directly call vacate API
        $.ajax({
            url: '/api/hostel-summary/vacate',
            type: 'POST',
            data: {
                session_id: sessionId,
                hostel_id: hostelId,
                remarks: ''
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                confirmBtn.prop('disabled', false).html(originalText);

                if (response.success) {
                    showToast('success', 'Hostel vacated successfully! Total Vacated: ' + response.total_vacated + ', Paid Kept: ' + response.paid_kept);
                    loadSummary();
                } else {
                    var errorMsg = 'Unknown error';
                    if (response.message) {
                        errorMsg = response.message;
                    }
                    showToast('error', errorMsg);
                }
            },
            error: function(xhr) {
                confirmBtn.prop('disabled', false).html(originalText);
                var errorMsg = 'Error performing vacate operation. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast('error', errorMsg);
            }
        });
    });

    function showLogsModal() {
        $('#logsModal').modal('show');
        loadLogs();
    }

    function loadLogs() {
        $.ajax({
            url: '/api/hostel-summary/get-logs',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderLogsTable(response.logs);
                } else {
                    var errorMsg = 'Unknown error';
                    if (response.message) {
                        errorMsg = response.message;
                    }
                    showToast('error', errorMsg);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error loading logs. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast('error', errorMsg);
            }
        });
    }

    function renderLogsTable(logs) {
        // This will be handled by logs_view component
        $(document).trigger('logsDataLoaded', [logs]);
    }

    // Export functions removed - now handled by DataTables built-in buttons

    $(document).on('click', '.summary-drill-down', function(e) {
        e.preventDefault();

        var hostelId = $(this).data('hostel-id');
        var type = $(this).data('type');
        var hostelName = $(this).data('hostel-name');

        // Get current filters
        var sessionId = $('#session_id').val();
        var hostelTypeFilter = $('#hostel_type').val();
        var statusFilter = $('#status').val();

        if (!sessionId) {
            showToast('error', 'Please select a session first');
            return;
        }

        // Show modal immediately with loading state
        var typeText = 'Total';
        if (type === 'paid') {
            typeText = 'Paid';
        } else if (type === 'reserved') {
            typeText = 'Reserved';
        }

        $('#summaryDrillDownTitle').text(hostelName + ' - ' + typeText + ' Students');
        $('#summaryDrillDownBody').empty();
        $('#summaryDrillDownLoading').show();
        $('#summaryDrillDownTable').hide();

        // IMPORTANT: Destroy DataTable BEFORE making AJAX call to prevent caching issues
        if ($.fn.DataTable.isDataTable('#summaryDrillDownTable')) {
            $('#summaryDrillDownTable').DataTable().destroy();
        }
        // Clear the table body completely
        $('#summaryDrillDownBody').empty();

        $('#summaryDrillDownModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });

        // Fetch fresh data from server
        $.ajax({
            url: '/api/hostel-summary/get-summary',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            data: {
                session_id: sessionId,
                hostel_type: hostelTypeFilter,
                status: statusFilter
            },
            success: function(response) {
                if (!response.success || !response.summary) {
                    $('#summaryDrillDownLoading').hide();
                    var errorRow = '<tr><td colspan="6" class="text-center py-5 font-weight-bold text-danger">Failed to load data. Please try again.</td></tr>';
                    $('#summaryDrillDownBody').html(errorRow);
                    $('#summaryDrillDownTable').show();
                    return;
                }

                // Find the specific hostel from fresh data
                var hostel = null;
                $.each(response.summary, function(index, h) {
                    if (h.hostel_id == hostelId) {
                        hostel = h;
                        return false;
                    }
                });

                if (!hostel) {
                    $('#summaryDrillDownLoading').hide();
                    var errorRow = '<tr><td colspan="6" class="text-center py-5 font-weight-bold text-danger">Hostel data not found.</td></tr>';
                    $('#summaryDrillDownBody').html(errorRow);
                    $('#summaryDrillDownTable').show();
                    return;
                }

                // Filter students based on type
                var students = [];
                if (hostel.students && hostel.students.length > 0) {
                    $.each(hostel.students, function(index, student) {
                        if (type === 'paid') {
                            if (student.sc_challan_status === 'PAID') {
                                students.push(student);
                            }
                        } else if (type === 'reserved') {
                            if (student.sc_challan_status !== 'PAID' || !student.sc_challan_status) {
                                students.push(student);
                            }
                        } else {
                            // total/all - push everyone
                            students.push(student);
                        }
                    });
                }

                $('#summaryDrillDownLoading').hide();
                if (students.length === 0) {
                    var noDataRow = '<tr><td colspan="6" class="text-center py-5 font-weight-bold text-muted">No ' + typeText.toLowerCase() + ' students found.</td></tr>';
                    $('#summaryDrillDownBody').html(noDataRow);
                    $('#summaryDrillDownTable').show();
                } else {
                    var html = '';
                    $.each(students, function(index, student) {
                        var statusBadge = 'badge-warning';
                        var statusText = 'RESERVED';
                        if (student.sc_challan_status === 'PAID') {
                            statusBadge = 'badge-success';
                            statusText = 'PAID';
                        }

                        var applicantId = 'N/A';
                        if (student.ha_aai_id) {
                            applicantId = student.ha_aai_id;
                        } else if (student.ha_enrolment_no) {
                            applicantId = student.ha_enrolment_no;
                        }

                        var floorRoomBed = 'N/A';
                        if (student.floor_name && student.room_no && student.bed_no) {
                            floorRoomBed = student.floor_name + ', Room No# ' + student.room_no + ', Bed No# ' + student.bed_no;
                        }

                        var studentName = student.student_name || 'N/A';
                        var studentEmail = student.student_email || 'N/A';

                        html += '<tr>';
                        html += '<td class="pl-4 py-3 font-weight-bold">' + (index + 1) + '</td>';
                        html += '<td class="py-3 font-weight-bold text-muted">' + applicantId + '</td>';
                        html += '<td class="py-3 font-weight-bold">' + studentName + '</td>';
                        html += '<td class="py-3">' + studentEmail + '</td>';
                        html += '<td class="py-3">' + floorRoomBed + '</td>';
                        html += '<td class="text-center py-3">';
                        html += '<span class="badge ' + statusBadge + ' px-3 py-2" style="font-size: 10px;">' + statusText + '</span>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#summaryDrillDownBody').html(html);
                    $('#summaryDrillDownTable').show();

                    // Use setTimeout to ensure DOM is fully updated before initializing DataTable
                    setTimeout(function() {
                        // Initialize DataTable for Drill Down
                        if ($.fn.DataTable.isDataTable('#summaryDrillDownTable')) {
                            $('#summaryDrillDownTable').DataTable().destroy();
                        }
                        $('#summaryDrillDownTable').DataTable({
                            pageLength: 10,
                            language: {
                                search: "Search Student:",
                                lengthMenu: "Show _MENU_"
                            },
                            dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"p>>' +
                                '<"row"<"col-sm-12"tr>>' +
                                '<"row"<"col-sm-12"i>>'
                        });
                    }, 100);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading drill-down data:', error);
                $('#summaryDrillDownLoading').hide();
                var errorRow = '<tr><td colspan="6" class="text-center py-5 font-weight-bold text-danger">Error loading data. Please try again.</td></tr>';
                $('#summaryDrillDownBody').html(errorRow);
                $('#summaryDrillDownTable').show();
            }
        });
    });
</script>

@endsection