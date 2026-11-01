@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')
{{-- @dd("123"); --}}
@include('components.toast-notifications')
  <style>
    /* =========================================================
       Figma-aligned Road Map Session presentation layer
       Visual reference: connected Figma node 1278:3 (Road Map Session).
       Business logic, IDs, names, Blade loops and JavaScript are preserved.
       ========================================================= */

    .roadmap-session-page {
        --rm-primary: #17675f;
        --rm-primary-dark: #12564f;
        --rm-warning: #ffba49;
        --rm-text: #252525;
        --rm-muted: #64748b;
        --rm-border: #e5e7eb;
        --rm-soft: #f5f5f5;
        width: 100%;
        min-height: 100vh;
        margin: 0 !important;
        /* padding: 28px 30px 44px !important; */
        background: #f2f6f9;
        color: var(--rm-text);
        font-family: 'Montserrat', sans-serif;
    }

    .roadmap-session-page *,
    .roadmap-session-page *::before,
    .roadmap-session-page *::after {
        box-sizing: border-box;
    }

    .roadmap-page-heading {
        margin: 0 0 16px;
    }

    .roadmap-page-heading h2 {
        margin: 0 0 4px;
        color: var(--rm-text);
        font-size: 20px;
        line-height: 1.35;
        font-weight: 600;
        text-align: left;
    }

    .roadmap-page-heading p {
        margin: 0;
        color: var(--rm-text);
        font-size: 14px;
        line-height: 1.45;
        font-weight: 400;
    }

    /* Figma pill tabs */
    .roadmap-session-page #quotationTabs {
        display: flex;
        flex-wrap: wrap;
        gap: 12px !important;
        margin: 0 0 18px;
        padding: 0;
        border: 0;
    }

    .roadmap-session-page #quotationTabs .nav-item {
        margin: 0;
    }

    .roadmap-session-page #quotationTabs .nav-link {
        min-height: 41px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 24px !important;
        border: 1px solid var(--rm-border);
        border-radius: 999px !important;
        background: #fff;
        color: #111827;
        font-size: 14px;
        line-height: 1.2;
        font-weight: 500;
        box-shadow: none;
    }

    .roadmap-session-page #quotationTabs .nav-link.active,
    .roadmap-session-page #quotationTabs .nav-link:hover,
    .roadmap-session-page #quotationTabs .nav-link:focus {
        border-color: var(--rm-primary);
        background: var(--rm-primary);
        color: #fff;
    }

    .roadmap-session-page .tab-content {
        margin-top: 0 !important;
    }

    /* Original section cards remain in the DOM; presentation matches Figma. */
    .roadmap-session-page #roadmapWiseOffering > .card,
    .roadmap-session-page #studentWiseOffering > .card {
        margin: 0;
        border: 0 !important;
        border-radius: 0;
        background: transparent;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapWiseOffering > .card > .card-header,
    .roadmap-session-page #studentWiseOffering > .card > .card-header {
        display: none;
    }

    .roadmap-session-page #formCourse,
    .roadmap-session-page #st_formCourse {
        margin: 0;
        border-radius: 20px;
        background: #fff;
        overflow: visible;
        box-shadow: none;
    }

    .roadmap-session-page #formCourse > .card-body,
    .roadmap-session-page #st_formCourse > .card-body {
        padding: 30px 27px 20px;
    }

    /* Roadmap form: three-column Figma field grid without removing any original field. */
    .roadmap-session-page #formCourse > .card-body {
        display: flex;
        flex-wrap: wrap;
        column-gap: 24px;
        row-gap: 0;
    }

    .roadmap-session-page #formCourse > .card-body > .form-row {
        flex: 0 0 100%;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
        margin: 0 0 20px !important;
    }

    .roadmap-session-page #formCourse > .card-body > .form-row > .form-group {
        width: 100%;
        max-width: none;
        flex: none;
        margin: 0;
        padding: 0;
    }

    .roadmap-session-page #studentCount {
        flex: 0 0 calc(33.333% - 16px);
        width: calc(33.333% - 16px);
        margin: 0 0 20px !important;
    }

    .roadmap-session-page #sectionsDiv {
        flex: 1 1 calc(66.667% - 8px);
        width: calc(66.667% - 8px);
        margin: 0 0 20px !important;
    }

    .roadmap-session-page #error-messages,
    .roadmap-session-page #success-message {
        flex: 0 0 100%;
        width: 100%;
        margin-top: 0 !important;
        border-radius: 8px;
    }

    .roadmap-session-page label,
    .roadmap-session-page .form-label {
        display: block;
        margin: 0 0 8px;
        color: #111827;
        font-size: 14px;
        line-height: 1.5;
        font-weight: 600;
    }

    .roadmap-session-page .form-control,
    .roadmap-session-page .select2-container .select2-selection--single,
    .roadmap-session-page .select2-container .select2-selection--multiple {
        width: 100%;
        min-height: 48px !important;
        border: 1px solid var(--rm-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #111827 !important;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px !important;
        font-weight: 400;
        box-shadow: none !important;
    }

    .roadmap-session-page .form-control {
        height: 48px;
        padding: 11px 16px;
    }

    .roadmap-session-page .form-control:focus,
    .roadmap-session-page .select2-container--focus .select2-selection--single,
    .roadmap-session-page .select2-container--open .select2-selection--single,
    .roadmap-session-page .select2-container--focus .select2-selection--multiple {
        border-color: var(--rm-primary) !important;
        box-shadow: 0 0 0 3px rgba(23,103,95,.08) !important;
        outline: none;
    }

    .roadmap-session-page .select2-container {
        width: 100% !important;
    }

    .roadmap-session-page .select2-container .select2-selection--single .select2-selection__rendered {
        height: 46px;
        padding: 0 38px 0 16px !important;
        color: #111827 !important;
        line-height: 46px !important;
    }

    .roadmap-session-page .select2-container .select2-selection--single .select2-selection__placeholder,
    .roadmap-session-page .select2-container .select2-selection--multiple .select2-search__field::placeholder {
        color: #9ca3af !important;
    }

    .roadmap-session-page .select2-container .select2-selection--single .select2-selection__arrow {
        top: 2px !important;
        right: 10px !important;
        height: 44px !important;
    }

    .roadmap-session-page .select2-container .select2-selection--multiple {
        height: auto !important;
        padding: 5px 10px;
    }

    .roadmap-session-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 3px;
        border: 0;
        border-radius: 999px;
        background: #eef7f5;
        color: var(--rm-primary);
        font-size: 12px;
    }

    .roadmap-session-page .input-group {
        min-height: 48px;
    }

    .roadmap-session-page .input-group .form-control {
        border-radius: 8px 0 0 8px !important;
    }

    .roadmap-session-page .input-group-text {
        min-width: 46px;
        min-height: 48px;
        justify-content: center;
        border: 1px solid var(--rm-border);
        border-left: 0;
        border-radius: 0 8px 8px 0;
        background: #f8fafc;
        color: var(--rm-muted);
    }

    .roadmap-session-page input[readonly] {
        background: #f8fafc !important;
        color: #64748b !important;
    }

    /* Dynamic section rows preserve original markup and behavior. */
    .roadmap-session-page #sectionsContainer .sectionRow {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 48px;
        gap: 10px;
        margin: 0 0 10px !important;
        align-items: center;
    }

    .roadmap-session-page #sectionsContainer .sectionRow > [class*='col-'] {
        width: 100%;
        max-width: none;
        flex: none;
        padding: 0;
    }

    .roadmap-session-page #addSectionBtn {
        min-height: 48px;
        margin-top: 0 !important;
        padding: 11px 16px;
        border: 1px solid var(--rm-border) !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #64748b !important;
        font-size: 14px;
        font-weight: 500;
        text-align: left;
    }

    /* Buttons */
    .roadmap-session-page .btn {
        min-height: 40px;
        padding: 9px 18px;
        border-radius: 8px !important;
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
        line-height: 1.25;
        font-weight: 600;
        box-shadow: none !important;
    }

    .roadmap-session-page #formCourse > .card-footer,
    .roadmap-session-page #st_formCourse > .card-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        padding: 0 27px 30px;
        border: 0;
        background: #fff;
        text-align: left !important;
        border-radius: 0 0 20px 20px;
    }

    .roadmap-session-page #formCourse > .card-footer .btn-success,
    .roadmap-session-page #st_formCourse > .card-footer .btn-success,
    .roadmap-session-page #st_roadmap_session,
    .roadmap-session-page #saveCourseBtn {
        min-height: 50px;
        border: 1px solid var(--rm-primary) !important;
        background: var(--rm-primary) !important;
        color: #fff !important;
    }

    .roadmap-session-page #formCourse > .card-footer .btn-info {
        min-height: 50px;
        border: 1px solid var(--rm-warning) !important;
        background: var(--rm-warning) !important;
        color: #fff !important;
    }

    .roadmap-session-page .btn-primary {
        border-color: var(--rm-primary) !important;
        background: var(--rm-primary) !important;
        color: #fff !important;
    }

    .roadmap-session-page .btn-secondary {
        border-color: #cbd5e1 !important;
        background: #fff !important;
        color: #475569 !important;
    }

    .roadmap-session-page .btn-danger {
        border-color: #dc5d69 !important;
        background: #dc5d69 !important;
        color: #fff !important;
    }

    .roadmap-session-page .btn:hover,
    .roadmap-session-page .btn:focus {
        filter: brightness(.97);
    }

    /* Roadmap listing area */
    .roadmap-session-page #roadmapWiseOffering > .card > .container-fluid.py-4 {
        width: 100%;
        margin: 28px 0 0;
        padding: 0 !important;
    }

    .roadmap-session-page #roadmapWiseOffering > .card > .container-fluid.py-4 > .card {
        border: 0 !important;
        background: transparent;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm {
        margin: 0 0 18px !important;
        border: 0;
        background: transparent;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm > .card-header {
        display: none;
    }

    .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm > .card-body {
        padding: 0;
        background: transparent;
    }

    .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm .row {
        margin: 0 -9px;
    }

    .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm .col-md-3 {
        padding: 0 9px;
    }

    /* DataTables toolbar mirrors Figma search / PDF / Excel row. */
    .roadmap-session-page #roadmapTable_wrapper {
        width: 100%;
        color: var(--rm-text);
        font-family: 'Montserrat', sans-serif;
        overflow-x: auto;
        overflow-y: visible;
    }

    .roadmap-session-page #roadmapTable_wrapper > .d-flex {
        min-width: 100%;
        gap: 14px;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_filter {
        margin: 0;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_filter label {
        margin: 0;
        font-size: 0;
        font-weight: 400;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_filter input {
        width: 386px;
        height: 50px;
        margin: 0 !important;
        padding: 0 42px 0 18px;
        border: 1px solid #fff !important;
        border-radius: 10px !important;
        background: #fff;
        color: var(--rm-text);
        font-size: 14px;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_filter input::placeholder {
        color: #9ca3af;
    }

    .roadmap-session-page #roadmapTable_wrapper .dt-buttons {
        display: flex;
        flex-direction: row-reverse;
        align-items: center;
        gap: 10px;
    }

    .roadmap-session-page #roadmapTable_wrapper .dt-buttons .btn {
        min-width: 82px;
        min-height: 40px;
        margin: 0 !important;
        border-radius: 8px !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .dt-buttons .buttons-pdf {
        border: 1px solid var(--rm-primary) !important;
        background: var(--rm-primary) !important;
        color: #fff !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .dt-buttons .buttons-excel {
        border: 1px solid #252525 !important;
        background: transparent !important;
        color: #252525 !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_length select {
        min-width: 105px;
        height: 32px;
        padding: 4px 28px 4px 12px;
        border: 1px solid #252525;
        border-radius: 999px;
        background: transparent;
        color: #252525;
        font-size: 12px;
    }

    /* Preserve all 18 original columns; allow horizontal scroll rather than dropping data. */
    .roadmap-session-page #roadmapTable {
        width: 100% !important;
        min-width: 1950px;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0;
        border: 0 !important;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
        font-family: 'Montserrat', sans-serif;
        font-size: 12px;
    }

    .roadmap-session-page #roadmapTable thead th {
        padding: 15px 14px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--rm-primary) !important;
        background: var(--rm-primary) !important;
        color: #fff !important;
        font-size: 11px;
        line-height: 1.35;
        font-weight: 600;
        text-align: left !important;
        white-space: nowrap;
        vertical-align: middle;
    }

    .roadmap-session-page #roadmapTable thead th:first-child {
        border-top-left-radius: 10px;
    }

    .roadmap-session-page #roadmapTable thead th:last-child {
        border-top-right-radius: 10px;
    }

    .roadmap-session-page #roadmapTable tbody td {
        padding: 14px !important;
        border: 0 !important;
        border-bottom: 1px solid #edf0f2 !important;
        background: #fff;
        color: var(--rm-text);
        font-size: 12px;
        line-height: 1.4;
        text-align: left !important;
        vertical-align: middle;
        white-space: nowrap;
    }

    .roadmap-session-page #roadmapTable tbody tr:nth-child(even) td {
        background: var(--rm-soft);
    }

    .roadmap-session-page #roadmapTable tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .roadmap-session-page #roadmapTable tbody td:last-child {
        min-width: 96px;
        text-align: center !important;
    }

    .roadmap-session-page #roadmapTable tbody td:last-child .btn {
        min-height: 34px;
        padding: 7px 10px;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_info {
        padding-top: 8px;
        color: #475569;
        font-size: 12px;
    }

    .roadmap-session-page #roadmapTable_wrapper .dataTables_paginate {
        padding-top: 0;
    }

    .roadmap-session-page #roadmapTable_wrapper > .d-flex.mt-3 {
    position: sticky;
    left: 0;
    width: 100%;
    z-index: 2;
}

.roadmap-session-page #roadmapTable_wrapper > .d-flex.mb-3,
.roadmap-session-page #roadmapTable_wrapper > .d-flex.mt-3 {
    position: sticky;
    left: 0;
    width: 100%;
    z-index: 5;
    background: #f2f6f9;
}

    .roadmap-session-page #roadmapTable_wrapper .pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .roadmap-session-page #roadmapTable_wrapper .paginate_button,
    .roadmap-session-page #roadmapTable_wrapper .pagination .page-item {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .pagination .page-link {
        min-width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 8px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: transparent !important;
        color: #252525 !important;
        font-size: 12px;
        box-shadow: none !important;
    }

    .roadmap-session-page #roadmapTable_wrapper .pagination .page-item.active .page-link {
        background: var(--rm-primary) !important;
        color: #fff !important;
    }

    /* Student-wise tab gets the same Figma system while keeping all original controls. */
    .roadmap-session-page #studentWiseOffering #st_formCourse > .card-body > .form-row:first-of-type {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
        margin: 0 !important;
    }

    .roadmap-session-page #studentWiseOffering #st_formCourse > .card-body > .form-row:first-of-type > .form-group {
        width: 100%;
        max-width: none;
        flex: none;
        margin: 0;
        padding: 0;
    }

    .roadmap-session-page #st_roadmap_session {
        width: 100%;
        margin-top: 29px !important;
    }

    .roadmap-session-page #studentWiseOffering #st_formCourse > .form-row {
        margin: 0 !important;
    }

    .roadmap-session-page #sessionTableContainer {
        width: 100%;
        /* padding: 28px 27px !important; */
    }

    .roadmap-session-page #sessionTableContainer h5 {
        margin: 0 0 12px;
        color: var(--rm-primary) !important;
        font-size: 15px;
        font-weight: 600;
    }

    .roadmap-session-page #sessionTableContainer iframe {
        border: 1px solid var(--rm-border) !important;
        border-radius: 10px !important;
        background: #fff;
    }

    .roadmap-session-page #sessionTableContainer .table-responsive,
    .roadmap-session-page #sessionTableContainer .col-md-6 {
        min-width: 0;
    }

    .roadmap-session-page #sessionTableContainer table {
        width: 100%;
        border: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        font-size: 12px;
    }

    .roadmap-session-page #sessionTableContainer table thead th {
        padding: 12px 10px !important;
        border: 0 !important;
        background: var(--rm-primary) !important;
        color: #fff !important;
        font-size: 11px;
        font-weight: 600;
        vertical-align: middle;
    }

    .roadmap-session-page #sessionTableContainer table tbody td,
    .roadmap-session-page #sessionTableContainer table tfoot td {
        padding: 11px 10px !important;
        border: 0 !important;
        border-bottom: 1px solid #edf0f2 !important;
        background: #fff;
        vertical-align: middle;
    }

    .roadmap-session-page #sessionTableContainer table tbody tr:nth-child(even) td {
        background: var(--rm-soft);
    }

    .roadmap-session-page #disclaimerMsg {
        flex: 0 0 100%;
        width: 100%;
        margin: 0 0 8px !important;
        color: #c94d5e !important;
        font-size: 12px;
        text-align: left;
    }

    /* Modal */
    .roadmap-session-page #courseModal {
        z-index: 99999;
    }

    .roadmap-session-page #courseModal .modal-dialog {
        max-width: 620px;
        margin-top: 6vh;
    }

    .roadmap-session-page #courseModal .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 22px 54px rgba(15,23,42,.18);
    }

    .roadmap-session-page #courseModal .modal-header {
        padding: 16px 20px;
        border: 0;
        background: var(--rm-primary);
        color: #fff;
    }

    .roadmap-session-page #courseModal .modal-title {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
    }

    .roadmap-session-page #courseModal .close {
        color: #fff;
        opacity: 1;
        text-shadow: none;
    }

    .roadmap-session-page #courseModal .modal-body {
        padding: 22px;
    }

    .roadmap-session-page #courseModal .modal-footer {
        padding: 14px 22px 20px;
        border-top: 1px solid var(--rm-border);
        gap: 8px;
    }

    @media (max-width: 1199.98px) {
        .roadmap-session-page {
            padding: 24px 20px 36px !important;
        }

        .roadmap-session-page #formCourse > .card-body > .form-row,
        .roadmap-session-page #studentWiseOffering #st_formCourse > .card-body > .form-row:first-of-type {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .roadmap-session-page #studentCount,
        .roadmap-session-page #sectionsDiv {
            flex: 0 0 100%;
            width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .roadmap-session-page {
            padding: 20px 12px 30px !important;
        }

        .roadmap-session-page #quotationTabs {
            gap: 8px !important;
        }

        .roadmap-session-page #quotationTabs .nav-link {
            width: 100%;
        }

        .roadmap-session-page #formCourse > .card-body,
        .roadmap-session-page #st_formCourse > .card-body {
            padding: 22px 16px 14px;
        }

        .roadmap-session-page #formCourse > .card-body > .form-row,
        .roadmap-session-page #studentWiseOffering #st_formCourse > .card-body > .form-row:first-of-type {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .roadmap-session-page #formCourse > .card-footer,
        .roadmap-session-page #st_formCourse > .card-footer {
            padding: 0 16px 22px;
        }

        .roadmap-session-page #formCourse > .card-footer .btn,
        .roadmap-session-page #st_formCourse > .card-footer .btn {
            width: 100%;
            margin: 0 !important;
        }

        .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm .row {
            margin: 0;
        }

        .roadmap-session-page #roadmapWiseOffering .card.mb-3.shadow-sm .col-md-3 {
            padding: 0;
            margin-bottom: 14px;
        }

        .roadmap-session-page #roadmapTable_wrapper .dataTables_filter input {
            width: 100%;
            min-width: 260px;
        }

        .roadmap-session-page #roadmapTable_wrapper > .d-flex {
            flex-wrap: wrap;
            align-items: flex-start !important;
        }

        .roadmap-session-page #sectionsContainer .sectionRow {
            grid-template-columns: 1fr;
        }
    }
    /* Table header text — Title Case only */
.roadmap-session-page #roadmapTable thead th,
.roadmap-session-page #sessionTableContainer table thead th {
    text-transform: capitalize !important;
}











/* =====================================================
   DATATABLE RESPONSIVE DETAILS — FIGMA STYLE
===================================================== */

.roadmap-session-page #roadmapTable tbody tr.child {
    background: #f8faf9 !important;
}

.roadmap-session-page #roadmapTable tbody tr.child > td.child {
    padding: 20px 24px !important;
    background: #f8faf9 !important;
    border-bottom: 1px solid #e5ebe9 !important;
    white-space: normal !important;
    text-align: left !important;
}

/* Details container */
.roadmap-session-page #roadmapTable tbody tr.child ul.dtr-details {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* Each detail item */
.roadmap-session-page #roadmapTable tbody tr.child ul.dtr-details > li {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 5px !important;
    min-width: 0;
    padding: 14px 16px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8e6 !important;
    border-radius: 8px !important;
    box-shadow: none !important;
}

/* Detail heading */
.roadmap-session-page #roadmapTable tbody tr.child .dtr-title {
    display: block !important;
    min-width: 0 !important;
    color: #64748b !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    text-transform: capitalize !important;
}

/* Detail value */
.roadmap-session-page #roadmapTable tbody tr.child .dtr-data {
    display: block !important;
    width: 100% !important;
    color: #1f2937 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    line-height: 1.5 !important;
    overflow-wrap: anywhere;
}

/* Action buttons alignment */
.roadmap-session-page #roadmapTable tbody tr.child .dtr-data .btn {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    min-height: 38px;
    margin: 2px 4px 2px 0 !important;
    border: 0 !important;
    border-radius: 8px !important;
}

/* Edit button */
.roadmap-session-page #roadmapTable tbody tr.child .edit-btn {
    background: #17675f !important;
    color: #ffffff !important;
}

/* Delete button */
.roadmap-session-page #roadmapTable tbody tr.child .delete-btn {
    background: #ef5b68 !important;
    color: #ffffff !important;
}

/* Tablet */
@media (max-width: 991px) {
    .roadmap-session-page #roadmapTable tbody tr.child ul.dtr-details {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* Mobile */
@media (max-width: 575px) {
    .roadmap-session-page #roadmapTable tbody tr.child > td.child {
        padding: 14px !important;
    }

    .roadmap-session-page #roadmapTable tbody tr.child ul.dtr-details {
        grid-template-columns: 1fr;
    }
}


/* Action box — label left, buttons right */
.roadmap-session-page
#roadmapTable
tbody tr.child
ul.dtr-details > li:last-child {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
}

/* Action label */
.roadmap-session-page
#roadmapTable
tbody tr.child
ul.dtr-details > li:last-child .dtr-title {
    flex: 0 0 auto;
    margin: 0 !important;
}

/* Buttons container */
.roadmap-session-page
#roadmapTable
tbody tr.child
ul.dtr-details > li:last-child .dtr-data {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
    width: auto !important;
    margin-left: auto !important;
}

/* Remove unnecessary button margins */
.roadmap-session-page
#roadmapTable
tbody tr.child
ul.dtr-details > li:last-child .btn {
    margin: 0 !important;
}

  </style>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid py-4 roadmap-session-page">
    <div class="roadmap-page-heading" aria-hidden="true">
        <h2>Road Map Session</h2>
        <p>Map Your Journey</p>
    </div>
    
    <ul class="nav nav-pills gap-2" id="quotationTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 py-2" id="roadmapWiseOffering-tab" data-toggle="tab" href="#roadmapWiseOffering" role="tab">Roadmap Wise Course Offering</a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 py-2" id="studentWiseOffering-tab" data-toggle="tab" href="#studentWiseOffering" role="tab">Student Wise Course Offering</a>
        </li>
    </ul>


    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="roadmapWiseOffering" role="tabpanel">
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0 text-dark">Roadmap Wise Course Offering</h4>
                </div>                
                <form id="formCourse">
                    <!-- Card Body -->
                    <div class="card-body">            
                        <input type="hidden" id="formId" name="formId">
                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label for="roadmap_session">Roadmap Session</label>
                                <select id="roadmap_session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="session">Active Session</label>
                                <select id="session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}" >
                                        
                                            {{ $row->sess_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                         
                            <div class="form-group col-md-4">
                                <label for="schools">Schools</label>
                                <select id="schools" name="schools" class="form-control select2">
                                    <option value="">Select Degree</option>
                                    @foreach($activeSchools as $row)
                                        <option value="{{ $row->school_id }}">{{ $row->school_code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="departments">Departments</label>
                                <select id="departments" name="departments" class="form-control select2">
                                    <option value="">Select Department</option>
                                    @foreach($activeDeparments as $row)
                                        <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="programs">Program</label>
                                <select id="programs" name="programs" class="form-control select2">
                                    <option value="">Select Program</option>
                                    @foreach($activePrograms as $row)
                                        <option value="{{ $row->prog_id }}">{{ $row->prog_name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="form-group col-md-2">
                                <label for="shift">Shift</label>
                                <select id="shift" class="form-control">
                                    <option selected>Select Shift</option>
                                    <option value="1">Morning</option>
                                    <option value="2">Afternoon</option>
                                    <option value="3">Evening</option>
                                    <option value="4">Weekend</option>                                    
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="semester">Semester</label>
                                <select id="semester" class="form-control">
                                    <option selected>Select Semester</option>
                                    <option value="1">I</option>
                                    <option value="2">II</option>
                                    <option value="3">III</option>
                                    <option value="4">IV</option>
                                    <option value="5">V</option>
                                    <option value="6">VI</option>
                                    <option value="7">VII</option>
                                    <option value="8">VIII</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="course">Course</label>
                                <select id="course" name="course[]" class="form-control select2" multiple>
                                    
                                    <option value="">Select Course</option>

                                    <!-- Static Electives -->
                                    <optgroup label="Electives">
                                        <option value="123">Elective 1</option>
                                        <option value="456">Elective 2</option>
                                        <option value="789">Elective 3</option>
                                        <option value="555">Elective 4</option>
                                        <option value="666">Elective 5</option>
                                        <option value="777">Elective 6</option>
                                    </optgroup>

                                    <!-- Static Specializations -->
                                    <optgroup label="Specializations">
                                        <option value="111">Specialization 1</option>
                                        <option value="222">Specialization 2</option>
                                        <option value="333">Specialization 3</option>
                                        <option value="444">Specialization 4</option>
                                    </optgroup>

                                    <optgroup label="Degree Electives">
                                        <option value="888">Degree Elective 1</option>
                                        <option value="118">Degree Elective 2</option>
                                        <option value="112">Degree Elective 3</option>
                                        <option value="113">Degree Elective 4</option>
                                        <option value="114">Degree Elective 5</option>
                                        <option value="115">Degree Elective 6</option>
                                        <option value="116">Degree Elective 7</option>
                                        <option value="117">Degree Elective 8</option>
                                    </optgroup>

                                    <optgroup label="Minor">
                                        <option value="119">Minor 1</option>
                                        <option value="120">Minor 2</option>
                                        <option value="121">Minor 3</option>
                                        <option value="122">Minor 4</option>                                   
                                    </optgroup>
                                    
                                    <optgroup label="SVAD Courses">
                                        <option value="124">Major Studio I</option>
                                        <option value="125">Major Studio II</option>
                                        <option value="126">Major Studio III</option>
                                        <option value="127">Major Studio IV</option>        
                                        <option value="128">Research Methods & Academic writing for Art and Design III or Studio/Theory Elective</option>
                                        <option value="129">Integrated VA/TFA/VCD Studio</option>
                                        <option value="130">Fieldwork/Internship for Professional Practice</option>
                                        <option value="131">Independent Study</option>       
                                        <option value="132">Current Discourse in VA/VCD/TFA</option>   
                                        <option value="133">Professional Practice in VA/VCD/TFA</option>   
                                        <option value="134">Artistic Development and Psychology
                                                OR
                                                Diversity in Art Education
                                                OR
                                                Tools and Technologies in Art Education
                                        </option>     
                                        <option value="135">Foundational Theory Elective
                                                Research Methods in Art and Education
                                                OR
                                                General Education (3Cr.)
                                                Academic Writing and Critical Reading
                                                OR
                                                Visual Thinking: Data Visualization & Archives
                                        </option>  
                                        <option value="136">Foundational Theory Elective
                                                History of Visual Arts
                                                OR
                                                History of Communication Design
                                                OR
                                                History of Textile, Fashion & Accessories Design
                                                OR
                                                History of Trans-disciplinary
                                        </option>                    
                                    </optgroup>

                                    <!-- Dynamic Courses from DB -->
                                    <optgroup label="Courses">
                                        @foreach($masterCourses as $row)
                                        <option value="{{ $row->course_id }}">
                                            {{ $row->course_code }} - {{ $row->course_cd_name }}
                                        </option>
                                        @endforeach
                                    </optgroup>

                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="course_offering_type">Course Offering Type</label>
                                <select id="course_offering_type" class="form-control select2">
                                    <option value="">Select Course Offering Type</option>                       
                                    <option value="1">Regular Course</option>
                                    <option value="2">Project</option>
                                    <option value="3">Thesis</option>
                                    <option value="4">Internship Course</option>                            
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="course_capacity">Class Capacity</label>
                                <input type="number" id="course_capacity" name="course_capacity" class="form-control" data-options="required:true,min:1,validType:'range[0,70]'"/>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label for="co_total_classes">Total Classes</label>
                                <input type="number" id="co_total_classes" name="co_total_classes" class="form-control" min="0" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="co_req_classes">Required Classes %</label>
                                <div class="input-group">
                                    <input type="number" id="co_req_classes" name="co_req_classes" class="form-control" min="0" max="100" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="co_req_present">Required Classes</label>
                                <input type="number" id="co_req_present" name="co_req_present" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label for="courseType">Course Type</label>
                                <select id="courseType" class="form-control">
                                    <option value="" selected>Select Course Type</option>
                                    <option value="1">Compulsory</option>
                                    <option value="2">Elective</option>
                                    <option value="9">Major Specific Studio Electives</option>
                                    <option value="11">SVAD Studio and Theory Elective</option>
                                    <option value="12">Other Discipline Elective</option>
                                    <option value="13">SVAD Studio Elective</option>
                                    <option value="14">SVAD Theory Elective</option>
                                    <option value="15">SVAD Studio / Theory Elective (Art and Design)</option>
                                    <option value="16">SVAD Studio / Theory Elective (Other Disciplines)</option>
                                    <option value="17">Major Specific Theory Elective</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="theoryHours">Theory Credit Hours</label>
                                <input type="number" id="theoryHours" class="form-control" min="0">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="co_custom">Have Timetable</label>
                                <select id="co_custom" class="form-control select2">
                                                    
                                    <option value="1" selected>Yes</option>
                                    <option value="2">No</option>
                                                                
                                </select>
                            </div>
                        </div>

                        <div id="studentCount" class="mt-3">
                            <label for="studentCountValue">Total Students</label>
                            <input type="text" id="studentCountValue" name="student_count" class="form-control" readonly>
                        </div>

                        <div id="sectionsDiv" class="mt-3">
                            <label>Sections</label>
                            <div id="sectionsContainer"></div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="addSectionBtn">+ Add Section</button>
                        </div>

                        <div id="error-messages" class="alert alert-danger mt-3" style="display:none;"></div>
                        <div id="success-message" class="alert alert-success mt-3" style="display:none;"></div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer text-center">
                        <button type="button" onclick="sbtBtn()" id="submitBtn" class="btn btn-success mr-2">Submit</button>
                        <button type="button" onclick="courseOfferedInsertion()" id="submitBtn" class="btn btn-info mr-2">Move Courses to Course Offering Page</button>
                        <button type="button" id="newEntryBtn" class="btn btn-secondary" style="display:none;" onclick="resetToNewEntry()">New Entry</button>
                    </div>
                </form>  
                <div class="container-fluid py-4">
                    <div class="card shadow-sm border-0">
                        <!-- FILTER DROPDOWNS (Add these BEFORE the table) -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Filters</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-3">
                                        <label class="form-label">Active Session</label>
                                        <select id="filterActiveSession" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Roadmap Session</label>
                                        <select id="filterRoadmapSession" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Department</label>
                                        <select id="filterDepartment" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Program</label>
                                        <select id="filterProgram" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <table class="table assets-table align-middle" id="roadmapTable">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>RoadMap Session</th>
                                    <th>School</th>
                                    <th>Department</th>
                                    <th>Program</th>
                                    <th>Shift</th>
                                    <th>Semester</th>
                                    <th>Course</th>
                                    <th>Course Type</th>
                                    <th>Theory CH</th>
                                    <th>Offered Sections</th>
                                    <th>Course Offering Type</th>
                                    <th>Class Capacity</th>
                                    <th>Total Classes</th>
                                    <th>Required Classes %</th>
                                    <th>Required Classes</th>
                                    <th>Have Timetable</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                            <tbody class="align-middle text-center">
                                <!-- Data will be appended here -->
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="studentWiseOffering" role="tabpanel">
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0 text-dark">Student Wise Course Offering</h4>
                </div>                
                <form id="st_formCourse">
                    <!-- Card Body -->
                    <div class="card-body">            
                        <input type="hidden" id="st_formId" name="st_formId">
                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label for="session">Active Session</label>
                                <select id="st_session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}" >                                
                                            {{ $row->sess_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                           
                            
                            <div class="form-group col-md-4">
                                <label for="studentEnrolmentNO">Enrollment No</label>
                                <select id="studentEnrolmentNO" class="form-control select2">
                                    <option value="">Select Student</option>
                                    @foreach($resactiveStudents as $row)
                                        <option value="{{ $row->sai_enrollment_no }}">{{ $row->sai_enrollment_no }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <div class="form-group col-md-4">
                                <label for="roadmap_session">Roadmap Session</label>
                                <select id="st_roadmap_session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>         
                             -->    
                            <div class="form-group col-md-4">                            
                                <button type="button" id="st_roadmap_session" onclick="loadsession()" style="margin-top: 28px;" class="btn btn-primary">
                                    Load Roadmap Session
                                </button>
                            </div>                              
                        </div>
                        
                        
                        <div id="error-messages" class="alert alert-danger mt-3" style="display:none;"></div>
                        <div id="success-message" class="alert alert-success mt-3" style="display:none;"></div>
                    </div>                 

                    <div class="form-row mb-3">
                        <div class="container-fluid py-4" id="sessionTableContainer">
                                                        
                        </div>   
                    </div>  



                    <!-- Card Footer -->
                    <div class="card-footer text-center">
                         <span id="disclaimerMsg" style="color:red; font-weight:bold; display:block; margin-bottom:8px;display:none">
                            This is the main roadmap for this session. 
                            To create a separate roadmap for this student, please press Submit.
                        </span>
                        <button type="button" onclick="st_sbtBtn()" id="submitBtn" class="btn btn-success mr-2">Submit</button>
                        <button type="button" style="display:none" class="btn btn-primary" id="showBtn" data-toggle="modal" data-target="#courseModal">
                            Add new Roadmap Session Student Wise
                        </button>
                        <button type="button" id="st_newEntryBtn" class="btn btn-secondary" style="display:none;" onclick="resetToNewEntry()">New Entry</button>
                    </div>
                </form>           
                    
            </div>
            <!-- Button to Open Modal -->        

            <!-- Modal -->
            <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="courseModalLabel">Add New Roadmap Session Student Wise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="courseForm">
                            <div class="form-group">
                                <label for="activeSession">Active Session</label>
                                <input type="hidden" name="studentRollNo" id="studentRollNo" >
                                <select id="active_session_new" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="courseName">Course Name</label>
                                <select id="course_new" class="form-control select2">
                                    <option value="">Select Course</option>

                                    <!-- Static Electives -->
                                    <optgroup label="Electives">
                                        <option value="123">Elective 1</option>
                                        <option value="456">Elective 2</option>
                                        <option value="789">Elective 3</option>
                                        <option value="555">Elective 4</option>
                                        <option value="666">Elective 5</option>
                                        <option value="777">Elective 6</option>
                                    </optgroup>

                                    <!-- Static Specializations -->
                                    <optgroup label="Specializations">
                                        <option value="111">Specialization 1</option>
                                        <option value="222">Specialization 2</option>
                                        <option value="333">Specialization 3</option>
                                        <option value="444">Specialization 4</option>
                                    </optgroup>

                                    <optgroup label="Degree Electives">
                                        <option value="888">Degree Elective 1</option>
                                        <option value="118">Degree Elective 2</option>
                                        <option value="112">Degree Elective 3</option>
                                        <option value="113">Degree Elective 4</option>
                                        <option value="114">Degree Elective 5</option>
                                        <option value="115">Degree Elective 6</option>
                                        <option value="116">Degree Elective 7</option>
                                        <option value="117">Degree Elective 8</option>
                                    </optgroup>

                                    <optgroup label="Minor">
                                        <option value="119">Minor 1</option>
                                        <option value="120">Minor 2</option>
                                        <option value="121">Minor 3</option>
                                        <option value="122">Minor 4</option>                                   
                                    </optgroup>

                                    <optgroup label="SVAD Courses">
                                        <option value="124">Major Studio I</option>
                                        <option value="125">Major Studio II</option>
                                        <option value="126">Major Studio III</option>
                                        <option value="127">Major Studio IV</option>        
                                        <option value="128">Research Methods & Academic writing for Art and Design III or Studio/Theory Elective</option>
                                        <option value="129">Integrated VA/TFA/VCD Studio</option>
                                        <option value="130">Fieldwork/Internship for Professional Practice</option>
                                        <option value="131">Independent Study</option>       
                                        <option value="132">Current Discourse in VA/VCD/TFA</option>   
                                        <option value="133">Professional Practice in VA/VCD/TFA</option>   
                                        <option value="134">Artistic Development and Psychology
                                                OR
                                                Diversity in Art Education
                                                OR
                                                Tools and Technologies in Art Education
                                        </option>     
                                        <option value="135">Foundational Theory Elective
                                                Research Methods in Art and Education
                                                OR
                                                General Education (3Cr.)
                                                Academic Writing and Critical Reading
                                                OR
                                                Visual Thinking: Data Visualization & Archives
                                        </option>  
                                        <option value="136">Foundational Theory Elective
                                                History of Visual Arts
                                                OR
                                                History of Communication Design
                                                OR
                                                History of Textile, Fashion & Accessories Design
                                                OR
                                                History of Trans-disciplinary
                                        </option>                    
                                    </optgroup>

                                    <!-- Dynamic Courses from DB -->
                                    <optgroup label="Courses">
                                        @foreach($masterCourses as $row)
                                        <option value="{{ $row->course_id }}">
                                            {{ $row->course_code }} - {{ $row->course_cd_name }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    <option value="">Select Course</option>
                                    @foreach($masterCourses as $row)
                                        <option value="{{ $row->course_id }}">{{ $row->course_code }} - {{ $row->course_cd_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <select id="semester_new" class="form-control">
                                    <option selected>Select Semester</option>
                                    <option value="1">I</option>
                                    <option value="2">II</option>
                                    <option value="3">III</option>
                                    <option value="4">IV</option>
                                    <option value="5">V</option>
                                    <option value="6">VI</option>
                                    <option value="7">VII</option>
                                    <option value="8">VIII</option>
                                    <option value="9">IX</option>
                                    <option value="10">X</option>
                                    <option value="11">XI</option>
                                    <option value="12">XII</option>
                                    <option value="13">XIII</option>
                                    <option value="14">XIV</option>
                                    <option value="15">XV</option>
                                    <option value="16">XVI</option>
                                    <option value="17">XVII</option>
                                    <option value="18">XVIII</option>
                                </select>
                            </div>                            
                            <div class="form-group">
                                <label for="course_offering_type">Course Offering Type</label>
                                <select id="course_offering_type_new" class="form-control select2">
                                    <option value="">Select Course Offering Type</option>                       
                                    <option value="1">Regular Course</option>
                                    <option value="2">Project</option>
                                    <option value="3">Thesis</option>
                                    <option value="4">Internship Course</option>                            
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courseType">Course Type</label>
                                <select id="courseType_new" class="form-control">
                                    <option value="" selected>Select Course Type</option>
                                    <option value="1">Compulsory</option>
                                    <option value="2">Elective</option>
                                    <option value="9">Major Specific Studio Electives</option>
                                    <option value="11">SVAD Studio and Theory Elective</option>
                                    <option value="12">Other Discipline Elective</option>
                                    <option value="13">SVAD Studio Elective</option>
                                    <option value="14">SVAD Theory Elective</option>
                                    <option value="15">SVAD Studio / Theory Elective (Art and Design)</option>
                                    <option value="16">SVAD Studio / Theory Elective (Other Disciplines)</option>
                                    <option value="17">Major Specific Theory Elective</option>
                                </select>
                            </div>  

                            <div class="form-group">
                                <label for="theoryHours">Theory Credit Hours</label>
                                <input type="number" id="theoryHours_new" class="form-control" min="0">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveCourseBtn">Save</button>
                    </div>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</div>   
@stop
@section('javascript')
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">

function courseOfferedInsertion() {
    //  showToast('error', 'This feature is currently under development. Please proceed according to the roadmap.');
    // return false;
    // Disable button to prevent multiple clicks
    let sessions = $('#sessions').val();
    let schoolID = $('#schools').val();
    $('#submitBtn').prop('disabled', true);

    $.ajax({
        url: '/courses-offered/insert', // Laravel route
        type: 'POST',
        data: {
            active_session_id: sessions, // pass dynamically if needed
            schoolID: schoolID, // pass dynamically if needed
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
        },
        success: function(response) {
            showToast('success', response.message);
            // alert(response.message);
            $('#submitBtn').prop('disabled', false);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Something went wrong!');
            $('#submitBtn').prop('disabled', false);
        }
    });
}


$('#courseModal').on('show.bs.modal', function () {
    // Get value from #studentEnrolmentNO
    let enrolNo = $('#studentEnrolmentNO').val();
    // Set it into the Active Session field (or any other field)
    $('#studentRollNo').val(enrolNo);
});
$('#saveCourseBtn').on('click', function() {
    let studentRollNo          = $('#studentRollNo').val();
    let activeSession          = $('#active_session_new').val();
   
    let courseId               = $('#course_new').val();
    let semester               = $('#semester_new').val();
    let courseOfferingType     = $('#course_offering_type_new').val();
    let courseType_new         = $('#courseType_new').val();
    let theoryHours_new        = $('#theoryHours_new').val();

    // Example: Send via AJAX
    $.ajax({
        url: '/save-course', // your route
        type: 'POST',
        data: {
            student_roll_no: studentRollNo,
            active_session: activeSession,           
            course_id: courseId,
            semester: semester,
            course_offering_type: courseOfferingType,
            courseType_new: courseType_new,
            theoryHours_new: theoryHours_new,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showToast('success', 'Course saved successfully!');
           
            $('#courseModal').modal('hide');
            loadsession()
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Error saving course');
        }
    });
});

function loadsession(){ 
    $('#showBtn').hide();
    $('#disclaimerMsg').show();
    selectedCourses = [];
    let sessionId = $('#st_session').val(); 
    let enrollNo = $('#studentEnrolmentNO').val(); // if your input id is different, change it

      // ✅ FIXED LINK
    let documentLink = `/assets/cms/reports/weak_portal_academic_history_report.php?sel_enrollment_no=${enrollNo}&pt_sai_enroll_no=${enrollNo}`;

    if (sessionId == "") {
        $('#sessionTableContainer').html('');
        return;
    }

    $.ajax({
        url: "/get-session-data",
        type: "GET",
        data: {
            session_id: sessionId,
            enrollNo: enrollNo
        },
        success: function (response) {
            console.log(response);
            if (response.data.length === 0) {
                $('#sessionTableContainer').html('<p class="text-danger">No records found.</p>');
                return;
            }

            let html = '';   // ✅ FIX: added

            html += `
                <div class="row mb-4">
                    <!-- Academic History -->
                    <div class="col-md-6">
                        <h5 class="text-primary"><b>Academic History</b></h5>
                        <iframe 
                            src="${documentLink}" 
                            width="100%" 
                            height="500"
                            style="border:1px solid #ccc;border-radius:6px;">
                        </iframe>
                    </div>
                    <!-- Enrolled Courses -->
                    <div class="col-md-6">
                        <h5 class="text-primary"><b>Current Session Enrolled Courses</b></h5>
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Course Name</th>
                                    <th>Cr. Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    response.studentEnrollment && response.studentEnrollment.length > 0
                                        ? response.studentEnrollment.map((course, index) => `
                                            <tr>
                                                <td class="text-center">${index + 1}</td>
                                                <td>${course.course_name}</td>
                                                <td class="text-center">${course.sc_pc_course_cr}</td>
                                            </tr>
                                        `).join('')
                                        : `<tr><td colspan="3" class="text-center">No record found</td></tr>`
                                }
                            </tbody>
                                <!-- ✅ TOTAL CREDIT HOURS ROW -->
                                ${
                                    response.studentEnrollment && response.studentEnrollment.length > 0
                                    ? `
                                    <tfoot>
                                        <tr class="fw-bold text-center">
                                            <td colspan="2">Total Cr Hr</td>
                                            <td>
                                                ${
                                                    response.studentEnrollment.reduce((total, course) => {
                                                        return total + (parseFloat(course.sc_pc_course_cr) || 0);
                                                    }, 0)
                                                }
                                            </td>
                                        </tr>
                                    </tfoot>
                                    `
                                    : ''
                                }
                        </table>
                    </div>
                </div>
                <div class="row">
            `;


            

           
            let currentSess = null;
            let counter = 1;
            let hasExistingCourse = false; // flag to check if any row has rwf_sai_enrollment_no

            $.each(response.data, function (i, row) {
                if (row.rwf_sai_enrollment_no) {
                    hasExistingCourse = true;
                }
                // When session changes → start new table block
                if (currentSess !== row.sess_id) {

                    // Close previous table (except first)
                    if (currentSess !== null) {
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    // Reset counter
                    counter = 1;

                    // Update session ID
                    currentSess = row.sess_id;

                    // Open new column and table
                    html += `
                        <div class="col-md-6 mb-6">
                            <h5 class="text-primary"><b>${row.sess_name} (${row.sess_id})(${response.studentSessId})</b></h5>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Course</th>                               
                                        <th>Grades</th>  
                                        <th class='text-center'>
                                            Add Course <input type='checkbox' class='select-all-checkbox'>
                                        </th>
                                        ${ row.sess_id == response.studentSessId ? '' : `
                                        <th class='text-center'>
                                            Delete Course <input type='checkbox' class='delete-all-checkbox'>
                                        </th>
                                         `}
                                    </tr>
                                </thead>

                                <tbody>
                    `;
                }
                
                // Add row inside table
                html += `
                        <tr>
                            <td>${counter}</td>
                            <td>
                                ${
                                    row.course_cd_name && row.course_cd_name !== ""
                                        ? row.course_cd_name
                                        : (
                                            row.course_id == 123 ? "Elective 1" :
                                            row.course_id == 456 ? "Elective 2" :
                                            row.course_id == 789 ? "Elective 3" :
                                            row.course_id == 555 ? "Elective 4" :
                                            row.course_id == 666 ? "Elective 5" :
                                            row.course_id == 777 ? "Elective 6" :
                                            row.course_id == 888 ? "Degree Elective 1" :
                                            row.course_id == 118 ? "Degree Elective 2" :
                                            row.course_id == 112 ? "Degree Elective 3" :
                                            row.course_id == 113 ? "Degree Elective 4" :
                                            row.course_id == 114 ? "Degree Elective 5" :
                                            row.course_id == 115 ? "Degree Elective 6" :
                                            row.course_id == 116 ? "Degree Elective 7" :
                                            row.course_id == 117 ? "Degree Elective 8" :
                                            row.course_id == 111 ? "Specialization 1" :
                                            row.course_id == 222 ? "Specialization 2" :
                                            row.course_id == 333 ? "Specialization 3" :
                                            row.course_id == 444 ? "Specialization 4" :
                                            row.course_id == 119 ? "Minor 1" :
                                            row.course_id == 120 ? "Minor 2" :
                                            row.course_id == 121 ? "Minor 3" :
                                            row.course_id == 122 ? "Minor 4" :
                                            row.course_id == 124 ? "Major Studio I" :
                                            row.course_id == 125 ? "Major Studio II" :
                                            row.course_id == 126 ? "Major Studio III" :
                                            row.course_id == 127 ? "Major Studio IV" :
                                            row.course_id == 128 ? "Research Methods & Academic Writing for Art and Design III / Studio-Theory Elective" :
                                            row.course_id == 129 ? "Integrated VA / TFA / VCD Studio" :
                                            row.course_id == 130 ? "Fieldwork / Internship for Professional Practice" :
                                            row.course_id == 131 ? "Independent Study" :
                                            row.course_id == 132 ? "Current Discourse in VA / VCD / TFA" :
                                            row.course_id == 133 ? "Professional Practice in VA / VCD / TFA" :
                                            row.course_id == 134 ? "Artistic Development & Psychology / Diversity in Art Education / Tools & Technologies in Art Education" :
                                            row.course_id == 135 ? "Foundational Theory Elective (Research Methods in Art & Education / General Education / Academic Writing / Visual Thinking)" :
                                            row.course_id == 136 ? "Foundational Theory Elective (History of Visual Arts / History of Communication Design / History of TFA / History of Trans-disciplinary)" :
                                            "N/A"
                                        )
                                }
                                (${row.course_id})
                                (${row.rwf_sai_enrollment_no ?? "-"})
                            </td>
                            <td>${row.grades ?? 'N/A'}</td>
                            <td class="text-center" ${ row.rwf_sai_enrollment_no ? 'style="display:none;"' : '' }>
                                <!-- Checkbox for selecting NEW courses -->
                                <input type="checkbox" class="session-checkbox"
                                    data-sessid="${row.sess_id}" 
                                    data-course="${row.course_cd_name}" 
                                    data-course-id="${row.course_id}" 
                                    data-student-session="${response.studentSessId}" 
                                    data-student-school="${response.studentSchoolId}"  
                                    data-student-dept="${response.studentDeptId}" 
                                    data-student-prog="${response.studentProgId}"                                 
                                >
                            </td>
                            ${ row.sess_id == response.studentSessId ? '' : `
                                <td class="text-center" ${ row.rwf_sai_enrollment_no ? '' : 'style="display:none;"' }>
                                    <!-- Checkbox for deleting EXISTING courses -->
                                    <input type="checkbox" class="delete-checkbox"
                                        data-rwf-id="${row.id}"
                                        data-enroll="${row.rwf_sai_enrollment_no}">
                                </td>
                            `}
                            ${ row.sess_id == response.studentSessId ? '' : `
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success add-row-btn">+</button>
                                </td>
                            `}
                        </tr>`;


                counter++;
            });

            // Close final table & row
            html += `
                        </tbody>
                    </table>
                    `;

                    if (hasExistingCourse) {
                        $('#showBtn').show();
                         $('#disclaimerMsg').hide();
                        
                        html += `
                        <button type="button" class="btn btn-danger btn-sm delete-selected-btn mt-2">
                            Delete Selected
                        </button>
                        `;
                    }
                    // $('#disclaimerMsg').show();

            html += `</div></div>`;

            $('#sessionTableContainer').html(html);
            updateHeaderVisibility();
            $('#sessionTableContainer').find('.select-all-checkbox').prop('checked', true).trigger('change');

        }
    });
}

// function updateHeaderVisibility() {
//     $('#sessionTableContainer table').each(function () {
//         let table = $(this);

//         // Add Course column
//         let addColumnVisible = false;
//         table.find('tbody tr').each(function () {
//             if ($(this).find('td:eq(2)').is(':visible')) {
//                 addColumnVisible = true;
//                 return false; // break loop
//             }
//         });
//         table.find('thead th:eq(2)').toggle(addColumnVisible);

//         // Delete Course column
//         let deleteColumnVisible = false;
//         table.find('tbody tr').each(function () {
//             if ($(this).find('td:eq(3)').is(':visible')) {
//                 deleteColumnVisible = true;
//                 return false; // break loop
//             }
//         });
//         table.find('thead th:eq(3)').toggle(deleteColumnVisible);
//     });
// }

function updateHeaderVisibility() {
    $('#sessionTableContainer table').each(function () {
        let table = $(this);

        // Add Course column
        let addColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(4)').is(':visible')) { 
                addColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(4)').toggle(addColumnVisible);

        // Delete Course column
        let deleteColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(5)').is(':visible')) {
                deleteColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(5)').toggle(deleteColumnVisible);

        // ⭐ NEW — Modify Grade column
        let modifyColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(3)').is(':visible')) { 
                modifyColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(3)').toggle(modifyColumnVisible);

    });
}


let deleteSelected = [];

$(document).on("change", ".delete-checkbox", function () {

    let rwfId = $(this).data("rwf-id");
    let enroll = $(this).data("enroll");

    if ($(this).is(":checked")) {
        deleteSelected.push({
            id: rwfId,
            enroll_no: enroll
        });
    } else {
        deleteSelected = deleteSelected.filter(item => item.id != rwfId);
    }

    console.log("Delete Array:", deleteSelected);
});


$(document).on("click", ".delete-selected-btn", function () {

    if (deleteSelected.length === 0) {
        alert("Please select courses to delete.");
        return;
    }

    if (!confirm("Are you sure you want to delete selected courses?")) return;

    $.ajax({
        url: "/delete-multiple-courses",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            rows: deleteSelected
        },
        success: function(response) {
            // alert("Selected courses deleted!");
            showToast('error', 'Selected courses deleted!');

            // Remove from UI
            deleteSelected.forEach(item => {
                $(`.delete-checkbox[data-rwf-id="${item.id}"]`).closest("tr").remove();
            });

            deleteSelected = [];
            loadsession();
        }
    });
});

$(document).on('change', '.select-all-checkbox', function() {
    let isChecked = $(this).is(':checked');
    // Find all checkboxes in the same table
    $(this).closest('table').find('.session-checkbox').prop('checked', isChecked).trigger('change');
});

$(document).on('change', '.delete-all-checkbox', function() {
    let isChecked = $(this).is(':checked');
    // Find all checkboxes in the same table
    $(this).closest('table').find('.delete-checkbox').prop('checked', isChecked).trigger('change');
});




let selectedCourses = [];

// Add new row
$(document).on('click', '.add-row-btn', function () {

    let table = $(this).closest('table');
    let tableBody = table.find('tbody');

    // Detect which header columns exist
    let hasAddColumn = table.find('thead th:contains("Add Course")').length > 0;
    let hasDeleteColumn = table.find('thead th:contains("Delete Course")').length > 0;

    $.ajax({
        url: "/get-courses-enrollment",
        type: "GET",
        success: function (courses) {          

            let options = `<option value="">Select Course</option>`;
            // Dynamic courses
            courses.forEach(function(course) {
                let name = course.course_cd_name;
                let label = "";

                if (!name) {
                    label = "-";
                }
                else {
                    label = name;
                }
                options += `<option value="${course.course_id}">${label}</option>`;
            });

            // Add static Electives
            options += `
                <optgroup label="Electives">
                    <option value="123">Elective 1</option>
                    <option value="456">Elective 2</option>
                    <option value="789">Elective 3</option>
                    <option value="555">Elective 4</option>
                    <option value="666">Elective 5</option>
                    <option value="777">Elective 6</option>
                </optgroup>
            `;

            // Add static Degree Electives
            options += `
                <optgroup label="Degree Electives">
                    <option value="888">Degree Elective 1</option>
                    <option value="118">Degree Elective 2</option>
                    <option value="112">Degree Elective 3</option>
                    <option value="113">Degree Elective 4</option>
                    <option value="114">Degree Elective 5</option>
                    <option value="115">Degree Elective 6</option>
                    <option value="116">Degree Elective 7</option>
                    <option value="117">Degree Elective 8</option>
                    
                </optgroup>
            `;


            // Add static Specializations
            options += `
                <optgroup label="Specializations">
                    <option value="111">Specialization 1</option>
                    <option value="222">Specialization 2</option>
                    <option value="333">Specialization 3</option>
                    <option value="444">Specialization 4</option>
                </optgroup>
            `;

             // Add static Minors
            options += `
                <optgroup label="Minor">
                    <option value="119">Minor 1</option>
                    <option value="120">Minor 2</option>
                    <option value="121">Minor 3</option>
                    <option value="122">Minor 4</option>                                   
                </optgroup>
            `;

            options += `
                <optgroup label="SVAD Courses"> 
                    <option value="124">Major Studio I</option> 
                    <option value="125">Major Studio II</option> 
                    <option value="126">Major Studio III</option> 
                    <option value="127">Major Studio IV</option> 
                    <option value="128">Research Methods & Academic writing for Art and Design III or Studio/Theory Elective</option> 
                    <option value="129">Integrated VA/TFA/VCD Studio</option>
                    <option value="130">Fieldwork/Internship for Professional Practice</option> 
                    <option value="131">Independent Study</option>
                    <option value="132">Current Discourse in VA/VCD/TFA</option> 
                    <option value="133">Professional Practice in VA/VCD/TFA</option>
                    <option value="134">Artistic Development and Psychology OR Diversity in Art Education OR Tools and Technologies in Art Education </option>
                    <option value="135">Foundational Theory Elective Research Methods in Art and Education OR General Education (3Cr.) Academic Writing and Critical Reading OR Visual Thinking: Data Visualization & Archives </option> <option value="136">Foundational Theory Elective History of Visual Arts OR History of Communication Design OR History of Textile, Fashion & Accessories Design OR History of Trans-disciplinary </option> 
                </optgroup>
            `;         


            

            $("#course").html(options);


            // Build action columns
            let actionColumns = "";

            // ADD COURSE column exists → add semester dropdown
            if (hasAddColumn) {
                actionColumns += `
                    <td>
                        <select class="form-control semester-select">
                            <option value="">Select Semester</option>
                            <option value="1">I</option>
                            <option value="2">II</option>
                            <option value="3">III</option>
                            <option value="4">IV</option>
                            <option value="5">V</option>
                            <option value="6">VI</option>
                            <option value="7">VII</option>
                            <option value="8">VIII</option>
                            <option value="9">IX</option>
                            <option value="10">X</option>
                            <option value="11">XI</option>
                            <option value="12">XII</option>
                            <option value="13">XIII</option>
                            <option value="14">XIV</option>
                            <option value="15">XV</option>
                            <option value="16">XVI</option>
                            <option value="17">XVII</option>
                            <option value="18">XVIII</option>
                        </select>
                    </td>
                `;
            }

            // DELETE COURSE column exists → add remove button
            if (hasDeleteColumn) {
                actionColumns += `
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn">−</button>
                    </td>
                `;
            }

            // Create final row
            let newRow = `
                <tr class="new-row">
                    <td></td>
                    <td>
                        <select class="form-control select2dynamic">
                            ${options}
                        </select>
                    </td>
                    ${actionColumns}
                </tr>
            `;

            tableBody.append(newRow);

            // Activate Select2
            tableBody.find(".select2dynamic").last().select2({
                placeholder: "Select Course",
                allowClear: true,
                width: '100%'
            });

            renumberRows(tableBody);
        }
    });
});

// Remove row
$(document).on('click', '.remove-row-btn', function () {
    let tableBody = $(this).closest('tbody');
    let row = $(this).closest('tr');

    // Get course_id and semester (or any other identifiers)
    let courseId = row.find('.select2dynamic').val();
    let courseName = row.find('.select2dynamic option:selected').text();
    let semester = row.find('.semester-select').val();

    // Find session_id from heading
    let sessionText = $(this).closest(".col-md-6").find("h5 b").text(); 
    let sessionId = sessionText.match(/\((\d+)\)/)[1]; // extract sess_id

    // Remove from array
    selectedCourses = selectedCourses.filter(item => {
        return !(item.sess_id == sessionId && item.course_id == courseId && item.semester == semester);
    });

    console.log("After remove:", selectedCourses);

    // Remove row from DOM
    row.remove();

    // Re-number rows
    renumberRows(tableBody);
});


function getRowKey(tr) {
    // Use tr.index() or a data attribute to uniquely identify the row
    return tr.index(); // unique per row
}

function updateRowData(tr) {
    let courseId = tr.find(".select2dynamic").val();
    let courseName = tr.find(".select2dynamic option:selected").text();
    let semester = tr.find(".semester-select").val();

    if (!courseId || !semester) {
        // If either is empty, remove the row from the array
        selectedCourses = selectedCourses.filter(item => item.row_key !== getRowKey(tr));
        return;
    }

    let sessionText = tr.closest(".col-md-6").find("h5 b").text(); 
    let sessId = sessionText.match(/\((\d+)\)/)[1];

    let checkbox = tr.closest("table").find(".session-checkbox").first();
    let studentSess = checkbox.data('student-session');
    let studentSchool = checkbox.data('student-school');
    let studentDept = checkbox.data('student-dept');
    let studentProg = checkbox.data('student-prog');

    let rowKey = getRowKey(tr);

    // Remove any previous entry for this row
    selectedCourses = selectedCourses.filter(item => item.row_key !== rowKey);

    // Push latest selected course & semester for this row
    selectedCourses.push({
        row_key: rowKey, // unique per row
        sess_id: sessId,
        course_id: courseId,
        course_name: courseName,
        semester: semester,
        grade: null,
        student_sess: studentSess,
        student_school: studentSchool,
        student_dept: studentDept,
        student_prog: studentProg
    });

    console.log("Selected Courses:", selectedCourses);
}


// Trigger on course change
$(document).on('change', '.select2dynamic', function() {
    
    updateRowData($(this).closest("tr"));
});

// Trigger on semester change
$(document).on('change', '.semester-select', function() {
   
    updateRowData($(this).closest("tr"));
});


// Handle session checkboxes
$(document).on("change", ".session-checkbox", function () {

    let tr = $(this).closest("tr");

    let courseId = $(this).data("course-id");
    let sessId = $(this).data("sessid");
    let studentSess = $(this).data("student-session");
    let studentSchool = $(this).data("student-school");
    let studentDept = $(this).data("student-dept");
    let studentProg = $(this).data("student-prog");

    // Determine course name EXACTLY as in loadsession()
    let courseName =
        $(this).data("course") && $(this).data("course") !== ""
        ? $(this).data("course")
        : (
            courseId == 123 ? "Elective 1" :
            courseId == 456 ? "Elective 2" :
            courseId == 789 ? "Elective 3" :
            courseId == 555 ? "Elective 4" :
            courseId == 666 ? "Elective 5" :
            courseId == 777 ? "Elective 6" :
            courseId == 888 ? "Degree Elective 1" :
            courseId == 118 ? "Degree Elective 2" :
            courseId == 112 ? "Degree Elective 3" :
            courseId == 113 ? "Degree Elective 4" :
            courseId == 114 ? "Degree Elective 5" :
            courseId == 115 ? "Degree Elective 6" :
            courseId == 116 ? "Degree Elective 7" :
            courseId == 117 ? "Degree Elective 8" :
            courseId == 111 ? "Specialization 1" :
            courseId == 222 ? "Specialization 2" :
            courseId == 333 ? "Specialization 3" :
            courseId == 444 ? "Specialization 4" :
            courseId == 119 ? "Minor 1" :
            courseId == 120 ? "Minor 2" :
            courseId == 121 ? "Minor 3" :
            courseId == 122 ? "Minor 4" :
            courseId == 124 ? "Major Studio I" :
            courseId == 125 ? "Major Studio II" :
            courseId == 126 ? "Major Studio III" :
            courseId == 127 ? "Major Studio IV" :
            courseId == 128 ? "Research Methods & Academic Writing for Art and Design III / Studio-Theory Elective" :
            courseId == 129 ? "Integrated VA / TFA / VCD Studio" :
            courseId == 130 ? "Fieldwork / Internship for Professional Practice" :
            courseId == 131 ? "Independent Study" :
            courseId == 132 ? "Current Discourse in VA / VCD / TFA" :
            courseId == 133 ? "Professional Practice in VA / VCD / TFA" :
            courseId == 134 ? "Artistic Development & Psychology / Diversity in Art Education / Tools & Technologies in Art Education" :
            courseId == 135 ? "Foundational Theory Elective (Research Methods / GE / Academic Writing / Visual Thinking)" :
            courseId == 136 ? "Foundational Theory Elective (History of Visual Arts / CD / TFA / Trans-disciplinary)" :
            "N/A"
        );

    // If unchecked → remove
    if (!$(this).is(":checked")) {
        selectedCourses = selectedCourses.filter(item => item.course_id != courseId);
        return;
    }

    // Remove duplicates
    selectedCourses = selectedCourses.filter(item => item.course_id != courseId);

    // Push NEW record
    selectedCourses.push({
        sess_id: sessId,
        course_id: courseId,
        course_name: courseName,
        semester: null,
        grade: null,
        student_sess: studentSess,
        student_school: studentSchool,
        student_dept: studentDept,
        student_prog: studentProg
    });

    console.log("selectedCourses:", selectedCourses);
});


// Renumber rows
function renumberRows(tbody) {
    tbody.find('tr').each(function (i) {
        $(this).find('td:first').text(i + 1);
    });
}


$(document).ready(function() {
    function calculateRequiredClasses() {
        let totalClasses = parseFloat($('#co_total_classes').val()) || 0;
        let requiredPercent = parseFloat($('#co_req_classes').val()) || 0;

        // calculate required classes = total * percent / 100
        let requiredClasses = Math.round((totalClasses * requiredPercent) / 100);
        $('#co_req_present').val(requiredClasses);
    }

    // trigger when total classes or percentage changes
    $('#co_total_classes, #co_req_classes').on('input change', calculateRequiredClasses);
});

$(document).ready(function() {



    $('#assetList').DataTable({
        "pageLength": 10,
        "ordering": false, // ✅ Disable all sorting
        "responsive": true,
        "language": {
            "search": "Filter records:",
            "emptyTable": "No Gate Passes Found"
        },
        dom:
            "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center gap-3'B l>f>" +
            "rt" +
            "<'d-flex justify-content-between align-items-center mt-3'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm mr-2',
                title: 'Asset List',
                orientation: 'landscape',
                pageSize: { width: 800, height: 500 },
                exportOptions: { columns: ':not(:last-child)' },
                
            }
        ]
    });
});


$(document).ready(function() {
    $('#schools').select2({
      placeholder: "Select School",
      allowClear: true
    });

     $('#studentEnrolmentNO').select2({
      placeholder: "Select Students",
      allowClear: true,
      width: '100%'
    });

    

    $('#course_new').select2({
      placeholder: "Select Course",
      allowClear: true
    });

    $('#st_schools').select2({
      placeholder: "Select School",
      allowClear: true
    });
     $('#course').select2({
      placeholder: "Select Course",
      allowClear: true
    });
    loadRoadmapData();

    // Add Section button click
    $('#addSectionBtn').on('click', function () {
        var totalStudents = parseInt($('#studentCountValue').val()) || 0;

        if (totalStudents === 0) {
            alert("Please load the total student count first.");
            return;
        }

        var currentTotal = 0;
        $('#sectionsContainer').find('input[name="section_students[]"]').each(function () {
            currentTotal += parseInt($(this).val()) || 0;
        });

        if (currentTotal >= totalStudents) {
            alert("You cannot exceed the total student count.");
            return;
        }

        var sectionHtml = `
            <div class="row mb-2 sectionRow">
                <div class="col-md-5">
                    <select name="section_names[]" class="form-control" required>
                        <option value="">Select Section</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                        <option value="K">K</option>
                        <option value="L">L</option>
                        <option value="M">M</option>
                        <option value="N">N</option>
                        <option value="O">O</option>
                        <option value="P">P</option>
                        <option value="Q">Q</option>
                        <option value="R">R</option>
                        <option value="S">S</option>
                        <option value="T">T</option>
                        <option value="U">U</option>
                        <option value="V">V</option>
                        <option value="W">W</option>
                        <option value="X">X</option>
                        <option value="Y">Y</option>
                        <option value="Z">Z</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" name="section_students[]" class="form-control" placeholder="No. of Students" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger removeSectionBtn">X</button>
                </div>
            </div>
        `;
        $('#sectionsContainer').append(sectionHtml);
    });

    

    // Remove section
    $(document).on('click', '.removeSectionBtn', function () {
        $(this).closest('.sectionRow').remove();
    });

    // Example: When AJAX loads student count
    function loadStudentCount(count) {
        $('#studentCountValue').val(count);
        $('#studentCount').show();
    }


       // run when either session select changes
    $('#session, #roadmap_session').on('change', function () {


        $('#departments').val('').trigger('change'); 
        
        let curText = $('#session option:selected').text().trim();
        let roadText = $('#roadmap_session option:selected').text().trim();

        // ensure both selected and not the placeholder
        if (!curText || curText.toLowerCase().includes('select') || !roadText || roadText.toLowerCase().includes('select')) {
            resetSemesterOptions();
            return;
        }

        let cur = parseSession(curText);       // { term: 'Fall'|'Spring', year: 2025 }
        let road = parseSession(roadText);
        if (!cur || !road) {
            console.warn('Session text not in expected format (e.g. "Fall 2025").');
            resetSemesterOptions();
            return;
        }

        // compute numeric indexes and semester number (inclusive)
        let curIndex  = cur.year * 2 + (cur.term.toLowerCase() === 'fall' ? 1 : 0);
        let roadIndex = road.year * 2 + (road.term.toLowerCase() === 'fall' ? 1 : 0);
        let diff = curIndex - roadIndex;

        if (diff < 0) {
            // roadmap is in future compared to current session — clear/disable
            alert('Roadmap session is later than selected session. Please choose valid sessions.');
            resetSemesterOptions();
            return;
        }

        let semesterNo = diff + 1;           // inclusive count
        semesterNo = Math.max(1, semesterNo); // min 1
        semesterNo = Math.min(8, semesterNo); // clamp to max 8 (adjust if you allow >8)

        // disable all options except the calculated one, then set value
        $('#semester option').each(function () {
            // keep the option enabled if it matches semesterNo, otherwise disable
            $(this).prop('disabled', $(this).val() !== String(semesterNo));
        });

        $('#semester').val(String(semesterNo));
    });

    function parseSession(text) {
        // Accepts "Fall 2025", "Spring 2024", "Fall-2025" etc.
        text = text.replace('-', ' ').replace(/\s+/g, ' ').trim();
        let parts = text.split(' ');
        if (parts.length < 2) return null;
        let term = parts[0];
        let year = parseInt(parts[1], 10);
        if (!term || isNaN(year)) return null;
        return { term: term, year: year };
    }

    function resetSemesterOptions() {
        // Enable all and reset selection to placeholder (or keep current)
        $('#semester option').prop('disabled', false);
        $('#semester').val($('#semester option:first').val()); // assumes first is "Select Semester"
    }
});


$('#programs').on('change', function () {
    var progId = $(this).val();
    var rdmapsession = $('#roadmap_session').val();
    // alert(progId+'--'+rdmapsession);
    // return false;
    if (progId) {
        $.ajax({
            url: '/get-roadmap-sessions-students',
            type: 'GET',
            data: {
                program_id: progId,
                roadmap_session: rdmapsession,
                _token: '{{ csrf_token() }}' // CSRF token for Laravel
            },
            dataType: 'json',
            success: function (data) {
                $('#studentCountValue').val(data.count);
                // $('#studentCount').show();

                // $('#studentCount').html(
                //     '<div class="alert alert-info">Total Students: <strong>' + data.count + '</strong></div>'
                // );
            }
        });
    } else {
        $('#roadmap_session').empty().append('<option value="">Select Session</option>');
    }
});
$('#schools').on('change', function () {
    let schoolId = $('#schools').val();

    $('#departments').html('<option value="">Loading...</option>');

    if (schoolId) {
        $.ajax({
            url: '{{ url('/get_departments_by_school') }}',
            type: 'GET',
            data: { school_id: schoolId },
            success: function (data) {
                $('#departments').html('<option value="">Select Department</option>');
                $.each(data, function (key, dept) {
                    $('#departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch departments.');
                $('#departments').html('<option value="">Select Department</option>');
            }
        });
    } else {
        $('#departments').html('<option value="">Select Department</option>');
    }
});

$('#st_schools').on('change', function () {
    let schoolId = $('#st_schools').val();

    $('#st_departments').html('<option value="">Loading...</option>');

    if (schoolId) {
        $.ajax({
            url: '{{ url('/get_departments_by_school') }}',
            type: 'GET',
            data: { school_id: schoolId },
            success: function (data) {
                $('#st_departments').html('<option value="">Select Department</option>');
                $.each(data, function (key, dept) {
                    $('#st_departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch departments.');
                $('#st_departments').html('<option value="">Select Department</option>');
            }
        });
    } else {
        $('#st_departments').html('<option value="">Select Department</option>');
    }
});

$('#departments').on('change', function () {
    let deptId = $('#departments').val();
     var rdmapsession = $('#roadmap_session').val();

    $('#programs').html('<option value="">Loading...</option>');

    if (deptId) {
        $.ajax({
            url: '{{ url('/get_programs_by_school') }}',
        
            type: 'GET',
            data: { dept_id: deptId,rdmapsession: rdmapsession },
            success: function (data) {
                $('#programs').html('<option value="">Select Program</option>');
                $.each(data, function (key, prog) {
                    $('#programs').append(`<option value="${prog.prog_id}">${prog.prog_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch programs.');
                $('#programs').html('<option value="">Select Programs</option>');
            }
        });
    } else {
        $('#programs').html('<option value="">Select Programs</option>');
    }
});
function st_sbtBtn() {
  
    let sessionId = $('#st_session').val();
    let enrollNo = $('#studentEnrolmentNO').val();

    if (selectedCourses.length === 0) {
        alert("Please add at least one course.");
        return;
    }

    $.ajax({
        url: "/submit-session-courses",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            session_id: sessionId,
            enroll_no: enrollNo,
            courses: selectedCourses
        },
        success: function (res) {
            
            showToast('success', 'Saved Successfully');
            $('#disclaimerMsg').hide();
            selectedCourses = [];
            loadsession();
            console.log(res);
        }
    });
}


function sbtBtn(e) {
    event.preventDefault();
    let sectionNames = $("[name='section_names[]']").map(function() { 
                            return $(this).val(); 
                        }).get();
    let sectionStudents = $("input[name='section_students[]']").map(function(){ return $(this).val(); }).get();
    let data = {     
        formId: $('#formId').val(),
        session_id: $('#session').val(),
        roadmap_session_id: $('#roadmap_session').val(),
        school_id: $('#schools').val(),
        department_id: $('#departments').val(),
        program_id: $('#programs').val(),
        semester_id: $('#semester').val(),
        course_id: $('#course').val(),
        course_type: $('#courseType').val(),
        theory_credit_hours: $('#theoryHours').val(),
        course_offering_type: $('#course_offering_type').val(),
        course_capacity: $('#course_capacity').val(),
        co_total_classes: $('#co_total_classes').val(),
        co_req_classes: $('#co_req_classes').val(),
        co_req_present: $('#co_req_present').val(),
        studentCountValue: $('#studentCountValue').val(),
        co_custom: $('#co_custom').val(),
        section_names: sectionNames,
        section_students: sectionStudents,
        shift_id: $('#shift').val(),
        // practical_credit_hours: $('#practicalHours').val(),
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        url: '{{ route("roadmap.store") }}',
        method: 'POST',
        data: data,
        success: function (response) {
            // Clear previous messages
            $('#error-messages').hide().html('');
            $('#success-message').hide().html('');

            // Show success toast
            showToast('success', 'Roadmap data submitted successfully!');

            // Reset form
            $('#formCourse')[0].reset();
            $('#formCourse').find('select').val('').trigger('change.select2');

            // Refresh table
            loadRoadmapData();

            // Optional: if you have any dynamic selects (like Select2)
            // $('.select2').val(null).trigger('change');
        },

        error: function (xhr) {
            let responseText = xhr.responseText || "";

            if (xhr.status === 422) { // Laravel validation error
                let errors = xhr.responseJSON.errors;

                // Get first error field and message
                let firstField = Object.keys(errors)[0];
                let firstMessage = errors[firstField][0];

                // Show toast instead of inline message
                showToast('error', firstMessage);

                // Focus the corresponding input/select field
                let fieldElement = $('[name="' + firstField + '"]');
                if (fieldElement.length) {
                    fieldElement.focus();
                }

            } else if (responseText.includes("ORA-00001")) {
                showToast('error', 'Course already exists. Please choose another.');
            } else {
                showToast('error', 'Something went wrong. Please try again.');
            }
        }

    });
}


function loadRoadmapData() {
 

    
    if ($.fn.DataTable.isDataTable('#roadmapTable')) {
        $('#roadmapTable').DataTable().clear().destroy();
    }
    $('#roadmapTable').DataTable({
        processing: true,        
        responsive: true,
        pageLength: 11,
         lengthMenu: [
            [10, 20, 30, 50, -1], 
            [10, 20, 30, 50, "All"]
        ],
       
        ajax: {
            url: "{{ route('roadmap.data') }}", // Define this route in web.php
            type: 'GET',
           
        },
        columns: [
            { data: 'active_session_name' },
            { 
                data: 'roadmap_session_name',
                render: function(data, type, row) {
                    return data ? data : 'N/A';
                }
            },
            { data: 'school_code' },
            { data: 'dept_name' },
            { data: 'prog_name' },
            { 
                data: 'shift_id', 
                title: 'Shift',
                render: function(data, type, row) {
                    const shiftMap = {
                        1: 'Morning',
                        2: 'Afternoon',
                        3: 'Evening',
                        4: 'Weekend'
                    };
                    return shiftMap[data] || 'N/A';
                }
            },
            { data: 'semester_id' },
            {
                data: 'course_cd_name',
                render: function (data, type, row) {

                    // If normal course name exists → show it
                    if (data !== null && data !== "") {
                        return data;
                    }

                    // Otherwise use static mapping based on course_id
                    let id = row.course_id;   // your dropdown value

                    if (id == 123) return "Elective 1";
                    if (id == 456) return "Elective 2";
                    if (id == 789) return "Elective 3";
                    if (id == 555) return "Elective 4";
                    if (id == 666) return "Elective 5";
                    if (id == 777) return "Elective 6";

                    if (id == 888) return "Degree Elective 1";
                    if (id == 118) return "Degree Elective 2";
                    if (id == 112) return "Degree Elective 3";
                    if (id == 113) return "Degree Elective 4";
                    if (id == 114) return "Degree Elective 5";
                    if (id == 115) return "Degree Elective 6";
                    if (id == 116) return "Degree Elective 7";
                    if (id == 117) return "Degree Elective 8";

                    if (id == 111) return "Specialization 1";
                    if (id == 222) return "Specialization 2";
                    if (id == 333) return "Specialization 3";
                    if (id == 444) return "Specialization 4";

                    if (id == 119) return "Minor 1";
                    if (id == 120) return "Minor 2";
                    if (id == 121) return "Minor 3";
                    if (id == 122) return "Minor 4";

                    if (id == 124) return "Major Studio I";
                    if (id == 125) return "Major Studio II";
                    if (id == 126) return "Major Studio III";
                    if (id == 127) return "Major Studio IV";
                    if (id == 128) return "Research Methods & Academic Writing for Art and Design III / Studio-Theory Elective";
                    if (id == 129) return "Integrated VA / TFA / VCD Studio";
                    if (id == 130) return "Fieldwork / Internship for Professional Practice";
                    if (id == 131) return "Independent Study";
                    if (id == 132) return "Current Discourse in VA / VCD / TFA";
                    if (id == 133) return "Professional Practice in VA / VCD / TFA";
                    if (id == 134) return "Artistic Development & Psychology / Diversity in Art Education / Tools & Technologies in Art Education";
                    if (id == 135) return "Foundational Theory Elective (Research Methods / GE / Academic Writing / Visual Thinking)";
                    if (id == 136) return "Foundational Theory Elective (History of Visual Arts / CD / TFA / Trans-disciplinary)";

                    // Fallback
                    return "N/A";
                }
            },
            { data: 'ct_name' },
            { 
                data: 'theory_credit_hours',
                render: function(data, type, row) {
                    return data ? data : 'N/A';
                }
            },
            { data: 'sections_summary' },
            // New fields
            { 
                data: 'course_offering_type',
                render: function(data, type, row) {
                    let val = Number(data); // cast to number
                    switch(val) {
                        case 1: return 'Regular Course';
                        case 2: return 'Project';
                        case 3: return 'Thesis';
                        case 4: return 'Internship Course';
                        default: return 'N/A';
                    }
                }
            },
            { data: 'course_capacity' },
            { data: 'co_total_classes' },
            { data: 'co_req_classes', render: function(data){ return data ? data + '%' : 'N/A'; } },
            { data: 'co_req_present' },
            { 
                data: 'co_custom',
                render: function(data) {
                    return data == 1 ? 'Yes' : (data == 2 ? 'No' : 'N/A');
                }
            },
            {
                data: 'id',
                render: function (data, type, row) {
                     return `
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" data-action="edit" title="Edit" style="
    margin-right: 10px !important;
">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-action="delete" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ],
        order: [[16, 'desc']],
         initComplete: function () {
            let api = this.api();

            // Build dropdown options by reading unique values
            function fillFilter(columnIndex, selectId) {
                let column = api.column(columnIndex);

                let uniqueValues = new Set();
                column.data().each(function(value) {
                    if (value !== null && value !== "") uniqueValues.add(value);
                });

                uniqueValues = [...uniqueValues].sort();
                uniqueValues.forEach(function(v) {
                    $(`${selectId}`).append(`<option value="${v}">${v}</option>`);
                });

                // On dropdown change, filter column
                $(selectId).on('change', function () {
                    let val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });
            }

            // Filters:
            fillFilter(0, '#filterActiveSession');      // active_session_name
            fillFilter(1, '#filterRoadmapSession');     // roadmap_session_name
            fillFilter(3, '#filterDepartment');     // roadmap_session_name
            fillFilter(4, '#filterProgram');            // prog_name
        },
        dom:
            "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center gap-3'B l>f>" +
            "rt" +
            "<'d-flex justify-content-between align-items-center mt-3'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm mr-2',
                title: 'Roadmap Course Offering',
                orientation: 'landscape',
                pageSize: { width: 1200, height: 500 },
                exportOptions: { columns: ':not(:last-child)' },
                
            }
        ]
    });
}

$(document).on('click', '.edit-btn, .delete-btn', function () {
    let id = $(this).data('id');
    let action = $(this).data('action');

    if (action === 'edit') {
  
        $('#session').focus();
        $.get('/roadmap-offering/' + id + '/edit', function (response) {
            $('#formId').val(response.id);
            $('#session').val(response.active_session_id);
            $('#roadmap_session').val(response.rdmap_session_id);
            $('#schools').val(response.school_id).trigger('change');

            let departmentId = response.department_id;
            let programId = response.program_id;
            let courseId = response.course_id;

            // Step 2: Load departments
            $.ajax({
                url: '{{ url('/get_departments_by_school') }}',
                type: 'GET',
                data: { school_id: response.school_id },
                success: function (departments) {
                    $('#departments').html('<option value="">Select Department</option>');
                    $.each(departments, function (key, dept) {
                        $('#departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                    });

                    $('#departments').val(departmentId); // select correct department



                    // Step 3: Load programs after department is set
                    $.ajax({
                        url: '{{ url('/get_programs_by_school') }}',
                        type: 'GET',
                        data: {
                            dept_id: departmentId,
                            rdmapsession: response.rdmap_session_id
                        },
                        success: function (programs) {
                            $('#programs').html('<option value="">Select Program</option>');
                            $.each(programs, function (key, prog) {
                                $('#programs').append(`<option value="${prog.prog_id}">${prog.prog_name}</option>`);
                            });

                            $('#programs').val(programId); // select correct program
                            $('#course').val(courseId);    // select correct course if already loaded
                            getStudentCount(programId, response.rdmap_session_id);
                        }
                    });
                }
            });
            $('#course').val(courseId).trigger('change');
            $('#semester').val(response.semester_id);
            $('#courseType').val(response.course_type).trigger('change');
            $('#theoryHours').val(response.theory_credit_hours);
            // $('#practicalHours').val(response.practical_credit_hours);

            $('#course_offering_type').val(response.course_offering_type).trigger('change');
            $('#course_capacity').val(response.course_capacity);
            $('#co_total_classes').val(response.co_total_classes);
            $('#co_req_classes').val(response.co_req_classes);
            $('#co_req_present').val(response.co_req_present);
            $('#co_custom').val(response.co_custom).trigger('change');

            // Clear old sections before adding new ones
            $('#sectionsContainer').html('');         

            // Append sections from response.sections (array of {name, count})
            if (response.sections && response.sections.length > 0) {
                response.sections.forEach(function (sec) {
                    let options = '<option value="">Select Section</option>';
                    for (let i = 65; i <= 90; i++) { // ASCII codes for A-Z
                        let letter = String.fromCharCode(i);
                        let selected = (letter === sec.course_section) ? 'selected' : '';
                        options += `<option value="${letter}" ${selected}>${letter}</option>`;
                    }

                    let sectionHtml = `
                        <div class="row mb-2 sectionRow">
                            <div class="col-md-5">
                                <select name="section_names[]" class="form-control" required>
                                    ${options}
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="section_students[]" value="${sec.course_section_count}" class="form-control" placeholder="No. of Students" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger removeSectionBtn">X</button>
                            </div>
                        </div>
                    `;
                    $('#sectionsContainer').append(sectionHtml);
                });
            }

            $('#submitBtn').text('Update').data('update-id', id);
            $('#newEntryBtn').show();
        });
    } 
    else if (action === 'delete') {
        if (confirm('Are you sure you want to delete this record?')) {
            $.post('/roadmap-offering-delete/' + id, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function () {
                // alert('Deleted successfully!');
                $('#roadmapTable').DataTable().ajax.reload();
            })
            .fail(function () {
                alert('Error deleting record.');
            });
        }
    }
});

function getStudentCount(progId, rdmapsession) {
    if (progId) {
        $.ajax({
            url: '/get-roadmap-sessions-students',
            type: 'GET',
            data: {
                program_id: progId,
                roadmap_session: rdmapsession,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function (data) {
                $('#studentCountValue').val(data.count);
            }
        });
    } else {
        $('#studentCountValue').val('');
    }
}


function resetToNewEntry() {
    // Clear the form
    $('#formCourse')[0].reset();
    // Clear any saved ID and revert button to Submit
    $('#submitBtn')
        .text('Submit')
        .removeData('update-id');
    // Hide New Entry button
    $('#newEntryBtn').hide();
    $('#schools').val('').trigger('change');
    $('#departments').html('<option value="">Select Department</option>');
    $('#programs').html('<option value="">Select Program</option>');
    $('#course').val('').trigger('change');
    
     // Clear section rows except the first one
    $('.sectionRow').not(':first').remove();

    // Reset values in the first section row
    $('.sectionRow:first select').val('');
    $('.sectionRow:first input').val('');

    $('.sectionRow').hide();

    
}





</script>
@stop