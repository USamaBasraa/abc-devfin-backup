@extends('layouts.app')
{{-- @dd("123"); --}}
@section('title')
BNU CMS
@stop
@section('content')
@include('components.toast-notifications')

<head>
    <!-- Prevent Browser Caching - Always fetch fresh data -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
</head>
<style>
    .invisible {
        display: none;
    }

    /* Custom red border for invalid input */
    .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Enhanced focus indicators for accessibility */
    .form-control:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.3rem rgba(0, 123, 255, 0.3) !important;
        outline: 2px solid #007bff !important;
        outline-offset: 2px !important;
    }

    .btn:focus {
        box-shadow: 0 0 0 0.3rem rgba(0, 123, 255, 0.3) !important;
        outline: 2px solid #007bff !important;
        outline-offset: 2px !important;
    }

    /* Focus indicators for custom elements */
    input[type="checkbox"]:focus,
    input[type="radio"]:focus {
        outline: 2px solid #007bff !important;
        outline-offset: 2px !important;
    }

    /* High contrast focus for better visibility */
    .form-control:focus-visible {
        outline: 3px solid #007bff !important;
        outline-offset: 3px !important;
    }

    /* Character counter badge styling */
    #remarks_counter {
        font-size: 0.7rem !important;
        max-width: fit-content !important;
        white-space: nowrap !important;
    }

    /* Ensure form group contains badge properly */
    .form-group {
        overflow: visible !important;
        position: relative !important;
    }

    /* Modern Refund Details Styling */
    .refund-details-card {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .refund-details-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .refund-details-card .card-header {
        border-bottom: none;
        font-weight: 600;
    }

    .refund-details-card .card-body {
        background-color: #e9ecef;
    }

    .refund-details-card .row {
        margin-bottom: 0.5rem;
    }

    .refund-details-card .text-muted {
        font-size: 0.85rem;
        font-weight: 500;
    }

    .refund-details-card strong {
        color: #2c3e50;
    }

    /* Mobile responsiveness for buttons - Using Bootstrap 4 classes only */

    /* Mobile responsiveness for refund details */
    @media (max-width: 768px) {
        .refund-details-card .col-md-6 {
            margin-bottom: 1rem;
        }

        .refund-details-card .card-body {
            padding: 0.75rem;
        }

        .refund-details-card .row {
            margin-bottom: 0.25rem;
        }
    }
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
        margin-right: 5px;
        margin-left: 5px;
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
        margin-right: 5px;
        margin-left: 5px;
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

    /* Search Button Loading Effects - Using Bootstrap 4 classes only */

    /* Animation keyframes removed - using Bootstrap 4 classes only */

    .fa-search-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Table Header Styling */
    .table thead th {
        font-weight: bold !important;
        font-size: 14px !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .thead-light th {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }

    /* Escalation Animation */
    .escalation-border-blink {
        animation: border-blink 2s infinite !important;
        border-left: 4px solid #ff6b35 !important;
    }

    @keyframes border-blink {

        0%,
        50% {
            border-left-color: #ff6b35 !important;
            background-color: rgba(255, 107, 53, 0.08) !important;
        }

        51%,
        100% {
            border-left-color: transparent !important;
            background-color: transparent !important;
        }
    }

    /* Lazy Loading Styles */
    .lazy-load {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .lazy-load.loaded {
        opacity: 1;
    }

    /* Blinking Orange Border Animation for Escalation Rows - High Priority */
    .escalation-border-blink {
        animation: border-blink 2s infinite !important;
        border-left: 4px solid #ff6b35 !important;
    }

    @keyframes border-blink {

        0%,
        50% {
            border-left-color: #ff6b35;
        }

        25%,
        75% {
            border-left-color: #ffa500;
        }

        100% {
            border-left-color: #ff6b35;
        }
    }

    /* Force override any conflicting styles */
    tr.escalation-border-blink {
        border-left: 4px solid #ff6b35 !important;
        animation: border-blink 2s infinite !important;
    }

    /* Stronger CSS to override Bootstrap/jQuery conflicts */
    table tr.escalation-border-blink {
        border-left: 4px solid #ff6b35 !important;
        animation: border-blink 2s infinite !important;
    }

    /* Alternative blinking effect if border animation fails */
    .escalation-border-blink:nth-child(odd) {
        animation: escalation-pulse 1.5s ease-in-out infinite alternate !important;
    }

    /* Modal Improvements */
    .modal-xl {
        max-width: 70%;
        width: 70%;
    }

    .modal-content {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: none;
        border-radius: 10px;
    }

    .modal-header {
        border-radius: 10px 10px 0 0;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 2rem;
        max-height: 80vh;
        overflow-y: auto;
    }

    /* Message Box Improvements */
    .message-textarea {
        min-height: 50px;
        resize: vertical;
        border: 1px solid #424e79 !important;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        line-height: 1.4;
    }


    /* Button Enhancements in Modals - Using Bootstrap 4 classes only */

    /* Modal buttons using Bootstrap 4 classes only */

    /* Typography Improvements */
    .modal h4,
    .modal h5,
    .modal h6 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .modal .card-header {
        background-color: #4454c3;
        color: white;
        border: none;
        padding: 1rem 1.5rem;
    }

    .modal .form-control {
        border-radius: 6px;
        border: 2px solid #e9ecef;
        padding: 10px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .modal .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .modal .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    /* Fix dropdown text visibility */
    .form-control {
        height: auto !important;
        min-height: 38px;
        line-height: 1.5;
        padding: 8px 12px;
    }

    .form-control option {
        padding: 8px 12px;
        line-height: 1.5;
        white-space: normal;
    }

    /* Modern Search Loader */
    .modern-search-loader {
        position: relative;
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }

    .search-glass {
        width: 40px;
        height: 40px;
        border: 3px solid #e3f2fd;
        border-radius: 50%;
        position: absolute;
        top: 10px;
        left: 10px;
        animation: searchGlass 2s ease-in-out infinite;
    }

    .search-glass::before {
        content: '';
        position: absolute;
        width: 12px;
        height: 3px;
        background: #2196f3;
        border-radius: 2px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(45deg);
        animation: searchHandle 2s ease-in-out infinite;
    }

    .search-pulse {
        width: 60px;
        height: 60px;
        border: 2px solid #2196f3;
        border-radius: 50%;
        position: absolute;
        top: 0;
        left: 0;
        animation: searchPulse 2s ease-in-out infinite;
    }

    @keyframes searchGlass {

        0%,
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }

        50% {
            transform: scale(1.1) rotate(180deg);
            opacity: 0.7;
        }
    }

    @keyframes searchHandle {

        0%,
        100% {
            transform: translate(-50%, -50%) rotate(45deg) scale(1);
        }

        50% {
            transform: translate(-50%, -50%) rotate(225deg) scale(1.2);
        }
    }

    @keyframes searchPulse {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.3;
        }

        100% {
            transform: scale(0.8);
            opacity: 1;
        }
    }

    /* Modern Refresh Loader */
    .modern-refresh-loader {
        position: relative;
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }

    .refresh-icon {
        width: 40px;
        height: 40px;
        border: 3px solid #fff3cd;
        border-top: 3px solid #ffc107;
        border-radius: 50%;
        position: absolute;
        top: 10px;
        left: 10px;
        animation: refreshSpin 1.5s linear infinite;
    }

    .refresh-pulse {
        width: 60px;
        height: 60px;
        border: 2px solid #ffc107;
        border-radius: 50%;
        position: absolute;
        top: 0;
        left: 0;
        animation: refreshPulse 1.5s ease-in-out infinite;
    }

    @keyframes refreshSpin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes refreshPulse {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.4;
        }

        100% {
            transform: scale(0.8);
            opacity: 1;
        }

    }
</style>
<style type="text/css">
    /* ============================================================
       Figma UI override: e-Portal (Pending) / e-Portal (Approved)
       Presentation only. Existing IDs, Blade conditions and JS are
       intentionally retained for backend / interaction compatibility.
       ============================================================ */
    .eportal-page {
        --ep-primary: #17675f;
        --ep-primary-dark: #10574f;
        --ep-accent: #f43f5e;
        --ep-text: #252525;
        --ep-muted: #64748b;
        --ep-subtle: #94a3b8;
        --ep-border: #e2e8f0;
        --ep-row-alt: #f8fafc;
        --ep-success-bg: #dcfce7;
        --ep-success: #16a34a;
        --ep-danger-bg: #fee2e2;
        --ep-danger: #dc2626;
        --ep-warning-bg: #fef3c7;
        --ep-warning: #b45309;
        position: relative;
        width: 100% !important;
        min-height: calc(100vh - 110px);
        margin: 0 !important;
        /* padding: 32px 30px 48px !important; */
        border: 0 !important;
        border-radius: 10px !important;
        background: #f2f6f9 !important;
        box-shadow: none !important;
        overflow: visible;
        color: var(--ep-text);
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    }

    .eportal-page *,
    #approval_status *,
    #application_tracking * {
        box-sizing: border-box;
    }

    .eportal-shell {
        width: 100%;
        /* max-width: 1380px; */
        margin: 0 auto;
    }

    .eportal-page-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin: 0 0 18px;
    }

    .eportal-page-heading h3 {
        margin: 0 0 4px !important;
        color: var(--ep-text) !important;
        font-size: 20px !important;
        line-height: 1.25;
        font-weight: 600 !important;
        text-align: left !important;
    }

    .eportal-page-heading p {
        margin: 0;
        color: var(--ep-text);
        font-size: 18px;
        line-height: 1.35;
        font-weight: 400;
    }

    .eportal-download-btn {
        min-height: 40px;
        padding: 9px 14px !important;
        border: 1px solid var(--ep-primary) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--ep-primary) !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 600 !important;
    }

    .eportal-download-btn:hover,
    .eportal-download-btn:focus {
        background: rgba(23, 103, 95, 0.06) !important;
        color: var(--ep-primary-dark) !important;
    }

    .eportal-tabs-and-extra {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .eportal-page .tabs-menu {
        padding: 0 !important;
        margin: 0;
    }

    .eportal-page .panel-tabs,
    .eportal-page .eportal-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 0;
        border: 0 !important;
        list-style: none;
    }

    .eportal-page .panel-tabs > li {
        margin: 0 !important;
        padding: 0 !important;
    }

    .eportal-page .panel-tabs > li > a {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 40px;
        padding: 10px 20px !important;
        margin: 0 !important;
        border: 1px solid var(--ep-border) !important;
        border-radius: 999px !important;
        background: transparent !important;
        color: var(--ep-muted) !important;
        font-size: 14px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        box-shadow: none !important;
    }

    .eportal-page .panel-tabs > li > a i:not(.spinner-border) {
        display: none;
    }

    .eportal-page .panel-tabs > li > a.active,
    .eportal-page .panel-tabs > li.active > a,
    .eportal-page .panel-tabs > li > a[aria-selected="true"] {
        border-color: var(--ep-primary) !important;
        background: var(--ep-primary) !important;
        color: #fff !important;
    }

    .eportal-page .panel-tabs .spinner-border {
        width: 13px;
        height: 13px;
        border-width: 2px;
    }

    .eportal-request-status-wrap {
        width: 200px;
        margin-left: auto;
    }

    .eportal-request-status-wrap .form-control {
        height: 42px !important;
        min-height: 42px !important;
        border: 1px solid var(--ep-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--ep-text) !important;
        font-size: 13px !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    .eportal-hidden-category-wrap {
        display: none !important;
    }

    .eportal-page #result-body-categ {
        width: 100%;
    }

    .eportal-page #result-body-categ > .mb-4 {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin: 0 !important;
    }

    /* The category title is retained in the DOM for compatibility; the
       Figma layout communicates the category through the filter control. */
    .eportal-page #result-body-categ > .mb-4 > h4 {
        display: none !important;
    }

    /* Figma filter card */
    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 {
        order: 1;
        display: flex !important;
        flex-wrap: wrap;
        align-items: flex-end !important;
        gap: 16px;
        width: 100%;
        margin: 0 0 18px !important;
        padding: 16px !important;
        border-radius: 12px;
        background: #fff;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"] {
        padding: 0 !important;
        margin: 0 !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(1) {
        flex: 0 0 220px;
        width: 220px;
        max-width: 220px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(2) {
        flex: 0 0 220px;
        width: 220px;
        max-width: 220px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3) {
        flex: 0 0 376px;
        width: 376px;
        max-width: 376px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) {
        flex: 1 1 250px;
        width: auto;
        max-width: none;
        margin-left: auto !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3) > .row {
        display: flex;
        gap: 16px;
        margin: 0 !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3) > .row > [class*="col-"] {
        flex: 1 1 0;
        max-width: none;
        padding: 0 !important;
    }

    .eportal-page #result-body-categ label {
        display: block !important;
        margin: 0 0 6px !important;
        color: var(--ep-muted) !important;
        font-size: 12px !important;
        line-height: 1.2 !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0 !important;
    }

    .eportal-page #result-body-categ label b {
        font-weight: 600 !important;
    }

    .eportal-page #result-body-categ .form-control,
    .eportal-page #result-body-categ .form-control-sm {
        width: 100%;
        height: 42px !important;
        min-height: 42px !important;
        padding: 9px 12px !important;
        border: 1px solid var(--ep-border) !important;
        border-radius: 8px !important;
        background-color: #fff !important;
        color: var(--ep-text) !important;
        font-family: inherit;
        font-size: 14px !important;
        line-height: 22px !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    .eportal-page #result-body-categ .form-control::placeholder {
        color: var(--ep-subtle) !important;
        opacity: 1;
    }

    .eportal-page #result-body-categ .form-control:focus {
        border-color: rgba(23, 103, 95, 0.65) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
        outline: 0 !important;
    }

    .eportal-page #result-body-categ input[type="date"] {
        color: var(--ep-subtle) !important;
    }

    .eportal-page #result-body-categ input[type="date"]:valid {
        color: var(--ep-text) !important;
    }

    /* Action group inside dynamically rendered filter card */
    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) > .d-flex {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end;
        flex-wrap: wrap !important;
        gap: 10px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) .btn {
        min-height: 42px;
        margin: 0 !important;
        padding: 9px 18px !important;
        border-radius: 8px !important;
        font-family: inherit;
        font-size: 13px !important;
        line-height: 20px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    .eportal-page #getCategButton {
        border-color: var(--ep-primary) !important;
        background: var(--ep-primary) !important;
        color: #fff !important;
    }

    .eportal-page #getCategButton:hover,
    .eportal-page #getCategButton:focus {
        border-color: var(--ep-primary-dark) !important;
        background: var(--ep-primary-dark) !important;
        color: #fff !important;
    }

    .eportal-page #result-body-categ button[onclick="refreshData()"] {
        width: 48px;
        min-width: 48px;
        padding-left: 0 !important;
        padding-right: 0 !important;
        border-color: var(--ep-border) !important;
        background: transparent !important;
        color: var(--ep-muted) !important;
        font-size: 0 !important;
    }

    .eportal-page #result-body-categ button[onclick="refreshData()"] i {
        margin: 0 !important;
        font-size: 14px !important;
    }

    .eportal-page #result-body-categ .btn-group {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
    }

    .eportal-page #filter-all-btn {
        border-color: var(--ep-border) !important;
        background: #fff !important;
        color: var(--ep-muted) !important;
    }

    .eportal-page #filter-all-btn.active {
        border-color: var(--ep-primary) !important;
        color: var(--ep-primary) !important;
    }

    .eportal-page #filter-escalation-btn,
    .eportal-page #filter-escalation-btn.active {
        border-color: var(--ep-accent) !important;
        background: var(--ep-accent) !important;
        color: #fff !important;
    }

    .eportal-page #filter-all-btn i,
    .eportal-page #filter-escalation-btn i {
        display: none;
    }

    /* Record count: Pending Figma places it above the table. */
    .eportal-page #result-body-categ > .mb-4 > .text-center.p-2.border-top {
        order: 2;
        margin: 0 0 14px !important;
        padding: 0 !important;
        border: 0 !important;
        color: var(--ep-muted) !important;
        font-size: 12px !important;
        text-align: left !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .text-center.p-2.border-top small {
        color: inherit !important;
        font-size: 12px !important;
        font-weight: 500;
    }

    /* Approved reference goes directly from filters to table. The record
       count node is kept in the DOM but visually suppressed in this state. */
    .eportal-page:has(#setclasscompleted.active) #result-body-categ > .mb-4 > .text-center.p-2.border-top {
        display: none !important;
    }

    /* Table card */
    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 {
        order: 3;
        width: 100%;
        margin: 0 !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 > [class*="col-"] {
        position: relative;
        width: 100%;
        max-width: 100%;
        padding: 0 !important;
        border-radius: 12px;
        background: #fff;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: thin;
    }

    .eportal-page #result-body-categ table.table {
        width: 100% !important;
        min-width: 1280px;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff;
        table-layout: auto;
    }

    .eportal-page #result-body-categ table.table thead th {
        height: 48px;
        padding: 13px 12px !important;
        border: 0 !important;
        border-bottom: 0 !important;
        background: var(--ep-primary) !important;
        color: #fff !important; 
        font-family: inherit;
        font-size: 11px !important;
        line-height: 16px !important;
        font-weight: 700 !important;
        letter-spacing: 0 !important;
        text-transform: capitalize !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .eportal-page #result-body-categ table.table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .eportal-page #result-body-categ table.table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .eportal-page #result-body-categ table.table tbody td {
        min-height: 56px;
        padding: 13px 12px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--ep-border) !important;
        background: transparent;
        color: #1e293b !important;
        font-family: inherit;
        font-size: 12px !important;
        line-height: 1.35 !important;
        font-weight: 400;
        vertical-align: middle !important;
    }

    .eportal-page #result-body-categ table.table tbody tr:nth-child(even) {
        background: var(--ep-row-alt);
    }

    .eportal-page #result-body-categ table.table tbody tr:hover {
        background-color: rgba(23, 103, 95, 0.035);
    }

    /* Pending Figma intentionally omits the two time columns while the
       source data/DOM remains intact. Approved keeps all 10 columns. */
    .eportal-page:has(#setclasspending.active) #result-body-categ table.table th:nth-child(6),
    .eportal-page:has(#setclasspending.active) #result-body-categ table.table th:nth-child(7),
    .eportal-page:has(#setclasspending.active) #result-body-categ table.table td:nth-child(6),
    .eportal-page:has(#setclasspending.active) #result-body-categ table.table td:nth-child(7) {
        display: none;
    }

    .eportal-page:has(#setclasspending.active) #result-body-categ table.table {
        min-width: 1060px;
    }

    /* Figma-like status pills, overriding current inline outline styles only
       at the presentation layer. */
    .eportal-page #result-body-categ .badge-outline-success {
        border: 0 !important;
        border-radius: 99px !important;
        background: var(--ep-success-bg) !important;
        color: var(--ep-success) !important;
        padding: 5px 11px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .eportal-page #result-body-categ .badge-outline-danger {
        border: 0 !important;
        border-radius: 99px !important;
        background: var(--ep-danger-bg) !important;
        color: var(--ep-danger) !important;
        padding: 5px 11px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .eportal-page #result-body-categ .badge-outline-primary {
        border: 0 !important;
        border-radius: 99px !important;
        background: #dbeafe !important;
        color: #2563eb !important;
        padding: 5px 11px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .eportal-page #result-body-categ .badge i {
        margin-right: 4px !important;
    }

    /* Actions are horizontal in Figma. */
    .eportal-page #result-body-categ .btn-group-vertical {
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        gap: 6px;
        width: auto;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn {
        width: auto !important;
        min-height: 30px;
        margin: 0 !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        background: transparent !important;
        font-size: 12px !important;
        line-height: 18px !important;
        font-weight: 500 !important;
        white-space: nowrap;
        box-shadow: none !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn-outline-success {
        border-color: var(--ep-primary) !important;
        color: var(--ep-primary) !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn-outline-primary {
        border-color: var(--ep-muted) !important;
        color: var(--ep-muted) !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn i {
        display: none;
    }

    /* Empty state from the Pending Figma screen. This is generated entirely
       with CSS, so no change is required to the data/AJAX rendering logic. */
    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 > [class*="col-"]:has(table tbody:empty) {
        min-height: 448px;
        overflow: hidden;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 > [class*="col-"]:has(table tbody:empty) table {
        min-height: 448px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 > [class*="col-"]:has(table tbody:empty)::before {
        content: "\f01c";
        position: absolute;
        z-index: 3;
        left: 50%;
        top: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 86px;
        height: 86px;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background: var(--ep-row-alt);
        color: #8da0bf;
        font-family: FontAwesome;
        font-size: 36px;
        font-weight: normal;
        pointer-events: none;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.mt-3 > [class*="col-"]:has(table tbody:empty)::after {
        content: "No records found";
        position: absolute;
        z-index: 3;
        left: 50%;
        top: 255px;
        transform: translateX(-50%);
        color: var(--ep-muted);
        font-size: 16px;
        font-weight: 500;
        pointer-events: none;
    }

    /* Load-more/lazy-loading footer remains functional and keeps its original
       placement below the table. */
    .eportal-page #scroll-trigger {
        order: 4;
        margin-top: 14px;
        padding: 12px 0 !important;
        border: 0 !important;
        color: var(--ep-muted);
    }

    .eportal-page #load-more-content .btn {
        border-color: var(--ep-primary) !important;
        border-radius: 8px !important;
        color: var(--ep-primary) !important;
        box-shadow: none !important;
    }

    /* Loader/error states */
    .eportal-page #result-body-categ > .text-center,
    .eportal-page #result-body-categ > .p-4 {
        border-radius: 12px;
        background: #fff;
    }

    /* Keep existing modals fully present, but align their visual palette with
       the new screen without changing modal IDs/fields/handlers. */
    #approval_status,
    #application_tracking {
        z-index: 99999 !important;
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    }

    #approval_status .modal-content,
    #application_tracking .modal-content {
        overflow: hidden;
        border: 0 !important;
        border-radius: 14px !important;
        box-shadow: 0 18px 60px rgba(15, 23, 42, 0.22) !important;
    }

    #approval_status .modal-header,
    #application_tracking .modal-header {
        min-height: 60px !important;
        padding: 16px 20px !important;
        border: 0 !important;
        border-radius: 14px 14px 0 0 !important;
        background: var(--ep-primary, #17675f) !important;
    }

    #approval_status .modal-header h4,
    #application_tracking .modal-header h4,
    #approval_status .modal-header i,
    #application_tracking .modal-header i {
        color: #fff !important;
    }

    .modal-backdrop.show {
        z-index: 99990 !important;
    }

    @media (max-width: 1199.98px) {
        .eportal-page {
            padding: 26px 22px 40px !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(1),
        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(2) {
            flex: 1 1 220px;
            width: auto;
            max-width: none;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3) {
            flex: 1 1 360px;
            width: auto;
            max-width: none;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) {
            flex: 1 1 100%;
            margin-left: 0 !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) > .d-flex {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .eportal-page {
            padding: 20px 14px 32px !important;
            border-radius: 0 !important;
        }

        .eportal-page-heading {
            flex-direction: column;
            margin-bottom: 16px;
        }

        .eportal-page-heading h3 {
            font-size: 19px !important;
        }

        .eportal-page-heading p {
            font-size: 15px;
        }

        .eportal-tabs-and-extra {
            align-items: stretch;
            flex-direction: column;
            margin-bottom: 18px;
        }

        .eportal-request-status-wrap {
            width: 100%;
            margin-left: 0;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 {
            gap: 12px;
            padding: 14px !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(1),
        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(2),
        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3),
        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) {
            flex: 1 1 100%;
            width: 100%;
            max-width: 100%;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(3) > .row {
            gap: 10px;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.mb-3 > [class*="col-"]:nth-child(4) > .d-flex {
            justify-content: flex-start;
        }

        .eportal-page #result-body-categ table.table {
            min-width: 1120px;
        }

        .eportal-page:has(#setclasspending.active) #result-body-categ table.table {
            min-width: 920px;
        }

        #approval_status .modal-dialog,
        #application_tracking .modal-dialog {
            width: calc(100% - 20px) !important;
            max-width: calc(100% - 20px) !important;
            margin: 10px auto !important;
        }
    }
</style>
<div class="eportal-page card e-panel">
    <div class="eportal-shell">
        <div class="eportal-page-heading">
            <div>
                <h3>E-Portal Approval</h3>
                <p>Improve Performance</p>
            </div>
            <?php if (auth()->user()->username == 'hina.razzaq') { ?>
            <button id="downloadExcel" class="btn eportal-download-btn" onclick="downloadPDF()">
                <i class="fa fa-download mr-1"></i> Download Excel
            </button>
            <?php } ?>
        </div>

        <div class="eportal-tabs-and-extra">
            <div class="tabs-menu">
                <ul class="nav panel-tabs eportal-tabs">
                    <li>
                        <a href="#lectures" onclick="status('Pending')" id="setclasspending" data-toggle="tab">
                            <i class="fa fa-clock-o"></i> Pending
                            <span id="pendingSpinner" class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;">
                                <span class="sr-only">Loading...</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#material" onclick="status('Approved')" id="setclasscompleted" data-toggle="tab">
                            <i class="fa fa-check-circle"></i> Approved
                            <span id="approvedSpinner" class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;">
                                <span class="sr-only">Loading...</span>
                            </span>
                        </a>
                    </li>
                </ul>
                <input type="hidden" id="taskStatus" value="Pending">
                <input type="hidden" id="studentStatus" value="noStudent">
            </div>

            @if(auth()->user()->username === 'somia.maqsood')
                <div class="eportal-request-status-wrap">
                    <select class="form-control" id="request_status" onchange="getCateg()" name="request_status">
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Declined">Declined</option>
                    </select>
                </div>
                <div class="eportal-hidden-category-wrap">
                    <!-- Hidden category dropdown to maintain backend compatibility -->
                    <select class="form-control" id="category_id" style="display: none;" name="category_id">
                        <option value="0">All Requests</option>
                        @foreach($resEpCateg as $user)
                            <option value="{{$user->id}}">{{$user->ec_title}}<span style="font-weight:bold"> <b>(Pending Count:
                                        {{$user->total_rec}})</b></span></option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="eportal-hidden-category-wrap">
                    <!-- Hidden category dropdown to maintain backend compatibility -->
                    <select class="form-control" id="category_id" style="display: none;" name="category_id">
                        <option value="0">All Requests</option>
                        @foreach($resEpCateg as $user)
                            <option value="{{$user->id}}">{{$user->ec_title}}<span style="font-weight:bold"> <b>(Pending Count:
                                        {{$user->total_rec}})</b></span></option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Content area preserved; AJAX continues to render the filters/table here -->
        <div id="result-body-categ"></div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="approval_status">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0 rounded-top"
                style="min-height: 50px; padding: 20px;">
                <div class="d-flex align-items-center">
                    <i class="fa fa-eye fa-lg mr-3 text-white"></i>
                    <h4 class="modal-title mb-0 font-weight-bold text-white" id="approvalStatusModalLabel">
                        <span id="header_modal">Application Details</span>
                    </h4>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="font-size: 18px; padding: 0.5rem;">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left;">

            </div>
            <div class="col-12">
                <div class="form-group">

                    <div class="row">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Name:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="stdname"></span>
                            <span id="stdStatus"></span><br>
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
                    <div class="row" id="bank_details">
                        <div class="col-12">
                            <div class="card border-light">
                                <div
                                    class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
                                    <h6 id="bank_details_title" class="mb-0"><i class="fa fa-money text-primary"></i>
                                        Refund Details</h6>
                                    <div class="btn-group" role="group">

                                        <button type="button" class="btn btn-sm mr-2" id="export_button"
                                            onclick="exportBankDetailsToExcelFormatted()" title="Export formatted data"
                                            style="background-color: #00C851; border-color: #00C851; color: white; box-shadow: 0 2px 4px rgba(0,200,81,0.3);">
                                            <i class="fa fa-file-excel-o"></i> Export to Excel
                                        </button>
                                        <button type="button" class="btn btn-sm" id="export_pdf_button"
                                            onclick="exportBankDetailsToPDFFormatted()"
                                            title="Export formatted data to PDF"
                                            style="background-color: #2196F3; border-color: #2196F3; color: white; box-shadow: 0 2px 4px rgba(33,150,243,0.3);">
                                            <i class="fa fa-file-pdf-o"></i> Export to PDF
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div id="application_bank_details"></div>
                                </div>
                            </div>
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
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Res Remarks:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="resolving_user_remarks"></span><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Approval Remarks:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="approval_user_remarks"></span><br>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="check_form_div">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Check Details:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="checkForm"></span><br>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="view_status_div">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>View Status:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="view_status"></span><br>
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
<div class="modal fade" id="application_tracking" tabindex="-1" role="dialog"
    aria-labelledby="applicationTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px; width: 800px;" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg">
            <!-- Enhanced Header -->
            <div class="modal-header bg-primary text-white border-0 rounded-top"
                style="min-height: 50px; padding: 20px;">
                <div class="d-flex align-items-center">
                    <i class="fa fa-search-plus fa-lg mr-3 text-white"></i>
                    <h4 class="modal-title mb-0 font-weight-bold text-white" id="applicationTrackingModalLabel">
                        <span id="application_status">Application Tracking</span>
                    </h4>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="font-size: 18px; padding: 0.5rem;">
                    <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>

            <!-- Enhanced Body -->
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <!-- Timeline Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-list-alt text-primary mr-2"></i>
                        <h5 class="mb-0 text-dark font-weight-bold">Request Processing Timeline</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="font-weight-bold">Sr.No</th>
                                    <th scope="col" class="font-weight-bold">Department</th>
                                    <th scope="col" class="font-weight-bold text-center">Status</th>
                                    <th scope="col" class="font-weight-bold text-center">Remarks</th>
                                    <th scope="col" class="font-weight-bold text-center">DateTime</th>
                                </tr>
                            </thead>
                            <tbody id="result-body">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-search fa-lg mb-2 text-muted"></i>
                                        <br>
                                        <strong>Loading tracking information...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Messages Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-comments text-primary mr-2"></i>
                        <h5 class="mb-0 text-dark font-weight-bold">Send Message</h5>
                    </div>
                    <form enctype="multipart/form-data" method="post" name="sendMessage_form" id="sendMessage_form">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="d-flex align-items-center" style="cursor: pointer; margin-bottom: 0;">
                                <input type="checkbox" id="sendMessageToStudent" name="sendMessageToStudent" value="1"
                                    class="mr-2" style="transform: scale(1.2);">
                                <i class="fa fa-envelope mr-1 text-primary"></i>
                                <strong>Send message to student</strong>
                            </label>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <textarea id="messageTextarea" oninput="checkLimit()" name='messageTextarea'
                                    class="form-control" placeholder="Type your message here..." rows="3"
                                    style="resize: vertical; min-height: 60px;"></textarea>
                                <input type="hidden" id="sendToUsername" name="sendToUsername">
                                <input type="hidden" id="RequestID" name="RequestID">
                                <input type="hidden" id="login_username" value="<?=auth()->user()->username;?>"
                                    name="login_username">
                                <div class="input-group-append">
                                    <button type="button" id="messageBtn" onclick="sendMsgBtn()"
                                        class="btn btn-primary">
                                        <span id="messageBtnText">
                                            <i class="fa fa-paper-plane mr-1"></i>Send
                                        </span>
                                        <span id="messageBtnLoading" style="display: none;">
                                            <i class="fa fa-spinner fa-spin mr-1"></i>Sending...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mb-3">
                            <span id="countLimit" class="badge badge-secondary small"></span>
                        </div>
                    </form>

                    <!-- Message History -->
                    <div id="msg_cotainer_send"></div>
                    <div class="message-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop
@section('javascript')
<script type="text/javascript">
    // Global variable to track current completed filter
    var currentCompletedFilter = '';

    $(document).ready(function () {
        // alert($('#category_id').val());
        // $("#category_id").val(selectedValue);
        getCateg('noStudent', $('#taskStatus').val());

        // Clear modal data when modal is hidden/closed
        $('#approval_status').on('hidden.bs.modal', function () {
            clearModalData();
        });
    });
    function downloadPDF() {
        let staffUrl = "{{ config('app.staff_url') }}";

        // Get From date
        let fromDate = $('#fromDate').val() || '';

        // Get To date
        let toDate = $('#toDate').val() || '';

        // Get Category (check inline category first, then main category)
        let categoryId = $('#inlineCategory_id').val() || $('#category_id').val() || '0';

        // Get Tab status (Pending or Approved)
        let tabStatus = $('#taskStatus').val() || 'Pending';

        // Build URL with parameters
        let url = `${staffUrl}/HOD/examReport.php?From=${encodeURIComponent(fromDate)}&To=${encodeURIComponent(toDate)}&Category=${encodeURIComponent(categoryId)}&Tab=${encodeURIComponent(tabStatus)}`;

        window.open(url, '_blank');
    }
    // Countdown timer function for remarks
    function startCountdownTimer(targetDate, elementId) {
        const timer = setInterval(function () {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(timer);
                var element = document.getElementById(elementId);
                if (element) {
                    element.innerHTML = "<i class='fa fa-exclamation-circle' style='margin-right: 4px; font-size: 9px;'></i>Escalated";
                    if (element.parentElement) {
                        element.parentElement.style.cssText = "display: inline-block; font-size: 10px; padding: 4px 8px; margin-top: 4px; background: linear-gradient(135deg, #ffe6e6 0%, #ffd9d9 100%); color: #cc0000; border-radius: 5px; border: 1px solid #ffb3b3; box-shadow: 0 1px 3px rgba(204, 0, 0, 0.15); font-weight: 500; letter-spacing: 0.3px;";
                    }
                }
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let timeString = "";
            if (days > 0) timeString += days + "d ";
            if (hours > 0) timeString += hours + "h ";
            if (minutes > 0) timeString += minutes + "m ";
            timeString += seconds + "s";

            document.getElementById(elementId).innerHTML = timeString;
        }, 1000);
    }

    function reqBtn(action) {
        var reqRemarksValue = $('#req_remarks').val();

        // Skip validation for rollback (Pending action)
        if (action !== 'Pending') {
            if (!validateRemarks(reqRemarksValue)) {
                // Highlight the remarks input field
                $('#req_remarks').addClass('is-invalid').focus();

                // Remove highlight after 3 seconds
                setTimeout(function () {
                    $('#req_remarks').removeClass('is-invalid');
                }, 3000);

                return false;
            }
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
            cache: false, // Disable caching
            contentType: false,
            processData: false, // Ensure FormData is not processed
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                button.html('<i class="fa fa-check"></i> Updated');
                $('#approval_status').modal('hide');

                showToast('success', 'Success', response.message || 'Request updated successfully!');

                // Save current scroll position
                var scrollPosition = $(window).scrollTop();

                // Save current state
                var currentTaskStatus = $('#taskStatus').val();
                var currentStudent = $('#txtStudentid').val();
                var currentCategory = $('#inlineCategory_id').val() || $('#category_id').val();

                // Store scroll position to restore after data loads
                window.savedScrollPosition = scrollPosition;

                // Stay on current tab and reload data with current filters
                if (currentStudent && currentStudent.trim() !== '') {
                    getCateg(currentStudent, currentTaskStatus);
                } else {
                    getCateg('noStudent', currentTaskStatus);
                }
            },
            error: function (xhr, status, error) {
                button.html('<i class="fa fa-cogs mr-1"></i>Action');
                button.prop('disabled', false);
                showToast('error', 'Error', 'Failed to update request. Please try again.');
                console.error('Error:', error);
            }
        });
    }
    function status(val) {
        $('#taskStatus').val(val);

        // Clear modal data when switching tabs
        clearModalData();

        // Reset category to "All Requests" when switching tabs
        $('#category_id').val(0);
        $('#inlineCategory_id').val(0);

        // 🧹 Reset lazy loading & pagination state before new tab
        $(window).off('scroll.lazyload');
        window.paginationData = null;
        window.isLoadingMore = false;
        window.fullData = null;
        window.currentValueObj = null;
        $('#result-body-categ').off();

        // Show loading state immediately
        $('#result-body-categ').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><br><h5>Loading ' + val + ' requests...</h5><small class="text-muted">Please wait while we fetch the data</small></div>');

        // Show the appropriate spinner based on status
        if (val == 'Pending') {
            $('#pendingSpinner').show();
            $('#approvedSpinner').hide();
            getCateg('noStudent', 'Pending');
        } else if (val == 'Approved') {
            $('#approvedSpinner').show();
            $('#pendingSpinner').hide();
            getCateg('noStudent', 'Approved');
        }
    }

    /**
     * Clear all data from modal
     * Called when switching tabs or closing modal to prevent showing old data
     */
    function clearModalData() {
        // Clear student information fields
        $('#header_modal').html('');
        $('#stdname').html('');
        $('#stdschool').html('');
        $('#stdprog').html('');
        $('#stdmob').html('');
        $('#stddesc').html('');
        $('#er_id').val('');

        // Clear file upload fields
        $('#uploadedFileStudent').html('');
        $('#uploadedFile').html('');

        // Hide and clear bank details section
        $('#bank_details').hide();
        $('#application_bank_details').html('');

        // Clear form dropdown and remarks
        $('#dropDown').html('');
        $('#resolving_user_remarks').html('');
        $('#approval_user_remarks').html('');

        // Hide export buttons
        $('#export_button').hide();
        $('#export_pdf_button').hide();

        // Clear global data object
        window.currentValueObj = null;
    }

    function getCategWithCompletedFilter() {
        var studentRoll = $('#txtStudentid').val();
        var statusFilter = $('#completedStatusFilter').val();

        // Store current filter globally
        currentCompletedFilter = statusFilter;

        // Build the status parameter - use Approved instead of Completed
        var taskStatus = 'Approved';
        if (statusFilter && statusFilter !== '') {
            taskStatus = statusFilter; // 'Approved' or 'Declined'
        }

        // Sync inline category with main category
        var inlineCategoryValue = $('#inlineCategory_id').val();
        if (inlineCategoryValue) {
            $('#category_id').val(inlineCategoryValue);
        }

        console.log('Filter Selected:', statusFilter);
        console.log('Task Status:', taskStatus);
        console.log('Student Roll:', studentRoll);
        console.log('Category Selected:', inlineCategoryValue);

        // If "All Status" is selected (empty value), call refreshData function directly
        if (statusFilter === '') {
            console.log('All Status selected - Calling refreshData function');
            refreshData(); // This will get fresh data from database
            return; // Exit early, refreshData will handle everything
        }

        // Call getCateg with appropriate parameters for specific filters (Approved/Declined)
        if (studentRoll && studentRoll.trim() !== '') {
            getCateg(studentRoll, taskStatus);
        } else {
            getCateg('noStudent', taskStatus);
        }
    }

    function getCategPending() {
        var studentRoll = $('#txtStudentid').val();
        var taskStatus = 'Pending';

        // Sync inline category with main category
        var inlineCategoryValue = $('#inlineCategory_id').val();
        if (inlineCategoryValue) {
            $('#category_id').val(inlineCategoryValue);
        }

        console.log('Pending Task Status:', taskStatus);
        console.log('Student Roll:', studentRoll);
        console.log('Category Selected:', inlineCategoryValue || 'Not selected');

        // Call getCateg with appropriate parameters (loader will be handled by getCateg)
        if (studentRoll && studentRoll.trim() !== '') {
            getCateg(studentRoll, taskStatus);
        } else {
            getCateg('noStudent', taskStatus);
        }
    }


    function getCateg(sel, customTaskStatus) {
        // Step 1: Get current task status (use provided status or current tab status)
        // Important: If customTaskStatus is provided, use it. Otherwise use current tab status.
        var taskstatus = customTaskStatus || $('#taskStatus').val();

        // Step 2: Ensure Approved status is maintained
        // If user is on Approved tab, always keep it as Approved
        var currentTabStatus = $('#taskStatus').val();
        if (currentTabStatus == 'Approved') {
            taskstatus = 'Approved'; // Always keep Approved status
        }
        var request_status = $('#request_status').val();

        var fromDate = $('#fromDate').val() || 0;
        var toDate = $('#toDate').val() || 0;

        // 🧹 Clean up previous lazy loading state before new search
        $(window).off('scroll.lazyload');
        window.isLoadingMore = false;

        // Update taskStatus in hidden field to maintain state
        $('#taskStatus').val(taskstatus);

        // Step 3: Update tab UI based on task status
        if (taskstatus == 'Pending') {
            $("#setclasscompleted").removeClass("active");
            $("#setclasspending").addClass("active");
        } else if (taskstatus == 'Approved') {
            // Keep Approved tab active
            $("#setclasscompleted").addClass("active");
            $("#setclasspending").removeClass("active");
        } else {
            // For other statuses (Declined)
            $("#setclasscompleted").addClass("active");
            $("#setclasspending").removeClass("active");
        }
        // Step 1: Get category value (check inline dropdown first, then main dropdown)
        var inlineCategoryValue = $('#inlineCategory_id').val();
        var ec_id;

        // If inline category exists, use it and sync with main category
        if (inlineCategoryValue !== undefined && inlineCategoryValue !== null && inlineCategoryValue !== '') {
            ec_id = inlineCategoryValue;
            $('#category_id').val(inlineCategoryValue);
        } else {
            // Otherwise, use main category dropdown value
            ec_id = $('#category_id').val() || $('#category_id option:selected').val() || '0';
        }

        // Sync main category dropdown with selected value
        $('#category_id').val(ec_id);

        // Get selected text from main category dropdown
        var selectedText = $('#category_id option:selected').text() || $('#category_id option[value="' + ec_id + '"]').text();

        // Remove count from selectedText only for Approved/Declined status (keep count for Pending)
        if (taskstatus == 'Approved' || taskstatus == 'Declined') {
            selectedText = selectedText.replace(/\s*<span[^>]*>\([^)]*\)<\/span>/g, '');
            // Remove "Pending Count: X" format
            selectedText = selectedText.replace(/\s*\(Pending Count:\s*\d+\)\s*$/g, '');
            // Also remove any remaining numbers in parentheses (like category IDs)
            selectedText = selectedText.replace(/\s*\(\d+\)\s*$/g, '');
        }
        var roll = $('#txtStudentid').val();
        if (roll) {
            var rollno = $('#txtStudentid').val()
        } else {
            var rollno = $('#studentStatus').val();
        }

        // Add search button loader
        $('#getCategButton').addClass('btn-searching').prop('disabled', true);
        $('#getCategButton').html('<i class="fa fa-search fa-search-spin"></i> Searching...');



        // 🧹 Reset lazy loading & pagination state before new tab or filter
        $(window).off('scroll.lazyload'); // remove old scroll event
        window.paginationData = null;     // clear previous pagination reference
        window.isLoadingMore = false;     // reset loading flag
        $('#result-body-categ').off();    // clear bound events if any







        // Show search loading spinner (only if not from refresh function)
        if (!window.forceRefresh) {
            $('#result-body-categ').html('<div class="text-center p-4"><div class="modern-search-loader"><div class="search-glass"></div><div class="search-pulse"></div></div><br><br><h5>Searching data...</h5><small class="text-muted">Please wait while we search for records</small></div>');
        }

        // Build URL with search parameters (for approval - use get_categ_appr)
        var searchUrl = "{{route('get_categ_appr')}}?ec_id=" + ec_id + "&sel=" + taskstatus + "&taskStatus=" + taskstatus + '&student=' + rollno + '&request_status=' + request_status + '&page=1&per_page=100' + '&fromDate=' + fromDate + '&toDate=' + toDate;

        // Always add force refresh to bypass cache and get fresh data
        searchUrl += '&force_refresh=1&timestamp=' + Date.now();

        $.ajax({
            url: searchUrl,
            method: 'get',
            cache: false, // Disable caching
            timeout: 30000, // 30 seconds timeout
            success: function (response) {
                // Hide both spinners after request completes
                $('#pendingSpinner').hide();
                $('#approvedSpinner').hide();

                // Reset search button state (same as dashboard)
                $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');

                // Handle Laravel pagination response (same as dashboard)
                var data = response.data || [];
                var pagination = response.pagination || {};
                var totalRecords = pagination.total || 0;
                var hasMoreData = pagination.has_more_pages || false;
                var displayData = data; // All data from server (already paginated)

                // Store pagination data for lazy loading
                window.currentPagination = pagination;

                var sr_no = 1;
                var html = '';
                // Create section header
                html += '<div class="mb-4">';
                html += '<h4 class="text-primary font-weight-bold mb-3">' + selectedText + '</h4>';
                html += '<div class="row mb-3">';

                // 1️⃣ Category Dropdown with Label
                html += '<div class="col-12 col-sm-3 col-md-3 col-lg-3 mb-2 mb-sm-0">';
                html += '<label class="d-block mb-1" style="font-size: 12px;"><b>Category:</b></label>';
                if (taskstatus == 'Approved' || taskstatus == 'Declined') {
                    // For Approved/Declined status - use getCateg with current status to maintain tab
                    html += '<select class="form-control form-control-sm" id="inlineCategory_id" onchange="getCateg(\'noStudent\', \'' + taskstatus + '\')">';
                } else {
                    // For all other statuses (Pending) - use main getCateg function
                    html += '<select class="form-control form-control-sm" id="inlineCategory_id" onchange="getCateg(\'noStudent\', \'' + taskstatus + '\')">';
                }
                // Add All Requests option with proper selection
                if (ec_id == '0' || ec_id == 0) {
                    html += '<option value="0" selected>All Requests</option>';
                } else {
                    html += '<option value="0">All Requests</option>';
                }

                // Add category options with proper selection
                $('#category_id option').each(function () {
                    if ($(this).val() != '0') {
                        var value = $(this).val();
                        var text = $(this).text();

                        // Remove count from text for Approved/Declined status
                        if (taskstatus == 'Approved' || taskstatus == 'Declined') {
                            text = text.replace(/\s*<span[^>]*>\([^)]*\)<\/span>/g, '');
                            // Remove "Pending Count: X" format
                            text = text.replace(/\s*\(Pending Count:\s*\d+\)\s*$/g, '');
                            // Also remove any remaining numbers in parentheses (like category IDs)
                            text = text.replace(/\s*\(\d+\)\s*$/g, '');
                        }
                        // For Pending status, keep the original text with pending count (no modification needed)

                        var selected = (value == ec_id) ? ' selected' : '';
                        html += '<option value="' + value + '"' + selected + '>' + text + '</option>';
                    }
                });
                html += '</select>';
                html += '</div>';

                // 2️⃣ Student Roll No input with Label
                html += `
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 mb-2 mb-sm-0">
                <label class="d-block mb-1" style="font-size: 12px;"><b>Student ID:</b></label>
                <input placeholder="Student Roll No." class="form-control form-control-sm" type="text" id="txtStudentid" 
                    name="txtStudentid" maxlength="20" pattern="[A-Za-z0-9-]+" 
                    title="Only alphanumeric characters and hyphens allowed"
                    onkeypress="if(event.key==='Enter') { 
                        var roll = $(this).val(); 
                        if(validateRollNumber(roll)) { 
                            getCateg(roll && roll.trim() !== '' ? roll : 'noStudent', '${taskstatus}'); 
                        } 
                    }" 
                    oninput="validateRollNumberInput(this)">
            </div>
            `;

                // 3️⃣ From & To date fields - Responsive
                html += `
            <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2 mb-sm-0">
                <div class="row">
                    <div class="col-6 pr-1">
                        <label for="fromDate" class="d-block mb-1" style="font-size: 12px;"><b>From:</b></label>
                        <input type="date" id="fromDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 pl-1">
                        <label for="toDate" class="d-block mb-1" style="font-size: 12px;"><b>To:</b></label>
                        <input type="date" id="toDate" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            `;

                // 3️⃣ Action Buttons with label for alignment
                html += `
            <div class="col-12 col-sm-3 col-md-3 col-lg-3  mb-sm-0">
                <label class="d-block mb-1" style="font-size: 12px;"><b>&nbsp;</b></label>
                <div class="d-flex align-items-center flex-wrap">
                    <button type="button" id="getCategButton" 
                        class="btn btn-outline-primary btn-sm mr-2"
                        onclick="var roll = $('#txtStudentid').val(); 
                            if(validateRollNumber(roll)) { 
                                getCateg(roll && roll.trim() !== '' ? roll : 'noStudent', '${taskstatus}'); 
                            }">
                        <i class="fa fa-search mr-1" aria-hidden="true"></i> Search
                    </button>

                    <button type="button" class="btn btn-outline-warning btn-sm mr-2"
                        onclick="refreshData()" 
                        title="Refresh Data - Get latest data from database">
                        <i class="fa fa-refresh mr-1" aria-hidden="true"></i>
                    </button>`;

                // Add escalation filter pill button group only for Pending status (on same line)
                if (taskstatus == 'Pending') {
                    html += `
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active btn-sm" id="filter-all-btn" onclick="setFilterMode('all')">
                            <i class="fa fa-list mr-1"></i>All
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="filter-escalation-btn" onclick="setFilterMode('escalation')">
                            <i class="fa fa-exclamation-triangle mr-1"></i>Escalation
                        </button>
                    </div>`;
                }

                html += `
                </div>
            </div>
            `;

                html += '</div>'; // close controls row
                html += '</div>'; // close controls section

                html += '<div class="row mt-3">';
                html += '<div class="col-12 col-sm-12 col-md-12 col-lg-12">';
                html += '<table class="table table-bordered table-striped table-hover">';
                html += '<thead class="thead-light">';
                html += '<tr>';
                html += '<th scope="col">Sr. No.</th>';
                html += '<th scope="col">First Name</th>';
                html += '<th scope="col">School</th>';
                html += '<th scope="col">Program</th>';
                html += '<th scope="col">Type / Resolving Status</th>';
                html += '<th scope="col">Request Start Time</th>';
                html += '<th scope="col">Request End Time</th>';
                html += '<th scope="col">Resolving Person</th>';
                html += '<th scope="col">Status</th>';
                html += '<th scope="col">Actions</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                // Initialize countdown timers array
                window.countdownTimers = [];

                $.each(data, function (key, value) {

                    var esclationTime = moment(value.esclation_time, 'YYYY-MM-DD HH:mm:ss Z');
                    var currentTime = moment();
                    var color = '';
                    // Calculate the time difference in hours
                    var hoursDifference = esclationTime.diff(currentTime, 'hours');
                    // Check if the difference is within 12 hours and greater than 11 hours
                    // if (hoursDifference >= 11 && hoursDifference < 12) {
                    if (hoursDifference < 12) {
                        // Trigger an alert 12 hours before esclation_time
                        // alert('12 hours remaining until esclation_time for ' + value.full_name);
                        if (taskstatus == 'Pending') {
                            var color = 'orange';
                        }
                    }

                    // Step 1: Convert data object to JSON string
                    var valueJson = JSON.stringify(value);

                    // Step 2: Set badge color based on status
                    var badgeColor;
                    if (value.erd_status === 'Declined') {
                        badgeColor = 'red';
                    } else if (value.erd_status === 'Pending') {
                        badgeColor = '#007bff';
                    } else {
                        badgeColor = 'green';
                    }

                    // Step 3: Escape JSON for safe use in HTML attribute
                    // Replace special characters with HTML entities
                    var escapedValueJson = valueJson
                        .replace(/"/g, '&quot;')    // Double quotes
                        .replace(/'/g, '&#39;')     // Single quotes
                        .replace(/\n/g, '&#10;')     // New lines
                        .replace(/\r/g, '&#13;');    // Carriage returns

                    // Step 4: Apply styling for escalated requests
                    var rowStyle = '';
                    var rowClass = '';
                    if (color === 'orange') {
                        rowStyle = 'background-color: rgba(255,107,53,0.08);';
                        rowClass = 'escalation-border-blink';
                    }
                    var rowStyle = (value.er_request_status === 'Pending')
                        ? 'background-color:#fff4e5 !important;'
                        : '';
                    html += '<tr style="' + rowStyle + '" class="lazy-load ' + rowClass + '" data-escalation="' + color + '">';
                    html += '<td scope="col">' + (sr_no++) + '</td>';
                    html += '<td scope="col">' + value.full_name + ' (' + value.username + ')</td>';
                    html += '<td scope="col">' + value.school + '</td>';
                    html += '<td scope="col">' + value.programs + '</td>';
                    html += '<td scope="col">' + value.ec_title + ' /<b> ' + value.er_request_status + '</b></td>';
                    html += '<td  scope="col">' + value.erdatetime + '</td>';

                    // Always show escalation time value
                    html += '<td  scope="col">';
                    html += (value.esclation_time || 'NA');

                    // Check if escalation time has passed or countdown needed (only for Pending status)
                    if (taskstatus == 'Pending' && value.esclation_time) {
                        if (hoursDifference <= 0) {
                            // Time has already passed - show ESCALATED badge
                            html += '<br><span style="display: inline-block; font-size: 10px; padding: 4px 8px; margin-top: 4px; background: linear-gradient(135deg, #ffe6e6 0%, #ffd9d9 100%); color: #cc0000; border-radius: 5px; border: 1px solid #ffb3b3; box-shadow: 0 1px 3px rgba(204, 0, 0, 0.15); font-weight: 500; letter-spacing: 0.3px;">';
                            html += '<i class="fa fa-exclamation-circle" style="margin-right: 4px; font-size: 9px;"></i>';
                            html += 'Escalated';
                            html += '</span>';
                        } else if (hoursDifference <= 24) {
                            // Show countdown badge if <=24 hours left
                            var countdownId = 'countdown_' + value.erd_id;
                            html += '<br><span style="display: inline-block; font-size: 11px; padding: 6px 12px; margin-top: 5px; background: linear-gradient(135deg, #e8f4fd 0%, #c3e0f7 100%); color: #1565c0; border-radius: 6px; border: 1px solid #64b5f6; box-shadow: 0 2px 5px rgba(21, 101, 192, 0.2); font-weight: 600;">';
                            html += '<i class="fa fa-clock-o" style="margin-right: 5px; opacity: 0.9;"></i>';
                            html += '<span id="' + countdownId + '">Loading...</span>';
                            html += '</span>';
                            // Store countdown data for later initialization
                            if (!window.countdownTimers) window.countdownTimers = [];
                            window.countdownTimers.push({ id: countdownId, targetDate: new Date(value.esclation_time).getTime() });
                        }
                    }

                    html += '</td>';
                    html += '<td  scope="col">' + value.resolving_name + '</td>';
                    var statusIcon = '';
                    var statusClass = '';
                    if (value.erd_status === 'Declined') {
                        statusIcon = 'fa-times-circle';
                        statusClass = 'badge-outline-danger';
                    } else if (value.erd_status === 'Pending') {
                        statusIcon = 'fa-clock-o';
                        statusClass = 'badge-outline-primary';
                    } else {
                        statusIcon = 'fa-check-circle';
                        statusClass = 'badge-outline-success';
                    }
                    html += '<td scope="col"><span class="badge ' + statusClass + '" style="border: 2px solid ' + badgeColor + '; color: ' + badgeColor + '; background-color: transparent; font-size: 12px; padding: 0.5em 0.8em; white-space: nowrap;"><i class="fa ' + statusIcon + '" style="margin-right: 6px;"></i>' + value.erd_status + '</span></td>';
                    // html += '<td><button class="btn btn-info" onclick="openModalStatus('+value.er_id+')" type="button">Application Tracking</button></td>';
                    html += '<td scope="col">';
                    html += '<div class="btn-group-vertical" role="group">';
                    html += '<button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ',\'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus mr-1"></i>Tracking</button>';
                    html += '<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                });
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                html += '</div>';

                // Add scroll loading section if there's more data
                if (pagination && pagination.has_more_pages) {
                    var currentShowing = pagination.to || data.length;
                    html += '<div class="text-center p-3 border-top" id="scroll-trigger">';
                    html += '<p class="text-muted mb-2">Showing ' + currentShowing + ' of ' + pagination.total + ' records</p>';
                    html += '<div id="load-more-content">';
                    html += '<button class="btn btn-outline-primary btn-sm" onclick="loadMoreDataLaravel()">';
                    html += '<i class="fa fa-plus-circle"></i> Load More (' + (pagination.total - currentShowing) + ' remaining)';
                    html += '</button>';
                    html += '</div>';
                    html += '<div id="scroll-loading" style="display: none;">';
                    html += '<i class="fa fa-spinner fa-spin"></i> Loading more data...';
                    html += '</div>';
                    html += '</div>';

                    // Store pagination data for lazy loading
                    window.paginationData = {
                        current_page: pagination.current_page,
                        per_page: pagination.per_page,
                        total: pagination.total,
                        has_more_pages: pagination.has_more_pages,
                        current_showing: currentShowing,
                        filters: {
                            ec_id: ec_id,
                            sel: taskstatus,
                            taskStatus: taskstatus,
                            student: rollno,
                            request_status: request_status
                        }
                    };
                    window.isLoadingMore = false;

                    // Setup scroll-based lazy loading
                    setupScrollLazyLoadingLaravel();
                } else if (pagination) {
                    html += '<div class="text-center p-2 border-top"><small class="text-muted">Showing all ' + pagination.total + ' records</small></div>';
                }

                html += '</div>'; // close mb-4 section
                if (html != '') {
                    $('#result-body-categ').html(html);

                    // Initialize countdown timers for rows with <=24 hours left
                    if (window.countdownTimers && window.countdownTimers.length > 0) {
                        window.countdownTimers.forEach(function (timer) {
                            startCountdownTimer(timer.targetDate, timer.id);
                        });
                        // Clear the array for next render
                        window.countdownTimers = [];
                    }

                    // Apply lazy loading to table rows
                    setTimeout(function () {
                        $('.lazy-load').addClass('loaded');
                    }, 100);

                    // Apply blinking to escalation rows
                    setTimeout(function () {
                        var escalationRows = $('tr.escalation-border-blink');

                        if (escalationRows.length > 0) {
                            // Apply JavaScript blinking to escalation rows
                            escalationRows.each(function (index) {
                                var $row = $(this);
                                var blinkInterval = setInterval(function () {
                                    $row.css({
                                        'border-left-color': $row.css('border-left-color') === 'rgb(255, 107, 53)' ? '#ffa500' : '#ff6b35',
                                        'background-color': $row.css('background-color') === 'rgba(255, 107, 53, 0.08)' ? 'rgba(255, 165, 0, 0.12)' : 'rgba(255, 107, 53, 0.08)'
                                    });
                                }, 1800); // 1.8 second cycle

                                // Store interval ID for cleanup
                                $row.data('blink-interval', blinkInterval);
                            });
                        }
                    }, 200);
                } else {
                    $('#result-body-categ').html('No Result Found!');
                }

                // Restore scroll position if saved (after approval/action)
                if (window.savedScrollPosition !== undefined && window.savedScrollPosition > 0) {
                    setTimeout(function () {
                        $(window).scrollTop(window.savedScrollPosition);
                        // Clear the saved position
                        window.savedScrollPosition = undefined;
                    }, 300);
                }

                // Reset search button
                $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');
            },
            error: function (xhr, status, error) {
                // Hide both spinners on error
                $('#pendingSpinner').hide();
                $('#approvedSpinner').hide();

                // Reset search button on error (same as dashboard)
                $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');

                // Show error message (same as dashboard)
                $('#result-body-categ').html('<div class="text-center p-4 text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><br><br><h5>Error loading data</h5><small>Please try again</small></div>');
            }
        });

        // Reset force refresh flag after a short delay
        setTimeout(function () {
            window.forceRefresh = false;
        }, 1000);
    }

    // Refresh Data Function - Get fresh data from database
    function refreshData() {
        // Set force refresh flag to bypass cache
        window.forceRefresh = true;

        // Get current values from form controls
        var taskstatus = $('#taskStatus').val();
        var roll = $('#txtStudentid').val();
        var rollno = roll ? roll : $('#studentStatus').val();

        // Show loading with refresh message
        $('#result-body-categ').html('<div class="text-center p-4"><div class="modern-refresh-loader"><div class="refresh-icon"></div><div class="refresh-pulse"></div></div><br><br><h5>Refreshing data from database...</h5><small class="text-muted">Bypassing cache to get latest data</small></div>');

        // Call getCateg function with current parameters and force_refresh=1&timestamp
        getCateg(rollno, taskstatus);
    }


    // Refresh Data Function for Second Row - Get fresh data from database
    function refreshData2() {
        // Set flag to indicate this is a refresh operation
        window.forceRefresh = true;

        // Get current values from second row controls
        var taskstatus = $('#taskStatus').val();
        var request_status = $('#request_status').val();
        var ec_id = $('#inlineCategory_id2 option:selected').val() || $('#inlineCategory_id option:selected').val() || $('#category_id option:selected').val();
        var roll = $('#txtStudentid2').val();
        var rollno = roll ? roll : $('#studentStatus').val();

        // Get category from main dropdown
        $('#category_id').val(ec_id);

        // Show loading with refresh message
        $('#result-body-categ').html('<div class="text-center p-4"><div class="modern-refresh-loader"><div class="refresh-icon"></div><div class="refresh-pulse"></div></div><br><br><h5>Refreshing data from database...</h5><small class="text-muted">Bypassing cache to get latest data</small></div>');

        // Call the same getCateg function to maintain proper structure
        getCateg(rollno, taskstatus);

        // Reset flag after a short delay
        setTimeout(function () {
            window.forceRefresh = false;
        }, 1000);
    }

    // Escalation Filter Function
    function setFilterMode(mode) {
        var allBtn = $('#filter-all-btn');
        var escalationBtn = $('#filter-escalation-btn');

        if (mode === 'escalation') {
            // Show only escalation requests
            $('tbody tr').hide();
            $('tbody tr.escalation-border-blink').show();

            // Update button states
            allBtn.removeClass('active btn-primary').addClass('btn-outline-primary');
            escalationBtn.removeClass('btn-outline-danger').addClass('active btn-danger');
        } else {
            // Show all requests
            $('tbody tr').show();

            // Update button states
            allBtn.removeClass('btn-outline-primary').addClass('active btn-primary');
            escalationBtn.removeClass('active btn-danger').addClass('btn-outline-danger');
        }
    }

    // Performance optimization: Debounced scroll handler
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Setup Scroll-based Lazy Loading for Laravel Pagination
    function setupScrollLazyLoadingLaravel() {
        // Remove previous scroll handlers to avoid duplicates
        $(window).off('scroll.lazyload');

        // Create debounced scroll handler for better performance
        const debouncedScrollHandler = debounce(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadMoreDataLaravel();
            }
        }, 250);

        // Add debounced scroll handler
        $(window).on('scroll.lazyload', debouncedScrollHandler);
    }

    // Laravel-based Load More Data Functions
    function loadMoreDataLaravel() {
        if (!window.paginationData || window.isLoadingMore) return;

        var loadMoreBtn = $('button[onclick*="loadMoreDataLaravel"]');
        loadMoreBtn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        loadMoreBtn.prop('disabled', true);

        loadMoreDataLaravelScroll();
    }

    function loadMoreDataLaravelScroll() {
        if (!window.paginationData || window.isLoadingMore || !window.paginationData.has_more_pages) return;

        window.isLoadingMore = true;
        var nextPage = window.paginationData.current_page + 1;
        var filters = window.paginationData.filters;

        $.ajax({
            url: "{{route('get_categ_appr')}}?ec_id=" + filters.ec_id + "&sel=" + filters.sel + "&taskStatus=" + filters.taskStatus + "&student=" + filters.student + "&request_status=" + filters.request_status + "&page=" + nextPage + "&per_page=" + window.paginationData.per_page + "&timestamp=" + Date.now(),
            method: 'get',
            cache: false, // Disable caching
            timeout: 30000,
            success: function (response) {
                var data = response.data || [];
                var pagination = response.pagination || {};

                // Generate HTML for additional rows
                var additionalHTML = '';
                var sr_no = window.paginationData.current_showing + 1;
                var recordsAdded = 0;

                $.each(data, function (key, value) {
                    var esclationTime = moment(value.esclation_time, 'YYYY-MM-DD HH:mm:ss Z');
                    var currentTime = moment();
                    var color = '';
                    var hoursDifference = esclationTime.diff(currentTime, 'hours');
                    if (hoursDifference < 12) {
                        if ($('#taskStatus').val() == 'Pending') {
                            color = 'orange';
                        }
                    }

                    var badgeColor;
                    if (value.erd_status === 'Declined') {
                        badgeColor = 'red';
                    } else if (value.erd_status === 'Pending') {
                        badgeColor = '#007bff';
                    } else {
                        badgeColor = 'green';
                    }

                    // Step 1: Convert data object to JSON string
                    var valueJson = JSON.stringify(value);

                    // Step 2: Set badge color based on status
                    var badgeColor;
                    if (value.erd_status === 'Declined') {
                        badgeColor = 'red';
                    } else if (value.erd_status === 'Pending') {
                        badgeColor = '#007bff';
                    } else {
                        badgeColor = 'green';
                    }

                    // Step 3: Escape JSON for safe use in HTML attribute
                    // Replace special characters with HTML entities
                    var escapedValueJson = valueJson
                        .replace(/"/g, '&quot;')    // Double quotes
                        .replace(/'/g, '&#39;')      // Single quotes
                        .replace(/\n/g, '&#10;')     // New lines
                        .replace(/\r/g, '&#13;');    // Carriage returns

                    // Step 4: Apply styling for escalated requests (load more data)
                    var rowStyle = '';
                    var rowClass = '';
                    if (color === 'orange') {
                        rowStyle = 'background-color: rgba(255,107,53,0.08);';
                        rowClass = 'escalation-border-blink';
                    }

                    additionalHTML += '<tr style="' + rowStyle + '" class="lazy-load ' + rowClass + '" data-escalation="' + color + '">';
                    additionalHTML += '<td scope="col">' + (sr_no++) + '</td>';
                    additionalHTML += '<td scope="col">' + value.full_name + ' (' + value.username + ')</td>';
                    additionalHTML += '<td scope="col">' + value.school + '</td>';
                    additionalHTML += '<td scope="col">' + value.programs + '</td>';
                    additionalHTML += '<td scope="col">' + value.ec_title + ' /<b> ' + value.er_request_status + '</b></td>';
                    additionalHTML += '<td scope="col">' + value.erdatetime + '</td>';

                    // Always show escalation time value
                    additionalHTML += '<td scope="col">';
                    additionalHTML += (value.esclation_time || 'NA');

                    // Check if escalation time has passed or countdown needed (only for Pending status)
                    var taskStatus = $('#taskStatus').val();
                    if (taskStatus == 'Pending' && value.esclation_time) {
                        if (hoursDifference <= 0) {
                            // Time has already passed - show ESCALATED badge
                            additionalHTML += '<br><span style="display: inline-block; font-size: 10px; padding: 4px 8px; margin-top: 4px; background: linear-gradient(135deg, #ffe6e6 0%, #ffd9d9 100%); color: #cc0000; border-radius: 5px; border: 1px solid #ffb3b3; box-shadow: 0 1px 3px rgba(204, 0, 0, 0.15); font-weight: 500; letter-spacing: 0.3px;">';
                            additionalHTML += '<i class="fa fa-exclamation-circle" style="margin-right: 4px; font-size: 9px;"></i>';
                            additionalHTML += 'Escalated';
                            additionalHTML += '</span>';
                        } else if (hoursDifference <= 24) {
                            // Show countdown badge if <=24 hours left
                            var countdownId = 'countdown_' + value.erd_id;
                            additionalHTML += '<br><span style="display: inline-block; font-size: 11px; padding: 6px 12px; margin-top: 5px; background: linear-gradient(135deg, #e8f4fd 0%, #c3e0f7 100%); color: #1565c0; border-radius: 6px; border: 1px solid #64b5f6; box-shadow: 0 2px 5px rgba(21, 101, 192, 0.2); font-weight: 600;">';
                            additionalHTML += '<i class="fa fa-clock-o" style="margin-right: 5px; opacity: 0.9;"></i>';
                            additionalHTML += '<span id="' + countdownId + '">Loading...</span>';
                            additionalHTML += '</span>';
                            // Store countdown data for later initialization
                            if (!window.countdownTimers) window.countdownTimers = [];
                            window.countdownTimers.push({ id: countdownId, targetDate: new Date(value.esclation_time).getTime() });
                        }
                    }

                    additionalHTML += '</td>';
                    additionalHTML += '<td scope="col">' + value.resolving_name + '</td>';

                    var statusIcon = '';
                    var statusClass = '';
                    if (value.erd_status === 'Declined') {
                        statusIcon = 'fa-times-circle';
                        statusClass = 'badge-outline-danger';
                    } else if (value.erd_status === 'Pending') {
                        statusIcon = 'fa-clock-o';
                        statusClass = 'badge-outline-primary';
                    } else {
                        statusIcon = 'fa-check-circle';
                        statusClass = 'badge-outline-success';
                    }
                    additionalHTML += '<td scope="col"><span class="badge ' + statusClass + '" style="border: 2px solid ' + badgeColor + '; color: ' + badgeColor + '; background-color: transparent; font-size: 12px; padding: 0.5em 0.8em; white-space: nowrap;"><i class="fa ' + statusIcon + '" style="margin-right: 6px;"></i>' + value.erd_status + '</span></td>';
                    additionalHTML += '<td scope="col">';
                    additionalHTML += '<div class="btn-group-vertical" role="group">';
                    additionalHTML += '<button type="button" class="btn btn-outline-success mb-2" onclick="openModalStatus(' + value.er_id + ',\'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus mr-1"></i>Tracking</button>';
                    additionalHTML += '<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                    additionalHTML += '</div>';
                    additionalHTML += '</td>';
                    additionalHTML += '</tr>';

                    recordsAdded++;
                });

                // Append new rows to existing table
                $('table tbody').append(additionalHTML);

                // Initialize countdown timers for newly loaded rows with <=24 hours left
                if (window.countdownTimers && window.countdownTimers.length > 0) {
                    window.countdownTimers.forEach(function (timer) {
                        startCountdownTimer(timer.targetDate, timer.id);
                    });
                    // Clear the array for next render
                    window.countdownTimers = [];
                }

                // Update pagination data
                window.paginationData.current_page = pagination.current_page;
                window.paginationData.current_showing = pagination.to || (window.paginationData.current_showing + recordsAdded);
                window.paginationData.has_more_pages = pagination.has_more_pages;

                // Update UI
                if (pagination.has_more_pages) {
                    var remainingCount = pagination.total - window.paginationData.current_showing;
                    $('#load-more-content button').html('<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)');
                    $('#load-more-content').show();
                    $('#scroll-loading').hide();
                    $('p.text-muted').text('Showing ' + pagination.to + ' of ' + pagination.total + ' records');
                } else {
                    $('#scroll-trigger').html('<div class="text-center p-2 border-top"><small class="text-muted">Showing all ' + pagination.total + ' records</small></div>');
                    $(window).off('scroll.lazyload'); // Remove scroll listener
                }

                window.isLoadingMore = false;

                // Apply lazy loading to new rows
                setTimeout(function () {
                    $('.lazy-load').addClass('loaded');
                }, 100);

                // Apply JavaScript blinking to newly added escalation rows
                setTimeout(function () {
                    var newEscalationRows = $('tbody tr.escalation-border-blink').not('[data-blink-applied]');

                    newEscalationRows.each(function (index) {
                        var $row = $(this);
                        var blinkInterval = setInterval(function () {
                            $row.css({
                                'border-left-color': $row.css('border-left-color') === 'rgb(255, 107, 53)' ? '#ffa500' : '#ff6b35',
                                'background-color': $row.css('background-color') === 'rgba(255, 107, 53, 0.08)' ? 'rgba(255, 165, 0, 0.12)' : 'rgba(255, 107, 53, 0.08)'
                            });
                        }, 1800); // 1.8 second cycle

                        // Store interval ID for cleanup
                        $row.data('blink-interval', blinkInterval);
                        $row.attr('data-blink-applied', 'true');
                    });
                }, 200);
            },
            error: function (xhr, status, error) {
                console.error('Load more error:', xhr.responseText);
                $('#scroll-loading').hide();
                $('#load-more-content').show();
                window.isLoadingMore = false;
            }
        });
    }




    // Separate function to generate bank details HTML
    function generateBankDetailsHTML(valueObj) {

        // Check if there are any bank details values       
        var hasBankDetails = false;
        var hasNomineeInfo = valueObj.er_nominee_name || valueObj.er_nominee_cnic || valueObj.er_nomi_mobile;
        var hasPersonalInfo = valueObj.beneficiary_name || valueObj.relationship || valueObj.cnic_number || valueObj.mobile_number;
        var hasBankInfo = valueObj.bank_name || valueObj.iban || valueObj.bank_account_title;
        var hasFeeInfo = valueObj.original_tuition_fee || valueObj.original_medical_fee || valueObj.hostel_fee_original || valueObj.security_amt;
        var hasRefundInfo = valueObj.refund_tuition_fee || valueObj.refund_activity_fee || valueObj.total_refund_amount || valueObj.refund_percentage || valueObj.days_since_commencement || valueObj.hostel_fee_refundable || (valueObj.refund_security_fee != null);


        // Use id field (which is ec_id) for consistency with controller response
        var ecId = valueObj.ec_id || valueObj.id;

        if (valueObj.er_applicant_request_type == 'refund' || ecId == 86) {
            $('#export_button').show();
            $('#export_pdf_button').show();

            $('#bank_details_title').html('<i class="fa fa-money text-primary"></i> Refund Details');

        }

        if (valueObj.er_applicant_request_type == 'deferment' && ecId != 86) {
            $('#export_button').hide();

            $('#bank_details_title').html('<i class="fa fa-pause text-warning"></i> Deferment Details');
        }

        if (valueObj.er_applicant_request_type == 'degree_completion' || ecId == 23) {
            $('#export_button').show();
            $('#export_pdf_button').show();

            $('#bank_details_title').html('<i class="fa fa-graduation-cap text-success"></i> Degree Completion Details');

        }


        if ((hasPersonalInfo || hasBankInfo || hasFeeInfo || hasRefundInfo || hasNomineeInfo) && (ecId == 120 || ecId == 86 || ecId == 23)) {
            hasBankDetails = true;
            console.log('generateBankDetailsHTML - hasBankDetails set to true');
        }

        console.log('generateBankDetailsHTML - Final checks:', {
            hasBankDetails: hasBankDetails,
            ecId: ecId,
            er_applicant_request_type: valueObj.er_applicant_request_type,
            condition1: (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'deferment' || ecId == 86),
            condition2: (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'deferment' || ecId == 86)
        });

        if (hasBankDetails && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'deferment' || valueObj.er_applicant_request_type == 'degree_completion' || ecId == 86 || ecId == 23)) {
            var bankDetails = '';

            // Create a modern, compact layout
            bankDetails += '<div class="row">';

            // Personal Information Section - Only show if there's data
            if (hasPersonalInfo) {
                bankDetails += '<div class="col-md-6 mb-3">';
                bankDetails += '<div class="card border-light h-100 refund-details-card">';
                bankDetails += '<div class="card-header bg-light text-dark py-2">';
                bankDetails += '<h6 class="mb-0"><i class="fa fa-user text-primary"></i> Personal Information</h6>';
                bankDetails += '</div>';
                bankDetails += '<div class="card-body p-2">';
                bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Beneficiary Name:</small></div><div class="col-7"><strong>' + (valueObj.beneficiary_name || 'N/A') + '</strong></div></div>';
                bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Relationship:</small></div><div class="col-7"><strong>' + (valueObj.relationship || 'N/A') + '</strong></div></div>';
                bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Beneficiary CNIC:</small></div><div class="col-7"><strong>' + (valueObj.cnic_number || 'N/A') + '</strong></div></div>';
                bankDetails += '</div></div></div>';
            }

            // Bank Information Section - Only show if there's data
            if (hasBankInfo) {
                bankDetails += '<div class="col-md-6 mb-3">';
                bankDetails += '<div class="card border-light h-100 refund-details-card">';
                bankDetails += '<div class="card-header bg-light text-dark py-2">';
                bankDetails += '<h6 class="mb-0"><i class="fa fa-university text-success"></i> Bank Information</h6>';
                bankDetails += '</div>';
                bankDetails += '<div class="card-body p-2">';

                if (valueObj.bank_name) {
                    bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Bank Name:</small></div><div class="col-7"><strong>' + valueObj.bank_name + '</strong></div></div>';
                }

                if (valueObj.iban) {
                    bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">IBAN:</small></div><div class="col-7"><strong>' + valueObj.iban + '</strong></div></div>';
                }

                if (valueObj.bank_account_title) {
                    bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Account Title:</small></div><div class="col-7"><strong>' + valueObj.bank_account_title + '</strong></div></div>';
                }

                bankDetails += '</div></div></div>';
            }

            if (hasNomineeInfo) {
                bankDetails += '<div class="col-md-6 mb-3">';
                bankDetails += '<div class="card border-light h-100 refund-details-card">';
                bankDetails += '<div class="card-header bg-light text-dark py-2">';
                bankDetails += '<h6 class="mb-0"><i class="fa fa-user-plus text-info"></i> Nominee Details</h6>';
                bankDetails += '</div>';
                bankDetails += '<div class="card-body p-2">';

                if (valueObj.er_nominee_name) {
                    bankDetails += '<div class="row mb-1">' +
                        '<div class="col-5"><small class="text-muted">Name:</small></div>' +
                        '<div class="col-7"><strong>' + valueObj.er_nominee_name + '</strong></div>' +
                        '</div>';
                }

                if (valueObj.er_nominee_cnic) {
                    bankDetails += '<div class="row mb-1">' +
                        '<div class="col-5"><small class="text-muted">CNIC:</small></div>' +
                        '<div class="col-7"><strong>' + valueObj.er_nominee_cnic + '</strong></div>' +
                        '</div>';
                }

                if (valueObj.er_nomi_mobile) {
                    bankDetails += '<div class="row mb-1">' +
                        '<div class="col-5"><small class="text-muted">Mobile:</small></div>' +
                        '<div class="col-7"><strong>' + valueObj.er_nomi_mobile + '</strong></div>' +
                        '</div>';
                }

                bankDetails += '</div></div></div>';
            }

            // Fee Information Section - Only show if there's data
            if (hasFeeInfo && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'degree_completion' || ecId == 86 || ecId == 23)) {
                bankDetails += '<div class="col-md-6 mb-3">';
                bankDetails += '<div class="card border-light h-100 refund-details-card">';
                bankDetails += '<div class="card-header bg-light text-dark py-2">';
                bankDetails += '<h6 class="mb-0"><i class="fa fa-credit-card text-warning"></i> Original Fees</h6>';
                bankDetails += '</div>';
                bankDetails += '<div class="card-body p-2">';

                if (valueObj.original_tuition_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Tuition Fee:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.original_tuition_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.original_medical_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Medical Fee:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.original_medical_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.original_activity_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Activity Fee:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.original_activity_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.security_amt) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Security Amount:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.security_amt || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.hostel_fee_original) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Hostel Fee:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.hostel_fee_original || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.scholarship_amount && parseFloat(valueObj.scholarship_amount) > 0) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Scholarship Amount:</small></div><div class="col-5"><strong class="text-success">Rs. ' + parseFloat(valueObj.scholarship_amount || 0).toFixed(2) + '</strong></div></div>';
                }

                bankDetails += '</div></div></div>';
            }

            // Refund Information Section - Only show for refund type or ec_id 86
            if (hasRefundInfo && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'degree_completion' || ecId == 86 || ecId == 23)) {
                bankDetails += '<div class="col-md-6 mb-3">';
                bankDetails += '<div class="card border-light h-100 refund-details-card">';
                bankDetails += '<div class="card-header bg-light text-dark py-2">';
                bankDetails += '<h6 class="mb-0"><i class="fa fa-refresh text-danger"></i> Refund Details</h6>';
                bankDetails += '</div>';
                bankDetails += '<div class="card-body p-2">';

                if (valueObj.refund_tuition_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Tuition Refund:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.refund_tuition_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.refund_medical_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Medical Refund:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.refund_medical_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.refund_activity_fee) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Activity Refund:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.refund_activity_fee || 0).toFixed(2) + '</strong></div></div>';
                }


                // Show security refund if it exists (including 0)
                if (valueObj.refund_security_fee != null) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Security Refund:</small></div><div class="col-5"><strong>Rs. ' + parseFloat(valueObj.refund_security_fee || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.hostel_fee_refundable) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Hostel Refundable:</small></div><div class="col-5"><strong>' + valueObj.hostel_fee_refundable + '</strong></div></div>';
                }
                if (valueObj.total_refund_amount) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Total Refund:</small></div><div class="col-5"><strong class="text-danger">Rs. ' + parseFloat(valueObj.total_refund_amount || 0).toFixed(2) + '</strong></div></div>';
                }
                if (valueObj.refund_percentage) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Tuition Refund %:</small></div><div class="col-5"><strong>' + parseFloat(valueObj.refund_percentage || 0).toFixed(2) + '%</strong></div></div>';
                }

                if (valueObj.days_since_commencement) {
                    bankDetails += '<div class="row mb-1"><div class="col-7"><small class="text-muted">Days Since Start:</small></div><div class="col-5"><strong>' + parseInt(valueObj.days_since_commencement || 0) + '</strong></div></div>';
                }


                bankDetails += '</div></div></div>';
            }

            bankDetails += '</div>'; // Close main row


            if (valueObj.er_applicant_request_type == 'deferment') {
                bankDetails += '<div class="row mb-3"><div class="col-12"><div class="alert alert-info" style="background-color: #e8f4fd; border: 1px solid #bee5eb; border-radius: 8px; padding: 12px 16px; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"><div class="d-flex align-items-center"><i class="fa fa-info-circle me-2 mr-1" style="color: #0c5460; font-size: 16px;"></i><small style="color: #0c5460; font-weight: 500; margin: 0;">Applicant status will be marked as "Defer" on request completion for deferment case</small></div></div></div>';
            }


            if (valueObj.er_applicant_request_type == 'refund') {
                bankDetails += '<div class="row mb-3"><div class="col-12"><div class="alert alert-info" style="background-color: #e8f4fd; border: 1px solid #bee5eb; border-radius: 8px; padding: 12px 16px; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"><div class="d-flex align-items-center"><i class="fa fa-info-circle me-2 mr-1" style="color: #0c5460; font-size: 16px;"></i><small style="color: #0c5460; font-weight: 500; margin: 0;">Applicant status will be marked as "Left" on request completion for refund case</small></div></div></div>';
            }

            if (valueObj.er_applicant_request_type == 'degree_completion') {
                bankDetails += '<div class="row mb-3"><div class="col-12"><div class="alert alert-info" style="background-color: #e8f4fd; border: 1px solid #bee5eb; border-radius: 8px; padding: 12px 16px; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"><div class="d-flex align-items-center"><i class="fa fa-info-circle me-2 mr-1" style="color: #0c5460; font-size: 16px;"></i><small style="color: #0c5460; font-weight: 500; margin: 0;">Bank details and refund information will be processed for degree completion case</small></div></div></div>';
            }


            return {
                hasData: true,
                html: bankDetails
            };
        } else {
            return {
                hasData: false,
                html: ''
            };
        }
    }

    /**
     * Handle Details button click
     * Gets JSON data from button's data-value attribute and displays it in modal
     * @param {HTMLElement} buttonElement - The button that was clicked
     */
    function setProfileFromData(buttonElement) {
        try {
            // Step 1: Get escaped JSON from button's data attribute
            var escapedValueJson = $(buttonElement).attr('data-value');

            // Step 2: Convert HTML entities back to normal characters
            var valueJson = escapedValueJson
                .replace(/&quot;/g, '"')      // Convert &quot; to "
                .replace(/&#39;/g, "'")      // Convert &#39; to '
                .replace(/&#10;/g, '\n')     // Convert &#10; to newline
                .replace(/&#13;/g, '\r');    // Convert &#13; to carriage return

            // Step 3: Parse JSON string to JavaScript object
            var valueObj = JSON.parse(valueJson);

            // Step 4: Display the data in modal
            setProfile(valueObj);
        } catch (e) {
            // If anything goes wrong, show error message
            console.error('Error loading request details:', e);
            alert('Error loading request details. Please try again.');
        }
    }

    /**
     * Display request details in modal
     * @param {Object} valueObj - Request data object
     */
    function setProfile(valueObj) {
        // Step 1: Clear any old data from modal
        clearModalData();

        // Step 2: If data is string, convert to object (backward compatibility)
        if (typeof valueObj === 'string') {
            try {
                valueObj = JSON.parse(valueObj);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Error loading request details. Please try again.');
                return;
            }
        }

        // Step 3: Save data globally for export functions (Excel/PDF)
        window.currentValueObj = valueObj;

        // Step 4: Generate bank details HTML (if available)
        var bankDetailsResult = generateBankDetailsHTML(valueObj);

        // Step 5: Show or hide bank details section
        if (bankDetailsResult.hasData) {
            $('#bank_details').show();
            $('#application_bank_details').html(bankDetailsResult.html);
        } else {
            $('#bank_details').hide();
        }




        // Step 6: Fill modal with student information
        $('#header_modal').html('Status' + ' (' + valueObj.username + ')')
        $('#stdname').html(valueObj.full_name);
        let statusClass = valueObj.sai_student_status == 'Left' ? 'badge badge-danger' : 'badge badge-primary';
        $('#stdStatus').html('<span class="' + statusClass + '">' + (valueObj.sai_student_status ? valueObj.sai_student_status : 'N/A') + '</span>');
        $('#stdschool').html(valueObj.school);
        $('#stdprog').html(valueObj.programs);
        $('#stdmob').html(valueObj.mobile);
        $('#stddesc').html(valueObj.description);
        $('#er_id').val(valueObj.erd_id);

        if (valueObj.id == 18 || valueObj.id == 25 || valueObj.id == 19 || valueObj.id == 23) {
            if (valueObj.id == 18) {
                if (valueObj.er_student_upload_challan) {
                    let str = valueObj.er_student_upload_challan;
                    if (str.search("drive.google.com") > 0) {
                        $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_student_upload_challan + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                    }
                    else {

                        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_student_upload_challan + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');

                    }

                } else {
                    $('#uploadedFileStudent').html('N/A');
                }
            } else {
                if (valueObj.er_request_file) {
                    let str = valueObj.er_request_file;
                    if (str.search("drive.google.com") > 0) {
                        $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                    }
                    else {

                        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');

                    }

                } else {
                    $('#uploadedFileStudent').html('N/A');
                }
            }
        } else {
            let str = valueObj.er_request_file;
            if (valueObj.er_request_file) {
                if (str.search("drive.google.com") > 0) {
                    $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                }
                else {
                    $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                }
            } else {
                $('#uploadedFileStudent').html('N/A');
            }
            // if(valueObj.er_request_file){
            //     $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
            // }else{
            //     $('#uploadedFileStudent').html('N/A');
            // }
        }
        if (valueObj.google_drive_url) {
            $('#uploadedFile').html('<a href="' + valueObj.google_drive_url + '"><b>Uploaded File by Department <i class="fa fa-download larger-icon"></i></b></a>');
        } else {
            $('#uploadedFile').html('N/A');
        }
        // console.log(valueObj);
        if (valueObj.id == 85) {
            let id = valueObj.er_id;
            $('#view_status_div').show();
            $('#view_status').html(`<a class="btn btn-primary" href="http://devstaff.bnu.edu.pk/leaving_form_report.php?id=${id}" target="_blank">View Status</a>`);
        } else {
            $('#view_status_div').hide();
            $('#view_status').html('')
        }
        if (valueObj.id == 86) {
            let id = valueObj.username;
            let erID = valueObj.er_id;
            $('#check_form_div').show();
            // $('#checkForm').html(`<a class="btn btn-primary" href="http://devstaff.bnu.edu.pk/HOD/studentExitForm.php?id=${id}" target="_blank">View PDF</a>`);
            $('#checkForm').html(`  <a  href="http://devstaff.bnu.edu.pk/leaving_form_report.php?id=${erID}" class="btn btn-primary">View PDF </a>`);
        } else {
            $('#check_form_div').hide();
            $('#checkForm').html('')
        }
        $('#resolving_user_remarks').html(valueObj.er_remarks);
        $('#approval_user_remarks').html(valueObj.er_ecd_remarks);
        var taskstatus = $('#taskStatus').val();
        var html = '';
        html += '<form id="approvalForm">';
        html += '<input type="hidden" id="er_id" name="erd_id" value="' + valueObj.erd_id + '">';
        html += '<input type="hidden"  name="er_id" value="' + valueObj.er_id + '">';
        // Get dept_id from Laravel (Blade)
        const dept_id = {{ auth()->user()->dept_id }};

        // Show nominee details ONLY if dept_id == 61 (Exam Department)
        if (dept_id == 61) {
            html += '<div class="col-md-12 mb-3">'; // ✅ same width as Remarks section
            html += '  <div class="card border-light h-100 refund-details-card">';
            html += '    <div class="card-header bg-light text-dark py-2">';
            html += '      <h6 class="mb-0"><i class="fa fa-university text-success"></i> Personal Information</h6>';
            html += '    </div>';
            html += '    <div class="card-body p-2">';

            if (valueObj.er_student_mobile) {
                html += '<div class="row mb-1">';
                html += '  <div class="col-5"><small class="text-muted">Student Mobile:</small></div>';
                html += '  <div class="col-7"><strong>' + valueObj.er_student_mobile + '</strong></div>';
                html += '</div>';
            }

            if (valueObj.er_nominee_name) {
                html += '<div class="row mb-1">';
                html += '  <div class="col-5"><small class="text-muted">Nominee Name:</small></div>';
                html += '  <div class="col-7"><strong>' + valueObj.er_nominee_name + '</strong></div>';
                html += '</div>';
            }

            if (valueObj.er_nominee_cnic) {
                html += '<div class="row mb-1">';
                html += '  <div class="col-5"><small class="text-muted">Nominee CNIC:</small></div>';
                html += '  <div class="col-7"><strong>' + valueObj.er_nominee_cnic + '</strong></div>';
                html += '</div>';
            }

            if (valueObj.er_nomi_mobile) {
                html += '<div class="row mb-1">';
                html += '  <div class="col-5"><small class="text-muted">Nominee Mobile:</small></div>';
                html += '  <div class="col-7"><strong>' + valueObj.er_nomi_mobile + '</strong></div>';
                html += '</div>';
            }
            if (valueObj.er_beneficiary_detail) {
                html += '<div class="row mb-1">';
                html += '   <div class="col-5"><small class="text-muted">Beneficiary Detail:</small></div>';
                html += '   <div class="col-7"><strong>' + valueObj.er_beneficiary_detail + '</strong></div>';
                html += '</div>';
            }

            html += '    </div>'; // card-body
            html += '  </div>';   // card
            html += '</div>';     // col
        }
        if (taskstatus == 'Pending') {
            html += '<label for="remarks"><b>Remarks:</b></label>';
            html += '<div class="form-group">';
            html += '<input type="text" class="form-control" name="remarks" id="req_remarks" maxlength="1500" placeholder="Enter remarks (max 1500 characters)" oninput="updateRemarksCounter(this)">';
            html += '<div class="d-flex justify-content-end mt-1">';
            html += '<span class="badge badge-secondary" style="font-size: 0.7rem;" id="remarks_counter">0/1500</span>';
            html += '</div>';
            html += '</div>';
        }
        if (valueObj.er_request_status == 'Pending' || valueObj.chkdeclined >= '0') {
            html += '<div  style="float:right;margin-top: 10px;margin-bottom: 10px">';
            html += '<span id="remarksReq"></span>';
            html += '<button id="btnloader" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ';
            html += '<i class="fa fa-cogs mr-1"></i>Action';
            html += '</button>';
            html += '<div class="dropdown-menu"> ';
            if (taskstatus == 'Approved') {
                html += '<a class="dropdown-item" onclick="reqBtn(\'Pending\')">RollBack</a>';
            } else {
                html += '<a class="dropdown-item" onclick="reqBtn(\'Approved\')">Approve</a>';
                html += '<a class="dropdown-item" onclick="reqBtn(\'Declined\')">Decline</a>';
                html += '<a class="dropdown-item" onclick="reqBtn(\'futher_action\')">Further Action Needed</a>';
            }
            html += '</div>';
        } else {
            html += ' <span class="badge badge-pill badge-primary text-right">Request Completed By Resolving User, cannot Rollback</span>';
        }
        html += '</div>';
        html += '</form>';
        $('#dropDown').html(html);

    }
    function openModalStatus(id, username) {
        // alert(id);
        $('#msg_cotainer_send').html('');
        $('#RequestID').val(id);
        // $('#application_status').html('asdasd');
        $("#application_status").css("font-weight", "bold");
        //   Use jQuery to show the modal
        $("#applicationStatus").modal("show");
        $.ajax({
            type: 'get',
            url: '/eportal-approval-authority?id=' + id + '&timestamp=' + Date.now(),
            cache: false, // Disable caching
            processData: false,
            contentType: false,
            data: {
                'id': id
            },
            success: function (res) {
                $('#application_status').html(res.data[0].title + ' (' + username + ')');

                html = '';
                $.each(res.data, function (key, value) {
                    var srno = parseInt(key + 1);
                    html += '<tr>';
                    html += '<td>' + srno + '</td>';
                    html += '<td >' + value.department + '(' + value.full_name + ')' + '</td>';
                    var statusIcon, statusClass;
                    if (value.erd_status === 'Declined') {
                        statusIcon = 'fa-times-circle';
                        statusClass = 'badge-danger';
                    } else if (value.erd_status === 'Pending') {
                        statusIcon = 'fa-clock-o';
                        statusClass = 'badge-warning';
                    } else if (value.erd_status === 'Approved') {
                        statusIcon = 'fa-check-circle';
                        statusClass = 'badge-success';
                    } else {
                        statusIcon = 'fa-info-circle';
                        statusClass = 'badge-info';
                    }
                    html += '<td><span class="badge ' + statusClass + '"><i class="fa ' + statusIcon + ' mr-1"></i>' + value.erd_status + '</span></td>';
                    html += '<td>' + (value.er_ecd_remarks || 'N/A') + '</td>';
                    html += '<td>' + (value.er_ecd_appr_datetime ? formatDate(value.er_ecd_appr_datetime) : 'N/A') + '</td>';
                    html += '</tr>';
                });
                if (html != '') {
                    $('#result-body').html(html);
                    $('#canvas').show();
                } else {
                    $('#teacher_remarks').hide();
                    $('#pdf').hide();
                    $('#result-body').html('<tr><td colspan="12" style="text-align:center;font-size:24px">No Result Found!</td></tr>');
                    $('#canvas').hide();
                    $('#course_name').html('Course Name');
                }
            },
            error: function (xhr) {

            }
        });
        getMsgs(id);
    }
    function formatDate(dateString) {
        const date = new Date(dateString);

        // Define options for formatting
        const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };

        // Get formatted components
        const day = date.toLocaleString('en-US', { day: '2-digit' });
        const month = date.toLocaleString('en-US', { month: 'long' });
        const year = date.toLocaleString('en-US', { year: 'numeric' });
        const time = date.toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

        // Construct the final formatted string
        return `${day}-${month}-${year} at ${time}`;
    }
    function getMsgs(id) {
        var ad_username = "<?php echo auth()->user()->username; ?>";
        var token = "<?php echo csrf_token(); ?>"; // CSRF token
        $.ajax({
            type: 'get',
            url: '/eportal/get-user-messages?reqid=' + id + '&timestamp=' + Date.now(),
            cache: false, // Disable caching
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
                let tmTitle = res.tmTitle;
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
                    getMsgs($('#RequestID').val());
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



    // Alternative function for better formatted Excel export
    function exportBankDetailsToExcelFormatted() {
        try {
            const currentValueObj = window.currentValueObj || {};

            // Extract only the name part before the opening parenthesis
            const fullNameText = $('#stdname').text() || 'N/A';
            const cleanName = fullNameText.includes('(') ? fullNameText.split('(')[0].trim() : fullNameText;

            const studentInfo = [
                ['Name', cleanName],
                ['Applicant ID', currentValueObj.er_sai_enrollment_no || 'N/A'],
                ['School', $('#stdschool').text() || 'N/A'],
                ['Program', $('#stdprog').text() || 'N/A'],
                ['Mobile', $('#stdmob').text() || 'N/A']
            ];

            const personalInfo = [
                ['Beneficiary Name', currentValueObj.beneficiary_name || 'N/A'],
                ['CNIC', currentValueObj.cnic_number || 'N/A'],
                ['Mobile', currentValueObj.mobile_number || 'N/A'],
                ['Relationship', currentValueObj.relationship || 'N/A']
            ];

            const bankInfo = [
                ['Bank Name', currentValueObj.bank_name || 'N/A'],
                ['IBAN', currentValueObj.iban || 'N/A'],
                ['Account Title', currentValueObj.bank_account_title || 'N/A']
            ];

            const originalFees = [
                ['Tuition Fee', currentValueObj.original_tuition_fee ? 'Rs. ' + parseFloat(currentValueObj.original_tuition_fee).toFixed(2) : 'N/A'],
                ['Medical Fee', currentValueObj.original_medical_fee ? 'Rs. ' + parseFloat(currentValueObj.original_medical_fee).toFixed(2) : 'N/A'],
                ['Activity Fee', currentValueObj.original_activity_fee ? 'Rs. ' + parseFloat(currentValueObj.original_activity_fee).toFixed(2) : 'N/A'],
                ['Security Amount', currentValueObj.security_amt ? 'Rs. ' + parseFloat(currentValueObj.security_amt).toFixed(2) : 'N/A'],
                ['Hostel Fee', currentValueObj.hostel_fee_original ? 'Rs. ' + parseFloat(currentValueObj.hostel_fee_original).toFixed(2) : 'N/A'],
                ['Scholarship Amount', currentValueObj.scholarship_amount && parseFloat(currentValueObj.scholarship_amount) > 0 ? 'Rs. ' + parseFloat(currentValueObj.scholarship_amount).toFixed(2) : 'N/A']
            ];

            const refundDetails = [
                ['Refund Details', 'Amount'],
                ['Tuition Refund', currentValueObj.refund_tuition_fee ? 'Rs. ' + parseFloat(currentValueObj.refund_tuition_fee).toFixed(2) : 'N/A'],
                ['Medical Refund', currentValueObj.refund_medical_fee ? 'Rs. ' + parseFloat(currentValueObj.refund_medical_fee).toFixed(2) : 'N/A'],
                ['Activity Refund', currentValueObj.refund_activity_fee ? 'Rs. ' + parseFloat(currentValueObj.refund_activity_fee).toFixed(2) : 'N/A'],
                ['Security Refund', currentValueObj.refund_security_fee ? 'Rs. ' + parseFloat(currentValueObj.refund_security_fee).toFixed(2) : 'N/A'],
                ['Total Refund', currentValueObj.total_refund_amount ? 'Rs. ' + parseFloat(currentValueObj.total_refund_amount).toFixed(2) : 'N/A'],
                ['Refund Percentage', currentValueObj.refund_percentage ? parseFloat(currentValueObj.refund_percentage).toFixed(2) + '%' : 'N/A'],
                ['Days Since Commencement', currentValueObj.days_since_commencement || 'N/A'],
                ['Hostel Fee Refundable', currentValueObj.hostel_fee_refundable || 'N/A']
            ];

            // Build worksheet data in the required format
            const worksheetData = [
                // Header row
                ['Student Information', '', 'Personal Information', '', 'Bank Information', '', 'Original Fees', ''],
                // Data rows - fill all 6 rows (including hostel fee and scholarship)
                [studentInfo[0][0], studentInfo[0][1], personalInfo[0][0], personalInfo[0][1], bankInfo[0][0], bankInfo[0][1], originalFees[0][0], originalFees[0][1]],
                [studentInfo[1][0], studentInfo[1][1], personalInfo[1][0], personalInfo[1][1], bankInfo[1][0], bankInfo[1][1], originalFees[1][0], originalFees[1][1]],
                [studentInfo[2][0], studentInfo[2][1], personalInfo[2][0], personalInfo[2][1], bankInfo[2][0], bankInfo[2][1], originalFees[2][0], originalFees[2][1]],
                [studentInfo[3][0], studentInfo[3][1], personalInfo[3][0], personalInfo[3][1], '', '', originalFees[3][0], originalFees[3][1]],
                ['', '', '', '', '', '', originalFees[4][0], originalFees[4][1]],
                ['', '', '', '', '', '', originalFees[5] ? originalFees[5][0] : '', originalFees[5] ? originalFees[5][1] : ''],
                // Empty rows for spacing
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                // Refund Details section
                ...refundDetails.map(row => [row[0], row[1], '', '', '', '', '', ''])
            ];

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(worksheetData);

            // Define border style
            const borderStyle = {
                top: { style: "thin", color: { rgb: "000000" } },
                bottom: { style: "thin", color: { rgb: "000000" } },
                left: { style: "thin", color: { rgb: "000000" } },
                right: { style: "thin", color: { rgb: "000000" } }
            };

            // Define header style
            const headerStyle = {
                border: borderStyle,
                fill: { fgColor: { rgb: "D9D9D9" } },
                font: { bold: true },
                alignment: { horizontal: "center", vertical: "center" }
            };

            // Define refund header style
            const refundHeaderStyle = {
                border: borderStyle,
                fill: { fgColor: { rgb: "D9D9D9" } },
                font: { bold: true },
                alignment: { horizontal: "center", vertical: "center" }
            };

            // Define regular cell style
            const regularCellStyle = {
                border: borderStyle,
                alignment: { vertical: "center" }
            };

            // Apply styles
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r; ++R) {
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cellRef = XLSX.utils.encode_cell({ r: R, c: C });
                    if (!ws[cellRef]) {
                        ws[cellRef] = { t: 's', v: '' };
                    }

                    // Apply styles based on position
                    if (R === 0) {
                        // Main headers
                        ws[cellRef].s = headerStyle;
                    } else if (R === 9 && C <= 1) {
                        // Refund Details header
                        ws[cellRef].s = refundHeaderStyle;
                    } else if ((R >= 1 && R <= 6 && C <= 7) || (R >= 9 && R <= 17 && C <= 1)) {
                        // Data cells in main sections and refund section
                        ws[cellRef].s = regularCellStyle;
                    } else {
                        // Empty cells - no border
                        ws[cellRef].s = {};
                    }
                }
            }

            // Set column widths
            ws['!cols'] = [
                { width: 18 }, // A
                { width: 25 }, // B
                { width: 20 }, // C
                { width: 25 }, // D
                { width: 18 }, // E
                { width: 35 }, // F - wider for IBAN
                { width: 18 }, // G
                { width: 20 }  // H
            ];

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Refund Info");

            // Export to Excel file
            try {
                XLSX.writeFile(wb, `refund_details_${new Date().toISOString().slice(0, 10)}.xlsx`);
                console.log('Excel file exported successfully');
            } catch (error) {
                console.error('Error exporting Excel file:', error);
                alert('Error exporting Excel file. Please try again.');
            }
        } catch (error) {
            console.error('Excel export error:', error);
            alert('Error exporting Excel file. Please try again.');
        }
    }

    // PDF export function - Professional portrait layout
    function exportBankDetailsToPDFFormatted() {
        try {
            const currentValueObj = window.currentValueObj || {};

            // Create PDF using jsPDF with A4 landscape orientation
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape', 'mm', 'a4');

            // Set font and size
            doc.setFont('helvetica');
            doc.setFontSize(18);

            // Add title with soft modern styling
            doc.setFillColor(100, 181, 246); // Soft blue color
            doc.rect(20, 15, 170, 8, 'F');
            doc.setTextColor(255, 255, 255);
            doc.text('Student Refund Information Report', 105, 22, { align: 'center' });

            // Reset text color
            doc.setTextColor(0, 0, 0);

            // Add date and user info
            doc.setFontSize(10);
            doc.text(`Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}`, 20, 35);

            // Add generated by on left and request date on right
            doc.text(`Generated by: <?= auth()->user()->email ?>`, 20, 42);

            if (currentValueObj.erdatetime) {
                // Get page width and position request date on the right
                const pageWidth = doc.internal.pageSize.width;

                // Format the date to be more human readable
                const dateObj = new Date(currentValueObj.erdatetime);
                const formattedDate = dateObj.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const formattedTime = dateObj.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                const requestDateText = `Request Date: ${formattedDate} at ${formattedTime}`;
                const textWidth = doc.getTextWidth(requestDateText);
                doc.text(requestDateText, pageWidth - textWidth - 20, 42);
            }

            let yPosition = 50;

            // Row 1: Student Information, Personal Information, and Bank Details
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.setFillColor(240, 240, 240);
            doc.rect(20, yPosition - 5, 80, 6, 'F');
            doc.text('Student Information', 25, yPosition);

            doc.setFillColor(240, 240, 240);
            doc.rect(110, yPosition - 5, 80, 6, 'F');
            doc.text('Beneficiary Information', 115, yPosition);

            doc.setFillColor(240, 240, 240);
            doc.rect(200, yPosition - 5, 80, 6, 'F');
            doc.text('Bank Details', 205, yPosition);

            yPosition += 8;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');

            // Student Information
            // Extract only the name part before the opening parenthesis
            const fullNameText = $('#stdname').text() || 'N/A';
            const cleanName = fullNameText.includes('(') ? fullNameText.split('(')[0].trim() : fullNameText;

            const studentInfo = [
                ['Name', cleanName],
                ['Applicant ID', currentValueObj.username || 'N/A'],
                ['Mobile Number', currentValueObj.mobile || 'N/A'],
                ['School', $('#stdschool').text() || 'N/A'],
                ['Program', $('#stdprog').text() || 'N/A']
            ];

            studentInfo.forEach(([label, value]) => {
                doc.setFont('helvetica', 'bold');
                doc.text(label + ':', 25, yPosition);
                doc.setFont('helvetica', 'normal');
                doc.text(value, 60, yPosition);
                yPosition += 8;
            });

            // Personal Information
            yPosition = 55;
            if (currentValueObj.beneficiary_name || currentValueObj.cnic_number || currentValueObj.mobile_number) {
                const personalInfo = [
                    ['Beneficiary Name', currentValueObj.beneficiary_name || 'N/A'],
                    ['CNIC', currentValueObj.cnic_number || 'N/A'],
                    ['Mobile', currentValueObj.mobile_number || 'N/A'],
                    ['Relationship', currentValueObj.relationship || 'N/A']
                ];

                personalInfo.forEach(([label, value]) => {
                    doc.setFont('helvetica', 'bold');
                    doc.text(label + ':', 115, yPosition);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, 160, yPosition);
                    yPosition += 8;
                });
            }

            // Bank Details
            yPosition = 55;
            if (currentValueObj.bank_name || currentValueObj.iban || currentValueObj.bank_account_title) {
                const bankInfo = [
                    ['Bank Name', currentValueObj.bank_name || 'N/A'],
                    ['IBAN', currentValueObj.iban || 'N/A'],
                    ['Account Title', currentValueObj.bank_account_title || 'N/A']
                ];

                bankInfo.forEach(([label, value]) => {
                    doc.setFont('helvetica', 'bold');
                    doc.text(label + ':', 205, yPosition);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, 240, yPosition);
                    yPosition += 8;
                });
            }

            // Row 2: Original Fees, Refund Details, and Remaining Refund Info (3 Sections)
            yPosition = 100;
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.setFillColor(240, 240, 240);
            doc.rect(20, yPosition - 5, 80, 6, 'F');
            doc.text('Original Fees', 25, yPosition);

            doc.setFillColor(240, 240, 240);
            doc.rect(110, yPosition - 5, 80, 6, 'F');
            doc.text('Refund Details', 115, yPosition);

            doc.setFillColor(240, 240, 240);
            doc.rect(200, yPosition - 5, 80, 6, 'F');
            doc.text('', 205, yPosition);

            yPosition += 8;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');

            // Original Fees (Left Side)
            const originalFees = [];
            if (currentValueObj.original_tuition_fee) {
                originalFees.push(['Tuition Fee', 'Rs. ' + parseFloat(currentValueObj.original_tuition_fee).toFixed(2)]);
            }
            if (currentValueObj.original_medical_fee) {
                originalFees.push(['Medical Fee', 'Rs. ' + parseFloat(currentValueObj.original_medical_fee).toFixed(2)]);
            }
            if (currentValueObj.original_activity_fee) {
                originalFees.push(['Activity Fee', 'Rs. ' + parseFloat(currentValueObj.original_activity_fee).toFixed(2)]);
            }
            if (currentValueObj.security_amt) {
                originalFees.push(['Security Amount', 'Rs. ' + parseFloat(currentValueObj.security_amt).toFixed(2)]);
            }
            if (currentValueObj.hostel_fee_original) {
                originalFees.push(['Hostel Fee', 'Rs. ' + parseFloat(currentValueObj.hostel_fee_original).toFixed(2)]);
            }
            if (currentValueObj.scholarship_amount && parseFloat(currentValueObj.scholarship_amount) > 0) {
                originalFees.push(['Scholarship Amount', 'Rs. ' + parseFloat(currentValueObj.scholarship_amount).toFixed(2)]);
            }

            // Refund Details (Middle Section) - 5 specific items
            const refundDetails = [];
            if (currentValueObj.refund_tuition_fee) {
                refundDetails.push(['Tuition Refund', 'Rs. ' + parseFloat(currentValueObj.refund_tuition_fee).toFixed(2)]);
            }
            if (currentValueObj.refund_medical_fee) {
                refundDetails.push(['Medical Refund', 'Rs. ' + parseFloat(currentValueObj.refund_medical_fee).toFixed(2)]);
            }
            if (currentValueObj.refund_activity_fee) {
                refundDetails.push(['Activity Refund', 'Rs. ' + parseFloat(currentValueObj.refund_activity_fee).toFixed(2)]);
            }
            if (currentValueObj.refund_security_fee) {
                refundDetails.push(['Security Refund', 'Rs. ' + parseFloat(currentValueObj.refund_security_fee).toFixed(2)]);
            }
            if (currentValueObj.refund_percentage) {
                refundDetails.push(['Refund %', parseFloat(currentValueObj.refund_percentage).toFixed(2) + '%']);
            }

            // Remaining Refund Info (Right Section)
            const remainingRefundInfo = [];


            if (currentValueObj.hostel_fee_refundable) {
                remainingRefundInfo.push(['Hostel Refundable', currentValueObj.hostel_fee_refundable]);
            }
            if (currentValueObj.days_since_commencement) {
                remainingRefundInfo.push(['Days Since Start', currentValueObj.days_since_commencement]);
            }
            if (currentValueObj.total_refund_amount) {
                remainingRefundInfo.push(['Total Refund', 'Rs. ' + parseFloat(currentValueObj.total_refund_amount).toFixed(2)]);
            }
            if (currentValueObj.scholarship_amount && parseFloat(currentValueObj.scholarship_amount) > 0) {
                remainingRefundInfo.push(['Scholarship Amount', 'Rs. ' + parseFloat(currentValueObj.scholarship_amount).toFixed(2)]);
            }

            // Display all three sections side by side
            const maxRows = Math.max(originalFees.length, refundDetails.length, remainingRefundInfo.length);

            for (let i = 0; i < maxRows; i++) {
                // Left side - Original Fees
                if (i < originalFees.length) {
                    const [label, value] = originalFees[i];
                    doc.setFont('helvetica', 'bold');
                    doc.text(label + ':', 25, yPosition);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, 60, yPosition);
                }

                // Middle side - Refund Details
                if (i < refundDetails.length) {
                    const [label, value] = refundDetails[i];
                    doc.setFont('helvetica', 'bold');
                    doc.text(label + ':', 115, yPosition);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, 160, yPosition);
                }

                // Right side - Remaining Refund Info
                if (i < remainingRefundInfo.length) {
                    const [label, value] = remainingRefundInfo[i];
                    doc.setFont('helvetica', 'bold');
                    doc.text(label + ':', 205, yPosition);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, 250, yPosition);
                }

                yPosition += 8;
            }

            // Row 3: Complete Description (Full Row)
            yPosition = 150;
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.setFillColor(240, 240, 240);
            doc.rect(20, yPosition - 5, 260, 6, 'F');
            doc.text('Description', 25, yPosition);

            // Description - Full page width with smaller text
            yPosition += 15;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');

            if (currentValueObj.description) {
                const description = currentValueObj.description || 'N/A';

                // Split description into words
                const words = description.split(' ');
                let line = '';
                let currentY = yPosition;
                const maxWidth = 170; // Full page width for landscape layout
                const lineHeight = 6; // Smaller line height for compact layout

                words.forEach(word => {
                    const testLine = line + word + ' ';
                    if (testLine.length > maxWidth) {
                        // Add current line to PDF
                        doc.text(line.trim(), 25, currentY);
                        line = word + ' ';
                        currentY += lineHeight;
                    } else {
                        line += word + ' ';
                    }
                });

                // Add the last line
                if (line.trim()) {
                    doc.text(line.trim(), 25, currentY);
                }
            }

            // Save PDF
            doc.save(`refund_details_${new Date().toISOString().slice(0, 10)}.pdf`);
        } catch (error) {
            console.error('PDF export error:', error);
            alert('Error exporting PDF file. Please try again.');
        }
    }




    document.addEventListener('DOMContentLoaded', function () {
        // Handle enter key on specific inputs
        document.addEventListener('keydown', function (event) {
            if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                // Check if it's the student enrollment input field
                if (event.target.id === 'txtStudentid') {
                    // Let the inline onkeypress handler work for student search
                    return;
                }
                // For other inputs (like message textarea), prevent default and send message
                event.preventDefault();
                sendMsgBtn();
            }
        });
    });

    // Input validation functions
    function validateRollNumber(rollNumber) {
        if (!rollNumber || rollNumber.trim() === '') {
            return true; // Allow empty for "all students" search
        }

        // Check if roll number contains only alphanumeric characters and hyphens
        const alphanumericRegex = /^[A-Za-z0-9-]+$/;
        if (!alphanumericRegex.test(rollNumber)) {
            showToast('error', 'Invalid Roll Number', 'Roll number should contain only letters, numbers, and hyphens.');
            return false;
        }

        // Check length (between 3 and 20 characters)
        if (rollNumber.length < 3 || rollNumber.length > 20) {
            showToast('error', 'Invalid Roll Number', 'Roll number should be between 3 and 20 characters.');
            return false;
        }

        return true;
    }

    function validateRollNumberInput(input) {
        const value = input.value;
        // Remove any non-alphanumeric characters except hyphens
        const cleanValue = value.replace(/[^A-Za-z0-9-]/g, '');
        if (value !== cleanValue) {
            input.value = cleanValue;
            showToast('warning', 'Invalid Characters', 'Only letters, numbers, and hyphens are allowed in roll number.');
        }
    }

    function validateRemarks(remarks) {
        if (!remarks || remarks.trim() === '') {
            showToast('error', 'Remarks Required', 'Please enter remarks before proceeding.');
            return false;
        }

        // Check for XSS attempts
        const xssPattern = /<script|javascript:|on\w+\s*=|data:|vbscript:/i;
        if (xssPattern.test(remarks)) {
            showToast('error', 'Invalid Content', 'Remarks contain invalid content. Please remove any scripts or special codes.');
            return false;
        }

        // Check character limit (max 1500 characters)
        if (remarks.length > 1500) {
            showToast('error', 'Text Too Long', 'Remarks should not exceed 1500 characters.');
            return false;
        }

        return true;
    }

    function sanitizeSearchInput(input) {
        // Remove potentially dangerous characters
        return input.replace(/[<>'"&]/g, '');
    }

    function sanitizeRemarksInput(input) {
        const value = input.value;
        // Remove potentially dangerous characters and scripts
        const cleanValue = value.replace(/[<>'"&]/g, '').replace(/<script|javascript:|on\w+\s*=|data:|vbscript:/gi, '');
        if (value !== cleanValue) {
            input.value = cleanValue;
            showToast('warning', 'Invalid Content', 'Some characters were removed for security.');
        }
    }

    function updateRemarksCounter(input) {
        const value = input.value;
        const counter = document.getElementById('remarks_counter');
        const currentLength = value.length;
        const maxLength = 1500;

        if (counter) {
            counter.textContent = `${currentLength}/${maxLength}`;

            // Change badge color based on character count
            if (currentLength > maxLength * 0.9) { // 90% of limit
                counter.className = 'badge badge-danger position-absolute';
            } else if (currentLength > maxLength * 0.7) { // 70% of limit
                counter.className = 'badge badge-warning position-absolute';
            } else {
                counter.className = 'badge badge-secondary position-absolute';
            }
        }

        // Also sanitize input
        sanitizeRemarksInput(input);
    }

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
        countLimit.textContent = `${inputValue.length}/${limit} characters`;
    }
</script>
@stop