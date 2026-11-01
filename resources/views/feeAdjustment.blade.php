@extends('layouts.app')

@section('title')
  BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
<style>
    .success-flag {
            position: fixed;
            top: 170px;
            right: -300px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: right 0.5s;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }
        .success-flag.show {
            right: 0px;
        }
        .edit-btn, .delete-btn{
            width:76px;
        }
        .error-flag {
            position: fixed;
            top: 170px;
            right: -300px;
            background-color: #f44336; /* Red color for error */
            color: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: right 0.5s;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }
        .error-flag.show {
            right: 0px;
        }
</style>
</head>

@section('content')
<div id="success-flag" class="success-flag"></div>
<div id="error-flag" class="error-flag"></div>
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <div class="row"><h3>Fee Adjustment:</h3></div>
        <form id="searchForm">
            <div class="row">
                <div class="col-6 col-sm-12 col-md-6" style="padding-right: 250px;">
                    <label for="challan">Search by Challan No.</label>
                    <div class="input-group">
                        <input placeholder="Enter Challan number" class="form-control" type="text" name="studentChallanNo" id="studentChallan" onkeypress="enterPressChallanSearch()">
                        <div class="input-group-append">
                            <button onclick='search("challan")' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                    <small class="searchChallan-disclaimer"></small>
                    <div id="searchError_challanmessage" class="alert alert-danger" style="display:none;"></div>
                </div>
                <div class="col-6 col-sm-12 col-md-6" style="padding-right: 250px;">
                    <label for="challan">Search by Enrollment No.</label>
                    <div class="input-group">
                        <input placeholder="Enter Enrollment number" class="form-control" type="text" name="studentEnrollmentNo" id="studentEnrollment" onkeypress="enterPressEnrollSearch()">
                        <div class="input-group-append">
                            <button onclick='search("enrollment")' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                    <small class="searchEnrollment-disclaimer"></small>
                    <div id="searchError_enrollmessage" class="alert alert-danger" style="display:none;"></div>
                </div>
            </div>
        </form>

        <div id="modal-1" class="modal fade" style="background-color: rgba(255, 255, 255, 0.2);" role="dialog">
            <div class="modal-dialog" style="top: 40%;transform: translateY(-40%)">
                <!-- Modal content-->
                <div class="modal-content" style="width:500px;">
                    <div class="modal-header" style="background-color:#ededed;">
                        <h1 class="modal-title" style="font-size: 25px;">Action</h1>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-1" method="POST" action="{{ route('changeStatusFeeAdjustment') }}" data-toggle="validator">
                            @csrf
                            <div class="row" style="margin-top: 12px;">
                                <input hidden id="main_id" name="main_id" value="">
                                <div class="col-md-3 text-left">
                                    <label for="status-dropdown" class="col-form-label">Status:</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="selectpicker form-control" id="status-dropdown" name="status">
                                        <option value="" disabled selected hidden>Select Status</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Partial Paid">Partial Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 12px;">
                                <div class="col-md-3 text-left">
                                    <label for="ajdustAmount" class="col-form-label">Amount:</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="amount" id="amount" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top:12px;">
                                        <label for="remarks">Remarks:</label>
                                        <textarea class="form-control" value="" id="status_remarks" name="status_remarks" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                        <div class="clear clear20"></div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-danger alert-dismissible" id="error-message-1" style="display:none">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

        <div id="dataDiv"   style="margin-top:20px;">
        </div>
        <div class="row" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                              <tr>
                                <th scope="col">Challan No.</th>
                                <th scope="col">Enrollment No.</th>
                                <th scope="col">Apply Id</th>
                                <th scope="col">Category</th>
                                <th scope="col">Type</th>
                                <th scope="col">Surplus</th>
                                <th scope="col">Amount Status</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                          </table>
                    </div>
                </div>
            </div>
    </div>









<hr>

<div id="showDataInTable" class="table-responsive"></div>

    </div>
</div>
@stop

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

@section('javascript')
<script>
$(document).ready(function(){
    // Handle form submission
    $('#form-1').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'), // Use the form's action attribute
            data: formData,
            dataType: 'json',
            success: function(res) {
                $("#modal-1").modal('hide');
                loadFeeAdjustments();
                // Show success flag
                $('#success-flag').text('Record updated successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
            },
            error: function(res) {
                var message = "";
                var data = res.responseJSON.errors;
                $.each(data, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
                $('#error-message-1 span').html(message);
                $('#error-message-1').fadeIn('slow');
            }
        });
    });
    loadFeeAdjustments();
    $('#modal-1').on('hidden.bs.modal', function (e) {
        $('#form-1')[0].reset();
    });
});
function enterPressChallanSearch() {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        search("challan");
    }

}
function enterPressEnrollSearch() {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        search("enrollment")
    }

}

// function studentScholarshipRec(data) {
//     let showDataInTable = document.getElementById('showDataInTable');
//     showDataInTable.innerHTML = ''; // Clear previous content

//     if (data && data.length > 0) {
//         let createHtml = `
//             <table class="table table-bordered table-hover">
//                 <thead class="thead-light">
//                     <tr>
//                         <th scope="col">Scholarship</th>
//                         <th scope="col">Type</th>
//                         <th scope="col">Given Percentage</th>
//                         <th scope="col">Given Amount</th>
//                         <th scope="col">Status</th>
//                         <th scope="col">Action</th>
//                     </tr>
//                 </thead>
//                 <tbody>
//                 ${data.map(element => `
//                         <tr>
//                             <td>${element.scholarship_name}</td>
//                             <td>${element.fsa_search_type}</td>
//                             <td>${element.fsa_percentage}%</td>
//                             <td>${element.fsa_given_scholarship_amount} -/ PKR</td>
//                             <td>
//                                 ${element.fsa_ro_status === 'Pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
//                                 ${element.fsa_ro_status === 'Approved' ? '<span class="badge badge-success">Approved</span>' : ''}
//                                 ${element.fsa_ro_status === 'Disapproved' ? '<span class="badge badge-danger">Disapproved</span>' : ''}
//                             </td>
//                             <td>
//                                 <button class="btn btn-primary edit-scholarship" data-scholarship='${JSON.stringify(element)}'>Edit</button>
//                                 <button class="btn btn-danger" onclick="deleteRec(${element.fsa_id})">Delete</button>
//                             </td>
//                         </tr>
//                     `).join('')}

//                 </tbody>
//             </table>
//         `;

//         showDataInTable.innerHTML = createHtml;

//         // Add event listeners to dynamically created buttons
//         let editButtons = document.querySelectorAll('.edit-scholarship');
//         editButtons.forEach(button => {
//             button.addEventListener('click', editScholarshipRec);
//         });
//     } else {
//         showDataInTable.innerHTML = '<p>No data available</p>';
//     }
// }
function submitFormBtn() {
    // Serialize form data
    var formData = $('#submitFeeAdjustment').serialize();
    // Make an AJAX request
    // console.log(formData);
    $.ajax({
        type: 'POST',
        url: '/addfeeadjustment',
        data: formData,
        success: function(data) {
            refreshFields();
            console.log(data);
            $("#error_message_challan").text('').hide();
            $('#success_message_challan').html('Saved Successfully');
            $('#success_message_challan').fadeIn('slow');
            $('#success_message_challan').fadeOut(5000);
            loadFeeAdjustments();

        },
        error: function(xhr, status, error) {
            var data = res.responseJSON;
            var message = "";
            if ('msg' in data) {
                message = data.msg;
            } else if ('errors' in data) {
                var errors = data.errors;
                $.each(errors, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
            }
            $('#success_message_challan').text('').hide();
            $('#error_message_challan').html(message);
            $('#error_message_challan').fadeIn('slow');
            $('#error_message_challan').fadeOut(5000);
        }
    });
}
function submitEnrollmentFormBtn() {
    // Serialize form data
    var formData = $('#submitEnrollmentFeeAdjustment').serialize();
    // Make an AJAX request
    console.log(formData);
    $.ajax({
        type: 'POST',
        url: '/addfeeadjustment',
        data: formData,
        success: function(res) {
            refreshFields();
            $("#error_message_enrollment").text('').hide();
            $('#success_message_enrollment').html('Saved Successfully');
            $('#success_message_enrollment').fadeIn('slow');
            $('#success_message_enrollment').fadeOut(5000);
            loadFeeAdjustments();

        },
        error: function(res, xhr, status, error) {
            var data = res.responseJSON;
            var message = "";
            if ('msg' in data) {
                message = data.msg;
            } else if ('errors' in data) {
                var errors = data.errors;
                $.each(errors, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
            }
            $('#success_message_enrollment').text('').hide();
            $('#error_message_enrollment').html(message);
            $('#error_message_enrollment').fadeIn('slow');
            $('#error_message_enrollment').fadeOut(5000);
        }
    });
}
function search(searchBy) {
    var valid = true;
    valid = validateSearchBar(searchBy, valid);//this function will make valid variable false if fields are empty
    if(valid){
        let form = document.getElementById('searchForm');
        let data = new FormData(form);
        let challNo = $('#studentChallan').val();
        let enrollmentNo = $('#studentEnrollment').val();
        if(searchBy == 'challan'){
            fetch('/api/feeadjustment/get-challan-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, challanData, adjustmentData }) => { // Correct destructuring syntax
                if (success) {
                    console.log(success);
                    makeChallanDataHtml(challanData, adjustmentData);
                    $("#error_message").text('').hide();
                }
                else{
                    console.log(success);
                    // $('#success_message').text('').hide();
                        $('#searchError_challanmessage').html('No Data found against challan no. '+challNo);
                        $('#searchError_challanmessage').fadeIn('slow');
                        $('#searchError_challanmessage').fadeOut(5000);
                // const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
                // const message = usernameError;
                // const alertHTML = createAlert(notificationType, message);
                // dataDiv.innerHTML = alertHTML;
                    // return false;
                }
            })
        }else if(searchBy == 'enrollment'){
            fetch('/api/feeadjustment/get-enrollment-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, enrollmentData, adjustmentData }) => { // Correct destructuring syntax
                if (success) {
                    console.log(success);
                    makeEnrollmentDataHtml(enrollmentData, adjustmentData);
                    $("#error_message").text('').hide();
                }
                else{
                    console.log(success);
                    // $('#success_message').text('').hide();
                        $('#searchError_enrollmessage').html('No Data found against Enrollment no. '+enrollmentNo);
                        $('#searchError_enrollmessage').fadeIn('slow');
                        $('#searchError_enrollmessage').fadeOut(5000);
                // const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
                // const message = usernameError;
                // const alertHTML = createAlert(notificationType, message);
                // dataDiv.innerHTML = alertHTML;
                    // return false;
                }
            })
        }

    }

}
function makeChallanDataHtml(challanData, adjustmentData) {

    let dataDiv = document.getElementById('dataDiv');
    dataDiv.innerHTML = ''; // Clear previous content
    let sr = 1;
    console.log(challanData);
    console.log(adjustmentData);

    // if (feeChallanAmount==null) {
    //     const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
    //     const message = 'Error in fetching Challan Details';
    //     const alertHTML = createAlert(notificationType, message);
    //     dataDiv.innerHTML = alertHTML;
    // return false;
    // }

    //  <p class="lead"><strong>Department:</strong> ${data.emp_dept_name}</p>
    let optionHtml = '';
    adjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });
    console.log(optionHtml);
    if (challanData) {
        var enrollmentNo = challanData.sc_enrollment_no ? challanData.sc_enrollment_no : 'NA';
        var sc_scholarship_amount = challanData.sc_scholarship_amount ? challanData.sc_scholarship_amount : 'NA';
        let lettersHtml = `
    <div class="container-fluid">
        <div class="row align-items-center" style="margin-bottom: 25px;">
            <!-- Enrollment Number -->
            <div class="col-md-3 col-sm-3 col-12 ">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                    <strong class="d-inline">Challan No:</strong> <span>${challanData.id}</span>
                </p>
            </div>

            <!-- Email -->
            <div class="col-md-3 col-sm-3 col-12 ">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                    <strong class="d-inline">Enrollment No:</strong> <span class="d-inline">${enrollmentNo}</span>
                </p>
            </div>

            <div class="col-md-3 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0" style="font-size: 16px;">
                        <strong class="d-inline">Apply ID:</strong> <span>${challanData.sc_app_id}</span>
                    </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0" style="font-size: 16px;">
                        <strong class="d-inline">Total Amount:</strong> <span>${challanData.actual_amount}</span>
                    </p>
            </div>

        </div>
        <div class="row align-items-center" style="margin-bottom: 25px;">
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Challan Status:</strong> <span>${challanData.sc_challan_status}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Student Status:</strong> <span>${challanData.sc_student_status}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Challan Type:</strong> <span>${challanData.sc_challan_type}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Scholarship Amount:</strong> <span>${sc_scholarship_amount}</span>

                </p>
            </div>
        </div>

        <form id="submitFeeAdjustment">
            @csrf
            <div class="row align-items-center" style="margin-bottom: 25px;">
                <div class="form-group col-md-3 col-sm-3 col-12">
                    <label for="CategoryOption">Select Adjustment Category:</label>
                    <select name="CategoryOption" id="CategoryOption" class="form-control">
                        <option value="" selected disabled>--- Select Adjustment Category ---</option>`+
                        optionHtml
                    +`</select>
                </div>
                <div class="col-md-3 col-sm-2 col-12  mb-3">
                    <label for="surplus">Surplus Amount:</label>
                    <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="surplus_amount" id="surplus_amount" class="form-control">
                </div>
                <div class="col-md-6 col-sm-2 col-12  mb-3">
                    <label for="remarks">Remarks:</label>
                    <input type="textarea" name="remarks" id="remarks" class="form-control">
                </div>
            </div>
            <input type="text" hidden name="challan_no" value="${challanData.id}" class="form-control">
            <input type="text" hidden name="enrollment_no" value="${challanData.sc_enrollment_no}" class="form-control">
            <input type="text" hidden name="app_id" value="${challanData.sc_app_id}" class="form-control">
            <input type="text" hidden name="actual_amount" value="${challanData.actual_amount}" class="form-control">
            <input type="text" hidden name="invoice_amount" value="${challanData.invoiceamount}" class="form-control">
            <input type="text" hidden name="challan_status" value="${challanData.sc_challan_status}" class="form-control">
            <input type="text" hidden name="student_status" value="${challanData.sc_student_status}" class="form-control">
            <input type="text" hidden name="challan_type" value="${challanData.sc_challan_type}" class="form-control">
            <input type="text" hidden name="scholarship_amount" value="${challanData.sc_scholarship_amount}" class="form-control">


            <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                <button type="button" class="btn btn-primary" id="saveAdjustment" onclick="submitFormBtn()">Save</button>
            </div>
        </form>
        <div id="error_message_challan" class="alert alert-danger" style="display: none;"></div>
        <div id="success_message_challan" class="alert alert-success" style="display: none;"></div>

    </div>

    `;
    // <div class="col-md-3 col-sm-2 col-12  mb-3">
    //             <p class="lead text-muted mb-0">
    //                 <strong class="d-inline">Invoice Amount:</strong> <span class="text-uppercase">${challanData.invoiceamount}</span>
    //                 <input type="hidden" name="actual_amount" id="actual_amount" value=${challanData.actual_amount}
    //             </p>
    //         </div>
    dataDiv.innerHTML = lettersHtml;
        } else {
            // Optionally display a message when there is no data
            dataDiv.innerHTML = '<p>No data available.</p>';
        }
}
function makeEnrollmentDataHtml(enrollmentData, adjustmentData) {

    let dataDiv = document.getElementById('dataDiv');
    dataDiv.innerHTML = ''; // Clear previous content
    let sr = 1;
    console.log(enrollmentData);
    console.log(adjustmentData);

    // if (feeChallanAmount==null) {
    //     const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
    //     const message = 'Error in fetching Challan Details';
    //     const alertHTML = createAlert(notificationType, message);
    //     dataDiv.innerHTML = alertHTML;
    // return false;
    // }

    //  <p class="lead"><strong>Department:</strong> ${data.emp_dept_name}</p>
    let optionHtml = '';
    adjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });
    console.log(optionHtml);
    if (enrollmentData) {
        let lettersHtml = `
    <div class="container-fluid">
        <div class="row align-items-center" style="margin-bottom: 25px;">
            <!-- Enrollment Number -->
            <div class="col-md-3 col-sm-3 col-12 ">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                    <strong class="d-inline">Enrollment No:</strong> <span class="d-inline">${enrollmentData.sai_enrollment_no}</span>
                </p>
            </div>

            <div class="col-md-3 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0" style="font-size: 16px;">
                        <strong class="d-inline">Student Name:</strong> <span>${enrollmentData.spi_name}</span>
                    </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                    <strong class="d-inline">Student Status:</strong> <span>${enrollmentData.sai_student_status}</span>
                </p>
            </div>

        </div>
        <div class="row align-items-center" style="margin-bottom: 25px;">
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Session:</strong> <span>${enrollmentData.sess_name}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">School:</strong> <span>${enrollmentData.school_name}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Department:</strong> <span>${enrollmentData.dept_name}</span>

                </p>
            </div>
            <div class="col-md-3 col-sm-2 col-12  mb-3">
                <p class="lead text-muted mb-0" style="font-size: 16px;">
                <strong class="d-inline">Program:</strong> <span>${enrollmentData.prog_name}</span>

                </p>
            </div>
        </div>

        <form id="submitEnrollmentFeeAdjustment">
            @csrf
            <div class="row align-items-center" style="margin-bottom: 25px;">
                <div class="form-group col-md-3 col-sm-3 col-12">
                    <label for="CategoryOption">Select Adjustment Category:</label>
                    <select name="CategoryOption" id="CategoryOption" class="form-control">
                        <option value="" selected disabled>--- Select Adjustment Category ---</option>`+
                        optionHtml
                    +`</select>
                </div>
                <div class="col-md-3 col-sm-2 col-12  mb-3">
                    <label for="surplus">Surplus Amount:</label>
                    <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="surplus_amount" id="surplus_amount" class="form-control">
                </div>
                <div class="col-md-6 col-sm-2 col-12  mb-3">
                    <label for="remarks">Remarks:</label>
                    <input type="textarea" name="remarks" id="remarks" class="form-control">
                </div>
            </div>
            <input type="text" hidden name="sai_id" value="${enrollmentData.sai_id}" class="form-control">
            <input type="text" hidden name="enrollment_no" value="${enrollmentData.sai_enrollment_no}" class="form-control">
            <input type="text" hidden name="student_status" value="${enrollmentData.sai_student_status}" class="form-control">

             <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                <button type="button" class="btn btn-primary" id="saveAdjustment" onclick="submitEnrollmentFormBtn()">Save</button>
            </div>
        </form>
        <div id="error_message_enrollment" class="alert alert-danger" style="display: none;"></div>
        <div id="success_message_enrollment" class="alert alert-success" style="display: none;"></div>

    </div>

    `;
    dataDiv.innerHTML = lettersHtml;
        } else {
            // Optionally display a message when there is no data
            dataDiv.innerHTML = '<p>No data available.</p>';
        }
}
function validateSearchBar(searchBy, valid){
    let challNo = $('#studentChallan').val();
    let enrollmentNo = $('#studentEnrollment').val();
    // console.log(challNo);
    // console.log(enrollmentNo);
    if(searchBy == 'challan' && (challNo == '' || !/^\d+$/.test(challNo)) ){
        $('.searchChallan-disclaimer').text('Enter valid Challan No.');
        $('.searchChallan-disclaimer').css('color', 'red');
        $('.searchEnrollment-disclaimer').text('');
        valid = false;
    }else{
        $('.searchChallan-disclaimer').text('');
        $('.searchEnrollment-disclaimer').text('');
    }
    if(searchBy == 'enrollment' && (enrollmentNo == '' || /^\d+$/.test(enrollmentNo))){
        $('.searchEnrollment-disclaimer').text('Enter valid Enrollment No.');
        $('.searchEnrollment-disclaimer').css('color', 'red');
        $('.searchChallan-disclaimer').text('');
        valid = false;
    }
    else{
        $('.searchChallan-disclaimer').text('');
        $('.searchEnrollment-disclaimer').text('');
    }
    return valid;
}
function loadFeeAdjustments(){
    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: true,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 10,
        order: [1, 'desc'],
        ajax:{
        dataType: 'json',
        method: 'post',
        url:  "/api/feeadjustment/getFeeAdjustmentList",
        },
        columns: [
            {
                data: "ffa_sc_challan_no",
                render: function(data, type, row) {
                    return data ? data : 'NA';
                }
            },
            {
                data: "ffa_enrollment_no",
                render: function(data, type, row) {
                    return (data && data !== 'null') ? data : 'NA';
                }
            },
            {
                data: "ffa_app_id",
                render: function(data, type, row) {
                    return (data && data !== 'null') ? data : 'NA';
                }
            },
            {data: "ffac_title"},
            {data: "name"},
            {data: "ffa_surplus_amount"},
            {
                data: "ffa_amount_status",
                render: function(data, type, row) {
                    if (data === 'Paid') {
                        return `<span class="badge badge-success">${data}</span>`;
                    }else if(data === 'Partial Paid'){
                        return `<span class="badge badge-warning">${data}</span>`;
                    }else{
                        return `<span class="badge badge-danger">${data}</span>`;
                    }
                }
            },
            {
                data: "ffa_remarks",
                render: function(data, type, row) {
                    return data ? data : 'NA';
                }
            },
        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    console.log(row);
                    var button = '<div>'+
                                    '<button class="btn btn-primary btn-sm edit-btn" id="'+row["ffa_id"]+'"  onclick="showModal('+row["ffa_id"]+')">Action</button>'+
                                        '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>'+
                                    '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffa_id"]+'])"><i class="fa fa-trash"></i> Delete</button>'+
                                '</div>';

                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 8,
            }
        //     {
        //         render: function (data, type, row) {
        //                 var   selectbtn = '<div><input name="fee_ids[]" class="select-data" idData='+row["id"]+' value='+row["id"]+' type="checkbox" ></div>';

        //             return selectbtn;

        //         },
        //         // className: 'dt-body-center',
        //         // className: 'dt-head-center',
        //         targets: 13,
        //         searchable: false,
        //         orderable: false
        //     },

        //     {
        //         "width": "100px", "targets": 1
        //     }
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
    });
}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/feeadjustment/delete_feeAdjustment",
            data: {
                mainId: mainId
            },
            success: function(response) {
                if(response.success){
                    $('#success-flag').text(response.messege);
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    loadFeeAdjustments();
                }else{
                    $('#error-flag').text(response.messege);
                    $('#error-flag').addClass('show');
                    setTimeout(function() {
                        $('#error-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                }
            },
            error: function(error) {
                // Handle the error
                 $('#error-flag').text('Error');
                $('#error-flag').addClass('show');
                setTimeout(function() {
                    $('#error-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                console.error("Error:", error);
            }
        });
    } else {

    }

}
function showModal(mainId){
    $('#main_id').val(mainId);
    $('#status-dropdown').val('');
    $('#status_remarks').val('');

    $('#modal-1').modal('show');
}
function refreshFields(){
    $('#CategoryOption').val('');
    $('#surplus_amount').val('');
    $('#remarks').val('');
}

</script>
@stop
