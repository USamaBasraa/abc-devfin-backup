@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

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
</style>

<div class="row mt-4" style="margin:5px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Scholarship Report</h3>
            </div>
            <div class="card-body pb-2">
                <form id="scholarshipForm" method="post">
                    <!-- Session Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Session</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="ad_sess_id" name="ad_sess_id" class="form-select" required>
                                <option value="">Select Session</option>
                            </select>
                        </div>
                    </div>

                    <!-- School Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">School</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="ad_school_id" name="ad_school_id" class="form-select" required>
                                <option value="">Select School</option>
                            </select>
                        </div>
                    </div>

                    <!-- Admission Schedules Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Admission Schedules</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="admission_sch" name="admission_sch" class="form-select" required>
                                <option value="">Select Admission Schedule</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scholarship Type Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Scholarship</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="scholarship_type" name="scholarship_type" class="form-select" required>
                                <option value="">Select Scholarship Type</option>
                                <option value="api_needbase_scholarship">Need Base Scholarship</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scholarship Filter Dropdown -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Scholarship Filter</label>
                        </div>
                        <div class="col-lg-8">
                            <select id="scholarship_filter_type" name="scholarship_filter_type" class="form-select" required>
                                <option value="">All</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden dropdowns -->
                    <div class="form-group row" style="margin-bottom:0px; display:none;">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Selected Student</label>
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
                                <option value="Verified Students">Verified Students</option>
                                <option value="Non Verified Students">Non Verified Students</option>
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
                            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent; margin-right: 10px;"
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
                            <th scope="col" class="text-left"><i class="fa fa-user mr-1"></i> Full Name</th>
                            <th scope="col"><i class="fa fa-id-card mr-1"></i> Applicant ID</th>
                            <th scope="col"><i class="fa fa-envelope mr-1"></i> Email</th>
                            <th scope="col"><i class="fa fa-university mr-1"></i> School</th>
                            <th scope="col"><i class="fa fa-book mr-1"></i> Program</th>
                            <th scope="col"><i class="fa fa-percent mr-1"></i> Percentage</th>
                            <th scope="col"><i class="fa fa-comment mr-1"></i> Remarks</th>
                            <th scope="col"><i class="fa fa-save mr-1"></i> Save</th>
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

@stop
@section('javascript')
<script>
// Initialize DataTable
let scholarshipTable;

// Load Sessions and Schools on page load
$(document).ready(function() {
    loadSessions();
    loadSchools();
    
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
        $(this).parent().parent().hide();
    });
});

// Function to load sessions
function loadSessions() {
    $.ajax({
        url: '<?= url('/sessions') ?>',
        type: 'GET',
        success: function(data) {
            var select = $('#ad_sess_id');
            select.empty().append('<option value="">Select Session</option>');
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
            select.empty().append('<option value="">Select School</option>');
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
    $('#ad_school_id').empty().append('<option value="">Select School</option>');
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
            url: '<?= url('/adm_schedules') ?>',
            type: 'GET',
            data: { school_id: schoolId, sess_id: sessId },
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
    var sessionId = $('#ad_sess_id').val();
    var admissionSch = $('#admission_sch').val();
    var scholarshipType = $('#scholarship_type').val();
    var scholarshipFilterType = $('#scholarship_filter_type').val();

    // Validate required fields
    if (!sessionId || sessionId === '') {
        showValidationMessage('Please select a Session');
        return;
    }
    
    if (!admissionSch || admissionSch === '') {
        showValidationMessage('Please select an Admission Schedule');
        return;
    }
    
    if (!scholarshipType || scholarshipType === '') {
        showValidationMessage('Please select a Scholarship Type');
        return;
    }
    
    if (!scholarshipFilterType || scholarshipFilterType === '') {
        showValidationMessage('Please select a Scholarship Filter');
        return;
    }

    // Show loading overlay or spinner
    if (scholarshipTable) {
        scholarshipTable.destroy();
    }

    $('#scholarshipTable').addClass('loading');
    $('#filterStatus').show(); // Show the filter status indicator
    
    const data = {
        ad_sess_id: sessionId,
        ad_school_id: $('#ad_school_id').val(),
        admission_sch: admissionSch,
        scholarship_type: scholarshipType,
        scholarship_filter_type: scholarshipFilterType,
        selected_filter_type: $('#selected_filter_type').val(),
        status_filter_type: $('#status_filter_type').val()
    };

    $.ajax({
        url: '/api/get-data-nbs-form',
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
                const processedData = processDataForTable(response.data, data.scholarship_type);
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

// Process data for DataTable format
function processDataForTable(data, scholarshipType) {
    const sessionId = $('#ad_sess_id').val();
    
    return data.map(function(applicant) {
        // Create action buttons with icons
        const actionButtons = `
            <div class="btn-group" role="group">
                <button class="btn btn-sm theme-btn download-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    data-scholarship-type="${scholarshipType || ''}"
                    data-session-id="${sessionId || ''}">
                    <i class="fa fa-file-excel-o mr-1"></i> Excel
                </button>
            </div>`;
        percentageInput = `
            <div style="display: flex; align-items: center; gap: 5px;">
                <input type="number" value="${applicant.percentage || ''}"  class="form-control form-control-sm percentage-input"
                    data-applicant-id="${applicant.aai_id}" placeholder="%" style="width: 70px;" />
                <i class="fa fa-check-circle text-success tick-icon" 
                    style="display: none; font-size: 16px;"></i>
            </div>`;
        remarksInput = `
            <input type="text" value="${applicant.remarks || ''}"  class="form-control form-control-sm remarks-input" 
                data-applicant-id="${applicant.aai_id}" placeholder="Remarks" style="width: 150px;" />`;

        const saveButton = `<button class="btn btn-sm btn-success save-btn" 
                        data-applicant-id="${applicant.aai_id}">
                        <i class="fa fa-save"></i> Save
                    </button>`;
        
        // Return formatted row data
        return [
            applicant.api_name || '',
            applicant.aai_id || '',
            applicant.api_email || '',
            applicant.school_code || '',
            applicant.aai_programid || '',
            percentageInput,
            remarksInput,
            saveButton,
            actionButtons
        ];
    });
}

// Initialize DataTable with processed data
function initializeDataTable(data, sessionId) {
    scholarshipTable = $('#scholarshipTable').DataTable({
        data: data,
        responsive: true,
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
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { responsivePriority: 1, targets: [0, 1, 5] }, // Name, ID and action are most important
            { responsivePriority: 2, targets: [2] },       // Email is next
            { responsivePriority: 3, targets: [3, 4] }     // School and Program are least important
        ]
    });

     // Handle Save button click
     $('#scholarshipTable').on('click', '.save-btn', function () {
        const applicantId = $(this).data('applicant-id');
        const percentage = $(`.percentage-input[data-applicant-id="${applicantId}"]`).val();
        const remarks = $(`.remarks-input[data-applicant-id="${applicantId}"]`).val();

        $.ajax({
            url: '/save-scholarship-input',
            method: 'POST',
            data: {
                applicant_id: applicantId,
                percentage: percentage,
                sessionId: sessionId,
                remarks: remarks,
                _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            },
            success: function (response) {
                 // Find the input and its parent container
                const input = $(`.percentage-input[data-applicant-id="${applicantId}"]`);
                const tickIcon = input.siblings('.tick-icon');

                // Show the tick icon
                tickIcon.show();

                // Optional: hide again after 2 seconds
                setTimeout(() => tickIcon.hide(), 2000);
            },
            error: function () {
                alert('Failed to save');
            }
        });
    });
    
    // Add event listener for download buttons
    $('#scholarshipTable').on('click', '.download-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const scholarshipType = $(this).data('scholarship-type');
        const sessionId = $(this).data('session-id');
        
        // Redirect to scholarship_criteria_excel.php with all three parameters
        window.open(`https://staff.bnu.edu.pk/HOD/scholarship_criteria_excel.php?applicant_id=${applicantId}&scholarship_type=${scholarshipType}&ad_sess_id=${sessionId}`, '_blank');
    });
}

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
    $('#ad_sess_id').val('').trigger('change');
    $('#ad_school_id').val('').trigger('change');
    $('#admission_sch').val('').trigger('change');
    $('#scholarship_type').val('').trigger('change');
    $('#scholarship_filter_type').val('').trigger('change');
    $('#selected_filter_type').val('').trigger('change');
    $('#status_filter_type').val('').trigger('change');
    
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

// Function to display validation message in the alert div
function showValidationMessage(message) {
    $('#validationMessage').text(message);
    $('#validationAlert').fadeIn();
    
    // Highlight the form panel
    $('.card').addClass('highlight-animation');
    setTimeout(function() {
        $('.card').removeClass('highlight-animation');
    }, 1000);
    
    // Scroll to the validation message
    $('html, body').animate({
        scrollTop: $('#validationAlert').offset().top - 100
    }, 200);
}

</script>
@stop

