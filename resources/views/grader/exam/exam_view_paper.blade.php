<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
//print_r($sess);
//exit;

// dd("123");
?>
@extends('layouts.app',['pageTitle' => 'Exam'])

@section('content')

<?php
$fc_co_id='';
// print_r($exam_bank);exit;

  $co_id=$exam_bank[0]->co_id;
  $co_course_id=$exam_bank[0]->fc_co_course_id;
$co_id=my_simple_crypt($co_id,'e');
$course_id=my_simple_crypt($co_course_id,'e');
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  :root {
    --exam-primary: #17675f;
    --exam-primary-dark: #0f514b;
    --exam-primary-soft: rgba(23, 103, 95, 0.08);
    --exam-title: #1e293b;
    --exam-text: #475569;
    --exam-muted: #64748b;
    --exam-border: #e2e8f0;
    --exam-surface: #ffffff;
    --exam-surface-soft: #f8fafc;
  }

  .content-wrapper,
  .content,
  .main-content {
    background: linear-gradient(270deg, rgba(23, 103, 95, 0.15) 0%, rgba(255, 255, 255, 0.15) 100%);
  }

  .main-content {
    padding: 32px 24px !important;
    min-height: 100vh;
    font-family: 'Montserrat', sans-serif;
    color: var(--exam-title);
  }

  .exam-paper-card {
    margin-top: 0 !important;
    border: 0;
    border-radius: 16px;
    background: var(--exam-surface);
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .exam-paper-card .card-header {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    min-height: 92px;
    padding: 20px 24px;
    border: 0;
    border-bottom: 1px solid var(--exam-border);
    background: var(--exam-surface);
  }

  .exam-paper-card .card-header::after {
    display: none;
  }

  .exam-paper-title-wrap,
  .exam-paper-actions {
    float: none !important;
  }

  .exam-paper-title {
    margin: 0;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px;
    font-weight: 600;
    line-height: 1.4;
    color: var(--exam-title);
  }

  .exam-paper-title::after {
    content: 'Review all questions included in this exam';
    display: block;
    margin-top: 2px;
    font-size: 13px;
    font-weight: 400;
    color: var(--exam-muted);
  }

  .exam-paper-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--exam-muted);
    white-space: nowrap;
  }

  .exam-paper-actions .btn {
    min-height: 42px;
    padding: 10px 18px;
    border: 1px solid var(--exam-primary);
    border-radius: 10px;
    background: var(--exam-primary);
    box-shadow: none;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
  }

  .exam-paper-actions .btn:hover,
  .exam-paper-actions .btn:focus {
    border-color: var(--exam-primary-dark);
    background: var(--exam-primary-dark);
    color: #fff;
    transform: translateY(-1px);
  }

  .exam-paper-card .card-body {
    padding: 24px;
  }

  #simpletabledelete_wrapper {
    width: 100%;
  }

  #simpletabledelete {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0;
    border: 1px solid var(--exam-border);
    border-radius: 12px;
    overflow: hidden;
    background: var(--exam-surface);
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    color: var(--exam-text);
  }

  #simpletabledelete th {
    white-space: nowrap !important;
    vertical-align: middle;
  }

  #simpletabledelete td {
    white-space: normal !important;
    word-break: break-word;
    vertical-align: middle;
  }

  #simpletabledelete thead tr {
    background: var(--exam-primary-soft) !important;
    color: var(--exam-primary) !important;
  }

  #simpletabledelete thead th {
    padding: 14px 12px;
    border-color: var(--exam-border);
    border-top: 0;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: .035em;
    text-transform: uppercase;
  }

  #simpletabledelete tbody th,
  #simpletabledelete tbody td {
    padding: 14px 12px;
    border-color: var(--exam-border);
    background: var(--exam-surface);
    line-height: 1.6;
  }

  #simpletabledelete tbody th[scope='row'] {
    min-width: 52px;
    font-weight: 700;
    color: var(--exam-primary);
    text-align: center;
    background: var(--exam-surface-soft);
  }

  #simpletabledelete.table-striped tbody tr:nth-of-type(odd) td,
  #simpletabledelete.table-striped tbody tr:nth-of-type(odd) th {
    background: #fbfdfd;
  }

  #simpletabledelete.table-hover tbody tr:hover td,
  #simpletabledelete.table-hover tbody tr:hover th {
    background: rgba(23, 103, 95, 0.045);
  }

  #simpletabledelete img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
  }

  .dataTables_wrapper .dataTables_scroll,
  .dataTables_wrapper .dataTables_scrollBody {
    border-radius: 12px;
  }

  .dataTables_wrapper .dataTables_scrollBody {
    border-bottom: 0 !important;
  }

  @media (max-width: 767.98px) {
    .main-content {
      padding: 16px 12px !important;
    }

    .exam-paper-card .card-header {
      align-items: flex-start;
      flex-direction: column;
      padding: 18px;
    }

    .exam-paper-actions {
      width: 100%;
      justify-content: space-between;
      white-space: normal;
    }

    .exam-paper-card .card-body {
      padding: 14px;
    }

    .exam-paper-title {
      font-size: 18px;
    }
  }
</style>


<div class="card exam-paper-card" style="margin-top: 20px">     
    <div class="card-header" style="display:inline !important">
      <div class="exam-paper-title-wrap" style="float:left;">
          <h3 class="exam-paper-title" style="font-family:'Times New Roman', Times, serif; " > Instructor View Paper </h3>
      </div>
      <div class="exam-paper-actions" style="float:right !important;">Total Questions: <?=$exam_questinbank[0]->total_marks?>
         <button type="button" onclick="window.history.back()" class="btn btn-primary" >Go Back</button>
         {{-- <button type="button" onclick="go_back('<?=$co_id?>','<?=$course_id?>')" class="btn btn-primary" >Go Back</button> --}}
      </div>  
    </div>
     
    <div class="card-body">
      <div style="float:right"> 
      </div>
<table id="simpletabledelete" class="table table-striped table-bordered table-hover w-100">
  <thead >
  <!-- style="background-color:#D2D6F1 !important; color:#FFF;" -->
    <tr style="background-color:rgba(23, 103, 95, 0.08) !important; color:#17675f;">
      <th scope="col" style="text-align:center">#</th>
      <th colspan="8" style="text-align:center">MCQ Question</th>
      <th scope="col" style="text-align:center">Option A</th>
      <th scope="col" style="text-align:center">Option B</th>
      <th scope="col" style="text-align:center">Option C</th>
      <th scope="col" style="text-align:center">Option D</th>
      <th scope="col" style="text-align:center">Option E</th>
      <th scope="col" style="text-align:center">Correct</th>
      <th scope="col"  style="text-align:center">Negative Marks %</th>
      <th scope="col"  style="text-align:center">Total Marks</th>
      
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
      <td colspan="8">
        @if($exam_bank_rec->question !== strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
      </td>
      <td>{{$exam_bank_rec->oa}}</td>
      <td>{{$exam_bank_rec->ob}}</td>
      <td>{{$exam_bank_rec->oc}}</td>
      <td>{{$exam_bank_rec->od}}</td>
      <td>{{$exam_bank_rec->oe}}</td>
      <td>{{$exam_bank_rec->correct}}</td>
      <td align="center">{{$exam_bank_rec->neg_marks}}</td>
      <td align="center">{{$exam_bank_rec->question_marks}}</td>
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
      </tr>
     <?php
    $count++;
}
    ?>
    @endforeach

    <thead>
    <tr style="background-color:rgba(23, 103, 95, 0.08) !important; color:#17675f;">
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center" >Short Question</th>   
      <th colspan="1"  style="text-align:center">Word Limit</th>
      <th colspan="1"  style="text-align:center">Negative Marks %</th>
      <th scope="col"  style="text-align:center">Total Marks</th>     
    </tr>
  </thead>
        <tr>
        <?php
        
        $count=1;
        ?>
        @foreach($exam_questinbank as $exam_bank_rec)
            <?php
           
            if($exam_bank_rec->exam_ques_type == 2){
            ?>
            <th scope="row">{{$count}}</th>
            <td colspan="13">
               @if($exam_bank_rec->question !== strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
            </td>
            <td colspan="1">{{$exam_bank_rec->word_limit}}</td>
            <td align="center" colspan="1">{{$exam_bank_rec->neg_marks}}</td>
            <td align="center" colspan="1">{{$exam_bank_rec->question_marks}}</td>
            <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
            </tr>
            <?php
            $count++;
            }
            ?>
        @endforeach
     
     <thead>
    <tr style="background-color:rgba(23, 103, 95, 0.08) !important; color:#17675f;">
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center">True False</th>        
      <th scope="col"  style="text-align:center">Correct </th>
      <th colspan="1"  style="text-align:center">Negative Marks %</th> 
      <th scope="col"  style="text-align:center">Total Marks</th>     
    </tr>
  </thead>
    <tr>
    <?php
    $count=1;
    ?>
    @foreach($exam_questinbank as $exam_bank_rec)
    <?php
    if($exam_bank_rec->exam_ques_type == 3){      
    ?>
    <th scope="row">{{$count}}</th>
    <td colspan="13">
        @if($exam_bank_rec->question !== strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
    </td>
    <td scope="col">{{$exam_bank_rec->correct}}</td>
    <td align="center" scope="col">{{$exam_bank_rec->neg_marks}}</td>
    <td align="center" scope="col">{{$exam_bank_rec->question_marks}}</td>
    <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
    </tr>
    <?php
    $count++;
    }
    ?>
    @endforeach

    <thead>
    <tr style="background-color:rgba(23, 103, 95, 0.08) !important; color:#17675f;">
      <th scope="col"  style="text-align:center">#</th>
      <th colspan="13" style="text-align:center" >Long Question</th>
      <th colspan="1"  style="text-align:center">Word Limit</th>  
      <th colspan="1"  style="text-align:center">Negative Marks %</th> 
      <th colspan="1"  style="text-align:center">Total Marks</th> 
      <!-- <th colspan="1"  style="text-align:center">Exam Section</th>      
      <th colspan="2"  style="text-align:center">Action </th> -->
    </tr>
  </thead>

  <tr>
      <?php
      $count=1;
      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 4){
//       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'
      
      ?>
      <th scope="row">{{$count}}</th>
      <td colspan="13">
          @if($exam_bank_rec->question !== strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
      </td>
      <td colspan="1">{{$exam_bank_rec->word_limit}}</td>
      <td align="center" colspan="1">{{$exam_bank_rec->neg_marks}}</td>
      <td align="center" colspan="1">{{$exam_bank_rec->question_marks}}</td>
      <!-- <td> 
       <select name="section_name" class="form-control" onchange="getSection(this,<?=$exam_bank_rec->id ;?>)">
            @foreach($examSectionResults as $rowexamSectionResults)
                <option <?=($rowexamSectionResults->id == $exam_bank_rec->exam_section_id)? 'selected="selected"' : ''?> value="{{ $rowexamSectionResults->id}}">{{ $rowexamSectionResults->section_name }}</option>
            @endforeach
       </select> 
       <div id="saved_div<?=$exam_bank_rec->id;?>" style="display:none;font-size:12px;font-style: italic;color:green;text-align: center;font-weight: bold;">Saved</div>
      </td>  -->
      <!-- <td scope="row"> <button type="button"s class="btn btn-lg" onclick="edit_question1('<?=$exam_bank_rec->exam_question_bank_id;?>','<?=$exam_bank_rec->oa;?>','<?=$exam_bank_rec->ob;?>','<?=$exam_bank_rec->oc;?>','<?=$exam_bank_rec->od;?>','<?=$exam_bank_rec->oe;?>','<?=$exam_bank_rec->correct;?>','<?=$exam_bank_rec->exam_ques_type;?>','<?=$exam_bank_rec->word_limit;?>','<?=$exam_bank_rec->file_required;?>')"><i class="fa fa-edit"></i> Edit</button></td> -->
      <td style="display: none;"> <input  type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}"></td>
     
       <!-- <td> <button type="button"s class="btn btn-lg" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>')"><i class="fa fa-trash"></i> Remove</button></td> -->

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

@stop
@section('javascript')
<script>
  function go_back(co_id,course_id){
//alert(co_id);
  window.location = '<?=url('/teacher/online_exam/')?>/'+co_id+'/'+course_id;
 
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
function exam_type_func(sel){
 
 
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
      }, 2000);
      },
        complete: function(){
        }
    });
  }
}

////// add question in exam///

function add_question_exam(id){
	
    var exam_id = $("#exam_id").val();
    //alert(exam_id);
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
				        alert('Question Already Added In This Exam');
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
$('#simpletabledelete').DataTable({
    scrollX: true,
    autoWidth: false
});
</script>
@stop




