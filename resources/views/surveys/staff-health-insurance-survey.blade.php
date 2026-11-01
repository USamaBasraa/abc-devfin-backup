@extends('layouts.app')

@section('content')

<style>
/* Disable sidebar interactions */
.app-sidebar, .app-sidebar * {
    pointer-events: none !important;
    cursor: default !important;
}

/* Make sidebar appear disabled */
.app-sidebar {
    opacity: 0.9;
}

.question-card {
    background: #f8f9fa;
    border-left: 4px solid #357694;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.question-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    font-size: 15px;
}

.required-asterisk {
    color: #dc3545;
    margin-left: 3px;
}
.bg-gradient-primary {
    background: yellow;
}

.form-control {
    border-radius: 5px;
    padding: 10px 15px;
    border: 1px solid #ddd;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #2C3E50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Disable all links in the sidebar
    var sidebarLinks = document.querySelectorAll('.app-sidebar a');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }, true);
        link.removeAttribute('href');
        link.style.pointerEvents = 'none';
    });
    
    var sidebarElements = document.querySelectorAll('.app-sidebar, .app-sidebar *');
    sidebarElements.forEach(function(element) {
        element.style.pointerEvents = 'none';
    });
});
</script>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="col-md-12 bg-gradient-primary" style="background-color: #357694; padding: 18px 0; color: white; box-shadow: 0 2px 5px ; border-radius: 10px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12  text-center">
                    <h1 style="font-weight: 600; letter-spacing: 0.5px;">Survey for Inclusion of Family Member in Health Insurance</h1>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Message Container -->
    <div id="success-message-container" class="d-none text-center my-5" style="padding: 50px;">
        <img src="assets/images/thank-you.png" id="success-image" alt="Success" style="max-width:500px; margin-bottom:20px;">
    </div>

    <!-- Survey Form Section -->
    <div class="my-4" id="survey-form-container">
        <div class="row mx-0">
             <div class="col-md-12">
                <div class="card shadow-lg border-0" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-body p-4">

                        <form id="staffHealthInsuranceForm" method="POST">
                            @csrf
                            
                            <!-- Date of Birth -->
                            <div class="question-card">
                                <label class="question-label">
                                   Please enter your date of Birth:
                                    <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" class="form-control datepicker" name="dob_emp" required placeholder="dd/mm/yyyy" style="max-width: 300px;" autocomplete="off">
                            </div>

                            <!-- Q1: Interest -->
                            <div class="question-card">
                                <label class="question-label">
                                    1- Are you interested in including your family members (spouse and children) in Health Insurance Policy? (Yes/No)
                                    <span class="required-asterisk">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_interested" id="interested_yes" value="Yes" required>
                                        <label class="form-check-label" for="interested_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_interested" id="interested_no" value="No">
                                        <label class="form-check-label" for="interested_no">No</label>
                                    </div>
                                </div>
                                <div id="not_interested_msg" class="text-danger mt-2 d-none" style="font-weight: bold;">
                                    <!-- If No, Form closes. -->
                                </div>
                            </div>

                            <!-- Conditional Section if YES -->
                            <div id="family_details_section" class="d-none">
                                <div class="question-card">
                                    <!-- <h5 class="mb-3" style="color: #357694; font-weight: 600;">3- If Yes,</h5> -->
                                    
                                    <div class="mb-3">
                                        <label class="question-label">2. Please add the date of Birth of your spouse:</label>
                                        <input type="text" class="form-control datepicker" name="dob_spouse" placeholder="dd/mm/yyyy" style="max-width: 300px;" autocomplete="off">
                                    </div>

                                    <div class="mb-3">
                                        <label class="question-label">3. Please add the number of your children:</label>
                                        <input type="number" class="form-control" name="no_of_children" id="no_of_children" min="0" max="9" style="max-width: 300px;">
                                    </div>

                                    <div id="children_dob_container">
                                        <!-- Dynamic children DOB fields will appear here -->
                                    </div>
                                </div>

                                <!-- Q4: Contribution -->
                                <div class="question-card">
                                    <label class="question-label">
                                        4- How much contribution will you be willing to pay to get your family members covered?
                                    </label>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contribution_amount" id="contrib_6000" value="6000">
                                            <label class="form-check-label" for="contrib_6000">Rs. 6,000/- per person per year</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contribution_amount" id="contrib_7000" value="7000">
                                            <label class="form-check-label" for="contrib_7000">Rs. 7,000/- per person per year</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contribution_amount" id="contrib_8000" value="8000">
                                            <label class="form-check-label" for="contrib_8000">Rs. 8,000/- per person per year</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contribution_amount" id="contrib_other" value="Other">
                                            <label class="form-check-label" for="contrib_other">Other</label>
                                        </div>
                                        <div id="other_contribution_container" class="mt-2 d-none">
                                            <label class="question-label">How much contribution would you like to contribute to cover your family?</label>
                                            <div class="input-group" style="max-width: 500px;">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rs.</span>
                                                </div>
                                                <input type="number" class="form-control" name="other_contribution_amount" id="other_contribution_amount" placeholder="Enter amount">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">/- per person per year</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Q5: Payment Frequency -->
                                <div class="question-card">
                                    <label class="question-label">
                                        5- What payment frequency will you be comfortable with to pay the contribution?
                                    </label>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_frequency" id="freq_monthly" value="Monthly">
                                            <label class="form-check-label" for="freq_monthly">Monthly deduction from salary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_frequency" id="freq_quarterly" value="Quarterly">
                                            <label class="form-check-label" for="freq_quarterly">Quarterly deduction from salary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_frequency" id="freq_biannual" value="Bi-annual">
                                            <label class="form-check-label" for="freq_biannual">Bi-annual deduction from salary</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- Submit Button -->
                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2" id="submitButton" 
                                        style="background-color: #357694; font-weight: 500; border-radius: 5px; transition: all 0.3s;">
                                    Submit
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSpinner"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- Include Toast Notifications Component --}}
@include('components.toast-notifications')

<script type="text/javascript">
$(document).ready(function() {
    // Generate ordinal number string (1st, 2nd, 3rd, etc.)
    function getOrdinal(n) {
        var s = ["th", "st", "nd", "rd"];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    }

    // Initialize datepicker for existing fields
    function initDatepickers() {
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0"
        });
    }

    initDatepickers();

    // Handle Number of Children Change
    $('#no_of_children').on('input change', function() {
        var count = parseInt($(this).val()) || 0;
        
        // Enforce max 9
        if (count > 9) {
            count = 9;
            $(this).val(9);
        }
        
        var container = $('#children_dob_container');
        
        // Don't rebuild if count is same (optimization if needed, but here we rebuild to keep it simple or simple diff)
        // Actually, simple rebuild is easier to ensure consistency, but resets values.
        // Let's try to preserve values if possible, or just rebuild for now as requirement is simple.
        
        // To preserve values we would need to check existing inputs. 
        // For this task, let's keep it simple: clear and rebuild or append/remove.
        // Rebuilding is safest to ensure order and correct labelling.
        
        // However, user might type 1, fill it, then type 2. We don't want to lose 1.
        // Let's iterate.
        
        var currentFields = container.find('.child-dob-group').length;
        
        if (count > currentFields) {
            // Add more
            for (var i = currentFields + 1; i <= count; i++) {
                var ordinal = getOrdinal(i);
                // Alphabetic label prefix: c, d, e, f... 
                // spouse is 'a', children count is 'b'. So first child is 'c'.
                // Alphabetic label prefix: A, B, C...
                var charCode = 65 + (i - 1); // 65 = 'A'
                var labelPrefix = String.fromCharCode(charCode) + ".";
                
                var html = `
                    <div class="mb-3 child-dob-group" id="child_div_${i}">
                        <div class="mb-2">
                            <label class="question-label">${labelPrefix} Please add the date of birth of your ${ordinal} child:</label>
                            <input type="text" class="form-control child-datepicker" name="dob_child_${i}" placeholder="dd/mm/yyyy" style="max-width: 300px;" autocomplete="off">
                        </div>
                        <div class="mb-2">
                            <label class="question-label">Please select gender of your ${ordinal} child:</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender_child_${i}" id="gender_child_${i}_male" value="Male">
                                    <label class="form-check-label" for="gender_child_${i}_male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender_child_${i}" id="gender_child_${i}_female" value="Female">
                                    <label class="form-check-label" for="gender_child_${i}_female">Female</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                `;
                container.append(html);
                
                // Initialize datepicker for the new field
                container.find(`input[name="dob_child_${i}"]`).datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100:+0"
                });
            }
        } else if (count < currentFields) {
            // Remove excess
            for (var i = currentFields; i > count; i--) {
                $('#child_div_' + i).remove();
            }
        }
        
    });

    // Handle Contribution Amount Change
    $('input[name="contribution_amount"]').change(function() {
        if ($(this).val() == 'Other') {
            $('#other_contribution_container').removeClass('d-none');
            $('#other_contribution_amount').prop('required', true);
        } else {
            $('#other_contribution_container').addClass('d-none');
            $('#other_contribution_amount').prop('required', false);
        }
    });

    // Handle Interest Radio
    $('input[name="is_interested"]').change(function() {
        if ($(this).val() == 'Yes') {
            $('#family_details_section').removeClass('d-none');
            $('#not_interested_msg').addClass('d-none');
            // Add required attribute dynamically
            $('input[name="contribution_amount"]').prop('required', true);
            $('input[name="payment_frequency"]').prop('required', true);
            // Spouse DOB usually required if Yes?
        } else {
            $('#family_details_section').addClass('d-none');
            $('#not_interested_msg').removeClass('d-none');
            // Remove required
            $('input[name="contribution_amount"]').prop('required', false);
            $('input[name="payment_frequency"]').prop('required', false);
        }
    });

    // Form submission
    $("#staffHealthInsuranceForm").on("submit", function(e) {
        e.preventDefault();
        
        var isInterested = $('input[name="is_interested"]:checked').val();
        if (!isInterested && isInterested !== 'No') {
            showToast('error', 'Please select if you are interested or not.');
            return false;
        }

        // Disable submit button and show spinner
        $("#submitButton").prop("disabled", true);
        $("#submitSpinner").removeClass("d-none");
        
        // Get form data
        var formData = $(this).serialize();

        $.ajax({
            method: "POST",
            url: "{{ route('submit-staff-health-insurance-survey') }}",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        }).done(function(res) {
            showToast('success', res.message || 'Submitted successfully!', '', 1000);

            setTimeout(function() {
                $('.toast').remove();
                $("#survey-form-container").addClass('d-none');
                $("#success-message-container").removeClass('d-none');
                
                // Redirect to dashboard
                setTimeout(function() {
                    window.location.href = "{{ url('/') }}";
                }, 2000);
            }, 1000);
        }).fail(function(err) {
            console.error("Error submitting:", err);
            const errorMessage = err.responseJSON?.message || "Error submitting survey. Please try again.";
            showToast('error', errorMessage);
             $("#submitButton").prop("disabled", false);
             $("#submitSpinner").addClass("d-none");
        });
    });
});
</script>
@endsection
