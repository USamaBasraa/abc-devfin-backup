@extends('layouts.new_app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("123"); --}}
<style>
@media (max-width: 576px){
  .center{
    margin-left: 20px!important;
    margin-right: 10px!important;
}
}


.center{
margin-left: 300px; 
margin-right: 10px;
}


/* Page wrapper spacing */
.center {
    padding: 45px 25px !important;
}

/* Panel wrapper */
div[style*="width:500px"] {
    width: 100% !important;
    max-width: 500px;
    margin: 20px auto !important;
}

.easyui-panel[title="HR Attendance Report"] {
    background: #ffffff;
    border-radius: 8px;
    /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06); */
    padding: 24px !important;
}

/* Form groups */
.form-group.row {
    display: flex;
    align-items: center;
    margin-bottom: 18px !important;
    flex-wrap: wrap;
}

.form-group.row .col-form-label {
    flex: 0 0 110px;
    max-width: 110px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
    padding-right: 8px;
}

.form-group.row .col-sm-8 {
    flex: 1;
    max-width: calc(100% - 110px);
}

/* Fields - full width, via Bootstrap's .form-control */
.form-control {
    width: 100% !important;
    max-width: 100%;
    border-radius: 6px;
    border: 1px solid #d5d9e0;
    padding: 6px 10px;
    font-size: 14px;
    /* transition: border-color 0.2s ease, box-shadow 0.2s ease; */
}

.form-control:focus {
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

/* Action row */
.row .col-sm-10 {
    padding-left: 15px !important;
    margin-top: 15px;
}

/* Results card below the form */
.card-block {
    margin-top: 25px;
}

/* Combobox / datebox dropdown panel (EasyUI generated) */
.combo-panel,
.calendar {
    border-radius: 6px !important;
    /* box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important; */
}

/* Responsive tweak */
@media (max-width: 576px) {
    .form-group.row .col-form-label {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 4px;
    }
    .form-group.row .col-sm-8 {
        max-width: 100%;
    }
}
</style>
<style>
/* Container 24px margins on both sides */
.form-card-container {
    background: #ffffff;
    padding: 30px !important;
    border-radius: 5px !important;
    border: 1px solid #e2e8f0 !important;
    /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); */
    width: calc(100% - 48px) !important;
    margin: 24px !important;
    box-sizing: border-box !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Card Title */
.profile-overview-title {
    display: block !important;
    margin-bottom: 20px !important;
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #2d2d2d !important;
    margin: 24px !important;
}

/* Spacing & Form Labels */
.form-group {
    margin-bottom: 20px !important;
}

.form-group .col-form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 6px;
}

/* EasyUI Combobox/Datebox Full Width Fix */
.easyui-combobox,
.easyui-datebox,
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
}

/* Inner Double Border Remove Fix */
.textbox-text,
.textbox-text:focus,
.textbox-text:active,
.form-control.textbox-text {
    height: 100% !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

.combo-focused {
    border-color: #1F776E !important;
    /* box-shadow: 0 0 0 3px rgba(31, 119, 110, 0.15) !important; */
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
    transition: background 0.2s ease;
}

.fa.fa-file-pdf-o:hover {
    background: #185e57 !important;
}

.fa.fa-file-pdf-o:before {
    font-size: 16px;
}
/* =========================================================
   FIGMA OVERRIDE - HR ATTENDANCE REPORT
   Add at the END of existing <style>
========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================
   PAGE TITLE
========================= */

.profile-overview-title {
    display: block !important;
    margin: 24px 24px 16px !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}

/* Figma subtitle */
.profile-overview-title::after {
    content: "Improve Performance";
    display: block;

    margin-top: 2px;

    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 1.4;

    color: #475569;
}


/* =========================
   MAIN WHITE CARD
========================= */

.form-card-container {
    width: calc(100% - 48px) !important;
    max-width: none !important;
    /* min-height: 474px; */

    margin: 0 24px 30px !important;
    padding: 20px !important;

    background: #ffffff !important;
    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;

    box-sizing: border-box !important;
    font-family: 'Montserrat', sans-serif !important;
}


/* =========================
   2 COLUMN GRID
========================= */

.form-card-container > form > .row {
    margin-left: -10px !important;
    margin-right: -10px !important;
}

.form-card-container .col-sm-6 {
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.form-card-container .form-group {
    margin-bottom: 16px !important;
}


/* =========================
   LABELS
========================= */

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


/* =========================
   EASYUI DATE / COMBO FIELD
========================= */

.form-card-container .combo,
.form-card-container .textbox {
    width: 100% !important;
    max-width: 100% !important;
    height: 40px !important;

    display: flex !important;
    align-items: center !important;

    background: #ffffff !important;
    border: 1px solid #d9d9d9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
    box-sizing: border-box !important;
}


/* Inner input */
.form-card-container .textbox-text,
.form-card-container .form-control.textbox-text {
    width: 100% !important;
    height: 38px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    background: transparent !important;

    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;

    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    line-height: 38px !important;
    box-sizing: border-box !important;
}


/* Select placeholder */
.form-card-container .textbox-text::placeholder {
    color: #64748b !important;
    opacity: 1;
}


/* Right calendar/dropdown area */
.form-card-container .textbox-addon,
.form-card-container .textbox-addon-right {
    height: 38px !important;
}

.form-card-container .combo-arrow {
    width: 40px !important;
    height: 38px !important;

    background-color: transparent !important;
}


/* Focus */
.form-card-container .combo-focused,
.form-card-container .textbox-focused {
    border-color: #1f776e !important;
    box-shadow: none !important;
}


/* Original EasyUI fields */
.form-card-container .easyui-combobox,
.form-card-container .easyui-datebox,
#hr_employee_id,
#classification_id,
#start_date,
#end_date {
    width: 100% !important;
    max-width: 100% !important;
}


/* =========================
   PDF BUTTON ROW
========================= */

.form-card-container .col-sm-12 {
    margin-top: 0 !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
}


/* =========================
   DOWNLOAD PDF BUTTON
========================= */

.form-card-container .fa.fa-file-pdf-o {
    width: 188px !important;
    height: 50px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 20px !important;

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


/* Hide old PDF icon */
.form-card-container .fa.fa-file-pdf-o::before {
    display: none !important;
}


/* Figma button text */
.form-card-container .fa.fa-file-pdf-o::after {
    content: "Download PDF";

    font-family: 'Montserrat', sans-serif !important;
    font-size: 16px !important;
    font-weight: 500 !important;

    color: #ffffff !important;
}


.form-card-container .fa.fa-file-pdf-o:hover {
    background: #17675f !important;
    transform: none !important;
}


/* =========================
   EASYUI DROPDOWNS / CALENDAR
========================= */

.combo-panel,
.calendar {
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


/* =========================
   RESULT AREA
========================= */

.form-card-container .card-block {
    margin-top: 20px !important;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 767px) {

    .profile-overview-title {
        margin: 18px 16px 14px !important;
        font-size: 18px !important;
    }

    .profile-overview-title::after {
        font-size: 14px;
    }

    .form-card-container {
        width: calc(100% - 32px) !important;
        min-height: auto;

        margin: 0 16px 20px !important;
        padding: 16px !important;
    }

    .form-card-container > form > .row {
        margin: 0 !important;
    }

    .form-card-container .col-sm-6,
    .form-card-container .col-sm-12 {
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
</style>

<?php
//  print_r( auth()->user()->employee_id);exit;
// echo $tac_end_date;exit;?>
<div style="width:500px; margin:10px auto;display:none !important">
<div class="easyui-panel" title="HR Attendance Report" style="padding:10px; overflow: hidden; !important;border:none !important; padding-left:0 !important; padding-right:0 !important;">
        <form id="ff" action="/faculty_course_attendance_report_action" method="post" enctype="multipart/form-data" >
            {{-- <form id="ff" action="{{ route('faculty_course_attendance_report_action'), ['tac_end_date' => $tac_end_date] }}" method="post" enctype="multipart/form-data" > --}}
            @csrf
            <input type="hidden" id="hr_id" value="<?=$hr_employee_id?>">
            <?php if(auth()->user()->employee_id == 6455 or auth()->user()->employee_id == 1294){?>
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Employees</label>
                    <div class="col-sm-8">
                        <input id="hr_employee_id" name="hr_employee_id" style="width:350px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                            valueField:'employee_id', 
                            textField:'name',  
                            required:'true',                 
                            url: 'https://cms.bnu.edu.pk/hr/emp_details.json.php', 
                            method:'GET',
                            onSelect: function(rec){                  
                    
                   } ">
                    </div>
            </div>    
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Classification</label>
                    <div class="col-sm-8">
                        <input id="classification_id" name="classification_id" style="width:350px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                            valueField:'classification_id', 
                            textField:'classification_name',  
                            required:'true',                 
                            url: 'https://cms.bnu.edu.pk/hr/classification.json.php', 
                            method:'GET',
                            onSelect: function(rec){                  
                    
                   } ">
                    </div>
            </div>    
          
            <?php } ?>
           
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">Start Date</label>
                <div class="col-sm-8">
                <input id="start_date" type="text" style="width:350px;" name="start_date" class="easyui-datebox form-control" data-options="formatter:myformatter,parser:myparser,required:'true'">
            
                </div>
            </div> 
            <div class="form-group row" style="margin-bottom:0px;">
                <label class="col-sm-4 col-form-label">End Date</label>
                <div class="col-sm-8">
                    <input id="end_date" type="text" style="width:350px;" name="end_date" class="easyui-datebox form-control" data-options="formatter:myformatter,parser:myparser,required:'true'">
                </div>
            </div>   
            <div class="row">
                <label class="col-sm-4"></label>                     
                <div class="row">
                <label class="col-sm-2"></label>
                <div style="padding-left:120px" class="col-sm-10">
                    <i style="color:red;cursor:pointer" onclick="goToPDF();" class="fa fa-file-pdf-o fa-2x">PDF</i>                    
                </div>
            </div>            
            </div>
        </form>
    </div>
    <div class="card-block">
        <div id="showtimetable">
          
        </div>
    </div>
</div>


<h4 class="profile-overview-title">
    HR Attendance Report
</h4>

<div class="form-card-container">
    <form id="ff" action="/faculty_course_attendance_report_action" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="hr_id" value="<?=$hr_employee_id?>">

        <div class="row" style="margin: 0;">
            
            <?php if(auth()->user()->employee_id == 6455 or auth()->user()->employee_id == 1294){?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-form-label">Employees</label>
                    <div>
                        <input id="hr_employee_id" name="hr_employee_id" placeholder="Sessions" class="easyui-combobox form-control" data-options="
                            valueField:'employee_id', 
                            textField:'name',  
                            required:'true',                 
                            url: 'https://cms.bnu.edu.pk/hr/emp_details.json.php', 
                            method:'GET',
                            onSelect: function(rec){                  
                            } ">
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-form-label">Classification</label>
                    <div>
                        <input id="classification_id" name="classification_id" placeholder="Sessions" class="easyui-combobox form-control" data-options="
                            valueField:'classification_id', 
                            textField:'classification_name',  
                            required:'true',                 
                            url: 'https://cms.bnu.edu.pk/hr/classification.json.php', 
                            method:'GET',
                            onSelect: function(rec){                  
                            } ">
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-form-label">Start Date</label>
                    <div>
                        <input id="start_date" type="text" name="start_date" class="easyui-datebox form-control" data-options="formatter:myformatter,parser:myparser,required:'true'">
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-form-label">End Date</label>
                    <div>
                        <input id="end_date" type="text" name="end_date" class="easyui-datebox form-control" data-options="formatter:myformatter,parser:myparser,required:'true'">
                    </div>
                </div>
            </div>

            <div class="col-sm-12" style="margin-top: 15px;">
                <i onclick="goToPDF();" class="fa fa-file-pdf-o">PDF</i>
            </div>

        </div>
    </form>

    <div class="card-block">
        <div id="showtimetable"></div>
    </div>
</div>

@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
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
      //  return  (d < 10 ? ('0' + d) : d) + '/' + (m < 10 ? ('0' + m) : m) + '/' +  y;
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
    function goToPDF(){     
        if(<?=auth()->user()->employee_id?> == 6455 || <?=auth()->user()->employee_id?> == 1294){   
            if($('#hr_employee_id').combobox('getValue') == ''){
                var empl = 'undefined';
            }else{
                var empl = $('#hr_employee_id').combobox('getValue');
            }
            if($('#classification_id').combobox('getValue') == ''){
                var classification = 'undefined';
            }else{
                var classification = $('#classification_id').combobox('getValue');
            }        
            var link = "http://hr.bnu.edu.pk/employees/attendance/cms_report.php?start_date="+$('#start_date').datebox('getValue')+"&end_date="+$('#end_date').datebox('getValue')+"&employee_id="+empl+"&classification_id="+classification;
            window.open(link);
        }else{
           
            var link = "http://hr.bnu.edu.pk/employees/attendance/cms_report.php?start_date="+$('#start_date').datebox('getValue')+"&end_date="+$('#end_date').datebox('getValue')+"&employee_id="+$('#hr_id').val()+"&classification_id=2";
            window.open(link); 
        }
    }
    $('#sai_enrollment_no').autoComplete({
    resolver: 'custom',
    autoSelect: false,
    formatResult: function (item) {
						return {
							value: 'hello ap',
							text: item.text + "[" + item.value + "] ",
							// html: [ 
							// 		$('<img>').attr('src', item.icon).css("height", 18), ' ',
							// 		item.text 
							// 	] 
						};
					},
                    select : function (evt, item){
            console.log('Hello Ahmed',item);
        },
    events: {
        search: function (qry, callback) {
            // let's do a custom ajax call
            $.ajax(
                '<?=url('api/search-user-for-suggestion')?>',
                {
                    data: { 'qry': qry}
                }
            ).done(function (res) {
                console.log(res);
                callback(res)
            });
        }
      
    }
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    $('#sai_enrollment_no').val(item.value);
});




</script>
@stop
