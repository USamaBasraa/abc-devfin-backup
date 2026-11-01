@extends('layouts.app')
@section('content')

{{-- @dd("12434"); --}}
<style>
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        border: none;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .card-header {
        border-bottom: none;
        border-radius: 8px 8px 0 0 !important;
        font-weight: 600;
    }

    .nav-tabs {
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 20px;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #555 !important;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s;
        position: relative;
        background: transparent !important;
        font-size: 14px !important;
    }

    .nav-tabs .nav-link.active {
        color: #4454c3 !important;
        font-weight: 700 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #4454c3;
    }

    .table thead th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 700;
    }

    .badge-ride {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 50px;
    }

    /* Chat Modal Styles */
    .modal-content.chat {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .modal-header.chat-header {
        background: linear-gradient(135deg, #4454c3 0%, #2d3bb1 100%);
        color: #fff;
        border-bottom: none;
        padding: 1.2rem;
    }

    .modal-body.chat-body {
        height: 450px;
        overflow-y: auto;
        background: #f8f9fa;
        padding: 20px;
        scroll-behavior: smooth;
    }

    .modal-footer.chat-footer {
        background: #fff;
        border-top: 1px solid #eee;
        padding: 15px;
    }

    .msg_container {
        max-width: 85%;
        padding: 12px 16px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        line-height: 1.4;
        position: relative;
    }

    .bg-sent {
        background: #4454c3 !important;
        color: #fff !important;
        border-radius: 18px 18px 0 18px;
        margin-left: auto;
        box-shadow: 0 4px 10px rgba(68, 84, 195, 0.2);
    }

    .bg-received {
        background: #fff !important;
        color: #333 !important;
        border-radius: 18px 18px 18px 0;
        margin-right: auto;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
    }

    .chat-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 12px;
    }

    .chat-user-info h6 {
        margin: 0;
        line-height: 1.2;
        font-weight: 600;
        color: #fff;
    }

    .chat-user-info small {
        opacity: 0.8;
        font-size: 0.75rem;
        color: #fff;
    }

    /* Chat Layout Overhaul (Top Input) */
    .chat-input-row {
        padding: 15px;
        background: #fff;
        border-bottom: 2px solid tomato;
    }

    .btn-send-modern {
        border-radius: 4px;
        background: #4454c3;
        border: none;
        color: #fff;
        padding: 0 20px;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .btn-send-modern:hover {
        background: #2d3bb1;
    }

    .form-control-chat {
        border: 1.5px solid #ddd;
        padding: 10px 15px;
        border-radius: 4px;
        box-shadow: none !important;
    }

    .modal-body.chat-body {
        height: 400px;
    }

    /* Modern Chat Icon Button */
    .btn-modern-chat {
        background: transparent;
        border: none;
        padding: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        outline: none !important;
    }

    .btn-modern-chat i {
        font-size: 24px;
        color: #4454c3;
        opacity: 0.7;
        transition: all 0.3s;
    }

    .btn-modern-chat:hover i {
        opacity: 1;
        transform: scale(1.15);
        color: #2d3bb1;
    }

    .btn-modern-chat:hover {
        background: rgba(68, 84, 195, 0.05);
        border-radius: 50%;
    }

    /* ============================================================
       FIGMA FACULTY CMS — SEARCH RIDE VISUAL OVERRIDES
       Reference: Faculty CMS Design / Search Ride (node 453:1250)
       Styling only. Existing Blade/PHP/HTML/JS logic is untouched.
       ============================================================ */

    :root {
        --faculty-primary: #17675F;
        --faculty-primary-soft: rgba(23, 103, 95, 0.10);
        --faculty-primary-muted: rgba(23, 103, 95, 0.50);
        --faculty-text: #252525;
        --faculty-muted: #949494;
        --faculty-line: #E3E9E8;
        --faculty-surface: #FFFFFF;
        --faculty-page: #F6FAF9;
        --faculty-danger: #C54C4C;
    }

    /* Keep the current CMS layout; restyle only this screen's content card. */
    .card.pl-3 {
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding-left: 0 !important;
        margin-bottom: 24px !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        color: var(--faculty-text);
    }

    .card.pl-3 > .card-header {
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 0 0 14px 0 !important;
        min-height: auto !important;
    }

    .card.pl-3 > .card-header .card-title {
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 20px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        margin: 0 !important;
        letter-spacing: 0 !important;
    }

    .card.pl-3 > .card-body {
        background: transparent !important;
        padding: 0 !important;
    }

    /* Figma pill tabs + underline rail. */
    .card.pl-3 .tab-menu-heading,
    .card.pl-3 .tabs-menu {
        background: transparent !important;
        border: 0 !important;
        padding: 0 !important;
    }

    .card.pl-3 .nav-tabs {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        min-height: 66px !important;
        margin: 0 0 20px 0 !important;
        padding: 0 0 15px 0 !important;
        border: 0 !important;
        border-bottom: 2px solid rgba(23, 103, 95, 0.13) !important;
    }

    .card.pl-3 .nav-tabs .nav-item {
        margin: 0 !important;
    }

    .card.pl-3 .nav-tabs .nav-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 150px !important;
        height: 50px !important;
        padding: 0 22px !important;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary-muted) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 15px !important;
        line-height: 50px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: background .18s ease, color .18s ease !important;
    }

    .card.pl-3 .nav-tabs .nav-link i {
        margin-right: 8px !important;
    }

    .card.pl-3 .nav-tabs .nav-link.active,
    .card.pl-3 .nav-tabs .nav-link.active:hover,
    .card.pl-3 .nav-tabs .nav-link.active:focus {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        font-weight: 500 !important;
    }

    .card.pl-3 .nav-tabs .nav-link:not(.active):hover {
        background: rgba(23, 103, 95, 0.15) !important;
        color: var(--faculty-primary) !important;
    }

    .card.pl-3 .nav-tabs .nav-link.active::after {
        content: '' !important;
        position: absolute !important;
        left: 0 !important;
        bottom: -17px !important;
        width: 100% !important;
        height: 3px !important;
        border-radius: 3px !important;
        background: var(--faculty-primary) !important;
    }

    /* Figma source does not repeat a second large section heading here. */
    .card.pl-3 .d-flex.align-items-center.justify-content-between.mb-4 {
        margin-bottom: 14px !important;
    }

    .card.pl-3 .d-flex.align-items-center.justify-content-between.mb-4 h4 {
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 16px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
        margin: 0 !important;
    }

    /* DataTables search bar — visually follows the 50px Figma search field. */
    .card.pl-3 #simpletable_wrapper {
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    .card.pl-3 #simpletable_wrapper > .row:first-child {
        margin-left: 0 !important;
        margin-right: 0 !important;
        align-items: center !important;
    }

    .card.pl-3 #simpletable_wrapper > .row:first-child > div {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .card.pl-3 #simpletable_filter {
        width: 100% !important;
        margin: 0 0 14px 0 !important;
        text-align: left !important;
    }

    .card.pl-3 #simpletable_filter label {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        color: transparent !important;
        font-size: 0 !important;
    }

    .card.pl-3 #simpletable_filter input {
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

    .card.pl-3 #simpletable_filter input:focus {
        border-color: rgba(23, 103, 95, 0.35) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.07) !important;
    }

    /* Keep DataTables functionality intact but make the control unobtrusive. */
    .card.pl-3 #simpletable_length {
        margin-bottom: 12px !important;
        color: var(--faculty-text) !important;
        font-size: 13px !important;
    }

    .card.pl-3 #simpletable_length select {
        min-width: 82px !important;
        height: 32px !important;
        border: 1px solid var(--faculty-text) !important;
        border-radius: 50px !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-size: 13px !important;
    }

    /* Table surface and green Figma header. */
    .card.pl-3 .table-responsive {
        border-radius: 5px !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        /* background: #FFFFFF !important; */
    }

    .card.pl-3 #simpletable {
        width: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    /* .card.pl-3 #simpletable thead th {
        height: 54px !important;
        padding: 13px 14px !important;
        border: 0 !important;
        border-bottom: 0 !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
    } */

    /* DataTable visible cloned header */
.card.pl-3 .dataTables_scrollHead thead th {
    height: 17px !important;
    padding: 13px 14px !important;
    border: 0 !important;
    background: var(--faculty-primary) !important;
    color: #FFFFFF !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
    font-family: 'Montserrat', Arial, sans-serif !important;
    font-size: 14px !important;
    line-height: 1.2 !important;
    font-weight: 500 !important;
}


/* DataTables original sizing header - don't display */
.card.pl-3 .dataTables_scrollBody #simpletable thead th {
    height: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    border: 0 !important;
    line-height: 0 !important;
}

.card.pl-3 .dataTables_scrollBody #simpletable thead th > div {
    height: 0 !important;
    overflow: hidden !important;
}

    /* .card.pl-3 #simpletable thead th:first-child {
        border-top-left-radius: 5px !important;
    }

    .card.pl-3 #simpletable thead th:last-child {
        border-top-right-radius: 5px !important;
    } */

    .card.pl-3 .dataTables_scrollHead thead th:first-child {
    border-top-left-radius: 5px !important;
}

.card.pl-3 .dataTables_scrollHead thead th:last-child {
    border-top-right-radius: 5px !important;
}

    .card.pl-3 #simpletable tbody td {
        padding: 13px 14px !important;
        border-top: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background-color: transparent !important;
        color: var(--faculty-text) !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 13px !important;
        line-height: 1.45 !important;
        font-weight: 400 !important;
    }

    .card.pl-3 #simpletable tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .card.pl-3 #simpletable.table-striped tbody tr:nth-of-type(odd),
    .card.pl-3 #simpletable.table-striped tbody tr:nth-of-type(even) {
        background: #FFFFFF !important;
    }

    .card.pl-3 #simpletable tbody tr:hover td {
        background: rgba(23, 103, 95, 0.035) !important;
    }

    .card.pl-3 #simpletable .font-weight-600,
    .card.pl-3 #simpletable .font-weight-bold {
        font-weight: 500 !important;
    }

    .card.pl-3 #simpletable .text-dark,
    .card.pl-3 #simpletable .text-muted,
    .card.pl-3 #simpletable .text-primary {
        color: var(--faculty-text) !important;
    }

    .card.pl-3 #simpletable td .small,
    .card.pl-3 #simpletable td.small {
        font-size: 12px !important;
    }

    /* Avatar + status treatment. */
    .card.pl-3 #simpletable .avatar {
        background: var(--faculty-primary-soft) !important;
        border: 0 !important;
        color: var(--faculty-primary) !important;
    }

    .card.pl-3 .badge-ride,
    .card.pl-3 #simpletable .badge-success {
        padding: 5px 9px !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
        font-size: 10px !important;
        font-weight: 500 !important;
    }

    .card.pl-3 #simpletable .badge-light {
        border: 0 !important;
        border-radius: 50px !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
        font-weight: 500 !important;
    }

    /* Action links/buttons follow the compact Figma treatment. */
    .card.pl-3 #simpletable .btn {
        min-height: 34px !important;
        padding: 7px 12px !important;
        border-radius: 5px !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
        transition: background .18s ease, border-color .18s ease, color .18s ease !important;
    }

    .card.pl-3 #simpletable .btn-primary,
    .card.pl-3 #simpletable .btn-info {
        border-color: var(--faculty-primary) !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card.pl-3 #simpletable .btn-primary:hover,
    .card.pl-3 #simpletable .btn-info:hover {
        border-color: #12564F !important;
        background: #12564F !important;
        color: #FFFFFF !important;
    }

    .card.pl-3 #simpletable .btn-outline-primary {
        border-color: rgba(23, 103, 95, 0.35) !important;
        background: #FFFFFF !important;
        color: var(--faculty-primary) !important;
    }

    .card.pl-3 #simpletable .btn-outline-primary:hover {
        border-color: var(--faculty-primary) !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
    }

    .card.pl-3 #simpletable .btn-danger {
        border-color: rgba(197, 76, 76, 0.25) !important;
        background: rgba(197, 76, 76, 0.08) !important;
        color: var(--faculty-danger) !important;
    }

    .card.pl-3 #simpletable .btn-danger:hover {
        border-color: var(--faculty-danger) !important;
        background: var(--faculty-danger) !important;
        color: #FFFFFF !important;
    }

    .card.pl-3 .btn-modern-chat {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 50% !important;
        background: var(--faculty-primary-soft) !important;
        box-shadow: none !important;
    }

    .card.pl-3 .btn-modern-chat i {
        color: var(--faculty-primary) !important;
        font-size: 16px !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .card.pl-3 .btn-modern-chat:hover {
        background: var(--faculty-primary) !important;
    }

    .card.pl-3 .btn-modern-chat:hover i {
        color: #FFFFFF !important;
        transform: none !important;
    }

    /* DataTables footer / pagination. */
    .card.pl-3 #simpletable_wrapper > .row:last-child {
        margin: 14px 0 0 0 !important;
        align-items: center !important;
    }

    .card.pl-3 #simpletable_info {
        padding-top: 0 !important;
        color: var(--faculty-text) !important;
        font-size: 12px !important;
    }

    .card.pl-3 #simpletable_paginate {
        padding-top: 0 !important;
    }

    .card.pl-3 #simpletable_paginate .pagination {
        gap: 4px !important;
        margin: 0 !important;
        justify-content: flex-end !important;
    }

    .card.pl-3 #simpletable_paginate .page-link {
        min-width: 28px !important;
        height: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 7px !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: transparent !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-size: 12px !important;
    }

    .card.pl-3 #simpletable_paginate .page-item.active .page-link {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card.pl-3 #simpletable_paginate .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
    }

    /* Modal visual language mapped to the same Figma palette. */
    #confirmModal .modal-content,
    #carPoolingChatModal .modal-content,
    #default-Modal .modal-content,
    #routeMapModal .modal-content {
        overflow: hidden !important;
        border: 1px solid rgba(23, 103, 95, 0.10) !important;
        border-radius: 10px !important;
        background: #FFFFFF !important;
        box-shadow: 0 18px 55px rgba(37, 37, 37, 0.14) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    #confirmModal .modal-header,
    #carPoolingChatModal .modal-header,
    #default-Modal .modal-header,
    #routeMapModal .modal-header {
        padding: 16px 20px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
    }

    #confirmModal .modal-title,
    #carPoolingChatModal .modal-title,
    #default-Modal .modal-title,
    #routeMapModal .modal-title,
    #carPoolingChatModal .chat-user-info h6,
    #carPoolingChatModal .chat-user-info small {
        color: var(--faculty-text) !important;
    }

    #carPoolingChatModal .chat-avatar {
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
    }

    #confirmModal .close,
    #carPoolingChatModal .close,
    #default-Modal .close,
    #routeMapModal .close {
        color: var(--faculty-text) !important;
        text-shadow: none !important;
        opacity: .65 !important;
    }

    #carPoolingChatModal .chat-input-row {
        padding: 14px 16px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
    }

    #carPoolingChatModal .form-control-chat {
        min-height: 42px !important;
        border: 1px solid var(--faculty-line) !important;
        border-radius: 5px !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        font-size: 13px !important;
    }

    #carPoolingChatModal .btn-send-modern {
        min-width: 46px !important;
        border-radius: 5px !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    #carPoolingChatModal .modal-body.chat-body {
        background: #F8FBFA !important;
    }

    #carPoolingChatModal .bg-sent {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        box-shadow: none !important;
    }

    #carPoolingChatModal .bg-received {
        border: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
    }

    #confirmModal .btn-primary,
    #default-Modal .btn-primary,
    #routeMapModal .btn-primary {
        border-color: var(--faculty-primary) !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    #confirmModal .btn-light,
    #default-Modal .btn-secondary,
    #routeMapModal .btn-secondary {
        border-color: var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
    }

    #default-Modal thead th {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
    }

    #default-Modal .modal-footer {
        border-top: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
    }

    /* Responsive: keep existing DOM and horizontal DataTable behavior. */
    @media (max-width: 991.98px) {
        .card.pl-3 .nav-tabs {
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
        }

        .card.pl-3 .nav-tabs .nav-link {
            min-width: 138px !important;
            height: 44px !important;
            padding: 0 18px !important;
            line-height: 44px !important;
            font-size: 13px !important;
        }

        .card.pl-3 #simpletable thead th,
        .card.pl-3 #simpletable tbody td {
            padding-left: 11px !important;
            padding-right: 11px !important;
        }
    }
.card.pl-3 #simpletable_length select {
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



/* DataTable search field */
.card.pl-3 #simpletable_filter {
    display: flex !important;
    justify-content: flex-end !important;
    margin-bottom: 12px !important;
}

.card.pl-3 #simpletable_filter label {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
    margin: 0 !important;

    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
    font-weight: 400 !important;
}

.card.pl-3 #simpletable_filter input {
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
    font-weight: 400 !important;

    outline: none !important;
    box-shadow: none !important;
}

.card.pl-3 #simpletable_filter input:focus {
    border-color: #17675F !important;
    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}
/* DataTable top controls full-width alignment */
.card.pl-3 #simpletable_wrapper > .row:first-child {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    margin-right: 0 !important;
    margin-left: 0 !important;
}

.card.pl-3 #simpletable_wrapper > .row:first-child > div:last-child {
    display: flex !important;
    justify-content: flex-end !important;
    flex: 1 1 auto !important;
    width: auto !important;
    max-width: none !important;
    margin-left: auto !important;
    padding-right: 0 !important;
}

.card.pl-3 #simpletable_filter {
    float: none !important;
    width: auto !important;
    margin-right: 0 !important;
    margin-left: auto !important;
}
/* Keep DataTable headings in Title Case */
#simpletable thead th,
#simpletable_wrapper .dataTables_scrollHead thead th {
    text-transform: none !important;
}
</style>


<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-dark" id="confirmModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fa fa-question-circle text-primary fa-3x"></i>
                </div>
                <span style="display:none" id="username"></span>
                <span style="display:none" id="action"></span>
                <span style="display:none" id="id"></span>
                <p class="mb-0 text-muted" id="body_message"></p>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-light btn-pill px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-pill px-4" id="submitRequestBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="card pl-3">
    <div class="card-header bg-primary"
        style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
        <div class="card-title text-white">
            Car Pooling Search
        </div>
    </div>
    <div class="card-body">
        <div class="tab-menu-heading pt-0">
            <div class="tabs-menu ">
                <!-- Tabs -->
                <ul class="nav nav-tabs border-bottom-0">
                    <li class="nav-item">
                        <a href="{{ url('/car-pooling-details') }}" class="nav-link active">
                            <i class="fa fa-car mr-2"></i>Ride Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/car-pooling-inbox') }}" class="nav-link">
                            <i class="fa fa-inbox mr-2"></i>Ride Request
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                            <a href="{{ url('/car-pooling-chat') }}" class="nav-link">
                                <i class="fa fa-commenting-o mr-2"></i>ChatBox
                                <span class="badge badge-danger ml-1">{{ count($car_pulling_count) }}</span>
                            </a>
                        </li> -->
                </ul>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 font-weight-bold text-dark">Ride Details</h4>
        </div>
        <div class="table-responsive">
            <table id="simpletable" class="table table-striped table-bordered no-footer" style="width:100% !important;">
                <thead>
                    <tr>
                        <th class="tab-title">Username</th>
                        <th class="tab-title">Conversation</th>
                        <th class="tab-title">Route</th>
                        <th class="tab-title">Ride</th>
                        <th class="tab-title"> Contact</th>
                        <th class="tab-title">Preferred Location</th>
                        <th class="tab-title">Route Option</th>
                        <th class="tab-title">Request</th>
                        <th class="tab-title">Vehicle</th>
                        <th class="tab-title">Engine Size</th>
                        <th class="tab-title">Capacity</th>
                    </tr>
                </thead>
                <!-- <tbody> -->
                <?php foreach ($car_pulling as $item):
    $isAccepted = (strpos($item->cp_partner, auth()->user()->username) !== false);
    $rowStyle = $isAccepted ? 'background-color: #f8fbff;' : '';
                    ?>
                <tr style="<?= $rowStyle ?>">
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div
                                class="avatar avatar-md bg-light rounded-circle mr-2 d-flex align-items-center justify-content-center text-primary border">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <div class="font-weight-600 text-dark">{{ $item->cp_entry_username }}</div>
                                <?php    if ($isAccepted): ?>
                                <span class="badge badge-success badge-pill badge-ride mt-1">
                                    <i class="fa fa-check-circle mr-1"></i> Ride Accepted
                                </span>
                                <?php    endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <button class="btn-modern-chat chat-trigger"
                            onclick="getUsername('<?= $item->cp_entry_username ?>','<?= $item->cp_id ?>')"
                            data-toggle="modal" data-target="#carPoolingChatModal" title="Messaging">
                            <i class="fa fa-comments"></i>
                        </button>
                    </td>
                    <td class="text-center align-middle">
                        @if ($item->cp_route_link != '' || $item->cp_preferred_location != '')
                            <button type="button" class="btn btn-outline-primary btn-sm btn-pill"
                                onclick="openRouteMap('{{ $item->cp_route_link }}', '{{ $item->cp_preferred_location }}')"
                                data-toggle="modal" data-target="#routeMapModal">
                                <i class="fa fa-map-marker mr-1"></i> Route
                            </button>
                            <span style="display:none">{{ $item->cp_route_link }}</span>
                        @else
                            <span class="text-muted small">Not Mentioned</span>
                        @endif
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge badge-light border px-3 py-2 text-dark">{{ $item->cp_want_ride }}</span>
                    </td>
                    <td class="align-middle small text-muted">{{ $item->cp_contact_det }}</td>
                    <td class="align-middle small">{{ $item->cp_preferred_location }}</td>
                    <td class="align-middle">
                        <div class="small mb-2 text-dark font-weight-500">{{ $item->cp_route_option }}</div>
                        <button class="btn btn-info btn-xs btn-pill px-3"
                            onclick="getTimings('<?= $item->cp_entry_username ?>','<?= $item->cp_id ?>')"
                            data-toggle="modal" data-target="#default-Modal">
                            <i class="fa fa-clock-o mr-1"></i> Timings
                        </button>
                    </td>
                    <td class="text-center align-middle">
                        @if ($item->offer_status == '' or $item->cp_want_ride == 'PENDING')
                            <button type="button" class="btn btn-primary btn-sm btn-pill px-3 shadow-sm" data-toggle="modal"
                                data-target="#confirmModal" data-username="<?= $item->cp_entry_username ?>"
                                data-id="<?= $item->cp_id ?>" data-action="insert">
                                <i class="fa fa-paper-plane mr-1"></i> Send Request
                            </button>
                        @else
                            <button type="button" class="btn btn-danger btn-sm btn-pill px-3 shadow-sm" data-toggle="modal"
                                data-target="#confirmModal" data-username="<?= $item->cp_entry_username ?>"
                                data-id="<?= $item->offer_status ?>" data-action="delete">
                                <i class="fa fa-times mr-1"></i> Cancel Request
                            </button>
                        @endif
                    </td>
                    <td class="align-middle small">{{ $item->cp_vehical_model }}</td>
                    <td class="align-middle small text-center">{{ $item->cp_vehical_cc }}</td>
                    <td class="align-middle text-center font-weight-bold text-primary">{{ $item->cp_capacity }}</td>
                </tr>
                <?php endforeach; ?>
                <!-- </tbody> -->
            </table>
        </div>

    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="carPoolingChatModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content chat">
            <div class="modal-header chat-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="chat-avatar">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="chat-user-info">
                        <h6 id="title">User Name</h6>
                        <small><i class="fa fa-circle text-success mr-1" style="font-size: 7px;"></i> Online</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- TOP INPUT ROW -->
            <div class="chat-input-row">
                <form id="sendMessageCarPulling_form" class="w-100 m-0">
                    @csrf
                    <input type="hidden" id="sendToUsername" name="sendToUsername">
                    <input type="hidden" id="cp_id" name="cp_id">
                    <input type="hidden" id="login_username" value="<?= auth()->user()->username ?>"
                        name="login_username">

                    <div class="input-group">
                        <input type="text" id="message" name="message" class="form-control form-control-chat"
                            placeholder="Please Enter Message..." autocomplete="off">
                        <div class="input-group-append ml-1">
                            <button type="button" id="messageBtn" onclick="btn_sendMessageCarPulling()"
                                class="btn-send-modern">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-body chat-body" id="chat_body_scroll">
                <div id="msg_cotainer_send" class="d-flex flex-column">
                    <!-- Messages will be loaded here -->
                    <div class="text-center py-5 text-muted small">Loading messages...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-clock-o mr-2"></i>Ride Timings</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                                <th>Sun</th>
                            </tr>
                        </thead>
                        <tbody id="dailyTimings">
                            <!-- Loaded via Ajax -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-pill px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- NEW MAP MODAL -->
<div class="modal fade" id="routeMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-map-marker mr-2"></i>Route Map View</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="mapModalBody">
                <!-- Map will be loaded here via iframe or a message if link is invalid -->
                <div id="mapLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading Map...</p>
                </div>
                <iframe id="routeMapFrame" src="" width="100%" height="500px" frameborder="0"
                    style="border:0; display:none;" allowfullscreen=""></iframe>
                <div id="mapError" class="text-center py-5" style="display:none;">
                    <i class="fa fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p class="text-muted">This route link cannot be displayed directly in a popup.</p>
                    <a id="externalMapLink" href="#" target="_blank" class="btn btn-primary btn-pill px-4 mt-2">
                        Open in Google Maps <i class="fa fa-external-link ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script type="text/javascript">





    function check_validation(validation_array) {
        var count_errors = 0;
        $.each(validation_array, function (index, value) {
            //alert(value);
            if ($('#' + value).val() === "" || $('#' + value).val() === null || $('#' + value).tokenfield('getTokensList') === "" || $('#' + value).tokenfield('getTokensList') === null) {
                $('#' + value).removeClass('is-valid').addClass('is-invalid');
                count_errors++;
            }
            else {
                $('#' + value).removeClass('is-invalid').addClass('is-valid');
                setTimeout(function () {
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

    function sendGetRequest(endPoint, successBlock) {
        $.ajax({
            method: "GET",
            url: url + '/api/' + endPoint,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function (res) {
            console.log(res);
        });
    }


    function sendPostRequest(endPoint, successBlock, postData) {
        $.ajax({
            method: "POST",
            url: url + '/api/' + endPoint,
            data: postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function (res) {
            // alert('bilal')
        });
    }




    $(document).ready(function () {
        $('#simpletable').DataTable({
            "scrollX": true,
            "autoWidth": false
        });
        $('#confirmModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var username = button.data('username'); // Extract info from data-* attributes
            var id = button.data('id');
            var action = button.data('action');

            if (action == 'delete') {
                $('#body_message').text('Are you Sure you want to delete Request');
            } else {
                $('#body_message').text('Are you Sure you want to Send Request');
            }

            // Update the modal content with the extracted information
            var modal = $(this);
            modal.find('#username').text(username);
            modal.find('#id').text(id);
            modal.find('#action').text(action);


        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // disable enter key on form
        document.addEventListener('keydown', function (event) {
            if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                event.preventDefault();
                return false;
            }
        });
    });
    $(document).ready(function () {
        $('#res-config').DataTable();
        // var username=$('#sendToUsername').val();
        // var id= $('#cp_id').val();
        setInterval(function () {
            console.log('love');
            // btn_sendMessageCarPulling();
            var username = $('#sendToUsername').val();
            var id = $('#cp_id').val();
            getUsername(username, id);
        }, 5000);

    });
    $('#submitRequestBtn').click(function () {
        var username = $('#confirmModal').find('#username').text();
        var id = $('#confirmModal').find('#id').text();
        var action = $('#confirmModal').find('#action').text();
        var login_user_id = '<?=auth()->user()->username;?>';

        // alert(id);
        // Add your request code here, passing in the username and id variables
        //  console.log("Submitting request for user " + username + " with ID " + id);

        var data = new FormData();
        // alert(AuthUserNamee)
        data.append("user_ID", username);
        data.append("cp_id", id);
        data.append("requested_user", login_user_id);
        data.append("action", action);



        sendPostRequest('pooling_request_aceept_cancel?rno=' + Math.random(), function (res) {
            //console.log('Hello Baby', res);
            //alert(res.status);
            if (res.status) {
                // $('#successMsg' + userID).css('color', 'green');
                // $('#successMsg' + userID).html('Request Accepted Successfully');
                // // getRequest();     
                // setTimeout(function() {
                //     location.reload();
                // }, 1000);

                location.reload();
            } else {

            }
        }, data);



        // Close the modal
        $('#confirmModal').modal('hide');
    });


    function getUsername(username, id) {
        var loginUsername = $('#login_username').val();
        $('#title').html(username);
        $('#sendToUsername').val(username);
        $('#cp_id').val(id);
        if (!id) return false;

        $.ajax({
            type: 'get',
            url: '{{ url("api/get-car-pulling-message") }}',
            data: {
                cp_id: id,
                username: 'all',
                authUsername: username
            },
            headers: {
                'Authorization': 'Bearer <?= auth()->user()->api_token ?>',
                'Accept': 'application/json'
            },
            success: function (res) {
                if (res.status) {
                    $('#msg_cotainer_send').html('');

                    if (res.data && res.data.length > 0) {
                        // Sort chronological (oldest to newest) - assuming the API returns newest first based on original code and 'ORDER BY created_at desc'
                        var messages = res.data.reverse();

                        $(messages).each(function (index, row) {
                            var senderId = row.entry_user_id || row.ENTRY_USER_ID;
                            var msgText = row.message || row.MESSAGE;

                            var isMe = (loginUsername == senderId);
                            var alignment = isMe ? 'justify-content-end' : 'justify-content-start';
                            var bgColor = isMe ? 'bg-sent' : 'bg-received';

                            var msgHtml = '<div class="d-flex ' + alignment + ' mb-2">' +
                                '<div class="msg_container ' + bgColor + '">' +
                                msgText +
                                '</div>' +
                                '</div>';
                            $('#msg_cotainer_send').append(msgHtml);
                        });

                        // Scroll to bottom
                        var objDiv = document.getElementById("chat_body_scroll");
                        objDiv.scrollTop = objDiv.scrollHeight;
                    } else {
                        $('#msg_cotainer_send').html('<div class="text-center py-5 text-muted small">No messages yet.</div>');
                    }
                }
            }
        });
    }

    function btn_sendMessageCarPulling() {
        var message = $('#message').val();
        if (message.trim() == '') return;

        var username = $('#sendToUsername').val();
        var cp_id = $('#cp_id').val();
        var form_data = $('#sendMessageCarPulling_form').serialize();
        var authUsername = '<?= auth()->user()->username ?>';

        $.ajax({
            type: 'post',
            url: '{{ url("api/car-pulling-message") }}?cp_id=' + cp_id + '&sendToUsername=' + username + '&authUsername=' + authUsername,
            data: form_data,
            headers: {
                'Authorization': 'Bearer <?= auth()->user()->api_token ?>',
                'Accept': 'application/json'
            },
            success: function (res) {
                if (res.status) {
                    $('#message').val('');
                    getUsername(username, cp_id);
                }
            }
        });
    }
    function getTimings(username, cp_id) {
        //    alert(cp_id);
        $('#dailyTimings').html('');
        // sendPostRequest('car-pulling-timings?cp_id=' + cp_id, function(res) {
        $.ajax({
            type: 'POST',
            url: '{{ url("api/car-pulling-timings") }}?cp_id=' + cp_id,
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json'
            },
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    var tr = "";
                    $(res.data).each(function (index, row) {
                        if (row.monday == '-' && row.tuesday == '-' && row.wednesday == '-' && row.thursday == '-' && row.friday == '-' && row.saturday == '-' && row.sunday == '-') {
                            tr += '<tr>';
                            tr += '<td colspan="12" style="font-weight:bold" align="center">All Days</td>';
                            tr += '</tr>';
                        } else {
                            tr += '<tr>';
                            tr += '<td >' + row.monday + '</td>';
                            tr += '<td >' + row.tuesday + '</td>';
                            tr += '<td >' + row.wednesday + '</td>';
                            tr += '<td >' + row.thursday + '</td>';
                            tr += '<td >' + row.friday + '</td>';
                            tr += '<td >' + row.saturday + '</td>';
                            tr += '<td >' + row.sunday + '</td>';
                            tr += '</tr>';
                        }
                    });
                    $('#dailyTimings').html(tr);
                }
            }

        });
    }

    function openRouteMap(link, location) {
        $('#mapLoader').show();
        $('#routeMapFrame').hide();
        $('#mapError').hide();

        var finalLink = "";
        if (link && (link.includes('http://') || link.includes('https://'))) {
            finalLink = link;
            if (link.includes('google.com/maps') && !link.includes('embed')) {
                finalLink = link.replace('/maps/', '/maps/embed/').replace('/dir/', '/dir/embed/');
            }
        } else if (location && location.trim() !== "") {
            finalLink = "https://maps.google.com/maps?q=" + encodeURIComponent(location) + "&output=embed";
        }

        if (finalLink !== "") {
            $('#routeMapFrame').attr('src', finalLink);
            $('#externalMapLink').attr('href', (link && link.includes('http')) ? link : "https://www.google.com/maps/search/" + encodeURIComponent(location));

            setTimeout(function () {
                $('#mapLoader').hide();
                $('#routeMapFrame').show();
                $('#mapError').show().find('p').html('If the map does not appear above, use the button below:');
            }, 1000);
        } else {
            $('#mapLoader').hide();
            $('#mapError').show().find('p').html('Invalid or missing route link and location.');
        }
    }
</script>
@stop