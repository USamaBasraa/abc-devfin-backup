<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
//print_r($sqlresult);
//exit;
?>
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('mainBody')

<?php

function my_simple_crypt( $string, $action = 'e' ) {
  // you may change these values to your own
  $secret_key = 'my_simple_secret_key';
  $secret_iv = 'my_simple_secret_iv';

  $output = false;
  $encrypt_method = "AES-256-CBC";
  $key = hash( 'sha256', $secret_key );
  $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

  if( $action == 'e' ) {
      $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
  }
  else if( $action == 'd' ){
      $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
  }

  return $output;
}
$adm_sch_id='';
$co_id=$sqlresult[0]->co_id;
$final_submit= $sqlfresult[0]->gh_exam_fsubmit;
$co_id_decrpt=my_simple_crypt($co_id,'e');
foreach ($result as $key ) {
 $adm_sch_id = $key->exam_paper_admission_sch_id;

}
?>
<!-- Print Table Start Here  -->



<div class="card">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > 
                Exam Name : <?=$sqlresult[0]->title;?>
           </h3>
            <h3 style="float: right;">{{$course_name}}</h3>

          
        <span style="float:right;"><a href="{{route('DownloadPaperWord',$sqlresult[0]->id)}}"> Download All Students Paper </a> </span>
    </div>
    <div class="card-body" >
     <div style="float:right">
        <?php if($adm_sch_id != 0){ ?>

          <button type="button" onclick="adm_go_back('<?=$adm_sch_id;?>')" class="btn btn-primary" >Go Back</button>
      
       <?php }else{?>
             <button type="button" onclick="go_back('<?=$co_id_decrpt;?>')" class="btn btn-primary" >Go Back</button>
       <?php } ?>
      </div>
<table id="exampfle-1" class="table table-striped table-bordered" >
  <thead>
     <tr>    
          <td align="center"><b>Sr #</b></td>
          <?php if($adm_sch_id != 0){ ?>
          <td align="center"><b>Applicant ID</b></td>
          <?php }else{?>
            <td align="center"><b>Enrollment No</b></td>
            <?php } ?>
          <td align="center"><b>Student Name</b></td>
           <?php if($adm_sch_id != 0){ ?>
          <td align="center"><b>Mobil No</b></td>
           <?php } ?>
          <td align="center"><b>Total Questions</b></td>
          <td align="center"><b>Right</b></td>
          <td align="center"><b>Wrong</b></td>
          <td align="center"><b>Missed</b></td>
          <td align="center"><b>Total Marks</b></td>
          <td align="center"><b>Obtained Marks</b></td>
           <td align="center"><b>Exam Status</b></td>
          <td align="center"><b>Action</b></td>
      </tr>
  </thead>
  <tbody>
  <tr>
    <?php
      $count=1;
    ?>
     @foreach($result as $question_bank_rec) 
     <?php 

     $enrollment_no=my_simple_crypt($question_bank_rec->sai_enrollment_no,'e');
      $id=my_simple_crypt($question_bank_rec->id,'e');
     $correct = $question_bank_rec->correct;
     $wrong = $question_bank_rec->wrong;
     $missed = $question_bank_rec->missed;
     $correct_marks = $question_bank_rec->final_obtained_marks;
     $wrong_marks = $question_bank_rec->wrong_marks;
     $missed_marks = $question_bank_rec->missed_marks;
     $total_marks = $correct_marks+$wrong_marks+$missed_marks;
     $total = $correct+$wrong+$missed;
     
     
     ?>
      <td align="center" >{{$count}} </td>
      <td align="center" >{{$question_bank_rec->sai_enrollment_no}} </td>
      <td align="left" >{{$question_bank_rec->spi_name}} </td>
       <?php if($adm_sch_id != 0){ ?>
      <td align="center" >{{$question_bank_rec->mobile_no}} </td>
        <?php } ?>
      <td align="center"> <?=$total?></td>   
      <td align="center">{{$question_bank_rec->correct}}</td>
      <td align="center">{{$question_bank_rec->wrong}}</td>
      <td align="center">{{$question_bank_rec->missed}}</td>
      <?php
		if($question_bank_rec->get_total_marks!=$question_bank_rec->total_questions){
	  ?>
		<td align="center">{{$question_bank_rec->total_questions}}</td>
	  <?php
		}else{
	  ?>
	   <td align="center">{{$question_bank_rec->get_total_marks}}</td>
	  <?php
		}
	  ?>
      <td align="center"><?=$correct_marks?></td> 
       <td align="center"><button data-toggle="modal" data-target="#advancemodel" onclick="paperstatus('<?=$question_bank_rec->sai_enrollment_no;?>','<?=$question_bank_rec->id?>','<?=$question_bank_rec->exam_status;?>','<?=$question_bank_rec->description;?>')" type="button" class="btn btn-<?=(3 == $question_bank_rec->exam_status) ? 'success' : 'primary';?> " >Exam Status</button></td>      
      <td align="center"><a class="<?=(0!= $final_submit) ? 'disabled' : ' ';?>" href="{{route('teacher/check_paper')}}/<?=$enrollment_no;?>?id=<?=$id?>"  ><button type="button" class="btn btn-<?=(0 == $question_bank_rec->result_status) ? 'success' : 'primary';?>" ><?=(0 == $question_bank_rec->result_status) ? 'Marked Paper' : 'Mark Paper ';?></button></a></td> 

  </tr>
    <?php
    $count++;
    ?>
     @endforeach
  </tbody>
</table>
</div>
</div>




<!----------  ADVANCE MODEL upload file START---------------------->
<div class="modal fade" id="advancemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="  width: 600px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Student Paper</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                     <div class="modal-body">
                     <form enctype="multipart/form-data" method="post" name="paper_setting"  id="paper_setting">
                     @csrf
                     <input type="hidden" id="paper_id" name="paper_id" />
                     <input type="hidden" id="roll_no" name="roll_no" />
                     <div class="row">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><strong>Exam Status </strong></div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                            <select id="paper_status" name="paper_status" class="form-control"  style="width:300px;" >
                            <option value="0">Exam Continue</option>
                            <option value="3">Exam Stop</option>
                            <option value="4">Exam Reload</option>
                           <!--  <option value="6">Exam Re Allowed</option> --> 
                             </select>                       
                        </div>
                     </div>
                   <div class="row" style="margin-top: 6px;">
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><strong> Description </strong></div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                          <input type="text" class="form-control"  style="width:300px;" id="desc" name="desc" />             
                        </div>
                     </div>
                                           

                    </form>
                        </div>
                     
                    <div class="modal-footer">
                    <div id="saved_msg_div_file" style="display: none;color: green"><span>Data Successfully</span></div> 
                    <button type="button" onclick="cancell_ass()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button  onclick="savesetting()" type="button" class="btn btn-primary upload_btn">
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

  function go_back(co_id){
//alert(co_id);
  window.location = '<?=url('/teacher/online_result/')?>/'+co_id;
 
}
function adm_go_back(adm_sch_id){
//alert(co_id);
  window.location = '<?=url('/teacher/adm_online_result/')?>/'+adm_sch_id;
 
}


  function paperstatus(enroll,exam_id,status,desc){
    //alert(enroll+exam_id)
 $("#paper_id").val(exam_id) ;
 $("#roll_no").val(enroll) ;

 $("#paper_status").val(status) ;
   $("#desc").val(desc) ;

}
  function savesetting(){

          var bi_form = document.querySelector('#paper_setting');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('teacher/update-exam-status')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                    $("#saved_msg_div_file_error").hide();
                    $("#saved_msg_div_file").show();
          $("#exampfle-1").load(window.location + " #exampfle-1");
          setTimeout(function(){
                  $("#advancemodel").modal('hide');

                  }, 2000);
                  location.reload();

                },
                complete: function(){
                }
            });

   

  }

  setInterval(function() {
             $("#exampfle-1").load(window.location + " #exampfle-1");
location.reload();

}, 1800000);
</script>
@stop