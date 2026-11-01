@extends('template')
@section('mainBody')
<div class="page-header">
    <div class="page-header-title">
        <h4>Dashboard</h4>
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
    <div class="page-header-panel">Faculty Course Attendance</div>
    <div style="padding:10px 10px 20px 10px">



        <form id="ff" action="reports/faculty_course_attendance_report_action.php" method="post" enctype="multipart/form-data" target="new">
            <table style="margin-left:auto; margin-right:auto;" border="0" width="86%">
                <tr>
                    <td align="right">Session : </td>
                    <td> <!-- $('#cc').combobox('setValue', '001'); -->
                        <input id="facultyWload_sess_id" name="facultyWload_sess_id" placeholder="Sessions" style="width:300px;" class="easyui-combobox" data-options="
                       valueField:'sess_id',
                       textField:'sess_name',
                       method:'get',
                       url: '<?=url('/sessions')?>',
                       onSelect: function(rec){}
                       ">
                    </td>
                </tr>
                <tr>
                    <td align="right" style="width:300px;">School : </td>
                    <td>
                        <input id="facultyWload_school_id" name="facultyWload_school_id" style="width:300px;" class="easyui-combobox" data-options="
                        valueField: 'school_id',
                        textField: 'school_name',
                        required:'true',
                        method:'get',
                        url: '<?=url('/schools')?>',
                        onSelect: function(rec){
                            $('#facultyWload_dept_id').combobox('clear');
                            var urldept = 'common/departments.json.php?sdpo_school_id='+rec.id;
                            $('#facultyWload_dept_id').combobox('reload', urldept);      
                        }">
                    </td>
                </tr>
                <tr>
                    <td align="right">Department : </td>
                    <td>
                        <input id="facultyWload_dept_id" name="facultyWload_dept_id" style="width:300px;" class="easyui-combobox" data-options="
                   valueField:'dept_id',
                   textField:'dept_name',
                   method:'get',
                   validType: 'exists[\'#facultyWload_dept_id\']',
                   url: '<?=url('/departments')?>',
                   onSelect: function(rec){
            
                        
                   }">
                    </td>
                </tr>
                <tr>
                    <td align="right"> Date :</td>
                    <td><input id="tac_end_date" type="text" name="tac_end_date" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser,required:'true'" style="width:300px;">
                    </td>
                </tr>
                <tr>
                <tr>
                    <td align="right">Reprot Type: </td>
                    <td>
                        <input id="fco_attendance_type" name="fco_attendance_type" class="easyui-combobox" style="width:300px;" data-options="valueField:'id'
                                    ,textField:'text'
                                    ,required:'true'
                   					,validType: 'exists[\'#fco_attendance_type\']'
                                    ,data: [{id: '1',text: 'All',selected:true}
                                           ,{id: '3',text: 'Not Delivered'}
                                           ,{id: '2',text: 'Delivered'}
                                          ]">


                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" value="Generate Report" id="btnvalue" name="btnvalue" class="easyui-linkbutton" data-options="iconCls:'icon-search'" iconCls="icon-ok" style="padding:1px; width:105px; height:25px; font-size:12px; "></input>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="showtimetable"> </td>
                </tr>
            </table>
        </form>
        @stop
        @section('pageScripts')
        <script type="text/javascript">
            $(function() {
                $('#ff').form({
                    success: function(data) {

                        $("#showtimetable").html(data);

                        //$.messager.alert('Info', data, 'info');
                        //$('#ff').form('clear');
                        var btnvalue = $("#btnvalue").val();
                        // alert(btnvalue);
                        if (btnvalue == "Update") {
                            $("#btnvalue").attr('value', 'Add Record');
                            $("#rowid").attr('value', '')
                        }
                        var tt_sess_id = $("#tt_sess_id").val();
                        var sel_school_id = $("#tt_school_id").val();
                        var tt_dept_id = $("#tt_dept_id").val();
                        var facultyWload_sess_id = $("#facultyWload_sess_id").val();
                        var urlprogget = 'common/offered_program_courses.json.php?ranno=' + Math.floor((Math.random() * 10) + 1) + '&tt_sess_id=' + tt_sess_id + '&sel_school_id=' + sel_school_id + '&tt_dept_id=' + tt_dept_id + '&facultyWload_sess_id=' + facultyWload_sess_id;
                        $('#tblProgramCoursesOffered').datagrid('reload', urlprogget);

                    }
                });
            });
        </script>

    </div>
    @stop