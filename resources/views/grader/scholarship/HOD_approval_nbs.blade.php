@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("!23"); --}}
@include('components.toast-notifications')

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- Figma aligned NBS Verification visual layer. Business logic, IDs, endpoints and handlers are preserved. -->
<style>
    :root {
        --nbs-primary: #17675f;
        --nbs-primary-dark: #12574f;
        --nbs-primary-soft: rgba(23, 103, 95, 0.08);
        --nbs-text: #252525;
        --nbs-heading: #111827;
        --nbs-muted: #6b7280;
        --nbs-placeholder: #9ca3af;
        --nbs-border: #e5e7eb;
        --nbs-row: #f5f5f5;
        --nbs-white: #ffffff;
    }

    .nbs-page {
        min-height: calc(100vh - 30px);
        /* padding: 28px 30px 48px; */
        border-radius: 10px;
        /* background: linear-gradient(90deg, rgba(255,255,255,0.15) 0%, rgba(23,103,95,0.15) 100%); */
        color: var(--nbs-text);
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    }

    .nbs-shell {
        width: 100%;
        /* max-width: 1088px; */
        margin: 0 auto;
    }

    .nbs-heading {
        margin-bottom: 17px;
    }

    .nbs-heading h1 {
        margin: 0 0 4px;
        color: var(--nbs-text);
        font-size: 20px;
        font-weight: 600;
        line-height: 1.25;
    }

    .nbs-heading p {
        margin: 0;
        color: var(--nbs-text);
        font-size: 18px;
        font-weight: 400;
        line-height: 1.35;
    }

    .nbs-filter-card {
        padding: 30px 27px 20px;
        border-radius: 20px;
        background: var(--nbs-white);
    }

    .nbs-filter-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 24px;
    }

    .nbs-filter-field {
        min-width: 0;
    }

    .nbs-filter-field--full {
        grid-column: 1 / -1;
    }

    .nbs-filter-field label,
    .nbs-page .form-label,
    .nbs-page .col-form-label {
        display: block;
        margin: 0 0 8px;
        padding: 0;
        color: var(--nbs-heading);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
        text-align: left;
    }

    .nbs-page .form-select,
    .nbs-page select.form-select,
    .nbs-page .form-control {
        width: 100%;
        min-width: 0;
        height: 48px;
        padding: 0 16px;
        border: 1px solid var(--nbs-border) !important;
        border-radius: 8px !important;
        outline: none;
        background-color: #fff !important;
        color: var(--nbs-text) !important;
        font-size: 14px;
        font-weight: 400;
        box-shadow: none !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .nbs-page .form-select:focus,
    .nbs-page .form-control:focus {
        border-color: var(--nbs-primary) !important;
        box-shadow: 0 0 0 3px rgba(23,103,95,0.10) !important;
    }

    .nbs-filter-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }

    .nbs-page .nbs-btn {
        min-width: 105px;
        height: 50px;
        padding: 0 22px;
        border-radius: 8px !important;
        font-size: 14px;
        font-weight: 600;
        line-height: 48px;
        text-align: center;
        box-shadow: none !important;
        transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
    }

    .nbs-page .nbs-btn-reset {
        border: 1px solid var(--nbs-primary) !important;
        background: #fff !important;
        color: var(--nbs-primary) !important;
    }

    .nbs-page .nbs-btn-reset:hover {
        background: var(--nbs-primary-soft) !important;
        color: var(--nbs-primary-dark) !important;
    }

    .nbs-page .nbs-btn-submit {
        min-width: 117px;
        border: 1px solid var(--nbs-primary) !important;
        background: var(--nbs-primary) !important;
        color: #fff !important;
    }

    .nbs-page .nbs-btn-submit:hover {
        border-color: var(--nbs-primary-dark) !important;
        background: var(--nbs-primary-dark) !important;
        color: #fff !important;
    }

    .nbs-page .validation-alert {
        margin: 16px 0 0;
        border: 1px solid rgba(180,35,24,.18) !important;
        border-left: 4px solid #b42318 !important;
        border-radius: 10px;
        background: #fff !important;
        color: #7f1d1d !important;
        box-shadow: none;
    }

    .nbs-table-section {
        margin-top: 22px;
    }

    .nbs-table-heading {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .nbs-table-card {
        background: transparent;
    }

   .nbs-table-card .table-responsive {
    width: 100%;
    overflow: visible !important;
    overflow-y: visible !important;
    max-height: none !important;
    height: auto !important;
    border-radius: 10px;
}

/* horizontal scroll sirf small screens par */
@media (max-width: 991.98px) {
    .nbs-table-card .table-responsive {
        overflow-x: auto !important;
        overflow-y: hidden !important;
    }
}
.nbs-table-card,
.nbs-table-card .dataTables_wrapper,
#scholarshipTable_wrapper {
    height: auto !important;
    max-height: none !important;
    overflow-y: visible !important;
}

    .nbs-table-card .dataTables_wrapper {
    display: block !important;
    width: 100% !important;
    color: var(--nbs-text);
    font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
}

.nbs-table-card .dataTables_filter {
    width: 100% !important;
    margin: 0 0 14px !important;
    float: none !important;
    text-align: left !important;
}

.nbs-table-card .dataTables_filter input {
    width: 304px !important;
    height: 50px;
}

.nbs-table-card .table-responsive,
.nbs-table-card #scholarshipTable_wrapper,
.nbs-table-card table.dataTable {
    width: 100% !important;
}

.nbs-table-card .dataTables_length {
    float: left !important;
    margin-top: 18px !important;
}

.nbs-table-card .dataTables_info {
    float: left !important;
    padding: 24px 0 0 20px !important;
}

.nbs-table-card .dataTables_paginate {
    float: right !important;
    margin-top: 18px !important;
    padding: 0 !important;
}

.nbs-table-card .dataTables_wrapper::after {
    content: "";
    display: block;
    clear: both;
}

    .nbs-table-card .dataTables_filter {
        order: 1;
        width: 100%;
        margin: 0 0 14px;
        text-align: left !important;
        float: none !important;
    }

    .nbs-table-card .dataTables_filter label {
        margin: 0;
        color: transparent;
        font-size: 0;
        font-weight: 400;
    }

    .nbs-table-card .dataTables_filter input {
        width: 304px !important;
        height: 50px;
        margin: 0 !important;
        padding: 0 18px !important;
        border: 1px solid transparent !important;
        border-radius: 50px !important;
        outline: none !important;
        background: #fff !important;
        color: var(--nbs-text) !important;
        font-size: 14px !important;
        box-shadow: none !important;
    }

    .nbs-table-card .dataTables_filter input::placeholder {
        color: var(--nbs-muted);
    }

    .nbs-table-card .dataTables_filter input:focus {
        border-color: rgba(23,103,95,.35) !important;
    }

    .nbs-table-card table.dataTable,
    .nbs-table-card #scholarshipTable {
        order: 2;
        width: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        overflow: hidden;
        border-radius: 10px;
        background: #fff;
        box-shadow: none !important;
    }

    .nbs-table-card table.dataTable thead th,
    .nbs-table-card #scholarshipTable thead th {
        padding: 17px 14px !important;
        border: 0 !important;
        background: var(--nbs-primary) !important;
        color: #fff !important;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.4;
        text-align: left !important;
        white-space: nowrap;
    }

    .nbs-table-card table.dataTable thead th:first-visible,
    .nbs-table-card #scholarshipTable thead th:first-child {
        border-top-left-radius: 10px;
    }

    .nbs-table-card #scholarshipTable thead th:last-child {
        border-top-right-radius: 10px;
    }

    .nbs-table-card table.dataTable tbody td,
    .nbs-table-card #scholarshipTable tbody td {
        padding: 15px 14px !important;
        border: 0 !important;
        color: var(--nbs-text);
        font-size: 13px;
        font-weight: 400;
        line-height: 1.4;
        text-align: left !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .nbs-table-card table.dataTable tbody tr:nth-child(even),
    .nbs-table-card #scholarshipTable tbody tr:nth-child(even) {
        background: var(--nbs-row) !important;
    }

    .nbs-table-card table.dataTable tbody tr:nth-child(odd),
    .nbs-table-card #scholarshipTable tbody tr:nth-child(odd) {
        background: #fff !important;
    }

    .nbs-table-card table.dataTable tbody tr:hover {
        background: rgba(23,103,95,.06) !important;
    }

    .nbs-table-card .percentage-dropdown {
        min-width: 112px;
        height: 36px !important;
        padding: 0 10px !important;
        border: 1px solid var(--nbs-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--nbs-text) !important;
        font-size: 13px !important;
    }

    .nbs-table-card .btn-group .btn,
    .nbs-table-card .preview-btn,
    .nbs-table-card .approve-btn {
        margin: 0 4px 0 0;
        padding: 7px 12px !important;
        border: 1px solid var(--nbs-primary) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--nbs-primary) !important;
        font-size: 12px !important;
        font-weight: 600;
        box-shadow: none !important;
    }

    .nbs-table-card .btn-group .btn:hover,
    .nbs-table-card .preview-btn:hover,
    .nbs-table-card .approve-btn:hover {
        background: var(--nbs-primary-soft) !important;
        color: var(--nbs-primary-dark) !important;
    }

    .nbs-table-card .dataTables_length {
        order: 3;
        margin: 18px 16px 0 0;
        float: none !important;
    }

    .nbs-table-card .dataTables_length label {
        margin: 0;
        color: transparent;
        font-size: 0;
    }

    .nbs-table-card .dataTables_length select {
        min-width: 110px;
        height: 32px;
        padding: 0 34px 0 12px;
        border: 1px solid var(--nbs-text) !important;
        border-radius: 50px !important;
        background-color: transparent !important;
        color: var(--nbs-text) !important;
        font-size: 13px !important;
    }

    .nbs-table-card .dataTables_info {
        order: 4;
        flex: 1 1 220px;
        padding: 18px 0 0 !important;
        color: var(--nbs-text) !important;
        font-size: 13px;
        line-height: 32px;
    }

    .nbs-table-card .dataTables_paginate {
        order: 5;
        margin-left: auto;
        padding: 18px 0 0 !important;
        float: none !important;
    }

    .nbs-table-card .dataTables_paginate .paginate_button {
        min-width: 30px !important;
        height: 30px;
        margin: 0 2px !important;
        padding: 5px 8px !important;
        border: 0 !important;
        border-radius: 50% !important;
        background: transparent !important;
        color: var(--nbs-text) !important;
        box-shadow: none !important;
        font-size: 13px;
        line-height: 20px;
    }

    .nbs-table-card .dataTables_paginate .paginate_button.current,
    .nbs-table-card .dataTables_paginate .paginate_button.current:hover {
        background: var(--nbs-primary) !important;
        color: #fff !important;
    }

    .nbs-table-card .dataTables_paginate .paginate_button:hover {
        background: var(--nbs-primary-soft) !important;
        color: var(--nbs-primary) !important;
    }

    .nbs-table-card .dt-buttons {
        order: 1;
        margin: 0 0 14px auto;
    }

    .nbs-table-card .dt-button {
        margin-left: 8px !important;
        padding: 9px 14px !important;
        border: 1px solid var(--nbs-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--nbs-primary) !important;
        box-shadow: none !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    .nbs-table-card .dataTables_processing {
        z-index: 3;
        border: 0;
        background: rgba(255,255,255,.92);
        color: var(--nbs-primary);
    }

    #filterStatus {
        margin: 0 0 12px;
        color: var(--nbs-muted) !important;
        font-size: 12px;
    }

    .theme-bg {
        background: var(--nbs-primary) !important;
        color: #fff !important;
    }

    .theme-text {
        color: var(--nbs-primary) !important;
    }

    .theme-border {
        border-color: var(--nbs-primary) !important;
    }

    .theme-btn {
        border-color: var(--nbs-primary) !important;
        color: var(--nbs-primary) !important;
    }

    .modal-backdrop { z-index: 1040; }
    .modal { z-index: 1050; }
    .modal-dialog { pointer-events: auto; }
    .modal-dialog.modal-half { max-width: 50%; margin: auto; }
    .modal-content { height: auto; max-height: 90vh; overflow-y: auto; }

    @media (max-width: 991.98px) {
        .nbs-page { padding: 24px 20px 40px; }
        .nbs-filter-grid { grid-template-columns: 1fr; }
        .nbs-filter-field--full { grid-column: auto; }
    }

    @media (max-width: 767.98px) {
        .nbs-page { padding: 20px 14px 32px; border-radius: 0; }
        .nbs-heading p { font-size: 15px; }
        .nbs-filter-card { padding: 22px 18px; border-radius: 16px; }
        .nbs-filter-actions { flex-wrap: wrap; }
        .nbs-page .nbs-btn { flex: 1 1 120px; }
        .nbs-table-card .dataTables_filter input { width: 100% !important; }
        .nbs-table-card .dataTables_length,
        .nbs-table-card .dataTables_info,
        .nbs-table-card .dataTables_paginate { width: 100%; margin-right: 0; text-align: center !important; }
        .nbs-table-card .dataTables_paginate { margin-left: 0; }
        .modal-dialog.modal-half { max-width: 95%; }
    }
    #previewModal {
    z-index: 99999 !important;
}

.modal-backdrop.show {
    z-index: 99990 !important;
}

#previewModal .modal-dialog {
    position: relative;
    z-index: 100000 !important;
}
</style>

@php
$admissionApprovalUsernames = ['asbah.rehman', 'sidra.tanveer'];
$currentUsername = auth()->user()->username;
$isAdmissionApprovalUser = in_array($currentUsername, $admissionApprovalUsernames);
@endphp

<div class="nbs-page">
    <div class="nbs-shell">
        <div class="nbs-heading">
            <h1>NBS HOD Approval</h1>
            <p>Expert Minds</p>
        </div>

        <form id="scholarshipForm" method="post">
            <section class="nbs-filter-card">
                <div class="nbs-filter-grid">
                    <!-- Application Session Dropdown -->
                    <div class="nbs-filter-field">
                        <label for="application_sess_id" class="col-form-label">Application Session</label>
                        <select id="application_sess_id" name="application_sess_id" class="form-select" required>
                            <option value="">Select Application Session</option>
                        </select>
                    </div>

                    <div class="nbs-filter-field">
                        <label for="student_type" class="col-form-label">Applicants / Student</label>
                        <select id="student_type" name="student_type" class="form-select" required>
                            <option value="">Select Student Type</option>
                            <option value="existing">Existing</option>
                            <option value="new_applicants">New Applicants</option>
                        </select>
                    </div>

                    <!-- Roadmap Session Dropdown - kept exactly as a hidden functional field -->
                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none">
                        <label for="ad_sess_id" class="col-form-label">Roadmap Session</label>
                        <select id="ad_sess_id" name="ad_sess_id" class="form-select" required>
                            <option value="">Select Roadmap Session</option>
                        </select>
                    </div>

                    <!-- Admission Schedule - shown by the existing student type logic -->
                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none;" id="adm_sch_div">
                        <label for="adm_sch" class="col-form-label">Admission Schedule</label>
                        <select id="adm_sch" name="adm_sch" class="form-select" required>
                            <option value="">Select Admission Schedule</option>
                            <option value="1">Early Round</option>
                            <option value="2">Regular Round</option>
                            <option value="3">Rolling Round</option>
                        </select>
                    </div>

                    <!-- School Dropdown - retained hidden -->
                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none">
                        <label for="ad_school_id" class="col-form-label">School</label>
                        <select id="ad_school_id" name="ad_school_id" class="form-select" required>
                            <option value="">---All Schools---</option>
                        </select>
                    </div>

                    <!-- Admission Schedules Dropdown - retained hidden -->
                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none;">
                        <label for="admission_sch" class="col-form-label">Admission Schedules</label>
                        <select id="admission_sch" name="admission_sch" class="form-select" required>
                            <option value="">Select Admission Schedule</option>
                        </select>
                    </div>

                    <!-- Hidden dropdowns retained -->
                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none;">
                        <label for="selected_filter_type" class="col-form-label">Selected Applicant</label>
                        <select id="selected_filter_type" name="selected_filter_type" class="form-select">
                            <option value="">All</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="nbs-filter-field nbs-filter-field--full" style="display:none;">
                        <label for="status_filter_type" class="col-form-label">Fee Status</label>
                        <select id="status_filter_type" name="status_filter_type" class="form-select">
                            <option value="">All</option>
                            <option value="Verified Applicants">Verified Applicants</option>
                            <option value="Non Verified Applicants">Non Verified Applicants</option>
                        </select>
                    </div>
                </div>

                <div class="nbs-filter-actions">
                    <!-- Original hidden Generate Excel action retained -->
                    <button type="submit"
                            style="display:none;"
                            class="btn nbs-btn">
                        <i class="fa fa-file-excel-o"></i> Generate Excel
                    </button>

                    <button type="button" onclick="resetFilters()" class="btn nbs-btn nbs-btn-reset">
                        Reset
                    </button>

                    <button type="button" onclick="applyScholarshipFilter()" class="btn nbs-btn nbs-btn-submit">
                        Submit
                    </button>
                </div>
            </section>
        </form>

        <!-- Validation alert retained -->
        <div id="validationAlert" class="alert validation-alert" role="alert" style="display: none;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fa fa-exclamation-circle theme-text mr-2"></i>
                    <span id="validationMessage"></span>
                </div>
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

        <section class="nbs-table-section">
            <h2 class="nbs-table-heading">Scholarship Applicants List</h2>
            {{-- <div id="filterStatus" class="small text-muted" style="display: none;">
                <i class="fa fa-filter mr-1"></i> Filters applied
            </div> --}}

            <div class="nbs-table-card">
                <div class="table-responsive">
                    <table id="scholarshipTable" class="table table-bordered table-striped table-hover text-center display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="display:none"></th> <!-- Hidden column for sorting -->
                                <th scope="col">RFID</th>
                                <th scope="col">Percentage</th>
                                <th scope="col">House Status</th>
                                <th scope="col">Monthly Income</th>
                                <th scope="col">Assets</th>
                                <th scope="col">Expenses</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows will be dynamically injected by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Preview Modal -->
<style>
    /* Enhanced Applicant Preview Modal Styles */
    #previewModal .modal-content {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(21, 72, 163, 0.15), 0 1.5px 4px rgba(0,0,0,0.08);
        border: none;
    }
    #previewModal .modal-header {
        background: linear-gradient(90deg, #1548a3 0%, #3a7bd5 100%);
        color: #fff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 2px 8px rgba(21, 72, 163, 0.08);
    }
    #previewModal .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    #previewModal .close {
        color: #fff;
        opacity: 1;
        font-size: 1.5rem;
        font-weight: 700;
        transition: color 0.2s;
    }
    #previewModal .close:hover {
        color: #ffd700;
    }
    #previewModal .modal-body {
        background: #f8f9fa;
        padding: 1.5rem;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #previewFrame {
        width: 100%;
        height: 70vh;
        min-height: 400px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(21, 72, 163, 0.06);
    }
    @media (max-width: 768px) {
        #previewModal .modal-body {
            padding: 0.5rem;
        }
        #previewFrame {
            height: 50vh;
            min-height: 200px;
        }
    }

    .nbs-table-card .dataTables_wrapper {
    width: 100% !important;
    display: block !important;
    font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    color: var(--nbs-text);
}

/* SEARCH */
.nbs-dt-top {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 20px;
}

.nbs-table-card .dataTables_filter {
    float: none !important;
    margin: 0 !important;
    padding: 0 !important;
    text-align: left !important;
}

.nbs-table-card .dataTables_filter label {
    margin: 0 !important;
    font-size: 0;
}

.nbs-table-card .dataTables_filter input {
    width: 304px !important;
    height: 50px !important;
    margin: 0 !important;
    padding: 0 20px !important;
    border: 0 !important;
    border-radius: 50px !important;
    background: #fff !important;
    color: var(--nbs-text) !important;
    font-size: 14px !important;
    outline: none !important;
    box-shadow: none !important;
}

/* BOTTOM CONTROLS */
.nbs-dt-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 18px;
    width: 100%;
}

.nbs-dt-bottom-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.nbs-dt-bottom-right {
    margin-left: auto;
}

/* ITEMS PER PAGE */
.nbs-table-card .dataTables_length {
    float: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.nbs-table-card .dataTables_length label {
    margin: 0 !important;
    font-size: 0;
}

.nbs-table-card .dataTables_length select {
    min-width: 110px !important;
    height: 32px !important;
    padding: 0 14px !important;
    border: 1px solid #252525 !important;
    border-radius: 50px !important;
    background: transparent !important;
    color: #252525 !important;
    font-size: 13px !important;
    box-shadow: none !important;
}

/* SHOWING INFO */
.nbs-table-card .dataTables_info {
    float: none !important;
    margin: 0 !important;
    padding: 0 !important;
    color: #252525 !important;
    font-size: 13px !important;
    line-height: 32px;
}

/* PAGINATION */
.nbs-table-card .dataTables_paginate {
    float: none !important;
    margin: 0 !important;
    padding: 0 !important;
    text-align: right !important;
}

.nbs-table-card .dataTables_paginate .paginate_button,
.nbs-table-card .dataTables_paginate .page-link {
    min-width: 30px !important;
    height: 30px !important;
    margin: 0 3px !important;
    padding: 5px 8px !important;
    border: 0 !important;
    border-radius: 50% !important;
    background: transparent !important;
    color: #252525 !important;
    box-shadow: none !important;
    font-size: 13px !important;
}

.nbs-table-card .dataTables_paginate .paginate_button.current,
.nbs-table-card .dataTables_paginate .page-item.active .page-link {
    background: var(--nbs-primary) !important;
    color: #fff !important;
}

.nbs-table-card .dataTables_paginate .disabled,
.nbs-table-card .dataTables_paginate .disabled .page-link {
    background: transparent !important;
    color: #9ca3af !important;
    opacity: .65;
}

/* MOBILE */
@media (max-width: 767.98px) {
    .nbs-dt-top {
        width: 100%;
    }

    .nbs-table-card .dataTables_filter,
    .nbs-table-card .dataTables_filter input {
        width: 100% !important;
    }

    .nbs-dt-bottom {
        flex-direction: column;
        align-items: stretch;
    }

    .nbs-dt-bottom-left {
        justify-content: space-between;
    }

    .nbs-dt-bottom-right {
        margin-left: 0;
        text-align: center;
    }
}
</style>
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90vw; margin: 1.75rem auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fa fa-eye mr-2"></i>Applicant Preview
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="card">
            <div class="card-header theme-bg">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fa fa-check-circle mr-2"></i> HOD Approval - Form ID: <span id="modal_form_id"></span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Hidden fields -->
                <input type="hidden" id="approve_spd_id">
                <input type="hidden" id="apply_id" name="apply_id">
                
                <!-- Percentage Field -->
                <div class="form-group">
                    <label for="approve_percentage" class="form-label theme-text">
                        <i class="fa fa-percent mr-1"></i> Percentage
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="approve_percentage" 
                           name="approve_percentage" 
                           placeholder="Enter percentage (0-100)"
                           maxlength="6">
                    <div class="invalid-feedback" id="percentage-error">
                        Please enter a valid percentage between 0 and 100.
                    </div>
                </div>
                
                <!-- HOD Remarks Field -->
                <div class="form-group">
                    <label for="approve_remarks" class="form-label theme-text">
                        <i class="fa fa-comment mr-1"></i> HOD Remarks
                    </label>
                    <textarea class="form-control" 
                              id="approve_remarks" 
                              name="approve_remarks" 
                              rows="4" 
                              placeholder="Enter HOD remarks"></textarea>
                    <div class="invalid-feedback">
                        Please enter HOD remarks.
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="submitHODApproval()">
                    <i class="fa fa-check mr-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script>
// Initialize DataTable
let scholarshipTable;
let staffUrl = "{{ config('app.staff_url') }}";
// Load Sessions and Schools on page load
$(document).ready(function() {
    // Load Application Sessions
    function loadApplicationSessions() {
        $.ajax({
            url: '<?= url('/sessions') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#application_sess_id');
                select.empty().append('<option value="">Select Application Session</option>');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.sess_id)
                        .text(item.sess_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading application sessions:', error);
                showToast('error', 'Error!', 'Failed to load application sessions');
            }
        });
    }

    $('#student_type').on('change', function () {
        toggleAdmissionSchedule();
        applyScholarshipFilter();
    });

    function toggleAdmissionSchedule() {
        const selected = $('#student_type').val();
        if (selected === 'new_applicants') {
            $('#adm_sch_div').show();
        } else {
            $('#adm_sch_div').hide();
        }
    }

    $('#adm_sch').on('change', function () {
        
        applyScholarshipFilter();
    });

    // Load Roadmap Sessions
    function loadSessions() {
        $.ajax({
            url: '<?= url('/sessions') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#ad_sess_id');
                select.empty().append('<option value="">Select Roadmap Session</option>');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.sess_id)
                        .text(item.sess_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading sessions:', error);
            }
        });
    }

    // Call functions to load sessions
    loadApplicationSessions();
    loadSessions();
    
    // Apply theme styling to buttons
    $('.btn').addClass('theme-btn');
    
    // Add nice transition effect to all buttons
    $('.btn').on('mouseenter', function() {
        $(this).css('transform', 'translateY(-2px)');
    }).on('mouseleave', function() {
        $(this).css('transform', 'translateY(0)');
    });
    
    // Close alert when clicking the close button
    $(document).on('click', '.alert .close', function() {
        $(this).closest('.alert').hide();
    });

    // Add onchange handlers to all filter dropdowns
    $('#application_sess_id, #ad_sess_id, #ad_school_id, #admission_sch, #selected_filter_type, #status_filter_type').on('change', function() {
        applyScholarshipFilter();
    });
    // Optionally hide the Apply Filter button if still present
    $("button:contains('Apply Filter')").hide();
    
    // Allow modal to close properly
    $('#approveModal').on('hidden.bs.modal', function() {
        // Clear form fields when modal is closed
        $('#approve_percentage').val('');
        $('#approve_remarks').val('');
        $('#approveModalAlert').hide();
    });

    if ($('#ad_school_id').val() == '') {
        $('#ad_school_id').trigger('change');
    }





    // Real-time percentage validation
    $(document).on('input', '#approve_percentage', function() {
        const input = $(this);
        const value = input.val();
        const errorDiv = $('#percentage-error');
        
        // Remove any non-numeric characters except decimal point
        let cleanValue = value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        const decimalCount = (cleanValue.match(/\./g) || []).length;
        if (decimalCount > 1) {
            cleanValue = cleanValue.replace(/\.+$/, ''); // Remove trailing dots
            const parts = cleanValue.split('.');
            cleanValue = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (cleanValue.includes('.')) {
            const parts = cleanValue.split('.');
            if (parts[1].length > 2) {
                cleanValue = parts[0] + '.' + parts[1].substring(0, 2);
            }
        }
        
        // Update input value
        input.val(cleanValue);
        
        // Validate range
        const numValue = parseFloat(cleanValue);
        
        if (cleanValue === '' || cleanValue === '.') {
            // Empty or just decimal point - remove error
            input.removeClass('is-invalid');
            errorDiv.hide();
        } else if (isNaN(numValue)) {
            // Invalid number
            input.addClass('is-invalid');
            errorDiv.text('Please enter a valid number.').show();
        } else if (numValue < 0) {
            // Less than 0
            input.addClass('is-invalid');
            errorDiv.text('Percentage cannot be less than 0.').show();
        } else if (numValue > 100) {
            // Greater than 100
            input.addClass('is-invalid');
            errorDiv.text('Percentage cannot be greater than 100.').show();
        } else {
            // Valid percentage
            input.removeClass('is-invalid');
            errorDiv.hide();
        }
    });
    
    // Prevent paste of invalid characters
    $(document).on('paste', '#approve_percentage', function(e) {
        e.preventDefault();
        const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        const cleanText = pastedText.replace(/[^0-9.]/g, '');
        
        if (cleanText) {
            const numValue = parseFloat(cleanText);
            if (!isNaN(numValue) && numValue >= 0 && numValue <= 100) {
                $(this).val(cleanText);
            }
        }
    });
    
    // Prevent keydown of invalid characters
    $(document).on('keydown', '#approve_percentage', function(e) {
        const key = e.key;
        const value = $(this).val();
        
        // Allow: backspace, delete, tab, escape, enter, decimal point, numbers
        if ([8, 9, 27, 13, 46, 110, 190].indexOf(e.keyCode) !== -1 ||
            // Allow numbers
            (e.keyCode >= 48 && e.keyCode <= 57) ||
            // Allow numpad numbers
            (e.keyCode >= 96 && e.keyCode <= 105)) {
            return;
        }
        
        // Allow decimal point only once
        if (key === '.' && !value.includes('.')) {
            return;
        }
        
        // Block all other keys
        e.preventDefault();
    });
});

// Function to load sessions
function loadSessions() {
    $.ajax({
        url: '<?= url('/sessions') ?>',
        type: 'GET',
        success: function(data) {
            var select = $('#ad_sess_id');
            select.empty().append('<option value="">Select Roadmap Session</option>');
            data.forEach(function(item) {
                select.append($('<option></option>')
                    .attr('value', item.sess_id)
                    .text(item.sess_name));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading sessions:', error);
        }
    });
}

// Function to load schools
function loadSchools() {
    $.ajax({
        url: '<?= url('/schools') ?>',
        type: 'GET',
        success: function(data) {
            var select = $('#ad_school_id');
            select.empty().append('<option value="">---All Schools---</option>');
            data.forEach(function(item) {
                select.append($('<option></option>')
                    .attr('value', item.school_id)
                    .text(item.school_name));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading schools:', error);
        }
    });
}

// Session Change Event
$('#ad_sess_id').change(function() {
    var sessId = $(this).val();
    
    // Clear dependent dropdowns
    $('#ad_school_id').empty().append('<option value="">---All Schools---</option>');
    $('#admission_sch').empty().append('<option value="">Select Admission Schedule</option>');
    
    // Load schools
    if(sessId) {
        $.ajax({
            url: '<?= url('/schools') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#ad_school_id');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.school_id)
                        .text(item.school_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading schools:', error);
            }
        });
    }
});

// School Change Event
$('#ad_school_id').change(function() {
    var schoolId = $(this).val();
    var sessId = $('#ad_sess_id').val();
    
    // Clear admission schedules dropdown
    $('#admission_sch').empty().append('<option value="">Select Admission Schedule</option>');
    
    // Load admission schedules
    if(schoolId && sessId) {
        $.ajax({
            url: '<?= url('/api/adm_schedules') ?>',
            type: 'GET',
            data: { school_id: schoolId, sess_id: sessId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
                    },
            success: function(data) {
                var select = $('#admission_sch');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.admission_sch_id)
                        .text(item.admission_sch_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading admission schedules:', error);
            }
        });
    }
});

// function applyScholarshipFilter() {
//     $('#validationAlert').hide();
    
//     var sessionId = $('#application_sess_id').val();
//     var student_type = $('#student_type').val();
//     var admissionSch = $('#adm_sch').val();

//     $('#scholarshipTable').addClass('loading');
//     $('#filterStatus').show();

//     // Destroy existing DataTable and clear body
//     if ($.fn.DataTable.isDataTable('#scholarshipTable')) {
//         $('#scholarshipTable').DataTable().clear().destroy();
//     }
//     $('#scholarshipTable tbody').empty();
    
//     const data = {
//         ad_sess_id: sessionId,
//         student_type: student_type,
//         admission_sch: admissionSch
//     };

//     $.ajax({
//         url: '/api/get-data-nbs-form-hod-approval',
//         method: 'GET',
//         data: data,
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
//                 "Authorization": 'Bearer {{ auth()->user()->api_token }}',
//                 "Accept": "application/json"
//                     },
//         dataType: 'json',
//         success: function(response) {
//             $('#scholarshipTable').removeClass('loading');
//             if (response && response.status && Array.isArray(response.data)) {
//                 const processedData = processDataForTable(response.data);
//                 initializeDataTable(processedData);
//             } else {
//                 initializeDataTable([]);
//             }
//         },
//         error: function(xhr, status, error) {
//             $('#scholarshipTable').removeClass('loading');
//             initializeDataTable([]);
//         }
//     });
// }
function applyScholarshipFilter() {
    $('#validationAlert').hide();

    var sessionId = $('#application_sess_id').val();
    var student_type = $('#student_type').val();
    var admissionSch = $('#adm_sch').val();

    $('#scholarshipTable').addClass('loading');
    $('#filterStatus').show();

    const data = {
        ad_sess_id: sessionId,
        student_type: student_type,
        admission_sch: admissionSch
    };

    $.ajax({
        url: '/api/get-data-nbs-form-hod-approval',
        method: 'GET',
        data: data,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        dataType: 'json',
        success: function(response) {
            $('#scholarshipTable').removeClass('loading');
            
            const processedData = response && response.status && Array.isArray(response.data)
                ? processDataForTable(response.data)
                : [];

            const dataTable = $('#scholarshipTable').DataTable();

            // Instead of destroying the table:
            dataTable.clear();                      // Clear existing rows
            dataTable.rows.add(processedData);      // Add new rows
            dataTable.draw(false);                  // Redraw without resetting paging
            bindTableEventHandlers();               // <-- Ensure Preview button works after update
        },
        error: function(xhr, status, error) {
            $('#scholarshipTable').removeClass('loading');
            const dataTable = $('#scholarshipTable').DataTable();
            dataTable.clear().draw(false);
        }
    });
}

// Update initializeDataTable to always clear and destroy before re-initializing
function initializeDataTable(data) {
    // Always clear and destroy before re-initializing
    if ($.fn.DataTable.isDataTable('#scholarshipTable')) {
        $('#scholarshipTable').DataTable().clear().destroy();
    }
    $('#scholarshipTable tbody').empty();

    scholarshipTable = $('#scholarshipTable').DataTable({
        data: data,
        responsive: true,
        processing: true,
        destroy: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row mb-2"<"col-12"B>>' + // Buttons in their own row
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                className: 'btn btn-success',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-secondary',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table",
            paginate: {
                first: "First",
                last: "Last", 
                next: "Next",
                previous: "Previous"
            },
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        columnDefs: [
            { targets: 0, visible: false, type: 'order-number' }, // HIDDEN column for sorting
            { responsivePriority: 1, targets: [1, 2] },
            { responsivePriority: 2, targets: [3, 4, 5, 6] },
            { responsivePriority: 3, targets: 7 }
        ],
        order: [[0, 'asc']],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        }
    });
    // Re-bind event handlers after table is reinitialized
    bindTableEventHandlers();
}

// Make sure DOM is fully loaded before initializing
// $(document).ready(function() {
//     // Initialize DataTable first time
//     initializeDataTable([]);
    
//     // Load initial data
//     applyScholarshipFilter();
    
//     // Handle student type change
//     $('#student_type').on('change', function() {
//         const selected = $(this).val();
//         if (selected === 'new_applicants') {
//             $('#adm_sch_div').show();
//             applyScholarshipFilter(); // Only call here for new_applicants
//         } else {
//             $('#adm_sch_div').hide();
//             $('#adm_sch').val('').trigger('change'); // This will trigger the handler below
//             // Do NOT call applyScholarshipFilter() here
//         }
//     });

//     // Handle session and other filter changes
//     $('#application_sess_id, #ad_sess_id, #adm_sch').on('change', function() {
//         applyScholarshipFilter();
//     });
//     });

$(document).ready(function() {
    $('#scholarshipTable').DataTable({
        // your initialization options here
        responsive: true,
        pageLength: 10,
          lengthMenu: [
        [10, 25, 50, -1],
        ['10 Items', '25 Items', '50 Items', 'All Items']
    ],
     dom:
        '<"nbs-dt-top"f>' +
        't' +
        '<"nbs-dt-bottom"<"nbs-dt-bottom-left"li><"nbs-dt-bottom-right"p>>',

    language: {
        search: '',
        searchPlaceholder: 'Search Scholarship ...',
        info: 'Showing _START_-_END_ of _TOTAL_',
        infoEmpty: 'Showing 0-0 of 0',
        zeroRecords: 'No matching records found',
        emptyTable: 'No data available in table',
        paginate: {
            previous: 'Previous',
            next: 'Next'
        }
    },
        columns: [
            { visible: false },  // Hidden sort column
            { title: 'Ref ID' },
            { title: 'Percentage' },
            { title: 'House Status' },
            { title: 'Monthly Income' },
            { title: 'Assets' },
            { title: 'Expenses' },
            { title: 'Actions', orderable: false }
        ]
    });
});




// Add some custom CSS for loading state
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }
        .loading:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin-top: -20px;
            margin-left: -20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `)
    .appendTo('head');

// Add JavaScript function for reset button
function resetFilters() {
    // Clear all select selections
    $('#application_sess_id').val('').trigger('change');
    $('#ad_sess_id').val('').trigger('change');
    // $('#ad_school_id').val('').trigger('change');
    // $('#admission_sch').val('').trigger('change');
    // $('#selected_filter_type').val('').trigger('change');
    // $('#status_filter_type').val('').trigger('change');
    
    loadSchools();

    // Clear the table if it exists
    if (scholarshipTable) {
        scholarshipTable.clear().draw();
    }
}

// Add this function for exporting all data
function exportTableToExcel() {
    // Get current filter parameters
    const data = {
        ad_sess_id: $('#ad_sess_id').val(),
        ad_school_id: $('#ad_school_id').val(),
        admission_sch: $('#admission_sch').val(),
        scholarship_type: $('#scholarship_type').val(),
        scholarship_filter_type: $('#scholarship_filter_type').val(),
        selected_filter_type: $('#selected_filter_type').val(),
        status_filter_type: $('#status_filter_type').val(),
        export_all: 'true'
    };
    
    // Redirect to a page that will generate the Excel file with all applicants
    window.open('scholarship_export_all.php?' + $.param(data), '_blank');
}


function submitHODApproval(params = null) {
    let spdId, percentage, remarks, action, aai_id;
    
    if (params) {
        // Disapprove case (direct call)
        spdId = params.spd_id;
        percentage = params.percentage;
        remarks = params.hod_remarks;
        action = params.action;
        aai_id = params.aai_id;
    } else {
        // Approve case (from modal)
        spdId = $('#approve_spd_id').val();
        aai_id = $('#apply_id').val();
        percentage = $('#approve_percentage').val();
        remarks = $('#approve_remarks').val();
        action = 'approve';
    }

    // ✅ Validate only if approving
    if (action === 'approve') {
        let isValid = true;
        let errorMessage = '';

        if (!percentage || percentage === '') {
            $('#approve_percentage').addClass('is-invalid');
            $('#percentage-error').text('Please enter percentage.').show();
            isValid = false;
            errorMessage = 'Please enter percentage.';
        } else {
            const numPercentage = parseFloat(percentage);
            if (isNaN(numPercentage)) {
                $('#approve_percentage').addClass('is-invalid');
                $('#percentage-error').text('Please enter a valid number.').show();
                isValid = false;
                errorMessage = 'Please enter a valid number.';
            } else if (numPercentage < 0 || numPercentage > 100) {
                $('#approve_percentage').addClass('is-invalid');
                $('#percentage-error').text('Percentage must be between 0 and 100.').show();
                isValid = false;
                errorMessage = 'Percentage must be between 0 and 100.';
            } else {
                $('#approve_percentage').removeClass('is-invalid');
                $('#percentage-error').hide();
            }
        }

        if (!remarks || remarks.trim() === '') {
            $('#approve_remarks').addClass('is-invalid');
            isValid = false;
            errorMessage += ' Please enter HOD remarks.';
        } else {
            $('#approve_remarks').removeClass('is-invalid');
        }

        if (!isValid) {
            showToast('danger', 'Error!', errorMessage);
            return;
        }
    }

    // ✅ Show loading for Approve button (modal)
    const approveBtn = $('#approveModal .btn-success');
    const originalText = approveBtn.html();
    if (action === 'approve') {
        approveBtn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Processing...');
        approveBtn.prop('disabled', true);
    }

    // ✅ AJAX request
    $.ajax({
        url: '/api/hod-approve-scholarship',
        method: 'POST',
        data: {
            spd_id: spdId,
            percentage: percentage,
            hod_remarks: remarks,
            action: action,
            applicant_id: aai_id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            if (response.status) {
                showToast('success', 'Success!', action === 'approve' ? 'Scholarship approved successfully!' : 'Scholarship disapproved successfully!');
                
                if (action === 'approve') {
                    $('#approveModal').modal('hide');
                    $('#approve_percentage').val('');
                    $('#approve_remarks').val('');
                }
                // ✅ Refresh the table data
                setTimeout(function() {
                    applyScholarshipFilter();
                }, 500);
            } else {
                showToast('danger', 'Error!', response.message || 'Failed to process request.');
            }
        },
        error: function(xhr) {
            let errorMsg = 'An error occurred while processing your request.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast('danger', 'Error!', errorMsg);
        },
        complete: function() {
            if (action === 'approve') {
                approveBtn.html(originalText);
                approveBtn.prop('disabled', false);
            }
        }
    });
}



function generatePreview(response) {
    const applicant = response.data || {};
    const siblings = response.siblings || [];
    const employed = response.employed || [];
    const unemployed = response.unemployed || [];
    const transport = response.transport || [];

    // Check Data Conditions
    const hasSiblingData = siblings && siblings.length > 0 && siblings.some(s => s.sibling_name && s.sibling_name.trim());
    const hasEmployedData = employed && employed.length > 0 && employed.some(e => e.employed_name && e.employed_name.trim());
    const hasUnemployedData = unemployed && unemployed.length > 0 && unemployed.some(u => u.unemployed_name && u.unemployed_name.trim());
    const hasTransportData = transport && transport.length > 0 && transport.some(t => t.vehicle_type && t.vehicle_type.trim());

    // List of fields to exclude
    const excludedFields = [
        'aai_id',
        'Applicant_name',
        'api_id',
        'registration_no',
        'father_name',
        'phone_no',
        'guardian_mobile_no',
        'school_name',
        'marks',
        'degree_certificate',
        'institution_name',
        'grades',
        'parent_name',
        'occupation',
        'office_phone',
        'designation',
        'organization_name',
        'fax_no',
        'residence_phone',
        'mobile_no',
        'residence_address',
        'residence_city',
        'office_address',
        'office_city',
        'sai_enrollment_no',
        'std_mobile_no'
    ];

    let html = `<div class="container-fluid">`;

    // Applicant Information Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E3F2FD; color: #1976D2;">
                <h5 class="mb-0"><i class="fa fa-user mr-2"></i>Applicant Information</h5>
            </div>
            <div class="card-body">
                <div class="row">`;
    
    // Loop through all applicant data fields, excluding the specified ones
    Object.entries(applicant).forEach(([key, value]) => {
        if (!excludedFields.includes(key.toLowerCase())) {
            html += `
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small">
                        <i class="fa fa-tag mr-1"></i>${key.replace(/_/g, ' ').toUpperCase()}
                    </label>
                    <div class="form-control form-control-sm">${value || 'N/A'}</div>
                </div>`;
        }
    });

    html += `
                </div>
            </div>
        </div>`;

    // Siblings Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E8F5E9; color: #2E7D32;">
                <h5 class="mb-0"><i class="fa fa-users mr-2"></i>Siblings Information</h5>
            </div>
            <div class="card-body">`;
    
    if (hasSiblingData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>School/College</th>
                            <th>Fee</th>
                            <th>Scholarship</th>
                            <th>Parent Contribution</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        siblings.forEach(sibling => {
            if (sibling.sibling_name && sibling.sibling_name.trim()) {
                html += `
                    <tr>
                        <td>${sibling.sibling_name || 'N/A'}</td>
                        <td>${sibling.sibling_age || 'N/A'}</td>
                        <td>${sibling.sibling_marital_status || 'N/A'}</td>
                        <td>${sibling.school_college_name || 'N/A'}</td>
                        <td>${sibling.annual_fee || 'N/A'}</td>
                        <td>${sibling.annual_scholarship || 'N/A'}</td>
                        <td>${sibling.annual_parents_contribution || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No siblings data found for this Applicant.
            </div>`;
    }
    html += `</div></div>`;

    // Employed Family Members Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #FFF3E0; color: #F57C00;">
                <h5 class="mb-0"><i class="fa fa-briefcase mr-2"></i>Employed Family Members</h5>
            </div>
            <div class="card-body">`;
    
    if (hasEmployedData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>Profession</th>
                            <th>Annual Income</th>
                            <th>Annual Contribution</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        employed.forEach(emp => {
            if (emp.employed_name && emp.employed_name.trim()) {
                html += `
                    <tr>
                        <td>${emp.employed_name || 'N/A'}</td>
                        <td>${emp.employed_age || 'N/A'}</td>
                        <td>${emp.employeed_marital_status || 'N/A'}</td>
                        <td>${emp.profession || 'N/A'}</td>
                        <td>${emp.annual_income || 'N/A'}</td>
                        <td>${emp.annual_contribution || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No employed family members data found.
            </div>`;
    }
    html += `</div></div>`;

    // Unemployed Family Members Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #FCE4EC; color: #C2185B;">
                <h5 class="mb-0"><i class="fa fa-user-times mr-2"></i>Unemployed Family Members</h5>
            </div>
            <div class="card-body">`;
    
    if (hasUnemployedData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>Reason of Unemployment</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        unemployed.forEach(unemp => {
            if (unemp.unemployed_name && unemp.unemployed_name.trim()) {
                html += `
                    <tr>
                        <td>${unemp.unemployed_name || 'N/A'}</td>
                        <td>${unemp.unemployed_age || 'N/A'}</td>
                        <td>${unemp.unemployed_marital_status || 'N/A'}</td>
                        <td>${unemp.unemployment_reason || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No unemployed family members data found.
            </div>`;
    }
    html += `</div></div>`;

    // Transport Information Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E8EAF6; color: #3F51B5;">
                <h5 class="mb-0"><i class="fa fa-car mr-2"></i>Transport Information</h5>
            </div>
            <div class="card-body">`;
    
    if (hasTransportData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Vehicle Type</th>
                            <th>Vehicle Model</th>
                            <th>Vehicle Year</th>
                            <th>Fuel Cost</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        transport.forEach(trans => {
            if (trans.vehicle_type && trans.vehicle_type.trim()) {
                html += `
                    <tr>
                        <td>${trans.vehicle_type || 'N/A'}</td>
                        <td>${trans.vehicle_model || 'N/A'}</td>
                        <td>${trans.vehicle_year || 'N/A'}</td>
                        <td>${trans.vehicle_fuel_cost || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No transport information found.
            </div>`;
    }
    html += `</div></div>`;

    html += `</div>`; // Close container-fluid

    document.getElementById('previewContent').innerHTML = html;
    $('#previewModal').modal('show');
}



function processDataForTable(data) {
    return data.map(function(applicant) {
      
        // Check if enrollment_no is present and not empty
        const hasEnrollment = applicant.sai_enrollment_no !== null && applicant.sai_enrollment_no !== undefined && applicant.sai_enrollment_no !== '';

        // Sorting column
        const sortValue = hasEnrollment ? (applicant.form_id || '') : (applicant.order_number || '');

        // Ref column
        const refValue = hasEnrollment
            ? `${applicant.ref_id || 'N/A'}`
            : `Ref-${applicant.order_number || 'N/A'}`;



        const actionButtons = `
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-info preview-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    data-session-id="${applicant.sess_id || ''}"
                    data-sai-enrollment-no="${applicant.sai_enrollment_no || ''}">
                    <i class="fa fa-eye mr-1"></i> Preview
                </button>
            </div>`;

       const percentageVal = applicant.percentage !== null && applicant.percentage !== undefined
            ? parseFloat(applicant.percentage)
            : null; // default to null for easier comparison

            const showCheckIcon = percentageVal === 0 || percentageVal === 25 || percentageVal === 50;

            const percentageDropdown = `
            ${showCheckIcon ? '<span class="fa fa-check-circle text-success mr-2" style="font-size: 1.2rem;"></span>' : ''}
            <select class="form-select form-select-sm percentage-dropdown" 
                data-aai-id="${applicant.aai_id}" 
                data-spd-id="${applicant.spd_id}" 
                data-sess-id="${applicant.sess_id}"
                data-form-id="${applicant.form_id || ''}">
                <option value="" ${percentageVal === null ? 'selected' : ''}>Select %</option>
                <option value="0" ${percentageVal === 0 ? 'selected' : ''}>0%</option>
                <option value="25" ${percentageVal === 25 ? 'selected' : ''}>25%</option>
                <option value="50" ${percentageVal === 50 ? 'selected' : ''}>50%</option>
            </select>
            `;

        const houseStatus = applicant.house_status || 'N/A';
        const totalIncome = parseFloat(applicant.total_income);
        const monthlyIncome = totalIncome && !isNaN(totalIncome)
            ? new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR', minimumFractionDigits: 2 }).format(totalIncome / 12)
            : 'N/A';

        const assets = [
            applicant.father_assets,
            applicant.mother_assets,
            applicant.guardian_assets,
            applicant.self_assets
        ];
        const totalAssets = assets.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
        const currentAssets = totalAssets > 0
            ? new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR', minimumFractionDigits: 2 }).format(totalAssets)
            : 'N/A';

        const bills = [
            applicant.avg_electricity_bill,
            applicant.avg_gas_bill,
            applicant.avg_ptcl_bill,
            applicant.avg_grocery_expense,
            applicant.avg_medical_expense
        ];
        const total = bills.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
        const monthlyExpense = total > 0
            ? new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR', minimumFractionDigits: 2 }).format(total)
            : 'N/A';

        return [
            sortValue, // HIDDEN column for sorting
            refValue,
            percentageDropdown,
            houseStatus,
            monthlyIncome,
            currentAssets,
            monthlyExpense,
            actionButtons
        ];
    });
}



$(document).off('change', '.percentage-dropdown').on('change', '.percentage-dropdown', function () {
    const $select = $(this);
    const percentage = $select.val();
    const aaiId = $select.data('aai-id');
    const spdId = $select.data('spd-id');
    const sessId = $select.data('sess-id');
    if (!percentage) return;
    $select.prop('disabled', true);
    $.ajax({
        url: '/api/hod-approve-scholarship',
        method: 'POST',
        data: {
            spd_id: spdId,
            percentage: percentage,
            action: 'approve',
            applicant_id: aaiId,
            sess_id: sessId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function (response) {
            $select.prop('disabled', false);
            if (response.status) {
                showToast('success', 'Success!', 'Scholarship percentage updated successfully!');
                // Refresh the DataTable so the icon and value both update
                applyScholarshipFilter();
            } else {
                showToast('danger', 'Error!', response.message || 'Failed to process request.');
            }
        },
        error: function () {
            $select.prop('disabled', false);
            showToast('danger', 'Error!', 'Error updating percentage');
        }
    });
});




function bindTableEventHandlers() {
    // Remove any previous handlers to avoid duplicates
    $('#scholarshipTable').off('click', '.preview-btn').on('click', '.preview-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const sessionId = $(this).data('session-id');
        const saiEnrollmentNo = $(this).data('sai-enrollment-no');

        const checkSaiEnrollmentNo = !saiEnrollmentNo || saiEnrollmentNo === '';
        const excelUrl = checkSaiEnrollmentNo 
            ? `${staffUrl}/HOD/nbs_excel_admin.php` 
            : `${staffUrl}/HOD/nbs_excel_ro.php`;

        const fullUrl = `${excelUrl}?applicant_id=${applicantId}&ad_sess_id=${sessionId}&hodPreview=1`;

        $('#previewFrame').attr('src', '');
        $('#previewModal').modal('show');

        setTimeout(() => {
            $('#previewFrame').attr('src', fullUrl);
        }, 300);
    });

    // Approve button handler (keep as before)
    $('#scholarshipTable').off('click', '.approve-btn').on('click', '.approve-btn', function() {
        const spdId = $(this).data('spd-id');
        const currentPercentage = $(this).data('current-percentage');
        const formId = $(this).data('form-id');
        const aaiId = $(this).data('aai-id');
        $('#approve_spd_id').val(spdId);
        $('#apply_id').val(aaiId);
        $('#approve_percentage').val(currentPercentage === 'N/A' ? '' : currentPercentage);
        $('#modal_form_id').text(formId);
        $('#approveModal').modal('show');
    });
}


</script>
@stop

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<!-- DataTables JS (ensure present) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS and dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

