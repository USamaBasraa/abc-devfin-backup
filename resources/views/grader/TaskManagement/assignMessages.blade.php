@extends('layouts.app')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<style>
     .text-wrap {
      /* Add your desired styles here */
      word-wrap: break-word;
      white-space: pre-line;
    }
.invisible { display: none; }
.modal-content {
    border-radius: 10px;
  }

  .modal-title {
    font-weight: bold;
    margin-bottom: 0;
  }

  .form-group label {
    font-weight: bold;
  }

  .form-control {
    border-radius: 3px;
  }
  .question-title {
  color: #007bff;
  text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.5);
}

.icon-question-sign {
  font-size: 40px;
}
.close {
    background-color: #007bff;
    color: #ffffff;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    font-size: 35px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s, transform 0.3s;
}

.close:hover {
    background-color: #ffffff;
    color: #1abc9c;
    transform: scale(1.1);

}
.form-input {
    border-radius: 10px;
    padding: 15px;
    font-size: 18px;
}
.error {
  color: #ff0000; /* Set the text color to red */
  font-size: 14px; /* Adjust the font size as desired */
  margin-top: 5px; /* Add some space above the error message */
}
#ChatBody {
    max-height: 400px; /* Adjust the height as needed */
    overflow-y: auto;
}
</style>


	<!-- Row -->
    <div class="row" style="padding-bottom:20px">
        <div class="col-md-12 col-lg-12">
            <div class="bootstrap snippet card overflow-hidden bg-transparent">
                <div class="tile tile-alt mb-0 col-md-10 col-lg-8" id="messages-main">

                    <div id="chaty"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Row-->
@endsection


<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>


<script>

$(document).ready(function() {;
    $('.reply-btn').click(function() {
        var email = $(this).data('email');
        var id = $(this).data('id');
        var phone_number = $(this).data('phone-number');
        $('#replyto').val(email);
        $('#replytoid').val(id);
        $('#phone_number').val(phone_number);
    });


    // $('#contactModal').modal('show');
    $("#replyModalButton").click(function(e) {
        e.preventDefault();
        var ad_username = "<?php echo auth()->user()->ad_username; ?>";
        var token = "<?php echo csrf_token();?>"; // CSRF token
        // alert(ad_username);
        let data = $("#replyForm").serializeArray();
        data.push({ name: 'ad_username', value: ad_username });
        var button = $(this);
        button.prop('disabled', true);
        var spinnerIcon = '<i class="icon-spinner icon-spin"></i>';
        button.html(spinnerIcon + ' Sending...');
        $('#closeIcon').hide();
        $.ajax({
            type: 'POST',
            url: 'api/submit-ask-answer',
            data: data,
            headers: {
            'ad_username': ad_username,
            'X-CSRF-TOKEN': token
        },
            success: function(response) {
                if (response.status) {
                    setTimeout(function() {
                    button.html('<i class="icon-ok"></i> Sent');
                    $('#success_msg').html(response.message + ' <span class="icon-smile"></span>');
                    $('#success_msg').removeClass('invisible');
                    setTimeout(function() {
                        $('#success_msg').addClass('invisible');
                        button.html('<i class="icon-ok"></i> Submit');
                    }, 2000);
                    document.getElementById("replyMessage").value = '';
                    button.prop('disabled', false); // Re-enable the button
                    $('#closeIcon').show();
                    window.location.reload();
                },3000);
                }
       },
       error: function(xhr) {
                        button.html('<i class="icon-ok"></i> Submit');
                        button.prop('disabled', false); // Re-enable the button
                        displayValidationErrors(xhr);
            },
            complete: function() {
            // Run the following code regardless of success or error
            button.html('<i class="icon-ok"></i> Submit');
            button.prop('disabled', false); // Re-enable the button
        }


        });

        });

        // others work:


        getMesssags()


    });

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






function getMesssags(){
    // alert('ok');
    var ad_username = "<?php echo auth()->user()->ad_username; ?>";
    var token = "<?php echo csrf_token();?>"; // CSRF token

    $.ajax({
            type: 'POST',
            url: '/staff/assign-to-get-messages',
            // data: data,
            headers: {
            'ad_username': ad_username,
            'X-CSRF-TOKEN': token
        },

            success: function(res) {
                console.log(res[0].tm_client_name)
                var recievedMsg = '';
                var sendMsg = '';
                var div = '';
                var parent_message_id=res[0].parent_message_id




div += '<div>';
div += '<div class="action-header clearfix">';
div += '<div class="float-left hidden-xs d-flex ml-6 chat-user">';
div += '<img src="../../assets/images/users/16.jpg" alt="" class="avatar avatar-md brround mr-2">';
div += '<div class="align-items-center mt-2">';
div += '<span class="text-white font-weight-bold" id="tmClientName">'+res[0].tm_client_name+'</span>';
div += '</div>';
div += '</div>';
div += '</div>';
div += '<div class="chat-body-style" id="ChatBody">';


var authenticatedEmployeeId = '<?php echo auth()->user()->employee_id; ?>';

$(res).each(function(index, row) {
    let receivedMsg = '<div class="message-feed media mt-0">' +
        '<div class="float-left pr-2">' +
        '<img src="../../assets/images/users/16.jpg" alt="" class="avatar avatar-md brround">' +
        '</div>' +
        '<div class="media-body">' +
        '<div class="mf-content">' +
        row.message +
        '</div>' +
        '<small class="mf-date"><i class="fa fa-clock-o"></i> ' +
        row.created_at +
        '</small>' +
        '</div>' +
        '</div>';

    let sendMsg = '<div class="message-feed right">';
    sendMsg += '<div class="float-left pr-2">';
    sendMsg += '<img src="../../assets/images/users/5.jpg" alt="" class="avatar avatar-md brround">';
    sendMsg += '</div>';
    sendMsg += '<div class="media-body text-right">';
    sendMsg += '<div class="mf-content">';
    sendMsg += row.message;
    sendMsg += '</div>';
    sendMsg += '<small class="mf-date"><i class="fa fa-clock-o"></i>';
    sendMsg += row.created_at;
    sendMsg += '</small>';
    sendMsg += '</div>';
    sendMsg += '</div>';

    if (row.entry_user_id === authenticatedEmployeeId) {
        // If the message is from the authenticated user, display it on the right side
        div += sendMsg;
    } else {
        // Otherwise, display the received message on the left side
        div += receivedMsg;
    }
});

div += '</div>';
div += '<div class="msb-reply pb-3">';
div += '<form id="sendReplyForm"> @csrf';
div += '<textarea placeholder="What\'s on your mind..." id="messageTextarea" name="messageTextarea"></textarea>';
div += '<button onclick="btn_sendMessage('+parent_message_id+')"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">' +
    '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z" opacity=".3"/>' +
    '<path d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z"/></svg></button>';
div += '</form>';
div += '</div>';

div += '</div>';
div += '</div>';


$('#chaty').html(div);




       },
       error: function(xhr) {

            },
            complete: function() {

        }


        });

}



function btn_sendMessage(parent_message_id) {
    // alert(parent_message_id);

event.preventDefault()
let message = document.getElementById("messageTextarea").value.trim();
if (message === '') {
    return false;
}

//         // $('#messageBtn').attr('disabled', true);
var bi_form = document.querySelector('#sendReplyForm');
var formData = new FormData(bi_form);
formData.append('taskIdInput', parent_message_id);
var authUsername = '<?= auth()->user()->username ?>';
$.ajax({
    type: 'POST',
    url: '/staff/assign-to-message',
    headers: {
        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
        Accept: 'application/json'
    },
    data: formData,
    processData: false,
    contentType: false,
    success: function(res) {
        if (res.status) {
            document.getElementById("messageTextarea").value = '';
            getMesssags()

        }
    }

});


}








</script>
