@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("12"); --}}
<style>
#calendar .fc th, .fc td{

    line-height: 1.5 !important;
}

/* ============================================================
   FIGMA FACULTY CMS - FACULTY TIMETABLE VISUAL OVERRIDES
   References:
   - Faculty Timetable (Month) 2712:4
   - Faculty Timetable (Week)  2717:674
   - Faculty Timetable (List)  2735:1095
   Styling only. Existing Blade/PHP/HTML/FullCalendar/AJAX logic unchanged.
   ============================================================ */

:root {
    --tt-primary: #17675F;
    --tt-primary-hover: #12564F;
    --tt-page-bg: rgba(15, 118, 110, 0.05);
    --tt-heading: #1E293B;
    --tt-body: #334155;
    --tt-muted: #64748B;
    --tt-soft-muted: #94A3B8;
    --tt-border: #E2E8F0;
    --tt-soft: #F8FAFC;
    --tt-weekend: #FAFBFC;
    --tt-control: #F1F5F9;
    --tt-event: #EEF2FF;
    --tt-event-green: #F0FDF4;
    --tt-event-orange: #FFF7ED;
    --tt-event-purple: #F5F3FF;
}

/* Shared type */
#week,
.page-body,
#calendar111 {
    font-family: 'Montserrat', Arial, sans-serif !important;
    color: var(--tt-heading) !important;
}

/* ============================================================
   PAGE TITLE + WEEKLY SCHEDULE CARD
   ============================================================ */

#week.card {
    margin: 0 0 32px 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

#week > .card-header {
    min-height: auto !important;
    margin: 0 !important;
    padding: 0 0 20px 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

#week > .card-header strong {
    color: var(--tt-heading) !important;
    font-size: 22px !important;
    line-height: 1.25 !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
}

#week > .card-body {
    position: relative !important;
    display: block !important;
    padding: 24px !important;
    border: 1px solid var(--tt-border) !important;
    border-radius: 12px !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.02) !important;
}

/* Figma weekly-card subheading without changing Blade markup */
#week > .card-body::before {
    content: 'Weekly Schedule' !important;
    display: block !important;
    margin: 0 0 20px 0 !important;
    color: var(--tt-heading) !important;
    font-size: 14px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
}

#week > .card-body > .col-lg-12,
#week .card-block.table-border-style {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

#week .table-responsive {
    width: 100% !important;
    margin: 0 !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    border: 1px solid var(--tt-border) !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
}

#week table.table {
    width: 100% !important;
    min-width: 760px !important;
    margin: 0 !important;
    border: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    background: #FFFFFF !important;
}

#week table.table thead,
#week table.table thead tr {
    background: var(--tt-primary) !important;
}

#week table.table thead th {
    height: 38px !important;
    padding: 12px 20px !important;
    border: 0 !important;
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
    text-align: left !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    font-size: 11px !important;
    line-height: 1.25 !important;
    font-weight: 600 !important;
    letter-spacing: .01em !important;
}

#week table.table thead th:first-child {
    width: 200px !important;
    border-top-left-radius: 7px !important;
}

#week table.table thead th:last-child {
    border-top-right-radius: 7px !important;
}

#week table.table tr {
    background: #FFFFFF !important;
}

#week table.table td {
    padding: 16px 20px !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid var(--tt-border) !important;
    background: #FFFFFF !important;
    color: var(--tt-body) !important;
    vertical-align: middle !important;
    font-size: 12px !important;
    line-height: 1.45 !important;
    font-weight: 500 !important;
}

#week table.table tr:last-child td {
    border-bottom: 0 !important;
}

#week table.table td:first-child {
    width: 200px !important;
    color: var(--tt-heading) !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}

/* Subject cells read like the soft Figma slot cards */
#week table.table td:not(:first-child):not([style*="background-color:lightblue"]) {
    color: var(--tt-primary) !important;
    background: var(--tt-soft) !important;
    background-clip: padding-box !important;
    border-left: 8px solid #FFFFFF !important;
    border-top: 10px solid #FFFFFF !important;
    border-bottom: 10px solid #FFFFFF !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
}

/* Legacy spacer cells remain in DOM but are visually neutralized */
#week table.table td[style*="background-color:lightblue"] {
    width: 8px !important;
    min-width: 8px !important;
    padding: 0 !important;
    border: 0 !important;
    background: #FFFFFF !important;
    font-size: 0 !important;
}

#week h5[align="center"] {
    margin: 10px 0 !important;
    padding: 22px !important;
    color: var(--tt-muted) !important;
    text-align: center !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}

/* ============================================================
   CALENDAR CARD
   ============================================================ */

.page-body {
    margin: 0 !important;
    padding: 0 !important;
}

.page-body > .card {
    margin: 0 !important;
    padding: 0 !important;
    border: 1px solid var(--tt-border) !important;
    border-radius: 12px !important;
    background: #FFFFFF !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.02) !important;
    overflow: hidden !important;
}

.page-body > .card > .card-header {
    min-height: auto !important;
    padding: 24px 24px 0 24px !important;
    border: 0 !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
}

.page-body > .card > .card-header h5 {
    margin: 0 !important;
    color: var(--tt-heading) !important;
    font-size: 14px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
}

/* Figma list-view title while preserving original heading node/text */
.page-body > .card:has(#calendar111 .fc-list-view) > .card-header h5 {
    font-size: 0 !important;
}

.page-body > .card:has(#calendar111 .fc-list-view) > .card-header h5::after {
    content: 'Timetable List' !important;
    color: var(--tt-heading) !important;
    font-size: 14px !important;
    line-height: 1.3 !important;
    font-weight: 600 !important;
}

.page-body > .card > .card-header .card-header-right {
    display: none !important;
}

.page-body > .card > .card-block {
    padding: 24px !important;
    background: #FFFFFF !important;
}

.page-body > .card > .card-block .row,
.page-body > .card > .card-block .col-xl-12,
.page-body > .card > .card-block .col-md-12 {
    margin: 0 !important;
    padding: 0 !important;
}

#calendar111 {
    width: 100% !important;
}

/* ============================================================
   FULLCALENDAR TOOLBAR / CONTROLS
   ============================================================ */

#calendar111 .fc-toolbar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 18px !important;
    min-height: 32px !important;
    /* margin: 0 0 24px 0 !important; */
}

#calendar111 .fc-toolbar .fc-left,
#calendar111 .fc-toolbar .fc-center,
#calendar111 .fc-toolbar .fc-right {
    float: none !important;
    display: flex !important;
    align-items: center !important;
}

#calendar111 .fc-toolbar .fc-left {
    gap: 12px !important;
}

#calendar111 .fc-toolbar .fc-center {
    flex: 1 1 auto !important;
    justify-content: center !important;
}

#calendar111 .fc-toolbar .fc-right {
    justify-content: flex-end !important;
}

#calendar111 .fc-toolbar h2 {
    margin: 0 !important;
    color: var(--tt-heading) !important;
    font-size: 16px !important;
    line-height: 1.2 !important;
    font-weight: 600 !important;
}

#calendar111 .fc-button {
    height: 28px !important;
    min-height: 28px !important;
    margin: 0 !important;
    padding: 0 10px !important;
    border: 0 !important;
    outline: 0 !important;
    background: transparent !important;
    background-image: none !important;
    color: var(--tt-muted) !important;
    text-shadow: none !important;
    box-shadow: none !important;
    font-family: 'Montserrat', Arial, sans-serif !important;
    font-size: 12px !important;
    line-height: 28px !important;
    font-weight: 500 !important;
}

#calendar111 .fc-button:focus,
#calendar111 .fc-button:hover {
    outline: 0 !important;
    box-shadow: none !important;
}

#calendar111 .fc-left .fc-button-group {
    display: inline-flex !important;
    gap: 4px !important;
    padding: 3px !important;
    border-radius: 8px !important;
    background: var(--tt-control) !important;
}

#calendar111 .fc-prev-button,
#calendar111 .fc-next-button {
    width: 26px !important;
    min-width: 26px !important;
    height: 26px !important;
    padding: 0 !important;
    border: 1px solid var(--tt-border) !important;
    border-radius: 6px !important;
    background: #FFFFFF !important;
    color: var(--tt-heading) !important;
    line-height: 24px !important;
}

#calendar111 .fc-prev-button:hover,
#calendar111 .fc-next-button:hover {
    border-color: rgba(23, 103, 95, .35) !important;
    color: var(--tt-primary) !important;
}

#calendar111 .fc-today-button {
    padding: 0 !important;
    background: transparent !important;
    color: var(--tt-primary) !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    opacity: 1 !important;
}

#calendar111 .fc-today-button.fc-state-disabled {
    opacity: .55 !important;
}

#calendar111 .fc-right .fc-button-group {
    display: inline-flex !important;
    gap: 6px !important;
    padding: 3px !important;
    border-radius: 50px !important;
    background: var(--tt-control) !important;
}

#calendar111 .fc-right .fc-button {
    min-width: 58px !important;
    height: 26px !important;
    padding: 0 16px !important;
    border-radius: 50px !important;
    color: var(--tt-muted) !important;
    line-height: 26px !important;
}

#calendar111 .fc-right .fc-button:hover {
    color: var(--tt-primary) !important;
}

#calendar111 .fc-right .fc-button.fc-state-active,
#calendar111 .fc-right .fc-button.fc-state-down {
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
}

/* ============================================================
   SHARED CALENDAR GRID
   ============================================================ */

#calendar111 .fc-view-container {
    border: 1px solid var(--tt-border) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: #FFFFFF !important;
}

#calendar111 .fc-view,
#calendar111 .fc-view > table {
    border: 0 !important;
}

#calendar111 table,
#calendar111 th,
#calendar111 td {
    border-color: var(--tt-border) !important;
    font-family: 'Montserrat', Arial, sans-serif !important;
}

#calendar111 th.fc-day-header {
    padding: 12px 6px !important;
    border-color: var(--tt-border) !important;
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
    text-transform: uppercase !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
    font-weight: 600 !important;
}

#calendar111 th.fc-day-header a,
#calendar111 th.fc-day-header span {
    color: #FFFFFF !important;
    text-decoration: none !important;
}
.fc-day-header {
    height: 0;
}

/* ============================================================
   MONTH VIEW
   ============================================================ */

#calendar111 .fc-month-view .fc-day {
    background: #FFFFFF !important;
}

#calendar111 .fc-month-view tbody tr td:first-child.fc-day,
#calendar111 .fc-month-view tbody tr td:last-child.fc-day {
    background: var(--tt-weekend) !important;
}

#calendar111 .fc-month-view .fc-day-top {
    padding: 7px 8px 0 !important;
}

#calendar111 .fc-month-view .fc-day-number {
    min-width: 24px !important;
    min-height: 24px !important;
    padding: 5px 6px !important;
    color: var(--tt-heading) !important;
    text-decoration: none !important;
    font-size: 12px !important;
    line-height: 14px !important;
    font-weight: 500 !important;
}

#calendar111 .fc-month-view .fc-other-month .fc-day-number {
    color: var(--tt-soft-muted) !important;
    opacity: 1 !important;
}

#calendar111 .fc-month-view td.fc-today {
    background: #FFFFFF !important;
}

#calendar111 .fc-month-view .fc-today .fc-day-number {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    padding: 0 !important;
    border-radius: 50% !important;
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
    font-weight: 700 !important;
}

#calendar111 .fc-month-view .fc-event-container {
    padding: 0 6px 5px !important;
}

#calendar111 .fc-month-view .fc-event {
    min-height: 24px !important;
    padding: 4px 6px !important;
    border: 0 !important;
    border-radius: 4px !important;
    background: var(--tt-event) !important;
    color: var(--tt-primary) !important;
    box-shadow: none !important;
    font-size: 9px !important;
    line-height: 1.25 !important;
    font-weight: 600 !important;
}

#calendar111 .fc-month-view .fc-event .fc-content,
#calendar111 .fc-month-view .fc-event .fc-title,
#calendar111 .fc-month-view .fc-event .fc-time {
    color: inherit !important;
}

#calendar111 .fc-month-view .fc-more {
    color: var(--tt-primary) !important;
    font-size: 10px !important;
    font-weight: 600 !important;
}

/* ============================================================
   WEEK / AGENDA VIEW
   ============================================================ */

#calendar111 .fc-agendaWeek-view .fc-axis,
#calendar111 .fc-agenda-view .fc-axis {
    width: 70px !important;
    color: var(--tt-soft-muted) !important;
    text-align: right !important;
    font-size: 10px !important;
    font-weight: 600 !important;
}

#calendar111 .fc-agendaWeek-view thead .fc-axis,
#calendar111 .fc-agenda-view thead .fc-axis {
    border-color: var(--tt-border) !important;
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid .fc-slats td,
#calendar111 .fc-agenda-view .fc-time-grid .fc-slats td {
    height: 60px !important;
    border-color: var(--tt-border) !important;
    background: #FFFFFF !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid .fc-slats .fc-minor td,
#calendar111 .fc-agenda-view .fc-time-grid .fc-slats .fc-minor td {
    border-top-style: solid !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid-event,
#calendar111 .fc-agenda-view .fc-time-grid-event {
    margin: 2px 4px !important;
    padding: 8px !important;
    border: 0 !important;
    border-radius: 6px !important;
    background: var(--tt-event) !important;
    color: var(--tt-primary) !important;
    box-shadow: none !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid-event .fc-content,
#calendar111 .fc-agenda-view .fc-time-grid-event .fc-content {
    color: inherit !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid-event .fc-title,
#calendar111 .fc-agenda-view .fc-time-grid-event .fc-title {
    color: inherit !important;
    font-size: 10px !important;
    line-height: 1.3 !important;
    font-weight: 700 !important;
}

#calendar111 .fc-agendaWeek-view .fc-time-grid-event .fc-time,
#calendar111 .fc-agenda-view .fc-time-grid-event .fc-time {
    color: inherit !important;
    opacity: .82 !important;
    font-size: 9px !important;
    line-height: 1.25 !important;
    font-weight: 500 !important;
}

#calendar111 .fc-agendaWeek-view .fc-now-indicator-line,
#calendar111 .fc-agenda-view .fc-now-indicator-line {
    border-color: var(--tt-primary) !important;
}

/* ============================================================
   LIST VIEW
   ============================================================ */

#calendar111 .fc-list-view {
    border: 0 !important;
    background: #FFFFFF !important;
}

#calendar111 .fc-list-table {
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

#calendar111 .fc-list-heading td {
    padding: 12px 20px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--tt-border) !important;
    background: var(--tt-primary) !important;
    color: #FFFFFF !important;
    font-size: 13px !important;
    line-height: 1.3 !important;
    font-weight: 700 !important;
}

#calendar111 .fc-list-heading-main,
#calendar111 .fc-list-heading-alt {
    color: #FFFFFF !important;
    text-decoration: none !important;
}

#calendar111 .fc-list-item:hover td {
    background: rgba(23, 103, 95, .025) !important;
}

#calendar111 .fc-list-item td {
    padding: 16px 20px !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid var(--tt-border) !important;
    background: #FFFFFF !important;
    vertical-align: middle !important;
}

#calendar111 .fc-list-item-time {
    width: 180px !important;
    color: var(--tt-heading) !important;
    white-space: nowrap !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}

#calendar111 .fc-list-item-marker {
    width: 24px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#calendar111 .fc-list-item-marker .fc-event-dot {
    display: block !important;
    width: 4px !important;
    height: 48px !important;
    margin: 0 auto !important;
    border: 0 !important;
    border-radius: 2px !important;
    background: rgba(23, 103, 95, .75) !important;
}

#calendar111 .fc-list-item-title a {
    color: var(--tt-heading) !important;
    text-decoration: none !important;
    font-size: 14px !important;
    line-height: 1.35 !important;
    font-weight: 700 !important;
}

#calendar111 .fc-list-empty-wrap2 {
    background: #FFFFFF !important;
}

#calendar111 .fc-list-empty {
    color: var(--tt-muted) !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 991.98px) {
    #week > .card-header strong {
        font-size: 20px !important;
    }

    #week > .card-body,
    .page-body > .card > .card-block {
        padding: 18px !important;
    }

    .page-body > .card > .card-header {
        padding: 18px 18px 0 18px !important;
    }

    #calendar111 .fc-toolbar {
        flex-wrap: wrap !important;
        align-items: center !important;
    }

    #calendar111 .fc-toolbar .fc-left,
    #calendar111 .fc-toolbar .fc-center,
    #calendar111 .fc-toolbar .fc-right {
        flex: 1 1 auto !important;
    }

    #calendar111 .fc-toolbar .fc-center {
        order: -1 !important;
        flex-basis: 100% !important;
        margin-bottom: 8px !important;
    }

    #calendar111 .fc-right .fc-button {
        min-width: 52px !important;
        padding: 0 12px !important;
    }
}

@media (max-width: 575.98px) {
    #week > .card-body,
    .page-body > .card > .card-block {
        padding: 14px !important;
    }

    .page-body > .card > .card-header {
        padding: 14px 14px 0 14px !important;
    }

    #calendar111 .fc-toolbar .fc-left,
    #calendar111 .fc-toolbar .fc-right {
        flex-basis: 100% !important;
        justify-content: space-between !important;
    }

    #calendar111 .fc-list-item td {
        padding: 12px 10px !important;
    }

    #calendar111 .fc-list-item-time {
        width: 118px !important;
        font-size: 11px !important;
    }
}

/* ================= END FIGMA TIMETABLE OVERRIDES ================= */
</style>

<!-- Fullcalendar css-->
     <link href="{{ asset('plugins/fullcalendar/fullcalendar.css') }}"  />
		<link href="{{ asset('plugins/fullcalendar/fullcalendar.print.min.css') }}" />

<div class="card" id="week">
  <div class="card-header">
    <strong>Faculty Timetable</strong>
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


          <tr>
            <?php
            $count = 0;
            $sub_date=''; 
            $i=1;
            $background_colors = array('steelblue','orange','green','brown');
            
            foreach ($weekly as $rec) {

              if($rec->tt_subject_day !=$sub_date){
                $sub_date = $rec->tt_subject_day;
                //echo $background_colors[$num];
               
            ?>
              <td align="left" ;>
                <?= $rec->tt_subject_day; ?><br>
               
              </td>
              
              @foreach ($weekly as $key) 
              <?php
              $date= $key->tt_subject_day;
  
              if( $sub_date == $key->tt_subject_day  ){ 
                $rand_background = $background_colors[array_rand($background_colors)];
                ?>
                <td >  {{$key->tt_subject_name}} <br>
                (<?= $key->tt_subject_time_from . ' - ' . $key->tt_subject_time_to; ?>)
                </td><td style="background-color:lightblue">
             <?php 
             $i++;
            
            }
             
             ?>
              @endforeach
              </td>
          </tr>
        <?php }
             } 
        
            }else{ 
              echo '<h5 align="center" >Yet No Time Table has been created for this course </h5>'; 
             }?>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<style>
#calendar .fc th, .fc td{

line-height: 1.5 !important;
}












/* =========================================
   WEEKLY SCHEDULE - HORIZONTAL SCROLL
   Logic / HTML structure unchanged
   ========================================= */

/* #week .table-responsive {
    width: 100% !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    -webkit-overflow-scrolling: touch;
} */

/* Table ko cells squeeze karne se roko */
/* #week .table-responsive > table {
    width: max-content !important;
    min-width: 100% !important;
    table-layout: auto !important;
} */

/* Day column */
#week .table-responsive table tr td:first-child,
#week .table-responsive table thead th:first-child {
    min-width: 200px !important;
    width: 200px !important;
}

/* Har lecture ko readable width */
#week .table-responsive table tr td:not(:first-child) {
    min-width: 280px !important;
    width: 280px !important;
}

/* Text readable rahe */
#week .table-responsive td {
    white-space: normal !important;
}





/* Subject cards - Figma width */
#week .table-responsive table tr td:not(:first-child):not([style*="background-color:lightblue"]) {
    min-width: 380px !important;
    width: 380px !important;
}

/* Spacer should remain very small */
#week .table-responsive table tr td[style*="background-color:lightblue"] {
    min-width: 8px !important;
    width: 8px !important;
    max-width: 8px !important;
    padding: 0 !important;
    border: 0 !important;
    background: #FFFFFF !important;
    flex: 0 0 8px !important;
}



/* =================================================
   FULLCALENDAR TOOLBAR EXACT FIGMA ALIGNMENT
   Left   = Prev / Next / Today
   Center = Month + Year
   Right  = Month / Week / List
   ================================================= */

#calendar111 .fc-toolbar {
    display: grid !important;
    grid-template-columns: 1fr auto 1fr !important;
    align-items: center !important;
    width: 100% !important;
    gap: 20px !important;
}

/* LEFT */
#calendar111 .fc-toolbar .fc-left {
    grid-column: 1 !important;
    grid-row: 1 !important;

    display: flex !important;
    align-items: center !important;
    justify-self: start !important;

    width: auto !important;
    flex: none !important;
    float: none !important;
}

/* CENTER */
#calendar111 .fc-toolbar .fc-center {
    grid-column: 2 !important;
    grid-row: 1 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    justify-self: center !important;

    width: auto !important;
    flex: none !important;
    float: none !important;

    margin: 0 !important;
}

/* RIGHT */
#calendar111 .fc-toolbar .fc-right {
    grid-column: 3 !important;
    grid-row: 1 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    justify-self: end !important;

    width: auto !important;
    flex: none !important;
    float: none !important;
}
.fc-toolbar.fc-header-toolbar {
  margin-bottom: 0 !important;
}
.fc-icon-left-single-arrow:after {
    content: "\02039";
    font-weight: 700;
    font-size: 200%;
    top: -7%;
    right: 7px;
}
.fc-icon-right-single-arrow:after {
    content: "\0203A";
    font-weight: 700;
    font-size: 200%;
    top: -7%;
    right: 7px;
}




/* Outer container ko scroll karne se roko */
#week .table-responsive {
    overflow-x: hidden !important;
}

/* Table structure */
#week .table-responsive > table {
    display: block !important;
    width: 100% !important;
    min-width: 0 !important;
}

/* Header fixed and full width */
#week .table-responsive table thead {
    display: block !important;
    width: 100% !important;
}

#week .table-responsive table thead tr {
    display: flex !important;
    width: 100% !important;
}

/* Fixed Day heading */
#week .table-responsive table thead th:first-child {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
}

/* Take all remaining header space */
#week .table-responsive table thead th:last-child {
    flex: 1 1 auto !important;
    width: auto !important;
    min-width: 0 !important;
    max-width: none !important;
}

/* Only table body will scroll */
#week .table-responsive table tbody {
    display: block !important;
    width: 100% !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    -webkit-overflow-scrolling: touch;
}

/* Keep body rows wide enough for all lectures */
#week .table-responsive table tbody tr {
    display: flex !important;
    width: max-content !important;
    min-width: 100% !important;
    align-items: stretch !important;
}

/* Preserve fixed Day column width */
#week .table-responsive table tbody td:first-child {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
}
</style>
<div class="page-body">
                                    <div class="card">
                    <div class="card-header">
                        <h5>Timetable</h5>
                        <div class="card-header-right">
                            <i class="icofont icofont-rounded-down"></i>
                            <i class="icofont icofont-refresh"></i>
                            <i class="icofont icofont-close-circled"></i>
                        </div>
                    </div>
                    <div class="card-block" style="padding: 25px;">
                        <div class="row">
                            <div class="col-xl-12 col-md-12">
                                <div id='calendar111'></div>
                            </div>
                        </div>
                    </div>
                </div>
 </div>
@stop

@section('javascript')
<!-- Full-calendar js-->
<script src="{{ asset('plugins/fullcalendar/moment.min.js') }}"></script>
		<script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
		<script src="{{ asset('js/app-calendar-events.js') }}"></script>
		<script src="{{ asset('js/app-calendar.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#calendar111').fullCalendar({
            header: {left: 'prev,next today',center: 'title',
              right: 'month,agendaWeek,listMonth'
            },
            views: {agendaFourDay: {type: 'agenda',duration: { days: 14 },buttonText: '14 day'}},
            buttonText:{
              today:    'Today',
                month:    'Month',
                week:     'Week',
             //   day:      'Day',
                list:     'List'
            },
            noEventsMessage:"No Lectures to display",
            eventConstraint : {start : '7:00',end : '12:00'},
            minTime:'8:00',
            height: 650,
            allDay:true,
          //  color: 'black',   // an option!
            textColor: 'black', // an option!
            defaultView: 'month',
            defaultDate: moment().format("YYYY-MM-DD"),
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true // allow "more" link when too many events
        });
            var colorArr    = [
                '#c6e2ff','#d3ffce','#b0e0e6','#eeeeee','#5eabed',
                '#81BEF7','#FFAB91','#FFCC80','#FFE082','#C5E1A5',
                '#C8FE2E','#FAAC58','#ffa500','#6a5acd','#3cb371',
            ];
            var eventArr    = [];
            var colorsObjs  = {};
            var data        = {session :'', course:''};

           
            setTimeout(() => {

                <?php $url = url("api/timetable/faculty?facultyID=".strtoupper(auth()->user()->id)); ?>
                $.ajax({
                url:'<?=$url?>',
                type: "GET",
                headers: {
                    'Authorization' : 'Bearer {{ auth()->user()->api_token }}',
                    'Accept': 'application/json'
                },
                success: function (res) {

                    console.log('from server', res);

                    var eventArr = [];
                    $(res).each(function (index,obj) {


                //      console.log('single data :: ',obj);

                        var date_str    = obj.tt_class_date.replace(" 00:00:00", ""); 
                        var from_str    = obj.start_time.replace(" 00:00:00", "");
                        var to_str      = obj.end_time.replace(" 00:00:00", "");
                       
                        var from_date   =  moment(date_str+' '+from_str, 'YYYY-MM-DD hh:mm a').format();
                        var to_date     =  moment(date_str+' '+to_str, 'YYYY-MM-DD hh:mm a').format();
                        var color;
                        // console.log(from_date+' 222'+to_date);
                        if (colorsObjs[obj.co_course_id] != null){
                            color = colorsObjs[obj.co_course_id];
                        }else{
                            var lenght  = Object.keys(colorsObjs).length;
                            var color   = colorArr[lenght+1];
                            colorsObjs[obj.co_course_id] = color;
                        }
                        var eventNode = {
                            title:  obj.course_name+' | '+obj.teacher_name,
                            start:from_date,
                            end:to_date,
                            color:color,
                            eventTextColor:"black",
                            textColor:"black"
                        };
                      // console.log(eventNode);
                        eventArr.push(eventNode);
                    });
                    console.log('data objects',eventArr);
                    $('#calendar111').fullCalendar('addEventSource', eventArr);
                }
            });
            }, 500);

});
</script>
@endsection