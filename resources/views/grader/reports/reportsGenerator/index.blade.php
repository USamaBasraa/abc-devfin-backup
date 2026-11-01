@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("!23"); --}}

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap"
      rel="stylesheet">


<!-- ion.rangeSlider css -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"> -->
<!-- <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
<!-- Include your jQuery library -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->

<link href="{{ asset('weekStudentsReport/ion.rangeSlider.min.css') }}" rel="stylesheet" />
<script src="{{ asset('weekStudentsReport/canvasjs.min.js') }}"></script>
<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>



<style>
    .invisible { display: none; }
    .irs--flat .irs-bar {
    top: 20px;
    height: 20px;
    background-color: #007bff;
}
.irs--flat .irs-handle>i:first-child {
    position: absolute;
    display: block;
    top: -3px;
    left: 30%;
    width: 12px;
    height: 120%;
    margin-left: -8px;
    background-color: #FAA300;
}
.modal-full-width {
        max-width: 90%;
    }

    .modal-full-width .modal-dialog {
        width: 90%;
        margin: 0;
    }

    .hidden {
    display: none;
}
/* ============================================================
   REPORT GENERATOR SCREEN
   Figma: Report Generator
   Scoped only to this screen
============================================================ */

.report-generator-page {
    --rg-primary: #17675F;
    --rg-primary-dark: #125B54;

    --rg-text: #0F172A;
    --rg-heading: #252525;
    --rg-label: #475569;
    --rg-muted: #94A3B8;

    --rg-border: #E2E8F0;
    --rg-row-alt: #F8FAFC;
    --rg-soft: #F1F5F9;

    background:
        linear-gradient(
            270deg,
            rgba(23, 103, 95, 0.15) 0%,
            rgba(255, 255, 255, 0.15) 100%
        ) !important;

    padding: 25px 25px 50px !important;
}


/* ============================================================
   REMOVE OLD OUTER CARD LOOK
============================================================ */

.report-generator-screen {
    margin: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-family: "Montserrat", sans-serif;
}


/* ============================================================
   PAGE TITLE
============================================================ */

.report-generator-screen > .card-header {
    min-height: auto !important;

    padding: 0 5px 18px !important;

    background: transparent !important;

    border: 0 !important;

    display: flex !important;
    align-items: center;
}


.report-generator-screen > .card-header .card-title {
    margin: 0 !important;

    color: var(--rg-heading) !important;

    font-family: "Montserrat", sans-serif;
    font-size: 20px !important;
    font-weight: 600 !important;

    line-height: 1.35;
    letter-spacing: 0;
}


/* ============================================================
   BODY
============================================================ */

.report-generator-screen > .card-body {
    padding: 0 !important;

    background: transparent !important;
}


/* ============================================================
   FILTER CARD
============================================================ */

.report-generator-screen .report-filter-card {
    margin: 0 0 20px !important;

    padding: 32px !important;

    background: #FFFFFF;

    border: 0;
    border-radius: 12px;

    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
}


/*
   Existing HTML has another .form-row inside .col-12.
   Convert that area into the Figma-style horizontal filter row.
*/

.report-generator-screen
.report-filter-card > .col-12 > .form-row {
    display: grid !important;

    grid-template-columns:
        minmax(190px, 0.8fr)
        minmax(220px, 1fr)
        minmax(220px, 1fr)
        auto;

    column-gap: 24px;
    row-gap: 18px;

    align-items: end;

    margin: 0 !important;
}


/* Session / School / Department */

.report-generator-screen
.report-filter-card .col-lg-4 {
    width: auto !important;
    max-width: none !important;

    flex: none !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* remove unnecessary empty spacing */

.report-generator-screen
.report-filter-card .col-12:empty {
    display: none !important;
}


/* ============================================================
   LABELS
============================================================ */

.report-generator-screen
.report-filter-card .col-form-label {
    display: block;

    margin: 0 0 6px !important;
    padding: 0 !important;

    color: var(--rg-label) !important;

    font-size: 13px !important;
    font-weight: 600 !important;

    line-height: 1.4;

    text-align: left !important;
}


/* remove Bootstrap spacing wrapper gap */

.report-generator-screen
.report-filter-card .mb-2:has(.col-form-label) {
    margin-bottom: 0 !important;
}


/* ============================================================
   NATIVE SELECTS
============================================================ */

.report-generator-screen
.report-filter-card .custom-select,
.report-generator-screen
.report-filter-card .form-control {
    width: 100%;

    height: 42px !important;

    padding: 9px 36px 9px 12px !important;

    background-color: #FFFFFF !important;

    border: 1px solid var(--rg-border) !important;
    border-radius: 8px !important;

    color: var(--rg-text) !important;

    font-family: "Montserrat", sans-serif;
    font-size: 14px !important;
    font-weight: 400;

    line-height: 1.5;

    box-shadow: none !important;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}


.report-generator-screen
.report-filter-card .custom-select:focus,
.report-generator-screen
.report-filter-card .form-control:focus {
    border-color: var(--rg-primary) !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.10) !important;

    outline: 0;
}


/* ============================================================
   SELECT2
============================================================ */

.report-generator-screen
.select2-container {
    width: 100% !important;
}


.report-generator-screen
.select2-container .select2-selection--single {
    height: 42px !important;

    display: flex !important;
    align-items: center;

    background: #FFFFFF !important;

    border: 1px solid var(--rg-border) !important;
    border-radius: 8px !important;

    box-shadow: none !important;
}


.report-generator-screen
.select2-container--default
.select2-selection--single
.select2-selection__rendered {
    width: 100%;

    padding: 0 36px 0 12px !important;

    color: var(--rg-text) !important;

    font-family: "Montserrat", sans-serif;
    font-size: 14px !important;

    line-height: 40px !important;
}


.report-generator-screen
.select2-container--default
.select2-selection--single
.select2-selection__arrow {
    height: 40px !important;

    top: 1px !important;
    right: 8px !important;
}


.report-generator-screen
.select2-container--default.select2-container--focus
.select2-selection--single,
.report-generator-screen
.select2-container--default.select2-container--open
.select2-selection--single {
    border-color: var(--rg-primary) !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.10) !important;
}


/* ============================================================
   ACTION BUTTON CONTAINER
============================================================ */

.report-generator-screen
.report-filter-card .col-lg-6.mb-2.mt-2 {
    width: auto !important;
    max-width: none !important;

    flex: none !important;

    margin: 0 !important;
    padding: 0 !important;

    align-self: end;
}


.report-generator-screen
.report-filter-card .col-lg-6.mb-2.mt-2 .d-flex {
    height: 42px;

    align-items: center !important;

    flex-wrap: nowrap;
}


/* ============================================================
   SUBMIT / GET REPORT BUTTON
============================================================ */

.report-generator-screen #submitFormBtn {
    min-width: 105px;

    height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    margin: 0 !important;
    padding: 0 16px !important;

    background: var(--rg-primary) !important;

    border: 1px solid var(--rg-primary) !important;
    border-radius: 8px !important;

    color: #FFFFFF !important;

    font-family: "Montserrat", sans-serif;
    font-size: 13px !important;
    font-weight: 600 !important;

    line-height: 1.4;

    box-shadow: none !important;

    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}


.report-generator-screen #submitFormBtn:hover {
    background: var(--rg-primary-dark) !important;

    border-color: var(--rg-primary-dark) !important;
}


.report-generator-screen #submitFormBtn:focus {
    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.12) !important;
}


.report-generator-screen #submitFormBtn:disabled {
    background: #8BB7B2 !important;

    border-color: #8BB7B2 !important;

    opacity: 1;
}


/* ============================================================
   EXPORT BUTTON
============================================================ */

.report-generator-screen #exportButton {
    height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    padding: 0 14px !important;

    background: var(--rg-soft) !important;

    border: 1px solid var(--rg-soft) !important;
    border-radius: 8px !important;

    color: var(--rg-text) !important;

    font-family: "Montserrat", sans-serif;
    font-size: 13px;
    font-weight: 600;

    box-shadow: none !important;
}


.report-generator-screen #exportButton:hover {
    background: #E7EDF3 !important;

    border-color: #E7EDF3 !important;
}


/* ============================================================
   WARNING BUTTON
============================================================ */

.report-generator-screen #sendMultipleLetters {
    min-height: 42px;

    display: inline-flex;
    align-items: center;

    gap: 7px;

    padding: 0 14px !important;

    border-radius: 8px !important;

    font-family: "Montserrat", sans-serif;
    font-size: 13px;
    font-weight: 600;

    box-shadow: none !important;
}


/* ============================================================
   CHARTS
   Keep functionality - only visually align if visible
============================================================ */

.report-generator-screen #chartDisplay {
    margin-left: 0 !important;
    margin-right: 0 !important;
}


.report-generator-screen #chartContainer1,
.report-generator-screen #chartContainer2 {
    background: #FFFFFF;

    border-radius: 12px;

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}


/* ============================================================
   TABLE CARD
============================================================ */

.report-generator-screen .dt-responsive {
    width: 100%;

    margin-top: 20px !important;

    background: #FFFFFF;

    border: 0;
    border-radius: 12px;

    overflow: hidden;

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}


/* DataTables wrapper */

.report-generator-screen
.dt-responsive .dataTables_wrapper {
    width: 100%;

    padding-top: 0;

    color: var(--rg-text);

    font-family: "Montserrat", sans-serif;
}


/* ============================================================
   DATATABLE TOP CONTROLS
============================================================ */

.report-generator-screen
.dataTables_wrapper > .row:first-child {
    margin: 0 !important;

    padding: 18px 18px 12px;

    align-items: center;
}


.report-generator-screen
.dataTables_wrapper .dataTables_length label,
.report-generator-screen
.dataTables_wrapper .dataTables_filter label {
    margin: 0;

    color: var(--rg-label);

    font-size: 12px;
    font-weight: 500;
}


.report-generator-screen
.dataTables_wrapper .dataTables_filter input,
.report-generator-screen
.dataTables_wrapper .dataTables_length select {
    height: 36px;

    margin-left: 8px;

    padding: 6px 10px;

    background: #FFFFFF;

    border: 1px solid var(--rg-border);
    border-radius: 8px;

    color: var(--rg-text);

    font-size: 13px;

    box-shadow: none;

    outline: none;
}


.report-generator-screen
.dataTables_wrapper .dataTables_filter input:focus,
.report-generator-screen
.dataTables_wrapper .dataTables_length select:focus {
    border-color: var(--rg-primary);

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08);
}


/* ============================================================
   TABLE
============================================================ */

.report-generator-screen #tbl_exporttable_to_xls {
    width: 100% !important;

    display: table !important;

    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0;

    background: #FFFFFF;

    color: var(--rg-text);

    font-family: "Montserrat", sans-serif;

    table-layout: auto;
}


/* remove unnecessary empty first header row */

.report-generator-screen
#tbl_exporttable_to_xls thead tr:first-child {
    display: none;
}


/* Header */

.report-generator-screen
#tbl_exporttable_to_xls thead tr:last-child {
    background: var(--rg-primary) !important;
}


.report-generator-screen
#tbl_exporttable_to_xls thead th {
    padding: 12px 14px !important;

    background: var(--rg-primary) !important;

    border: 0 !important;

    color: #FFFFFF !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    line-height: 1.35;

    text-transform: uppercase;

    vertical-align: middle;

    white-space: nowrap;
}


/* ============================================================
   TABLE BODY
============================================================ */

.report-generator-screen
#tbl_exporttable_to_xls tbody td {
    padding: 13px 14px !important;

    background: #FFFFFF;

    border: 0 !important;
    border-bottom: 1px solid var(--rg-border) !important;

    color: var(--rg-text);

    font-size: 12px !important;
    font-weight: 400;

    line-height: 1.4;

    vertical-align: middle;
}


.report-generator-screen
#tbl_exporttable_to_xls tbody tr:nth-child(even) td {
    background: var(--rg-row-alt) !important;
}


.report-generator-screen
#tbl_exporttable_to_xls tbody tr:hover td {
    background: #F1F7F6 !important;
}


/* last row */

.report-generator-screen
#tbl_exporttable_to_xls tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* ============================================================
   LINKS / VALUES INSIDE TABLE
============================================================ */

.report-generator-screen
#tbl_exporttable_to_xls tbody a {
    color: var(--rg-primary);

    font-weight: 500;

    text-decoration: none;
}


.report-generator-screen
#tbl_exporttable_to_xls tbody a:hover {
    text-decoration: underline;
}


/* ============================================================
   DATATABLE FOOTER
============================================================ */

.report-generator-screen
.dataTables_wrapper > .row:last-child {
    margin: 0 !important;

    padding: 14px 18px 18px;

    align-items: center;
}


.report-generator-screen
.dataTables_info {
    padding-top: 0 !important;

    color: var(--rg-muted) !important;

    font-size: 12px;
}


/* Pagination */

.report-generator-screen
.pagination {
    margin: 0 !important;
}


.report-generator-screen
.page-link {
    min-width: 32px;
    height: 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-left: 4px;

    padding: 0 8px;

    background: #FFFFFF;

    border: 1px solid var(--rg-border);

    border-radius: 7px !important;

    color: var(--rg-label);

    font-size: 12px;

    box-shadow: none !important;
}


.report-generator-screen
.page-item.active .page-link {
    background: var(--rg-primary) !important;

    border-color: var(--rg-primary) !important;

    color: #FFFFFF !important;
}


.report-generator-screen
.page-link:hover {
    background: #EEF7F5;

    border-color: #CFE3DF;

    color: var(--rg-primary);
}


/* ============================================================
   MESSAGES
============================================================ */

.report-generator-screen #successMsg {
    color: var(--rg-primary) !important;

    font-size: 13px;
    font-weight: 500;
}


/* ============================================================
   RESPONSIVE
============================================================ */

@media (max-width: 1200px) {

    .report-generator-screen
    .report-filter-card > .col-12 > .form-row {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .report-generator-screen
    .report-filter-card .col-lg-6.mb-2.mt-2 {
        grid-column: 1 / -1;
    }
}


@media (max-width: 767px) {

    .report-generator-page {
        padding: 18px 14px 35px !important;
    }

    .report-generator-screen > .card-header {
        padding: 0 2px 15px !important;
    }

    .report-generator-screen > .card-header .card-title {
        font-size: 18px !important;
    }

    .report-generator-screen .report-filter-card {
        padding: 20px !important;
    }

    .report-generator-screen
    .report-filter-card > .col-12 > .form-row {
        grid-template-columns: 1fr;

        gap: 16px;
    }

    .report-generator-screen
    .report-filter-card .col-lg-6.mb-2.mt-2 {
        grid-column: auto;
    }

    .report-generator-screen #submitFormBtn {
        width: 100%;
    }

    .report-generator-screen
    .report-filter-card .col-lg-6.mb-2.mt-2 .d-flex {
        width: 100%;
        height: auto;

        flex-wrap: wrap;
    }

    .report-generator-screen .dt-responsive {
        overflow-x: auto;
    }

    .report-generator-screen #tbl_exporttable_to_xls {
        min-width: 1050px;
    }
}
#chartDisplay {
    display: none;
}
    </style>

@include('grader.reports.reportsGenerator.comments')
@include('grader.reports.reportsGenerator.warningLetter')
@include('grader.reports.reportsGenerator.scholarshipDetail')
@include('grader.reports.reportsGenerator.psychiatristRemarks')
@include('grader.reports.reportsGenerator.currentEnrollmentModal')


<div class="card report-generator-screen">
    <div class="card-header" style="display:inline">
      <div style="float:left pt-3">
          <h3 class="card-title">Student Progress Report Generator </h3>
      </div>
    </div>
    <div class="card-body">

<div class="row" id="chartDisplay" >
    <div class="col-6">
        <div id="chartContainer1" style="height: 250px; width: 90%;"></div>

    </div>
    <div class="col-6">
        <div id="chartContainer2" style="height: 250px; width: 90%;"></div>

    </div>
</div>

            <div class="form-row mb-3">
                <div  hidden class=" mb-2">
                    <label class="col-form-label text-right"><strong>Enrollment No #</strong> </label>
                </div>
                <div hidden class="col-lg-5 mb-2 input-group">
                    <input class="form-control" type="text" name="sch_enrollment_no" id="sai_enrollment_no" autocomplete="off" placeholder="Search By Username" value="<?php echo (isset($roll_no)) ? $roll_no : ''; ?>">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" onclick="getProfile()">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                    </div>
                </div>
                <form id="studentRecord" method="post" onsubmit="event.preventDefault();" class="mb-4">
                    @csrf
                {{-- <div class="col-lg-3 mb-2">
                    <button type="button" class="btn btn-success" onclick="getProfile()"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div> --}}
            </div>

        <div class="form-row">

<div class="col-12">

    {{-- <div class="form-row"> --}}
        <div class="form-row report-filter-card">

        <div class="col-lg-4 mb-2">
            <div class=" mb-2">
                <label class="col-form-label text-right">Session</label>
            </div>
            <select   class="custom-select select2-show-search" name="academic_session" id="academic_session"  data-placeholder="Select Session">
                <option value="" selected>---Select Session---</option>
                <option value="All" >All</option>
                @foreach ($academic_session as $row)
                <option value="{{$row->sess_id}}">{{ $row->sess_name }}</option>
                @endforeach
            </select>
        </div>


        <div class="col-lg-4 mb-2">
            <div class="mb-2">
                <label class="col-form-label text-right">School</label>
            </div>
            <select class="custom-select select2-show-search" name="school" id="school" onchange="getDepartments(this.value)" data-placeholder="Select School">
                <!-- Add options for School here -->
                 <option value="" selected>---Select School---</option>
               <!-- <option value="2" >Razia Hassan School of Architecture</option> -->
                @foreach ($recSchool as $row)
                    <option value="{{$row->school_id}}">{{ $row->school_name }}</option>
                @endforeach
            </select>
        </div>


        <div class="col-lg-4 mb-2">
            <div class=" mb-2">
                <label class="col-form-label text-right">Department</label>
            </div>
            <select class="custom-select select2-show-search" name="departments" id="departments"  data-placeholder="Select Department">
                <!-- Add options for Department here -->

            </select>
        </div>
        <div class="col-lg-12" style="display: none;" id="irsSlider">
            <span class="irs js-irs-2 irs-outline">
                <!-- Add your ion.rangeSlider HTML here -->
            </span>
            <input class="rangeslider1 irs-hidden-input" data-extra-classes="irs-outline" name="example_name" type="text" tabindex="-1" readonly="">
        </div>
        <div class="col-12">

        </div>

        <div class="col-lg-6 mb-2 mt-2">
    <div class="d-flex align-items-center">


        <button type="button" class="btn btn-primary btn-sm" id="submitFormBtn" onclick="submitForm()">
            <i class="fa fa-check"></i> <span class="button-text">Submit</span>
        </button>



        <button class="btn btn-outline-primary btn-sm ml-2" id="exportButton" onclick="exportToExcel('tbl_exporttable_to_xls')" style="display: none;">
        <i class="fa fa-file-excel-o"></i> Export to Excel

        </button>



        <button class="btn btn-outline-warning btn-sm ml-2 hidden" id="sendMultipleLetters" data-toggle="modal" data-target="#multipleWarningLettersModal">
    <i class="fa fa-envelope"></i> Send Warning Letters
</button>




    </div>
</div>


        <div class="col-lg-6 mb-2">
            <!-- <input type="text" id="stdname" class="form-control">        -->
            <span id="successMsg" class="text-success"></span>
        </div>
    </div>

</div>

<div class="col-12">

   {{-- <div id="chartContainer1" style="height: 400px; width: 100%;"></div> --}}


</div>




        </div>

        </form>


        <div class="dt-responsive ">
        <table class="table table-striped pl-3 pr-3 table-responsive  tbl_exporttable_to_xls" width="100%"    id="tbl_exporttable_to_xls">
            <thead>
                <tr>
                  <th colspan="9"></th>
                <tr>
                    <th style="text-align:center;" scope="col">Sr#</th>
                    <!-- <th style="text-align:center;" scope="col">Enrollment No</th> -->
                    <th style="text-align:left;" scope="col">Name</th>
                    <th style="text-align:left;" scope="col">Enrollment</th>
                    <th style="text-align:left;" scope="col">Program</th>
                    <th style="text-align:center;" scope="col">Attendance</th>
                    <th style="text-align:center;" scope="col">CGPA</th>
                    <th style="text-align:center;" scope="col">Comment</th>
                    <th style="text-align:center;" scope="col">Warning Letter</th>
                    <th style="text-align:center;" scope="col">Scholarship</th>
                    <th style="text-align:center;" scope="col">History</th>
                    <th style="text-align:center;" scope="col">Current Enrollment</th>
                </tr>
            </thead>
            <?php $count = 1; ?>
            <tbody id="result-body" >


            </tbody>
        </table>
                </div>
  </div>








  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-full-width"> <!-- Adjust modal size as needed (e.g., modal-sm, modal-lg, modal-xl) -->
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="stEnrollmentNumber"></h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <!-- Header content -->
            </div>
            {{-- body for Attendance --}}
            <div class="modal-body" id="modal-body1">



                <div class="row" id="studentGPAChart" style="margin-bottom: 100px">
                    <div class="col-6">
                        <div id="studentGPAChart1" style="height: 350px; width: 90%;"></div>

                    </div>
                    <div class="col-6">
                        <div id="studentGPAChart2" style="height: 350px; width: 90%;"></div>

                    </div>
                </div>


                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                    <iframe class="embed-responsive-item" id="modalIframe1" style="position: absolute; width: 100%; height: 90%; border: none;"></iframe>
                    <div id="loadingSpinner1" style="position: absolute; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); display: none; display: flex; justify-content: center; align-items: center;">
                        <!-- Add your loading indicator content here -->
                        <div style="font-size: 24px; text-align: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span c  lass="visually-hidden"></span>
                            </div>


                        </div>
                    </div>


                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="myModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size as needed (e.g., modal-sm, modal-lg, modal-xl) -->
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title" id="stEnrollmentNumber2"></h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- body for Attendance --}}
            <div class="modal-body" id="modal-body2">

                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                    <iframe class="embed-responsive-item" id="modalIframe2" style="position: absolute; width: 100%; height: 100%; border: none;"></iframe>
                    <div id="loadingSpinner2" style="position: absolute; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); display: none; display: flex; justify-content: center; align-items: center;">
                        <!-- Add your loading indicator content here -->
                        <div style="font-size: 24px; text-align: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>


                        </div>
                    </div>


                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('javascript')


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script> -->


<script src="{{ asset('weekStudentsReport/xlsx.full.min.js') }}"></script>

<script src="{{ asset('weekStudentsReport/ion.rangeSlider.min.js') }}"></script>

<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>

<!-- {{-- <script src="{{ asset('canvasjs.min.js') }}" ></script> --}} -->







<script type="text/javascript">

// $(document).ready(function() {
//     $('#tbl_exporttable_to_xls').DataTable({
//         // Add any additional options here
//     });
// });






$('[data-toggle="tooltip"]').tooltip();







var baseUrl = window.location.origin;
$(document).ready(function() {
    $('#tbl_exporttable_to_xls').DataTable();


    $('#sendMultipleWarningLettersBtn').click(function() {
        sendMultipleWarningLetters();
    });


    $('#academic_session').change(function() {
        const submitButton = $('#submitFormBtn');

        // Check if the dropdown has a valid selection
        if ($(this).val() !== '') {
            submitButton.prop('disabled', false); // Re-enable the submit button
            // Optionally, reset the button text to its default state
            updateButtonState('submitFormBtn', 'default'); // Reset to default state if needed
        }
    });







    // Use 'on' method to delegate the click event for dynamically generated links
    $(document).on('click', '.openModalLink', function(e) {
        e.preventDefault(); // Prevent the default behavior of the link

        var documentLink = $(this).data('document-link');
        getStudentGPA(documentLink)
        documentLink = `/assets/cms/reports/weak_portal_academic_history_report.php?sel_enrollment_no=${documentLink}&pt_sai_enroll_no=${documentLink}`;
        $('#loadingSpinner1').show();
        // Set the iframe source
        $('#modalIframe1').attr('src', documentLink);
        // Attach a 'load' event handler to hide the loading spinner once the content is loaded
        $('#modalIframe1').on('load', function() {
            // Hide the loading spinner
            $('#loadingSpinner1').hide();
        });

        // Open the modal
        $('#myModal1').modal('show');
    });
    $(document).on('click', '.openModalLinkAttendance', function(e) {
        e.preventDefault(); // Prevent the default behavior of the link
        let enrollment_no = $(this).data('enrollment_no');


        getStudentCourses(enrollment_no);
        document.querySelector('#stEnrollmentNumber2').innerHTML = enrollment_no;

        $('#myModal2').modal('show');

    });
});


function getStudentCourses(enrollmentNo) {
    var tableBody = $('#modal-body2');
    tableBody.html('');
    $('#loadingSpinner2').show();

    $.ajax({
        url: '{{ route("reports/get-students-courses-rec") }}',
        type: 'GET',
        data: {
            'sai_enrollment_no': enrollmentNo
        },
        success: function (res) {
            // Assuming res is an array of courses
            if (res.length > 0) {
                // Create the responsive table structure with a defined height
                var table = `
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">Sr#</th>
                                    <th class="font-weight-bold">Session</th>
                                    <th class="font-weight-bold">Course Title</th>
                                    <th class="font-weight-bold">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>`;

                // Iterate over the courses and add rows
                for (var i = 0; i < res.length; i++) {
                    var course = res[i];
                    var row = `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td class="text-left">${course.sess_name}</td>
                            <td class="text-left">${course.course_title}</td>
                            <td class="text-center">
                                <a style="width: 130px;" target="_blank" href="/assets/cms/reports/attendance_details_studentwise_report.php?adcr_co_id=${course.sc_co_id}&adcr_enrollment_no=${course.sc_sai_enrollment_no}" class="btn btn-primary">
                                    <span class="fa fa-eye mr-1"></span> ${parseFloat(course.attendance_percentage).toFixed(2)}%
                                </a>
                            </td>
                        </tr>`;
                    table += row;
                }

                // Close the table structure
                table += `
                            </tbody>
                        </table>
                    </div>`;

                // Append the table to the modal body
                tableBody.append(table);
                // Hide the loading spinner
                $('#loadingSpinner2').hide();
            } else {
                // Handle the case where no courses are returned
                tableBody.html('<p>No courses found</p>');
                $('#loadingSpinner2').hide(); // Ensure the spinner is hidden
            }
        },
        error: function (xhr, status, error) {
            // Handle the error response
            console.error("Error: " + error);
            // Hide the loading spinner
            $('#loadingSpinner2').hide();
        }
    });
}



let coursesList;




function submitForm() {

    const buttonID = $('#exportButton');


    updateButtonState('submitFormBtn', 'loading');

    var bi_form = document.querySelector('#studentRecord');
    var data = new FormData(bi_form);
    document.getElementById('sai_enrollment_no').value = '';

    $.ajax({
        url: '{{ route("reports/get-students-rec") }}',
        type: 'POST',
        data: data,
        processData: false, // Set to false for FormData
        contentType: false, // Set to false for FormData
        success: function(res) {
            // Update button state to success
            updateButtonState('submitFormBtn', 'success');
            $('#exportButton').show();
            document.getElementById('submitFormBtn').disabled = true;
            // Handle success
            coursesList = res;
            $('#irsSlider').show();
            makeHtmlCoursesTable(res);
            
            if (res && res.length > 0) {
    $('#chartDisplay').show();
    makeChart(res);
} else {
    $('#chartDisplay').hide();
}


            makeChart(res);
        },
        error: function(xhr, status, error) {
            // Update button state to error
            updateButtonState('submitFormBtn', 'error');

            // Handle error response
            console.error("Error: " + error);
        }
    });
}

function makeHtmlCoursesTable(res) {
    // Destroy any existing DataTable instance before creating a new one
    if ($.fn.DataTable.isDataTable('#tbl_exporttable_to_xls')) {
        $('#tbl_exporttable_to_xls').DataTable().clear().destroy();
    }

    var tableBody = $('#result-body');
    tableBody.html(''); // Clear previous table body content

    // Append new rows dynamically
    res.forEach((element, index) => {
       // Create a descriptive tooltip based on the student status
              let statusTooltip = '';
            if (element.student_status === 'Active') {
                statusTooltip = `${element.student_name}'s current status is Active.`;
            } else if (element.student_status === 'Expected-Graduate') {
                statusTooltip = `${element.student_name}'s current status is Expected-Graduate.`;
            }



        var row = `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-left" data-tooltip="${statusTooltip}">${element.student_name}</td>
                <td class="text-left" data-tooltip="${statusTooltip}">${element.enrollment_no}</td>
                <td class="text-left">${element.degree_title}</td>
                <td class="text-center">
                    <button href="#" class="btn btn-outline-primary openModalLinkAttendance" data-enrollment_no="${element.enrollment_no}">
                        <span class="fa fa-calendar mr-1"></span>
                    </button>
                </td>
                <td class="text-center">
                    <a href="#" style="width: 80px;" class="btn btn-outline-primary openModalLink" data-toggle="modal" data-target="#myModal" data-document-link="${element.enrollment_no}">
                        <span class="fa fa-graduation-cap"></span> ${element.cgpa ?? 0}
                    </a>
                </td>
                <td class="text-center">
                    <button href="#" onClick="setEnrollmentNo('${element.enrollment_no}')" class="btn btn-outline-info openModalComment" data-toggle="modal" data-target="#commentModal" data-enrollment_no="${element.enrollment_no}">
                        <span class="fa fa-edit mr-1"></span> Write
                    </button>
                </td>
                <td class="text-center">
                    <button href="#" style="width:80px" onClick="warningLetterBtn('${element.enrollment_no}')" class="btn btn-outline-warning openModalWarningLetter" data-toggle="modal" data-target="#warningLetterModal" data-enrollment_no="${element.enrollment_no}">
                        <span class="fa fa-exclamation-triangle mr-1"></span>
                        <badge>${element.total_warning_letters > 0 ? '(' + element.total_warning_letters + ')' : ''}</badge>
                    </button>
                    <input type="checkbox" name="selectedStudents[]" value="${element.enrollment_no}" class="form-check-input" style="margin-left: 5px;" onChange="toggleSendButton()">
                </td>
                <td class="text-center">
                    <button href="#" style="width:40px text-center" onClick="scholarshipDetails('${element.enrollment_no}')" class="btn btn-outline-success openModalScholarship" data-toggle="modal" data-target="#scholarshipModal" data-enrollment_no="${element.enrollment_no}">
                        ${element.scholarship_details}
                    </button>
                </td>
                <td class="text-center">
                    <button href="#" onClick="setEnrollmentNoPsychiatrist('${element.enrollment_no}')" class="btn btn-outline-info" data-toggle="modal" data-target="#psychiatristModal" data-enrollment_no="${element.enrollment_no}">
                        <span class="fa fa-info-circle mr-1"></span> Details
                    </button>
                </td>
                        <td class="text-center">
                            ${element.enrollment_status > 0 ? `
                                <button
                                    onClick="handleStudentEnrollment('${element.enrollment_no}')"
                                    class="btn btn-outline-success w-100"
                                    data-toggle="modal"
                                    data-target="#studentModal">
                                    <span class="fa fa-check-circle mr-1"></span>
                                    Yes (${element.enrollment_status})
                                </button>
                            ` : `
                                <button
                                    type="button"
                                    class="btn btn-outline-danger w-100"
                                    onclick="showNotification('Student is not enrolled in any course.', 'warning', 'NotificationDiv')">
                                    <span class="fa fa-exclamation-circle mr-1"></span>
                                    No
                                </button>
                            `}
                        </td>

            </tr>
        `;

        // Append the row to the table body
        tableBody.append(row);
    });

    // Handle tooltip display
    $('[data-tooltip]').each(function() {
        const tooltipData = $(this).data('tooltip');

        if (tooltipData) {
            // Display tooltip when mouse hovers over the element
            $(this).css('cursor', 'pointer'); // Change cursor to pointer

            $(this).on('mouseenter', function(e) {
                // Create a tooltip element
                const $tooltip = $('<div class="custom-tooltip" style="position: absolute; background-color: #fff; border: 1px solid #ccc; padding: 5px; z-index: 1000; display: none;">' + tooltipData + '</div>');

                // Append tooltip to body
                $('body').append($tooltip);

                // Position tooltip near the cursor
                $tooltip.css({
                    top: e.pageY + 10 + 'px', // Position below the cursor
                    left: e.pageX + 10 + 'px'
                }).fadeIn(200); // Show the tooltip with a fade-in effect
            }).on('mousemove', function(e) {
                // Update position of the tooltip as the mouse moves
                $('.custom-tooltip').css({
                    top: e.pageY + 10 + 'px',
                    left: e.pageX + 10 + 'px'
                });
            }).on('mouseleave', function() {
                // Remove the tooltip on mouse leave
                $('.custom-tooltip').remove();
            });
        }
    });

    // Initialize DataTables after table rows are appended
    $('#tbl_exporttable_to_xls').DataTable({
        responsive: true,
        autoWidth: false, // Change to false to allow full control
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        columnDefs: [
            { orderable: false, targets: [-1] }, // Disable sorting on the last column (action buttons)
            { width: '20%', targets: 0 }, // Example: set first column width to 20%
            { width: '30%', targets: 1 }, // Example: set second column width to 30%
            { width: '25%', targets: 2 }, // Example: set third column width to 25%
            { width: '25%', targets: 3 }  // Example: set fourth column width to 25%
        ]
    });
}





function filterCGPA() {
    // Get the entered CGPA value
    var cgpaValue = parseFloat(document.getElementById('selectCGPA').value);
    var filteredCourses = coursesList.filter(course => parseFloat(course.cgpa) < cgpaValue);
    // console.log(filteredCourses);
    makeHtmlCoursesTable(filteredCourses)

}
// range slider start from
  // Initialize ion.rangeSlider
//   var $range = $(".irs").ionRangeSlider({
//         type: "single",
//         min: 1,
//         max: 4,
//         from: 2.08,
//         step: 0.01, // Set step to 0.01 for 3 decimal places
//         grid: true, // Display grid
//         grid_num: 4, // Number of grid lines
//         // Add other configuration options as needed
//     });



var $range = $(".irs").ionRangeSlider({
    type: "double", // Change type to "double"
    min: 1,
    max: 4,
    from: 1.5,
    to: 2.5,
    step: 0.01,
    grid: true,
    grid_num: 4,
});

    // Get the ion.rangeSlider instance
    let rangeSlider = $range.data("ionRangeSlider");

//     // Add an event listener for when the slider changes
//     $range.on("change", function () {
//         // Get the selected range values

//         let selectedValueFrom=rangeSlider.result.from;
//         let selectedValueTo=rangeSlider.result.to;
//         // console.log(selectedValueFrom,selectedValueTo);
//         // Log the value to the console
//         // console.log("Selected Value:", selectedValue);


//         // if (selectedValueFrom > selectedValueTo) {
//         //     // Swap values if selectedValueFrom is greater than selectedValueTo
//         //     var temp = selectedValueFrom;
//         //     selectedValueFrom = selectedValueTo;
//         //     selectedValueTo = temp;
//         // }

// console.log(selectedValueFrom,selectedValueTo)

//         // Filter coursesList based on the selected range
//         var filteredCourses = coursesList.filter(course => {
//             const cgpa = parseFloat(course.cgpa);
//             return cgpa >= selectedValueFrom && cgpa <= selectedValueTo;
//         });
//                     // old code------------------------------------------------
//             //  let selectedValue = rangeSlider.result.from;
//             // var filteredCourses = coursesList.filter(course => parseFloat(course.cgpa) < selectedValueFrom);


//     // console.log(filteredCourses);
//     makeHtmlCoursesTable(filteredCourses)
//     makeChart(filteredCourses,selectedValue)


//     });


// Debounce function to limit how often a function can be called
function debounce(func, delay) {
    let timeout;
    return function (...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
}

// Function to handle filtering and updating the table and chart
function handleSliderChange() {
    // Call the function to swap slider handles if necessary
    swapSliderHandles(rangeSlider);

    let selectedValueFrom = rangeSlider.result.from;
    let selectedValueTo = rangeSlider.result.to;

    // Log the selected range values
    console.log(selectedValueFrom, selectedValueTo);

    // Filter the courses based on CGPA
    var filteredCourses = coursesList.filter(course => {
        const cgpa = parseFloat(course.cgpa);
        return cgpa >= selectedValueFrom && cgpa <= selectedValueTo;
    });

    // Update the table and chart with the filtered data
    makeHtmlCoursesTable(filteredCourses);
    makeChart(filteredCourses);
}

// Event listener for the slider change event with debouncing
$range.on("change", debounce(handleSliderChange, 100)); // 200ms delay

// Event listener for the slider update event (in case the slider is updated programmatically) with debouncing
$range.on("update", debounce(handleSliderChange, 100)); // 200ms delay



    function swapSliderHandles(rangeSlider) {
    // Get the selected range values
    let selectedValueFrom = rangeSlider.result.from;
    let selectedValueTo = rangeSlider.result.to;

    // Check if selectedValueFrom is greater than selectedValueTo
    if (selectedValueFrom > selectedValueTo) {
        // Update the slider handles to swap their values
        rangeSlider.update({
            from: selectedValueTo,
            to: selectedValueFrom
        });}

}
//  end  slider work to get min and max value ////////////////////////////////////////////
function getDepartments(id){
    // alert(id);
$.ajax({
    type: 'GET',
    url:'/reports/get-dept-rec?schoolId='+id,
    headers: {
        "Authorization": 'Bearer {{ auth()->user()->api_token }}',
        "Accept": "application/json"
      },
    success: function(response) {
        // $('#empDeptId').val(response.emp_dept_id);
        updateDepartments(response)

    },
    error: function(xhr) {

    }
});}

function updateDepartments(departments) {
        var departmentDropdown = document.getElementById('departments');
        departmentDropdown.innerHTML = '<option value="">--Please Select Department--</option>';

        departments.forEach(function(department) {
            var option = document.createElement('option');
            option.value = department.dept_id; // Assuming your department object has an 'id' property
            option.text = department.dept_name; // Assuming your department object has a 'name' property
            departmentDropdown.add(option);
        });
    }






// chart start here
function makeChart(data) {
    let totalStudentsData = {
    '1.5': undefined,
    '2.0': undefined,
    '2.5': undefined,
    '3.0': undefined,
    '3.5': undefined,
    '4.0': undefined
};
for (let index = 0; index < data.length; index++) {
    const cgpa = data[index].cgpa;

    if (cgpa >= 0 && cgpa <= 1.5) {
        totalStudentsData['1.5'] === undefined ? totalStudentsData['1.5'] = 1 : totalStudentsData['1.5'] += 1;
    } else if (cgpa > 1.5 && cgpa <= 2.0) {
        totalStudentsData['2.0'] === undefined ? totalStudentsData['2.0'] = 1 : totalStudentsData['2.0'] += 1;
    } else if (cgpa > 2.0 && cgpa <= 2.5) {
        totalStudentsData['2.5'] === undefined ? totalStudentsData['2.5'] = 1 : totalStudentsData['2.5'] += 1;
    } else if (cgpa > 2.5 && cgpa <= 3.0) {
        totalStudentsData['3.0'] === undefined ? totalStudentsData['3.0'] = 1 : totalStudentsData['3.0'] += 1;
    } else if (cgpa > 3.0 && cgpa <= 3.5) {
        totalStudentsData['3.5'] === undefined ? totalStudentsData['3.5'] = 1 : totalStudentsData['3.5'] += 1;
    } else if (cgpa > 3.5 && cgpa <= 4.0) {
        totalStudentsData['4.0'] === undefined ? totalStudentsData['4.0'] = 1 : totalStudentsData['4.0'] += 1;
    }
}
    // Convert totalStudentsData into CanvasJS dataPoints format
    const dataPoints = Object.entries(totalStudentsData).map(([x, y]) => ({ label: x, y }));
    // Create a line chart
    printCanvas(dataPoints,'line','chartContainer1','CGPA Ranges','Number of Students')
    printCanvas(dataPoints,'column','chartContainer2','CGPA Ranges','Number of Students')

}


function displayValidationErrors(errors) {
        Object.entries(errors).forEach(([key, value]) => {
            var trimmedKey = key.trim();
            var errorElement = document.querySelector("[data-error-for='" + trimmedKey + "']");

            if (errorElement) {
                // Check if the value is an array, and get the first element
                var errorMessage = Array.isArray(value) ? value[0] : value;

                // Create a span element for the error message and icon
                var errorSpan = document.createElement('span');
                errorSpan.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errorMessage;

                // Clear the existing content and add the new span
                errorElement.innerHTML = '';
                errorElement.appendChild(errorSpan);

                errorElement.classList.add('text-danger');

                setTimeout(function () {
                    errorElement.innerHTML = ''; // Clear both icon and text
                    errorElement.classList.remove('text-danger');
                }, 5000);
            }
        });
    }





    function getStudentGPA(enrollmentNo) {
        document.querySelector('#stEnrollmentNumber').innerHTML = enrollmentNo;
    fetch(`/reports/get-student-gpa-rec/${enrollmentNo}`, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Corrected this line
    })
    .then(data => {
        makeStudentGPAchart(data)
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}


function studentGPAarray(data) {
  let result = {};

 data.reduce((accumulator, currentValue) => {
    result[currentValue.sess_code] = parseFloat((currentValue.total_points / currentValue.total_cr).toFixed(2));
  }, {});

  return result;
}


function makeStudentGPAchart(data){
   let StudentsData= studentGPAarray(data);
   // Convert StudentsData into CanvasJS dataPoints format
   const dataPoints = Object.entries(StudentsData).map(([x, y]) => ({ label: x, y: parseFloat(y) }));
   printCanvas(dataPoints,'line','studentGPAChart1','Semesters','GPA')
   printCanvas(dataPoints,'column','studentGPAChart2','Semesters','GPA')
}

function printCanvas(dataPoints,chartType,divID,axisX_Title,axisY_Title){
    const renderChart = new CanvasJS.Chart(divID, {
    animationEnabled: true,
    zoomEnabled: true,
    data: [{
        type: chartType,
        color: "#007bff",
        dataPoints: dataPoints,
        markerType: "circle",
        markerSize: 12,
        lineColor: '#FAA300',
        toolTipContent: `{label}: {y} ${axisY_Title}`,
        indexLabel: `{y}`,
        markerLabel: {
            backgroundColor: "transparent",
            fontColor: "#000",
            fontWeight: "bold",
            content: "{y}",
            padding: 10
        }
    }],
    axisX: {
        title: axisX_Title,
        labelAngle: 0
    },
    axisY: {
        title: axisY_Title,
        minimum: 0
    }
});

renderChart.render();
}



function exportToExcel(tableId) {
    // Get the DataTable API instance
    const table = $(`#${tableId}`).DataTable();
    // Create a workbook and a worksheet
    const wb = XLSX.utils.book_new();
    const wsData = [];
    // Add column headers
    wsData.push(["Student Name", "Enrollment No", "Program Name", "CGPA"]);
    // Get all the rows from the DataTable (not just the current page)
    const allData = table.rows().data(); // Use rows() to get all rows
    // Loop through all the rows and push data into wsData
    allData.each(function (rowData) {
        // Check the structure of rowData
        console.log(rowData); // Debugging: Log rowData to check its structure
        const studentName = rowData[1]; // Adjusted index for Student Name
        const enrollmentNo = rowData[2]; // Adjusted index for Enrollment No
        const programName = rowData[3]; // Adjusted index for Program Name
        const cgpaHtml = rowData[5]; // This is where the button is located for CGPA
        // Extract CGPA text from the button's HTML
        const cgpaText = $(cgpaHtml).text().trim(); // This should extract the text
        // Debugging: Log the extracted values
        console.log("Extracted Student Name:", studentName);
        console.log("Extracted Enrollment No:", enrollmentNo);
        console.log("Extracted Program Name:", programName);
        console.log("Extracted CGPA Text:", cgpaText);
        // Push the data into wsData array
        wsData.push([
            studentName,  // Student Name
            enrollmentNo, // Enrollment No
            programName,  // Program Name
            cgpaText      // Extracted CGPA text
        ]);
    });
    // Convert the data to a worksheet
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    // Auto-adjust column widths
    const colWidths = wsData[0].map((_, i) => {
        return { wpx: Math.max(...wsData.map(row => row[i].toString().length)) * 10 };
    });
    ws['!cols'] = colWidths;
    // Add the worksheet to the workbook
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    // Generate a file and prompt the user to download it
    XLSX.writeFile(wb, 'week_student_summary_report.xlsx');
}




const buttonOriginalTexts = new Map();






function updateButtonState(buttonId, state) {
    const button = document.getElementById(buttonId);

    if (!button) {
        console.error('Button not found:', buttonId);
        return;
    }

    // Store original text if not already stored
    if (!buttonOriginalTexts.has(buttonId)) {
        // Only store the part of the button's HTML that's intended to be text
        buttonOriginalTexts.set(buttonId, button.querySelector('.button-text').textContent.trim());
    }

    const originalText = buttonOriginalTexts.get(buttonId);

    // Clear any existing HTML to avoid duplication
    button.innerHTML = '';

    switch (state) {
        case 'loading':
            button.disabled = true;
            button.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm mr-2" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="button-text">Processing...</span>
                </div>
            `;
            break;
        case 'success':
            button.disabled = false;
            button.innerHTML = `
                <i class="fa fa-check"></i> <span class="button-text">${originalText}</span>
            `;
            break;
        case 'error':
            button.disabled = false;
            button.innerHTML = `
                <i class="fa fa-times"></i> <span class="button-text">${originalText} - Error</span>
            `;
            // Revert to original text after 3 seconds
            setTimeout(() => {
                updateButtonState(buttonId, 'default');
            }, 3000);
            break;
        case 'default':
            button.disabled = false;
            button.innerHTML = `
                <i class="fa fa-check"></i> <span class="button-text">${originalText}</span>
            `;
            break;
        default:
            console.error('Unknown state:', state);
    }
}





function toggleSendButton() {
    const checkboxes = document.querySelectorAll('input[name="selectedStudents[]"]');
    const sendButton = document.getElementById('sendMultipleLetters');

    let anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    sendButton.style.display = anyChecked ? 'inline-block' : 'none';
}




</script>
@stop
