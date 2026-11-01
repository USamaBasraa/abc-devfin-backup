@extends('layouts.app')
@section('content')


<?php
//    echo $sess->emp_dept_id;
//    session_start();
//    echo "ID = ".$_SESSION["active_session_id"];
//    echo " Name = ".$_SESSION["active_session_name"];{{getLoginUser('active_session_name')}}
?>
<style>
    /* =====================================================
   MARKS APPROVAL SESSION - MAIN AREA REDESIGN
   CSS ONLY - HTML / JS NO CHANGE
===================================================== */

:root {
    --approval-primary: #17675F;
    --approval-primary-dark: #10564f;
    --approval-primary-light: #eef7f5;
    --approval-bg: #f5f7f7;
    --approval-card: #ffffff;
    --approval-border: #e5eaea;
    --approval-text: #252b2d;
    --approval-muted: #747d80;
}


/* -----------------------------------------------------
   PAGE BACKGROUND - ONLY THIS SCREEN
----------------------------------------------------- */

body:has(#accordian-main-div) .app-content {
    background: var(--approval-bg) !important;
}


/* -----------------------------------------------------
   PAGE TITLE
----------------------------------------------------- */

body:has(#accordian-main-div) h2 {
    color: var(--approval-text);
    font-size: 26px;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 24px;
    letter-spacing: -0.3px;
}


/* -----------------------------------------------------
   SESSION SELECT
----------------------------------------------------- */

#sess_id {
    height: 50px !important;

    padding: 0 45px 0 16px !important;

    background-color: #ffffff !important;

    border: 1px solid #dce4e3 !important;
    border-radius: 10px !important;

    color: #343a3c !important;

    font-size: 14px !important;
    font-weight: 500;

    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.02),
        0 4px 14px rgba(0, 0, 0, 0.025) !important;

    outline: none !important;

    transition:
        border-color .2s ease,
        box-shadow .2s ease !important;
}


#sess_id:hover {
    border-color: #b9cbc8 !important;
}


#sess_id:focus {
    border-color: var(--approval-primary) !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.09),
        0 4px 14px rgba(0, 0, 0, 0.03) !important;
}


/* Reduce unnecessary Bootstrap spacing below select */

#sess_id.closest {
    margin-bottom: 0;
}

body:has(#accordian-main-div) h2 + .form-group {
    margin-bottom: 18px !important;
}


/* -----------------------------------------------------
   ACCORDION MAIN CONTAINER
----------------------------------------------------- */

#accordian-main-div {
    background: #ffffff;

    border: 1px solid var(--approval-border);
    border-radius: 14px;

    padding: 10px;

    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.02),
        0 8px 30px rgba(31, 48, 46, 0.035);

    scrollbar-width: thin;
    scrollbar-color: #cbd7d5 transparent;
}


/* Chrome / Edge scrollbar */

#accordian-main-div::-webkit-scrollbar {
    width: 6px;
}

#accordian-main-div::-webkit-scrollbar-track {
    background: transparent;
}

#accordian-main-div::-webkit-scrollbar-thumb {
    background: #ccd7d5;
    border-radius: 20px;
}

#accordian-main-div::-webkit-scrollbar-thumb:hover {
    background: #aebfbc;
}


/* -----------------------------------------------------
   REMOVE OLD CARD LOOK
----------------------------------------------------- */

#cha-accordion,
#cha-accordion .card,
#facultycoList {
    background: transparent !important;
}

#cha-accordion .card {
    border: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
}


/* -----------------------------------------------------
   FACULTY ACCORDION ROW
----------------------------------------------------- */

#cha-accordion .cha-accordion-header {
    position: relative;

    display: flex;
    align-items: center;

    min-height: 60px;

    margin-bottom: 8px;
    padding: 13px 16px !important;

    background: #ffffff !important;

    border: 1px solid #e6ebea !important;
    border-radius: 10px !important;

    cursor: pointer;

    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.015);

    transition:
        background-color .2s ease,
        border-color .2s ease,
        box-shadow .2s ease,
        transform .2s ease;
}


/* Hover */

#cha-accordion .cha-accordion-header:hover {
    background: #f7fbfa !important;

    border-color: #cbdedb !important;

    box-shadow:
        0 5px 16px rgba(23, 103, 95, 0.06);

    transform: translateY(-1px);
}


/* -----------------------------------------------------
   FACULTY NAME
----------------------------------------------------- */

#cha-accordion .cha-accordion-header .card-title {
    width: 100%;

    display: flex;
    align-items: center;

    margin: 0 !important;
    padding: 0 !important;

    color: var(--approval-text) !important;

    font-size: 15px !important;
    font-weight: 600 !important;
    line-height: 1.4;

    text-decoration: none !important;
}


#cha-accordion .cha-accordion-header .card-title span {
    margin-left: 13px !important;
}


/* -----------------------------------------------------
   PLUS / MINUS ICON
----------------------------------------------------- */

#cha-accordion .cha-accordion-header .fa-plus,
#cha-accordion .cha-accordion-header .fa-minus {
    float: none !important;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 34px;

    width: 34px;
    height: 34px;

    margin: 0 !important;

    border-radius: 9px;

    background: var(--approval-primary-light);

    color: var(--approval-primary) !important;

    font-size: 12px;

    transition:
        background-color .2s ease,
        color .2s ease,
        transform .2s ease;
}


#cha-accordion .cha-accordion-header:hover .fa-plus,
#cha-accordion .cha-accordion-header:hover .fa-minus {
    background: #dfeeea;
}


/* -----------------------------------------------------
   OPEN / ACTIVE FACULTY
   Bootstrap automatically manages aria-expanded
----------------------------------------------------- */

#cha-accordion .cha-accordion-header[aria-expanded="true"] {
    background: var(--approval-primary) !important;

    border-color: var(--approval-primary) !important;

    box-shadow:
        0 7px 18px rgba(23, 103, 95, 0.16);

    transform: none;
}


#cha-accordion
.cha-accordion-header[aria-expanded="true"]
.card-title {
    color: #ffffff !important;
}


#cha-accordion
.cha-accordion-header[aria-expanded="true"]
.fa-plus,

#cha-accordion
.cha-accordion-header[aria-expanded="true"]
.fa-minus {
    color: #ffffff !important;

    background: rgba(255, 255, 255, 0.15);
}


/* -----------------------------------------------------
   EXPANDED CONTENT
----------------------------------------------------- */

#cha-accordion .card-body.collapse,
#cha-accordion .card-body.collapsing {
    border: 0 !important;
}


#cha-accordion .card-body.show {
    margin: -2px 3px 10px;

    padding: 18px 20px !important;

    background: #f8fbfa;

    border: 1px solid #e2ecea !important;
    border-top: 0 !important;

    border-radius: 0 0 10px 10px;

    box-shadow: none !important;
}


/* -----------------------------------------------------
   COURSE TITLE INSIDE EXPANDED FACULTY
----------------------------------------------------- */

#cha-accordion .course-title {
    color: #263331 !important;

    font-size: 14px !important;
    font-weight: 600 !important;

    padding: 4px 0;

    letter-spacing: -0.1px;
}


/* -----------------------------------------------------
   COURSE ACTION AREA
----------------------------------------------------- */

#cha-accordion .teacher-courses {
    background: #ffffff;

    border: 1px solid #e7edec;
    border-radius: 10px;

    padding: 14px 10px !important;

    margin:
        10px 0 18px 0 !important;
}


/* Midterm / Final text */

#cha-accordion .teacher-courses .txtBlue {
    display: block;

    margin-top: 5px;

    color: var(--approval-primary) !important;

    font-size: 12px;
    font-weight: 600;
}


/* Action images */

#cha-accordion .teacher-courses img {
    cursor: pointer;

    transition:
        transform .18s ease,
        opacity .18s ease;
}


#cha-accordion .teacher-courses img:hover {
    transform: translateY(-2px);
    opacity: .88;
}


/* -----------------------------------------------------
   HR INSIDE COURSE LIST
----------------------------------------------------- */

#cha-accordion .card-body hr {
    margin: 14px 0 !important;

    border: 0;
    border-top: 1px solid #e7eceb;
}


/* -----------------------------------------------------
   RESPONSIVE
----------------------------------------------------- */

@media (max-width: 767px) {

    body:has(#accordian-main-div) h2 {
        font-size: 22px;
        margin-bottom: 18px;
    }

    #sess_id {
        height: 46px !important;
    }

    #accordian-main-div {
        padding: 7px;
        border-radius: 12px;
    }

    #cha-accordion .cha-accordion-header {
        min-height: 56px;

        padding: 11px 12px !important;
    }

    #cha-accordion .cha-accordion-header .card-title {
        font-size: 14px !important;
    }

    #cha-accordion .cha-accordion-header .fa-plus,
    #cha-accordion .cha-accordion-header .fa-minus {
        width: 31px;
        height: 31px;
        flex-basis: 31px;
    }

    #cha-accordion .card-body.show {
        padding: 14px 12px !important;
    }
}


/* =========================================================
   MARKS APPROVAL MODALS
   Faculty CMS Theme
   CSS ONLY
========================================================= */

#courseMarksListModal,
#MissingMarksAlert {
    --cms-green: #17675F;
    --cms-green-dark: #10564F;
    --cms-green-soft: #EEF7F5;

    --cms-text: #252B2D;
    --cms-muted: #747D80;

    --cms-border: #E3E9E8;
    --cms-bg: #F7F9F9;

    --cms-danger: #D04444;
    --cms-danger-soft: #FFF3F3;

    --cms-success: #17675F;
    --cms-success-soft: #EEF7F5;
}


/* =========================================================
   MODAL BOX
========================================================= */

#courseMarksListModal .modal-content,
#MissingMarksAlert .modal-content {
    background: #ffffff;

    border: 0 !important;
    border-radius: 16px !important;

    overflow: hidden;

    box-shadow:
        0 24px 60px rgba(20, 48, 44, 0.16),
        0 6px 20px rgba(20, 48, 44, 0.08);
}


/* =========================================================
   HEADER
========================================================= */

#courseMarksListModal .modal-header,
#MissingMarksAlert .modal-header {
    min-height: 65px;

    display: flex;
    align-items: center;

    padding: 17px 22px !important;

    background: var(--cms-green) !important;

    border: 0 !important;
    border-bottom: 0 !important;
}


/* Modal title */

#courseMarksListModal .modal-title,
#MissingMarksAlert .modal-title {
    margin: 0;

    color: #ffffff !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    line-height: 1.4;

    letter-spacing: -0.2px;
}


/* Course name next to "Marks List" */

#courseMarksListModal #marks-name {
    color: rgba(255, 255, 255, 0.82) !important;

    font-size: 14px;
    font-weight: 400;

    margin-left: 5px;
}


/* =========================================================
   CLOSE BUTTON
========================================================= */

#courseMarksListModal .modal-header .close,
#MissingMarksAlert .modal-header .close {
    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0 !important;
    margin: 0 0 0 auto !important;

    background: rgba(255, 255, 255, 0.13) !important;

    border-radius: 9px;

    color: #ffffff !important;

    font-size: 24px;
    font-weight: 300;

    line-height: 1;

    opacity: 1 !important;

    text-shadow: none !important;

    transition:
        background-color .2s ease,
        transform .2s ease;
}


#courseMarksListModal .modal-header .close:hover,
#MissingMarksAlert .modal-header .close:hover {
    background: rgba(255, 255, 255, 0.22) !important;

    transform: rotate(4deg);

    color: #ffffff !important;
}


/* =========================================================
   MODAL BODY
========================================================= */

#courseMarksListModal .modal-body {
    padding: 22px 24px 24px !important;

    background: #ffffff;
}


#MissingMarksAlert .modal-body {
    padding: 24px !important;

    background: #ffffff;
}


/* =========================================================
   MARKS SUMMARY
   Total Marks / Students / Approved
========================================================= */

#courseMarksListModal
#marksApprovalForm > .form-group.row:first-child {
    margin: 0 0 20px 0 !important;

    padding: 15px 10px;

    background: var(--cms-green-soft);

    border: 1px solid #DDEAE7;
    border-radius: 12px;
}


#courseMarksListModal
#marksApprovalForm > .form-group.row:first-child > div {
    position: relative;

    padding: 4px 14px;
}


/* separators */

#courseMarksListModal
#marksApprovalForm > .form-group.row:first-child > div:not(:last-child)::after {
    content: "";

    position: absolute;

    right: 0;
    top: 5px;

    width: 1px;
    height: calc(100% - 10px);

    background: #D4E3E0;
}


/* Labels */

#courseMarksListModal .form-check-label {
    margin: 0 !important;

    color: var(--cms-muted) !important;

    font-size: 12px;
    font-weight: 500;
}


#courseMarksListModal .form-check-label b {
    margin-left: 0 !important;

    font-weight: 500 !important;
}


/* Values */

#courseMarksListModal #total_marks,
#courseMarksListModal #total_students,
#courseMarksListModal #total_approved_students {
    display: block;

    margin-top: 4px;

    color: var(--cms-green) !important;

    font-size: 18px;
    font-weight: 700;
}


/* =========================================================
   MARKS TABLE
========================================================= */

#courseMarksListModal #MarksListMainTable {
    width: 100% !important;

    margin-top: 0 !important;

    border: 1px solid var(--cms-border) !important;
    border-radius: 10px;

    overflow: hidden;

    border-collapse: separate !important;
    border-spacing: 0;
}


/* Table header */

#courseMarksListModal #MarksListMainTable thead,
#courseMarksListModal #MarksListMainTable thead.bnu-header {
    background: var(--cms-green) !important;
}


#courseMarksListModal #MarksListMainTable thead th {
    padding: 12px 12px !important;

    background: var(--cms-green) !important;

    border: 0 !important;
    border-right:
        1px solid rgba(255, 255, 255, 0.1) !important;

    color: #ffffff !important;

    font-size: 12px !important;
    font-weight: 600 !important;

    text-transform: none;

    vertical-align: middle;
}


#courseMarksListModal
#MarksListMainTable thead th:last-child {
    border-right: 0 !important;
}


/* Body cells */

#courseMarksListModal #MarksListMainTable tbody td {
    padding: 11px 12px !important;

    background: #ffffff;

    border-color: #EDF0EF !important;

    color: #444B4A;

    font-size: 13px;

    vertical-align: middle;
}


/* Alternate rows */

#courseMarksListModal
#MarksListMainTable tbody tr:nth-child(even) td {
    background: #FAFBFB;
}


/* Row hover */

#courseMarksListModal
#MarksListMainTable tbody tr:hover td {
    background: #F4F9F8 !important;
}


/* =========================================================
   DATATABLE FIXES
========================================================= */

#courseMarksListModal .dataTables_wrapper {
    color: var(--cms-text);
}


#courseMarksListModal table.dataTable {
    margin-bottom: 0 !important;
}


#courseMarksListModal .table-bordered {
    border-color: var(--cms-border) !important;
}


/* =========================================================
   APPROVE BUTTON
========================================================= */

#courseMarksListModal #marks_approval_btn {
    min-width: 115px;

    height: 42px;

    padding: 0 22px !important;

    margin-top: 15px !important;

    background: var(--cms-green) !important;

    border: 1px solid var(--cms-green) !important;
    border-radius: 9px !important;

    color: #ffffff !important;

    font-size: 14px !important;
    font-weight: 600 !important;

    box-shadow: none !important;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        box-shadow .2s ease,
        transform .2s ease;
}


#courseMarksListModal #marks_approval_btn:hover {
    background: var(--cms-green-dark) !important;

    border-color: var(--cms-green-dark) !important;

    box-shadow:
        0 6px 15px rgba(23, 103, 95, 0.18) !important;

    transform: translateY(-1px);
}


#courseMarksListModal #marks_approval_btn:focus {
    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.12) !important;
}


#courseMarksListModal #marks_approval_btn:disabled {
    background: #A8C4C0 !important;

    border-color: #A8C4C0 !important;

    cursor: not-allowed;

    box-shadow: none !important;

    transform: none;
}


/* =========================================================
   SUCCESS / ERROR MESSAGES
========================================================= */

#courseMarksListModal #success-msg {
    margin-top: 14px !important;

    padding: 11px 14px;

    background: var(--cms-success-soft) !important;

    border: 1px solid #CFE3DE !important;
    border-left: 3px solid var(--cms-success) !important;

    border-radius: 8px;

    color: var(--cms-green-dark) !important;

    font-size: 13px;
}


#courseMarksListModal #error-msg {
    margin-top: 14px !important;

    padding: 11px 14px;

    background: var(--cms-danger-soft) !important;

    border: 1px solid #F0D4D4 !important;
    border-left: 3px solid var(--cms-danger) !important;

    border-radius: 8px;

    color: #A93636 !important;

    font-size: 13px;
}


/* =========================================================
   INLINE CONFIRMATION BOX
========================================================= */

#courseMarksListModal #confirm_div {
    clear: both;

    margin-top: 15px !important;

    padding: 16px !important;

    background: var(--cms-danger-soft) !important;

    border: 1px solid #F0D4D4 !important;
    border-left: 3px solid var(--cms-danger) !important;

    border-radius: 10px;

    color: var(--cms-text) !important;
}


#courseMarksListModal #confirm_div h4 {
    margin: 0 0 10px;

    color: var(--cms-text) !important;

    font-size: 14px;
    font-weight: 500;

    line-height: 1.6;
}


#courseMarksListModal #confirm_div span {
    color: var(--cms-danger) !important;

    font-weight: 600;
}


/* =========================================================
   MISSING MARKS ALERT MODAL
========================================================= */

#MissingMarksAlert .modal-body h4 {
    margin: 0;

    color: var(--cms-text) !important;

    font-size: 14px !important;
    font-weight: 400;

    line-height: 1.65;
}


/* preserve danger emphasis */

#MissingMarksAlert .modal-body span {
    color: var(--cms-danger) !important;

    font-weight: 700;
}


#MissingMarksAlert .modal-body h4[style] {
    margin-top: 10px;

    color: var(--cms-danger) !important;

    font-weight: 600;
}


/* =========================================================
   MODAL FOOTER
========================================================= */

#MissingMarksAlert .modal-footer {
    padding: 14px 20px 18px;

    background: #FBFCFC;

    border-top: 1px solid var(--cms-border);
}


/* NO button */

#MissingMarksAlert #rfcm-btn-no,
#courseMarksListModal #rfcm-btn-no {
    min-width: 80px;
    height: 40px;

    padding: 0 18px;

    background: #ffffff !important;

    border: 1px solid #CCD6D4 !important;
    border-radius: 8px !important;

    color: #505856 !important;

    font-size: 13px;
    font-weight: 500;

    box-shadow: none !important;
}


#MissingMarksAlert #rfcm-btn-no:hover,
#courseMarksListModal #rfcm-btn-no:hover {
    background: #F3F6F5 !important;

    border-color: #AEBDBA !important;

    color: var(--cms-text) !important;
}


/* YES button */

#MissingMarksAlert #rfcm-btn-yes,
#courseMarksListModal #rfcm-btn-yes {
    min-width: 80px;
    height: 40px;

    padding: 0 18px;

    background: var(--cms-green) !important;

    border: 1px solid var(--cms-green) !important;
    border-radius: 8px !important;

    color: #ffffff !important;

    font-size: 13px;
    font-weight: 600;

    box-shadow: none !important;
}


#MissingMarksAlert #rfcm-btn-yes:hover,
#courseMarksListModal #rfcm-btn-yes:hover {
    background: var(--cms-green-dark) !important;

    border-color: var(--cms-green-dark) !important;
}


/* =========================================================
   SMALL ALERT MODAL
========================================================= */

#MissingMarksAlert .modal-dialog {
    max-width: 420px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    #courseMarksListModal .modal-dialog,
    #MissingMarksAlert .modal-dialog {
        margin: 12px;
    }

    #courseMarksListModal .modal-header,
    #MissingMarksAlert .modal-header {
        padding: 14px 16px !important;
    }

    #courseMarksListModal .modal-body,
    #MissingMarksAlert .modal-body {
        padding: 17px !important;
    }

    #courseMarksListModal
    #marksApprovalForm > .form-group.row:first-child > div {
        margin-bottom: 10px;
    }

    #courseMarksListModal
    #marksApprovalForm > .form-group.row:first-child > div::after {
        display: none;
    }

    #courseMarksListModal #marks_approval_btn {
        width: 100%;

        float: none !important;
    }
}
</style>
<h2>Marks Approval Session </h2>

        <div class="form-group row">
        <!-- <label class="col-md-12 form-label">Inline Select:</label> -->
        <div class="col-md-12">
        <select id="sess_id" class="SlectBox form-control" onchange="showfacultyList(this.value)">
            <!-- <option value="0">All</option> -->
            @foreach($getSession as $rec)
            <option value="{{$rec->sess_id}}">{{$rec->sess_name}}</option>
            
            @endforeach
        </select>
        </div>
        </div>
{{-- <div class="bnu-header text-white" style="padding-left: 20px;line-height: 40px;"><strong>&nbsp;</strong></div> --}}
<div id="accordian-main-div" style="max-height: 485px;overflow: auto;">
    <div id="cha-accordion" class="accordion" style="border: none !important;">
        <div class="card">
            <!-- @foreach($facultyList as $row)
                <div class="card-header cha-accordion-header" data-toggle="collapse" href="#collapse{{$row->fc_employee_id}}" id="collapse-{{$row->fc_employee_id}}" onclick="showCoursesList('{{$row->fc_co_sess_id}}','{{$row->fc_employee_id}}','{{$row->emp_email}}')">
                    <a class="card-title">
                        <i class="fa fa-plus" aria-hidden="true" style="float: left"></i>
                        <span style="margin-left: 20px;">{{$row->prefix}} {{$row->emp_f_name}} {{$row->emp_l_name}} </span>
                    </a>
                </div>
             <div id="collapse{{$row->fc_employee_id}}" class="card-body collapse" data-parent="#cha-accordion" > -->

               
            <!-- @endforeach -->
            <div id="facultycoList"></div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="courseMarksListModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Marks List<span id="marks-name"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="marksApprovalForm" name="marksApprovalForm" role="form">
                    <div class="form-group row">
                        <div class="col-md-4 col-sm-6 col-6">
                            <label for="total_marks" class="form-check-label"><b style="margin-left: 7px;">Total Marks :</b></label>
                            <span id="total_marks" name="total_marks"></span>
                        </div>
                        <div class="col-md-4 col-sm-6 col-6">
                            <label for="total_students" class="form-check-label"><b style="margin-left: 7px;">Total Students :</b></label>
                            <span id="total_students" name="total_students"></span>
                        </div>
                        <div class="col-md-4 col-sm-6 col-6">
                            <label for="total_approved_students" class="form-check-label"><b style="margin-left: 7px;">Approved :</b></label>
                            <span id="total_approved_students" name="total_approved_students"></span>
                        </div>
                    </div>
                    @csrf
                    <table id="MarksListMainTable" class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                        <thead class="bnu-header">
                        <tr>
                            <th class="tab-title-center">ID.</th>
                            {{--<th class="tab-title-center">Sr.</th>--}}
                            <th class="tab-title-center">Enroll No.</th>
                            <th class="tab-title">Name</th>
                            <th class="tab-title-center">Marks</th>
                            <th class="tab-title-center">Status</th>
                        </tr>
                    </table>
                    <div class="alert alert-danger" id="error-msg" style="display:none;margin-top: 5px"></div>
                    <div class="alert alert-success" id="success-msg" style="display:none;margin-top: 5px;"></div>
                    <input type="hidden" id="marks_null_check">
                    <input type="submit" value="Approve" id="marks_approval_btn" class="btn btn-primary btn-lg float-md-right" style="margin-top: 5px;">
                    <div id="confirm_div" class="alert alert-danger" style="display:none;margin-top: 5px"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="MissingMarksAlert">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alert!</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4><span id="count_missing_marks" style="color: red;"></span> Students have missing marks. Approving NULL or Blank marks will result in <span style="color: red;">I</span> Grade.</h4>
                <br>
                <h4 style="color: red;">Are you sure to proceed? </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="rfcm-btn-no">No</button>
                <button type="button" class="btn btn-primary" id="rfcm-btn-yes">Yes</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
    <script type="text/javascript">
$(document).ready(function() {
var sess=$('#sess_id').val();
    showfacultyList(sess);
});
        $('.collapse').on('shown.bs.collapse', function(e) {
            var $card = $(this).closest('.card');
            $('#accordian-main-div').animate({
//                scrollTop: $card.offset().top+305
//                scrollTop: $card.offset().top - $('.collapse').height() - 100 Final Working
//                scrollTop: $card.offset({top:110,left:250})
            }, 500);
        });

        function toggleIcon(e) {
//            alert($(e.target).height());

//            $('#accordian-main-div').animate({
//                scrollTop: $(e.target).offset().top-180
//            }); Final Working

            $(e.target)
                .prev('.card-header')
                .find(".fa")
                .toggleClass('fa-plus fa-minus');
        }
        $('.accordion').on('hidden.bs.collapse', toggleIcon);
        $('.accordion').on('shown.bs.collapse', toggleIcon);
        function showfacultyList(sess_id)
        {
            //alert(sess_id);
            $('#facultycoList').html('');
            $.ajax({
                url: '{{url('teacher/faculty/list')}}/'+sess_id,
                type: "GET",
                dataType: "HTML",
                success:function(data){
                    var i;
                    var obj = JSON.parse(data);
                   var html='';
                   $('#collapse').html('');
                    $.each(obj, function (key,value) {
                        
                    html+="<div class='card-header cha-accordion-header' data-toggle='collapse' href='#collapse"+value.fc_employee_id+"' id='collapse-"+value.fc_employee_id+"' onclick='showCoursesList("+value.fc_co_sess_id+","+value.fc_employee_id+",\""+value.emp_email+"\")' >";
                    html+='<a class="card-title">';
                    html+='<i class="fa fa-plus" aria-hidden="true" style="float: left"></i><span style="margin-left: 20px;">'+value.prefix+' '+value.emp_f_name+' '+value.emp_l_name+' </span>';
                    html+='</a>';
                    html+='</div>';
                    html+='<div id="collapse'+value.fc_employee_id+'" class="card-body collapse" data-parent="#cha-accordion" ></div>';  
                    });
                    //console.log(html);
                    $('#facultycoList').html(html);
                }
            });
        }



        function showCoursesList(sess_id,fc_emp_id,emp_email)
        {
            $.ajax({
                url: '{{url('teacher/courses/list')}}/'+sess_id+'/'+fc_emp_id,
                type: "GET",
                dataType: "HTML",
                success:function(data){
                    $('#collapse'+fc_emp_id).html('');
                    var i;
                    var obj = JSON.parse(data);
//                    alert(JSON.stringify(obj));
                    $.each(obj, function (key,value) {
                        var obj_co_id           = obj[key]['course_offer_id'];
                        var obj_course_cd_name  = obj[key]['course_cd_name'];
                        var obj_co_course_cr    = obj[key]['co_course_cr'];
                        var obj_co_section      = obj[key]['co_section'];
                        var obj_mid_count       = obj[key]['mid_count'];
                        $('#collapse'+fc_emp_id).append('<table><tr><td id="cha-course-title-'+obj_co_id+'" align="left" class="course-title">'+obj_course_cd_name+' '+obj_co_course_cr+' ('+obj_co_section+')</td></tr></table>');
                        $('#collapse'+fc_emp_id).append('<div class="form-group row teacher-courses" style="margin:15px 0px 30px 10px;" id="course-'+obj_co_id+'"></div>');

                        if(obj_mid_count>1)
                        {
                            $('#course-'+obj_co_id).append('<div class="col-lg-1 col-md-2 col-sm-2 col-3 text-align-center" id="midterm-'+obj_co_id+'" ><div class="dropdown"><img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}"><div class="dropdown-content" id="midterm-list-'+obj_co_id+'"></div><span class="txtBlue">Midterm</span></div></div>');
                            for(i=1;i<=obj_mid_count;i++){
                                $('#midterm-list-'+obj_co_id).append('<a href="#" onclick="openMarksListModal('+obj_co_id+',3,'+i+',\''+obj_course_cd_name+'\')">Midterm '+i+'</a>');
                            }
                        }else{
                            $('#course-'+obj_co_id).append('<div class="col-lg-1 col-md-2 col-sm-2 col-3 text-align-center" id="midterm-'+obj_co_id+'" ><div class="dropdown"><img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}" onclick="openMarksListModal('+obj_co_id+',3,1,\''+obj_course_cd_name+'\')"><div class="dropdown-content" id="midterm-list-'+obj_co_id+'"></div><span class="txtBlue">Midterm</span></div></div>');
                        }
                        $('#course-'+obj_co_id).append('<div class="col-lg-1 col-md-2 col-sm-2 col-3 text-align-center" id="final-'+obj_co_id+'" ><img title="Finalterm Exam" src="{{asset('images/btn_GDB_1.png')}}" onclick="openMarksListModal('+obj_co_id+',4,1,\''+obj_course_cd_name+'\')"><span class="txtBlue">Final Term</span></div>');
                        $('#collapse'+fc_emp_id).append('<hr>');
                    });
                }
            });
        }

        function openMarksListModal(co_id,assess_type,num,course)
        {
            if(assess_type==3)
            {
                var assess_name = "Mid Term";
            }else if(assess_type==4){
                var assess_name = "Final Term";
            }
            $('#marks-name').html(' '+course+' ('+assess_name+' '+num+')');
            $("#courseMarksListModal").modal();
            openMarksListPopup(co_id,assess_type,num);
        }

        function openMarksListPopup(co_id,assess_type,num)
        {

            $("#marks_approval_btn").show();
            $("#confirm_div").hide();
           // var errorCheck = $.fn.dataTable.ext.errMode = 'throw';
            $("#marks_approval_btn").removeAttr("disabled");
            $("#error-msg").css('display','none');
            $("#success-msg").css('display','none');
            $('#total_marks').html('');
            $('#total_students').html('');
            $('#total_approved_students').html('');
            var get_total_marks=0;
            if(co_id && assess_type && num)
            {
                $("#sel_co_id").val(co_id);
                $("#sel_assess_type").val(assess_type);
                var url = '{{url('teacher/courses/approve/list/')}}/'+co_id+"/"+assess_type+"/"+num;
                var table = $('#MarksListMainTable').DataTable();
                table.destroy();
                var t = $('#MarksListMainTable').DataTable( {
                    "keys" : {columns:[4]},
                    "searching": false,
                    "paging": false,
                    "bInfo": false,
                    "language": {
                        "emptyTable": "Marks Not Yet Entered"
                    },
                    "ajax": {
                        "url": url,
                        "dataSrc": ""
                    },
                    "columnDefs": [{
                        "targets": 0,
                        "visible": false,
                        "searchable": false
                    },{
                        "targets": 1,
                        "className": "text-center",
                        "responsivePriority": 1,
                        "orderable": false,
                    },{
                        "targets": 2,
                        "className": "text-left",
                        "orderable": false,
                    },{
                        "targets": 3,
                        "className": "text-center",
                        "searching": false,
                        "orderable": false,
                        "responsivePriority": 2
                    },{
                        "targets": 4,
                        "className": "text-center",
                        "searching": false,
                        "orderable": false,
                        "responsivePriority": 2
                    }],
                    "columns": [
                        { data: 'cam_id'},
                        { data: 'sai_enrollment_no'},
                        { mRender: function (data, type, row) {
                            $('#total_marks').html(row.total_marks);
                            $('#total_students').html(row.total_students);
                            $('#total_approved_students').html(row.total_approved_students);
                            // if(row.total_students==row.total_approved_students)
                            // {
                            //     $('#marks_approval_btn').prop('disabled', true);
                            // }else{
                                $('#marks_approval_btn').prop('disabled', false);
                            //}
                            var std_name = row.spi_name;
                            if(std_name.length<=20){
                                return std_name;
                            }else{
                                return std_name.substr(0, 20) + "...";
                            }
                        }
                        },
                        { data: 'obtained_marks'},
                        { mRender: function (data, type, row) {

                            if(row.missing_marks>0)
                            {
                                $("#marks_null_check").val(row.missing_marks);
                            }else{
                                $("#marks_null_check").val(0);
                            }

                            if(row.approval_status==1){
                                return '<span style="color:green"><i class="fa fa-check" aria-hidden="true"></i></span><input type="hidden" id="approval_status[]" name="approval_status[]" value="1"/><input type="hidden" id="cam_id[]" name="cam_id[]" value="'+row.cam_id+'"/><input type="hidden" id="obt_marks[]" name="obt_marks[]" value="'+row.obtained_marks+'" />';
                            }else{
                                return '<span style="color:blue"> </span>'+'<input type="hidden" id="approval_status[]" name="approval_status[]" value="0"/><input type="hidden" id="cam_id[]" name="cam_id[]" value="'+row.cam_id+'"/><input type="hidden" id="obt_marks[]" name="obt_marks[]" value="'+row.obtained_marks+'" />';
                            }
                        }
                        }
                    ]
                });

//                t.on( 'order.dt search.dt', function () {
//                    t.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
//                        cell.innerHTML = i+1;
//                    } );
//                } ).draw();
            }
        }

        $("#marksApprovalForm").submit(function(e)
        {
            if($("#marks_null_check").val()>0)
            {
                e.preventDefault();
                var modalConfirm = function(callback){
                    $("#confirm_div").css('display','block');
                    $("#confirm_div").html('<h4><span id="count_missing_marks" style="color: red;"></span> Students have missing marks. Approving NULL or Blank marks will result in <span style="color: red;">I</span> Grade.</h4><br><h4 style="color: red;">Are you sure to proceed? </h4>' +
                        '<button type="button" class="btn btn-default" id="rfcm-btn-no">No</button><button type="button" class="btn btn-primary" id="rfcm-btn-yes">Yes</button>');
                    $("#count_missing_marks").html($("#marks_null_check").val());
                    $("#marks_approval_btn").hide();

                    $("#rfcm-btn-yes").on("click", function(){
                        callback(true);
                        $("#confirm_div").empty();
                    });

                    $("#rfcm-btn-no").on("click", function(){
                        callback(false);
                        $("#confirm_div").empty();
                    });
                };

                modalConfirm(function(confirm){
                    if(!confirm){
                        $('#courseMarksListModal').modal('hide');
                        return 0;
                    }else{
                        approve_list($("#marksApprovalForm").serializeArray());
                    }
                });
            }else{
                e.preventDefault();
                approve_list($("#marksApprovalForm").serializeArray());
            }
        });

        function approve_list(form_data)
        {
            $("#error-msg").css('display','none');
            $("#success-msg").css('display','none');
            $("#marks_approval_btn").attr('disabled','true');
            $.ajax({
                type:'POST',
                url:'{{url('teacher/courses/store/approval')}}',
                data: form_data,
                success:function(data){
                    $("#confirm_div").css('display','none');
                    if(data==1){
                        $("#success-msg").css('display','block');
                        $("#success-msg").html('<strong>Success!</strong> Marks Approved Successfully');
                        $('#success-msg').focus();
                        $('#MarksListMainTable').DataTable().ajax.reload();
                        setTimeout(function(){
                            $('#courseMarksListModal').modal('hide');
                        },1500);
                    }else if(data==2){
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Error!</strong> No Marks To Approve');
                        $('#error-msg').focus();
                    }
                },
                error: function(data,status, response) {
                    var errorData = jQuery.parseJSON(data.responseText);
                    var error = errorData.errors;
                    var msg = errorData.message;
                    if(msg=='The given data was invalid.')
                    {
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Error!</strong> Invalid Marks, Please Correct & Try Again');
                        $('#error-msg').focus();
                        $("#marks_approval_btn").removeAttr("disabled");
                    }
                }
            });
        }
    </script>
@stop