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

    #summary-data-table thead th.group-header {
        text-align: center !important;
        vertical-align: middle !important;
        font-weight: 700;
        color: #1f2937;
        border-bottom: 2px solid #94a3b8;
    }

    .summaryTable.grouped-active .dataTables_scrollHead th.group-header {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .summaryTable.grouped-active .dataTables_scrollHead th.group-header-existing,
    .summaryTable.grouped-active .dataTables_scrollHead th.metric-existing {
        background-color: #eff6ff !important;
    }

    .summaryTable.grouped-active .dataTables_scrollHead th.group-header-fall,
    .summaryTable.grouped-active .dataTables_scrollHead th.metric-fall {
        background-color: #fff7ed !important;
    }

    .summaryTable.grouped-active .dataTables_scrollHead th.group-header-spring,
    .summaryTable.grouped-active .dataTables_scrollHead th.metric-spring {
        background-color: #f0fdf4 !important;
    }

    .summaryTable.grouped-active .dataTables_scrollHead th.group-header-consolidated,
    .summaryTable.grouped-active .dataTables_scrollHead th.metric-consolidated {
        background-color: #f5f3ff !important;
    }

    #summary-data-table thead th.group-header-existing,
    #summary-data-table thead th.metric-existing,
    #summary-data-table tbody td.group-data-existing {
        background-color: #eff6ff !important;
    }

    #summary-data-table thead th.group-header-fall,
    #summary-data-table thead th.metric-fall,
    #summary-data-table tbody td.group-data-fall {
        background-color: #fff7ed !important;
    }

    #summary-data-table thead th.group-header-spring,
    #summary-data-table thead th.metric-spring,
    #summary-data-table tbody td.group-data-spring {
        background-color: #f0fdf4 !important;
    }

    #summary-data-table thead th.group-header-consolidated,
    #summary-data-table thead th.metric-consolidated,
    #summary-data-table tbody td.group-data-consolidated {
        background-color: #f5f3ff !important;
    }

    #summary-data-table thead th.metric-header {
        text-align: center;
        white-space: nowrap;
        font-size: 12px;
    }

    #summary-data-table .group-col-start {
        border-left: 3px solid #4454c3 !important;
    }

    #summary-data-table .group-col-end {
        border-right: 2px solid #94a3b8 !important;
    }

    #summary-data-table.grouped-mode {
        min-width: 3400px;
    }

    #summary-data-table.grouped-mode th,
    #summary-data-table.grouped-mode td {
        min-width: 88px;
    }

    #summary-data-table.grouped-mode th:nth-child(1),
    #summary-data-table.grouped-mode td:nth-child(1) {
        min-width: 95px;
    }

    #summary-data-table.grouped-mode th:nth-child(2),
    #summary-data-table.grouped-mode td:nth-child(2) {
        min-width: 220px;
    }

    #summary-data-table.grouped-mode th:nth-child(3),
    #summary-data-table.grouped-mode td:nth-child(3) {
        min-width: 180px;
    }

    #summary-data-table.grouped-mode th:nth-child(4),
    #summary-data-table.grouped-mode td:nth-child(4) {
        min-width: 260px;
    }

    #summary-data-table.grouped-mode.school-view {
        min-width: 2800px;
    }

    #summary-data-table.grouped-mode.school-view th:nth-child(2),
    #summary-data-table.grouped-mode.school-view td:nth-child(2) {
        min-width: 280px;
    }

    #summary-data-table.school-view th:nth-child(2),
    #summary-data-table.school-view td:nth-child(2) {
        min-width: 280px;
    }

    .summary-table-wrapper {
        width: 100%;
        overflow: hidden;
    }

    .summary-table-wrapper.grouped-scroll {
        overflow: hidden;
    }

    .summaryTable.grouped-active .dataTables_wrapper {
        position: relative;
    }

    .summaryTable.grouped-active .dataTables_scrollHead,
    .summaryTable.grouped-active .dataTables_scrollFoot {
        overflow: hidden !important;
    }

    .summaryTable.grouped-active .dataTables_scrollBody {
        overflow-x: scroll !important;
        overflow-y: auto !important;
        max-height: calc(100vh - 300px) !important;
        border-bottom: 2px solid #cbd5e1;
    }

    .summaryTable.grouped-active .dataTables_scrollBody::-webkit-scrollbar {
        height: 14px;
        width: 14px;
    }

    .summaryTable.grouped-active .dataTables_scrollBody::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .summaryTable.grouped-active .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 7px;
        border: 2px solid #f1f5f9;
    }

    .summaryTable.summary-scroll-active .dataTables_scrollBody {
        overflow-x: scroll !important;
        overflow-y: auto !important;
        max-height: calc(100vh - 300px) !important;
        border-bottom: 2px solid #cbd5e1;
    }

    .summaryTable.summary-scroll-active .dataTables_scrollBody::-webkit-scrollbar {
        height: 14px;
        width: 14px;
    }

    .summaryTable.summary-scroll-active .dataTables_scrollBody::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .summaryTable.summary-scroll-active .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 7px;
        border: 2px solid #f1f5f9;
    }
</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card-header" style="width:100%;">
        <form id="feeForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Challan Summary Report:</h3></div>

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

                    <div class="form-group col-sm-6 deptDiv d-none">
                        <label for="department">Department</label>
                        <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                        </select>
                    </div>
                    <div class="form-group col-sm-6 degDiv d-none">
                        <label for="course">Degree Level</label>
                        <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                        </select>
                    </div>

                    <div class="form-group col-sm-6 progDiv d-none">
                    <label for="course">Program</label>
                    <select name="program" disabled id="program" class="form-control">
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
                    <div class="form-group col-sm-6">
                        <label for="studentType">Student Type</label>
                        <select name="student_type" id="studentTypeOption" class="form-control">
                            <option value="">Select type</option>
                            <option value="Existing">Existing</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="studentType">Fee Type</label>
                        <select name="fee_type" id="feeType" class="form-control">
                            <option value="">Select fee type</option>
                            <option value="Full_semester">Full Semester</option>
                            <option value="Credit_hours">Credit Hours</option>
                            <option value="Hostel_Fee">Hostel Fee</option>
                            {{-- <option value="Processing_Fee">Processing Fee</option>
                            <option value="Credit_hours">Credit Hour wise</option> --}}
                        </select>
                        <small id="feeTypeError" class="text-danger d-none">Please select a fee type.</small>

                    </div>

                    <div class="form-group col-sm-6">
                        <label for="summaryViewLevel">Report View</label>
                        <select name="summary_view_level" id="summaryViewLevel" class="form-control">
                            <option value="school" selected>School Wise (Summary)</option>
                            <option value="program">Program Wise (Detail)</option>
                           
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
                    {{-- <div class="form-group col-sm-2">
                        <label for="sch_round">Admission Round</label>
                        <select name="sch_round" disabled id="sch_round" class="form-control">
                            <option value="">Select type</option>
                            <option value="early">Early</option>
                            <option value="regular">Regular</option>
                            <option value="rolling">Rolling</option>
                        </select>
                    </div> --}}
                </div>

                {{-- <div class="form-group col-sm-12" style="display: flex; justify-content: center; align-items: center;"> --}}
                    <div class="form-group mt-5" style="display: flex; justify-content: center; align-items: center; width: 100%; gap: 50px;">

                            <button type="button" id="applyFilterButton" class="btn btn-primary">
                                Apply Filter
                            </button>

                            <button type="button" style="width: 100px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">
                                Clear
                            </button>
                    </div>
                {{-- </div> --}}
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>


    </div>
    <div class="row summaryTable d-none" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <div class="summary-table-wrapper" id="summary-table-wrapper">
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
        var studentType = $(this).val();

        if (studentType && studentType !== 'Existing') {
            $('#sch_round').prop('disabled', false).val('');
        } else {
            $('#sch_round').prop('disabled', true).val('');
        }
    });
    $(window).on('resize.summaryTable', function() {
        adjustSummaryTableScroll();
    });
    $(document).on('click', '#applyFilterButton', function() {
        var feeType = $('#feeType').val();

        if (!feeType) {
            $('#feeTypeError').removeClass('d-none');
            $('#feeType').addClass('is-invalid');
            return;
        }

        $('#feeTypeError').addClass('d-none');
        $('#feeType').removeClass('is-invalid');
        LoadSummary();
    });
});


</script>

<script>
function destroySummaryTable() {
    const $wrapper = $('#summary-table-wrapper');
    const $table = $('#summary-data-table');

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().clear().destroy(false);
    }

    $wrapper.find('.dataTables_wrapper').remove();

    if ($table.length === 0) {
        $wrapper.html(`
            <table id="summary-data-table" class="table display" style="width:100%">
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        `);
    }
}

const GROUPED_SUMMARY_FEE_TYPES = ['Full_semester', 'Credit_hours', 'Hostel_Fee'];

function isSchoolSummaryView(filterParams) {
    return (filterParams.summaryViewLevel || 'school') === 'school';
}

function getSummaryIdentityColumnCount(isSchoolView) {
    return isSchoolView ? 2 : 4;
}

function shouldUseGroupedSummary(filterParams) {
    return GROUPED_SUMMARY_FEE_TYPES.includes(filterParams.feeType)
        && (!filterParams.studentTypeOption || filterParams.studentTypeOption === '');
}

function applySummaryViewClasses(isSchoolView) {
    const $table = $('#summary-data-table');
    $table.toggleClass('school-view', isSchoolView);
}

function LoadSummary(){
    $('.summaryTable').removeClass('d-none grouped-active summary-scroll-active');

    let filterParams = getFilterParams();
    let isGroupedSummary = shouldUseGroupedSummary(filterParams);
    let isSchoolView = isSchoolSummaryView(filterParams);

    destroySummaryTable();

    $('#summary-data-table').removeClass('grouped-mode school-view');
    $('#summary-table-wrapper').removeClass('grouped-scroll');

    if (isGroupedSummary) {
        $('#summary-data-table').addClass('grouped-mode');
        applySummaryViewClasses(isSchoolView);
        $('#summary-table-wrapper').addClass('grouped-scroll');
        $('.summaryTable').addClass('grouped-active');
        initGroupedSummaryTable(filterParams);
        return;
    }

    applySummaryViewClasses(isSchoolView);
    $('.summaryTable').addClass('summary-scroll-active');
    initStandardSummaryTable(filterParams);
}

const FULL_SEM_SUMMARY_GROUPS = [
    { prefix: 'existing', label: 'Existing' },
    { prefix: 'fall', label: 'New Admission Fall' },
    { prefix: 'spring', label: 'New Admission Spring' },
    { prefix: 'consolidated', label: 'Consolidated' }
];

const SUMMARY_METRICS = [
    { key: 'total_students', label: 'Issued Students' },
    { key: 'cancelled', label: 'Cancelled' },
    { key: 'active', label: 'Active' },
    { key: 'fullpaid', label: 'Full Paid' },
    { key: 'installment', label: 'Partial Paid' },
    { key: 'totalpaid', label: 'Total Paid' },
    { key: 'unpaid', label: 'Unpaid' }
];

function setStandardSummaryTableHead(isStandard, isSchoolView) {
    const table = $('#summary-data-table');
    const identityColumnCount = getSummaryIdentityColumnCount(isSchoolView);
    const detailHeaders = isSchoolView
        ? ''
        : `
            <th scope="col">Department</th>
            <th scope="col">Program</th>
        `;

    if (isStandard) {
        table.find('thead').html(`
            <tr>
                <th scope="col">Session</th>
                <th scope="col">School</th>
                ${detailHeaders}
                <th scope="col">Issued Students</th>
                <th scope="col">Cancelled</th>
                <th scope="col">Active</th>
                <th scope="col">Full Paid</th>
                <th scope="col">Partial Paid</th>
                <th scope="col">Total Paid</th>
                <th scope="col">Unpaid</th>
            </tr>
        `);
        table.find('tfoot').html(`
            <tr style="background-color:#8e98db6b;">
                <th colspan="${identityColumnCount}" style="text-align:left">Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        `);
        return;
    }

    const detailGroupedHeaders = isSchoolView
        ? ''
        : `
            <th rowspan="2">Department</th>
            <th rowspan="2">Program</th>
        `;

    let topRow = `
        <tr>
            <th rowspan="2">Session</th>
            <th rowspan="2">School</th>
            ${detailGroupedHeaders}
    `;
    let metricRow = '<tr>';

    FULL_SEM_SUMMARY_GROUPS.forEach(function(group) {
        topRow += `<th colspan="${SUMMARY_METRICS.length}" class="group-header group-header-${group.prefix}" style="text-align:center;">${group.label}</th>`;
        SUMMARY_METRICS.forEach(function(metric, metricIndex) {
            const edgeClasses = [
                metricIndex === 0 ? 'group-col-start' : '',
                metricIndex === SUMMARY_METRICS.length - 1 ? 'group-col-end' : ''
            ].join(' ').trim();
            metricRow += `<th class="metric-header metric-${group.prefix} ${edgeClasses}">${metric.label}</th>`;
        });
    });

    topRow += '</tr>';
    metricRow += '</tr>';
    table.find('thead').html(topRow + metricRow);

    let footerCells = `<th colspan="${identityColumnCount}" style="text-align:left">Total</th>`;
    FULL_SEM_SUMMARY_GROUPS.forEach(function() {
        SUMMARY_METRICS.forEach(function() {
            footerCells += '<th></th>';
        });
    });
    table.find('tfoot').html(`<tr style="background-color:#8e98db6b;">${footerCells}</tr>`);
}

function buildSummaryIdentityColumns(isSchoolView) {
    let columns = [
        { data: 'sess_name', name: 'sess_name', title: 'Session' },
        { data: 'school_name', name: 'school_name', title: 'School' }
    ];

    if (!isSchoolView) {
        columns.push(
            { data: 'dept_name', name: 'dept_name', title: 'Department' },
            { data: 'prog_name', name: 'prog_name', title: 'Program' }
        );
    }

    return columns;
}

function buildStandardSummaryColumns(isSchoolView) {
    return buildSummaryIdentityColumns(isSchoolView).concat([
        { data: 'total_students', name: 'total_students', defaultContent: 0 },
        { data: 'cancelled', name: 'cancelled', defaultContent: 0 },
        { data: 'active', name: 'active', defaultContent: 0 },
        { data: 'fullpaid', name: 'fullpaid', defaultContent: 0 },
        { data: 'installment', name: 'installment', defaultContent: 0 },
        { data: 'totalpaid', name: 'totalpaid', defaultContent: 0 },
        { data: 'unpaid', name: 'unpaid', defaultContent: 0 }
    ]);
}

function buildGroupedSummaryColumns(isSchoolView) {
    let columns = buildSummaryIdentityColumns(isSchoolView);

    FULL_SEM_SUMMARY_GROUPS.forEach(function(group) {
        SUMMARY_METRICS.forEach(function(metric, metricIndex) {
            const edgeClasses = [
                metricIndex === 0 ? 'group-col-start' : '',
                metricIndex === SUMMARY_METRICS.length - 1 ? 'group-col-end' : ''
            ].join(' ').trim();

            columns.push({
                data: `${group.prefix}_${metric.key}`,
                name: `${group.prefix}_${metric.key}`,
                title: metric.label,
                defaultContent: 0,
                className: `text-center group-data group-data-${group.prefix} ${edgeClasses}`.trim()
            });
        });
    });

    return columns;
}

function buildSummaryExportColumns(isGrouped, isSchoolView) {
    const identityColumnCount = getSummaryIdentityColumnCount(isSchoolView);
    const metricColumnCount = isGrouped
        ? FULL_SEM_SUMMARY_GROUPS.length * SUMMARY_METRICS.length
        : 7;

    return Array.from({ length: identityColumnCount + metricColumnCount }, (_, i) => i);
}

function summaryFooterCallback(isGrouped, isSchoolView) {
    return function(row, data, start, end, display) {
        let api = this.api();
        let intVal = function(i) {
            return typeof i === 'string'
                ? i.replace(/[\$,]/g, '') * 1
                : typeof i === 'number' ? i : 0;
        };

        let startColumn = getSummaryIdentityColumnCount(isSchoolView);
        let totalColumns = isGrouped
            ? FULL_SEM_SUMMARY_GROUPS.length * SUMMARY_METRICS.length
            : 7;

        for (let i = 0; i < totalColumns; i++) {
            let total = api
                .column(startColumn + i, { page: 'all', search: 'none' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            $(api.column(startColumn + i).footer()).html(total.toLocaleString());
        }
    };
}

const SUMMARY_TABLE_SCROLL_Y = 'calc(100vh - 300px)';

function excelColLetter(index) {
    let letter = '';
    let temp = index + 1;

    while (temp > 0) {
        const mod = (temp - 1) % 26;
        letter = String.fromCharCode(65 + mod) + letter;
        temp = Math.floor((temp - 1) / 26);
    }

    return letter;
}

function escapeXmlText(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function getSummaryIdentityLabels(isSchoolView) {
    return isSchoolView
        ? ['Session', 'School']
        : ['Session', 'School', 'Department', 'Program'];
}

function buildGroupedPdfHeaderRows(isSchoolView) {
    const identityCount = getSummaryIdentityColumnCount(isSchoolView);
    const identityLabels = getSummaryIdentityLabels(isSchoolView);
    const row0 = [];
    const row1 = [];

    identityLabels.forEach(function(label) {
        row0.push({ text: label, style: 'tableHeader', rowSpan: 2, alignment: 'center' });
    });

    FULL_SEM_SUMMARY_GROUPS.forEach(function(group) {
        row0.push({
            text: group.label,
            style: 'tableHeader',
            colSpan: SUMMARY_METRICS.length,
            alignment: 'center'
        });
        for (let i = 1; i < SUMMARY_METRICS.length; i++) {
            row0.push({});
        }
    });

    for (let i = 0; i < identityCount; i++) {
        row1.push('');
    }

    FULL_SEM_SUMMARY_GROUPS.forEach(function() {
        SUMMARY_METRICS.forEach(function(metric) {
            row1.push({ text: metric.label, style: 'tableHeader', alignment: 'center' });
        });
    });

    return [row0, row1];
}

function applyGroupedPdfExportCustomize(doc, isSchoolView) {
    let tableBlock = null;

    doc.content.forEach(function(item) {
        if (item.table) {
            tableBlock = item;
        }
    });

    if (!tableBlock || !tableBlock.table.body.length) {
        return;
    }

    const headerRows = buildGroupedPdfHeaderRows(isSchoolView);
    const dataAndFooter = tableBlock.table.body.slice(1);

    tableBlock.table.headerRows = 2;
    tableBlock.table.body = headerRows.concat(dataAndFooter);
    doc.defaultStyle.fontSize = 7;
    doc.styles.tableHeader.fontSize = 7;
    doc.styles.tableHeader.alignment = 'center';
}

function applyGroupedExcelExportCustomize(xlsx, isSchoolView) {
    const sheet = xlsx.xl.worksheets['sheet1.xml'];
    const $sheet = $(sheet);
    const $rows = $sheet.find('sheetData row');

    if (!$rows.length) {
        return;
    }

    const identityCount = getSummaryIdentityColumnCount(isSchoolView);
    const metricCount = SUMMARY_METRICS.length;
    const headerStyle = $rows.first().find('c').first().attr('s') || '2';
    const mergeCells = [];

    $rows.each(function() {
        const $row = $(this);
        const rowNum = parseInt($row.attr('r'), 10);
        const newRowNum = rowNum + 1;

        $row.attr('r', newRowNum);
        $row.find('c').each(function() {
            const ref = $(this).attr('r');
            const col = ref.replace(/[0-9]+/g, '');
            $(this).attr('r', col + newRowNum);
        });
    });

    let row1Xml = '<row r="1">';

    for (let i = 0; i < identityCount; i++) {
        const colLetter = excelColLetter(i);
        row1Xml += '<c r="' + colLetter + '1" s="' + headerStyle + '"/>';
        mergeCells.push(colLetter + '1:' + colLetter + '2');
    }

    let colIndex = identityCount;
    FULL_SEM_SUMMARY_GROUPS.forEach(function(group) {
        const startLetter = excelColLetter(colIndex);
        const endLetter = excelColLetter(colIndex + metricCount - 1);

        row1Xml += '<c r="' + startLetter + '1" s="' + headerStyle + '" t="inlineStr"><is><t>'
            + escapeXmlText(group.label) + '</t></is></c>';
        mergeCells.push(startLetter + '1:' + endLetter + '1');

        for (let j = 1; j < metricCount; j++) {
            row1Xml += '<c r="' + excelColLetter(colIndex + j) + '1" s="' + headerStyle + '"/>';
        }

        colIndex += metricCount;
    });

    row1Xml += '</row>';
    $sheet.find('sheetData').prepend(row1Xml);

    let mergeCellsXml = '<mergeCells count="' + mergeCells.length + '">';
    mergeCells.forEach(function(ref) {
        mergeCellsXml += '<mergeCell ref="' + ref + '"/>';
    });
    mergeCellsXml += '</mergeCells>';

    $sheet.find('mergeCells').remove();
    $sheet.find('sheetData').after(mergeCellsXml);
}

function applyGroupedPrintExportCustomize(win) {
    const $body = $(win.document.body);
    const $clonedHead = $('#summary-data-table thead').clone();

    $body.find('table thead').replaceWith($clonedHead);
    $body.find('table thead th').css({
        'background-color': '#eef0fb',
        'border': '1px solid #94a3b8',
        'padding': '4px',
        'text-align': 'center',
        'vertical-align': 'middle'
    });
}

function adjustSummaryTableScroll() {
    const $table = $('#summary-data-table');
    if (!$.fn.DataTable.isDataTable($table)) {
        return;
    }

    const api = $table.DataTable();
    api.columns.adjust();
    $('.summaryTable:not(.d-none) .dataTables_scrollBody').css('overflow-x', 'scroll');
}

function initStandardSummaryTable(filterParams) {
    destroySummaryTable();
    const isSchoolView = isSchoolSummaryView(filterParams);
    setStandardSummaryTableHead(true, isSchoolView);

    $('#summary-data-table').DataTable({
        scrollX: true,
        scrollY: SUMMARY_TABLE_SCROLL_Y,
        scrollCollapse: true,
        responsive: false,
        processing: true,
        serverSide: false,
        searching: true,
        lengthChange: true,
        paging: true,
        pageLength: 100,
        lengthMenu: [[10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000]],
        order: [[1, 'desc']],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: '/api/get-challan-summary-report',
            data: function(d) {
                return $.extend({}, d, filterParams);
            },
            dataSrc: function(json) {
                return json.data || [];
            },
            error: function(xhr) {
                console.error('Challan summary load failed', xhr.responseText);
                alert('Unable to load challan summary data. Please try again.');
            }
        },
        columns: buildStandardSummaryColumns(isSchoolView),
        footerCallback: summaryFooterCallback(false, isSchoolView),
        autoWidth: false,
        dom: 'Blfrtip',
        buttons: buildSummaryExportButtons(false, isSchoolView),
        drawCallback: adjustSummaryTableScroll,
        initComplete: adjustSummaryTableScroll
    });
}

function initGroupedSummaryTable(filterParams) {
    destroySummaryTable();
    const isSchoolView = isSchoolSummaryView(filterParams);
    setStandardSummaryTableHead(false, isSchoolView);

    $('#summary-data-table').DataTable({
        scrollX: true,
        scrollY: SUMMARY_TABLE_SCROLL_Y,
        scrollCollapse: true,
        responsive: false,
        processing: true,
        serverSide: false,
        searching: true,
        orderCellsTop: true,
        lengthChange: true,
        paging: true,
        pageLength: 100,
        lengthMenu: [[10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000]],
        order: [[0, 'asc']],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: '/api/get-challan-summary-report',
            data: function(d) {
                return $.extend({}, d, filterParams);
            },
            dataSrc: function(json) {
                return json.data || [];
            },
            error: function(xhr) {
                console.error('Challan summary load failed', xhr.responseText);
                alert('Unable to load challan summary data. Please try again.');
            }
        },
        columns: buildGroupedSummaryColumns(isSchoolView),
        footerCallback: summaryFooterCallback(true, isSchoolView),
        autoWidth: false,
        dom: 'Blfrtip',
        buttons: buildSummaryExportButtons(true, isSchoolView),
        drawCallback: adjustSummaryTableScroll,
        initComplete: adjustSummaryTableScroll
    });
}

function getSummaryExportOptions(isGrouped, isSchoolView) {
    return {
        columns: buildSummaryExportColumns(isGrouped, isSchoolView),
        modifier: {
            page: 'all',
            search: 'none',
            order: 'applied'
        }
    };
}

function buildSummaryExportButtons(isGrouped, isSchoolView) {
    let exportOptions = getSummaryExportOptions(isGrouped, isSchoolView);

    return [
        {
            extend: 'excelHtml5',
            title: 'Challan Summary',
            footer: true,
            exportOptions: exportOptions,
            customize: function(xlsx) {
                if (isGrouped) {
                    applyGroupedExcelExportCustomize(xlsx, isSchoolView);
                }
            }
        },
        {
            extend: 'pdfHtml5',
            title: 'Challan Summary',
            orientation: 'landscape',
            pageSize: isGrouped ? 'A2' : 'A4',
            footer: true,
            exportOptions: exportOptions,
            customize: function(doc) {
                if (isGrouped) {
                    applyGroupedPdfExportCustomize(doc, isSchoolView);
                    return;
                }

                doc.defaultStyle.fontSize = 9;
                doc.styles.tableHeader.fontSize = 10;
            }
        },
        {
            extend: 'print',
            title: 'Challan Summary',
            footer: true,
            exportOptions: exportOptions,
            customize: function(win) {
                const body = $(win.document.body);
                body.find('table').css('font-size', isGrouped ? '9px' : '11px');
                body.find('table').css('width', '100%');

                if (isGrouped) {
                    applyGroupedPrintExportCustomize(win);
                }
            }
        }
    ];
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
function refreshFields(){
    $('#main-Id').val('');
    $('#roadmap_session_sel').val('');
    $('#school_sel').val('');
    $('#studentTypeOption').val('');
    $('#feeType').val('');
    $('#summaryViewLevel').val('school');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');
    $('#eff-from').val('');
    $('#eff-to').val('');
    $('#tutionAmt').val('');
    $('#admissionAmt').val('');
    $('#securityAmt').val('');
    $('#medicalAmt').val('');
    $('#credFee').val('');
    $('#hostelAmt').val('');
    $('#activitychargesAmt').val('');
    $('#sch_round').val('').prop('disabled', true);
    $('#feeTypeError').addClass('d-none');
    $('#feeType').removeClass('is-invalid');
    $('.summaryTable').addClass('d-none');
    destroySummaryTable();
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
    const sch_round = $('#sch_round').val();
    let filterParams =  {
        session_sel: session_sel,
        school_sel: school_sel,
        studentTypeOption: studentTypeOption,
        feeType: feeType,
        summaryViewLevel: $('#summaryViewLevel').val() || 'school',
        department: department,
        degree_level: degree_level,
        program: program,
        sch_round: sch_round
    };
    return filterParams;
}

</script>

@stop

