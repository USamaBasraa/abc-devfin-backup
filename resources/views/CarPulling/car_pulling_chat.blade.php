<?php

// print_r($car_pulling);
// exit();
?>


@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<style>
     

    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
}
    div.dataTables_wrapper div.dataTables_filter input {
        width: 450px;
        height: 40px;
    }
}
    tr:nth-child(even) {
        background-color: #dddddd;
    }

    /* ============================================================
       FIGMA FACULTY CMS - CAR POOLING CHAT VISUAL OVERRIDES
       Page reference: Car Pooling Search (453:1250)
       Chat reference: User Messaging (2449:5)
       CSS only - Blade/PHP/HTML/JS logic and structure unchanged.
       ============================================================ */

    :root {
        --faculty-primary: #17675F;
        --faculty-primary-hover: #12564F;
        --faculty-primary-soft: rgba(23, 103, 95, 0.10);
        --faculty-primary-muted: rgba(23, 103, 95, 0.50);
        --faculty-text: #252525;
        --faculty-heading: #0F172A;
        --faculty-body: #334155;
        --faculty-muted: #94A3B8;
        --faculty-line: #E2E8F0;
        --faculty-bubble: #F1F5F9;
        --faculty-white: #FFFFFF;
        --faculty-danger: #C84E4E;
    }

    /* ---------- Page card ---------- */
    .card > .card-header.bg-primary {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        min-height: auto !important;
        padding: 0 0 14px 0 !important;
        color: var(--faculty-text) !important;
    }

    .card > .card-header.bg-primary .card-title,
    .card > .card-header.bg-primary .card-title.text-white {
        margin: 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 20px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        letter-spacing: 0 !important;
    }

    .card > .card-block {
        padding: 0 !important;
        background: transparent !important;
    }

    .card > .card-block > .tab-menu-heading {
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    /* ---------- Figma pill tabs ---------- */
    .card > .card-block .panel-tabs {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        min-height: 66px !important;
        margin: 0 0 20px 0 !important;
        padding: 0 0 15px 0 !important;
        list-style: none !important;
        border: 0 !important;
        border-bottom: 2px solid rgba(23, 103, 95, 0.13) !important;
    }

    .card > .card-block .panel-tabs > li {
        position: relative !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 50px !important;
        background: transparent !important;
    }

    .card > .card-block .panel-tabs > li > a {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 150px !important;
        height: 50px !important;
        margin: 0 !important;
        padding: 0 22px !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary-muted) !important;
        box-shadow: none !important;
        text-decoration: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 15px !important;
        line-height: 50px !important;
        font-weight: 500 !important;
        transition: background .18s ease, color .18s ease !important;
    }

    .card > .card-block .panel-tabs > li > a i {
        margin-right: 8px !important;
        color: inherit !important;
    }

    .card > .card-block .panel-tabs > li > a.active,
    .card > .card-block .panel-tabs > li > a.active.text-white {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block .panel-tabs > li > a.active::after {
        content: '' !important;
        position: absolute !important;
        left: 0 !important;
        bottom: -17px !important;
        width: 100% !important;
        height: 3px !important;
        border-radius: 3px !important;
        background: var(--faculty-primary) !important;
    }

    .card > .card-block .panel-tabs > li > a:not(.active):hover {
        background: rgba(23, 103, 95, 0.15) !important;
        color: var(--faculty-primary) !important;
    }

    .card > .card-block .panel-tabs .badge-danger {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 22px !important;
        height: 22px !important;
        margin-left: 8px !important;
        padding: 0 6px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #FFFFFF !important;
        color: var(--faculty-primary) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .card > .card-block .tab-menu-heading > hr {
        display: none !important;
    }

    .card > .card-block .tab-menu-heading > h4 {
        margin: 0 0 14px 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 16px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    /* ---------- DataTables controls ---------- */
    .card > .card-block #simpletable_wrapper {
        width: 100% !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    .card > .card-block #simpletable_wrapper > .row:first-child {
        margin: 0 0 14px 0 !important;
        align-items: center !important;
    }

    .card > .card-block #simpletable_wrapper > .row:first-child > div {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .card > .card-block #simpletable_filter {
        width: 100% !important;
        margin: 0 !important;
        text-align: right !important;
    }

    .card > .card-block #simpletable_filter label {
        display: inline-block !important;
        width: 100% !important;
        max-width: 790px !important;
        margin: 0 !important;
        color: transparent !important;
        font-size: 0 !important;
    }

    .card > .card-block #simpletable_filter input {
        width: 100% !important;
        height: 50px !important;
        margin: 0 !important;
        padding: 0 16px !important;
        border: 1px solid rgba(23, 103, 95, 0.08) !important;
        border-radius: 5px !important;
        outline: 0 !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        font-weight: 400 !important;
    }

    .card > .card-block #simpletable_filter input:focus {
        border-color: rgba(23, 103, 95, 0.35) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.07) !important;
    }

    .card > .card-block #simpletable_length {
        color: var(--faculty-text) !important;
        font-size: 13px !important;
    }

    .card > .card-block #simpletable_length select {
        min-width: 82px !important;
        height: 32px !important;
        border: 1px solid var(--faculty-text) !important;
        border-radius: 50px !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-size: 13px !important;
    }

    /* ---------- Chat list table ---------- */
  .card > .card-block .dt-responsive {
    width: 100% !important;
    overflow: visible !important;
    border-radius: 5px !important;
}

    .card > .card-block #simpletable {
        width: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    .card > .card-block #simpletable thead th {
        /* height: 54px !important; */
        padding: 13px 20px !important;
        border: 0 !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
    }

    .card > .card-block #simpletable thead th:first-child {
        border-top-left-radius: 5px !important;
    }

    .card > .card-block #simpletable thead th:last-child {
        border-top-right-radius: 5px !important;
    }

    .card > .card-block #simpletable tbody tr,
    .card > .card-block #simpletable tbody tr:nth-child(even),
    .card > .card-block #simpletable tbody tr:nth-child(odd) {
        background: #FFFFFF !important;
    }

    .card > .card-block #simpletable tbody td {
        min-height: 52px !important;
        padding: 14px 20px !important;
        border-top: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        vertical-align: middle !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 13px !important;
        line-height: 1.45 !important;
        font-weight: 400 !important;
    }

    .card > .card-block #simpletable tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .card > .card-block #simpletable tbody tr:hover td {
        background: rgba(23, 103, 95, 0.035) !important;
    }

    /* Conversation icon */
    .card > .card-block #simpletable .option-dots.new-list2 {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 36px !important;
        height: 36px !important;
        border: 0 !important;
        border-radius: 50% !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
        text-decoration: none !important;
        transition: background .18s ease, color .18s ease !important;
    }

    .card > .card-block #simpletable .option-dots.new-list2 svg {
        width: 18px !important;
        height: 18px !important;
        transform: none !important;
        scale: 1 !important;
        fill: currentColor !important;
    }

    .card > .card-block #simpletable .option-dots.new-list2:hover {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    /* ---------- DataTables footer ---------- */
    .card > .card-block #simpletable_wrapper > .row:last-child {
        margin: 14px 0 0 0 !important;
        align-items: center !important;
    }

    .card > .card-block #simpletable_info {
        padding-top: 0 !important;
        color: var(--faculty-text) !important;
        font-size: 12px !important;
    }

    .card > .card-block #simpletable_paginate {
        padding-top: 0 !important;
    }

    .card > .card-block #simpletable_paginate .pagination {
        gap: 4px !important;
        margin: 0 !important;
        justify-content: flex-end !important;
    }

    .card > .card-block #simpletable_paginate .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 30px !important;
        height: 30px !important;
        padding: 0 8px !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: transparent !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-size: 12px !important;
    }

    .card > .card-block #simpletable_paginate .page-item.active .page-link {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block #simpletable_paginate .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
    }

    /* ============================================================
       FIGMA USER MESSAGING - existing Bootstrap modal, styled only
       ============================================================ */
    #large-Modal_chat {
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    #large-Modal_chat .modal-dialog.modal-dialog-right.chatbox {
        width: calc(100% - 48px) !important;
        max-width: 1024px !important;
        margin: 24px auto !important;
        min-height: calc(100% - 48px) !important;
        display: flex !important;
        align-items: center !important;
    }

    #large-Modal_chat .modal-content.chat {
        width: 100% !important;
        height: min(760px, calc(100vh - 48px)) !important;
        overflow: hidden !important;
        border: 0 !important;
        border-radius: 16px !important;
        background: #FFFFFF !important;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.16) !important;
    }

    #large-Modal_chat .modal-content.chat > .card {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 16px !important;
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    /* Figma 72px white messaging header */
    #large-Modal_chat .action-header {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex: 0 0 72px !important;
        min-height: 72px !important;
        padding: 12px 16px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-heading) !important;
        box-shadow: none !important;
    }

    #large-Modal_chat .action-header .float-left {
        display: flex !important;
        align-items: center !important;
        margin-left: 0 !important;
    }

    /* Figma header has no large avatar; keep DOM but visually remove it */
    #large-Modal_chat .action-header .img_cont {
        display: none !important;
    }

    #large-Modal_chat .action-header .align-items-center.mt-2 {
        margin-top: 0 !important;
        color: var(--faculty-heading) !important;
    }

    #large-Modal_chat .action-header h5,
    #large-Modal_chat .action-header #title {
        margin: 0 0 3px 0 !important;
        color: var(--faculty-heading) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 16px !important;
        line-height: 1.2 !important;
        font-weight: 600 !important;
    }

    #large-Modal_chat .action-header .dot-label.bg-success {
        display: inline-block !important;
        width: 8px !important;
        height: 8px !important;
        margin-right: 6px !important;
        border-radius: 50% !important;
        background: #10B981 !important;
        vertical-align: middle !important;
    }

    #large-Modal_chat .action-header .fs-12 {
        color: var(--faculty-muted) !important;
        font-size: 12px !important;
        text-transform: capitalize !important;
    }

    #large-Modal_chat .action-header .ah-actions {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    #large-Modal_chat .action-header .ah-actions a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 16px !important;
        color: var(--faculty-muted) !important;
        text-decoration: none !important;
    }

    #large-Modal_chat .action-header .ah-actions svg {
        width: 18px !important;
        height: 18px !important;
        fill: currentColor !important;
    }

    #large-Modal_chat .action-header .ah-actions a:hover {
        background: var(--faculty-bubble) !important;
        color: var(--faculty-heading) !important;
    }

    /* Chat area */
    #large-Modal_chat .msg_card_body {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        background: #FFFFFF !important;
    }

    #large-Modal_chat .msg_card_body > .col-lg-12 {
        height: 100% !important;
        padding: 0 !important;
    }

    #large-Modal_chat .widget-chat-box {
        height: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    #large-Modal_chat .widget-chat-box > .card-block {
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        padding: 0 !important;
        background: #FFFFFF !important;
    }

    /* Messages are visually above the composer without moving DOM nodes */
    #large-Modal_chat .widget-chat-box > .card-block > #msg_cotainer_send {
        order: 1 !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 24px 16px !important;
        background: #FFFFFF !important;
        scroll-behavior: smooth !important;
    }

    #large-Modal_chat .widget-chat-box > .card-block > form {
        order: 2 !important;
        flex: 0 0 auto !important;
        margin: 0 !important;
        padding: 12px 16px 18px !important;
        border-top: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
    }

    #large-Modal_chat .widget-chat-box > .card-block > hr {
        display: none !important;
    }

    /* Hide only the duplicate direct-child legacy message containers; first live container stays intact */
    #large-Modal_chat .msg_card_body > .col-lg-12 > #msg_cotainer,
    #large-Modal_chat .msg_card_body > .col-lg-12 > #msg_cotainer_send {
        display: none !important;
    }

    #large-Modal_chat #sendMessageCarPulling_form .input-group {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        width: 100% !important;
    }

    #large-Modal_chat #message {
        flex: 1 1 auto !important;
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 16px !important;
        border: 0 !important;
        border-radius: 22px !important;
        outline: 0 !important;
        background: var(--faculty-bubble) !important;
        color: var(--faculty-body) !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        font-weight: 400 !important;
    }

    #large-Modal_chat #message::placeholder {
        color: var(--faculty-muted) !important;
        opacity: 1 !important;
    }

    #large-Modal_chat #message:focus {
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
    }

    #large-Modal_chat #messageBtn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 36px !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        min-height: 36px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 18px !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        box-shadow: none !important;
    }

    #large-Modal_chat #messageBtn svg {
        width: 18px !important;
        height: 18px !important;
        fill: currentColor !important;
    }

    #large-Modal_chat #messageBtn:hover {
        background: var(--faculty-primary-hover) !important;
    }

    /* JS-generated message rows */
    #large-Modal_chat .widget-chat-box #msg_cotainer_send > .d-flex {
        width: 100% !important;
        margin-bottom: 16px !important;
    }

    #large-Modal_chat .widget-chat-box #msg_cotainer_send .img_cont_msg {
        display: none !important;
    }

    #large-Modal_chat .widget-chat-box #msg_cotainer_send .msg_cotainer {
        width: auto !important;
        max-width: 520px !important;
        margin: 0 !important;
        padding: 12px !important;
        border: 0 !important;
        border-radius: 18px !important;
        box-shadow: none !important;
        word-break: break-word !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 20px !important;
        font-weight: 400 !important;
    }

    /* In current JS: bg-primary = received, bg-success = sent */
    #large-Modal_chat .widget-chat-box #msg_cotainer_send .msg_cotainer.bg-primary {
        background: var(--faculty-bubble) !important;
        color: var(--faculty-body) !important;
    }

    #large-Modal_chat .widget-chat-box #msg_cotainer_send .msg_cotainer.bg-success {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    /* Modal backdrop closer to the clean Figma presentation */
    .modal-backdrop.show {
        opacity: .28 !important;
    }

    @media (max-width: 991.98px) {
        .card > .card-block .panel-tabs {
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
        }

        .card > .card-block .panel-tabs > li > a {
            min-width: 138px !important;
            height: 44px !important;
            padding: 0 18px !important;
            line-height: 44px !important;
            font-size: 13px !important;
        }

        .card > .card-block #simpletable_filter label {
            max-width: none !important;
        }

        #large-Modal_chat .modal-dialog.modal-dialog-right.chatbox {
            width: calc(100% - 20px) !important;
            margin: 10px auto !important;
            min-height: calc(100% - 20px) !important;
        }

        #large-Modal_chat .modal-content.chat {
            height: calc(100vh - 20px) !important;
            border-radius: 12px !important;
        }

        #large-Modal_chat .widget-chat-box #msg_cotainer_send .msg_cotainer {
            max-width: 82% !important;
        }
    }
    /* =========================================================
   HORIZONTAL SCROLL ONLY FOR TABLE
   Pagination / Info remain outside
   ========================================================= */

.card > .card-block #simpletable_wrapper {
    width: 100% !important;
    overflow: visible !important;
}

/* Only table area scrolls */
.card > .card-block .dataTables_scroll {
    width: 100% !important;
}

.card > .card-block .dataTables_scrollBody {
    width: 100% !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
}

/* Header moves with table horizontally */
.card > .card-block .dataTables_scrollHead {
    width: 100% !important;
    overflow: hidden !important;
}

/* Pagination + Showing text outside scrollbar */
.card > .card-block #simpletable_wrapper > .row:last-child {
    width: 100% !important;
    margin: 14px 0 0 0 !important;
    position: relative !important;
    overflow: visible !important;
}

.card > .card-block #simpletable_info {
    position: relative !important;
    overflow: visible !important;
}

.card > .card-block #simpletable_paginate {
    position: relative !important;
    overflow: visible !important;
}
/* Visible DataTables header */
.card > .card-block .dataTables_scrollHead thead th {
    /* height: 54px !important; */
    padding: 13px 20px !important;
    background: var(--faculty-primary) !important;
    color: #FFFFFF !important;
    border: 0 !important;
    white-space: nowrap !important;
    font-family: 'Montserrat', Arial, sans-serif !important;
    font-size: 14px !important;
    font-weight: 500 !important;
}

/* Original internal sizing header hide/collapse */
.card > .card-block .dataTables_scrollBody #simpletable thead th {
    height: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    border: 0 !important;
    line-height: 0 !important;
}

.card > .card-block .dataTables_scrollBody #simpletable thead th > div {
    height: 0 !important;
    overflow: hidden !important;
}
/* Counter dropdown arrow */
.card > .card-block #simpletable_length select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;

    padding: 0 30px 0 12px !important;

    background-color: #ffffff !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7' fill='none'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23252525' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 10px center !important;
    background-size: 12px 7px !important;
}

/* Counter left and Search right */
.card > .card-block #simpletable_wrapper > .row:first-child {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    margin-right: 0 !important;
    margin-left: 0 !important;
}

.card > .card-block
#simpletable_wrapper > .row:first-child > div:last-child {
    display: flex !important;
    justify-content: flex-end !important;
    flex: 1 1 auto !important;
    width: auto !important;
    max-width: none !important;
    margin-left: auto !important;
    padding-right: 0 !important;
}

.card > .card-block #simpletable_filter {
    float: none !important;
    width: auto !important;
    margin: 0 0 14px auto !important;
    text-align: right !important;
}

.card > .card-block #simpletable_filter label {
    display: flex !important;
    align-items: center !important;
    width: auto !important;
    max-width: none !important;
    gap: 8px;
    margin: 0 !important;

    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
}

.card > .card-block #simpletable_filter input {
    width: 250px !important;
    height: 40px !important;
    margin: 0 !important;
    padding: 8px 14px !important;

    border: 1px solid #dbe3ea !important;
    border-radius: 5px !important;
    background: #ffffff !important;

    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
}
/* Keep DataTable headings in Title Case */
#simpletable thead th,
#simpletable_wrapper .dataTables_scrollHead thead th {
    text-transform: none !important;
}
</style>
    <div class="card" style="background: none !important;">
        <div class="card-header bg-primary"
            style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
            <div class="card-title text-white">
                Car Pooling Search
            </div>
        </div>
        <div class="card-block">
            <div class="tab-menu-heading">
                <div class="tabs-menu ">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs">
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-details') }}" ><i
                                    class="fa fa-car fa-lg mr-2"></i>Ride Search</a>
                        </li>
                        <li class="mr-2 p-2 rounded"> <a href="{{ url('/car-pooling-inbox') }}" ><i
                                    class="fa fa-inbox fa-lg mr-1"></i>Ride Request </a>
                        </li>
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-chat') }}" class="active text-white"><i
                            class="fa fa-commenting-o fa-lg mr-2"></i>ChatBox
                            <span
                            class="badge badge-danger">{{ count($car_pulling) }}</span>
                     </a>
                </li>
                    </ul>
                </div>

                
                <hr>
                <h4>Details</h4>
                <div class="dt-responsive ">
                    <table id="simpletable" class="table table-striped table-bordered dt-responsive">
                        <thead>
                            <tr>
                                   <th class="wd-15p border-bottom-0">Username</th>
                                       <th class="wd-25p border-bottom-0">Conversation</th>
                                       <!-- <th class="wd-15p border-bottom-0">Ride Type</th> -->
                                       <!-- <th class="wd-15p border-bottom-0">Vehicle Capacity</th> -->
                                       <!-- <th class="wd-15p border-bottom-0">Preferred Locations</th> -->
                                    
                               </tr>
                        </thead>
                        <!-- <tbody> -->
                            @foreach ($car_pulling as $item)
                            <tr>
                                <td>{{ $item->entry_user_id }}</td>
                                <input type="hidden" id="userId"
                                    value="{{ $item->entry_user_id }}">
                                <td>

                                    <a class="option-dots new-list2" href="#"
                                        data-toggle="modal"
                                        onclick="getUsername('<?= $item->entry_user_id ?>','<?= $item->cp_id ?>')"
                                        data-target="#large-Modal_chat"><svg
                                            class="svg-icon" style="scale:1.3;"
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="24" viewBox="0 0 24 24"
                                            width="24">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path
                                                d="M20 4H4v13.17L5.17 16H20V4zm-6 10H6v-2h8v2zm4-3H6V9h12v2zm0-3H6V6h12v2z"
                                                opacity=".3" />
                                            <path
                                                d="M20 18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14zm-16-.83V4h16v12H5.17L4 17.17zM6 12h8v2H6zm0-3h12v2H6zm0-3h12v2H6z" />
                                        </svg></a>

                                </td>
   
                            </tr>
                        @endforeach
                        <!-- </tbody> -->
                    </table>
                </div>

            </div>
        </div>

        <!-- Modal large-->
        <div class="modal fade" id="large-Modal_chat" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right chatbox" role="document">
                <div class="modal-content chat border-0">
                    <div class="card overflow-hidden mb-0 border-0">
                        <!-- action-header -->
                        <div class="action-header">
                            <div class="float-left hidden-xs d-flex ml-2">
                                <div class="img_cont mr-3">
                                    <img src="../../assets/images/users/16.jpg"
                                        class="rounded-circle user_img avatar avatar-md" alt="img">
                                </div>
                                <div class="align-items-center mt-2 text-white">
                                    <h5 class="mb-0"><span id="title"></span></h5>
                                    <span class="dot-label bg-success"></span><span class="mr-3 fs-12">online</span>
                                </div>
                            </div>
                            <ul class="ah-actions actions align-items-center">
                                <li>
                                    <a href="" class="" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                height="24" viewBox="0 0 24 24" width="24">
                                                <path d="M0 0h24v24H0V0z" fill="none" />
                                                <path
                                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                                            </svg></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- action-header end -->

                        <!-- msg_card_body -->
                        <div class="card-body msg_card_body">
                            <div class="col-lg-12">
                                <div class="card widget-chat-box" style="border-top:0px solid">
                                    <div class="card-block">
                                        <form enctype="multipart/form-data"  method="post" name="sendMessageCarPulling_form"
                                            id="sendMessageCarPulling_form">
                                            @csrf
                                            <div class="input-group">
                                                <input type="text" id="message" name='message' class="form-control"
                                                    placeholder="Please Enter Message..."
                                                    style="    border: 1px solid #424e79 !important;" />
                                                <input type="hidden" id="sendToUsername" name="sendToUsername">
                                                <input type="hidden" id="cp_id" name="cp_id">
                                                <input type="hidden" id="login_username"
                                                    value="<?= auth()->user()->username ?>" name="login_username">
                                                <button type="button" id="messageBtn"
                                                    onclick="btn_sendMessageCarPulling()" class="btn btn-primary ">
                                                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                        height="24" viewBox="0 0 24 24" width="24">
                                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                                        <path d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z"
                                                            opacity=".3" />
                                                        <path
                                                            d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                        <hr style="border-top:2px solid tomato">
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
                </div>
            </div>
        </div>

        <table id="itrc"></table>

    @stop

    @section('javascript')
        <script type="text/javascript">


function check_validation(validation_array){
					var count_errors = 0;
					$.each(validation_array, function( index, value ) {
						//alert(value);
						if ($('#'+value).val()==="" || $('#'+value).val()===null || $('#'+value).tokenfield('getTokensList')==="" || $('#'+value).tokenfield('getTokensList')===null){  
							$('#'+value).removeClass('is-valid').addClass('is-invalid');
							count_errors++;
						}
						else{
							$('#'+value).removeClass('is-invalid').addClass('is-valid');
							setTimeout(function(){
							$('#'+value).removeClass('is-valid');
							},2000);
						} 
					});
					if(count_errors>0){
					return 0;
					}else{
					return 1;
					}
	}

	function sendGetRequest(endPoint,successBlock) {
        $.ajax({
            method: "GET",
            url:    url+'/api/'+endPoint,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
            console.log(res);
        });
    }


    function sendPostRequest(endPoint,successBlock,postData) {
        $.ajax({
            method: "POST",
            url:    url+'/api/'+endPoint,
            data : postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
            // alert('bilal')
        });
    }









            document.addEventListener('DOMContentLoaded', function() {
                // disable enter key on form
                document.addEventListener('keydown', function(event) {
                    if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                        event.preventDefault();
                        return false;
                    }
                });
            });


            // $(document).ready(function() {
            //     $('#simpletable').DataTable();

            // });
            $(document).ready(function() {
    $('#simpletable').DataTable({
        scrollX: true,
        autoWidth: false
    });
});


            function acceptRide(id, cap, userID, AuthUserNamee,textNocap,parent_cpID,cp_partner) {
                // sendMail();
                // var bi_form = document.querySelector('#car_pulling');
               
                var data = new FormData();
               
                data.append("user_ID", userID);
                data.append("AuthUserNamee", AuthUserNamee);
                data.append("textNocap", textNocap);
                data.append("parent_cpID", parent_cpID);
                data.append("cpPartner", cp_partner);
                sendPostRequest('update-Capacity?cp_id=' + id + '&cap=' + cap, function(res) {
                    //console.log('Hello Baby', res);
                    //alert(res.status);
                    if (res.status) {
                        $('#successMsg' + userID).css('color', 'green');
                        $('#successMsg' + userID).html('Request Accepted Successfully');
                        // getRequest();     
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {

                    }
                }, data);
            }

            // var flag=0;
            $(document).ready(function() {
                $('#res-config').DataTable();
                setInterval(function() {
                    var username = $('#sendToUsername').val();
                    var id = $('#cp_id').val();
                    getUsername(username, id);

                }, 5000);
            });
            function getUsername(username, id) {
                var loginUsername = $('#login_username').val();
                $('#title').html(username);
                $('#sendToUsername').val(username);
                $('#cp_id').val(id);
                if (!id) {
                    return false;
                }
                var authUsername = '<?= auth()->user()->username ?>';
                $.ajax({
                    type: 'get',
                    url: 'api/get-car-pulling-message?cp_id=' + id + '&username=all' + '&authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    // data:formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            var rightSide = '';
                            var leftSide = '';
                            $('#msg_cotainer').html('');
                            $('#msg_cotainer_send').html('');

                            $(res.data).each(function(index, row) {
                                if (loginUsername != row.entry_user_id) {
                                    var divone = '<div class="d-flex justify-content-start">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-primary text-white"><span>' + row
                                        .message + '</span></div>';
                                } else {
                                    var divone = '<div class="d-flex justify-content-end">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-success text-white"><span>' + row
                                        .message + '</span></div>';

                                }
                                var row_add = divone + '' + divtwo + ' ' + divthree + '</div';
                                $('#msg_cotainer_send').append(row_add);
                            });

                            if (rightSide == '' && leftSide == '') {
                                rightSide = '<tr><td colspan="4"> No Message FOund Yet! </td></tr>';
                            }

                        } else {

                        }
                    },
                    error: function(jqXHR, exception) {},
                });
            }

            function btn_sendMessageCarPulling() {

                event.preventDefault()
                let message = document.getElementById("message").value.trim();
                if (message === '') {
                    return false;
                }
                //         // $('#messageBtn').attr('disabled', true);
                var bi_form = document.querySelector('#sendMessageCarPulling_form');
                var formData = new FormData(bi_form);
                var authUsername = '<?= auth()->user()->username ?>';
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: 'api/car-pulling-message?authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#message').val('');
                            var username = $('#sendToUsername').val();
                            var id = $('#cp_id').val();
                            getUsername(username, id)
                            // getRequest();     
                            setTimeout(function() {
                                $('#successMsg').html('');
                                $('#messageBtn').removeAttr("disabled");
                            }, 2000);

                        }
                    }

                });


            }
        </script>
    @stop
