<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
//print_r($sess);
//exit;

?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')





<!-- //// print record /////////////// -->
<div class="card" style="width: 86%;margin-left: 167px;margin-top: 20px">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Time Management  </h3>
            <h3 style="float: right;">{{$course_name}}</h3>

    </div>
  
    <div class="card-body">
        <?php


$count=count($exam_questinbank);

if($count==0){

   
  echo '<h1> Kindly Enter Question First In Exam </h1>';
  //exit;
}else{ 
 // echo $exam_total_ques.'__'.$total_question;
  if($exam_total_ques > $total_question){

echo '<h1>IF You Want to Change Marks Please Add Exam Question Equal to Total Question </h1>';
//exit();
 }else{


 
?>
<table id="simpletable" class="table table-striped">
  <thead>
    <tr>
      <th scope="col ">#</th>
      <th scope="col">Question</th>
      <th scope="col">Exam Name</th>
      <th scope="col">Exam Time</th>
    
       <th scope="col">Action </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php

//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td>{{$exam_bank_rec->question}}</td>
     <td>{{$exam_bank_rec->title}}</td>
    <td><input type="number" min="0" name="time" id="time<?=$exam_bank_rec->id;?>"class="form-control"  style="width:70px;" value="{{$exam_bank_rec->exam_question_time}}" ></td>
    
       <td> <button type="button"s class="btn btn-lg" onclick="update_question_marks('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-edit"></i> Update </button></td>

    </tr>
     <?php
    $count++;
    ?>
    @endforeach

  </tbody>
</table>

<?php } }?>
</div>

 <div id="saved_msg_div_file" style="display: none;color: green"><span>Time Update Successfully In Exam</span></div> 
</div>
@stop
@section('pageScripts')
<script>

function update_question_marks(id){
	
    var time = $("#time"+id).val();
   

            var fd = new FormData();
            
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: '{{url('teacher/update_time_mgt')}}/'+id+'/'+time,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
               //   console.log(data.message);
        		
            $("#saved_msg_div_file").show();
          $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
                  $("#saved_msg_div_file").hide();

                  }, 2000);

                },
                complete: function(){
                }
            });

  
}
</script>
@stop




