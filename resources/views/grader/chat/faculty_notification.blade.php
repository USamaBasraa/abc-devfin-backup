@extends('layouts.app',['pageTitle' => 'Course Chat'])
@section('content')

{{-- @dd("test"); --}}
<?php
$eCoID = my_simple_crypt($coID);
//echo $coID;
$mobileURL='https://api.bnu.edu.pk';
?>
<style>
   
    .message-feed.right .mf-content{
        background: #579ffb !important;
       color: #10163a !important;
      color : #ffffff !important ;
      width:90% !important;

    font-family: Helvetica, sans-serif;
    }
</style>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">

            <div class="bootstrap snippet card overflow-hidden bg-transparent">
            <ul class="nav nav-pills mb-2" id="pills-tab" role="tablist">
            <li class="nav-item" >
                <a class="nav-link active" style="min-width:35rem;min-height:2rem" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Group Chat</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="min-width:35rem;min-height:2rem"  id="pills-profile-tab" onclick="getStudent()" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Student Chat</a>
            </li>
         
            </ul>
            <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="chat-body-style ps ps--active-y" style="min-height: 350px; overflow-y: scroll !important; " id="ChatBody">
            
            <div class="message-feed media mt-0">
                <b>Loading...</b>
            </div>
             </div>

            <form action="javascript:void(0);" onsubmit="this.preventDefault; return" method="post" id="messageForm">
                <input type="hidden" value="" id="endPoint">
                <input type="hidden" value="<?= $coID?>" name="course_co_id" id="co_id" >
                <input type="hidden" value="all" name="viewFor" id="viewFor" >
                <input type="hidden" value="" name="to_user_id" id="to_user_id">

                <div class="row">

                    <div class="col-10 col-lg-10">
                    <!-- <textarea placeholder="What's on your mind..." class="form-control input-value" name="description" id="msg-input"  ></textarea> -->
                    <textarea placeholder="Type your message here.." class="form-control input-value" name="description" id="msg-input"  ></textarea>
                    </div>
                    <div class="col-2 col-lg-2">
                    <!-- <button class="btn btn-primary send-msg-btn" style="margin-top: 12px; width: 80%; margin-right: 12px;" onclick="createMessage();" type="button"> Create</button> -->
                    <button class="btn btn-primary send-msg-btn" style="margin-top: 12px; width: 80%; margin-right: 12px;" onclick="createMessage();" type="button"> Send</button>
                    </div>
                    <div class="input_container" style="width: 100%;" ></div>
                </div>

            </form>

            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <!-- Row -->
                <div class="card">
                    {{-- <div class="card-header" style="display:inline">
                        <div style="float:left">
                        <h3 class="card-title">Student Chat </h3>
                        </div>   
                    </div> --}}
                    <div id="loader" style="margin-top: 10px;margin-bottom: 10px;margin-left: 46%;display:none">
                        <i class="fa fa-spinner fa-pulse fa-4x" aria-hidden="true"></i>
                    </div>
                    <div id="result-body" ></div>  
                    <div id="error-body" style="margin-top: 10px;margin-bottom: 10px;margin-left: 38%;color:red;font-weight:bold">
                    </div>
                </div>
                <style type="text/css">	
                    .sec{
                        position: relative;
                        right: 5px;
                        top:-22px;
                    }
                    .counter.counter-lg {
                        top: -24px !important;
                    }	
                    
                    
                    /* =========================
   COURSE CHAT - NEW UI
   ========================= */

/* Main chat card */
.bootstrap.snippet.card {
    background: #ffffff !important;
    border: 0 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: none !important;
}

/* Remove unnecessary outer spacing */
.bootstrap.snippet.card > .nav {
    margin-bottom: 0 !important;
}


/* =========================
   TOP TABS
   ========================= */

#pills-tab {
    display: flex !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    border-bottom: 1px solid #e9eef3;
    background: #ffffff;
}

#pills-tab .nav-item {
    flex: 1 1 50%;
    margin: 0 !important;
}

#pills-tab .nav-link {
    width: 100% !important;

    /* override your inline min-width */
    min-width: 0 !important;
    min-height: 0 !important;

    height: 40px;
    padding: 7px 15px !important;

    border-radius: 0 !important;
    border: 0 !important;

    background: #ffffff !important;
    color: #6e7f96 !important;

    font-size: 18px;
    font-weight: 500;
    text-align: center;

    transition: all 0.2s ease;
}

/* Active tab */
#pills-tab .nav-link.active {
    background: #176f66 !important;
    color: #ffffff !important;
    font-weight: 600;
}


/* =========================
   TAB CONTENT
   ========================= */

#pills-tabContent {
    background: #ffffff;
}

#pills-home {
    background: #ffffff;
}


/* =========================
   GROUP CHAT BODY
   ========================= */

#ChatBody {
    position: relative;

    min-height: 240px !important;
    height: 240px;

    padding: 18px 20px;

    overflow-y: auto !important;
    overflow-x: hidden !important;

    background: #ffffff;

    scrollbar-width: thin;
    scrollbar-color: #d9e1e8 transparent;
}

#ChatBody::-webkit-scrollbar {
    width: 5px;
}

#ChatBody::-webkit-scrollbar-track {
    background: transparent;
}

#ChatBody::-webkit-scrollbar-thumb {
    background: #d9e1e8;
    border-radius: 20px;
}


/* =========================
   EMPTY MESSAGE STATE
   ========================= */

#ChatBody .message-feed > p {
    position: absolute;
    top: 50%;
    left: 50%;

    margin: 0;
    width: 100%;

    transform: translate(-50%, -50%);

    text-align: center;
    color: #91a3bb;
    font-size: 11px;
    font-weight: 500;
}

/* Chat icon */
#ChatBody .message-feed > p::before {
    content: "\f075";

    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    margin: 0 auto 9px;

    border-radius: 50%;

    background: #f1f6fb;
    color: #718fb4;

    font-family: FontAwesome;
    font-size: 16px;
    font-weight: normal;
}

/* Text shown below "No Messages Found!" */
#ChatBody .message-feed > p::after {
    content: "Start a conversation by typing a message below.";

    display: block;

    margin-top: 4px;

    color: #b5c0cf;
    font-size: 8px;
    font-weight: 400;
}


/* =========================
   MESSAGE AREA
   ========================= */

.message-feed.right {
    margin-bottom: 12px;
}

.message-feed.right .mf-content {
    width: auto !important;
    max-width: 75% !important;

    margin-left: auto;

    padding: 10px 13px !important;

    background: #176f66 !important;
    color: #ffffff !important;

    border-radius: 10px !important;

    font-family: Helvetica, Arial, sans-serif;
    font-size: 12px;

    box-shadow: none !important;
}


/* Remove old separator look */
#ChatBody .card-block {
    border-bottom: 0 !important;
    padding-bottom: 0 !important;
}


/* =========================
   MESSAGE INPUT BOTTOM BAR
   ========================= */

#messageForm {
    padding: 8px 10px;
    margin: 0;

    background: #ffffff;

    border-top: 1px solid #edf0f4;
}

#messageForm > .row {
    display: flex;
    align-items: center;

    margin: 0 !important;
}


/* Message input column */
#messageForm .col-10 {
    flex: 1;
    max-width: none;

    padding: 0 8px 0 0 !important;
}


/* Send button column */
#messageForm .col-2 {
    flex: 0 0 72px;
    max-width: 72px;

    padding: 0 !important;
}


/* Textarea */
#msg-input {
    width: 100%;

    height: 30px !important;
    min-height: 30px !important;
    max-height: 70px;

    resize: none;

    padding: 7px 10px;

    border: 1px solid #e1e8ef !important;
    border-radius: 6px !important;

    background: #ffffff;

    color: #4b5b6f;

    font-size: 9px;

    box-shadow: none !important;
    outline: none !important;
}

#msg-input::placeholder {
    color: #9cafc3;
}

#msg-input:focus {
    border-color: #176f66 !important;
}


/* Send button */
.send-msg-btn {
    width: 100% !important;
    height: 30px;

    margin: 0 !important;
    padding: 0 !important;

    border: 0 !important;
    border-radius: 5px !important;

    background: #176f66 !important;

    color: #ffffff !important;
    font-size: 9px;
    font-weight: 600;

    box-shadow: none !important;
}

.send-msg-btn:hover,
.send-msg-btn:focus {
    background: #125f57 !important;
}


/* =========================
   STUDENT CHAT TAB
   ========================= */

#pills-profile > .card {
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
}

#pills-profile .card-header {
    background: #ffffff;
    border-bottom: 1px solid #edf0f4;
}

#pills-profile .card-title {
    margin: 0;
    font-size: 14px;
    color: #34495e;
}


/* =========================
   RESPONSIVE
   ========================= */

@media (max-width: 767px) {

    #ChatBody {
        height: 320px;
        min-height: 320px !important;
    }

    #pills-tab .nav-link {
        font-size: 9px;
    }

    #messageForm .col-2 {
        flex: 0 0 65px;
        max-width: 65px;
    }

    .message-feed.right .mf-content {
        max-width: 88% !important;
    }
}

/* =====================================
   FIGMA FONT FAMILY - COURSE CHAT
   Only typography family changed
===================================== */

.bootstrap.snippet.card,
.bootstrap.snippet.card button,
.bootstrap.snippet.card input,
.bootstrap.snippet.card textarea,
.bootstrap.snippet.card select,
.bootstrap.snippet.card .mf-content,
#reply-message-modal,
#reply-message-modal button,
#reply-message-modal input,
#reply-message-modal textarea,
#edit-message-modal,
#edit-message-modal button,
#edit-message-modal input,
#edit-message-modal textarea {
    font-family: 'Montserrat', sans-serif !important;
}

/* Preserve Font Awesome icons */
.bootstrap.snippet.card .fa,
#reply-message-modal .fa,
#edit-message-modal .fa,
#ChatBody .message-feed > p::before {
    font-family: FontAwesome !important;
}


/* =========================================================
   FACULTY NOTIFICATION / COURSE CHAT
   SAME STYLE AS course_chat2
   STYLE ONLY — functionality preserved
   ========================================================= */

:root {
    --chat-primary: #17675F;
    --chat-primary-dark: #105149;
    --chat-primary-soft: #E8F2F0;
    --chat-bg: #F6F9F8;
    --chat-white: #FFFFFF;
    --chat-border: #E3EBE8;
    --chat-text: #27333A;
    --chat-muted: #8B989E;
}


/* =========================
   MAIN CARD
   ========================= */

.bootstrap.snippet.card {
    border: 0 !important;
    border-radius: 16px !important;
    background: var(--chat-white) !important;
    box-shadow: 0 6px 24px rgba(23, 103, 95, 0.08) !important;
    overflow: hidden !important;
}


/* =========================
   TABS
   ========================= */

#pills-tab {
    display: flex !important;
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: var(--chat-white) !important;

    border: 0 !important;
    border-bottom: 1px solid var(--chat-border) !important;
}

#pills-tab .nav-item {
    flex: 1 1 50% !important;
    margin: 0 !important;
}

#pills-tab .nav-link {
    width: 100% !important;

    min-width: 0 !important;
    min-height: 56px !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: var(--chat-white) !important;

    border: 0 !important;
    border-radius: 0 !important;

    color: var(--chat-muted) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 15px !important;
    font-weight: 600 !important;

    text-align: center !important;
    text-decoration: none !important;

    transition: all 0.2s ease !important;
}

#pills-tab .nav-link.active {
    background: var(--chat-primary) !important;
    color: var(--chat-white) !important;
}

#pills-tab .nav-link:hover {
    color: var(--chat-primary) !important;
}

#pills-tab .nav-link.active:hover {
    color: var(--chat-white) !important;
}


/* =========================
   TAB CONTENT
   ========================= */

#pills-tabContent,
#pills-home {
    background: var(--chat-white) !important;
}


/* =========================
   CHAT BODY
   ========================= */

#ChatBody {
    position: relative !important;

    width: 100% !important;

    min-height: 400px !important;

    padding: 24px 26px !important;

    background-color: #F7FAF9 !important;

    background-image:
        radial-gradient(
            circle at 20px 20px,
            rgba(23, 103, 95, 0.025) 2px,
            transparent 2px
        ) !important;

    background-size: 35px 35px !important;

    border: 0 !important;

    overflow-y: auto !important;
    overflow-x: hidden !important;

    scrollbar-width: thin !important;
    scrollbar-color: #B8CBC7 transparent !important;
}

#ChatBody::-webkit-scrollbar {
    width: 6px !important;
}

#ChatBody::-webkit-scrollbar-track {
    background: transparent !important;
}

#ChatBody::-webkit-scrollbar-thumb {
    background: #B8CBC7 !important;
    border-radius: 20px !important;
}


/* =========================
   EMPTY STATE
   ========================= */

#ChatBody .message-feed > p {
    position: absolute !important;

    top: 50% !important;
    left: 50% !important;

    width: 100% !important;

    margin: 0 !important;

    transform: translate(-50%, -50%) !important;

    text-align: center !important;

    color: var(--chat-primary) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}

#ChatBody .message-feed > p::before {
    content: "\f075";

    width: 44px !important;
    height: 44px !important;

    margin: 0 auto 10px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: var(--chat-primary-soft) !important;

    border-radius: 50% !important;

    color: var(--chat-primary) !important;

    font-family: FontAwesome !important;
    font-size: 18px !important;
    font-weight: normal !important;
}

#ChatBody .message-feed > p::after {
    content: "Start a conversation by typing a message below.";

    display: block !important;

    margin-top: 5px !important;

    color: var(--chat-muted) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 11px !important;
    font-weight: 400 !important;
}


/* =========================
   MESSAGE WRAPPER
   ========================= */

#ChatBody .message-feed {
    width: 100% !important;

    margin-bottom: 16px !important;

    border: 0 !important;
}


/* =========================
   MY MESSAGE
   ========================= */

#ChatBody .message-feed.right {
    display: flex !important;

    flex-direction: row-reverse !important;

    align-items: flex-end !important;
    justify-content: flex-start !important;
}

#ChatBody .message-feed.right .float-right {
    float: none !important;

    padding-left: 10px !important;
}

#ChatBody .message-feed.right .float-right img {
    width: 40px !important;
    height: 40px !important;

    object-fit: cover !important;

    border-radius: 50% !important;

    border: 2px solid var(--chat-white) !important;

    box-shadow: 0 2px 7px rgba(0, 0, 0, 0.08) !important;
}

#ChatBody .message-feed.right .media-body {
    flex: none !important;

    width: auto !important;
    max-width: 72% !important;

    text-align: right !important;
}

#ChatBody .message-feed.right .mf-content {
    width: fit-content !important;
    max-width: 100% !important;

    margin-left: auto !important;

    padding: 12px 15px !important;

    background: var(--chat-primary) !important;

    color: var(--chat-white) !important;

    border: 0 !important;
    border-radius: 15px 5px 15px 15px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    line-height: 1.55 !important;

    text-align: left !important;

    box-shadow: 0 2px 8px rgba(23, 103, 95, 0.14) !important;
}

#ChatBody .message-feed.right .mf-content b {
    display: block !important;

    color: var(--chat-white) !important;

    font-size: 12px !important;
    font-weight: 700 !important;
}

#ChatBody .message-feed.right .mf-content small {
    color: rgba(255,255,255,0.72) !important;

    font-size: 10px !important;
}

#ChatBody .message-feed.right .mf-content .badge {
    background: rgba(255,255,255,0.15) !important;

    border: 0 !important;
    border-radius: 20px !important;

    color: #fff !important;

    padding: 4px 8px !important;
}


/* =========================
   REMOVE OLD SEPARATORS
   ========================= */

#ChatBody .card-block {
    height: 0 !important;

    margin: 0 !important;
    padding: 0 !important;

    border: 0 !important;
    border-bottom: 0 !important;
}


/* =========================
   MESSAGE FORM
   ========================= */

#messageForm {
    margin: 0 !important;

    padding: 14px 16px !important;

    background: var(--chat-white) !important;

    border: 0 !important;
    border-top: 1px solid var(--chat-border) !important;
}

/* #messageForm .input_container {
    display: none !important;
} */

#messageForm > .row {
    width: 100% !important;

    display: flex !important;
    align-items: center !important;

    flex-wrap: nowrap !important;

    gap: 12px !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* INPUT COLUMN */

#messageForm > .row > .col-10,
#messageForm > .row > .col-lg-10 {
    flex: 1 1 0 !important;

    width: auto !important;
    max-width: none !important;
    min-width: 0 !important;

    padding: 0 !important;
}


/* SEND BUTTON COLUMN */

#messageForm > .row > .col-2,
#messageForm > .row > .col-lg-2 {
    flex: 0 0 120px !important;

    width: 120px !important;
    max-width: 120px !important;
    min-width: 120px !important;

    padding: 0 !important;
}


/* =========================
   TEXTAREA
   ========================= */

#messageForm #msg-input {
    display: block !important;

    width: 100% !important;
    max-width: 100% !important;

    height: 46px !important;
    min-height: 46px !important;
    max-height: 110px !important;

    margin: 0 !important;
    padding: 11px 14px !important;

    box-sizing: border-box !important;

    resize: none !important;

    background: #F8FAFA !important;

    border: 1px solid #D9E3E1 !important;
    border-radius: 12px !important;

    color: var(--chat-text) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    line-height: 22px !important;

    outline: none !important;

    box-shadow: none !important;

    transition: all 0.2s ease !important;
}

#messageForm #msg-input::placeholder {
    color: #A1ADB3 !important;
}

#messageForm #msg-input:focus {
    background: var(--chat-white) !important;

    border-color: var(--chat-primary) !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================
   SEND BUTTON
   ========================= */

#messageForm .send-msg-btn {
    width: 100% !important;

    height: 46px !important;

    margin: 0 !important;
    padding: 0 18px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: var(--chat-primary) !important;

    border: 0 !important;
    border-radius: 12px !important;

    color: var(--chat-white) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;

    box-shadow: 0 4px 12px rgba(23, 103, 95, 0.22) !important;

    transition: all 0.2s ease !important;
}

#messageForm .send-msg-btn:hover,
#messageForm .send-msg-btn:focus {
    background: var(--chat-primary-dark) !important;

    color: var(--chat-white) !important;

    transform: translateY(-1px) !important;
}


/* =========================
   STUDENT CHAT
   ========================= */

#pills-profile > .card {
    margin: 0 !important;

    border: 0 !important;
    border-radius: 0 0 16px 16px !important;

    background: var(--chat-white) !important;

    box-shadow: none !important;
}

#result-body .table-responsive {
    border-radius: 0 0 16px 16px !important;

    overflow: hidden !important;
}

#result-body table {
    margin: 0 !important;

    background: var(--chat-white) !important;

    border: 0 !important;
}

#result-body table tr {
    transition: background-color 0.2s ease !important;
}

#result-body table tr:hover {
    background: #F7FAF9 !important;
}

#result-body table td {
    padding: 14px 18px !important;

    vertical-align: middle !important;

    border: 0 !important;
    border-bottom: 1px solid var(--chat-border) !important;
}

#result-body table tr:last-child td {
    border-bottom: 0 !important;
}

#result-body img.avatar-lg {
    width: 48px !important;
    height: 48px !important;

    object-fit: cover !important;

    border: 2px solid var(--chat-white) !important;

    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}

#result-body h5 {
    margin: 0 0 3px !important;

    color: var(--chat-text) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}

#result-body p {
    color: var(--chat-muted) !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
}

#result-body .fa-bars {
    color: var(--chat-primary) !important;

    font-size: 20px !important;
}

#result-body .badge-danger {
    background: #E24D4F !important;

    border-radius: 50px !important;
}


/* =========================
   LOADER
   ========================= */

#loader .fa {
    color: var(--chat-primary) !important;
}


/* =========================
   REPLY / EDIT MODALS
   ========================= */

#reply-message-modal .modal-content,
#edit-message-modal .modal-content {
    overflow: hidden !important;

    border: 0 !important;
    border-radius: 16px !important;

    box-shadow: 0 15px 45px rgba(0,0,0,0.16) !important;

    font-family: 'Montserrat', sans-serif !important;
}

#reply-message-modal .modal-header,
#edit-message-modal .modal-header {
    padding: 16px 20px !important;

    background: var(--chat-primary) !important;

    border: 0 !important;
}

#reply-message-modal .modal-title,
#edit-message-modal .modal-title {
    color: #fff !important;

    font-size: 17px !important;
    font-weight: 600 !important;
}

#reply-message-modal .close,
#edit-message-modal .close {
    color: #fff !important;

    opacity: 1 !important;

    text-shadow: none !important;
}

#reply-message-modal .modal-body,
#edit-message-modal .modal-body {
    padding: 20px !important;

    background: #fff !important;
}

#reply-message-modal .form-control,
#edit-message-modal .form-control {
    height: 44px !important;

    border: 1px solid #D9E3E1 !important;
    border-radius: 10px !important;

    box-shadow: none !important;
}

#reply-message-modal .form-control:focus,
#edit-message-modal .form-control:focus {
    border-color: var(--chat-primary) !important;

    box-shadow: 0 0 0 3px rgba(23,103,95,0.08) !important;
}

#reply-message-modal .modal-footer,
#edit-message-modal .modal-footer {
    padding: 14px 20px !important;

    border-top: 1px solid var(--chat-border) !important;

    background: #fff !important;
}

#reply-message-modal .btn-primary,
#edit-message-modal .btn-primary {
    background: var(--chat-primary) !important;

    border-color: var(--chat-primary) !important;

    border-radius: 8px !important;
}

#reply-message-modal .btn-default,
#edit-message-modal .btn-default {
    background: #fff !important;

    border: 1px solid var(--chat-border) !important;

    border-radius: 8px !important;

    color: var(--chat-text) !important;
}


/* =========================
   MOBILE
   ========================= */

@media (max-width: 767px) {

    .bootstrap.snippet.card {
        border-radius: 12px !important;
    }

    #pills-tab .nav-link {
        min-height: 50px !important;

        padding: 0 10px !important;

        font-size: 13px !important;
    }

    #ChatBody {
        min-height: 320px !important;

        padding: 18px 12px !important;
    }

    #ChatBody .message-feed.right .media-body {
        max-width: 82% !important;
    }

    #ChatBody .message-feed.right .float-right img {
        width: 36px !important;
        height: 36px !important;
    }

    #messageForm {
        padding: 10px !important;
    }

    #messageForm > .row {
        gap: 8px !important;
    }

    #messageForm > .row > .col-2,
    #messageForm > .row > .col-lg-2 {
        flex: 0 0 80px !important;

        width: 80px !important;
        max-width: 80px !important;
        min-width: 80px !important;
    }

    #messageForm #msg-input {
        height: 44px !important;
        min-height: 44px !important;

        padding: 10px 12px !important;
    }

    #messageForm .send-msg-btn {
        height: 44px !important;

        padding: 0 12px !important;
    }

    #result-body table td {
        padding: 12px !important;
    }
}
/* Keep input_container in DOM but don't let it disturb flex layout */
#messageForm .input_container {
    position: absolute !important;
    width: 100% !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
}
                    </style>
                <!-- End Row-->
            </div>
            </div>


            </div>
        </div>
    </div>
</div>    

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
                                <span id="error-msg"></span>
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

var co_id = '<?=$coID;?>';
  

    function getStudent(){
        $('#loader').show();   
        var fd = new FormData();
        $.ajax({
               type:'POST',
               url:  '<?=$mobileURL;?>/course_enrolled_students?co_id='+co_id+'&user_name='+'<?=auth()->user()->username;?>',
               // data:fd,
			    headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
               processData:false,
               contentType:false,
               success:function(data){
               // alert('bilal')
                console.log('bilal',data)
               var html = '';         
               html += '</div>';
               html += '</div>';
               html += '<div class="card-body" style="padding: 0px;">';
               html += '<div class="table-responsive">';
               html += '<table class="table table-vcenter text-nowrap mb-0 border">'
               html += '<thead>';
               html += '<tr>';
               html += '</tr>';
               
               $.each(data,function( key, value ) {                      
                    var filePath = 'https://cms.bnu.edu.pk/students_pics/'+value.reg_no+'.jpg';
                    html += '<tr>';
                    html += '<td class="d-flex" style="cursor:pointer" onclick="btn('+co_id+',\''+value.reg_no+'\')">'; 
                    html += '<img class="avatar-lg rounded-circle mr-3" src="'+filePath+'" >';
                    html += '<div class="ml-3 mt-2" >';
                    html += '<h5 class="mb-0 text-dark" >'+value.name+'</h5>';
                    html += '<p class="mb-0  fs-13 text-muted">'+value.reg_no+'</p>';                
                    html += '</div>';
                    html += '</td>';
                    html += '<td onclick="btn('+co_id+',\''+value.reg_no+'\')" style="cursor:pointer">';
                    html += '</td>';
                    html += '<td onclick="btn('+co_id+',\''+value.reg_no+'\')" style="cursor:pointer">';
                    html += '</td >';
                    html += '<td onclick="btn('+co_id+',\''+value.reg_no+'\')" style="cursor:pointer">';
                    // html += '<i class="fa fa-bars fa-2x" aria-hidden="true"></i><span style="display:'+(value.notification_count>0?'':'none')+'" class="badge sec badge-danger">'+value.notification_count+'</span>';
                html += '<img src="{{ asset("images/forward.png") }}" alt="Forward" style="width: 18px; height: 18px; object-fit: contain;">' +
        '<span style="display:' + (value.notification_count > 0 ? '' : 'none') + ';" class="badge sec badge-danger">' +
        value.notification_count +
        '</span>';
                    html += '</td>';
                    html += '</tr>';                   
               });
                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    html += '</div>';             
                    if(html != ''){
                       $('#loader').hide();  
                       $('#result-body').html(html);
                    }else{
                        $('#result-body').html('No Result Found!');
                    }                
                },
                error: function (jqXHR, exception) {
                     alert(jqXHR+"===".exception)
                    $('#error-body').html('Some Error Occured Please contact ITRC!');
     
                },               
        });
    }
    function btn(co_id,reg){
        window.location = '<?=url('/course/chat2')?>/'+co_id+'?reg='+reg;
        //  alert(co_id)
    }

function createMessage() {

            var bi_form = document.querySelector("#messageForm");
            var fd    = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url:  '<?=$mobileURL;?>/sendNotification',
                data:fd,
                headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
                processData:false,
                contentType:false,
                success:function(res){
                    $('#msg-input').val('');
                    getMessage();
                    $('#error-msg').html(res.description[0]);
                    //console.log(res.description[0]);
                },    
                error: function (jqXHR, exception) {
                

            },                            
            });
}
function getMessage() {
       
        $.ajax({
                type:'GET',
                url:  '<?=$mobileURL;?>/getNotification?co_id='+co_id+'&reg_no='+'<?=auth()->user()->username;?>',
                headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
                //data:fd,
                processData:false,
                contentType:false,
                success:function(res){
                    var html = '';
                    var index = 0;
                    var borderColor='steelblue';
                    if(res.data!=''){
                        $(res.data).each(function(index, value) {
                            html += rightSide(value, index);
                            html += '<div class="card-block" style="padding-bottom:0px; border-bottom:solid 2px ' + borderColor + ';">';
                            html += '</div>';
                            index++;
                        });
                        $('#ChatBody').html(html);
                    }else{
                        // $('.message-feed').html('<p><b>No Record found!</b></p>');
                        $('.message-feed').html('<p><b>No Messages Found!</b></p>');
                    }
                    
                },
                error: function (jqXHR, exception) {
                
                        console.log(jqXHR);
                 },  
                              
            });
    }

$(document).ready(function() {
     getMessage();
    setInterval(() => {
        getMessage();
        }, 5000);
});



function rightSide(row, index) { // my message
   
        var username = '';//(row.sender_name) ? row.sender_name : row.entry_user_id.toString();
        var html = '<div class="messages"><div class="message-feed right">';
        //   html    += '<div class="message out no-avatar media" style=""><div class="body media-body text-right p-l-50">';
        html += '<div class="float-right pl-6" ><img src="<?= asset('/staffimages/'.auth()->user()->employee_id.'.jpg') ?>" class="rounded-circle"  alt="dp" width="40" height="40" ></a></div>';
        html += '<a href="/course/chat/'+co_id+'?topic='+row.un_id+'"><div class="media-body" style="display:block"  > <div class="mf-content" style="text-align:left" >    <b>' + '<?=auth()->user()->username;?>' +'<small style="margin-left:40px;" class="float-right"><span class="badge badge-light">Notification:'+row.total_notification+'</span></small></b><br>';
        html += '<br>' + row.un_description+ '<span style="text-align:left">  </span><small style="margin-left:40px;" class="float-right">'+row.un_entry_datetime+'</small></div></div>';
        html += '</div></a>';

        html += '</div>';
        return html;
    }
</script>
@stop
