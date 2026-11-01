@extends('layouts.app',['pageTitle' => 'Course Assessment Marks'])
{{-- @dd("123"); --}}
@section('content')
<div id="for_key_up_down">
<div class="page-body">
<?php
//print_r($studentsList);exit;
 $sc_co_id = my_simple_crypt($co_id, 'e');


 if(count($studentsList)>0){
?>
<style>
    .profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}























/* =========================================================
   MARKS ENTRY CARD — ONLY #cardDisplayControl
========================================================= */

#cardDisplayControl {
    overflow: hidden;
    border: 0;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
}

/* Card header */
#cardDisplayControl > .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin: 0;
    padding: 16px 20px;
    border: 0;
    border-bottom: 1px solid #e7ebef;
    background: linear-gradient(
        90deg,
        #eef7f5 0%,
        #e5f1ef 55%,
        #dcecea 100%
    );
}

/* Marks Entry title */
#cardDisplayControl > .card-header .card-title {
    margin: 0;
    color: #1f2937;
    font-size: 17px;
    font-weight: 700;
    line-height: 1.3;
}

/* Header warning */
#cardDisplayControl > .card-header > span {
    color: #d14343 !important;
    font-size: 11px;
    font-weight: 600 !important;
    line-height: 1.4;
    text-align: right;
}

/* Card body */
#cardDisplayControl > .card-body {
    padding: 0;
    background: #ffffff;
}

/* Table wrapper */
#cardDisplayControl > .card-body > .table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border-radius: 0;
    background: #ffffff;
    -webkit-overflow-scrolling: touch;
}

/* Main table */
#cardDisplayControl .table-responsive > .table {
    width: 100%;
    min-width: 900px;
    margin: 0;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
}

/* Table header */
#cardDisplayControl .table thead tr {
    background: #176e65;
}

#cardDisplayControl .table thead th {
    height: 40px;
    padding: 11px 20px;
    border: 0 !important;
    background: #176e65;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700 !important;
    line-height: 1.2;
    letter-spacing: 0.2px;
    text-align: center;
    text-transform: uppercase;
    vertical-align: middle;
    white-space: nowrap;
}

#cardDisplayControl .table thead th:first-child {
    padding-left: 24px;
    text-align: left;
}

/* Table rows */
#cardDisplayControl .table tbody tr {
    height: 70px;
    background: #ffffff;
    transition: background-color 0.18s ease;
}

#cardDisplayControl .table tbody tr:nth-child(even) {
    background: #f8fafb;
}

#cardDisplayControl .table tbody tr:hover {
    background: #f1f7f6;
}

/* Table cells */
#cardDisplayControl .table tbody td {
    height: 70px;
    padding: 12px 20px;
    border: 0;
    border-bottom: 1px solid #e7ebef;
    color: #111827;
    font-size: 12px;
    line-height: 1.35;
    vertical-align: middle;
    white-space: nowrap;
}

#cardDisplayControl .table tbody tr:last-child td {
    border-bottom: 0;
}

/* =========================================================
   STUDENT COLUMN
========================================================= */

#cardDisplayControl .table tbody td:first-child {
    display: table-cell !important;
    min-width: 275px;
    padding-left: 24px;
    vertical-align: middle;
}

/* Avatar */
#cardDisplayControl .table .avatar-lg {
    display: inline-block;
    width: 34px;
    min-width: 34px;
    height: 34px;
    margin-right: 10px !important;
    border: 0;
    border-radius: 50%;
    background: #c9ced4;
    object-fit: cover;
    vertical-align: middle;
    box-shadow: none;
}

/* Student text block */
#cardDisplayControl .table tbody td:first-child .ml-3 {
    display: inline-block;
    margin-top: 0 !important;
    margin-left: 0 !important;
    vertical-align: middle;
}

/* Student name */
#cardDisplayControl .table tbody td:first-child h5 {
    margin: 0 0 2px !important;
    color: #111827 !important;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
}

/* Enrollment number */
#cardDisplayControl .table tbody td:first-child p {
    margin: 0 !important;
    color: #8b95a5 !important;
    font-size: 9px !important;
    line-height: 1.3;
}

/* =========================================================
   PERCENTAGE COLUMN
========================================================= */

#cardDisplayControl .table tbody td:nth-child(2) {
    min-width: 130px;
    text-align: center;
}

#cardDisplayControl .table .chart-circle {
    width: 32px !important;
    height: 32px !important;
    margin-right: auto !important;
    margin-left: auto !important;
}

#cardDisplayControl .table .chart-circle-value {
    font-size: 9px !important;
    font-weight: 700;
}

/* =========================================================
   MARKS INPUT
========================================================= */

#cardDisplayControl .table tbody td:nth-child(3) {
    min-width: 160px;
    text-align: center;
}

/* Override inline width:30% */
#cardDisplayControl .table tbody td:nth-child(3) .marks {
    display: block;
    width: 82px !important;
    height: 34px;
    margin: 0 auto;
    padding: 6px 10px;
    border: 1px solid #d8e0e7;
    border-radius: 7px;
    background: #ffffff;
    color: #111827;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    box-shadow: none;
    transition:
        border-color 0.18s ease,
        box-shadow 0.18s ease;
}

#cardDisplayControl .table tbody td:nth-child(3) .marks:focus {
    border-color: #176e65;
    outline: none;
    box-shadow: 0 0 0 3px rgba(23, 110, 101, 0.12);
}

/* Remove unnecessary br spacing */
#cardDisplayControl .table tbody td:nth-child(3) br {
    display: none;
}

/* Validation message */
#cardDisplayControl .table tbody td:nth-child(3) small {
    display: block;
    min-height: 14px;
    margin-top: 4px;
    font-size: 9px;
    line-height: 1.2;
}

/* Existing negative margin reset */
#cardDisplayControl .table tbody td:nth-child(3) > div:last-child {
    margin-top: 0 !important;
}

/* =========================================================
   STATUS + HOD APPROVAL
========================================================= */

#cardDisplayControl .table tbody td:nth-child(4),
#cardDisplayControl .table tbody td:nth-child(5) {
    min-width: 135px;
    text-align: center !important;
    font-size: 10px;
    font-weight: 600;
}

/* Saved/check icons */
#cardDisplayControl .table tbody td:nth-child(4) .fa-check {
    color: #14a96f;
    font-size: 16px;
}

/* Pending / saved labels */
#cardDisplayControl .table tbody td:nth-child(4) strong,
#cardDisplayControl .table tbody td:nth-child(4) span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 58px;
    padding: 5px 10px;
    border-radius: 999px;
    background: #def9ed;
    color: #14a96f !important;
    font-size: 10px;
    font-weight: 600;
    line-height: 1;
}

/* HOD approval labels */
#cardDisplayControl .table tbody td:nth-child(5) strong {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 78px;
    padding: 5px 10px;
    border-radius: 999px;
    background: #def9ed;
    color: #14a96f !important;
    font-size: 10px;
    font-weight: 600;
    line-height: 1;
}

/* Not approved */
#cardDisplayControl .table tbody td:nth-child(5) strong[style*="orangered"] {
    background: #ffe9ec;
    color: #e5484d !important;
}

/* =========================================================
   CARD FOOTER
========================================================= */

#cardDisplayControl > .card-footer {
    min-height: 14px;
    padding: 8px 18px;
    border: 0;
    border-top: 1px solid #e7ebef;
    background: #f8fafb;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {
    #cardDisplayControl > .card-header {
        align-items: flex-start;
        flex-direction: column;
        padding: 14px 16px;
    }

    #cardDisplayControl > .card-header > span {
        text-align: left;
    }

    #cardDisplayControl .table-responsive > .table {
        min-width: 820px;
    }

    #cardDisplayControl .table thead th,
    #cardDisplayControl .table tbody td {
        padding-right: 14px;
        padding-left: 14px;
    }

    #cardDisplayControl .table thead th:first-child,
    #cardDisplayControl .table tbody td:first-child {
        padding-left: 18px;
    }
}
/* Header */
#cardDisplayControl .marks-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 16px 22px;
    background: linear-gradient(90deg,#eef7f5,#dcecea);
    border-bottom: 1px solid #e5e7eb;
}

#cardDisplayControl .marks-header-title .card-title{
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #176e65;
}

#cardDisplayControl .marks-header-note{
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 420px;
    padding: 8px 14px;
    background: #fff3cd;
    border: 1px solid #ffe69c;
    border-radius: 8px;
    color: #856404;
    font-size: 13px;
    font-weight: 600;
    text-align: right;
}

#cardDisplayControl .marks-header-note i{
    color: #f59e0b;
    font-size: 16px;
}

@media (max-width:768px){
    #cardDisplayControl .marks-header{
        flex-direction: column;
        align-items: flex-start;
    }

    #cardDisplayControl .marks-header-note{
        width:100%;
        max-width:100%;
        text-align:left;
    }
}

/* ===== Assessment Details Section ===== */

.form-group.row{
    background: #fff;
    border: 1px solid #fff;
    border-radius: 12px;
    padding: 20px 15px;
    margin: 0 10px 20px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.form-group.row label{
    font-weight: 700;
    color: #334155;
    font-size: 14px;
    margin-top: 8px;
}

.form-group.row .form-control{
    height: 42px;
    border-radius: 8px;
    border: 1px solid #d7dde5;
    box-shadow: none;
    font-size: 15px;
    transition: .25s;
}

.form-group.row .form-control:focus{
    border-color: #176e65;
    box-shadow: 0 0 0 3px rgba(23,110,101,.12);
}

.form-group.row .text-right{
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

/* spacing between rows */
.form-group.row > div{
    margin-bottom: 12px;
}

@media (max-width:768px){

    .form-group.row{
        padding:15px;
    }

    .form-group.row .text-right{
        justify-content:flex-start;
        margin-bottom:5px;
    }
}


.form-group.row > .col-md-4{
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.form-group.row > .col-md-4 label{
    display: block;
    width: 100%;
    margin-bottom: 8px;
    text-align: left !important;
}

.form-group.row > .col-md-4 .form-control{
    width: 100%;
}

.form-group.row > .col-md-4.text-right{
    text-align: left !important;
}





















/* =========================================================
   COURSE ASSESSMENT MARKS — FIGMA STYLE OVERRIDE
   CSS ONLY

   Structure / Logic / Functionality preserved.
   No existing element is hidden or force-shown.
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


#MarksEntry {
    --figma-primary: #17675F;
    --figma-primary-dark: #125B54;
    --figma-primary-soft: rgba(23, 103, 95, 0.10);
    --figma-primary-softer: rgba(23, 103, 95, 0.06);

    --figma-text: #252525;
    --figma-muted: #949494;
    --figma-line: #E4ECEA;

    --figma-bg: #F7FAF9;
    --figma-white: #FFFFFF;

    --figma-warning: #FFBA49;
    --figma-danger: #E5484D;
    --figma-success: #1F9D77;

    width: 100%;

    color: var(--figma-text);

    font-family: 'Montserrat', Arial, sans-serif;
}


/* =========================================================
   PAGE HEADER
   ========================================================= */

#MarksEntry > div:first-child {
    display: flex !important;

    align-items: flex-start;
    justify-content: space-between;

    gap: 24px;

    width: 100%;

    padding: 8px 0 16px !important;
}


#MarksEntry > div:first-child > div {
    float: none !important;
}


/* Assessment Marks title */
#MarksEntry .profile-overview-title {
    margin: 0;

    color: var(--figma-text);

    font-size: 20px;
    font-weight: 600;
    line-height: 1.35;

    letter-spacing: 0;
}


/* Course name */
#MarksEntry > div:first-child h3 {
    margin: 1px 0 0;

    color: var(--figma-primary);

    font-size: 16px;
    font-weight: 500;
    line-height: 1.4;

    text-align: right;
}


/* =========================================================
   MAIN FORM BODY
   ========================================================= */

#MarksEntry > .card-body {
    padding: 0 !important;

    background: transparent;
}


/* =========================================================
   ASSESSMENT DETAILS
   Title / Date / Total Marks
   ========================================================= */

#MarksEntry .form-group.row {
    display: grid !important;

    grid-template-columns: repeat(3, minmax(0, 1fr));

    column-gap: 20px;
    row-gap: 12px;

    width: 100%;

    margin: 0 0 18px !important;
    padding: 18px 20px 16px;

    border: 0;
    border-radius: 10px;

    background: var(--figma-white);

    box-shadow:
        0 0 10px rgba(0, 0, 0, 0.05);
}


#MarksEntry .form-group.row > div {
    width: 100% !important;
    max-width: none !important;

    margin: 0 !important;
    padding: 0 !important;

    text-align: left !important;
}


#MarksEntry .form-group.row > .col-md-4 {
    display: flex;

    flex-direction: column;

    align-items: stretch;
}


/* Existing message column remains in structure */
#MarksEntry .form-group.row > .col-md-3 {
    grid-column: 1 / -1;

    min-height: 0;
}


/* Labels */
#MarksEntry .form-group.row label {
    width: 100%;

    margin: 0 0 8px;

    color: var(--figma-text);

    font-size: 12px;
    font-weight: 500;
    line-height: 1.35;

    text-align: left !important;
}


#MarksEntry .form-group.row label b {
    font-weight: 500;
}


/* Inputs */
#MarksEntry .form-group.row .form-control {
    width: 100%;

    height: 42px;

    padding: 9px 12px;

    border: 1px solid #D7E2DF;
    border-radius: 8px;

    background: #FFFFFF;

    color: var(--figma-text);

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 14px;
    font-weight: 400;
    line-height: 1.4;

    box-shadow: none;

    transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background-color .18s ease;
}


#MarksEntry .form-group.row .form-control:hover {
    border-color: #B8CBC7;
}


#MarksEntry .form-group.row .form-control:focus {
    border-color: var(--figma-primary);

    outline: 0;

    background: #FFFFFF;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.10);
}


#MarksEntry .form-group.row .form-control.is-invalid {
    border-color: var(--figma-danger);

    box-shadow:
        0 0 0 3px rgba(229, 72, 77, 0.08);
}


/* Existing saved / validation messages */
#MarksEntry #saved_msg_div,
#MarksEntry #res_total_marks {
    font-size: 11px;
    line-height: 1.35;
}


/* =========================================================
   UPDATE TOTAL MARKS BUTTON
   Existing JS visibility remains untouched
   ========================================================= */

#MarksEntry #editBtn {
    min-height: 42px;

    margin: 0 0 14px;

    border: 1px solid var(--figma-primary) !important;
    border-radius: 50px;

    background: var(--figma-primary) !important;

    color: #FFFFFF !important;

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 14px;
    font-weight: 500;

    box-shadow: none !important;
}


#MarksEntry #editBtn:hover,
#MarksEntry #editBtn:focus {
    border-color: var(--figma-primary-dark) !important;

    background: var(--figma-primary-dark) !important;
}


#MarksEntry #maxMarksdiv,
#MarksEntry #maxMarksdivSucess {
    font-size: 12px;
    line-height: 1.45;
}


/* =========================================================
   MARKS ENTRY CARD
   ========================================================= */

#MarksEntry #cardDisplayControl {
    overflow: hidden;

    margin: 0;

    border: 0;
    border-radius: 10px;

    background: var(--figma-white);

    box-shadow:
        0 0 10px rgba(0, 0, 0, 0.05);
}


/* =========================================================
   MARKS ENTRY HEADER
   ========================================================= */

#MarksEntry #cardDisplayControl > .card-header,
#MarksEntry #cardDisplayControl .marks-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 20px;

    min-height: 64px;

    margin: 0;
    padding: 14px 20px;

    border: 0;
    border-bottom: 1px solid var(--figma-line);

    background: #FFFFFF;
}


#MarksEntry #cardDisplayControl .marks-header-title .card-title,
#MarksEntry #cardDisplayControl > .card-header .card-title {
    margin: 0;

    color: var(--figma-text);

    font-size: 20px;
    font-weight: 600;
    line-height: 1.35;
}


/* Existing warning */
#MarksEntry #cardDisplayControl .marks-header-note {
    display: flex;

    align-items: center;

    gap: 8px;

    max-width: 430px;

    margin: 0;
    padding: 8px 12px;

    border: 1px solid rgba(56, 128, 222, 0.22);
    border-radius: 8px;

    background: rgba(56, 128, 222, 0.05);

    color: var(--figma-text);

    font-size: 11px;
    font-weight: 400;
    line-height: 1.45;

    text-align: left;
}


#MarksEntry #cardDisplayControl .marks-header-note i {
    flex: 0 0 auto;

    color: #3880DE;

    font-size: 14px;
}


#MarksEntry #cardDisplayControl .marks-header-note strong {
    color: var(--figma-primary);

    font-weight: 600;
}


/* =========================================================
   CARD BODY
   ========================================================= */

#MarksEntry #cardDisplayControl > .card-body {
    padding: 0;

    background: #FFFFFF;
}


#MarksEntry
#cardDisplayControl
> .card-body
> .table-responsive {

    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    background: #FFFFFF;

    -webkit-overflow-scrolling: touch;
}


/* =========================================================
   TABLE BASE
   ========================================================= */

#MarksEntry
#cardDisplayControl
.table-responsive
> .table {

    width: 100%;

    min-width: 900px;

    margin: 0;

    border: 0 !important;

    border-collapse: separate;
    border-spacing: 0;

    background: #FFFFFF;
}


/* =========================================================
   TABLE HEADER
   ========================================================= */

#MarksEntry #cardDisplayControl .table thead tr {
    background: var(--figma-primary);
}


#MarksEntry #cardDisplayControl .table thead th {
    height: 44px;

    padding: 12px 18px;

    border: 0 !important;

    background: var(--figma-primary);

    color: #FFFFFF;

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 10px;
    font-weight: 500 !important;
    line-height: 1.25;

    letter-spacing: .35px;

    text-align: center;
    text-transform: uppercase;

    vertical-align: middle;

    white-space: nowrap;
}


#MarksEntry
#cardDisplayControl
.table
thead
th:first-child {

    padding-left: 24px;

    text-align: left;
}


/* =========================================================
   TABLE ROWS
   ========================================================= */

#MarksEntry #cardDisplayControl .table tbody tr {
    height: 68px;

    background: #FFFFFF;

    transition:
        background-color .16s ease;
}


#MarksEntry
#cardDisplayControl
.table
tbody
tr:nth-child(even) {

    background: rgba(23, 103, 95, 0.035);
}


#MarksEntry
#cardDisplayControl
.table
tbody
tr:hover {

    background: rgba(23, 103, 95, 0.07);
}


/* =========================================================
   TABLE CELLS
   ========================================================= */

#MarksEntry #cardDisplayControl .table tbody td {
    height: 68px;

    padding: 11px 18px;

    border: 0;
    border-bottom: 1px solid var(--figma-line);

    color: var(--figma-text);

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 12px;
    font-weight: 400;
    line-height: 1.35;

    vertical-align: middle;

    white-space: nowrap;
}


#MarksEntry
#cardDisplayControl
.table
tbody
tr:last-child
td {

    border-bottom: 0;
}


/* =========================================================
   STUDENT COLUMN
   ========================================================= */

#MarksEntry
#cardDisplayControl
.table
tbody
td:first-child {

    display: table-cell !important;

    min-width: 285px;

    padding-left: 24px;

    text-align: left;

    vertical-align: middle;
}


/* Student image */
#MarksEntry #cardDisplayControl .table .avatar-lg {
    display: inline-block;

    width: 38px;
    min-width: 38px;

    height: 38px;

    margin-right: 10px !important;

    border: 2px solid rgba(23, 103, 95, 0.10);
    border-radius: 50%;

    background: #E6ECEA;

    object-fit: cover;

    vertical-align: middle;

    box-shadow: none;
}


/* Student info */
#MarksEntry
#cardDisplayControl
.table
tbody
td:first-child
.ml-3 {

    display: inline-block;

    margin: 0 !important;

    vertical-align: middle;
}


/* Student name */
#MarksEntry
#cardDisplayControl
.table
tbody
td:first-child
h5 {

    margin: 0 0 3px !important;

    color: var(--figma-text) !important;

    font-size: 12px;
    font-weight: 500;
    line-height: 1.25;
}


/* Enrollment */
#MarksEntry
#cardDisplayControl
.table
tbody
td:first-child
p {

    margin: 0 !important;

    color: var(--figma-muted) !important;

    font-size: 10px !important;
    font-weight: 400;
    line-height: 1.3;
}


/* =========================================================
   PERCENTAGE
   ========================================================= */

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(2) {

    min-width: 130px;

    text-align: center;
}


#MarksEntry
#cardDisplayControl
.table
.chart-circle {

    width: 34px !important;
    height: 34px !important;

    margin-right: auto !important;
    margin-left: auto !important;
}


#MarksEntry
#cardDisplayControl
.table
.chart-circle-value {

    color: var(--figma-text);

    font-size: 9px !important;
    font-weight: 600;
}


/* =========================================================
   MARKS INPUT
   ========================================================= */

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3) {

    min-width: 160px;

    text-align: center;
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
.marks {

    width: 82px !important;

    height: 34px;

    margin: 0 auto;

    padding: 6px 10px;

    border: 1px solid #D7E2DF;
    border-radius: 7px;

    background: #FFFFFF;

    color: var(--figma-text);

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 12px;
    font-weight: 500;

    text-align: center;

    box-shadow: none;

    transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background-color .18s ease;
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
.marks:hover {

    border-color: #B8CBC7;
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
.marks:focus {

    border-color: var(--figma-primary);

    outline: 0;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.10);
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
.marks.is-invalid {

    border-color: var(--figma-danger);

    box-shadow:
        0 0 0 3px rgba(229, 72, 77, 0.08);
}


/* Existing marks validation text */
#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
small {

    min-height: 12px;

    margin-top: 4px;

    color: var(--figma-danger) !important;

    font-size: 9px;
    line-height: 1.25;
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(3)
> div:last-child {

    margin-top: 0 !important;
}


/* =========================================================
   STATUS + HOD APPROVAL
   ========================================================= */

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(4),

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(5) {

    min-width: 135px;

    text-align: center !important;

    font-size: 10px;
    font-weight: 500;
}


/* Existing check */
#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(4)
.fa-check {

    color: var(--figma-success);

    font-size: 16px;
}


/* Pending / Saved / Approved */
#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(4)
strong,

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(4)
span,

#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(5)
strong {

    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 64px;

    padding: 5px 10px;

    border-radius: 50px;

    background: rgba(31, 157, 119, 0.10);

    color: var(--figma-success) !important;

    font-size: 10px;
    font-weight: 500;
    line-height: 1.1;
}


#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(5)
strong {

    min-width: 82px;
}


/* Not Approved */
#MarksEntry
#cardDisplayControl
.table
tbody
td:nth-child(5)
strong[style*="orangered"] {

    background: rgba(229, 72, 77, 0.10);

    color: var(--figma-danger) !important;
}


/* =========================================================
   CARD FOOTER
   ========================================================= */

#MarksEntry
#cardDisplayControl
> .card-footer {

    min-height: 12px;

    padding: 7px 18px;

    border: 0;
    border-top: 1px solid var(--figma-line);

    background: rgba(23, 103, 95, 0.035);
}


/* =========================================================
   EXISTING SUCCESS / ERROR / WAIT ALERTS
   ========================================================= */

#MarksEntry .alert {
    margin-top: 8px !important;

    padding: 10px 14px;

    border-radius: 8px;

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 12px;
    line-height: 1.4;
}


/* =========================================================
   SAVE MARKS BUTTON
   ========================================================= */

#MarksEntry #marks_submit_btn {
    min-width: 132px;
    min-height: 42px;

    margin-top: 14px !important;

    padding: 10px 18px;

    border: 1px solid var(--figma-primary) !important;
    border-radius: 50px;

    background: var(--figma-primary) !important;

    color: #FFFFFF !important;

    font-family: 'Montserrat', Arial, sans-serif;

    font-size: 14px;
    font-weight: 500;
    line-height: 1.2;

    box-shadow: none !important;

    transition:
        background-color .18s ease,
        border-color .18s ease,
        transform .18s ease;
}


#MarksEntry #marks_submit_btn:hover,
#MarksEntry #marks_submit_btn:focus {
    border-color: var(--figma-primary-dark) !important;

    background: var(--figma-primary-dark) !important;
}


#MarksEntry #marks_submit_btn:active {
    transform: translateY(1px);
}


/* =========================================================
   RESPONSIVE — TABLET
   ========================================================= */

@media (max-width: 991.98px) {

    #MarksEntry > div:first-child {
        gap: 16px;
    }


    #MarksEntry .form-group.row {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    #MarksEntry
    .form-group.row
    > .col-md-4:nth-of-type(3) {

        grid-column: 1 / -1;
    }


    #MarksEntry
    #cardDisplayControl
    .marks-header-note {

        max-width: 360px;
    }

}


/* =========================================================
   RESPONSIVE — MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    #MarksEntry > div:first-child {
        flex-direction: column;

        align-items: stretch;

        padding-bottom: 14px !important;
    }


    #MarksEntry > div:first-child h3 {
        text-align: left;
    }


    #MarksEntry .profile-overview-title {
        font-size: 18px;
    }


    #MarksEntry .form-group.row {
        grid-template-columns: 1fr;

        padding: 15px;
    }


    #MarksEntry
    .form-group.row
    > .col-md-4:nth-of-type(3),

    #MarksEntry
    .form-group.row
    > .col-md-3 {

        grid-column: auto;
    }


    #MarksEntry
    #cardDisplayControl
    > .card-header,

    #MarksEntry
    #cardDisplayControl
    .marks-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 10px;

        padding: 14px 16px;
    }


    #MarksEntry
    #cardDisplayControl
    .marks-header-title
    .card-title,

    #MarksEntry
    #cardDisplayControl
    > .card-header
    .card-title {

        font-size: 18px;
    }


    #MarksEntry
    #cardDisplayControl
    .marks-header-note {

        width: 100%;

        max-width: none;
    }


    #MarksEntry
    #cardDisplayControl
    .table-responsive
    > .table {

        min-width: 820px;
    }


    #MarksEntry
    #cardDisplayControl
    .table
    thead
    th,

    #MarksEntry
    #cardDisplayControl
    .table
    tbody
    td {

        padding-right: 14px;
        padding-left: 14px;
    }


    #MarksEntry
    #cardDisplayControl
    .table
    thead
    th:first-child,

    #MarksEntry
    #cardDisplayControl
    .table
    tbody
    td:first-child {

        padding-left: 18px;
    }


    #MarksEntry #marks_submit_btn {
        min-width: 120px;
    }

}


/* =========================================================
   SMALL MOBILE
   ========================================================= */

@media (max-width: 480px) {

    #MarksEntry .form-group.row {
        padding: 14px;
    }


    #MarksEntry #cardDisplayControl {
        border-radius: 8px;
    }

}
    </style>

      <div class="" id="MarksEntry" style="">
        <div class="" style="padding:8px !important;display:inline">
            <div style="float:left">                
                <h4 class="profile-overview-title">Assessment Marks for <?=$studentsList[0]['assessment_name'].' # '.$studentsList[0]['ccd_number']?> </h4>
            </div>
            <div style="float:right">
                <h3><?=$studentsList[0]['course_name']?></h3>
               
                <!-- <button class="btn btn-primary" type="button" onclick="upd('<?=$co_id?>','<?=$ass_type?>','<?=$num?>')">Update Marks</button>
                <span style="font-size: 10px; color: red; margin: 5px 0;">
                    Press this to update marks for transferred students.
                </span> -->
            </div>
        </div>
        <div class="card-body" style="padding-left: 0px !important; padding-right: 0px !important;">
            {{-- action="{{url('teacher/marks-save')}}" --}}
        <form   method="post" enctype="multipart/form-data" id="marksEntryForm" name="marksEntryForm" role="form">
                        <div class="form-group row" style="margin-right:10px;margin:0 !important;">
                            <div class="col-md-4 col-sm-6 col-4 text-right">
                                <label for="cam_details" class="form-check-label"><b>Title :</b></label>
                                <input type="hidden" id="form_change_flag" name="form_change_flag" value="1">
                                <input type="hidden" id="co_id" name="co_id" value="<?=$co_id;?>" >
                                <input type="hidden" id="assessment_id" name="assessment_id" value="<?=$ass_type?>">
                                <input type="hidden" id="cam_number" name="cam_number" value="<?=$num?>">
                                <input type="hidden" id="course_criteria_id" name="course_criteria_id" value="<?=$ccd_id?>">
                                <input type="hidden" id="ccd_id" name="ccd_id">

                                <input type="text" id="cam_details" maxlength="50" onkeypress="showEditBtn()"  name="cam_details" value="{{$studentsList[0]['cam_details']}}" class="form-control">
                            </div>
                            {{-- <div class="col-md-10 col-sm-6 col-8">
                                <input type="text" id="cam_details" maxlength="50" onkeypress="showEditBtn()"  name="cam_details" value="{{$studentsList[0]['cam_details']}}" class="form-control">
                            </div> --}}
                            <div class="col-md-4 col-sm-6 col-4 text-right" style="margin-top: 5px;">
                                <label for="cam_date" class="form-check-label"><b>Date :</b></label>
                                 <input type="date" onchange="showEditBtn()"  id="cam_date"  value="{{$studentsList[0]['cam_date']}}" name="cam_date" min="2018-01-01" max="2022-12-31" class="form-control">
                            </div>
                            {{-- <div class="col-md-4 col-sm-6 col-8"  style="margin-top: 5px;">
                                <input type="date" onchange="showEditBtn()"  id="cam_date"  value="{{$studentsList[0]['cam_date']}}" name="cam_date" min="2018-01-01" max="2022-12-31" class="form-control">
                            </div> --}}
                            <div class="col-md-4 col-sm-6 col-4 text-right"  style="margin-top: 5px;">
                                <label for="total_marks"  class="form-check-label"><b>Total Marks :</b></label>
                                       <input type="text" onkeypress="return event.charCode >= 46 && event.charCode <= 57"  onkeyup="showEditBtn()" id="total_marks" value="{{$studentsList[0]['total_marks']}}" name="total_marks" class="form-control" min="1" step="1" max="500" maxlength="3"   required>
                            </div>
                            {{-- onkeydown="if(event.key==='.'){event.preventDefault();}" oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');" onkeyup="checkmarks(<?=$studentsList[0]['ccd_id'];?>)" --}}
                            <div class="col-md-3 col-sm-6 col-8" style="margin-top: 5px;">
                                {{-- <input type="text" onkeypress="return event.charCode >= 46 && event.charCode <= 57"  onkeyup="showEditBtn()" id="total_marks" value="{{$studentsList[0]['total_marks']}}" name="total_marks" class="form-control" min="1" step="1" max="500" maxlength="3"   required> --}}
                                <span id="saved_msg_div" style="color: green;display: none">saved</span>
                                <input type="hidden" id="old_total_marks" name="old_total_marks" value="0" >
                                <small id="res_total_marks" class="text-danger" style="display:none;"></small>
                            </div>
                        </div>
                        <div>
                            <button type="button" id="editBtn" style="width:100%;display:none" class="btn btn-primary" onclick="totalMarksUpd()">Update</button>
                        </div>
                        <div align="center" style="padding-top: 20px">
                            <span style="color:red;font-weight:bold;text-align: center;" id="maxMarksdiv"></span>
                            <span style="color:green;font-weight:bold;text-align: center;" id="maxMarksdivSucess"></span>
                        </div>
                   @csrf
                   <div class="card" id="cardDisplayControl">
                 <div class="card-header marks-header">
    <div class="marks-header-title">
        <h3 class="card-title mb-0">Marks Entry</h3>
    </div>

    <div class="marks-header-note">
        <i class="fa fa-exclamation-circle"></i>
        After entering all marks, please click the
        <strong>Save Marks</strong> button.
    </div>
</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter text-nowrap mb-0 border">
                                <thead>
                                    <tr>
                                        <th style="font-weight:bold !important" class="border-bottom-0">Student</th>
                                        <th style="font-weight:bold !important" class="text-center">Percentage</th>
                                        <th style="font-weight:bold !important" class="text-center">Marks</th>
                                        <th style="font-weight:bold !important" class="text-center">Status</th>
                                        <th style="font-weight:bold !important" class="text-center">Hod Approval</th>
                                    </tr>
                                </thead>
                             <tbody>
                                    <?php $sr = 1;?>
                                    @foreach($studentsList as $rec_list)
                                    @php
                                    	$marks_percentage = 0;
                                        $file_path = '/students_pics/'.$rec_list['sai_enrollment_no'].'.jpg';
                                        if(!file_exists(public_path($file_path))){
                                            $file_path = '/students_pics/noimage.png';
                                        }
										if($studentsList[0]['total_marks'] != 0){
                                        $marks_percentage = round(($rec_list['obtained_marks']/$studentsList[0]['total_marks'])*100,0);
                                        }
                                        if($marks_percentage>80){
                                                $graph_type_name    = "success";
                                                $graph_color        = "2dce89";

                                        }elseif($marks_percentage>=75){
                                                $graph_type_name    = "primary";
                                                $graph_color        = "4454c3";
                                        }else{
                                                $graph_type_name    = "secondary";
                                                $graph_color        = "f72d66";
                                        }
                                    @endphp
                                    <tr>
                                        <td class="d-flex">
                                            <img class="avatar-lg rounded-circle mr-3" src="{{$file_path}}" alt="Image description">
                                            <div class="ml-3 mt-2">
                                                <h5 class="mb-0 text-dark">{{$rec_list['spi_name']}}</h5>
                                                <p class="mb-0  fs-13 text-muted">{{$rec_list['sai_enrollment_no']}}</p>
                                            </div>
                                        </td>


                                        <td id="graph_id{{$rec_list['id']}}">
                                            <div  class="mx-auto chartcircle-<?=$rec_list['id'];?> chart-circle chart-circle-<?=$rec_list['id'];?> chart-circle-xs chart-circle-<?=$graph_type_name;?> mt-sm-0 mb-0 icon-dropshadow-<?=$graph_type_name;?>" data-value="{{$marks_percentage/100}}" data-thickness="4" data-color="#<?=$graph_color;?>">
                                                <div class="mx-auto chart-circle-value text-center">{{$marks_percentage}}%</div>
                                            </div>
                                        </td>
                                        {{-- <td id="graph_id{{$rec_list['id']}}">&nbsp;
                                          
                                        </td> --}}
                                        <input type="hidden" id="updated_at<?=$rec_list['id'];?>" value="{{$rec_list['updated_at']}}">
                                        <td align="center">
                                            <div style="
    text-align: -webkit-center;
">
                                             <input type="hidden" name="sai_enrollment_no[]" class="marks_id" value="<?=$rec_list['sai_enrollment_no'];?>">
                                             <input type="hidden" name="marks_id[]" class="marks_id" value="<?=$rec_list['id'];?>">   
                                             <input onkeypress="return event.charCode >= 46 && event.charCode <= 57"  onkeyup="update_question_marks(<?=$rec_list['id'] ;?>,'<?=$rec_list['sai_enrollment_no'] ;?>','<?=$rec_list['co_id'];?>')" type="text" value="{{$rec_list['obtained_marks']}}" id="marks<?=$rec_list['id'];?>" class="form-control marks onfocus<?=$sr?>" style="width:30% !important;" max="{{$rec_list['total_marks']}}" min="0" name="user_enter_marks[]" maxlength="{{(strlen($rec_list['total_marks'])+3)}}"> <br>
                                            </div>
                                            <div style="margin-top: -20px;">
                                             <small id="small_msg_<?=$rec_list['id'];?>" style="color: red;">&nbsp;</small>
                                             <div>
                                        </td>
                                        <td align="center">
                                            <div id="{{$rec_list['id']}}saved_msg_div_file1" style="display:none;color: green;"><i class="fa fa-check fa-2x" aria-hidden="true"></i></div>
                                                <?php if($rec_list['obtained_marks']!=''){ ?>

                                                <div id="{{$rec_list['id']}}saved_msg_div_file11" style="color: green;"><i class="fa fa-check fa-2x" aria-hidden="true"></i></div>
                                                <?php } else { ?>
                                                <div id="{{$rec_list['id']}}saved_msg_div" style="color: #177E89;"><strong>Pending</strong>
                                                    </div>
                                                    <div id="{{$rec_list['id']}}saved_msg_div_file" style="display:none;color: green;"><span> Saved</span></div>
                                                <?php } ?>
                                        </td>
                                        <td align="center" >
                                            @if ($rec_list['assessment_id'] == 3 || $rec_list['assessment_id'] ==4)
                                                @if ($rec_list['approval_status'] == 1)
                                                    <strong style="color: #177E89;">Approved</strong>
                                                @else
                                                    <strong style="color: orangered;">Not Approved</strong>
                                                @endif
                                            @else
                                                <strong style="color: orangered;">Approved</strong>
                                            @endif
                                        </td>
                                    </tr>
                                    <?php $sr++;?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div style="float:right">
                           <!-- <button type="button" onclick="window.open('/teacher/course_evaluation/<?=$sc_co_id?>','_self');" class="btn btn-primary">Done.</button>-->

                        </div>
                      </div>

                        <div class="alert alert-success" id="success-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-danger" id="error-msg" style="display:none;margin-top:5px;"></div>
                        <div class="alert alert-success" id="wait-msg" style="display:none;margin-top:5px;">Please wait Processing.....'</div>
                       <!-- <input type="submit" value="Save Marks" id="marks_submit_btn" class="btn btn-primary btn-lg float-md-right" style="margin-top: 5px;"> -->
              </form>
                            <input type="button" value="Save Marks" id="marks_submit_btn" onclick="update_question_marks_all()" class="btn btn-primary btn-lg float-md-right" style="margin-top: 5px;background-color: #1F776E;border-color: #1F776E;">

        </div>



        </div>
<?php }else{?>

        <h1>No Active Student in this course</h1>
    <?php } ?>

</div>
        @endsection
@section('javascript')
<script>
$( document ).ready(function() {
    $(".onfocus1").focus();
});
function showEditBtn(){

    $('#editBtn').show();
    $('#cardDisplayControl').hide();
}
$('#for_key_up_down').on('keydown', 'input, select', function(e) {
  if (e.which === 13) {
    var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
    focusable = form.find('input').filter(':visible');
    next = focusable.eq(focusable.index(this)+1);
    if (next.length) {
        next.focus();
    }
    return false;
  }
  if (e.which == 39)
            $(this).closest('td').next().find('input').focus();
        else if (e.which == 37)
            $(this).closest('td').prev().find('input').focus();
        else if (e.which == 40)
            $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        else if (e.which == 38)
            $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();




});

function upd(co_id,asses_type,cnum){
    $.ajax({
	        type:'get',
	        url: '{{url('transfer-marks')}}/'+co_id+'/'+asses_type+'/'+cnum,
	        // data:fd,
	        processData:false,
	        contentType:false,
	        success:function(data){
	        // alert(data)
              if(data){
                 window.location.reload();
              }
	        //   if(data.status){

	        //     $("#saved_msg_div").show();
	        //   }

	        // setTimeout(function(){

	        // $("#saved_msg_div").hide();
	        //             }, 2000);
	        }

	   		});
}


function totalMarksUpd(){

    var bi_form = document.querySelector('#marksEntryForm');
    var fd      = new FormData(bi_form);
    sendPostRequest('#btn','#kihhh','teacher/update-totalmarks-desc',function(data){

        if(data.message == 'Total Marks Cannot be less than Obtained Marks'){     
           
           $('#maxMarksdiv').html('Maximum obtained marks by student is '+data.data+', So Kindly Update Valid Total Marks to process further');
           $('#cardDisplayControl').hide();
           $('#total_marks').focus()
      }else{
           $('#maxMarksdivSucess').html('Updated Sucessfully');
           setTimeout(function () {
                $('#maxMarksdivSucess').html('')
            }, 3000);
           $('#cardDisplayControl').show();
           $('#editBtn').hide();
           $('#maxMarksdiv').html('');
           
      }

    },fd);
}

 function checkmarks(ccd_id){

 	var total_marks=parseInt($('#total_marks').val());
 	var flag=0;
      $('.marks').each(function(index, ele) {

           var total_marks=parseInt($('#total_marks').val());
           var obt_marks=parseInt(ele.value);

 		    console.log(ele);
 			if(total_marks < obt_marks ){
            	 $('#total_marks').focus();
            	 $('#total_marks').addClass('is-invalid');
            	 return false;

            }else if(total_marks >= obt_marks ){

            	 $('#total_marks').focus();
            	$('#total_marks').removeClass('is-invalid');
            	flag=1;

            }


        });

      	if(flag==1){

	        var fd = new FormData();
	        $.ajax({
	        type:'get',
	        url: '{{url('teacher/update_totalmarks')}}/'+ccd_id+'/'+total_marks,
	        data:fd,
	        processData:false,
	        contentType:false,
	        success:function(data){
	          //alert(data)
	          if(data.status){

	            $("#saved_msg_div").show();
	          }

	        setTimeout(function(){

	        $("#saved_msg_div").hide();
	                    }, 2000);
	        }

	   		});
	}


 }


 function number_validation(full_no,max_num){
   if(parseInt(full_no)==parseInt(max_num)){
         full_no =   parseInt(full_no);//full_no.toString().slice(0,-1);
   }else{

            try {
                var before_decimal_number   = full_no.toString().split(".")[0];
                var after_decimal_number    = full_no.toString().split(".")[1];
                var after_decimal_length    = full_no.toString().split(".")[1].length;

                if(full_no>max_num){
                    var before_decimal_number =before_decimal_number.toString().slice(0,-1)
                }else{
                    before_decimal_number   = full_no.toString().split(".")[0];
                }


                if(after_decimal_length>2){
                    full_no = before_decimal_number.concat(".").concat(after_decimal_number.slice(0,2-after_decimal_length));
                }else{
                    full_no = before_decimal_number.concat(".").concat(after_decimal_number);
                }
            }
            catch(err) {

                if(full_no>max_num){
                    full_no =   full_no.toString().slice(0,-1);
                }

            }


        }

        return full_no;
}

function update_question_marks_all(id,enrollment_no){
    $('#wait-msg').show();
    $.ajax({
        url: "{{url('api/update_assessmentmarks_all')}}",
        method: 'post',
        data: $('#marksEntryForm').serialize(),
        success: function(response){
            $('#wait-msg').hide();
            $('#success-msg').show();
            $('#success-msg').html('Marks Update Successfully');
            setTimeout(function(){
                $('#success-msg').hide();
                }, 2000);
       }});
}



function update_question_marks(id,enrollment_no,co_id){

    //$("#demo02").attr('maxlength','2');
    var obt_marks=0;
    var total_marks =   parseFloat($('#total_marks').val());
    var obt_marks   =   $('#marks'+id).val();
    var percentage = 0 ;

   // console.log(number_validation(obt_marks,total_marks));
    $('#marks'+id).val(number_validation(obt_marks,total_marks));

    if( obt_marks > total_marks || obt_marks < 0){

            $('#marks'+id).focus();
            $('#marks'+id).addClass('is-invalid');
            $("#"+id+"saved_msg_div").show();
            $("#"+id+"saved_msg_div_file").hide();
            $("#small_msg_"+id).show();
            $("#small_msg_"+id).html('More then'+ total_marks +'Not allowed');

            return false;

    }else if(isNaN(obt_marks) ){
            $('#marks'+id).focus();
            $('#marks'+id).addClass('is-invalid');

            $("#small_msg_"+id).show();
            $("#small_msg_"+id).html('Marks are not valid');

            return false;

    }else{

        $('#marks'+id).focus();
        $('#marks'+id).removeClass('is-invalid');
        $("#small_msg_"+id).hide();
        $("#small_msg_"+id).html('');
        var percentage = parseInt((obt_marks/total_marks)*100) ;
    }


    $("#"+id+"saved_msg_div_file").hide();
    var update_marks = $("#marks"+id).val();
    var updated_at = $("#updated_at"+id).val();
    if(updated_at == ''){
        updated_at = 'undefined'
    }
    var fd = new FormData();

    console.log("___"+update_marks);
if(!update_marks){
    update_marks = "NAN";
}


sendPostRequest('#btn','#kihhh','teacher/update_assessmentmarks/'+id+'/'+enrollment_no+'/'+update_marks+'/'+updated_at+'/'+co_id,function(data){
		if(data.message=='NOTALLOWED'){
                alert('You have process Grading thats why Marks Entry not allowed');
                window.location.href = "/home";
                return false;
            }
            /////---------------------
            if(data.status ){
         $("#"+id+"saved_msg_div").hide();
          $("#"+id+"saved_msg_div_file").show();



        if(percentage>80){
            var  graph_type_name    = "success";
           var  graph_color        = "2dce89";

        }else if(percentage>=60){
            var     graph_type_name    = "primary";
           var     graph_color        = "4454c3";
        }else{
            var     graph_type_name    = "secondary";
           var     graph_color        = "f72d66";
        }

        var graph_div = '';

        graph_div +='<div class="mx-auto chart-circle chart-circle-xs chart-circle-'+graph_type_name+' mt-sm-0 mb-0 icon-dropshadow-primary" data-value="'+percentage/100+'" data-thickness="5" data-color="#'+graph_color+'">';
        graph_div +='<div class="mx-auto chart-circle-value text-center">'+percentage+'%</div>';
        graph_div +='</div>';

        var getdivid = $("#graph_id"+id).html(graph_div);

        if(graph_type_name == 'success'){
            var value = 'rgba(45, 206, 137, 0.5)';
        }else if(graph_type_name == 'primary'){
            var value = 'rgba(68, 84, 195, 0.4)';
        }else{
            var value = 'rgba(247, 45, 102, 0.4)';
        }

        $('.chart-circle-'+graph_type_name).each(function() {
            let $this = $(this);

            $this.circleProgress({
            fill: {
                color: $this.attr('data-color')
            },
            size: $this.height(),
            startAngle: -Math.PI / 4 * 2,
            emptyFill: value,
            lineCap: 'round'
            });
        });


        $('.chart-circle'+id).each(function() {
        let $this = $(this);

        $this.circleProgress({
            fill: {
            color: $this.attr('data-color')
            },
            size: $this.height(),
            startAngle: -Math.PI / 4 * 2,
            emptyFill: '#e5e9f2',
            lineCap: 'round'
        });
        });



        setTimeout(function(){
        //$("#"+id+"saved_msg_div_file").hide();
        $("#"+id+"#saved_msg_div_file1").show();
        //location.reload();
                }, 2000);
            ////----------------------
            }
    },fd);

}





</script>
@stop




