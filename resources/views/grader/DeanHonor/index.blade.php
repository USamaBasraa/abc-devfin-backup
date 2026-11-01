@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')


{{-- @dd("123a"); --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* Original Button Design with Blinking Border */
.btn-blink {
    background-color: transparent; /* Original light blue background */
    border: 3px solid #4454c3; /* Blue border */
    cursor: pointer; /* Pointer on hover */
    animation: border-blink-animation 1s infinite; /* Blinking border animation */
    transition: all 0.3s ease; /* Smooth transition for hover effect */
}
 /* Custom checkbox style */
 .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #4454c3;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
        background-color: #ffffff;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
 /* Custom checkbox style */
 .approve-select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #4454c3;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
        background-color: #ffffff;
    }

    /* Checked state */
    .approve-select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .approve-select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
.dt-button {
    background-color: #4454c3;
    color: #4454c3;
    border: none;
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
        margin-top:5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }

/* Blinking Border Animation */
@keyframes border-blink-animation {
    0%, 100% {
        border-color: #4454c3; /* Original blue border */
    }
    50% {
        border-color: transparent; /* Border becomes transparent */
    }
}

.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}

















/* =========================================================
   DEAN HONOR FILTER SECTION - CSS ONLY REDESIGN
   ========================================================= */

/* Main card */
.row.mt-4 > .col-lg-12 > .card {
    background: #ffffff;
    border: none;
    border-radius: 3px;
    box-shadow: none;
}

/* Filter area */
.row.mt-4 > .col-lg-12 > .card > .card-body {
    padding: 18px 20px 14px;
}

/* Convert form into 2-column layout */
#messageForm {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 16px;
    row-gap: 10px;
}

/* Remove Bootstrap row behavior */
#messageForm .form-group.row {
    display: block;
    margin: 0 !important;
}

/* Label column becomes full width */
#messageForm .form-group.row > .col-lg-4 {
    width: 100%;
    max-width: 100%;
    flex: none;
    padding: 0;
    text-align: left !important;
}

/* Input column becomes full width */
#messageForm .form-group.row > .col-lg-8 {
    width: 100%;
    max-width: 100%;
    flex: none;
    padding: 0;
}

/* Labels like screenshot */
#messageForm .col-form-label {
    display: block;
    padding: 0;
    margin-bottom: 5px;
    font-size: 12px;
    line-height: 1.3;
    font-weight: 500;
    color: #222;
}

/* Original input width is 400px inline,
   force responsive width */
#messageForm input.easyui-combobox {
    width: 100% !important;
}


/* =========================================================
   EASYUI COMBOBOX
   ========================================================= */

#messageForm .combo {
    width: 100% !important;
    height: 34px !important;
    display: flex !important;
    align-items: center;
    border: 1px solid #d9dee3 !important;
    border-radius: 4px !important;
    background: #fff !important;
    box-shadow: none !important;
    box-sizing: border-box;
}

/* Text field */
#messageForm .combo .combo-text {
    width: calc(100% - 34px) !important;
    height: 32px !important;
    line-height: 32px !important;
    padding: 0 10px !important;
    font-size: 12px !important;
    color: #555 !important;
    border: none !important;
    background: transparent !important;
    box-sizing: border-box;
}

/* Arrow area */
#messageForm .combo .combo-arrow {
    width: 32px !important;
    height: 32px !important;
    opacity: .65;
    background-color: transparent !important;
}

/* Hover */
#messageForm .combo:hover {
    border-color: #bfc7ce !important;
}

/* Focus */
#messageForm .combo:focus-within {
    border-color: #7eaaa5 !important;
}


/* =========================================================
   SELECT OPTION
   ========================================================= */

/* Select option lives outside #messageForm */
.card-body > .form-group.row:nth-of-type(2) {
    margin-top: 10px !important;
}

/* Make Select Option match field structure */
.card-body > .form-group.row:not(#messageForm .form-group) {
    margin-left: 0;
    margin-right: 0;
}

/* Specific select-option row */
#messageForm + .form-group.row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 16px;
    margin: 10px 0 0 !important;
}

/* Label */
#messageForm + .form-group.row > .col-lg-4 {
    grid-column: 1 / 2;
    width: 100%;
    max-width: 100%;
    flex: none;
    padding: 0;
    text-align: left !important;
}

/* Options */
#messageForm + .form-group.row > .col-lg-8 {
    grid-column: 2 / 3;
    width: 100%;
    max-width: 100%;
    flex: none;
    padding: 0;
    margin-top: 0 !important;
}

#messageForm + .form-group.row .col-form-label {
    font-size: 12px;
    padding: 0;
    margin-bottom: 5px;
    color: #222;
}

/* Radio option row */
#messageForm + .form-group.row .col-lg-8 > .row {
    margin: 0;
    min-height: 34px;
    display: flex;
    align-items: center;
    gap: 22px;
}

/* Remove Bootstrap col-md-2 limitation */
#messageForm + .form-group.row .form-check {
    width: auto;
    max-width: none;
    flex: none;
    padding-left: 20px;
    text-align: left !important;
}

/* Radio */
#messageForm + .form-group.row .form-check-input {
    margin-top: 2px;
}

/* Radio text */
#messageForm + .form-group.row .form-check-label {
    font-size: 12px;
    color: #333;
}


/* =========================================================
   ACTION BUTTONS
   ========================================================= */

.card-body > .form-group.row[style*="justify-content: center"] {
    justify-content: flex-start !important;
    margin: 12px 0 0 !important;
    gap: 10px;
}

/* Shared button style */
#saveButton,
#approveButton,
#downloadExcel {
    min-height: 34px;
    padding: 7px 14px;
    margin: 0 !important;
    border: none;
    border-radius: 4px;
    box-shadow: none;
    font-size: 12px;
    font-weight: 500;
}

/* Submit */
#saveButton,
#approveButton {
    background: #247d78 !important;
    border-color: #247d78 !important;
    color: #fff !important;
}

/* PDF */
#downloadExcel {
    background: #247d78 !important;
    border-color: #247d78 !important;
    color: #fff !important;
}

/* Hover */
#saveButton:hover,
#approveButton:hover,
#downloadExcel:hover {
    opacity: .9;
}


/* =========================================================
   PAGE / CARD LOOK
   ========================================================= */

.row.mt-4 {
    /* background: #eef4f3; */
    /* padding: 14px 10px; */
    /* margin-top: 10px !important; */
}

.row.mt-4 > .col-lg-12 {
    padding: 0 !important;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 767px) {

    #messageForm {
        grid-template-columns: 1fr;
    }

    #messageForm + .form-group.row {
        grid-template-columns: 1fr;
    }

    #messageForm + .form-group.row > .col-lg-4,
    #messageForm + .form-group.row > .col-lg-8 {
        grid-column: 1;
    }

    #messageForm + .form-group.row .col-lg-8 > .row {
        margin-top: 6px;
    }

    .card-body > .form-group.row[style*="justify-content: center"] {
        flex-wrap: wrap;
    }
}



.validatebox-invalid {
    /* border-color: #ffa8a8; */
    background-color: #fff !important;
    backgorund:#fff !important;
    /* color: #000; */
}


/* =========================================
   PROGRAM + SELECT OPTION CONSISTENT ALIGNMENT
   ========================================= */

/* Program and Select Option are two grid items */
#messageForm .form-group.row:nth-last-child(2),
#messageForm .form-group.row:last-child {
    display: block;
    margin: 0 !important;
}

/* Make both labels identical */
#messageForm .form-group.row:nth-last-child(2) .col-form-label,
#messageForm .form-group.row:last-child .col-form-label {
    display: block;
    padding: 0 !important;
    margin: 0 0 6px 0 !important;

    font-size: 12px;
    line-height: 16px;
    font-weight: 500;
    color: #222;
}


/* =========================================
   SELECT OPTION COLUMN
   ========================================= */

#messageForm .form-group.row:last-child > .col-lg-4 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;

    padding: 0 !important;
    text-align: left !important;
}

#messageForm .form-group.row:last-child > .col-lg-8 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;

    padding: 0 !important;
    margin: 0 !important;
}


/* Remove Bootstrap row negative margins */
#messageForm .form-group.row:last-child .col-lg-8 > .row {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;

    flex-wrap: nowrap !important;

    min-height: 34px;

    margin: 0 !important;
    padding: 0 !important;

    gap: 32px;
}


/* =========================================
   INDIVIDUAL RADIO ITEM
   ========================================= */

#messageForm .form-group.row:last-child .form-check {
    display: flex !important;
    align-items: center !important;

    width: auto !important;
    max-width: none !important;

    flex: 0 0 auto !important;

    padding: 0 !important;
    margin: 0 !important;

    text-align: left !important;
}


/* Radio circle */
#messageForm .form-group.row:last-child .form-check-input {
    position: static !important;

    width: 14px;
    height: 14px;

    flex: 0 0 14px;

    margin: 0 8px 0 0 !important;
    padding: 0;

    cursor: pointer;
}


/* Radio text */
#messageForm .form-group.row:last-child .form-check-label {
    display: inline-block !important;

    margin: 0 !important;
    padding: 0 !important;

    font-size: 12px;
    line-height: 16px;

    font-weight: 400;
    color: #333;

    white-space: nowrap !important;
    cursor: pointer;
}


/* =========================================
   PROGRAM FIELD
   ========================================= */

#messageForm .form-group.row:nth-last-child(2) > .col-lg-4,
#messageForm .form-group.row:nth-last-child(2) > .col-lg-8 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;

    padding: 0 !important;
}


/* Keep program combobox same height as radio area */
#messageForm .form-group.row:nth-last-child(2) .combo {
    width: 100% !important;
    height: 34px !important;
}


/* =========================================
   MOBILE
   ========================================= */

@media (max-width: 767px) {

    #messageForm .form-group.row:last-child .col-lg-8 > .row {
        flex-wrap: wrap !important;
        gap: 12px 24px;
    }
}
.card-footer {
    border-top:none;
}

/* =========================================
   FILTER RESULT - SEPARATE CARD STYLE
   ========================================= */

/* Result wrapper */
#dTable {
    margin-top: 18px;
    /* padding: 0 10px 14px; */
}

/* White card container */
#dTable .table-responsive.container {
    width: 100% !important;
    max-width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #ffffff;

    border-radius: 6px;
    overflow: hidden;

    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}


/* =========================================
   TABLE TITLE
   ========================================= */

/* Hide / simplify existing title if you want
   screenshot-style compact table only */
#dTable .card-title {
    margin: 0;
    padding: 12px 14px;

    font-size: 14px;
    font-weight: 600;

    color: #1d3e3a;
    background: #ffffff;
}

/* Remove generated BR spacing */
#dTable .card-title + br {
    display: none;
}


/* =========================================
   STUDENT TABLE
   ========================================= */

#studentTable {
    width: 100% !important;

    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    background: #fff !important;

    font-size: 11px;
}


/* =========================================
   HEADER
   ========================================= */

#studentTable thead,
#studentTable thead.bg-primary {
    background: #176d64 !important;
}

#studentTable thead th {
    background: #176d64 !important;

    color: #ffffff !important;

    border: none !important;

    padding: 9px 12px !important;

    font-size: 10px !important;
    font-weight: 500 !important;

    white-space: nowrap;

    vertical-align: middle;
    text-align: left;
}


/* First header rounded corner */
#studentTable thead th:first-child {
    border-top-left-radius: 5px;
}

/* Last header rounded corner */
#studentTable thead th:last-child {
    border-top-right-radius: 5px;
}


/* =========================================
   BODY
   ========================================= */

#studentTable tbody td {
    border: none !important;

    padding: 9px 12px !important;

    font-size: 10px;
    font-weight: 400;

    color: #555;

    vertical-align: middle;

    white-space: nowrap;
}


/* Alternate row colors like screenshot */
#studentTable tbody tr:nth-child(odd) td {
    background: #ffffff !important;
}

#studentTable tbody tr:nth-child(even) td {
    background: #f3f3f3 !important;
}


/* Row hover */
#studentTable tbody tr:hover td {
    background: #eaf4f2 !important;
}


/* Bottom rounded corners */
#studentTable tbody tr:last-child td:first-child {
    border-bottom-left-radius: 5px;
}

#studentTable tbody tr:last-child td:last-child {
    border-bottom-right-radius: 5px;
}


/* =========================================
   CHECKBOX COLUMN
   ========================================= */

#studentTable th:first-child,
#studentTable td:first-child {
    width: 38px;
    text-align: center;
}

#studentTable .select-data {
    width: 15px;
    height: 15px;

    margin: 0;

    border-width: 1.5px;

    vertical-align: middle;
}


/* =========================================
   DATATABLE WRAPPER
   ========================================= */

#dTable .dataTables_wrapper {
    padding: 0 !important;
    padding-left: 14px !important;
    padding-right:14px !important;
    margin: 0 !important;
}


/* Top controls */
#dTable .dataTables_wrapper .dt-buttons,
#dTable .dataTables_wrapper .dataTables_length,
#dTable .dataTables_wrapper .dataTables_filter {
    margin-top: 10px;
    margin-bottom: 10px;
}


/* Export buttons */
#dTable .dt-button {
    background: #176d64 !important;
    color: #ffffff !important;

    border: none !important;
    border-radius: 4px !important;

    padding: 6px 10px !important;

    font-size: 11px !important;

    box-shadow: none !important;
}

#dTable .dt-button:hover {
    background: #125b53 !important;
    color: #ffffff !important;
}


/* Search field */
#dTable .dataTables_filter input {
    height: 30px;

    border: 1px solid #d8dedc;
    border-radius: 4px;

    padding: 4px 8px;

    font-size: 11px;

    outline: none;
}


/* Length dropdown */
#dTable .dataTables_length select {
    height: 30px;

    border: 1px solid #d8dedc;
    border-radius: 4px;

    padding: 3px 7px;

    font-size: 11px;
}


/* =========================================
   PAGINATION
   ========================================= */

#dTable .dataTables_paginate {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}

#dTable .dataTables_paginate .paginate_button {
    border: none !important;
    border-radius: 4px !important;

    padding: 5px 9px !important;

    font-size: 11px !important;
}

#dTable .dataTables_paginate .paginate_button.current {
    background: #176d64 !important;
    color: #ffffff !important;

    border: none !important;
}


/* =========================================
   RESULT AREA BACKGROUND
   Makes it visually separate from filter card
   ========================================= */

#dTable.newlist {
    background: #f2f6f9;

    border-radius: 6px;

    padding-top: 34px;
}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 767px) {

    #dTable {
        padding-left: 0;
        padding-right: 0;
    }

    #studentTable thead th,
    #studentTable tbody td {
        padding: 8px 9px !important;
    }
}
#studentTable_wrapper {
    padding-left: 14px !important;
    padding-right: 14px !important;
}









/* =========================================
   DATATABLE TOP CONTROLS - SAME ROW
   Left: Buttons + Show entries
   Right: Search
   ========================================= */

#studentTable_wrapper {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    width: 100%;
}

/* Buttons */
#studentTable_wrapper .dt-buttons {
    order: 1;

    display: flex !important;
    align-items: center;

    margin: 10px 12px 10px 0 !important;
}

/* Show entries */
#studentTable_wrapper .dataTables_length {
    order: 2;

    display: flex !important;
    align-items: center;

    margin: 10px 0 !important;
}

/* Search on right side */
#studentTable_wrapper .dataTables_filter {
    order: 3;

    margin: 10px 0 10px auto !important;

    display: flex !important;
    align-items: center;
}

/* Force table onto next full-width row */
#studentTable_wrapper #studentTable {
    order: 4 !important;
    flex: none !important;

    width: max-content !important;
    min-width: 1500px !important;
}


/* =========================================
   DATATABLE BOTTOM CONTROLS - SAME ROW
   Left: Showing info
   Right: Pagination
   ========================================= */

#studentTable_wrapper .dataTables_info {
    order: 5;

    flex: 0 0 auto;

    margin: 10px 0 !important;
    padding: 0 !important;

    display: flex;
    align-items: center;
}

/* Pagination stays on right */
#studentTable_wrapper .dataTables_paginate {
    order: 6;

    margin: 10px 0 10px auto !important;
    padding: 0 !important;

    display: flex;
    align-items: center;
}


/* =========================================
   CONSISTENT TOP CONTROL HEIGHT
   ========================================= */

#studentTable_wrapper .dataTables_length label,
#studentTable_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;

    margin: 0 !important;

    font-size: 12px;
    line-height: 30px;
}

#studentTable_wrapper .dataTables_length select,
#studentTable_wrapper .dataTables_filter input {
    height: 30px !important;
}


/* Export buttons consistent */
#studentTable_wrapper .dt-button {
    min-height: 30px;

    display: inline-flex !important;
    align-items: center;
    justify-content: center;

    margin: 0 !important;

    border-radius: 0 !important;
}

/* First button rounded left */
#studentTable_wrapper .dt-button:first-child {
    border-radius: 4px 0 0 4px !important;
}

/* Last button rounded right */
#studentTable_wrapper .dt-button:last-child {
    border-radius: 0 4px 4px 0 !important;
}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 767px) {

    #studentTable_wrapper .dt-buttons,
    #studentTable_wrapper .dataTables_length,
    #studentTable_wrapper .dataTables_filter,
    #studentTable_wrapper .dataTables_info,
    #studentTable_wrapper .dataTables_paginate {
        flex: 0 0 100%;
        width: 100%;

        margin: 6px 0 !important;s
    }

    #studentTable_wrapper .dataTables_filter {
        justify-content: flex-start;
    }

    #studentTable_wrapper .dataTables_paginate {
        justify-content: flex-start;
    }
}














/* Page typography */
.profile-overview-title,
.row.mt-4 > .col-lg-12 > .card,
#messageForm,
#dTable,
#studentTable_wrapper,
.oldDataDiv,
.newDataDiv {
    font-family: 'Montserrat', sans-serif !important;
}

.profile-overview-title {
    margin: 0 0 16px !important;
    font-size: 20px !important;
    line-height: 1.25 !important;
    font-weight: 600 !important;
    color: #111827 !important;
}

/* Main filter card */
.row.mt-4 > .col-lg-12 > .card {
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 16px !important;
    box-shadow: none !important;
}

.row.mt-4 > .col-lg-12 > .card > .card-body {
    padding: 24px !important;
}

#messageForm {
    column-gap: 24px !important;
    row-gap: 16px !important;
}

/* Labels */
#messageForm .col-form-label {
    margin-bottom: 8px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    line-height: 1.3 !important;
    font-weight: 500 !important;
    color: #111827 !important;
}

/* EasyUI fields */
#messageForm .combo {
    height: 40px !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

#messageForm .combo .combo-text {
    height: 38px !important;
    line-height: 38px !important;
    padding: 0 12px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #111827 !important;
}

#messageForm .combo .combo-arrow {
    width: 38px !important;
    height: 38px !important;
}

#messageForm .combo:hover {
    border-color: #C4CECC !important;
}

#messageForm .combo:focus-within {
    border-color: #17675F !important;
    box-shadow: 0 0 0 2px rgba(23, 103, 95, 0.08) !important;
}

/* Select option */
#messageForm .form-group.row:last-child .form-check-label {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    line-height: 18px !important;
    font-weight: 400 !important;
    color: #111827 !important;
}

#messageForm .form-group.row:last-child .form-check-input {
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
    accent-color: #17675F;
}

/* Action buttons */
#saveButton,
#approveButton,
#downloadExcel {
    min-height: 40px !important;
    padding: 0 18px !important;
    margin: 0 !important;
    border: 1px solid #17675F !important;
    border-radius: 8px !important;
    background: #17675F !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    color: #FFFFFF !important;
}

#saveButton:hover,
#approveButton:hover,
#downloadExcel:hover {
    background: #125D56 !important;
    border-color: #125D56 !important;
    color: #FFFFFF !important;
    opacity: 1 !important;
}

/* Keep Font Awesome icon font intact */
#approveButton .fa {
    font-family: FontAwesome !important;
}

/* Checkboxes */
.select-data,
.approve-select-data {
    width: 16px !important;
    height: 16px !important;
    border: 1.5px solid #BFC8C7 !important;
    border-radius: 4px !important;
    background: #FFFFFF !important;
}

.select-data:checked,
.approve-select-data:checked {
    background: #17675F !important;
    border-color: #17675F !important;
}

.select-data:checked::after,
.approve-select-data:checked::after {
    font-size: 12px !important;
    line-height: 14px !important;
}

/* Dynamic result area */
#dTable.newlist {
    margin-top: 24px !important;
    padding: 24px !important;
    background: #F7FAF9 !important;
    border-radius: 16px !important;
}

#dTable .table-responsive.container {
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 16px !important;
    box-shadow: none !important;
}

#dTable .card-title {
    padding: 16px 20px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 16px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
    color: #111827 !important;
}

/* JS-generated summary cards */
#dTable .card.bg-info,
#dTable .card.bg-success {
    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 12px !important;
    color: #111827 !important;
    box-shadow: none !important;
}

#dTable .card.bg-info .card-title,
#dTable .card.bg-success .card-title {
    padding: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #6B7280 !important;
}

#dTable .card.bg-info .card-text,
#dTable .card.bg-success .card-text {
    margin: 6px 0 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-weight: 700 !important;
    color: #17675F !important;
}

/* Main generated student table */
#studentTable {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
}

#studentTable thead,
#studentTable thead.bg-primary,
#studentTable thead th {
    background: #17675F !important;
}

#studentTable thead th {
    height: 48px !important;
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    color: #FFFFFF !important;
    text-transform: uppercase !important;
    vertical-align: middle !important;
}

#studentTable tbody td {
    height: 52px !important;
    padding: 12px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
    color: #111827 !important;
    vertical-align: middle !important;
}

#studentTable tbody tr:nth-child(odd) td {
    background: #FFFFFF !important;
}

#studentTable tbody tr:nth-child(even) td {
    background: #FAFBFF !important;
}

#studentTable tbody tr:hover td {
    background: #F4F8F7 !important;
}

/* DataTables controls */
#studentTable_wrapper .dt-button {
    min-height: 36px !important;
    padding: 0 14px !important;
    border: 1px solid #17675F !important;
    background: #17675F !important;
    color: #FFFFFF !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 600 !important;
}

#studentTable_wrapper .dataTables_length label,
#studentTable_wrapper .dataTables_filter label,
#studentTable_wrapper .dataTables_info,
#studentTable_wrapper .dataTables_paginate {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    color: #6B7280 !important;
}

#studentTable_wrapper .dataTables_length select,
#studentTable_wrapper .dataTables_filter input {
    height: 36px !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
    color: #111827 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    box-shadow: none !important;
}

#studentTable_wrapper .dataTables_filter input:focus,
#studentTable_wrapper .dataTables_length select:focus {
    border-color: #17675F !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(23, 103, 95, 0.08) !important;
}

#studentTable_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-family: 'Montserrat', sans-serif !important;
}

#studentTable_wrapper .dataTables_paginate .paginate_button.current,
#studentTable_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #17675F !important;
    border-color: #17675F !important;
    color: #FFFFFF !important;
}

/* Old/New DataTables */
#data-table-OldData,
#data-table-NewData {
    font-family: 'Montserrat', sans-serif !important;
}

#data-table-OldData thead th,
#data-table-NewData thead th {
    background: #17675F !important;
    border-color: #17675F !important;
    color: #FFFFFF !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
}

#data-table-OldData tbody td,
#data-table-NewData tbody td {
    border-color: #E5E7EB !important;
    color: #111827 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
}

/* Feedback messages */
#processing_message,
#error_message,
#success_message {
    margin-top: 16px !important;
    border-radius: 8px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
}

/* Responsive */
@media (max-width: 767px) {
    .row.mt-4 > .col-lg-12 > .card > .card-body {
        padding: 18px !important;
    }

    #dTable.newlist {
        padding: 14px !important;
    }
}
/* =========================================================
   ONLY TABLE AREA HORIZONTAL SCROLL
   Controls + pagination stay fixed
   ========================================================= */

/* Outer result/card must NOT scroll */
#dTable {
    overflow: visible !important;
}

#studentTable_wrapper {
    overflow: visible !important;
    max-width: 100% !important;
}


/* Table container only */
#dTable .table-responsive.container {
    width: 100% !important;
    max-width: 100% !important;

    overflow-x: auto !important;
    overflow-y: visible !important;

    -webkit-overflow-scrolling: touch;

    box-sizing: border-box !important;
}


/* Table needs enough width to create horizontal scroll */
#studentTable {
    width: max-content !important;
    min-width: 1500px !important;

    margin: 0 !important;
}


/* Keep header visible / aligned */
#studentTable thead {
    position: sticky;
    top: 0;
    z-index: 5;
}


/* Prevent cells from wrapping */
#studentTable thead th,
#studentTable tbody td {
    white-space: nowrap !important;
}


/* Horizontal scrollbar */
#dTable .table-responsive.container::-webkit-scrollbar {
    height: 8px;
}

#dTable .table-responsive.container::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 20px;
}

#dTable .table-responsive.container::-webkit-scrollbar-thumb {
    background: #B8C6C3;
    border-radius: 20px;
}
















/* =========================================================
   OLD DATA TABLE = ADD NEW LIST TABLE STYLE
   CSS ONLY - functionality preserved
   ========================================================= */

/* Main old data card */
.oldDataDiv {
    margin: 24px 0 0 !important;
    padding: 24px !important;

    background: #F7FAF9 !important;
    border-radius: 16px !important;
}

.oldDataDiv > .col-lg-12 {
    padding: 0 !important;
}

.oldDataDiv .card {
    margin: 0 !important;

    background: #FFFFFF !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 16px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}

.oldDataDiv .card-body {
    padding: 0 !important;
}


/* =========================================================
   DATATABLE WRAPPER
   ========================================================= */

#data-table-OldData_wrapper {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;

    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;

    padding: 0 16px 16px !important;

    box-sizing: border-box !important;

    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   TOP LEFT - SEARCH
   ========================================================= */

#data-table-OldData_wrapper .dataTables_filter {
    order: 1 !important;

    display: flex !important;
    align-items: center !important;

    flex: 0 0 auto !important;

    margin: 14px 16px 14px 0 !important;
}

#data-table-OldData_wrapper .dataTables_filter label {
    display: flex !important;
    align-items: center !important;

    gap: 8px;

    margin: 0 !important;

    font-size: 12px !important;
    color: #6B7280 !important;
}

#data-table-OldData_wrapper .dataTables_filter input {
    width: 200px !important;
    height: 36px !important;

    margin-left: 0 !important;
    padding: 0 10px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
}

#data-table-OldData_wrapper .dataTables_filter input:focus {
    border-color: #17675F !important;
    outline: 0 !important;

    box-shadow: 0 0 0 2px rgba(23, 103, 95, .08) !important;
}


/* =========================================================
   SHOW ENTRIES
   ========================================================= */

#data-table-OldData_wrapper .dataTables_length {
    order: 2 !important;

    display: flex !important;
    align-items: center !important;

    flex: 0 0 auto !important;

    margin: 14px 0 !important;
}

#data-table-OldData_wrapper .dataTables_length label {
    display: flex !important;
    align-items: center !important;

    gap: 6px;

    margin: 0 !important;

    font-size: 12px !important;
    color: #6B7280 !important;
}

#data-table-OldData_wrapper .dataTables_length select {
    height: 36px !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;

    background: #FFFFFF !important;

    font-size: 12px !important;
}


/* =========================================================
   TOP RIGHT - EXCEL / PDF / PRINT
   ========================================================= */

#data-table-OldData_wrapper .dt-buttons {
    order: 3 !important;

    display: flex !important;
    align-items: center !important;

    flex: 0 0 auto !important;

    margin: 14px 0 14px auto !important;
}

#data-table-OldData_wrapper .dt-button {
    min-height: 36px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;

    box-shadow: none !important;

    color: #FFFFFF !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 600 !important;
}

#data-table-OldData_wrapper .dt-button:hover {
    background: #125D56 !important;
    border-color: #125D56 !important;
    color: #FFFFFF !important;
}

#data-table-OldData_wrapper .dt-button:first-child {
    border-radius: 8px 0 0 8px !important;
}

#data-table-OldData_wrapper .dt-button:last-child {
    border-radius: 0 8px 8px 0 !important;
}


/* =========================================================
   TABLE AREA
   ========================================================= */

#data-table-OldData_wrapper .dataTables_scroll {
    order: 4 !important;

    flex: 0 0 100% !important;

    width: 100% !important;
    max-width: 100% !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 10px !important;

    overflow: hidden !important;
}


/* Header itself remains fixed in its table area */
#data-table-OldData_wrapper .dataTables_scrollHead {
    overflow: hidden !important;

    background: #17675F !important;
}


/* Horizontal scrolling only inside table region */
#data-table-OldData_wrapper .dataTables_scrollBody {
    overflow-x: auto !important;
    overflow-y: visible !important;

    -webkit-overflow-scrolling: touch;
}


/* =========================================================
   HEADER SAME AS ADD NEW LIST
   ========================================================= */

#data-table-OldData thead,
#data-table-OldData thead th {
    background: #17675F !important;
}

#data-table-OldData thead th {
    height: 48px !important;

    padding: 14px 16px !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    color: #FFFFFF !important;

    text-transform: uppercase !important;
    white-space: nowrap !important;

    vertical-align: middle !important;
}


/* DataTables sorting arrows */
#data-table-OldData thead th::before,
#data-table-OldData thead th::after {
    opacity: .35 !important;
}


/* =========================================================
   BODY SAME AS ADD NEW LIST
   ========================================================= */

#data-table-OldData tbody td {
    height: 52px !important;

    padding: 12px 16px !important;

    border: 0 !important;
    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #111827 !important;

    white-space: nowrap !important;

    vertical-align: middle !important;
}

#data-table-OldData tbody tr:nth-child(odd) td {
    background: #FFFFFF !important;
}

#data-table-OldData tbody tr:nth-child(even) td {
    background: #FAFBFF !important;
}

#data-table-OldData tbody tr:hover td {
    background: #F4F8F7 !important;
}


/* Checkbox alignment */
#data-table-OldData th:first-child,
#data-table-OldData td:first-child {
    width: 42px !important;

    text-align: center !important;
}


/* =========================================================
   HORIZONTAL SCROLLBAR
   ========================================================= */

#data-table-OldData_wrapper .dataTables_scrollBody::-webkit-scrollbar {
    height: 8px;
}

#data-table-OldData_wrapper .dataTables_scrollBody::-webkit-scrollbar-track {
    background: #F3F4F6;
}

#data-table-OldData_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb {
    background: #B8C6C3;

    border-radius: 20px;
}


/* =========================================================
   BOTTOM LEFT - COUNTER
   ========================================================= */

#data-table-OldData_wrapper .dataTables_info {
    order: 5 !important;

    flex: 0 0 auto !important;

    margin: 14px 0 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;

    color: #6B7280 !important;
}


/* =========================================================
   BOTTOM RIGHT - PAGINATION
   ========================================================= */

#data-table-OldData_wrapper .dataTables_paginate {
    order: 6 !important;

    flex: 0 0 auto !important;

    margin: 14px 0 0 auto !important;
    padding: 0 !important;
}

#data-table-OldData_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
}

#data-table-OldData_wrapper .dataTables_paginate .paginate_button.current,
#data-table-OldData_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767px) {

    .oldDataDiv {
        padding: 14px !important;
    }

    #data-table-OldData_wrapper .dataTables_filter {
        width: 100% !important;

        margin: 8px 0 !important;
    }

    #data-table-OldData_wrapper .dataTables_filter input {
        flex: 1 !important;

        width: auto !important;
        min-width: 0 !important;
    }

    #data-table-OldData_wrapper .dataTables_length {
        margin: 8px 0 !important;
    }

    #data-table-OldData_wrapper .dt-buttons {
        margin: 8px 0 8px auto !important;
    }
}



/* =========================================================
   OLD DATA - REMOVE DUPLICATE EMPTY HEADER ROW
   DataTables scrollX sizing header
   ========================================================= */

#data-table-OldData_wrapper .dataTables_scrollBody thead {
    visibility: hidden !important;
}

#data-table-OldData_wrapper .dataTables_scrollBody thead tr {
    height: 0 !important;
}

#data-table-OldData_wrapper .dataTables_scrollBody thead th {
    height: 0 !important;
    min-height: 0 !important;

    padding-top: 0 !important;
    padding-bottom: 0 !important;

    border-top: 0 !important;
    border-bottom: 0 !important;

    line-height: 0 !important;
}

/* If DataTables adds sizing wrappers */
#data-table-OldData_wrapper .dataTables_scrollBody thead th > div {
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
}
/* Old Data visible DataTable header - white text */
#data-table-OldData_wrapper .dataTables_scrollHead thead th,
#data-table-OldData_wrapper .dataTables_scrollHead thead th span,
#data-table-OldData_wrapper .dataTables_scrollHead thead th div {
    color: #FFFFFF !important;
}

/* Sorting arrows/icons */
#data-table-OldData_wrapper .dataTables_scrollHead thead th::before,
#data-table-OldData_wrapper .dataTables_scrollHead thead th::after {
    color: #FFFFFF !important;
    opacity: 0.65 !important;
}
</style>


<h4 class="profile-overview-title"> Student Dean's Honor List  </h4>
<div class="row mt-4" style="margin:5px;margin-left:0; margin-right:0;">
    <div class="col-lg-12 col-md-12d-flex justify-content-center align-items-center" style="padding-left:0 !important; padding-right:0 !important;">
        <div class="card">
        
            <input type="hidden" id="session">
            <input type="hidden" id="school">
            <input type="hidden" id="department">
            <input type="hidden" id="program">
            <input type="hidden" id="category">
            <div class="card-body">
                <form id="messageForm" method="post">
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Select Category</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="category_combobox" name="category" style="width:400px;" placeholder="Select Category" 
                                class="easyui-combobox form-control" data-options="
                                    valueField:'id',
                                    textField:'name',
                                    data: [
                                        { id: 'probation', name: 'Probation Letter' },
                                        { id: 'dean_honor', name: 'Dean Honor' }
                                    ],
                                    required:true,
                                    onSelect: function(rec){
                                      $('#category').val(rec.id);
                                    }
                                ">
                        </div>
                    </div>

                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label" >Enrollment Session</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="facultyWload_sess_id" name="sess_id" style="width:400px;" placeholder="Sessions" class="easyui-combobox form-control" data-options="
                                valueField:'sess_id',
                                textField:'sess_name',
                                required:'true',
                                url: '<?= url('/sessions') ?>',
                                method:'GET',
                                onSelect: function(rec){
                                    var urlschool = '<?= url('/activeschools') ?>?sess_id='+rec.sess_id;
                                    $('#facultyWload_school_id').combobox('clear');
                                    $('#facultyWload_school_id').val('');
                                    $('#department_id').combobox('clear');
                                    $('#department').val('');
                                    $('#program_id').combobox('clear');
                                    $('#program').val('');
                                    $('#facultyWload_school_id').combobox('reload', urlschool);
                                    $('#session').val(rec.sess_id);
                                    getData();
                                } ">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label" >School</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="facultyWload_school_id" name="school_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                                valueField: 'school_id',
                                textField: 'school_name',
                                required:'true',
                                method:'get',
                                onSelect: function(rec){
                                    var departmentURL = '<?= url('/activedepartments') ?>?school_id='+rec.school_id+'&sess_id='+$('#facultyWload_sess_id').combobox('getValue');
                                    $('#department_id').combobox('clear');
                                    $('#department').val('');
                                    $('#program_id').combobox('clear');
                                    $('#program').val('');
                                    $('#department_id').combobox('reload', departmentURL);
                                    $('#school').val(rec.school_id);
                                    getData();
                                }
                            ">
                        </div>
                    </div>

                    <!----------------------------------------   DEPARTMENT   ---------------------------------------->
                    <div class="form-group row " style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Department</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="department_id" name="department_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                            valueField: 'dept_id',
                            textField: 'dept_name',
                            required:'true',
                            method:'get',
                            url:'',
                            onSelect: function(rec){
                            var dept_ID = rec.dept_id
                            var progURL = '<?= url('/activeprograms') ?>?co_status=E&dept_id='+dept_ID+'&sel_sess_id='+$('#facultyWload_sess_id').combobox('getValue');
                            $('#program_id').combobox('clear');
                            $('#program').val('');
                            $('#program_id').combobox('reload', progURL);
                            $('#department').val(rec.dept_id);
                            getData();
                            }">
                        </div>
                    </div>
                    <!----------------------------------------   DEPARTMENT END   ---------------------------------------->

                    <!------------------ PROGRAM START ------------------>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Program</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="program_id" name="program_id" style="width:400px;" class="easyui-combobox form-control" data-options="
                            textField: 'prog_name',
                            valueField: 'prog_id',
                            required:'true',
                            method:'get',
                            onSelect: function(rec){
                                $('#program').val(rec.prog_id);
                                getData();
                            }">
                        </div>
                    </div>
                        <div class="form-group row" style="margin-bottom:0px;">
                    <div class="col-lg-4" style="text-align: right">
                        <label class="col-form-label">Select Option</label>
                    </div>
                    <div class="col-lg-8" style="margin-top:7px; ">
                        <div class='row'>
                            <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" checked type="radio" name="data_option" id="addNewList" value="add_new_list">
                                <label class="form-check-label" for="addNewList">Add New List</label>
                            </div>
                            {{-- <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" type="radio" name="data_option" id="newData" value="new_data">
                                <label class="form-check-label" for="newData">New Data</label>
                            </div> --}}
                            <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" type="radio" name="data_option" id="oldData" value="old_data">
                                <label class="form-check-label" for="oldData">Old Data</label>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            
                <div class="form-group row" style="display: flex;justify-content: center;">
                    <button type="button" id="saveButton" onclick="submitList()" class="btn btn-primary">  Submit List</button>
                    <button type="button" id="approveButton" onclick="approveList()" class="btn btn-primary d-none"> <i class="fa fa-paper-plane"></i> Approve</button>
                    <button id="downloadExcel" style="margin-left:30px;" class="btn btn-info" onclick="pdf()">
                        Download PDF
                    </button>
                </div>
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
            </div>
            <center>                    
            <div>
                <table border="1" id="programTable" style="display:none">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Top Students Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be inserted here dynamically -->
                    </tbody>
                </table>
            </div>
                            </center>

          
        {{-- <div class="card-header" style="display:inline">

            <div style="float: right">
                <!-- <span>
                    <button onclick="getData();" class="btn">Refersh Enrollment List</button>
                </span> -->
            </div>
        </div> --}}
        <div id='dTable' class='d-none newlist'></div>
         {{-- ///////Old Data//////////// --}}
         <div class="row d-none oldDataDiv" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table-OldData" class="table display table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="checkbox approve-select-data" id="approve-select-all" value=""/></th>
                                <th scope="col">Enrollment No</th>
                                <th id="gpa-label">GPA</th>
                                <th scope="col">Workload</th>
                                <th scope="col">Total Earn Cr Hr</th>
                                <th scope="col">Short of Req Cr Hr</th>
                                <th scope="col">Session</th>
                                <th scope="col">School</th>
                                <th scope="col">Department</th>
                                <th scope="col">Program</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- ///////New Data//////////// --}}
        <div class="row d-none newDataDiv" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table-NewData" class="table display table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Enrollment No</th>
                                    <th scope="col">GPA</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">School</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Program</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="card-footer">

        </div> --}}
    </div>
</div>
@stop
@section('javascript')

<script type="text/javascript">

function pdf(){
    
    sessionId = $('#facultyWload_sess_id').combobox('getValue');
    schoolId = $('#facultyWload_school_id').combobox('getValue'); 
    departmentId = $('#department_id').combobox('getValue');
    progId = $('#program_id').combobox('getValue'); 
   
    url = 'https://staff.bnu.edu.pk/HOD/exam_summary_report_grader.php?sessionId='+sessionId+'&esr_school_id='+schoolId+'&esr_prog_id='+progId+'&esr_dept_id='+departmentId;
    window.open(url, '_blank');
}

var baseUrl = window.location.protocol + "//" + window.location.host;

function formatGpa(value) {
    if (value == null || value === '' || value === 'null') {
        return '0.00';
    }
    var num = parseFloat(value);
    return isNaN(num) ? '0.00' : num.toFixed(2);
}


$(document).ready(function () {
    $('input[name="data_option"]').change(function () {
        getData();
    });
});
$(document).on('change', '#select-all', function() {
    if ($(this).prop("checked") == true) {
        $('.select-data').prop('checked', true);
        console.log($('.select-data').val());

    } else {
        $('.select-data').prop('checked', false);
        console.log($('.select-data').val());
    }
});
$(document).on('change', '#approve-select-all', function() {
    if ($(this).prop("checked") == true) {
        $('.approve-select-data').prop('checked', true);
        console.log($('.approve-select-data').val());

    } else {
        $('.approve-select-data').prop('checked', false);
        console.log($('.approve-select-data').val());
    }
});
function getData(){
    var selectedOption = $('input[name="data_option"]:checked').val();

    if (selectedOption === "add_new_list") {
        // console.log("Add New List selected");
        getEnrollmentStatus();
        $('#approveButton').addClass('d-none');
        $('#saveButton').removeClass('d-none');

        // $('#facultyWload_sess_id').combobox('enable');
        // $('#facultyWload_school_id').combobox('enable');
        // $('#department_id').combobox('enable');
        // $('#program_id').combobox('enable');

    } else if (selectedOption === "old_data") {
        // console.log("Old Data selected");
        LoadOldData();
        $('#saveButton').addClass('d-none');
        $('#approveButton').removeClass('d-none');

        // $('#facultyWload_sess_id').combobox('disable');
        // $('#facultyWload_school_id').combobox('disable');
        // $('#department_id').combobox('disable');
        // $('#program_id').combobox('disable');

    } else if (selectedOption === "new_data") {
        // console.log("New Data selected");
        LoadNewData();
        $('#saveButton').addClass('d-none');
        $('#approveButton').addClass('d-none');
    }
}
function submitList(){
    var deanHonorArr = getIdsOfSelectedData();
    // console.log(JSON.stringify(deanHonorArr));
    if(deanHonorArr != undefined){
        const sessionId = $('#facultyWload_sess_id').combobox('getValue');
        const schoolId = $('#facultyWload_school_id').combobox('getValue');
        const departmentId = $('#department_id').combobox('getValue');
        const programId = $('#program_id').combobox('getValue');
        const requestData = {
            session: sessionId,
            school: schoolId,
            dept: departmentId,
            program: programId,
            students: deanHonorArr
        };

        $("#processing_message").show();
        fetch('submit-deanhonor-list', {
            method: 'POST',
            body: JSON.stringify( requestData ), // Wrap in an object
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('if: '+data);
                $("#processing_message").hide();
                $("#error_message").text('').hide();
                $('#success_message').html(data.messege);
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(5000);
            } else {
                console.log(data);
                var message = "";
                if ('messege' in data) {
                    message = data.messege;
                } else if ('errors' in data) {
                    var errors = data.errors;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(5000);
            }
        })
        .catch(error => {
            console.log('error: '+error)
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    }
}
function approveList(){
    var deanHonorArr = getSelectedData_approved();
    // console.log(JSON.stringify(deanHonorArr));
    if(deanHonorArr != undefined){
        // const sessionId = $('#facultyWload_sess_id').combobox('getValue');
        // const schoolId = $('#facultyWload_school_id').combobox('getValue');
        // const departmentId = $('#department_id').combobox('getValue');
        // const programId = $('#program_id').combobox('getValue');
        const requestData = {
            // session: sessionId,
            // school: schoolId,
            // dept: departmentId,
            // program: programId,
            students: deanHonorArr
        };

        $("#processing_message").show();
        fetch('approve-deanhonor-list', {
            method: 'POST',
            body: JSON.stringify( requestData ), // Wrap in an object
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('if: '+data);
                $("#processing_message").hide();
                $("#error_message").text('').hide();
                $('#success_message').html(data.messege);
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(5000);
            } else {
                console.log(data);
                var message = "";
                if ('messege' in data) {
                    message = data.messege;
                } else if ('errors' in data) {
                    var errors = data.errors;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(5000);
            }
        })
        .catch(error => {
            console.log('error: '+error)
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    }
}
function getEnrollmentStatus() {
    // Show the loading spinner
    $('.oldDataDiv').addClass('d-none');
    $('.newDataDiv').addClass('d-none');
    $('#loading-spinner').show();

    // Get selected session, school, department, and program values
    var sess = $('#session').val();
    var school = $('#school').val();
    var dept = $('#department').val();
    var prog = $('#program').val();

    // Send the GET request to fetch applicant information
    $.ajax({
        url: 'dean-honor-student-information',  // Specify the correct URL here
        type: 'GET',
        data: {
            session: sess,
            school: school,
            department: dept,
            program: prog
        },
        success: function(res) {
            // Hide the loading spinner once the data is loaded
            $('#loading-spinner').hide();

            // Log the response to check what is being returned
            console.log('Response:', res);
            // Extract values from response
            if($('#category').val() != 'probation'){
                $('#programTable').show();
                // Populate Table
                $("#programTable tr:not(:first)").remove();
                var table = $("#programTable");
                $.each(res.programWiseCount, function(index, row) {
                    table.append(`
                        <tr>
                            <td>${row.program_name}</td>
                            <td style="text-align:center">${row.count}/${row.total_students}</td>
                        </tr>
                    `);
                });
            }

         function validNumber(value) {
    return value !== null &&
           value !== undefined &&
           value !== '' &&
           Number.isFinite(Number(value));
}

var totalStudents = validNumber(res.count)
    ? res.count
    : 'No Data Available';

var top7Percent = validNumber(res.countrow)
    ? res.countrow
    : 'No Data Available';

var formattedPercentage = validNumber(res.percentage)
    ? parseFloat(res.percentage).toFixed(1)
    : null;

var topStudentsTitle = formattedPercentage
    ? `Top ${formattedPercentage}% Students`
    : 'Top Students';

            // Create the info boxes
            var infoBoxes = `
                <div class="d-flex justify-content-center mb-3">
                    <div class="row w-100 d-flex justify-content-center">
                        <div class="col-md-3">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text fw-bold" style="font-size: 25px;">${totalStudents}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body text-center">
                                    <h5 class="card-title">${topStudentsTitle}</h5>
                                    <p class="card-text fw-bold" style="font-size: 25px;">${top7Percent}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            // Separate rows for upload_missing_document = 1 and upload_missing_document = 0
            let trueRows = [];
            let falseRows = [];
            let sr = 1;

            // Process rows and divide into true/false arrays
            $(res.top_7_percent_records).each(function(index, row) {
                var upload_missing_document = $.trim(row.upload_missing_document);
                var buttonClass = upload_missing_document === '1' ? 'btn-blink' : '';

                console.log(row);
                var rowHTML = `
                    <tr>
                        <td><input type="checkbox" ${row.tickmark == 1 ? 'checked':''} class="checkbox select-data" name="honorList[]" id="${row.aai_id}" value="${encodeURIComponent(JSON.stringify(row))}"/></td>
                        <td>${sr++}</td>
                        <td>${row.aai_id}</td>
                        <td>${row.cms_sai_enrollment_no}</td>
                        <td>${row.api_name}</td>
                        <td>${row.prog_name}</td>
                        <td>${row.sgpa}</td>
                        <td>${row.sai_prog_total_credits}</td>
                        <td>${row.workload_curr}</td>
                        <td>${row.total_earn_crhr}</td>
                        <td>${(row.sai_prog_total_credits)-(row.workload_curr)}</td>
                        <td>${row.sai_student_status}</td>
                    </tr>
                `;

                // Add to trueRows if upload_missing_document is '1', otherwise add to falseRows
                if (upload_missing_document === '1') {
                    trueRows.push(rowHTML); // Add to trueRows (1)
                } else {
                    falseRows.push(rowHTML); // Add to falseRows (0)
                }
            });

            // Combine trueRows (upload_missing_document = 1) first, then falseRows (upload_missing_document = 0)
            var tableHTML = `
                <div class="table-responsive container">
                    <h3 class="card-title">${topStudentsTitle}</h3>
                    <br>
                    <table class="table card-table table-primary mb-0" id="studentTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th><input type="checkbox" class="checkbox select-data" id="select-all" value=""/></th>
                                <th class="text-white">Sr #</th>
                                <th class="text-white">Applicant ID</th>
                                <th class="text-white">Enrollment No#</th>
                                <th class="text-white">Name</th>
                                <th class="text-white">Program</th>
                                <th class="text-white">SGPA</th>
                                <th class="text-white">Total Req Cr Hr</th>
                                <th class="text-white">Workload</th>
                                <th class="text-white">Total Earn Cr Hr</th>
                                <th class="text-white">Short of Req Cr Hr</th>
                                <th class="text-white">Student Status</th>
                            </tr>
                        </thead>
                        <tbody id="noti-msg-tbody">
            `;

            // Append trueRows first (those with upload_missing_document = 1)
            tableHTML += trueRows.join('');

            // Then append falseRows (those with upload_missing_document = 0)
            tableHTML += falseRows.join('');

            tableHTML += '</tbody></table></div>';


            // Append the generated table HTML to the #dTable div
            $('#dTable').html(infoBoxes + tableHTML);
            $('#dTable').removeClass('d-none');

            // Initialize DataTable without sorting enabled
            if ($.fn.DataTable.isDataTable('#studentTable')) {
                $('#studentTable').DataTable().clear().destroy(); // Clear and destroy previous DataTable
            }
            $('#studentTable').DataTable({
                "responsive": true,
                "ordering": false, // Disable sorting to maintain the original order
                "serverSide": false, //to download all data once
                "dom": 'Blfrtip', // Adds the buttons to the DataTable
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        title: 'Deans Honor List',
                        exportOptions: {
                            columns: ':visible' // Export all visible columns
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Deans Honor List',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Deans Honor List',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            // Log the count of rows with upload_missing_document = 1 and 0
            console.log('Rows with upload_missing_document = 1:', trueRows.length);
            console.log('Rows with upload_missing_document = 0:', falseRows.length);
        },
        error: function(xhr, status, error) {
            console.log(error);
            $('#loading-spinner').hide();
        }
    });
}
function LoadOldData(){ 
    const selected = $('#category').val();
    if (selected === 'probation') {
        $('#gpa-label').text('CGPA');
    } else {
        $('#gpa-label').text('GPA');
    }
    $('.newDataDiv').addClass('d-none');
    $('.newlist').addClass('d-none');
    $('.oldDataDiv').removeClass('d-none');
    // Get selected session, school, department, and program values
    var sess = $('#session').val();
    var school = $('#school').val();
    var dept = $('#department').val();
    var prog = $('#program').val();
    var category = $('#category').val();
        $('#data-table-OldData').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data once
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "dean-honor-oldData",
                // data: function(d) {
                //     return $.extend({}, d, filterParams);
                // }
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Send CSRF token in headers
                },
                data: {
                    session: sess,
                    school: school,
                    department: dept,
                    program: prog,
                    category:category
                },

            },
            columns: [
                {
                    render: function (data, type, row) {
                        var checked = row.status === 'E' ? 'checked' : ''; // Check if status is 'E'
                        var selectbtn = `<div>
                                            <input name="approved_honorList[]" class="approve-select-data" value="${row.sdl_id}" type="checkbox" ${checked}>
                                        </div>`;
                        return selectbtn;
                    }
                },
                { data: "sdl_enrollment_no", name: "sdl_enrollment_no" },
                // { data: "sdl_gpa", name: "sdl_gpa" },
                {
                    data: "sdl_gpa",
                    name: "sdl_gpa",
                    defaultContent: "0.00",
                    render: function (data, type, row) {
                        return formatGpa(data ?? row.sdl_gpa ?? row.SDL_GPA);
                    }
                },
                { data: "workload_curr", name: "workload_curr" },
                { data: "total_earn_crhr", name: "total_earn_crhr" },
                // { 
                //     data: null,  
                //     render: function(data, type, row) {
                //         let workload_curr = row.workload_curr ?? 0;
                //         let total_earn_crhr = row.total_earn_crhr ?? 0;
                //         return total_earn_crhr - workload_curr;
                //     } 
                // },
                { 
                    data: null,  
                    render: function(data, type, row) {
                        let total_req_crhr = row.total_req_crhr ?? 0;
                        let total_earn_crhr = row.total_earn_crhr ?? 0;
                        return total_req_crhr - total_earn_crhr;
                    } 
                },
                { data: "sess_name", name: "sess_name" },
                { data: "school_name", name: "school_name" },
                { 
                    data: "dept_name", 
                    name: "dept_name",
                    render: function(data) {
                        return data ?? ''; // Show empty string if null
                    }
                },
                { 
                    data: "prog_name", 
                    name: "prog_name",
                    render: function(data) {
                        return data ?? ''; // Show empty string if null
                    }
                }

            ],
            columnDefs: [
                // {
                //     render: function (data, type, row) {
                //         // console.log(row);
                //         var button = '<div><button class="btn btn-primary btn-sm edit-btn" id="'+row["sdl_id"]+'" onclick="editRow(`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`,`'+row["sdl_id"]+'`)"><i class="fa fa-check"></i>Approve</button></div>';
                //                                 //     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                //                                 // '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffs_id"]+'])"><i class="fa fa-trash"></i> Delete</button></div>';
                //         return button;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 6,
                // },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 14,
                //     searchable: false,
                //     orderable: false
                // },

                // {
                //     "width": "100px", "targets": 1
                // },
                // {
                //     "width": "5px", "targets": 14
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Dean Honor List (Old Students)',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Dean Honor List (Old Students)',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },
                {
                    extend: 'print',
                    title: 'Dean Honor List (Old Students)',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                }
            ]
        });
}
function LoadNewData(){
    $('.oldDataDiv').addClass('d-none');
    $('.newlist').addClass('d-none');
    $('.newDataDiv').removeClass('d-none');
        // let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table-NewData').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data once
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "dean-honor-newData",
                // data: function(d) {
                //     return $.extend({}, d, filterParams);
                // }
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Send CSRF token in headers
                }

            },
            columns: [
                {data: "sdl_enrollment_no", name: "sdl_enrollment_no"},
                // {data: "sdl_gpa", name: "sdl_gpa"},
                {
                    data: "sdl_gpa",
                    name: "sdl_gpa",
                    defaultContent: "0.00",
                    render: function (data, type, row) {
                        return formatGpa(data ?? row.sdl_gpa ?? row.SDL_GPA);
                    }
                },
                {data: "sess_name", name: "sess_name"},
                {data: "school_name", name: "school_name"},
                {data: "dept_name", name: "dept_name"},
                {data: "prog_name", name: "prog_name"},

            ],
            columnDefs: [
                // {
                //     render: function (data, type, row) {
                //         // console.log(row);
                //         var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["ffs_id"]+'" onclick="editRow(`'+row["ffs_id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`,`'+row["ffs_activity_amt"]+'`,`'+row["ffs_degree_level"]+'`,`'+row["ffs_roadmap_session"]+'`,`'+row["ffs_hostel_amt"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                //                                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffs_id"]+'])"><i class="fa fa-trash"></i> Delete</button></div>';
                //         return button;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 13,
                // },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 14,
                //     searchable: false,
                //     orderable: false
                // },

                // {
                //     "width": "100px", "targets": 1
                // },
                // {
                //     "width": "5px", "targets": 14
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Dean Honor List (New Students)',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Dean Honor List (New Students)',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },


                {
                    extend: 'print',
                    title: 'Dean Honor List (New Students)',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                }
            ]
        });
}
function verifyDocs(apiId, progId, sessId) {

    // Construct the new URL
    var newLink = baseUrl + "/doc-verifications?api_id=" + apiId + "&prog_id=" + progId + "&sess_id=" + sessId;

    // Open the link in a new tab
    window.open(newLink, "_blank");
}
function getIdsOfSelectedData(){
    var honorList = new Array();
    // $.each($("input[name='honorList[]']:checked"), function () {//get only checked rows
    $("input[name='honorList[]']").each(function () {//get all rows
        var data = JSON.parse(decodeURIComponent($(this).val())); // Parse value
        data.checked = $(this).prop("checked"); // Add checked property (true/false)
        honorList.push(data);

    });
    if (honorList.length == 0) {
        alert("Please select atleast one student.");
    } else {
        // console.log(honorList);
        return honorList;
    }
}
function getSelectedData_approved() {
    var approvedhonorList = [];

    $("input[name='approved_honorList[]']").each(function () {
        var data = {
            sdl_id: $(this).val(), // Store the value directly (no need for JSON.parse)
            checked: $(this).prop("checked") // Store checked status
        };
        approvedhonorList.push(data);
    });

    if (approvedhonorList.length === 0) {
        alert("Please select at least one student.");
    } else {
        console.log(approvedhonorList);
        return approvedhonorList;
    }
}
</script>
@stop
