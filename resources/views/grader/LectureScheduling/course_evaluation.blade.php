@extends('layouts.app',['pageTitle' => 'Course Grading'])
@section('content')

{{-- @dd("123"); --}}
<style>
    .blink_me {
    animation: blinker 1s linear infinite;
    color:red;
    }

    @keyframes blinker {
    50% { opacity: 0; }
    }
	    .disabled {
		  pointer-events: none;
		  cursor: default;
		  opacity: 0.6;
	}
    .blinking-text {
    animation: blink-animation 1s steps(5, start) infinite;
    }

    @keyframes blink-animation {
        50% {
            opacity: 0;
        }
    }

    /* Make accordion take full available width */
#accordionExample,
#accordionExample.accordion,
.bs-example,
.card-body.scroller,
.page-body,
.tab-content,
#grading {
    width: 100% !important;
    max-width: 100% !important;
    display: block !important;
    flex: 1 1 100% !important;
}

/* Every accordion card should also be full width */
#accordionExample > .card {
    width: 100% !important;
    margin: 0 0 15px 0 !important;
}

/* Remove any shrinking caused by floats */
#accordionExample .card-header,
#accordionExample .card-body {
    width: 100% !important;
    box-sizing: border-box;
}


















/* Header */
.grading-card-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:24px;
    width:100%;
}

/* Left side */
.grading-card-info{
    flex:1;
    min-width:0;
}

/* Right side (all direct children except info) */
.grading-card-header > *:not(.grading-card-info){
    margin-left:16px;
}

/* Make every button wrapper inline */
.grading-card-header > div:not(.grading-card-info){
    display:inline-flex;
    align-items:center;
    float:none !important;
}

/* Buttons */
.grading-card-header button{
    white-space:nowrap;
}

/* Keep notice below course name */
.grading-important-notice{
    display:block;
    margin-top:12px;
}


.grading-card-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    flex-wrap:wrap;
    gap:20px;
}

.grading-card-info{
    flex:1 1 auto;
    min-width:0;
}

.grading-card-title{
    margin-bottom:5px;
}

.grading-card-actions{
    display:flex;
    align-items:center;
    gap:12px;
    margin-left:auto;
}

.grading-important-notice{
    display:block;
    margin-top:12px;
    line-height:1.6;
}
.profile-overview-title
 {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}


/* ==========================================
   MODERN BOOTSTRAP ACCORDION
========================================== */

/* Accordion */
#accordionExample{
    border:0;
}

/* Card */
#accordionExample .card{
    border:0 !important;
    border-radius:10px;
    overflow:hidden;
    margin-bottom:18px;
    background:#fff;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

/* Header */
#accordionExample .card-header{
    background:#1F6F68 !important;
    border:0 !important;
    padding:16px 20px !important;
    min-height:70px;

    display:flex !important;
    align-items:center;
    justify-content:space-between;
}

/* Remove inline floats */
#accordionExample .card-header > div{
    float:none !important;
}

#accordionExample .card-header > div:first-child{
    display:flex;
    align-items:center;
    flex:1;
}

#accordionExample .card-header > div:last-child{
    margin-left:auto;
}

/* Title */
#accordionExample .card-header i{
    color:#fff !important;
    font-size:20px !important;
    font-style:normal !important;
    font-weight:500;
}

/* Add New Button */
#accordionExample .btn-outline-info{
    background:#fff !important;
    color:#1F6F68 !important;
    border:0 !important;
    border-radius:6px;
    padding:9px 18px;
    font-weight:600;
    box-shadow:none;
}

#accordionExample .btn-outline-info:hover{
    background:#F4F4F4 !important;
}

/* Body */
#accordionExample .card-body{
    padding:0 !important;
    background:#fff;
}

/* Rows */
#accordionExample .card-body > div{
    padding:16px 20px;
    border-bottom:1px solid #ECECEC;
}

#accordionExample .card-body > div:last-child{
    border-bottom:none;
}

/* Assessment Title */
#accordionExample strong{
    font-size:18px;
    font-weight:600;
    color:#333;
}

/* Students */
#accordionExample .text-muted{
    color:#8A8A8A !important;
}

/* Action Buttons */
#accordionExample .btn-outline-success,
#accordionExample .btn-outline-secondary{
    border-radius:6px;
    min-width:170px;
    height:44px;
}

/* Remove HR */
#accordionExample hr{
    display:none;
}



/* ==========================================
   FIXED MODERN BOOTSTRAP ACCORDION
========================================== */

#accordionExample {
    width: 100%;
    border: 0;
}

/* Each accordion section */
#accordionExample > .card {
    width: 100% !important;
    margin: 0 0 16px !important;
    overflow: hidden;
    border: 0 !important;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

/* Accordion header */
#accordionExample > .card > .card-header {
    position: relative;
    z-index: 2;
    width: 100% !important;
    min-height: 58px;
    margin: 0 !important;
    padding: 10px 16px !important;

    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 16px;

    border: 0 !important;
    border-radius: 0;
    background: #1f756d !important;
    box-sizing: border-box;
}

/* Override inline floats */
#accordionExample > .card > .card-header > div {
    float: none !important;
    margin: 0 !important;
}

#accordionExample > .card > .card-header > div:first-child {
    display: flex;
    align-items: center;
    flex: 1 1 auto;
    min-width: 0;
}

#accordionExample > .card > .card-header > div:last-child {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex: 0 0 auto;
    margin-left: auto !important;
}

/* Accordion heading only */
#accordionExample > .card > .card-header > div:first-child > i {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #fff !important;
    font-size: 18px !important;
    font-style: normal;
    font-weight: 500;
    line-height: 1.3 !important;
    cursor: pointer;
}

/* Add New wrapper */
#accordionExample .card-header span {
    margin-left: 0 !important;
}

/* Add New button */
#accordionExample .card-header .btn-outline-info {
    width: auto !important;
    min-width: 128px;
    height: 38px;
    margin: 0 !important;
    padding: 7px 14px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;

    border: 1px solid #fff !important;
    border-radius: 6px;
    background: #fff !important;
    color: #176d63 !important;
    font-size: 15px;
    font-weight: 500;
    line-height: 1;
    box-shadow: none !important;
}

/* Important: restore button icon/text color */
#accordionExample .card-header .btn-outline-info i {
    color: #176d63 !important;
    font-size: 15px !important;
    font-style: normal;
    font-weight: 500;
    line-height: 1 !important;
}

#accordionExample .card-header .btn-outline-info:hover {
    background: #f3f8f7 !important;
    color: #125b54 !important;
}

/* Remove standalone separator */
#accordionExample > .card > hr {
    display: none !important;
}

/* Collapsible content */
#accordionExample > .card > div[id^="collapse"] {
    position: relative;
    z-index: 1;
    width: 100%;
    clear: both;
    margin: 0 !important;
    background: #fff;
}

/* Body */
#accordionExample > .card > div[id^="collapse"] > .card-body {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #fff;
    box-sizing: border-box;
}

/* Assessment rows */
#accordionExample > .card > div[id^="collapse"] > .card-body > div {
    position: relative;
    width: 100% !important;
    min-height: 88px;
    margin: 0 !important;
    padding: 18px 18px !important;

    display: block;
    float: none !important;
    clear: both;

    border-bottom: 1px solid #e8ecec !important;
    background: #fff;
    box-sizing: border-box;
}

#accordionExample > .card > div[id^="collapse"] > .card-body > div:last-child {
    border-bottom: 0 !important;
}

/* Assessment numbering */
#accordionExample .card-body b > span {
    margin-right: 8px !important;
    color: #2448d8 !important;
}

/* Assessment title */
#accordionExample .card-body strong {
    color: #34383c;
    font-size: 18px !important;
    font-weight: 600;
}

/* Student status */
#accordionExample .card-body .fs-16 {
    margin-left: 14px !important;
    color: #697386;
    font-size: 15px !important;
}

/* Right-side action links */
#accordionExample .card-body > div > a {
    margin-left: 10px;
}

/* Action buttons */
#accordionExample .card-body .btn-outline-success,
#accordionExample .card-body .btn-outline-secondary {
    width: 230px !important;
    min-height: 44px;
    border-radius: 6px;
    box-shadow: none;
}

/* Tablet/mobile */
@media (max-width: 767px) {
    #accordionExample > .card > .card-header {
        align-items: stretch;
        flex-direction: column;
        gap: 10px;
    }

    #accordionExample > .card > .card-header > div:last-child {
        width: 100%;
        margin-left: 0 !important;
    }

    #accordionExample .card-header .btn-outline-info {
        width: 100% !important;
    }

    #accordionExample > .card > div[id^="collapse"] > .card-body > div {
        min-height: auto;
        padding: 16px !important;
    }

    #accordionExample .card-body > div > a,
    #accordionExample .card-body > div > button {
        display: block;
        float: none !important;
        width: 100%;
        margin: 10px 0 0 !important;
    }

    #accordionExample .card-body .btn-outline-success,
    #accordionExample .card-body .btn-outline-secondary {
        width: 100% !important;
    }
}










/* =========================================
   COURSE GRADING MODALS
========================================= */

#uploadFileModel,
#smallmodal,
#courseMarksModal,
#parentchildModal,
#gradeFinalizationConfirmModal {
    padding-right: 0 !important;
}

/* Modal position and width */
#uploadFileModel .modal-dialog,
#smallmodal .modal-dialog,
#courseMarksModal .modal-dialog,
#parentchildModal .modal-dialog,
#gradeFinalizationConfirmModal .modal-dialog {
    width: calc(100% - 30px);
    max-width: 620px;
    margin: 40px auto;
}

/* Modal container */
#uploadFileModel .modal-content,
#smallmodal .modal-content,
#courseMarksModal .modal-content,
#parentchildModal .modal-content,
#gradeFinalizationConfirmModal .modal-content {
    overflow: hidden;
    border: 0;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.22);
}

/* Header */
#uploadFileModel .modal-header,
#smallmodal .modal-header,
#courseMarksModal .modal-header,
#parentchildModal .modal-header,
#gradeFinalizationConfirmModal .modal-header {
    min-height: 68px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
    background: #1f756d;
}

/* Titles */
#uploadFileModel .modal-title,
#smallmodal .modal-title,
#courseMarksModal .modal-title,
#parentchildModal .modal-title,
#gradeFinalizationConfirmModal .modal-title,
#smallmodal .modal-header h3 {
    margin: 0;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
}

/* Small modal existing heading reset */
#smallmodal .modal-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Close button */
#uploadFileModel .close,
#smallmodal .close,
#courseMarksModal .close,
#parentchildModal .close,
#gradeFinalizationConfirmModal .close {
    width: 36px;
    height: 36px;
    margin: 0;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    background: #eef2f7;
    color: #475569;
    font-size: 24px;
    font-weight: 400;
    line-height: 1;
    opacity: 1;
    text-shadow: none;
    transition: background 0.2s ease, color 0.2s ease;
}

#uploadFileModel .close:hover,
#smallmodal .close:hover,
#courseMarksModal .close:hover,
#parentchildModal .close:hover,
#gradeFinalizationConfirmModal .close:hover {
    background: #e2e8f0;
    color: #111827;
}

/* Body */
#uploadFileModel .modal-body,
#smallmodal .modal-body,
#courseMarksModal .modal-body,
#parentchildModal .modal-body,
#gradeFinalizationConfirmModal .modal-body {
    padding: 24px;
    color: #475569;
    font-size: 15px;
    line-height: 1.6;
    background: #ffffff;
}

/* Form labels */
#uploadFileModel label,
#courseMarksModal label,
#parentchildModal label {
    margin-bottom: 7px;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

/* Inputs */
#uploadFileModel .form-control,
#courseMarksModal .form-control,
#parentchildModal .form-control,
#uploadFileModel .form-control-file {
    min-height: 44px;
    border: 1px solid #d7dee7;
    border-radius: 8px;
    background: #ffffff;
    color: #1f2937;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#uploadFileModel .form-control,
#courseMarksModal .form-control,
#parentchildModal .form-control {
    padding: 10px 12px;
}

#uploadFileModel .form-control:focus,
#courseMarksModal .form-control:focus,
#parentchildModal .form-control:focus {
    border-color: #1f756d;
    box-shadow: 0 0 0 3px rgba(31, 117, 109, 0.12);
}

/* Textarea */
#courseMarksModal textarea.form-control {
    min-height: 88px;
    resize: vertical;
}

/* Footer */
#uploadFileModel .modal-footer,
#smallmodal .modal-footer,
#courseMarksModal .modal-footer,
#parentchildModal .modal-footer,
#gradeFinalizationConfirmModal .modal-footer {
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #e5e7eb;
    background: #f8fafc;
}

/* Footer buttons */
#uploadFileModel .modal-footer .btn,
#smallmodal .modal-footer .btn,
#courseMarksModal .modal-footer .btn,
#parentchildModal .modal-footer .btn,
#gradeFinalizationConfirmModal .modal-footer .btn {
    min-width: 110px;
    min-height: 42px;
    margin: 0;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: none;
}

/* Primary action */
#uploadFileModel .btn-success,
#parentchildModal .btn-primary,
#gradeFinalizationConfirmModal .btn-primary,
#courseMarksModal .btn-primary {
    border-color: #1f756d;
    background: #1f756d;
    color: #ffffff;
}

#uploadFileModel .btn-success:hover,
#parentchildModal .btn-primary:hover,
#gradeFinalizationConfirmModal .btn-primary:hover,
#courseMarksModal .btn-primary:hover {
    border-color: #175f59;
    background: #175f59;
}

/* Secondary action */
#uploadFileModel .btn-secondary,
#smallmodal .btn-secondary,
#courseMarksModal .btn-secondary,
#parentchildModal .btn-secondary,
#gradeFinalizationConfirmModal .btn-secondary {
    border-color: #d1d5db;
    background: #ffffff;
    color: #374151;
}

#uploadFileModel .btn-secondary:hover,
#smallmodal .btn-secondary:hover,
#courseMarksModal .btn-secondary:hover,
#parentchildModal .btn-secondary:hover,
#gradeFinalizationConfirmModal .btn-secondary:hover {
    background: #f3f4f6;
}

/* Upload file preview */
#uploadFileModel .file-container {
    padding: 22px;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
}

#uploadFileModel .file-info i {
    color: #1f756d !important;
}

/* Alerts */
#smallmodal .alert-info,
#courseMarksModal .alert-info {
    border: 1px solid #bae6fd;
    border-radius: 8px;
    background: #f0f9ff;
    color: #075985;
}

/* Tables inside modal */
#smallmodal .table,
#courseMarksModal .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
}

#smallmodal .table thead th,
#courseMarksModal .table thead th {
    padding: 12px;
    border-color: #e5e7eb;
    background: #f1f5f9;
    color: #334155;
    font-size: 13px;
    font-weight: 700;
}

#smallmodal .table td,
#courseMarksModal .table td {
    padding: 12px;
    vertical-align: middle;
    border-color: #e5e7eb;
    color: #475569;
}

/* Backdrop */
.modal-backdrop.show {
    opacity: 0.48;
}

/* Mobile */
@media (max-width: 575px) {
    #uploadFileModel .modal-dialog,
    #smallmodal .modal-dialog,
    #courseMarksModal .modal-dialog,
    #parentchildModal .modal-dialog,
    #gradeFinalizationConfirmModal .modal-dialog {
        width: calc(100% - 20px);
        margin: 16px auto;
    }

    #uploadFileModel .modal-header,
    #smallmodal .modal-header,
    #courseMarksModal .modal-header,
    #parentchildModal .modal-header,
    #gradeFinalizationConfirmModal .modal-header {
        padding: 15px 16px;
    }

    #uploadFileModel .modal-body,
    #smallmodal .modal-body,
    #courseMarksModal .modal-body,
    #parentchildModal .modal-body,
    #gradeFinalizationConfirmModal .modal-body {
        padding: 18px 16px;
    }

    #uploadFileModel .modal-footer,
    #smallmodal .modal-footer,
    #courseMarksModal .modal-footer,
    #parentchildModal .modal-footer,
    #gradeFinalizationConfirmModal .modal-footer {
        padding: 14px 16px;
        flex-direction: column-reverse;
    }

    #uploadFileModel .modal-footer .btn,
    #smallmodal .modal-footer .btn,
    #courseMarksModal .modal-footer .btn,
    #parentchildModal .modal-footer .btn,
    #gradeFinalizationConfirmModal .modal-footer .btn {
        width: 100%;
    }
}





















/* =========================================================
   COURSE GRADING — FIGMA STYLE OVERRIDE
   Exact Figma frame: 9:6

   STRICT RULES
   ---------------------------------------------------------
   Existing Blade structure 100% preserved
   Existing PHP conditions preserved
   Existing buttons preserved
   Existing icons preserved
   Existing notices/messages preserved
   Existing accordion/collapse behavior preserved
   Existing modals preserved
   Existing JS/AJAX preserved

   STYLE ONLY
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. FIGMA DESIGN TOKENS
   ========================================================= */

.grading-card-header,
.page-body,
#accordionExample {
    --grading-primary: #17675F;
    --grading-primary-dark: #125D56;

    --grading-text: #252525;
    --grading-heading: #252525;
    --grading-muted: #7A7A7A;

    --grading-white: #FFFFFF;
    --grading-border: #E5E7EB;
    --grading-row-alt: #FAFBFF;

    --grading-warning: #FFBA49;
    --grading-danger: #EF4444;
    --grading-success: #10B981;

    font-family: 'Montserrat', sans-serif !important;
}


.grading-card-header *:not(.fa):not([class^="ti-"]):not([class*=" ti-"]),
.page-body *:not(.fa):not([class^="ti-"]):not([class*=" ti-"]),
#accordionExample *:not(.fa):not([class^="ti-"]):not([class*=" ti-"]) {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. MAIN HEADER AREA
   Figma:
   Course Grading
   course name beneath
   action/state at right
   ========================================================= */

.grading-card-header {
    width: 100% !important;

    margin: 0 0 16px !important;
    padding: 0 !important;

    align-items: flex-end !important;
    justify-content: space-between !important;

    gap: 12px 16px !important;

    flex-wrap: wrap !important;
}


/* Left information area */
.grading-card-info {
    flex: 1 1 520px !important;

    min-width: 280px !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* Existing conditional right-side elements */
.grading-card-header > *:not(.grading-card-info) {
    flex: 0 0 auto !important;

    margin: 0 !important;
}


/* Existing right-side wrappers */
.grading-card-header > div:not(.grading-card-info) {
    align-items: center !important;

    margin: 0 !important;
}


/* =========================================================
   3. COURSE GRADING TITLE
   Figma = Montserrat SemiBold 20px
   ========================================================= */

.profile-overview-title {
    margin: 0 0 5px !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #252525 !important;
}


/* =========================================================
   4. COURSE NAME
   Figma = 18px Regular
   ========================================================= */

.grading-course-name {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #252525 !important;
}


/* =========================================================
   5. IMPORTANT NOTICE
   Not present in this Figma state,
   but exists in original Blade and MUST remain.
   Give it matching visual language.
   ========================================================= */

.grading-important-notice {
    width: 100% !important;

    margin: 12px 0 0 !important;
    padding: 10px 12px !important;

    background: rgba(255, 186, 73, 0.10) !important;

    border: 1px solid rgba(255, 186, 73, 0.32) !important;
    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.55 !important;

    color: #4B5563 !important;
}


.grading-important-notice strong {
    font-size: 12px !important;
    font-weight: 600 !important;

    color: #B45309 !important;
}


/* Existing success message such as
   Course combined successfully */
.grading-card-info > span:not(.grading-important-notice) {
    margin-top: 10px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    line-height: 1.4 !important;
}


/* =========================================================
   6. NOT READY FOR GRADING
   Exact Figma:
   221 x 30
   orange
   50px pill
   ========================================================= */

.grading-card-header > .btn-secondary {
    width: 221px !important;
    min-width: 221px !important;

    height: 30px !important;
    min-height: 30px !important;

    margin: 0 !important;
    padding: 0 8px 0 12px !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 5px !important;

    background: #FFBA49 !important;

    border: 0 !important;
    border-radius: 50px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;

    white-space: nowrap !important;
}


.grading-card-header > .btn-secondary:hover,
.grading-card-header > .btn-secondary:focus {
    background: #FFBA49 !important;

    border: 0 !important;

    color: #FFFFFF !important;

    box-shadow: none !important;
}


/* Existing question mark image stays present */
.grading-card-header > .btn-secondary img {
    width: 24px !important;
    height: 24px !important;

    margin: 0 0 0 4px !important;

    object-fit: contain !important;
}


/* =========================================================
   7. MAKE COMBINE GRADING
   Existing state preserved
   ========================================================= */

.grading-card-header > .btn-outline-info,
.grading-card-header > div > .btn-outline-info {
    width: auto !important;
    min-width: 190px !important;

    height: 36px !important;
    min-height: 36px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 1px solid #17675F !important;
    border-radius: 50px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


.grading-card-header .btn-outline-info i {
    margin-right: 5px !important;

    font-size: 13px !important;

    color: #17675F !important;
}


/* =========================================================
   8. COURSE COMBINED BUTTON
   Existing conditional state
   ========================================================= */

.grading-card-header .btn-success {
    min-width: 190px !important;

    height: 36px !important;
    min-height: 36px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 50px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #FFFFFF !important;
}


/* =========================================================
   9. GRADES FINALIZATION
   Existing link preserved
   ========================================================= */

.grade-finalization-link {
    margin: 0 !important;

    text-decoration: none !important;
}


.grade-finalization-link h5 {
    min-height: 36px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 1px solid #17675F !important;
    border-radius: 50px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;
    line-height: 34px !important;

    color: #17675F !important;
}


/* =========================================================
   10. EXISTING FILE WARNING
   Remains fully visible
   ========================================================= */

.grading-card-header .alert-danger {
    min-height: 36px !important;

    margin: 0 !important;
    padding: 8px 12px !important;

    background: #FEF2F2 !important;

    border: 1px solid #FECACA !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;

    color: #DC2626 !important;
}


/* =========================================================
   11. MAIN GRADING AREA
   No visibility/display state touched
   ========================================================= */

.page-body {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;
}


.page-body > div,
.page-body .tab-content,
.page-body #grading,
.page-body .card-body.scroller,
.page-body .bs-example,
#accordionExample {
    width: 100% !important;
    max-width: 100% !important;

    margin-left: 0 !important;
    margin-right: 0 !important;
}


/* =========================================================
   12. ACCORDION
   ========================================================= */

#accordionExample {
    border: 0 !important;

    background: transparent !important;
}


/* =========================================================
   13. EACH ASSESSMENT CARD
   Figma radius = 5px
   ========================================================= */

#accordionExample > .card {
    width: 100% !important;

    margin: 0 0 20px !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: visible !important;
}


/* =========================================================
   14. ACCORDION HEADER
   Exact Figma:
   height 52
   #17675F
   radius 5
   ========================================================= */

#accordionExample
> .card
> .card-header {

    width: 100% !important;

    min-height: 52px !important;

    margin: 0 !important;
    padding: 10px 14px 10px 20px !important;

    align-items: center !important;
    justify-content: space-between !important;

    gap: 14px !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    box-sizing: border-box !important;
}


/* Expanded card header */
#accordionExample
> .card
> .card-header:has(+ hr + .collapse.show),

#accordionExample
> .card
> .card-header:has(+ .collapse.show) {
    border-radius: 5px 5px 0 0 !important;
}


/* =========================================================
   15. HEADER LEFT SIDE
   ========================================================= */

#accordionExample
> .card
> .card-header
> div:first-child {

    flex: 1 1 auto !important;

    min-width: 0 !important;

    margin: 0 !important;
}


/* Existing arrow + assessment title */
#accordionExample
> .card
> .card-header
> div:first-child
> i {

    margin: 0 !important;

    align-items: center !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;

    color: #FFFFFF !important;

    cursor: pointer !important;
}


/* FontAwesome arrow itself stays present */
#accordionExample
> .card
> .card-header
> div:first-child
> i::before {

    margin-right: 8px !important;

    font-family: FontAwesome !important;

    font-size: 13px !important;

    color: #FFFFFF !important;
}


/* =========================================================
   16. HEADER RIGHT SIDE
   ========================================================= */

#accordionExample
> .card
> .card-header
> div:last-child {

    flex: 0 0 auto !important;

    margin: 0 !important;

    align-items: center !important;
    justify-content: flex-end !important;
}


/* =========================================================
   17. ADD NEW
   Exact Figma = 114 x 30
   ========================================================= */

#accordionExample
.card-header
.btn-outline-info {

    width: 114px !important;
    min-width: 114px !important;

    height: 30px !important;
    min-height: 30px !important;

    margin: 0 !important;
    padding: 0 10px !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #17675F !important;
}


#accordionExample
.card-header
.btn-outline-info i {

    margin: 0 !important;

    /* font-family: 'Montserrat', sans-serif !important; */

    font-size: 16px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #17675F !important;
}


/* Actual plus glyph stays from existing FontAwesome */
#accordionExample
.card-header
.btn-outline-info
i::before {

    margin-right: 3px !important;

    font-family: FontAwesome !important;

    font-size: 13px !important;

    color: #17675F !important;
}


#accordionExample
.card-header
.btn-outline-info:hover {

    background: #F6FAF9 !important;

    color: #17675F !important;
}


/* =========================================================
   18. "FINAL MARKS ASSESSMENT CREATED"
   Existing structure preserved
   ========================================================= */

#accordionExample
.card-header
span {

    font-family: 'Montserrat', sans-serif !important;
}


#accordionExample
.card-header
div:last-child
> span:not(:has(button)) {

    margin: 0 !important;
    padding: 7px 10px !important;

    background: rgba(255, 255, 255, 0.12) !important;

    border-radius: 5px !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #FFFFFF !important;
}


/* =========================================================
   19. COLLAPSIBLE CONTENT
   CRITICAL:
   No display property here.
   Bootstrap remains 100% responsible for show/collapse.
   ========================================================= */

#accordionExample
> .card
> div[id^="collapse"] {

    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border-right: 1px solid #E5E7EB !important;
    border-bottom: 1px solid #E5E7EB !important;
    border-left: 1px solid #E5E7EB !important;

    border-radius: 0 0 5px 5px !important;

    box-shadow: none !important;
}


/* =========================================================
   20. COLLAPSE BODY
   ========================================================= */

#accordionExample
> .card
> div[id^="collapse"]
> .card-body {

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #FFFFFF !important;

    border-radius: 0 0 5px 5px !important;

    box-shadow: none !important;
}


/* =========================================================
   21. ASSESSMENT ROWS
   Figma rows ~51px.
   Existing extra actions may require more space,
   therefore min-height rather than forced fixed height.
   ========================================================= */

#accordionExample
> .card
> div[id^="collapse"]
> .card-body
> div {

    position: relative !important;

    width: 100% !important;
    min-height: 51px !important;

    margin: 0 !important;
    padding: 10px 20px !important;

    float: none !important;
    clear: both !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    box-shadow: none !important;

    box-sizing: border-box !important;

    display: flow-root !important;
}


#accordionExample
> .card
> div[id^="collapse"]
> .card-body
> div:last-child {

    border-bottom: 0 !important;
}


/* =========================================================
   22. ASSESSMENT NUMBER
   Existing number remains visible
   ========================================================= */

#accordionExample
.card-body
b > span {

    margin-right: 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


/* =========================================================
   23. EXISTING PENCIL ICON
   Figma does not contain this exact control,
   but original structure has it so it remains.
   ========================================================= */

#accordionExample
.card-body
.ti-pencil-alt {

    margin-right: 7px !important;

    vertical-align: middle !important;

    font-size: 14px !important;

    color: #17675F !important;
}


/* =========================================================
   24. ASSESSMENT TITLE
   Figma = 16px Regular #252525
   ========================================================= */

#accordionExample
.card-body
strong {

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #252525 !important;
}


/* =========================================================
   25. STUDENTS UNMARKED
   Exact Figma = 12px #7A7A7A
   ========================================================= */

#accordionExample
.card-body
.fs-16 {

    margin-left: 6px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #7A7A7A !important;
}


/* Existing status line */
#accordionExample
.card-body
.mb-3 {

    margin-top: 2px !important;
    margin-bottom: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    line-height: 1.4 !important;
}


/* Existing "Students Marks not Approved" stays visible */
#accordionExample
.card-body
.mb-3
> span[style*="crimson"] {

    margin-left: 6px !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #DC2626 !important;
}


/* =========================================================
   26. EXISTING ROW ACTION LINKS
   Preserve every one of them
   ========================================================= */

#accordionExample
.card-body
> div
> a {

    float: right !important;

    margin: 0 0 6px 8px !important;

    text-decoration: none !important;
}


/* Existing direct Upload File button */
#accordionExample
.card-body
> div
> button {

    float: right !important;

    margin: 0 0 6px 8px !important;
}


/* =========================================================
   27. ADD / EDIT MARKS + REQUEST HOD
   Existing controls kept,
   visually adapted to Figma.
   ========================================================= */

#accordionExample
.card-body
.btn-outline-success {

    width: auto !important;
    min-width: 124px !important;

    height: 32px !important;
    min-height: 32px !important;

    margin: 0 !important;
    padding: 0 11px !important;

    background: #FFFFFF !important;

    border: 1px solid #17675F !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


/* Keep all existing button icon/text */
#accordionExample
.card-body
.btn-outline-success i {

    margin: 0 !important;

    /* font-family: 'Montserrat', sans-serif !important; */

    font-size: 12px !important;
    font-style: normal !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


#accordionExample
.card-body
.btn-outline-success
i::before {

    margin-right: 5px !important;

    font-family: FontAwesome !important;

    font-size: 11px !important;

    color: #17675F !important;
}


/* =========================================================
   28. UPLOAD FILE
   Existing secondary state preserved
   ========================================================= */

#accordionExample
.card-body
.btn-outline-secondary {

    width: auto !important;
    min-width: 116px !important;

    height: 32px !important;
    min-height: 32px !important;

    margin: 0 !important;
    padding: 0 11px !important;

    background: #FFFFFF !important;

    border: 1px solid #D1D5DB !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #4B5563 !important;
}


#accordionExample
.card-body
.btn-outline-secondary i {

    margin-right: 5px !important;

    font-size: 12px !important;

    color: #4B5563 !important;
}


/* =========================================================
   29. ACTION HOVERS
   Pure styling, no structure change
   ========================================================= */

#accordionExample
.card-body
.btn-outline-success:hover,

#accordionExample
.card-body
.btn-outline-success:focus {

    background: rgba(23, 103, 95, 0.06) !important;

    border-color: #17675F !important;

    color: #17675F !important;

    box-shadow: none !important;
}


#accordionExample
.card-body
.btn-outline-secondary:hover,

#accordionExample
.card-body
.btn-outline-secondary:focus {

    background: #F9FAFB !important;

    border-color: #9CA3AF !important;

    color: #374151 !important;

    box-shadow: none !important;
}


/* =========================================================
   30. EXISTING DISABLED STATES
   Preserve meaning and interaction behavior
   ========================================================= */

#accordionExample
.disabled {

    cursor: default !important;
}


/* =========================================================
   31. RESPONSIVE — TABLET
   No content removed.
   Existing actions simply wrap.
   ========================================================= */

@media (max-width: 991.98px) {

    .grading-card-header {
        align-items: flex-start !important;
    }


    .grading-card-info {
        flex-basis: 100% !important;
    }


    #accordionExample
    > .card
    > .card-header {

        flex-wrap: wrap !important;
    }

}


/* =========================================================
   32. RESPONSIVE — MOBILE
   Every original control remains present.
   ========================================================= */

@media (max-width: 767.98px) {

    .profile-overview-title {
        font-size: 20px !important;
    }


    .grading-course-name {
        font-size: 16px !important;
    }


    .grading-card-header {
        gap: 10px !important;
    }


    .grading-card-header > *:not(.grading-card-info) {
        max-width: 100% !important;
    }


    .grading-card-header > .btn-secondary {
        width: auto !important;
        min-width: 200px !important;
    }


    #accordionExample
    > .card
    > .card-header {

        min-height: 52px !important;

        padding: 10px 12px !important;

        gap: 10px !important;
    }


    #accordionExample
    > .card
    > .card-header
    > div:first-child
    > i {

        font-size: 14px !important;
    }


    #accordionExample
    .card-header
    .btn-outline-info {

        width: auto !important;
        min-width: 100px !important;

        font-size: 14px !important;
    }


    #accordionExample
    > .card
    > div[id^="collapse"]
    > .card-body
    > div {

        padding: 12px !important;
    }


    #accordionExample
    .card-body
    > div
    > a,

    #accordionExample
    .card-body
    > div
    > button {

        float: none !important;

        margin: 6px 6px 0 0 !important;
    }


    #accordionExample
    .card-body
    .btn-outline-success,

    #accordionExample
    .card-body
    .btn-outline-secondary {

        width: auto !important;
        min-width: 118px !important;
    }

}
/* Icon font fix */
.fa {
    font-family: FontAwesome !important;
}

[class^="ti-"],
[class*=" ti-"] {
    font-family: 'themify' !important;
}



/* Button text = Montserrat, icon = FontAwesome */
#accordionExample .card-body .btn-outline-success i.fa {
    font-family: 'Montserrat', sans-serif !important;
}

#accordionExample .card-body .btn-outline-success i.fa::before {
    font-family: FontAwesome !important;
}
/* Add New text = Montserrat, plus icon = FontAwesome */
#accordionExample .card-header .btn-outline-info i.fa {
    font-family: 'Montserrat', sans-serif !important;
}

#accordionExample .card-header .btn-outline-info i.fa::before {
    font-family: FontAwesome !important;
}
</style>
<div class="">
    {{-- *************************************** header section start from here ***************************************************** --}}
    <div class="grading-card-header">
        <div class="grading-card-info">
            <h4 class="profile-overview-title">Course Grading</h4>
            <h5 class="grading-course-name">{{ $course_name }} {{$courseId[0]->grading_type}}</h5>
            <?php if($courseId[0]->grading_type == 'Relative'){?>  
            <?php if($checkCount > 0){?> 
                <?php if($chkParentDone == 0){?>
                <span class="grading-important-notice" style="color:#4B5563;"><strong style="color:#B45309;"> Important Notice: </strong>

                    Faculty teaching different sections of the same course will have their sections combined.

                    Initially, faculty must enter marks in all sections. Then course must be combined to proceed with the grading process.

                    Please note that combining sections is mandatory for Relative Grading.</span>
            <?php }else{ ?>
             
                    <span  style="color:green;font-weight: bold;font-size: 16px;">Course combined successfully</span>
               

                

            <?php } ?>
            <?php }} ?>
        </div>
       
        @if($status == '1') 
            @if($get_criteria[0]->co_school_id == '12' || $get_criteria[0]->co_school_id == '21')
                @if($result == '1')
                    <?php if($courseId[0]->grading_type == 'Relative'){?>
                        <?php if($checkCount > 0){?>
                            <?php if($chkParentDone == 0){?>
                                
                                <?php if($getParentCourse ==1){ ?>

                                    <button style="width:230px;height: 44px;"
                                        type="button"
                                        class="btn btn-success"
                                        disabled>
                                        <i class="fa fa-check"></i> Course Combined
                                    </button>

                                <?php } else { ?>

                                    <button style="width:230px; height: 44px;border-radius: 50px;" 
                                        type="button"
                                        class="btn btn-outline-info"
                                        data-toggle="modal"
                                        onclick="openMarksModal3('{{$courseId[0]->co_course_id}}')"
                                        data-target="#Student_Marks_list_new2">
                                        <i class="fa fa-plus"></i> Make Combine Grading
                                    </button>

                                <?php } ?>
                        <?php }} ?>
                    <?php } ?>
                    <?php  if($chkParentDone == 0){
                    // if($getParentCourse > 0){?>
                        <div style="float: right;">
                            <a href="javascript:void(0)"
                                class="grade-finalization-link"
                                data-toggle="modal"
                                data-target="#gradeFinalizationConfirmModal"
                                data-finalization-url="/teacher/courses2?co_id={{ $get_criteria[0]->co_id }}">
                                <h5>Grades Finalization</h5>
                            </a>
                        </div>
                    <?php } ?>
                @else 
                    <div style="float: right;">
                        <button type="button" class="alert alert-danger mr-2" data-toggle="modal" data-target="#smallmodal">
                            <i class="fa fa-exclamation-triangle"></i> Kindly ensure all files are uploaded before you finalize the grades.
                        </button>
                    </div>                
                    <div class="modal fade" id="smallmodal" tabindex="-1" role="dialog" aria-labelledby="smallmodal" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3  id="smallmodal1">
                                    
                                        <i class="fa fa-file mr-4" style="font-size: 30px;"></i>
                                        <span style="font-size: 24px;">Files Required</span>
                                    </h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body pb-4 mt-3">
                                    <p class="alert alert-info">It is requested that you kindly upload the following files before finalizing the grading.</p>
                                    <table class="table table-bordered">
                                        <thead>
                                            <th scope="col" >Sr#</th>
                                            <th scope="col" >Type</th>
                                            <th scope="col" >Name</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($checkNullFiles as $index => $nullFiles)
                                            <tr>
                                                <td>
                                                    <p>{{$index}}</p>
                                                </td>
                                                <td>
                                                    <p>{{$nullFiles->assessment_name}}</p>
                                                </td>
                                                <td>
                                                    <strong>{{$nullFiles->ccd_details}}</strong>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
            @else 
                <?php if($courseId[0]->grading_type == 'Relative'){?>
                    <?php if($checkCount > 0){?>
                        <?php if($chkParentDone == 0){?>
                            
                            <?php if($getParentCourse ==1){ ?>

                                <button style="width:230px;height: 44px;"
                                    type="button"
                                    class="btn btn-success"
                                    disabled>
                                    <i class="fa fa-check"></i> Course Combined
                                </button>

                            <?php } else { ?>

                                <button style="width:230px; height: 44px;border-radius: 50px;" 
                                    type="button"
                                    class="btn btn-outline-info"
                                    data-toggle="modal"
                                    onclick="openMarksModal3('{{$courseId[0]->co_course_id}}')"
                                    data-target="#Student_Marks_list_new2">
                                    <i class="fa fa-plus"></i> Make Combine Grading
                                </button>

                            <?php } ?>
                    <?php }} ?>
                <?php } ?>
                <?php  if($chkParentDone == 0){
                    if($CombineButtonAppears >= 0 || $checkCount == 0 || $courseId[0]->grading_type != 'Relative'){?>
                    <div style="float: right;">
                        <a href="javascript:void(0)"
                            class="grade-finalization-link"
                            data-toggle="modal"
                            data-target="#gradeFinalizationConfirmModal"
                            data-finalization-url="/teacher/courses2?co_id={{ $get_criteria[0]->co_id }}">
                            <h5>Grades Finalization</h5>
                        </a>
                    </div>
                <?php } ?>
                <?php } ?>
            
            @endif
        @else
            <?php if($courseId[0]->grading_type == 'Relative'){?>       
                <?php if($checkCount > 0){?>
                    <?php if($chkParentDone == 0){?>
                        <div style="float: right;margin-left:0;">
                        <!-- To check course Combined or not -->
                            <?php if($getParentCourse ==1){ ?>

                                <button style="width:230px;height: 44px;"
                                    type="button"
                                    class="btn btn-success"
                                    disabled>
                                    <i class="fa fa-check"></i> Course Combined
                                </button>

                            <?php } else { ?>

                                <button style="width:230px; height: 44px;margin-bottom:25px;border-radius: 50px;" 
                                    type="button"
                                    class="btn btn-outline-info"
                                    data-toggle="modal"
                                    onclick="openMarksModal3('{{$courseId[0]->co_course_id}}')"
                                    data-target="#Student_Marks_list_new2">
                                    <i class="fa fa-plus"></i> Make Combine Grading
                                </button>

                            <?php } ?>

                        </div>
                <?php }} ?>
            <?php } ?>
            <?php if($chkParentDone == 0){?>
                <div style="float: right;">
                    <button type="button" class="btn btn-secondary"
                        onclick="gradingDetailPending('{{ $get_criteria[0]->co_id }}')" style="border-radius: 50px;
background: #FFBA49;border:none;">
                        Not Ready for Grading
                        <img src="{{ asset('images/questionmark.png') }}" width="30px">
                    </button>
                </div>
            <?php } ?>       
        @endif

    </div> 
     {{--****************************************** header section end here ********************************************************  --}}
    <?php if($chkParentDone == 0){?>
    <div class="page-body">
        <div  style="width: 100%;">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="grading">
                <!-- ///////////////// start tab /////////////////////////// -->
                <div class="card-body scroller" style="padding:0px;  width:100%; ">
                    <div class="bs-example">
                        <div class="accordion" style="border-style: none !important;background:none !important;" id="accordionExample">
                            <?php
                            $sr_no=1;
                            $hostName = $_SERVER['HTTP_HOST'];
                            foreach($get_criteria as $rec_teacher_courses){
                            if($rec_teacher_courses->lock_status==1){
                                    header("Location: https://".$hostName."/teacher/courses2?co_id=".$rec_teacher_courses->co_id);
                                    exit;
                                }
                            $pic_name = "asset('images/dummy.jpg')";
                            $course_criter_co_id = $rec_teacher_courses->co_id;
                            $new_criteria_id = 2222;
                            ?>
                            <div class="card">
                                <div class="card-header" style="cursor: pointer; border:solid 0px #000000;display:inline;margin-bottom: 0 !important" id="heading<?=$rec_teacher_courses->assessment_id ;?>" >
                                    <div style="float:left">
                                    <i class="fa fa-arrow-down" style="color:#0C3; font-size:18px; line-height:5px;" data-toggle="collapse" data-target="#collapse<?=$rec_teacher_courses->assessment_id ;?>" >
                                        <?php echo $rec_teacher_courses->assessment_name; ?>
                                        (<?=$rec_teacher_courses->cc_weightage;?>)
                                    </i>
                                </div>
                                <div style="float:right"
                                    <?php if($rec_teacher_courses->assessment_id == 4){?>
                                        <?php if($rec_teacher_courses->total_assessments > 0){?>
                                            <span style="margin-left:50%;">Final Marks Assessment Created</span>
                                        <?php } else { ?>
                                            <span style="margin-left:50%;"><button style="width:230px"  type="button" class="btn btn-outline-info" data-toggle="modal" onclick="openMarksModal('<?=$course_criter_co_id;?>','<?=$rec_teacher_courses->assessment_id;?>','<?=$rec_teacher_courses->assessment_name;?>','new','<?=$rec_teacher_courses->id;?>')" data-target="#Student_Marks_list_new"><i class="fa fa-plus">Add New <?php ////$rec_teacher_courses->assessment_name ?></i></button></span>
                                        <?php } ?>
                                    <?php }else{?>
                                        <span style="margin-left:50%;"><button style="width:230px" type="button" class="btn btn-outline-info" data-toggle="modal" onclick="openMarksModal('<?=$course_criter_co_id;?>','<?=$rec_teacher_courses->assessment_id;?>','<?=$rec_teacher_courses->assessment_name;?>','new','<?=$rec_teacher_courses->id;?>')" data-target="#Student_Marks_list_new"><i class="fa fa-plus">Add New <?php ////$rec_teacher_courses->assessment_name ?></i></button></span>
                                    <?php } ?>
                                    </div>
                                </div>

                            <hr>

                                <div id="collapse<?=$rec_teacher_courses->assessment_id ;?>" class="collapse show" aria-labelledby="heading<?=$rec_teacher_courses->assessment_id ;?>" data-parent="#accordionExample">
                                    <div class="card-body">

                                        <?php
                                        $count=1;

                                        $image_width = "35px";
                                        $assessment_id  = $rec_teacher_courses->assessment_id;
                                    // print_r($get_criteria_details);

                                    // echo "____".count($get_criteria_details);
                                    // exit();
                                        foreach($get_criteria_details as $rec_get_criteria_details){
                                        $co_id 						= $rec_get_criteria_details->co_id;
                                        $course_criteria_detail_id  = $rec_get_criteria_details->id;
                                            // echo $rec_teacher_courses->assessment_id."===".$rec_get_criteria_details->assessment_id;
                                        if($rec_teacher_courses->assessment_id==$rec_get_criteria_details->assessment_id){

                                        ?>

                                        <div style="border:solid 0px #000000; width:100%; float:left; padding:4px; margin:4px;">
                                            {{--<div style="border:solid 0px #000000; margin:4px; float:left;">--}}

                                                <b><span style="margin-right:10px; color:#0021ff;">{{$count}}) </span></b>
                                                {{-- <a  href="{{url('teacher/check_assignment',['title'=>$course_criteria_detail_id,'id'=> $co_id])}}" style="float:right; color:#000;"  onclick="openMarksModal('<?=$course_criter_co_id;?>','<?=$rec_teacher_courses->assessment_id;?>','<?=$rec_teacher_courses->id;?>')" > --}}

                                                <?php $course_criteria_detail_id;?>
                                                <?php $co_id;?>
                                                {{-- <i class="fa fa-pencil-square-o" aria-hidden="true" style="float:right; text-decoration:none; font-size:24px;"></i> --}}
                                            </a>

                                            <?php if($rec_teacher_courses->assessment_id==4 or $rec_teacher_courses->assessment_id==3){?>
                                            <a href="{{url('teacher/assessment-marks',['co_id'=> $co_id,'ass_type'=>$rec_get_criteria_details->assessment_id,'num'=>$rec_get_criteria_details->ccd_number,'ccd_id'=>$rec_get_criteria_details->id])}}" style="float:right; color:#000;"  >
                                                <?php $course_criteria_detail_id;?>
                                                <?php $co_id;?>
                                                    <button style="width:230px" type="button" class="btn btn-outline-success"><i class="fa fa-edit">Add/Edit Marks</i></button>
                                            </a> 
                                            <!-- href="{{url('teacher/assessment-approval',['co_id'=> $co_id,'ass_type'=>$rec_get_criteria_details->assessment_id,'num'=>$rec_get_criteria_details->ccd_number,'ccd_id'=>$rec_get_criteria_details->id])}}" -->
                                            <?php $var_disabled='';
                                            $var_disabled=($rec_get_criteria_details->missing_approval_students == 0)?'disabled':'';
                                            ?>
                                            

                                            <a id="req-hod"  class="<?=$var_disabled;?>" onclick="HodRequest('<?=$co_id;?>','<?=$rec_get_criteria_details->id;?>','<?=$rec_get_criteria_details->ccd_number?>','<?=$rec_get_criteria_details->assessment_id;?>')"  style="float:right; color:#000;"  >
                                                <?php $course_criteria_detail_id;?>
                                                <?php $co_id;?>
                                                    <button style="width:230px" type="button" class="btn btn-outline-success req"><i class="fa fa-send"> Request to HOD </i></button>
                                            </a> 
                                            
                                            @if($get_criteria[0]->co_school_id == '12' || $get_criteria[0]->co_school_id == '21')   
                                            <button  type="button"
                                            style="float:right; color:#000;" data-toggle="modal"
                                            data-target="#uploadFileModel"
                                            data-course-criteria-detail-id="<?php echo $rec_get_criteria_details->id; ?>"
                                            data-co-id="<?php echo $co_id; ?>"
                                            data-file-name="<?php echo $rec_get_criteria_details->file_name; ?>"
                                            data-drive-file-name="<?php echo $rec_get_criteria_details->google_drive_file_name; ?>"
                                            data-drive-url="<?php echo $rec_get_criteria_details->google_drive_url; ?>"
                                            data-file-description="<?php echo $rec_get_criteria_details->description; ?>"
                                            class="btn btn-outline-<?=($rec_get_criteria_details->google_drive_url)?"success":"secondary";?> mr-2">
                                            <i class="mr-1 fa fa-<?=($rec_get_criteria_details->google_drive_url)?"edit":"cloud-upload";?>"></i><?=($rec_get_criteria_details->google_drive_url)?"Update File":"Upload File";?> 
                                            </button>
                                            @endif

                                            <?php }else{?>
                                                <a href="{{url('teacher/assessment-marks',['co_id'=> $co_id,'ass_type'=>$rec_get_criteria_details->assessment_id,'num'=>$rec_get_criteria_details->ccd_number,'ccd_id'=>$rec_get_criteria_details->id])}}" style="float:right; color:#000;"  >

                                                    <button style="width:230px" type="button" class="btn btn-outline-success"><i class="fa fa-edit">Add/Edit Marks</i></button>
                                                    </a>
                                                    @if($get_criteria[0]->co_school_id == '12' || $get_criteria[0]->co_school_id == '21')
                                                    <button  type="button"
                                                    style="float:right; color:#000;" data-toggle="modal"
                                                    data-target="#uploadFileModel"
                                                    data-course-criteria-detail-id="<?php echo $rec_get_criteria_details->id; ?>"
                                                    data-co-id="<?php echo $co_id; ?>"
                                                    data-file-name="<?php echo $rec_get_criteria_details->file_name; ?>"
                                                    data-drive-file-name="<?php echo $rec_get_criteria_details->google_drive_file_name; ?>"
                                                    data-drive-url="<?php echo $rec_get_criteria_details->google_drive_url; ?>"
                                                    data-file-description="<?php echo $rec_get_criteria_details->description; ?>"
                                                    class="btn btn-outline-<?=($rec_get_criteria_details->google_drive_url)?"success":"secondary";?> mr-2">
                                                    <i class="mr-1 fa fa-<?=($rec_get_criteria_details->google_drive_url)?"edit":"cloud-upload";?>"></i><?=($rec_get_criteria_details->google_drive_url)?"Update File":"Upload File";?> 
                                                    </button>
                                                    @endif

                                            <?php }?>
                                            
                                            
                                        
                                            
                                        

                                            <?php ?>
                                            {{-- <i class="ti-pencil-alt" data-toggle="tooltip" title="" data-original-title="ti-pencil-alt"style="font-size:25px;"></i> --}}


                                            <?php
                                            if(!empty($rec_get_criteria_details->total)){
                                                $marks_percentage =round((($rec_get_criteria_details->obtained/$rec_get_criteria_details->total)*100),0);
                                            }
                                            ?>

                                            <?php $rec_teacher_courses->assessment_name; ?>
                                            <?php $rec_get_criteria_details->ccd_number;?>
                                            <strong style="font-size: 20px"><?=$rec_get_criteria_details->ccd_details;?></strong>

                                            <div class="mb-3">
                                                {{-- <a href="#"><i class="fa fa-star text-warning"></i></a>
                                                <a href="#"><i class="fa fa-star text-warning"></i></a>
                                                <a href="#"><i class="fa fa-star text-warning"></i></a>
                                                <a href="#"><i class="fa fa-star-half-o text-warning"></i></a>
                                                <a href="#"><i class="fa fa-star-o text-warning"></i></a> --}}

                                                <span class="fs-16 ml-3" >(<?=$rec_get_criteria_details->missing_students?> Students unmarked)</span>
                                                <span style="font-weight: bold;color:crimson">
                                                    <?php
                                                    if($rec_get_criteria_details->missing_approval_students != 0){
                                                        if($rec_get_criteria_details->assessment_id == 3 or $rec_get_criteria_details->assessment_id == 4){
                                                            echo $rec_get_criteria_details->missing_approval_students." Students Marks not Approved";
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>


                                        </div>

                                        <?php
                                            $count++;
                                        }}?>


                                    </div>
                                </div>
                            </div>
                            <?php
                            $sr_no++;
                            }
                            ?>


                        </div>
                    </div>
                </div>
        </div>
    </div>
    <?php }   ?>


<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModel"   data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="uploadFileModelLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadFileModelLabel">Upload File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="uploadAssignmentForm">
          <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" class="form-control" id="description" name="description">
            <div id="description-error" class="error-message text-danger"></div>
            <input type="hidden"  class="form-control" id="course_criteria_detail_id" name="course_criteria_detail_id">
            <input type="hidden" class="form-control" id="cr_co_id" name="cr_co_id">
          </div>
          <div class="form-group">
            <label for="fileInput">Choose File:</label>
            <input type="file" class="form-control-file" id="fileInput" name="file">
            <div id="file-error" class="error-message text-danger"></div>
          </div>
        </form>
        <div  id="fileStatus"  class="file-container text-center mt-4 mb-4">
          <div class="file-info">
            <i class="fa text-primary fa-file fa-5x"></i> <br>
            <span id="file_name"></span>
          </div>
          <div class="file-actions mt-3">
            <button type="button" class="btn btn-sm btn-danger" id="delete_google_drive_url">Delete</button>
            <a  id="google_drive_file_url"  target='__blank'class="btn btn-sm btn-primary" download>Download</a>
          </div>
        </div>
      </div>
      <h4 id="success_msg" class="text-success text-right mr-4"></h4>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeModel" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="uploadButton">Upload Now</button>
      </div>
    </div>
  </div>
</div>

{{-- check file modal  --}}


<div class="modal fade" id="smallmodal" tabindex="-1" role="dialog" aria-labelledby="smallmodal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <h3  id="smallmodal1">
    
            <i class="fa fa-file mr-4" style="font-size: 30px;"></i>
            <span style="font-size: 24px;">Files Required</span>
        </h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body pb-4 mt-3">
        <p class="alert alert-info">It is requested that you kindly upload the following files before finalizing the grading.</p>
        <table class="table table-bordered">
            <thead>
                <th scope="col" >Sr#</th>
                <th scope="col" >Type</th>
                <th scope="col" >Name</th>
            </thead>
            <tbody>
                @foreach ($checkNullFiles as $index => $nullFiles)
                <tr>
                    <td>
                        <p>{{$index}}</p>
                    </td>
                    <td>
                        <p>{{$nullFiles->assessment_name}}</p>
                    </td>
                    <td>
                        <strong>{{$nullFiles->ccd_details}}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
    </div>
    </div>
    </div>
    





<!-- {{--zain--}} -->
<div class="modal fade" id="courseMarksModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Marks Entry for <span id="assessment_name"> </span> (<span id="entrly_details" style="font-size: 20px;"> </span>)</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="marksEntryForm" name="marksEntryForm" role="form">
                        <div class="form-group row" style="margin-right:10px;">
                            <div class="col-md-2 col-sm-6 col-4 text-right">
                                <label for="cam_details" class="form-check-label"><b>Details:</b></label>
                                <input type="hidden" id="form_change_flag" name="form_change_flag" value="1">
                                <input type="hidden" id="co_id" name="co_id">
                                <input type="hidden" id="assessment_id_marks" name="assessment_id">
                                <input type="hidden" id="cam_number" name="cam_number">
                                <input type="hidden" id="course_criteria_id" name="course_criteria_id">
                                <input type="hidden" id="ccd_id" name="ccd_id">
                            </div>
                            <div class="col-md-10 col-sm-6 col-8">
                                <textarea rows="2" id="cam_details" maxlength="50" onkeyup="countChar(this)"  name="cam_details" class="form-control"></textarea>
                                    <div id="charNum"></div>
                              
                            </div>
                            <div class="col-md-2 col-sm-6 col-8 text-right" style="margin-top: 5px;">
                                <label for="cam_date" class="form-check-label"><b>Date:</b></label>
                            </div>
                            <div class="col-md-5 col-sm-6 col-8" style="margin-top: 5px;">
                                <input type="date" id="cam_date" name="cam_date" min="2018-01-01" max="2030-12-31" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-6 col-4 text-right" style="margin-top: 5px;">
                                <label for="total_marks" class="form-check-label"><b>Total Marks:</b></label>
                            </div>
                            <div class="col-md-2 col-sm-6 col-8" style="margin-top: 5px;">
                                <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="3" id="total_marks" name="total_marks" class="form-control" min="1" step="1" max="500" onchange="setMaxLimit()" required>
                                <input type="hidden" id="old_total_marks" name="old_total_marks" value="0" >
                                <small id="res_total_marks" class="text-danger" style="display:none;"></small>
                            </div>
                        </div>
                        @csrf
                        <table id="MarksEntryMainTable"  style="display: none" class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                            <thead class="bnu-header">
                            <tr>
                                <th class="tab-title-center">ID. </th>
                                <th class="tab-title-center">Sr. </th>
                                <th class="tab-title-center">Enroll No. </th>
                                <th class="tab-title">Name </th>
                                <th class="tab-title-center">Obt. Marks </th>
                            </tr>
                        </table>

                        <div class="alert alert-success" id="success-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-danger" id="error-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-success" id="wait-msg" style="display:none;margin-top:5px;">Please wait Processing.....'</div>
                        <input type="submit" value="Save Marks" id="marks_submit_btn" class="btn btn-primary btn-lg float-md-right" style="margin-top: 5px;">
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- make parent child for relative grading -->
        <div class="modal fade" id="parentchildModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Combine Course</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="parentChildForm" name="parentChildForm" role="form">
                            
                            @csrf
                            
                            <!-- <label for="courseSelect">Courses:</label>
                            <select id="yourSelectElementId" class="form-control"></select>
                                -->
    

                            
                        </form>
                    </div>
                </div>
            </div>
        </div>





        <div class="modal fade" id="gradeFinalizationConfirmModal" tabindex="-1" role="dialog" aria-labelledby="gradeFinalizationConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="gradeFinalizationConfirmModalLabel">Grades Finalization</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you have verified all students' marks in each assessment? Then move forward.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmGradeFinalization">Move Forward</button>
                    </div>
                </div>
            </div>
        </div>

@stop

@section('javascript')
<script type="text/javascript">

$('#gradeFinalizationConfirmModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    $('#confirmGradeFinalization').data('finalization-url', button.data('finalization-url'));
});

$('#confirmGradeFinalization').on('click', function() {
    var finalizationUrl = $(this).data('finalization-url');

    if (finalizationUrl) {
        window.location.href = finalizationUrl;
    }
});

function HodRequest(coID,CID,camNo,assID){

    sendGetRequest('#btn',null,'teacher/approval/'+coID+'/'+CID+'?cam='+camNo+'&assID='+assID,function(res){
       $(".req").attr("disabled", true);
      $('#req-hod').addClass('disabled');
        $.showAlert({title: "Marks Approval", body: 'Student Marks Approval Request has been Sent To Course HOD'});
    });

}
  function countChar(val) {
        var len = val.value.length;

        if (len >= 51) {
          val.value = val.value.substring(0, 51);
        } else {
          $('#charNum').text(50 - len);
        }
      };
function gradingDetailPending(co_id){
   // $.showAlert({title: "Hi", body: "Please press ok, if you like or dislike cookies."});
    sendGetRequest('#btn',null,'get-course-grading-process-details/'+co_id,function(res){
        if(res.status){
            console.log(res.data);
                var html = '<table>';
                $(res.data).each(function(index,row){
                    html += '<tr><td>'+row.assessment_name+' marks not entered yet<td></tr>';
                });
                html += '</table>';
        }else{
            html += 'No Criteria Defined'
        }

        $.showAlert({title: "Grading Process Detail", body: html});
    })
}
function openMarksModal3(course_id) {
                        //alert(course_id);
                        sendGetRequest('#btn', null, 'check-course-combine-faculty/' + course_id, function(res) {
                            if (res.status === 'error') {
                                // $('#errorMsg').html(res.message);
                                return false;
                            }
                            // Ensure the modal is opened
                            $("#parentchildModal").modal();

                            // Dynamically find or create the content in the modal
                            var modalBody = $("#parentchildModal .modal-body");
                            var checkboxContainerId = "courseCheckboxes"; // ID for the container holding checkboxes

                            // Clear the modal body to avoid duplication
                            modalBody.empty();

                            // Remove any existing checkboxes container to avoid duplication
                            modalBody.find("#" + checkboxContainerId).remove();

                            // Create a new container for checkboxes
                            var checkboxContainer = $('<div></div>')
                                .attr("id", checkboxContainerId)
                                .addClass("checkbox-container");

                            // Add a label for the checkboxes
                            var label = $('<label></label>').text("Select Courses:");

                            // Append label to modal body
                            modalBody.append(label);

                            // Append dynamic checkboxes to the container using the response
                            res.forEach(function(item) {
                                var optionText = item["get_course_name(co_id)"];
                                var optionValue = item["co_id"];

                                // Create a checkbox element for each course
                                var checkbox = $('<input>')
                                    .attr("type", "checkbox")
                                    .attr("id", "course_" + optionValue)
                                    .attr("value", optionValue)
                                    .attr("data-co_id", optionValue);

                                // Create a label for each checkbox
                                var checkboxLabel = $('<label></label>')
                                    .attr("for", "course_" + optionValue)
                                    .text(optionText);

                                // Append the checkbox and label to the container
                                var checkboxWrapper = $('<div></div>').append(checkbox).append(checkboxLabel);
                                checkboxContainer.append(checkboxWrapper);
                            });

                            // Append the checkboxes container to the modal body
                            modalBody.append(checkboxContainer);

                            // Add a Done button to the modal
                            var doneButton = $('<button></button>')
                                .attr("id", "doneButton")
                                .addClass("btn btn-primary mt-3")
                                .text("Done");

                            var spanMsg = $('<span></span>')
                            .attr("id", "successMsg")
                            .css({
                                "display": "block", // Ensures it's displayed as a block element
                                "margin-top": "10px", // Adds some spacing
                                "color": "green", // Sets the text color to green
                            });

                            var ErrorspanMsg = $('<span></span>')
                            .attr("id", "errorMsg")
                            .css({
                                "display": "block", // Ensures it's displayed as a block element
                                "margin-top": "10px", // Adds some spacing
                                "color": "red", // Sets the text color to green
                            });                  

                            // Append the Done button to the modal body
                            modalBody.append(doneButton);
                            modalBody.append(spanMsg);
                            modalBody.append(ErrorspanMsg);


                            doneButton.on('click', function() {
                                var selectedCoIds = [];
                                var totalCheckboxes = $("input[type='checkbox']").length;
                                
                                // Get all selected checkboxes
                                $("input[type='checkbox']:checked").each(function() {
                                    selectedCoIds.push($(this).val());
                                });

                                // Ensure all checkboxes are selected
                                if (selectedCoIds.length !== totalCheckboxes) {
                                    $('#errorMsg').html('Please select all courses before proceeding.');
                                    setTimeout(function() {
                                        $('#errorMsg').html('');
                                    }, 3000); // 3000 milliseconds = 3 seconds
                                    // alert("Please select all courses before proceeding.");
                                    return;
                                }
                                // alert('123');
                            
                                // Send AJAX request with the selected course IDs
                                $.ajax({
                                    url: '/combine-course',
                                    type: 'POST',
                                    data: {
                                        co_ids: selectedCoIds, // Send as an array
                                        current_co_id: <?=$co_id?>, // Pass current course ID
                                        course_id: course_id,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include the CSRF token
                                    },
                                    success: function(res) {
                                        if (res) {
                                            $('#successMsg').html('Course Combined Successfully');
                                            setTimeout(function() {
                                                window.location.href = '/'; // Redirect after 3 seconds
                                            }, 3000); // 3000 milliseconds = 3 seconds
                                        }
                                        // Add any additional functionality here
                                    },
                                    error: function(xhr, status, error) {
                                        alert("An error occurred: " + error); // Handle any errors
                                    }
                                });
                            });

                            // Log to confirm the checkboxes have been added
                            console.log('Checkboxes populated:', checkboxContainer.html());
                        });
                    }
// function openMarksModal3(course_id) {
//                         //alert(course_id);
//                         sendGetRequest('#btn', null, 'check-course-combine-faculty/' + course_id, function(res) {
//                             //alert(res.status);
//                            // Ensure the modal is opened
//                                 $("#parentchildModal").modal();

//                             // Dynamically find or create the select element in the modal
//                             var modalBody = $("#parentchildModal .modal-body");
//                             var selectElementId = "courseSelect"; // ID for the select element

//                              // Clear the modal body to avoid duplication
//                                 modalBody.empty();

//                             // Remove any existing select element to avoid duplication
//                             modalBody.find("#" + selectElementId).remove();

//                             // Create a new select element
//                             var selectElement = $('<select></select>')
//                                 .attr("id", selectElementId)
//                                 .addClass("form-control");

//                             // Add a default "Select Course" option
//                             var defaultOption = $('<option></option>')
//                                 .val("")
//                                 .text("Select Course")
//                                 .attr("disabled", true)
//                                 .attr("selected", true); // Makes it the default selected option
//                             selectElement.append(defaultOption);

//                             // Append dynamic options to the select element using the response
//                             res.forEach(function(item) {
//                                 var optionText = item["get_course_name(co_id)"];
//                                 var optionValue = item["co_id"];

//                                 // Create an option element and add a data attribute
//                                 var option = $('<option></option>')
//                                     .val(optionValue)
//                                     .text(optionText)
//                                     .attr("data-co_id", optionValue); // Attach custom data attribute

//                                 // Append the option to the select element
//                                 selectElement.append(option);
//                             });

//                             // Append the select element to the modal body
//                             modalBody.append('<label for="' + selectElementId + '">Select Course:</label>');
//                             modalBody.append(selectElement);

//                             // Add a change event to call a function with the selected co_id
//                             selectElement.on('change', function() {
//                                 var selectedCoId = $(this).find(':selected').data('co_id'); // Get co_id from data attribute
//                                 console.log("Selected co_id:", selectedCoId);

//                                 // Call your custom function with the selected co_id
//                                 handleCourseSelection(selectedCoId,'<?=$co_id?>');
//                             });

//                             var doneButton = $('<button></button>')
//                                 .attr("id", "doneButton")
//                                 .addClass("btn btn-primary mt-3")
//                                 .text("Done");

//                             // Append the "Done" button to the modal body
//                             modalBody.append(doneButton);

//                             doneButton.on('click', function() {
//                             var selectedCoId = selectElement.find(':selected').val(); // Get the selected value

//                             if (!selectedCoId) {
//                                 alert("Please select a course before proceeding.");
//                                 return;
//                             }

//                             // Send AJAX request with the selected course ID
//                             sendGetRequest('#btn', null, 'transfer-marks/' + selectedCoId +'/'+ <?=$co_id?>, function(res) {
//                                 if(res){
//                                     // window.location.href = '/'; 
//                                 }
//                             // Add any additional functionality here
//                             });
                           
//                         });  
//                             // Log to confirm the options have been added
//                             console.log('Options populated:', selectElement.html());

//                         })                           

// }

// function handleCourseSelection(co_id,current_co_id) {
//     // alert("You selected course ID: " + co_id);
//     sendGetRequest('#btn', null, 'combine-course/' + co_id +'/'+ current_co_id, function(res) {
//         if(res){
//             window.location.href = '/'; 
//         }
//     // Add any additional functionality here
//     });
// }
function openMarksModal(co_id,assess_type,assess_name,num,cc_id){

    sendGetRequest('#btn',null,'get-course-course-assessment-count/'+co_id+'/'+assess_type,function(res){
            //alert(res.status);
            var camNumber  = $('#cam_number').val(res['0']['count']);
            $('#entrly_details').html(res['0']['count']);
    })  

    $('#total_marks').val('');
    $("#courseMarksModal").modal();
    $('#co_id').val(co_id);
    $('#assessment_id_marks').val(assess_type);
    $('#assessment_name').html(assess_name);
   
}


$("#marksEntryForm").submit(function(e)
{

    $("#error-msg").css('display','none');
    $("#success-msg").css('display','none');
    //$("#marks_submit_btn").attr('disabled','true');
    $('#wait-msg').show();
        var show_msg = 'Please wait Processing.....';
      $("#wait-msg").html(show_msg);
    e.preventDefault();
    $.ajax({
        type:'POST',
        url:'{{url('teacher/courses/marks-enter')}}',
        data: $("#marksEntryForm").serializeArray(),
        success:function(data){
           // location.reload();

            var result          = data.split('-');
            var res             = result[0];
            var missing_marks   = result[1];

            if(res=='A'){
                  $("#wait-msg").css('display','none');
                $("#success-msg").css('display','block');
                var show_msg = '<strong>Success!</strong> Marks Saved Successfully';
                // window.open('/teacher/assessment-marks/17730/1/1');
                if(missing_marks>0)
                {
                    show_msg += '<br>'+missing_marks+' Students Marks Are Missing';
                }
                $("#success-msg").html(show_msg);
                setTimeout(function(){
                    $('#courseMarksModal').modal('hide');
                    location.reload();
                    getCourseCriteria();
                },2000);

            }else if(res=='B'){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Alert!</strong> No Change in the Marks');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else if(res=='C'){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Alert!</strong> Obtained Marks Must Be Less Than Total Marks');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else if(res=='D'){
                $("#success-msg").css('display','block');
                $("#success-msg").html('<strong>Success!</strong> Title & Date Saved Successfully');
                setTimeout(function(){
                    $('#courseMarksModal').modal('hide');
                    getCourseCriteria();
                },2000);
            }else if(res==0){
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong> Unchanged Marks, Please Correct & Try Again');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }else{
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong>'+data);
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }
        },
        error: function(data,status, response) {
            var result          = data.split('-');
            var res             = result[0];
            var missing_marks   = result[1];
            var errorData = jQuery.parseJSON(res.responseText);
            var error = errorData.errors;
            var msg = errorData.message;
            if(msg=='The given data was invalid.')
            {
                $("#error-msg").css('display','block');
                $("#error-msg").html('<strong>Error!</strong> Invalid Marks or Date/Details, Please Correct & Try Again');
                $('#error-msg').focus();
                $("#marks_submit_btn").removeAttr("disabled");
            }
            var tot_marks = error.total_marks;
            $("#res_total_marks").css('display','block');
            $("#res_total_marks").html(tot_marks);
            $('#res_total_marks').focus();

        }
    });
});


$('#uploadFileModel').on('show.bs.modal', function(event) {
                            try {
                                const button = $(event.relatedTarget);
                                const courseCriteriaDetailId = button.data('course-criteria-detail-id');
                                const coId = button.data('co-id');
                                const fileName = button.data('file-name');
                                const driveFileName = button.data('drive-file-name');
                                const googleDriveUrl = button.data('drive-url');
                                const description = button.data('file-description');

                                if(!driveFileName){
                                    $('#fileStatus').hide();
                                }
                                // alert(coId); // Place the alert statement here
                                $('#img-url').toggle(!!fileName); // Use toggle() instead of if-else
                                $('#file_name').text(fileName); // Use text() instead of innerHTML
                                $('#description').val(description); // Use val() instead of innerHTML
                                $('#course_criteria_detail_id').val(courseCriteriaDetailId);
                                $('#google_drive_file_url').attr('href', googleDriveUrl);
                                $('#google_drive_file_name').val(driveFileName);
                                $('#cr_co_id').val(coId);
                                $('#google_drive_url').attr('href', googleDriveUrl); // Chain methods for brevity
                            } catch (error) {
                                console.error('An error occurred:', error);
                                // Handle the error or display an error message as needed
                            }

                            });

                            $(document).ready(function() {
                            $('#uploadButton').click(function(e) {
                                        e.preventDefault();
                                        $('#uploadButton').prop('disabled', true).html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Uploading...');

                                        var form = $('#uploadAssignmentForm')[0];
                                        var formData = new FormData(form);
                                        $('#closeModel').hide();
                                    
                                        $.ajax({
                                            method: "POST",
                                            url: "/api/upload-assignment-file",
                                            data: formData,
                                            processData: false,
                                            contentType: false,
                                            headers: {
                                                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                                                "Accept": "application/json"
                                            }
                                        })
                                        .done(function(res) {
                                            if (res.status == true) {
                                                var span = document.getElementById("success_msg");
                                                span.innerHTML = '<i class="fa fa-check-circle"></i> <i class="fa fa-file"></i> ' + res.message;
                                                $('#uploadButton').prop('disabled', true).html('Uploaded');
                                                document.getElementById("uploadAssignmentForm").querySelector("input[type='file']").value = "";
                                                setInterval(function() {
                                                    window.location.reload();
                                                }, 3000);
                                            }
                                        })
                                        .fail(function(xhr) {
                                            $('#uploadButton').prop('disabled', false).html('Upload Now');
                                            $('#closeModel').show();

                                            if (xhr.status == 422) {
                                                var errors = xhr.responseJSON.errors;

                                                if (errors.hasOwnProperty('description')) {
                                                    $('#description-error').text(errors.description[0]);
                                                    setTimeout(function() {
                                                        $('#description-error').text('');
                                                    }, 3000);
                                                }

                                                if (errors.hasOwnProperty('file')) {
                                                    $('#file-error').text(errors.file[0]);
                                                    setTimeout(function() {
                                                        $('#file-error').text('');
                                                    }, 3000);
                                                }
                                            }

                                        });
                                    });

                            // delete upload file for assignmnets/quiz
                                    $('#delete_google_drive_url').click(function(e) {
                                    e.preventDefault();
                                    $('#uploadButton').prop('disabled', true).html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Deleting...');
                                    $('#closeModel').hide();
                                    var form = $('#uploadAssignmentForm')[0];
                                    var formData = new FormData(form);
                                    $('#uploadButton').prop('disabled', true).html('Deleting');
                                    $('#google_drive_file_url').hide();
                                    $('#file_status').show();
                                    $('#google_drive_url').hide();
                                    $('#delete_google_drive_url').hide();
                                    $.ajax({
                                        method: "POST",
                                        url: "/api/delete-assignment-file",
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        headers: {
                                            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                                            "Accept": "application/json"
                                        }
                                    }).done(function(res) {
                                        if (res.status) {
                                            var span = document.getElementById("success_msg");
                                            span.innerHTML = '<i class="fa fa-check-circle"></i> <i class="fa fa-file"></i> ' + res.message;

                                            $('#file_status').hide();
                                            setInterval(function() {
                                                location.reload();
                                            }, 3000);
                                        }
                                    }).fail(function(xhr) {
                                        $('#google_drive_url').show();
                                        $('#google_drive_file_url').show();
                                        if (xhr.status == 422) {
                                            var span = $('#success_msg');
                                            span.html(`<i class="mdi mdi-file-outline fs-20 mr-2"></i> File not deleted`);
                                            $('#file_status').hide();
                                            $('#delete_google_drive_url').removeAttr('style');
                                             $('#delete_google_drive_url').show();
                                            setInterval(function() {
                                                span.html('');
                                            }, 3000);
                                        }
                                    }).always(function() {
                                        $('#uploadButton').prop('disabled', true).html('Deleted');
                                    });
                                });

                            // delete upload file for assignmnets/quiz
                        });



</script>
@stop
