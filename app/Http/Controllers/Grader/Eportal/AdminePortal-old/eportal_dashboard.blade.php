
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<style>
.invisible { display: none; }
</style>



<style type="text/css">

    .chat_div {

        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 20px;
        border: 1px solid green;
        padding: 10px;
        background-color: #DCF8C6;
        /* text-align: left; */
        margin-bottom: 10px;
        margin-right:5px;
        margin-left:5px;
        color: #000;
        font-size: 18px;
        padding-left: 15px;
        /* word-break: break-all; */


    }

    .chat_div_other {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 20px;
        border: 1px solid green;
        padding: 10px;
        background-color: #FFF;
        margin-right:5px;
        margin-left:5px;
        /* text-align: left; */
        margin-bottom: 10px;
        color: #000;
        font-size: 18px;
        padding-left: 15px;
        /* word-break: break-all; */
    }

    .msg_card_body {
        height: calc(100vh - 200px);
        overflow-y: auto;
    }

    /* New styles for mobile responsiveness */
    @media only screen and (max-width: 767px) {
        /* .float-left.hidden-xs {
            display: none !important;
        } */

        .action-header {
            padding: 10px;
        }

        .widget-chat-box {
            margin-bottom: 15px;
        }
        .tm-title-mobile {
        display: block;
    }
    }
    .user_outer_div{
        border:2px solid #3D4BB7;
        float:left;
        border-radius: 10px;
        margin-right:5px;
        padding:5px;
        margin: 1px;
        position: relative;
        background-color:#ccffee !important;
    }
    .user_inner_div{
            float:left;
    }
    .user_outer_div_strong {
            position: absolute;
            top: 1px; /* Adjust the top distance as needed */
            right: 5px; /* Adjust the right distance as needed */
            color: red;
            cursor: pointer;

    }
</style>
<div class="card e-panel" style="margin:10px;width:98%;">
    <div class="card-header">
        <h3 align ="center"> E-Portal Requests</h3>   <br>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
            <div class="tabs-menu" style="padding:10px;">
                <!-- Tabs -->
                <ul class="nav panel-tabs">
                    <li class=""><a href="#lectures" onclick="status('Pending')"  id="setclasspending" data-toggle="tab">
                        <i class="fa fa-laptop"></i> Pending
                    </a></li>
                    <li><a href="#material" onclick="status('Completed')" id="setclasscompleted"  data-toggle="tab">
                        <i class="fa fa-book"></i> Completed
                    </a></li>
                </ul>
                <input type="hidden" id="taskStatus" value="Pending">
                <input type="hidden" id="studentStatus" value="noStudent">
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
            <select class="form-control" id="category_id" onchange="getCateg()" name="category_id">
                <option value="0">All Requests</option>
                @foreach($resEpCateg as $user)
                    <option value="{{$user->id}}">{{$user->ec_title}} <span style="color:red">({{$user->total_req}})</span></option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row" style ="margin-left: 62px;">
    <div class="col-12 col-sm-10 col-md-10 col-lg-10 form-group">
        <label class="col-form-label" ><b>Search By username No or Name</b></label>
        <input class=" form-control" type="text" name="sch_enrollment_no"   id="sai_enrollment_no"  autocomplete="off" name="username" placeholder="Search By Username" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>" >
    </div>
    <div class="col-12 col-sm-2 col-md-2 col-lg-2 form-group" style="padding-top:35px">
        <button type="submit"  class="btn btn-success" ><i class="fa fa-search" aria-hidden="true"></i></button>
        <button type="button" class="btn btn-primary" onclick="getCateg('noStudent')"><i class="fa fa-refresh" aria-hidden="true"></i></button>
    </div>
</div>
<div id="result-body-categ">
</div>

<!-- The Modal -->
<div class="modal" id="approval_status">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="header_modal"></span>
                <button type="button" style="color:white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">

            </div>
            <div  class="col-12">
            <div class="form-group">

                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Name:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="stdname"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>School:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="stdschool"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Program:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="stdprog"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Mobile:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="stdmob"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Description:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="stddesc"></span><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>File Student:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="uploadedFileStudent"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>File Department:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="uploadedFile"></span><br>
                    </div>
                </div>
                <div class="row" style="display:none" id="showRemarks">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Remarks:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="resolverRemarks"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b><?php echo $EmpType == '4' || $EmpType == '5' ? 'Forwarded To:' : 'Forwarded From:'; ?></b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="forwardedto"></span><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Forwarded Description:</b></label>
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="fwrddesc"></span><br>
                    </div>
                </div>


                @if ($EmpType == '4' || $EmpType == '5')

                <div class="row" style="display:none" id="showuser"  >
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <button onclick="forwardToUser()" type="button" class="btn btn-primary">Forward To user</button>
                        <!-- <label for="sortDropdown"><b>Forward To:</b></label>                          -->
                    </div>
                    <div class="col-12 col-sm-7 col-md-7 col-lg-7 userDropDown"  style="display:none">
                        <select name="staff_for_filter" id="staff_for_filter" class="form-control select2">
                            <option value="0|0|0" style="background-color: yellow;font-weight: bold;">Select School / Department / Staff:</option>
                            @foreach ($staffMembers as $staff)
                                <option value="{{$staff->parent_id . '|' . $staff->id. '|' . $staff->ordername }}">
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-2 col-md-2 col-lg-2 userDropDown"  style="display:none">
                        <i class="fa fa-history" onclick="reset()" style="  padding-top: 8px;font-size:16px;cursor:pointer;color:blue;"> Clear </i>
                    </div>
                </div>

                @endif


                <div style="margin-top: 10px;" id="dropDown"></div>


            </div>
            </div>
            <span id="saved_msg_des"></span>
            <!-- <div class="modal-footer">
                <div class="btn-group">
                    <input type="hidden" id="ereqID">

                </div>
            </div> -->
        </div>
    </div>
</div>
<div class="modal" id="application_tracking">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="application_status"></span>
                <button type="button" class="close" style="color:white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">
                <div class="card-body">
                    <table class="table table-sm  table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">Sr.No</th>
                            <th scope="col">Department</th>
                            <th style="text-align:center;" scope="col">Status</th>
                            <th style="text-align:center;" scope="col">Remarks</th>
                        </tr>
                        </thead>
                        <tbody id="result-body" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
                    </table>
                </div>
                <div class="card">
                    <div class="card-header" style="border-top: 4px solid #8CDDCD">
                        <h4 class="card-title">Messages</h4>
                    </div>
                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="card widget-chat-box" style="border-top: 0px solid">
                                <div class="card-block">
                                    <form enctype="multipart/form-data" method="post" name="sendMessage_form" id="sendMessage_form" class="fixed-form">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input type="text" id="messageTextarea" oninput="checkLimit()" name='messageTextarea' class="form-control"
                                                placeholder="Please Enter Message..."
                                                style="border: 1px solid #424e79 !important; height: 60px;" />
                                            <input type="hidden" id="sendToUsername" name="sendToUsername">
                                            <input type="hidden" id="RequestID" name="RequestID" >
                                            <input type="hidden" id="login_username" value="<?=auth()->user()->username;?>"name="login_username">
                                            <button type="button" id="messageBtn" onclick="sendMsgBtn()" class="btn btn-primary">
                                                <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24"
                                                    width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z" opacity=".3" />
                                                    <path
                                                        d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-left">

                                            <span id="countLimit" class="badge badge-pill badge-primary text-right"></span>
                                        </div>
                                    </form>
                                    <hr style="border-top: 2px solid tomato">
                                </div>
                            <div class="text-left" id="msg_container_send"></div>
                        </div>
                    </div>
                    <div id="msg_cotainer_send" class="text-left"></div>
                    <div id="msg_container_send"></div>
                    <div class="container mt-5">
                        <div class="row justify-content-center">
                        <div class="col-md-11">
                            <div class="message-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">
    function reset(){
        // alert('ok')
        // $('#staff_for_filter').val(null);
        $('#staff_for_filter').val('0|0|0').trigger('change');
        $('#showForwardBtn').hide();
        $('#hideForwardRemarks').hide();
        $('#showHideBtn').show();
        $('.recordsDiv').html('');


       // $('#tab').hide();


    }
$(document).ready(function () {
    // alert($('#category_id').val());
   // $("#category_id").val(selectedValue);
    $('.select2').select2();
    getCateg($('#taskStatus').val());
});
$('#staff_for_filter').on('change', function() {
    // alert('aaaa');
    $('#hideForwardRemarks').show();
    $('#showForwardBtn').show();
    $('#showHideBtn').hide();

    // alert('ok')
     fetchRecordsFromController(this.value);
});
var appendedIDs = {}; // Object to store the already appended employee IDs
function fetchRecordsFromController(staffId) {

   var explodedArray = staffId.split('|');
   var er_id = $('#er_id').val();

//    alert(explodedArray[2]+"__"+staffId);
//    return false;

   if(explodedArray[2]=="G"){
      // appendedIDs = {};
       $('.recordsDiv').html('');
   }
   $.ajax({
        type: 'get',
        url: '/admin/fetch_staff_data?staffId='+staffId,
        processData: false,
        contentType: false,
        success: function(response) {

               console.log(response.data);
               // alert(response.length);
               // Upon success, handle the data and append it to the div
               if (response && response.length > 0) {
                //alert(response+'--'+response.length);
                   // Assuming the response is an array of records
                  var recordsDiv = $('.recordsDiv');
                  // recordsDiv.append('<tr>');
                    for (var i = 0; i < response.length; i++) {
                       var record = response[i];
                       // Check if the employee_id is already appended
                       //alert(appendedIDs.hasOwnProperty(record.employee_id))
                        //if (!appendedIDs.hasOwnProperty(record.employee_id)) {
                           // If not, append the record and mark the employee_id as appended in the object
                           if(i%5 == 0) {
                              // recordsDiv.append('</tr><tr>');
                           }

                            //  alert(explodedArray[2]+"__"+staffId);
                            //    var imageSrc = 'http://cmstest.bnu.edu.pk/assets/staffimages/' + record.employee_id + '.jpg';
                            //    var fallbackImage = 'http://cmstest.bnu.edu.pk/assets/staffimages/no.jpg';
                            // $.ajax({
                            //         type: 'get',
                            //         url: '/admin/chk_employee?emp_id='+record.employee_id+'&er_id='er_id,
                            //         processData: false,
                            //         contentType: false,
                            //         success: function(response) {

                            //         }
                            //     });
                           var append_div = '<div id="tab' + record.employee_id + '" class="user_outer_div">' +
                                            '<div class="user_inner_div">' +
                                            // '<img src="' + imageSrc + '" onerror="this.src=\'' + fallbackImage + '\'" alt="" class="avatar avatar-md brround">' +
                                            '</div>' +
                                            '<div class="user_inner_div">' +
                                            '<strong> &nbsp;' + record.emp_f_name + ' ' + record.emp_l_name + '</strong><br>' +
                                            '<input type="hidden" name="users[]" value="' + record.employee_id + '">' +
                                            record.dept_name +
                                            '</div>' +
                                            '<div class="user_inner_div">&nbsp;&nbsp;&nbsp;&nbsp;</div>';
                                            // Check if explodedArray[2] is not equal to "edit"
                                            if (explodedArray[2] !== "edit") {
                                                append_div += '<div class="user_inner_div inner_div_when_edit" onclick="remove_table_td(' + record.employee_id + ')">' +
                                                            '<div class="user_outer_div_strong">X</div>' +
                                                            '</div>';
                                            }
                            append_div += '</div>';
                            if (explodedArray[2] !== "edit") {
                                recordsDiv.append(append_div);
                            }else{
                                // alert('bilal');
                                $('#show_edit_div').html('');
                                $('#show_edit_div').append(append_div);
                                //$('#clear_button').hide();
                            }
                            //alert('123');
                            // recordsDiv.append(append_div);
                            $('#staff_for_filter').val('0|0|0').trigger('change');
                            //    resetDropdown();
                            //var user_image = '<img src="htt://cmstesttest.bnu.edu.pk/assets/staffimages/8996.jpg" width="30px" alt="NA">';
                            // recordsDiv.append('<p>'+user_image+'<input type="text" name="user_id[]" value="' + record.employee_id + '">' + record.emp_f_name + '  ' + record.emp_l_name + '</p>');
                            appendedIDs[record.employee_id] = true;
                            //    focusSelect2SearchBox();
                        //}

                   }

                  // recordsDiv.append('</tr>');

               } else {
                   // Handle case when no records are returned
                  // $('.recordsDiv').html('<p>No records found.</p>');
               }
           },
           error: function(error) {
               // Handle errors (optional)
               console.error('Error fetching records:', error);
               //$('.recordsDiv').html('<p>Error fetching records.</p>');
           }
       });
}
function remove_table_td(employeeId) {
    if (appendedIDs.hasOwnProperty(employeeId)) {
    delete appendedIDs[employeeId];
    }
    $('#tab'+employeeId).remove();
};


function displayValidationErrors(xhr){
       if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $("#" + key + "-error").text(value[0]);
                        $("#" + key + "-error").addClass('text-danger');
                        setTimeout(function(){
                            $("#" + key + "-error").text('');
                     },5000);
                    });
                }
              }

function reqBtn(action){

    // var id = $('#er_id').val();
    // var remarks = $('#req_remarks').val();
    // var data = {
    //     id: id,
    //     remarks: remarks,
    //     action:action,
    //     _token: '{{ csrf_token() }}'
    // };
    var reqRemarksValue = $('#req_remarks').val();
    var user = $('#staff_for_filter').val();

    // if (reqRemarksValue === null || reqRemarksValue === "") {
    //     // Input is empty or null, so perform validation here
    //     // You can return false or show an error message or take any other action
    //     $('#remarksReq').html('Remarks Required ');
    //     $('#remarksReq').css('color','red');
    //     return false;
    // }else{
    //     $('#remarksReq').html('');
    // }

    $('#remarksReq').html(' ');
    var button = $('#btnloader');
    button.prop('disabled', true);
    var spinnerIcon = '<i class="fa fa-spinner fa-spin"></i>';
    button.html(spinnerIcon + ' Updating');
    // button.html('<i class="fa fa-check"></i> Updated');
    var bi_form = document.querySelector('#approvalForm');
    var formData = new FormData(bi_form);

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('action', action);
    formData.append('user', user);
    $.ajax({
        url: "{{ route('admin/eportal_request_complete') }}",
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false, // Ensure FormData is not processed
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            var empArray=response.existArr;

        if(empArray.length>=1 && response.allow==0){
            console.log(response.existArr);
            var empName='';
            $.each(response.existArr, function(key, value) {
                console.log(value);
                 empName+=value+' ';
                $('#remarksReq').css('color','red');
                $('#remarksReq').html(' Resolving User Already added '+empName);
                //return false;
            });
                // return false;
        }
        //$('#remarksReq').html('');
        //alert(response.allow);
        if(response.allow == 1){
            if(response.resstatus == 'Pending'){
                $('#remarksReq').css('color','red');
                $('#remarksReq').html('Request Roll Back Successfully');
                setTimeout(function () {
                $('#approval_status').modal('hide');
                    button.html('<i class="fa fa-check"></i> Updated');
                }, 2000);
                // $('#approval_status').modal('hide');
                getCateg('Completed');
            }else{
                $('#remarksReq').css('color','green');
                $('#remarksReq').html(response.message);
                setTimeout(function () {
                $('#approval_status').modal('hide');
                    button.html('<i class="fa fa-check"></i> Updated');
                }, 2000);
                // $('#approval_status').modal('hide');
                getCateg('Pending');

            }

        }

        },
        error: function(xhr, status, error) {
            button.html('<i class="fa fa-plus"></i> Action');
            button.prop('disabled', false); // Re-enable the button
            displayValidationErrors(xhr);
            // console.error(xhr.responseText);
        }
    });
}
function status(val){
    $('#taskStatus').val(val);
    if(val == 'Pending'){
        // $('#result-body-categ').show();
        getCateg('Pending');
        $('#category_id').val(0);
    }else if (val == 'Completed'){
        // $('#result-body-categ').hide();
        getCateg('Completed');
        $('#category_id').val(0)
    }
}
function getCateg(sel){
    //alert(sel);
    var taskstatus = $('#taskStatus').val();
    if(taskstatus == 'Pending'){
        $("#setclasscompleted").removeClass("active");
        $("#setclasspending").addClass("active");

    }else{
        $("#setclasscompleted").addClass("active");
        $("#setclasspending").removeClass("active");
    }
    var ec_id = $('#category_id option:selected').val();//sel.value;
    var selectedText = $('#category_id option:selected').text();
    var roll = $('#sai_enrollment_no').val();
    //alert(id);
    //var roll = $('#txtStudentid').val();
    if(roll){
        if(sel == 'noStudent'){
            var rollno = $('#studentStatus').val();
        }else{
            var rollno = $('#sai_enrollment_no').val();
        }

    }else{
        var rollno = $('#studentStatus').val();
    }
    // alert(selectedText);
    $.ajax({
        url: "{{route('admin/get_categ')}}?ec_id="+ec_id+"&sel="+sel+"&taskStatus="+taskstatus+'&student='+rollno,
        method: 'get',
        success: function(data) {
            var sr_no = 1;
            var html = '';

            html += '<div class="card e-panel" style="margin:10px;width:98%;">';
            html += '<div class="card-header">';
            html += '<div class="col-12 col-sm-7 col-md-7 col-lg-7">';
            html += '<h3 align ="left">'+selectedText+' ('+taskstatus+')</h3><br>';
            html += '</div>';
            html += '<div style="display:none" class="col-12 col-sm-5 col-md-5 col-lg-5">';
            html += '<div class="row">';
            html += '<div class="col-10 col-sm-10 col-md-10 col-lg-10">';
            // onkeyup="func(event, \'' + taskstatus + '\')"
            html += '<input placeholder="Search by Name or Roll #" class="form-control" type="text" id="txtStudentid" name="txtStudentid" >';
            html += '</div>';
            html += '<div class="col-1 col-sm-1 col-md-1 col-lg-1">';
            html += '<button type="button" id="getCategButton" class="btn btn-success" onclick="getCateg(\'' + taskstatus + '\')"><i class="fa fa-search" aria-hidden="true"></i></button>';
            html += '</div>';
            html += '<div class="col-1 col-sm-1 col-md-1 col-lg-1">';
            html += '<button type="button" class="btn btn-primary" onclick="getCateg(\'noStudent\')"><i class="fa fa-refresh" aria-hidden="true"></i></button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="row">';
            html += '<div class="col-12 col-sm-12 col-md-12 col-lg-12">';
            html += '<table class="table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th scope="col">#</th>';
            html += '<th scope="col">First Name</th>';
            html += '<th scope="col">School</th>';
            html += '<th scope="col">Program</th>';
            html += '<th scope="col">Request Time</th>';
            html += '<th scope="col">Status</th>';
            html += '<th scope="col">Approval</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            $.each(data,function(key,value) {


                var valueJson = JSON.stringify(value);
                var badgeColor;
                if (value.er_request_status === 'Declined') {
                    badgeColor = 'red';
                } else if (value.er_request_status === 'Pending') {
                    badgeColor = '#007bff';
                } else if (value.er_request_status === 'Forward') {
                    badgeColor = '#ecb403';
                }else {
                    badgeColor = 'green';
                }
                valueJson = valueJson.replace(/"/g, '&quot;');
                console.log(value)
                html += '<tr>';
                html += '<td scope="col">'+ (sr_no++) +'</td>';
                html += '<td scope="col">'+value.full_name+' ('+value.username+')</td>';
                html += '<td scope="col">'+value.school+'</td>';
                html += '<td scope="col">' + (value.programs ? value.programs : 'N/A') + '</td>';
                html += '<td scope="col">'+value.erdatetime+'</td>';
                html += '<td scope="col"><span style="display: inline-block; padding: 0.25em 0.5em; border-radius: 0.25rem; background-color: ' + badgeColor + '; color: #fff; font-weight: bold;">'+value.er_request_status+'</span></td>';
                html += '<td><button type="button" class="btn btn-primary" data-toggle="modal" onclick="setProfile(\''+ valueJson +'\')"  data-target="#approval_status">Details</button></td>';
                html += '</tr>';
            });
            html += '<tbody>';
            html += '<table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            if(html != ''){
                $('#result-body-categ').html(html);
            }else{
                $('#result-body-categ').html('No Result Found!');
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}
function setProfile(valueJson){
    $('#staff_for_filter').val('0|0|0').trigger('change');
    //$('#staff_for_filter').val('0|0|0').trigger('change');
    var appendedIDs = {};

    $('.recordsDiv').html('');
    $('.userDropDown').hide();
    var valueObj = JSON.parse(valueJson);
    console.log(valueObj.full_name);
    if($('#taskStatus').val() == 'Pending'){
        $('#showuser').show();
    }else{
        $('#showRemarks').show();
        if(valueObj.er_remarks){
            $('#resolverRemarks').html(valueObj.er_remarks);
        }else{
            $('#resolverRemarks').html('N / A');
        }
        $('#showuser').hide();
    }

    $('#header_modal').html(valueObj.username);
    $('#stdname').html(valueObj.full_name+ '  ('+valueObj.username+')');
    $('#stdschool').html(valueObj.school);
    $('#stdprog').html(valueObj.programs);
    $('#stdmob').html(valueObj.mobile);
    $('#stddesc').html(valueObj.description);
    $('#er_id').val(valueObj.er_id);
    $('#fwrddesc').html(valueObj.er_remarks);
    if(valueObj.ec_id == 18 || valueObj.ec_id == 25){
        if(valueObj.er_student_upload_challan){
            $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_student_upload_challan + '">Uploaded File by Student</a>');
        }else{
            $('#uploadedFileStudent').html('N/A');
        }
    }else{

        if(valueObj.er_request_file){
            let str = valueObj.er_request_file;
            if(str.search("drive.google.com") > 0){

                 $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
            }
            else{
                $('#uploadedFileStudent').html('<a target="_blank" href="http://studenttest.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
            }
        }else{
            $('#uploadedFileStudent').html('N/A');
        }
    }
    if(valueObj.google_drive_url){
        $('#uploadedFile').html('<a target="_blank" href="'+valueObj.google_drive_url+'">Uploaded File by Department</a>');
    }else{
        $('#uploadedFile').html('N/A');
    }
    var er_id = valueObj.er_id;
    $.ajax({
        type: 'get',
        url: 'get_forward_user?er_id='+er_id,
        processData: false,
        contentType: false,
        success: function(res) {
            console.log(res);
            var concatenatedNames = '';
            var hasFullName = false;
            $.each(res.data, function(key, value) {
                console.log(value);
                if (value.full_name) {
                    concatenatedNames += value.full_name + ', ';
                    hasFullName = true;
                }
            });
            if (!hasFullName) {
                concatenatedNames = 'Not Forwarded';
            } else {
                // Remove the trailing comma and space from the concatenated string
                concatenatedNames = concatenatedNames.slice(0, -2);
            }
            $('#forwardedto').html(concatenatedNames);
        },
        error: function(xhr) {

        }
    });

    var taskstatus = $('#taskStatus').val();
    var html = '';
    html += '<form id="approvalForm">';
    html += '<input type="hidden" id="er_id" name="er_id" value="'+valueObj.er_id+'">';
    html += '<input type="hidden"  name="stdroll" value="'+valueObj.username+'">';
    html += '<input type="hidden"  name="ec_title" value="'+valueObj.ec_title+'">';
    html += ' <div class="col-sm-11" id="show_edit_div"></div>';
    html += '<div class="row">';
    html += '<div class="col-sm-12 recordsDiv"></div></div>';
    if(taskstatus == 'Pending'){
    html += '<div id="hideForwardRemarks" style="display:none">';
    html += '<label for="remarks"><b>Remarks to Forwarding User:</b></label>';
    html += '<input type="text" class="form-control" name="remarksToForward" id="req_remarks_forward">';
    html += '</div>';
    html += '<label for="remarks"><b>Remarks to Requested User:</b></label>';
    html += '<input type="text" class="form-control" name="remarks" id="req_remarks">';
    html += '<span id="remarks-error" class="error text-danger"></span>';



    html += '<div class="form-group">';
    html += '<label for="file">Upload File:</label>';
    html += '<input type="file" class="form-control-file" id="file" name="file" accept=".pdf, .jpg, .jpeg, .png">';
    html += '<small class="form-text text-muted">Only <span class="text-danger">PDF, JPG, PNG and JPEG</span> files are allowed.</small>';
    html += '<span id="file-error" class="error text-danger"></span>';
    html += '</div>';
    }
    html += '<div style="float:right;margin-bottom: 10px">';
    html += '<span id="remarksReq"></span>';
    if(taskstatus == 'Completed'){
    html += '<button onclick="reqBtn(\'Pending\')" type="button" class="btn btn-danger">Rollback</button> ';
    }else{
    html += '<button id="showHideBtn" onclick="reqBtn(\'Completed\')" type="button" class="btn btn-success">Complete</button> ';
    html += '<button id="showForwardBtn" style="display:none" onclick="reqBtn(\'Forward\')" type="button" class="btn btn-warning">Forward & Complete</button> ';
    }
    // html += '<button id="btnloader" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ';
    // html += 'Action';
    // html += '</button>';
    // html += '<div class="dropdown-menu"> ';
    // if(taskstatus == 'Completed'){
    //     html += '<a class="dropdown-item" onclick="reqBtn(\'Pending\')">RollBack</a>';
    // }else{
    //     html += '<a class="dropdown-item" onclick="reqBtn(\'Completed\')">Mark as Complete</a>';
    //     html += '<a class="dropdown-item" onclick="reqBtn(\'Declined\')">Declined</a>';
    // }
    html += '</div>';
    html += '</div>';
    html += '</form>';
    $('#dropDown').html(html);



}
function forwardToUser(){
    // alert('bilal');
    $('.userDropDown').show()
}
function openModalStatus(id){
    // alert(id);
    $('#msg_cotainer_send').html('');
    $('#RequestID').val(id);
    // $('#application_status').html('asdasd');
    $("#application_status").css("font-weight", "bold");
    //   Use jQuery to show the modal
    $("#applicationStatus").modal("show");
    $.ajax({
            type: 'get',
            url: '/eportal-approval-authority?id='+id,
            processData: false,
            contentType: false,
            data: {
                'id':id
            },
            success: function(res) {
                html='';
                $.each(res.data,function( key, value ) {
                      $('#application_status').html(res.data[0].title);
                    var srno = parseInt(key+1);
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td >'+value.department+'('+value.full_name+')'+'</td>';
                    html += '<td><span class="badge ' + (value.erd_status === 'Pending' ? 'badge-info' : 'badge-primary') + '">' + value.erd_status + '</span></td>';
                    html += '<td>'+( value.er_ecd_remarks == null ? 'N/A' :value.er_ecd_remarks )+'</td>';
                    html += '</tr>';
                });
                if(html != ''){
                    $('#result-body').html(html);
                    $('#canvas').show();
                }else{
                    $('#teacher_remarks').hide();
                    $('#pdf').hide();
                    $('#result-body').html('<tr><td colspan="12" style="text-align:center;font-size:24px">No Result Found!</td></tr>');
                    $('#canvas').hide();
                    $('#course_name').html('Course Name');
                }
            },
            error: function(xhr) {

            }
    });
    getMsgs(id);
}

function getMsgs(id){
    var ad_username = "<?php echo auth()->user()->username; ?>";
        var token = "<?php echo csrf_token(); ?>"; // CSRF token
        $.ajax({
            type: 'get',
            url: '/eportal/get-user-messages?reqid='+id,
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json',
                'ad_username': ad_username,
                'X-CSRF-TOKEN': token
            },
            processData: false,
            contentType: false,
            success: function (res) {
                 //alert(res.tmTitle);
                let  tmTitle = res.tmTitle;
                $('#tmTitle').html(tmTitle);
                let parent_message_id = res.data[0].parent_message_id;
                if (res.status) {
            $('#msg_cotainer').html('');
            $('#msg_cotainer_send').html('');

            $(res.data).each(function (index, row) {
                if (ad_username != row.entry_user_id) {
                        var new_row_append =
                        '<div class="chat_div_other row"><div class="col-4 text-center" style="padding-right: 25px;font-size:12px"><strong>'
                            + row.message_to +
                            ' </strong></div> <div class="col-8">'
                            + row.message +
                            '</div></div>' +
                            '<small class="mf-date text-info" style="padding-left: 50px; margin-top: -10px; margin-bottom: 10px;"><i class="fa fa-clock-o mr-1"></i>' + row.time_created + '</small>';
                    } else {
                        var new_row_append = '<div class="chat_div row"><div class="col-4 text-center" "><strong>'
                            + row.message_to +
                            ' </strong></div> <div class="col-8">'
                            + row.message +
                            '</div></div>' +

                            '<small class="mf-date text-info" style="text-align: right; padding-right: 50px; margin-top: -10px; margin-bottom: 10px;"><i class="fa fa-clock-o mr-1"></i>' + row.time_created + '</small>';
                    }

                     $('#msg_cotainer_send').append(new_row_append);
                  });
            }

            },
            error: function (jqXHR, exception) {
            },
        });
}
function sendMsgBtn() {
        event.preventDefault();
        let message = document.getElementById("messageTextarea").value.trim();
        if (message === '') {
            return false;
        }
        var bi_form = document.querySelector('#sendMessage_form');
        var formData = new FormData(bi_form);
        $.ajax({
            type: 'POST',
            url: '/eportal/send-message-to-user',
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json'
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    getMsgs( $('#RequestID').val());
                    $('#messageTextarea').val('');
                    // var username = $('#sendToUsername').val();
                    // window.location.reload();
                    setTimeout(function () {
                        $('#successMsg').html('');
                    }, 2000);
                }
            }
        });
}





document.addEventListener('DOMContentLoaded', function () {
    // disable enter key on form
    document.addEventListener('keydown', function (event) {
        if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
            event.preventDefault();
            sendMsgBtn();
        }
    });
});

let counter = 1500;
    function checkLimit() {
  let limit = 1500;
  // Get the input element (text area) by its ID
  const inputMessage = document.getElementById('messageTextarea');
  let inputValue = inputMessage.value;
  const countLimit = document.getElementById('countLimit');

  // Check if the input value is greater than 10 characters
  if (inputValue.length > limit) {
    // Truncate to the first 10 characters if character length exceeds 10
    inputValue = inputValue.substring(0, limit);
    inputMessage.value = inputValue;

    // Use classList to add and remove classes
    countLimit.classList.remove('badge-primary');
    countLimit.classList.add('badge-danger');
  } else {
    // If the input value is within the limit, revert to the original class
    countLimit.classList.remove('badge-danger');
    countLimit.classList.add('badge-primary');
  }

  // Update the counter to show how many characters are left
  counter = limit - inputValue.length;
  countLimit.textContent = counter;
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
            '<?=url('api/library/search-eportal?status=')?>'+$('#taskStatus').val(),
            {
                data: { 'qry': qry}
            }
        ).done(function (res) {
            console.log(res);
            callback(res)
            var taskstatus = $('#taskStatus').val();
            getCateg(taskstatus);
            // alert('123');
        });
    }

}
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    // alert('ok')
                    var val = item.value+' || '+item.text
                    $('#sai_enrollment_no').val(val);
});
</script>
@stop

