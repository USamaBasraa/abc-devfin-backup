@extends('layouts.app')

@section('title')
BNU CMS
@stop

@section('content')


<style>
    .scg-card { border: 0; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .section-title { color: #4454c3; border-bottom: 2px solid #4454c3; padding-bottom: 8px; margin-bottom: 1.25rem; }
    .required-star { color: #dc3545; }
    .field-error { display: block; font-size: 12px; margin-top: 3px; }
    .student-profile-wrap { margin-bottom: 1.25rem; }
    .scg-options-box {
        background: #f7f8fd;
        border: 1px solid #e3e6f5;
        border-radius: 10px;
        padding: 18px 16px 8px;
        margin-bottom: 1rem;
    }
    .scg-options-box .form-group {
        margin-bottom: 14px;
    }
    .scg-options-box label {
        font-weight: 600;
        font-size: .88rem;
        color: #3a4155;
        margin-bottom: 4px;
    }
    .scg-subhead {
        width: 100%;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .45px;
        color: #6b7280;
        margin: 2px 0 10px;
        padding-top: 10px;
        border-top: 1px dashed #d8deef;
    }
    .scg-subhead:first-child {
        border-top: 0;
        padding-top: 0;
        margin-top: 0;
    }
    .scg-fee-panel {
        background: #fff;
        border: 1px solid #e3e6f5;
        border-radius: 8px;
        padding: 12px 12px 2px;
        margin-bottom: 8px;
    }
    /* Hostel utility fee + include checkbox alignment */
    .scg-utility-fee-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .scg-utility-fee-group .input-group-append {
        display: flex;
        align-items: stretch;
    }
    .scg-utility-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 0;
        padding: 0 12px;
        min-width: 88px;
        cursor: pointer;
        background: #f3f5fb;
        border: 1px solid #ced4da;
        border-left: 0;
        border-radius: 0 4px 4px 0;
        font-size: 12px;
        font-weight: 600;
        color: #4b5563;
        user-select: none;
        white-space: nowrap;
    }
    .scg-utility-check input[type="checkbox"] {
        width: 15px;
        height: 15px;
        margin: 0;
        flex-shrink: 0;
        cursor: pointer;
        vertical-align: middle;
    }
    .scg-utility-check span {
        line-height: 1;
    }
    .scg-utility-check:hover {
        background: #e9edf8;
        color: #374151;
    }
    .scg-utility-fee-group:focus-within .scg-utility-check {
        border-color: #80bdff;
    }
    .scg-flags-box {
        background: #f8fbff;
        border: 1px solid #dce8f5;
        border-radius: 10px;
        padding: 14px 12px 10px;
        margin-bottom: 1rem;
    }
    .scg-flags-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
    }
    .scg-flags-installments {
        flex: 0 0 220px;
        max-width: 100%;
        margin-bottom: 0 !important;
    }
    .scg-flags-options {
        flex: 1 1 480px;
        min-width: 0;
        margin-bottom: 0 !important;
    }
    .scg-options-inline {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        gap: 8px;
        width: 100%;
    }
    .scg-option-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 0;
        min-width: 0;
        height: 38px;
        margin: 0;
        padding: 0 10px;
        border: 1px solid #d7dbe8;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        font-size: .8rem;
        font-weight: 600;
        color: #3a4155;
        line-height: 1.2;
        transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
        user-select: none;
    }
    .scg-option-chip span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .scg-option-chip input[type="checkbox"] {
        width: 15px;
        height: 15px;
        margin: 0;
        flex-shrink: 0;
        cursor: pointer;
        accent-color: #4454c3;
        position: static !important;
    }
    .scg-option-chip .form-check-input {
        position: static !important;
        margin-top: 0 !important;
    }
    .scg-option-chip:hover {
        border-color: #4454c3;
        background: #f7f8fd;
    }
    .scg-option-chip.scg-option-disabled,
    .scg-option-chip.scg-option-disabled:hover {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
        border-color: #d5d9e6;
        background: #f3f4f8;
        box-shadow: none;
    }
    .scg-option-chip.is-checked,
    .scg-option-chip:has(input:checked) {
        border-color: #4454c3;
        background: #eef0fb;
        color: #4454c3;
        box-shadow: 0 0 0 1px rgba(68, 84, 195, .12);
    }
    .scg-flags-box .form-group {
        margin-bottom: 0;
    }
    .scg-flags-box > .scg-flags-row > .form-group > label,
    .scg-flags-options > label {
        font-weight: 600;
        font-size: .88rem;
        color: #3a4155;
        margin-bottom: 6px;
    }
    @media (max-width: 991px) {
        .scg-options-inline {
            flex-wrap: wrap;
        }
        .scg-option-chip {
            flex: 1 1 calc(50% - 8px);
        }
    }
    .custom-card-header {
        background: #fff;
        color: #4454c3;
        font-weight: 700;
        padding: .85rem 1rem;
        font-size: 1rem;
        border-bottom: 2px solid #4454c3 !important;
    }
    .student-info-card {
        border: 1px solid #e3e6f5;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 10px rgba(68, 84, 195, .06);
    }
    .student-info-item {
        background: #f7f8fd;
        border: 1px solid #e8ecf5;
        border-radius: 8px;
        min-height: 70px;
        padding: 12px 14px;
    }
    .student-info-item small {
        display: block;
        color: #6b7280;
        font-size: .8rem;
        margin-bottom: 2px;
    }
    .focused-border {
        border: 2px solid #4454c3 !important;
        border-radius: 6px;
        outline: none;
        box-shadow: 0 4px 6px rgba(68, 84, 195, 0.18);
    }
    .form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #e8ecf5;
    }
    .scg-surplus-disclaimer {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #f0c36d;
        background: #fff8e8;
        color: #7a5b12;
        font-size: 0.9rem;
        line-height: 1.4;
        font-weight: 600;
    }
    .btn-generate-challan {
        min-width: 200px;
        padding: 11px 20px;
        font-weight: 600;
        border: 0;
        border-radius: 8px;
        color: #fff;
        background: linear-gradient(135deg, #3b49b5 0%, #4454c3 55%, #5a69d6 100%);
        box-shadow: 0 8px 18px rgba(68, 84, 195, .28);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .btn-generate-challan:hover,
    .btn-generate-challan:focus {
        color: #fff;
        filter: brightness(1.04);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(68, 84, 195, .34);
    }
    .btn-clear-form {
        min-width: 120px;
        padding: 10px 18px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #d7dbe8;
        color: #5c6478;
        background: #fff;
    }
    .btn-clear-form:hover {
        border-color: #4454c3;
        color: #4454c3;
        background: #f7f8fd;
    }
    .btn-filter-challan {
        padding: 10px 16px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #c5cef0;
        color: #4454c3;
        background: #f7f8fd;
    }
    .btn-filter-challan:hover,
    .btn-filter-challan:focus {
        color: #4454c3;
        background: #eef0fb;
        border-color: #4454c3;
    }
    .scg-bulk-actions {
        margin-left: auto;
        display: none;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-surplus-bulk {
        min-width: 160px;
        padding: 10px 18px;
        font-weight: 600;
        border-radius: 8px;
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, #0d7377 0%, #14919b 55%, #2a9d8f 100%);
        box-shadow: 0 6px 14px rgba(20, 145, 155, .25);
        align-items: center;
        justify-content: center;
    }
    .btn-surplus-bulk:hover,
    .btn-surplus-bulk:focus {
        color: #fff;
        filter: brightness(1.04);
    }
    .btn-cancel-challan {
        min-width: 160px;
        padding: 10px 18px;
        font-weight: 600;
        border-radius: 8px;
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, #c82333 0%, #dc3545 55%, #e4606d 100%);
        box-shadow: 0 6px 14px rgba(220, 53, 69, .25);
        align-items: center;
        justify-content: center;
    }
    .btn-cancel-challan:hover,
    .btn-cancel-challan:focus {
        color: #fff;
        filter: brightness(1.04);
    }
    .scg-bulk-selected-table {
        margin-bottom: 0;
        background: #fff;
    }
    .scg-bulk-selected-table th {
        background: #f3f5fb;
        font-size: .8rem;
        white-space: nowrap;
    }
    .scg-bulk-selected-table td {
        font-size: .88rem;
        vertical-align: middle;
    }
    .scg-table-wrap {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e8ecf5;
    }
    .scg-table-wrap .dataTables_wrapper .scg-dt-top {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 10px;
        gap: 8px;
    }
    .scg-table-wrap .dataTables_wrapper .scg-dt-left {
        flex: 0 0 auto;
    }
    .scg-table-wrap .dataTables_wrapper .scg-dt-right {
        flex: 0 0 auto;
        margin-left: auto;
        text-align: right;
    }
    .scg-table-wrap .dataTables_wrapper .dataTables_filter {
        float: none;
        text-align: right;
    }
    .scg-table-wrap .dataTables_wrapper .dataTables_length {
        float: none;
    }
    .scg-table-wrap .dataTables_filter label {
        margin-bottom: 0;
    }
    #mydatatable thead th {
        background: #f3f5fb;
        color: #3a4155;
        font-weight: 700;
        font-size: .82rem;
        white-space: nowrap;
        border-bottom: 2px solid #dfe3f0;
    }
    #mydatatable tbody td {
        vertical-align: middle;
        font-size: .88rem;
    }
    /* Fee Refund / Hostel Fee Refund rows */
    #mydatatable tbody tr.scg-refund-row > td {
        background-color: #fff !important;
    }
    #mydatatable tbody tr.scg-refund-row:hover > td {
        background-color: #ffff !important;
    }
    .scg-refund-badge {
        background-color: #e67e22;
        color: #fff;
        margin-left: 4px;
        font-weight: 600;
        letter-spacing: .02em;
    }
    .scg-challan-type-badge {
        font-weight: 600;
        white-space: nowrap;
        letter-spacing: .01em;
    }
    .scg-ctype-full { background-color: #3498db; color: #fff; }
    .scg-ctype-summer { background-color: #9b59b6; color: #fff; }
    .scg-ctype-hostel { background-color: #16a085; color: #fff; }
    .scg-ctype-refund { background-color: #e67e22; color: #fff; }
    .scg-ctype-credit { background-color: #2980b9; color: #fff; }
    .scg-ctype-processing { background-color: #7f8c8d; color: #fff; }
    .scg-ctype-other { background-color: #95a5a6; color: #fff; }
    /* Degree Level on Summer Credit — display only, still submitted */
    #degree_level.scg-degree-level-ro {
        pointer-events: none;
        background-color: #e9ecef;
        color: #495057;
        cursor: not-allowed;
    }
    #mydatatable td.scg-action-td {
        min-width: 110px;
        width: 120px;
        white-space: nowrap;
    }
    #mydatatable td.scg-check-td,
    #mydatatable th.scg-check-th {
        width: 42px;
        text-align: center;
        vertical-align: middle;
    }
    .scg-action-stack {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
    }
    .scg-action-stack .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 0;
        padding: 3px 8px;
        font-size: 12px;
        line-height: 1.3;
        white-space: nowrap;
    }
    .scg-action-stack .btn i {
        margin-right: 4px;
    }
    .scg-search-bar {
        margin-bottom: 1rem;
    }
    .scg-search-bar .scg-search-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px 16px;
    }
    .scg-search-bar .scg-search-field {
        flex: 1 1 220px;
        min-width: 200px;
        max-width: 360px;
        margin-bottom: 0;
    }
    .scg-search-bar .scg-search-field label {
        display: block;
        min-height: 22px;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: .9rem;
        color: #3a4155;
        line-height: 1.25;
    }
    .scg-search-bar .scg-search-field .form-control {
        height: 38px;
    }
    .scg-search-bar .scg-search-actions {
        flex: 0 0 auto;
        margin-bottom: 0;
        padding-bottom: 0;
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }
    .scg-search-bar #searchButton,
    .scg-search-bar #searchClearBtn {
        height: 38px;
        min-width: 100px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 16px;
    }
    .scg-search-bar #searchClearBtn {
        border: 1px solid #d7dbe8;
        color: #5c6478;
        background: #fff;
        font-weight: 600;
        border-radius: .25rem;
    }
    .scg-search-bar #searchClearBtn:hover {
        border-color: #4454c3;
        color: #4454c3;
        background: #f7f8fd;
    }
    .scg-search-bar .scg-session-hint-row {
        min-height: 22px;
        margin-top: 8px;
        padding-left: 0;
    }
    .scg-search-bar #tableSessionHint {
        display: block;
        color: #6b7280;
        font-size: .82rem;
        line-height: 1.35;
    }
    .scg-search-bar #studentEnrollment-error {
        min-height: 0;
        margin-top: 4px;
    }
    .hidden { display: none; }
    select:required,
    select:required:focus {
        border-color: #dc3545;
    }
</style>


<div class="container-fluid">
    <div class="card scg-card">
        <div class="card-body">

        <form id="singleChallanForm" autocomplete="off">
            <h3 class="section-title">Single Challan Generation</h3>
            @csrf
            <div class="scg-search-bar">
                <input type="hidden" name="verifyEdit" id="verifyEdit" value="">
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <div class="scg-search-controls">
                    <div class="form-group scg-search-field">
                        <label for="studentEnrollment">
                            Enrollment No# or Applicant ID
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" class="form-control" id="studentEnrollment" name="studentEnrollment"
                            placeholder="Enrollment No / Applicant ID" onkeydown="handleEnterKey(event)"
                            oninput="updateProcessingFeeOptionAvailability()"
                            aria-label="Student Enrollment Number">
                        <span id="studentEnrollment-error" class="error text-danger field-error"></span>
                    </div>

                    <div class="form-group scg-search-field">
                        <label for="tableSessionFilter">
                            Challan List Session
                        </label>
                        <select id="tableSessionFilter" class="form-control" onchange="applyTableSessionFilter()">
                            <option value="latest" selected>Latest Session (default)</option>
                            <option value="all">All Sessions</option>
                        </select>
                    </div>

                    <div class="form-group scg-search-actions">
                        <button class="btn btn-primary" type="button" id="searchButton" onclick="searchStudent()">
                            <i class="fa fa-search"></i>
                            <span>Search</span>
                        </button>
                        <button class="btn" type="button" id="searchClearBtn" onclick="clearFormAndContent()">
                            <i class="fa fa-undo"></i>
                            <span>Clear</span>
                        </button>
                    </div>
                </div>

                <div class="scg-session-hint-row">
                    <small id="tableSessionHint"></small>
                </div>
            </div>

            <div class="student-profile-wrap">
                <div id="profileDiv"></div>
            </div>

            <input name='st_school_id' id='st_school_id' hidden>
            <input name='st_dept_id' id='st_dept_id' hidden>
            <input name='st_prog_id' id='st_prog_id' hidden>
            <input name='st_aai_id' id='st_aai_id' hidden>

            <h5 class="section-title" style="font-size: 1.05rem;">Challan Parameters</h5>
            <div class="scg-options-box">
            <div class="row" id='installmentFocus'>

                <div class="col-12">
                    <div class="scg-subhead">Basic Info</div>
                </div>

                <div class="form-group col-md-4">
                    <label for="sessionId">Session <span class="required-star">*</span></label>
                    <select id="sessionId" name="sessionId" class="form-control">
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span id="sessionId-error" class="error text-danger"></span>
                </div>

                {{-- <div class="form-group col-md-4">
                    <label for="installmentSelect">No. of Installments:</label>
                    <select class="form-control" name="installmentSelect" id="installmentSelect">
                        <option disabled selected>---Select Option---</option>
                        <option value="2">2 Installments</option>
                        <option value="3">3 Installments</option>
                        <option value="4">4 Installments</option>
                        <option value="5">5 Installments</option>
                    </select>
                    <span id="installmentSelect-error" class="error text-danger mt-2 pl-2"></span>
                </div> --}}

                <div class="form-group col-md-4">
                    <label for="studentType">Student Type <span class="required-star">*</span></label>
                    <select name="student_type" id="student_type" class="form-control">
                        <option value="">Select type</option>
                        <option value="Existing">Existing</option>
                        {{-- <option value="New without Registration FALL">New without Registration FALL</option>
                        <option value="New without Registration SPRING">New without Registration SPRING</option>
                        <option value="New without Registration SUMMER">New without Registration SUMMER</option> --}}
                        <option value="New Admission FALL">New Admission FALL</option>
                        <option value="New Admission SPRING">New Admission SPRING</option>
                        <option value="New Admission SUMMER">New Admission SUMMER</option>
                    </select>
                    <span id="student_type-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                    <label for="studentStatus">Student Status <span class="required-star">*</span></label>
                    <select name="student_status" id="student_status" class="form-control">
                        <option value="">Select status</option>
                        <option value="Active">Active</option>
                        <option value="Expected-Graduate">Expected Graduate</option>
                        <option value="Dormant">Dormant</option>
                        <option value="Left">Left</option>
                        <option value="Struck off">Struck off</option>
                    </select>
                    <span id="student_status-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                    <label for="feeType">Fee Type <span class="required-star">*</span></label>
                    <select name="fee_type" id="feeType" class="form-control" onchange="getFeeStructure()">
                        <option value="">Select fee type</option>
                        <option value="Full_semester">Full Semester wise</option>
                        <!-- <option value="Credit_hours">Credit Hour wise</option> -->
                        <option value="Credit_hours_Summer_Course">Credit Hour wise Summer Course</option>
                        <option value="Hostel_Full_semster">Hostel Full Semester</option>
                        <option value="Hostel_day_wise">Hostel Days Wise</option>
                        <option value="Hostel_other_utilities">Hostel Other Utilities</option>
                        <option value="Fee_Refund">Fee Refund</option>
                        <option value="Hostel_Fee_Refund">Hostel Fee Refund</option>
                        <option value="Short/Summer_Course">Short / Summer Course</option>
                        <option value="Processing_Fee">Processing Fee</option>
                    </select>
                    <span id="fee_type-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                    <label for="dueDate">Due Date</label>
                    <input type="date" class="form-control" id="dueDate" name="dueDate">
                    <span id="dueDate-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="anotherDate">Validate Date</label>
                    <input type="date" class="form-control" id="validateDate" name="validateDate">
                    <span id="validateDate-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4" id='invoiceAmountOpt' style="display: none;">
                    <label for="studentInvoiceamount">Invoice Amount</label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="studentInvoiceamount" id="studentInvoiceamount"
                            oninput="validateInput(this)">
                    </div>
                </div>
                <div class="form-group col-md-4" id='scholarshipsAmountOpt' style="display: none;">
                    <label for="scholarshipsAmount">Scholarships Amount</label>
                    <div class="input-group">
                        <input readonly class="form-control" type="text" name="scholarshipsAmount"
                            id="scholarshipsAmount" oninput="validateInput(this)">
                    </div>
                </div>

                <div class="form-group col-md-4 roadmap-semester-div" id="roadmap-sess-Div">
                    <label for="roadmap-sess">Road Map Session</label>
                    <div class="input-group">
                        <select name="roadmap_sess" id="roadmap-sess" disabled class="form-control"
                            onchange="getFeeStructure()">
                            <option value="">Select Roadmap</option>
                            @foreach ($sessions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span id="roadmap-sess-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-4 roadmap-semester-div" id="semester-Div">
                    <label for="semester">Semester</label>
                    <div class="input-group">
                        <select name="semester" id="semester" disabled class="form-control"
                            onchange="getFeeStructure()">
                            <option value="">Select Semester</option>
                            @foreach ($semesters as $key => $value)
                                <option value="{{$value->semester_id}}">{{ $value->semester_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span id="semester-error" class="error text-danger"></span>
                </div>

                <div class="form-group d-none col-md-4 credit_hr_div">
                    <label for="credithours">Credit Hours</label>
                    <input type="number" min='0' class="form-control" id="creditHr" name="creditHr">
                </div>

                <div class="form-group d-none col-md-4 credit_hr_summer_div" id="degreeLevelDiv">
                    <label for="degree_level">Degree Level <span class="required-star">*</span></label>
                    {{-- Read-only (from student program). Keep enabled so FormData still posts value. --}}
                    <select name="degree_level" id="degree_level" class="form-control scg-degree-level-ro" tabindex="-1">
                        <option value="">Select Degree Level</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Graduate">Graduate</option>
                        <option value="Postgraduate">Postgraduate</option>
                        <option value="Doctorate">Doctorate</option>
                    </select>
                    <small class="text-muted">From student program (not editable)</small>
                    <span id="degree_level-error" class="error text-danger"></span>
                </div>

                <div class="col-12">
                    <div id="full_semester_dev" class="scg-fee-panel">
                        <div class="scg-subhead" style="border-top:0;padding-top:0;">Fee Amounts</div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="tuitionFee">Tuition Fee</label>
                                <input hidden type="text" name="refundEdit" id="refundEdit">
                                <input hidden type="text" name="editChallan" id="editChallan">
                                <div class="input-group">
                                    <input placeholder="Enter tuition fee" readonly class="form-control" type="text"
                                        name="tuitionFee" id="tuitionFee" oninput="validateInput(this)">
                                </div>
                                <span id="tuitionFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4 admissionFeeDiv">
                                <label for="admissionFee">Admission Fee</label>
                                <div class="input-group">
                                    <input placeholder="Enter admission fee" readonly class="form-control" type="text"
                                        name="admissionFee" id="admissionFee" oninput="validateInput(this)">
                                </div>
                                <span id="admissionFee-error" class="error text-danger"></span>
                            </div>
                            <div class="form-group col-md-4 securityAmtDiv">
                                <label for="securityFee">Security Fee</label>
                                <div class="input-group">
                                    <input placeholder="Enter security fee" readonly class="form-control" type="text"
                                        name="securityFee" id="securityFee" oninput="validateInput(this)">
                                </div>
                                <span id="securityFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="medicalFee">Medical Fee</label>
                                <div class="input-group">
                                    <input placeholder="Enter medical fee" readonly class="form-control" type="text"
                                        name="medicalFee" id="medicalFee" oninput="validateInput(this)">
                                </div>
                                <span id="medicalFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="activitycharges">Activity Charges</label>
                                <div class="input-group">
                                    <input placeholder="Enter activity charges" readonly class="form-control" type="text"
                                        name="activity_charges_amount" id="activitychargesAmt" oninput="validateInput(this)">
                                </div>
                                <span id="activity_charges_amount-error" class="error text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row">
                            {{-- Row 1: Session Type + Hostel Student Type filter --}}
                            <div class="form-group col-md-6" id="hostel-sess-typeDiv">
                                <label for="sess_type">Session Type</label>
                                <div class="input-group">
                                    <select name="sess_type" id="sess_type" class="form-control" onchange="getFeeStructure()">
                                        {{-- <option value="">Select Type</option> --}}
                                        <option value="">Select Type</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Non-Regular">Non-Regular</option>
                                        <option value="Regular_outside">Regular Safari Campus</option>
                                        <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                                        <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                                        <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                                    </select>
                                </div>
                                <span id="sess_type-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-6" id="existing-hostellite-div">
                                <label for="hostel_student_type">Student Type</label>
                                <select name="hostel_student_type" id="hostel_student_type" class="form-control"
                                    onchange="getFeeStructure()">
                                    <option value="">Select Type</option>
                                    <option value="Existing">Existing</option>
                                    <option value="New_Admission">New Applicant</option>
                                </select>
                            </div>

                            {{-- Row 2+: amount / utility fields --}}
                            <div class="form-group col-md-4" id="hostelFeeDiv">
                                <label for="hostelFee">Hostel Fee</label>
                                <div class="input-group">
                                    <input placeholder="Enter hostel fee" readonly class="form-control" type="text" name="hostelFee"
                                        id="hostelFee" oninput="validateInput(this)">
                                </div>
                                <span id="hostelFee-error" class="error text-danger"></span>
                            </div>
                            <div class="form-group col-md-4" id="hostel_security_feeDiv">
                                <label for="hostel_security_fee">Hostel Security Fee</label>
                                <div class="input-group">
                                    <input placeholder="Enter hostel security fee" readonly class="form-control" type="text"
                                        name="hostel_security_fee" id="hostel_security_fee" oninput="validateInput(this)">
                                </div>
                                <span id="hostel_security_fee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="hostel_per_dayDiv">
                                <label for="hostel_per_day">Amount per day</label>
                                <div class="input-group">
                                    <input placeholder="From fee structure" readonly class="form-control" type="text"
                                        name="hostel_per_day" id="hostel_per_day">
                                </div>
                                <span id="hostel_per_day-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="daysNoDiv">
                                <label for="daysNo">Enter Days <span class="text-muted">(max 365)</span></label>
                                <div class="input-group">
                                    <input placeholder="Enter No of Days (1–365)" class="form-control" type="text" name="daysNo" id="daysNo"
                                        oninput="validateHostelDays(this)">
                                </div>
                                <span id="daysNo-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="refrigeratorFeeDiv">
                                <label for="refrigeratorFee">Room Refrigerator Fee</label>
                                <div class="input-group scg-utility-fee-group">
                                    <input placeholder="Enter refrigerator fee" class="form-control" type="text"
                                        name="refrigeratorFee" id="refrigeratorFee" oninput="validateInput(this)">
                                    <div class="input-group-append">
                                        <label class="input-group-text scg-utility-check" for="refrigeratorCheckBox"
                                            title="Include refrigerator in challan">
                                            <input type="checkbox" name="refrigeratorCheckBox" id="refrigeratorCheckBox">
                                            <span>Include</span>
                                        </label>
                                    </div>
                                </div>
                                <span id="refrigeratorFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="electricFeeDiv">
                                <label for="electricFee">Room Electric Heater Fee</label>
                                <div class="input-group scg-utility-fee-group">
                                    <input placeholder="Enter Electric Heater fee" class="form-control" type="text"
                                        name="electricFee" id="electricFee" oninput="validateInput(this)">
                                    <div class="input-group-append">
                                        <label class="input-group-text scg-utility-check" for="electricHeaterCheckBox"
                                            title="Include electric heater in challan">
                                            <input type="checkbox" name="electricHeaterCheckBox" id="electricHeaterCheckBox">
                                            <span>Include</span>
                                        </label>
                                    </div>
                                </div>
                                <span id="electricFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="conditionerFeeDiv">
                                <label for="conditionerFee">Air Conditioner Fee</label>
                                <div class="input-group scg-utility-fee-group">
                                    <input placeholder="Enter Conditioner fee" class="form-control" type="text"
                                        name="conditionerFee" id="conditionerFee" oninput="validateInput(this)">
                                    <div class="input-group-append">
                                        <label class="input-group-text scg-utility-check" for="conditionerCheckBox"
                                            title="Include air conditioner in challan">
                                            <input type="checkbox" name="conditionerCheckBox" id="conditionerCheckBox">
                                            <span>Include</span>
                                        </label>
                                    </div>
                                </div>
                                <span id="conditionerFee-error" class="error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4" id="hostelRemarksDiv">
                                <label for="hostelRemarks">Hostel Remarks <span class="text-muted">(optional)</span></label>
                                <textarea placeholder="Enter remarks (optional, max 200 characters)"
                                    class="form-control" name="hostelRemarks" id="hostelRemarks" rows="2"
                                    maxlength="200" oninput="validateRemarks(this)"></textarea>
                                <small id="hostelRemarks-error" class="form-text text-danger"></small>
                            </div>
                    </div>
                </div>

            </div>
            </div>

            <div class="scg-flags-box">
            <div class="scg-flags-row">
                <div class="form-group scg-flags-installments noOfInstallmentsDiv" id='noOfInstallmentsDiv'>
                    <label for="noOfInstallments">No of Installments</label>
                    <select class="form-control" id="noOfInstallments" name="noOfInstallments"
                        onchange='calculateAndDisplayInstallments(this)'>
                        <option value="">--- Select number of installments ---</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <span id="noOfInstallments-error" class="error text-danger"></span>
                </div>
                <div class="form-group scg-flags-options">
                    <label class="d-block">Options</label>
                    <div class="scg-options-inline">
                        <label class="scg-option-chip" for="studentInstallment" title="Installment Allowed">
                            <input class="form-check-input studentInstallment" type="checkbox" id="studentInstallment"
                                name="studentInstallment" value="YES">
                            <span>Installment Allowed</span>
                        </label>
                        <label class="scg-option-chip" for="manualFeeAdjustment" title="Fee Adjustment">
                            <input type="checkbox" id="manualFeeAdjustment" name="manualFeeAdjustment" value="YES">
                            <span>Fee Adjustment</span>
                        </label>
                        <label class="scg-option-chip" for="surplusAdjustment" title="Surplus Adjustment">
                            <input type="checkbox" id="surplusAdjustment" name="surplusAdjustment" value="YES">
                            <span>Surplus Adjustment</span>
                        </label>
                        <input type="hidden" name="surplus_apply_amount" id="surplus_apply_amount" value="">
                        <input type="hidden" name="surplus_ffa_id" id="surplus_ffa_id" value="">
                        <label class="scg-option-chip" for="roadmap_semester_structure" title="Road Map / Semester Fee Structure">
                            <input class="form-check-input roadmap_semester_Feestructure" type="checkbox"
                                id="roadmap_semester_structure" name="roadmap_semester_structure" value="YES">
                            <span>Road Map / Semester</span>
                        </label>
                    </div>
                    {{-- <label class="scg-option-chip" for="gymFee">
                        <input class="form-check-input gymFee" type="checkbox" id="gymFee" name="gymFee" value="YES">
                        <span>Gym Fee</span>
                    </label> --}}
                </div>
            </div>
            </div>

            <div id='installmentsDiv' class='p-1'>
            </div>

            <h2 id="successMsg" class="text-success"></h2>
            <div class="form-actions">
                <button type="submit" class="btn btn-generate-challan"
                    onclick="submitFormBtn(event)" id="submitBtn">
                    <i class="fa fa-file-text-o mr-1"></i> Generate Challan
                </button>

                <button type="button" id="clear" class="btn btn-clear-form"
                    onclick="clearFormAndContent()">
                    <i class="fa fa-undo mr-1"></i> Clear
                </button>

                <div class="btn-group" role="group" aria-label="Filter Challan" style='display:none;'
                    id='filterChallanBtn'>
                    <button type="button"
                        class="btn btn-filter-challan dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter mr-1"></i> Filter Challan
                    </button>

                    <div class="dropdown-menu">
                        <!-- <a class="dropdown-item" onclick="handleButtonClick('ALL')">All</a> -->
                        <a class="dropdown-item" onclick="handleButtonClick('UNPAID')">UNPAID</a>
                        <a class="dropdown-item" onclick="handleButtonClick('PAID')">PAID</a>
                        <a class="dropdown-item" onclick="handleButtonClick('CANCELLED')">CANCELLED</a>
                        <!-- <a class="dropdown-item" onclick="handleButtonClick('VERSION')">VERSION</a> -->
                    </div>
                </div>

                <div class="scg-bulk-actions" id="scgBulkActions">
                    <button type="button" class="btn btn-surplus-bulk" id="surplusAdjustBulkBtn"
                        onclick="openBulkSurplusAdjust()">
                        <i class="fa fa-adjust mr-1"></i> Surplus Adjust
                    </button>
                    <button type="button" class="btn btn-cancel-challan" id="cancelChallanBtn"
                        onclick="cancelChallans()">
                        <i class="fa fa-times-circle mr-1"></i> Cancel Challan
                    </button>
                </div>
            </div>
            <div id="surplusGenerateDisclaimer" class="scg-surplus-disclaimer" style="display:none;" role="alert">
                <i class="fa fa-info-circle mr-1"></i>
                <span id="surplusGenerateDisclaimerText"></span>
            </div>
            <div id="form-errors"></div>
        </form>

        <div id="showDataInTable" class="table-responsive scg-table-wrap"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="challanDetailsModal" tabindex="-1" role="dialog" aria-labelledby="challanDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="challanDetailsModalLabel">Confirmation of Challan Deletion</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">


                <form id="cancelChallanForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="challanIdInput" name="id" value="">
                    <input type="hidden" id="enrollmentNoInput" name="enrollment_no" value="">
                    <input type="hidden" id="scChallanStatus" name="scChallanStatus" value="">
                    <input type="hidden" id="scCheckInstallments" name="scCheckInstallments" value="">

                    <table class="table table-hover table-striped">
                        <thead id="challanModalMainHead">
                            <tr>
                                <th>Field Name</th>
                                <th>Actual Value</th>
                                <th>Edit Payable Values</th>
                            </tr>
                        </thead>
                        <tbody id="challanDetailsTableBody">
                            <!-- Dynamic rows will be appended here -->
                        </tbody>
                        <tfoot>
                            <tr id="challanModalTotalRow">
                                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                <td>
                                    <input type="text" class="form-control" id="totalEditable" name="totalEditable"
                                        readonly value="0">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
            <!-- Error Message Placeholder -->
            <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                Please correct the highlighted values. No value can exceed the original amount.
            </div>
            <!-- Modal Footer -->
            <div id='successMessage'></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="submitCancelChallan()"
                    id="deleteChallanBtn">Cancel Now</button>
            </div>
        </div>
    </div>
</div>

{{-- Surplus Adjustment (edit + bulk select): available surplus (>0), apply amount(s), live remaining --}}
<div class="modal fade" id="surplusAdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="surplusAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="surplusAdjModalDialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="surplusAdjustmentModalLabel">Surplus Adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="surplusAdjMsg" class="mb-2"></div>
                <p class="mb-2 text-muted small">
                    App ID: <strong id="surplusAdjAppIdLabel">—</strong>
                    &nbsp;|&nbsp; Available surplus from adjustment table (amount &gt; 0 only).
                </p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Available Surplus</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="surplusAdjTableBody">
                            <tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Edit / single challan --}}
                <div id="surplusAdjApplyBox" style="display:none;">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Available Surplus</label>
                            <input type="text" class="form-control" id="surplusAdjAvailable" readonly value="0">
                            <input type="hidden" id="surplusAdjSelectedFfaId" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Challan Amount</label>
                            <input type="text" class="form-control" id="surplusAdjChallanAmount" readonly value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="surplusAdjApplyAmount">Amount to apply on this challan</label>
                        <input type="text" class="form-control" id="surplusAdjApplyAmount"
                            onkeypress="return /[0-9.]/i.test(event.key)"
                            oninput="updateSurplusRemainingPreview()"
                            placeholder="e.g. 3000">
                        <small class="text-muted d-block">Cannot exceed Available Surplus or Challan Amount.</small>
                        <small id="surplusAdjApplyError" class="text-danger"></small>
                    </div>
                    <div class="form-group mb-0">
                        <label>Remaining surplus after apply</label>
                        <input type="text" class="form-control font-weight-bold" id="surplusAdjRemaining" readonly value="0">
                    </div>
                </div>

                {{-- Bulk: amounts per selected challan (Cancel Challan style multi-select) --}}
                <div id="surplusAdjBulkBox" style="display:none;">
                    <div class="form-row mb-2">
                        <div class="form-group col-md-6 mb-2">
                            <label>Available Surplus</label>
                            <input type="text" class="form-control" id="surplusAdjBulkAvailable" readonly value="0">
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label>Remaining after apply</label>
                            <input type="text" class="form-control font-weight-bold" id="surplusAdjBulkRemaining" readonly value="0">
                        </div>
                    </div>
                    <p class="mb-2 small text-muted" id="surplusAdjBulkCountLabel"></p>
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-sm scg-bulk-selected-table mb-0">
                            <thead>
                                <tr>
                                    <th>Challan No#</th>
                                    <th>Invoice Amount</th>
                                    <th>Apply Amount</th>
                                    <th>After Amount</th>
                                </tr>
                            </thead>
                            <tbody id="surplusAdjBulkChallanBody"></tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block">Total apply cannot exceed available surplus. Each amount cannot exceed that challan’s invoice.</small>
                    <small id="surplusAdjBulkError" class="text-danger d-block"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" id="surplusAdjCancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="surplusAdjConfirmBtn">Confirm Amount</button>
            </div>
        </div>
    </div>
</div>

{{-- Hostel Other Utilities + Fee Adjustment confirm (Manual Scholarship style amounts) --}}
<div class="modal fade" id="hostelUtilitiesFeeAdjModal" tabindex="-1" role="dialog" aria-labelledby="hostelUtilitiesFeeAdjModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hostelUtilitiesFeeAdjModalLabel">Fee Adjustment — Hostel Utilities</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Only checked utility amounts will update the challan (same as Manual Scholarship).
                </p>
                <div class="row text-center mb-2">
                    <div class="col-6 col-md-3 mb-2">
                        <small class="d-block text-muted">Refrigerator</small>
                        <strong id="huAdjFridge">0.00</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <small class="d-block text-muted">Air Conditioner</small>
                        <strong id="huAdjAc">0.00</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <small class="d-block text-muted">Electric Heater</small>
                        <strong id="huAdjHeater">0.00</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <small class="d-block text-muted">Invoice Amount</small>
                        <strong class="text-success" id="huAdjInvoice">0.00</strong>
                    </div>
                </div>
                <div id="huAdjError" class="text-danger small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="hostelUtilitiesFeeAdjConfirmBtn">
                    <i class="fa fa-check"></i> Confirm Update
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
<script>

    function setDefaultDates() {
        // Function to format the date to YYYY-MM-DD
        function formatDate(date) {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Get the current date
        let currentDate = new Date();

        // Set the Due Date as the current date - with null check
        const dueDateElement = document.getElementById('dueDate');
        if (dueDateElement) {
            dueDateElement.value = formatDate(currentDate);
        }

        // Calculate the Validate Date (current date + 1 week)
        let validateDate = new Date(currentDate);
        validateDate.setDate(currentDate.getDate() + 7); // Add 7 days

        // Set the Validate Date - with null check
        const validateDateElement = document.getElementById('validateDate');
        if (validateDateElement) {
            validateDateElement.value = formatDate(validateDate);
        }
    }


    window.onload = setDefaultDates;

    $(document).ready(function () {

        hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);
        $('#invoiceAmountOpt').hide();
        $('#scholarshipsAmountOpt').hide();

        function syncOptionChips() {
            $('.scg-option-chip').each(function () {
                $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
            });
        }
        syncOptionChips();
        $(document).on('change', '.scg-option-chip input[type="checkbox"]', syncOptionChips);

        // Edit challan: Surplus Adjustment — show available (>0), apply amount, remaining preview
        $(document).on('change', '#surplusAdjustment', function () {
            if (!$(this).is(':checked')) {
                $('#surplus_apply_amount').val('');
                $('#surplus_ffa_id').val('');
                hideSurplusGenerateDisclaimer();
                return;
            }
            if ($('#verifyEdit').val() != '1') {
                return; // new generate: keep existing submit-time procedure only
            }
            window.isBulkSurplusAdjust = false;
            window.bulkSurplusRows = null;
            openSurplusAdjustmentModal();
        });

        $('#surplusAdjConfirmBtn').on('click', function () {
            if (window.isBulkSurplusAdjust) {
                submitBulkSurplusAdjust();
            } else {
                confirmSurplusApplyAmount();
            }
        });

        // Hostel utilities Fee Adjustment confirm modal
        $('#hostelUtilitiesFeeAdjConfirmBtn').on('click', function () {
            confirmHostelUtilitiesFeeAdjAndSubmit();
        });

        $(document).on('change', '#manualFeeAdjustment', function () {
            if ($('#verifyEdit').val() == '1' && $('#feeType').val() === 'Hostel_other_utilities' && $(this).is(':checked')) {
                removeInputReadOnly('refrigeratorFee');
                removeInputReadOnly('electricFee');
                removeInputReadOnly('conditionerFee');
            }
        });

        $('#surplusAdjCancelBtn').on('click', function () {
            if (window.isBulkSurplusAdjust) {
                clearBulkSurplusState();
                return;
            }
            $('#surplusAdjustment').prop('checked', false);
            $('#surplus_apply_amount').val('');
            $('#surplus_ffa_id').val('');
            hideSurplusGenerateDisclaimer();
            $('.scg-option-chip').each(function () {
                $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
            });
        });

        // X / dismiss without confirm: clear surplus only if amount was not confirmed (edit mode)
        $('#surplusAdjustmentModal').on('hidden.bs.modal', function () {
            if (window.isBulkSurplusAdjust) {
                clearBulkSurplusState();
                return;
            }
            if (!$('#surplus_apply_amount').val()) {
                $('#surplusAdjustment').prop('checked', false);
                $('#surplus_ffa_id').val('');
                $('#surplus_apply_amount').val('');
                hideSurplusGenerateDisclaimer();
                $('.scg-option-chip').each(function () {
                    $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
                });
            }
        });

        $(document).on('change', 'input[name="surplusAdjSelectRow"]', function () {
            const available = parseFloat($(this).data('surplus')) || 0;
            const ffaId = $(this).val();
            $('#surplusAdjSelectedFfaId').val(ffaId);
            $('#surplusAdjAvailable').val(available);
            $('#surplusAdjBulkAvailable').val(available);
            if (window.isBulkSurplusAdjust) {
                $('#surplusAdjApplyBox').hide();
                $('#surplusAdjBulkBox').show();
                updateBulkSurplusRemainingPreview();
            } else {
                $('#surplusAdjBulkBox').hide();
                $('#surplusAdjChallanAmount').val(getChallanAmountForSurplus());
                $('#surplusAdjApplyAmount').val('');
                $('#surplusAdjApplyError').text('');
                $('#surplusAdjApplyBox').show();
                updateSurplusRemainingPreview();
            }
        });

        $(document).on('input', '.surplus-bulk-apply-amt', function () {
            updateBulkSurplusRemainingPreview();
        });

        $('#student_type').change(function () {
            if ($(this).val() != 'Existing') {
                $('.admissionFeeDiv').removeClass('d-none');
                $('.securityAmtDiv').removeClass('d-none');
            } else {
                $('.admissionFeeDiv').addClass('d-none');
                $('#admissionFee').val('');
                $('.securityAmtDiv').addClass('d-none');
                $('#securityFee').val('');
            }

            // Student Type change → reset Fee Type (user must re-select)
            $('#feeType').val('');
            $('#fee_type-error').text('');
            $('.credit_hr_div, .credit_hr_summer_div').addClass('d-none');
            $('#degree_level').off('mousedown.scgDegreeLock');
            $('#full_semester_dev').hide();
            hideElementsById([
                'hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv',
                'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv',
                'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div'
            ]);
            applyFeeTypeOptionLocks();
            updateProcessingFeeOptionAvailability();
        });
        $('#roadmap_semester_structure').on('change', function () {
            if ($(this).is(':checked')) {
                $('#roadmap-sess, #semester').prop('disabled', false); // enable
            } else {
                $('#roadmap-sess, #semester').prop('disabled', true); // disable
                $('#roadmap-sess, #semester').val(''); // clear values
            }
            // Fee structure API only when Fee Type is already selected
            if ($('#feeType').val()) {
                getFeeStructure();
            }
        });

    });
    //

    const globalDataMap = new Map();

    // Full challan list from last search/filter (before table-session filter)
    let allStudentChallans = [];
    let currentTableStatusFilter = null;

    /**
     * Latest session = highest sc_session_id in the loaded challans.
     */
    function getLatestSessionMeta(challans) {
        let maxId = null;
        let code = '';
        (challans || []).forEach(function (c) {
            const sid = Number(c.sc_session_id);
            if (!Number.isNaN(sid) && (maxId === null || sid > maxId)) {
                maxId = sid;
                code = c.sess_code || ('Session ' + sid);
            }
        });
        return { id: maxId, code: code };
    }

    function filterChallansForTable(challans) {
        const mode = $('#tableSessionFilter').val() || 'latest';
        const list = Array.isArray(challans) ? challans : [];
        if (mode === 'all') {
            return list;
        }
        const latest = getLatestSessionMeta(list);
        if (latest.id === null) {
            return list;
        }
        return list.filter(function (c) {
            return Number(c.sc_session_id) === latest.id;
        });
    }

    function updateTableSessionHint(challans) {
        const el = document.getElementById('tableSessionHint');
        if (!el) {
            return;
        }
        const list = Array.isArray(challans) ? challans : [];
        if (!list.length) {
            el.textContent = '';
            return;
        }
        const latest = getLatestSessionMeta(list);
        const mode = $('#tableSessionFilter').val() || 'latest';
        const filteredCount = filterChallansForTable(list).length;
        if (mode === 'all') {
            el.textContent = 'Showing all ' + list.length + ' challan(s)'
                + (latest.code ? ' · Latest session: ' + latest.code : '');
        } else {
            el.textContent = latest.code
                ? ('Showing latest session: ' + latest.code + ' (' + filteredCount + ' row(s))')
                : ('Showing latest session (' + filteredCount + ' row(s))');
        }
    }

    /**
     * Store full list and refresh table using Latest / All session filter.
     * Does not change generate Session (sessionId) used for challan creation.
     */
    function setStudentChallansForTable(challans, status) {
        allStudentChallans = Array.isArray(challans) ? challans : [];
        currentTableStatusFilter = (typeof status === 'undefined') ? null : status;
        applyTableSessionFilter();
    }

    function applyTableSessionFilter() {
        const filtered = filterChallansForTable(allStudentChallans);
        updateTableSessionHint(allStudentChallans);
        generateTable(filtered, currentTableStatusFilter);
    }

    function resetFormExceptButton() {
        const form = document.getElementById('singleChallanForm');
        const searchButton = document.getElementById('studentEnrollment');
        // Never wipe CSRF / profile ids — only clear edit flags via exitEditModeAndResetParams
        const preserveHiddenIds = {
            _token: true,
            st_school_id: true,
            st_dept_id: true,
            st_prog_id: true,
            st_aai_id: true
        };

        // Loop through form elements and reset them except for the enrollment search field
        Array.from(form.elements).forEach(element => {
            if (element === searchButton) {
                return;
            }
            if (element.type === 'hidden') {
                if (!preserveHiddenIds[element.id] && element.name !== '_token') {
                    // Clear edit/surplus flags etc.; keep csrf + student profile ids
                    if (['verifyEdit', 'edit_id', 'editChallan', 'refundEdit', 'surplus_apply_amount', 'surplus_ffa_id', 'scCheckInstallments', 'scChallanStatus'].indexOf(element.id) !== -1
                        || ['verifyEdit', 'edit_id', 'editChallan', 'refundEdit'].indexOf(element.name) !== -1) {
                        element.value = '';
                    }
                }
                return;
            }
            switch (element.type) {
                case 'text':
                case 'password':
                case 'textarea':
                case 'select-one':
                case 'select-multiple':
                case 'date':
                case 'number':
                    element.value = '';
                    break;
                case 'checkbox':
                case 'radio':
                    element.checked = false;
                    break;
                case 'file':
                    element.value = '';
                    break;
                default:
                    if (element.tagName === 'SELECT' || element.tagName === 'TEXTAREA' || element.tagName === 'INPUT') {
                        element.value = '';
                    }
                    break;
            }
        });
    }

    /**
     * After Edit: Search must leave edit mode and wipe challan params
     * so leftover tuition/utilities/remarks do not stick on a new fee type.
     */
    function exitEditModeAndResetParams() {
        // Invalidate delayed applyHostelUtilities / date re-apply from set_fields_by_hostel_fee_type
        hostelEditApplyToken++;
        window._scgFeeStructureReqSeq = (window._scgFeeStructureReqSeq || 0) + 1;
        editHostelUtilitiesRow = null;

        $('#verifyEdit').val('');
        $('#edit_id').val('');
        $('#editChallan').val('');
        $('#refundEdit').val('');
        $('#scCheckInstallments').val('');
        $('#surplus_apply_amount').val('');
        $('#surplus_ffa_id').val('');

        [
            'tuitionFee', 'admissionFee', 'securityFee', 'medicalFee', 'activitychargesAmt',
            'hostelFee', 'hostel_security_fee', 'hostel_per_day', 'daysNo',
            'refrigeratorFee', 'electricFee', 'conditionerFee', 'hostelRemarks',
            'dueDate', 'validateDate', 'creditHr', 'studentInvoiceamount', 'scholarshipsAmount',
            'sess_type', 'hostel_student_type', 'noOfInstallments'
        ].forEach(function (id) {
            $('#' + id).val('');
        });
        $('#refrigeratorCheckBox, #electricHeaterCheckBox, #conditionerCheckBox').prop('checked', false);
        $('#studentInstallment, #manualFeeAdjustment, #surplusAdjustment, #roadmap_semester_structure').prop('checked', false);

        hideElementsById([
            'hostelFeeDiv', 'hostel_per_dayDiv', 'hostel_security_feeDiv',
            'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv',
            'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div',
            'roadmap-sess-Div', 'semester-Div'
        ]);
        $('#full_semester_dev').hide();
        $('.credit_hr_div').addClass('d-none');
        $('.credit_hr_summer_div').addClass('d-none');
        $('#degree_level').off('mousedown.scgDegreeLock');
        $('#invoiceAmountOpt').hide();
        $('#scholarshipsAmountOpt').hide();
        hideSurplusGenerateDisclaimer();
        clearFeeStructureCaps();
        setSubmitButtonMode(false);
    }

    function validateInput(input) {
        // Allow only numbers and one decimal point
        input.value = input.value.replace(/[^0-9.]/g, ''); // Replace anything not a digit or a dot
        input.value = input.value.replace(/^(\d*\.?)|(\d*\.\d{0,2}).*/g, '$1$2'); // Ensure only one dot and at most two decimal places
        // Edit mode: cannot exceed fee structure amount
        enforceFeeStructureCapOnEdit(input);
    }

    /** Hostel day-wise: days must be 1–365 (integers only) */
    function validateHostelDays(input) {
        input.value = String(input.value || '').replace(/[^0-9]/g, '');
        const err = document.getElementById('daysNo-error');
        if (!input.value) {
            if (err) err.textContent = '';
            input.classList.remove('is-invalid');
            return true;
        }
        const days = parseInt(input.value, 10);
        if (isNaN(days) || days < 1) {
            if (err) err.textContent = 'Days must be at least 1.';
            input.classList.add('is-invalid');
            return false;
        }
        if (days > 365) {
            input.value = '365';
            if (err) err.textContent = 'Days cannot be more than 365.';
            input.classList.add('is-invalid');
            setTimeout(function () {
                if (err) err.textContent = '';
                input.classList.remove('is-invalid');
            }, 3000);
            return false;
        }
        if (err) err.textContent = '';
        input.classList.remove('is-invalid');
        return true;
    }

    // Max allowed fee amounts from fee structure (edit Fee Adjustment)
    let feeStructureCaps = {};

    function feeCapErrorElementId(fieldId) {
        if (fieldId === 'activitychargesAmt') {
            return 'activity_charges_amount-error';
        }
        return fieldId + '-error';
    }

    function setFeeStructureCapsFromResponse(rowData) {
        if (!rowData) {
            return;
        }
        feeStructureCaps = Object.assign({}, feeStructureCaps, {
            tuitionFee: parseFloat(rowData.ffs_tutionfee_amt) || 0,
            admissionFee: parseFloat(rowData.ffs_admission_amt) || 0,
            securityFee: parseFloat(rowData.ffs_security_amt) || 0,
            medicalFee: parseFloat(rowData.ffs_medical_amt) || 0,
            activitychargesAmt: parseFloat(rowData.ffs_activity_amt) || 0,
            hostelFee: parseFloat(rowData.fhfs_amount_per_semester) || 0,
            hostel_security_fee: parseFloat(rowData.fhfs_hostel_security_fee) || 0,
            hostel_per_day: parseFloat(rowData.fhfs_amount_per_day) || 0,
            refrigeratorFee: parseFloat(rowData.fhfs_refrigerator_fee) || 0,
            electricFee: parseFloat(rowData.fhfs_heater_fee) || 0,
            conditionerFee: parseFloat(rowData.fhfs_ac_fee) || 0
        });
    }

    function setFeeStructureCapsFromEditRow(rowData) {
        if (!rowData) {
            return;
        }
        const fromRow = {
            tuitionFee: parseFloat(rowData.sc_tuition_fee_amnt) || 0,
            admissionFee: parseFloat(rowData.ffs_admission_amt) || 0,
            securityFee: parseFloat(rowData.ffs_security_amt) || 0,
            medicalFee: parseFloat(rowData.ffs_medical_amt) || 0,
            activitychargesAmt: parseFloat(rowData.sc_activity_amt) || 0,
            hostelFee: parseFloat(rowData.invoiceamount) || parseFloat(rowData.ffs_hostel_amt) || 0,
            hostel_security_fee: parseFloat(rowData.hostel_security_fee) || 0
        };
        Object.keys(fromRow).forEach(function (key) {
            feeStructureCaps[key] = Math.max(parseFloat(feeStructureCaps[key]) || 0, fromRow[key] || 0);
        });
    }

    function clearFeeStructureCapErrors() {
        [
            'tuitionFee', 'admissionFee', 'securityFee', 'medicalFee', 'activitychargesAmt',
            'hostelFee', 'hostel_security_fee', 'hostel_per_day', 'refrigeratorFee', 'electricFee', 'conditionerFee'
        ].forEach(function (id) {
            const err = document.getElementById(feeCapErrorElementId(id));
            if (err) {
                err.textContent = '';
            }
        });
    }

    function clearFeeStructureCaps() {
        feeStructureCaps = {};
        clearFeeStructureCapErrors();
    }

    function showFeeCapError(fieldId, maxAmount) {
        const err = document.getElementById(feeCapErrorElementId(fieldId));
        if (err) {
            err.textContent = 'Cannot exceed fee structure amount (' + maxAmount + ').';
        }
    }

    function clearFeeCapError(fieldId) {
        const err = document.getElementById(feeCapErrorElementId(fieldId));
        if (err) {
            err.textContent = '';
        }
    }

    /** Edit only: clamp value to fee-structure max and show message immediately */
    function enforceFeeStructureCapOnEdit(input) {
        if (!input || !input.id) {
            return true;
        }
        if ($('#verifyEdit').val() != '1') {
            return true;
        }
        // Hostel utilities Fee Adjustment: allow manual amounts (Manual Scholarship)
        if ($('#feeType').val() === 'Hostel_other_utilities' && $('#manualFeeAdjustment').is(':checked')) {
            return true;
        }
        if (!(input.id in feeStructureCaps)) {
            return true;
        }
        const maxNum = parseFloat(feeStructureCaps[input.id]);
        if (isNaN(maxNum) || maxNum < 0) {
            return true;
        }
        // No structure amount known yet — skip
        if (maxNum === 0 && (input.value === '' || input.value === '0')) {
            return true;
        }
        if (input.value === '' || input.value === '.') {
            clearFeeCapError(input.id);
            return true;
        }
        const val = parseFloat(input.value);
        if (isNaN(val)) {
            return true;
        }
        if (val > maxNum) {
            input.value = String(maxNum);
            showFeeCapError(input.id, maxNum);
            return false;
        }
        clearFeeCapError(input.id);
        return true;
    }

    /** Returns false if any edit fee field exceeds fee structure */
    function validateFeeFieldsAgainstStructureOnEdit() {
        if ($('#verifyEdit').val() != '1') {
            return true;
        }
        let ok = true;
        Object.keys(feeStructureCaps).forEach(function (fieldId) {
            const el = document.getElementById(fieldId);
            if (!el || !$(el).is(':visible')) {
                return;
            }
            if (!enforceFeeStructureCapOnEdit(el)) {
                ok = false;
            }
        });
        return ok;
    }

    // Function to generate HTML table code

    function generateTable(data, status = null) {
        // console.log("Status passed to function:", status);
        // alert(status);
        let staffUrl = "{{ config('app.staff_url') }}";
        // console.log(status);
        let showDataInTable = document.getElementById('showDataInTable');
        showDataInTable.innerHTML = ''; // Clear previous content
        // Define the base URL globally
        const generateChallanUrl = `${staffUrl}/chalanfin.php?challan_id=`;

        if (Array.isArray(data) && data.length > 0) {
            globalDataMap.set('data', data);
            window.scgChallanRowById = window.scgChallanRowById || {};
            let actionHeading = '';
            if (status != 'VERSION') {
                actionHeading = `<th class="text-center">Action</th><th class="scg-check-th"><input type="checkbox" id="selectAll" title="Select all"></th>`;
            }
            let createHtml = `
            <table class="table table-bordered table-hover" id='mydatatable'>
                <thead>
                    <tr>

                        <th>Challan No#</th>
                        <th>Enrollment No</th>
                        <th>Session ID</th>
                        <th>Student Type</th>
                        <th>Challan Type</th>
                        <th>Due Date</th>
                        <th>Valid Date</th>
                        <th>Tuition Fee</th>
                        <th>Admission Fee</th>
                        <th>Security Fee</th>
                        <th>Medical Fee</th>
                        <th>Activity Charges</th>
                        <th>Invoice Amount</th>
                        <th>Challan Status</th>
                        `+ actionHeading + `

                    </tr>
                </thead>
                <tbody>
                    ${data.map(item => {
                let badgeColor = 'secondary'; // Default color for undefined status
                if (item.sc_challan_status === 'PAID') {
                    badgeColor = 'success';
                } else if (item.sc_challan_status === 'UNPAID') {
                    badgeColor = 'danger';
                } else if (status === 'CANCELLED') {
                    badgeColor = 'warning';
                }

                let rowClass = '';
                let disableCheckbox = '';
                let textDecoration = '';


                const rowStyle = item.sc_session_id < 163 ? 'background-color: #e0f7fa;' : '';
                const isOldSession = item.sc_session_id < 160 && item.sc_challan_type != 'Full_semester'; // Boolean for session condition

                if (status === 'CANCELLED' && item.challan_cancelled_by) {
                    rowClass = 'table-danger'; // Change row color to indicate cancellation
                    disableCheckbox = 'disabled'; // Disable the checkbox
                    textDecoration = 'text-decoration: line-through;'; // Apply line-through decoration
                }




                // Function to check if value is null or undefined and return 'N/A'
                const checkValue = value => (value === null || value === undefined) ? 'N/A' : value;

                let challanNo = status === 'CANCELLED' && item.challan_cancelled_by
                    ? item.sc_id
                    : item.id;
                window.scgChallanRowById[String(challanNo)] = item;
                let concateActions = '';
                let cancelButton = '';
                // if (item.sc_challan_status === 'UNPAID') {
                cancelButton = `<button class="btn btn-outline-danger btn-sm cancel-row" data-challan-id="${checkValue(challanNo)}">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>`;
                // }
                // Default Installment Button Name
                let installmentBtnName = item.sc_challan_status === 'CANCEL FOR REISSUANCE'
                    ? "Active Now"
                    : "Edit";
                // Fetch the user's auth group ID from PHP
                let userAuthGroupId = <?php echo json_encode(auth()->user()->group_id); ?>;
                let hasEditPermission = userAuthGroupId == 644;
                // Check if this is a hostel fee challan
                const isHostelFee = item.sc_hostel_fee_type &&
                    ['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(item.sc_hostel_fee_type);

                // Check if this is a Fee Refund or Hostel Fee Refund challan
                const isRefundChallan = item.sc_challan_type === 'Fee_Refund' || item.sc_challan_type === 'Hostel_Fee_Refund';
                // Processing / Summer credit: no safe edit path (create-only procedures)
                const editNotSupported = item.sc_challan_type === 'Processing_Fee'
                    || item.sc_challan_type === 'Credit_hours_Summer_Course';
                let refundBadgeHtml = '';
                if (isRefundChallan) {
                    rowClass = (rowClass + ' scg-refund-row').trim();
                    refundBadgeHtml = ' <span class="badge scg-refund-badge" title="' +
                        (item.sc_challan_type === 'Hostel_Fee_Refund' ? 'Hostel Fee Refund' : 'Fee Refund') +
                        '">REFUND</span>';
                }

                // Challan Type badge label + color class
                let challanTypeLabel = checkValue(item.sc_challan_type);
                let challanTypeClass = 'scg-ctype-other';
                const ctype = item.sc_challan_type || '';
                const htype = item.sc_hostel_fee_type || '';
                if (ctype === 'Full_semester') {
                    challanTypeLabel = 'Full Semester';
                    challanTypeClass = 'scg-ctype-full';
                } else if (ctype === 'Short/Summer_Course') {
                    challanTypeLabel = 'Short/Summer Course';
                    challanTypeClass = 'scg-ctype-summer';
                } else if (ctype === 'Hostel_Fee') {
                    challanTypeClass = 'scg-ctype-hostel';
                    if (htype === 'Hostel_Full_semster') {
                        challanTypeLabel = 'Hostel Full Semester';
                    } else if (htype === 'Hostel_day_wise') {
                        challanTypeLabel = 'Hostel Day Wise';
                    } else if (htype === 'Hostel_other_utilities') {
                        challanTypeLabel = 'Hostel Other Utilities';
                    } else {
                        challanTypeLabel = 'Hostel Fee';
                    }
                } else if (ctype === 'Fee_Refund') {
                    challanTypeLabel = 'Fee Refund';
                    challanTypeClass = 'scg-ctype-refund';
                } else if (ctype === 'Hostel_Fee_Refund') {
                    challanTypeLabel = 'Hostel Fee Refund';
                    challanTypeClass = 'scg-ctype-refund';
                } else if (ctype === 'Credit_hours') {
                    challanTypeLabel = 'Credit Hours';
                    challanTypeClass = 'scg-ctype-credit';
                } else if (ctype === 'Credit_hours_Summer_Course') {
                    challanTypeLabel = 'Credit Hours Summer';
                    challanTypeClass = 'scg-ctype-summer';
                } else if (ctype === 'Processing_Fee') {
                    challanTypeLabel = 'Processing Fee';
                    challanTypeClass = 'scg-ctype-processing';
                }
                const challanTypeBadgeHtml = `<span class="badge scg-challan-type-badge ${challanTypeClass}">${challanTypeLabel}</span>`;

                const canShowCancelBtn = !(disableCheckbox || item.sc_challan_status === 'CANCEL FOR REISSUANCE');
                const rowSelectCheckbox = canShowCancelBtn
                    ? `<input type="checkbox" class="challan-checkbox" data-id="${checkValue(challanNo)}" ${disableCheckbox}>`
                    : '';

                if (status != 'VERSION') {
                    if (item.sc_challan_status === 'PAID') {
                        // PAID status - Edit button disabled with reduced opacity
                        previewBtn = `<a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="fa fa-eye"></i> Preview
                                                    </a>`;
                        concateActions = `
                                    <td class="scg-action-td" style="${textDecoration}">
                                        <div class="scg-action-stack">
                                            <button class="btn btn-outline-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed; pointer-events: none;">
                                                <i class="fa fa-edit"></i> ${installmentBtnName}
                                            </button>
                                            ${previewBtn}
                                            ${canShowCancelBtn ? cancelButton : ''}
                                        </div>
                                    </td>
                                    <td class="scg-check-td">
                                        ${rowSelectCheckbox}
                                    </td>`;
                    } else if (item.sc_challan_status !== 'PAID' && status !== 'CANCELLED' && !isOldSession) {
                        // Normal Case - Non-Paid Challans (incl. UNPAID refunds)
                        const editBtnHtml = editNotSupported
                            ? `<button class="btn btn-outline-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;" title="Edit not available for this challan type">
                                                <i class="fa fa-edit"></i> ${installmentBtnName}
                                            </button>`
                            : `<button class="btn btn-outline-primary btn-sm edit-row" data-challan-id="${checkValue(challanNo)}" ${disableCheckbox}>
                                                <i class="fa fa-edit"></i> ${installmentBtnName}
                                            </button>`;
                        concateActions = `
                                    <td class="scg-action-td" style="${textDecoration}">
                                        <div class="scg-action-stack">
                                            ${editBtnHtml}
                                            <a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="fa fa-eye"></i> Preview
                                            </a>
                                            ${canShowCancelBtn ? cancelButton : ''}
                                        </div>
                                    </td>
                                    <td class="scg-check-td">
                                        ${rowSelectCheckbox}
                                    </td>`;
                    } else {
                        // Only Preview Button
                        if (status != 'CANCELLED' && status != 'VERSION') {
                            previewBtn = `<a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i> Preview
                                            </a>`;
                        } else {
                            previewBtn = `<a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="fa fa-eye"></i> Preview
                                                    </a>`;
                        }
                        const elseCancel = !isOldSession && canShowCancelBtn ? cancelButton : '';
                        const elseCheckbox = !isOldSession && canShowCancelBtn ? rowSelectCheckbox : '';
                        concateActions = `
                                    <td class="scg-action-td" style="${textDecoration}">
                                        <div class="scg-action-stack">
                                            ${previewBtn}
                                            ${elseCancel}
                                        </div>
                                    </td>
                                    <td class="scg-check-td">
                                        ${elseCheckbox}
                                    </td>`;
                    }
                }
                return `
                           <tr class="${rowClass}" style="${rowStyle}">

                                <td style="${textDecoration}">${checkValue(challanNo)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_enrollment_no)}</td>
                                <td style="${textDecoration}">${checkValue(item.sess_code)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_student_status)}</td>
                                <td style="${textDecoration}">${challanTypeBadgeHtml}</td>
                                 <td>${formatDateTime(item.sc_due_date)}</td>
                                <td>${formatDateTime(item.sc_valid_date)}</td>

                                <td style="${textDecoration}">${checkValue(item.sc_installment_tuition_fee_amnt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_admission_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_security_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_medical_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_activity_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.invoiceamount)}</td>
                                <td style="${textDecoration}"><span class="badge badge-${badgeColor}">${checkValue(item.sc_challan_status)}</span>${refundBadgeHtml}</td>
                                `+ concateActions + `
                            </tr>
                        `;
            }).join('')}
                </tbody>
            </table>
        `;

            showDataInTable.innerHTML = createHtml;
            $('#mydatatable').DataTable({
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"scg-dt-top"<"scg-dt-left"l><"scg-dt-right"f>>rtip'
            });


            // Add event listeners to dynamically created elements
            let editButtons = document.querySelectorAll('.edit-row');
            editButtons.forEach(button => {
                button.addEventListener('click', editRec);
            });

            let checkboxes = document.querySelectorAll('.challan-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleCancelButton);
            });

            // Add event listener only if element exists
            const selectAllElement = document.getElementById('selectAll');
            if (selectAllElement) {
                selectAllElement.addEventListener('change', toggleSelectAll);
            }

            toggleCancelButton();

            $('#challanDetailsModal').off('hidden.bs.modal.bulkCancel').on('hidden.bs.modal.bulkCancel', function () {
                window.bulkCancelRows = null;
                window.isBulkCancel = false;
                $('#deleteChallanBtn').prop('disabled', false).html('Cancel Now');
                $('#challanModalTotalRow').show();
                $('#challanModalMainHead').show();
            });
        } else {
            showDataInTable.innerHTML = '<p>No data available</p>';
            toggleCancelButton();
        }
    }


    function formatDateTime(dateString) {
        const date = new Date(dateString); // Parse the input string into a Date object

        // Check if the date is valid
        if (isNaN(date.getTime())) {
            return "Invalid Date"; // Return a fallback if the date is invalid
        }

        const day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        const month = monthNames[date.getMonth()]; // Get the month name
        const year = date.getFullYear(); // Get the 4-digit year

        return `${day} ${month} ${year}`; // Format as "DD Month YYYY"
    }




    function handleButtonClick(status) {
        // Construct the URL with query parameters
        var enrollmentNo = $('#studentEnrollment').val();
        var enrollmentNo = $('.enrollment-appid').text();
        if (!enrollmentNo) {
            alert('Please enter the enrollment number.');
            return false; // Exit the function
        }

        var successMsg = $('#successMsg');
        const filterReqSeq = ++window._scgSearchReqSeq;
        $('#filterChallanBtn').prop('disabled', true);

        const url = `/finance/student-filter-challans?status=${encodeURIComponent(status)}&enrollmentNo=${encodeURIComponent(enrollmentNo)}`;

        // Fetch data from the server
        fetch(url, {
            method: 'GET', // Use GET method for fetching data
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (filterReqSeq !== window._scgSearchReqSeq) {
                    return;
                }
                if (data.success) {
                    // Keep status filter data, then apply Latest / All session filter on table
                    setStudentChallansForTable(data.all_data, status);

                    successMsg.html(createAlert('success', data.message));
                } else {
                    // Handle the case where the request was not successful
                    successMsg.html(createAlert('danger', data.message));
                }
            })
            .catch(error => {
                if (filterReqSeq !== window._scgSearchReqSeq) {
                    return;
                }
                console.error('Error:', error);
                successMsg.html(createAlert('danger', 'An error occurred while processing your request.'));
            })
            .finally(function () {
                if (filterReqSeq === window._scgSearchReqSeq) {
                    $('#filterChallanBtn').prop('disabled', false);
                }
            });
    }




    function toggleSelectAll() {
        let checkboxes = document.querySelectorAll('.challan-checkbox:not(:disabled)');
        let isChecked = this.checked;
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        toggleCancelButton();
    }

    function toggleCancelButton() {
        let selectedCheckboxes = document.querySelectorAll('.challan-checkbox:checked');
        const bulkWrap = document.getElementById('scgBulkActions');
        if (!bulkWrap) {
            return;
        }
        bulkWrap.style.display = selectedCheckboxes.length > 0 ? 'inline-flex' : 'none';
    }



    function cancelChallans() {
        var successMsg = $('#successMsg');
        successMsg.html('');

        let selectedCheckboxes = document.querySelectorAll('.challan-checkbox:checked');
        if (!selectedCheckboxes.length) {
            alert('No challans selected');
            return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(cb => String(cb.dataset.id));
        const allData = globalDataMap.get('data') || [];
        const selectedRows = allData.filter(item => {
            const challanId = String(item.id);
            return selectedIds.includes(challanId);
        });

        if (!selectedRows.length) {
            alert('Selected challan data not found. Please search again.');
            return;
        }

        openBulkCancelModal(selectedRows);
    }

    function isHostelSurplusBlockedRow(row) {
        const hostelTypes = ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'];
        const feeType = row.sc_challan_type || '';
        const hostelFeeType = row.sc_hostel_fee_type || '';
        return hostelTypes.includes(feeType) || hostelTypes.includes(hostelFeeType);
    }

    function clearBulkSurplusState() {
        window.isBulkSurplusAdjust = false;
        window.bulkSurplusRows = null;
        $('#surplusAdjBulkBox').hide();
        $('#surplusAdjBulkChallanBody').empty();
        $('#surplusAdjBulkError').text('');
        $('#surplusAdjConfirmBtn').prop('disabled', false).text('Confirm Amount');
        $('#surplusAdjustmentModalLabel').text('Surplus Adjustment');
    }

    /** Multi-select Surplus Adjust (same checkboxes as Cancel Challan) — applies immediately */
    function openBulkSurplusAdjust() {
        let selectedCheckboxes = document.querySelectorAll('.challan-checkbox:checked');
        if (!selectedCheckboxes.length) {
            alert('No challans selected');
            return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(cb => String(cb.dataset.id));
        const allData = globalDataMap.get('data') || [];
        const selectedRows = allData.filter(item => selectedIds.includes(String(item.id)));

        if (!selectedRows.length) {
            alert('Selected challan data not found. Please search again.');
            return;
        }

        const eligible = [];
        const skipped = [];
        selectedRows.forEach(function (row) {
            const status = (row.sc_challan_status || '').toUpperCase();
            const invoice = parseFloat(row.invoiceamount) || 0;
            if (status !== 'UNPAID') {
                skipped.push((row.id || '?') + ' (not UNPAID)');
                return;
            }
            if (isHostelSurplusBlockedRow(row)) {
                skipped.push((row.id || '?') + ' (hostel — surplus not allowed)');
                return;
            }
            if (invoice <= 0) {
                skipped.push((row.id || '?') + ' (invoice 0)');
                return;
            }
            eligible.push(row);
        });

        if (!eligible.length) {
            alert('No eligible UNPAID challans for Surplus Adjust.\n' + (skipped.length ? skipped.join('\n') : ''));
            return;
        }
        if (skipped.length) {
            alert('Some selected challans were skipped:\n' + skipped.join('\n') + '\n\nContinuing with ' + eligible.length + ' eligible challan(s).');
        }

        window.isBulkSurplusAdjust = true;
        window.bulkSurplusRows = eligible;
        openSurplusAdjustmentModal();
    }

    function openBulkCancelModal(selectedRows) {
        window.bulkCancelRows = selectedRows;
        window.isBulkCancel = true;

        document.getElementById('challanIdInput').value = '';
        document.getElementById('enrollmentNoInput').value = '';
        document.getElementById('scChallanStatus').value = '';
        document.getElementById('scCheckInstallments').value = '';

        const tableBody = document.getElementById('challanDetailsTableBody');
        const selectedRowsHtml = selectedRows.map(row => {
            const challanNo = row.id ?? 'N/A';
            const amount = (row.invoiceamount === null || row.invoiceamount === undefined) ? 'N/A' : row.invoiceamount;
            const dueDate = formatDateTime(row.sc_due_date);
            return `
                <tr>
                    <td><strong>${challanNo}</strong></td>
                    <td>${amount}</td>
                    <td>${dueDate}</td>
                </tr>`;
        }).join('');

        tableBody.innerHTML = `
        <tr>
            <td colspan="3" class="p-0 border-0">
                <div class="p-3">
                    <div class="mb-2">
                        <strong>${selectedRows.length} challan(s) selected for cancellation</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm scg-bulk-selected-table mb-2">
                            <thead>
                                <tr>
                                    <th>Challan No#</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${selectedRowsHtml}
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Same remarks will be applied to all selected challans.</small>
                </div>
            </td>
        </tr>
        <tr>
            <td>Type</td>
            <td colspan="2">
                <label for="cancell_type" class="form-label mb-1">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <select class="form-control" name="cancell_type" id="cancell_type">
                    <option value="">---Select Type---</option>
                    <option value="CANCELLED">Cancel</option>
                </select>
                <span id="cancell_type-error" class="error text-danger"></span>
            </td>
        </tr>
        <tr>
            <td>Remarks</td>
            <td colspan="2">
                <label for="bulkCancelRemarks" class="form-label mb-1">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <input
                    type="text"
                    class="form-control"
                    name="remarks"
                    id="bulkCancelRemarks"
                    placeholder="Enter remarks"
                >
                <span id="remarks-error" class="error text-danger"></span>
            </td>
        </tr>`;

        document.getElementById('totalEditable').value = '0';
        $('#challanModalTotalRow').hide();
        $('#challanModalMainHead').hide();
        document.getElementById('challanDetailsModalLabel').textContent = 'Confirmation of Challan Cancellation';
        $('#errorMessage').addClass('d-none');
        $('#successMessage').html('');
        $('#challanDetailsModal').modal('show');
    }



    // Already-split installment challan: check_fee_installments > 0 (same column used elsewhere)
    function isAlreadyInstallmentChallan(checkFeeInstallments) {
        return Number(checkFeeInstallments) > 0;
    }

    function setSurplusAdjMsg(type, message) {
        const $box = $('#surplusAdjMsg');
        if (!message) {
            $box.html('');
            return;
        }
        const cls = type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'danger');
        $box.html(`<div class="alert alert-${cls} py-2 mb-0">${message}</div>`);
    }

    function openSurplusAdjustmentModal() {
        const appId = $('#st_aai_id').val();
        const enrollmentNo = ($('#studentEnrollment').val() || '').trim();
        if (!appId && !enrollmentNo) {
            alert('App ID / Enrollment not found. Please search the student again.');
            if (window.isBulkSurplusAdjust) {
                clearBulkSurplusState();
            } else {
                $('#surplusAdjustment').prop('checked', false);
                $('.scg-option-chip').each(function () {
                    $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
                });
            }
            return;
        }
        $('#surplusAdjAppIdLabel').text(appId || '—');
        $('#surplusAdjApplyBox').hide();
        $('#surplusAdjBulkBox').hide();
        $('#surplusAdjApplyAmount').val('');
        $('#surplusAdjAvailable').val('0');
        $('#surplusAdjBulkAvailable').val('0');
        $('#surplusAdjChallanAmount').val(getChallanAmountForSurplus());
        $('#surplusAdjRemaining').val('0');
        $('#surplusAdjBulkRemaining').val('0');
        $('#surplusAdjSelectedFfaId').val('');
        $('#surplusAdjApplyError').text('');
        $('#surplusAdjBulkError').text('');
        setSurplusAdjMsg('', '');

        if (window.isBulkSurplusAdjust && Array.isArray(window.bulkSurplusRows)) {
            $('#surplusAdjustmentModalLabel').text('Surplus Adjustment — Selected Challans');
            $('#surplusAdjConfirmBtn').text('Apply Surplus Now');
            renderBulkSurplusChallanRows(window.bulkSurplusRows);
            $('#surplusAdjBulkCountLabel').text(window.bulkSurplusRows.length + ' challan(s) selected — enter apply amount per challan.');
        } else {
            window.isBulkSurplusAdjust = false;
            $('#surplusAdjustmentModalLabel').text('Surplus Adjustment');
            $('#surplusAdjConfirmBtn').text('Confirm Amount');
        }

        $('#surplusAdjustmentModal').modal('show');
        loadSurplusAdjustments();
    }

    function renderBulkSurplusChallanRows(rows) {
        const $tbody = $('#surplusAdjBulkChallanBody');
        $tbody.html((rows || []).map(function (row) {
            const id = row.id ?? '';
            const amt = parseFloat(row.invoiceamount) || 0;
            return `<tr>
                <td><strong>${id}</strong></td>
                <td>${amt}</td>
                <td>
                    <input type="text" class="form-control form-control-sm surplus-bulk-apply-amt"
                        data-challan-id="${id}" data-max="${amt}"
                        onkeypress="return /[0-9.]/i.test(event.key)"
                        placeholder="0">
                </td>
                <td class="font-weight-bold surplus-bulk-after-amt" data-invoice="${amt}">${amt}</td>
            </tr>`;
        }).join(''));
    }

    function getBulkSurplusApplyItems() {
        const items = [];
        $('.surplus-bulk-apply-amt').each(function () {
            const $inp = $(this);
            const raw = ($inp.val() || '').trim();
            if (raw === '') {
                return;
            }
            const amount = parseFloat(raw);
            const editId = $inp.data('challan-id');
            const maxAmt = parseFloat($inp.data('max')) || 0;
            items.push({ edit_id: editId, amount: amount, max: maxAmt, $el: $inp });
        });
        return items;
    }

    function updateBulkSurplusAfterAmounts() {
        $('.surplus-bulk-apply-amt').each(function () {
            const $inp = $(this);
            const invoice = parseFloat($inp.data('max')) || 0;
            const raw = ($inp.val() || '').trim();
            const applyAmt = raw === '' ? 0 : parseFloat(raw);
            const $after = $inp.closest('tr').find('.surplus-bulk-after-amt');
            if (isNaN(applyAmt) || applyAmt < 0) {
                $after.text(invoice);
                return;
            }
            const afterAmt = Math.round((invoice - applyAmt) * 100) / 100;
            $after.text(afterAmt);
        });
    }

    function updateBulkSurplusRemainingPreview() {
        updateBulkSurplusAfterAmounts();
        const available = parseFloat($('#surplusAdjBulkAvailable').val()) || 0;
        const items = getBulkSurplusApplyItems();
        let total = 0;
        let err = '';
        items.forEach(function (it) {
            if (isNaN(it.amount) || it.amount < 0) {
                err = 'Enter valid amounts (0 or greater).';
                return;
            }
            if (it.amount > it.max) {
                err = 'Amount for challan ' + it.edit_id + ' cannot exceed invoice (' + it.max + ').';
            }
            total += (isNaN(it.amount) ? 0 : it.amount);
        });
        total = Math.round(total * 100) / 100;
        if (!err && total > available) {
            err = 'Total apply (' + total + ') cannot exceed available surplus (' + available + ').';
        }
        $('#surplusAdjBulkError').text(err);
        const remaining = err && total > available
            ? available
            : Math.round((available - total) * 100) / 100;
        $('#surplusAdjBulkRemaining').val(isNaN(remaining) ? available : remaining);
        return !err;
    }

    function submitBulkSurplusAdjust() {
        const ffaId = $('#surplusAdjSelectedFfaId').val();
        const available = parseFloat($('#surplusAdjBulkAvailable').val()) || 0;
        if (!ffaId || available <= 0) {
            setSurplusAdjMsg('error', 'No surplus row selected.');
            return;
        }
        if (!updateBulkSurplusRemainingPreview()) {
            return;
        }
        const items = getBulkSurplusApplyItems().filter(function (it) {
            return !isNaN(it.amount) && it.amount > 0;
        });
        if (!items.length) {
            $('#surplusAdjBulkError').text('Enter amount greater than 0 on at least one challan.');
            return;
        }

        const payload = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            surplus_ffa_id: ffaId,
            items: items.map(function (it) {
                return { edit_id: it.edit_id, amount: it.amount };
            })
        };

        const $btn = $('#surplusAdjConfirmBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Applying...');

        $.ajax({
            url: '/finance/student-surplus-adjust-bulk',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function (res) {
                if (!res.success) {
                    setSurplusAdjMsg('error', res.message || 'Unable to apply surplus.');
                    $btn.prop('disabled', false).text('Apply Surplus Now');
                    return;
                }
                setSurplusAdjMsg('success', res.message || 'Surplus applied.');
                setTimeout(function () {
                    $('#surplusAdjustmentModal').modal('hide');
                    clearBulkSurplusState();
                    searchStudent();
                }, 800);
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Failed to apply surplus adjustment.';
                setSurplusAdjMsg('error', msg);
                $btn.prop('disabled', false).text('Apply Surplus Now');
            }
        });
    }

    function loadSurplusAdjustments() {
        const appId = $('#st_aai_id').val();
        const enrollmentNo = ($('#studentEnrollment').val() || '').trim();
        const $tbody = $('#surplusAdjTableBody');
        $tbody.html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');

        $.ajax({
            url: '/finance/student-surplus-adjustments',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                app_id: appId,
                enrollment_no: enrollmentNo
            },
            success: function (res) {
                if (!res.success) {
                    $tbody.html(`<tr><td colspan="5" class="text-center text-danger">${res.message || 'Failed to load'}</td></tr>`);
                    $('#surplusAdjApplyBox').hide();
                    $('#surplusAdjBulkBox').hide();
                    return;
                }

                // Only rows with surplus > 0
                const rows = (res.adjustments || []).filter(function (r) {
                    const amt = parseFloat(r.ffa_surplus_amount ?? r.FFA_SURPLUS_AMOUNT ?? 0) || 0;
                    return amt > 0;
                });

                if (!rows.length) {
                    $tbody.html('<tr><td colspan="5" class="text-center text-muted">No surplus amount (&gt; 0) found for this App ID.</td></tr>');
                    $('#surplusAdjApplyBox').hide();
                    $('#surplusAdjBulkBox').hide();
                    setSurplusAdjMsg('warning', 'No available surplus to apply on this challan.');
                    return;
                }

                $tbody.html(rows.map(function (r, idx) {
                    const id = r.ffa_id ?? r.FFA_ID ?? '';
                    const cat = r.ffac_title ?? r.FFAC_TITLE ?? 'N/A';
                    const amt = parseFloat(r.ffa_surplus_amount ?? r.FFA_SURPLUS_AMOUNT ?? 0) || 0;
                    const status = r.ffa_amount_status ?? r.FFA_AMOUNT_STATUS ?? 'N/A';
                    const checked = idx === 0 ? 'checked' : '';
                    return `<tr>
                        <td class="text-center">
                            <input type="radio" name="surplusAdjSelectRow" value="${id}" data-surplus="${amt}" ${checked}>
                        </td>
                        <td>${id}</td>
                        <td>${cat}</td>
                        <td><strong>${amt}</strong></td>
                        <td>${status}</td>
                    </tr>`;
                }).join(''));

                // Auto-select first row
                const $first = $('input[name="surplusAdjSelectRow"]:checked');
                if ($first.length) {
                    $first.trigger('change');
                }
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load surplus adjustments.';
                $tbody.html(`<tr><td colspan="5" class="text-center text-danger">${msg}</td></tr>`);
                $('#surplusAdjApplyBox').hide();
                $('#surplusAdjBulkBox').hide();
            }
        });
    }

    function getChallanAmountForSurplus() {
        let challanAmt = parseFloat($('#studentInvoiceamount').val());
        if (!isNaN(challanAmt) && challanAmt > 0) {
            return challanAmt;
        }
        // Fallback: sum fee fields if invoice field empty
        const tuition = parseFloat($('#tuitionFee').val()) || 0;
        const admission = parseFloat($('#admissionFee').val()) || 0;
        const security = parseFloat($('#securityFee').val()) || 0;
        const medical = parseFloat($('#medicalFee').val()) || 0;
        const activity = parseFloat($('#activitychargesAmt').val()) || 0;
        return tuition + admission + security + medical + activity;
    }

    /** Manual Scholarship style: only checked utilities count toward invoice */
    function calcHostelUtilitiesFeeAdjAmounts() {
        const fridgeOn = $('#refrigeratorCheckBox').is(':checked');
        const acOn = $('#conditionerCheckBox').is(':checked');
        const heaterOn = $('#electricHeaterCheckBox').is(':checked');
        const fridge = fridgeOn ? (parseFloat($('#refrigeratorFee').val()) || 0) : 0;
        const ac = acOn ? (parseFloat($('#conditionerFee').val()) || 0) : 0;
        const heater = heaterOn ? (parseFloat($('#electricFee').val()) || 0) : 0;
        return {
            fridgeOn: fridgeOn,
            acOn: acOn,
            heaterOn: heaterOn,
            fridge: fridge,
            ac: ac,
            heater: heater,
            invoice: Math.round((fridge + ac + heater) * 100) / 100
        };
    }

    function openHostelUtilitiesFeeAdjModal() {
        const amt = calcHostelUtilitiesFeeAdjAmounts();
        $('#huAdjError').text('');
        if (!amt.fridgeOn && !amt.acOn && !amt.heaterOn) {
            $('#successMsg').html(createAlert('error', 'No utility selected. Check at least one Include checkbox.'));
            return false;
        }
        if (amt.fridgeOn && amt.fridge <= 0) {
            $('#successMsg').html(createAlert('error', 'Enter refrigerator fee amount greater than zero.'));
            return false;
        }
        if (amt.acOn && amt.ac <= 0) {
            $('#successMsg').html(createAlert('error', 'Enter air conditioner fee amount greater than zero.'));
            return false;
        }
        if (amt.heaterOn && amt.heater <= 0) {
            $('#successMsg').html(createAlert('error', 'Enter electric heater fee amount greater than zero.'));
            return false;
        }
        if (amt.invoice <= 0) {
            $('#successMsg').html(createAlert('error', 'Invoice amount must be greater than zero.'));
            return false;
        }
        $('#huAdjFridge').text(amt.fridge.toFixed(2));
        $('#huAdjAc').text(amt.ac.toFixed(2));
        $('#huAdjHeater').text(amt.heater.toFixed(2));
        $('#huAdjInvoice').text(amt.invoice.toFixed(2));
        $('#hostelUtilitiesFeeAdjModal').modal('show');
        return true;
    }

    function confirmHostelUtilitiesFeeAdjAndSubmit() {
        window._hostelUtilitiesFeeAdjConfirmed = true;
        $('#hostelUtilitiesFeeAdjModal').modal('hide');
        submitFormBtn({ preventDefault: function () {} });
    }

    function getSurplusMaxAllowed() {
        const available = parseFloat($('#surplusAdjAvailable').val()) || 0;
        const challanAmt = parseFloat($('#surplusAdjChallanAmount').val()) || 0;
        if (available <= 0) {
            return 0;
        }
        if (challanAmt <= 0) {
            return available;
        }
        return Math.min(available, challanAmt);
    }

    function updateSurplusRemainingPreview() {
        const available = parseFloat($('#surplusAdjAvailable').val()) || 0;
        const challanAmt = parseFloat($('#surplusAdjChallanAmount').val()) || 0;
        const maxAllowed = getSurplusMaxAllowed();
        let applyAmt = parseFloat($('#surplusAdjApplyAmount').val());

        if (isNaN(applyAmt) || $('#surplusAdjApplyAmount').val() === '') {
            $('#surplusAdjRemaining').val(available);
            $('#surplusAdjApplyError').text('');
            return;
        }
        if (applyAmt < 0) {
            applyAmt = 0;
            $('#surplusAdjApplyAmount').val('0');
        }
        if (applyAmt > maxAllowed) {
            let msg = 'Amount cannot exceed max allowed (' + maxAllowed + ').';
            if (applyAmt > available && applyAmt > challanAmt) {
                msg = 'Amount cannot exceed available surplus (' + available + ') or challan amount (' + challanAmt + ').';
            } else if (applyAmt > available) {
                msg = 'Amount cannot exceed available surplus (' + available + ').';
            } else if (applyAmt > challanAmt) {
                msg = 'Amount cannot exceed challan amount (' + challanAmt + ').';
            }
            $('#surplusAdjApplyError').text(msg);
            $('#surplusAdjRemaining').val(available);
            return;
        }
        $('#surplusAdjApplyError').text('');
        const remaining = Math.round((available - applyAmt) * 100) / 100;
        $('#surplusAdjRemaining').val(remaining);
    }

    function confirmSurplusApplyAmount() {
        const available = parseFloat($('#surplusAdjAvailable').val()) || 0;
        const challanAmt = parseFloat($('#surplusAdjChallanAmount').val()) || 0;
        const maxAllowed = getSurplusMaxAllowed();
        const ffaId = $('#surplusAdjSelectedFfaId').val();
        const applyAmt = parseFloat($('#surplusAdjApplyAmount').val());

        if (!ffaId || available <= 0) {
            setSurplusAdjMsg('error', 'No surplus row selected.');
            return;
        }
        if (challanAmt <= 0) {
            setSurplusAdjMsg('error', 'Challan amount not found. Please edit the challan again.');
            return;
        }
        if (isNaN(applyAmt) || applyAmt <= 0) {
            $('#surplusAdjApplyError').text('Enter amount to apply on this challan (greater than 0).');
            return;
        }
        if (applyAmt > available) {
            $('#surplusAdjApplyError').text('Amount cannot exceed available surplus (' + available + ').');
            return;
        }
        if (applyAmt > challanAmt) {
            $('#surplusAdjApplyError').text('Amount cannot exceed challan amount (' + challanAmt + ').');
            return;
        }
        if (applyAmt > maxAllowed) {
            $('#surplusAdjApplyError').text('Amount cannot exceed max allowed (' + maxAllowed + ').');
            return;
        }

        $('#surplus_apply_amount').val(applyAmt);
        $('#surplus_ffa_id').val(ffaId);
        $('#surplusAdjustment').prop('checked', true);
        $('.scg-option-chip').each(function () {
            $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
        });
        const remaining = Math.round((available - applyAmt) * 100) / 100;
        showSurplusGenerateDisclaimer(applyAmt, remaining);
        setTimeout(function () {
            $('#surplusAdjustmentModal').modal('hide');
        }, 400);
    }

    function showSurplusGenerateDisclaimer(applyAmt, remaining) {
        const actionLabel = ($('#verifyEdit').val() == '1') ? 'Update' : 'Generate Challan';
        const text = 'Surplus amount ' + applyAmt + ' is selected only — it is NOT applied yet. '
            + 'Press ' + actionLabel + ' below to apply it (remaining surplus will be ' + remaining + ').';
        $('#surplusGenerateDisclaimerText').text(text);
        $('#surplusGenerateDisclaimer').show();
        // Scroll so user sees action button + disclaimer
        const $btn = $('#submitBtn');
        const $scrollParent = $('.app-content').first();
        if ($btn.length && $scrollParent.length) {
            const top = $btn.offset().top - ($scrollParent.offset() ? $scrollParent.offset().top : 0) + ($scrollParent.scrollTop() || 0) - 40;
            $scrollParent.stop(true).animate({ scrollTop: Math.max(0, top) }, 400);
        }
    }

    function hideSurplusGenerateDisclaimer() {
        $('#surplusGenerateDisclaimer').hide();
        $('#surplusGenerateDisclaimerText').text('');
    }

    function blockFurtherInstallmentsUI() {
        $('#noOfInstallments').val('');
        const installmentsDiv = document.getElementById('installmentsDiv');
        if (installmentsDiv) {
            installmentsDiv.innerHTML = '';
        }
        const noOfDiv = document.getElementById('noOfInstallmentsDiv');
        if (noOfDiv) {
            noOfDiv.style.setProperty('display', 'none', 'important');
        }
        $('#noOfInstallments').closest('.form-group').hide();

        // Already an installment — Installment Allowed cannot be used
        $('#studentInstallment').prop('checked', false).prop('disabled', true);
        $('#studentInstallment').closest('.scg-option-chip')
            .addClass('scg-option-disabled')
            .attr('title', 'Installment Allowed is not available for installment challans');

        // Already an installment — Road Map / Semester cannot be used
        $('#roadmap_semester_structure').prop('checked', false).prop('disabled', true);
        $('#roadmap_semester_structure').closest('.scg-option-chip')
            .addClass('scg-option-disabled')
            .attr('title', 'Road Map / Semester is not available for installment challans');
        $('#roadmap-sess, #semester').prop('disabled', true).val('');
    }

    function restoreInstallmentAllowedUI() {
        $('#studentInstallment').prop('disabled', false);
        $('#studentInstallment').closest('.scg-option-chip')
            .removeClass('scg-option-disabled')
            .attr('title', 'Installment Allowed');

        $('#roadmap_semester_structure').prop('disabled', false);
        $('#roadmap_semester_structure').closest('.scg-option-chip')
            .removeClass('scg-option-disabled')
            .attr('title', 'Road Map / Semester Fee Structure');
        // Selects stay disabled until user checks Road Map again (same as default)
        if (!$('#roadmap_semester_structure').is(':checked')) {
            $('#roadmap-sess, #semester').prop('disabled', true);
        }

        $('#manualFeeAdjustment').prop('disabled', false);
        $('#manualFeeAdjustment').closest('.scg-option-chip')
            .removeClass('scg-option-disabled')
            .attr('title', 'Fee Adjustment');

        $('#surplusAdjustment').prop('disabled', false);
        $('#surplusAdjustment').closest('.scg-option-chip')
            .removeClass('scg-option-disabled')
            .attr('title', 'Surplus Adjustment');
    }

    /**
     * Processing Fee is for applicants (numeric App ID) only.
     * Enrollment starting with F or S → disable Processing Fee option.
     */
    function enrollmentBlocksProcessingFee() {
        const enr = ($('#studentEnrollment').val() || '').trim().toUpperCase();
        if (!enr) {
            return false;
        }
        const first = enr.charAt(0);
        return first === 'F' || first === 'S';
    }

    function updateProcessingFeeOptionAvailability() {
        const $opt = $('#feeType option[value="Processing_Fee"]');
        if (!$opt.length) {
            return;
        }
        const block = enrollmentBlocksProcessingFee();
        // Keep available while editing an existing Processing Fee challan
        const editingProcessing = $('#verifyEdit').val() == '1' && $('#feeType').val() === 'Processing_Fee';
        if (block && !editingProcessing) {
            $opt.prop('disabled', true);
            if ($('#feeType').val() === 'Processing_Fee') {
                $('#feeType').val('');
                applyFeeTypeOptionLocks();
            }
        } else {
            $opt.prop('disabled', false);
        }
    }

    /**
     * Fee-type option locks:
     * - Processing Fee: installment / fee adj / roadmap off (surplus ok)
     * - Hostel (+ utilities): installment / no-of-installments / roadmap / surplus off (fee adj ok)
     */
    function applyFeeTypeOptionLocks() {
        const feeType = $('#feeType').val();
        const hostelTypes = ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'];

        if (feeType === 'Processing_Fee') {
            $('#studentInstallment').prop('checked', false).prop('disabled', true);
            $('#studentInstallment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Processing Fee');

            $('#manualFeeAdjustment').prop('checked', false).prop('disabled', true);
            $('#manualFeeAdjustment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Processing Fee');

            $('#roadmap_semester_structure').prop('checked', false).prop('disabled', true);
            $('#roadmap_semester_structure').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Processing Fee');
            $('#roadmap-sess, #semester').prop('disabled', true).val('');

            // Surplus remains available
            $('#surplusAdjustment').prop('disabled', false);
            $('#surplusAdjustment').closest('.scg-option-chip')
                .removeClass('scg-option-disabled')
                .attr('title', 'Surplus Adjustment');

            $('#noOfInstallments').val('');
            const noOfDiv = document.getElementById('noOfInstallmentsDiv');
            if (noOfDiv) {
                noOfDiv.style.setProperty('display', 'none', 'important');
            }
            $('#noOfInstallments').closest('.form-group').hide();
            const installmentsDiv = document.getElementById('installmentsDiv');
            if (installmentsDiv) {
                installmentsDiv.innerHTML = '';
            }
            $('#full_semester_dev').hide();
            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
            $('.scg-option-chip').each(function () {
                $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
            });
            return;
        }

        if (feeType === 'Credit_hours_Summer_Course') {
            // Bulk summer: installment always NO; fee adj/roadmap off; surplus ok
            $('#studentInstallment').prop('checked', false).prop('disabled', true);
            $('#studentInstallment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Summer Credit Hour (bulk uses NO)');

            $('#manualFeeAdjustment').prop('checked', false).prop('disabled', true);
            $('#manualFeeAdjustment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Summer Credit Hour');

            $('#roadmap_semester_structure').prop('checked', false).prop('disabled', true);
            $('#roadmap_semester_structure').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Summer Credit Hour');
            $('#roadmap-sess, #semester').prop('disabled', true).val('');

            $('#surplusAdjustment').prop('disabled', false);
            $('#surplusAdjustment').closest('.scg-option-chip')
                .removeClass('scg-option-disabled')
                .attr('title', 'Surplus Adjustment');

            $('#noOfInstallments').val('');
            const noOfDivSummer = document.getElementById('noOfInstallmentsDiv');
            if (noOfDivSummer) {
                noOfDivSummer.style.setProperty('display', 'none', 'important');
            }
            $('#noOfInstallments').closest('.form-group').hide();
            const installmentsDivSummer = document.getElementById('installmentsDiv');
            if (installmentsDivSummer) {
                installmentsDivSummer.innerHTML = '';
            }
            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').removeClass('d-none');
            $('.scg-option-chip').each(function () {
                $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
            });
            return;
        }

        if (hostelTypes.includes(feeType)) {
            // Installment Allowed + No of Installments + Road Map off for hostel
            $('#studentInstallment').prop('checked', false).prop('disabled', true);
            $('#studentInstallment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Hostel fee types');

            $('#roadmap_semester_structure').prop('checked', false).prop('disabled', true);
            $('#roadmap_semester_structure').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Hostel fee types');
            $('#roadmap-sess, #semester').prop('disabled', true).val('');

            $('#noOfInstallments').val('');
            const noOfDivHostel = document.getElementById('noOfInstallmentsDiv');
            if (noOfDivHostel) {
                noOfDivHostel.style.setProperty('display', 'none', 'important');
            }
            $('#noOfInstallments').closest('.form-group').hide();
            const installmentsDivHostel = document.getElementById('installmentsDiv');
            if (installmentsDivHostel) {
                installmentsDivHostel.innerHTML = '';
            }

            // Fee Adjustment stays available; Surplus not available for hostel
            $('#manualFeeAdjustment').prop('disabled', false);
            $('#manualFeeAdjustment').closest('.scg-option-chip')
                .removeClass('scg-option-disabled')
                .attr('title', 'Fee Adjustment');
            $('#surplusAdjustment').prop('checked', false).prop('disabled', true);
            $('#surplusAdjustment').closest('.scg-option-chip')
                .addClass('scg-option-disabled')
                .attr('title', 'Not available for Hostel fee types');

            $('.scg-option-chip').each(function () {
                $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
            });
            return;
        }

        // Other fee types: restore option chips (installment edit may re-lock installment/roadmap)
        if ($('#verifyEdit').val() == '1' && isAlreadyInstallmentChallan($('#scCheckInstallments').val())) {
            blockFurtherInstallmentsUI();
            $('#manualFeeAdjustment').prop('disabled', false);
            $('#manualFeeAdjustment').closest('.scg-option-chip')
                .removeClass('scg-option-disabled')
                .attr('title', 'Fee Adjustment');
            $('#surplusAdjustment').prop('disabled', false);
            $('#surplusAdjustment').closest('.scg-option-chip')
                .removeClass('scg-option-disabled')
                .attr('title', 'Surplus Adjustment');
        } else {
            restoreInstallmentAllowedUI();
            const noOfDivRestore = document.getElementById('noOfInstallmentsDiv');
            if (noOfDivRestore) {
                noOfDivRestore.style.setProperty('display', 'block', 'important');
            }
            $('#noOfInstallments').closest('.form-group').show();
        }
        $('.scg-option-chip').each(function () {
            $(this).toggleClass('is-checked', $(this).find('input[type="checkbox"]').is(':checked'));
        });
    }

    /** Toggle primary action label: Edit → Update, otherwise Generate Challan */
    function setSubmitButtonMode(isEdit) {
        const $btn = $('#submitBtn');
        if (!$btn.length) {
            return;
        }
        if (isEdit) {
            $btn.html('<i class="fa fa-save mr-1"></i> Update');
        } else {
            $btn.html('<i class="fa fa-file-text-o mr-1"></i> Generate Challan');
        }
    }

    function editRec(event) {

        // Get the button element whether icon or button was clicked
        let buttonElement = event.target.closest('.edit-row');
        if (!buttonElement) {
            console.error('Button element not found');
            return;
        }

        // Drop focus from Edit so it does not stay highlighted on the table
        buttonElement.blur();
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
        }

        let rowData = null;
        const challanId = buttonElement.dataset.challanId;
        if (challanId && window.scgChallanRowById && window.scgChallanRowById[challanId]) {
            rowData = window.scgChallanRowById[challanId];
        } else if (buttonElement.dataset.row) {
            try {
                rowData = JSON.parse(buttonElement.dataset.row);
            } catch (e) {
                console.error('Failed to parse row data', e);
                return;
            }
        }
        if (!rowData) {
            console.error('Row data not found');
            return;
        }
        let sc_challan_type = rowData.sc_challan_type;
        let hostel_fee_type = rowData.sc_hostel_fee_type;
        // Keep app id available for Surplus Adjustment fetch on edit
        if (rowData.sc_app_id) {
            $('#st_aai_id').val(rowData.sc_app_id);
        }
        const alreadyInstallment = isAlreadyInstallmentChallan(rowData.check_fee_installments);
        $('#scCheckInstallments').val(rowData.check_fee_installments ?? 0);
        if (alreadyInstallment) {
            blockFurtherInstallmentsUI();
        } else {
            restoreInstallmentAllowedUI();
        }
        setSubmitButtonMode(true);
        setFeeStructureCapsFromEditRow(rowData);
        // Processing / Summer credit — edit not supported (button disabled; guard if clicked)
        if (sc_challan_type === 'Processing_Fee' || sc_challan_type === 'Credit_hours_Summer_Course') {
            alert('Edit is not available for ' + (sc_challan_type === 'Processing_Fee' ? 'Processing Fee' : 'Credit Hour Summer') + ' challans. Please generate a new challan if needed.');
            setSubmitButtonMode(false);
            return;
        }
        const formatDateIfValid = (date) => date ? formatDate(new Date(date)) : '';
        const formattedScDueDate = formatDateIfValid(rowData.sc_due_date);
        const formattedValidateDate = formatDateIfValid(rowData.sc_valid_date);
        console.log(rowData);
        if (sc_challan_type == 'Credit_hours') {
            $('#noOfInstallments').val('');

            // First set the select values without triggering change
            $('#sessionId').val(rowData.sc_session_id);
            $('#student_type').val(rowData.sc_student_status);
            $('#student_status').val(rowData.sc_sai_student_status);
            $('#feeType').val(sc_challan_type);

            // Prevent dropdown from opening
            setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
            setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
            setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);
            setSelectValueAndPreventDropdown('feeType', sc_challan_type);

            $('#creditHr').val(rowData.sc_enrolled_cr_hr).attr('readonly', true);
            $('#verifyEdit').val('1');
            $('#edit_id').val(rowData.id).attr('readonly', true);

            $('.credit_hr_div').removeClass('d-none');
            $('#full_semester_dev').hide();

            // Set the formatted dates to the respective fields
            setTimeout(function () {
                $('#dueDate').val(formattedScDueDate).removeAttr('readonly');
                $('#validateDate').val(formattedValidateDate).removeAttr('readonly');
                if (alreadyInstallment) {
                    // Already an installment — do not allow splitting further
                    blockFurtherInstallmentsUI();
                } else {
                    document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'block', 'important');
                }
                // $('#noOfInstallments').closest('.form-group').show();
            }, 500);

            // Add readonly attribute back to the fields
            $('#tuitionFee').attr('readonly', true);
            $('#medicalFee').attr('readonly', true);
            $('#admissionFee').attr('readonly', true);
            $('#activitychargesAmt').attr('readonly', true);
            $('#securityFee').attr('readonly', true);
            scrollToInstallments();
        }
        else if (sc_challan_type == 'Fee_Refund') {
            $('#feeType').val(sc_challan_type);
            $('#sessionId').val(rowData.sc_session_id);
            $('#student_type').val(rowData.sc_student_status);
            $('#student_status').val(rowData.sc_sai_student_status);
            $('#full_semester_dev').show();
            $('#tuitionFee').val(rowData['sc_tuition_fee_amnt']);
            $('#medicalFee').val(rowData['ffs_medical_amt']);
            $('#admissionFee').val(rowData['ffs_admission_amt']);
            $('#activitychargesAmt').val(rowData['sc_activity_amt']);
            $('#securityFee').val(rowData['ffs_security_amt']);
            $('#editChallan').val(rowData['challan_no'] || rowData.id);
            $('#refundEdit').val(1);
            $('#dueDate').val(formattedScDueDate).removeAttr('readonly');
            $('#validateDate').val(formattedValidateDate).removeAttr('readonly');

            // Remove readonly attribute from the fields
            $('#tuitionFee').removeAttr('readonly');
            $('#medicalFee').removeAttr('readonly');
            $('#admissionFee').removeAttr('readonly');
            $('#activitychargesAmt').removeAttr('readonly');
            $('#securityFee').removeAttr('readonly');
            $('.credit_hr_div, .credit_hr_summer_div').addClass('d-none');

            scrollToInstallments();
        }
        else if (sc_challan_type == 'Hostel_Fee_Refund') {
            $('#feeType').val(sc_challan_type);
            $('#sessionId').val(rowData.sc_session_id);
            $('#student_type').val(rowData.sc_student_status);
            $('#student_status').val(rowData.sc_sai_student_status);
            $('#full_semester_dev').hide();
            hideElementsById(['hostelFeeDiv', 'hostelRemarksDiv', 'hostel_per_dayDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);

            // Show only hostel fee and security fee fields
            showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv']);

            // Set values from rowData
            $('#hostelFee').val(rowData['invoiceamount'] || '');
            $('#hostel_security_fee').val(rowData['hostel_security_fee'] || '');
            $('#editChallan').val(rowData['challan_no'] || rowData.id);
            $('#refundEdit').val(1);
            $('#dueDate').val(formattedScDueDate).removeAttr('readonly');
            $('#validateDate').val(formattedValidateDate).removeAttr('readonly');

            // Prod: Hostel Fee Refund fields are editable
            $('#hostelFee').removeAttr('readonly');
            $('#hostel_security_fee').removeAttr('readonly');
            $('.credit_hr_div, .credit_hr_summer_div').addClass('d-none');

            scrollToInstallments();
        } else if (sc_challan_type == 'Hostel_Fee') {
            console.log(rowData);
            set_fields_by_hostel_fee_type(hostel_fee_type, rowData);
            // Prod: Hostel Fee + Security editable on edit (per-day rate stays structure-driven)
            $('#hostelFee').removeAttr('readonly');
            $('#hostel_security_fee').removeAttr('readonly');
            $('#hostel_per_day').attr('readonly', true);
            $('.credit_hr_div, .credit_hr_summer_div').addClass('d-none');

            scrollToInstallments();
        } else {
            $('#refundEdit').val('');
            if (rowData.check_fee_installments > 0 || ['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(hostel_fee_type)) {

                document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'none', 'important');
                $('#noOfInstallments').closest('.form-group').hide();
                $('#scholarshipsAmountOpt').hide();

                // First set values without triggering
                $('#sessionId').val(rowData.sc_session_id);
                $('#student_type').val(rowData.sc_student_status);
                $('#student_status').val(rowData.sc_sai_student_status);

                // Then prevent dropdown
                setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
                setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
                setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);


            }





            let installmentsDiv = document.getElementById('installmentsDiv');
            installmentsDiv.innerHTML = ''; // Clear previous content

            scrollToInstallments();
            $('#noOfInstallments').val('');
            $('#verifyEdit').val('1');
            $('#edit_id').val(rowData.id).attr('readonly', true);
            hideElementsById(['hostelFeeDiv', 'hostelRemarksDiv', 'hostel_per_dayDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);
            // Check for valid 'sc_challan_type' and hide/display elements based on feeType
            if (['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(hostel_fee_type)) {
                // Display relevant elements based on feeType
                const feeElements = {
                    'Hostel_Full_semster': ['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div'],
                    'Hostel_day_wise': ['hostel_per_dayDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div'],
                    'Hostel_other_utilities': ['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv']
                };
                // Show elements for the selected hostel fee type
                if (feeElements[hostel_fee_type]) {
                    showElementsById(feeElements[hostel_fee_type]);
                }
                if (hostel_fee_type == 'Hostel_other_utilities') {
                    setTimeout(function () {
                        removeInputReadOnly('refrigeratorFee');
                        removeInputReadOnly('electricFee');
                        removeInputReadOnly('conditionerFee');
                    }, 2000);
                }
                // Make input read-only with the given invoice amount
                makeInputReadOnly('studentInvoiceamount', rowData.invoiceamount);

                // Set the feeType value WITHOUT triggering to avoid AJAX overwriting values
                $('#feeType').val(hostel_fee_type);

                // Parse and format dates using JavaScript Date object
                const formattedScDueDate = formatDateIfValid(rowData.sc_due_date);
                const formattedValidateDate = formatDateIfValid(rowData.sc_valid_date);

                // Set the formatted dates directly with delay
                setTimeout(function () {
                    $('#dueDate').val(formattedScDueDate).removeAttr('readonly');
                    $('#validateDate').val(formattedValidateDate).removeAttr('readonly');
                }, 3000);

                // Show the invoice amount option
                $('#invoiceAmountOpt').show();

                //$('#daysNo').val(123);

                setTimeout(function () {
                    getFeeStructure();
                }, 100);


                if (['Hostel_day_wise'].includes(hostel_fee_type)) {
                    setTimeout(function () {
                        let hostelPerDay = parseFloat($('#hostel_per_day').val());
                        let feeDetails = rowData.sc_hostel_fee_details.replace(/^Hostel Fee days wise charges\.\s*/, '');
                        $('#hostelRemarks').val(feeDetails);
                        let daysNo = rowData.invoiceamount
                            ? Math.floor(rowData.invoiceamount / hostelPerDay)
                            : '';
                        $('#daysNo').val(daysNo);

                    }, 1000);
                }
                else {
                    $('#hostel_per_day').val('')
                }


                return;
            }


            removeInputReadOnly('sessionId');

            // Show full semester fields for non-hostel fee types
            $('#full_semester_dev').show();

            // First set select values without triggering
            $('#sessionId').val(rowData.sc_session_id);
            $('#student_type').val(rowData.sc_student_status);
            $('#student_status').val(rowData.sc_sai_student_status);

            // Then make them readonly with prevention
            setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
            setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
            setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);

            // Set input values
            makeInputReadOnly('studentInvoiceamount', rowData.invoiceamount);
            makeInputReadOnly('scholarshipsAmount', rowData.sc_scholarship_amount);
            makeInputReadOnly('tuitionFee', rowData.sc_tuition_fee_amnt);
            makeInputReadOnly('admissionFee', rowData.ffs_admission_amt);
            makeInputReadOnly('securityFee', rowData.ffs_security_amt);
            makeInputReadOnly('medicalFee', rowData.ffs_medical_amt);
            makeInputReadOnly('activitychargesAmt', rowData.sc_activity_amt);


            if (alreadyInstallment) {
                blockFurtherInstallmentsUI();
            } else if (rowData.sc_installment_allowed === "YES") {
                $('#studentInstallment').prop('checked', true);
            } else {
                $('#studentInstallment').prop('checked', false);
            }
            console.log(rowData.sc_due_date);
            // Parse dates using JavaScript Date object
            const scDueDate = rowData.sc_due_date ? new Date(rowData.sc_due_date) : null;
            const ssValidateDate = rowData.sc_valid_date ? new Date(rowData.sc_valid_date) : null;

            const formattedScDueDate = scDueDate ? formatDate(scDueDate) : '';
            const formattedValidateDate = ssValidateDate ? formatDate(ssValidateDate) : '';

            setTimeout(function () {
                $('#dueDate').val(formattedScDueDate);
                $('#validateDate').val(formattedValidateDate);
            }, 3000);
            $('#invoiceAmountOpt').show();
            $('#scholarshipsAmountOpt').show();





            // ysa sy read only remove krna phly readonly sirf  CANCEL FOR REISSUANCE  us k bad sb k lie open kr dia
            // hy account office k khny py
            // if(rowData.sc_challan_status === 'CANCEL FOR REISSUANCE'){
            removeInputReadOnly('studentInvoiceamount');
            // removeInputReadOnly('scholarshipsAmount');
            removeInputReadOnly('sessionId');
            removeInputReadOnly('admissionFee');
            removeInputReadOnly('tuitionFee');
            removeInputReadOnly('securityFee');
            removeInputReadOnly('medicalFee');
            removeInputReadOnly('activitychargesAmt');

            // Set feeType without triggering to avoid AJAX call overwriting values
            $('#feeType').val(sc_challan_type);

            // Set dates without delay since values are already available
            $('#dueDate').val(formattedScDueDate).removeAttr('readonly');
            $('#validateDate').val(formattedValidateDate).removeAttr('readonly');

            setTimeout(function () {
                getFeeStructure();
            }, 100);

            // Parent / unsplit challan can still create installments; already-split cannot
            if (alreadyInstallment) {
                blockFurtherInstallmentsUI();
            } else {
                restoreInstallmentAllowedUI();
                $('#noOfInstallments').closest('.form-group').show();
                document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'block', 'important');
            }

            try {
                if (rowData && rowData.sc_challan_status) {
                    // console.log("Data is valid");
                }
            } catch (e) {
                console.error("Error processing data: ", e);
            }
            // }
        }
    }


    // Function to format date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }


    function setSelectValueAndPreventDropdown(id, value) {
        // Set value without triggering change to prevent unwanted AJAX calls
        $(`#${id}`).val(value).off('mousedown.scgLock').on('mousedown.scgLock', function (e) {
            e.preventDefault(); // Prevent the dropdown from opening
        });
    }

    function makeInputReadOnly(id, value) {
        $(`#${id}`)
            .val(value) // Set the value
            .attr('readonly', true) // Make the input read-only
            .off('keydown.scgLock')
            .on('keydown.scgLock', function (e) {
                e.preventDefault(); // Prevent any key press in the input field
            });
    }

    function removeInputReadOnly(id) {
        $(`#${id}`)
            .removeAttr('readonly') // Remove the read-only attribute
            .off('keydown.scgLock'); // Remove the event handler to allow key presses
    }

    /** After edit+generate: unlock selects/inputs so next action works without page refresh */
    function unlockFormFieldsAfterEdit() {
        ['sessionId', 'student_type', 'student_status', 'feeType', 'roadmap-sess', 'semester', 'sess_type'].forEach(function (id) {
            $('#' + id).off('mousedown.scgLock');
        });
        [
            'studentInvoiceamount', 'scholarshipsAmount', 'tuitionFee', 'admissionFee',
            'securityFee', 'medicalFee', 'activitychargesAmt', 'creditHr',
            'dueDate', 'validateDate',
            'refrigeratorFee', 'electricFee', 'conditionerFee',
            'daysNo', 'hostelRemarks'
        ].forEach(function (id) {
            removeInputReadOnly(id);
            $('#' + id).prop('disabled', false);
        });
        // Hostel fee/security: lock after edit complete so next Generate uses structure.
        // (During Hostel_Fee edit they are unlocked like prod — see set_fields / editRec.)
        $('#hostelFee, #hostel_security_fee, #hostel_per_day').attr('readonly', true).prop('disabled', false);
        restoreInstallmentAllowedUI();
        const noOfDiv = document.getElementById('noOfInstallmentsDiv');
        if (noOfDiv) {
            noOfDiv.style.setProperty('display', 'block', 'important');
        }
        $('#noOfInstallments').closest('.form-group').show();
        $('.credit_hr_div').addClass('d-none');
        $('#creditHr').val('');
        setSubmitButtonMode(false);
        clearFeeStructureCaps();
        applyFeeTypeOptionLocks();
        updateProcessingFeeOptionAvailability();
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('.cancel-row')) {
            const buttonElement = event.target.closest('.cancel-row');
            let rowData = null;
            const challanId = buttonElement.dataset.challanId || buttonElement.getAttribute('data-challan-id');
            if (challanId && window.scgChallanRowById && window.scgChallanRowById[challanId]) {
                rowData = window.scgChallanRowById[challanId];
            } else if (buttonElement.getAttribute('data-row')) {
                try {
                    rowData = JSON.parse(buttonElement.getAttribute('data-row'));
                } catch (e) {
                    console.error('Failed to parse cancel row data', e);
                    return;
                }
            }
            if (!rowData) {
                console.error('Cancel row data not found');
                return;
            }
            cancelRow(rowData);
        }
    });


    //Updates the total value dynamically and validates input values.


    function updateTotal() {
        const inputs = document.querySelectorAll('.editable-input');
        const scCheckInstallments = document.getElementById('scCheckInstallments').value;
        const scChallanStatus = document.getElementById('scChallanStatus').value;

        let total = 0;

        inputs.forEach(input => {
            const maxValue = Math.abs(parseFloat(input.dataset.maxValue) || 0);
            let value = parseFloat(input.value) || 0;
            const inputName = input.name;


            if (scChallanStatus == 'PAID') {


                if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
                    total += value;
                    return; // Skip the rest of the loop for these specific inputs
                }
            }
            else {
                if (scCheckInstallments == 0) {
                    // Agar scCheckInstallments 0 hai, toh 'sc_surplus_amount' aur 'sc_scholarship_amount' ko sum mein add karo
                    if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
                        total += value;
                        return; // Skip the rest of the loop for these specific inputs
                    }
                } else {
                    // Agar scCheckInstallments 0 nahi hai, toh 'sc_surplus_amount' aur 'sc_scholarship_amount' ko skip kar do
                    if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
                        return; // Skip the rest of the loop for these specific inputs
                    }
                }
            }






            if (value < 0) {
                value = 0;
                input.value = 0;
            }
            if (isNaN(value)) {
                input.value = 0;
                value = 0;
            }
            if (value > maxValue) {
                input.classList.add('is-invalid'); // Add Bootstrap error class
            } else {
                input.classList.remove('is-invalid'); // Remove error class if valid
            }

            // Add to total only if value is valid and non-negative
            total += value <= maxValue ? value : 0;
        });

        // Update the total value
        document.getElementById('totalEditable').value = total.toFixed(2);
    }


    //Populates the modal with row data and displays it.




    function cancelRow(rowData) {
        // Update hidden inputs in the form
        // console.log(rowData);
        window.bulkCancelRows = null;
        window.isBulkCancel = false;
        document.getElementById('challanDetailsModalLabel').textContent = 'Confirmation of Challan Deletion';
        $('#challanModalTotalRow').show();
        $('#challanModalMainHead').show();

        document.getElementById('challanIdInput').value = rowData.id;
        document.getElementById('enrollmentNoInput').value = rowData.sc_enrollment_no || rowData.sc_app_id;
        document.getElementById('scChallanStatus').value = rowData.sc_challan_status;
        document.getElementById('scCheckInstallments').value = rowData.check_fee_installments;
        let ffs_credhr_amt = rowData.ffs_credhr_amt;

        // Clear previous rows in the table body
        const tableBody = document.getElementById('challanDetailsTableBody');
        tableBody.innerHTML = '';

        // Define fields to display
        let fields = [
            {
                name: 'Tuition Fee',
                key: (rowData.ffs_credhr_amt !== null &&
                    typeof rowData.ffs_credhr_amt !== 'undefined' &&
                    rowData.ffs_credhr_amt > 0)
                    ? 'ffs_credhr_amt'
                    : 'sc_installment_tuition_fee_amnt',
                isTuitionFee: true
            },
            { name: 'Medical Fee', key: 'ffs_medical_amt' },
            { name: 'Adjusted Surplus', key: 'sc_surplus_amount' },
            { name: 'Activity Charges', key: 'sc_activity_amt' },
            { name: 'Admission Fee', key: 'ffs_admission_amt' },
            { name: 'Security Fee', key: 'ffs_security_amt' },
            { name: 'Scholarship Amount', key: 'sc_scholarship_amount' },
        ];

        const isUnpaid = rowData.sc_challan_status === "UNPAID";
        const sc_surplus_amount = parseFloat(rowData.sc_surplus_amount) || 0;
        const sc_scholarship_amount = parseFloat(rowData.sc_scholarship_amount) || 0;

        let challanType = rowData.sc_hostel_fee_type;

        if (['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(challanType)) {
            // Modify challanType to make it more readable
            fields = [];
            let formattedChallanType = challanType.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
            // Empty the fields array and add the dynamic data
            fields.push({ name: formattedChallanType, key: 'invoiceamount' });
        }

        // Build table rows dynamically
        let rowsContent = '';
        fields.forEach(field => {
            let value = parseFloat(rowData[field.key]) || 0;

            // Adjust Surplus Amount if applicable
            if (field.key === 'sc_surplus_amount') {
                value = sc_surplus_amount;
                if (value > 0) {
                    value = -value;
                }
            }

            if (field.key === 'sc_scholarship_amount') {
                value = parseFloat(rowData.sc_scholarship_amount) || 0;
                if (value > 0) {
                    value = -value;
                }
            }

            rowsContent += `
            <tr>
                <td>${field.name}</td>
                <td class="value-cell">
                    ${field.key === 'sc_installment_tuition_fee_amnt' && rowData.sc_tuition_fee_amnt
                    ? Math.abs(parseFloat(rowData.sc_tuition_fee_amnt))
                    : Math.abs(value)}
                </td>
                <td>
                    <input
                        type="number"
                        class="form-control editable-input"
                        name="${field.key}"
                        value="${value}"
                        placeholder="Enter new value"
                        data-max-value="${Math.abs(value)}"
                        oninput="updateTotal()"
                        ${isUnpaid ? 'disabled' : ''}
                    >
                    <div class="invalid-feedback">Value cannot exceed ${Math.abs(value)}</div>
                </td>
            </tr>
        `;
        });

        if (rowData.sc_challan_status === 'PAID') {
            rowsContent += `
              <tr class='d-none'>
                <td>Adjust in Surplus</td>
                <td colspan="2">
                    <label for="surplus_option" class="form-label">
                        <small>Required <span style="color: red;">*</span></small>
                    </label>
                    <select class="form-control" name="surplus_option" id="surplus_option">

                        <option value="NO">No</option>
                    </select>
                    <span id="surplus_option-error" class="error text-danger"></span>
                </td>
            </tr>
        `;
        }

        // Add the dropdown for Type and the input for Remarks
        rowsContent += `
        <tr>
            <td>Type</td>
            <td colspan="2">
                <label for="cancell_type" class="form-label">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <select class="form-control" name="cancell_type" id="cancell_type">
                    <option value="">---Select Type---</option>
                    <option value="CANCELLED">Cancel</option>
                </select>
                <span id="cancell_type-error" class="error text-danger"></span>
            </td>
        </tr>
        <tr>
            <td>Remarks</td>
            <td colspan="2">
                <label for="cancell_type" class="form-label">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <input
                    type="text"
                    class="form-control"
                    name="remarks"
                    placeholder="Enter remarks"
                >
                <span id="remarks-error" class="error text-danger"></span>
            </td>
        </tr>
    `;

        // Append rows to the table body
        tableBody.innerHTML = rowsContent;

        // Update the total immediately
        updateTotal();

        // Show the modal
        $('#challanDetailsModal').modal('show');
    }
    function mapSessionType(value) {
        const key = value.trim().toLowerCase();

        const mapping = {
            'regular': 'Regular',
            'non-regular': 'Non-Regular',
            'regular_outside': 'Regular_outside',
            'non-regular_outside': 'Non-Regular_outside',
            'regular_outside_bedian': 'Regular_outside_bedian',
            'non-regular_outside_bedian': 'Non-Regular_outside_bedian',
        };

        return mapping[key] || 'Unknown';
    }

    /** Map challan row → Hostel Student Type dropdown (Existing / New_Admission) */
    function resolveHostelStudentTypeFromRow(rowData) {
        if (!rowData) {
            return '';
        }
        const candidates = [
            rowData.sc_hostel_student_type,
            rowData.SC_HOSTEL_STUDENT_TYPE,
            rowData.sc_hostelite_type,
            rowData.fhfs_student_type,
            rowData.FHFS_STUDENT_TYPE
        ];
        for (let i = 0; i < candidates.length; i++) {
            const v = String(candidates[i] || '').trim();
            if (!v) continue;
            if (v === 'Existing' || v === 'New_Admission') {
                return v;
            }
            if (/new_?admission|new\s*applicant/i.test(v)) {
                return 'New_Admission';
            }
            if (/exist/i.test(v)) {
                return 'Existing';
            }
        }
        // Enrolled student → Existing; applicant-only → New_Admission
        if (rowData.sc_enrollment_no) {
            return 'Existing';
        }
        return 'New_Admission';
    }

    /** Snapshot of utility amounts/remarks while editing (survives async getFeeStructure). */
    var editHostelUtilitiesRow = null;
    /** Bumped on Search to cancel delayed edit re-apply timeouts. */
    var hostelEditApplyToken = 0;
    window._scgFeeStructureReqSeq = window._scgFeeStructureReqSeq || 0;
    window._scgSearchReqSeq = window._scgSearchReqSeq || 0;

    function pickRowAmount(rowData, keys) {
        if (!rowData) return '';
        for (var i = 0; i < keys.length; i++) {
            var v = rowData[keys[i]];
            if (v !== null && v !== undefined && v !== '') {
                return v;
            }
        }
        return '';
    }

    function hostelRemarksFromRow(rowData) {
        var remark = pickRowAmount(rowData, ['sc_hostel_fee_details', 'SC_HOSTEL_FEE_DETAILS']) || '';
        remark = String(remark);
        if (!remark) return '';
        return remark.indexOf('.') >= 0
            ? remark.substring(remark.indexOf('.') + 1).trim()
            : remark;
    }

    /** Fill fridge / heater / AC + Include checkboxes + remarks from challan row. */
    function applyHostelUtilitiesFromEditRow(rowData) {
        if (!rowData) return;
        var fridge = pickRowAmount(rowData, ['sc_refrigerator', 'SC_REFRIGERATOR']);
        var electric = pickRowAmount(rowData, ['sc_electric', 'SC_ELECTRIC']);
        var conditioner = pickRowAmount(rowData, ['sc_conditioner', 'SC_CONDITIONER']);

        $('#refrigeratorFee').val(fridge);
        $('#electricFee').val(electric);
        $('#conditionerFee').val(conditioner);

        $('#refrigeratorCheckBox').prop('checked', parseFloat(fridge) > 0);
        $('#electricHeaterCheckBox').prop('checked', parseFloat(electric) > 0);
        $('#conditionerCheckBox').prop('checked', parseFloat(conditioner) > 0);

        var remarks = hostelRemarksFromRow(rowData);
        if (remarks !== '') {
            $('#hostelRemarks').val(remarks);
        }
    }

    function set_fields_by_hostel_fee_type(hostel_fee_type, rowData) {
        // New edit session — cancel any previous delayed re-apply / in-flight fee-structure
        const token = ++hostelEditApplyToken;
        window._scgFeeStructureReqSeq = (window._scgFeeStructureReqSeq || 0) + 1;
        // First set values without triggering
        $('#sessionId').val(rowData.sc_session_id);
        $('#student_type').val(rowData.sc_student_status);
        $('#student_status').val(rowData.sc_sai_student_status);
        // Then prevent dropdown
        setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
        setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
        setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);
        setSelectValueAndPreventDropdown('feeType', hostel_fee_type);
        // Parse dates using JavaScript Date object
        let scDueDate = rowData.sc_due_date ? new Date(rowData.sc_due_date) : null;
        let ssValidateDate = rowData.sc_valid_date ? new Date(rowData.sc_valid_date) : null;

        let formattedScDueDate = scDueDate ? formatDate(scDueDate) : '';
        let formattedValidateDate = ssValidateDate ? formatDate(ssValidateDate) : '';

        $('#editChallan').val(rowData['challan_no'] || rowData.id);
        // Prefer CMS id for PHP where('id') / surplus / utilities update; challan_no still accepted as fallback on BE
        $('#edit_id').val(rowData.id || rowData['challan_no']);
        $('#verifyEdit').val('1');
        $('#refundEdit').val('');

        $('#full_semester_dev').hide();

        hideElementsById(['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel_per_dayDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);

        let elements_of_hostel = {
            'Hostel_Full_semster': ['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div'],
            'Hostel_day_wise': ['hostel_per_dayDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div'],
            'Hostel_other_utilities': ['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv']
        };

        // Must set BEFORE sess_type change (fee-structure API uses hostel_student_type)
        const hostelStudentType = resolveHostelStudentTypeFromRow(rowData);
        $('#hostel_student_type').val(hostelStudentType);

        console.log(rowData);
        if (hostel_fee_type == 'Hostel_Full_semster') {
            editHostelUtilitiesRow = null;
            // Show only hostel fee and security fee fields
            showElementsById(elements_of_hostel[hostel_fee_type]);
            // Set values from rowData
            $('#sess_type').val(mapSessionType(rowData['sc_session_type']) || '').trigger('change');
            $('#hostelFee').val(rowData['ffs_hostel_amt'] || '');
            $('#hostel_security_fee').val(rowData['ffs_security_amt'] || '');
            let remarkFull = rowData['sc_hostel_fee_details'] || '';
            let afterDotFull = remarkFull.indexOf('.') >= 0
                ? remarkFull.substring(remarkFull.indexOf('.') + 1).trim()
                : remarkFull;
            $('#hostelRemarks').val(afterDotFull);
        }
        if (hostel_fee_type == 'Hostel_day_wise') {
            editHostelUtilitiesRow = null;
            // Show only hostel fee and security fee fields
            showElementsById(elements_of_hostel[hostel_fee_type]);
            // Set values from rowData
            // $('#hostelFee').val(rowData['invoiceamount'] || '');
            $('#sess_type').val(mapSessionType(rowData['sc_session_type']) || '').trigger('change');
            $('#daysNo').val(rowData['sc_hostel_days'] || '');
            $('#hostel_per_day').val(rowData['ffs_hostel_amt'] / rowData['sc_hostel_days'] || '');
            let remark = rowData['sc_hostel_fee_details'] || '';
            let afterDot = remark.indexOf('.') >= 0
                ? remark.substring(remark.indexOf('.') + 1).trim()
                : remark;
            $('#hostelRemarks').val(afterDot);
            $('#hostel_per_day').attr('readonly', true);
        }
        if (hostel_fee_type == 'Hostel_other_utilities') {
            // Show utility fields and restore challan amounts (do not clear)
            showElementsById(elements_of_hostel[hostel_fee_type]);
            hideElementsById(['existing-hostellite-div']);
            editHostelUtilitiesRow = rowData;
            applyHostelUtilitiesFromEditRow(rowData);
            $('#sess_type').val(mapSessionType(rowData['sc_session_type']) || '').trigger('change');
            $('#hostel_security_fee').val(rowData['ffs_security_amt'] || '');
            setTimeout(function () {
                if (token !== hostelEditApplyToken) return;
                applyHostelUtilitiesFromEditRow(editHostelUtilitiesRow || rowData);
                removeInputReadOnly('refrigeratorFee');
                removeInputReadOnly('electricFee');
                removeInputReadOnly('conditionerFee');
            }, 500);
        }

        // Re-apply after sess_type→getFeeStructure (async) so value is not left empty
        setTimeout(function () {
            if (token !== hostelEditApplyToken) return;
            if (hostel_fee_type == 'Hostel_other_utilities') {
                applyHostelUtilitiesFromEditRow(editHostelUtilitiesRow || rowData);
                // Other utilities: do not force hostel_student_type (fee structure must stay unfiltered)
            } else if (hostelStudentType) {
                $('#hostel_student_type').val(hostelStudentType);
            }
            $('#dueDate').val(formattedScDueDate);
            $('#validateDate').val(formattedValidateDate);
            // Prod: fee + security editable on Full/day edit; per-day rate stays read-only
            if (hostel_fee_type === 'Hostel_Full_semster' || hostel_fee_type === 'Hostel_day_wise') {
                $('#hostelFee, #hostel_security_fee').removeAttr('readonly');
            }
            $('#hostel_per_day').attr('readonly', true);
            applyFeeTypeOptionLocks();
        }, 500);

        setTimeout(function () {
            if (token !== hostelEditApplyToken) return;
            if (hostel_fee_type == 'Hostel_other_utilities') {
                applyHostelUtilitiesFromEditRow(editHostelUtilitiesRow || rowData);
            } else if (hostelStudentType) {
                $('#hostel_student_type').val(hostelStudentType);
            }
            $('#dueDate').val(formattedScDueDate);
            $('#validateDate').val(formattedValidateDate);
            if (hostel_fee_type === 'Hostel_Full_semster' || hostel_fee_type === 'Hostel_day_wise') {
                $('#hostelFee, #hostel_security_fee').removeAttr('readonly');
            }
            $('#hostel_per_day').attr('readonly', true);
        }, 2000);

    }

    // Submit the delete challan form after validation.
    function submitCancelChallan() {
        const form = $('#cancelChallanForm');
        const inputs = form.find('.editable-input');
        const errorMessage = $('#errorMessage');
        const successMessageDiv = $('#successMessage');
        const submitButton = $('#deleteChallanBtn');
        let hasErrors = false;

        // Hide any previous error message
        errorMessage.addClass('d-none');
        $('#cancell_type-error').text('');
        $('#remarks-error').text('');

        const cancellType = form.find('[name="cancell_type"]').val();
        const remarks = (form.find('[name="remarks"]').val() || '').trim();

        if (!cancellType) {
            $('#cancell_type-error').text('Cancellation type is required.');
            hasErrors = true;
        }
        if (!remarks) {
            $('#remarks-error').text('Remarks are required.');
            hasErrors = true;
        }

        // Bulk cancel: same modal remarks/type, same backend procedure in a loop
        if (window.isBulkCancel && Array.isArray(window.bulkCancelRows) && window.bulkCancelRows.length > 0) {
            if (hasErrors) {
                return false;
            }
            submitBulkCancelChallans(cancellType, remarks, submitButton, successMessageDiv);
            return;
        }

        if (hasErrors) {
            return false;
        }

        // Check if any input exceeds its max value
        inputs.each(function () {
            const input = $(this);
            let value = parseFloat(input.val()) || 0;
            if (value < 0) {
                value = 0;
                input.val(0);
            }
            const maxValue = Math.abs(parseFloat(input.data('max-value')) || 0);
            const inputName = input.attr('name');

            // Skip validation for 'sc_surplus_amount'
            if (inputName === 'sc_surplus_amount') {
                return; // Skip the rest of the validation for this specific input
            }

            if (value > maxValue) {
                input.addClass('is-invalid'); // Add error class
                hasErrors = true;
            } else {
                input.removeClass('is-invalid'); // Remove error class
            }
        });

        // If there are errors, show the error message in the modal
        if (hasErrors) {
            errorMessage.removeClass('d-none'); // Display error message

            // Automatically hide the error message after 5 seconds
            setTimeout(() => errorMessage.addClass('d-none'), 5000);
            return false;
        }

        // Disable the button and show the spinner with 'Submitting...' text
        submitButton.prop('disabled', true);
        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling, please wait... Do not close.');

        // Serialize form data
        const formData = form.serialize();

        // Perform AJAX POST
        $.ajax({
            url: '/finance/student-cancel-challans',
            type: 'POST',
            data: formData,
            contentType: 'application/x-www-form-urlencoded',
            success: function (response) {
                // Ensure response.success exists and is properly handled
                const alertType = response.success ? 'success' : 'danger';
                const message = `
                <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                    <strong>${response.success ? 'Success!' : 'Error!'}</strong> ${response.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
                successMessageDiv.html(message);

                if (response.success) {
                    setTimeout(() => {
                        $('#challanDetailsModal').modal('hide');
                        $('#verifyEdit').val('');
                        $('#edit_id').val('');
                        successMessageDiv.html('');
                        // Uncomment if searchStudent needs to run
                    }, 1000);
                }
                searchStudent();

                // Always reset the button and spinner
                submitButton.prop('disabled', false);
                submitButton.html('Cancel Now');
            },
            error: function (xhr, status, error) {
                displayValidationErrors(xhr.responseJSON);
                submitButton.prop('disabled', false);
                submitButton.html('Cancel Now');
            },
            complete: function () {
                // Ensure spinner stops in any case (optional safeguard)
                submitButton.prop('disabled', false);
                submitButton.html('Cancel Now');
            },
        });
    }

    async function submitBulkCancelChallans(cancellType, remarks, submitButton, successMessageDiv) {
        const rows = window.bulkCancelRows || [];
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('#cancelChallanForm input[name="_token"]').val();

        submitButton.prop('disabled', true);
        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling selected challans... Do not close.');

        let successCount = 0;
        let failCount = 0;
        const failMessages = [];

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const payload = {
                _token: csrfToken,
                id: row.id,
                enrollment_no: row.sc_enrollment_no || row.sc_app_id || '',
                scChallanStatus: row.sc_challan_status || '',
                scCheckInstallments: row.check_fee_installments || '',
                cancell_type: cancellType,
                remarks: remarks,
                totalEditable: row.invoiceamount || 0
            };

            try {
                const response = await $.ajax({
                    url: '/finance/student-cancel-challans',
                    type: 'POST',
                    data: payload,
                    contentType: 'application/x-www-form-urlencoded'
                });

                if (response && response.success) {
                    successCount++;
                } else {
                    failCount++;
                    failMessages.push(`#${row.id}: ${(response && response.message) ? response.message : 'Failed'}`);
                }
            } catch (xhr) {
                failCount++;
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Request failed';
                failMessages.push(`#${row.id}: ${msg}`);
            }
        }

        const allOk = failCount === 0;
        const summary = allOk
            ? `${successCount} challan(s) cancelled successfully.`
            : `${successCount} cancelled, ${failCount} failed. ${failMessages.slice(0, 3).join(' | ')}`;

        successMessageDiv.html(`
            <div class="alert alert-${allOk ? 'success' : 'warning'} alert-dismissible fade show" role="alert">
                <strong>${allOk ? 'Success!' : 'Completed with issues'}</strong> ${summary}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);

        window.bulkCancelRows = null;
        window.isBulkCancel = false;

        setTimeout(() => {
            $('#challanDetailsModal').modal('hide');
            successMessageDiv.html('');
            searchStudent();
        }, allOk ? 1000 : 2500);

        submitButton.prop('disabled', false);
        submitButton.html('Cancel Now');
    }



    function submitFormBtn(event) {
        event.preventDefault();

        // Validate sessionId
        let sessionId = $('#sessionId').val();
        let studentEnrollment = $('#studentEnrollment').val();

        if (!sessionId) {
            $('#sessionId-error').text('Session is required.');
            return;
        } else {
            $('#sessionId-error').text('');
        }

        if (!studentEnrollment) {
            $('#studentEnrollment-error').text('Student enrollment is required.');
            return;
        } else {
            $('#studentEnrollment-error').text('');
        }

        // Block submit when editing an installment challan and user still selected further installments
        if ($('#verifyEdit').val() == '1'
            && isAlreadyInstallmentChallan($('#scCheckInstallments').val())
            && $('#noOfInstallments').val()) {
            alert('This challan is already an installment. Further installments cannot be created from it.');
            blockFurtherInstallmentsUI();
            return;
        }

        // Edit + Surplus: must confirm apply amount (cannot exceed available)
        if ($('#verifyEdit').val() == '1'
            && $('#surplusAdjustment').is(':checked')
            && (!$('#surplus_apply_amount').val() || !$('#surplus_ffa_id').val())) {
            alert('Please select Surplus Adjustment amount (available surplus) and Confirm before Generate.');
            openSurplusAdjustmentModal();
            return;
        }

        // Edit: fee amounts cannot exceed fee structure
        // Hostel utilities + Fee Adjustment: allow manual amounts (Manual Scholarship style)
        var skipFeeCapsForUtilitiesAdj = $('#verifyEdit').val() == '1'
            && $('#feeType').val() === 'Hostel_other_utilities'
            && $('#manualFeeAdjustment').is(':checked');
        if (!skipFeeCapsForUtilitiesAdj && !validateFeeFieldsAgainstStructureOnEdit()) {
            alert('Fee amount cannot exceed fee structure. Please correct the highlighted field(s).');
            return;
        }

        if (!validateMainChallanDates()) {
            return;
        }

        if (!validateDateFields()) {
            return; // Stop if installment date validation fails
        }

        // Don't allow generating a hostel challan when the hostel fee is 0 / not set.
        if ($('#hostelFeeDiv').is(':visible')) {
            var hostelFeeVal = parseFloat($('#hostelFee').val());
            if (!hostelFeeVal || hostelFeeVal <= 0) {
                $('#successMsg').html(createAlert('error', 'Hostel fee is 0 or not defined — cannot generate the challan. Please ensure the hostel fee structure is set for this student/session.'));
                return;
            }
        }
        // Day-wise: amount per day must be set from fee structure
        if ($('#feeType').val() === 'Hostel_day_wise' && $('#hostel_per_dayDiv').is(':visible')) {
            var perDayVal = parseFloat($('#hostel_per_day').val());
            if (!perDayVal || perDayVal <= 0) {
                $('#successMsg').html(createAlert('error', 'Hostel amount per day is 0 or not defined — cannot generate the challan. Please ensure the hostel fee structure is set.'));
                return;
            }
        }

        // Hostel types: Session Type + Hostel Student Type required
        var feeTypeVal = $('#feeType').val();
        var hostelFeeTypes = ['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'];
        if (hostelFeeTypes.indexOf(feeTypeVal) !== -1) {
            if (!$('#sess_type').val()) {
                $('#sess_type-error').text('Session Type is required for Hostel challan.');
                $('#successMsg').html(createAlert('error', 'Please select Session Type.'));
                return;
            }
            $('#sess_type-error').text('');
            if ($('#existing-hostellite-div').is(':visible') && !$('#hostel_student_type').val()) {
                $('#successMsg').html(createAlert('error', 'Please select Hostel Student Type (Existing / New Applicant).'));
                return;
            }
        }

        // Hostel other utilities create: at least one Include required (matches backend)
        if (feeTypeVal === 'Hostel_other_utilities' && $('#verifyEdit').val() != '1') {
            if (!$('#refrigeratorCheckBox').is(':checked')
                && !$('#electricHeaterCheckBox').is(':checked')
                && !$('#conditionerCheckBox').is(':checked')) {
                $('#successMsg').html(createAlert('error', 'No utility selected. Check at least one Include checkbox.'));
                return;
            }
        }

        // Hostel day-wise: days required, max 365
        if (feeTypeVal === 'Hostel_day_wise') {
            var daysEl = document.getElementById('daysNo');
            var daysVal = parseInt(($('#daysNo').val() || '').trim(), 10);
            if (!daysVal || daysVal < 1) {
                $('#daysNo-error').text('Please enter number of days (1–365).');
                $('#successMsg').html(createAlert('error', 'Please enter number of days (1–365).'));
                return;
            }
            if (daysVal > 365) {
                if (daysEl) {
                    validateHostelDays(daysEl);
                }
                $('#successMsg').html(createAlert('error', 'Days cannot be more than 365.'));
                return;
            }
            $('#daysNo-error').text('');
        }

        // Credit hour Summer Course — same rules as bulk page
        if (feeTypeVal === 'Credit_hours_Summer_Course') {
            var enrSummer = ($('#studentEnrollment').val() || '').trim();
            if (!enrSummer || /^\d+$/.test(enrSummer)) {
                $('#successMsg').html(createAlert('error', 'Enrollment number is required for Credit hour Summer Course (App ID not allowed).'));
                return;
            }
            if ($('#student_type').val() !== 'Existing') {
                $('#successMsg').html(createAlert('error', 'Student type must be Existing for Credit hour Summer Course.'));
                return;
            }
            if (!$('#degree_level').val()) {
                $('#degree_level-error').text('Degree Level is required.');
                $('#successMsg').html(createAlert('error', 'Please select Degree Level.'));
                return;
            }
            $('#degree_level-error').text('');
            // Bulk hardcodes installment NO
            $('#studentInstallment').prop('checked', false);
        }

        // Edit + Hostel Utilities + Fee Adjustment → confirm modal (Manual Scholarship amounts)
        if ($('#verifyEdit').val() == '1'
            && feeTypeVal === 'Hostel_other_utilities'
            && $('#manualFeeAdjustment').is(':checked')
            && !window._hostelUtilitiesFeeAdjConfirmed) {
            if (!openHostelUtilitiesFeeAdjModal()) {
                return;
            }
            return;
        }
        window._hostelUtilitiesFeeAdjConfirmed = false;


        // Get the form element
        var form = $('#singleChallanForm')[0];

        // Check if the form exists
        if (!form) {
            console.error('Form element not found');
            return;
        }

        // Create a new FormData object
        var formData = new FormData(form);
        // console.log('sess_type value:', formData.get('sess_type'));
        var checkboxes = form.querySelectorAll('input[type=checkbox]');
        checkboxes.forEach(function (checkbox) {
            if (!formData.has(checkbox.name)) {
                formData.append(checkbox.name, 'NO');
            }
        });
        // Get the message element
        var successMsg = $('#successMsg');
        // Clear any existing message
        successMsg.html('');
        const submitButton = $('#submitBtn');

        submitButton.prop('disabled', true);
        const isEditSubmit = $('#verifyEdit').val() == '1';
        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + (isEditSubmit ? 'Updating, please wait...' : 'Generating, please wait...'));


        // Make an AJAX request
        $.ajax({
            url: '/finance/submit-single-challan-generate',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                // Handle success response
                $('#editChallan').val('');
                $('#refundEdit').val('');


                if (data.recordIssue || data.already_exists || data.success === false
                    || data.status === 'error' || data.status === 'false' || data.status === false) {
                    successMsg.html(createAlert('error', data.message || 'Unable to generate challan.'));
                    if (data.all_data) {
                        setTimeout(function () {
                            document.getElementById('searchButton').click();
                        }, 1500);
                    }
                    setTimeout(function () {
                        successMsg.html('');
                    }, 5000);
                    submitButton.prop('disabled', false);
                    setSubmitButtonMode($('#verifyEdit').val() == '1');
                    return;

                }
                //    console.log(data.message);

                // document.getElementById("singleChallanForm").reset();
                // Generate success alert HTML and append to successMsg element
                if (data.message == 'contradicted') {

                    successMsg.html(createAlert('error', 'There is contradiction of Student Id and Student type'));
                } else {
                    successMsg.html(createAlert('success', data.message));
                }

                $('#verifyEdit').val('');
                $('#edit_id').val('');
                restoreInstallmentAllowedUI();
                $('#scCheckInstallments').val('');
                $('#surplus_apply_amount').val('');
                $('#surplus_ffa_id').val('');
                $('#surplusAdjustment').prop('checked', false);
                hideSurplusGenerateDisclaimer();
                unlockFormFieldsAfterEdit();

                // // Generate table HTML using the function
                // var tableHTML = generateTable(data.all_data); // Assuming data is a single object
                // $('#showDataInTable').html(tableHTML);
                // $('#dataTable').DataTable();

                // Timeout to hide success message after 4 seconds
                setTimeout(function () {
                    successMsg.html('');
                }, 4000);
                setTimeout(function () {
                    document.getElementById('searchButton').click();
                }, 2000);




                // Re-enable the button and reset text
                submitButton.prop('disabled', false);
                setSubmitButtonMode(false);


            },
            error: function (xhr) {
                // Handle error response
                console.error('Error:', xhr);

                // Attempt to parse response JSON if possible
                try {
                    var response = JSON.parse(xhr.responseText);

                    // Display validation errors if they exist
                    if (response.errors || response.message) {
                        displayValidationErrors(response);
                        displayGeneralError(response);
                        var summaryMsgs = collectResponseErrorMessages(response);
                        successMsg.html(createAlert('error', summaryMsgs.join(' ') || 'Please fix the highlighted fields.'));
                        setTimeout(function () {
                            successMsg.html('');
                        }, 5000);
                    } else {
                        var errMsg = response.message || ('Failed to ' + ($('#verifyEdit').val() == '1' ? 'Update' : 'Generate') + ' Challan');
                        successMsg.html(createAlert('error', errMsg));
                        setTimeout(function () {
                            successMsg.html('');
                        }, 3000);
                    }
                } catch (e) {
                    console.error('Error parsing response JSON:', e);
                    successMsg.html(createAlert('error', 'Failed to ' + ($('#verifyEdit').val() == '1' ? 'Update' : 'Generate') + ' Challan: ' + (xhr.statusText || 'Unknown error')));
                    setTimeout(function () {
                        successMsg.html('');
                    }, 3000);
                }

                submitButton.prop('disabled', false);
                setSubmitButtonMode($('#verifyEdit').val() == '1');


            }
        });
    }

    // Add event listener only if element exists
    const submitFormBtnElement = document.getElementById('submitFormBtn');
    if (submitFormBtnElement) {
        submitFormBtnElement.addEventListener('click', submitFormBtn);
    }

    /** Collect readable error messages (never split a string into characters). */
    function collectResponseErrorMessages(response) {
        var messages = [];
        if (!response) {
            return messages;
        }
        if (response.message && typeof response.message === 'string') {
            messages.push(response.message);
        }
        if (response.errors) {
            if (typeof response.errors === 'string') {
                messages.push(response.errors);
            } else if (Array.isArray(response.errors)) {
                response.errors.forEach(function (val) {
                    messages.push(Array.isArray(val) ? val[0] : String(val));
                });
            } else if (typeof response.errors === 'object') {
                Object.keys(response.errors).forEach(function (key) {
                    var val = response.errors[key];
                    messages.push(Array.isArray(val) ? val[0] : String(val));
                });
            }
        }
        return messages.filter(function (msg, idx, arr) {
            return msg && arr.indexOf(msg) === idx;
        });
    }

    function displayValidationErrors(response) {
        // console.log('Displaying validation errors:', response);

        if (response && response.errors) {
            // Bare string errors belong in successMsg / form-errors — not field spans
            if (typeof response.errors === 'string') {
                return;
            }
            Object.entries(response.errors).forEach(([key, value]) => {
                //  console.log(`Processing error for: ${key}`);
                var trimmedKey = key.trim();
                var errorElement = document.getElementById(trimmedKey + '-error');

                // console.log('Error Element:', errorElement);

                if (errorElement) {
                    var errorMessage = Array.isArray(value) ? value[0] : value;
                    var errorSpan = document.createElement('span');
                    errorSpan.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errorMessage;
                    errorElement.innerHTML = ''; // Clear previous error message
                    errorElement.appendChild(errorSpan);
                    errorElement.classList.add('text-danger'); // Add error styling

                    // Clear the error message after 5 seconds
                    setTimeout(function () {
                        errorElement.innerHTML = '';
                        errorElement.classList.remove('text-danger');
                    }, 7000);
                } else {
                    console.warn(`No element found for ID: ${trimmedKey}-error`);
                }
            });
        } else {
            console.error('Unexpected error structure:', response);
        }
    }
    function displayGeneralError(response) {
        const errorBox = $('#form-errors');
        if (!errorBox.length) {
            return;
        }
        let messages = collectResponseErrorMessages(response);

        if (!messages.length) {
            messages.push('An unknown error occurred.');
        }

        errorBox.html('<div class="alert alert-danger">' + messages.join('<br>') + '</div>');

        setTimeout(function () {
            errorBox.html('');
        }, 6000);
    }


    function createAlert(notificationType, message) {
        // Define the alert color and icon based on the notification type
        let alertColor = '';
        let icon = '';
        switch (notificationType) {
            case 'error':
                alertColor = 'danger';
                icon = '<i class="fa fa-exclamation-circle"></i>';
                break;
            case 'success':
                alertColor = 'success';
                icon = '<i class="fa fa-check-circle"></i>';
                break;
            case 'info':
                alertColor = 'info';
                icon = '<i class="fa fa-info-circle"></i>';
                break;
            case 'warning':
                alertColor = 'warning';
                icon = '<i class="fa fa-exclamation-triangle"></i>';
                break;
            default:
                alertColor = 'secondary';
                icon = '<i class="fa fa-bell"></i>';
                break;
        }

        // Generate the alert HTML
        let alertHTML = `
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-sm-4 col-12">
                    <div class="alert alert-${alertColor} alert-dismissible fade show" role="alert">
                        ${icon} <strong>${notificationType.charAt(0).toUpperCase() + notificationType.slice(1)}:</strong> ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Return the alert HTML
        return alertHTML;
    }


    // Function to handle Enter key press
    function handleEnterKey(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default form submission
            searchStudent(); // Call the searchStudent function
        }
    }

    // Main search function
    function searchStudent() {
        var enrollmentNo = $('#studentEnrollment').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        // Clear any previous error message
        $('#studentEnrollment-error').text('');
        // Edit ke baad search: exit edit mode + clear leftover fee amounts / hostel panels
        exitEditModeAndResetParams();
        resetFormExceptButton();
        unlockFormFieldsAfterEdit();
        restoreInstallmentAllowedUI();
        $('#scCheckInstallments').val('');
        $('#surplus_apply_amount').val('');
        $('#surplus_ffa_id').val('');
        hideSurplusGenerateDisclaimer();
        clearFeeStructureCaps();
        var installmentsDiv = document.getElementById('installmentsDiv');
        installmentsDiv.innerHTML = '';

        // Validate the input
        if (enrollmentNo.trim() === '') {
            $('#studentEnrollment-error').text('Please enter an enrollment number.');
            return;
        }

        const searchReqSeq = ++window._scgSearchReqSeq;
        $('#searchButton').prop('disabled', true);

        // Send the AJAX request
        $.ajax({
            url: '/finance/search-student-challans-available',
            type: 'POST',
            data: {
                studentEnrollment: enrollmentNo
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (allData) {
                if (searchReqSeq !== window._scgSearchReqSeq) {
                    return;
                }

                //console.log(allData.studentRec);
                // Handle success response — default table to Latest Session
                $('#tableSessionFilter').val('latest');
                setStudentChallansForTable(allData.challans, null);
                makeProfileHtml(allData.studentRec);
                $('#student_status').val(allData.studentRec.sai_student_status);
                $('#sessionId').val('');
                $('#student_type').val('Existing');
                // Set fee type without trigger('change') — that was calling getFeeStructure API on every search
                $('#feeType').val('');
                // Ensure edit flags stayed off after profile fill
                $('#verifyEdit').val('');
                $('#edit_id').val('');
                $('#editChallan').val('');
                $('#refundEdit').val('');
                setSubmitButtonMode(false);
                updateProcessingFeeOptionAvailability();
                applyFeeTypeOptionLocks();
                setDefaultDates();

                // Show filter button only if element exists
                const filterChallanBtn = document.getElementById('filterChallanBtn');
                if (filterChallanBtn) {
                    filterChallanBtn.style.display = 'block';
                }

                $('#invoiceAmountOpt').hide();
                $('#scholarshipsAmountOpt').hide();
                // You can update the UI with the response data here
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (searchReqSeq !== window._scgSearchReqSeq) {
                    return;
                }
                // Handle error response
                console.error(textStatus, errorThrown);
                $('#studentEnrollment-error').text('An error occurred while searching. Please try again.');
            },
            complete: function () {
                if (searchReqSeq === window._scgSearchReqSeq) {
                    $('#searchButton').prop('disabled', false);
                }
            }
        });
    }

    function makeProfileHtml(data) {
        let dataDiv = document.getElementById('profileDiv');
        dataDiv.innerHTML = ''; // Clear previous content

        if (data && data.usernameError) {
            // Show error if usernameError exists
            let errorHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            ${data.usernameError}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
            dataDiv.innerHTML = errorHtml;
        } else if (data) {
            // Generate Profile Card

            $('#st_school_id').val(data.school_id);
            $('#st_dept_id').val(data.dept_id);
            $('#st_prog_id').val(data.prog_id);
            $('#st_aai_id').val(data.aai_id);
            // Bulk summer needs degree_level — prefill from program (read-only field)
            if (data.prog_degree_level) {
                $('#degree_level').val(data.prog_degree_level);
            } else {
                $('#degree_level').val('');
            }


            const label = isNaN(data.username) ? 'Enrollment No:' : 'Applicant ID:';

            let imageUrl = data.api_imagename
                ? `https://admission.bnu.edu.pk/images/${data.api_imagename}`
                : `https://cms.bnu.edu.pk/students_pics/${data.username.toUpperCase()}.jpg`;

            let profileHtml = `
        <div class="student-info-card">
            <div class="card-header custom-card-header d-flex align-items-center">
                <i class="fa fa-user mr-2"></i>
                <span class="mb-0">Student Info</span>
            </div>
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center mb-2">
                        <img
                            src="${imageUrl}"
                            alt="Student Image"
                            class="img-fluid rounded-circle shadow-sm mb-2"
                            style="width: 80px; height: 80px; object-fit: cover;">
                        <h6 class="text-uppercase font-weight-bold mb-1" style="font-size: 1rem; color: #4454c3;">${data.spi_name || 'N/A'}</h6>
                        <span class="badge badge-info">${data.sai_student_status || 'N/A'}</span>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                  ${generateInfoBox(
                'fa-id-card',
                'Enrollment No / Applicant ID:',
                `${data.username && isNaN(data.username) ? data.username : 'N/A'} / ${data.aai_id && !isNaN(data.aai_id) ? data.aai_id : 'N/A'}`,
                'enrollment-appid'
            )}
                    ${generateInfoBox(
                'fa-envelope',
                'Email:',
                !isNaN(data.username)
                    ? data.spi_email || 'N/A'
                    : `${data.username || 'N/A'}@bnu.edu.pk`,
                'email-address'
            )}
                            ${generateInfoBox('fa-phone', 'Mobile:', data.spi_mobile || 'N/A', 'mobile-number')}
                            ${generateInfoBox('fa-building', 'School:', data.school_name || 'N/A', 'school-name')}
                            ${generateInfoBox('fa-university', 'Department:', data.dept_name || 'N/A', 'department-name')}
                            ${generateInfoBox('fa-book', 'Program:', data.prog_name || 'N/A', 'program-name')}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

            dataDiv.innerHTML = profileHtml;
        } else {
            // Show a generic error message if data does not meet the criteria
            let noDataHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            No data available
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
            dataDiv.innerHTML = noDataHtml;
        }
    }

    // Helper Function to Generate Compact Info Boxes
    function generateInfoBox(iconClass, label, value, specialClass = '') {
        return `
    <div class="col-md-4 mb-3">
        <div class="student-info-item d-flex align-items-center">
            <i class="fa ${iconClass} mr-3 text-muted" style="font-size: 1.1rem;"></i>
            <div>
                <small>${label}</small>
                <strong class="${specialClass}" style="font-size: 0.9rem; color: #1f2937;">${value}</strong>
            </div>
        </div>
    </div>`;
    }

    function clearFormAndContent() {
        editHostelUtilitiesRow = null;
        // Reset the form
        document.getElementById('singleChallanForm').reset();

        // Clear the content of profileDiv
        let dataDiv = document.getElementById('profileDiv');
        let showDataInTable = document.getElementById('showDataInTable');
        dataDiv.innerHTML = '';
        showDataInTable.innerHTML = '';
        window.location.reload();
    }

    function parseInputValue(id) {
        let value = document.getElementById(id).value;
        let parsedValue = parseFloat(value);
        return isNaN(parsedValue) ? 0 : parsedValue;
    }

    function calculateAndDisplayInstallments(selectElement) {
        // Check if the student enrollment field is empty
        var studentEnrollment = document.getElementById('studentEnrollment').value.trim();
        if (!studentEnrollment) {
            alert("Kindly enter the enrollment number and search for the student first.");
            return;
        }

        var numInstallments = parseInt(selectElement.value);
        var installmentsDiv = document.getElementById('installmentsDiv');

        if (!numInstallments) {
            installmentsDiv.innerHTML = '';
            return;
        }

        // Editing an already-split installment challan — block further installments
        var isEditMode = document.getElementById('verifyEdit').value == '1';
        var checkFeeInstallments = document.getElementById('scCheckInstallments').value;
        if (isEditMode && isAlreadyInstallmentChallan(checkFeeInstallments)) {
            alert('This challan is already an installment. Further installments cannot be created from it.');
            selectElement.value = '';
            installmentsDiv.innerHTML = '';
            return;
        }

        // Get input values and parse them to numbers
        let tuitionFee = parseInputValue('tuitionFee');
        let admissionFee = parseInputValue('admissionFee');
        let securityFee = parseInputValue('securityFee');
        let medicalFee = parseInputValue('medicalFee');
        let activityCharges = parseInputValue('activitychargesAmt');
        let studentInvoiceamount = parseInputValue('studentInvoiceamount');

        // Calculate total invoice amount
        if (!studentInvoiceamount) {
            studentInvoiceamount = tuitionFee;
        }

        var invoiceAmount = studentInvoiceamount - (admissionFee + securityFee + medicalFee + activityCharges);

        // Calculate installment amount
        var tuitionPerInstallment = Math.ceil(invoiceAmount / numInstallments);

        // Get today's date
        var today = new Date();

        // Display results
        var installmentsDiv = document.getElementById('installmentsDiv');
        installmentsDiv.innerHTML = ''; // Clear previous content

        var tableHtml = `
        <table class="table table-bordered table-striped table-hover w-100 border border-info p-2">
             <thead class="font-weight-bold"
             style="background-color: #C7C8CC;">
                <tr>
                    <th>Installment</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Validate Date</th>
                </tr>
            </thead>
            <tbody>
    `;

        for (var i = 0; i < numInstallments; i++) {
            var installmentAmount = i === 0
                ? tuitionPerInstallment + admissionFee + securityFee + medicalFee + activityCharges
                : tuitionPerInstallment;

            // Calculate the due date and validate date
            var dueDate = new Date(today);
            dueDate.setMonth(dueDate.getMonth() + i); // Due date is one month apart for each installment

            var validateDate = new Date(dueDate);
            validateDate.setDate(dueDate.getDate() + 7); // Validate date is one week after the due date

            // Format dates to YYYY-MM-DD (local, not UTC)
            var dueDateFormatted = formatDate(dueDate);
            var validateDateFormatted = formatDate(validateDate);

            tableHtml += `
            <tr>
                <td>Installment ${i + 1}</td>
                <td>
                    <input type="text" readonly class="form-control" value="${installmentAmount}" oninput="validateInput(this)">
                </td>
                <td>
                    <input type="date" class="form-control" name="installmentDueDate[]" value="${dueDateFormatted}" >
                    <span id="dueDate-error-${i}" class="error text-danger"></span>
                </td>
                <td>
                    <input type="date" class="form-control" name="installmentValidateDate[]" value="${validateDateFormatted}" >
                    <span id="validateDate-error-${i}" class="error text-danger"></span>
                </td>
            </tr>
        `;
        }

        tableHtml += `
            <tr>
                <td colspan="3"><strong>Total Amount Payable</strong></td>
                <td>
                    <input type="hidden" name="invoiceAmount" value="${studentInvoiceamount}">
                    <strong>${studentInvoiceamount}</strong>
                </td>
            </tr>
            </tbody>
        </table>
    `;

        installmentsDiv.innerHTML = tableHtml;
    }


    /** Main Due Date / Validate Date (required for generate, incl. Processing Fee) */
    function validateMainChallanDates() {
        let ok = true;
        const due = ($('#dueDate').val() || '').trim();
        const valid = ($('#validateDate').val() || '').trim();
        const dueErr = document.getElementById('dueDate-error');
        const validErr = document.getElementById('validateDate-error');

        if (dueErr) {
            dueErr.innerHTML = '';
        }
        if (validErr) {
            validErr.innerHTML = '';
        }

        if (!due) {
            if (dueErr) {
                dueErr.innerHTML = '<i class="fa fa-exclamation-circle"></i> Due date is required.';
            }
            ok = false;
        }
        if (!valid) {
            if (validErr) {
                validErr.innerHTML = '<i class="fa fa-exclamation-circle"></i> Validate date is required.';
            }
            ok = false;
        }
        if (due && valid && valid < due) {
            if (validErr) {
                validErr.innerHTML = '<i class="fa fa-exclamation-circle"></i> Validate date must be on or after due date.';
            }
            ok = false;
        }

        if (!ok) {
            $('#successMsg').html(createAlert('error', 'Please select Due Date and Validate Date.'));
            setTimeout(function () {
                $('#successMsg').html('');
            }, 4000);
        }

        return ok;
    }

    function validateDateFields() {
        // Check if all installment date fields are filled
        const dueDates = document.getElementsByName('installmentDueDate[]');
        const validateDates = document.getElementsByName('installmentValidateDate[]');
        let allFieldsFilled = true;

        for (let i = 0; i < dueDates.length; i++) {
            const dueDateError = document.getElementById(`dueDate-error-${i}`);
            const validateDateError = document.getElementById(`validateDate-error-${i}`);

            if (dueDates[i].value === '') {
                if (dueDateError) {
                    dueDateError.textContent = 'Due date is required.';
                }
                allFieldsFilled = false;
            } else if (dueDateError) {
                dueDateError.textContent = '';
            }

            if (validateDates[i].value === '') {
                if (validateDateError) {
                    validateDateError.textContent = 'Validate date is required.';
                }
                allFieldsFilled = false;
            } else if (validateDateError) {
                validateDateError.textContent = '';
            }
        }

        return allFieldsFilled;
    }


    function setAttributes(attributeName, value) {
        const inputFieldIds = [
            '#studentInvoiceamount',
            '#scholarshipsAmount',
            '#activitychargesAmt',
            '#tuitionFee',
            '#admissionFee',
            '#securityFee',
            '#medicalFee',
            '#dueDate',
            '#validateDate'
        ];

        const selectFieldIds = [
            '#sessionId',
            '#student_type'
        ];

        // Apply readonly attribute to input fields
        inputFieldIds.forEach(fieldId => {
            if (value) {
                $(fieldId).attr('readonly', true);
            } else {
                $(fieldId).removeAttr('readonly');
            }
        });

        // Make select fields behave as readonly
        selectFieldIds.forEach(fieldId => {
            if (value) {
                $(fieldId).on('focus', function () {
                    $(this).data('previous', $(this).val());
                }).on('change', function () {
                    $(this).val($(this).data('previous'));
                });
            } else {
                $(fieldId).off('focus change');
            }
        });
    }

    // Call the function with 'readonly' attribute
    function applyReadonly() {
        setAttributes(true);
    }


    // Lock / unlock the Session Type dropdown WITHOUT using `disabled` — a
    // disabled <select> is dropped from FormData, which would lose sess_type at
    // challan generation. pointer-events:none keeps it submittable but
    // non-interactive when a hostel is assigned.
    function setSessTypeLocked(locked) {
        var el = $('#sess_type');
        if (locked) {
            el.css({ 'pointer-events': 'none', 'background-color': '#e9ecef' }).attr('tabindex', '-1').data('locked', true);
        } else {
            el.css({ 'pointer-events': '', 'background-color': '' }).removeAttr('tabindex').data('locked', false);
        }
    }
    function getFeeStructure() {
        // let selectedValue = selectElement.value;
        let selectedValue = $('#feeType').val();
        let sess_type_val = $('#sess_type').val();
        let roadmap_val = $('#roadmap-sess').val();
        let semester_val = $('#semester').val();
        // Do not force-show hostel student-type filter here — each fee type sets its own panels below

        console.log(roadmap_val);
        console.log(semester_val);

        // Do not call fee-structure API until Fee Type is selected
        // (Student Type / empty fee type must not trigger this request)
        if (!selectedValue) {
            $('.credit_hr_div, .credit_hr_summer_div').addClass('d-none');
            $('#degree_level').off('mousedown.scgDegreeLock');
            $('#full_semester_dev').hide();
            hideElementsById([
                'hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv',
                'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv',
                'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div'
            ]);
            return;
        }

        // Processing Fee availability (F/S enrollment → option disabled)
        updateProcessingFeeOptionAvailability();

        // Processing Fee UI locks (only Surplus stays enabled)
        applyFeeTypeOptionLocks();

        // Hostel: show correct panels immediately (don't wait for AJAX)
        const allHostelPanels = [
            'hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv',
            'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv',
            'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div'
        ];
        if (selectedValue === 'Hostel_Full_semster') {
            hideElementsById(allHostelPanels);
            showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
        } else if (selectedValue === 'Hostel_day_wise') {
            hideElementsById(allHostelPanels);
            showElementsById(['hostel_per_dayDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
        } else if (selectedValue === 'Hostel_other_utilities') {
            hideElementsById(allHostelPanels);
            showElementsById(['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv']);
            // Utilities fee structure is not filtered by hostel student type
            $('#hostel_student_type').val('');
        } else if (selectedValue !== 'Hostel_Fee_Refund') {
            // non-hostel: keep existing-hostellite hidden unless a hostel type re-shows it
            if (['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'].indexOf(selectedValue) === -1) {
                hideElementsById(['existing-hostellite-div']);
            }
        }

        if (selectedValue == 'Processing_Fee') {
            if (enrollmentBlocksProcessingFee()) {
                $('#feeType').val('');
                $('#fee_type-error').text('Processing Fee is not allowed for enrollment numbers starting with F or S.');
                return;
            }
            $('#fee_type-error').text('');
            $('#full_semester_dev').hide();
            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
            hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);
            return; // Create uses processingFee_challan PHP — no fee-structure amounts needed
        }

        if (selectedValue == 'Credit_hours') {
            // Procedure fin_credit_hr_wise_challan uses creditHr — no fin_fee_structure call
            $('.credit_hr_div').removeClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
            $('#full_semester_dev').hide();
            hideElementsById(allHostelPanels);
            $('#tuitionFee').attr('readonly', true);
            $('#medicalFee').attr('readonly', true);
            $('#admissionFee').attr('readonly', true);
            $('#activitychargesAmt').attr('readonly', true);
            $('#securityFee').attr('readonly', true);
            applyFeeTypeOptionLocks();
            return;
        } else if (selectedValue == 'Credit_hours_Summer_Course') {
            // Bulk summer: no creditHr input — procedure uses enrollment + degree_level
            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').removeClass('d-none');
            $('#full_semester_dev').hide();
            hideElementsById(allHostelPanels);
            // Same as bulk: Existing only, installment NO
            $('#student_type').val('Existing');
            $('#studentInstallment').prop('checked', false);
            // Block mousedown so dropdown cannot open (value still posts)
            $('#degree_level').off('mousedown.scgDegreeLock').on('mousedown.scgDegreeLock', function (e) {
                e.preventDefault();
            });
            applyFeeTypeOptionLocks();
            return; // No fee-structure AJAX (same as bulk — procedure computes amounts)
        } else if (selectedValue == 'Fee_Refund') {
            hideElementsById(allHostelPanels);
            $('#full_semester_dev').show();
            $('#tuitionFee').val('');
            $('#medicalFee').val('');
            $('#admissionFee').val('');
            $('#activitychargesAmt').val('');
            $('#securityFee').val('');

            // Remove readonly attribute from the fields
            $('#tuitionFee').removeAttr('readonly');
            $('#medicalFee').removeAttr('readonly');
            $('#admissionFee').removeAttr('readonly');
            $('#activitychargesAmt').removeAttr('readonly');
            $('#securityFee').removeAttr('readonly');

            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
            return; // Don't make AJAX call for refund
        } else if (selectedValue == 'Hostel_Fee_Refund') {
            $('#full_semester_dev').hide();
            hideElementsById(allHostelPanels);

            // Show only hostel fee and security fee fields
            showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv']);

            // Clear values
            $('#hostelFee').val('');
            $('#hostel_security_fee').val('');

            // Prod: Hostel Fee Refund — both fields editable
            $('#hostelFee').removeAttr('readonly');
            $('#hostel_security_fee').removeAttr('readonly');

            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
            return; // Don't make AJAX call for refund
        } else {
            // Add readonly attribute back to the fields
            // $('#tuitionFee').attr('readonly', true);
            // $('#medicalFee').attr('readonly', true);
            // $('#admissionFee').attr('readonly', true);
            // $('#activitychargesAmt').attr('readonly', true);
            // $('#securityFee').attr('readonly', true);
            $('#full_semester_dev').hide();
            $('.credit_hr_div').addClass('d-none');
            $('.credit_hr_summer_div').addClass('d-none');
        }
        let feeType = selectedValue === 'Hostel_day_wise' || selectedValue === 'Hostel_Full_semster' || selectedValue === 'Hostel_other_utilities' ? 'Hostel' : selectedValue;
        let sess_type = selectedValue === 'Hostel_day_wise' || selectedValue === 'Hostel_Full_semster' || selectedValue === 'Hostel_other_utilities' ? sess_type_val : '';
        let roadmap = selectedValue === 'Full_semester' || selectedValue === 'Short/Summer_Course' ? roadmap_val : '';
        let semester = selectedValue === 'Full_semester' || selectedValue === 'Short/Summer_Course' ? semester_val : '';

        var successMsg = $('#successMsg');
        // Clear any existing message
        successMsg.html('');
        // Retrieve other form data
        let school_id = $('#st_school_id').val();
        let dept_id = $('#st_dept_id').val();
        let prog_id = $('#st_prog_id').val();
        let sess_id = $('#sessionId').val();
        let student_type = $('#student_type').val();
        let hostel_student_type = $('#hostel_student_type').val();
        // Other Utilities: never filter fee structure by hostel student type
        if (selectedValue === 'Hostel_other_utilities') {
            hostel_student_type = '';
            $('#hostel_student_type').val('');
        }

        if (['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'].indexOf(selectedValue) !== -1) {
            if (!sess_id) {
                successMsg.html(createAlert('error', 'Please select Session first for hostel fee structure.'));
                return;
            }
            if (!sess_type) {
                sess_type = 'Regular';
                $('#sess_type').val('Regular');
            }
        }

        // Create the data object to be sent with the request
        let requestData = {
            fee_type: feeType,
            fee_type_for_hostel: selectedValue,
            school_id: school_id,
            dept_id: dept_id,
            prog_id: prog_id,
            sess_id: sess_id,
            student_type: student_type,
            hostel_student_type: hostel_student_type,
            sess_type: sess_type,
            student_id: $('#studentEnrollment').val(),
            roadmap: roadmap,
            semester: semester,
        };
        // console.log(requestData);
        // Ignore stale fee-structure responses (fee type / edit session changed)
        const feeStructureReqSeq = ++window._scgFeeStructureReqSeq;
        const feeTypeAtRequest = selectedValue;
        // Send the AJAX request
        $.ajax({
            url: '/finance/student-get-fee-structure',
            type: 'GET',
            data: requestData,
            success: function (response) {
                if (feeStructureReqSeq !== window._scgFeeStructureReqSeq) {
                    return;
                }
                if ($('#feeType').val() !== feeTypeAtRequest) {
                    return;
                }

                if (response.data && response.status === 'success') {
                    let rowData = response.data;
                    //    console.log();
                    // Edit mode: keep challan due/validate dates (do not overwrite with today+7)
                    if ($('#verifyEdit').val() != '1') {
                        setDefaultDates();
                    }

                    hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);

                    // Conditional logic based on feeType
                    if ($('#feeType').val() === 'Hostel_Full_semster') {

                        showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

                    } else if ($('#feeType').val() === 'Hostel_day_wise') {

                        showElementsById(['hostel_per_dayDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

                    } else if ($('#feeType').val() === 'Hostel_other_utilities') {

                        showElementsById(['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv']);
                        hideElementsById(['existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

                    } else if ($('#feeType').val() === 'Hostel_Fee_Refund') {

                        showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv']);
                        $('#hostelFee').removeAttr('readonly');
                        $('#hostel_security_fee').removeAttr('readonly');

                    } else {
                        // Optional: Handle case where feeType doesn't match any of the above
                        //console.log('No valid feeType found');
                    }




                    if (feeType === 'Full_semester' || feeType === 'Short/Summer_Course') {

                        $('#full_semester_dev').show();
                        $('.roadmap-semester-div').show();
                        $('#roadmap-sess').val(response.roadmap || '');//If response.roadmap is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an the latest session which is 165 for now because we are doing same in controller (getting lestest session Fee Structure is empty is passsed at the first attempt).
                        $('#semester').val(response.semester || '');//If response.semester is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an first semester  because we are doing same in controller (getting 1 semsters Fee Structure is empty is passsed at the first attempt).
                        // Always store fee-structure caps (edit max validation)
                        setFeeStructureCapsFromResponse(rowData);
                        // New generate: fill fee fields from structure. Edit: keep challan values, only enforce caps.
                        if ($('#verifyEdit').val() != '1') {
                            $('#tuitionFee').val(rowData.ffs_tutionfee_amt || '');
                            $('#medicalFee').val(rowData.ffs_medical_amt || '');
                            $('#admissionFee').val(rowData.ffs_admission_amt || '');
                            $('#activitychargesAmt').val(rowData.ffs_activity_amt || '');
                            $('#securityFee').val(rowData.ffs_security_amt || '');
                        }
                        // Do not re-show installment split UI when editing an already-split challan
                        if (!($('#verifyEdit').val() == '1' && isAlreadyInstallmentChallan($('#scCheckInstallments').val()))) {
                            document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'block', 'important');
                        } else {
                            blockFurtherInstallmentsUI();
                        }
                        //$('#noOfInstallments').closest('.form-group').show();

                    } else {
                        $('#full_semester_dev').hide();
                        // $('#hostel_dev').show();

                        setFeeStructureCapsFromResponse(rowData);
                        if ($('#verifyEdit').val() != '1') {
                            $('#hostelFee').val(rowData.fhfs_amount_per_semester || '');
                            $('#hostel_per_day').val(rowData.fhfs_amount_per_day || '');
                            $('#hostel_per_day').attr('readonly', true);
                            $('#refrigeratorFee').val(rowData.fhfs_refrigerator_fee || '');
                            $('#electricFee').val(rowData.fhfs_heater_fee || '');
                            $('#conditionerFee').val(rowData.fhfs_ac_fee || '');
                            //$('#daysNo').val(rowData.ffs_security_amt || '');
                            $('#hostel_security_fee').val(rowData.fhfs_hostel_security_fee || '');
                        } else if ($('#feeType').val() === 'Hostel_other_utilities') {
                            // Edit: restore challan utility amounts (fee structure must not wipe them)
                            applyHostelUtilitiesFromEditRow(editHostelUtilitiesRow);
                            if (!$('#refrigeratorFee').val()) {
                                $('#refrigeratorFee').val(rowData.fhfs_refrigerator_fee || '');
                            }
                            if (!$('#electricFee').val()) {
                                $('#electricFee').val(rowData.fhfs_heater_fee || '');
                            }
                            if (!$('#conditionerFee').val()) {
                                $('#conditionerFee').val(rowData.fhfs_ac_fee || '');
                            }
                            // If DB breakdown missing, tick Include for fee-structure combo that matches invoice
                            var hasStoredBreakdown = editHostelUtilitiesRow && (
                                parseFloat(pickRowAmount(editHostelUtilitiesRow, ['sc_refrigerator', 'SC_REFRIGERATOR'])) > 0 ||
                                parseFloat(pickRowAmount(editHostelUtilitiesRow, ['sc_electric', 'SC_ELECTRIC'])) > 0 ||
                                parseFloat(pickRowAmount(editHostelUtilitiesRow, ['sc_conditioner', 'SC_CONDITIONER'])) > 0
                            );
                            if (editHostelUtilitiesRow && !hasStoredBreakdown) {
                                var inv = parseFloat(editHostelUtilitiesRow.invoiceamount) || 0;
                                var f = parseFloat($('#refrigeratorFee').val()) || 0;
                                var e = parseFloat($('#electricFee').val()) || 0;
                                var c = parseFloat($('#conditionerFee').val()) || 0;
                                var opts = [
                                    { r: 1, e: 1, c: 1, sum: f + e + c },
                                    { r: 1, e: 1, c: 0, sum: f + e },
                                    { r: 1, e: 0, c: 1, sum: f + c },
                                    { r: 0, e: 1, c: 1, sum: e + c },
                                    { r: 1, e: 0, c: 0, sum: f },
                                    { r: 0, e: 1, c: 0, sum: e },
                                    { r: 0, e: 0, c: 1, sum: c }
                                ];
                                for (var oi = 0; oi < opts.length; oi++) {
                                    if (inv > 0 && opts[oi].sum > 0 && Math.abs(opts[oi].sum - inv) < 1) {
                                        $('#refrigeratorCheckBox').prop('checked', !!opts[oi].r && f > 0);
                                        $('#electricHeaterCheckBox').prop('checked', !!opts[oi].e && e > 0);
                                        $('#conditionerCheckBox').prop('checked', !!opts[oi].c && c > 0);
                                        break;
                                    }
                                }
                            }
                        }
                        // Edit Full / day-wise: keep fee+security editable (prod); per-day rate read-only
                        if ($('#verifyEdit').val() == '1') {
                            var htEdit = $('#feeType').val();
                            if (htEdit === 'Hostel_Full_semster' || htEdit === 'Hostel_day_wise') {
                                $('#hostelFee, #hostel_security_fee').removeAttr('readonly');
                            }
                            $('#hostel_per_day').attr('readonly', true);
                        } else {
                            // New generate: amounts come from structure
                            $('#hostelFee, #hostel_security_fee, #hostel_per_day').attr('readonly', true);
                        }
                        $('#scholarshipsAmountOpt').hide();
                        // hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'refrigeratorFeeDiv','electricFeeDiv','conditionerFeeDiv','daysNoDiv']);
                    }

                } else {
                    hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'hostelRemarksDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div', 'roadmap-sess-Div', 'semester-Div']);

                    // Clear the form fields and show appropriate messages (skip wipe while editing utilities)
                    const keepEditUtilities = $('#verifyEdit').val() == '1' && selectedValue === 'Hostel_other_utilities';
                    if (!keepEditUtilities) {
                        $('#tuitionFee').val('');
                        $('#medicalFee').val('');
                        $('#admissionFee').val('');
                        $('#activitychargesAmt').val('');
                        $('#securityFee').val('');
                        $('#hostelFee').val('');
                        $('#hostel_per_day').val('');
                        $('#hostel_security_fee').val('');
                        $('#refrigeratorFee').val('');
                        $('#electricFee').val('');
                        $('#conditionerFee').val('');
                        $('#daysNo').val('');
                        $('#hostelRemarks').val('');
                    }
                    if (selectedValue === 'Hostel_Full_semster') {
                        showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');
                    } else if (selectedValue === 'Hostel_day_wise') {
                        showElementsById(['hostel_per_dayDiv', 'daysNoDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv', 'existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');
                    } else if (selectedValue === 'Hostel_other_utilities') {
                        showElementsById(['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostelRemarksDiv', 'hostel-sess-typeDiv']);
                        hideElementsById(['existing-hostellite-div']);
                        $('#sess_type').val(response.sess_type || 'Regular');
                        if (keepEditUtilities) {
                            applyHostelUtilitiesFromEditRow(editHostelUtilitiesRow);
                        }
                    } else if (feeType == 'Hostel_Fee_Refund' || selectedValue == 'Hostel_Fee_Refund') {
                        showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv']);
                        $('#hostelFee').removeAttr('readonly');
                        $('#hostel_security_fee').removeAttr('readonly');
                    } else if (feeType == 'Full_semester' || feeType == 'Short/Summer_Course') {
                        $('#full_semester_dev').show();
                        $('.roadmap-semester-div').show();
                        $('#roadmap-sess').val(response.roadmap || '');//If response.roadmap is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an the latest session which is 165 for now because we are doing same in controller (getting lestest session Fee Structure is empty is passsed at the first attempt).
                        $('#semester').val(response.semester || '');//If response.semester is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an first semester  because we are doing same in controller (getting 1 semsters Fee Structure is empty is passsed at the first attempt).
                        $('#noOfInstallments').closest('.form-group').hide();
                    }
                    successMsg.html(createAlert('error', response.message));
                    setTimeout(function () {
                        successMsg.html('');
                    }, 4000);
                    console.warn('No data found or error in response');
                }

                // Lock the Session Type to the assigned-hostel value; editable
                // only when the student has no hostel assigned for the session.
                if (['Hostel_Full_semster', 'Hostel_day_wise', 'Hostel_other_utilities'].indexOf($('#feeType').val()) !== -1) {
                    if (response.sess_type) { $('#sess_type').val(response.sess_type); }
                    setSessTypeLocked(!!response.hostel_assigned);
                } else {
                    setSessTypeLocked(false);
                }
            },
            error: function (xhr, status, error) {
                if (feeStructureReqSeq !== window._scgFeeStructureReqSeq) {
                    return;
                }
                if ($('#feeType').val() !== feeTypeAtRequest) {
                    return;
                }
                // Handle any errors
                console.error('AJAX Error:', status, error);
                // Clear the form fields
                $('#tuitionFee').val('');
                $('#medicalFee').val('');
                $('#admissionFee').val('');
                $('#activitychargesAmt').val('');
                $('#securityFee').val('');

            }
        });
    }



    function scrollToInstallments() {
        // Layout scrolls inside .app-content (html/body are overflow:hidden)
        const installmentFocus = $('#installmentFocus');
        if (!installmentFocus.length) {
            return;
        }

        // Leave Edit button focus so highlight does not stay on the table row
        if (document.activeElement && typeof document.activeElement.blur === 'function') {
            document.activeElement.blur();
        }

        const $scrollParent = $('.app-content').first();
        const offsetTop = installmentFocus.offset()
            ? (installmentFocus.offset().top - ($scrollParent.offset() ? $scrollParent.offset().top : 0) + ($scrollParent.scrollTop() || 0) - 24)
            : 0;

        const afterScroll = function () {
            installmentFocus.addClass('focused-border');
            setTimeout(() => {
                installmentFocus.removeClass('focused-border');
            }, 3000);

            // Move keyboard focus into Challan Parameters (first usable field)
            const $focusTarget = $('#dueDate:visible:not([readonly]), #sessionId:visible, #feeType:visible').first();
            if ($focusTarget.length) {
                $focusTarget.trigger('focus');
            }
        };

        if ($scrollParent.length) {
            $scrollParent.stop(true).animate({ scrollTop: Math.max(0, offsetTop) }, 600, afterScroll);
        } else {
            $('html, body').stop(true).animate({
                scrollTop: installmentFocus.offset().top - 100
            }, 600, afterScroll);
        }
    }


    // Function to hide elements based on the passed IDs (array of IDs)
    function hideElementsById(idsArray) {
        // Loop through each ID in the array
        idsArray.forEach(function (id) {
            var element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';  // Hide the element
            }
        });
    }

    // Function to show elements based on the passed IDs (array of IDs)
    function showElementsById(idsArray) {
        // Loop through each ID in the array
        idsArray.forEach(function (id) {
            var element = document.getElementById(id);
            if (element) {
                element.style.display = 'block';  // Show the element
            }
        });
    }
    function validateRemarks(input) {
        const errorSpan = document.getElementById('hostelRemarks-error');
        if (!errorSpan) return; // Exit if element doesn't exist

        const maxLength = 200;

        // Optional — only enforce max length
        if (input.value.length > maxLength) {
            errorSpan.textContent = `Remarks cannot exceed ${maxLength} characters.`;
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            errorSpan.textContent = "";
            input.classList.remove('is-invalid');
            if (input.value.trim().length > 0) {
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
            }
        }
    }

</script>
@stop
