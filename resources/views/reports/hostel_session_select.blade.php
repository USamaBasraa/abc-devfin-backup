@extends('layouts.app')

@section('content')
<style>
    .hostel-report-page { padding-bottom: 24px; }
    .hostel-report-hero {
        background: linear-gradient(135deg, #4249eb 0%, #6c63ff 100%);
        border-radius: 12px;
        color: #fff;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(66, 73, 235, .25);
    }
    .hostel-report-hero h3 { font-weight: 700; margin-bottom: 4px; }
    .hostel-report-hero p { opacity: .9; margin-bottom: 0; font-size: 14px; }
    .hostel-report-filters {
        background: #fff;
        border: 1px solid #e8ecf4;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }
    .hostel-report-filters .form-control {
        border-radius: 8px;
        border-color: #dde3ef;
        font-size: 13px;
    }
    .hostel-report-filters label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6b7280;
        font-weight: 600;
    }
    #loadReportBtn {
        border-radius: 8px;
        font-weight: 600;
        background: #4249eb;
        border-color: #4249eb;
    }
    #loadReportBtn:hover { background: #353ccf; border-color: #353ccf; }
    .hostel-report-hint {
        background: #f0f4ff;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        color: #4b5563;
    }
    #studentsResult .hostel-report-stat {
        border-radius: 10px;
        padding: 16px 12px;
        background: #fff;
        border: 1px solid #eef1f6;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    #studentsResult .hostel-report-stat::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
    }
    #studentsResult .stat-students::before { background: #4249eb; }
    #studentsResult .stat-new::before { background: #0ea5e9; }
    #studentsResult .stat-continuing::before { background: #64748b; }
    #studentsResult .stat-paid::before { background: #10b981; }
    #studentsResult .stat-unpaid::before { background: #ef4444; }
    #studentsResult .stat-bed::before { background: #8b5cf6; }
    #studentsResult .stat-no-challan::before { background: #f59e0b; }
    #studentsResult .hostel-report-stat h4 {
        font-weight: 800;
        margin: 4px 0 0;
        font-size: 1.6rem;
        line-height: 1.2;
    }
    #studentsResult .hostel-report-stat small {
        color: #6b7280;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .6px;
        font-weight: 600;
    }
    #studentsResult .hostel-report-stat .stat-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }
    #studentsResult .hostel-report-card {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,.06);
    }
    #studentsResult .hostel-report-card .card-header {
        background: linear-gradient(90deg, #1e293b, #334155);
        border: 0;
        padding: 12px 16px;
    }
    #studentsResult .hostel-report-table thead tr.group-row th {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        padding: 8px 6px;
        border: 1px solid rgba(0,0,0,.06);
    }
    #studentsResult .group-admission th { background: #dbeafe !important; color: #1e40af; }
    #studentsResult .group-finance th { background: #fef3c7 !important; color: #92400e; }
    #studentsResult .group-hostel th { background: #d1fae5 !important; color: #065f46; }
    #studentsResult .hostel-report-table thead tr.column-row th {
        background: #1f2937 !important;
        color: #f9fafb;
        white-space: nowrap;
        font-size: 10px;
        font-weight: 600;
        padding: 10px 8px;
        vertical-align: middle;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
    #studentsResult .hostel-report-table tbody td {
        font-size: 12px;
        vertical-align: middle;
        padding: 8px;
    }
    #studentsResult .hostel-report-table tbody tr:nth-child(even) { background: #f9fafb; }
    #studentsResult .hostel-report-table tbody tr:hover { background: #f0f4ff; }
    #studentsResult .hostel-report-table tbody tr.row-multi { background: #fffbeb !important; }
    #studentsResult .col-name { min-width: 160px; max-width: 200px; font-weight: 600; }
    #studentsResult .col-program { min-width: 140px; max-width: 180px; }
    #studentsResult .col-amount { text-align: right; font-weight: 600; white-space: nowrap; }
    #studentsResult .col-center { text-align: center; }
    #studentsResult .badge-status { font-size: 10px; padding: 4px 8px; font-weight: 600; }
    #studentsResult .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 6px 14px;
        border: 1px solid #dde3ef;
    }
    #studentsResult .dt-buttons .btn {
        border-radius: 8px;
        font-weight: 600;
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    #studentsResult .dataTables_scrollHead { border-radius: 0; }
    .hostel-challan-section {
        background: #fff8e1;
        border: 1px solid #ffe082;
        border-left: 4px solid #ff9800;
        border-radius: 12px;
        padding: 20px;
        margin-top: 28px;
    }
    .hostel-challan-section h5 { font-weight: 700; color: #e65100; }
    .hostel-challan-stat {
        background: #fff;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        border: 1px solid #ffe082;
    }
    .hostel-challan-stat h4 { margin-bottom: 0; font-weight: 700; }
</style>

@include('components.toast-notifications')

<div class="container-fluid hostel-report-page">
    <div class="hostel-report-hero">
        <h3><i class="fa fa-building-o mr-2"></i>Admission & Finance Hostel Report</h3>
        <p>New admission + continuing students — admission status, challan, scholarship & bed assignment</p>
    </div>

    <div class="card hostel-report-filters mb-4">
        <div class="card-body py-3">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <label>Academic Session *</label>
                    <select class="form-control" id="session_id">
                        <option value="">-- Select Session --</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                    <label>Student Type</label>
                    <select class="form-control filter-input" id="student_type">
                        <option value="all" selected>All</option>
                        <option value="new">New Admission</option>
                        <option value="existing">Continuing Students</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-3 mb-md-0">
                    <label>Challan Status</label>
                    <select class="form-control filter-input" id="challan_status">
                        <option value="all">All</option>
                        <option value="with_challan">With Challan</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="no_challan">No Challan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-3 mb-md-0">
                    <label>Hostel Location</label>
                    <select class="form-control filter-input" id="hostel_location">
                        <option value="all">All</option>
                        <option value="on_campus">On Campus</option>
                        <option value="off_campus">Off Campus</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-3 mb-md-0">
                    <label>Gender</label>
                    <select class="form-control filter-input" id="gender">
                        <option value="all">All</option>
                        <option value="male">Boys</option>
                        <option value="female">Girls</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4">
                    <button type="button" class="btn btn-primary btn-block" id="loadReportBtn">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="hostel-report-hint mt-3">
                <i class="fa fa-info-circle text-primary"></i>
                <strong>New Admission</strong> = session applicants &nbsp;|&nbsp;
                <strong>Continuing</strong> = <code>hostel_applicants</code> &nbsp;|&nbsp;
                Yellow row = student with multiple challans
            </div>
        </div>
    </div>

    <div id="studentsResult"></div>

    <!-- Hostel Challan List: students who requested & challan generated -->
    <div class="hostel-challan-section">
        <h5 class="mb-1"><i class="fa fa-file-text-o mr-2"></i>Hostel Request — Challan Generated (Session-wise)</h5>
        <p class="text-muted small mb-3">Students jinhon ne hostel request ki aur unka <strong>Hostel_Fee</strong> challan issue ho chuka hai.</p>

        <div class="row align-items-end">
            <div class="col-md-2">
                <label class="font-weight-bold small">Academic Session *</label>
                <select class="form-control" id="challan_session_id">
                    <option value="">Select Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="font-weight-bold small">Location *</label>
                <select class="form-control" id="challan_location">
                    <option value="">Select Location</option>
                    @foreach($hostelLocations as $location)
                        <option value="{{ $location }}">{{ ucwords(str_replace('_', ' ', $location)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="font-weight-bold small">Hostel Type</label>
                <select class="form-control" id="challan_gender" disabled>
                    <option value="">All (Boys / Girls)</option>
                    <option value="boys">Boys</option>
                    <option value="girls">Girls</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="font-weight-bold small">Hostel</label>
                <select class="form-control" id="challan_hostel_id" disabled>
                    <option value="">All Hostels</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-warning btn-block text-white font-weight-bold" onclick="loadHostelChallanList()">
                    <i class="fa fa-list mr-1"></i> Load List
                </button>
            </div>
        </div>

        <div id="challan_list_section" style="display:none;" class="mt-4">
            <div class="row text-center mb-3">
                <div class="col-6 col-md-3 mb-2">
                    <div class="hostel-challan-stat">
                        <small class="text-muted d-block">Issued</small>
                        <h4 class="text-primary" id="challan_stat_issued">0</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="hostel-challan-stat">
                        <small class="text-muted d-block">Paid</small>
                        <h4 class="text-success" id="challan_stat_paid">0</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="hostel-challan-stat">
                        <small class="text-muted d-block">Unpaid</small>
                        <h4 class="text-danger" id="challan_stat_unpaid">0</h4>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="hostel-challan-stat">
                        <small class="text-muted d-block">With Scholarship</small>
                        <h4 class="text-info" id="challan_stat_scholarship">0</h4>
                    </div>
                </div>
            </div>
            <div class="table-responsive bg-white rounded border">
                <table class="table table-bordered table-hover table-sm mb-0" id="challan_list_table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Enrollment</th>
                            <th>Applicant ID</th>
                            <th>Student Name</th>
                            <th>Challan No</th>
                            <th>Session Type</th>
                            <th>Amount</th>
                            <th>Scholarship</th>
                            <th>Schol. Amt</th>
                            <th>Status</th>
                            <th>Floor / Room / Bed</th>
                            <th>Due Date</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody id="challan_list_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
function destroyReportTable() {
    if ($.fn.DataTable.isDataTable('#studentsTable')) {
        $('#studentsTable').DataTable().clear().destroy();
    }
}

function initReportTable(title) {
    destroyReportTable();

    $('#studentsTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        autoWidth: false,
        order: [[4, 'asc']],
        dom: '<"row mb-3 align-items-center"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row mt-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o mr-1"></i> Export Excel',
            title: title,
            exportOptions: {
                columns: ':visible',
                format: {
                    body: function(data, row, column, node) {
                        if (node && $(node).attr('data-export')) {
                            return $(node).attr('data-export');
                        }
                        return $('<div>').html(data).text();
                    }
                }
            }
        }],
        language: {
            search: '',
            searchPlaceholder: 'Search name, ID, challan...'
        },
        columnDefs: [
            { orderable: false, targets: 0 },
            { className: 'col-name', targets: 4 },
            { className: 'col-program', targets: 7 },
            { className: 'col-amount', targets: [16, 17] },
            { className: 'col-center', targets: [1, 5, 18] }
        ]
    });
}

function loadHostelReport() {
    const sessionId = $('#session_id').val();
    if (!sessionId) {
        $('#studentsResult').html('');
        return;
    }

    $('#studentsResult').html(
        '<div class="text-center py-5">' +
        '<div class="spinner-border text-primary" role="status"></div>' +
        '<p class="mt-3 text-muted">Loading report...</p></div>'
    );

    $.ajax({
        url: `/new-hostel-report/students/${sessionId}`,
        type: 'GET',
        data: {
            student_type: $('#student_type').val(),
            challan_status: $('#challan_status').val(),
            hostel_location: $('#hostel_location').val(),
            gender: $('#gender').val()
        },
        success: function(html) {
            $('#studentsResult').html(html);
            if ($('#studentsTable').length) {
                initReportTable($('#reportExportTitle').val() || 'Hostel Report');
            }
        },
        error: function(xhr) {
            $('#studentsResult').html(xhr.responseText || '<div class="alert alert-danger m-3">Failed to load report.</div>');
        }
    });
}

$('#session_id, .filter-input').on('change', function() {
    const sid = $('#session_id').val();
    if (sid) {
        $('#challan_session_id').val(sid);
    }
    loadHostelReport();
});
$('#loadReportBtn').on('click', loadHostelReport);

// ---- Hostel Challan List (same as Hostel Summary) ----
var allChallanHostelTypes = @json($hostelTypes);

function challanLocationMatches(selected, hostelLocation) {
    const selectedLoc = (selected || '').toLowerCase();
    const location = (hostelLocation || 'on_campus').toLowerCase();
    if (selectedLoc === location) return true;
    const offCampus = ['off_campus', 'out_of_campus'];
    return offCampus.indexOf(selectedLoc) !== -1 && offCampus.indexOf(location) !== -1;
}

function populateChallanHostelDropdown() {
    const location = $('#challan_location').val();
    const gender = $('#challan_gender').val();
    const $hostelSelect = $('#challan_hostel_id');
    $hostelSelect.html('<option value="">All Hostels</option>');
    if (!location) {
        $hostelSelect.prop('disabled', true);
        return;
    }
    let filtered = allChallanHostelTypes.filter(function(type) {
        return challanLocationMatches(location, type.ht_location);
    });
    if (gender) {
        const genderType = gender === 'boys' ? 1 : 2;
        filtered = filtered.filter(function(type) {
            return parseInt(type.ht_hostel_type, 10) === genderType;
        });
    }
    filtered.forEach(function(type) {
        $hostelSelect.append('<option value="' + type.ht_id + '">' + type.ht_name + '</option>');
    });
    $hostelSelect.prop('disabled', false);
}

$('#challan_location').on('change', function() {
    const location = $(this).val();
    $('#challan_gender')
        .html('<option value="">All (Boys / Girls)</option><option value="boys">Boys</option><option value="girls">Girls</option>')
        .val('')
        .prop('disabled', !location);
    populateChallanHostelDropdown();
});
$('#challan_gender').on('change', populateChallanHostelDropdown);

function loadHostelChallanList() {
    const sessionId = $('#challan_session_id').val();
    const hostelLocation = $('#challan_location').val();
    const hostelGender = $('#challan_gender').val();
    const hostelId = $('#challan_hostel_id').val();

    if (!sessionId || !hostelLocation) {
        if (typeof showToast === 'function') {
            showToast('error', 'Please select session and location.');
        } else {
            alert('Please select session and location.');
        }
        return;
    }

    destroyChallanListDataTable();
    $('#challan_list_section').show();
    $('#challan_list_body').html('<tr><td colspan="13" class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

    $.ajax({
        url: '/api/hostel-summary/get-challan-list',
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}'
        },
        data: {
            session_id: sessionId,
            hostel_location: hostelLocation,
            hostel_gender: hostelGender || '',
            hostel_id: hostelId || ''
        },
        success: function(res) {
            if (!res.success) {
                if (typeof showToast === 'function') showToast('error', res.message || 'Failed to load.');
                return;
            }
            const stats = res.stats || {};
            $('#challan_stat_issued').text(stats.issued || 0);
            $('#challan_stat_paid').text(stats.paid || 0);
            $('#challan_stat_unpaid').text(stats.unpaid || 0);
            $('#challan_stat_scholarship').text(stats.with_scholarship || 0);

            const rows = res.students || [];
            const tbody = $('#challan_list_body').empty();
            if (!rows.length) {
                tbody.html('<tr><td colspan="13" class="text-center text-muted py-4">No challans found.</td></tr>');
                return;
            }

            const sessionName = res.session_name || $('#challan_session_id option:selected').text().trim();
            const locationName = $('#challan_location option:selected').text().trim();
            const genderName = hostelGender ? $('#challan_gender option:selected').text().trim() : 'All';
            const hostelName = (res.hostel && res.hostel.name) ? res.hostel.name : (hostelId ? $('#challan_hostel_id option:selected').text().trim() : 'All Hostels');
            const exportTitle = 'Hostel Challan List - ' + sessionName + ' - ' + locationName + ' - ' + genderName + ' - ' + hostelName;

            rows.forEach(function(row, index) {
                const status = (row.challan_status || 'UNPAID').toUpperCase();
                const statusBadge = status === 'PAID' ? 'badge-success' : 'badge-warning';
                const floorRoomBed = (row.floor_name || row.room_no || row.bed_no)
                    ? (row.floor_name || 'N/A') + ' / ' + (row.room_no || 'N/A') + ' / ' + (row.bed_no || 'N/A') : 'N/A';
                const scholarshipAmt = Number(row.scholarship_amount || 0);
                tbody.append(
                    '<tr><td>' + (index + 1) + '</td>' +
                    '<td>' + (row.enrollment_no || 'N/A') + '</td>' +
                    '<td>' + (row.applicant_id || 'N/A') + '</td>' +
                    '<td>' + (row.student_name || 'N/A') + '</td>' +
                    '<td>' + (row.challan_no || row.challan_id || 'N/A') + '</td>' +
                    '<td>' + (row.session_type || 'N/A') + '</td>' +
                    '<td>Rs. ' + (row.challan_amount || 0) + '</td>' +
                    '<td>' + (row.scholarship_names || 'No Scholarship') + '</td>' +
                    '<td>' + (scholarshipAmt > 0 ? 'Rs. ' + scholarshipAmt : 'Rs. 0') + '</td>' +
                    '<td data-export="' + status + '"><span class="badge ' + statusBadge + '">' + status + '</span></td>' +
                    '<td>' + floorRoomBed + '</td>' +
                    '<td>' + (row.due_date || 'N/A') + '</td>' +
                    '<td>' + (row.paid_date || 'N/A') + '</td></tr>'
                );
            });
            initChallanListDataTable(exportTitle);
        },
        error: function(xhr) {
            destroyChallanListDataTable();
            $('#challan_list_body').html('<tr><td colspan="13" class="text-center text-danger py-4">Error loading list.</td></tr>');
        }
    });
}

function destroyChallanListDataTable() {
    if ($.fn.DataTable.isDataTable('#challan_list_table')) {
        $('#challan_list_table').DataTable().clear().destroy();
    }
}

function initChallanListDataTable(title) {
    $('#challan_list_table').DataTable({
        dom: '<"row mb-2"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        scrollX: true,
        pageLength: 50,
        order: [[0, 'asc']],
        buttons: [
            { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i> Excel', title: title },
            { extend: 'pdfHtml5', text: '<i class="fa fa-file-pdf-o"></i> PDF', title: title, orientation: 'landscape', pageSize: 'A3' }
        ],
        language: { searchPlaceholder: 'Search challans...' }
    });
}
</script>
@endsection
