@extends('layouts.app',['pageTitle' => 'BNU Fines'])
@section('content')
{{-- @dd("123"); --}}
<style>
    .student-fine-page {
        --sf-primary: #17675f;
        --sf-primary-dark: #12564f;
        --sf-bg: rgba(15, 118, 110, 0.05);
        --sf-text: #0f172a;
        --sf-muted: #64748b;
        --sf-border: #e2e8f0;
        --sf-soft: #f8fafc;
        width: 100%;
        min-height: calc(100vh - 110px);
        /* padding: 48px; */
        background: #f2f6f9;
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        color: var(--sf-text);
        box-sizing: border-box;
    }

    .student-fine-page *,
    .student-fine-page *::before,
    .student-fine-page *::after {
        box-sizing: border-box;
    }

    .student-fine-shell {
        width: 100%;
        /* max-width: 1704px; */
        margin: 0 auto;
    }

    .student-fine-header {
        margin-bottom: 32px;
    }

    .student-fine-header h1 {
        margin: 0 0 8px;
        color: var(--sf-text);
        font-size: 24px;
        line-height: 1.25;
        font-weight: 700;
    }

    .student-fine-header p {
        margin: 0;
        color: var(--sf-muted);
        font-size: 14px;
        line-height: 1.5;
        font-weight: 400;
    }

    .student-fine-search-card {
        width: 100%;
        padding: 32px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    }

    .student-fine-search-row {
        display: flex;
        align-items: flex-end;
        gap: 24px;
    }

    .student-fine-field {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .student-fine-field label,
    #dataDiv #submitFineForm label,
    #edit-Modal label {
        margin: 0;
        color: var(--sf-muted);
        font-size: 11px;
        line-height: 1.2;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .student-fine-page .form-control,
    #edit-Modal .form-control,
    #remarksModal textarea {
        width: 100%;
        min-height: 44px;
        padding: 10px 16px;
        border: 1px solid var(--sf-border, #e2e8f0) !important;
        border-radius: 8px !important;
        background: #fff;
        color: var(--sf-text, #0f172a);
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        font-size: 14px;
        box-shadow: none !important;
        outline: none !important;
    }

    .student-fine-page .form-control::placeholder,
    #remarksModal textarea::placeholder {
        color: #94a3b8;
    }

    .student-fine-page .form-control:focus,
    #edit-Modal .form-control:focus,
    #remarksModal textarea:focus {
        border-color: var(--sf-primary, #17675f) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, .10) !important;
    }

    .student-fine-search-button {
        width: 140px;
        min-width: 140px;
        height: 44px;
        margin: 0 !important;
        padding: 0 20px !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: var(--sf-primary) !important;
        color: #fff !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
        transition: background-color .2s ease, transform .2s ease;
    }

    .student-fine-search-button:hover,
    .student-fine-search-button:focus {
        background: var(--sf-primary-dark) !important;
        transform: translateY(-1px);
    }

    .student-fine-search-button i {
        display: none;
    }

    #dataDiv {
        margin-top: 24px !important;
    }

    #dataDiv > .container-fluid {
        padding: 0 !important;
    }

    /* Student summary card - styled like the Figma result card */
    #dataDiv > .container-fluid > .row.align-items-center {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin: 0 !important;
        padding: 32px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    }

    #dataDiv > .container-fluid > .row.align-items-center > [class*="col-"] {
        position: relative;
        width: auto !important;
        max-width: none !important;
        flex: none !important;
        margin: 0 !important;
        padding: 2px 20px 2px 24px !important;
        text-align: left !important;
        min-height: 62px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    #dataDiv > .container-fluid > .row.align-items-center > [class*="col-"]::before {
        content: '';
        position: absolute;
        left: 0;
        top: 1px;
        bottom: 1px;
        width: 3px;
        border-radius: 3px;
        /* background: #6e62e5; */
    }

    #dataDiv > .container-fluid > .row.align-items-center > [class*="col-"]:nth-child(2)::before { background: #10b981; }
    #dataDiv > .container-fluid > .row.align-items-center > [class*="col-"]:nth-child(3)::before { background: #f59e0b; }
    #dataDiv > .container-fluid > .row.align-items-center > [class*="col-"]:nth-child(4)::before { background: var(--sf-primary); }

    #dataDiv .lead {
        margin: 0 !important;
        color: var(--sf-text) !important;
        font-size: 14px !important;
        line-height: 1.55;
    }

    #dataDiv .lead strong {
        display: block !important;
        margin-bottom: 8px;
        color: var(--sf-muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    #dataDiv .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
    }

    #dataDiv .badge-info {
        background: #ecfdf5 !important;
        color: #059669 !important;
    }

    #dataDiv h4.text-primary {
        margin: 0 0 8px !important;
        color: var(--sf-text) !important;
        font-size: 18px !important;
        font-weight: 700 !important;
        text-transform: none !important;
    }

    #dataDiv img.rounded-circle {
        width: 56px !important;
        height: 56px !important;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px var(--sf-border);
        object-fit: cover;
    }

    /* Fine creation form */
    #dataDiv .col-md-12.text-left.mt-3 {
        width: 100%;
        max-width: none;
        padding: 0 !important;
        margin-top: 24px !important;
    }

    #dataDiv #submitFineForm {
        position: relative;
        padding: 64px 32px 32px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    }

    #dataDiv #submitFineForm::before {
        content: 'Fine Details';
        position: absolute;
        left: 32px;
        top: 26px;
        color: var(--sf-text);
        font-size: 16px;
        font-weight: 700;
    }

    #dataDiv #submitFineForm > .row {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 20px 24px;
        margin: 0 !important;
        align-items: end;
    }

    #dataDiv #submitFineForm .form-group {
        width: auto !important;
        max-width: none !important;
        flex: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #dataDiv #submitFineForm .form-group label {
        display: block;
        margin-bottom: 8px;
    }

    #dataDiv #submitFineForm .form-group.col-sm-12 {
        grid-column: 1 / -1;
        display: flex !important;
        justify-content: flex-end !important;
        padding-top: 4px !important;
    }

    #dataDiv #finesubmit {
        width: 140px;
        height: 52px;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 8px !important;
        background: var(--sf-primary) !important;
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
        box-shadow: none !important;
    }

    #dataDiv #finesubmit:hover { background: var(--sf-primary-dark) !important; }

    #dataDiv .validation-error {
        margin: 4px 0 0 !important;
        padding: 0 !important;
        font-size: 11px;
        font-weight: 500;
    }

    #successMessage {
        margin-top: 16px !important;
    }

    /* Existing challan table presented as a Figma-style data card */
    #dataDiv .table-responsive.mt-3 {
        width: 100%;
        margin-top: 24px !important;
        overflow-x: auto;
        border: 1px solid rgba(226, 232, 240, .8);
        border-radius: 12px;
        background: #fff;
        box-shadow: none;
    }

    #dataDiv .table-responsive.mt-3::before {
        content: 'Fine Challans';
        display: block;
        padding: 22px 24px 16px;
        color: var(--sf-text);
        font-size: 16px;
        font-weight: 700;
    }

    #dataDiv .table {
        width: 100%;
        min-width: 1120px;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        overflow: visible !important;
    }

    #dataDiv .table thead th {
        padding: 16px 14px !important;
        border: 0 !important;
        background: var(--sf-primary) !important;
        color: #fff !important;
        font-size: 11px !important;
        line-height: 1.25;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: .02em;
        vertical-align: middle;
        white-space: nowrap;
    }

    #dataDiv .table tbody td {
        padding: 14px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--sf-border) !important;
        background: #fff;
        color: #1e293b;
        font-size: 13px;
        line-height: 1.45;
        vertical-align: middle;
    }

    #dataDiv .table tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    #dataDiv .table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    #dataDiv .badge-danger { background: #fee2e2 !important; color: #dc2626 !important; }
    #dataDiv .badge-success { background: #dcfce7 !important; color: #16a34a !important; }
    #dataDiv .badge-secondary { background: #f1f5f9 !important; color: #64748b !important; }

    #dataDiv .table .btn {
        min-height: 34px;
        margin: 0 4px 0 0 !important;
        padding: 7px 12px !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    #dataDiv .table .btn-danger { background: #f43f5e !important; border-color: #f43f5e !important; }
    #dataDiv .table .btn-success { background: #16a34a !important; border-color: #16a34a !important; }
    #dataDiv .table .btn-primary { background: var(--sf-primary) !important; border-color: var(--sf-primary) !important; }

    .success-flag {
        position: fixed;
        top: 120px;
        right: -360px;
        z-index: 1100;
        padding: 14px 18px;
        border-radius: 10px 0 0 10px;
        background: #16a34a;
        color: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .16);
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        font-size: 13px;
        transition: right .35s ease;
    }

    .success-flag.show { right: 0; }

    /* Remarks modal: keep original IDs/JS, update presentation only */
    #remarksModal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1090;
        width: 100%;
        height: 100%;
        padding: 32px 16px;
        overflow: auto;
        background: rgba(15, 23, 42, .48);
    }

    #remarksModal .modal-content {
        width: min(560px, calc(100vw - 32px));
        margin: 10vh auto 0;
        padding: 28px;
        border: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
    }

    #remarksModal h3 {
        margin: 0 0 18px;
        color: #0f172a;
        font-size: 20px;
        font-weight: 700;
    }

    #remarksModal textarea {
        min-height: 120px;
        margin: 0 0 18px;
        resize: vertical;
    }

    #remarksModal button {
        min-height: 40px;
        margin: 0 8px 0 0;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    #remarksModal button:first-of-type {
        background: #f43f5e;
        color: #fff;
    }

    #remarksModal button:last-of-type {
        border-color: #e2e8f0;
        background: #fff;
        color: #64748b;
    }

    /* Edit modal visual alignment; modal/form structure and JS stay untouched */
    #edit-Modal { z-index: 1090 !important; }

    #edit-Modal .modal-dialog {
        width: min(620px, calc(100vw - 32px));
        max-width: 620px !important;
        margin-left: auto;
        margin-right: auto;
    }

    #edit-Modal .modal-content {
        width: 100% !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 12px !important;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
    }

    #edit-Modal .modal-header {
        min-height: 64px;
        padding: 18px 22px !important;
        border: 0 !important;
        background: var(--sf-primary, #17675f) !important;
        color: #fff;
    }

    #edit-Modal .modal-title {
        margin: 0;
        color: #fff;
        font-size: 18px !important;
        font-weight: 700;
    }

    #edit-Modal .close {
        color: #fff !important;
        opacity: .9;
        text-shadow: none;
    }

    #edit-Modal .modal-body { padding: 26px !important; }
    #edit-Modal .modal-footer { padding: 16px 26px; border-top: 1px solid #e2e8f0; }

    #edit-Modal #updateBtn {
        min-width: 120px;
        min-height: 42px;
        border: 0 !important;
        border-radius: 8px !important;
        background: var(--sf-primary, #17675f) !important;
        color: #fff !important;
        font-size: 13px;
        font-weight: 700;
    }

    #edit-Modal .btn-default {
        min-height: 40px;
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #64748b;
    }

    @media (max-width: 1199.98px) {
        .student-fine-page { padding: 32px 24px; }
        #dataDiv > .container-fluid > .row.align-items-center { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        #dataDiv #submitFineForm > .row { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
    }

    @media (max-width: 767.98px) {
        .student-fine-page { padding: 24px 16px; }
        .student-fine-header { margin-bottom: 22px; }
        .student-fine-header h1 { font-size: 21px; }
        .student-fine-search-card { padding: 20px; }
        .student-fine-search-row { flex-direction: column; align-items: stretch; gap: 16px; }
        .student-fine-search-button { width: 100%; }
        #dataDiv > .container-fluid > .row.align-items-center { grid-template-columns: 1fr; padding: 22px; }
        #dataDiv #submitFineForm { padding: 58px 22px 22px; }
        #dataDiv #submitFineForm::before { left: 22px; top: 22px; }
        #dataDiv #submitFineForm > .row { grid-template-columns: 1fr; }
        #dataDiv #submitFineForm .form-group.col-sm-12 { justify-content: stretch !important; }
        #dataDiv #finesubmit { width: 100%; }
        #edit-Modal .modal-dialog { width: calc(100vw - 24px); }
    }
</style>
<div id="success-flag" class="success-flag"></div>
<div class="student-fine-page">
    <div class="student-fine-shell">
        <div class="student-fine-header">
            <h1>Student Fine</h1>
            <p>Search and manage fines for enrolled students</p>
        </div>

        <section class="student-fine-search-card">
            <form id="searchStudentForm">
                @csrf
                <div class="student-fine-search-row">
                    <div class="student-fine-field">
                        <label for="categoryName">Enrollment No#</label>
                        <input placeholder="Enter student enrollment number" class="form-control" onkeypress="handleEnterPress()" type="text" name="studentEnrollment" id="categoryName">
                    </div>
                    <button onclick='searchStudent()' type="button" class="btn btn-info student-fine-search-button"><i class="fa fa-search"></i> Search</button>
                </div>
            </form>
        </section>

        <div id="dataDiv"></div>
    </div>
</div>

<!-- Modal for Remarks -->
<div id="remarksModal" class="modal">
    <div class="modal-content">
        <h3>Enter Remarks</h3>
        <textarea id="remarksInput" placeholder="Enter your remarks here"></textarea>
        <button onclick="cancelChallanWithRemarks()">Cancel Challan</button>
        <button onclick="closeRemarksModal()">Close</button>
    </div>
</div>

<div id="edit-Modal" class="modal fade" data-backdrop="false" style="background-color: rgba(255, 255, 255, 0.2);" role="dialog">
    <div class="modal-dialog" style="top: 40%;transform: translateY(-40%)">
        <!-- Modal content-->
        <div class="modal-content" style="width:500px;">
            <div class="modal-header" style="background-color:#ededed;">
                <h1 class="modal-title" style="font-size: 25px;">Edit Fine</h1>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-form" method="POST" action="" data-toggle="validator">
                    @csrf
                    <div class="row" style="margin-top: 12px;">
                        <input hidden id="challan_id" name="challan_id" value="">
                        <div class="col-md-4 text-left">
                            <label for="edit_sess_id" class="col-form-label">Session:</label>
                        </div>
                        <div class="form-group col-md-8">
                            <select class="form-control" id="edit_sess_id" name="edit_sess_id">

                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 12px;">
                        <div class="col-md-4 text-left">
                            <label for="edit_fineAmount" class="col-form-label">Fine Amount:</label>
                        </div>
                        <div class="form-group col-md-8">
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="edit_fineAmount" id="edit_fineAmount" class="form-control" placeholder="Enter Amount" style="width: 100%;">
                        </div>
                    </div>

                    <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                        <button type="button" id="updateBtn" class="btn btn-primary">Update</button>
                    </div>
                </form>
                <div class="clear clear20"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissible" id="error-message-1" style="display:none">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
@stop
@section('javascript')
<script>
      const userGroupId = Number(@json(auth()->user()->group_id));
      const allowedGroupIds = [644, 324];
    document.getElementById('updateBtn').addEventListener('click', function() {
        var form = document.getElementById("edit-form");
        var formData = new FormData(form);
        fetch('update-student-fine-challan', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#challan_id').val('');
                $('#edit_sess_id').val('');
                $('#edit_fineAmount').val('');

                $("#error_message").text('').hide();
                $('#edit-Modal').modal('hide');
                $('#success-flag').text(data.message);
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                searchStudent();
            } else {
                // console.log(data);
                var message = "";
                if ('message' in data) {
                    message = data.message;
                } else if ('errors' in data) {
                    var errors = data.errors;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(5000);
            }
        })
        .catch(error => {
            console.error('There was an error!', error);
        });
    });
function handleEnterPress() {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        searchStudent();
    }
}

function searchStudent(){
        let form = document.getElementById('searchStudentForm');
        let data = new FormData(form);
        let successMsg=document.getElementById('successMessage');

        fetch(`/get-student-fine-rec`, {
            method: 'POST', // Using POST method
            body: data
        })
        .then(response => response.json())
        .then(response=>{
            if (response.success) {
                makeDataHtml(response.getFinesData,response.fineCategories,response.student_challan, response.sessions);
            }

        })
}



function makeDataHtml(data,fineCategories,student_challan, sessions) {

    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content
    let sr = 1;
    let htmlResult = generateFeeChallanHTML(student_challan);

    //  <p class="lead"><strong>Department:</strong> ${data.emp_dept_name}</p>
    if (data) {
        let sessionDiv = '';
        if (allowedGroupIds.includes(userGroupId)) {
            sessionDiv = `
                <div class="form-group col-md-3">
                    <label for="session">Select Session:</label>
                    <select name="session" id="session_id" class="form-control">
                        <option value="" selected disabled>--- Select Session ---</option>
                        ${sessions.map((session) => {
                            return `<option value="${session.sess_id}">${session.sess_name}</option>`;
                        }).join('')}
                    </select>
                    <h6 data-error-for="session" class="validation-error pt-4"></h6>
                </div>
            `;
        }
        // console.log(sessionDiv);
        let lettersHtml = `
    <div class="container-fluid">
        <div class="row align-items-center">
                <!-- SPI Name and Student Image -->
        <div class="col-md-3 col-sm-6 col-12 text-center mb-3">
            <h4 class="text-uppercase font-weight-bold text-primary mt-4">${data.spi_name}</h4>
            <img src="https://cms.bnu.edu.pk/students_pics/${data.spi_enrollment_no.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:50px; height:50px;">
        </div>
        <!-- Enrollment Number -->
        <div class="col-md-3 col-sm-3 col-12 text-center mb-3">
            <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Enrollment Number:</strong> <span class="text-uppercase">${data.username}</span>
            </p>
        </div>
        <div class="col-md-3 col-sm-3 col-12 text-center mb-3">
            <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Status:</strong> <span class="text-uppercase"><span class="badge badge-info">${data.sai_student_status}</span></span>
            </p>
        </div>

        <!-- Email -->
        <div class="col-md-3 col-sm-3 col-12 text-center mb-3">
            <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Email:</strong> <span class="d-inline">${data.email}</span>
            </p>
        </div>

    



</div>


<div class="col-md-12 text-left mt-3">
   <form id="submitFineForm">
        @csrf
        <div class="row">
            <input type="hidden"  name="enrollment_no" id="enrollment_no" value="${data.username}" >
            <input type="hidden"  name="active_sess_id" id="active_sess_id" value="${data.spi_active_sess_id}" >
             <!-- First Dropdown -->

             ${sessionDiv}
            <!-- First Dropdown -->
            <div class="form-group col-md-3">
                <label for="fineCategoryOption">Select Category:</label>
                <select name="fineCategoryOption" id="fineCategoryOption" onchange="getOffenceDetails(this.value)" class="form-control">
                    <option value="" selected disabled>--- Select Category ---</option>
                    ${fineCategories.map((category, index) => {
                        return `<option value="${category.id}">${category.fc_category_name}</option>`;
                    }).join('')}
                </select>
                <h6 data-error-for="fineCategoryOption" class="validation-error pt-4"></h6>
            </div>

            <!-- Second Dropdown -->
            <div class="form-group col-md-3">
                <label for="violationType">Select Offence Type:</label>
                <select name="violationType" id="violationType" class="form-control" onchange="getSelectedValue()"></select>
                <h6 data-error-for="violationType" class="validation-error pt-4"></h6>
            </div>

            <!-- Fine Amount Input -->

                <div class="form-group col-md-3">
                    <label for="fineAmount">Fine Amount:</label>
                    <input type="number" readonly name="fineAmount" id="fineAmount" class="form-control">
                    <h6 data-error-for="fineAmount" class="validation-error pt-4"></h6>
                </div>


            <!-- Success Message and Submit Button -->
               <div class="form-group col-sm-3 text-right" style="">
                    <button id="finesubmit" type="button" onclick="submitFineFormBtn()" class="btn btn-primary">Submit</button>
                </div>


        </div>
    </form>
</div>
<div id="successMessage" class="text-success mt-2"></div>




        </div>

        ${htmlResult}
    </div>
`;
    let editoptionsHtml = '';
    sessions.forEach(session => {
        editoptionsHtml += `<option value="${session.sess_id}">${session.sess_name}</option>`;
    });

    // Set the options in the select element
    $('#edit_sess_id').html(editoptionsHtml);

        showWarningLetter.innerHTML = lettersHtml;
    } else {
        // Optionally display a message when there is no data
        showWarningLetter.innerHTML = '<p>No data available.</p>';
    }
}

function getSelectedValue() {
    // Get the dropdown elements
    var violationType = document.getElementById("violationType");
    var fineCategoryOption = document.getElementById("fineCategoryOption");
    var fineAmount = document.getElementById("fineAmount");

    // Get the selected values
    var selectedViolationType = violationType.value;
    var selectedFineCategoryOption = fineCategoryOption.value;

    // Build the URL with parameters
    var url = `/check-violation-fines?violationType=${selectedViolationType}&fineCategoryOption=${selectedFineCategoryOption}`;

    fetch(url)
        .then(response => response.json())
        .then(response => {
            if (response.fcd_fine_amount !== undefined) {
                // Convert the fine amount to an integer
                var fineAmountValue = Number(response.fcd_fine_amount); // or parseInt(response.fcd_fine_amount, 10)
                fineAmount.value = fineAmountValue;
                // Check if the fine amount is 0
                if (fineAmountValue === 0) {
                    fineAmount.removeAttribute('readonly');

                } else {
                    fineAmount.setAttribute('readonly', 'readonly');

                }
            } else {

                fineAmount.value = '';
                fineAmount.setAttribute('readonly', 'readonly');
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}

function getOffenceDetails(selectedValue) {
        var violationType = document.getElementById("violationType");
        var fineAmount = document.getElementById("fineAmount");
        violationType.value='';
        fineAmount.value='';


        // Adjust the URL by appending the selectedValue to it
        let url = `/check-violation-types?fineCategoryOption=${selectedValue}`;

        fetch(url)
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    violationType.innerHTML = '<option value="" selected disabled>--- Select Violation Type ---</option>';
                     response.data.forEach(type => {
                    let option = document.createElement("option");
                    option.value = type.id;
                    option.textContent = type.fcd_violation_type;
                    violationType.appendChild(option);
                });
                }

            });
    }

// (() => {
//     fetch(`/get-fine-data`, {
//         method: 'GET', // Using GET method
//     })
//     .then(response => response.json())
//     .then(data => {
//         makeDataHtml(data);
//     })
//     .catch((error) => {
//         console.error('Error:', error);
//         // Handle fetch error here
//     });
// })();

function submitFineFormBtn(){
        $('#finesubmit').prop('disabled', true);
        let form = document.getElementById('submitFineForm');
        let dataDiv = document.getElementById('dataDiv');
        let data = new FormData(form);
        let successMsg=document.getElementById('successMessage');
        successMsg.innerHTML = '';

        fetch(`/generate-student-fine-challan`, {
            method: 'POST', // Using POST method
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.reset();
                console.log('Success:', data.message);
                $('#finesubmit').prop('disabled', false);
                // successMsg.innerHTML =data.message;
                const alertHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> <strong>Success!</strong> ${data.message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
            successMsg.innerHTML = alertHTML;
                // makeDataHtml(data.challanRec);
                setTimeout(function(){
                    searchStudent()
                },3000)
                // Handle success response here
            } else {

                //   displayValidationErrors(data.errors);
                $('#finesubmit').prop('disabled', false);

                  // 1. Show normal error message
    if (data.message) {
        const alertHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle"></i>
                <strong>Error!</strong> ${data.message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        successMsg.innerHTML = alertHTML;
    }

    // 2. Show validation errors
    if (data.errors) {
        displayValidationErrors(data.errors);
    }
    
                console.error('Error:', data.errors);
                // Handle error here
            }
        })
        .catch((error) => {
            // displayValidationErrors(error);
             $('#finesubmit').prop('disabled', false);
            console.error('Error:', error);
            // Handle fetch error here
        });
}
function generateFeeChallanHTML(student_challan) {
    // console.log(student_challan);
            if (student_challan && student_challan.length > 0) {


                    // Group challans by category
                    const groupedChallans = student_challan.reduce((acc, challan) => {
                        if (!acc[challan.category_name]) {
                            acc[challan.category_name] = [];
                        }
                        acc[challan.category_name].push(challan);
                        return acc;
                    }, {});

                    // Generate HTML for each category
                    // const categoriesHTML = Object.entries(groupedChallans).map(([category, challans]) => {
                    //     const tableRows = challans.map(challan => `
                    //         <tr>
                    //             <td>${challan.sess_name}</td>
                    //             <td>${challan.category_name}</td>
                    //             <td>${challan.violation_type}</td>
                    //             <td>${challan.id ? challan.id : 'N/A'}</td>
                    //             <td>
                    //                 <span class="badge ${challan.sc_challan_status === 'UNPAID' ? 'badge-danger' : 'badge-success'}">
                    //                     ${challan.sc_challan_status === 'UNPAID' ? 'Unpaid' : 'Paid'}
                    //                 </span>
                    //             </td>
                    //             <td>${challan.sc_amount}</td>
                    //             <td>${new Date(challan.created_at).toLocaleString()}</td>
                    //         </tr>
                    //     `).join('');

                    //     return `
                    //         <div class="accordion-item">
                    //             <h2 class="accordion-header" id="heading${category.replace(/\s+/g, '')}">
                    //                 <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${category.replace(/\s+/g, '')}" aria-expanded="false" aria-controls="collapse${category.replace(/\s+/g, '')}">
                    //                     ${category}
                    //                 </button>
                    //             </h2>
                    //             <div id="collapse${category.replace(/\s+/g, '')}" class="accordion-collapse collapse" aria-labelledby="heading${category.replace(/\s+/g, '')}" data-bs-parent="#accordionExample">
                    //                 <div class="accordion-body">
                    //                     <div class='table-responsive mt-3'>
                    //                         <table class='table'>
                    //                             <thead>
                    //                                 <tr>
                    //                                     <th>Session</th>
                    //                                     <th>Category</th>
                    //                                     <th>Violation Type</th>
                    //                                     <th>Challan No</th>
                    //                                     <th>Challan Status</th>
                    //                                     <th>Amount</th>
                    //                                     <th>Challan Date</th>
                    //                                 </tr>
                    //                             </thead>
                    //                             <tbody>
                    //                                 ${tableRows}
                    //                             </tbody>
                    //                         </table>
                    //                     </div>
                    //                 </div>
                    //             </div>
                    //         </div>
                    //     `;


                    // }).join('');

                            const userId = @json(auth()->user()->id);
                            const groupId = @json(auth()->user()->group_id);

                            const tableRows = student_challan.map(challan => `
                                <tr>
                                    <td>${challan.sess_name}</td>
                                    <td>${challan.sc_enrollment_no}</td>
                                    <td>${challan.sc_app_id}</td>
                                    <td>${challan.category_name}</td>
                                    <td>${challan.violation_type}</td>
                                    <td>${challan.id?challan.id:'N/A'}</td>
                                    <td>
                                        <span class="badge ${
                                                challan.sc_challan_status === 'UNPAID' ? 'badge-danger' :
                                                (challan.sc_challan_status === 'PAID' ? 'badge-success' : 'badge-secondary')
                                            }">
                                                ${
                                                    challan.sc_challan_status === 'UNPAID' ? 'Unpaid' :
                                                    (challan.sc_challan_status === 'PAID' ? 'Paid' : 'Cancelled ')
                                                }
                                        </span>
                                    </td>
                                    <td>${challan.sc_amount}</td>
                                    <td>${new Date(challan.sc_due_date).toLocaleString()}</td>
                                    <td>
                                        ${
                                            challan.sc_challan_status == 'UNPAID' && (groupId == '644' || userId == '713' || userId == '1492' || userId == '23804')//713 saqib, 1492 asifnadeem
                                            ? `<button id="${challan.id}" class="btn btn-danger" onclick="cancelChallan(${challan.id})">
                                                Cancel
                                            </button>`
                                            : (challan.sc_challan_status == 'CANCELLED' && (groupId == '644' || userId == '713' || userId == '1492' || userId == '23804'))//713 saqib, 1492 asifnadeem
                                            ? `<button id="${challan.id}" class="btn btn-success" onclick="activeChallan(${challan.id})">
                                                Activate
                                            </button>`
                                            : ''
                                        }
                                    </td>
                                    <td>
                                        ${
                                            (groupId == '644' || userId == '23804')//esha.abid 23804 tIdDi
                                            ? `<button id="${challan.id}" class="btn btn-primary" onclick="editChallan(${challan.id}, ${challan.sess_id}, ${challan.category_id}, ${challan.violation_id}, ${challan.sc_amount})">
                                                Edit
                                            </button>`
                                            : ''
                                        }
                                    </td>
                                </tr>
                            `).join('');

                            return `
                                <div class='table-responsive mt-3'>
                                    <table class='table'>
                                        <thead>
                                            <tr>
                                                <th>Session</th>
                                                <th>Enrollment No</th>
                                                <th>Apply Id</th>
                                                <th>Category</th>
                                                <th>Violation Type</th>
                                                <th>Token No</th>
                                                <th>Challan Status</th>
                                                <th>Amount</th>
                                                <th>Challan Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${tableRows}
                                        </tbody>
                                    </table>
                                </div>
                            `;
    } else {
        return '';
    }
}
function editChallan(challan_id, sess_id, category_id, violation_id, sc_amount){
    $('#challan_id').val(challan_id);
    $('#edit_sess_id').val(sess_id);
    $('#edit_fineAmount').val(sc_amount);
    $('#edit-Modal').modal('show');

}
function displayValidationErrors(errors) {
        Object.entries(errors).forEach(([key, value]) => {
            var trimmedKey = key.trim();
            var errorElement = document.querySelector("[data-error-for='" + trimmedKey + "']");

            if (errorElement) {
                // Check if the value is an array, and get the first element
                var errorMessage = Array.isArray(value) ? value[0] : value;

                // Create a span element for the error message and icon
                var errorSpan = document.createElement('span');
                errorSpan.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errorMessage;

                // Clear the existing content and add the new span
                errorElement.innerHTML = '';
                errorElement.appendChild(errorSpan);

                errorElement.classList.add('text-danger');

                setTimeout(function () {
                    errorElement.innerHTML = ''; // Clear both icon and text
                    errorElement.classList.remove('text-danger');
                }, 5000);
            }
        });
    }
    function cancelChallan(challanId) {
    // Show the modal to collect remarks
    $('#remarksModal').show();
    $('#remarksInput').val('');  // Clear any existing input in the remarks textarea
    $('#remarksInput').focus();  // Focus the input for user convenience
    // Store the challan ID globally or within the modal
    window.currentChallanId = challanId; // Store the challan ID for use later
}

function cancelChallanWithRemarks() {
    var challanId = window.currentChallanId; // Get the stored challan ID
    var remarks = $('#remarksInput').val(); // Get the inputted remarks

    if (remarks.trim() === '') {
        alert('Please enter some remarks.');
        return; // Don't proceed if no remarks are entered
    }

    var confirmation = window.confirm("Are you sure you want to cancel this challan?");

    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/student-cancel-challan",
            data: {
                _token: '{{ csrf_token() }}',
                challanId: challanId,
                remarks: remarks // Pass remarks to the server
            },
            success: function(response) {
                // Refresh the table or take appropriate action
                searchStudent();
                $('#successMsg').html(response.message);
                closeRemarksModal(); // Close the modal after the cancellation
            },
            error: function(error) {
                console.error("Error:", error);
                closeRemarksModal(); // Close the modal in case of error
            }
        });
    } else {
        closeRemarksModal(); // Close the modal if the action is cancelled
    }
}

function closeRemarksModal() {
    $('#remarksModal').hide(); // Hide the modal when done
}
    function activeChallan(challanId){
        var confirmation = window.confirm("Are you sure you want to activate challan?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/student-activate-challan",
                data: {
                    _token: '{{ csrf_token() }}',
                    challanId: challanId
                },
                success: function(response) {
                     // $('.select-data').prop('checked', false);
                    // $('#success-flag').text('Deleted Successfully');
                    // $('#success-flag').addClass('show');
                    searchStudent();
                    $('#successMsg').html(response.message);
                },
                error: function(error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }

</script>
@stop
