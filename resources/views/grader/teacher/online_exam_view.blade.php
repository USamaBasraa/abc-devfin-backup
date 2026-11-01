<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
 //echo $co_id;
// exit;
?>
@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')

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
    width: 86.9%;
    height: 490px;
    /* overflow-y: scroll;
    overflow-x:hidden;
    scrollbar-color: #2F3B4D white;
     scrollbar-width: thin; */
}




</style>
<!-- Print Table Start Here  -->
<div class="card pic-container">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Details </h4>
             <h3 style="float: right;" class="blinking">Disclaimer :Final submit is mandatory to start the exam at student side </h4>
    </div>
   


<div class="card cards">     
    <!-- <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Details </h4>
             <h3 style="float: right;" class="blinking">Disclaimer :Final submit is mandatory to start the exam at student side </h4>
    </div> -->
    <div class="card-body" id="table_div">
    
<table class="table table-striped">
  <thead>
    <tr>    
    <td align="center"><b>Teacher</b></td>
    <td align="center"><b>Course</b></td>
      <td align="center"><b>Assessment</b></td>
      <td align="center"><b>Questions</b></td>
      <td style="display:none;" align="center"><b>Pass Percentage</b></td>
      <td style="display:none;" align="center"><b>Exam Pattern</b></td>
      <td align="center"><b>Exam Date</b></td>
      <td align="center"><b>Exam Start Time</b></td>
      <td align="center"><b>Exam Duration</b></td>
      </tr>
     
  </thead>
  <tbody>
   
    @foreach($result as $question_bank_rec)  
    <tr >
    <td align="left">{{$question_bank_rec->teacher_name}}</td>
    <td align="left">{{$question_bank_rec->course_name}}</td>
      <td align="left">{{$question_bank_rec->title}}</td>
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
    <td align="center"><a href="{{route('viewstudentresultcopy')}}/<?=$question_bank_rec->id;?>" ><button type="button" class="btn btn-primary" >View Result</button></a></td> 
    </tr>
@endforeach
      </tbody>
</table>

</div>
</div>


</div>
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
@section('pageScripts')
<script>

function advancesetting(id,marks,type,reatmp,review,ccp,file_require){
// alert(type);
// return false;
    $("#paper_id").val(id) ;  
    $("#neg_marks").val(marks) ;
   $("#exam_paper_type").val(type) ;
   $("#re_attempt").val(reatmp) ;
   $("#re_view").val(review) ;
   $("#ccp").val(ccp) ;
   $("#neg_marks_hidden").val(marks) ;
   $("#file_require").val(file_require) ;
        
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


// function getval(sel,co_id){
//     //alert (sel.value+' '+co_id);
//     var assess_id = sel.value;
//     var co_id = co_id;
//       //  return false;
//     $.ajax({
//             type:'get',
//             url:'{{url('getcountassessment')}}/'+assess_id+'/'+co_id,
//             success:function(data){          
//             $("#assessment_no").val(data['0']['count'])
//             }
//         });
// } 
function insertcreatexam(){
    // var downloadPath = '<?=url('/docs/')?>';
    // var filepath = downloadPath+'/'+data['0']['file_name'];
    
    var id = ($("#id").val());
    if(id == ''){
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
     //alert(sc_co_id);
    // return false;
    // if(total_question == ''){
    //     $('.msg-div_modal').show();  
    //          $('#edit-fail-msg_modal span').html('Please enter total questons no');
    //        //  $('#ModalCenter4').modal('hide');

    // }else{
   
     
    $.ajax({
            type:'get',
            url:'{{url('teacher/finalexam')}}/'+id+'/'+sc_co_id,
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