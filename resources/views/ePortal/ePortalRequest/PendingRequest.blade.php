@extends('layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')
{{-- @dd("123"); --}}
<style>
/* Figma-aligned MCM Portal Pending presentation layer.
   Original Blade data flow, tabs, table markup, conditions and JavaScript remain unchanged. */

.mcm-pending-page {
    min-height: calc(100vh - 20px);
    /* padding: 28px 30px 40px; */
    margin: 0;
    background: #f2f6f9;
    font-family: 'Montserrat', sans-serif;
    color: #252525;
}

.mcm-pending-page .container {
    width: 100%;
    max-width: 100%;
    padding: 0;
}

.mcm-pending-page > .container > .card {
    margin: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

/* Heading mirrors Figma: title + subtitle */
.mcm-pending-page > .container > .card > .card-header {
    padding: 0 0 18px;
    border: 0;
    background: transparent;
}

.mcm-pending-page > .container > .card > .card-header h5 {
    margin: 0;
    color: #252525;
    font-size: 20px;
    line-height: 1.35;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.mcm-pending-page > .container > .card > .card-header h5::after {
    content: 'Improve Performance';
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 400;
    letter-spacing: 0;
}

.mcm-pending-page .card-block.tab-icon {
    padding: 0;
    background: transparent;
}

/* Figma pill navigation while preserving all original tabs/counts */
.mcm-pending-page .tab-menu-heading,
.mcm-pending-page .tabs-menu1 {
    padding: 0 !important;
    border: 0 !important;
    background: transparent;
}

.mcm-pending-page .tabs-menu1 .nav {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 20px;
    padding: 0;
    font-size: 14px !important;
    border: 0;
}

.mcm-pending-page .tabs-menu1 .nav-item {
    margin: 0 !important;
}

.mcm-pending-page .tabs-menu1 .nav-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 40px;
    padding: 10px 18px;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    background: transparent;
    color: #64748b;
    font-size: 14px;
    line-height: 1;
    font-weight: 600;
    text-decoration: none;
    box-shadow: none;
}

.mcm-pending-page .tabs-menu1 .nav-link i {
    font-size: 13px;
}

.mcm-pending-page .tabs-menu1 .nav-link.active,
.mcm-pending-page .tabs-menu1 .nav-link:hover,
.mcm-pending-page .tabs-menu1 .nav-link:focus {
    border-color: #17675f;
    background: #17675f;
    color: #fff;
}

.mcm-pending-page .tabs-menu1 .nav-link .badge {
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: rgba(23, 103, 95, 0.10) !important;
    color: #17675f !important;
    font-size: 11px;
    font-weight: 600;
}

.mcm-pending-page .tabs-menu1 .nav-link.active .badge,
.mcm-pending-page .tabs-menu1 .nav-link:hover .badge,
.mcm-pending-page .tabs-menu1 .nav-link:focus .badge {
    background: rgba(255, 255, 255, 0.20) !important;
    color: #fff !important;
}

/* Table card */
.mcm-pending-page .card-body {
    padding: 0;
}

.mcm-pending-page .table-responsive {
    width: 100%;
    margin: 0;
    border: 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    overflow-x: auto;
    overflow-y: visible;
}

.mcm-pending-page .table {
    width: 100%;
    min-width: 860px;
    margin: 0;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    color: #0f172a;
    font-size: 13px;
}

.mcm-pending-page .table thead {
    background: #17675f;
}

.mcm-pending-page .table thead th {
    padding: 16px 20px !important;
    border: 0 !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
}

.mcm-pending-page .table thead th:first-child {
    width: 14%;
    border-top-left-radius: 12px;
}

.mcm-pending-page .table thead th:nth-child(2) {
    width: 31%;
}

.mcm-pending-page .table thead th:nth-child(3) {
    width: 39%;
}

.mcm-pending-page .table thead th:last-child {
    width: 16%;
    border-top-right-radius: 12px;
}

.mcm-pending-page .table tbody td {
    padding: 16px 20px !important;
    border: 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #fff;
    color: #252525;
    font-size: 13px;
    line-height: 1.55;
    vertical-align: middle;
    word-break: break-word;
}

.mcm-pending-page .table tbody tr:nth-child(even) td {
    background: #f8fafc;
}

.mcm-pending-page .table tbody tr:last-child td {
    border-bottom: 0 !important;
}

/* Preserve original status condition, only modernize its appearance */
.mcm-pending-page .table tbody td .btn {
    min-width: 118px;
    min-height: 36px;
    padding: 8px 14px;
    border-radius: 999px !important;
    border-width: 1px;
    font-size: 12px;
    line-height: 1;
    font-weight: 600;
    box-shadow: none !important;
    cursor: default;
}

.mcm-pending-page .table tbody td .btn-info {
    border-color: #cfe8e4 !important;
    background: #e9f5f3 !important;
    color: #17675f !important;
}

.mcm-pending-page .table tbody td .btn-danger {
    border-color: #f1c8cf !important;
    background: #fff0f2 !important;
    color: #c94d5e !important;
}

.mcm-pending-page .column-link {
    cursor: pointer;
}

.mcm-pending-page .text-info {
    color: #17675f !important;
}

@media (max-width: 991.98px) {
    .mcm-pending-page {
        padding: 24px 18px 34px;
    }

    .mcm-pending-page .tabs-menu1 .nav {
        flex-wrap: wrap;
    }
}

@media (max-width: 575.98px) {
    .mcm-pending-page {
        padding: 20px 12px 28px;
    }

    .mcm-pending-page .tabs-menu1 .nav-link {
        padding: 9px 14px;
        font-size: 13px;
    }
}
</style>


<div class="mcm-pending-page">
    <div class="container" >

        <div class="card">
            <div class="card-header">
                <h5>MCM-Portal Requests</h5>
            </div>

            <div class="card-block tab-icon">







                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-menu-heading p-0 border-0">
                                <div class="tabs-menu1 px-3">
                                    <ul class="nav" style="font-size:17px;">
                                        <li class="nav-item ml-3 mr-3">
                                            <a href="eportal-request" class="nav-link" >
                                                <i class="fa fa-flag"></i> Request
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="e-portal-pending " class="nav-link active" data-toggle="tab">
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

                {{-- <!-- Row start -->
                <div class="row">
                    <div class="col-lg-12 col-xl-12">
                        <!-- Tab navigation -->
                        <ul class="nav nav-tabs md-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" href="eportal-request" role="tab" aria-expanded="true">
                                    <i class="icofont icofont-flag"></i> Request
                                </a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="e-portal-pending" role="tab" aria-expanded="false">
                                    <i class="icofont icofont-clock-time"></i> Pending

                                    <span class="badge">{{ $CountPending[0]->total_pending }}</span>

                                </a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="e-portal-completed" role="tab" aria-expanded="false">
                                    <i class="icofont icofont-check"></i> Completed
                                    <span class="badge">{{ $CountCompleted[0]->total_completed }}</span>
                                </a>
                                <div class="slide"></div>
                            </li>
                        </ul>
                        <!-- Tab navigation end -->
                    </div>
                </div> --}}

                <div class="card-body">
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" >
                            <thead >
                                <tr >
                                    <th>Ticket ID</th>
                                    <th>Title</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($res as $row)
                                    <tr >
                                        <td>{{ $row->request_id }}</td>
                                        <td>{{ $row->ec_title }} ({{ $row->emp_name }}  {{ $row->school_name }}  )
                                        </td>
                                        <td>{{ $row->er_description }}</td>
                                        <td>                                 <button  style="width: 130px;" type="button" class="btn btn-<?=($row->er_request_status=='Declined')?'danger':'info';?> waves-effect waves-light" data-toggle="tooltip" data-placement="top" >{{$row->er_request_status}}</button></td>
                                        {{-- <td>
                                            <form method="GET" action="{{ route('eportal-application-status') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $row->id }}">
                                                <input type="hidden" name="request_id" value="{{ $row->request_id }}">
                                                <input type="hidden" name="dept_id" value="{{ $row->ec_department_id }}">
                                                <button type="submit" class="btn btn-info">Details</button>

                                            </form>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>






@endsection

@section('javascript')
<script type="text/javascript">



function getDepartments(id){

// alert(id);
$.ajax({
    type: 'GET',
    url:'get-departments?schoolId='+id,
    headers: {
        "Authorization": 'Bearer {{ auth()->user()->api_token }}',
        "Accept": "application/json"
      },
    success: function(response) {
        updateDepartments(response);
    },
    error: function(xhr) {

    }
});}

function getEmployee(id){

// alert(id);
$.ajax({
    type: 'GET',
    url:'get-departments?deptId='+id,
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
        option.value = employee.username; // Assuming your employee object has an 'id' property
        option.innerHTML = employee.emp_name + '<small class="text-info">  (' + employee.user_email + ')</small>';

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
