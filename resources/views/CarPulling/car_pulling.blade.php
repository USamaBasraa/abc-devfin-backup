@extends('layouts.app', ['pageTitle' => 'car pooling'])
@section('content')

{{-- @dd("1234"); --}}
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

<style>
.invisible { display: none; }
.dropbtn {
  background-color: #04AA6D;
  color: white;
  padding: 10px;
  font-size: 16px;
  border: none;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba((142, 152, 219, 1.0));
  z-index: 1;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {background-color: #ddd;}

.dropdown:hover .dropdown-content {display: block;}

.dropdown:hover .dropbtn {background-color: #3e8e41;}
ul {
    margin: 20px;
}
.input-color {
    position: relative;
}
.input-color input {
    padding-left: 25px;
      border:0px solid;
}
.input-color .color-box {
    width: 15px;
    height: 15px;
    display: inline-block;
    background-color: #ccc;
    position: absolute;
    left: 5px;
    top: 5px;
}
.challanDIV {
    height: 350px;
    display: inline-block;
    width: 100%;
    overflow: auto;
}
.weekDays-selector input {
  display: none!important;
}
.weekDays-selector input[type=checkbox] + label {
  display: inline-block;
  border-radius: 6px;
  background: #dddddd;
  height: 35px;
  width: 80px;
  margin-right: 3px;
  line-height: 40px;
  text-align: center;
  cursor: pointer;
}
.weekDays-selector input[type=checkbox]:checked + label {
  background: blue;
  color: #ffffff;
}
#back-to-top {
    background: #4454c3;
    color: #fff;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99;
    display: none;
    text-align: center;
    border-radius: 3px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    -o-border-radius: 2px;
    z-index: 10000;
    height: 50px;
    width: 50px;
    line-height: 50px;
    background-repeat: no-repeat;
    background-position: center;
    transition: background-color 0.1s linear;
    -moz-transition: background-color 0.1s linear;
    -webkit-transition: background-color 0.1s linear;
    -o-transition: background-color 0.1s linear;
}
.app-sidebar__user {
    text-align: center;
}
.side-menu.is-expanded {
    margin:0px !important;
}
.side-menu {
    margin: 0px !important;
}
.slide-item {
    padding-left:14px !important;
}
.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}










































/* =========================
   Car Pooling Card Styling
   ========================= */

/* Target only Car Pooling card */
.mainarea-containrer > .card:nth-of-type(2) {
    background: #fff;
    border: none;
    border-radius: 10px !important;
    box-shadow: none;
}

/* Card body spacing */
.mainarea-containrer > .card:nth-of-type(2) > .card-body {
    padding: 18px 18px 14px;
}

/* Reduce row spacing */
.mainarea-containrer > .card:nth-of-type(2) .form-group {
    margin-bottom: 12px;
}

/* Labels */
.mainarea-containrer > .card:nth-of-type(2) label {
    font-size: 12px;
    font-weight: 400;
    color: #3f3f3f;
    margin-bottom: 4px;
    padding-top: 0;
    padding-bottom: 0;
}

/* Remove bold heavy appearance */
.mainarea-containrer > .card:nth-of-type(2) label b {
    font-weight: 400;
}

/* Inputs + Selects */
.mainarea-containrer > .card:nth-of-type(2) .form-control {
    height: 38px;
    border: 1px solid #dedede;
    border-radius: 4px;
    background-color: #fff;
    font-size: 12px;
    color: #555;
    box-shadow: none;
    padding: 8px 12px;
}

/* Placeholder */
.mainarea-containrer > .card:nth-of-type(2) .form-control::placeholder {
    color: #b7b7b7;
    font-size: 11px;
}

/* Select appearance */
.mainarea-containrer > .card:nth-of-type(2) select.form-control {
    color: #999;
}

/* Preferred Location helper text */
.mainarea-containrer > .card:nth-of-type(2) .form-label span {
    color: #aaa !important;
    font-size: 10px !important;
    font-weight: 400;
}

/* Preferred Location spacing */
.mainarea-containrer > .card:nth-of-type(2) #search_data {
    margin-top: 2px;
}

/* Separator before Optional */
.mainarea-containrer > .card:nth-of-type(2) form > hr {
    border: 0;
    border-top: 1px solid #e5e5e5;
    margin: 20px 0;
}


/* =========================
   OPTIONAL BAR
   ========================= */

/* Your current inline Optional wrapper */
.mainarea-containrer > .card:nth-of-type(2) form > div[style*="background-color:#4959c3"] {
    background-color: #e8eeee !important;
    color: #333 !important;

    height: 36px !important;
    padding: 0 10px !important;

    border-radius: 5px !important;

    display: flex !important;
    align-items: center !important;

    margin-bottom: 12px;
}

/* Optional title */
.mainarea-containrer > .card:nth-of-type(2)
form > div[style*="background-color:#4959c3"] .card-title {
    color: #333 !important;
    font-size: 14px;
    font-weight: 600;
    margin-left: 0 !important;
}


/* =========================
   Route Map link
   ========================= */

.mainarea-containrer > .card:nth-of-type(2) #routePlan a {
    color: #ef7f72 !important;
    font-size: 11px;
    text-decoration: underline;
}


/* =========================
   Submit / Update Buttons
   ========================= */

.mainarea-containrer > .card:nth-of-type(2) #submitBtn,
.mainarea-containrer > .card:nth-of-type(2) #updtBtn {
    text-align: left !important;
    margin-top: 5px;
    margin-bottom: 0 !important;
}

/* Primary buttons */
.mainarea-containrer > .card:nth-of-type(2) .btn-primary {
    background: #167968;
    border-color: #167968;
    border-radius: 4px;

    font-size: 11px;
    font-weight: 400;

    padding: 8px 16px;
}

/* Hover */
.mainarea-containrer > .card:nth-of-type(2) .btn-primary:hover {
    background: #126858;
    border-color: #126858;
}


/* =========================
   Desktop layout adjustments
   ========================= */

@media (min-width: 992px) {

    /* First row: Ride Status + Contact Option */
    .mainarea-containrer > .card:nth-of-type(2)
    #car_pulling > .row:first-of-type > .form-group {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }

    /* Vehicle row: 3 columns */
    .mainarea-containrer > .card:nth-of-type(2)
    #car_pulling > .row:nth-of-type(2) > .form-group {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
}


/* =========================
   Mobile
   ========================= */

@media (max-width: 767px) {

    .mainarea-containrer > .card:nth-of-type(2) > .card-body {
        padding: 15px;
    }

    .mainarea-containrer > .card:nth-of-type(2) .form-control {
        height: 38px;
    }
}


/* =========================
   Ride Details Styling
   ========================= */

/* Title row above card */
#cardLoop > div[style*="display:flex"] {
    background: #17675F !important;
    color: #fff !important;
    border-radius: 4px 4px 0 0;
    padding: 10px 14px;
    margin-top: 12px !important;
    margin-right: 20px !important;
    width: 100% !important;
}

/* Ride Details title */
#cardLoop > div[style*="display:flex"] .profile-overview-title {
    color: #fff !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    margin: 0 !important;
}

/* Date/time */
#cardLoop > div[style*="display:flex"] .profile-overview-title span {
    font-size: 11px !important;
    font-weight: 400 !important;
    color: #fff !important;
}

/* Edit button */
#cardLoop > div[style*="display:flex"] button {
    background: transparent;
    border: none;
    color: #fff;
    padding: 0;
    font-size: 12px;
}

/* Card directly after Ride Details header */
#cardLoop > div[style*="display:flex"] + .card {
    margin-top: 0 !important;
    border: none !important;
    border-radius: 0 0 4px 4px !important;
    box-shadow: none !important;
    background: #fff;
}

/* Card body compact spacing */
#cardLoop > div[style*="display:flex"] + .card .card-body {
    padding: 14px 14px 10px !important;
}

/* Compact row gaps */
#cardLoop > div[style*="display:flex"] + .card .row {
    margin-bottom: 0;
}

/* Each item */
#cardLoop > div[style*="display:flex"] + .card .form-group {
    margin-bottom: 14px !important;
}

/* Labels */
#cardLoop > div[style*="display:flex"] + .card .col-form-label {
    display: block;
    padding: 0 !important;
    margin: 0 0 2px !important;
    font-size: 10px !important;
    line-height: 1.1;
    font-weight: 400 !important;
    color: #A1A1A1 !important;
}

/* Remove bold look */
#cardLoop > div[style*="display:flex"] + .card .col-form-label b {
    font-weight: 400 !important;
}

/* Values */
#cardLoop > div[style*="display:flex"] + .card .form-group {
    font-size: 11px;
    color: #222;
    line-height: 1.25;
}

/* Week day colors */
#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(1) .col-form-label {
    color: #b52b27 !important;
}

#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(2) .col-form-label {
    color: #8cc221 !important;
}

#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(3) .col-form-label {
    color: #f05a3f !important;
}

#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(4) .col-form-label {
    color: #008c87 !important;
}

#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(5) .col-form-label {
    color: #a65328 !important;
}

#cardLoop > div[style*="display:flex"] + .card
.row:nth-of-type(3) .form-group:nth-child(6) .col-form-label {
    color: #2b8b4b !important;
}


/* =========================
   Make layout like 2nd SS
   5 items per row on desktop
   ========================= */

@media (min-width: 992px) {

    #cardLoop > div[style*="display:flex"] + .card .row > .form-group {
        flex: 0 0 20% !important;
        max-width: 20% !important;
    }
}


/* Mobile */
@media (max-width: 767px) {

    #cardLoop > div[style*="display:flex"] {
        width: 100% !important;
        margin-right: 0 !important;
    }

    #cardLoop > div[style*="display:flex"] + .card {
        width: 100% !important;
        margin-right: 0 !important;
    }
}








/* =========================================================
   FIGMA - CAR POOLING REGISTRATION
   Frame: 453:360
   STYLE ONLY - NO LOGIC / HTML CHANGE
========================================================= */

/* ---------- PAGE BACKGROUND ---------- */

body .app-content.main-content {
    background: linear-gradient(
        to left,
        rgba(23, 103, 95, 0.15) 0%,
        rgba(255, 255, 255, 0.15) 100%
    ) !important;
}

.app-content.main-content .side-app {
    background: transparent !important;
}


/* ---------- PAGE TYPOGRAPHY ---------- */

.mainarea-containrer,
#cardLoop {
    font-family: "Montserrat", sans-serif !important;
    color: #252525;
}

.mainarea-containrer {
    width: 100%;
    margin-top: 45px;
}


/* ---------- SECTION TITLES ---------- */

.mainarea-containrer > div[style*="float:left"] {
    float: none !important;
    clear: both;
    width: 100%;
    margin: 0 0 10px 0 !important;
}

.mainarea-containrer .profile-overview-title {
    margin: 0 !important;
    padding: 0 !important;

    font-family: "Montserrat", sans-serif !important;
    font-size: 18px !important;
    font-weight: 600 !important;
    line-height: 22px !important;

    color: #252525 !important;
}


/* =========================================================
   RIDER PROFILE
========================================================= */

.mainarea-containrer > .card:nth-child(2) {
    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;
    box-shadow: none !important;

    margin-top: 0 !important;
}

.mainarea-containrer > .card:nth-child(2) > .card-body {
    padding: 20px !important;
}


/* Desktop profile layout */

@media (min-width: 992px) {

    .mainarea-containrer > .card:nth-child(2) {
        height: 193px !important;
        min-height: 193px !important;
    }

    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row {

        display: grid !important;

        grid-template-columns:
            222px
            repeat(3, minmax(0, 1fr));

        column-gap: 20px;

        align-items: center !important;

        height: 100%;

        margin: 0 !important;
    }


    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group {

        width: auto !important;
        max-width: none !important;
        flex: none !important;

        position: relative;

        margin: 0 !important;
        padding: 0 15px !important;
    }


    /* Image column */

    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group:first-child {

        padding: 0 !important;
    }


    /* Vertical separators */

    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group:nth-child(2)::after,

    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group:nth-child(3)::after {

        content: "";

        position: absolute;

        right: 0;
        top: 50%;

        width: 1px;
        height: 20px;

        background: #E5E7EB;

        transform: translateY(-50%);
    }
}


/* Profile image inner row */

.mainarea-containrer > .card:nth-child(2)
.form-group:first-child .row {

    margin: 0 !important;
}

.mainarea-containrer > .card:nth-child(2)
.form-group:first-child .row .form-group {

    margin: 0 !important;
    padding: 0 !important;

    text-align: left !important;
}


/* Profile image */

.mainarea-containrer > .card:nth-child(2) img {

    width: 222px !important;
    height: 153px !important;

    max-width: 100% !important;

    object-fit: cover !important;

    border-radius: 10px !important;
}


/* Profile labels */

.mainarea-containrer > .card:nth-child(2)
> .card-body > .row > .form-group > label {

    display: block !important;

    margin: 0 0 3px 0 !important;
    padding: 0 !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    line-height: 18px !important;

    color: #949494 !important;
}

.mainarea-containrer > .card:nth-child(2)
> .card-body > .row > .form-group > label b {

    font-weight: 400 !important;
}


/* Profile values */

.mainarea-containrer > .card:nth-child(2)
> .card-body > .row > .form-group > span {

    display: block;

    font-family: "Montserrat", sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    line-height: 20px !important;

    color: #252525 !important;
}


/* Space before Car Pooling heading */

.mainarea-containrer
> .card:nth-child(2)
+ div[style*="float:left"] {

    margin-top: 20px !important;
    margin-bottom: 10px !important;
}


/* =========================================================
   CAR POOLING FORM CARD
========================================================= */

.mainarea-containrer > .card:has(#car_pulling) {

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    margin-top: 0 !important;
}

.mainarea-containrer
> .card:has(#car_pulling)
> .card-body {

    padding: 25px 20px 21px !important;
}


/* ---------- FORM LABELS ---------- */

#car_pulling label.col-form-label,
#car_pulling label.form-label {

    display: block;

    padding: 0 !important;
    margin: 0 0 7px 0 !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    line-height: 18px !important;

    color: #252525 !important;
}

#car_pulling label b {
    font-weight: 400 !important;
}


/* Preferred location helper */

#car_pulling .form-label span {

    font-size: 12px !important;
    font-weight: 400 !important;

    color: #A1A1A1 !important;
}


/* ---------- INPUTS / SELECTS ---------- */

#car_pulling .form-control {

    width: 100%;

    height: 40px !important;

    padding: 8px 14px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
    outline: none !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

#car_pulling .form-control::placeholder {

    color: #A1A1A1 !important;
    opacity: 1 !important;

    font-size: 14px !important;
}

#car_pulling select.form-control {

    color: #A1A1A1 !important;

    cursor: pointer;
}

#car_pulling .form-control:focus {

    border-color: #17675F !important;

    box-shadow:
        0 0 0 2px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   DESKTOP FORM GRID
   Matches Figma 3-column layout
========================================================= */

@media (min-width: 992px) {

    #car_pulling {

        display: grid !important;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        column-gap: 20px;
        row-gap: 25px;
    }


    #car_pulling > input[type="hidden"] {
        display: none;
    }


    /*
       Flatten required Bootstrap rows so fields from
       different rows can align exactly like Figma.
    */

    #car_pulling > .row:has(#want_ride),
    #car_pulling > .row:has(#vehical_type),
    #car_pulling > .row:has(#routePlan),
    #car_pulling > .row:has(#share_info) {

        display: contents !important;
    }


    #car_pulling .form-group {

        min-width: 0;

        margin: 0 !important;
    }


    #car_pulling > .row:has(#want_ride) > .form-group,
    #car_pulling > .row:has(#vehical_type) > .form-group,
    #car_pulling > .row:has(#routePlan) > .form-group,
    #car_pulling > .row:has(#share_info) > .form-group {

        width: auto !important;

        flex: none !important;
        max-width: none !important;

        padding: 0 !important;
    }


    /* ----- Row 1 ----- */

    #car_pulling .form-group:has(#want_ride) {

        order: 10;
        grid-column: 1;
    }

    #car_pulling .form-group:has(#contact_me) {

        order: 11;
        grid-column: 2;
    }

    #car_pulling .form-group:has(#vehical_type) {

        order: 12;
        grid-column: 3;
    }


    /* Contact detail - when visible */

    #car_pulling #contDetail {

        order: 15;

        grid-column: 1 / -1;
    }


    /* ----- Row 2 ----- */

    #car_pulling .form-group:has(#vehicle_no) {

        order: 20;
        grid-column: 1;
    }

    #car_pulling .form-group:has(#vehicle_capacity) {

        order: 21;
        grid-column: 2;
    }

    #car_pulling .form-group:has(#share_with) {

        order: 22;
        grid-column: 3;
    }


    /* ----- Preferred Location ----- */

    #car_pulling > .row:has(#search_data) {

        order: 30;

        grid-column: 1 / -1;

        width: 100%;

        margin: 0 !important;
    }

    #car_pulling > .row:has(#search_data)
    > .form-group {

        width: 100% !important;

        flex: 0 0 100% !important;
        max-width: 100% !important;

        padding: 0 !important;
        margin: 0 !important;
    }


    /* ----- Optional ----- */

    #car_pulling
    > div[style*="background-color:#17675F1A"] {

        order: 40;

        grid-column: 1 / -1;
    }


    /* ----- Optional row ----- */

    #car_pulling .form-group:has(#route_link) {

        order: 50;
        grid-column: 1;
    }

    #car_pulling .form-group:has(#pay_for_one_side) {

        order: 51;
        grid-column: 2;
    }

    #car_pulling .form-group:has(#share_info) {

        order: 52;
        grid-column: 3;
    }


    /* Fields which appear dynamically */

    #car_pulling .form-group:has(#vehicle) {

        order: 54;
        grid-column: 1;
    }

    #car_pulling .form-group:has(#vehicle_model) {

        order: 55;
        grid-column: 2;
    }

    #car_pulling #time_div,
    #car_pulling #specific_day {

        order: 56;

        grid-column: 1 / -1;
    }

    #car_pulling > #showDays {

        order: 57;

        grid-column: 1 / -1;

        margin: 0 !important;
    }


    /* ----- Smoking ----- */

    #car_pulling .form-group:has(#smoking_info) {

        order: 60;

        grid-column: 1 / -1;
    }


    /* Empty row */

    #car_pulling > .row:empty {
        display: none !important;
    }


    /* ----- Buttons ----- */

    #car_pulling > .row:has(#submitBtn) {

        order: 70;

        grid-column: 1 / -1;

        width: 100%;

        margin: 0 !important;
    }

    #car_pulling > .row:has(#submitBtn) > div {

        padding: 0 !important;
    }
}


/* =========================================================
   OPTIONAL BAR
========================================================= */

#car_pulling
> div[style*="background-color:#17675F1A"] {

    position: relative !important;

    width: 100% !important;
    height: 40px !important;

    padding: 0 15px !important;
    margin-top: 25px !important;

    display: flex !important;
    align-items: center !important;

    border-radius: 5px !important;

    box-sizing: border-box;
}


/* Divider above Optional */

#car_pulling
> div[style*="background-color:#17675F1A"]::before {

    content: "";

    position: absolute;

    left: 0;
    right: 0;
    top: -26px;

    height: 1px;

    background: #E5E7EB;
}


/* Optional up arrow */

#car_pulling
> div[style*="background-color:#17675F1A"]::after {

    content: "";

    position: absolute;

    right: 15px;
    top: 50%;

    width: 7px;
    height: 7px;

    border-top: 1.5px solid #252525;
    border-right: 1.5px solid #252525;

    transform:
        translateY(-20%)
        rotate(-45deg);
}


/* Optional heading */

#car_pulling
> div[style*="background-color:#17675F1A"]
.card-title {

    margin: 0 !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    line-height: 22px !important;

    color: #252525 !important;
}


/* =========================================================
   ROUTE MAP LINK
========================================================= */

#car_pulling #routePlan a {

    color: #EE8066 !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    text-decoration: underline !important;
}


/* =========================================================
   SUBMIT / UPDATE
========================================================= */

#car_pulling #submitBtn,
#car_pulling #updtBtn {

    text-align: left !important;

    margin: 0 !important;
}

#car_pulling #submitBtn .btn-primary,
#car_pulling #updtBtn .btn-primary {

    width: 93px !important;
    height: 40px !important;

    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #FFFFFF !important;
}

#car_pulling #submitBtn .btn-primary:hover,
#car_pulling #updtBtn .btn-primary:hover {

    background: #145C55 !important;
    border-color: #145C55 !important;
}


/* =========================================================
   RIDE DETAILS
========================================================= */

#cardLoop {

    width: 100%;

    margin-top: 35px !important;
}


/* Green header */

#cardLoop > div[style*="display:flex"] {

    height: 54px !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;

    background: #17675F !important;

    border: 0 !important;

    border-radius: 5px 5px 0 0 !important;

    box-shadow: none !important;

    box-sizing: border-box;
}


/* Ride Details heading */

#cardLoop
> div[style*="display:flex"]
.profile-overview-title {

    margin: 0 !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;

    line-height: 22px !important;

    color: #FFFFFF !important;
}


/* Date/time inside title */

#cardLoop
> div[style*="display:flex"]
.profile-overview-title span {

    font-size: 14px !important;
    font-weight: 400 !important;

    color: rgba(255, 255, 255, 0.8) !important;
}


/* Edit icon */

#cardLoop > div[style*="display:flex"] button {

    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;

    color: #FFFFFF !important;
}


/* Details white card */

#cardLoop
> div[style*="display:flex"]
+ .card {

    margin-top: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-radius: 0 0 5px 5px !important;

    box-shadow: none !important;
}

#cardLoop
> div[style*="display:flex"]
+ .card .card-body {

    padding: 20px !important;
}


/* Five columns as Figma */

@media (min-width: 992px) {

    #cardLoop
    > div[style*="display:flex"]
    + .card .row {

        display: grid !important;

        grid-template-columns:
            repeat(5, minmax(0, 1fr));

        column-gap: 28px;

        margin: 0 !important;
    }


    #cardLoop
    > div[style*="display:flex"]
    + .card .row > .form-group {

        width: auto !important;

        max-width: none !important;
        flex: none !important;

        padding: 0 !important;

        margin: 0 0 22px 0 !important;
    }
}


/* Ride detail labels */

#cardLoop
> div[style*="display:flex"]
+ .card
.col-form-label {

    display: block !important;

    padding: 0 !important;
    margin: 0 0 4px 0 !important;

    font-family: "Montserrat", sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    line-height: 18px !important;

    color: #949494 !important;
}

#cardLoop
> div[style*="display:flex"]
+ .card
.col-form-label b {

    font-weight: 400 !important;

    color: #949494 !important;
}


/* Ride detail values */

#cardLoop
> div[style*="display:flex"]
+ .card .form-group {

    font-family: "Montserrat", sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;

    line-height: 20px !important;

    color: #252525 !important;
}

#cardLoop
> div[style*="display:flex"]
+ .card .form-group > b {

    font-weight: 500 !important;

    color: #252525 !important;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991px) {

    .mainarea-containrer {
        margin-top: 25px;
    }


    .mainarea-containrer > .card:nth-child(2) {
        height: auto !important;
    }


    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row {

        display: block !important;

        margin: 0 !important;
    }


    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group {

        max-width: 100% !important;

        padding: 8px 0 !important;

        border: 0 !important;
    }


    .mainarea-containrer > .card:nth-child(2)
    > .card-body > .row > .form-group::after {

        display: none !important;
    }


    .mainarea-containrer > .card:nth-child(2) img {

        margin-bottom: 12px;
    }


    #car_pulling > .row {

        margin-left: 0 !important;
        margin-right: 0 !important;
    }


    #car_pulling > .row > .form-group {

        padding-left: 0 !important;
        padding-right: 0 !important;

        margin-bottom: 16px !important;
    }


    #car_pulling
    > div[style*="background-color:#17675F1A"] {

        margin: 15px 0 20px !important;
    }


    #cardLoop
    > div[style*="display:flex"]
    + .card .row {

        margin: 0 !important;
    }
}
@media (min-width: 992px) {

    #car_pulling .form-group:has(#route_link),
    #car_pulling .form-group:has(#pay_for_one_side),
    #car_pulling .form-group:has(#share_info) {
        display: flex !important;
        flex-direction: column;
    }

    #car_pulling #route_link,
    #car_pulling #pay_for_one_side,
    #car_pulling #share_info {
        margin-top: auto !important;
    }
}
#car_pulling select option:hover,
#car_pulling select option:checked {
    background: #17675F !important;
    background-color: #17675F !important;
    color: #ffffff !important;
}

/* Select2 field */
#car_pulling .select2-container {
    width: 100% !important;
}

#car_pulling .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;
}

#car_pulling .select2-container .select2-selection__rendered {
    line-height: 38px !important;
    padding-left: 14px !important;
    color: #252525 !important;
}

#car_pulling .select2-container .select2-selection__arrow {
    height: 38px !important;
}

/* Hovered option */
.select2-container--default
.select2-results__option--highlighted[aria-selected] {
    background-color: #17675F !important;
    color: #ffffff !important;
}

/* Selected option */
.select2-container--default
.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}
</style>
<?php
$sharing_with = array("Male","Female","Any","Noboday");
?>
<div class="mainarea-containrer" style="">
    <div style="float:left">
            <!-- <h3 class="card-title">Rider Profile</h3> -->
            <h4 class="profile-overview-title">Rider's Profile</h4>
        </div>
<div class="card" style="margin-top:10px;margin-right: 20px !important;width:100%;border-radius:10px">

    <div class="card-body" style="padding-bottom: 0 !important;">
        <div class="row align-items-center">
            <div class="col-6 col-sm-6 col-md-2  col-lg-3 form-group " style="margin-bottom: 8px !important;">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="text-align:right;"> 
    @php
        $defaultImage = "https://i.pinimg.com/236x/d6/5c/fa/d65cfa8b47227df12fb97217e8f940e3.jpg";
        $imageName = auth()->user()->imagename;
    @endphp

    @if(!empty($imageName))
        @if(auth()->user()->group_id == '825')         
            <img 
                src="https://cms.bnu.edu.pk/students_pics/{{ $imageName }}" 
                alt="Profile Image"
                width="100%" height="190"
            style="object-fit: contain !important; object-position: center !important;"
                onerror="this.onerror=null;this.src='{{ $defaultImage }}';"
            >    
        @else
            <img 
                src="https://cms.bnu.edu.pk/assets/staffimages/{{ $imageName }}" 
                alt="Profile Image"
                width="100%" height="190"
            style="object-fit: contain !important; object-position: center !important;"
                onerror="this.onerror=null;this.src='{{ $defaultImage }}';"
            > 
        @endif
    @else
        <img 
            src="{{ $defaultImage }}" 
            alt="Default Image"
            width="100%" height="190"
            style="object-fit: contain !important; object-position: center !important;"
        >
    @endif                                 
</div>
                </div>
            </div>
            {{-- <div class="col-6 col-sm-6 col-md-6  col-lg-6 form-group ">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Name:</b></label>           
                        <span><?=auth()->user()->full_name?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Mobile:</b></label>           
                        <span><?=auth()->user()->mobile?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Email:</b></label>           
                        <span><?=auth()->user()->email?></span>
                    </div>
                </div>               
            </div> --}}
            <!-- Name -->
<div class="col-12 col-sm-6 col-md-3 col-lg-3 form-group">
    <label class="col-form-label">
        <b>Name:</b>
    </label>
    <span><?=auth()->user()->full_name?></span>
</div>

<!-- Mobile -->
<div class="col-12 col-sm-6 col-md-3 col-lg-3 form-group">
    <label class="col-form-label">
        <b>Mobile:</b>
    </label>
    <span><?=auth()->user()->mobile?></span>
</div>

<!-- Email -->
<div class="col-12 col-sm-6 col-md-3 col-lg-3 form-group">
    <label class="col-form-label">
        <b>Email:</b>
    </label>
    <span><?=auth()->user()->email?></span>
</div>
        </div>  
    </div>
</div>
 <div style="float:left">
            <h4 class="profile-overview-title">Car Pooling</h4>
        </div>
<div class="card" style="margin-top:10px;margin-right: 20px !important;width:100%;border-radius:10px;">
  
    <div class="card-body">
        <form id="car_pulling" method="post" onsubmit="event.preventDefault(); autoSearch(this);">    
            <input type="hidden" id="cp_id" name="cp_id">  
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6  col-lg-6 form-group " id="show_hide_ride">
                    <!-- <label class="col-form-label" id="ride_status_id"><b>Ride Status</b></label>            -->
                    <label class="col-form-label" id="ride_status_id"><b>Ride Status:</b></label>           
                    <select  class="form-control" onchange="rideClicked(this)" name="want_ride" id="want_ride" >
                        <option value="">--Please Select Ride Options--</option>                
                        <!-- <option value="Offer Ride">Offer Ride</option>
                        <option value="Get Ride">Get Ride</option>                 -->
                        <option value="Offer Ride">Offer a Ride</option>
                        <option value="Get Ride">Get a Ride</option>                
                    </select>   
                </div>  
                <div class="col-12 col-sm-12 col-md-6  col-lg-6 form-group ">
                    <!-- <label class="col-form-label" ><b>Contact Options</b></label>            -->
                    <label class="col-form-label" ><b>Contact Options:</b></label>           
                    <select  class="form-control " onchange="contactDetail(this)" name="contact_me" id="contact_me">
                        <option value="">--How to Contact Me--</option>                
                        <!-- <option value="On Mobile">On Mobile</option>                         
                        <option value="On CMS Email">On CMS Chat</option>              -->
                        <option value="On Mobile">Via Mobile</option>                         
                        <option value="On CMS Email">Via CMS Chat</option>             
                    </select>   
                </div>  
                <div id="contDetail" class="col-12 col-sm-12 col-md-3 col-lg-3 form-group" style="display:none">
                    <label class="col-form-label" ><b>Contact Detail</b></label>           
                    <input type="text" class="form-control" readonly id="contact_detail" name="contact_detail" placeholder="Contact Detail">    
                </div>     
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-3  col-lg-3 form-group ">
                    <!-- <label class="col-form-label" ><b>Vehicle Type</b></label>            -->
                    <label class="col-form-label" ><b>Vehicle Type:</b></label>           
                    <select  class="form-control" name="vehical_type" id="vehical_type"  onchange="vehicalType(this)" data-placeholder="--Please Select Vehical--">
                        <!-- <option value="">--Please Select Vehical--</option>                 -->
                        <option value="">--Please Select a Vehicle--</option>                
                        <option value="Car">Car</option>
                        <option value="Bike">Bike</option>                
                    </select>   
                </div>  
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group vehicle_cap">
                    <!-- <label class="col-form-label" ><b>Sharing Capacity</b></label>            -->
                    <label class="col-form-label" ><b>Sharing Capacity:</b></label>           
                    <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" maxlength="1" class="form-control" id="vehicle_capacity" name="vehicle_capacity" placeholder="Sharing Capacity">    
                </div>  
                
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group vehicle_cap">
                        <!-- <label class="col-form-label" ><b>Vehicle No</b></label>            -->
                        <label class="col-form-label" ><b>Vehicle No.</b></label>           
                        <input type="text" class="form-control" style="text-transform: uppercase;" id="vehicle_no" name="vehicle_no" placeholder="ABC-00-0000">    
                    </div>  
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group "> 
                    <!-- <label class="col-form-label"><b>Share With</b></label> -->
                    <label class="col-form-label"><b>Share With:</b></label>
                    <select  class="form-control" name="share_with" id="share_with">
                        <!-- <option value="">--Select Share With--</option>                
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>   
                        <option value="Any">Any</option>                               
                        <option value="Nobody">Nobody</option>                                -->
                        <option value="">-Please Select Your Sharing Preference-</option>                
                        <option value="Male">Males</option>
                        <option value="Female">Females</option>   
                        <option value="Any">Anyone</option>                               
                        <option value="Nobody">No One</option>                               
                    </select>                 
                </div>
            </div>
            </hr>               
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group "> 
                    <!-- <label class="form-label"><b>Preferred Location</b><span style="color:red;font-size:12px"> (You can add multiple locations by comma separated or entering space)</span></label> -->
                    <label class="form-label"><b>Preferred Location</b><span style="color:#A1A1A1;font-size:12px"> (You can enter multiple locations by seperating them with commas or spaces)</span></label>
                    <!-- <input type="text" name="search_data" id="search_data" placeholder="" autocomplete="off" class="form-control input-lg" />  -->
                
                    <input  type="text" class="form-control" name="search_data" id="search_data" placeholder="Preferred Location">        
                </div>
            </div>              
          <div style="
    padding:3px;
    background-color:#17675F1A !important;
    color:white !important;
    border-radius:5px;
    height:40px;

    display:flex;
    align-items:center; 
">
    <h3 class="card-title" style="margin:0;margin-left:20px; color:black;">Optional</h3>
</div>
                <div class="row">                                                 
                    <div id="routePlan" class="col-12 col-sm-12 col-md-12 col-lg-6 form-group">
                        <!-- <label class="col-form-label" ><b>Route Map</b></label>&nbsp &nbsp&nbsp<a target="_blank" style="color:blue" href="https://www.google.com/maps/dir/BNU+CITY+CAMPUS,+Shahrah-e-Quaid-e-Azam,+Upper+Mall+Scheme,+Lahore/Beaconhouse+National+University%D8%8C+13+Km+Off+Thokar+Niazbeg,+Raiwand+Rd,+Beacon+House+Society,+Lahore,+Punjab+53700%E2%80%AD/@31.4539637,74.2551051,12z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3919052e2e32fc41:0xf431b17a26ac6528!2m2!1d74.3529845!2d31.5406309!1m5!1m1!1s0x391855549698f6f7:0x4ffb44644f967144!2m2!1d74.2160936!2d31.36463!3e0">(Copy Route From here)</a>            -->
                        <label class="col-form-label" ><b>Route Map: &nbsp &nbsp&nbsp<a target="_blank" style="color:#EE8066; text-decoration:underline;" href="https://www.google.com/maps/dir/BNU+CITY+CAMPUS,+Shahrah-e-Quaid-e-Azam,+Upper+Mall+Scheme,+Lahore/Beaconhouse+National+University%D8%8C+13+Km+Off+Thokar+Niazbeg,+Raiwand+Rd,+Beacon+House+Society,+Lahore,+Punjab+53700%E2%80%AD/@31.4539637,74.2551051,12z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3919052e2e32fc41:0xf431b17a26ac6528!2m2!1d74.3529845!2d31.5406309!1m5!1m1!1s0x391855549698f6f7:0x4ffb44644f967144!2m2!1d74.2160936!2d31.36463!3e0">Click here for the Route Map</a></b></label>           
                        <input  type="text" class="form-control" id="route_link" name="route_link" placeholder="Route Map">    
                    </div>   
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 form-group ">
                        <!-- <label class="col-form-label" ><b>Route Option</b></label>                               -->
                        <label class="col-form-label" ><b>Route Option:</b></label>                              
                        <select  class="form-control " onchange="chkWay(this)"   name="pay_for_one_side" id="pay_for_one_side" data-placeholder="--Please Select--">
                            <option value="">--Please Select--</option>                
                            <option value="One Way">One Way</option>
                            <option value="Two Way">Two Way</option>                              
                        </select>    
                    </div>    
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="display:none" id="time_div">
                        <label class="col-form-label" ><b>Time</b></label>           
                        <input type="text" class="form-control" id="time" name="time" placeholder="One Way">    
                    </div>          
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="display:none" id="specific_day">
                        <label class="col-form-label" ><b>Enter Time for Today <span style="color:tomato">( Format 09:30 ) </span></b></label>           
                        <input type="text" class="form-control" id="specific_date_time" name="specific_date_time" placeholder="09:30">    
                    </div>                        
                </div>  
                <div id="showDays" class="row" style="display:none">
                    <?php
                    $days_array = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                    foreach($days_array as $day_name)
                    {                            
                    ?>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group " >  
                            <div class="row">                                   
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">   
                                    <label class="form-control">{{$day_name}}</label>  
                                </div> 
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">
                                    <select name="<?=$day_name?>_morning_time" class="form-control" id="<?=$day_name?>_morning_time">
                                    <?php
                                    $start_time = strtotime('00:00');
                                    $end_time = strtotime('23:59');
                                    $interval = 30 * 60; // 30 minutes in seconds
                                    echo '<option value="">Not Available</option>';
                                    for ($i = $start_time; $i <= $end_time; $i += $interval) {
                                        $time = date('H:i', $i);
                                        echo '<option value="' . $time . '">' . $time . '</option>';
                                    }
                                    ?>
                                    </select>    
                                </div>
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">
                                    <select name="<?=$day_name?>_back_time" class="form-control" id="<?=$day_name?>_back_time">
                                    <?php
                                    $start_time = strtotime('00:00');
                                    $end_time = strtotime('23:59');
                                    $interval = 30 * 60; // 30 minutes in seconds
                                    echo '<option value="">Not Available</option>';
                                    for ($i = $start_time; $i <= $end_time; $i += $interval) {
                                    $time = date('H:i', $i);
                                    echo '<option value="' . $time . '">' . $time . '</option>';
                                    }
                                    ?>
                                    </select>
                                </div> 
                            </div>  
                        </div>   
                    <?php
                    }
                    ?>              
                </div>      
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-6 form-group ">
                        <!-- <label class="col-form-label" ><b>Vehicle Information</b></label>            -->
                        <label class="col-form-label" ><b>Vehicle Information:</b></label>           
                        <select  class="form-control" onchange="shareInfo(this,1)" name="share_info" id="share_info">
                            <option value="">--Select Information--</option>     
                            <option value="Vehical Info">Vehicle Information</option>                               
                        </select>   
                    </div>  
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group show-vehicle" style="display:none">
                        <!-- <label class="col-form-label" ><b>Engine Capacity</b></label>            -->
                        <label class="col-form-label" ><b>Engine Capacity:</b></label>           
                        <input type="text" class="form-control" id="vehicle" name="vehicle" placeholder="Engine Capacity">    
                    </div>   
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group show-vehicle" style="display:none">
                        <!-- <label class="col-form-label" ><b>Vehicle Model</b></label>            -->
                        <label class="col-form-label" ><b>Vehicle Model:</b></label>           
                        <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="Vehicle Model">    
                    </div>  
                     <div class="col-12 col-sm-12 col-md-12  col-lg-6 form-group ">
                        <!-- <label class="col-form-label" ><b>Do you Smoke while Driving</b></label>            -->
                        <label class="col-form-label" ><b>Do you smoke while driving? (Please indicate for the comfort of other passengers)</b></label>           
                        <select  class="form-control" name="smoking_info" id="smoking_info">
                            <option value="">--Please Select--</option>         
                        
                            <option value="Yes">Yes</option>   
                            <option value="No">No</option>                               
                        </select>   
                    </div> 
                </div>
                <div class="row">
                                           
                </div>                
                <div class="row">  
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="text-align:left;margin-bottom:7px"  id="submitBtn">  
                        <button type="button" id="btn" class="btn btn-primary" onclick="submitForm()" style="background-color: #17675F; border:none;">Submit</button>                
                        <span id="successMsg"></span>   
                        <span id="errorMsg"></span>                          
                    </div>   
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;margin-bottom:20px;display:none"  id="updtBtn">                
                        <button type="button" class="btn btn-primary" onclick="UpdateForm()" style="background-color: #17675F !important; border:none !important;">Update</button> 
                        <button type="button" class="btn btn-secondary" onclick="AddNewForm()">Reset</button> 
                        <span id="UpdSuccessMsg"></span>       
                        <span id="errorUpdMsg"></span>                       
                    </div>         
                </div>               
        </form>
    </div>
</div>
</div>
<div  id="cardLoop">

</div>
<div class="table-responsive" style="display:none">
    <table class="table table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th class="text-white" style="text-align:center;">Sr#</th>
                <th class="text-white" style="text-align:left;">I Want To</th>
                <th class="text-white" style="text-align:left;">Contact Me Via</th>
                <th class="text-white" style="text-align:left;">Start Location</th>   
                <th class="text-white" style="text-align:left;">Vehicle Type</th>
                <th class="text-white" style="text-align:left;">Preffered Location</th>
                <th class="text-white" style="text-align:left;">Vehicle CC</th>    
                <th class="text-white" style="text-align:left;">Vehicle Model</th>
                <th class="text-white" style="text-align:left;">Vehicle Capacity</th>                              
                <th class="text-white" style="text-align:left;">Mon</th>
                <th class="text-white" style="text-align:left;">Tue</th>
                <th class="text-white" style="text-align:left;">Wed</th>
                <th class="text-white" style="text-align:left;">Thu</th>
                <th class="text-white" style="text-align:left;">Fri</th>
                <th class="text-white" style="text-align:left;">Sat</th>
                <th class="text-white" style="text-align:left;">Sun</th>       
                <th class="text-white" style="text-align:left;">Can Pay For</th>                           
                <th class="text-white" style="text-align:left;">Route Link</th>
                <th class="text-white" style="text-align:left;">Rider Name</th>             
            </tr>
        </thead>
        <tbody id="tbl_exporttable_to_xls">
        </tbody>
    </table>
</div>
</div>
@endsection
@section('javascript')     
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css"> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>   -->
<script type="text/javascript">
function check_validation(validation_array){
					var count_errors = 0;
					$.each(validation_array, function( index, value ) {
						//alert(value);
						
					});
					if(count_errors>0){
					return 0;
					}else{
					return 1;
					}
	}

	function sendGetRequest(endPoint,successBlock) {
        $.ajax({
            method: "GET",
            url:    url+'/api/'+endPoint,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
            console.log(res);
        });
    }


    function sendPostRequest(endPoint,successBlock,postData) {
        $.ajax({
            method: "POST",
            url:    url+'/api/'+endPoint,
            data : postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
            // alert('bilal')
        });
    }

function rideClicked(sel){     
    if(sel.value == 'Get Ride'){
        $('.vehicle_cap').hide();  
        $('#vehicle_capacity').val(''); 
        $('#vehicle_no').val('');        
    }else{
        $('.vehicle_cap').show();
    }
}

function AddNewForm(){
    window.scrollTo(0, 370);
    $('#search_data').tokenfield('setTokens', " ");
    $('#updtBtn').hide();
    $('#submitBtn').show();
    $("#car_pulling")[0].reset();
    $('#share_info').val('');
    $('#uni_timings').val('will share on contact');
    $('#showDays').hide();
    $('.show-vehicle').hide();   
    $('#startLocation').hide();
}
$(document).ready(function(){
    getRequest();
    // $('#search_data').tokenfield({
    // autocomplete :{
    //     source: function(request, response)
    //     {
    //     console.log('tokenfield console');             
    //     },
    //     delay: 100
    // }
    // });

});
function vehicalType(sel){
    if(sel.value == 'Car'){
        $('#vehicle_capacity').val(3);
    }else if(sel.value == 'Bike'){
        $('#vehicle_capacity').val(1);
    }
}
function contactDetail(sel){    
    var mobile = '<?=auth()->user()->mobile?>';
    var email = '<?=auth()->user()->email?>';
    if(sel.value == 'On Mobile'){            
        $('#contact_detail').val(mobile);
    }else if(sel.value == 'On CMS Email'){            
        $('#contact_detail').val(email);
    }
}
function inOutTime(sel){        
    if(sel.value == 'let me mention days wise'){
        $('#showDays').show();
    }else{
        $('#showDays').hide();
    }
}
function routeshareInfo(sel,id){
    if(sel.value == 'Route Map'){
        $('#area_pickup').val('');
        $('#route_link').val('');          
        $('#routePlan').show();
    }else if(sel.value == ''){          
        $('#routePlan').hide();
    }
}
function shareInfo(sel,id){    
    if(sel.value == 'Just My Start Location'){   
        $('#area_pickup').val('');
        $('#route_link').val('');
        $('#vehicle').val('');
        $('#vehicle_model').val('');           
        $('#startLocation').show();
    }else{
        $('#startLocation').hide();
    }    
    if(sel.value == 'Vehical Info'){ 
        $('#area_pickup').val('');            
        $('#vehicle').val('');
        $('#vehicle_model').val('');         
        $('.show-vehicle').show();
    }else if(sel.value == ''){          
        $('.show-vehicle').hide();
    }
}
function chkWay(sel){      
    $('#showDays').hide();
    $('#Monday_morning_time').val('');
    $('#Monday_back_time').val('');
    $('#Tuesday_morning_time').val('');
    $('#Tuesday_back_time').val('');
    $('#Wednesday_morning_time').val('');
    $('#Wednesday_back_time').val('');
    $('#Thursday_morning_time').val('');
    $('#Thursday_back_time').val('');
    $('#Friday_morning_time').val('');
    $('#Friday_back_time').val('');
    $('#Saturday_morning_time').val('');
    $('#Saturday_back_time').val('');
    $('#Sunday_morning_time').val('');
    $('#Sunday_back_time').val('');
    if(sel.value == 'One Way'){          
        $('#showDays').show(); 
    }       
    if(sel.value == 'Two Way'){                
        $('#showDays').show(); 
    }
}
function resetForm(){    
    $("#car_pulling")[0].reset();
}    
function submitForm() {  
    if($('#want_ride').val() == 'Get Ride'){
        var getdata = check_validation(['want_ride','contact_me','vehical_type','share_with','search_data']);   
    }else{
        var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);   
    }    
         
    // if($('#search_data').tokenfield('getTokensList') == '' )
    // {     
    // $('.tokenfield').css('border-color', 'red');
    // var getdata = 0;
    // }else{
    // $('.tokenfield').css('border-color', 'green');
    // }
    if(getdata==0){
        //window.scrollTo(0, 370);
        $('#errorMsg').css('color', 'red');
        $('#errorMsg').html('Compulsory Fields Missing');
        setTimeout(function(){
            $('#errorMsg').html('');                                                   
        },2000);
        return false;
    }
    var username = '<?=auth()->user()->username?>';   
    var bi_form = document.querySelector('#car_pulling');
    var postData = new FormData(bi_form);     
    $.ajax({
            type:'POST',
            url: "{{ url('/api/insert-car-pulling?username=') }}"+username,           
            data : postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            },            
            success:function(res){
                if (res.status) {  
                    AddNewForm(); 
                    $('#btn').prop('disabled', true);                    
                    $('#successMsg').css('color', 'green');
                    $('#successMsg').html('Ride Generated Successfully');
                    $(".tokenfield").removeAttr("style");                
                    getRequest();  
                    
                     setTimeout(function(){
            window.location.reload();
        }, 2000);
        
                    setTimeout(function(){
                        $('#successMsg').html('');   
                        $('#btn').prop('disabled', false);                                        
                    },6000);
                }else{
                   
                    $('#errorMsg').css('color', 'red');
                    $('#errorMsg').html(res.message);
                    setTimeout(function(){
                        $('#errorMsg').html('');   
                        $('#btn').prop('disabled', false);                                        
                    },3000);
                }
            },
            error: function (jqXHR, exception) {
                $('#errorMsg').css('color', 'red');
                $('#errorMsg').html('You Have Already Offered A Ride');
                setTimeout(function(){
                    $('#errorMsg').html('');   
                    $('#btn').prop('disabled', false);                                        
                },3000);   
            },               
    });   
 
}    
function UpdateForm() {  
    if($('#want_ride').val() == 'Get Ride'){
        var getdata = check_validation(['want_ride','contact_me','vehical_type','share_with','search_data']);   
    }else{
        var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);   
    }               
   // var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);      
    // if($('#search_data').tokenfield('getTokensList') == '' )
    // {     
    //     $('.tokenfield').css('border-color', 'red');
    //     var getdata = 0;
    // }else{
    //     $('.tokenfield').css('border-color', 'green');
    // }
    if(getdata==0){
        window.scrollTo(0, 370);  
        return false;
    } 
    var username = '<?=auth()->user()->username?>';   
    var bi_form = document.querySelector('#car_pulling');
    var data = new FormData(bi_form);    
    sendPostRequest('update-car-pulling2?username='+username,function (res){   
        if (res.status) {     
            $('#UpdSuccessMsg').css('color', 'green');
            $('#UpdSuccessMsg').html('Ride Updated Successfully');
            $(".tokenfield").removeAttr("style");
            getRequest();     
            setTimeout(function(){
                $('#UpdSuccessMsg').html('');                            
            },3000);
        }else{
            $('#errorUpdMsg').css('color', 'red');
            $('#errorUpdMsg').html(res.message);
            setTimeout(function(){
                $('#errorUpdMsg').html('');   
              //  $('#btn').prop('disabled', false);                                        
            },3000);
        }
    }, data);   
} 
function getRequest(){
    var username = '<?=auth()->user()->username?>';   
    // sendGetRequest('get-car-pulling2?username='+username,function (res){       
    //     console.log('Hello Baby', res.data);            
   
    // }, '');    
    $.ajax({
            type:'GET',
            url: "{{ url('/api/get-car-pulling2?username=') }}"+username,
            headers: {"Authorization": 'Bearer {{ auth()->user()->api_token }}'},     
            //headers:reqHeaders,
            success:function(res){
                if (res.data) {
                    var tbody = '';
                    var div = '';
                    var count = 1;                    
                    $(res.data).each(function(index,row){
                        //     if(row.cp_want_ride=='Offer Ride'){
                        //     $("#show_hide_ride").hide();//('readonly',true);
                        // }    
                        var sr = count++;
                        var link = row.cp_route_link;  
                        if(link != null){
                            if(link.includes("https") == true){
                                var rout = '<a target="_blank"  '+((row.cp_route_link == null)?'':'style="color:blue;cursor:pointer" href="'+link+'"')+' >'+((row.cp_route_link == null)?'Route Not Defined':'Route Link')+'</a>';
                            }else{
                                var rout = row.cp_route_link;
                            }
                        }else{
                            var rout = 'Not Available';
                        }
                                                      
                        tbody += '<tr>';
                        tbody += '<td align="center">'+sr+'</td>';                   
                        tbody += '<td>'+row.cp_want_ride+'</td>';
                        tbody += '<td>'+row.cp_contact_me+'</td>';
                        tbody += '<td>'+row.cp_area_pickup+'</td>';
                        tbody += '<td>'+row.cp_vehical_type+'</td>';
                        tbody += '<td>'+row.cp_preferred_location+'</td>';
                        tbody += '<td>'+row.cp_vehical_cc+'</td>';
                        tbody += '<td>'+row.cp_vehical_model+'</td>';
                        tbody += '<td>'+row.cp_capacity+'</td>';    
                        tbody += '<td>'+row.cp_monday+'</td>';
                        tbody += '<td>'+row.cp_tuesday+'</td>';
                        tbody += '<td>'+row.cp_wednesday+'</td>';
                        tbody += '<td>'+row.cp_thursday+'</td>';
                        tbody += '<td>'+row.cp_friday+'</td>';
                        tbody += '<td>'+row.cp_saturday+'</td>';
                        tbody += '<td>'+row.cp_sunday+'</td>';    
                        tbody += '<td>'+row.cp_pay_for_oneside+'</td>';                      
                        tbody += '<td><a target="_blank"  '+((row.cp_route_link == null)?'':'style="color:blue;cursor:pointer" href="'+link+'"')+' >'+((row.cp_route_link == null)?'Route Not Defined':'Route Link')+'</a></td>';
                        tbody += '<td>'+row.cp_entry_username+'</td>';    
                        tbody += '</tr>';    
                        
                        
                    
                        var monday              = (row.cp_monday == '-')?'Not Available':'<b>'+row.cp_monday+'</b>';                    
                        var tuesday             = (row.cp_tuesday == '-')?'Not Available':'<b>'+row.cp_tuesday+'</b>';
                        var wednesday           = (row.cp_wednesday == '-')?'Not Available':'<b>'+row.cp_wednesday+'</b>'; 
                        var thursday            = (row.cp_thursday == '-')?'Not Available':'<b>'+row.cp_thursday+'</b>'; 
                        var friday              = (row.cp_friday == '-')?'Not Available':'<b>'+row.cp_friday+'</b>'; 
                        var saturday            = (row.cp_saturday == '-')?'Not Available':'<b>'+row.cp_saturday+'</b>'; 
                        var sunday              = (row.cp_sunday == '-')?'Not Available':'<b>'+row.cp_sunday+'</b>'; 
                        var smoking             = (row.cp_smoking == null)?'Not Available':row.cp_smoking;                    
                        var specific_day        = (row.cp_specific_day == null)?'Not Available':row.cp_specific_day;
                        var vehicleCC           = (row.cp_vehical_cc == null)?'Not Available':row.cp_vehical_cc;
                        var vehicleModel        = (row.cp_vehical_model == null)?'Not Available':row.cp_vehical_model;
                        var vehicleCap          = (row.cp_capacity == null)?'Not Available':row.cp_capacity;
                        var routeOption         = (row.cp_route_option == null)?'Not Available':row.cp_route_option;
                        if(row.cp_route_option == 'One Way'){
                            var routeOption = row.cp_route_option;
                        }                                      
                        // div += '<div class="card" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">';
                        // div += '<div class="card-header" style="background-color:#2C3E50 !important;color:white !important;border-top-right-radius:10px;border-top-left-radius:10px;display:inline">';
                        // div += '<div style="float:left">';
                        // div += '<h3 class="card-title">Ride Details <span style="">('+row.cp_entry_datetime+')</span></h3>';
                        // div += '</div>';
                        // div += '<div style="float:right">';
                        // div += '<button type="button" onclick="edit_carPull('+row.cp_id+')"><i class="fa fa-edit"></i></button>';
                        // div += '</div>';
                        // div += '</div>';
                        // div += '<div class="card-body">';    
                            // Ride Details title OUTSIDE card
div += '<div style="display:flex; justify-content:space-between; align-items:center; width:100%; margin-top:10px; margin-right:20px;">';

div += '<h4 class="profile-overview-title" style="margin-bottom:0;">';
div += 'Ride Details <span style="font-size:14px;font-weight:400;">(' + row.cp_entry_datetime + ')</span>';
div += '</h4>';

div += '<button type="button" onclick="edit_carPull(' + row.cp_id + ')">';
div += '<i class="fa fa-edit"></i>';
div += '</button>';

div += '</div>';

// Ride Details card
div += '<div class="card" style="margin-top:10px;margin-right:20px !important;width:100%;border-radius:10px;">';
div += '<div class="card-body">';               
                        div += '<div class="row">';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>To Ride </b></label>';
                        div +=  row.cp_want_ride;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Contact Me </b></label>';
                        div +=  row.cp_contact_det;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Share With </b></label>';
                        div +=  row.cp_share_with;                    
                        div += '</div>'; 
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Vehicle Type </b></label>';
                        div +=  row.cp_vehical_type;                    
                        div += '</div>';                       
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Model </b></label>';
                        div +=  vehicleModel;                    
                        div += '</div>';     
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Engine Capacity </b></label>';
                        div +=  vehicleCC;                    
                        div += '</div>';
                        div += '</div>';
                        div += '<div class="row">';   
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Vehicle No </b></label>';
                        div +=  row.cp_vehical_no;                    
                        div += '</div>';                      
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Capacity </b></label>';
                        div +=  vehicleCap;                    
                        div += '</div>';   
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Route Option </b></label>';
                        div +=  routeOption;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Route Map </b></label>';
                        div +=  rout;//'<a target="_blank"  '+((row.cp_route_link == null)?'':'style="color:blue;cursor:pointer" href="'+link+'"')+' >'+((row.cp_route_link == null)?'Route Not Defined':'Route Link')+'</a>';                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Preferred Location </b></label>';
                        div +=  row.cp_preferred_location;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Smoking while Driving </b></label>';
                        div +=  smoking;                    
                        div += '</div>';                                
                        div += '</div>';   
                        div += '<div class="row">';    
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:brown" ><b>Monday </b></label>';
                        div +=  monday;                    
                        div += '</div>';       
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:yellowgreen" ><b>Tuesday </b></label>';
                        div +=  tuesday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:tomato" ><b>Wednesday </b></label>';
                        div +=  wednesday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:teal" ><b>Thursday </b></label>';
                        div +=  thursday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:sienna" ><b>Friday </b></label>';
                        div +=  friday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:seagreen" ><b>Saturday </b></label>';
                        div +=  saturday;                    
                        div += '</div>';                          
                        div += '</div>';
                        div += '</div>';
                        div += '</div>';
                    });
                    if(tbody == ''){
                        tbody = '<tr><td colspan="4"> No Car Pulling Request Found Yet! </td></tr>';
                    }  
                    $('#cardLoop').html(div);                    
                }   
            },
            error: function (jqXHR, exception) {
                // alert('bilal saleem')   
            },               
    });
  
}
function edit_carPull(id){  
    $('#updtBtn').show();
    $('#submitBtn').hide();
    $('#cp_id').val(id);
    window.scrollTo(0, 370);
    sendGetRequest('edit-car-pulling?cp_id='+id,function (res){             
        if (res.data){                   
            if(res.data[0].cp_monday.charAt(0) == '-'){
                var mondayMorningTime  = res.data[0].cp_monday.substring(0, 5);
                var mondayBackTime     = res.data[0].cp_monday.substring(1,6);
            }else{
                var mondayMorningTime  = res.data[0].cp_monday.substring(0, 5);
                var mondayBackTime     = res.data[0].cp_monday.substring(6,11);
            }
            if(res.data[0].cp_tuesday.charAt(0) == '-'){
                var tuesdayMorningTime = res.data[0].cp_tuesday.substring(0, 5);
                var tuesdayBackTime    = res.data[0].cp_tuesday.substring(1,6);
            }else{
                var tuesdayMorningTime = res.data[0].cp_tuesday.substring(0, 5);
                var tuesdayBackTime    = res.data[0].cp_tuesday.substring(6,11);
            }
            if(res.data[0].cp_wednesday.charAt(0) == '-'){
                var wednesdayMorningTime = res.data[0].cp_wednesday.substring(0, 5);
                var wednesdayBackTime    = res.data[0].cp_wednesday.substring(1,6);
            }else{
                var wednesdayMorningTime = res.data[0].cp_wednesday.substring(0, 5);
                var wednesdayBackTime    = res.data[0].cp_wednesday.substring(6,11);
            }             
            if(res.data[0].cp_thursday.charAt(0) == '-'){
                var thursdayMorningTime  = res.data[0].cp_thursday.substring(0, 5);
                var thursdayBackTime     = res.data[0].cp_thursday.substring(1,6);
            }else{
                var thursdayMorningTime  = res.data[0].cp_thursday.substring(0, 5);
                var thursdayBackTime     = res.data[0].cp_thursday.substring(6,11);
            }
            if(res.data[0].cp_friday.charAt(0) == '-'){
                var fridayMorningTime = res.data[0].cp_friday.substring(0, 5);
                var fridayBackTime    = res.data[0].cp_friday.substring(1,6);
            }else{
                var fridayMorningTime = res.data[0].cp_friday.substring(0, 5);
                var fridayBackTime    = res.data[0].cp_friday.substring(6,11);
            }
            if(res.data[0].cp_saturday.charAt(0) == '-'){
                var saturdayMorningTime = res.data[0].cp_saturday.substring(0, 5);
                var saturdayBackTime    = res.data[0].cp_saturday.substring(1,6);
            }else{
                var saturdayMorningTime = res.data[0].cp_saturday.substring(0, 5);
                var saturdayBackTime    = res.data[0].cp_saturday.substring(6,11);
            }
            if(res.data[0].cp_sunday.charAt(0) == '-'){
                var sundayMorningTime = res.data[0].cp_sunday.substring(0, 5);
                var sundayBackTime    = res.data[0].cp_sunday.substring(1,6);
            }else{
                var sundayMorningTime = res.data[0].cp_sunday.substring(0, 5);
                var sundayBackTime    = res.data[0].cp_sunday.substring(6,11);
            }  
            
            if(res.data[0].total_shared_req>=0){
                $('#want_ride').hide();
                $('#ride_status_id').html('<strong>Ride Status: </strong> You are unable to modify your ride status unless you have request(s) aginst your Ride.');
            }                
            $('#want_ride').val(res.data[0].cp_want_ride);
            $('#contact_me').val(res.data[0].cp_contact_me);
            $('#contact_detail').val(res.data[0].cp_contact_det);
            $('#vehical_type').val(res.data[0].cp_vehical_type);
            $('#share_with').val(res.data[0].cp_share_with);
            $('#search_data').val(res.data[0].cp_preferred_location);
            $('#share_info').val(res.data[0].cp_further_information);
            $('#route_share_info').val(res.data[0].route_information);
            $('#pay_for_one_side').val(res.data[0].cp_route_option);    
            $('#uni_timings').val(res.data[0].cp_timings);    
            $('#vehicle_capacity').val(res.data[0].cp_capacity);       
            $('#route_link').val(res.data[0].cp_route_link);   
            $('#smoking_info').val(res.data[0].cp_smoking); 
            $('#vehicle_no').val(res.data[0].cp_vehical_no);
            $('#search_data').tokenfield('setTokens', res.data[0].cp_preferred_location);    
            if(res.data[0].cp_want_ride == 'Get Ride'){                
                $('.vehicle_cap').hide();
            }else{
                $('.vehicle_cap').show();
            }
            if(res.data[0].cp_route_option == 'One Way'){
                $('#specific_day').hide();
                // $('#time_div').show();
                $('#time').val(res.data[0].cp_pay_for_oneside);                   
            } 
            if(res.data[0].cp_further_information == 'Just My Start Location'){
                $('#startLocation').show();
                $('#area_pickup').val(res.data[0].cp_area_pickup);
                $('#routePlan').hide();              
                $('.show-vehicle').hide();
                $('#vehicle').val('');
                $('#vehicle_model').val('');                 
            }
            if(res.data[0].cp_further_information == 'Route Map'){
                $('#routePlan').show();                 
                $('#startLocation').hide();
                $('#area_pickup').val('');                
                $('.show-vehicle').hide();
                $('#vehicle').val('');
                $('#vehicle_model').val('');              
            }
            if(res.data[0].cp_further_information == 'Vehical Info'){
                $('.show-vehicle').show();
                $('#vehicle').val(res.data[0].cp_vehical_cc);
                $('#vehicle_model').val(res.data[0].cp_vehical_model);                    
                $('#routePlan').show();                 
                $('#startLocation').hide();
                $('#area_pickup').val('');
            }
            if(res.data[0].cp_route_option == 'One Way' || res.data[0].cp_route_option == 'Two Way'){    
                $('#showDays').show();               
                $('#Monday_morning_time').val(mondayMorningTime);
                $('#Monday_back_time').val(mondayBackTime);
                $('#Tuesday_morning_time').val(tuesdayMorningTime);
                $('#Tuesday_back_time').val(tuesdayBackTime);
                $('#Wednesday_morning_time').val(wednesdayMorningTime);
                $('#Wednesday_back_time').val(wednesdayBackTime);
                $('#Thursday_morning_time').val(thursdayMorningTime);
                $('#Thursday_back_time').val(thursdayBackTime);
                $('#Friday_morning_time').val(fridayMorningTime);
                $('#Friday_back_time').val(fridayBackTime);
                $('#Saturday_morning_time').val(saturdayMorningTime);
                $('#Saturday_back_time').val(saturdayBackTime);
                $('#Sunday_morning_time').val(sundayMorningTime);
                $('#Sunday_back_time').val(sundayBackTime);
            }else{
                $('#showDays').hide();  
                $('#Monday_morning_time').val('');
                $('#Monday_back_time').val('');
                $('#Tuesday_morning_time').val('');
                $('#Tuesday_back_time').val('');
                $('#Wednesday_morning_time').val('');
                $('#Wednesday_back_time').val('');
                $('#Thursday_morning_time').val('');
                $('#Thursday_back_time').val('');
                $('#Friday_morning_time').val('');
                $('#Friday_back_time').val('');
                $('#Saturday_morning_time').val('');
                $('#Saturday_back_time').val('');
                $('#Sunday_morning_time').val('');
                $('#Sunday_back_time').val('');
            }     
        }else{
        
        }
    }, '');
}
$(document).ready(function () {
    $('#car_pulling select').select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });
});
</script>
@endsection

