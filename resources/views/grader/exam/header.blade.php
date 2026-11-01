<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
// exit;
?>
@extends('layouts.app',['pageTitle' => 'Exam'])

@section('content')
<?php

 $fc_co_idd=my_simple_crypt($fc_co_id, 'd' );
//echo  $fc_co_idd;exit;

?>
<div class="card"> 
  <div class="card-header" >
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Online Exam System </h3>

            <h3 style="float: right;">{{$course_name}}</h3>
    </div>
<body style="margin:0px;">
<table border="1" class="table table-bordered">
	 <tr>
    	<td> <img  title="WithDraw" src="{{asset('online-exam.png')}}"  width="100%" Height="90%">         </td>
     </tr>
     <tr>
     	<td>
     		<table border="0" width="100%" cellpadding="4px" cellspacing="0px">
        		<tr> 
           <!--  <td align="center" width="10%">   <a href="{{route('teacher/question_bank')}}/<?=$fc_co_id?>" target="_blank" style="color:#007bff; text-decoration:none" ><img  title="Question Bank" src="{{asset('assets/exam1.png')}}"  width="27%"><br><b>Question Bank</b> </a> </td> -->
            <td align="center" width="50%"><a href="{{route('teacher/online_exam')}}/<?=$fc_co_id?>"  style="color:#007bff; text-decoration:none"> <img  title="WithDraw" src="{{asset('exam33.png')}}"  width="14%"><br><b>Create Exam </b></a> </td>
            
           <!--  <td align="center" width="10%"><a href="add_question.php" style="color:#007bff; text-decoration:none" > <img  title="WithDraw" src="{{asset('exam4.png')}}"  width="26%">  <br><b> Add Question In Exam</b> </a> </td> -->
            <td align="center" width="50%"><a href="{{route('teacher/online_result')}}/<?=$fc_co_id?>" style="color:#007bff; text-decoration:none" >  <img  title="WithDraw" src="{{asset('online.png')}}"  width="19%">    <br><b style>Result</b> </a></td>
         
                </tr>
        	</table>
     	</td>
     </tr>
</table>
</div>
@stop
@section('pageScripts')
