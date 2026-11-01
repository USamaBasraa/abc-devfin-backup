@extends('layouts.app',['pageTitle' => 'Course Chat'])
@section('content')
{{-- @dd("12"); --}}
<?php
$eCoID = my_simple_crypt($coID);
$mobileURL='http://devapi.bnu.edu.pk';
$url='http://devapi.bnu.edu.pk/send?mesg_bnu_un_id=0&message=seconddirect&course_co_id=22010&viewFor=direct&to_user_id=s2019-099&entry_user_id=s2019-099';
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


    /* ========================================
   INDIVIDUAL STUDENT CHAT - UPDATED UI
   ======================================== */

/* Main outer chat card */
.bootstrap.snippet.card {
    background: #fff !important;
    border: 0 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: none !important;
}


/* Main chat wrapper */
#messages-main {
    background: #fff !important;
    border: 0 !important;
    margin: 0 !important;
}


/* We don't need the old side menu space */
#messages-main .ms-menu {
    display: none !important;
}


/* Main right/chat section */
#messages-main .ms-body {
    width: 100% !important;
    margin-left: 0 !important;
    padding-left: 0 !important;
    background: #fff !important;
}


/* ========================================
   HEADER
   ======================================== */

#messages-main .action-header {
    position: relative !important;

    display: flex !important;
    align-items: center !important;

    height: 60px !important;
    min-height: 60px !important;

    padding: 0 18px !important;
    margin: 0 !important;

    background: #fff !important;

    border: 0 !important;
    border-bottom: 1px solid #edf0f4 !important;

    box-shadow: none !important;
}


/* Back / menu button */
#ms-menu-trigger {
    position: static !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 32px !important;
    height: 32px !important;

    margin: 0 10px 0 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 6px !important;

    color: #42547c !important;
}

#ms-menu-trigger i {
    color: #42547c !important;
    font-size: 20px !important;
}


/* Student/course information */
.action-header .chat-user {
    display: flex !important;
    align-items: center !important;

    margin-left: 0 !important;
}


/* Header profile image */
.action-header .chat-user img {
    width: 40px !important;
    height: 40px !important;

    object-fit: cover;

    margin-right: 10px !important;

    border-radius: 50% !important;
}


/* Student registration/name */
#chat_person_name {
    display: block;

    margin: 0 !important;

    color: #42547c !important;

    font-size: 13px !important;
    font-weight: 600 !important;
}


/* Remove old extra top margin */
.action-header .chat-user .mt-2 {
    margin-top: 0 !important;
}


/* ========================================
   CHAT BODY
   ======================================== */

#ChatBody {
    position: relative !important;

    width: 100% !important;

    min-height: 350px !important;

    padding: 20px 22px !important;

    background-color: #fff !important;
    background-image: none !important;

    overflow-y: auto !important;
    overflow-x: hidden !important;

    border: 0 !important;

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


/* ========================================
   NO MESSAGE STATE
   ======================================== */

#ChatBody > .message-feed.media.mt-0 {
    position: absolute !important;

    top: 50% !important;
    left: 50% !important;

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    transform: translate(-50%, -50%);

    display: block !important;

    text-align: center !important;

    border: 0 !important;
    background: transparent !important;
}


#ChatBody > .message-feed.media.mt-0 b {
    display: block;

    color: #91a3bb !important;

    font-size: 11px !important;
    font-weight: 500 !important;
}


/* Empty state icon */
#ChatBody > .message-feed.media.mt-0::before {
    content: "\f075";

    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    margin: 0 auto 9px;

    background: #f1f6fb;

    border-radius: 50%;

    color: #718fb4;

    font-family: FontAwesome;
    font-size: 16px;
}


/* Empty state description */
#ChatBody > .message-feed.media.mt-0::after {
    content: "Start a conversation by typing a message below.";

    display: block;

    margin-top: 4px;

    color: #b5c0cf;

    font-size: 8px;
    font-weight: 400;
}


/* ========================================
   EXISTING MESSAGES
   ======================================== */

#ChatBody .message-feed {
    border: 0 !important;
}


/* My message */
#ChatBody .message-feed.right .mf-content {
    width: auto !important;
    max-width: 70% !important;

    margin-left: auto !important;

    padding: 10px 13px !important;

    background: #176f66 !important;
    color: #fff !important;

    border: 0 !important;
    border-radius: 10px !important;

    font-size: 12px !important;

    box-shadow: none !important;
}


/* Other/student message */
#ChatBody .otherMessages .mf-content {
    width: auto !important;
    max-width: 70% !important;

    padding: 10px 13px !important;

    background: #f2f5f8 !important;
    color: #42526a !important;

    border: 0 !important;
    border-radius: 10px !important;

    font-size: 12px !important;

    box-shadow: none !important;
}


/* Message profile images */
#ChatBody .message-feed img {
    width: 34px !important;
    height: 34px !important;

    object-fit: cover;

    border-radius: 50% !important;
}


/* Remove colored message separators */
#ChatBody .card-block {
    padding: 0 !important;

    border: 0 !important;
    border-bottom: 0 !important;
}


/* ========================================
   BOTTOM MESSAGE FORM
   ======================================== */

#messageForm {
    margin: 0 !important;
    padding: 8px 10px !important;

    background: #fff !important;

    border: 0 !important;
    border-top: 1px solid #edf0f4 !important;
}


#messageForm > .row {
    display: flex !important;
    align-items: center !important;

    margin: 0 !important;
}


/* Input column */
#messageForm .col-10 {
    flex: 1 !important;
    /* max-width: none !important; */

    padding: 0 8px 0 0 !important;
}


/* Button column */
#messageForm .col-2 {
    flex: 0 0 72px !important;
    max-width: 72px !important;

    padding: 0 !important;
}


/* Textarea */
#msg-input {
    display: block !important;

    width: 100% !important;

    height: 30px !important;
    min-height: 30px !important;
    max-height: 70px !important;

    margin: 0 !important;
    padding: 7px 10px !important;

    resize: none !important;

    background: #fff !important;

    border: 1px solid #e1e8ef !important;
    border-radius: 6px !important;

    color: #4b5b6f !important;

    font-size: 9px !important;
    line-height: 14px !important;

    outline: none !important;
    box-shadow: none !important;
}


#msg-input::placeholder {
    color: #9cafc3 !important;
}


#msg-input:focus {
    border-color: #176f66 !important;
    box-shadow: none !important;
}


/* Send button */
#messageForm .send-msg-btn {
    display: block !important;

    width: 100% !important;
    height: 30px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #176f66 !important;

    border: 0 !important;
    border-radius: 5px !important;

    color: #fff !important;

    font-size: 9px !important;
    font-weight: 600 !important;

    box-shadow: none !important;
}


#messageForm .send-msg-btn:hover,
#messageForm .send-msg-btn:focus {
    background: #125f57 !important;
}


/* ========================================
   MOBILE
   ======================================== */

@media (max-width: 767px) {

    #messages-main .action-header {
        height: 55px !important;
        min-height: 55px !important;

        padding: 0 12px !important;
    }

    .action-header .chat-user img {
        width: 36px !important;
        height: 36px !important;
    }

    #ChatBody {
        padding: 15px !important;
    }

    #ChatBody .message-feed.right .mf-content,
    #ChatBody .otherMessages .mf-content {
        max-width: 85% !important;
    }

    #messageForm .col-2 {
        flex: 0 0 65px !important;
        max-width: 65px !important;
    }
}


/* =========================================================
   INDIVIDUAL CHAT - FINAL UI OVERRIDE
   Primary: #17675F
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


/* =========================================================
   MAIN CARD
   ========================================================= */

.bootstrap.snippet.card {
    border: 0 !important;
    border-radius: 16px !important;
    background: var(--chat-white) !important;
    box-shadow: 0 6px 24px rgba(23, 103, 95, 0.08) !important;
    overflow: hidden !important;
}

#messages-main {
    margin: 0 !important;
    border: 0 !important;
    border-radius: 16px !important;
    background: var(--chat-white) !important;
    overflow: hidden !important;
}

#messages-main .ms-menu {
    display: none !important;
}

#messages-main .ms-body {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: var(--chat-white) !important;
}


/* =========================================================
   HEADER
   ========================================================= */

#messages-main .action-header {
    min-height: 74px !important;
    height: auto !important;

    padding: 14px 18px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;

    gap: 12px !important;

    background: var(--chat-white) !important;

    border: 0 !important;
    border-bottom: 1px solid var(--chat-border) !important;

    box-shadow: none !important;

    position: relative !important;
}


/* link wrapper */
#messages-main .action-header > a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 !important;

    flex: 0 0 auto !important;

    position: static !important;

    text-decoration: none !important;
}


/* hamburger */
#ms-menu-trigger {
    width: 42px !important;
    height: 42px !important;

    min-width: 42px !important;
    min-height: 42px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    position: static !important;

    border: none !important;
    border-radius: 12px !important;

    background: var(--chat-primary-soft) !important;
    color: var(--chat-primary) !important;

    cursor: pointer !important;

    flex-shrink: 0 !important;

    transition: all 0.2s ease !important;
}

#ms-menu-trigger i {
    margin: 0 !important;
    padding: 0 !important;

    font-size: 21px !important;
    line-height: 1 !important;

    color: inherit !important;
}

#ms-menu-trigger:hover {
    background: var(--chat-primary) !important;
    color: var(--chat-white) !important;
}


/* =========================================================
   HEADER TITLE
   ========================================================= */

.action-header .chat-user {
    display: flex !important;
    align-items: center !important;

    margin: 0 !important;
    padding: 0 !important;

    flex: 1 1 auto !important;
    min-width: 0 !important;

    float: none !important;
    position: static !important;
}


/* hide old avatar like previous screen */
.action-header .chat-user img {
    display: none !important;
}


.action-header .chat-user > div {
    display: flex !important;
    align-items: center !important;

    margin: 0 !important;
    padding: 0 !important;

    min-width: 0 !important;
}

.action-header .chat-user .mt-2 {
    margin-top: 0 !important;
}

#chat_person_name {
    display: block !important;

    margin: 0 !important;
    padding: 0 !important;

    color: var(--chat-text) !important;

    font-size: 17px !important;
    font-weight: 700 !important;

    line-height: 1.35 !important;

    white-space: normal !important;
    overflow: visible !important;
}


/* empty actions */
.action-header .ah-actions {
    display: none !important;
}


/* =========================================================
   CHAT BODY
   ========================================================= */

#ChatBody {
    width: 100% !important;

    padding: 24px 26px !important;

    min-height: 400px !important;

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


/* =========================================================
   EMPTY STATE
   ========================================================= */

#ChatBody > .message-feed.media.mt-0 {
    min-height: 300px !important;

    position: static !important;

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    transform: none !important;

    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;

    text-align: center !important;

    background: transparent !important;
    border: 0 !important;
}

#ChatBody > .message-feed.media.mt-0::before {
    content: "\f075";

    width: 44px !important;
    height: 44px !important;

    margin: 0 auto 10px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    border-radius: 50% !important;

    background: var(--chat-primary-soft) !important;

    color: var(--chat-primary) !important;

    font-family: FontAwesome !important;
    font-size: 18px !important;
}

#ChatBody > .message-feed.media.mt-0 b {
    display: block !important;

    color: var(--chat-primary) !important;

    font-size: 14px !important;
    font-weight: 600 !important;
}

#ChatBody > .message-feed.media.mt-0::after {
    content: "Start a conversation by typing a message below.";

    display: block !important;

    margin-top: 5px !important;

    color: var(--chat-muted) !important;

    font-size: 11px !important;
}


/* =========================================================
   MESSAGE COMMON
   ========================================================= */

#ChatBody .message-feed {
    width: 100% !important;

    margin-bottom: 16px !important;

    border: 0 !important;
}

#ChatBody .message-feed.media {
    align-items: flex-end !important;
}

#ChatBody .message-feed .media-body {
    max-width: 72% !important;
}


/* =========================================================
   MY MESSAGE
   ========================================================= */

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


/* edit link inside my message */
#ChatBody .message-feed.right .mf-content a {
    color: #ffffff !important;

    font-size: 11px !important;

    text-decoration: underline !important;
}


/* =========================================================
   OTHER / STUDENT MESSAGE
   ========================================================= */

#ChatBody .otherMessages {
    display: flex !important;
    align-items: flex-end !important;
}

#ChatBody .otherMessages .float-left {
    float: none !important;

    padding-right: 10px !important;
}

#ChatBody .otherMessages img {
    width: 40px !important;
    height: 40px !important;

    object-fit: cover !important;

    border-radius: 50% !important;

    border: 2px solid var(--chat-white) !important;

    box-shadow: 0 2px 7px rgba(0,0,0,0.08) !important;
}

#ChatBody .otherMessages .mf-content {
    width: fit-content !important;
    max-width: 100% !important;

    padding: 12px 15px !important;

    background: var(--chat-white) !important;

    color: var(--chat-text) !important;

    border: 1px solid var(--chat-border) !important;
    border-radius: 5px 15px 15px 15px !important;

    font-size: 14px !important;
    line-height: 1.55 !important;

    box-shadow: 0 2px 8px rgba(30, 55, 53, 0.06) !important;
}

#ChatBody .otherMessages .mf-content b {
    display: inline-block !important;

    margin-bottom: 3px !important;

    color: var(--chat-primary) !important;

    font-size: 12px !important;
    font-weight: 700 !important;
}


/* =========================================================
   MESSAGE META / EDIT
   ========================================================= */

#ChatBody .seen {
    margin-top: 5px !important;

    padding-left: 4px !important;

    color: var(--chat-muted) !important;

    font-size: 11px !important;
}

#ChatBody .seen p {
    margin: 0 !important;

    color: var(--chat-muted) !important;

    font-size: 11px !important;
}

#ChatBody .seen a {
    color: var(--chat-primary) !important;

    font-size: 11px !important;
    font-weight: 600 !important;
}


/* remove old separators */
#ChatBody .card-block {
    height: 0 !important;

    margin: 0 !important;
    padding: 0 !important;

    border: 0 !important;
    border-bottom: 0 !important;
}


/* =========================================================
   BOTTOM COMPOSER
   ========================================================= */

#messageForm {
    margin: 0 !important;

    padding: 14px 16px !important;

    background: var(--chat-white) !important;

    border: 0 !important;
    border-top: 1px solid var(--chat-border) !important;
}

#messageForm > .row {
    display: flex !important;
    align-items: center !important;

    flex-wrap: nowrap !important;

    gap: 10px !important;

    margin: 0 !important;
}


/* textarea column */
#messageForm .col-10,
#messageForm .col-lg-10 {
    flex: 1 1 auto !important;

    /* max-width: none !important; */

    padding: 0 !important;
}


/* button column */
#messageForm .col-2,
#messageForm .col-lg-2 {
    flex: 0 0 110px !important;

    max-width: 110px !important;

    padding: 0 !important;
}


/* textarea */
#msg-input {
    width: 100% !important;

    height: 46px !important;
    min-height: 46px !important;
    max-height: 110px !important;

    margin: 0 !important;
    padding: 11px 14px !important;

    resize: none !important;

    background: #F8FAFA !important;

    border: 1px solid #D9E3E1 !important;
    border-radius: 12px !important;

    color: var(--chat-text) !important;

    font-size: 14px !important;
    line-height: 22px !important;

    outline: none !important;

    box-shadow: none !important;

    transition: all 0.2s ease !important;
}

#msg-input::placeholder {
    color: #A1ADB3 !important;
}

#msg-input:focus {
    background: var(--chat-white) !important;

    border-color: var(--chat-primary) !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* send */
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


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767px) {

    #messages-main .action-header {
        min-height: 64px !important;

        padding: 10px 12px !important;

        gap: 10px !important;
    }

    #ms-menu-trigger {
        width: 38px !important;
        height: 38px !important;

        min-width: 38px !important;
        min-height: 38px !important;

        border-radius: 10px !important;
    }

    #ms-menu-trigger i {
        font-size: 19px !important;
    }

    #chat_person_name {
        font-size: 14px !important;
    }

    #ChatBody {
        padding: 18px 12px !important;
    }

    #ChatBody .message-feed .media-body,
    #ChatBody .message-feed.right .media-body {
        max-width: 82% !important;
    }

    #ChatBody .message-feed img {
        width: 36px !important;
        height: 36px !important;
    }

    #messageForm {
        padding: 10px !important;
    }

    #messageForm .col-2,
    #messageForm .col-lg-2 {
        flex: 0 0 85px !important;
        max-width: 85px !important;
    }

    #msg-input {
        height: 44px !important;
        min-height: 44px !important;

        padding: 10px 12px !important;
    }

    #messageForm .send-msg-btn {
        height: 44px !important;

        padding: 0 12px !important;
    }
}



/* ==========================================
   FINAL MESSAGE INPUT WIDTH FIX
   ========================================== */

/* This empty div is taking unnecessary width */
#messageForm .input_container {
    display: none !important;
}


/* Complete composer row */
#messageForm > .row {
    width: 100% !important;

    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;

    margin: 0 !important;
    padding: 0 !important;

    gap: 12px !important;
}


/* INPUT COLUMN - use ALL available space */
#messageForm > .row > .col-10,
#messageForm > .row > .col-lg-10 {
    flex: 1 1 0 !important;

    width: auto !important;
    max-width: none !important;
    min-width: 0 !important;

    padding: 0 !important;
}


/* TEXTAREA */
#messageForm #msg-input {
    display: block !important;

    width: 100% !important;
    max-width: 100% !important;

    box-sizing: border-box !important;

    margin: 0 !important;
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


/* BUTTON */
#messageForm .send-msg-btn {
    width: 100% !important;
    margin: 0 !important;
}

/* Mobile */
@media (max-width: 767px) {

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
}
</style>
<!-- Row -->
<div class="row">
    <div class="col-md-12">
        <div class="bootstrap snippet card overflow-hidden bg-transparent">
            <div class="tile tile-alt mb-0" style="width: 100%;" id="messages-main">
                <div class="ms-menu">
                    <!-- <div class="tab-menu-heading border-top-0"> -->
                    <!-- <h4 class="font-weight-bold" style="margin-top:0px;"  >Participants</h4>
                        <div class="tabs-menu1 px-3">
                            <ul class="nav">
                                <li style="width: 100%;"><a href="#tab-7" class="active" data-toggle="tab">Students</a></li>
                                <li><a href="#tab-8" data-toggle="tab" class="">Contacts</a></li>
                            </ul>
                        </div> -->
                    <!-- </div> -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-7">
                            <ul class="list-group lg-alt chat-conatct-list" id="ChatList"></ul>
                                <!-- <li class="list-group-item media p-4 border-top mt-0">
                                    <div class="float-left pr-2">
                                        <img src="../../assets/images/users/5.jpg" alt="" class="avatar avatar-md brround">
                                    </div>
                                    <div class="media-body">
                                        <div class="list-group-item-heading text-default font-weight-semibold">Bilal Saleem</div>
                                        <small class="list-group-item-text text-muted">My Last Message</small>
                                    </div>
                                    <span class="chat-time text-muted">2 mins</span>
                                </li> -->
                        </div>
                    </div>
                </div>
                <div class="ms-body" style="padding-left: 19px;">
                    <div class="action-header clearfix">
                         <a href="<?=URL::to('/course/notification/'.$coID);?>">
							{{-- <div class="" id="ms-menu-trigger" >
								<i class="fa fa-bars"></i>
							</div> --}}
                            <div class="" id="ms-menu-trigger">
    <img src="{{ asset('images/backword.png') }}"
         alt="Forward"
         style="width: 18px; height: 18px; object-fit: contain; vertical-align: middle;">
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
                            <input type="hidden" value="<?= $eCoID ?>" name="co_id" id="co_id" >
                            <input type="hidden" value="<?= $coID ?>" name="course_co_id" id="course_co_id" >
                            <input type="hidden" value="all" name="viewFor" id="viewFor" >
                            <input type="hidden" value="" name="to_user_id" id="to_user_id">

                            <div class="row  "  >

                                <div class="col-10 col-lg-10">
                                <textarea placeholder="Type your message here..." class="form-control input-value" name="message" id="msg-input"  ></textarea>
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
function readMessages(unID,coID,userName) {

var formData    = new FormData();
formData.append("un_id",unID);
formData.append("co_id",'<?=$coID;?>');
formData.append("reg_no",'<?=auth()->user()->username;?>');
formData.append("sender_name",userName);

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

    var groupID = '<?=$coID?>';
    var url      = window.location.href; 
        var url = new URL(url);
        var reg = url.searchParams.get("reg");
      
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
        //alert('<?=auth()->user()->access_token;?>');
    });

    var username = '<?= auth()->user()->username ?>';
    var uID = '<?= auth()->user()->id ?>';
    var messageArr = [];
    var readMessageArr = [];
    var viewFor = 'all';
    var courseName = '<?=$course_name?>';

//  <span class="font-weight-bold" id="chat_person_name" ><?=$course_name?></span>




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


    function sendGetRequest1(endPoint, successBlock) {
        $.ajax({
            method: "GET",
            // url:    'http://172.20.250.37'+'/api/'+endPoint,
            url: '<?=url('/')?>' + '/api/' + endPoint,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function(res) {
            console.log(res);
        });
    }


 //    sendPostRequest(loadingEle,messageDiv,endPoint,successBlock,postData,failBlock)


    function sendPostRequest1(endPoint, successBlock, postData) {


        $.ajax({
            method: "POST",
            url: '<?=url('/')?>' + '/api/' + endPoint,
            data: postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function(res) {
            console.log(res);
        });
    }






    function rightSide(row, index) { // my message
        var username = (row.sender_name) ? row.sender_name : row.entry_user_id.toString();
        var html = '<div class="messages"><div class="message-feed right">';
        var editLink = createEditLink(row, index, null);
        var msgTime = row.time_ago ? row.time_ago : (row.created_at ? row.created_at : '');
        //   html    += '<div class="message out no-avatar media" style=""><div class="body media-body text-right p-l-50">';
        html += '<div class="float-right pl-2" ><img src="<?= asset('/staffimages/'.auth()->user()->employee_id.'.jpg') ?>" class="rounded-circle"  alt="dp" width="40" height="40" ></a></div>';
        html += '<div class="media-body" style="display:block"  > <div class="mf-content" style="text-align:left" >    <b>' + username.toUpperCase() + '        <small style="margin-left:40px;" class="float-right">' + msgTime + '</small>  </b>';
        html += '<br>' + textFormat(row.message) + ' <br>  <span style="text-align:left"> ' + editLink + ' </span> </div></div>';
        html += '</div>';
        var replyArr = row.reply; /// Array.from(val.reply);

        if ((replyArr) && (replyArr.length > 0)) {
            $(replyArr).each(function(m_index, child_reply) {
                var childEditLink = createEditLink(child_reply, index, m_index);
                var username = (child_reply.sender_name) ? child_reply.sender_name : child_reply.entry_user_id.toString();
                html += '<div class="message-feed right mt-5">';
                html += '<div class="float-right pl-2"><img src="<?= asset('/staffimages/'.auth()->user()->employee_id.'.jpg') ?>" class="rounded-circle" alt="dp" width="50" height="50" > </div>';
                html += '<div class="media-body" style="display:block" > <div class="mf-content" style="text-align:left" > <b>' + username + '</b>';
                html += '<br>  ' + textFormat(child_reply.message) + ' <br><span>  ' + childEditLink + ' | <small><i class="icon-clock f-12 m-r-5 txt-muted d-inline-block"></i>' + child_reply.time_ago + '</small>  </span> </div> </div>';
                html += '</div>';
            });
        }

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

        var replyArr = row.reply; /// Array.from(val.reply);

        if ((replyArr) && (replyArr.length > 0)) {
            $(replyArr).each(function(m_index, child_reply) {
                var username = (child_reply.sender_name) ? child_reply.sender_name : child_reply.entry_user_id.toString();
                var childEditLink = createEditLink(child_reply, index, m_index);
                html += '<div class="message-feed media messages otherMessages" style="" data-msgID="' + row.id + '" >';
                html += '<div style="" class=float-left pr-2"><img src="<?= asset('/images/user-circle.png') ?>" class="img-circle img-chat-profile" alt="Me" width="70px" height="70px"></div>';
                html += '<div class="media-body"><div class="mf-content msg-reply"> Reply By : <b>' + username + '</b><br>' + textFormat(child_reply.message) + ' </div><div class="seen"> ' + childEditLink + ' | <i class="icon-clock f-12 m-r-5 txt-muted d-inline-block"></i><span><p class="d-inline-block">' + child_reply.time_ago + '</p></span><div class="clear"></div></div></div>';

                html += '</div>'
            });
        }

        html += '</div>';
        return html;
    }





    function editMessagePopup(index, childIndex) {
        if (childIndex != null) {
            var msgArr = messageArr[index].reply;
            var msg = msgArr[childIndex];
        } else {
            var msg = messageArr[index];
        }
        $('#edit-message').val(msg.message);
        $('#edit-msg-id').val(msg.id);
        $('#edit-message-modal').modal('show');
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
            html = '<div class="message-feed media mt-0"> <b>No Message Found!</b> </div>';
        }

        $('#ChatBody').html(html);
        //  $('.message-scrooler').scrollTop( $('.message-scrooler').height() );
        //  $('.message-scrooler').slideDown("slow");
        // $('#ChatBody').animate({
        //     scrollTop: $('.message-scrooler').height()
        // }, 800);

        // const ps1 = new PerfectScrollbar('#ChatBody', {
        //     useBothWheelAxes: false,
        //     suppressScrollX: false,
        // });

        // ps1.update();

       // $("#ChatBody").scrollBy(0,180)


    //    setTimeout(() => {
    //     $("#ChatBody").animate({ scrollTop: 15000 }, 1000);
    //    }, 2000);

 //    ps1.update();

    const container = document.querySelector('#ChatBody');
    console.log( '----->>>>> chatbody :: ', container.scrollHeight );
container.scrollTop = container.scrollHeight;



        //readMessage();
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
            sendPostRequest1('message/read', function(res) {
                // console.log('read', res);
            }, data);
        }
    }

    // function getMessage() {
    //     var endPoint = $('#endPoint').val();
    //     sendGetRequest1(endPoint, function(res) {
    //         if (res.status) {
    //             messageArr = res.data;
    //             reloadMessage();
    //         }
    //     });
    // }
    function getMessage() {
        var toUserId = $('#to_user_id').val();
        var formData    = new FormData();
        formData.append("mesg_bnu_un_id",0);
        formData.append("co_id", groupID);
        formData.append("to_user_id", toUserId);
        formData.append("viewFor", 'direct');
        formData.append("sender_name", '<?=auth()->user()->username;?>');
        $.ajax({
            type:'post',
            url: '<?=$mobileURL;?>/getMessages',
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            data:formData,
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
        var toUserId = $('#to_user_id').val();
        var formData    = new FormData();
        formData.append("mesg_bnu_un_id",0);
        formData.append("co_id", groupID);
        formData.append("to_user_id", toUserId);
        var endPoint = $('#endPoint').val();
        var lastMsg  = 0;
        if(messageArr.length > 0){
            var msg = messageArr[(messageArr.length)-1];
            lastMsg = msg.id;
        }
        // return false;
        var point = endPoint.replace('get','get-by-last-message-id');
        $.ajax({
            type:'POST',
            url: '<?=$mobileURL;?>/getMessages',
            // data:data,            
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            data : formData,        
            processData:false,
            contentType:false,
            success:function(res){
            // alert('bilal')
            if (res.status) {
                console.log('Before Count :: ', messageArr.length );
                messageArr.concat(res.data);


                // $(res.data).each(function(index,obj){
                //     messageArr.push(obj);
                // });


                console.log('Before Count :: ', messageArr.length );
                reloadMessage();
            }
                    
            },
            error: function (jqXHR, exception) {
                

            },               
        });
      
    }

    function replyMessagePopup(index) {
        // console.log('yes yes', index, messageArr);
        var msg = messageArr[index];
        $('#reply-mesg-tt_id').val(msg.tt_id);
        $('#reply-msg-id').val(msg.id);
        $('#reply-to-user-id').val(msg.entry_user_id);
        $('#reply-message-modal').modal('show');
    }

    function replyMessage() {
        var msg = $('#reply-message').val();
        if (msg.trim() == '') {
            return false;
        }

        var bi_form     = document.querySelector('#reply-message-form');
        var data        = new FormData(bi_form);

        sendPostRequest1('message/reply', function(res) {
            if (!(res.status)) {
                $('#error-p').html(res.message);
            } else {
                getMessage();
                $('#reply-message-modal').modal('hide');
                $('#reply-message').val('');
            }
        }, data);
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


    function showStudents() {
        sendGetRequest1('course/get-student-by-course?co_id=<?= $coID ?>', function(res) {
            $('#ChatList').html('Loading...');
            var studentHtml = '';
            $(res.data).each(function(index, row) {
                var studentImage = '<?=asset('/studentpics')?>/'+row.username.toUpperCase()+'.jpg';
                studentHtml += '<li class="list-group-item media p-4 border-top mt-0  chat_objects" id="chat_obj_'+row.id+'" >';
                studentHtml += '<div class="float-left pr-2"><img src="'+studentImage+'" alt="image" class="avatar avatar-md brround"></div>';
                studentHtml += '<div class="media-body"> <div class="list-group-item-heading text-default font-weight-semibold"> <a href="javascript:void(0);" onclick="setChat(\'student\',\''+ row.id +'\', \''+ row.username +'\');" >' + row.name + ' </a> </div> <small class="list-group-item-text text-muted"> '+row.username+' </small> </div>';
                studentHtml += '<span class="chat-time text-muted"> ... </span></li>';
            });
            if(studentHtml == ''){
                studentHtml = '<p style="text-align:center; vertical-align:center">No Students are founds.</p>';
            }
            var allClsHtml = '';
            allClsHtml += '<li class="list-group-item media p-4 border-top mt-0 chat_objects active" id="chat_obj_<?= $eCoID ?>" >';
            allClsHtml += '<div class="float-left pr-2"><img src="<?= asset('/images/users/5.jpg') ?>" alt="" class="avatar avatar-md brround"></div>';
            allClsHtml += '<div class="media-body"> <div class="list-group-item-heading text-default font-weight-semibold">  <a href="javascript:void(0);" onclick="setChat(\'course\',\'<?= $eCoID ?>\', \''+courseName+'\');" >  All Class </a> </div> <small class="list-group-item-text text-muted"> ... </small> </div>';
            allClsHtml += '<span class="chat-time text-muted"> ... </span></li>'+studentHtml;
            $('#ChatList').html(allClsHtml);
        });
    }


    function sendMessage() {
        var msg = $('#msg-input').val();
     
        if (msg.trim() == '') {
            return false;
        }

        var bi_form = document.querySelector('#messageForm');
        var data    = new FormData(bi_form);
        var inputMessage = $('#msg-input').val();
        var toUserId = $('#to_user_id').val();
        messageArr.push({
            'co_id' : groupID,
            'time_ago' : 'sending',
            'message' : $('#msg-input').val(),
            'sender_name' : username,
            'entry_user_id':uID,
            'to_user_id' : $('#to_user_id').val()
        });
        var collectionId=username+toUserId;
        reloadMessage();
        $('#msg-input').val('');
        //'message/send'
        $.ajax({
            type:'POST',
            url: '<?=$mobileURL;?>/send?mesg_bnu_un_id=0&message='+inputMessage+'&course_co_id='+groupID+'&viewFor=direct&to_user_id='+toUserId+'&entry_user_id='+uID+'',
            data:data,            
            headers: {
                    Authorization: 'Bearer <?=auth()->user()->access_token;?>',
                    Accept: 'application/json'
                    },
            processData:false,
            contentType:false,
        success:function(data){

    console.log('DEV API SEND RESPONSE:', data);

    messageArr.pop();

    if (data.status && data.data) {
        messageArr.push(data.data);
    }

    checkMessage();
    getMessage();
},
            error: function (jqXHR, exception) {
                

            },               
        });

        $.ajax({
            type:'GET',
            url: '<?=$mobileURL;?>/create_chat?sender_name='+username+'&message='+inputMessage+'&collection_id='+collectionId,
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


    // Query --> 43100 Syeda Asham Murad --> 485 --> f2021-269


    function updateMessage(){
        var msg = $('#edit-message').val();
        if(msg.trim() == ''){ return false; }
        
        var bi_form = document.querySelector('#edit-message-form');
        var data    = new FormData(bi_form);

        sendPostRequest('','','message/update',function (res) {
               //  $('#edit-message').val('');
                // console.log(res);
               if( !(res.status) ){
                   $('#error-p').html(res.message);
               }else{
                getMessage();
                $('#edit-message-modal').modal('hide');
                $('#edit-message').val('');
               }
        },data);
    }


    







    $(document).ready(function() {
        var co_id = $('#co_id').val();
       
        var url      = window.location.href; 
        var url = new URL(url);
        var reg = url.searchParams.get("reg");

       if(reg==null){
        setChat('course',co_id);
       }else{
        setChat('student',reg,reg)
        readMessages(0,co_id,reg)
       }


       
       //showStudents();

        $('#back-to-top').hide();

        //getMessage();
        // checkMessage();
        setInterval(() => {
             getMessage();
           checkMessage();
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
</script>
@stop
