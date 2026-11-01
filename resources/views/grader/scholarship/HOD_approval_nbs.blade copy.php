@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

@include('components.toast-notifications')

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- Add custom CSS for theme-consistent styling -->
    <style>
        .theme-bg {
            background-color: #1548a3 !important;
            color: white !important;
        }
        
        .theme-text {
            color: #1548a3 !important;
        }
        
        .theme-border {
            border-color: #1548a3 !important;
        }
        
        .theme-btn {
            color: #1548a3 !important; 
            border: 1px solid #1548a3 !important;
            background-color: transparent !important;
            transition: all 0.3s !important;
        }
        
        .theme-btn:hover {
            background-image: linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%) !important;
        }
        
        .validation-alert {
            border-left: 4px solid #1548a3 !important;
            background-color: #f8f9fa !important;
            color: #333 !important;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            border: none !important;
        }
        
        .card-header {
            border-bottom: 2px solid #1548a3 !important;
        }
        
        .table thead th {
            border-bottom: 2px solid #1548a3 !important;
        }
        
        .btn-group .btn {
            margin-right: 5px;
        }
        
        /* Animation for filter actions */
        @keyframes highlight {
            0% { background-color: transparent; }
            50% { background-color: rgba(21, 72, 163, 0.1); }
            100% { background-color: transparent; }
        }
        
        .highlight-animation {
            animation: highlight 1s ease-in-out;
        }
    
    /* Custom styles for select dropdowns */
    .form-select {
        width: 400px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-select:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Modal specific styles */
    .modal-backdrop {
        z-index: 1040;
    }
    
    .modal {
        z-index: 1050;
    }
    
    .modal-dialog {
        pointer-events: auto;
    }
    

    .modal-dialog.modal-half {
    max-width: 50%; /* Half of the screen width */
    margin: auto;
}

.modal-content {
    height: auto;
    max-height: 90vh;
    overflow-y: auto;
}



</style>


@php
$admissionApprovalUsernames = ['asbah.rehman', 'sidra.tanveer'];
$currentUsername = auth()->user()->username;
$isAdmissionApprovalUser = in_array($currentUsername, $admissionApprovalUsernames);
@endphp



<div class="row mt-4" style="margin:5px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            {{-- <div class="card-header">
                <h3 class="card-title">Scholarship Report</h3>
            </div> --}}
            <div class="card-body pb-2">
                <form id="scholarshipForm" method="post">
                    <!-- Application Session Dropdown -->
                    <div class="form-group row">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Application Session</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="application_sess_id" name="application_sess_id" class="form-select" required>
                                <option value="">Select Application Session</option>
                            </select>
                        </div>
                    </div>

                    <!-- Roadmap Session Dropdown -->
                    <div class="form-group row" style="display:none">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Roadmap Session</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="ad_sess_id" name="ad_sess_id" class="form-select" required>
                                <option value="">Select Roadmap Session</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Applicants / Student</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="student_type" name="student_type" class="form-select" required>
                                 <option value="">Select Student Type</option>
                                <option value="existing">Existing</option>
                                 <option value="new_applicants">New Applicants</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" style="display: none;" id="adm_sch_div">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Admission Schedule</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="adm_sch" name="adm_sch" class="form-select" required>
                                <option value="">Select Admission Schedule</option>
                                <option value="1">Early Round</option>
                                 <option value="2">Regular Round</option>
                                  <option value="3">Rolling Round</option>
                            </select>
                        </div>
                    </div>

                    <!-- School Dropdown -->
                    <div class="form-group row"  style="margin-bottom:0px;display:none">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">School</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="ad_school_id" name="ad_school_id" class="form-select" required>
                                <option value="">---All Schools---</option>
                            </select>
                        </div>
                    </div>

                    <!-- Admission Schedules Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px; display:none;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Admission Schedules</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="admission_sch" name="admission_sch" class="form-select" required>
                                <option value="">Select Admission Schedule</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden dropdowns -->
                    <div class="form-group row" style="margin-bottom:0px; display:none;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Selected Applicant</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="selected_filter_type" name="selected_filter_type" class="form-select">
                                <option value="">All</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" style="margin-bottom:0px; display:none;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Fee Status</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="status_filter_type" name="status_filter_type" class="form-select">
                                <option value="">All</option>
                                <option value="Verified Applicants">Verified Applicants</option>
                                <option value="Non Verified Applicants">Non Verified Applicants</option>
                            </select>
                        </div>
                    </div>

                </form>

                <!-- Action Buttons -->
                <div class="form-group row mt-3">
                    <div class="col-lg-3" style="text-align: right">
                        <label class="col-form-label"></label>
                    </div>
                    <div class="col-lg-8">
        <button type="submit" 
                            style=" display:none; color: #1548a3; border: 1px solid #1548a3; background-color: transparent; margin-right: 10px;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
                            <i class="fa fa-file-excel-o"></i> Generate Excel
        </button>    

        <button type="button" onclick="applyScholarshipFilter()" 
                            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent; margin-right: 10px;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
            <i class="fa fa-filter"></i> Apply Filter
        </button>

        <button type="button" onclick="resetFilters()" 
            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
                            <i class="fa fa-refresh"></i> Reset
        </button>
                    </div>
                </div>
	</form>

     <!-- Add validation alert div with close button -->
 <div id="validationAlert" class="alert validation-alert" role="alert" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-exclamation-circle theme-text mr-2"></i> 
                <span id="validationMessage"></span>
            </div>
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
            </div>
        </div>
    </div>
            </div>

<div class="container mt-4" style="max-width: 2000px !important">
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 theme-text">
                    <i class="fa fa-graduation-cap mr-2"></i> Scholarship Applicants List
                </h4>
                <div id="filterStatus" class="small text-muted" style="display: none;">
                    <i class="fa fa-filter mr-1"></i> Filters applied
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="scholarshipTable" class="table table-bordered table-striped table-hover text-center display responsive nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col"><i class="fa fa-id-card mr-1"></i> Form ID</th>
                            <th scope="col"><i class="fa fa-percent mr-1"></i> Percentage</th>
                            <th scope="col"><i class="fa fa-check-circle mr-1"></i> Verify</th>
                            <th scope="col"><i class="fa fa-cogs mr-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows will be dynamically injected by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90vw; margin: 1.75rem auto;">
        <div class="modal-content">
            <div class="modal-header theme-bg">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fa fa-eye mr-2"></i>Applicant Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 85vh;">
                <iframe id="previewFrame" style="width: 100%; height: 100%; border: none;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script>
// Initialize DataTable
let scholarshipTable;
let staffUrl = "{{ config('app.staff_url') }}";
// Load Sessions and Schools on page load
$(document).ready(function() {
    // Load Application Sessions
    function loadApplicationSessions() {
        $.ajax({
            url: '<?= url('/sessions') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#application_sess_id');
                select.empty().append('<option value="">Select Application Session</option>');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.sess_id)
                        .text(item.sess_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading application sessions:', error);
                showToast('error', 'Error!', 'Failed to load application sessions');
            }
        });
    }

    $('#student_type').on('change', function () {
        toggleAdmissionSchedule();
        applyScholarshipFilter();
    });

    function toggleAdmissionSchedule() {
        const selected = $('#student_type').val();
        if (selected === 'new_applicants') {
            $('#adm_sch_div').show();
        } else {
            $('#adm_sch_div').hide();
        }
    }

    $('#adm_sch').on('change', function () {
        
        applyScholarshipFilter();
    });

    // Load Roadmap Sessions
    function loadSessions() {
        $.ajax({
            url: '<?= url('/sessions') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#ad_sess_id');
                select.empty().append('<option value="">Select Roadmap Session</option>');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.sess_id)
                        .text(item.sess_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading sessions:', error);
            }
        });
    }

    // Call functions to load sessions
    loadApplicationSessions();
    loadSessions();
    
    // Apply theme styling to buttons
    $('.btn').addClass('theme-btn');
    
    // Add nice transition effect to all buttons
    $('.btn').on('mouseenter', function() {
        $(this).css('transform', 'translateY(-2px)');
    }).on('mouseleave', function() {
        $(this).css('transform', 'translateY(0)');
    });
    
    // Close alert when clicking the close button
    $(document).on('click', '.alert .close', function() {
        $(this).closest('.alert').hide();
    });

    // Add onchange handlers to all filter dropdowns
    $('#application_sess_id, #ad_sess_id, #ad_school_id, #admission_sch, #selected_filter_type, #status_filter_type').on('change', function() {
        applyScholarshipFilter();
    });
    // Optionally hide the Apply Filter button if still present
    $("button:contains('Apply Filter')").hide();
    
    // Allow modal to close properly
    $('#approveModal').on('hidden.bs.modal', function() {
        // Clear form fields when modal is closed
        $('#approve_percentage').val('');
        $('#approve_remarks').val('');
        $('#approveModalAlert').hide();
    });

    if ($('#ad_school_id').val() == '') {
        $('#ad_school_id').trigger('change');
    }





    // Real-time percentage validation
    $(document).on('input', '#approve_percentage', function() {
        const input = $(this);
        const value = input.val();
        const errorDiv = $('#percentage-error');
        
        // Remove any non-numeric characters except decimal point
        let cleanValue = value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        const decimalCount = (cleanValue.match(/\./g) || []).length;
        if (decimalCount > 1) {
            cleanValue = cleanValue.replace(/\.+$/, ''); // Remove trailing dots
            const parts = cleanValue.split('.');
            cleanValue = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (cleanValue.includes('.')) {
            const parts = cleanValue.split('.');
            if (parts[1].length > 2) {
                cleanValue = parts[0] + '.' + parts[1].substring(0, 2);
            }
        }
        
        // Update input value
        input.val(cleanValue);
        
        // Validate range
        const numValue = parseFloat(cleanValue);
        
        if (cleanValue === '' || cleanValue === '.') {
            // Empty or just decimal point - remove error
            input.removeClass('is-invalid');
            errorDiv.hide();
        } else if (isNaN(numValue)) {
            // Invalid number
            input.addClass('is-invalid');
            errorDiv.text('Please enter a valid number.').show();
        } else if (numValue < 0) {
            // Less than 0
            input.addClass('is-invalid');
            errorDiv.text('Percentage cannot be less than 0.').show();
        } else if (numValue > 100) {
            // Greater than 100
            input.addClass('is-invalid');
            errorDiv.text('Percentage cannot be greater than 100.').show();
        } else {
            // Valid percentage
            input.removeClass('is-invalid');
            errorDiv.hide();
        }
    });
    
    // Prevent paste of invalid characters
    $(document).on('paste', '#approve_percentage', function(e) {
        e.preventDefault();
        const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        const cleanText = pastedText.replace(/[^0-9.]/g, '');
        
        if (cleanText) {
            const numValue = parseFloat(cleanText);
            if (!isNaN(numValue) && numValue >= 0 && numValue <= 100) {
                $(this).val(cleanText);
            }
        }
    });
    
    // Prevent keydown of invalid characters
    $(document).on('keydown', '#approve_percentage', function(e) {
        const key = e.key;
        const value = $(this).val();
        
        // Allow: backspace, delete, tab, escape, enter, decimal point, numbers
        if ([8, 9, 27, 13, 46, 110, 190].indexOf(e.keyCode) !== -1 ||
            // Allow numbers
            (e.keyCode >= 48 && e.keyCode <= 57) ||
            // Allow numpad numbers
            (e.keyCode >= 96 && e.keyCode <= 105)) {
            return;
        }
        
        // Allow decimal point only once
        if (key === '.' && !value.includes('.')) {
            return;
        }
        
        // Block all other keys
        e.preventDefault();
    });
});

// Function to load sessions
function loadSessions() {
    $.ajax({
        url: '<?= url('/sessions') ?>',
        type: 'GET',
        success: function(data) {
            var select = $('#ad_sess_id');
            select.empty().append('<option value="">Select Roadmap Session</option>');
            data.forEach(function(item) {
                select.append($('<option></option>')
                    .attr('value', item.sess_id)
                    .text(item.sess_name));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading sessions:', error);
        }
    });
}

// Function to load schools
function loadSchools() {
    $.ajax({
        url: '<?= url('/schools') ?>',
        type: 'GET',
        success: function(data) {
            var select = $('#ad_school_id');
            select.empty().append('<option value="">---All Schools---</option>');
            data.forEach(function(item) {
                select.append($('<option></option>')
                    .attr('value', item.school_id)
                    .text(item.school_name));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading schools:', error);
        }
    });
}

// Session Change Event
$('#ad_sess_id').change(function() {
    var sessId = $(this).val();
    
    // Clear dependent dropdowns
    $('#ad_school_id').empty().append('<option value="">---All Schools---</option>');
    $('#admission_sch').empty().append('<option value="">Select Admission Schedule</option>');
    
    // Load schools
    if(sessId) {
        $.ajax({
            url: '<?= url('/schools') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#ad_school_id');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.school_id)
                        .text(item.school_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading schools:', error);
            }
        });
    }
});

// School Change Event
$('#ad_school_id').change(function() {
    var schoolId = $(this).val();
    var sessId = $('#ad_sess_id').val();
    
    // Clear admission schedules dropdown
    $('#admission_sch').empty().append('<option value="">Select Admission Schedule</option>');
    
    // Load admission schedules
    if(schoolId && sessId) {
        $.ajax({
            url: '<?= url('/api/adm_schedules') ?>',
            type: 'GET',
            data: { school_id: schoolId, sess_id: sessId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
                    },
            success: function(data) {
                var select = $('#admission_sch');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.admission_sch_id)
                        .text(item.admission_sch_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading admission schedules:', error);
            }
        });
    }
});

function applyScholarshipFilter() {
    // Hide any previous validation messages
    $('#validationAlert').hide();
    
    // Check required fields  
    var sessionId = $('#application_sess_id').val();
    var student_type = $('#student_type').val();
    var admissionSch = $('#adm_sch').val();
    // alert(admissionSch);
    // return false;
    // Remove scholarshipType and scholarshipFilterType validation
    // if (!sessionId || sessionId === '') {
    //     showToast('danger', 'Error!', 'Please select a Session');
    //     return;
    // }
    


    // Show loading overlay or spinner
    if (scholarshipTable) {
        scholarshipTable.destroy();
    }

    $('#scholarshipTable').addClass('loading');
    $('#filterStatus').show(); // Show the filter status indicator
    
    const data = {
        ad_sess_id: sessionId,
        student_type: student_type,
        admission_sch: admissionSch,
       
    };

    $.ajax({
        url: '/api/get-data-nbs-form-hod-approval',
        method: 'GET',
        data: data,
        headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'), // Add CSRF token
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
                    },
        dataType: 'json',
        success: function(response) {
            $('#scholarshipTable').removeClass('loading');
            
            // Process the data to match DataTables format
            if (response && response.status && Array.isArray(response.data)) {
                const processedData = processDataForTable(response.data);
                initializeDataTable(processedData, sessionId);
            } else {
                // Handle error or empty response
                initializeDataTable([]);
                console.error("Invalid response format:", response);
            }
        },
        error: function(xhr, status, error) {
            $('#scholarshipTable').removeClass('loading');
            initializeDataTable([]);
            console.error("AJAX Error:", error);
            if (xhr.responseText) {
                console.error("Server response:", xhr.responseText);
            }
        }
    });
}

// Update processDataForTable function to handle the conditional display
function processDataForTable(data, scholarshipType=null) {
    const sessionId = $('#ad_sess_id').val();
 
    return data.map(function(applicant) {
        // Action buttons (Excel and Preview)
        const actionButtons = `
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-info preview-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    
                    data-session-id="${sessionId || ''}"
                    data-sai-enrollment-no="${applicant.sai_enrollment_no || ''}">
                    <i class="fa fa-eye mr-1"></i> Preview
                </button>
            </div>`;

        // Percentage display
        const percentage = applicant.percentage || applicant.percentage === 0 ? applicant.percentage : 'N/A';
        const percentageDisplay = percentage === 'N/A' ? '<span class="text-muted">N/A</span>' : percentage + '%';

        // Check if record is already approved
        const isApproved = applicant.percentage && applicant.percentage !== '' && applicant.percentage > 0  && applicant.percentage !== 'N/A';
        
        // Decide which button to show
        const approveBtn = isApproved 
            ? `<span class="badge badge-success" disabled>Approved</span>
            <button class="btn btn-sm btn-info approve-btn" 
                data-spd-id="${applicant.spd_id}" 
                data-aai-id="${applicant.aai_id}" 
                data-current-percentage="${percentage}"
                data-form-id="${applicant.form_id || ''}">
                <i class="fa fa-check-circle"></i> Update Percentage
               </button>
            `
            : `<button class="btn btn-sm btn-success approve-btn" 
                data-spd-id="${applicant.spd_id}" 
                data-aai-id="${applicant.aai_id}" 
                data-current-percentage="${percentage}"
                data-form-id="${applicant.form_id || ''}">
                <i class="fa fa-check-circle"></i> Approve
               </button>`;

        // Return formatted row data without Remarks column
        return [
            applicant.ref_id,
            percentageDisplay,
            approveBtn,
            actionButtons
        ];
    });
}

// Initialize DataTable with processed data
function initializeDataTable(data, sessionId) {
    // Destroy existing table if it exists
    if ($.fn.DataTable.isDataTable('#scholarshipTable')) {
        $('#scholarshipTable').DataTable().destroy();
    }

    // Clear the table body
    $('#scholarshipTable tbody').empty();

    // Initialize new DataTable
    scholarshipTable = $('#scholarshipTable').DataTable({
        data: data,
        responsive: true,
        processing: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table",
            paginate: {
                first: "First",
                last: "Last", 
                next: "Next",
                previous: "Previous"
            },
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        columnDefs: [
            { responsivePriority: 1, targets: [0, 3] },  // Form ID and Actions are most important
            { responsivePriority: 2, targets: [1] },     // Percentage is next
            { responsivePriority: 3, targets: [2] }      // Verify is least important
        ],
        drawCallback: function(settings) {
            // Re-initialize any tooltips or popovers if needed
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        },
        initComplete: function(settings, json) {
            // Any additional initialization after table is loaded
            $('#scholarshipTable').show();
        }
    });

    // Add event listener for preview buttons
    $('#scholarshipTable').on('click', '.preview-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const sessionId = $(this).data('session-id');
        // const saiEnrollmentNo = $(this).data('sai-enrollment-no');
        

        // const checkSaiEnrollmentNo = saiEnrollmentNo == '' || saiEnrollmentNo == null;
        const excelUrl = checkSaiEnrollmentNo 
            ? `${staffUrl}/HOD/nbs_excel_admin.php` 
            : `${staffUrl}/HOD/nbs_excel_ro.php`;

        const fullUrl = `${excelUrl}?applicant_id=${applicantId}&ad_sess_id=${sessionId}&hodPreview=1`;
        
        // Show loading state
        $('#previewFrame').attr('src', '');
        $('#previewModal').modal('show');
        
        // Set iframe source after modal is shown
        setTimeout(() => {
            $('#previewFrame').attr('src', fullUrl);
        }, 300);
    });
}

// Make sure DOM is fully loaded before initializing
$(document).ready(function() {
    // Initial table setup with empty data
    initializeDataTable([], null);
    
    // Load initial data if needed
    applyScholarshipFilter();
    
    // Add event handlers for filter changes
    $('#application_sess_id, #ad_sess_id, #ad_school_id, #admission_sch, #selected_filter_type, #status_filter_type').on('change', function() {
        applyScholarshipFilter();
    });
});

// Add some custom CSS for loading state
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }
        .loading:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin-top: -20px;
            margin-left: -20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `)
    .appendTo('head');

// Add JavaScript function for reset button
function resetFilters() {
    // Clear all select selections
    $('#application_sess_id').val('').trigger('change');
    $('#ad_sess_id').val('').trigger('change');
    // $('#ad_school_id').val('').trigger('change');
    // $('#admission_sch').val('').trigger('change');
    // $('#selected_filter_type').val('').trigger('change');
    // $('#status_filter_type').val('').trigger('change');
    
    loadSchools();

    // Clear the table if it exists
    if (scholarshipTable) {
        scholarshipTable.clear().draw();
    }
}

// Add this function for exporting all data
function exportTableToExcel() {
    // Get current filter parameters
    const data = {
        ad_sess_id: $('#ad_sess_id').val(),
        ad_school_id: $('#ad_school_id').val(),
        admission_sch: $('#admission_sch').val(),
        scholarship_type: $('#scholarship_type').val(),
        scholarship_filter_type: $('#scholarship_filter_type').val(),
        selected_filter_type: $('#selected_filter_type').val(),
        status_filter_type: $('#status_filter_type').val(),
        export_all: 'true'
    };
    
    // Redirect to a page that will generate the Excel file with all applicants
    window.open('scholarship_export_all.php?' + $.param(data), '_blank');
}


function submitHODApproval(params = null) {
    let spdId, percentage, remarks, action, aai_id;
    
    if (params) {
        // Disapprove case (direct call)
        spdId = params.spd_id;
        percentage = params.percentage;
        remarks = params.hod_remarks;
        action = params.action;
        aai_id = params.aai_id;
    } else {
        // Approve case (from modal)
        spdId = $('#approve_spd_id').val();
        aai_id = $('#apply_id').val();
        percentage = $('#approve_percentage').val();
        remarks = $('#approve_remarks').val();
        action = 'approve';
    }

    // ✅ Validate only if approving
    if (action === 'approve') {
        let isValid = true;
        let errorMessage = '';

        if (!percentage || percentage === '') {
            $('#approve_percentage').addClass('is-invalid');
            $('#percentage-error').text('Please enter percentage.').show();
            isValid = false;
            errorMessage = 'Please enter percentage.';
        } else {
            const numPercentage = parseFloat(percentage);
            if (isNaN(numPercentage)) {
                $('#approve_percentage').addClass('is-invalid');
                $('#percentage-error').text('Please enter a valid number.').show();
                isValid = false;
                errorMessage = 'Please enter a valid number.';
            } else if (numPercentage < 0 || numPercentage > 100) {
                $('#approve_percentage').addClass('is-invalid');
                $('#percentage-error').text('Percentage must be between 0 and 100.').show();
                isValid = false;
                errorMessage = 'Percentage must be between 0 and 100.';
            } else {
                $('#approve_percentage').removeClass('is-invalid');
                $('#percentage-error').hide();
            }
        }

        if (!remarks || remarks.trim() === '') {
            $('#approve_remarks').addClass('is-invalid');
            isValid = false;
            errorMessage += ' Please enter HOD remarks.';
        } else {
            $('#approve_remarks').removeClass('is-invalid');
        }

        if (!isValid) {
            showToast('danger', 'Error!', errorMessage);
            return;
        }
    }

    // ✅ Show loading for Approve button (modal)
    const approveBtn = $('#approveModal .btn-success');
    const originalText = approveBtn.html();
    if (action === 'approve') {
        approveBtn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Processing...');
        approveBtn.prop('disabled', true);
    }

    // ✅ AJAX request
    $.ajax({
        url: '/api/hod-approve-scholarship',
        method: 'POST',
        data: {
            spd_id: spdId,
            percentage: percentage,
            hod_remarks: remarks,
            action: action,
            applicant_id: aai_id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            if (response.status) {
                showToast('success', 'Success!', action === 'approve' ? 'Scholarship approved successfully!' : 'Scholarship disapproved successfully!');
                
                if (action === 'approve') {
                    $('#approveModal').modal('hide');
                    $('#approve_percentage').val('');
                    $('#approve_remarks').val('');
                }
                // ✅ Refresh the table data
                setTimeout(function() {
                    applyScholarshipFilter();
                }, 500);
            } else {
                showToast('danger', 'Error!', response.message || 'Failed to process request.');
            }
        },
        error: function(xhr) {
            let errorMsg = 'An error occurred while processing your request.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast('danger', 'Error!', errorMsg);
        },
        complete: function() {
            if (action === 'approve') {
                approveBtn.html(originalText);
                approveBtn.prop('disabled', false);
            }
        }
    });
}



function generatePreview(response) {
    const applicant = response.data || {};
    const siblings = response.siblings || [];
    const employed = response.employed || [];
    const unemployed = response.unemployed || [];
    const transport = response.transport || [];

    // Check Data Conditions
    const hasSiblingData = siblings && siblings.length > 0 && siblings.some(s => s.sibling_name && s.sibling_name.trim());
    const hasEmployedData = employed && employed.length > 0 && employed.some(e => e.employed_name && e.employed_name.trim());
    const hasUnemployedData = unemployed && unemployed.length > 0 && unemployed.some(u => u.unemployed_name && u.unemployed_name.trim());
    const hasTransportData = transport && transport.length > 0 && transport.some(t => t.vehicle_type && t.vehicle_type.trim());

    // List of fields to exclude
    const excludedFields = [
        'aai_id',
        'Applicant_name',
        'api_id',
        'registration_no',
        'father_name',
        'phone_no',
        'guardian_mobile_no',
        'school_name',
        'marks',
        'degree_certificate',
        'institution_name',
        'grades',
        'parent_name',
        'occupation',
        'office_phone',
        'designation',
        'organization_name',
        'fax_no',
        'residence_phone',
        'mobile_no',
        'residence_address',
        'residence_city',
        'office_address',
        'office_city',
        'sai_enrollment_no',
        'std_mobile_no'
    ];

    let html = `<div class="container-fluid">`;

    // Applicant Information Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E3F2FD; color: #1976D2;">
                <h5 class="mb-0"><i class="fa fa-user mr-2"></i>Applicant Information</h5>
            </div>
            <div class="card-body">
                <div class="row">`;
    
    // Loop through all applicant data fields, excluding the specified ones
    Object.entries(applicant).forEach(([key, value]) => {
        if (!excludedFields.includes(key.toLowerCase())) {
            html += `
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small">
                        <i class="fa fa-tag mr-1"></i>${key.replace(/_/g, ' ').toUpperCase()}
                    </label>
                    <div class="form-control form-control-sm">${value || 'N/A'}</div>
                </div>`;
        }
    });

    html += `
                </div>
            </div>
        </div>`;

    // Siblings Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E8F5E9; color: #2E7D32;">
                <h5 class="mb-0"><i class="fa fa-users mr-2"></i>Siblings Information</h5>
            </div>
            <div class="card-body">`;
    
    if (hasSiblingData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>School/College</th>
                            <th>Fee</th>
                            <th>Scholarship</th>
                            <th>Parent Contribution</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        siblings.forEach(sibling => {
            if (sibling.sibling_name && sibling.sibling_name.trim()) {
                html += `
                    <tr>
                        <td>${sibling.sibling_name || 'N/A'}</td>
                        <td>${sibling.sibling_age || 'N/A'}</td>
                        <td>${sibling.sibling_marital_status || 'N/A'}</td>
                        <td>${sibling.school_college_name || 'N/A'}</td>
                        <td>${sibling.annual_fee || 'N/A'}</td>
                        <td>${sibling.annual_scholarship || 'N/A'}</td>
                        <td>${sibling.annual_parents_contribution || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No siblings data found for this Applicant.
            </div>`;
    }
    html += `</div></div>`;

    // Employed Family Members Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #FFF3E0; color: #F57C00;">
                <h5 class="mb-0"><i class="fa fa-briefcase mr-2"></i>Employed Family Members</h5>
            </div>
            <div class="card-body">`;
    
    if (hasEmployedData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>Profession</th>
                            <th>Annual Income</th>
                            <th>Annual Contribution</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        employed.forEach(emp => {
            if (emp.employed_name && emp.employed_name.trim()) {
                html += `
                    <tr>
                        <td>${emp.employed_name || 'N/A'}</td>
                        <td>${emp.employed_age || 'N/A'}</td>
                        <td>${emp.employeed_marital_status || 'N/A'}</td>
                        <td>${emp.profession || 'N/A'}</td>
                        <td>${emp.annual_income || 'N/A'}</td>
                        <td>${emp.annual_contribution || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No employed family members data found.
            </div>`;
    }
    html += `</div></div>`;

    // Unemployed Family Members Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #FCE4EC; color: #C2185B;">
                <h5 class="mb-0"><i class="fa fa-user-times mr-2"></i>Unemployed Family Members</h5>
            </div>
            <div class="card-body">`;
    
    if (hasUnemployedData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>Reason of Unemployment</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        unemployed.forEach(unemp => {
            if (unemp.unemployed_name && unemp.unemployed_name.trim()) {
                html += `
                    <tr>
                        <td>${unemp.unemployed_name || 'N/A'}</td>
                        <td>${unemp.unemployed_age || 'N/A'}</td>
                        <td>${unemp.unemployed_marital_status || 'N/A'}</td>
                        <td>${unemp.unemployment_reason || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No unemployed family members data found.
            </div>`;
    }
    html += `</div></div>`;

    // Transport Information Section
    html += `
        <div class="card mb-4">
            <div class="card-header" style="background: #E8EAF6; color: #3F51B5;">
                <h5 class="mb-0"><i class="fa fa-car mr-2"></i>Transport Information</h5>
            </div>
            <div class="card-body">`;
    
    if (hasTransportData) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Vehicle Type</th>
                            <th>Vehicle Model</th>
                            <th>Vehicle Year</th>
                            <th>Fuel Cost</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        transport.forEach(trans => {
            if (trans.vehicle_type && trans.vehicle_type.trim()) {
                html += `
                    <tr>
                        <td>${trans.vehicle_type || 'N/A'}</td>
                        <td>${trans.vehicle_model || 'N/A'}</td>
                        <td>${trans.vehicle_year || 'N/A'}</td>
                        <td>${trans.vehicle_fuel_cost || 'N/A'}</td>
                    </tr>`;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>`;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle mr-2"></i>No transport information found.
            </div>`;
    }
    html += `</div></div>`;

    html += `</div>`; // Close container-fluid

    document.getElementById('previewContent').innerHTML = html;
    $('#previewModal').modal('show');
}


</script>
@stop

