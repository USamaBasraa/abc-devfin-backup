@extends('layouts.new_app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("213"); --}}
{{-- <div class="caC"> --}}
    <div class="">
    <h4 class="profile-overview-title">
        Course Attendance Detail
    </h4>
  <div class="form-card-container">   
<div class="row" style="margin: 0;">
    <div class="col-sm-6">        
    <div class="form-group " style="margin-bottom:0px;">
                <label class="col-form-label">Session</label>
                    <div class="">
                        <input id="facultyWload_sess_id" name="facultyWload_sess_id form-control" style="" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
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
            </div>
                <div class="col-sm-6">   
            <div class="form-group" style="margin-bottom:0px;">
                <label class="col-form-label">School</label>
                <div class="">
                    <input id="facultyWload_school_id" name="facultyWload_school_id form-control" style="width:550px;"  class="easyui-combobox form-control" data-options="
                    valueField: 'school_id',
                    textField: 'school_name',
                    required:'true',
                    method:'get',
                    url: '<?= url('/schools') ?>',
                    onSelect: function(rec){

                        var urlcourse = '<?=url('/teacher')?>?school_id='+rec.school_id+'&sess_id='+$('#facultyWload_sess_id').combobox('getValue')+'';
                        console.log(urlcourse);
                        $('#facultyWload_teacher_id').combobox('reload', urlcourse);
                    }">
                </div>
            </div>
        </div>

         <div class="col-sm-6">   
            <div class="form-group" style="margin-bottom:0px;">
                <label class="col-form-label">Faculty</label>
                <div class="">
                    <input id="facultyWload_teacher_id" name="facultyWload_teacher_id form-control"  style="width:550px;" class="easyui-combobox form-control" data-options="
                    valueField: 'employee_id',
                    textField: 'emp_f_name',
                    required:'true',
                    method:'get',
                    onSelect: function(rec){
                        var urlcourse = '<?=url('/course_offering_exam')?>?co_status=E&sel_sess_id='+$('#facultyWload_sess_id').combobox('getValue')+'&sel_fc_emp_email='+rec.emp_email;;
                        $('#ca_course_id_name').combobox('reload', urlcourse);
                    }">
                </div>
            </div>
        </div>

         <div class="col-sm-6">   
            <div class="form-group" style="margin-bottom:0px;">
                <label class="col-form-label">Course</label>
                <div class="">
                    <input id="ca_course_id_name" name="ca_course_id_name form-control" style="width:550px;"
                    class="easyui-combobox" data-options="
                       valueField:'co_id',
                       textField :'course_cd_name',
                       method:'get',
                       validType: 'exists[\'#ca_course_id_name\']',
                       data: [
                        {id:0, course_cr_sec:'No Course'}
                       ],
                       formatter: function (row) {

                        var s = '<table width=100%><tr><td colspan=3><b>'+row.course_cd_name+'</b></td></tr><tr><td width=60%>'+row.course_code+'</td><td width=20%>'+row.co_course_cr+'</td><td width=20%>'+row.co_section+'</td></tr></table>';
                        return s;
                       }
                      ,filter: function(q, row){
                            var opts = $(this).combobox('options');
                            return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
                        }">
                </div>
            </div>
        </div>

            <div class="row">
                <label class="col-sm-2"></label>

                <div style="padding-left:120px" class="col-sm-10">
                    <i style="cursor:pointer" onclick="goToPDF();" class="fa fa-file-pdf-o fa-2x">PDF</i>
                 <!--   <i style="color:green;cursor:pointer" onclick="goToEXCEL();" class="fa fa-file-excel-o fa-2x">Excel</i>-->
                </div>

            </div>

    </div>
    </div>
</div>
<style>
   .profile-overview-title {
    margin: 24px !important;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}
/* ******************************************************************************************************** */

.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}

/* Main content area spacing: 25px left/right, 45px top/bottom */
.caC {
    padding: 45px 25px !important;
}

/* Panel wrapper */
.row.easyui-panel.caC {
    background: #ffffff;
    /* border-radius: 8px; */
    /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06); */
    padding: 20px !important;
}

/* Form groups */
.form-group {
    margin-bottom: 18px !important;
}

.form-group .col-form-label {
    display: block;
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
}

/* Fields full width */
.easyui-combobox.form-control,
.easyui-combobox,
input[id^="facultyWload_"],
#ca_course_id_name {
    width: 100% !important;
    max-width: 100%;
    border-radius: 6px;
    /* border: 1px solid #d5d9e0; */
    padding: 6px 10px;
    font-size: 14px;
    /* transition: border-color 0.2s ease, box-shadow 0.2s ease; */
}

.easyui-combobox.form-control:focus,
.easyui-combobox:focus {
    border-color: #4a90e2;
    /* box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15); */
    outline: none;
}

/* PDF button style */
.fa.fa-file-pdf-o {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ffffff !important;
    background: #e74c3c;
    font-size: 15px !important;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    /* box-shadow: 0 2px 6px rgba(231, 76, 60, 0.3); */
    /* transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease; */
}

.fa.fa-file-pdf-o:hover {
    /* background: #c0392b; */
    /* transform: translateY(-1px); */
    /* box-shadow: 0 4px 10px rgba(231, 76, 60, 0.4); */
}

.fa.fa-file-pdf-o:before {
    font-size: 18px;
}

/* Action row */
.row > .col-sm-10 {
    padding-left: 0 !important;
    margin-top: 10px;
}

/* Combobox dropdown panel (EasyUI generated) */
.combo-panel {
    border-radius: 6px !important;
    /* box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important; */
}



/* PDF button style */
.fa.fa-file-pdf-o {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ffffff !important;
    background: #e74c3c;
    font-size: 15px !important;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    /* box-shadow: 0 2px 6px rgba(231, 76, 60, 0.3); */
    /* transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease; */
}

.fa.fa-file-pdf-o:hover {
    /* background: #c0392b; */
    /* transform: translateY(-1px); */
    /* box-shadow: 0 4px 10px rgba(231, 76, 60, 0.4); */
}

.fa.fa-file-pdf-o:before {
    font-size: 18px;
}

/* Action row - same left alignment & spacing as the fields above */
.row > .col-sm-10 {
    padding-left: 0 !important;
    margin-left: 15px;
    margin-top: 20px;
}


/* Card Container */
.form-card-container {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); */
    max-width: 1000px;
    margin: 0 auto;
    /* font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; */
}

/* Form Labels */
.form-group label,
.col-form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 8px;
}

/* Input Fields, Select Dropdowns & EasyUI Comboboxes */
.form-control,
select,
input[type="text"],
input[type="file"],
.easyui-combobox {
    width: 100% !important;
    height: 44px !important;
    padding: 8px 14px !important;
    font-size: 14px !important;
    color: #333333;
    background-color: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    transition: all 0.2s ease-in-out;
    outline: none;
    box-sizing: border-box;
}

/* Input Hover & Focus States */
.form-control:focus,
select:focus,
input[type="text"]:focus,
.easyui-combobox:focus {
    border-color: #227367 !important;
    /* box-shadow: 0 0 0 3px rgba(34, 115, 103, 0.15) !important; */
}

/* File Upload Custom Styling */
input[type="file"]::file-selector-button {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    margin-right: 10px;
    transition: background 0.2s ease;
}

input[type="file"]::file-selector-button:hover {
    background: #e2e8f0;
}

/* Button Group Container */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

/* Submit Button */
.btn-submit,
button[type="submit"] {
    background-color: #227367;
    color: #ffffff;
    font-weight: 600;
    font-size: 15px;
    padding: 10px 28px;
    border: 1px solid #227367;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-submit:hover,
button[type="submit"]:hover {
    background-color: #1a5c52;
    border-color: #1a5c52;
    transform: translateY(-1px);
    /* box-shadow: 0 4px 12px rgba(34, 115, 103, 0.2); */
}

/* Reset Button */
.btn-reset,
button[type="reset"] {
    background-color: #ffffff;
    color: #227367;
    font-weight: 600;
    font-size: 15px;
    padding: 10px 28px;
    border: 1.5px solid #227367;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-reset:hover,
button[type="reset"]:hover {
    background-color: #f0fdfa;
    transform: translateY(-1px);
}

/* EasyUI Generated Classes ko Full Width dene ke liye */
.easyui-combobox,
.combo,
.textbox,
.textbox-text {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

.combo {
    height: 42px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
    display: flex !important;
    align-items: center;
    transition: all 0.2s ease-in-out;
}

.textbox-text {
    height: 100% !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    border: none !important;
    border-radius: 8px 0 0 8px !important;
    outline: none !important;
    box-shadow: none !important;
}

.combo-focused {
    border-color: #227367 !important;
    /* box-shadow: 0 0 0 3px rgba(34, 115, 103, 0.15) !important; */
}
/* PDF Button Style */
.fa.fa-file-pdf-o {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #ffffff !important;
    background: #1F776E !important;
    font-size: 15px !important;
    font-weight: 500;
    padding: 10px 28px;
    border-radius: 5px !important;
    cursor: pointer;
    border: none;
    box-shadow: none;
    transition: background 0.2s ease, transform 0.15s ease;
}

.fa.fa-file-pdf-o:hover {
    background: #185e57 !important;
    transform: translateY(-1px);
}

.fa.fa-file-pdf-o:before {
    font-size: 16px;
}
/* Card Container - Updated for 100% Full Width */
.form-card-container {
    background: #ffffff;
    padding: 0 !important;
    border-radius: 5px !important;
    border: 1px solid #e2e8f0; /* Outer Border */
    /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); */
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    /* font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; */
}
/* Purani .form-card-container ya .caC styling ko is se replace karein */
.form-card-container {
    background: #ffffff;
    padding: 30px !important;
    border-radius: 5px !important;
    border: 1px solid #e2e8f0 !important;
    
    /* Fixed Width Formula for margins on both sides */
    width: calc(100% - 48px) !important; 
    margin: 24px !important;
    
    box-sizing: border-box !important;
}

.profile-overview-title {
    display: block !important;
    position: relative !important;
    z-index: 10 !important;
    margin-bottom: 20px !important;
    font-size: 20px !weight: 700 !important;
    color: #2d2d2d !important;
    visibility: visible !important;
    opacity: 1 !important;
}


/* EasyUI Inner Input Field - Remove Inner Border & Outline */
.textbox-text,
.textbox-text:focus,
.textbox-text:active,
.form-control.textbox-text,
input[id^="facultyWload_"].textbox-text,
#ca_course_id_name.textbox-text {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

/* Outer Combobox Wrapper - Single Unified Border */
.combo {
    height: 42px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
    display: flex !important;
    align-items: center;
    transition: all 0.2s ease-in-out;
}

/* Combobox Active/Focus State - Single Outer Highlight */
.combo-focused {
    border-color: #227367 !important;
    box-shadow: 0 0 0 3px rgba(34, 115, 103, 0.15) !important;
}
/* =========================================================
   FIGMA OVERRIDE - COURSE ATTENDANCE DETAIL
   Add this at the END of your existing <style>
========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* ---------- Page Title ---------- */
.profile-overview-title {
    margin: 24px 24px 16px !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}


/* ---------- Main Form Card ---------- */
.form-card-container {
    width: calc(100% - 48px) !important;
    max-width: none !important;

    margin: 0 24px 24px !important;
    padding: 20px !important;

    background: #ffffff !important;
    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
    box-sizing: border-box !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* ---------- Form Grid ---------- */
.form-card-container > .row {
    margin-left: -10px !important;
    margin-right: -10px !important;
}

.form-card-container > .row > .col-sm-6 {
    padding-left: 10px !important;
    padding-right: 10px !important;
}


/* ---------- Form Groups ---------- */
.form-card-container .form-group {
    margin-bottom: 16px !important;
}


/* ---------- Labels ---------- */
.form-card-container .col-form-label,
.form-card-container .form-group label {
    display: block !important;

    margin: 0 0 8px !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.3 !important;

    color: #000000 !important;
}


/* =========================================================
   EASYUI COMBOBOX
========================================================= */

.form-card-container .combo,
.form-card-container .textbox {
    width: 100% !important;
    max-width: 100% !important;

    height: 40px !important;

    background: #ffffff !important;
    border: 1px solid #d9d9d9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
    box-sizing: border-box !important;

    display: flex !important;
    align-items: center !important;
}


/* Inner EasyUI input */
.form-card-container .textbox .textbox-text,
.form-card-container .combo .textbox-text {
    width: 100% !important;
    height: 38px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;

    background: transparent !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    line-height: 38px !important;
    box-sizing: border-box !important;
}


/* Placeholder */
.form-card-container .textbox-text::placeholder {
    color: #64748b !important;
    opacity: 1;
}


/* Dropdown arrow area */
.form-card-container .textbox-addon,
.form-card-container .textbox-addon-right {
    height: 38px !important;
}

.form-card-container .combo-arrow {
    width: 36px !important;
    height: 38px !important;

    background-color: transparent !important;
    opacity: .65;
}


/* Focus */
.form-card-container .combo-focused,
.form-card-container .textbox-focused {
    border-color: #1f776e !important;
    box-shadow: none !important;
}


/* Remove inner borders */
.form-card-container .textbox-text,
.form-card-container .textbox-text:focus,
.form-card-container .textbox-text:active {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}


/* Original inputs before EasyUI conversion */
#facultyWload_sess_id,
#facultyWload_school_id,
#facultyWload_teacher_id,
#ca_course_id_name {
    width: 100% !important;
    max-width: 100% !important;
}


/* =========================================================
   PDF ACTION ROW
========================================================= */

.form-card-container > .row > .row {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 10px !important;
}

.form-card-container > .row > .row > .col-sm-2 {
    display: none !important;
}

.form-card-container > .row > .row > .col-sm-10 {
    width: auto !important;
    max-width: none !important;

    flex: none !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* ---------- Download PDF Button ---------- */
.form-card-container .fa.fa-file-pdf-o {
    min-width: 134px !important;
    height: 50px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 18px !important;

    background: #1f776e !important;
    color: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;
    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 0 !important;
    font-weight: 500 !important;

    cursor: pointer !important;
}


/* Hide PDF icon to match Figma */
.form-card-container .fa.fa-file-pdf-o::before {
    display: none !important;
}


/* Change visible text from "PDF" to "Download PDF" */
.form-card-container .fa.fa-file-pdf-o::after {
    content: "Download PDF";

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 500 !important;

    color: #ffffff !important;
}


.form-card-container .fa.fa-file-pdf-o:hover {
    background: #17675f !important;
    transform: none !important;
}


/* =========================================================
   EASYUI DROPDOWN
========================================================= */

.combo-panel {
    border: 1px solid #d9d9d9 !important;
    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
}

.combobox-item {
    padding: 8px 12px !important;

    font-size: 13px !important;
    color: #252525 !important;
}

.combobox-item-hover {
    background: #f1f7f6 !important;
}

.combobox-item-selected {
    background: rgba(31, 119, 110, 0.12) !important;
    color: #17675f !important;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    .profile-overview-title {
        margin: 18px 16px 14px !important;
        font-size: 18px !important;
    }

    .form-card-container {
        width: calc(100% - 32px) !important;
        margin: 0 16px 20px !important;
        padding: 16px !important;
    }

    .form-card-container > .row {
        margin: 0 !important;
    }

    .form-card-container > .row > .col-sm-6 {
        width: 100% !important;
        padding: 0 !important;
    }

    .form-card-container > .row > .row {
        padding: 0 !important;
    }
}
</style>
@stop
@section('javascript')


<script type="text/javascript">

    function goToPDF(){
        // alert('kk');
      var link = 'https://cms.bnu.edu.pk/assets/cms/reports/course_wise_attendance_report.php?action=grader&ca_course_id_name='+$('#ca_course_id_name').combobox('getValue')+'&cwa_report_type=1';
       // var link = 'http://172.20.251.100/assets/cms/reports/course_wise_attendance_report.php?action=grader&ca_course_id_name='+$('#ca_course_id_name').combobox('getValue')+'&cwa_report_type=1';
      //  window.location = link;
        window.open(link);
    }
    function goToEXCEL(){
        // alert('kk');
        var link = 'https://cms.bnu.edu.pk/assets/cms/reports/course_wise_attendance_report.php?action=grader&ca_course_id_name='+$('#ca_course_id_name').combobox('getValue')+'&cwa_report_type=2';
      //  window.location = link;
        window.open(link);
    }

function iframe(){
    $('#frameID').show();
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
