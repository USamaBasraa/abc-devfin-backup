@extends('layouts.app',['pageTitle' => 'Course Chat'])
@section('content')
{{-- @dd("123"); --}}
<?php
$eCoID = my_simple_crypt($coID);
$mobileURL='https://api.bnu.edu.pk';
?>
<style>
    .message-feed {
     
    }
    .message-feed.right .mf-content{
        background: #579ffb !important;
       color: #10163a !important;
    color : #ffffff !important ;

    font-family: Helvetica, sans-serif;
    }





















    
</style>
<style>
/* =========================================================
   COURSE CHAT - UI ONLY
   Primary: #17675F
   ========================================================= */

:root {
    --chat-primary: #17675F;
    --chat-primary-dark: #105149;
    --chat-primary-light: #E8F2F0;

    --chat-bg: #F5F7F8;
    --chat-white: #FFFFFF;
    --chat-border: #E4E9ED;

    --chat-text: #27333A;
    --chat-muted: #89969E;

    --chat-radius: 16px;
}


/* =========================================================
   MAIN CHAT CONTAINER
   ========================================================= */

.bootstrap.snippet.card {
    border: 0 !important;
    border-radius: var(--chat-radius) !important;
    background: var(--chat-white) !important;
    box-shadow: 0 6px 24px rgba(23, 103, 95, 0.08) !important;
    overflow: hidden !important;
}

.tile.tile-alt {
    background: var(--chat-white) !important;
    border: none !important;
    border-radius: var(--chat-radius) !important;
    overflow: hidden !important;
}

.ms-body {
    width: 100% !important;
    background: var(--chat-white) !important;
    border-radius: var(--chat-radius) !important;
    overflow: hidden !important;
}


/* =========================================================
   CHAT HEADER
   ========================================================= */

.action-header {
    min-height: 82px !important;
    padding: 16px 24px !important;

    display: flex !important;
    align-items: center !important;

    background: var(--chat-white) !important;
    border-bottom: 1px solid var(--chat-border) !important;

    position: relative !important;
    z-index: 5 !important;
}


/* menu icon */
#ms-menu-trigger {
    width: 42px !important;
    height: 42px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin-right: 12px !important;

    border-radius: 12px !important;

    background: var(--chat-primary-light) !important;
    color: var(--chat-primary) !important;

    cursor: pointer !important;

    transition: all 0.2s ease !important;
}

#ms-menu-trigger:hover {
    background: var(--chat-primary) !important;
    color: var(--chat-white) !important;
}

#ms-menu-trigger i {
    font-size: 19px !important;
    color: inherit !important;
}


/* course area */
.action-header .chat-user {
    display: flex !important;
    align-items: center !important;

    margin-left: 0 !important;
}


/* course image */
.action-header .chat-user img {
    width: 48px !important;
    height: 48px !important;

    object-fit: cover !important;

    border-radius: 50% !important;

    margin-right: 13px !important;

    border: 2px solid var(--chat-primary-light) !important;
    padding: 2px !important;

    background: var(--chat-white) !important;
}


/* course name */
#chat_person_name {
    display: block !important;

    font-size: 16px !important;
    font-weight: 700 !important;

    line-height: 1.4 !important;

    color: var(--chat-text) !important;
}


/* remove unnecessary top spacing */
.action-header .chat-user .mt-2 {
    margin-top: 0 !important;
}


/* =========================================================
   CHAT BODY
   ========================================================= */

#ChatBody {
    position: relative !important;

    padding: 25px 28px !important;

    background-color: #F7FAF9 !important;

    min-height: 400px !important;

    overflow-y: auto !important;
    overflow-x: hidden !important;

    scrollbar-width: thin !important;
    scrollbar-color: #B8CBC7 transparent !important;
}


/* subtle chat pattern using CSS only */
#ChatBody {
    background-image:
        radial-gradient(
            circle at 20px 20px,
            rgba(23, 103, 95, 0.025) 2px,
            transparent 2px
        ) !important;

    background-size: 35px 35px !important;
}


#ChatBody::-webkit-scrollbar {
    width: 6px !important;
}

#ChatBody::-webkit-scrollbar-track {
    background: transparent !important;
}

#ChatBody::-webkit-scrollbar-thumb {
    background: #B9CBC8 !important;
    border-radius: 20px !important;
}

#ChatBody::-webkit-scrollbar-thumb:hover {
    background: var(--chat-primary) !important;
}


/* =========================================================
   EMPTY STATE
   ========================================================= */

#ChatBody > .message-feed.mt-0 {
    min-height: 300px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    color: var(--chat-muted) !important;
}

#ChatBody > .message-feed.mt-0 b {
    font-size: 15px !important;
    font-weight: 600 !important;

    padding: 10px 18px !important;

    border-radius: 30px !important;

    color: var(--chat-primary) !important;
    background: var(--chat-primary-light) !important;
}


/* =========================================================
   MESSAGE COMMON
   ========================================================= */

.message-feed {
    width: 100% !important;
    margin-bottom: 18px !important;
}

.message-feed.media {
    align-items: flex-end !important;
}

.message-feed .media-body {
    max-width: 72% !important;
}


/* bubble common */
.message-feed .mf-content {
    width: fit-content !important;
    max-width: 100% !important;

    padding: 12px 15px !important;

    border-radius: 15px !important;

    font-family:
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        Helvetica,
        Arial,
        sans-serif !important;

    font-size: 14px !important;

    line-height: 1.55 !important;

    word-break: break-word !important;

    box-shadow: 0 2px 8px rgba(30, 55, 53, 0.06) !important;
}


/* =========================================================
   OTHER USER MESSAGE
   ========================================================= */

.message-feed:not(.right) .mf-content,
.message-feed .msg-reply {
    background: var(--chat-white) !important;

    color: var(--chat-text) !important;

    border: 1px solid var(--chat-border) !important;

    border-radius: 5px 15px 15px 15px !important;
}

.message-feed:not(.right) .mf-content b {
    display: inline-block !important;

    margin-bottom: 4px !important;

    color: var(--chat-primary) !important;

    font-size: 13px !important;
    font-weight: 700 !important;
}


/* left user profile image */
.message-feed .img-chat-profile {
    width: 42px !important;
    height: 42px !important;

    object-fit: cover !important;

    border-radius: 50% !important;

    border: 2px solid var(--chat-white) !important;

    box-shadow: 0 2px 7px rgba(0, 0, 0, 0.08) !important;
}


/* =========================================================
   MY MESSAGE - RIGHT SIDE
   ========================================================= */

.message-feed.right {
    display: flex !important;
    flex-direction: row-reverse !important;

    justify-content: flex-start !important;

    align-items: flex-end !important;
}

.message-feed.right .float-right {
    float: none !important;

    padding-left: 10px !important;
}

.message-feed.right .float-right img {
    width: 42px !important;
    height: 42px !important;

    object-fit: cover !important;

    border-radius: 50% !important;

    border: 2px solid var(--chat-white) !important;

    box-shadow: 0 2px 7px rgba(0, 0, 0, 0.08) !important;
}

.message-feed.right .media-body {
    flex: none !important;

    width: auto !important;
    max-width: 72% !important;

    text-align: right !important;
}

.message-feed.right .mf-content {
    margin-left: auto !important;

    text-align: left !important;

    background: var(--chat-primary) !important;

    color: var(--chat-white) !important;

    border: none !important;

    border-radius: 15px 5px 15px 15px !important;
}

.message-feed.right .mf-content b {
    display: block !important;

    color: var(--chat-white) !important;

    font-size: 13px !important;
    font-weight: 700 !important;

    margin-bottom: 3px !important;
}


/* message time */
.message-feed.right .mf-content small {
    color: rgba(255, 255, 255, 0.75) !important;

    font-size: 10px !important;

    font-weight: 400 !important;

    margin-left: 20px !important;
}


/* =========================================================
   LEFT MESSAGE TIME / EDIT
   ========================================================= */

.message-feed .seen {
    margin-top: 5px !important;

    padding-left: 4px !important;

    color: var(--chat-muted) !important;

    font-size: 11px !important;
}

.message-feed .seen p {
    margin: 0 !important;

    font-size: 11px !important;

    color: var(--chat-muted) !important;
}

.message-feed .seen a {
    color: var(--chat-primary) !important;

    font-size: 11px !important;

    font-weight: 600 !important;
}


/* =========================================================
   REMOVE OLD MESSAGE DIVIDER LOOK
   ========================================================= */

#ChatBody .card-block {
    padding: 0 !important;

    border-bottom: none !important;

    height: 0 !important;
}


/* private tag */
#ChatBody > p {
    width: fit-content !important;

    margin: 8px auto !important;

    padding: 5px 12px !important;

    border-radius: 30px !important;

    background: #FFF1F0 !important;

    color: #C85650 !important;

    font-size: 11px !important;

    font-weight: 600 !important;
}


/* =========================================================
   MESSAGE COMPOSER
   ========================================================= */

#messageForm {
    background: var(--chat-white) !important;

    border-top: 1px solid var(--chat-border) !important;

    padding: 14px 18px !important;
}

#messageForm > .row {
    margin: 0 !important;

    display: flex !important;
    align-items: center !important;

    flex-wrap: nowrap !important;

    gap: 12px !important;
}


/* textarea column */
#messageForm .col-10,
#messageForm .col-lg-10 {
    flex: 1 1 auto !important;

    max-width: none !important;

    padding: 0 !important;
}


/* button column */
#messageForm .col-2,
#messageForm .col-lg-2 {
    flex: 0 0 120px !important;

    max-width: 120px !important;

    padding: 0 !important;
}


/* textarea */
#msg-input {
    width: 100% !important;

    min-height: 48px !important;
    height: 48px !important;

    max-height: 120px !important;

    padding: 12px 16px !important;

    resize: none !important;

    border: 1px solid #D9E3E1 !important;

    border-radius: 12px !important;

    outline: none !important;

    background: #F8FAFA !important;

    color: var(--chat-text) !important;

    font-size: 14px !important;

    line-height: 22px !important;

    box-shadow: none !important;

    transition: all 0.2s ease !important;
}

#msg-input::placeholder {
    color: #A2ADB3 !important;
}

#msg-input:focus {
    background: var(--chat-white) !important;

    border-color: var(--chat-primary) !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   SEND BUTTON
   ========================================================= */

.send-msg-btn {
    width: 100% !important;

    height: 48px !important;

    margin: 0 !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    border: none !important;

    border-radius: 12px !important;

    background: var(--chat-primary) !important;

    color: var(--chat-white) !important;

    font-size: 14px !important;

    font-weight: 600 !important;

    box-shadow:
        0 4px 12px rgba(23, 103, 95, 0.22) !important;

    transition: all 0.2s ease !important;
}

.send-msg-btn:hover,
.send-msg-btn:focus {
    background: var(--chat-primary-dark) !important;

    color: var(--chat-white) !important;

    transform: translateY(-1px) !important;

    box-shadow:
        0 6px 16px rgba(23, 103, 95, 0.28) !important;
}

.send-msg-btn:active {
    transform: translateY(0) !important;
}


/* unused dynamic input container */
.input_container {
    display: none !important;
}


/* =========================================================
   LINKS INSIDE MESSAGE
   ========================================================= */

.mf-content a {
    text-decoration: underline !important;
}

.message-feed.right .mf-content a {
    color: #FFFFFF !important;
}

.message-feed:not(.right) .mf-content a {
    color: var(--chat-primary) !important;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 768px) {

    .bootstrap.snippet.card,
    .tile.tile-alt,
    .ms-body {
        border-radius: 12px !important;
    }

    .action-header {
        min-height: 70px !important;

        padding: 12px 14px !important;
    }

    #ms-menu-trigger {
        width: 38px !important;
        height: 38px !important;

        margin-right: 8px !important;
    }

    .action-header .chat-user img {
        width: 42px !important;
        height: 42px !important;
    }

    #chat_person_name {
        font-size: 14px !important;
    }

    #ChatBody {
        padding: 18px 12px !important;
    }

    .message-feed .media-body,
    .message-feed.right .media-body {
        max-width: 82% !important;
    }

    .message-feed .img-chat-profile,
    .message-feed.right .float-right img {
        width: 36px !important;
        height: 36px !important;
    }

    #messageForm {
        padding: 10px !important;
    }

    #messageForm > .row {
        gap: 8px !important;
    }

    #messageForm .col-2,
    #messageForm .col-lg-2 {
        flex: 0 0 85px !important;

        max-width: 85px !important;
    }

    #msg-input {
        min-height: 44px !important;
        height: 44px !important;

        padding: 10px 13px !important;
    }

    .send-msg-btn {
        height: 44px !important;

        padding: 0 14px !important;
    }
}
</style>
<style>
    /* =========================================
   HEADER - KEEP ONLY HAMBURGER + COURSE NAME
   ========================================= */

.action-header {
    min-height: 74px !important;
    padding: 14px 20px !important;

    display: flex !important;
    align-items: center !important;

    background: #ffffff !important;
    border-bottom: 1px solid #e4e9ed !important;
}


/* hamburger */
#ms-menu-trigger {
    width: 42px !important;
    height: 42px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin-right: 14px !important;

    border-radius: 12px !important;

    background: #e8f2f0 !important;
    color: #17675F !important;

    transition: 0.2s ease !important;
}

#ms-menu-trigger:hover {
    background: #17675F !important;
    color: #ffffff !important;
}

#ms-menu-trigger i {
    font-size: 21px !important;
    color: inherit !important;
}


/* hide course/avatar image only */
.action-header .chat-user img {
    display: none !important;
}


/* course title */
.action-header .chat-user {
    display: flex !important;
    align-items: center !important;

    margin-left: 0 !important;
}

.action-header .chat-user .mt-2 {
    margin-top: 0 !important;
}

#chat_person_name {
    font-size: 17px !important;
    font-weight: 700 !important;

    color: #27333A !important;

    line-height: 1.3 !important;
}


/* remove unnecessary right actions spacing */
.action-header .ah-actions {
    margin: 0 !important;
    padding: 0 !important;
}


/* mobile */
@media (max-width: 768px) {

    .action-header {
        min-height: 64px !important;
        padding: 10px 12px !important;
    }

    #ms-menu-trigger {
        width: 38px !important;
        height: 38px !important;

        margin-right: 10px !important;
    }

    #ms-menu-trigger i {
        font-size: 19px !important;
    }

    #chat_person_name {
        font-size: 14px !important;
    }
}
</style>


<style>
    /* =====================================================
   FINAL CHAT HEADER FIX
   Hamburger + Course Title
   ===================================================== */

.action-header {
    min-height: 76px !important;
    height: auto !important;

    padding: 14px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;

    gap: 14px !important;

    position: relative !important;

    background: #ffffff !important;
    border-bottom: 1px solid #e5ecea !important;

    overflow: visible !important;
}


/* =====================================================
   HAMBURGER LINK WRAPPER
   ===================================================== */

.action-header > a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    position: static !important;

    width: auto !important;
    height: auto !important;

    margin: 0 !important;
    padding: 0 !important;

    flex: 0 0 auto !important;

    text-decoration: none !important;
}


/* =====================================================
   HAMBURGER BUTTON
   ===================================================== */

#ms-menu-trigger {
    width: 44px !important;
    height: 44px !important;

    min-width: 44px !important;
    min-height: 44px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    position: static !important;

    border-radius: 12px !important;

    background: #e8f2f0 !important;
    color: #17675F !important;

    cursor: pointer !important;

    flex-shrink: 0 !important;

    transition:
        background-color 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease !important;
}


#ms-menu-trigger i {
    margin: 0 !important;
    padding: 0 !important;

    position: static !important;

    font-size: 22px !important;
    line-height: 1 !important;

    color: #17675F !important;
}


#ms-menu-trigger:hover {
    background: #17675F !important;
    color: #ffffff !important;
}


#ms-menu-trigger:hover i {
    color: #ffffff !important;
}


/* =====================================================
   COURSE TITLE CONTAINER
   ===================================================== */

.action-header .chat-user {
    display: flex !important;
    align-items: center !important;

    position: static !important;

    float: none !important;

    flex: 1 1 auto !important;
    min-width: 0 !important;

    margin: 0 !important;
    padding: 0 !important;

    height: auto !important;
}


/* hide old avatar completely */
.action-header .chat-user img {
    display: none !important;
}


/* remove Bootstrap spacing from title wrapper */
.action-header .chat-user > div {
    display: flex !important;
    align-items: center !important;

    margin: 0 !important;
    padding: 0 !important;

    min-width: 0 !important;
}


/* specifically reset existing mt-2 */
.action-header .chat-user .mt-2 {
    margin-top: 0 !important;
}


/* =====================================================
   COURSE TITLE
   ===================================================== */

#chat_person_name {
    display: block !important;

    margin: 0 !important;
    padding: 0 !important;

    position: static !important;

    font-size: 18px !important;
    font-weight: 700 !important;

    line-height: 1.35 !important;

    color: #27333A !important;

    white-space: normal !important;
    overflow: visible !important;
    text-overflow: initial !important;

    max-width: 100% !important;
}


/* =====================================================
   REMOVE EMPTY RIGHT ACTIONS EFFECT
   ===================================================== */

.action-header .ah-actions {
    display: none !important;
}


/* =====================================================
   MOBILE
   ===================================================== */

@media (max-width: 768px) {

    .action-header {
        min-height: 66px !important;

        padding: 10px 12px !important;

        gap: 10px !important;
    }


    #ms-menu-trigger {
        width: 40px !important;
        height: 40px !important;

        min-width: 40px !important;
        min-height: 40px !important;

        border-radius: 10px !important;
    }


    #ms-menu-trigger i {
        font-size: 20px !important;
    }


    #chat_person_name {
        font-size: 15px !important;
        line-height: 1.3 !important;
    }
}
</style>
<!-- Row -->
<div class="row">
    <div class="col-md-12">
        <div class="bootstrap snippet card overflow-hidden bg-transparent">
            <div class="tile tile-alt mb-0" style="width: 100%;" id="messages-mai1n">
                <!-- <div class="ms-menu">
                    <div class="tab-menu-heading border-top-0">
                    <h4 class="font-weight-bold" style="margin-top:0px;"  >Participants</h4>
                       
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-7">
                            <ul class="list-group lg-alt chat-conatct-list" id="ChatList">
                            </ul>
                               
                        </div>
                    </div>
                </div> -->
                <div class="ms-body">
                    <div class="action-header clearfix">
                          <a href="<?=URL::to('/course/notification/'.$coID);?>">
							<div class="" id="ms-menu-trigger" >
								<i class="fa fa-bars"></i>
							</div>
                        </a>
                        <div class="float-left hidden-xs d-flex ml-6 chat-user">
                            <img src="../../assets/images/users/16.jpg" alt="" class="avatar avatar-md brround mr-2">
                            <div class="align-items-center mt-2">
                                <span class="font-weight-bold" id="chat_person_name" ><?=$course_name?></span>
                            </div>
                        </div>
                        <ul class="ah-actions actions align-items-center">

                            <!-- <li class="dropdown">
                                <a data-toggle="dropdown" class="option-dots" href="#">
                                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                    </svg>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="">Refresh</a>
                                    </li>
                                    <li>
                                        <a href="">Message Settings</a>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                    </div>
                    <div class="chat-body-style ps ps--active-y" style="min-height: 350px; overflow-y: scroll !important; " id="ChatBody">
                        <div class="message-feed media mt-0">
                            <b>Loading...</b>
                        </div>
                    </div>
                    <!-- <div class="msb-reply"> -->
                    <div>
                    <form action="javascript:void(0);" onsubmit="this.preventDefault; return" method="post" id="messageForm">
                            <input type="hidden" value="" id="endPoint">
                            <input type="hidden" value="<?= $coID ?>" name="course_co_id" id="co_id" >
                            <input type="hidden" value="all" name="viewFor" id="viewFor" >
                            <input type="hidden" value="" name="to_user_id" id="to_user_id">

                            <div class="row  "  >

                                <div class="col-10 col-lg-10">
                                <textarea placeholder="What's on your mind..." class="form-control input-value" name="message" id="msg-input"  ></textarea>
                                </div>
                                <div class="col-2 col-lg-2">
                                <button class="btn btn-primary send-msg-btn" style="margin-top: 12px; width: 80%; margin-right: 12px;" onclick="sendMessage();" type="button">Send</button>
                                </div>
                                <div class="input_container" style="width: 100%;" ></div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row-->






<!-- Sign in modal reply start -->
<div class="modal fade" id="reply-message-modal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-b-0">

                <form action="javascript:void(0);" onsubmit="this.preventDefault; return" id="reply-message-form" method="post">
                    <input type="hidden" name="reply-mesg-tt_id" value="" id="reply-mesg-tt_id">
                    <input type="text" name="reply-to-user-id" value="" id="reply-to-user-id">
                    <input type="hidden" name="reply-msg-id" value="" id="reply-msg-id">
                    <input type="hidden" value="<?= $eCoID ?>" name="co_id" id="reply_co_id">



                    <div class="row">
                        <div class="col-sm-12">
                            <div>
                                <!-- <label class="form-control-label">Message</label> -->
                                <input type="text" name="message" id="reply-message" class="form-control input-value" placeholder="Type a Message">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <p id="error-p"></p><br>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="replyMessage();">Send</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Sign in modal reply end -->





<!-- Sign in modal start -->
<div class="modal fade" id="edit-message-modal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-b-0">

                <form action="javascript:void(0);" onsubmit="this.preventDefault; return" id="edit-message-form" method="post">

                    <input type="hidden" name="msg-id" value="" id="edit-msg-id">
                    <input type="hidden" value="" id="----====">

                    <div class="row">
                        <div class="col-sm-12">
                            <div>
                                <!-- <label class="form-control-label">Message</label> -->
                                <input type="text" name="message" id="edit-message" class="form-control input-value" placeholder="Type a Message">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <p id="error-p"></p><br>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="updateMessage();">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Sign in modal end -->







@stop
@section('javascript')
<script type="text/javascript">
var getMessageurl='';

function readMessages(unID,coID,userName) {

        var formData    = new FormData();
        formData.append("un_id",unID);
        formData.append("co_id",coID);
        formData.append("reg_no",userName);

        $.ajax({
            type:'POST',
            url: '<?=$mobileURL;?>/updateSeen',
            data:formData,
            headers: {
                Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                Accept: 'application/json'
                },
            processData:false,
            contentType:false,
            success:function(res){
                $('#msg-input').val('');
            },    
            error: function (jqXHR, exception) {
            

        },                            
        });
}

$(document).ready(function() {
        var co_id = $('#co_id').val();
    //    console.log('<?=auth()->user()->id;?>');
        var url      = window.location.href; 
        var url = new URL(url);
        var reg = url.searchParams.get("reg");
        var topic = url.searchParams.get("topic");

        

       // https://api.bnu.edu.pk/getMessages?co_id=22064&viewFor=direct&to_user_id=F2019-267
       if(reg==null){
        getMessageurl='?co_id='+co_id+'&mesg_bnu_un_id='+topic;
        readMessages(topic,co_id,'<?=auth()->user()->username;?>');

       }else{
        getMessageurl='?co_id='+co_id+'viewFor=direct&to_user_id='+reg;


       }
       //showStudents();
        $('#back-to-top').hide();

        getMessage();

        setInterval(() => {
            getMessage();
         // checkMessage();
        }, 3000);

      //  alert($(document).height());

      var chatHeight = $(window).height() - 300;

      //alert($('.top-header').length());

       // alert(chatHeight);

      $('#ChatBody').css('max-height',chatHeight);
      $('#ChatBody').css('height',chatHeight);



        $("#msg-input").keypress(function (e) {
            if(e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

    });

    var groupID = '<?=$coID?>';

    $(function() {
        'use strict'
        const ps = new PerfectScrollbar('#ChatList', {
            useBothWheelAxes: false,
            suppressScrollX: false,
        });
        // const ps2 = new PerfectScrollbar('#ChatList2', {
        //     useBothWheelAxes: false,
        //     suppressScrollX: false,
        // });
        const ps1 = new PerfectScrollbar('#ChatBody', {
            useBothWheelAxes: false,
            suppressScrollX: false,
        });

        $('[data-toggle="tooltip"]').tooltip();

    });

    var username = '<?= auth()->user()->username ?>';
    var uID = '<?= auth()->user()->id ?>';
    var messageArr = [];
    var readMessageArr = [];
    var viewFor = 'all';
    var courseName = '<?=$course_name?>';

    function textFormat(text) {
        return text.replace(/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/g, '<a href="$1">$1</a>');
    }

    function createEditLink(rowItem, pIndex, childIndex) {
       // console.log(rowItem.id, rowItem.entry_user_id, username);
        if (uID == rowItem.entry_user_id) {
            return '<a onclick="editMessagePopup(' + pIndex + ',' + childIndex + ')" href="javascript:void(0);">Edit </a>';
        }
        return '';
    }



    function rightSide(row, index) { // my message
        var username = (row.sender_name) ? row.sender_name : row.entry_user_id.toString();
        var html = '<div class="messages"><div class="message-feed right">';
        //   html    += '<div class="message out no-avatar media" style=""><div class="body media-body text-right p-l-50">';
        html += '<div class="float-right pl-2" ><img src="<?= asset('/staffimages/'.auth()->user()->employee_id.'.jpg') ?>" class="rounded-circle"  alt="dp" width="40" height="40" ></a></div>';
        html += '<div class="media-body" style="display:block"  > <div class="mf-content" style="text-align:left" >    <b>' + username.toUpperCase() + '        <small style="margin-left:40px;" class="float-right">' + row.created_at + '</small>  </b>';
        html += '<br>' + textFormat(row.message) + ' <br>   </div></div>';
        html += '</div>';
        html += '</div>';
        return html;
    }

    function leftSide(row, index) {
        //  return '';
        // var username    = (row.sender_name)?row.sender_name.toString():row.entry_user_id.toString();
        if (row.entry_user_id == null) {
            var username = (row.sender_name) ? row.sender_name.toString() : '';

        } else {
            var username = (row.sender_name) ? row.sender_name.toString() : row.entry_user_id.toString();
        }
        var html = '<div class="message-feed media messages otherMessages" style="" data-msgID="' + row.id + '" >';
        var editLink = createEditLink(row, index, null);

        var studentImg = '<?=asset('/')?>studentpics/'+(username.toUpperCase())+'.jpg';

        // console.log('student Img', studentImg);

        html += '<div class="float-left pr-2"> <img src="'+studentImg+'" class="img-circle img-chat-profile" width="70px" height="70px" alt="Me"> </div>';
        html += '<div class="media-body"><div class="mf-content msg-reply">  <b>' + username.toUpperCase() + '</b><br>' + textFormat(row.message) + ' </div><div class="seen"> ' + editLink + ' <i class="icon-clock f-12 m-r-5 txt-muted d-inline-block"></i><span><p class="d-inline-block">' + row.created_at + '</p></span><div class="clear"></div></div></div></div>';

       // var replyArr = row.reply; /// Array.from(val.reply);

        // if ((replyArr) && (replyArr.length > 0)) {
        //     $(replyArr).each(function(m_index, child_reply) {
        //         var username = (child_reply.sender_name) ? child_reply.sender_name : child_reply.entry_user_id.toString();
        //         var childEditLink = createEditLink(child_reply, index, m_index);
        //         html += '<div class="message-feed media messages otherMessages" style="" data-msgID="' + row.id + '" >';
        //         html += '<div style="" class=float-left pr-2"><img src="<?= asset('/images/user-circle.png') ?>" class="img-circle img-chat-profile" alt="Me" width="70px" height="70px"></div>';
        //         html += '<div class="media-body"><div class="mf-content msg-reply"> Reply By : <b>' + username + '</b><br>' + textFormat(child_reply.message) + ' </div><div class="seen"> ' + childEditLink + ' | <i class="icon-clock f-12 m-r-5 txt-muted d-inline-block"></i><span><p class="d-inline-block">' + child_reply.time_ago + '</p></span><div class="clear"></div></div></div>';

        //         html += '</div>'
        //     });
        // }

        html += '</div>';
        return html;
    }






    function reloadMessage() {
        var html = '';
        var index = 0;
        $(messageArr).each(function(index, value) {

            var borderColor = (value.private_comment == 1) ? '#262184' : '#28E1BD';
         
            if (value.private_comment == 1) {
                html += '<p style="background-color: #efd3d3; padding:0px; margin:0px;">[private]</p>';
            }
            if (value.entry_user_id == uID) {
                html += rightSide(value, index);
            } else {
                html += leftSide(value, index);
            }
            //  html += '</div>';
            html += '<div class="card-block" style="padding-bottom:0px; border-bottom:solid 2px ' + borderColor + ';">';
          //   html += '<span> <a onclick="replyMessagePopup(' + index + ')" href="javascript:void(0);">Reply</a></span>';
            html += '</div>';
            // html += '</div>';
            index++;
        });

        if(html == ''){
            html = '<div class="message-feed media mt-0"> <b>No Message Yet!</b> </div>';
        }

        $('#ChatBody').html(html);

    const container = document.querySelector('#ChatBody');
    console.log( '----->>>>> chatbody :: ', container.scrollHeight );
   container.scrollTop = container.scrollHeight;



        readMessage();
    }






    function readMessage() {
        $('.otherMessages').each(function(index, ele) {
            var msgID = $(ele).attr('data-msgID');
            readMessageArr.push(msgID);
        });

        if (readMessageArr.length > 0) {
            var co_id = $('#co_id').val();
            var data = new FormData();
            data.append('messageID', readMessageArr);
            data.append('co_id', co_id);
            // sendPostRequest1('message/read', function(res) {
            //     // console.log('read', res);
            // }, data);
        }
    }

    function getMessage() {
        //var endPoint = $('#endPoint').val();
        // sendGetRequest1(endPoint, function(res) {
        //     if (res.status) {
        //         messageArr = res.data;
        //         reloadMessage();
        //     }
        // });
  
        //alert('rr');
        //var bi_form = document.querySelector('#messageForm');
        //var formData    = new FormData();
        //formData.append("mesg_bnu_un_id",469587);
       // formData.append("co_id", 22064);
        $.ajax({
            type:'post',
            url: '<?=$mobileURL;?>/getMessages'+getMessageurl,
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
           // data:formData,
            processData:false,
            contentType:false,
            success:function(res){
                messageArr = res.data;
                reloadMessage();
                    
            },
            error: function (jqXHR, exception) {
                

            },               
        });



    }


    function checkMessage(){
        // var endPoint = $('#endPoint').val();
        // var lastMsg  = 0;
        // if(messageArr.length > 0){
        //     var msg = messageArr[(messageArr.length)-1];
        //     lastMsg = msg.id;
        // }
        
        // var point = endPoint.replace('get','get-by-last-message-id');
        $.ajax({
            type:'POST',
            url: '<?=$mobileURL;?>/getMessages?mesg_bnu_un_id=468402&co_id=22064',
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            processData:false,
            contentType:false,
            success:function(res){
            if (res.status) {
                console.log('Before Count :: ', messageArr.length );
                messageArr.concat(res.data);


                $(res.data).each(function(index,obj){
                    messageArr.push(obj);
                });


                console.log('Before Count :: ', messageArr.length );
                //reloadMessage();
            }
                    
            },
            error: function (jqXHR, exception) {
                

            },               
        });
    }





    // var objectType = '';

    function setChat(object='course',objectID,studentRollNo){
        $('.chat_objects').removeClass('active');
        $('#chat_obj_'+objectID).addClass('active');
        var endPoint = '';
        if(object == 'course'){
            endPoint = 'message/get?co_id='+objectID;
            $('#chat_person_name').html(courseName);
            $('#viewFor').val('all');
            $('#to_user_id').val('');
            $('#endPoint').val(endPoint);
        }else if(object == 'student'){
            var co_id = $('#co_id').val();
            $('#chat_person_name').html(studentRollNo);
            $('#to_user_id').val(objectID);
            $('#viewFor').val('direct');
            endPoint = 'message/get?co_id='+co_id+'&to_user_id='+objectID+'&reg_id='+studentRollNo;
            $('#endPoint').val(endPoint);
        }
        getMessage();
    }




    function sendMessage() {
        var msg = $('#msg-input').val();
        if (msg.trim() == '') {
            return false;
        }

        var msg=$('#msg-input').val();

        var bi_form = document.querySelector('#messageForm');
        var formData    = new FormData(bi_form);
        formData.append("mesg_bnu_un_id",'<?=$unID;?>');
        formData.append("entry_user_id", '<?=auth()->user()->id;?>');
        messageArr.push({
            'co_id' : groupID,
            'time_ago' : 'sending',
            'message' : $('#msg-input').val(),
            'sender_name' : username,
            'entry_user_id':uID,
            'to_user_id' : $('#to_user_id').val()
        });

        reloadMessage();
        $('#msg-input').val('');
        //'message/send'
        $.ajax({
            type:'POST',
            url: '<?=$mobileURL;?>/send',
            data:formData,
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            processData:false,
            contentType:false,
            success:function(res){
            // alert('bilal')
            messageArr.pop();
            messageArr.push(res.data);
                    
            },
            error: function (jqXHR, exception) {
                

            },               
        });
       var collection_id='<?=$coID;?><?=$unID;?>';
        $.ajax({
            type:'GET',
            url: '<?=$mobileURL;?>/create_chat?sender_name='+username+'&message='+msg+'&collection_id='+collection_id,
            //data:formData,
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            processData:false,
            contentType:false,
            success:function(res){
           
                    
            },
            error: function (jqXHR, exception) {
                

            },               
        });
     
    }


</script>
@stop
