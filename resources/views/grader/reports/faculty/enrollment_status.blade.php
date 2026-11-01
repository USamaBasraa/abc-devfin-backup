@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')



<div style="width:1200px; margin:10px auto; " >
<div class="easyui-panel" title="Student Workload Report" style="  padding:10px;">
<form method="post" id="scel" action="/assets/cms/reports/student_workload_report.php"  >	
    <table id="ff" style="margin-left:auto; margin-right:auto;">
        <tr>
            <td align="right">Active Session: </td>
            <td align="left">
                <input id="swl_sess_id" name="swl_sess_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                   valueField:'id',
                   textField:'text',
                   method:'GET',
                    url: '<?=asset('cms/common/academic_session.json.php');?>?sel_sess_id=153',
                    onSelect: function(rec){
                                                        
                            $('#swl_school_id').combobox('clear');
                           
                            var urlsch = ' <?=asset('cms/common/schools.json.php');?>?sdpo_sess_id='+rec.id;
                            $('#swl_school_id').combobox('reload', urlsch);
   							
                            $('#swl_rmp_id').combobox('clear');  
                            $('#swl_dept_id').combobox('clear');
                            $('#swl_prog_id').combobox('clear')
                            $('#swl_shift_id').combobox('clear');
                            
                            $('#sess_name').val(rec.sess_name);
                            
                            
                         } ">
            </td>
            <td><input type="text" hidden="true" name="sess_name" id="sess_name"  /></td>
        </tr>
        <tr>
        	<td align="right">RoadMap Session: </td>
            <td><input id="swl_rmp_id" name="swl_rmp_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                       valueField:'id',
                       textField:'text',
                       method:'GET',
                      url: '<?=asset('cms/common/academic_session.json.php');?>',
                    ">
           </td>
        </tr>
       	<tr>
            <td align="right">School: </td>
            <td>
                 <input id="swl_school_id" name="swl_school_id" style="width:300px;" 
                    class="easyui-combobox" data-options="
                        valueField: 'school_id',
                        textField: 'school_name',
                        method:'GET',
                        url: '<?=url('/schools');?>',
                        
                        onSelect: function(rec){
                            
                        $('#swl_dept_id').combobox('clear');
                        
                        var urldept = '<?=asset('cms/common/departments.json.php');?>?sdpo_sess_id='+$('#swl_sess_id').combobox('getValue')+'&sdpo_school_id='+rec.school_id;
                        $('#swl_dept_id').combobox('reload', urldept);
                        
                        $('#swl_prog_id').combobox('clear');
                        $('#swl_shift_id').combobox('clear');
                        
                }">
            </td>
        </tr>
        <tr>
            <td align="right">Department: </td>
            <td>
                <input id="swl_dept_id" name="swl_dept_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                    valueField:'id',
                    textField:'text',
                    method:'GET',
                    url: '<?=asset('cms/common/departments.json.php');?>',
                    onSelect: function(rec){
                    	
                        $('#swl_prog_id').combobox('clear');
                        
                       var urlprog = '<?=asset('cms/common/staff_rights_programs.json.php');?>?sel_school_id='+$('#swl_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                        $('#swl_prog_id').combobox('reload', urlprog);
                                                    
                        $('#swl_shift_id').combobox('clear');
                        
                        var urlshifts ='<?=asset('cms/common/shifts.json.php');?>?sel_sess_id='+$('#swl_sess_id').combobox('getValue')+'&sel_school_id='+$('#swl_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                        $('#swl_shift_id').combobox('reload', urlshifts); 
           }" />
           </td>
        </tr>
        <tr>
            <td align="right">Program: </td>
            <td>
                <input id="swl_prog_id" name="swl_prog_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                    valueField:'id',
                    textField:'text',
                    method:'GET',
                    url: '<?=asset('cms/common/staff_rights_programs.json.php');?>',
                    onSelect: function(rec){
                            
                        $('#swl_shift_id').combobox('clear');
                        var urlshifts ='common/shifts.json.php?sel_sess_id='+$('#swl_sess_id').combobox('getValue')+'&sel_school_id='+$('#swl_school_id').combobox('getValue')+'&sel_dept_id='+$('#swl_dept_id').combobox('getValue')+'&sel_prog_id='+rec.id;
                        $('#swl_shift_id').combobox('reload', urlshifts); 
           }" />
           </td>
        </tr>
        <tr>
           <td align="right" >Shift: </td>
           <td>
               <input id="swl_shift_id" name="swl_shift_id" style="width:300px;"
                    class="easyui-combobox" data-options="
                        valueField:'id',
                        textField:'text',
                        method:'GET',
                    url: '<?=asset('cms/common/shifts.json.php');?>',
                        ">
            </td>
        </tr>
        <tr>
            <td align="right">Report Type:</td>
            <td>
            
            <input id="swl_student_status" name="swl_student_status" class="easyui-combobox" style="width:300px;"
                            data-options="valueField:'id'
                                    ,textField:'text'
                                    ,required:'true'
                   					,validType: 'exists[\'#swl_student_status\']'
                                    ,data: [{id: '1',text: 'Enrolled',selected:true}
                                           ,{id: '2',text: 'Not Enrolled'}
                                          ],onSelect: function(rec){
                                            if($( this ).val()=='1')
                                            {
                                                $('#course_type').show();
                                            }
                                            else
                                            {
                                                $('#course_type').hide();
                                            }
                                    }"
                >
                
           </td>
        </tr>
         <tr id="course_type">
            <td align="right">Course Type:</td>
            <td>
                <select class="easyui-combobox" name="swl_course_status" id="swl_course_status" style="width:300px;">
                    <option value="">--Select Type--</option>
                    <option value="A">Enrolled</option>
                    <option value="DisApprove">Cancelled </option>
                     <option value="Withdrawal">Withdraw </option>
                      <option value="Block">Blocked </option>
                </select>
           </td>
        </tr>
        <tr>
            <td align="right" width="107">Fee Status: </td>
            <td>
                <input id="swl_fee_status" name="swl_fee_status" class="easyui-combobox" style="width:300px;" 
                    data-options="valueField:'id',
                            	  textField:'text',
                            	  data: [{id: '',text: 'Select Status' ,selected : true },
                                    {'id':'Unpaid'                   , 'text':'Unpaid'},
                                    {'id':'Paid'                     , 'text':'Paid'},
                                    {'id':'Partially Paid'           , 'text':'Partially Paid'},
                                    {'id':'Installments'             , 'text':'Installments'},
                                    {'id':'Cr. Hrs. Full Paid'       , 'text':'Cr. Hrs. Full Paid'},
                                    {'id':'Cr. Hrs. Installment Paid', 	'text':'Cr. Hrs. Installment Paid'}
                                   ]"/>
            </td> 
        </tr>
        <tr>
            <td></td>
            <td height="50" align="center"><input id="swlr" name="swlr" type="submit" value="Generate Report" 																			  						class="easyui-linkbutton" style="width:120px; height:30px;" ></input></td>
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