
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
</style>
<style>
    .column-link {
        cursor: pointer; /* Set cursor style to pointer */
    }

    span{
        border: 0px solid black;
    }
    .accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}

    .status-btn {
      width: 120px; /* Adjust the width as needed */
    }
    .e-panel{
        /* border:solid 2px green !important;
        background-color:yellow; */
    }
</style>


<script>
    $(document).ready(function () {
        // Initialize the accordion behavior
        $(".accordion").click(function () {
            // Get the id from the data-id attribute
            var id = $(this).data("id");
            // Increase the count in the badge
            var badge = $(this).find(".badge");
            badge.text(parseInt(badge.text()) );

            // Perform any other actions you need here
            // For example, you can open a modal or make an AJAX request
            // to load additional content related to the clicked item.
        });
    });
</script>


        <div class="card e-panel" style="margin:3px;width:98%;">
            <div class="card-header">    
                <h3 align ="center"> E-Portal Requests</h3>   <br>
              
            </div>     
            <div class="row">   
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="tabs-menu" style="padding:10px;">
                        <!-- Tabs -->
                        <ul class="nav panel-tabs">
                            <li class=""><a href="#lectures" onclick="pending()"  class="active"      data-toggle="tab">
                                <i class="fa fa-laptop"></i> Pending
                            </a></li>
                            <li><a href="#material" onclick="completed()" data-toggle="tab">
                                <i class="fa fa-book"></i> Completed
                            </a></li>
                        
                        </ul>
                    </div>
                </div>
            </div>  
        </div>
  

        <div class="card-block" style="margin:10px;width:98%">
            <?php
           //dd($res);
            // Initialize an associative array to store rows grouped by id and username
            $groupedRows = [];
            $penStatus='';
            foreach ($res as $row) {

                $id = $row->id;
                $username = $row->username;
                $penStatus=$row->er_request_status;
                // Add the row to the groupedRows array, grouped by id and username
                if (!isset($groupedRows[$id][$username])) {
                    $groupedRows[$id][$username] = [];
                }

                $groupedRows[$id][$username][] = $row;
            }
            //dd($groupedRows);
            foreach ($groupedRows as $id => $usernameRows) { ?>

                <button class="accordion" data-id="<?= $id ?>"><?php echo reset($usernameRows)[0]->title ?> <span style=" display: inline-block;padding: 2px 8px;background-color: red;color: white;border-radius: 30%;">1</span></button>
                <div class="panel">
                    <?php foreach ($usernameRows as $rows) { ?>
                        <div class="row">
                            <?php foreach ($rows as $row) { ?>
                                <div class="col-md-12">
                                    <div class="p-4" style="background-color:#f0f0f0;color:black !important">
                                        <b>Name:</b> <?= $row->full_name ?>   <b>Roll #:</b> <?= $row->username ?>  <b>School:</b> <?= $row->school ?> <b>Dept:</b> <?= $row->programs ?>
                                        <div style="float:right;padding-bottom:10px;">
                                            <?php  { ?>
                                                <button class="btn btn-info" onclick="openModalStatus('<?= $row->er_id ?>')" type="button">Application Tracking</button>
                                            <?php } ?>
                                            <button class="btn btn-success" onclick="openModal('<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>')" type="button">Approval Status</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>





<!-- model start  -->
            <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                            <span style="text-color:white" id="header_modal"></span>
                            <button type="button" style="color:white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body" style="text-align: left;">

                        </div>
                        <div  class="col-12">
                        <div class="form-group">
                                <label for="remarks">Remarks:</label>
                                <input type="text" class="form-control" name="remarks" id="req_remarks">
                            </div>
                        </div>
                        <span id="saved_msg_des"></span>
                                <div class="modal-footer">
                                    <!-- Dropdown button and options -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Request Action
                                        </button>
                                        <input type="hidden" id="ereqID">
                                        @if(auth()->user()->username=="zonia.akbar")
                                                <div class="dropdown-menu">                                             
                                                    <a class="dropdown-item" name ="markComplete" id="markComplete">Mark as Complete</a>
                                                    <a class="dropdown-item" href="#" id="markRejected" name = "markRejected">Rejected</a>
                                                </div>
                                            @else
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="approvalStatus('Approved')" name ="markApproved" id="markApproved">Approved</a>
                                                    <a class="dropdown-item" onclick="approvalStatus('Rejected')" href="#" id="markRejected" name = "markRejected">Rejected</a>
                                                </div>                                               
                                            @endif          
                                    </div>
                                    <!-- <button type="button" class="btn btn-secondary">Recommended</button> -->
                                </div>
                    </div>
                </div>
            </div>


<div class="modal" id="applicationStatus" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <!-- <div class="modal-dialog modal-dialog-centered" role="document"> -->
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="application_status"></span>
                <!-- <p class="h5 modal-title" id="ModalTitle">Students Profile Details</p> -->
                <button type="button" class="close" style="color:white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">
                <!-- <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="font-weight:bold" class="h4">#</th>
                                <th style="font-weight:bold" class="h4">Department</th>
                                <th style="font-weight:bold" class="h4">Status</th>
                                <th style="font-weight:bold" class="h4">Reason</th>

                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td class="align-middle"><span class="h5">2</span></td>
                                <td class="align-middle h5">RO</td>
                                <td class="align-middle">
                                    <button class="btn <?=($penStatus=='Pending')?"btn-info": 'btn-success';?> status-btn"><?=$penStatus;?></button>
                                </td>
                                <td class="align-middle h5">N/A</td>
                            </tr>

                        </tbody>
                    </table>
                </div> -->

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
        {{-- <button type="button" style="font-size: 35px;" class="close" aria-label="Close"> --}}
            {{-- <span aria-hidden="true">&times;</span> --}}
        </button>
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
        <input type="hidden" id="login_username" value="<?=auth()->user()->username;?>"
            name="login_username">
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
                    <div class="text-left" id="msg_container_send"></div>
                </div>
            </div>


            <div id="msg_cotainer_send" class="text-left"></div>


                <div id="msg_container_send">

                </div>


                <div class="container mt-5">
                    <div class="row justify-content-center">
                    <div class="col-md-11">
                            <div class="message-container">
                                <!-- Messages will be appended here -->

                                <!-- <div class="media mb-3">
                                    <div class="media-body p-3 border rounded bg-info text-white">
                                            <i class="fa fa-user fa-lg mr-2"></i>
                                            <strong>Accounts:</strong> One fee challan is unpaid
                                            <div class="small text-muted mt-2">
                                                <i class="fa fa-clock fa-sm mr-1"></i>2023-08-24 09:37:36
                                            </div>
                                        </div>
                                    </div>
                                                                    <div class="media mb-3">
                                        <div class="media-body p-3 border rounded bg-light">
                                            <i class="fa fa-building fa-lg mr-2"></i>
                                            <strong>RO:</strong> Completed from our side
                                            <div class="small text-muted mt-2">
                                                <i class="fa fa-clock fa-sm mr-1"></i>2023-08-24 09:37:36
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>
<div class="modal" id="recmended" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <!-- <div class="modal-dialog modal-dialog-centered" role="document"> -->
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="application_status"></span>
                <!-- <p class="h5 modal-title" id="ModalTitle">Students Profile Details</p> -->
                <button type="button" class="close" style="color:white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">

                <div class="card">
    <div class="card-header" style="border-top: 4px solid #8CDDCD">

        {{-- <button type="button" style="font-size: 35px;" class="close" aria-label="Close"> --}}
            {{-- <span aria-hidden="true">&times;</span> --}}
        </button>
    </div>

        <!-- User Dropdown (Searchable) -->
        <div class="form-group" id="user_dropdown" style="display: none;">
            <label for="user">Select User</label>
            <select class="form-control" id="user_id" name="user_id">
                <option value="">-- Select User --</option>
                {{-- @foreach($users as $user)
                    <option value="{{$user->id}}~{{$user->emp_dept_id}}">{{$user->full_name}}</option>
                @endforeach --}}
            </select>
            <!-- Hidden input to store the selected user ID -->
        </div>


</div>



            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success"  >Mark as Complete</button>
                <button type="button" class="btn btn-secondary"  >Recommned to Head</button> -->
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script type="text/javascript">
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
function openModal(jsonString){
    var data = JSON.parse(jsonString); // Parse the JSON string back to an object

    console.log(data);
     //alert(data.cate_id)
    $('#ereqID').val(data.er_id)
// Access the modal element
var modal = document.getElementById("myModal");

// Access the modal's content area
var modalContent = modal.querySelector(".modal-body");
    // Access and set the content of an element with the ID "header_modal"
    $('#header_modal').html(data.full_name + ' ' + data.username);
    // modalContent.innerHTML = "<p>Name: " + data.full_name + "</p><p>Username: " + data.username + "</p>";er_upload_file_name
// Populate the modal content with data from the JSON object
modalContent.innerHTML = "<p>Name: " + data.full_name + "</p><p>School: " + data.school + "</p><p>Program: " + data.programs + "</p><p>Contact: " + data.mobile + "</p><p >Request Decription: <span style='font-weight:bold'>" + data.description + "</span></p>"+
"<p>Upload File: <a target='_blank' style='font-weight:bold' href='http://studenttest.bnu.edu.pk/images/" + data.er_upload_file_name + "'>Download File</a></p>";
$(modal).modal("show");
   
    // var username = row.username;
    // alert(row.name);
      $('#header_modal').html(data.full_name + data.username);
      $("#header_modal").css("font-weight", "bold");
      // Use jQuery to show the modal
    //   $("#myModal").modal("show");






}

function approvalStatus(status){
    // alert(status);
    // return false;
    var id = $('#ecd_id').val();
        var remarks = $('#req_remarks').val();
        $.ajax({
            url: "{{ route('eportal_request_approved') }}?id="+id+'&remarks='+remarks+'&status='+status,
            method: 'get',
            data: '',
            success: function(response) {
                 alert(response.message);
                // $('#saved_msg_des').html(response.message);
                // setTimeout(function() {
                //     location.reload();
                // $('#myModal').modal('hide');
                // }, 1500);
            },
            error: function(xhr, status, error) {
            
                console.error(xhr.responseText);
            }
        });
        console.log("Mark as Complete clicked");

}
    $('#markComplete').click(function () {
                var id = $('#ereqID').val();
                var remarks = $('#req_remarks').val();
                $.ajax({
                url: "{{ route('eportal_request_complete') }}?id="+id+'&remarks='+remarks,
                method: 'get',
                data: '',
                success: function(response) {
                   // alert(response.message);
                    $('#saved_msg_des').html(response.message);
                    setTimeout(function() {
                        location.reload();
                    $('#myModal').modal('hide');
                    }, 1500);
                },
                error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                }
            });
        console.log("Mark as Complete clicked");
    });


    $('#markRejected').click(function () {
                var id = $('#ereqID').val();
                var remarks = $('#req_remarks').val();
                $.ajax({
                url: "{{ route('eportal_request_reject') }}?id="+id+'&remarks='+remarks,
                method: 'get',
                data: '',
                success: function(response) {
                    if(response.data){
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {

                    console.error(xhr.responseText);
                }
            });
        console.log("Mark as Complete clicked");
    });

    function openModalrecomended(title){
      $('#application_status').html(title);
      $("#application_status").css("font-weight", "bold");
      // Use jQuery to show the modal
      $("#recmended").modal("show");

}


function openModalStatus(id){
    //alert(id);
     $('#RequestID').val(id);
      $('#application_status').html('asdasd');
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
function completed(){
    var routeUrl = "{{ route('eportal_completed') }}"; // Replace 'my.route' with your route name
    window.location.href = routeUrl;
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

