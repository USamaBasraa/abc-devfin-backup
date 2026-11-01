@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div class="card">
    <div class="card-header" style="display:inline">
        <div style="float:left">
            <h3 class="card-title">Academic History </h3>  
        </div> 
    </div>
    <div class="card-body" style="padding-left:600px">
        <form action="" id="filterFormOne">
            <div class="row" >
                <div class="form-group col-sm-6">
                    <label for="session_sel_from"><b>From Session</b></label>
                    <select name="from_session" id="from_session" class=" form-control" onchange="on_session_change(this);" style="width: 300px"  >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="session_sel_to"><b>To Session</b></label>
                    <select name="to_session" id="to_session" class=" form-control" onchange="on_session_change(this);" style="width: 300px"  >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>             
            </div>    
            <div class="row">
                <div class="form-group col-sm-3" id="school_div">
                    <label for="school_sel"><b>School</b></label>
                    <select name="school" id="school_sel" class=" form-control" onchange="getDepartments();" style="width: 300px" >
                        <option value=""></option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-3" id="department_div">
                    <label for="department"><b>Department</b></label>                   
                    <select name="department" id="department" class="form-control" onchange="getPrograms();" style="width: 300px">
                    <option value=""></option>    
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="program"><b>Program</b></label>
                    <select class="form-control" id="program" name="program" style="width: 300px">
                    <option value=""></option>
                    </select>
                </div>  
            </div>
            <div class="row">                
                <div class="form-group col-sm-3" id="department_div">
                    <label for="department"><b>Enrollment No</b><span style="color:red"><b> * </b></span></label>                        
                    <input name="reg_no" id="reg_no" class="form-control" data-options="required:'true'"  style="width:300px">                        
                </div>    
            </div>    
            <div class="row">               
                <div class="form-group col-sm-3" style="text-align:center">
                    <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <!-- <button class="btn btn-primary" style="margin-top: 25px;" type="button" onclick="getPDF();" >Get Result</button> -->
                    <button type="button" class="btn btn-primary" onclick="goToPDF();">Generate</button>                        
                </div>  
                <div class="form-group col-sm-3" style="text-align:center">
                    <label class="hidegif" style="display:none">Please Wait System is Generating PDF</label>
                </div>
            </div>          
        </form>      
    </div>
</div>  


<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
    function goToPDF(){
        $('.hidegif').show();
        var reg_no = $('#reg_no').val();
      
        var from_sess_id =  $('#from_session').val();     
    
        var to_sess_id =  $('#to_session').val();
        var school_id =  $('#school_sel').val();
        var dept_id =  $('#department').val();
        var prog_id =  $('#program').val();

        var link = 'https://staff.bnu.edu.pk/HOD/academic_history_report.php?grader_entry_user_id='+reg_no+'&action=grader&from_sess_id='+from_sess_id+'&to_sess_id='+to_sess_id+'&ah_school_id='+school_id+'&ah_dept_id='+dept_id+'&ah_prog_id='+prog_id;

        setTimeout(function(){
            $('.hidegif').hide();
            window.open(
                link,
                '_blank' // <- This is what makes it open in a new window.
                );
            // $('#pdfIcon').show();
        }, 2000);

    }

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

   
  
  

 
  
 

    // getCoursesByTeacher

  

</script>
@stop

