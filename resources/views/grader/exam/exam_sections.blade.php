@extends('layouts.app')
{{-- @dd("12"); --}}
@section('content')
  <div class="" style="margin-bottom:30px;">
        <h4 class="profile-overview-title">Manage Exam Sections</h4>
    </div>
<div class="card">
  
    <div class="card-body">
        <form id="sectionForm" class="mb-5">
            @csrf
            <input type="hidden" name="id" id="section_id">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="section_name" class="font-weight-bold">Section Name <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <textarea class="form-control" id="section_name" name="section_name" required maxlength="3900" rows="3"></textarea>
                            <small class="text-muted d-block text-right">
                                Characters remaining: <span id="section_name_counter">3900</span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status" class="font-weight-bold">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="section_description" class="font-weight-bold">Description</label>
                <div class="position-relative">
                    <textarea class="form-control" id="section_description" name="section_description" rows="5" maxlength="3900"></textarea>
                    <small class="text-muted d-block text-right">
                        Characters remaining: <span id="section_description_counter">3900</span>
                    </small>
                </div>
            </div>
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fa fa-save"></i> <span id="btnText">Add Section</span>
                </button>
                <button type="button" class="btn btn-secondary" id="resetBtn">
                    <i class="fa fa-undo"></i> Reset
                </button>
            </div>
        </form>

        <hr>

        <div class="table-responsive">
            <table id="sectionsTable" class="table table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="color:white;">#</th>
                        <th style="color:white;">Section Name</th>
                        <th style="color:white;">Description</th>
                        <th style="color:white;">Entry Date</th>
                        <th style="color:white;">Status</th>
                        <th style="color:white;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examSection as $section)
                    <tr>
                        <td>{{ $section->id }}</td>
                        <td>{{ $section->section_name }}</td>
                        <td>{{ $section->secction_description }}</td>
                           <td>{{ $section->entery_datetime }}</td>
                        <td>
                            <span class="badge badge-{{ $section->status ? 'success' : 'danger' }}">
                                {{ $section->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                     
                        {{-- both button in one row following the same style --}}
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn mb-2" 
                            style="width: 100px;"
                                    data-id="{{ $section->id }}" 
                                    data-name="{{ $section->section_name }}"
                                    data-desc="{{ $section->secction_description }}"
                                    data-status="{{ $section->status }}">
                                <i class="fa fa-edit"></i> Edit
                            </button>




@if(auth()->user()->group_id == 644)
                            <button class="btn btn-sm btn-danger delete-btn mb-2"
                            style="width: 100px;"
                            data-id="{{ $section->id }}">
                                <i class="fa fa-trash"></i> Delete
                            </button>
@endif


                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('components.toast-notifications')

@endsection

<style>
    .form-group label {
        margin-bottom: 0.5rem;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .badge {
        padding: 0.5em 1em;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        margin: 0 0.2rem;
    }
    /* Add styles for character counter */
    .text-right {
        text-align: right !important;
    }
    textarea {
        resize: vertical;
        min-height: 100px;
    }
    .character-limit-reached {
        border-color: #dc3545 !important;
    }
    .main-content {
    padding-left: 12px;
    padding-right: 12px;
}


.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}

/* =========================================
   Exam Section Form - Screenshot Style
========================================= */

#sectionForm {
    margin-bottom: 25px !important;
}

/* Labels */
#sectionForm .form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 500 !important;
    color: #222;
    line-height: 1.2;
}

/* Reduce bootstrap row gap slightly */
#sectionForm .row {
    margin-left: -8px;
    margin-right: -8px;
}

#sectionForm .row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}

/* Form spacing */
#sectionForm .form-group {
    margin-bottom: 12px;
}

/* Common input styling */
#sectionForm .form-control {
    width: 100%;
    border: 1px solid #dedede;
    border-radius: 4px;
    background: #fff;
    color: #333;
    font-size: 12px;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Focus */
#sectionForm .form-control:focus {
    border-color: #8ebbb5;
    box-shadow: 0 0 0 2px rgba(39, 126, 116, 0.08);
    outline: none;
}

/* =========================================
   Section Name
   Existing textarea ko compact field jaisa
========================================= */

#section_name {
    height: 38px !important;
    min-height: 38px !important;
    padding: 9px 12px;
    resize: none;
    overflow: hidden;
    line-height: 18px;
}

/* =========================================
   Status Select
========================================= */

#status {
    height: 38px;
    padding: 6px 35px 6px 12px;
    cursor: pointer;
}

/* =========================================
   Description
========================================= */

#section_description {
    width: 100%;
    height: 83px !important;
    min-height: 83px !important;
    padding: 10px 12px;
    resize: none;
    line-height: 18px;
}

/* =========================================
   Character Counters
   Screenshot mein visible nahi hain
========================================= */

#sectionForm small.text-muted {
    /* display: none !important; */
}

/* =========================================
   Buttons container
========================================= */

#sectionForm .form-group.mt-4 {
    margin-top: 11px !important;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 7px;
}

/* Common button */
#sectionForm .btn {
    min-width: 94px;
    height: 28px;
    padding: 4px 15px;
    border-radius: 3px;
    font-size: 12px;
    line-height: 18px;
    box-shadow: none !important;
}

/* Add Section */
#sectionForm #submitBtn {
    background-color: #247d72 !important;
    border: 1px solid #247d72 !important;
    color: #fff !important;
}

#sectionForm #submitBtn:hover {
    background-color: #1e6b62 !important;
    border-color: #1e6b62 !important;
}

/* Reset */
#sectionForm #resetBtn {
    background-color: #fff !important;
    border: 1px solid #247d72 !important;
    color: #247d72 !important;
}

#sectionForm #resetBtn:hover {
    background-color: #f1f8f7 !important;
    color: #1e6b62 !important;
}

/* Screenshot mein buttons par icons nahi hain */
#sectionForm .btn > i {
    display: none;
}

/* =========================================
   Mobile responsive
========================================= */

@media (max-width: 767.98px) {

    #sectionForm .col-md-6 {
        margin-bottom: 0;
    }

    #sectionForm .form-group.mt-4 {
        flex-wrap: wrap;
    }

    #sectionForm .btn {
        min-width: 90px;
    }
}







/* =========================================================
   MANAGE EXAM SECTIONS - DATATABLE
   CSS ONLY
========================================================= */

/* Remove old HR heavy appearance */
.card-body > hr {
    border: 0;
    border-top: 1px solid #eeeeee;
    margin: 22px 0 20px;
}

/* DataTable wrapper */
#sectionsTable_wrapper {
    width: 100%;
    font-size: 12px;
    color: #333;
}

/* =========================================================
   TOP CONTROLS
   Show entries + Search
========================================================= */

#sectionsTable_wrapper .dataTables_length,
#sectionsTable_wrapper .dataTables_filter {
    margin-bottom: 14px;
    font-size: 12px;
    color: #444;
}

#sectionsTable_wrapper .dataTables_length {
    float: left;
}

#sectionsTable_wrapper .dataTables_filter {
    float: right;
    text-align: right;
}

/* Show entries dropdown */
#sectionsTable_wrapper .dataTables_length select {
    width: 65px;
    height: 32px;
    margin: 0 5px;
    padding: 4px 8px;

    border: 1px solid #dddddd;
    border-radius: 4px;

    background-color: #fff;
    color: #333;

    font-size: 12px;
    outline: none;
    box-shadow: none;
}

/* Search input */
#sectionsTable_wrapper .dataTables_filter input {
    width: 190px;
    height: 32px;

    margin-left: 7px;
    padding: 5px 10px;

    border: 1px solid #dddddd;
    border-radius: 4px;

    background: #fff;
    color: #333;

    font-size: 12px;

    outline: none;
    box-shadow: none;

    transition: all 0.2s ease;
}

#sectionsTable_wrapper .dataTables_filter input:focus,
#sectionsTable_wrapper .dataTables_length select:focus {
    border-color: #247d72;
    box-shadow: 0 0 0 2px rgba(36, 125, 114, 0.08);
}


/* =========================================================
   TABLE CONTAINER
========================================================= */

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

/* Main Table */
#sectionsTable {
    width: 100% !important;
    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0;

    border: 1px solid #e5e5e5;
    border-radius: 4px;

    overflow: hidden;

    background: #fff;
}


/* =========================================================
   TABLE HEADER
========================================================= */

/* Override Bootstrap bg-primary */
#sectionsTable thead,
#sectionsTable thead.bg-primary {
    background: #247d72 !important;
}

#sectionsTable thead th {
    background: #247d72 !important;

    color: #ffffff !important;

    padding: 11px 12px;

    border-top: 0 !important;
    border-bottom: 0 !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15);

    font-size: 12px;
    font-weight: 500;

    vertical-align: middle;
    white-space: nowrap;
}

#sectionsTable thead th:last-child {
    border-right: 0;
}

/* DataTables sorting arrows */
#sectionsTable thead th.sorting,
#sectionsTable thead th.sorting_asc,
#sectionsTable thead th.sorting_desc {
    padding-right: 25px !important;
}


/* =========================================================
   TABLE BODY
========================================================= */

#sectionsTable tbody td {
    padding: 10px 12px;

    border-top: 0 !important;
    border-bottom: 1px solid #eeeeee;

    color: #444;

    font-size: 12px;
    line-height: 1.5;

    vertical-align: middle;

    background: #fff;
}

/* Remove Bootstrap striped background */
#sectionsTable.table-striped tbody tr:nth-of-type(odd),
#sectionsTable.table-striped tbody tr:nth-of-type(even) {
    background-color: #fff;
}

#sectionsTable.table-striped tbody tr:nth-of-type(odd) td,
#sectionsTable.table-striped tbody tr:nth-of-type(even) td {
    background-color: #fff;
}

/* Row hover */
#sectionsTable.table-hover tbody tr:hover td {
    background-color: #f7faf9 !important;
}

/* Last row */
#sectionsTable tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   COLUMN ADJUSTMENTS
========================================================= */

/* ID */
#sectionsTable th:nth-child(1),
#sectionsTable td:nth-child(1) {
    width: 55px;
    text-align: center;
}

/* Section name */
#sectionsTable th:nth-child(2),
#sectionsTable td:nth-child(2) {
    min-width: 150px;
}

/* Description */
#sectionsTable th:nth-child(3),
#sectionsTable td:nth-child(3) {
    min-width: 250px;
}

/* Entry Date */
#sectionsTable th:nth-child(4),
#sectionsTable td:nth-child(4) {
    min-width: 135px;
    white-space: nowrap;
}

/* Status */
#sectionsTable th:nth-child(5),
#sectionsTable td:nth-child(5) {
    width: 100px;
    text-align: center;
}

/* Actions */
#sectionsTable th:nth-child(6),
#sectionsTable td:nth-child(6) {
    width: 180px;
    text-align: center;
    white-space: nowrap;
}


/* =========================================================
   STATUS BADGES
========================================================= */

#sectionsTable .badge {
    display: inline-block;

    min-width: 62px;

    padding: 5px 10px;

    border-radius: 3px;

    font-size: 11px;
    font-weight: 500;

    line-height: 1.2;
}

/* Active */
#sectionsTable .badge-success {
    color: #247d72 !important;
    background: #e8f4f1 !important;
    border: 1px solid #c8e3de;
}

/* Inactive */
#sectionsTable .badge-danger {
    color: #c94c4c !important;
    background: #fceeee !important;
    border: 1px solid #f2cccc;
}


/* =========================================================
   ACTION BUTTONS
========================================================= */

#sectionsTable .edit-btn,
#sectionsTable .delete-btn {
    width: auto !important;
    min-width: 65px;

    height: 28px;

    margin: 0 3px !important;
    padding: 4px 10px;

    border-radius: 3px;

    font-size: 11px;
    font-weight: 400;

    line-height: 18px;

    box-shadow: none !important;
}

/* Edit */
#sectionsTable .edit-btn {
    color: #247d72 !important;

    background: #ffffff !important;

    border: 1px solid #247d72 !important;
}

#sectionsTable .edit-btn:hover {
    color: #ffffff !important;

    background: #247d72 !important;
}

/* Delete */
#sectionsTable .delete-btn {
    color: #c94c4c !important;

    background: #ffffff !important;

    border: 1px solid #c94c4c !important;
}

#sectionsTable .delete-btn:hover {
    color: #ffffff !important;

    background: #c94c4c !important;
}

/* Icons */
#sectionsTable .edit-btn i,
#sectionsTable .delete-btn i {
    margin-right: 3px;
    font-size: 10px;
}


/* =========================================================
   DATATABLE BOTTOM
   Showing entries + Pagination
========================================================= */

#sectionsTable_wrapper .dataTables_info {
    padding-top: 16px;

    color: #666;

    font-size: 12px;
}

#sectionsTable_wrapper .dataTables_paginate {
    padding-top: 10px;
}

/* Pagination buttons */
#sectionsTable_wrapper .dataTables_paginate .paginate_button {
    min-width: 31px;

    margin-left: 4px !important;
    padding: 5px 9px !important;

    border: 1px solid #dddddd !important;
    border-radius: 3px !important;

    background: #fff !important;

    color: #555 !important;

    font-size: 11px;

    box-shadow: none !important;
}

/* Hover */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button:hover {
    color: #247d72 !important;

    background: #f3f8f7 !important;

    border-color: #247d72 !important;
}

/* Current page */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.current,
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.current:hover {
    color: #fff !important;

    background: #247d72 !important;

    border-color: #247d72 !important;
}

/* Disabled */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.disabled,
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.disabled:hover {
    color: #aaa !important;

    background: #fff !important;

    border-color: #e5e5e5 !important;

    cursor: default;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767.98px) {

    #sectionsTable_wrapper .dataTables_length,
    #sectionsTable_wrapper .dataTables_filter {
        float: none;
        width: 100%;
        text-align: left;
    }

    #sectionsTable_wrapper .dataTables_filter {
        margin-top: 10px;
    }

    #sectionsTable_wrapper .dataTables_filter input {
        width: calc(100% - 55px);
        max-width: 250px;
    }

    #sectionsTable_wrapper .dataTables_info,
    #sectionsTable_wrapper .dataTables_paginate {
        float: none;
        width: 100%;
        text-align: left;
    }

    #sectionsTable_wrapper .dataTables_paginate {
        margin-top: 7px;
    }

    #sectionsTable {
        min-width: 850px;
    }
}


/* =========================================================
   MANAGE EXAM SECTIONS — EXACT FIGMA STYLE OVERRIDE
   Figma Frame: Add Exam Section / 527:2

   STYLE ONLY
   Existing Blade / PHP / DataTable / AJAX / JS unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

:root {
    --exam-section-green: #1F776E;
    --exam-section-green-dark: #17675F;

    --exam-section-text: #252525;
    --exam-section-heading: #111827;
    --exam-section-muted: #6B7280;

    --exam-section-border: #D9D9D9;
    --exam-section-light-border: #E5E7EB;

    --exam-section-soft-green: rgba(31, 119, 110, 0.10);

    --exam-section-success: #10B981;
    --exam-section-success-bg: rgba(16, 185, 129, 0.10);

    --exam-section-danger: #EF4444;
    --exam-section-danger-bg: rgba(239, 68, 68, 0.10);

    --exam-section-action-bg: #F3F4F6;

    --exam-section-white: #FFFFFF;
}


/* =========================================================
   2. PAGE
   ========================================================= */

.main-content {
    padding-left: 12px !important;
    padding-right: 12px !important;

    background:
        linear-gradient(
            to left,
            rgba(23, 103, 95, 0.15),
            rgba(255, 255, 255, 0.15)
        ) !important;

    font-family: 'Montserrat', sans-serif !important;
}


.main-content *,
.main-content input,
.main-content select,
.main-content textarea,
.main-content button {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   3. PAGE TITLE
   Figma = 20px SemiBold
   ========================================================= */

.profile-overview-title {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #252525 !important;
}


/* Keep heading spacing close to Figma */
div:has(> .profile-overview-title) {
    margin-bottom: 30px !important;
}


/* =========================================================
   4. REMOVE OLD BOOTSTRAP CARD LOOK

   Figma has:
   - one white form card
   - separate Added Sections area
   ========================================================= */

.card:has(#sectionForm) {
    width: 100% !important;

    margin: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    overflow: visible !important;
}


.card:has(#sectionForm) > .card-body {
    padding: 0 !important;

    background: transparent !important;
}


/* Old divider not present in Figma */
.card:has(#sectionForm) .card-body > hr {
    display: none !important;
}


/* =========================================================
   5. FORM CARD
   Figma:
   white
   radius 5px
   approximately 378px height
   ========================================================= */

#sectionForm {
    width: 100% !important;

    min-height: 378px !important;

    margin: 0 0 30px !important;
    padding: 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


/* =========================================================
   6. FORM GRID
   Figma = two equal columns
   ========================================================= */

#sectionForm .row {
    margin-left: -9px !important;
    margin-right: -9px !important;
}


#sectionForm .row > [class*="col-"] {
    padding-left: 9px !important;
    padding-right: 9px !important;
}


#sectionForm .form-group {
    margin-bottom: 20px !important;
}


/* =========================================================
   7. LABELS
   Figma = Montserrat Medium 16px
   ========================================================= */

#sectionForm .form-group label {
    display: block !important;

    margin: 0 0 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    color: #000000 !important;
}


/* Required * */
#sectionForm label .text-danger {
    color: #000000 !important;

    font-size: inherit !important;
    font-weight: inherit !important;
}


/* =========================================================
   8. ALL FORM CONTROLS
   Figma = 40px / 5px radius / #D9D9D9
   ========================================================= */

#sectionForm .form-control {
    width: 100% !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    transition:
        border-color .15s ease,
        box-shadow .15s ease !important;
}


#sectionForm .form-control:focus {
    outline: 0 !important;

    border-color: #1F776E !important;

    box-shadow:
        0 0 0 3px rgba(31, 119, 110, .08) !important;
}


#sectionForm .form-control::placeholder {
    color: #9F9F9F !important;

    opacity: 1;
}


/* =========================================================
   9. SECTION NAME
   Existing textarea stays textarea/functionally unchanged
   Figma visual height = 40px
   ========================================================= */

#section_name {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 9px 12px !important;

    resize: none !important;

    overflow-y: auto !important;

    line-height: 20px !important;
}


/* =========================================================
   10. STATUS
   ========================================================= */

#status {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 0 36px 0 12px !important;

    background-color: #FFFFFF !important;

    cursor: pointer;
}


/* =========================================================
   11. DESCRIPTION
   Exact Figma height = 150px
   ========================================================= */

#section_description {
    width: 100% !important;

    height: 150px !important;
    min-height: 150px !important;

    padding: 12px !important;

    resize: none !important;

    line-height: 1.5 !important;
}


/* =========================================================
   12. CHARACTER COUNTERS

   Not visible in Figma.
   Functionality remains in JS.
   ========================================================= */

#sectionForm small.text-muted {
    /* display: none !important; */
}


/* =========================================================
   13. BUTTON ROW
   ========================================================= */

#sectionForm .form-group.mt-4 {
    margin-top: 0 !important;
    margin-bottom: 0 !important;

    display: flex !important;
    align-items: center !important;

    gap: 8px !important;
}


/* Common Figma buttons */
#sectionForm #submitBtn,
#sectionForm #resetBtn {
    height: 50px !important;
    min-height: 50px !important;

    margin: 0 !important;
    padding: 0 20px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    letter-spacing: 0 !important;

    transform: none !important;
}


/* =========================================================
   14. ADD SECTION
   Exact Figma = 172 x 50
   ========================================================= */

#sectionForm #submitBtn {
    width: 172px !important;
    min-width: 172px !important;

    background: #1F776E !important;

    border: 1px solid #1F776E !important;

    color: #FFFFFF !important;
}


#sectionForm #submitBtn:hover,
#sectionForm #submitBtn:focus {
    background: #17675F !important;

    border-color: #17675F !important;

    color: #FFFFFF !important;

    box-shadow: none !important;
}


/* =========================================================
   15. RESET
   Exact Figma = 136 x 50
   ========================================================= */

#sectionForm #resetBtn {
    width: 136px !important;
    min-width: 136px !important;

    background: #FFFFFF !important;

    border: 1px solid #17675F !important;

    color: #17675F !important;
}


#sectionForm #resetBtn:hover,
#sectionForm #resetBtn:focus {
    background: rgba(23, 103, 95, .05) !important;

    border-color: #17675F !important;

    color: #17675F !important;
}


/* Icons not present on Figma buttons */
#sectionForm #submitBtn > i,
#sectionForm #resetBtn > i {
    /* display: none !important; */
}


/* Disabled / processing */
#sectionForm #submitBtn:disabled {
    opacity: .65 !important;

    cursor: not-allowed !important;
}


/* =========================================================
   16. TABLE AREA
   ========================================================= */

.table-responsive {
    width: 100% !important;

    margin: 0 !important;

    overflow-x: auto !important;

    background: transparent !important;

    border: 0 !important;
}


/* =========================================================
   17. DATATABLE WRAPPER

   Creates Figma "Added Sections" header without
   modifying Blade / JS.
   ========================================================= */

#sectionsTable_wrapper {
    position: relative !important;

    width: 100% !important;

    padding-top: 57px !important;

    background: transparent !important;

    font-family: 'Montserrat', sans-serif !important;

    color: #252525 !important;
}


/* Figma dark green Added Sections bar */
#sectionsTable_wrapper::before {
    content: "Added Sections";

    position: absolute !important;

    top: 0 !important;
    left: 0 !important;
    right: 0 !important;

    height: 57px !important;

    padding: 0 20px !important;

    display: flex !important;
    align-items: center !important;

    background: #1F776E !important;

    border-radius: 12px 12px 0 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;

    z-index: 1;
}


/* =========================================================
   18. SEARCH
   Figma = white 255 x 40 inside green bar
   ========================================================= */

#sectionsTable_wrapper .dataTables_filter {
    position: absolute !important;

    top: 8px !important;
    right: 14px !important;

    z-index: 5 !important;

    float: none !important;

    margin: 0 !important;

    text-align: right !important;
}


#sectionsTable_wrapper .dataTables_filter label {
    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 0 !important;

    color: transparent !important;
}


#sectionsTable_wrapper .dataTables_filter input {
    width: 255px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 38px 0 14px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;

    outline: none !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* Search icon */
#sectionsTable_wrapper .dataTables_filter label {
    position: relative !important;
}


#sectionsTable_wrapper .dataTables_filter label::after {
    content: "\f002";

    position: absolute;

    top: 50%;
    right: 13px;

    transform: translateY(-50%);

    font-family: FontAwesome;

    font-size: 14px;

    color: #252525;

    pointer-events: none;
}


#sectionsTable_wrapper .dataTables_filter input:focus {
    border-color: #D9D9D9 !important;

    box-shadow: none !important;
}


/* =========================================================
   19. TABLE
   ========================================================= */

#sectionsTable {
    width: 100% !important;

    margin: 0 !important;

    table-layout: fixed !important;

    border: 0 !important;

    border-collapse: collapse !important;

    background: transparent !important;

    border-radius: 0 !important;

    overflow: visible !important;
}


/* =========================================================
   20. FIGMA TABLE HEADER
   39px soft green
   ========================================================= */

#sectionsTable thead,
#sectionsTable thead.bg-primary {
    background: rgba(31, 119, 110, 0.10) !important;
}


#sectionsTable thead tr {
    height: 39px !important;

    background: rgba(31, 119, 110, 0.10) !important;
}


#sectionsTable thead th {
    height: 39px !important;

    padding: 0 12px !important;

    vertical-align: middle !important;

    background: rgba(31, 119, 110, 0.10) !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    color: #1F776E !important;

    text-transform: uppercase !important;

    white-space: nowrap !important;
}


/* Remove old white inline color */
#sectionsTable thead th[style] {
    color: #1F776E !important;
}


/* Sorting icon spacing */
#sectionsTable thead th.sorting,
#sectionsTable thead th.sorting_asc,
#sectionsTable thead th.sorting_desc {
    padding-right: 24px !important;
}


/* =========================================================
   21. TABLE ROWS
   Figma = white 64px rows
   ========================================================= */

#sectionsTable tbody tr {
    height: 64px !important;

    background: transparent !important;
}


#sectionsTable tbody td {
    height: 64px !important;

    padding: 0 12px !important;

    vertical-align: middle !important;

    background: #FFFFFF !important;

    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;

    /*
       16px visual separation between rows,
       matching Figma.
    */
    border-bottom: 12px solid rgba(31, 119, 110, 0.045) !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #111827 !important;
}


/* No bootstrap zebra stripes */
#sectionsTable.table-striped tbody tr:nth-of-type(odd),
#sectionsTable.table-striped tbody tr:nth-of-type(even),
#sectionsTable.table-striped tbody tr:nth-of-type(odd) td,
#sectionsTable.table-striped tbody tr:nth-of-type(even) td {
    background: transparent !important;
}


#sectionsTable.table-striped tbody tr:nth-of-type(odd) td,
#sectionsTable.table-striped tbody tr:nth-of-type(even) td {
    background: #FFFFFF !important;
}


/* No hover color change in Figma */
#sectionsTable.table-hover tbody tr:hover td {
    background: #FFFFFF !important;
}


/* =========================================================
   22. COLUMN WIDTHS
   Matching Figma proportions
   ========================================================= */

#sectionsTable th:nth-child(1),
#sectionsTable td:nth-child(1) {
    width: 5% !important;

    text-align: left !important;
}


#sectionsTable th:nth-child(2),
#sectionsTable td:nth-child(2) {
    width: 40% !important;
}


#sectionsTable th:nth-child(3),
#sectionsTable td:nth-child(3) {
    width: 15% !important;
}


#sectionsTable th:nth-child(4),
#sectionsTable td:nth-child(4) {
    width: 14% !important;

    white-space: nowrap !important;
}


#sectionsTable th:nth-child(5),
#sectionsTable td:nth-child(5) {
    width: 11% !important;

    text-align: left !important;
}


#sectionsTable th:nth-child(6),
#sectionsTable td:nth-child(6) {
    width: 15% !important;

    text-align: left !important;

    white-space: nowrap !important;
}


/* Long text stays one-line like Figma */
#sectionsTable td:nth-child(2),
#sectionsTable td:nth-child(3) {
    overflow: hidden !important;

    white-space: nowrap !important;

    text-overflow: ellipsis !important;
}


/* =========================================================
   23. STATUS BADGES
   Exact Figma colors
   ========================================================= */

#sectionsTable .badge {
    width: auto !important;
    min-width: 64px !important;

    height: 20px !important;

    padding: 0 10px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    border: 0 !important;
    border-radius: 4px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1 !important;
}


/* Active */
#sectionsTable .badge-success {
    background: rgba(16, 185, 129, 0.10) !important;

    color: #10B981 !important;
}


/* Inactive */
#sectionsTable .badge-danger {
    min-width: 79px !important;

    background: rgba(239, 68, 68, 0.10) !important;

    color: #EF4444 !important;
}


/* =========================================================
   24. ACTION BUTTONS
   Figma = 32 x 32 grey icon tiles

   Existing Edit/Delete functionality stays unchanged.
   ========================================================= */

#sectionsTable .edit-btn,
#sectionsTable .delete-btn {
    width: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;

    height: 32px !important;
    min-height: 32px !important;

    margin: 0 4px 0 0 !important;
    padding: 0 !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    vertical-align: middle !important;

    background: #F3F4F6 !important;

    border: 0 !important;
    border-radius: 6px !important;

    box-shadow: none !important;

    font-size: 0 !important;

    color: #6B7280 !important;

    transform: none !important;
}


/* Icons remain visible */
#sectionsTable .edit-btn i,
#sectionsTable .delete-btn i {
    margin: 0 !important;

    font-size: 14px !important;

    color: #6B7280 !important;
}


/* Edit hover */
#sectionsTable .edit-btn:hover {
    background: #E5E7EB !important;

    color: #374151 !important;
}


#sectionsTable .edit-btn:hover i {
    color: #374151 !important;
}


/* Delete — keep destructive meaning */
#sectionsTable .delete-btn i {
    color: #EF4444 !important;
}


#sectionsTable .delete-btn:hover {
    background: rgba(239, 68, 68, .10) !important;
}


#sectionsTable .delete-btn:hover i {
    color: #EF4444 !important;
}


/* Remove bootstrap mb-2 */
#sectionsTable .edit-btn.mb-2,
#sectionsTable .delete-btn.mb-2 {
    margin-bottom: 0 !important;
}


/* =========================================================
   25. DATATABLE LENGTH
   Bottom-left Figma control
   ========================================================= */

#sectionsTable_wrapper .dataTables_length {
    float: left !important;

    margin: 2px 10px 0 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 0 !important;

    color: #252525 !important;
}


#sectionsTable_wrapper .dataTables_length label {
    margin: 0 !important;

    font-size: 0 !important;
}


#sectionsTable_wrapper .dataTables_length select {
    width: 110px !important;
    height: 30px !important;

    margin: 0 !important;
    padding: 0 28px 0 10px !important;

    background: #FFFFFF !important;

    border: 1px solid #252525 !important;
    border-radius: 50px !important;

    outline: none !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* =========================================================
   26. DATATABLE INFO
   ========================================================= */

#sectionsTable_wrapper .dataTables_info {
    float: left !important;

    padding: 8px 0 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* =========================================================
   27. PAGINATION
   Figma minimal text pagination
   ========================================================= */

#sectionsTable_wrapper .dataTables_paginate {
    float: right !important;

    padding-top: 3px !important;

    font-family: 'Montserrat', sans-serif !important;
}


#sectionsTable_wrapper
.dataTables_paginate
.paginate_button {
    min-width: 26px !important;
    height: 26px !important;

    margin: 0 2px !important;
    padding: 4px 7px !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


/* Current page = green circle */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.current,

#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.current:hover {
    min-width: 22px !important;
    width: 22px !important;
    height: 22px !important;

    padding: 3px !important;

    background: #1F776E !important;

    border: 0 !important;

    color: #FFFFFF !important;
}


/* Hover */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button:hover {
    background: rgba(31, 119, 110, .08) !important;

    border: 0 !important;

    color: #17675F !important;
}


/* Disabled */
#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.disabled,

#sectionsTable_wrapper
.dataTables_paginate
.paginate_button.disabled:hover {
    background: transparent !important;

    border: 0 !important;

    color: #9CA3AF !important;
}


/* Clear float after bottom controls */
#sectionsTable_wrapper::after {
    content: "";

    display: block;

    clear: both;
}


/* =========================================================
   28. INVALID FIELD
   ========================================================= */

#sectionForm .form-control.is-invalid,
#sectionForm .character-limit-reached {
    border-color: #EF4444 !important;

    box-shadow: 0 0 0 3px rgba(239, 68, 68, .06) !important;
}


/* =========================================================
   29. RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    #sectionsTable {
        min-width: 900px !important;
    }

}


@media (max-width: 767.98px) {

    .profile-overview-title {
        font-size: 20px !important;
    }


    #sectionForm {
        min-height: auto !important;

        padding: 16px !important;
    }


    #sectionForm .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }


    #sectionForm .row > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    #sectionForm .form-group.mt-4 {
        display: grid !important;

        grid-template-columns: 1fr 1fr !important;

        gap: 8px !important;
    }


    #sectionForm #submitBtn,
    #sectionForm #resetBtn {
        width: 100% !important;
        min-width: 100% !important;
    }


    #sectionsTable_wrapper {
        padding-top: 112px !important;
    }


    #sectionsTable_wrapper::before {
        height: 57px !important;
    }


    #sectionsTable_wrapper .dataTables_filter {
        top: 65px !important;
        left: 0 !important;
        right: 0 !important;

        width: 100% !important;
    }


    #sectionsTable_wrapper .dataTables_filter label {
        width: 100% !important;
    }


    #sectionsTable_wrapper .dataTables_filter input {
        width: 100% !important;
    }


    #sectionsTable_wrapper .dataTables_length,
    #sectionsTable_wrapper .dataTables_info,
    #sectionsTable_wrapper .dataTables_paginate {
        float: none !important;

        width: 100% !important;

        margin-top: 10px !important;

        text-align: left !important;
    }

}


/* =========================================================
   FINAL FIGMA MATCH — MANAGE EXAM SECTIONS
   Figma node: 527:2 (Add Exam Section)
   STYLE ONLY — Blade / PHP / AJAX / DataTable logic unchanged
   ========================================================= */

/* Keep Font Awesome icons working despite Montserrat override */
.main-content .fa,
.main-content i.fa,
.main-content .fas,
.main-content .far {
    font-family: FontAwesome !important;
    font-style: normal !important;
}

/* Page/content tone from Figma */
.main-content {
    color: #252525 !important;
    background: linear-gradient(
        to left,
        rgba(23, 103, 95, 0.15),
        rgba(255, 255, 255, 0.15)
    ) !important;
}

.profile-overview-title {
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;
    color: #252525 !important;
}

div:has(> .profile-overview-title) {
    margin-bottom: 30px !important;
}

/* Strip Bootstrap card chrome; Figma uses one clean white form panel */
.card:has(#sectionForm),
.card:has(#sectionForm) > .card-body {
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
}

.card:has(#sectionForm) .card-body > hr {
    display: none !important;
}

#sectionForm {
    width: 100% !important;
    min-height: 378px !important;
    margin: 0 0 30px !important;
    padding: 20px !important;
    background: #fff !important;
    border: 0 !important;
    border-radius: 5px !important;
    box-shadow: none !important;
}

#sectionForm .row {
    margin-left: -13px !important;
    margin-right: -13px !important;
}

#sectionForm .row > [class*="col-"] {
    padding-left: 13px !important;
    padding-right: 13px !important;
}

#sectionForm .form-group {
    margin-bottom: 20px !important;
}

#sectionForm .form-group label {
    display: block !important;
    margin: 0 0 8px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.15 !important;
    letter-spacing: 0 !important;
    color: #000 !important;
}

#sectionForm label .text-danger {
    color: #000 !important;
}

#sectionForm .form-control {
    width: 100% !important;
    background: #fff !important;
    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #252525 !important;
}

#sectionForm .form-control:focus {
    outline: 0 !important;
    border-color: #1F776E !important;
    box-shadow: 0 0 0 3px rgba(31,119,110,.08) !important;
}

/* Existing textarea remains functionally unchanged, visually matches Figma input */
#section_name,
#status {
    height: 40px !important;
    min-height: 40px !important;
}

#section_name {
    padding: 9px 12px !important;
    resize: none !important;
    overflow-y: auto !important;
    line-height: 20px !important;
}

#status {
    padding: 0 36px 0 12px !important;
    cursor: pointer !important;
}

#section_description {
    height: 150px !important;
    min-height: 150px !important;
    padding: 12px !important;
    resize: none !important;
    line-height: 1.5 !important;
}

/* Counters keep JS functionality but are not visible in Figma */
#sectionForm small.text-muted {
    display: none !important;
}

#sectionForm .form-group.mt-4 {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

#sectionForm #submitBtn,
#sectionForm #resetBtn {
    height: 50px !important;
    min-height: 50px !important;
    margin: 0 !important;
    padding: 0 20px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 5px !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
    transform: none !important;
}

#sectionForm #submitBtn {
    width: 172px !important;
    min-width: 172px !important;
    background: #1F776E !important;
    border: 1px solid #1F776E !important;
    color: #fff !important;
}

#sectionForm #submitBtn:hover,
#sectionForm #submitBtn:focus {
    background: #17675F !important;
    border-color: #17675F !important;
}

#sectionForm #resetBtn {
    width: 136px !important;
    min-width: 136px !important;
    background: #fff !important;
    border: 1px solid #17675F !important;
    color: #17675F !important;
}

#sectionForm #resetBtn:hover,
#sectionForm #resetBtn:focus {
    background: rgba(23,103,95,.05) !important;
    color: #17675F !important;
}

/* Figma buttons are text-only */
#sectionForm #submitBtn > i,
#sectionForm #resetBtn > i {
    display: none !important;
}

/* ===== Added Sections / DataTable ===== */
.table-responsive {
    width: 100% !important;
    margin: 0 !important;
    overflow-x: auto !important;
    background: transparent !important;
    border: 0 !important;
}

#sectionsTable_wrapper {
    position: relative !important;
    width: 100% !important;
    padding-top: 57px !important;
    padding-bottom: 55px !important;
    background: transparent !important;
    font-family: 'Montserrat', sans-serif !important;
    color: #252525 !important;
}

#sectionsTable_wrapper::before {
    content: "Added Sections";
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 57px !important;
    padding: 0 20px !important;
    display: flex !important;
    align-items: center !important;
    background: #1F776E !important;
    border-radius: 12px 12px 0 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    color: #fff !important;
    z-index: 1 !important;
}

/* Search inside green header */
#sectionsTable_wrapper .dataTables_filter {
    position: absolute !important;
    top: 8px !important;
    right: 20px !important;
    z-index: 5 !important;
    float: none !important;
    margin: 0 !important;
}

#sectionsTable_wrapper .dataTables_filter label {
    position: relative !important;
    margin: 0 !important;
    font-size: 0 !important;
    color: transparent !important;
}

#sectionsTable_wrapper .dataTables_filter input {
    width: 255px !important;
    height: 40px !important;
    margin: 0 !important;
    padding: 0 40px 0 18px !important;
    background: #fff !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;
    outline: 0 !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #252525 !important;
}

#sectionsTable_wrapper .dataTables_filter label::after {
    content: "\f002";
    position: absolute !important;
    top: 50% !important;
    right: 14px !important;
    transform: translateY(-50%) !important;
    font-family: FontAwesome !important;
    font-size: 14px !important;
    color: #252525 !important;
    pointer-events: none !important;
}

/* Table */
#sectionsTable {
    width: 100% !important;
    margin: 0 !important;
    table-layout: fixed !important;
    border: 0 !important;
    border-collapse: collapse !important;
    background: transparent !important;
}

#sectionsTable thead,
#sectionsTable thead.bg-primary,
#sectionsTable thead tr {
    height: 39px !important;
    background: rgba(31,119,110,.10) !important;
}

#sectionsTable thead th,
#sectionsTable thead th[style] {
    height: 39px !important;
    padding: 0 12px !important;
    vertical-align: middle !important;
    background: rgba(31,119,110,.10) !important;
    border: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    letter-spacing: 0 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    color: #1F776E !important;
}

#sectionsTable tbody tr {
    height: 80px !important;
    background: transparent !important;
}

#sectionsTable tbody td,
#sectionsTable.table-striped tbody tr:nth-of-type(odd) td,
#sectionsTable.table-striped tbody tr:nth-of-type(even) td,
#sectionsTable.table-hover tbody tr:hover td {
    height: 64px !important;
    padding: 0 12px !important;
    vertical-align: middle !important;
    background: #fff !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    /* border-bottom: 16px solid rgba(31,119,110,.045) !important; */
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
    color: #111827 !important;
}

/* Figma column proportions */
#sectionsTable th:nth-child(1), #sectionsTable td:nth-child(1) { width: 5% !important; text-align: left !important; }
#sectionsTable th:nth-child(2), #sectionsTable td:nth-child(2) { width: 40% !important; }
#sectionsTable th:nth-child(3), #sectionsTable td:nth-child(3) { width: 15% !important; }
#sectionsTable th:nth-child(4), #sectionsTable td:nth-child(4) { width: 14% !important; white-space: nowrap !important; }
#sectionsTable th:nth-child(5), #sectionsTable td:nth-child(5) { width: 11% !important; text-align: left !important; }
#sectionsTable th:nth-child(6), #sectionsTable td:nth-child(6) { width: 15% !important; text-align: left !important; white-space: nowrap !important; }

#sectionsTable td:nth-child(2),
#sectionsTable td:nth-child(3) {
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}

/* Status chips */
#sectionsTable .badge {
    width: auto !important;
    min-width: 64px !important;
    height: 20px !important;
    padding: 0 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: 0 !important;
    border-radius: 4px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1 !important;
}

#sectionsTable .badge-success {
    background: rgba(16,185,129,.10) !important;
    color: #10B981 !important;
}

#sectionsTable .badge-danger {
    min-width: 79px !important;
    background: rgba(239,68,68,.10) !important;
    color: #EF4444 !important;
}

/* 32x32 action tiles */
#sectionsTable .edit-btn,
#sectionsTable .delete-btn {
    width: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;
    height: 32px !important;
    min-height: 32px !important;
    margin: 0 8px 0 0 !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    vertical-align: middle !important;
    background: #F3F4F6 !important;
    border: 0 !important;
    border-radius: 6px !important;
    box-shadow: none !important;
    font-size: 0 !important;
    color: #6B7280 !important;
}

#sectionsTable .edit-btn i,
#sectionsTable .delete-btn i {
    margin: 0 !important;
    font-family: FontAwesome !important;
    font-size: 14px !important;
    color: #6B7280 !important;
}

#sectionsTable .edit-btn:hover,
#sectionsTable .delete-btn:hover {
    background: #E5E7EB !important;
}

#sectionsTable .delete-btn:hover i {
    color: #EF4444 !important;
}

/* Bottom controls exactly under the table */
#sectionsTable_wrapper .dataTables_length {
    position: absolute !important;
    left: 0 !important;
    bottom: 0 !important;
    float: none !important;
    margin: 0 !important;
    font-size: 0 !important;
}

#sectionsTable_wrapper .dataTables_length label {
    margin: 0 !important;
    font-size: 0 !important;
}

#sectionsTable_wrapper .dataTables_length select {
    width: 110px !important;
    height: 30px !important;
    margin: 0 !important;
    padding: 0 28px 0 10px !important;
    background: #fff !important;
    border: 1px solid #252525 !important;
    border-radius: 50px !important;
    outline: 0 !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #252525 !important;
}

#sectionsTable_wrapper .dataTables_info {
    position: absolute !important;
    left: 125px !important;
    bottom: 4px !important;
    float: none !important;
    padding: 0 !important;
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #252525 !important;
}

#sectionsTable_wrapper .dataTables_paginate {
    position: absolute !important;
    right: 0 !important;
    bottom: 0 !important;
    float: none !important;
    padding: 0 !important;
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
}

#sectionsTable_wrapper .dataTables_paginate .paginate_button {
    min-width: 26px !important;
    height: 26px !important;
    margin: 0 2px !important;
    padding: 4px 7px !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 50% !important;
    box-shadow: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #252525 !important;
}

#sectionsTable_wrapper .dataTables_paginate .paginate_button.current,
#sectionsTable_wrapper .dataTables_paginate .paginate_button.current:hover {
    min-width: 22px !important;
    width: 22px !important;
    height: 22px !important;
    padding: 3px !important;
    background: #1F776E !important;
    border: 0 !important;
    color: #fff !important;
}

#sectionsTable_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(31,119,110,.08) !important;
    border: 0 !important;
    color: #17675F !important;
}

#sectionsTable_wrapper .dataTables_paginate .paginate_button.disabled,
#sectionsTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    background: transparent !important;
    color: #9CA3AF !important;
}

@media (max-width: 991px) {
    #sectionsTable { min-width: 900px !important; }
}

@media (max-width: 767.98px) {
    #sectionForm {
        min-height: auto !important;
        padding: 16px !important;
    }

    #sectionForm .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #sectionForm .row > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #sectionForm .form-group.mt-4 {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
    }

    #sectionForm #submitBtn,
    #sectionForm #resetBtn {
        width: 100% !important;
        min-width: 100% !important;
    }

    #sectionsTable_wrapper {
        padding-top: 112px !important;
        padding-bottom: 130px !important;
    }

    #sectionsTable_wrapper .dataTables_filter {
        top: 65px !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
    }

    #sectionsTable_wrapper .dataTables_filter label,
    #sectionsTable_wrapper .dataTables_filter input {
        width: 100% !important;
    }

    #sectionsTable_wrapper .dataTables_length,
    #sectionsTable_wrapper .dataTables_info,
    #sectionsTable_wrapper .dataTables_paginate {
        position: static !important;
        width: 100% !important;
        margin-top: 10px !important;
        text-align: left !important;
    }
}
/* =========================================================
   DATATABLE CONTROLS FINAL FIX
   Search + items/count + pagination
   ========================================================= */

/*
 * DataTables Bootstrap creates:
 * .row > .col-md-* > .dataTables_filter etc.
 *
 * Absolute controls ko actual wrapper ke relative karo.
 */
#sectionsTable_wrapper {
    position: relative !important;
    padding-top: 57px !important;
    padding-bottom: 70px !important;
}

/* Bootstrap columns absolute positioning ko capture na karein */
#sectionsTable_wrapper > .row > [class*="col-"] {
    position: static !important;
}

/* Remove Bootstrap row negative spacing */
#sectionsTable_wrapper > .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

#sectionsTable_wrapper > .row > [class*="col-"] {
    padding-left: 0 !important;
    padding-right: 0 !important;
}


/* =========================================================
   SEARCH
   Properly inside Added Sections green header
   ========================================================= */

#sectionsTable_wrapper .dataTables_filter {
    position: absolute !important;

    top: 8px !important;
    right: 20px !important;

    width: auto !important;

    margin: 0 !important;
    padding: 0 !important;

    float: none !important;

    z-index: 20 !important;
}

#sectionsTable_wrapper .dataTables_filter label {
    position: relative !important;

    display: block !important;

    margin: 0 !important;

    font-size: 0 !important;
}

#sectionsTable_wrapper .dataTables_filter input {
    width: 255px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 42px 0 15px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;

    outline: none !important;
    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;

    color: #252525 !important;
}

/* Search icon */
#sectionsTable_wrapper .dataTables_filter label::after {
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


/* =========================================================
   ITEMS PER PAGE
   ========================================================= */

#sectionsTable_wrapper .dataTables_length {
    position: absolute !important;

    left: 20px !important;
    bottom: 15px !important;

    width: auto !important;

    margin: 0 !important;

    float: none !important;

    font-size: 14px !important;
}

#sectionsTable_wrapper .dataTables_length label {
    margin: 0 !important;

    display: flex !important;
    align-items: center !important;

    gap: 8px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

#sectionsTable_wrapper .dataTables_length select {
    width: 70px !important;
    height: 32px !important;

    margin: 0 !important;
    padding: 0 26px 0 10px !important;

    background-color: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;

    color: #252525 !important;

    outline: none !important;
    box-shadow: none !important;
}


/* =========================================================
   SHOWING COUNT / INFO
   ========================================================= */

#sectionsTable_wrapper .dataTables_info {
    position: absolute !important;

    left: 240px !important;
    bottom: 21px !important;

    width: auto !important;

    margin: 0 !important;
    padding: 0 !important;

    float: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    line-height: 1 !important;

    color: #6B7280 !important;
}


/* =========================================================
   PAGINATION
   Bootstrap .page-link is the actual visible element
   ========================================================= */

#sectionsTable_wrapper .dataTables_paginate {
    position: absolute !important;

    right: 20px !important;
    bottom: 12px !important;

    margin: 0 !important;
    padding: 0 !important;

    float: none !important;
}

/* Bootstrap pagination wrapper */
#sectionsTable_wrapper .dataTables_paginate .pagination {
    display: flex !important;
    align-items: center !important;

    gap: 5px !important;

    margin: 0 !important;
    padding: 0 !important;
}

/* Remove styling from outer LI */
#sectionsTable_wrapper .dataTables_paginate .paginate_button,
#sectionsTable_wrapper .dataTables_paginate .page-item {
    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    box-shadow: none !important;
}

/* ACTUAL clickable page */
#sectionsTable_wrapper .dataTables_paginate .page-link {
    min-width: 30px !important;
    height: 30px !important;

    padding: 0 8px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}

/* Active page */
#sectionsTable_wrapper
.dataTables_paginate
.page-item.active
.page-link {

    width: 30px !important;
    min-width: 30px !important;

    background: #1F776E !important;

    border: 0 !important;
    border-radius: 50% !important;

    color: #FFFFFF !important;
}

/* Hover */
#sectionsTable_wrapper
.dataTables_paginate
.page-item:not(.active):not(.disabled)
.page-link:hover {

    background: rgba(31, 119, 110, 0.08) !important;

    color: #17675F !important;
}

/* Previous / Next */
#sectionsTable_wrapper
.dataTables_paginate
.page-item.previous
.page-link,

#sectionsTable_wrapper
.dataTables_paginate
.page-item.next
.page-link {

    width: auto !important;
    min-width: auto !important;

    padding: 0 10px !important;

    border-radius: 5px !important;
}

/* Disabled */
#sectionsTable_wrapper
.dataTables_paginate
.page-item.disabled
.page-link {

    background: transparent !important;

    color: #9CA3AF !important;

    opacity: .65 !important;
}


/* =========================================================
   DESKTOP SMALL SCREEN SAFETY
   ========================================================= */

@media (max-width: 1100px) {

    #sectionsTable_wrapper .dataTables_info {
        left: 160px !important;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    #sectionsTable_wrapper {
        padding-top: 112px !important;
        padding-bottom: 20px !important;
    }

    #sectionsTable_wrapper .dataTables_filter {
        top: 65px !important;
        left: 0 !important;
        right: 0 !important;

        width: 100% !important;
    }

    #sectionsTable_wrapper .dataTables_filter label,
    #sectionsTable_wrapper .dataTables_filter input {
        width: 100% !important;
    }

    #sectionsTable_wrapper .dataTables_length,
    #sectionsTable_wrapper .dataTables_info,
    #sectionsTable_wrapper .dataTables_paginate {
        position: static !important;

        width: 100% !important;

        margin-top: 12px !important;

        text-align: left !important;
    }

    #sectionsTable_wrapper .dataTables_info {
        padding-top: 5px !important;
    }

    #sectionsTable_wrapper .dataTables_paginate .pagination {
        justify-content: flex-start !important;

        flex-wrap: wrap !important;
    }
}
/* Fix gap between entries selector and showing info */

#sectionsTable_wrapper .dataTables_length {
    left: 20px !important;
    white-space: nowrap !important;
}

#sectionsTable_wrapper .dataTables_length label {
    white-space: nowrap !important;
}

#sectionsTable_wrapper .dataTables_info {
    left: 330px !important;
    white-space: nowrap !important;
}
/* =========================================
   TABLE BODY - CONSISTENT FIGMA ROWS
   ========================================= */

#sectionsTable {
    border-collapse: separate !important;
    border-spacing: 0 12px !important;
    background: transparent !important;
}

/* Remove fake gap from individual cells */
#sectionsTable tbody td,
#sectionsTable.table-striped tbody tr:nth-of-type(odd) td,
#sectionsTable.table-striped tbody tr:nth-of-type(even) td,
#sectionsTable.table-hover tbody tr:hover td {
    height: 64px !important;

    padding: 0 12px !important;

    background: #FFFFFF !important;

    border: 0 !important;

    vertical-align: middle !important;

    color: #111827 !important;
}

/* Every row exactly same */
#sectionsTable tbody tr,
#sectionsTable.table-striped tbody tr:nth-of-type(odd),
#sectionsTable.table-striped tbody tr:nth-of-type(even),
#sectionsTable.table-hover tbody tr:hover {
    height: 64px !important;

    background: #FFFFFF !important;
}

/* Rounded first / last cells */
#sectionsTable tbody tr td:first-child {
    border-radius: 5px 0 0 5px !important;
}

#sectionsTable tbody tr td:last-child {
    border-radius: 0 5px 5px 0 !important;
}

#sectionsTable thead th, #sectionsTable thead th[style]
 {
    height: 39px !important;
    padding: 0 12px !important;
    vertical-align: middle !important;
    background: rgb(31 119 110) !important;
    color: white !important;
    border: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    letter-spacing: 0 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    color: #FFF !important;
}

#sectionsTable_wrapper::before {
    content: "Added Sections";
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 57px !important;
    padding: 0 20px !important;
    padding-left:0 !important;
    display: flex !important;
    align-items: center !important;
    background: none !important;
    border-radius: 12px 12px 0 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    color: #252525 !important;
    z-index: 1 !important;
}
#sectionsTable_wrapper .dataTables_length select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;

    padding-right: 28px !important;

    background-color: #ffffff !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7' fill='none'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23252525' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 10px center !important;
    background-size: 12px 7px !important;
}
</style>



{{-- cdn for jquery --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="{{ asset('js/vendors/jquery-3.5.1.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Character counter function
    function updateCharacterCounter(inputElement, counterElement) {
        const maxLength = inputElement.attr('maxlength');
        const currentLength = inputElement.val().length;
        const remaining = maxLength - currentLength;
        counterElement.text(remaining);
        
        // Add visual feedback when limit is reached
        if (remaining === 0) {
            inputElement.addClass('character-limit-reached');
        } else {
            inputElement.removeClass('character-limit-reached');
        }
    }

    // Initialize character counters
    $('#section_name, #section_description').on('input', function() {
        const counterId = '#' + $(this).attr('id') + '_counter';
        updateCharacterCounter($(this), $(counterId));
    });

    // Prevent typing when limit is reached
    $('#section_name, #section_description').on('keypress', function(e) {
        const maxLength = $(this).attr('maxlength');
        if ($(this).val().length >= maxLength) {
            e.preventDefault();
            showToast('warning', 'Character limit reached');
        }
    });

    // Prevent pasting more characters than allowed
    $('#section_name, #section_description').on('paste', function(e) {
        const maxLength = $(this).attr('maxlength');
        const pastedData = e.originalEvent.clipboardData.getData('text');
        const currentLength = $(this).val().length;
        
        if ((currentLength + pastedData.length) > maxLength) {
            e.preventDefault();
            showToast('warning', 'Pasted content would exceed character limit');
        }
    });

    // Initialize DataTable
    $('#sectionsTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10,
        "language": {
            "lengthMenu": "Show _MENU_ entries per page",
            "zeroRecords": "No sections found",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "No sections available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        }
    });

    // Form submission
    $('#sectionForm').on('submit', function(e) {
        // alert('1: Form submit event triggered');
        e.preventDefault();

        
        // Disable submit button and show loading state
        $('#submitBtn').prop('disabled', true);
        $('#btnText').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: "{{ route('manage.exam.section') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    showToast('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('error', response.message);
                    // Re-enable submit button
                    $('#submitBtn').prop('disabled', false);
                    updateButtonText();
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while processing your request.';
                
                if (xhr.status === 422) {
                    // Validation error
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Server error with message
                    errorMessage = xhr.responseJSON.message;
                }
                
                showToast('error', errorMessage);
                
                // Re-enable submit button
                $('#submitBtn').prop('disabled', false);
                updateButtonText();
            }
        });
    });

    // Edit button click
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var desc = $(this).data('desc');
        var status = $(this).data('status');

        // Populate form
        $('#section_id').val(id);
        $('#section_name').val(name);
        $('#section_description').val(desc);
        $('#status').val(status);

        // Update button text
        updateButtonText();

        // Smooth scroll to form
        $('html, body').animate({
            scrollTop: $("#sectionForm").offset().top - 100
        }, 500);

        // Focus on first input
        $('#section_name').focus();
    });

    // Reset button click
    $('#resetBtn').on('click', function() {
        resetForm();
    });

    // Delete button click
    $('.delete-btn').on('click', function() {
        if(!confirm('Are you sure you want to delete this section?')) return;

        var button = $(this);
        var id = button.data('id');
        
        // Disable delete button
        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('delete.exam.section') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id
            },
            success: function(response) {
                if(response.status === 'success') {
                    showToast('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('error', response.message);
                    // Re-enable delete button
                    button.prop('disabled', false);
                    button.html('<i class="fa fa-trash"></i> Delete');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while processing your request.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showToast('error', errorMessage);
                
                // Re-enable delete button
                button.prop('disabled', false);
                button.html('<i class="fa fa-trash"></i> Delete');
            }
        });
    });

    function updateButtonText() {
        var isEdit = $('#section_id').val() !== '';
        $('#btnText').text(isEdit ? 'Update Section' : 'Add Section');
    }

    function resetForm() {
        // Clear form
        $('#sectionForm')[0].reset();
        $('#section_id').val('');
        
        // Update button text
        updateButtonText();

        // Remove any validation styles
        $('.is-invalid').removeClass('is-invalid');
        
        // Focus on first input
        $('#section_name').focus();
    }
});
</script>
