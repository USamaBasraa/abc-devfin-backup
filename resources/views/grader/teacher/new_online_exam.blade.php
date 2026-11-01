<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
//  echo $adm_sch_id;
//print_r($result);

// exit;
?>
{{-- @extends('layouts.master_add_question') --}}

@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')
<div class="card" style="border:0px !important">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF;  border-top-left-radius: 15px;border-top-right-radius: 15px">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Create Exam </h3>
                <h3 style="float: right;">
                  
                   </h3>  
            <h3 style="float: right;">{{$course_name}}</h3>
    </div>
    <div class="card-body" style="border:1px solid black;border-bottom-left-radius: 15px;border-bottom-right-radius: 15px; margin-bottom:25px">
    <div style="padding-left:300px;padding-right:300px">
        <form enctype="multipart/form-data" method="post" name="exam_form"  id="exam_form">
            @csrf
            <input type="hidden" style="width: 200px!important" value="<?=$co_id?>" class="form-control" id="co_id" name="co_id"></input>
            <input type="hidden" style="width: 200px!important"  class="form-control" id="id" name="id"></input>
            <input type="hidden" style="width: 200px!important"   class="form-control" id="course_criteria_id" name="course_criteria_id"></input>
            <input type="hidden" style="width: 200px!important"   class="form-control" id="edit_file" name="edit_file"></input>
            <input type="hidden" style="width: 200px!important" value="<?=$adm_sch_id?>" class="form-control" id="adm_sch_id" name="adm_sch_id"></input>
            
           <table style="width:100%">
            <?php if($adm_sch_id !='' ){  ?>
                <tr>
                <td align="right" width="30%">Assessment<b class="text-danger">*</b></td>
                <td width="70%"> 
                    <select class="form-control" style="width: 250px!important;"  onclick="getadmval('<?=$adm_sch_id?>')" id="assessment_id" name="assessment_id">
                  
                    
                    <option value="<?=$adm_sch_id?>"><?=$adm_sch_name[0]->admission_sch_name?></option>
                   
                    </select></td>
            </tr>
           <?php }else{ ?>
            <tr>
                <td align="right" width="30%">Assessment<b class="text-danger">*</b></td>
                <td width="70%"> 
                    <select class="form-control" style="width: 250px!important;"  onchange="getval(this,'<?=$co_id?>')" id="assessment_id" name="assessment_id">
                    <option value="">--Select Assessment--</option>
                    @foreach($course_criteria as $rec_course_criteria)
                    <option value="{{$rec_course_criteria->assessment_id}}">{{ $rec_course_criteria->assessment_name}}</option>
                    @endforeach
                    </select></td>
            </tr>
           <?php } ?>
           <tr>
                <td align="right">Assessment No<b class="text-danger">*</b></td>
                <td><input type="text" style="width: 250px!important;" class="form-control" id="assessment_no" name="assessment_no" readonly ></input></td>
            </tr>
            
            <tr>
                <td align="right">Description<b class="text-danger">*</b></td>
                <td><input type="text" style="width: 250px!important;" class="form-control" id="exam_name" name="exam_name"></input></td>
            </tr>
            <tr>
                <td align="right">Total Questions<b class="text-danger">*</b></td>
                <td><input type="text" style="width: 250px!important" class="form-control" id="total_question" name="total_question"></input></td>
            </tr>
            <tr style="display:none;">
                <td align="right">Pass Percentage<b class="text-danger">*</b></td>
                <td><input type="text" style="width: 250px!important" class="form-control" id="percentage_status" name="percentage_status"></input></td>
            </tr>
            <tr style="display:none;">
                <td align="right">Paper Pattern<b class="text-danger">*</b></td>
                <td><select class="form-control assess-form-input" style="width: 250px!important"  id="papper_pattern" name="papper_pattern">
                <option value="R">Random Order</option>
                <option value="F">Fixed Order</option>
                </select></td>
            </tr>
            <tr>
                <td align="right">Due Date<b class="text-danger">*</b></td>
                <td><input type="date" style="width: 250px!important" class="form-control assess-form-input optional-inputs" id="duedate" name="duedate" placeholder="Due Date"></input>
            </tr>
            <tr>
                <td align="right">Start Time<b class="text-danger">*</b></td>
                <td><input type="time" style="width: 250px!important" class="form-control assess-form-input optional-inputs" id="duetime" name="duetime"  placeholder="Due Time">
            </tr>
            <tr>
                <td align="right">Duration(In Minutes)<b class="text-danger">*</b></td>
                <td><input type="number"  style="width: 250px!important" class="form-control assess-form-input" id="duration" name="duration" placeholder="duration">
            </tr>
            
            <tr>
                <td align="right">Exam File<b class="text-danger">(Optional)</b></td>
                <td>  <input type="file" style="width: 250px!important" class="form-control" name="uploadfile" id="uploadfile" ></td>
                
            </tr>
            <tr style="display:none" id="download_div">
            <td>
            </td>
                <td>                
                    <a id="exam_file" style="text-decoration: underline !important;color:black"  >
                    Download </a>       
                </td>
            </tr>           
            <tr>
                <td></td>
                <td>  <button style="padding-left:108px;padding-right:106px" type="button" class="btn btn-primary" onclick="insertcreatexam()">Schedule</button>
                      <div id="edit-fail-msg"     class="msg-div" style="display: none;color: red"><span></span></div>
                      <div id="edit-success-msg"  class="succ-msg-div" style="display: none;color: green"><span></span></div>
                </td>
               
            </tr>
            </table>   
            <!-- {{--<textarea id="getcontent" name="getcontent" rows="4" cols="72"></textarea>--}} -->
        </form>
    </div>
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
     border: .5px solid black;
    // padding-bottom: 2%;
 }
 .pic-container {
    width: 100%;
    height: 270px;
    /* overflow-y: scroll;
    overflow-x:hidden;
    scrollbar-color: #2F3B4D white;
     scrollbar-width: thin; */
}
</style>
<!-- Print Table Start Here  -->


{{-- <div class="card pic-container">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Details </h4>
             <h3 style="float: right;" class="blinking">Disclaimer :Final submit is mandatory to start the exam at student side </h4>
    </div> --}}

@foreach($result as $question_bank_rec)  
<div class="card pic-container" style="border:0px !important">  
    {{-- style="background-color:#2F3B4D !important; color:#FFF; " --}}
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; border-top-left-radius: 15px;border-top-right-radius: 15px" >
        <div   style="width:430px;float:left;color: #white"> <span style="font-size: 16px;font-weight: 600" >Exam Name : {{$question_bank_rec->title}} </span> </div>
        <div align="center"  style="width:430px;float:left;color: #white"> <span style="font-size: 16px;font-weight: 600" >Assessment : </span> 

            @foreach($course_criteria as $rec_course_criteria)
               @if($rec_course_criteria->id==$question_bank_rec->course_criteria_id)
                  <span style="font-size: 16px;font-weight: 600;color: #white"> {{ $rec_course_criteria->assessment_name}} ( {{ $question_bank_rec->assessment_no}} )
                  </span>
                @endif
            @endforeach</div>
            <div align="right"  style="width:430px;float:left;color: #white;"> <span style="font-size: 16px;font-weight: 600" ><button style="padding-left:43px;padding-right:43px" type="button" class="btn btn-primary" onclick="updtcreatexam('{{$question_bank_rec->id}}')">Edit Schedule</button> </span> </div>
    </div>
<div class="card cards" style="border-bottom-left-radius: 15px;border-bottom-right-radius: 15px">     
    <!-- <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Details </h4>
             <h3 style="float: right;" class="blinking">Disclaimer :Final submit is mandatory to start the exam at student side </h4>
    </div> -->
    <div class="card-body" id="table_div">

<table class="table table-striped">
  <thead>
   
    <tr>    
     
      {{-- <td align="center"><b>Exam Name</b></td> --}}
      <td align="center"><b>Questions</b></td>
      <td style="display:none;" align="center"><b>Pass Percentage</b></td>
      <td style="display:none;" align="center"><b>Exam Pattern</b></td>
      <td align="center"><b>Exam Date</b></td>
      <td align="center"><b>Exam Start Time</b></td>
      <td align="center"><b>Exam Duration</b></td>
      </tr>
     
  </thead>
  <tbody>
    <tr >
       
      {{-- <td align="center">{{$question_bank_rec->title}}</td> --}}
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
    </tr>
<tr>
      
      {{-- <td align="center"><button style="padding-left:43px;padding-right:43px" type="button" class="btn btn-primary" onclick="updtcreatexam('{{$question_bank_rec->id}}')">Edit Schedule</button></td>  --}}
      <td align="center"><a href="{{route('teacher/quiz_question')}}/?sch_id=<?=$question_bank_rec->exam_paper_admission_sch_id;?>&co_id=<?=$question_bank_rec->co_id;?>&id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary" >Add Question</button></a></td> 
      <td align="center"><a href="{{route('teacher/quiz_marks')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary" >Marks Management</button></a></td> 
     
        <td align="center"><button  type="button"  onclick="advancesetting('{{$question_bank_rec->id}}','{{$question_bank_rec->negative_marks}}','{{$question_bank_rec->paper_attmp_type}}','{{$question_bank_rec->re_attempt}}','{{$question_bank_rec->re_view}}','{{$question_bank_rec->copy_paste_allowed}}','{{$question_bank_rec->exam_answer_file}}','<?=$co_id?>')" class="btn btn-primary">Instructor Control</button></a></td> 
     
      <?php if($question_bank_rec->paper_attmp_type == 'all_in_one' or $question_bank_rec->paper_attmp_type == ''){?>
      <td style="display:none;" align="center"><a href="{{route('teacher/time_management')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary" disabled>Time Management</button></a></td> 
      <?php }else{ ?>
        <td style="display:none;" align="center"><a href="{{route('teacher/time_management')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button type="button" class="btn btn-primary">Time Management</button></a></td> 
      <?php } ?>
      <?php if($question_bank_rec->student_exam_question > 0){ ?>
      <td align="center"><a href="{{route('teacher/view_paper')}}?co_id=<?=$question_bank_rec->co_id;?>&id=<?=$question_bank_rec->id?>&sch_id=<?=$question_bank_rec->exam_paper_admission_sch_id;?>" ><button type="button" class="btn btn-primary" >View Paper</button></a></td> 
      <?php }else{ ?>
<td align="center"><a href="{{route('teacher/view_paper')}}/<?=$question_bank_rec->co_id;?>?id=<?=$question_bank_rec->id?>" ><button disabled type="button" class="btn btn-primary" >View Paper</button></a></td> 
      <?php } ?>
      </tr>
      </tbody>
</table>
<table class="table table-striped">
<tbody>
      <tr>
      <?php 
       if($question_bank_rec->final_submit == 0){
      if($question_bank_rec->student_exam_question > 0){?>
     
      <td align="center"><button id="finalsubmited" style="padding-left:575px;padding-right:575px" type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?> " >Final <?=(1 == $question_bank_rec->final_submit) ? 'Submitted' : 'Submit';?> </button></a></td> 
     
          <?php  }else{  ?>
        <td align="center"><button disabled style="padding-left:575px;padding-right:575px" type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?> " >Final <?=(1 == $question_bank_rec->final_submit) ? 'Submitted' : 'Submit';?> </button></a></td> 
      <?php }
      }else { ?>
       <td align="center"><button disabled style="padding-left:575px;padding-right:575px" type="button" data-toggle="modal" data-target="#ModalCenter4" onclick="finalexam('{{$question_bank_rec->id}}')" class="btn btn-<?=(1 == $question_bank_rec->final_submit) ? 'success' : 'primary';?> " >Final <?=(1 == $question_bank_rec->final_submit) ? 'Submitted' : 'Submit';?> </button></a></td> 
      <?php } ?>
   </tr>

   
  </tbody>
</table>
</div>
</div>
</div>
@endforeach

{{-- </div> --}}
     
 <!----------  MODEL 4 upload file START---------------------->
    <div class="modal fade" id="ModalCenter4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Online Exam creation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                     <div class="modal-body">
                        <h1>Are you sure Want to Exam Final Submit... </h1>
                        <br>
                        <input type="hidden" id="exam_id" name="exam_id" />
                         <input type="hidden" id="co_id" name="co_id" value="<?=$co_id?>" />
                        <h4 style="display: inline-block;">Start Time is :</h4>
                        <h3 style="display: inline-block;" id="time"></h3>
                        
                        <div>
                        <h4 style="display: inline-block;"> Total question Is :</h4>
                        <h3 style="display: inline-block;"  id="total"></h3>
                        </div>
                        <div>
                        <h4 style="display: inline-block;"> Total Defined Questions :</h4>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Advance Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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


@stop
@section('javascript')
<script>
$( document ).ready(function() {

var adm_sch=<?=$adm_sch_id;?>

   getadmval(adm_sch);


});
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
    
   
    $("#download_div").show();
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
            var downloadPath = '<?=url('/docs/')?>';
            var filepath = downloadPath+'/'+data['0']['exam_file'];
            
            $("#exam_file").attr('href',filepath);
          }
            }
        });
}



function finalexam(id){


    var exam_total     = $("#total_exam_ques"+id).val();

  //   alert(exam_total1);
    var total_ques       = $("#total_ques"+id).val();
     var exam_time       = $("#exam_time"+id).val();
   
   $('#exam_id').val(id);
    $("#total").html(total_ques);
    $("#time").html(exam_time);
    $("#exam_total").html(exam_total);
}
function closemodal(){
    $('#edit-fail-msg_modal span').html("");

}
function finalsubmit(sc_co_id){   
 //   $('#edit-fail-msg_modal').html("");
   // total_question
  //  var total_question       = $("#total_question").val();
    var id       = $("#exam_id").val();
    var sc_co_id       = $("#co_id").val();
     //  alert(adm_sch_id);
    // return false;
    var adm_sch_id       = $("#adm_sch_id").val();
    if(adm_sch_id != 0){
        var sc_co_id = 0;
    }else if (sc_co_id != 0){
        var adm_sch_id =0
    }
    //  alert(adm_sch_id);
    // return false;
    // if(total_question == ''){
    //     $('.msg-div_modal').show();  
    //          $('#edit-fail-msg_modal span').html('Please enter total questons no');
    //        //  $('#ModalCenter4').modal('hide');

    // }else{
   
     
    $.ajax({
            type:'get',
            url:'{{url('teacher/finalexam')}}/'+id+'/'+sc_co_id+'/'+adm_sch_id,
            success:function(data){ 
              //  alert(data)
                if(data == 1){
                    $('.msg-div_modal').show();  
             $('#edit-fail-msg_modal span').html('Your entered questions are less than defined questions');
           //  $('#ModalCenter4').modal('hide');
                      return false;  
                
                }
            //    $('.msg-div').hide(); 
              $('.succ-msg-div').show();  
             $('#edit-success-msg span').html('Exam Final Submit Successfully!');
             $('#ModalCenter4').modal('hide');

             setTimeout(function(){
                 $('.succ-msg-div').hide();
                $("#table_div").load(window.location + " #table_div");
                    
            }, 1800);

            }
        });
  //  }
}
</script>
@stop