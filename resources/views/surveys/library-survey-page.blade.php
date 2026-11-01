@extends('layouts.app')
@section('title')
    BNU CMS
@stop

@section('content')

<style>
/* Disable sidebar interactions */
.pcoded-navbar, .pcoded-navbar * {
    pointer-events: none !important;
    cursor: default !important;
}

/* Make sidebar appear disabled */
.pcoded-navbar {
    opacity: 0.9;
}
</style>

<!-- Include Toast Notifications Component -->
@include('components.toast-notifications')

<script>
// Additional script to ensure sidebar links are disabled
document.addEventListener('DOMContentLoaded', function() {
    // Disable all links in the sidebar
    var sidebarLinks = document.querySelectorAll('.pcoded-navbar a');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }, true);
        
        // Remove href attribute to prevent navigation
        link.removeAttribute('href');
        link.style.pointerEvents = 'none';
    });
    
    // Disable all JavaScript events on sidebar elements
    var sidebarElements = document.querySelectorAll('.pcoded-navbar, .pcoded-navbar *');
    sidebarElements.forEach(function(element) {
        element.style.pointerEvents = 'none';
    });
});
</script>



<div class="container-fluid p-0">
    <!-- Header Banner removed -->
    <!-- Survey Form Section -->
    <div class="my-4">
        <div class="row mx-0">
            <div class="col-md-12">
                <div class="card shadow-lg border-0" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-body p-4">

                        
                        <div class="mb-4">
                            <h1 class="text-center mb-3" style="font-weight: 600; color: #2C3E50;">BNU Library Satisfaction Survey</h1>
                        </div>


                        <div class="alert alert-info mb-4 p-3" style="background-color: #edf2f7; border-left: 4px solid #2C3E50; color: #2C3E50;">
                            <p class="mb-0" style="font-size: 16px; line-height: 1.6;">The BNU library is conducting a survey to determine the satisfaction level of users with current opening hours. Your participation will help us enhance our services and opening times to better serve the BNU community.</p>
                        </div>


                        <form id="librarySurveyFormStandalone" method="POST">
                            @csrf
                            <!-- Q1 -->
                            <div class="form-group mb-3">
                                <label for="roleSelect" class="form-label fw-bold">1. Please select the category that best describes your role at BNU:</label>
                                <select class="form-control form-select" id="roleSelect" name="role" required>
                                    <option value="" disabled selected>Select an option</option>
                                    {{-- <option value="Student">Student</option> --}}
                                    <option value="Staff">Staff</option>
                                    <option value="Faculty Member">Faculty Member</option>
                                    <option value="Administrator">Administrator</option>
                                </select>
                            </div>

                            <!-- Q2 -->
                            <div class="form-group mb-3">
                                <label for="visitFrequency" class="form-label fw-bold">2. How often have you visited the Library?</label>
                                <select class="form-control form-select" id="visitFrequency" name="visitFrequency" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Never">Never</option>
                                </select>
                            </div>

                            <!-- Q3 -->
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">3. What are the reasons for your visit to the library? (Select all that apply)</label>
                                <div class="mt-2">
                                    <div class="checkbox-row">
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason1" name="reasons[]" value="To Study">
                                            <label class="custom-checkbox-label" for="reason1">To Study</label>
                                        </div>
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason2" name="reasons[]" value="To use computers">
                                            <label class="custom-checkbox-label" for="reason2">To use computers</label>
                                        </div>
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason3" name="reasons[]" value="To read the newspaper">
                                            <label class="custom-checkbox-label" for="reason3">To read the newspaper</label>
                                        </div>
                                    </div>
                                    <div class="checkbox-row">
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason4" name="reasons[]" value="To find a book">
                                            <label class="custom-checkbox-label" for="reason4">To find a book</label>
                                        </div>
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason5" name="reasons[]" value="To find a print journal">
                                            <label class="custom-checkbox-label" for="reason5">To find a print journal</label>
                                        </div>
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason6" name="reasons[]" value="To get help with assignments">
                                            <label class="custom-checkbox-label" for="reason6">To get help with assignments</label>
                                        </div>
                                    </div>
                                    <div class="checkbox-row">
                                        <div class="custom-checkbox">
                                            <input class="custom-checkbox-input" type="checkbox" id="reason7" name="reasons[]" value="To get help with research work">
                                            <label class="custom-checkbox-label" for="reason7">To get help with research work</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Q4 -->
                            <div class="form-group mb-3">
                                <label for="satisfiedSelect" class="form-label fw-bold">4. Are you satisfied with the current library opening hours?</label>
                                <select class="form-control form-select" id="satisfiedSelect" name="satisfaction" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <!-- Q5 -->
                            <div class="form-group mb-3">
                                <label for="examWeekExtend" class="form-label fw-bold">5. Do you want an extension in the library timing a week before the exam?</label>
                                <select class="form-control form-select" id="examWeekExtend" name="examWeekExtend" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <!-- Q6 -->
                            <div class="form-group mb-3">
                                <label for="regularExtension" class="form-label fw-bold">6. Do you want an extension in the regular Opening Hours?</label>
                                <select class="form-control form-select" id="regularExtension" name="regularExtension" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <!-- Q7 -->
                            <div class="form-group mb-3">
                                <label for="weekendExtension" class="form-label fw-bold">7. Do you want an extension in weekend hours?</label>
                                <select class="form-control form-select" id="weekendExtension" name="weekendExtension" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" name="enrollment_no" value="{{ strtoupper(auth()->user()->username) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- Submit Button -->
                            <div class="form-group d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2" id="submitLibraryButtonStandalone" 
                                        style="background-color: #2C3E50; border-color: #2C3E50; font-weight: 500; border-radius: 5px; transition: all 0.3s;">
                                    <i class="fa fa-paper-plane" style="margin-right: 8px;"></i> Submit Survey
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitLibrarySpinnerStandalone"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<!-- jQuery Slim CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Set up form submission
    $("#librarySurveyFormStandalone").on("submit", function(e) {
        e.preventDefault();
        
        // Validate form
        var isValid = e;
        
        // Check if at least one checkbox is selected
        if ($('input[name="reasons[]"]:checked').length === 0) {
            // Show error toast instead of alert
            showToast('error', 'Please select at least one reason for visiting the library');
            isValid = false;
        }
        
        // If form is not valid, stop here
        if (!isValid) {
            return false;
        }
        
        // Disable submit button and show spinner
        $("#submitLibraryButtonStandalone").prop("disabled", true);
        $("#submitLibrarySpinnerStandalone").removeClass("d-none");
        $("#submitLibraryButtonStandalone i.fa").addClass("d-none");
        
        // Get form data
        var formData = $(this).serialize();
        console.log("Form data being submitted:", formData);
        
        $.ajax({
            method: "POST",
            url: "{{ url('/submit-library-survey') }}",
            data: {
                role: $("#roleSelect").val(),
                visitFrequency: $("#visitFrequency").val(),
                reasons: $('input[name="reasons[]"]:checked').map(function() {return this.value;}).get(),
                satisfaction: $("#satisfiedSelect").val(),
                examWeekExtend: $("#examWeekExtend").val(),
                regularExtension: $("#regularExtension").val(),
                weekendExtension: $("#weekendExtension").val(),
                enrollment_no: "{{ strtoupper(auth()->user()->username) }}",
                _token: "{{ csrf_token() }}"
            },
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        }).done(function(res) {
            console.log("Survey submission successful:", res);
            
            // Show success toast instead of alert
            showToast('success', res.message || 'Survey submitted successfully');
            
            // Redirect to dashboard after a delay
            setTimeout(function() {
                window.location.href = "{{ url('/') }}";
            }, 3000);
        }).fail(function(err) {
            console.error("Error submitting library survey:", err);
            
            // Show error toast instead of alert
            showToast('error', 'Error submitting survey. Please try again.');
        }).always(function() {
            // Re-enable submit button and hide spinner
            $("#submitLibraryButtonStandalone").prop("disabled", false);
            $("#submitLibrarySpinnerStandalone").addClass("d-none");
            $("#submitLibraryButtonStandalone i.fa").removeClass("d-none");
        });
    });
    
    // Smooth transitions between form sections
    $(".form-group").css("transition", "opacity 0.3s ease");
    
    // Add hover effect to submit button
    $("#submitLibraryButtonStandalone").hover(
        function() {
            $(this).css({
                "background-color": "#1a252f",
                "transform": "translateY(-2px)"
            });
        },
        function() {
            $(this).css({
                "background-color": "#2C3E50",
                "transform": "translateY(0)"
            });
        }
    );
});
</script>

<style>
.bg-gradient-primary {
    background-color: #2C3E50;
    color: white;
}

.form-label.fw-bold {
    color: #2C3E50;
    font-weight: 600;
}

.checkbox-row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.custom-checkbox {
    display: flex;
    align-items: center;
    margin-right: 20px;
    margin-bottom: 10px;
    width: 31%;
}

.custom-checkbox-input {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    accent-color: #2C3E50;
}

.custom-checkbox-label {
    font-size: 15px;
    cursor: pointer;
}

.btn-primary {
    background-color: #2C3E50;
    border-color: #2C3E50;
}

.btn-primary:hover {
    background-color: #1a252f;
    border-color: #1a252f;
}

/* Enhanced form styling */
.form-control {
    border-radius: 5px;
    padding: 10px 15px;
    border: 1px solid #ddd;
    transition: all 0.3s;
}

/* Select input specific styling */
select.form-control, select.form-select {
    height: 50px !important;
    padding: 12px 15px !important;
    font-size: 16px !important;
    appearance: auto;
    background-size: 16px 12px;
    background-position: right 15px center;
}

.form-control:focus {
    border-color: #2C3E50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
}

.form-label.fw-bold {
    color: #2C3E50;
    font-weight: 600;
    margin-bottom: 8px;
}

/* Enhanced checkbox styling */
.custom-checkbox {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 8px 12px;
    transition: all 0.3s;
    border: 1px solid transparent;
}

.custom-checkbox:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/* Animation for alerts */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: slideIn 0.3s ease forwards;
}
</style>

@endsection 