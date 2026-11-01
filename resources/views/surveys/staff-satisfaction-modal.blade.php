@extends('layouts.app')

@section('title')
    BNU CMS
@stop

@section('content')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- staff Description Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" role="dialog"
     aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content custom-modal-content">

            <!-- Decorative Header -->
            <div class="modal-top-header"></div>

            <div class="modal-body text-center pt-0">
                <!-- Icon -->
                <div class="icon-container">
                    <i class="fa fa-users fa-2x" style="color: #357694;"></i>
                </div>

                <h4 class="modal-title-text mt-3">
                    Please Fill out the Employee Satisfaction Form
                </h4>

                <p class="text-muted mb-4">
                    This information helps HR better understand your satisfaction.
                </p>

                <!-- Proceed Button (ONLY exit path) -->
                <button type="button"
                        class="btn btn-proceed btn-block mb-3"
                        id="proceedToStaffForm">
                    Proceed
                </button>
            </div>

        </div>
    </div>
</div>


<style>
    .custom-modal-content {
        border: none;
        border-radius: 15px; /* Softer corners */
        overflow: hidden; /* Keep header inside proper radius */
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .modal-top-header {
        background-color: #357694; /* Teal/Blue color */
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
        margin: -40px auto 0; /* Pull up by half height to overlap */
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    }

    .modal-title-text {
        color: #202124;
        font-weight: 700;
        font-size: 1.35rem;
    }

    .btn-proceed {
        background-color: #357694;
        color: white;
        border-radius: 50px; /* Pill shape */
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 500;
        border: none;
        width: 60%; /* Or auto with min-width */
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

    .custom-close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        color: white;
        opacity: 0.8;
        z-index: 1050;
        text-shadow: none;
        outline: none;
    }

    .custom-close-btn:hover {
        color: white;
        opacity: 1;
    }
</style>

<!-- jQuery and Bootstrap JS -->
<script>
$(document).ready(function() {
    // Show the staff description modal on page load
    // $('#staffModal').modal('show');

    // Handle Proceed button
    $('#proceedToStaffForm').click(function() {
        window.location.href = "{{ route('staffSatisfaction') }}";
    });

    // Handle Close link/button
    function setStaffDescriptionFlagAndClose(e) {
        if(e) e.preventDefault(); // Prevent default anchor behavior

        $.ajax({
            url: "{{ route('set-staff-satisfaction-survey-flag') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function() {
                $('#staffModal').modal('hide');
                window.location.reload();
            }
        });
    }

    // $('#closeStaffModal').click(setStaffDescriptionFlagAndClose);
    // $('.close').click(setStaffDescriptionFlagAndClose);

    // Prevent modal close by outside click or ESC key (optional, can be removed if strictly following 'Close' button only)
    $('#staffModal').modal({
        backdrop: 'static',
        keyboard: false
    });




});
</script>
@endsection

