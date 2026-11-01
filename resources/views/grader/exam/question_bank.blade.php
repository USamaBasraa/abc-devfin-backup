<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
//print_r($sess);
//exit;

?>
@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')


<?php

//print_r($course_name);exit;
?>


<div class="card">     
    <div class="card-header" style="background-color:steelblue !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Add Question Bank </h3>

            <h3 style="float: right;">{{$course_name}}</h3>
    </div>
    <div class="card-body">
		<form enctype="multipart/form-data" method="post" name="question_form"  id="question_form">
			    <?php foreach($result_course as $key_rec)
        $co_id=$key_rec->fc_co_id;
        $course_id=$key_rec->fc_co_course_id;
      ?>
    <input type="hidden" name="co_id" id="co_id" value="<?php echo $co_id;?>" >
    <input type="hidden" name="course_id" id="course_id" value="<?php echo $course_id;?>" >
     @csrf

		<table border="1" class="table table-bordered">
		<!-- 	 <tr>
		    	<td> Exam Name </td>
		    	<td> 
		    		
		        	<select name="exam_id" id="exam_id" class="form-control"  style="width:300px;" required="required">
		        		@foreach($exam_bank as $exam_rec)
						<option value="{{$exam_rec->id}}">{{$exam_rec->title}}</option>
						 @endforeach
		            </select>
		           
		         </td>
		    </tr> -->
			<tr class="form-group">
		    	<td style="padding-top: 26px;text-align-last: center;"><strong> Question </strong> </td>
		    	<td> <textarea class="form-control" rows="3" name="qname" id="qname" placeholder="Add Question" maxlength="2000" style="width:300px;" ></textarea>
		    	  </td>
		    </tr>
		    <tr>
		    	<td style="text-align-last: center;"><strong> A </strong></td>
		    	<td> <input type="text" name="option_a" id="option_a" class="form-control form-control-md"  maxlength="1000" > </td>
		    </tr>
		    <tr>
		    	<td style="text-align-last: center;" > <strong>B </strong></td>
		    	<td> <input type="text" name="option_b" id="option_b"class="form-control form-control-md"  maxlength="1000" > </td>
		    </tr>
		    <tr>
		    	<td style="text-align-last: center;"><strong> C</strong></td>
		    	<td> <input type="text" name="option_c" id="option_c"class="form-control"  maxlength="1000"> </td>
		    </tr>
		    <tr>
		    	<td style="text-align-last: center;"><strong> D </strong></td>
		    	<td> <input type="text" name="option_d" id="option_d"class="form-control form-control-md"  maxlength="1000" > </td>
		    </tr>
		    <tr>
		    	<td style="text-align-last: center;"><strong>Correct Answer</strong> </td>
		    	<td> 
		        	<select name="correct_option" class="form-control"  style="width:300px;" >
		        		<option>A</option>
		                <option>B</option>
		                <option>C</option>
		                <option>D</option>
		            </select>
		         </td>
		    </tr>
		        <tr>
		    	<td> </td>
		    	<td> <button type="button" class="btn btn-primary btn-lg btn-block" onclick="add_question()">Submit</button> </td>
		    </tr>
		    
 
		    

		</table>
		</form>
		<div id="saved_msg_div_file" style="display: none;color: green"><span>Question Add Successfully In Question Bank</span></div> 
		<div id="saved_msg_div_delete" style="display: none;color: green"><span>Question Delete Successfully In Question Bank</span></div> 
		 <div id="error_msg" class="msg-div" style="display: none;color: red"><span></span></div>
    </div>
</div>

<!-- //// print record /////////////// -->
<div class="card" style="margin-top: 20px">     
    <div class="card-header" style="background-color:steelblue !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Question Bank </h3>

    </div>
    <div class="card-body">
<table id="simpletable" class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Question</th>
      <th scope="col">Option A</th>
      <th scope="col">Option B</th>
      <th scope="col">Option C</th>
      <th scope="col">Option D</th>
      <th scope="col">Correct</th>
       <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <?php
      $count=1;
      ?>
    	@foreach($question_bank as $question_bank_rec)
    	<?php

    	 $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
    	
    	?>
      <th scope="row">{{$count}}</th>
      <td>{{$question_bank_rec->question}}</td>
     <td>{{$question_bank_rec->oa}}</td>
     <td>{{$question_bank_rec->ob}}</td>
     <td>{{$question_bank_rec->oc}}</td>
     <td>{{$question_bank_rec->od}}</td>
     <td>{{$question_bank_rec->correct}}</td>
       <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$question_bank_rec->id;?>')"><i class="fa fa-trash"></i> Delete</button></td>

    </tr>
    <?php
    $count++;
    ?>
    @endforeach
  </tbody>
</table>


</div>
</div>
@stop
@section('pageScripts')
<script>
function add_question(){
			var exam_id       = $("#exam_id").val();
 			var qname       = $("#qname").val();
            var option_a        = $("#option_a").val();
             var option_b        = $("#option_b").val();
              var option_c        = $("#option_c").val();
               var option_d        = $("#option_d").val();
                $('.form-control').removeClass('is-invalid');
 				var error = 0;
 			  $('#error_msg').hide();
   			if(qname == ''){
   	 			$('#qname').addClass('is-invalid');
   	 			 $('#error_msg span').html('Please provide question complete!');
                error++;
            } if(option_a== ''){
            	$('#option_a').addClass('is-invalid');
            	error++;
            }if(option_b== ''){
				$('#option_b').addClass('is-invalid');
				error++;
            }if(option_c == ''){
			$('#option_c').addClass('is-invalid');
			error++;
            }if(option_d== ''){
				$('#option_d').addClass('is-invalid');
				error++;
            }

 		if( error > 0 ){
                $('#error_msg').show();
                return false;
            }
    var bi_form = document.querySelector('#question_form');
    var fd = new FormData(bi_form);
    $.ajax({
        type:'POST',
        url: '{{url('teacher/add_questionbank')}}',
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){

			$("#saved_msg_div_file").show();
					$("#qname").val('');
                    $("#option_a").val('');
                    $("#option_b").val('');
                     $("#option_c").val('');
                      $("#option_d").val('');
                $('.form-control').removeClass('is-invalid');
			 $("#simpletable").load(window.location + " #simpletable");
			setTimeout(function(){
			           	$("#saved_msg_div_file").hide();
			}, 2000);
			},
        complete: function(){
        }
    });
}

////// add question in exam///

function add_question_exam(id){
	//alert(id);
   // var tt_new_content_id = $("#tt_new_content_id").val();
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/exam_question')}}/'+id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
			console.log(data.message);
        		if(data.message==1){
        			$("#saved_msg_div_file").show();
        			 $("#simpletable").load(window.location + " #simpletable");
        		}else if(data.message==0){
				alert('Question Already Added In Exam');
        		}
        		setTimeout(function(){
                    $("#saved_msg_div_file").hide();

                    }, 2000);
        },

        complete: function(){
        	
        }
    });
}

////// delete question in exam///

function delete_question_exam(id){
	//alert(id);
   // var tt_new_content_id = $("#tt_new_content_id").val();
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/delete_question')}}/'+id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
			console.log(data.message);
        		
        			$("#saved_msg_div_delete").show();
        			 $("#simpletable").load(window.location + " #simpletable");
        		
        		setTimeout(function(){
                    $("#saved_msg_div_delete").hide();

                    }, 2000);
        },

        complete: function(){
        	
        }
    });
}





</script>
@stop




