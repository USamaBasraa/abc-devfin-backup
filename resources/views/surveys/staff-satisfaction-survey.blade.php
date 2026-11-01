@extends('layouts.app')

@section('content')

<style>
/* Disable sidebar interactions */
/* Disable sidebar interactions */
.app-sidebar, .app-sidebar * {
    pointer-events: none !important;
    cursor: default !important;
}

/* Make sidebar appear disabled */
.app-sidebar {
    opacity: 0.9;
}

/* Star rating styles */
.star-rating {
    display: inline-flex;
    gap: 15px;
    align-items: center;
}

.star-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.2s;
}

.star-option:hover {
    transform: scale(1.1);
}

.star-option input[type="radio"] {
    display: none;
}

.star-icon {
    font-size: 32px;
    color: #ddd;
    transition: color 0.3s;
}

.star-option input[type="radio"]:checked ~ .star-icon {
    color: #2C3E50;
}

.star-option:hover .star-icon {
    color: #4CA1AF;
}

.star-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
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

</style>

<script>
// Additional script to ensure sidebar links are disabled
document.addEventListener('DOMContentLoaded', function() {
    // Disable all links in the sidebar
    var sidebarLinks = document.querySelectorAll('.app-sidebar a');
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
    var sidebarElements = document.querySelectorAll('.app-sidebar, .app-sidebar *');
    sidebarElements.forEach(function(element) {
        element.style.pointerEvents = 'none';
    });
});
</script>
<?php
 if($record->total <= 0 ){ ?>
<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="bg-gradient-primary" style="background-color: #357694; padding: 18px 0; color: white; box-shadow: 0 2px 5px ; border-radius: 10px;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <h1 style="font-weight: 600; letter-spacing: 0.5px;">BNU Employee Satisfaction Survey</h1>
                    <!-- <p class="mt-2 mb-0">Your feedback helps us improve our services</p> -->
                    <!-- <p class="mt-2 mb-0">January 2026</p> -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Message Container (Hidden by default) -->
    <div id="success-message-container" class="d-none text-center my-5" style="padding: 50px;">
        <img src="assets/images/thank-you.png" id="success-image" alt="Success" style="max-width:500px; margin-bottom:20px;">
        <!-- <h3 class="mt-3" style="color: #2C3E50; font-weight: 600;">Submitted Successfully!</h3>
        <p class="text-muted">Thank you for your feedback.</p> -->
    </div>

    <!-- Survey Form Section -->
    <div class="my-4" id="survey-form-container">
        <div class="row mx-0">
             <div class="col-md-12">
                <div class="card shadow-lg border-0" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <!-- <p style="font-size: 16px; line-height: 1.6;"><strong>Your voice matters.</strong></p> -->
                            <!-- <p style="font-size: 16px; line-height: 1.6;">In no more than <strong>5 minutes</strong>, share your experience and help shape a better campus for everyone. This short survey covers academics, co-curricular life, cafeteria services, and administration. Your feedback directly informs meaningful improvements.</p> -->
                          
                            <!-- <p style="font-size: 16px; line-height: 1.6;"><strong>Make your voice count!</strong></p> -->
                        </div>

                        <form id="staffSatisfactionSurveyForm" method="POST">
                            @csrf
                            
                            <!-- Campus Experience Rating -->
                            <div class="mb-4">
                                <!-- <h5 class="fw-bold" style="color: #2C3E50;">Campus Experience Rating</h5> -->
                                <p style="font-size: 16px; line-height: 1.6;"><p>(Scale: 1: Extremely Dissatisfied, 2: Dissatisfied, 3: Neutral, 4: Satisfied, 5: Extremely Satisfied)</p><p style="font-size: 14px; color: #666; margin-bottom: 25px;">Please indicate your level of satisfaction by selecting the appropriate number of stars.</p>
                            </div>

                            <!-- Q1: Teaching Quality -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        Satisfaction with the overall culture of the university 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="culture_satisfaction" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Q2: Co-curricular Activities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                    	Satisfaction with the support for innovation 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="innovation_support" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Q3: Cafeteria Services -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                       	Satisfaction with autonomy in choosing professional approaches for performing your duty 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="professional_autonomy" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Q4: Administrative Support -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                       	Satisfaction with transparency in academic and administrative decision-making 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="decision_transparency" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Q5: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with communication from university leadership 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="leadership_communication" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q6: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with opportunities to provide input in academic matters
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="academic_input" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q7: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with workload 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="workload_satisfaction" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q8: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with workload assigned in addition to core duties
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="additional_workload" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q9: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with opportunities for professional development and training 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="professional_development" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q10: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with salary structure
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="salary_structure" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q11: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with compensation relative to workload
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="compensation_workload" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q12: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with quality of workplace, classrooms, and other facilities 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="workplace_quality" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q13: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with IT and technical support for teaching and research 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="it_support" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q14: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with responsiveness of other offices
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="office_responsiveness" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q15: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with overall campus environment and facilities
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="campus_environment" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q16: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Overall satisfaction with working at the university
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="overall_satisfaction" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q17: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Satisfaction with sense of belonging to the institution
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="belonging_sense" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <!-- Q18: Hostel Facilities -->
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="question-label mb-0" style="flex: 1; padding-right: 20px;">
                                        	Likelihood of recommending the university as a workplace to others 
                                        <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="star-option">
                                            <input type="radio" name="recommend_likelihood" value="{{ $i }}">
                                            <span class="star-icon">★</span>
                                            <span class="star-label">{{ $i }}</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Comments & Suggestions -->
                            <!-- <div class="mb-4 mt-5">
                                <h5 class="fw-bold" style="color: #2C3E50;">Open-Ended (Optional)</h5> -->
                                <!-- <p style="font-size: 14px; color: #666; margin-bottom: 25px; font-style: italic;">Share any additional feedback, suggestions, or ideas to help us improve.</p> -->
                            <!-- </div> --> 

                            <!-- Q19.: Key Strengths -->
                            <div class="question-card">
                                <label class="question-label">
                                    		What are the university’s key strengths? 
                                    <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control char-counter-input" name="key_strengths" rows="5" placeholder="Short answer text" maxlength="1500" data-counter="#key_strengths_count"></textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Maximum 1500 characters</small>
                                    <small class="text-muted" id="key_strengths_count">0/1500</small>
                                </div>
                            </div>

                            <!-- Q20: Improvement Areas -->
                            <div class="question-card">
                                <label class="question-label">
                                    		What areas need the most improvement to enhance your satisfaction with the job? 
                                    <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control char-counter-input" name="improvement_areas" rows="5" placeholder="Short answer text" maxlength="1500" data-counter="#improvement_areas_count"></textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Maximum 1500 characters</small>
                                    <small class="text-muted" id="improvement_areas_count">0/1500</small>
                                </div>
                            </div>

                            

 
                            <!-- Hidden fields -->
                            <input type="hidden" name="enrollment_no" value="{{ strtoupper(auth()->user()->username) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- Submit Button -->
                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2" id="submitSatisfactionButton" 
                                        style="background-color: #357694; font-weight: 500; border-radius: 5px; transition: all 0.3s;">
                                    Submit Survey
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSatisfactionSpinner"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }else { ?>
    <div style="text-align:center;">
        <img src="assets/images/thank-you.png" alt="Thank You" style="max-width:500px; margin-bottom:50px; margin-top:100px;">
        <!-- <p>Your Feedback Form Has Been Submitted Successfully.</p> -->
    </div>

    <?php } ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- Include Toast Notifications Component --}}
@include('components.toast-notifications')

<script type="text/javascript">
$(document).ready(function() {
    // Star rating interaction
    $('.star-option').on('click', function() {
        const $this = $(this);
        const $input = $this.find('input[type="radio"]');
        $input.prop('checked', true);
        
        // Update all stars in this group
        const groupName = $input.attr('name');
        const value = parseInt($input.val());
        
        $(`input[name="${groupName}"]`).each(function(index) {
            const $star = $(this).siblings('.star-icon');
            if (index < value) {
                $star.css('color', '#2C3E50');
            } else {
                $star.css('color', '#ddd');
            }
        });
    });

    // Form submission
    $("#staffSatisfactionSurveyForm").on("submit", function(e) {
        e.preventDefault();
        
        // Validate all required fields
        const requiredFields = [
            { name: 'culture_satisfaction', label: 'Satisfaction with university culture' },
            { name: 'innovation_support', label: 'Satisfaction with innovation support' },
            { name: 'professional_autonomy', label: 'Satisfaction with professional autonomy' },
            { name: 'decision_transparency', label: 'Satisfaction with decision-making transparency' },
            { name: 'leadership_communication', label: 'Satisfaction with leadership communication' },
            { name: 'academic_input', label: 'Satisfaction with academic input opportunities' },
            { name: 'workload_satisfaction', label: 'Satisfaction with workload' },
            { name: 'additional_workload', label: 'Satisfaction with additional workload' },
            { name: 'professional_development', label: 'Satisfaction with professional development' },
            { name: 'salary_structure', label: 'Satisfaction with salary structure' },
            { name: 'compensation_workload', label: 'Satisfaction with compensation vs workload' },
            { name: 'workplace_quality', label: 'Satisfaction with workplace quality' },
            { name: 'it_support', label: 'Satisfaction with IT support' },
            { name: 'office_responsiveness', label: 'Satisfaction with other offices responsiveness' },
            { name: 'campus_environment', label: 'Satisfaction with campus environment' },
            { name: 'overall_satisfaction', label: 'Overall satisfaction' },
            { name: 'belonging_sense', label: 'Sense of belonging' },
            { name: 'recommend_likelihood', label: 'Likelihood to recommend' }
        ];
        
        // Check star ratings
        for (let field of requiredFields) {
            if (!$(`input[name="${field.name}"]:checked`).length) {
                showToast('error', `Please provide: ${field.label}`);
                return false;
            }
        }
        
        // Check text fields
        const keyStrengths = $('textarea[name="key_strengths"]').val().trim();
        if (!keyStrengths) {
            showToast('error', 'Please share the university’s key strengths');
            return false;
        }
        
        const improvementAreas = $('textarea[name="improvement_areas"]').val().trim();
        if (!improvementAreas) {
            showToast('error', 'Please suggest areas for improvement');
            return false;
        }
        
        // Disable submit button and show spinner
        $("#submitSatisfactionButton").prop("disabled", true);
        $("#submitSatisfactionSpinner").removeClass("d-none");
        
        // Get form data
        var formData = {
            culture_satisfaction: $('input[name="culture_satisfaction"]:checked').val(),
            innovation_support: $('input[name="innovation_support"]:checked').val(),
            professional_autonomy: $('input[name="professional_autonomy"]:checked').val(),
            decision_transparency: $('input[name="decision_transparency"]:checked').val(),
            leadership_communication: $('input[name="leadership_communication"]:checked').val(),
            academic_input: $('input[name="academic_input"]:checked').val(),
            workload_satisfaction: $('input[name="workload_satisfaction"]:checked').val(),
            additional_workload: $('input[name="additional_workload"]:checked').val(),
            professional_development: $('input[name="professional_development"]:checked').val(),
            salary_structure: $('input[name="salary_structure"]:checked').val(),
            compensation_workload: $('input[name="compensation_workload"]:checked').val(),
            workplace_quality: $('input[name="workplace_quality"]:checked').val(),
            it_support: $('input[name="it_support"]:checked').val(),
            office_responsiveness: $('input[name="office_responsiveness"]:checked').val(),
            campus_environment: $('input[name="campus_environment"]:checked').val(),
            overall_satisfaction: $('input[name="overall_satisfaction"]:checked').val(),
            belonging_sense: $('input[name="belonging_sense"]:checked').val(),
            recommend_likelihood: $('input[name="recommend_likelihood"]:checked').val(),
            key_strengths: $('textarea[name="key_strengths"]').val(),
            improvement_areas: $('textarea[name="improvement_areas"]').val(),
            enrollment_no: "{{ strtoupper(auth()->user()->username) }}",
            _token: "{{ csrf_token() }}"
        };
        
        console.log("Form data being submitted:", formData);
        
        $.ajax({
            method: "POST",
            url: "{{ url('/submit-staff-satisfaction-survey') }}",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        }).done(function(res) {
            console.log("Survey submission successful:", res);
             // Show success toast
            showToast('success', res.message || 'Survey submitted successfully!', '', 1000);

            // Delay hiding the form to let the user see the success message
            setTimeout(function() {
                // Force remove any lingering toasts
                $('.toast').remove();
                
                // Hide form and show success message
                $("#survey-form-container").addClass('d-none');
                $("#success-message-container").removeClass('d-none');
                
                // Redirect to dashboard after showing the thank you image
                setTimeout(function() {
                    window.location.href = "{{ url('/') }}";
                }, 2000); // Wait 2 seconds showing the thank you image
            }, 1500); // Wait 1.5 seconds showing the form with success toast
        }).fail(function(err) {
            console.error("Error submitting satisfaction survey:", err);
            // Show error toast
            const errorMessage = err.responseJSON?.message || "Error submitting survey. Please try again.";
            showToast('error', errorMessage);
             // Re-enable submit button and hide spinner on failure
             $("#submitSatisfactionButton").prop("disabled", false);
             $("#submitSatisfactionSpinner").addClass("d-none");
        });
    });
    
    // Add hover effect to submit button
    $("#submitSatisfactionButton").hover(
        function() {
            $(this).css("transform", "translateY(-2px)");
        },
        function() {
            $(this).css("transform", "translateY(0)");
        }
    );

    // Character Counter
    $('.char-counter-input').on('input', function() {
        var currentLength = $(this).val().length;
        var maxLength = $(this).attr('maxlength');
        var counterId = $(this).data('counter');
        $(counterId).text(currentLength + '/' + maxLength);
    });
});
</script>

<style>
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

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}
</style>

@endsection
