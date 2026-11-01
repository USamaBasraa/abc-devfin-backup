@extends('layouts.new_app')
@section('title')
    BNU CMS
@stop
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
<div class="card">     
    <div class="card-header" style="background-color:steelblue !important; color:#FFFF !important; padding:0px;padding-left:10px ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Faculty Evaluation Report</h3>
    </div>
</div>


<div class="card"> 
<div class="row" style=" width:98% ;height: 110px;!important; margin-left:auto; margin-right:auto; padding:10px;">
        <div class="col-12 col-md-3 text-center" style="border:dotted 0px #000000; width:200px;">
            <img src="{{asset('staffimages/'.$profile['emp_imagefile'].'')}}" class="rounded-circle" alt="Image Here . . ." width="100" height="100">
            <!-- <h3 style="font-family:'Times New Roman', Times, serif;">{{$profile['full_name'] }}</h3>   -->
        </div>
                           
<div class="col-12 col-md-8 text-md-left text-center" style="min-height:150px;">
    <form action="" id="filterFormOne"  >
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

    <div class="card-header" style="background-color:steelblue !important; color:#FFFF !important; padding:0px;padding-left:10px ">
         <h3 id="course_name" style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Course Name</h3>
    </div>
        <!-- <button class="btn primary" onclick="getPDF();" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF</button> -->
        <div class="card-body">
            <table class="table table-sm  table-bordered">
                <thead>
                <tr>
                    <th scope="col">Sr.No</th>
                    <!-- <th scope="col">Name of Faculty</th> -->
                    <!-- <th scope="col">Course Code</th> -->
                    <th scope="col">Course Title</th>
                    <th style="text-align:center;" scope="col">Course Cr.Hrs</th>
                    <th style="text-align:center;" scope="col">Total No. of Students</th>
                    <th style="text-align:center;" scope="col">No. of Evaluators</th>
                    <th style="text-align:center;" scope="col">Core Functions</th>
                    <th style="text-align:center;" scope="col">Support Functions</th>
                    <th style="text-align:center;" scope="col">Overall Rating(%)</th>
                    <th style="text-align:center;" scope="col">Comments</th>
                    <!-- <th scope="col">Re Chance</th> -->
                    <!-- <th scope="col">Student's Contribution</th> -->
                </tr>
                </thead>
                <tbody id="result-body" ><tr><td colspan="9" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
            </table>
        </div>
        <!-- <button  type="button" onclick="getPDF();" class="btn btn-info btn-lg btn-block"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> View Comments</button> -->
        <button id="pdf" style="display:none" type="button" onclick="getPDF();" class="btn btn-info btn-lg btn-block"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF REPORT</button>

 <!-- //////////html  -->
            <div class="card-h1eader" >
                <div class="row" style=" width:98% ;!important; margin-left:auto; margin-right:auto; padding:10px;">
                    <div class="col-12 col-md-12 " >
                        <div id="fc-remarks"></div>
                    </div>
               



        <!-- ///////////html end -->

        <div class="card" id="teacher_remarks" style="display:none"> 
            <div class="card-header" >
                <div class="row" style=" width:98% ;!important; margin-left:auto; margin-right:auto; padding:10px;">
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
<div style="margin-top:10px;" class="row chart1">
    <div class="form-group col-sm-6">
        <div id="chartContainer1" ></div>
    </div>
    <div class="form-group col-sm-6">
        <div id="chartContainer" ></div>
    </div>
</div>
<div style="clear:both;"></div>
<div style="margin-bottom:45%;">
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
        $('#result-body').html('<tr><td colspan="9" style="text-align:center;">Loading...</td></tr>');
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
                    html += '<td colspan="2">Student Feed back Not Submitted Yet</td>';
                    html += '</tr>';
                    }else{
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    // html += '<td>'+value.teacher_name+'</td>';
                    html += '<td style="color:#0B7A75" >'+value.course_name+'</td>';
                    html += '<td align="center" style="color:#A30000" >'+value.co_course_cr+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.total_students+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.feedback_submitted+'</td>';
                    html += '<td align="center" style="color:#19535F" >'+value.core_summary+'</td>';
                    html += '<td align="center" style="color:#7B2D26" >'+value.support_summary+'</td>';                    
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
                    $('#result-body').html('<tr><td colspan="9" style="text-align:center;font-size:24px">No Result Found! and Evaluation Closed By Exam Department</td></tr>');
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
        if(sess_id != '' && parseInt(sess_id) < 165) {
            // Redirect to OLD view and pass the selected session
            window.location.href = "{{route('faculty_evaluation_listing')}}?session=" + sess_id;
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
        // var val=5-parseFloat(subject.overall_rating);
        var val=100-parseFloat(subject.overall_rating);
        dataAs.push({label: 'Core Functions', y :  parseFloat(subject.core_summary)});
        dataAs.push({label: 'Support Functions', y :  parseFloat(subject.support_summary)});
        // dataAs.push({label: 'Course Assessment', y :  parseFloat(subject.exam_and_assessment)});
        //// pie chart 
        dataAspie.push({label: 'Overall Rating ', y :  parseFloat(subject.overall_rating)});
        dataAspie.push({label: 'Gap', y :  parseFloat(val)});
    
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

