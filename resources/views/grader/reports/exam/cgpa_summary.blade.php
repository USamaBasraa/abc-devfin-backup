@extends('layouts.app',['pageTitle' => 'Course Content'])
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>
<div class="card">
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >CGPA Summary</h3>
    </div>

<div class="card-header" style="width:100%;">
<div class="row" style=" width:98% ; margin-left:auto; margin-right:auto; padding:10px;">
    <div class="col-12 col-md-12 text-md-left text-center" style="min-height:150px;">
        <form method="post" action="" target="_blank" id="filterFormOne" enctype="multipart/form-data">
      <!-- {{ csrf_field() }}        -->

            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="session_sel">From Session</label>
                    <select name="from_session" id="session_from" class=" form-control"   >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="session_sel">To Session</label>
                    <select name="to_session" id="session_to" class=" form-control"   >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="cwa_report_type" id="teacher" class="form-control" readonly value="1" >

                <div class="form-group col-sm-3">
                    <label for="school_sel">School</label>
                    <select name="school" id="school_sel" class=" form-control" onchange="getDepartments()" >
                        <option value=""></option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="department">Department</label>
                    <select name="department" id="department" class="form-control" onchange="getTeacher();" >
                    </select>
                </div>
                <!-- <div class="form-group col-sm-3">
                        <button type="submit" class="btn btn-info btn-lg" style="margin-top: 25px;" type="button"  ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF REPORT</button>
                </div> -->
        </div>
        <div class="row">
        <div class="form-group col-sm-12">
                <button type="button"   onclick="getPDF()" class="btn btn-info btn-lg btn-block"> GET Result</button>
        </div>
        </div>
    </form>

</div>
</div>
</div>
</div>

@stop
@section('javascript')
<script>
function getPDF(){
         var  lastURL;//="reports/exam/cgpa-summary-report";
       // alert(lastURL+'&result-for=print');
       var data = $('#filterFormOne').serialize();
           window.open('/reports/exam/cgpa-summary-report?'+data);
          // /reports/exam/cgpa-summary-report

    }
function on_session_change(session){
        //console.log(session.value);
        getCourses();

    }


    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}/"+endPoint,
            type: "GET",
            dataType: "json",
            headers: {
                    Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                    Accept: 'application/json'
                },
            success:successBlock
        }).fail(failBlock);
    }
    function getDepartments(){
       // alert('dd');
        var school_id = $('#school_sel').val();
        getRequest('api/department/get?school_id='+school_id,function(res){
            if(res.status){
                var html = '<option value="">All Dept</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }

</script>
@stop
