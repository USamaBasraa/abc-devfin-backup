@extends('layouts.app')
@section('title') BNU CMS @stop
{{-- @dd("123"); --}}
<style>
/* Figma-aligned Student Document Verification presentation layer.
   Functional markup, Blade conditions, IDs, names and JavaScript remain unchanged. */

#notificationMessage {
    display: none;
}

#loadingSpinner {
    display: none;
}

#loadingSpinner.active {
    display: flex;
    justify-content: center;
    align-items: center;
}

.dropdown-menu {
    min-width: 10rem !important;
}

.document-verification-page {
    min-height: calc(100vh - 20px);
    /* padding: 30px 26px 44px !important; */
    margin-top: 0 !important;
    background: #f2f6f9;
    font-family: 'Montserrat', sans-serif;
    color: #252525;
}

.document-verification-page > .card {
    margin: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

/* Page heading + applicant profile */
.document-verification-page > .card > .card-header:first-child {
    padding: 0 0 22px 4px;
    border: 0;
    background: transparent;
}

.document-verification-page > .card > .card-header:first-child .row {
    margin: 0;
    align-items: center !important;
}

.document-verification-page > .card > .card-header:first-child .row > [class*='col-'] {
    padding-left: 0;
    padding-right: 0;
}

.document-verification-page > .card > .card-header:first-child h3 {
    margin: 0 0 5px;
    color: #252525;
    font-size: 20px;
    line-height: 1.35;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.document-verification-page > .card > .card-header:first-child h3 + span {
    display: block;
    max-width: 760px;
    color: #64748b;
    font-size: 14px;
    line-height: 1.55;
    font-weight: 400;
}

.document-verification-page > .card > .card-header:first-child .col-md-6.text-center {
    display: flex;
    flex-direction: row-reverse;
    align-items: center;
    justify-content: flex-start;
    gap: 12px;
    text-align: right !important;
    color: #0f172a;
    font-size: 15px;
    font-weight: 600;
}

.document-verification-page > .card > .card-header:first-child .col-md-6.text-center img {
    width: 64px !important;
    height: 64px !important;
    flex: 0 0 64px;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #dbe4e8, 0 4px 12px rgba(15,23,42,.08);
}

/* Main content stays structurally identical; visual cards mirror Figma */
.document-verification-page > .card > .container.mt-5 {
    width: 100%;
    max-width: 100%;
    margin-top: 0 !important;
    padding: 0;
}

/* Toolbar card */
.document-verification-page > .card > .container.mt-5 > .row.container {
    width: 100%;
    max-width: 100%;
    min-height: 76px;
    margin: 0 0 20px !important;
    padding: 14px 18px;
    border: 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(15,23,42,.04);
    gap: 12px;
}

.document-verification-page .btn {
    min-height: 38px;
    padding: 8px 14px;
    border-radius: 8px !important;
    font-size: 12px;
    line-height: 1.2;
    font-weight: 600;
    box-shadow: none !important;
    transition: background-color .2s ease, border-color .2s ease, color .2s ease;
}

.document-verification-page #notifyButton {
    border: 1px solid #17675f !important;
    background: #fff !important;
    color: #17675f !important;
}

.document-verification-page #notifyButton:hover,
.document-verification-page #notifyButton:focus {
    background: #eef7f5 !important;
    border-color: #17675f !important;
    color: #17675f !important;
}

.document-verification-page .fileDownload,
.document-verification-page .downloadZip.btn-success,
.document-verification-page .btn-primary {
    border-color: #17675f !important;
    background: #17675f !important;
    color: #fff !important;
}

.document-verification-page .fileDownload:hover,
.document-verification-page .downloadZip.btn-success:hover,
.document-verification-page .btn-primary:hover,
.document-verification-page .btn-primary:focus {
    border-color: #12564f !important;
    background: #12564f !important;
    color: #fff !important;
}

.document-verification-page .btn-outline-primary,
.document-verification-page .btn-outline-info {
    border-color: #17675f !important;
    background: #fff !important;
    color: #17675f !important;
}

.document-verification-page .btn-outline-primary:hover,
.document-verification-page .btn-outline-primary:focus,
.document-verification-page .btn-outline-info:hover,
.document-verification-page .btn-outline-info:focus {
    border-color: #17675f !important;
    background: #eef7f5 !important;
    color: #17675f !important;
}

.document-verification-page .btn-outline-success {
    border-color: #17675f !important;
    background: #fff !important;
    color: #17675f !important;
}

.document-verification-page .btn-outline-success:hover,
.document-verification-page .btn-outline-success:focus {
    background: #17675f !important;
    color: #fff !important;
}

.document-verification-page .btn-outline-danger {
    border-color: #dc6b78 !important;
    background: #fff !important;
    color: #c94d5e !important;
}

.document-verification-page .btn-outline-danger:hover,
.document-verification-page .btn-outline-danger:focus,
.document-verification-page .btn-danger {
    border-color: #c94d5e !important;
    background: #c94d5e !important;
    color: #fff !important;
}

.document-verification-page .btn-secondary {
    border-color: #e2e8f0 !important;
    background: #f8fafc !important;
    color: #475569 !important;
}

/* Documents table card */
.document-verification-page > .card > .container.mt-5 > .table-responsive {
    width: 100%;
    margin: 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(15,23,42,.04);
    overflow-x: auto;
    overflow-y: visible;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table {
    width: 100%;
    min-width: 980px;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    color: #0f172a;
    font-size: 13px;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table thead {
    background: #17675f;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table thead th {
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #17675f !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 12px;
    line-height: 1.35;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table thead th:first-child {
    border-top-left-radius: 12px;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table thead th:last-child {
    border-top-right-radius: 12px;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody td {
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #fff;
    color: #0f172a;
    font-size: 13px;
    line-height: 1.45;
    vertical-align: middle;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody tr:nth-child(even) > td {
    background: #f8fafc;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody tr:last-child > td {
    border-bottom: 0 !important;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody td:last-child {
    min-width: 390px;
    white-space: nowrap;
}

.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody td:last-child > .btn,
.document-verification-page > .card > .container.mt-5 > .table-responsive > .table tbody td:last-child > a.btn {
    margin: 2px 4px 2px 0;
}

/* Status pills */
.document-verification-page .badge {
    min-width: 104px;
    padding: 7px 10px;
    border-radius: 999px !important;
    font-size: 11px;
    line-height: 1.1;
    font-weight: 600;
    letter-spacing: .01em;
    box-shadow: none;
}

.document-verification-page .badge-success {
    background: #e7f7ef !important;
    color: #14845f !important;
}

.document-verification-page .badge-info {
    background: #e7f3f5 !important;
    color: #17675f !important;
}

.document-verification-page .badge-warning {
    background: #fff4d8 !important;
    color: #a56b00 !important;
}

.document-verification-page .badge-secondary {
    background: #eef2f6 !important;
    color: #64748b !important;
}

/* Modals */
.document-verification-page .modal {
    z-index: 99999;
}

.document-verification-page .modal-dialog {
    margin-top: 6vh;
}

.document-verification-page .modal-content {
    border: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15,23,42,.18);
}

.document-verification-page .modal-header {
    min-height: 58px;
    padding: 16px 20px;
    border: 0;
    background: #17675f;
    color: #fff;
}

.document-verification-page .modal-header .modal-title {
    margin: 0;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
}

.document-verification-page .modal-header .close,
.document-verification-page .modal-header .close.text-white {
    margin: -4px -4px -4px auto;
    color: #fff !important;
    opacity: 1;
    text-shadow: none;
}

.document-verification-page .modal-body {
    padding: 22px;
    background: #fff;
}

.document-verification-page .modal-footer {
    padding: 14px 20px 18px;
    border-top: 1px solid #e2e8f0;
    background: #fff;
    gap: 8px;
}

.document-verification-page .modal .form-control {
    min-height: 44px;
    padding: 9px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    color: #0f172a;
    font-size: 14px !important;
    box-shadow: none;
}

.document-verification-page .modal .form-control:focus {
    border-color: #17675f;
    box-shadow: 0 0 0 3px rgba(23,103,95,.10);
}

.document-verification-page .modal label {
    color: #475569 !important;
    font-size: 13px;
    font-weight: 600 !important;
}

.document-verification-page .dropdown-menu {
    padding: 6px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(15,23,42,.10);
}

.document-verification-page .dropdown-item {
    padding: 8px 10px;
    border-radius: 6px;
    color: #334155;
    font-size: 13px;
}

.document-verification-page .dropdown-item:hover,
.document-verification-page .dropdown-item:focus {
    background: #eef7f5;
    color: #17675f;
}

/* Dynamically-rendered previous uploads table */
.document-verification-page #previousUploadsContent .table-responsive {
    border-radius: 10px;
    overflow-x: auto;
}

.document-verification-page #previousUploadsContent table {
    margin: 0;
    font-size: 13px;
}

.document-verification-page #previousUploadsContent table thead th {
    padding: 11px 12px;
    border: 0;
    background: #17675f;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.document-verification-page #previousUploadsContent table tbody td {
    padding: 12px;
    border-color: #e2e8f0;
    vertical-align: middle;
}

.document-verification-page #previousUploadsContent table tbody tr:nth-child(even) td {
    background: #f8fafc;
}

@media (max-width: 991.98px) {
    .document-verification-page {
        padding: 24px 16px 34px !important;
    }

    .document-verification-page > .card > .card-header:first-child .col-md-6.text-center {
        margin-top: 16px;
        justify-content: flex-end;
    }

    .document-verification-page > .card > .container.mt-5 > .row.container {
        align-items: flex-start !important;
    }
}

@media (max-width: 767.98px) {
    .document-verification-page {
        padding: 20px 12px 28px !important;
    }

    .document-verification-page > .card > .card-header:first-child .row {
        display: block;
    }

    .document-verification-page > .card > .card-header:first-child .col-md-6.text-center {
        justify-content: flex-start;
        text-align: left !important;
    }

    .document-verification-page > .card > .container.mt-5 > .row.container {
        flex-direction: column;
        align-items: stretch !important;
        padding: 14px;
    }

    .document-verification-page > .card > .container.mt-5 > .row.container > .d-flex {
        width: 100%;
        flex-wrap: wrap;
        gap: 8px;
    }

    .document-verification-page > .card > .container.mt-5 > .row.container .btn,
    .document-verification-page > .card > .container.mt-5 > .row.container .downloadZip {
        flex: 1 1 180px;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
}
.document-verification-page
> .card
> .container.mt-5
> .table-responsive
> .table {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed !important;
    margin: 0;
}

/* Proper full-width column distribution */
.document-verification-page .table thead th:nth-child(1),
.document-verification-page .table tbody td:nth-child(1) {
    width: 5%;
}

.document-verification-page .table thead th:nth-child(2),
.document-verification-page .table tbody td:nth-child(2) {
    width: 25%;
}

.document-verification-page .table thead th:nth-child(3),
.document-verification-page .table tbody td:nth-child(3) {
    width: 20%;
}

.document-verification-page .table thead th:nth-child(4),
.document-verification-page .table tbody td:nth-child(4) {
    width: 20%;
}

.document-verification-page .table thead th:nth-child(5),
.document-verification-page .table tbody td:nth-child(5) {
    width: 15%;
}

/* Purana 390px restriction remove */
.document-verification-page
> .card
> .container.mt-5
> .table-responsive
> .table tbody td:last-child {
    min-width: 0 !important;
    white-space: normal;
}
.document-verification-page
> .card
> .container.mt-5
> .row.container
> .d-flex {
    margin-left: auto !important;
    justify-content: flex-end !important;
}
















</style>
<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
$baseUrl = $protocol . $_SERVER['HTTP_HOST'];
// $baseUrl = 'http://devstudent.bnu.edu.pk/';
?>

@section('content')
    <div class="container-fluid mt-4 document-verification-page" style="padding: 0 !important; margin:0 !important;">
        <!-- Default table border table start-->
        <div class="card">
            <div class="card-header">
                <div class="row" style="
    width: 100%;
">
                      <div class="col-md-6">
                          
                          <img class="img-fluid rounded-circle" style="width: 125px; height: 125px;" src="{{ $baseUrl }}/images/{{ $record->api_imagename }}" alt="profile-img">
                          {{$admissionDocument[0]->applicant_name ?? 'N/A'}}
                    </div>
                    <div class="col-md-6">
                        <h3>Student's document verification </h3>
                        <span>Student's document verification for admissions. This table displays the verification status of various admission documents. Each document is listed with its corresponding Document Name, Status, and available action.</span>
                    </div>
                  

                </div>
            </div>

            <div class="container mt-5">
                


                <div class="row container d-flex justify-content-between align-items-center">
                    <!-- Left Side (Notify Button) -->
                    <button class="btn btn-outline-info w-auto px-3" id="notifyButton" data-toggle="modal" data-target="#notificationModal">
                        <i class="fa fa-bell"></i> Notify
                    </button>
                
                    <!-- Right Side (Download Buttons) -->

                    <?php
                    $getApi_id = isset($_GET['api_id']) ? $_GET['api_id'] : 'default';
                    $getProg_id = isset($_GET['prog_id']) ? $_GET['prog_id'] : 'default';
                    $getSess_id = isset($_GET['sess_id']) ? $_GET['sess_id'] : 'default';
                    ?>
                    
                    <div class="d-flex" style="
    position: relative;
    right: -180px;
">
                        <button class="btn btn-outline-primary btn-sm fileDownload ml-2 mr-2 w-auto px-3"
                            type="button"
                            data-api_id="<?= htmlspecialchars($getApi_id) ?>"
                            data-prog_id="<?= htmlspecialchars($getProg_id) ?>"
                            data-sess_id="<?= htmlspecialchars($getSess_id) ?>">
                            <i class="fa fa-folder" aria-hidden="true"></i> Download All Documents
                        </button>
                    
                        <a id="downloadZipBtn" class="btn btn-outline-primary invisible downloadZip btn-sm"  
                           style="border-radius: 5px; font-weight: bold; text-transform: uppercase; padding: 8px 16px;">
                           <i class="fa fa-download"></i> Download Now
                        </a>
                    </div>
                    
                </div>
                
                


 <!-- Modal structure -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalBody">
        <div id="notificationMessage" class="alert alert-success" role="alert">
        </div>

        <div id="remarkDetails" class="mt-3">
        <form id="notificationForm">
    @csrf
    <input type="hidden" name="sai_enrollment_no" id="sai_enrollment_no" value="{{ $studentInfo->sai_enrollment_no ?? '' }}">

    <div class="form-group">
        <label for="remarkInput" class="font-weight-bold text-muted">Remarks</label>
        <input type="text" id="remarkInput" name="remarkInput"
               value="{{ $studentInfo->sai_pf_remarks ?? '' }}"
               class="form-control form-control-lg" placeholder="Enter remarks" required>
    </div>
</form>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
        <button type="button" id="sendNotificationBtn" class="btn btn-primary btn-sm" onclick="sendNotification()">
          <i class="fa fa-paper-plane"></i> Send Notification
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add a spinner icon inside the button when sending -->
<div id="spinner" class="spinner-border text-light" role="status" style="display:none;">
  <span class="sr-only">Loading...</span>
</div>



                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Document Name</th>
                                <th>Verification Status</th>
                                <th>Result Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        <?php echo count($admissionDocument)  == 0 ? '<tr><td>No Document Submitted Yet!</td></tr>' : ''; ?>
                        <?php $sr=0; ?>
                        @foreach($admissionDocument as $document)
                        <?php $sr++; ?>

                                <tr>
                                    <td scope="row">{{$sr}}</td>


                                    <td scope="row">
                                        @if ($document->ad_type=='O_Level')
                                            O-Level
                                            @elseif ($document->ad_type=='Cnic')
                                            Applicant CNIC / Passport
                                            @elseif ($document->ad_type=='GaurdianCnic')
                                            Gaurdian CNIC/Passport
                                            @elseif ($document->ad_type=='A_Level')
                                            A-Level
                                            @elseif ($document->ad_type=='4_Years')
                                            {{-- 4 Years Graduation --}}
                                            BA/Bsc transcript and degree
                                          @elseif ($document->ad_type=='2_Years')
                                          {{-- 2 Years Graduation --}}
                                          BA/Bsc transcript and degree
                                          @elseif ($document->ad_type=='papers')
                                             Admission Test / Interview / Evaluation
                                          @else
                                        {{$document->ad_type}}

                                        @endif

                                    </td>

                                    {{-- <td scope="row">
                                        {{$document->ad_type}}</td> --}}
                                    <td>
                                    <span class="badge badge-pill <?php echo $document->ad_verification_status == 1 ? 'badge-success' : ($document->ad_verification_status == 2 ? 'badge-info' : 'badge-warning'); ?> badge-lg" style="min-width: 100px; text-align: center; display: inline-block;">
                                            <?php
                                                if ($document->ad_verification_status == 1) {
                                                    echo 'Verified';
                                                } elseif ($document->ad_verification_status == 2) {
                                                    echo 'To be Verified';
                                                } else {
                                                    echo 'Pending';
                                                }
                                            ?>
                                        </span>



                                    </td>
                                    <td>
                                        @if ($document->result_status == 'Declared')
                                        <span class="badge badge-success badge-lg">{{$document->result_status}}</span>
                                    @else
                                        <span class="badge badge-secondary badge-lg">{{$document->result_status}}</span>
                                    @endif
                                    </td>
                                    <td scope="row">
                                     


                                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                                            <iframe class="embed-responsive-item" id="documentIframe" style="position: absolute; width: 100%; height: 100%; border: none;"></iframe>
                                                            <div id="loadingIndicator" style="position: absolute; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); display: none; display: flex; justify-content: center; align-items: center;">

                                                                <div style="font-size: 24px; text-align: center;">
                                                                    <div class="spinner-border text-primary" role="status">
                                                                        <span class="visually-hidden">Loading...</span>
                                                                    </div>


                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    



                    @php
                    $expectedPath = "/docs/adm_docs/up_docs/" . $document->ad_api_id . "/";
                    @endphp
                    
                    @if(isset($document->ad_filelink) && strpos($document->ad_filelink, $expectedPath) === 0)
                        <a href="{{ $baseUrl }}{{ $document->ad_filelink }}" target="_blank" class="btn btn-outline-primary btn-sm downloadbtn">
                            <i class="fa fa-eye"></i> Preview
                        </a>
                    @else
                        <a href="{{ $baseUrl }}/docs/adm_docs/up_docs/{{ $document->ad_filename }}" target="_blank" class="btn btn-outline-primary btn-sm downloadbtn">
                            <i class="fa fa-eye"></i> Preview
                        </a>
                    @endif
                                    
                                    


 @php
 $username = auth()->user()->username;
    $allowedUsers = [
        'haider.raza', 'aiman.adnan', 'somia.maqsood', 'asbah.rehman', 'zainabsanik',
        'sidra.tanveer', 'zonia.akbar', 'hassan.farooq', 'bilal.saleem',
        'muhammad.hamid', 'noreen', 'sheeraz'
    ];
@endphp

@if (in_array($username, $allowedUsers))
    {{-- Verify or Unverified Button --}}
    @if ($document->ad_verification_status != 1)
        <button class="btn btn-outline-success btn-sm verifyBtn"
                data-toggle="modal"
                data-target="#verificationModal{{ $document->ad_id }}"
                data-ad-reason="{{ $document->ad_reason }}">
            <i class="fa fa-check"></i> Verify Now
        </button>
    @else
        <button class="btn btn-outline-danger btn-sm verifyBtn"
                data-toggle="modal"
                data-target="#verificationModal{{ $document->ad_id }}"
                data-ad-reason="{{ $document->ad_reason }}">
            <i class="fa fa-times"></i> Unverified
        </button>
    @endif

    <a href="javascript:void(0);" class="btn btn-outline-info btn-sm previousUploadsBtn"
       data-id="{{ $document->ad_id }}">
        <i class="fa fa-folder-open"></i> Previous Uploads
    </a>

    @if ($document->ad_verification_status == 1)
        <button class="btn btn-danger btn-sm deleteDocumentBtn" disabled>
            <i class="fa fa-trash"></i> Delete
        </button>
    @else
        <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm deleteDocumentBtn"
           data-id="{{ $document->ad_id }}">
            <i class="fa fa-trash"></i> Delete
        </a>
    @endif
@endif




                                        <!-- Bootstrap Modal -->
                                        <div class="modal fade" id="verificationModal{{$document->ad_id}}" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel{{$document->ad_id}}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title " id="verificationModalLabel{{$document->ad_id}}">
                                                            @if ($document->ad_verification_status != 1)
                                                            Document Status
                                                            @else
                                                            Document Status

                                                            @endif
                                                        </h5>
                                                        <button type="button" style="font-size: 25px;" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">


                                                        <form id="verifyDocForm{{$document->ad_id}}">
                                                            @csrf
                                                            <div class="form-group">

                                                                <br>
                                                                <label for="reason">Remarks:</label>
                                                                <input type="text" style="font-size: 18px;" class="form-control" id="ad_reason{{$document->ad_id}}" name="ad_reason" placeholder="Enter reason" value="{{$document->ad_reason}}">
                                                                <span id="ad_reason{{$document->ad_id}}-error" class="error text-danger"></span>
                                                            </div>
                                                            <div id="success_msg" style="background-color: #1abc9c;" class="alert alert-info text-white invisible" role="alert"></div>
                                                        </form>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                                        <button class="btn btn-primary sendMailBtn" onclick="sendMail(event, {{$document->ad_id}}, {{$document->ad_api_id}}, '{{$document->student_email}}')">
                                                            <i class="icon-envelope"></i> Send Email
                                                        </button>


                                                                                    <div class="btn-group">
                                <button type="button" class="btn
                                    @if ($document->ad_verification_status == 1)
                                        btn-danger
                                    @else
                                        btn-success
                                    @endif"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">

                                    Select Status
                                    <i class="ml-1 fa fa-caret-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @if ($document->ad_verification_status != 2 && $document->ad_verification_status != 1)
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="verifyDoc(event, {{$document->ad_id}}, 2)">
                                            To Be Verified
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="verifyDoc(event, {{$document->ad_id}}, {{$document->ad_verification_status == 1 ? '0' : '1'}})">
                                        @if ($document->ad_verification_status == 1)
                                            Unverified
                                        @else
                                            Verify
                                        @endif
                                    </a>
                                </div>
                            </div>


                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </td>



                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>







        </div>
        <!-- Default table border table end-->






        <div class="modal fade" id="previousUploadsModal" tabindex="-1" role="dialog" aria-labelledby="previousUploadsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previousUploadsModalLabel"><i class="fa fa-folder-open"></i> Previous Uploads</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Spinner -->
                        <div id="loadingMessage" class="text-center text-primary my-3" style="display: none;">
                            <strong>Loading Data...</strong>
                        </div>

                        <!-- Content -->
                        <div id="previousUploadsContent" class="p-3">
                            <!-- Content dynamically loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>










    </div>
@endsection
<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>

<script>
var baseUrl = window.location.protocol + "//" + window.location.host;
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
    $('.viewbtn').click(function() {
        var documentLink = $(this).data('document-link');
        var iframe = $('#documentIframe');
        var loadingIndicator = $('#loadingIndicator');
        loadingIndicator.show();
        iframe.attr('src', 'about:blank');
         iframe.off('load');
        setTimeout(function() {
            iframe.attr('src', documentLink);
            iframe.on('load', function() {
                loadingIndicator.hide();
            });
        }, 1000);
    });


    $(document).on('click', '.deleteDocumentBtn', function () {
        var button = $(this);
        var docId = button.data('id');

        if (confirm('Are you sure you want to delete this document?')) {
            // Change button text and add spinner
            var originalHtml = button.html();
            button.html('<i class="fa fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            $.ajax({
                url: "{{ url('/api/delete-uploaded-student-doc') }}",
                type: "GET",
                data: { id: docId
                 },
                 headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
                    "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                    "Accept": "application/json"
                },
                success: function (response) {
                    if (response.success) {
                        button.html('<i class="fa fa-check"></i> Deleted');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        alert("Something went wrong!");
                        button.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseText);
                    button.html(originalHtml).prop('disabled', false);
                }
            });
        }
    });


    $('.previousUploadsBtn').click(function () {
        var recordId = $(this).data('id'); // Get the `ad_id` from the button's data attribute

        // Show the modal and display the loading message
        $('#previousUploadsModal').modal('show');
        $('#loadingMessage').show(); // Show "Loading Data..."
        $('#previousUploadsContent').html(''); // Clear previous content

        const urlParams = new URLSearchParams(window.location.search);
        var sess_id=urlParams.get('sess_id');

        // Send AJAX request to fetch previous uploads
        $.ajax({
            url: '/api/fetch-previous-uploads', // Backend endpoint
            method: 'GET',
            data: { id: recordId,
                sess_id: sess_id
             },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
                    },
            success: function (response) {
                $('#loadingMessage').hide(); // Remove the loading message

                if (response.status && response.data.length > 0) {
                    // Build the table with fetched data
                    var tableHTML = `
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>

                                        <th>File Type</th>
                                        <th>Uploaded Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.data.forEach(function (doc, index) {
                        tableHTML += `
                            <tr>
                                <td>${index + 1}</td>

                                <td>${doc.ad_type}</td>
                                <td>${new Date(doc.ad_entry_datetime).toLocaleDateString()}</td>
                                <td>
                                    <a href="${baseUrl}/docs/adm_docs/up_docs/${doc.ad_filename }" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                    });



                    tableHTML += '</tbody></table></div>';

                    // Append the table to the modal content
                    $('#previousUploadsContent').html(tableHTML);
                } else {
                    // No data found
                    $('#previousUploadsContent').html(`
                        <div class="alert alert-warning text-center" role="alert">
                            <i class="fa fa-info-circle"></i> No previous uploads found.
                        </div>
                    `);
                }
            },
            error: function () {
                $('#loadingMessage').hide(); // Remove the loading message
                $('#previousUploadsContent').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="fa fa-exclamation-circle"></i> Error fetching data. Please try again later.
                    </div>
                `);
            },
        });
    });










});

    function verifyDoc(event, ad_id, verification_status) {

        // alert(`${ad_id}----${verification_status}`);
        // return




        event.preventDefault(); // Prevent form submission
        let button = $(event.currentTarget); // Get the clicked button
        let allButtons = $('.verifyBtn'); // Get all buttons with the class "verifyBtn"
        let downloadButton = $('.downloadbtn');
        let sendMailBtn = $('.sendMailBtn');
        // Disable all buttons except the clicked button
        allButtons.not(button).prop('disabled', true);
        downloadButton.prop('disabled', true);
        sendMailBtn.prop('disabled', true);
        let spinner = '<i class="icon-spinner icon-spin"></i> Please Wait...';
        button.html(spinner);
        let data = $("#verifyDocForm"+ad_id).serializeArray();
        data.push({ name: 'ad_id', value: ad_id });
        data.push({ name: 'verification_status', value: verification_status });
        $.ajax({
            type: 'POST',
            url: '/api/update-doc-status',
            data: data,
            headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
            success: function(response) {
                if (response.status) {
                    $('#success_msg').removeClass('invisible');
                    $('#success_msg').html(response.message);
                    button.prop('disabled', false); // Re-enable the button
                    downloadButton.prop('disabled', false);
                    allButtons.not(button).prop('disabled', false); // Re-enable other buttons
                    setTimeout(function() {
                        $('#success_msg').addClass('invisible');
                        // button.html('<i class="icon-ok"></i> Please Wait');
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $("#" + key + ad_id + "-error").text(value[0]);
                        $("#" + key + ad_id + "-error").addClass('text-danger');
                        setTimeout(function() {
                            $("#" + key + ad_id + "-error").text('');
                        }, 5000);
                    });
                }
                button.html(verification_status === 2 ? 'To Be Verified' : 'Verify');
                // button.html('Verify'); // Reset the button text
                button.prop('disabled', false); // Re-enable the button
                allButtons.not(button).prop('disabled', false); // Re-enable other buttons
                downloadButton.prop('disabled', false);
            }
        });
    }

    function sendMail_(event, ad_id, ad_api_id,student_email) {



        event.preventDefault(); // Prevent form submission
        let button = $(event.currentTarget); // Get the clicked button
        let allButtons = $('.verifyBtn'); // Get all buttons with the class "verifyBtn"
        let downloadButton = $('.downloadbtn');
        let sendMailBtn = $('.sendMailBtn');
        // Disable all buttons except the clicked button
        allButtons.not(button).prop('disabled', true);
        downloadButton.prop('disabled', true);
        sendMailBtn.prop('disabled', true);
        let spinner = '<i class="icon-spinner icon-spin"></i> Please Wait...';
        button.html(spinner);
        let data = $("#verifyDocForm"+ad_id).serializeArray();
        data.push({ name: 'ad_id', value: ad_id });
        data.push({ name: 'ad_api_id', value: ad_api_id });
        let ad_reason_value = $("#ad_reason" + ad_id).val();
        // console.log(data)
        $.ajax({
            // type: 'POST',
            type: 'POST',
            // url: 'https://cms.bnu.edu.pk/send-otp',  // Correct the URL according to your application's routing
            url: '/api/send-mail-doc-verification',  // Correct the URL according to your application's routing
                data: {
                    description: ad_reason_value,
                    email: student_email
                },
                headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
            success: function(response) {

                // if (response && (response.status || response === 1)) {
                    $('#success_msg').removeClass('invisible');
                    $('#success_msg').html(response.message);
                    button.prop('disabled', false); // Re-enable the button
                    downloadButton.prop('disabled', false);
                    allButtons.not(button).prop('disabled', false); // Re-enable other buttons
                    setTimeout(function() {
                        $('#success_msg').addClass('invisible');
                        // button.html('<i class="icon-ok"></i> Please Wait');
                        window.location.reload();
                    }, 2000);
                // }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $("#" + key + ad_id + "-error").text(value[0]);
                        $("#" + key + ad_id + "-error").addClass('text-danger');
                        setTimeout(function() {
                            $("#" + key + ad_id + "-error").text('');
                        }, 5000);
                    });
                }
                button.html('Send Mail'); // Reset the button text
                button.prop('disabled', false); // Re-enable the button
                allButtons.not(button).prop('disabled', false); // Re-enable other buttons
                downloadButton.prop('disabled', false);
            }
        });
    }

/// This function handles the notification sending when the button is clicked
function sendNotification() {
    // Get the button and disable it while sending the request
    let button = $('#sendNotificationBtn');
    button.prop('disabled', true); // Disable the button to prevent double click
    button.html('<i class="fa fa-spinner fa-spin"></i> Sending...'); // Show loading spinner on button

    // Get the data from the form
    let remarkInput = $('#remarkInput').val(); // Remark input value
    let saiEnrollmentNo = $('#sai_enrollment_no').val(); // Hidden enrollment number

    // Send the AJAX request to the server
    $.ajax({
        url: '/api/send-document-notification', // Replace with your actual URL to send notification
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
            remarkInput: remarkInput,
            sai_enrollment_no: saiEnrollmentNo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            // Show success message inside the modal
            $('#notificationMessage').html('<div class="alert alert-success" role="alert">' + response.message + '</div>')
                                    .show(); // Make the success message visible

            // Enable the button and reset its text
            button.prop('disabled', false);
            button.html('<i class="fa fa-paper-plane"></i> Send Notification');

            // Optionally, close the modal and reload the page after success
            setTimeout(function() {
                $('#notificationModal').modal('hide'); // Close the modal
                location.reload(); // Reload the page after 2 seconds
            }, 2000); // Delay of 2 seconds before closing and reloading
        },
        error: function(xhr, status, error) {
            // Show error message inside the modal
            let errorMessage = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
            $('#notificationMessage').html('<div class="alert alert-danger" role="alert">' + errorMessage + '</div>')
                                    .show(); // Make the error message visible

            // Enable the button and reset its text
            button.prop('disabled', false);
            button.html('<i class="fa fa-paper-plane"></i> Send Notification');
        }
    });
}





$(document).ready(function() {






    $('.fileDownload').click(function(e) {
    e.preventDefault();
    const button = $(this);
    const row = button.closest('.row'); // Ensure correct row selection
    const api_id = button.data('api_id');
    const prog_id = button.data('prog_id');
    const sess_id = button.data('sess_id');

    // Disable button and show spinner
    button.prop('disabled', true).html(`
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating Download, Please Wait...
    `);

    // Create Progress Bar
    const progressBar = $(`
        <div class="progress mt-2" style="height: 5px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    `);

    button.after(progressBar);

    const downloadButton = row.find('.downloadZip');
    downloadButton.addClass('invisible');

    const formData = new FormData();
    formData.append('api_id', api_id);
    formData.append('prog_id', prog_id);
    formData.append('sess_id', sess_id);

    $('.fileDownload').prop('disabled', true);

    $.ajax({
        method: "POST",
        url: "/api/download-student-documents-bundle",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    const percentComplete = (evt.loaded / evt.total) * 100;
                    progressBar.find('.progress-bar').css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        }
    })
    .done(function(res) {
        if (res.status) {
            // Success - Enable button and show download link
            progressBar.remove();
            button.removeClass('btn-outline-primary')
                  .addClass('btn-primary')
                  .prop('disabled', false)
                  .html('<i class="fa fa-folder" aria-hidden="true"></i> Try Again');

            // Show download button in the same row
            downloadButton.attr("href", res.url)
                .attr("target", "_blank") // Open in new tab
                .removeClass("invisible btn-outline-primary")
                .addClass("btn-success")
                .html('<i class="fa fa-download"></i> Download Now');


        }
    })
    .fail(function(xhr) {
        progressBar.remove();
        button.prop('disabled', false)
              .html('<i class="fa fa-exclamation-circle"></i> Error! Try Again')
              .removeClass('btn-outline-primary')
              .addClass('btn-danger');
    })
    .always(function() {
        progressBar.remove();
        button.removeClass('btn-outline-primary')
              .addClass('btn-primary')
              .prop('disabled', false)
              .html('<i class="fa fa-folder" aria-hidden="true"></i> Generate Again');

        $('.fileDownload').prop('disabled', false);
    });
});













});






</script>
