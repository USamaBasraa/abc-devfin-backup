<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// print_r($sch_id);
// exit;
$tile='';
$exam_adm='';
?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')

<?php
$fc_co_id='';
//print_r($exam_bank);
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

?>
<div class="card">   
<div class="card">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Online Exam <?php 
              foreach ($exam_bank as $key) 
              {
                  $tile=$key->title;
                }
               echo $tile;
               if($exam_adm){
                 foreach ($exam_adm as $key) 
              {
                  echo $key->$admission_sch_name;
                }
               
               }
              ?>
                

              </h3>
                        <h3 style="float: right;">{{$course_name}}</h3>

    </div>
    <div class="card-body">
    <form enctype="multipart/form-data" method="post" name="question_form"  id="question_form">

        <?php

          if($exam_bank){
             foreach($exam_bank as $key_rec)
        $co_id=$key_rec->co_id;
        $course_id=$key_rec->fc_co_course_id;
        $fc_co_id=my_simple_crypt($co_id, 'e' );
      }else{

         $co_id='';
        $course_id='';
        $fc_co_id='';
      }

        
      ?>
    <input type="hidden" name="co_id" id="co_id" value="<?php echo $co_id;?>" >
    <input type="hidden" name="course_id" id="course_id" value="<?php echo $course_id;?>" >
    <input type="hidden" name="countoptions" id="countoptions" value="4" >
    <input type="hidden" name="adm_sch_id" id="adm_sch_id" value="<?=$sch_id;?> " >
    <input type="hidden" name="countexamtype" id="countexamtype"  value="1">
    <input type="hidden" name="adm_exam_id" id="adm_exam_id"  value="<?=$exam_id;?>">
    <input type="hidden"   name="current_date_time" id="current_date_time" >
   
     @csrf

    <table border="1" class="table table-bordered">
        <tr>
       <td> 
       
       <input type="hidden" name="edit_id" id="edit_id" class="form-control"   >
       <input type="hidden" name="select_option_val" id="select_option_val"  >
       
       </td>

      </tr>
      <tr>
          <td style="text-align-last: center;"> <strong>Exam Type </strong></td>
          
          <td> 
          <div style="float:left">
            <select onchange="exam_type_func(this)" name="exam_type" id="exam_type" class="form-control"  style="width:300px;" required="required">
              <option value="1">MCQ</option>
              <option value="2">Short Question</option>
              <option value="3">True False</option>  
              <option <?=($sch_id != 0)? 'style="display:none"' : ''?> value="4">Long Question</option>          
            </select>
               
             
             </div>
             <div style="float:right">
                 <?php if($sch_id != 0){ ?>

         <button type="button" onclick="adm_go_back('<?=$sch_id;?>')" class="btn btn-primary" >Go Back</button>
     
           <?php }else{?>
            <button type="button" onclick="go_back('<?=$fc_co_id?>')" class="btn btn-primary" >Go Back</button>
           
          <?php } ?>
            </div>
             </td>
        </tr>
       <tr style="display:none;">
          <td style="text-align-last: center;"> <strong>Exam Name </strong></td>
          <td> 
            
              <select name="exam_id" id="exam_id" class="form-control"  style="width:300px;" required="required">
                @foreach($exam_bank as $exam_rec)
            <option value="{{$exam_rec->id}}">{{$exam_rec->title}}</option>
             @endforeach
                </select>
               
             </td>
        </tr>
      <tr class="form-group"  >
          <td style="padding-top: 26px;text-align-last: center;"> <strong>Question</strong> </td>
          <td> <textarea class="form-control" rows="3" name="qname" id="qname" placeholder="Add Question" maxlength="2000" ></textarea>
            </td>
        </tr>         
        <tr id="wordlimit" style="display:none;">
          <td style="text-align-last: center;"><strong> Word Limit</strong> </td>
          <td>
          <div style="float:left">
          <input  type="number" name="word_limit" id="word_limit" class="form-control form-control-md"  style="width:300px;"  />
          </div>
          <div id="lq_disclaimer" style="display:none;color:red;float:left;margin-left:20px ">Disclaimer Only 500 word
           limit allowed</div>
          </td>         
        </tr>      
        <tr id="lq_file_require" style="display:none">
          <td style="text-align-last: center;"> <strong>File Required </strong></td>
          <td> 
            
            <select  name="file_require" id="file_require" class="form-control"  style="width:300px;" required="required">
              <option value="0">No</option>
              <option value="1">Yes</option>
                    
            </select>
               
             </td>
        </tr>
       
        <tr id="sqwordlimit" style="display:none;">
          <td style="text-align-last: center;"><strong> Word Limit</strong> </td>
          <td>
          <div style="float:left">
          <input  type="number" name="sq_word_limit" id="sq_word_limit" class="form-control form-control-md" style="width:300px;" />
          </div>
          <div id="sq_disclaimer" style="display:none;color:red;float:left;margin-left:20px ">Disclaimer Only 200 word limit allowed</div>

          </td>
          
        </tr>

        
        <tr id="sq_file_require" style="display:none">
          <td style="text-align-last: center;"> <strong>File Required </strong></td>
          <td> 
            
            <select  name="sqfile_require" id="sqfile_require" class="form-control"  style="width:300px;" required="required">
              <option value="0">No</option>
              <option value="1">Yes</option>
                    
            </select>
               
             </td>
        </tr>
        
        
        <tr id="a">
          <td style="text-align-last: center;"><strong> A</strong> </td>
          <td> <input type="text" name="option_arr[]" id="option_a" class="form-control form-control-md"  maxlength="1000" > </td>
        </tr>
        <tr id="b">
          <td style="text-align-last: center;"> <strong>B </strong></td>
          <td> <input type="text" name="option_arr[]" id="option_b"class="form-control form-control-md" maxlength="1000" > </td>
        </tr>
        <tr id="c">
          <td style="text-align-last: center;"> <strong>C </strong></td>
          <td> <input type="text" name="option_arr[]" id="option_c"class="form-control"  maxlength="1000" > </td>
        </tr>
        <tr id="d">
          <td style="text-align-last: center;"> <strong> D </strong></td>
          <td> <input type="text" name="option_arr[]" id="option_d"class="form-control form-control-md"  maxlength="1000" > </td>
        </tr>
        <tr id="e" style="display:none;">
          <td style="text-align-last: center;"> <strong> E </strong></td>
          <td> <input type="text" name="option_arr[]" id="option_e"class="form-control form-control-md"  maxlength="1000" > </td>
        </tr>
       
        <tr>
          <td style="text-align-last: center;"></td>
          <td id="addremove" scope="row"> <button type="button" class="btn btn-lg" onclick="addmore()"><i class="fa fa-plus"></i> Add More</button>
          <button type="button" class="btn btn-lg" onclick="removerow()"><i class="fa fa-plus"></i> Remove</button>
          </td>
          
           </tr>
        <tr id="correctanswer">
          <td  style="text-align-last: center;"><strong>Correct Answer</strong> </td>
          <td> 
              <select name="correct_option" class="form-control"  style="width:300px;" >
                    <option>A</option>
                    <option>B</option>
                    <option>C</option>
                    <option>D</option>
                    <option>E</option>                    
                </select>
             </td>
        </tr>
        <tr id="truefalse" style="display:none;">
          <td  style="text-align-last: center;"><strong>Correct Answer</strong> </td>
          <td> 
              <select name="true_false" class="form-control"  style="width:300px;" >
                    <option value="T">True</option>
                    <option value="F" >False</option>
                </select>
             </td>
        </tr>
            <tr>
          <td> </td>

          <td> <button type="button" class="btn btn-primary btn-lg btn-block" id="btnsubmit" onclick="add_question()">Submit</button>
          <button type="button" class="btn btn-warning btn-lg btn-block" onclick="add_question_reset()">Add New Question</button>
           </td>
        </tr>  

    </table>
    </form>
   
     <div id="error_msg" class="msg-div" style="display: none;color: red"><span></span></div>
    </div>
</div>

 <div id="saved_msg_div_file" style="display: none;color: green"><span>Question Add Successfully In Exam</span></div> 
    <div id="saved_msg_div_delete" style="display: none;color: green"><span>Question Delete Successfully In Bank</span></div> 
<!-- ///////////////////////////////////////////////////////////////////////////////// -->

<div class="card" style="margin-top: 20px">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" > Exam Question Bank </h3>

    </div>
   
    <div class="card-body">
<table id="simpletabledelete" class="table table-striped table-bordered table-responsive table-hover" width="100%">
  <thead >
    <tr style="background-color:#2F3B4D !important; color:#FFF;">
      <th scope="col" style="text-align:center">#</th>
      <th colspan="8" style="text-align:center">MCQ Question</th>
      <th scope="col" style="text-align:center">Option A</th>
      <th scope="col" style="text-align:center">Option B</th>
      <th scope="col" style="text-align:center">Option C</th>
      <th scope="col" style="text-align:center">Option D</th>
      <th scope="col" style="text-align:center">Option E</th>
      <th scope="col" style="text-align:center">Correct</th>
      <th scope="col"  style="text-align:center">Negative Marks %</th>
      <th scope="col" style="text-align:center">Exam Section</th>
      <th colspan="2" style="text-align:center">Action </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 1){
//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td colspan="8">{{$exam_bank_rec->question}}</td>
      <td>{{$exam_bank_rec->oa}}</td>
      <td>{{$exam_bank_rec->ob}}</td>
      <td>{{$exam_bank_rec->oc}}</td>
      <td>{{$exam_bank_rec->od}}</td>
      <td>{{$exam_bank_rec->oe}}</td>
      <td>{{$exam_bank_rec->correct}}</td>
      <td align="center">{{$exam_bank_rec->neg_marks}}</td>
      <td> 
       <select name="section_name" class="form-control" onchange="getSection(this,<?=$exam_bank_rec->id ;?>)">
            @foreach($examSectionResults as $rowexamSectionResults)
                <option <?=($rowexamSectionResults->id == $exam_bank_rec->exam_section_id)? 'selected="selected"' : ''?> value="{{ $rowexamSectionResults->id}}">{{ $rowexamSectionResults->section_name }}</option>
            @endforeach
      </select>
       <div id="saved_div<?=$exam_bank_rec->id;?>" style="display:none;font-size:12px;font-style: italic;color:green;text-align: center;font-weight: bold;">Saved</div>
      </td> 
      <td scope="row"> <button type="button"s class="btn btn-lg" onclick="edit_question1('<?=$exam_bank_rec->exam_question_bank_id;?>','<?=$exam_bank_rec->oa;?>','<?=$exam_bank_rec->ob;?>','<?=$exam_bank_rec->oc;?>','<?=$exam_bank_rec->od;?>','<?=$exam_bank_rec->oe;?>','<?=$exam_bank_rec->correct;?>','<?=$exam_bank_rec->exam_ques_type;?>','<?=$exam_bank_rec->word_limit;?>')"><i class="fa fa-edit"></i> Edit</button></td>
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
      <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-trash"></i> Remove</button></td>

    </tr>
     <?php
    $count++;
}
    ?>
    @endforeach

    <thead>
    <tr style="background-color:#2F3B4D !important; color:#FFF; "> 
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center" >Short Question</th>   
      <th colspan="1"  style="text-align:center">Word Limit</th>
      <th colspan="1"  style="text-align:center">Negative Marks %</th>
      <th colspan="1"  style="text-align:center">Exam Section</th>
      <th colspan="2"  style="text-align:center">Action </th>
    </tr>
  </thead>

  <tr>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 2){
//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td colspan="13">{{$exam_bank_rec->question}}</td>
      <td colspan="1">{{$exam_bank_rec->word_limit}}</td>
      <td align="center" colspan="1">{{$exam_bank_rec->neg_marks}}</td>
      <td> 
       <select name="section_name" class="form-control" onchange="getSection(this,<?=$exam_bank_rec->id ;?>)">
            @foreach($examSectionResults as $rowexamSectionResults)
                <option <?=($rowexamSectionResults->id == $exam_bank_rec->exam_section_id)? 'selected="selected"' : ''?> value="{{ $rowexamSectionResults->id}}">{{ $rowexamSectionResults->section_name }}</option>
            @endforeach
       </select>
       <div id="saved_div<?=$exam_bank_rec->id;?>" style="display:none;font-size:12px;font-style: italic;color:green;text-align: center;font-weight: bold;">Saved</div>

      </td> 
      <td scope="row"> <button type="button"s class="btn btn-lg" onclick="edit_question1('<?=$exam_bank_rec->exam_question_bank_id;?>','<?=$exam_bank_rec->oa;?>','<?=$exam_bank_rec->ob;?>','<?=$exam_bank_rec->oc;?>','<?=$exam_bank_rec->od;?>','<?=$exam_bank_rec->oe;?>','<?=$exam_bank_rec->correct;?>','<?=$exam_bank_rec->exam_ques_type;?>','<?=$exam_bank_rec->word_limit;?>','<?=$exam_bank_rec->file_required;?>')"><i class="fa fa-edit"></i> Edit</button></td>
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
      
       <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-trash"></i> Remove</button></td>
      
    </tr>
     <?php
    $count++;
}
    ?>
    @endforeach

     <thead>
    <tr style="background-color:#2F3B4D !important; color:#FFF; ">
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center">True False</th>        
      <th scope="col"  style="text-align:center">Correct </th>
      <th colspan="1"  style="text-align:center">Negative Marks %</th> 
      <th scope="col"  style="text-align:center">Exam Section </th>
      <th colspan="2"  style="text-align:center">Action </th>
    </tr>
  </thead>

  <tr>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 3){
//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td colspan="13">{{$exam_bank_rec->question}}</td>
      <td scope="col">{{$exam_bank_rec->correct}}</td>
      <td align="center" scope="col">{{$exam_bank_rec->neg_marks}}</td>
      <td> 
       <select name="section_name" class="form-control" onchange="getSection(this,<?=$exam_bank_rec->id ;?>)">
            @foreach($examSectionResults as $rowexamSectionResults)
                <option <?=($rowexamSectionResults->id == $exam_bank_rec->exam_section_id)? 'selected="selected"' : ''?> value="{{ $rowexamSectionResults->id}}">{{ $rowexamSectionResults->section_name }}</option>
            @endforeach
       </select>
       <div id="saved_div<?=$exam_bank_rec->id;?>" style="display:none;font-size:12px;font-style: italic;color:green;text-align: center;font-weight: bold;">Saved</div>
      </td> 
      <td scope="row"> <button type="button"s class="btn btn-lg" onclick="edit_question1('<?=$exam_bank_rec->exam_question_bank_id;?>','<?=$exam_bank_rec->oa;?>','<?=$exam_bank_rec->ob;?>','<?=$exam_bank_rec->oc;?>','<?=$exam_bank_rec->od;?>','<?=$exam_bank_rec->oe;?>','<?=$exam_bank_rec->correct;?>','<?=$exam_bank_rec->exam_ques_type;?>','<?=$exam_bank_rec->word_limit;?>')"><i class="fa fa-edit"></i> Edit</button></td>
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
     
       <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-trash"></i> Remove</button></td>
      
    </tr>
     <?php
    $count++;
}
    ?>
    @endforeach
    <thead>
    <tr <?=($sch_id != 0)? 'style="display:none"' : ' style="background-color:#2F3B4D !important; color:#FFF; "'?> >
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center" >Long Question</th>
      <th colspan="1"  style="text-align:center">Word Limit</th>  
      <th colspan="1"  style="text-align:center">Negative Marks %</th> 
      <th colspan="1"  style="text-align:center">Exam Section</th>      
      <th colspan="2"  style="text-align:center">Action </th>
    </tr>
  </thead>

  <tr <?=($sch_id != 0)? 'style="display:none"' : ''?>>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 4){
//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td colspan="13">{{$exam_bank_rec->question}}</td>
      <td colspan="1">{{$exam_bank_rec->word_limit}}</td>
      <td align="center" colspan="1">{{$exam_bank_rec->neg_marks}}</td>
      <td> 
       <select name="section_name" class="form-control" onchange="getSection(this,<?=$exam_bank_rec->id ;?>)">
            @foreach($examSectionResults as $rowexamSectionResults)
                <option <?=($rowexamSectionResults->id == $exam_bank_rec->exam_section_id)? 'selected="selected"' : ''?> value="{{ $rowexamSectionResults->id}}">{{ $rowexamSectionResults->section_name }}</option>
            @endforeach
       </select> 
       <div id="saved_div<?=$exam_bank_rec->id;?>" style="display:none;font-size:12px;font-style: italic;color:green;text-align: center;font-weight: bold;">Saved</div>
      </td> 
      <td scope="row"> <button type="button"s class="btn btn-lg" onclick="edit_question1('<?=$exam_bank_rec->exam_question_bank_id;?>','<?=$exam_bank_rec->oa;?>','<?=$exam_bank_rec->ob;?>','<?=$exam_bank_rec->oc;?>','<?=$exam_bank_rec->od;?>','<?=$exam_bank_rec->oe;?>','<?=$exam_bank_rec->correct;?>','<?=$exam_bank_rec->exam_ques_type;?>','<?=$exam_bank_rec->word_limit;?>','<?=$exam_bank_rec->file_required;?>')"><i class="fa fa-edit"></i> Edit</button></td>
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
     
       <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-trash"></i> Remove</button></td>

    </tr>
     <?php
    $count++;
}
    ?>
    @endforeach


  </tbody>
</table>


</div>
</div>

<hr>

<div id="saved_msg_div_file" style="display: none;color: green"><span>Question Add Successfully In Exam</span></div> 
    <div id="saved_msg_div_delete" style="display: none;color: green"><span>Question Delete Successfully In Bank</span></div> 

<hr>
<!-- //// print record /////////////// -->
<div class="card">  

   <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
    <h3><a href="{{route('teacher/question_bank')}}/<?=$fc_co_id?>" target="_blank" style="color:red; text-decoration:no1ne" ><b>Question Bank</b> </a>  </h3>

    </div>

    <div class="card-body" style="overflow-x:auto;">
<table id="simpletable" class="table table-striped table-bordered table-responsive table-hover"  width="100%">
  <thead>
    <tr>
      <th scope="col" style="text-align:center">#</th>
      <th scope="col" style="text-align:center">Question</th>
      <th scope="col" style="text-align:center">Option A</th>
      <th scope="col" style="text-align:center">Option B</th>
      <th scope="col" style="text-align:center">Option C</th>
      <th scope="col" style="text-align:center">Option D</th>
      <th scope="col" style="text-align:center">Option E</th>
      <th scope="col" style="text-align:center">Correct</th>
      <th scope="col" style="text-align:center">Word Limits</th>
      <th scope="col" style="text-align:center">Add</th>
      <th scope="col" style="text-align:center">Edit</th>
      <th scope="col" style="text-align:center">Remove</th>
    </tr>
  </thead>
  <tbody>
   
       <?php
      $count=1;
      ?>
    	@foreach($question_bank as $question_bank_rec)
      <tr style="background-color:<?=(1 == $question_bank_rec->count) ? '#8989ff' : '';?>">
    	<?php
    	 $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'    	
    	?>
      <th scope="row">{{$count}}</th>
      <td  scope="row" >{{$question_bank_rec->question}}</td>
     <td scope="row">{{$question_bank_rec->oa}}</td>
     <td scope="row">{{$question_bank_rec->ob}}</td>
     <td scope="row">{{$question_bank_rec->oc}}</td>
     <td scope="row">{{$question_bank_rec->od}}</td>
     <td scope="row">{{$question_bank_rec->oe}}</td>
     <td scope="row">{{$question_bank_rec->correct}}</td>
     <td scope="row">{{$question_bank_rec->word_limit}}</td>
      <td style="display: none;"> <input  type="text" name="ques" id="ques<?=$question_bank_rec->id;?>" class="form-control" value="{{$question_bank_rec->question}}"></td>
      
      <td  scope="row"> <button type="button" class="btn btn-lg" onclick="add_question_exam('<?=$question_bank_rec->id;?>')"><i class="fa fa-plus"></i> Add</button></td>
     
      <td scope="row"> <button type="button" class="btn btn-lg" onclick="edit_question('<?=$question_bank_rec->id;?>','<?=$question_bank_rec->oa;?>','<?=$question_bank_rec->ob;?>','<?=$question_bank_rec->oc;?>','<?=$question_bank_rec->od;?>','<?=$question_bank_rec->oe;?>','<?=$question_bank_rec->correct;?>','<?=$question_bank_rec->exam_ques_type;?>','<?=$question_bank_rec->word_limit;?>','<?=$question_bank_rec->file_required;?>')"><i class="fa fa-edit"></i> Edit</button></td>
       <td scope="row"> <button type="button" class="btn btn-lg" onclick="delete_question('<?=$question_bank_rec->id;?>')"><i class="fa fa-trash"></i> Remove</button></td>

    </tr>
    <?php
    $count++;
    ?>
    @endforeach
  </tbody>
</table>


</div>
</div>
</div>
@stop
@section('pageScripts')
<script>
  $( document ).ready(function() {

const formatter = new Intl.DateTimeFormat('eng', { month: 'short' });
const month1 = formatter.format(new Date());

setInterval(function(){ 
  var currentdate = new Date(); 
  var year = currentdate.getFullYear();
  year = year.toString().substr(-2);
  var datetime = year + "-"
              + month1  + "-" 
              + currentdate.getDate() + " "  
              + currentdate.getHours() + ":"  
              + currentdate.getMinutes() + ":" 
              + currentdate.getSeconds();

  // var dt = new Date();
  // var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds(); 
  $("#current_date_time").val(datetime);
},3000);
//alert(time);
});
function go_back(co_id){
//alert(co_id);
  window.location = '<?=url('/teacher/online_exam/')?>/'+co_id;
 
}
function adm_go_back(adm_sch_id){
//alert(co_id);
 window.location = '<?=url('/teacher/adm_online_exam/')?>/'+adm_sch_id;

}
function getSection(selectObject,QuizId) {
  var id = selectObject.value;  
  //alert(SectionId+"_____"+QuizId);
   //alert(QuizId);
  // return false;
  var fd = new FormData();
            
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: '{{url('teacher/updateexamsection')}}/'+id+'/'+QuizId,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
               //   console.log(data.message);
        		
            $("#saved_div"+QuizId).show();
          // $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
                  $("#saved_div"+QuizId).hide();

                  }, 2000);

                },
                complete: function(){
                }
            });
}

function addmore(){
 var countopt =  $("#countoptions").val();

  
  if(countopt < 5){
    countopt++;
  }
  
  $('#e').show();
  $("#countoptions").val(countopt);
  
 
}
function removerow(){
  var countopt =  $("#countoptions").val();


 if(countopt > 4){
   countopt--;
  }
$('#e').hide();
$("#option_e").val('');
$("#countoptions").val(countopt);

}
function add_question_reset(){
 // alert('reset');
  $('#edit_id').val('');
  $("#select_option_val").val('');
}
function exam_type_func(sel){
 // alert('ok');
var get_edit_id = parseInt($('#edit_id').val());
if(get_edit_id>0){
    $('#exam_type').val($("#select_option_val").val());
    alert('Quetion Type can not be changed');
    return false;
}

  var value = sel.value;  
  $("#countexamtype").val(value);
 // alert(value);
  if(value == 2){
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').show();
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
  }else if(value == 1){
    $('#error_msg span').html("");
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();    
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
  }else if(value == 3){
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
  }else{
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('#sq_file_require').hide();
  }
}


function add_question(){
  
//  alert('ok');  
  if($("#exam_type").val() == 1){
   
      var exam_id       = $("#exam_id").val();
      var qname       = $("#qname").val();
            var option_a        = $("#option_a").val();
             var option_b        = $("#option_b").val();
              var option_c        = $("#option_c").val();
               var option_d        = $("#option_d").val();
                var option_e        = $("#option_e").val();
                $('.form-control').removeClass('is-invalid');
        var error = 0;
        $('#error_msg').hide();
        if(qname == ''){
          $('#qname').addClass('is-invalid');
           $('#error_msg span').html('Please provide question!');
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
  }else if($("#exam_type").val() == 2){

    var exam_id       = $("#exam_id").val();
    var qname       = $("#qname").val();
    var option_a        = $("#option_a").val();
    var sq_word_limit        = $("#sq_word_limit").val();
    $('.form-control').removeClass('is-invalid');
    var error = 0;
    $('#error_msg').hide();
    if(qname == ''){
          $('#qname').addClass('is-invalid');
           $('#error_msg span').html('Please provide question!');
                error++;
            } 
            if(sq_word_limit == ''){
          $('#sq_word_limit').addClass('is-invalid');
           $('#error_msg span').html('Please provide Word Limit!');
                error++;
            } 
            
            if( error > 0 ){
                $('#error_msg').show();
                return false;
            }

  }else if($("#exam_type").val() == 3){

var exam_id       = $("#exam_id").val();
var qname       = $("#qname").val();
var option_a        = $("#option_a").val();
var option_b        = $("#option_b").val();
$('.form-control').removeClass('is-invalid');
var error = 0;
$('#error_msg').hide();
if(qname == ''){
      $('#qname').addClass('is-invalid');
       $('#error_msg span').html('Please provide question!');
            error++;
         } 
        // if(option_a== ''){
        //   $('#option_a').addClass('is-invalid');
        //   error++;
        // }if(option_b== ''){
        // $('#option_b').addClass('is-invalid');
        // error++;
        //     }
        if( error > 0 ){
            $('#error_msg').show();
            return false;
        }

}else if($("#exam_type").val() == 4){

var exam_id       = $("#exam_id").val();
var qname       = $("#qname").val();
var word_limit        = $("#word_limit").val();

$('.form-control').removeClass('is-invalid');
var error = 0;
$('#error_msg').hide();
if(qname == ''){
      $('#qname').addClass('is-invalid');
       $('#error_msg span').html('Please provide question!');
            error++;
        } 
        if(word_limit == ''){
      $('#word_limit').addClass('is-invalid');
       $('#error_msg span').html('Please provide Word Limit!');
            error++;
        } 
      
        if( error > 0 ){
            $('#error_msg').show();
            return false;
        }


}
//alert($("#word_limit").val());
  
  if($("#word_limit").val() > 500){
    $('#error_msg').show();
   $('#error_msg span').html('Word Limit cannot b greater than 500!');
   $("#word_limit").val('');
   setTimeout(function(){
                  $("#error_msg").hide();
      }, 3000);
    //return false;
  }else if($("#sq_word_limit").val() > 200){
    $('#error_msg').show();
   $('#error_msg span').html('Word Limit cannot b greater than 200!');
   $("#sq_word_limit").val('');
   setTimeout(function(){
                  $("#error_msg").hide();
      }, 3000);
  }else{

    var bi_form = document.querySelector('#question_form');
    var fd = new FormData(bi_form);
    //  fd.append("option_a", option_a)    
    $.ajax({
        type:'POST',
        url: '{{url('teacher/add_question')}}',
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
          
           if(data.message==2){
              //alert('No Question Added in this Exam greater then Total Question');
              alert('No Question Added in Exam greater then Total Question Due to Marks Changed');

              return false;
            }
             if(data.message==1){
              //alert(' Question Update Successfully');
            }
            $("#btnsubmit").addClass("btn-success");
      $("#btnsubmit").html('Question Add Successfully In Exam');
      $("#saved_msg_div_file").show();
          $("#qname").val('');
                    $("#option_a").val('');
                    $("#option_b").val('');
                    $("#option_c").val('');
                    $("#option_d").val('');
                    $("#option_e").val('');
                    $("#word_limit").val('');
                    $("#sq_word_limit").val('');
                    $("#lq_file_require").val('0');
                    $("#sq_file_require").val('0');
                $('.form-control').removeClass('is-invalid');
       $("#simpletable").load(window.location + " #simpletable");
       $("#simpletabledelete").load(window.location + " #simpletabledelete");


      setTimeout(function(){
                  $("#saved_msg_div_file").hide();
                  $("#btnsubmit").html('Submit');
                  $("#btnsubmit").removeClass("btn-success");
                  add_question_reset();
      }, 2000);
      },
        complete: function(){
        }
    });
  }
}

////// add question in exam///

function add_question_exam(id){
	
    var exam_id = $("#adm_exam_id").val();
    //alert(exam_id);adm_exam_id
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/exam_question')}}/'+id+'/'+exam_id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
			console.log(data.message);
        		if(data.status){
        			$("#saved_msg_div_file").show();
        			 $("#simpletable").load(window.location + " #simpletable");
                $("#simpletabledelete").load(window.location + " #simpletabledelete");
        		}else if(data.message==0){
				        alert('Question already Added in this Exam');
                //alert('No Question Added in Exam greater then Total Question Due to Marks Changed');

        		} else  if(data.message==2){
              alert('No Question Added in Exam greater then Total Question Due to Marks Changed');
              return false;
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
        url: '{{url('teacher/delete_examquestion')}}/'+id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
			console.log(data.message);
        		
        			$("#saved_msg_div_delete").show();
        			 $("#simpletabledelete").load(window.location + " #simpletabledelete");
               $("#simpletable").load(window.location + " #simpletable");
        		
        		setTimeout(function(){
                    $("#saved_msg_div_delete").hide();

                    }, 2000);
        },

        complete: function(){
        	
        }
    });
}

////// delete question in exam///

function delete_question(id){
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
              $("#simpletabledelete").load(window.location + " #simpletabledelete");

            setTimeout(function(){
                    $("#saved_msg_div_delete").hide();

                    }, 2000);
        },

        complete: function(){
          
        }
    });
}
function edit_question1(id,a,b,c,d,e,correc,type,word_limit,file_req){
//alert(file_req);
//$('#exam_type').attr("disabled", true); 

//$('#exam_type').attr('readonly', 'readonly');



$('#select_option_val').val(type);
$( "#exam_type" ).focus();
$('#exam_type').val(type);
var value = type;
if(value == 2){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#sqwordlimit').show();
    $('#wordlimit').hide();
    $('#sq_word_limit').val(word_limit);
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
    $('[name=sqfile_require]').val( file_req);

    
    var quest       = $("#quest"+id).val();
      $('#qname').val(quest);
  }else if(value == 1){
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();    
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#edit_id').val(id);
    $('#option_a').val(a);
    $('#option_b').val(b);
    $('#option_c').val(c);
    $('#option_d').val(d);
    $('#option_e').val(e);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
      var quest       = $("#quest"+id).val();
     // alert(quest)
      $('#qname').val(quest);
      $('[name=correct_option]').val( correc);
  }else if(value == 3){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    var quest       = $("#quest"+id).val();
      $('#qname').val(quest);
      $('[name=true_false]').val( correc);
  }else{
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#word_limit').val(word_limit);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('[name=file_require]').val( file_req);
    $('#sq_file_require').hide();
    
    var quest       = $("#quest"+id).val();
      $('#qname').val(quest);

  }

    }

function edit_question(id,a,b,c,d,e,correc,type,word_limit,file_req){
//alert(type);word_limit

$('#exam_type').val(type);
var value = type;
if(value == 2){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#sqwordlimit').show();
    $('#wordlimit').hide();
    $('#sq_word_limit').val(word_limit);
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
    $('[name=sqfile_require]').val( file_req);
   
    var quest       = $("#ques"+id).val();
      $('#qname').val(quest);
  }else if(value == 1){
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();    
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#edit_id').val(id);
    $('#option_a').val(a);
    $('#option_b').val(b);
    $('#option_c').val(c);
    $('#option_d').val(d);
    $('#option_e').val(e);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
      var quest       = $("#ques"+id).val();
      $('#qname').val(quest);
      $('[name=correct_option]').val( correc);
  }else if(value == 3){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    var quest       = $("#ques"+id).val();
      $('#qname').val(quest);
      $('[name=true_false]').val( correc);
  }else{
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#word_limit').val(word_limit);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('[name=file_require]').val( file_req);
    $('#sq_file_require').hide();
    
    var quest       = $("#ques"+id).val();
      $('#qname').val(quest);

  }

    }

</script>
@stop




