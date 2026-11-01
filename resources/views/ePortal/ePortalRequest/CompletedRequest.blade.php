@extends('layouts.app')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')

<style>
/* Figma-aligned MCM Portal Completed screen presentation layer.
   Original Blade conditions, data bindings, table structure and JavaScript remain unchanged. */
.mcm-completed-page {
    width: 100% !important;
    max-width: 100% !important;
    min-height: calc(100vh - 20px);
    margin: 0 !important;
    /* padding: 30px !important; */
    border-radius: 10px;
    background: #f2f6f9;
    font-family: 'Montserrat', sans-serif;
    color: #252525;
    
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.mcm-completed-page > .card {
    margin: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

/* Figma heading */
.mcm-completed-page > .card > .card-header {
    padding: 0 0 20px 0;
    border: 0;
    background: transparent;
}

.mcm-completed-page > .card > .card-header h5 {
    margin: 0;
    color: #252525;
    font-size: 20px;
    line-height: 1.35;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.mcm-completed-page > .card > .card-header h5::after {
    content: 'Improve Performance';
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 14px;
    line-height: 1.45;
    font-weight: 400;
    letter-spacing: 0;
}

.mcm-completed-page .card-block.tab-icon {
    padding: 0;
    background: transparent;
}

/* Pill navigation */
.mcm-completed-page .container-fluid {
    padding: 0;
}

.mcm-completed-page .tab-menu-heading,
.mcm-completed-page .tabs-menu1 {
    padding: 0 !important;
    background: transparent;
}

.mcm-completed-page .tabs-menu1 .nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 22px;
    padding: 0;
}

.mcm-completed-page .tabs-menu1 .nav-item {
    margin: 0 !important;
}

.mcm-completed-page .tabs-menu1 .nav-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 40px;
    padding: 10px 20px !important;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    background: rgba(255,255,255,.72);
    color: #64748b;
    font-size: 14px !important;
    line-height: 1;
    font-weight: 600;
    text-decoration: none;
    box-shadow: none;
}

.mcm-completed-page .tabs-menu1 .nav-link i {
    font-size: 13px;
}

.mcm-completed-page .tabs-menu1 .nav-link.active {
    border-color: #17675f;
    background: #17675f;
    color: #fff;
}

.mcm-completed-page .tabs-menu1 .nav-link:hover,
.mcm-completed-page .tabs-menu1 .nav-link:focus {
    border-color: #17675f;
    color: #17675f;
}

.mcm-completed-page .tabs-menu1 .nav-link.active:hover,
.mcm-completed-page .tabs-menu1 .nav-link.active:focus {
    color: #fff;
}

.mcm-completed-page .tabs-menu1 .badge {
    min-width: 22px;
    height: 22px;
    margin-left: 2px;
    padding: 4px 7px;
    border-radius: 999px;
    background: #eef7f5 !important;
    color: #17675f !important;
    font-size: 10px;
    line-height: 14px;
    font-weight: 700;
}

.mcm-completed-page .tabs-menu1 .nav-link.active .badge {
    background: rgba(255,255,255,.18) !important;
    color: #fff !important;
}

/* Completed requests table card */
.mcm-completed-page .card-body {
    margin: 0;
    padding: 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 16px rgba(15,23,42,.04);
}

.mcm-completed-page .table-responsive {
    width: 100%;
    margin: 0;
    border-radius: 12px;
    overflow-x: auto;
    overflow-y: visible;
}

.mcm-completed-page .table {
    width: 100% !important;
    min-width: 900px;
    margin: 0;
    border: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    color: #252525;
    font-size: 13px;
}

.mcm-completed-page .table thead {
    background: #17675f;
}

.mcm-completed-page .table thead th {
    padding: 16px 20px !important;
    border: 0 !important;
    border-bottom: 1px solid #17675f !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
}

.mcm-completed-page .table thead th:first-child {
    width: 12%;
    border-top-left-radius: 12px;
}

.mcm-completed-page .table thead th:nth-child(2) {
    width: 34%;
}

.mcm-completed-page .table thead th:nth-child(3) {
    width: 30%;
}

.mcm-completed-page .table thead th:last-child {
    width: 24%;
    border-top-right-radius: 12px;
}

.mcm-completed-page .table tbody td {
    padding: 16px 20px !important;
    border: 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #fff;
    color: #334155;
    font-size: 13px;
    line-height: 1.45;
    vertical-align: middle;
}

.mcm-completed-page .table tbody tr:nth-child(even) td {
    background: #f8fafc;
}

.mcm-completed-page .table tbody tr:last-child td {
    border-bottom: 0 !important;
}

.mcm-completed-page .table tbody td:nth-child(2),
.mcm-completed-page .table tbody td:nth-child(3) {
    white-space: normal;
    overflow-wrap: anywhere;
}

/* Remarks */
.mcm-completed-page .table .text-success,
.mcm-completed-page .table .text-danger {
    display: inline-block;
    padding-left: 0 !important;
    font-size: 13px;
    font-weight: 500;
}

.mcm-completed-page .table .text-success {
    color: #14845f !important;
}

.mcm-completed-page .table .text-danger {
    color: #c94d5e !important;
}

/* Status and download actions */
.mcm-completed-page .table td:last-child {
    white-space: nowrap;
}

.mcm-completed-page .table td:last-child > .btn {
    width: auto !important;
    min-width: 108px;
    min-height: 36px;
    margin: 2px 6px 2px 0;
    padding: 8px 14px;
    border-radius: 8px !important;
    font-size: 12px;
    line-height: 1.15;
    font-weight: 600;
    box-shadow: none !important;
}

.mcm-completed-page .table .btn-success {
    border: 1px solid #17675f !important;
    background: #17675f !important;
    color: #fff !important;
}

.mcm-completed-page .table .btn-danger {
    border: 1px solid #c94d5e !important;
    background: #c94d5e !important;
    color: #fff !important;
}

.mcm-completed-page .table td:last-child .btn-success + .btn-success {
    border-color: #17675f !important;
    background: #fff !important;
    color: #17675f !important;
}

.mcm-completed-page .table td:last-child .btn-success + .btn-success a {
    color: #17675f !important;
    text-decoration: none;
}

.mcm-completed-page .table td:last-child .btn a {
    color: inherit !important;
    text-decoration: none;
}

.mcm-completed-page .table td:last-child .btn:hover,
.mcm-completed-page .table td:last-child .btn:focus {
    box-shadow: none !important;
}

.column-link {
    cursor: pointer;
}

.text-info {
    color: #17675f !important;
}

@media (max-width: 991.98px) {
    .mcm-completed-page {
        padding: 24px 16px 34px !important;
    }

    .mcm-completed-page .table {
        min-width: 860px;
    }
}

@media (max-width: 767.98px) {
    .mcm-completed-page {
        padding: 20px 12px 28px !important;
    }

    .mcm-completed-page > .card > .card-header h5 {
        font-size: 18px;
    }

    .mcm-completed-page .tabs-menu1 .nav-link {
        padding: 9px 14px !important;
        font-size: 12px !important;
    }
}
</style>



    <div class="container mcm-completed-page" >




        <div class="card">
            <div class="card-header">
                <h5>MCM-Portal Requests</h5>
            </div>
            <div class="card-block tab-icon">
                <!-- Row start -->


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
                                            <a href="e-portal-pending " class="nav-link ">
                                                <i class="fa fa-hourglass"></i> Pending
                                                <span class="badge badge-info">{{ $CountPending[0]->total_pending }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="e-portal-completed" class="nav-link active">
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



                <!-- Row end -->

           <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered" style="width: 100%;"> <!-- Set table width to 100% -->
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Title</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($res as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->ec_title }} ({{ $row->emp_name }}  |  {{ $row->school_name }}  )
                            </td>
                            <td> @if(!empty($row->er_remarks))

                                <span class="text-<?=($row->er_request_status=='Declined')?'danger':'success';?> pl-3">{{ $row->er_remarks }}</span>
                                @endif</td>
                            <td>
                                <button  style="width: 130px;" type="button" class="btn btn-<?=($row->er_request_status=='Declined')?'danger':'success';?> waves-effect waves-light" data-toggle="tooltip" data-placement="top" >{{$row->er_request_status}}</button>
                                {{-- <button type="button" class="btn btn-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" ><a href="/studentfeedbacklist" style="color:white">Feedback</a></button> --}}

                                @if ($row->google_drive_url)
                                <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="tooltip" data-placement="top">
                                    <a target="__blank" href="{{ $row->google_drive_url }}" style="color:white">
                                      <i class="fa fa-download"></i>Download
                                    </a>
                                  </button>

                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
