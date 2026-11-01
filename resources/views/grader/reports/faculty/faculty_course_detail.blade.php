@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')


<div class="card"> 
    
    <div class="card-header" style="width:100%;">
        <form action="" id="filterFormOne">
            <div class="row"></div>
            <div class="row" style="border: solid 2px read;">
                <div class="form-group col-sm-12">
                    <label for="session_sel">Please Select Session</label>
                    <select name="session" id="session_sel" class=" form-control" onchange="on_session_change(this);" style="width: 100%;"  >
                        <option value="">Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>    
            <div class="row">
                <div class="form-group col-sm-3" id="school_div">
                    <label for="school_sel">School</label>
                    <select name="school" id="school_sel" class=" form-control" onchange="getDepartments();" >
                        <option value=""></option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-3" id="department_div">
                    <label for="department">Department</label>                   
                    <select name="department" id="department" class="form-control" onchange="getTeacher();" >
                    </select>
                </div>
                <div class="form-group col-sm-3" id="teacher_div">
                    <label for="teacher">Instructor</label>                   
                    <select name="teacher" id="teacher" class="form-control" onchange="getCourses();" >
                    </select>
                </div>
                <div class="form-group col-sm-3"  id="courses_div">
                    <label for="course">Course</label>                   
                    <select name="course" id="course" class="form-control" >
                    </select>
                </div>
                <div class="form-group col-sm-3">
                        <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button class="btn btn-primary" style="margin-top: 25px;" type="button" onclick="getPDF();" >Get Result</button>
                </div>

               <div style="display:none">
                    <div class="form-group col-sm-3">
                        <label for="program">Program</label>
                        <select class="form-control" id="program" name="program">
                        </select>
                    </div>                    
               </div> 

            </div>
         </form>
      
    </div>
  
</div>

<div style="clear:both;"></div>
@stop
@section('javascript')
<script>

    var lastURL = '';

    // https://github.com/biggora/bootstrap-ajax-typeahead


    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}"+endPoint,
            type: "GET",
            dataType: "json",
            success:successBlock
        }).fail(failBlock);
    }
    function on_session_change(session){
        console.log(session.value);
        //getTeacher(session.value);
    }
    function getDepartments(){
        var school_id = $('#school_sel').val();
        getRequest('/department/get?school_id='+school_id,function(res){
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
    $("#session_sel").change(function () {
        var end = this.value;
        if(parseInt(this.value)){
            $('#bottom_filters').show();
            // $('#department_div').show();
            // $('#teacher_div').show();
            // $('#courses_div').show();
            
        }else{
            $('#bottom_filters').hide();
            // $('#department_div').hide();
            // $('#teacher_div').hide();
            // $('#courses_div').hide();
        }
    });
    function getTeacher(){
        var session_id = $('#session_sel').val();
       
        var dept_id = $('#department').val();
        var url = '/teacher/get?depID='+dept_id+'&session_sel='+session_id;
        getRequest(url,function(res){
            if(res.status){
                var html = '<option value="All Teacher">All Teacher</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.id+'" >'+value.name+'</option>';
                });
                $('select[name="teacher"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });

        getCourses();
    }

    function getPrograms(){
        var dept_id = $('#department').val();
        getRequest('/program/get?dept_id='+dept_id,function(res){
            if(res.status){
                var html = '<option value=" ">All Prog</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.prog_id+'" >'+value.prog_name+'</option>';
                });
                $('select[name="program"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }

    function getPDF(){
        var co_id     = $('#course').val();
        var lastURL = "/faculty_course_detail_report/"+co_id;
        //var lastURL='/reports/faculty';
        //alert(lastURL+'&result-for=print');
        //return false;
        // window.open(lastURL+'&result-for=print');
        window.open(lastURL);

    }

    function getResultsByTeacher(){
        var sess = $('#session_sel').val();
        // if(sess == ''){
        //     alert('Please Select Session First!'); return false;
        // }
        var tID = $('#tid_input').val();
        if(tID == ''){
            alert('Please Select Teacher First!'); return false;
        }
        var url = '/reports/faculty/get-results?tid='+tID+'&session='+sess;
        getResults(url);
    }

    function getResultsByProgDept(){
        var session = $('#session_sel').val();
        var school  = $('#school_sel').val();
        var dept    = $('#department').val();
        var prog    = $('#program').val();
        if( (session == '') && (school == '') ){
            alert('Please Select Session and School');
            return false;
        }
        // alert($('#session_sel').val()+' -- '+$('#school_sel').val());
        var data = $('#filterFormOne').serialize();
        var url = '/reports/faculty/get-results?'+data;
        getResults(url);
    }

    function getCourses(){
        var teacher_id  = $('#teacher').val();
        var sess_id     = $('#session_sel').val();
        var dept_id     = $('#department').val();


        getRequest('/teacher_courses/get?id='+teacher_id+'&sess_id='+sess_id+'&dept_id='+dept_id,function(res){
            if(res.status){
                var html = '<option value="1">All Courses</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.co_id+'" >'+value.course_code+' | '+value.course_cd_name+' ('+value.co_section+') </option>';
                });
                $('select[name="course"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }

  
    $(document).ready(function(){
        $('#teacher_input').typeahead({
        onSelect: function(item) {
		console.log(item);
        $('#tid_input').val(item.value);
	},
	ajax: {
		url: "{{ url('/teacher/get') }}",
		timeout: 500,
		displayField: "name",
		triggerLength: 2,
		method: "get",
		loadingClass: "loading-circle",
		preDispatch: function (query) {
			//showLoadingMask(true);
			return {
				search: query
			}
		},
		preProcess: function (data) {
			// showLoadingMask(false);
			if (data.success === false) {
				// Hide the list, there was some error
				return false;
			}
			// We good!
			return data.data;
		}
	}
        });
    });

    // getCoursesByTeacher

  

</script>
@stop

