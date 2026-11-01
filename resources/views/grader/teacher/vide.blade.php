<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
$color='#ff6c43';
?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')


<a href="docs/Final Project.docx">Download </a>FASD
<a href="{{asset('docs/Final Project.docx')}}"   >DOWNLOAD</a>



 <a href="{{asset('http://172.20.251.118/images/Assignment_2.docx')}}" target="_blank" >PDF</a>

@stop
@section('pageScripts')

@stop