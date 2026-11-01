@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div style="width:500px; margin:10px auto;">
<div class="easyui-panel" title="Academic History" style="padding:10px; overflow: hidden; !important">
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Session From</label>
                    <div class="col-sm-8">
                        <input id="from_sess_id" name="from_sess_id" style="width:300px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                   valueField:'sess_id',
                   textField:'sess_name',
                   url: '<?= url('/sessions') ?>',
                   method:'GET',
                   onSelect: function(rec){
                    var urlcourse = '<?=url('/course_offering_exam')?>?sess_id='+rec.sess_id;
                        <!-- $('#ca_course_id_name').combobox('reload', urlcourse);    -->
                   } ">
                    </div>
            </div>
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Session To</label>
                    <div class="col-sm-8">
                        <input id="to_sess_id" name="to_sess_id" style="width:300px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                   valueField:'sess_id',
                   textField:'sess_name',
                   url: '<?= url('/sessions') ?>',
                   method:'GET',
                   onSelect: function(rec){
                    var urlcourse = '<?=url('/course_offering_exam')?>?sess_id='+rec.sess_id;
                        <!-- $('#ca_course_id_name').combobox('reload', urlcourse);    -->
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
                <label class="col-sm-4 col-form-label m_title">Registration</label>
                    <div class="col-sm-8">
                        <input name="reg_no" id="reg_no" class="easyui-textbox" data-options="required:'true'"  style="width:140px">
                    </div>
            </div>

            <div class="row">
                <label class="col-sm-2"></label>

                <div style="padding-left:120px" class="col-sm-10">
                <button class="btn btn-primary" onclick="goToPDF();">Generate</button>

                </div>

            </div>
            <div style="margin-left:4">
                <label class="hidegif" style="display:none">Please Wait System is Generating PDF</label>
            <img class="hidegif" style="display:none"  class="img-fluid" src="/images/loading-gears-animation.gif"  width="22%" >
            <div>
    </div>
<div>

@stop


@section('javascript')

   <!-- <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/default/easyui.css">
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.easyui.min.js"></script> -->
<script type="text/javascript">
    $(function(){
        $('#reg_no').textbox('textbox').css('text-transform','capitalize');
    })
    function goToPDF(){
        $('#pdfIcon').hide();
        $('.hidegif').show();
        var reg_no = $('#reg_no').val();

        var from_sess_id =  $('#from_sess_id').combobox('getValue');
        var to_sess_id =  $('#to_sess_id').combobox('getValue');
        var school_id =  $('#facultyWload_school_id').combobox('getValue');
        var dept_id =  $('#facultyWload_dept_id').combobox('getValue');
        var prog_id =  $('#asdw_prog_id').combobox('getValue');

        var link = 'https://staff.bnu.edu.pk/HOD/academic_history_report.php?grader_entry_user_id='+reg_no+'&action=grader&from_sess_id='+from_sess_id+'&to_sess_id='+to_sess_id+'&ah_school_id='+school_id+'&ah_dept_id='+dept_id+'&ah_prog_id='+prog_id;

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
