
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
    .larger-icon {
        margin-top:10px;
        font-size: 1.5em; /* You can adjust the font-size for larger or smaller icons */
        padding-top: 10px; /* Adjust the padding as needed */
    }
    .blink_me {
  animation: blinker 1s linear infinite;
  color:tomato;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<div class="card e-panel" style="margin:10px;width:98%; padding-right: 30px;">
    <div class="card-header">
        <h3 align ="center"> E-Portal Tracking</h3>   <br>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
            <div class="tabs-menu" style="padding:10px;">
                <!-- Tabs -->
                <ul class="nav panel-tabs">
                    <li class=""><a href="#lectures" onclick="status('Pending')"  id="setclasspending"      data-toggle="tab">
                        <i class="fa fa-laptop"></i> Escalated
                    </a></li>
                    <li style="display:none;"><a href="#material" onclick="status('Approved')" id="setclasscompleted"  data-toggle="tab">
                        <i class="fa fa-book"></i> Completed
                    </a></li>
                </ul>
                <input type="hidden" id="taskStatus" value="Pending">
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="display:none;">
            <select class="form-control" id="category_id" onchange="getCateg()" name="category_id">
                <option value="0">All Requests</option>
                @foreach($resEpCateg as $user)
                    <option value="{{$user->id}}">{{$user->ec_title}}<span style="font-weight:bold"> </span></option>
                    {{-- <option value="{{$user->id}}">{{$user->ec_title}}<span style="font-weight:bold"> <b>(Pending Count: {{$user->total_rec}})</b></span></option> --}}
                @endforeach
            </select>
        </div>
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
                <div class="row">
                    {{-- <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                        <label for="name"><b>Res Remarks:</b></label>
                    </div> --}}
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                        <span id="resolving_user_remarks"></span><br>
                    </div>
                </div>
                <div id="dropDown"></div>

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
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1150px !important">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="application_status"></span>
                <button type="button" class="close" style="color:white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">
                <div class="card-body">
                <div align="center" id="escalate-tr"> </div>

                    <table class="table table-sm  table-bordered">
                        <thead>
                        <tr>
                            {{-- <th scope="col">Sr.No</th> --}}
                            <th scope="col">Department</th>
                            <th style="text-align:center;" scope="col">Status</th>
                            <th style="text-align:center;" scope="col">Remarks</th>
                            <th style="text-align:center;" scope="col">Request Time</th>
                            <th style="text-align:center;" scope="col">Approval Time</th>
                            <th style="text-align:center;display:none" id="comptime" scope="col">Completion Time</th>
                            <!-- <th style="text-align:center;display:none" id="ecstime" scope="col">Esclation Time</th> -->
                        </tr>
                        </thead>
                        <tbody id="result-body" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
                    </table>
                </div>
                <div class="card">

                    <div class="card-body border-0 rounded-0">
                        <div >
                            <h4 class="card-title">Messages</h4>
                        </div>


                        <div class="col-lg-12">
                            <div class="card1 widget-chat-box" style="border-top: 0px solid">
                                <div class="card-block pr-2">
                                    <form enctype="multipart/form-data" method="post" name="sendMessage_form" id="sendMessage_form" class="fixed-form">
                                        @csrf
                                        <div class="message-option mt-2 mb-2" style="display: inline-block; vertical-align: middle;">
                                            <input type="checkbox" value='1' class="message-checkbox" id="sendMessageToStudent" name="sendMessageToStudent" style="width: 20px; height: 20px; vertical-align: middle;">
                                            <label class="message-label" for="sendMessageToStudent" style="font-size: 18px; vertical-align: top;">Send a message to the student</label>
                                        </div>

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
                    <div id="msg_cotainer_send" class="text-left " style="height: 200px; overflow: auto;"></div>
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
<script type="text/javascript">
$(document).ready(function () {
    // alert($('#category_id').val());
   // $("#category_id").val(selectedValue);
    getCateg($('#taskStatus').val());
});
function reqBtn(action){
    var reqRemarksValue = $('#req_remarks').val();
    if (reqRemarksValue === null || reqRemarksValue === "") {
        // Input is empty or null, so perform validation here
        // You can return false or show an error message or take any other action
        $('#remarksReq').html('Remarks Required ');
        $('#remarksReq').css('color','red');
        return false;
    }else{
        $('#remarksReq').html('');
    }
    var button = $('#btnloader');
    button.prop('disabled', true);
    var spinnerIcon = '<i class="fa fa-spinner fa-spin"></i>';
    button.html(spinnerIcon + ' Updating');
    // button.html('<i class="fa fa-check"></i> Updated');
    var bi_form = document.querySelector('#approvalForm');
    var formData = new FormData(bi_form);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('action', action);
    $.ajax({
        url: "{{ route('eportal_request_approved') }}",
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false, // Ensure FormData is not processed
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
          // alert(response.resstatus);
           button.html('<i class="fa fa-check"></i> Updated');
            $('#approval_status').modal('hide');
            getCateg($('#taskStatus').val(response.resstatus));

        },
        error: function(xhr, status, error) {
            button.html('<i class="fa fa-plus"></i> Action');
            button.prop('disabled', false); // Re-enable the button
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
    }else if (val == 'Approved'){
        // $('#result-body-categ').hide();
        getCateg('Approved');
        $('#category_id').val(0)
    }
}
function getCateg(sel){
    // alert(sel);
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
    // alert(selectedText);
    $.ajax({
        url: "{{route('vc/get_categ_appr')}}?ec_id="+ec_id+"&sel="+sel+"&taskStatus="+taskstatus,
        method: 'get',
        success: function(data) {
            var sr_no = 1;
            var html = '';
            html += '<div class="card e-panel" style="margin:10px;width:98%;">';
            html += '<div class="card-header">';
            html += '<h3 align ="center">'+selectedText+' ('+taskstatus+')</h3><br>';
            html += '</div>';
            html += '<div class="row">';
            html += '<div class="col-12 col-sm-12 col-md-12 col-lg-12">';
            html += '<table class="table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th scope="col">#</th>';
            html += '<th scope="col">Name</th>';
            html += '<th scope="col">School</th>';
            html += '<th scope="col">Degree Program</th>';
			html += '<th scope="col">Type</th>';
			// html += '<th scope="col">Type / Resolving Status</th>';
            html += '<th scope="col">Request Time</th>';
            // html += '<th scope="col">Request End Time</th>';

            html += '<th scope="col">Status</th>';
            html += '<th scope="col">Tracking</th>';
            html += '<th scope="col">Details</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            $.each(data,function(key,value) {

                var esclationTime = moment(value.er_escalation_time, 'YYYY-MM-DD HH:mm:ss Z');
                var currentTime = moment();
                var color = '';

                // Calculate the time difference in hours
                var hoursDifference = esclationTime.diff(currentTime, 'hours');

                // Check if the difference is within 12 hours and greater than 11 hours

                // if (hoursDifference >= 11 && hoursDifference < 12) {
                if (hoursDifference < 12) {
                    // Trigger an alert 12 hours before esclation_time
                    // alert('12 hours remaining until esclation_time for ' + value.full_name);
                    if(taskstatus == 'Pending'){
                        var color = 'grey';
                    }
                }
                var valueJson = JSON.stringify(value);
                var badgeColor;
                console.log(value)
                if (value.er_request_status === 'Declined') {
                    badgeColor = 'red';
                } else if (value.er_request_status === 'Pending') {
                    badgeColor = '#007bff';
                } else {
                    badgeColor = 'orange';
                }
                valueJson = valueJson.replace(/"/g, '&quot;');
                console.log(value)
                html += '<tr style="color:'+color+'">';
                html += '<td scope="col">'+ (sr_no++) +'</td>';
                html += '<td scope="col">'+value.full_name+' ('+value.er_sai_enrollment_no+')</td>';
                html += '<td scope="col">'+value.school+'</td>';
                html += '<td scope="col">'+value.programs+'</td>';
				html += '<td scope="col">'+value.ec_title+'</td>';
				// html += '<td scope="col">'+value.ec_title+' /<b> '+value.er_request_status+'</b></td>';
                html += '<td  scope="col">'+value.er_entry_datetime+'</td>';
                // html += '<td  scope="col">'+value.er_escalation_time+'</td>';
                html += '<td scope="col"><span style="display: inline-block; padding: 0.25em 0.5em; border-radius: 0.25rem; background-color: ' + badgeColor + '; color: #fff; font-weight: bold;">'+value.er_request_status+'</span></td>';
                // html += '<td><button class="btn btn-info" onclick="openModalStatus('+value.er_id+')" type="button">Application Tracking</button></td>';
                html += '<td scope="col"><button type="button" class="btn btn-success" onclick="openModalStatus('+value.er_id+',\'' + value.er_sai_enrollment_no + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search mr-1"></i>Tracking</button></td>';
                html += '<td scope="col"><button type="button" class="btn btn-primary" data-toggle="modal" onclick="setProfile(\'' + valueJson + '\')"  data-target="#approval_status"> <i class="fa fa-eye"></i> Request</button></td>';
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
    var valueObj = JSON.parse(valueJson);
    console.log(valueObj.full_name);
    $('#header_modal').html('Approval Status'+' ('+valueObj.er_sai_enrollment_no+')')
    $('#stdname').html(valueObj.full_name);
    $('#stdschool').html(valueObj.school);
    $('#stdprog').html(valueObj.programs);
    $('#stdmob').html(valueObj.mobile);
    $('#stddesc').html(valueObj.description);
    $('#er_id').val(valueObj.erd_id);

if (valueObj.id == 18 || valueObj.id == 25) {
    if (valueObj.er_student_upload_challan) {
        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_student_upload_challan + '">Uploaded File by Student <i class="fa fa-download larger-icon"></i></a>');
    } else {
        $('#uploadedFileStudent').html('N/A');
    }
} else {
    if (valueObj.er_request_file) {
        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '">Uploaded File by Student <i class="fa fa-download larger-icon"></i></a>');
    } else {
        $('#uploadedFileStudent').html('N/A');
    }
}

if (valueObj.google_drive_url) {
    $('#uploadedFile').html('<a href="' + valueObj.google_drive_url + '">Uploaded File by Department <i class="fa fa-eye larger-icon"></i></a>');
} else {
    $('#uploadedFile').html('N/A');
}

    $('#resolving_user_remarks').html(valueObj.er_remarks);
    var taskstatus = $('#taskStatus').val();
    taskstatus==='Approved'?'Completed':taskstatus;
    var html = '';
    html += '<form id="approvalForm">';
    html += '<input type="hidden" id="er_id" name="erd_id" value="'+valueObj.erd_id+'">'
    html += '<input type="hidden"  name="er_id" value="'+valueObj.er_id+'">'
    // html += '<label  for="remarks"><b>Remarks:</b></label>';
    html += '<input   style="display:none;" type="text" class="form-control" name="remarks" id="req_remarks">';
    html += '<div style="float:right;margin-top: 10px;margin-bottom: 10px">';
    html += '<span id="remarksReq"></span>';
    html += '<button id="btnloader"  style="display:none;" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ';
    html += 'Action';
    html += '</button>';
    html += '<div class="dropdown-menu"> ';
    if(taskstatus == 'Approved'){
        html += '<a class="dropdown-item" onclick="reqBtn(\'Pending\')">RollBack</a>';
    }else{
        html += '<a class="dropdown-item" onclick="reqBtn(\'Approved\')">Approve</a>';
        html += '<a class="dropdown-item" style="display:none;" onclick="reqBtn(\'Declined\')">Decline</a>';
        html += '<a class="dropdown-item" onclick="reqBtn(\'futher_action\')">Further Action Needed</a>';
    }
    html += '</div>';
    html += '</div>';
    html += '</form>';
    $('#dropDown').html(html);

}
function openModalStatus(id,username){
    // alert(id);
    // alert(username);
    $('#msg_cotainer_send').html('');
    $('#RequestID').val(id);
    $('#sendToUsername').val(username);
    // $('#application_status').html('asdasd');
    $("#application_status").css("font-weight", "bold");
    //   Use jQuery to show the modal
    $("#applicationStatus").modal("show");
    $.ajax({
        type: 'get',
        url: '/vc/eportal-approval-authority?id='+id,
        processData: false,
            contentType: false,
            data: {
                'id':id
            },
            success: function(res) {
                $('#application_status').html(res.data[0].title+' ('+username+')');
                var dateTime=new Date();
                var taskstatus = $('#taskStatus').val();
                //alert(dateTime);
                html='';
                $.each(res.data,function( key, value ) {
                    //alert(value.er_escalation_time+"==="+dateTime)
                    const dateString = dateTime;
                    const dateObject = new Date(dateString);

                    const year = dateObject.getFullYear();
                    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
                    const day = String(dateObject.getDate()).padStart(2, '0');
                    const hours = String(dateObject.getHours()).padStart(2, '0');
                    const minutes = String(dateObject.getMinutes()).padStart(2, '0');
                    const seconds = String(dateObject.getSeconds()).padStart(2, '0');

                    const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

                   
                    // alert(value.er_escalation_time+"==="+formattedDate)
                    const dateStringWithOffset = value.er_escalation_time;
                    const dateStringWithoutOffset = dateStringWithOffset.split(' ')[0] + ' ' + dateStringWithOffset.split(' ')[1];


                    const dateString1 = dateStringWithoutOffset;
                    const dateString2 = formattedDate;

                    // Parse the date strings into Date objects
                    const date1 = new Date(dateString1);
                    const date2 = new Date(dateString2);

                    // Calculate the time difference in milliseconds
                    const timeDifference = date2 - date1;

                    // Convert the time difference to days
                    //alert(timeDifference);
                    const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

                    const remainingMilliseconds = timeDifference % (1000 * 60 * 60 * 24);
                    const hoursDifference = Math.floor(remainingMilliseconds / (1000 * 60 * 60));

                    
                    //console.log(`The number of days between the two dates is: ${daysDifference}`);
                    //alert(daysDifference)
                    //console.log(dateStringWithoutOffset);
                    var name = '';
                    var dept = '';
                    var status = '';
                    if(value.full_name == null){
                        var name = ' ('+value.res_full_name+') (Resolving Authority)';
                        var status = value.er_request_status;
                    }else if(value.res_full_name == null){
                        var name = ' ('+value.full_name+') (Approving Authority)';
                        var status = value.erd_status;
                    }
                    if(value.department == null){
                        var dept = value.res_dept;
                    }else if(value.res_dept == null){
                        var dept = value.department;
                    }
                    var srno = parseInt(key+1);
                    html += '<tr>';
                    // html += '<td>'+srno+'</td>';
                    html += '<td >'+dept+name+'</td>';
                    html += '<td><span class="badge ' + (status === 'Pending' ? 'badge-info' : 'badge-primary') + '">' + status + '</span></td>';
                    html += '<td>'+(value.er_ecd_remarks == null ? 'N/A' :value.er_ecd_remarks )+'</td>';
                    // html += '</tr>';
                    // html += '<tr>';
                    html += '<td>'+value.er_entry_datetime+'</td>';
                    html += '<td>'+(value.er_ecd_appr_datetime==null?'N/A':value.er_ecd_appr_datetime)+'</td>';
                    if(taskstatus == 'Pending'){                        
                        $('#comptime').show();
                        $('#ecstime').show();
                        html += '<td>'+(value.er_resolving_datetime==null?'N/A':value.er_resolving_datetime)+'</td>';
                    }else{
                        $('#comptime').hide();
                        $('#ecstime').hide();
                    }
                  
                    html += '</tr>';
                    $('#escalate-tr').html('<strong class="text-align">Escalate Time </strong>:<span class="blink_me">'+daysDifference+' Days and '+hoursDifference+' hours ago</span>');

                });
                if(html != ''){
                    $('#result-body').html(html);

                   // html += '<td><span class="blink_me">'+daysDifference+' Days and '+hoursDifference+' hours ago</span></td>';

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
</script>
@stop

