@extends('layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')
{{-- @dd("213"); --}}

<style>
/* MCM Portal Request — Figma-aligned presentation layer.
   Existing Blade conditions, IDs, names, handlers and JavaScript are unchanged. */
.column-link { cursor: pointer; }
.text-info { color: #17675f !important; }

.mcm-portal-request-page {
    width: 100% !important;
    max-width: 100% !important;
    min-height: calc(100vh - 20px);
    margin: 0 !important;
    /* padding: 28px 30px 42px !important; */
    background: #f2f6f9;
    font-family: 'Montserrat', sans-serif;
    color: #252525;
      padding-left: 0 !important;
    padding-right: 0 !important;
}

.mcm-portal-request-page > .card {
    margin: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

/* Page heading */
.mcm-portal-request-page > .card > .card-header {
    padding: 0 0 18px 0;
    border: 0;
    background: transparent;
}

.mcm-portal-request-page > .card > .card-header h5 {
    margin: 0;
    color: #252525;
    font-size: 20px;
    line-height: 1.35;
    font-weight: 600;
}

.mcm-portal-request-page > .card > .card-header h5::after {
    content: 'Improve Performance';
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 400;
}

.mcm-portal-request-page > .card > .card-block.tab-icon {
    padding: 0;
    border: 0;
    background: transparent;
}

/* Request / Pending / Completed pills */
.mcm-portal-request-page .tab-menu-heading,
.mcm-portal-request-page .tabs-menu1 {
    margin: 0;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
}

.mcm-portal-request-page .tabs-menu1 .nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 20px;
    padding: 0;
    font-size: 14px !important;
}

.mcm-portal-request-page .tabs-menu1 .nav-item {
    margin: 0 !important;
}

.mcm-portal-request-page .tabs-menu1 .nav-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 40px;
    padding: 10px 20px;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    background: transparent;
    color: #64748b;
    font-size: 14px;
    line-height: 1;
    font-weight: 600;
    box-shadow: none;
}

.mcm-portal-request-page .tabs-menu1 .nav-link.active {
    border-color: #17675f;
    background: #17675f;
    color: #fff;
}

.mcm-portal-request-page .tabs-menu1 .nav-link:hover,
.mcm-portal-request-page .tabs-menu1 .nav-link:focus {
    border-color: #17675f;
    color: #17675f;
}

.mcm-portal-request-page .tabs-menu1 .nav-link.active:hover,
.mcm-portal-request-page .tabs-menu1 .nav-link.active:focus {
    color: #fff;
}

.mcm-portal-request-page .tabs-menu1 .badge {
    min-width: 22px;
    height: 22px;
    padding: 5px 7px;
    border-radius: 999px;
    font-size: 10px;
    line-height: 1;
    font-weight: 600;
}

.mcm-portal-request-page .tabs-menu1 .badge-info,
.mcm-portal-request-page .tabs-menu1 .badge-success {
    background: #e7f7ef !important;
    color: #17675f !important;
}

/* Form card */
.mcm-portal-request-page .card-body {
    margin: 0;
    padding: 26px 28px 28px;
    border: 0;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 4px 14px rgba(15,23,42,.04);
}

.mcm-portal-request-page .card-body > .card-block {
    padding: 0;
    border: 0;
    background: transparent;
}

.mcm-portal-request-page #mainForm > .container {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

.mcm-portal-request-page #mainForm > .container > .row:first-child {
    margin-left: -10px;
    margin-right: -10px;
}

.mcm-portal-request-page #mainForm > .container > .row:first-child > [class*='col-'],
.mcm-portal-request-page #mainForm > .container > .form-group[class*='col-'] {
    padding-left: 10px;
    padding-right: 10px;
}

.mcm-portal-request-page .form-group {
    margin-bottom: 20px;
}

.mcm-portal-request-page label,
.mcm-portal-request-page .col-form-label {
    display: block;
    margin: 0 0 8px;
    padding: 0;
    color: #111827;
    font-size: 14px;
    line-height: 1.5;
    font-weight: 600;
}

.mcm-portal-request-page label b { font-weight: 600; }

.mcm-portal-request-page .form-control {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background: #fff;
    color: #252525;
    font-size: 14px;
    line-height: 1.5;
    font-weight: 400;
    box-shadow: none !important;
}

.mcm-portal-request-page select.form-control {
    cursor: pointer;
}

.mcm-portal-request-page .form-control:focus {
    border-color: #17675f !important;
    box-shadow: 0 0 0 3px rgba(23,103,95,.09) !important;
}

.mcm-portal-request-page .form-control::placeholder {
    color: #9ca3af;
    opacity: 1;
}

.mcm-portal-request-page #message {
    height: 128px !important;
    min-height: 128px;
    padding: 14px 16px;
    border: 1px solid #e5e7eb !important;
    resize: vertical;
}

.mcm-portal-request-page #mainForm > .form-group {
    margin-top: 2px;
}

.mcm-portal-request-page #mainForm > .form-group > label h5 {
    margin: 0;
    color: #111827;
    font-size: 14px;
    line-height: 1.5;
    font-weight: 600;
}

.mcm-portal-request-page #countLimit {
    min-width: 48px;
    padding: 5px 9px;
    border-radius: 999px;
    background: #eef7f5 !important;
    color: #17675f !important;
    font-size: 11px;
    font-weight: 600;
}

.mcm-portal-request-page .error.text-danger {
    display: inline-block;
    margin-top: 5px;
    font-size: 11px;
}

/* Actions */
.mcm-portal-request-page #mainForm > .row.justify-content-md-center {
    margin: 4px 0 0;
}

.mcm-portal-request-page #mainForm > .row.justify-content-md-center > .col {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 0;
}

.mcm-portal-request-page .btn {
    min-height: 44px;
    padding: 11px 22px;
    border-radius: 8px !important;
    font-size: 14px;
    line-height: 1.2;
    font-weight: 600;
    box-shadow: none !important;
}

.mcm-portal-request-page #submitButton,
.mcm-portal-request-page .btn-primary {
    border-color: #17675f !important;
    background: #17675f !important;
    color: #fff !important;
}

.mcm-portal-request-page #submitButton:hover,
.mcm-portal-request-page #submitButton:focus,
.mcm-portal-request-page .btn-primary:hover,
.mcm-portal-request-page .btn-primary:focus {
    border-color: #12564f !important;
    background: #12564f !important;
}

.mcm-portal-request-page .btn-secondary {
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #64748b !important;
}

.mcm-portal-request-page .btn-secondary:hover,
.mcm-portal-request-page .btn-secondary:focus {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
    color: #334155 !important;
}

.mcm-portal-request-page #successMsg {
    margin: 0 0 0 4px !important;
    color: #14845f !important;
    font-size: 13px;
    font-weight: 600;
}

@media (max-width: 991.98px) {
    .mcm-portal-request-page {
        padding: 24px 18px 34px !important;
    }

    .mcm-portal-request-page .card-body {
        padding: 22px 20px 24px;
    }
}

@media (max-width: 767.98px) {
    .mcm-portal-request-page {
        padding: 20px 12px 28px !important;
    }

    .mcm-portal-request-page > .card > .card-header h5 {
        font-size: 19px;
    }

    .mcm-portal-request-page > .card > .card-header h5::after {
        font-size: 14px;
    }

    .mcm-portal-request-page .tabs-menu1 .nav {
        gap: 6px;
    }

    .mcm-portal-request-page .tabs-menu1 .nav-link {
        padding: 9px 14px;
        font-size: 12px;
    }

    .mcm-portal-request-page .card-body {
        padding: 18px 14px 20px;
        border-radius: 14px;
    }

    .mcm-portal-request-page #mainForm > .container > .form-group[class*='col-'] {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        padding-left: 0;
        padding-right: 0;
    }

    .mcm-portal-request-page #mainForm > .row.justify-content-md-center > .col {
        align-items: stretch;
    }

    .mcm-portal-request-page #mainForm > .row.justify-content-md-center .btn {
        flex: 1 1 140px;
    }

    .mcm-portal-request-page #successMsg {
        width: 100%;
        margin-left: 0 !important;
    }
}
</style>


<div class="container-fluid mcm-portal-request-page" >

    <div class="card">
        <div class="card-header">
            <h5>MCM Portal Requests</h5>
        </div>

        <div class="card-block tab-icon">


            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="tab-menu-heading p-0 border-0">
                            <div class="tabs-menu1 px-3">
                                <ul class="nav" style="font-size:17px;">
                                    <li class="nav-item ml-3 mr-3">
                                        <a href="eportal-request" class="nav-link active" >
                                            <i class="fa fa-flag"></i> Request
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="e-portal-pending " class="nav-link ">
                                            <i class="fa fa-hourglass"></i> Pending
                                            <span class="badge badge-info">{{ $CountPending[0]->total_pending }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="e-portal-completed" class="nav-link">
                                            <i class="fa fa-check-circle"></i> Completed
                                            <span class="badge badge-success">{{ $CountCompleted[0]->total_completed }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
@php
        $userGroupId = auth()->user()->dept_id;
        $allowedGroupIds = [23,24,122,141,61,41];
@endphp
                <div class="card-block">
                    <form id="mainForm" novalidate="">
                @csrf
                <div class="container">
                    <div class="row">
                        @if (!in_array($userGroupId, $allowedGroupIds))
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group">
                            <label class="col-form-label" for="department"><b>Department</b></label>
                                <select class="form-control" name="dept_id" id="dept_id" onchange="getEmployee1(this.value)">
                                        <option   option disabled  selected value="">--Please Select Department--</option>
                                    @foreach ($schools as $dept)
                                        <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            <span id="department-error" class="error text-danger"></span>
                        </div>
                       @else

                       <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group">
                           <label class="col-form-label" for="school"><b>School</b></label>
                           <select class="form-control" name="school_id" id="school_id" onchange="getEmployee(this.value)">
                               <option disabled  selected value="">--Please Select School--</option>
                               @foreach ($schools as $school)
                                   <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                               @endforeach
                           </select>
                           <span id="school-error" class="error text-danger"></span>
                       </div>
                       @endif
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group">
                            <label class="col-form-label" for="employee_id"><b>DEAN / HOD</b></label>
                            <select class="form-control" name="employees" id="employees" onchange="getDepartments(this.value)">
                                <option value="">--Please Select Employee--</option>
                            </select>
                            <input type="hidden" id="empDeptId" name="empDeptId">
                            <span id="employees-error" class="error text-danger"></span>
                        </div>
                    </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group">
                            <label class="col-form-label" for="employee_id"><b>Completion Date</b></label>
                            <input type="date" class="form-control" id="requiredDays" name="requiredDays">
                            {{-- <span id="employees-error" class="error text-danger"></span> --}}
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden"  name="request_from" id="request_from" value="<?php  echo auth()->user()->username;?>">
                        <input type="hidden"  name="requestId" id="requestId" value="84">
                        <label for="exampleFormControlTextarea1"><h5>Description:
                        </h5></label>
                        <textarea id="message" oninput="checkLimit()" name="message" class="form-control"
                        placeholder="Please Enter Details..."
                        style="border: 1px solid #424e79 !important; height: 100px;"></textarea>

                        <div class="text-left m-1">
                            <span id="countLimit" class="badge badge-pill badge-primary text-right"></span> <br>
                            <span id="message-error" class="error text-danger"></span>
                        </div>
                    </div>
                        <div class="row justify-content-md-center">
                            <div class="col col-lg-12">
                                {{-- <button  type="button" name="submitButton" id="submitButton"  class="btn btn-primary">Submit</button> --}}
                                <button id="submitButton" type="button" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="button-text">Submit</span>
                                </button>
                                <button type="button"  class="btn btn-secondary" onclick="clearForm()">Cancel</button>
                                <h5 id="successMsg" class="m-3" style="color:green"></h5>
                        </div>

                    </div>
                </div>
            </form>
                </div>
            </div>
    </div>
</div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">

function getEmployee(id){
$.ajax({
    type: 'GET',
    url:'get-employees?schoolId='+id,
    headers: {
        "Authorization": 'Bearer {{ auth()->user()->api_token }}',
        "Accept": "application/json"
      },
    success: function(response) {
        updateEmployees(response);
    },
    error: function(xhr) {

    }
});}
function getEmployee1(id){
$.ajax({
    type: 'GET',
    url:'get-employees?deptId='+id,
    headers: {
        "Authorization": 'Bearer {{ auth()->user()->api_token }}',
        "Accept": "application/json"
      },
    success: function(response) {
        updateEmployees(response);
    },
    error: function(xhr) {

    }
});}

function getDepartments(id){
$.ajax({
    type: 'GET',
    url:'get-departments?empId='+id,
    headers: {
        "Authorization": 'Bearer {{ auth()->user()->api_token }}',
        "Accept": "application/json"
      },
    success: function(response) {
        $('#empDeptId').val(response.emp_dept_id);

    },
    error: function(xhr) {

    }
});}

function updateDepartments(departments) {
        var departmentDropdown = document.getElementById('departments');
        departmentDropdown.innerHTML = '<option value="">--Please Select Department--</option>';

        departments.forEach(function(department) {
            var option = document.createElement('option');
            option.value = department.dept_id; // Assuming your department object has an 'id' property
            option.text = department.dept_name; // Assuming your department object has a 'name' property
            departmentDropdown.add(option);
        });
    }
    function updateEmployees(employees) {
    var employeeDropdown = document.getElementById('employees');
    employeeDropdown.innerHTML = '<option value="">--Please Select Employee--</option>';
    employees.forEach(function(employee) {
        var option = document.createElement('option');
        option.value = employee.employee_id; // Assuming your employee object has an 'id' property
        //option.innerHTML = employee.emp_name + '<small class="text-info">  (' + employee.user_email + ')' + employee.emp_dept_name +   ' </small>';
        option.innerHTML = employee.emp_name + '<small class="text-info">  (' + employee.emp_dept_name + ')' +   ' </small>';

        employeeDropdown.add(option);
    });
}

function displayValidationErrors(xhr){
       if (xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $("#" + key + "-error").text(value[0]);
                        $("#" + key + "-error").addClass('text-danger');
                        setTimeout(function(){
                            $("#" + key + "-error").text('');
                     },5000);
                    });
                }
              }

// Attach click event handlers to columns with class "column-link"
$(document).ready(function() {
    $("#submitButton").click(function(e) {
    e.preventDefault(); // Prevent the default form submission behavior

      // Show spinner and change text
    $(this).find('.spinner-border').removeClass('d-none');
    $(this).find('.button-text').text('Submitting...');

    var bi_form = document.querySelector('#mainForm');
    var data = new FormData(bi_form);
    var baseUrl = window.location.origin;
    // console.log(baseUrl);
    var button = $(this);
    button.attr('disabled', true);
    var spinnerIcon = '<i class="fa fa-spinner fa-spin"></i>';
    button.html(spinnerIcon + ' Submitting...');

    $.ajax({
        type: 'POST',
        url: baseUrl+'/add-ePortal-request',
        data: data,
        contentType: false, // Don't set csontent type
        processData: false, // Don't process the data
        headers: {
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
          },
        success: function(response) {
            if (response.status) {
                $('#successMsg').html(response.message);
                document.getElementById("mainForm").reset();
                 button.html('<i class="fa fa-check"></i>Submitted');
                 button.attr('disabled', false);
                setTimeout(function() {
            $('#successMsg').html('');
            window.location.href = 'e-portal-pending';
        }, 3000);
            }
            if (!response.status) {

                if (response.requestStatus) {
                    $('#successMsg')
                .html(response.requestStatus)
                .addClass('text-danger');
                            setTimeout(function() {
                         $('#successMsg').html('');
                         $('#submitButton').attr('disabled', false);
                         button.text('Submit');
                         button.attr('disabled', false);
                         }, 3000);

                }
                else{
                    $('#successMsg')
                .html('Error..!! please try again')
                .addClass('text-danger');
                            setTimeout(function() {
                         $('#successMsg').html('');
                         $('#submitButton').attr('disabled', false);
                         button.text('Submit');
                         button.attr('disabled', false);
                         }, 3000);
                }

            }
        },
        error: function(xhr) {
            // Handle the error case here
            var $button = $("#submitButton");
            button.html('Submit');
                 button.attr('disabled', false);
            displayValidationErrors(xhr);
            // You can also display an error message to the user

            // alert('Form submission failed');
        }
    });
});

});


function clearForm() {
    var bi_form = document.querySelector('#mainForm');
    bi_form.reset();
}


let counter = 1000; // Updated character limit to 250
countLimit.textContent = counter;
function checkLimit() {
    let limit = 1000; // Updated character limit to 250
    // Get the input element (text area) by its ID
    const inputMessage = document.getElementById('message');
    let inputValue = inputMessage.value;
    const countLimit = document.getElementById('countLimit');


    // Check if the input value is greater than 250 characters
    if (inputValue.length > limit) {
        // Truncate to the first 250 characters if character length exceeds 250
        inputValue = inputValue.substring(0, limit);
        inputMessage.value = inputValue;
    }

    // Use classList to toggle classes for badge color
    countLimit.classList.remove('badge-danger');
    countLimit.classList.add('badge-primary');

    // Update the counter to show how many characters are left
    counter = limit - inputValue.length;
    countLimit.textContent = counter;
}


</script>
@stop
