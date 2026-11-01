@extends('layouts.app')


@section('content')

<!-- Add CSRF token meta tag directly in this view for AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include Toast Notifications Component -->
@include('components.toast-notifications')

<!-- Form Header -->
<div class="card mb-4 shadow-sm" style="position: sticky; top: 0; z-index: 1000; background: white;">
    <div class="card-header" style="background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%); border-bottom: 1px solid #1565c0;">
        <h4 class="mb-0" style="color: #ffffff; font-weight: 600;"><i class="fa fa-file-text me-2 mr-2" style="color: #ffffff;"></i>Job Description Form</h4>
    </div>
</div>

<!-- Fixed Circular Progress Bar -->
<div class="fixed-progress-circle">
    <div class="progress-circle">
        <div class="progress-circle-inner">
            <div class="progress-percentage" id="progress-percentage">0%</div>
            <div class="progress-label">Complete</div>
        </div>
        <svg class="progress-ring" width="120" height="120">
            <circle class="progress-ring-bg" cx="60" cy="60" r="54" stroke-width="8"></circle>
            <circle class="progress-ring-fill" cx="60" cy="60" r="54" stroke-width="8" stroke-dasharray="339.292" stroke-dashoffset="339.292"></circle>
        </svg>
    </div>
</div>



<div class="card shadow-lg border-0" style="border-radius: 10px; overflow: hidden;">
    <div class="card-body p-4">

    <form id="job-description-form" method="POST">
        @csrf
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
         <!-- Section A: Basic Information -->
         <div class="card mb-4 shadow-sm" id="section-a">
             <div class="card-header bg-light">
                 <h4 class="mb-0">Basic Information (A)</h4>
             </div>
             <div class="card-body">
                 <div class="row mb-3">
                     <div class="col-md-6">
                         <label class="form-label" for="job_title">Job Title <span class="text-danger">*</span></label>
                         <input type="text" name="job_title" id="job_title" class="form-control required-field" maxlength="150" title="Please enter a valid job title" aria-describedby="job_title_help" placeholder="Enter your job title">
                         <div id="job_title_help" class="form-text">Enter your current job title</div>
                         <div class="invalid-feedback">Please enter a valid job title</div>
                         <small class="text-muted char-count-info">Characters: 0/150</small>
                     </div>
                  
                      <div class="col-md-6" style="display: none;">
                         <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                         <input type="text" name="employee_code" class="form-control" value="{{ auth()->user()->employee_id }}" maxlength="150" readonly title="Please enter only numbers.">
                         <div class="invalid-feedback">Please enter only numbers.</div>
                     </div>
                     
                     <div class="col-md-6">
                         <label class="form-label">HRIS ID <span class="text-danger">*</span></label>
                         <input type="text" name="hris_id" class="form-control required-field" pattern="[0-9]*" maxlength="150" onkeypress="return event.charCode >= 48 && event.charCode <= 57" oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="Please enter a valid HRIS ID (numbers only)." placeholder="Enter your HRIS ID">
                         <div class="invalid-feedback">Please enter a valid HRIS ID (numbers only).</div>
                         <small class="text-muted">HRIS ID written on your employee card</small>
                     </div>
                    
                 </div>
                 <div class="row mb-3">
                     
                     <div class="col-md-6">
                         <label class="form-label">Name of Incumbent <span class="text-danger">*</span></label>
                         <input type="text" name="incumbent_name" value="" class="form-control required-field" maxlength="150" placeholder="Enter your full name">
                         <div class="invalid-feedback">Please enter a valid name</div>
                         <small class="text-muted char-count-info">Characters: 0/150</small>
                     </div>
                     <div class="col-md-6">
                         <label class="form-label">Supervisor's Name <span class="text-danger">*</span></label>
                         <input type="text" name="supervisor_name" class="form-control required-field" maxlength="150" placeholder="Enter your supervisor's name">
                         <div class="invalid-feedback">Please enter a valid supervisor name</div>
                         <small class="text-muted char-count-info">Characters: 0/150</small>
                     </div>
                 </div>
                 <div class="row mb-3">
                     <div class="col-md-6">
                         <label class="form-label">School (if applicable)</label>
                         <input type="text" name="school" value="{{ auth()->user()->school }}" class="form-control" maxlength="150">
                         <small class="text-muted char-count-info">Characters: 0/150</small>
                     </div>
                     <div class="col-md-6">
                         <label class="form-label">Department (if applicable)</label>
                         <input type="text" name="department" value="{{ auth()->user()->department }}" class="form-control" maxlength="150">
                         <small class="text-muted char-count-info">Characters: 0/150</small>
                     </div>
                     
                 </div>
                 <div class="row mb-3">
                     <div class="col-md-6">
                         <label class="form-label">Job Status</label><br>
                         <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="job_status" checked  value="Full-time">
                             <label class="form-check-label">Full-time</label>
                         </div>
                         <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="job_status" value="Part-time">
                             <label class="form-check-label">Part-time</label>
                         </div>
                     </div>
                 </div>
                 
                 <!-- Save Section A Button at Bottom -->
                 <div class="text-end mt-3">
                     <button type="button" class="btn btn-success save-section" data-section="a">
                         <i class="fa fa-save"></i> Save Section A
                     </button>
                 </div>
             </div>
         </div>

         <!-- Section B: Role Description -->
         <div class="card mb-4 shadow-sm" id="section-b">
             <div class="card-header bg-light">
                 <h4 class="mb-0">Brief Description of Your Role (B)</h4>
             </div>
             <div class="card-body">
                 <div class="form-group">
                     <label class="form-label">(Please give a brief description of your current role highlighting broader areas of your work) <span class="text-danger">*</span>:</label>
                     <textarea name="role_description" class="form-control char-count-input" data-max-chars="3000" rows="8" id="role-description"></textarea>
                     <div class="invalid-feedback">Please provide a role description (maximum 3000 characters)</div>
                     <small class="text-muted char-count-info">Characters: 0/3000</small>
                 </div>
                 
                 <!-- Save Section B Button at Bottom -->
                 <div class="text-end mt-3">
                     <button type="button" class="btn btn-success save-section" data-section="b">
                         <i class="fa fa-save"></i> Save Section B
                     </button>
                 </div>
             </div>
         </div>

        <!-- Section C: Major Responsibilities of Your Role -->
        <div class="card mb-4 shadow-sm" id="section-c">
            <div class="card-header bg-light">
                <h4 class="mb-0">Major Responsibilities of Your Role (C)</h4>
            </div>
            <div class="card-body">
                <p>(List in order of importance the major responsibilities of the Job, and the % of time spent on that particular responsibility)<br>
                Please enlist at least 7 responsibilities. Maximum 3000 characters per responsibility.</p>

                <table class="table table-bordered" id="responsibilities-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70%;">Responsibilities <span class="text-danger">*</span></th>
                            <th style="width: 25%;">% of your time <span class="text-danger">*</span></th>
                            <th style="width: 5%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 7; $i++)
                            <tr>
                                <td>
                                    <input type="text" name="responsibilities[]" class="form-control responsibility-input char-count-input" data-max-chars="3000" placeholder="Enter responsibility">
                                    <div class="invalid-feedback">This responsibility is required</div>
                                    <small class="text-muted char-count-info">Characters: 0/3000</small>
                                </td>
                                <td>
                                    <input type="number" name="responsibility_percentage[]" class="form-control percentage-input" placeholder="e.g. 20" min="1" max="100" step="1">
                                    <div class="invalid-feedback">Please enter a valid percentage (1-100)</div>
                                </td>
                                <td class="action-cell text-center">
                                    @if ($i === 6)
                                        <button type="button" class="btn btn-sm btn-outline-success add-responsibility"><i class="fa fa-plus"></i></button>
                                    @else
                                        <!-- No button for rows 1-6 -->
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end"><strong>Total Percentage:</strong></td>
                            <td><span id="total-percentage">0</span>%</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="invalid-feedback" id="responsibilities-error"></div>
                <div class="alert alert-danger d-none" id="percentage-error">Total percentage must equal 100%</div>
                

                

                
                <!-- Save Section C Button at Bottom -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-success save-section" data-section="c">
                        <i class="fa fa-save"></i> Save Section C
                    </button>
                </div>
            </div>
        </div>

        <!-- Section D: Job Specifications -->
        <div class="card mb-4 shadow-sm" id="section-d">
            <div class="card-header bg-light">
                <h4 class="mb-0">Job Specifications (D)</h4>
            </div>
            <div class="card-body">
                <p>Please provide the details of your qualification (highest/latest) and total experience. Maximum 3000 characters per entry.</p>

                <table class="table table-bordered" id="job-specifications-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Qualification (Highest):</th>
                            <th style="width: 45%;">Total Experience: (No. of years)</th>
                            <th style="width: 10%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="qualification[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter qualification">
                                <div class="invalid-feedback">This field is required</div>
                                <small class="text-muted char-count-info">Characters: 0/3000</small>
                            </td>
                            <td>
                                <input type="number" name="experience[]" class="form-control" placeholder="Enter experience" min="0" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" pattern="[0-9]*">
                                <div class="invalid-feedback">Please enter a valid experience in years (positive numbers only)</div>
                            </td>
                            <td class="action-cell text-center">
                                <button type="button" class="btn btn-sm btn-success add-spec"><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Save Section D Button at Bottom -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-success save-section" data-section="d">
                        <i class="fa fa-save"></i> Save Section D
                    </button>
                </div>
            </div>
        </div>

        <!-- Section E: Areas of Multidisciplinary Interests (E) -->
        <div class="card mb-4 shadow-sm" id="section-e">
            <div class="card-header bg-light">
                <h4 class="mb-0">Areas of Multidisciplinary Interests (E)</h4>
            </div>
            <div class="card-body">
                <p>Please list the areas of multidisciplinary interest in research, projects, product development, business/startup idea for which you need the expertise/skills of other colleagues at BNU for collaboration. Maximum 3000 characters per entry.</p>

                <table class="table table-bordered" id="multi-interest-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Area of Multidisciplinary Interest</th>
                            <th style="width: 45%;">Required Expertise/Knowledge/Skills you are looking for</th>
                            <th style="width: 10%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="multi_interest[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter area of interest">
                                <div class="invalid-feedback">This field is required</div>
                                <small class="text-muted char-count-info">Characters: 0/3000</small>
                            </td>
                            <td>
                                <input type="text" name="required_skills[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter required expertise">
                                <div class="invalid-feedback">This field is required</div>
                                <small class="text-muted char-count-info">Characters: 0/3000</small>
                            </td>
                            <td class="action-cell text-center">
                                <button type="button" class="btn btn-sm btn-success add-spec"><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Save Section E Button at Bottom -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-success save-section" data-section="e">
                        <i class="fa fa-check-circle"></i> Final Submit
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Removed final submit button since we have "Final Submit" in Section E -->
    </form>
</div>


</div>

@endsection

<style>
/* Section indicator styles removed - now using circular progress bar */

/* Required field styling */
.required-field {
    border-left: 3px solid #dc3545;
}

.required-field:focus {
    border-left: 3px solid #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.required-field:valid {
    border-left: 3px solid #28a745;
}

@keyframes gentle-pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.02); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

.card.disabled {
    opacity: 0.6;
    pointer-events: none;
}

.card.section-saved {
    background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e3f2fd;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    opacity: 0.9;
    cursor: not-allowed;
    position: relative;
}

.card.section-saved::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1565c0 0%, #1976d2 100%);
    border-radius: 4px 4px 0 0;
}

.card.section-saved .card-header {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
    border-bottom: 1px solid #1565c0;
    color: #1565c0 !important;
}

.card.section-saved .card-header h4 {
    color: #1565c0 !important;
    font-weight: 600;
}

.card.section-saved input:disabled,
.card.section-saved textarea:disabled,
.card.section-saved select:disabled {
    background-color: #fafafa;
    color: #757575;
    cursor: not-allowed;
    border: 1px solid #e0e0e0;
}

.card.section-saved input[type="radio"]:disabled,
.card.section-saved input[type="checkbox"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.card.section-saved input[type="radio"]:disabled + label,
.card.section-saved input[type="checkbox"]:disabled + label {
    color: #757575;
    cursor: not-allowed;
}

.card.section-saved input[readonly],
.card.section-saved textarea[readonly] {
    background-color: #fafafa !important;
    color: #757575 !important;
    cursor: not-allowed !important;
    border: 1px solid #e0e0e0 !important;
    opacity: 0.8;
}

.card.section-saved .form-label {
    color: #616161;
    font-weight: 500;
}

.card.section-saved .text-muted {
    color: #9e9e9e !important;
}

.card.section-saved .table {
    background-color: #fafafa;
    border-radius: 8px;
    overflow: hidden;
}

.card.section-saved .table th {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
    color: #1565c0 !important;
    font-weight: 600;
    border-bottom: 2px solid #1565c0;
}

.card.section-saved .btn:disabled {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border: 2px solid #1565c0;
    color: #1565c0;
    cursor: not-allowed;
    font-weight: 600;
}

/* Sticky Progress Bar Enhancements */
.sticky-progress {
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.sticky-progress.scrolled {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-bottom: 1px solid #e0e0e0;
}

/* Mobile Responsiveness Improvements */
@media (max-width: 768px) {
    .section-indicator {
        padding: 15px 10px;
        margin-bottom: 15px;
        min-height: 100px;
    }
    
    .section-icon {
        font-size: 36px;
    }
    
    .section-text strong {
        font-size: 14px;
    }
    
    .section-text small {
        font-size: 11px;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table-responsive {
        font-size: 14px;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .progress {
        height: 25px;
    }
    
    .progress-bar {
        font-size: 12px;
    }
}

/* Accessibility Improvements */
.form-control:focus {
    border-color: #1565c0;
    box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .card.section-saved {
        border: 2px solid #000000;
    }
    
    .text-muted {
        color: #000000 !important;
    }
}

.card.section-saved .add-responsibility,
.card.section-saved .add-spec,
.card.section-saved .remove-responsibility,
.card.section-saved .remove-spec,
.card.section-saved .remove-interest {
    display: none !important;
}

/* Fixed Circular Progress Bar */
.fixed-progress-circle {
    position: fixed;
    top: 100px;
    right: 30px;
    z-index: 1001;
    background: white;
    border-radius: 50%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: 3px solid #e9ecef;
    transition: all 0.3s ease;
}

.fixed-progress-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-circle-inner {
    position: absolute;
    text-align: center;
    z-index: 2;
}

.progress-percentage {
    font-size: 24px;
    font-weight: 700;
    color: #28a745;
    line-height: 1;
    margin-bottom: 2px;
}

.progress-label {
    font-size: 10px;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-ring {
    position: absolute;
    top: 0;
    left: 0;
    transform: rotate(-90deg);
}

.progress-ring-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.progress-ring-fill {
    fill: none;
    stroke: #28a745;
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
    filter: drop-shadow(0 2px 4px rgba(40, 167, 69, 0.3));
}

/* Progress color states */
.progress-ring-fill.progress-0 {
    stroke: #6c757d;
}

.progress-ring-fill.progress-25 {
    stroke: #dc3545;
}

.progress-ring-fill.progress-50 {
    stroke: #ffc107;
}

.progress-ring-fill.progress-75 {
    stroke: #17a2b8;
}

.progress-ring-fill.progress-100 {
    stroke: #28a745;
}

/* Mobile responsiveness for circular progress */
@media (max-width: 768px) {
    .fixed-progress-circle {
        top: 80px;
        right: 15px;
        transform: scale(0.8);
    }
    
    .fixed-progress-circle:hover {
        transform: scale(0.85);
    }
    
    .progress-percentage {
        font-size: 20px;
    }
    
    .progress-label {
        font-size: 9px;
    }
}

.card.section-saved .btn-secondary {
    background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%) !important;
    border: 2px solid #0d47a1 !important;
    color: #ffffff !important;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(21, 101, 192, 0.3);
}

.save-section {
    transition: all 0.3s ease;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 8px;
}

.save-section.saving {
    background: linear-gradient(135deg, #fef7e0 0%, #fef3c7 100%);
    border-color: #f59e0b;
    color: #92400e;
}

.save-section.saved {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-color: #10b981;
    color: #065f46;
}

.save-section:disabled {
    opacity: 0.8;
    cursor: not-allowed;
    background: transparent;
    border-color: #28a745;
    color: #28a745;
}

.save-section:disabled:hover {
    background: transparent;
    border-color: #28a745;
    color: #28a745;
    transform: none;
    box-shadow: none;
}

/* Progress bar enhancements */
.progress {
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}

.progress-bar {
    font-weight: 500;
    text-shadow: none;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    
    $(document).ready(function() {
        // Debug message to confirm jQuery is loaded
        console.log("jQuery version:", $.fn.jquery);

        // Initialize section status
        const sectionStatus = @json($sectionStatus ?? []);
        let currentSection = 'a';
        
        // Initialize circular progress and section indicators
        function initializeProgress() {
            updateProgress();
        }
        
        // Update circular progress bar
        function updateProgress() {
            let completedSections = 0;
            Object.values(sectionStatus).forEach(status => {
                if (status) completedSections++;
            });
            
            const progressPercentage = Math.round((completedSections / 5) * 100);
            
            // Update circular progress bar
            const progressPercentageElement = $('#progress-percentage');
            const progressRingFill = $('.progress-ring-fill');
            
            progressPercentageElement.text(progressPercentage + '%');
            
            // Calculate stroke-dashoffset for circular progress
            const circumference = 2 * Math.PI * 54; // r = 54
            const offset = circumference - (progressPercentage / 100) * circumference;
            progressRingFill.css('stroke-dashoffset', offset);
            
            // Update progress ring color based on percentage
            progressRingFill.removeClass('progress-0 progress-25 progress-50 progress-75 progress-100');
            
            if (progressPercentage === 0) {
                progressRingFill.addClass('progress-0');
                progressPercentageElement.css('color', '#6c757d');
            } else if (progressPercentage <= 25) {
                progressRingFill.addClass('progress-25');
                progressPercentageElement.css('color', '#dc3545');
            } else if (progressPercentage <= 50) {
                progressRingFill.addClass('progress-50');
                progressPercentageElement.css('color', '#ffc107');
            } else if (progressPercentage <= 75) {
                progressRingFill.addClass('progress-75');
                progressPercentageElement.css('color', '#17a2b8');
            } else {
                progressRingFill.addClass('progress-100');
                progressPercentageElement.css('color', '#28a745');
            }
            
            // Enable/disable sections
            updateSectionAccess();
            

        }
        
        // Update section access based on completion
        function updateSectionAccess() {
            let previousCompleted = true;
            
            ['a', 'b', 'c', 'd', 'e'].forEach(section => { // Added 'b' back to the array
                const sectionKey = `section_${section}`;
                const sectionCard = $(`#section-${section}`);
                const saveButton = $(`.save-section[data-section="${section}"]`);
                
                if (previousCompleted) {
                    sectionCard.removeClass('disabled');
                } else {
                    sectionCard.addClass('disabled');
                }
                
                // Update save button status based on completion
                if (sectionStatus[sectionKey]) {
                    // Section is completed - disable entire section and show "Saved"
                    sectionCard.addClass('section-saved');
                    
                    // Disable ALL form elements comprehensively
                    sectionCard.find('input[type="text"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="number"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="email"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="password"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="tel"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="url"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="date"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="time"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="datetime-local"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="month"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="week"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="search"]').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('input[type="radio"]').prop('disabled', true);
                    sectionCard.find('input[type="checkbox"]').prop('disabled', true);
                    sectionCard.find('textarea').prop('disabled', true).prop('readonly', true);
                    sectionCard.find('select').prop('disabled', true);
                    sectionCard.find('option').prop('disabled', true);
                    
                    // Disable all action buttons
                    sectionCard.find('.add-responsibility, .add-spec, .remove-responsibility, .remove-spec, .remove-interest').prop('disabled', true);
                    
                    saveButton.prop('disabled', true)
                              .removeClass('btn-success')
                              .addClass('btn-secondary')
                              .html('<i class="fa fa-lock"></i> Saved Section ' + section.toUpperCase());
                    previousCompleted = true;
                } else {
                    // Section is not completed - enable section and show normal text
                    sectionCard.removeClass('section-saved');
                    
                    // Enable ALL form elements comprehensively
                    sectionCard.find('input[type="text"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="number"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="email"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="password"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="tel"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="url"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="date"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="time"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="datetime-local"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="month"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="week"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="search"]').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('input[type="radio"]').prop('disabled', false);
                    sectionCard.find('input[type="checkbox"]').prop('disabled', false);
                    sectionCard.find('textarea').prop('disabled', false).prop('readonly', false);
                    sectionCard.find('select').prop('disabled', false);
                    sectionCard.find('option').prop('disabled', false);
                    
                    // Enable all action buttons
                    sectionCard.find('.add-responsibility, .add-spec, .remove-responsibility, .remove-spec, .remove-interest').prop('disabled', false);
                    
                    saveButton.prop('disabled', false)
                              .removeClass('btn-outline-success')
                              .addClass('btn-success');
                    
                    // Restore original button text based on section
                    if (section === 'e') {
                        saveButton.html('<i class="fa fa-check-circle"></i> Final Submit');
                    } else {
                        saveButton.html(`<i class="fa fa-save"></i> Save Section ${section.toUpperCase()}`);
                    }
                    
                    previousCompleted = false;
                }
            });
        }
        
        // Load saved data for a section
        function loadSectionData(section) {
            console.log('Loading data for section:', section);
            $.ajax({
                url: `/survey/get-section-data/${section}`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Response for section', section, ':', response);
                    if (response.success && response.data) {
                        console.log('Data found for section', section, ':', response.data);
                        populateSectionData(section, response.data);
                    } else {
                        console.log('No data found for section', section);
                    }
                },
                error: function(xhr) {
                    console.log('Error loading data for section', section, ':', xhr.responseText);
                }
            });
        }
        
        // Populate section with saved data
        function populateSectionData(section, data) {
            console.log('Populating section', section, 'with data:', data);
            switch(section) {
                case 'a':
                    // Populate only Section A data
                    $('input[name="job_title"]').val(data.job_title || '');
                    $('input[name="hris_id"]').val(data.hris_id || '');
                    $('input[name="school"]').val(data.school || '');
                    $('input[name="incumbent_name"]').val(data.incumbent_name || '');
                    $('input[name="department"]').val(data.department || '');
                    $('input[name="supervisor_name"]').val(data.supervisor_name || '');
                    $(`input[name="job_status"][value="${data.job_status}"]`).prop('checked', true);
                    break;
                case 'b':
                    $('textarea[name="role_description"]').val(data.role_description || '');
                    break;
                case 'c':
                    console.log('Populating Section C - responsibilities:', data.responsibilities);
                    if (data.responsibilities && data.responsibilities.length > 0) {
                        // Clear existing rows and add saved data
                        $('#responsibilities-table tbody').empty();
                        data.responsibilities.forEach((responsibility, index) => {
                            console.log('Adding responsibility row:', responsibility, data.responsibility_percentage[index]);
                            addResponsibilityRow(responsibility, data.responsibility_percentage[index]);
                        });
                        
                        // Recalculate total percentage after loading data
                        updateTotalPercentage();
                    } else {
                        console.log('No responsibilities data found for Section C');
                    }
                    break;
                case 'd':
                    console.log('Populating Section D - qualifications:', data.qualification);
                    if (data.qualification && data.qualification.length > 0) {
                        // Clear existing rows and add saved data
                        $('#job-specifications-table tbody').empty();
                        data.qualification.forEach((qualification, index) => {
                            console.log('Adding qualification row:', qualification, data.experience[index]);
                            addSpecificationRow(qualification, data.experience[index]);
                        });
                    } else {
                        console.log('No qualifications data found for Section D');
                    }
                    break;
                case 'e':
                    if (data.multi_interest && data.multi_interest.length > 0) {
                        // Clear existing rows and add saved data
                        $('#multi-interest-table tbody').empty();
                        data.multi_interest.forEach((interest, index) => {
                            addInterestRow(interest, data.required_skills[index]);
                        });
                    }
                    break;
            }
        }
        
        // Save section data
        function saveSection(section) {
            const $button = $(`.save-section[data-section="${section}"]`);
            const originalText = $button.html();
            
            $button.addClass('saving').html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            let formData = new FormData();
            
            // Collect data based on section
            switch(section) {
                case 'a':
                    // Send only Section A form data
                    formData.append('job_title', $('input[name="job_title"]').val());
                    formData.append('hris_id', $('input[name="hris_id"]').val());
                    formData.append('school', $('input[name="school"]').val());
                    formData.append('incumbent_name', $('input[name="incumbent_name"]').val());
                    formData.append('department', $('input[name="department"]').val());
                    formData.append('supervisor_name', $('input[name="supervisor_name"]').val());
                    formData.append('job_status', $('input[name="job_status"]:checked').val());
                    break;
                case 'b':
                    formData.append('role_description', $('textarea[name="role_description"]').val());
                    break;
                case 'c':
                    const responsibilities = [];
                    const percentages = [];
                    $('#responsibilities-table tbody tr').each(function() {
                        const responsibility = $(this).find('input[name="responsibilities[]"]').val();
                        const percentage = $(this).find('input[name="responsibility_percentage[]"]').val();
                        if (responsibility && percentage) {
                            responsibilities.push(responsibility);
                            percentages.push(percentage);
                        }
                    });
                    formData.append('responsibilities', JSON.stringify(responsibilities));
                    formData.append('responsibility_percentage', JSON.stringify(percentages));
                    break;
                case 'd':
                    const qualifications = [];
                    const experiences = [];
                    $('#job-specifications-table tbody tr').each(function() {
                        const qualification = $(this).find('input[name="qualification[]"]').val();
                        const experience = $(this).find('input[name="experience[]"]').val();
                        if (qualification && experience) {
                            qualifications.push(qualification);
                            experiences.push(experience);
                        }
                    });
                    formData.append('qualification', JSON.stringify(qualifications));
                    formData.append('experience', JSON.stringify(experiences));
                    break;
                case 'e':
                    const interests = [];
                    const skills = [];
                    $('#multi-interest-table tbody tr').each(function() {
                        const interest = $(this).find('input[name="multi_interest[]"]').val();
                        const skill = $(this).find('input[name="required_skills[]"]').val();
                        if (interest && skill) {
                            interests.push(interest);
                            skills.push(skill);
                        }
                    });
                    formData.append('multi_interest', JSON.stringify(interests));
                    formData.append('required_skills', JSON.stringify(skills));
                    break;
            }
            
            $.ajax({
                url: `/survey/save-section-${section}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $button.removeClass('saving').addClass('saved').html('<i class="fa fa-check"></i> Saved!');
                        
                        // Update section status
                        sectionStatus[`section_${section}`] = true;
                        updateProgress();
                        
                        // Show success message
                        showToast('Success', response.message, 'success');
                        
                        // Reset form changes flag since section was saved
                        resetFormChanges();
                        
                        // If this is section E (Final Submit), redirect using the URL from response
                        if (section === 'e' && response.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 2000);
                        }
                        
                        // Reset button after 3 seconds and update to saved status
                        setTimeout(function() {
                            $button.removeClass('saved')
                                  .prop('disabled', true)
                                  .removeClass('btn-success')
                                  .addClass('btn-secondary')
                                  .html('<i class="fa fa-lock"></i> Saved Section ' + section.toUpperCase());
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    $button.removeClass('saving').html(originalText);
                    
                    // Handle network errors
                    if (xhr.status === 0) {
                        showToast('Network Error', 'Please check your internet connection and try again. Your data is safe.', 'error');
                        // Store form data in localStorage for recovery
                        localStorage.setItem('formDataBackup', JSON.stringify($('#job-description-form').serializeArray()));
                        return;
                    }
                    
                    // Handle server errors
                    if (xhr.status >= 500) {
                        showToast('Server Error', 'Server is temporarily unavailable. Please try again later. Your data is safe.', 'error');
                        // Store form data in localStorage for recovery
                        localStorage.setItem('formDataBackup', JSON.stringify($('#job-description-form').serializeArray()));
                        return;
                    }
                    
                    // Handle timeout errors
                    if (xhr.status === 408) {
                        showToast('Timeout Error', 'Request timed out. Please try again.', 'error');
                        return;
                    }
                    
                    // Handle connection refused
                    if (xhr.status === 0 && xhr.readyState === 0) {
                        showToast('Connection Error', 'Unable to connect to server. Please check your internet connection.', 'error');
                        localStorage.setItem('formDataBackup', JSON.stringify($('#job-description-form').serializeArray()));
                        return;
                    }
                    
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        // Show first error in toast
                        const firstError = Object.values(response.errors)[0][0];
                        showToast('Validation Error', firstError, 'error');
                        
                        // Highlight all error fields
                        Object.keys(response.errors).forEach(function(key) {
                            const field = $(`[name="${key}"]`);
                            field.addClass('is-invalid');
                            field.siblings('.invalid-feedback').addClass('show').text(response.errors[key][0]);
                            
                            // Scroll to first error field
                            if (key === Object.keys(response.errors)[0]) {
                                $('html, body').animate({
                                    scrollTop: field.offset().top - 100
                                }, 500);
                            }
                        });
                    } else {
                        showToast('Error', response?.message || 'Error saving section. Please try again.', 'error');
                    }
                }
            });
        }
        
        // Handle section save button clicks
        $(document).on('click', '.save-section', function() {
            const section = $(this).data('section');
            saveSection(section);
        });
        

        
        // Check if section can be accessed
        function canAccessSection(section) {
            const sectionOrder = ['a', 'b', 'c', 'd', 'e']; // Added 'b' back to the array
            const currentIndex = sectionOrder.indexOf(section);
            
            for (let i = 0; i < currentIndex; i++) {
                const previousSection = sectionOrder[i];
                const previousSectionKey = `section_${previousSection}`;
                if (!sectionStatus[previousSectionKey]) {
                    return false;
                }
            }
            return true;
        }
        
        // Helper functions for adding rows
        function addResponsibilityRow(responsibility = '', percentage = '') {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="responsibilities[]" class="form-control responsibility-input char-count-input" data-max-chars="3000" placeholder="Enter responsibility" value="${responsibility}">
                        <div class="invalid-feedback">This responsibility is required</div>
                        <small class="text-muted char-count-info">Characters: ${responsibility.length}/3000</small>
                    </td>
                    <td>
                        <input type="number" name="responsibility_percentage[]" class="form-control percentage-input" placeholder="e.g. 20" min="1" max="100" step="1" value="${percentage}">
                        <div class="invalid-feedback">Please enter a valid percentage (1-100)</div>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-responsibility"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $('#responsibilities-table tbody').append(template);
            
            // Update character count for the new row
            const newRow = $('#responsibilities-table tbody tr:last');
            newRow.find('.char-count-input').each(function() {
                updateCharCount(this);
            });
            
            // Update total percentage after adding row
            updateTotalPercentage();
            
            // Update button visibility based on row count
            updateResponsibilityButtons();
        }
        
        function addSpecificationRow(qualification = '', experience = '') {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="qualification[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter qualification" value="${qualification}">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: ${qualification.length}/3000</small>
                    </td>
                    <td>
                        <input type="number" name="experience[]" class="form-control" placeholder="Enter experience" min="0" step="1" value="${experience}">
                        <div class="invalid-feedback">Please enter a valid experience in years (positive numbers only)</div>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-spec"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $('#job-specifications-table tbody').append(template);
        }
        
        function addInterestRow(interest = '', skills = '') {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="multi_interest[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter area of interest" value="${interest}">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: ${interest.length}/3000</small>
                    </td>
                    <td>
                        <input type="text" name="required_skills[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter required expertise" value="${skills}">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: ${skills.length}/3000</small>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-interest"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $('#multi-interest-table tbody').append(template);
        }
        
        // Initialize on page load
        initializeProgress();
        
        // Check for form data recovery
        const savedFormData = localStorage.getItem('formDataBackup');
        if (savedFormData) {
            try {
                const formData = JSON.parse(savedFormData);
                formData.forEach(function(item) {
                    const field = $(`[name="${item.name}"]`);
                    if (field.length && !field.val()) {
                        field.val(item.value);
                        field.trigger('input'); // Trigger character count update
                    }
                });
                
                showToast('Data Recovery', 'Your previous form data has been restored.', 'info');
                localStorage.removeItem('formDataBackup'); // Clear after recovery
            } catch (e) {
                console.error('Error recovering form data:', e);
                localStorage.removeItem('formDataBackup');
            }
        }
        
        // Track form changes for beforeunload warning
        let formHasChanges = false;
        
        // Monitor form changes
        $(document).on('input change', 'input, textarea, select', function() {
            formHasChanges = true;
        });
        
        // Reset form changes flag when section is saved
        function resetFormChanges() {
            formHasChanges = false;
        }
        
        // Warn user before leaving page with unsaved changes
        $(window).on('beforeunload', function(e) {
            if (formHasChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        // Enhanced sticky progress bar with shadow effect
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const stickyProgress = $('.sticky-progress');
            
            if (scrollTop > 10) {
                stickyProgress.addClass('scrolled');
            } else {
                stickyProgress.removeClass('scrolled');
            }
        });
        
        // Debug: Log section status
        console.log('Section status on page load:', sectionStatus);
        
        // Apply disabled state to saved sections on page load
        Object.keys(sectionStatus).forEach(sectionKey => {
            if (sectionStatus[sectionKey]) {
                const section = sectionKey.split('_')[1]; // Extract section letter (a, b, c, d, e)
                console.log('Section', section, 'is completed, applying disabled state');
                
                // Apply disabled state immediately
                const sectionCard = $(`#section-${section}`);
                const saveButton = $(`.save-section[data-section="${section}"]`);
                
                // Add saved class and disable all elements
                sectionCard.addClass('section-saved');
                
                // Disable ALL form elements comprehensively
                sectionCard.find('input[type="text"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="number"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="email"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="password"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="tel"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="url"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="date"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="time"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="datetime-local"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="month"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="week"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="search"]').prop('disabled', true).prop('readonly', true);
                sectionCard.find('input[type="radio"]').prop('disabled', true);
                sectionCard.find('input[type="checkbox"]').prop('disabled', true);
                sectionCard.find('textarea').prop('disabled', true).prop('readonly', true);
                sectionCard.find('select').prop('disabled', true);
                sectionCard.find('option').prop('disabled', true);
                
                // Disable all action buttons
                sectionCard.find('.add-responsibility, .add-spec, .remove-responsibility, .remove-spec, .remove-interest').prop('disabled', true);
                
                // Update save button to show saved status
                saveButton.prop('disabled', true)
                          .removeClass('btn-success')
                          .addClass('btn-secondary')
                          .html('<i class="fa fa-lock"></i> Saved Section ' + section.toUpperCase());
                
                // Load saved data
                loadSectionData(section);
            } else {
                const section = sectionKey.split('_')[1];
                console.log('Section', section, 'is not completed, skipping data load');
            }
        });
        
        // Initialize total percentage after a short delay to ensure all data is loaded
        setTimeout(function() {
            updateTotalPercentage();
            updateResponsibilityButtons(); // Initialize responsibility buttons
        }, 500);

        // Fix for Basic Information section character count
        // Initialize character counters on page load
        $('input[maxlength]').each(function() {
            const maxLength = $(this).attr('maxlength');
            const currentLength = $(this).val().length;
            const countInfo = $(this).siblings('.char-count-info');
            
            if (countInfo.length) {
                countInfo.text(`Characters: ${currentLength}/${maxLength}`);
                
                // Visual feedback for character count
                if (currentLength >= maxLength) {
                    countInfo.addClass('text-danger');
                } else if (currentLength >= maxLength * 0.8) {
                    countInfo.addClass('text-warning').removeClass('text-danger');
                } else {
                    countInfo.removeClass('text-warning text-danger');
                }
            }
        });
        
        // Initialize character count for textarea
        $('.char-count-input').each(function() {
            const maxChars = $(this).data('max-chars');
            if (maxChars) {
                const text = $(this).val();
                const charCount = text.length;
                $(this).siblings('.char-count-info').text(`Characters: ${charCount}/${maxChars}`);
                
                // Visual feedback
                if (charCount >= maxChars) {
                    $(this).siblings('.char-count-info').addClass('text-danger');
                } else if (charCount >= maxChars * 0.8) {
                    $(this).siblings('.char-count-info').addClass('text-warning').removeClass('text-danger');
                } else {
                    $(this).siblings('.char-count-info').removeClass('text-warning text-danger');
                }
            }
        });
        
        // Update character count in real-time as user types
        $(document).on('input', 'input[maxlength]', function() {
            const maxLength = $(this).attr('maxlength');
            const currentLength = $(this).val().length;
            const countInfo = $(this).siblings('.char-count-info');
            
            if (countInfo.length) {
                countInfo.text(`Characters: ${currentLength}/${maxLength}`);
                
                // Visual feedback
                if (currentLength >= maxLength) {
                    countInfo.addClass('text-danger');
                } else if (currentLength >= maxLength * 0.8) {
                    countInfo.addClass('text-warning').removeClass('text-danger');
                } else {
                    countInfo.removeClass('text-warning text-danger');
                }
            }
        });
        
        // Force the count update when the page loads (for fields with default values)
        setTimeout(function() {
            $('input[maxlength]').trigger('input');
            $('.char-count-input').trigger('input');
            $('textarea[maxlength]').trigger('input');
        }, 100);
        
        // Initialize character counts for pre-filled fields
        $(document).ready(function() {
            $('input[maxlength], .char-count-input, textarea[maxlength]').each(function() {
                const currentLength = $(this).val().length;
                const maxLength = $(this).attr('maxlength') || $(this).data('max-chars');
                if (maxLength) {
                    $(this).siblings('.char-count-info').text(`Characters: ${currentLength}/${maxLength}`);
                }
            });
        });
        
        // Add character count functionality for Basic Information section
        function updateCharCount(input) {
            const maxChars = $(input).data('max-chars');
            if (maxChars) {
                const text = $(input).val();
                const charCount = countChars(text);
                
                // Update the character count display
                $(input).siblings('.char-count-info').text(`Characters: ${charCount}/${maxChars}`);
                
                // Add visual feedback
                if (charCount > maxChars) {
                    $(input).addClass('is-invalid');
                    $(input).siblings('.invalid-feedback').text(`Maximum ${maxChars} characters allowed`);
                    $(input).addClass('word-limit-exceeded'); // Add red text for exceeded limit
                } else {
                    $(input).removeClass('is-invalid');
                    $(input).removeClass('word-limit-exceeded');
                }
            }
        }
        
        // Initialize character count for all fields
        $('.char-count-input').each(function() {
            updateCharCount(this);
        });

        // Handle input events for character-count-input fields
        $(document).on('input', '.char-count-input', function(e) {
            const text = $(this).val();
            const canType = preventTypingAfterCharLimit(this, text);
            
            if (!canType) {
                // Get cursor position
                const cursorPos = this.selectionStart;
                
                // Prevent the input and restore the previous value
                const maxChars = $(this).data('max-chars');
                const limitedText = text.substring(0, maxChars);
                $(this).val(limitedText);
                
                // Restore cursor to the end
                this.setSelectionRange(limitedText.length, limitedText.length);
            }
            
            updateCharCount(this);
            validateRelatedFields($(this).closest('tr'));
        });

        // Function to prevent typing after character limit
        function preventTypingAfterCharLimit(input, text) {
            const maxChars = $(input).data('max-chars');
            
            if (text.length > maxChars) {
                // Keep only the first maxChars
                $(input).val(text.substring(0, maxChars));
                return false;
            }
            return true;
        }

        // Prevent paste if it would exceed character limit
        $(document).on('paste', '.char-count-input', function(e) {
            const maxChars = $(this).data('max-chars');
            
            // Get current text and pasted text
            const currentText = $(this).val();
            const pastedText = e.originalEvent.clipboardData.getData('text');
            
            // Combine current and pasted text
            const combinedText = currentText + pastedText;
            
            if (combinedText.length > maxChars) {
                e.preventDefault();
                // Show a temporary message that paste was blocked
                const $input = $(this);
                const originalMessage = $input.siblings('.invalid-feedback').text();
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text(`Cannot paste: would exceed ${maxChars} character limit`).show();
                
                // Reset the message after 2 seconds
                setTimeout(() => {
                    if ($input.siblings('.invalid-feedback').text() === `Cannot paste: would exceed ${maxChars} character limit`) {
                        $input.siblings('.invalid-feedback').text(originalMessage);
                        if (countChars($input.val()) <= maxChars) {
                            $input.removeClass('is-invalid');
                        }
                    }
                }, 2000);
            }
        });

        // Also prevent drag and drop if it would exceed limit
        $(document).on('drop', '.char-count-input', function(e) {
            const maxChars = $(this).data('max-chars');
            
            // Get current text and dropped text
            const currentText = $(this).val();
            const droppedText = e.originalEvent.dataTransfer.getData('text');
            
            // Combine current and dropped text
            const combinedText = currentText + droppedText;
            
            if (combinedText.length > maxChars) {
                e.preventDefault();
                // Show a temporary message that drop was blocked
                const $input = $(this);
                const originalMessage = $input.siblings('.invalid-feedback').text();
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text(`Cannot drop: would exceed ${maxChars} character limit`).show();
                
                // Reset the message after 2 seconds
                setTimeout(() => {
                    if ($input.siblings('.invalid-feedback').text() === `Cannot drop: would exceed ${maxChars} character limit`) {
                        $input.siblings('.invalid-feedback').text(originalMessage);
                        if (countChars($input.val()) <= maxChars) {
                            $input.removeClass('is-invalid');
                        }
                    }
                }, 2000);
            }
        });

        // Validation for incumbent_name and supervisor_name fields (3-150 characters)
        $('input[name="incumbent_name"], input[name="supervisor_name"]').on('input', function() {
            const input = $(this);
            const value = input.val().trim();
            
            if (value && value.length < 3) {
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').show().text('Name must be at least 3 characters');
            } else if (value && value.length > 150) {
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').show().text('Name cannot exceed 150 characters');
            } else {
                input.removeClass('is-invalid');
                input.siblings('.invalid-feedback').hide();
            }
        });

        // Word count functionality
        function countWords(text) {
            return text.trim().split(/\s+/).filter(word => word.length > 0).length;
        }

        // Character count functionality
        function countChars(text) {
            return text.length;
        }

        function updateWordCount(input) {
            const maxWords = $(input).data('max-words');
            const text = $(input).val();
            const wordCount = countWords(text);
            
            // Update the word count display
            $(input).siblings('.word-count-info').text(`Words: ${wordCount}/${maxWords}`);
            
            // Check if it's a qualification field
            const isQualificationField = $(input).attr('name') === 'qualification[]';
            
            // Add visual feedback
            if (wordCount > maxWords || (!isQualificationField && wordCount < 3 && text.trim() !== '')) {
                $(input).addClass('is-invalid');
                if (wordCount > maxWords) {
                    $(input).siblings('.invalid-feedback').text(`Maximum ${maxWords} words allowed`);
                    $(input).addClass('word-limit-exceeded'); // Add red text for exceeded word limit
                } else if (!isQualificationField) {
                    // Check if it's a text area for role description
                    if ($(input).attr('id') === 'role-description') {
                        $(input).siblings('.invalid-feedback').text(`Minimum 5 words required`);
                    } else {
                        $(input).siblings('.invalid-feedback').text(`Minimum 10 characters required`);
                    }
                    $(input).removeClass('word-limit-exceeded'); // Remove red text class
                }
            } else {
                $(input).removeClass('is-invalid');
                $(input).removeClass('word-limit-exceeded'); // Remove red text class
            }
        }

        // Initialize word count for all fields including textarea
        $('.word-count-input').each(function() {
            updateWordCount(this);
        });

        // Handle input events for word-count-input fields (including textarea)
        $(document).on('input', '.word-count-input', function(e) {
            const text = $(this).val();
            const canType = preventTypingAfterLimit(this, text);
            
            if (!canType) {
                // Get cursor position
                const cursorPos = this.selectionStart;
                
                // Prevent the input and restore the previous value
                const words = text.trim().split(/\s+/).filter(word => word.length > 0);
                const maxWords = $(this).data('max-words');
                const limitedText = words.slice(0, maxWords).join(' ');
                $(this).val(limitedText);
                
                // Restore cursor to the end
                this.setSelectionRange(limitedText.length, limitedText.length);
            }
            
            updateWordCount(this);
            validateRelatedFields($(this).closest('tr'));
        });

        // Function to prevent typing after word limit
        function preventTypingAfterLimit(input, text) {
            const maxWords = $(input).data('max-words');
            const words = text.trim().split(/\s+/).filter(word => word.length > 0);
            
            if (words.length > maxWords) {
                // Keep only the first maxWords
                $(input).val(words.slice(0, maxWords).join(' '));
                return false;
            }
            return true;
        }

        // Update word count on input
        $(document).on('input', '.word-count-input', function() {
            updateWordCount(this);
        });

        // Update word count when adding new rows
        function initializeNewRow(row) {
            row.find('.word-count-input').each(function() {
                $(this).val('');
                updateWordCount(this);
            });
            validateRelatedFields(row);
        }

        // Handle remove buttons for all sections
        $(document).on('click', '.remove-responsibility, .remove-spec, .remove-interest', function() {
            const $row = $(this).closest('tr');
            const $table = $(this).closest('table');
            
            // Don't remove if it's the last row
            if ($table.find('tbody tr').length > 1) {
                $row.remove();
                
                // If it's responsibilities table, recalculate total percentage and update buttons
                if ($table.attr('id') === 'responsibilities-table') {
                    updateTotalPercentage();
                    updateResponsibilityButtons();
                }
            } else {
                // If it's the last row, just clear the inputs
                $row.find('input').val('').removeClass('is-invalid');
                $row.find('.word-count-info').text('Words: 0/150');
                if ($table.attr('id') === 'responsibilities-table') {
                    updateTotalPercentage();
                    updateResponsibilityButtons();
                }
            }
        });

        // Function to update responsibility buttons based on row count
        function updateResponsibilityButtons() {
            const rowCount = $('#responsibilities-table tbody tr').length;
            
            $('#responsibilities-table tbody tr').each(function(index) {
                const $button = $(this).find('.action-cell button');
                
                if (index < 6) {
                    // Rows 1-6 (index 0-5): No button
                    $button.remove();
                } else if (index === 6) {
                    // 7th row (index 6): + button
                    if ($button.length === 0) {
                        // Create + button if it doesn't exist
                        $(this).find('.action-cell').html('<button type="button" class="btn btn-sm btn-outline-success add-responsibility"><i class="fa fa-plus"></i></button>');
                    } else {
                        $button.removeClass('btn-outline-danger remove-responsibility').addClass('btn-outline-success add-responsibility')
                              .html('<i class="fa fa-plus"></i>');
                    }
                } else {
                    // 8th row onwards (index 7+): - button
                    if ($button.length === 0) {
                        // Create - button if it doesn't exist
                        $(this).find('.action-cell').html('<button type="button" class="btn btn-sm btn-outline-danger remove-responsibility"><i class="fa fa-minus"></i></button>');
                    } else {
                        $button.removeClass('btn-outline-success add-responsibility').addClass('btn-outline-danger remove-responsibility')
                              .html('<i class="fa fa-minus"></i>');
                    }
                }
            });
        }

        // Handle adding new rows for responsibilities
        $("#responsibilities-table").on("click", ".add-responsibility", function() {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="responsibilities[]" class="form-control responsibility-input char-count-input" data-max-chars="3000" placeholder="Enter responsibility">
                        <div class="invalid-feedback">This responsibility is required</div>
                        <small class="text-muted char-count-info">Characters: 0/3000</small>
                    </td>
                    <td>
                        <input type="number" name="responsibility_percentage[]" class="form-control percentage-input" placeholder="e.g. 20" min="1" max="100" step="1">
                        <div class="invalid-feedback">Please enter a valid percentage (1-100)</div>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-responsibility"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $(this).closest('tbody').append(template);
            const newRow = $(this).closest('tbody').find('tr:last');
            initializeNewRow(newRow);
            validateRelatedFields(newRow);
            
            // Update button visibility after adding row
            updateResponsibilityButtons();
        });

        // Handle adding new rows for job specifications
        $("#job-specifications-table").on("click", ".add-spec", function() {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="qualification[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter qualification">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: 0/3000</small>
                    </td>
                    <td>
                        <input type="number" name="experience[]" class="form-control" placeholder="Enter experience" min="0" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" pattern="[0-9]*">
                        <div class="invalid-feedback">Please enter a valid experience in years (positive numbers only)</div>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-spec"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $(this).closest('tbody').append(template);
            const newRow = $(this).closest('tbody').find('tr:last');
            initializeNewRow(newRow);
            validateRelatedFields(newRow);
        });

        // Handle adding new rows for multidisciplinary interests
        $("#multi-interest-table").on("click", ".add-spec", function() {
            const template = `
                <tr>
                    <td>
                        <input type="text" name="multi_interest[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter area of interest">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: 0/3000</small>
                    </td>
                    <td>
                        <input type="text" name="required_skills[]" class="form-control char-count-input" data-max-chars="3000" placeholder="Enter required expertise">
                        <div class="invalid-feedback">This field is required</div>
                        <small class="text-muted char-count-info">Characters: 0/3000</small>
                    </td>
                    <td class="action-cell text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-interest"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
            `;
            $(this).closest('tbody').append(template);
            const newRow = $(this).closest('tbody').find('tr:last');
            initializeNewRow(newRow);
            validateRelatedFields(newRow);
        });

        // Handle input on percentage fields to update total
        $(document).on('input', '.percentage-input', function() {
            updateTotalPercentage();
        });
        


        // Function to update total percentage
        function updateTotalPercentage() {
            let totalPercentage = 0;
            $('.percentage-input').each(function() {
                const value = parseInt($(this).val()) || 0;
                totalPercentage += value;
            });
            $('#total-percentage').text(totalPercentage);
            
            // Show/hide error based on total
            if (totalPercentage === 100) {
                $('#percentage-error').addClass('d-none');
            } else {
                $('#percentage-error').removeClass('d-none');
            }
        }

        // Initialize total percentage on page load
        function initializeTotalPercentage() {
            updateTotalPercentage();
        }
        
        // Call this on page load
        initializeTotalPercentage();

        // Function to handle related field validation
        function validateRelatedFields(row) {
            const $row = $(row);
            
            // For responsibilities table
            if ($row.closest('#responsibilities-table').length) {
                const responsibilityInput = $row.find('.responsibility-input');
                const percentageInput = $row.find('.percentage-input');
                
                // If one has a value, the other must have a value too
                if (responsibilityInput.val().trim()) {
                    percentageInput.prop('required', true);
                    if (!percentageInput.val()) {
                        percentageInput.addClass('is-invalid');
                        percentageInput.siblings('.invalid-feedback').addClass('show').text('Percentage is required when responsibility is filled');
                    } else {
                        percentageInput.removeClass('is-invalid');
                        percentageInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    percentageInput.prop('required', false);
                }
                
                if (percentageInput.val()) {
                    responsibilityInput.prop('required', true);
                    if (!responsibilityInput.val().trim()) {
                        responsibilityInput.addClass('is-invalid');
                        responsibilityInput.siblings('.invalid-feedback').addClass('show').text('Responsibility is required when percentage is filled');
                    } else {
                        responsibilityInput.removeClass('is-invalid');
                        responsibilityInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    responsibilityInput.prop('required', false);
                }
            }
            
            // For job specifications table
            if ($row.closest('#job-specifications-table').length) {
                const qualificationInput = $row.find('input[name="qualification[]"]');
                const experienceInput = $row.find('input[name="experience[]"]');
                
                // If one has a value, the other must have a value too
                if (qualificationInput.val().trim()) {
                    experienceInput.prop('required', true);
                    if (!experienceInput.val()) {
                        experienceInput.addClass('is-invalid');
                        experienceInput.siblings('.invalid-feedback').addClass('show').text('Experience is required when qualification is filled');
                    } else {
                        experienceInput.removeClass('is-invalid');
                        experienceInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    experienceInput.prop('required', false);
                }
                
                if (experienceInput.val()) {
                    qualificationInput.prop('required', true);
                    if (!qualificationInput.val().trim()) {
                        qualificationInput.addClass('is-invalid');
                        qualificationInput.siblings('.invalid-feedback').addClass('show').text('Qualification is required when experience is filled');
                    } else {
                        qualificationInput.removeClass('is-invalid');
                        qualificationInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    qualificationInput.prop('required', false);
                }
            }
            
            // For multidisciplinary interests table
            if ($row.closest('#multi-interest-table').length) {
                const interestInput = $row.find('input[name="multi_interest[]"]');
                const skillsInput = $row.find('input[name="required_skills[]"]');
                
                // If one has a value, the other must have a value too
                if (interestInput.val().trim()) {
                    skillsInput.prop('required', true);
                    if (!skillsInput.val().trim()) {
                        skillsInput.addClass('is-invalid');
                        skillsInput.siblings('.invalid-feedback').addClass('show').text('Skills are required when interest is filled');
                    } else {
                        skillsInput.removeClass('is-invalid');
                        skillsInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    skillsInput.prop('required', false);
                }
                
                if (skillsInput.val().trim()) {
                    interestInput.prop('required', true);
                    if (!interestInput.val().trim()) {
                        interestInput.addClass('is-invalid');
                        interestInput.siblings('.invalid-feedback').addClass('show').text('Area of interest is required when skills are filled');
                    } else {
                        interestInput.removeClass('is-invalid');
                        interestInput.siblings('.invalid-feedback').removeClass('show');
                    }
                } else {
                    interestInput.prop('required', false);
                }
            }
        }

        // Handle input on any field in the tables - validate related fields
        $(document).on('input', '#responsibilities-table input, #job-specifications-table input, #multi-interest-table input', function() {
            validateRelatedFields($(this).closest('tr'));
        });

        // Initialize validation on page load for all existing rows
        function initializeAllRowsValidation() {
            $('#responsibilities-table tbody tr, #job-specifications-table tbody tr, #multi-interest-table tbody tr').each(function() {
                validateRelatedFields($(this));
            });
        }
        
        // Call this on page load
        initializeAllRowsValidation();

        // Form validation
        function validateForm() {
            let isValid = true;
            let firstError = null;

            // Reset all error states
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').removeClass('show').hide();
            $('.alert-danger').addClass('d-none');

            // Validate Basic Information
            function validateBasicInfo() {
                let basicInfoValid = true;

                // Job Title validation
                const jobTitle = $('input[name="job_title"]');
                if (!jobTitle.val().trim()) {
                    jobTitle.addClass('is-invalid');
                    jobTitle.siblings('.invalid-feedback').show().text('Job title is required');
                    basicInfoValid = false;
                    firstError = firstError || jobTitle;
                }

                // HRIS ID validation
                const hrisId = $('input[name="hris_id"]');
                if (!hrisId.val().trim() || !hrisId.val().match(/^\d+$/)) {
                    hrisId.addClass('is-invalid');
                    hrisId.siblings('.invalid-feedback').show().text('Please enter a valid HRIS ID (numbers only)');
                    basicInfoValid = false;
                    firstError = firstError || hrisId;
                }

                // Incumbent Name validation
                const incumbentName = $('input[name="incumbent_name"]');
                if (!incumbentName.val().trim()) {
                    incumbentName.addClass('is-invalid');
                    incumbentName.siblings('.invalid-feedback').show().text('Name is required');
                    basicInfoValid = false;
                    firstError = firstError || incumbentName;
                } else if (!incumbentName.val().match(/^[A-Za-z\s\.]{3,150}$/)) {
                    incumbentName.addClass('is-invalid');
                    incumbentName.siblings('.invalid-feedback').show().text('Name must be 3-150 characters, letters only');
                    basicInfoValid = false;
                    firstError = firstError || incumbentName;
                }

                // Supervisor Name validation
                const supervisorName = $('input[name="supervisor_name"]');
                if (!supervisorName.val().trim()) {
                    supervisorName.addClass('is-invalid');
                    supervisorName.siblings('.invalid-feedback').show().text('Supervisor name is required');
                    basicInfoValid = false;
                    firstError = firstError || supervisorName;
                } else if (!supervisorName.val().match(/^[A-Za-z\s\.]{3,150}$/)) {
                    supervisorName.addClass('is-invalid');
                    supervisorName.siblings('.invalid-feedback').show().text('Supervisor name must be 3-150 characters, letters only');
                    basicInfoValid = false;
                    firstError = firstError || supervisorName;
                }

                return basicInfoValid;
            }

            // Validate Role Description
            function validateRoleDescription() {
                const roleDescription = $('#role-description');
                const text = roleDescription.val().trim();
                const charCount = text.length;
                
                if (!text) {
                    roleDescription.addClass('is-invalid');
                    roleDescription.siblings('.invalid-feedback').show().text('Role description is required');
                    firstError = firstError || roleDescription;
                    return false;
                } else if (charCount > 3000) {
                    roleDescription.addClass('is-invalid');
                    roleDescription.siblings('.invalid-feedback').show().text('Role description exceeds 3000 characters limit');
                    firstError = firstError || roleDescription;
                    return false;
                }
                return true;
            }

            // Validate Responsibilities
            function validateResponsibilities() {
                let responsibilitiesValid = true;
                let totalPercentage = 0;
                let responsibilityCount = 0;
                let emptyRowCount = 0;
                let firstResponsibilityError = null;

                $('#responsibilities-table tbody tr').each(function(index) {
                    const responsibilityInput = $(this).find('.responsibility-input');
                    const percentageInput = $(this).find('.percentage-input');
                    const responsibilityText = responsibilityInput.val().trim();
                    const wordCount = countWords(responsibilityText);
                    const charCount = responsibilityText.length;
                    const percentage = parseInt(percentageInput.val()) || 0;

                    // All rows are required
                    if (!responsibilityText) {
                        responsibilityInput.addClass('is-invalid');
                        responsibilityInput.siblings('.invalid-feedback').addClass('show').text('This responsibility is required');
                        responsibilitiesValid = false;
                        firstResponsibilityError = firstResponsibilityError || responsibilityInput;
                        emptyRowCount++;
                    } else if (charCount > 3000) {
                        responsibilityInput.addClass('is-invalid');
                        responsibilityInput.siblings('.invalid-feedback').addClass('show').text('Maximum 3000 characters allowed');
                        responsibilitiesValid = false;
                        firstResponsibilityError = firstResponsibilityError || responsibilityInput;
                    } else {
                        responsibilityCount++;
                    }

                    // Percentage is required for all rows
                    if (!percentageInput.val()) {
                        percentageInput.addClass('is-invalid');
                        percentageInput.siblings('.invalid-feedback').addClass('show').text('Percentage is required');
                        responsibilitiesValid = false;
                        firstResponsibilityError = firstResponsibilityError || percentageInput;
                    } else if (percentage < 1 || percentage > 100) {
                        percentageInput.addClass('is-invalid');
                        percentageInput.siblings('.invalid-feedback').addClass('show').text('Percentage must be between 1 and 100');
                        responsibilitiesValid = false;
                        firstResponsibilityError = firstResponsibilityError || percentageInput;
                    }

                    totalPercentage += percentage;
                });

                // Update total percentage display
                $('#total-percentage').text(totalPercentage);

                // Check minimum responsibilities (all 7 rows must be filled)
                if (emptyRowCount > 0) {
                    $('#responsibilities-error').removeClass('d-none').text(`All ${$('#responsibilities-table tbody tr').length} responsibilities must be filled`);
                    responsibilitiesValid = false;
                } else {
                    $('#responsibilities-error').addClass('d-none');
                }

                // Check total percentage
                if (totalPercentage !== 100) {
                    $('#percentage-error').removeClass('d-none');
                    responsibilitiesValid = false;
                } else {
                    $('#percentage-error').addClass('d-none');
                }

                if (!responsibilitiesValid && firstResponsibilityError) {
                    firstError = firstError || firstResponsibilityError;
                }

                return responsibilitiesValid;
            }

            // Validate Job Specifications
            function validateJobSpecs() {
                let specsValid = true;
                let firstSpecError = null;
                
                $('#job-specifications-table tbody tr').each(function() {
                    const qualificationInput = $(this).find('input[name="qualification[]"]');
                    const experienceInput = $(this).find('input[name="experience[]"]');
                    const qualificationText = qualificationInput.val().trim();
                    const experienceText = experienceInput.val().trim();
                    const wordCount = countWords(qualificationText);
                    const charCount = qualificationText.length;
                    
                    // All rows in the table are required - just check that they're not empty
                    if (!qualificationText) {
                        qualificationInput.addClass('is-invalid');
                        qualificationInput.siblings('.invalid-feedback').addClass('show').text('Qualification is required');
                        specsValid = false;
                        firstSpecError = firstSpecError || qualificationInput;
                    } else if (charCount > 3000) {
                        qualificationInput.addClass('is-invalid');
                        qualificationInput.siblings('.invalid-feedback').addClass('show').text('Maximum 3000 characters allowed');
                        specsValid = false;
                        firstSpecError = firstSpecError || qualificationInput;
                    }
                    
                    // Experience is required for all rows
                    if (!experienceText) {
                        experienceInput.addClass('is-invalid');
                        experienceInput.siblings('.invalid-feedback').addClass('show').text('Experience is required');
                        specsValid = false;
                        firstSpecError = firstSpecError || experienceInput;
                    } else if (!experienceInput.val().match(/^\d+$/)) {
                        experienceInput.addClass('is-invalid');
                        experienceInput.siblings('.invalid-feedback').addClass('show').text('Please enter valid experience in years (positive numbers only)');
                        specsValid = false;
                        firstSpecError = firstSpecError || experienceInput;
                    }
                });
                
                if (!specsValid && firstSpecError) {
                    firstError = firstError || firstSpecError;
                }
                
                return specsValid;
            }

            // Validate Multidisciplinary Interests
            function validateInterests() {
                let interestsValid = true;
                let firstInterestError = null;
                
                $('#multi-interest-table tbody tr').each(function() {
                    const interestInput = $(this).find('input[name="multi_interest[]"]');
                    const skillsInput = $(this).find('input[name="required_skills[]"]');
                    const interestText = interestInput.val().trim();
                    const skillsText = skillsInput.val().trim();
                    const interestChars = interestText.length;
                    const skillsChars = skillsText.length;
                    
                    // All rows in the table are required
                    if (!interestText) {
                        interestInput.addClass('is-invalid');
                        interestInput.siblings('.invalid-feedback').addClass('show').text('Area of interest is required');
                        interestsValid = false;
                        firstInterestError = firstInterestError || interestInput;
                    } else if (interestChars > 3000) {
                        interestInput.addClass('is-invalid');
                        interestInput.siblings('.invalid-feedback').addClass('show').text('Maximum 3000 characters allowed');
                        interestsValid = false;
                        firstInterestError = firstInterestError || interestInput;
                    }
                    
                    // Skills are required for all rows
                    if (!skillsText) {
                        skillsInput.addClass('is-invalid');
                        skillsInput.siblings('.invalid-feedback').addClass('show').text('Required skills are required');
                        interestsValid = false;
                        firstInterestError = firstInterestError || skillsInput;
                    } else if (skillsChars > 3000) {
                        skillsInput.addClass('is-invalid');
                        skillsInput.siblings('.invalid-feedback').addClass('show').text('Maximum 3000 characters allowed');
                        interestsValid = false;
                        firstInterestError = firstInterestError || skillsInput;
                    }
                });
                
                if (!interestsValid && firstInterestError) {
                    firstError = firstError || firstInterestError;
                }
                
                return interestsValid;
            }

            // Run all validations
            const basicInfoValid = validateBasicInfo();
            const roleDescriptionValid = validateRoleDescription();
            const responsibilitiesValid = validateResponsibilities();
            const jobSpecsValid = validateJobSpecs();
            const interestsValid = validateInterests();

            isValid = basicInfoValid && roleDescriptionValid && responsibilitiesValid && 
                      jobSpecsValid && interestsValid;

            // Make sure all invalid feedback is visible
            $('.is-invalid').each(function() {
                $(this).siblings('.invalid-feedback').addClass('show');
            });

            // Scroll to first error if validation fails
            if (!isValid && firstError) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }

            return isValid;
        }

        // Real-time validation on input
        $(document).on('input', '.form-control', function() {
            const input = $(this);
            const words = countWords(input.val());
            const maxWords = input.data('max-words');

            // Clear previous error state
            input.removeClass('is-invalid');
            input.siblings('.invalid-feedback').hide();

            // Validate word count if applicable
            if (maxWords) {
                if (words > maxWords) {
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').show()
                        .text(`Maximum ${maxWords} words allowed`);
                }
            }

            // Update word count display if applicable
            const wordCountDisplay = input.siblings('.word-count-info');
            if (wordCountDisplay.length) {
                wordCountDisplay.text(`Words: ${words}/${maxWords} (${maxWords - words} remaining)`);
            }
        });

        // Prevent paste if it would exceed word limit
        $(document).on('paste', '.word-count-input', function(e) {
            const maxWords = $(this).data('max-words');
            
            // Get current text and pasted text
            const currentText = $(this).val();
            const pastedText = e.originalEvent.clipboardData.getData('text');
            
            // Combine current and pasted text
            const combinedText = currentText + pastedText;
            const wordCount = combinedText.trim().split(/\s+/).filter(word => word.length > 0).length;
            
            if (wordCount > maxWords) {
                e.preventDefault();
                // Show a temporary message that paste was blocked
                const $input = $(this);
                const originalMessage = $input.siblings('.invalid-feedback').text();
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text('Cannot paste: would exceed 150 character limit').show();
                
                // Reset the message after 2 seconds
                setTimeout(() => {
                    if ($input.siblings('.invalid-feedback').text() === 'Cannot paste: would exceed 150 character limit') {
                        $input.siblings('.invalid-feedback').text(originalMessage);
                        if (countWords($input.val()) <= maxWords) {
                            $input.removeClass('is-invalid');
                        }
                    }
                }, 2000);
            }
        });

        // Also prevent drag and drop if it would exceed limit
        $(document).on('drop', '.word-count-input', function(e) {
            const maxWords = $(this).data('max-words');
            
            // Get current text and dropped text
            const currentText = $(this).val();
            const droppedText = e.originalEvent.dataTransfer.getData('text');
            
            // Combine current and dropped text
            const combinedText = currentText + droppedText;
            const wordCount = combinedText.trim().split(/\s+/).filter(word => word.length > 0).length;
            
            if (wordCount > maxWords) {
                e.preventDefault();
                // Show a temporary message that drop was blocked
                const $input = $(this);
                const originalMessage = $input.siblings('.invalid-feedback').text();
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text('Cannot drop: would exceed 150 character limit').show();
                
                // Reset the message after 2 seconds
                setTimeout(() => {
                    if ($input.siblings('.invalid-feedback').text() === 'Cannot drop: would exceed 150 character limit') {
                        $input.siblings('.invalid-feedback').text(originalMessage);
                        if (countWords($input.val()) <= maxWords) {
                            $input.removeClass('is-invalid');
                        }
                    }
                }, 2000);
            }
        });

        // Apply CSS to make sure invalid-feedback elements are displayed
        const style = document.createElement('style');
        style.textContent = `
            .invalid-feedback.show {
                display: block !important;
            }
            .word-limit-exceeded {
                color: red !important;
            }
        `;
        document.head.appendChild(style);

        // Form submission handler
        $('#job-description-form').submit(function(e) {
            e.preventDefault(); // Prevent form from submitting normally
            
            // Reset all error states
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').removeClass('show').hide();
            $('.alert-danger').addClass('d-none');
            
            // Run complete validation
            const isValid = validateForm();
            
            // Ensure all invalid-feedback elements are shown
            $('.is-invalid').each(function() {
                $(this).siblings('.invalid-feedback').addClass('show');
            });
            
            if (!isValid) {
                return false; // Stop form submission if validation fails
            }

            // If validation passes, proceed with AJAX submission
            // Show loading state
            $('#submit-text').text('Submitting...');
            $('#spinner').removeClass('d-none');

            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action') || '{{ route("survey/job-description") }}',
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    "_token": "{{ csrf_token() }}",
                    'Authorization': "Bearer {{ auth()->user()->api_token ?? '' }}",
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Success', response.message, 'success');
                        if (response.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 3000);
                        }
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        Object.keys(response.errors).forEach(function(key) {
                            const field = $(`[name="${key}"]`);
                            field.addClass('is-invalid');
                            field.siblings('.invalid-feedback').addClass('show').text(response.errors[key][0]);
                        });
                    } else {
                        showToast('Error', 'An error occurred while submitting the form', 'error');
                    }
                },
                complete: function() {
                    $('#submit-text').text('Submit');
                    $('#spinner').addClass('d-none');
                }
            });

            return false; // Prevent form submission
        });
    });
</script>
