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
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons {
            display: inline-block;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
            margin-top:5px;
        }

        .dataTables_wrapper .dt-buttons {
            margin-right: 15px;
        }
        .custom-card-header {
            background-color: #ffffff; /* Neutral background */
            color: #4454c3; /* Theme color for text and icons */
            text-align: center; /* Center alignment */
            font-weight: bold; /* Bold text */
            padding: 1rem; /* Vertical padding */
            font-size: 1.2rem; /* Font size */
            border-bottom: 3px solid #4454c3 !important; /* Ensure the border is present */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important; /* Subtle shadow */

        }
        .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
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
                <div class="col-6 col-sm-12 col-md-4" style="padding-right: 100px;">
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
                <div class="col-6 col-sm-12 col-md-4" style="padding-right: 100px;">
                    <label for="challan">Search by Apply Id / Enrollment No.</label>
                    <div class="input-group">
                        <input placeholder="Enter Enrollment number" class="form-control" type="text" name="studentEnrollmentNo" id="studentEnrollment" onkeypress="enterPressEnrollSearch()">
                        <div class="input-group-append">
                            <button onclick='search("enrollment")' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                    <small class="searchEnrollment-disclaimer"></small>
                    <div id="searchError_enrollmessage" class="alert alert-danger" style="display:none;"></div>
                </div>
                <div class="col-6 col-sm-12 col-md-4" style="padding-right: 100px;">
                    <label style="visibility: hidden;">Placeholder</label>
                    <div class="form-check" style="transform: scale(1.2); margin-top: 5px;">
                        <input  class="form-check-input"  type="checkbox" id="adjustmentCheckbox">
                        <label class="form-check-label" for="adjustmentCheckbox">
                            Add New Surplus
                        </label>
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
                                <input hidden id="challan_type" name="challan_type" value="">
                                <div class="col-md-3 text-left">
                                    <label for="status-dropdown" class="col-form-label">Status:</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="selectpicker form-control" id="status-dropdown" name="status">
                                        <option value="">Select Status</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Partial Paid">Partial Paid</option>
                                        <option value="Fee Refund">Fee Refund</option>
                                        <option value="Readmission">Readmission</option>
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
                        <div class="row mt-3">
                            <div class="alert alert-danger alert-dismissible fade show" id="error-message-1" style="display: none;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div id="error-text"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="btn-group ml-2" role="group" aria-label="Filter Challan" id="custom-filter-btn" style="margin-right:14px;">
            <button type="button" class="btn btn-outline-info shadow font-weight-bold rounded-pill dropdown-toggle filterbutton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter"></i> Choose Filter
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" onclick="handleFilter(`ALL`,`All`)">All</a>
                <a class="dropdown-item" onclick="handleFilter(`pending`,`UNPAID`)">UNPAID</a>
                <a class="dropdown-item" onclick="handleFilter(`Paid`, `PAID`)">PAID</a>
            </div>
        </div>

        <div id="dataDiv"   style="margin-top:20px;">
        </div>
        <div class="row d-none filteredAdjustmentData" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-tableAdjustment" class="table display" style="width:100%">
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
        <div class="row d-none filteredAUD_adjustmentData" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-tableAudAdjustment" class="table display" style="width:100%">
                            <thead>
                              <tr>
                                <th scope="col">Challan No.</th>
                                <th scope="col">Enrollment No.</th>
                                <th scope="col">Apply Id</th>
                                <th scope="col">Category</th>
                                <th scope="col">Type</th>
                                <th scope="col">Old Amount</th>
                                <th scope="col">Used Amount</th>
                                <th scope="col">Surplus Amount</th>
                                <th scope="col">Change On</th>
                                <th scope="col">Amount Status</th>
                                <th scope="col">Remarks</th>
                                {{-- <th scope="col">Action</th> --}}
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
     var globalChallanData = '';
     var globalAdjustmentData = '';
$(document).ready(function(){
    loadFeeAdjustments();
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
                if(res.success){
                    $("#modal-1").modal('hide');
                    loadFeeAdjustments();
                    // Show success flag
                    $('#success-flag').text('Record updated successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                }else{
                    var message = "";
                    var data = res.data;
                    $.each(data, function (i, obj) {
                        message += "<p>" + obj + "</p>";
                    });
                    $('#error-text').html(message);
                    $('#error-message-1').fadeIn('slow');
                }

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
    // loadFeeAdjustments();
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
   // console.log(formData);
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
    loadFeeAdjustments();
    $('.filterbutton').html('<i class="fa fa-filter"></i> Filter <span class="filter-bubble">ALL</span>')
    var valid = true;
    valid = validateSearchBar(searchBy, valid);//this function will make valid variable false if fields are empty
    if(valid){
        let form = document.getElementById('searchForm');
        let data = new FormData(form);
        let challNo = $('#studentChallan').val();
        let enrollmentNo = $('#studentEnrollment').val();
        if(searchBy == 'challan'){
            $('#data-tableAdjustment').DataTable().search($('#studentChallan').val()).draw();
            fetch('/api/feeadjustment/get-challan-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, challanData, adjustmentData }) => { // Correct destructuring syntax
                if (success) {
                    //console.log(success);
                    makeChallanDataHtml(challanData, adjustmentData);
                    $("#error_message").text('').hide();
                }
                else{
                 //   console.log(success);
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

            $('#data-tableAdjustment').DataTable().search($('#studentEnrollment').val()).draw();
            fetch('/api/feeadjustment/get-enrollment-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, enrollmentData, adjustmentData, newAdjustmentData }) => { // Correct destructuring syntax
                if (success) {
                //    console.log(enrollmentData);
                   console.log(newAdjustmentData);
                   /////////////
                   const checkbox = document.getElementById('adjustmentCheckbox');
                    if (checkbox.checked) {
                        addNewFeeAdjustment(adjustmentData, newAdjustmentData);
                    } else {
                        makeEnrollmentDataHtml(enrollmentData, adjustmentData);
                        $("#error_message").text('').hide();
                    }
                    ////////

                }
                else{
                   // console.log(success);
                    // $('#success_message').text('').hide();
                        $('#searchError_enrollmessage').html('No data found');
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
    $('#dataDiv').html('');
    let dataDiv = document.getElementById('dataDiv');
    dataDiv.innerHTML = ''; // Clear previous content
    let sr = 1;
    let optionHtml = '';
    adjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });
   // console.log(optionHtml);
    if (challanData) {
        challanHtml =`<div shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header custom-card-header d-flex align-items-center p-2">
                        <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                        <span class="h6 mb-0">Challan Info</span>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <!-- Information Section -->
                            <div class="col-md-12">
                                <div class="row">
                                    ${generateInfoBox('fa fa-money', 'Challan No:', challanData.id || 'N/A' , 3)}
                                    ${generateInfoBox('fa-id-card', 'Enrollment No:', challanData.sc_enrollment_no || 'N/A' , 3)}
                                    ${generateInfoBox('fa-id-card', 'Apply Id:', challanData.sc_app_id || 'N/A' , 3)}
                                    ${generateInfoBox('fa fa-money', 'Total Amount:', challanData.actual_amount || 'N/A' , 3)}
                                    ${generateInfoBox('fa fa-money', 'Challan Status:', challanData.sc_challan_status || 'N/A' , 3)}
                                    ${generateInfoBox('fa fa-money', 'Challan Type:', challanData.sc_challan_type || 'N/A' , 3)}
                                    ${generateInfoBox('fa-id-card', 'Student Status:', challanData.sc_student_status || 'N/A' , 3)}
                                    ${generateInfoBox('fa fa-money', 'Scholarship Amount:', challanData.sc_scholarship_amount || 'N/A' , 3)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- Card Header -->
                    <div class="card-header custom-card-header d-flex align-items-center p-2">
                        <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                        <span class="h6 mb-0">Adjusment Form</span>
                    </div>
                    <!-- Card Body -->
                <form id="submitFeeAdjustment" style='margin-top:10px;'>
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
                `;
        dataDiv.innerHTML = challanHtml;
        } else {
            // Optionally display a message when there is no data
            dataDiv.innerHTML = '<p>No data available.</p>';
        }
}
function makeEnrollmentDataHtml(enrollmentData, adjustmentData) {
    $('#dataDiv').html('');
    let dataDiv = document.getElementById('dataDiv');
    dataDiv.innerHTML = ''; // Clear previous content
    globalChallanData = enrollmentData;
    globalAdjustmentData = adjustmentData;
    let sr = 1;
    let optionHtml = '';
    adjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });
    let challanOptionHtml='';
    enrollmentData.forEach(challanOption => {
        challanOptionHtml +=  `<option value="${challanOption.id}">Challan No. ${challanOption.id}</option>`;
    });
    if (enrollmentData[0]) {

        let imageUrl = enrollmentData[0].api_imagename
                ? `https://admission.bnu.edu.pk/images/${enrollmentData[0].api_imagename}`
                : `https://cms.bnu.edu.pk/students_pics/${enrollmentData[0].sai_enrollment_no.toUpperCase()}.jpg`;
        lettersHtml2 = `
            <div class="container-fluid studentDetail">
                <div shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header custom-card-header d-flex align-items-center p-2">
                        <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                        <span class="h6 mb-0">Student Info</span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <!-- Student Image -->
                            <div class="col-md-2 text-center mb-2">
                                <img
                                    src="${imageUrl}"
                                    alt="Student Image"
                                    class="img-fluid rounded-circle shadow-sm mb-2"
                                    style="width: 80px; height: 80px;">
                                <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${enrollmentData[0].spi_name || enrollmentData[0].sc_studentname|| 'N/A'}</h6>
                                <span class="badge bg-info text-white" style="font-size: 0.8rem;">
                                    ${enrollmentData[0].sai_student_status || 'NEW'}

                                    </span>
                            </div>

                            <!-- Information Section -->
                            <div class="col-md-10">
                                <div class="row">
                                    ${generateInfoBox('fa-id-card', 'Enrollment No:', enrollmentData[0].sai_enrollment_no || 'N/A' , 4)}
                                    ${generateInfoBox('fa-building', 'Session:', enrollmentData[0].sess_name || 'N/A' , 4)}
                                    ${generateInfoBox('fa-building', 'School:', enrollmentData[0].school_name || 'N/A' , 4)}
                                    ${generateInfoBox('fa-university', 'Department:', enrollmentData[0].dept_name || 'N/A' , 4)}
                                    ${generateInfoBox('fa-book', 'Program:', enrollmentData[0].prog_name || 'N/A' , 4)}
                                    <div class="col-md-4 mb-3">
                                        <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
                                            <i class="fa fa-money mr-3 text-muted" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <span class="text-muted d-block" style="font-size: 0.85rem;">Select Challan</span>
                                                    <select name="challan" id="challan" class="form-control" onchange="loadChallanhtml(event)">
                                                    <option value="" selected disabled>--Select Challan--</option>`+
                                                        challanOptionHtml
                                                    +`</select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='challanDataHtml'>

                </div>


            </div>

            `;
            dataDiv.innerHTML = lettersHtml2;
        } else {
            // Optionally display a message when there is no data
            dataDiv.innerHTML = '<p>No data available.</p>';
        }
}
function loadChallanhtml(event){
    // let ChallanDataDiv = document.getElementById('challanDataHtml');

    let ChallanDataDiv = document.querySelector('.challanDataHtml');
    let optionHtml = '';
    globalAdjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });

    const selectedValue = event.target.value;
    // console.log(`selected: ${selectedValue}`);

        $('.challanDataHtml').html('');
         // console.log(globalChallanData);
        const selectedChallan = globalChallanData.find(challan => challan.id === selectedValue);
        // console.log(selectedChallan);
        challanHtml = `<div shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header custom-card-header d-flex align-items-center p-2">
                            <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                            <span class="h6 mb-0">Challan Info</span>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <!-- Information Section -->
                                <div class="col-md-12">
                                    <div class="row">
                                        ${generateInfoBox('fa fa-money', 'Challan No:', selectedChallan.id || 'N/A' , 3)}
                                        ${generateInfoBox('fa-id-card', 'Enrollment No:', selectedChallan.sai_enrollment_no || 'N/A' , 3)}
                                        ${generateInfoBox('fa-id-card', 'Apply Id:', selectedChallan.sc_app_id || 'N/A' , 3)}
                                        ${generateInfoBox('fa fa-money', 'Total Amount:', selectedChallan.sc_installment_tuition_fee_amnt || 'N/A' , 3)}
                                        ${generateInfoBox('fa fa-money', 'Challan Status:', selectedChallan.sc_challan_status || 'N/A' , 3)}
                                        ${generateInfoBox('fa fa-money', 'Challan Type:', selectedChallan.sc_challan_type || 'N/A' , 3)}
                                        ${generateInfoBox('fa-id-card', 'Student Status:', selectedChallan.sai_student_status || 'N/A' , 3)}
                                        ${generateInfoBox('fa fa-money', 'Scholarship Amount:', selectedChallan.sc_scholarship_amount || 'N/A' , 3)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card Header -->
                        <div class="card-header custom-card-header d-flex align-items-center p-2">
                            <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                            <span class="h6 mb-0">Adjusment Form</span>
                        </div>
                        <!-- Card Body -->
                    <form id="submitEnrollmentFeeAdjustment" style="margin-top:10px;">
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

                        <input type="text" hidden name="app_id" value="${selectedChallan.aai_id}" class="form-control">
                        <input type="text" hidden name="challan_no" value="${selectedChallan.id}" class="form-control">
                        <input type="text" hidden name="enrollment_no" value="${selectedChallan.sai_enrollment_no}" class="form-control">
                        <input type="text" hidden name="student_status" value="${selectedChallan.sai_student_status}" class="form-control">
                        <input type="text" hidden name="with_aai_id" value="${selectedChallan.with_aai_id}" class="form-control">
                        <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                            <button type="button" class="btn btn-primary" id="saveAdjustment" onclick="submitEnrollmentFormBtn()">Save</button>
                        </div>
                    </form>
                    <div id="error_message_enrollment" class="alert alert-danger" style="display: none;"></div>
                    <div id="success_message_enrollment" class="alert alert-success" style="display: none;"></div>
                    `;
            ChallanDataDiv.innerHTML = challanHtml;


}
function addNewFeeAdjustment(adjustmentData, newAdjustmentData){
    // console.log(adjustmentData);
    // console.log(newAdjustmentData);
    $('#dataDiv').html('');
     let optionHtml = '';
    adjustmentData.forEach(option => {
        optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
    });
    // console.log(globalChallanData);
    newAjustmentFormHtml=`  <!-- Card Header -->
                                    <div class="card-header custom-card-header d-flex align-items-center p-2">
                                        <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                                        <span class="h6 mb-0">Adjusment Form</span>
                                    </div>
                                    <!-- Card Body -->
                                    <form id="submitEnrollmentFeeAdjustment" style="margin-top:10px;">
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

                                    <input type="text" hidden name="submitType" value="add_new" class="form-control">
                                    <input type="text" hidden name="app_id" value="${newAdjustmentData[0].aai_id}" class="form-control">
                                    <input type="text" hidden name="enrollment_no" value="${newAdjustmentData[0].sai_enrollment_no}" class="form-control">
                                    <input type="text" hidden name="student_status" value="${newAdjustmentData[0].sai_student_status}" class="form-control">
                                    <input type="text" hidden name="with_aai_id" value="${newAdjustmentData[0].with_aai_id}" class="form-control">

                                    <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                                        <button type="button" class="btn btn-primary" id="saveAdjustment" onclick="submitEnrollmentFormBtn()">Save</button>
                                    </div>
                                </form>
                                <div id="error_message_enrollment" class="alert alert-danger" style="display: none;"></div>
                                <div id="success_message_enrollment" class="alert alert-success" style="display: none;"></div>
                                `;
    $('#dataDiv').html(newAjustmentFormHtml);
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
    if (searchBy == 'enrollment' && (enrollmentNo === '' || /^[A-Za-z]+$/.test(enrollmentNo))) {
        $('.searchEnrollment-disclaimer').text('Enter valid Apply Id / Enrollment No.');
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
    $('.filteredAUD_adjustmentData').addClass('d-none');
    $('.filteredAdjustmentData').removeClass('d-none');
    $('#data-tableAdjustment').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        lengthMenu: [ [10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000] ],
        order: [1, 'desc'],
        ajax:{
            dataType: 'json',
            method: 'post',
            url:  "/api/feeadjustment/getFeeAdjustmentList"
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
                    let badgeClass = '';

                    switch (data) {
                        case 'Paid':
                            badgeClass = 'badge-success';
                            break;
                        case 'Unpaid':
                            badgeClass = 'badge-danger';
                            break;
                        case 'Partial Paid':
                            badgeClass = 'badge-warning';
                            break;
                        case 'Fee Refund':
                            badgeClass = 'badge-info';
                            break;
                        case 'Readmission':
                            badgeClass = 'badge-primary';
                            break;
                        default:
                            badgeClass = 'badge-secondary';
                    }

                    return `<span class="badge ${badgeClass}">${data}</span>`;
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
                   // console.log(row);
                    // var button = '<div>'+
                    //                 '<button class="btn btn-primary btn-sm edit-btn" id="'+row["ffa_id"]+'"  onclick="showModal('+row["ffa_id"]+')">Action</button>'+
                    //                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>'+
                    //                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffa_id"]+'])"><i class="fa fa-trash"></i> Delete</button>'+
                    //             '</div>';
                    var button = '<div>'+
                                    '<button class="btn btn-primary btn-sm edit-btn" id="'+row["ffa_id"]+'"  onclick="showModal('+row["ffa_id"]+')">Action</button>'+
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
        dom: 'Blfrtip', // Adds the buttons to the DataTable
        buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Fee Adjustment',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7] // Include all columns except the delete button (index 8)
                        }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Fee Adjustment',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7] // Include all columns except the delete button (index 8)
                        }

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },


                {
                    extend: 'print',
                    title: 'Fee Adjustment',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7] // Include all columns except the delete button (index 8)
                        }
                }
            ],
        autoWidth: false, // Ensures DataTable does not set its own width
    });

}
function loadAudFeeAdjustments(challanStatus){
    $('.filteredAdjustmentData').addClass('d-none');
    $('.filteredAUD_adjustmentData').removeClass('d-none');
    $('#data-tableAudAdjustment').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        lengthMenu: [ [10, 100, 500, 1000, 5000], [10, 100, 500, 1000, 5000] ],
        order: [1, 'desc'],
        ajax:{
            dataType: 'json',
            method: 'post',
            url:  "/api/feeadjustment/getAudFeeAdjustmentList",
            data: function(d) {
                d.challanStatus = challanStatus;
            }
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
            {data: "old_surplus_amnt"},
            {data: "ffa_used_amount"},
            {data: "ffa_surplus_amount"},
            {
                data: "changed_on",
                render: function(data, type, row) {
                    if (!data) return ''; // Handle empty data

                    let dateObj = new Date(data); // Convert string to Date object
                    if (isNaN(dateObj)) return ''; // Handle invalid dates

                    let day = dateObj.getDate();
                    let month = dateObj.toLocaleString('en-US', { month: 'short' }); // Get short month name (e.g., "Dec")
                    let year = dateObj.getFullYear();

                    return `${day}-${month}-${year}`; // Format: 17-Dec-2024
                }
            },
            {
                data: "ffa_amount_status",
                render: function(data, type, row) {
                    if (data === 'Paid') {
                        return `<span class="badge badge-success">${data}</span>`;
                    }else if(data === 'pending'){
                        return `<span class="badge badge-warning">Unpaid</span>`;
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
            // {
            //     render: function (data, type, row) {
            //        // console.log(row);
            //         var button = '<div>'+
            //                         '<button class="btn btn-primary btn-sm edit-btn" id="'+row["ffa_id"]+'"  onclick="showModal('+row["ffa_id"]+')">Action</button>'+
            //                             '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>'+
            //                         '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffa_id"]+'])"><i class="fa fa-trash"></i> Delete</button>'+
            //                     '</div>';

            //         return button;

            //     },
            //     // className: 'dt-body-center',
            //     // className: 'dt-head-center',
            //     targets: 8,
            // }
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

            {
                "width": "100px", "targets": 8
            }
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
        dom: 'Blfrtip', // Adds the buttons to the DataTable
        buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Fee Adjustment',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7,8,9,10] // Include all columns except the delete button (index 8)
                        }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Fee Adjustment',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7,8,9,10] // Include all columns except the delete button (index 8)
                        }

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },


                {
                    extend: 'print',
                    title: 'Fee Adjustment',
                    exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7,8,9,10] // Include all columns except the delete button (index 8)
                        }
                }
            ],
        autoWidth: false, // Ensures DataTable does not set its own width
    });
}
function handleFilter(status, text) {
    $('.filterbutton').html('<i class="fa fa-filter"></i> Filter <span class="filter-bubble">'+text+'</span>')
    if(status=='ALL'){
        loadFeeAdjustments();
    }else{
        // Check if an element with class 'container-fluid' that conatin specific student details exists then remove it
        const studentDetail = document.querySelector('.studentDetail');

        if (studentDetail) {
            studentDetail.remove();
        }
        loadAudFeeAdjustments(status);
    }

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
function generateInfoBox(iconClass, label, value, colwidth) {
    return `
    <div class="col-md-${colwidth} mb-3">
        <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
            <i class="fa ${iconClass} mr-3 text-muted" style="font-size: 1.2rem;"></i>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem;">${label}</span>
                <strong style="font-size: 0.9rem;">${value}</strong>
            </div>
        </div>
    </div>`;
}

</script>
@stop
