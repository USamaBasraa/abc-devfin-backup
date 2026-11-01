@extends('layouts.new_app')
@section('content')
{{-- @dd("!234"); --}}
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        /* ==========================================================
           FIGMA: Report Generator (node 851:2928)
           VISUAL LAYER ONLY.
           Existing Blade/PHP/HTML IDs, conditions, routes, AJAX,
           downloads, DataTables and report logic remain unchanged.
           ========================================================== */

        :root {
            --rg-teal: #17675f;
            --rg-teal-soft: rgba(15, 118, 110, .12);
            --rg-text: #0f172a;
            --rg-muted: #475569;
            --rg-subtle: #94a3b8;
            --rg-border: #e2e8f0;
            --rg-row: #f8fafc;
            --rg-chip: #f1f5f9;
            --rg-white: #ffffff;
        }

        .grid_center { text-align: center; }
        .button_div {
            margin-left: auto;
            margin-right: auto;
            border: solid 2px #6f42c1;
        }
        .button_size {
            width: auto;
            font-size: 13px;
            margin: 0;
            float: none;
        }
        .criteria_sub_div { float: left; padding: 0; }

        /* Keep page typography aligned to Figma. */
        @supports selector(:has(*)) {
            div:has(> #criteria_panel) {
                width: 100% !important;
                float: none !important;
                box-sizing: border-box;
                border: 0 !important;
                padding: 0 25px 36px !important;
                font-family: 'Montserrat', sans-serif;
                /* background: linear-gradient(90deg, rgba(255,255,255,.98) 0%, rgba(23,103,95,.10) 100%) !important; */
                min-height: 520px;
            }

            /* Legacy title strip -> Figma heading. */
            div:has(> #criteria_panel) > div:first-child {
                width: 100% !important;
                float: none !important;
                min-height: 96px;
                margin: 0 !important;
                padding: 22px 4px 18px !important;
                box-sizing: border-box;
                background: transparent !important;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
            }

            div:has(> #criteria_panel) > div:first-child > div:first-child {
                float: none !important;
                width: auto !important;
                margin: 0 !important;
                padding: 0 0 28px !important;
                line-height: 1.3 !important;
                color: var(--rg-text) !important;
                font-family: 'Montserrat', sans-serif !important;
                font-size: 20px !important;
                font-weight: 600 !important;
                position: relative;
            }

            div:has(> #criteria_panel) > div:first-child > div:first-child::after {
                content: 'Improve Performance';
                position: absolute;
                left: 0;
                bottom: 3px;
                color: #252525;
                font-size: 14px;
                font-weight: 400;
                white-space: nowrap;
            }

            div:has(> #criteria_panel) > div:first-child > div:last-child {
                display: none !important;
            }
        }

        /* ==========================================================
           FILTER / REPORT CONFIGURATION CARD
           ========================================================== */

        #criteria_panel {
            float: none !important;
            width: 100% !important;
            background: transparent;
        }

        /* First criteria_panel only: the configuration card. */
        #criteria_panel:nth-of-type(2) {
            background: var(--rg-white) !important;
            border: 0 !important;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,.04);
            padding: 32px !important;
            margin: 0 0 20px !important;
            box-sizing: border-box;
            overflow: visible;
        }

        #example2 {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            border: 0 !important;
            background: transparent !important;
            font-family: 'Montserrat', sans-serif;
        }

        /* Visual restructuring only: rows become grid items while
           existing row IDs / show-hide JS remain untouched. */
        #example2,
        #example2 > tbody {
            display: block;
        }

        #example2 > tbody {
            display: grid;
            grid-template-columns: 240px minmax(250px, auto) minmax(320px, 1fr);
            column-gap: 24px;
            row-gap: 28px;
            align-items: end;
            width: 100%;
        }

        #example2 > tbody > tr {
            width: 100%;
            margin: 0;
            border: 0;
            background: transparent;
        }

        /* Respect the existing inline/jQuery hidden state even though
           visible rows are visually block/grid based. */
        #criteria_filters_tr,
        #criterias_buttons_tr {
            display: block !important;
        }

        #criteria_filters_tr[style*="display:none"],
        #criteria_filters_tr[style*="display: none"],
        #criterias_buttons_tr[style*="display:none"],
        #criterias_buttons_tr[style*="display: none"] {
            display: none !important;
        }

        #example2 > tbody > tr:first-child { grid-column: 1; }
        #criteria_panel_tr { grid-column: 2; }
        #criteria_filters_tr { grid-column: 3; }
        #criterias_buttons_tr { grid-column: 1 / -1; }

        /* School/Department row (only rendered for allowed groups). */
        #example2 > tbody > tr:first-child + tr:not([id]) {
            grid-column: 1 / -1;
        }

        #example2 td {
            border: 0 !important;
            color: var(--rg-muted);
            font-size: 13px;
            vertical-align: middle !important;
        }

        /* Shared controls */
        #example2 .form-control {
            height: 42px !important;
            min-height: 42px !important;
            border: 1px solid var(--rg-border) !important;
            border-radius: 8px !important;
            background: #fff !important;
            color: var(--rg-text) !important;
            font-family: 'Montserrat', sans-serif !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            box-shadow: none !important;
            outline: 0 !important;
            padding: 9px 36px 9px 12px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #example2 .form-control:focus {
            border-color: rgba(23,103,95,.55) !important;
            box-shadow: 0 0 0 3px rgba(23,103,95,.08) !important;
        }

        /* Session */
        #example2 > tbody > tr:first-child > td {
            display: block;
            width: 100%;
            padding: 24px 0 0 !important;
            position: relative;
        }

        #example2 > tbody > tr:first-child > td::before {
            content: 'Select Session';
            position: absolute;
            left: 0;
            top: 0;
            color: var(--rg-muted);
            font-size: 13px;
            line-height: 18px;
            font-weight: 600;
        }

        #session_rptg {
            width: 100% !important;
            max-width: 240px;
        }

        /* School / Department */
        #example2 > tbody > tr:first-child + tr:not([id]) > td {
            display: flex;
            gap: 12px;
            align-items: end;
            padding: 0 !important;
        }

        #school_id,
        #dept_id {
            width: calc(50% - 6px) !important;
            float: none !important;
            margin: 0 !important;
        }

        /* ==========================================================
           REPORT TYPE - FIGMA SEGMENTED CONTROL
           Existing radios remain the source of truth.
           ========================================================== */

        #criteria_panel_tr {
            min-height: 66px;
            padding-top: 24px;
            position: relative;
            align-self: end;
            white-space: nowrap;
        }

        #criteria_panel_tr::before {
            content: 'Report Type';
            position: absolute;
            left: 0;
            top: 0;
            color: var(--rg-muted);
            font-size: 13px;
            line-height: 18px;
            font-weight: 600;
        }

        #criteria_panel_tr {
            display: flex;
            align-items: center;
            width: max-content;
            max-width: 100%;
            background: var(--rg-teal-soft);
            border-radius: 10px;
            padding: 28px 4px 4px;
        }

        /* Hidden employee values are functional only. */
        #criteria_panel_tr td:has(#emp_school_id) {
            display: none !important;
        }

        /* Make each radio cover its adjacent text tab without changing HTML. */
        #criteria_panel_tr td:has(.cirter_type) {
            position: relative;
            width: 0 !important;
            min-width: 0 !important;
            padding: 0 !important;
            overflow: visible;
            z-index: 2;
        }

        #criteria_panel_tr .cirter_type {
            position: absolute;
            left: 0;
            top: -16px;
            width: 118px;
            height: 32px;
            margin: 0 !important;
            opacity: 0;
            cursor: pointer;
            z-index: 3;
        }

        #criteria_panel_tr td:has(.cirter_type) + td {
            min-width: 118px;
            height: 32px;
            padding: 7px 14px !important;
            border-radius: 8px !important;
            color: var(--rg-muted) !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            line-height: 18px;
            text-align: center !important;
            transition: background .15s ease, color .15s ease, box-shadow .15s ease;
        }

        #criteria_panel_tr td:has(.cirter_type:checked) + td {
            background: #fff !important;
            color: var(--rg-teal) !important;
            box-shadow: 0 2px 2px rgba(0,0,0,.05);
        }

        /* ==========================================================
           DYNAMIC FACULTY / COURSE / STUDENT FILTER
           ========================================================== */

        #criteria_filters_tr {
            min-height: 66px;
            position: relative;
            padding-top: 24px;
        }

        #criteria_filters_tr::before {
            content: 'Select Course';
            position: absolute;
            left: 0;
            top: 0;
            color: var(--rg-muted);
            font-size: 13px;
            line-height: 18px;
            font-weight: 600;
        }

        #criteria_filters_tr > td {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 0 !important;
        }

        #courses_faculty_rpgt,
        #courses_rptg {
            height: 42px !important;
        }

        #stu_enrollment_no {
            width: 100% !important;
        }

        /* ==========================================================
           QUICK VIEWS & ASSETS
           ========================================================== */

        #criterias_buttons_tr {
            width: 100%;
            position: relative;
            padding-top: 30px;
        }

        #criterias_buttons_tr::before {
            content: 'QUICK VIEWS & ASSETS';
            position: absolute;
            left: 0;
            top: 0;
            color: var(--rg-muted);
            font-size: 13px;
            line-height: 18px;
            font-weight: 600;
        }

        #criterias_buttons_tr > td {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
            padding: 0 !important;
            text-align: left !important;
        }

        #criterias_buttons_tr .btn,
        #course_outline_url .btn,
        #fileDownload,
        #downloadZip {
            float: none !important;
            width: auto !important;
            min-height: 38px;
            margin: 0 !important;
            padding: 9px 16px !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: var(--rg-chip) !important;
            color: var(--rg-text) !important;
            font-family: 'Montserrat', sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            line-height: 18px !important;
            box-shadow: none !important;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background .15s ease, color .15s ease;
        }

        #criterias_buttons_tr .btn:hover,
        #course_outline_url .btn:hover,
        #fileDownload:hover,
        #downloadZip:hover {
            background: #e2e8f0 !important;
            color: var(--rg-teal) !important;
        }

        #criterias_buttons_tr .col-12 {
            width: auto !important;
            max-width: none !important;
            flex: 0 0 auto !important;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0 !important;
            padding: 0 !important;
            text-align: left !important;
        }

        #course_outline_url {
            display: inline-flex;
            align-items: center;
        }

        #print_marks_sheet {
            background: var(--rg-teal) !important;
            color: #fff !important;
        }

        /* ==========================================================
           AJAX RESULT CARDS / TABLES
           ========================================================== */

        #assessment_marks,
        #course_criteria {
            width: 100%;
            font-family: 'Montserrat', sans-serif;
        }

        #assessment_marks:empty,
        #course_criteria:empty {
            display: none;
        }

        #assessment_marks:not(:empty),
        #course_criteria:not(:empty) {
            margin-top: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,.04);
            overflow: hidden;
        }

        #assessment_marks:not(:empty)::before {
            content: 'Student Marks Details';
            display: block;
            padding: 20px 18px;
            border-bottom: 1px solid var(--rg-border);
            color: var(--rg-text);
            font-size: 18px;
            line-height: 24px;
            font-weight: 600;
            background: #fff;
        }

        #assessment_marks table,
        #course_criteria table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
            background: #fff !important;
        }

        #assessment_marks table thead th,
        #assessment_marks table thead td,
        #course_criteria table thead th,
        #course_criteria table thead td {
            background: var(--rg-teal) !important;
            color: #fff !important;
            border: 0 !important;
            padding: 12px 14px !important;
            font-family: 'Montserrat', sans-serif !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            line-height: 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        #assessment_marks table tbody td,
        #course_criteria table tbody td {
            padding: 13px 14px !important;
            border: 0 !important;
            border-bottom: 1px solid var(--rg-border) !important;
            color: var(--rg-text) !important;
            font-family: 'Montserrat', sans-serif !important;
            font-size: 13px !important;
            line-height: 18px;
            background: #fff !important;
        }

        #assessment_marks table tbody tr:nth-child(even) td,
        #course_criteria table tbody tr:nth-child(even) td {
            background: var(--rg-row) !important;
        }

        #assessment_marks table tbody tr:last-child td,
        #course_criteria table tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        /* DataTables chrome around any dynamically returned table. */
        #assessment_marks .dataTables_wrapper,
        #course_criteria .dataTables_wrapper {
            padding: 0 !important;
        }

        #assessment_marks .dataTables_filter,
        #course_criteria .dataTables_filter,
        #assessment_marks .dataTables_length,
        #course_criteria .dataTables_length,
        #assessment_marks .dataTables_info,
        #course_criteria .dataTables_info,
        #assessment_marks .dataTables_paginate,
        #course_criteria .dataTables_paginate {
            padding: 12px 18px;
            color: var(--rg-muted) !important;
            font-size: 12px !important;
        }

        #assessment_marks .dataTables_filter input,
        #course_criteria .dataTables_filter input,
        #assessment_marks .dataTables_length select,
        #course_criteria .dataTables_length select {
            border: 1px solid var(--rg-border) !important;
            border-radius: 8px !important;
            min-height: 36px;
            padding: 6px 10px;
            box-shadow: none !important;
            outline: 0 !important;
        }

        /* Existing material-generation progress state. */
        .progress {
            width: 220px;
            height: 8px !important;
            margin: 6px 0 0 !important;
            border-radius: 999px !important;
            overflow: hidden;
            background: #e2e8f0 !important;
        }
        .progress-bar { background: var(--rg-teal) !important; }

        /* ==========================================================
           RESPONSIVE
           ========================================================== */

        @media (max-width: 1180px) {
            #example2 > tbody {
                grid-template-columns: 220px minmax(240px, 1fr);
            }
            #example2 > tbody > tr:first-child { grid-column: 1; }
            #criteria_panel_tr { grid-column: 2; }
            #criteria_filters_tr { grid-column: 1 / -1; }
            #criterias_buttons_tr { grid-column: 1 / -1; }
            #example2 > tbody > tr:first-child + tr:not([id]) { grid-column: 1 / -1; }
        }

        @media (max-width: 768px) {
            @supports selector(:has(*)) {
                div:has(> #criteria_panel) {
                    padding: 0 14px 28px !important;
                }
            }

            #criteria_panel:nth-of-type(2) {
                padding: 22px 18px !important;
            }

            #example2 > tbody {
                display: block;
            }

            #example2 > tbody > tr,
            #example2 > tbody > tr:first-child,
            #criteria_panel_tr,
            #criteria_filters_tr,
            #criterias_buttons_tr,
            #example2 > tbody > tr:first-child + tr:not([id]) {
                width: 100%;
                margin-bottom: 20px;
            }

            #session_rptg,
            #school_id,
            #dept_id,
            #courses_faculty_rpgt,
            #courses_rptg,
            #stu_enrollment_no {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
            }

            #example2 > tbody > tr:first-child + tr:not([id]) > td,
            #criteria_filters_tr > td {
                flex-direction: column;
                align-items: stretch;
            }

            #criteria_panel_tr {
                width: 100%;
                overflow-x: auto;
            }
        }

        @media (max-width: 600px) {
            @supports selector(:has(*)) {
                div:has(> #criteria_panel) > div:first-child > div:first-child {
                    font-size: 18px !important;
                }
            }

            #criteria_panel:nth-of-type(2) {
                padding: 18px 12px !important;
                border-radius: 10px;
            }

            #criterias_buttons_tr > td {
                align-items: stretch;
                flex-direction: column;
            }

            #criterias_buttons_tr .btn,
            #course_outline_url .btn,
            #fileDownload,
            #downloadZip,
            #criterias_buttons_tr .col-12,
            #course_outline_url {
                width: 100% !important;
                max-width: 100% !important;
            }

            #assessment_marks:not(:empty),
            #course_criteria:not(:empty) {
                overflow-x: auto;
            }

            #assessment_marks table,
            #course_criteria table {
                min-width: 720px;
            }
        }
        




























        /* Report Type radio buttons - Figma segmented style */

#criteria_panel_tr {
    display: flex;
    align-items: center;
    width: fit-content;
    border-radius: 10px;
    padding: 4px !important;
}

#criteria_panel_tr td {
    border: 0 !important;
    padding: 0 !important;
}

#criteria_panel_tr td:has(input[type="radio"]) {
    /* width: 0 !important; */
    /* min-width: 0 !important; */
    position: relative;
}

#criteria_panel_tr input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 120px;
    height: 36px;
    left: 0;
    top: 0;
    margin: 0;
    cursor: pointer;
    z-index: 5;
}

#criteria_panel_tr td:has(input[type="radio"]) + td {
    min-width: 120px;
    height: 36px;
    padding: 0 16px !important;
    border-radius: 8px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    background: transparent !important;
}

#criteria_panel_tr td:has(input[type="radio"]:checked) + td {
    background: #fff !important;
    color: #17675f !important;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.08);
}




/* =========================================================
   REPORT TYPE RADIO BUTTONS - VISIBLE FIGMA STYLE
   CSS ONLY - Logic / HTML / JS unchanged
   ========================================================= */

#criteria_panel_tr {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;

    width: max-content !important;
    max-width: 100% !important;

    padding: 30px 0 4px !important;

    background: transparent !important;
    border-radius: 0 !important;

    white-space: nowrap !important;
}


/* Radio containing TD */
#criteria_panel_tr td:has(.cirter_type) {
    position: static !important;

    width: 20px !important;
    min-width: 20px !important;
    height: 20px !important;

    padding: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    overflow: visible !important;
}


/* Actual visible radio */
#criteria_panel_tr .cirter_type {
    position: static !important;

    appearance: none !important;
    -webkit-appearance: none !important;

    opacity: 1 !important;

    width: 18px !important;
    height: 18px !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 2px solid #17675F !important;
    border-radius: 50% !important;

    cursor: pointer !important;

    transition: all .15s ease !important;
}


/* Selected radio */
#criteria_panel_tr .cirter_type:checked {
    background: #FFFFFF !important;

    border: 5px solid #17675F !important;
}


/* Keyboard focus */
#criteria_panel_tr .cirter_type:focus {
    outline: none !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, .12) !important;
}


/* Course Wise / Student Wise / Faculty Wise text */
#criteria_panel_tr td:has(.cirter_type) + td {
    min-width: auto !important;
    width: auto !important;
    height: auto !important;

    padding: 0 24px 0 3px !important;

    display: flex !important;
    align-items: center !important;

    background: transparent !important;

    border-radius: 0 !important;
    box-shadow: none !important;

    font-size: 14px !important;
    font-weight: 500 !important;

    color: #252525 !important;

    text-align: left !important;
}


/* Checked text - keep clean */
#criteria_panel_tr td:has(.cirter_type:checked) + td {
    background: transparent !important;

    color: #17675F !important;

    box-shadow: none !important;
}


/* Hover */
#criteria_panel_tr td:has(.cirter_type:hover) + td {
    color: #17675F !important;
}
    </style>
    <!-- Grid row -->
<?php
    $loginUser = Auth::user();
    
    $emp_user = $loginUser::find(Auth::User()->id)->employees;
   // print_r($emp_user->employee_id);
?>
<div  style="float: left; width:100%;border: solid 1px rgba(0, 0, 0, 0.125);">
    <div  style="background-color: #EEEFF0; float: left; width: 100%;">
        <div style="margin-left:10px; float:left; line-height: 40px;  color: #6A6B79; width: 240px;font-weight: bold">Report Generator</div>
        <div style="margin-right: 10px;  line-height: 40px;  float: right; width:200px; color: white; ">
            @php
               error_reporting(0);
            @endphp
        </div>
    </div>
    <div  style=" float: left; width: 100%;" id="criteria_panel">
        <table id="example2"  align="center" border="0"  style="width:40%;" class="table">
            <tr>
                <td colspan="6" align="center">
                    <select name="session_rptg" id="session_rptg" onclick="clearData()" class="form-control" style="width:100%;" >
                        <option value="">--Select Session--</option>
                            @foreach ($sessions as $key => $value)
                                @if($key==$_SESSION['active_session_id'])
                                    @php
                                        $sess_selected= 'selected="selected"';
                                    @endphp
                                @else
                                    @php
                                        $sess_selected= '';
                                    @endphp
                                @endif
                                <option value="{{ $key }}" {{$sess_selected}}>{{ $value }}</option>
                            @endforeach
                    </select>
                </td>
            </tr>
            @if((Auth::User()->group_id==201)or(Auth::User()->group_id==424))
            <tr>
                <td colspan="6" align="center">
                    <select class="form-control" id="school_id" name="school_id" style="width:49%;  float: left">
                        <option value="">--Select School--</option>
                        @foreach($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>

                    <select  class="form-control" id="dept_id" name="dept_id" style="width:50%; ">
                        <option value="">--Select Department--</option>
                        @foreach($departments as $row)
                            <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @endif
            <tr id="criteria_panel_tr">
                @if((Auth::User()->group_id==223)or(Auth::User()->group_id==364)or(Auth::User()->group_id==201) or(Auth::User()->group_id==424)or(Auth::User()->group_id==824))
                    <td align="right"><input type="radio" value="faculty" name="criter_rptg" id="criter_rptg" class="cirter_type"></td>
                    <td align="left">Faculty Wise</td>
                @endif
               @if((Auth::User()->group_id!=201)and(Auth::User()->group_id!=424))
                    <td align="right"><input type="radio" value="course" name="criter_rptg" id="criter_rptg"  class="cirter_type"></td>
                    <td align="left" style="">Course Wise</td>
                @endif
                    <td align="right"><input type="radio" value="student" name="criter_rptg" id="criter_rptg" class="cirter_type"></td>
                    <td align="left" style="">Student Wise</td>

<?php
 //getLoginUser('school_id');//F2020-140
?>
                <td>
                    <input type="text" class="form-control" name="emp_school_id" id="emp_school_id" value="<?=getLoginUser('school_id'); ?>" hidden />
                    @if($emp_user->emp_type==5)
                        <input type="text" class="form-control" name="emp_dept_id"   id="emp_dept_id" value="<?=$_SESSION['emp_dept_id'];?>" hidden />
                    @else
                        <input type="text" class="form-control" name="emp_dept_id"   id="emp_dept_id" value="" hidden />
                    @endif
                </td>
            </tr>
            <tr id="criteria_filters_tr" style="display:none;">
                <td colspan="6" align="center">
                    <select name="courses_faculty_rpgt" id="courses_faculty_rpgt" class="form-control" style="width:49%; display: none;float: left; ">
                        <option value="">--Select Faculty--</option>
                    </select>
                    <select name="courses_rptg" id="courses_rptg" class="form-control" style="width:50%; margin-left:2px; display: none; float: left;">
                        <option value="">--Select Course--</option>
                    </select>
                    <input type="text" name="stu_enrollment_no" id="stu_enrollment_no" placeholder="Enrollment No"  class="form-control" style="width:100%; display: none;" >
                </td>
            </tr>
            <tr id="criterias_buttons_tr" style="display: none;">
                <td colspan="6" align="center">
                    <button type="button" class="btn btn-primary button_size" id="course_marks">Marks Details</button>
                    <button type="button" class="btn btn-primary button_size" id="student_courses" style="width: 100%;!important;" >Student Courses</button>
                    <button type="button" class="btn btn-primary button_size" id="courseMarksSummaries">Marks Summary</button>
                    <button type="button" class="btn btn-primary button_size" id="courseCriteria">Course Criteria</button>
                    <button onclick="cOntent()" style="width: 100%;" type="button" class=" btn btn-secondary btn-md btn-block " id="courseContent"> Course Content</button>
                    <div id='course_outline_url'> </div>
                    <div class="col-12 col-md-12 col-xl-12 text-center pt-3 pb-3">
                        <button class="btn btn-outline-primary fileDownload" id="fileDownload" style="width: 100%; margin-top:10px;"
                                type="button"
                                data-course_criteria_detail_id=""
                                data-co_id=""
                                data-co_name="">
                          <i class="fa fa-folder" aria-hidden="true"></i>
                          Course Material
                        </button>
                        <a class="btn btn-outline-primary invisible downloadZip " id="downloadZip">
                          <i class="fa fa-download" aria-hidden="true"></i>
                          Start Download
                        </a>
                      </div>
                    <button type="button" class="btn btn-success button_size" id="print_marks_sheet">Print Report</button>
                </td>
            </tr>
        </table>
    </div>
    <div  style=" float:left; width: 100%" id="criteria_panel">
        <div id="assessment_marks"></div>
    </div>
    <div  style=" float: left; width:100%;" id="criteria_panel">
        <div id="course_criteria">  </div>
    </div>
</div>

<?php $_SESSION['active_session_id']=150?>
  @stop
@section('javascript')
<script type="text/javascript">

    function cOntent(){
        window.open("{{URL::to('course_content')}}/"+$('#courses_rptg').val());

    }

    $('#course_marks').click(function () {
       var courses_rptg = $("#courses_rptg").val();
        $.ajax({
            type:'GET',
            url:"{{ URL::route('load_marks') }}/"+courses_rptg,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           success:function(data){
              $("#assessment_marks").html(data);
              $("#course_criteria" ).empty();
            }
        });
    });
    $('#student_courses').click(function () {
        var get_enrollment_no = $("#stu_enrollment_no").val();
        var sess_id = $('#session_rptg').val();
        var school_combo_id = $('#school_id').val();
        if((school_combo_id!=null)){
            var dept_id = $('#dept_id').val();
            var school_id = school_combo_id;
        }else{
            var school_id = $('#emp_school_id').val();
            var dept_id =   $('#emp_dept_id').val();
        }
       $.ajax({
            type:'GET',
            url:"{{ URL::route('student_courses') }}/"+get_enrollment_no.toUpperCase() + "/" + sess_id + "/" + school_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data) {
                if (data) {
                    $("#assessment_marks").html(data);
                    $("#course_criteria").empty();
                } else {

                }
            }
        });
    });
    $('#courseCriteria').click(function () {
        var courses_rptg = $('#courses_rptg').val();
        $.ajax({
            type:'GET',
            url:"{{ URL::route('load_criteria') }}/"+courses_rptg,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                $("#assessment_marks" ).empty();
                $("#course_criteria").html(data);
            }
        });
    });
    $('#courseMarksSummaries').click(function () {
        var courses_rptg = $("#courses_rptg").val();
        $.ajax({
            type:'GET',
            url:"{{ URL::route('courseMarksSummaries') }}/"+courses_rptg,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                 $("#assessment_marks").html(data);
                 $("#course_criteria" ).empty();
            }
        });
    });
    function load_offered_course_faculty(get_sess_id){
        var school_combo_id = $('#school_id').val();
        var dept_combo_id = $('#dept_id').val();
        if((school_combo_id!=null)){
            var dept_id =   dept_combo_id;
            var school_id = school_combo_id;
        }else{
            var school_id = $('#emp_school_id').val();
            var dept_id =   $('#emp_dept_id').val();
        }
        $.ajax({
            url:"{{ URL::route('CoursesOffered/getOfferedCoursesFaculty') }}/"+ get_sess_id +"/" +school_id +"/"+ dept_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="courses_faculty_rpgt"]').empty();
                $('select[name="courses_faculty_rpgt"]').append('<option value="">--Select Faculty--</option>');
                $.each(data, function(key, value) {
                    $('select[name="courses_faculty_rpgt"]').append('<option  value="'+ data[key].fc_employee_id +'">'+ data[key].emp_full_name +'</option>');
                });

            }
        });
    }

    function clearData(){
        $("#courses_rptg").hide();
        $('#criter_rptg').prop('checked', false);
    }
    function load_self_courses(get_sess_id){
        var emp_school_id = $('#emp_school_id').val();
        var emp_dept_id = $('#emp_dept_id').val();
        var faculty_id = '<?= auth()->user()->employee_id?>';
        var get_sess_id = $('#session_rptg').val();
        
        $.ajax({
            url:"{{ URL::route('CoursesOffered/getCourses') }}/"+ get_sess_id +"/" + faculty_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="courses_rptg"]').empty();
                $('select[name="courses_rptg"]').append('<option value="">--Select Courses--</option>');
                $.each(data, function(key, value) {
                    $('select[name="courses_rptg"]').append('<option value="'+ data[key].fc_co_id +'">'+ data[key].course_full_name +'</option>');
                });
            }
        });
    }
    $('select[name="courses_faculty_rpgt"]').on('change', function() {
        var sess_id = $('#session_rptg').val();
        var school_combo_id = $('#school_id').val();
        var dept_combo_id = $('#dept_id').val();
        if((school_combo_id!=null)){
            var dept_id = dept_combo_id;
            var school_id = school_combo_id;
        }else{
            var school_id = $('#emp_school_id').val();
            var dept_id =   $('#emp_dept_id').val();
        }
        var faculty_id = $('#courses_faculty_rpgt').val();
        if((sess_id)&&(faculty_id)) {
            $.ajax({
                url:"{{ URL::route('CoursesOffered/getFacultyDepCourses') }}/"+ sess_id +"/" +  school_id + "/" + faculty_id + "/" + dept_id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    const $select = $('select[name="courses_rptg"]');
                        $select.empty();
                        $select.append('<option value="">--Select Courses--</option>');
                        $.each(data, function(key, value) {
                            const { course_code, course_cd_name, co_section, co_id, course_criteria_detail_id, co_course_cr, course_contents_url } = value;
                            const coName = `${course_code}-${course_cd_name}-${co_section}`;
                            const sanitizedCoName = coName.replace(/\s/g, '').replace(/[^a-zA-Z0-9]/g, '');
                            // const cleanedUrl = course_contents_url.replace(/\\/g, '').replace(/&export=media/g, '');
                            const cleanedUrl = course_contents_url 
                                            ? course_contents_url.replace(/\\/g, '').replace(/&export=media/g, '') 
                                            : null; 
                            // Append the option to the select element
                            $select.append(`
                                <option 
                                    value="${co_id}" 
                                    data-course_criteria_detail_id="${course_criteria_detail_id}" 
                                    data-co_id="${co_id}" 
                                    data-co_name="${sanitizedCoName}" 
                                    data-course_contents_url="${cleanedUrl}">
                                    ${course_code} - ${course_cd_name} | ${co_section} | ${co_course_cr}
                                </option>
                            `);
                        });
                }
            });
        }else{
            //$('select[name="courses"]').empty(); value="'+ data[key].emp_full_name +'"
            $('select[name="courses_rptg"]').empty();
            $('select[name="courses_rptg"]').append('<option value=""> No Course Found Courses--</option>');
        }

    });
    $('select[name="session_rptg"]').on('change', function() {
        $("#criteria_panel_tr").show();
    });
    $('select[name="school_id"]').on('change', function() {
        var school_id = $(this).val();
        if(school_id) {
            $.ajax({
                url: '{{url('departments/get/')}}/'+school_id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    $('select[name="dept_id"]').empty();
                    $('select[name="dept_id"]').append('<option value=""> </option>');
                    $.each(data, function(key, value) {
                        $('select[name="dept_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        }else{
            $('select[name="dept_id"]').empty();
        }
    });
    $('select[name="dept_id"]').on('change', function() {
        var sess_id = $('#session_rptg').val();
        var school_combo_id = $('#school_id').val();
        var dept_combo_id = $('#dept_id').val();
        if((school_combo_id!=null)){
            var dept_id =   dept_combo_id;
            var school_id = school_combo_id;
        }else{
            var school_id = $('#emp_school_id').val();
            var dept_id =   $('#emp_dept_id').val();
        }
        $.ajax({
            url:"{{ URL::route('CoursesOffered/getOfferedCoursesFaculty') }}/"+ sess_id +"/" +school_id +"/"+ dept_id,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $('select[name="courses_faculty_rpgt"]').empty();
                $('select[name="courses_faculty_rpgt"]').append('<option value="">--Select Faculty--</option>');
                $.each(data, function(key, value) {
                    $('select[name="courses_faculty_rpgt"]').append('<option  value="'+ data[key].fc_employee_id +'">'+ data[key].emp_full_name +'</option>');
                });

            }
        });
    });
    $('select[name="courses_rptg"]').on('change', function() {
        var co_id = $(this).val();
        const selectedOption = $('option:selected', this);
        var element = document.getElementById('fileDownload');


        var course_contents_url_div = document.getElementById('course_outline_url');
        var course_contents_url=selectedOption.data('course_contents_url');
        course_contents_url_div.innerHTML = '';
        if (course_contents_url) {
        var buttonHTML = `
            <button   style="width: 100%;" class="btn btn-primary mt-1 mb-1" onclick="window.location.href='${course_contents_url}'">
                <i class="fa fa-file-alt"></i> View Course Outline
            </button> `;
        course_contents_url_div.innerHTML = '';
        course_contents_url_div.innerHTML = buttonHTML;
    }



        element.setAttribute('data-course_criteria_detail_id', selectedOption.data('course_criteria_detail_id'));
        element.setAttribute('data-co_id', selectedOption.data('co_id'));
        element.setAttribute('data-co_name', selectedOption.data('co_name'));

        var enrollment_no = 'null';
        $("#criterias_buttons_tr").show();
        $("#courseCriteria").show();
        $("#course_marks").show();
        $("#courseMarksSummaries").show();
        $("#stu_enrollment_no").hide();
        $("#student_courses").hide();

        $.ajax({
            url:"{{ URL::route('load_student_percentage') }}/"+enrollment_no+"/"+co_id,
            type: "GET",
            success:function(data) {
                var get_size=parseInt(0);
                $.each(data, function(key, value) {
                    get_size = get_size+1;
                });
               // alert(get_size);
                if(get_size>0){
                    $("#courseMarksSummaries").prop("disabled",false);
                }else{
                    $("#courseMarksSummaries").prop("disabled",true);
                }

            }
        });

    });
    $(document).ready(function() {



        $('#fileDownload').click(function(e) {
    e.preventDefault();
    const button = $(this);
    // const row = button.closest('.col-6, .col-12, .col-10'); // Find the common parent element for the row
    const courseCriteriaDetailId = button.attr('data-course_criteria_detail_id');
    const coId = button.attr('data-co_id');
    const coName = button.attr('data-co_name');
    console.log(courseCriteriaDetailId, coId, coName);
   
    // Disable button and show spinner
    button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating Downloads...');

    const progressBar = $('<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">Please wait...</div></div>');

    button.after(progressBar);

    const downloadButton = $('#downloadZip');
    downloadButton.addClass('invisible');

    const formData = new FormData();
    formData.append('course_criteria_detail_id', courseCriteriaDetailId);
    formData.append('co_id', coId);
    formData.append('co_name', coName);

    $('#fileDownload').prop('disabled', true);

    $.ajax({
        method: "POST",
        url: "/api/download-assignment-file",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            // Add progress event listener to update progress bar
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    const percentComplete = evt.loaded / evt.total * 100;
                    progressBar.find('.progress-bar').css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        }
    })
    .done(function(res) {
        if (res.status) {
            // Remove progress bar and enable button
            progressBar.remove();
            button.removeClass('btn-outline-primary').addClass('btn-primary').prop('disabled', false).html('<i class="fa fa-folder" aria-hidden="true"></i> Generate Again');

            // Assuming `res` is your JSON response
            const downloadUrl = res.url.replace(/\\/g, '').replace(/\s/g, '%20');
            // console.log(downloadUrl);

            // Set the corrected URL to the download button
            downloadButton.attr('href', downloadUrl).removeClass('invisible');


        }
    })
    .fail(function(xhr) {
        if (xhr.status == 422) {
            // Remove progress bar and enable button
            progressBar.remove();
            button.prop('disabled', false).html('Generated Again').addClass('btn-danger');
        }
    })
    .always(function() {
        // Remove progress bar and enable button
        progressBar.remove();
        button.removeClass('btn-outline-primary').addClass('btn-primary').prop('disabled', false).html('<i class="fa fa-folder" aria-hidden="true"></i> Generate Again');

        $('#fileDownload').prop('disabled', false);
    });
});


        // //////////////////////////////////////////////////////////////////////
        $('.cirter_type').click(function() {
            var get_value = $(this).val();
            $("#assessment_marks").html('');
            $("#courses_faculty_rpgt").val('');
            $("#courses_rptg").val('');
            $("#course_criteria" ).html('');
            $("#stu_enrollment_no").val('');

            if(get_value=="faculty"){
                $("#courses_faculty_rpgt").val('');
                $("#criteria_filters_tr").show();
                $("#courses_rptg").show();
                $("#courses_faculty_rpgt").show();
                $("#courses_rptg").css({"width": "49%"});
                $("#stu_enrollment_no").hide();
                $("#student_courses").hide();
                var sess_id = $('#session_rptg').val();
                load_offered_course_faculty(sess_id);
            }else if(get_value=="course"){
                var sess_id = $('#session_rptg').val();
                load_self_courses(sess_id);
                $("#courseCriteria").hide();
                $("#criteria_filters_tr").show();
                $("#stu_enrollment_no").hide();
                $("#student_courses").hide();
                $("#courseMarksSummaries").show();
                $("#courses_rptg").show();
                $("#courses_faculty_rpgt").hide();
                $("#courses_rptg").css({"width": "100%"});
                $("#course_marks").hide();
                $("#courseMarksSummaries").hide();
            }else if(get_value=="student"){
                $("#courseCriteria").hide();
                $("#criterias_buttons_tr").show();
                $("#criteria_filters_tr").show();
                $("#stu_enrollment_no").show();
                $("#student_courses").show();
                $("#courses_rptg").hide();
                $("#courses_faculty_rpgt").hide();
                $("#course_marks").hide();
                $("#courseMarksSummaries").hide();
            }
        });
        $("#print_marks_sheet").hide();
        var table = $('#assessment_marks_table').DataTable();

         var table = $('#example').DataTable({
            'responsive': true
        });
        var table_summary = $('#assessment_marks_table_summary').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":true
        });



        
    });

    
</script>
@stop

