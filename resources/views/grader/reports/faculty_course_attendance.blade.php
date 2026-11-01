@extends('template')
@section('mainBody')
<div class="page-header">
    <div class="page-header-title">
        <h4>Faculty Course Attendance</h4>
    </div>
</div>
<div class="page-body">





    <script type="text/javascript">
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




    <div class="card">
        <!-- <div class="card-header">
                                <h5>Filter</h5>
                            </div> -->
        <div class="card-block">
            <form id="second" action="/" method="post" novalidate="">



                <div class="form-group row" style="margin-bottom:0px;">
                    <label class="col-sm-2 col-form-label">Session</label>
                        <div class="col-sm-8">
                            <input id="facultyWload_sess_id" name="facultyWload_sess_id" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                       valueField:'sess_id',  textField:'sess_name',  method:'get',  url: '<?= url('/sessions') ?>',  onSelect: function(rec){} ">
                        </div>
                </div>



                <div class="form-group row" style="margin-bottom:0px;">
                    <label class="col-sm-2 col-form-label">School</label>
                    <div class="col-sm-8">
                        <input id="facultyWload_school_id" name="facultyWload_school_id"  class="easyui-combobox form-control" data-options="
                        valueField: 'school_id',
                        textField: 'school_name',
                        required:'true',
                        method:'get',
                        url: '<?= url('/schools') ?>',
                        onSelect: function(rec){
                            $('#facultyWload_dept_id').combobox('clear');
                            var urldept = '<?=url('/departments')?>?school_id='+rec.school_id;
                            $('#facultyWload_dept_id').combobox('reload', urldept);      
                        }">
                    </div>
                </div>


                <div class="form-group row" style="margin-bottom:0px;"> <label class="col-sm-2 col-form-label">Department</label>
                        <div class="col-sm-8">
                            <input id="facultyWload_dept_id" name="facultyWload_dept_id"  class="easyui-combobox form-control" data-options="
                   valueField:'dept_id', textField:'dept_name', method:'get', validType: 'exists[\'#facultyWload_dept_id\']',
                   url: '<?= url('/departments') ?>',
                   onSelect: function(rec){
                        $('#program_id').combobox('clear');
                        var urldept = '<?=url('/programs')?>?dept_id='+rec.dept_id;
                        $('#program_id').combobox('reload', urldept);
                    }">
                </div></div>



                <div class="form-group row" style="margin-bottom:0px;"> <label class="col-sm-2 col-form-label">Programs</label>
                        <div class="col-sm-8">
                            <input id="program_id" name="program_id"  class="easyui-combobox form-control" data-options="
                   valueField:'prog_id', textField:'prog_title', method:'get', 
                   validType: 'exists[\'#facultyWload_dept_id\']',
                   url: '<?= url('/programs') ?>',
                   onSelect: function(rec){ }">
                </div></div>


                <div class="form-group row" style="margin-bottom:0px;"><label class="col-sm-2 col-form-label">Date</label>
                        <div class="col-sm-8">
                        <input id="tac_end_date" type="text" name="tac_end_date" class="easyui-datebox form-control" data-options="formatter:myformatter,parser:myparser,required:'true'" ">
                    
                </div></div>

                


                <div class="form-group row" style="margin-bottom:0px;"><label class="col-sm-2 col-form-label">Report Type</label>
                        <div class="col-sm-8">
                        <input id="fco_attendance_type" name="fco_attendance_type" class="easyui-combobox form-control"  data-options="valueField:'id'
                                    ,textField:'text'
                                    ,required:'true'
                   					,validType: 'exists[\'#fco_attendance_type\']'
                                    ,data: [{id: '1',text: 'All',selected:true}
                                           ,{id: '3',text: 'Not Delivered'}
                                           ,{id: '2',text: 'Delivered'}
                                          ]">
                </div></div>                                          





                <div class="row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary m-b-0">Generate Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
        @stop

@section('pageScripts')
@stop