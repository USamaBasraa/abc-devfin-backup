<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
//  echo $adm_sch_id;
// print_r($course_id);

// exit;
$course_id=$co_id;

?>
{{-- @dd('123A'); --}}
<style>

.custom{
    padding-top: 5px;
}
.main-content {
    padding-left:12px !important;
    padding-right:12px !important;
    padding-bottom: 42px !important;
}

.main-content {
    padding: 25px !important;
    padding-bottom: 50px !important;
    padding-left: 25px !important;
    padding-right: 25px !important;
}
.exam-schedule-table {
    width: 100%;
    margin-bottom: 0;
    background-color: #fff;
    border-collapse: collapse;
}

.exam-schedule-table thead td {
    font-size: 12px;
    font-weight: 600;
    color: #555;
    padding: 14px 10px 10px 10px;
    border-top: 0 !important;
    border-bottom: 1px solid #e5e5e5 !important;
    background-color: #fff;
    text-align: center;
}

.exam-schedule-table tbody td {
    font-size: 12px;
    color: #555;
    padding: 12px 10px;
    border-top: 0 !important;
    border-bottom: 1px solid #e5e5e5 !important;
    background-color: #fff;
    vertical-align: middle;
    text-align: center;
}

.exam-schedule-table tbody tr:last-child td {
    border-bottom: 0 !important;
    padding-top: 14px;
    padding-bottom: 14px;
}

.exam-schedule-table .exam-action-btn {
    color: #fff !important;
    min-width: 130px;
    height: 35px;
    padding: 7px 18px !important;
    font-size: 12px;
    border-radius: 5px;
    box-shadow: none !important;
}


.exam-schedule-table .exam-action-btn:hover,
.exam-schedule-table .exam-action-btn:focus {
    background-color: #126f65 !important;
    border-color: #126f65 !important;
    color: #fff !important;
}

.exam-schedule-table .exam-student-col {
    vertical-align: middle;
    min-width: 130px;
}

.exam-schedule-table .exam-student-count {
    font-size: 14px;
    font-weight: 600;
    color: #222;
    padding: 4px 0 8px;
}

.exam-schedule-table .exam-student-btn-wrap {
    padding-top: 10px;
}
.pic-container {
    width: 100%;
    min-height: 110px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;

    color: #6b7280;
    font-size: 15px;
    font-weight: 500;

    padding: 30px 20px;
    margin-top: 0;

    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}
.exam-schedule-table .exam-action-btn {
    background-color: #1F776E !important;
    border-color: #1F776E !important;
    color: #fff !important;

    min-width: 130px;
    height: 35px;
    padding: 7px 18px !important;
    font-size: 12px;
    border-radius: 5px;
    box-shadow: none !important;
}

.exam-schedule-table .exam-action-btn:hover,
.exam-schedule-table .exam-action-btn:focus {
    background-color: #1F776E !important;
    border-color: #1F776E !important;
    color: #fff !important;
}

/* Disabled button bhi same color rahe */
.exam-schedule-table .exam-action-btn:disabled,
.exam-schedule-table .exam-action-btn.disabled {
    background-color: #1F776E !important;
    border-color: #1F776E !important;
    color: #fff !important;
    opacity: 0.65;
}
.btn-primary:disabled {
    color: #fff !important;
    background-color: #6da6a1 !important;
    border-color: #6da6a1 !important;
}
</style>
@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')
<div>
         <div style="float:left">             
            <h4 class="profile-overview-title" style="margin-bottom:14px !important;">{{$course_name}}</h4>
        </div>
        <?php if(!$adm_sch_id){?>
            <div style="float:right">
            <a style="text-decoration: underline;"  href="{{url('teacher/quiz_question')}}?sch_id=<?=$adm_sch_id?>&co_id=<?=$course_id?>&from_link=yes"><b>Question Bank</b></a>
            </div>
        <?php } ?>
</div>
<div class="card">     
    <div class="card-body">       
   
        <form enctype="multipart/form-data" method="post" name="exam_form"  id="exam_form">
            @csrf
            <input type="hidden" style="width: 200px!important" value="<?=$co_id?>" class="form-control" id="co_id" name="co_id"></input>
            <input type="hidden" style="width: 200px!important"  class="form-control" id="id" name="id"></input>
            <input type="hidden" style="width: 200px!important"   class="form-control" id="course_criteria_id" name="course_criteria_id"></input>
            <input type="hidden" style="width: 200px!important"   class="form-control" id="edit_file" name="edit_file"></input>
            <input type="hidden" style="width: 200px!important" value="<?=$adm_sch_id?>" class="form-control" id="adm_sch_id" name="adm_sch_id"></input>
            
            {{-- <div class="container">
                <div class="row custom" >
                    <?php if($adm_sch_id !='' ){  ?>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Assessment:</div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">  
                        <select class="form-control" style="width: 250px!important;"  onclick="getadmval('<?=$adm_sch_id?>')" id="assessment_id" name="assessment_id">
                        <option value="<?=$adm_sch_id?>"><?=$adm_sch_name[0]->admission_sch_name?></option>
                        </select>
                    </div>  
                    <?php }else{ ?>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:right;padding-top:6px">Assessment:</div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">   
                        <select class="form-control" style="width: 250px!important;"  onchange="getval(this,'<?=$co_id?>')" id="assessment_id" name="assessment_id">
                        <option value="">--Select Assessment--</option>
                        @foreach($course_criteria as $rec_course_criteria)
                        <option value="{{$rec_course_criteria->assessment_id}}">{{ $rec_course_criteria->assessment_name}}</option>
                        @endforeach
                        </select>
                    </div>  
                    <?php } ?>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;border:0px solid black;padding-top:6px;text-align:right;">Assessment No:<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                                <input type="text" style="width: 250px!important;" class="form-control" id="assessment_no" name="assessment_no" readonly ></input>
                        </div>
                </div>
                <div class="row custom">                   
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Description:<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="text" style="width: 250px!important;" class="form-control exam_input" id="exam_name" maxlength="100" placeholder="Description" name="exam_name"></input>
                    </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">Total Questions:<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="number" min="0" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" placeholder="Total Questions" style="width: 250px!important" class="form-control exam_input" id="total_question" name="total_question"></input>
                    </div>                       
                </div>
                <div class="row custom">                   
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Due Date:<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="text" style="width: 250px!important" class="form-control exam_input assess-form-input optional-inputs cms-datePicker" id="duedate" name="duedate" placeholder="Due Date" maxlength="10">    
                        </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">Start Time:<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="time" style="width: 250px!important" class="form-control exam_input assess-form-input optional-inputs" id="duetime" name="duetime"  placeholder="Due Time">   
                    </div>                       
                </div>
                <div class="row custom">                   
                    <!-- <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Duration(In Minutes)<b class="text-danger">*</b> </div> -->
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Duration (In Minutes)<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="number"  style="width: 250px!important" class="form-control exam_input assess-form-input" id="duration" min="0" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" name="duration" placeholder="duration">   
                        </div>
                    <!-- <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">File<b class="text-danger">(Optional)</b> </div> -->
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">File<b class="text-danger"> (Optional)</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <input type="file" style="width: 250px!important" class="form-control" name="uploadfile" id="uploadfile" >  
                        </div>       
                        <div style="display:none;padding-top: 6px" id="download_div"> <a id="exam_file" style="text-decoration: underline !important;color:black"  target="_blank" ><img src="{{asset('images/download.png')}}"  width="100px">  </a></div>               
                </div>
                <div class="row custom" style="display:none;">                   
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Pass Percentage<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                             <input type="text" style="width: 250px!important" class="form-control" id="percentage_status" name="percentage_status"></input>
                        </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">Paper Pattern</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <select class="form-control assess-form-input" style="width: 250px!important"  id="papper_pattern" name="papper_pattern">
                                <option value="R">Random Order</option>
                                <option value="F">Fixed Order</option>
                            </select>
                    </div>                       
                </div>
                <div class="row custom" style="display:none;">                   
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;padding-top:6px;text-align:right;padding-top:6px">Pass Percentage<b class="text-danger">*</b> </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                             <input type="text" style="width: 250px!important" class="form-control" id="percentage_status" name="percentage_status"></input>
                        </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="max-width: 12%;padding-top:6px;text-align:right;">Paper Pattern</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" style="border:0px solid black;text-align:left;" >
                            <select class="form-control assess-form-input" style="width: 250px!important"  id="papper_pattern" name="papper_pattern">
                                <option value="R">Random Order</option>
                                <option value="F">Fixed Order</option>
                            </select>
                    </div>                       
                </div> 
                <div class="row custom" style="margin-top:20px" >                      
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style="border:0px solid black;text-align:left;padding-left: 40%" >
                        <button style="padding-left:108px;padding-right:106px" type="button" class="btn btn-primary" onclick="insertcreatexam()">Schedule</button>
                    </div> 
                </div>
                <div class="row custom" style="margin-top:20px" >  
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style="border:0px solid black;text-align:left;padding-left: 40%" >                     
                        <div id="edit-fail-msg"     class="msg-div" style="color: red;float:left"><span></span></div>
                        <div id="edit-success-msg"  class="succ-msg-div" style="color: green;float:left"><span></span></div>       
                    </div>                            
                </div>
            </div>           --}}

            <style>
    .exam-form-box {
        background: #fff;
        padding: 15px 12px 12px 12px;
        border-radius: 2px;
    }

    .exam-form-box label {
        font-size: 13px;
        font-weight: 600;
        color: #222;
        margin-bottom: 6px;
    }

    .exam-form-box .form-control {
        height: 25px;
        font-size: 13px;
        padding: 2px 8px;
        border-radius: 3px;
    }

    .exam-form-box select.form-control {
        padding-top: 2px;
        padding-bottom: 2px;
    }

    .exam-form-box input[type="file"].form-control {
        padding: 1px 6px;
        line-height: 20px;
    }

    .exam-form-box .form-group {
        margin-bottom: 11px;
    }

    .schedule-btn {
        min-width: 85px;
        height: 31px;
        padding: 4px 18px;
        font-size: 13px;
        border-radius: 3px;
    }

    .text-danger {
        color: #ff3b30 !important;
    }
    .exam-form-box {
    background: #fff;
    padding: 15px 12px 12px 12px;
}

.exam-form-box label {
    font-size: 13px;
    font-weight: 600;
    color: #222;
    margin-bottom: 6px;
}

.exam-form-box .form-control {
    height: 25px;
    font-size: 13px;
    padding: 2px 8px;
    border-radius: 3px;
}

.exam-form-box input[type="file"].form-control {
    padding: 1px 6px;
    line-height: 20px;
}

.exam-form-box .form-group {
    margin-bottom: 11px;
}

.custom {
    padding-top: 5px;
}

.exam-form-box .form-control {
    height: 40px !important;
    border-radius: 5px !important;
    border: 1px solid #D9D9D9 !important;
    font-size: 14px;
    padding: 8px 12px;
    box-shadow: none !important;
}

.exam-form-box .form-control:focus {
    border-color: #D9D9D9 !important;
    box-shadow: none !important;
}

.exam-form-box input[type="file"].form-control {
    height: 40px !important;
    padding: 7px 12px;
}
</style>

<div class="container-fluid exam-form-box">

    <div class="form-row custom">
        <?php if($adm_sch_id !='' ){  ?>

            <div class="form-group col-md-6">
                <label>Assessment:</label>
                <select class="form-control"
                        style="width: 100%!important;"
                        onclick="getadmval('<?=$adm_sch_id?>')"
                        id="assessment_id"
                        name="assessment_id">
                    <option value="<?=$adm_sch_id?>"><?=$adm_sch_name[0]->admission_sch_name?></option>
                </select>
            </div>

        <?php }else{ ?>

            <div class="form-group col-md-6">
                <label>Assessment:</label>
                <select class="form-control"
                        style="width: 100%!important;"
                        onchange="getval(this,'<?=$co_id?>')"
                        id="assessment_id"
                        name="assessment_id">
                    <option value="">--Select Assessment--</option>
                    @foreach($course_criteria as $rec_course_criteria)
                        <option value="{{$rec_course_criteria->assessment_id}}">{{ $rec_course_criteria->assessment_name}}</option>
                    @endforeach
                </select>
            </div>

        <?php } ?>

        <div class="form-group col-md-6">
            <label>Assessment No:<b class="text-danger">*</b></label>
            <input type="text"
                   style="width: 100%!important;"
                   class="form-control"
                   id="assessment_no"
                   name="assessment_no"
                   readonly>
        </div>
    </div>

    <div class="form-row custom">
        <div class="form-group col-md-6">
            <label>Description:<b class="text-danger">*</b></label>
            <input type="text"
                   style="width: 100%!important;"
                   class="form-control exam_input"
                   id="exam_name"
                   maxlength="100"
                   name="exam_name">
        </div>

        <div class="form-group col-md-6">
            <label>Total Questions:<b class="text-danger">*</b></label>
            <input type="number"
                   min="0"
                   pattern="/^-?\d+\.?\d*$/"
                   onKeyPress="if(this.value.length==4) return false;"
                   style="width: 100%!important;"
                   class="form-control exam_input"
                   id="total_question"
                   name="total_question">
        </div>
    </div>

    <div class="form-row custom">
        <div class="form-group col-md-6">
            <label>Due Date:<b class="text-danger">*</b></label>
            <input type="text"
                   style="width: 100%!important;"
                   class="form-control exam_input assess-form-input optional-inputs cms-datePicker"
                   id="duedate"
                   name="duedate"
                   maxlength="10">
        </div>

        <div class="form-group col-md-6">
            <label>Start Time:<b class="text-danger">*</b></label>
            <input type="time"
                   style="width: 100%!important;"
                   class="form-control exam_input assess-form-input optional-inputs"
                   id="duetime"
                   name="duetime"
                   placeholder="Due Time">
        </div>
    </div>

    <div class="form-row custom">
        <div class="form-group col-md-6">
            <label>Duration (In Minutes)<b class="text-danger">*</b></label>
            <input type="number"
                   style="width: 100%!important;"
                   class="form-control exam_input assess-form-input"
                   id="duration"
                   min="0"
                   pattern="/^-?\d+\.?\d*$/"
                   onKeyPress="if(this.value.length==4) return false;"
                   name="duration">
        </div>

        <div class="form-group col-md-6">
            <label>File<b class="text-danger"> (Optional)</b></label>
            <input type="file"
                   style="width: 100%!important;"
                   class="form-control"
                   name="uploadfile"
                   id="uploadfile">

            <div style="display:none;padding-top: 6px;width:159px !important;" id="download_div">
                <a id="exam_file"
                   style="text-decoration: none !important;color:black"
                   target="_blank">
                    {{-- <img src="{{asset('images/download.png')}}" width="100px"> --}}
                    <p style="display: flex;
height: 44px;
padding: 0 24px;
align-items: center;border-radius: 8px;
background: #17675F;color:#fff;color: #FFF;
font-family: Montserrat;
font-size: 14px;
font-style: normal;
font-weight: 600;
line-height: normal;">Download Now</p>
                </a>
            </div>
        </div>
    </div>

    <div class="form-row custom" style="display:none;">
        <div class="form-group col-md-6">
            <label>Pass Percentage<b class="text-danger">*</b></label>
            <input type="text"
                   style="width: 100%!important;"
                   class="form-control"
                   id="percentage_status"
                   name="percentage_status">
        </div>

        <div class="form-group col-md-6">
            <label>Paper Pattern</label>
            <select class="form-control assess-form-input"
                    style="width: 100%!important;"
                    id="papper_pattern"
                    name="papper_pattern">
                <option value="R">Random Order</option>
                <option value="F">Fixed Order</option>
            </select>
        </div>
    </div>

    <div class="form-row custom" style="display:none;">
        <div class="form-group col-md-6">
            <label>Pass Percentage<b class="text-danger">*</b></label>
            <input type="text"
                   style="width: 100%!important;"
                   class="form-control"
                   id="percentage_status"
                   name="percentage_status">
        </div>

        <div class="form-group col-md-6">
            <label>Paper Pattern</label>
            <select class="form-control assess-form-input"
                    style="width: 100%!important;"
                    id="papper_pattern"
                    name="papper_pattern">
                <option value="R">Random Order</option>
                <option value="F">Fixed Order</option>
            </select>
        </div>
    </div>

    <div class="form-row custom" style="margin-top:20px;">
        <div class="col-12">
            <button style="padding-left:25px;padding-right:25px;background-color:#1F776E !important;border-color:#1F776E !important"
                    type="button"
                    class="btn btn-primary"
                    onclick="insertcreatexam()">
                Schedule
            </button>
        </div>
    </div>

    <div class="form-row custom" style="">
        <div class="col-12">
            <div id="edit-fail-msg" class="msg-div" style="color: red;float:left">
                <span></span>
            </div>
            <div id="edit-success-msg" class="succ-msg-div" style="color: green;float:left">
                <span></span>
            </div>
        </div>
    </div>

</div>
        </form> 
    </div>
</div>
<style type="text/css">
    
.blinking{
    animation:blinkingText 1.2s infinite;
   
}
@keyframes blinkingText{
    0%{     color: #FFF;    }
    49%{    color: #FFF; }
    60%{    color: transparent; }
    99%{    color:transparent;  }
    100%{   color: #FFF;    }
}
.cards {
   // padding-top: 2%;
    // border: .5px solid black;
    // padding-bottom: 2%;
 }
 .pic-container {
    width: 100%;
    height: auto;
    /* overflow-y: scroll;
    overflow-x:hidden;
    scrollbar-color: #D2D6F1 white;
     scrollbar-width: thin; */
}


/* Advance Settings Modal */
#advancemodel .modal-content {
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.18);
}

/* Header */
#advancemodel .modal-header {
    background-color: #1F776E !important;
    border-bottom: none;
    padding: 16px 20px;
}

#advancemodel .modal-title {
    color: #fff !important;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

/* Close X */
#advancemodel .modal-header .close {
    color: #fff !important;
    opacity: 1;
    text-shadow: none;
    font-size: 24px;
    font-weight: 400;
}

#advancemodel .modal-header .close:hover {
    color: #fff !important;
    opacity: 0.8;
}

/* Body */
#advancemodel .modal-body {
    padding: 22px 20px;
    background-color: #fff;
}

/* Each setting row */
#advancemodel .modal-body .row {
    align-items: center;
    margin-bottom: 12px !important;
}

/* Labels */
#advancemodel .modal-body .col-4 {
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

/* Select container */
#advancemodel .modal-body .col-3 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
}

/* Select fields */
#advancemodel .form-control {
    width: 100% !important;
    height: 40px;
    border: 1px solid #D9D9D9;
    border-radius: 5px;
    font-size: 14px;
    color: #444;
    padding: 6px 12px;
    box-shadow: none !important;
    background-color: #fff;
}

/* Focus */
#advancemodel .form-control:focus {
    border-color: #1F776E !important;
    box-shadow: 0 0 0 2px rgba(31, 119, 110, 0.12) !important;
}

/* Footer */
#advancemodel .modal-footer {
    border-top: 1px solid #eeeeee;
    padding: 15px 20px;
    background-color: #fff;
}

/* Common footer button styling */
#advancemodel .modal-footer .btn {
    min-width: 95px;
    height: 38px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: none !important;
}

/* Close button */
#advancemodel .modal-footer .btn-secondary {
    background-color: #fff !important;
    border: 1px solid #1F776E !important;
    color: #1F776E !important;
}

#advancemodel .modal-footer .btn-secondary:hover,
#advancemodel .modal-footer .btn-secondary:focus {
    background-color: #eef7f6 !important;
    border-color: #1F776E !important;
    color: #1F776E !important;
}

/* Save Changes */
#advancemodel .modal-footer .btn-primary {
    background-color: #1F776E !important;
    border-color: #1F776E !important;
    color: #fff !important;
}

#advancemodel .modal-footer .btn-primary:hover,
#advancemodel .modal-footer .btn-primary:focus {
    background-color: #195f58 !important;
    border-color: #195f58 !important;
    color: #fff !important;
}

/* Success / Error messages */
#advancemodel #saved_msg_div_file {
    color: #1F776E !important;
    font-size: 13px;
    font-weight: 500;
    margin-right: auto;
}

#advancemodel #saved_msg_div_file_error {
    color: #dc3545 !important;
    font-size: 13px;
    font-weight: 500;
    margin-right: auto;
}








/* =========================================================
   ONLINE EXAM — FIGMA STYLE OVERRIDE
   Figma frame: Online Exam-1 (505:10)

   STYLE ONLY
   Existing PHP / Blade / AJAX / JS remains unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

:root {
    --exam-green: #1F776E;
    --exam-green-alt: #17675F;

    --exam-text: #252525;
    --exam-dark: #333333;
    --exam-muted: #555555;

    --exam-border: #D9D9D9;
    --exam-divider: #E0E0E0;

    --exam-soft-green: rgba(31, 119, 110, 0.10);

    --exam-success: #10B981;
    --exam-warning: #FFBA49;
    --exam-disabled: #D9D9D9;

    --exam-white: #FFFFFF;
}


/* =========================================================
   2. FONT
   ========================================================= */

#exam_form,
#exam_form *,
.exam-schedule-table,
.exam-schedule-table *,
.cards,
.cards *,
.profile-overview-title {
    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   3. PAGE TITLE
   Current Blade h4 contains course name.
   ::before adds Figma "Online Exam" heading without HTML edit.
   ========================================================= */

.profile-overview-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}


.profile-overview-title::before {
    content: "Online Exam";

    display: block;

    margin-bottom: 6px;

    font-family: 'Montserrat', sans-serif;

    font-size: 20px;
    font-weight: 600;
    line-height: 1.2;

    color: #252525;
}


/* =========================================================
   4. QUESTION BANK
   Figma = 18px Medium underline
   ========================================================= */

a[href*="teacher/quiz_question"][href*="from_link=yes"] {
    display: inline-block !important;

    margin-top: 29px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #252525 !important;

    text-decoration: underline !important;

    text-underline-offset: 2px;
}


a[href*="teacher/quiz_question"][href*="from_link=yes"] b {
    font-weight: 500 !important;
}


/* =========================================================
   5. MAIN FORM CARD
   Figma:
   white
   radius 5px
   approx 446px height
   ========================================================= */

.card:has(> .card-body > #exam_form) {
    clear: both !important;

    width: 100% !important;

    min-height: 446px !important;

    margin-top: 24px !important;
    margin-bottom: 30px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


.card:has(> .card-body > #exam_form) > .card-body {
    padding: 0 !important;

    background: #FFFFFF !important;
}


/* =========================================================
   6. FORM CONTAINER
   The padding values reproduce Figma's:
   20px outside edge + approx 27px column gap
   ========================================================= */

#exam_form {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;
}


#exam_form .exam-form-box {
    width: 100% !important;

    min-height: 446px !important;

    padding: 20px 7px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


/* =========================================================
   7. TWO COLUMN FIGMA GRID
   ========================================================= */

#exam_form .exam-form-box > .form-row {
    width: 100% !important;

    margin-left: 0 !important;
    margin-right: 0 !important;

    padding: 0 !important;
}


#exam_form .exam-form-box > .form-row + .form-row {
    margin-top: 20px !important;
}


#exam_form .form-group.col-md-6 {
    padding-left: 13.5px !important;
    padding-right: 13.5px !important;

    margin-bottom: 0 !important;
}


/* =========================================================
   8. LABELS
   Figma = Montserrat Medium 16
   ========================================================= */

#exam_form .exam-form-box label {
    display: block !important;

    margin: 0 0 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #000000 !important;
}


/*
   Figma required stars and "(Optional)"
   use the same label color.
*/
#exam_form .exam-form-box label .text-danger,
#exam_form .exam-form-box label b.text-danger {
    color: #000000 !important;

    font-size: inherit !important;
    font-weight: 500 !important;
}


/* =========================================================
   9. INPUTS / SELECT
   Figma = 40px / radius 5 / #D9D9D9
   ========================================================= */

#exam_form .exam-form-box .form-control {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 5px !important;

    background-color: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    transition:
        border-color .15s ease,
        box-shadow .15s ease !important;
}


#exam_form .exam-form-box .form-control:focus {
    outline: none !important;

    border-color: #1F776E !important;

    box-shadow: 0 0 0 3px rgba(31, 119, 110, .08) !important;
}


#exam_form .exam-form-box .form-control::placeholder {
    color: #9F9F9F !important;

    opacity: 1;
}


/* Read-only Assessment No */
#assessment_no[readonly] {
    background: #FFFFFF !important;

    color: #252525 !important;
}


/* =========================================================
   10. SELECT
   ========================================================= */

#assessment_id {
    height: 40px !important;

    padding-left: 12px !important;
    padding-right: 36px !important;

    background-color: #FFFFFF !important;

    cursor: pointer;
}


/* =========================================================
   11. FILE INPUT
   Figma Choose File:
   110 x 29
   ========================================================= */

#uploadfile {
    height: 40px !important;

    padding: 5px !important;

    font-size: 14px !important;

    color: #9F9F9F !important;
}


#uploadfile::file-selector-button {
    width: 110px;
    height: 29px;

    margin-right: 10px;
    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #000000;

    cursor: pointer;
}


/* Older Chromium/WebKit */
#uploadfile::-webkit-file-upload-button {
    width: 110px;
    height: 29px;

    margin-right: 10px;
    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #000000;

    cursor: pointer;
}


/* Existing file download */
#download_div {
    margin-top: 7px !important;
}


#exam_file img {
    width: auto !important;
    height: 28px !important;

    object-fit: contain !important;
}


/* =========================================================
   12. SCHEDULE BUTTON
   Figma = 136 x 50
   ========================================================= */

#exam_form .exam-form-box
.form-row[style*="margin-top:20px"]
.col-12 {
    padding-left: 13.5px !important;
    padding-right: 13.5px !important;
}


#exam_form .exam-form-box
button[onclick="insertcreatexam()"] {
    width: 136px !important;
    min-width: 136px !important;

    height: 50px !important;
    min-height: 50px !important;

    padding: 0 18px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #1F776E !important;

    border: 1px solid #1F776E !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;

    text-align: center !important;

    transition: background-color .15s ease !important;
}


#exam_form .exam-form-box
button[onclick="insertcreatexam()"]:hover,
#exam_form .exam-form-box
button[onclick="insertcreatexam()"]:focus {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;

    box-shadow: none !important;

    transform: none !important;
}


/* =========================================================
   13. VALIDATION MESSAGES
   ========================================================= */

#edit-fail-msg,
#edit-success-msg {
    margin-top: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 400 !important;

    line-height: 1.4 !important;
}


#edit-fail-msg {
    color: #E24D4F !important;
}


#edit-success-msg {
    color: #10B981 !important;
}


/* =========================================================
   14. SCHEDULED EXAMS MAIN HEADER
   Figma = 56px #1F776E
   ========================================================= */

.card-header[style*="margin-bottom:15px"] {
    width: 100% !important;

    min-height: 56px !important;
    height: 56px !important;

    margin-top: 30px !important;
    margin-bottom: 15px !important;

    padding: 0 20px !important;

    display: flex !important;

    align-items: center !important;
    justify-content: space-between !important;

    background: #1F776E !important;

    border: 0 !important;
    border-radius: 5px 5px 0 0 !important;

    box-shadow: none !important;

    color: #FFFFFF !important;
}


.card-header[style*="margin-bottom:15px"] h3 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;
}


/* Optional Applicants Report */
.card-header[style*="margin-bottom:15px"]
.btn-light {
    height: 36px !important;

    padding: 0 14px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 4px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #1F776E !important;

    box-shadow: none !important;
}


/* =========================================================
   15. EACH SCHEDULED EXAM CARD
   ========================================================= */

.cards {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 0 0 5px 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


/* =========================================================
   16. ASSESSMENT STRIP
   Figma = 56px / 10% green
   ========================================================= */

.cards > div:first-child {
    width: 100% !important;

    min-height: 56px !important;

    padding: 7px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: rgba(31, 119, 110, 0.10) !important;

    border: 0 !important;

    color: #333333 !important;
}


/* Assessment left side */
.cards > div:first-child > div:first-child {
    display: flex !important;

    align-items: center !important;

    gap: 5px !important;
}


.cards > div:first-child > div:first-child span {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #333333 !important;
}


/* Quiz (1) */
.cards > div:first-child
span[style*="#078282"] {
    color: #01A46D !important;
}


/* Right action group */
.cards > div:first-child > div:last-child {
    display: flex !important;

    align-items: center !important;

    gap: 8px !important;

    margin-left: auto !important;
}


/* =========================================================
   17. SECONDARY ACTION BUTTONS
   View Result / Final Submitted / Move to CMS
   ========================================================= */

.cards > div:first-child > div:last-child .btn {
    min-width: auto !important;

    height: 42px !important;

    margin: 0 !important;

    padding: 0 24px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    border: 0 !important;
    border-radius: 4px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    line-height: 1 !important;

    transform: none !important;
}


/* Final Submitted */
.cards > div:first-child .btn-success {
    background: #10B981 !important;

    color: #FFFFFF !important;
}


/* View Result */
.cards > div:first-child .btn-dark {
    background: #D9D9D9 !important;

    color: #252525 !important;
}


/* Move To CMS */
.cards > div:first-child .btn-warning {
    background: #FFBA49 !important;

    color: #252525 !important;
}


/* Other extra current functionality */
.cards > div:first-child .btn-primary {
    background: #1F776E !important;

    color: #FFFFFF !important;
}


/*
   Figma Final Submitted is visually full opacity
   even though current Blade button is disabled.
*/
.cards > div:first-child .btn:disabled {
    opacity: 1 !important;
}


/* =========================================================
   18. EXAM TABLE BODY WRAPPER
   Figma = 32px top + 20px horizontal
   ========================================================= */

#table_div {
    padding: 32px 20px 32px !important;

    background: #FFFFFF !important;
}


/* =========================================================
   19. EXAM TABLE
   ========================================================= */

#table_div .exam-schedule-table {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-collapse: collapse !important;

    table-layout: fixed;
}


/* =========================================================
   20. TABLE HEADERS
   Figma = 14px SemiBold / 41px
   ========================================================= */

#table_div .exam-schedule-table thead tr {
    height: 41px !important;
}


#table_div .exam-schedule-table thead td {
    height: 41px !important;

    padding: 12px 8px !important;

    vertical-align: middle !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E0E0E0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #555555 !important;

    text-align: center !important;
}


#table_div .exam-schedule-table thead td b {
    font-weight: 600 !important;
}


/* =========================================================
   21. TABLE DATA ROW
   Figma = 49px / 14px Regular
   ========================================================= */

#table_div
.exam-schedule-table
tbody
tr:first-child {
    height: 49px !important;
}


#table_div
.exam-schedule-table
tbody
tr:first-child td {
    height: 49px !important;

    padding: 16px 8px !important;

    vertical-align: middle !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E0E0E0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;

    color: #333333 !important;

    text-align: center !important;
}


/* Student count */
#table_div .exam-student-count {
    padding: 0 !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #333333 !important;
}


/* =========================================================
   22. ACTION BUTTON ROW
   Figma:
   24px top gap
   42px buttons
   radius 6
   ========================================================= */

#table_div
.exam-schedule-table
tbody
tr:nth-child(2) td {
    padding: 24px 8px 0 !important;

    vertical-align: middle !important;

    background: #FFFFFF !important;

    border: 0 !important;

    text-align: center !important;
}


/* Exact action language */
#table_div .exam-action-btn {
    width: 100% !important;
    min-width: 0 !important;

    height: 42px !important;
    min-height: 42px !important;

    margin: 0 !important;
    padding: 0 10px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #1F776E !important;

    border: 1px solid #1F776E !important;
    border-radius: 6px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.15 !important;

    color: #FFFFFF !important;

    text-align: center !important;

    white-space: normal !important;

    transition: background-color .15s ease !important;
}


#table_div .exam-action-btn:hover,
#table_div .exam-action-btn:focus {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;

    box-shadow: none !important;

    transform: none !important;
}


/* Preserve disabled functionality */
#table_div .exam-action-btn:disabled,
#table_div .exam-action-btn.disabled {
    background: #1F776E !important;

    border-color: #1F776E !important;

    color: #FFFFFF !important;

    opacity: .55 !important;
}


/* Existing View Student / Applicant wrapper */
#table_div .exam-student-btn-wrap {
    padding: 0 !important;
}


/* =========================================================
   23. FINAL SUBMIT / DISCLAIMER TABLE
   Not part of visible Figma state,
   so functionality remains and style is kept minimal.
   ========================================================= */

#table_div > .table.table-striped {
    margin: 16px 0 0 !important;

    background: transparent !important;

    border: 0 !important;
}


#table_div > .table.table-striped td {
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
}


#table_div > .table.table-striped .btn {
    min-height: 42px !important;

    padding: 0 18px !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;
}


/* =========================================================
   24. NO EXAMS STATE
   ========================================================= */

.pic-container {
    width: 100% !important;

    min-height: 150px !important;

    margin: 0 !important;
    padding: 30px 20px !important;

    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    color: #7A7A7A !important;
}


/* =========================================================
   25. LINKS INSIDE TABLE
   ========================================================= */

#table_div a {
    text-decoration: none !important;
}


/* =========================================================
   26. RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    #exam_form .form-group.col-md-6 {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }


    #table_div {
        overflow-x: auto !important;
    }


    #table_div .exam-schedule-table {
        min-width: 900px !important;
    }

}


@media (max-width: 767px) {

    .profile-overview-title {
        font-size: 16px !important;
    }


    .profile-overview-title::before {
        font-size: 20px !important;
    }


    a[href*="teacher/quiz_question"][href*="from_link=yes"] {
        margin-top: 8px !important;

        font-size: 14px !important;
    }


    .card:has(> .card-body > #exam_form) {
        min-height: auto !important;

        margin-top: 20px !important;
    }


    #exam_form .exam-form-box {
        min-height: auto !important;

        padding: 16px 6px !important;
    }


    #exam_form .form-group.col-md-6 {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }


    #exam_form .exam-form-box > .form-row + .form-row {
        margin-top: 0 !important;
    }


    #exam_form .form-group {
        margin-bottom: 18px !important;
    }


    #exam_form .exam-form-box
    button[onclick="insertcreatexam()"] {
        width: 100% !important;
    }


    .cards > div:first-child {
        flex-direction: column !important;

        align-items: flex-start !important;

        gap: 12px !important;

        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }


    .cards > div:first-child > div:last-child {
        width: 100% !important;

        margin-left: 0 !important;

        flex-wrap: wrap !important;
    }


    .cards > div:first-child > div:last-child .btn {
        flex: 1 1 auto;
    }


    #table_div {
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

}
/* Assessment Select */
#exam_form .select2-container {
    width: 100% !important;
}

#exam_form .select2-selection--single {
    height: 40px !important;
    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;
}

#exam_form .select2-selection__rendered {
    line-height: 38px !important;
    padding-left: 12px !important;
    color: #252525 !important;
}

#exam_form .select2-selection__arrow {
    height: 38px !important;
}

/* Hover and active option */
.exam-assessment-dropdown
.select2-results__option--highlighted[aria-selected],
.exam-assessment-dropdown
.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}
.btn-primary,.btn-primary:hover,.btn-primary:focus,.btn-primary:active,.btn-primary.active,.open>.dropdown-toggle.btn-primary {
    background-color: #1f776e !important;
    border-color: #1f776e !important;
}
</style>
<!-- Print Table Start Here  -->


<!-- <div class="card pic-container">      -->
    <div class="card-header" style="background-color:#1F776E !important; color:#FFF;margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="display: inline-block; margin-bottom: 0;">Scheduled Exams</h3>
            @if(!empty($adm_sch_id))
                <a href="{{ route('teacher/applicant_list_report', ['adm_sch_id' => $adm_sch_id]) }}" class="btn btn-light btn-sm">
                    Applicants List Report
                </a>
            @endif
    </div>

    <?php if (count($result) > 0): ?>
    <?php
        // Assuming $result contains the data and exam_paper_admission_sch_id is an array
        $examPaperAdmissionSchId = is_array($result[0]->exam_paper_admission_sch_id)
            ? $result[0]->exam_paper_admission_sch_id[0]
            : $result[0]->exam_paper_admission_sch_id;

        // Construct the full URL
        $baseUrl = request()->getSchemeAndHttpHost();
        $fullUrl = $baseUrl . '/assets/cms/reports/meritListReport.php?id=' . urlencode($examPaperAdmissionSchId);
    ?>

    <div class="row mr-4">
        <div class="col text-right">
            {{-- <a target="_blank" href="<?php echo htmlspecialchars($fullUrl); ?>" class="btn btn-primary">
                Merit List Report
            </a> --}}
        </div>
    </div>
    <?php endif; ?>


@if(count($result)>0)
@foreach($result as $question_bank_rec)  

<div class="card cards">     
    <!-- <div class="card-header" style="background-color:#D2D6F1 !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Details </h4>
             <h3 style="float: right;" class="blinking">Disclaimer :Final submit is mandatory to start the exam at student side </h4>
    </div> -->

    {{-- <div style="background-color: #E7EBE0FF;padding: 10px;color: #00203FFF"> <span style="font-size: 16px;font-weight: 600" >Assessment : </span> 

    @if (!empty($course_criteria))
            @foreach($course_criteria as $rec_course_criteria)
                    @if($rec_course_criteria->id==$question_bank_rec->course_criteria_id)
                        <span style="font-size: 16px;font-weight: 600;color: #078282FF"> {{ $rec_course_criteria->assessment_name}} ( {{ $question_bank_rec->assessment_no}} )
                        </span>
                    @endif
                @endforeach
            @endif

    
    
    </div> --}}

    <div style="background-color: #E7EBE0FF; padding: 10px; color: #00203FFF; display:flex; justify-content:space-between; align-items:center;">
    
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <span style="font-size: 16px;font-weight: 600">Assessment : </span>

        @if (!empty($course_criteria))
            @foreach($course_criteria as $rec_course_criteria)
                @if($rec_course_criteria->id==$question_bank_rec->course_criteria_id)
                    <span style="font-size: 16px;font-weight: 600;color: #078282FF">
                        {{ $rec_course_criteria->assessment_name}} ( {{ $question_bank_rec->assessment_no}} )
                    </span>
                @endif
            @endforeach
        @endif
    </div>

    <div style="display:flex; gap:6px; align-items:center; margin-left:auto;">
        @if($adm_sch_id)
        <a href="{{ route('teacher/transfer_exam_questions', ['target_paper_id' => $question_bank_rec->id, 'target_sch_id' => $question_bank_rec->exam_paper_admission_sch_id]) }}">
            <button type="button" class="btn btn-primary btn-sm">Question Bank Transfer</button>
        </a>
        @endif
        <?php 
            $exam_id = my_simple_crypt($question_bank_rec->id, 'e');

            if($question_bank_rec->final_submit != 0){ 
        ?>

            <button disabled
                    type="button"
                    data-toggle="modal"
                    data-target="#ModalCenter4"
                    onclick="finalexam('{{$question_bank_rec->id}}')"
                    class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?>">
                Final <?=(1 == $question_bank_rec->final_submit) ? 'Submitted' : 'Submit';?>
            </button>

            <button <?=($question_bank_rec->student_paper > 0) ? '' : 'disabled';?>
                    type="button"
                    class="btn btn-dark"
                    onclick="window.location.href='{{route('viewstudentresult')}}/<?=$exam_id?>';">
                View Result
            </button>

            <?php if($question_bank_rec->move_to_cms == 'No'){ ?>

                <button type="button"
                        <?=($question_bank_rec->student_paper > 0) ? '' : 'disabled';?>
                        class="btn btn-warning"
                        onclick="move_to_cms(<?=$question_bank_rec->id;?>,<?=$question_bank_rec->co_id;?>,<?=$question_bank_rec->assessment_id;?>)">
                    Move to CMS
                </button>

            <?php } else { ?>

                <?php
                    $criteria = '';
                    foreach($criteria_detail as $rec_criteria){
                        if($rec_criteria->id == $question_bank_rec->move_to_cms){
                ?>

                    <button type="button" class="btn btn-success">
                        {{$rec_criteria->ccd_details}}
                    </button>

                <?php 
                        }
                    }
                ?>

            <?php } ?>

        <?php } ?>
    </div>

</div>
    
    <div class="card-body" id="table_div">

{{-- <table class="table table-striped"> --}}
    <table class="table exam-schedule-table">
  <thead>
    
    <tr>    
     
      <td align="center"><b>Exam Name</b></td>
      <td align="center"><b>Questions</b></td>
      <td style="display:none;" align="center"><b>Pass Percentage</b></td>
      <td style="display:none;" align="center"><b>Exam Pattern</b></td>
      <td align="center"><b>Exam Date</b></td>
      <td align="center"><b>Exam Start Time</b></td>
      <td align="center"><b>Exam Duration</b></td>
      <td align="center" class="exam-student-col"><b>{{ $adm_sch_id ? 'Total Applicant' : 'Total Student' }}</b></td>
      </tr>
     
  </thead>
  <tbody>
    <tr >
       
      <td align="center">{{$question_bank_rec->title}}</td>
      <td align="center">{{$question_bank_rec->total_questions}}</td>
      <td style="display:none;"> <input type="text" name="total_ques" id="total_ques<?=$question_bank_rec->id;?>" value=" {{$question_bank_rec->total_questions}}" >

        <td style="display:none;"> <input type="text" name="total_exam_ques" id="total_exam_ques<?=$question_bank_rec->id;?>" value=" {{$question_bank_rec->student_exam_question}}" >
      <td style="display:none;" align="center">{{$question_bank_rec->percentage_status}}</td>
      <td style="display:none;">{{$question_bank_rec->exam_pattern}}</td>
      <td align="center"><?php echo substr($question_bank_rec->exam_date,0,11); ?></td>
      <td align="center"><?php echo substr($question_bank_rec->exam_start_time,11,10);?></td>
       <td style="display:none;"> <input type="text" name="exam_time" id="exam_time<?=$question_bank_rec->id;?>" value=" <?php echo substr($question_bank_rec->exam_start_time,11,10);?>" >
    </td>
    <td align="center">{{$question_bank_rec->exam_duration}}</td>
    <td align="center" class="exam-student-col">
        <div class="exam-student-count">{{ $adm_sch_id ? ($total_applicants ?? 0) : ($total_students ?? 0) }}</div>
    </td>
    </tr>
<tr>
      
      <td align="center"><button type="button" class="btn btn-primary exam-action-btn" onclick="updtcreatexam('{{$question_bank_rec->id}}')">Edit Schedule</button></td> 
      <td align="center"><a href="{{route('teacher/quiz_question')}}/?sch_id=<?=$question_bank_rec->exam_paper_admission_sch_id;?>&co_id=<?=$course_id?>&id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary exam-action-btn" >Add Question</button></a></td>
      
      <td align="center">
        @if($question_bank_rec->co_id == 0)
       
        <a href="{{ route('teacher/quiz_0_marks', ['co_id' => 0]) }}?id={{ $question_bank_rec->id }}&sch_id={{ $question_bank_rec->exam_paper_admission_sch_id }}">
            <button type="button" class="btn btn-primary exam-action-btn">Marks Management</button>
        </a>

        @else
            <a href="{{ route('teacher/quiz_marks', ['co_id' => $question_bank_rec->co_id, 'id' => $question_bank_rec->id, 'sch_id' => $question_bank_rec->exam_paper_admission_sch_id]) }}">
                <button type="button" class="btn btn-primary exam-action-btn">Marks Management</button>
            </a>
        @endif
    </td>      
     
        <td align="center"><button  type="button"  onclick="advancesetting('{{$question_bank_rec->id}}','{{$question_bank_rec->negative_marks}}','{{$question_bank_rec->paper_attmp_type}}','{{$question_bank_rec->re_attempt}}','{{$question_bank_rec->re_view}}','{{$question_bank_rec->copy_paste_allowed}}','{{$question_bank_rec->exam_answer_file}}','<?=$co_id?>')" class="btn btn-primary exam-action-btn">Instructor Control</button></a></td> 
     
      <?php if($question_bank_rec->paper_attmp_type == 'all_in_one' or $question_bank_rec->paper_attmp_type == ''){?>
      <td style="display:none;" align="center"><a href="{{route('teacher/time_management')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary exam-action-btn" disabled>Time Management</button></a></td> 
      <?php }else{ ?>
        <td style="display:none;" align="center"><a href="{{route('teacher/time_management')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary exam-action-btn">Time Management</button></a></td> 
      <?php } ?>
      <?php if($question_bank_rec->student_exam_question > 0){ ?>
      <td align="center"><a href="{{route('teacher/view_paper')}}?co_id=<?=$question_bank_rec->co_id;?>&id=<?=$question_bank_rec->id?>&sch_id=<?=$question_bank_rec->exam_paper_admission_sch_id;?>" ><button type="button" class="btn btn-primary exam-action-btn" >View Paper</button></a></td>
      <td align="center" class="exam-student-col">
        <div class="exam-student-btn-wrap">
            @if($adm_sch_id)
            <a href="{{ route('teacher/exam_participants', ['type' => 'applicants', 'id' => $adm_sch_id]) }}"><button type="button" class="btn btn-primary exam-action-btn">View Applicant</button></a>
            @elseif($question_bank_rec->co_id != 0)
            <a href="{{ route('teacher/exam_participants', ['type' => 'students', 'id' => $co_id, 'course_id' => $co_id]) }}"><button type="button" class="btn btn-primary exam-action-btn">View Student</button></a>
            @endif
        </div>
      </td>
      <?php }else{ ?>
<td align="center"><a href="{{route('teacher/view_paper')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button disabled type="button" class="btn btn-primary exam-action-btn" >View Paper</button></a></td>
      <td align="center" class="exam-student-col">
        <div class="exam-student-btn-wrap">
            @if($adm_sch_id)
            <a href="{{ route('teacher/exam_participants', ['type' => 'applicants', 'id' => $adm_sch_id]) }}"><button type="button" class="btn btn-primary exam-action-btn">View Applicant</button></a>
            @elseif($question_bank_rec->co_id != 0)
            <a href="{{ route('teacher/exam_participants', ['type' => 'students', 'id' => $co_id, 'course_id' => $co_id]) }}"><button type="button" class="btn btn-primary exam-action-btn">View Student</button></a>
            @endif
        </div>
      </td>
      <?php } ?>
      </tr>
      </tbody>
</table>
<table class="table table-striped">
<tbody>
<!-- window.open( "{{URL::to('library/get-user-detail-pdf?username=')}}"+username, '_blank'); -->
      <tr>
      <?php 
      	$exam_id=my_simple_crypt($question_bank_rec->id, 'e' );
       if($question_bank_rec->final_submit == 0){
      if($question_bank_rec->student_exam_question > 0){?>
     
      <td align="center"><button id="finalsubmited" type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?>" >Disclaimer :Final submit is mandatory to start the exam at student side </button></a></td> 
     
          <?php  }else{  ?>
        <td align="center"><button disabled type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?>" >Add number of Questions you entered for Final Submit </button></a></td> 
      <?php }
      }else { ?>
       <td align="center">
            <button disabled style="display: none;"  type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?> " >Final <?=(1 == $question_bank_rec->final_submit) ? 'Submitted' : 'Submit';?> </button></a>
            <button style="display: none;" <?=($question_bank_rec->student_paper > 0) ? '' : 'disabled';?> type="button"  class="btn btn-dark" onclick="window.location.href='{{route('viewstudentresult')}}/<?=$exam_id?>';" >
             View Result
            </button> 
          
            <?php if($question_bank_rec->move_to_cms == 'No'){?>
        <button style="display: none;" type="button"  <?=($question_bank_rec->student_paper > 0) ? '' : 'disabled';?> class="btn btn-warning" onclick="move_to_cms(<?=$question_bank_rec->id;?>,<?=$question_bank_rec->co_id;?>,<?=$question_bank_rec->assessment_id;?>)">Move to CMS</button>
        <?php } else {
         $criteria='';
 foreach($criteria_detail as $rec_criteria){
  if( $rec_criteria->id  == $question_bank_rec->move_to_cms){ ?>

        <button  type="button" class="btn btn-success">{{$rec_criteria->ccd_details}}</button>

<?php } } ?>
      <?php } ?>
            <!-- <button type="button" class="btn btn-warning" >Move to CMS </button> -->
       </td> 
      <?php } ?>
   </tr>

   
  </tbody>
</table>
<!-- </div> -->
</div>
</div>
@endforeach     
@else
<div class="card pic-container"> 
        <!-- No Exam Created Yet -->
        No Exams Scheduled
</div>
@endif
 <!----------  MODEL 4 upload file START---------------------->
    <div class="modal fade" id="ModalCenter4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="
    background: #1f776e;
    color: white;
">
                    <h5 class="modal-title" id="exampleModalLongTitle">Online Exam creation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="
    color: white;
">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                     <div class="modal-body">
                        <h1>Are you sure you want to submit the final exam? </h1>
                        <br>
                        <input type="hidden" id="exam_id" name="exam_id" />
                         <input type="hidden" id="co_id" name="co_id" value="<?=$co_id?>" />
                        <h4 style="display: inline-block;">Exam Start Time :</h4>
                        <h3 style="display: inline-block;" id="time"></h3>
                        
                        <div>
                        <h4 style="display: inline-block;"> Exam Total Question :</h4>
                        <h3 style="display: inline-block;"  id="total"></h3>
                        </div>
                        <div>
                        <h4 style="display: inline-block;"> Exam Total Defined Questions :</h4>
                        <h3 style="display: inline-block;"  id="exam_total"></h3>
                        </div>
                    </div> 
                    <div class="modal-footer">
                    <div id="edit-fail-msg_modal"     class="msg-div_modal" style="display: none;color: red"><span></span></div>
                      <div id="edit-success-msg_modal"  class="succ-msg-div_modal" style="display: none;color: green"><span></span></div>
               
                
                    <button type="button" onclick="closemodal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="final" onclick="finalsubmit()" type="button" class="btn btn-primary upload_btn">
                        <span class="save" >Save changes</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!----------  MODEL 4 END  --------------------->
<!----------  ADVANCE MODEL upload file START---------------------->
<div class="modal fade" id="advancemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="  width: 600px;" role="document">
            <div class="modal-content">
                <div class="modal-header" style="
    background: #1f776e;
    color: white;
">
                    <h5 class="modal-title" id="exampleModalLongTitle">Advance Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="
    color: white;
">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                     <div class="modal-body">
                     <form enctype="multipart/form-data" method="post" name="advance_setting"  id="advance_setting">
                     @csrf
                     <input type="hidden" id="paper_id" name="paper_id" />
                     <div class="row">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Re Attempt</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="re_attempt" name="re_attempt" class="form-control"  style="width:300px;" >
                            <option value="1">Yes</option>
                            <option value="0">No</option>                                             
                            </select>                       
                        </div>
                     </div>
                     <div class="row" style="margin-top:5px;">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">View Exam Result</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="re_view" name="re_view" class="form-control"  style="width:300px;" >
                            <option value="0">No</option>
                            <option value="1">Yes</option>                                             
                            </select>                       
                        </div>
                     </div>
                     <div class="row" style="margin-top:5px;">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Display of Questions</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="exam_paper_type" name="exam_paper_type" class="form-control"  style="width:300px;" >
                            <option value="all_in_one">All in one</option>
                            <option value="one_by_one">One by one</option>                                             
                            </select>                       
                        </div>
                     </div>      
                     <div class="row" style="margin-top:5px;display:none">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Negative Mark <strong>%</strong></div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                        <select name="neg_marks" class="form-control" id="neg_marks"   style="width:300px;" >
                    <option  value="0" >0</option>
                    <option  value=".25" >25</option>
                    <option value=".33" >33</option>
                    <option  value=".50"  >50</option>
                    <option value=".75"  >75</option>
                    <option value="1"  >100</option>
                </select>
                           <!-- <input class="form-control" type="text"  id="neg_marks" name="neg_marks" placeholder="%" style="width:300px;">    -->
                           <input  type="hidden"  id="neg_marks_hidden" name="neg_marks_hidden" style="width:300px;">               
                        </div>
                     </div>  
                     <div class="row" style="margin-top:5px;">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Copy Paste</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="ccp" name="ccp" class="form-control"  style="width:300px;" >
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>                                             
                            </select>                       
                        </div>
                     </div> 
                     <div class="row" style="margin-top:5px;">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">File Required</div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="file_require" name="file_require" class="form-control"  style="width:300px;" >
                            <option value="0">No</option>
                            <option value="1">Yes</option>                                             
                            </select>                       
                        </div>
                     </div>                    

                    </form>
                        </div>
                     
                    <div class="modal-footer">
                    <div id="saved_msg_div_file" style="display: none;color: green"><span>Data Entered Successfully</span></div> 
                    <div id="saved_msg_div_file_error" style="display: none;color: red"><span>You already have enetered negative marks one by one</span></div> 
                   
                    <button type="button" onclick="cancell_ass()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button  onclick="saveadvancesetting()" type="button" class="btn btn-primary upload_btn">
                        <span class="save" >Save changes</span>
                    </button>
                    
                  
                </div>

            </div>
        </div>
    </div>
    <!----------ADVANCE  MODEL  END  --------------------->
    <!-- Move to CMS Model -->
    <div class="modal fade" id="move_to_cms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:700px;" role="document">
        <div class="modal-content">
            <div class="modal-header" style="
    background: #1f776e;
    color: white;
">
                <h5 class="modal-title" id="exampleModalLongTitle">Marks Move To GRADING</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="
    color: white;
">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <form id="move_to_cms_form">
                        {{csrf_field()}}
                        <span style="color: red;">Marks will only move one time.</span>
                        <div class="form-group">
                          <label for="email">Quiz Detail</label>
                          <input type="quiz_detail" class="form-control" style="border: solid 1px black" placeholder="Quiz Detail" id="quiz_detail" name="quiz_detail">
                       <input type="hidden" name="cms_exam_id" id="cms_exam_id">
                       <input type="hidden" name="cms_co_id" id="cms_co_id">
                       <input type="hidden" name="cms_assessment_id" id="cms_assessment_id">
                </div>
                <div class="modal-body" id="model_message" style="display: none;">

                </div>
                <input type="button" onclick="POST_move_to_cms()" class="btn btn-primary" value="Click to Move to CMS">
                        </form>

                </div>
        </div>
    </div>
</div>
<!-- Move to cms model ends here -->


@stop
@section('javascript')
<script>
$( document ).ready(function() {

    if ($.fn.select2) {
    $('#assessment_id').select2({
        width: '100%',
        minimumResultsForSearch: Infinity,
        dropdownCssClass: 'exam-assessment-dropdown'
    });
}


var adm_sch=<?=$adm_sch_id;?>

   getadmval(adm_sch);

});
function move_to_cms(exam_id,co_id,assessment_id){
    $('#move_to_cms').modal('show');
    $("#cms_exam_id").val(exam_id);
    $("#cms_co_id").val(co_id);
    $("#cms_assessment_id").val(assessment_id);
    $("#quiz_detail").val('');
}
function  POST_move_to_cms(){
       $("#model_message").show();
       if($.trim($('#quiz_detail').val()) == '')
        {
            $('#quiz_detail').css("border-color", "red");
            $('#quiz_detail').focus();
            $("#model_message").html("Enter Details please");
        }else{
        var myform = document.getElementById("move_to_cms_form");
        var fd = new FormData(myform );
        $.ajax({
            url: '/move_to_cms',
            data: fd,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
             if(data.trim()==1){
                 $("#model_message").html('Marks move to CMS Successfully');
                 setTimeout(function()
                {
                    location.reload();
                },1500);
             }else{
                $("#model_message").html(data);
             }
            }
        });
    }

}
function advancesetting(id,marks,type,reatmp,review,ccp,file_require,co_id){
	if(!co_id){
        co_id=0;
    }
//$('#advancemodel').modal('show');
    $("#paper_id").val(id) ;  
    $("#neg_marks").val(marks) ;
   $("#exam_paper_type").val(type) ;
   $("#re_attempt").val(reatmp) ;
   $("#re_view").val(review) ;
   $("#ccp").val(ccp) ;
   $("#neg_marks_hidden").val(marks) ;
   $("#file_require").val(file_require) ;
   $.ajax({
            type:'get',
            url:'{{url('checkstudentattendance')}}/'+id+'/'+co_id,
            success:function(data){ 
             //   console.log(data)    
             
            // return false; 
                if(data == 1){
                    $('#advancemodel').modal('hide');
                }       else{
                    $('#advancemodel').modal('show');
                }
               
            //$("#assessment_no").val(data['0']['count'])
            }
        });
        
}
function saveadvancesetting(){
 //  alert ('bilal');
   var paper_id = $("#paper_id").val();
            var bi_form = document.querySelector('#advance_setting');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('advancesetting')}}/'+paper_id,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                //alert(data)	
                if(data == 1){
                //    alert('You already have enetered negative marks one by one')
                     $("#saved_msg_div_file_error").show();
        //   $("#simpletable").load(window.location + " #simpletable");
        //   setTimeout(function(){
        //           $("#saved_msg_div_file_error").hide();

        //           }, 2000);
        //           location.reload();
        //             }
                }else{
                    $("#saved_msg_div_file_error").hide();
            $("#saved_msg_div_file").show();
          $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
                  $("#saved_msg_div_file").hide();

                  }, 2000);
                  location.reload();
                }
                },
                complete: function(){
                }
            });
}

function getval(sel,co_id){
    //alert (sel.value+' '+co_id);
    var assess_id = sel.value;
    var co_id = co_id;
      //  return false;
    $.ajax({
            type:'get',
            url:'{{url('getcountassessment')}}/'+assess_id+'/'+co_id,
            success:function(data){          
            $("#assessment_no").val(data['0']['count'])
            }
        });
} 

function getadmval(adm_sch_id){
    //alert (sel.value+' '+adm_sch_id);
    //var assess_id = sel.value;
    var adm_sch_id = adm_sch_id;
      //  return false;
    $.ajax({
            type:'get',
            url:'{{url('getcount_admexam')}}/'+adm_sch_id,
            success:function(data){   
            //alert(data);       
            $("#assessment_no").val(data['0']['count'])
            }
        });
} 

function insertcreatexam(){
    // var downloadPath = '<?=url('/docs/')?>';
    // var filepath = downloadPath+'/'+data['0']['file_name'];
    var id = ($("#id").val());
    var adm_sch_id  =$("#adm_sch_id").val();
//alert(adm_sch_id);
    if(adm_sch_id && id ==''){
    
        if($("#exam_name").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Exam Name!');    
        }else if($("#total_question").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Total Question Number!'); 
        }else if($("#duedate").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Due Date!'); 
        }else if($("#duetime").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Start Time!'); 
        }else if($("#duration").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Duration!'); 
        }else{
            var bi_form = document.querySelector('#exam_form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('insert_admission_exam')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                    $('.msg-div').hide();
                    $('.succ-msg-div').show();
                    $('#edit-success-msg span').html('Data Insert Successfully!');
					location.reload(); 
                    setTimeout(function(){
                        $('.succ-msg-div').hide();                    
                    }, 1800);                   
                    /*
                    $("#exam_name").val('');
                    $("#total_question").val('');
                    $("#duedate").val(''); 
                    $("#duetime").val('');
                    $("#duration").val(''); 
                    */

                    $('.exam_input').val('');

                    $("#assessment_id").val('');
                    $("#assessment_no").val('');
                    $("#table_div").load(window.location + " #table_div");
                    $('.msg-div').hide();
                    $("#uploadfile").val('');
                },
                complete: function(){
                },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
       // alert("Status: " + textStatus); alert("Error: " + errorThrown); 
       $('.msg-div').show();
            $('#edit-fail-msg span').html('File Uploading Permission Denied. Please contact ITRC');
    } 
            });
        }

    }else if(id == ''){
        if($("#assessment_id").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Select Assessment!');    
        }else if($("#exam_name").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Exam Name!');    
        }else if($("#total_question").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Total Question Number!'); 
        }else if($("#duedate").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Due Date!'); 
        }else if($("#duetime").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Start Time!'); 
        }else if($("#duration").val() == ''){
            $('.msg-div').show();
            $('#edit-fail-msg span').html('Please Enter Duration!'); 
        }else{
            var bi_form = document.querySelector('#exam_form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('insertexamquestion')}}',
                data:fd,
                processData:false,
                contentType:false,
                
                success:function(data){
                    $('.msg-div').hide();
                    $('.succ-msg-div').show();
                    $('#edit-success-msg span').html('Data Insert Successfully!'); 
                    setTimeout(function(){
                        $('.succ-msg-div').hide();  
                        location.reload();                   
                    }, 1800);                   
                    $("#exam_name").val('');
                    $("#total_question").val('');
                    $("#duedate").val(''); 
                    $("#duetime").val('');
                    $("#duration").val(''); 
                    $("#assessment_id").val('');
                    $("#assessment_no").val('');
                    $("#table_div").load(window.location + " #table_div");
                    $('.msg-div').hide();
                    $("#uploadfile").val('');
                  
                },
                complete: function(){
                },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
       // alert("Status: " + textStatus); alert("Error: " + errorThrown); 
       $('.msg-div').show();
            $('#edit-fail-msg span').html('File Uploading Permission Denied. Please contact ITRC');
    } 
            });
        }
    }else{
        
        var bi_form = document.querySelector('#exam_form');
        var fd = new FormData(bi_form);
        $.ajax({
            type:'POST',
            url: '{{url('updateexamquestion')}}/'+id,
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
                $('.msg-div').hide();
                $('.succ-msg-div').show();
                $('#edit-success-msg span').html('Data Update Successfully!'); 


                // reset form code start **********************************************************
                 // form reset
    $('#exam_form')[0].reset();

    // hidden/edit values clear
    $('#id').val('');
    $('#course_criteria_id').val('');
    $('#edit_file').val('');

    // assessment دوبارہ enable
    $('#assessment_id').prop('disabled', false);
    $('#assessment_no').prop('disabled', false);

    // download file hide
    $('#download_div').hide();
    $('#exam_file').attr('href', '');

    // table refresh
    $("#table_div").load(window.location + " #table_div");
                // reset form code end *******************************************************************

                setTimeout(function(){
                    $('.succ-msg-div').hide();                    
                }, 1800);
                $("#table_div").load(window.location + " #table_div");
            },
            complete: function(){
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
       // alert("Status: " + textStatus); alert("Error: " + errorThrown); 
       $('.msg-div').show();
            $('#edit-fail-msg span').html('File Uploading Permission Denied. Please contact ITRC');
    } 
        });
    }
}

function updtcreatexam(id){ 
    $( "#assessment_id").focus();
    $( "#exam_name").focus();
    
    $("#assessment_no").prop('disabled', true);
    $("#assessment_id").prop('disabled', true);

    $.ajax({
            type:'get',
            url:'{{url('EditExam')}}/'+id,
            success:function(data){                      
            var row = (data['0']['exam_date']);
            var datetime= row;
            var date=datetime.split(' ')[0];
            var rowtime = (data['0']['exam_start_time']);
            var time= rowtime;
            var time=time.split(' ')[1];           
            $("#exam_name").val(data['0']['title']);
            $("#total_question").val(data['0']['total_questions']);
            $("#papper_pattern").val(data['0']['exam_pattern']); 
            $("#duedate").val(date); 


            $("#duetime").val(time);
            $("#duration").val(data['0']['exam_duration']); 
            $("#id").val(data['0']['id']);
            $("#assessment_id").val(data['0']['assessment_id']);
            $("#assessment_no").val(data['0']['assessment_no']);
            $("#edit_file").val(data['0']['exam_file']);
           var filme_name=data['0']['exam_file'] 
            if(filme_name){
                $("#download_div").show();
            var downloadPath = '<?=url('/docs/')?>';
            var filepath = downloadPath+'/'+data['0']['exam_file'];
            
            $("#exam_file").attr('href',filepath);
          }else{
            $("#download_div").hide();
          }
            }
        });
}

    function finalexam(id){
        var exam_total     = $("#total_exam_ques"+id).val();
        var total_ques     = $("#total_ques"+id).val();
        var exam_time      = $("#exam_time"+id).val();
    
        $('#exam_id').val(id);
        $("#total").html(total_ques);
        $("#time").html(exam_time);
        $("#exam_total").html(exam_total);
    }

    function closemodal(){
        $('#edit-fail-msg_modal span').html("");
    }

    function finalsubmit(sc_co_id){   
        var id          = $("#exam_id").val();
        var sc_co_id    = $("#co_id").val();
        var adm_sch_id  = $("#adm_sch_id").val();
        if(adm_sch_id != 0){
            var sc_co_id = 0;
        }else if (sc_co_id != 0){
            var adm_sch_id =0
        }
        // alert(adm_sch_id);
        // return false;
        $.ajax({
            type:'get',
            url:'{{url('teacher/finalexam')}}/'+id+'/'+sc_co_id+'/'+adm_sch_id,
            success:function(data){ 
            if(data == 1){
                $('.msg-div_modal').show();  
                $('#edit-fail-msg_modal span').html('Your entered questions are less than defined questions');
                return false;  
            }
            $('.succ-msg-div').show();  
            $('#edit-success-msg span').html('Exam Final Submit Successfully!');
            $('#ModalCenter4').modal('hide');

            setTimeout(function(){
                // $('.succ-msg-div').hide();
                // $("#table_div").load(window.location + " #table_div");
            }, 1800);

            }
        });
    }

    $( function() {
        // $( "#datepicker" ).datepicker({
        //     dateFormat: "yy-mm-dd",
        //   changeMonth: true,
        //   changeYear: true,
        //   yearRange: "1980:2008"
        // });
    } );
    
</script>
@stop