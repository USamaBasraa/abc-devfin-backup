@extends('layouts.app',['pageTitle' => 'BNU CMS - HOD Approval'])

@section('content')
<div class="page-header">
    {{-- <div class="page-header-title"> --}}
        <div class="card-header" style="background-color:#2F3B4D !important; color:#639; width:100%;margin-top: 2px;">
            <h5 style="font-family:'Times New Roman', Times, serif; color:white;">{{$all_classes[0]->course_name}}<div style="float:right;"></div></h5>
        </div>
    {{-- </div> --}}
</div>
  <div class="page-body">
       @include('teacher.lecture_scheduling')
  </div>

<script type='text/javascript'>
window.onload = function(){
  ///  alert('ok');
   // document.querySelector(".preloader").style.display = "none";
 }
</script>
@stop

@section('javascript')


@stop
