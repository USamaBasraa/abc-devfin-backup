<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
// print_r($result);
// exit;

?>
@extends('layouts.app',['pageTitle' => 'View Result'])
@section('content')
<style type="text/css">
.correct_option{
    background-color: green;
    color: white;
    padding: 4px;
}
.main-content {
        padding-left: 12px;
    padding-right: 12px;
}
.page-header {
    margin-top: 0px !important;
}

</style>


<div class="page-header" style="margin-bottom: 0px;">
    <div class="page-header-title" style="">
    <h4 style="color: #1E293B;
    font-size: 20px;
    font-style: normal;
    font-weight: 600;
    margin-bottom: 0px;
    line-height: normal;">Mark Exam</h4>
    @php
    $examTypesCount = collect($result)
        ->pluck('exam_ques_type')
        ->unique()
        ->count();
@endphp

<p style="color: #64748B;font-size: 14px;font-weight: 400;">
    Showing {{ $examTypesCount }} Exam Types
</p>
    </div>
 
</div>
<div class="page-body">


<div id="source-html">
<div >
<!-- Print Table Start Here  -->
<div class="card" style="background: none !important;">
    <div class="card-header" style="background-color:#FFF !important;border-radius:10px;display:none;">
        <div style="float:left !important;border:0px solid black">
            <h3 style="font-family:'Times New Roman', Times, serif;margin-top:15.840px;" >
                <?=$sqlresult[0]->title.' ( '.$result[0]->spi_name.' - '.$result[0]->sai_enrollment_no.' ) '?>
            </h3>
        </div>
        <?php if($result[0]->answer_file != ''){?>
        <div class="col-12 col-sm-2" style="margin-left:15px;float:left;padding:4px !important;  border:solid 0px #FF0000;">
            <a href="{{asset('https://student.bnu.edu.pk/images/'.$result[0]->answer_file)}}"  > <img src="{{asset('images/download.png')}}" > </a>
        </div>
        <?php } ?>
      
        <div style="float: right !important;border:0px solid black">
        <h3 style="margin-top: 15.840px;"><?=$result[0]->course_name;?></h3>
        </div>
    </div>

    {{-- paper title new section start --}}
    <div class="student-course-card">
    <div class="info-box">
        <div class="icon-box">
            <img src="{{ asset('images/profileImg.png') }}" alt="Student" width="39" height="39">
        </div>

        <div class="info-text">
            <p class="label">STUDENT</p>
            <h6 style="margin-bottom: 3px;">{{ $result[0]->spi_name }}</h6>
            <p class="sub-text">{{ $result[0]->sai_enrollment_no }}</p>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-box">
        <div class="icon-box">
            <img src="{{ asset('images/courseImg.png') }}" alt="Course" width="39" height="39">
        </div>

        <div class="info-text">
            <p class="label">COURSE</p>
            <h6>{{ $result[0]->course_name }}</h6>
            {{-- <p class="sub-text">Section A · Final Term</p> --}}
        </div>
    </div>
</div>
<style>
.student-course-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 40px;
    max-width: 100%;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
}

.info-box {
    display: flex;
    align-items: center;
    gap: 12px;
}

.icon-box {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-text p,
.info-text h6 {
    margin: 0;
}

.info-text .label {
    color: #64748B;
    font-size: 10px;
    font-weight: 500;
    line-height: 1.2;
}

.info-text h6 {
    color: #0F172A;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
}

.info-text .sub-text {
    color: #64748B;
    font-size: 10px;
    font-weight: 400;
    line-height: 1.3;
}

.divider {
    width: 1px;
    height: 30px;
    background: #E2E8F0;
}
</style>
    {{-- paper title new section end --}}

  </div>

<!-- ////////////////////////// print mcq ///////////////// -->
<div class="card">
    <div class="card-header" style="background-color:#17675F !important; color:#FFF; ">
        <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; margin-top:15.840px;" >
        Exam Type : MCQ's
        </h3>
    </div>
    <div class="card-body" id="table_div">

        {{-- <table class="table table-hover">
            <thead>
                <th></th><th></th><th></th>
            </thead>

            <tbody>
                <tr data-toggle="collapse" data-target="#accordion" class="clickable">
                    <td>Some Stuff</td>
                    <td>Some more stuff</td>
                    <td>And some more</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="accordion" class="collapse">Hidden by default</div>
                    </td>
                </tr>
            </tbody>
        </table> --}}
<style>
    .mcq-table td,
.mcq-table th {
    vertical-align: middle !important;
}

.mcq-table .question-cell table,
.mcq-table .question-cell table tbody,
.mcq-table .question-cell table tr,
.mcq-table .question-cell table td {
    vertical-align: middle !important;
}

.mcq-table .question-cell table td {
    padding: 0 !important;
    border: 0 !important;
    white-space: normal !important;
}
</style>
<table class="table table-hover table-striped mcq-table">
  <thead>
     <tr>
          <td align="left" class="question-cell" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Question</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Answer Status</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Selected Option</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Correct Option</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>All Options</b></td>
      </tr>
  </thead>
  <tbody>
    <?php
 $questionNo = 1;
foreach($result as $question_bank_rec):

 if($question_bank_rec->exam_ques_type == 1){

  ?>
  <tr data-toggle="collapse" data-target="#accordion<?=$questionNo;?>" class="clickable ">

     <?php
     $answer_given = $question_bank_rec->answer_given;
     $question = $question_bank_rec->question;
     $answer_status = $question_bank_rec->answer_status;
     $correct_answer = $question_bank_rec->correct;
     $optA = $question_bank_rec->oa;
     $optB = $question_bank_rec->ob;
     $optC = $question_bank_rec->oc;
     $optD = $question_bank_rec->od;

      ?>

      {{-- <td align="left"><?=$question;?></td> --}}
      <td align="left">
    <span><?= $question; ?></span>
</td>
      <td align="center" style="display: none;"><?=($answer_status=='F'?'<span class="answer_status">Wrong</span>':($answer_status=='M'?'Missing':'Correct'))?>

    </td>
    <td align="center">
    <?php if($answer_status == 'F'){ ?>
        <span class="answer_status" style="background-color:#FEE2E280;border-radius:20px;font-size: 12px;font-weight: 600;padding: 5px 10px;">Wrong</span>
    <?php } elseif($answer_status == 'M'){ ?>
        <span style="background-color: #FFF3CD80; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;">
            Missing
        </span>
    <?php } else { ?>
        <span style="background-color:#D1FAE580;border-radius:20px;font-size: 12px;font-weight: 600;padding: 5px 10px;"> Correct </span>
    <?php } ?>
</td>

      <td align="center">
    <span style="<?= $answer_status == 'M' ? 'background-color: #FFF3CD80; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;' : '' ?>">
        <?= ($answer_status == 'M' ? 'Missing' : $answer_given) ?>
    </span>
</td>
      <td align="center" ><?=$correct_answer?> </td>
      <td  align="center" > <span style="border-bottom: 1px solid #1F2937;">View All</span></i>      </td>
      </tr>
<tr>
    <td colspan="5" style="padding: 0px !important;">
    <div id="accordion<?= (int) $questionNo; ?>" class="collapse">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Option</th>
                    <th class="text-center">Option Title</th>
                </tr>
            </thead>

            <tbody>
                <?php
                    $options = [
                        'A' => $optA ?? '',
                        'B' => $optB ?? '',
                        'C' => $optC ?? '',
                        'D' => $optD ?? '',
                        'E' => $question_bank_rec->oe ?? '',
                        'F' => $question_bank_rec->option_f ?? '',
                        'G' => $question_bank_rec->og ?? '',
                        'H' => $question_bank_rec->oh ?? '',
                        'I' => $question_bank_rec->oi ?? '',
                    ];

                    $correctAnswer = strtoupper(trim($correct_answer ?? ''));

                    foreach ($options as $optionKey => $optionTitle) {
                        $optionTitle = trim((string) $optionTitle);

                        // Handles normal empty values and HTML empty values like &nbsp; or <p>&nbsp;</p>
                        $cleanOptionTitle = trim(
                            html_entity_decode(
                                strip_tags($optionTitle),
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            )
                        );

                        if ($cleanOptionTitle === '') {
                            continue;
                        }
                ?>
                    <tr>
                        <th class="text-center">
                            <?= htmlspecialchars($optionKey, ENT_QUOTES, 'UTF-8'); ?>
                        </th>

                        <td class="text-center">
                            <?= htmlspecialchars($optionTitle, ENT_QUOTES, 'UTF-8'); ?>

                            <?php if ($correctAnswer === $optionKey) { ?>
                                <span class="correct_option">Correct Option</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</td>
    <td colspan="5" style="padding: 0px !important;display:none;">
        <div id="accordion<?=$questionNo;?>" class="collapse">
            <table class="table table-bordered">
                <tr>
                    <td align="center"><b>Option </b></td>
                    <td align="center"><b>Option Title</b></td>
                </tr>
                <tr><th align="center" style="
    text-align: center;
">A</th><td align="center" ><?=$optA?><?php if($correct_answer=='A'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">B</th><td align="center" ><?=$optB?><?php if($correct_answer=='B'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">C</th><td align="center" ><?=$optC?><?php if($correct_answer=='C'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">D</th><td align="center" ><?=$optD?><?php if($correct_answer=='D'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">E</th><td align="center" ><?=$question_bank_rec->oe;?><?php if($correct_answer=='E'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">F</th><td align="center" ><?=$question_bank_rec->option_f;?><?php if($correct_answer=='F'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">G</th><td align="center" ><?=$question_bank_rec->og;?><?php if($correct_answer=='G'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">H</th><td align="center" ><?=$question_bank_rec->oh;?><?php if($correct_answer=='H'){ ?><span class="correct_option">Correct Option</h2> <?php } ?> </td></tr>
                <tr><th align="center" style="
    text-align: center;
">I</th><td align="center" ><?=$question_bank_rec->oi;?> <?php if($correct_answer=='I'){ ?><span class="correct_option">Correct Option</h2> <?php } ?></td></tr>
            </table>
        </div>
    </td>
</tr>
<?php
$questionNo++;
}
endforeach
?>
  </tbody>
</table>
</div>
</div>



<!-- ////////////////// print true false //////////////////// -->
<style>
    .true-false-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}

.true-false-table th,
.true-false-table td {
    vertical-align: middle !important;
    padding: 22px 16px !important;
}

.true-false-question-col {
    width: 65%;
}

.true-false-status-col {
    width: 12%;
}

.true-false-given-col {
    width: 12%;
}

.true-false-correct-col {
    width: 11%;
}

.true-false-question {
    color: #33456f;
    font-size: 16px;
    line-height: 1.6;
}

.true-false-question table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: collapse !important;
}

.true-false-question table td {
    padding: 0 !important;
    border: 0 !important;
    white-space: normal !important;
    vertical-align: middle !important;
}

.tf-badge {
    background-color: #FFF3CD80;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    min-width: 72px;
    text-align: center;
}

.tf-header-cell {
    font-size: 12px;
    color: #6B7280;
    font-weight: 600;
    white-space: nowrap;
}
</style>
  <div class="card">
  <div class="card-header" style="background-color:#17675F !important; color:#FFF; ">
      <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; margin-top:15.840px;" >
      Exam Type : True & False
      </h3>

  </div>
  <div class="card-body" id="table_div">

<table class="table table-striped true-false-table">
    
    
   <colgroup>
        <col class="true-false-question-col">
        <col class="true-false-status-col">
        <col class="true-false-given-col">
        <col class="true-false-correct-col">
    </colgroup>


<thead>
   <tr>
          <td align="left" class="tf-header-cell" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Question</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Answer Status</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Answer Given</b></td>
          <td align="center" style="font-size: 12px;color: #6B7280;font-weight: 600;"><b>Correct Answer</b></td>

        <td style="display:none" align="center"><b>Action</b></td>
    </tr>
</thead>
<tbody>
    <?php
    foreach($result as $question_bank_rec):

 if($question_bank_rec->exam_ques_type == 3){

  ?>
<tr>

   <?php
   $answer_given = $question_bank_rec->answer_given;
   $question = $question_bank_rec->question;
   $answer_status = $question_bank_rec->answer_status;
   $correct_answer = $question_bank_rec->correct;

   ?>

      <td align="left" class="true-false-question"><?=$question?></td>
      <td align="center">
    {{-- <?php if($answer_status == 'M'){ ?>
        <span style="background-color: #FFF3CD80; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;">
            Missing
        </span>
    <?php } else { ?>
        <?= $answer_status ?>
    <?php } ?> --}}

    <?php if($answer_status == 'M'){ ?>
        <span style="background-color: #FFF3CD80; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;">
            Missing
        </span>
    <?php } elseif($answer_status == 'F'){ ?>
        <span style="background-color: #FEE2E280; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> False </span>
    <?php } elseif($answer_status == 'T'){ ?>
        <span style="background-color: #D1FAE580; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> True </span>
    <?php } else { ?>
        <?= $answer_status ?>
    <?php } ?>
</td>
      {{-- <td align="center" ><?=$answer_given?> </td> --}}
      <td align="center">
    <?php
        $givenAnswer = strtoupper(trim((string) $answer_given));
    ?>

    <?php if($givenAnswer == ''){ ?>
        <span style="background-color: #FFF3CD80; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;">
            Missing
        </span>
    <?php } elseif($givenAnswer == 'T'){ ?>
        <span style="background-color: #D1FAE580; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> True </span>
    <?php } elseif($givenAnswer == 'F'){ ?>
        <span style="background-color: #FEE2E280; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> False </span>
    <?php } else { ?>
        <?= htmlspecialchars($answer_given, ENT_QUOTES, 'UTF-8') ?>
    <?php } ?>
</td>
      {{-- <td align="center" ><?=$correct_answer?> </td> --}}

      <td align="center">
    <?php
        $correctAnswer = strtoupper(trim((string) $correct_answer));
    ?>

    <?php if($correctAnswer == 'T'){ ?>
        <span style="background-color: #D1FAE580; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> True </span>
    <?php } elseif($correctAnswer == 'F'){ ?>
        <span style="background-color: #FEE2E280; border-radius: 20px; padding: 5px 10px; font-size: 12px; font-weight: 600;"> False </span>    <?php } else { ?>
        <?= htmlspecialchars($correct_answer, ENT_QUOTES, 'UTF-8') ?>
    <?php } ?>
</td>


    <td style="display:none" align="center"><button class="btn btn-primary" type="button" data-toggle="modal" data-target="#checkpaperreason"  >Reason </button></a></td>



</tr>

<?php

 }
 endforeach
?>

</tbody>
</table>
</div>
</div>


<!-- ////////////////////// print short question ////////////////// -->
    <div class="card">
    <div class="card-header" style="background-color:#17675F !important; color:#FFF; ">
        <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; margin-top:15.840px;" >
        Exam Type : Short Question Answer
        </h3>
    </div>
    <div class="card-body" id="table_div">

<table class="table table-striped">
  <thead>
     <tr>

      </tr>
  </thead>
  <tbody>
     <?php
    foreach($result as $question_bank_rec):
      if($question_bank_rec->exam_ques_type == 2){
     ?>
     <tr>
     {{-- <td align="left" style="border-top:1px solid black"><b>Question</b> --}}
     <td align="left" style="font-size: 18px;font-weight: 600;color: #1E293B;"><b>Question</b>


<div style="float:right">

<input id="{{$question_bank_rec->ceqp_id}}ob_marks" class="ob_marks" type="text" onkeyup="studentatt('{{$question_bank_rec->ceqp_id}}','{{$question_bank_rec->sai_enrollment_no}}','{{$question_bank_rec->course_exam_papers_id}}','{{$question_bank_rec->question_marks}}')" spellcheck="false" autofocus onfocus="this.value = this.value;" value="{{$question_bank_rec->question_obtained_marks}}" style="width:50%;"></input>
<p class="total_marks_show"> / {{$question_bank_rec->question_marks}}</p>
<!-- <img id="{{$question_bank_rec->ceqp_id}}image" style="display:none" src="{{asset('images/green_cog.png')}}" width="20px"/> -->
<!-- <img id="{{$question_bank_rec->ceqp_id}}imageerror" style="display:none" src="{{asset('images/red_cog.png')}}" width="20px"/> -->

<div id="{{$question_bank_rec->ceqp_id}}saved_msg_div_file" style="display:none;color: green;"><span>Marks Saved</span></div>

</div>
<div style="display:none;float:right;color: red;padding-right:10px" id="{{$question_bank_rec->ceqp_id}}saved_msg_div_file_error"  ><span>Obtained marks should be less than total marks</span></div>


</div>



     </td>
     </tr>
  <tr>
      <td align="left">
    @php
        $question = $question_bank_rec->question;
    @endphp

    @if($question !== strip_tags($question))
        {!! $question !!}
    @else
        {{ $question }}
    @endif
</td>
      </tr>
      <tr>
        @php
         $answerGiven = trim((string) ($question_bank_rec->answer_given ?? ''));
          $uploadedFile = trim((string) ($question_bank_rec->one_in_one ?? ''));
    $hasAnswer = $answerGiven !== '';
    $hasUploadedFile = $uploadedFile !== '';
@endphp
      <td align="left"><b>Answer Given</b>
      <?php if($question_bank_rec->one_in_one != ''){?>
        
      <div class="col-12 col-sm-2 text-right" style="float:right;">
      <a href="{{asset('https://student.bnu.edu.pk/images/'.$question_bank_rec->one_in_one)}}"  style="color:black"><i class="fa fa-upload"></i> Uploaded File</a></td>
     </div>
      <?php } ?></td>
      </tr>
      {{-- @if(!empty(trim($question_bank_rec->answer_given))) --}}
      <tr>
      {{-- <td align="left" >{{$question_bank_rec->answer_given }} </td> --}}

         <td align="left">
        @if($hasAnswer)
           {{$question_bank_rec->answer_given }}
        @elseif(!$hasUploadedFile)
            <span style="color:#DC2626; font-weight:600;">Not Answered</span>
        @else
            <span style="color:#64748B; font-style:italic;">Answer uploaded as file</span>
        @endif
    </td>

      </tr>
        {{-- @endif --}}
      <tr>
      <td align="left">
       <textarea
    id="teacher_remarks{{ $question_bank_rec->ceqp_id }}"
    onkeyup="teacherremarks('{{ $question_bank_rec->ceqp_id }}')"
    style="width:100%; height:100px;border-radius: 8px;
border: 1px solid #E2E8F0;
background: #FFF;padding:10px;"
    name="Text1"
    placeholder="Enter your remarks or feedback here...">{{ $question_bank_rec->teacher_remarks }}</textarea>
       <div id="saved_div{{$question_bank_rec->ceqp_id}}" style="display:none;font-size:12px;font-style: italic;color:green;text-align: left;font-weight: bold;">Saved</div>
       </td>
      </tr>
      <!-- <td align="center"><button class="<?=($question_bank_rec->question_obtained_marks != '') ? 'btn btn-success' : 'btn btn-primary'?>" type="button" data-toggle="modal" data-target="#checkpaperreason" onclick="updtmarks('{{$question_bank_rec->ceqp_id}}','{{$question_bank_rec->sai_enrollment_no}}','{{$question_bank_rec->course_exam_papers_id}}','{{$question_bank_rec->question_marks}}','{{$question_bank_rec->question_obtained_marks}}')"  ><?=($question_bank_rec->question_obtained_marks != '') ? 'Updated' : 'Update Marks'?> </button></a></td>  -->
  </tr>
<?php
}
endforeach
?>
  </tbody>
</table>
</div>
</div>

<!-- /////////////////// print long question ///////////////// -->

<div class="card" style="margin-bottom: 45px !important;">
    <div class="card-header" style="background-color:#17675F !important; color:#FFF; ">
        <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; margin-top:15.840px;" >
        Exam Type : Long Question Answer
        </h3>
    </div>
    <div class="card-body" id="table_div">

<table class="table table-striped">
  <thead>



  </thead>
  <tbody>
     <?php
     //print_r($result);exit;
    foreach($result as $question_bank_rec):
      if($question_bank_rec->exam_ques_type == 4){
     ?>
        <tr>
          <td align="left" style="font-size: 18px;font-weight: 600;color: #1E293B;"><b>Question</b>
<style type="text/css">

.ob_marks {
    width: 100%;
    float:left;
    font-size: 20px;
    padding: 0px 1px 0px 0px;
    box-sizing:border-box;
}
.total_marks_show{
    margin:0px 0px 0px -35px;
    float:left;
    padding-top: 5px;
    cursor: default;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.table-striped tbody tr:nth-of-type(even) {
    background-color: white;
}

</style>

<div style="float:right">
<input id="{{$question_bank_rec->ceqp_id}}ob_marks" class="ob_marks" type="text" onkeyup="studentatt('{{$question_bank_rec->ceqp_id}}','{{$question_bank_rec->sai_enrollment_no}}','{{$question_bank_rec->course_exam_papers_id}}','{{$question_bank_rec->question_marks}}')" spellcheck="false" autofocus onfocus="this.value = this.value;" value="{{$question_bank_rec->question_obtained_marks}}" style="width:50%;"></input>
<p class="total_marks_show"> / {{$question_bank_rec->question_marks}}</p>
<img id="{{$question_bank_rec->ceqp_id}}image" style="display:none" src="{{asset('images/green_cog.png')}}" width="20px"/>
<img id="{{$question_bank_rec->ceqp_id}}imageerror" style="display:none" src="{{asset('images/red_cog.png')}}" width="20px"/>

<div id="{{$question_bank_rec->ceqp_id}}saved_msg_div_file" style="display: none;color: green"><span>Marks Saved</span></div>

</div>
<div style="float:right;display: none;color: red" id="{{$question_bank_rec->ceqp_id}}saved_msg_div_file_error"  ><span>Obtained marks should be less than total marks</span></div>

          </td>
       </tr>
  <tr>
      <td align="left" > @php
        $question = $question_bank_rec->question;
    @endphp

    @if($question != strip_tags($question))
        {!! $question !!}
    @else
        {{ $question }}
    @endif</td>
      </tr>
      {{-- ************************************************************************ --}}
      @php
    $answerGiven = trim((string) ($question_bank_rec->answer_given ?? ''));
    $uploadedFile = trim((string) ($question_bank_rec->one_in_one ?? ''));

    $hasAnswer = $answerGiven !== '';
    $hasUploadedFile = $uploadedFile !== '';
@endphp

<tr>
    <td align="left">
        <b>Answer</b>

        @if($hasUploadedFile)
            <div class="col-12 col-sm-2 text-right" style="float:right;">
                <a href="{{ asset('https://student.bnu.edu.pk/images/'.$uploadedFile) }}" style="color:black">
                    <i class="fa fa-upload"></i> Uploaded File
                </a>
            </div>
        @endif
    </td>
</tr>

<tr>
    <td style="text-align: justify;">
        @if($hasAnswer)
            {{ $answerGiven }}
        @elseif($hasUploadedFile)
            <span style="color:#64748B; font-style:italic;">
                Answer uploaded as file
            </span>
        @else
            <span style="color:#DC2626; font-weight:600;">
                Not Answered
            </span>
        @endif
    </td>
</tr>
      {{-- ************************************************************************ --}}
      <tr style="display: none;">
      <td align="left" ><b>Answer</b>
      <?php if($question_bank_rec->one_in_one != ''){?>
      <div class="col-12 col-sm-2 text-right" style="float:right;">
      <a href="{{asset('https://student.bnu.edu.pk/images/'.$question_bank_rec->one_in_one)}}"  style="color:black"><i class="fa fa-upload"></i> Uploaded File</a></td>
     </div>
      <?php } ?>
      </tr>
      <tr>
      <td style="  text-align: justify;" >{{$question_bank_rec->answer_given }} </td>
      </tr>
      
      <tr>
       <td align="left">
       <textarea
    id="teacher_remarks{{ $question_bank_rec->ceqp_id }}"
    onkeyup="teacherremarks('{{ $question_bank_rec->ceqp_id }}')"
    style="width:100%; height:100px; border-radius:8px; border:1px solid #E2E8F0; background:#FFF; padding:10px;"
    name="Text1"
    placeholder="Enter your remarks or feedback here...">{{ $question_bank_rec->teacher_remarks }}</textarea>
       <div id="saved_div{{$question_bank_rec->ceqp_id}}" style="display:none;font-size:12px;font-style: italic;color:green;text-align: left;font-weight: bold;">Saved</div>
       </td>
      </tr>
      <!-- <tr>
      <td align="center"><button class="<?=($question_bank_rec->question_obtained_marks != '') ? 'btn btn-success' : 'btn btn-primary'?>" type="button" data-toggle="modal" data-target="#checkpaperreason" onclick="updtmarks('{{$question_bank_rec->ceqp_id}}','{{$question_bank_rec->sai_enrollment_no}}','{{$question_bank_rec->course_exam_papers_id}}','{{$question_bank_rec->question_marks}}','{{$question_bank_rec->question_obtained_marks}}')"  ><?=($question_bank_rec->question_obtained_marks != '') ? 'Updated' : 'Update Marks'?> </button></a></td>
  </tr> -->
<?php
}
endforeach
?>
  </tbody>
</table>
</div>
</div>


</div>

</div>


@stop
@section('javascript')

<script>
function teacherremarks(cepq){

         var comments =  $("#teacher_remarks"+cepq).val();
         var fd = new FormData();
         fd.append("comments", comments);
         fd.append("cepq", cepq);

            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: '{{url('teacher/teacherremarks')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
               //   console.log(data.message);

            $("#saved_div"+cepq).show();
          // $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
                  $("#saved_div"+cepq).hide();

                  }, 2000);

                },
                complete: function(){
                }
            });


}
    function exportHTML(){
       var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
            "xmlns:w='urn:schemas-microsoft-com:office:word' "+
            "xmlns='http://www.w3.org/TR/REC-html40'>"+
            "<head><meta charset='utf-8'></head><body>";
       var footer = "</body></html>";
       var sourceHTML = header+document.getElementById("source-html").innerHTML+footer;

       var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
       var fileDownload = document.createElement("a");
       document.body.appendChild(fileDownload);
       fileDownload.href = source;
       fileDownload.download = 'document.doc';
       fileDownload.click();
       document.body.removeChild(fileDownload);
    }
</script>
<script>

function studentatt(id,enroll,paper_id,marks){


   var obt_marks = parseFloat($("#"+id+"ob_marks").val());
   var total_marks = parseFloat(marks);
 //  var a = parseInt("10") + "<br>";
   //alert(obt_marks+' - '+total_marks)

    if(obt_marks <= total_marks){
     //   alert(obt_marks+' - '+marks)
         //   var bi_form = document.querySelector('#updatemarks');
            var fd = new FormData();
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: '{{url('updatequesmarks')}}/'+id+'/'+enroll+'/'+paper_id+'/'+obt_marks,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                    // $("#"+id+"imageerror").hide();
                    // $("#"+id+"image").show();
                    $("#"+id+"saved_msg_div_file_error").hide();
                    $("#"+id+"saved_msg_div_file").show();
        //   $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
            $("#"+id+"saved_msg_div_file").hide();
                //  $("#"+id+"image").hide();

                  }, 2000);
        //           location.reload();

                },
                complete: function(){
                }
            });

    }else{
     //   $("#"+id+"imageerror").show();
       $("#"+id+"saved_msg_div_file_error").show();
        //alert('Obtained marks cannot be greater than total marks')
    }


}
</script>
@stop
