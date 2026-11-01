<?php
$file_name = "app";
if (auth()->user()->cms_sidebar == 'new') {
    $file_name = "updated_app";
}
?>
@extends('layouts.' . $file_name)
@section('title')
{{-- @dd("123"); --}}
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

    /* Lazy Loading Styles */
    .lazy-load {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .lazy-load.loaded {
        opacity: 1;
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

        /* Table responsive improvements */
        .table-responsive {
            border: none;
        }

        .table td,
        .table th {
            padding: 0.5rem !important;
            font-size: 0.85rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Bootstrap 4 Outline Button Enhancements */
        .btn-outline-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        .btn-outline-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.2);
        }

        .btn:active {
            transform: translateY(0) !important;
        }

        /* Search Button Loading State */
        .btn-searching {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-searching .fa-search {
            animation: search-pulse 1.5s infinite;
        }

        @keyframes search-pulse {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0.5;
            }
        }

        /* Clean Form Control Styling */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Enhanced Table Header Styling */
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




        /* Mobile Responsive */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95% !important;
                width: 95% !important;
                margin: 10px auto;
            }

            .table-responsive {
                font-size: 12px;
            }

            .input-group-sm .form-control {
                font-size: 14px;
            }
        }

        /* Loading States */
        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .message-loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .message-loading i {
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

        /* Professional Table Styling */
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #495057;
            padding: 15px 12px;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            border: none;
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            font-size: 14px;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Professional Button Enhancement */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            border: none;
            text-transform: none;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .btn-outline-primary {
            border: 2px solid #007bff;
            color: #007bff;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: #007bff;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .btn-outline-warning {
            border: 2px solid #ffc107;
            color: #856404;
            background: transparent;
        }

        .btn-outline-warning:hover {
            background: #ffc107;
            color: #212529;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Professional Typography */
        .modal-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            font-size: 18px;
            letter-spacing: 0.5px;
            color: white;
        }

        .card-header h5 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #495057;
            letter-spacing: 0.3px;
        }

        .form-control {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            font-size: 14px;
            padding: 10px 12px;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        .custom-control-label {
            font-weight: 500;
            font-size: 14px;
            color: #495057;
            letter-spacing: 0.2px;
        }

        .badge {
            font-weight: 500;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 4px;
            letter-spacing: 0.3px;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 13px;
        }

        .small {
            font-size: 12px;
            font-weight: 500;
        }


        /* Blinking Orange Border Animation for Escalation Rows - High Priority */
        .escalation-border-blink {
            animation: border-blink 2s infinite !important;
            border-left: 4px solid #ff6b35 !important;
        }

        @keyframes border-blink {

            0%,
            100% {
                border-left: 4px solid #ff6b35 !important;
            }

            50% {
                border-left: 4px solid #ffb366 !important;
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

        @keyframes escalation-pulse {
            0% {
                border-left: 4px solid #ff6b35 !important;
                background-color: rgba(255, 107, 53, 0.05) !important;
            }

            100% {
                border-left: 6px solid #ff4500 !important;
                background-color: rgba(255, 107, 53, 0.15) !important;
            }
        }

        /* Enhanced Search Button Loading Effects */
        .btn-searching {
            position: relative;
            color: #fff !important;
            background: linear-gradient(45deg, #007bff, #0056b3, #007bff) !important;
            background-size: 200% 200% !important;
            animation: search-glow 1.5s ease-in-out infinite, gradient-shift 2s ease-in-out infinite !important;
            border: 2px solid #007bff !important;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.5) !important;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        @keyframes search-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
            }

            50% {
                box-shadow: 0 0 30px rgba(0, 123, 255, 0.8), 0 0 40px rgba(0, 123, 255, 0.3);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Search Icon Spinning Effect */
        .fa-search-spin {
            animation: search-spin 1s linear infinite;
        }

        @keyframes search-spin {
            0% {
                transform: rotate(0deg) scale(1);
            }

            25% {
                transform: rotate(90deg) scale(1.1);
            }

            50% {
                transform: rotate(180deg) scale(1);
            }

            75% {
                transform: rotate(270deg) scale(1.1);
            }

            100% {
                transform: rotate(360deg) scale(1);
            }
        }
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
       Figma UI override: E-Portal Requests
       Presentation/structure only. All original IDs, fields, Blade
       conditions, routes, AJAX calls and JavaScript remain unchanged.
       Visual language mirrors the e-Portal Approval Figma screens.
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
        background: #f2f6f9;
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

    .eportal-tabs-and-extra {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 0 0 24px;
    }

    .eportal-page .tabs-menu {
        padding: 0 !important;
        margin: 0 !important;
    }

    .eportal-page .panel-tabs {
        display: flex !important;
        align-items: center;
        gap: 8px;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        list-style: none !important;
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
        display: none !important;
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

    /* Keep generated title in DOM; Figma communicates the active state via tabs. */
    .eportal-page #result-body-categ > .mb-4 > h4 {
        display: none !important;
    }

    /* Dynamic filter row -> Figma white filter card. */
    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: flex-end !important;
        gap: 16px;
        width: 100%;
        margin: 0 0 24px !important;
        padding: 16px !important;
        border-radius: 12px;
        background: #fff !important;
        box-shadow: none !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"] {
        position: relative;
        width: auto;
        max-width: none;
        margin: 0 !important;
        padding: 20px 0 0 !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(1) {
        flex: 0 1 260px !important;
        min-width: 210px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(2) {
        flex: 0 1 240px !important;
        min-width: 200px;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(3) {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex: 1 1 420px !important;
        gap: 10px;
        min-width: 330px;
        margin-left: auto !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(1)::before,
    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(2)::before {
        position: absolute;
        top: 0;
        left: 0;
        color: var(--ep-muted);
        font-size: 12px;
        line-height: 14px;
        font-weight: 600;
        text-transform: uppercase;
        pointer-events: none;
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(1)::before {
        content: "CATEGORY";
    }

    .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(2)::before {
        content: "STUDENT ID";
    }

    .eportal-page #result-body-categ .form-control,
    .eportal-page #result-body-categ .form-control-sm {
        width: 100% !important;
        height: 42px !important;
        min-height: 42px !important;
        padding: 9px 12px !important;
        border: 1px solid var(--ep-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--ep-text) !important;
        font-family: inherit !important;
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

    .eportal-page #result-body-categ .btn {
        min-height: 42px;
        margin: 0 !important;
        padding: 9px 18px !important;
        border-radius: 8px !important;
        font-family: inherit !important;
        font-size: 13px !important;
        line-height: 20px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        text-transform: none !important;
    }

    .eportal-page #getCategButton {
        border: 1px solid var(--ep-primary) !important;
        background: var(--ep-primary) !important;
        color: #fff !important;
    }

    .eportal-page #getCategButton:hover,
    .eportal-page #getCategButton:focus {
        border-color: var(--ep-primary-dark) !important;
        background: var(--ep-primary-dark) !important;
        color: #fff !important;
    }

    /* Figma refresh is an icon-only square. Original onclick remains intact. */
    .eportal-page #result-body-categ button[onclick="refreshData()"] {
        width: 48px !important;
        min-width: 48px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        border: 1px solid var(--ep-border) !important;
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
        align-items: center !important;
        gap: 8px;
        margin: 0 !important;
    }

    .eportal-page #filter-all-btn {
        border: 1px solid var(--ep-border) !important;
        background: #fff !important;
        color: var(--ep-muted) !important;
    }

    .eportal-page #filter-all-btn.active {
        border-color: var(--ep-primary) !important;
        background: rgba(23, 103, 95, 0.06) !important;
        color: var(--ep-primary) !important;
    }

    .eportal-page #filter-escalation-btn,
    .eportal-page #filter-escalation-btn.active {
        border: 1px solid var(--ep-accent) !important;
        background: var(--ep-accent) !important;
        color: #fff !important;
    }

    /* Table card */
    .eportal-page #result-body-categ > .mb-4 > .row:not(.align-items-center) {
        width: 100%;
        margin: 0 !important;
    }

    .eportal-page #result-body-categ > .mb-4 > .row:not(.align-items-center) > [class*="col-"] {
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

    .eportal-page #result-body-categ .table-responsive {
        width: 100%;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 12px;
        background: #fff;
        overflow-x: auto;
        overflow-y: hidden;
        box-shadow: none !important;
    }

    .eportal-page #result-body-categ table.table {
        width: 100% !important;
        min-width: 1180px;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
        table-layout: auto;
    }

    .eportal-page #result-body-categ table.table thead th {
        height: 48px;
        padding: 13px 12px !important;
        border: 0 !important;
        background: var(--ep-primary) !important;
        color: #fff !important;
        font-family: inherit !important;
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
        background: transparent !important;
        color: #1e293b !important;
        font-family: inherit !important;
        font-size: 12px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
        vertical-align: middle !important;
    }

    .eportal-page #result-body-categ table.table tbody tr:nth-child(even) {
        background: var(--ep-row-alt) !important;
    }

    .eportal-page #result-body-categ table.table tbody tr:hover {
        background: rgba(23, 103, 95, 0.035) !important;
    }

    /* Status pills: visual override only; original status values/classes remain. */
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

    .eportal-page #result-body-categ .badge-outline-warning {
        border: 0 !important;
        border-radius: 99px !important;
        background: var(--ep-warning-bg) !important;
        color: var(--ep-warning) !important;
        padding: 5px 11px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
    }

    .eportal-page #result-body-categ .badge i {
        margin-right: 4px !important;
    }

    /* Figma actions are horizontal. Original click handlers remain unchanged. */
    .eportal-page #result-body-categ .btn-group-vertical {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 6px;
        width: auto !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn {
        width: auto !important;
        min-height: 30px !important;
        margin: 0 !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        background: transparent !important;
        font-size: 12px !important;
        line-height: 18px !important;
        font-weight: 500 !important;
        white-space: nowrap;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn-outline-success {
        border: 1px solid var(--ep-primary) !important;
        color: var(--ep-primary) !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn-outline-primary {
        border: 1px solid var(--ep-muted) !important;
        color: var(--ep-muted) !important;
    }

    .eportal-page #result-body-categ .btn-group-vertical .btn i {
        display: none !important;
    }

    /* Figma-like empty state without changing AJAX/table generation. */
    .eportal-page #result-body-categ .table-responsive:has(tbody:empty) {
        position: relative;
        min-height: 430px;
        overflow: hidden;
    }

    .eportal-page #result-body-categ .table-responsive:has(tbody:empty) table {
        min-height: 430px;
    }

    .eportal-page #result-body-categ .table-responsive:has(tbody:empty)::before {
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

    .eportal-page #result-body-categ .table-responsive:has(tbody:empty)::after {
        content: "No records found";
        position: absolute;
        z-index: 3;
        left: 50%;
        top: 250px;
        transform: translateX(-50%);
        color: var(--ep-muted);
        font-size: 16px;
        font-weight: 500;
        pointer-events: none;
    }

    /* Lazy-loading/pagination footer remains in its original DOM position. */
    .eportal-page #scroll-trigger,
    .eportal-page #result-body-categ .border-top.text-center {
        padding: 12px 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: var(--ep-muted) !important;
    }

    .eportal-page #load-more-content .btn {
        border: 1px solid var(--ep-primary) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--ep-primary) !important;
    }

    /* Loader/error states */
    .eportal-page #result-body-categ > .text-center,
    .eportal-page #result-body-categ > .p-4,
    .eportal-page #result-body-categ > .card {
        border: 0 !important;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    /* Existing modals stay fully intact; only visual palette/z-index changes. */
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

        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(1),
        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(2) {
            flex: 1 1 220px !important;
            max-width: none !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(3) {
            flex: 1 1 100% !important;
            justify-content: flex-start !important;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 767.98px) {
        .eportal-page {
            padding: 20px 14px 32px !important;
            border-radius: 0 !important;
        }

        .eportal-page-heading h3 {
            font-size: 19px !important;
        }

        .eportal-page-heading p {
            font-size: 15px;
        }

        .eportal-tabs-and-extra {
            margin-bottom: 18px;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 {
            gap: 12px;
            padding: 14px !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(1),
        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(2),
        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(3) {
            flex: 1 1 100% !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }

        .eportal-page #result-body-categ > .mb-4 > .row.align-items-center.mb-3 > [class*="col-"]:nth-child(3) {
            flex-wrap: wrap !important;
        }

        .eportal-page #result-body-categ table.table {
            min-width: 1060px;
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
            <h3>E-Portal Requests</h3>
            <p>Improve Performance</p>
        </div>

        <div class="eportal-tabs-and-extra">
            <div class="tabs-menu">
                <!-- Tabs -->
                <ul class="nav panel-tabs">
                    <li class=""><a href="#lectures" onclick="status('Pending')" id="setclasspending" data-toggle="tab">
                            <i class="fa fa-clock-o"></i> Pending <span id="pendingSpinner"
                                class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;"><span
                                    class="sr-only">Loading...</span></span>
                        </a></li>
                    <li><a href="#material" onclick="status('Completed')" id="setclasscompleted" data-toggle="tab">
                            <i class="fa fa-check-circle"></i> Completed <span id="completedSpinner"
                                class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;"><span
                                    class="sr-only">Loading...</span></span>
                        </a></li>
                </ul>
                <input type="hidden" id="taskStatus" value="Pending">
                <input type="hidden" id="studentStatus" value="noStudent">
            </div>

            <div class="eportal-hidden-category-wrap">
                <!-- Old category dropdown removed - now using inline dropdowns in search layouts -->
                <!-- Hidden category dropdown to maintain backend compatibility -->
                <select class="form-control" id="category_id" style="display: none;" name="category_id">
                    <option value="0">All Requests</option>
                    @foreach($resEpCateg as $user)
                        <option value="{{$user->id}}">{{$user->ec_title}}
                            {{-- <span style="color:red">({{$user->total_req}}) --}}
                            </span></option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Content area inside the main card; original AJAX target retained -->
        <div id="result-body-categ">
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="approval_status">
    <div class="modal-dialog modal-lg" role="document">
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
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <span id="stdname"></span><br>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
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

                    <div class="row" style="display:none" id="forwardedByRow">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Forwarded By:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="fwd_by_name"></span><br>
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
                    <div class="row" style="display:none" id="resRemarks">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Resolver Remarks:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="res_remarks"></span><br>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="showVcRemarks">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b id="vcRemarksLabel">VC Remarks:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="vc_remarks"></span><br>
                        </div>
                    </div>
                    <div class="row" tyle="display:none" id="check_form_div">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <label for="name"><b>Check Details:</b></label>
                        </div>
                        <div class="col-12 col-sm-9 col-md-9 col-lg-9">
                            <span id="checkForm"></span>
                            <span id="checkFormTracking"></span><br>
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
                <hr class="my-4">

                <!-- Message Form -->
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
@stop
@section('javascript')

<script type="text/javascript">
    var loggedInEmpId = "{{ auth()->user()->employee_id }}";
    $(document).ready(function () {
        // alert($('#category_id').val());
        // $("#category_id").val(selectedValue);
        getCateg('noStudent', $('#taskStatus').val());


        // Auto-refresh every 5 minutes to get new data
        var autoRefreshTimer = setInterval(function () {
            refreshData();
        }, 300000); // 5 minutes = 300000 milliseconds

        // Store timer reference for cleanup
        window.autoRefreshTimer = autoRefreshTimer;
    });


    // Refresh Data Function - Clear cache and get fresh data
    function refreshData() {
        // Set refresh flag to bypass cache
        window.forceRefresh = true;

        // Show loading with refresh message
        $('#result-body-categ').html('<div class="text-center p-4"><div class="modern-refresh-loader"><div class="refresh-icon"></div><div class="refresh-pulse"></div></div><br><br><h5>Refreshing data from database...</h5><small class="text-muted">Bypassing cache to get latest data</small></div>');

        // Call getCateg to fetch fresh data
        getCateg('noStudent', $('#taskStatus').val());

        // Reset refresh flag after request
        setTimeout(function () {
            window.forceRefresh = false;
        }, 1000);
    }



    // Function for Completed status with category filter
    function getCategWithCompletedFilter() {
        var studentRoll = $('#txtStudentid').val();
        var statusFilter = $('#completedStatusFilter').val();

        // Store current filter globally
        window.currentCompletedFilter = statusFilter;

        // Build the status parameter
        var taskStatus = 'Completed';
        if (statusFilter && statusFilter !== '') {
            taskStatus = statusFilter; // 'Completed' or 'Declined'
        }

        // Sync inline category with main category (if element exists)
        var inlineCategoryElement = $('#inlineCategory_id');
        var inlineCategoryValue = '';
        if (inlineCategoryElement.length > 0) {
            inlineCategoryValue = inlineCategoryElement.val();
            $('#category_id').val(inlineCategoryValue);
        } else {
            // If inline category doesn't exist, get value from main category
            inlineCategoryValue = $('#category_id option:selected').val();
        }

        console.log('Completed Filter Selected:', statusFilter);
        console.log('Task Status:', taskStatus);
        console.log('Student Roll:', studentRoll);
        console.log('Category Selected:', inlineCategoryValue || 'Not selected');

        // Call getCateg with appropriate parameters
        if (studentRoll && studentRoll.trim() !== '') {
            if (validateRollNumber(studentRoll)) {
                getCateg(studentRoll, taskStatus);
            }
        } else {
            getCateg('noStudent', taskStatus);
        }
    }

    // Function for Pending status with category filter
    function getCategPending() {
        var studentRoll = $('#txtStudentid').val();
        var taskStatus = 'Pending';

        // Sync inline category with main category (if element exists)
        var inlineCategoryElement = $('#inlineCategory_id');
        var inlineCategoryValue = '';
        if (inlineCategoryElement.length > 0) {
            inlineCategoryValue = inlineCategoryElement.val();
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

    // Export Functions (kept for future use)
    function exportCurrentData() {
        var exportModal = `
        <div class="modal fade" id="exportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-download"></i> Export Data</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Export Format:</label>
                            <select class="form-control" id="export_format">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                                <option value="pdf">PDF (.pdf)</option>
                                <option value="json">JSON (.json)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data Range:</label>
                            <select class="form-control" id="export_range">
                                <option value="current">Current Page Only</option>
                                <option value="filtered">All Filtered Results</option>
                                <option value="all">All Data (No Filters)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Include Columns:</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_name" checked>
                                        <label class="form-check-label">Student Name</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_program" checked>
                                        <label class="form-check-label">Program</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_request_type" checked>
                                        <label class="form-check-label">Request Type</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_request_time" checked>
                                        <label class="form-check-label">Request Time</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_escalation">
                                        <label class="form-check-label">Escalation Time</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_description">
                                        <label class="form-check-label">Description</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="processExport()">
                            <i class="fa fa-download"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remove existing modal and add new one
        $('#exportModal').remove();
        $('body').append(exportModal);
        $('#exportModal').modal('show');
    }

    function processExport() {
        var format = $('#export_format').val();
        var range = $('#export_range').val();

        // Get selected columns
        var selectedColumns = [];
        if ($('#col_name').is(':checked')) selectedColumns.push('name');
        if ($('#col_program').is(':checked')) selectedColumns.push('program');
        if ($('#col_request_type').is(':checked')) selectedColumns.push('request_type');
        if ($('#col_request_time').is(':checked')) selectedColumns.push('request_time');
        if ($('#col_escalation').is(':checked')) selectedColumns.push('escalation');
        if ($('#col_description').is(':checked')) selectedColumns.push('description');

        // Show loading in modal
        $('.modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Preparing export...</div>');

        // Process based on format
        switch (format) {
            case 'excel':
                exportToExcel(range, selectedColumns);
                break;
            case 'csv':
                exportToCSV(range, selectedColumns);
                break;
            case 'pdf':
                exportToPDF(range, selectedColumns);
                break;
            case 'json':
                exportToJSON(range, selectedColumns);
                break;
        }

        setTimeout(function () {
            $('#exportModal').modal('hide');
        }, 2000);
    }

    // Specific Export Functions
    function exportToExcel(range, columns) {
        var data = getExportData(range);
        var filteredData = filterDataByColumns(data, columns);

        // Create workbook and worksheet
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.json_to_sheet(filteredData);

        // Add styling
        var headerStyle = {
            fill: { fgColor: { rgb: "366092" } },
            font: { bold: true, color: { rgb: "FFFFFF" } }
        };

        // Apply header styling
        var range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const headerCell = XLSX.utils.encode_cell({ r: 0, c: C });
            if (!ws[headerCell]) continue;
            ws[headerCell].s = headerStyle;
        }
        // Set column widths
        ws['!cols'] = columns.map(() => ({ width: 20 }));
        XLSX.utils.book_append_sheet(wb, ws, "EPortal Requests");
        XLSX.writeFile(wb, `eportal_requests_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }
    function exportToCSV(range, columns) {
        var data = getExportData(range);
        var filteredData = filterDataByColumns(data, columns);

        // Convert to CSV
        var csv = '';
        if (filteredData.length > 0) {
            // Headers
            csv += Object.keys(filteredData[0]).join(',') + '\n';

            // Data rows
            filteredData.forEach(row => {
                csv += Object.values(row).map(field => `"${field}"`).join(',') + '\n';
            });
        }

        // Download
        var blob = new Blob([csv], { type: 'text/csv' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `eportal_requests_${new Date().toISOString().slice(0, 10)}.csv`;
        link.click();
    }

    function exportToPDF(range, columns) {
        var data = getExportData(range);
        var filteredData = filterDataByColumns(data, columns);

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        // Title
        doc.setFontSize(16);
        doc.text('EPortal Requests Report', 20, 20);

        // Date
        doc.setFontSize(10);
        doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 30);

        // Table
        var tableHeaders = Object.keys(filteredData[0] || {});
        var tableData = filteredData.map(row => Object.values(row));

        doc.autoTable({
            head: [tableHeaders],
            body: tableData,
            startY: 40,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [54, 96, 146] }
        });

        doc.save(`eportal_requests_${new Date().toISOString().slice(0, 10)}.pdf`);
    }

    function exportToJSON(range, columns) {
        var data = getExportData(range);
        var filteredData = filterDataByColumns(data, columns);

        var exportObj = {
            export_date: new Date().toISOString(),
            total_records: filteredData.length,
            data: filteredData
        };

        var blob = new Blob([JSON.stringify(exportObj, null, 2)], { type: 'application/json' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `eportal_requests_${new Date().toISOString().slice(0, 10)}.json`;
        link.click();
    }

    function getExportData(range) {
        var data = [];

        if (range === 'current') {
            // Get current page data from table
            $('tbody tr').each(function () {
                var row = {};
                row.sr_no = $(this).find('td:eq(0)').text();
                row.name = $(this).find('td:eq(1)').text();
                row.school = $(this).find('td:eq(2)').text();
                row.program = $(this).find('td:eq(3)').text();
                row.request_type = $(this).find('td:eq(4)').text();
                row.request_time = $(this).find('td:eq(5)').text();
                row.escalation = $(this).find('td:eq(6)').text();
                row.status = $(this).find('td:eq(7)').text();
                data.push(row);
            });
        } else {
            // For 'filtered' and 'all', make AJAX request to get full data
            // This would need backend support
            alert('Full data export requires backend implementation. Currently showing table data only.');
            return getExportData('current');
        }

        return data;
    }

    function filterDataByColumns(data, selectedColumns) {
        return data.map(row => {
            var filteredRow = {};
            selectedColumns.forEach(col => {
                switch (col) {
                    case 'name': filteredRow['Student Name'] = row.name; break;
                    case 'program': filteredRow['Program'] = row.program; break;
                    case 'request_type': filteredRow['Request Type'] = row.request_type; break;
                    case 'request_time': filteredRow['Request Time'] = row.request_time; break;
                    case 'escalation': filteredRow['Escalation Time'] = row.escalation; break;
                    case 'description': filteredRow['Description'] = row.description || 'N/A'; break;
                }
            });
            return filteredRow;
        });
    }
    // Countdown timer function for remarks
    function startCountdownTimer(targetDate, elementId) {
        const timer = setInterval(function () {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById(elementId).innerHTML = "<i class='fa fa-exclamation-circle' style='margin-right: 4px; font-size: 9px;'></i>Escalated";
                document.getElementById(elementId).parentElement.style.cssText = "display: inline-block; font-size: 10px; padding: 4px 8px; margin-top: 4px; background: linear-gradient(135deg, #ffe6e6 0%, #ffd9d9 100%); color: #cc0000; border-radius: 5px; border: 1px solid #ffb3b3; box-shadow: 0 1px 3px rgba(204, 0, 0, 0.15); font-weight: 500; letter-spacing: 0.3px;";
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
            url: "{{ route('eportal_request_complete') }}",
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

        // Reset category to "All Requests" when switching tabs
        $('#category_id').val(0);

        // Show loading state immediately
        $('#result-body-categ').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><br><h5>Loading ' + val + ' requests...</h5><small class="text-muted">Please wait while we fetch the data</small></div>');

        // Show the appropriate spinner based on status
        if (val == 'Pending') {
            $('#pendingSpinner').show();
            $('#completedSpinner').hide();
            getCateg('noStudent', 'Pending');
        } else if (val == 'Completed') {
            $('#completedSpinner').show();
            $('#pendingSpinner').hide();
            // Reset filter when clicking Completed tab
            currentCompletedFilter = '';
            getCateg('noStudent', 'Completed');
        }
    }
    function getCateg(sel, customTaskStatus) {
        var taskstatus = customTaskStatus || $('#taskStatus').val();

        // Add enhanced loading state to search button with glow and spinner
        $('#getCategButton').addClass('btn-searching').prop('disabled', true);
        $('#getCategButton').html('<i class="fa fa-search fa-search-spin"></i> Searching...');

        // Show search loading spinner (only if not from refresh function)
        if (!window.forceRefresh) {
            $('#result-body-categ').html('<div class="text-center p-4"><div class="modern-search-loader"><div class="search-glass"></div><div class="search-pulse"></div></div><br><br><h5>Searching data...</h5><small class="text-muted">Please wait while we search for records</small></div>');
        }

        if (taskstatus == 'Pending') {
            $("#setclasscompleted").removeClass("active");
            $("#setclasspending").addClass("active");
        } else {
            $("#setclasscompleted").addClass("active");
            $("#setclasspending").removeClass("active");
        }
        // Check if inline category exists and sync with main category
        var inlineCategoryElement = $('#inlineCategory_id');
        var inlineCategoryValue = '';
        if (inlineCategoryElement.length > 0) {
            inlineCategoryValue = inlineCategoryElement.val();
            $('#category_id').val(inlineCategoryValue);
        } else {
            // If inline category doesn't exist, get value from main category
            inlineCategoryValue = $('#category_id option:selected').val();
        }
        console.log('Inline Category Value:', inlineCategoryValue || 'Not selected');
        var ec_id = $('#category_id option:selected').val();
        var selectedText = $('#category_id option:selected').text();

        // Remove count from selectedText for all statuses (Pending and Completed)
        selectedText = selectedText.replace(/\s*<span[^>]*>\([^)]*\)<\/span>/g, '');

        // Also remove any remaining numbers in parentheses (like category IDs)
        selectedText = selectedText.replace(/\s*\(\d+\)\s*$/g, '');

        // Handle enrollment number: if sel is not 'noStudent', use it as enrollment number
        var rollno;
        if (sel && sel !== 'noStudent' && sel.trim() !== '') {
            rollno = sel; // sel contains the enrollment number
        } else {
            // Try to read from input field if it exists
            var roll = $('#txtStudentid').val();
            if (roll && roll.trim() !== '') {
                rollno = roll;
            } else {
                rollno = $('#studentStatus').val();
            }
        }

        // Allow all requests to load with lazy loading - removed filter restriction

        // Build URL with search parameters
        var searchUrl = "{{route('get_categ')}}?ec_id=" + ec_id + "&sel=" + rollno + "&taskStatus=" + taskstatus + '&student=' + rollno + '&page=1&per_page=100';

        // Add specific filter flag for Completed/Declined only
        if (taskstatus === 'Completed' && customTaskStatus === 'Completed') {
            searchUrl += '&specific_filter=completed_only';
        } else if (taskstatus === 'Declined') {
            searchUrl += '&specific_filter=declined_only';
        }


        // Always add timestamp to bypass cache
        searchUrl += '&force_refresh=1&timestamp=' + Date.now();

        $.ajax({
            url: searchUrl,
            method: 'get',
            cache: false, // Disable caching
            timeout: 30000, // 30 seconds timeout
            success: function (response) {
                // Hide both spinners after request completes
                $('#pendingSpinner').hide();
                $('#completedSpinner').hide();

                // Reset search button state
                $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');

                // Handle Laravel pagination response
                var data = response.data || [];
                var pagination = response.pagination || {};
                var totalRecords = pagination.total || 0;
                var hasMoreData = pagination.has_more_pages || false;
                var displayData = data; // All data from server (already paginated)


                // Store pagination data for lazy loading
                window.currentPagination = pagination;

                var sr_no = 1;
                var html = '';


                // Show appropriate title based on filter
                var displayTitle = selectedText + ' (';
                if (taskstatus == 'Declined') {
                    displayTitle += 'Declined Only';
                } else if (taskstatus == 'Completed' && currentCompletedFilter == 'Completed') {
                    displayTitle += 'Completed Only';
                } else {
                    displayTitle += taskstatus;
                }
                displayTitle += ')';

                // Create section header
                html += '<div class="mb-4">';
                html += '<h4 class="text-primary font-weight-bold mb-3">' + displayTitle + '</h4>';
                html += '<div class="row align-items-center mb-3">';
                if (taskstatus == 'Completed' || taskstatus == 'Declined') {
                    // For Completed status - show Category → Input → Status Filter → Search → Refresh
                    html += '<div class="col-4 col-sm-4 col-md-4 col-lg-4">';
                    html += '<select class="form-control" id="inlineCategory_id" onchange="getCategWithCompletedFilter()">';
                    if (ec_id == '0' || ec_id == 0) {
                        html += '<option value="0" selected>All Requests</option>';
                    } else {
                        html += '<option value="0">All Requests</option>';
                    }
                    // Add category options dynamically from main dropdown
                    $('#category_id option').each(function () {
                        if ($(this).val() != '0') {
                            var value = $(this).val();
                            var text = $(this).text();

                            // Remove count from text for Completed/Declined status
                            if (taskstatus == 'Completed' || taskstatus == 'Declined') {
                                text = text.replace(/\s*<span[^>]*>\([^)]*\)<\/span>/g, '');
                                // Also remove any remaining numbers in parentheses (like category IDs)
                                text = text.replace(/\s*\(\d+\)\s*$/g, '');
                            }

                            var selected = (value == ec_id) ? ' selected' : '';
                            html += '<option value="' + value + '"' + selected + '>' + text + '</option>';
                        }
                    });
                    html += '</select>';
                    html += '</div>';
                    html += '<div class="col-3 col-sm-3 col-md-3 col-lg-3">';
                    html += '<input placeholder="Student Roll No." class="form-control" type="text" id="txtStudentid" name="txtStudentid" maxlength="20" pattern="[A-Za-z0-9-]+" title="Only alphanumeric characters and hyphens allowed" onkeypress="if(event.key===\'Enter\') { var roll = $(this).val(); if(validateRollNumber(roll)) { getCateg(roll && roll.trim() !== \'\' ? roll : \'noStudent\', \'' + taskstatus + '\'); } }" oninput="validateRollNumberInput(this)">';
                    html += '</div>';
                    html += '<div class="col-3 col-sm-3 col-md-3 col-lg-3">';
                    html += '<button type="button" id="getCategButton" class="btn btn-outline-primary btn-sm mr-2" onclick="var roll = $(\'#txtStudentid\').val(); if(validateRollNumber(roll)) { getCateg(roll && roll.trim() !== \'\' ? roll : \'noStudent\', \'' + taskstatus + '\'); }">';
                    html += '<i class="fa fa-search mr-1" aria-hidden="true"></i> Search';
                    html += '</button>';
                    html += '<button type="button" class="btn btn-outline-warning btn-sm" onclick="refreshData()" title="Refresh Data - Get latest data from database">';
                    html += '<i class="fa fa-refresh mr-1" aria-hidden="true"></i> Refresh';
                    html += '</button>';
                    html += '</div>';
                } else {
                    // For Pending status - show Category → Input → Search → Refresh
                    html += '<div class="col-4 col-sm-4 col-md-4 col-lg-4">';
                    html += '<select class="form-control" id="inlineCategory_id" onchange="getCategPending()">';
                    if (ec_id == '0' || ec_id == 0) {
                        html += '<option value="0" selected>All Requests</option>';
                    } else {
                        html += '<option value="0">All Requests</option>';
                    }
                    // Add category options dynamically from main dropdown
                    $('#category_id option').each(function () {
                        if ($(this).val() != '0') {
                            var value = $(this).val();
                            var text = $(this).text();

                            // Remove count from text for Completed/Declined status
                            if (taskstatus == 'Completed' || taskstatus == 'Declined') {
                                text = text.replace(/\s*<span[^>]*>\([^)]*\)<\/span>/g, '');
                                // Also remove any remaining numbers in parentheses (like category IDs)
                                text = text.replace(/\s*\(\d+\)\s*$/g, '');
                            }

                            var selected = (value == ec_id) ? ' selected' : '';
                            html += '<option value="' + value + '"' + selected + '>' + text + '</option>';
                        }
                    });
                    html += '</select>';
                    html += '</div>';
                    html += '<div class="col-3 col-sm-3 col-md-3 col-lg-3">';
                    html += '<input placeholder="Student Roll No." class="form-control" type="text" id="txtStudentid" name="txtStudentid" maxlength="20" pattern="[A-Za-z0-9-]+" title="Only alphanumeric characters and hyphens allowed" onkeypress="if(event.key===\'Enter\') { var roll = $(this).val(); if(validateRollNumber(roll)) { getCateg(roll && roll.trim() !== \'\' ? roll : \'noStudent\', \'' + taskstatus + '\'); } }" oninput="validateRollNumberInput(this)">';
                    html += '</div>';
                    html += '<div class="col-5 col-sm-5 col-md-5 col-lg-5">';
                    html += '<button type="button" id="getCategButton" class="btn btn-outline-primary btn-sm mr-2" onclick="var roll = $(\'#txtStudentid\').val(); if(validateRollNumber(roll)) { getCateg(roll && roll.trim() !== \'\' ? roll : \'noStudent\', \'' + taskstatus + '\'); }">';
                    html += '<i class="fa fa-search mr-1" aria-hidden="true"></i> Search';
                    html += '</button>';
                    html += '<button type="button" class="btn btn-outline-warning btn-sm" onclick="refreshData()" title="Refresh Data - Get latest data from database">';
                    html += '<i class="fa fa-refresh mr-1" aria-hidden="true"></i> Refresh';
                    html += '</button>';

                    // Add escalation filter pill button group only for Pending status
                    if (taskstatus == 'Pending') {
                        html += '<div class="btn-group ml-2" role="group">';
                        html += '<button type="button" class="btn btn-outline-primary active btn-sm" id="filter-all-btn" onclick="setFilterMode(\'all\')"><i class="fa fa-list mr-1"></i>All</button>';
                        html += '<button type="button" class="btn btn-outline-danger btn-sm" id="filter-escalation-btn" onclick="setFilterMode(\'escalation\')"><i class="fa fa-exclamation-triangle mr-1"></i>Escalation Only</button>';
                        html += '</div>';
                    }

                    html += '</div>';
                }
                html += '</div>'; // close controls row
                html += '</div>'; // close controls section

                html += '<div class="row">';
                html += '<div class="col-12 col-sm-12 col-md-12 col-lg-12">';
                html += '<div class="table-responsive">';
                html += '<table class="table table-bordered table-striped table-hover">';
                html += '<thead class="thead-light">';
                html += '<tr>';
                html += '<th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Sr. No.</th>';
                html += '<th scope="col" class="font-weight-bold" style="font-size: 14px;">First Name</th>';
                html += '<th scope="col" class="d-none d-md-table-cell font-weight-bold" style="font-size: 14px;">School</th>';
                html += '<th scope="col" class="d-none d-lg-table-cell font-weight-bold" style="font-size: 14px;">Program</th>';
                html += '<th scope="col" class="font-weight-bold" style="font-size: 14px;">Request Type</th>';
                html += '<th scope="col" class="d-none d-sm-table-cell font-weight-bold" style="font-size: 14px;">Request Time</th>';
                html += '<th scope="col" class="d-none d-md-table-cell font-weight-bold" style="font-size: 14px;">Escalation Time</th>';
                html += '<th scope="col" class="text-center font-weight-bold" style="font-size: 14px; width: 140px; min-width: 140px;">Status</th>';
                html += '<th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Actions</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                // Initialize countdown timers array
                window.countdownTimers = [];

                coordinatorArray = ['21', '41', '42', '61', '81', '244', '344', '444', '464', '465', '524', '784', '944'];
                $.each(displayData, function (key, value) {
                    userGroupId = '<?= auth()->user()->group_id ?>';
                    isCoordinator = coordinatorArray.includes(userGroupId);
                    userDeptId = '<?= auth()->user()->dept_id ?>';
                    userSchoolId = '<?= auth()->user()->school_id ?>';
                    if (userDeptId == 161) {
                        userDeptId = 203;
                    }

                    if (isCoordinator) {
                        //alert(userDeptId+'==='+ value.dept_id);
                        // if (userDeptId == value.dept_id) {
                        if (userSchoolId == value.school_id) {
                            //alert(userDeptId+'==='+ value.dept_id);
                            var esclationTime = moment(value.esclation_time, 'YYYY-MM-DD HH:mm:ss Z');
                            var currentTime = moment();

                            // Calculate the time difference in hours
                            var hoursDifference = esclationTime.diff(currentTime, 'hours');
                            var color = ''; // Initialize color variable for escalation highlighting

                            // Check if the difference is within 12 hours and greater than 11 hours

                            // if (hoursDifference >= 11 && hoursDifference < 12) {
                            if (hoursDifference < 12) {
                                // Trigger an alert 12 hours before esclation_time
                                // alert('12 hours remaining until esclation_time for ' + value.full_name);
                                if (taskstatus == 'Pending') {
                                    color = 'orange'; // Set orange color for escalating requests
                                    console.log('🔥 ESCALATION TRIGGERED for:', value.full_name, 'Hours difference:', hoursDifference);
                                }
                            }
                            var valueJson = JSON.stringify(value);
                            var badgeColor;
                            var escalationChek = value.esclation_time;
                            // alert(escalationChek+'---'+value.chkdeclined);
                            if (value.chkdeclined > 0) {
                                escalationChek = 'Request Declined by one of the Approver';

                            }
                            else if (escalationChek == null && value.chkdeclined == 0) {
                                escalationChek = 'Request was declined and then rolled back';
                            }
                            if (value.er_request_status === 'Declined') {
                                badgeColor = 'red';
                            } else if (value.er_request_status === 'Pending') {
                                badgeColor = '#007bff';
                            } else if (value.er_request_status === 'Forward') {
                                badgeColor = '#ecb403';
                            } else {
                                badgeColor = 'green';
                            }
                            var msg = '';
                            var width = '';
                            if (value.er_request_status == 'Forward') {
                                var msg = 'Forwarded From VC';
                                var width = '156px';
                            } else {
                                var msg = value.er_request_status;
                            }
                            // Properly escape the JSON for HTML data attribute
                            var escapedValueJson = valueJson.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '&#10;').replace(/\r/g, '&#13;');
                            // console.log(value)

                            // Apply modern escalation styling with blinking border
                            var rowStyle = '';
                            var rowClass = '';
                            if (color === 'orange') {
                                rowStyle = 'background-color: rgba(255,107,53,0.08);';
                                rowClass = 'escalation-border-blink';
                            }
                            html += '<tr style="' + rowStyle + '" class="lazy-load ' + rowClass + '" data-escalation="' + color + '">';
                            html += '<td scope="col" class="text-center">' + (sr_no++) + '</td>';
                            if (value.parent_req_id != 102) {

                                // html += '<td scope="col">'+value.full_name+' ('+value.username+')</td>';
                                html += '<td scope="col">' + (



                                    (value.username !== null) ?
                                        value.full_name + ' (' + value.username + ')' :
                                        value.full_name
                                ) + '</td>';
                                html += '<td scope="col" class="d-none d-md-table-cell">' + value.school + '</td>';
                                html += '<td scope="col" class="d-none d-lg-table-cell">' + (value.programs ? value.programs : 'N/A') + '</td>';
                            }

                            else {
                                html += '<input type="hidden" name="checkDrugStatus" id="checkDrugStatus" value=' + value.ec_id + '>';
                                html += '<td scope="col">' + 'Anonymous' + '</td>';
                                html += '<td scope="col" class="d-none d-md-table-cell">' + 'Anonymous' + '</td>';
                                html += '<td scope="col" class="d-none d-lg-table-cell">' + 'Anonymous' + '</td>';
                            }
                            // alert(value.erdatetime+'---'+value.reinitiate_time);
                            let reinitiate_time = '';
                            if (value.reinitiate_time && value.erdatetime !== value.reinitiate_time) {
                                reinitiate_time = `
                                            <span class="badge badge-outline-primary"
                                                style="
                                                    border: 2px solid #007bff;
                                                    color: #007bff;
                                                    background-color: transparent;
                                                    font-size: 11px;
                                                    padding: 0.5em 0.8em;
                                                    white-space: nowrap;
                                                ">
                                                <i class="fa fa-repeat" style="margin-right: 6px;"></i>
                                                Reinitiated at<br>${value.reinitiate_time}
                                            </span>
                                        `;
                            }
                            html += '<td scope="col">' + value.ec_title + '</td>';
                            html += `
                            <td scope="col" class="d-none d-sm-table-cell">
                                ${value.erdatetime}
                                ${reinitiate_time ? '<br>' + reinitiate_time : ''}
                            </td>
                        `;

                            // Always show escalation time value
                            html += '<td scope="col" class="d-none d-md-table-cell">';
                            html += escalationChek;

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
                                    var countdownId = 'countdown_' + value.er_id;
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

                            // Create Bootstrap 4 outline badge with icon + text
                            var statusIcon = '';
                            var badgeClass = '';
                            var textColor = '';
                            if (value.er_request_status === 'Declined') {
                                statusIcon = 'fa-times';
                                badgeClass = 'badge-outline-danger';
                                textColor = '#dc3545';
                            } else if (value.er_request_status === 'Pending') {
                                statusIcon = 'fa-clock-o';
                                badgeClass = 'badge-outline-primary';
                                textColor = '#007bff';
                            } else if (value.er_request_status === 'Forward') {
                                statusIcon = 'fa-arrow-right';
                                badgeClass = 'badge-outline-warning';
                                textColor = '#ffc107';
                            } else {
                                statusIcon = 'fa-check';
                                badgeClass = 'badge-outline-success';
                                textColor = '#28a745';
                            }

                            html += '<td scope="col" class="text-center">';
                            html += '<span class="badge ' + badgeClass + '" style="border: 2px solid ' + textColor + '; color: ' + textColor + '; background-color: transparent; font-size: 12px; padding: 0.5em 0.8em; white-space: nowrap;">';
                            html += '<i class="fa ' + statusIcon + '" style="margin-right: 6px;"></i>' + msg;
                            html += '</span>';
                            html += '</td>';
                            // html += '<td><button class="btn btn-info" onclick="openModalStatus('+value.er_id+')" type="button">Application Tracking</button></td>';
                            // html += '<td><button type="button" class="btn btn-success" onclick="openModalStatus('+value.er_id+')" data-toggle="modal" data-target="#application_tracking">Tracking</button></td>';
                            html += '<td class="text-center">';
                            html += '<div class="btn-group-vertical" role="group">';
                            if (value.ec_id != 84) {
                                html += '<button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ', \'' + value.ec_title + '\', \'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus"></i> Tracking</button>';
                            }
                            html += '<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                            html += '</div>';
                            html += '</td>';

                            html += '</tr>';
                        }
                    } else {
                        var esclationTime = moment(value.esclation_time, 'YYYY-MM-DD HH:mm:ss Z');
                        var currentTime = moment();

                        // Calculate the time difference in hours
                        var hoursDifference = esclationTime.diff(currentTime, 'hours');
                        var color = ''; // Initialize color variable for escalation highlighting

                        // Check if the difference is within 12 hours and greater than 11 hours

                        // if (hoursDifference >= 11 && hoursDifference < 12) {
                        if (hoursDifference < 12) {
                            // Trigger an alert 12 hours before esclation_time
                            // alert('12 hours remaining until esclation_time for ' + value.full_name);
                            if (taskstatus == 'Pending') {
                                color = 'orange'; // Set orange color for escalating requests
                                console.log('🔥 ESCALATION TRIGGERED for:', value.full_name, 'Hours difference:', hoursDifference);
                            }
                        }
                        var valueJson = JSON.stringify(value);
                        var badgeColor;
                        var escalationChek = value.esclation_time;
                        // alert(escalationChek+'---'+value.chkdeclined);
                        if (value.chkdeclined > 0) {
                            escalationChek = 'Request Declined by one of the Approver';

                        }
                        else if (escalationChek == null && value.chkdeclined == 0) {
                            escalationChek = 'Request was declined and then rolled back';
                        }
                        if (value.er_request_status === 'Declined') {
                            badgeColor = 'red';
                        } else if (value.er_request_status === 'Pending') {
                            badgeColor = '#007bff';
                        } else if (value.er_request_status === 'Forward') {
                            badgeColor = '#ecb403';
                        } else {
                            badgeColor = 'green';
                        }
                        var msg = '';
                        var width = '';
                        if (value.er_request_status == 'Forward') {
                            var msg = 'Forwarded From VC';
                            var width = '156px';
                        } else {
                            var msg = value.er_request_status;
                        }
                        // Properly escape the JSON for HTML data attribute
                        var escapedValueJson = valueJson.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '&#10;').replace(/\r/g, '&#13;');
                        // console.log(value)

                        // Apply modern escalation styling with blinking border
                        var rowStyle = '';
                        var rowClass = '';
                        if (color === 'orange') {
                            rowStyle = 'background-color: rgba(255,107,53,0.08);';
                            rowClass = 'escalation-border-blink';
                        }
                        html += '<tr style="' + rowStyle + '" class="lazy-load ' + rowClass + '" data-escalation="' + color + '">';
                        html += '<td scope="col" class="text-center">' + (sr_no++) + '</td>';
                        if (value.parent_req_id != 102) {

                            // html += '<td scope="col">'+value.full_name+' ('+value.username+')</td>';
                            html += '<td scope="col">' + (
                                (value.username !== null) ?
                                    value.full_name + ' (' + value.username + ')' :
                                    value.full_name
                            ) + '</td>';
                            html += '<td scope="col">' + value.school + '</td>';
                            html += '<td scope="col">' + (value.programs ? value.programs : 'N/A') + '</td>';
                        }

                        else {
                            html += '<input type="hidden" name="checkDrugStatus" id="checkDrugStatus" value=' + value.ec_id + '>';
                            html += '<td scope="col">' + 'Anonymous' + '</td>';
                            html += '<td scope="col" class="d-none d-md-table-cell">' + 'Anonymous' + '</td>';
                            html += '<td scope="col" class="d-none d-lg-table-cell">' + 'Anonymous' + '</td>';
                        }
                        // alert(value.erdatetime+'---'+value.reinitiate_time);
                        let reinitiate_time = '';
                        if (value.reinitiate_time && value.erdatetime !== value.reinitiate_time) {
                            reinitiate_time = `
                                            <span class="badge badge-outline-primary"
                                                style="
                                                    border: 2px solid #007bff;
                                                    color: #007bff;
                                                    background-color: transparent;
                                                    font-size: 11px;
                                                    padding: 0.5em 0.8em;
                                                    white-space: nowrap;
                                                ">
                                                <i class="fa fa-repeat" style="margin-right: 6px;"></i>
                                                Reinitiated at<br>${value.reinitiate_time}
                                            </span>
                                        `;
                        }
                        html += '<td scope="col">' + value.ec_title + '</td>';
                        html += `
                            <td scope="col" class="d-none d-sm-table-cell">
                                ${value.erdatetime}
                                ${reinitiate_time ? '<br>' + reinitiate_time : ''}
                            </td>
                        `;

                        // Always show escalation time value
                        html += '<td scope="col" class="d-none d-md-table-cell">';
                        html += escalationChek;

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
                                var countdownId = 'countdown_' + value.er_id;
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

                        // Create Bootstrap 4 outline badge with icon + text
                        var statusIcon = '';
                        var badgeClass = '';
                        var textColor = '';
                        if (value.er_request_status === 'Declined') {
                            statusIcon = 'fa-times';
                            badgeClass = 'badge-outline-danger';
                            textColor = '#dc3545';
                        } else if (value.er_request_status === 'Pending') {
                            statusIcon = 'fa-clock-o';
                            badgeClass = 'badge-outline-primary';
                            textColor = '#007bff';
                        } else if (value.er_request_status === 'Forward') {
                            statusIcon = 'fa-arrow-right';
                            badgeClass = 'badge-outline-warning';
                            textColor = '#ffc107';
                        } else {
                            statusIcon = 'fa-check';
                            badgeClass = 'badge-outline-success';
                            textColor = '#28a745';
                        }

                        html += '<td scope="col" class="text-center">';
                        html += '<span class="badge ' + badgeClass + '" style="border: 2px solid ' + textColor + '; color: ' + textColor + '; background-color: transparent; font-size: 12px; padding: 0.5em 0.8em; white-space: nowrap;">';
                        html += '<i class="fa ' + statusIcon + '" style="margin-right: 6px;"></i>' + msg;
                        html += '</span>';
                        html += '</td>';
                        // html += '<td><button class="btn btn-info" onclick="openModalStatus('+value.er_id+')" type="button">Application Tracking</button></td>';
                        // html += '<td><button type="button" class="btn btn-success" onclick="openModalStatus('+value.er_id+')" data-toggle="modal" data-target="#application_tracking">Tracking</button></td>';
                        html += '<td class="text-center">';
                        html += '<div class="btn-group-vertical" role="group">';
                        if (value.ec_id != 84) {
                            html += '<button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ', \'' + value.ec_title + '\', \'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus"></i> Tracking</button>';
                        }
                        html += '<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                        html += '</div>';
                        html += '</td>';

                        html += '</tr>';
                    }

                });
                html += '</tbody>';
                html += '</table>';
                html += '</div>'; // close table-responsive

                // Add scroll loading section if there's more data
                if (hasMoreData) {
                    var currentShowing = pagination.to || displayData.length;
                    html += '<div class="text-center p-3 border-top" id="scroll-trigger">';
                    html += '<p class="text-muted mb-2">Showing ' + currentShowing + ' of ' + totalRecords + ' records</p>';
                    html += '<div id="load-more-content">';
                    html += '<button class="btn btn-outline-primary btn-sm" onclick="loadMoreDataLaravel()">';
                    html += '<i class="fa fa-plus-circle"></i> Load More (' + (totalRecords - currentShowing) + ' remaining)';
                    html += '</button>';
                    html += '<br><small class="text-muted mt-2 d-block">Or scroll down to load automatically</small>';
                    html += '</div>';
                    html += '<div id="scroll-loading" style="display:none;">';
                    html += '<i class="fa fa-spinner fa-spin"></i> Loading more records...';
                    html += '</div>';
                    html += '</div>';
                } else if (totalRecords > 0) {
                    html += '<div class="text-center p-2 border-top">';
                    html += '<small class="text-muted">Showing all ' + totalRecords + ' records</small>';
                    html += '</div>';
                }

                html += '</div>'; // close col-12
                html += '</div>'; // close row
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

                    // Sync inline category dropdown with main category after HTML is loaded
                    setTimeout(function () {
                        var mainCategoryValue = $('#category_id option:selected').val();
                        var inlineCategoryElement = $('#inlineCategory_id');
                        if (inlineCategoryElement.length > 0 && mainCategoryValue) {
                            inlineCategoryElement.val(mainCategoryValue);
                            console.log('Synced inline category dropdown to:', mainCategoryValue);
                        }
                    }, 50);

                    // Apply lazy loading to table rows
                    setTimeout(function () {
                        $('.lazy-load').addClass('loaded');
                    }, 100);

                    // Reset search button state on success
                    $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                    $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');

                    // Apply blinking to escalation rows
                    setTimeout(function () {
                        var escalationRows = $('tr.escalation-border-blink');

                        if (escalationRows.length > 0) {
                            // Apply JavaScript blinking to escalation rows
                            escalationRows.each(function (index) {
                                var $row = $(this);

                                // Mark as processed to avoid duplicate intervals
                                if ($row.attr('data-blink-applied')) {
                                    return;
                                }
                                $row.attr('data-blink-applied', 'true');

                                // Add stronger CSS styles directly
                                $row.css({
                                    'border-left': '4px solid #ff6b35',
                                    'animation': 'border-blink 2s infinite',
                                    'background-color': 'rgba(255,107,53,0.08)'
                                });

                                // Add smooth transition for blinking effect
                                $row.css({
                                    'transition': 'all 0.8s ease-in-out',
                                    '-webkit-transition': 'all 0.8s ease-in-out',
                                    '-moz-transition': 'all 0.8s ease-in-out'
                                });

                                // Smooth JavaScript blinking with slower timing
                                var blinkCount = 0;
                                var blinkInterval = setInterval(function () {
                                    blinkCount++;
                                    if (blinkCount % 2 === 0) {
                                        // Light orange - smooth transition
                                        $row.css({
                                            'border-left': '5px solid #ffb366',
                                            'background-color': 'rgba(255,182,102,0.12)',
                                            'box-shadow': '0 0 8px rgba(255,182,102,0.3)'
                                        });
                                    } else {
                                        // Dark orange - smooth transition
                                        $row.css({
                                            'border-left': '5px solid #ff6b35',
                                            'background-color': 'rgba(255,107,53,0.18)',
                                            'box-shadow': '0 0 12px rgba(255,107,53,0.4)'
                                        });
                                    }
                                }, 1800); // Slower 1.8 second cycle

                                // Store interval for cleanup if needed
                                $row.data('blinkInterval', blinkInterval);
                            });
                        }
                    }, 500);

                    // Store pagination data for Laravel-based loading
                    window.paginationData = {
                        current_page: pagination.current_page || 1,
                        total: totalRecords,
                        per_page: pagination.per_page || 100,
                        has_more_pages: hasMoreData,
                        current_showing: pagination.to || displayData.length,
                        filters: {
                            ec_id: ec_id,
                            sel: sel,
                            taskStatus: taskstatus,
                            student: rollno
                        }
                    };
                    window.isLoadingMore = false;

                    // Setup scroll-based lazy loading for Laravel pagination
                    setupScrollLazyLoadingLaravel();
                } else {
                    $('#result-body-categ').html('No Result Found!');

                    // Reset search button state when no results
                    $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                    $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');
                }

                // Restore scroll position if saved (after completion/action)
                if (window.savedScrollPosition !== undefined && window.savedScrollPosition > 0) {
                    setTimeout(function () {
                        $(window).scrollTop(window.savedScrollPosition);
                        // Clear the saved position
                        window.savedScrollPosition = undefined;
                    }, 300);
                }
            },
            error: function (xhr, status, error) {
                // Hide both spinners on error
                $('#pendingSpinner').hide();
                $('#completedSpinner').hide();

                // Reset search button state on error
                $('#getCategButton').removeClass('btn-searching').prop('disabled', false);
                $('#getCategButton').html('<i class="fa fa-search" aria-hidden="true"></i> Search');

                var errorMessage = '';
                var retryButton = '<button class="btn btn-primary btn-sm mt-3" onclick="getCateg(\'noStudent\', \'' + sel + '\')"><i class="fa fa-refresh"></i> Retry</button>';

                if (status === 'timeout') {
                    errorMessage = `
                    <div class="card e-panel" style="margin:10px;width:98%;">
                        <div class="card-body text-center p-5">
                            <i class="fa fa-clock-o fa-3x text-warning mb-3"></i>
                            <h4 class="text-warning">Request Timeout</h4>
                            <p class="text-muted">The request is taking too long. This might be due to:</p>
                            <ul class="list-unstyled">
                                <li><i class="fa fa-exclamation-triangle text-warning"></i> Large amount of data</li>
                                <li><i class="fa fa-exclamation-triangle text-warning"></i> Server is busy</li>
                                <li><i class="fa fa-exclamation-triangle text-warning"></i> Network connectivity issues</li>
                            </ul>
                            <p><strong>Suggestion:</strong> Try selecting a specific category to reduce data load.</p>
                            ${retryButton}
                        </div>
                    </div>`;
                } else if (status === 'error') {
                    var statusCode = xhr.status;
                    if (statusCode === 500) {
                        errorMessage = `
                        <div class="card e-panel" style="margin:10px;width:98%;">
                            <div class="card-body text-center p-5">
                                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Server Error</h4>
                                <p class="text-muted">There's a problem with the server. Please try again in a moment.</p>
                                ${retryButton}
                            </div>
                        </div>`;
                    } else if (statusCode === 0) {
                        errorMessage = `
                        <div class="card e-panel" style="margin:10px;width:98%;">
                            <div class="card-body text-center p-5">
                                <i class="fa fa-wifi fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Connection Problem</h4>
                                <p class="text-muted">Unable to connect to server. Please check your internet connection.</p>
                                ${retryButton}
                            </div>
                        </div>`;
                    } else {
                        errorMessage = `
                        <div class="card e-panel" style="margin:10px;width:98%;">
                            <div class="card-body text-center p-5">
                                <i class="fa fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                <h4 class="text-danger">Error Loading Data</h4>
                                <p class="text-muted">Error ${statusCode}: ${error}</p>
                                ${retryButton}
                            </div>
                        </div>`;
                    }
                } else {
                    errorMessage = `
                    <div class="card e-panel" style="margin:10px;width:98%;">
                        <div class="card-body text-center p-5">
                            <i class="fa fa-exclamation-circle fa-3x text-danger mb-3"></i>
                            <h4 class="text-danger">Something went wrong</h4>
                            <p class="text-muted">Please try again or contact support if the problem persists.</p>
                            ${retryButton}
                        </div>
                    </div>`;
                }

                $('#result-body-categ').html(errorMessage);
                console.error('AJAX Error:', status, error, xhr.responseText);
            }
        });
    }

    // Timer cleanup function to prevent memory leaks
    function cleanupAllTimers() {
        // Clear auto refresh timer
        if (window.autoRefreshTimer) {
            clearInterval(window.autoRefreshTimer);
            window.autoRefreshTimer = null;
        }

        // Clear all blink intervals
        $('[data-blink-interval]').each(function () {
            var interval = $(this).data('blink-interval');
            if (interval) {
                clearInterval(interval);
                $(this).removeData('blink-interval');
            }
        });

        // Clear any remaining intervals
        for (var i = 1; i < 99999; i++) {
            clearInterval(i);
            clearTimeout(i);
        }
    }

    // Cleanup timers when page unloads
    $(window).on('beforeunload', function () {
        cleanupAllTimers();
    });

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
            url: "{{route('get_categ')}}?ec_id=" + filters.ec_id + "&sel=" + filters.sel + "&taskStatus=" + filters.taskStatus + "&student=" + filters.student + "&page=" + nextPage + "&per_page=" + window.paginationData.per_page + "&timestamp=" + Date.now(),
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

                coordinatorArray = ['21', '41', '42', '61', '81', '244', '344', '444', '464', '465', '524', '784', '944'];
                $.each(data, function (key, value) {
                    userGroupId = '<?= auth()->user()->group_id ?>';
                    isCoordinator = coordinatorArray.includes(userGroupId);
                    userDeptId = '<?= auth()->user()->dept_id ?>';
                    if (userDeptId == 161) userDeptId = 203;

                    // Same logic as main table - handle both coordinator and non-coordinator
                    var shouldShowRecord = false;
                    if (isCoordinator) {
                        if (userDeptId == value.dept_id) {
                            shouldShowRecord = true;
                        }
                    } else {
                        shouldShowRecord = true; // Non-coordinators see all records
                    }

                    if (shouldShowRecord) {
                        // Add escalation logic for load more data
                        var color = '';
                        if (isCoordinator && userDeptId == value.dept_id) {
                            var esclationTime = moment(value.esclation_time, 'YYYY-MM-DD HH:mm:ss Z');
                            var currentTime = moment();
                            var hoursDifference = esclationTime.diff(currentTime, 'hours');

                            if (hoursDifference < 12) {
                                if ($('#taskStatus').val() == 'Pending') {
                                    color = 'orange';
                                }
                            }
                        }

                        var valueJson = JSON.stringify(value);
                        var badgeColor, escalationChek = value.esclation_time;

                        if (value.chkdeclined > 0) {
                            escalationChek = 'Request Declined by one of the Approver';
                        } else if (escalationChek == null && value.chkdeclined == 0) {
                            escalationChek = 'Request was declined and then rolled back';
                        }

                        if (value.er_request_status === 'Declined') {
                            badgeColor = 'red';
                        } else if (value.er_request_status === 'Pending') {
                            badgeColor = '#007bff';
                        } else if (value.er_request_status === 'Forward') {
                            badgeColor = '#ecb403';
                        } else {
                            badgeColor = 'green';
                        }

                        var msg = '', width = '';
                        if (value.er_request_status == 'Forward') {
                            msg = 'Forwarded From VC';
                            width = '156px';
                        } else {
                            msg = value.er_request_status;
                        }

                        var escapedValueJson = valueJson.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '&#10;').replace(/\r/g, '&#13;');

                        // Apply modern escalation styling with blinking border for load more
                        var rowStyle = '';
                        var rowClass = '';
                        if (color === 'orange') {
                            rowStyle = 'background-color: rgba(255,107,53,0.08);';
                            rowClass = 'escalation-border-blink';
                        }

                        additionalHTML += '<tr style="' + rowStyle + '" class="lazy-load ' + rowClass + '">';
                        additionalHTML += '<td scope="col" class="text-center">';

                        additionalHTML += (sr_no++) + '</td>';

                        if (value.parent_req_id != 102) {
                            additionalHTML += '<td scope="col">' + (value.username ? value.full_name + ' (' + value.username + ')' : value.full_name) + '</td>';
                            additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + value.school + '</td>';
                            additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">' + (value.programs ? value.programs : 'N/A') + '</td>';
                        } else {
                            additionalHTML += '<td scope="col">Anonymous</td>';
                            additionalHTML += '<td scope="col" class="d-none d-md-table-cell">Anonymous</td>';
                            additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">Anonymous</td>';
                        }

                        let reinitiate_time = '';
                        if (value.reinitiate_time && value.erdatetime !== value.reinitiate_time) {
                            reinitiate_time = `
                                            <span class="badge badge-outline-primary"
                                                style="
                                                    border: 2px solid #007bff;
                                                    color: #007bff;
                                                    background-color: transparent;
                                                    font-size: 11px;
                                                    padding: 0.5em 0.8em;
                                                    white-space: nowrap;
                                                ">
                                                <i class="fa fa-repeat" style="margin-right: 6px;"></i>
                                                Reinitiated at<br>${value.reinitiate_time}
                                            </span>
                                        `;
                        }
                        additionalHTML += '<td scope="col">' + value.ec_title + '</td>';
                        additionalHTML += `
                            <td scope="col" class="d-none d-sm-table-cell">
                                ${value.erdatetime}
                                ${reinitiate_time ? '<br>' + reinitiate_time : ''}
                            </td>
                        `;
                        additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + escalationChek + '</td>';
                        // Create Bootstrap 4 outline badge for load more data
                        var statusIcon = '';
                        var badgeClass = '';
                        var textColor = '';
                        if (value.er_request_status === 'Declined') {
                            statusIcon = 'fa-times';
                            badgeClass = 'badge-outline-danger';
                            textColor = '#dc3545';
                        } else if (value.er_request_status === 'Pending') {
                            statusIcon = 'fa-clock-o';
                            badgeClass = 'badge-outline-primary';
                            textColor = '#007bff';
                        } else if (value.er_request_status === 'Forward') {
                            statusIcon = 'fa-arrow-right';
                            badgeClass = 'badge-outline-warning';
                            textColor = '#ffc107';
                        } else {
                            statusIcon = 'fa-check';
                            badgeClass = 'badge-outline-success';
                            textColor = '#28a745';
                        }

                        additionalHTML += '<td scope="col" class="text-center">';
                        additionalHTML += '<span class="badge ' + badgeClass + '" style="border: 2px solid ' + textColor + '; color: ' + textColor + '; background-color: transparent; font-size: 12px; padding: 0.5em 0.8em; white-space: nowrap;">';
                        additionalHTML += '<i class="fa ' + statusIcon + '" style="margin-right: 6px;"></i>' + msg;
                        additionalHTML += '</span>';
                        additionalHTML += '</td>';
                        additionalHTML += '<td class="text-center">';
                        additionalHTML += '<div class="btn-group-vertical" role="group">';
                        if (value.ec_id != 84) {
                            additionalHTML += '<button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ', \'' + value.ec_title + '\', \'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus"></i> Tracking</button>';
                        }
                        additionalHTML += '<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                        additionalHTML += '</div>';
                        additionalHTML += '</td>';
                        additionalHTML += '</tr>';
                        recordsAdded++;
                    }
                });


                // Append new rows to table
                if (additionalHTML.length > 0) {
                    $('tbody').append(additionalHTML);

                    // Apply JavaScript blinking to newly added escalation rows
                    setTimeout(function () {
                        var newEscalationRows = $('tbody tr.escalation-border-blink').not('[data-blink-applied]');

                        newEscalationRows.each(function (index) {
                            var $row = $(this);
                            $row.attr('data-blink-applied', 'true');

                            // Add smooth transition for load more blinking
                            $row.css({
                                'transition': 'all 0.8s ease-in-out',
                                '-webkit-transition': 'all 0.8s ease-in-out',
                                '-moz-transition': 'all 0.8s ease-in-out'
                            });

                            // Smooth JavaScript blinking for load more rows
                            var blinkCount = 0;
                            var blinkInterval = setInterval(function () {
                                blinkCount++;
                                if (blinkCount % 2 === 0) {
                                    // Light orange - smooth transition
                                    $row.css({
                                        'border-left': '5px solid #ffb366',
                                        'background-color': 'rgba(255,182,102,0.12)',
                                        'box-shadow': '0 0 8px rgba(255,182,102,0.3)'
                                    });
                                } else {
                                    // Dark orange - smooth transition
                                    $row.css({
                                        'border-left': '5px solid #ff6b35',
                                        'background-color': 'rgba(255,107,53,0.18)',
                                        'box-shadow': '0 0 12px rgba(255,107,53,0.4)'
                                    });
                                }
                            }, 1800); // Slower 1.8 second cycle

                            $row.data('blinkInterval', blinkInterval);
                        });
                    }, 200);
                } else {
                    console.log('No HTML to append - records may be filtered out by coordinator check');
                }

                // Update pagination data
                window.paginationData = {
                    current_page: pagination.current_page,
                    total: pagination.total,
                    per_page: pagination.per_page,
                    has_more_pages: pagination.has_more_pages,
                    current_showing: pagination.to,
                    filters: filters
                };

                // Update UI based on remaining data
                var remainingCount = pagination.total - pagination.to;
                if (pagination.has_more_pages) {
                    $('#load-more-content button').html('<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)');
                    $('#load-more-content').show();
                    $('#scroll-loading').hide();
                    $('p.text-muted').text('Showing ' + pagination.to + ' of ' + pagination.total + ' records');
                } else {
                    $('#scroll-trigger').html('<div class="text-center p-2 border-top"><small class="text-muted">Showing all ' + pagination.total + ' records</small></div>');
                    $(window).off('scroll.lazyload'); // Remove scroll listener
                }

                // Apply lazy loading to new rows
                setTimeout(function () {
                    $('.lazy-load').addClass('loaded');
                }, 100);

                window.isLoadingMore = false;

            },
            error: function (xhr, status, error) {
                window.isLoadingMore = false;
                $('#load-more-content').show();
                $('#scroll-loading').hide();
                $('button[onclick*="loadMoreDataLaravel"]').html('<i class="fa fa-plus-circle"></i> Load More').prop('disabled', false);
                console.error('Load More Error:', status, error);
            }
        });
    }

    // Load More Data Function for Scroll (Performance Optimized)
    function loadMoreDataScroll(currentCount, totalCount) {
        if (!window.fullData || window.isLoadingMore) return;

        window.isLoadingMore = true;

        setTimeout(function () {
            var nextBatch = 50; // Load 50 more records
            var newCount = Math.min(currentCount + nextBatch, totalCount);
            var additionalData = window.fullData.slice(currentCount, newCount);

            // Generate HTML for additional rows (same logic as loadMoreData)
            var additionalHTML = '';
            var sr_no = currentCount + 1;

            coordinatorArray = ['21', '41', '42', '61', '81', '244', '344', '444', '464', '465', '524', '784', '944'];
            $.each(additionalData, function (key, value) {
                userGroupId = '<?= auth()->user()->group_id ?>';
                isCoordinator = coordinatorArray.includes(userGroupId);
                userDeptId = '<?= auth()->user()->dept_id ?>';
                if (userDeptId == 161) userDeptId = 203;

                if (isCoordinator && userDeptId == value.dept_id) {
                    var valueJson = JSON.stringify(value);
                    var badgeColor, escalationChek = value.esclation_time;
                    var color = '';

                    if (value.chkdeclined > 0) {
                        escalationChek = 'Request Declined by one of the Approver';
                    } else if (escalationChek == null && value.chkdeclined == 0) {
                        escalationChek = 'Request was declined and then rolled back';
                    }

                    if (value.er_request_status === 'Declined') {
                        badgeColor = 'red';
                    } else if (value.er_request_status === 'Pending') {
                        badgeColor = '#007bff';
                    } else if (value.er_request_status === 'Forward') {
                        badgeColor = '#ecb403';
                    } else {
                        badgeColor = 'green';
                    }

                    var msg = '', width = '';
                    if (value.er_request_status == 'Forward') {
                        msg = 'Forwarded From VC';
                        width = '156px';
                    } else {
                        msg = value.er_request_status;
                    }

                    var escapedValueJson = valueJson.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '&#10;').replace(/\r/g, '&#13;');

                    additionalHTML += '<tr style="color:' + color + '" class="lazy-load">';
                    additionalHTML += '<td scope="col" class="text-center">' + (sr_no++) + '</td>';

                    if (value.parent_req_id != 102) {
                        additionalHTML += '<td scope="col">' + (value.username ? value.full_name + ' (' + value.username + ')' : value.full_name) + '</td>';
                        additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + value.school + '</td>';
                        additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">' + (value.programs ? value.programs : 'N/A') + '</td>';
                    } else {
                        additionalHTML += '<td scope="col">Anonymous</td>';
                        additionalHTML += '<td scope="col" class="d-none d-md-table-cell">Anonymous</td>';
                        additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">Anonymous</td>';
                    }

                    let reinitiate_time = '';
                    if (value.reinitiate_time && value.erdatetime !== value.reinitiate_time) {
                        reinitiate_time = `
                                            <span class="badge badge-outline-primary"
                                                style="
                                                    border: 2px solid #007bff;
                                                    color: #007bff;
                                                    background-color: transparent;
                                                    font-size: 11px;
                                                    padding: 0.5em 0.8em;
                                                    white-space: nowrap;
                                                ">
                                                <i class="fa fa-repeat" style="margin-right: 6px;"></i>
                                                Reinitiated at<br>${value.reinitiate_time}
                                            </span>
                                        `;
                    }
                    additionalHTML += '<td scope="col">' + value.ec_title + '</td>';
                    additionalHTML += `
                            <td scope="col" class="d-none d-sm-table-cell">
                                ${value.erdatetime}
                                ${reinitiate_time ? '<br>' + reinitiate_time : ''}
                            </td>
                        `;
                    additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + escalationChek + '</td>';
                    additionalHTML += '<td scope="col" class="text-center"><span style="display: inline-block;' + width + '; padding: 0.25em 0.5em; border-radius: 0.25rem; background-color: ' + badgeColor + '; color: #fff; font-weight: bold;">' + msg + '</span></td>';
                    additionalHTML += '<td class="text-center">';
                    additionalHTML += '<div class="btn-group-vertical" role="group">';
                    if (value.ec_id != 84) {
                        additionalHTML += '<button type="button" class="btn btn-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ', \'' + value.ec_title + '\', \'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus"></i> Tracking</button>';
                    }
                    additionalHTML += '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                    additionalHTML += '</div>';
                    additionalHTML += '</td>';
                    additionalHTML += '</tr>';
                }
            });

            // Append new rows to table
            $('tbody').append(additionalHTML);

            // Update status and controls
            var remainingCount = totalCount - newCount;
            if (remainingCount > 0) {
                $('#load-more-content button').html('<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)');
                $('#load-more-content button').attr('onclick', 'loadMoreData(' + newCount + ', ' + totalCount + ')');
                $('#load-more-content').show();
                $('#scroll-loading').hide();
                $('p.text-muted').text('Showing ' + newCount + ' of ' + totalCount + ' records');
            } else {
                $('#scroll-trigger').html('<div class="text-center p-2 border-top"><small class="text-muted">Showing all ' + totalCount + ' records</small></div>');
                $(window).off('scroll.lazyload'); // Remove scroll listener
            }

            window.currentDisplayCount = newCount;
            window.isLoadingMore = false;
        }, 300); // Small delay for smooth UX
    }

    // Load More Data Function for Performance
    function loadMoreData(currentCount, totalCount) {
        if (!window.fullData) return;

        var loadMoreBtn = $('button[onclick*="loadMoreData"]');
        loadMoreBtn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        loadMoreBtn.prop('disabled', true);

        setTimeout(function () {
            var nextBatch = 50; // Load 50 more records
            var newCount = Math.min(currentCount + nextBatch, totalCount);
            var additionalData = window.fullData.slice(currentCount, newCount);

            // Generate HTML for additional rows
            var additionalHTML = '';
            var sr_no = currentCount + 1;

            coordinatorArray = ['21', '41', '42', '61', '81', '244', '344', '444', '464', '465', '524', '784', '944'];
            $.each(additionalData, function (key, value) {
                // Same row generation logic as main function
                userGroupId = '<?= auth()->user()->group_id ?>';
                isCoordinator = coordinatorArray.includes(userGroupId);
                userDeptId = '<?= auth()->user()->dept_id ?>';
                if (userDeptId == 161) userDeptId = 203;

                if (isCoordinator && userDeptId == value.dept_id) {
                    // Generate row HTML (same logic as main function)
                    var valueJson = JSON.stringify(value);
                    var badgeColor, escalationChek = value.esclation_time;
                    var color = '';

                    if (value.chkdeclined > 0) {
                        escalationChek = 'Request Declined by one of the Approver';
                    } else if (escalationChek == null && value.chkdeclined == 0) {
                        escalationChek = 'Request was declined and then rolled back';
                    }

                    if (value.er_request_status === 'Declined') {
                        badgeColor = 'red';
                    } else if (value.er_request_status === 'Pending') {
                        badgeColor = '#007bff';
                    } else if (value.er_request_status === 'Forward') {
                        badgeColor = '#ecb403';
                    } else {
                        badgeColor = 'green';
                    }

                    var msg = '', width = '';
                    if (value.er_request_status == 'Forward') {
                        msg = 'Forwarded From VC';
                        width = '156px';
                    } else {
                        msg = value.er_request_status;
                    }

                    var escapedValueJson = valueJson.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '&#10;').replace(/\r/g, '&#13;');

                    additionalHTML += '<tr style="color:' + color + '" class="lazy-load">';
                    additionalHTML += '<td scope="col" class="text-center">' + (sr_no++) + '</td>';

                    if (value.parent_req_id != 102) {
                        additionalHTML += '<td scope="col">' + (value.username ? value.full_name + ' (' + value.username + ')' : value.full_name) + '</td>';
                        additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + value.school + '</td>';
                        additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">' + (value.programs ? value.programs : 'N/A') + '</td>';
                    } else {
                        additionalHTML += '<td scope="col">Anonymous</td>';
                        additionalHTML += '<td scope="col" class="d-none d-md-table-cell">Anonymous</td>';
                        additionalHTML += '<td scope="col" class="d-none d-lg-table-cell">Anonymous</td>';
                    }

                    let reinitiate_time = '';
                    if (value.reinitiate_time && value.erdatetime !== value.reinitiate_time) {
                        reinitiate_time = `
                                            <span class="badge badge-outline-primary"
                                                style="
                                                    border: 2px solid #007bff;
                                                    color: #007bff;
                                                    background-color: transparent;
                                                    font-size: 11px;
                                                    padding: 0.5em 0.8em;
                                                    white-space: nowrap;
                                                ">
                                                <i class="fa fa-repeat" style="margin-right: 6px;"></i>
                                                Reinitiated at<br>${value.reinitiate_time}
                                            </span>
                                        `;
                    }
                    additionalHTML += '<td scope="col">' + value.ec_title + '</td>';
                    additionalHTML += `
                            <td scope="col" class="d-none d-sm-table-cell">
                                ${value.erdatetime}
                                ${reinitiate_time ? '<br>' + reinitiate_time : ''}
                            </td>
                        `;
                    additionalHTML += '<td scope="col" class="d-none d-md-table-cell">' + escalationChek + '</td>';
                    additionalHTML += '<td scope="col" class="text-center"><span style="display: inline-block;' + width + '; padding: 0.25em 0.5em; border-radius: 0.25rem; background-color: ' + badgeColor + '; color: #fff; font-weight: bold;">' + msg + '</span></td>';
                    additionalHTML += '<td class="text-center">';
                    additionalHTML += '<div class="btn-group-vertical" role="group">';
                    if (value.ec_id != 84) {
                        additionalHTML += '<button type="button" class="btn btn-success btn-sm mb-2" onclick="openModalStatus(' + value.er_id + ', \'' + value.ec_title + '\', \'' + value.username + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search-plus"></i> Tracking</button>';
                    }
                    additionalHTML += '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-value=\'' + escapedValueJson + '\' onclick="setProfileFromData(this)" data-target="#approval_status"><i class="fa fa-eye mr-1"></i>Details</button>';
                    additionalHTML += '</div>';
                    additionalHTML += '</td>';
                    additionalHTML += '</tr>';
                }
            });

            // Append new rows to table
            $('tbody').append(additionalHTML);

            // Update load more button or remove it
            var remainingCount = totalCount - newCount;
            if (remainingCount > 0) {
                loadMoreBtn.html('<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)');
                loadMoreBtn.attr('onclick', 'loadMoreData(' + newCount + ', ' + totalCount + ')');
                loadMoreBtn.prop('disabled', false);
                $('p.text-muted').text('Showing ' + newCount + ' of ' + totalCount + ' records');
            } else {
                loadMoreBtn.parent().html('<div class="text-center p-2 border-top"><small class="text-muted">Showing all ' + totalCount + ' records</small></div>');
            }

            window.currentDisplayCount = newCount;
        }, 500); // Small delay to show loading state
    }

    // Separate function to generate bank details HTML
    function generateBankDetailsHTML(valueObj) {
        // Check if there are any bank details values       
        var hasBankDetails = false;
        var hasPersonalInfo = valueObj.beneficiary_name || valueObj.relationship || valueObj.cnic_number || valueObj.mobile_number;
        var hasBankInfo = valueObj.bank_name || valueObj.iban || valueObj.bank_account_title;
        var hasFeeInfo = valueObj.original_tuition_fee || valueObj.original_medical_fee || valueObj.hostel_fee_original || valueObj.security_amt;
        var hasRefundInfo = valueObj.refund_tuition_fee || valueObj.refund_activity_fee || valueObj.total_refund_amount || valueObj.refund_percentage || valueObj.days_since_commencement || valueObj.hostel_fee_refundable || (valueObj.refund_security_fee != null);


        if (valueObj.er_applicant_request_type == 'refund' || valueObj.ec_id == 86 || valueObj.ec_id == 121) {
            $('#export_button').show();

            $('#bank_details_title').html('<i class="fa fa-money text-primary"></i> Refund Details');

        }

        if (valueObj.er_applicant_request_type == 'deferment' && valueObj.ec_id != 86) {
            $('#export_button').hide();

            $('#bank_details_title').html('<i class="fa fa-pause text-warning"></i> Deferment Details');
        }

        if (valueObj.er_applicant_request_type == 'degree_completion' || valueObj.ec_id == 23) {
            $('#export_button').show();

            $('#bank_details_title').html('<i class="fa fa-graduation-cap text-success"></i> Degree Completion Details');

        }




        if ((hasPersonalInfo || hasBankInfo || hasFeeInfo || hasRefundInfo) && (valueObj.ec_id == 120 || valueObj.ec_id == 86 || valueObj.ec_id == 23 || valueObj.ec_id == 121)) {
            hasBankDetails = true;
        }
        if (hasBankDetails && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'deferment' || valueObj.er_applicant_request_type == 'degree_completion' || valueObj.ec_id == 86 || valueObj.ec_id == 23 || valueObj.ec_id == 121)) {
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
                bankDetails += '<div class="row mb-1"><div class="col-5"><small class="text-muted">Mobile:</small></div><div class="col-7"><strong>' + (valueObj.mobile_number || 'N/A') + '</strong></div></div>';
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

            // Fee Information Section - Only show if there's data
            if (hasFeeInfo && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'degree_completion' || valueObj.ec_id == 86 || valueObj.ec_id == 23 || valueObj.ec_id == 121)) {
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
            if (hasRefundInfo && (valueObj.er_applicant_request_type == 'refund' || valueObj.er_applicant_request_type == 'degree_completion' || valueObj.ec_id == 86 || valueObj.ec_id == 23 || valueObj.ec_id == 121)) {
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

    // --- VC Style Forwarding Helper Functions ---
    var appendedIDs = {};

    function initStaffSelect2() {
        if ($('#staff_for_filter').hasClass('select2-hidden-accessible')) {
            return;
        }

        $('#staff_for_filter').select2({
            placeholder: 'Select Category...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('eportal/fetch_staff_data') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        staffId: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {

                            let deptName = (item.dept_name === 'Marketing')
                                ? 'Student Affairs'
                                : item.dept_name;

                            return {
                                id: item.id,
                                text: item.ec_title + ' (' + deptName + ')'
                            };
                        })
                    };
                }
            }
        });

        // SINGLE selection only
        $('#staff_for_filter').off('select2:select').on('select2:select', function (e) {
            var data = e.params.data;

            // Store selected value in ONE hidden input
            $('#selected_user').val(data.id);

            // Show selected name (optional)
            $('#selected_user_text').text(data.text);
        });
    }
    let isForwarding = false;
    function forwardToUser() {
        isForwarding = true;

        document.querySelectorAll('.userDropDown').forEach(el => el.style.display = 'block');
        document.getElementById('hideForwardRemarks').style.display = 'block';
        $('.userDropDown').show();
        $('#hideForwardRemarks').show(); // ✅ SHOW REMARKS
        $('#showForwardBtn').show();
        $('#hideResolverRemarks').hide();
        $('#showHideBtn').hide();
        $('#showHideDeclinedBtn').hide();
        initStaffSelect2();
    }

    function addStaffToTable(record) {
        if (!record || !record.id) {
            return;
        }

        if (appendedIDs[record.id]) {
            return;
        }

        $('#hideForwardRemarks').show();
        $('#showForwardBtn').show();
        $('#showHideBtn').hide();
        $('#showHideDeclinedBtn').hide();

        var appendDiv = `
        <div id="tab${record.id}" class="user_outer_div" 
             style="display:inline-block; background:#e9ecef; border:1px solid #ced4da; border-radius:4px; padding:5px 10px; margin:2px;">
             
            <div class="user_inner_div" style="display:inline-block;">
                <strong>${record.ec_title || ''}</strong>
                <div style="font-size:10px; color:#666;">
                    ${record.dept_name || ''}
                    ${record.eportal_dept_name ? ' | ' + record.eportal_dept_name : ''}
                </div>
            </div>

            <div class="user_inner_div inner_div_when_edit"
                 onclick="remove_table_td(${record.id})"
                 style="display:inline-block; margin-left:10px; cursor:pointer; color:red; font-weight:bold; vertical-align:top;">
                <i class="fa fa-times"></i>
            </div>
        </div>
    `;

        $('.recordsDiv').append(appendDiv);

        $('#hidden_users_inputs').append(
            `<input type="hidden" name="users[]" value="${record.id}" id="user_input_${record.id}">`
        );

        appendedIDs[record.id] = true;
    }

    function remove_table_td(id) {
        $('#tab' + id).remove();
        $('#user_input_' + id).remove();
        delete appendedIDs[id];

        if ($('.recordsDiv').children().length === 0) {
            $('#hideForwardRemarks').hide();
            $('#showForwardBtn').hide();
            $('#showHideBtn').show();
            $('#showHideDeclinedBtn').show();
        }
    }

    function resetForward() {
        isForwarding = false;

        document.querySelectorAll('.userDropDown').forEach(el => el.style.display = 'none');
        document.getElementById('hideForwardRemarks').style.display = 'none';
        $('.recordsDiv').html('');
        $('#hidden_users_inputs').html('');
        $('.userDropDown').hide();
        $('#hideForwardRemarks').hide();
        $('#hideResolverRemarks').show();
        // Reset Buttons
        $('#showForwardBtn').hide();
        $('#showJustForwardBtn').hide();
        $('#showHideBtn').show();
        $('#showHideDeclinedBtn').show();

        appendedIDs = {};
    }

    function resetEportalModal() {
        $('#header_modal').html('');
        $('#stdname').html('');
        $('#stdStatus').html('');
        $('#stdschool').html('');
        $('#stdprog').html('');
        $('#stdmob').html('');
        $('#stddesc').html('');
        $('#fwd_by_name').html('');
        $('#forwardedByRow').hide();
        $('#vc_remarks').html('');
        $('#showVcRemarks').hide();
        $('#res_remarks').html('');
        $('#resRemarks').hide();
        $('#bank_details').hide();
        $('#vcRemarksLabel').html('Remarks:');
    }

    function setForwarderInfo(valueObj) {
        let fwdName = valueObj.forwarder_name || valueObj.forwarder_name || 'Unknown';
        let isForwarded = valueObj.parent_er_id ? true : false;

        // Body Row
        if (isForwarded) {
            $('#forwardedByRow').show();
            $('#fwd_by_name').html(fwdName);
        } else {
            $('#forwardedByRow').hide();
            $('#fwd_by_name').html('');
        }

        // Label
        if (valueObj.ec_id == 41) {
            $('#vcRemarksLabel').html('VC Remarks:');
        } else {
            if (isForwarded && fwdName != 'Unknown') {
                $('#vcRemarksLabel').html('Forwarded by ' + fwdName + ':');
            } else {
                $('#vcRemarksLabel').html('Forwarded by:');
            }
        }

        return fwdName;
    }

    function setProfile(valueJson) {
        resetEportalModal();
        var valueObj = JSON.parse(valueJson);

        // Store the current valueObj globally for export functions
        window.currentValueObj = valueObj;

        // Show/Hide Forwarder Row Globally & Set Label
        let fwdName = setForwarderInfo(valueObj);

        // Use the separate function to generate bank details
        var bankDetailsResult = generateBankDetailsHTML(valueObj);

        if (bankDetailsResult.hasData) {
            $('#bank_details').show();
            $('#application_bank_details').html(bankDetailsResult.html);
        } else {
            $('#bank_details').hide();
        }

        if (valueObj.parent_req_id != 102) {
            if (valueObj.username == null) {
                let title = valueObj.full_name;
                if (valueObj.parent_er_id) {
                    let fwdName = valueObj.forwarder_name ? valueObj.forwarder_name : 'Unknown';
                    title += ' (Forwarded By ' + fwdName + ')';
                }
                $('#header_modal').html(title);
                $('#stdname').html(valueObj.full_name);


            } else {
                let title = valueObj.username;
                if (valueObj.parent_er_id) {
                    let fwdName = valueObj.forwarder_name ? valueObj.forwarder_name : 'Unknown';
                    title += ' (Forwarded By ' + fwdName + ')';
                }
                $('#header_modal').html(title);
                $('#stdname').html(valueObj.full_name);
                let statusClass = valueObj.sai_student_status == 'Left' ? 'badge badge-danger' : 'badge badge-primary';
                $('#stdStatus').html('<span class="' + statusClass + '">' + (valueObj.sai_student_status ? valueObj.sai_student_status : 'N/A') + '</span>');
            }

            $('#stdschool').html(valueObj.school);
            $('#stdprog').html(valueObj.programs ? valueObj.programs : 'N/A');
            $('#stdmob').html(valueObj.mobile ? valueObj.mobile : 'N/A');

            // Show/Hide Forwarder Row (AJAX Path)
            if (valueObj.parent_er_id) {
                let fwdName = valueObj.forwarder_name || valueObj.forwarder_name || 'Unknown';
                $('#forwardedByRow').show();
                $('#fwd_by_name').html(fwdName);
            } else {
                $('#forwardedByRow').hide();
                $('#fwd_by_name').html('');
            }
        }
        else {
            $('#header_modal').html('Anonymous');
            $('#stdname').html('Anonymous');
            $('#stdschool').html('Anonymous');
            $('#stdprog').html('Anonymous');
            $('#stdmob').html('Anonymous');
        }
        $('#stddesc').html(valueObj.description);
        $('#er_id').val(valueObj.er_id);
        if (valueObj.er_ecd_vc_remarks) {
            $('#showVcRemarks').show();
            $('#vc_remarks').html(valueObj.er_ecd_vc_remarks);
            // Label logic moved to setForwarderInfo
        } else if (valueObj.er_remarks) {
            $('#resRemarks').show();
            $('#res_remarks').html(valueObj.er_remarks);
        } else {
            $('#showVcRemarks').hide();
            $('#vc_remarks').html('');
        }

        if (valueObj.ec_id == 85) {
            let id = valueObj.er_id;
            $('#view_status_div').show();
            $('#view_status').html(`<a class="btn btn-primary" href="https://staff.bnu.edu.pk/leaving_form_report.php?id=${id}" target="_blank"><i class="fa fa-eye"></i> View Status</a>`);
        } else {
            $('#view_status_div').hide();
            $('#view_status').html('')
        }
       if (valueObj.id == 86 || valueObj.id == 121) {
            let id = valueObj.username;
            let erID = valueObj.er_id;
            $('#check_form_div').show();
            $('#checkForm').html('');
            $('#checkFormTracking').html(`  <a  href="https://staff.bnu.edu.pk/leaving_form_report_tracking.php?id=${erID}" class="btn btn-primary"><i class="fa fa-search"></i> View Tracking PDF</a>`);
        } else {
            $('#check_form_div').hide();
            $('#checkForm').html('')
        }
        $('#er_id').val(valueObj.er_id);
        if (valueObj.ec_id == 18 || valueObj.ec_id == 25) {
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
            let str = valueObj.er_request_file;

            if (valueObj.er_request_file) {
                if (str.search("drive.google.com") > 0) {

                    $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                }
                else {
                    $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student<i class="fa fa-download larger-icon"></i></b></a>');
                }
            } else {
                $('#uploadedFileStudent').html('N/A');
            }
        }
        if (valueObj.google_drive_url) {
            $('#uploadedFile').html('<a target="_blank" href="' + valueObj.google_drive_url + '"><b>Uploaded File by Department <i class="fa fa-download larger-icon"></i></b></a>');
        } else {
            $('#uploadedFile').html('N/A');
        }
        var taskstatus = $('#taskStatus').val();
        var html = '';
        html += '<form id="approvalForm">';
        html += '<input type="hidden" id="er_id" name="er_id" value="' + valueObj.er_id + '">';
        html += '<input type="hidden"  name="stdroll" value="' + valueObj.username + '">';
        html += '<input type="hidden"  name="admissionApplicant" value="admission">';
        html += '<input type="hidden"  name="EmailUser" value="' + valueObj.er_sai_enrollment_no + '">';
        html += '<input type="hidden"  name="ec_title" value="' + valueObj.ec_title + '">';
        html += '<input type="hidden"  name="ec_title" value="' + valueObj.ec_title + '">';

        // Logic Update: Allow action if NO viewer is assigned OR if the current user IS the Resolver
        // This fixes the issue where a "Viewer" presence might lock the request for the actual Resolver.
        var isResolver = (valueObj.er_ecd_resolving_user == loggedInEmpId);

        // Store globally for other functions
        window.requestIsResolver = isResolver;
        window.requestViewer = valueObj.er_ecd_viewer_user;

        if (valueObj.er_ecd_viewer_user == null || isResolver) {
            if (taskstatus == 'Pending') {
                // --- Add Forwarding UI ---
                html += '<div class="form-group" style="background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #e9ecef;">';
                html += '<label class="text-primary"><i class="fa fa-share"></i> <b>Forward To (Generic Staff):</b></label>';
                html += '<div class="input-group mb-2">';
                html += '<input type="text" class="form-control" id="search_user" placeholder="Search Name..." autocomplete="off" onkeyup="fetchStaff(this.value)">';
                html += '<div class="input-group-append"><span class="input-group-text"><i class="fa fa-search"></i></span></div>';
                html += '</div>';
                html += '<div id="user_list_dropdown" style="display:none; position:absolute; z-index:1000; background:white; border:1px solid #ddd; width:90%; max-height:200px; overflow-y:auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>';
                html += '<div id="selected_users_container" class="mt-2"></div>';
                html += '<div style="display:none" id="hidden_users_inputs"></div>';

                html += '<div id="forward_remarks_div"  margin-top:10px;">';
                html += '<label><b>Forwarding Remarks:</b></label>';
                html += '<input type="text" class="form-control" name="remarksToForward" placeholder="Instructions for the recipient...">';
                html += '</div>';
                html += '</div>';
                // -------------------------

                html += '<label for="remarks"><b>Resolver Remarks:</b></label>';
                html += '<div class="form-group">';
                html += '<input type="text" class="form-control" name="remarks" id="req_remarks" maxlength="1500" placeholder="Enter remarks (max 1500 characters)" oninput="updateRemarksCounter(this)">';
                html += '<div class="d-flex justify-content-end mt-1">';
                html += '<span class="badge badge-secondary" style="font-size: 0.7rem;" id="remarks_counter">0/1500</span>';
                html += '</div>';
                html += '</div>';
                html += '<div class="form-group">';
                html += '<label for="file">Upload File:</label>';
                html += '<input type="file" class="form-control-file" id="file" name="file" accept=".pdf, .jpg, .jpeg, .png">';
                html += '<small class="form-text text-muted">Only <span class="text-danger">PDF, JPG, PNG and JPEG</span> files are allowed.</small>';
                html += '<span id="file-error" class="error text-danger"></span>';
                html += '</div>';
            }
            // && valueObj.chkdeclined == 0
            if (valueObj.chkapproval == 0) {//display buttons only if NO PENDING APPROVAL AND NO DECLINED APPROVAL
                html += '<div style="float:right;margin-bottom: 10px">';
                html += '<span id="remarksReq"></span>';
                html += '<button id="btnloader" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ';
                html += '<i class="fa fa-cogs mr-1"></i>Action';
                html += '</button>';
                html += '<div class="dropdown-menu"> ';
                if (taskstatus == 'Completed') {
                    html += '<a class="dropdown-item" onclick="reqBtn(\'Pending\')">RollBack</a>';
                } else {
                    html += '<a class="dropdown-item" onclick="reqBtn(\'Completed\')">Mark as Complete</a>';
                    html += '<a class="dropdown-item" onclick="reqBtn(\'Declined\')">Declined</a>';
                    html += '<div class="dropdown-divider"></div>';
                    html += '<a class="dropdown-item text-primary" onclick="reqBtn(\'justForward\')"><i class="fa fa-share mr-1"></i> Just Forward</a>';
                    html += '<a class="dropdown-item text-success" onclick="reqBtn(\'Forward\')"><i class="fa fa-check-circle mr-1"></i> Forward & Complete</a>';
                }
                html += '</div>';
            } else {
                html += ' <span class="badge badge-info badge-pill badge-enhanced" style="box-shadow: 0 2px 8px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.2); font-weight: 500;"><i class="fa fa-info-circle mr-1"></i>Click on Tracking to view Approval Pending</span>';
            }
        }
        html += '</div>';
        html += '</form>';
        $('#dropDown').html(html);

    }

    // New function to handle data from data attribute (safe for special characters)
    function setProfileFromData(buttonElement) {
        try {
            // Get the data from the button's data-value attribute
            var escapedValueJson = $(buttonElement).attr('data-value');

            // Decode the HTML entities back to their original characters
            var valueJson = escapedValueJson
                .replace(/&quot;/g, '"')
                .replace(/&#39;/g, "'")
                .replace(/&#10;/g, '\n')
                .replace(/&#13;/g, '\r');

            // Parse the JSON and call the original setProfile function
            var valueObj = JSON.parse(valueJson);

            // Store the current valueObj globally for export functions
            window.currentValueObj = valueObj;

            // Use the separate function to generate bank details
            var bankDetailsResult = generateBankDetailsHTML(valueObj);

            if (bankDetailsResult.hasData) {
                $('#bank_details').show();
                $('#application_bank_details').html(bankDetailsResult.html);
            } else {
                $('#bank_details').hide();
            }

            if (valueObj.parent_req_id != 102) {
                if (valueObj.username == null) {
                    $('#header_modal').html(valueObj.full_name);
                    $('#stdname').html(valueObj.full_name);
                } else {
                    $('#header_modal').html(valueObj.username);
                    $('#stdname').html(valueObj.full_name);
                    let statusClass = valueObj.sai_student_status == 'Left' ? 'badge badge-danger' : 'badge badge-primary';
                    $('#stdStatus').html('<span class="' + statusClass + '">' + (valueObj.sai_student_status ? valueObj.sai_student_status : 'N/A') + '</span>');
                }

                $('#stdschool').html(valueObj.school);
                $('#stdprog').html(valueObj.programs ? valueObj.programs : 'N/A');
                $('#stdmob').html(valueObj.mobile ? valueObj.mobile : 'N/A');
            } else {
                $('#header_modal').html('Anonymous');
                // If forwarded, maybe show here too? But Anonymous logic implies simpler view.
                if (valueObj.parent_er_id) {
                    let fwdName = valueObj.forwarder_name ? valueObj.forwarder_name : 'Unknown';
                    $('#header_modal').append(' (Forwarded By ' + fwdName + ')');
                }

                $('#stdname').html('Anonymous');
                $('#stdschool').html('Anonymous');
                $('#stdprog').html('Anonymous');
                $('#stdmob').html('Anonymous');
            }

            $('#stddesc').html(valueObj.description);
            $('#er_id').val(valueObj.er_id);

            // Update Forwarder UI (Row & Label)
            let fwdName = setForwarderInfo(valueObj);

            if (valueObj.er_ecd_vc_remarks) {
                $('#showVcRemarks').show();
                $('#vc_remarks').html(valueObj.er_ecd_vc_remarks);
            } else if (valueObj.er_remarks) {
                $('#resRemarks').show();
                $('#res_remarks').html(valueObj.er_remarks);
            } else {
                $('#showVcRemarks').hide();
                $('#vc_remarks').html('')
            }

            if (valueObj.ec_id == 85) {
                let id = valueObj.er_id;
                $('#view_status_div').show();
                $('#view_status').html(`<a class="btn btn-primary" href="https://staff.bnu.edu.pk/leaving_form_report.php?id=${id}" target="_blank"><i class="fa fa-eye"></i> View Status</a>`);
            } else {
                $('#view_status_div').hide();
                $('#view_status').html('')
            }

              if (valueObj.id == 86 || valueObj.id == 121) {
                let id = valueObj.username;
                let erID = valueObj.er_id;
                $('#check_form_div').show();
                $('#checkForm').html('');
                $('#checkFormTracking').html(`  <a  href="https://staff.bnu.edu.pk/leaving_form_report_tracking.php?id=${erID}" class="btn btn-primary"><i class="fa fa-search"></i> View Tracking PDF</a>`);
            } else {
                $('#check_form_div').hide();
                $('#checkForm').html('')
            }

            $('#er_id').val(valueObj.er_id);

            if (valueObj.ec_id == 18 || valueObj.ec_id == 25) {
                if (valueObj.er_student_upload_challan) {
                    let str = valueObj.er_student_upload_challan;
                    if (str.search("drive.google.com") > 0) {
                        $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_student_upload_challan + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                    } else {
                        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_student_upload_challan + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                    }
                } else {
                    $('#uploadedFileStudent').html('N/A');
                }
            } else {
                let str = valueObj.er_request_file;

                if (valueObj.er_request_file) {
                    if (str.search("drive.google.com") > 0) {
                        $('#uploadedFileStudent').html('<a target="_blank" href="' + valueObj.er_request_file + '"><b>Uploaded File by Student <i class="fa fa-download larger-icon"></i></b></a>');
                    } else {
                        $('#uploadedFileStudent').html('<a target="_blank" href="https://student.bnu.edu.pk/images/' + valueObj.er_request_file + '"><b>Uploaded File by Student<i class="fa fa-download larger-icon"></i></b></a>');
                    }
                } else {
                    $('#uploadedFileStudent').html('N/A');
                }
            }

            if (valueObj.google_drive_url) {
                $('#uploadedFile').html('<a target="_blank" href="' + valueObj.google_drive_url + '"><b>Uploaded File by Department <i class="fa fa-download larger-icon"></i></b></a>');
            } else {
                $('#uploadedFile').html('N/A');
            }

            var taskstatus = $('#taskStatus').val();
            var html = '';
            html += '<form id="approvalForm">';
            html += '<input type="hidden" id="er_id" name="er_id" value="' + valueObj.er_id + '">';
            html += '<input type="hidden"  name="stdroll" value="' + valueObj.username + '">';
            html += '<input type="hidden"  name="admissionApplicant" value="admission">';
            html += '<input type="hidden"  name="EmailUser" value="' + valueObj.er_sai_enrollment_no + '">';
            html += '<input type="hidden"  name="ec_title" value="' + valueObj.ec_title + '">';

            // Logic Update: Allow action if NO viewer is assigned OR if the current user IS the Resolver
            var isResolver = (valueObj.er_ecd_resolving_user == loggedInEmpId);

            if (valueObj.er_ecd_viewer_user == null || isResolver) {
                if (taskstatus == 'Pending') {

                    // --- VC STYLE FORWARDING UI ---
                    html += '<div class="row" id="showuser">';

                    html += '<div class="col-12 col-sm-3 col-md-3 col-lg-3">';
                    html += '<button onclick="forwardToUser()" type="button" class="btn btn-primary btn-sm">';
                    html += '<i class="fa fa-share"></i> Forward To Category</button>';
                    html += '</div>';

                    html += '<div class="col-12 col-sm-7 col-md-7 col-lg-7 userDropDown" style="display:none">';
                    html += '<select name="user_id" id="staff_for_filter" class="form-control" style="width:100%;">';
                    html += '<option value="">Search...</option>';
                    html += '</select>';
                    html += '</div>';

                    html += '<div class="col-12 col-sm-2 col-md-2 col-lg-2 userDropDown" style="display:none">';
                    html += '<i class="fa fa-history" onclick="resetForward()" style="padding-top: 8px;font-size:16px;cursor:pointer;color:blue;"> Clear </i>';
                    html += '</div>';
                    html += '</div>';

                    // Container for selected users (VC Style "recordsDiv")
                    html += '<div class="row"><div class="col-sm-12 recordsDiv mb-3 mt-2"></div></div>';

                    // Hidden Input for Users (to be populated by JS)
                    html += '<div id="hidden_users_inputs"></div>';

                    html += '<div id="hideForwardRemarks" style="display:none; margin-top:10px;">';
                    html += '<label for="remarks" class="text-primary"><b>Remarks to Forwarding Person:</b></label>';
                    html += '<input type="text" class="form-control" name="remarksToForward" id="req_remarks_forward" placeholder="Instructions...">';
                    html += '</div>';
                    // ------------------------------

                    html += '<div id="hideResolverRemarks"  margin-top:10px;">';
                    html += '<label for="remarks"><b>Resolver Remarks:</b></label>';
                    html += '<div class="form-group">';
                    html += '<input type="text" class="form-control" name="remarks" id="req_remarks" maxlength="1500" placeholder="Enter remarks (max 1500 characters)" oninput="updateRemarksCounter(this)">';
                    html += '<div class="d-flex justify-content-end mt-1">';
                    html += '<span class="badge badge-secondary" style="font-size: 0.7rem;" id="remarks_counter">0/1500</span>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    html += '<div class="form-group">';
                    html += '<label for="file">Upload File:</label>';
                    html += '<input type="file" class="form-control-file" id="file" name="file" accept=".pdf, .jpg, .jpeg, .png">';
                    html += '<small class="form-text text-muted">Only <span class="text-danger">PDF, JPG, PNG and JPEG</span> files are allowed.</small>';
                    html += '<span id="file-error" class="error text-danger"></span>';
                    html += '</div>';
                }

                if (valueObj.chkapproval == 0) {
                    html += '<div style="float:right;margin-bottom: 10px">';
                    html += '<span id="remarksReq"></span>';

                    html += '<button id="btnloader" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ';
                    html += '<i class="fa fa-cogs mr-1"></i>Action';
                    html += '</button>';
                    html += '<div class="dropdown-menu"> ';
                    if (taskstatus == 'Completed') {
                        html += '<a class="dropdown-item" onclick="reqBtn(\'Pending\')">RollBack</a>';
                    } else {
                        html += '<a class="dropdown-item" id="showHideBtn" onclick="reqBtn(\'Completed\')">Mark as Complete</a>';
                        html += '<a class="dropdown-item" id="showHideDeclinedBtn" onclick="reqBtn(\'Declined\')">Declined</a>';
                        html += '<div class="dropdown-divider"></div>';
                        html += '<a class="dropdown-item text-primary" id="showJustForwardBtn" style="display:none !important" onclick="reqBtn(\'justForward\')"><i class="fa fa-share mr-1"></i> Just Forward</a>';
                        html += '<a class="dropdown-item text-success" id="showForwardBtn" style="display:none" onclick="reqBtn(\'Forward\')"><i class="fa fa-check-circle mr-1"></i> Forward & Complete</a>';
                    }
                    html += '</div>';

                    html += '</div>';
                } else {
                    html += ' <span class="badge badge-info badge-pill badge-enhanced" style="box-shadow: 0 2px 8px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.2); font-weight: 500;"><i class="fa fa-info-circle mr-1"></i>Click on Tracking to view Approval Pending</span>';
                }
            }
            html += '</div>';
            html += '</form>';
            $('#dropDown').html(html);

            // Initialize Select2 for Staff Search
            initStaffSelect2();

        } catch (error) {
            console.error('Error parsing JSON data:', error);
            alert('Error loading request details. Please try again.');
        }
    }
    function openModalStatus(id, categoryName, username) {
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
                // Set modal title once outside the loop
                $('#application_status').html(res.data[0].title + ' - ' + ' (' + username + ')');

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

    function getTimeAgo(dateString) {
        const now = new Date();
        const messageDate = new Date(dateString);
        const diffInSeconds = Math.floor((now - messageDate) / 1000);

        if (diffInSeconds < 60) {
            return 'Just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return minutes + ' min ago';
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
        } else if (diffInSeconds < 2592000) {
            const days = Math.floor(diffInSeconds / 86400);
            return days + ' day' + (days > 1 ? 's' : '') + ' ago';
        } else {
            return messageDate.toLocaleDateString();
        }
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
                let parent_req_id = res.parent_req_id;
                $('#tmTitle').html(tmTitle);
                let parent_message_id = res.data[0].parent_message_id;
                let messege_to = '';
                if (res.status) {
                    $('#msg_cotainer').html('');
                    $('#msg_cotainer_send').html('');

                    $(res.data).each(function (index, row) {
                        if (ad_username != row.entry_user_id) {
                            if (parent_req_id == 102) {//anonymous request to VC
                                messege_to = 'Anonymous';
                            } else {
                                messege_to = row.message_to;
                            }
                            var new_row_append =
                                '<div class="chat_div_other row"><div class="col-4 text-center" style="padding-right: 25px;font-size:12px"><strong>'
                                + ($('#checkDrugStatus' == 82) ? messege_to : messege_to) +
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

        // Show loading state
        $('#messageBtnText').hide();
        $('#messageBtnLoading').show();
        $('#messageBtn').addClass('btn-loading').prop('disabled', true);

        var bi_form = document.querySelector('#sendMessage_form');
        var formData = new FormData(bi_form);
        $.ajax({
            type: 'POST',
            url: '/eportal/send-message-to-user',
            cache: false, // Disable caching
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json'
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    // Clear message input
                    $('#messageTextarea').val('');
                    $('#countLimit').text('');

                    // Reload messages with auto-scroll
                    getMsgs($('#RequestID').val());
                } else {
                    alert('Failed to send message. Please try again.');
                }
            },
            error: function () {
                alert('Error sending message. Please check your connection.');
            },
            complete: function () {
                // Hide loading state
                $('#messageBtnText').show();
                $('#messageBtnLoading').hide();
                $('#messageBtn').removeClass('btn-loading').prop('disabled', false);
            }
        });
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
        if (isForwarding) {
            return true;
        }
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

    // Function to update inline category dropdown when main category changes
    function updateInlineCategoryDropdown() {
        var inlineCategoryElement = $('#inlineCategory_id');
        if (inlineCategoryElement.length > 0) {
            var mainCategoryValue = $('#category_id option:selected').val();
            inlineCategoryElement.val(mainCategoryValue);
            console.log('Updated inline category to:', mainCategoryValue);
        }
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
            doc.text('Personal Information', 115, yPosition);

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
                ['Applicant ID', currentValueObj.er_sai_enrollment_no || 'N/A'],
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
            // // Modern footer with soft line
            // doc.setDrawColor(100, 181, 246);
            // doc.setLineWidth(0.5);
            // doc.line(20, 270, 190, 270);

            // doc.setFontSize(8);
            // doc.setFont('helvetica', 'italic');
            // doc.text('Generated by BNU CMS System', 105, 280, { align: 'center' });

            // Save PDF
            doc.save(`refund_details_${new Date().toISOString().slice(0, 10)}.pdf`);
        } catch (error) {
            console.error('PDF export error:', error);
            alert('Error exporting PDF file. Please try again.');
        }
    }
</script>
@stop