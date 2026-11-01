@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('weekStudentsReport/d3.v6.min.js') }}"></script>


@include('grader.reportgenerator.BNUReportsGenerator.students')
@include('grader.reportgenerator.BNUReportsGenerator.leftStudent')
@include('grader.reportgenerator.BNUReportsGenerator.admissionDatapieChart')
@include('grader.reportgenerator.BNUReportsGenerator.courseAttendance')
@include('grader.reportgenerator.BNUReportsGenerator.courseAttendanceWithdraw')



<style>
    /* Add any specific styles here */
    .clear-icon {
        cursor: pointer;
    }

    .clear-icon .input-group-text {
        padding: 0.25rem;
    }

    .clear-icon:hover {
        color: red;
    }

    /* #myPieChart {
            width: 550px !important; 
            height: 550px !important; 
        } */



    /* =========================================================
       Figma-aligned Admission Analysis Portal presentation layer
       Visual reference: Admission Analysis Portal Hierarchical / Graphic views.
       Functional Blade markup, IDs, names, includes and JavaScript remain intact.
       ========================================================= */
    .admission-analysis-page {
        width: 100%;
        min-height: calc(100vh - 20px);
        margin: 0 !important;
        /* padding: 48px !important; */
        border-radius: 10px;
        background: rgb(242 246 249);
        color: #0f172a;
        font-family: 'Montserrat', sans-serif;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .admission-analysis-page .admission-analysis-card {
        margin: 0 !important;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
    }

    .admission-analysis-page .admission-analysis-header {
        position: relative;
        display: block;
        min-height: 68px;
        margin: 0;
        padding: 0 0 28px !important;
        border: 0;
        background: transparent !important;
        color: #0f172a !important;
    }

    .admission-analysis-page .admission-analysis-header .card-title {
        float: none;
        margin: 0;
        color: #0f172a;
        font-family: 'Montserrat', sans-serif;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }

    .admission-analysis-page .admission-analysis-header .card-title i {
        display: none;
    }

    .admission-analysis-page .admission-analysis-header::after {
        content: 'Comprehensive admission data and statistics';
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.4;
    }

    .admission-analysis-page .admission-analysis-body {
        padding: 0 !important;
    }

    .admission-analysis-page .admission-analysis-form {
        margin: 0;
        padding: 32px;
        border: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: none;
    }

    .admission-analysis-page .analysis-filter-grid {
        margin: 0 -12px !important;
        align-items: flex-end;
    }

    .admission-analysis-page .analysis-filter-grid > .form-group {
        margin-bottom: 24px;
        padding-left: 12px;
        padding-right: 12px;
    }

    .admission-analysis-page .analysis-filter-grid label {
        min-height: 15px;
        margin: 0 0 6px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.3;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .admission-analysis-page .analysis-filter-grid label > span:first-child {
        color: inherit;
    }

    .admission-analysis-page .clear-icon {
        margin-left: 8px;
        color: #94a3b8;
        cursor: pointer;
    }

    .admission-analysis-page .clear-icon .input-group-text {
        min-width: 18px;
        min-height: 18px;
        padding: 0 !important;
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 11px;
    }

    .admission-analysis-page .clear-icon:hover {
        color: #17675f;
    }

    .admission-analysis-page .form-control,
    .admission-analysis-page .select2-container .select2-selection--single {
        width: 100%;
        height: 44px !important;
        min-height: 44px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #0f172a !important;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px !important;
        font-weight: 500;
        line-height: 1.2;
        box-shadow: none !important;
    }

    .admission-analysis-page .form-control {
        padding: 10px 16px;
    }

    .admission-analysis-page .form-control:focus,
    .admission-analysis-page .select2-container--focus .select2-selection--single,
    .admission-analysis-page .select2-container--open .select2-selection--single {
        border-color: #17675f !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
        outline: none;
    }

    .admission-analysis-page .select2-container {
        width: 100% !important;
    }

    .admission-analysis-page .select2-container .select2-selection--single .select2-selection__rendered {
        height: 42px;
        padding: 0 38px 0 16px !important;
        color: #0f172a !important;
        font-size: 14px;
        line-height: 42px !important;
    }

    .admission-analysis-page .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
    }

    .admission-analysis-page .select2-container .select2-selection--single .select2-selection__arrow {
        top: 0 !important;
        right: 10px !important;
        width: 24px !important;
        height: 42px !important;
    }

    .admission-analysis-page input[type='date'] {
        color: #64748b !important;
    }

    /* Figma places Program on its own full-width row. */
    .admission-analysis-page #select-program-field {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .admission-analysis-page .analysis-actions {
        flex: 0 0 100%;
        max-width: 100%;
        height: auto !important;
        min-height: 44px;
        margin-bottom: 0 !important;
        padding-left: 12px;
        padding-right: 12px;
        gap: 16px;
        align-items: center !important;
    }

    .admission-analysis-page .analysis-actions .btn {
        position: relative;
        z-index: 2;
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
        padding: 11px 22px;
        border-radius: 8px !important;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        box-shadow: none !important;
        overflow: hidden;
    }

    .admission-analysis-page #filterStudentsButton {
        border: 1px solid #17675f !important;
        background: #17675f !important;
        color: #fff !important;
    }

    .admission-analysis-page #filterStudentsButton:hover,
    .admission-analysis-page #filterStudentsButton:focus {
        border-color: #12564f !important;
        background: #12564f !important;
        color: #fff !important;
    }

    .admission-analysis-page #clearStudentsButton {
        border: 1.5px solid #64748b !important;
        background: #fff !important;
        color: #64748b !important;
    }

    .admission-analysis-page #clearStudentsButton #fillBackground {
        background: rgba(100, 116, 139, 0.08) !important;
    }

    .admission-analysis-page #generateGrandSummaryBtn {
        border: 1.5px solid #17675f !important;
        background: #fff !important;
        color: #17675f !important;
    }

    .admission-analysis-page #generateGrandSummaryBtn:disabled {
        opacity: .6;
    }

    .admission-analysis-page .analysis-notification {
        width: 100%;
        max-width: 100%;
        margin: 20px 0 0;
        padding: 0;
    }

    .admission-analysis-page .analysis-notification .alert {
        margin: 0;
        border-radius: 10px;
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
    }

    /* Do not render blank result cards before the original logic injects content. */
    .admission-analysis-page #fetchSummaryDataDiv:not(:has(*)),
    .admission-analysis-page #fetchSummaryDataDivWithdraw:not(:has(*)),
    .admission-analysis-page #tableDataDiv:empty,
    .admission-analysis-page #tableDataDivWithdraw:empty,
    .admission-analysis-page #tabularDataDiv:empty,
    .admission-analysis-page #tabularDataDivWithdraw:empty {
        display: none !important;
    }

    .admission-analysis-page #fetchSummaryDataDiv:has(*),
    .admission-analysis-page #fetchSummaryDataDivWithdraw:has(*),
    .admission-analysis-page #tableDataDiv:not(:empty),
    .admission-analysis-page #tableDataDivWithdraw:not(:empty) {
        width: 100%;
        margin-top: 20px !important;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
        box-shadow: none;
    }

    /* Hierarchical / tabular content generated by the existing partials. */
    .admission-analysis-page #fetchSummaryDataDiv table,
    .admission-analysis-page #fetchSummaryDataDivWithdraw table,
    .admission-analysis-page #tableDataDiv table,
    .admission-analysis-page #tableDataDivWithdraw table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        color: #0f172a;
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
    }

    .admission-analysis-page #fetchSummaryDataDiv table thead th,
    .admission-analysis-page #fetchSummaryDataDivWithdraw table thead th,
    .admission-analysis-page #tableDataDiv table thead th,
    .admission-analysis-page #tableDataDivWithdraw table thead th {
        padding: 14px 24px !important;
        border: 0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background: #17675f !important;
        color: #fff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: .06em;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .admission-analysis-page #fetchSummaryDataDiv table tbody td,
    .admission-analysis-page #fetchSummaryDataDivWithdraw table tbody td,
    .admission-analysis-page #tableDataDiv table tbody td,
    .admission-analysis-page #tableDataDivWithdraw table tbody td {
        padding: 14px 24px !important;
        border: 0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background: #fff;
        color: #0f172a;
        vertical-align: middle;
    }

    .admission-analysis-page #fetchSummaryDataDiv table tbody tr:nth-child(even) td,
    .admission-analysis-page #fetchSummaryDataDivWithdraw table tbody tr:nth-child(even) td,
    .admission-analysis-page #tableDataDiv table tbody tr:nth-child(even) td,
    .admission-analysis-page #tableDataDivWithdraw table tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    .admission-analysis-page #fetchSummaryDataDiv table tbody tr:last-child td,
    .admission-analysis-page #fetchSummaryDataDivWithdraw table tbody tr:last-child td,
    .admission-analysis-page #tableDataDiv table tbody tr:last-child td,
    .admission-analysis-page #tableDataDivWithdraw table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    /* Existing chart + legend output, presented like the Figma Graphical View card. */
    .admission-analysis-page .analysis-chart-card {
        width: 100%;
        min-height: 496px;
        margin: 20px 0 0 !important;
        padding: 48px;
        border-radius: 16px;
        background: #fff;
        align-items: center;
        box-shadow: none;
    }

    /* Bootstrap d-flex otherwise overrides the original inline display:none. */
    .admission-analysis-page #myPieChartDiv[style*='display:none'],
    .admission-analysis-page #myPieChartDiv[style*='display: none'] {
        display: none !important;
    }

    .admission-analysis-page .analysis-chart-card #myPieChartDiv {
        min-height: 400px;
        align-items: center;
        justify-content: center !important;
        padding: 0 32px 0 0;
    }

    .admission-analysis-page .analysis-chart-card #myPieChart {
        width: 400px !important;
        max-width: 100% !important;
        height: 400px !important;
        max-height: 400px !important;
    }

    .admission-analysis-page .analysis-chart-card #tabularDataDiv,
    .admission-analysis-page .analysis-chart-card #tabularDataDivWithdraw {
        padding-left: 32px;
        color: #334155;
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
    }

    .admission-analysis-page .analysis-chart-card #tabularDataDiv table,
    .admission-analysis-page .analysis-chart-card #tabularDataDivWithdraw table {
        width: 100%;
        margin: 0;
        color: #334155;
        font-size: 13px;
    }

    .admission-analysis-page .analysis-chart-card #tabularDataDiv td,
    .admission-analysis-page .analysis-chart-card #tabularDataDivWithdraw td {
        padding: 6px 4px;
        border: 0;
        vertical-align: middle;
    }

    .admission-analysis-page .spinner-border {
        color: #17675f;
    }

    .admission-analysis-page .progress {
        height: 6px !important;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .admission-analysis-page .progress-bar {
        background: #17675f !important;
    }

    @media (max-width: 1199.98px) {
        .admission-analysis-page {
            padding: 32px !important;
        }

        .admission-analysis-page .analysis-chart-card {
            padding: 32px;
        }
    }

    @media (max-width: 991.98px) {
        .admission-analysis-page {
            padding: 24px 18px !important;
        }

        .admission-analysis-page .admission-analysis-form {
            padding: 24px;
        }

        .admission-analysis-page .analysis-chart-card {
            min-height: 0;
            padding: 28px 20px;
        }

        .admission-analysis-page .analysis-chart-card #myPieChartDiv,
        .admission-analysis-page .analysis-chart-card #tabularDataDiv,
        .admission-analysis-page .analysis-chart-card #tabularDataDivWithdraw {
            flex: 0 0 100%;
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
        }
    }

    @media (max-width: 767.98px) {
        .admission-analysis-page {
            padding: 20px 12px !important;
        }

        .admission-analysis-page .admission-analysis-header .card-title {
            font-size: 21px;
        }

        .admission-analysis-page .admission-analysis-form {
            padding: 18px;
        }

        .admission-analysis-page .analysis-actions {
            flex-wrap: wrap;
            gap: 10px;
        }

        .admission-analysis-page .analysis-actions .btn {
            flex: 1 1 180px;
        }

        .admission-analysis-page .analysis-chart-card #myPieChart {
            width: 320px !important;
            height: 320px !important;
            max-height: 320px !important;
        }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>
<div class="container-fluid admission-analysis-page">
    <div class="card mt-3 admission-analysis-card">
        <div class="card-header bg-primary text-white admission-analysis-header">
            <h3 class="card-title">
                <i class="fa fa-cogs"></i>
                Admission Analysis Portal
            </h3>
        </div>

        <div class="card-body admission-analysis-body">
            <form action="#" id="myForm" class="admission-analysis-form">
                @csrf

                <div class="row analysis-filter-grid">

                    <!-- Report Type -->
        <div class="form-group col-md-4" id="report-type-field">
            <label for="report_type" class="d-flex justify-content-between align-items-center">
                <span>Report Type:</span>
                <span class="input-group-append clear-icon" id="clear-report-type" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="report_type" id="report_type" class="form-control select2" required>
                <option value="" disabled selected>-- Select Report Type --</option>
                <option value="admission">Admission Analysis Report</option>
                <!-- <option value="course_attendance">Attendance Analysis Portal</option>
                <option value="course_attendance_withdraw">Data Attrition</option> -->
            </select>
        </div>
      
        <!-- Academic Session -->
        <div class="form-group col-md-4" id="academic-session-field">
            <label for="academic_session" class="d-flex justify-content-between align-items-center">
                <span>Academic Session:</span>
                <span class="input-group-append clear-icon" id="clear-academic_session" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="academic_session" id="academic_session" class="form-control select2" required>
                <option value="" disabled selected>-- Select Academic Session --</option>
                @foreach($academic_session as $session)
                    <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Roadmap Session -->
        <div class="form-group col-md-4" id="roadmap-session-field">
            <label for="roadmap_session" class="d-flex justify-content-between align-items-center">
                <span>Road Map Session:</span>
                <span class="input-group-append clear-icon" id="clear-roadmap-session" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="roadmap_session" id="roadmap_session" class="form-control select2" required>
                <option value="" disabled selected>-- Select Road Map Session --</option>
                @foreach($academic_session as $session)
                    <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date Selection -->
        <div class="form-group col-md-4" id="selected-date-field">
            <label for="selected_date" class="d-flex justify-content-between align-items-center">
                <span>Cut-off Date:</span>
                <span class="input-group-append clear-icon" id="clear-selected-date" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <input type="date" name="selected_date" id="selected_date" class="form-control">
        </div>








        <div class="form-group col-md-4" id="select-school-field">
            <label for="school_id" class="d-flex justify-content-between align-items-center">
                <span>Select School:</span>
                <span class="input-group-append clear-icon" id="clear-school-id" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="school_id" id="school_id" class="form-control select2">
                <option value="">-- Select School --</option>
                @foreach($recSchool as $school)
                    <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group col-md-4" id="select-department-field">
            <label for="department_id" class="d-flex justify-content-between align-items-center">
                <span>Select Department:</span>
                <span class="input-group-append clear-icon" id="clear-department-id" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="department_id" id="department_id" class="form-control select2">
                <option value="">-- Select Department --</option>
                {{-- Departments dynamically load hoga --}}
            </select>
        </div>
        
        <div class="form-group col-md-4" id="select-program-field">
            <label for="program_id" class="d-flex justify-content-between align-items-center">
                <span>Select Program:</span>
                <span class="input-group-append clear-icon" id="clear-program-id" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <select name="program_id" id="program_id" class="form-control select2">
                <option value="">-- Select Program --</option>
                {{-- Programs dynamically load hoga --}}
            </select>
        </div>
        
  



        <!-- Date Selection (From) -->
        <!-- <div class="form-group col-md-4" id="from-date-field">
            <label for="from_date" class="d-flex justify-content-between align-items-center">
                <span>Select Date From:</span>
                <span class="input-group-append clear-icon" id="clear-from-date" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <input type="date" name="from_date" id="from_date" class="form-control">
        </div> -->

        <!-- Date Selection (To) -->
        <!-- <div class="form-group col-md-4" id="to-date-field">
            <label for="to_date" class="d-flex justify-content-between align-items-center">
                <span>Select Date To:</span>
                <span class="input-group-append clear-icon" id="clear-to-date" style="display:none;">
                    <span class="input-group-text">
                        <i class="fa fa-times"></i>
                    </span>
                </span>
            </label>
            <input type="date" name="to_date" id="to_date" class="form-control">
        </div> -->











                    <div class="form-group col-md-3 d-flex justify-content-left align-items-center analysis-actions" style="height: 100px;">
                        <button type="button" id="filterStudentsButton" class="btn btn-outline-primary mr-2" title="Search">
                            <i class="fa fa-search"></i> Search
                        </button>
                    <button type="button" id="clearStudentsButton" class="btn btn-outline-warning mr-2" title="Clear"
                            style="font-weight: bold; transition: all 0.3s ease; position: relative; display: flex; align-items: center;">
                        <i class="fa fa-undo" style="margin-right: 8px;"></i>
                        <span id="clearText" style="z-index: 2; position: relative;">Clear</span>
                        <div id="fillBackground" style="position: absolute; top: 0; left: 0; width: 0; height: 100%; background-color: #ffc107; transition: width 0.5s ease; z-index: 1;"></div>
                    </button>
                    
                    <!-- Generate Grand Summary Report Button -->
                    <button type="button" id="generateGrandSummaryBtn" class="btn btn-outline-success mr-2" title="Generate Grand Summary Report" style="display: none;" disabled>
                        <i class="fa fa-file-pdf-o"></i> Generate Grand Summary Report
                    </button>
  

                    </div>
                </div>
            </form>
        </div>


<div id='fetchSummaryDataDiv' class='analysis-summary-output'>


<!-- <div id="tree-container"></div> -->




</div>

<div id='fetchSummaryDataDivWithdraw' class='analysis-summary-output'>


<!-- <div id="tree-container"></div> -->




</div>






        <div id="NotificationDiv" class="container analysis-notification">
        </div>

            <div class="container">
                <div id="tableDataDiv" class="mt-3 analysis-tabular-output"></div>

                <div class="row analysis-chart-card" id="myPieChartParentDiv">
                    <!-- Left Column for Pie Chart -->
                    <div class="col-md-7 text-left d-flex justify-content-left" id="myPieChartDiv" style='display:none;'  >
                        <canvas id="myPieChart"></canvas>
                    </div>

                    <!-- Right Column for Table Data -->
                    <div class="col-md-5 " id="tabularDataDiv"></div>
                </div>
            </div>

            <div class="container">
                <div id="tableDataDivWithdraw" class="mt-3 analysis-tabular-output"></div>

                <div class="row analysis-chart-card" id="myPieChartParentDiv">
                    <!-- Left Column for Pie Chart -->
                    <div class="col-md-7 text-left d-flex justify-content-left" id="myPieChartDiv" style='display:none;'  >
                        <canvas id="myPieChart"></canvas>
                    </div>

                    <!-- Right Column for Table Data -->
                    <div class="col-md-5 " id="tabularDataDivWithdraw"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop

@section('javascript')
<script src="{{ asset('weekStudentsReport/xlsx.full.min.js') }}"></script>



<script>



$(document).ready(function () {


    $("#report_type").change(function () {
        $("#NotificationDiv").html('')
        $("#tableDataDiv").html('')
    });


    $("#academic_session").change(function () {
        $("#roadmap_session").val("").trigger("change"); // Reset and trigger change event
    });

    $("#roadmap_session").change(function () {
        var selectedAcademicId = parseInt($("#academic_session").val()) || 0;
        var selectedRoadmapId = parseInt($(this).val()) || 0;
        var reportType = $("#report_type").val(); // Get report type

        if (reportType === 'course_attendance') { // Apply only if report type is 'course_attendance'
            if (selectedRoadmapId > selectedAcademicId) {
                showNotification('Cannot choose a Roadmap Session greater than the selected Academic Session.', 'warning', 'NotificationDiv');
                $(this).val(""); // Reset selection
            }
        }

        if (reportType === 'course_attendance_withdraw') { // Apply only if report type is 'course_attendance'
            if (selectedRoadmapId > selectedAcademicId) {
                showNotification('Cannot choose a Roadmap Session greater than the selected Academic Session.', 'warning', 'NotificationDiv');
                $(this).val(""); // Reset selection
            }
        }




    });
});










// Colors for the pie chart
const backgroundColors = [
    '#FF9F9A',  // Soft coral
    '#B39DDB',  // Muted lavender
    '#FFD166',  // Soft golden yellow
    '#82CFFD',  // Light sky blue
    '#A5D6A7',  // Soft green
    '#FFE082',  // Light amber
    '#FFAB91',  // Warm peach
    '#DCE775',  // Soft lime green
    '#FFCC80',  // Warm orange
    '#CE93D8',  // Light purple
    '#FF8A80',  // Soft salmon red
    '#F4A261',  // Soft earthy orange
    '#76C7C0',  // Light teal
    '#A1887F',  // Muted brown
    '#F48FB1',  // Soft pink
];



$(document).ready(function() {
    // Initialize Select2 on all select elements with class 'select2'

$('#clearStudentsButton').click(function() {
    $('#fetchSummaryDataDiv').html('');
    $('#tableDataDiv').html('');
    resetMyPieChartParentDiv();
    $("#NotificationDiv").html('');

    // Start the background fill animation
    $('#fillBackground').css('width', '100%');

    setTimeout(function() {
        // Reset all select2 dropdowns
        $('#report_type').val('').trigger('change');
        $('#academic_session').val('').trigger('change');
        $('#roadmap_session').val('').trigger('change');
        $('#school_id').val('').trigger('change');
        $('#department_id').val('').trigger('change');
        $('#program_id').val('').trigger('change');
        
        // Clear date input
        $('#selected_date').val('');

        // Reset the background fill
        $('#fillBackground').css('width', '0');
    }, 500);
});


    $('.select2').select2();
    // Hide all optional fields initially
    $('#academic-session-field, #roadmap-session-field, #selected-date-field, #select-school-field, #select-department-field, #select-program-field').hide();
    
    // Flag to prevent multiple simultaneous requests
    let isGeneratingPDF = false;
    
    // Remove any existing event handlers first to prevent duplicates
    $('#generateGrandSummaryBtn').off('click');
    
    // Handle Grand Summary button click
    $('#generateGrandSummaryBtn').on('click', function() {
        // Prevent multiple simultaneous requests
        if (isGeneratingPDF) {
            console.log('PDF generation already in progress, ignoring click');
            return;
        }
        
        // Disable button during generation
        $('#generateGrandSummaryBtn').prop('disabled', true);
        
        console.log('Grand Summary button clicked');
        const roadmapSession = $('#roadmap_session').val();
        console.log('Roadmap Session:', roadmapSession);
        
        if (!roadmapSession) {
            alert('Please select a Roadmap Session first.');
            // Re-enable button if no session selected
            $('#generateGrandSummaryBtn').prop('disabled', false);
            return;
        }
        
        // Set flag to prevent multiple requests
        isGeneratingPDF = true;
        
        // Show professional loading state
        console.log('Showing PDF loading state');
        showPDFLoadingState();
        
        // Create form data
        const formData = {
            roadmapSession: roadmapSession,
            _token: '{{ csrf_token() }}'
        };
        
        // Send AJAX request to generate PDF
        console.log('Sending AJAX request to:', '{{ route("generate-grand-summary-report") }}');
        console.log('Form data:', formData);
        
        $.ajax({
            url: '{{ route("generate-grand-summary-report") }}',
            method: 'POST',
            data: formData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                console.log('PDF generation successful, data received:', data);
                // Create blob URL and open in new tab
                const blob = new Blob([data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                console.log('Opening PDF in new tab:', url);
                window.open(url, '_blank');
                
                // Clean up the blob URL after a delay
                setTimeout(function() {
                    window.URL.revokeObjectURL(url);
                }, 1000);
                
                // Keep loader visible for 2 more seconds after success
                setTimeout(function() {
                    hidePDFLoadingState();
                    // Re-enable button after successful generation
                    $('#generateGrandSummaryBtn').prop('disabled', false);
                    isGeneratingPDF = false;
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error generating PDF:', error);
                console.error('XHR response:', xhr.responseText);
                console.error('Status:', status);
                
                let errorMessage = 'Error generating PDF. Please try again.';
                
                // Check for specific Oracle errors
                if (xhr.responseText && xhr.responseText.includes('ORA-00054')) {
                    errorMessage = 'Database is currently busy with another process. The system tried 5 times with increasing delays. Please wait a few minutes and try again.';
                } else if (xhr.responseText && xhr.responseText.includes('resource busy')) {
                    errorMessage = 'System is currently processing another request. Please try again in a few minutes.';
                }
                
                alert(errorMessage);
                
                // Hide loader and reset flag
                hidePDFLoadingState();
                isGeneratingPDF = false;
                // Re-enable button after error
                $('#generateGrandSummaryBtn').prop('disabled', false);
            }
        });
    });
    
    $('#report_type').change(function () {
        const reportType = $(this).val();
        
        // Show/hide Grand Summary button based on report type
        if (reportType === 'admission') {
            $('#generateGrandSummaryBtn').show();
            // Enable button after successful operation
            $('#generateGrandSummaryBtn').prop('disabled', false);
        } else {
            $('#generateGrandSummaryBtn').hide();
        }
        
        const fields = {
            'admission': ['#selected-date-field', '#roadmap-session-field', '#select-school-field', '#select-department-field', '#select-program-field'],
            'course_attendance': ['#roadmap-session-field', '#academic-session-field'],
            'course_attendance_withdraw': ['#roadmap-session-field', '#academic-session-field']
        };

    // Clear all fields before hiding
    $('#academic_session').val('').trigger('change');
    $('#roadmap_session').val('').trigger('change');
    $('#selected_date').val('');
    $('#school_id').val('');
    $('#department_id').val('');
    $('#program_id').val('');

    // Hide all fields
    $('#academic-session-field, #roadmap-session-field, #selected-date-field, #select-school-field, #select-department-field, #select-program-field').hide();

    // Show relevant fields based on the selected report type
    if (fields[reportType]) {
        fields[reportType].forEach(field => $(field).show());
    }
});



    $('#clearButton').on('click', function() {
    // Reset the form
    $('#myForm')[0].reset();
    $('.select2').val(null).trigger('change');

    // Clear the pie chart if it exists
    if (myPieChart) {
        myPieChart.destroy(); // Destroy the current chart instance
        myPieChart = null; // Reset the variable
    }

    // Optionally, clear the canvas element if needed
    const canvasElement = document.getElementById('myPieChart');
    if (canvasElement) {
        const ctx = canvasElement.getContext('2d');
        ctx.clearRect(0, 0, canvasElement.width, canvasElement.height); // Clear the canvas
    }
});


    // School select change -> load departments and reset program dropdown
    $('#school_id').on('change', function() {
        let schoolId = $(this).val();
       
        // Clear department and program dropdowns first
        $('#department_id').empty().append('<option value="">-- Select Department --</option>').trigger('change');
        $('#program_id').empty().append('<option value="">-- Select Program --</option>').trigger('change');

        if (!schoolId) {
            return;
        }

        $.ajax({
            url: '/departments',
            type: 'GET',
            data: { school_id: schoolId },
            success: function(departments) {
               
                if (departments && departments.length > 0) {
                    $.each(departments, function(i, dept) {
                        $('#department_id').append(
                            $('<option></option>').val(dept.dept_id).text(dept.dept_name)
                        );
                    });
                } else {
                    $('#department_id').append('<option value="">No Departments Found</option>');
                    showNotification('No departments found for this school.', 'info', 'NotificationDiv');
                }
            },
            error: function(xhr, status, error) {
                console.error('Department fetch error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                showNotification('Failed to fetch departments: ' + error, 'danger', 'NotificationDiv');
            }
        });
    });


    // Department select change -> load programs
    $('#department_id').on('change', function() {
        let deptId = $(this).val();
        // Clear program dropdown
        $('#program_id').empty().append('<option value="">-- Select Program --</option>').trigger('change');

        if (!deptId) {
            return;
        }
        $.ajax({
            url: '/programs',
            type: 'GET',
            data: { dept_id: deptId },
            success: function(programs) {
                console.log('Programs received:', programs);
                if (programs && programs.length > 0) {
                    $.each(programs, function(i, program) {
                        $('#program_id').append(
                            $('<option></option>').val(program.prog_id).text(program.prog_name)
                        );
                    });
                } else {
                    $('#program_id').append('<option value="">No Programs Found</option>');
                    showNotification('No programs found for this department.', 'info', 'NotificationDiv');
                }
            },
            error: function(xhr, status, error) {
                console.error('Program fetch error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                showNotification('Failed to fetch programs: ' + error, 'danger', 'NotificationDiv');
            }
        });
    });

    $('#clearButton').on('click', function() {
        // Clear the form fields
        $('#myForm')[0].reset(); // Reset the form fields

        // Clear the table data
        $('#tableDataDiv').empty();
    });

     // Function to handle clear icon visibility and actions
     function handleClearIcon(selectId, iconId) {
        const $select = $(`#${selectId}`);
        const $icon = $(`#${iconId}`);

        // Show icon when a value is selected
        $select.on('change', function() {
            if ($select.val()) {
                $icon.show();
            } else {
                $icon.hide();
            }
        });

        // Clear selection and hide icon on click
        $icon.on('click', function() {
            $select.val(null).trigger('change');
            $icon.hide();
        });
    }







   // Apply handleClearIcon to each select


handleClearIcon('roadmap_session', 'clear-roadmap-session');
handleClearIcon('school', 'clear-school');
handleClearIcon('department', 'clear-department');
handleClearIcon('pf_status', 'clear-pf-status');
handleClearIcon('qualification_status', 'clear-qualification-status');
handleClearIcon('status', 'clear-status');
handleClearIcon('students_moved_to_cms', 'students_moved_to_cms-status');
handleClearIcon('cms_student_status', 'clear-cms_student_status-status');
handleClearIcon('report_type', 'clear-report-type');
handleClearIcon('academic_session', 'clear-academic_session');
handleClearIcon('selected_date', 'clear-selected-date');
handleClearIcon('school_id', 'clear-school-id');
handleClearIcon('department_id', 'clear-department-id');
handleClearIcon('program_id', 'clear-program-id');


});



function showNotification(message, type, targetDivId) {
    // Define icon based on the type of alert
    var iconHtml;
    switch (type) {
        case 'success':
            iconHtml = '<i class="bi bi-check-circle-fill"></i>';
            break;
        case 'info':
            iconHtml = '<i class="bi bi-info-circle-fill"></i>';
            break;
        case 'warning':
            iconHtml = '<i class="bi bi-exclamation-triangle-fill"></i>';
            break;
        case 'danger':
            iconHtml = '<i class="bi bi-exclamation-circle-fill"></i>';
            break;
        default:
            iconHtml = '<i class="bi bi-bell-fill"></i>'; // default icon if none of the above
    }

    // Clear existing notifications in the target div
    $('#' + targetDivId).empty();

    // Create Bootstrap 4 alert HTML with icon and dismiss button
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${iconHtml} <strong>${message}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Append the alert, then fade in and set to fade out after 5 seconds
    $(`#${targetDivId}`).append(alertHtml).hide().fadeIn(500);
    setTimeout(function() {
        $(`#${targetDivId} .alert`).alert('close');
    }, 5000);
}


                 // Function to show loading spinner
        function showLoadingSpinner(targetDiv) {
           $(targetDiv).html(`
               <div class="text-center">
                   <span>Fetching Student Data, Please Wait...</span>
                   <div class="spinner-border" role="status">
                       <span class="sr-only">Loading...</span>
                   </div>
                   <div class="progress mt-2">
                       <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                   </div>
               </div>
           `);
       }

       // Function to show professional PDF generation loading
       function showPDFLoadingState() {
           // Disable entire page
           $('body').addClass('loading-disabled');
           
           // Create modern loading overlay
           const loadingOverlay = `
               <div id="pdfLoadingOverlay" class="pdf-loading-overlay">
                   <div class="pdf-loading-container">
                       <div class="pdf-loading-content">
                           <div class="pdf-loading-icon">
                               <div class="pdf-icon-wrapper">
                                   <span class="pdf-text">PDF</span>
                               </div>
                           </div>
                           <div class="pdf-loading-spinner">
                               <div class="modern-spinner">
                                   <div class="spinner-ring"></div>
                                   <div class="spinner-ring"></div>
                                   <div class="spinner-ring"></div>
                               </div>
                           </div>
                           <div class="pdf-loading-text">
                               <h4 class="loading-title">Generating Grand Summary Report</h4>
                               <p class="loading-subtitle">Scheduler is running, please wait while PDF is being generated...</p>
                               <div class="loading-progress">
                                   <div class="progress-bar"></div>
                               </div>
                               <div class="loading-alert">
                                   <span class="shield-symbol">🛡️</span>
                                   <span>Please do not close this page during processing</span>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           `;
           
           $('body').append(loadingOverlay);
       }

       // Function to hide PDF loading state
       function hidePDFLoadingState() {
           $('body').removeClass('loading-disabled');
           $('#pdfLoadingOverlay').remove();
       }

        function resetMyPieChartParentDiv() {
    $('#myPieChartParentDiv').empty();
    const newStructure = `
            <div class="col-md-7 text-left d-flex justify-content-left"
     id="myPieChartDiv"
     style="display:none;">
            <canvas id="myPieChart"></canvas>
        </div>
        <div class="col-md-5 mt-4" id='tabularDataDiv'></div>
    `;
    $('#myPieChartParentDiv').append(newStructure);

}




</script>

<style>
/* Modern PDF Loading Overlay Styles */
.pdf-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.7));
    backdrop-filter: blur(15px);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.4s ease-out;
}

.pdf-loading-container {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 25px;
    padding: 50px 40px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
    text-align: center;
    max-width: 550px;
    width: 90%;
    animation: slideUp 0.6s ease-out;
    position: relative;
    overflow: hidden;
}

.pdf-loading-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
    animation: gradientShift 3s ease-in-out infinite;
}

.pdf-loading-content {
    position: relative;
    z-index: 1;
}

.pdf-loading-icon {
    margin-bottom: 30px;
}

.pdf-icon-wrapper {
    display: inline-block;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(0, 123, 255, 0.3);
    animation: float 3s ease-in-out infinite;
}

.pdf-text {
    font-size: 1.8rem;
    font-weight: 900;
    color: white;
    letter-spacing: 2px;
    animation: bounce 2s ease-in-out infinite;
    font-family: 'Arial', sans-serif;
}

.pdf-loading-spinner {
    margin: 30px 0;
}

.modern-spinner {
    position: relative;
    width: 60px;
    height: 60px;
    margin: 0 auto;
}

.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
}

.spinner-ring:nth-child(2) {
    width: 80%;
    height: 80%;
    top: 10%;
    left: 10%;
    border-top-color: #28a745;
    animation-delay: -0.5s;
    animation-duration: 2s;
}

.spinner-ring:nth-child(3) {
    width: 60%;
    height: 60%;
    top: 20%;
    left: 20%;
    border-top-color: #ffc107;
    animation-delay: -1s;
    animation-duration: 2.5s;
}

.pdf-loading-text {
    margin-top: 30px;
}

.loading-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #007bff, #28a745);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.loading-subtitle {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 25px;
    font-weight: 500;
}

.loading-progress {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 25px;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #28a745);
    border-radius: 10px;
    animation: progress 2s ease-in-out infinite;
}

.loading-alert {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    border-radius: 15px;
    color: #1976d2;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px solid rgba(25, 118, 210, 0.2);
}

.shield-symbol {
    font-size: 1.2rem;
    animation: pulse 2s ease-in-out infinite;
}

/* Disable page interactions during loading */
body.loading-disabled {
    pointer-events: none;
    user-select: none;
}

body.loading-disabled * {
    pointer-events: none !important;
}

/* Modern Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes progress {
    0% {
        width: 0%;
        background-position: 0% 50%;
    }
    50% {
        width: 70%;
        background-position: 100% 50%;
    }
    100% {
        width: 100%;
        background-position: 0% 50%;
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
/* Hide empty chart cards until chart/table data is available */
.admission-analysis-page
.analysis-chart-card:has(> #myPieChartDiv[style*="display:none"]):has(> #tabularDataDiv:empty),

.admission-analysis-page
.analysis-chart-card:has(> #myPieChartDiv[style*="display: none"]):has(> #tabularDataDiv:empty),

.admission-analysis-page
.analysis-chart-card:has(> #myPieChartDiv[style*="display:none"]):has(> #tabularDataDivWithdraw:empty),

.admission-analysis-page
.analysis-chart-card:has(> #myPieChartDiv[style*="display: none"]):has(> #tabularDataDivWithdraw:empty) {
    display: none !important;
}
</style>

@stop
