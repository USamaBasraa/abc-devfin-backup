@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:1200px; margin:10px auto;">
<div class="card">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Admission Status</h3>
       
    </div>
<form method="post" id="scel" action="/assets/cms/reports/applicant_academic_summary.php" target="_blank" >	
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
                <td align="right">Report Type : </td>
                <td>
                    <input id="report_type_wise" name="report_type_wise" style="width:300px;" 
                    class="easyui-combobox" data-options="
                       valueField:'id',
                       textField :'text',
                       data: [{
                                id: '1',
                                text: 'School Wise',
                                selected: 'true'
                            },{
                                id: '2',
                                text: 'Department Wise',
                                
                            }]
                       ">
                </td>
        	</tr> 
          <tr>
      
      
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
            <td height="50" align="center"><input id="aepr" name="aepr" type="submit" value="Generate Report" 																			  						class="easyui-linkbutton" style="width:120px; height:30px;" ></input></td>
            <td><span id="errorMsg"></span></td>
        </tr>
 </table>
</div>
</div>
</div>
@stop
@section('javascript')

<script type="text/javascript">
$('#scel').on('submit', function(e) {
    var comboBoxValue = $('#aep_sess_id').combobox('getValue'); // Use 'combobox' method if it's an EasyUI element
    
    if (!comboBoxValue) {
        $('#errorMsg').html('Please Select Session');
        $('#errorMsg').css('color', 'red');
        setTimeout(function() {
            $('#errorMsg').html(''); // Remove the content after 2 seconds
        }, 2000);
        //alert('Please select an option from the combo box.');
        e.preventDefault(); // Prevent form submission if the combo box is empty
    }
});
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