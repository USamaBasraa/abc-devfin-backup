<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
// exit;
?>
@extends('layouts.app',['pageTitle' => 'Online Exam Result'])
@section('content')
<div class="modal fade" id="move_to_cms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:700px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Marks Move To GRADING</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
<?php



?>

<!-- Print Table Start Here  -->
<div class="card">
    <div class="card-header" style="background-color:#D2D6F1 !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Exam Details </h3>
                                    <h3 style="float: right;">{{$course_name}}</h3>

    </div>
    <div class="card-body" id="table_div">
<table class="table table-striped table-responsive">
  <thead>
    <tr>
      <td ><b>Assessment</b></td>
      <td align="center"><b>Title</b></td>
      <td align="center"><b>Total Questions</b></td>
      <td style="display:none;" align="center"><b>Pass Percentage</b></td>
      <td style="display:none;" align="center"><b>Exam Pattern</b></td>
      <td align="center"><b>Exam Date</b></td>
      <td align="center"><b>Exam Start Time</b></td>
      <td align="center"><b>Exam Duration</b></td>
      <td align="center"><b>View Result</b></td>
      <!-- <td align="center"><b>Action</b></td> -->


    </tr>
  </thead>
  <tbody>
    <tr>
     @foreach($result as $question_bank_rec)
     <?php
     $id=my_simple_crypt($question_bank_rec->id,'e');
     $criteria='';
      ?>
       <td>
       <?php if(isset($get_criteria)){?>
         @foreach($get_criteria as $rec_course_criteria)
         @if($rec_course_criteria->id==$question_bank_rec->course_criteria_id )

            <span style="font-weight: 600;color: #078282FF"> {{ $rec_course_criteria->assessment_name}} ({{ $question_bank_rec->assessment_no}})
            </span>
          @endif
          @endforeach
        <?php } ?>
         </td>
      <td>{{$question_bank_rec->title}}  </td>
      <td align="center">{{$question_bank_rec->total_questions}}</td>
      <td style="display:none;" align="center">{{$question_bank_rec->percentage_status}}</td>
      <td style="display:none;">{{$question_bank_rec->exam_pattern}}</td>
      <td align="center"><?php echo substr($question_bank_rec->exam_date,0,11); ?></td>
      <td align="center"><?php echo substr($question_bank_rec->exam_start_time,11,10);?></td>
      <td align="center">{{$question_bank_rec->exam_duration}}</td>
      <td >
      <?php if($question_bank_rec->move_to_cms == 'No'){?>
        <button  type="button" class="btn btn-warning btn-lg btn-block" onclick="move_to_cms(<?=$question_bank_rec->id;?>,<?=$question_bank_rec->co_id;?>,<?=$question_bank_rec->assessment_id;?>)">Move to CMS</button><hr>
      <?php } else {
         $criteria='';
 foreach($criteria_detail as $rec_criteria){
  if( $rec_criteria->id  == $question_bank_rec->move_to_cms){ ?>

        <button  type="button" class="btn btn-success btn-lg btn-block">{{$rec_criteria->ccd_details}}</button><hr>

<?php } } ?>
      <?php } ?>
      <a href="{{route('viewstudentresult')}}/<?=$id;?>" ><button type="button" class="btn btn-primary btn-lg btn-block" >View Result</button></a></td>
    </tr>
     @endforeach
  </tbody>
</table>
</div>
</div>

@stop
@section('javascript')
<script>
function movetocms(id){
$.ajax({
            type:'get',
            url:'{{url('movetocms')}}/'+id,
            success:function(data){
              if(data == 1){

                alert('Move to Cms Successfully')
              }else{
                alert('Already Moved to Cms')
              }
            }
        });
}
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

</script>
@stop
