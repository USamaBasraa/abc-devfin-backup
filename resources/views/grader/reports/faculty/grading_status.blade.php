@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:500px; margin:10px auto;">
<div class="easyui-panel" title="Grading Status" style="padding:10px;">
<form method="post" id="gs" action="/assets/cms/reports/grading_status_report.php" target="_blank" >	
    <table id="ff" style="margin-left:auto; margin-right:auto;">
        <tr>
        	<td align="right">Session: </td>
            <td><input id="gs_sess_id" name="gs_sess_id" style="width:300px;" 
                	class="easyui-combobox" data-options="
                       valueField:'id',
                       textField:'text',
                       url: '<?=asset('cms/common/academic_session.json.php');?>',
                       onSelect: function(rec){
                       
                        $('#gs_school_id').combobox('clear');
                        $('#gs_dept_id').combobox('clear');
                        $('#gs_shift_id').combobox('clear');
                        $('#gs_course_id').combobox('clear');
                        $('#gs_co_id').val('');
                        $('#gs_sess_name').val(rec.text);
                       
                       }">
                       <input type="hidden" name="gs_sess_name" id="gs_sess_name" />
           </td>
        </tr>
       	<tr>
			<td align="right">School: </td>
            <td><input id="gs_school_id" name="gs_school_id" style="width:300px;" 
            class="easyui-combobox" data-options="
                        valueField: 'school_id',
                        textField: 'school_name',
                        method:'GET',
                       url: '<?=url('/schools');?>',
                        onSelect: function(rec){                            
                            $('#gs_dept_id').combobox('clear');                         
                            var urldept = '<?=asset('cms/common/departments.json.php');?>?sdpo_sess_id='+$('#gs_sess_id').combobox('getValue')+'&sdpo_school_id='+rec.school_id;
                            $('#gs_dept_id').combobox('reload', urldept);     
                            $('#gs_shift_id').combobox('clear');
                            $('#gs_course_id').combobox('clear'); 
                        	$('#gs_co_id').val('');                     
              	  }">
            </td>
        </tr>
        <tr>
            <td align="right">Department: </td>
            <td><input id="gs_dept_id" name="gs_dept_id" style="width:300px;" 
                	class="easyui-combobox" data-options="
                        valueField:'id',
                        textField:'text',
                        url: '<?=asset('cms/common/departments.json.php');?>',
                        onSelect: function(rec){
                    	                            
                            $('#gs_shift_id').combobox('clear');
                            var urlshifts = '<?=asset('cms/common/shifts.json.php');?>?sel_sess_id='+$('#gs_sess_id').combobox('getValue')+'&sel_school_id='+$('#gs_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                            
                            $('#gs_shift_id').combobox('reload', urlshifts); 
                        
                            $('#gs_course_id').combobox('clear');
                            var urlLoadCourse = '<?=asset('cms/common/course_offering.json.php');?>?sel_sess_id='+$('#gs_sess_id').combobox('getValue')+'&sel_school_id='+$('#gs_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                            
                           
                            $('#gs_course_id').combobox('reload', urlLoadCourse);
                        	$('#gs_co_id').val('');
           }" />
           </td>
        </tr>
        <tr>
           <td align="right" >Shift: </td>
           <td><input id="gs_shift_id" name="gs_shift_id" style="width:300px;"
                    class="easyui-combobox" data-options="
                        valueField:'id',
                        textField:'text',
                        url: '<?=asset('cms/common/shifts.json.php');?>',
                        onSelect: function(rec){
                           
                         $('#gs_course_id').combobox('clear');
                         var urlLoadCourse = '<?=asset('cms/common/course_offering.json.php');?>?sel_sess_id='+$('#gs_sess_id').combobox('getValue')+'&sel_school_id='+$('#gs_school_id').combobox('getValue')+'&sel_dept_id='+$('#gs_dept_id').combobox('getValue')+'&sel_shift_id='+rec.id
                            
                         $('#gs_course_id').combobox('reload', urlLoadCourse);
                         
                        $('#gs_co_id').val('');
                        
                        }">
            </td>
        </tr>
        <tr>
            <td align="right"> Offered Courses: </td>
            <td><input id="gs_course_id" name="gs_course_id" style="width:300px;" 
                	 class="easyui-combobox" data-options="
                         valueField:'id',
                         textField :'course_cr_sec',
                         url: '<?=asset('cms/common/course_offering.json.php');?>',                         
                              
                        formatter: function (row) {
                        var s = '<table width=100%><tr><td colspan=3><b>'+row.course_name+'</b></td></tr><tr><td width=60%>'+row.course_code+'</td><td width=20%>'+row.co_course_cr+'</td><td width=20%>'+row.co_section+'</td></tr></table>';
                        return s;
                       }
                       ,filter: function(q, row){
                        var opts = $(this).combobox('options');
                        return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
                },
                onSelect: function(rec){
                   
                 $('#gs_co_id').val(rec.co_id);
                
                }
               ">
            </td>
         </tr>
      	<tr>
        	<td><input type="hidden" name="gs_co_id" id="gs_co_id" /></td>
        	<td height="50" align="center"><input id="gsr" name="gsr" type="submit" value="Generate Report" 																			  						class="easyui-linkbutton" style="width:120px; height:30px;" ></input></td>
    	</tr>
 </table>
</div>
</div>

@stop
@section('javascript')

<script type="text/javascript">

function goToPDF(){
        $('#pdfIcon').hide();
        $('.hidegif').show();
        var reg_no = $('#reg_no').val();
        var from_sess_id = $('#from_sess_id').val();
        var from_sess_id = $('#from_sess_id').val()
        var to_sess_id = $('#to_sess_id').val()
        var link = 'http://172.20.250.100/public/assets/cms/HOD/academic_history_report.php?grader_entry_user_id='+reg_no+'&action=grader&from_sess_id='+from_sess_id+'&to_sess_id='+to_sess_id;

        setTimeout(function(){ 
            $('.hidegif').hide();          
            window.open(
                link,
                '_blank' // <- This is what makes it open in a new window.
                );
            $('#pdfIcon').show();
        }, 2000);    
       
    }
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