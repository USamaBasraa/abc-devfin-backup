@extends('layouts.app',['pageTitle' => ''])
{{-- @dd("123A"); --}}
@section('content')
<style>
        .delete-button {
        background-color: transparent;
        border: none;
        }
        .row {
    display: flex;
    flex-wrap: wrap;
    }
    .card_header{
        border: 1px solid black;
        border-radius:10px 10px 0px 0px;
        padding:1%;
        font-size:18px;
        margin:2% 5% 0% 5%;
        background-color:#D2D6F1;
    }
    .bottom_tr{
        border: 1px solid black;
        border-radius:0px 0px 10px 10px;
        /* padding:5%; */
        font-size:18px;
        margin:0% 5% 5% 5%;
    }
    .resources_div{
        border-bottom:solid 1px black;
        padding:2px;
        font-size:18px;
    }
    .for_li{
         /* border:solid 1px green;  */
         margin-left:1%;
         font-size: 20px !important;
    }

    .description-wrapper {
        float: left;
        width: 70%;
        margin-left:3%;
        /* border:solid 1px red; */
    }
    .button-wrapper {
    float: left;
    width: 22%;
    /* padding-top:2%; */
    text-align: center;
    /* border:solid 1px green; */
    }
    label {
        font-weight: bold;
        font-size: 14px;
       font-family: "Helvetica Neue",Helvetica,Arial";
    }
    .btn_custome{
        height:80% !important;
        width:90% !important;
    }
    @media (min-width: 768px) {
    .description-wrapper {
        width: 80%;
        margin-left:1.5%;
        float: left;
        /* border:solid 1px black; */
    }

    .button-wrapper {
        width: 16%;
        text-align: left;
        float: left;
        /* border:solid 1px black; */
        padding-top:3%;
    }
    .check_uncheck_all_tr{
         border-bottom:solid 2px black;
         font-size:20px;
         color:green;
    }
}
    .user_outer_div{
        border:2px solid #3D4BB7;
        float:left;
        border-radius: 10px;
        margin-right:5px;
        padding:5px;
        margin: 1px;
        position: relative;
        background-color:#ccffee !important;
    }
    .user_inner_div{
            float:left;
    }
    .user_outer_div_strong {
            position: absolute;
            top: 1px; /* Adjust the top distance as needed */
            right: 5px; /* Adjust the right distance as needed */
            color: red;
            cursor: pointer;

    }
    /* Override the Select2 dropdown background */
.select2-dropdown {
    background-color: rgba(255, 255, 255, 0.9); /* A very light color with transparency */
}

/* Override the dropdown search box background */
.select2-search__field {
  background-color: transparent;
}

/* Additional CSS to make the dropdown and search box transparent */
.transparent-dropdown .select2-dropdown {
    background-color: rgba(255, 255, 255, 0.9); /* A very light color with transparency */
}

.transparent-search-box .select2-search__field {
  background-color: transparent;
}
</style>

<style>
  .filter-container {
    margin-bottom: 10px;
  }
  .filter-div {
    display: none;
  }

  .task-mgmt-wrapper {
        background-color: #f2f6f6;
        padding: 25px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #333;
    }

    /* Header Section */
    .header-title-section h2 {
        font-size: 22px;
        font-weight: 700;
        color: #2b3a3a;
        margin: 0;
    }
    .header-title-section p {
        font-size: 13px;
        color: #6c757d;
        margin: 0 0 15px 0;
    }

    /* Top Controls Row */
    .top-controls-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    /* Tabs Styling */
    .custom-pill-tabs {
        display: flex;
        align-items: center;
        gap: 10px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .custom-pill-tabs .nav-link {
        background: #ffffff;
        border-radius: 20px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        color: #4a5568;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .custom-pill-tabs .nav-link.active,
    .custom-pill-tabs .nav-link:hover {
        background-color: #1b5e54 !important;
        color: #ffffff !important;
    }

    /* Header Right Filters & Sort */
    .top-right-filters {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    .sort-dropdown-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
        color: #4a5568;
        outline: none;
    }
    .filter-checkbox-inline {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #4a5568;
        cursor: pointer;
    }
    .filter-checkbox-inline input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #1b5e54;
    }

    /* Form Container Card */
    .form-card-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 25px;
        /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); */
        margin-bottom: 25px;
    }

    /* Form Layout Grid */
    .form-grid-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px 25px;
    }
    .grid-full-width {
        grid-column: 1 / -1;
    }

    /* Labels & Inputs */
    .form-card-container label {
        font-weight: 600;
        font-size: 13px;
        color: #2d3748;
        margin-bottom: 6px;
        display: block;
    }
    .form-card-container .form-control,
    .form-card-container select {
        border: 1px solid #edf2f7;
        background-color: #fafbfc;
        border-radius: 8px;
        height: 42px;
        padding: 8px 14px;
        font-size: 13px;
        width: 100%;
        color: #4a5568;
        outline: none;
    }
    .form-card-container textarea.form-control {
        height: 120px;
        resize: vertical;
    }
    .form-card-container .form-control:focus {
        border-color: #1b5e54;
        background-color: #ffffff;
    }

    /* Select2 UI Fixes */
    .select2-container--default .select2-selection--single {
        border: 1px solid #edf2f7 !important;
        background-color: #fafbfc !important;
        border-radius: 8px !important;
        height: 42px !important;
        padding-top: 6px;
    }

    /* Custom Radio Controls */
    .radio-option-group {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-top: 10px;
    }
    .radio-option-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        cursor: pointer;
        margin: 0;
    }
    .radio-option-group input[type="radio"] {
        accent-color: #1b5e54;
        width: 16px;
        height: 16px;
    }

    /* Action Buttons Row */
    .form-action-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .left-actions {
        display: flex;
        gap: 12px;
    }
    .btn-custom-draft {
        background: #ffffff;
        border: 1px solid #1b5e54;
        color: #1b5e54;
        font-weight: 600;
        font-size: 13px;
        padding: 9px 24px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-custom-draft:hover {
        background: #f0fdf4;
    }
    .btn-custom-submit {
        background: #1b5e54;
        border: 1px solid #1b5e54;
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        padding: 9px 24px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-custom-submit:hover {
        background: #14473f;
    }
    .btn-custom-reset {
        background: #ffb703;
        border: none;
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        padding: 9px 24px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-custom-reset:hover {
        background: #fb8500;
    }

    /* Helper Utilities */
    .delete-button { background-color: transparent; border: none; }
    .user_outer_div {
        border: 2px solid #3D4BB7;
        float: left;
        border-radius: 10px;
        margin: 2px;
        padding: 5px;
        position: relative;
        background-color: #ccffee !important;
    }
    .user_inner_div { float: left; }
    .user_outer_div_strong {
        position: absolute;
        top: 1px;
        right: 5px;
        color: red;
        cursor: pointer;
    }
    .profile-overview-title {
    /* margin: 0 0 14px; */
    /* margin-left: 24px; */
    /* margin-top: 24px; */
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}


.custom-sort-wrapper {
    position: relative;
    display: inline-block;
}

.custom-sort-dropdown {
    /* Layout & Dimensions */
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding: 10px 40px 10px 18px;
    
    /* Colors & Border as per Screenshot */
    background-color: #e3eceb; /* Soft light greenish/greyish tint */
    color: #1a2332; /* Dark text color */
    border: 1.5px solid #6c757d; /* Soft dark border */
    border-radius: 12px; /* Smooth rounded corners */
    
    /* Typography */
    font-family: inherit;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease-in-out;
}

/* Custom Dropdown Arrow SVG Icon */
.custom-sort-wrapper::after {
    content: '';
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 7px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 7'%3E%3Cpath fill='none' stroke='%201a2332' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: contain;
    pointer-events: none; /* Allows click through to select box */
}

/* Hover & Focus States */
.custom-sort-dropdown:hover {
    background-color: #d8e4e3;
}

.custom-sort-dropdown:focus {
    border-color: #1a2332;
    /* box-shadow: 0 0 0 2px rgba(26, 35, 50, 0.1); */
}

/* Container for Pill Tabs */
.custom-pill-tabs {
    display: flex;
    align-items: center;
    gap: 12px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    border: none !important;
}

/* Individual Pill Button */
.custom-pill-tabs .nav-link {
    background-color: #ffffff;
    color: #1a2332;
    border: 1.5px solid #e2e8f0;
    border-radius: 25px; /* Pill Shape */
    padding: 8px 24px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    display: inline-block;
    text-align: center;
}

/* Hover State */
.custom-pill-tabs .nav-link:hover {
    border-color: #17675F;
    color: #17675F;
}

/* Active Tab State as per SS */
.custom-pill-tabs .nav-link.active {
    background-color: #17675F !important;
    color: #ffffff !important;
    border-color: #17675F !important;
    /* box-shadow: 0 2px 6px rgba(23, 103, 95, 0.2); */
}

/* Plus/PDF Action Buttons Alignment */
.custom-pill-tabs .tab-action-btn {
    display: flex;
    align-items: center;
    margin-left: 5px;
}

.custom-pill-tabs #toggleToDoForm {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #17675F;
    border: none;
}
.tabs-menu ul li a {
    padding: 10px 20px 11px 20px;
    display: block;
    font-size: 14px !important;
    font-weight: 500 !important;
    background-color: white;
    border-radius: 25px;
}
.tabs-menu ul li .active {
    background: #17675F;
    color: #fff !important;
    border-radius: 25px;
}


/* Custom Pills Styling */
.custom-pill-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    border: none !important;
}

.custom-pill-tabs .nav-link {
    background-color: #ffffff;
    color: #1a2332;
    border: 1.5px solid #e2e8f0;
    border-radius: 25px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    display: inline-block;
    text-align: center;
}

.custom-pill-tabs .nav-link:hover {
    border-color: #17675F;
    color: #17675F;
}

/* Active State Dark Teal Color */
.custom-pill-tabs .nav-link.active {
    background-color: #17675F !important;
    color: #ffffff !important;
    border-color: #17675F !important;
    /* box-shadow: 0 2px 6px rgba(23, 103, 95, 0.2); */
}

/* Plus Icon Button Adjustment */
.custom-pill-tabs #toggleToDoForm {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #17675F;
    border: none;
}

.custom-pill-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    border: none !important;
}

.custom-pill-tabs .nav-link {
    background-color: #ffffff;
    color: #1a2332;
    border: 1.5px solid #e2e8f0;
    border-radius: 25px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    display: inline-block;
    text-align: center;
}

.custom-pill-tabs .nav-link:hover {
    border-color: #17675F;
    color: #17675F;
}

.custom-pill-tabs .nav-link.active {
    background-color: #17675F !important;
    color: #ffffff !important;
    border-color: #17675F !important;
    /* box-shadow: 0 2px 6px rgba(23, 103, 95, 0.2); */
}

.custom-pill-tabs #toggleToDoForm {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #17675F;
    border: none;
}

#tab_innerdivabc [id^="row"] {
    margin-bottom: 24px !important;
}

#tab_innerdivabc [id^="row"]:last-child {
    margin-bottom: 0 !important;
}

/* ==================================================
   TASK LIST - NEW CARD DESIGN
   ================================================== */

#tab_innerdivabc {
    width: 100% !important;
    background: transparent !important;
    padding: 0 !important;
}


/* ---------- TASK ROW / CARD ---------- */

/* If returned rows are DIVs */
#tab_innerdivabc div[id^="row"] {
    background: #ffffff !important;
    border: 1px solid #edf1f1 !important;
    border-radius: 10px !important;

    min-height: 58px;
    padding: 13px 16px !important;

    margin-bottom: 14px !important;

    /* box-shadow: 0 3px 12px rgba(25, 70, 65, 0.06) !important; */

    box-sizing: border-box;
    transition: all 0.2s ease;
    margin-left: -2px !important;
    margin-right: -2px !important;
}


/* last row */
#tab_innerdivabc div[id^="row"]:last-child {
    margin-bottom: 0 !important;
}


/* subtle hover */
#tab_innerdivabc div[id^="row"]:hover {
    /* box-shadow: 0 5px 16px rgba(25, 70, 65, 0.09) !important; */
    transform: translateY(-1px);
}


/* ==================================================
   IF AJAX RESPONSE IS USING TABLE/TR
   ================================================== */

#tab_innerdivabc table {
    width: 100% !important;

    border-collapse: separate !important;
    border-spacing: 0 14px !important;

    background: transparent !important;
}


/* row */
#tab_innerdivabc tr[id^="row"] {
    background: transparent !important;
    filter: drop-shadow(0 3px 8px rgba(25, 70, 65, 0.06));
}


/* cells become one white card */
#tab_innerdivabc tr[id^="row"] > td {
    background: #ffffff !important;

    border-top: 1px solid #edf1f1 !important;
    border-bottom: 1px solid #edf1f1 !important;
    border-left: 0 !important;
    border-right: 0 !important;

    padding: 13px 16px !important;

    vertical-align: middle !important;
}


/* left rounded side */
#tab_innerdivabc tr[id^="row"] > td:first-child {
    border-left: 1px solid #edf1f1 !important;
    border-radius: 10px 0 0 10px !important;
}


/* right rounded side */
#tab_innerdivabc tr[id^="row"] > td:last-child {
    border-right: 1px solid #edf1f1 !important;
    border-radius: 0 10px 10px 0 !important;
}


/* ==================================================
   LEFT SIDE CONTENT
   ================================================== */

#tab_innerdivabc .description-wrapper {
    width: 80% !important;
    margin-left: 0 !important;
    padding-left: 8px !important;
}


/* main task title */
#tab_innerdivabc .description-wrapper strong,
#tab_innerdivabc .description-wrapper b {
    color: #1d2939 !important;

    font-size: 13px !important;
    font-weight: 600 !important;

    line-height: 1.35;
}


/* secondary / assigned information */
#tab_innerdivabc .description-wrapper {
    color: #8a979b !important;

    font-size: 10px !important;
    line-height: 1.45 !important;
}


/* ==================================================
   RIGHT SIDE ACTIONS
   ================================================== */

#tab_innerdivabc .button-wrapper {
    width: 18% !important;

    padding-top: 0 !important;

    text-align: right !important;

    vertical-align: middle !important;
}


/* links */
#tab_innerdivabc .button-wrapper a {
    color: #17675F !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    text-decoration: none !important;

    margin-left: 12px;
    white-space: nowrap;
}


#tab_innerdivabc .button-wrapper a:hover {
    color: #0f514b !important;
    text-decoration: none !important;
}


/* right-side icons */
#tab_innerdivabc .button-wrapper i {
    font-size: 11px !important;
    color: #17675F !important;
}


/* ==================================================
   CHECKBOX
   ================================================== */

#tab_innerdivabc input[type="checkbox"],
#tab_innerdivabc .subtask-checkbox {
    width: 14px !important;
    height: 14px !important;

    margin: 0 10px 0 0 !important;

    accent-color: #17675F;

    cursor: pointer;
}


/* ==================================================
   PRIORITY BADGES
   ================================================== */

#tab_innerdivabc .badge {
    border-radius: 20px !important;

    padding: 5px 11px !important;

    font-size: 9px !important;
    font-weight: 500 !important;

    line-height: 1 !important;

    border: 0 !important;
}


/* Long Term */
#tab_innerdivabc .badge-warning,
#tab_innerdivabc .bg-warning {
    background: #fff2c9 !important;
    color: #dfa700 !important;
}


/* Urgent */
#tab_innerdivabc .badge-danger,
#tab_innerdivabc .bg-danger {
    background: #ffe4e6 !important;
    color: #f45d68 !important;
}


/* Completed */
#tab_innerdivabc .badge-success,
#tab_innerdivabc .bg-success {
    background: #e0f3ed !important;
    color: #17675F !important;
}


/* ==================================================
   OLD LIST STYLING RESET
   ================================================== */

#tab_innerdivabc .for_li {
    margin-left: 0 !important;
    font-size: inherit !important;
}


/* remove old borders */
#tab_innerdivabc .resources_div {
    border-bottom: 0 !important;
}


/* ==================================================
   MOBILE
   ================================================== */

@media (max-width: 767px) {

    #tab_innerdivabc div[id^="row"] {
        padding: 12px !important;
    }

    #tab_innerdivabc .description-wrapper {
        width: 72% !important;
    }

    #tab_innerdivabc .button-wrapper {
        width: 26% !important;
    }

    #tab_innerdivabc .button-wrapper a {
        margin-left: 6px;
        font-size: 10px !important;
    }
}

#tab_innerdivabc .fa-align-justify {
    display: none !important;
}

/* ==================================================
   TASK DETAIL SECTION - COMPACT NEW DESIGN
   ================================================== */

/* Main expanded detail area */
#tab_innerdivabc .bottom_tr {
    width: 100% !important;

    margin: 10px 0 0 0 !important;
    padding: 12px 0 0 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;
    border-radius: 0 !important;

    font-size: 10px !important;

    box-sizing: border-box !important;
}


/* remove old unnecessary spacing */
#tab_innerdivabc .bottom_tr .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}


/* columns */
#tab_innerdivabc .bottom_tr [class*="col-"] {
    padding-left: 8px !important;
    padding-right: 8px !important;
}


/* ==================================================
   DESCRIPTION / SCHOOL / DATE / DEPARTMENT ITEMS
   ================================================== */

#tab_innerdivabc .bottom_tr .resources_div {
    position: relative !important;

    border: 0 !important;
    border-bottom: 0 !important;

    margin: 0 0 10px 0 !important;
    padding: 0 0 0 9px !important;

    min-height: 28px;

    background: transparent !important;

    color: #667085 !important;

    font-size: 10px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
}


/* small vertical accent line */
#tab_innerdivabc .bottom_tr .resources_div::before {
    content: "";

    position: absolute;
    left: 0;
    top: 1px;

    width: 2px;
    height: calc(100% - 2px);

    background: #25b99a;
    border-radius: 2px;
}


/* alternate accent colors */
#tab_innerdivabc .bottom_tr .resources_div:nth-child(2n)::before {
    background: #8c63e6;
}


/* ==================================================
   LABELS
   Description
   Reminder Date / Time
   School
   Department
   ================================================== */

#tab_innerdivabc .bottom_tr .resources_div label,
#tab_innerdivabc .bottom_tr .resources_div .badge {
    display: block !important;

    width: auto !important;

    margin: 0 0 2px 0 !important;
    padding: 0 !important;

    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;

    color: #7b8794 !important;

    font-size: 9px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;
}


/* detail values */
#tab_innerdivabc .bottom_tr .resources_div p,
#tab_innerdivabc .bottom_tr .resources_div span:not(.badge),
#tab_innerdivabc .bottom_tr .resources_div strong,
#tab_innerdivabc .bottom_tr .resources_div b {
    margin: 0 !important;

    color: #667085 !important;

    font-size: 10px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
}


/* ==================================================
   BOTTOM ACTION AREA
   ================================================== */

#tab_innerdivabc .bottom_tr hr {
    margin: 2px 0 10px 0 !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;
}


/* Edit/Delete buttons */
#tab_innerdivabc .bottom_tr .btn {
    min-width: 50px !important;
    height: 27px !important;

    padding: 4px 11px !important;

    border-radius: 5px !important;

    font-size: 9px !important;
    font-weight: 500 !important;
    line-height: 17px !important;

    box-shadow: none !important;

    margin-right: 6px !important;
}


/* first button = Edit */
#tab_innerdivabc .bottom_tr .btn:first-of-type {
    background: #17675F !important;
    border: 1px solid #17675F !important;
    color: #ffffff !important;
}


/* second button = Delete */
#tab_innerdivabc .bottom_tr .btn:nth-of-type(2) {
    background: #ffffff !important;
    border: 1px solid #17675F !important;
    color: #17675F !important;
}


/* button hover */
#tab_innerdivabc .bottom_tr .btn:first-of-type:hover {
    background: #12544e !important;
    border-color: #12544e !important;
}

#tab_innerdivabc .bottom_tr .btn:nth-of-type(2):hover {
    background: #f2f8f7 !important;
}


/* ==================================================
   TASK SORTING ORDER DROPDOWN
   ================================================== */

#tab_innerdivabc .bottom_tr select,
#tab_innerdivabc .bottom_tr .form-control {
    height: 29px !important;

    padding: 4px 9px !important;

    background: #ffffff !important;

    border: 1px solid #dfe6e9 !important;
    border-radius: 5px !important;

    color: #667085 !important;

    font-size: 9px !important;

    box-shadow: none !important;
}


/* sorting dropdown ko right side compact rakho */
#tab_innerdivabc .bottom_tr select {
    max-width: 150px !important;
    margin-left: auto !important;
}


/* focus */
#tab_innerdivabc .bottom_tr select:focus,
#tab_innerdivabc .bottom_tr .form-control:focus {
    border-color: #17675F !important;
    box-shadow: none !important;
}


/* ==================================================
   MOBILE DETAIL
   ================================================== */

@media (max-width: 767px) {

    #tab_innerdivabc .bottom_tr {
        padding-top: 10px !important;
    }

    #tab_innerdivabc .bottom_tr [class*="col-"] {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    #tab_innerdivabc .bottom_tr select {
        max-width: 100% !important;
        width: 100% !important;
        margin-top: 8px !important;
    }
}

/* TEST: confirm whether detail is really inside .bottom_tr */
/* ==================================================
   TASK DETAIL SECTION - CORRECT SELECTOR
   ================================================== */

/* demo7326, demo1234, demo5678 etc */
#tab_innerdivabc div[id^="demo"].collapse {
    width: 100% !important;

    margin: 12px 0 0 0 !important;
    padding: 14px 12px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;
    border-radius: 0 !important;

    box-sizing: border-box !important;

    font-size: 10px !important;
}


/* rows inside detail */
#tab_innerdivabc div[id^="demo"] .row {
    margin-left: -6px !important;
    margin-right: -6px !important;
}


/* columns */
#tab_innerdivabc div[id^="demo"] [class*="col-"] {
    padding-left: 6px !important;
    padding-right: 6px !important;
}


/* detail information blocks */
#tab_innerdivabc div[id^="demo"] .resources_div {
    position: relative !important;

    margin: 0 0 10px 0 !important;
    padding: 0 0 0 9px !important;

    border: 0 !important;
    border-bottom: 0 !important;

    min-height: 28px;

    background: transparent !important;

    font-size: 10px !important;
    line-height: 1.4 !important;
}


/* left vertical line like reference SS */
#tab_innerdivabc div[id^="demo"] .resources_div::before {
    content: "";
    position: absolute;

    left: 0;
    top: 1px;

    width: 2px;
    height: calc(100% - 2px);

    background: #28b99a;

    border-radius: 2px;
}


/* alternating purple line */
#tab_innerdivabc div[id^="demo"] .resources_div:nth-child(even)::before {
    background: #8f67e8;
}


/* labels */
#tab_innerdivabc div[id^="demo"] .resources_div label,
#tab_innerdivabc div[id^="demo"] .resources_div .badge {
    display: block !important;

    width: auto !important;

    margin: 0 0 2px 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    color: #7d8896 !important;

    font-size: 9px !important;
    font-weight: 400 !important;

    line-height: 1.2 !important;
}


/* values */
#tab_innerdivabc div[id^="demo"] .resources_div p,
#tab_innerdivabc div[id^="demo"] .resources_div span:not(.badge),
#tab_innerdivabc div[id^="demo"] .resources_div strong,
#tab_innerdivabc div[id^="demo"] .resources_div b {
    margin: 0 !important;

    color: #667085 !important;

    font-size: 10px !important;
    font-weight: 400 !important;

    line-height: 1.4 !important;
}


/* separator */
#tab_innerdivabc div[id^="demo"] hr {
    margin: 5px 0 10px !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;
}


/* Edit / Delete */
#tab_innerdivabc div[id^="demo"] .btn {
    height: 27px !important;
    min-width: 50px !important;

    padding: 4px 11px !important;
    margin-right: 6px !important;

    border-radius: 5px !important;

    font-size: 9px !important;
    font-weight: 500 !important;

    line-height: 17px !important;

    box-shadow: none !important;
}


/* Edit */
#tab_innerdivabc div[id^="demo"] .btn:first-of-type {
    background: #17675F !important;
    border: 1px solid #17675F !important;
    color: #ffffff !important;
}


/* Delete */
#tab_innerdivabc div[id^="demo"] .btn:nth-of-type(2) {
    background: #ffffff !important;
    border: 1px solid #17675F !important;
    color: #17675F !important;
}


/* Sorting dropdown */
#tab_innerdivabc div[id^="demo"] select {
    width: 150px !important;
    max-width: 150px !important;
    height: 29px !important;

    padding: 4px 9px !important;

    margin-left: auto !important;

    background: #ffffff !important;

    border: 1px solid #dfe6e9 !important;
    border-radius: 5px !important;

    color: #667085 !important;

    font-size: 9px !important;

    box-shadow: none !important;
}


</style>
</head>
<body>
<!-- <div class="filter-container">
  <label>
    <input type="checkbox" id="categoryCheckbox"> Filter by Category
  </label>
</div>

<div class="filter-container">
  <label>

  </label>
</div> -->
<input type="hidden" id="employee_id" value="{{auth()->user()->employee_id}}">

<div class="row" style="margin-bottom: 30px !important;">
    <div class="col-sm-6">
        <h3 class="profile-overview-title" style="margin-bottom: 0 !important;">Task Management</h3>
        <span style="font-size: 18px;color: #252525;font-weight: 400;">
            Stay Organized
        </span>
    </div>
    <div class="col-sm-6 text-right d-flex align-items-center justify-content-end">
         <div class=" custom-sort-wrapper">
                                                        {{-- <label for="sortDropdown">Sort by:</label>
                                                        <select  id="sortDropdown" onchange="fetchData()">
                                                            <option value="Latest">Latest to Oldest</option>
                                                            <option value="OldestFirst">Oldest to Latest</option>
                                                            <option value="User">User</option>
                                                            <option value="School">School</option>
                                                            <option value="Department">Department</option>
                                                            <option value="Deadline">Deadline</option>
                                                        </select> --}}

                                                        <div class="custom-sort-wrapper">
    <select id="sortDropdown" class="custom-sort-dropdown" onchange="fetchData()">
        <option value="Latest">Sort by: Latest to Oldest</option>
        <option value="OldestFirst">Sort by: Oldest to Latest</option>
        <option value="User">Sort by: User</option>
        <option value="School">Sort by: School</option>
        <option value="Department">Sort by: Department</option>
        <option value="Deadline">Sort by: Deadline</option>
    </select>
</div>
                                                    </div>
    </div>
</div>

<div style="margin-left:auto;margin-right:auto;width:100%;border:solid 0px red;">
    <div style="border:solid 0px black;margin-left:auto;margin-right:auto;width:100%;">

    <table border="0" width="100%" cellpadding="0px">
                <tr style="background-color: lightgray;">
                    <td width="40%" style="background-color: lightgray;">
                        @if(isset($_REQUEST['category_name']) && $_REQUEST['category_name'] != "")
                                <label for="task_title"><?=$_REQUEST['category_name'];?></label>
                        @endif
                    </td>
                    {{-- <td width="33%" style="background-color: lightgray;"> <h2 class="pt-3">Task Management</h2> </td> --}}
                    <td>
                    </td>
                    <td>
                    @if(isset($_REQUEST['id']) && $_REQUEST['id'] != "")
                            <label for="task_title"></label>
                            <button type="button"  class="btn btn-primary" id="toggleToDoForm" style="display:none">
                                <i id="iconToggleToDoForm" class="fa fa-plus"></i>
                            </button>
                    @endif
                    </td>
                </tr>
            </table>
        <!-- <div class="jumbotron text-center" style="padding: 0rem;margin-bottom: 0rem;">
            <h1>Task Management</h1>
        </div> -->
        <!--  tab bar start--------------------- -->
            <div class="div_web" style="background-color: #f2f6f9;">
            <div class="container-fluid" style="padding-left: 0 !important; padding-right: 0 !important;">
                    <div class="panel panel-primary tabs-style-3 row">
                          <div class="col-md-6" style="margin-top:2px;margin-bottom:2px;padding-top:6px !important;padding-top: 10px !important;padding-bottom: 30px !important;" >
                            {{-- <div class="tabs-menu" style="margin-bottom: 30px;">
                                <!-- Tabs -->
                                <ul class="nav panel-tabs" id="ul_show_hide">
                                    <li style="margin-right:12px;">
                                        <a href="#taball" id="aidall" data-categoryid="all" class="active" data-toggle="tab" onclick="setCategory('All'),fetchData(), hide_form(this)">
                                            All
                                        </a>
                                    </li>
                                    @if(auth()->user()->employee_id!="8996")
                                    <li style="margin-right:12px;">
                                        <a href="#tabcomplete" id="aidcomplete" data-categoryid="complete" data-toggle="tab" onclick="setCategory('AssignedMe'),fetchData(), hide_form(this)">
                                            Received Task
                                        </a>
                                    </li>
                                    @endif
                                    <li style="margin-right:12px;">
                                        <a href="#tabcomplete" id="aidcomplete" data-categoryid="assign" data-toggle="tab" onclick="setCategory('AssignedOthers'),fetchData(), hide_form(this)">
                                            <!-- Assign Task -->
                                            Assigned Task
                                        </a>
                                    </li>
                                    <li style="margin-right:12px;">
                                        <a href="#tabcomplete" id="aidcomplete" data-categoryid="draft" data-toggle="tab" onclick="setCategory('Draft'),fetchData(), hide_form(this)">
                                            Draft
                                        </a>
                                    </li>
                                    <li style="margin-right:12px;">
                                        <a href="#tabcomplete" id="aidcomplete" data-categoryid="completed" data-toggle="tab" onclick="setCategory('Completed'),fetchData(), hide_form(this)">
                                            Completed
                                        </a>
                                    </li>
                                    <li style="width:10% !important;padding-left:10px">
                                        <button type="button" class="btn btn-success" id="toggleToDoForm" >
                                            <i id="iconToggleToDoForm" class="fa fa-plus"></i>
                                        </button>
                                    </li>
                                    <?php if(auth()->user()->username=='moeedyusuf' || auth()->user()->username=='sumair.shafique'|| auth()->user()->username=='bilal.saleem' || auth()->user()->username=='salman.zafar' ){?>

                                        <li style="width:10% !important;">
                                            <i style="cursor:pointer;color: red" class="fa fa-file-pdf-o fa-2x" onclick="pdf()"></i>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div> --}}
                            <div class="tabs-menu">
    <ul class="nav custom-pill-tabs" id="ul_show_hide">
        <li>
            <a href="#taball" id="aidall" data-categoryid="all" class="nav-link active" data-toggle="tab" onclick="setCategory('All'),fetchData(), hide_form(this)">
                All
            </a>
        </li>
        @if(auth()->user()->employee_id!="8996")
        <li>
            <a href="#tabcomplete" id="aidcomplete" data-categoryid="complete" class="nav-link" data-toggle="tab" onclick="setCategory('AssignedMe'),fetchData(), hide_form(this)">
                Received Task
            </a>
        </li>
        @endif
        <li>
            <a href="#tabcomplete" class="nav-link" data-categoryid="assign" data-toggle="tab" onclick="setCategory('AssignedOthers'),fetchData(), hide_form(this)">
                Assigned Task
            </a>
        </li>
        <li>
            <a href="#tabcomplete" class="nav-link" data-categoryid="draft" data-toggle="tab" onclick="setCategory('Draft'),fetchData(), hide_form(this)">
                Draft
            </a>
        </li>
        <li>
            <a href="#tabcomplete" class="nav-link" data-categoryid="completed" data-toggle="tab" onclick="setCategory('Completed'),fetchData(), hide_form(this)">
                Completed
            </a>
        </li>
        <li class="ml-2">
            <button type="button" class="btn btn-success" id="toggleToDoForm">
                <i id="iconToggleToDoForm" class="fa fa-plus"></i>
            </button>
        </li>
        @if(in_array(auth()->user()->username, ['moeedyusuf', 'sumair.shafique', 'bilal.saleem', 'salman.zafar']))
        <li class="ml-2">
            <i style="cursor:pointer; color: red;" class="fa fa-file-pdf-o fa-2x" onclick="pdf()"></i>
        </li>
        @endif
    </ul>
</div>
                        </div>
                            <div class="col-md-6 text-right" style="margin-top:2px;margin-bottom:2px;padding-top:6px !important;padding-top: 10px !important;" >
                                <div>
                                   <div class="col-md-12 text-right">
                                   <table border="0" cellspacing="8" cellpadding="8" style="border-radius:5px;    float: right;" >
                                            <tr>
                                                <td>Filter by:</td>
                                                <td>
                                                        Urgent: <input type="checkbox" name="selection[]" checked value="Urgent" onchange="fetchData()">
                                                </td>
                                                <td>
                                                        Long Term: <input type="checkbox" name="selection[]" checked value="LongTerm" onchange="fetchData()">
                                                </td>
                                                <td>
                                                 <input type="checkbox" id="categoryCheckbox"> Filter by Category </td>
                                                 <td>
                                            <input type="checkbox" id="userCheckbox"> Filter by User
                                            </td>
                                            </tr>
                                        </table>
                                    </div>
                                    {{-- <div class="col-md-6 text-right d-flex align-items-center justify-content-end" style="gap: 15px;">
                                           
                                    </div> --}}
                                    <div class="col-md-3">
                                        <table border="0" cellspacing="4" cellpadding="4">
                                            <tr>
                                                {{-- <td colspan="2">
                                                    <div class="form-check">
                                                        <label for="sortDropdown">Sort by:</label>
                                                        <select  id="sortDropdown" onchange="fetchData()">
                                                            <option value="Latest">Latest to Oldest</option>
                                                            <option value="OldestFirst">Oldest to Latest</option>
                                                            <option value="User">User</option>
                                                            <option value="School">School</option>
                                                            <option value="Department">Department</option>
                                                            <option value="Deadline">Deadline</option>
                                                        </select>
                                                    </div>
                                                </td> --}}
                                            </tr>
                                        </table>
                                     </div>
                                </div>
                            </div>
                            <div class="col-md-12 filter-div" id="filter_of_category" style="margin-top:2px;background-color:#FFA500;margin-bottom:2px;border:solid 0px #4D5CC6;border-radius:5px;padding-top:6px !important;" >
                                <div class="row">
                                   <div class="col-md-2">
                                        <div class="form-check">
                                            <label for="sortDropdown">Select Category:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="form-check">
                                    <table border="0">
                                    <tr>
                                        @foreach ($sql_tmc as $tmc_category)
                                            <td align="center" width="180px">
                                                {{ $tmc_category->tmc_name }} <br>
                                                <input type="checkbox" checked value="{{ $tmc_category->tmc_id }}" name="filter_tmc_name[]" class="filter-checkbox"><br>
                                            </td>
                                        @endforeach
                                </tr>
                                </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 filter-div" id="filter_of_users" style="margin-top:2px;background-color:#40E0D0;margin-bottom:2px;border:solid 0px #4D5CC6;border-radius:5px;padding-top:6px !important;" >
                                <div class="row">
                                   <div class="col-md-2">
                                        <div class="form-check">
                                            <label for="sortDropdown">Select User:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                    <div class="form-check">
                                        <select name="staff_for_filter" id="staff_for_filter" class="form-control select2">
                                                <option value="0|0|0" style="background-color: yellow;font-weight: bold;">Select School/ Department/ Staff</option>
                                                @foreach ($staffMembers as $staff)
                                                    <option value="{{$staff->parent_id . '|' . $staff->id. '|' . $staff->ordername }}">
                                                        {{ $staff->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           </div>
                    </div>
                </div>
        <!-- -------------tab end  -->
        <?php
           // print_r($staffMembers);
        ?>
        <div class="page-body" id="toDoDivForm" style="width:93%;background-color:#D2D6F1;display:none;border: 1px solid green;border-radius: 5px; margin-left:auto; margin-right:auto;">
            <div class="col-sm-12">
                <form id="taskForm"  method="POST">
                    @csrf
                    <div class="container-fluid">
                    <!-- ----------- -->
                        <div class="row"  id="show_staff_pannel" >
                            <div class="col-sm-6">
                                <div class="row">
                                    <label for="completion_date">Group Task:</label>
                                    <input type="checkbox"  name="group_task" id="group_task" >
                                    <div class="col-sm-11" id="show_edit_div"></div>
                                    <div class="col-sm-11" id="staff_div_row">
                                            <label for="staff">School / Department :</label>
                                            <select name="staff" id="staff" class="form-control select2" style="width:100% !important;">
                                                <!-- <option value="0|0|3" style="background-color: yellow;font-weight: bold;">Select School / Department / Staff:</option> -->
                                                <option value="0|0|3" style="background-color: yellow;font-weight: bold;">Select School/ Department/ Staff:</option>
                                                @foreach ($staffMembers as $staff)
                                                    <option value="{{$staff->parent_id . '|' . $staff->id. '|' . $staff->ordername }}">
                                                        {{ $staff->name }}

                                                    </option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-1" id="clear_button">
                                        <label for="staff">&nbsp;</label>
                                        <i class="fa fa-history" onclick="reset_users()" style="font-size:16px;cursor:pointer;color:blue;"> Clear </i>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <label for="tmc_categ">Task Categories :</label> <br>
                                 <table border="1">
                                    <tr>
                                @foreach ($sql_tmc as $tmc_category)
                                    <td align="center" width="180px">
                                    {{ $tmc_category->tmc_name }} <br>
                                        <input type="checkbox" @if ($tmc_category->tmc_id == 1) checked @endif value="{{ $tmc_category->tmc_id }}" name="tmc_name[]" id="tmc_name"><br><br>
                                    </td>
                                @endforeach
                                </tr>
                                </table>

                            </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 recordsDiv" >
                        </div>
                    </div>
                    <div class="row" id="date_time_desc_group"  style="display:none">
                        <div class="col-sm-12">
                            <div class="row" >
                            <!-- onkeypress="callJqFunction(event)" -->
                                <div class="col-sm-6">
                                    <label for="task_title">Task / Project :</label>
                                    <strong style="float:right;color:blue;"> Word Count: <span id="wordCount">0</span></strong>
                                    <strong  style="float:right;color:blue;">Character Count: <span id="charCount">0</span> &nbsp;</strong>
                                    <!-- <input type="textbox" name="description" class="form-control" id="description"  maxlength="120"> -->
                                    <textarea name="description" id="description" class="form-control" placeholder="Please enter Task " maxlength="1900"  rows="2" cols="40" oninput="countWordsAndCharacters(this)"></textarea>

                                    <input type="hidden" name="task_category_id" id="task_category_id" value="13">
                                    <input type="hidden" name="task_category_id1" id="task_category_id1" value="13">

                                    <input type="hidden" id="edit_tm_id" name="edit_tm_id" >
                                    <input type="hidden" id="edit_row_id" name="edit_row_id" >
                                    <input type="hidden" id="btn_status" name="btn_status" >
                                </div>
                            <div class="col-sm-2">
                                <label for="completion_date">Reporting Date:</label>
                                <input type="checkbox" checked name="noTime" id="noTime" >

                                <input type="date" name="completion_date" class="form-control" id="completion_date">
                                <span id="dateMsg"></span>
                            </div>
                            <div class="col-sm-2">
                                <label for="completion_time">Reporting Time:</label>
                                <select name="completion_time" class="form-control" id="completion_time">
                                    <?php
                                        $start_time = strtotime('08:00 AM'); // Set the start time to 08:00 AM
                                        $end_time = strtotime('23:00');
                                        $interval = 30 * 60; // 1 hour interval
                                        echo '<option value="">--Select Time--</option>';
                                        for ($time = $start_time; $time <= $end_time; $time += $interval) {
                                            $display_time = date('h:i A', $time);
                                            $option_value = date('H:i', $time);
                                            echo '<option value="' . $option_value . '">' . $display_time . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2" >
                                <div class="form-check">
                                    <label class="form-check-label" for="No time">
                                        Add Description
                                    </label>
                                    <div onclick="toggleSortingIcon()">
                                        <i id="sortingIcon" class="fa fa-sort-desc" style="font-size:36px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row" id="type_desc_button"style="display:none">
                            <div class="col-sm-12">
                                <label for="description">Description:</label>
                                <span style="color: red; font-weight: bold;font-size:12px">
                                    (Only 3000 words are allowed.)
                                </span>

                                <textarea name="task_title" id="task_title" class="form-control" placeholder="Please enter something" oninput="handleTextareaChange(this)" maxlength="2600" rows="5" cols="40"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="padding-top: 15px;">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input onchangeRadio" type="radio" name="tasktype"  id="radio1" checked="checked" value="Longterm">
                            <label class="form-check-label" for="radio1">
                                Long Term
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input onchangeRadio" type="radio" name="tasktype"  id="radio1"  value="Urgent">
                            <label class="form-check-label" for="radio1">
                                Urgent
                            </label>
                        </div>
                    </div>

                </div>
                <!-- <div class="form-check">
                    <input class="form-check-input" name="tasktype" type="checkbox" id="longTermCheckbox">
                    <label class="form-check-label" for="longTermCheckbox"><span id="chkboxTxt">Mark Task as Long Term</span></label>
                </div> -->
                </div>

                <div class="row" id="only_button" style="display:non1e">
                    <div class="col-sm-3">
                        <br>
                        <!-- <button type="submit" class="btn-block btn btn-success" ><i class="fe fe-check mr-2"></i>Submit Task</button> -->

                        <button type="button" onclick="insert('Draft')"   class="btn-block btn btn-primary btnPropDraft">
                            <i class="fe fe-edit-2"></i>   Save as Draft
                        </button>

                        <br>
                    </div>
                    <div class="col-sm-3">
                        <br>
                        <!-- <button type="submit" class="btn-block btn btn-success" ><i class="fe fe-check mr-2"></i>Submit Task</button> -->

                        <button type="button"  onclick="insert('Pending')" class="btn-block btn btn-success btnPropPending">
                            <i class="fe fe-check mr-2"></i>Submit Task
                        </button>

                        <br>
                    </div>
                    <div class="col-sm-3" style="padding-top:19px">
                        <span id="errorMsg"></span>
                        <span id="DraftMsg" style="font-weight: bold;color: green;"></span>
                    </div>
                    <div class="col-sm-3" style="padding-top:19px">
                        <button type="button"  onclick="reset_form()" class="btn-block btn btn-warning">
                            <i class="fe fe-check mr-2"></i>Reset Form
                        </button>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div id="tab_innerdivabc" style="width:99%; margin-left:auto; margin-right:auto;background-color:white;"></div>
</div>

@endsection
@section('javascript')
<script>
// checkboxes for filters
function capitalizeSentences(text) {
    if (text.length === 0) {
        return text;
    }

    // Split the text into sentences
    var sentences = text.split(/([.!?]+\s*)/);

    // Capitalize the first letter of each sentence
    var capitalizedSentences = sentences.map(function(sentence) {
        // If the sentence is not empty, capitalize the first letter
        if (sentence.trim().length > 0) {
            return sentence.charAt(0).toUpperCase() + sentence.slice(1);
        }

        return sentence;
    });

    // Join the sentences back together with the appropriate punctuation
    return capitalizedSentences.join('');
}


        function handleTextareaChange(textarea){
         // Capitalize the first letter of the text
             textarea.value = capitalizeSentences(textarea.value);
            }
function countWordsAndCharacters(textarea) {
  const text = textarea.value.trim();
  const words = text.split(/\s+/).filter(word => word !== '');
  const wordCount = words.length;

// Capitalize the first letter of the text
  textarea.value = capitalizeSentences(textarea.value);

  document.getElementById('wordCount').textContent = wordCount;
  document.getElementById('charCount').textContent = text.length;

  if (text.length > 1800) {
    textarea.style.borderColor = 'red'; // You can change the style as needed
  } else {
    textarea.style.borderColor = ''; // Reset the style if within limit
  }
}

const categoryCheckbox = document.getElementById("categoryCheckbox");
const userCheckbox = document.getElementById("userCheckbox");
const categoryDiv = document.getElementById("filter_of_category");
const userDiv = document.getElementById("filter_of_users");

  categoryCheckbox.addEventListener("change", updateVisibility);
  userCheckbox.addEventListener("change", updateVisibility);

  function updateVisibility() {
    if (categoryCheckbox.checked && userCheckbox.checked) {
      categoryDiv.style.display = "block";
      userDiv.style.display = "block";
    } else if (categoryCheckbox.checked) {
      categoryDiv.style.display = "block";
      userDiv.style.display = "none";
    } else if (userCheckbox.checked) {
      userDiv.style.display = "block";
      categoryDiv.style.display = "none";
    } else {
      categoryDiv.style.display = "none";
      userDiv.style.display = "none";
    }
  }



// Function to fetch records from the controller function

var appendedIDs = {}; // Object to store the already appended employee IDs
function focusSelect2SearchBox() {
  // Assuming "staff" is the ID of your Select2 select box
  $('#staff').select2('open'); // Opens the dropdown
  $('.select2-search__field').focus(); // Focuses on the search input

  // Add the class to the dropdown and search box after opening
  $('.select2-dropdown').addClass('transparent-dropdown');
  $('.select2-search__field').addClass('transparent-search-box');
}
function reset_users(){
    $('.recordsDiv').html('');
    $("#staff_div_row").show();
    $("#clear_button").show();

    $("#show_edit_div").hide();
    appendedIDs = {};
    resetDropdown();
}
function reset_form(){
    $("#taskForm")[0].reset();
    $("#staff_div_row").show();
    $("#clear_button").show();
    $("#show_edit_div").hide();
    appendedIDs = {};
    resetDropdown();
}



function fetchRecordsFromController(staffId) {

   var explodedArray = staffId.split('|');

   //alert(explodedArray[2]+"__"+staffId);

   if(explodedArray[2]=="G"){
       appendedIDs = {};
       $('.recordsDiv').html('');
   }
       $.ajax({
           url: 'fetch_staff_data?staffId='+staffId, // Replace with the actual URL of your controller function
           method: 'GET', // Use GET or POST depending on your server-side implementation
           dataType: 'json', // Expect JSON data as the response
           success: function(response) {
               console.log(response);
              // alert(response.length);
               // Upon success, handle the data and append it to the div
               if (response && response.length > 0) {
                   // Assuming the response is an array of records
                   var recordsDiv = $('.recordsDiv');
                   var editDivContent = '';
                  // recordsDiv.append('<tr>');
                  for (var i = 0; i < response.length; i++) {
                       var record = response[i];
                       // Check if the employee_id is already appended
                       console.log(appendedIDs.hasOwnProperty(record.employee_id),'bilal saleem');
                       if (!appendedIDs.hasOwnProperty(record.employee_id)) {
                           // If not, append the record and mark the employee_id as appended in the object
                           if(i%5 == 0) {
                              // recordsDiv.append('</tr><tr>');
                           }
                           //  alert(explodedArray[2]+"__"+staffId);

                           var imageSrc = 'https://cms.bnu.edu.pk/assets/staffimages/' + record.employee_id + '.jpg';
                           var fallbackImage = 'https://cms.bnu.edu.pk/assets/staffimages/avatar-4.png';

                           var append_div = '<div id="tab' + record.employee_id + '" class="user_outer_div">' +
                                           '<div class="user_inner_div">' +
                                           '<img src="' + imageSrc + '" onerror="this.src=\'' + fallbackImage + '\'" alt="" class="avatar avatar-md brround">' +
                                           '</div>' +
                                           '<div class="user_inner_div">' +
                                           '<strong> &nbsp;' + record.emp_f_name + ' ' + record.emp_l_name + '</strong><br>' +
                                           '<input type="hidden" name="user[]" value="' + record.employee_id + '">' +
                                           record.dept_name +
                                           '</div>' +
                                           '<div class="user_inner_div">&nbsp;&nbsp;&nbsp;&nbsp;</div>';
                                           // Check if explodedArray[2] is not equal to "edit"
                                           if (explodedArray[2] !== "edit") {
                                               append_div += '<div class="user_inner_div inner_div_when_edit" onclick="remove_table_td(' + record.employee_id + ')">' +
                                                   '<div class="user_outer_div_strong">X</div>' +
                                                   '</div>';
                                           }
                                       append_div += '</div>';
                                       if (explodedArray[2] !== "edit") {
                                           recordsDiv.append(append_div);
                                       }else{
                                           editDivContent += append_div;
                                           //recordsDiv.append(append_div);
                                           //$('#clear_button').hide();
                                       }

                                      // recordsDiv.append(append_div);
                           resetDropdown();
                           //var user_image = '<img src="htt://cmstesttest.bnu.edu.pk/assets/staffimages/8996.jpg" width="30px" alt="NA">';

                          // recordsDiv.append('<p>'+user_image+'<input type="text" name="user_id[]" value="' + record.employee_id + '">' + record.emp_f_name + '  ' + record.emp_l_name + '</p>');
                           appendedIDs[record.employee_id] = true;
                           focusSelect2SearchBox();
                       }
                   }
                   if (explodedArray[2] === "edit") {
                       $('#show_edit_div').html('');
                       $('#show_edit_div').append(editDivContent);
                   }
                  // recordsDiv.append('</tr>');

               } else {
                   // Handle case when no records are returned
                  // $('.recordsDiv').html('<p>No records found.</p>');
               }
           },
           error: function(error) {
               // Handle errors (optional)
               console.error('Error fetching records:', error);
               //$('.recordsDiv').html('<p>Error fetching records.</p>');
           }
       });
   }
// Function to check if the image exists
function imageExists(imageUrl) {
    var http = new XMLHttpRequest();
    http.open('HEAD', imageUrl, false);
    http.send();
    return http.status !== 404;
}

$('#staff').on('change', function() {
    fetchRecordsFromController(this.value);
});
$('#staff_for_filter').on('change', function() {
    fetchData();
});

function toggleSortingIcon() {
  const icon = document.getElementById("sortingIcon");
  if (icon.classList.contains("fa-sort-desc")) {
    icon.classList.remove("fa-sort-desc");
    icon.classList.add("fa-sort-asc");
    $("#type_desc_button").show();
  } else if (icon.classList.contains("fa-sort-asc")) {
    icon.classList.remove("fa-sort-asc");
    icon.classList.add("fa-sort-desc");
    $("#type_desc_button").hide();
  }
}

function remove_table_td(employeeId) {
    if (appendedIDs.hasOwnProperty(employeeId)) {
    delete appendedIDs[employeeId];
    }
    $('#tab'+employeeId).remove();
};

  function handleDropdownChange(id) {
        // var id=$('#taskMgt_id').val();
        var taskOrder = $('#taskSortOrder'+id).val();
        var selectedValue = document.getElementById("myDropdown"+id).value;
        console.log("Selected value: " + selectedValue);
        //    alert(selectedValue);
        $.ajax({
            url: '/staff/tmsortOrder?id='+id+'&newSortOrder='+selectedValue+'&currentOrder='+taskOrder,
            method: 'GET',
            success: function(data) {
                if(data){
                    $('#tickIcon'+id).show();
                    setTimeout(function() {
                        $('#tickIcon'+id).hide();
                    },2000);
                    fetchData();
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
        // Add your logic here based on the selected value
    }
    $('#noTime').change(function() {
        var noTimeChecked = $(this).is(':checked');
        // alert(noTimeChecked);
        if(noTimeChecked == false){
            $('#completion_date').prop('disabled', true);
            $('#completion_time').prop('disabled', true);
        }else{
            $('#completion_date').prop('disabled', false);
            $('#completion_time').prop('disabled', false);
        }
    // $('#completion_date').prop('disabled', !$(this).is(':checked'));
    // $('#completion_time').prop('disabled', !$(this).is(':checked'));
    });

function draftStatus(id){
    //alert(id);
    $.ajax({
        url: '{{ route("updateDraftStatus") }}?id='+id,
        type: 'GET',
        success: function(response) {
            console.log(response.data);
            if(response){

                $('#TaskAssigned').html('Task Assigned Sucessfully');
                setTimeout(function(event) {
                    $('#TaskAssigned').html('');
                    fetchData();
                }, 2000);
            }
            // $('#edit_tm_id').val(response.data);
            // Set a timeout to close the loader after two seconds
            // fetchData($("#task_category_id").val(),'Latest','All');
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}

$('#completion_date').change(function() {
    var selectedDateTime = new Date($(this).val());
    var currentDateTime = new Date();
    selectedDateTime.setHours(0, 0, 0, 0);
    currentDateTime.setHours(0, 0, 0, 0);
    console.log(selectedDateTime +'-------'+ currentDateTime)
    // Compare the selected date and time with the current date and time
    if (selectedDateTime < currentDateTime) {
        // Display a message indicating that the selected date and time is in the past
        $('#dateMsg').html("Reporting Date Has Passed");
        $('#dateMsg').css('color', 'red');
        // setTimeout(function(event) {
        //     $('#dateMsg').html("");
        // }, 5000);
    } else {
        // Clear the message if the selected date and time is not in the past
        $('#dateMsg').html('');
    }
});
$('#staff').select2({
    allowClear: true,
    minimumInputLength: 1, // Show list when at least one character is typed
    dropdownAutoWidth: true, // Adjust dropdown width to fit content
    minimumResultsForSearch: Infinity
});
function rollBack(id){
    //alert('Rollback Successfully Done');
    $.ajax({
        url: '{{ route("rollBack") }}?id='+id,
        type: 'GET',
        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData();

        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
function confirmDelete(id,tm_status){
    // alert($("#task_category_id").val());
    // return false;
    var confirmed = confirm("Are you sure you want to Delete this task?");
    if (confirmed) {
        $.ajax({
            url: '{{ route("deleteTask") }}?id='+id,
            type: 'GET',
            success: function(response) {
                // Set a timeout to close the loader after two seconds
                //fetchData( 'All','Latest','');
                    if (tm_status === 'Draft') {
                    fetchData();
                    } else {
                    fetchData();
                    }

                $("#description").val('');
                // var placeholderValue = "0|<?=auth()->user()->employee_id;?>|3";
                // $("#staff").val(placeholderValue).trigger("change.select2");
                $("#completion_date").val(null);
                $("#task_title").val(null);
                $("#completion_time").val('');
                $("#edit_tm_id").val('');
            },
            error: function(xhr, status, error) {
                // Request failed, handle the error
                console.log("Error: " + error);
            }
        });
    }
}
function editTodo(id){
    //alert("Please");
    //reset_users();
    window.scrollTo(0, 0);
    if ($("#toDoDivForm").is(':visible')) {
        console.log("The div is currently visible.");
    }else {
        $("#toDoDivForm").toggle();
        $("#description").focus();
    }
    $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
    var btn=$('#edit_tm_task_priority'+id).val();
    $("input[name='tasktype'][value='"+btn+"']").prop("checked", true);
    $('#description').val($('#edit_tm_task_title'+id).val());
    $('#edit_tm_id').val(id);
    //$('#staff').val($('#edit_task_client'+id).val()+'|'+$('#edit_task_client'+id).val()+'|edit').trigger('change');

    fetchRecordsFromController($('#edit_task_client'+id).val()+'|'+$('#edit_task_client'+id).val()+'|edit');

    if($('#edit_group_task'+id).val() =="1"){
        $("#group_task").prop("checked", true);
    }else{
        $("#group_task").prop("checked", false);
    }

    $('#task_title').val($('#edit_tm_description'+id).val());
    // alert($('#tasknoTime'+id).val());

    if($('#edit_tm_completion_date'+id).val()===""){
        $('#completion_date').prop('disabled', true);
        $("#noTime").prop("checked", true);
    }

    // if ($('#tasknoTime'+id).val() == 'on') {
    //     $("#noTime").prop("checked", true);
    //     //$('#completion_date').prop('disabled', true);
    //     $('#completion_time').prop('disabled', true);
    // } else {
    //     $("#noTime").prop("checked", false);
    //    // $('#completion_date').prop('disabled', false);
    //     $('#completion_time').prop('disabled', false);
    // }
    // $('#noTime').val($('#tasknoTime'+id).val());

    $('#completion_date').val($('#edit_tm_completion_date'+id).val());
    $('#completion_time').val($('#edit_tm_completion_time'+id).val());
    $('#tmc_categ').val($('#edit_tm_project_category'+id).val()).trigger('change');

    //var staffDropdown = document.getElementById("staff");
    $("#staff_div_row").hide();
    $("#clear_button").hide();
    $("#show_edit_div").show();



    $(".inner_div_when_edit").hide();
    $(".user_outer_div_strong").hide();
    $(".user_inner_div").removeAttr("onclick");

}

function submitTask(id) {
    var submitButton = document.querySelector(".btnProp"+id);
    // Disable the submit button
    submitButton.disabled = true;
    // Display the "Data is processing" message
    submitButton.innerHTML = '<i class="fe fe-loader mr-2"></i>Data is processing...';
    // Simulate a delay of 2 seconds (2000 milliseconds)
    setTimeout(function() {
        // Enable the submit button
        submitButton.disabled = false;
        // Display the "Data saved successfully" message
        submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Data saved successfully';
        // Reset the message after 2 seconds
        setTimeout(function(event) {
            //event.preventDefault();
            if(id == 'Draft'){
                submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Save As Draft';
            }else{
                submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Submit Task';
            }

        // document.getElementById('taskForm').submit();
        }, 2000);
    }, 2000);
}
// Add event listener for Enter key press
document.getElementById("description").addEventListener("keydown", function(event) {
    if (event.keyCode === 13) { // Check if Enter key was pressed
        event.preventDefault(); // Prevent the default Enter key behavior (new line)
        addValue(); // Call the addValue() function
    }
});
function hide_form(){
    const iconToggle = document.getElementById("iconToggleToDoForm");
    if (iconToggle.classList.contains("fa-plus")) {
        //iconToggle.classList.remove("fa-plus");
        //iconToggle.classList.add("fa-minus");
    } else if (iconToggle.classList.contains("fa-minus")) {
        iconToggle.classList.remove("fa-minus");
        iconToggle.classList.add("fa-plus");
        $("#toDoDivForm").hide();
    }
    reset_users();
}
// AJAX function to submit the form
$(document).ready(function() {
    //$('#ul_show_hide a[data-toggle="tab"]').on('click', fetchData);



    $("#noTime").prop("checked", true);
    $('#completion_date').prop('disabled', false);
    $('#completion_time').prop('disabled', false);
    $("#toggleToDoForm").click(function() {
        // $('#tmc_categ option:first').prop('selected', true);
        $("#description").val('');
        var placeholderValue = "0|<?=auth()->user()->employee_id;?>|3";
        $("#staff").val(placeholderValue).trigger("change.select2");
        $("#completion_date").val(null);
        $("#task_title").val(null);
        $("#completion_time").val('');
        // var placeholderValueCateg = "";
        // $("#tmc_categ").val(placeholderValueCateg).trigger("change.select2");
        //   alert('123');
        // $('#taskForm').submit();
        //alert($("#toDoDivForm").is(':visible'));
        //  return false;
        $("#toDoDivForm").toggle();
        // alert($(this).is(':visible'));
        // $("#ul_show_hide").hide();
        $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
        //  $("#tab_innerdivabc").hide();
        // if ($("#toDoDivForm").is(':visible')) {
        //     //$("#staff").focus();
        //     var select2Element = $("#staff").next(".select2-container").find(".select2-selection");
        //     select2Element.trigger("focus");
        //     $.ajax({
        //         url: '{{ route("addDraft") }}',
        //         type: 'GET',
        //         success: function(response) {
        //             console.log(response.data);
        //             $('#edit_tm_id').val(response.data);
        //             // Set a timeout to close the loader after two seconds
        //             //fetchData($("#task_category_id").val(),'Latest','All');
        //         },
        //         error: function(xhr, status, error) {
        //             // Request failed, handle the error
        //             console.log("Error: " + error);
        //         }
        //     });
        // }
    });

    // alert($("#task_category_id").val());
    fetchData();

    $("#longTermCheckbox").change(function() {
        var badgeText = $("#badgeText");

        if ($(this).is(":checked")) {
            badgeText.html("<strong>Long term</strong>");
            $('#chkboxTxt').html('');
            $('#chkboxTxt').html('<strong>Mark as Long term</strong>');
        } else {
            badgeText.html("<strong>Urgent</strong>");
            $('#chkboxTxt').html('');
            $('#chkboxTxt').html("<strong>Mark as Urgent</strong>");
        }
    });

    $('.select2').select2();
    $('#user_and_me_div').hide();
    $('#date_time_desc_group').show();
    $('#task_type').change(function() {
        $('#userDataBody').empty();
        $('#type_desc_button').show();
        $('#report_date_time').show();
        // $('#show_staff_pannel').show();
        // $('#user_and_me_div').show();
        // $('#only_button').show();
        checkbox.checked = false;
        $('#show_staff_pannel').show();
        $('#user_and_me_div').show();
    });



});
function checkInputsExist(inputName) {
  var inputs = document.querySelectorAll('input[name="' + inputName + '"]');
  return inputs.length > 0;
}


function insert(param){
        $('#btn_status').val(param);
        var isChecked = $("#noTime").prop("checked");
        event.preventDefault();
        var form = document.querySelector('#taskForm');
        // var form = $('#taskForm').val();
        //var url = form.attr('action');
        var url = '{{url('dboard_submit') }}';
        //var method = form.attr('method');
        var method = 'post';
        var formData = new FormData(form);

        // Assuming record object is provided
    var inputName = 'user[]';
    var hasInputs = checkInputsExist(inputName);

            if ($('input[name="tmc_name[]"]:checked').length === 0) {
                alert('Please select at least one Category.');
                return false;
                event.preventDefault(); // Prevent the form from being submitted
            }
          //  alert("Please");


        if (!hasInputs) {
            event.preventDefault();
            $('#errorMsg').html('');
            $('#errorMsg').html('Please Select user.');
            $('#errorMsg').css('color','red');
            setTimeout(function() {
                $('#errorMsg').html('');
            },4000);
            return false;
        }

        if ($('#description').val()=='') {
            event.preventDefault();
            $('#errorMsg').html('');
            $('#errorMsg').html('Please enter task field.');
            $('#errorMsg').css('color','red');
            setTimeout(function() {
                $('#errorMsg').html('');
            },4000);
            return false;
        }
        if(isChecked){
            if ($('#completion_date').val()=='') {
                event.preventDefault();
                $('#errorMsg').html('');
                $('#errorMsg').html('Please enter reporting date field.');
                $('#errorMsg').css('color','red');
                setTimeout(function() {
                    $('#errorMsg').html('');
                },4000);
                // alert('Please enter reporting date field.');
                return false;
            }
        }
        // Send the AJAX request
        $.ajax({
            url: url,
            type: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                //alert(response.message)
                $('#DraftMsg').html(response.message);
                setTimeout(function() {
                    $('#DraftMsg').html('');
                }, 3000);
                submitTask(param);
                $('#edit_row_id').val($("#edit_tm_id").val());
                $("#edit_tm_id").val('');
                $("#task_title").val('');
                $('#completion_date').val('');
                $("#completion_time").val('');

                if ($("#toDoDivForm").is(':visible')) {
                //$("#toDoDivForm").toggle();
                //$("#iconToggleToDoForm").toggleClass("fa-minus fa-plus");
                }
                setTimeout(function() {
                    if (response === "NOUSER") {
                        alert("Please select at least one user.");
                    } else if (response === "NOTASK") {
                        alert("Please enter at least one task.");
                    } else {
                        $("#description").val('');
                        fetchData();
                        var valueList = document.getElementById('valueList');
                        valueList.innerHTML = '';

                        var checkboxes = document.querySelectorAll('input[name="user_id[]"]');
                        checkboxes.forEach(function(checkbox) {
                            //checkbox.checked = false;
                        });
                    }
                    // alert("Task Saved Successfully");
                    //$("#taskForm")[0].reset();
                }, 2000);
                $("#group_task").prop("checked", false);
                $('.recordsDiv').html('');
                appendedIDs = {};
                resetDropdown();
            },
            error: function(xhr, status, error) {
                // Display error message or perform any other actions
                alert('An error occurred while saving the task.');
            }
        });
        // });
        $("#staff_div_row").show();
        $("#clear_button").show();
        $("#show_edit_div").hide();
    }
    function resetDropdown() {
       $('#staff').val('0|0|3').trigger('change');
    }

//     function handleCheckboxChange(checkbox) {
//         var checked = $('input[name="selection[]"]:checked').map(function() {
//             return $(this).val();
//         }).get();
//         fetchData(checked);
//     }
// $('#tmc_categ_filter').on('change', function() {
//     fetchData($('#sortDropdown').val());
// });
// $('#sortDropdown').on('change', function() {
//     var selectedValue = $(this).val();
//     // Perform actions based on the selected value
//    // alert('Selected value: ' + selectedValue);
//     fetchData(selectedValue);
// });

function getCheckboxValues() {
            // Initialize an array to hold the selected values
            var selectedValues = [];

            // Get the values of both checkboxes
            var urgentCheckbox = $("input[name='selection[]'][value='Urgent']");
            var longTermCheckbox = $("input[name='selection[]'][value='LongTerm']");

            // Check if the checkboxes are checked and push their values to the array
            if (urgentCheckbox.is(":checked")) {
                selectedValues.push(urgentCheckbox.val());
            }
            if (longTermCheckbox.is(":checked")) {
                selectedValues.push(longTermCheckbox.val());
            }

            // Return the array of selected values
            return selectedValues;
        }

// -------------------
var currentCategory = 'All';
function setCategory(categoryid) {
       currentCategory = categoryid;
}
// Attach event listeners to the checkboxes
const checkboxes = document.querySelectorAll('.filter-checkbox');
checkboxes.forEach(checkbox => {
  checkbox.addEventListener('change', fetchData);
});
function fetchData() {

    // Get all the checkboxes with the class "filter-checkbox"
    const checkboxes = document.querySelectorAll('.filter-checkbox');
    // Filter the checked checkboxes and get their values
    const checkedValues = Array.from(checkboxes)
    .filter(checkbox => checkbox.checked)
    .map(checkbox => checkbox.value);
    // You can perform further actions with the checked values here
    //console.log('Checked values:', checkedValues);

    var SortID      = $('#sortDropdown').val();
    var chk         = getCheckboxValues();
    var a_href = $("#aid"+currentCategory).attr("value");
    $("#task_category_id1").val(currentCategory);
    const tabContent = $('#tab_innerdivabc');
    tabContent.empty(); // Empty the div before appending new data
    $("#tab_innerdivabc").show();
    //   $("#toDoDivForm").hide();
    $.ajax({
        url: '/staff/showTaskList?id=' + currentCategory+
        '&type=Task&sortId='+SortID+
        '&chkbox='+chk+
        '&task_type='+$("#tmc_categ_filter").val()+
        '&task_filter_categories='+checkedValues+
        '&user_id='+$("#staff_for_filter").val(),
        method: 'GET',
        success: function(data) {
        tabContent.html(data); // Append the fetched data to the tab content element
        // initializeDataTable(); // Re-initialize DataTable after content is appended
            $("#tab_innerdivabc").html(data);
            $('#row' + $("#edit_row_id").val()).css('background-color', '#ded6f1');
            // $('html, body').animate({
            //     scrollTop: $('#row' + $("#edit_row_id").val()).offset().top
            // }, 1000); // Adjust the duration as needed
            var targetElement = $('#row' + $("#edit_row_id").val());
            var targetCenter = targetElement.offset().top + targetElement.outerHeight() / 2;
            var windowHeight = $(window).height();

            $('html, body').animate({
                scrollTop: targetCenter - windowHeight / 2
            }, 500);
        },
        error: function(error) {
        console.error('Error:', error);
        }
    });
}
function confirmMarkCompleted(taskId) {
    // Ask for confirmation using the built-in confirm() function
    var confirmed = confirm("Are you sure you want to complete this task?");

    if (confirmed) {
        // User confirmed, proceed with marking the task as completed
        markCompleted(taskId);
    }
}
function markCompletedAll(){
    //console.log((".subtask-checkbox:checked").length);
    if ($(".subtask-checkbox:checked").length > 0) {
            // At least one checkbox is checked, perform the "Mark Completed" action here
            //alert("Marking completed...");
            // You can replace the alert with your actual "Mark Completed" logic
    } else {
        // No checkboxes are checked, show an error message or take appropriate action
        alert("Please check at least one Task before Mark Completed All.");
        return false;
    }
    var confirmed = confirm("Are you sure you want to complete this all task?");

    if (confirmed) {
        //alert('ok')
        // User confirmed, proceed with marking the task as completed
       // markCompleted(taskId);
       var checkboxValues = [];
        // Select all checkboxes with the name attribute "markCompletedAll[]"
        $('input[name^="markCompletedAll["]').each(function() {
            // Check if the checkbox is checked
            if ($(this).is(":checked")) {
                // Push the value of the checked checkbox to the array
                checkboxValues.push($(this).val());
            }
        });
        var data = {
            checkboxValues:checkboxValues,
            _token: '{{ csrf_token() }}'
        };
        // Make the AJAX request
        $.ajax({
            url: '{{ route("updateTaskAll") }}',
            type: 'POST',
            data: data,
            success: function(response) {
                // Set a timeout to close the loader after two seconds
                fetchData();
                // showLoader(taskId);
                // setTimeout(function() {
                //     // Hide the loader
                //     hideLoader(taskId);

                //     // Handle the response if needed
                //     console.log(response);
                //     hide_row(taskId);
                // }, 2000);
            },
            error: function(xhr, status, error) {
                // Request failed, handle the error
                console.log("Error: " + error);
            }
        });
    }
}
function markCompleted(taskId) {
    // Show the loader
    // Set the data to send in the request body
    var data = {
        taskId: taskId, 
        _token: '{{ csrf_token() }}'
    };
    // Make the AJAX request
    $.ajax({
        url: '{{ route("updateTask") }}',
        type: 'POST',
        data: data,
        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData();
            showLoader(taskId);
            setTimeout(function() {
                // Hide the loader
                hideLoader(taskId);

                // Handle the response if needed
                console.log(response);
                hide_row(taskId);
            }, 2000);
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}

function pdf(){
    const checkboxes = document.querySelectorAll('.filter-checkbox');
    // Filter the checked checkboxes and get their values
    const checkedValues = Array.from(checkboxes)
    .filter(checkbox => checkbox.checked)
    .map(checkbox => checkbox.value);

    id=currentCategory;
    sortID = $("#sortDropdown").val();
    categoryId = $("#task_category_id").val();
    var checked = $('input[name="selection[]"]:checked').map(function() {
        if($(this).val()=="LongTerm"){

            return "Longterm";
        }
        return $(this).val();
    }).get();
    type='Task';
    task_type="undefined";
    emp_id = $("#employee_id").val();
    var user_id = $("#staff_for_filter").val()
    url = '/assets/cms/reports/task_pdf.php?id='+currentCategory+'&type='+type+'&sortId='+sortID+'&chkbox='+checked+'&eid='+emp_id + '&user_id=' + user_id + '&task_filter_categories='+checkedValues,
    window.open(url, '_blank');
}
</script>
@stop
