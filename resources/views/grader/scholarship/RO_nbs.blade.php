@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')


<?php 
// dd($isAdmissionApprovalUser);
?>

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
    
    /* Documents button styling */
    .btn-info {
        background-color: #17a2b8 !important;
        border-color: #17a2b8 !important;
        color: white !important;
    }
    
    .btn-info:hover {
        background-color: #138496 !important;
        border-color: #117a8b !important;
        color: white !important;
    }
    
    /* Modal styling */
    .modal-lg {
        max-width: 900px;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.375em 0.75em;
    }

    /* Add these styles at the top of your existing styles */
    .hidden-section {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .visible-section {
        display: flex !important;
        visibility: visible !important;
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
                    <!-- Application Session Dropdown -->
                    <div class="form-group row" id="application_session_group">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Session</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="application_sess_id" name="application_sess_id" class="form-select" required>
                                <option value="">Select Session</option>
                            </select>
                        </div>
                    </div>

                    <!-- Roadmap Session Dropdown -->
                    <div class="form-group row" id="roadmap_session_group">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Roadmap Session</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="ad_sess_id" name="ad_sess_id" class="form-select" required>
                                <option value="">Select Roadmap Session</option>
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

                    <!-- Admission Schedules Dropdown Removed -->


                   <!-- Already NBS Dropdown -->
                    <div class="form-group row mt-2" id="already_nbs_group">
                        <div class="col-lg-3" style="text-align: right">
                            <label class="col-form-label">Already Availed NBS?</label>
                        </div>
                        <div class="col-lg-9">
                            <select id="already_availed_nbs" name="already_availed_nbs" class="form-select">
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
                            style="  display:none;          color: #1548a3; border: 1px solid #1548a3; background-color: transparent; margin-right: 10px;"
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


        @php
            $staffUrl = config('app.staff_url');
            $type = ($isAdmissionApprovalUser === true) ? 'admission' : 'ro';
        @endphp
      
        <!-- <a href="{{ $staffUrl }}/HOD/scholarship_summary_admin.php?type={{$type}}" class="btn" target="_blank">
            <i class="fa fa-print mr-2"></i>Scholarship Summary Report
        </a> -->

        <input type="hidden" id="admission_sch_name2" class="form-control" readonly>
        <button type="button" id="scholarshipReportBtn" class="btn">
            <i class="fa fa-print mr-2"></i> Scholarship Summary Report
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
            <div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group" id="cgpa_filter_group">
            <label for="cgpaFilter" class="form-label">Filter by CGPA Points:</label>
            <div class="input-group">
                <?php
                    $options = [2.5, 2.65];
                    ?>
                    <select id="cgpaMinFilter" class="form-control">
                        <option value="">Min CGPA</option>
                        <?php foreach ($options as $cgpa): ?>
                            <option value="<?= $cgpa ?>"><?= $cgpa ?></option>
                        <?php endforeach; ?>
                    </select>

                <div class="input-group-prepend input-group-append" style="display:none;">
                    <span class="input-group-text">to</span>
                </div>
                <select id="cgpaMaxFilter" class="form-control" style="display:none;">
                    <option value="">Max CGPA</option>
                    <?php
                    for($i = 20; $i <= 40; $i++) {
                        $cgpa = $i/10;
                        echo "<option value=\"$cgpa\">$cgpa</option>";
                    }
                    ?>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-primary" id="applyCgpaFilter">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <button class="btn btn-secondary" id="resetCgpaFilter">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
            {{-- <small class="form-text text-muted">Select CGPA range to filter (e.g., 2.5 to 3.0)</small> --}}
        </div>
    </div>
</div>
            <div class="table-responsive">
                <table id="scholarshipTable" class="table table-bordered table-striped table-hover text-center display responsive nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th style="display:none"></th> <!-- Hidden column for sorting by form_id_order -->
                            <th scope="col"><i class="fa fa-id-badge mr-1"></i> Form ID</th>
                            <th scope="col"><i class="fa fa-id-badge mr-1"></i> Applicant ID</th>
                            <th scope="col"><i class="fa fa-id-card mr-1"></i> Enrollment No</th>
                            <th scope="col"><i class="fa fa-graduation-cap mr-1"></i> CGPA</th>
                            <th scope="col"><i class="fa fa-graduation-cap mr-1"></i> Semester</th>
                            <th scope="col"><i class="fa fa-user mr-1"></i> Student Name</th>
                            <th scope="col"><i class="fa fa-book mr-1"></i> Program</th>
                            <th scope="col"><i class="fa fa-envelope mr-1"></i> Email</th>
                            <th scope="col"><i class="fa fa-envelope mr-1"></i> Monthly Income</th>
                            <th scope="col"><i class="fa fa-envelope mr-1"></i> Monthly Expense</th>
                            <th scope="col"><i class="fa fa-check-circle mr-1"></i> Approval</th>
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

@include('components.toast-notifications')
@stop
@section('javascript')
<script>
// Enhanced custom sort type for order_number/form_id: null/empty/non-numeric values sort last, numbers sort ascending
$.fn.dataTable.ext.type.order['order-number-pre'] = function (d) {
    if (d === null || d === undefined || d === '' || isNaN(Number(d))) {
        return Number.MAX_SAFE_INTEGER; // Sort null/empty/non-numeric to end
    }
    return parseInt(d, 10);
};
// Initialize DataTable
let scholarshipTable;

$(document).ready(function() {
    $('#scholarshipReportBtn').on('click', function() {
       var sessId = $('#application_sess_id').val();
       if(!sessId){
            showToast('error', 'Please select a session first.', 'Missing Selection');
            return;
       }
        let url = "{{ $staffUrl }}/HOD/scholarship_summary_admin.php?type={{ $type }}&sess_id=" + sessId;
        window.open(url, '_blank');
    });
});

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

    // Add onchange handlers to all filter dropdowns
    $('#ad_sess_id, #ad_school_id, #admission_sch, #selected_filter_type, #status_filter_type, #already_availed_nbs').on('change', function() {
        applyScholarshipFilter();
    });
    // Optionally hide the Apply Filter button if still present
    $("button:contains('Apply Filter')").hide();


    if ($('#ad_school_id').val() == '') {
        $('#ad_school_id').trigger('change');
    }

    // Load Application Sessions
    function loadApplicationSessions() {
        $.ajax({
            url: '<?= url('/sessions') ?>',
            type: 'GET',
            success: function(data) {
                var select = $('#application_sess_id');
                select.empty().append('<option value="">Select Session</option>');
                
                // Assuming data is sorted latest first, or we take the first one
                if(data.length > 0) {
                    var latestSession = data[0]; 
                    
                    data.forEach(function(item) {
                        select.append($('<option></option>')
                            .attr('value', item.sess_id)
                            .text(item.sess_name));
                    });

                    // Auto-select the latest session
                    select.val(latestSession.sess_id);

                    // Trigger dependent loads
                    $('#ad_sess_id').val('').trigger('change');
                    $('#ad_school_id').empty().append('<option value="">---All Schools---</option>');
                    
                    loadSessions();
                    loadSchools();
                    loadAdmissionSchedules();

                    // Delay filter application to ensure other dropdowns might populate or just for UI smoothness
                    setTimeout(function() {
                        applyScholarshipFilter();
                    }, 1000);

                } else {
                     data.forEach(function(item) {
                        select.append($('<option></option>')
                            .attr('value', item.sess_id)
                            .text(item.sess_name));
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading  sessions:', error);
                showToast('error', 'Error!', 'Failed to load  sessions');
            }
        });
    }

    // Call function to load application sessions
    loadApplicationSessions();

    function loadAdmissionSchedules() {
    const schoolId = $('#ad_school_id').val();
    const sessId = $('#application_sess_id').val();
    
    // Clear admission schedules dropdown
    const select = $('#admission_sch');
    select.empty().append('<option value="">Select Admission Schedule</option>');

    if (schoolId || sessId) {
        $.ajax({
            url: '<?= url('/api/adm_schedules') ?>',
            type: 'GET',
            data: { 
                school_id: schoolId, 
                sess_id: sessId 
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
            },
            success: function(data) {
                const addedIds = new Set();

                data.forEach(function(item) {
                    if (!addedIds.has(item.admission_sch_id)) {
                        addedIds.add(item.admission_sch_id);
                        select.append(
                            $('<option></option>')
                                .attr('value', item.admission_sch_id)
                                .text(item.admission_sch_name)
                        );
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading admission schedules:', error);
                showToast('error', 'Error!', 'Failed to load admission schedules');
            }
        });
    }
}


    // Modify session change event
    $('#application_sess_id').change(function() {
        var applicationSessId = $(this).val();
        
        // Clear dependent dropdowns
        $('#ad_sess_id').val('').trigger('change');
        $('#ad_school_id').empty().append('<option value="">---All Schools---</option>');
        
        if(applicationSessId) {
            loadSessions();
            loadSchools();
            loadAdmissionSchedules(); // Call the new function
        }
    });

    // Modify school change event
    $('#ad_school_id').change(function() {
        loadAdmissionSchedules(); // Call the new function
    });

    const isAdmissionApprovalUser = @json($isAdmissionApprovalUser);

    // Update visibility using styles instead of classes
    // if (isAdmissionApprovalUser) {
    //     $('#application_session_group').css('display', 'none');
    //     $('#roadmap_session_group').css('display', 'none');
    //     $('#admission_schedule_group').css('display', 'flex');
    //     $('#cgpa_filter_group').css('display', 'none');
    // } else {
    //     $('#application_session_group').css('display', 'flex');
    //     $('#roadmap_session_group').css('display', 'flex');
    //     $('#admission_schedule_group').css('display', 'none');
    //     $('#cgpa_filter_group').css('display', 'block');
    // }

    // Event listener for Excel download buttons
    $('#scholarshipTable').on('click', '.download-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const scholarshipType = 'api_needbase_scholarship';
        const sessionId = $(this).data('session-id');
        let staffUrl = "{{ config('app.staff_url') }}";

        const isAdmissionApprovalUser = @json($isAdmissionApprovalUser);
        const excelUrl = isAdmissionApprovalUser 
            ? `${staffUrl}/HOD/nbs_excel_admin.php` 
            : `${staffUrl}/HOD/nbs_excel_ro.php`;

        window.open(`${excelUrl}?applicant_id=${applicantId}&scholarship_type=${scholarshipType}&ad_sess_id=${sessionId}`, '_blank');
    });

    // Event listener for Documents buttons
    $('#scholarshipTable').on('click', '.documents-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const enrollmentNo = $(this).data('enrollment-no');
        
        // Show loading state and reset modal
        $('#documentsLoading').show();
        $('#documentsContent').hide();
        $('#documentsError').hide();
        $('#documentsEmpty').hide();
        $('#documentsModal').modal('show');
        
        // Fetch documents via AJAX
        $.ajax({
            url: '/api/get-applicant-documents',
            method: 'GET',
            data: {
                applicant_id: applicantId,
                enrollment_no: enrollmentNo
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
            },
            success: function(response) {
                $('#documentsLoading').hide();
                
                if (response.status && response.data && response.data.length > 0) {
                    displayDocuments(response.data);
                    $('#documentsContent').show();
                } else {
                    $('#documentsEmpty').show();
                }
            },
            error: function(xhr, status, error) {
                $('#documentsLoading').hide();
                let errorMessage = 'Failed to load documents.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#documentsError').text(errorMessage).show();
            }
        });
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
    var sessId = $('#application_sess_id').val();
    
    // Clear admission schedules dropdown
    $('#admission_sch').empty().append('<option value="">Select Admission Schedule</option>');
    
    
    // Load admission schedules
    if(schoolId || sessId) {
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
    var applicationSessId = $('#application_sess_id').val();
    var sessionId = $('#ad_sess_id').val();
    
    $('#admission_sch_name2').val($('#admission_sch').val());
 
    


    // Show loading overlay or spinner
    if (scholarshipTable) {
        scholarshipTable.destroy();
    }

    $('#scholarshipTable').addClass('loading');
    $('#filterStatus').show(); // Show the filter status indicator
    
    const data = {
        application_sess_id: applicationSessId,
        ad_sess_id: sessionId,
        ad_school_id: $('#ad_school_id').val(),
        admission_sch: $('#admission_sch').val(),
        selected_filter_type: $('#selected_filter_type').val(),
        status_filter_type: $('#status_filter_type').val(),
        already_availed_nbs: $('#already_availed_nbs').val(),
        request_type: 'RO'
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
                if (response.status) {
                
                } 
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
        // Check if enrollment_no is present and not empty
        const hasEnrollment = applicant.enrollment_no !== null && applicant.enrollment_no !== undefined && applicant.enrollment_no !== '';

        // Sorting column
        const sortValue = hasEnrollment ? (applicant.form_id || '') : (applicant.order_number || '');

        // Ref column
        const refValue = hasEnrollment
            ? `Ref-${applicant.form_id || 'N/A'}`
            : `Ref-${applicant.order_number || 'N/A'}`;

        // Action buttons (Excel and Documents)
        const actionButtons = `
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary download-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    data-scholarship-type="${scholarshipType || ''}"
                    data-session-id="${sessionId || ''}">
                    <i class="fa fa-file-excel-o mr-1"></i> Excel
                </button>
                <button class="btn btn-sm btn-outline-info documents-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    data-enrollment-no="${applicant.enrollment_no || ''}">
                    <i class="fa fa-file-text-o mr-1"></i> Documents
                </button>
                <a class="btn btn-sm btn-outline-warning update-profile-btn"
                   href="/nbs-form-update-profile?applicant_id=${applicant.aai_id || ''}&enrollment_no=${applicant.enrollment_no || ''}&session_id=${sessionId || ''}"
                   target="_blank">
                   <i class="fa fa-user"></i> Update Profile
                </a>
            </div>`;

        // Approval logic based on has_percentage_detail
        let approvalHtml;
        if (applicant.admin_ro_approval == 'Approved') {
            approvalHtml = `<span class="badge badge-success">Approved</span>`;
        }
        else if (applicant.admin_ro_approval == 'Disapproved') {
            approvalHtml = `<span class="badge badge-danger">Disapproved</span>`;
        }
        else {
            approvalHtml = `<div class="btn-group" role="group">
                <button class="btn btn-sm btn-primary approve-btn" 
                    data-applicant-id="${applicant.aai_id}" 
                    data-enrollment-no="${applicant.enrollment_no}"
                    data-form-id="${applicant.form_id}"
                    data-session-id="${applicant.sess_id}">
                    <i class="fa fa-check-circle"></i> Approve
                </button>
                <button class="btn btn-sm btn-danger disapprove-btn" 
                    data-applicant-id="${applicant.aai_id}" 
                    data-enrollment-no="${applicant.enrollment_no}"
                    data-form-id="${applicant.form_id}"
                    data-session-id="${applicant.sess_id}">
                    <i class="fa fa-times-circle"></i> Disapprove
                </button>
            </div>`;
        }

        // Return formatted row data without school column
        return [
            sortValue, // HIDDEN column for sorting
            refValue,
            applicant.aai_id || 'N/A',
            applicant.enrollment_no || 'N/A',
            applicant.cgpa || 'N/A',
            applicant.semester_number || 'N/A',
            applicant.student_name || 'N/A',
            applicant.prog_name || 'N/A',
            applicant.api_email || 'N/A',
            applicant.monthly_income || 'N/A',
            applicant.total_expenses  || 'N/A',
            approvalHtml,
            actionButtons
        ];
    });
}

// Initialize DataTable with processed data
function initializeDataTable(data, sessionId) {
    if ($.fn.DataTable.isDataTable('#scholarshipTable')) {
        $('#scholarshipTable').DataTable().destroy();
    }

    scholarshipTable = $('#scholarshipTable').DataTable({
        data: data,
        responsive: true,
        dom: '<"row mb-2"<"col-12"B>>' + // Buttons in their own row
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                className: 'btn btn-success',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-secondary',
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var $node = $(node);
                            if ($node.find('select').length) {
                                var selectedText = $node.find('select option:selected').text();
                                if (!selectedText || selectedText.trim().toLowerCase().includes('select')) {
                                    return 'N/A';
                                }
                                return selectedText;
                            }
                            return $node.text().trim();
                        }
                    }
                }
            }
        ],
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
            { targets: 0, visible: false, type: 'order-number' }, // Hide the sorting column, use custom type
            { responsivePriority: 1, targets: [1, 2, 3] }, // ID, Enrollment, and CGPA are most important
            { responsivePriority: 2, targets: [4, 5] },    // Name and Program are next
            { responsivePriority: 3, targets: [6, 7, 8] }  // Rest are least important
        ],
        order: [[0, 'asc']]
    });


    
    // Event listeners removed from here to prevent multiple bindings. 
    // They are now in $(document).ready().

   $('#applyCgpaFilter').on('click', function() {
    const minCgpa = parseFloat($('#cgpaMinFilter').val());
    const maxCgpa = parseFloat($('#cgpaMaxFilter').val());

    // Remove any existing custom CGPA filters
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
        return !fn.isCgpaFilter;
    });

    // Create CGPA filter
    const cgpaFilter = function(settings, data, dataIndex) {
        let cgpaStr = data[4]; // CGPA column
        cgpaStr = cgpaStr.replace(/[^\d.]/g, '');
        const cgpa = parseFloat(cgpaStr);

        if (isNaN(cgpa)) return false;

        if (isNaN(minCgpa) && isNaN(maxCgpa)) return true;
        if (!isNaN(minCgpa) && isNaN(maxCgpa)) return cgpa >= minCgpa;
        if (isNaN(minCgpa) && !isNaN(maxCgpa)) return cgpa <= maxCgpa;

        return cgpa >= minCgpa && cgpa <= maxCgpa;
    };

    // Tag the filter so we can remove it later
    cgpaFilter.isCgpaFilter = true;

    // Add to search filters
    $.fn.dataTable.ext.search.push(cgpaFilter);

    // Redraw DataTable
    scholarshipTable.draw();
});


    // Reset CGPA filter
    $('#resetCgpaFilter').on('click', function() {
        $('#cgpaMinFilter, #cgpaMaxFilter').val('');
        $.fn.dataTable.ext.search.pop(); // Remove the custom filter
        scholarshipTable.draw();
    });

    // Add validation for min/max relationship
    $('#cgpaMinFilter').on('change', function() {
        const minVal = $(this).val();
        const maxVal = $('#cgpaMaxFilter').val();
        
        if (minVal && maxVal && parseFloat(minVal) > parseFloat(maxVal)) {
            $('#cgpaMaxFilter').val(minVal);
        }
    });

    $('#cgpaMaxFilter').on('change', function() {
        const maxVal = $(this).val();
        const minVal = $('#cgpaMinFilter').val();
        
        if (minVal && maxVal && parseFloat(maxVal) < parseFloat(minVal)) {
            $('#cgpaMinFilter').val(maxVal);
        }
    });

    // Validate CGPA input range
    $('#cgpaMinFilter, #cgpaMaxFilter').on('input', function() {
        let value = parseFloat($(this).val());
        if (value < 0) $(this).val('');
        else if (value > 4) $(this).val(4);
        if (value && !isNaN(value)) {
            $(this).val(Math.round(value * 100) / 100); // Round to 2 decimal places
        }
    });
}

// Add modal HTML after the table
$(document).ready(function() {
    if ($('#approvalModal').length === 0) {
        $('body').append(`
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approvalModalLabel">Enter Remarks</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="approvalForm">
          <input type="hidden" id="modal_applicant_id" name="applicant_id">
          <input type="hidden" id="modal_enrollment_no" name="enrollment_no">
          <input type="hidden" id="modal_form_id" name="form_id">
          <input type="hidden" id="modal_session_id" name="session_id">
          <input type="hidden" id="modal_action_type" name="action_type">
          <div class="form-group">
            <label for="modal_remarks">Remarks <span style="color:red">*</span></label>
            <textarea class="form-control" id="modal_remarks" name="remarks" rows="4" required></textarea>
            <div class="invalid-feedback">Remarks are required.</div>
          </div>
        </form>
        <div id="approvalModalAlert" class="alert alert-danger" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submitApprovalBtn">Submit</button>
      </div>
    </div>
  </div>
</div>
        `);
    }
    
    // Add Documents Modal
    if ($('#documentsModal').length === 0) {
        $('body').append(`
<div class="modal fade" id="documentsModal" tabindex="-1" role="dialog" aria-labelledby="documentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentsModalLabel">
          <i class="fa fa-file-text-o mr-2"></i> Student Documents
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="documentsLoading" class="text-center" style="display:none;">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
          <p class="mt-2">Loading documents...</p>
        </div>
        <div id="documentsContent" style="display:none;">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="thead-light">
                <tr>
                  <th><i class="fa fa-file-o mr-1"></i> Document Type</th>
                  <th><i class="fa fa-calendar mr-1"></i> Upload Date</th>
                  <th><i class="fa fa-eye mr-1"></i> Action</th>
              
                </tr>
              </thead>
              <tbody id="documentsTableBody">
              </tbody>
            </table>
          </div>
        </div>
        <div id="documentsError" class="alert alert-danger" style="display:none;"></div>
        <div id="documentsEmpty" class="alert alert-info" style="display:none;">
          <i class="fa fa-info-circle mr-2"></i> No documents found for this student.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
        `);
    }
});
// Approve button click handler
$(document).on('click', '.approve-btn', function() {
    const applicantId = $(this).data('applicant-id');
    const enrollmentNo = $(this).data('enrollment-no');
    const formId = $(this).data('form-id');
    const sessionId = $(this).data('session-id');
    
    $('#modal_applicant_id').val(applicantId);
    $('#modal_enrollment_no').val(enrollmentNo);
    $('#modal_form_id').val(formId);
    $('#modal_session_id').val(sessionId); // Set session ID in hidden field
    $('#modal_action_type').val('approve'); // Set action type
    $('#approvalModalLabel').text('Enter Remarks for Approval'); // Set modal title
    $('#submitApprovalBtn').text('Submit Approval').removeClass('btn-danger').addClass('btn-success'); // Set submit button text and color
    $('#modal_remarks').val('');
    $('#approvalModalAlert').hide();
    $('#modal_remarks').removeClass('is-invalid');
    $('#approvalModal').modal('show');
});

// Disapprove button click handler
$(document).on('click', '.disapprove-btn', function() {
    const applicantId = $(this).data('applicant-id');
    const enrollmentNo = $(this).data('enrollment-no');
    const formId = $(this).data('form-id');
    const sessionId = $(this).data('session-id');
    
    $('#modal_applicant_id').val(applicantId);
    $('#modal_enrollment_no').val(enrollmentNo);
    $('#modal_form_id').val(formId);
    $('#modal_session_id').val(sessionId); // Set session ID in hidden field
    $('#modal_action_type').val('disapprove'); // Set action type
    $('#approvalModalLabel').text('Enter Remarks for Disapproval'); // Set modal title
    $('#submitApprovalBtn').text('Submit Disapproval').removeClass('btn-success').addClass('btn-danger'); // Set submit button text and color
    $('#modal_remarks').val('');
    $('#approvalModalAlert').hide();
    $('#modal_remarks').removeClass('is-invalid');
    $('#approvalModal').modal('show');
});
// Modal submit handler
$(document).on('click', '#submitApprovalBtn', function() {
    const applicantId = $('#modal_applicant_id').val();
    const enrollmentNo = $('#modal_enrollment_no').val();
    const formId = $('#modal_form_id').val();
    const sessionId = $('#modal_session_id').val();
    const actionType = $('#modal_action_type').val();
    const remarks = $('#modal_remarks').val().trim();
    
    if (!remarks) {
        $('#modal_remarks').addClass('is-invalid');
        $('#approvalModalAlert').text('Remarks are required.').show();
        return;
    } else {
        $('#modal_remarks').removeClass('is-invalid');
        $('#approvalModalAlert').hide();
    }
    
    // Determine API endpoint based on action type
    const apiUrl = actionType === 'approve' ? '/api/approve-scholarship-applicant' : '/api/disapprove-scholarship-applicant';
    const successMessage = actionType === 'approve' ? 'Approval submitted successfully!' : 'Disapproval submitted successfully!';
    
    // AJAX POST for approval/disapproval
    $.ajax({
        url: apiUrl,
        method: 'POST',
        data: {
            applicant_id: applicantId,
            enrollment_no: enrollmentNo,
            form_id: formId,
            session_id: sessionId,
            remarks: remarks
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            $('#approvalModal').modal('hide');
            // Optionally show a toast or alert
            showValidationMessage(successMessage);
            // Refresh table
            applyScholarshipFilter();
        },
        error: function(xhr, status, error) {
            let msg = actionType === 'approve' ? 'Failed to submit approval.' : 'Failed to submit disapproval.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            $('#approvalModalAlert').text(msg).show();
        }
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
    window.location.reload();
    // Clear all select selections
    $('#application_sess_id').val('').trigger('change');
    $('#ad_sess_id').val('').trigger('change');
    $('#admission_sch').val('').trigger('change');
    // $('#ad_school_id').val('').trigger('change');
    // $('#admission_sch').val('').trigger('change');
    // $('#selected_filter_type').val('').trigger('change');
    // $('#status_filter_type').val('').trigger('change');
    // Clear the table if it exists
    loadSchools();
    if (scholarshipTable) {
        scholarshipTable.clear().draw();
    }


    // if ($('#ad_school_id').val() == '') {
    //     $('#ad_school_id').trigger('change');
    // }
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

// Function to display documents in the modal
function displayDocuments(documents) {
    const tbody = $('#documentsTableBody');
    tbody.empty();

    if (!Array.isArray(documents) || documents.length === 0) {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No documents found.</td></tr>');
        return;
    }

    documents.forEach(function(doc) {
        const uploadDate = doc.ad_entry_datetime 
            ? new Date(doc.ad_entry_datetime).toLocaleDateString() 
            : 'N/A';

        let downloadLink = '';
        if (doc.ad_filelink) {
            downloadLink = `<a href="https://cms.bnu.edu.pk${doc.ad_filelink}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fa fa-eye"></i> View</a>`;
        } else if (doc.ad_filelink) {
            downloadLink = `<a href="${doc.ad_filelink}" target="_blank" class="btn btn-sm btn-primary" download>
                <i class="fa fa-eye"></i> View</a>`;
        } else {
            downloadLink = '<span class="text-muted">No file available</span>';
        }

        const row = `
            <tr>
                <td><strong>${doc.ad_type.replace('needbased_form_documents_', '').trim() || 'Unknown'}</strong></td>
                <td>${uploadDate}</td>
                <td>${downloadLink}</td>
            </tr>
        `;
        
        tbody.append(row);
    });
}


</script>
@stop

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<!-- DataTables JS (ensure present) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS and dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

