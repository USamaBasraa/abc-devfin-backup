<?php

// print_r($car_pulling);
// exit();
?>


@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
{{-- @dd("!23"); --}}
<style>
     

    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
}
    div.dataTables_wrapper div.dataTables_filter input {
        width: 450px;
        height: 40px;
    }
}
    tr:nth-child(even) {
        background-color: #dddddd;
    }


    /* ============================================================
       FIGMA FACULTY CMS - CAR POOLING / RIDE REQUEST VISUAL OVERRIDES
       Reference: Faculty CMS Design / Search Ride (node 453:1250)
       Presentation only: Blade/PHP conditions, URLs, IDs, AJAX and JS unchanged.
       ============================================================ */

    :root {
        --faculty-primary: #17675F;
        --faculty-primary-hover: #12564F;
        --faculty-primary-soft: rgba(23, 103, 95, 0.10);
        --faculty-primary-muted: rgba(23, 103, 95, 0.50);
        --faculty-text: #252525;
        --faculty-muted: #949494;
        --faculty-line: #E3E9E8;
        --faculty-white: #FFFFFF;
        --faculty-danger: #C84E4E;
        --faculty-warning: #C98A2E;
    }

    /* Root Car Pooling card */
    .card > .card-header.bg-primary {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 0 14px 0 !important;
        min-height: auto !important;
        color: var(--faculty-text) !important;
    }

    .card > .card-header.bg-primary .card-title,
    .card > .card-header.bg-primary .card-title.text-white {
        margin: 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 20px !important;
        line-height: 1.35 !important;
        font-weight: 600 !important;
        letter-spacing: 0 !important;
    }

    .card > .card-block {
        padding: 0 !important;
        background: transparent !important;
    }

    .card > .card-block > .tab-menu-heading {
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        color: var(--faculty-text) !important;
    }

    /* Figma pill tabs and underline rail */
    .card > .card-block .panel-tabs {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        min-height: 66px !important;
        margin: 0 0 20px 0 !important;
        padding: 0 0 15px 0 !important;
        list-style: none !important;
        border: 0 !important;
        border-bottom: 2px solid rgba(23, 103, 95, 0.13) !important;
    }

    .card > .card-block .panel-tabs > li {
        position: relative !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 50px !important;
        background: transparent !important;
    }

    .card > .card-block .panel-tabs > li > a {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 150px !important;
        height: 50px !important;
        padding: 0 22px !important;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary-muted) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 15px !important;
        line-height: 50px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        box-shadow: none !important;
        transition: background .18s ease, color .18s ease !important;
    }

    .card > .card-block .panel-tabs > li > a i {
        margin-right: 8px !important;
        color: inherit !important;
    }

    .card > .card-block .panel-tabs > li > a.active,
    .card > .card-block .panel-tabs > li > a.active.text-white {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block .panel-tabs > li > a.active::after {
        content: '' !important;
        position: absolute !important;
        left: 0 !important;
        bottom: -17px !important;
        width: 100% !important;
        height: 3px !important;
        border-radius: 3px !important;
        background: var(--faculty-primary) !important;
    }

    .card > .card-block .panel-tabs > li > a:not(.active):hover {
        background: rgba(23, 103, 95, 0.15) !important;
        color: var(--faculty-primary) !important;
    }

    .card > .card-block .tab-menu-heading > hr {
        display: none !important;
    }

    /* Details title */
    .card > .card-block .tab-menu-heading > h4 {
        margin: 0 0 14px 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 16px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    /* DataTables controls */
    .card > .card-block #simpletable_wrapper {
        width: 100% !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    .card > .card-block #simpletable_wrapper > .row:first-child {
        margin-left: 0 !important;
        margin-right: 0 !important;
        align-items: center !important;
    }

    .card > .card-block #simpletable_wrapper > .row:first-child > div {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .card > .card-block #simpletable_filter {
        width: 100% !important;
        margin: 0 0 14px 0 !important;
        text-align: left !important;
    }

    .card > .card-block #simpletable_filter label {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        color: transparent !important;
        font-size: 0 !important;
    }

    .card > .card-block #simpletable_filter input {
        width: 100% !important;
        height: 50px !important;
        margin: 0 !important;
        padding: 0 16px !important;
        border: 1px solid rgba(23, 103, 95, 0.08) !important;
        border-radius: 5px !important;
        outline: 0 !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        font-weight: 400 !important;
    }

    .card > .card-block #simpletable_filter input:focus {
        border-color: rgba(23, 103, 95, 0.35) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.07) !important;
    }

    .card > .card-block #simpletable_length {
        margin-bottom: 12px !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 13px !important;
    }

    .card > .card-block #simpletable_length select {
        min-width: 82px !important;
        height: 32px !important;
        border: 1px solid var(--faculty-text) !important;
        border-radius: 50px !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-size: 13px !important;
    }

    /* Table surface */
.card > .card-block .dt-responsive {
    width: 100% !important;
    overflow: visible !important;
    border-radius: 5px !important;
}

/* Sirf table horizontally scroll hogi */
.card > .card-block .dataTables_scrollBody {
    overflow-x: auto !important;
    overflow-y: hidden !important;
}

/* Header table ke sath aligned rahe */
.card > .card-block .dataTables_scrollHead {
    overflow: hidden !important;
}

    .card > .card-block #simpletable {
        width: 100% !important;
        margin: 0 !important;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    .card > .card-block #simpletable thead th {
        /* height: 54px !important; */
        padding: 13px 20px !important;
        border: 0 !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
    }

    .card > .card-block #simpletable thead th:first-child {
        border-top-left-radius: 5px !important;
    }

    .card > .card-block #simpletable thead th:last-child {
        border-top-right-radius: 5px !important;
    }

    .card > .card-block #simpletable tbody tr,
    .card > .card-block #simpletable tbody tr:nth-child(even),
    .card > .card-block #simpletable tbody tr:nth-child(odd) {
        background: #FFFFFF !important;
    }

    .card > .card-block #simpletable tbody td {
        min-height: 52px !important;
        padding: 12px 20px !important;
        border-top: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-bottom: 1px solid var(--faculty-line) !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        vertical-align: middle !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 13px !important;
        line-height: 1.45 !important;
        font-weight: 400 !important;
    }

    .card > .card-block #simpletable tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .card > .card-block #simpletable tbody tr:hover td {
        background: rgba(23, 103, 95, 0.035) !important;
    }

    /* Action buttons */
    .card > .card-block #simpletable .btn {
        min-height: 40px !important;
        padding: 9px 16px !important;
        margin: 2px 4px 2px 0 !important;
        border-radius: 5px !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 12px !important;
        line-height: 1.2 !important;
        font-weight: 500 !important;
        transition: background .18s ease, border-color .18s ease, color .18s ease !important;
    }

    .card > .card-block #simpletable .btn-primary {
        border-color: var(--faculty-primary) !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block #simpletable .btn-primary:hover {
        border-color: var(--faculty-primary-hover) !important;
        background: var(--faculty-primary-hover) !important;
    }

    .card > .card-block #simpletable .btn-danger {
        border-color: rgba(200, 78, 78, 0.24) !important;
        background: rgba(200, 78, 78, 0.08) !important;
        color: var(--faculty-danger) !important;
    }

    .card > .card-block #simpletable .btn-danger:hover {
        border-color: var(--faculty-danger) !important;
        background: var(--faculty-danger) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block #simpletable .btn-warning {
        border-color: rgba(201, 138, 46, 0.22) !important;
        background: rgba(201, 138, 46, 0.10) !important;
        color: #9A651D !important;
    }

    /* DataTables footer / pagination */
    .card > .card-block #simpletable_wrapper > .row:last-child {
        margin: 14px 0 0 0 !important;
        align-items: center !important;
    }

    .card > .card-block #simpletable_info {
        padding-top: 0 !important;
        color: var(--faculty-text) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 12px !important;
    }

    .card > .card-block #simpletable_paginate {
        padding-top: 0 !important;
    }

    .card > .card-block #simpletable_paginate .pagination {
        gap: 4px !important;
        margin: 0 !important;
        justify-content: flex-end !important;
    }

    .card > .card-block #simpletable_paginate .page-link {
        min-width: 28px !important;
        height: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 7px !important;
        border: 0 !important;
        border-radius: 50px !important;
        background: transparent !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 12px !important;
    }

    .card > .card-block #simpletable_paginate .page-item.active .page-link {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    .card > .card-block #simpletable_paginate .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--faculty-primary-soft) !important;
        color: var(--faculty-primary) !important;
    }

    /* Existing chat modal restyled to the Faculty CMS palette */
    #large-Modal_chat .modal-content.chat {
        overflow: hidden !important;
        border: 1px solid rgba(23, 103, 95, 0.10) !important;
        border-radius: 10px !important;
        background: #FFFFFF !important;
        box-shadow: 0 18px 55px rgba(37, 37, 37, 0.14) !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
    }

    #large-Modal_chat .action-header {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        border: 0 !important;
    }

    #large-Modal_chat .msg_card_body,
    #large-Modal_chat .widget-chat-box,
    #large-Modal_chat .widget-chat-box .card-block {
        background: #F8FBFA !important;
    }

    #large-Modal_chat #message {
        min-height: 42px !important;
        border: 1px solid var(--faculty-line) !important;
        border-radius: 5px 0 0 5px !important;
        background: #FFFFFF !important;
        color: var(--faculty-text) !important;
        box-shadow: none !important;
        font-family: 'Montserrat', Arial, sans-serif !important;
        font-size: 13px !important;
    }

    #large-Modal_chat #messageBtn {
        min-width: 46px !important;
        border: 0 !important;
        border-radius: 0 5px 5px 0 !important;
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
        box-shadow: none !important;
    }

    #large-Modal_chat .msg_cotainer.bg-primary {
        background: var(--faculty-primary) !important;
        color: #FFFFFF !important;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .card > .card-block .panel-tabs {
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
        }

        .card > .card-block .panel-tabs > li > a {
            min-width: 138px !important;
            height: 44px !important;
            padding: 0 18px !important;
            line-height: 44px !important;
            font-size: 13px !important;
        }

        .card > .card-block #simpletable thead th,
        .card > .card-block #simpletable tbody td {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    }

/* Visible DataTables header */
.card > .card-block .dataTables_scrollHead thead th {
    padding: 13px 20px !important;
    background: var(--faculty-primary) !important;
    color: #fff !important;
    border: 0 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
}

/* Original sizing header ko visible na hone dein */
.card > .card-block .dataTables_scrollBody #simpletable thead th {
    height: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    border: 0 !important;
    line-height: 0 !important;
}


































/* =========================================
   DATATABLE SEARCH BAR - FINAL STYLE
   ========================================= */

.card > .card-block #simpletable_filter {
    width: 100% !important;
    margin: 0 0 14px 0 !important;

    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;

    text-align: right !important;
}

.card > .card-block #simpletable_filter label {
    position: relative !important;

    display: inline-block !important;
    width: 320px !important;

    margin: 0 !important;

    font-size: 0 !important;
    color: transparent !important;
}

.card > .card-block #simpletable_filter input {
    width: 320px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 42px 0 14px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    outline: none !important;
    box-shadow: none !important;

    font-family: 'Montserrat', Arial, sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

/* Search icon */
.card > .card-block #simpletable_filter label::after {
    content: "\f002";

    position: absolute !important;
    right: 14px !important;
    top: 50% !important;

    transform: translateY(-50%) !important;

    font-family: FontAwesome !important;
    font-size: 14px !important;

    color: #252525 !important;

    pointer-events: none !important;
}

/* Focus */
.card > .card-block #simpletable_filter input:focus {
    border-color: #17675F !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}

/* Mobile */
@media (max-width: 767.98px) {

    .card > .card-block #simpletable_filter label,
    .card > .card-block #simpletable_filter input {
        width: 100% !important;
    }
}


/* Show entries dropdown arrow */
.card > .card-block #simpletable_length select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;

    padding: 0 30px 0 12px !important;

    background-color: #ffffff !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7' fill='none'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23252525' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 10px center !important;
    background-size: 12px 7px !important;
}

/* Show entries left and Search complete right */
.card > .card-block #simpletable_wrapper > .row:first-child {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    margin-right: 0 !important;
    margin-left: 0 !important;
}

.card > .card-block
#simpletable_wrapper > .row:first-child > div:last-child {
    display: flex !important;
    justify-content: flex-end !important;
    flex: 1 1 auto !important;
    width: auto !important;
    max-width: none !important;
    margin-left: auto !important;
    padding-right: 0 !important;
}

.card > .card-block #simpletable_filter {
    float: none !important;
    width: auto !important;
    margin: 0 0 14px auto !important;
}

.card > .card-block #simpletable_filter label {
    display: flex !important;
    align-items: center !important;
    width: auto !important;
    gap: 8px;

    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
}

.card > .card-block #simpletable_filter input {
    width: 250px !important;
    height: 40px !important;
    margin: 0 !important;
    padding: 8px 14px !important;

    border: 1px solid #dbe3ea !important;
    border-radius: 5px !important;
    background: #ffffff !important;

    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
}

/* Remove existing search icon */
.card > .card-block #simpletable_filter label::after {
    display: none !important;
    content: none !important;
}
/* Keep DataTable headings in Title Case */
#simpletable thead th,
#simpletable_wrapper .dataTables_scrollHead thead th {
    text-transform: none !important;
}
</style>
    <div class="card" style="background: none !important; background-color:none !important;">
        <div class="card-header bg-primary"
            style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
            <div class="card-title text-white">
                Car Pooling Search
            </div>
        </div>
        <div class="card-block">
            <div class="tab-menu-heading">
                <div class="tabs-menu ">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs">
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-details') }}" ><i
                                    class="fa fa-car fa-lg mr-2"></i>Ride Search</a>
                        </li>
                        <li class="mr-2 p-2 rounded"> <a href="{{ url('/car-pooling-inbox') }}" class="active text-white"><i
                                    class="fa fa-inbox fa-lg mr-1"></i>Ride Request </a>
                        </li>
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-chat') }}"><i
                            class="fa fa-commenting-o fa-lg mr-2"></i>ChatBox
                        
                        </a>
                </li>
                    </ul>
                </div>

                
                <hr>
                <h4>Details</h4>
                <div class="dt-responsive ">
                    <table id="simpletable" class="table table-striped table-bordered dt-responsive">
                        <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0">Username</th>
                                <!-- <th class="wd-25p border-bottom-0">Conversation</th> -->
                                <!-- <th class="wd-15p border-bottom-0">Ride Type</th> -->
                                <!-- <th class="wd-15p border-bottom-0">Vehicle Capacity</th> -->
                                <!-- <th class="wd-15p border-bottom-0">Preferred Locations</th> -->
                                <th class="wd-15p border-bottom-0">Action</th>
                            </tr>
                        </thead>
                        <!-- <tbody> -->
                            @foreach ($car_pulling as $item)
                            <tr>
                                <td>{{ $item->cp_partner }}</td>
                                <input type="hidden" id="userId"
                                    value="{{ $item->entry_user_id }}">
                                <!-- <td>

                                    <a class="option-dots new-list2" href="#"
                                        data-toggle="modal"
                                        onclick="getUsername('<?= $item->entry_user_id ?>','<?= $item->cp_id ?>')"
                                        data-target="#large-Modal_chat"><svg
                                            class="svg-icon" style="scale:1.3;"
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="24" viewBox="0 0 24 24"
                                            width="24">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path
                                                d="M20 4H4v13.17L5.17 16H20V4zm-6 10H6v-2h8v2zm4-3H6V9h12v2zm0-3H6V6h12v2z"
                                                opacity=".3" />
                                            <path
                                                d="M20 18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14zm-16-.83V4h16v12H5.17L4 17.17zM6 12h8v2H6zm0-3h12v2H6zm0-3h12v2H6z" />
                                        </svg></a>

                                </td> -->
                                <!-- <td>{{ $item->cp_want_ride }}</td> -->
                                <!-- <td>
                                    {{ $item->cp_capacity }}

                                </td> -->
                                <!-- <td>{{ $item->cp_preferred_location }}</td> -->
                                <?php
                                $display = '';
                                $show_cap = '';
                                $textNocap = 'Accept Request';
                                if (strpos($item->cp_partner, $item->entry_user_id) !== false) {
                                    //echo "Found!";
                                    $display = 'disabled';
                                    $textNocap = 'Accepted';
                                }
                                if ($item->cp_capacity == '0') {
                                    $display = 'disabled';
                                    $textNocap = 'Capacity Full';
                                }
                                if ($item->cp_want_ride == 'BE_SAHRED') {
                                   // $display = 'disabled';
                                    $textNocap = 'Cancel My Request';
                                }
        
                                ?>
                                <td> 
                                @if ( $item->cp_want_ride == 'PENDING'  && $item->remaining_capacity>0)
                                    @if(strtolower($item->entry_user_id) != strtolower(auth()->user()->username))
                                        <button type="button" class="btn btn-danger"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Cancel by Owner','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Cancel Request</button><br>
                                    @else
                                    <button type="button" class="btn btn-primary"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Accept','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Accept Request</button> &nbsp;
                                    <button type="button" class="btn btn-danger"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Cancel by Owner','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Cancel Request</button><br>
                                    @endif
                                @elseif ( $item->cp_want_ride == 'PENDING' && $item->remaining_capacity==0)
                                    @if(strtolower($item->entry_user_id) != strtolower(auth()->user()->username))
                                        <button type="button" class="btn btn-danger"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Cancel by Owner','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Cancel Request</button><br>
                                    @else
                                    <button type="button" class="btn btn-warning">Capacity Full</button> &nbsp;
                                    <button type="button" class="btn btn-danger"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Cancel by Owner','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Cancel Request</button><br>
                                    @endif
                                @else
                                <button type="button" class="btn btn-danger"
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Remove by Owner','<?= $item->parent_cp_id?>','<?= $item->cp_partner ?>')">Remove Request</button><br>
   
                                @endif

                                    <!-- <?php
                                     if (strpos($item->cp_partner, $item->entry_user_id) !== false) {
                                        //echo "Found!";
                                        $display = 'disabled';
                                        $textNocap = 'Accepted';
                                    ?>
                                    <button type="button" class="btn btn-primary">Accepted</button>
                                    <button type="button" class="btn btn-danger"
                                        
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','Cancel by Owner')">Cancel Request</button><br>

                                    <span id="successMsg<?= $item->entry_user_id ?>"></span>
                                    4242
                                    <?php
                                    }else{   
                                    ?>   asdfadf                
                                <button type="button" class="btn btn-primary"
                                        <?= $display ?>
                                        onclick="acceptRide('<?= $item->cp_id ?>','<?= $item->cp_capacity ?>','<?= $item->entry_user_id ?>','<?= auth()->user()->username ?>','<?=$textNocap;?>','<?= $item->parent_cp_id?>')"><?= $textNocap ?></button><br>

                                    <span id="successMsg<?= $item->entry_user_id ?>"></span>
                                    <?php
                                      }
                                    ?> -->
                                </td>
                            </tr>
                        @endforeach
                        <!-- </tbody> -->
                    </table>
                </div>

            </div>
        </div>

        <!-- Modal large-->
        <div class="modal fade" id="large-Modal_chat" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right chatbox" role="document">
                <div class="modal-content chat border-0">
                    <div class="card overflow-hidden mb-0 border-0">
                        <!-- action-header -->
                        <div class="action-header">
                            <div class="float-left hidden-xs d-flex ml-2">
                                <div class="img_cont mr-3">
                                    <img src="../../assets/images/users/16.jpg"
                                        class="rounded-circle user_img avatar avatar-md" alt="img">
                                </div>
                                <div class="align-items-center mt-2 text-white">
                                    <h5 class="mb-0"><span id="title"></span></h5>
                                    <span class="dot-label bg-success"></span><span class="mr-3 fs-12">online</span>
                                </div>
                            </div>
                            <ul class="ah-actions actions align-items-center">
                                <li>
                                    <a href="" class="" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                height="24" viewBox="0 0 24 24" width="24">
                                                <path d="M0 0h24v24H0V0z" fill="none" />
                                                <path
                                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                                            </svg></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- action-header end -->

                        <!-- msg_card_body -->
                        <div class="card-body msg_card_body">
                            <div class="col-lg-12">
                                <div class="card widget-chat-box" style="border-top:0px solid">
                                    <div class="card-block">
                                        <form enctype="multipart/form-data"  method="post" name="sendMessageCarPulling_form"
                                            id="sendMessageCarPulling_form">
                                            @csrf
                                            <div class="input-group">
                                                <input type="text" id="message" name='message' class="form-control"
                                                    placeholder="Please Enter Message..."
                                                    style="    border: 1px solid #424e79 !important;" />
                                                <input type="hidden" id="sendToUsername" name="sendToUsername">
                                                <input type="hidden" id="cp_id" name="cp_id">
                                                <input type="hidden" id="login_username"
                                                    value="<?= auth()->user()->username ?>" name="login_username">
                                                <button type="button" id="messageBtn"
                                                    onclick="btn_sendMessageCarPulling()" class="btn btn-primary ">
                                                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                        height="24" viewBox="0 0 24 24" width="24">
                                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                                        <path d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z"
                                                            opacity=".3" />
                                                        <path
                                                            d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                        <hr style="border-top:2px solid tomato">
                                        <div id="msg_cotainer_send" class="text-left"></div>
                                    </div>
                                </div>
                                <p>
                                <div id="msg_cotainer"></div>
                                <div id="msg_cotainer_send"></div>
                                </p>
                            </div>
                        </div>
                        <!-- msg_card_body end -->
                        <!-- card-footer -->

                    </div>
                </div>
            </div>
        </div>

        <table id="itrc"></table>

    @stop

    @section('javascript')
        <script type="text/javascript">


function check_validation(validation_array){
					var count_errors = 0;
					$.each(validation_array, function( index, value ) {
						//alert(value);
						if ($('#'+value).val()==="" || $('#'+value).val()===null || $('#'+value).tokenfield('getTokensList')==="" || $('#'+value).tokenfield('getTokensList')===null){  
							$('#'+value).removeClass('is-valid').addClass('is-invalid');
							count_errors++;
						}
						else{
							$('#'+value).removeClass('is-invalid').addClass('is-valid');
							setTimeout(function(){
							$('#'+value).removeClass('is-valid');
							},2000);
						} 
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








            document.addEventListener('DOMContentLoaded', function() {
                // disable enter key on form
                document.addEventListener('keydown', function(event) {
                    if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                        event.preventDefault();
                        return false;
                    }
                });
            });

$(document).ready(function() {
    $('#simpletable').DataTable({
        scrollX: true,
        autoWidth: false
    });
});


            function acceptRide(id, cap, userID, AuthUserNamee,textNocap,parent_cpID,cp_partner) {
                // sendMail();
                // var bi_form = document.querySelector('#car_pulling');
               
                var data = new FormData();
               
                data.append("user_ID", userID);
                data.append("AuthUserNamee", AuthUserNamee);
                data.append("textNocap", textNocap);
                data.append("parent_cpID", parent_cpID);
                data.append("cpPartner", cp_partner);
                sendPostRequest('update-Capacity?cp_id=' + id + '&cap=' + cap, function(res) {
                    //console.log('Hello Baby', res);
                    //alert(res.status);
                    if (res.status) {
                        $('#successMsg' + userID).css('color', 'green');
                        $('#successMsg' + userID).html('Request Accepted Successfully');
                        // getRequest();     
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {

                    }
                }, data);
            }

            // var flag=0;
            $(document).ready(function() {
                $('#res-config').DataTable();
                setInterval(function() {
                    var username = $('#sendToUsername').val();
                    var id = $('#cp_id').val();
                    getUsername(username, id);

                }, 5000);
            });
            function getUsername(username, id) {
                var loginUsername = $('#login_username').val();
                $('#title').html(username);
                $('#sendToUsername').val(username);
                $('#cp_id').val(id);
                if (!id) {
                    return false;
                }
                var authUsername = '<?= auth()->user()->username ?>';
                $.ajax({
                    type: 'get',
                    url: 'api/get-car-pulling-message?cp_id=' + id + '&username=all' + '&authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    // data:formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            var rightSide = '';
                            var leftSide = '';
                            $('#msg_cotainer').html('');
                            $('#msg_cotainer_send').html('');

                            $(res.data).each(function(index, row) {
                                if (loginUsername != row.entry_user_id) {
                                    var divone = '<div class="d-flex justify-content-start">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-primary text-white"><span>' + row
                                        .message + '</span></div>';
                                } else {
                                    var divone = '<div class="d-flex justify-content-end">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-success text-white"><span>' + row
                                        .message + '</span></div>';

                                }
                                var row_add = divone + '' + divtwo + ' ' + divthree + '</div';
                                $('#msg_cotainer_send').append(row_add);
                            });

                            if (rightSide == '' && leftSide == '') {
                                rightSide = '<tr><td colspan="4"> No Message FOund Yet! </td></tr>';
                            }

                        } else {

                        }
                    },
                    error: function(jqXHR, exception) {},
                });
            }

            function btn_sendMessageCarPulling() {

                event.preventDefault()
                let message = document.getElementById("message").value.trim();
                if (message === '') {
                    return false;
                }
                //         // $('#messageBtn').attr('disabled', true);
                var bi_form = document.querySelector('#sendMessageCarPulling_form');
                var formData = new FormData(bi_form);
                var authUsername = '<?= auth()->user()->username ?>';
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: 'api/car-pulling-message?authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#message').val('');
                            var username = $('#sendToUsername').val();
                            var id = $('#cp_id').val();
                            getUsername(username, id)
                            // getRequest();     
                            setTimeout(function() {
                                $('#successMsg').html('');
                                $('#messageBtn').removeAttr("disabled");
                            }, 2000);

                        }
                    }

                });


            }
        </script>
    @stop
