@extends('layouts.app')

@section('title')
  BNU CMS
@stop

@section('content')

<div class="container-fluid">
    <div class="card p-4 pt-4">
        <form id="searchStudentForm">
            @csrf
            <div class="row">
                <div class="col-6 col-sm-12 col-md-6">
                    <div class="input-group">
                        <input placeholder="Enter student enrollment number" class="form-control" type="text" name="studentEnrollment" id="categoryName">
                        <div class="input-group-append">
                            <button onclick='searchStudent()' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>

            </div>
            
        </form>


        <div id="dataDiv"   style="margin-top:20px;">
        </div>









<hr>

<div id="showDataInTable" class="table-responsive"></div>

    </div>
</div>
@stop

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

@section('javascript')
<script>



function studentScholarshipRec(data) {
    let showDataInTable = document.getElementById('showDataInTable');
    showDataInTable.innerHTML = ''; // Clear previous content

    if (data && data.length > 0) {
        let createHtml = `
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Scholarship</th>
                        <th scope="col">Type</th>
                        <th scope="col">Given Percentage</th>
                        <th scope="col">Given Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                ${data.map(element => `
                        <tr>
                            <td>${element.scholarship_name}</td>
                            <td>${element.fsa_search_type}</td>
                            <td>${element.fsa_percentage}%</td>
                            <td>${element.fsa_given_scholarship_amount} -/ PKR</td>
                            <td>
                                ${element.fsa_ro_status === 'Pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                                ${element.fsa_ro_status === 'Approved' ? '<span class="badge badge-success">Approved</span>' : ''}
                                ${element.fsa_ro_status === 'Disapproved' ? '<span class="badge badge-danger">Disapproved</span>' : ''}
                            </td>
                            <td>
                                <button class="btn btn-primary edit-scholarship" data-scholarship='${JSON.stringify(element)}'>Edit</button>
                                <button class="btn btn-danger" onclick="deleteRec(${element.fsa_id})">Delete</button>
                            </td>
                        </tr>
                    `).join('')}

                </tbody>
            </table>
        `;

        showDataInTable.innerHTML = createHtml;

        // Add event listeners to dynamically created buttons
        let editButtons = document.querySelectorAll('.edit-scholarship');
        editButtons.forEach(button => {
            button.addEventListener('click', editScholarshipRec);
        });
    } else {
        showDataInTable.innerHTML = '<p>No data available</p>';
    }
}


function submitFormBtn(event) {
    event.preventDefault();

        // Serialize form data
        var formData = $('#submitScholarshipForm').serialize();
        // Make an AJAX request
        $.ajax({
            type: 'POST',
            url: '/finance/submit-student-scholarship',
            data: formData,
            success: function(response) {
              // document.getElementById("feePolicyFrom").reset();
                    $('#successMsg').html('<i class="fa fa-check-circle mr-1"></i>' + response.message);
                     document.getElementById("submitScholarshipForm").reset();
                     setTimeout(function() {
                         $('#successMsg').html('');
                         searchStudent();
                }, 3000);

            },
            error: function(xhr, status, error) {
                // There was an error
                console.error('Error:', error);
            }
        });
    }




function handleSearchType() {
    var selectedType = $('#searchType').val();

    // Remove any existing dateInputs and scholarshipInputs elements
    $('#dateInputs').remove();
    $('#scholarshipInputs').remove();
    $('#scholarshipCalculations').remove();

    if (selectedType === 'Annually') {
        // Dynamically create HTML elements for date inputs
        var dateInputsHTML = `
    

            <div id="dateInputs" class="form-group col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <label for="fromDate">From:</label>
                        <input type="date" id="fromDate" name="fromDate" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="toDate">To:</label>
                        <input type="date" id="toDate" name="toDate" class="form-control">
                    </div>
                </div>
            </div>
        `;
        // Append dateInputsHTML to the form
        $('#searchType').parent().after(dateInputsHTML);
    } else if (selectedType === 'Semester') {
        // Create HTML for session select dropdown
        var sessionSelectHTML = `
            <div id="dateInputs" class="form-group col-md-5">
                <div class="form-group col-sm-12">
                    <label for="session_sel">Session</label>
                    <select id="session_sel"  name="session_sel" class="form-control" onchange="on_session_change(this);">
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span id="session_sel-error" class="error text-danger"></span>
                </div>
            </div>
        `;
        // Append sessionSelectHTML to the form
        $('#searchType').parent().after(sessionSelectHTML);
        const currentSessionId = $('#cur_session_id').val();
        // Select the option based on the current session ID
        // $('#session_sel').val(currentSessionId).prop('disabled', true);
        $('#session_sel').val(currentSessionId);
    // }
    }

    // Create HTML for scholarship inputs for both Annually and Semester
    let scholarshipInputsHTML = `
        <div id="scholarshipInputs" class="form-group col-md-6">
            <div class="row">
                <div class="col-md-4">
                    <label for="scholarshipPercentage">Scholarship %:</label>
                    <input type="number" id="scholarshipPercentage" name="scholarshipPercentage" class="form-control">

                    <small  style="color: tomato;">*percentage of scholarship to provide</small>

                </div>
                <div class="col-md-1 d-flex justify-content-center align-items-center mb-2">
                            <span   class="bg-dark text-white p-1">OR </span>
                        </div>
                <div class="col-md-4">
                    <label for="scholarshipAmount">Scholarship Amount:</label>
                    <input type="number" id="scholarshipAmount" name="scholarshipAmount" class="form-control">
                    <small style="color: tomato;">*Total amount of scholarship to provide</small>

                </div>
                <div class="col-md-2 d-flex justify-content-center align-items-center mb-1 pl-2 ml-2">
                            <button onclick="calculateScholarshipAmount(event)"  class="btn btn-primary mb-2 "><i class="fa fa-calculator"></i> Calculate </button>
                        </div>

            </div>
            <di
        </div>
    `;
    // Append scholarshipInputsHTML to the form
    $('#dateInputs').parent().append(scholarshipInputsHTML);

}


document.getElementById('scholarshipPercentage').addEventListener('input', function() {
            let inputElement = this;
            let inputValue = parseFloat(inputElement.value);
            if (!isNaN(inputValue)) {
                console.log("Floating-point value: ", inputValue);
                // You can perform additional operations with inputValue here
            } else {
                console.log("Invalid input. Please enter a number.");
            }
        });

function displayValidationErrors(xhr){
    if (xhr.status == 422) {
        var errors = xhr.responseJSON.errors;
        $.each(errors, function(key, value) {
            $("#" + key + "-error").html('<i class="fa fa-info-circle"></i> ' + value[0]);
            $("#" + key + "-error").addClass('text-danger');
            setTimeout(function(){
                $("#" + key + "-error").text('');
            }, 5000);
        });
    }
}


function searchStudent() {
    let form = document.getElementById('searchStudentForm');
    let data = new FormData(form);
    $('#dateInputs').remove();
    $('#scholarshipInputs').remove();


    fetch(`/finance/get-student-rec`, {
        method: 'POST', // Using POST method
        body: data
    })
    .then(response => response.json())
    .then(({ success, getStudentRec, scholarships,feeChallanAmount,usernameError,studentScholarship }) => { // Correct destructuring syntax
        if (success) {
            studentScholarshipRec(studentScholarship);
            makeDataHtml(getStudentRec, scholarships, feeChallanAmount);
        }
        else{
        const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
        const message = usernameError;
        const alertHTML = createAlert(notificationType, message);
        dataDiv.innerHTML = alertHTML;
            return false;
        }
    })
}




function makeDataHtml(data,scholarships,feeChallanAmount) {

    let dataDiv = document.getElementById('dataDiv');
    dataDiv.innerHTML = ''; // Clear previous content
    let sr = 1;
    console.log(feeChallanAmount);

    if (feeChallanAmount==null) {
        const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
        const message = 'Error in fetching Challan Details';
        const alertHTML = createAlert(notificationType, message);
        dataDiv.innerHTML = alertHTML;
    return false;
}

    //  <p class="lead"><strong>Department:</strong> ${data.emp_dept_name}</p>
    if (data) {
        let lettersHtml = `
    <div class="container-fluid">
  <div class='row'>
  <input type="hidden" name="cur_session_id" id="cur_session_id" value=${feeChallanAmount.sc_session_id}
  </div>
        <div class="row align-items-center">
        <!-- Enrollment Number -->
        <div class="col-md-4 col-sm-3 col-12 ">
            <p class="lead text-muted mb-0">
                <strong class="d-inline">Enrollment Number:</strong> <span class="text-uppercase">${data.username}</span>
            </p>
        </div>

        <!-- Email -->
        <div class="col-md-4 col-sm-3 col-12 ">
            <p class="lead text-muted mb-0">
                <strong class="d-inline">Email:</strong> <span class="d-inline">${data.email}</span>
            </p>
        </div>

        <!-- SPI Name and Student Image -->
        <div class="col-md-4 col-sm-6 col-12  ">
            <h4 class="text-uppercase font-weight-bold text-primary mt-4">${data.spi_name}</h4>
            <img src="https://cms.bnu.edu.pk/students_pics/${data.spi_enrollment_no.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:100px; height:100px;">
        </div>

        <div class="col-md-2 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0">
                        <strong class="d-inline">Challan No:</strong> <span class="text-uppercase">${feeChallanAmount.id}</span>
                    </p>
                </div>
                <div class="col-md-2 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0">
                        <strong class="d-inline">Due Amount:</strong> <span class="text-uppercase">${feeChallanAmount.sc_tuition_fee_amnt}</span>
                        <input type="hidden" name="actual_amount" id="actual_amount" value=${feeChallanAmount.sc_tuition_fee_amnt}
                    </p>
                </div>
                <div class="col-md-2 col-sm-2 col-12  mb-3">
                    <p class="lead text-muted mb-0">
                    <strong class="d-inline">Due Date:</strong> <span class="text-uppercase">${formatDueDate(feeChallanAmount.sc_due_date)}</span>

                    </p>
                </div>


</div>


<div class="col-md-12 text-left mt-3">
    <form id="submitScholarshipForm">
        @csrf
        <div class="row">
            <input type="hidden"  name="enrollment_no" id="enrollment_no" value="${data.username}" >
                <input type="hidden"  name="verifyEdit" id="verifyEdit" value="" >
                <input type="hidden"  name="edit_id" id="edit_id" value="" >
            <!-- First Dropdown -->

            <div class="form-group col-md-4">
                <label for="scholarshipOption">Select Scholarship:</label>
                <select name="scholarshipOption" id="scholarshipOption" onchange="handleScholarshipType(this.value,this.options[this.selectedIndex].dataset.amount)" class="form-control">
                            <option value="" selected disabled>--- Select Category ---</option>
                            ${scholarships.map((category, index) => {
                                return `<option data-amount="${category.fsb_amount}" value="${category.fsb_schlr_ship_id}">${category.scholarship_name}</option>`;
                            }).join('')}
                        </select>

                <h6 data-error-for="scholarshipOption" class="validation-error pt-4"></h6>
            </div>

            <!-- Fine Amount Input -->

            <div class="form-group col-md-4">
                <label for="availableAmount">Available Amount:</label>
                <input type="number" readonly name="availableAmount" id="availableAmount" class="form-control">
                <h6 data-error-for="availableAmount" class="validation-error pt-4"></h6>
            </div>



        <div class="form-group col-md-4">
    <label for="scholarshipType">Scholarship Type:</label>
    <select   id="searchType" name="searchType" class="form-control" onchange=handleSearchType()>
        <option value="" selected disabled>Select Type</option>
        <option value="Annually">Annually</option>
        <option value="Semester">Semester wise</option>
    </select>
    <h6 data-error-for="scholarshipType" class="validation-error pt-4"></h6>
</div>



                </div>
    </form>
</div>




`;



dataDiv.innerHTML = lettersHtml;
    } else {
        // Optionally display a message when there is no data
        dataDiv.innerHTML = '<p>No data available.</p>';
    }
}

function handleScholarshipType(scholarshipId,fsbAmount) {
    console.log("Selected scholarship ID: ", scholarshipId);
    console.log("Selected fsb_amount: ", fsbAmount);
        $('#dateInputs').remove();
        $('#scholarshipInputs').remove();
        $('#searchType').val('');


    document.getElementById("availableAmount").value = fsbAmount;


    // Your logic here
}
function formatDueDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function calculateScholarshipAmount(event) {
    event.preventDefault(); // Prevent the default form submission

    $('#scholarshipCalculations').remove();
    $('#errorMessage').remove(); // Remove previous error message if any

    const availableAmount = parseFloat(document.getElementById("availableAmount").value);
    const scholarshipPercentage = parseFloat(document.getElementById("scholarshipPercentage").value);
    const scholarshipAmount = parseFloat(document.getElementById("scholarshipAmount").value);
    const actualAmount = parseFloat(document.getElementById("actual_amount").value);
    let givenScholarship;
    let remainingAmount;


    // if (scholarshipPercentage < 0 || scholarshipAmount < 0 || isNaN(scholarshipPercentage) || isNaN(scholarshipAmount)) {
    //     showErrorMessage("Scholarship values cannot be negative or empty.");
    //     return;
    // }

    
    // Check for invalid inputs
    if ((scholarshipPercentage && scholarshipAmount) || (!scholarshipPercentage && !scholarshipAmount)) {
        showErrorMessage('Please provide either a scholarship percentage or a scholarship amount, not both.');
        return;
    }

    // Check for non-positive input values
    if (availableAmount <= 0 || actualAmount <= 0) {
        showErrorMessage('Both the available amount and the actual amount must be greater than zero.');
        return;
    }

    // Calculate scholarship and remaining amount
    if (scholarshipPercentage) {
        givenScholarship = (actualAmount * scholarshipPercentage) / 100;
        remainingAmount = availableAmount - givenScholarship;

        // Check if the calculated scholarship amount exceeds the available amount
        if (givenScholarship > availableAmount) {
            showErrorMessage(`The calculated scholarship amount of PKR/- ${givenScholarship.toFixed(2)} exceeds the available amount of PKR/- ${availableAmount.toFixed(2)}.`);
            return;
        }
    } else if (scholarshipAmount) {
        givenScholarship = scholarshipAmount;
        remainingAmount = availableAmount - scholarshipAmount;

        // Check if the scholarship amount exceeds the available amount
        if (scholarshipAmount > availableAmount) {
            showErrorMessage(`The scholarship amount of PKR/- ${scholarshipAmount.toFixed(2)} exceeds the available amount of PKR/- ${availableAmount.toFixed(2)}.`);
            return;
        }
    } else {
        // Default case if neither percentage nor fixed amount is provided
        givenScholarship = 0;
        remainingAmount = availableAmount;
    }

    // Check if the remaining amount is negative
    if (remainingAmount < 0) {
        showErrorMessage('The remaining amount cannot be negative.');
        return;
    }

    // Determine the scholarship percentage if the amount was given
    let calculatedPercentage = scholarshipPercentage;
    if (!scholarshipPercentage && scholarshipAmount) {
        calculatedPercentage = (scholarshipAmount / actualAmount) * 100;
    }

    // Display scholarship calculations
    let scholarshipCalculationsHTML = `
        <div id="scholarshipCalculations" class="form-group col-md-12">
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <label for="givenScholarshipPercentage">Given Scholarship Percentage:</label>
                    <input hidden name="givenScholarshipPercentage" id="givenScholarshipPercentage" class="form-control" value="${calculatedPercentage.toFixed(5)}" readonly>
                    <input name="givenScholarshipPercentage1" id="givenScholarshipPercentage1" class="form-control" value="${calculatedPercentage.toFixed(5)}%" readonly>
                </div>
                <div class="col-md-4">
                    <label for="givenScholarshipAmount">Amount of Given Scholarship:</label>
                    <input name="givenScholarshipAmount" id="givenScholarshipAmount" value="${givenScholarship.toFixed(5)}" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <label for="remainingAmount">Remaining Amount:</label>
                    <input name="remainingAmount" id="remainingAmount" value="${remainingAmount.toFixed(2)}" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <button onclick="submitFormBtn(event)" class="btn btn-primary" id="submitBtn">Submit</button>
                </div>
            </div>
            <h2 id="successMsg" class="text-success"></h2>
        </div>
    `;

    // Append scholarshipCalculationsHTML to the form
    $('#dateInputs').parent().append(scholarshipCalculationsHTML);
}

function showErrorMessage(message) {
    const errorMessage = `
        <div id="errorMessage" class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fa fa-exclamation-triangle fa-2x mr-2"></i>
            <div>${message}</div>
        </div>
    `;
    $('#dateInputs').parent().append(errorMessage);
    setTimeout(function() {
        $('#errorMessage').remove();
    }, 9000); // Remove the error message after 5 seconds
}



function createAlert(notificationType, message) {
    // Define the alert color and icon based on the notification type
    let alertColor = '';
    let icon = '';
    switch (notificationType) {
        case 'error':
            alertColor = 'danger';
            icon = '<i class="fa fa-exclamation-circle"></i>';
            break;
        case 'success':
            alertColor = 'success';
            icon = '<i class="fa fa-check-circle"></i>';
            break;
        case 'info':
            alertColor = 'info';
            icon = '<i class="fa fa-info-circle"></i>';
            break;
        case 'warning':
            alertColor = 'warning';
            icon = '<i class="fa fa-exclamation-triangle"></i>';
            break;
        default:
            alertColor = 'secondary';
            icon = '<i class="fa fa-bell"></i>';
            break;
    }

    // Generate the alert HTML
    let alertHTML = `
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-sm-4 col-12">
                    <div class="alert alert-${alertColor} alert-dismissible fade show" role="alert">
                        ${icon} <strong>${notificationType.charAt(0).toUpperCase() + notificationType.slice(1)}:</strong> ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
            setTimeout(function() {
            dataDiv.innerHTML = '';
        }, 5000);
    return alertHTML;
}

// Function to handle editing scholarship data
function editScholarshipRec(event) {
    const buttonElement = event.target;
    const scholarshipDataString = buttonElement.dataset.scholarship;
    try {
        const scholarshipData = JSON.parse(scholarshipDataString);
   console.log(scholarshipData);

        $('#scholarshipOption').val(scholarshipData.fsa_schlr_ship_id);
        $('#verifyEdit').val('1');
        $('#edit_id').val(scholarshipData.fsa_id);
        $('#availableAmount').val(scholarshipData.fsa_remainingamount);
        // $('#availableAmount').val(scholarshipData.);
        $('#searchType').val(scholarshipData.fsa_search_type);
        handleSearchType()

        $('#scholarshipPercentage').val(scholarshipData.fsa_percetage);
        $('#scholarshipAmount').val(scholarshipData.fsa_amount);

        if (scholarshipData.fsa_search_type=='Semester') {

        // Ensure scholarshipData.fsa_sess_id matches one of the options' values
        $('#session_sel').val(scholarshipData.fsa_sess_id);
        calculateScholarshipAmount(event);

        }
        if (scholarshipData.fsa_search_type=='Annually') {
            let fromDate = scholarshipData.fsa_from_date.substring(0, 10);
            let toDate = scholarshipData.fsa_to_date.substring(0, 10);
            $('#fromDate').val(fromDate);
            $('#toDate').val(toDate);
            calculateScholarshipAmount(event);
        }






        // $('#fromDate').val();
        // $('#toDate').val();
        // $('#scholarshipPercentage').val();
        // $('#scholarshipAmount').val();




    } catch (error) {
        console.error("Parsing Error:", error);
    }
}


function deleteRec(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            url: '/finance/delete-student-scholarship-rec',
            type: 'GET', // Change to 'DELETE' if you are using RESTful routes
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    searchStudent();
                    alert('Record deleted successfully.');
                    // Optionally remove the record from the DOM
                    $('#record-' + id).remove();
                } else {
                    alert('Failed to delete the record.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }
}


</script>
@stop
