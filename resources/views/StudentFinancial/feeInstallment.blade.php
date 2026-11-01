@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
</head>
<style>
    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
    .dt-button {
        background-color: #4454c3;
        color: #4454c3;
        border: none;
        padding: 5px 10px;
        margin: 5px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
        margin-top:5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }
    #clear{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear:hover {
        background-color: #1b82cb; /* New color on hover */
        border-color: #1b82cb;     /* Matching border color on hover */
    }
    #clear_dates{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear_dates:hover {
        background-color: #1b82cb; /* New color on hover */
        border-color: #1b82cb;     /* Matching border color on hover */
    }
    .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
    }
    #filterChallan {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    #unpaidCheckbox {
        margin: 0;
        transform: scale(1.2); /* Adjust size for better visibility */
    }

    #filterChallan label {
        margin: 0;
        font-size: 14px; /* Adjust the label text size */
    }
    .edit-btn, .delete-btn{
        width:76px;
    }
    .installment-section {
        margin: 0 0 16px;
        padding: 16px;
        border: 1px solid #dfe5ec;
        border-radius: 6px;
        background: #fff;
    }
    .installment-section--scope {
        background: #f8fafc;
        border-left: 4px solid #4454c3;
    }
    .installment-section--update {
        background: #fffaf0;
        border-left: 4px solid #f0ad00;
    }
    .installment-section__title {
        margin: 0 0 4px;
        font-size: 17px;
        font-weight: 600;
    }
    .installment-section__help {
        display: block;
        margin-bottom: 14px;
        color: #6c757d;
    }
    .installment-section .form-group {
        margin-bottom: 8px;
    }
    .upd-scope-info {
        padding: 12px 14px;
        margin-bottom: 10px;
        border: 1px solid #f0d68a;
        border-radius: 4px;
        background: #fffdf5;
        font-size: 13px;
    }
    .upd-scope-info__title {
        margin: 0 0 8px;
        font-weight: 600;
        color: #5a4a00;
    }
    .upd-scope-info table {
        margin: 0;
        background: #fff;
        width: 100%;
        max-width: 620px;
    }
    .upd-scope-info table th,
    .upd-scope-info table td {
        padding: 6px 10px;
        vertical-align: middle;
    }
    tr.upd-row-disabled {
        opacity: 0.45;
    }

    /* ---------- Installment Management redesign ---------- */
    .im-card {
        border: 1px solid #e6eaf0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
    }
    .im-header {
        margin-bottom: 18px;
    }
    .im-header h3 {
        margin: 0 0 4px;
        font-weight: 700;
        color: #1f2a44;
    }
    .im-subtitle {
        margin: 0;
        color: #667085;
        font-size: 13.5px;
        max-width: 820px;
    }
    .im-scope {
        border: 1px solid #e6eaf0;
        border-radius: 10px;
        background: #f8fafc;
        padding: 14px 16px;
        margin-bottom: 18px;
    }
    .im-scope__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        gap: 10px;
        flex-wrap: wrap;
    }
    .im-scope__label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.6px;
        color: #8a94a6;
        text-transform: uppercase;
    }
    .im-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #eef0f4;
        color: #667085;
        border: 1px solid #e0e4ea;
    }
    .im-badge.is-set {
        background: #e7f4ec;
        color: #1a7f47;
        border-color: #bfe3cd;
    }
    .im-tabs {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #eceff3;
        margin-bottom: 0;
        padding: 0;
        list-style: none;
        flex-wrap: wrap;
    }
    .im-tab {
        display: block;
        padding: 12px 18px;
        border: none;
        background: transparent;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        color: #667085;
        font-weight: 600;
        border-radius: 6px 6px 0 0;
        text-align: left;
    }
    .im-tab:hover {
        color: #2f3b5c;
        background: #f5f7fa;
    }
    .im-tab small {
        display: block;
        font-weight: 400;
        font-size: 11.5px;
        color: #98a2b3;
    }
    .im-tab.active {
        color: #2f43b3;
        border-bottom-color: #2f43b3;
    }
    .im-tab.active small {
        color: #7c88c9;
    }
    .im-pane {
        border: 1px solid #eceff3;
        border-top: none;
        border-radius: 0 0 10px 10px;
        padding: 18px;
        background: #fff;
    }
    .im-pane[hidden] {
        display: none;
    }
    .im-note {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
    }
    .im-note i {
        margin-top: 2px;
    }
    .im-note--info {
        background: #eef4ff;
        border: 1px solid #cddcff;
        color: #33488f;
    }
    .im-note--warn {
        background: #fff8ec;
        border: 1px solid #f3e0b5;
        color: #8a6516;
    }
    .im-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 12px;
    }
    .upd-scope-info tr.upd-scope-noinst td {
        background: #f0f7ff;
        color: #1e3a5f;
    }
    /* Action footer: button + status together (not a floating purple overlay) */
    .im-update-footer {
        margin-top: 18px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .im-update-footer__row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .im-update-footer__hint {
        font-size: 12px;
        color: #64748b;
        max-width: 420px;
        line-height: 1.4;
    }
    .im-update-footer #updateChallanDatesBtn {
        min-width: 240px;
        border: none;
        border-radius: 8px;
        background: #1e3a5f;
        color: #fff;
        font-weight: 600;
        padding: 10px 18px;
        box-shadow: 0 2px 6px rgba(30, 58, 95, 0.25);
    }
    .im-update-footer #updateChallanDatesBtn:hover {
        background: #274874;
        color: #fff;
    }
    .im-status-dock {
        margin-top: 16px;
        padding: 0;
        border: none;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        position: static;
        left: auto;
        bottom: auto;
        transform: none;
        width: 100%;
        z-index: auto;
    }
    .im-status-dock .alert {
        margin: 0 !important;
        width: 100%;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 13px;
        padding: 10px 14px;
    }
    .im-status-dock .alert + .alert {
        margin-top: 8px !important;
    }
    .im-status-dock #processing_message {
        background: #eef4fb !important;
        border-color: #c5d6ec !important;
        color: #1e3a5f !important;
    }
    .im-status-dock #error_message {
        background: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #991b1b !important;
    }
    .im-status-dock #success_message {
        background: #ecfdf5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
    }
    .im-status-dock.is-empty {
        display: none !important;
    }
    .im-records-head {
        margin: 26px 0 8px;
    }
    .im-records-head h5 {
        margin: 0;
        font-weight: 700;
        color: #1f2a44;
    }
    .im-records-head small {
        color: #98a2b3;
    }
    .im-empty {
        text-align: center;
        padding: 26px 10px;
        color: #98a2b3;
    }
    .im-empty i {
        font-size: 26px;
        display: block;
        margin-bottom: 8px;
        color: #cbd2dd;
    }
</style>
@section('content')

<div class="card im-card" style="margin:10px;">
    <div class="card-body">
        <form id="InstallmentForm" method="POST" action="">
            @csrf
                <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>

                <div class="im-header">
                    <h3>Installment Management</h3>
                    <p class="im-subtitle">Manage fee installment dates in two ways: correct dates on challans that already exist and haven't been paid, or set the default schedule that applies to challans generated going forward.</p>
                </div>

                {{-- Shared scope: applies to both tabs --}}
                <div class="im-scope">
                    <div class="im-scope__head">
                        <span class="im-scope__label">Academic Scope &middot; Session required &middot; School / Dept / Program optional</span>
                        <span id="scopeBadge" class="im-badge">No scope selected yet</span>
                    </div>
                    <div class="row filter-row">
                        <div class="form-group col-sm-3 col-6">
                            <label for="session_sel">Session</label>
                            <select name="session" id="session_sel" class="form-control">
                                <option value="">Select Session</option>
                                @foreach ($sessions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-3 col-6">
                            <label for="school_sel">School</label>
                            <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                                @php
                                    $allSchools = [];
                                    foreach ($schools as $value) {
                                        $allSchools[] = $value['school_id'];
                                    }
                                @endphp
                                <option value="">Select School</option>
                                <option value="[{{implode(',', $allSchools) }}]">All School</option>
                                @foreach ($schools as $row)
                                <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-3 col-6">
                            <label for="department">Department</label>
                            <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                            </select>
                        </div>
                        {{-- <div class="form-group col-sm-3 col-6">
                            <label for="course">Degree Level</label>
                            <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                            </select>
                        </div> --}}
                        <div class="form-group col-sm-3 col-6">
                            <label for="program">Program</label>
                            <select name="program" disabled id="program" class="form-control">
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Tabs: 1) Master installment  2) Update challans --}}
                <ul class="im-tabs" role="tablist">
                    <li>
                        <a href="javascript:void(0)" class="im-tab active" data-im-tab="master" role="tab">
                            Set master schedule
                            <small>Template for future creations</small>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="im-tab" data-im-tab="update" role="tab">
                            Update existing challans
                            <small>Fixes challans already generated</small>
                        </a>
                    </li>
                </ul>

                {{-- ============ TAB 2: Update existing unpaid challans ============ --}}
                <div class="im-pane" data-im-pane="update" hidden>
                    <div class="im-note im-note--warn">
                        <i class="fa fa-exclamation-circle"></i>
                        <div>
                            Fixes <strong>Due &amp; Valid</strong> dates on UNPAID challans that already exist (installments <strong>and</strong> Full Semester without installments), within the selected scope.
                            The master schedule is not changed here. If you extend an earlier installment beyond a later one's date, that later installment must also be selected and extended — otherwise the update is blocked.
                        </div>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <button type="button" id="updCheckScopeBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-search"></i> Check Installments in Scope
                        </button>
                    </div>
                    <div id="upd_scope_info" class="upd-scope-info" style="display:none;"></div>

                    <table class="table table-bordered upd-challan-table" style="margin-top: 12px;">
                        <thead>
                            <tr>
                                <th style="width:110px; text-align:center; white-space:nowrap;">
                                    <input type="checkbox" id="upd-select-all"> Select All
                                </th>
                                <th>Installment No</th>
                                <th>New Due Date</th>
                                <th>New Valid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-inst-row="0">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="0"></td>
                                <td>Full Semester (No Installment) <small class="text-muted upd-inst-count" data-inst="0"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="0" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="0" disabled></td>
                            </tr>
                            <tr data-inst-row="1">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="1"></td>
                                <td>1st Installment <small class="text-muted upd-inst-count" data-inst="1"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="1" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="1" disabled></td>
                            </tr>
                            <tr data-inst-row="2">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="2"></td>
                                <td>2nd Installment <small class="text-muted upd-inst-count" data-inst="2"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="2" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="2" disabled></td>
                            </tr>
                            <tr data-inst-row="3">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="3"></td>
                                <td>3rd Installment <small class="text-muted upd-inst-count" data-inst="3"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="3" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="3" disabled></td>
                            </tr>
                            <tr data-inst-row="4">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="4"></td>
                                <td>4th Installment <small class="text-muted upd-inst-count" data-inst="4"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="4" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="4" disabled></td>
                            </tr>
                            <tr data-inst-row="5">
                                <td style="text-align:center;"><input type="checkbox" class="upd-row-check" data-inst="5"></td>
                                <td>5th Installment <small class="text-muted upd-inst-count" data-inst="5"></small></td>
                                <td><input type="date" class="form-control upd-due" data-inst="5" disabled></td>
                                <td><input type="date" class="form-control upd-valid" data-inst="5" disabled></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="im-update-footer">
                        <div class="im-update-footer__row">
                            <div class="im-update-footer__hint">
                                Select the rows to update, set New Due / Valid dates, then preview.
                                Status messages appear in the bar below.
                            </div>
                            <button type="button" id="updateChallanDatesBtn" class="btn">
                                Preview &amp; Update Challans
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ============ TAB 1: Master schedule ============ --}}
                <div class="im-pane" data-im-pane="master">
                    <div class="im-note im-note--info">
                        <i class="fa fa-info-circle"></i>
                        <div>
                            This sets the <strong>default schedule only</strong>. It does not touch challans that have already been generated — use <strong>Update existing challans</strong> for those. New challans created after saving will follow these dates.
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Installment No</th>
                                <th>Due Date</th>
                                <th>Valid Date</th>
                                {{-- <th>Visible Date</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1st Installment</td>
                                <td>
                                    <input type="date" name="first_installment_due_date" id="first-dueDate" class="form-control">
                                </td>
                                <td>
                                    <input type="date" name="first_installment_valid_date" id="first-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>2nd Installment</td>
                                <td>
                                    <input type="date" name="second_installment_due_date" id="second-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="date" name="second_installment_valid_date" id="second-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>3rd Installment</td>
                                <td>
                                    <input type="date" name="third_installment_due_date" id="third-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="date" name="third_installment_valid_date" id="third-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>4th Installment</td>
                                <td>
                                    <input type="date" name="forth_installment_due_date" id="forth-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="date" name="forth_installment_valid_date" id="forth-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>5th Installment</td>
                                <td>
                                    <input type="date" name="fifth_installment_due_date" id="fifth-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="date" name="fifth_installment_valid_date" id="fifth-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="im-actions">
                        <button type="button" style="width: 170px;" onclick="LoadData()" id="filterButton" class="btn btn-outline-primary">Filter Master Records</button>
                        <button type="button" id="clear_dates" class="btn btn-secondary" onclick="refreshDatesFields()"><i class="fa fa-undo"></i> Clear Dates</button>
                        <button type="button" id="clear" class="btn btn-secondary" onclick="refreshFields()"><i class="fa fa-undo"></i> Clear All</button>
                        <button type="button" id="saveButton" class="btn btn-primary">Save Master Dates</button>
                    </div>

                    <div class="im-records-head">
                        <h5>Master schedule records</h5>
                        <small>Every saved schedule, by session, school, department and program.</small>
                    </div>

                    <div class="filteredData">
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                            <tr>
                                <th scope="col">Session</th>
                                <th scope="col">School</th>
                                <th scope="col">Department</th>
                                <th scope="col">Program</th>
                                <th scope="col">First Due Date</th>
                                <th scope="col">First Valid Date</th>
                                <th scope="col">Second Due Date</th>
                                <th scope="col">Second Valid Date</th>
                                <th scope="col">Third Due Date</th>
                                <th scope="col">Third Valid Date</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="im-status-dock is-empty" id="imStatusDock">
                    <div id="processing_message" class="alert" style="display: none;">Processing, please wait...</div>
                    <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                    <div id="success_message" class="alert alert-success" style="display: none;"></div>
                </div>

        </form>
    </div>
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
   $(document).ready(function() {
        // Load the data table
        LoadData();
        updateScopeBadge();
    });

// ---- Tab switching (Update existing challans / Set master schedule) ----
function switchImTab(tab) {
    $('.im-tab').removeClass('active');
    $('.im-tab[data-im-tab="' + tab + '"]').addClass('active');
    $('.im-pane').attr('hidden', true);
    $('.im-pane[data-im-pane="' + tab + '"]').removeAttr('hidden');

    // DataTable lives in the (initially hidden) master pane — fix column widths on show
    if (tab === 'master' && $.fn.dataTable && $.fn.dataTable.isDataTable('#data-table')) {
        try { $('#data-table').DataTable().columns.adjust(); } catch (e) {}
    }
}

$(document).on('click', '.im-tab', function () {
    switchImTab($(this).data('im-tab'));
});

// ---- Academic scope badge ----
function updateScopeBadge() {
    var $badge = $('#scopeBadge');
    if (!$badge.length) {
        return;
    }
    var sess = ($('#session_sel option:selected').text() || '').trim();
    var sessVal = $('#session_sel').val();

    if (!sessVal) {
        $badge.text('No scope selected yet').removeClass('is-set');
        return;
    }

    var parts = [sess];
    var schoolVal = $('#school_sel').val();
    if (schoolVal) { parts.push(($('#school_sel option:selected').text() || '').trim()); }
    var deptVal = $('#department').val();
    if (deptVal) { parts.push(($('#department option:selected').text() || '').trim()); }
    var progVal = $('#program').val();
    if (progVal) { parts.push(($('#program option:selected').text() || '').trim()); }

    $badge.text(parts.join(' \u2022 ')).addClass('is-set');
}

// Badge + scope-stats refresh are handled together in the Academic Scope change handler below.

document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("InstallmentForm");
        var formData = new FormData(form);
        formData.append('session', $('#session_sel').val());
        formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('program', $('#program').val());
        // formData.append('student_type', $('#student_type').val());
        formData.append('first_installment_due_date', $('#first-dueDate').val());
        formData.append('first_installment_valid_date', $('#first-validDate').val());
        formData.append('second_installment_due_date', $('#second-dueDate').val());
        formData.append('second_installment_valid_date', $('#second-validDate').val());
        formData.append('third_installment_due_date', $('#third-dueDate').val());
        formData.append('third_installment_valid_date', $('#third-validDate').val());
        formData.append('forth_installment_due_date', $('#forth-dueDate').val());
        formData.append('forth_installment_valid_date', $('#forth-validDate').val());
        formData.append('fifth_installment_due_date', $('#fifth-dueDate').val());
        formData.append('fifth_installment_valid_date', $('#fifth-validDate').val());

        console.log(formData);
        showImProcessing();
        fetch('add-fee-installment', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                    refreshFields();
                    updShowSuccess(data.msg);
                    LoadData();
                } else {
                    // console.log(data);
                    var message = "";
                    if ('msg' in data) {
                        message = data.msg;
                    } else if ('errors' in data) {
                        var errors = data.errors;
                        $.each(errors, function (i, obj) {
                            message += "<p>" + obj[0] + "</p>";
                        });
                    }
                    updShowError(message || 'Unable to save master installment dates.');
                }
            })
            .catch(error => {
            console.log('error: '+error)
            hideImProcessing();
            updShowError('There was an error saving master installment dates.');
            console.error('There was an error!', error);
        });
    // }
});

var UPD_INSTALLMENT_LABELS = {
    0: 'Full Semester (No Installment)',
    1: '1st Installment',
    2: '2nd Installment',
    3: '3rd Installment',
    4: '4th Installment',
    5: '5th Installment'
};
var UPD_TODAY = '{{ \Carbon\Carbon::now("Asia/Karachi")->toDateString() }}';

function updInstLabel(no) {
    return UPD_INSTALLMENT_LABELS[parseInt(no, 10)] || ('Installment ' + no);
}

function syncImStatusDock() {
    var $dock = $('#imStatusDock');
    if (!$dock.length) {
        return;
    }
    var hasVisible = $('#processing_message, #error_message, #success_message').filter(function () {
        return $(this).css('display') !== 'none' && $.trim($(this).text()) !== '';
    }).length > 0;
    // processing may have default text even when shown
    if ($('#processing_message').is(':visible')) {
        hasVisible = true;
    }
    $dock.toggleClass('is-empty', !hasVisible);
}

function showImProcessing() {
    $('#error_message, #success_message').stop(true, true).hide();
    $('#processing_message').stop(true, true).show();
    syncImStatusDock();
}

function hideImProcessing() {
    $('#processing_message').hide();
    syncImStatusDock();
}

function scrollToImStatusDock() {
    var $dock = $('#imStatusDock');
    if (!$dock.length || $dock.hasClass('is-empty')) {
        return;
    }
    var el = $dock[0];
    if (el.scrollIntoView) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        $('html, body').animate({ scrollTop: $dock.offset().top - 120 }, 300);
    }
}

function updShowError(html) {
    $('#success_message').stop(true, true).text('').hide();
    hideImProcessing();
    $('#error_message').html(html).stop(true, true).show();
    syncImStatusDock();
    scrollToImStatusDock();
    clearTimeout(window._imStatusTimer);
    window._imStatusTimer = setTimeout(function () {
        $('#error_message').fadeOut(400, syncImStatusDock);
    }, 8000);
}

function updShowSuccess(html) {
    $('#error_message').stop(true, true).text('').hide();
    hideImProcessing();
    $('#success_message').html(html).stop(true, true).show();
    syncImStatusDock();
    scrollToImStatusDock();
    clearTimeout(window._imStatusTimer);
    window._imStatusTimer = setTimeout(function () {
        $('#success_message').fadeOut(400, syncImStatusDock);
    }, 8000);
}

function updExtractMsg(xhr, fallback) {
    if (xhr && xhr.responseJSON) {
        if (xhr.responseJSON.errors) {
            var m = '';
            $.each(xhr.responseJSON.errors, function (i, obj) { m += obj[0] + ' '; });
            return m.trim();
        }
        if (xhr.responseJSON.msg || xhr.responseJSON.message) {
            return xhr.responseJSON.msg || xhr.responseJSON.message;
        }
    }
    return fallback;
}

function getUpdScopePayload() {
    return {
        session: $('#session_sel').val(),
        school: $('#school_sel').val(),
        department: $('#department').val(),
        program: $('#program').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
}

// New due dates from CHECKED update rows only (used by BE for installment-order checks).
// Unchecked rows are ignored so leftover/disabled values cannot trigger false errors.
function getUpdNeighborDueDates() {
    var out = {
        first_installment_due_date: '',
        second_installment_due_date: '',
        third_installment_due_date: '',
        forth_installment_due_date: '',
        fifth_installment_due_date: ''
    };
    var keyByInst = {
        1: 'first_installment_due_date',
        2: 'second_installment_due_date',
        3: 'third_installment_due_date',
        4: 'forth_installment_due_date',
        5: 'fifth_installment_due_date'
    };
    $('.upd-row-check:checked').each(function () {
        var inst = parseInt($(this).data('inst'), 10);
        if (!keyByInst[inst]) {
            return; // skip Full Semester (0)
        }
        out[keyByInst[inst]] = $('.upd-due[data-inst="' + inst + '"]').val() || '';
    });
    return out;
}

// Installment breakdown of the currently selected academic scope (null = not loaded yet)
var UPD_SCOPE_STATS = null;

function updScopeStat(instNo) {
    if (!UPD_SCOPE_STATS || !UPD_SCOPE_STATS.by_installment) {
        return null;
    }
    return UPD_SCOPE_STATS.by_installment[String(instNo)] || null;
}

function renderUpdScopeInfo(stats, errorText) {
    var $box = $('#upd_scope_info');

    if (errorText) {
        $box.html('<span class="text-danger">' + errorText + '</span>').show();
        return;
    }
    if (!stats) {
        $box.hide().html('');
        return;
    }

    var sessionLabel = stats.session_name
        || ($('#session_sel option:selected').text() || '').trim()
        || 'Selected Session';

    var noInst = Number(stats.no_installment_count || 0);
    var maxInst = Number(stats.max_installment || 0);

    if (maxInst <= 0 && noInst <= 0) {
        $box.html(
            '<div class="upd-scope-info__title">Session: ' + sessionLabel + '</div>'
            + '<span class="text-danger">No unpaid challans found for this session scope.</span>'
        ).show();
        return;
    }

    var byInst = stats.by_installment || {};
    var rowsHtml = '';

    function scopeDueText(s) {
        if (!s) {
            return '-';
        }
        var dueText = s.min_due || '-';
        if (s.max_due && s.max_due !== s.min_due) {
            dueText = (s.min_due || '-') + ' to ' + s.max_due;
        }
        return dueText;
    }

    // Full Semester (No Installment) — same columns as installment rows (count + current due)
    var noStat = byInst['0'] || null;
    if (noStat && Number(noStat.count || 0) > 0) {
        rowsHtml += '<tr class="upd-scope-noinst">'
            + '<td><strong>' + updInstLabel(0) + '</strong></td>'
            + '<td><strong>' + noStat.count + '</strong></td>'
            + '<td>' + scopeDueText(noStat) + '</td>'
            + '</tr>';
    }

    for (var i = 1; i <= Math.max(maxInst, 5); i++) {
        var s = byInst[String(i)] || null;
        if (!s) {
            continue;
        }
        rowsHtml += '<tr>'
            + '<td>' + updInstLabel(i) + '</td>'
            + '<td><strong>' + s.count + '</strong></td>'
            + '<td>' + scopeDueText(s) + '</td>'
            + '</tr>';
    }

    var html = '<div class="upd-scope-info__title">Session: ' + sessionLabel + '</div>';

    html += '<div style="margin-bottom:8px;">'
        + 'Maximum installments in this session: <strong>' + maxInst + '</strong>. '
        + 'Unpaid installment challans: <strong>' + (stats.total_unpaid || 0) + '</strong>. '
        + 'Full Semester (no installment): <strong>' + noInst + '</strong> unpaid.'
        + '</div>';

    if (rowsHtml) {
        html += '<table class="table table-sm table-bordered">'
            + '<thead><tr><th>Type</th><th>Unpaid Challans</th><th>Current Due Date</th></tr></thead>'
            + '<tbody>' + rowsHtml + '</tbody>'
            + '</table>';
    } else {
        html += '<div class="text-muted" style="margin-bottom:8px;">No unpaid challans found in this session scope.</div>';
    }

    $box.html(html).show();
}

function applyUpdScopeToRows() {
    $('.upd-row-check').each(function () {
        var inst = String($(this).data('inst'));
        var $row = $(this).closest('tr');
        var $count = $('.upd-inst-count[data-inst="' + inst + '"]');
        var stat = updScopeStat(inst);

        if (!UPD_SCOPE_STATS) {
            $(this).prop('disabled', false);
            $row.removeClass('upd-row-disabled');
            $count.text('');
            return;
        }

        if (stat) {
            $(this).prop('disabled', false);
            $row.removeClass('upd-row-disabled');
            var dueHint = '';
            if (stat.min_due) {
                dueHint = stat.max_due && stat.max_due !== stat.min_due
                    ? ' · due ' + stat.min_due + ' to ' + stat.max_due
                    : ' · due ' + stat.min_due;
            }
            $count.text('(' + stat.count + ' unpaid' + dueHint + ')');
        } else {
            $(this).prop('checked', false).prop('disabled', true);
            $('.upd-due[data-inst="' + inst + '"], .upd-valid[data-inst="' + inst + '"]')
                .val('').prop('disabled', true);
            $row.addClass('upd-row-disabled');
            $count.text('(not generated)');
        }
    });

    $('#upd-select-all').prop('checked', false);
}

function loadUpdScopeStats(showMessages) {
    var scope = getUpdScopePayload();
    // Session is enough to load unpaid installment breakdown.
    // School / Department / Program are optional filters (same as backend).
    if (!scope.session) {
        UPD_SCOPE_STATS = null;
        applyUpdScopeToRows();
        renderUpdScopeInfo(null);
        if (showMessages) {
            updShowError('Please select Session in Academic Scope first.');
        }
        return $.Deferred().reject().promise();
    }

    return $.ajax({
        url: 'unpaid-installment-scope-stats',
        type: 'POST',
        data: scope
    }).done(function (res) {
        if (res && res.success) {
            UPD_SCOPE_STATS = res;
            applyUpdScopeToRows();
            renderUpdScopeInfo(res);
            if (showMessages) {
                $('#error_message').text('').hide();
                syncImStatusDock();
            }
        } else {
            UPD_SCOPE_STATS = null;
            applyUpdScopeToRows();
            renderUpdScopeInfo(null, (res && res.msg) || 'Unable to read installment breakdown for this scope.');
        }
    }).fail(function (xhr) {
        UPD_SCOPE_STATS = null;
        applyUpdScopeToRows();
        renderUpdScopeInfo(null, updExtractMsg(xhr, 'Unable to read installment breakdown for this scope.'));
    });
}

// Scope change: refresh badge + auto-load breakdown when Session is set (no error flash)
$(document).on('change', '#session_sel, #school_sel, #department, #program', function () {
    UPD_SCOPE_STATS = null;
    applyUpdScopeToRows();
    renderUpdScopeInfo(null);
    updateScopeBadge();
    if ($('#session_sel').val()) {
        loadUpdScopeStats(false);
    }
});

$('#updCheckScopeBtn').on('click', function () {
    loadUpdScopeStats(true);
});

function getSelectedInstallmentRows() {
    var rows = [];
    $('.upd-row-check:checked').each(function () {
        var inst = $(this).data('inst');
        rows.push({
            installment_no: String(inst),
            due_date: $('.upd-due[data-inst="' + inst + '"]').val(),
            valid_date: $('.upd-valid[data-inst="' + inst + '"]').val()
        });
    });
    return rows;
}

function validateSelectedInstallments(scope, selected) {
    if (!scope.session) {
        return 'Please select Session in Academic Scope first.';
    }
    if (!selected.length) {
        return 'Please tick at least one installment to update.';
    }
    for (var i = 0; i < selected.length; i++) {
        var r = selected[i];
        if (!r.due_date || !r.valid_date) {
            return updInstLabel(r.installment_no) + ': please set both New Due Date and New Valid Date.';
        }
        if (r.due_date < UPD_TODAY) {
            return updInstLabel(r.installment_no)
                + ': New Due Date cannot be earlier than today (' + UPD_TODAY + ').';
        }
        if (r.valid_date < UPD_TODAY) {
            return updInstLabel(r.installment_no)
                + ': New Valid Date cannot be earlier than today (' + UPD_TODAY + ').';
        }
        if (r.valid_date < r.due_date) {
            return updInstLabel(r.installment_no) + ': Valid Date cannot be earlier than Due Date.';
        }
        // Do not pull due dates backward vs current dues in this scope
        var st = updScopeStat(r.installment_no);
        if (st && st.max_due && r.due_date < st.max_due) {
            return updInstLabel(r.installment_no)
                + ': New Due Date cannot be earlier than current due date(s) in scope (current up to '
                + st.max_due + ').';
        }
    }
    // Due dates must be non-decreasing across SELECTED installments only (1st <= 2nd <= ...).
    // Full Semester (0) is excluded. Unchecked installments are not part of this check.
    var selectedDues = {};
    selected.forEach(function (r) {
        var n = parseInt(r.installment_no, 10);
        if (n >= 1 && n <= 5 && r.due_date) {
            selectedDues[n] = r.due_date;
        }
    });
    var lastVal = null, lastIdx = 0;
    for (var k = 1; k <= 5; k++) {
        if (selectedDues[k]) {
            if (lastVal && selectedDues[k] < lastVal) {
                return 'Installment ' + k + ' due date cannot be earlier than installment ' + lastIdx + '.';
            }
            lastVal = selectedDues[k];
            lastIdx = k;
        }
    }

    if (UPD_SCOPE_STATS) {
        // Selected installment must actually exist in this scope
        for (var s = 0; s < selected.length; s++) {
            if (!updScopeStat(selected[s].installment_no)) {
                return updInstLabel(selected[s].installment_no)
                    + ' is not generated in this session. Maximum installments here: '
                    + UPD_SCOPE_STATS.max_installment + '.';
            }
        }

        // Extending an earlier installment past a later one's current due → later must also be selected & extended
        var selectedMap = {};
        selected.forEach(function (r) { selectedMap[String(r.installment_no)] = r; });

        var mustInclude = [];
        for (var later = 2; later <= 5; later++) {
            var laterStat = updScopeStat(later);
            if (!laterStat) {
                continue;
            }
            for (var earlier = 1; earlier < later; earlier++) {
                var picked = selectedMap[String(earlier)];
                if (!picked) {
                    continue;
                }
                // New earlier due is after this later installment's current due → later must move too
                if (laterStat.min_due && picked.due_date > laterStat.min_due) {
                    var laterRow = selectedMap[String(later)];
                    if (!laterRow) {
                        mustInclude.push(later);
                    } else if (laterRow.due_date < picked.due_date) {
                        return updInstLabel(later) + ' due date must be on or after '
                            + updInstLabel(earlier) + ' new due date (' + picked.due_date + ').';
                    }
                    break;
                }
            }
        }

        // unique mustInclude
        mustInclude = mustInclude.filter(function (v, i, a) { return a.indexOf(v) === i; });

        if (mustInclude.length) {
            return 'Update blocked. You are extending '
                + Object.keys(selectedMap).map(function (n) { return updInstLabel(n); }).join(', ')
                + ' beyond existing later installment date(s). Also select and extend: '
                + mustInclude.map(function (n) { return updInstLabel(n); }).join(', ')
                + '. Otherwise no challans will be updated.';
        }
    }

    return '';
}

async function runUpdateChallanFlow() {
    var $btn = $('#updateChallanDatesBtn');
    if ($btn.data('busy')) {
        return;
    }

    var scope = getUpdScopePayload();
    var selected = getSelectedInstallmentRows();

    function setBusy(on) {
        $btn.data('busy', !!on).prop('disabled', !!on);
        if (on) {
            $btn.data('orig-label', $btn.text());
            $btn.text('Working…');
        } else {
            $btn.text($btn.data('orig-label') || 'Preview & Update Challans');
        }
    }

    function isSchoolScoped(s) {
        if (!s.school) {
            return false;
        }
        try {
            if (Array.isArray(JSON.parse(s.school))) {
                return false;
            }
        } catch (e) {}
        return true;
    }

    // Later installments first (5→1), then Full Semester (0). Helps coordinated extends.
    selected.sort(function (a, b) {
        var na = parseInt(a.installment_no, 10);
        var nb = parseInt(b.installment_no, 10);
        if (na === 0 && nb !== 0) {
            return 1;
        }
        if (nb === 0 && na !== 0) {
            return -1;
        }
        return nb - na;
    });

    setBusy(true);

    // Breakdown is needed to enforce "later installments must move too"
    if (!UPD_SCOPE_STATS && scope.session) {
        showImProcessing();
        try {
            await loadUpdScopeStats(false);
        } catch (e) {
            // validation below still runs without the breakdown
        }
        hideImProcessing();
    }

    var clientError = validateSelectedInstallments(scope, selected);
    if (clientError) {
        setBusy(false);
        updShowError(clientError);
        return;
    }

    var confirmSessionWide = '0';
    if (!isSchoolScoped(scope)) {
        var okWide = window.confirm(
            'WARNING: School is not selected.\n\n'
            + 'This will update unpaid challans across the ENTIRE session'
            + (scope.session ? '' : '')
            + ' (all schools matching Session filters).\n\n'
            + 'Prefer selecting a School/Department/Program for safer updates.\n\n'
            + 'Continue with session-wide update?'
        );
        if (!okWide) {
            setBusy(false);
            return;
        }
        confirmSessionWide = '1';
    }

    var neighbors = getUpdNeighborDueDates();
    var commonPayload = $.extend({}, scope, neighbors, { confirm_session_wide: confirmSessionWide });

    // ---- Preview (per selected installment) ----
    showImProcessing();
    var previews = [];
    try {
        for (var i = 0; i < selected.length; i++) {
            var p = await $.ajax({
                url: 'preview-update-unpaid-installment-challan-dates',
                type: 'POST',
                data: $.extend({}, commonPayload, selected[i])
            });
            previews.push({ row: selected[i], res: p });
        }
    } catch (xhr) {
        hideImProcessing();
        setBusy(false);
        updShowError(updExtractMsg(xhr, 'There was an error previewing challan dates.'));
        return;
    }
    hideImProcessing();

    var failed = previews.filter(function (x) { return !x.res || !x.res.success; });
    if (failed.length) {
        setBusy(false);
        updShowError(failed.map(function (f) {
            return updInstLabel(f.row.installment_no) + ': ' + ((f.res && f.res.msg) || 'validation failed');
        }).join('<br>'));
        return;
    }

    var totalEligible = previews.reduce(function (s, x) { return s + Number(x.res.eligible_count || 0); }, 0);
    var totalSkipped = previews.reduce(function (s, x) { return s + Number(x.res.skipped_order_count || 0); }, 0);

    if (totalEligible === 0) {
        setBusy(false);
        updShowError('No eligible unpaid installment challans found for this academic scope.');
        return;
    }

    var lines = previews.map(function (x) {
        return '  \u2022 ' + updInstLabel(x.row.installment_no) + ' \u2192 ' + x.row.due_date + ' / ' + x.row.valid_date + '  (' + x.res.eligible_count + ' challan)';
    }).join('\n');

    var confirmMsg = 'Academic Scope\n'
        + 'Session: ' + $('#session_sel option:selected').text().trim() + '\n'
        + 'School: ' + $('#school_sel option:selected').text().trim() + '\n'
        + 'Department: ' + $('#department option:selected').text().trim() + '\n'
        + 'Program: ' + $('#program option:selected').text().trim() + '\n\n'
        + (confirmSessionWide === '1' ? 'SCOPE: ENTIRE SESSION (no school filter)\n\n' : '')
        + 'Installments to update:\n' + lines
        + '\n\nTotal: ' + totalEligible + ' unpaid challan(s). Proceed?'
        + (totalSkipped > 0 ? '\n\nNote: ' + totalSkipped + ' matched challan(s) will be skipped due to installment date order.' : '');

    if (!window.confirm(confirmMsg)) {
        setBusy(false);
        return;
    }

    // ---- Update (per selected installment, later-first) ----
    showImProcessing();
    var totalUpdated = 0, totalSkippedUpd = 0, errs = [];
    for (var j = 0; j < selected.length; j++) {
        try {
            var u = await $.ajax({
                url: 'update-unpaid-installment-challan-dates',
                type: 'POST',
                data: $.extend({}, commonPayload, selected[j])
            });
            if (u && u.success) {
                totalUpdated += Number(u.updated_count || 0);
                totalSkippedUpd += Number(u.skipped_order_count || 0);
            } else {
                errs.push(updInstLabel(selected[j].installment_no) + ': ' + ((u && u.msg) || 'failed'));
            }
        } catch (xhr) {
            errs.push(updInstLabel(selected[j].installment_no) + ': ' + updExtractMsg(xhr, 'request failed'));
        }
    }
    hideImProcessing();
    setBusy(false);

    if (errs.length) {
        updShowError('Updated ' + totalUpdated + ' challan(s). Some installments failed:<br>' + errs.join('<br>'));
    } else {
        updShowSuccess(totalUpdated + ' unpaid installment challan(s) updated successfully'
            + (totalSkippedUpd > 0 ? ' (' + totalSkippedUpd + ' skipped due to installment date order).' : '.'));
    }

    // Refresh breakdown so Current Due / unpaid counts stay accurate
    if (scope.session) {
        try {
            await loadUpdScopeStats(false);
        } catch (e) {}
    }
}

$('#updateChallanDatesBtn').on('click', function () {
    runUpdateChallanFlow();
});

// Browser guard: past dates cannot be picked. Valid Date must be on/after Due Date.
$('.upd-due, .upd-valid').attr('min', UPD_TODAY);

$(document).on('change', '.upd-due', function () {
    var inst = $(this).data('inst');
    var dueDate = $(this).val();
    var validMin = dueDate && dueDate > UPD_TODAY ? dueDate : UPD_TODAY;
    var $valid = $('.upd-valid[data-inst="' + inst + '"]');

    $valid.attr('min', validMin);
    if ($valid.val() && $valid.val() < validMin) {
        $valid.val('');
    }
});

// Enable/disable a row's date inputs based on its checkbox
$(document).on('change', '.upd-row-check', function () {
    var inst = $(this).data('inst');
    var on = $(this).is(':checked');
    var $due = $('.upd-due[data-inst="' + inst + '"]');
    var $valid = $('.upd-valid[data-inst="' + inst + '"]');
    $due.prop('disabled', !on);
    $valid.prop('disabled', !on);
    if (!on) {
        $due.val('');
        $valid.val('');
    }
    // keep header "select all" in sync (ignore installments not present in scope)
    var $selectable = $('.upd-row-check').not(':disabled');
    $('#upd-select-all').prop('checked', $selectable.length > 0 && $selectable.filter(':checked').length === $selectable.length);
});

$(document).on('change', '#upd-select-all', function () {
    var on = $(this).is(':checked');
    $('.upd-row-check').not(':disabled').each(function () {
        if ($(this).is(':checked') !== on) {
            $(this).prop('checked', on).trigger('change');
        }
    });
});

$(document).on('change', '#first-dueDate', function() {
    console.log($(this).val());
    $('#second-dueDate').attr('min',$(this).val());
    $('#second-validDate').attr('min',$(this).val());
    $('#third-dueDate').attr('min',$(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#first-validDate').attr('min',$(this).val());
    $('#first-validDate').prop('disabled', false);
});
$(document).on('change', '#second-dueDate', function() {
    console.log($(this).val());
    $('#third-dueDate').attr('min',$(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#second-validDate').attr('min',$(this).val());
    $('#second-validDate').prop('disabled', false);
});
$(document).on('change', '#third-dueDate', function() {
    console.log($(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#third-validDate').attr('min',$(this).val());
    $('#third-validDate').prop('disabled', false);
});
$(document).on('change', '#forth-dueDate', function() {
    console.log($(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#forth-validDate').attr('min',$(this).val());
    $('#forth-validDate').prop('disabled', false);
});
$(document).on('change', '#fifth-dueDate', function() {
    console.log($(this).val());

    $('#fifth-validDate').attr('min',$(this).val());
    $('#fifth-validDate').prop('disabled', false);
});

function getRequest(endPoint,successBlock,failBlock){
    $.ajax({
        url: "{{url('/')}}"+endPoint,
        type: "GET",
        dataType: "json",
        success:successBlock
    }).fail(failBlock);
}
    function getPrograms() {
        const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
        const dept_id = $('#department').val();
        // console.log(session_id, dept_id);
        const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
        getRequest(url,function(res){
                    if(res.status){
                        console.log(res.data);
                        let allProg = [];
                        let degreeLevels = [];
                        $.each(res.data, function(key, value) {
                            allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                            degreeLevels.push(value.prog_degree_level);
                        });
                        populateDegreeLevel(degreeLevels);
                        if(dept_id == ''){
                            let html = `<option value=''>Select Program</option>`;
                            $.each(res.data, function(key, value) {
                            // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                            html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                            });
                            $('select[name="program"]').html(html); // Update "program" select
                        }else{
                            let html = `<option value=''>Select Program</option>`;
                            html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                            $.each(res.data, function(key, value) {
                            // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                            html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                            });
                            $('select[name="program"]').html(html); // Update "program" select
                        }

                        ////////
                        action_onAllDepartmentSelection(dept_id);
                        ///////

                    } else {
                        console.error('Error fetching programs:', res.message); // Log error message
                        // Optionally display an error message to the user
                    }

                },function(res){
                    alert('Some Error found!');
        });
        console.log(url);
    }
    function getDepartments(){
        var school_id = $('#school_sel').val();
        getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
            console.log(res);
            if(res.status){
                let allDep = [];
                $.each(res.data, function(key, value) {
                    allDep.push(value.dept_school_id+'~~'+value.dept_id);
                });
                if(school_id == ''){
                    let html = `<option value=''>Select Department</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
                }else{
                    let html = `<option value=''>Select Department</option>`;
                    html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
                }

                /////////
                let htmlProg = `<option value=''>Select Program</option>`;
                $('select[name="program"]').html(htmlProg);
                htmlProg = `<option value=''>Select Degree Level</option>`;
                $('select[name="degree_level"]').html(htmlProg);
                action_onAllSchoolSelection(school_id);
                ////
            }
        },function(res){
            alert('Some Error found!');
        });
    }


</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

<script>

function LoadData(){
    let filterParams = getFilterParams();
    console.log(filterParams);
    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false, //download all data
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        order: [1, 'desc'],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: "/api/get-installment-dates",
            data: function(d) {
                    return $.extend({}, d, filterParams);
                }
        },
        columns: [
            {data: "sess_name"},
            {data: "school_name", width: "120px"},
            {data: "dept_name"},
            {data: "prog_name"},
            {
                data: "fci_first_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part

                },
                 width: "90px"

            },
            {
                data: "fci_first_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_second_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_second_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_third_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_third_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            }


        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    console.log(row);
                    // var button = '<div>' +
                    //                 '<button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["fci_id"]+'" onclick="editRow(`'+row["fci_id"]+'`,`'+row["fci_session_id"]+'`,`'+row["fci_school_id"]+'`,`'+row["fci_dept_id"]+'`,`'+row["fci_prog_id"]+'`,`'+row["fci_first_duedate"]+'`,`'+row["fci_first_validdate"]+'`,`'+row["fci_first_visibledate"]+'`,`'+row["fci_second_duedate"]+'`,`'+row["fci_second_validdate"]+'`,`'+row["fci_second_visibledate"]+'`,`'+row["fci_third_duedate"]+'`,`'+row["fci_third_validdate"]+'`,`'+row["fci_third_visibledate"]+'`)">' +
                    //                     '<i class="fa fa-edit"></i> Edit' +
                    //                 '</button>' +
                    //                 '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                    //                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["fci_id"]+'])">' +
                    //                     '<i class="fa fa-trash"></i> Delete' +
                    //                 '</button>' +
                    //             '</div>';
                    var button = '<div>' +

                                    '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["fci_id"]+'])">' +
                                        '<i class="fa fa-trash"></i> Delete' +
                                    '</button>' +
                                '</div>';
                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 10,
            },

        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Installments Dates',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Installments Dates',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'print',
                    title: 'Installments Dates',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },
            ]
    });
}

function action_onAllSchoolSelection(school_id){
    if(school_id != ''){
        school_ids_array = JSON.parse(school_id);
        if(Array.isArray(school_ids_array)){
            $('#department').prop('disabled', true);
            $('#program').prop('disabled', true);
            setTimeout(function() {
                $('#department').val($('#department option:nth-child(2)').val());
                getPrograms();
                setTimeout(function() {
                    $('#program').val($('#program option:nth-child(2)').val());
                }, 500);  // Adjust the delay as needed
            }, 500);  // Adjust the delay as needed
        }else{
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
        }
    }else{
        $('#department').prop('disabled', false);
        $('#program').prop('disabled', false);
    }

}
function action_onAllDepartmentSelection(dept_id){
    if(dept_id != ''){
        dept_ids_array = JSON.parse(dept_id);
        if(Array.isArray(dept_ids_array)){
            $('#program').prop('disabled', true);
            $('#program').val($('#program option:nth-child(2)').val());
        }else{
            $('#program').prop('disabled', false);
        }
    }else{
        $('#program').prop('disabled', false);
    }
    console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    console.log(distinctDegreeLevels);
    html='<option value="" >Select Degree Level</option>';
    $.each(distinctDegreeLevels,function( key, value ) {
                html += '<option value="'+value+'" >'+value+'</option>';
            });
    $('select[name="degree_level"]').html(html);
}
function setProgramsAccordingly(){
    let session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    let dept_id = $('#department').val();
    let degreeLevel = $('#degree_level').val();
    let degreeLevelwithArrow = ((degreeLevel != '')? '&#x2192;'+degreeLevel: '');
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&degreeLevel=${degreeLevel}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    console.log(res.data);
                    let allProg = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                    });
                    // let html = `<option value=''>Select Program</option>`;
                    html = `<option value='${JSON.stringify(allProg)}'>All Program `+degreeLevelwithArrow+`</option>`;
                    $.each(res.data, function(key, value) {
                    // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                    html += `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "course" select

                    $('#program option').filter(function() {
                        return $(this).text() === 'All Program';
                    }).prop('selected', true);
                    ////////
                    // action_onAllDepartmentSelection(dept_id);
                    ///////
                    console.log($('#program').val());

                } else {
                    console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
// function editRow(fci_id, fci_session_id, fci_school_id, fci_dept_id, fci_prog_id, fci_first_duedate, fci_first_validdate, fci_first_visibledate, fci_second_duedate, fci_second_validdate, fci_second_visibledate, fci_third_duedate, fci_third_validdate, fci_third_visibledate){

//     console.log('fci_id -> ' + fci_id);
//     console.log('fci_session_id -> ' + fci_session_id);
//     console.log('fci_school_id -> ' + fci_school_id);
//     console.log('fci_dept_id -> ' + fci_dept_id);
//     console.log('fci_prog_id -> ' + fci_prog_id);
//     console.log('fci_first_duedate -> ' + fci_first_duedate);
//     console.log('fci_first_validdate -> ' + fci_first_validdate);
//     console.log('fci_first_visibledate -> ' + fci_first_visibledate);
//     console.log('fci_second_duedate -> ' + fci_second_duedate);
//     console.log('fci_second_validdate -> ' + fci_second_validdate);
//     console.log('fci_second_visibledate -> ' + fci_second_visibledate);
//     console.log('fci_third_duedate -> ' + fci_third_duedate);
//     console.log('fci_third_validdate -> ' + fci_third_validdate);
//     console.log('fci_third_visibledate -> ' + fci_third_visibledate);

//     $('#main-Id').val(fci_id);
//     $('#session_sel').val(fci_session_id);
//     $('#school_sel').val(fci_school_id);
//     getDepartments();//this will set department dropdown menu
//     setTimeout(function() {
//         $('#department').val(fci_dept_id);
//         getPrograms();//this will set programs dropdown menu and degreelevel dropdown menu
//         setTimeout(function() {
//             // setProgramsAccordingly();
//             setTimeout(function() {
//                 $('#program').val(fci_school_id+'~~'+fci_dept_id+'~~'+fci_prog_id);
//             }, 500);  // Adjust the delay as needed
//         }, 500);  // Adjust the delay as needed
//     }, 500);  // Adjust the delay as needed

//     // Set installment dates
//     $('#first-dueDate').val(fci_first_duedate.split(' ')[0]); // Set first due date
//     $('#first-validDate').val(fci_first_validdate.split(' ')[0]); // Set first valid date
//     $('#first-visibleDate').val(fci_first_visibledate.split(' ')[0]); // Set first visible date

//     $('#second-dueDate').val(fci_second_duedate.split(' ')[0]); // Set second due date
//     $('#second-validDate').val(fci_second_validdate.split(' ')[0]); // Set second valid date
//     $('#second-visibleDate').val(fci_second_visibledate.split(' ')[0]); // Set second visible date

//     $('#third-dueDate').val(fci_third_duedate.split(' ')[0]); // Set third due date
//     $('#third-validDate').val(fci_third_validdate.split(' ')[0]); // Set third valid date
//     $('#third-visibleDate').val(fci_third_visibledate.split(' ')[0]); // Set third visible date

//     // Enable fields if necessary
//     $('#first-validDate').prop('disabled', false);
//     $('#second-validDate').prop('disabled', false);
//     $('#third-validDate').prop('disabled', false);

//     // Scroll to the form (optional)
//     $('html, body').animate({scrollTop: 0}, 'slow');
//     // $('html, body').animate({ scrollTop: $('#InstallmentForm').offset().top }, 'slow');

// }
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/delete-installment-dates",
            data: {
                mainId: mainId
            },
            success: function(response) {
                // $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadData();
            },
            error: function(error) {
                // Handle the error
                // console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields(){
    $('#session_sel').val('');
    $('#school_sel').val('');
    $('#department').val('');
    $('#program').val('');
    // $('#student_type').val('');

    $('#upd-select-all').prop('checked', false);
    $('.upd-row-check').prop('checked', false).prop('disabled', false);
    $('.upd-due, .upd-valid').val('').prop('disabled', true);
    $('.upd-inst-count').text('');
    $('tr[data-inst-row]').removeClass('upd-row-disabled');
    UPD_SCOPE_STATS = null;
    renderUpdScopeInfo(null);

    $('#first-dueDate').val('');
    $('#first-validDate').val('');
    // $('#first-visibleDate').val('');

    $('#second-dueDate').val('');
    $('#second-validDate').val('');
    // $('#second-visibleDate').val('');

    $('#third-dueDate').val('');
    $('#third-validDate').val('');
    // $('#third-visibleDate').val('');

    $('#forth-dueDate').val('');
    $('#forth-validDate').val('');

    $('#fifth-dueDate').val('');
    $('#fifth-validDate').val('');
}
function refreshDatesFields(){

$('#first-dueDate').val('');
$('#first-validDate').val('');
// $('#first-visibleDate').val('');

$('#second-dueDate').val('');
$('#second-validDate').val('');
// $('#second-visibleDate').val('');

$('#third-dueDate').val('');
$('#third-validDate').val('');
// $('#third-visibleDate').val('');

$('#forth-dueDate').val('');
$('#forth-validDate').val('');

$('#fifth-dueDate').val('');
$('#fifth-validDate').val('');
}
function getFilterParams() {
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const department = $('#department').val();
    const program = $('#program').val();
    // const student_type = $('#student_type').val();
    const first_dueDate = $('#first-dueDate').val();
    const first_validDate = $('#first-validDate').val();
    // const first_visibleDate = $('#first-visibleDate').val();
    const second_dueDate = $('#second-dueDate').val();
    const second_validDate = $('#second-validDate').val();
    // const second_visibleDate = $('#second-visibleDate').val();
    const third_dueDate = $('#third-dueDate').val();
    const third_validDate = $('#third-validDate').val();
    // const third_visibleDate = $('#third-visibleDate').val();
    const forth_dueDate = $('#forth-dueDate').val();
    const forth_validDate = $('#forth-validDate').val();

    const fifth_dueDate = $('#fifth-dueDate').val();
    const fifth_validDate = $('#fifth-validDate').val();
    let filterParams =  {
        session_sel: session_sel,
        school_sel: school_sel,
        department: department,
        program: program,
        // student_type: student_type,
        first_dueDate: first_dueDate,
        first_validDate: first_validDate,
        second_dueDate: second_dueDate,
        second_validDate: second_validDate,
        third_dueDate: third_dueDate,
        third_validDate: third_validDate,
        forth_dueDate: forth_dueDate,
        forth_validDate: forth_validDate,
        fifth_dueDate: fifth_dueDate,
        fifth_validDate: fifth_validDate,
    };
    return filterParams;
}
</script>

@stop

