@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:600px; margin:10px auto;">
	<div class="easyui-panel" title="Student Assessment Report" style="padding:10px;">
		<form id="frm_student_assessment_report" name="frm_student_assessment_report" method="post" action="/assets/cms/reports/student_assessment_report.php" >
			<table style="margin-left:auto; margin-right:auto;">
            	<tr>
                    <td align="right"> Session: </td>
                    <td align="left">
                        <input id="sar_sess_id" name="sar_sess_id" style="width:300px;"
                            class="easyui-combobox" data-options="
                                    valueField:'id',
                                    textField:'text',
                                    url:  '<?=asset('cms/common/academic_session.json.php');?>',
                                  
                                    onSelect: function(rec){
                                        $('#sar_sess_name').val(rec.text);

                                    }">
						<input type="hidden" name="sar_sess_name" id="sar_sess_name" />
                    </td>
       	 		</tr>
        		<tr>
                    <td align="right"> School: </td>
                    <td><input id="sar_school_id" name="sar_school_id" style="width:300px;"
                            class="easyui-combobox" data-options="
                            valueField: 'school_id',
                        textField: 'school_name',
                            method:'GET',
                            url: '<?=url('/schools');?>',
                            onSelect: function(rec){
                                $('#sar_school_name').val(rec.text);
                                $('#sar_dept_id').combobox('clear');
                                var urldept =   '<?=asset('cms/common/departments.json.php');?>?school_id='+rec.school_id;
                                $('#sar_dept_id').combobox('reload', urldept);
                            }">
                        <input type="hidden" name="sar_school_name" id="sar_school_name" />
            		</td>
        		</tr>
        		<tr>
            		<td align="right">Department: </td>
            		<td><input id="sar_dept_id" name="sar_dept_id" style="width:300px;"
                            class="easyui-combobox" data-options="
                                   valueField:'id',
                                   textField:'text',
                                   url:  '<?=asset('cms/common/departments.json.php');?>',                                 
                                   onSelect: function(rec){
                                        $('#sar_dept_name').val(rec.text);
                                        enableButton();
                                    }">
                        <input type="hidden" name="sar_dept_name" id="sar_dept_name" />
                	</td>
        		</tr>
                </tr>
                    <td align="right"> Assessment: </td>
                    <td>
                        <select name="sar_assessment_id" id="sar_assessment_id" class="easyui-combobox" style="width: 300px;">
                            <option value="0" selected>--Select Assessment--</option>
                            <option value="3"> Midterm </option>
                            <option value="4"> Final </option>
                        </select>
                    </td>
                </tr>
         		<tr>
            		<td colspan="2">&nbsp;</td>
         		</tr>
        		<tr id="buttons">
       	  			<td height="33" colspan="4" align="center"> 
                    	<input type="submit" value="Generate" class="easyui-linkbutton" name="submit" id="submit"   style="height:30px; width:90px;" />
       	    			<a href="javascript:void(0)" class="easyui-linkbutton" onClick="clearForm()" 
                        	style="height:28px; width:90px;">Clear</a>
                    </td>
        		</tr>         
      		</table>
		</form>
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