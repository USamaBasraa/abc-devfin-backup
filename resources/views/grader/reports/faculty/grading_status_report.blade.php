@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:1200px; margin:10px auto;">
<div class="easyui-panel" title="Grading Status Report" style="  padding:10px;">
<form method="post" id="scel" action="/assets/cms/reports/grading_report_status_pdf.php" target="_blank" >	
    <table id="ff" style="margin-left:auto; margin-right:auto;">
        <tr>
        	<td align="right">Session : </td>
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
            <td align="right">Date: </td>
            <td>
                <input type="date" id="aep_date" name="aep_date" style="width:300px;">
            </td>
        </tr> 
      
        <tr style="display:none">
                <td align="right" >Report Type : </td>
                <td>
                    <input id="report_type" name="report_type" style="width:300px;" 
                    class="easyui-combobox" data-options="
                       valueField:'id',
                       textField :'text',
                       data: [{
                                id: '1',
                                text: 'PDF',
                                selected: 'true'
                            }]
                       ">
                </td>
        </tr> 
        <tr>
            <td></td>
            <!-- <td height="50" align="center"><input id="aepr" name="aepr" type="submit" value="Genrate Report" 																			  						class="easyui-linkbutton" style="width:120px; height:30px;" ></input></td> -->
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