@extends('layouts.app')

<style>
/* Figma-aligned Staff Exit Feedback Form presentation layer.
   Existing Blade/PHP conditions, fields, names, IDs and JavaScript remain unchanged. */
.staff-exit-root {
    min-height: 100vh;
    /* padding: 24px 26px 44px; */
    background: #f2f6f9;
    font-family: 'Montserrat', sans-serif;
    color: #252525;
}

.staff-exit-root .card-border-primary {
    border: 0;
    background: transparent;
    box-shadow: none;
}

.staff-exit-root .card-border-primary .alert-success {
    margin: 0 auto;
    max-width: 1380px;
    padding: 18px 22px;
    border: 1px solid #cfe9e3;
    border-radius: 12px;
    background: #eff9f6;
    color: #17675f;
    box-shadow: none;
}

.staff-exit-page {
    width: 100% !important;
    /* max-width: 1380px; */
    margin: 0 auto !important;
}

.staff-exit-page > .card:first-child {
    margin: 0 0 18px;
    border: 0;
    background: transparent;
    box-shadow: none;
}

.staff-exit-page > .card:first-child .card-header.staff-exit-heading {
    position: relative;
    display: block !important;
    min-height: 58px;
    padding: 0 0 10px !important;
    border: 0;
    background: transparent !important;
    color: #252525 !important;
}

.staff-exit-page .staff-exit-heading h3 {
    margin: 0;
    color: #252525;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px;
    font-weight: 600;
    line-height: 1.35;
}

.staff-exit-page .staff-exit-heading::after {
    content: 'Share your feedback before you leave BNU';
    display: block;
    margin-top: 5px;
    color: #64748b;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.4;
}

.staff-exit-page .staff-exit-form-shell {
    padding: 0 !important;
    background: transparent;
}

/* Personal information card: preserve original two rows/fields */
.staff-exit-page .personal-info-row {
    margin: 0 !important;
    padding: 0 28px;
    background: #fff;
}

.staff-exit-page .personal-info-row.personal-info-top {
    padding-top: 28px;
    padding-bottom: 12px;
    border-radius: 12px 12px 0 0;
}

.staff-exit-page .personal-info-row.personal-info-bottom {
    padding-top: 12px;
    padding-bottom: 28px;
    border-radius: 0 0 12px 12px;
    margin-bottom: 24px !important;
}

.staff-exit-page .personal-info-row > [class*='col-'] {
    padding-left: 0;
    padding-right: 0;
}

.staff-exit-page .personal-info-row > [class*='col-']:first-child {
    padding-right: 16px;
}

.staff-exit-page .personal-info-row > [class*='col-']:last-child {
    padding-left: 16px;
}

.staff-exit-page .col-form-label,
.staff-exit-page .form-group > label,
.staff-exit-page table label {
    margin-bottom: 8px;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.35;
}

.staff-exit-page .personal-info-row .col-form-label {
    text-transform: uppercase;
    letter-spacing: .02em;
}

.staff-exit-page .form-control {
    min-height: 44px;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    color: #334155;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    line-height: 1.45;
    box-shadow: none;
}

.staff-exit-page .form-control:focus {
    border-color: #17675f;
    box-shadow: 0 0 0 3px rgba(23,103,95,.10);
}

.staff-exit-page .form-control[readonly] {
    background: #fff;
    color: #64748b;
}

/* Principal reason */
.staff-exit-page .principal-reason-section {
    margin: 0 0 24px;
}

.staff-exit-page .principal-reason-section br {
    display: none;
}

.staff-exit-page .principal-reason-section h4 {
    margin: 0 0 10px;
    color: #334155;
    font-size: 15px;
    font-weight: 600;
}

.staff-exit-page #q0 {
    height: 72px;
    min-height: 72px;
    resize: vertical;
    border-radius: 12px;
}

/* Section headings / banners */
.staff-exit-page .position-section-title {
    margin: 28px 0 14px;
    color: #334155;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.4;
}

.staff-exit-page .section-banner {
    margin: 0;
    padding: 16px;
    border-radius: 12px 12px 0 0;
    background: #17675f;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.35;
}

/* Rating tables */
.staff-exit-page .rating-section {
    margin: 0 0 28px;
    padding: 20px 28px 24px;
    border-radius: 0 0 12px 12px;
    background: #fff;
    overflow-x: auto;
}

.staff-exit-page .rating-section .table {
    min-width: 930px;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.staff-exit-page .rating-section thead th {
    padding: 11px 10px;
    border: 0;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
    color: #17675f;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    white-space: nowrap;
}

.staff-exit-page .rating-section thead th:first-child,
.staff-exit-page .rating-section tbody td:first-child {
    width: 48%;
    text-align: left;
}

.staff-exit-page .rating-section tbody td {
    height: 48px;
    padding: 9px 10px;
    border: 0;
    vertical-align: middle;
    color: #334155;
    font-size: 13px;
    text-align: center;
}

.staff-exit-page .rating-section tbody tr:nth-child(even) td {
    background: rgba(15,118,110,.05);
}

.staff-exit-page input[type='radio'] {
    width: 18px;
    height: 18px;
    margin: 0;
    accent-color: #17675f;
    vertical-align: middle;
    cursor: pointer;
}

.staff-exit-page .rating-section textarea.form-control {
    width: 100%;
    /* min-width: 120px; */
    height: 32px;
    min-height: 32px;
    padding: 5px 8px;
    border-radius: 4px;
    resize: vertical;
}

/* Workload */
.staff-exit-page .workload-banner {
    margin-top: 4px;
}

.staff-exit-page .workload-table-card {
    width: 100%;
    margin: 0 0 24px;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 0 0 12px 12px;
    background: #fff;
    overflow: hidden;
}

.staff-exit-page .workload-table-card thead th {
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid #e2e8f0;
    color: #17675f;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
}

.staff-exit-page .workload-table-card thead th:first-child,
.staff-exit-page .workload-table-card tbody td:first-child {
    width: 46%;
    text-align: left;
}

.staff-exit-page .workload-table-card tbody td {
    padding: 18px 16px;
    border: 0;
    color: #334155;
    font-size: 13px;
    text-align: center;
    vertical-align: middle;
}

/* q16-q19 open answers */
.staff-exit-page .career-trigger-section,
.staff-exit-page .open-text-section,
.staff-exit-page .yes-no-section {
    margin: 0 0 24px;
    padding: 20px 28px;
    border-radius: 12px;
    background: #fff;
    overflow: visible;
}

.staff-exit-page .career-trigger-section .table,
.staff-exit-page .open-text-section .table,
.staff-exit-page .yes-no-section .table {
    margin: 0;
}

.staff-exit-page .career-trigger-section tbody,
.staff-exit-page .career-trigger-section tr,
.staff-exit-page .career-trigger-section td,
.staff-exit-page .open-text-section tbody,
.staff-exit-page .open-text-section tr,
.staff-exit-page .open-text-section td {
    display: block;
    width: 100%;
}

.staff-exit-page .career-trigger-section td,
.staff-exit-page .open-text-section td {
    padding: 0 0 20px !important;
    border: 0 !important;
}

.staff-exit-page .career-trigger-section tr:last-child td,
.staff-exit-page .open-text-section tr:last-child td {
    padding-bottom: 0 !important;
}

.staff-exit-page .career-trigger-section label,
.staff-exit-page .open-text-section label {
    display: block;
    margin: 0 0 8px;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
}

.staff-exit-page .open-text-section textarea.form-control {
    min-height: 116px;
    resize: vertical;
}

/* Yes / No section */
.staff-exit-page .yes-no-section tbody {
    display: block;
}

.staff-exit-page .yes-no-section tr {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(360px, 44%);
    gap: 20px;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #edf2f4;
}

.staff-exit-page .yes-no-section tr:last-child {
    border-bottom: 0;
}

.staff-exit-page .yes-no-section td {
    display: block;
    width: auto;
    padding: 0 !important;
    border: 0 !important;
}

.staff-exit-page .yes-no-section td:first-child label {
    margin: 0;
    color: #334155;
    font-size: 14px;
    font-weight: 500;
}

.staff-exit-page .yes-no-section td:last-child {
    display: grid;
    grid-template-columns: auto auto minmax(170px, 1fr);
    gap: 8px;
    align-items: center;
}

.staff-exit-page .yes-no-section td:last-child > label {
    min-width: 58px;
    margin: 0 4px 0 -28px;
    padding: 8px 18px 8px 30px;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    background: #fff;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
}

.staff-exit-page .yes-no-section td:last-child input[type='radio'] {
    position: relative;
    z-index: 2;
    margin-left: 12px;
}

.staff-exit-page .yes-no-section textarea.form-control {
    min-height: 44px;
    height: 44px;
    resize: vertical;
}

/* Career opportunities (checkboxes) */
.staff-exit-page .career-opportunities-card {
    margin: 20px 0 0;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fbfa;
}

.staff-exit-page .career-opportunities-card > label:first-child {
    display: block;
    margin-bottom: 14px;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
}

.staff-exit-page input[type='checkbox'] {
    width: 17px;
    height: 17px;
    margin-right: 8px;
    accent-color: #17675f;
    vertical-align: middle;
}

.staff-exit-page .career-opportunities-card label:not(:first-child) {
    display: inline-block;
    margin: 4px 0 10px;
    color: #475569;
    font-size: 13px;
    font-weight: 500;
}

.staff-exit-page #other_description {
    margin-top: 8px;
    min-height: 90px;
    resize: vertical;
}

/* Submit button */
.staff-exit-page .staff-exit-submit {
    min-width: 126px;
    min-height: 48px;
    margin: 0 0 28px;
    padding: 12px 26px;
    border: 1px solid #17675f !important;
    border-radius: 10px;
    background: #17675f !important;
    color: #fff !important;
    font-size: 14px;
    font-weight: 700;
    box-shadow: none !important;
}

.staff-exit-page .staff-exit-submit:hover,
.staff-exit-page .staff-exit-submit:focus {
    border-color: #12564f !important;
    background: #12564f !important;
    color: #fff !important;
}

/* Success modal */
.staff-exit-page .modal-content {
    border: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.18);
}

.staff-exit-page .modal-header {
    border: 0;
    background: #17675f;
    color: #fff;
}

.staff-exit-page .modal-header .modal-title,
.staff-exit-page .modal-header .close {
    color: #fff;
    text-shadow: none;
}

.staff-exit-page .modal-footer {
    border-top: 1px solid #e2e8f0;
}

.staff-exit-page .btn-secondary {
    border-color: #e2e8f0;
    background: #f8fafc;
    color: #475569;
}

@media (max-width: 991.98px) {
    .staff-exit-root {
        padding: 22px 16px 34px;
    }

    .staff-exit-page .personal-info-row > [class*='col-']:first-child,
    .staff-exit-page .personal-info-row > [class*='col-']:last-child {
        padding-left: 0;
        padding-right: 0;
    }

    .staff-exit-page .personal-info-row > [class*='col-'] + [class*='col-'] {
        margin-top: 16px;
    }

    .staff-exit-page .yes-no-section tr {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}

@media (max-width: 767.98px) {
    .staff-exit-root {
        padding: 18px 12px 28px;
    }

    .staff-exit-page .personal-info-row,
    .staff-exit-page .rating-section,
    .staff-exit-page .career-trigger-section,
    .staff-exit-page .open-text-section,
    .staff-exit-page .yes-no-section {
        padding-left: 16px;
        padding-right: 16px;
    }

    .staff-exit-page .yes-no-section td:last-child {
        grid-template-columns: auto auto;
    }

    .staff-exit-page .yes-no-section textarea.form-control {
        grid-column: 1 / -1;
        width: 100%;
        margin-top: 8px;
    }
}
/* Yes / No controls - same row, explanation next row */
.staff-exit-page .yes-no-section td:last-child {
    display: grid;
    grid-template-columns: 20px auto 20px auto;
    align-items: center;
    justify-content: start;
    column-gap: 8px;
    row-gap: 10px;
}

/* Radio buttons */
.staff-exit-page .yes-no-section td:last-child input[type='radio'] {
    width: 18px;
    height: 18px;
    margin: 0 !important;
    position: static;
    accent-color: #17675f;
}

/* Yes / No labels */
.staff-exit-page .yes-no-section td:last-child > label {
    margin: 0 20px 0 0 !important;
    padding: 0 !important;
    min-width: auto !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #334155;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
}

/* Explanation always separate row */
.staff-exit-page .yes-no-section td:last-child textarea.form-control {
    grid-column: 1 / -1;
    width: 100%;
    height: 52px;
    min-height: 52px;
    margin-top: 2px;
    padding: 10px 14px;
    resize: vertical;
}



</style>


@section('content')
{{-- @dd("123"); --}}
<div class="staff-exit-root" style="padding: 0 !important;">


<?php
//dd($sqlChkForm);
if($sqlChkForm > 0){?>
    <div class="card card-border-primary">
            <div class="alert alert-success">
                <center>
                    <strong>Instructions!</strong>
                        Your Staff Exit Feedback Form has submitted Successfully
                    <!-- <button type="button" onclick="btnPending()">Check Status</button> -->
                </center>
            </div>
       
    </div>

<?php } else { ?>
<div class="staff-exit-page" style="margin:10px auto;">
<div class="card">     
    <div class="card-header staff-exit-heading" style=" color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Staff Exit Feedback Form</h3>
       
    </div>
</div>
<div class="card-content staff-exit-form-shell" style="padding: 20px;"> 
  
<form  id = 'staffExitfeedbackForm'>
    <!-- @csrf -->
    <div class="form-group row personal-info-row personal-info-top">
                    <div class="col-sm-6">
                    
                        <label class="col-form-label">Name:</label>
                        <input  type="text" readonly  class="form-control" name="name" id="name" value='<?php echo auth()->user()->full_name  ?>' placeholder="Enter student's name">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label">User Name</label>
                        <input type="text"  readonly class="form-control" name="er_sai_enrollment_no" id="er_sai_enrollment_no" value='<?php echo auth()->user()->username  ?>'  placeholder="Enter enrollment number">
                    </div>
                </div>
                <div class="form-group row personal-info-row personal-info-bottom">
                    <div class="col-sm-6">
                        <label class="col-form-label">School:</label>
                        <input type="text"  readonly  class="form-control" name="school" id="school" value='<?php echo auth()->user()->school  ?>'  placeholder="Enter school name">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label">Department:</label>
                        <input type="text"  readonly  class="form-control" name="department" id="department" value='<?php echo auth()->user()->department  ?>' placeholder="Enter department name">
                    </div>
                </div>
    </div>

    <div class="principal-reason-section" style="margin-bottom: 0 !important;">
    <br>
    <h4>Principal reason for leaving:</h4>
       
                                 <td>
                                    <!-- <label for="q29">Principal reason for leaving:</label> -->
                                    <textarea id="q0" name="q0" class="form-control"  maxlength="3500" required></textarea>
                                </td>
    </div>
    <br>

            <h2 class="position-section-title">Questions on the position you held while employed at BNU:</h2>

            <h3 class="section-banner">What is your assessment of your supervisory chain in regard to the following?:</h3>
            <div class="table-responsive rating-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Excellent</th>
                            <th>Good</th>
                            <th>Fair</th>
                            <th>Poor</th>
                            <th>N/A</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Fair and equal treatment</td>
                            <td><input type="radio" name="q1" value="Excellent" required></td>
                            <td><input type="radio" name="q1" value="Good" required></td>
                            <td><input type="radio" name="q1" value="Fair" required></td>
                            <td><input type="radio" name="q1" value="Poor" required></td>
                            <td><input type="radio" name="q1" value="N/A" required></td>
                            <td><textarea name="q1_comments" class="form-control"   maxlength="3500"></textarea></td>
                        </tr>
                        <tr>
                            <td>Recognition</td>
                            <td><input type="radio" name="q2" value="Excellent" required></td>
                            <td><input type="radio" name="q2" value="Good" required></td>
                            <td><input type="radio" name="q2" value="Fair" required></td>
                            <td><input type="radio" name="q2" value="Poor" required></td>
                            <td><input type="radio" name="q2" value="N/A" required></td>
                            <td><textarea name="q2_comments" class="form-control"   maxlength="3500"></textarea></td>
                        </tr>
                        <tr>
                            <td>Cooperation and teamwork</td>
                            <td><input type="radio" name="q3" value="Excellent" required></td>
                            <td><input type="radio" name="q3" value="Good" required></td>
                            <td><input type="radio" name="q3" value="Fair" required></td>
                            <td><input type="radio" name="q3" value="Poor" required></td>
                            <td><input type="radio" name="q3" value="N/A" required></td>
                            <td><textarea name="q3_comments" class="form-control"  maxlength="3500"></textarea></td>
                        </tr>
                        <tr>
                            <td>Training/Development Opportunities</td>
                            <td><input type="radio" name="q4" value="Excellent" required></td>
                            <td><input type="radio" name="q4" value="Good" required></td>
                            <td><input type="radio" name="q4" value="Fair" required></td>
                            <td><input type="radio" name="q4" value="Poor" required></td>
                            <td><input type="radio" name="q4" value="N/A" required></td>
                            <td><textarea name="q4_comments" class="form-control"   maxlength="3500"></textarea></td>
                        </tr>
                        <tr>
                            <td>Policies and procedures</td>
                            <td><input type="radio" name="q5" value="Excellent" required></td>
                            <td><input type="radio" name="q5" value="Good" required></td>
                            <td><input type="radio" name="q5" value="Fair" required></td>
                            <td><input type="radio" name="q5" value="Poor" required></td>
                            <td><input type="radio" name="q5" value="N/A" required></td>
                            <td><textarea name="q5_comments" class="form-control"   maxlength="3500"></textarea></td>
                        </tr>
                        <tr>
                            <td>Communication</td>
                            <td><input type="radio" name="q6" value="Excellent" required></td>
                            <td><input type="radio" name="q6" value="Good" required></td>
                            <td><input type="radio" name="q6" value="Fair" required></td>
                            <td><input type="radio" name="q6" value="Poor" required></td>
                            <td><input type="radio" name="q6" value="N/A" required></td>
                            <td><textarea name="q6_comments" class="form-control"   maxlength="3500"></textarea></td>
                        </tr>
                        <!-- Repeat for other questions -->
                    </tbody>
                </table>

                </div>

                    <h3 class="section-banner">How relevant were the below factors in your decision to leave BNU?</h3>

                    <div class="table-responsive rating-section">
                    <table class="table">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Excellent</th>
                                    <th>Good</th>
                                    <th>Fair</th>
                                    <th>Poor</th>
                                    <th>N/A</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Previous questions -->
                                <tr>
                                    <td>Tenure/ Promotion opportunities</td>
                                    <td><input type="radio" name="q7" value="Excellent" required></td>
                                    <td><input type="radio" name="q7" value="Good" required></td>
                                    <td><input type="radio" name="q7" value="Fair" required></td>
                                    <td><input type="radio" name="q7" value="Poor" required></td>
                                    <td><input type="radio" name="q7" value="N/A" required></td>
                                    <td><textarea name="q7_comments" class="form-control"  maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Decision making autonomy</td>
                                    <td><input type="radio" name="q8" value="Excellent" required></td>
                                    <td><input type="radio" name="q8" value="Good" required></td>
                                    <td><input type="radio" name="q8" value="Fair" required></td>
                                    <td><input type="radio" name="q8" value="Poor" required></td>
                                    <td><input type="radio" name="q8" value="N/A" required></td>
                                    <td><textarea name="q8_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Pay, benefits and incentives</td>
                                    <td><input type="radio" name="q9" value="Excellent" required></td>
                                    <td><input type="radio" name="q9" value="Good" required></td>
                                    <td><input type="radio" name="q9" value="Fair" required></td>
                                    <td><input type="radio" name="q9" value="Poor" required></td>
                                    <td><input type="radio" name="q9" value="N/A" required></td>
                                    <td><textarea name="q9_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Job Satisfaction</td>
                                    <td><input type="radio" name="q10" value="Excellent" required></td>
                                    <td><input type="radio" name="q10" value="Good" required></td>
                                    <td><input type="radio" name="q10" value="Fair" required></td>
                                    <td><input type="radio" name="q10" value="Poor" required></td>
                                    <td><input type="radio" name="q10" value="N/A" required></td>
                                    <td><textarea name="q10_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Salary</td>
                                    <td><input type="radio" name="q11" value="Excellent" required></td>
                                    <td><input type="radio" name="q11" value="Good" required></td>
                                    <td><input type="radio" name="q11" value="Fair" required></td>
                                    <td><input type="radio" name="q11" value="Poor" required></td>
                                    <td><input type="radio" name="q11" value="N/A" required></td>
                                    <td><textarea name="q11_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>HOD</td>
                                    <td><input type="radio" name="q12" value="Excellent" required></td>
                                    <td><input type="radio" name="q12" value="Good" required></td>
                                    <td><input type="radio" name="q12" value="Fair" required></td>
                                    <td><input type="radio" name="q12" value="Poor" required></td>
                                    <td><input type="radio" name="q12" value="N/A" required></td>
                                    <td><textarea name="q12_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Dean</td>
                                    <td><input type="radio" name="q13" value="Excellent" required></td>
                                    <td><input type="radio" name="q13" value="Good" required></td>
                                    <td><input type="radio" name="q13" value="Fair" required></td>
                                    <td><input type="radio" name="q13" value="Poor" required></td>
                                    <td><input type="radio" name="q13" value="N/A" required></td>
                                    <td><textarea name="q13_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Overall rating of BNU</td>
                                    <td><input type="radio" name="q14" value="Excellent" required></td>
                                    <td><input type="radio" name="q14" value="Good" required></td>
                                    <td><input type="radio" name="q14" value="Fair" required></td>
                                    <td><input type="radio" name="q14" value="Poor" required></td>
                                    <td><input type="radio" name="q14" value="N/A" required></td>
                                    <td><textarea name="q14_comments" class="form-control"   maxlength="3500"></textarea></td>
                                </tr>
                            </tbody>
                    </table>

                </div>
                <h3 class="section-banner workload-banner"> Workload</h3>
                <table class="table workload-table-card">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Overburdened</th>
                            <th>Varied, but manageable</th>
                            <th>Just right</th>
                            <th>Too light</th>
                            <!-- <th>Comments</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Previous questions -->
                        <!-- ... -->
                        <tr>
                            <td>What was your workload usually?</td>
                            <td><input type="radio" name="q15" value="Overburdened" required></td>
                            <td><input type="radio" name="q15" value="Varied, but manageable" required></td>
                            <td><input type="radio" name="q15" value="Just right" required></td>
                            <td><input type="radio" name="q15" value="Too light" required></td>
                            <!-- <td><textarea name="q15_comments" class="form-control"  maxlength="3500"></textarea></td> -->
                        </tr>
                    </tbody>
                </table>

                <div class="table-responsive career-trigger-section">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        <label for="q16">Did any specific event trigger your decision to leave?</label>
                                        <input type="text" id="q16" name="q16" class="form-control" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="q17">When did you begin searching for another job?</label>
                                        <input type="text" id="q17" name="q17" class="form-control" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="q18">What makes the new job more attractive than your current position?</label>
                                        <input type="text" id="q18" name="q18" class="form-control" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="q19">Have you spoken with anyone, either your director or any of your colleagues about your career goals?</label>
                                        <input type="text" id="q19" name="q19" class="form-control" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                </div>

                <div class="table-responsive yes-no-section">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <label for="q20">Did you receive timely, useful, and adequate feedback on your performance?</label>
                                </td>
                                <td>
                                    <input type="radio" id="q20_yes" name="q20" value="Yes" required>
                                    <label for="q20_yes">Yes</label>
                                    <input type="radio" id="q20_no" name="q20" value="No" required>
                                    <label for="q20_no">No</label>
                                    <textarea id="q20_explanation" name="q20_explanation" class="form-control" placeholder="Please explain"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q21">Did you have clear goals and know what was expected of you in your job?</label>
                                </td>
                                <td>
                                    <input type="radio" id="q21_yes" name="q21" value="Yes" required>
                                    <label for="q21_yes">Yes</label>
                                    <input type="radio" id="q21_no" name="q21" value="No" required>
                                    <label for="q21_no">No</label>
                                    <textarea id="q21_explanation" name="q21_explanation" class="form-control" placeholder="Please explain"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q22">Did you have the resources and support necessary to accomplish your job?</label>
                                </td>
                                <td>
                                    <input type="radio" id="q22_yes" name="q22" value="Yes" required>
                                    <label for="q22_yes">Yes</label>
                                    <input type="radio" id="q22_no" name="q22" value="No" required>
                                    <label for="q22_no">No</label>
                                    <textarea id="q22_explanation" name="q22_explanation" class="form-control" placeholder="Please explain"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q23">Did you understand and feel a part of the accomplishment of the BNU mission?</label>
                                </td>
                                <td>
                                    <input type="radio" id="q23_yes" name="q23" value="Yes" required>
                                    <label for="q23_yes">Yes</label>
                                    <input type="radio" id="q23_no" name="q23" value="No" required>
                                    <label for="q23_no">No</label>
                                    <textarea id="q23_explanation" name="q23_explanation" class="form-control" placeholder="Please explain"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>


                <div class="table-responsive open-text-section">
                    <table class="table">
                        <tbody>
                           
                            <tr>
                                <td>
                                    <label for="q24">What improvements (to BNU, your department, or to your job) would you suggest?</label>
                                    <textarea required id="q24" name="q24" class="form-control"></textarea >
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q25">What recommendation would you have for making the BNU as a whole a better place to work?</label>
                                    <textarea required id="q25" name="q25" class="form-control"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q26">What did you like most about your job?</label>
                                    <textarea required id="q26" name="q26" class="form-control"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q27">What was the least exciting part of your job?</label>
                                    <textarea  required id="q27" name="q27" class="form-control"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q28">What makes BNU a good place to work?</label>
                                    <textarea  required id="q28" name="q28" class="form-control"  maxlength="3500"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="q29">What makes BNU a poor place to work?</label>
                                    <textarea required id="q29" name="q29" class="form-control"  maxlength="3500"></textarea>
                                </td>
                                    
                            </tr>
                            <tr>
                                <td>
                                    <label for="q30">In your opinion have there been adequate career progression opportunities available within BNU?</label>
                                    <input required type="text" id="q30" name="q30" class="form-control">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group career-opportunities-card">
                        <label for="opportunities">What types of career opportunities are important to you? (Select all that apply)</label><br>
                        <input type="checkbox" id="promotional_opportunities" name="q31[]" value="Promotional Opportunities">
                        <label for="promotional_opportunities">Promotional Opportunities</label><br>
                        <input type="checkbox" id="position_rotations" name="q31[]" value="Position Rotations">
                        <label for="position_rotations">Position Rotations</label><br>
                        <input type="checkbox" id="special_projects" name="q31[]" value="Special Projects">
                        <label for="special_projects">Special Projects</label><br>
                        <input type="checkbox" id="other" name="q31[]" value="Other">
                        <label for="other">Other</label><br>
                        <textarea id="other_description" name="other_description" class="form-control" placeholder="If you selected other, please elaborate"  maxlength="3500"></textarea>
                    </div>




                </div>


                    <button type="submit"  class="btn btn-primary staff-exit-submit">Submit</button>
                </div>
</form >
    <!-- Success Modal -->

    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Your form has been submitted successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>

</div>
<?php } ?>



@stop
@section('javascript')

 <script type="text/javascript">
           $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $("#address").focus();
        $('#staffExitfeedbackForm').on('submit', function(event) {
        event.preventDefault();

        // Show confirmation dialog
        if (confirm('Are you sure you want to submit this form?')) {
            $.ajax({
      url: '{{ route('staff-exit-interview-submit') }}',
      method: 'POST',
      data: $(this).serialize(),
      success: function(response) {
        if (response.message === 'You cannot request another one until the first one is completed.') {
          $('#errorModal').modal('show');
        } else {
          $('#successModal').modal('show');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        // Handle AJAX errors (optional)
        console.error('AJAX Error:', textStatus, errorThrown);
      }
    });
}
  });

});



</script>


@endsection

