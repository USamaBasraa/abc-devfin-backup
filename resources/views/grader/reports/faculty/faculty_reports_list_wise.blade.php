@extends('layouts.app')
@section('title')
    BNU CMS
@stop
{{-- @dd("123"); --}}


@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}

.blink_me {
  animation: blinker 1s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}

</style>
<div class="card evaluation-report-heading-card">     
    <div class="card-header" style="color:#FFFF !important; padding:0px;padding-left:10px ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Faculty Evaluation Report</h3>
    </div>
</div>


<div class="card evaluation-report-page"> 
<div class="row evaluation-filter-profile-row" style=" width:98% ;height: 110px;!important; margin-left:auto; margin-right:auto; padding:10px;">
        <div class="col-12 col-md-3 text-center evaluation-profile-card" style="border:dotted 0px #000000; width:200px;">
            <img src="{{asset('staffimages/'.$profile['emp_imagefile'].'')}}" class="rounded-circle" alt="Image Here . . ." width="100" height="100">
            <!-- <h3 style="font-family:'Times New Roman', Times, serif;">{{$profile['full_name'] }}</h3>   -->
            <h3 class="evaluation-faculty-name">{{$profile['full_name'] }}</h3>
        </div>
                           
<div class="col-12 col-md-8 text-md-left text-center evaluation-filter-card" style="min-height:150px;">
    <form action="" id="filterFormOne" class="evaluation-filter-form" >
            <div class="row">
                <div class="form-group col-sm-4">
                    <label  for="session_sel">Select Session</label>
                    <select name="session" id="session_sel" class=" form-control" onchange="on_session_change(this);"  >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <label  for="course">Select Course</label>                   
                    <select name="course" id="course" class="form-control" onchange="on_course_change(this);" >
                    </select>
                </div>
                <input type="hidden" name="fc_co_id" id="fc_co_id" class="form-control" readonly value="<?=$co_id;?>" >
                <input type="hidden" name="teacher" id="teacher" class="form-control" readonly value="{{$emp_user}}" >
                <div class="form-group col-sm-3">
                        <button class="btn btn-info btn-lg" style="margin-top: 25px;" type="button" onclick="getResultsByCourse();" >Get Result</button>
                </div>
            </div>
    </form>

    <form action="" id="filterByTeacher" style="display:none;">
    <div class="row"><h3>Filter By: Teacher</h3></div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="teacher_input">Teacher</label>
                <input type="input" name="teacher_input" id="teacher_input" value="" placeholder="Teacher" class="form-control">
            </div>
            <div class="form-group col-sm-3">
                <label for="tid_input">Hidden T-ID</label>
                <input type="input" name="tid_input" id="tid_input" value="" placeholder="Teacher ID" class="form-control">
            </div>
            <div class="form-group col-sm-3">
                <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <button class="btn primary" style="margin-top: 25px;" type="button" onclick="getResultsByTeacher();" >Get Result</button>
            </div>
        </div>
    </form>
</div>
</div>


    <div class="card-header evaluation-course-chip" style=" color:#FFFF !important; padding:0px;padding-left:10px ">
         <h3 id="course_name" style="margin-left: 90px !important;
    margin-top: -30px !important;" >Course Name</h3>
    </div>
        <!-- <button class="btn primary" onclick="getPDF();" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF</button> -->
        <div class="card-body evaluation-table-card">
            <table class="table table-sm table-bordered evaluation-results-table">
                <thead>
                <tr>
                    <th scope="col">Sr.No</th>
                    <!-- <th scope="col">Name of Faculty</th> -->
                    <!-- <th scope="col">Course Code</th> -->
                    <th scope="col">Course Title</th>
                    <th style="text-align:center;" scope="col">Course Cr.Hrs</th>
                    <th style="text-align:center;" scope="col">Total No. of Students</th>
                    <th style="text-align:center;" scope="col">No. of Evaluators</th>
                    <th style="text-align:center;" scope="col">Quality of Teaching</th>
                    <th style="text-align:center;" scope="col">Course Evaluation</th>
                    <th style="text-align:center;" scope="col">Exams & Assessment</th>
                    <th style="text-align:center;" scope="col">Overall Rating(%)</th>
                    <th style="text-align:center;" scope="col">Comments</th>
                    <!-- <th scope="col">Re Chance</th> -->
                    <!-- <th scope="col">Student's Contribution</th> -->
                </tr>
                </thead>
                <tbody id="result-body" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
            </table>
        </div>
        <!-- <button  type="button" onclick="getPDF();" class="btn btn-info btn-lg btn-block"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> View Comments</button> -->
        <button id="pdf" style="display:none" type="button" onclick="getPDF();" class="btn btn-info btn-lg btn-block"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF REPORT</button>

 <!-- //////////html  -->
            <div class="card-h1eader evaluation-remarks-summary" >
                <div class="row" style=" width:100% ;!important; margin-left:auto; margin-right:auto;">
                    <div class="col-12 col-md-12 " >
                        <div id="fc-remarks"></div>
                    </div>
               



        <!-- ///////////html end -->

        <div class="card evaluation-remarks-card" id="teacher_remarks" style="display:none; padding-left:0 !important; padding-right: 0 !important;margin-left:0 !important;"> 
            <div class="card-header" >
                <div class="row" style=" width:100% ;!important; margin-left:auto; margin-right:auto; padding:10px;">
                    <div class="col-12 col-md-12 " >
                        <h4 style="color:#red"><strong>Disclaimer: Please Enter Your Remarks If Any </strong> </h4>
                            <textarea type="input" name="remark" id="teacher_remark"   row="3" placeholder="Teacher Remarks"   class="form-control"> Acknowledge </textarea>
                            <div id="div_save" style="color:green"></div><div id="div_error" style="color:red"></div>
                    </div>
                <div style="padding-top: 20px;font-size:16px;" class="col-12 col-md-12 " >
                     <button type="button" onclick="saveData()" class="btn btn-success btn-lg btn-block"> Yes, I acknowledge.</button>
                    <!-- <button  style="font-size:14px;" type="button" class="btn btn-info" onclick="saveData()" >Acknowledge</button> -->
                    
               </div>
        </div>

       
</div>

<!-- ///////////// chart div -->
<div style="margin-top:10px;" class="row chart1 evaluation-analytics-row">
    <div class="form-group col-sm-6 evaluation-chart-card">
        <div id="chartContainer1" ></div>
    </div>
    <div class="form-group col-sm-6 evaluation-chart-card">
        <div id="chartContainer" ></div>
    </div>
</div>
<div style="clear:both;"></div>
<div class="evaluation-overall-chart-section" style="margin-bottom:45%;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
               
                <div class="panel-body">
                    <div id="canvas" height="280" width="800"></div>
                </div>
            </div>
        </div>
    </div>
</div> 

 
<style>
.canvasjs-chart-credit{
color:white !important ;
}
</style>


<style type="text/css">
/* Figma Evaluation Report presentation layer. Existing Blade fields, IDs, names,
   handlers, hidden forms, table structure and JavaScript behavior are preserved. */
.evaluation-report-heading-card,
.evaluation-report-page {
    font-family: 'Montserrat', sans-serif;
    color: #111827;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: #f2f6f9;
}

.evaluation-report-heading-card {
    margin: 0 !important;
    /* padding: 28px 30px 10px; */
}

.evaluation-report-heading-card > .card-header {
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    color: #111827 !important;
}

.evaluation-report-heading-card h3 {
    float: none !important;
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    line-height: 1.35;
    font-weight: 600 !important;
    color: #111827 !important;
}

.evaluation-report-page {
    /* min-height: calc(100vh - 60px); */
    margin: 0 !important;
    /* padding: 12px 30px 48px; */
}

/* Filters: same form/controls, Figma layout */
.evaluation-report-page .evaluation-filter-profile-row {
    width: 100% !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
}

.evaluation-report-page .evaluation-filter-card {
    order: 1;
    flex: 0 0 100%;
    max-width: 100%;
    min-height: 0 !important;
    padding: 0 !important;
    text-align: left !important;
}

.evaluation-report-page #filterFormOne {
    margin: 0 0 20px;
}

.evaluation-report-page #filterFormOne > .row {
    margin: 0;
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
}

.evaluation-report-page #filterFormOne .form-group {
    margin: 0;
    padding: 0;
    flex: 0 0 auto;
    max-width: none;
}

.evaluation-report-page #filterFormOne .form-group:nth-of-type(1) { width: 180px; }
.evaluation-report-page #filterFormOne .form-group:nth-of-type(2) { width: 280px; }
.evaluation-report-page #filterFormOne .form-group:nth-of-type(3) { width: auto; }

.evaluation-report-page #filterFormOne label,
.evaluation-report-page #filterByTeacher label {
    display: block;
    margin: 0 0 6px;
    color: #6b7280;
    font-size: 12px;
    line-height: 1.2;
    font-weight: 500;
    text-transform: uppercase;
}

.evaluation-report-page #session_sel,
.evaluation-report-page #course,
.evaluation-report-page #filterByTeacher .form-control {
    width: 100%;
    height: 40px;
    min-height: 40px;
    padding: 0 36px 0 12px;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background-color: #fff;
    color: #111827;
    font-size: 14px;
    font-weight: 400;
    box-shadow: none !important;
}

.evaluation-report-page #session_sel:focus,
.evaluation-report-page #course:focus,
.evaluation-report-page textarea:focus,
.evaluation-report-page .form-control:focus {
    border-color: #17675f !important;
    box-shadow: 0 0 0 3px rgba(23,103,95,.10) !important;
}

.evaluation-report-page #filterFormOne .btn-info,
.evaluation-report-page #filterByTeacher .btn {
    height: 40px;
    min-height: 40px;
    margin-top: 0 !important;
    padding: 0 20px;
    border: 1px solid #17675f !important;
    border-radius: 8px !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 14px;
    font-weight: 600;
    box-shadow: none !important;
}

/* Faculty profile card */
.evaluation-report-page .evaluation-profile-card {
    order: 2;
    flex: 0 0 100%;
    max-width: 100%;
    min-height: 166px;
    margin: 0 0 20px;
    padding: 20px 24px !important;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 12px;
    border: 0 !important;
    border-radius: 12px;
    background: #fff;
    text-align: left !important;
    box-shadow: none;
}

.evaluation-report-page .evaluation-profile-card img {
    width: 80px !important;
    height: 80px !important;
    object-fit: cover;
    border: 0;
    border-radius: 50% !important;
    background: #f9fafb;
}

.evaluation-report-page .evaluation-faculty-name {
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    color: #111827;
    font-size: 20px !important;
    line-height: 1.3;
    font-weight: 700;
}

/* Existing dynamic course heading becomes the Figma course badge inside the profile visual area. */
.evaluation-report-page .evaluation-course-chip {
    position: relative;
    z-index: 2;
    width: fit-content;
    max-width: calc(100% - 190px);
    margin: -74px 0 30px 138px !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    color: #111827 !important;
}

.evaluation-report-page #course_name {
    float: none !important;
    display: inline-flex !important;
    max-width: 100%;
    margin: 0 !important;
    padding: 6px 12px !important;
    border-radius: 6px;
    background: rgba(23,103,95,.05);
    color: #111827 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    line-height: 1.35;
    font-weight: 500 !important;
    white-space: normal;
}

/* Results table */
.evaluation-report-page .evaluation-table-card {
    width: 100%;
    margin: 0 0 20px;
    padding: 0 !important;
    border: 0;
    border-radius: 12px;
    background: #fff;
    overflow-x: auto;
    box-shadow: none;
}

.evaluation-report-page .evaluation-results-table {
    width: 100%;
    min-width: 980px;
    margin: 0 !important;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    color: #334155;
    font-size: 12px;
}

.evaluation-report-page .evaluation-results-table thead th {
    padding: 10px 12px !important;
    border: 0 !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 11px;
    line-height: 1.25;
    font-weight: 600;
    text-align: center;
    text-transform: capitalize;
    vertical-align: middle;
    white-space: normal;
}

.evaluation-report-page .evaluation-results-table thead th:first-child {
    border-top-left-radius: 12px;
}
.evaluation-report-page .evaluation-results-table thead th:last-child {
    border-top-right-radius: 12px;
}

.evaluation-report-page .evaluation-results-table tbody td {
    padding: 16px 12px !important;
    border: 0 !important;
    border-bottom: 1px solid #e5e7eb !important;
    background: #fff;
    color: #334155 !important;
    font-size: 12px;
    line-height: 1.4;
    vertical-align: middle;
}

.evaluation-report-page .evaluation-results-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.evaluation-report-page .evaluation-results-table tbody tr:last-child td {
    border-bottom: 0 !important;
}

.evaluation-report-page .evaluation-results-table a {
    color: #17675f !important;
    font-weight: 600 !important;
    text-decoration: underline;
}
.evaluation-report-page .evaluation-results-table a img {
    width: 22px;
    height: 22px;
    margin-right: 5px;
    object-fit: contain;
}

/* PDF remains the same functional control */
.evaluation-report-page #pdf {
    width: auto;
    min-height: 40px;
    margin: 0 0 20px auto;
    padding: 9px 18px;
    border: 1px solid #17675f !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #17675f !important;
    font-size: 13px;
    font-weight: 600;
    box-shadow: none !important;
}

/* Existing remarks + acknowledgement */
.evaluation-report-page .evaluation-remarks-summary {
    width: 100%;
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
}

.evaluation-report-page #fc-remarks:not(:empty) {
    margin: 0 0 20px;
    padding: 18px 20px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    color: #334155;
}

.evaluation-report-page .evaluation-remarks-card {
    width: 100%;
    margin: 0 0 20px !important;
    padding: 0 !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 12px !important;
    background: #fff !important;
    box-shadow: none !important;
    overflow: hidden;
}

.evaluation-report-page .evaluation-remarks-card > .card-header {
    padding: 18px 20px !important;
    border: 0 !important;
    background: #fff !important;
}

.evaluation-report-page .evaluation-remarks-card > .card-header > .row {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.evaluation-report-page .evaluation-remarks-card h4 {
    margin: 0 0 12px;
    color: #1e293b !important;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 600;
}

.evaluation-report-page #teacher_remark {
    width: 100%;
    min-height: 120px;
    padding: 14px 16px;
    resize: vertical;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background: #fff;
    color: #334155;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    line-height: 1.5;
    box-shadow: none !important;
}

.evaluation-report-page .evaluation-remarks-card .btn-success {
    width: 100%;
    min-height: 40px;
    padding: 9px 20px;
    border: 1px solid #17675f !important;
    border-radius: 8px !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 14px;
    font-weight: 600;
    box-shadow: none !important;
}

/* Analytics cards — same CanvasJS containers/data, Figma card treatment */
.evaluation-report-page .evaluation-analytics-row {
    width: 100%;
    margin: 20px 0 0 !important;
    display: flex;
    gap: 22px;
    align-items: stretch;
}

.evaluation-report-page .evaluation-chart-card {
    flex: 1 1 0;
    max-width: none;
    margin: 0 !important;
    padding: 20px !important;
    border-radius: 12px;
    background: #fff;
    box-shadow: none;
}

.evaluation-report-page #chartContainer1,
.evaluation-report-page #chartContainer {
    width: 100% !important;
    height: 340px !important;
    min-height: 340px;
}

.evaluation-report-page .evaluation-overall-chart-section {
    width: 100%;
    margin: 20px 0 0 !important;
    padding: 20px;
    border-radius: 12px;
    background: #fff;
}

.evaluation-report-page .evaluation-overall-chart-section .row {
    margin: 0;
}
.evaluation-report-page .evaluation-overall-chart-section .col-md-10 {
    width: 100%;
    max-width: 100%;
    flex: 0 0 100%;
    margin: 0 !important;
    padding: 0;
}
.evaluation-report-page .evaluation-overall-chart-section .panel,
.evaluation-report-page .evaluation-overall-chart-section .panel-body {
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
    box-shadow: none;
}
.evaluation-report-page #canvas {
    width: 100% !important;
    min-height: 360px;
}

.evaluation-report-page .canvasjs-chart-credit {
    display: none !important;
}

@media (max-width: 991.98px) {
    .evaluation-report-heading-card { padding-left: 20px; padding-right: 20px; }
    .evaluation-report-page { padding-left: 20px; padding-right: 20px; }
    .evaluation-report-page .evaluation-course-chip {
        max-width: calc(100% - 150px);
        margin-left: 118px !important;
    }
}

@media (max-width: 767.98px) {
    .evaluation-report-heading-card { padding: 20px 14px 8px; }
    .evaluation-report-page { padding: 10px 14px 32px; }
    .evaluation-report-page #filterFormOne > .row { display: block; }
    .evaluation-report-page #filterFormOne .form-group:nth-of-type(1),
    .evaluation-report-page #filterFormOne .form-group:nth-of-type(2),
    .evaluation-report-page #filterFormOne .form-group:nth-of-type(3) {
        width: 100%;
        margin-bottom: 12px;
    }
    .evaluation-report-page #filterFormOne .btn-info { width: 100%; }
    .evaluation-report-page .evaluation-profile-card {
        min-height: 150px;
        padding: 18px !important;
    }
    .evaluation-report-page .evaluation-course-chip {
        max-width: calc(100% - 110px);
        margin: -66px 0 24px 110px !important;
    }
    .evaluation-report-page .evaluation-analytics-row {
        display: block;
    }
    .evaluation-report-page .evaluation-chart-card {
        width: 100%;
        margin-bottom: 16px !important;
    }
}
</style>

@stop
@section('javascript')
<script src="{{ asset('canvasjs.min.js') }}" ></script>
<script>
  $( document ).ready(function() {
   // $('#teacher_remark').val('');
   var co_id='<?=$co_id;?>';
   
   // Handle session from URL
   var urlParams = new URLSearchParams(window.location.search);
   var sess_id = urlParams.get('session');
   if(sess_id) {
       $('#session_sel').val(sess_id);
       getCourses();
   }
  
   if(co_id!=''){
    getResultsByCourse();
   } else if(sess_id) {
    getResultsByCourse();
   }
    
});
function saveData(){
        var session = $('#session_sel').val();
        var course    = $('#course').val();
        var fc_co_id    = $('#fc_co_id').val();
        var remarks   = $('#teacher_remark').val();
        var html='Remarks Saved';
        
        if(course!=null){
            course=course;
        }else{
            course=fc_co_id;
        }
//alert(remarks);
if(remarks==''){
    $('#div_error').html('Please Enter Remarks');
    return false;
}
getRequest('/teacher_remarks/get-result?course_id='+course+'&remarks='+remarks,function(res){
            if(res.status){
                 $('#div_save').html(html);
                 setTimeout(function(){ 
                    $('#div_save').html(''); 
                    location.reload(true);
                     }, 2000);
            }
        },function(res){
            alert('Some Error found!');
        });
}


    var lastURL = '';

    // https://github.com/biggora/bootstrap-ajax-typeahead

////////////

function getResultsForchart(url,course){
    $('#teacher_remarks').hide();
    $('#fc-remarks').html('');
        $('#result-body').html('<tr><td colspan="12" style="text-align:center;">Loading...</td></tr>');
        getRequest(url,function(res){
            if(res.status && res.data){
            console.log(res.data);
              lastURL = url;
               html='';
           
                $.each(res.data,function( key, value ) {
                    var srno = parseInt(key+1);
                    var co_id = value.fc_co_id;
                   
                   
                    if(value.feedback_submitted==0){
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td >'+value.teacher_name+'</td>';
                    html += '<td>'+value.course_name+'</td>';
                    html += '<td>'+value.co_course_cr+'</td>';
                    html += '<td>'+value.total_students+'</td>';
                    html += '<td colspan="5">Student Feed back Not Submitted Yet</td>';
                    html += '</tr>';
                    }else{
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    // html += '<td>'+value.teacher_name+'</td>';
                    html += '<td style="color:#0B7A75" >'+value.course_name+'</td>';
                    html += '<td align="center" style="color:#A30000" >'+value.co_course_cr+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.total_students+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.feedback_submitted+'</td>';
                    html += '<td align="center" style="color:#19535F" >'+value.quality_of_teaching+'</td>';
                    html += '<td align="center" style="color:#7B2D26" >'+value.course_evaluation+'</td>';
                    html += '<td align="center" style="color:#0B7A75">'+value.exam_and_assessment+'</td>';                    
                    html += '<td align="center" >'+value.overall_rating+'</td>';
                    html += '<td style="text-align:center;" > <a style="text-align:center;color:#F95700FF;font-weight:bold" class="blink_me" href="{{route('faculty_comments_show')}}/'+co_id+'" target="_blank"><img   height="30" src="{{asset('images/comments.png')}}" />Comments</a></td>';
                    // html += '<td><a href="#" target="_blank">Comments</a></td>';
                    //html += '<td>'+value.want_course_again+'</td>';
                    // html += '<td>N/A</td>';
                    html += '</tr>';
                }
                });
                if(html != ''){
                    $('#result-body').html(html);
                    $('#canvas').show();
                }else{
                    $('#teacher_remarks').hide();
                    $('#pdf').hide();
                    $('#result-body').html('<tr><td colspan="12" style="text-align:center;font-size:24px">No Result Found! and Evaluation Closed By Exam Department</td></tr>');
                    $('#canvas').hide();
                    $('#course_name').html('Course Name');
                }

            if(course!=''){
                var remark='';
                console.log(res.data[0].course_name);
                getchartBycourse(res.data);
                if(res.data[0].remarks){
                    remark += '<span style="font-size:16px;font-weight:bold" >Teacher Remarks About Class:</span><span style="font-size:16px" >'+res.data[0].remarks+'</span>';
                        $('#teacher_remarks').hide();
                        $('#fc-remarks').html(remark);
                        $('#pdf').show();
                    }else{
                        $('#fc-remarks').html('');
                        $('#teacher_remarks').show();
                    }
                    //getchart(res.data);
                $('#course_name').html(res.data[0].course_name);
              }else{
                    getchart(res.data);
              }
    
            }
        },function(res){
            alert('Some Error found!');
        });
    }
    
    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}"+endPoint,
            type: "GET",
            dataType: "json",
            success:successBlock
        }).fail(failBlock);
    }
    function on_session_change(session){
        var sess_id = session.value;
        if(sess_id != '' && parseInt(sess_id) >= 165) {
            // Redirect to F2025 view and pass the selected session
            window.location.href = "{{route('faculty_evaluation_listing')}}?view=f2025&session=" + sess_id;
            return;
        }
        //console.log(session.value);
        getCourses();
        //getResultsByCourse();
        $('.chart').hide();
        $('#canvas').hide();
    }

    function on_course_change(session){
        var course    = $('#course').val();
       getResultsByCourse();
        
    }

    function getPDF(){
       //  var qString = lastURL.replace("/reports/faculty/get-results", "");
       // alert(lastURL+'&result-for=print');
       var session = $('#session_sel').val();
        var course    = $('#course').val();
       
     if( (session == '') ){
            alert('Please Select Session or Course');
            return false;
        }else{
            getResultsByCourse();
           window.open(lastURL+'&result-for=print');
        }

    }

    function getResultsByCourse(){
        var session = $('#session_sel').val();
        var course    = $('#course').val();
        $('.chart').hide();
        $('#canvas').hide();
        if( (session == '') && (course == '') ){
            alert('Please any criteria');
            return false;
        }
        var data = $('#filterFormOne').serialize();
        var url = '/reports/faculty-course/get-results?'+data;
        var urlchart = '/reports/faculty-course-chart/get-results?'+data;
       // getResults(url);
        getResultsForchart(url,course);

        
    }

    function getCourses(){
        var teacher_id  = $('#teacher').val();
        var sess_id     = $('#session_sel').val();
        getRequest('/teacher_courses/get-result?id='+teacher_id+'&sess_id='+sess_id,function(res){
            if(res.status){
                var html = '<option value="">All Courses</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.co_id+'" >'+value.course_code+' | '+value.course_cd_name+' ('+value.co_section+') </option>';
                });
                $('select[name="course"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }


  function getchart(data){
    
     $('.chart').hide();
     var dataAs = new Array();
     if(data.length>0){
        $('#canvas').show();
        $.each(data,function( key, value ) {
    dataAs.push({label: value.course_name, y :  parseFloat(value.overall_rating)});
    //dataAs.push({label: 'Course Evaluation', y :  parseFloat(subject.course_evaluation)});
   // dataAs.push({label: 'Course Assessment', y :  parseFloat(subject.exam_and_assessment)});
        });
        var chart = new CanvasJS.Chart("canvas", {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: 'Courses '+data[0].sess_name
            },
        
            data: [{
                type: "doughnut",
                indexLabel: "{symbol} - {y}",
                yValueFormatString: "#,##0.0\"%\"",
                showInLegend: true,
                legendText: "{label} : {y}",
                dataPoints: dataAs
            }]
        });
        chart.render();
    }
  }
 function getchartBycourse(data){
     console.log(data);
    if(data.length>0){
         $('.chart').show();
         $('#canvas').hide();
        var subject = data[0];
    if(subject.quality_of_teaching!=null){

        var dataAs = new Array();
        var dataAspie = new Array();
        var val=5-parseFloat(subject.overall_rating);
        dataAs.push({label: 'Quality of Education', y :  parseFloat(subject.quality_of_teaching)});
        dataAs.push({label: 'Course Evaluation', y :  parseFloat(subject.course_evaluation)});
        dataAs.push({label: 'Course Assessment', y :  parseFloat(subject.exam_and_assessment)});
        //// pie chart 
        dataAspie.push({label: 'Overall Course Rating By Students ', y :  parseFloat(subject.overall_rating)});
        dataAspie.push({label: 'Lost Course Point in Evaluation', y :  parseFloat(val)});
    
    var dd = {
            animationEnabled: true,
            theme: "light2",
            title:{
                text: "Course Evaluation"
            },
            axisY: {
                maximum: 5,
                minimum: 0,
                
                title: "Course Wise Evaluation"
            },
            data: [{
                type: "column",
                indexLabel: "{y}",
		        indexLabelPlacement: "inside",
		        indexLabelFontWeight: "bolder",
                indexLabelFontColor: "white",
                yValueFormatString: "#,##0.00\"%\"",
                dataPoints: dataAs
            }]
    };
    var chart =  new CanvasJS.Chart("chartContainer", dd);
    chart.render();
}


    var chart = new CanvasJS.Chart("chartContainer1", {
        animationEnabled: true,
        title: {
            text: "Course Evaluation"
        },
        subtitles: [{
		text: "Overall Course Point is: 5%"
	}],
        axisY: {
                maximum: 5
            },
        data: [{
            type: "pie",
            indexLabel: "{y}",
            yValueFormatString: "#,##0.00\"%\"",
            indexLabelPlacement: "inside",
            indexLabelFontColor: "#36454F",
            indexLabelFontSize: 18,
            indexLabelFontWeight: "bolder",
            showInLegend: true,
            legendText: "{label}",
            dataPoints: dataAspie
        }]
    });
    chart.render();
   }
}
</script>


@stop

