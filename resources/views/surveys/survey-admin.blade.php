<?php
    $file_name = "app";
    if (auth()->user()->cms_sidebar == 'new') {
        $file_name = "updated_app";
    }
?>
@extends('layouts.' . $file_name)

@section('title')
    Manage Surveys
@stop

@section('content')
@include('components.toast-notifications')

<style>
    .survey-admin-card-header {
        border-bottom: 2px solid #4555C3;
        background: #fff;
    }
    .survey-admin-card-header h3 {
        margin: 0;
        font-weight: 600;
    }
    #surveySubmissionsTable thead th {
        background: #4555C3;
        color: #fff;
        border-color: #4555C3;
    }
    .survey-type-select {
        max-width: 420px;
    }
    .survey-empty-state {
        padding: 40px 10px;
        text-align: center;
        color: #9ca3af;
        font-style: italic;
    }
    .desc-limit {
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .survey-action-btn {
        border: none;
        background: transparent;
        font-size: 15px;
        padding: 4px 6px;
        cursor: pointer;
    }
    .survey-action-btn.view-btn { color: #4555C3; }
    .survey-action-btn.edit-btn { color: #de690c; }
    .survey-action-btn.delete-btn { color: #C34545; }
    .survey-action-btn:hover { opacity: 0.7; }

    .report-view-modal .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(17, 24, 39, 0.18);
    }
    .report-view-modal .modal-header {
        background: linear-gradient(135deg, #4555C3 0%, #3544a8 100%);
        color: #fff;
        border-bottom: none;
        padding: 16px 20px;
    }
    .report-view-modal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }
    .report-view-modal .modal-body {
        padding: 20px 22px;
        background: #f8fafc;
        max-height: 70vh;
        overflow-y: auto;
    }
    .report-view-modal .modal-footer {
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 20px;
    }
    .report-detail-grid {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 10px 16px;
        align-items: start;
    }
    .report-detail-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        margin: 0;
        padding-top: 2px;
    }
    .report-detail-value {
        font-size: 14px;
        color: #1f2937;
        margin: 0;
        word-break: break-word;
    }
    .edit-field-row label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
    }
    @media (max-width: 576px) {
        .report-detail-grid {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }

    /* ---- loader + polish ---- */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(17, 24, 39, 0.06);
    }
    #getSurveyDataBtn {
        border-radius: 6px;
        font-weight: 500;
        transition: opacity 0.15s ease;
    }
    #getSurveyDataBtn:hover { opacity: 0.85; }
    #getSurveyDataBtn:disabled { opacity: 0.65; cursor: not-allowed; }

    .survey-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff;
        border-radius: 50%;
        display: inline-block;
        animation: survey-spin 0.7s linear infinite;
        vertical-align: -2px;
        margin-right: 6px;
    }
    .survey-loading-overlay {
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
        padding: 60px 10px;
        color: #6b7280;
    }
    .survey-loading-overlay .survey-spinner-lg {
        width: 32px;
        height: 32px;
        border: 3px solid #e5e7eb;
        border-top-color: #4555C3;
        border-radius: 50%;
        animation: survey-spin 0.8s linear infinite;
    }
    .modal-loading-overlay {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        padding: 40px 10px;
        color: #6b7280;
    }
    .modal-loading-overlay .survey-spinner-lg {
        width: 28px;
        height: 28px;
        border: 3px solid #e5e7eb;
        border-top-color: #4555C3;
        border-radius: 50%;
        animation: survey-spin 0.8s linear infinite;
    }
    @keyframes survey-spin {
        to { transform: rotate(360deg); }
    }
    .survey-table-busy #surveySubmissionsTable {
        opacity: 0.45;
        pointer-events: none;
        transition: opacity 0.15s ease;
    }
</style>

<div class="row" style="margin:10px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header survey-admin-card-header">
                <h3><i class="fa fa-list-alt"></i> Survey Management</h3>
            </div>
            <div class="card-body">
                <label for="surveyTypeSelect" class="font-weight-bold mb-2">Select Survey to Manage</label>
                <div class="d-flex align-items-stretch" style="gap:10px; max-width: 560px;">
                    <select id="surveyTypeSelect" class="form-control survey-type-select" style="flex:1 1 auto;">
                        <option value="">-- Select a survey --</option>
                        <option value="hostel-consent-form"> Hostel consent form </option>
                        <option value="librarysurvey"> Library survey form </option>
                        <!-- <option value="jobdescription"> Job description forms </option> -->
                        <option value="admissions-feedback"> Admissions feedback </option>
                        <option value="zeroweek2025"> Zero week 2025 </option>
                        <option value="entrepreneurial-survey"> Entrepreneurial Survey </option>
                        <option value="hostelsurvay"> Hostel survey </option>
                        <option value="student_satisfaction_survey"> Student satisfaction survey </option>
                        <option value="student_affair_survey"> Student affair survey </option>
                        <option value="ramzan_timing"> Ramzan timing </option>
                        <option value="staff_satisfaction_survey"> Staff satisfaction survey </option>
                        <option value="staff_health_insurance_survey"> Staff health insurance survey </option>
                        <option value="survey-on-research-ethics"> Survey on Research Ethics </option>
                        <option value="counseling-hours-feedback"> Counseling hours feedback </option>
                        <option value="zero-week"> Zero Week </option>
                    </select>
                    <button type="button" id="getSurveyDataBtn" class="btn" style="background:#4555C3;border-color:#4555C3;color:#fff;white-space:nowrap;">
                        <i class="fa fa-search"></i> Get Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin:10px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header survey-admin-card-header">
                <h3 id="surveySubmissionsTitle"><i class="fa fa-table"></i> Submissions</h3>
            </div>
            <div class="card-body">
                <div id="surveyEmptyState" class="survey-empty-state">
                    Select a survey type above to view its submissions.
                </div>
                <div id="surveyLoadingState" class="survey-loading-overlay">
                    <div class="survey-spinner-lg"></div>
                    <span>Loading submissions...</span>
                </div>
                <div id="surveyTableWrapper" style="display:none; overflow-x:auto;">
                    <table id="surveySubmissionsTable" class="table table-bordered table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Enrollment No</th>
                                <th>Survey Title</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- View Modal --}}
<div class="modal fade report-view-modal" id="viewSurveyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-eye"></i> Submission Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewSurveyModalBody">
                <div class="modal-loading-overlay">
                    <div class="survey-spinner-lg"></div>
                    <span>Loading submission...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-close-modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade report-view-modal" id="editSurveyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editSurveyForm">
                <input type="hidden" id="editSurveyId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-pencil"></i> Edit Submission</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editSurveyModalBody">
                    <div class="modal-loading-overlay">
                        <div class="survey-spinner-lg"></div>
                        <span>Loading submission...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-close-modal" style="background:#de690c;border-color:#de690c;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@section('javascript')
<script type="text/javascript">
    jQuery(function ($) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var dataTable = null;
        var currentSfType = '';

        // Exact same base path as the page route itself.
        var BASE_URL = '{{ url("student-surveys/admin") }}';

        function escapeHtml(str) {
            return $('<div>').text(str === null || str === undefined ? '' : str).html();
        }

        function destroyTable() {
            if (dataTable) {
                dataTable.destroy();
                dataTable = null;
            }
            $('#surveySubmissionsTable tbody').empty();
        }

        function showTableLoader() {
            destroyTable();
            $('#surveyEmptyState').hide();
            $('#surveyTableWrapper').hide();
            $('#surveyLoadingState').css('display', 'flex');
        }

        function hideTableLoader() {
            $('#surveyLoadingState').hide();
        }

        function renderRows(rows) {
            var $tbody = $('#surveySubmissionsTable tbody');
            $tbody.empty();

            if (!rows.length) {
                $tbody.append('<tr><td colspan="5" class="text-center text-muted">No submissions found for this survey.</td></tr>');
                return;
            }

            $.each(rows, function (i, row) {
                var $tr = $('<tr>');
                $tr.append('<td>' + (i + 1) + '</td>');
                $tr.append('<td>' + escapeHtml(row.enrollment_no) + '</td>');
                $tr.append('<td class="desc-limit" title="' + escapeHtml(row.title) + '">' + escapeHtml(row.title) + '</td>');
                $tr.append('<td>' + escapeHtml(row.created_at) + '</td>');
                $tr.append(
                    '<td>' +
                        '<button type="button" class="survey-action-btn view-btn" data-id="' + row.id + '" title="View"><i class="fa fa-eye"></i></button>' +
                        '<button type="button" class="survey-action-btn edit-btn" data-id="' + row.id + '" title="Edit"><i class="fa fa-pencil"></i></button>' +
                        '<button type="button" class="survey-action-btn delete-btn" data-id="' + row.id + '" title="Delete"><i class="fa fa-trash"></i></button>' +
                    '</td>'
                );
                $tbody.append($tr);
            });
        }

        function initDataTable() {
            dataTable = $('#surveySubmissionsTable').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                order: [[0, 'asc']],
                responsive: true,
                autoWidth: false,
                dom: 'Blfrtip',
                buttons: [
                    { extend: 'copy', text: 'Copy', exportOptions: { columns: [0, 1, 2, 3] } },
                    { extend: 'excel', text: 'Excel', exportOptions: { columns: [0, 1, 2, 3] } },
                    { extend: 'print', text: 'Print', exportOptions: { columns: [0, 1, 2, 3] } }
                ]
            });
        }

        function loadSubmissions(sfType, opts) {
            opts = opts || {};
            currentSfType = sfType;

            if (!opts.silent) {
                showTableLoader();
            } else {
                $('#surveyTableWrapper').addClass('survey-table-busy');
            }

            $.ajax({
                url: BASE_URL + '/data',
                method: 'GET',
                data: { sf_type: sfType },
                success: function (resp) {
                    hideTableLoader();
                    $('#surveyTableWrapper').removeClass('survey-table-busy').show();

                    destroyTable();
                    if (resp.success) {
                        renderRows(resp.data);
                    } else {
                        renderRows([]);
                    }
                    initDataTable();
                },
                error: function () {
                    hideTableLoader();
                    $('#surveyTableWrapper').removeClass('survey-table-busy');
                    $('#surveyEmptyState').show();
                    if (typeof showToast === 'function') {
                        showToast('error', 'Unable to load submissions. Please try again.');
                    }
                }
            });
        }

        // Reset the table whenever the user changes the dropdown, but don't
        // fetch until they explicitly click "Get Data".
        $('#surveyTypeSelect').on('change', function () {
            destroyTable();
            $('#surveyTableWrapper').hide().removeClass('survey-table-busy');
            $('#surveyLoadingState').hide();
            $('#surveyEmptyState').show();
            $('#surveySubmissionsTitle').html('<i class="fa fa-table"></i> Submissions');
        });

        $('#getSurveyDataBtn').on('click', function () {
            var sfType = $('#surveyTypeSelect').val();
            var label = $('#surveyTypeSelect').find('option:selected').text();
            var $btn = $(this);

            if (!sfType) {
                if (typeof showToast === 'function') {
                    showToast('error', 'Please select a survey type first.');
                }
                return;
            }

            $('#surveySubmissionsTitle').html('<i class="fa fa-table"></i> Submissions &mdash; ' + escapeHtml(label));

            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="survey-spinner"></span> Loading...');

            $.ajax({
                url: BASE_URL + '/data',
                method: 'GET',
                data: { sf_type: sfType },
                beforeSend: function () {
                    showTableLoader();
                    currentSfType = sfType;
                },
                success: function (resp) {
                    hideTableLoader();
                    $('#surveyTableWrapper').show();
                    destroyTable();
                    renderRows(resp.success ? resp.data : []);
                    initDataTable();
                },
                error: function () {
                    hideTableLoader();
                    $('#surveyEmptyState').show();
                    if (typeof showToast === 'function') {
                        showToast('error', 'Unable to load submissions. Please try again.');
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // ===== View =====
        $(document).on('click', '.view-btn', function () {
            var id = $(this).data('id');
            $('#viewSurveyModalBody').html('<div class="modal-loading-overlay"><div class="survey-spinner-lg"></div><span>Loading submission...</span></div>');
            $('#viewSurveyModal').modal('show');

            $.ajax({
                url: BASE_URL + '/' + id,
                method: 'GET',
                success: function (resp) {
                    if (!resp.success) {
                        $('#viewSurveyModalBody').html('<div class="text-danger">Unable to load record.</div>');
                        return;
                    }

                    var d = resp.data;
                    var html = '<div class="report-detail-block">';
                    html += '<div class="report-detail-grid">';
                    html += '<p class="report-detail-label">Enrollment No</p><p class="report-detail-value">' + escapeHtml(d.enrollment_no) + '</p>';
                    html += '<p class="report-detail-label">Survey Title</p><p class="report-detail-value">' + escapeHtml(d.title) + '</p>';
                    html += '<p class="report-detail-label">Submitted At</p><p class="report-detail-value">' + escapeHtml(d.created_at) + '</p>';
                    html += '</div></div>';

                    html += '<div class="report-detail-block"><div class="report-detail-grid">';
                    $.each(d.fields, function (key, value) {
                        if (value !== null && typeof value === 'object') {
                            value = JSON.stringify(value);
                        }
                        html += '<p class="report-detail-label">' + escapeHtml(key.replace(/_/g, ' ')) + '</p>';
                        html += '<p class="report-detail-value">' + escapeHtml(value) + '</p>';
                    });
                    html += '</div></div>';

                    $('#viewSurveyModalBody').html(html);
                },
                error: function () {
                    $('#viewSurveyModalBody').html('<div class="text-danger">Unable to load record.</div>');
                }
            });
        });

        // ===== Edit =====
        $(document).on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            $('#editSurveyId').val(id);
            $('#editSurveyModalBody').html('<div class="modal-loading-overlay"><div class="survey-spinner-lg"></div><span>Loading submission...</span></div>');
            $('#editSurveyModal').modal('show');

            $.ajax({
                url: BASE_URL + '/' + id,
                method: 'GET',
                success: function (resp) {
                    if (!resp.success) {
                        $('#editSurveyModalBody').html('<div class="text-danger">Unable to load record.</div>');
                        return;
                    }

                    var d = resp.data;
                    var html = '';
                    $.each(d.fields, function (key, value) {
                        if (value !== null && typeof value === 'object') {
                            value = JSON.stringify(value);
                        }
                        var safeKey = escapeHtml(key);
                        html += '<div class="form-group edit-field-row">';
                        html += '<label>' + escapeHtml(key.replace(/_/g, ' ')) + '</label>';
                        html += '<input type="text" class="form-control" name="fields[' + safeKey + ']" value="' + escapeHtml(value) + '">';
                        html += '</div>';
                    });

                    if (!html) {
                        html = '<div class="text-muted">No editable fields found for this submission.</div>';
                    }

                    $('#editSurveyModalBody').html(html);
                },
                error: function () {
                    $('#editSurveyModalBody').html('<div class="text-danger">Unable to load record.</div>');
                }
            });
        });

        $('#editSurveyForm').on('submit', function (e) {
            e.preventDefault();
            var id = $('#editSurveyId').val();
            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            var originalHtml = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<span class="survey-spinner"></span> Saving...');

            $.ajax({
                url: BASE_URL + '/' + id,
                method: 'PUT',
                data: $form.serialize(),
                success: function (resp) {
                    if (resp.success) {
                        $('#editSurveyModal').modal('hide');
                        if (typeof showToast === 'function') {
                            showToast('success', resp.message || 'Record updated successfully.');
                        }
                        if (currentSfType) {
                            loadSubmissions(currentSfType, { silent: true });
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', resp.message || 'Unable to update record.');
                        }
                    }
                },
                error: function () {
                    if (typeof showToast === 'function') {
                        showToast('error', 'Unable to update record. Please try again.');
                    }
                },
                complete: function () {
                    $submitBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // ===== Delete =====
        $(document).on('click', '.delete-btn', function () {
            var id = $(this).data('id');
            var $btn = $(this);

            if (!confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
                return;
            }

            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: BASE_URL + '/' + id,
                method: 'DELETE',
                success: function (resp) {
                    if (resp.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', resp.message || 'Record deleted successfully.');
                        }
                        if (currentSfType) {
                            loadSubmissions(currentSfType, { silent: true });
                        }
                    } else {
                        $btn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                        if (typeof showToast === 'function') {
                            showToast('error', resp.message || 'Unable to delete record.');
                        }
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                    if (typeof showToast === 'function') {
                        showToast('error', 'Unable to delete record. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endsection