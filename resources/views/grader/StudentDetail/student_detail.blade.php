@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

{{-- @dd("123"); --}}
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<style>
  .student-actions{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:15px;
    width:100%;
}

.student-actions > div:last-child{
    display:flex;
    gap:10px;
}

.student-actions a{
    display:inline-block;
    padding:8px 14px;
    background:#fff;
    border-radius:6px;
    text-decoration:none;
    cursor:pointer;
    box-shadow:0 1px 4px rgba(0,0,0,.1);
}

.student-actions .fa-file-pdf-o{
    color:#dc3545;
}

.student-actions .fa-file-excel-o{
    color:#28a745;
}

@media (max-width:768px){
    .student-actions{
        flex-direction:column;
        align-items:flex-start;
        gap:12px;
    }

    .student-actions > div:last-child{
        width:100%;
    }
}


/* Inline wrapper ko normal block banayein */
div[style="display:inline"] {
    display: block !important;
    width: 100%;
}

/* Heading section */
div[style="display:inline"] > div:first-child {
    width: 100%;
}

/* Total students + action buttons row */
div[style="display:inline"] > div:nth-child(2) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-top: 10px;
}

/* Total Students badge */
div[style="display:inline"] > div:nth-child(2) > div:first-child {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 6px;
    background-color: #0b7167;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
}

/* PDF and Excel buttons container */
div[style="display:inline"] > div:nth-child(2) > div:last-child {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-left: auto;
}

/* PDF and Excel buttons */
div[style="display:inline"] > div:nth-child(2) > div:last-child a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 7px 14px;
    border: 0;
    border-radius: 7px;
    background-color: #ffffff;
    color: #242b38 !important;
    text-decoration: none !important;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

/* Inline icon styling override */
div[style="display:inline"] > div:nth-child(2) > div:last-child a i {
    color: #242b38 !important;
    font-family: inherit;
    font-size: 12px !important;
    font-style: normal;
    font-weight: 600;
}

/* Screenshot ki tarah icons hide karne ke liye */
div[style="display:inline"] > div:nth-child(2) > div:last-child a i::before {
    display: none;
}

/* Hover effect */
div[style="display:inline"] > div:nth-child(2) > div:last-child a:hover {
    background-color: #f8f9fa;
    color: #242b38 !important;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
}

/* Mobile */
@media (max-width: 576px) {
    div[style="display:inline"] > div:nth-child(2) {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
    }

    div[style="display:inline"] > div:nth-child(2) > div:last-child {
        width: 100%;
        margin-left: 0;
    }
}


/* =========================================================
   ONLY STUDENTS DISPLAY TABLE
   Targets: .card-body > .table-responsive > .table
========================================================= */

.card-body > .table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border-radius: 12px;
    background: #ffffff;
    -webkit-overflow-scrolling: touch;
}

.card-body > .table-responsive > .table {
    width: 100%;
    min-width: 900px;
    margin: 0;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
}

/* Header */
.card-body > .table-responsive > .table thead tr {
    background: #176e65;
}

.card-body > .table-responsive > .table thead th {
    height: 38px;
    padding: 11px 20px;
    border: 0 !important;
    background: #176e65;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: 0.2px;
    text-align: center;
    text-transform: capitalize;
    vertical-align: middle;
    white-space: nowrap;
}

.card-body > .table-responsive > .table thead th:first-child {
    padding-left: 24px;
    text-align: left;
    border-top-left-radius: 12px;
}

.card-body > .table-responsive > .table thead th:last-child {
    border-top-right-radius: 12px;
}

/* Rows */
.card-body > .table-responsive > .table tbody tr {
    height: 64px;
    background: #ffffff;
    transition: background-color 0.18s ease;
}

.card-body > .table-responsive > .table tbody tr:nth-child(even) {
    background: #f8fafb;
}

.card-body > .table-responsive > .table tbody tr:hover {
    background: #f1f7f6;
}

/* Cells */
.card-body > .table-responsive > .table tbody td {
    padding: 12px 20px;
    border: 0;
    border-bottom: 1px solid #e7ebef;
    color: #111827;
    font-size: 12px;
    line-height: 1.35;
    vertical-align: middle;
    white-space: nowrap;
}

.card-body > .table-responsive > .table tbody tr:last-child td {
    border-bottom: 0;
}

/* Name column */
.card-body > .table-responsive > .table tbody td:first-child {
    display: flex !important;
    align-items: center;
    min-width: 270px;
    padding-left: 24px;
}

.card-body > .table-responsive > .table .avatar-lg {
    width: 40px;
    min-width: 40px;
    height: 40px;
    margin-right: 10px !important;
    border: 0;
    border-radius: 50%;
    background: #c9ced4;
    object-fit: cover;
    box-shadow: none;
}

.card-body > .table-responsive > .table tbody td:first-child .ml-3 {
    min-width: 0;
    margin-top: 0 !important;
    margin-left: 0 !important;
}

.card-body > .table-responsive > .table tbody td:first-child h5 {
    margin: 0 0 2px !important;
    color: #111827 !important;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
}

.card-body > .table-responsive > .table tbody td:first-child p {
    margin: 0 !important;
    color: #8b95a5 !important;
    font-size: 9px !important;
    line-height: 1.3;
}

/* Hide email line, screenshot style */
.card-body > .table-responsive > .table tbody td:first-child p:last-child {
    display: none;
}

/* Attendance */
.card-body > .table-responsive > .table tbody td:nth-child(2) {
    min-width: 135px;
    text-align: center;
}

.card-body > .table-responsive > .table .chart-circle {
    width: 31px !important;
    height: 31px !important;
    margin-right: auto !important;
    margin-left: auto !important;
}

.card-body > .table-responsive > .table .chart-circle-value {
    font-size: 9px !important;
    font-weight: 700;
}

/* Status columns */
.card-body > .table-responsive > .table tbody td:nth-child(3),
.card-body > .table-responsive > .table tbody td:nth-child(4),
.card-body > .table-responsive > .table tbody td:nth-child(5) {
    min-width: 125px;
    text-align: center !important;
    font-size: 10px;
    font-weight: 600;
}

/* Green status effect */
.card-body > .table-responsive > .table tbody td:nth-child(3),
.card-body > .table-responsive > .table tbody td:nth-child(5) {
    color: #14a96f;
 
    background-repeat: no-repeat;
}

/* Blue course status effect */
.card-body > .table-responsive > .table tbody td:nth-child(4) {
    color: #3f82f7;

    background-repeat: no-repeat;
}

/* Action column */
.card-body > .table-responsive > .table tbody td:last-child {
    min-width: 130px;
    padding-right: 24px;
    text-align: center !important;
}

/* View Marks button */
.card-body > .table-responsive > .table .btn-light {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 90px;
    min-height: 30px;
    padding: 7px 14px;
    border: 0;
    border-radius: 7px;
    background: #176e65;
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 500;
    line-height: 1;
    text-decoration: none !important;
    cursor: pointer;
    box-shadow: none;
    transition:
        background-color 0.18s ease,
        transform 0.18s ease,
        box-shadow 0.18s ease;
}

.card-body > .table-responsive > .table .btn-light:hover,
.card-body > .table-responsive > .table .btn-light:focus {
    background: #105b54;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(23, 110, 101, 0.18);
    outline: none;
}

/* Responsive */
@media (max-width: 767px) {
    .card-body > .table-responsive > .table {
        min-width: 850px;
    }

    .card-body > .table-responsive > .table thead th,
    .card-body > .table-responsive > .table tbody td {
        padding-right: 14px;
        padding-left: 14px;
    }

    .card-body > .table-responsive > .table thead th:first-child,
    .card-body > .table-responsive > .table tbody td:first-child {
        padding-left: 18px;
    }
}








/* =========================================================
   MARKS DETAIL MODAL — ONLY #myModal
========================================================= */

#myModal {
    padding: 18px !important;
    background: rgba(15, 23, 42, 0.68);
    overflow-y: auto;
}

/* Dialog */
#myModal .modal-dialog {
    width: 100% !important;
    max-width: 1080px;
    margin: 10px auto;
    padding: 0 !important;
}

/* Main modal card */
#myModal .modal-content {
    width: 100% !important;
    overflow: hidden;
    border: 0;
    border-radius: 14px;
    background: #ffffff;
    box-shadow:
        0 24px 60px rgba(15, 23, 42, 0.28),
        0 8px 20px rgba(15, 23, 42, 0.12);
}

/* AJAX response wrapper */
#myModal #marks_details {
    max-height: calc(100vh - 60px);
    overflow-x: hidden;
    overflow-y: auto;
    background: #ffffff;
    color: #1f2937;
    scrollbar-width: thin;
    scrollbar-color: #b8c6c4 #eef3f2;
}

/* Scrollbar */
#myModal #marks_details::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

#myModal #marks_details::-webkit-scrollbar-track {
    background: #eef3f2;
}

#myModal #marks_details::-webkit-scrollbar-thumb {
    border: 2px solid #eef3f2;
    border-radius: 10px;
    background: #9eb6b2;
}

/* =========================================================
   MODAL HEADER
   Works with common Bootstrap/modal response markup
========================================================= */

#myModal #marks_details .modal-header,
#myModal #marks_details > div:first-child {
    position: relative;
    /* min-height: 72px; */
    /* padding: 20px 58px 20px 22px; */
    border: 0;
    border-bottom: 1px solid #dce7e5;
    background: linear-gradient(
        90deg,
        #eef7f5 0%,
        #e4f1ef 55%,
        #d7e9e6 100%
    );
}

/* Registration / title */
#myModal #marks_details .modal-title,
#myModal #marks_details > div:first-child h1,
#myModal #marks_details > div:first-child h2,
#myModal #marks_details > div:first-child h3,
#myModal #marks_details > div:first-child h4,
#myModal #marks_details > div:first-child h5 {
    margin: 0;
    color: #176e65;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
}

/* Close button */
#myModal #marks_details .close,
#myModal #marks_details [data-dismiss="modal"] {
    position: absolute;
    top: 50%;
    right: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 0;
    border-radius: 8px;
    /* background: rgba(255, 255, 255, 0.72); */
    color: #334155;
    font-size: 22px;
    font-weight: 600;
    line-height: 1;
    opacity: 1;
    transform: translateY(-50%);
    cursor: pointer;
    transition:
        background-color 0.18s ease,
        color 0.18s ease,
        transform 0.18s ease;
}

#myModal #marks_details .close:hover,
#myModal #marks_details [data-dismiss="modal"]:hover {
    background: #ffffff;
    color: #176e65;
    transform: translateY(-50%) scale(1.04);
}

/* =========================================================
   TABLES INSIDE MODAL
========================================================= */

#myModal #marks_details table {
    width: 100%;
    margin: 0;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
}

#myModal #marks_details table thead tr {
    background: #176e65;
}

#myModal #marks_details table thead th {
    padding: 13px 16px;
    border: 0 !important;
    border-right: 1px solid rgba(255, 255, 255, 0.14) !important;
    background: #176e65;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.25;
    text-align: center;
    text-transform: capitalize;
    vertical-align: middle;
    white-space: nowrap;
}

#myModal #marks_details table thead th:last-child {
    border-right: 0 !important;
}

/* If response uses heading rows inside tbody */
#myModal #marks_details table tbody tr:first-child th,
#myModal #marks_details table tbody tr.section-heading td,
#myModal #marks_details table tr.table-heading th {
    padding: 13px 16px;
    border: 0 !important;
    background: #176e65;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-transform: capitalize;
}

/* Body rows */
#myModal #marks_details table tbody tr {
    background: #ffffff;
    transition: background-color 0.16s ease;
}

#myModal #marks_details table tbody tr:nth-child(even) {
    background: #f8fafb;
}

#myModal #marks_details table tbody tr:hover {
    background: #f0f7f6;
}

/* Cells */
#myModal #marks_details table tbody td,
#myModal #marks_details table tbody th {
    padding: 14px 16px;
    border: 0 !important;
    border-right: 1px solid #e7ebef !important;
    border-bottom: 1px solid #e7ebef !important;
    color: #334155;
    font-size: 12px;
    line-height: 1.4;
    vertical-align: middle;
}

#myModal #marks_details table tbody td:last-child,
#myModal #marks_details table tbody th:last-child {
    border-right: 0 !important;
}

/* Numeric columns */
#myModal #marks_details table tbody td:nth-child(1),
#myModal #marks_details table tbody td:nth-child(2),
#myModal #marks_details table tbody td:nth-child(3) {
    text-align: center;
}

/* First text-heavy column stays left aligned */
#myModal #marks_details table tbody td:first-child {
    text-align: left;
}

/* Absence table first column */
#myModal #marks_details table:first-of-type tbody td:first-child {
    width: 80px;
    text-align: center;
}

/* Assessment table first column */
#myModal #marks_details table:last-of-type tbody td:first-child {
    width: 48%;
    text-align: center;
    font-weight: 500;
}

/* Section separation */
#myModal #marks_details table + table {
    margin-top: 18px;
    border-top: 10px solid #f3f7f6 !important;
}

/* =========================================================
   LOADER
========================================================= */

#myModal #loading {
    position: absolute;
    inset: 0;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 220px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(2px);
}

#myModal #loading .spinner4 {
    transform: scale(0.9);
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {
    #myModal {
        padding: 8px !important;
    }

    #myModal .modal-dialog {
        margin: 0 auto;
    }

    #myModal .modal-content {
        border-radius: 10px;
    }

    #myModal #marks_details {
        max-height: calc(100vh - 16px);
        overflow-x: auto;
    }

    #myModal #marks_details .modal-header,
    #myModal #marks_details > div:first-child {
        min-height: 62px;
        padding: 16px 50px 16px 16px;
    }

    #myModal #marks_details .modal-title,
    #myModal #marks_details > div:first-child h1,
    #myModal #marks_details > div:first-child h2,
    #myModal #marks_details > div:first-child h3,
    #myModal #marks_details > div:first-child h4,
    #myModal #marks_details > div:first-child h5 {
        font-size: 17px;
    }

    #myModal #marks_details table {
        min-width: 680px;
    }

    #myModal #marks_details table thead th,
    #myModal #marks_details table tbody td,
    #myModal #marks_details table tbody th {
        padding: 11px 12px;
    }
}

/* =========================================================
   STUDENT DETAILS / ENROLLED STUDENTS
   FIGMA STYLE OVERRIDE
   Figma frame: 406:2

   IMPORTANT
   ---------------------------------------------------------
   - Existing structure unchanged
   - Nothing added
   - Nothing removed
   - Nothing hidden
   - Nothing force-shown
   - Existing PHP / JS / AJAX unchanged
   - Existing modal behavior unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

div[style="display:inline"],
.card-body > .table-responsive {
    --student-green: #17675F;

    --student-heading: #111827;
    --student-text: #111827;
    --student-muted: #6B7280;

    --student-white: #FFFFFF;
    --student-row-alt: #FAFBFF;

    --student-border: #E5E7EB;

    --student-success: #10B981;
    --student-success-bg: rgba(209, 250, 229, 0.50);

    --student-blue: #3B82F6;
    --student-blue-bg: rgba(219, 234, 254, 0.50);

    --student-red: #EF4444;
    --student-red-bg: rgba(254, 226, 226, 0.50);

    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   2. MAIN TOP AREA
   Keep exact current DOM structure
   ========================================================= */

div[style="display:inline"] {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   3. STUDENT DETAILS TITLE
   Figma = 20px SemiBold
   ========================================================= */

div[style="display:inline"]
> div:first-child
> h5 {

    margin: 0 0 4px !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #111827 !important;
}


/* =========================================================
   4. COURSE NAME
   Figma = 18px Regular / #6B7280
   ========================================================= */

div[style="display:inline"]
> div:first-child
> .card-title {

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #6B7280 !important;
}


/* =========================================================
   5. TOTAL STUDENTS + ACTIONS ROW
   ========================================================= */

div[style="display:inline"]
> div:nth-child(2) {

    width: 100% !important;

    margin-top: 10px !important;
    margin-bottom: 25px !important;

    align-items: center !important;
}


/* =========================================================
   6. TOTAL STUDENTS PILL
   Figma:
   green #17675F
   13px SemiBold
   radius 8px
   ========================================================= */

div[style="display:inline"]
> div:nth-child(2)
> div:first-child {

    min-height: 29px !important;

    padding: 6px 12px !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 17px !important;

    color: #FFFFFF !important;

    white-space: nowrap;
}


/* =========================================================
   7. PDF / EXCEL ACTION GROUP
   Existing links remain exactly the same
   ========================================================= */

div[style="display:inline"]
> div:nth-child(2)
> div:last-child {

    align-items: center !important;

    gap: 8px !important;

    margin-left: auto !important;
}


/* =========================================================
   8. PRINT PDF / EXPORT EXCEL BUTTONS
   Figma = white 37px buttons / radius 8px
   ========================================================= */

div[style="display:inline"]
> div:nth-child(2)
> div:last-child
> a {

    min-height: 37px !important;

    margin: 0 !important;
    padding: 0 16px !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    color: #111827 !important;

    text-decoration: none !important;

    cursor: pointer !important;
}


/* Existing <i> content remains exactly as-is */
div[style="display:inline"]
> div:nth-child(2)
> div:last-child
> a i {

    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    color: #111827 !important;
}


div[style="display:inline"]
> div:nth-child(2)
> div:last-child
> a:hover {

    background: #FFFFFF !important;

    color: #17675F !important;

    box-shadow:
        0 3px 10px rgba(15, 23, 42, 0.05) !important;
}


div[style="display:inline"]
> div:nth-child(2)
> div:last-child
> a:hover i {

    color: #17675F !important;
}


/* =========================================================
   9. VISIBLE STUDENTS TABLE WRAPPER
   Figma = 16px radius white card

   Only actual visible table is targeted.
   Export-only hidden table is not touched.
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive {

    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 16px !important;

    box-shadow: none !important;

    overflow-x: auto !important;
}


/* =========================================================
   10. TABLE BASE
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table {

    width: 100% !important;

    min-width: 1000px !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   11. TABLE HEADER
   Figma:
   #17675F
   11px Bold
   16px vertical / 32px horizontal
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead tr {

    background: #17675F !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th {

    height: 52px !important;

    padding: 16px !important;

    vertical-align: middle !important;

    background: #17675F !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    color: #FFFFFF !important;
text-transform: capitalize !important;

    white-space: nowrap !important;
}


/* Name */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:first-child {

    padding-left: 32px !important;

    text-align: left !important;

    border-top-left-radius: 16px !important;
}


/* Other headings */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:not(:first-child) {

    text-align: center !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:last-child {

    border-top-right-radius: 16px !important;
}


/* =========================================================
   12. FIGMA COLUMN PROPORTIONS
   Existing 6 columns remain unchanged
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:first-child {

    min-width: 265px !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:nth-child(2) {

    width: 120px !important;
    min-width: 120px !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:nth-child(3),

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:nth-child(4),

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:nth-child(5) {

    width: 150px !important;
    min-width: 150px !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
thead th:last-child {

    width: 140px !important;
    min-width: 140px !important;
}


/* =========================================================
   13. TABLE ROWS
   Exact Figma = 84px
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody tr {

    height: 84px !important;

    background: #FFFFFF !important;

    transition: background-color .15s ease !important;
}


/* Alternating Figma row */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody tr:nth-child(even) {

    background: #FAFBFF !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody tr:hover {

    background: rgba(23, 103, 95, 0.025) !important;
}


/* =========================================================
   14. BODY CELLS
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td {

    height: 84px !important;

    padding: 16px !important;

    vertical-align: middle !important;

    background: transparent !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #111827 !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody tr:last-child td {

    border-bottom: 0 !important;
}


/* =========================================================
   15. NAME COLUMN
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:first-child {

    min-width: 265px !important;

    padding-left: 32px !important;

    align-items: center !important;

    text-align: left !important;
}


/* =========================================================
   16. STUDENT AVATAR
   Figma = 40 × 40 circle
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.avatar-lg {

    width: 40px !important;
    min-width: 40px !important;

    height: 40px !important;

    margin: 0 12px 0 0 !important;

    object-fit: cover !important;

    background: #CCCCCC !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;
}


/* Existing name/info wrapper */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:first-child
.ml-3 {

    margin-top: 0 !important;
    margin-left: 0 !important;

    padding: 0 !important;
}


/* =========================================================
   17. STUDENT NAME
   Figma = 14px SemiBold
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:first-child h5 {

    margin: 0 0 2px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #111827 !important;
}


/* =========================================================
   18. ENROLLMENT / EXISTING SECONDARY TEXT
   No visibility changes are made.
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:first-child p {

    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #6B7280 !important;
}


/* =========================================================
   19. ATTENDANCE
   Figma = 44px container / ~40px progress ring
   Existing plugin values/colors remain
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(2) {

    width: 120px !important;
    min-width: 120px !important;

    text-align: center !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.chart-circle {

    width: 44px !important;
    height: 44px !important;

    margin: 0 auto !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.chart-circle-value {

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    color: #111827 !important;
}


/* =========================================================
   20. STATUS COLUMNS
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(3),

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(4),

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(5) {

    width: 150px !important;
    min-width: 150px !important;

    text-align: center !important;
}


/* Common status pills */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(3) span,

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(4) span,

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(5) span {

    min-height: 24px !important;

    padding: 4px 10px !important;

    border: 0 !important;
    border-radius: 99px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 16px !important;

    white-space: nowrap !important;
}


/* Student Status */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(3) span {

    background: rgba(209, 250, 229, .50) !important;

    color: #10B981 !important;
}


/* Course Status */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(4) span {

    background: rgba(219, 234, 254, .50) !important;

    color: #3B82F6 !important;
}


/* Semester Status */
.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:nth-child(5) span {

    background: rgba(209, 250, 229, .50) !important;

    color: #10B981 !important;
}


/* =========================================================
   21. ACTION COLUMN
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
tbody td:last-child {

    width: 140px !important;
    min-width: 140px !important;

    padding-right: 32px !important;

    text-align: center !important;
}


/* =========================================================
   22. VIEW MARKS
   Exact Figma:
   37px height
   #17675F
   radius 8
   14px SemiBold
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.btn-light {

    min-width: 108px !important;

    height: 37px !important;
    min-height: 37px !important;

    margin: 0 !important;
    padding: 0 16px !important;

    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;

    text-decoration: none !important;
}


.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.btn-light:hover,

.card-body[style*="padding-left:0"]
> .table-responsive
> .table
.btn-light:focus {

    background: #125D56 !important;

    color: #FFFFFF !important;

    box-shadow: none !important;

    transform: none !important;
}


/* =========================================================
   23. NO STUDENT FOUND
   Existing empty state remains exactly present
   ========================================================= */

.card-body[style*="padding-left:0"]
> .table-responsive
> h1 {

    margin: 0 !important;

    padding: 45px 20px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;

    color: #6B7280 !important;

    text-align: center !important;
}


/* =========================================================
   24. RESPONSIVE
   No column/content is removed.
   Table scrolls horizontally instead.
   ========================================================= */

@media (max-width: 991.98px) {

    .card-body[style*="padding-left:0"]
    > .table-responsive
    > .table {

        min-width: 1000px !important;
    }

}


@media (max-width: 767.98px) {

    div[style="display:inline"]
    > div:first-child
    > h5 {

        font-size: 20px !important;
    }


    div[style="display:inline"]
    > div:first-child
    > .card-title {

        font-size: 16px !important;
    }


    div[style="display:inline"]
    > div:nth-child(2) {

        gap: 12px !important;
    }


    div[style="display:inline"]
    > div:nth-child(2)
    > div:last-child {

        gap: 8px !important;
    }


    .card-body[style*="padding-left:0"]
    > .table-responsive
    > .table
    thead th,

    .card-body[style*="padding-left:0"]
    > .table-responsive
    > .table
    tbody td {

        padding-right: 12px !important;
        padding-left: 12px !important;
    }


    .card-body[style*="padding-left:0"]
    > .table-responsive
    > .table
    thead th:first-child,

    .card-body[style*="padding-left:0"]
    > .table-responsive
    > .table
    tbody td:first-child {

        padding-left: 20px !important;
    }

}
.icon-dropshadow-secondary {
    filter: none !important;
}
</style>
<div class="">
  <div class="" style="display:inline">
  @if(count($studentsList)>0)
    <div>
    <h5>Student Details</h5>
    <h3 class="card-title" style="margin-bottom: 17px !important;"> <?php echo ( count($studentsList) > 0) ? $studentsList[0]->course_name : ''; ?></h3>

  </div>
    <div>
    <div>
      <!-- Total No of Students: <?php echo ( count($studentsList) > 0) ? count($studentsList) : '0'; ?>      -->
      Total No. of Students: <?php echo ( count($studentsList) > 0) ? count($studentsList) : '0'; ?>     
    </div>
    <div>
    <a  type="button" target="_blank" href="{{route('/course_enrolled_students', ['co_id' => $studentsList[0]->co_id]) }}">  <i style="color:red" class="fa fa-file-pdf-o fa-2x"  aria-hidden="true"> Print PDF</i> </a>
    <a type="button"   onclick="ExportToExcel('xlsx')"><i style="color:green" class="fa fa-file-excel-o fa-2x" aria-hidden="true">Print Excel</i></a>  
    </div>
  </div>
  </div>
  <div class="card-body" style="padding-left:0;padding-right:0;">
    <div class="table-responsive">
     
      <table class="table table-vcenter text-nowrap mb-0 border">
        <thead>
          <tr>
            <th class="border-bottom-0">Name</th>            
            <th class="text-center">Attendance Percentage</th>
            <th class="text-center">Student Status</th>
            <th class="text-center">Course Status</th>
            <th class="text-center">Semester Status</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($studentsList as $rec)
          @php
          $file_path = '/students_pics/'.$rec->sai_enrollment_no.'.jpg';
          //  echo $file_path;exit;
          if(!file_exists(public_path($file_path))){
              $file_path = '/students_pics/noimage.png';
          }  
          $attenadance_percentage = round($rec->attendance_percentage,0);
          // $marks_percentage = round(($rec_list['obtained_marks']/$studentsList[0]['total_marks'])*100,0);
          if($attenadance_percentage>80){
                  $graph_type_name    = "success";
                  $graph_color        = "2dce89";

          }elseif($attenadance_percentage>=75){
                  $graph_type_name    = "primary";
                  $graph_color        = "4454c3";
          }else{
                  $graph_type_name    = "secondary";
                  $graph_color        = "f72d66";
          }
          @endphp
          {{-- data-trigger="<?=($rec->sai_remarks=='')?'':'hover'?>" --}}
          {{-- data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom"  data-title="Student Description (<?=$rec->sai_enrollment_no?>) "  data-content="<?=($rec->sai_remarks=='')?'':$rec->sai_remarks ?>" --}}
          <tr>
            <td class="d-flex">
              <img class="avatar-lg rounded-circle mr-3" src="{{$file_path}}" alt="Image description">
              <div class="ml-3 mt-2">
                <h5 class="mb-0 text-dark">{{$rec->spi_name}}</h5>
                <p class="mb-0  fs-13 text-muted">{{$rec->sai_enrollment_no}}</p>
                <p class="mb-0  fs-13 text-muted">{{strtolower($rec->sai_enrollment_no)}}@bnu.edu.pk</p>
              </div>
            </td>
            <td>
              <div class="mx-auto chart-circle chart-circle-xs chart-circle-<?=$graph_type_name?> mt-sm-0 mb-0 icon-dropshadow-secondary" data-value="{{$attenadance_percentage}}" data-thickness="5" data-color="#<?=$graph_color?>">
                <div class="mx-auto chart-circle-value text-center">{{$attenadance_percentage}}%</div>
              </div>
            </td>
            <td class="text-center"> <span style="
    padding: 4px 10px;
    background-color: #D1FAE580;
    border-radius: 99px;
">{{$rec->sai_student_status}} </span></td>
            <td align="center" > <span style="
    padding: 4px 10px;
    background-color: #E2E8F0;
    border-radius: 99px;
"> <?php echo ( $rec->sc_status == 'A') ? 'Enrolled' : $rec->sc_status; ?></span></td>
            <td align="center"> <span style="
    padding: 4px 10px;
    background-color: #F0F0F0;
    border-radius: 99px;
"> {{$rec->ssr_semester_status}}</span></td>
  </div>
            <td class="text-right"><a class="btn btn-light"class="btn btn-primary" data-toggle="modal" data-target="#myModal" onclick="viewMarks('<?=$studentsList[0]->main_co_id?>','<?=$rec->sai_enrollment_no?>')">View Marks</a></td>
          </tr>
         @endforeach
        </tbody>
      </table>
      @else
        <h1>No Student Found</h1>
        @endif
    </div>
  </div>
</div>


<div class="card-body" style="display:none">
  <table   style="border:1px solid black;border-collapse:collapse;" width="100%" id="tbl_exporttable_to_xls">
      <thead>
        
          <tr>
          <th colspan="9"></th>
          
          <tr>
          <th style="text-align:center;" scope="col">Sr.No</th>
          <th style="text-align:center;" scope="col">Name</th>
          <th style="text-align:center;" scope="col">Reg</th>
          <th style="text-align:center;" scope="col">Email </th>
          
      

          </tr>
      </thead>
      <tbody id="result-body" >
          <?php $count = 1; ?>
          @foreach($studentsList as $row)
          <tr>
              <td  style="text-align:center;" >{{$count++}}</td>
              <td style="text-align:left;" >{{$row->spi_name}}</td>
              <td style="text-align:center;" >{{$row->sai_enrollment_no}}</td>
              <td style="text-align:center;" >{{strtolower($row->sai_enrollment_no)}}@bnu.edu.pk</td>
          </tr>
          @endforeach
      </tbody>
  </table>
</div>

{{-- Modal Start --}}
<div class="modal" id="myModal" style="padding:0px !important;width: 100%; margin-left: 0px; margin-right: 0px;">
  <div class="modal-dialog modal-lg" style="padding: 0px !important;width: 100%;">
      <div class="modal-content" style=" width: 100%;">


  
        <div id="loading" class="dimmer active">
          <div class="spinner4">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
          </div>
        </div>
          <div id="marks_details"></div>
      

      </div>
  </div>
</div>

{{-- Modal end here --}}

@stop


@section('javascript')
<script type="text/javascript">
function ExportToExcel(type, fn, dl) {
    var elt = document.getElementById('tbl_exporttable_to_xls');
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
    return dl ?
      XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
      XLSX.writeFile(wb, fn || ('MySheetName.' + (type || 'xlsx')));
 }

function viewMarks(gh_co_id,roll_no) {

            $('#loading').show();
          
            $('#marks_details').html('');
            var co_id = gh_co_id
           
            $.ajax({
                type: 'get',
                url: '{{url('/marksdetail')}}/'+co_id+'/'+roll_no,

                success: function(data){
                    $('#loading').hide()
                    $('#marks_details').html(data);

                }
            });
        }



</script>

@stop
