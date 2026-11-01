@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

{{-- @dd("123"); --}}
<style>
    .profile-overview-title {
            margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
    }
    .form-group.row .SlectBox option:hover,
.form-group.row .SlectBox option:focus,
.form-group.row .SlectBox option:checked {
    color: #ffffff !important;
    background-color: #17675F !important;
}
.select2-results__option--highlighted {
    background-color: #17675F !important;
    color: #ffffff !important;
}

.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}
</style>
<style>
    /* Timetable filter wrapper */
    .form-group.row {
        margin: 0 0 18px 0;
        padding: 14px 12px;
        background: #ffffff;
        border-radius: 8px;
    }

    /* Small label like first screenshot */
    .form-group.row::before {
        content: "SEMESTER TIMETABLE";
        display: block;
        width: 100%;
        margin-bottom: 5px;
        font-size: 9px;
        font-weight: 600;
        color: #667085;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }

    /* Remove bootstrap side padding inside this section */
    .form-group.row .col-md-12 {
        padding-left: 0;
        padding-right: 0;
    }

    /* Select styling */
    .form-group.row .SlectBox {
        width: 100%;
        height: 38px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 400;
        color: #344054;
        background-color: #ffffff;
        border: 1px solid #e4e7ec;
        border-radius: 5px;
        box-shadow: none;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group.row .SlectBox:focus {
        border-color: #cbd5e1;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.05);
    }


















    /* =========================================
   SEMESTER TIMETABLE - NEW UI
   ========================================= */

#semester {
    border: none;
    border-radius: 9px;
    overflow: hidden;
    background: #fff;
    box-shadow: none;
}

/* Card Header */
#semester .card-header {
    min-height: 58px;
    padding: 0 20px;
    background: #ffffff;
    border: none;
    border-bottom: 1px solid #e9edf1;

    display: flex;
    align-items: center;
}

#semester .card-header strong {
    font-size: 16px;
    font-weight: 700;
    color: #182230;
}

/* Card body */
#semester .card-body {
    padding: 0;
}

#semester .col-lg-12 {
    padding: 0;
}

#semester .card-block {
    padding: 0;
}

#semester .table-responsive {
    border: none;
    margin: 0;
    overflow-x: auto;
}


/* =========================================
   MAIN TABLE
   ========================================= */

#semester .table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
    border-spacing: 0;
    background: #fff;
    color: #182230;
}

/* Remove Bootstrap borders */
#semester .table-bordered,
#semester .table-bordered th,
#semester .table-bordered td {
    border: none;
}


/* =========================================
   TABLE HEADER
   ========================================= */

#semester .table > thead {
    background: #19746b;
}

#semester .table > thead > tr > th,
#semester .table > thead > th {
    padding: 12px 16px;

    background: #19746b !important;
    color: #ffffff;

    font-size: 10px;
    font-weight: 700;
    line-height: 1.2;

    text-transform: uppercase;
    vertical-align: middle;
    border: none !important;
}


/* Class Date */
#semester .table > thead > th:first-child {
    width: 170px;
    text-align: left;
}

/* Subject */
#semester .table > thead > th:nth-child(2) {
    text-align: left;
}


/* =========================================
   MAIN ROWS
   ========================================= */

#semester .table > tbody > tr,
#semester .table > tr {
    border-bottom: 1px solid #edf0f2;
}

/* Remove Bootstrap striped background */
#semester .table-striped tbody tr:nth-of-type(odd),
#semester .table-striped tbody tr:nth-of-type(even) {
    background: #ffffff;
}

/* Alternating rows similar to screenshot */
#semester .table > tbody > tr:nth-child(even),
#semester .table > tr:nth-child(even) {
    background: #f8fafb;
}

#semester .table > tbody > tr:nth-child(odd),
#semester .table > tr:nth-child(odd) {
    background: #ffffff;
}

#semester .table > tbody > tr > td,
#semester .table > tr > td {
    padding: 15px 16px;
    border: none !important;
    vertical-align: middle;

    font-size: 12px;
    color: #182230;
}


/* =========================================
   CLASS DATE COLUMN
   ========================================= */

#semester .table > tbody > tr > td:first-child,
#semester .table > tr > td:first-child {
    width: 170px;
    min-width: 170px;

    font-size: 11px;
    line-height: 1.45;
    color: #182230;
}

#semester .table > tbody > tr > td:first-child strong,
#semester .table > tr > td:first-child strong {
    display: block;
    margin-bottom: 1px;

    font-size: 12px;
    font-weight: 700;
    color: #182230;
}


/* Day text */
#semester .table > tbody > tr > td:first-child br + *,
#semester .table > tr > td:first-child br + * {
    color: #98a2b3;
}


/* =========================================
   NESTED SUBJECT TABLE
   ========================================= */

#semester td > table {
    width: 100%;
    margin: 0;
    border: none;
    border-collapse: collapse;
    background: transparent;
}

#semester td > table tr {
    background: transparent !important;
    border: none;
}

#semester td > table td {
    width: 33.333%;
    padding: 4px 16px 4px 0 !important;

    background: transparent !important;
    border: none !important;

    font-size: 11px !important;
    font-weight: 500;
    line-height: 1.5;
    color: #182230 !important;

    vertical-align: top;
}


/* =========================================
   ROOM COLUMN
   ========================================= */

#semester .table > tbody > tr > td:last-child,
#semester .table > tr > td:last-child {
    width: 110px;
    min-width: 110px;

    text-align: center;

    font-size: 11px;
    font-weight: 600;
    color: #344054;
}


/* =========================================
   HOVER
   ========================================= */

#semester .table > tbody > tr:hover,
#semester .table > tr:hover {
    background: #f5f8f8;
}


/* =========================================
   MOBILE
   ========================================= */

@media (max-width: 768px) {

    #semester .card-header {
        padding: 0 14px;
        min-height: 52px;
    }

    #semester .card-header strong {
        font-size: 15px;
    }

    #semester .table {
        min-width: 800px;
    }

    #semester .table > thead > th {
        padding: 10px 12px;
    }

    #semester .table > tbody > tr > td,
    #semester .table > tr > td {
        padding: 13px 12px;
    }
}




/* =========================================================
   COURSE TIMETABLE — FIGMA STYLE OVERRIDE
   Figma frame: Course Timetable (2307:4)

   STYLE ONLY
   ---------------------------------------------------------
   - Existing HTML unchanged
   - Existing PHP unchanged
   - Existing JS unchanged
   - Nothing hidden
   - Nothing newly shown
   - Dropdown behavior unchanged
   - Today / Week / Semester structure unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

.profile-overview-title,
.form-group.row,
#today,
#week,
#semester {
    --tt-primary: #17675F;
    --tt-page-bg: rgba(15, 118, 110, 0.05);

    --tt-heading: #0F172A;
    --tt-text: #0F172A;
    --tt-secondary: #64748B;
    --tt-muted: #94A3B8;

    --tt-border: #E2E8F0;
    --tt-row-border: #F1F5F9;

    --tt-white: #FFFFFF;
    --tt-row-alt: #FAFBFC;

    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   2. PAGE TITLE
   Figma:
   24px Bold
   #0F172A
   ========================================================= */

.profile-overview-title {
    margin: 0 0 32px !important;

    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 24px !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;

    color: #0F172A !important;
}


/* =========================================================
   3. FILTER CARD
   Figma:
   white card
   24px padding
   12px radius
   ========================================================= */

.form-group.row {
    width: 100% !important;

    margin: 0 0 32px !important;
    padding: 24px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: none !important;
}


/*
   Existing CSS already creates this label.
   We only restyle it — no new content added here.
*/
.form-group.row::before {
    width: 100% !important;

    margin: 0 0 6px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    color: #64748B !important;

    text-transform: uppercase !important;
}


/* Existing Bootstrap column remains unchanged */
.form-group.row .col-md-12 {
    width: 100% !important;

    padding-left: 0 !important;
    padding-right: 0 !important;
}


/* =========================================================
   4. TIMETABLE SELECT
   Exact Figma:
   height 44px
   radius 8px
   border #E2E8F0
   14px Medium
   ========================================================= */

.form-group.row .SlectBox {
    width: 100% !important;

    height: 44px !important;
    min-height: 44px !important;

    margin: 0 !important;
    padding: 0 16px !important;

    background-color: #FFFFFF !important;

    border: 1px solid #E2E8F0 !important;
    border-radius: 8px !important;

    outline: 0 !important;
    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #0F172A !important;

    cursor: pointer;
}


/* Focus */
.form-group.row .SlectBox:focus {
    border-color: #17675F !important;

    outline: 0 !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* Dropdown options */
.form-group.row .SlectBox option {
    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #0F172A;

    background: #FFFFFF;
}


/* =========================================================
   5. ALL THREE TIMETABLE CARDS
   No display property is touched.
   Existing JS controls which one is visible.
   ========================================================= */

#today,
#week,
#semester {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


/* =========================================================
   6. CARD HEADERS
   Figma = 24px padding
   ========================================================= */

#today > .card-header,
#week > .card-header,
#semester > .card-header {
    width: 100% !important;

    min-height: 72px !important;

    margin: 0 !important;
    padding: 24px !important;

    align-items: center !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #F1F5F9 !important;

    border-radius: 12px 12px 0 0 !important;

    box-shadow: none !important;
}


/* Figma timetable card title:
   20px Bold */
#today > .card-header strong,
#week > .card-header strong,
#semester > .card-header strong {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;

    color: #0F172A !important;
}


/* =========================================================
   7. CARD BODY
   Existing wrappers remain
   ========================================================= */

#today > .card-body,
#week > .card-body,
#semester > .card-body {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #FFFFFF !important;
}


#today .col-lg-12,
#week .col-lg-12,
#semester .col-lg-12 {
    width: 100% !important;

    padding: 0 !important;
}


#today .card-block,
#week .card-block,
#semester .card-block {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;
}


/* =========================================================
   8. TABLE RESPONSIVE WRAPPER
   ========================================================= */

#today .table-responsive,
#week .table-responsive,
#semester .table-responsive {
    width: 100% !important;

    margin: 0 !important;

    border: 0 !important;

    background: #FFFFFF !important;

    overflow-x: auto !important;
}


/* =========================================================
   9. TABLE BASE
   ========================================================= */

#today .table,
#week .table,
#semester .table {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-collapse: collapse !important;
    border-spacing: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    color: #0F172A !important;
}


/* Remove old Bootstrap borders visually */
#today .table-bordered,
#today .table-bordered th,
#today .table-bordered td,

#week .table-bordered,
#week .table-bordered th,
#week .table-bordered td,

#semester .table-bordered,
#semester .table-bordered th,
#semester .table-bordered td {
    border-left: 0 !important;
    border-right: 0 !important;
}


/* =========================================================
   10. TABLE HEADER
   Exact Figma:
   #17675F
   11px Bold uppercase
   ========================================================= */

#today .table > thead,
#week .table > thead,
#semester .table > thead {
    background: #17675F !important;
}


#today .table > thead > tr > th,
#today .table > thead > th,

#week .table > thead > tr > th,
#week .table > thead > th,

#semester .table > thead > tr > th,
#semester .table > thead > th {
    height: 50px !important;

    padding: 16px !important;

    vertical-align: middle !important;

    background: #17675F !important;

    border: 0 !important;
    border-bottom: 1px solid #F1F5F9 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    text-transform: uppercase !important;

    color: #FFFFFF !important;

    white-space: nowrap;
}


/* =========================================================
   11. TABLE ROWS
   Figma alternating:
   white / #FAFBFC
   ========================================================= */

#today .table > tbody > tr,
#today .table > tr,

#week .table > tbody > tr,
#week .table > tr,

#semester .table > tbody > tr,
#semester .table > tr {
    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #F1F5F9 !important;
}


/* Alternating */
#today .table > tbody > tr:nth-child(even),
#today .table > tr:nth-child(even),

#week .table > tbody > tr:nth-child(even),
#week .table > tr:nth-child(even),

#semester .table > tbody > tr:nth-child(even),
#semester .table > tr:nth-child(even) {
    background: #FAFBFC !important;
}


#today .table > tbody > tr:nth-child(odd),
#today .table > tr:nth-child(odd),

#week .table > tbody > tr:nth-child(odd),
#week .table > tr:nth-child(odd),

#semester .table > tbody > tr:nth-child(odd),
#semester .table > tr:nth-child(odd) {
    background: #FFFFFF !important;
}


/* =========================================================
   12. STANDARD TABLE CELLS
   ========================================================= */

#today .table > tbody > tr > td,
#today .table > tr > td,

#week .table > tbody > tr > td,
#week .table > tr > td,

#semester .table > tbody > tr > td,
#semester .table > tr > td {
    padding: 16px !important;

    vertical-align: middle !important;

    background: transparent !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 400 !important;
    line-height: 1.45 !important;

    color: #0F172A !important;
}


/* =========================================================
   13. SEMESTER — DATE COLUMN
   Figma:
   date 14px Semibold
   day 12px muted
   width 180px
   ========================================================= */

#semester .table > tbody > tr > td:first-child,
#semester .table > tr > td:first-child {
    width: 180px !important;
    min-width: 180px !important;

    padding: 16px 16px 16px 28px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;

    color: #94A3B8 !important;

    text-align: left !important;
}


/* Date */
#semester .table > tbody > tr > td:first-child strong,
#semester .table > tr > td:first-child strong {
    margin: 0 0 2px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #0F172A !important;
}


/* =========================================================
   14. SEMESTER — SUBJECT NESTED TABLE
   Existing structure stays exactly intact.
   ========================================================= */

#semester td > table {
    width: 100% !important;

    margin: 0 !important;

    background: transparent !important;

    border: 0 !important;

    border-collapse: separate !important;
    border-spacing: 8px 0 !important;
}


#semester td > table tr {
    background: transparent !important;

    border: 0 !important;
}


/* Figma slot cards */
#semester td > table td {
    width: 33.333333% !important;

    padding: 12px !important;

    vertical-align: top !important;

    background: rgba(15, 118, 110, 0.01) !important;

    border: 0 !important;
    border-radius: 8px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;
    line-height: 1.45 !important;

    color: #0F172A !important;
}


/* Class time text is part of same existing cell.
   Keep it visible, only make overall line rhythm compact. */
#semester td > table td br {
    line-height: 8px !important;
}


/* =========================================================
   15. SEMESTER ROOM COLUMN
   ========================================================= */

#semester .table > tbody > tr > td:last-child,
#semester .table > tr > td:last-child {
    width: 140px !important;
    min-width: 140px !important;

    padding: 16px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 600 !important;

    color: #252525 !important;

    text-align: center !important;

    white-space: nowrap !important;
}


/* =========================================================
   16. TODAY TIMETABLE
   Keep every existing column visible
   ========================================================= */

#today .table > tbody > tr > td,
#today .table > tr > td {
    min-height: 56px !important;
}


#today .table > tbody > tr > td:first-child,
#today .table > tr > td:first-child {
    font-weight: 600 !important;

    color: #0F172A !important;
}


/*
   Existing orange/brown <font> elements remain visible.
   Just align typography with Figma.
*/
#today .table font {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;
}


/* =========================================================
   17. WEEK TIMETABLE
   Existing subject/time cells remain intact
   ========================================================= */

#week .table > tbody > tr > td:first-child,
#week .table > tr > td:first-child {
    width: 180px !important;
    min-width: 180px !important;

    font-size: 14px !important;
    font-weight: 600 !important;

    color: #0F172A !important;
}


#week .table > tbody > tr > td:not(:first-child),
#week .table > tr > td:not(:first-child) {
    font-size: 13px !important;
    font-weight: 500 !important;

    color: #0F172A !important;
}


/* =========================================================
   18. EMPTY STATE TEXT
   Existing messages remain visible
   ========================================================= */

#today h5,
#week h5,
#semester h5 {
    margin: 0 !important;
    padding: 32px 20px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.5 !important;

    color: #64748B !important;

    text-align: center !important;
}


/* =========================================================
   19. HOVER
   Visual only
   ========================================================= */

#today .table > tbody > tr:hover,
#today .table > tr:hover,

#week .table > tbody > tr:hover,
#week .table > tr:hover,

#semester .table > tbody > tr:hover,
#semester .table > tr:hover {
    background: rgba(15, 118, 110, 0.025) !important;
}


/* =========================================================
   20. RESPONSIVE
   Existing structure remains visible.
   Horizontal scroll is used instead of hiding columns.
   ========================================================= */

@media (max-width: 991.98px) {

    .profile-overview-title {
        margin-bottom: 24px !important;
    }


    .form-group.row {
        margin-bottom: 24px !important;

        padding: 20px !important;
    }


    #today .table,
    #week .table,
    #semester .table {
        min-width: 850px !important;
    }

}


@media (max-width: 767.98px) {

    .profile-overview-title {
        font-size: 22px !important;
    }


    .form-group.row {
        padding: 16px !important;

        border-radius: 10px !important;
    }


    #today,
    #week,
    #semester {
        border-radius: 10px !important;
    }


    #today > .card-header,
    #week > .card-header,
    #semester > .card-header {
        min-height: 62px !important;

        padding: 18px 16px !important;
    }


    #today > .card-header strong,
    #week > .card-header strong,
    #semester > .card-header strong {
        font-size: 18px !important;
    }


    #today .table > thead > th,
    #week .table > thead > th,
    #semester .table > thead > th {
        padding: 13px 12px !important;
    }


    #today .table > tbody > tr > td,
    #today .table > tr > td,

    #week .table > tbody > tr > td,
    #week .table > tr > td,

    #semester .table > tbody > tr > td,
    #semester .table > tr > td {
        padding: 14px 12px !important;
    }

}
.select2-results__option--highlighted {
    background-color: #17675F !important;
    color: #ffffff !important;
}

.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}


/* Select2 selected field */
#timetableSelect + .select2-container .select2-selection--single {
    height: 44px !important;
    border: 1px solid #17675F !important;
    border-radius: 8px !important;
}

#timetableSelect + .select2-container .select2-selection__rendered {
    line-height: 42px !important;
    padding-left: 16px !important;
    color: #0F172A !important;
}

/* Dropdown option hover */
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
{{-- @dd("213"); --}}
<h4 class="profile-overview-title">Course Timetable</h4>


<div class="form-group row">
													<!-- <label class="col-md-12 form-label">Inline Select:</label> -->
													<div class="col-md-12">
														<select id="timetableSelect" class="SlectBox form-control" onchange="time(this.value)">
															<!-- <option value="0">All</option> -->
                              <option value="3">Semester Timetable</option>
															<option value="1">Today Timetable</option>
															<option value="2">Weekly Timetable</option>
									
														</select>
													</div>
												</div>

                        
<div class="card" id="today">
  <div class="card-header" style="padding-left: 0 !important;background-color:#f2f6f9 !important;">
    <strong>Today Timetable</strong>
    <!-- <label>Today Classes</label> -->
  </div>
  <div class="card-body">
  <div class=" col-lg-12">
    <div class="card-block table-border-style">
      <div class="table-responsive">
      <?php if(count($timetable)>0){ ?>
        <table class="table table-bordered">
          <thead>
            <th align="center"> Day</th>
            <th align="center">Subject Name</th>
            <th align="center">Class Date</th>
            <th align="center">Class Time</th>
          </thead>
          <tr>
            <?php
            $count = 0;
            $today=date("d-M-Y");//'10-Mar-2021';//
          
            foreach ($timetable as $rec) {
              $curr_date = date("d-M-Y", strtotime(substr($rec->tt_class_date, 0, 11)));
              if($today==$curr_date){
            ?>
              <td align="left" ;>
                <?= $rec->tt_subject_day; ?>
              </td>
              <td align="left" ;>
                <?= $rec->course_name ?>
              </td>
              <td align="left" ;>
                <?php if ($count == 0) { ?>
                  <font color="orange"> <?= $curr_date; ?></font>

                <?php $count = 1;
                } else { ?>
                  <font color="brown"> <?= $curr_date; ?></font>
                <?php
                } ?>
              </td>
              <td align="left" color="pink" ;>
                <?= $rec->tt_subject_time_from . ' - ' . $rec->tt_subject_time_to; ?>
              </td>
          </tr>

          <?php }
             } }else{ 
              echo '<h5 align="center" >You have No Class Today For this Course </h5>'; 
             }?>

        </table>
      </div>
    </div>
  </div>
</div>
</div>




<div class="card" id="week">
  <div class="card-header" style="padding-left: 0 !important;background-color:#f2f6f9 !important;">
    <strong>Week Timetable</strong>
    <!-- <label>Today Classes</label> -->
  </div>
  <div class="card-body">
  <div class=" col-lg-12">
    <div class="card-block table-border-style">
      <div class="table-responsive">
      <?php if(count($weekly)>0){ ?>
        <table class="table table-bordered">
          <thead>
            <th align="center"> <strong>Day</strong></th>
            <th colspan="4" align="center"><strong>Subject Name & Class Time</strong></th>
            
          </thead>
          <tr>
            <?php
            $count = 0;
            $sub_date=''; 
            $i=1;
            $background_colors = array('steelblue','orange','green','brown');
            
            foreach ($weekly as $rec) {
              $rand_background = $background_colors[array_rand($background_colors)];
              if($rec->tt_subject_day !=$sub_date){
                $sub_date = $rec->tt_subject_day;
            ?>
              <td align="left" ;>
                <?= $rec->tt_subject_day; ?>
              </td>
              
              @foreach ($weekly as $key) 
              <?php
             
              $date= $key->tt_subject_day;
             
              if( $sub_date == $key->tt_subject_day  ){ 

                ?>
                <td >  {{$key->tt_subject_name}} <br>
                (<?= $key->tt_subject_time_from . ' - ' . $key->tt_subject_time_to; ?>)
             <?php 
             $i++;
            }
             
             ?>
              @endforeach
              </td>
          </tr>
        <?php }
             } }else{ 
              echo '<h5 align="center" >No Time Table has been created for this course yet </h5>'; 
             }?>
        </table>
      </div>
    </div>
  </div>
</div>
</div>

<!-- /////////////////// semester timetable//////////////////////// -->
<div class="card" id="semester">
  <div class="card-header" style="padding-left: 0 !important;background-color:#f2f6f9 !important;">
    <strong>Semester Timetable</strong>
  </div>
  <div class="card-body">
  <div class="col-lg-12">
    <div class="card-block table-border-style">
      <div class="table-responsive">
      <?php if(count($timetable)>0){ ?>

        <table class="table table-bordered table-striped">
          <thead class="thead-light">
            <th align="center"> <strong>Class Date </strong> </th>
            <th align="center"><strong>Subject Name (Class Time)</strong></th>
                <th align="center" style="padding-left:31px !important;">
        <strong>Room</strong>
    </th>
          </thead>
          <tr>
            <?php
            $count = 0;
            $date='';
            $subject='';
            $curr_date ='';
            $background_colors = array('steelblue','orange','green','brown');
           
            
            foreach ($timetable as $rec) {
              $rand_background = $background_colors[array_rand($background_colors)];

              if($curr_date != date("d-M-Y", strtotime(substr($rec->tt_class_date, 0, 11))) ){
                $curr_date = date("d-M-Y", strtotime(substr($rec->tt_class_date, 0, 11)));
            ?>
              <td align="left" ;>
                 <strong><?=$curr_date; ?></strong> <br><?= $rec->tt_subject_day; ?>
              </td>
              <td>
                  <table>
                  <tr>
                  @foreach ($timetable as $key) 
                  <?php
                  $date= date("d-M-Y", strtotime(substr($key->tt_class_date, 0, 11)));
                  $subject=$key->course_name;
                  if( $curr_date == date("d-M-Y", strtotime(substr($key->tt_class_date, 0, 11)))  ){ 

                    ?>
                    <!-- style="color:<?=$rand_background;?>">  -->
                    <td > <?= $key->course_name ?> <br>
                    (<?= $key->tt_subject_time_from . ' - ' . $key->tt_subject_time_to; ?>)
                <?php } ?>
             
                  @endforeach
                  </td>
                  </tr>
                  </table>
              </td>
              <td>{{$rec->room_name}}</td>
          </tr>

            <?php }
             } }else{ 
              echo '<h5 align="center" >No Timetable has been created for this course yet. </h5>'; 
             }?>

        </table>
      </div>
    </div>


  </div>

</div>
</div>
@stop


@section('javascript')
<script type="text/javascript">
   $(document).ready(function(){
     time(3);
  });
function time(sel){
if(sel==0){
  $('#today').show();
  $('#week').show();
  $('#semester').show();
 
}else if(sel==1){
  $('#today').show();
  $('#week').hide();
  $('#semester').hide();
}else if(sel==2){
  $('#today').hide();
  $('#semester').hide();
  $('#week').show();
}else if(sel==3){
  $('#today').hide();
  $('#week').hide();
  $('#semester').show();
  //console.log(sel);
}
}

$(document).ready(function () {
    $('#timetableSelect').select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    time(3);
});
</script>

@stop

