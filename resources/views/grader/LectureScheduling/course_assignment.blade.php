@extends('layouts.app',['pageTitle' => 'Course Assignment'])
@section('content')

{{-- @dd("123d"); --}}
<style>
    .form-group{
        margin-bottom: 0.2rem !important;
    }


    /* =====================================
   Course Assignment Section
===================================== */

#messages{
    width:100%;
}

/* Outer cards */
#messages > .container-fluid.card,
#messages + .card,
.page-body .container-fluid.card{
    padding:0 !important;
    margin:0 0 22px !important;
    border:0 !important;
    border-radius:12px;
    background:#ffffff;
    box-shadow:0 3px 16px rgba(0,0,0,.05);
    overflow:hidden;
}

/* Main heading */
#messages .card-header{
    min-height:64px;
    display:flex;
    align-items:center;
    padding:0 22px !important;
    border:0 !important;
    border-bottom:1px solid #E8ECEF !important;
    border-radius:0 !important;
    background:#17675F !important;
}

#messages .card-header h3{
    margin:0 !important;
    float:none !important;
    font-family:inherit !important;
    font-size:20px;
    font-weight:600;
    color:#ffffff;
}

/* Scroll area */
#reload{
    height:auto !important;
    max-height:650px;
    padding:0 !important;
    overflow:auto !important;
    scrollbar-width:thin;
    scrollbar-color:#BFCAC8 transparent;
}

#reload::-webkit-scrollbar{
    width:6px;
    height:6px;
}

#reload::-webkit-scrollbar-thumb{
    background:#BFCAC8;
    border-radius:999px;
}

#reload::-webkit-scrollbar-track{
    background:transparent;
}

/* Main assignment table */
#messages .table{
    width:100% !important;
    margin:0;
    border:0 !important;
    border-collapse:separate;
    border-spacing:0;
    background:#ffffff;
}

/* Existing Bootstrap row structure */
#messages .table tr.row{
    display:flex;
    align-items:stretch;
    margin:0 !important;
    border:0 !important;
}

/* Header row */
#messages .table tr.row:first-child{
    position:sticky;
    top:0;
    z-index:2;
    background:#17675F1A;
    border-bottom:1px solid #DDE5E8 !important;
}

#messages .table th{
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:54px;
    padding:14px 16px !important;
    border:0 !important;
    color:#17675F;
    font-size:14px;
    font-weight:600 !important;
    line-height:1.4;
    background:transparent;
}

/* Primary assignment row */
#messages .table td{
    display:flex;
    align-items:center;
    min-height:58px;
    padding:14px 16px !important;
    border:0 !important;
    border-bottom:1px solid #EDF1F3 !important;
    color:#3F4650;
    font-size:14px;
    line-height:1.5;
    background:#ffffff;
    word-break:break-word;
}

/* Row hover */
#messages .table tr.row:hover td{
    background:#F6FAF9;
}

/* Action columns */
#messages .table td[style*="text-align: center"]{
    justify-content:center;
}

/* Update and evaluation icons */
#messages .table td a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:40px;
    height:40px;
    border-radius:8px;
    background:#F1F7F6;
    text-decoration:none;
    transition:.2s ease;
}

#messages .table td a:hover{
    background:#E3F0ED;
    transform:translateY(-1px);
}

#messages .table td a .fa-pencil-square-o{
    color:#17675F !important;
    font-size:20px !important;
}

#messages .table td a .fa-file-text-o{
    color:#198754 !important;
    font-size:20px !important;
}

/* Details row */
#messages .table tr.row + tr.row td{
    min-height:72px;
    background:#FAFCFC;
    color:#4B5563;
    font-size:13px;
}

/* Description */
#messages .table tr.row + tr.row td:first-child{
    align-items:flex-start;
}

/* Late and resubmission values */
#messages .table tr.row + tr.row td div[style*="color:blue"]{
    margin-top:5px;
    color:#17675F !important;
    font-weight:600 !important;
}

/* Download link */
#messages .table tr.row + tr.row td a{
    width:auto;
    height:auto;
    padding:0;
    border-radius:0;
    background:transparent;
    color:#17675F !important;
    font-weight:600;
    text-decoration:none;
}

#messages .table tr.row + tr.row td a:hover{
    background:transparent;
    text-decoration:underline;
    transform:none;
}

/* Alternate assignment groups */
#messages .table-striped > tbody > tr:nth-of-type(odd){
    background:transparent;
}

/* =====================================
   Assessment Summary Table
===================================== */

#messages + .card,
#messages ~ .card{
    margin-top:22px;
}

#messages ~ .card .container-fluid{
    padding:0 !important;
}

#messages ~ .card .table{
    margin:0;
    border:0 !important;
}

#messages ~ .card .table tr.row:first-child{
    background:#17675F1A;
    border-bottom:1px solid #DDE5E8 !important;
}

#messages ~ .card .table th{
    min-height:52px;
    padding:14px 18px !important;
    border:0 !important;
    color:#17675F;
    font-size:14px;
    font-weight:600 !important;
}

#messages ~ .card .table td{
    min-height:52px;
    padding:14px 18px !important;
    border:0 !important;
    border-bottom:1px solid #EDF1F3 !important;
    color:#3F4650;
    font-size:14px;
    background:#ffffff;
}

#messages ~ .card .table tr.row:hover td{
    background:#F6FAF9;
}

/* Responsive */
@media(max-width:991px){

    #reload{
        overflow-x:auto !important;
    }

    #messages .table{
        min-width:950px;
    }

    #messages ~ .card .table{
        min-width:520px;
    }
}

@media(max-width:767px){

    #messages .card-header{
        min-height:58px;
        padding:0 16px !important;
    }

    #messages .card-header h3{
        font-size:18px;
    }

    #messages .table th,
    #messages .table td{
        padding:12px 13px !important;
    }
}


/* =========================================================
   COURSE ASSIGNMENT — FIGMA STYLE OVERRIDE
   Figma reference: Course Grading / Assignments
   Style only — HTML / PHP / JS unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   DESIGN TOKENS
   ========================================================= */

:root {
    --assignment-green: #17675F;
    --assignment-green-dark: #1F776E;
    --assignment-green-soft: rgba(23, 103, 95, 0.10);

    --assignment-text: #252525;
    --assignment-muted: #7A7A7A;

    --assignment-border: #E6E6E6;
    --assignment-light-border: #EDF1F3;

    --assignment-white: #FFFFFF;
    --assignment-bg: #F7FAF9;
}


/* =========================================================
   1. TYPOGRAPHY
   ========================================================= */

.page-body,
#messages,
#messages {
    font-family: 'Montserrat', sans-serif !important;
}


/* Course name at top */
.container-fluid > .card-header > .card-title {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.3 !important;

    color: #252525 !important;

    float: none !important;
}


/* First header only contains course name */
.container-fluid > .card-header:first-of-type {
    min-height: auto !important;

    margin: 0 !important;
    padding: 0 0 18px 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   2. LECTURES / STUDY MATERIALS / ASSIGNMENTS TABS
   Figma: 50px pill tabs
   ========================================================= */

.container-fluid > .card-header:nth-of-type(2) {
    min-height: auto !important;

    margin: 0 !important;
    padding: 0 0 15px 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-bottom: 2px solid rgba(23, 103, 95, 0.10) !important;

    border-radius: 0 !important;

    box-shadow: none !important;
}


/* Shared header nav */
.container-fluid > .card-header:nth-of-type(2) .nav,
.container-fluid > .card-header:nth-of-type(2) .nav-tabs,
.container-fluid > .card-header:nth-of-type(2) .nav-pills {
    display: flex !important;
    align-items: center !important;

    gap: 8px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
}


.container-fluid > .card-header:nth-of-type(2) .nav-item {
    margin: 0 !important;
}


/* Figma tab */
.container-fluid > .card-header:nth-of-type(2) .nav-link {
    position: relative !important;

    height: 50px !important;

    padding: 0 26px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border: 0 !important;
    border-radius: 50px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: rgba(23, 103, 95, 0.50) !important;

    text-decoration: none !important;

    transition:
        background-color .2s ease,
        color .2s ease !important;
}


/* Figma tab sizing */
.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(1) .nav-link {
    min-width: 154px !important;
}

.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(2) .nav-link {
    min-width: 208px !important;
}

.container-fluid > .card-header:nth-of-type(2)
.nav-item:nth-child(3) .nav-link {
    min-width: 195px !important;
}


/* Assignments active */
.container-fluid > .card-header:nth-of-type(2) .nav-link.active {
    background: #1F776E !important;

    color: #FFFFFF !important;
}


/* Active indicator */
.container-fluid > .card-header:nth-of-type(2) .nav-link.active::after {
    content: "";

    position: absolute;

    left: 0;
    right: 0;

    bottom: -17px;

    height: 4px;

    background: #17675F;

    border-radius: 5px;
}


.container-fluid > .card-header:nth-of-type(2)
.nav-link:hover:not(.active) {
    background: rgba(23, 103, 95, 0.14) !important;

    color: #17675F !important;
}


/* =========================================================
   3. ASSIGNMENT PAGE BODY
   ========================================================= */

.page-body {
    width: 100% !important;

    margin-top: 20px !important;

    background: transparent !important;
}


.page-body > .row {
    width: 100% !important;

    margin: 0 !important;

    display: block !important;
}


/* Outer bootstrap card */
.page-body > .row > .container-fluid.card {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;

    overflow: visible !important;
}


/* =========================================================
   4. MAIN ASSIGNMENT CARD
   Figma = white + 5px radius
   ========================================================= */

#messages {
    width: 100% !important;

    background: transparent !important;
}


#messages > .container-fluid.card {
    width: 100% !important;

    margin: 0 0 20px !important;
    padding: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


/* =========================================================
   5. COURSE ASSIGNMENT GREEN BAR
   Figma assignment section header = 52px
   ========================================================= */

#messages > .container-fluid.card > .card-header {
    width: 100% !important;

    min-height: 52px !important;
    height: 52px !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 5px 5px 0 0 !important;

    box-shadow: none !important;
}


/* Override inline Times New Roman */
#messages > .container-fluid.card > .card-header h3 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;

    float: none !important;
}


/* =========================================================
   6. TABLE SCROLL AREA
   ========================================================= */

#reload {
    width: 100% !important;

    height: auto !important;
    max-height: 650px !important;

    margin: 0 !important;
    padding: 0 !important;

    overflow-y: auto !important;
    overflow-x: hidden !important;

    background: #FFFFFF !important;

    scrollbar-width: thin;
    scrollbar-color: #BFCAC8 transparent;
}


#reload::-webkit-scrollbar {
    width: 5px;
}


#reload::-webkit-scrollbar-thumb {
    background: #BFCAC8;

    border-radius: 50px;
}


#reload::-webkit-scrollbar-track {
    background: transparent;
}


/* =========================================================
   7. MAIN ASSIGNMENT TABLE
   ========================================================= */

#messages .table {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* Remove bootstrap stripe */
#messages .table-striped > tbody > tr:nth-of-type(odd),
#messages .table-striped > tbody > tr:nth-of-type(even) {
    background: #FFFFFF !important;
}


/* Existing .row based table */
#messages .table tr.row {
    width: 100% !important;

    margin: 0 !important;

    display: flex !important;
    align-items: stretch !important;

    background: #FFFFFF !important;

    border: 0 !important;
}


/* =========================================================
   8. TABLE COLUMN HEADER
   Uses Figma dark green table system
   ========================================================= */

#messages .table tr.row:first-child {
    position: sticky !important;

    top: 0;

    z-index: 5;

    min-height: 42px !important;

    background: #17675F !important;

    border: 0 !important;
}


#messages .table tr.row:first-child th {
    min-height: 42px !important;

    padding: 8px 12px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #FFFFFF !important;

    text-align: center !important;
}


/* =========================================================
   9. PRIMARY ASSIGNMENT ROW
   Figma list row approximately 51–52px
   ========================================================= */

#messages .table td {
    min-height: 54px !important;

    padding: 11px 14px !important;

    display: flex !important;
    align-items: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E6E6E6 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #252525 !important;

    word-break: break-word !important;
}


/* Center cells that already have inline text-align */
#messages .table td[style*="text-align: center"] {
    justify-content: center !important;

    text-align: center !important;
}


/* Hover */
#messages .table tr.row:hover > td {
    background: rgba(23, 103, 95, 0.025) !important;
}


/* =========================================================
   10. ASSIGNMENT DETAIL ROW
   Description / Late / Re Submission / File
   ========================================================= */

/*
   The current markup has a primary row immediately followed
   by its detail row.
*/

#messages .table tr.row + tr.row > td {
    min-height: 72px !important;

    padding-top: 14px !important;
    padding-bottom: 14px !important;

    align-items: center !important;

    background: #FAFCFC !important;

    border-bottom: 1px solid #E6E6E6 !important;

    font-size: 13px !important;
    line-height: 1.5 !important;

    color: #4B5563 !important;
}


/* Description */
#messages .table tr.row + tr.row > td:first-child {
    align-items: flex-start !important;

    text-align: left !important;
}


#messages .table tr.row + tr.row b {
    font-weight: 500 !important;

    color: #252525 !important;
}


/* Late + Resubmission label */
#messages .table tr.row + tr.row td div:first-child {
    font-size: 12px !important;
    font-weight: 500 !important;

    color: #7A7A7A !important;
}


/* Yes / No */
#messages .table tr.row + tr.row
td div[style*="color:blue"] {
    margin-top: 4px !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


/* =========================================================
   11. UPDATE / CHECK EVALUATION ACTIONS
   ========================================================= */

#messages .table td a {
    width: 34px !important;
    height: 34px !important;

    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(23, 103, 95, 0.08) !important;

    border: 1px solid rgba(23, 103, 95, 0.10) !important;
    border-radius: 5px !important;

    color: #17675F !important;

    text-decoration: none !important;

    box-shadow: none !important;

    transition:
        background-color .15s ease,
        color .15s ease !important;
}


#messages .table td a:hover {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;

    transform: none !important;
}


/* Existing pencil */
#messages .table td a .fa-pencil-square-o {
    margin: 0 !important;

    font-size: 16px !important;

    color: #17675F !important;
}


/* Evaluation */
#messages .table td a .fa-file-text-o {
    margin: 0 !important;

    font-size: 16px !important;

    color: #17675F !important;
}


#messages .table td a:hover .fa-pencil-square-o,
#messages .table td a:hover .fa-file-text-o {
    color: #FFFFFF !important;
}


/* =========================================================
   12. DOWNLOAD ASSIGNMENT FILE
   ========================================================= */

#messages .table tr.row + tr.row td a {
    width: auto !important;
    height: auto !important;

    padding: 0 !important;

    display: inline !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #17675F !important;

    text-decoration: underline !important;
}


#messages .table tr.row + tr.row td a:hover {
    background: transparent !important;

    color: #125A53 !important;
}


/* =========================================================
   13. ASSESSMENT SUMMARY CARD
   ========================================================= */

#messages ~ .card {
    width: 100% !important;

    margin: 20px 0 0 !important;

    padding: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


#messages ~ .card > .container-fluid {
    padding: 0 !important;
}


#messages ~ .card .table {
    width: 100% !important;

    margin: 0 !important;

    border: 0 !important;

    background: #FFFFFF !important;
}


/* Summary header */
#messages ~ .card .table tr.row:first-child {
    min-height: 52px !important;

    margin: 0 !important;

    background: #17675F !important;

    border: 0 !important;
}


#messages ~ .card .table tr.row:first-child th {
    min-height: 52px !important;

    padding: 0 18px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;

    color: #FFFFFF !important;
}


/* Summary data */
#messages ~ .card .table td {
    min-height: 52px !important;

    padding: 12px 18px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #E6E6E6 !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* =========================================================
   14. EDIT ASSESSMENT MODAL
   ========================================================= */

#updatemodal .modal-dialog {
    max-width: 620px !important;

    margin: 40px auto !important;
}


#updatemodal .modal-content {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 16px 40px rgba(0, 0, 0, .14) !important;
}


/* Modal Header */
#updatemodal .modal-header {
    min-height: 60px !important;

    padding: 16px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #17675F !important;

    border: 0 !important;
}


#updatemodal .modal-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;
}


/* Close icon */
#updatemodal .close {
    width: 34px !important;
    height: 34px !important;

    margin: 0 !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(255, 255, 255, .12) !important;

    border: 0 !important;
    border-radius: 50% !important;

    color: #FFFFFF !important;

    font-size: 22px !important;
    line-height: 1 !important;

    opacity: 1 !important;
}


/* =========================================================
   15. MODAL FORM
   ========================================================= */

#updatemodal .modal-body {
    padding: 20px !important;

    background: #FFFFFF !important;
}


#updatemodal .form-group {
    margin-bottom: 16px !important;
}


#updatemodal label {
    margin-bottom: 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #252525 !important;
}


#updatemodal .form-control {
    min-height: 40px !important;

    padding: 9px 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


#updatemodal textarea.form-control {
    min-height: 100px !important;

    resize: vertical;
}


#updatemodal .form-control:focus {
    outline: 0 !important;

    border-color: #17675F !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, .08) !important;
}


/* Radio */
#updatemodal input[type="radio"] {
    accent-color: #17675F;
}


/* Upload notes */
#updatemodal small.text-danger {
    margin-top: 4px !important;

    font-size: 11px !important;
    line-height: 1.4 !important;
}


/* Existing uploaded file link */
#updatemodal #href {
    display: inline-block !important;

    margin-bottom: 8px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #17675F !important;

    text-decoration: underline !important;
}


/* =========================================================
   16. MODAL FOOTER / BUTTONS
   ========================================================= */

#updatemodal .modal-footer {
    padding: 16px 20px 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;

    gap: 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #EEEEEE !important;
}


#updatemodal .modal-footer .btn {
    min-width: 100px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 18px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
}


/* Close */
#updatemodal .btn-secondary {
    background: #FFFFFF !important;

    border: 1px solid #D1D5DB !important;

    color: #374151 !important;
}


/* Save changes */
#updatemodal .btn-primary {
    background: #17675F !important;

    border: 1px solid #17675F !important;

    color: #FFFFFF !important;
}


#updatemodal .btn-primary:hover {
    background: #125A53 !important;

    border-color: #125A53 !important;
}


/* =========================================================
   17. NO ASSESSMENT FOUND
   ========================================================= */

#messages > span h1 {
    margin: 40px 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;

    color: #7A7A7A !important;

    text-align: center !important;
}


/* =========================================================
   18. RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    #reload {
        overflow-x: auto !important;
    }

    #messages .table {
        min-width: 900px !important;
    }

    #messages ~ .card {
        overflow-x: auto !important;
    }

    #messages ~ .card .table {
        min-width: 520px !important;
    }

}


@media (max-width: 767px) {

    .container-fluid > .card-header:nth-of-type(2) .nav,
    .container-fluid > .card-header:nth-of-type(2) .nav-tabs,
    .container-fluid > .card-header:nth-of-type(2) .nav-pills {
        display: grid !important;

        grid-template-columns: 1fr !important;

        gap: 8px !important;
    }


    .container-fluid > .card-header:nth-of-type(2)
    .nav-item,

    .container-fluid > .card-header:nth-of-type(2)
    .nav-link {
        width: 100% !important;
        min-width: 100% !important;
    }


    .container-fluid > .card-header:nth-of-type(2)
    .nav-link.active::after {
        display: none !important;
    }


    #updatemodal .modal-dialog {
        margin: 15px !important;
    }


    #updatemodal .modal-body {
        padding: 16px !important;
    }

}
/* Keep table header and records on one row */
#reload {
    overflow-x: auto !important;
}

#messages #reload > .table {
    min-width: 980px !important;
}

#messages #reload > .table tr.row {
    flex-wrap: nowrap !important;
}

/* Keep ID and Due Date columns hidden */
#messages #reload > .table th[style*="display:none"],
#messages #reload > .table th[style*="display: none"],
#messages #reload > .table td[style*="display:none"],
#messages #reload > .table td[style*="display: none"] {
    display: none !important;
}
/* Detail row alignment with upper record row */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
}

/* Description spans Assessment + Title columns */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td:first-child {
    flex: 0 0 50% !important;
    max-width: 50% !important;
    justify-content: flex-start !important;
    text-align: left !important;
}

/* Late Submission, Re Submission and Upload */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td:not(:first-child) {
    flex: 0 0 16.666667% !important;
    max-width: 16.666667% !important;
}

/* Keep label and value properly aligned */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td div {
    margin: 0 !important;
}
/* Description exactly under Assessments/Quiz column */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td:first-child {
    flex: 0 0 25% !important;
    max-width: 25% !important;
    justify-content: center !important;
    text-align: center !important;
}

/* Keep Title column space empty before Late Submission */
#messages #reload > .table tr.row:not([id]):not(:first-child) > td:nth-child(2) {
    margin-left: 25% !important;
}
    </style>
<div class="container-fluid" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
    <div class="card-header" style="padding-left: 0 !important;">
        <h3 class="card-title " style="padding-left: 0 !important;">{{$criteria[0]->course_code.' '.$criteria[0]->course_cd_name }} ( {{$criteria[0]->co_section}} )</h3>
    </div>
    <div class="card-header" style="border-radius:10px;padding-left:0px;padding-right:0px;">
    @include('grader/LectureScheduling.lecture_scheduling_header', ['active_tab_name' => 'Assignments'])
    </div>
</div>



<div class="page-body">
  <div class="row mx-auto justify-content-center align-items-center flex-column ">   
  <div class="container-fluid card" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
        <div class="tab-pane fade show active" id="messages">
        <?php
        if($chkassess){?>
      <div class="container-fluid card" style="border:solid 0px #0C0;padding:revert;  margin-left:auto; margin-right:auto; width:100%;">
            <div class="card-header" style=" background-color: #f2f6f9 !important;padding-left:0 !important;color:#334155; border-top-left-radius: 7px; border-top-right-radius: 7px;">
            <h3 style=" color:#334155 !important;display: inline-block; float:left" >Course Assignment</h3>
            </div>
            <div class="container-fluid" id="reload" style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
            <table class="table table-bordered table-striped" style="width:100%!important;border-left: none!important;border-right: none!important">
            
            <tr class="row" >
                <th style="display:none;" class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">ID</th>
                <th style="text-align: center;font-weight:bold " class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Assessments</th>
                <th style="text-align: center;font-weight:bold" class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Title</th>
                <th style="text-align: center;font-weight:bold" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Marks</th>
                <th style="text-align: center;display: none" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Due Date</th>
                <th style="text-align: center;font-weight:bold" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Update</th>
                <th style="text-align: center;font-weight:bold" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Check Evaluation</th>
            </tr>
        
            <?php
             //   print_r($all_uploads);
             
            foreach($all_uploads as $rec){
              $timestamp = strtotime( $rec->due_date);
              $newDate = date('d-M-y', $timestamp);
            ?>
            <tr class="row" style="border-color:blue; border:solid 4px;">
            <tr class="row"  id="{{$rec->id}}">
            <td style="display: none" class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">{{$rec->id}}</td>
            <td style="text-align: center" class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">{{$rec->assessment_name.' # '.$rec->ccd_number}}</td>
            <td style="text-align: center" class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">{{$rec->ccd_details}}</td>
            <td style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">{{$rec->marks}}</td>
            <td style="text-align: center;display: none" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">{{$newDate}}</td>
            <td style="text-align: center;"  class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                <!-- <?=$rec->id;?> -->
                <a href="#" data-toggle="modal" data-target="#updatemodal" onclick="btn(<?=$rec->id;?>);" ><i class="fa fa-pencil-square-o fa-2x" style="color: steelblue"></i></a>
            </td>

                <td style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <a href="{{url('teacher/check_assignment',['title'=>$rec->id,'id'=> $rec->co_id])}}"   ><i class="fa fa-file-text-o fa-2x" style="color: green"></i></a>
                </td>
            </tr>
            <tr class="row">
                <?php if($rec->description == ''){?>
                    <td class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"><b>Description: </b>Not Entered</td>
                    <?php }else {?>
                <td class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"><b>Description: </b>{{$rec->description}}</td>
                    <?php } ?>
                <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <div align="center"><b>Late Submission:</b></div>  &nbsp;
                    <div style="color:blue;font-weight:bold;margin-top: 0 !important;" align="center">{{$rec->late_submission_allowed}}</div>
                </td>
                <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <div align="center"><b>Re Submission:</b></div>  &nbsp;
                    <div style="color:blue;font-weight:bold;margin-top: 0 !important;" align="center">{{$rec->re_submission_allowed}}</div>
                    
                </td>
                    <?php if($rec->file_original_name == ''){?>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b>No Uploads</b></td>
                    <?php }else {?>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b><a href="{{ url('docs/'.$rec->file_name) }}" style="color: blue;cursor: pointer;">Download {{$rec->file_original_name}}</a></b></td>
                    <?php } ?>

            </tr>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="card" >
    <div class="container-fluid" style="padding-left: 0 !important; padding-right:0 !important;">
        <table class="table table-bordered table-striped" style="border-left: none!important;border-right: none!important">
            <tr class="row">
                <th style="text-align: center;font-weight:bold" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Assessments</th>
                <th style="text-align: center;font-weight:bold" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Total</th>
            </tr>
            <tr class="row">
                <?php
                foreach($count as $row){
                ?>
                <td style="text-align: center" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">{{$row->assessment_name}}</td>
                <td style="text-align: center" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">{{$row->count}}</td>
                <?php
                }
                ?>
            </tr>
        </table>
    </div>
</div>
 
<!-- MODAL START -->
<div class="modal fade" id="updatemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Assessment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="update_form"  id="update_form">
                    <input type="hidden" id="edit_assessment_id" value="" name="assessment_id" >
                    @csrf
                        <div class="form-group">
                            <div class="row">
                                <div style="display: none" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                    <label for="assessment_id" style="font-weight: bold">Assessments</label>
                                    <select class="form-control"  id="assessment_idd" name="assessment_id_edit">
                                        <option value="">--Select Assessment--</option>
                                        @foreach($criteria as $rec_course_criteria)
                                            <option value="{{$rec_course_criteria->assessment_id}}">{{ $rec_course_criteria->assessment_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                    <label for="marked" style="font-weight: bold;">Marks <b class="text-danger">*</b> </label>
                                    <input type="text"  onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"  maxlength="3" class="form-control assess-form-input" id="marked" name="marks" min="0" max="500" maxlength="5"  placeholder="Marks">
                                    {{-- <input type="number" class="form-control assess-edit-input" id="marked" name="marks" min="0" max="500" maxlength="5"  placeholder="Marks"> --}}
                                </div>
                                <div class="col-9 col-sm-9 col-md-9 col-lg-9 col-xl-9">
                                    <input type="hidden" id="fcu_id">
                                    <label for="exampleFormControlInput1" style="font-weight: bold">Title <b class="text-danger">*</b> </label>
                                    <input type="text" class="form-control assess-edit-input"  id="title_models" name="title2"  placeholder="Title Here . . ." maxlength="200">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;font-weight: bold">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style="padding:0px !important;">
                                    <label for="exampleFormControlInput1"><b>Due Date</b> <b class="text-danger">*</b></label>
                                    <input type="date" class="form-control assess-edit-input optional-inputs" id="duedated"  class="form-control" name="duedate" placeholder="Due Date">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="is_file_edit" name="is_file" value="">                  
                      
                        <div style="display:none" >
                            <label for="is_assignment_edit">
                                <input id="is_assignment_edit" name="is_assignment" data-toggle="collapse"  data-target="#detailed" type="checkbox"   value="yes" > &nbsp;Is Assignment</label>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1" ><b>Description</b> <b class="text-danger">*</b> (Word limit 500)</label>
                            <textarea class="form-control assess-edit-input optional-inputs" id="descriptioned" name="description" rows="2"  maxlength="800"></textarea>
                        </div>                            
                        <div class="form-group"> 
                            <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;">
                                <div class="col-10 col-sm-6" style="padding:0px !important;font-weight: bold">
                                    <label for="latesubmission"><b>Late Submission Allowed</b></label><br />
                                    <input type="radio" id="latesub-yes-edit"   name="latesub" value="Yes"> &nbsp;Yes
                                    <input type="radio" id="latesub-no-edit"  name="latesub" value="No"> &nbsp;No
                                </div>
                                <div class="col-10 col-sm-6" style="padding:0px !important;font-weight: bold">
                                    <label for="resubmission"><b>Re Submission Allowed</b></label><br />
                                    <input type="radio"  name="resub" id="resub-yes-edit" value="Yes"> &nbsp;Yes
                                    <input type="radio" name="resub" id="resub-no-edit" value="No"> &nbsp;No
                                    <input type="hidden" name="co_id"  value="{{$all_uploads[0]->co_id}}">
                                </div>
                                <div></div>
                            </div>
                        </div>                       
                        <div class="form-group">
                            <label for="exampleFormControlInput1" style="font-weight: bold">UploadFile</label>

                            <a id="href"  class="file-remove-btn" style="color:black;cursor: pointer;" >
                                  <b>  Uploaded File(Download) </b>
                                     <label  id="lastuploadfile" style="cursor:pointer"></label>
                            </a>
                            <a href="javascript:void(0);"  class="file-remove-btn" onclick="remove_edit_file();" id="remove_file_btn"  ></a> 
                            <input type="file" name="uploadfile2" id="uploadfile2_edit"  accept=".xlsx,.docx,.pptx,.jpg,.jpeg,.pdf,.png,.zip" class="form-control assess-edit-input optional-inputs" >
                            <small class="form-text text-danger">Only xlsx, docx, pptx, jpg, jpeg, pdf, png and zip file type allow </small>
                            <small class="form-text text-danger">Maximum file size limit 200 MB!</small>
                        </div>
                 
                    </form>
                </div>
                
                <div class="modal-footer">
                    <div class="assignment_info"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
                    <button id="btn"  name="btn" type="button" class="btn btn-primary upload_btn" onclick="updtbtn()" >Save changes</button>
                </div>
            </div>
        </div>
    </div>
<!-- MODAL START -->
    <?php }else{ ?>
   <span style="text-align: center;font-weight: bold"><h1 style="margin-top: 0.66em;">No Assessment Found</h1></span>
    <?php } ?>

          </div>
          </div>
          </div>
          </div>
  
@stop

@section('javascript')
<script type="text/javascript">
 function remove_edit_file(){
        $("#href").attr('href','');
     }

function btn(id){
       //  console.log('nabeel mahamood',id); return false;
        $.ajax({
            type:'get',
            url:'{{url('EditAssessment')}}/'+id,
            success:function(data){

                $("#fcu_id").val(data['0']['id']);
                $("#title_models").val(data['0']['ccd_details']);
                var downloadPath = '<?=url('/docs/')?>';
                var filepath = downloadPath+'/'+data['0']['file_name'];

                if( !(data['0']['file_original_name']) ){
                    $('.file-remove-btn').hide();
                }

                $("#href").attr('href',filepath);
                // $("#lastuploadfile").text(data['0']['file_original_name']);
                $("#descriptioned").val(data['0']['description']);
                $("#marked").val(data['0']['marks']);
                $('#edit_assessment_id').val(data['0']['assessment_id']);
                $("#duedated").val(data['0']['due_date']);

                if(data['0']['re_submission_allowed'] == 'No'){
                    $('#resub-no-edit').prop('checked',true);
                }else{
                    $('#resub-yes-edit').prop('checked',true);
                }

                if(data['0']['late_submission_allowed'] == 'No'){
                    $('#latesub-no-edit').prop('checked',true);
                }else{
                    $('#latesub-yes-edit').prop('checked',true);
                }
                if(data['0']['is_assignment'] == 1){
                    $('#is_assignment_edit').prop('checked',true);
                    $('#detailed').addClass('show');
                }



            }
        });
    }

    function updtbtn(){

    

        $('.msg-div').hide();
        var marks       = $("#marked").val();
      //  console.log('marks',marks);
        var title       = $("#title_models").val();
        var assessment  = $("#assessment_id").val();
        var file        = $("#uploadfile2_edit").val();
        var dueDate     = $("#duedated").val();
        var description = $("#descriptioned").val();
        var typeArr     = ["xlsx","docx","pptx","jpg","jpeg","pdf","png","zip"];
        var old_file = $('#href').attr('href');


        if( $('#detailed').hasClass('show') ){
            if(old_file == ''){
                $('#is_file_edit').val('yes');
            }else{
                $('#is_file_edit').val('no');
            }        
        }else{
            $('#is_file_edit').val('no');
        }


        $('.msg-div').hide();
            $('.form-control').removeClass('is-invalid');
            var error = 0;
            if( (marks.trim() == '') || (marks < 1) ){
                if( !( isFloat() || isInteger() ) ){
                    error++;
                    $('#marked').addClass('is-invalid');
                    $('#edit-fail-msg span').html('Please define valid marks!');
                    return false;
                }
            }else if(title.trim() == ''){
                error++;
                $('#title_models').addClass('is-invalid');
                $('#edit-fail-msg span').html('Please provide some title!');
            }

   
                    if(file.trim() != ''){
                        var fileInfoArr = file.split(".");
                        var len         = fileInfoArr.length;
                        var type        = fileInfoArr[len-1];
                        var index       = typeArr.indexOf(type);

                        if(index < 0){
                            error++;
                            $('#uploadfile2_edit').addClass('is-invalid');
                            $('#edit-fail-msg span').html('Only xlsx, docx, jpg, jpeg, png and pdf are allow !');
                        }

                        

                        var fileInput =  document.getElementById("uploadfile2_edit");
                        var file_size = 0;
                        try{
                            file_size = fileInput.files[0].size/1024;
                        }catch(e){
                            var objFSO = new ActiveXObject("Scripting.FileSystemObject");
                            var e = objFSO.getFile( fileInput.value);
                            var fileSize = e.size;  
                            file_size = fileSize/1024;
                        }
                       
                        if(file_size > 2048){
                            error++;
                            $('#uploadfile2_edit').addClass('is-invalid');
                            $('#edit-fail-msg span').html('File size must be less then 200MB!');
                        }
                     }
                     
                     

            if( $('#detailed').hasClass('show') ){ // #rule if user click on detail then he must provide the whole detail 
                    if(description.trim() == ''){
                        error++;
                        $('#descriptioned').addClass('is-invalid');
                        $('#edit-fail-msg span').html('Please provide description!');
                    }

                    if(dueDate.trim() == ''){
                        error++;
                        $('#duedate').addClass('is-invalid');
                        $('#edit-fail-msg span').html('Please provide due date!');
                    }
                    $('#is_file').val('yes');
                }else{
                    $('#is_file').val('no');
                }

                if( error > 0 ){
                    $('#edit-fail-msg').show();
                    return false;
                }


                save_btn_action('loading');


        var bi_form = document.querySelector('#update_form');
        var fd      = new FormData(bi_form);

        $("#btn").prop("disabled", true);
        $("#btn").html('Loading');

       







        $.ajax({
            type:'post',
            processData:false,
            contentType:false,
            data:fd,
            url:'<?=url('uploadassessment')?>?fcu_id='+$("#fcu_id").val(),
            success: function (data) {


                         
                        $("#btn").html('Save Change');
                        $("#btn").attr("disabled", false);
                        if(data.status){
                           
                            showAlert('.assignment_info',data.message,'success');
                            location.reload();
                        }else{
                            showAlert('.assignment_info',data.message,'danger');
                        }
                    
                       
                    
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#btn").html('Save Change');
                        $("#btn").attr("disabled", false);
                        defaultFailBlock('.assignment_info', jqXHR, textStatus, errorThrown);
                        $("#btn").prop("disabled", false);
                    }
        });




    }
    function save_btn_action(action){
         //   alert(action);
            if(action == 'loading'){
                $(".upload_btn").prop('disabled', true);
                // $(".upload_btn").text('Save');
                $(".upload_btn").text('Loading');
            }else{
                $(".upload_btn").prop('disabled', false);
               //  $(".upload_btn .save").show();
                $(".upload_btn").text('Loading');
            }
        }


</script>
@stop