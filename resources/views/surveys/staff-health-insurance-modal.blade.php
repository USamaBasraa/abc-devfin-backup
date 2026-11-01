@extends('layouts.app')

@section('title')
    BNU CMS
@stop

@section('content')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Staff Health Insurance Modal -->
<div class="modal fade" id="staffHealthModal" tabindex="-1" role="dialog"
     aria-labelledby="staffHealthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content custom-modal-content">

            <!-- Decorative Header -->
            <div class="modal-top-header"></div>

            <div class="modal-body text-center pt-0">
                <!-- Icon -->
                <div class="icon-container">
                    <i class="fa fa-heartbeat fa-2x" style="color: #357694;"></i>
                </div>

                <h4 class="modal-title-text mt-3">
                    Health Insurance Survey
                </h4>

                <p class="text-muted mb-4">
                    Please fill out the survey for inclusion of family members in Health Insurance.
                </p>

                <!-- Proceed Button -->
                <button type="button"
                        class="btn btn-proceed btn-block mb-3"
                        id="proceedToHealthForm">
                    Proceed
                </button>
                
                <!-- Close Button -->
         <a href="javascript:void(0);" 
                   class="close-link d-block mb-3" 
                   id="closeHealthModal">
                    Close
                </a> 
            </div>

        </div>
    </div>
</div>

<style>
    .custom-modal-content {
        border: none;
        border-radius: 15px; 
        overflow: hidden; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .modal-top-header {
        background-color: #357694; 
        height: 100px;
        width: 100%;
        border: none;
    }

    .icon-container {
        width: 80px;
        height: 80px;
        background-color: #ffffff;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: -40px auto 0; 
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
    }

    .modal-title-text {
        color: #202124;
        font-weight: 700;
        font-size: 1.35rem;
    }

    .btn-proceed {
        background-color: #357694;
        color: white;
        border-radius: 50px; 
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 500;
        border: none;
        width: 60%; 
        margin: 0 auto;
        display: block;
    }

    .btn-proceed:hover {
        background-color: #2a5d75;
        color: white;
    }

    .close-link {
        color: #357694;
        font-weight: 500;
        text-decoration: none;
        font-size: 1rem;
        cursor: pointer;
    }

    .close-link:hover {
        color: #2a5d75;
        text-decoration: underline;
    }
</style>

<script>
$(document).ready(function() {
    // Show the modal on page load
    // $('#staffHealthModal').modal('show');

    // Handle Proceed button
    $('#proceedToHealthForm').click(function() {
        window.location.href = "{{ route('staffHealthInsuranceSurvey') }}";
    });

    // Handle Close link/button
    function setHealthSurveyFlagAndClose(e) {
        if(e) e.preventDefault(); 

        $.ajax({
            url: "{{ route('set-staff-health-insurance-survey-flag') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function() {
                $('#staffHealthModal').modal('hide');
                window.location.reload();
            }
        });
    }

    $('#closeHealthModal').click(setHealthSurveyFlagAndClose);
    $('.close').click(setHealthSurveyFlagAndClose);

    $('#staffHealthModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});
</script>
@endsection
