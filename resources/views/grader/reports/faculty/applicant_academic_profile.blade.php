@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:1200px; margin:10px auto;">
<div class="easyui-panel" title="Applicant Academic Profile" style="  padding:10px;">
<form method="post" id="scel" action="/assets/cms/reports/applicant_academic_profile_report.php" target="_blank" >	
    <table id="ff" style="margin-left:auto; margin-right:auto;">
        <tr>
        	<td align="right">Session: </td>
            <td><input id="aep_sess_id" name="aep_sess_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                       valueField:'id',
                       textField:'text',
                       method:'GET',
                       url: '<?=asset('cms/common/academic_session.json.php');?>',
                         onSelect: function(rec){
                         
                         
                        	//var urlforcombo = './admission/admission_schedules.json.php?sel_sess_id='+rec.id;
                                              
                        // $('#aep_admission_sch').combobox('reload', urlforcombo);
                        } ">
           </td>
        </tr>
       	<tr>
            <td align="right">School: </td>
            <td>
                 <input id="aep_school_id" name="aep_school_id" style="width:300px;" 
                    class="easyui-combobox" data-options="
                        valueField: 'school_id',
                        textField: 'school_name',
                        method:'GET',
                       url: '<?=url('/schools');?>',
                        
                        onSelect: function(rec){
                            
                        $('#aep_dept_id').combobox('clear');
                        
                        var urldept = '<?=asset('cms/common/departments.json.php');?>?sdpo_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sdpo_school_id='+rec.school_id;
                        $('#aep_dept_id').combobox('reload', urldept);
                        
                        $('#aep_admission_sch').combobox('clear');
                     //  var urlforcombo = './admission/admission_schedules.json.php?sel_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sel_school_id='+rec.id;
                                              
                     //    $('#aep_admission_sch').combobox('reload', urlforcombo);
                        
                        $('#aep_prog_id').combobox('clear');
                        $('#aep_shift_id').combobox('clear');
                        
                }">
            </td>
        </tr>
        <tr>
            <td align="right">Department: </td>
            <td>
                <input id="aep_dept_id" name="aep_dept_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                    valueField:'id',
                    textField:'text',
                    method:'GET',
                       url: '<?=asset('cms/common/departments.json.php');?>',
                   
                    onSelect: function(rec){
                    	
                        
                       var urlprog = '<?=asset('cms/common/staff_rights_programs.json.php');?>?sel_school_id='+$('#aep_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                        $('#aep_prog_id').combobox('reload', urlprog);
                        
                         $('#aep_admission_sch').combobox('clear');
                      //  var urlforcombo = './admission/admission_schedules.json.php?sel_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sel_school_id='+$('#aep_school_id').combobox('getValue')+'&sel_dept_id='+rec.id;
                        
                       // $('#aep_admission_sch').combobox('reload', urlforcombo);  
                                                    
                        $('#aep_shift_id').combobox('clear');
                        
                      
           }" />
           </td>
        </tr>
        <tr>
            <td align="right">Program: </td>
            <td>
                <input id="aep_prog_id" name="aep_prog_id" style="width:300px;" 
                class="easyui-combobox" data-options="
                    valueField:'prog_id',
                    textField:'text',
                    method:'GET',
                     url: '<?=asset('cms/common/staff_rights_programs.json.php');?>',
                  
                    onSelect: function(rec){
                            
                         $('#aep_admission_sch').combobox('clear');
                        var urlforcombo = '<?=asset('cms/admission/admission_schedules.json.php');?>?sel_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sel_school_id='+$('#aep_school_id').combobox('getValue')+'&sel_dept_id='+$('#aep_dept_id').combobox('getValue')+'&aep_prog_id='+rec.prog_id;
                        <!-- url: '<?=asset('cms/admission/admission_schedules.json.php');?>?sel_sess_id='+rec.id, -->
                      
                         $('#aep_admission_sch').combobox('reload', urlforcombo);    
                            
                        $('#aep_shift_id').combobox('clear');
                        
                        var urlshifts ='<?=asset('cms/common/shifts.json.php');?>?sel_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sel_school_id='+$('#aep_school_id').combobox('getValue')+'&sel_dept_id='+$('#aep_dept_id').combobox('getValue')+'&sel_prog_id='+rec.prog_id;
                        $('#aep_shift_id').combobox('reload', urlshifts); 
                        
                      
                        
           }" />
           </td>
        </tr>
        <tr>
           <td align="right" >Shift: </td>
           <td>
               <input id="aep_shift_id" name="aep_shift_id" style="width:300px;"
                    class="easyui-combobox" data-options="
                        valueField:'id',
                        textField:'text',
                        method:'GET',
                     url: '<?=asset('cms/common/shifts.json.php');?>',
                        onSelect: function(rec){
                        
                       	$('#aep_admission_sch').combobox('clear');
                           
                        var urlforcombo = '<?=asset('cms/admission/admission_schedules.json.php');?>?sel_sess_id='+$('#aep_sess_id').combobox('getValue')+'&sel_school_id='+$('#aep_school_id').combobox('getValue')+'&sel_dept_id='+$('#aep_dept_id').combobox('getValue')+'&aep_prog_id='+$('#aep_prog_id').combobox('getValue')+'&sel_shift_id='+rec.id;
                                              
                         $('#aep_admission_sch').combobox('reload', urlforcombo);
                        
                        }">
            </td>
        </tr>
        <tr>
            <td align="left">Admission Schedules: </td>
            <td align="left">
                <input id="aep_admission_sch" name="aep_admission_sch" style="width:300px;" 
                	class="easyui-combobox" data-options="
                  		 	valueField:'admission_sch_id',
                   			textField :'admission_sch_name',
                   url: ''">
            </td>
        </tr>
         <tr>
                <td align="right">Report Type: </td>
                <td>
                    <input id="report_type" name="report_type" style="width:300px;" 
                    class="easyui-combobox" data-options="
                       valueField:'id',
                       textField :'text',
                       data: [{
                                id: '1',
                                text: 'PDF',
                                selected: 'true'
                            },{
                                id: '2',
                                text: 'Excel'
                            }]
                       ">
                </td>
        	</tr> 
          <tr>
            <td></td>
            <td height="50" align="center"><input id="aepr" name="aepr" type="submit" value="Generate Report" 																			  						class="easyui-linkbutton" style="width:120px; height:30px;" ></input></td>
        </tr>
 </table>
</div>
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