@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div class="easyui-panel" title="Student Semester Performance" style="width:100%;padding:10px; margin-left:auto; margin-right:auto;">
    <form id="ff" method="post" enctype="multipart/form-data">
   
    <table align="center" cellpadding="4px">
 <tr>
            <td align="right" width="150">Enrollment Session:</td>
            <td>
                <input id="sec_rm_id" name="sec_rm_id" style="width:140px;" 
            		class="easyui-combobox" data-options="
                   				valueField:'id',
                   				textField:'text',
                                
                                url:  '<?=asset('cms/common/academic_session.json.php');?>'
                 ">
            </td>
        </tr> 
    <tr>
    <td align="right">Registration No.:</td>
    <td>
    <input name="reg_no" id="reg_no" class="f1 easyui-textbox"   style="width:140px"></input>
   </td>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td>
      
 <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()" style="width:140px">Print</a>
 
    </td>
    </tr>  
    <tr>
      <td align="right"> <input type="checkbox" id="all" class="checkbox" name="all" checked="checked"></td>
      <td align="center"> <input type="checkbox" id="attendance" class="easyui-checkbox" name="attendance"></td>
      <td align="left"> <input  type="checkbox" id="marks" class="easyui-checkbox" name="marks"></td>
    <tr>
    <tr>
      <td align="right"> All</td>
      <td align="center"> Attendacnce</td>
      <td align="left"> Marks</td>
    <tr>
    <td>&nbsp;</td>
    <td>  
   
    </tr>
    
    </table>
    </form>
    </div>

    <div style="width: 80%;margin-left: 10%;" id="showrecord"></div>






@stop
@section('javascript')

<script type="text/javascript">

$('#ff').form({
    success:function(data){
    $.messager.alert('Info', data, 'info');
	$('#tdTtPuposal').html(data);
    }
    });

  function saveUser(){
	  var get_studtn_id = $("#reg_no").val();
	  var get_sec_rm_id = $('#sec_rm_id').combobox('getValue');		
    var all = $('#all').prop('checked');
    var attendance = $('#attendance').prop('checked');
    var marks = $('#marks').prop('checked');
   // alert($('#all').prop('checked')); 
    //return false;
   
window.open("/assets/cms/reports/student_semester_performance_report.php?entry_user_id="+get_studtn_id+"&sc_co_sess_id="+get_sec_rm_id+"&all="+all+"&attendance="+attendance+"&marks="+marks);
		
		
  }




</script>
@stop