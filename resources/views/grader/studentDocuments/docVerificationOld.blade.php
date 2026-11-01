@extends('layouts.app')
@section('title') BNU CMS @stop

<style>

#notificationMessage {
    display: none;
}

/* Initially hide the spinner */
#loadingSpinner {
    display: none;
}

/* Show the spinner when needed */
#loadingSpinner.active {
    display: flex; /* Or block if alignment is not needed */
    justify-content: center;
    align-items: center;
}
.dropdown-menu{
    min-width: 10rem  !important;

}

</style>
<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
$baseUrl = $protocol . $_SERVER['HTTP_HOST'];
// $baseUrl = 'http://devstudent.bnu.edu.pk/';
?>

@section('content')
    <div class="container-fluid mt-4">
        <!-- Default table border table start-->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3>Student's document verification </h3>
                        <span>Student's document verification for admissions. This table displays the verification status of various admission documents. Each document is listed with its corresponding Document Name, Status, and available action.</span>
                    </div>
                    <div class="col-md-6 text-center">
                        {{$admissionDocument[0]->applicant_name??'N/A'}}

                <img class="img-fluid rounded-circle" style="width: 125px; height: 125px;" src="{{ $baseUrl }}/images/{{ $record->api_imagename }}" alt="profile-img">
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
                    
                    <div class="d-flex">
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
                        @foreach($admissionDocument as $Document)
                        <?php $sr++; ?>

                                <tr>
                                    <td scope="row">{{$sr}}</td>


                                    <td scope="row">
                                        @if ($Document->sd_ad_type=='O_Level')
                                            O-Level
                                            @elseif ($Document->sd_ad_type=='Cnic')
                                            Applicant CNIC / Passport
                                            @elseif ($Document->sd_ad_type=='GaurdianCnic')
                                            Gaurdian CNIC/Passport
                                            @elseif ($Document->sd_ad_type=='A_Level')
                                            A-Level
                                            @elseif ($Document->sd_ad_type=='4_Years')
                                            {{-- 4 Years Graduation --}}
                                            BA/Bsc transcript and degree
                                          @elseif ($Document->sd_ad_type=='2_Years')
                                          {{-- 2 Years Graduation --}}
                                          BA/Bsc transcript and degree
                                          @elseif ($Document->sd_ad_type=='papers')
                                             Admission Test / Interview / Evaluation
                                          @else
                                        {{$Document->sd_ad_type}}

                                        @endif

                                    </td>

                                    {{-- <td scope="row">
                                        {{$Document->sd_ad_type}}</td> --}}
                                    <td>
                                    <span class="badge badge-pill <?php echo $Document->sd_ad_verification_status == 1 ? 'badge-success' : ($Document->sd_ad_verification_status == 2 ? 'badge-info' : 'badge-warning'); ?> badge-lg" style="min-width: 100px; text-align: center; display: inline-block;">
                                            <?php
                                                if ($Document->sd_ad_verification_status == 1) {
                                                    echo 'Verified';
                                                } elseif ($Document->sd_ad_verification_status == 2) {
                                                    echo 'To be Verified';
                                                } else {
                                                    echo 'Pending';
                                                }
                                            ?>
                                        </span>



                                    </td>
                                    <td>
                                        @if ($Document->result_status == 'Declared')
                                        <span class="badge badge-success badge-lg">{{$Document->result_status}}</span>
                                    @else
                                        <span class="badge badge-secondary badge-lg">{{$Document->result_status}}</span>
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
                            $expectedPath = "/docs/adm_docs/up_docs/" . $Document->sd_ad_api_id . "/";
                        @endphp
                        
                        @if(isset($Document->sd_ad_filelink) && strpos($Document->sd_ad_filelink, $expectedPath) === 0)
                            <a href="{{ $baseUrl }}{{ $Document->sd_ad_filelink }}" target="_blank" class="btn btn-outline-primary btn-sm downloadbtn">
                                <i class="fa fa-eye"></i> Preview
                            </a>
                        @else
                            <a href="{{ $baseUrl }}/docs/adm_docs/up_docs/{{ $Document->sd_ad_filename }}" target="_blank" class="btn btn-outline-primary btn-sm downloadbtn">
                                <i class="fa fa-eye"></i> Preview
                            </a>
                        @endif
                        



                    {{-- Verify or Unverified Button --}}
                    @if ($Document->sd_ad_verification_status != 1)
                        <button class="btn btn-outline-success btn-sm verifyBtn" data-toggle="modal" data-target="#verificationModal{{$Document->sd_ad_id}}" data-ad-reason="{{$Document->sd_ad_reason}}">
                            <i class="fa fa-check"></i> Verify Now
                        </button>
                    @else
                        <button class="btn btn-outline-danger btn-sm verifyBtn" data-toggle="modal" data-target="#verificationModal{{$Document->sd_ad_id}}" data-ad-reason="{{$Document->sd_ad_reason}}">
                            <i class="fa fa-times"></i> Unverified
                        </button>
                    @endif
                    <a href="javascript:void(0);" class="btn btn-outline-info btn-sm previousUploadsBtn" data-id="{{ $Document->sd_ad_id }}">
                        <i class="fa fa-folder-open"></i> Previous Uploads
                    </a>

                    @if ($Document->sd_ad_verification_status == 1)
                    <button class="btn btn-danger btn-sm deleteDocumentBtn" disabled>
                        <i class="fa fa-trash"></i> Delete
                    </button>
                @else
                    <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm deleteDocumentBtn" data-id="{{ $Document->sd_ad_id }}">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                @endif




                                        <!-- Bootstrap Modal -->
                                        <div class="modal fade" id="verificationModal{{$Document->sd_ad_id}}" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel{{$Document->sd_ad_id}}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title " id="verificationModalLabel{{$Document->sd_ad_id}}">
                                                            @if ($Document->sd_ad_verification_status != 1)
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


                                                        <form id="verifyDocForm{{$Document->sd_id}}">
                                                            @csrf
                                                            <div class="form-group">

                                                                <br>
                                                                <label for="reason">Remarks:</label>
                                                                <input type="text" style="font-size: 18px;" class="form-control" id="sd_ad_reason{{$Document->sd_id}}" name="sd_ad_reason" placeholder="Enter reason" value="{{$Document->sd_ad_reason}}">
                                                                <span id="sd_ad_reason{{$Document->sd_id}}-error" class="error text-danger"></span>
                                                            </div>
                                                            <div id="success_msg" style="background-color: #1abc9c;" class="alert alert-info text-white invisible" role="alert"></div>
                                                        </form>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                                        <button class="btn btn-primary sendMailBtn" onclick="sendMail(event, {{$Document->sd_id}}, {{$Document->sd_ad_api_id}}, '{{$Document->student_email}}')">
                                                            <i class="icon-envelope"></i> Send Email
                                                        </button>


                                                                                    <div class="btn-group">
                                <button type="button" class="btn
                                    @if ($Document->sd_ad_verification_status == 1)
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
                                    @if ($Document->sd_ad_verification_status != 2 && $Document->sd_ad_verification_status != 1)
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="verifyDoc(event, {{$Document->sd_id}}, 2)">
                                            To Be Verified
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="verifyDoc(event, {{$Document->sd_id}}, {{$Document->sd_ad_verification_status == 1 ? '0' : '1'}})">
                                        @if ($Document->sd_ad_verification_status == 1)
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
                data: { id: docId,
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
        var recordId = $(this).data('id'); // Get the `sd_ad_id` from the button's data attribute

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

                                <td>${doc.sd_ad_type}</td>
                                <td>${new Date(doc.sd_ad_entry_datetime).toLocaleDateString()}</td>
                                <td>
                                    <a href="${baseUrl}/docs/adm_docs/up_docs/${doc.sd_ad_filename}" target="_blank" class="btn btn-outline-primary btn-sm">
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

    function verifyDoc(event, sd_ad_id, verification_status) {

        // alert(`${sd_ad_id}----${verification_status}`);
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
        let data = $("#verifyDocForm"+sd_ad_id).serializeArray();
        data.push({ name: 'sd_ad_id', value: sd_ad_id });
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
                        $("#" + key + sd_ad_id + "-error").text(value[0]);
                        $("#" + key + sd_ad_id + "-error").addClass('text-danger');
                        setTimeout(function() {
                            $("#" + key + sd_ad_id + "-error").text('');
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

    function sendMail_(event, sd_ad_id, sd_ad_api_id,student_email) {



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
        let data = $("#verifyDocForm"+sd_ad_id).serializeArray();
        data.push({ name: 'sd_ad_id', value: sd_ad_id });
        data.push({ name: 'sd_ad_api_id', value: sd_ad_api_id });
        let sd_ad_reason_value = $("#sd_ad_reason" + sd_ad_id).val();
        // console.log(data)
        $.ajax({
            // type: 'POST',
            type: 'POST',
            // url: 'https://cms.bnu.edu.pk/send-otp',  // Correct the URL according to your application's routing
            url: '/api/send-mail-doc-verification',  // Correct the URL according to your application's routing
                data: {
                    description: sd_ad_reason_value,
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
                        $("#" + key + sd_ad_id + "-error").text(value[0]);
                        $("#" + key + sd_ad_id + "-error").addClass('text-danger');
                        setTimeout(function() {
                            $("#" + key + sd_ad_id + "-error").text('');
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
            sai_enrollment_no: saiEnrollmentNo,

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
