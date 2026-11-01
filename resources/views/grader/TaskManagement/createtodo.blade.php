@extends('layouts.app')
{{-- @dd("123"); --}}
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
        font-size: 18px;
        color:#4454C3 !important;
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
.div_web {
    display: block;
    background-color:white;
}
.div_mobile {
    display: none;
    /* background-color:green; */
}
.add_more_sign {
  /* Default padding for web */
  padding: 38px;
}
/* Media query for mobile view */
@media (max-width: 767px) {
    .div_web {
        display: none;
    }
    #task_title_lable{
        display: none;
    }
    .div_mobile {
        display: block;
    }
    .add_more_sign {
  /* Default padding for web */
  padding: 50px;
}
}
.left_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:14%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
.right_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:8%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
/* =================================================
   TODO PAGE HEADER - TASK MANAGEMENT STYLE
   ================================================= */

.todo-management-header {
    width: 100%;
    padding: 0 24px 18px;
    box-sizing: border-box;
}


/* TITLE ROW */
.todo-page-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 28px;
}


.todo-page-title {
    margin: 0 !important;

    color: #2d2d2d;

    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
}


.todo-page-subtitle {
    display: block;
    margin-top: 4px;

    color: #6c757d;

    font-size: 13px;
    font-weight: 400;
}


/* SORT */
.todo-sort-wrapper {
    position: relative;
}


.todo-sort-dropdown {
    appearance: none;
    -webkit-appearance: none;

    min-width: 205px;

    padding: 10px 38px 10px 16px;

    background: #e3eceb;

    border: 1px solid #ccd8d7;
    border-radius: 10px;

    color: #1a2332;

    font-size: 13px;
    font-weight: 500;

    cursor: pointer;
    outline: none;
}


.todo-sort-wrapper::after {
    content: "\f107";

    position: absolute;
    right: 15px;
    top: 50%;

    transform: translateY(-50%);

    font-family: FontAwesome;

    color: #667085;

    pointer-events: none;
}


/* SECOND ROW */
.todo-controls-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    flex-wrap: wrap;

    gap: 18px;
}


/* TABS */
.todo-pill-tabs {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 10px;

    margin: 0 !important;
    padding: 0 !important;

    list-style: none;
    border: 0 !important;
}


.todo-pill-tabs > li {
    margin: 0 !important;
}


.todo-pill-tabs > li > a {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 36px;

    padding: 8px 20px !important;

    background: #ffffff !important;

    border: 1px solid #e2e8f0 !important;
    border-radius: 22px !important;

    color: #344054 !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    text-decoration: none !important;

    transition: all .2s ease;
}


.todo-pill-tabs > li > a.active,
.todo-pill-tabs > li.active > a,
.todo-pill-tabs > li > a:hover {
    background: #17675F !important;
    border-color: #17675F !important;

    color: #ffffff !important;
}


/* ADD BUTTON */
.todo-add-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 36px;
    height: 36px;

    padding: 0;

    background: #17675F;

    border: 1px solid #17675F;
    border-radius: 50%;

    color: #ffffff;

    cursor: pointer;
}


.todo-add-btn i {
    margin: 0;
    color: #ffffff;
}


/* FILTERS */
.todo-header-filters {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    flex-wrap: wrap;

    gap: 14px;

    color: #667085;

    font-size: 12px;
}


.todo-filter-title {
    font-weight: 500;
}


.todo-filter-option {
    display: inline-flex;
    align-items: center;

    gap: 6px;

    margin: 0 !important;

    color: #667085 !important;

    font-size: 12px !important;
    font-weight: 400 !important;

    cursor: pointer;
}


.todo-filter-option input {
    width: 14px;
    height: 14px;

    margin: 0;

    accent-color: #17675F;
}


.todo-pdf-btn {
    color: #d64545;

    font-size: 18px;

    cursor: pointer;
}


/* MOBILE */
@media (max-width: 767px) {

    .todo-management-header {
        padding: 0 14px 16px;
    }

    .todo-page-heading,
    .todo-controls-row {
        align-items: flex-start;
        flex-direction: column;
    }

    .todo-sort-wrapper,
    .todo-sort-dropdown {
        width: 100%;
    }

    .todo-header-filters {
        justify-content: flex-start;
    }
}



/* Main typography */
.todo-management-header,
#toDoDivForm,
#taskForm,
#tab_innerdivabc {
    font-family: 'Montserrat', sans-serif !important;
}

/* =========================
   PAGE HEADER
   ========================= */

.todo-management-header {
    /* background: #F7FAF9 !important; */
    padding: 0 24px 20px !important;
}

.todo-page-heading {
    margin-bottom: 24px !important;
}

.todo-page-title {
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;
    color: #252525 !important;
}

.todo-page-subtitle {
    margin-top: 4px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #667085 !important;
}


/* =========================
   SORT DROPDOWN
   ========================= */

.todo-sort-dropdown {
    min-width: 205px !important;
    height: 40px !important;
    padding: 0 40px 0 14px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #252525 !important;

    outline: none !important;
}

.todo-sort-dropdown:hover {
    border-color: #C4CECC !important;
}

.todo-sort-dropdown:focus {
    border-color: #17675F !important;
    box-shadow: 0 0 0 2px rgba(23, 103, 95, 0.08) !important;
}

.todo-sort-wrapper::after {
    color: #667085 !important;
    font-family: FontAwesome !important;
}


/* =========================
   CATEGORY TABS
   ========================= */

.todo-pill-tabs {
    gap: 10px !important;
}

.todo-pill-tabs > li > a {
    min-height: 36px !important;

    padding: 8px 20px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 22px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;

    color: #344054 !important;
}

.todo-pill-tabs > li > a.active,
.todo-pill-tabs > li.active > a {
    background: #17675F !important;
    border-color: #17675F !important;
    color: #FFFFFF !important;
}

.todo-pill-tabs > li > a:hover {
    background: #17675F !important;
    border-color: #17675F !important;
    color: #FFFFFF !important;
}


/* =========================
   ADD BUTTON
   ========================= */

.todo-add-btn {
    width: 36px !important;
    height: 36px !important;

    padding: 0 !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    color: #FFFFFF !important;
}

.todo-add-btn:hover {
    background: #125D56 !important;
    border-color: #125D56 !important;
}

.todo-add-btn .fa {
    margin: 0 !important;
    font-family: FontAwesome !important;
    color: #FFFFFF !important;
}


/* =========================
   FILTERS
   ========================= */

.todo-header-filters {
    gap: 14px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;

    color: #667085 !important;
}

.todo-filter-title {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #667085 !important;
}

.todo-filter-option {
    gap: 6px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 400 !important;

    color: #667085 !important;
}

.todo-filter-option input {
    width: 15px !important;
    height: 15px !important;

    margin: 0 !important;

    accent-color: #17675F;
}

.todo-pdf-btn {
    font-family: FontAwesome !important;
    font-size: 18px !important;
    color: #D92D20 !important;
}


/* =========================
   ADD / EDIT TASK FORM
   ========================= */

#toDoDivForm {
    /* margin-top: 18px !important;
    padding: 0 24px 24px !important;

    background: #F7FAF9 !important; */
}

#toDoDivForm > .col-sm-12 {
    padding: 0 !important;
}

#taskForm > .container-fluid {
    padding: 24px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 12px !important;

    box-shadow: none !important;
}


/* Form labels */
#taskForm label {
    margin-bottom: 7px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;

    color: #252525 !important;
}


/* Inputs / Select / Textarea */
#taskForm .form-control {
    height: 40px !important;

    padding: 0 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

#taskForm .form-control::placeholder {
    color: #98A2B3 !important;
}

#taskForm .form-control:hover {
    border-color: #C4CECC !important;
}

#taskForm .form-control:focus {
    border-color: #17675F !important;
    box-shadow: 0 0 0 2px rgba(23, 103, 95, 0.08) !important;

    outline: none !important;
}

#taskForm textarea.form-control {
    height: auto !important;
    min-height: 76px !important;

    padding: 10px 12px !important;

    resize: vertical;
}


/* Main task description */
#description {
    height: 40px !important;
}


/* Plus / Advance option button */
#toggleButton {
    width: 40px !important;
    min-width: 40px !important;
    height: 40px !important;

    padding: 0 !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    color: #FFFFFF !important;
}

#toggleButton:hover {
    background: #125D56 !important;
    border-color: #125D56 !important;
}

#toggleButton .fa {
    margin: 0 !important;
    font-family: FontAwesome !important;
    color: #FFFFFF !important;
}


/* Advance section */
#advanceOption {
    margin-top: 12px !important;
    padding-top: 18px !important;

    border-top: 1px solid #E5E7EB !important;
}


/* Radio buttons */
#taskForm .form-check {
    font-family: 'Montserrat', sans-serif !important;
}

#taskForm .form-check-input[type="radio"] {
    width: 16px !important;
    height: 16px !important;

    margin-top: 2px !important;

    accent-color: #17675F;
}

#taskForm .form-check-label {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 400 !important;

    color: #344054 !important;
}


/* =========================
   SUBMIT BUTTON
   ========================= */

#only_button {
    padding-top: 18px !important;
    padding-bottom: 6px !important;
}

#only_button .btn-success {
    min-width: 145px !important;
    height: 40px !important;

    padding: 0 18px !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 8px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;
}

#only_button .btn-success:hover,
#only_button .btn-success:focus {
    background: #125D56 !important;
    border-color: #125D56 !important;

    color: #FFFFFF !important;

    box-shadow: none !important;
}


/* Validation message */
#errorMsg {
    display: inline-block;

    margin-top: 8px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
}


/* =========================
   AJAX LOADED TASK LIST
   Existing classes preserved
   ========================= */

#tab_innerdivabc {
    padding: 0 24px 24px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
margin-left: 5px !important;
margin-right: 5px !important;
    /* background: #F7FAF9 !important; */
}


/* Existing task card heading */
#tab_innerdivabc .card_header {
    margin: 0 !important;

    padding: 14px 18px !important;

    background: #17675F !important;

    border: 1px solid #17675F !important;
    border-radius: 10px 10px 0 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;
}


/* Existing task card body */
#tab_innerdivabc .bottom_tr {
    margin: 0 0 16px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-top: 0 !important;
    border-radius: 0 0 10px 10px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;

    color: #252525 !important;
}


/* Resource rows */
#tab_innerdivabc .resources_div {
    padding: 10px 14px !important;

    border-bottom: 1px solid #E5E7EB !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;

    color: #344054 !important;
}

#tab_innerdivabc .for_li {
    margin-left: 1% !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;

    color: #344054 !important;
}


/* Existing left/right blocks */
#tab_innerdivabc .left_div,
#tab_innerdivabc .right_div {
    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    color: #252525 !important;
}


/* Delete action */
#tab_innerdivabc .delete-button {
    background: transparent !important;

    border: 0 !important;

    color: #D92D20 !important;
}


/* Preserve FontAwesome icons */
#tab_innerdivabc .fa,
.todo-management-header .fa,
#taskForm .fa {
    font-family: FontAwesome !important;
}


/* =========================
   MOBILE
   ========================= */

@media (max-width: 767px) {

    .todo-management-header {
        padding: 0 14px 16px !important;
    }

    .todo-page-heading {
        margin-bottom: 18px !important;
    }

    .todo-page-title {
        font-size: 21px !important;
    }

    .todo-sort-wrapper,
    .todo-sort-dropdown {
        width: 100% !important;
    }

    .todo-header-filters {
        justify-content: flex-start !important;
    }

    #toDoDivForm {
        padding: 0 14px 18px !important;
    }

    #taskForm > .container-fluid {
        padding: 16px !important;
    }

    #tab_innerdivabc {
        padding: 0 14px 18px !important;
    }
}
  </style>
   <input type="hidden" id="employee_id" value="{{auth()->user()->employee_id}}">
<div style="margin-left:auto;margin-right:auto;width:100%;border:solid 0px red;">
    <div class="todo-management-header" style="padding-left: 0 !important; padding-right: 0 !important;">

    {{-- =========================
         TITLE + SORT
         ========================= --}}
    <div class="todo-page-heading">

        <div>
            <h3 class="todo-page-title">To-Do</h3>
            <span class="todo-page-subtitle" style="font-size: 16px !important;">Stay Organized</span>
        </div>

        <div class="todo-sort-wrapper">
            <select id="sortDropdown" class="todo-sort-dropdown">
                <option value="Latest">Sort by: Latest to Oldest</option>
                <option value="OldestFirst">Sort by: Oldest to Latest</option>
            </select>
        </div>

    </div>


    {{-- =========================
         TABS + FILTERS
         ========================= --}}
    <div class="todo-controls-row">

        <div class="tabs-menu">

            <ul class="nav todo-pill-tabs" id="ul_show_hide">

                <li>
                    <a
                        href="#tabAll"
                        id="aidAll"
                        class="active"
                        data-toggle="tab"
                        onclick="fetchData('All','Latest')"
                    >
                        All
                    </a>
                </li>


                @foreach ($category as $rec_category)

                    <li>
                        <a
                            href="#tab{{$rec_category->id}}"
                            id="aid{{$rec_category->id}}"
                            value="{{$rec_category->id}}"
                            data-toggle="tab"
                            onclick="fetchData({{$rec_category->id}},'Latest')"
                        >
                            {{$rec_category->category_title}}
                        </a>
                    </li>

                @endforeach


                <li>
                    <a
                        href="#tabCompleted"
                        data-toggle="tab"
                        onclick="fetchData('Completed','Latest')"
                    >
                        Completed
                    </a>
                </li>


                {{-- SAME ADD BUTTON / SAME ID --}}
                <li class="todo-add-item">

                    <button
                        type="button"
                        class="todo-add-btn"
                        id="toggleToDoForm"
                        style="display:none"
                    >
                        <i
                            id="iconToggleToDoForm"
                            class="fa fa-plus"
                        ></i>
                    </button>

                </li>

            </ul>

        </div>


        {{-- FILTERS --}}
        <div class="todo-header-filters">

            <span class="todo-filter-title">
                Filter by:
            </span>


            <label class="todo-filter-option">

                <input
                    type="checkbox"
                    name="selection[]"
                    checked
                    value="Urgent"
                    onchange="handleCheckboxChange(this)"
                >

                <span>Urgent</span>

            </label>


            <label class="todo-filter-option">

                <input
                    type="checkbox"
                    name="selection[]"
                    checked
                    value="LongTerm"
                    onchange="handleCheckboxChange(this)"
                >

                <span>Long Term</span>

            </label>


            <?php
            if (
                auth()->user()->username=='moeedyusuf'
                || auth()->user()->username=='bilal.saleem'
                || auth()->user()->username=='salman.zafar'
                || auth()->user()->username=='sumair.shafique'
            ){
            ?>

                <i
                    class="fa fa-file-pdf-o todo-pdf-btn"
                    onclick="pdf()"
                ></i>

            <?php } ?>

        </div>

    </div>

</div>
    <div style="border:solid 0px black;margin-left:auto;margin-right:auto;width:100%;display:none !important;">
            <table border="0" width="100%" cellpadding="0px">
                <tr>
                    <td width="45%" style="background-color: lightgray;">
                        @if(isset($_REQUEST['category_name']) && $_REQUEST['category_name'] != "")
                                <label for="task_title"><?=$_REQUEST['category_name'];?></label>
                        @endif
                    </td>
                    <td width="33%" style="background-color: lightgray;"> <h2 class="pt-3">To-Do</h2> </td>
                    <td style="background-color: lightgray;">
                    @if(isset($_REQUEST['id']) && $_REQUEST['id'] != "")
                                <button type="button"  class="btn btn-primary" id="toggleToDoForm">
                                    <i id="iconToggleToDoForm" style="font-size:14px;" class="fa fa-plus"></i>
                                </button>
                            <label for="task_title"></label>

                    @endif
                    </td>
                </tr>
            </table>
    </div>
    <!--  tab bar start--------------------- -->
    <div style="padding-top:15px; display:none !important;">
        <div class="container-fluid" >
            <div class="panel panel-primary tabs-style-3">
                <!-- <div class="tab-menu-heading"> -->
                    <div class="tabs-menu">
                        <!-- Tabs -->
                        <div style="display:inline">
                            <div style="float:left">
                                <ul class="nav panel-tabs" id="ul_show_hide">
                                <li style="widht:50% !important;">
                                        <a href="#tabAll" id="aidAll" class="active"  data-toggle="tab" onclick="fetchData('All','Latest')">
                                        <i class="fa fa-cube"></i>
                                            All
                                        </a>
                                    </li>
                                @php

                                $li_width = 100/(count($category)+1);
                                @endphp


                                @foreach ($category as $rec_category)
                                    <li style="widht:50% !important;">
                                        <a href="#tab{{$rec_category->id}}" id="aid{{$rec_category->id}}" value="{{$rec_category->id}}" data-toggle="tab" onclick="fetchData({{$rec_category->id}},'Latest')">
                                        <i class="fa fa-cube"></i>
                                                    {{$rec_category->category_title}}
                                        </a>
                                    </li>
                                @endforeach
                                    <li style="widht:50% !important;">
                                        <a href="#tab{{$rec_category->id}}" id="aid{{$rec_category->id}}" value="{{$rec_category->id}}" data-toggle="tab" onclick="fetchData('Completed','Latest')">
                                        <i class="fa fa-cube"></i>
                                            Completed
                                        </a>
                                    </li>
                                </ul>
                            </div>

                                <div style="float:right;padding-top:2.5px">

                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
<td><label for="sortDropdown">Sort by:</label></td>
<td>
    <select  id="sortDropdown" class="form-control">
    <option value="Latest">Latest to Oldest</option>
    <option value="OldestFirst">Oldest to Latest</option>
    </select>
</td>
<td>
<label for="task_title"></label>
<button type="button"  class="btn btn-primary" id="toggleToDoForm" style="display:none">
<i id="iconToggleToDoForm" class="fa fa-plus"></i>
</button>
</td>
</tr>
                                </table>




                                </div>

                        </div>
                    </div>
                <!-- </div> -->
            </div>


<div class="row" style="border:solid 0px green">
    <div class="col-12 col-sm-12 col-md-12  col-lg-12" style="text-align:right">
        <table width="30%" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td>Filter by:</td>
                <td align="center">Urgent:<input type="checkbox" name="selection[]" checked value="Urgent" onchange="handleCheckboxChange(this)"> </td>
                <td align="center">Long Term:<input type="checkbox" name="selection[]" checked value="LongTerm" onchange="handleCheckboxChange(this)"></td>
                <?php if(auth()->user()->username=='moeedyusuf' || auth()->user()->username=='bilal.saleem' || auth()->user()->username=='salman.zafar'  || auth()->user()->username=='sumair.shafique'){?>
                <td align="center"><i style="cursor:pointer;color: red" class="fa fa-file-pdf-o fa-2x" onclick="pdf()"></i></td>
                <?php } ?>
            </tr>
        </table>
    </div>
</div>


        </div>
    </div>
    <!-- --------------tab bar end----------------- -->
    <div class="page-body" id="toDoDivForm" style="display:none">
        <div class="col-sm-12">
            <form id="taskForm" action="{{url('tododboard_submit') }}" method="POST">
                @csrf
                <div class="container-fluid" style="border:solid 1px black">
                    <div class="row" style="display:none">
                        <div class="col-sm-12">
                            <label for="Task Type">Task Type:</label>
                            <select class="form-control" name="task_type" id="task_type">
                                <!-- {{-- <option selected="selected">--Select Activity --</option> --}} -->
                                <option>Task</option>
                                <option selected="selected">ToDo</option>
                            </select>
                            <input type="hidden" id="entry_user_id" name="entry_user_id" value="<?=auth()->user()->employee_id;?>">
                        </div>
                    </div>
                    <div class="row" id="user_and_me_divs" style="display:none">
                        <div class="col-sm-12">
                            <table id="userDataBody"></table>
                        </div>
                    </div>

                    <div class="row" id="date_time_desc_group">
                        <div class="col-12 col-sm-12 col-md-12  col-lg-12">
                            <div class="row" style="border:solid 0px green;height:90px">
                                <div class="col-12 col-sm-12 col-md-12  col-lg-12">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td>
                                                <label for="task_title_lable" id="task_title_lable">
                                                Task / Project :
                                                <!-- <span class="badge badge-warning" id="badgeText"><b>Urgent</b></span> -->
                                                </label>
                                            </td>
                                            <td> &nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="textbox" placeholder="Enter something to do" name="description" class="form-control" id="description" maxlength="1800">
                                                <input type="hidden" name="task_category_id" id="task_category_id">
                                                <input type="hidden" name="task-id" id="task-id">
                                                <input type="hidden" id="edit_tm_id" name="edit_tm_id" >
                                                <input type="hidden" id="edit_todo_row_id" name="edit_todo_row_id" >
                                            </td>
                                            <td>
                                                <button type="button"  class="btn btn-primary" id="toggleButton">
                                                    <i id="iconToggle" class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-2 col-sm-2 col-md-3  col-lg-3 add_more_sign">

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12" style="display:none" id="advanceOption">
                            <div class="col-sm-12">
                                <div class="row" >
                                    <div class="col-sm-6">
                                        <label for="completion_time">Alert Time:</label>
                                            <select name="completion_time" class="form-control" id="completion_time">
                                        <?php
                                            $start_time = strtotime('00:00');
                                            $end_time = strtotime('23:00');
                                            $interval = 30 * 60; // 1 hour interval
                                            echo '<option selected="selected" value="">--Select Time--</option>';
                                            for ($time = $start_time; $time <= $end_time; $time += $interval) {
                                                $display_time = date('h:i A', $time);
                                                $option_value = date('H:i', $time);
                                                $selected = "";//($option_value === '09:00') ? 'selected' : '';

                                                echo '<option value="' . $option_value . '" ' . $selected . '>' . $display_time . '</option>';
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="completion_date">Alert Date:</label>
                                        <input type="date" name="completion_date" class="form-control" id="completion_date" onchange="handleDateSelection()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row" >
                                    <div class="col-sm-12">
                                        <label for="description">Description:</label>
                                        <textarea name="task_title" id="task_title" class="form-control" placeholder="Please enter something "  maxlength="2900"  rows="2" cols="40"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12  col-lg-12"  style="padding-left:34px !important">
                            <div class="form-check">
                                <!-- <input class="form-check-input" name="tasktype" type="checkbox" id="longTermCheckbox">
                                <label class="form-check-label" for="longTermCheckbox"><span id="chkboxTxt">Mark Task as Long Term</span></label> -->
                                <input class="form-check-input" type="radio" name="tasktype" id="Radiobtn" value="Urgent" checked>
                                <label class="form-check-label" for="urgentRadio">Urgent</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <input class="form-check-input" type="radio" name="tasktype" id="Radiobtn" value="Longterm">
                                <label class="form-check-label" for="longTermRadio">Long Term</label>



                            </div>
                        </div>
                        <div id="task_type_div" style="display: none;left:21px;" class="col-12 col-sm-12 col-md-12  col-lg-12"  style="padding-left:34px !important">
                            <div class="form-check">
                                <!-- <input class="form-check-input" name="tasktype" type="checkbox" id="longTermCheckbox">
                                <label class="form-check-label" for="longTermCheckbox"><span id="chkboxTxt">Mark Task as Long Term</span></label> -->
                                <input class="form-check-input" type="radio" name="category_type" id="Radiobtn" value="22">
                                <label class="form-check-label" for="urgentRadio">BNU</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <input class="form-check-input" type="radio" name="category_type" id="Radiobtn" value="23">
                                <label class="form-check-label" for="longTermRadio">Meeting/Calls</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <input class="form-check-input" type="radio" name="category_type" id="Radiobtn" value="24">
                                <label class="form-check-label" for="longTermRadio">Funding/Partnership</label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp


                            </div>
                        </div>
                    </div>
                    <div class="row" id="points_heading" style="display:none">
                        <div class="col-sm-12" id="show_list">
                            <div class="row" style="background-color:gray;padding: 4px;" >
                                <div class="col-sm-3 for_task_show">
                                        Title
                                        <inpu type="text" name="task_point_desc[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_date">
                                        Date
                                        <inpu type="text" name="task_point_date[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_time">
                                        Time
                                        <inpu type="text" name="task_point_time[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_remove">
                                        Remove
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="append_desc_date_time"></div>
                    <!-- <div class="row">
                        <div class="col-sm-12">
                                <ul id="valueList"></ul>
                        </div>
                    </div>                   -->
                    <div class="row" id="only_button" style="padding-bottom: 10px;">
                        <div class="col-sm-12">
                            <!-- <button type="submit" class="btn-block btn btn-success" ><i class="fe fe-check mr-2"></i>Submit Task</button> -->
                            <center>
                                <button type="submit" class="btn btn-success">
                                    <i class="fe fe-check mr-2"></i>Submit Task
                                </button>
                            </center>
                            <span id="errorMsg"></span>
                        </div>
                    </div>
            </form>

        </div>
    </div>
</div>
<div class="row">
        <div id="tab_innerdivabc" style="width:100%; margin-left:auto; margin-right:auto;"></div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
function pdf(){
    SortID = $("#sortDropdown").val();
    categoryId = $("#task_category_id").val();
    var checked = $('input[name="selection[]"]:checked').map(function() {
        if($(this).val()=="LongTerm"){

            return "Longterm";
        }
        return $(this).val();
    }).get();
    type='ToDo';
    emp_id = $("#employee_id").val();
    url = '/assets/cms/reports/todo_task_pdf.php?id='+categoryId+'&type='+type+'&sortId='+SortID+'&chkbox='+checked+'&eid='+emp_id,
    window.open(url, '_blank');
}
function editTodo(id){
    $("#task_type_div").css("display","block");
  //  $("#toDoDivForm").toggle();
    $("#toDoDivForm").toggle();
    $("#description").focus();

    if ($("#toDoDivForm").is(':visible')) {
      console.log("The div is currently visible.");
    } else {
        $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
        $("#toDoDivForm").toggle();
        if($('#edit_tm_task_title').val()){
        $("#advanceOption").toggle();
        $("#iconToggle").toggleClass("fa-plus fa-minus");
        }

    }
    //alert($('#edit_tm_description').val());
    var btn=$('#edit_tm_task_priority'+id).val();
    $("input[name='tasktype'][value='"+btn+"']").prop("checked", true);
    $("input[name='category_type'][value='"+$('#category_id_'+id).val()+"']").prop("checked", true);

    $('#description').val($('#edit_tm_task_title'+id).val());
    $('#edit_tm_id').val(id);
    $('#task_title').val($('#edit_tm_description'+id).val());
    $('#completion_time').val($('#edit_tm_completion_time'+id).val());
    $('#completion_date').val($('#edit_tm_completion_date'+id).val());
}

function rollBack(id){
    //alert('Rollback Successfully Done');
    $.ajax({
        url: '{{ route("rollBack") }}?id='+id,
        type: 'GET',

        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData($("#task_category_id").val(),'Latest','All');

        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}

function editTodoS(id){
    // alert(id);
    $('#toDoDivForm').show();
    $('#task-id').val(id);

    $.ajax({
        url: '{{ route("editTask") }}?id='+id,
        type: 'GET',

        success: function(response) {
            console.log(response.data[0]['tm_title'])
            // Set a timeout to close the loader after two seconds
            //fetchData($("#task_category_id").val(),'Latest','All');
             $('#description').val(response.data[0]['tm_title'])

        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
function confirmDelete(id) {
  // Show confirmation dialog
  if (confirm("Are you sure you want to delete this task forever?")) {
    // Call the delete function
    del(id);
  }
}
function del(id){
        //alert(id);
    $.ajax({
        url: '{{ route("deleteTask") }}?id='+id,
        type: 'GET',

        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData($("#task_category_id").val(),'Latest','All');

        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
function handleCheckboxChange(checkbox) {
    var checked = $('input[name="selection[]"]:checked').map(function() {
        return $(this).val();
    }).get();

    //alert(checkbox);

    fetchData($("#task_category_id").val(),'Latest',checked);
}
$('#sortDropdown').on('change', function() {
    var selectedValue = $(this).val();
    // Perform actions based on the selected value
    console.log('Selected value: ' + selectedValue);

    //fetchData('All',selectedValue);
    fetchData($("#task_category_id").val(),selectedValue,'undefined');

});

$(document).ready(function() {

    fetchData('All','Latest');
    $("#description").focus();
    $("#toggleButton").click(function() {
        $("#task_type_div").css("display","none");
        $("#advanceOption").toggle();
        $("#iconToggle").toggleClass("fa-plus fa-minus");
    });
    $("#toggleToDoForm").click(function() {
        $("#task_type_div").css("display","none");
        $("#toDoDivForm").toggle();
        $("#description").focus();
        // alert($(this).is(':visible'));
        // $("#ul_show_hide").hide();
        $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
        //  $("#tab_innerdivabc").hide();
    });
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
    $('#toggleToDoForm').hide();
    //--------submit function--------------------------------
    $('#taskForm').submit(function(event) {
        $("#task_type_div").css("display","none");
        event.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var formData = new FormData(form[0]);
        // var category_id = $("#aid"+categoryId).attr("value");
        // formData.append('category_id', values[j]);
        // alert(a_href);
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
                //alert($("#task_category_id").val());
                $('#edit_todo_row_id').val($("#edit_tm_id").val());
                fetchData($("#task_category_id").val(),'Latest');
                $('#completion_time').val('--Select Time--');
                submitTask();
                   $("#edit_tm_id").val('');
                   $("#task_title").val('');
                   $('#completion_date').val('');
                   $('#completion_date').val('');

                setTimeout(function() {
                    if (response === "NOUSER") {
                        alert("Please select at least one user.");
                    } else if (response === "NOTASK") {
                        alert("Please enter at least one task.");
                    } else {
                        $("#description").val('');
                        var valueList = document.getElementById('valueList');
                        valueList.innerHTML = '';
                        var checkboxes = document.querySelectorAll('input[name="user_id[]"]');
                        checkboxes.forEach(function(checkbox) {
                        //checkbox.checked = false;
                        });
                    }
                }, 1000);
            },
            error: function(xhr, status, error) {
                // Display error message or perform any other actions
                //alert('An error occurred while saving the task.');
                //console.log(xhr)
                //alert(message)
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $('#errorMsg').html('Task / Project Required');
                    $('#errorMsg').css('color','red');
                    // Handle validation errors
                    setTimeout(function() {
                        $('#errorMsg').html('');
                    }, 2000);
                }else{
                    // $('#errorMsg').html('');
                    // Handle other error cases
                }
            }
        });
    });
    // end of submit function --------------------------------
});
function submitTask() {
    $("#description").val('');
    $('#description').prop('readonly', true);
    //$("#description").hide();
    var submitButton = document.querySelector(".btn-success");
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

            //event.preventDefault();
            submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Submit Task';
        // document.getElementById('taskForm').submit();
        $("#description").show();
        $('#description').prop('readonly', false);

    }, 1000);
}
function fetchData(categoryId,SortID,chk) {
   //alert(chk)
    //var str=$("#aid"+categoryId).text();
    if (categoryId == 22) {
        $("input[name='category_type'][value='22']").prop('checked', true);
    }else if (categoryId == 23) {
        $("input[name='category_type'][value='23']").prop('checked', true);
    }else if (categoryId == 24) {
        $("input[name='category_type'][value='24']").prop('checked', true);
    }
    $("#edit_tm_id").val('');
    $('#description').val('');
    if(categoryId == 'All' || categoryId == 'Completed'){
        $('#toggleToDoForm').hide();
        $("#toDoDivForm").hide();
        $("#iconToggleToDoForm").removeClass().addClass("fa fa-plus");
        //$("#iconToggleToDoForm").addClass("fa-plus");
    }else{
        $('#toggleToDoForm').show();
       // $("#toDoDivForm").hide();
       // $("#iconToggleToDoForm").removeClass().addClass("fa fa-plus");
        // $("#toDoDivForm").hide();
        // $("#iconToggleToDoForm").removeClass().addClass("fa fa-plus");
        // $(".iconToggleToDoForm").removeClass("fa-minus").addClass("fa-plus");
    }
    if(chk == 'LongTerm' || chk == 'Urgent,LongTerm' || chk == 'Urgent' || chk == ''){
        //alert('asd');
        $('#toggleToDoForm').show();
    }

    var a_href = $("#aid"+categoryId).attr("value");
    //  alert(a_href);
    //return false;
    $("#task_category_id").val((categoryId==""?"All":categoryId));
    const tabContent = $('#tab_innerdivabc');
    tabContent.empty(); // Empty the div before appending new data
    $("#tab_innerdivabc").show();
    //   $("#toDoDivForm").hide();
    $.ajax({
        url: '/staff/showTodoTaskList?id='+categoryId+'&type=ToDo&sortId='+SortID+'&chkbox='+chk,
        method: 'GET',
        success: function(data) {
        //console.log(data);
        tabContent.html(data); // Append the fetched data to the tab content element
        // initializeDataTable(); // Re-initialize DataTable after content is appended

        $("#tab_innerdivabc").html(data);
        $('#row' + $("#edit_todo_row_id").val()).css('background-color', '#ded6f1');
            var targetElement = $('#row' + $("#edit_todo_row_id").val());
            var targetCenter = targetElement.offset().top + targetElement.outerHeight() / 2;
            var windowHeight = $(window).height();
            $('html, body').animate({
                scrollTop: targetCenter - windowHeight / 2
            }, 500);


        },
        error: function(error) {
        //console.error('Error:', error);
        }
    });
}
function confirmMarkCompleted(taskId) {
    // $("#demo"+taskId).collapse('toggle');
    $("#demo"+taskId).collapse('hide');
    // Ask for confirmation using the built-in confirm() function
    var confirmed = confirm("Are you sure you mark this task as Completed?");
    if (confirmed) {
        // User confirmed, proceed with marking the task as completed
        markCompleted(taskId);
    }
}
function markCompletedAllTodo(){
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
        $('input[name^="markCompletedAllTodo["]').each(function() {
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
            url: '{{ route("updateTaskAllTodo") }}',
            type: 'POST',
            data: data,
            success: function(response) {
                // Set a timeout to close the loader after two seconds
                // fetchData();
                fetchData($("#task_category_id").val(),'Latest','All');
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
function deleteSelected(){

    //console.log((".subtask-checkbox:checked").length);
    if ($(".subtask-checkbox:checked").length > 0) {
            // At least one checkbox is checked, perform the "Mark Completed" action here
            //alert("Marking completed...");
            // You can replace the alert with your actual "Mark Completed" logic
    } else {
        // No checkboxes are checked, show an error message or take appropriate action
        alert("Please check at least one Task before Delete All.");
        return false;
    }
    var confirmed = confirm("Are you sure you want to delete all these tasks?");

    if (confirmed) {
        //alert('ok')
        // User confirmed, proceed with marking the task as completed
    // markCompleted(taskId);
    var checkboxValues = [];
        // Select all checkboxes with the name attribute "markCompletedAll[]"
        $('input[name^="markCompletedAllTodo["]').each(function() {
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
            url: '{{ route("delete-selected-todo") }}',
            type: 'POST',
            data: data,
            success: function(response) {
                // Set a timeout to close the loader after two seconds
                // fetchData();
                fetchData($("#task_category_id").val(),'Latest','All');
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
            showLoader(taskId);
            setTimeout(function() {
                // Hide the loader
                hideLoader(taskId);
                // Handle the response if needed
                //console.log(response);
                hide_row(taskId);
            }, 1000);
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
           // console.log("Error: " + error);
        }
    });
}
function showLoader(rowid) {
    // Show the loader by modifying the display style
    var loader = document.getElementById("loader"+rowid);
    loader.style.display = "block";
}

function hideLoader(rowid) {
    // Hide the loader by modifying the display style
    var loader = document.getElementById("loader"+rowid);
    loader.style.display = "none";
}
function hide_row(taskId){
    // Hide the corresponding div with ID "row" + taskId
    var rowId = "row" + taskId;
    var rowElement = document.getElementById(rowId);
    if (rowElement) {
        rowElement.style.display = "none";
    }
}
// Add event listener for Enter key press
document.getElementById("description").addEventListener("keydown", function(event) {
    if (event.keyCode === 13) { // Check if Enter key was pressed
        event.preventDefault();
            // Prevent the default Enter key behavior (new line)
        $('#taskForm').submit();
        fetchData($("#task_category_id").val(),'Latest');
        //addValue(); // Call the addValue() function
    }
});
</script>
@stop
