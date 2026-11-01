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
    .success-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .success-flag.show {
        right: 0px;
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
    .edit-btn, .delete-btn{
        width:76px;
    }

    /* ---- Report filters: visual-only polish; control IDs/behavior unchanged ---- */
    .challan-filter-card {
        border: 1px solid #e7edf5;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: visible;
    }
    #feeForm label {
        display: block;
        margin-bottom: 6px;
        color: #526174;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
    }
    #feeForm .fee-type-group label {
        color: #334155;
    }
    .challan-filter-card > .card-header {
        padding: 0 !important;
        border: 0;
        background: #ffffff;
    }
    .filter-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        margin-bottom: 4px;
        border-bottom: 1px solid #edf2f7;
        background: #f8fbff;
    }
    .filter-heading h3 {
        margin: 0;
        color: #334155;
        font-size: 18px;
        font-weight: 600;
    }
    .filter-heading small {
        color: #94a3b8;
        font-size: 12px;
    }
    #feeForm {
        padding-bottom: 4px;
    }
    #feeForm .filter-row,
    #feeForm > .row:not(.filter-actions) {
        margin-left: 0;
        margin-right: 0;
        padding-left: 10px;
        padding-right: 10px;
    }
    #feeForm .form-group,
    #feeForm .col-md-6,
    #feeForm .col-md-4 {
        margin-bottom: 14px;
    }
    #feeForm .form-control {
        min-height: 38px;
        border: 1px solid #dce5ef;
        border-radius: 7px;
        background-color: #ffffff;
        color: #334155;
        font-size: 13px;
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    #feeForm .form-control:focus {
        border-color: #93b4df;
        box-shadow: 0 0 0 3px rgba(147, 180, 223, 0.16);
    }
    #feeForm .form-control:disabled {
        background-color: #f7f9fc;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .filter-actions {
        display: flex !important;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end !important;
        gap: 10px !important;
        width: auto !important;
        margin: 4px 20px 16px !important;
        padding: 14px 16px;
        border: 1px solid #edf2f7;
        border-radius: 9px;
        background: #fafcff;
    }
    .filter-actions label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0 8px 0 0 !important;
        white-space: nowrap;
        color: #475569 !important;
        font-size: 12px !important;
        cursor: pointer;
    }
    .filter-actions input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #7699c9;
    }
    .filter-actions .btn {
        min-width: 100px;
        padding: 8px 16px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: none;
    }
    #filterButton,
    #summaryButton {
        border-color: #89a9d2;
        background: #89a9d2;
        color: #ffffff;
    }
    #filterButton:hover,
    #summaryButton:hover {
        border-color: #7397c5;
        background: #7397c5;
    }
    #clear {
        border-color: #d8e1eb !important;
        background: #f1f5f9 !important;
        color: #526174 !important;
    }
    #clear:hover {
        border-color: #cbd5e1 !important;
        background: #e8eef5 !important;
    }
    @media (max-width: 767px) {
        .filter-heading small { display: none; }
        .filter-actions {
            justify-content: flex-start !important;
            margin: 4px 12px 14px !important;
        }
    }

    /* ---- School Wise Summary: professional, easy to scan ---- */
    .summaryTable2 .card {
        border: 1px solid #e6eaf0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.06);
        overflow: hidden;
    }
    .summaryTable2 .card-body {
        padding: 0 !important;
    }
    .schwise-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px 8px;
        border-bottom: 1px solid #eef1f5;
        background: #fafbfc;
    }
    .schwise-toolbar h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: 0.01em;
    }
    .schwise-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 12px;
        color: #4b5563;
    }
    .schwise-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .schwise-legend i {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
    }
    .leg-paid { background: #8bcbb3; }
    .leg-unpaid { background: #e7c68b; }
    .leg-total { background: #9dbce1; }
    .leg-school { background: #aab7c7; }

    #summary-data-table2 {
        border-collapse: separate !important;
        border-spacing: 0;
        font-size: 13px;
        color: #1f2937;
    }
    #summary-data-table2 thead th {
        background: #eaf1f8 !important;
        color: #42556c !important;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        vertical-align: middle !important;
        text-align: center !important;
        padding: 12px 10px !important;
        border: none !important;
        border-bottom: 2px solid #cfdae7 !important;
        white-space: normal;
        line-height: 1.35;
    }
    #summary-data-table2 thead th:first-child {
        text-align: left !important;
        padding-left: 16px !important;
    }
    #summary-data-table2 thead th .th-sub {
        display: block;
        margin-top: 3px;
        font-size: 10px;
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
        color: #7f91a6;
    }
    #summary-data-table2 tbody td {
        padding: 10px 12px !important;
        border-top: 1px solid #edf0f4 !important;
        border-bottom: none !important;
        vertical-align: middle !important;
        background-clip: padding-box;
    }
    #summary-data-table2 tbody td:first-child {
        text-align: left !important;
        padding-left: 16px !important;
        font-weight: 500;
        color: #111827;
        max-width: 280px;
    }
    #summary-data-table2 tbody td.num-col {
        text-align: center !important;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    #summary-data-table2 .school-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 52px;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #cbd9e8;
        background: #eef4fa;
        color: #4f6781;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
    }
    #summary-data-table2 .metric {
        display: inline-block;
        min-width: 36px;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }
    #summary-data-table2 .metric-paid {
        color: #467a68;
        background: #edf8f3;
    }
    #summary-data-table2 .metric-unpaid {
        color: #8b6a31;
        background: #fff8ea;
    }
    #summary-data-table2 .metric-total {
        color: #5277a3;
        background: #eef5fc;
    }
    #summary-data-table2 .metric-school {
        color: #536273;
        background: #edf1f5;
        font-size: 14px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 6px;
    }
    #summary-data-table2 tfoot th {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 10px !important;
        border-top: 2px solid #cbd5e1 !important;
        text-align: center !important;
    }
    #summary-data-table2 tfoot th:first-child {
        text-align: left !important;
        padding-left: 16px !important;
    }

    /* Soft school banding (left accent + wash) — readable, not loud */
    #summary-data-table2 tbody tr.school-color-0 td { background-color: #fffafa !important; box-shadow: inset 2px 0 0 #e7bcbc; }
    #summary-data-table2 tbody tr.school-color-1 td { background-color: #f9fdfb !important; box-shadow: inset 2px 0 0 #b8d8c7; }
    #summary-data-table2 tbody tr.school-color-2 td { background-color: #f8fcfe !important; box-shadow: inset 2px 0 0 #b6d3e2; }
    #summary-data-table2 tbody tr.school-color-3 td { background-color: #fffdf8 !important; box-shadow: inset 2px 0 0 #e4d19d; }
    #summary-data-table2 tbody tr.school-color-4 td { background-color: #fbfcfd !important; box-shadow: inset 2px 0 0 #c7d0da; }
    #summary-data-table2 tbody tr.school-color-5 td { background-color: #fffcf9 !important; box-shadow: inset 2px 0 0 #e5c6ad; }
    #summary-data-table2 tbody tr.school-color-6 td { background-color: #fefafd !important; box-shadow: inset 2px 0 0 #dec0d2; }
    #summary-data-table2 tbody tr.school-color-7 td { background-color: #f9fdfb !important; box-shadow: inset 2px 0 0 #c1dcc9; }
    #summary-data-table2 tbody tr.school-color-8 td { background-color: #f8fbfe !important; box-shadow: inset 2px 0 0 #bdd0e5; }
    #summary-data-table2 tbody tr.school-color-9 td { background-color: #fffafa !important; box-shadow: inset 2px 0 0 #e5c1c1; }

    #summary-data-table2 tbody tr:hover td {
        filter: brightness(0.985);
    }

    .summaryTable2 .dataTables_wrapper {
        padding: 12px 16px 16px;
    }
    .summaryTable2 .dt-button {
        background-color: #eaf1f8 !important;
        color: #4b627c !important;
        border: 1px solid #d5e0eb !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        margin: 2px 4px 2px 0 !important;
        font-size: 12px !important;
    }
    .summaryTable2 .dt-button:hover {
        background-color: #dfeaf4 !important;
    }
    .summaryTable2 .dataTables_info,
    .summaryTable2 .dataTables_paginate {
        padding-top: 10px !important;
        font-size: 12px;
        color: #64748b;
    }
</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card challan-filter-card">
    <div class="card-header" style="width:100%;">
        <form id="feeForm" method="POST" action="">
            @csrf
                <div class="filter-heading">
                    <h3>Challan Report</h3>
                    <small>Select filters below to generate the required report</small>
                </div>

                <div class="row filter-row formDiv">
                    <div class="form-group col-sm-6">
                        <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                            {{-- <option value="">Select Session</option> --}}
                       @foreach ($sessions as $key => $session)
                            <option value="{{ $key }}">{{ $session}}</option>
                        @endforeach





                        </select>
                    </div>

                    <div class="form-group col-sm-6 schoolDiv">
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

                    <div class="form-group col-sm-6 deptDiv">
                        <label for="department">Department</label>
                        <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                        </select>
                    </div>
                    <div class="form-group col-sm-6 degDiv">
                        <label for="course">Degree Level</label>
                        <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                        </select>
                    </div>

                    <div class="form-group col-sm-3 progDiv">
                    <label for="program">Program</label>
                    <select name="program" disabled id="program" class="form-control">
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="studentTypeOption">Student Type</label>
                        <select name="student_type" id="studentTypeOption" class="form-control">
                            <option value="">Select type</option>
                            <option value="Existing">Existing</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                            <option value="New Admission FALL_withouReg">New Admission FALL (without registration)</option>
                            <option value="New Admission SPRING_withouReg">New Admission SPRING (without registration)</option>
                            <option value="New Admission SUMMER_withouReg">New Admission SUMMER (without registration)</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3 fee-type-group">
                        <label for="feeType">Fee Type</label>
                        <select name="fee_type" id="feeType" class="form-control">
                            <option value="">Select fee type</option>
                            <option value="Full_semester">Full Semester wise</option>
                            <option value="Processing_Fee">Processing Fee</option>
                            <option value="Credit_hours">Credit Hour wise</option>
                        </select>
                        <small id="feeTypeError" class="text-danger d-none">Please select a fee type.</small>

                    </div>
                    <div class="form-group col-sm-3 challanStatusDiv">
                        <label for="challanStatus">Challan Status</label>
                        <select name="challanStatus" id="challanStatus" class="form-control">
                            <option value="">Select Status</option>
                            <option value="PAID">Full Paid</option>
                            <option value="partialpaid">Partial Paid</option>
                            <option value="UNPAID">Full Unpaid</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="CANCEL FOR REISSUANCE">Cancel for Reissuance</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3 challanStatus_summary d-none">
                        <label for="challanStatus_summary">Challan Status</label>
                        <select name="challanStatus_summary" id="challanStatus_summary" class="form-control">
                            <option value="all">All (Paid + Unpaid)</option>
                            <option value="totalpaid">Full Paid / Partial Paid</option>
                            <option value="unpaid">Full Unpaid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6 d-none">
                        <label for="roadmap_session_sel">Road Map Session</label>
                        <select name="roadmap_session" id="roadmap_session_sel" class="form-control">
                            <option value="">Select Road Map Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-sm-6 d-none">
                        <label for="studentType">Effective From</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                    </div>
                <div class="form-group col-sm-6 d-none">
                        <label for="studentType">Effective To</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-2 otherThanHostelDivs d-none">
                        <label for="tutionAmt">Tuition Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="tution_amount" id="tutionAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 admissionFeeDiv d-none">
                        <label for="admissionAmt">Admission Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="admission_amount" id="admissionAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs securityAmtDiv d-none">
                        <label for="securityAmt">Security Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="security_amount" id="securityAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs d-none">
                        <label for="medicalAmt">Medical Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)"  name="medical_amount" id="medicalAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs d-none">
                        <label for="activitycharges">Activity Charges</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)"  name="activity_charges_amount" id="activitychargesAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 credFeeDiv d-none">
                        <label for="credFee">Per Credit Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="per_credit_fee" id="credFee" class="form-control">
                    </div>
                    <div class="form-group col-sm-2 d-none hostelFeeDiv d-none">
                        <label for="hostelFee">Hostel Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="hostel_amount" id="hostelAmt" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="fromDate" class="form-label">Challan Issued From</label>
                                <input type="date" disabled class="form-control" name="fromDate" id="fromDate">
                            </div>
                            <div class="col-md-6">
                                <label for="toDate" class="form-label">Challan Issued Till</label>
                                <input type="date" disabled  class="form-control" name="toDate" id="toDate">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="sch_round">Admission Round</label>
                                <select name="sch_round" disabled  id="sch_round" class="form-control">
                                    <option value="">Select type</option>
                                    <option value="early">Early</option>
                                    <option value="regular">Regular</option>
                                    <option value="rolling">Rolling</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- <div class="form-group col-sm-12" style="display: flex; justify-content: center; align-items: center;"> --}}
                    <div class="form-group filter-actions">


                        {{-- <div style="display: flex; align-items: center; gap: 20px;"> --}}
                            <label>
                                <input type="checkbox" id="countSummaryCheckbox" onchange="toggleButtons(this)"> Challan Summary
                            </label>
                            <label>
                                <input type="checkbox" id="countSummaryCheckboxSchwise" onchange="toggleButtons(this)"> Challan Summary School Wise
                            </label>

                            <button type="button" style="width: auto; display: none;" id="summaryButton" class="btn btn-primary">
                                Apply Filter
                            </button>

                            <button type="button" style="width: auto;" id="filterButton" class="btn btn-primary">
                                Apply Filter
                            </button>

                            <button type="button" style="width: 100px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">
                                Clear
                            </button>
                        {{-- </div> --}}
                    </div>
                {{-- </div> --}}
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>


    </div>
    <div class="row filterTable" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            <th scope="col">Challan No</th>
                            <th scope="col">Enrollment No</th>
                            <th scope="col">Apply Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Session</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Program</th>
                            <th scope="col">Student Type</th>
                            <th scope="col">Fee Type</th>
                            <th scope="col">Challan Status</th>
                            <th scope="col">Created at</th>
                            <th scope="col">Action</th>


                          </tr>
                        </thead>

                      </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row summaryTable d-none" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="summary-data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            {{-- <th scope="col">Session</th> --}}
                            <th scope="col">Session</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Program</th>
                            <th scope="col">Issued Students</th>
                            <th scope="col">Cancelled</th>
                            <th scope="col">Active</th>
                            <th scope="col">Full Paid</th>
                            <th scope="col">Partial Paid</th>
                            <th scope="col">Total Paid</th>
                            <th scope="col">Unpaid</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr style='background-color:#8e98db6b;'>
                                <th colspan="1" style="text-align:left">Total</th>
                                <th colspan="1" style="text-align:right"></th>
                                <th colspan="1" style="text-align:right"></th>
                                <th colspan="1" style="text-align:right"></th>
                                <th id="totalissued-footer"></th> <!-- Issued Students -->
                                <th id="totalcancelled-footer"></th> <!-- Cancelled -->
                                <th id="totalactive-footer"></th> <!-- Active -->
                                <th id="totalfullpaid-footer"></th> <!-- Full Paid -->
                                <th id="totalinstallment-footer"></th> <!-- Installment -->
                                <th id="totalpaid-footer"></th> <!-- Total Paid -->
                                <th id="totalunpaid-footer"></th> <!-- Unpaid -->
                            </tr>
                        </tfoot>
                      </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row summaryTable2 d-none" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="schwise-toolbar">
                    <h5>New Admissions Summary — School Wise</h5>
                    <div class="schwise-legend">
                        <span><i class="leg-paid"></i> Paid / Partial</span>
                        <span><i class="leg-unpaid"></i> Full Unpaid</span>
                        <span><i class="leg-total"></i> Program Total</span>
                        <span><i class="leg-school"></i> School Total</span>
                    </div>
                </div>
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="summary-data-table2" class="table display" style="width:100%">
                        <thead>
                          <tr id="summary2-head-row">
                            <th scope="col">Program</th>
                            <th scope="col">School</th>
                            <th scope="col">Paid / Partial</th>
                            <th scope="col">Full Unpaid</th>
                            <th scope="col">Program Total</th>
                            <th scope="col">School Total</th>
                          </tr>
                        </thead>
                        <tfoot>
                            <tr id="summary2-foot-row">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card" style="margin-top:10px;">
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
    var lastURL = '';
$(document).ready(function(){
    $('#studentTypeOption').on('change', function() {
        var isChecked = $('#countSummaryCheckbox').is(':checked');
        var isCheckedschwise = $('#countSummaryCheckboxSchwise').is(':checked');
        var studentType = $(this).val();

        if ((isChecked || isCheckedschwise) && studentType !== 'Existing') {
            $('#sch_round').prop('disabled', false).val('');
        } else {
            $('#sch_round').prop('disabled', true).val('');
        }
    });
    LoadFee();
    $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', true);
        }
    });
    $(document).on('click', '#bulk-Del', function() {
        var fee_ids = new Array();
        $.each($("input[name='fee_ids[]']:checked"), function () {
            fee_ids.push($(this).val());
        });
        if (fee_ids.length == 0) {
            alert("Please select atleast one row to delete.");
        } else {
            // console.log(fee_ids);
            deleteRows(fee_ids);
        }
    });
    $(document).on('click', '#filterButton', function() {
        LoadFee();
    });
    $(document).on('click', '#summaryButton', function() {
        var feeType = $('#feeType').val();

        if (!feeType) {
            $('#feeTypeError').removeClass('d-none'); // Show error
            $('#feeType').addClass('is-invalid');     // Optional: red border
            return;
        }

        // Clear error if value is selected
        $('#feeTypeError').addClass('d-none');
        $('#feeType').removeClass('is-invalid');

        var studentType = $('#studentTypeOption').val();

        if ($('#countSummaryCheckboxSchwise').is(':checked')) {
            LoadSummary2();
        }else if($('#countSummaryCheckbox').is(':checked')){
            //  if(studentType == 'Existing' && feeType == 'Full_semester'){
            //      LoadExistingSummary();
            //  }else{
                LoadSummary();
            //  }
        }


    });
});


</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script>
 function toggleButtons(checkbox) {
        // Uncheck the other box if this one is checked
        if (checkbox.checked) {
            if (checkbox.id === "countSummaryCheckbox") {
                document.getElementById("countSummaryCheckboxSchwise").checked = false;
            } else {
                document.getElementById("countSummaryCheckbox").checked = false;
            }
        }
        const summaryButton = document.getElementById("summaryButton");
        const filterButton = document.getElementById("filterButton");

        if (checkbox.checked) {
            // Show the Summary Button and hide the Apply Filter Button
            summaryButton.style.display = "block";
            filterButton.style.display = "none";
            $('#school_sel').val('');
            $('#department').val('');
            $('#degree_level').val('');
            $('#program').val('');
            $('#challanStatus').val('');
            $('#challanStatus_summary').val('all');
            if (checkbox.id === "countSummaryCheckboxSchwise") {
                $('.challanStatus_summary').removeClass('d-none');
                $('.challanStatusDiv').addClass('d-none');
            } else {
                $('.challanStatus_summary').addClass('d-none');
                $('.challanStatusDiv').removeClass('d-none');
            }

            // $('#school_sel').prop('disabled', true);
            // $('#department').prop('disabled', true);
            // $('#degree_level').prop('disabled', true);
            // $('#program').prop('disabled', true);
            $('#challanStatus').prop('disabled', true);
            $('#fromDate').prop('disabled', false);
            $('#toDate').prop('disabled', false);
            $('#sch_round').prop('disabled', false);
        } else {
            // Hide the Summary Button and show the Apply Filter Button
            summaryButton.style.display = "none";
            filterButton.style.display = "block";
            $('#school_sel').val('');
            $('#department').val('');
            $('#degree_level').val('');
            $('#program').val('');
            $('#challanStatus').val('');
            $('#challanStatus_summary').val('all');
            $('#fromDate').val('');
            $('#toDate').val('');
            $('#sch_round').val('');


            $('#school_sel').prop('disabled', false);
            $('#department').prop('disabled', false);
            $('#degree_level').prop('disabled', false);
            $('#program').prop('disabled', false);
            $('#challanStatus').prop('disabled', false);
            $('.challanStatus_summary').addClass('d-none');
            $('.challanStatusDiv').removeClass('d-none');
            $('#fromDate').prop('disabled', true);
            $('#toDate').prop('disabled', true);
            $('#sch_round').prop('disabled', true);
        }
    }

function LoadFee(){
    $('.summaryTable').addClass('d-none');
    $('.summaryTable2').addClass('d-none');
    $('.filterTable').removeClass('d-none');
    let filterParams = getFilterParams();
    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false, //to download all data once
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        lengthMenu: [ [10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000] ],
        order: [1, 'desc'],
        ajax:{
            dataType: 'json',
            method: 'post',
            url:  "/api/get-challan-report",
            data: function(d) {
                return $.extend({}, d, filterParams);
            }
        },
        columns: [
            {data: "id", name: "id"},
            {data: "sc_enrollment_no", name: "sc_enrollment_no",
                    render: function (data, type, row) {
                        return data ? data : 'NA';
                }
            },
            {data: "sc_app_id", name: "sc_app_id"},
            {data: "sc_studentname", name: "sc_studentname"},
            {data: "sess_name", name: "sess_name"},
            {data: "school_name", name: "school_name"},
            {data: "dept_name", name: "dept_name"},
            {data: "prog_name", name: "prog_name"},
            {data: "sc_student_status", name: "sc_student_status"},
            {data: "sc_challan_type", name: "sc_challan_type"},
            {data: "sc_challan_status", name: "sc_challan_status"},
            {data: "created_at", name: "created_at"},

        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    // console.log(row);

                    var button = '<button class="btn btn-primary btn-sm preview-btn" id="'+row["id"]+'" onclick="previewRow(`'+row["id"]+'`)"><i class="fa fa-eye" aria-hidden="true"></i> Preview</button>';

                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 12,
            },
            // {
            //     render: function (data, type, row) {
            //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

            //         return selectbtn;

            //     },
            //     // className: 'dt-body-center',
            //     // className: 'dt-head-center',
            //     targets: 14,
            //     searchable: false,
            //     orderable: false
            // },

            // {
            //     "width": "100px", "targets": 1
            // },
            // {
            //     "width": "5px", "targets": 14
            // },
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
        dom: 'Blfrtip', // Adds the buttons to the DataTable
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Challan Report',
                exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11]
                    }
            },
            {
                extend: 'pdfHtml5',
                title: 'Challan Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11]
                    }

            },
            // {
            //     text: 'Export All Data (PDF)', // Custom button text
            //     action: function(e, dt, button, config) {
            //         // Call your custom function
            //         pdfExportFunction(dt, config);
            //     }
            // },


            {
                extend: 'print',
                title: 'Challan Report',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11]
                    }
            }
        ]
    });
}
function LoadSummary(){
    $('.filterTable').addClass('d-none');
    $('.summaryTable2').addClass('d-none');
    $('.summaryTable').removeClass('d-none');
    let filterParams = getFilterParams();
    $('#summary-data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false, //to download all data once
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 100, // Set default page length to 100
        lengthMenu: [ [10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000] ],
        order: [1, 'desc'],
        ajax:{
            dataType: 'json',
            method: 'post',
            url:  "/api/get-challan-summary",
            data: function(d) {
                // This summary table renders per-program rows (dept_name/prog_name),
                // so it needs the program view level; otherwise the server defaults
                // to school level and those columns come back empty.
                return $.extend({}, d, filterParams, { summaryViewLevel: 'program' });
            },
        },
        columns: [
            {data: "sess_name", name: "sess_name"},
            {data: "school_name", name: "school_name"},
            {data: "dept_name", name: "dept_name"},
            {data: "prog_name", name: "prog_name"},
            {data: "total_students", name: "total_students"},
            {data: "cancelled", name: "cancelled"},
            {data: "active", name: "active"},
            {data: "fullpaid", name: "fullpaid"},
            {data: "installment", name: "installment"},
            {data: "totalpaid", name: "totalpaid"},
            {data: "unpaid", name: "unpaid"}

        ],
        footerCallback: function(row, data, start, end, display) {
            let api = this.api();

            // Helper function to get integer value of a column
            let intVal = function(i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ? i : 0;
            };

            // Sum all filtered rows (not just the current page)
            let totalIssuedStudents = api
                .column(4, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalCancelled = api
                .column(5, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalActive = api
                .column(6, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalFullPaid = api
                .column(7, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalInstallment = api
                .column(8, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalPaid = api
                .column(9, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalUnpaid = api
                .column(10, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            // Update the footer with the totals
            $(api.column(4).footer()).html('' + totalIssuedStudents.toLocaleString());
            $(api.column(5).footer()).html(' ' + totalCancelled.toLocaleString());
            $(api.column(6).footer()).html('' + totalActive.toLocaleString());
            $(api.column(7).footer()).html('' + totalFullPaid.toLocaleString());
            $(api.column(8).footer()).html('' + totalInstallment.toLocaleString());
            $(api.column(9).footer()).html('' + totalPaid.toLocaleString());
            $(api.column(10).footer()).html('' + totalUnpaid.toLocaleString());
        },
        autoWidth: false, // Ensures DataTable does not set its own width
        dom: 'Blfrtip', // Adds the buttons to the DataTable
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Challan Summary',
                footer: true,  // This ensures footer is included
                exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10],
                        modifier: {
                        page: 'all'
                    }
                },
                customize: function(xlsx) {
                    let sheet = xlsx.xl.worksheets['sheet1.xml'];
                    let totalPaid = $('#export-totalpaid').val();

                    // Add the Total Paid row at the bottom of the Excel file
                    // $('row:last', sheet).after(
                    //     '<row>' +
                    //     '<c t="inlineStr"><is><t>Total Paid</t></is></c>' +
                    //     '<c t="inlineStr"><is><t>' + totalPaid + '</t></is></c>' +
                    //     '</row>'
                    // );
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Challan Summary',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,  // This ensures footer is included
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10],
                    modifier: {
                        page: 'all'
                    }
                },
                // customize: function(doc) {
                //     let totalPaid = $('#export-totalpaid').val();

                //     // Add Total Paid at the bottom of the PDF
                //     doc.content.push({
                //         text: 'Total Paid: ' + totalPaid,
                //         alignment: 'right',
                //         margin: [0, 10, 40, 0],
                //         bold: true
                //     });
                // }

            },
            // {
            //     text: 'Export All Data (PDF)', // Custom button text
            //     action: function(e, dt, button, config) {
            //         // Call your custom function
            //         pdfExportFunction(dt, config);
            //     }
            // },


            {
                extend: 'print',
                title: 'Challan Summary',
                footer: true,
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10]
                    }
            }
        ]
    });
}
function LoadSummary2() {
    $('.filterTable').addClass('d-none');
    $('.summaryTable').addClass('d-none');
    $('.summaryTable2').removeClass('d-none');

    const chalanStatus = $('#challanStatus_summary').val() || 'all';
    const isAllStatus = (chalanStatus === 'all');
    let filterParams = getFilterParams();
    let schoolTotals = {};

    // Build header to match accounts PDF when All (Paid + Unpaid) is selected
    const $head = $('#summary2-head-row').empty();
    const $foot = $('#summary2-foot-row').empty();
    if (isAllStatus) {
        $head.append(
            '<th scope="col">Program</th>' +
            '<th scope="col">School</th>' +
            '<th scope="col">Paid / Partial<span class="th-sub">Students</span></th>' +
            '<th scope="col">Full Unpaid<span class="th-sub">Students</span></th>' +
            '<th scope="col">Program Total<span class="th-sub">Paid + Unpaid</span></th>' +
            '<th scope="col">School Total<span class="th-sub">All programs</span></th>'
        );
        $foot.append('<th></th><th></th><th></th><th></th><th></th><th></th>');
        $('.schwise-legend').removeClass('d-none');
    } else {
        let headerText = 'Students';
        let headerSub = '';
        if (chalanStatus === 'totalpaid') { headerText = 'Paid / Partial'; headerSub = 'Students'; }
        else if (chalanStatus === 'unpaid') { headerText = 'Full Unpaid'; headerSub = 'Students'; }
        else if (chalanStatus === 'cancelled') { headerText = 'Cancelled'; headerSub = 'Students'; }
        $head.append(
            '<th scope="col">Program</th>' +
            '<th scope="col">' + headerText + (headerSub ? '<span class="th-sub">' + headerSub + '</span>' : '') + '</th>' +
            '<th scope="col">School</th>' +
            '<th scope="col">School Total<span class="th-sub">All programs</span></th>'
        );
        $foot.append('<th></th><th></th><th></th><th></th>');
        $('.schwise-legend').addClass('d-none');
    }

    const fmt = (v) => (Number(v) || 0).toLocaleString();
    const num = (v) => (typeof v === 'string' ? Number(String(v).replace(/[,]/g, '')) : (Number(v) || 0));
    const rowActiveTotal = (r) => num(r.totalpaid) + num(r.unpaid);
    const metricHtml = (val, cls) => '<span class="metric ' + cls + '">' + fmt(val) + '</span>';
    const schoolBadge = (code) => '<span class="school-badge">' + (code || '') + '</span>';
    const renderMetric = (data, type, cls) => {
        const val = num(data);
        if (type === 'display') return metricHtml(val, cls);
        if (type === 'filter' || type === 'export') return fmt(val);
        return val;
    };

    let columns;
    let schoolCodeColIdx;
    let schoolTotalColIdx;
    let orderCol;

    if (isAllStatus) {
        schoolCodeColIdx = 1;
        schoolTotalColIdx = 5;
        orderCol = 1;
        columns = [
            { data: 'prog_name', name: 'prog_name' },
            {
                data: 'school_code',
                name: 'school_code',
                className: 'num-col',
                render: function (data, type) {
                    if (type === 'display') return schoolBadge(data);
                    return data || '';
                }
            },
            {
                data: 'totalpaid',
                name: 'totalpaid',
                className: 'num-col',
                render: function (data, type) { return renderMetric(data, type, 'metric-paid'); }
            },
            {
                data: 'unpaid',
                name: 'unpaid',
                className: 'num-col',
                render: function (data, type) { return renderMetric(data, type, 'metric-unpaid'); }
            },
            {
                // Program row total: Paid + Unpaid
                data: null,
                name: 'program_total',
                orderable: false,
                className: 'num-col',
                render: function (data, type, row) {
                    const val = rowActiveTotal(row);
                    if (type === 'display') return metricHtml(val, 'metric-total');
                    if (type === 'filter' || type === 'export') return fmt(val);
                    return val;
                }
            },
            {
                // School-wise distinct active students (backend SCHOOL_TOTAL_STUDENTS)
                data: 'school_total_students',
                name: 'school_total_students',
                orderable: false,
                className: 'num-col',
                render: function (data, type, row) {
                    const total = num(data != null ? data : schoolTotals[row.sc_school_id]);
                    if (type === 'sort' || type === 'type') return total;
                    if (type === 'display') return metricHtml(total, 'metric-school');
                    if (type === 'filter' || type === 'export') return fmt(total);
                    return total;
                }
            }
        ];
    } else {
        schoolCodeColIdx = 2;
        schoolTotalColIdx = 3;
        orderCol = 3;
        columns = [
            { data: 'prog_name', name: 'prog_name' },
            {
                data: chalanStatus,
                name: chalanStatus,
                className: 'num-col',
                render: function (data, type) {
                    const cls = chalanStatus === 'unpaid' ? 'metric-unpaid'
                        : (chalanStatus === 'cancelled' ? 'metric-school' : 'metric-paid');
                    return renderMetric(data, type, cls);
                }
            },
            {
                data: 'school_code',
                name: 'school_code',
                className: 'num-col',
                render: function (data, type) {
                    if (type === 'display') return schoolBadge(data);
                    return data || '';
                }
            },
            {
                data: 'school_total_students',
                name: 'school_total_students',
                className: 'num-col',
                render: function (data, type, row, meta) {
                    const total = num(data != null ? data : schoolTotals[row.sc_school_id]);
                    const api = new $.fn.dataTable.Api(meta.settings);
                    const prev = (meta.row > 0) ? api.row(meta.row - 1).data()?.sc_school_id : null;
                    if (prev !== row.sc_school_id) {
                        if (type === 'display') return metricHtml(total, 'metric-school');
                        if (type === 'filter' || type === 'export') return fmt(total);
                        return total;
                    }
                    return '';
                }
            }
        ];
    }

    $('#summary-data-table2').DataTable({
        scrollX: true,
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 100,
        lengthMenu: [[10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000]],
        order: [[orderCol, 'asc']],
        language: {
            emptyTable: 'No records found for the selected filters.',
            zeroRecords: 'No matching records found.',
            processing: 'Loading, please wait...'
        },
        ajax: {
            dataType: 'json',
            method: 'post',
            url: '/api/get-challan-summary',
            data: function (d) {
                $('#error_message').addClass('d-none').hide().text('');
                return $.extend({}, d, filterParams, { summaryViewLevel: 'program' });
            },
            dataSrc: function (json) {
                let rows = json.data || [];
                // Accounts PDF shows active paid/unpaid only (skip cancelled-only programs)
                if (isAllStatus) {
                    rows = rows.filter(function (r) {
                        return (Number(r.totalpaid) || 0) + (Number(r.unpaid) || 0) > 0;
                    });
                }
                return rows;
            },
            error: function (xhr) {
                let msg = 'Unable to load the report. Please try again.';
                if (xhr && xhr.status === 419) {
                    msg = 'Your session has expired. Please refresh the page and try again.';
                } else if (xhr && (xhr.status === 401 || xhr.status === 403)) {
                    msg = 'You are not authorized to view this data.';
                } else if (xhr && xhr.status >= 500) {
                    msg = 'A server error occurred while generating the report.';
                }
                $('#error_message').removeClass('d-none').text(msg).show();
            }
        },
        drawCallback: function (settings) {
            const api = new $.fn.dataTable.Api(settings);
            schoolTotals = {};

            api.rows({ search: 'applied' }).every(function () {
                const r = this.data();
                const id = r.sc_school_id;
                // Prefer backend school-level distinct count (no program double-count).
                if (r.school_total_students != null && r.school_total_students !== '') {
                    schoolTotals[id] = num(r.school_total_students);
                } else {
                    const add = isAllStatus ? rowActiveTotal(r) : num(r[chalanStatus]);
                    schoolTotals[id] = (schoolTotals[id] || 0) + add;
                }
            });

            // Reset rowspan/display from previous draw
            api.rows({ page: 'current' }).nodes().to$().children('td').each(function () {
                $(this).css({ display: '', 'vertical-align': '', 'text-align': '', 'font-weight': '' }).removeAttr('rowspan');
            });

            let lastSchool = null;
            let rowspan = 1;
            let firstCellCode = null;
            let firstCellId = null;

            api.rows({ search: 'applied', order: 'applied', page: 'current' }).every(function () {
                const row = $(this.node());
                const schoolId = this.data().sc_school_id;
                const codeCell = row.children('td').eq(schoolCodeColIdx);
                const idCell = row.children('td').eq(schoolTotalColIdx);

                if (schoolId !== lastSchool) {
                    if (firstCellCode && rowspan > 1) {
                        firstCellCode.attr('rowspan', rowspan).css({ 'vertical-align': 'middle', 'text-align': 'center', 'font-weight': 'bold' });
                        firstCellId.attr('rowspan', rowspan).css({ 'vertical-align': 'middle', 'text-align': 'center', 'font-weight': 'bold' });
                    }
                    firstCellCode = codeCell;
                    firstCellId = idCell;
                    rowspan = 1;
                    idCell.html(metricHtml(schoolTotals[schoolId] || 0, 'metric-school'));
                    lastSchool = schoolId;
                } else {
                    codeCell.css('display', 'none');
                    idCell.css('display', 'none');
                    rowspan++;
                }
            });

            if (firstCellCode && lastSchool != null) {
                if (rowspan > 1) {
                    firstCellCode.attr('rowspan', rowspan).css({ 'vertical-align': 'middle', 'text-align': 'center', 'font-weight': 'bold' });
                    firstCellId.attr('rowspan', rowspan).css({ 'vertical-align': 'middle', 'text-align': 'center', 'font-weight': 'bold' });
                }
                firstCellId.html(metricHtml(schoolTotals[lastSchool] || 0, 'metric-school'));
                firstCellId.css({ 'vertical-align': 'middle', 'text-align': 'center', 'font-weight': 'bold' });
            }
        },
        columns: columns,
        columnDefs: [
            { orderable: false, targets: isAllStatus ? [0, 2, 3, 4, 5] : [0, 1, 3] }
        ],
        createdRow: function (row, data) {
            $(row).addClass('school-color-' + (data.sc_school_id % 10));
        },
        footerCallback: function () {
            const api = this.api();
            let totalPaid = 0;
            let totalUnpaid = 0;
            let totalSingle = 0;
            const perSchool = {};

            api.rows({ search: 'applied' }).every(function () {
                const d = this.data();
                if (isAllStatus) {
                    totalPaid += num(d.totalpaid);
                    totalUnpaid += num(d.unpaid);
                    if (d.school_total_students != null && d.school_total_students !== '') {
                        perSchool[d.sc_school_id] = num(d.school_total_students);
                    } else {
                        perSchool[d.sc_school_id] = (perSchool[d.sc_school_id] || 0) + rowActiveTotal(d);
                    }
                } else {
                    totalSingle += num(d[chalanStatus]);
                    if (d.school_total_students != null && d.school_total_students !== '') {
                        perSchool[d.sc_school_id] = num(d.school_total_students);
                    } else {
                        perSchool[d.sc_school_id] = (perSchool[d.sc_school_id] || 0) + num(d[chalanStatus]);
                    }
                }
            });
            const schoolGrand = Object.values(perSchool).reduce((a, b) => a + b, 0);

            if (isAllStatus) {
                $(api.column(0).footer()).html('<b>Grand Total</b>');
                $(api.column(1).footer()).html('');
                $(api.column(2).footer()).html(metricHtml(totalPaid, 'metric-paid'));
                $(api.column(3).footer()).html(metricHtml(totalUnpaid, 'metric-unpaid'));
                $(api.column(4).footer()).html(metricHtml(totalPaid + totalUnpaid, 'metric-total'));
                $(api.column(5).footer()).html(metricHtml(schoolGrand, 'metric-school'));
            } else {
                $(api.column(0).footer()).html('<b>Grand Total</b>');
                $(api.column(1).footer()).html(metricHtml(totalSingle, 'metric-paid'));
                $(api.column(2).footer()).html('');
                $(api.column(3).footer()).html(metricHtml(schoolGrand, 'metric-school'));
            }
        },
        autoWidth: false,
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'New Admissions Summary - School Wise',
                footer: true,
                exportOptions: {
                    columns: isAllStatus ? [0, 1, 2, 3, 4, 5] : [0, 1, 2, 3],
                    modifier: { page: 'all' },
                    format: {
                        body: function (data) { return $('<div>').html(data).text().trim(); },
                        header: function (data) { return $('<div>').html(data).text().replace(/\s+/g, ' ').trim(); },
                        footer: function (data) { return $('<div>').html(data).text().trim(); }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'New Admissions Summary - School Wise',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,
                exportOptions: {
                    columns: isAllStatus ? [0, 1, 2, 3, 4, 5] : [0, 1, 2, 3],
                    modifier: { page: 'all' },
                    format: {
                        body: function (data) { return $('<div>').html(data).text().trim(); },
                        header: function (data) { return $('<div>').html(data).text().replace(/\s+/g, ' ').trim(); },
                        footer: function (data) { return $('<div>').html(data).text().trim(); }
                    }
                },
                customize: function (doc) {
                    const now = new Date();
                    const genAt = now.toLocaleString('en-GB', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                    const sessionLabel = ($('#session_sel option:selected').text() || '').trim() || '-';
                    const studentType = ($('#studentTypeOption option:selected').text() || '').trim() || 'All';
                    const feeType = ($('#feeType option:selected').text() || '').trim() || '-';
                    const schRoundVal = $('#sch_round').val();
                    const admissionRound = schRoundVal
                        ? (($('#sch_round option:selected').text() || '').trim() || schRoundVal)
                        : 'All';
                    const schoolVal = $('#school_sel').val();
                    const schoolLabel = schoolVal
                        ? (($('#school_sel option:selected').text() || '').trim() || schoolVal)
                        : 'All Schools';

                    doc.pageMargins = [28, 50, 28, 40];
                    doc.defaultStyle = { fontSize: 9, color: '#334155' };
                    doc.styles = doc.styles || {};
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 8.5,
                        color: '#FFFFFF',
                        fillColor: '#1e3a5f',
                        alignment: 'center'
                    };

                    // Keep the table intact: only INSERT a brand header before it.
                    // (Never splice/replace content[0] — with an empty title that WAS the table.)
                    let tableIdx = -1;
                    for (let i = 0; i < doc.content.length; i++) {
                        if (doc.content[i] && doc.content[i].table) {
                            tableIdx = i;
                            break;
                        }
                    }
                    if (tableIdx < 0) {
                        return;
                    }

                    // Drop DataTables default title text node (we render our own header)
                    if (tableIdx > 0) {
                        doc.content.splice(0, tableIdx);
                        tableIdx = 0;
                    }

                    doc.content.splice(tableIdx, 0, {
                        margin: [0, 0, 0, 8],
                        stack: [
                            {
                                columns: [
                                    {
                                        width: '*',
                                        stack: [
                                            { text: 'Beaconhouse National University', color: '#1e3a5f', bold: true, fontSize: 13, margin: [0, 0, 0, 2] },
                                            { text: 'Student Financials  |  Challan Report', fontSize: 8, color: '#64748b' }
                                        ]
                                    },
                                    {
                                        width: 'auto',
                                        stack: [
                                            { text: 'Generated', fontSize: 7, color: '#94a3b8', alignment: 'right' },
                                            { text: genAt, fontSize: 8, color: '#475569', alignment: 'right', bold: true }
                                        ]
                                    }
                                ]
                            },
                            {
                                canvas: [{ type: 'line', x1: 0, y1: 0, x2: 780, y2: 0, lineWidth: 1, lineColor: '#1e3a5f' }],
                                margin: [0, 6, 0, 6]
                            },
                            {
                                text: 'New Admissions Summary - School Wise',
                                fontSize: 11,
                                bold: true,
                                color: '#0f172a',
                                margin: [0, 0, 0, 4]
                            },
                            {
                                text: [
                                    { text: 'Session: ', bold: true }, sessionLabel + '   ',
                                    { text: 'School: ', bold: true }, schoolLabel + '   ',
                                    { text: 'Student Type: ', bold: true }, studentType + '   ',
                                    { text: 'Fee Type: ', bold: true }, feeType + '   ',
                                    { text: 'Admission Round: ', bold: true }, admissionRound
                                ],
                                fontSize: 8,
                                color: '#475569',
                                margin: [0, 0, 0, 6]
                            }
                        ]
                    });

                    tableIdx = -1;
                    for (let i = 0; i < doc.content.length; i++) {
                        if (doc.content[i] && doc.content[i].table) {
                            tableIdx = i;
                            break;
                        }
                    }
                    const tableNode = doc.content[tableIdx];
                    const body = tableNode.table.body;

                    if (isAllStatus) {
                        tableNode.table.widths = ['*', 48, 70, 70, 70, 72];
                    } else {
                        tableNode.table.widths = ['*', 80, 48, 80];
                    }
                    tableNode.table.headerRows = 1;
                    tableNode.table.dontBreakRows = true;
                    tableNode.layout = {
                        hLineWidth: function (i, node) {
                            if (i === 0 || i === 1 || i === node.table.body.length) return 0.7;
                            return 0.3;
                        },
                        vLineWidth: function () { return 0.3; },
                        hLineColor: function (i, node) {
                            if (i === 0 || i === 1 || i === node.table.body.length) return '#1e3a5f';
                            return '#e2e8f0';
                        },
                        vLineColor: function () { return '#e2e8f0'; },
                        paddingLeft: function () { return 5; },
                        paddingRight: function () { return 5; },
                        paddingTop: function () { return 4; },
                        paddingBottom: function () { return 4; }
                    };

                    const palette = [
                        '#F8FAFC', '#F1F5F9', '#EEF6FF', '#F0FDF4',
                        '#FFFBEB', '#FFF7ED', '#FDF2F8', '#F5F3FF'
                    ];
                    const colors = {};
                    let lastSchoolCode = null;
                    let schoolColorIdx = 0;

                    const cellText = function (cell) {
                        if (!cell) return '';
                        if (typeof cell === 'string' || typeof cell === 'number') return String(cell);
                        if (cell.text != null) return String(cell.text);
                        return '';
                    };

                    if (body[0]) {
                        body[0].forEach(function (cell, idx) {
                            if (typeof cell === 'object') {
                                cell.fillColor = '#1e3a5f';
                                cell.color = '#FFFFFF';
                                cell.bold = true;
                                cell.fontSize = 8;
                                cell.alignment = (idx === 0) ? 'left' : 'center';
                            }
                        });
                    }

                    const isGrandTotalRow = function (row) {
                        return cellText(row[0]).toLowerCase().indexOf('grand total') !== -1;
                    };

                    for (let i = 1; i < body.length; i++) {
                        const row = body[i];
                        if (!row || !row.length) continue;

                        if (isGrandTotalRow(row)) {
                            row.forEach(function (cell, idx) {
                                if (typeof cell !== 'object') return;
                                cell.fillColor = '#1e3a5f';
                                cell.color = '#FFFFFF';
                                cell.bold = true;
                                cell.fontSize = 9;
                                cell.alignment = (idx === 0) ? 'left' : 'center';
                            });
                            continue;
                        }

                        let schoolCode = cellText(row[schoolCodeColIdx]).trim();
                        if (schoolCode && schoolCode === lastSchoolCode) {
                            if (typeof row[schoolCodeColIdx] === 'object') row[schoolCodeColIdx].text = '';
                            else row[schoolCodeColIdx] = '';
                            if (row[schoolTotalColIdx]) {
                                if (typeof row[schoolTotalColIdx] === 'object') row[schoolTotalColIdx].text = '';
                                else row[schoolTotalColIdx] = '';
                            }
                        } else if (schoolCode) {
                            lastSchoolCode = schoolCode;
                            if (!colors[schoolCode]) {
                                colors[schoolCode] = palette[schoolColorIdx % palette.length];
                                schoolColorIdx++;
                            }
                        }

                        const bgColor = colors[schoolCode] || colors[lastSchoolCode] || '#FFFFFF';
                        row.forEach(function (cell, idx) {
                            if (typeof cell !== 'object') return;
                            cell.fillColor = bgColor;
                            cell.color = '#334155';
                            cell.fontSize = 8.5;
                            cell.alignment = (idx === 0) ? 'left' : 'center';
                            if (idx === schoolCodeColIdx && cellText(cell)) {
                                cell.bold = true;
                                cell.color = '#1e3a5f';
                            }
                            if (idx === schoolTotalColIdx && cellText(cell)) {
                                cell.bold = true;
                            }
                        });
                    }

                    doc.footer = function (currentPage, pageCount) {
                        return {
                            margin: [28, 0, 28, 0],
                            columns: [
                                {
                                    text: 'BNU CMS  |  Confidential - for internal use only',
                                    fontSize: 7,
                                    color: '#94a3b8',
                                    alignment: 'left'
                                },
                                {
                                    text: 'Page ' + currentPage + ' of ' + pageCount,
                                    fontSize: 7,
                                    color: '#64748b',
                                    alignment: 'right'
                                }
                            ]
                        };
                    };
                }
            },
            {
                extend: 'print',
                title: 'New Admissions Summary - School Wise',
                footer: true,
                exportOptions: {
                    columns: isAllStatus ? [0, 1, 2, 3, 4, 5] : [0, 1, 2, 3],
                    format: {
                        body: function (data) { return $('<div>').html(data).text().trim(); },
                        header: function (data) { return $('<div>').html(data).text().replace(/\s+/g, ' ').trim(); },
                        footer: function (data) { return $('<div>').html(data).text().trim(); }
                    }
                }
            }
        ]
    });
}

// function LoadExistingSummary(){
//     $('.filterTable').addClass('d-none');
//     $('.summaryTable2').addClass('d-none');
//     $('.summaryTable').removeClass('d-none');
//     let filterParams = getFilterParams();
//     $('#summary-data-table').DataTable({
//         scrollX: true, // Enable horizontal scrolling
//         responsive: true,
//         fixedHeader: false,
//         scrollCollapse: false,
//         processing: true,
//         serverSide: false, //to download all data once
//         select: false,
//         searching: true,
//         destroy: true,
//         lengthChange: true,
//         paging: true,
//         pageLength: 100, // Set default page length to 100
//         lengthMenu: [ [10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000] ],
//         order: [1, 'desc'],
//         ajax:{
//             dataType: 'json',
//             method: 'post',
//             url:  "/api/get-challan-summary",
//             data: function(d) {
//                 return $.extend({}, d, filterParams);
//             },
//         },
//         columns: [
//             {data: "sess_name", name: "sess_name"},
//             {data: "school_name", name: "school_name"},
//             {data: "dept_name", name: "dept_name"},
//             {data: "prog_name", name: "prog_name"},
//             {data: "total_students", name: "total_students"},
//             {data: "cancelled", name: "cancelled"},
//             {data: "active", name: "active"},
//             {data: "fullpaid", name: "fullpaid"},
//             {data: "installment", name: "installment"},
//             {data: "totalpaid", name: "totalpaid"},
//             {data: "unpaid", name: "unpaid"}

//         ],
//         footerCallback: function(row, data, start, end, display) {
//             let api = this.api();

//             // Helper function to get integer value of a column
//             let intVal = function(i) {
//                 return typeof i === 'string' ?
//                     i.replace(/[\$,]/g, '') * 1 :
//                     typeof i === 'number' ? i : 0;
//             };

//             // Calculate the total sum for each column
//             let totalIssuedStudents = api
//                 .column(4, { page: 'current' }) // Index of 'Issued Students' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalCancelled = api
//                 .column(5, { page: 'current' }) // Index of 'Cancelled' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalActive = api
//                 .column(6, { page: 'current' }) // Index of 'Active' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalFullPaid = api
//                 .column(7, { page: 'current' }) // Index of 'Full Paid' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalInstallment = api
//                 .column(8, { page: 'current' }) // Index of 'Installment' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalPaid = api
//                 .column(9, { page: 'current' }) // Index of 'Total Paid' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             let totalUnpaid = api
//                 .column(10, { page: 'current' }) // Index of 'Unpaid' column
//                 .data()
//                 .reduce((a, b) => intVal(a) + intVal(b), 0);

//             // Update the footer with the totals
//             $(api.column(4).footer()).html('' + totalIssuedStudents.toLocaleString());
//             $(api.column(5).footer()).html(' ' + totalCancelled.toLocaleString());
//             $(api.column(6).footer()).html('' + totalActive.toLocaleString());
//             $(api.column(7).footer()).html('' + totalFullPaid.toLocaleString());
//             $(api.column(8).footer()).html('' + totalInstallment.toLocaleString());
//             $(api.column(9).footer()).html('' + totalPaid.toLocaleString());
//             $(api.column(10).footer()).html('' + totalUnpaid.toLocaleString());
//         },
//         autoWidth: false, // Ensures DataTable does not set its own width
//         dom: 'Blfrtip', // Adds the buttons to the DataTable
//         buttons: [
//             {
//                 extend: 'excelHtml5',
//                 title: 'Challan Summary',
//                 footer: true,  // This ensures footer is included
//                 exportOptions: {
//                         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10],
//                         modifier: {
//                         page: 'all'
//                     }
//                 }
//             },
//             {
//                 extend: 'pdfHtml5',
//                 title: 'Challan Summary',
//                 orientation: 'landscape',
//                 pageSize: 'A4',
//                 footer: true,  // This ensures footer is included
//                 exportOptions: {
//                     columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10],
//                     modifier: {
//                         page: 'all'
//                     }
//                 }

//             },
//             {
//                 extend: 'print',
//                 title: 'Challan Summary',
//                 exportOptions: {
//                     columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10]
//                     }
//             }
//         ]
//     });
// }
function previewRow(challan_id){
var link = 'https://staff.bnu.edu.pk/chalanfin.php?challan_id='+challan_id;

setTimeout(function(){
        // $('.hidegif').hide();
        window.open(
            link,
            '_blank' // <- This is what makes it open in a new window.
            );
    }, 1000);
}
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
                    // console.log(res.data);
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
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name}  &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
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
    // console.log(url);
    //   $.ajax({
    //     url: url,
    //     method: 'GET',
    //     success: function(response) {
    //       if (response.status) {
    //         let html = '<option value="">All Program</option>';
    //         $.each(response.data, function(key, value) {
    //           html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
    //         });
    //         $('select[name="course"]').html(html); // Update "course" select
    //       } else {
    //         console.error('Error fetching programs:', response.message); // Log error message
    //         // Optionally display an error message to the user
    //       }
    //     },
    //     error: function(response) {
    //       console.error('Error fetching programs:', response.statusText); // Log error details
    //       // Optionally display a generic error message to the user
    //     }
    //   });
}
function getDepartments(){
    var school_id = $('#school_sel').val();
    getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
        // console.log(res);
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
function exportToPDF(tableId){
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}

function refreshFields(){
     // Clear the checkbox and reset button visibility

        // Add any additional field-clearing logic here
    $('#main-Id').val('');
    // $('#session_sel').val('');
    $('#roadmap_session_sel').val('');
    $('#school_sel').val('');
    $('#studentTypeOption').val('');
    $('#feeType').val('');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');
    $('#eff-from').val('');
    $('#eff-to').val('');
    $('#fromDate').val('');
    $('#toDate').val('');
    $('#tutionAmt').val('');
    $('#admissionAmt').val('');
    $('#securityAmt').val('');
    $('#medicalAmt').val('');
    $('#credFee').val('');
    $('#hostelAmt').val('');
    $('#activitychargesAmt').val('');
    $('#challanStatus').val('');
    $('#sch_round').val('');
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
    // console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    // console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    // console.log(distinctDegreeLevels);
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
                    // console.log(res.data);
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
                    // console.log($('#program').val());

                } else {
                    console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
function getFilterParams() {
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const studentTypeOption = $('#studentTypeOption').val();
    const feeType = $('#feeType').val();
    const department = $('#department').val();
    const degree_level = $('#degree_level').val();
    const program = $('#program').val();
    const challanStatus = $('#challanStatus').val();
    const fromDate = $('#fromDate').val();
    const toDate = $('#toDate').val();
    const sch_round = $('#sch_round').val();
    // const tutionAmt = $('#tutionAmt').val();
    // const admissionAmt = $('#admissionAmt').val();
    // const securityAmt = $('#securityAmt').val();
    // const medicalAmt = $('#medicalAmt').val();
    // const credFee = $('#credFee').val();
    // const activitychargesAmt = $('#activitychargesAmt').val();
    let filterParams =  {
        session_sel: session_sel,
        school_sel: school_sel,
        studentTypeOption: studentTypeOption,
        feeType: feeType,
        department: department,
        degree_level: degree_level,
        program: program,
        challanStatus: challanStatus,
        fromDate: fromDate,
        toDate: toDate,
        sch_round: sch_round
        // tutionAmt: tutionAmt,
        // admissionAmt: admissionAmt,
        // securityAmt: securityAmt,
        // medicalAmt: medicalAmt,
        // credFee: credFee,
        // activitychargesAmt: activitychargesAmt
    };
    return filterParams;
}

</script>

@stop

