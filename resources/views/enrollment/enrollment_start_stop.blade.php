@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')


<div class="row" style="margin:5px;">
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> Start End Enrollment  </h3>
            </div>
            <div class="card-body pb-2">
                <form id="messageForm" method="post">                    
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right"> 
                            <label class="col-form-label" >Enrollment Session</label>
                        </div>
                        <div class="col-lg-9">
                            <input id="facultyWload_sess_id" name="sess_id" style="width:400px;" placeholder="Sessions" class="easyui-combobox form-control" data-options="
                                valueField:'sess_id',
                                textField:'sess_name',
                                required:'true',
                                url: '<?= url('/sessions') ?>',
                                method:'GET',
                                onSelect: function(rec){
                                    var urlschool = '<?= url('/schools') ?>?sess_id='+rec.sess_id;
                                        $('#facultyWload_school_id').combobox('clear');
                                        $('#facultyWload_school_id').val('');
                                        $('#facultyWload_school_id').combobox('reload', urlschool);                   
                                } ">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right"> 
                            <label class="col-form-label" >School</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="facultyWload_school_id" name="school_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                                valueField: 'school_id',
                                textField: 'school_name',
                                required:'true',
                                method:'get',
                                onSelect: function(rec){
                                    var departmentURL = '<?= url('/departments') ?>?school_id='+rec.school_id+'&sess_id='+$('#facultyWload_sess_id').combobox('getValue');
                                    $('#department_id').combobox('clear');
                                    $('#department_id').val('');
                                    $('#department_id').combobox('reload', departmentURL);
                                }
                            ">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">RoadMap Session</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="roadmap_session" name="roadmap_session" style="width:400px;" placeholder="RoadMap Sessions" class="easyui-combobox form-control" data-options="
                                valueField:'sess_id',
                                textField:'sess_name',
                                
                                url: '<?= url('/sessions') ?>',
                                method:'GET',
                                onSelect: function(rec){
                                                    
                                } ">
                        </div>
                    </div>
                    <!----------------------------------------   DEPARTMENT   ---------------------------------------->
                    <div class="form-group row " style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right"> 
                            <label class="col-form-label">Department</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="department_id" name="department_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                            valueField: 'dept_id',
                            textField: 'dept_name',                            
                            method:'get',
                            url:'',
                            onSelect: function(rec){                        
                            var dept_ID = rec.dept_id
                            var progURL = '<?= url('/programs') ?>?co_status=E&dept_id='+dept_ID+'&sel_sess_id='+$('#facultyWload_sess_id').combobox('getValue')+'&sel_fc_emp_email='+rec.emp_email;
                            $('#program_id').combobox('clear');
                            $('#program_id').val('');
                            $('#program_id').combobox('reload', progURL); 
                            }">
                        </div>
                    </div>
                    <!----------------------------------------   DEPARTMENT END   ---------------------------------------->

                    <!------------------ PROGRAM START ------------------>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right"> 
                            <label class="col-form-label">Program</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="program_id" name="program_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                            textField: 'prog_name',
                            valueField: 'prog_id',                            
                            method:'get',
                            onSelect: function(rec){                 

                            }">
                        </div>
                    </div>
                    <!------------------ PROGRAM End ------------------>
                    {{-- Shift Start --}}
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right"> 
                            <label class="col-form-label">Offered Shift</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="shift_id" name="shift_id" style="width:400px;" placeholder="Sessions" class="easyui-combobox form-control" data-options="
                                valueField:'shift_id',
                                textField:'shift_name',
                                url: '<?=url('/shift')?>',
                                method:'GET',
                                onSelect: function(rec){
                                                  
                                } ">
                        </div>
                    </div>
                    {{-- SHift ENd --}}
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Registration No</label>
                        </div>
                        <div class="col-lg-8">
                            <input name="reg_no" id="reg_no" style="width:400px;" class="easyui-textbox">
                        </div>
                    </div>
                    <div class="form-group row " style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Pages Rights</label>
                        </div>
                        <div class="col-lg-8">  
                            <input id="page_right_id" name="page_right_id" style="width:400px;" 
                            class="easyui-combobox" data-options="
                            valueField:'gmp_page_id',
                            textField :'gmp_page_name',
                            required:'true',
                            method:'get',
                            url: '<?= url('/page_name') ?>',
                            onSelect: function(rec){                                               
                                            
                                    
                            }
                            ">
                        </div>
                    </div>                    
                    <div class="form-group row" style="margin-top:9px;text-align: right;margin-left:-16px">
                        <div class="col-lg-3" style="text-align: right">
                            <label>Start Date/Time</label>
                        </div>
                        <div class="col-lg-3" style="max-width: 23%">
                            <input type="text" style="width: 137px!important" class="form-control  assess-form-input optional-inputs cms-datePicker" id="start_date" name="start_date" placeholder="Start Date" maxlength="10">    
                        </div>
                            
                        <div class="col-lg-3" style="max-width: 23%">
                             <input type="time" style="width: 137px!important" class="form-control  assess-form-input optional-inputs" id="start_time" name="start_time"  placeholder="Due Time">
                        </div>
                    </div>                    
                    <div class="form-group row" style="margin-top:9px;text-align: right;margin-left:-16px">
                        <div class="col-lg-3" style="text-align: right">
                            <label>End Date/Time</label>
                        </div>
                        <div class="col-lg-3" style="max-width: 23%">
                            <input type="text" style="width: 137px!important" class="form-control  assess-form-input optional-inputs cms-datePicker" id="end_date" name="end_date" placeholder="End Date" maxlength="10">    
                        </div>
                            
                        <div class="col-lg-3" style="max-width: 23%">
                            <input type="time" style="width: 137px!important" class="form-control  assess-form-input optional-inputs" id="end_time" name="end_time"  placeholder="Due Time">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2"></label>                        
                        <div style="padding-left:120px" class="col-sm-10"></br>
                            <button type="button" class="btn btn-light send-msg-btn" onclick="submitForm()">Add record</button>
                            <button type="button" class="btn btn-light send-msg-btn" onclick="resetForm()">Reset</button>
                        </div>
                        <div class="message_div info-alert" style="color: green;"></div>
                        <div class="message_div_error" style="color: red;"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-header" style="display:inline">
                <div style="float: left">
                    <h3 class="card-title">Enrollment Open Status </h3>
                </div>              
            </div>
            <div class="card-body pb-2">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap table-primary mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-white">School</th>
                                <th class="text-white">Total</th>                        
                            </tr>                         
                        </thead>
                        <tbody id="enrollment-status-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" style="display:inline">
            <div style="float: left">
                <h3 class="card-title">Enrollment List </h3>
            </div>
            <div style="float: right">
                <span>
                    <button onclick="getEnrollmentStatus();" class="btn">Refersh Enrollment List</button>
                </span>
            </div>
        </div>       
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap table-primary mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-white">Action</th>
                        <th class="text-white">Page</th>
                        <th class="text-white">Student</th>
                        <th class="text-white">Enrollment Session</th>
                        <th class="text-white">RoadMap Session</th>
                        <th class="text-white">School</th>
                        <th class="text-white">Department</th>
                        <th class="text-white">Program</th>
                        <th class="text-white">Shift</th>
                        <th class="text-white">Start Time</th>
                        <th class="text-white">End Time</th>
                    </tr>
                </thead>
                <tbody id="noti-msg-tbody">
                </tbody>
            </table>
        </div>
        <div class="card-footer">

        </div>
    </div>    
</div>
@stop
@section('javascript')

<script type="text/javascript">

    function submitForm() {        
        var bi_form = document.querySelector('#messageForm');
        var data = new FormData(bi_form);
        var sess_name = $('#facultyWload_sess_id').combobox('getText')
        var page_name = $('#page_right_id').combobox('getText')
        data.append('sess_name', sess_name);
        data.append('page_name', page_name);

          
        sendPostRequest('.send-msg-btn', '.ghgfhfghdfh', 'save-enrollment-start-stop', function(res) {
            //console.log('Hello Baby', res);
            //alert(res.status);
            if (res.status) {
                getEnrollmentStatus();
                getEnrollmentOpenCloseStatus();
                $('.message_div').html(res.message);
                $('.message_div_error').html('');
                setTimeout(function(){
                    $('.message_div').html('');
                    $('.message_div_error').html('');
                }, 3000);
                
            }else{
                $('.message_div').html('');
                $('.message_div_error').html(res.message);
                setTimeout(function(){
                    $('.message_div').html('');
                    $('.message_div_error').html('');
                }, 3000);
            }
        }, data);
    } 

    function resetForm(){
        $('#messageForm').form('reset'); 
    }
    // get-create-by-user

    function getEnrollmentStatus() {
        getEnrollmentOpenCloseStatus();
        sendGetRequest('#noti-msg-tbody', '.message_div', 'get-group-pages-management', function(res) {
           
            var tbody = '';                  
            $(res).each(function(index,row){    
                var dept_name_show  = $.trim(row.dept_name);            
                var prog_name_show  = $.trim(row.programe_name);
                var roadmap_session = $.trim(row.rd_sess_name);
                var student_rollNo  = $.trim(row.gpm_sai_enrollment_no);
                var shift_name_show = $.trim(row.gpm_shift_name);
                        
                tbody += '<tr><td onclick="delBtn('+row.id+')" style="cursor:pointer">Delete</td><td>'+row.page_name+'</td><td>'+student_rollNo+'</td><td>'+row.sess_name+'</td><td>'+roadmap_session+'</td><td>'+row.school_name+'</td><td>'+dept_name_show+'</td><td>'+prog_name_show+'</td><td>'+shift_name_show+'</td><td>'+row.start_date_time+'</td><td>'+row.end_date_time+'</td></tr>';
            });
            if(tbody == ''){
                tbody = '<tr><td colspan="4"> No Enrollment Open Yet! </td></tr>';
            }
            $('#noti-msg-tbody').html(tbody);
            // /getEnrollmentStatus()
        });
    }

    function getEnrollmentOpenCloseStatus(){

        sendGetRequest('#enrollment-status-body', '.message_div', 'get-enrollment-status', function(res) {
           
           var tbody = '';                  
           $(res).each(function(index,row){  
               console.log('bilal',row);  
                         
               tbody += '<tr><td>'+row.school_code+'</td><td>'+row.total+'</td></tr>';
           });
           if(tbody == ''){
               tbody = '<tr><td colspan="4"> No Enrollment Open Yet! </td></tr>';
           }
           $('#enrollment-status-body').html(tbody);
           // /getEnrollmentStatus()
       });



    }

    function delBtn(id){
       // alert(id);
       // return false;
        sendPostRequest('.send-msg-btn', '.ghgfhfghdfh', 'delete-enrollment/'+id, function(res) {
            //console.log('Hello Baby', res);
            //alert(res.status);
            if (res.status) {
                getEnrollmentStatus();
                getEnrollmentOpenCloseStatus();
                $('.message_div').html(res.message);
                $('.message_div_error').html('');
                setTimeout(function(){
                    $('.message_div').html('');
                    $('.message_div_error').html('');
                }, 3000);
            }
        }, '');
    }

    $(document).ready(function(){    
      getEnrollmentStatus();
      getEnrollmentOpenCloseStatus();
    });

</script>
@stop