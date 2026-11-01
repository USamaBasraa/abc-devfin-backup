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


    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>
<div class="container-fluid report-generator-page" style="padding: 0 !important;">
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fa fa-cogs"></i>
                BNU Report Generator
            </h3>
        </div>

        <div class="card-body">
            <form action="#" id="myForm">
                @csrf

                <div class="row">

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
                <!-- <option value="admission">Admission</option> -->
                <option value="course_attendance">Attendance Analysis Portal</option>
                <option value="course_attendance_withdraw">Data Attrition</option>
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











                    <div class="form-group col-md-3 d-flex justify-content-left align-items-center" style="height: 100px;">
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
                    {{-- <button type="button" id="generateGrandSummaryBtn" class="btn btn-outline-success mr-2" title="Generate Grand Summary Report" style="display: none;" disabled>
                        <i class="fas fa-file-pdf"></i> Generate Grand Summary Report
                    </button> --}}
  

                    </div>
                </div>
            </form>
        </div>


<div  id='fetchSummaryDataDiv' ></div>

<div  id='fetchSummaryDataDivWithdraw' ></div>






        <div id="NotificationDiv" class="container">
        </div>

            <div class="container">
                <div id="tableDataDiv" class="mt-3"></div>

                <div class="row" id="myPieChartParentDiv">
                    <!-- Left Column for Pie Chart -->
                    <div class="col-md-7 text-left justify-content-left" id="myPieChartDiv" style='display:none;'  >
                        <canvas id="myPieChart"></canvas>
                    </div>

                    <!-- Right Column for Table Data -->
                    <div class="col-md-5 " id="tabularDataDiv"></div>
                </div>
            </div>

            <div class="container">
                <div id="tableDataDivWithdraw" class="mt-3"></div>

                <div class="row" id="myPieChartParentDiv">
                    <!-- Left Column for Pie Chart -->
                    <div class="col-md-7 text-left justify-content-left" id="myPieChartDiv" style='display:none;'  >
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



{{-- Figma-aligned Report Generator presentation layer. Functional fields, output containers and JavaScript remain unchanged. --}}
<style>
    .report-generator-page {
        min-height: calc(100vh - 20px);
        padding: 30px 26px 42px !important;
        background: #f2f6f9;
        font-family: 'Montserrat', sans-serif;
    }

    .report-generator-page > .card.mt-3 {
        margin-top: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .report-generator-page > .card.mt-3 > .card-header {
        min-height: 72px;
        padding: 0 0 22px 4px !important;
        display: flex;
        align-items: flex-start;
        border: 0 !important;
        background: transparent !important;
        color: #0f172a !important;
    }

    .report-generator-page > .card.mt-3 > .card-header .card-title {
        margin: 0 !important;
        color: #0f172a !important;
        font-size: 20px !important;
        line-height: 1.35;
        font-weight: 600 !important;
        letter-spacing: -0.01em;
    }

    .report-generator-page > .card.mt-3 > .card-header .card-title > i {
        display: none;
    }

    .report-generator-page > .card.mt-3 > .card-header .card-title::after {
        content: 'Improve Performance';
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.45;
        font-weight: 400;
        letter-spacing: 0;
    }

    .report-generator-page > .card.mt-3 > .card-body {
        padding: 32px !important;
        border: 0 !important;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04) !important;
    }

    .report-generator-page #myForm > .row {
        display: grid;
        grid-template-columns: repeat(3, minmax(220px, 1fr));
        gap: 22px 24px;
        margin: 0 !important;
        align-items: end;
    }

    .report-generator-page #myForm > .row > [class*='col-'] {
        width: 100% !important;
        max-width: none !important;
        flex: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .report-generator-page #myForm .form-group {
        margin-bottom: 0 !important;
    }

    .report-generator-page #myForm label {
        margin-bottom: 7px !important;
        color: #475569 !important;
        font-size: 13px !important;
        line-height: 1.4;
        font-weight: 600 !important;
    }

    .report-generator-page #myForm .form-control {
        min-height: 44px;
        height: 44px;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #0f172a;
        font-size: 14px;
        box-shadow: none !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .report-generator-page #myForm .form-control:focus {
        border-color: #17675f;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.10) !important;
    }

    .report-generator-page #myForm .select2-container {
        width: 100% !important;
    }

    .report-generator-page #myForm .select2-container .select2-selection--single {
        height: 44px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .report-generator-page #myForm .select2-container--default .select2-selection--single .select2-selection__rendered {
        height: 42px;
        line-height: 42px !important;
        padding-left: 12px !important;
        padding-right: 34px !important;
        color: #0f172a !important;
        font-size: 14px !important;
    }

    .report-generator-page #myForm .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        right: 8px !important;
    }

    .select2-dropdown {
        border-color: #e2e8f0 !important;
        border-radius: 8px !important;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #17675f !important;
    }

    .report-generator-page #myForm .clear-icon .input-group-text {
        border: 0;
        padding: 2px 5px;
        background: transparent;
        color: #94a3b8;
        font-size: 12px;
    }

    .report-generator-page #myForm .clear-icon:hover .input-group-text {
        color: #dc3545;
    }

    .report-generator-page #myForm #filterStudentsButton,
    .report-generator-page #myForm #clearStudentsButton {
        min-width: 118px;
        height: 44px;
        margin: 0 10px 0 0 !important;
        padding: 0 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        box-shadow: none !important;
    }

    .report-generator-page #myForm #filterStudentsButton {
        border-color: #17675f !important;
        background: #17675f !important;
        color: #fff !important;
    }

    .report-generator-page #myForm #filterStudentsButton:hover,
    .report-generator-page #myForm #filterStudentsButton:focus {
        background: #12544e !important;
        border-color: #12544e !important;
    }

    .report-generator-page #myForm #clearStudentsButton {
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        color: #475569 !important;
    }

    .report-generator-page #myForm #clearStudentsButton:hover,
    .report-generator-page #myForm #clearStudentsButton:focus {
        border-color: #cbd5e1 !important;
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .report-generator-page #myForm #clearStudentsButton #fillBackground {
        border-radius: 8px;
        opacity: .14;
    }

    .report-generator-page #myForm .form-group.col-md-3.d-flex {
        height: 44px !important;
        min-height: 44px;
        justify-content: flex-start !important;
        align-items: center !important;
    }

    .report-generator-page #fetchSummaryDataDiv:not(:empty),
    .report-generator-page #fetchSummaryDataDivWithdraw:not(:empty),
    .report-generator-page #tableDataDiv:not(:empty),
    .report-generator-page #tableDataDivWithdraw:not(:empty),
    .report-generator-page #tabularDataDiv:not(:empty),
    .report-generator-page #tabularDataDivWithdraw:not(:empty) {
        margin-top: 24px !important;
        background: #fff;
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .report-generator-page #NotificationDiv:not(:empty) {
        margin-top: 20px;
        padding: 0;
        max-width: 100%;
    }

    .report-generator-page #tableDataDiv,
    .report-generator-page #tableDataDivWithdraw {
        max-width: 100%;
    }

    .report-generator-page #fetchSummaryDataDiv table,
    .report-generator-page #fetchSummaryDataDivWithdraw table,
    .report-generator-page #tableDataDiv table,
    .report-generator-page #tableDataDivWithdraw table,
    .report-generator-page #tabularDataDiv table,
    .report-generator-page #tabularDataDivWithdraw table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        color: #0f172a;
        font-size: 13px;
    }

    .report-generator-page #fetchSummaryDataDiv table thead,
    .report-generator-page #fetchSummaryDataDivWithdraw table thead,
    .report-generator-page #tableDataDiv table thead,
    .report-generator-page #tableDataDivWithdraw table thead,
    .report-generator-page #tabularDataDiv table thead,
    .report-generator-page #tabularDataDivWithdraw table thead {
        background: #17675f !important;
        color: #fff !important;
    }

    .report-generator-page #fetchSummaryDataDiv table thead th,
    .report-generator-page #fetchSummaryDataDivWithdraw table thead th,
    .report-generator-page #tableDataDiv table thead th,
    .report-generator-page #tableDataDivWithdraw table thead th,
    .report-generator-page #tabularDataDiv table thead th,
    .report-generator-page #tabularDataDivWithdraw table thead th {
        padding: 12px 16px !important;
        border-color: #17675f !important;
        background: #17675f !important;
        color: #fff !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .report-generator-page #fetchSummaryDataDiv table tbody td,
    .report-generator-page #fetchSummaryDataDivWithdraw table tbody td,
    .report-generator-page #tableDataDiv table tbody td,
    .report-generator-page #tableDataDivWithdraw table tbody td,
    .report-generator-page #tabularDataDiv table tbody td,
    .report-generator-page #tabularDataDivWithdraw table tbody td {
        padding: 13px 16px !important;
        border-color: #e2e8f0 !important;
        vertical-align: middle !important;
    }

    .report-generator-page #fetchSummaryDataDiv table tbody tr:nth-child(even),
    .report-generator-page #fetchSummaryDataDivWithdraw table tbody tr:nth-child(even),
    .report-generator-page #tableDataDiv table tbody tr:nth-child(even),
    .report-generator-page #tableDataDivWithdraw table tbody tr:nth-child(even),
    .report-generator-page #tabularDataDiv table tbody tr:nth-child(even),
    .report-generator-page #tabularDataDivWithdraw table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    .report-generator-page > .card.mt-3 .container {
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .report-generator-page #myPieChartParentDiv {
        margin: 24px 0 0 !important;
        gap: 18px;
        align-items: stretch;
    }

    /* .report-generator-page #myPieChartDiv:not(:empty) {
        padding: 24px !important;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    } */

    .report-generator-page .table-responsive {
        border-radius: 0 0 12px 12px;
    }

    @media (max-width: 1199.98px) {
        .report-generator-page #myForm > .row {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .report-generator-page {
            padding: 22px 14px 30px !important;
        }

        .report-generator-page > .card.mt-3 > .card-body {
            padding: 20px !important;
        }

        .report-generator-page #myForm > .row {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .report-generator-page #myForm .form-group.col-md-3.d-flex {
            height: auto !important;
            flex-wrap: wrap;
            gap: 10px;
        }

        .report-generator-page #myForm #filterStudentsButton,
        .report-generator-page #myForm #clearStudentsButton {
            flex: 1 1 130px;
            margin: 0 !important;
        }
    }
</style>

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
        <div class="col-md-7 text-left d-flex justify-content-left" id="myPieChartDiv">
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
</style>

@stop
