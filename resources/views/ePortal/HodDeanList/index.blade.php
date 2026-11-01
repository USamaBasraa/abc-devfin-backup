@extends('layouts.app')

@section('title')
    HOD/Dean List Management
@stop

@section('content')
@include('components.toast-notifications')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<style>
    /* Add custom styles for select dropdowns */
    .form-select {
        width: 100%;
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

    .card-section {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .card-section:last-child {
        border-bottom: none;
    }

    .card-section h6 {
        margin-bottom: 15px;
        color: #495057;
        font-weight: 600;
    }

    /* Button-style radio buttons */
    .position-type-btn {
        cursor: pointer;
        margin-right: 10px;
        transition: all 0.3s ease;
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
        min-width: 100px;
        font-weight: 500;
    }

    .position-type-btn:last-child {
        margin-right: 0;
    }

    .position-type-btn input[type="radio"] {
        display: none;
    }

    .position-type-btn.active {
        font-weight: bold;
    }

    .btn-outline-primary.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .btn-outline-success.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .btn-group-toggle {
        display: block;
        margin-top: 5px;
    }

    .col-form-label.d-block {
        display: block;
        margin-bottom: 8px;
    }

    /* Submit Confirmation Modal Options */
    #optionOnlyEntry, #optionEntryAndUpdate {
        transition: all 0.3s ease;
        border-width: 2px;
    }

    #optionOnlyEntry:hover {
        background-color: #e7f3ff;
        border-color: #007bff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    #optionEntryAndUpdate:hover {
        background-color: #e8f5e9;
        border-color: #28a745 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
    }

    #optionOnlyEntry:active, #optionEntryAndUpdate:active {
        transform: translateY(0);
    }
</style>

<!-- HOD/Dean List Management -->
<div class="card card-border-primary">
    <!-- Header -->
    <div class="card-header">
        <h5><i class="fa fa-users"></i> HOD/Dean List Management</h5>
    </div>

    <!-- Section 1: Filters and Form -->
    <div class="card-block card-section">
        <div class="container-fluid">
            <h6><i class="fa fa-filter"></i> Filter Options</h6>
            <form id="hodDeanForm">
                @csrf
                
                <div class="row">
                    <!-- School Dropdown - Left Side -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label">School</label>
                            <select id="facultyWload_school_id" name="school_id" class="form-select" required>
                                <option value="">Select School</option>
                            </select>
                        </div>
                    </div>

                    <!-- Department Dropdown - Right Side -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label">Department</label>
                            <select id="department_id" name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Program Dropdown - Left Side -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label">Program</label>
                            <select id="program_id" name="program_id" class="form-select">
                                <option value="">Select Program</option>
                            </select>
                        </div>
                    </div>

                    <!-- Employee Dropdown - Right Side -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label">Employee</label>
                            <select id="employee_id" name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Employee Type & Submit Button -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label d-block">Employee Type</label>
                            <div class="d-flex align-items-center">
                                <div class="btn-group-toggle mr-3" data-toggle="buttons">
                                    <label class="btn btn-outline-primary position-type-btn active">
                                        <input type="radio" name="position_type" id="position_hod" value="HOD" checked> HOD
                                    </label>
                                    <label class="btn btn-outline-success position-type-btn">
                                        <input type="radio" name="position_type" id="position_dean" value="DEAN"> DEAN
                                    </label>
                                </div>
                                <button type="button" id="submitHodDeanBtn" class="btn btn-success position-type-btn" style="display:none; min-width: 100px;">
                                    <i class="fa fa-save mr-1"></i>Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Section 2: Pending Requests Info -->
    <div class="card-block card-section" id="pending-requests-section" style="display:none;">
        <div class="container-fluid">
            <h6><i class="fa fa-exclamation-triangle text-warning"></i> Pending Requests Information</h6>
            <div id="pending-requests-content"></div>
        </div>
    </div>

    <!-- Section 3: HOD/Dean List Table -->
    <div class="card-block card-section">
        <div class="container-fluid">
            <h6><i class="fa fa-list"></i> Current HOD/Dean List</h6>
            <div class="table-responsive">
                <table id="hodDeanListTable" class="table table-striped table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>School</th>
                            <th>Department</th>
                            <th>Program</th>
                            <th>Employee Name</th>
                            <th>Employee Username</th>
                            <th>Employee Type</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>
<!-- End of HOD/Dean List Management Card -->

<!-- Pending Requests Modal -->
<div class="modal fade" id="pendingRequestsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fa fa-list"></i> Pending Requests Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-requests-content">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitConfirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-save mr-2"></i>Confirm Submission
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info" id="pendingRequestsAlert" style="display:none;">
                    <i class="fa fa-info-circle mr-1"></i>
                    <strong>Important:</strong> There are <span id="pendingCountText" class="badge badge-warning">0</span> pending request(s) currently assigned to the existing HOD/Dean.
                </div>
                
                <h6 class="mb-3 mt-4">HOD/DEAN Assignment Options</h6>
                
                <div class="card mb-3 border-primary" style="cursor: pointer;" id="optionOnlyEntry">
                    <div class="card-body">
                        <h6 class="card-title mb-2">
                            <i class="fa fa-list-alt text-primary mr-2"></i>Only Update HOD/DEAN Assignment
                        </h6>
                        <p class="card-text text-muted small mb-0">
                            Create or update the HOD/DEAN assignment only. Existing pending requests will remain with their current approvers.
                        </p>
                    </div>
                </div>
                
                <div class="card border-success" style="cursor: pointer;" id="optionEntryAndUpdate">
                    <div class="card-body">
                        <h6 class="card-title mb-2">
                            <i class="fa fa-refresh text-success mr-2"></i>Update HOD Assignment + Transfer Pending Requests
                        </h6>
                        <p class="card-text text-muted small mb-0">
                            Create or update the HOD assignment AND transfer all pending requests from the previous HOD to the newly assigned HOD.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Load schools on page load
    loadSchools();
    
    // Initialize and load HOD/Dean list table
    loadHodDeanListTable();
    
    // Function to reset the form
    function resetForm() {
        // Reset all dropdowns
        $('#facultyWload_school_id').val('').trigger('change');
        $('#department_id').empty().append('<option value="">Select Department</option>');
        $('#program_id').empty().append('<option value="">Select Program</option>');
        $('#employee_id').empty().append('<option value="">Select Employee</option>');
        
        // Reset position type to HOD (default)
        $('.position-type-btn').removeClass('active');
        $('#position_hod').parent().addClass('active');
        $('#position_hod').prop('checked', true);
        
        // Hide submit button
        $('#submitHodDeanBtn').fadeOut();
        
        // Hide pending requests section
        $('#pending-requests-section').hide();
        $('#pending-requests-content').html('');
        
        console.log('Form reset successfully');
    }

    // Function to load schools
    function loadSchools() {
        $.ajax({
            url: '/api/get-academic-sessions-schools',
            type: 'GET',
            cache: false,
            data: { 
                api_token: '{{ auth()->user()->api_token }}' 
            },
            success: function(data) {
                console.log('Schools loaded:', data);
                var select = $('#facultyWload_school_id');
                select.empty().append('<option value="">Select School</option>');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.school_id)
                        .text(item.school_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading schools:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    // School Change Event
    $('#facultyWload_school_id').change(function() {
        var schoolId = $(this).val();
        
        console.log('School selected:', schoolId);
        
        // Clear department, program, and employee dropdowns
        $('#department_id').empty().append('<option value="">Select Department</option>');
        $('#program_id').empty().append('<option value="">Select Program</option>');
        $('#employee_id').empty().append('<option value="">Select Employee</option>');
        
        // Load departments using only school_id
        if(schoolId) {
            console.log('Loading departments for school:', schoolId);
            
            // Load Departments
            $.ajax({
                url: '/api/get-academic-sessions-departments/'+schoolId,
                type: 'GET',
                cache: false,
                data: {
                    api_token: '{{ auth()->user()->api_token }}'
                },
                success: function(data) {
                    console.log('Departments loaded:', data);
                    
                    var select = $('#department_id');
                    
                    data.forEach(function(item) {
                        select.append($('<option></option>')
                            .attr('value', item.dept_id)
                            .text(item.dept_name));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading departments:', error);
                    console.error('Response:', xhr.responseText);
                }
            });

            // Load Employees
            console.log('Loading employees for school:', schoolId);
            $.ajax({
                url: '/eportal-hod-dean-list/employees/'+schoolId,
                type: 'GET',
                cache: false,
                success: function(response) {
                    console.log('Employees loaded:', response);
                    
                    if(response.status && response.data) {
                        var select = $('#employee_id');
                        
                        response.data.forEach(function(item) {
                            var displayText = item.employee_name;
                            if(item.emp_ad_username) {
                                displayText += ' (' + item.emp_ad_username;
                                if(item.emp_email) {
                                    displayText += ' - ' + item.emp_email;
                                }
                                displayText += ')';
                            } else if(item.emp_email) {
                                displayText += ' (' + item.emp_email + ')';
                            }
                            
                            select.append($('<option></option>')
                                .attr('value', item.employee_id)
                                .text(displayText));
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading employees:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    });

    // Department Change Event
    $('#department_id').change(function() {
        var deptId = $(this).val();
        var schoolId = $('#facultyWload_school_id').val();
        
        console.log('Department selected:', deptId);
        console.log('School ID for programs:', schoolId);
        
        // Clear program dropdown
        $('#program_id').empty().append('<option value="">Select Program</option>');
        
        // Load programs using dept_id and school_id
        if(deptId && schoolId) {
            console.log('Loading programs for dept:', deptId, 'and school:', schoolId);
            $.ajax({
                url: '/api/get-academic-sessions-programs/'+schoolId+'/'+deptId,
                type: 'GET',
                cache: false,
                data: {
                    api_token: '{{ auth()->user()->api_token }}'
                },
                success: function(data) {
                    console.log('Programs loaded:', data);
                    var select = $('#program_id');
                    data.forEach(function(item) {
                        select.append($('<option></option>')
                            .attr('value', item.prog_id)
                            .text(item.prog_name));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading programs:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    });

    // Program Change Event
    $('#program_id').change(function() {
        var progId = $(this).val();
        console.log('Program selected:', progId);
        
        // Check if HOD/Dean already exists when program changes
        var schoolId = $('#facultyWload_school_id').val();
        var deptId = $('#department_id').val();
        if (schoolId && deptId) {
            checkHodDeanExists();
        }
    });

    // Employee Change Event
    $('#employee_id').change(function() {
        var empId = $(this).val();
        console.log('Employee selected:', empId);
        
        // Show/hide submit button based on employee selection
        if(empId) {
            $('#submitHodDeanBtn').fadeIn();
            checkHodDeanExists();
        } else {
            $('#submitHodDeanBtn').fadeOut();
        }
    });

    // Position Type Button Click Event
    $('.position-type-btn').click(function() {
        // Remove active class from all buttons
        $('.position-type-btn').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        // Get selected position type
        var positionType = $(this).find('input[type="radio"]').val();
        console.log('Position type selected:', positionType);
        
        // Check if HOD/Dean already exists
        checkHodDeanExists();
    });

    // Function to check if HOD/Dean already exists
    function checkHodDeanExists() {
        var schoolId = $('#facultyWload_school_id').val();
        var deptId = $('#department_id').val();
        var programId = $('#program_id').val();
        var positionType = $('input[name="position_type"]:checked').val();
        
        if (!schoolId || !deptId) {
            console.log('Please select school and department first');
            return;
        }
        
        console.log('Checking HOD/Dean for:', {
            school_id: schoolId,
            dept_id: deptId,
            program_id: programId,
            position_type: positionType
        });
        
        $.ajax({
            url: '/eportal-hod-dean-list/check',
            type: 'POST',
            cache: false,
            data: {
                school_id: schoolId,
                dept_id: deptId,
                program_id: programId,
                position_type: positionType,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Check response:', response);
                
                // Display pending requests information
                displayPendingRequests(response);
                
                if (response.exists) {
                    console.log('⚠️ ' + response.message);
                } else {
                    console.log('✓ ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking HOD/Dean:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    // Function to display pending requests
    function displayPendingRequests(response) {
        const pendingSection = $('#pending-requests-section');
        const contentDiv = $('#pending-requests-content');
        
        if (response.pending_requests && response.pending_requests.total_count > 0) {
            let html = `
                <div class="alert alert-warning">
                    <strong>Total Pending Requests: ${response.pending_requests.total_count}</strong>
                    <button class="btn btn-sm btn-info float-right view-all-requests-btn" 
                            data-school-id="${$('#facultyWload_school_id').val()}"
                            data-dept-id="${$('#department_id').val()}">
                        <i class="fa fa-eye"></i> View All Requests
                    </button>
                </div>
            `;
            
            if (response.pending_requests.approvers && response.pending_requests.approvers.length > 0) {
                html += '<div class="table-responsive"><table class="table table-bordered table-sm">';
                html += '<thead class="thead-light"><tr><th>Previous Approver</th><th>Pending Count</th></tr></thead><tbody>';
                
                response.pending_requests.approvers.forEach(function(approver) {
                    html += `<tr>
                        <td>${approver.approver_name} (ID: ${approver.approver_id})</td>
                        <td><span class="badge badge-warning">${approver.pending_count}</span></td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
            }
            
            contentDiv.html(html);
            pendingSection.show();
        } else {
            contentDiv.html('<p class="text-success"><i class="fa fa-check-circle"></i> No pending requests found for this school/department.</p>');
            pendingSection.show();
        }
    }


    // View all requests button click
    $(document).on('click', '.view-all-requests-btn', function() {
        const schoolId = $(this).data('school-id');
        const deptId = $(this).data('dept-id');
        
        $('#pendingRequestsModal').modal('show');
        loadAllPendingRequests(schoolId, deptId);
    });

    // Load all pending requests for modal
    function loadAllPendingRequests(schoolId, deptId) {
        $('#modal-requests-content').html(`
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
                <p>Loading requests...</p>
            </div>
        `);
        
        $.ajax({
            url: '/eportal-hod-dean-list/check',
            type: 'POST',
            cache: false,
            data: {
                school_id: schoolId,
                dept_id: deptId,
                program_id: $('#program_id').val(),
                position_type: $('input[name="position_type"]:checked').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Modal data:', response);
                displayRequestsInModal(response);
            },
            error: function(xhr, status, error) {
                $('#modal-requests-content').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> Error loading requests: ${error}
                    </div>
                `);
            }
        });
    }

    // Display requests in modal
    function displayRequestsInModal(response) {
        let html = '';
        
        if (response.pending_requests && response.pending_requests.approvers) {
            response.pending_requests.approvers.forEach(function(approver) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>${approver.approver_name}</strong> (ID: ${approver.approver_id})
                            <span class="badge badge-warning float-right">${approver.pending_count} Requests</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Enrollment No</th>
                                            <th>Request Title</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
                
                // Display full request details
                if (approver.requests && approver.requests.length > 0) {
                    approver.requests.forEach(function(req, index) {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${req.enrollment_no || 'N/A'}</td>
                                <td>${req.request_title || 'N/A'}</td>
                                <td><span class="badge badge-warning">${req.status}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="4" class="text-center text-muted">No details available</td>
                        </tr>
                    `;
                }
                
                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="alert alert-info">No pending requests found.</div>';
        }
        
        $('#modal-requests-content').html(html);
    }

    // Global variable to store form data
    let pendingSubmitData = null;
    
    // Handle Submit HOD/Dean button
    $('#submitHodDeanBtn').on('click', function() {
        const schoolId = $('#facultyWload_school_id').val();
        const deptId = $('#department_id').val();
        const programId = $('#program_id').val();
        const employeeId = $('#employee_id').val();
        const positionType = $('input[name="position_type"]:checked').val();
        
        // Validation
        if (!schoolId) {
            showToast('error', 'Please select a school');
            return;
        }
        
        if (!positionType) {
            showToast('error', 'Please select employee type (HOD or DEAN)');
            return;
        }
        
        if (positionType === 'HOD' && !deptId) {
            showToast('error', 'Please select a department for HOD');
            return;
        }
        
        if (!employeeId) {
            showToast('error', 'Please select an employee');
            return;
        }
        
        // Store form data
        pendingSubmitData = {
            school_id: schoolId,
            dept_id: deptId,
            program_id: programId,
            employee_id: employeeId,
            position_type: positionType,
            _token: $('input[name="_token"]').val()
        };
        
        // Check if there are pending requests
        const pendingRequestsSection = $('#pending-requests-section');
        const hasPendingRequests = pendingRequestsSection.is(':visible');
        
        if (hasPendingRequests) {
            // Get the total pending count from the displayed data
            const totalCount = pendingRequestsSection.find('.badge-warning').first().text() || '0';
            $('#pendingCountText').text(totalCount);
            $('#pendingRequestsAlert').show();
        } else {
            $('#pendingRequestsAlert').hide();
        }
        
        // For DEAN: Only show "Only Entry" option, hide the "Entry + Update Requests" option
        if (positionType === 'DEAN') {
            $('#optionEntryAndUpdate').hide();
            // Update modal heading and text for DEAN
            $('#submitConfirmationModal').find('.modal-body h6').text('Confirm DEAN Assignment');
            $('#optionOnlyEntry').find('.card-title').html('<i class="fa fa-user-plus text-primary mr-2"></i>Assign DEAN');
            $('#optionOnlyEntry').find('.card-text').text('Create or update the DEAN assignment for the selected school and employee.');
        } else {
            $('#optionEntryAndUpdate').show();
            // Reset modal heading and text for HOD
            $('#submitConfirmationModal').find('.modal-body h6').text('HOD Assignment Options');
            $('#optionOnlyEntry').find('.card-title').html('<i class="fa fa-list-alt text-primary mr-2"></i>Only Update HOD Assignment');
            $('#optionOnlyEntry').find('.card-text').text('Create or update the HOD assignment only. Existing pending requests will remain with their current approvers.');
            $('#optionEntryAndUpdate').find('.card-title').html('<i class="fa fa-refresh text-success mr-2"></i>Update HOD Assignment + Transfer Pending Requests');
            $('#optionEntryAndUpdate').find('.card-text').text('Create or update the HOD assignment AND transfer all pending requests from the previous HOD to the newly assigned HOD.');
        }
        
        // Show confirmation modal
        $('#submitConfirmationModal').modal('show');
    });
    
    // Handle option: Only Entry
    $('#optionOnlyEntry').on('click', function() {
        $('#submitConfirmationModal').modal('hide');
        submitHodDean(false); // false = don't update pending requests
    });
    
    // Handle option: Entry + Update Requests
    $('#optionEntryAndUpdate').on('click', function() {
        $('#submitConfirmationModal').modal('hide');
        submitHodDean(true); // true = also update pending requests
    });
    
    // Function to submit HOD/Dean data
    function submitHodDean(updatePendingRequests) {
        if (!pendingSubmitData) {
            showToast('error', 'No data to submit');
            return;
        }
        
        // Disable button and show loading
        $('#submitHodDeanBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Saving...');
        
        // Add update flag to form data
        const formData = {
            ...pendingSubmitData,
            update_pending_requests: updatePendingRequests ? 1 : 0
        };
        
        // Submit via AJAX
        $.ajax({
            url: '{{ route("eportal.hod-dean.store") }}',
            method: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                if (response.status) {
                    showToast('success', response.message);
                    
                    // Reload HOD/Dean list table
                    if (typeof window.reloadHodDeanTable === 'function') {
                        window.reloadHodDeanTable();
                    }
                    
                    // Reset the form
                    resetForm();
                } else {
                    showToast('error', response.message || 'Failed to save');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving';
                showToast('error', errorMsg);
            },
            complete: function() {
                // Re-enable button
                $('#submitHodDeanBtn').prop('disabled', false).html('<i class="fa fa-save mr-1"></i>Submit');
                // Clear pending data
                pendingSubmitData = null;
            }
        });
    }
    
    // Function to load and initialize HOD/Dean List DataTable
    function loadHodDeanListTable() {
        // Initialize DataTable
        const table = $('#hodDeanListTable').DataTable({
            ajax: {
                url: '{{ route("eportal-hod-dean-list.get-all") }}',
                type: 'GET',
                cache: false,
                dataSrc: function(json) {
                    if (json.status) {
                        return json.data;
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error('Error loading HOD/Dean list:', error);
                    showToast('error', 'Error loading HOD/Dean list');
                }
            },
            columns: [
                { 
                    data: 'id', 
                    title: 'ID',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'school_name', 
                    title: 'School',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'dept_name', 
                    title: 'Department',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'program_name', 
                    title: 'Program',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'employee_name', 
                    title: 'Employee Name',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'employee_username', 
                    title: 'Employee Username',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'employee_type', 
                    title: 'Employee Type',
                    render: function(data, type, row) {
                        const badgeClass = data === 'DEAN' ? 'badge-success' : 'badge-primary';
                        return '<span class="badge ' + badgeClass + '">' + (data || 'N/A') + '</span>';
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            searching: true,
            ordering: true,
            processing: true,
            language: {
                emptyTable: "No HOD/Dean records found",
                loadingRecords: "Loading...",
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            }
        });
        
        // Reload table after successful submission
        window.reloadHodDeanTable = function() {
            table.ajax.reload(null, false);
        };
    }
});
</script>
