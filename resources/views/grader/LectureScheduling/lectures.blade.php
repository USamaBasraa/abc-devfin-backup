@extends('layouts.app',['pageTitle' => 'Lectures'])
@section('content')

{{-- @dd("!23"); --}}

<?php
//print_r($all_classes );
if(count($all_classes)==0){
    echo '<h1>Semester Timetable not Created Please Contact to RO Department</h1>';
}else{
?>



<div class="container-fluid" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
    <div class="">
        <h5>Course Outline</h5>
        <h3 class="card-title ">{{$all_classes[0]->course_name}}</h3>
    </div>
    <div class="card-header" style="border-radius:10px;padding-left:0 !important;padding-right:0 !important;">
        @include('grader/LectureScheduling.lecture_scheduling_header', ['active_tab_name' => 'Lecture'])
    </div>

</div>


<style type="text/css">
    .lecture_icons {
        bordered: solid 2px black;
        text-align: center;
    }

    .displaynone {
        display: none;
    }

    /* ===========================
   Lecture Cards
=========================== */

    #home .card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 1px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
        margin-bottom: 18px;
    }

    #home .card-header {
        background: #fff !important;
        border: 0 !important;
        padding: 18px 18px 6px;
        min-height: auto !important;
    }

    #home .card-body {
        background: #fff;
        padding: 0 18px 18px;
    }

    #home .card-footer {
        background: #fff;
        border-top: 1px solid #E8ECEF;
        padding: 18px;
    }

    /* ===========================
   Lecture Heading
=========================== */

    #home .card-header h5 {
        display: inline;
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #17675F;
    }

    #home .card-header {
        color: #17675F;
        font-size: 16px;
        font-weight: 500;
    }

    /* ===========================
   Lecture Description
=========================== */

    #home .card-text {
        margin: 0;
        color: #505050 !important;
        font-size: 16px;
        line-height: 1.8;
    }

    /* Hide old edit icon */
    #home .card-text>.fa-edit {
        display: none;
    }

    /* ===========================
   Footer Icons
=========================== */

    .lecture_icons {
        text-align: center;
    }

    .lecture_icons .card-block {
        padding: 0 !important;
    }

    .lecture_icons a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #3D3D3D !important;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        transition: .25s;
    }

    .lecture_icons a img {
        width: 28px;
        height: 28px;
        margin-bottom: 10px;
        transition: .25s;
    }

    .lecture_icons a:hover {
        color: #17675F !important;
    }

    .lecture_icons a:hover img {
        transform: translateY(-2px);
    }

    /* ===========================
   Today's Badge
=========================== */

    .badge-danger {
        background: #E85D5D !important;
        border-radius: 30px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
    }

    /* ===========================
   Responsive
=========================== */

    @media(max-width:768px) {

        #home .card-header,
        #home .card-body,
        #home .card-footer {
            padding-left: 15px;
            padding-right: 15px;
        }

        .lecture_icons {
            margin-bottom: 18px;
        }
    }

.card-footer .row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin:0;
}

.lecture_icons{
    display:flex;
    justify-content:center;
    align-items:center;
    padding:0;
    text-align:center;
}

.lecture_icons .card-block{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:0 !important;
    margin:0 !important;
}

.lecture_icons a{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:8px;
    width:100%;
    min-height:64px;
    text-align:center;
    text-decoration:none !important;
    color:#3F3F46 !important;
    font-size:14px;
    font-weight:500;
    line-height:1.3;
}

.lecture_icons a img{
    display:block;
    margin:0 auto;
}

.lecture_icons br{
    display:none;
}

/* Header Layout */
.card-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-height:70px;
    padding:18px !important;
    position:relative;
}

/* Left Content */
.card-header > .col-6.col-md-10.col-xl-10{
    flex:1;
    max-width:calc(100% - 160px);
    padding:0;
}

/* Right Badge Container */
.card-header > .header-option{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    float:none !important;
    padding-top:0 !important;
    margin:0 !important;
    display:flex;
    align-items:center;
    justify-content:flex-end;
}

/* Today's Class Badge */
.card-header .badge-danger{
    display:flex;
    align-items:center;
    justify-content:center;
    height:30px;
    padding:0 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}

/* ==========================
   Card Body
========================== */

.card-body{
    padding:20px 24px !important;
    background:#fff;
}

.card-text{
    margin:0 !important;
    color:#4B5563 !important;
    font-size:16px;
    line-height:1.8;
    font-weight:400;
}

/* Each Lecture Content Row */
.card-body .container-fluid{
    padding:0 !important;
    margin:0 0 14px;
    display:flex;
    align-items:flex-start;
    gap:10px;
}

/* Content Text */
.card-body .container-fluid span{
    flex:1;
    color:#4B5563 !important;
    word-break:break-word;
}

/* Edit Icon */
.card-body .fa-edit{
    color:#17675F !important;
    cursor:pointer;
    font-size:16px;
    transition:.2s;
    margin-top:4px;
}

.card-body .fa-edit:hover{
    color:#12564F !important;
}

/* Expanded Section */
.card-body .collapse{
    width:100%;
    margin-top:12px;
}

.card-body .form-control{
    border-radius:8px;
    border:1px solid #D9E2E7;
    min-height:44px;
}

/* Delete Button */
.card-body .btn-secondary{
    border-radius:8px;
    padding:8px 18px;
    font-size:14px;
    font-weight:500;
}

/* Empty Content */
#no_content_here{
    margin:0;
    color:#1F776E !important;
    font-size:18px;
    font-weight:600;
}

/* Each content item */
.card-body .container-fluid{
    display:block !important;
    width:100%;
    padding:0 !important;
    margin:0 0 14px;
}

/* Content text and edit icon */
.card-body .container-fluid > span{
    display:inline;
    color:#4B5563 !important;
    word-break:normal;
    overflow-wrap:anywhere;
}

.card-body .container-fluid > .fa-edit{
    display:inline-block;
    margin-left:8px;
    margin-top:0;
    vertical-align:middle;
}

/* Expanded edit/delete block */
.card-body .container-fluid > .collapse{
    width:100%;
    margin-top:12px;
    padding:14px;
    border:1px solid #E4E9ED;
    border-radius:10px;
    background:#F8FAFB;
}

/* Input and delete button row */
.card-body .container-fluid > .collapse.show{
    display:flex !important;
    align-items:center;
    gap:12px;
}

.card-body .container-fluid > .collapse > div:first-child{
    width:auto !important;
    flex:1;
}

.card-body .container-fluid > .collapse > div:last-child{
    display:flex !important;
    align-items:center;
    flex:0 0 auto;
}

.card-body .container-fluid > .collapse .form-control{
    width:100%;
    min-height:46px;
}

.card-body .container-fluid > .collapse .btn-secondary{
    margin:0;
    white-space:nowrap;
}



/* ======================================
   Production Modal UI
====================================== */

.modal .modal-dialog{
    /* max-width:480px; */
    margin:2rem auto;
}

.modal .modal-content{
    border:0;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 16px 40px rgba(0,0,0,.18);
}

/* Header */

.modal .modal-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-height:64px;
    padding:18px 22px;
    border:0;
    border-bottom:1px solid #E8ECEF;
    background:#17675F;
}

.modal .modal-title{
    margin:0;
    font-size:20px;
    font-weight:600;
    color:#fff;
}

/* Close Button */

.modal .close{
    width:36px;
    height:36px;
    border:0;
    border-radius:50%;
    background:rgba(255,255,255,.12);
    color:#fff;
    opacity:1;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.2s;
}

.modal .close:hover{
    background:rgba(255,255,255,.22);
}

.modal .close span{
    font-size:24px;
    line-height:1;
}

/* Body */

.modal .modal-body{
    padding:28px 24px;
    background:#fff;
}

.modal .modal-body h5{
    margin:0;
    color:#374151;
    font-size:18px;
    font-weight:500;
    text-align:center;
    line-height:1.6;
}

/* Success / Error */

#delete_content_error,
#course_content_success{
    margin-top:18px;
    padding:12px 16px;
    border-radius:8px;
    font-size:14px;
    font-weight:500;
    text-align:center;
}

#delete_content_error{
    background:#FFECEC;
    color:#D32F2F !important;
}

#course_content_success{
    background:#EAF8EF;
    color:#198754 !important;
}

/* Footer */

.modal .modal-footer{
    justify-content:center;
    gap:12px;
    padding:18px 24px 24px;
    border:0;
    background:#fff;
}

/* Primary Button */

.modal .btn-primary{
    /* min-width:130px; */
    /* height:46px; */
    border:0;
    border-radius:8px;
    background:#17675F !important;
    font-size:15px;
    font-weight:600;
    transition:.2s;
}

.modal .btn-primary:hover{
    background:#14584F !important;
    transform:translateY(-1px);
}

/* Backdrop */

.modal-backdrop.show{
    opacity:.55;
}

/* =========================================================
   LECTURES SCREEN — FIGMA STYLE OVERRIDE
   Style only — no HTML / JS / functionality changes
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. PAGE
   ========================================================= */

.page-body {
    font-family: 'Montserrat', sans-serif;
}

.page-body {
    color: #252525;
}


/* Top Course heading */
.container-fluid > div > h5 {
    margin: 0 0 6px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    line-height: 1.2 !important;
    font-weight: 600 !important;

    color: #252525 !important;
}


/* Course name */
.container-fluid > div > .card-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    line-height: 1.35 !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* =========================================================
   2. LECTURE PAGE CONTENT
   ========================================================= */

.page-body > .row {
    width: 100% !important;

    margin: 0 !important;

    display: block !important;
}


.page-body .tab-content,
.page-body .tab-pane {
    width: 100% !important;
}


#home > .container-fluid {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;
}


#home > .container-fluid > .row {
    margin: 0 !important;
}


/* =========================================================
   3. LECTURE CARD
   ========================================================= */

#home .col-md-12 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}


#home .card {
    width: 100% !important;

    margin: 0 0 20px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


#home .card > .card-block {
    background: #ffffff !important;
}


/* =========================================================
   4. LECTURE CARD HEADER
   ========================================================= */

#home .card-header {
    position: relative !important;

    width: 100% !important;
    min-height: 78px !important;
    height: auto !important;

    padding: 18px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #ffffff !important;

    border: 0 !important;
    border-bottom: 1px solid #EEEEEE !important;
    border-radius: 0 !important;
}


/* Reset Bootstrap column behavior in header */
#home .card-header > .col-6.col-md-10.col-xl-10 {
    width: auto !important;
    max-width: calc(100% - 150px) !important;

    flex: 1 1 auto !important;

    padding: 0 !important;
}


/* Inline styles currently wrap heading/date */
#home .card-header > .col-6 > div:first-child {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    line-height: 1.55 !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* Lecture 1 / Lecture 2 */
#home .card-header h5 {
    display: block !important;

    margin: 0 0 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    line-height: 1.25 !important;
    font-weight: 600 !important;

    color: #17675F !important;
}


/* Remove useless spacer */
#home .card-header > .col-6 > div:last-child {
    display: none !important;
}


/* =========================================================
   5. TODAY BADGE
   ========================================================= */

#home .card-header .header-option {
    position: static !important;

    width: auto !important;

    flex: 0 0 auto !important;

    margin: 0 !important;
    padding: 0 !important;

    transform: none !important;

    float: none !important;

    display: flex !important;
    align-items: center !important;
}


#home .badge-danger {
    min-width: 100px !important;
    height: 30px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 50px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    line-height: 1 !important;
    font-weight: 500 !important;

    color: #ffffff !important;

    box-shadow: none !important;

    animation: none !important;
}


/* =========================================================
   6. LECTURE CONTENT BODY
   ========================================================= */

#home .card-body {
    min-height: 90px !important;

    padding: 20px !important;

    background: #ffffff !important;
}


#home .card-text {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    line-height: 1.65 !important;
    font-weight: 400 !important;

    color: #252525 !important;

    overflow: visible !important;
}


/* Each lecture content row */
#home .card-body .container-fluid {
    width: 100% !important;

    margin: 0 0 12px !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    font-size: 16px !important;
    line-height: 1.55 !important;

    color: #252525 !important;
}


#home .card-body .container-fluid:last-child {
    margin-bottom: 0 !important;
}


#home .card-body .container-fluid > span {
    display: inline !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    line-height: 1.55 !important;
    font-weight: 400 !important;

    color: #252525 !important;

    word-break: normal !important;
    overflow-wrap: anywhere;
}


/* =========================================================
   7. EDIT ICON
   ========================================================= */

#home .card-body .container-fluid > .fa-edit {
    display: inline-flex !important;

    width: 28px !important;
    height: 28px !important;

    margin: 0 0 0 8px !important;

    vertical-align: middle !important;

    align-items: center !important;
    justify-content: center !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border-radius: 50% !important;

    font-size: 13px !important;

    color: #17675F !important;

    cursor: pointer;

    transition: background-color .2s ease,
                color .2s ease !important;
}


#home .card-body .container-fluid > .fa-edit:hover {
    background: #17675F !important;
    color: #ffffff !important;

    transform: none !important;
}


/* =========================================================
   8. EMPTY CONTENT
   ========================================================= */

#no_content_here {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    line-height: 1.5 !important;
    font-weight: 500 !important;

    color: #AAAAAA !important;
}


/* =========================================================
   9. EDIT / DELETE EXPANDED BLOCK
   ========================================================= */

#home .card-body .container-fluid > .collapse {
    width: 100% !important;

    margin-top: 14px !important;
    padding: 14px !important;

    background: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


#home .card-body .container-fluid > .collapse.show {
    display: flex !important;

    align-items: center !important;

    gap: 12px !important;
}


#home .card-body .collapse > div:first-child {
    flex: 1 1 auto !important;

    width: auto !important;
}


#home .card-body .collapse > div:last-child {
    flex: 0 0 auto !important;

    display: flex !important;
    align-items: center !important;
}


/* Content input */
#home .card-body .collapse .form-control {
    width: 100% !important;

    min-height: 40px !important;
    height: 40px !important;

    padding: 0 12px !important;

    background: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


#home .card-body .collapse .form-control:focus {
    outline: 0 !important;

    border-color: #17675F !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, .08) !important;
}


/* Delete */
#home .card-body .btn-secondary {
    min-width: 84px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 16px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #ffffff !important;

    border: 1px solid #D9534F !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;

    color: #D9534F !important;
}


#home .card-body .btn-secondary:hover {
    background: #D9534F !important;

    border-color: #D9534F !important;

    color: #ffffff !important;
}


/* =========================================================
   10. FOOTER / LECTURE ACTIONS
   ========================================================= */

#home .card-footer {
    width: 100% !important;

    padding: 16px 20px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-top: 1px solid #EEEEEE !important;
}


#home .card-footer > .row {
    width: 100% !important;

    margin: 0 !important;

    display: flex !important;

    align-items: stretch !important;
    justify-content: flex-start !important;

    flex-wrap: nowrap !important;

    gap: 10px !important;
}


/* Override bootstrap action columns */
#home .card-footer .lecture_icons {
    width: auto !important;
    max-width: none !important;

    flex: 1 1 0 !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
}


/* Icon action inner wrapper */
#home .lecture_icons .card-block {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
}


/* Each action */
#home .lecture_icons a {
    width: 100% !important;
    min-height: 76px !important;

    padding: 9px 6px !important;

    display: flex !important;

    flex-direction: column !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 7px !important;

    background: #ffffff !important;

    border: 1px solid #EEEEEE !important;
    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    line-height: 1.25 !important;
    font-weight: 500 !important;

    color: #252525 !important;

    text-align: center !important;
    text-decoration: none !important;

    cursor: pointer;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        color .2s ease !important;
}


#home .lecture_icons a:hover {
    background: rgba(23, 103, 95, 0.06) !important;

    border-color: rgba(23, 103, 95, .35) !important;

    color: #17675F !important;

    text-decoration: none !important;
}


/* Action icons */
#home .lecture_icons a img {
    display: block !important;

    width: 28px !important;
    height: 28px !important;

    margin: 0 auto !important;

    object-fit: contain !important;

    transition: none !important;
}


#home .lecture_icons a:hover img {
    transform: none !important;
}


/* Old BR tags not required */
#home .lecture_icons br {
    display: none !important;
}


/* Cloud drive nested span */
#home .lecture_icons a span {
    font: inherit !important;
    color: inherit !important;
}


/* Disabled action */
#home .lecture_icons .disabled-icon {
    opacity: .4 !important;

    cursor: not-allowed !important;

    pointer-events: none !important;
}


/* =========================================================
   11. DELETE MODAL — SAME FIGMA SYSTEM
   ========================================================= */

#deletepopup .modal-dialog {
    max-width: 480px !important;

    margin: 80px auto !important;
}


#deletepopup .modal-content {
    overflow: hidden !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 12px 35px rgba(0, 0, 0, .12) !important;
}


#deletepopup .modal-header {
    min-height: 64px !important;

    padding: 18px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #17675F !important;

    border: 0 !important;
}


#deletepopup .modal-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    color: #ffffff !important;
}


#deletepopup .close {
    width: 34px !important;
    height: 34px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(255, 255, 255, .12) !important;

    border: 0 !important;
    border-radius: 50% !important;

    color: #ffffff !important;

    opacity: 1 !important;
}


#deletepopup .close:hover {
    background: rgba(255, 255, 255, .20) !important;
}


#deletepopup .modal-body {
    padding: 28px 24px !important;

    background: #ffffff !important;
}


#deletepopup .modal-body h5 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    line-height: 1.5 !important;
    font-weight: 500 !important;

    color: #252525 !important;

    text-align: center !important;
}


#deletepopup .modal-footer {
    padding: 0 24px 24px !important;

    display: flex !important;
    justify-content: center !important;

    background: #ffffff !important;

    border: 0 !important;
}


#deletepopup #delpopup {
    min-width: 100px !important;
    height: 44px !important;

    padding: 0 20px !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;

    color: #ffffff !important;
}


#deletepopup #delpopup:hover {
    background: #125A53 !important;

    transform: none !important;
}


/* =========================================================
   12. MOBILE
   ========================================================= */

@media (max-width: 991px) {

    #home .card-footer > .row {
        flex-wrap: wrap !important;
    }

    #home .card-footer .lecture_icons {
        flex: 0 0 calc(33.333% - 7px) !important;
    }
}


@media (max-width: 767px) {

    #home .card-header {
        align-items: flex-start !important;

        padding: 16px !important;
    }

    #home .card-header > .col-6.col-md-10.col-xl-10 {
        max-width: calc(100% - 105px) !important;
    }

    #home .card-header h5 {
        font-size: 16px !important;
    }

    #home .card-body,
    #home .card-footer {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    #home .card-footer .lecture_icons {
        flex: 0 0 calc(50% - 5px) !important;
    }

    #home .card-body .container-fluid > .collapse.show {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    #home .card-body .collapse > div:last-child {
        width: 100% !important;
    }

    #home .card-body .btn-secondary {
        width: 100% !important;
    }
}


@media (max-width: 480px) {

    #home .card-footer .lecture_icons {
        flex: 0 0 100% !important;
    }

    #home .lecture_icons a {
        min-height: 68px !important;

        flex-direction: row !important;

        justify-content: flex-start !important;

        padding: 12px 16px !important;

        text-align: left !important;
    }

    #home .lecture_icons a img {
        margin: 0 !important;
    }
}





/* =========================================================
   LECTURES SCREEN — FIGMA MODAL FINAL OVERRIDE
   ---------------------------------------------------------
   STYLE ONLY
   No HTML / JS / Bootstrap modal logic changes
   ========================================================= */


/* =========================================================
   1. MODAL BASE
   ========================================================= */

.modal {
    font-family: 'Montserrat', sans-serif !important;
    color: #252525;
}

.modal *,
.modal input,
.modal textarea,
.modal select,
.modal button {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. MODAL WIDTH / POSITION
   ========================================================= */

.modal .modal-dialog {
    width: auto !important;
    max-width: 560px !important;

    margin-top: 48px !important;
    margin-right: auto !important;
    margin-bottom: 48px !important;
    margin-left: auto !important;
}


/* =========================================================
   3. MODAL CARD
   ========================================================= */

.modal .modal-content {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14) !important;
}


/* =========================================================
   4. HEADER
   ========================================================= */

.modal .modal-header {
    min-height: 64px !important;

    padding: 16px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #17675F !important;

    border: 0 !important;
    border-bottom: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   5. TITLE
   ========================================================= */

.modal .modal-title,
.modal .modal-header h3,
.modal .modal-header h4,
.modal .modal-header h5,
.modal .modal-header h6 {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    line-height: 1.35 !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;

    text-align: left !important;
}


/* =========================================================
   6. CLOSE BUTTON
   Existing Bootstrap close action stays untouched
   ========================================================= */

.modal .modal-header .close {
    flex: 0 0 34px !important;

    width: 34px !important;
    height: 34px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(255, 255, 255, 0.12) !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    color: #FFFFFF !important;

    opacity: 1 !important;

    text-shadow: none !important;

    transition:
        background-color .2s ease !important;
}

.modal .modal-header .close:hover,
.modal .modal-header .close:focus {
    background: rgba(255, 255, 255, 0.20) !important;

    color: #FFFFFF !important;

    outline: 0 !important;
    box-shadow: none !important;
}

.modal .modal-header .close span {
    display: block;

    margin: 0;
    padding: 0;

    color: #FFFFFF !important;

    font-size: 24px !important;
    line-height: 1 !important;
    font-weight: 400 !important;
}


/* =========================================================
   7. BODY
   ========================================================= */

.modal .modal-body {
    padding: 24px 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    line-height: 1.55 !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

.modal .modal-body p {
    margin-top: 0;

    font-size: 14px;
    line-height: 1.55;

    color: #252525;
}


/* =========================================================
   8. FORM GROUPS
   ========================================================= */

.modal .form-group {
    margin-bottom: 16px !important;
}


/* =========================================================
   9. LABELS
   ========================================================= */

.modal label,
.modal .control-label {
    margin: 0 0 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    line-height: 1.35 !important;
    font-weight: 500 !important;

    color: #252525 !important;
}


/* =========================================================
   10. INPUTS / SELECTS
   ========================================================= */

.modal .form-control,
.modal input.form-control,
.modal select.form-control {
    width: 100% !important;

    min-height: 40px !important;

    padding: 8px 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    outline: 0 !important;
    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    line-height: 1.4 !important;
    font-weight: 400 !important;

    color: #252525 !important;

    transition:
        border-color .15s ease,
        box-shadow .15s ease !important;
}

.modal .form-control::placeholder {
    color: #AAAAAA !important;
    opacity: 1 !important;
}

.modal .form-control:focus {
    background: #FFFFFF !important;

    border-color: #17675F !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* Existing validation remains functional */
.modal .form-control.is-invalid {
    border-color: #D9534F !important;

    box-shadow:
        0 0 0 3px rgba(217, 83, 79, 0.06) !important;
}


/* =========================================================
   11. TEXTAREA
   ========================================================= */

.modal textarea.form-control {
    min-height: 96px !important;

    padding: 10px 12px !important;

    line-height: 1.5 !important;

    resize: vertical;
}


/* =========================================================
   12. SELECT
   ========================================================= */

.modal select.form-control {
    padding-right: 34px !important;

    cursor: pointer;
}


/* =========================================================
   13. FILE INPUT
   Upload logic untouched
   ========================================================= */

.modal input[type="file"] {
    width: 100% !important;

    min-height: 40px !important;

    padding: 5px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;

    color: #737373 !important;
}

.modal input[type="file"]::file-selector-button {
    height: 28px;

    margin-right: 10px;
    padding: 0 12px;

    background: #F5F5F5;

    border: 1px solid #E1E1E1;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 13px;
    font-weight: 500;

    color: #252525;

    cursor: pointer;
}

.modal input[type="file"]::-webkit-file-upload-button {
    height: 28px;

    margin-right: 10px;
    padding: 0 12px;

    background: #F5F5F5;

    border: 1px solid #E1E1E1;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 13px;
    font-weight: 500;

    color: #252525;

    cursor: pointer;
}


/* =========================================================
   14. RADIO / CHECKBOX
   ========================================================= */

.modal input[type="radio"],
.modal input[type="checkbox"] {
    accent-color: #17675F;
}


/* =========================================================
   15. SMALL / HELPER TEXT
   ========================================================= */

.modal small,
.modal .form-text,
.modal .text-muted {
    font-size: 11px !important;
    line-height: 1.45 !important;

    color: #777777 !important;
}


/* =========================================================
   16. ERROR / SUCCESS MESSAGES
   No show/hide behavior changed
   ========================================================= */

.modal .alert {
    margin: 12px 0 0 !important;
    padding: 10px 12px !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-size: 12px !important;
    line-height: 1.45 !important;
}

.modal .alert-success {
    background: rgba(23, 103, 95, 0.07) !important;

    border: 1px solid rgba(23, 103, 95, 0.18) !important;

    color: #17675F !important;
}

.modal .alert-danger {
    background: rgba(217, 83, 79, 0.07) !important;

    border: 1px solid rgba(217, 83, 79, 0.18) !important;

    color: #D9534F !important;
}


/* Existing custom message IDs */
.modal #delete_content_error,
.modal #course_content_success {
    margin-top: 14px !important;
    padding: 10px 12px !important;

    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    line-height: 1.45 !important;
    font-weight: 500 !important;

    text-align: center !important;
}

.modal #delete_content_error {
    background: rgba(217, 83, 79, 0.07) !important;

    border: 1px solid rgba(217, 83, 79, 0.18) !important;

    color: #D9534F !important;
}

.modal #course_content_success {
    background: rgba(23, 103, 95, 0.07) !important;

    border: 1px solid rgba(23, 103, 95, 0.18) !important;

    color: #17675F !important;
}


/* =========================================================
   17. FOOTER
   ========================================================= */

.modal .modal-footer {
    min-height: 68px !important;

    padding: 14px 20px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #EEEEEE !important;

    border-radius: 0 !important;
}


/* =========================================================
   18. BUTTON BASE
   ========================================================= */

.modal .btn {
    min-width: 96px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 16px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    line-height: 1 !important;
    font-weight: 500 !important;

    text-decoration: none !important;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease !important;
}


/* =========================================================
   19. PRIMARY / SAVE BUTTON
   ========================================================= */

.modal .btn-primary,
.modal .btn-success,
.modal .upload_btn {
    background: #17675F !important;

    border: 1px solid #17675F !important;

    color: #FFFFFF !important;
}

.modal .btn-primary:hover,
.modal .btn-primary:focus,
.modal .btn-success:hover,
.modal .btn-success:focus,
.modal .upload_btn:hover,
.modal .upload_btn:focus {
    background: #125A53 !important;

    border-color: #125A53 !important;

    color: #FFFFFF !important;

    transform: none !important;

    outline: 0 !important;
    box-shadow: none !important;
}


/* =========================================================
   20. SECONDARY / CANCEL
   ========================================================= */

.modal .btn-secondary,
.modal .btn-light {
    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;

    color: #252525 !important;
}

.modal .btn-secondary:hover,
.modal .btn-secondary:focus,
.modal .btn-light:hover,
.modal .btn-light:focus {
    background: #F7F9F8 !important;

    border-color: #17675F !important;

    color: #17675F !important;

    transform: none !important;

    outline: 0 !important;
    box-shadow: none !important;
}


/* =========================================================
   21. DANGER BUTTON
   ========================================================= */

.modal .btn-danger {
    background: #FFFFFF !important;

    border: 1px solid #D9534F !important;

    color: #D9534F !important;
}

.modal .btn-danger:hover,
.modal .btn-danger:focus {
    background: #D9534F !important;

    border-color: #D9534F !important;

    color: #FFFFFF !important;

    transform: none !important;
}


/* =========================================================
   22. DISABLED BUTTON
   Existing JS disabled state untouched
   ========================================================= */

.modal .btn:disabled,
.modal .btn.disabled {
    opacity: .55 !important;

    cursor: not-allowed !important;

    transform: none !important;
    box-shadow: none !important;
}


/* =========================================================
   23. PROGRESS BAR
   File upload progress logic untouched
   ========================================================= */

.modal .progress {
    height: 8px !important;

    margin-top: 10px !important;

    overflow: hidden !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border-radius: 50px !important;

    box-shadow: none !important;
}

.modal .progress-bar {
    background: #17675F !important;

    border-radius: 50px !important;

    box-shadow: none !important;
}


/* =========================================================
   24. DELETE POPUP
   Existing markup / onclick / IDs 100% same
   ========================================================= */

#deletepopup .modal-dialog {
    max-width: 460px !important;

    margin-top: 80px !important;
}

#deletepopup .modal-content {
    border-radius: 10px !important;
}

#deletepopup .modal-body {
    padding: 28px 24px 22px !important;
}

#deletepopup .modal-body h5 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    line-height: 1.55 !important;
    font-weight: 500 !important;

    color: #252525 !important;

    text-align: center !important;
}

#deletepopup .modal-footer {
    min-height: auto !important;

    padding: 0 24px 24px !important;

    justify-content: center !important;

    border-top: 0 !important;
}

#deletepopup #delpopup {
    min-width: 110px !important;
}


/* =========================================================
   25. BACKDROP
   Only opacity; Bootstrap behavior remains unchanged
   ========================================================= */

.modal-backdrop.show {
    opacity: .52 !important;
}


/* =========================================================
   26. MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .modal .modal-dialog {
        width: auto !important;
        max-width: calc(100% - 24px) !important;

        margin-top: 20px !important;
        margin-right: 12px !important;
        margin-bottom: 20px !important;
        margin-left: 12px !important;
    }

    .modal .modal-header {
        min-height: 58px !important;

        padding: 14px 16px !important;
    }

    .modal .modal-title,
    .modal .modal-header h3,
    .modal .modal-header h4,
    .modal .modal-header h5,
    .modal .modal-header h6 {
        font-size: 16px !important;
    }

    .modal .modal-body {
        padding: 20px 16px !important;
    }

    .modal .modal-footer {
        padding: 14px 16px 16px !important;
    }

    #deletepopup .modal-dialog {
        margin-top: 30px !important;
    }
}   
</style>


@include('grader/LectureScheduling.lecture_model')
<div class="page-body">
    {{-- Lecture Page Code STart  --}}
    <div class="row mx-auto justify-content-center align-items-center flex-column ">

        <div class="tab-content">
            <div class="tab-pane fade show active" id="home">
                <!-------------- tab content 1 --------------->

                <div class="container-fluid" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
                    <div class="row">
                        <?php
                     $date_sr_no = 0;
                     $status=0;
                     foreach($all_classes as $rec_all_classes):
                             $no_content_print = 0;
                             $class_status="";
                             if($rec_all_classes->attendance_marked>0 && $rec_all_classes->checkclass=="past"){
                             $class_status = "";
                             $status=0;
                             $bg_color = '#00CC99';
                             }elseif($rec_all_classes->attendance_marked==0 && $rec_all_classes->checkclass=="past"){
                             $bg_color = '#FF0';
                             $status=0;
                             $class_status = '<label style="color:red">Missed</label>';
                             }elseif($rec_all_classes->attendance_marked==0 && $rec_all_classes->checkclass=="coming"){
                             $bg_color = '#337AB7';
                             $status=0;
                             $class_status = "";
                             }
                             //------------------
                             $today_date            = date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));
                             date_default_timezone_set('Asia/karachi');
                             $curr_time             = date("His");
                             $curr_date             = date("d-M-Y", strtotime(substr($curr_time,0,11)));
                             $bod='custom_boffrder';
                             $card_col=2;
                             $bdr='2';
                             if($curr_date==$today_date){
                                $bod='custom_border';
                                $bg_color='#00CCFF';
                                $bdr='4';
                             }

                         ?>
                        <!-- Card Contents Start -->



                        <div class="col-md-12 col-xl-12" id="class_content{{$rec_all_classes->tt_id}}">
                            <div class="card">
                                <div class="card-block">
                                    <div class="card-header" style="height:70px">
                                        <div class="col-6 col-md-10 col-xl-10">
                                            <div style="font-size:12px; font-weight:bold;">
                                                <h5>Lecture <?php echo ($date_sr_no=$date_sr_no+1)." |".$class_status; ?></h5>
                                                <?php echo date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));?>
                                                {{$rec_all_classes->class_day_name}} {{$rec_all_classes->start_time}} To {{$rec_all_classes->end_time}}
                                            </div>
                                            <div style="font-size:10px; line-height:4px;"></div>
                                        </div>
                                        <div class="header-option class=" col-6 col-md-2 col-xl-2" style="float:right; font-size:10px;padding-top: 28px;">&nbsp;&nbsp;
                                            <?php
                             $today_date=date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));
                             date_default_timezone_set('Asia/karachi');
                             $curr_time =date("His");
                             $curr_date=date("d-M-Y", strtotime(substr($curr_time,0,11)));
                             if($curr_date==$today_date){
                             ?>
                                            <span class="badge badge-danger blink align-text-top">Today's Class</span>

                                            <?php } ?>
                                        </div>
                                    </div>









                                    <div class="card-body" style="padding-top:0% !important;padding-left:18px !important;padding-right:18px !important;">
                                        <p class="card-text overflow-scroll" id="class_content_p{{$rec_all_classes->tt_id}}" style="color:#000;">
                                            <?php
                         $content_count = 0;
                       // echo count($rec_all_classes->courseofferingcontent);exit();

                         if(count($rec_all_classes->courseofferingcontent)>0){
                            foreach($rec_all_classes->courseofferingcontent  as $row):
                            ?>
                                            <div class="container-fluid">

                                              
                                                <span style="color:<?=($row->coc_content_type==2) ? 'green':'' ?>;"><?=$row->coc_details?></span>   <i class="fa fa-edit" style="color: red" data-toggle="collapse" data-target="#content_<?=$row->coc_id?>"></i><br>
                                                <div id="content_<?=$row->coc_id?>" class="collapse">

                                                    <div style="width:80%;display:inline-block">
                                                        {{-- onkeyup="countChar(this,'<?=$row->coc_id?>')"  --}}
                                                        <input readonly row="4" value="<?=trim($row->coc_details);?>" id="check<?=$row->coc_id?>" class="form-control"></input>
                                                        <div id="content_success<?=$row->coc_id?>" style="display: none;color: green"><span>Updated Successfully</span></div>

                                                    </div>

                                                    <div style="display:inline-block">
                                                        {{-- <button  type="button" onclick="countChar(this,'<?=$row->coc_id?>')" id ="btn<?=$row->coc_id?>"  class="btn btn-info custom1"  > Save </button> --}}
                                                        <!-- <a href="#" data-toggle="modal" data-target="#deletepopup" onclick="deletecontentpopup('<?=$row->coc_id?>','<?=$row->coc_tt_id?>','<?=$row->coc_details?>')" style="cursor: pointer"><i class="fa fa-edit" style="color: red"></i></a> -->
                                                        <button data-toggle="modal" data-target="#deletepopup" onclick="deletecontentpopup('<?=$row->coc_id?>','<?=$row->coc_tt_id?>')" style="cursor: pointer" class="btn btn-secondary custom"> Delete </button>
                                                    </div>

                                                </div>

                                            </div>
                                            <!-- <a href="#" data-toggle="modal" data-target="#deletepopup" onclick="deletecontentpopup('<?=$row->coc_id?>','<?=$row->coc_tt_id?>','<?=$row->coc_details?>')" style="cursor: pointer"> -->


                                            </a>
                                            <?php
                            $content_count++;
                            endforeach;
                        }
                         $image_width="32px";
                         if($content_count==0){
                          ?>
                                            <h3 id="no_content_here" class="text-success"> No Content Here . . . </h3>
                                            <?php
                          }
                          ?>
                                        </p>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row">

                                            <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                                <div class="card-block" style="padding-top: 0px !important;">
                                                    <a <?php if($status==0){ ?> onclick="openLectureContentModel('<?=$rec_all_classes->tt_id;?>','<?=$rec_all_classes->mtt_co_id;?>')" href="javascript:void(0);" <?php }else{  ?> class="disabled-icon" style="opacity: 0.4" <?php }?>>
                                                        <img src="{{asset('images/lectureContent.png')}}" width="28px" title="Lecture Content"><br>Lecture Content
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                                <div class="card-block" style="padding-top: 0px !important;">
                                                    <a <?php  if($status==0){ ?> onclick="uploadFileShowModal('<?=$all_classes[0]->mtt_co_id;?>','<?=$date_sr_no?>','<?=$rec_all_classes->tt_id;?>')" style="cursor:pointer" <?php }else{  ?> class="disabled-icon" style="opacity: 0.4" <?php }?>>
                                                        <img src="{{asset('images/uploadFile.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload File"><br>Upload File
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                                <div class="card-block" style="padding-top: 0px !important;">
                                                    <a <?php if($status==0){ ?> href='/teacher/videouploaddrive/<?php echo $all_classes[0]->mtt_co_id;?>/<?=$date_sr_no?>/<?php echo $rec_all_classes->tt_id;?>' <?php }else{  ?> class="disabled-icon" style="opacity: 0.4" data-target="#exampleModalCenter22" <?php }?> onclick="get_co_id('<?php echo $all_classes[0]->mtt_co_id;?>','','<?php echo $rec_all_classes->tt_id;?>')">
                                                        <img src="{{asset('images/uploadVideo.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload Video"><br>Upload Video
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                                <div class="card-block" style="margin-top:-10px">
                                                    <a <?php if($status==0){ ?> onclick="get_co_id('<?=$all_classes[0]->mtt_co_id;?>','<?=$date_sr_no?>','<?=$rec_all_classes->tt_id;?>')" data-toggle="modal" data-target="#exampleModalCenter5" style="cursor:pointer" <?php }else{  ?> class="disabled-icon" style="opacity: 0.4" <?php }?>>
                                                        <img src="{{asset('images/cdrive.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload Assignment"><br><span>Cloud Drive <span>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                                <div class="card-block" style="padding-top: 0px !important;">
                                                    <a <?php if($status==0){ ?> data-toggle="modal" data-target="#exampleModalCenter3" style="cursor:pointer" <?php }else{  ?> class="disabled-icon" style="opacity: 0.4" <?php }?> />
                                                    <img src="{{asset('images/uploadAssignment.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload Assignment"><br> <span style="">Upload Assignment</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Card Contents End -->
                        <?php  endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- <a href="javascript:void(0);" onclick="openAssignmentModal(0,0);">Test Assignment</a> -->

    {{-- Lecture Page Code End --}}
</div>

<div class="modal fade" id="deletepopup" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="color: white !important;">

                <h4 class="modal-title" style="text-align: left;">Delete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update_content_detail" name="update_content_detail" enctype="multipart/form-data" method="post">
                    @csrf
                    <input type="hidden" id="coc_id">
                    <input type="hidden" id="coc_tt_id">
                    <h5>Are You Sure Want to Delete it..! </h5>
                    <!-- <input type="text" class="form-control assess-form-input" id="updtleccont" name="updtleccont"  placeholder="Title Here . . ." maxlength="300"> -->
                </form>
                <div id="delete_content_error" style="display: none;color: red"><span>Content Deleted Successfully</span></div>
                <div id="course_content_success" style="display: none;color: green"><span>Content Update Successfully</span></div>
                {{--Are you Sure!!! You want to delete this Content?--}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="delpopup" onclick="deletecontent('123')">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php } ?>

@stop


@section('javascript')

<script type="text/javascript">
    $(document).ready(function() {
        window.location.hash = '#class_content' + '<?=$tt_id;?>';
        var tt_id = '<?=$tt_id;?>';
        if (tt_id) {
            $('#exampleModalCenter3').modal('show');
        }
        //$("#lect281158").focus();
    });

    ///////////////////////////

    function countChar(val, id) {

        // len; // Maximum word length
        //var words = val.value.match(/\S+/g).length;
        // var len = val.value.length;
        var text = $('#check' + id).val();
        //    alert(text);
        //    return false;
        //     var text=val.value;
        var updatetext = text.trim();
        console.log(updatetext)
        var formData = new FormData();
        formData.append("id", id);
        formData.append("text", updatetext);
        $.ajax({
            type: 'POST'
            , url: '{{url('updtcontent')}}/' + id
            , data: formData
            , processData: false
            , contentType: false
            , success: function(data) {
                // alert(data);
                if (data == 1) {
                    $("#content_success" + id).show();
                    setTimeout(function() {
                        $("#content_success" + id).hide();
                        $('#deletepopup').modal('hide');
                    }, 1800);

                }
                //location.reload();
            }
            , complete: function() {}
        });

    }



    function ContentUpdate() {
        var coc_id = $("#cc").val();

        //alert(coc_id);
        var bi_form = document.querySelector('#content_form');
        var fd = new FormData(bi_form);
        $.ajax({
            type: 'POST'
            , url: '{{url('insertlecturecontent')}}/' + coc_id
            , data: fd
            , processData: false
            , contentType: false
            , success: function(data) {
                //alert(data);
                if (data == 1) {
                    $("#course_content_success").show();
                    setTimeout(function() {
                        $("#course_content_success").hide();
                        $('#ContentModal').modal('hide');
                        $('#deletepopup').modal('hide');
                    }, 1800);

                }
                //     location.reload();
            }
            , complete: function() {}
        });

    }

    function lecturecontent(lec_id, id, type, content_type) {

        var get_desc = $('#desc' + id).val()
        var get_content = $('#content' + id + type).val();
        $('#getcontent').val(get_content);
        $('#desc').val(get_desc);
        $('#cc').val($('#tt_id').val());
        $('#cc_id').val(id);
        $('#coc_content_type').val(content_type);
    }

    function isFloat(n) {
        return n === +n && n !== (n | 0);
    }

    function isInteger(n) {
        return n === +n && n === (n | 0);
    }

    function get_tt_id(tt_id, mtt_co_id) {
        $('#tt_id').val(tt_id);
        $('#mtt_co_id').val(mtt_co_id);

    }

    function openLectureContentModel(tt_id, mtt_co_id) {
        $("#tt_new_content_id").val(tt_id);
        $(".mtt_co_id").val(mtt_co_id);
        $("#tt_id").val(tt_id);
        $.courseContentRawModal(mtt_co_id, tt_id);
    }


    function addnewcontent() {

        var tt_id = $("#tt_id").val();
        $("#tt_new_content_id").val(tt_id);
        $('#AddNewContentModal').modal('show');
    }

    function deletecontentpopup(coc_id, tt_id) {

        $("#coc_id").val(coc_id);
        $("#coc_tt_id").val(tt_id);
        //$("#updtleccont").val(coc_details);
    }

    function resetupload() {
        $("#uploadfile2").val('');
    }

    function deletecontent(coc_id) {
        var coc_id = $("#coc_id").val();
        var coc_tt_id = $("#coc_tt_id").val();
        $.ajax({
            method: "GET"
            , url: "<?php echo url('teacher/delete_course_content/'); ?>/" + coc_id + "/" + coc_tt_id
        , }).done(function(res) {
            if (res == 1) {
                $("#delete_content_error").show()
                setTimeout(function() {

                    $("#delete_content_error").hide()
                    $('#deletepopup').modal('hide');
                }, 1800);
            }
            location.reload();
        });
    }

    ///////////////////////////////

    $("#btn2").click(function(e) {

        e.preventDefault();

        $('#error_assignment span').html('');
        var latesub = $('input[name=latesub]:checked').val();
        var resub = $('input[name=resub]:checked').val();
        var bi_form = document.querySelector('#request_form');
        var fd = new FormData(bi_form);

        var marks = $("#marks").val();
        var title = $("#title2").val();
        var assessment = $("#assessment_id").val();
        var file = $("#uploadfile2").val();
        var dueDate = $("#duedate").val();
        var description = $("#description").val();
        var typeArr = ["xlsx", "docx", "pptx", "jpg", "jpeg", "pdf", "png", "zip"];


        // myInp.files[0].size
        //alert('ddd');
        // return false;
        $('.msg-div').hide();
        $('.form-control').removeClass('is-invalid');
        var error = 0;

        // if(assessment.trim() == ''){
        //     error++;
        //     $('#assessment_id').addClass('is-invalid');
        //     $('#error_msg span').html('Please select assessment first!');
        // }else if(description.trim() == ''){
        //     error++;
        //     $('#description').addClass('is-invalid');
        //     $('#error_msg span').html('Please provide description!');
        // }
        // if(file.trim() == ''){
        //         error++;
        //         $('#uploadfile2').addClass('is-invalid');
        //         $('#error_msg span').html('Please provide description!');
        //     }
        if (file.trim() != '') {
            var fileInfoArr = file.split(".");
            var len = fileInfoArr.length;
            var type = fileInfoArr[len - 1];
            var index = typeArr.indexOf(type);

            if (index < 0) {
                error++;
                $('#uploadfile2').addClass('is-invalid');
                $('#error_msg span').html('Only xlsx, docx, jpg, jpeg, png and pdf are allow !');
            }
            var fileInput = document.getElementById("uploadfile2");
            var file_size = 0;
            try {
                file_size = fileInput.files[0].size;
            } catch (e) {
                var objFSO = new ActiveXObject("Scripting.FileSystemObject");
                var e = objFSO.getFile(fileInput.value);
                var fileSize = e.size;
                // file_size = fileSize/1024;
            }
            file_size = (file_size / 1024) / 1024;

            if (file_size > 201) {
                error++;
                $('#uploadfile2').addClass('is-invalid');
                $('#error_msg span').html('File size must be less then 200MB!');
            }
        }
        //  $("#btn2").attr("disabled", true);


        if ($('#detail').hasClass('show')) { // #rule if user click on detail then he must provide the whole detail



            $('#is_file').val('yes');
        } else {
            $('#is_file').val('no');
        }

        if (error > 0) {
            $('#error_msg').show();
            return false;
        }
        var bar = $('.bar');
        var percent = $('.percent');
        // save_btn_action('loading');
        $("#btn2").prop("disabled", true);
        $("#btn2").html('Loading');
        $.ajax({
            type: 'POST'
            , url: "{{url('uploadassessment?fcu_id=')}}" + $("#fcu_id").val()
            , data: fd
            , processData: false
            , contentType: false,

            success: function(data) {
                $("#btn2").html('Save Change');
                $("#btn2").attr("disabled", false);
                if (data.status) {
                    $('#request_form')[0].reset();
                    showAlert('.assignment_info', data.message, 'success');
                    location.reload();
                } else {
                    showAlert('.assignment_info', data.message, 'danger');
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                $("#btn2").html('Save Change');
                $("#btn2").attr("disabled", false);
                defaultFailBlock('.assignment_info', jqXHR, textStatus, errorThrown);
                $("#btn2").prop("disabled", false);
            }
        });

    });

    ///////////////////////////////

    function uploadfile_google() {

        $('#post_error').html('');
        $('#error_msg span').html(' ');
        $("#error_msg_div").html(" ");
        $('#uploadStatus').html('');
        var title = $("#file_title").val();
        var file = $("#uploadfile").val();

        $('.form-control').removeClass('is-invalid');
        var error = 0;

        if (title.trim() == '') {
            error++;
            $('#file_title').addClass('is-invalid');
            $('#error_msg span').html('Please provide some title!');
            return false;
        }
        if (file.trim() == '') {
            error++;
            $('#uploadfile').addClass('is-invalid');
            $('#error_msg span').html('Please Select file!');
            return false;
        }

        ////////////// file size check ////////////////////////////
        $(".error_msg_div").css("border-color", "#F0F0F0");
        var file_size = $('#uploadfile')[0].files[0].size;
        // return false;524288000 /// 1048576000
        if (file_size > 2097152000) {
            error++;
            $("#error_msg_div").html("File size is greater than 2GB");
            $("#uploadfile").css("border-color", "#FF0000");
            return false;
        }

        if (error > 0) {
            $('#error_msg').show();
            return false;
        }
        $('#file_title').addClass('is-valid');
        $('#uploadfile').addClass('is-valid');
        $("#spin").addClass("fa fa-spinner fa-spin p-101");
        $('#file_up').prop('disabled', true);
        $('#loading').show();
        var bi_form = document.querySelector('#file_form_upload');
        var fd = new FormData(bi_form);
        $.ajax({
            type: 'POST'
            , url: '<?=url('api/lecture-content/fileupload_google ');?>'
            , data: fd
            , headers: {
                Authorization: 'Bearer <?=auth()->user()->api_token?>'
                , Accept: 'application/json'
            }
            , contentType: 'multipart/form-data'
            , processData: false
            , contentType: false
            , beforeSend: function() {
                $(".progress-bar").width('0%');
                //$('#uploadStatus').html('<img src="images/loading.gif"/>');
            }
            , xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var re_file = parseInt(25655555 + evt.loaded);
                        var to_file = parseInt(25655555 + evt.total)
                        console.log(evt.loaded + '--' + evt.total + '//' + re_file + '==' + to_file);
                        var percentComplete = ((re_file / to_file) * 100);
                        var percent = percentComplete.toFixed(2);
                        $(".progress-bar").width(percent + '%');
                        $(".progress-bar").html(percent + '%');
                    }
                }, false);
                return xhr;
            }
            , error: function() {
                $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
            }
            , success: function(res) {
                // alert(res.message);
                if (res.status) {
                    // $('#filepreview').show();
                    //$('#filepreview img,#filepreview a').hide();
                    if (res.data.extension == 'jpg' || res.data.extension == 'jpeg' || res.data.extension == 'png') {
                        $('#filepreview img').attr('src', res.data.filepath);
                        $('#filepreview a').attr('href', res.data.filepath);
                        $('#filepreview').show();
                    } else {
                        //$('#filepreview img').attr('src','{{asset('all.png')}}');
                        $('#filepreview a').attr('href', res.data.filepath);
                        $('#filepreview').show();
                    }
                    $('#loading').hide();
                    $('#file_form_upload')[0].reset();
                    $("#file_title").val('');
                    $("#uploadfile").val('');
                    $("#file_upload").show();
                    $("#file_up").hide();
                    $('#uploadStatus').addClass("alert-success");
                    $('#uploadStatus').html('<p style="color:black;">File Uploaded Successfully!</p>');
                } else {
                    // $('#uploadStatus').html(data.message);
                    var snd = new Audio(url + '/assets/sounds/notify.mp3');
                    snd.play();
                    $('#uploadStatus').addClass("alert-danger");
                    $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed,' + res.message + '</p>');
                }
                setTimeout(function() {
                    // location.reload();
                    //$('#exampleModalCenter5').modal('hide');
                }, 4000);

            }
            , error: function(jqXHR, exception, errorThrown) {
                var snd = new Audio(url + '/assets/sounds/notify.mp3');
                snd.play();
                var msg = '';
                $("#cancel").attr("disabled", false);
                $('#loading').hide();
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Server Error '; //jqXHR.responseJSON.message;
                    //$('#post_error').html(jqXHR.message);
                } else if (jqXHR.status == 403) {
                    msg = '403 Forbidden, file security issue ';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else if (jqXHR.status === 401) {
                    msg = jqXHR.responseJSON.message;
                } else if (jqXHR.status === 413) {
                    msg = 'PlayLoad To Large for Permission.';
                } else if (jqXHR.status == 504) {
                    msg = jqXHR.responseText;
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseJSON.message;

                }
                $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.' + msg + '</p>');
            }


        });
    }

    function get_co_id(co_id, lec, mtt_id) {
        //(co_id+lec+mtt_id);
        $('#course_id').val(co_id);
        $('#file_course_id').val(co_id);
        $('#file_lec_no').val(lec);
        $('#file_mtt_id').val(mtt_id);
        $('#video_mtt_id').val(mtt_id);
    }


    function uploadFileShowModal(co_id, lec, mtt_id) {
        $('#course_id').val(co_id);
        $('#file_course_id').val(co_id);
        $('#file_lec_no').val(lec);
        $('#file_mtt_id').val(mtt_id);
        $('#video_mtt_id').val(mtt_id);



        var hiddenFieldsArr = [{
                'key': 'co_id'
                , 'val': co_id
            }
            , {
                'key': 'course_id'
                , 'val': co_id
            }
            , {
                'key': 'file_course_id'
                , 'val': co_id
            }
            , {
                'key': 'file_lec_no'
                , 'val': lec
            }
            , {
                'key': 'file_mtt_id'
                , 'val': mtt_id
            }
            , {
                'key': 'video_mtt_id'
                , 'val': mtt_id
            }
        ];

        $.fileUploadPopup({
            hiddenFields: hiddenFieldsArr
        });
    }

    function cancell_ass() {
        location.reload();

    }

    function save_btn_action(action) {
        //   alert(action);
        if (action == 'loading') {
            $(".upload_btn").prop('disabled', true);
            // $(".upload_btn").text('Save');
            $(".upload_btn").text('Loading');
        } else {
            $(".upload_btn").prop('disabled', false);
            //  $(".upload_btn .save").show();
            $(".upload_btn").text('Loading');
        }
    }

</script>


@stop
