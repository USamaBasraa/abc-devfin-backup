@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:500px; margin:10px auto;">
<div class="easyui-panel" title="Attendance Summary Department Wise" style="padding:10px; overflow: hidden; !important">
        <form id="ff" action="/assets/cms/reports/faculty_course_attendance_report_action.php" method="post" enctype="multipart/form-data" >
            {{-- <form id="ff" action="{{ route('faculty_course_attendance_report_action'), ['tac_end_date' => $tac_end_date] }}" method="post" enctype="multipart/form-data" > --}}
            @csrf
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Active Session</label>
                    <div class="col-sm-8">
                        <input id="facultyWload_sess_id" name="facultyWload_sess_id" style="width:300px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                   valueField:'sess_id', 
                   textField:'sess_name',  
                   required:'true',                 
                   url: '<?= url('/sessions') ?>', 
                   method:'GET',
                   onSelect: function(rec){                  
                    var urlcourse = '<?=url('/course_offering_exam')?>?sess_id='+rec.sess_id;
                        $('#ca_course_id_name').combobox('reload', urlcourse);   
                   } ">
                    </div>
            </div>           
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">School</label>
                <div class="col-sm-8">
                    <input id="facultyWload_school_id" name="facultyWload_school_id" style="width:300px;"  class="easyui-combobox form-control" data-options="
                    valueField: 'school_id',
                    textField: 'school_name',
                    required:'true',
                    method:'get',
                    url: '<?= url('/schools') ?>',
                    onSelect: function(rec){
                                   
                        var urlcourseDept = '<?=url('/departments')?>?school_id='+rec.school_id;
                        $('#facultyWload_dept_id').combobox('reload', urlcourseDept);      
                    }">
                </div>
            </div>

            <div class="form-group row" style="margin-bottom:0px;" >
                <label class="col-sm-4 col-form-label">Department</label>
                <div class="col-sm-8">
                    <input id="facultyWload_dept_id" name="facultyWload_dept_id" style="width:300px;"  class="easyui-combobox form-control" data-options="
                    valueField:'dept_id', 
                    textField:'dept_name',
                    method:'get', 
                    validType: 'exists[\'#facultyWload_dept_id\']',
                    required:'true',
                    <!-- url: '<?= url('/departments') ?>', -->
           onSelect: function(rec){
                <!-- $('#program_id').combobox('clear'); -->
                var urldept = '<?=url('/programs')?>?dept_id='+rec.dept_id;
                $('#asdw_prog_id').combobox('reload', urldept);
            }">
                </div>
            </div>

            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Program</label>
                    <div class="col-sm-8">
                        <input id="asdw_prog_id" name="asdw_prog_id" style="width:300px;"
                          class="easyui-combobox" data-options="
                                        valueField:'prog_id',
                                        textField :'prog_name',
                                        required:'true',
                                        method:'get', 
                                        validType: 'exists[\'#asdw_prog_id\']',
                                        <!-- url: 'common/staff_rights_programs.json.php', -->
                               onSelect: function(rec){

                                    <!-- $('#asdw_shift_id').combobox('clear'); -->
                                         <!-- var urlshift = 'common/shifts.json.php?sdpo_sess_id='+$('#asdw_sess_id').combobox('getValue')+'&sdpo_school_id='+$('#asdw_school_id').combobox('getValue')+'&sdpo_dept_id='+$('#asdw_dept_id').combobox('getValue')+'&sdpo_prog_id='+rec.id; -->
                                    <!-- $('#asdw_shift_id').combobox('reload', urlshift); -->

                               }
                         ">
                    </div>
            </div> 
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Shift </label>
                    <div class="col-sm-8">
                        <input id="asdw_shift_id" name="asdw_shift_id" style="width:300px;"
                            class="easyui-combobox" data-options="
                                    valueField:'shift_id',
                                    textField :'shift_name',
                                    validType: 'exists[\'#asdw_shift_id\']',
                                    url: '<?= url('/shift') ?>', 
                                    method:'GET',
                           ">
                    </div>
                </div>
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Report Type</label>
                <div class="col-sm-8">
                    <input id="fco_attendance_type" name="fco_attendance_type" class="easyui-combobox" style="width:300px;"
                        
                    data-options="valueField:'id'
                                ,textField:'text'
                                ,required:'true'
                                    ,validType: 'exists[\'#fco_attendance_type\']'
                                ,data: [{id: '1',text: 'PDF',selected:true}
                                        ,{id: '2',text: 'Excel'}
                                       
                                    ]">
            
                </div>
            </div>


            <div class="row">
                <label class="col-sm-4"></label>
                
                <div style="padding-left:120px" class="col-sm-10">      
                    <button class="btn btn-primary">Generate</button>           
                                  
                </div>
            
            </div>
        </form>
    </div>
    <div class="card-block">
        <div id="showtimetable">
          
        </div>
    </div>
</div>

@stop
@section('javascript')

<script type="text/javascript">

$(function(){
			$('#ff').form({
				success:function(data){
					
					$("#showtimetable").html(data);					
	
				}
			});
		});
    function myformatter(date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        var d = date.getDate();
        return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
    }

    function myparser(s) {
        if (!s) return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0], 10);
        var m = parseInt(ss[1], 10);
        var d = parseInt(ss[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
            return new Date(y, m - 1, d);
        } else {
            return new Date();
        }
    }
</script>
@stop