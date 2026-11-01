@extends('layouts.app')
@section('title')
    BNU CMS
@stop

@section('content')

<style>
/* ═══════════════════════════════════════════════════════════════
   STUDENT RECORD MANAGEMENT — Redesigned UI
   ═══════════════════════════════════════════════════════════════ */

/* ---------- CSS Variables ---------- */
:root {
    --srm-primary: #4454c3;
    --srm-primary-light: #ebf2fa;
    --srm-primary-hover: #2b3890;
    --srm-accent: #38a169;
    --srm-accent-hover: #2f855a;
    --srm-danger: #e53e3e;
    --srm-text: #2d3748;
    --srm-text-muted: #718096;
    --srm-border: #e2e8f0;
    --srm-bg-page: #f7fafc;
    --srm-bg-card: #ffffff;
    --srm-bg-profile: #f0f5ff;
    --srm-radius: 10px;
    --srm-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
    --srm-shadow-lg: 0 4px 20px rgba(0,0,0,0.08);
    --srm-transition: 0.25s ease;
}

.invisible { display: none; }

/* ---------- Toast Notifications ---------- */
.success-flag,
.error-flag {
    position: fixed;
    top: 80px;
    right: -400px;
    color: #fff;
    padding: 14px 24px;
    box-shadow: var(--srm-shadow-lg);
    z-index: 1050;
    transition: right 0.4s cubic-bezier(0.22, 0.61, 0.36, 1);
    border-radius: 8px 0 0 8px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}
.success-flag { background-color: var(--srm-accent); }
.error-flag   { background-color: var(--srm-danger); }
.success-flag.show,
.error-flag.show { right: 0; }

/* ---------- Page Header ---------- */
.srm-page-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 28px 16px;
    border-bottom: 1px solid var(--srm-border);
}
.srm-page-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--srm-text);
    margin: 0;
    letter-spacing: -0.3px;
}
.srm-page-header .header-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--srm-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    flex-shrink: 0;
}

/* ---------- Main Card ---------- */
.srm-card {
    background: var(--srm-bg-card);
    border: 1px solid var(--srm-border);
    border-radius: var(--srm-radius);
    box-shadow: var(--srm-shadow);
    margin-bottom: 24px;
    overflow: hidden;
}
.srm-card-body {
    padding: 24px 28px;
}

/* ---------- Layout: Form + Profile Side by Side ---------- */
.srm-main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 992px) {
    .srm-main-grid {
        grid-template-columns: 1fr;
    }
}

/* ---------- Section Labels ---------- */
.srm-section-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--srm-primary);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--srm-primary-light);
    display: flex;
    align-items: center;
    gap: 8px;
}
.srm-section-label i {
    font-size: 14px;
}

/* ---------- Form Row ---------- */
.srm-form-row {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: 0 12px;
    align-items: center;
    margin-bottom: 14px;
}
.srm-form-row.wide-label {
    grid-template-columns: 180px 1fr;
}
@media (max-width: 576px) {
    .srm-form-row {
        grid-template-columns: 1fr;
        gap: 4px;
    }
}
.srm-form-row label {
    font-size: 13px;
    font-weight: 600;
    color: var(--srm-text);
    text-align: right;
    white-space: nowrap;
    margin: 0;
}
@media (max-width: 576px) {
    .srm-form-row label { text-align: left; }
}

/* ---------- Form Controls ---------- */
.srm-form-row .form-control,
.srm-form-row .custom-select {
    height: 38px;
    font-size: 13px;
    border: 1px solid var(--srm-border);
    border-radius: 6px;
    transition: border-color var(--srm-transition), box-shadow var(--srm-transition);
    background-color: #fff;
}
.srm-form-row .form-control:focus,
.srm-form-row .custom-select:focus {
    border-color: var(--srm-primary);
    box-shadow: 0 0 0 3px rgba(44,82,130,0.12);
}
.srm-form-row .form-control[readonly],
.srm-form-row .form-control[disabled] {
    background-color: #f7fafc;
    color: var(--srm-text-muted);
}

/* ---------- Enrollment Row (input + search btn) ---------- */
.srm-enrollment-group {
    display: flex;
    gap: 8px;
}
.srm-enrollment-group .form-control {
    flex: 1;
}
.srm-search-btn {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: var(--srm-accent);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background var(--srm-transition), transform 0.15s;
    flex-shrink: 0;
}
.srm-search-btn:hover {
    background: var(--srm-accent-hover);
    transform: scale(1.05);
}

/* ---------- Scholarship Type + Hostel Checkbox Row ---------- */
.srm-scholarship-row {
    display: flex;
    gap: 12px;
    align-items: center;
}
.srm-scholarship-row select {
    flex: 1;
}
.srm-hostel-check {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    background: var(--srm-primary-light);
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background var(--srm-transition);
    flex-shrink: 0;
}
.srm-hostel-check:hover {
    background: #dce6f5;
}
.srm-hostel-check label {
    font-size: 12px;
    font-weight: 600;
    color: var(--srm-primary);
    margin: 0;
    cursor: pointer;
}
.srm-hostel-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid var(--srm-primary);
    cursor: pointer;
    accent-color: var(--srm-primary);
}

/* ---------- Hostel Details Sub-Grid ---------- */
.srm-hostel-details {
    background: #fefcf3;
    border: 1px dashed #e2c97d;
    border-radius: 8px;
    padding: 16px;
    margin-top: 4px;
    margin-bottom: 14px;
}
.srm-hostel-details .srm-form-row {
    margin-bottom: 10px;
}
.srm-hostel-details .srm-form-row:last-child {
    margin-bottom: 0;
}
.srm-hostel-inline {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
@media (max-width: 576px) {
    .srm-hostel-inline {
        grid-template-columns: 1fr;
    }
}
.srm-hostel-inline-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.srm-hostel-inline-item label {
    text-align: left;
    font-size: 12px;
    color: var(--srm-text-muted);
}
#amount-range-message {
    font-size: 12px;
    color: var(--srm-primary);
    font-weight: 500;
    margin-top: 4px;
}

/* ---------- Profile Card ---------- */
.srm-profile-card {
    background: var(--srm-bg-profile);
    border: 1px solid #d1ddf0;
    border-radius: var(--srm-radius);
    padding: 16px 18px;
    height: 100%;
    min-height: 200px;
}
.srm-profile-card .srm-section-label {
    border-bottom-color: #c8d8ef;
}
.srm-profile-header-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}
.srm-profile-details {
    flex: 1;
    min-width: 0;
}
.srm-profile-row {
    display: flex;
    padding: 7px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    align-items: baseline;
}
.srm-profile-details .srm-profile-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.srm-profile-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--srm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 88px;
    flex-shrink: 0;
}
.srm-profile-value {
    font-size: 14px;
    color: var(--srm-text);
    font-weight: 500;
}
.srm-profile-scholarships {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(0,0,0,0.06);
}
.srm-profile-scholarships-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--srm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}
.srm-scholarship-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 5px 8px;
    margin-bottom: 4px;
    background: #fff;
    border: 1px solid #d1ddf0;
    border-radius: 6px;
    font-size: 12px;
}
.srm-scholarship-item:last-child {
    margin-bottom: 0;
}
.srm-scholarship-name {
    font-weight: 500;
    color: var(--srm-text);
}
.srm-scholarship-meta {
    font-size: 11px;
    font-weight: 700;
    color: var(--srm-primary);
    white-space: nowrap;
}
.srm-scholarship-item--zero {
    background: #f4f6f9;
    border-color: #e2e8f0;
    opacity: 0.75;
}
.srm-scholarship-item--zero .srm-scholarship-name,
.srm-scholarship-item--zero .srm-scholarship-meta {
    color: #9aa5b4;
    font-weight: 400;
}
/* ── Scholarships-to-assign table ── */
.srm-sch-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: #fff;
}
.srm-sch-table th,
.srm-sch-table td {
    padding: 8px 10px;
    border: 1px solid #e2e8f0;
    text-align: left;
    vertical-align: middle;
}
.srm-sch-table thead th {
    background: #eef2fb;
    color: var(--srm-primary);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.4px;
}
.srm-sch-table td.num,
.srm-sch-table th.num { text-align: right; white-space: nowrap; }
.srm-sch-table tbody tr:nth-child(even) { background: #f8faff; }
.srm-sch-table tfoot td {
    background: #eef2fb;
    font-weight: 700;
    color: #14305c;
}
.srm-sch-table tfoot td.ref {
    font-weight: 600;
    font-size: 11.5px;
    color: #5a6678;
    background: #fff;
}
.srm-sch-cat {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    background: var(--srm-primary);
    border-radius: 9px;
    padding: 1px 7px;
    margin-left: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.srm-sch-cat--hostel { background: #5a3ea0; }
.srm-row-btn {
    border: none;
    border-radius: 5px;
    padding: 3px 10px;
    font-size: 12px;
    cursor: pointer;
    margin-right: 4px;
}
.srm-row-btn.edit   { background: #e7effd; color: #1247a6; }
.srm-row-btn.delete { background: #fdeaea; color: #b3261e; }
.srm-profile-photo-wrap {
    flex-shrink: 0;
    text-align: center;
    padding-top: 2px;
}
.srm-profile-photo {
    width: 88px;
    height: 88px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(44,82,130,0.15);
    border: 2px solid #fff;
}

/* ---------- Submit Area ---------- */
.srm-submit-area {
    display: flex;
    justify-content: center;
    gap: 12px;
    padding-top: 20px;
    margin-top: 8px;
    border-top: 1px solid var(--srm-border);
}
.srm-submit-btn {
    padding: 10px 40px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    background: var(--srm-primary);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background var(--srm-transition), transform 0.15s, box-shadow var(--srm-transition);
    display: flex;
    align-items: center;
    gap: 8px;
}
.srm-submit-btn:hover {
    background: var(--srm-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(44,82,130,0.25);
}
/* Secondary action next to Save & Post: reset the page to its load state. */
.srm-clear-btn {
    padding: 10px 28px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    background: #fff;
    color: #555;
    border: 1px solid var(--srm-border);
    cursor: pointer;
    transition: background var(--srm-transition), transform 0.15s, box-shadow var(--srm-transition);
    display: flex;
    align-items: center;
    gap: 8px;
}
.srm-clear-btn:hover {
    background: #f3f4f6;
    color: #c0392b;
    border-color: #d9a3a3;
    transform: translateY(-1px);
}

/* ---------- Messages ---------- */
#processing_message {
    background-color: rgba(44,82,130,0.1) !important;
    color: var(--srm-primary) !important;
    border: 1px solid rgba(44,82,130,0.2);
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
    text-align: center;
    margin-top: 12px;
}
#error_message {
    border-radius: 8px;
    font-size: 14px;
    margin-top: 12px;
}
#success_message {
    border-radius: 8px;
    font-size: 14px;
    margin-top: 12px;
}

/* ---------- Filter Section ---------- */
.srm-filter-section {
    background: var(--srm-bg-page);
    border: 1px solid var(--srm-border);
    border-radius: var(--srm-radius);
    padding: 20px 24px;
    margin-top: 8px;
}
.srm-filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}
@media (max-width: 768px) {
    .srm-filter-grid {
        grid-template-columns: 1fr;
    }
}
.srm-filter-grid .form-control {
    height: 38px;
    font-size: 13px;
    border: 1px solid var(--srm-border);
    border-radius: 6px;
}
.srm-filter-grid label {
    font-size: 12px;
    font-weight: 600;
    color: var(--srm-text-muted);
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.srm-filter-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.srm-filter-btn {
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    background: var(--srm-primary);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background var(--srm-transition);
    white-space: nowrap;
}
.srm-filter-btn:hover {
    background: var(--srm-primary-hover);
}
.srm-sync-btn {
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    background: #ecc94b;
    color: #744210;
    border: none;
    cursor: pointer;
    transition: background var(--srm-transition);
    white-space: nowrap;
}
.srm-sync-btn:hover {
    background: #d69e2e;
}

/* ---------- Data Table Section ---------- */
.srm-table-wrapper {
    margin-top: 20px;
}
.srm-table-wrapper .card {
    border: 1px solid var(--srm-border);
    border-radius: var(--srm-radius);
    box-shadow: var(--srm-shadow);
}
.srm-table-wrapper .card-body {
    padding: 12px;
}

/* ---------- DataTables Overrides ---------- */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dt-buttons {
    display: inline-block;
    align-items: center;
}
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label {
    margin-top: 5px;
    font-size: 13px;
}
.dataTables_wrapper .dt-buttons {
    margin-right: 15px;
}

/* ---------- Filter Bubble ---------- */
.filter-bubble {
    background-color: var(--srm-primary);
    color: white;
    padding: 0 14px;
    border-radius: 15px;
    font-size: 12px;
    display: inline-block;
}
</style>

<?php
// $sch_type = array("Need Base","Merit Base","BSS Emp","BSS Emp Ward","BNU Emp","BNU Emp Ward","Nest","UMISAA","SHEHLA Siagol");
?>

<div id="success-flag" class="success-flag"></div>
<div id="error-flag" class="error-flag"></div>

<!-- ═══════════ MAIN CARD ═══════════ -->
<div class="srm-card">
    <div class="srm-page-header">
        <div class="header-icon"><i class="fa fa-graduation-cap"></i></div>
        <h3>Student Record Management</h3>
    </div>

    <div class="srm-card-body">
        <div class="srm-main-grid">

            <!-- ─── LEFT: FORM ─── -->
            <div>
                <form id="studentRecord" method="post" onsubmit="event.preventDefault(); autoSearch(this);">
                    @csrf

                    <div class="srm-section-label"><i class="fa fa-search"></i> Search &amp; Assign</div>

                    <!-- Session -->
                    <div class="srm-form-row">
                        <label>Session</label>
                        <select class="custom-select form-control select2-show-search" name="academic_session" id="academic_session" data-placeholder="Select Session">
                            <option value=""></option>
                            @foreach ($academic_session as $row)
                            <option value="{{$row->sess_id}}">{{ $row->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student Type -->
                    <div class="srm-form-row">
                        <label>Student Type</label>
                        {{-- The three "New Admission <SEASON>" types are ENROLLED students:
                             they are searched by enrollment no exactly like Existing. The only
                             difference is that the fee structure is matched on
                             FFS_STUDENT_TYPE = that literal value instead of 'Existing'. --}}
                        <select class="custom-select form-control select2" name="student_type" id="studentType" data-placeholder="Student Type">
                            @if(auth()->user()->username == 'talha.sarfraz')
                                <option value="Existing">Existing</option>
                                <option value="New Admission FALL">New Admission FALL</option>
                                <option value="New Admission SPRING">New Admission SPRING</option>
                                <option value="New Admission SUMMER">New Admission SUMMER</option>
                            @elseif(auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik')
                                <option value="New without Registration">New without Registration</option>
                            @else
                                <option value="Existing">Existing</option>
                                <option value="New Admission FALL">New Admission FALL</option>
                                <option value="New Admission SPRING">New Admission SPRING</option>
                                <option value="New Admission SUMMER">New Admission SUMMER</option>
                                <option value="New without Registration">New without Registration</option>
                            @endif
                        </select>
                    </div>

                    <!-- Apply Id / Enrollment # -->
                    <div class="srm-form-row">
                        @if(auth()->user()->username == 'talha.sarfraz')
                            <label class="searchBy">Enrollment #</label>
                        @elseif(auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik')
                            <label class="searchBy">Apply Id</label>
                        @else
                            <label class="searchBy">Apply Id / Enroll #</label>
                        @endif
                        <div class="srm-enrollment-group">
                            <input class="form-control" onblur="session_enrollment_change()" type="text" name="sch_enrollment_no" id="sai_enrollment_no" autocomplete="off" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>">
                            <input hidden type="text" name="sch_aai_id" id="sch_aai_id" value="">
                            <button type="submit" class="srm-search-btn" onclick="getProfile()"><i class="fa fa-search"></i></button>
                        </div>
                    </div>

                    <!-- Scholarship Type + Hostel Checkbox -->
                    <div class="srm-form-row">
                        <label>Scholarship Type</label>
                        <div class="srm-scholarship-row">
                            <select disabled class="custom-select form-control select2-show-search" onchange="scholarshipChange()" name="sch_type" id="sch_type" data-placeholder="Select Scholarship Type">
                                <option value=""></option>
                                @php
                                    $allowed_users = ['saqib.jamil', 'esha.abid', 'asif_nadeem', 'ahmad.arfeen'];
                                    $restricted_RO_users = ['talha.sarfraz', 'abdul.waheed', 'haider.raza', 'abdul.qadeer', 'eportal.ro', 'aiman.adnan', 'hassan.farooq'];
                                    $username = auth()->user()->username;
                                    $is_allowed_user = in_array($username, $allowed_users);
                                    $is_restricted_user = in_array($username, $restricted_RO_users);
                                @endphp
                                @foreach ($Scholarship_Type as $row)
                                    @php
                                        $is_private = $row->scholarship_doner_type == 'private';
                                        $should_hide = $is_restricted_user && $row->scholarship_id >= 207 && $is_private;
                                    @endphp
                                    @if (!$should_hide)
                                        @if ($is_allowed_user && $is_private)
                                            <option value="{{ $row->scholarship_id }}" data-category="{{ strtolower($row->scholarship_desc) }}">{{ $row->scholarship_name }}</option>
                                        @elseif (!$is_allowed_user && !$is_private)
                                            <option value="{{ $row->scholarship_id }}" data-category="{{ strtolower($row->scholarship_desc) }}">{{ $row->scholarship_name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            <div class="srm-hostel-check">
                                <input type="checkbox" name="hostel_scholarship" id="hostel_scholarship" value="Yes">
                                <label for="hostel_scholarship">Hostel</label>
                            </div>
                        </div>
                    </div>

                    <!-- Challan Category (academic only) -->
                    <div class="srm-form-row acad-only" id="challan_category_row">
                        <label>Challan Category</label>
                        <select class="form-control" name="challan_category" id="challan_category" onchange="fetchAcademicFeeBase()">
                            <option value="Full_semester">Full Semester</option>
                            <option value="additional_Full_semester">Additional Credit Hr</option>
                            <option value="Credit_hours">Credit Hour</option>
                            <option value="Short/Summer_Course">Short / Summer Course</option>
                        </select>
                    </div>

                    <!-- Irreversible-category disclaimer. Visibility is driven entirely by
                         updateLockedCategoryDisclaimer() (NOT the .acad-only toggle). -->
                    <div id="locked_category_disclaimer" style="display:none;margin:6px 0 14px;padding:12px 14px;border:1px solid #f0b429;border-left:5px solid #f0b429;background:#fff8e6;color:#7a4b00;border-radius:4px;font-size:13px;line-height:1.55;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <strong>Important — this cannot be undone.</strong><br>
                        A scholarship applied against <strong><span id="locked_category_name">this category</span></strong>
                        <u>cannot be edited or deleted</u> once you click <strong>Save &amp; Post</strong>,
                        because the credit-hour challan is regenerated against it.
                        Please make sure the scholarship and percentage are correct before applying.
                    </div>

                    <!-- Enrolled Credit Hours (credit categories only) -->
                    <div class="srm-form-row" id="enrolled_credit_hours_row" style="display:none;">
                        <label>Enrolled Credit Hours</label>
                        <input type="text" class="form-control" id="enrolled_credit_hours" readonly>
                    </div>

                    <!-- Repeat Credit Hours: charged at HALF the per-credit rate.
                         Only shown when the session bills them and the student has some. -->
                    <div class="srm-form-row" id="repeat_credit_hours_row" style="display:none;">
                        <label>Repeat Credit Hours</label>
                        <input type="text" class="form-control" id="repeat_credit_hours" readonly title="Charged at half the per-credit fee">
                    </div>

                    <!-- Hostel Details (hidden by default) -->
                    <div class="d-none hotelSch-amount">
                        <div class="srm-hostel-details">
                            <div class="srm-form-row">
                                <label>Fee Structure</label>
                                <div>
                                    <select class="form-control" name="hostle_fee_structure" id="hostle_fee_structure" data-placeholder="Select Regular / Non Regular">
                                        <option value="Regular">ON Campus Regular</option>
                                        <option value="Non-Regular">On Campus Non-Regular</option>
                                        <option value="Regular_outside">Off Campus Regular Safari Campus</option>
                                        <option value="Non-Regular_outside">Off Campus Non-Regular Safari Campus</option>
                                    </select>
                                    <small id="hostel-assign-notice" class="text-warning d-block mt-1"></small>
                                    <span id="amount-range-message"></span>
                                </div>
                            </div>
                            <div class="srm-hostel-inline" style="margin-top: 8px;">
                                <div class="srm-hostel-inline-item">
                                    <label>Scholarship Amount</label>
                                    <input type="text" class="form-control" name="sch_amount" id="sch_amount" disabled onblur="calculatePercent(this)">
                                    <input type="hidden" class="form-control" name="allowed-amount" id="allowed-amount">
                                </div>
                                <div class="srm-hostel-inline-item">
                                    <label>Hostel Fee</label>
                                    <input type="text" class="form-control" name="hostelfee" id="hostelfee" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scholarship Percentage -->
                    <div class="srm-form-row feeSch-percent">
                        <label>Scholarship %</label>
                        <input type="text" class="form-control" name="sch_percentage" id="sch_percentage" oninput="updateAcademicPreview()" disabled>
                    </div>

                    <!-- Arrears -->
                    <div class="srm-form-row">
                        <label>Arrears (Rs)</label>
                        <input type="text" class="form-control" name="sch_arrear" id="sch_arrear" disabled readonly>
                    </div>

                    <!-- Remarks -->
                    <div class="srm-form-row">
                        <label>Remarks</label>
                        <input type="text" class="form-control" name="sch_details" id="sch_details" disabled>
                    </div>

                    <!-- Live computed amount for academic scholarships (% of fee structure) -->
                    <div class="srm-form-row" id="academic_amount_preview_row" style="display:none;">
                        <label>Computed Amount</label>
                        <input type="text" class="form-control" id="academic_amount_preview" readonly>
                    </div>

                    <!-- Notice when the academic fee structure can't be resolved -->
                    <div id="academic_fee_notice" class="alert alert-warning" style="display:none; padding:8px 10px; margin-bottom:8px;"></div>

                    <!-- Add the filled scholarship as a tile (no DB write yet) -->
                    <div class="srm-form-row">
                        <button type="button" class="srm-filter-btn" onclick="addScholarship()"><i class="fa fa-plus"></i> Add Scholarship</button>
                    </div>
                </form>
            </div>

            <!-- ─── RIGHT: STUDENT PROFILE ─── -->
            <div class="srm-profile-card d-none">
                <div class="srm-section-label"><i class="fa fa-user"></i> Student Profile</div>

                <div class="srm-profile-header-row">
                    <div class="srm-profile-details">
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="student_label"></span>
                            <span class="srm-profile-value" id="student_name"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="apply_id_label"></span>
                            <span class="srm-profile-value" id="st_apply_id"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="school_label"></span>
                            <span class="srm-profile-value" id="school_name"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="dept_label"></span>
                            <span class="srm-profile-value" id="dept_name"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="prog_label"></span>
                            <span class="srm-profile-value" id="prog_name"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="status_label"></span>
                            <span class="srm-profile-value" id="st_status"></span>
                        </div>
                        <div class="srm-profile-row">
                            <span class="srm-profile-label" id="cgpa_label"></span>
                            <span class="srm-profile-value" id="st_cgpa"></span>
                        </div>
                    </div>

                    <div class="srm-profile-photo-wrap" id="srm_student_photo_wrap" style="display: none;">
                        <img id="srm_student_photo" class="srm-profile-photo" src="" alt="Student photo" width="88" height="88">
                    </div>
                </div>

                <div class="srm-profile-scholarships" id="srm_profile_scholarships_wrap" style="display: none;">
                    <div class="srm-profile-scholarships-title" id="srm_profile_scholarships_title"><i class="fa fa-history"></i> Last Session Scholarships</div>
                    <div id="srm_profile_scholarships_list"></div>

                    <!-- Copy last-session set into the current session -->
                    <div class="srm-copy-actions" id="srm_copy_actions" style="display:none; margin-top:8px; gap:6px;">
                        <button type="button" class="srm-filter-btn" id="copy_academic_btn" style="display:none;" onclick="copyAcademicFromLastSession()"><i class="fa fa-copy"></i> Copy Academic to Current</button>
                        <button type="button" class="srm-filter-btn" id="copy_hostel_btn" style="display:none;" onclick="copyHostelFromLastSession()"><i class="fa fa-copy"></i> Copy Hostel to Current</button>
                    </div>

                    <!-- Current-session fee structure (compact) -->
                    <div id="srm_fee_structure_summary" style="display:none; margin-top:10px;"></div>
                </div>
            </div>

        </div><!-- /srm-main-grid -->

        <!-- Status Messages (shown ABOVE the assign table) -->
        <div id="processing_message" class="alert" style="display: none;">Processing, please wait...</div>
        <div id="error_message" class="alert alert-danger" style="display: none;"></div>
        <div id="success_message" class="alert alert-success" style="display: none;"></div>

        <!-- ═══════════ SCHOLARSHIPS TO ASSIGN (TABLE) ═══════════ -->
        <div class="srm-assigned-wrap" id="assigned_scholarships_wrap" style="display:none;">
            <div class="srm-section-label"><i class="fa fa-graduation-cap"></i> Scholarships to Assign<span id="assigned_cat_label"></span></div>
            <div id="assigned_scholarships_list"></div>
        </div>

        <!-- Save & Post / Clear All -->
        <div class="srm-submit-area">
            <button type="button" class="srm-submit-btn" id="save_post_btn" onclick="saveAndPostScholarships()">
                <i class="fa fa-paper-plane"></i> Save &amp; Post
            </button>
            <button type="button" class="srm-clear-btn" onclick="clearAllScholarshipForm()" title="Reset the form, table and student profile to the page-load state">
                <i class="fa fa-eraser"></i> Clear All
            </button>
        </div>
        <span id="successMsg"></span>

        <!-- ═══════════ FILTERS ═══════════ -->
        <div class="srm-filter-section">
            <div class="srm-section-label"><i class="fa fa-filter"></i> Filters</div>
            <div class="srm-filter-grid">
                <div>
                    <label>Student Type</label>
                    <select class="form-control" name="filterStudent" id="filterStudent" data-placeholder="Filter Student Type">
                        @if(auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman')
                            <option selected value="New Admission">New Admission</option>
                        @else
                            <option value="">Select Student Type</option>
                            <option value="All">All</option>
                            <option value="Existing">Existing</option>
                            <option value="New Admission">New Admission</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label>Session</label>
                    <select class="form-control" name="filterSession" id="filterSession" data-placeholder="Filter Session">
                        <option value="">Select Session</option>
                        @foreach ($academic_session as $row)
                            <option value="{{$row->sess_id}}">{{ $row->sess_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="srm-filter-actions">
                    <button type="button" class="srm-filter-btn" onclick="filterSchlrDetail()">Apply Filter</button>
                    @if((int) auth()->user()->group_id === 644)
                    <button type="button" class="srm-sync-btn" id="btnSyncSchlrEnrollment" onclick="syncScholarshipEnrollmentFromAdmission()" title="Backfill enrollment numbers from admission_apply_info">Sync Enrollment</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- ═══════════ DATA TABLE ═══════════ -->
        <div class="srm-table-wrapper">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            <th scope="col">Session</th>
                            <th scope="col">Enrollment No</th>
                            <th scope="col">Status</th>
                            <th scope="col">CGPA</th>
                            <th scope="col">Apply Id</th>
                            <th scope="col">Student Type</th>
                            <th scope="col">Name</th>
                            <th scope="col">School</th>
                            <th scope="col">Program</th>
                            <th scope="col">Scholarship Type</th>
                            <th scope="col">Type</th>
                            <th scope="col">Scholarship Percentage</th>
                            <th scope="col">Arrears Amount</th>
                            <th scope="col">Remarks</th>
                          </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /srm-card-body -->
</div><!-- /srm-card -->
@stop

@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script>
    var totalAwardedAmount_hostel = 0;
    var totalAwardedPercent_hostel = 0;
    var permitted_amount = 0;
    $(document).ready(function(){
        filterSchlrDetail();
        let allScholarOptions = $('#sch_type option').clone();

        function filterScholarshipOptions(silent) {
            let isHostel = $('#hostel_scholarship').is(':checked');

            $('#sch_type').empty().append(allScholarOptions.clone());

            $('#sch_type option').each(function () {
                let category = $(this).data('category');

                if (isHostel && category !== 'hostel') {
                    $(this).remove();
                }

                if (!isHostel && category !== 'academic') {
                    $(this).remove();
                }
            });

            if (silent) {
                $('#sch_type').val('');
            } else {
                $('#sch_type').trigger('change.select2');
            }
        }

        $('#hostel_scholarship').change(function () {
            filterScholarshipOptions(false);
            // Challan Category + Enrolled Credit Hours are academic-only.
            var isHostel = $('#hostel_scholarship').is(':checked');
            $('.acad-only').toggle(!isHostel);
            if (isHostel) { $('#enrolled_credit_hours_row').hide(); }
            updateLockedCategoryDisclaimer();
            // The table view follows the Hostel checkbox (academic vs hostel).
            renderScholarshipTable();
            // Re-evaluate the hostel fee structure for this student/session.
            refreshHostelFeeStructure();
        });
        filterScholarshipOptions(true);

        // Manual fee-structure change (only possible when no hostel is assigned).
        $('#hostle_fee_structure').on('change', function () {
            getHostelFeeStructure();
        });

        // Arrears + applied scholarships are session-scoped: reload the student
        // when the session changes so everything (incl. the arrears field) is
        // recalculated for the newly selected session, and re-evaluate the
        // hostel fee structure.
        $('#academic_session').on('change', function () {
            if ($('#sai_enrollment_no').val()) {
                getProfile();
            }
            refreshHostelFeeStructure();
        });

    });
</script>
<script type="text/javascript">
function srmStudentPhotoUrl(data) {
    if (!data) {
        return '';
    }
    var api = data.api_imagename || data.API_IMAGENAME;
    var username = data.username || data.USERNAME;
    var aaiId = data.aai_id || data.AAI_ID;
    if (api) {
        return 'https://admission.bnu.edu.pk/images/' + api;
    }
    if (username !== undefined && username !== null && String(username).trim() !== '') {
        return 'https://cms.bnu.edu.pk/students_pics/' + String(username).trim().toUpperCase() + '.jpg';
    }
    if (aaiId !== undefined && aaiId !== null && String(aaiId).trim() !== '') {
        return 'https://cms.bnu.edu.pk/students_pics/' + String(aaiId).trim().toUpperCase() + '.jpg';
    }
    return '';
}
function setSrmStudentPhoto(row) {
    var url = srmStudentPhotoUrl(row);
    var wrap = document.getElementById('srm_student_photo_wrap');
    var img = document.getElementById('srm_student_photo');
    if (!wrap || !img) {
        return;
    }
    if (!url) {
        wrap.style.display = 'none';
        img.removeAttribute('src');
        img.onerror = null;
        return;
    }
    wrap.style.display = 'block';
    img.onerror = function () {
        wrap.style.display = 'none';
        img.removeAttribute('src');
        img.onerror = null;
    };
    img.src = url;
}
function clearSrmProfileScholarships() {
    var wrap = document.getElementById('srm_profile_scholarships_wrap');
    var list = document.getElementById('srm_profile_scholarships_list');
    var title = document.getElementById('srm_profile_scholarships_title');
    if (wrap) {
        wrap.style.display = 'none';
    }
    if (list) {
        list.innerHTML = '';
    }
    if (title) {
        title.innerHTML = '<i class="fa fa-graduation-cap"></i> Active Scholarships (Session)';
    }
}
function renderSrmProfileScholarships(scholarships) {
    var wrap = document.getElementById('srm_profile_scholarships_wrap');
    var list = document.getElementById('srm_profile_scholarships_list');
    var title = document.getElementById('srm_profile_scholarships_title');
    if (!wrap || !list) {
        return;
    }
    list.innerHTML = '';
    if (!scholarships || !scholarships.length) {
        wrap.style.display = 'none';
        return;
    }
    wrap.style.display = 'block';
    var sessionName = $('#academic_session option:selected').text().trim();
    if (title) {
        var sessionLabel = sessionName ? 'Active Scholarships (Session ' + sessionName + ')' : 'Active Scholarships (Session)';
        title.innerHTML = '<i class="fa fa-graduation-cap"></i> ' + sessionLabel;
    }
    var sorted = scholarships.slice().sort(function(a, b) {
        var pctA = parseFloat(a.scholarship_percentage);
        var pctB = parseFloat(b.scholarship_percentage);
        if (isNaN(pctA)) { pctA = 0; }
        if (isNaN(pctB)) { pctB = 0; }
        return pctB - pctA;
    });
    sorted.forEach(function(sch) {
        var typeLabel = (sch.scholarship_type || '').toString();
        var pctNum = parseFloat(sch.scholarship_percentage);
        if (isNaN(pctNum)) { pctNum = 0; }
        var isZeroPct = pctNum === 0;
        var pct = sch.scholarship_percentage != null && sch.scholarship_percentage !== '' ? sch.scholarship_percentage + '%' : '0%';
        var meta = [typeLabel, pct].filter(Boolean).join(' · ');
        var itemClass = 'srm-scholarship-item' + (isZeroPct ? ' srm-scholarship-item--zero' : '');
        list.innerHTML += '<div class="' + itemClass + '">'
            + '<span class="srm-scholarship-name">' + (sch.scholarship_name || '') + '</span>'
            + '<span class="srm-scholarship-meta">' + meta + '</span>'
            + '</div>';
    });
}
// ─────────── Multi-scholarship assignment (table) ───────────
var assignedScholarships = [];       // saved + in-session rows
var academicAmountBase = 0;          // base the % is applied to (tuition, or per-credit × credits)
var academicFeeLineTotal = 0;        // total fee SHOWN on the reference/Remaining lines
                                     // (tuition, or per-credit × credits)
var academicBaseByCategory = {};     // challanCategory -> amount base. Lets rows whose
                                     // category differs from the dropdown still be priced.
var academicRefFee = 0;              // value shown in place of "Tuition fee"
var academicRefLabel = 'Tuition Fee';
var academicCreditHours = null;      // enrolled credit hours (credit categories)
var academicRepeatCredits = 0;       // repeat credit hours, billed at HALF the per-credit
                                     // rate (Credit_hours, sessions 164-166 only)
var academicArrearsApplicable = true;
var academicAllowed = false;         // false blocks adding academic scholarships
var oneTimeFeeTotal = 0;             // fee-structure Other Fee cap for arrears
var arrearsBreakdown = {};           // keyed by scholarship_type_id
var editingTile = null;              // row pulled back into the form for editing
var prevSessionScholarships = [];    // last-session scholarships (for the copy buttons)
var hostelAssigned = false;          // whether the searched student has a hostel this session
// Saved rows the user pressed "delete" on. NOTHING is removed from the DB until
// Save & Post — delete is a staged action, exactly like add and edit.
// [{id, category, challanCategory, schName}]
var pendingDeletions = [];

// True while a Save & Post request is in flight. Guards against a double-click
// posting the same batch twice (which would re-run the challan procedures).
var savePostInFlight = false;

function setSavePostBusy(busy) {
    savePostInFlight = busy;
    var btn = $('#save_post_btn');
    btn.prop('disabled', busy).css({
        'opacity': busy ? 0.65 : '',
        'cursor': busy ? 'not-allowed' : ''
    });
    btn.html(busy
        ? '<i class="fa fa-spinner fa-spin"></i> Processing…'
        : '<i class="fa fa-paper-plane"></i> Save &amp; Post');
}

function clearAssignedTiles() {
    assignedScholarships = [];
    pendingDeletions = [];
    academicAmountBase = 0;
    academicFeeLineTotal = 0;
    academicBaseByCategory = {};
    academicRefFee = 0;
    academicRefLabel = 'Tuition Fee';
    academicCreditHours = null;
    academicRepeatCredits = 0;
    academicArrearsApplicable = true;
    academicAllowed = false;
    oneTimeFeeTotal = 0;
    arrearsBreakdown = {};
    editingTile = null;
    hostelAssigned = false;
    prevSessionScholarships = [];
    renderScholarshipTable();
}

// Smoothly bring an element into view. No-ops when the element is missing, and
// falls back to a plain jump on browsers without scrollIntoViewOptions.
function srmScrollIntoView(selector, block) {
    var el = (typeof selector === 'string') ? document.querySelector(selector) : selector;
    if (!el) { return; }
    try {
        el.scrollIntoView({ behavior: 'smooth', block: block || 'center' });
    } catch (e) {
        el.scrollIntoView();
    }
}

function srmScrollToTop() {
    try {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (e) {
        window.scrollTo(0, 0);
    }
}

// "Clear All": restore the form, the assign table and the student profile card
// to exactly the state they had on page load. Purely client-side — nothing is
// written to or deleted from the DB (already-saved rows stay saved).
// opts.silent       — skip the confirm (used after a successful Save & Post, where
//                     the user has already confirmed and nothing is being discarded).
// opts.keepMessages — leave the success/error banner on screen instead of hiding it.
function clearAllScholarshipForm(opts) {
    opts = opts || {};

    if (!opts.silent && assignedScholarships.length &&
        !confirm('Clear everything on this page?\n\nUnsaved scholarships in the table will be discarded. Already-saved scholarships are NOT deleted.')) {
        return;
    }

    // 1) In-memory state + the assign table.
    clearAssignedTiles();

    // 2) Student identity — clear BEFORE the hostel change trigger below, so
    //    renderScholarshipTable() sees no student and hides the table.
    $('#sch_aai_id').val('');
    $('#sai_enrollment_no').val('');

    // 3) Search / scholarship fields back to their initial (disabled) state.
    $('#academic_session').val('').trigger('change.select2');
    $('#studentType').prop('selectedIndex', 0).trigger('change.select2');
    $('#sch_type').val('').prop('disabled', true).trigger('change.select2');
    $('#sch_percentage').val('').prop('disabled', true);
    $('#sch_arrear').val('').prop('disabled', true);
    $('#sch_details').val('').prop('disabled', true);
    $('#sch_amount').val('').prop('disabled', true);
    $('#hostelfee').val('');
    $('#allowed-amount').val('');

    // 4) Challan category + credit hours.
    setChallanCategoryLocked(false);
    $('#challan_category').val('Full_semester');
    $('#enrolled_credit_hours').val('');
    $('#enrolled_credit_hours_row').hide();
    $('#locked_category_disclaimer').hide();
    $('#academic_amount_preview').val('');
    $('#academic_amount_preview_row').hide();
    $('#academic_fee_notice').stop(true, true).text('').hide();

    // 5) Hostel block. Unchecking + triggering change restores the academic
    //    scholarship-type options and the .acad-only rows; refreshHostelFeeStructure()
    //    early-returns because the checkbox is now unchecked.
    $('#hostel_scholarship').prop('checked', false).trigger('change');
    // filterScholarshipOptions() repopulated #sch_type — re-assert it stays disabled.
    $('#sch_type').val('').prop('disabled', true).trigger('change.select2');
    $('#hostle_fee_structure').val('Regular').prop('disabled', false).trigger('change.select2');
    $('#hostel-assign-notice').text('');
    $('#amount-range-message').text('');
    $('.hotelSch-amount').addClass('d-none');

    // 6) Student profile card back to hidden + empty.
    $('.srm-profile-card').addClass('d-none');
    ['student_label','student_name','apply_id_label','st_apply_id','school_label','school_name',
     'dept_label','dept_name','prog_label','prog_name','status_label','st_status','cgpa_label','st_cgpa']
        .forEach(function(id) { $('#' + id).text(''); });
    clearSrmStudentPhoto();
    $('#srm_profile_scholarships_wrap').hide();
    $('#srm_profile_scholarships_list').empty();
    $('#srm_copy_actions').hide();
    $('#copy_academic_btn').hide();
    $('#copy_hostel_btn').hide();
    $('#srm_fee_structure_summary').hide().empty();

    // 7) Messages. After a successful Save & Post the caller keeps the banner up so
    //    the user can read what was saved.
    $('#processing_message').hide();
    if (!opts.keepMessages) {
        $('#error_message').stop(true, true).text('').hide();
        $('#success_message').stop(true, true).text('').hide();
    }
    $('#successMsg').text('');

    // 8) Table is hidden again now that no student is loaded.
    renderScholarshipTable();

    // 9) Back to the top of the page, as on a fresh load. Skipped when a message is
    //    being kept — showSrmMessage() scrolls to the banner instead.
    if (!opts.keepMessages) {
        srmScrollToTop();
    }
}

// Lock / unlock the Challan Category (keeps it readable/enabled; just blocks
// interaction — used to force Short/Summer for SUM enrollments).
function setChallanCategoryLocked(locked) {
    $('#challan_category')
        .css({ 'pointer-events': locked ? 'none' : '', 'background-color': locked ? '#e9ecef' : '' })
        .data('locked', locked);
}

// SUM-prefixed enrollments are Summer students: force Short/Summer_Course and
// lock. Any other enrollment resets to Full Semester and unlocks.
function applyChallanCategoryForEnrollment() {
    var enroll = ($('#sai_enrollment_no').val() || '').toUpperCase();
    if (enroll.indexOf('SUM') === 0) {
        $('#challan_category').val('Short/Summer_Course');
        setChallanCategoryLocked(true);
    } else {
        if ($('#challan_category').val() === 'Short/Summer_Course') {
            $('#challan_category').val('Full_semester');
        }
        setChallanCategoryLocked(false);
    }
}

// Challan categories whose posted scholarships are immutable (the credit-hour
// challan is regenerated against them). Mirrors LOCKED_CHALLAN_CATEGORIES in
// StudentRecordController — keep the two lists in sync.
var LOCKED_CHALLAN_CATEGORIES = ['additional_Full_semester', 'Credit_hours'];

// Display labels for SSD_CHALLAN_CATEGORY — must mirror the #challan_category options.
var CHALLAN_CATEGORY_LABELS = {
    'Full_semester': 'Full Semester',
    'additional_Full_semester': 'Additional Credit Hr',
    'Credit_hours': 'Credit Hour',
    'Short/Summer_Course': 'Short / Summer Course'
};

function challanCategoryLabel(cat) {
    return CHALLAN_CATEGORY_LABELS[cat] || cat || '';
}

function isLockedChallanCategory(cat) {
    return LOCKED_CHALLAN_CATEGORIES.indexOf(cat) !== -1;
}

// A row locks only once it is persisted AND its regenerated challan already carries
// a scholarship amount — the backend decides that (t.locked, from is_locked). Staged
// rows (no id) and posted rows whose challan has no scholarship amount stay editable.
function isRowLocked(t) {
    return !!(t && t.id) && t.category === 'academic' && t.locked === true;
}

// Show the "cannot be undone" disclaimer only for academic + locked category.
function updateLockedCategoryDisclaimer() {
    var box = $('#locked_category_disclaimer');
    if (!box.length) { return; }
    var isHostel = $('#hostel_scholarship').is(':checked');
    var cat = $('#challan_category').val() || 'Full_semester';
    if (!isHostel && isLockedChallanCategory(cat)) {
        $('#locked_category_name').text(challanCategoryLabel(cat));
        box.show();
    } else {
        box.hide();
    }
}

function academicFeeUrl(challanCategory) {
    return '/api/get-academic-feeStructure/'
        + $('#academic_session').val() + '/' + $('#sai_enrollment_no').val()
        + '?student_type=' + encodeURIComponent($('#studentType').val())
        + '&challan_category=' + encodeURIComponent(challanCategory);
}

// Price every academic row: amount = percentage × fee base.
//
// Prefer the row's OWN challan category's base. Fall back to the base of the
// category currently selected in the dropdown when the row has no category
// stored (legacy rows: SSD_CHALLAN_CATEGORY is NULL and seeds as Full_semester)
// or when its category has no fee structure for this session — e.g. a SUM
// student whose session only defines a Short/Summer_Course row. Without the
// fallback those rows would render 0.
function priceAcademicRows() {
    var selectedBase = Number(academicAmountBase) || 0;
    assignedScholarships.forEach(function(t) {
        if (t.category !== 'academic') { return; }
        var ownBase = Number(academicBaseByCategory[t.challanCategory || 'Full_semester']) || 0;
        var base = ownBase || selectedBase;
        t.feeBase = base;
        t.amount  = base ? Number(((t.percentage / 100) * base).toFixed(2)) : 0;
    });
}

// Resolve the fee base for any challan category present in the table that we
// haven't fetched yet, then reprice + re-render. Categories the backend can't
// resolve (e.g. Credit_hours with no enrolled courses) are cached as 0 so we
// don't refetch them on every call.
function ensureAcademicBases() {
    var session = $('#academic_session').val();
    var student_id = $('#sai_enrollment_no').val();
    if (!session || !student_id) { return; }

    var missing = [];
    assignedScholarships.forEach(function(t) {
        if (t.category !== 'academic') { return; }
        var cat = t.challanCategory || 'Full_semester';
        if (!(cat in academicBaseByCategory) && missing.indexOf(cat) === -1) { missing.push(cat); }
    });

    if (!missing.length) { priceAcademicRows(); renderScholarshipTable(); return; }

    var pending = missing.length;
    missing.forEach(function(cat) {
        $.ajax({
            url: academicFeeUrl(cat),
            type: 'GET',
            cache: false,
            success: function(res) {
                academicBaseByCategory[cat] = (res && res.status) ? (parseFloat(res.amountBase) || 0) : 0;
            },
            error: function() { academicBaseByCategory[cat] = 0; },
            complete: function() {
                if (--pending === 0) { priceAcademicRows(); renderScholarshipTable(); }
            }
        });
    });
}

// Pull the academic fee base for the current student/session/challan-category so
// the percentage can be shown as a Fee amount and arrears can be capped.
function fetchAcademicFeeBase() {
    updateLockedCategoryDisclaimer();
    var session = $('#academic_session').val();
    var student_id = $('#sai_enrollment_no').val();
    var studentType = $('#studentType').val();
    var challanCategory = $('#challan_category').val() || 'Full_semester';
    academicAmountBase = 0; academicFeeLineTotal = 0; academicRefFee = 0; academicRefLabel = 'Tuition Fee';
    academicCreditHours = null; academicRepeatCredits = 0; academicArrearsApplicable = true; academicAllowed = false;
    oneTimeFeeTotal = 0;
    $('#academic_fee_notice').text('').hide();
    $('#enrolled_credit_hours_row').hide();
    $('#enrolled_credit_hours').val('');
    $('#repeat_credit_hours_row').hide();
    $('#repeat_credit_hours').val('');
    if (!session || !student_id) { renderScholarshipTable(); return; }
    $.ajax({
        url: '/api/get-academic-feeStructure/' + session + '/' + student_id + '?student_type=' + encodeURIComponent(studentType) + '&challan_category=' + encodeURIComponent(challanCategory),
        type: 'GET',
        // The URL is identical for a given student/session/category, so without
        // this the browser replays a stale fee structure (amounts would be wrong
        // after a fee-structure change). Never cache money.
        cache: false,
        success: function(res) {
            if (res && res.status) {
                academicAllowed = true;
                academicRefFee = parseFloat(res.feeBase) || 0;
                academicRefLabel = res.referenceLabel || 'Tuition Fee';
                academicAmountBase = parseFloat(res.amountBase) || 0;
                // Fee total used for the reference / Remaining lines.
                academicFeeLineTotal = (res.feeLineTotal != null)
                    ? (parseFloat(res.feeLineTotal) || 0)
                    : academicAmountBase;
                academicCreditHours = (res.creditHours != null) ? parseFloat(res.creditHours) : null;
                academicRepeatCredits = parseFloat(res.repeatCredits) || 0;
                academicArrearsApplicable = !!res.arrearsApplicable;
                oneTimeFeeTotal = parseFloat(res.oneTimeFeeTotal) || 0;
                $('#academic_fee_notice').stop(true, true).text('').hide();
                if (academicCreditHours != null && !$('#hostel_scholarship').is(':checked')) {
                    $('#enrolled_credit_hours').val(academicCreditHours);
                    $('#enrolled_credit_hours_row').show();
                    // Repeat credits are billed at half rate — show them so the user
                    // can see where the Total Fee came from.
                    if (academicRepeatCredits > 0) {
                        $('#repeat_credit_hours').val(academicRepeatCredits);
                        $('#repeat_credit_hours_row').show();
                    }
                }
            } else {
                academicAllowed = false;
                // The backend can ask to switch the challan category (e.g. credit
                // hours out of range for the degree level -> Full Semester).
                if (res.switchTo && ($('#challan_category').val() || 'Full_semester') !== res.switchTo) {
                    showSrmMessage(res.message || 'Challan Category changed.', false);
                    $('#challan_category').val(res.switchTo);
                    fetchAcademicFeeBase();   // re-resolve with the new category
                    return;
                }
                $('#academic_fee_notice')
                    .html('<i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Academic fee structure is not defined for this student.'))
                    .stop(true, true)
                    .show();
                setTimeout(function () { $('#academic_fee_notice').fadeOut(); }, 10000);
            }
            // Cache this category's base, then price EVERY academic row against
            // its own category (fetching the other categories' bases if needed).
            var currentCat = $('#challan_category').val() || 'Full_semester';
            academicBaseByCategory[currentCat] = academicAmountBase;
            priceAcademicRows();
            renderScholarshipTable();   // immediate paint with what we know
            ensureAcademicBases();      // fills in any other category, then repaints
            updateAcademicPreview();
            // Arrears are per challan category — refresh the field for the new one.
            updateArrearsField();
        }
    });
}

// Live "Computed Amount" preview for an academic scholarship while typing %.
function updateAcademicPreview() {
    var isHostel = $('#hostel_scholarship').is(':checked');
    var pct = parseFloat($('#sch_percentage').val());
    if (isHostel || isNaN(pct) || !academicAmountBase) {
        $('#academic_amount_preview_row').hide();
        $('#academic_amount_preview').val('');
        return;
    }
    var amount = Number(((pct / 100) * academicAmountBase).toFixed(2));
    $('#academic_amount_preview').val(amount);
    $('#academic_amount_preview_row').show();
}

// Arrears depend on scholarship type + session. Refresh the field from the
// per-type breakdown (already fetched for the current session) whenever the
// selected scholarship type changes. Leaves the field untouched when no type
// is selected yet.
function updateArrearsField() {
    var schTypeId = $('#sch_type').val();
    if (!schTypeId) { return; }
    // Categories without arrears (additional_Full_semester) always show 0.
    if (!$('#hostel_scholarship').is(':checked') && academicArrearsApplicable === false) {
        $('#sch_arrear').val(0);
        return;
    }
    var obj = getArrearsFor(schTypeId);   // scoped to the selected challan category
    $('#sch_arrear').val((obj && obj.total != null) ? obj.total : 0);
}

// Sum of percentages in the table for a category. The 100% cap is PER CHALLAN
// CATEGORY: a Full Semester scholarship and a Credit Hour scholarship discount
// two different challans, so they are capped independently. Hostel is capped as
// a whole (it has no challan category).
function runningPercent(category) {
    return assignedScholarships.reduce(function(sum, t) {
        if (t.category !== category) { return sum; }
        if (category === 'academic'
            && (t.challanCategory || 'Full_semester') !== currentChallanCategory()) {
            return sum;   // different challan → different cap
        }
        return sum + (parseFloat(t.percentage) || 0);
    }, 0);
}

// Sum of arrears for the academic rows of the SELECTED challan category (hostel
// rows carry no arrear). Scoped the same way as the percentage cap so it matches
// the Arrears Total shown in the table.
function runningArrears() {
    return assignedScholarships.reduce(function(sum, t) {
        if (t.category !== 'academic') { return sum; }
        if ((t.challanCategory || 'Full_semester') !== currentChallanCategory()) { return sum; }
        return sum + (parseFloat(t.arrear) || 0);
    }, 0);
}

// Arrears live per (scholarship type, challan category). NULL/blank category on a
// legacy FIN_OTHER_SCHOLARSHIPS row means Full_semester.
function arrearsKey(schTypeId, challanCategory) {
    return String(schTypeId) + '|' + (challanCategory || 'Full_semester');
}

// Defaults to the challan category currently selected on the form.
function getArrearsFor(schTypeId, challanCategory) {
    var cat = challanCategory || currentChallanCategory();
    return arrearsBreakdown[arrearsKey(schTypeId, cat)] || null;
}

// Read the form, validate against the running totals / fee structure, and add
// a row to the table. No DB write happens here.
function addScholarship() {
    var session   = $('#academic_session').val();
    var aaiId     = $('#sch_aai_id').val();
    var schTypeId = $('#sch_type').val();
    var schName   = $('#sch_type option:selected').text().trim();
    var category  = $('#hostel_scholarship').is(':checked') ? 'hostel' : 'academic';
    var percentage = parseFloat($('#sch_percentage').val());
    var details    = $('#sch_details').val();
    // Arrears for THIS scholarship type under the selected challan category.
    var arrearsObj = getArrearsFor(schTypeId);
    var arrear     = (arrearsObj && arrearsObj.total != null) ? (parseFloat(arrearsObj.total) || 0) : (parseFloat($('#sch_arrear').val()) || 0);

    if (!session || !aaiId)  { alert('Please search a student first.'); return; }
    if (!schTypeId)          { alert('Please select a scholarship type.'); return; }
    if (isNaN(percentage) || percentage < 0 || percentage > 100) { alert('Enter a valid percentage (0-100).'); return; }

    // 0% is allowed as an ARREARS-ONLY award: the student gets no fee discount, just
    // the one-time fee waiver. It only makes sense where arrears apply and the row
    // actually carries one — otherwise the row would do nothing at all.
    if (percentage === 0) {
        if (category !== 'academic') {
            alert('A 0% hostel scholarship is not allowed — hostel scholarships carry no arrears.');
            return;
        }
        if (academicArrearsApplicable === false) {
            alert('A 0% scholarship is only allowed for challan categories that accept arrears. "'
                + challanCategoryLabel(currentChallanCategory()) + '" does not.');
            return;
        }
        if (!arrear || arrear <= 0) {
            alert('This scholarship has 0% and no arrears, so it would have no effect.\n\n'
                + 'Enter a percentage greater than 0, or record arrears for this scholarship on the Other Scholarship page.');
            return;
        }
    }

    if (!details)            { alert('Remarks are required.'); return; }

    // Block the same scholarship type twice within the same CHALLAN category.
    // The same scholarship MAY be given under two different challan categories
    // (e.g. Merit Base on Full Semester and again on Credit Hour) — those discount
    // two different challans and are capped independently.
    var dupChallanCat = (category === 'academic') ? currentChallanCategory() : null;
    var dup = assignedScholarships.some(function(t) {
        if (t.category !== category || String(t.schTypeId) !== String(schTypeId)) { return false; }
        if (category !== 'academic') { return true; }   // hostel: one per type, full stop
        return (t.challanCategory || 'Full_semester') === dupChallanCat;
    });
    if (dup) {
        alert(category === 'academic'
            ? 'This scholarship is already added for the student under "' + challanCategoryLabel(dupChallanCat) + '".'
            : 'This scholarship is already added for the student.');
        return;
    }

    var amount = 0, hostelfee = 0, feeBase = 0;
    var challanCategory = 'Full_semester';
    var arrearsApplicable = true;
    // Note: percentage / arrears over-limits are NO LONGER blocked here — the
    // user can add rows that exceed; the table shows the shortfall in red and
    // Save & Post refuses to commit an exceeding set.
    if (category === 'hostel') {
        // Hostel scholarships require the student to have a hostel assigned.
        if (!hostelAssigned) { alert('No hostel is assigned to this student for the selected session — hostel scholarship not allowed.'); return; }
        hostelfee = parseFloat($('#hostelfee').val()) || 0;
        amount    = parseFloat($('#sch_amount').val()) || 0;
        if (!amount) { alert('Enter the hostel scholarship amount.'); return; }
        feeBase = hostelfee;
        arrear = 0;   // arrears are academic-only
    } else {
        if (!academicAllowed) { alert($('#academic_fee_notice').text().trim() || 'Academic fee structure is not available for this challan category.'); return; }
        challanCategory = $('#challan_category').val() || 'Full_semester';
        arrearsApplicable = academicArrearsApplicable;
        feeBase = academicAmountBase;
        amount  = feeBase ? Number(((percentage / 100) * feeBase).toFixed(2)) : 0;
        if (!arrearsApplicable) {
            arrear = 0;   // no arrears for Credit_hours / additional_Full_semester
        }
    }

    assignedScholarships.push({
        id: (editingTile ? editingTile.id : null),
        category: category,
        schTypeId: schTypeId,
        schName: schName,
        percentage: percentage,
        amount: amount,
        feeBase: feeBase,
        hostelfee: hostelfee,
        arrear: arrear,
        arrearsApplicable: arrearsApplicable,
        challanCategory: challanCategory,
        details: details
    });
    editingTile = null;
    renderScholarshipTable();
    resetScholarshipInputs();
}

// Clear only the per-scholarship inputs; keep session / student AND the current
// category (Hostel checkbox) so the user stays in the same table view.
function resetScholarshipInputs() {
    $('#sch_percentage').val('');
    $('#sch_amount').val('');
    $('#sch_details').val('');
    // Rebuild the dropdown for the current category + refresh the table view.
    $('#hostel_scholarship').trigger('change');
    if (!$('#hostel_scholarship').is(':checked')) {
        $('.hotelSch-amount').addClass('d-none');
    }
    updateAcademicPreview();
}

// Seed the table from the student's already-saved scholarships.
function seedAssignedFromSaved(saved, breakdown) {
    academicBaseByCategory = {};   // bases are per student/session — never reuse
    pendingDeletions = [];         // a fresh load discards any un-posted deletions
    // Arrears are recorded PER challan category, so the breakdown is keyed by
    // "<scholarshipTypeId>|<challanCategory>", not by type alone.
    arrearsBreakdown = {};
    (breakdown || []).forEach(function(b) {
        arrearsBreakdown[arrearsKey(b.scholarship_type_id, b.challan_category)] = b;
    });
    assignedScholarships = (saved || []).map(function(s) {
        var category = (s.scholarship_type || '').toString().toLowerCase(); // academic / hostel
        var pct = parseFloat(s.scholarship_percentage) || 0;
        var amount = parseFloat(s.scholarship_amount) || 0; // hostel has it stored; academic recomputed after fee base loads
        return {
            id: s.row_id,
            category: category,
            schTypeId: s.scholarship_type_id,
            schName: s.scholarship_name,
            percentage: pct,
            amount: amount,
            feeBase: parseFloat(s.hostel_fee) || 0,
            hostelfee: parseFloat(s.hostel_fee) || 0,
            arrear: category === 'academic' ? (parseFloat(s.scholarship_arrear) || 0) : 0,
            arrearsApplicable: category === 'academic',
            challanCategory: (s.scholarship_challan_category || 'Full_semester'),
            // Backend-computed: locked only when the regenerated challan already
            // carries a scholarship amount (see isScholarshipRowLockedByChallan).
            locked: String(s.is_locked) === '1',
            details: s.scholarship_details || ''
        };
    });
    renderScholarshipTable();
}

// Active table category follows the Hostel checkbox: unchecked = academic,
// checked = hostel. Academic and hostel are capped separately, so they are
// shown (and totalled) separately rather than mixed in one table.
function currentCategory() {
    return $('#hostel_scholarship').is(':checked') ? 'hostel' : 'academic';
}

function currentChallanCategory() {
    return $('#challan_category').val() || 'Full_semester';
}

// The table shows ONE view at a time: either hostel, or academic rows of the
// SELECTED challan category. Save & Post acts only on the rows in this view.
function rowIsInCurrentView(t) {
    if (currentCategory() === 'hostel') {
        return t.category === 'hostel';
    }
    return t.category === 'academic'
        && (t.challanCategory || 'Full_semester') === currentChallanCategory();
}

// Render the scholarships-to-assign table for the active category, with live
// Percent / Amount (and, for academic, Arrears + Tuition / Fee-structure
// reference lines). Hostel has no arrear concept.
function renderScholarshipTable() {
    var wrap = document.getElementById('assigned_scholarships_wrap');
    var list = document.getElementById('assigned_scholarships_list');
    if (!wrap || !list) { return; }

    var cat = currentCategory();
    var isAcademic = cat === 'academic';
    // Academic rows are scoped to the SELECTED challan category — the table shows
    // one category at a time, and Save & Post acts only on what is shown.
    var rowsData = assignedScholarships.filter(function(t) { return rowIsInCurrentView(t); });

    var catLabel = document.getElementById('assigned_cat_label');
    if (catLabel) {
        catLabel.textContent = isAcademic
            ? '  —  Academic: ' + challanCategoryLabel(currentChallanCategory())
            : '  —  Hostel';
    }

    // Only hide the table before a student is searched. Once a student is
    // loaded, always show it (with the tuition / fee-structure reference lines)
    // even when the student has no scholarship in this session.
    var studentLoaded = !!$('#sch_aai_id').val();
    if (!studentLoaded) {
        wrap.style.display = 'none';
        list.innerHTML = '';
        return;
    }
    wrap.style.display = 'block';

    var totalPct = 0, totalAmount = 0, totalArrear = 0;
    var rows = '';
    if (!rowsData.length) {
        // Academic: Scholarship | Challan Category | Percent | Amount | Action | Arrear
        // Hostel:   Scholarship | Percent | Amount | Action
        var emptyCols = isAcademic ? 6 : 4;
        rows = '<tr><td colspan="' + emptyCols + '" style="text-align:center;color:#888;">No scholarship in this session</td></tr>';
    }
    rowsData.forEach(function(t) {
        var index = assignedScholarships.indexOf(t);   // global index for edit / delete
        var pct = parseFloat(t.percentage) || 0;
        var amount = Number(t.amount || 0);
        // Arrear column: blank for hostel and for academic rows whose category
        // has no arrears (Credit_hours / additional_Full_semester).
        var showArrear = isAcademic && (t.arrearsApplicable !== false);
        var arrear = showArrear ? (Number(t.arrear) || 0) : null;
        totalPct += pct;
        totalAmount += amount;
        if (arrear) { totalArrear += arrear; }
        var catBadge = '<span class="srm-sch-cat' + (isAcademic ? '' : ' srm-sch-cat--hostel') + '">' + cat.toUpperCase() + '</span>';
        // Credit-hour rows lock once their challan carries a scholarship amount.
        var locked = isRowLocked(t);
        var actionCell = locked
            ? '<span title="Applied against ' + challanCategoryLabel(t.challanCategory)
                  + ' — the challan already carries a scholarship amount, so it can no longer be edited or deleted." '
                  + 'style="color:#7a4b00;background:#fff8e6;border:1px solid #f0b429;border-radius:3px;padding:2px 7px;font-size:11px;white-space:nowrap;">'
                  + '<i class="fa fa-lock" style="margin-right:4px;"></i>Locked</span>'
            : '<button type="button" class="srm-row-btn edit" onclick="editTile(' + index + ')">edit</button>'
              + '<button type="button" class="srm-row-btn delete" onclick="removeTile(' + index + ')">delete</button>';
        // The challan category this scholarship is applied against (SSD_CHALLAN_CATEGORY).
        var challanCell = isAcademic
            ? '<td>' + challanCategoryLabel(t.challanCategory || 'Full_semester') + '</td>'
            : '';
        rows += '<tr>'
            + '<td>' + (t.schName || '') + catBadge + '</td>'
            + challanCell
            + '<td class="num">' + pct + '%</td>'
            + '<td class="num">' + amount.toLocaleString() + '</td>'
            + '<td>' + actionCell + '</td>'
            + (isAcademic ? '<td class="num">' + (arrear != null ? arrear.toLocaleString() : '') + '</td>' : '')
            + '</tr>';
    });

    var header = '<thead><tr>'
        + '<th>Scholarship</th>'
        + (isAcademic ? '<th>Challan Category</th>' : '')
        + '<th class="num">Percent</th><th class="num">Amount</th><th>Action</th>'
        + (isAcademic ? '<th class="num">Arrear</th>' : '')
        + '</tr></thead>';

    var red = 'color:#c0392b;font-weight:700;';   // negative / over-limit highlight
    var pctStyle = totalPct > 100 ? red : '';
    var footer;
    if (isAcademic) {
        // Amount column = fee amounts (fee, scholarship total, remaining).
        // Arrear column = arrears (Other Fee, arrears total, remaining).
        var base = Number(academicFeeLineTotal || 0);        // tuition, or per-credit × credits
        var feeLabel = (academicCreditHours != null) ? 'Total Fee' : 'Tuition Fee';
        var remainingAmt = base - totalAmount;               // left after scholarships
        var oneTime = Number(oneTimeFeeTotal || 0);
        var remainingArr = oneTime - totalArrear;            // left after arrears
        var remAmtStyle = remainingAmt < 0 ? red : '';
        var remArrStyle = remainingArr < 0 ? red : '';

        // 6 columns: Scholarship | Challan Category | Percent | Amount | Action | Arrear
        footer = '<tfoot>'
            // Totals
            + '<tr>'
            +     '<td>Total</td>'
            +     '<td></td>'
            +     '<td class="num" style="' + pctStyle + '">' + totalPct + '%</td>'
            +     '<td class="num">' + totalAmount.toLocaleString() + '</td>'
            +     '<td></td>'
            +     '<td class="num">' + (academicArrearsApplicable ? totalArrear.toLocaleString() : '') + '</td>'
            + '</tr>'
            // Fee references
            + '<tr>'
            +     '<td class="ref"></td><td class="ref"></td><td class="ref"></td>'
            +     '<td class="ref">' + feeLabel + ': ' + base.toLocaleString() + '</td>'
            +     '<td class="ref"></td>'
            +     '<td class="ref">' + (academicArrearsApplicable ? ('Other Fee: ' + oneTime.toLocaleString()) : '') + '</td>'
            + '</tr>'
            // Remaining (red when negative = exceeding)
            + '<tr>'
            +     '<td class="ref"></td><td class="ref"></td><td class="ref"></td>'
            +     '<td class="ref" style="' + remAmtStyle + '">Remaining: ' + remainingAmt.toLocaleString() + '</td>'
            +     '<td class="ref"></td>'
            +     '<td class="ref" style="' + (academicArrearsApplicable ? remArrStyle : '') + '">' + (academicArrearsApplicable ? ('Remaining: ' + remainingArr.toLocaleString()) : '') + '</td>'
            + '</tr>';
        if (academicCreditHours != null) {
            // Show how the Total Fee was derived. Repeat credits are billed at HALF
            // the per-credit rate, so spell that out rather than leaving the user to
            // reconcile the number themselves.
            var perCr = Number(academicRefFee || 0);
            var creditLine = 'Per Credit Fee: ' + perCr.toLocaleString() + ' × ' + academicCreditHours + ' cr';
            if (academicRepeatCredits > 0) {
                creditLine += ' + ' + perCr.toLocaleString() + ' × ' + academicRepeatCredits
                    + ' repeat cr ÷ 2 (= ' + Number(perCr * (academicRepeatCredits / 2)).toLocaleString() + ')';
            }
            footer += '<tr><td class="ref"></td><td class="ref"></td><td class="ref"></td><td class="ref">' + creditLine + '</td><td class="ref"></td><td class="ref"></td></tr>';
        }
        footer += '</tfoot>';
    } else {
        // Hostel: Amount column = hostel fee, scholarship total, remaining.
        var hFee = rowsData.length ? (Number(rowsData[0].hostelfee) || 0) : (parseFloat($('#hostelfee').val()) || 0);
        var hRemaining = hFee - totalAmount;
        var hRemStyle = hRemaining < 0 ? red : '';
        footer = '<tfoot>'
            + '<tr><td>Total</td><td class="num" style="' + pctStyle + '">' + totalPct + '%</td><td class="num">' + totalAmount.toLocaleString() + '</td><td></td></tr>'
            + '<tr><td class="ref"></td><td class="ref"></td><td class="ref">Hostel Fee: ' + hFee.toLocaleString() + '</td><td class="ref"></td></tr>'
            + '<tr><td class="ref"></td><td class="ref"></td><td class="ref" style="' + hRemStyle + '">Remaining: ' + hRemaining.toLocaleString() + '</td><td class="ref"></td></tr>'
            + '</tfoot>';
    }

    list.innerHTML = '<table class="srm-sch-table">' + header + '<tbody>' + rows + '</tbody>' + footer + '</table>';
}

// Message shown when a posted credit-hour scholarship is edited / deleted.
function lockedRowMessage(t) {
    return 'This scholarship was applied against "'
        + challanCategoryLabel(t.challanCategory)
        + '" and its challan already carries a scholarship amount, so it can no longer be edited or deleted.';
}

// Pull a row back into the form for editing, then drop it from the table.
function editTile(index) {
    var t = assignedScholarships[index];
    if (!t) { return; }
    if (isRowLocked(t)) { showSrmMessage(lockedRowMessage(t), false); return; }
    editingTile = t;
    assignedScholarships.splice(index, 1);

    // Re-filter the dropdown for the row's category via the checkbox handler
    // (filterScholarshipOptions is scoped to document.ready), then select it.
    $('#hostel_scholarship').prop('checked', t.category === 'hostel').trigger('change');
    $('#sch_type').val(t.schTypeId).trigger('change.select2');
    $('#sch_percentage').val(t.percentage);
    $('#sch_details').val(t.details);
    if (t.category === 'hostel') {
        $('.hotelSch-amount').removeClass('d-none');
        $('#hostelfee').val(t.hostelfee);
        $('#sch_amount').val(t.amount);
    } else {
        $('.hotelSch-amount').addClass('d-none');
        // Restore the row's challan category and re-resolve the fee base.
        $('#challan_category').val(t.challanCategory || 'Full_semester');
        fetchAcademicFeeBase();
    }
    renderScholarshipTable();
    updateAcademicPreview();

    // Bring the form back into view and put the caret in the % field so the
    // user can immediately amend the row that was pulled up for editing.
    srmScrollIntoView('#studentRecord', 'start');
    $('#sch_percentage').trigger('focus');
}

// Remove a row. Saved rows (with an id) are deleted server-side; in-session
// rows are just dropped.
// Remove a row from the table. This is a STAGED action — nothing is deleted from
// the database here. A saved row (one with an id) is queued in pendingDeletions
// and only removed when the user presses Save & Post. In-session rows are simply
// dropped. Re-searching the student discards any un-posted deletions.
function removeTile(index) {
    var t = assignedScholarships[index];
    if (!t) { return; }
    if (isRowLocked(t)) { showSrmMessage(lockedRowMessage(t), false); return; }

    if (t.id) {
        pendingDeletions.push({
            id: t.id,
            category: t.category,
            challanCategory: t.challanCategory || 'Full_semester',
            schName: t.schName || ''
        });
    }
    assignedScholarships.splice(index, 1);
    renderScholarshipTable();
}

// Staged deletions that belong to the view currently on screen. Save & Post only
// commits the view it is showing, so deletions follow the same scoping.
function pendingDeletionsInCurrentView() {
    var isHostel = currentCategory() === 'hostel';
    var challanCat = currentChallanCategory();
    return pendingDeletions.filter(function(d) {
        return isHostel
            ? d.category === 'hostel'
            : (d.category === 'academic' && d.challanCategory === challanCat);
    });
}

// Save & Post commits ONLY the view currently on screen: the hostel rows, or the
// academic rows of the selected Challan Category. Rows of other categories are
// left untouched. Staged deletions for this view are applied in the same
// transaction. Everything is all-or-nothing.
function saveAndPostScholarships() {
    // One request at a time: a second click would re-post the same batch and re-run
    // the challan procedures.
    if (savePostInFlight) { return; }

    var session = $('#academic_session').val();
    var aaiId = $('#sch_aai_id').val();
    if (!session || !aaiId) { alert('Please search a student first.'); return; }

    var isHostelView = currentCategory() === 'hostel';
    var viewLabel = isHostelView
        ? 'Hostel'
        : challanCategoryLabel(currentChallanCategory());

    var rowsToPost = assignedScholarships.filter(rowIsInCurrentView);
    var deletionsToPost = pendingDeletionsInCurrentView();

    if (!rowsToPost.length && !deletionsToPost.length) {
        alert('Nothing to save for "' + viewLabel + '". Add a scholarship, or switch to another Challan Category.');
        return;
    }

    // Over-limit rows are allowed in the table (shown red) but must be fixed
    // before committing. Block Save & Post here (the backend also enforces).
    var problems = [];
    if (runningPercent('academic') > 100) { problems.push('Academic percentage total is ' + runningPercent('academic') + '% (max 100%).'); }
    if (runningPercent('hostel') > 100) { problems.push('Hostel percentage total is ' + runningPercent('hostel') + '% (max 100%).'); }
    if (oneTimeFeeTotal > 0 && runningArrears() > oneTimeFeeTotal) { problems.push('Arrears total exceeds the fee-structure Other Fee.'); }
    if (problems.length) { showSrmMessage('Cannot Save & Post — please adjust: ' + problems.join(' '), false); return; }

    // Build the confirmation: what is being saved, what is being DELETED, and
    // whether anything is about to become permanently locked.
    var summary = 'Save & Post — ' + viewLabel + '\n\n';
    if (rowsToPost.length)      { summary += rowsToPost.length + ' scholarship(s) will be saved and the challan regenerated.\n'; }
    if (deletionsToPost.length) {
        summary += '\nThe following saved scholarship(s) will be PERMANENTLY DELETED:\n'
            + deletionsToPost.map(function(d) { return '  • ' + d.schName; }).join('\n') + '\n';
    }

    var lockingRows = rowsToPost.filter(function(t) {
        return !t.id && t.category === 'academic' && isLockedChallanCategory(t.challanCategory);
    });
    if (lockingRows.length) {
        summary += '\nWARNING — this cannot be undone.\n'
            + 'These will be applied against a credit-hour challan and CANNOT be edited or deleted afterwards:\n'
            + lockingRows.map(function(t) {
                return '  • ' + t.schName + ' (' + t.percentage + '% — ' + challanCategoryLabel(t.challanCategory) + ')';
            }).join('\n') + '\n';
    }
    if (!confirm(summary + '\nProceed?')) { return; }

    // Lock the button only once the request is actually going out — a cancelled
    // confirm must not leave it stuck.
    setSavePostBusy(true);
    $('#processing_message').show();
    // Park the viewport on the message area so the result (success note or the
    // procedure error) is visible the moment the request returns.
    srmScrollIntoView('#processing_message', 'center');
    fetch('/api/save-and-post-scholarships', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            academic_session: session,
            sch_aai_id: aaiId,
            sch_enrollment_no: $('#sai_enrollment_no').val(),
            student_type: $('#studentType').val(),
            // Only the rows of the view on screen (hostel, or one challan category).
            scholarships: rowsToPost.map(function(t) {
                return {
                    id: t.id,
                    category: t.category,
                    sch_type: t.schTypeId,
                    sch_percentage: t.percentage,
                    sch_amount: t.amount,
                    hostelfee: t.hostelfee,
                    sch_arrear: t.arrear,
                    challan_category: t.challanCategory || 'Full_semester',
                    sch_details: t.details
                };
            }),
            // Saved rows the user removed from the table; deleted in the same
            // transaction as the saves. challan_category is sent so the backend
            // can regenerate that challan — removing a scholarship changes the fee.
            deleted_rows: deletionsToPost.map(function(d) {
                return { id: d.id, category: d.category, challan_category: d.challanCategory };
            })
        })
    })
    .then(function(r) {
        // Read the body as text first so a non-JSON error page (PHP fatal,
        // gateway timeout) still yields a meaningful message instead of a
        // silent JSON parse failure.
        return r.text().then(function(body) {
            var res;
            try { res = JSON.parse(body); }
            catch (e) {
                throw new Error('Server error (' + r.status + '). ' +
                    (body ? body.replace(/<[^>]*>/g, '').trim().slice(0, 300) : 'No response from server.'));
            }
            return res;
        });
    })
    .then(function(res) {
        $('#processing_message').hide();
        setSavePostBusy(false);

        if (res.success) {
            // Success: reset the page to its load state (same as Clear All), but
            // silently and keeping the banner so the user can read what was saved.
            clearAllScholarshipForm({ silent: true, keepMessages: true });
            showSrmMessage(res.message || 'Saved & posted', true);
            filterSchlrDetail();   // refresh the records grid at the bottom
        } else {
            // Failure: leave EVERYTHING on screen so the user can fix and retry.
            showSrmMessage(res.messege || res.message || 'Save & Post failed', false);
        }
    })
    .catch(function(err) {
        // Network / non-JSON failure — also leave the form intact for a retry.
        $('#processing_message').hide();
        setSavePostBusy(false);
        console.error(err);
        showSrmMessage((err && err.message) ? err.message : 'Save & Post failed — could not reach the server. Please try again.', false);
    });
}

function showSrmMessage(message, isSuccess) {
    var ok = $('#success_message');
    var err = $('#error_message');
    if (isSuccess) {
        err.stop(true, true).text('').hide();
        ok.stop(true, true).html(message).fadeIn('slow').fadeOut(4000);
    } else {
        ok.stop(true, true).text('').hide();
        err.stop(true, true).html(message).fadeIn('slow').fadeOut(10000);
    }
    // Messages sit above the assign table — make sure the user actually sees them.
    srmScrollIntoView(isSuccess ? '#success_message' : '#error_message', 'center');
}

// Show the student's previous-session scholarships (read-only) in the profile
// card, so the user can see what was given before the selected session.
function renderPrevSessionScholarships(scholarships, sessionLabel, feeSummary) {
    var wrap = document.getElementById('srm_profile_scholarships_wrap');
    var list = document.getElementById('srm_profile_scholarships_list');
    var title = document.getElementById('srm_profile_scholarships_title');
    if (!wrap || !list) { return; }
    list.innerHTML = '';
    // Always show the panel for a loaded student; intimate when there are none.
    wrap.style.display = 'block';
    if (title) {
        var label = sessionLabel ? 'Last Session Scholarships (' + sessionLabel + ')' : 'Last Session Scholarships';
        title.innerHTML = '<i class="fa fa-history"></i> ' + label;
    }

    var hasAcademic = (scholarships || []).some(function(s) { return (s.scholarship_type || '').toLowerCase() === 'academic'; });
    var hasHostel   = (scholarships || []).some(function(s) { return (s.scholarship_type || '').toLowerCase() === 'hostel'; });

    if (!scholarships || !scholarships.length) {
        list.innerHTML = '<div class="srm-scholarship-item" style="justify-content:center;color:#888;">No scholarships in the previous session</div>';
    } else {
        scholarships.forEach(function(sch) {
            var typeLabel = (sch.scholarship_type || '').toString();
            var pct = (sch.scholarship_percentage != null && sch.scholarship_percentage !== '') ? sch.scholarship_percentage + '%' : '0%';
            var meta = [typeLabel, pct].filter(Boolean).join(' · ');
            list.innerHTML += '<div class="srm-scholarship-item">'
                + '<span class="srm-scholarship-name">' + (sch.scholarship_name || '') + '</span>'
                + '<span class="srm-scholarship-meta">' + meta + '</span>'
                + '</div>';
        });
    }

    // Copy buttons — shown only for the categories present last session.
    $('#copy_academic_btn').toggle(hasAcademic);
    $('#copy_hostel_btn').toggle(hasHostel);
    $('#srm_copy_actions').css('display', (hasAcademic || hasHostel) ? 'flex' : 'none');

    // Current-session fee structure (compact).
    renderFeeStructureSummary(feeSummary);
}

// Compact current-session fee-structure values under the last-session panel.
function renderFeeStructureSummary(fs) {
    var box = document.getElementById('srm_fee_structure_summary');
    if (!box) { return; }
    if (!fs) { box.style.display = 'none'; box.innerHTML = ''; return; }
    var fmt = function(v) { return (v == null || v === '') ? '—' : Number(v).toLocaleString(); };
    var rows = [
        ['Tuition Fee', fs.tuition],
        ['Admission Fee', fs.admission],
        ['Security Fee', fs.security],
        ['Medical Fee', fs.medical],
        ['Activity Charges', fs.activity],
        ['Per Credit Fee', fs.per_credit]
    ];
    var items = rows.map(function(r) {
        return '<span style="display:inline-block;margin-right:12px;white-space:nowrap;"><b style="color:#41506a;">' + r[0] + ':</b> ' + fmt(r[1]) + '</span>';
    }).join('');
    // Name the session the figures belong to, e.g. "Fee Structure (Selected Session: Summer 2026)".
    var sessName = $('#academic_session option:selected').text().trim();
    var heading = 'Fee Structure (Session' + (sessName ? ': ' + sessName : '') + ')';
    box.innerHTML = '<div class="srm-profile-scholarships-title" style="margin-bottom:4px;"><i class="fa fa-file-invoice-dollar"></i> ' + heading + '</div>'
        + '<div style="font-size:11.5px;color:#5a6678;line-height:1.7;">' + items + '</div>';
    box.style.display = 'block';
}

// Copy last session's ACADEMIC scholarships into the current-session table,
// using the currently-selected Challan Category + its resolved base.
function copyAcademicFromLastSession() {
    if (!$('#sch_aai_id').val()) { alert('Please search a student first.'); return; }
    if (!academicAllowed) { alert('Select a valid Challan Category first — the academic fee base could not be resolved.'); return; }
    var prevAcademic = (prevSessionScholarships || []).filter(function(s) { return (s.scholarship_type || '').toLowerCase() === 'academic'; });
    if (!prevAcademic.length) { alert('No academic scholarships in the previous session.'); return; }

    var challanCategory = $('#challan_category').val() || 'Full_semester';
    var copied = 0, skippedDup = 0;
    prevAcademic.forEach(function(s) {
        var schTypeId = s.scholarship_type_id;
        var pct = parseFloat(s.scholarship_percentage) || 0;
        if (pct < 0) { return; }
        if (assignedScholarships.some(function(t) { return t.category === 'academic' && String(t.schTypeId) === String(schTypeId); })) { skippedDup++; return; }
        var arr = getArrearsFor(schTypeId);
        var arrear = (academicArrearsApplicable && arr && arr.total != null) ? (parseFloat(arr.total) || 0) : 0;
        // A 0% row is an arrears-only award — keep it only if it actually carries one.
        if (pct === 0 && arrear <= 0) { return; }
        assignedScholarships.push({
            id: null,
            category: 'academic',
            schTypeId: schTypeId,
            schName: s.scholarship_name,
            percentage: pct,
            amount: academicAmountBase ? Number(((pct / 100) * academicAmountBase).toFixed(2)) : 0,
            feeBase: academicAmountBase,
            hostelfee: 0,
            arrear: arrear,
            arrearsApplicable: academicArrearsApplicable,
            challanCategory: challanCategory,
            details: s.scholarship_details || 'Copied from last session'
        });
        copied++;
    });
    // Show the academic view so the copied rows are visible.
    if ($('#hostel_scholarship').is(':checked')) { $('#hostel_scholarship').prop('checked', false).trigger('change'); }
    renderScholarshipTable();
    var msg = copied + ' academic scholarship(s) copied.';
    if (skippedDup) { msg += ' ' + skippedDup + ' already present.'; }
    showSrmMessage(msg, copied > 0);
}

// Copy last session's HOSTEL scholarships as-is (previous amount / % / fee).
function copyHostelFromLastSession() {
    if (!$('#sch_aai_id').val()) { alert('Please search a student first.'); return; }
    if (!hostelAssigned) { alert('No hostel is assigned to this student for the selected session — hostel scholarship not allowed.'); return; }
    var prevHostel = (prevSessionScholarships || []).filter(function(s) { return (s.scholarship_type || '').toLowerCase() === 'hostel'; });
    if (!prevHostel.length) { alert('No hostel scholarships in the previous session.'); return; }

    var copied = 0, skippedDup = 0;
    prevHostel.forEach(function(s) {
        var schTypeId = s.scholarship_type_id;
        var pct = parseFloat(s.scholarship_percentage) || 0;
        if (pct <= 0) { return; }
        if (assignedScholarships.some(function(t) { return t.category === 'hostel' && String(t.schTypeId) === String(schTypeId); })) { skippedDup++; return; }
        assignedScholarships.push({
            id: null,
            category: 'hostel',
            schTypeId: schTypeId,
            schName: s.scholarship_name,
            percentage: pct,
            amount: parseFloat(s.scholarship_amount) || 0,
            feeBase: parseFloat(s.hostel_fee) || 0,
            hostelfee: parseFloat(s.hostel_fee) || 0,
            arrear: 0,
            arrearsApplicable: false,
            challanCategory: 'Full_semester',
            details: s.scholarship_details || 'Copied from last session'
        });
        copied++;
    });
    // Show the hostel view so the copied rows are visible.
    if (!$('#hostel_scholarship').is(':checked')) { $('#hostel_scholarship').prop('checked', true).trigger('change'); }
    renderScholarshipTable();
    var msg = copied + ' hostel scholarship(s) copied.';
    if (skippedDup) { msg += ' ' + skippedDup + ' already present.'; }
    showSrmMessage(msg, copied > 0);
}

function clearSrmStudentPhoto() {
    var wrap = document.getElementById('srm_student_photo_wrap');
    var img = document.getElementById('srm_student_photo');
    if (wrap) {
        wrap.style.display = 'none';
    }
    if (img) {
        img.removeAttribute('src');
        img.onerror = null;
        img.onload = null;
    }
}
function scholarshipChange(){
    // Note: no longer reloads the whole profile (that would wipe in-session
    // tiles). Only refreshes hostel calc + the academic amount preview.
    session_enrollment_change();
    getHostelFeeStructure();
    updateAcademicPreview();
    updateArrearsField();
}
function autoSearch(ele){
   $('#filter_message').hide();
   var bi_form = document.querySelector('#filterForm');
   var fd      = new FormData(bi_form);

   if( (fd.get('username') == '') && (fd.get('accesstion_user') == '') ){
       $('#filter_message').show();
       $('#filter_message').html('No Record Found!');
       return false;
   }

   $('#user-search-btn').html('Searching...');

   sendPostRequest('library/search-by-filter',function(res){
       $('#user-search-btn').html('Load Detail');
       if(res.username){
           var username = res.username;
           $('#sai_enrollment_no').val(username);
           $('#sisuserUsername').val(username);
           createProfileView(res);
           loadUserBookIssuedHistory(username);
       }else{
           $('#filter_message').show();
           $('#filter_message').html('No Record Found!');
       }
   },fd,function(error){
       $('#user-search-btn').html('Load Detail');
   });
   return false;
}
function getProfile(){

    var studentType = $('#studentType').val();
    var session = $('#academic_session').val();
    var rol_no = $.trim($('#sai_enrollment_no').val() || '');
    var sch_type = $('#sch_type').val();

    // The search needs Session + Student Type + Apply Id / Enroll No. Report the
    // input that is actually missing — firing the request with an empty session
    // used to come back "Kindly enter valid Apply Id / Enroll No.", blaming the
    // one field the user HAD filled in.
    var searchByLabel = $('.searchBy').text().replace('#', 'No.').trim() || 'Apply Id / Enroll No.';
    var missing = [];
    if (!session)     { missing.push('Session'); }
    if (!studentType) { missing.push('Student Type'); }
    if (!rol_no)      { missing.push(searchByLabel); }

    if (missing.length) {
        var msg = (missing.length === 1)
            ? 'Please select the ' + missing[0] + ' before searching.'
            : 'Please provide the following before searching: ' + missing.join(', ') + '.';
        showSrmMessage(msg, false);
        // Focus the first missing field so the user can act immediately.
        if (!session)          { $('#academic_session').trigger('focus'); }
        else if (!studentType) { $('#studentType').trigger('focus'); }
        else                   { $('#sai_enrollment_no').trigger('focus'); }
        return;
    }

    $('#sai_enrollment_no').val(rol_no);   // persist the trimmed value
    $('.srm-profile-card').removeClass('d-none');
   $.ajax({
       // studentType now contains spaces ("New Admission FALL") and goes into a URL
       // PATH segment — it must be encoded or the route will not match.
       url: '{{route("get-student-profile")}}/'+encodeURIComponent(rol_no)+'/'+encodeURIComponent(studentType)+'/'+session+'/'+sch_type,
       type: 'GET',
       cache: false,   // saved scholarships + fee summary must never come from cache
       success: function(res) {
            console.log(res);
           if(res.message == 1){
                $('#sch_type').removeAttr("disabled");
                $('#sch_percentage').removeAttr("disabled");
                $('#sch_amount').removeAttr("disabled");
                $('#sch_arrear').removeAttr("disabled");
                $('#sch_details').removeAttr("disabled");
                var tbodyTRhtml = '';
                if (res.data && res.data.length) {
                    setSrmStudentPhoto(res.data[0]);
                    // Set the app id before seeding so the table renders even
                    // when the student has no scholarship this session.
                    if (res.data[0].aai_id) { $('#sch_aai_id').val(res.data[0].aai_id); }
                } else {
                    clearSrmStudentPhoto();
                }
                seedAssignedFromSaved(res.scholarships || [], res.arrears_breakdown || []);
                // Auto-select & lock Short/Summer for SUM enrollments, then fetch.
                applyChallanCategoryForEnrollment();
                fetchAcademicFeeBase();
                prevSessionScholarships = res.prev_scholarships || [];
                renderPrevSessionScholarships(prevSessionScholarships, res.prev_session_name, res.fee_structure_summary || null);
                // Resolve whether the student has a hostel this session (drives
                // whether hostel scholarships / copy are allowed) and re-evaluate
                // the hostel fee structure.
                resolveHostelAssigned();
                refreshHostelFeeStructure();
                $(res.data).each(function(index,row){

                    if(row.aai_id){
                        $('#sch_aai_id').val(row.aai_id);
                    }
                    // NOTE: row.arrears (a whole-student, all-categories sum from
                    // getprofileMgt) is deliberately NOT used. Arrears are per
                    // (scholarship type, challan category) — updateArrearsField()
                    // below sets #sch_arrear from the scoped breakdown.
                    $('#sch_arrear').val(0);

                    if(row.school_name){
                        $('#student_label').html('Name');
                        $('#student_name').html(row.student_name);
                    }else{
                        $('#student_label').html('');
                        $('#student_name').html('');
                    }

                    // Apply Id = admission_apply_info.AAI_ID
                    if(row.aai_id){
                        $('#apply_id_label').html('Apply Id');
                        $('#st_apply_id').html(row.aai_id);
                    }else{
                        $('#apply_id_label').html('');
                        $('#st_apply_id').html('');
                    }

                    if(row.school_name){
                        $('#school_label').html('School');
                        $('#school_name').html(row.school_name);
                    }else{
                        $('#school_label').html('');
                        $('#school_name').html('');
                    }

                    if(row.dept_name){
                        $('#dept_label').html('Department');
                        $('#dept_name').html(row.dept_name);
                    }else{
                        $('#dept_label').html('');
                        $('#dept_name').html('');
                    }

                    if(row.prog_name){
                        $('#prog_label').html('Program');
                        $('#prog_name').html(row.prog_name);
                    }else{
                        $('#prog_label').html('');
                        $('#prog_name').html('');
                    }

                    if(row.sai_student_status){
                        $('#status_label').html('Status');
                        $('#st_status').html(row.sai_student_status);
                    }else{
                        $('#status_label').html('');
                        $('#st_status').html('');
                    }
                    if(row.cgpa){
                        $('#cgpa_label').html('CGPA');
                        $('#st_cgpa').html(row.cgpa);
                    }else{
                        $('#cgpa_label').html('');
                        $('#st_cgpa').html('');
                    }

                });
                // Reflect the per-type arrears for the (re)loaded session.
                updateArrearsField();
            }else{
                clearSrmStudentPhoto();
                clearSrmProfileScholarships();
                $('#sch_aai_id').val('');
                clearAssignedTiles();
                // All inputs were present (getProfile validates first), so this is a
                // genuine "no record" for this student + student type + session.
                var notFoundMsg = 'No record found for ' + $('.searchBy').text().replace('#', 'No.').trim()
                    + ' "' + rol_no + '" as a "' + studentType + '" student in '
                    + ($('#academic_session option:selected').text().trim() || 'the selected session')
                    + '. Please check the ID, Student Type and Session.';
                $('#student_name').html('<b style="color: red;">' + notFoundMsg + '</b>');
                showSrmMessage(notFoundMsg, false);
                $('#school_name').html('');
                $('#dept_name').html('');
                $('#prog_name').html('');
                $('#st_status').html('');
                $('#st_cgpa').html('');
                $('#student_label').html('');
                $('#school_label').html('');
                $('#dept_label').html('');
                $('#prog_label').html('');
                $('#status_label').html('');
                $('#cgpa_label').html('');

                $('#sch_type').attr('disabled', true);
                $('#sch_percentage').attr('disabled', true);
                $('#sch_amount').attr('disabled', true);
                $('#sch_arrear').attr('disabled', true);
                $('#sch_details').attr('disabled', true);
            }
       },
       error: function(xhr, status, error) {
            $('#error-flag').text('Error');
            $('#error-flag').addClass('show');
            setTimeout(function() {
                $('#error-flag').removeClass('show');
            }, 3000);
       }
   });
}
function submitForm() {
    var bi_form = document.querySelector('#studentRecord');
    var data = new FormData(bi_form);
    $("#processing_message").show();
    fetch('/api/save-student-record', {
            method: 'POST',
            body: data,
            _token: '{{ csrf_token() }}',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $("#error_message").text('').hide();
                $("#processing_message").hide();
                $('#success_message').html(data.message);
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(3000);
                $('#sai_enrollment_no').val('');
                $('#sch_percentage').val('');
                $('#sch_amount').val('');
                $('#sch_arrear').val('');
                $('#sch_details').val('');
                clearSrmStudentPhoto();
                clearSrmProfileScholarships();
                filterSchlrDetail();
            } else {
                var message = "";
                if ('messege' in data) {
                    message = data.messege;
                } else if ('error' in data) {
                    var errors = data.error;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(10000);
            }
        })
        .catch(error => {
            console.error('There was an error!', error);
        });
}
function LoadSchlrshipDetail(studentType=null, session=null){
        $('#data-table').DataTable({
            scrollX: true,
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false,
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 50,
            lengthMenu: [[50, 100, 500, 1000, 10000, 50000],
             [50, 100, 500, 1000, 10000, 50000]],
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/api/get-scholarship-record",
                data: function(d) {
                    d.studentType = studentType;
                    d.session = session;
                },
            },
            columns: [
                {data: "sess_name"},
                {data: "enrollmentno"},
                {
                    data: "student_status_cms",
                    defaultContent: "NA",
                    render: function (data, type, row) {
                        return (data === null || data === '') ? "NA" : data;
                    }
                },
                {
                    // CGPA comes from the Oracle CGPA(enrollment_no) function and is
                    // only meaningful for existing students; new admissions get NULL.
                    data: "cgpa",
                    defaultContent: "—",
                    render: function (data, type, row) {
                        if (data === null || data === '' || typeof data === 'undefined') { return "—"; }
                        if (type !== 'display') { return data; }   // keep raw value for sort/filter/export
                        var n = parseFloat(data);
                        return isNaN(n) ? data : n.toFixed(2);
                    }
                },
                {data: "apply_id"},
                {data: "studenttype"},
                {data: "studnt_name"},
                {data: "school_name"},
                {data: "prog_name"},
                {data: "schlr_name"},
                {data: "scholarship_category"},
                {data: "schlr_percent"},
                {
                    data: "arrear",
                    render: function (data, type, row) {
                        return data === null ? "NA" : data;
                    }
                },
                {data: "schlr_detail"}
            ],
            columnDefs: [],
            autoWidth: false,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Scholarship',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Scholarship',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        }
                },
                {
                    extend: 'print',
                    title: 'Scholarship',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        }
                }
            ]
        });
}


$('#sai_enrollment_no').autoComplete({
    resolver: 'custom',
    autoSelect: false,
    formatResult: function (item) {
        return {
            value: 'hello ap',
            text: item.text + "[" + item.value + "] ",
        };
    },
    select : function (evt, item){
    },
    events: {
        search: function (qry, callback) {
            var studentType = $('#studentType').val();
            $.ajax(
                '<?=url('api/library/search-user-for-suggestion')?>',
                {
                    data: { 'qry': qry, 'studentType': studentType}
                }
            ).done(function (res) {
                callback(res)
            });
        }
    }
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
                    $('#sai_enrollment_no').val(item.value);
});
function session_enrollment_change(){
    let session_id = $('#academic_session').val();
    let student_id = $('#sai_enrollment_no').val();
    let scholarship_id = $('#sch_type').val();

    if ($('#hostel_scholarship').is(':checked')) {
        if(session_id && student_id && scholarship_id){
            getHostelScholrshp_student(session_id, student_id, scholarship_id);
            $('#sch_percentage').val('');
            $('.hotelSch-amount').removeClass('d-none');
            $('#sch_percentage').prop('readonly', true);
        }else{
            $('#hostel_scholarship').prop('checked', false);
            alert('Please select Session, Scholarship Type and enter Student Id')
        }

    } else {
        $('.hotelSch-amount').addClass('d-none');
        $('#sch_amount').val('');
        $('#sch_percentage').prop('readonly', false);
    }
    $('#sch_amount').val('');
}
// Re-evaluate the hostel fee structure for the current student/session: unlock
// and reset the dropdown to a default, then let getHostelFeeStructure resolve
// (and re-lock it if a hostel is assigned). Used on session change, search,
// hostel-checkbox check and hostel-row edit.
// Resolve (up front, regardless of the Hostel checkbox) whether the student has
// a hostel assigned this session, so add / copy of hostel scholarships can be
// allowed or blocked reliably. UI-side-effect free.
function resolveHostelAssigned() {
    hostelAssigned = false;
    var session = $('#academic_session').val();
    var student_id = $('#sai_enrollment_no').val();
    var studentType = $('#studentType').val();
    if (!session || !student_id) { return; }
    $.ajax({
        url: '/api/get-hostel-feeStructure/' + session + '/Regular/' + student_id + '?student_type=' + encodeURIComponent(studentType),
        type: 'GET',
        cache: false,
        success: function(res) { hostelAssigned = !!(res && res.hostel_assigned); }
    });
}
function refreshHostelFeeStructure(){
    if (!$('#hostel_scholarship').is(':checked')) {
        $('#hostle_fee_structure').prop('disabled', false);
        return;
    }
    $('#hostle_fee_structure').prop('disabled', false).val('Regular');
    getHostelFeeStructure();
}
function getHostelFeeStructure(){
    let session = $('#academic_session').val();
    let hostelfeeStruct_type = $('#hostle_fee_structure').val();
    let student_id = $('#sai_enrollment_no').val();
    let studentType = $('#studentType').val();
    let isHostelschlrshp = false;
    if($('#hostel_scholarship').is(':checked')){
        isHostelschlrshp = true;
    }
    if(isHostelschlrshp){
        $.ajax({
                url: "/api/get-hostel-feeStructure/" + session + "/" + hostelfeeStruct_type + "/" + student_id + "?student_type=" + encodeURIComponent(studentType),
                type: 'GET',
                cache: false,
                success: function(res) {
                    console.log(res);

                    // Track whether the student has a hostel assigned this session
                    // (hostel scholarships are only allowed when assigned).
                    hostelAssigned = !!res.hostel_assigned;

                    // Reflect the type resolved from the student's assigned hostel
                    // (e.g. Regular -> Regular_outside). Set WITHOUT triggering
                    // change to avoid a re-entrant AJAX loop.
                    if (res.resolved_type && res.resolved_type !== $('#hostle_fee_structure').val()) {
                        $('#hostle_fee_structure').val(res.resolved_type);
                    }

                    // When a hostel is assigned, the fee structure is driven by the
                    // assignment -> lock the dropdown. Allow manual selection only
                    // when no hostel is assigned for the student/session.
                    $('#hostle_fee_structure').prop('disabled', !!res.auto_detected);

                    // Intimate when the student has no hostel assignment for the session.
                    $('#hostel-assign-notice').text(res.notice ? res.notice : '');

                    if(res.status){
                        $('#hostelfee').val(res.hostelFee);
                        session_enrollment_change();
                    }else{
                        $('#hostelfee').val('');
                        alert(res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                }
            });
        }

        session_enrollment_change();
}
function getHostelScholrshp_student(session, student_id, scholarship_id){
    $.ajax({
        url: "/api/get-hostelScholarship-student/" + session + "/" + student_id + "/" + scholarship_id,
        type: 'GET',
        success: function(res) {
            console.log(res);
            totalAwardedAmount_hostel = res.totalAwarded_amount;
            totalAwardedPercent_hostel = res.totalAwarded_percentage;
            hostelFee = $('#hostelfee').val();
            // console.log(hostelFee);
            // console.log(1);
            permitted_amount = hostelFee - totalAwardedAmount_hostel;
            $('#amount-range-message').text('Maximum scholarship could be given: '+permitted_amount);
            $('#allowed-amount').val(permitted_amount);
            if(res.status){
            }else{
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}
function calculatePercent(element){
    let hostel_sch_amount = element.value;
    if(hostel_sch_amount <= permitted_amount){
        let totalHostelFee = $('#hostelfee').val();
        let percentage_scholarship=0;
        if(totalHostelFee && totalHostelFee != 0){
            percentage_scholarship = (hostel_sch_amount * 100)/totalHostelFee;
            percentage_scholarship =  Number(percentage_scholarship.toFixed(2));
            $('#sch_percentage').val(percentage_scholarship);
        }else{
            alert('Hostel Fee can not be empty or 0');
        }
    }else{
        $('#sch_amount').val('');
        alert('Scholarship amount could not exceed '+permitted_amount);
    }
}
function downloadExcel() {
    const table = document.querySelector('.table');
    const clonedTable = table.cloneNode(true);
    const rows = clonedTable.querySelectorAll('tr');
    rows.forEach(row => {
        const lastCellIndex = row.cells.length - 1;
        row.deleteCell(lastCellIndex);
    });
    const wb = XLSX.utils.table_to_book(clonedTable);
    XLSX.writeFile(wb, 'student-record.xlsx');
}
function verifyCgpa(element){
}
function filterSchlrDetail() {
    let studentType = $('#filterStudent').val();
    let session = $('#filterSession').val();
    LoadSchlrshipDetail(studentType, session);
}

function syncScholarshipEnrollmentFromAdmission() {
    var btn = document.getElementById('btnSyncSchlrEnrollment');
    if (btn) {
        btn.disabled = true;
    }
    $("#processing_message").show();
    $('#error_message').text('').hide();
    $('#success_message').text('').hide();

    fetch('/api/sync-scholarship-enrollment-from-admission', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            });
        })
        .then(function (result) {
            $("#processing_message").hide();
            if (btn) {
                btn.disabled = false;
            }
            if (result.ok && result.data.success) {
                $('#success_message').html(result.data.message || 'Completed.');
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(8000);
                filterSchlrDetail();
            } else {
                var msg = (result.data && result.data.message) ? result.data.message : ('Request failed (' + result.status + ')');
                $('#error_message').html('<p>' + msg + '</p>');
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(10000);
            }
        })
        .catch(function (error) {
            $("#processing_message").hide();
            if (btn) {
                btn.disabled = false;
            }
            $('#error_message').html('<p>Network or server error.</p>');
            $('#error_message').fadeIn('slow');
            console.error(error);
        });
}
</script>
@stop
