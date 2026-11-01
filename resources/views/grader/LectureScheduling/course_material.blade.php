@extends('layouts.app',['pageTitle' => 'Course Material'])
@section('content')

{{-- @dd("!23"); --}}
<style>
  /* .nav-tabs .nav-link.active {
    background-color: steelblue !important;
  } */


   /* ==========================
   Course Material Section
========================== */

.page-body .tab-content{
    width:100% !important;
}

.page-body .tab-pane{
    width:100%;
}

.page-body .container-fluid.card{
    padding:0 !important;
    margin:0 auto !important;
    border:0 !important;
    border-radius:12px;
    background:#ffffff;
    box-shadow:0 3px 16px rgba(0,0,0,.05);
    overflow:hidden;
}

/* Header */
.page-body .container-fluid.card > .card-header{
    display:flex;
    align-items:center;
    min-height:64px;
    padding:0 22px !important;
    border:0 !important;
    border-bottom:1px solid #E8ECEF !important;
    border-radius:0 !important;
    background:#17675F !important;
    color:#ffffff !important;
}

.page-body .container-fluid.card > .card-header h3{
    margin:0 !important;
    float:none !important;
    font-family:inherit !important;
    font-size:20px;
    font-weight:600;
    line-height:1.3;
    color:#ffffff;
}

/* Table */
.page-body .table{
    width:100%;
    margin:0;
    border-collapse:separate;
    border-spacing:0;
    border:0 !important;
    background:#ffffff;
}

.page-body .table thead th{
    padding:15px 18px !important;
    border-top:0 !important;
    border-right:0 !important;
    border-left:0 !important;
    border-bottom:1px solid #DDE5E8 !important;
    background:#17675F1A !important;
    color:#17675F !important;
    font-size:14px;
    font-weight:600;
    text-align:left;
    vertical-align:middle;
    white-space:nowrap;
}

.page-body .table tbody th,
.page-body .table tbody td{
    padding:15px 18px !important;
    border-top:0 !important;
    border-right:0 !important;
    border-left:0 !important;
    border-bottom:1px solid #EDF1F3 !important;
    color:#3F4650;
    font-size:14px;
    font-weight:400;
    vertical-align:middle;
}

.page-body .table tbody th{
    width:70px;
    color:#17675F;
    font-weight:600;
}

.page-body .table-striped tbody tr:nth-of-type(odd){
    background:#FBFCFC;
}

.page-body .table tbody tr{
    transition:background-color .2s ease;
}

.page-body .table tbody tr:hover{
    background:#F3F8F7 !important;
}

/* File action icons */
.page-body .table tbody td a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:42px;
    height:42px;
    border-radius:8px;
    background:#F4F8F7;
    text-decoration:none;
    transition:.2s ease;
}

.page-body .table tbody td a:hover{
    background:#E7F2F0;
    transform:translateY(-1px);
}

.page-body .table tbody td a i{
    font-size:24px !important;
}

/* Delete button */
.page-body .table tbody td button{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:42px;
    height:42px;
    padding:0 !important;
    border:0 !important;
    border-radius:8px;
    background:#FFF1F0 !important;
    cursor:pointer;
    transition:.2s ease;
}

.page-body .table tbody td button:hover{
    background:#FFE1DE !important;
    transform:translateY(-1px);
}

.page-body .table tbody td button i{
    color:#E55353 !important;
    font-size:22px !important;
}

/* Empty state */
.page-body .table tbody td[colspan]{
    padding:36px 20px !important;
    color:#8A949E;
    font-size:15px;
    font-weight:500;
    text-align:center;
    background:#ffffff;
}

/* Responsive table */
@media(max-width:767px){

    .page-body .container-fluid.card{
        overflow-x:auto;
    }

    .page-body .table{
        min-width:760px;
    }

    .page-body .container-fluid.card > .card-header{
        min-width:760px;
        padding:0 16px !important;
    }

    .page-body .table thead th,
    .page-body .table tbody th,
    .page-body .table tbody td{
        padding:13px 14px !important;
    }
}



























/* =========================================================
   COURSE MATERIAL / STUDY MATERIALS
   FIGMA STYLE OVERRIDE ONLY
   No HTML / PHP / JS changes required
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. TYPOGRAPHY
   ========================================================= */

.page-body,
.container-fluid > .card-header {
    font-family: 'Montserrat', sans-serif;
}


/* Course Name - Figma secondary title */
.container-fluid > .card-header > .card-title {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.3 !important;

    color: #252525 !important;

    float: none !important;
}


/* First heading wrapper */
.container-fluid > .card-header:first-of-type {
    min-height: auto !important;

    padding: 0 0 18px 0 !important;

    background: transparent !important;

    border: 0 !important;
}


/* =========================================================
   2. LECTURE / STUDY MATERIAL / ASSIGNMENT TABS
   Figma Lecture Schedule tabs
   ========================================================= */

.container-fluid > .card-header:nth-of-type(2) {
    min-height: auto !important;

    margin: 0 !important;
    padding: 0 0 15px 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-bottom: 2px solid rgba(23, 103, 95, 0.10) !important;

    border-radius: 0 !important;

    box-shadow: none !important;
}


/* Nav wrapper */
.container-fluid > .card-header:nth-of-type(2) .nav,
.container-fluid > .card-header:nth-of-type(2) .nav-tabs,
.container-fluid > .card-header:nth-of-type(2) .nav-pills {
    display: flex !important;
    align-items: center !important;

    gap: 8px !important;

    margin: 0 !important;
    padding: 0 !important;

    border: 0 !important;

    background: transparent !important;
}


/* Each nav item */
.container-fluid > .card-header:nth-of-type(2) .nav-item {
    margin: 0 !important;
}


/* Figma tab pills */
.container-fluid > .card-header:nth-of-type(2) .nav-link {
    position: relative !important;

    height: 50px !important;

    padding: 0 26px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border: 0 !important;
    border-radius: 50px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: rgba(23, 103, 95, 0.50) !important;

    text-decoration: none !important;
}


/* Figma widths */
.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(1) .nav-link {
    min-width: 154px !important;
}

.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(2) .nav-link {
    min-width: 208px !important;
}

.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(3) .nav-link {
    min-width: 195px !important;
}


/* Active Study Materials tab */
.container-fluid > .card-header:nth-of-type(2) .nav-link.active {
    background: #1F776E !important;

    color: #FFFFFF !important;
}


/* Active green underline */
.container-fluid > .card-header:nth-of-type(2) .nav-link.active::after {
    content: "";

    position: absolute;

    left: 0;
    right: 0;

    bottom: -17px;

    height: 4px;

    background: #17675F;

    border-radius: 4px;
}


/* Hover */
.container-fluid > .card-header:nth-of-type(2)
.nav-link:hover:not(.active) {
    background: rgba(23, 103, 95, 0.14) !important;

    color: #17675F !important;
}


/* =========================================================
   3. MAIN STUDY MATERIAL AREA
   ========================================================= */

.page-body {
    width: 100% !important;

    margin-top: 20px !important;

    background: transparent !important;
}


.page-body > .row {
    width: 100% !important;

    margin: 0 !important;

    display: block !important;
}


.page-body .tab-content,
.page-body .tab-pane {
    width: 100% !important;
}


.page-body .tab-pane > .container-fluid {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* =========================================================
   4. COURSE MATERIAL CARD
   ========================================================= */

.page-body .container-fluid.card {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


/* =========================================================
   5. "COURSE MATERIAL" SECTION TITLE
   ========================================================= */

.page-body .container-fluid.card > .card-header {
    min-height: 56px !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    border-radius: 0 !important;

    color: #111827 !important;
}


/* Override inline Times New Roman */
.page-body .container-fluid.card > .card-header h3 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    color: #111827 !important;

    float: none !important;
}


/* =========================================================
   6. TABLE
   Figma table: green header + white 64px rows
   ========================================================= */

.page-body .table {
    width: 100% !important;

    margin: 0 !important;

    border: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    background: #FFFFFF !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* Remove Bootstrap striped treatment */
.page-body .table-striped tbody tr,
.page-body .table-striped tbody tr:nth-of-type(odd),
.page-body .table-striped tbody tr:nth-of-type(even) {
    background: #FFFFFF !important;
}


/* =========================================================
   7. TABLE HEADER
   Exact Figma visual language
   ========================================================= */

.page-body .table thead tr {
    height: 42px !important;

    background: #17675F !important;
}


.page-body .table thead th {
    height: 42px !important;

    padding: 8px 14px !important;

    vertical-align: middle !important;

    background: #17675F !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #FFFFFF !important;

    text-align: left !important;

    white-space: nowrap;
}


/* First / last table corner radius */
.page-body .table thead th:first-child {
    border-top-left-radius: 0 !important;
}

.page-body .table thead th:last-child {
    border-top-right-radius: 0 !important;
}


/* =========================================================
   8. TABLE BODY
   ========================================================= */

.page-body .table tbody tr {
    height: 64px !important;

    background: #FFFFFF !important;

    transition: background-color .15s ease;
}


.page-body .table tbody th,
.page-body .table tbody td {
    height: 64px !important;

    padding: 12px 14px !important;

    vertical-align: middle !important;

    background: transparent !important;

    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #334155 !important;
}


/* Sr # */
.page-body .table tbody th {
    width: 70px !important;

    color: #1E293B !important;

    font-weight: 400 !important;
}


/* Last row */
.page-body .table tbody tr:last-child th,
.page-body .table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* Subtle hover */
.page-body .table tbody tr:hover {
    background: #F9FAFB !important;
}


/* =========================================================
   9. COLUMN SIZING
   ========================================================= */

.page-body .table thead th:nth-child(1),
.page-body .table tbody th {
    width: 7%;
}


.page-body .table thead th:nth-child(2),
.page-body .table tbody td:nth-child(2) {
    width: 14%;
}


.page-body .table thead th:nth-child(3),
.page-body .table tbody td:nth-child(3) {
    width: 36%;
}


.page-body .table thead th:nth-child(4),
.page-body .table tbody td:nth-child(4) {
    width: 25%;
}


.page-body .table thead th:nth-child(5),
.page-body .table tbody td:nth-child(5) {
    width: 18%;
}


/* =========================================================
   10. ACTION COLUMN
   ========================================================= */

.page-body .table tbody td:last-child {
    white-space: nowrap;
}


/* Download / Open file button */
.page-body .table tbody td a {
    width: 36px !important;
    height: 36px !important;

    margin-right: 8px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    vertical-align: middle !important;

    background: rgba(23, 103, 95, 0.07) !important;

    border: 1px solid rgba(23, 103, 95, 0.10) !important;
    border-radius: 8px !important;

    text-decoration: none !important;

    transition:
        background-color .15s ease,
        border-color .15s ease !important;
}


.page-body .table tbody td a:hover {
    background: rgba(23, 103, 95, 0.13) !important;

    border-color: rgba(23, 103, 95, 0.22) !important;

    transform: none !important;
}


/* File icon */
.page-body .table tbody td a i {
    font-size: 18px !important;

    line-height: 1 !important;

    color: #17675F !important;
}


/* =========================================================
   11. DELETE ACTION
   ========================================================= */

.page-body .table tbody td button {
    width: 36px !important;
    height: 36px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    vertical-align: middle !important;

    background: #FEF2F2 !important;

    border: 1px solid #FEE2E2 !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    cursor: pointer !important;

    transition:
        background-color .15s ease,
        border-color .15s ease !important;
}


.page-body .table tbody td button:hover {
    background: #FEE2E2 !important;

    border-color: #FECACA !important;

    transform: none !important;
}


.page-body .table tbody td button i {
    margin: 0 !important;

    font-size: 17px !important;

    line-height: 1 !important;

    color: #DC2626 !important;
}


/* =========================================================
   12. EMPTY STATE
   ========================================================= */

.page-body .table tbody td[colspan] {
    height: auto !important;

    padding: 40px 20px !important;

    background: #FFFFFF !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #6B7280 !important;

    text-align: center !important;
}


/* =========================================================
   13. DELETE MODAL
   ========================================================= */

#myModal .modal-dialog {
    max-width: 460px !important;

    margin: 80px auto !important;
}


#myModal .modal-content {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: 0 16px 40px rgba(0, 0, 0, .14) !important;
}


/* Modal Header */
#myModal .modal-header {
    min-height: 60px !important;

    padding: 16px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;
}


#myModal .modal-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    color: #111827 !important;
}


/* Modal X */
#myModal .close {
    width: 32px !important;
    height: 32px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #F9FAFB !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 50% !important;

    color: #6B7280 !important;

    font-size: 22px !important;

    opacity: 1 !important;
}


/* Modal Body */
#myModal .modal-body {
    padding: 28px 20px !important;

    background: #FFFFFF !important;
}


#myModal .modal-body p {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 15px !important;
    line-height: 1.6 !important;
    font-weight: 400 !important;

    color: #334155 !important;
}


/* Modal Footer */
#myModal .modal-footer {
    padding: 16px 20px 20px !important;

    display: flex !important;
    justify-content: flex-end !important;

    gap: 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;
}


/* Both buttons */
#myModal .modal-footer .btn {
    min-width: 84px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 18px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
}


/* NO */
#myModal .modal-footer .btn:first-child {
    background: #FFFFFF !important;

    border: 1px solid #D1D5DB !important;

    color: #374151 !important;
}


/* YES */
#myModal .modal-footer .btn:last-child {
    background: #17675F !important;

    border: 1px solid #17675F !important;

    color: #FFFFFF !important;
}


#myModal .modal-footer .btn:last-child:hover {
    background: #125A53 !important;

    border-color: #125A53 !important;
}


/* =========================================================
   14. RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .container-fluid > .card-header:nth-of-type(2)
    .nav-link {
        min-width: 0 !important;

        padding-left: 18px !important;
        padding-right: 18px !important;
    }

}


@media (max-width: 767px) {

    .container-fluid > .card-header:nth-of-type(2)
    .nav,
    .container-fluid > .card-header:nth-of-type(2)
    .nav-tabs,
    .container-fluid > .card-header:nth-of-type(2)
    .nav-pills {
        display: grid !important;

        grid-template-columns: 1fr !important;

        gap: 8px !important;
    }


    .container-fluid > .card-header:nth-of-type(2)
    .nav-item,

    .container-fluid > .card-header:nth-of-type(2)
    .nav-link {
        width: 100% !important;
        min-width: 100% !important;
    }


    .container-fluid > .card-header:nth-of-type(2)
    .nav-link.active::after {
        display: none !important;
    }


    /* Horizontal table scroll */
    .page-body .container-fluid.card {
        overflow-x: auto !important;
    }


    .page-body .table {
        min-width: 780px !important;
    }


    .page-body .container-fluid.card > .card-header {
        min-width: 780px !important;
    }

}
</style>
<?php 
// @dd("123");
//print_r($course_handouts);
?>
      <div class="container-fluid" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
  <div class="card-header" style="padding-left: 0 !important;">
    <h3 class="card-title" style="padding-left: 0 !important;">{{$course_name}} </h3>
  </div>

  <div class="card-header" style="border-radius:10px;padding-left:0px;padding-right:0px;">
  @include('grader/LectureScheduling.lecture_scheduling_header', ['active_tab_name' => 'Study_Material'])
  </div>
  
</div>

<div class="page-body">
  <div class="row mx-auto justify-content-center align-items-center flex-column">


  <div class="tab-content" style="width:100% !important">
      <div class="tab-pane fade show active" id="profile">


        <!--  Lecture Page Code STart  -->

        <div class="container-fluid" style="padding-left:0px; padding-right:0px;">
        <div class=" container-fluid card" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%; ">
        <div class="card-header" style="background: #f2f6f9 !important;padding-left: 0 !important;">
              <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left">Course Material</h3>
            </div>
            <table class="table table-striped table-bordered">
              <thead>
                <tr style="background-color:#;font-weight:600; color:#FFF; padding:6px;">
                  <th scope="col">Sr #</th>
                  <th scope="col">Lecture No</th>
                  <th scope="col">Title</th>
                  <th scope="col">Entry Date / Time</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php

                $file_sr = 1;
                $allowed = "";
                $color = "";
                $is_video = "";
                if(count($course_handouts)>0){
                foreach ($course_handouts as $rec_course_handouts) {
                  $file_name = $rec_course_handouts->file_original_name;
                  $its_link = explode("=", $is_video);
                  if ($rec_course_handouts->type == '442') {


                    $get_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    if ($get_ext == 'docx' or $get_ext == 'doc') {
                      $allowed = "fa fa-file-word-o";
                      $color = 'blue';
                    } else if ($get_ext == 'csv' or $get_ext == 'xlsx' or $get_ext == 'xlsm' or $get_ext == 'xls') {
                      $allowed = "fa fa-file-excel-o";
                      $color = 'green';
                    } else if ($get_ext == 'pptx' or $get_ext == 'PPT' or $get_ext == 'XPS') {
                      $allowed = "fa fa-file-powerpoint-o";
                      $color = '#ff6c43';
                    } else if ($get_ext == 'pdf') {
                      $allowed = "fa fa-file-pdf-o";
                      $color = '#ff6c43';
                    } else if ($get_ext == 'mp4' or $get_ext == 'mkv' or $get_ext == '3gp') {
                      $allowed = "fa fa-youtube-play";
                      $color = '#ff0000';
                    }else if($get_ext == 'mp3'){
                      $allowed = "fa fa-file-audio-o";
                      $color = '#ff6c43';
                      
                    }else{
                      $allowed = " fa fa-file-image-o";
                      $color = '#ff6c43';
                    }
                ?>
                    <tr>
                      <th scope="row">{{$file_sr}} </th>
                      <td>{{$rec_course_handouts->lecture_no}}</td>
                      <td> {{$rec_course_handouts->title}}</td>
                      <td>{{$rec_course_handouts->entry_date_time}}</td>
                      <td> <a href="{{ url('docs/'.$file_name) }}"  > <i class="<?= $allowed; ?>" style="color:<?= $color; ?>;  font-size:65px;"></i></a><button style="border: 0px solid; background-color: transparent;" data-toggle="modal" data-target="#myModal" onclick="setval(<?= $rec_course_handouts->id ?>)"> <i style="color:coral;  font-size:45px;" class="fa fa-trash"></i></button></td>
                    </tr>
                  <?php     } else if($rec_course_handouts->type == '445'){ ?>

                    <tr>
                      <th scope="row">{{$file_sr}} </th>
                      <td>{{$rec_course_handouts->lecture_no}}</td>
                      <td> {{$rec_course_handouts->title}}</td>
                      <td>{{$rec_course_handouts->entry_date_time}}</td>
                      <td> <a href="<?=$rec_course_handouts->google_drive;?>" > <i class="fa fa-cloud-download" style="color:steelblue;  font-size:45px;"></i></a><button style="border: 0px solid; background-color: transparent;" data-toggle="modal" data-target="#myModal" onclick="setval(<?= $rec_course_handouts->id ?>)"> <i style="color:coral;  font-size:45px;" class="fa fa-trash"></i></button></td>
                    </tr>




                    <?php }
                  $file_sr++;
                }
              }else{ ?>

                <tr><td colspan="5">No Material Uploded</td> </tr>
            <?php }
                ?>

              </tbody>
            </table>

          </div>
          <!---  Lecture Page Code End --->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal start-->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">

        <h4 align="left" class="modal-title">Delete Material</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Are you sure want to Delete Material !.</p>
        <input type="hidden" id="dele" name="dele" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" onclick="dElete()" class="btn btn-default" data-dismiss="modal">Yes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal end -->

@stop

@section('javascript')
<script type="text/javascript">
  function setval(id) {
    $('#dele').val(id);
  }

  function dElete() {
    var id = $('#dele').val();
    $.ajax({
      method: "GET",
      url: "<?php echo url('uploads/delete_faculity_course_material/'); ?>/" + id,

    }).done(function(res) {
      // console.log(res);
      if (res.status) {
        alert(res.message);
        location.reload();
      }

    }).fail(function(res) {
      console.log(res);
    });



  }
</script>
@stop