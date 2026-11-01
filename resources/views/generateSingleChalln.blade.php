@extends('layouts.app')

@section('title')
  BNU CMS
@stop

@section('content')


<style>

select:required {
    border-color: #dc3545; /* Red border color for required select */
}

select:required:focus {
    border-color: #dc3545; /* Red border color for required select on focus */
}



@keyframes blink-border {
    0% {
        border-color: #007bff;
        border-width: 4px;
    }
    50% {
        border-color: transparent;
        border-width: 4px;
    }
    100% {
        border-color: #007bff;
        border-width: 4px;
    }
}

.focused-border {
    border: 2px solid #007bff; /* Modern blue border */
    border-radius: 6px; /* Smooth rounded corners */
    outline: none; /* Remove default focus outline */
    box-shadow: 0 4px 6px rgba(0, 123, 255, 0.2); /* Subtle shadow effect */
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Smooth transitions */
}

.focused-border:focus {
    border-color: #0056b3; /* Slightly darker blue on focus */
    box-shadow: 0 6px 8px rgba(0, 86, 179, 0.3); /* Enhanced shadow on focus */
}


/* Subtle pulsing animation */
@keyframes subtlePulse {
    0% {
        box-shadow: 0 0 5px rgba(128, 192, 255, 0.4);
    }
    50% {
        box-shadow: 0 0 10px rgba(128, 192, 255, 0.8);
    }
    100% {
        box-shadow: 0 0 5px rgba(128, 192, 255, 0.4);
    }
}



input[type="checkbox"] {
    margin-left: 10px;
    vertical-align: middle;
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
.hidden {
    display: none;
}
#searchButton {
        display: flex;
        align-items: center;
        padding-left: 12px;
        padding-right: 12px;
        border-left: none;
    }

    #searchButton i, #searchButton span {
        transition: all 0.3s ease;
    }

    #searchButton:hover i {
        transform: scale(1.5); /* Icon ka size badh jaayega */
        margin-right: 5px;
    }
</style>


<div class="container-fluid">
    <div class="card p-4 pt-4">


        <form id="singleChallanForm">
            <div class="row"><h3>Single Challan Generation</h3></div>
            @csrf
            <div class="row">
                <input type="hidden"  name="verifyEdit" id="verifyEdit" value="" >
                <input type="hidden"  name="edit_id" id="edit_id" value="" >

                <div class="form-group col-md-4">
                    <label for="studentEnrollment" class="form-label" style="font-weight: bold; font-size: 1rem; color: #333;">
                        Enrollment No# or Applicant ID
                        <span style="color: red;">*</span>
                    </label>
                    <div class="input-group mb-3" style="max-width: 400px; margin: auto;">
                        <!-- Input Field -->
                        <input
                        type="text"
                        class="form-control"
                        id="studentEnrollment"
                        name="studentEnrollment"
                        placeholder="Enrollment No / Applicant ID "
                        onkeydown="handleEnterKey(event)"
                        aria-label="Student Enrollment Number"
                        style="border-right: none; background-color: #fcfcfc; border: 2px solid #ced4da; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 10px; transition: box-shadow 0.2s ease, border-color 0.2s ease;"
                        onmouseover="this.style.boxShadow='0 3px 6px rgba(0, 0, 0, 0.15)'; this.style.borderColor='#007bff';"
                        onmouseout="this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#ced4da';"

                        style="border-right: none; background: linear-gradient(135deg, #fcfcfc, #e0f7fa); border: 2px solid #ced4da; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 12px; transition: box-shadow 0.2s ease, border-color 0.2s ease;"
                        onmouseover="this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)'; this.style.borderColor='#007bff';"
                        onmouseout="this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#ced4da';"
                        >


                        <!-- Search Button with Icon -->
                        <button
                        class="btn btn-primary ml-1"
                        type="button"
                        id="searchButton"
                        onclick="searchStudent()">
                        <i class="fa fa-search"></i>
                        <span>Search</span>
                    </button>


                    </div>



                    <span id="studentEnrollment-error" class="error text-danger"></span>
                </div>

                </div>


                <div class="row">


                <div class="container">

                <div id='profileDiv'>
                            </div>
                </div>

            </div>
           <hr style="width: 100%; height: 10px; height: 6px;">

            <input name='st_school_id' id='st_school_id' hidden>
            <input name='st_dept_id' id='st_dept_id' hidden>
            <input name='st_prog_id' id='st_prog_id' hidden>
            <input name='st_aai_id' id='st_aai_id' hidden>



    <div class="row"   id='installmentFocus' >
                <div class="form-group col-md-4">
                    <label for="sessionId">Session   <span style="color: red;">*</span></label>
                    <select id="sessionId" name="sessionId" class="form-control" >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span id="sessionId-error" class="error text-danger"></span>
                </div>

                {{-- <div class="form-group col-md-4">
                    <label for="installmentSelect">No. of Installments:</label>
                    <select class="form-control" name="installmentSelect" id="installmentSelect">
                        <option disabled selected>---Select Option---</option>
                        <option value="2">2 Installments</option>
                        <option value="3">3 Installments</option>
                        <option value="4">4 Installments</option>
                        <option value="5">5 Installments</option>
                    </select>
                    <span id="installmentSelect-error" class="error text-danger mt-2 pl-2"></span>
                </div> --}}

                <div class="form-group col-md-4">
                     <label for="studentType">Student Type  <span style="color: red;">*</span></label>
                    <select name="student_type" id="student_type" class="form-control">
                        <option value="">Select type</option>
                        <option value="Existing">Existing</option>
                        {{-- <option value="New without Registration FALL">New without Registration FALL</option>
                        <option value="New without Registration SPRING">New without Registration SPRING</option>
                        <option value="New without Registration SUMMER">New without Registration SUMMER</option> --}}
                        <option value="New Admission FALL">New Admission FALL</option>
                        <option value="New Admission SPRING">New Admission SPRING</option>
                        <option value="New Admission SUMMER">New Admission SUMMER</option>
                    </select>
                    <span id="student_type-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                    <label for="studentStatus">Student Status  <span style="color: red;">*</span></label>
                    <select name="student_status" id="student_status" class="form-control">
                        <option value="">Select status</option>
                        <option value="Active">Active</option>
                        <option value="Expected-Graduate">Expected Graduate</option>
                    </select>
                    <span id="student_status-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-sm-4">
                    <label for="feeType">Fee Type  <span style="color: red;">*</span></label>
                    <select name="fee_type" id="feeType" class="form-control" onchange="getFeeStructure(this)">
                        <option value="">Select fee type</option>
                        <option value="Full_semester">Full Semester wise</option>
                        <option value="Credit_hours">Credit Hour wise</option>
                        <option value="Hostel_Full_semster">Hostel Full Semester</option>
                        <option value="Hostel_day_wise">Hostel Days Wise</option>
                        <option value="Hostel_other_utilities">Hostel Other Utilities</option>
                        <option value="Fee_Refund">Fee Refund</option>
                        <option value="Short/Summer_Course">Short / Summer Course</option>
                        <option value="Processing_Fee">Processing Fee</option>

                    </select>
                    <span id="fee_type-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-4" id='invoiceAmountOpt' >
                    <label for="studentInvoiceamount">Invoice Amount</label>
                    <div class="input-group">
                        <input   class="form-control" type="text" name="studentInvoiceamount" id="studentInvoiceamount" oninput="validateInput(this)">
                    </div>

                </div>
                <div class="form-group col-md-4" id='scholarshipsAmountOpt' style="display: none;">
                    <label for="scholarshipsAmount">Scholarships Amount</label>
                    <div class="input-group">
                        <input readonly class="form-control" type="text" name="scholarshipsAmount" id="scholarshipsAmount" oninput="validateInput(this)">
                    </div>
                </div>







            <div id="full_semester_dev" class="row" >
                <div class="form-group col-md-4">
                    <label for="tuitionFee">Tuition Fee</label>
                    <input hidden type="text" name="refundEdit" id="refundEdit">
                    <input hidden type="text" name="editChallan" id="editChallan">
                    <div class="input-group">
                        <input placeholder="Enter tuition fee"   readonly class="form-control" type="text" name="tuitionFee" id="tuitionFee" oninput="validateInput(this)">
                    </div>
                    <span id="tuitionFee-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4 admissionFeeDiv">
                    <label for="admissionFee">Admission Fee</label>
                    <div class="input-group">
                        <input placeholder="Enter admission fee" readonly   class="form-control" type="text" name="admissionFee" id="admissionFee" oninput="validateInput(this)">
                    </div>
                    <span id="admissionFee-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-4 securityAmtDiv">
                    <label for="securityFee">Security Fee</label>
                    <div class="input-group">
                        <input placeholder="Enter security fee"   readonly  class="form-control" type="text" name="securityFee" id="securityFee" oninput="validateInput(this)">
                    </div>
                    <span id="securityFee-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                    <label for="medicalFee">Medical Fee</label>
                    <div class="input-group">
                        <input placeholder="Enter medical fee"  readonly  class="form-control" type="text" name="medicalFee" id="medicalFee" oninput="validateInput(this)">
                    </div>
                    <span id="medicalFee-error" class="error text-danger"></span>
                </div>


                <div class="form-group col-md-4">
                    <label for="activitycharges">Activity Charges</label>
                    <input  placeholder="Enter activity charges"   readonly  class="form-control" type="text" name="activity_charges_amount" id="activitychargesAmt" oninput="validateInput(this)">
                    <span id="activity_charges_amount-error" class="error text-danger"></span>
                </div>
            </div>









            <div class="form-group     col-md-2" id="hostel-sess-typeDiv">
                <label for="sess_type">Session Type</label>
                <div class="input-group">
                    <select name="sess_type" id="sess_type" class="form-control" onchange="getFeeStructure(this)">
                            {{-- <option value="">Select Type</option> --}}
                            <option selected value="Regular">Regular</option>
                            <option value="Non-Regular">Non-Regular</option>
                        </select>

                </div>
                <span id="sess_type-error" class="error text-danger"></span>
            </div>


            <div class="form-group     col-md-2" id="hostelFeeDiv">
                <label for="hostelFee">Hostel Fee</label>
                <div class="input-group">
                    <input placeholder="Enter hostel fee" readonly class="form-control" type="text" name="hostelFee" id="hostelFee" oninput="validateInput(this)">
                    {{-- <div class="input-group-append">
                        <input type="checkbox" name="hostelFeeCheckbox" id="hostelFeeCheckbox">
                    </div> --}}
                </div>
                <span id="hostelFee-error" class="error text-danger"></span>
            </div>
            <div class="form-group     col-md-2" id="hostel_security_feeDiv">
                <label for="hostel_security_fee">Hostel Security Fee</label>
                <div class="input-group">
                    <input placeholder="Enter hostel fee"  class="form-control" type="text" name="hostel_security_fee" id="hostel_security_fee" oninput="validateInput(this)">
                    {{-- <div class="input-group-append">
                        <input type="checkbox" name="hostel_security_feeCheckbox" id="hostel_security_feeCheckbox">
                    </div> --}}
                </div>
                <span id="hostel_security_fee-error" class="error text-danger"></span>
            </div>

            <div class="form-group  col-md-2" id="hostel_per_dayDiv">
                <label for="hostel_per_day">Amount per day</label>
                <div class="input-group">
                    <input placeholder="Enter per day charges" readonly class="form-control" type="text" name="hostel_per_day" id="hostel_per_day" oninput="validateInput(this)">
                    {{-- <div class="input-group-append">
                        <input type="checkbox" name="hostelPerDayCheckbox" id="hostelPerDayCheckbox">
                    </div> --}}
                </div>
                <span id="hostel_per_day-error" class="error text-danger"></span>
            </div>

            <div class="form-group col-md-2" id="refrigeratorFeeDiv">
                <label for="refrigeratorFee">Room Refrigerator Fee</label>
                <div class="input-group">
                    <input placeholder="Enter refrigerator fee" readonly class="form-control" type="text" name="refrigeratorFee" id="refrigeratorFee" oninput="validateInput(this)">
                    <div class="input-group-append">
                        <input type="checkbox" name="refrigeratorCheckBox" id="refrigeratorCheckBox" >
                    </div>
                </div>
                <span id="refrigeratorFee-error" class="error text-danger"></span>
            </div>

            <div class="form-group   col-md-2" id="electricFeeDiv">
                <label for="electricFee">Room Electric Heater Fee</label>
                <div class="input-group">
                    <input placeholder="Enter Electric Heater fee" readonly class="form-control" type="text" name="electricFee" id="electricFee" oninput="validateInput(this)">
                    <div class="input-group-append">
                        <input type="checkbox" name="electricHeaterCheckBox" id="electricHeaterCheckBox" >
                    </div>
                </div>
                <span id="electricFee-error" class="error text-danger"></span>
            </div>

            <div class="form-group  col-md-2" id="conditionerFeeDiv">
                <label for="conditionerFee">Air Conditioner Fee</label>
                <div class="input-group">
                    <input placeholder="Enter Conditioner fee" readonly class="form-control" type="text" name="conditionerFee" id="conditionerFee" oninput="validateInput(this)">
                    <div class="input-group-append">
                        <input type="checkbox" name="conditionerCheckBox" id="conditionerCheckBox" >
                    </div>
                </div>
                <span id="conditionerFee-error" class="error text-danger"></span>
            </div>

            <div class="form-group -md-2" id="daysNoDiv">
                <label for="daysNo">Enter Days</label>
                <div class="input-group">
                    <input placeholder="Enter No of Days" class="form-control" type="text" name="daysNo" id="daysNo" oninput="validateInput(this)">
                    {{-- <div class="input-group-append">
                        <input type="checkbox" name="daysNoCheckbox" id="daysNoCheckbox">
                    </div> --}}
                </div>
                <span id="daysNo-error" class="error text-danger"></span>
            </div>

            <div class="form-group col-md-4" id="hostelRemarksDiv">
                <label for="hostelRemarks" class="font-weight-bold">Remarks for Hostel</label>
                <textarea
                    placeholder="Enter your remarks (max 200 characters)"
                    class="form-control border-primary rounded"
                    name="hostelRemarks"
                    id="hostelRemarks"
                    rows="3"
                    maxlength="200"
                    oninput="validateRemarks(this)">
                </textarea>
                <small id="hostelRemarks-error" class="form-text text-danger"></small>
            </div>



                <div class="form-group col-md-4">
                    <label for="dueDate">Due Date</label>
                    <input type="date" class="form-control" id="dueDate" name="dueDate">
                    <span id="dueDate-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="anotherDate">Validate Date</label>
                    <input type="date" class="form-control" id="validateDate" name="validateDate">
                    <span id="validateDate-error" class="error text-danger"></span>
                </div>
                <div class="form-group d-none col-md-1 credit_hr_div">
                    <label for="credithours">Credit Hours</label>
                    <input type="number" min='0' class="form-control" id="creditHr" name="creditHr">
                </div>

            </div>















            <div class="row pt-2 pb-4 border rounded" style="background: linear-gradient(135deg, #f0f8ff, #e0f7fa); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 10px;">
                <div class="form-group col-md-4 noOfInstallmentsDiv" style='display:none;' id='noOfInstallmentsDiv'>
                    <label for="noOfInstallments">No of Installments</label>
                    <select class="form-control" id="noOfInstallments" name="noOfInstallments" onchange='calculateAndDisplayInstallments(this)' style="border-radius: 5px;">
                        <option value="">--- Select number of installments ---</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <span id="noOfInstallments-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-md-8">
                    <div class="form-check form-check-inline ml-4 mt-4">
                        <input class="form-check-input studentInstallment" type="checkbox" id="studentInstallment" name="studentInstallment" value="YES" checked style="transform: scale(1.5); margin-right: 10px;">
                        <label class="form-check-label" for="studentInstallment" style="font-size: 1rem; font-weight: 600; cursor: pointer; margin: 0;">
                            <i class="fa fa-check-circle"></i> Installment Allowed
                        </label>
                    </div>
                    <div class="form-check form-check-inline ml-4 mt-4">
                        <input type="checkbox" id="manualFeeAdjustment" name="manualFeeAdjustment" value="YES" style="width: 20px; height: 20px; cursor: pointer; margin-right: 10px;">
                        <label for="manualFeeAdjustment" style="font-size: 1rem; font-weight: 600; cursor: pointer; margin: 0;">
                            <i class="fa fa-adjust"></i> Fee Adjustment
                        </label>
                    </div>
                    <div class="form-check form-check-inline ml-4 mt-4">
                        <input type="checkbox" id="surplusAdjustment" name="surplusAdjustment" value="YES" style="width: 20px; height: 20px; cursor: pointer; margin-right: 10px;">
                        <label for="surplusAdjustment" style="font-size: 1rem; font-weight: 600; cursor: pointer; margin: 0;">
                            <i class="fa fa-plus-circle"></i> Surplus Adjustment
                        </label>
                    </div>
                </div>
            </div>



            <div id='installmentsDiv' class='p-1'>
</div>

<h2 id="successMsg" class="text-success"></h2>
            <div class="row">


            <div class="form-group col-md-12 d-flex align-items-left justify-content-start">
    <div>

        <button
    type="submit"
    class="btn btn-outline-primary shadow font-weight-bold rounded-pill"
    onclick="submitFormBtn(event)"
    id="submitBtn">
    <i class="fa fa-cogs mr-2"></i> Generate Challan
    </button>



<button
    type="button"
    id="clear"
    class="btn btn-outline-danger shadow font-weight-bold rounded-pill"
    onclick="clearFormAndContent()">
    <i class="fa fa-undo mr-2"></i> Clear
</button>



    </div>
    <div class="btn-group ml-2" role="group" aria-label="Filter Challan" style='display:none;' id='filterChallanBtn'>
        <button
    type="button"
    class="btn btn-outline-info shadow font-weight-bold rounded-pill dropdown-toggle"
    data-toggle="dropdown"
    aria-haspopup="true"
    aria-expanded="false">
    <i class="fa fa-filter mr-2"></i> Filter Challan
</button>

        <div class="dropdown-menu">
            <!-- <a class="dropdown-item" onclick="handleButtonClick('ALL')">All</a> -->
            <a class="dropdown-item" onclick="handleButtonClick('UNPAID')">UNPAID</a>
            <a class="dropdown-item" onclick="handleButtonClick('PAID')">PAID</a>
            <a class="dropdown-item" onclick="handleButtonClick('CANCELLED')">CANCELLED</a>
            <a class="dropdown-item" onclick="handleButtonClick('VERSION')">VERSION</a>
        </div>
    </div>
</div>
<div id="form-errors"></div>
            </div>
            </form>
            <hr>
            <div id="showDataInTable" class="table-responsive"></div>
        </div>
        </div>

        <div class="modal fade" id="challanDetailsModal" tabindex="-1" role="dialog" aria-labelledby="challanDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="challanDetailsModalLabel">Confirmation of Challan Deletion</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">


                        <form id="cancelChallanForm">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="challanIdInput" name="id" value="">
                            <input type="hidden" id="enrollmentNoInput" name="enrollment_no" value="">
                            <input type="hidden" id="scChallanStatus" name="scChallanStatus" value="">
                            <input type="hidden" id="scCheckInstallments" name="scCheckInstallments" value="">

                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Field Name</th>
                                        <th>Actual Value</th>
                                        <th>Edit Payable Values</th>
                                    </tr>
                                </thead>
                                <tbody id="challanDetailsTableBody">
                                    <!-- Dynamic rows will be appended here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="totalEditable"
                                                name="totalEditable"
                                                readonly
                                                value="0"
                                            >
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
         <!-- Error Message Placeholder -->
         <div id="errorMessage" class="alert alert-danger d-none" role="alert">
            Please correct the highlighted values. No value can exceed the original amount.
        </div>
                    <!-- Modal Footer -->
                    <div id='successMessage'></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="submitCancelChallan()" id="deleteChallanBtn">Cancel Now</button>
                    </div>
                </div>
            </div>
        </div>

@stop

@section('javascript')
<script>

function setDefaultDates() {
    // Function to format the date to YYYY-MM-DD
    function formatDate(date) {
        let d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    // Get the current date
    let currentDate = new Date();

    // Set the Due Date as the current date
    document.getElementById('dueDate').value = formatDate(currentDate);

    // Calculate the Validate Date (current date + 1 week)
    let validateDate = new Date(currentDate);
    validateDate.setDate(currentDate.getDate() + 7); // Add 7 days

    // Set the Validate Date
    document.getElementById('validateDate').value = formatDate(validateDate);
}


window.onload = setDefaultDates;

$(document).ready(function(){

    hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv','hostel_security_feeDiv', 'refrigeratorFeeDiv','electricFeeDiv','conditionerFeeDiv','daysNoDiv','hostelRemarksDiv','hostel-sess-typeDiv']);



    $('#student_type').change(function() {
        if($(this).val() != 'Existing'){
            $('.admissionFeeDiv').removeClass('d-none');
            $('.securityAmtDiv').removeClass('d-none');
        }else{
            $('.admissionFeeDiv').addClass('d-none');
            $('#admissionFee').val('');
            $('.securityAmtDiv').addClass('d-none');
            $('#securityFee').val('');
        }

    });
});
//

const globalDataMap = new Map();

function resetFormExceptButton() {
    const form = document.getElementById('singleChallanForm');
    const searchButton = document.getElementById('studentEnrollment');

    // Loop through form elements and reset them except for the button
    Array.from(form.elements).forEach(element => {
        if (element !== searchButton) {
            switch (element.type) {
                case 'text':
                case 'password':
                case 'textarea':
                case 'select-one':
                case 'date':
                    element.value = ''; // Clear value for text inputs, textareas, and selects
                    break;
                case 'checkbox':
                case 'radio':
                    element.checked = false; // Uncheck checkboxes and radio buttons
                    break;
                case 'file':
                    element.value = ''; // Clear file inputs (if you need to reset them)
                    break;
                default:
                    // Handle other input types if needed
                    break;
            }
        }
    });
}

 function validateInput(input) {
    // Allow only numbers and one decimal point
    input.value = input.value.replace(/[^0-9.]/g, ''); // Replace anything not a digit or a dot
    input.value = input.value.replace(/^(\d*\.?)|(\d*\.\d{0,2}).*/g, '$1$2'); // Ensure only one dot and at most two decimal places
}
// Function to generate HTML table code

function generateTable(data, status=null) {
   // console.log("Status passed to function:", status);
    // alert(status);
    let staffUrl = "{{ config('app.staff_url') }}";
    // console.log(status);
    let showDataInTable = document.getElementById('showDataInTable');
    showDataInTable.innerHTML = ''; // Clear previous content
    // Define the base URL globally
    const generateChallanUrl = `${staffUrl}/chalanfin.php?challan_id=`;

    if (Array.isArray(data) && data.length > 0) {
        globalDataMap.set('data', data);
        let actionHeading = '';
        if(status != 'VERSION'){
            actionHeading=`<th>Action</th><th><input type="checkbox" id="selectAll"></th>`;
        }
        let createHtml = `
            <div class="d-flex justify-content-end">
                <button class="btn btn-danger mb-3" id="cancelChallanBtn" style="display:none;">Cancel Challan</button>
            </div>

            <table class="table table-bordered table-hover" id='mydatatable'>
                <thead class="thead-light">
                    <tr>

                        <th>Challan No#</th>
                        <th>Enrollment No</th>
                        <th>Session ID</th>
                        <th>Student Type</th>
                        <th>Due Date</th>
                        <th>Valid Date</th>
                        <th>Tuition Fee</th>
                        <th>Admission Fee</th>
                        <th>Security Fee</th>
                        <th>Medical Fee</th>
                        <th>Activity Charges</th>
                        <th>Invoice Amount</th>
                        <th>Challan Status</th>
                        `+actionHeading+`

                    </tr>
                </thead>
                <tbody>
                    ${data.map(item => {
                        let badgeColor = 'secondary'; // Default color for undefined status
                        if (item.sc_challan_status === 'PAID') {
                            badgeColor = 'success';
                        } else if (item.sc_challan_status === 'UNPAID') {
                            badgeColor = 'danger';
                        } else if (status === 'CANCELLED') {
                            badgeColor = 'warning';
                        }

                        let rowClass = '';
                        let disableCheckbox = '';
                        let textDecoration = '';


                        const rowStyle = item.sc_session_id < 163 ? 'background-color: #e0f7fa;' : '';
                        const isOldSession = item.sc_session_id < 160 && item.sc_challan_type != 'Full_semester'; // Boolean for session condition

                        if (status === 'CANCELLED' && item.challan_cancelled_by) {
                            rowClass = 'table-danger'; // Change row color to indicate cancellation
                            disableCheckbox = 'disabled'; // Disable the checkbox
                            textDecoration = 'text-decoration: line-through;'; // Apply line-through decoration
                        }




                        // Function to check if value is null or undefined and return 'N/A'
                        const checkValue = value => (value === null || value === undefined) ? 'N/A' : value;

                        let challanNo = status === 'CANCELLED' && item.challan_cancelled_by
                                        ? item.sc_id
                                        : item.id;
                        let concateActions='';
                        let cancelButton = '';
                        // if (item.sc_challan_status === 'UNPAID') {
                            cancelButton = `<button class="btn btn-outline-danger btn-sm cancel-row" data-row='${JSON.stringify(item)}'  >
                                                <i class="fa fa-times"></i> Cancel
                                            </button>`;
                        // }
                        // Default Installment Button Name
                        let installmentBtnName = item.sc_challan_status === 'CANCEL FOR REISSUANCE'
                            ? "Active Now"
                            : "Edit";
                            // Fetch the user's auth group ID from PHP
                        let userAuthGroupId = <?php echo json_encode(auth()->user()->group_id); ?>;
                        let hasEditPermission = userAuthGroupId == 644;
                        if (status != 'VERSION') {
                            if (item.sc_challan_status === 'PAID' && hasEditPermission) {
                                // Show Edit Button only if PAID and user has permission
                                concateActions = `
                                    <td style="${textDecoration}; display: flex; align-items: center; gap: 10px;">
                                        <button class="btn btn-outline-primary btn-sm edit-row" data-row='${JSON.stringify(item)}' ${disableCheckbox}>
                                            <i class="fa fa-edit"></i> ${installmentBtnName}
                                        </button>
                                        <a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-outline-info d-inline-block p-1">
                                            <i class="fa fa-eye"></i> Preview
                                        </a>
                                    </td>
                                    <td>
                                        ${disableCheckbox || item.sc_challan_status === 'CANCEL FOR REISSUANCE' ? `` : cancelButton}
                                    </td>`;
                            } else if (item.sc_challan_status !== 'PAID' && status !== 'CANCELLED' && !isOldSession) {
                                // Normal Case - Non-Paid Challans
                                concateActions = `
                                    <td style="${textDecoration}; display: flex; align-items: center; gap: 10px;">
                                        <button class="btn btn-outline-primary btn-sm edit-row" data-row='${JSON.stringify(item)}' ${disableCheckbox}>
                                            <i class="fa fa-edit"></i> ${installmentBtnName}
                                        </button>
                                        <a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-outline-info d-inline-block p-1">
                                            <i class="fa fa-eye"></i> Preview
                                        </a>
                                    </td>
                                    <td>
                                        ${disableCheckbox || item.sc_challan_status === 'CANCEL FOR REISSUANCE' ? `` : cancelButton}
                                    </td>`;
                            } else {
                                // Only Preview Button
                                concateActions = `
                                    <td style="${textDecoration}; display: flex; align-items: center; gap: 10px;">
                                        <a href="${generateChallanUrl}${checkValue(challanNo)}" target="_blank" class="btn btn-info d-inline-block p-1">
                                            <i class="fa fa-eye"></i> Preview
                                        </a>
                                    </td>
                                    <td>
                                        ${!isOldSession
                                            ? (disableCheckbox || item.sc_challan_status === 'CANCEL FOR REISSUANCE'
                                                ? ''
                                                : cancelButton)
                                            : ''}
                                    </td>`;
                            }
                        }
                        return `
                           <tr class="${rowClass}" style="${rowStyle}">

                                <td style="${textDecoration}">${checkValue(challanNo)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_enrollment_no)}</td>
                                <td style="${textDecoration}">${checkValue(item.sess_code)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_student_status)}</td>
                                 <td>${formatDateTime(item.sc_due_date)}</td>
                                <td>${formatDateTime(item.sc_valid_date)}</td>

                                <td style="${textDecoration}">${checkValue(item.sc_installment_tuition_fee_amnt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_admission_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_security_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.ffs_medical_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.sc_activity_amt)}</td>
                                <td style="${textDecoration}">${checkValue(item.invoiceamount)}</td>
                                <td style="${textDecoration}"><span class="badge badge-${badgeColor}">${checkValue(item.sc_challan_status)}</span></td>
                                `+concateActions+`
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;

        showDataInTable.innerHTML = createHtml;
        $('#mydatatable').DataTable();

        // Add event listeners to dynamically created elements
        let editButtons = document.querySelectorAll('.edit-row');
        editButtons.forEach(button => {
            button.addEventListener('click', editRec);
        });

        let checkboxes = document.querySelectorAll('.challan-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleCancelButton);
        });

        document.getElementById('selectAll').addEventListener('change', toggleSelectAll);

        document.getElementById('cancelChallanBtn').addEventListener('click', cancelChallans);
    } else {
        showDataInTable.innerHTML = '<p>No data available</p>';
    }
}


function formatDateTime(dateString) {
    const date = new Date(dateString); // Parse the input string into a Date object

    // Check if the date is valid
    if (isNaN(date.getTime())) {
        return "Invalid Date"; // Return a fallback if the date is invalid
    }

    const day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const month = monthNames[date.getMonth()]; // Get the month name
    const year = date.getFullYear(); // Get the 4-digit year

    return `${day} ${month} ${year}`; // Format as "DD Month YYYY"
}




function handleButtonClick(status) {
    // Construct the URL with query parameters
    var enrollmentNo = $('#studentEnrollment').val();
    if (!enrollmentNo) {
        alert('Please enter the enrollment number.');
        return false; // Exit the function
    }

    const url = `/finance/student-filter-challans?status=${encodeURIComponent(status)}&enrollmentNo=${encodeURIComponent(enrollmentNo)}`;

    // Fetch data from the server
    fetch(url, {
        method: 'GET', // Use GET method for fetching data
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // alert(status);
            // Refresh the table data with new data
                generateTable(data.all_data, status);


            successMsg.html(createAlert('success', data.message));
        } else {
            // Handle the case where the request was not successful
            successMsg.html(createAlert('danger', data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        successMsg.html(createAlert('danger', 'An error occurred while processing your request.'));
    });
}




function toggleSelectAll() {
    let checkboxes = document.querySelectorAll('.challan-checkbox:not(:disabled)');
    let isChecked = this.checked;
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    toggleCancelButton();
}

function toggleCancelButton() {
    let selectedCheckboxes = document.querySelectorAll('.challan-checkbox:checked');
    let cancelChallanBtn = document.getElementById('cancelChallanBtn');
    cancelChallanBtn.style.display = selectedCheckboxes.length > 0 ? 'block' : 'none';
}



function cancelChallans() {
       // Get the message element
       var successMsg = $('#successMsg');
        // Clear any existing message
        successMsg.html('');
    let selectedCheckboxes = document.querySelectorAll('.challan-checkbox:checked');
    let ids = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.id);
    if (ids.length > 0) {
        fetch('/finance/student-cancel-challans', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the table data
                successMsg.html(createAlert('success', data.message));
                generateTable(data.all_data);
                document.getElementById('searchButton').click();

                // alert('Challans cancelled successfully');

                 // Timeout to hide success message after 4 seconds
        setTimeout(function() {
            successMsg.html('');
        }, 4000);

            } else {
                alert('Failed to cancel challans');
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        alert('No challans selected');
    }
}



function editRec(event) {

    let buttonElement = event.target;
    let rowDataString = buttonElement.dataset.row;
    let rowData = JSON.parse(rowDataString);
    let sc_challan_type=rowData.sc_challan_type;
    let hostel_fee_type=rowData.sc_hostel_fee_type;
    const formatDateIfValid = (date) => date ? formatDate(new Date(date)) : '';
    const formattedScDueDate = formatDateIfValid(rowData.sc_due_date);
    const formattedValidateDate = formatDateIfValid(rowData.sc_valid_date);
    // console.log(rowData);
        if(sc_challan_type == 'Credit_hours'){
             $('#noOfInstallments').val('');
            // Set the formatted dates to the respective fields
            setTimeout(function() {
                $('#dueDate').val(formattedScDueDate).trigger('change').removeAttr('readonly');
                $('#validateDate').val(formattedValidateDate).trigger('change').removeAttr('readonly');
                 $('#noOfInstallments').closest('.form-group').show();
            }, 1000); // 2000ms = 2 seconds delay
            setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
            setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
            setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);
            setSelectValueAndPreventDropdown('feeType', sc_challan_type);
            $('#creditHr').val(rowData.sc_enrolled_cr_hr).attr('readonly', true);
            $('#verifyEdit').val('1');
            $('#edit_id').val(rowData.id).attr('readonly', true);

            $('.credit_hr_div').removeClass('d-none');
            $('#full_semester_dev').hide();
            // Add readonly attribute back to the fields
            $('#tuitionFee').attr('readonly', true);
            $('#medicalFee').attr('readonly', true);
            $('#admissionFee').attr('readonly', true);
            $('#activitychargesAmt').attr('readonly', true);
            $('#securityFee').attr('readonly', true);
            scrollToInstallments();
        }
        else if(sc_challan_type == 'Fee_Refund'){
            $('#feeType').val(sc_challan_type)
            $('#full_semester_dev').show();
            $('#tuitionFee').val(rowData['sc_tuition_fee_amnt']);
            $('#medicalFee').val(rowData['ffs_medical_amt']);
            $('#admissionFee').val(rowData['ffs_admission_amt']);
            $('#activitychargesAmt').val(rowData['sc_activity_amt']);
            $('#securityFee').val(rowData['ffs_security_amt']);
            $('#editChallan').val(rowData['challan_no']);
            $('#refundEdit').val(1);


            // Remove readonly attribute from the fields
            $('#tuitionFee').removeAttr('readonly');
            $('#medicalFee').removeAttr('readonly');
            $('#admissionFee').removeAttr('readonly');
            $('#activitychargesAmt').removeAttr('readonly');
            $('#securityFee').removeAttr('readonly');
            $('.credit_hr_div').addClass('d-none');

           scrollToInstallments();
        }else{
            $('#refundEdit').val('');
            if (rowData.check_fee_installments > 0 || ['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(hostel_fee_type)) {

                // document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'none', 'important');
                $('#noOfInstallments').closest('.form-group').hide();
                $('#scholarshipsAmountOpt').hide();
                 setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
                 setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);
                 setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);


            }





                let installmentsDiv = document.getElementById('installmentsDiv');
                installmentsDiv.innerHTML = ''; // Clear previous content

                scrollToInstallments();
                $('#noOfInstallments').val('');
                $('#verifyEdit').val('1');
                $('#edit_id').val(rowData.id).attr('readonly', true);
                hideElementsById(['hostelFeeDiv', 'hostelRemarksDiv','hostel_per_dayDiv', 'hostel_security_feeDiv', 'refrigeratorFeeDiv', 'electricFeeDiv', 'conditionerFeeDiv', 'daysNoDiv','hostel-sess-typeDiv']);
                // Check for valid 'sc_challan_type' and hide/display elements based on feeType
                if (['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(hostel_fee_type)) {
                // Display relevant elements based on feeType
                const feeElements = {
                    'Hostel_Full_semster': ['hostelFeeDiv', 'hostel_security_feeDiv','hostel-sess-typeDiv'],
                    'Hostel_day_wise': ['hostel_per_dayDiv', 'daysNoDiv','hostelRemarksDiv','hostel-sess-typeDiv'],
                    'Hostel_other_utilities': ['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv','hostel-sess-typeDiv']
                };
                // Show elements for the selected feeType
                if (feeElements[feeType]) {
                    showElementsById(feeElements[feeType]);
            }
                    if(hostel_fee_type == 'Hostel_other_utilities'){
                        setTimeout(function() {
                            removeInputReadOnly('refrigeratorFee');
                            removeInputReadOnly('electricFee');
                            removeInputReadOnly('conditionerFee');
                        }, 2000);
                    }
                // Make input read-only with the given invoice amount
                makeInputReadOnly('studentInvoiceamount', rowData.invoiceamount);



                // Set the feeType value and trigger change event
                $('#feeType').val(hostel_fee_type).trigger('change');
                // Parse and format dates using JavaScript Date object
                // const formatDateIfValid = (date) => date ? formatDate(new Date(date)) : '';
                const formattedScDueDate = formatDateIfValid(rowData.sc_due_date);
                const formattedValidateDate = formatDateIfValid(rowData.sc_valid_date);
                // Set the formatted dates to the respective fields
                setTimeout(function() {
                    $('#dueDate').val(formattedScDueDate).trigger('change').removeAttr('readonly');
                    $('#validateDate').val(formattedValidateDate).trigger('change').removeAttr('readonly');
                }, 1000); // 2000ms = 2 seconds delay
                // Show the invoice amount option
                $('#invoiceAmountOpt').show();

                //$('#daysNo').val(123);



                if ( ['Hostel_day_wise'].includes(hostel_fee_type)) {
                    setTimeout(function() {
                    let hostelPerDay = parseFloat($('#hostel_per_day').val());
                    let feeDetails = rowData.sc_hostel_fee_details.replace(/^Hostel Fee days wise charges\.\s*/, '');
                    $('#hostelRemarks').val(feeDetails);
                    let daysNo = rowData.invoiceamount
                    ? Math.floor(rowData.invoiceamount/ hostelPerDay )
                    : '';
                    $('#daysNo').val(daysNo);

                    }, 1000);
                    }
                    else{
                        $('#hostel_per_day').val('')
                    }


                    return;
                }


                removeInputReadOnly('sessionId');

                        makeInputReadOnly('studentInvoiceamount', rowData.invoiceamount);
                        makeInputReadOnly('scholarshipsAmount', rowData.sc_scholarship_amount);
                        makeInputReadOnly('tuitionFee', rowData.sc_tuition_fee_amnt);

                        setSelectValueAndPreventDropdown('sessionId', rowData.sc_session_id);
                        setSelectValueAndPreventDropdown('student_type', rowData.sc_student_status);


                        makeInputReadOnly('admissionFee', rowData.ffs_admission_amt);
                        makeInputReadOnly('securityFee', rowData.ffs_security_amt);
                        makeInputReadOnly('medicalFee', rowData.ffs_medical_amt);

                        setSelectValueAndPreventDropdown('student_status', rowData.sc_sai_student_status);

                        makeInputReadOnly('activitychargesAmt', rowData.sc_activity_amt);


                    if (rowData.sc_installment_allowed === "YES") {
                        $('#studentInstallment').prop('checked', true);
                    } else {
                        $('#studentInstallment').prop('checked', false);
                    }
                            console.log(rowData.sc_due_date);

                    // Parse dates using JavaScript Date object
                    const scDueDate = rowData.sc_due_date ? new Date(rowData.sc_due_date) : null;
                    const ssValidateDate = rowData.sc_valid_date ? new Date(rowData.sc_valid_date) : null;

                    const formattedScDueDate = scDueDate ? formatDate(scDueDate) : '';
                    const formattedValidateDate = ssValidateDate ? formatDate(ssValidateDate) : '';

                    $('#dueDate').val(formattedScDueDate).trigger('change');
                    $('#validateDate').val(formattedValidateDate).trigger('change');

                    $('#invoiceAmountOpt').show();
                    $('#scholarshipsAmountOpt').show();





                // ysa sy read only remove krna phly readonly sirf  CANCEL FOR REISSUANCE  us k bad sb k lie open kr dia
                // hy account office k khny py
                // if(rowData.sc_challan_status === 'CANCEL FOR REISSUANCE'){
                    removeInputReadOnly('studentInvoiceamount');
                    // removeInputReadOnly('scholarshipsAmount');
                    removeInputReadOnly('sessionId');
                    removeInputReadOnly('admissionFee');
                    removeInputReadOnly('tuitionFee');
                    removeInputReadOnly('securityFee');
                    removeInputReadOnly('medicalFee');
                    removeInputReadOnly('activitychargesAmt');

                    $('#feeType').val(sc_challan_type).trigger('change');

                        setTimeout(function() {
                    $('#dueDate').val(formattedScDueDate).trigger('change').removeAttr('readonly');
                    $('#validateDate').val(formattedValidateDate).trigger('change').removeAttr('readonly');
                    $('#noOfInstallments').closest('.form-group').show();

                }, 1000); // 2000ms = 2 seconds delay





                //document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'block', 'important');


                    try {
                        if (rowData && rowData.sc_challan_status) {
                        // console.log("Data is valid");
                        }
                    } catch (e) {
                        console.error("Error processing data: ", e);
                    }
                // }
        }
}


// Function to format date as YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}


function setSelectValueAndPreventDropdown(id, value) {
    $(`#${id}`).val(value).trigger('change').on('mousedown', function(e) {
        e.preventDefault(); // Prevent the dropdown from opening
    });
}

function makeInputReadOnly(id, value) {
    $(`#${id}`)
        .val(value) // Set the value
        .attr('readonly', true) // Make the input read-only
        .on('keydown', function(e) {
            e.preventDefault(); // Prevent any key press in the input field
        });
}

function removeInputReadOnly(id) {
    $(`#${id}`)
        .removeAttr('readonly') // Remove the read-only attribute
        .off('keydown'); // Remove the event handler to allow key presses
}

document.addEventListener('click', function (event) {
    if (event.target.closest('.cancel-row')) {
        const buttonElement = event.target.closest('.cancel-row');
        const rowData = JSON.parse(buttonElement.getAttribute('data-row'));
        cancelRow(rowData);
    }
});


 //Updates the total value dynamically and validates input values.


 function updateTotal() {
    const inputs = document.querySelectorAll('.editable-input');
    const scCheckInstallments = document.getElementById('scCheckInstallments').value;
    const scChallanStatus = document.getElementById('scChallanStatus').value;

    let total = 0;

    inputs.forEach(input => {
        const maxValue = parseFloat(input.dataset.maxValue); // Get max value from data attribute
        let value = parseFloat(input.value) || 0; // Treat empty/invalid inputs as 0
        const inputName = input.name;


if(scChallanStatus == 'PAID'){


if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
        total += value;
        return; // Skip the rest of the loop for these specific inputs
            }
}
else{
   if (scCheckInstallments == 0) {
         // Agar scCheckInstallments 0 hai, toh 'sc_surplus_amount' aur 'sc_scholarship_amount' ko sum mein add karo
          if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
        total += value;
        return; // Skip the rest of the loop for these specific inputs
            }
        } else {
            // Agar scCheckInstallments 0 nahi hai, toh 'sc_surplus_amount' aur 'sc_scholarship_amount' ko skip kar do
            if (inputName === 'sc_surplus_amount' || inputName === 'sc_scholarship_amount') {
                return; // Skip the rest of the loop for these specific inputs
            }
        }
}






        if (value < 0) {
            value = 0; // Reset the value to 0 if it's negative
            input.value = 0; // Ensure the input field shows 0
        }
        // Check if input is empty (NaN or zero), and if so, set it to zero (if not already set)
        if (isNaN(value) || value === 0) {
            input.value = 0; // Default the value to 0
            value = 0; // Ensure value is 0 in the calculation
        }
        if (value > maxValue) {
            input.classList.add('is-invalid'); // Add Bootstrap error class
        } else {
            input.classList.remove('is-invalid'); // Remove error class if valid
        }

        // Add to total only if value is valid and non-negative
        total += value <= maxValue ? value : 0;
    });

    // Update the total value
    document.getElementById('totalEditable').value = total.toFixed(2);
}


//Populates the modal with row data and displays it.




function cancelRow(rowData) {
    // Update hidden inputs in the form
    // console.log(rowData);

    document.getElementById('challanIdInput').value = rowData.id;
    document.getElementById('enrollmentNoInput').value = rowData.sc_enrollment_no || rowData.sc_app_id;
    document.getElementById('scChallanStatus').value = rowData.sc_challan_status;
    document.getElementById('scCheckInstallments').value = rowData.check_fee_installments;
    let ffs_credhr_amt = rowData.ffs_credhr_amt;

    // Clear previous rows in the table body
    const tableBody = document.getElementById('challanDetailsTableBody');
    tableBody.innerHTML = '';

    // Define fields to display
    let fields = [
        {
            name: 'Tuition Fee',
            key: (rowData.ffs_credhr_amt !== null &&
                typeof rowData.ffs_credhr_amt !== 'undefined' &&
                rowData.ffs_credhr_amt > 0)
                ? 'ffs_credhr_amt'
                : 'sc_installment_tuition_fee_amnt',
            isTuitionFee: true
        },
        { name: 'Medical Fee', key: 'ffs_medical_amt' },
        { name: 'Adjusted Surplus', key: 'sc_surplus_amount' },
        { name: 'Activity Charges', key: 'sc_activity_amt' },
        { name: 'Admission Fee', key: 'ffs_admission_amt' },
        { name: 'Security Fee', key: 'ffs_security_amt' },
        { name: 'Scholarship Amount', key: 'sc_scholarship_amount' },
    ];

    const isUnpaid = rowData.sc_challan_status === "UNPAID";
    const sc_surplus_amount = parseFloat(rowData.sc_surplus_amount) || 0;
    const sc_scholarship_amount = parseFloat(rowData.sc_scholarship_amount) || 0;

    let challanType = rowData.sc_hostel_fee_type;

    if (['Hostel_other_utilities', 'Hostel_day_wise', 'Hostel_Full_semster'].includes(challanType)) {
        // Modify challanType to make it more readable
        fields = [];
        let formattedChallanType = challanType.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
        // Empty the fields array and add the dynamic data
        fields.push({ name: formattedChallanType, key: 'invoiceamount' });
    }

    // Build table rows dynamically
    let rowsContent = '';
    fields.forEach(field => {
        let value = parseFloat(rowData[field.key]) || 0;

        // Adjust Surplus Amount if applicable
        if (field.key === 'sc_surplus_amount') {
            value = sc_surplus_amount;
            if (value > 0) {
                value = -value;
            }
        }

        if (field.key === 'sc_scholarship_amount') {
            value = parseFloat(rowData.sc_scholarship_amount) || 0;
            if (value > 0) {
                value = -value;
            }
        }

        rowsContent += `
            <tr>
                <td>${field.name}</td>
                <td class="value-cell">
                    ${field.key === 'sc_installment_tuition_fee_amnt' && rowData.sc_tuition_fee_amnt
                        ? Math.abs(parseFloat(rowData.sc_tuition_fee_amnt))
                        : Math.abs(value)}
                </td>
                <td>
                    <input
                        type="number"
                        class="form-control editable-input"
                        name="${field.key}"
                        value="${value}"
                        placeholder="Enter new value"
                        data-max-value="${value}"
                        oninput="updateTotal()"
                        ${isUnpaid ? 'disabled' : ''}
                    >
                    <div class="invalid-feedback">Value cannot exceed ${value}</div>
                </td>
            </tr>
        `;
    });

    if (rowData.sc_challan_status === 'PAID') {
        rowsContent += `
            <tr>
                <td>Adjust in Surplus</td>
                <td colspan="2">
                    <label for="surplus_option" class="form-label">
                        <small>Required <span style="color: red;">*</span></small>
                    </label>
                    <select class="form-control" name="surplus_option" id="surplus_option">
                        <option value="">---Select Option---</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
                    <span id="surplus_option-error" class="error text-danger"></span>
                </td>
            </tr>
        `;
    }

    // Add the dropdown for Type and the input for Remarks
    rowsContent += `
        <tr>
            <td>Type</td>
            <td colspan="2">
                <label for="cancell_type" class="form-label">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <select class="form-control" name="cancell_type" id="cancell_type">
                    <option value="">---Select Type---</option>
                    <option value="CANCELLED">Cancel</option>
                </select>
                <span id="cancell_type-error" class="error text-danger"></span>
            </td>
        </tr>
        <tr>
            <td>Remarks</td>
            <td colspan="2">
                <label for="cancell_type" class="form-label">
                    <small>Required <span style="color: red;">*</span></small>
                </label>
                <input
                    type="text"
                    class="form-control"
                    name="remarks"
                    placeholder="Enter remarks"
                >
                <span id="remarks-error" class="error text-danger"></span>
            </td>
        </tr>
    `;

    // Append rows to the table body
    tableBody.innerHTML = rowsContent;

    // Update the total immediately
    updateTotal();

    // Show the modal
    $('#challanDetailsModal').modal('show');
}



// Submit the delete challan form after validation.
 function submitCancelChallan() {
    const form = $('#cancelChallanForm');
    const inputs = form.find('.editable-input');
    const errorMessage = $('#errorMessage');
    const successMessageDiv = $('#successMessage');
    const submitButton = $('#deleteChallanBtn');
    let hasErrors = false;

    // Hide any previous error message
    errorMessage.addClass('d-none');

    // Check if any input exceeds its max value
    inputs.each(function () {
        const input = $(this);
        const value = parseFloat(input.val()) || 0;
        const maxValue = parseFloat(input.data('max-value'));
        const inputName = input.attr('name');

        // Skip validation for 'sc_surplus_amount'
        if (inputName === 'sc_surplus_amount') {
            return; // Skip the rest of the validation for this specific input
        }

        if (value > maxValue) {
            input.addClass('is-invalid'); // Add error class
            hasErrors = true;
        } else {
            input.removeClass('is-invalid'); // Remove error class
        }
    });

    // If there are errors, show the error message in the modal
    if (hasErrors) {
        errorMessage.removeClass('d-none'); // Display error message

        // Automatically hide the error message after 5 seconds
        setTimeout(() => errorMessage.addClass('d-none'), 5000);
        return false;
    }

    // Disable the button and show the spinner with 'Submitting...' text
    submitButton.prop('disabled', true);
    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling, please wait... Do not close.');

    // Serialize form data
    const formData = form.serialize();

    // Perform AJAX POST
    $.ajax({
        url: '/finance/student-cancel-challans',
        type: 'POST',
        data: formData,
        contentType: 'application/x-www-form-urlencoded',
        success: function (response) {
            // Ensure response.success exists and is properly handled
            const alertType = response.success ? 'success' : 'danger';
            const message = `
                <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                    <strong>${response.success ? 'Success!' : 'Error!'}</strong> ${response.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            successMessageDiv.html(message);

            if (response.success) {
                setTimeout(() => {
                    $('#challanDetailsModal').modal('hide');
                    $('#verifyEdit').val('');
                    $('#edit_id').val('');
                    successMessageDiv.html('');
                    // Uncomment if searchStudent needs to run
                }, 1000);
            }
            searchStudent();

            // Always reset the button and spinner
            submitButton.prop('disabled', false);
            submitButton.html('Cancel Now');
        },
        error: function (xhr, status, error) {
            displayValidationErrors(xhr.responseJSON);
            submitButton.prop('disabled', false);
            submitButton.html('Cancel Now');
        },
        complete: function () {
            // Ensure spinner stops in any case (optional safeguard)
            submitButton.prop('disabled', false);
            submitButton.html('Cancel Now');
        },
    });
}



function submitFormBtn(event) {
    event.preventDefault();

    // Validate sessionId
    let sessionId = $('#sessionId').val();
    let studentEnrollment = $('#studentEnrollment').val();

    if (!sessionId) {
        $('#sessionId-error').text('Session is required.');
        return;
    } else {
        $('#sessionId-error').text('');
    }

    if (!studentEnrollment) {
        $('#studentEnrollment-error').text('Student enrollment is required.');
        return;
    } else {
        $('#studentEnrollment-error').text('');
    }

    if (!validateDateFields()) {
            return; // Stop if validation fails
        }


    // Get the form element
    var form = $('#singleChallanForm')[0];

    // Check if the form exists
    if (!form) {
        console.error('Form element not found');
        return;
    }

    // Create a new FormData object
    var formData = new FormData(form);
    // console.log('sess_type value:', formData.get('sess_type'));
    var checkboxes = form.querySelectorAll('input[type=checkbox]');
    checkboxes.forEach(function(checkbox) {
        if (!formData.has(checkbox.name)) {
            formData.append(checkbox.name, 'NO');
        }
    });
    // Get the message element
    var successMsg = $('#successMsg');
    // Clear any existing message
    successMsg.html('');
    const submitButton = $('#submitBtn');

    submitButton.prop('disabled', true);
    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating, please wait...');


    // Make an AJAX request
    $.ajax({
        url: '/finance/submit-single-challan-generate',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            // Handle success response
            $('#editChallan').val('');
            $('#refundEdit').val('');


            if (data.recordIssue) {
                successMsg.html(createAlert('error', data.message));
                        setTimeout(function() {
                        successMsg.html('');
                    }, 4000);
                return;

            }
        //    console.log(data.message);

            // document.getElementById("singleChallanForm").reset();
            // Generate success alert HTML and append to successMsg element
            if(data.message == 'contradicted'){

                successMsg.html(createAlert('error', 'There is contradiction of Student Id and Student type'));
            }else{
                successMsg.html(createAlert('success', data.message));
            }

            $('#verifyEdit').val('');
            $('#edit_id').val('');

            // // Generate table HTML using the function
            // var tableHTML = generateTable(data.all_data); // Assuming data is a single object
            // $('#showDataInTable').html(tableHTML);
            // $('#dataTable').DataTable();

            // Timeout to hide success message after 4 seconds
            setTimeout(function() {
                successMsg.html('');
            }, 4000);
            setTimeout(function() {
                document.getElementById('searchButton').click();
            }, 2000);




             // Re-enable the button and reset text
             submitButton.prop('disabled', false);
            submitButton.html('<i class="fa fa-cogs"></i> Generate Challan');


        },
        error: function(xhr) {
            // Handle error response
            console.error('Error:', xhr);

            // Attempt to parse response JSON if possible
            try {
                var response = JSON.parse(xhr.responseText);
            //    console.log('Response Data:', response);


                // Display validation errors if they exist
                if (response.errors) {
                   if (response.errors) {
                        displayGeneralError(response);
                    }
                    displayValidationErrors(response);
                } else {
                   // Show the error message
                    successMsg.html(createAlert('error', 'Failed to Generate Challan'));

                    // Hide the message after 3 seconds (3000 milliseconds)
                    setTimeout(function () {
                        successMsg.html(''); // Clear the message
                    }, 3000);


                }
            } catch (e) {
                console.error('Error parsing response JSON:', e);
                successMsg.html(createAlert('error', 'Failed to Generate Challan: ' + (xhr.statusText || 'Unknown error')));
                setTimeout(function () {
                    successMsg.html(''); // Clear the message
                }, 3000);
            }

            submitButton.prop('disabled', false);
            submitButton.html('<i class="fa fa-cogs"></i> Generate Challan');


        }
    });
}

document.getElementById('submitFormBtn').addEventListener('click', submitFormBtn);

function displayValidationErrors(response) {
   // console.log('Displaying validation errors:', response);

    if (response && response.errors) {
        Object.entries(response.errors).forEach(([key, value]) => {
          //  console.log(`Processing error for: ${key}`);
            var trimmedKey = key.trim();
            var errorElement = document.getElementById(trimmedKey + '-error');

           // console.log('Error Element:', errorElement);

            if (errorElement) {
                var errorMessage = Array.isArray(value) ? value[0] : value;
                var errorSpan = document.createElement('span');
                errorSpan.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errorMessage;
                errorElement.innerHTML = ''; // Clear previous error message
                errorElement.appendChild(errorSpan);
                errorElement.classList.add('text-danger'); // Add error styling

                // Clear the error message after 5 seconds
                setTimeout(function () {
                    errorElement.innerHTML = '';
                    errorElement.classList.remove('text-danger');
                }, 7000);
            } else {
                console.warn(`No element found for ID: ${trimmedKey}-error`);
            }
        });
    } else {
        console.error('Unexpected error structure:', response);
    }
}
function displayGeneralError(response) {
    console.log(response);
    const errorBox = $('#form-errors');
    let errorHtml = '';

    if (response && response.errors && response.errors.error) {
        // Handles: errors: { error: "message here" }
        errorHtml = `<div class="alert alert-danger">${response.errors.error}</div>`;
    } else if (response && response.errors) {
        // Handles: { error: "message here" }
        errorHtml = `<div class="alert alert-danger">${response.errors}</div>`;
    } else {
        errorHtml = `<div class="alert alert-danger">An unknown error occurred.</div>`;
    }

    errorBox.html(errorHtml);

    // Optional auto-clear after 6 seconds
    setTimeout(() => {
        errorBox.html('');
    }, 6000);
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

    // Return the alert HTML
    return alertHTML;
}


  // Function to handle Enter key press
  function handleEnterKey(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default form submission
            searchStudent(); // Call the searchStudent function
        }
    }

    // Main search function
    function searchStudent() {
        var enrollmentNo = $('#studentEnrollment').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        // Clear any previous error message
        $('#studentEnrollment-error').text('');
        resetFormExceptButton();
        var installmentsDiv = document.getElementById('installmentsDiv');
        installmentsDiv.innerHTML = '';

        // Validate the input
        if (enrollmentNo.trim() === '') {
            $('#studentEnrollment-error').text('Please enter an enrollment number.');
            return;
        }


        // Send the AJAX request
        $.ajax({
            url: '/finance/search-student-challans-available',
            type: 'POST',
            data: {
                studentEnrollment: enrollmentNo
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(allData) {


                //console.log(allData.studentRec);
                // Handle success response
                var tableHTML = generateTable(allData.challans);
                makeProfileHtml(allData.studentRec);
                $('#student_status').val(allData.studentRec.sai_student_status);
                $('#sessionId').val(163);
                $('#student_type').val('Existing');
                $('#feeType').val('Full_semester').trigger('change');
                document.getElementById('filterChallanBtn').style.display = 'block';
                $('#showDataInTable').html(tableHTML);
                $('#dataTable').DataTable({
                    pageLength: 25
                });

                $('#invoiceAmountOpt').hide();
                $('#scholarshipsAmountOpt').hide();
                // You can update the UI with the response data here
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle error response
                console.error(textStatus, errorThrown);
                $('#studentEnrollment-error').text('An error occurred while searching. Please try again.');
            }
        });
    }

    function makeProfileHtml(data) {
    let dataDiv = document.getElementById('profileDiv');
    dataDiv.innerHTML = ''; // Clear previous content

    if (data && data.usernameError) {
        // Show error if usernameError exists
        let errorHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            ${data.usernameError}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        dataDiv.innerHTML = errorHtml;
    } else if (data) {
        // Generate Profile Card

        $('#st_school_id').val(data.school_id);
         $('#st_dept_id').val(data.dept_id);
        $('#st_prog_id').val(data.prog_id);
        $('#st_aai_id').val(data.aai_id);


        const label = isNaN(data.username) ? 'Enrollment No:' : 'Applicant ID:';

        let imageUrl = data.api_imagename
            ? `https://admission.bnu.edu.pk/images/${data.api_imagename}`
            : `https://cms.bnu.edu.pk/students_pics/${data.username.toUpperCase()}.jpg`;

        let profileHtml = `
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
                        <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${data.spi_name || 'N/A'}</h6>
                        <span class="badge bg-info text-white" style="font-size: 0.8rem;">${data.sai_student_status || 'N/A'}</span>
                    </div>

                    <!-- Information Section -->
                    <div class="col-md-10">
                        <div class="row">

                  ${generateInfoBox(
                    'fa-id-card',
                    'Enrollment No / Applicant ID:',
                    `${data.username && isNaN(data.username) ? data.username : 'N/A'} / ${data.aai_id && !isNaN(data.aai_id) ? data.aai_id : 'N/A'}`
                )}


                    ${generateInfoBox(
                            'fa-envelope',
                            'Email:',
                            !isNaN(data.username)
                                ? data.spi_email || 'N/A'
                                : `${data.username || 'N/A'}@bnu.edu.pk`
                            )}

                            ${generateInfoBox('fa-phone', 'Mobile:', data.spi_mobile || 'N/A')}
                            ${generateInfoBox('fa-building', 'School:', data.school_name || 'N/A')}
                            ${generateInfoBox('fa-university', 'Department:', data.dept_name || 'N/A')}
                            ${generateInfoBox('fa-book', 'Program:', data.prog_name || 'N/A')}

                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        dataDiv.innerHTML = profileHtml;
    } else {
        // Show a generic error message if data does not meet the criteria
        let noDataHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            No data available
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        dataDiv.innerHTML = noDataHtml;
    }
}

// Helper Function to Generate Compact Info Boxes
function generateInfoBox(iconClass, label, value) {
    return `
    <div class="col-md-4 mb-3">
        <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
            <i class="fa ${iconClass} mr-3 text-muted" style="font-size: 1.2rem;"></i>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem;">${label}</span>
                <strong style="font-size: 0.9rem;">${value}</strong>
            </div>
        </div>
    </div>`;
}

function clearFormAndContent() {
        // Reset the form
        document.getElementById('singleChallanForm').reset();

        // Clear the content of profileDiv
        let dataDiv = document.getElementById('profileDiv');
        let showDataInTable = document.getElementById('showDataInTable');
        dataDiv.innerHTML = '';
        showDataInTable.innerHTML = '';
        window.location.reload();
    }

    function parseInputValue(id) {
    let value = document.getElementById(id).value;
    let parsedValue = parseFloat(value);
    return isNaN(parsedValue) ? 0 : parsedValue;
}

function calculateAndDisplayInstallments(selectElement) {
    // Check if the student enrollment field is empty
    var studentEnrollment = document.getElementById('studentEnrollment').value.trim();
    if (!studentEnrollment) {
        alert("Kindly enter the enrollment number and search for the student first.");
        return;
    }

    var numInstallments = parseInt(selectElement.value);
    var installmentsDiv = document.getElementById('installmentsDiv');

    if (!numInstallments) {
        installmentsDiv.innerHTML = '';
        return;
    }

    // Get input values and parse them to numbers
    let tuitionFee = parseInputValue('tuitionFee');
    let admissionFee = parseInputValue('admissionFee');
    let securityFee = parseInputValue('securityFee');
    let medicalFee = parseInputValue('medicalFee');
    let activityCharges = parseInputValue('activitychargesAmt');
    let studentInvoiceamount = parseInputValue('studentInvoiceamount');

    // Calculate total invoice amount
    if (!studentInvoiceamount) {
        studentInvoiceamount = tuitionFee;
    }

    var invoiceAmount = studentInvoiceamount - (admissionFee + securityFee + medicalFee + activityCharges);

    // Calculate installment amount
    var tuitionPerInstallment = Math.ceil(invoiceAmount / numInstallments);

    // Get today's date
    var today = new Date();

    // Display results
    var installmentsDiv = document.getElementById('installmentsDiv');
    installmentsDiv.innerHTML = ''; // Clear previous content

    var tableHtml = `
        <table class="table table-bordered table-striped table-hover w-100 border border-info p-2">
             <thead class="font-weight-bold"
             style="background-color: #C7C8CC;">
                <tr>
                    <th>Installment</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Validate Date</th>
                </tr>
            </thead>
            <tbody>
    `;

    for (var i = 0; i < numInstallments; i++) {
        var installmentAmount = i === 0
            ? tuitionPerInstallment + admissionFee + securityFee + medicalFee + activityCharges
            : tuitionPerInstallment;

        // Calculate the due date and validate date
        var dueDate = new Date(today);
        dueDate.setMonth(dueDate.getMonth() + i); // Due date is one month apart for each installment

        var validateDate = new Date(dueDate);
        validateDate.setDate(dueDate.getDate() + 7); // Validate date is one week after the due date

        // Format dates to YYYY-MM-DD
        var dueDateFormatted = dueDate.toISOString().split('T')[0];
        var validateDateFormatted = validateDate.toISOString().split('T')[0];

        tableHtml += `
            <tr>
                <td>Installment ${i + 1}</td>
                <td>
                    <input type="text" readonly class="form-control" value="${installmentAmount}" oninput="validateInput(this)">
                </td>
                <td>
                    <input type="date" class="form-control" name="installmentDueDate[]" value="${dueDateFormatted}" >
                    <span id="dueDate-error-${i}" class="error text-danger"></span>
                </td>
                <td>
                    <input type="date" class="form-control" name="installmentValidateDate[]" value="${validateDateFormatted}" >
                    <span id="validateDate-error-${i}" class="error text-danger"></span>
                </td>
            </tr>
        `;
    }

    tableHtml += `
            <tr>
                <td colspan="3"><strong>Total Amount Payable</strong></td>
                <td>
                    <input type="hidden" name="invoiceAmount" value="${studentInvoiceamount}">
                    <strong>${studentInvoiceamount}</strong>
                </td>
            </tr>
            </tbody>
        </table>
    `;

    installmentsDiv.innerHTML = tableHtml;
}


function validateDateFields() {
    // Check if all date fields are filled
    const dueDates = document.getElementsByName('installmentDueDate[]');
    const validateDates = document.getElementsByName('installmentValidateDate[]');
    let allFieldsFilled = true;

    for (let i = 0; i < dueDates.length; i++) {
        const dueDateError = document.getElementById(`dueDate-error-${i}`);
        const validateDateError = document.getElementById(`validateDate-error-${i}`);

        if (dueDates[i].value === '') {
            dueDateError.textContent = 'Due date is required.';
            allFieldsFilled = false;
        } else {
            dueDateError.textContent = '';
        }

        if (validateDates[i].value === '') {
            validateDateError.textContent = 'Validate date is required.';
            allFieldsFilled = false;
        } else {
            validateDateError.textContent = '';
        }
    }

    return allFieldsFilled; // Return true if all fields are filled, false otherwise
}


function setAttributes(attributeName, value) {
    const inputFieldIds = [
        '#studentInvoiceamount',
        '#scholarshipsAmount',
        '#activitychargesAmt',
        '#tuitionFee',
        '#admissionFee',
        '#securityFee',
        '#medicalFee',
        '#dueDate',
        '#validateDate'
    ];

    const selectFieldIds = [
        '#sessionId',
        '#student_type'
    ];

    // Apply readonly attribute to input fields
    inputFieldIds.forEach(fieldId => {
        if (value) {
            $(fieldId).attr('readonly', true);
        } else {
            $(fieldId).removeAttr('readonly');
        }
    });

    // Make select fields behave as readonly
    selectFieldIds.forEach(fieldId => {
        if (value) {
            $(fieldId).on('focus', function() {
                $(this).data('previous', $(this).val());
            }).on('change', function() {
                $(this).val($(this).data('previous'));
            });
        } else {
            $(fieldId).off('focus change');
        }
    });
    }

    // Call the function with 'readonly' attribute
    function applyReadonly() {
        setAttributes(true);
    }


function getFeeStructure(selectElement) {
    // let selectedValue = selectElement.value;
    let selectedValue = $('#feeType').val();
    let sess_type_val =  $('#sess_type').val();

    // console.log(selectedValue);
    if(selectedValue == 'Credit_hours'){
       $('.credit_hr_div').removeClass('d-none');
       $('#full_semester_dev').hide();
        // Add readonly attribute back to the fields
        $('#tuitionFee').attr('readonly', true);
        $('#medicalFee').attr('readonly', true);
        $('#admissionFee').attr('readonly', true);
        $('#activitychargesAmt').attr('readonly', true);
        $('#securityFee').attr('readonly', true);
    }else if(selectedValue == 'Fee_Refund'){
        $('#full_semester_dev').show();
        $('#tuitionFee').val('');
        $('#medicalFee').val('');
        $('#admissionFee').val('');
        $('#activitychargesAmt').val('');
        $('#securityFee').val('');

          // Remove readonly attribute from the fields
        $('#tuitionFee').removeAttr('readonly');
        $('#medicalFee').removeAttr('readonly');
        $('#admissionFee').removeAttr('readonly');
        $('#activitychargesAmt').removeAttr('readonly');
        $('#securityFee').removeAttr('readonly');

        $('.credit_hr_div').addClass('d-none');
    }else{
         // Add readonly attribute back to the fields
        // $('#tuitionFee').attr('readonly', true);
        // $('#medicalFee').attr('readonly', true);
        // $('#admissionFee').attr('readonly', true);
        // $('#activitychargesAmt').attr('readonly', true);
        // $('#securityFee').attr('readonly', true);
        $('#full_semester_dev').hide();
        $('.credit_hr_div').addClass('d-none');
    }
    let feeType = selectedValue === 'Hostel_day_wise' || selectedValue === 'Hostel_Full_semster' || selectedValue === 'Hostel_other_utilities' ? 'Hostel' : selectedValue;
    let sess_type = selectedValue === 'Hostel_day_wise' || selectedValue === 'Hostel_Full_semster' || selectedValue === 'Hostel_other_utilities' ? sess_type_val : '';

    var successMsg = $('#successMsg');
    // Clear any existing message
    successMsg.html('');
    // Retrieve other form data
    let school_id = $('#st_school_id').val();
    let dept_id = $('#st_dept_id').val();
    let prog_id = $('#st_prog_id').val();
    let sess_id = $('#sessionId').val();
    let student_type = $('#student_type').val();

    // Create the data object to be sent with the request
    let requestData = {
        fee_type: feeType,
        fee_type_for_hostel: selectedValue,
        school_id: school_id,
        dept_id: dept_id,
        prog_id: prog_id,
        sess_id: sess_id,
        student_type: student_type,
        sess_type: sess_type
    };
    // console.log(requestData);
    // Send the AJAX request
    $.ajax({
        url: '/finance/student-get-fee-structure',
        type: 'GET',
        data: requestData,
        success: function(response) {

            if (response.data && response.status === 'success') {
                let rowData = response.data;
            //    console.log();
                setDefaultDates();

                hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv','hostelRemarksDiv','hostel_security_feeDiv', 'refrigeratorFeeDiv','electricFeeDiv','conditionerFeeDiv','daysNoDiv','hostel-sess-typeDiv']);

            // Conditional logic based on feeType
            if ($('#feeType').val() === 'Hostel_Full_semster') {

                showElementsById(['hostelFeeDiv', 'hostel_security_feeDiv', 'hostel-sess-typeDiv']);
                $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

            } else if ($('#feeType').val() === 'Hostel_day_wise') {

                showElementsById(['hostel_per_dayDiv', 'daysNoDiv','hostelRemarksDiv', 'hostel-sess-typeDiv']);
                $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

            } else if ($('#feeType').val() === 'Hostel_other_utilities') {

                showElementsById(['refrigeratorFeeDiv', 'conditionerFeeDiv', 'electricFeeDiv', 'hostel-sess-typeDiv']);
                $('#sess_type').val(response.sess_type || 'Regular');//If response.sess_type is falsy (i.e. undefined, null, '', 0, false), it instead sets the value to an Regular because we are doing same in controller (getting regular Fee Structure is empty is passsed at the first attempt).

            } else {
                // Optional: Handle case where feeType doesn't match any of the above
                //console.log('No valid feeType found');
            }




                if (feeType==='Full_semester' || feeType==='Short/Summer_Course'){

                $('#full_semester_dev').show();
                // $('#hostel_dev').hide();
                // Populate the fields with the retrieved data
                $('#tuitionFee').val(rowData.ffs_tutionfee_amt || '');
                $('#medicalFee').val(rowData.ffs_medical_amt || '');
                $('#admissionFee').val(rowData.ffs_admission_amt || '');
                $('#activitychargesAmt').val(rowData.ffs_activity_amt || '');
                $('#securityFee').val(rowData.ffs_security_amt || '');
                $('#noOfInstallments').closest('.form-group').hide();

              // $('#noOfInstallments').closest('.form-group').show();

            //    if ($('#noOfInstallments').closest('.form-group').is(':visible')) {
            // }

            //document.getElementById('noOfInstallmentsDiv').style.setProperty('display', 'none', 'important');
            //$('#noOfInstallments').closest('.form-group').show();

                }else{
                    $('#full_semester_dev').hide();
                    // $('#hostel_dev').show();

                $('#hostelFee').val(rowData.fhfs_amount_per_semester || '');
                $('#hostel_per_day').val(rowData.fhfs_amount_per_day || '');
                $('#refrigeratorFee').val(rowData.fhfs_refrigerator_fee || '');
                $('#electricFee').val(rowData.fhfs_heater_fee || '');
                $('#conditionerFee').val(rowData.fhfs_ac_fee || '');
                //$('#daysNo').val(rowData.ffs_security_amt || '');
                $('#hostel_security_fee').val(rowData.fhfs_hostel_security_fee || '');
                $('#scholarshipsAmountOpt').hide();
                // hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv', 'refrigeratorFeeDiv','electricFeeDiv','conditionerFeeDiv','daysNoDiv']);
                }

            } else {
                hideElementsById(['hostelFeeDiv', 'hostel_per_dayDiv','hostelRemarksDiv','hostel_security_feeDiv', 'refrigeratorFeeDiv','electricFeeDiv','conditionerFeeDiv','daysNoDiv','hostel-sess-typeDiv']);

                // Clear the form fields and show appropriate messages
                $('#tuitionFee').val('');
                $('#medicalFee').val('');
                $('#admissionFee').val('');
                $('#activitychargesAmt').val('');
                $('#securityFee').val('');
                successMsg.html(createAlert('error', response.message));
                        setTimeout(function() {
                        successMsg.html('');
                    }, 4000);
                console.warn('No data found or error in response');
            }
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error('AJAX Error:', status, error);

            // Clear the form fields
            $('#tuitionFee').val('');
            $('#medicalFee').val('');
            $('#admissionFee').val('');
            $('#activitychargesAmt').val('');
            $('#securityFee').val('');
        }
    });
}



function scrollToInstallments() {
    // Scroll to the #noOfInstallments element with a 100px offset
    $('html, body').animate({
        scrollTop: $('#installmentFocus').offset().top - 100 // Adjust the offset as needed
    }, 1200, function () {
        // Add a focused border after the scroll completes
        $('#installmentFocus').addClass('focused-border');

        // Remove the border after 3 seconds
        setTimeout(() => {
            $('#installmentFocus').removeClass('focused-border');
        }, 9000);
    });
}


// Function to hide elements based on the passed IDs (array of IDs)
function hideElementsById(idsArray) {
    // Loop through each ID in the array
    idsArray.forEach(function(id) {
        var element = document.getElementById(id);
        if (element) {
            element.style.display = 'none';  // Hide the element
        }
    });
}

// Function to show elements based on the passed IDs (array of IDs)
function showElementsById(idsArray) {
    // Loop through each ID in the array
    idsArray.forEach(function(id) {
        var element = document.getElementById(id);
        if (element) {
            element.style.display = 'block';  // Show the element
        }
    });
}
function validateRemarks(input) {
    const errorSpan = document.getElementById('hostelRemarks-error');
    const maxLength = 200;

    if (input.value.trim().length === 0) {
        errorSpan.textContent = "Remarks cannot be empty.";
        input.classList.add('is-invalid');
    } else if (input.value.length > maxLength) {
        errorSpan.textContent = `Remarks cannot exceed ${maxLength} characters.`;
        input.classList.add('is-invalid');
    } else {
        errorSpan.textContent = "";
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
}

</script>
@stop
