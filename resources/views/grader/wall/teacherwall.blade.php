<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
@extends('layouts.app',['pageTitle' => @$course[0]->course_cd_name])
@section('content')



<style>
.text-muted{
    padding: 5px 10px;
    color: #0b172a !important;
    font-size: medium !important;
}
.media img {
    border-radius: 50%;
    margin-left: 10px;
}
.main-text-block{
    border-bottom: solid 0.3px #eff0f6;
}
.time-view{
    font-size: small;
}
.card-block .media{
    margin-top: 0px !important;
    padding: 10px 10px;
}
</style>




<div class="row">
<div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
    <div class="card">
        <form id="message-form" onsubmit="sendMessage2();" action="javascript:void(0);">
            <div class="card-header">
                
                <h4 class="card-title"><?= @$course[0]->course_cd_name ?> | Course Discussion</h4>
                
                
                <div class="card-options">
                    <a href="<?=url('/course/chat/'.$coID)?>">D Chat</a>
                </div>
            </div>


            <div class="card-body">
                <input type="hidden" value="<?= request()->get('co_id') ?>" id="d_co_id" name="co_id">
                <input type="hidden" value="" id="message_to_input" name="message_to">
                <div class="post-new-contain row card-block">
                    <div class="media-left media-middle friend-box">
                        <img src="<?= asset('/images/user-profile/user-circle.png') ?>" class="media-object img-circle m-r-20" alt="">
                        <!-- <img src="<?= asset('/images/users/1.jpg') ?>" class="media-object img-circle m-r-20" alt=""> -->
                    </div>
                    <div class="col-md-11 col-xs-9">
                        <textarea id="msg-input2" class="form-control post-input" name="discussion" rows="3" cols="10" required="" placeholder="Write something....."></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <span><a href="javascript:void(0);" onclick="sendMessage2();" id="post-new" class="btn btn-primary waves-effect waves-light f-right btn-lg btn-block" style="display: none;">Post</a></span>
            </div>


        </form>
    </div>
</div>
</div>



<!------------------------------------- ----------------------------------------- ------------------------------------->


<div class="row">
<div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
    <div class="card">
        <form id="message-form" onsubmit="sendMessage2();" action="javascript:void(0);">
            <div class="card-header">
                <h4 class="card-title">Others</h4>
            </div>

            <div class="card-footer" style="padding: 0rem 1.5rem; border-bottom: 1px solid #eff0f6;" >
                <div class="form-group">
                    <label for="message-to">Message To:</label>
                    <select id="message-to" class="form-control" name="" onchange="getMessages();">
                        <option value="">All Class</option>
                        <?php foreach ($courseStudent as $student) : ?>
                            <option value="<?= $student->sc_sai_enrollment_no ?>"><?= $student->sc_sai_enrollment_no ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <div class="card-body">
                <input type="hidden" value="<?= request()->get('co_id') ?>" id="d_co_id" name="co_id">
                <input type="hidden" value="" id="message_to_input" name="message_to">
                <div class="post-new-contain card-block">
                    <div id="node_listing"></div>
                </div>
            </div>

      


        </form>
    </div>
</div>
</div>


<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->



<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<div class="modal fade" id="reply-message" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-b-0">
                <form id="reply-form">
                    <input type="hidden" value="<?= request()->get('co_id') ?>" name="co_id">
                    <input type="hidden" value="" name="pid" id="pid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div>
                                <label class="form-control-label">Message</label>
                                <textarea id="reply-message-input" style="border:solid 0.3px #eff0f6" class="form-control post-input" name="discussion" rows="3" cols="10" required="" placeholder="Write something....."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="reply-send-btn" onclick="sendReplyMessageToServer();">Send</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->







@stop

@section('javascript')
<script>
    // message-form

    function sendMessageToRemote(postData) {
        $('#post-new').html('Sending...');
        $('#reply-send-btn').html('Sending...');

        $.ajax({
            method: "POST",
            url: '<?= url('api/teacher-insert-message') ?>',
            data: postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer <?=auth()->user()->api_token?>',
                Accept: 'application/json'
            }
        }).done(function(res) {
            $('#post-new').html('Post');
            $('#reply-send-btn').html('Send');
            $('#reply-message').modal('hide');
            console.log('re', res);
            if (res.status) {
                $('#msg-input2').val('');
                getMessages();
            }
        }).fail(function(res) {
            $('#post-new').html('Post');
            console.log(res);
        });
    }


    function sendMessage2() {

        var msg = $('#msg-input2').val();
        if (msg.trim() == '') {
            return false;
        }
        var bi_form = document.querySelector('#message-form');
        var postData = new FormData(bi_form);
        sendMessageToRemote(postData);
    }


    function openReplyPopup(pid) {
        $('#pid').val(pid);
        $('#reply-message').modal('show');
    }


    function sendReplyMessageToServer() {
        var msg = $('#reply-message-input').val();
        if (msg.trim() == '') {
            return false;
        }
        var bi_form = document.querySelector('#reply-form');
        var postData = new FormData(bi_form);
        sendMessageToRemote(postData);
    }







    function getMessages() {
        var message_to = $('#message-to').val();
        var co_id = $('#d_co_id').val();
        $('#message_to_input').val(message_to);
        $.ajax({
            method: "GET",
            url: '<?= url('get-messages') ?>?co_id=' + co_id + '&message_to=' + message_to,
            //   data : {co_id : co_id},
            processData: false,
            contentType: false,
            headers: {
                // Authorization: 'Bearer wWkIwy57WAUOZBkQnB09IpvljtRTsCjWNSyyhX2MqAZ42VL2yFioSlsYgUZ8ITZsJ0xVFTUofGlqZhTS',
                Accept: 'application/json'
            }
        }).done(function(res) {
            console.log('re', res);
            createListing(res.data);
        }).fail(function(res) {
            console.log(res);
        });

    }



    function createListing(data) {
        var html = '';


        $(data).each(function(index, val) {

            var name = '';

            var msgText = val.message.replace(/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/g, '<a href="$1">$1</a>');

            if (val.sender_name) {
                name = val.sender_name;
            } else {
                name = val.entry_user_id;
            }
            //    html +='<hr>';
            html += '<div class="bg-white p-relative" style="border:solid 0.3px #eff0f6; margin-top:3px;" > ';
            html += '<div class="card-block" style="padding-bottom:0px;border-bottom:solid 0.3px #eff0f6;">';

            html += '<div class="media" > ';
            html += '<div class="media-left media-middle friend-box">'; 
            html += '<a href="#"><img class="media-object img-circle m-r-20" src="<?= asset('staffimages/'.$sess->emp_user_id.'.jpg') ?>" alt=""></a>';
            html += '</div>';
            html += '<div class="media-body" >';

            html += '<div class="chat-header"><b>' + name + '</b></div>';
            html += '<div class="f-13 time-view">' + val.time_ago + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';


            html += '<div class="card-block main-text-block">';
            html += '<div class="timeline-details">';
            html += '<p class="text-muted">' + msgText + '</p>';
            html += '</div>';
            html += '</div>';
            // ah-----------------------------------------------------
            // console.log('array length',val.reply);
            var replyArr = val.reply; /// Array.from(val.reply);

            if ((replyArr) && (replyArr.length > 0)) {



                html += '<div class="card-block b-b-theme b-t-theme social-msg">';
                $(replyArr).each(function(m_index, child_reply) {
                    var msgText = child_reply.message.replace(/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/g, '<a href="$1">$1</a>');
                    html += '<div class="media" style="margin:20px; border-bottom: 1px solid #eff0f6; " >';
                    html += '<div class="media-left media-middle friend-box">';
                    html += '<a href="#"><img class="media-object img-circle m-r-20" src="<?= asset('/images/users/1.jpg') ?>" alt=""></a>';
                    html += '</div>';
                    html += '<div class="media-body">';

                    html += '<div class="chat-header"><b>' + child_reply.sender_name + '</b></div>';
                    html += '<div style=""> <div class="f-13 time-view">' + val.time_ago + '</div> <div class="f-13 text-muted">' + msgText + '</div> </div>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
            }

            html += '<div class="card-block user-box">';
          //  html += '<div class="p-b-20">';
            html += '<button onclick="openReplyPopup(' + val.id + ')" class="btn btn-primary waves-effect waves-light f-right btn-lg btn-block">Reply </button>';
          //  html += '</div>';
            html += '</div>';
            // ah----------------------------------------------------            
            html += '<div style="margin-bottom:15px;"></div>';
            html += '</div>';
        });

        // }else{
        //     html = '<div><p style="text-align:center;">No Messages</p></div>';
        // }

        if (html == '') {
            html = '<div><p style="text-align:center;">No Messages</p></div>';
        }


        $('#node_listing').html(html);
    }



    $(document).ready(function() {
        getMessages();
        setTimeout(() => {
            getMessages();
        }, 30000);
        $('#post-new').show();
    });
</script>
@stop