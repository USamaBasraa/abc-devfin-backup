@extends('layouts.app')
@section('title') BNU CMS @stop

@section('content')
    <div class="container">
        <!-- Default table border table start-->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3>Student's document verification </h3>
                        <span>Student's document verification for admissions. This table displays the verification status of various admission documents. Each document is listed with its corresponding Document Name, Status, and available action.</span>
                    </div>
                    <div class="col-md-6 text-center">
                        {{$admissionDocument[0]->applicant_name}}
                        <img class="img-fluid rounded-circle" style="width: 125px; height: 125px;" src="https://admission.bnu.edu.pk/images/{{ $record->api_imagename}}" alt="profile-img">
                    </div>

                </div>
            </div>


            <div class="card-block table-border-style">
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
                                        @if ($Document->ad_type=='O_Level')
                                            O-Level
                                            @elseif ($Document->ad_type=='Cnic')
                                            Applicant CNIC / Passport
                                            @elseif ($Document->ad_type=='GaurdianCnic')
                                            Gaurdian CNIC/Passport
                                            @elseif ($Document->ad_type=='A_Level')
                                            A-Level
                                            @elseif ($Document->ad_type=='4_Years')
                                            {{-- 4 Years Graduation --}}
                                            BA/Bsc transcript and degree
                                          @elseif ($Document->ad_type=='2_Years')
                                          {{-- 2 Years Graduation --}}
                                          BA/Bsc transcript and degree
                                          @elseif ($Document->ad_type=='papers')
                                             Admission Test / Interview / Evaluation
                                          @else
                                        {{$Document->ad_type}}

                                        @endif

                                    </td>

                                    {{-- <td scope="row">
                                        {{$Document->ad_type}}</td> --}}
                                    <td>
                                        <span class="badge badge-pill <?php echo $Document->ad_verification_status == 1 ? 'badge-success' : ($Document->ad_verification_status == 2 ? 'badge-info' : 'badge-warning'); ?> badge-lg">
                                            <?php
                                                if ($Document->ad_verification_status == 1) {
                                                    echo 'Verified';
                                                } elseif ($Document->ad_verification_status == 2) {
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
                                        {{-- start for view --}}
                                        <!-- <button class="btn btn-primary viewbtn" data-toggle="modal" data-target="#myModal" data-document-link="{{$Document->ad_filelink}}">
                                            <i class="icon-eye-open"></i> View
                                        </button> -->
                                        <!-- <button class="btn btn-primary viewbtn" data-toggle="modal" data-target="#myModal" data-document-link="{{ $Document->ad_type == 'papers' ? 'http://admissiontest.bnu.edu.pk/docs/adm_docs/up_docs/' . $Document->ad_filelink : $Document->ad_filelink }}">
                                            <i class="icon-eye-open"></i> View
                                        </button> -->



                                        <!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                        </div> -->





                                        {{-- end for view button --}}
                                        <a href="{{ $Document->ad_type == 'papers' ? 'https://cms.bnu.edu.pk/docs/adm_docs/up_docs/' . $Document->ad_filename : 'https://cms.bnu.edu.pk/docs/adm_docs/up_docs/' . $Document->ad_filename }}" target="_blank" class="btn btn-primary downloadbtn">
                                            <i class="icon-download"></i>
                                            Download
                                        </a>
                                        <!-- @if ($Document->ad_verification_status != 1)
                                            <button class="btn btn-success verifyBtn" data-toggle="modal" data-target="#verificationModal{{$Document->ad_id}}" data-ad-reason="{{$Document->ad_reason}}">
                                                <i class="icon-ok"></i> Verify Now
                                            </button>
                                        @else
                                            <button class="btn btn-danger verifyBtn" data-toggle="modal" data-target="#verificationModal{{$Document->ad_id}}" data-ad-reason="{{$Document->ad_reason}}">
                                                <i class="icon-remove"></i> Unverify it
                                            </button>
                                        @endif -->



                                        <!-- Bootstrap Modal -->
                                        <div class="modal fade" id="verificationModal{{$Document->ad_id}}" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel{{$Document->ad_id}}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white" id="verificationModalLabel{{$Document->ad_id}}">
                                                            @if ($Document->ad_verification_status != 1)
                                                                Verify Document
                                                            @else
                                                                Unverify Document
                                                            @endif
                                                        </h5>
                                                        <button type="button" style="font-size: 25px;" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">


                                                        <form id="verifyDocForm{{$Document->ad_id}}">
                                                            <div class="form-group">

                                                                <br>
                                                                <label for="reason">Remarks:</label>
                                                                <input type="text" style="font-size: 18px;" class="form-control" id="ad_reason{{$Document->ad_id}}" name="ad_reason" placeholder="Enter reason" value="{{$Document->ad_reason}}">
                                                                <span id="ad_reason{{$Document->ad_id}}-error" class="error text-danger"></span>
                                                            </div>
                                                            <div id="success_msg" style="background-color: #1abc9c;" class="alert alert-info text-white invisible" role="alert"></div>
                                                        </form>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                                        <button class="btn btn-primary sendMailBtn" onclick="sendMail(event, {{$Document->ad_id}}, {{$Document->ad_api_id}}, '{{$Document->student_email}}')">
                                                            <i class="icon-envelope"></i> Send Email
                                                        </button>


                                                        <button class="btn @if ($Document->ad_verification_status == 1) btn-danger @else btn-success @endif" onclick="verifyDoc(event, {{$Document->ad_id}}, {{$Document->ad_verification_status == 1 ? '0' : '1'}})">
                                                            @if ($Document->ad_verification_status == 1)
                                                                Unverify it
                                                            @else
                                                                Verify it
                                                            @endif
                                                        </button>

                                                        @if ($Document->ad_verification_status != 2 && $Document->ad_verification_status != 1)
                                                        <button class="btn btn-warning" onclick="verifyDoc(event, {{$Document->ad_id}}, 2)">
                                                            To Be Verified
                                                        </button>
                                                    @endif

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
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

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

        // Show the loading indicator
        loadingIndicator.show();

        // Clear the iframe content
        iframe.attr('src', 'about:blank');
         // Remove any existing 'load' event handlers
         iframe.off('load');

        // Set a timeout to load the new content
        setTimeout(function() {
            iframe.attr('src', documentLink);

            // Hide the loading indicator once the content is loaded
            iframe.on('load', function() {
                loadingIndicator.hide();
            });
        }, 1000); // 1000 milliseconds (1 second) delay as an example
    });
});



    function verifyDoc(event, ad_id, verification_status) {
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
            url: 'api/update-doc-status',
            data: data,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
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

    function sendMail(event, ad_id, ad_api_id,student_email) {
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
            // url: 'https://cms.bnu.edu.pk/api/send-otp',  // Correct the URL according to your application's routing
            url: '/api/send-mail-doc-verification',  // Correct the URL according to your application's routing
                data: {
                    description: ad_reason_value,
                    email: student_email
                },
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
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





</script>
