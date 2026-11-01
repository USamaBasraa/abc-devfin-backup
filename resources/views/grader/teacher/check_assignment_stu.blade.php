<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// print_r($show_student_data);
// exit;

?>
@extends('layouts.app',['pageTitle' => ' '])
{{-- @dd("!23"); --}}
@section('content')
    <style>
        /* a.disabled {
            pointer-events: none;
            cursor: default;
        } */

		.ellipses {
        white-space: nowrap;
        width: 5em;
        overflow: hidden;
        font-size: 14px;
        text-overflow: ellipsis;

    }
	.blink {
            animation: blinker 0.9s linear infinite;
            color: #1c87c9;
            //font-size: 30px;
            font-weight: bold;
            font-family: sans-serif;
        }
        @keyframes blinker {
            50% { opacity: 0; }
        }




        /* Course title and assignment summary header */
.row:has(.profile-overview-title) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    width: 100%;
    margin-left: 0;
    margin-right: 0;
}

/* Left side course title */
.row:has(.profile-overview-title) > div:first-child {
    flex: 1 1 auto;
    width: auto;
    max-width: none;
}

/* Right side submitted/pending/marked summary */
.row:has(.profile-overview-title) > div:last-child {
    display: flex !important;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-left: auto;
    padding-right: 0;
    white-space: nowrap;
    float: none !important;
    text-align: right;
}

/* Remove unnecessary label spacing */
.row:has(.profile-overview-title) > div:last-child label {
    margin: 0;
    padding: 0;
    font-weight: 600;
}

/* Mobile responsiveness */
@media (max-width: 767px) {
    .row:has(.profile-overview-title) {
        align-items: flex-start;
    }

    .row:has(.profile-overview-title) > div:first-child,
    .row:has(.profile-overview-title) > div:last-child {
        flex: 0 0 100%;
        width: 100%;
    }

    .row:has(.profile-overview-title) > div:last-child {
        justify-content: flex-start;
        margin-top: 8px;
        white-space: normal;
        flex-wrap: wrap;
    }
}

/* Assignment tabs — same style as Course Content tabs */
#pills-tab {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 18px;

    margin: 0;
    padding: 0 0 10px;
    border-bottom: none !important;
}

/* Remove existing fixed widths */
#pills-tab .nav-item {
    width: auto !important;
    margin: 0 !important;
    text-align: center;
}

/* Tab button */
#pills-tab .nav-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 150px;
    padding: 10px 28px;

    background: #17675F1A !important;
    color: #17675F80 !important;

    border: none !important;
    border-radius: 999px !important;

    font-size: 18px;
    font-weight: 500;
    line-height: 1.4;
    white-space: nowrap;

    transition:
        background-color 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease;
}

/* Count text */
#pills-tab .nav-link b {
    margin-left: 4px;
    color: inherit;
    font-weight: 700;
}

/* Hover */
#pills-tab .nav-link:hover {
    background: #17675F26 !important;
    color: #17675F !important;
    transform: translateY(-1px);
}

/* Active tab */
#pills-tab .nav-link.active,
#pills-tab .nav-link.active:hover {
    background: #1F776E !important;
    color: #ffffff !important;
}

/* Active bottom indicator */
#pills-tab .nav-link.active::after {
    content: "";
    position: absolute;

    left: 0;
    right: 0;
    bottom: -12px;

    height: 4px;
    background: #1F776E;
    border-radius: 4px;
}

/* Remove Bootstrap focus outline */
#pills-tab .nav-link:focus {
    box-shadow: none !important;
    outline: none;
}

/* Mobile */
@media (max-width: 767px) {
    #pills-tab {
        gap: 8px;
    }

    #pills-tab .nav-item {
        flex: 1 1 calc(50% - 8px);
    }

    #pills-tab .nav-link {
        width: 100%;
        min-width: 0;
        padding: 9px 14px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    #pills-tab .nav-item {
        flex: 1 1 100%;
    }
}
.no-record-found {
    width: 100%;
    min-height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #6c757d;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
}
















/* =====================================
   Pending Tab Cards - CSS Only
===================================== */

/* Pending grid spacing */
#pills-profile #profile > .row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -6px;
    margin-right: -6px;
    row-gap: 24px;
}

/* Four cards per row */
#pills-profile #profile > .row > .col-12.col-sm-3 {
    padding-left: 6px !important;
    padding-right: 6px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    margin: 0 !important;
}

/* Main student card */
#pills-profile .card {
    height: 100%;
    min-height: 220px;
    margin-bottom: 0;
    background: #ffffff;
    border: 1px solid #e8edf2;
    border-radius: 10px;
    box-shadow: none;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#pills-profile .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(31, 119, 110, 0.08);
}

/* Student information header */
#pills-profile .card-header {
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    min-height: 86px;
    padding: 12px 76px 12px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e8edf2;
}

/* Student details */
#pills-profile .card-title {
    width: 100%;
    margin: 0;
    line-height: 1.4;
}

#pills-profile .card-title p {
    display: flex;
    align-items: center;
    width: 100%;
    margin: 0 0 8px !important;
    color: #71809a !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#pills-profile .card-title p:first-child {
    font-size: 12px !important;
    font-weight: 700 !important;
}

#pills-profile .card-title p:last-child {
    margin-bottom: 0 !important;
    font-size: 18px !important;
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Icons beside name and enrollment */
#pills-profile .card-title p i {
    flex: 0 0 auto;
    width: 18px;
    margin-right: 7px !important;
}

/* Student image */
#pills-profile .card-options {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 52px;
    height: 52px;
    margin: 0;
}

#pills-profile .card-options img {
    display: block;
    width: 52px !important;
    height: 52px !important;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #edf2f5;
    background: #f5f7f9;
}

/* Hide broken image text overflow */
#pills-profile .card-options {
    overflow: hidden;
    border-radius: 50%;
}

/* File section */
#pills-profile .card-body {
    display: flex;
    align-items: flex-start;
    min-height: 132px;
    padding: 26px 16px !important;
    margin-top: 21px;
    border-radius: 8px;
    background: #ffffff;
}

/* Inner row */
#pills-profile .card-body > .row {
    width: 100%;
    margin: 0;
}

/* File block */
#pills-profile .card-body .col-12.col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    padding: 0;
}

/* File link */
#pills-profile .card-body a {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    color: #25364a !important;
    text-decoration: none;
}

/* File icon */
#pills-profile .card-body a > i {
    margin: 0 0 5px !important;
    font-size: 34px !important;
    color: #25364a !important;
}

/* File label */
#pills-profile .card-body .ellipses {
    width: auto;
    max-width: 180px;
    margin: 0;
    font-size: 15px;
    color: #222222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Disabled/no-file link */
#pills-profile .card-body a.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 1;
}

/* Tablet */
@media (max-width: 991px) {
    #pills-profile #profile > .row > .col-12.col-sm-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Mobile */
@media (max-width: 575px) {
    #pills-profile #profile > .row > .col-12.col-sm-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    #pills-profile .card {
        min-height: auto;
    }
}






/* =====================================
   Submitted Tab Cards - CSS Only
===================================== */

/* Grid */
#pills-home #profile > .row{
    display:flex;
    flex-wrap:wrap;
    row-gap:24px;
    margin-left:-6px;
    margin-right:-6px;
}

#pills-home #profile > .row > .col-12.col-md-3{
    padding:6px !important;
}

/* Card */
#pills-home .card{
    height:100%;
    border:1px solid #E6EDF3;
    border-radius:12px;
    overflow:hidden;
    background:#fff;
    box-shadow:none;
    transition:.25s ease;
}

#pills-home .card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

/* Header */
#pills-home .card-header{
    position:relative;
    min-height:86px;
    padding:14px 76px 14px 16px;
    background:#fff;
    border-bottom:1px solid #EEF2F6;
}

#pills-home .card-title{
    margin:0;
    width:100%;
}

#pills-home .card-title p{
    display:flex;
    align-items:center;
    margin-bottom:6px !important;
    color:#66758A !important;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

#pills-home .card-title p:first-child{
    font-size:12px !important;
    font-weight:700 !important;
}

#pills-home .card-title p:last-child{
    font-size:18px !important;
    font-weight:500;
}

#pills-home .card-options{
    position:absolute;
    top:14px;
    right:16px;
}

#pills-home .card-options img{
    width:52px !important;
    height:52px !important;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #EDF2F7;
}

/* Enter Marks Button */
#pills-home .passingID{
    width:calc(100% - 24px);
    margin:12px;
    border-radius:8px;
    font-weight:600;
    font-size:14px;
    background:#1F776E;
    border-color:#1F776E;
}

/* Body */
#pills-home .card-body{
    padding:16px !important;
}

/* File Grid */
#pills-home .card-body .row{
    row-gap:16px;
}

/* File Block */
#pills-home .card-body .col-12.col-md-4{
    display:flex;
    justify-content:center;
}

#pills-home .card-body a{
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    text-decoration:none;
    color:#2F3B4D !important;
}

#pills-home .card-body a i{
    font-size:36px !important;
    margin-bottom:8px;
}

/* File Name */
#pills-home .ellipses{
    width:100% !important;
    max-width:160px;
    text-align:center;
    font-size:14px;
}

/* Resubmit Badge */
#pills-home .blink{
    margin-top:8px;
    padding:4px 10px;
    border-radius:20px;
    background:#FFF4D6;
    color:#C98900 !important;
    font-size:12px;
    font-weight:600;
    animation:none;
}

/* Responsive */
@media(max-width:991px){

    #pills-home #profile > .row > .col-12.col-md-3{
        flex:0 0 50%;
        max-width:50%;
    }

}

@media(max-width:575px){

    #pills-home #profile > .row > .col-12.col-md-3{
        flex:0 0 100%;
        max-width:100%;
    }

}

/* ********************************** submitted tab content CSS end *************************************** */

/* =====================================
   Marked Tab Cards - CSS Only
===================================== */

/* Grid */
#pills-contact #profile > .row{
    display:flex;
    flex-wrap:wrap;
    row-gap:24px;
    margin-left:-6px;
    margin-right:-6px;
}

#pills-contact #profile > .row > .col-12.col-sm-3{
    padding:6px !important;
}

/* Card */
#pills-contact .card{
    height:100%;
    border:1px solid #E6EDF3;
    border-radius:12px;
    overflow:hidden;
    background:#fff;
    box-shadow:none;
    transition:.25s ease;
}

#pills-contact .card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

/* Header */
#pills-contact .card-header{
    position:relative;
    min-height:145px;
    padding:14px 16px 16px 16px;
    background:#fff;
    border-bottom:1px solid #EEF2F6;
}

/* Student Image */
#pills-contact .card-options{
    position:absolute;
    top:14px;
    right:16px;
}

#pills-contact .card-options img{
    width:52px !important;
    height:52px !important;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #EDF2F7;
}

/* Student Info */
#pills-contact .card-title{
    margin:0;
    width:100%;
}

#pills-contact .card-title p{
    display:flex;
    align-items:center;
    margin-bottom:6px !important;
    color:#66758A !important;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

#pills-contact .card-title p:first-child{
    font-size:12px !important;
    font-weight:700 !important;
}

#pills-contact .card-title p:last-child{
    font-size:18px !important;
    font-weight:500;
}

/* Update Marks Button */
#pills-contact .passingID{
    width:100%;
    margin-top:12px;
    border-radius:8px;
    background:#1F776E;
    border-color:#1F776E;
    font-weight:600;
}

/* Marks Badge */
#pills-contact .card-header strong{
    display:inline-block;
    margin-top:10px;
    padding:6px 12px;
    border-radius:20px;
    background:#E9F8F4;
    color:#1F776E;
    font-size:14px !important;
    font-weight:700;
}

/* Body */
#pills-contact .card-body{
    padding:16px !important;
}

/* Files Grid */
#pills-contact .card-body .row{
    row-gap:16px;
}

/* File Block */
#pills-contact .card-body .col-12.col-md-4{
    display:flex;
    justify-content:center;
}

#pills-contact .card-body a{
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    text-decoration:none;
    color:#2F3B4D !important;
}

#pills-contact .card-body a i{
    font-size:36px !important;
    margin-bottom:8px;
}

/* File Name */
#pills-contact .ellipses{
    width:100% !important;
    max-width:160px;
    text-align:center;
    font-size:14px;
}

/* Responsive */
@media (max-width:991px){

    #pills-contact #profile > .row > .col-12.col-sm-3{
        flex:0 0 50%;
        max-width:50%;
    }

}

@media (max-width:575px){

    #pills-contact #profile > .row > .col-12.col-sm-3{
        flex:0 0 100%;
        max-width:100%;
    }

}

/* ****************************************** marked tab content CSS end ************************************************ */
    </style>

    @include('grader/teacher.course_criteria_popup')
<?php



foreach($show_student_data as $rec){
  $course_id = $rec['co_id'];
  $course_assessment_id = $rec['assessment_id'];
  $course_cam_number = $rec['cam_number'];

   // echo $course_id.'--'.$course_assessment_id.'--'.$course_cam_number;exit;
}

$encr_co_id = my_simple_crypt($course_id, 'e' );
$encr_course_assessment_id = my_simple_crypt($course_assessment_id, 'e' );
$encr_course_cam_number = my_simple_crypt($course_cam_number, 'e' );
$encr_emp_user_id = my_simple_crypt($sess->emp_user_id, 'e' );

//print_r($show_student_data);exit();

$date_sr_no = 0;
$assignment_submitted=0;
$assignment_graded=0;
$assignment_missing=0;
$assessment_id=0;
$total_marks=0;
$course_criteria_id=0;
$cam_number=0;
$course_criteria_details_id=0;
$co_id=0;
foreach($show_student_data as $rec_all_aess){
    if($rec_all_aess['submission_status']=='YES'){
        $assignment_submitted++;
    }
    else if($rec_all_aess['submission_status']=='GRADED'){

        $assignment_graded++;
    }else{

        $assignment_missing++;
    }
}

//print_r($show_student_data);
foreach($show_student_data as $rec_studentsListtotal_marks){

    //$total_marks = $rec_studentsListtotal_marks['total_marks'];
    // $co_id = $rec_studentsListtotal_marks['co_id'];
    // $assessment_id = $rec_studentsListtotal_marks['assessment_id'];
    //$course_criteria_id=$rec_studentsListtotal_marks['course_criteria_id'];
    //$cam_number=$rec_studentsListtotal_marks['cam_number'];
    //$course_criteria_details_id=$rec_studentsListtotal_marks['course_criteria_details_id'];
    if($rec_studentsListtotal_marks['id']!=""){

        $total_marks = $rec_studentsListtotal_marks['total_marks'];
        $co_id = $rec_studentsListtotal_marks['co_id'];
		$assessment_id = $rec_studentsListtotal_marks['assessment_id'];
        $course_criteria_id=$rec_studentsListtotal_marks['course_criteria_id'];
        $cam_number=$rec_studentsListtotal_marks['cam_number'];
       $course_criteria_details_id=$rec_studentsListtotal_marks['course_criteria_details_id'];
    }
} 

?>

<div class="">
    <div class="row">
            <div class="col-2 col-sm-2 col-md-9 col-lg-9 col-xl-9" style="border: solid 0px green;padding-left: 0 !important;">
            <h4 class="profile-overview-title" >{{$course_name}} </h4>
            </div>
            <div style="display: inline-block; float:right"  >
                <label  style="padding-right:0px;padding-left: 0px;">Submitted: </label> {{$assignment_submitted}},
                <label  style="padding-right:0px;padding-left: 0px;">Pending: </label> {{$assignment_missing}},
                <label  style="padding-right:0px;padding-left: 0px;">Marked: </label> {{$assignment_graded}}
            </div>
    </div>
    <div class="card-body" style="padding-left: 0px; padding-right:0px;">
        <!-- start of tab menu ----->
        <ul class="nav nav-tabs" id="pills-tab" role="tablist" style="    border-bottom: 2px solid #17675F1A !important;
    padding-bottom: 8px;">
            <li class="nav-item" style="width:15%; text-align:center;">
                <a class="nav-link active" style="color: #0c0c0c" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Submitted : <b>{{$assignment_submitted}}</b></a>
            </li>
            <li class="nav-item" style="width:15%; text-align:center;">
                <a class="nav-link" style="color: #0c0c0c" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Pending : <b>{{$assignment_missing}}</b></a>
            </li>
            <li class="nav-item" style="width:15%; text-align:center;">
                <a class="nav-link" style="color: #0c0c0c" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Marked : <b>{{$assignment_graded}}</b></a>
            </li>
            <li style="float:right !important; width:55% !important; display: none;">

            <i class="fa fa-plus-circle" style="font-size:18px; float:right; margin:15px; color:#0C6;" data-toggle="modal" onclick="openMarksModal('<?php echo $assessment_id;?>','<?=$cam_number;?>','<?=$co_id;?>','<?=$course_criteria_id;?>')" data-target="#Student_Marks_list_new">Add Marks Listwise new</i>

                {{--<i class="fa fa-plus-circle" style="font-size:18px; float:right; margin:15px; color:#0C6;" data-toggle="modal" data-target="#Student_Marks_list">Add Marks Listwise</i>--}}
            </li>
            {{--///zain--}}

        </ul>
        <!-- end of tab menu ----->
        <!-- start of tab contents ----->
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <!--- First Tab Start ----->
                <?php
                $date_sr_no = 0;
                ?>
                <div class="card-body" style="padding:0px;">
                    <a style="color: black; display:none;" href="https://cms.bnu.edu.pk/all-assignment?co_id=<?=$encr_co_id?>&assessment_id=<?=$encr_course_assessment_id?>&cam_number=<?=$encr_course_cam_number?>&emp_user_id=<?=$encr_emp_user_id?>"><i class="fa fa-download" aria-hidden="true">Download All</i>
                    </a>
                    <div class="container-fluid" id="profile" style="border:solid 0px #0C0; margin-left:auto; margin-right:auto; width:100%;padding-left: 8px;
    padding-right: 5px !important;
    ">
                        <div class="row">
                           <?php
    error_reporting(0);
    $date_sr_no = 0;
    $submitted_record_found = false;

    foreach($show_student_data as $rec_all_aess){

        if($rec_all_aess['submission_status']=='YES'){

            $submitted_record_found = true;
                                    $image_name= $rec_all_aess['sai_enrollment_no'];
                                    $file_name= $rec_all_aess['submission_file_name'];
                                       //$file_drive= $rec_all_aess['google_drive'];

									 if($re_submit= $rec_all_aess['obtained_marks']!=''){
                                        $re_sub='Resubmit';
                                    }else{
                                        $re_sub='';
                                    }
                                    ?>
                            <?php
                            $file_name_ass=$rec_all_aess['file_name'];
                           // print_r($show_student_data);exit();

                            ?>

                                    <div class="col-12 col-md-3" style="padding:4px; !important; margin:0px !important; border:solid 0px #FF0000;">

                                    <div class="card">
									<div class="card-header">
										<h3 class="card-title"> @if($len <= 21)
                                            <p class="text-muted m-b-0" style="font-size: 11px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>
                                            @else
                                            <p class="text-muted m-b-0" style="font-size: 10px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>

                                            @endif
                                        </h3>
                                        <!-- <div class="col-12 col-md-2" style="display:inline;"> -->
                                        <!-- </div> -->

										<div class="card-options">
                                        <img src="{{asset('studentpics/'.$image_name.'.jpg')}}" class="rounded-circle" alt="Image" width="50" height="50">
										</div>
									</div>
                                    <button type="button" class="btn btn-info btn-sm passingID" onclick="get_co_id('<?php echo $rec_all_aess['obtained_marks'];?>','<?php echo $rec_all_aess['total_marks'];?>','<?php echo $rec_all_aess['teacher_comments'];?>','<?php echo $rec_all_aess['sai_enrollment_no'];?>','<?php echo $rec_all_aess['co_id'];?>','<?php echo $rec_all_aess['id'];?>','<?php echo $rec_all_aess['assessment_id'];?>','<?php echo $rec_all_aess['cam_number'];?>','<?php echo $rec_all_aess['course_criteria_id'];?>')" data-id="{{$rec_all_aess['id']}}">Enter Marks</button>

									<div class="card-body">
                                    <div class="row">
                                    <?php
                                               foreach($rec_all_aess['courseofferingcontent']  as $row){
                                                  // print_r($row);exit;
                                                  //$file_name=$row['file_name'];
                                               $file_drive= $row['google_drive'];
                                               if($row['file_status']==1){

                                              //echo  $file_upload=$row['file_name'];
                                                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                                if($row['file_name']=='' ){
                                                   // $file_upload='No File';
                                                }else{

                                                    $file_upload=$row['file_name'];//'Download';
                                                   // echo $ext;
                                                }
                                                if ($ext=='docx') {
													
                                                    $allowed ="fa fa-file-word-o";
                                                    $color='blue';
                                                }else if ($ext=='csv' or $ext=='xlsx'){
												
                                                    $allowed ="fa fa-file-excel-o";
                                                    $color='green';
                                                }else if ($ext=='pptx'){
												
                                                    $allowed ="fa fa-file-powerpoint-o";
                                                    $color='#ff6c43';
                                                }else if ($ext=='pdf'){
													
                                                    $allowed ="fa fa-file-pdf-o";
                                                    $color='#ff6c43';
                                                }else if ($ext==''){
													
                                                    $allowed ="fa fa-cloud-download";
                                                    $color='#ff6c43';
                                                }
                                                // else if($ext==''){
												// 	$allowed="fa fa-file-code-o";
												// 	$color='#2F3B4D';
												// 	$link='disabled';
												// }
                                                
                                                ?>
                                               
                                                <div class="col-12 col-md-4" style="display:inline;" >
                                                 <a  target="_blank" href="<?=($file_drive=='')?asset('https://student.bnu.edu.pk/images/'.$file_name):$file_drive?>"   style="color: #0c0c0c" ><i class="<?=$allowed;?>" style="color:<?=$color;?>; font-size:30px"></i>
                                                 <div class="ellipses" title="<?=$file_upload;?>"><?=$file_upload;?></div></a>
                                                 <?php if($re_sub!='') {?>
                                                        <p class="blink"><?=$re_sub;?><span>&#9733;</span></p>
                                                        <?php } ?>
                                                </div>
                                            
                                                    <?php } } ?>
                                               
                                            </div>
								        </div>
                                    </div>
                                </div>
                            
<?php
        }

        $date_sr_no++;
    }

    if (!$submitted_record_found) {
?>
        <div class="col-12">
            <div class="no-record-found">
                No Record Found
            </div>
        </div>
<?php
    }
?>
                        </div>
                    </div>
                </div>
                <!---- First Tabl End ---->
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <!--- SECOND Tab Start ----->
                <div class="card-body" style="padding:0px;">
                    <div class="container-fluid" id="profile" style="border:solid 0px #0C0;padding-left:2%; margin-left:auto; margin-right:auto; width:100%;padding-left: 8px;
    padding-right: 5px !important;">
                        <div class="row">
                         <?php
    error_reporting(0);
    $date_sr_no = 0;
    $pending_record_found = false;

    foreach($show_student_data as $rec_all_aess){

        if($rec_all_aess['submission_status']=='NO'){

            $pending_record_found = true;
                                    $image_name= $rec_all_aess['sai_enrollment_no'];
                                    $file_name= $rec_all_aess['submission_file_name'];
                                    $len=strlen($rec_all_aess['spi_name']);
                                    //echo $hello=str_repeat("&nbsp;", 10);

                                    ?>
                            <div class="col-12 col-sm-3" style="padding:4px !important; margin:0px !important; border:solid 0px #FF0000;">
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title"> @if($len <= 21)
                                            <p class="text-muted m-b-0" style="font-size: 11px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>
                                            @else
                                            <p class="text-muted m-b-0" style="font-size: 10px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>
                                            @endif
                                        </h3>

										<div class="card-options">
                                        <img src="{{asset('studentpics/'.$image_name.'.jpg')}}" class="rounded-circle" alt="Image" width="50" height="50">
										</div>
									</div>
									<div class="card-body" style="text-align: center;">
                                    <?php
                                                //array('xlsx', 'docx');
                                                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                                if($file_name==''){
                                                    $file_upload='No File';

                                                }else{

                                                    $file_upload='Download';
                                                   // echo $ext;
                                                }
                                                if ($ext=='docx') {
													 $link='';
                                                    $allowed ="fa fa-file-word-o";
                                                    $color='blue';
                                                }else if ($ext=='csv' or $ext=='xlsx'){
													$link='';
                                                    $allowed ="fa fa-file-excel-o";
                                                    $color='green';
                                                }else if ($ext=='pptx'){
													$link='';
                                                    $allowed ="fa fa-file-powerpoint-o";
                                                    $color='#ff6c43';
                                                }else if ($ext=='pdf'){
													$link='';
                                                    $allowed ="fa fa-file-pdf-o";
                                                    $color='#ff6c43';
                                                }else if ($ext!=''){
													$link='';
                                                    $allowed ="fa fa-file-text-o";
                                                    $color='#ff6c43';
                                                }else if($ext==''){
													$allowed="fa fa-file-code-o";
													$color='#2F3B4D';
													$link='disabled';
												}

                                                ?>
                                                <div class="row">
                                                <div class="col-12 col-md-6" style="display:inline;" >
                                                 <a href="{{asset('https://student.bnu.edu.pk/images/'.$file_name)}}"  style="color: #0c0c0c" class="<?=$link;?>"><i class="<?=$allowed;?>" style="color:<?=$color;?>; font-size:30px;margin-right: 10px"></i>
                                                 <div class="ellipses" title="<?=$file_upload;?>"><?=$file_upload;?></div></a>
                                                </div>
                                            </div>
								        </div>
                                    </div>
                                </div>
                              <?php
        }

        $date_sr_no++;
    }

    if (!$pending_record_found) {
?>
        <div class="col-12">
            <div class="no-record-found">
                No Record Found
            </div>
        </div>
<?php
    }
?>
                        </div>
                    </div>
                </div>
                <!---- SECOND Tabl End ---->
            </div>
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                <!--- THIRD Tab Start ----->
                <div class="card-body" style="padding:0px;">
                <a style="color: black; display:none;" href="https://cms.bnu.edu.pk/all-assignment?co_id=<?=$encr_co_id?>&assessment_id=<?=$encr_course_assessment_id?>&cam_number=<?=$encr_course_cam_number?>&emp_user_id=<?=$encr_emp_user_id?>&submission_status=graded"><i class="fa fa-download" aria-hidden="true">Download All</i></a>

                    <div class="container-fluid" id="profile" style="border:solid 0px #0C0;padding-left:2%; margin-left:auto; margin-right:auto; width:100%;padding-left: 8px;
                    padding-right: 5px !important;">
					   <div class="row">
                           <?php
    error_reporting(0);
    $date_sr_no = 0;
    $marked_record_found = false;

    foreach($show_student_data as $rec_all_aess){

        if($rec_all_aess['submission_status']=='GRADED'){

            $marked_record_found = true;
                                    $image_name= $rec_all_aess['sai_enrollment_no'];
                                    // $len=count($rec_all_aess['spi_name']);
                                    $obtainedmarks = $rec_all_aess['obtained_marks'];

                                    $len=strlen($rec_all_aess['spi_name']);
                                    $file_name= $rec_all_aess['submission_file_name'];
                                    $file_drive= $rec_all_aess['google_drive'];
                                    //echo $hello=str_repeat("&nbsp;", 10);
                                    ?>
                                    <div class="col-12 col-sm-3" style="padding:4px !important; margin:0px !important; border:solid 0px #FF0000;">

								<div class="card">
									<div class="card-header">
										<h3 class="card-title"> @if($len <= 21)
                                            <p class="text-muted m-b-0" style="font-size: 11px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>
                                            <button type="button" class="btn btn-info btn-sm passingID" onclick="get_co_id('<?php echo $rec_all_aess['obtained_marks'];?>','<?php echo $rec_all_aess['total_marks'];?>','<?php echo $rec_all_aess['teacher_comments'];?>','<?php echo $rec_all_aess['sai_enrollment_no'];?>','<?php echo $rec_all_aess['co_id'];?>','<?php echo $rec_all_aess['id'];?>','<?php echo $rec_all_aess['assessment_id'];?>','<?php echo $rec_all_aess['cam_number'];?>','<?php echo $rec_all_aess['course_criteria_id'];?>')" data-id="{{$rec_all_aess['id']}}">Update Marks</button>
                                            <div> <strong style="font-size:16px"> Marks : {{$obtainedmarks.'/'.$total_marks}}</strong></div>

											@else
                                            <!-- <img src="{{asset('studentpics/'.$image_name.'.jpg')}}" class="rounded-circle" alt="Image" width="50" height="50">    -->

                                            <p class="text-muted m-b-0" style="font-size: 10px;font-weight: bold"> <i class="fa fa-graduation-cap  m-r-15"></i>  {{$rec_all_aess['spi_name']}}</p>
                                            <p class="text-muted m-b-0"> <i class="fa fa-star text-info  m-r-20"></i> {{$image_name}}</p>
                                            <button type="button" class="btn btn-info btn-sm passingID" onclick="get_co_id('<?php echo $rec_all_aess['obtained_marks'];?>','<?php echo $rec_all_aess['total_marks'];?>','<?php echo $rec_all_aess['teacher_comments'];?>','<?php echo $rec_all_aess['sai_enrollment_no'];?>','<?php echo $rec_all_aess['co_id'];?>','<?php echo $rec_all_aess['id'];?>','<?php echo $rec_all_aess['assessment_id'];?>','<?php echo $rec_all_aess['cam_number'];?>','<?php echo $rec_all_aess['course_criteria_id'];?>')" data-id="{{$rec_all_aess['id']}}"><i class="fa fa-plus fa-spin ml-2"></i>Update Marks</button>
											<div><strong style="font-size:16px"> Marks : {{$obtainedmarks.'/'.$total_marks}}</strong></div>

                                            @endif
                                        </h3>


										<div class="card-options">
                                        <img src="{{asset('studentpics/'.$image_name.'.jpg')}}" class="rounded-circle" alt="Image" width="50" height="50">
											</br>
										</div>
										
									</div>
									<div class="card-body">
                                    <!-- <div class="card-options">
                                        <button type="button" class="btn btn-info btn-sm passingID" onclick="get_co_id('<?php echo $rec_all_aess['obtained_marks'];?>','<?php echo $rec_all_aess['total_marks'];?>','<?php echo $rec_all_aess['teacher_comments'];?>','<?php echo $rec_all_aess['sai_enrollment_no'];?>','<?php echo $rec_all_aess['co_id'];?>','<?php echo $rec_all_aess['id'];?>','<?php echo $rec_all_aess['assessment_id'];?>','<?php echo $rec_all_aess['cam_number'];?>','<?php echo $rec_all_aess['course_criteria_id'];?>')" data-id="{{$rec_all_aess['id']}}"><i class="fa fa-plus fa-spin ml-2"></i>Update Marks</button>
										</div> -->
                                            <div class="row">
                                    <?php
                                               foreach($rec_all_aess['courseofferingcontent']  as $row){
                                                  // print_r($row);exit;
                                                  //$file_name=$row['file_name'];
                                               $file_drive= $row['google_drive'];
                                               if($row['file_status']==1){

                                              //echo  $file_upload=$row['file_name'];
                                                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                                if($row['file_name']=='' ){
                                                   // $file_upload='No File';
                                                }else{

                                                    $file_upload=$row['file_name'];//'Download';
                                                   // echo $ext;
                                                }
                                                if ($ext=='docx') {
													
                                                    $allowed ="fa fa-file-word-o";
                                                    $color='blue';
                                                }else if ($ext=='csv' or $ext=='xlsx'){
												
                                                    $allowed ="fa fa-file-excel-o";
                                                    $color='green';
                                                }else if ($ext=='pptx'){
												
                                                    $allowed ="fa fa-file-powerpoint-o";
                                                    $color='#ff6c43';
                                                }else if ($ext=='pdf'){
													
                                                    $allowed ="fa fa-file-pdf-o";
                                                    $color='#ff6c43';
                                                }else if ($ext==''){
													
                                                    $allowed ="fa fa-cloud-download";
                                                    $color='#ff6c43';
                                                }
                                                // else if($ext==''){
												// 	$allowed="fa fa-file-code-o";
												// 	$color='#2F3B4D';
												// 	$link='disabled';
												// }
                                                
                                                ?>
                                               
                                                <div class="col-12 col-md-4" style="display:inline;" >
                                                 <a  target="_blank" href="<?=($file_drive=='')?asset('https://student.bnu.edu.pk/images/'.$file_name):$file_drive?>"   style="color: #0c0c0c" ><i class="<?=$allowed;?>" style="color:<?=$color;?>; font-size:30px"></i>
                                                 <div class="ellipses" title="<?=$file_upload;?>"><?=$file_upload;?></div></a>
                                                 <?php if($re_sub!='') {?>
                                                        <p class="blink"><?=$re_sub;?><span>&#9733;</span></p>
                                                        <?php } ?>
                                                </div>
                                            
                                                    <?php } } ?>
                                          
                                            </div>
                                               
								</div>
                                </div>

                                    </div>
                              <?php
        }

        $date_sr_no++;
    }

    if (!$marked_record_found) {
?>
        <div class="col-12">
            <div class="no-record-found">
                No Record Found
            </div>
        </div>
<?php
    }
?>

                        </div>
                    </div>
                </div>
                <!---- THIRD Tabl End ---->
            </div>
        <!-- end of tab contents ----->
    </div>
    </div>
</div>

<!-- /////// update markx modal start////////// -->
<div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background-color:#D2D6F1" >
                    <h5 >Assessment Total Marks : </h5>
                    <h5 style="padding-left: 10px;" id="total_marks_ass"></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="card1">

                        <div class="card-block1">
                            <form id="cform" enctype="multipart/form-data" method="post" name="cform"  id="cform" >
                                <input type="hidden"  name="idkl" id="idkl" value="">
                                <input type="hidden"  name="sai_enrollment" id="sai_enrollment" value="">
                                <input type="hidden"  name="course_criteria_id" id="course_criteria_id_2" value="">
                                <input type="hidden"  name="co_id" id="co_id_2" value="">
                                <input type="hidden"  name="total_marks" id="total_marks2" value="">
                                <input type="hidden"  name="assess_id" id="assess_id" value="">
                                <input type="hidden"  name="assess_number" id="assess_number" value="">

                                <div class="card-header1">
                                        <div class="form-group row" style="margin-top: 20px;">
                                            <label class="col-sm-4 col-form-label" style="padding-right:0px">Obtained marks: </label>
                                            <div class="col-sm-8">
                                                {{-- <input   type="number"  id="ob-marks" name="ob-marks" class="form-control" placeholder="0" min="0" step="0.1"/> --}}
                                                <input type="text"  onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46" maxlength="5" id="ob-marks" name="ob-marks" class="form-control" min="0" max="500" maxlength="5"  placeholder="Marks">
                                            </div>
                                        </div>

                                </div>
                                <div class="form-group row" style="margin-top: 20px;">
                                    <div class="col-sm-4 col-form-label">
                                    <label  style="padding-left:12px">Comments: </label>

                                    </div>
                                    <div class="col-sm-8">
                                        <textarea  type="text"  id="comment" name="comment" class="form-control" placeholder="Add Comments" maxlength="600" row="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="display:none;" >
                                <a id="href"  class="file-remove-btn" style="color:black;cursor: pointer;" >
                                        Uploaded File(Download)
                                        <label  id="lastuploadfile" style="cursor:pointer"></label>
                                </a>
                                    <br>
                                    <label for="upload">Upload File (Maximum size 200 MB!)</label>
                                    <input type="file" class="form-control" name="uploadfile" id="uploadfile" placeholder="Title Here . . ." required="required">
                                </div>
                                <!-- <div align="center" class="form-group">
                                    <button id="btn" name="btn" class="btn btn-success btn-submit">Submit</button>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>

               <div class="modal-footer">
                    <div id="saved_msg" style="display: none;color: green"><span>Data Saved Successfully</span></div>
                    <div id="error_msg" class="msg-div" style="display: none;color: red"><span>The File must be a required: xlsx, docx,pdf</span></div>
                    <div id="loading" class="preloader3 loader-block" style="display: none; height: 0px">
                            <h6>Please wait processing.....</h6>
                    </div>
                    <button id="btn" name="btn" class="btn btn-success btn-submit">Submit</button>

                    <button type="button" id="btn" name="btn" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
<!-- /////// update markx modal end////////// -->

    <!-- {{--zain--}} -->
    <div class="modal fade" id="courseMarksModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Marks Entry for<span id="marks-name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="marksEntryForm" name="marksEntryForm" role="form">
                        <div class="form-group row" style="margin-right:10px;">
                            <div class="col-md-2 col-sm-6 col-4 text-right">
                                <label for="cam_details" class="form-check-label"><b>Title :</b></label>
                                <input type="hidden" id="form_change_flag" name="form_change_flag" value="1">
                                <input type="hidden" id="co_id" name="co_id">
                                <input type="hidden" id="assessment_id" name="assessment_id">
                                <input type="hidden" id="cam_number" name="cam_number">
                                <input type="hidden" id="course_criteria_id" name="course_criteria_id">
                                <input type="hidden" id="ccd_id" name="ccd_id">
                            </div>
                            <div class="col-md-10 col-sm-6 col-8">
                                <input type="text" id="cam_details" name="cam_details" class="form-control">
                            </div>
                            <div class="col-md-2 col-sm-6 col-4 text-right" style="margin-top: 5px;">
                                <label for="cam_date" class="form-check-label"><b>Date :</b></label>
                            </div>
                            <div class="col-md-4 col-sm-6 col-8" style="margin-top: 5px;">
                                <input type="date" id="cam_date" name="cam_date" min="2018-01-01" max="2022-12-31" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-6 col-4 text-right" style="margin-top: 5px;">
                                <label for="total_marks" class="form-check-label"><b>Total Marks :</b></label>
                            </div>
                            <div class="col-md-3 col-sm-6 col-8" style="margin-top: 5px;">
                                <input type="number" id="total_marks" name="total_marks" class="form-control" min="1" step="1" max="500" onchange="setMaxLimit()" required>
                                <input type="hidden" id="old_total_marks" name="old_total_marks" value="0" >
                                <small id="res_total_marks" class="text-danger" style="display:none;"></small>
                            </div>
                        </div>
                        @csrf
                        <table id="MarksEntryMainTable" class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                            <thead class="bnu-header">
                            <tr>
                                <th class="tab-title-center">ID. </th>
                                <th class="tab-title-center">Sr. </th>
                                <th class="tab-title-center">Enroll No. </th>
                                <th class="tab-title">Name </th>
                                <th class="tab-title-center">Obt. Marks </th>
                            </tr>
                        </table>

                        <div class="alert alert-success" id="success-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-danger" id="error-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-success" id="wait-msg" style="display:none;margin-top:5px;">Please wait Processing.....'</div>
                        <input type="submit" value="Save Marks" id="marks_submit_btn" class="btn btn-primary btn-lg float-md-right" style="margin-top: 5px;">
                    </form>
                </div>
            </div>
        </div>
    </div>



@stop
@section('javascript')
<script>

//    zain
// var userName = document.querySelector('#ob-marks');

// userName.addEventListener('input', restrictNumber);
// function restrictNumber (e) {
//     var newValue = this.value.replace(new RegExp(/[^\d]/,'ig'), "");
//     this.value = newValue;
// }

$("#marksEntryForm").submit(function(e)
{

    //alert ('ok');
    return false;
    $("#error-msg").css('display','none');
    $("#success-msg").css('display','none');
    $("#marks_submit_btn").attr('disabled','true');
    $('#wait-msg').show();
        var show_msg = 'Please wait Processing.....';
      $("#wait-msg").html(show_msg);
    e.preventDefault();
    $.ajax({
        type:'POST',
        url:'{{url('teacher/courses/store/marks')}}',
        data: $("#marksEntryForm").serializeArray(),
        success:function(data){
            location.reload();

            var result          = data.split('-');
            var res             = result[0];
            var missing_marks   = result[1];

            if(res=='A'){
                  $("#wait-msg").css('display','none');
                $("#success-msg").css('display','block');
                var show_msg = '<strong>Success!</strong> Marks Saved Successfully';
                if(missing_marks>0)
                {
                    show_msg += '<br>'+missing_marks+' Students Marks Are Missing';
                }
                $("#success-msg").html(show_msg);
                setTimeout(function(){
                    $('#courseMarksModal').modal('hide');
                    getCourseCriteria();
                },2000);

            }else if(res=='B'){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Alert!</strong> No Change in the Marks');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else if(res=='C'){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Alert!</strong> Obtained Marks Must Be Less Than Total Marks');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else if(res=='D'){
                $("#success-msg").css('display','block');
                $("#success-msg").html('<strong>Success!</strong> Title & Date Saved Successfully');
                setTimeout(function(){
                    $('#courseMarksModal').modal('hide');
                    getCourseCriteria();
                },2000);
            }else if(res==0){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong> Unchanged Marks, Please Correct & Try Again');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else{
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong>'+data);
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }
        },
        error: function(data,status, response) {
            var result          = data.split('-');
            var res             = result[0];
            var missing_marks   = result[1];
            var errorData = jQuery.parseJSON(res.responseText);
            var error = errorData.errors;
            var msg = errorData.message;
            if(msg=='The given data was invalid.')
            {
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong> Invalid Marks or Date/Details, Please Correct & Try Again');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }
            var tot_marks = error.total_marks;
            $("#res_total_marks").css('display','block');
            $("#res_total_marks").html(tot_marks);
            $('#res_total_marks').focus();

        }
    });
});
function openMarksModal(assess_type,num,co_id,cc_id)
{
    //alert(co_id+"___"+assess_type+"___"+num+"___"+cc_id);
   // alert('ok');
    // $('#marks-name').html(' '+course+' ('+assess_name+' '+ass_number+')');
    // getAssessmentDetails(co_id,num);
    $("#courseMarksModal").modal();
    openMarksEntryPopup(co_id,assess_type,num,cc_id);
}
function openMarksEntryPopup(co_id,assess_type,num,cc_id)
{
    //alert(co_id+"___"+assess_type+"___"+num+"___"+cc_id);
    var errorCheck = $.fn.dataTable.ext.errMode = 'throw';
    $("#marks_submit_btn").removeAttr("disabled");
    $("#error-msg").css('display','none');
    $("#success-msg").css('display','none');
   // $('#form_change_flag').val('0');
    var get_total_marks=0;
    var get_cam_details=0;
    var get_cam_date=0;
    if(co_id && assess_type && num)
    {
        $("#sel_co_id").val(co_id);
        $("#sel_assess_type").val(assess_type);
        var url = '{{url('teacher/courses/students/list/')}}/'+co_id+"/"+assess_type+"/"+num;
        var table = $('#MarksEntryMainTable').DataTable();
        table.destroy();
        var t = $('#MarksEntryMainTable').DataTable( {
            "keys" : {columns:[4]},
            "searching": false,
            "paging": false,
            "bInfo": false,
            "ajax": {
                "url": url,
                "dataSrc": ""
            },
            "columnDefs": [{
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },{
                "targets": [ 1 ],
                "searchable": false,
                "orderable": false,
                "className": "text-center",
                "responsivePriority": 3
            },{
                "targets": 2,
                "className": "text-center",
                "width": "2%",
                "responsivePriority": 1,
                "orderable": false,
            },{
                "targets": 3,
                "className": "text-left",
                "orderable": false,
            },{
                "targets": 4,
                "className": "text-center",
                "searching": false,
                "orderable": false,
                "responsivePriority": 2
            }],
            "columns": [
                { data: 'cam_id'},
                { data: 'cam_id'},
                { data: 'sai_enrollment_no'},
                { mRender: function (data, type, row) {
                    var std_name = row.spi_name;
                    if(std_name.length<=18){
                        return std_name;
                    }else{
                        return std_name.substr(0, 18) + "...";
                    }
                }
                },
                { mRender: function (data, type, row, meta) {
                    if(row.total_marks!=null)
                    {
                        get_total_marks = row.total_marks;
                    }
                    get_cam_details = row.cam_details;
                    get_cam_date    = row.cam_date;

                    // alert(get_cam_date);
                    if(row.obtained_marks==undefined){
                        var obtMarks = '';
                    }
                    else{
                        var obtMarks = row.obtained_marks;
                    }
                    $("#total_marks").val(get_total_marks);
                    $("#old_total_marks").val(get_total_marks);
                    $(".obt-marks").attr("max", get_total_marks);

                    $("#cam_details").val(get_cam_details);
                    $("#cam_date").val(get_cam_date);

                    $("#co_id").val(co_id);
                    $("#assessment_id").val(assess_type);
                    $("#cam_number").val(num);
                    $("#course_criteria_id").val(cc_id);
                    $("#ccd_id").val(row.ccd_id);


                    return '<input type="number" id="'+meta.row+'" name="obt_marks[]"  class="form-control obt-marks obt-marks-number" min="0" step="0.01" value="'+obtMarks+'"/>' +
                        '<input type="hidden" id="old_obt_marks[]" name="old_obt_marks[]" value="'+obtMarks+'" />' +
                        '<input type="hidden" id="cam_id[]" name="cam_id[]" value="'+row.cam_id+'"/>' +
                        '<input type="hidden" id="sai_enrollment_no[]" name="sai_enrollment_no[]" value="'+row.sai_enrollment_no+'"/>' +
                        '<input type="hidden" id="sai_id[]" name="sai_id[]" value="'+row.sai_id+'"/>' +
                        '<input type="hidden" id="sc_co_id[]" name="sc_co_id[]" value="'+row.sc_co_id+'">'
                }
                }
            ]
        });

        t.on( 'order.dt search.dt', function () {
            t.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    }
}

    ////////////////////////////////
    function get_co_id(obt_marks,total_marks,comment,sai_enrollment,co_id,assessment_id,assess_id,cam_number,course_criteria_id){
        // alert(obt_marks);
        $('#sai_enrollment').val(sai_enrollment);
        $('#co_id_2').val(co_id);
        $('#course_criteria_id_2').val(course_criteria_id);
        $('#ob-marks').val(obt_marks);
        $('#comment').val(comment);
        $("#total_marks_ass").html(total_marks);
        $("#total_marks2").val(total_marks);
        $('#total_marks_ass').val(total_marks);
        $('#assess_id').val(assess_id);
        $('#assess_number').val(cam_number);

             var description = '';
        $.ajax({
            type:'get',
            url:'{{url('checkAssessment')}}/'+co_id+'/'+assessment_id,
            success:function(data){
                if(data.length > 0){
                console.log(data);
                console.log(data['0']['seen_record']);
                if(data['0']['course_assessment_id']==assessment_id){
                //alert(sai_enrollment);
                var downloadPath = '<?=url('/docs/')?>';
                var filepath = downloadPath+'/'+data['0']['file_name'];

                if( !(data['0']['file_name']) ){
                    $('.file-remove-btn').hide();
                }

                $("#href").attr('href',filepath);

                }else if( !(data['0']['file_name']) ){
                    $('.file-remove-btn').hide();
                }

                }


            }
        });

    }
    $(".passingID").click(function (id) {
        var ids = $(this).attr('data-id');
        $("#idkl").val( ids );
        $('#myModal').modal('show');
    });


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#marks_list_form_button").click(function(e){


        //$('#cc').val($('#tt_id').val());
        //var getcontent = $("#getcontent").val();
        var bi_form = document.querySelector('#marks_list_form');
        var fd = new FormData(bi_form);
        $.ajax({
                type:'POST',
                url: '{{route('teacher/courses/store/marksnew')}}',
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
            $("#saved_msgall").show()

            //$("#comment").val('')
            setTimeout(function(){
                location.reload();
               // $("#saved_msg").hide()
                $('#Student_Marks_list').modal('hide');
                $("#ob-marks").val('');

            }, 1000);
        },
        complete: function(){
        }
    });


    });
    $("#btn").click(function(e){
        e.preventDefault();
        var idkl = $("#idkl").val();
        var obmarks = parseInt($("#ob-marks").val());
        var comment = $("#comment").val();
		var total_marks_ass = parseInt($("#total_marks_ass").val());

		if(obmarks>total_marks_ass){
            alert('Please select a value that is no more than '+' '+total_marks_ass);
            exit;
        }

        ///file extension verify ////
         var file        = $("#uploadfile").val();
        $('.msg-div').hide();
        $('.form-control').removeClass('is-invalid');
        var error = 0;
        var typeArr     = ["xlsx","docx","pptx","jpg","jpeg","pdf","png"];
        if(file.trim() != ''){
            var fileInfoArr = file.split(".");
            var len         = fileInfoArr.length;
            var type        = fileInfoArr[len-1];
            var index       = typeArr.indexOf(type);
            if(index < 0){
                error++;
                $('#uploadfile').addClass('is-invalid');
                $('#error_msg span').html('Only xlsx, docx, jpg, jpeg, png and pdf are allowed !');
            }
        }
        if( error > 0 ){
            $('#error_msg').show();
            return false;
        }
            $('#loading').show();
           // $("#btn").attr("disabled", true);

        var bi_form = document.querySelector('#cform');
        var fd = new FormData(bi_form);
        $.ajax({
                type:"post",
                url:'<?=url('teacher/stdupdatemarks')?>',
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
               //  alert(data); return false;

            $('#loading').hide();
            $("#saved_msg").show();
            $("#comment").val('');
             $('#uploadfile').addClass('is-valid');
           setTimeout(function(){
              location.reload();
               $("#saved_msg").hide()
               $('#myModal').modal('hide');
               $("#ob-marks").val('');
                $("#uploadfile").val('');

           }, 1000);
            //alert(data.success);
        },
        complete: function(){
        }
    });
    });





</script>
@stop
