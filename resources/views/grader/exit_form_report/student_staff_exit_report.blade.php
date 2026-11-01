@extends('layouts.app')

@section('title') BNU CMS @stop
@section('content')

<!-- Include Toast Notifications Component -->
@include('components.toast-notifications')


<style>
/* Add custom styles for select dropdowns */
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



/* Date input styles - same width as School dropdown */
.date-input {
    width: 400px !important;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.date-input:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<div class="container-fluid">
    <div class="row mt-4" style="margin:5px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"> Exit Form Report </h3>
                </div>              
                <input type="hidden" id="session">
                <input type="hidden" id="school">
                <input type="hidden" id="department">
                <input type="hidden" id="program">
                <div class="card-body pb-2">
                    <form id="messageForm" method="post">
                        <!-- Report Type Radios -->
                        <div class="form-group row" style="margin-bottom:20px;">
                            <div class="col-lg-3" style="text-align: right">
                                <label class="col-form-label">Report Type</label>
                            </div>
                            <div class="col-lg-8 d-flex align-items-center">
                                <div class="form-check mr-4">
                                    <input class="form-check-input" type="radio" name="exit_form_type" id="student_exit" value="student" checked>
                                    <label class="form-check-label" for="student_exit">Student Exit Form Comments</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exit_form_type" id="faculty_exit" value="faculty">
                                    <label class="form-check-label" for="faculty_exit">Faculty/Staff Exit Form Comments</label>
                                </div>
                            </div>
                        </div>

                        <!-- Student-only filters wrapped for easy toggle -->
                        <div id="studentFilters">
                            <!-- School Dropdown -->
                            <div class="form-group row" style="margin-bottom:20px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">School</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="facultyWload_school_id" name="school_id" class="form-select" required>
                                        <option value="">Select School</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Department Dropdown -->
                            <div class="form-group row" style="margin-bottom:20px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">Department</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="department_id" name="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Program Dropdown -->
                            <div class="form-group row" style="margin-bottom:20px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">Program</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="program_id" name="program_id" class="form-select" required>
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                            </div>

                          
                        </div>
                          <!-- Date Range -->
                            <div class="form-group row" style="margin-bottom:20px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">From Date</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" id="from_date" name="from_date" class="form-control date-input">
                                </div>
                            </div>

                            <div class="form-group row" style="margin-bottom:20px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">To Date</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" id="to_date" name="to_date" class="form-control date-input">
                                </div>
                            </div>

                        <!-- Buttons -->
                        <div class="form-group row" style="margin-bottom:20px;">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-8">
                                <!-- Student Exit Button -->
                                <button type="button" id="studentBtn" class="btn btn-outline-primary btn-lg mr-3">
                                    <i class="fa fa-file-pdf-o mr-2"></i>
                                    Generate Student Report
                                </button>

                                <!-- Faculty Exit Button -->
                                <button type="button" id="facultyBtn" class="btn btn-outline-success btn-lg mr-3" style="display:none;">
                                    <i class="fa fa-file-pdf-o mr-2"></i>
                                    Generate Faculty Report
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-lg" id="clearBtn">
                                    <i class="fa fa-refresh mr-2"></i>
                                    Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script type="text/javascript">

var baseUrl = window.location.protocol + "//" + window.location.host;

document.addEventListener("DOMContentLoaded", function() {
    const studentRadio = document.getElementById('student_exit');
    const facultyRadio = document.getElementById('faculty_exit');
    const studentBtn = document.getElementById('studentBtn');
    const facultyBtn = document.getElementById('facultyBtn');
    const studentFilters = document.getElementById('studentFilters');
    const clearBtn = document.getElementById('clearBtn');
    const form = document.getElementById('messageForm');

    function toggleView() {
        if (facultyRadio.checked) {
            // Hide all student filters for Faculty Exit
            studentFilters.style.display = 'none';
            studentBtn.style.display = 'none';
            facultyBtn.style.display = 'inline-block';
        } else {
            // Show all filters for Student Exit
            studentFilters.style.display = 'block';
            studentBtn.style.display = 'inline-block';
            facultyBtn.style.display = 'none';
        }
    }

    // Attach change events
    studentRadio.addEventListener('change', toggleView);
    facultyRadio.addEventListener('change', toggleView);

    // Initialize default view
    toggleView();

    // Button events
    studentBtn.addEventListener('click', function() {
       // alert("Generating Student Exit Report...");
         goToExitFormReport();
    });

    facultyBtn.addEventListener('click', function () {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();

        // Optional validation
        // if (!fromDate || !toDate) {
        //     alert('Please select both From Date and To Date');
        //     return;
        // }

        // Laravel route
        const facultyPdfUrl = "/staff-exit-form?from_date=" + fromDate + "&to_date=" + toDate;

        // Open route
        window.open(facultyPdfUrl, '_blank');
    });

    // Reset form
    clearBtn.addEventListener('click', function() {
        form.reset();
        studentRadio.checked = true;
        toggleView();
    });
});

// Load Sessions and Schools on page load
$(document).ready(function() {
    loadSessions();
    loadSchools();

// Function to load sessions
function loadSessions() {
    $.ajax({
        url: '/api/get-academic-sessions',
        type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
        success: function(data) {
            var select = $('#facultyWload_sess_id');
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
        url: '/api/get-academic-sessions-schools',
        type: 'GET',
        data: { 
            api_token: '{{ auth()->user()->api_token }}' 
        },
        success: function(data) {
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
        }
    });
}

// Session Change Event
$('#facultyWload_sess_id').change(function() {
    var sessId = $(this).val();
    $('#session').val(sessId);
    
    // Clear dependent dropdowns
    $('#facultyWload_school_id').empty().append('<option value="">Select School</option>');
    $('#department_id').empty().append('<option value="">Select Department</option>');
    $('#program_id').empty().append('<option value="">Select Program</option>');
    
    // Load schools
    if(sessId) {
        $.ajax({
            url: '/api/get-academic-sessions-schools',
            type: 'GET',
            data: { sess_id: sessId, api_token: '{{ auth()->user()->api_token }}' },
            success: function(data) {
                var select = $('#facultyWload_school_id');
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
$('#facultyWload_school_id').change(function() {
    var schoolId = $(this).val();
    $('#school').val(schoolId);
    
    // Clear dependent dropdowns
    $('#department_id').empty().append('<option value="">Select Department</option>');
    $('#program_id').empty().append('<option value="">Select Program</option>');
    
    // Load departments using only school_id
    if(schoolId) {
        $.ajax({
            url: '/api/get-academic-sessions-departments/'+schoolId,
            type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
            success: function(data) {
                var select = $('#department_id');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.dept_id)
                        .text(item.dept_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading departments:', error);
            }
        });
    }
});

// Department Change Event
$('#department_id').change(function() {
    var deptId = $(this).val();
    var schoolId = $('#facultyWload_school_id').val();
    $('#department').val(deptId);
    
    // Clear program dropdown
    $('#program_id').empty().append('<option value="">Select Program</option>');
    
    // Load programs using dept_id and school_id
    if(deptId) {
        $.ajax({
            url: '/api/get-academic-sessions-programs/'+schoolId+'/'+deptId,
            type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
            success: function(data) {
                var select = $('#program_id');
                data.forEach(function(item) {
                    select.append($('<option></option>')
                        .attr('value', item.prog_id)
                        .text(item.prog_name));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading programs:', error);
            }
        });
    }
});

// Program Change Event
$('#program_id').change(function() {
    $('#program').val($(this).val());
});

});

// Function to go to Exit Form Report Overall page
function goToExitFormReport() {
    // Get selected values
    var schoolId = $('#facultyWload_school_id').val();
    var deptId = $('#department_id').val();
    var progId = $('#program_id').val();
    var fromDate = $('#from_date').val();
    var toDate = $('#to_date').val();
    
    // Validate that school is selected (required)
    if (!schoolId) {
        // Show toast with longer duration and debug
        console.log('Showing toast warning - School not selected');
        showToast('warning', 'Please select a School first!', 'School Required');
        return;
    }
    

    
    // Build URL with selected filters
    var url = baseUrl + '/exit-form-report/overall';
    var params = [];
    
    // Add authentication key for security
    params.push('key={{ auth()->user()->api_token }}');
    
    if (schoolId) params.push('school=' + schoolId);
    if (deptId) params.push('dept=' + deptId);
    if (progId) params.push('prog=' + progId);
    if (fromDate) params.push('from_date=' + fromDate);
    if (toDate) params.push('to_date=' + toDate);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    // Redirect to the overall page with filters
    

    // Redirect to the overall page with filters
    window.open(url, '_blank');
    
    // Show success message


}

// Function to clear form and reload page
function clearForm() {
    // Reload the page to reset all form fields
    window.location.reload();
}
</script>




