@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Optional: Add custom styles for the button -->
    <style>
        .go-back-button {
            text-decoration: none;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
        }

        /* Adjust icon size and spacing as needed */
        .go-back-button i {
            margin-right: 5px;
            font-size: 16px;
        }

        /* Add hover effect */
        /* .go-back-button:hover {
                background-color: #0056b3;
            } */
    </style>
    <style type="text/css">
        .chat_div {
            /* display: flex; */
            /* justify-content: flex-start; */

            align-items: center;
            border-radius: 20px;
            border: 1px solid green;
            padding: 10px;
            background-color: #DCF8C6;
            text-align: right;
            margin-bottom: 10px;
            color: #000;
            font-size: 18px;
            padding-right: 50px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .chat_div_other {
            align-items: center;
            border-radius: 20px;
            border: 1px solid green;
            padding: 10px;
            background-color: #FFF;
            text-align: left;
            margin-bottom: 10px;
            color: #000;
            font-size: 18px;
            padding-left: 50px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
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

    <?php
    $task_id = 0;
    if (isset($_REQUEST['task_id']) && $_REQUEST['task_id'] != '') {
        $task_id = $_REQUEST['task_id'];
    }
    ?>

    <div class="card overflow-hidden mb-0 border-0">
        <!-- action-header -->
        <div class="action-header" style="background-color:white">
            <div class="float-left hidden-xs d-flex ml-2">
                <div class="img_cont mr-3">
                    <img src="../../assets/images/users/16.jpg" class="rounded-circle user_img avatar avatar-md"
                        alt="img">
                </div>
                <div class="align-items-center mt-2">
                    <h1 class="mb-0"><span id="tmTitle"></span></h1>
                    {{-- <span class="dot-label bg-success"></span><span class="mr-3 fs-12">online</span> --}}
                </div>
            </div>
            <div class="float-right hidden-xs d-flex ml-2">
                <a href="https://cms.bnu.edu.pk/staff/task-list/#row<?= $task_id ?>" class="go-back-button">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
            {{-- <ul class="ah-actions actions align-items-center">
            <li>
                <a href="" class="" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24"
                            viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                        </svg></span>
                </a>
            </li>
        </ul> --}}
        </div>
        <!-- action-header end -->

        <!-- msg_card_body -->
        <div class="card-body msg_card_body">
            <div class="col-lg-12">
                <div class="card widget-chat-box" style="border-top:0px solid">
                    <div class="card-block p-3">
                        <form enctype="multipart/form-data" method="post" name="sendMessage_form" id="sendMessage_form"
                            class="fixed-form">
                            @csrf
                            <div class="mb-3 mt-2 text-danger">
                                <span>
                                    <i class="fa fa-keyboard pr-1 pl-2"></i> Press <strong>Shift + Enter</strong> to go to a new line and Press <strong>Enter</strong> to send a message
                                </span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group mb-3">
                                <textarea id="messageTextarea" name="messageTextarea" class="form-control"
                            placeholder="Please Enter Message..."
                            style="border: 1px solid #424e79 !important; height: 100px;"
                            oninput="processText(this)"></textarea>



                                <input type="hidden" id="sendToUsername" name="sendToUsername">
                                <input type="hidden" value="<?= $_REQUEST['to_user_id'] ?>" id="to_user_id"
                                    name="to_user_id">
                                <input type="hidden" id="taskIdInput" name="taskIdInput" value="<?= $task_id ?>">
                                <input type="hidden" id="login_username" value="<?= auth()->user()->username ?>"
                                    name="login_username">
                                <button type="button" id="messageBtn" onclick="sendMsgBtn()" class="btn btn-primary">
                                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24"
                                        viewBox="0 0 24 24" width="24">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z" opacity=".3" />
                                        <path
                                            d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z" />
                                    </svg>
                                </button>
                            </div>

                        </form>

                        <hr style="border-top:5px solid #007bff;">
                        <div id="msg_cotainer_send" class="text-left"></div>
                    </div>
                </div>
                <p>
                <div id="msg_cotainer"></div>
                <div id="msg_cotainer_send"></div>
                </p>
            </div>
        </div>
        <!-- msg_card_body end -->
        <!-- card-footer -->
    </div>
@endsection


@section('javascript')
    <script type="text/javascript">




        function check_validation(validation_array) {
            var count_errors = 0;
            $.each(validation_array, function(index, value) {
                //alert(value);
                if ($('#' + value).val() === "" || $('#' + value).val() === null || $('#' + value).tokenfield(
                        'getTokensList') === "" || $('#' + value).tokenfield('getTokensList') === null) {
                    $('#' + value).removeClass('is-valid').addClass('is-invalid');
                    count_errors++;
                } else {
                    $('#' + value).removeClass('is-invalid').addClass('is-valid');
                    setTimeout(function() {
                        $('#' + value).removeClass('is-valid');
                    }, 2000);
                }
            });
            if (count_errors > 0) {
                return 0;
            } else {
                return 1;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // disable enter key on form
            document.addEventListener('keydown', function(event) {
                if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                    event.preventDefault();
                    sendMsgBtn();
                }
            });
        });


        $(document).ready(function() {
            getMsgs('<?= $task_id ?>')
        });

        function getMsgs(gettaskid) {
    var ad_username = "<?php echo auth()->user()->employee_id; ?>";
    var token = "<?php echo csrf_token(); ?>"; // CSRF token
    $.ajax({
        type: 'get',
        url: '/staff/get-user-messages?taskid=' + gettaskid,
        headers: {
            Authorization: 'Bearer <?= auth()->user()->api_token ?>',
            Accept: 'application/json',
            'ad_username': ad_username,
            'X-CSRF-TOKEN': token
        },
        processData: false,
        contentType: false,
        success: function(res) {
            let tmTitle = res.tmTitle;
            $('#tmTitle').html(tmTitle);
            let parent_message_id = res.data[0].parent_message_id;
            if (res.status) {

                $('#msg_cotainer').html('');
                $('#msg_cotainer_send').html('');
                $(res.data).each(function(index, row) {
                    var messageToShow = row.message.substring(0, 200); // Show only first 200 characters initially
                    var messageFull = row.message; // Full message
                    var new_row_append = '';
                    let createdTime = convertTime(row);
                    let formattedDateTime = `${createdTime.date} ${createdTime.time}`;
                    console.log(formattedDateTime); // Formatted date and time



                    if (ad_username !== row.entry_user_id) {
                        new_row_append = `
                            <div class="chat_div_other">
                                <div class="row">
                                    <div class="col-12 col-md-3"><strong>${row.message_to} :</strong></div>
                                    <div class="col-12 col-md-9 text-justify">${messageToShow}
                                        <span class="full-message" style="display:none;">${messageFull}</span>
                                        <span class="read-more" style="cursor: pointer; color: blue;">  <i class="fas fa-chevron-circle-down"></i> Show more</span>
                                        <span class="read-less" style="display: none; cursor: pointer; color: blue;"> <i class="fas fa-chevron-circle-up"></i> Show less</span>
                                        <small class="mf-date text-info" style="text-align: right; margin-top: 10px;">
                                            <i class="fa fa-clock-o mr-1"></i>${formattedDateTime}
                                        </small>
                                    </div>
                                </div>
                            </div>`;
                    } else {
                        new_row_append = `
                            <div class="chat_div">
                                <div class="row">
                                    <div class="col-md-3 text-left "><strong>${row.message_to} :</strong></div>
                                    <div class="col-md-9 text-justify">${messageToShow}
                                        <span class="full-message" style="display:none;">${messageFull}</span>
                                        <span class="read-more" style="cursor: pointer; color: blue;"> <i class="fas fa-chevron-circle-down"></i> Show more</span>
                                        <span class="read-less" style="display: none; cursor: pointer; color: blue;">  <i class="fas fa-chevron-circle-up"></i> Show less</span>
                                        <small class="mf-date text-info" style="text-align: right; margin-top: 10px;">
                                            <i class="fa fa-clock-o mr-1"></i>${formattedDateTime}
                                        </small>
                                    </div>
                                </div>
                            </div>`;
                    }

                    $('#msg_cotainer_send').append(new_row_append);

                    // Check if message length exceeds 50 words and show/hide "Show more" accordingly
                    var words = row.message.split(/\s+/).length;
                    if (words > 50) {
                        $('#msg_cotainer_send .read-more').last().show();
                    } else {
                        $('#msg_cotainer_send .read-more').last().hide();
                    }
                });
            }
        },
        error: function(jqXHR, exception) {},
    });
}

// Event delegation for dynamically added elements
$('#msg_cotainer_send').on('click', '.read-more', function() {
    $(this).prev('.full-message').slideDown(); // Show full message
    $(this).hide(); // Hide "Show more" link
    $(this).next('.read-less').show(); // Show "Show less" link
});

$('#msg_cotainer_send').on('click', '.read-less', function() {
    var messageContainer = $(this).parent();
    var fullMessage = messageContainer.find('.full-message');
    var truncatedMessage = messageContainer.find('.text-justify');

    fullMessage.slideUp(); // Hide full message
    truncatedMessage.slideDown(); // Show truncated message

    $(this).hide(); // Hide "Show less" link
    $(this).prev('.read-more').show(); // Show "Show more" link
});

function convertTime(row) {
    // Splitting date and time parts
    let parts = row.time_created.split(" at ");
    let datePart = parts[0];
    let timePart = parts[1];

    // Parsing date part (assuming it's in the format "DD-MM-YY")
    let dateParts = datePart.split("-");
    let year = parseInt(dateParts[2]) + 2000; // Assuming the year is in YY format and we need to convert it to YYYY
    let month = parseInt(dateParts[1]) - 1; // Month is zero-based in JavaScript Date object
    let day = parseInt(dateParts[0]);

    // Parsing time part (assuming it's in the format "hh:mm A")
    let timeParts = timePart.split(":");
    let hour = parseInt(timeParts[0]);
    let minute = parseInt(timeParts[1].split(" ")[0]);
    let meridian = timeParts[1].split(" ")[1];
    if (meridian === "PM" && hour < 12) {
        hour += 12; // Convert to 24-hour format
    }

    // Creating the Date object
    let createdTime = new Date(year, month, day, hour, minute);

    // Adding 5 hours
    createdTime.setHours(createdTime.getHours() + 5);

    // Formatting the time
    let formattedTime = createdTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

    // Formatting the date
    let formattedDate = createdTime.toLocaleDateString([], {year: 'numeric', month: 'short', day: 'numeric'});

    return {
        date: formattedDate,
        time: formattedTime
    };
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
                url: '/staff/send-message-to-user',
                headers: {
                    Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                    Accept: 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        getMsgs('<?= $task_id ?>')
                        $('#messageTextarea').val('');
                        // var username = $('#sendToUsername').val();
                        // window.location.reload();
                        setTimeout(function() {
                            $('#successMsg').html('');
                        }, 2000);
                    }
                }
            });
        }


        let spacesRemoved = false; // Flag to track if spaces have been removed

function removeSpaces(text) {
    return text.replace(/\s+/g, '');
}

function capitalizeSentences(text) {
    if (text.length === 0) {
        return text;
    }

    // Split the text into sentences
    var sentences = text.split(/([.!?]+\s*)/);

    // Capitalize the first letter of each sentence
    var capitalizedSentences = sentences.map(function(sentence) {
        // If the sentence is not empty, capitalize the first letter
        if (sentence.trim().length > 0) {
            return sentence.charAt(0).toUpperCase() + sentence.slice(1);
        }

        return sentence;
    });

    // Join the sentences back together with the appropriate punctuation
    return capitalizedSentences.join('');
}



function processText(textarea) {
    if (!spacesRemoved) {
        // Remove spaces only if they haven't been removed yet
        textarea.value = removeSpaces(textarea.value);
        spacesRemoved = true; // Update flag
    }

    // Enforce capitalization rule
    textarea.value = capitalizeSentences(textarea.value);
}

document.getElementById('messageTextarea').addEventListener('keydown', function(event) {
    if (event.shiftKey && event.keyCode === 13) {
        event.preventDefault();
        var cursorPos = this.selectionStart;
        var textBefore = this.value.substring(0, cursorPos);
        var textAfter = this.value.substring(cursorPos, this.value.length);
        this.value = textBefore + '\n' + textAfter;
        this.selectionStart = cursorPos + 1;
        this.selectionEnd = cursorPos + 1;
    } else if (event.keyCode === 13) {
        event.preventDefault();
        sendMsgBtn();
    }
});

    </script>
@endsection
