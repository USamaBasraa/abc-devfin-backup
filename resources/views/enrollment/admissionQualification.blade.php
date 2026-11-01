@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')


<div class="row" style="margin:5px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> Student Documents Verification   </h3>
            </div>
            <input type="hidden" id="session">
            <input type="hidden" id="school">
            <input type="hidden" id="department">
            <input type="hidden" id="program">
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
                                    $('#session').val(rec.sess_id);             
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
                                    $('#school').val(rec.school_id); 
                                }
                            ">
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
                            $('#department').val(rec.dept_id); 
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
                                $('#program').val(rec.prog_id);             
                                getEnrollmentStatus();
                            }">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
    <div class="card">
        <div class="card-header" style="display:inline">
            <div style="float: left">
                <h3 class="card-title">Student Documents Verification List</h3>
            </div>
            <div style="float: right">
                <!-- <span>
                    <button onclick="getEnrollmentStatus();" class="btn">Refersh Enrollment List</button>
                </span> -->
            </div>
        </div>       
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap table-primary mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-white">Applicant ID</th>
                        <th class="text-white">Email</th>
                        <th class="text-white">Name</th>
                        <th class="text-white">Program</th>
                        <th class="text-white">Student NIC</th>
                        <th class="text-white">Guardina NIC</th>
                        <th class="text-white">Verify Docs</th>                      
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
        // getEnrollmentOpenCloseStatus();
        
        var sess = $('#session').val();   
        var school = $('#school').val();   
        var dept = $('#department').val();   
        var prog = $('#program').val();   

        //alert(sess+'---'+school+'---'+dept+'---'+prog);
        
        sendGetRequest('#noti-msg-tbody', '.message_div', 'get-applicant-information/'+sess+'/'+school+'/'+dept+'/'+prog, function(res) {
           
            var tbody = '';                  
            $(res).each(function(index,row){  
                console.log(row)  
                var aai_id  = $.trim(row.aai_id);  
                var api_id  = $.trim(row.api_id);             
                var api_email  = $.trim(row.uemail);
                var api_name = $.trim(row.api_name);
                var api_program  = $.trim(row.aai_programid);
                var api_nic = $.trim(row.api_nic);
                var progId = $.trim(row.aai_sdpo_prog_id);
                var sessId = $.trim(row.aai_sdpo_sess_id);
                var api_nic = $.trim(row.api_nic);
                var api_guardian_nic = $.trim(row.api_guardian_cnic);                        
                tbody += '<tr>';
                tbody += '<td>' + api_id + '</td>';
                tbody += '<td>' + api_email + '</td>';
                tbody += '<td>' + api_name + '</td>';
                tbody += '<td>' + api_program + '</td>';
                tbody += '<td>' + api_nic + '</td>';
                tbody += '<td>' + api_guardian_nic + '</td>';
                tbody += '<td><button type="button" onclick="verifyDocs(' + api_id + ',' + progId + ',' + sessId + ')">Verify Docs</button></td>';
                tbody += '</tr>';
            });
            if(tbody == ''){
                tbody = '<tr><td colspan="4"> No Applicant Found Yet! </td></tr>';
            }
            $('#noti-msg-tbody').html(tbody);
            // /getEnrollmentStatus()
        });
    }

    // function verifyDocs(apiId,progId,sessId){

    //     alert(apiId+'----'+progId+'----'+sessId);
    //     var newLink = baseUrl + "/doc-verifications?api_id=" + api_id + "&prog_id=" + prog_id + "&sess_id=" + sess_id;

    //     // Open the new link in a new tab or window
    //     window.open(newLink, "_blank");

    // }
    function verifyDocs(apiId, progId, sessId) {
        var baseUrl = "https://cms.bnu.edu.pk"; // Replace with your base URL
        var newLink = baseUrl + "/doc-verifications?api_id=" + apiId + "&prog_id=" + progId + "&sess_id=" + sessId;

        // Open the new link in a new tab or window
        window.open(newLink, "_blank");
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
                // getEnrollmentStatus();
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
    //  getEnrollmentStatus();
      //getEnrollmentOpenCloseStatus();
    });

</script>
@stop