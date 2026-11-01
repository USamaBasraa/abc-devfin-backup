@extends('layouts.app', ['pageTitle' => 'BNU Calendar'])
{{-- @dd("123"); --}}
@section('content')
    <style>
        /* .fc-timeline-event-harness {
                        top: 0 !important;
                    }

                    .fc-resource-timeline table tbody tr .fc-datagrid-cell div {
                        height: 33px !important;
                    }

                    .fc .fc-timeline-overlap-enabled .fc-timeline-lane-frame .fc-timeline-events {
                        padding-bottom: 0 !important;
                        height: 33px !important;
                    }

                    .fc-timeline-event {
                        padding: 4px 1px !important;
                    } */
        .fc-month-button {
            text-transform: none !important;
        }
        .fc-agendaWeek-button {
            text-transform: none !important;
        }
        .fc-listMonth-button {
            text-transform: none !important;
        }












        /* =========================================================
   BNU CALENDAR — FIGMA STYLE OVERRIDE
   Figma visual source: Add Event To BNU Calendar (1971:4)

   IMPORTANT:
   - NOTHING HIDDEN
   - NOTHING REMOVED
   - Existing FullCalendar structure unchanged
   - Existing toolbar/buttons/icons/events unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. FIGMA DESIGN TOKENS
   ========================================================= */

:root {
    --calendar-primary: #17675F;
    --calendar-primary-dark: #0F766E;

    --calendar-page-bg: rgba(15, 118, 110, 0.05);

    --calendar-white: #FFFFFF;

    --calendar-heading: #0F172A;
    --calendar-text: #252525;
    --calendar-secondary: #64748B;
    --calendar-placeholder: #94A3B8;

    --calendar-border: #E2E5EA;
    --calendar-divider: #F1F3F5;

    --calendar-soft-green: rgba(23, 103, 95, 0.08);

    --calendar-radius: 12px;
    --calendar-control-radius: 8px;

    --calendar-shadow:
        0 4px 10px rgba(15, 23, 42, 0.02);
}


/* =========================================================
   2. PAGE / MAIN CARD
   ========================================================= */

/*
   Current Blade has one main .card on this screen.
   We retain it — only visual treatment changes.
*/

.col-lg-12 > .card {
    width: 100% !important;

    margin: 0 !important;

    background: var(--calendar-white) !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: var(--calendar-shadow) !important;

    overflow: visible !important;

    font-family: 'Inter', sans-serif !important;
}


/* =========================================================
   3. CARD HEADER
   Existing:
   Calendar
   + rounded-down
   + refresh
   + close icons

   ALL remain visible.
   ========================================================= */

.col-lg-12 > .card > .card-header {
    min-height: 68px !important;

    padding: 18px 24px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #F1F3F5 !important;

    border-radius: 12px 12px 0 0 !important;

    box-shadow: none !important;
}


/* Calendar heading */
.col-lg-12 > .card > .card-header .card-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 24px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    color: #0F172A !important;
}


/* =========================================================
   4. EXISTING RIGHT HEADER ICONS
   DO NOT HIDE
   ========================================================= */

.col-lg-12 > .card
> .card-header
.card-header-right {
    position: static !important;

    float: none !important;

    display: flex !important;
    align-items: center !important;

    gap: 8px !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* Each existing icon */
.col-lg-12 > .card
> .card-header
.card-header-right i {
    width: 34px !important;
    height: 34px !important;

    margin: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;

    border: 1px solid #E2E5EA !important;
    border-radius: 8px !important;

    font-size: 15px !important;
    line-height: 1 !important;

    color: #64748B !important;

    cursor: pointer;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease !important;
}


.col-lg-12 > .card
> .card-header
.card-header-right i:hover {
    background: rgba(23, 103, 95, .06) !important;

    border-color: rgba(23, 103, 95, .25) !important;

    color: #17675F !important;
}


/* =========================================================
   5. CARD BODY
   ========================================================= */

.col-lg-12 > .card > .card-body {
    padding: 24px !important;

    background: rgba(15, 118, 110, 0.05) !important;

    border-radius: 0 0 12px 12px !important;
}


/* =========================================================
   6. FULL CALENDAR MAIN CONTAINER
   ========================================================= */

#calendar111 {
    width: 100% !important;

    padding: 24px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: 0 4px 10px rgba(15, 23, 42, .02) !important;

    font-family: 'Inter', sans-serif !important;

    color: #0F172A !important;
}


/* =========================================================
   7. FULLCALENDAR TOOLBAR
   ========================================================= */

#calendar111 .fc-toolbar {
    min-height: 44px !important;

    margin: 0 0 20px !important;

    /* display: flex !important; */
    align-items: center !important;
    justify-content: space-between !important;

    gap: 12px !important;
}


/* Existing left / center / right areas stay visible */
#calendar111 .fc-toolbar .fc-left,
#calendar111 .fc-toolbar .fc-center,
#calendar111 .fc-toolbar .fc-right {
    /* display: flex !important; */

    /* align-items: center !important; */

    margin: 0 !important;
}


/* =========================================================
   8. CURRENT MONTH / CALENDAR TITLE
   ========================================================= */

#calendar111 .fc-toolbar h2 {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    color: #0F172A !important;

    text-transform: none !important;
}


/* =========================================================
   9. COMMON FULLCALENDAR BUTTON
   ========================================================= */

#calendar111 .fc-button,
#calendar111 .fc-state-default {
    min-height: 38px !important;

    margin: 0 !important;
    padding: 0 14px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #FFFFFF !important;
    background-image: none !important;

    border: 1px solid #E2E5EA !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    text-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    color: #64748B !important;

    text-transform: none !important;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease !important;
}


/* Remove FullCalendar connected square corners */
#calendar111 .fc-button-group > .fc-button {
    border-radius: 8px !important;

    margin-right: 6px !important;
}


#calendar111 .fc-button-group > .fc-button:last-child {
    margin-right: 0 !important;
}


/* =========================================================
   10. PREVIOUS / NEXT
   Existing arrow icons remain.
   ========================================================= */

#calendar111 .fc-prev-button,
#calendar111 .fc-next-button {
    width: 38px !important;
    min-width: 38px !important;

    padding: 0 !important;
}


#calendar111 .fc-icon {
    font-size: 14px !important;

    color: inherit !important;
}


/* =========================================================
   11. TODAY BUTTON
   ========================================================= */

#calendar111 .fc-today-button {
    margin-left: 4px !important;

    padding-left: 16px !important;
    padding-right: 16px !important;

    color: #17675F !important;

    border-color: rgba(23, 103, 95, .30) !important;
}


/* =========================================================
   12. VIEW BUTTONS
   Monthly View / Week / List of Events
   Nothing removed.
   ========================================================= */

#calendar111 .fc-month-button,
#calendar111 .fc-agendaWeek-button,
#calendar111 .fc-listMonth-button {
    min-height: 38px !important;

    padding-left: 16px !important;
    padding-right: 16px !important;

    text-transform: none !important;
}


/* Active view — Figma green */
#calendar111 .fc-state-active,
#calendar111 .fc-button.fc-state-active {
    background: #17675F !important;
    background-image: none !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;

    box-shadow: none !important;
}


/* Hover */
#calendar111 .fc-button:hover,
#calendar111 .fc-state-default:hover {
    background: rgba(23, 103, 95, .06) !important;

    border-color: rgba(23, 103, 95, .30) !important;

    color: #17675F !important;
}


/* Active hover remains green */
#calendar111 .fc-state-active:hover {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;
}


/* Disabled button remains visible */
#calendar111 .fc-state-disabled {
    opacity: .45 !important;

    background: #F8FAFC !important;

    border-color: #E2E5EA !important;

    color: #94A3B8 !important;

    cursor: not-allowed !important;
}


/* =========================================================
   13. MONTH VIEW OUTER BORDER
   ========================================================= */

#calendar111 .fc-view-container {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 1px solid #E2E5EA !important;
    border-radius: 10px !important;
}


/* =========================================================
   14. TABLE BASE
   ========================================================= */

#calendar111 table {
    font-family: 'Inter', sans-serif !important;

    border-collapse: collapse !important;
}


/* =========================================================
   15. WEEK DAY HEADER
   ========================================================= */

#calendar111 .fc-head {
    background: rgba(23, 103, 95, .08) !important;
}


#calendar111 .fc-day-header {
    height: 44px !important;

    padding: 12px 8px !important;

    vertical-align: middle !important;

    background: rgba(23, 103, 95, .08) !important;

    border-color: #E2E5EA !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #17675F !important;

    text-transform: uppercase !important;

    letter-spacing: .02em !important;
}


/* Nav links in weekday headings */
#calendar111 .fc-day-header a {
    color: #17675F !important;

    text-decoration: none !important;
}


/* =========================================================
   16. CALENDAR GRID CELLS
   ========================================================= */

#calendar111 .fc-day,
#calendar111 .fc-day-top,
#calendar111 .fc-bg td,
#calendar111 .fc-content-skeleton td {
    border-color: #E2E5EA !important;
}


/* Normal day */
#calendar111 .fc-day {
    background: #FFFFFF !important;
}


/* Hover day area */
#calendar111 .fc-day:hover {
    background: rgba(23, 103, 95, .02) !important;
}


/* =========================================================
   17. DAY NUMBER
   ========================================================= */

#calendar111 .fc-day-number {
    padding: 8px 9px !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #334155 !important;

    text-decoration: none !important;
}


#calendar111 .fc-day-number:hover {
    color: #17675F !important;

    text-decoration: none !important;
}


/* =========================================================
   18. TODAY
   Keep today clearly visible.
   ========================================================= */

#calendar111 .fc-today {
    background: rgba(23, 103, 95, .07) !important;
}


#calendar111 .fc-today .fc-day-number {
    width: 28px !important;
    height: 28px !important;

    margin: 5px !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #17675F !important;

    border-radius: 50% !important;

    color: #FFFFFF !important;

    font-weight: 600 !important;
}


/* =========================================================
   19. OTHER MONTH DAYS
   Keep visible — just muted.
   ========================================================= */

#calendar111 .fc-other-month {
    background: #F8FAFC !important;
}


#calendar111 .fc-other-month .fc-day-number {
    color: #94A3B8 !important;

    opacity: 1 !important;
}


/* =========================================================
   20. EVENTS
   Existing dynamic event colors are NOT overwritten.

   Your JS assigns event-specific colors.
   Keep those backgrounds visible.
   ========================================================= */

#calendar111 .fc-event {
    min-height: 24px !important;

    margin: 2px 4px !important;
    padding: 4px 7px !important;

    border-width: 0 !important;
    border-radius: 6px !important;

    box-shadow: none !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 11px !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;

    cursor: default;
}


/* Do NOT force event background color;
   JS-generated event color remains intact. */
#calendar111 .fc-event,
#calendar111 .fc-event .fc-content,
#calendar111 .fc-event .fc-title,
#calendar111 .fc-event .fc-time {
    color: #0F172A !important;
}


/* Event title wrapping */
/* #calendar111 .fc-title {
    white-space: normal !important;

    overflow-wrap: anywhere !important;
} */


/* =========================================================
   21. "+ MORE" EVENTS
   ========================================================= */

#calendar111 .fc-more {
    display: inline-block !important;

    margin: 3px 5px !important;
    padding: 3px 7px !important;

    background: rgba(23, 103, 95, .08) !important;

    border-radius: 5px !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    color: #17675F !important;

    text-decoration: none !important;
}


/* =========================================================
   22. POPOVER
   When clicking "+ more"
   ========================================================= */

#calendar111 .fc-popover {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 1px solid #E2E5EA !important;
    border-radius: 10px !important;

    box-shadow:
        0 12px 30px rgba(15, 23, 42, .10) !important;
}


#calendar111 .fc-popover .fc-header {
    padding: 10px 12px !important;

    background: rgba(23, 103, 95, .08) !important;

    color: #0F172A !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 600 !important;
}


/* Existing close icon remains visible */
#calendar111 .fc-popover .fc-close {
    color: #64748B !important;
}


/* =========================================================
   23. WEEK VIEW
   Existing Week view remains available.
   ========================================================= */

#calendar111 .fc-agenda-view .fc-axis {
    padding: 8px !important;

    background: #F8FAFC !important;

    border-color: #E2E5EA !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #64748B !important;
}


#calendar111 .fc-agenda-view .fc-day-header {
    background: rgba(23, 103, 95, .08) !important;
}


#calendar111 .fc-agenda-view .fc-time-grid,
#calendar111 .fc-agenda-view .fc-time-grid td {
    border-color: #E2E5EA !important;
}


/* =========================================================
   24. LIST OF EVENTS VIEW
   Existing List of Events remains visible.
   ========================================================= */

#calendar111 .fc-list-view {
    border: 0 !important;

    background: #FFFFFF !important;
}


/* List date heading */
#calendar111 .fc-list-heading td {
    padding: 11px 14px !important;

    background: rgba(23, 103, 95, .08) !important;

    border-color: #E2E5EA !important;
}


#calendar111 .fc-list-heading-main,
#calendar111 .fc-list-heading-alt {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 600 !important;

    color: #17675F !important;

    text-decoration: none !important;
}


/* List item */
#calendar111 .fc-list-item td {
    padding-top: 12px !important;
    padding-bottom: 12px !important;

    border-color: #F1F3F5 !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 13px !important;

    color: #0F172A !important;
}


#calendar111 .fc-list-item:hover td {
    background: rgba(23, 103, 95, .025) !important;
}


/* Existing event color dot remains visible */
#calendar111 .fc-event-dot {
    border-width: 5px !important;
}


/* =========================================================
   25. "NO EVENTS" MESSAGE
   ========================================================= */

#calendar111 .fc-list-empty {
    padding: 45px 20px !important;

    background: #FFFFFF !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 14px !important;

    color: #94A3B8 !important;

    text-align: center !important;
}


/* =========================================================
   26. SCROLL AREA
   ========================================================= */

#calendar111 .fc-scroller {
    scrollbar-width: thin;

    scrollbar-color:
        rgba(100, 116, 139, .35)
        transparent;
}


#calendar111 .fc-scroller::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
}


#calendar111 .fc-scroller::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, .35) !important;

    border-radius: 50px !important;
}


#calendar111 .fc-scroller::-webkit-scrollbar-track {
    background: transparent !important;
}


/* =========================================================
   27. LINKS
   navLinks:true remains fully functional
   ========================================================= */

#calendar111 a {
    transition: color .15s ease !important;
}


#calendar111 a:hover {
    text-decoration: none !important;
}


/* =========================================================
   28. FOCUS STATES
   ========================================================= */

#calendar111 .fc-button:focus {
    outline: 0 !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, .10) !important;
}


/* =========================================================
   29. TABLE BORDER CLEANUP
   ========================================================= */

#calendar111 .fc-widget-header,
#calendar111 .fc-widget-content {
    border-color: #E2E5EA !important;
}


/* =========================================================
   30. RESPONSIVE
   Nothing hidden on mobile.
   Toolbar simply wraps.
   ========================================================= */

@media (max-width: 991.98px) {

    #calendar111 .fc-toolbar {
        flex-wrap: wrap !important;

        align-items: center !important;

        gap: 12px !important;
    }


    #calendar111 .fc-toolbar .fc-left {
        order: 1;
    }


    #calendar111 .fc-toolbar .fc-center {
        order: 2;

        flex: 1 1 auto !important;

        justify-content: center !important;
    }


    #calendar111 .fc-toolbar .fc-right {
        order: 3;

        width: 100% !important;

        justify-content: flex-start !important;

        flex-wrap: wrap !important;
    }

}


/* =========================================================
   31. MOBILE
   Again: no controls hidden.
   ========================================================= */

@media (max-width: 767.98px) {

    .col-lg-12 > .card > .card-header {
        padding: 16px !important;

        align-items: flex-start !important;

        gap: 12px !important;

        flex-wrap: wrap !important;
    }


    .col-lg-12 > .card > .card-header .card-title {
        font-size: 20px !important;
    }


    .col-lg-12 > .card > .card-body {
        padding: 12px !important;
    }


    #calendar111 {
        padding: 14px !important;

        overflow-x: auto !important;
    }


    #calendar111 .fc-toolbar {
        display: flex !important;

        flex-direction: column !important;

        align-items: stretch !important;
    }


    #calendar111 .fc-toolbar .fc-left,
    #calendar111 .fc-toolbar .fc-center,
    #calendar111 .fc-toolbar .fc-right {
        width: 100% !important;

        justify-content: center !important;

        flex-wrap: wrap !important;
    }


    #calendar111 .fc-toolbar h2 {
        padding: 7px 0 !important;

        font-size: 18px !important;

        text-align: center !important;
    }


    #calendar111 .fc-month-button,
    #calendar111 .fc-agendaWeek-button,
    #calendar111 .fc-listMonth-button {
        flex: 1 1 auto !important;
    }

}









/* =========================================================
   FIX: FULLCALENDAR "+ MORE" POPOVER
   Keep this AFTER all previous calendar CSS
   ========================================================= */


/* IMPORTANT:
   FullCalendar more-events popup must be allowed
   to overflow outside the calendar row/view.
*/
#calendar111 .fc-view-container {
    overflow: visible !important;
    position: relative !important;
}


/* Month/day-grid containers must not clip the popup */
#calendar111 .fc-month-view,
#calendar111 .fc-basic-view,
#calendar111 .fc-day-grid,
#calendar111 .fc-day-grid-container {
    overflow: visible !important;
}


/* Calendar rows must allow the +more popup to extend */
/* #calendar111 .fc-row {
    overflow: visible !important;
} */


/* Keep content layers from clipping popup */
#calendar111 .fc-content-skeleton,
#calendar111 .fc-content-skeleton table,
#calendar111 .fc-content-skeleton tbody,
#calendar111 .fc-content-skeleton td {
    overflow: visible !important;
}


/* "+4 more", "+7 more", etc. remains clickable */
#calendar111 .fc-more {
    position: relative !important;

    z-index: 20 !important;

    pointer-events: auto !important;
    cursor: pointer !important;

    display: inline-block !important;

    margin: 3px 5px !important;
    padding: 3px 7px !important;

    background: rgba(23, 103, 95, 0.08) !important;

    border-radius: 5px !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    color: #17675F !important;

    text-decoration: none !important;
}


/* Actual popup */
#calendar111 .fc-popover,
#calendar111 .fc-more-popover {
    position: absolute !important;

    z-index: 99999 !important;

    min-width: 250px !important;
    max-width: 340px !important;

    background: #FFFFFF !important;

    border: 1px solid #E2E5EA !important;
    border-radius: 8px !important;

    box-shadow:
        0 10px 30px rgba(15, 23, 42, 0.16) !important;
}


/* Popup title/header */
#calendar111 .fc-popover .fc-header,
#calendar111 .fc-more-popover .fc-header {
    min-height: 38px !important;

    padding: 9px 10px !important;

    display: flex !important;
    align-items: center !important;

    background: #F8FAFC !important;

    border: 0 !important;
    border-bottom: 1px solid #E2E5EA !important;

    font-family: 'Inter', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #45556C !important;
}


/* Existing popup close X stays visible */
#calendar111 .fc-popover .fc-close,
#calendar111 .fc-more-popover .fc-close {
    margin-left: auto !important;

    cursor: pointer !important;

    color: #64748B !important;

    opacity: 1 !important;
}


/* Popup event area */
#calendar111 .fc-popover .fc-body,
#calendar111 .fc-more-popover .fc-body {
    max-height: 360px !important;

    padding: 5px !important;

    overflow-x: hidden !important;
    overflow-y: auto !important;

    background: #FFFFFF !important;
}


/* Events inside popup */
#calendar111 .fc-popover .fc-event,
#calendar111 .fc-more-popover .fc-event {
    margin: 2px 0 !important;
    padding: 5px 8px !important;

    min-height: 28px !important;

    border-radius: 4px !important;

    font-size: 12px !important;
    line-height: 1.3 !important;

    cursor: default !important;
}


/*
   Do NOT set popup event background color here.
   Your existing JS assigns dynamic colors to each event.
*/


/* Make sure calendar card itself does not clip popup */
.col-lg-12 > .card,
.col-lg-12 > .card > .card-body,
#calendar111 {
    overflow: visible !important;
}
.fc-day-number, .fc-time, .fc-title, .fc-widget-content  {
    font-family: 'Montserrat', sans-serif !important;
}
    </style>
    <link href="{{ asset('plugins/fullcalendar/fullcalendar.css') }}" />
    <link href="{{ asset('plugins/fullcalendar/fullcalendar.print.min.css') }}" />

<style>
    .fc-day-number, .fc-time, .fc-title, .fc-widget-content, #calendar111 .fc-list-item td  {
    font-family: 'Montserrat', sans-serif !important;
}



</style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Calendar</h5>
                    <div class="card-header-right">
                        {{-- <i class="icofont icofont-rounded-down"></i>
                        <i class="icofont icofont-refresh"></i>
                        <i class="icofont icofont-close-circled"></i> --}}
                    </div>
                </div>
                <div class="card-body">

                    <div id='calendar111'></div>

                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script src='https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js'></script>

    <script src="{{ asset('plugins/fullcalendar/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/app-calendar-events.js') }}"></script>
    <script src="{{ asset('js/app-calendar.js') }}"></script>
    <script type="text/javascript">
      $(document).ready(function () {
    $('#calendar111').fullCalendar({
        header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,listMonth'
    },
    eventRender: function (event, element) {
        element.attr('title', event.title); // Set the title attribute for tooltip
    }, // Add the missing comma here
    views: {
        agendaWeek: {
            type: 'agenda',
            duration: {
                days: 7
            },
            buttonText: 'Week'
        }
    },
    buttonText: {
    today: 'today',
    // month: 'Month',
    month: 'Monthly View',
    // week: 'week',
    week: 'Weekly View',
    // list: 'list'
    list: 'List of Events'
},
    noEventsMessage: "No Events to display",
    eventConstraint: {
        start: '7:00',
        end: '00:00'
    },
    minTime: '08:00',
    height: 650,
    defaultView: 'month',
    defaultDate: moment().format("YYYY-MM-DD"),
    navLinks: true,
    editable: false,
    eventLimit: true
});

    var colorArr = [
        '#c6e2ff', '#d3ffce', '#b0e0e6', '#eeeeee', '#5eabed',
        '#81BEF7', '#FFAB91', '#FFCC80', '#FFE082', '#C5E1A5',
        '#C8FE2E', '#FAAC58', '#ffa500', '#6a5acd', '#3cb371',
    ];
    var colorsObjs = {};

    setTimeout(() => {
        var url = '<?php echo url('api/UniversityEvent/event-list/'); ?>';

        $.ajax({
            url: url,
            type: "GET",
            headers: {
                'Authorization': 'Bearer {{ auth()->user()->api_token }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                console.log('Response from server:', res);

                if (Array.isArray(res)) {
                    var eventArr = [];

                    res.forEach(function (obj) {
                        var startDate = moment(obj.event_date.replace(" 00:00:00", ""), 'YYYY-MM-DD');
                        var endDate = obj.to_event_date 
                            ? moment(obj.to_event_date.replace(" 00:00:00", ""), 'YYYY-MM-DD')
                            : startDate.clone();

                        var from_str = obj.start_time_slot;
                        var to_str = obj.end_time_slot;

                        // Loop from event_date → to_event_date
                        for (var m = startDate.clone(); m.isSameOrBefore(endDate, 'day'); m.add(1, 'days')) {
                            var from_date = moment(m.format('YYYY-MM-DD') + ' ' + from_str, 'YYYY-MM-DD hh:mm a').format();
                            var to_date = moment(m.format('YYYY-MM-DD') + ' ' + to_str, 'YYYY-MM-DD hh:mm a').format();

                            var color;
                            if (colorsObjs[obj.id] !== undefined) {
                                color = colorsObjs[obj.id];
                            } else {
                                var length = Object.keys(colorsObjs).length;
                                color = colorArr[length % colorArr.length];
                                colorsObjs[obj.id] = color;
                            }

                            var eventNode = {
                                <?php if(auth()->user()->username == 'naveed' || auth()->user()->username == 'asif' || auth()->user()->username == 'nasir.iqbal' || auth()->user()->username == 'umar.shahid' || auth()->user()->username == 'safdar.hussain' || auth()->user()->username == 'faisal.shahzad' || auth()->user()->username == 'm.siddiq' || auth()->user()->username == 'muhammad.akhtar' || auth()->user()->username == 'muhammad.arslan'|| auth()->user()->username == 'zeeshan.mahmood' || auth()->user()->username == 'mrashid' || auth()->user()->username == 'muhammad.noman' || auth()->user()->username == 'salman' || auth()->user()->username == 'hammad.iqbal'){?>
                                    title: `${obj.event_title} | Event Venue: ${obj.room_name} | Scheduled by: ${obj.uv_entry_user} | IT Details: ${(obj.itrc_details != null ? obj.itrc_details : 'N/A')}`,
                                <?php }else{ ?>
                                    title: `${obj.event_title} | Event Venue: ${obj.room_name} | Scheduled by: ${obj.uv_entry_user}`,
                                <?php } ?>
                                description: obj.event_description,
                                start: from_date,
                                end: to_date,
                                color: color,
                                user_id: obj.uv_entry_user,
                                eventTextColor: "black",
                                textColor: "black"
                            };

                            eventArr.push(eventNode);
                        }
                    });

                    console.log('Expanded Events to be added:', eventArr);
                    $('#calendar111').fullCalendar('addEventSource', eventArr);
                } else {
                    console.error('Invalid response format:', res);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching events:', error);
            }
        });
    }, 500);
});
    </script>
@stop
