<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();

//print_r($show_student_data);
//exit();
?>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')
    <embed src="{{asset('studentsfile/quiz_1.docx')}}" width="1050" height="1000"></embed>
@stop