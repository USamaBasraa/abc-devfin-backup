@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}


</head>
<style>
    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
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
    .dt-button {
    background-color: #4454c3;
    color: #4454c3;
    border: none;
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
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
    #clear{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear:hover {
    background-color: #1b82cb; /* New color on hover */
    border-color: #1b82cb;     /* Matching border color on hover */
    }
    .edit-btn, .delete-btn{
        width:76px;
    }

    /* ---- Interface polish (visual only) ---- */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 14px rgba(68, 84, 195, 0.08);
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #eef0f7;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .form-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.4rem;
        font-weight: 600;
        color: #2f3a8f;
        margin: 4px 0 18px 0;
    }
    .form-title i {
        background: #4454c3;
        color: #fff;
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .section-title {
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #8a90a8;
        margin-top: 12px;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #eef0f7;
    }
    .formDiv label,
    .row label {
        font-size: .82rem;
        font-weight: 600;
        color: #5b6178;
        margin-bottom: 4px;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e1e4f0;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .form-control:focus {
        border-color: #4454c3;
        box-shadow: 0 0 0 3px rgba(68, 84, 195, 0.12);
    }
    .btn {
        border-radius: 8px;
        font-weight: 600;
    }
    #saveButton, #filterButton {
        background-color: #4454c3;
        border-color: #4454c3;
    }
    #saveButton:hover, #filterButton:hover {
        background-color: #37449e;
        border-color: #37449e;
    }
    .action-bar {
        gap: 14px;
        flex-wrap: wrap;
        padding-top: 8px;
    }
</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card-header" style="width:100%;">
        <form id="feeForm" method="POST" action="">
            @csrf
                <div class="form-title"><i class="fa fa-money"></i> Master Fee Structure Form</div>

                <div class="row filter-row formDiv">
                    <div class="form-group col-sm-6">
                        <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                            <option value="">Select Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="school_sel">School</label>
                        <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                            @php
                                $allSchools = [];
                                foreach ($schools as $value) {
                                    $allSchools[] = $value['school_id'];
                                }
                            @endphp
                            <option value="">Select School</option>
                            <option value="[{{implode(',', $allSchools) }}]">All School</option>
                            @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="department">Department</label>
                        <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="course">Degree Level</label>
                        <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                        </select>
                    </div>

                    <div class="form-group col-sm-6">
                    <label for="course">Program</label>
                    <select name="program" disabled id="program" class="form-control">
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="roadmap_session_sel">Road Map Session</label>
                        <select name="roadmap_session" id="roadmap_session_sel" class="form-control">
                            <option value="">Select Road Map Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="semester_sel">Semester</label>
                        <select name="semester_sel" id="semester_sel" class="form-control">
                            <option value="">Select Semester</option>
                            @foreach ($semesters as $key => $value)
                            <option value="{{$value->semester_id}}">{{ $value->semester_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="studentType">Student Type</label>
                        <select name="student_type" id="studentTypeOption" class="form-control">
                            <option value="">Select type</option>
                            <option value="Existing">Existing</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="studentType">Fee Type</label>
                        <select name="fee_type" id="feeType" class="form-control">
                            <option value="">Select fee type</option>
                            <option value="Full_semester">Full Semester wise</option>
                            <option value="Credit_hours">Credit Hour wise</option>
                            <option value="Short/Summer_Course">Short / Summer Course</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="intake_from_sel">Intake From</label>
                        <select name="intake_from" id="intake_from_sel" class="form-control">
                            <option value="">Select Intake From</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="intake_to_sel">Intake To</label>
                        <select name="intake_to" id="intake_to_sel" class="form-control">
                            <option value="">Select Intake To</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6 d-none">
                        <label for="studentType">Effective From</label>
                        <input type="date" name="effective_from" id="eff-from" class="form-control">
                    </div>
                    <div class="form-group col-sm-6 d-none">
                            <label for="studentType">Effective To</label>
                        <input type="date" name="effective_to" id="eff-to" class="form-control">
                        </div>
                    </div>
                <div class="row filter-row formDiv">
                    <div class="col-sm-12 section-title">Fee Amounts</div>
                </div>
                <div class="row filter-row formDiv">
                    <div class="form-group col-sm-2 otherThanHostelDivs">
                        <label for="tutionAmt">Tuition Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="tution_amount" id="tutionAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 admissionFeeDiv d-none">
                        <label for="admissionAmt">Admission Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="admission_amount" id="admissionAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs securityAmtDiv">
                        <label for="securityAmt">Security Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="security_amount" id="securityAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs">
                        <label for="medicalAmt">Medical Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)"  name="medical_amount" id="medicalAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 otherThanHostelDivs">
                        <label for="activitycharges">Activity Charges</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)"  name="activity_charges_amount" id="activitychargesAmt" class="form-control otherThanHostelInputs">
                    </div>
                    <div class="form-group col-sm-2 credFeeDiv d-none">
                        <label for="credFee">Per Credit Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="per_credit_fee" id="credFee" class="form-control">
                    </div>
                    <div class="form-group col-sm-2 d-none hostelFeeDiv">
                        <label for="hostelFee">Hostel Fee</label>
                        <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="hostel_amount" id="hostelAmt" class="form-control">
                    </div>
                </div>

                <div class="form-group col-sm-12 action-bar" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    <button type="button" style="width: 150px;" id="filterButton" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filter</button>
                    <button type="button" style="width: 100px;" id="clear" class="btn btn-secondary" onclick="refreshFields()"><i class="fa fa-eraser"></i> Clear</button>
                </div>
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>


    </div>
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            <th scope="col">Session</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Program</th>
                            <th scope="col">Road Map Session</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Intake From</th>
                            <th scope="col">Intake To</th>
                            <th scope="col">Student Type</th>
                            <th scope="col">Fee Type</th>
                            <th scope="col">Tuition Fee</th>
                            <th scope="col">Admission Fee</th>
                            <th scope="col">Security Fee</th>
                            <th scope="col">Medical Fee</th>
                            <th scope="col">Per Credit Fee</th>
                            <th scope="col">Activity Charges</th>
                            {{-- <th scope="col">Hostel Fee</th> --}}
                            <th scope="col">Action</th>
                            <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" /></th>


                          </tr>
                        </thead>

                      </table>

                </div>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                  <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>




{{-- --------------------------- --}}






{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')






<script>

document.getElementById('saveButton').addEventListener('click', function() {
    var form = document.getElementById("feeForm");
    var formData = new FormData(form);
    // Re-assert the fee-type rules before reading the inputs, so an amount that
    // does not apply to the selected fee type is persisted as 0 rather than a
    // stale value left over from a previous selection.
    if (typeof applyFeeTypeVisibility === 'function') { applyFeeTypeVisibility(); }
    formData.append('school', $('#school_sel').val());
    formData.append('department', $('#department').val());
    formData.append('program', $('#program').val());
    formData.append('session', $('#session_sel').val());
    formData.append('roadmap_session', $('#roadmap_session_sel').val());
    formData.append('intake_from', $('#intake_from_sel').val());
    formData.append('intake_to', $('#intake_to_sel').val());
    formData.append('student_type', $('#studentTypeOption').val());
    formData.append('fee_type', $('#feeType').val());
    formData.append('effective_from', $('#eff-from').val());
    formData.append('effective_to', $('#eff-to').val());
    formData.append('tution_amount', $('#tutionAmt').val());
    formData.append('admission_amount', $('#admissionAmt').val());
    formData.append('security_amount', $('#securityAmt').val());
    formData.append('medical_amount', $('#medicalAmt').val());
    formData.append('activity_charges_amount', $('#activitychargesAmt').val());
    formData.append('per_credit_fee', $('#credFee').val());
    formData.append('hostel_amount', $('#hostelAmt').val());
    // console.log(formData);
    // console.log($('#school_sel').val());
    // console.log($('#department').val());
    // console.log($('#program').val());
    // console.log($('#session_sel').val());
    // console.log($('#studentTypeOption').val());
    // console.log($('#feeType').val());
    // console.log($('#eff-from').val());
    // console.log($('#eff-to').val());
    // console.log($('#tutionAmt').val());
    // console.log($('#admissionAmt').val());
    // console.log($('#securityAmt').val());
    // console.log($('#medicalAmt').val());
    // console.log($('#credFee').val());
    $("#processing_message").show();
    fetch('addFeeStructure', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())

    .then(data => {
        if (data.success) {
            refreshFields();
            $("#processing_message").hide();
            $("#error_message").text('').hide();
            $('#success_message').html(data.msg);
            $('#success_message').fadeIn('slow');
            $('#success_message').fadeOut(5000);
            LoadFee();
        } else {
            // console.log(data);
            var message = "";
            if ('msg' in data) {
                message = data.msg;
            } else if ('error' in data) {
                var errors = data.error;
                $.each(errors, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
            }
            $("#processing_message").hide();
            $('#success_message').text('').hide();
            $('#error_message').html(message);
            $('#error_message').fadeIn('slow');
            $('#error_message').fadeOut(5000);
        }
    })
    .catch(error => {
        $("#processing_message").hide();
        // console.error('There was an error!', error);
    });
});

    var lastURL = '';

// Single source of truth for which Fee Amount fields apply to the selected
// Fee Type. Hidden fields are zeroed so the DB never keeps a stale amount for
// a fee type that does not use it.
//
//   Credit_hours        -> Per Credit Fee editable. Medical Fee + Activity Charges
//                          EDITABLE — these two are the arrears cap for a Credit Hour
//                          scholarship (FIN_OTHER_SCHOLARSHIPS.FOS_CHALLAN_CATEGORY =
//                          'Credit_hours'). Tuition / Admission / Security are shown
//                          READONLY at 0 rather than hidden, so it is obvious they do
//                          not apply instead of leaving the user guessing.
//   Full_semester       -> Per Credit Fee hidden + 0. Others visible.
//   Short/Summer_Course -> EVERYTHING visible, nothing forced. A session can have
//                          two structures under this same fee type: one filled like
//                          a full semester (Per Credit Fee left empty), and one that
//                          is credit-hour based (only Per Credit Fee filled). The
//                          user distinguishes them by which fields they leave blank,
//                          so we must not hide or overwrite any amount here.
//   Hostel_fee          -> unchanged (Per Credit Fee hidden + cleared).
//
// Admission / Security stay governed by Student Type (Existing = not applicable).
function applyFeeTypeVisibility() {
    var feeType = $('#feeType').val();
    var isExisting = $('#studentTypeOption').val() === 'Existing';

    // Every fee type except Credit_hours has all amounts editable.
    setAmountsReadonly('#tutionAmt, #admissionAmt, #securityAmt, #medicalAmt, #activitychargesAmt', false);

    if (feeType === 'Hostel_fee') {
        $('.credFeeDiv').addClass('d-none');
        $('#credFee').val('');
        return;
    }

    if (feeType === 'Credit_hours') {
        $('.credFeeDiv').removeClass('d-none');
        // Show everything; Admission/Security no longer follow Student Type here,
        // because they must be visible-but-zero to make it clear they do not apply.
        $('.otherThanHostelDivs, .admissionFeeDiv, .securityAmtDiv').removeClass('d-none');
        // Not charged per credit hour -> forced to 0 and locked.
        $('#tutionAmt, #admissionAmt, #securityAmt').val(0);
        setAmountsReadonly('#tutionAmt, #admissionAmt, #securityAmt', true);
        // Medical + Activity ARE charged and form the arrears cap -> editable.
        setAmountsReadonly('#medicalAmt, #activitychargesAmt', false);
        return;
    }

    if (feeType === 'Short/Summer_Course') {
        // Both amount styles are valid — show all, force nothing.
        $('.credFeeDiv').removeClass('d-none');
        $('.otherThanHostelDivs').removeClass('d-none');
        showAdmissionSecurity(!isExisting);
        return;
    }

    // Full_semester (and the blank default).
    $('.credFeeDiv').addClass('d-none');
    $('#credFee').val(feeType ? 0 : '');
    $('.otherThanHostelDivs').removeClass('d-none');
    showAdmissionSecurity(!isExisting);
}

// readonly (NOT disabled): a disabled input is omitted from FormData, so the value
// would never reach the controller. readonly keeps it in the payload and locked.
function setAmountsReadonly(selector, readonly) {
    $(selector)
        .prop('readonly', readonly)
        .css('background-color', readonly ? '#e9ecef' : '');
}

// Admission Fee + Security Fee apply to new admissions only. Clearing on hide
// keeps a stale amount from being saved; the controller stores empty as 0.
function showAdmissionSecurity(show) {
    if (show) {
        $('.admissionFeeDiv, .securityAmtDiv').removeClass('d-none');
    } else {
        $('.admissionFeeDiv, .securityAmtDiv').addClass('d-none');
        $('#admissionAmt, #securityAmt').val('');
    }
}

$(document).ready(function(){
    LoadFee();
    $('#feeType').change(applyFeeTypeVisibility);
    // $('#feeType').change(function() {
    //     if($(this).val() == 'Hostel_fee'){
    //         $('.otherThanHostelDivs').addClass('d-none');
    //         $('.otherThanHostelInputs').val('');
    //         $('.credFeeDiv').addClass('d-none');
    //         $('#credFee').val('');
    //         $('.hostelFeeDiv').removeClass('d-none');
    //     }else if($(this).val() == 'Credit_hours'){
    //         $('.credFeeDiv').removeClass('d-none');
    //         $('.otherThanHostelDivs').removeClass('d-none');
    //         $('.hostelFeeDiv').addClass('d-none');
    //         $('.hostelAmt').val('');
    //     }else{
    //         $('.credFeeDiv').addClass('d-none');
    //         $('#credFee').val('');
    //         $('.hostelFeeDiv').addClass('d-none');
    //         $('.hostelAmt').val('');
    //         $('.otherThanHostelDivs').removeClass('d-none');

    //     }
    // });
    // Re-run the full rule set: under Credit_hours the Admission / Security
    // fields must stay hidden regardless of Student Type.
    $('#studentTypeOption').change(applyFeeTypeVisibility);
    $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', true);
        }
    });
    $(document).on('click', '#bulk-Del', function() {
        var fee_ids = new Array();
        $.each($("input[name='fee_ids[]']:checked"), function () {
            fee_ids.push($(this).val());
        });
        if (fee_ids.length == 0) {
            alert("Please select atleast one row to delete.");
        } else {
            // console.log(fee_ids);
            deleteRows(fee_ids);
        }
    });
    $(document).on('click', '#filterButton', function() {
        LoadFee();
    });
});


</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->


<script>

function LoadFee(){
        let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data once
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
                url:  "/api/masterFeeStructure/get_masterFee_list",
                data: function(d) {
                    return $.extend({}, d, filterParams);
                }
            },
            columns: [
                {data: "sess_name", name: "sess_name"},
                {data: "school_name", name: "school_name"},
                {data: "dept_name", name: "dept_name"},
                {data: "prog_name", name: "prog_name"},
                {
                    data: "roadmap_session_name",
                    name: "roadmap_session_name",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {
                    data: "semester_name",
                    name: "semester_name",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {
                    data: "intake_from_name",
                    name: "intake_from_name",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {
                    data: "intake_to_name",
                    name: "intake_to_name",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {data: "ffs_student_type", name: "ffs_student_type"},
                {data: "ffs_fee_type", name: "ffs_fee_type"},
                {
                    data: "ffs_tutionfee_amt",
                    name: "ffs_tutionfee_amt",

                },
                {
                    data: "ffs_admission_amt",
                    name: "ffs_admission_amt",

                },
                {
                    data: "ffs_security_amt",
                    name: "ffs_security_amt",

                },
                {
                    data: "ffs_medical_amt",
                    name: "ffs_medical_amt",

                },
                {
                    data: "ffs_credhr_amt",
                    name: "ffs_credhr_amt",

                },
                {
                    data: "ffs_activity_amt",
                    name: "ffs_activity_amt",

                },
                // {
                //     data: "ffs_hostel_amt",
                //     name: "ffs_hostel_amt",

                // },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        // console.log(row);
                        var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["ffs_id"]+'" onclick="editRow(`'+row["ffs_id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`,`'+row["ffs_activity_amt"]+'`,`'+row["ffs_degree_level"]+'`,`'+row["ffs_roadmap_session"]+'`,`'+row["ffs_hostel_amt"]+'`,`'+row["ffs_semester_no"]+'`,`'+row["ffs_intake_from_sess"]+'`,`'+row["ffs_intake_to_sess"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                                                    '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                                                '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffs_id"]+'])"><i class="fa fa-trash"></i> Delete</button></div>';
                        return button;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 16,
                },
                {
                    render: function (data, type, row) {
                            var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                        return selectbtn;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 17,
                    searchable: false,
                    orderable: false
                },

                {
                    "width": "100px", "targets": 1
                },
                {
                    "width": "5px", "targets": 17
                },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.roadmap_session_name) { // not null/empty
                    $(row).css("background-color", "rgb(252 229 205 / 50%)");
                }
            },
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Master Fee Structure',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Master Fee Structure',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }

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
                    title: 'Master Fee Structure',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                }
            ]
        });
}
function getRequest(endPoint,successBlock,failBlock){
    $.ajax({
        url: "{{url('/')}}"+endPoint,
        type: "GET",
        dataType: "json",
        success:successBlock
    }).fail(failBlock);
}
function getPrograms() {
    const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    const dept_id = $('#department').val();
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    let degreeLevels = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                        degreeLevels.push(value.prog_degree_level);
                    });
                    populateDegreeLevel(degreeLevels);
                    if(dept_id == ''){
                        let html = `<option value=''>Select Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "program" select
                    }else{
                        let html = `<option value=''>Select Program</option>`;
                        html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name}  &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
                    }
                    ////////
                    action_onAllDepartmentSelection(dept_id);
                    ///////

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
    // console.log(url);
    //   $.ajax({
    //     url: url,
    //     method: 'GET',
    //     success: function(response) {
    //       if (response.status) {
    //         let html = '<option value="">All Program</option>';
    //         $.each(response.data, function(key, value) {
    //           html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
    //         });
    //         $('select[name="course"]').html(html); // Update "course" select
    //       } else {
    //         console.error('Error fetching programs:', response.message); // Log error message
    //         // Optionally display an error message to the user
    //       }
    //     },
    //     error: function(response) {
    //       console.error('Error fetching programs:', response.statusText); // Log error details
    //       // Optionally display a generic error message to the user
    //     }
    //   });
}
function getDepartments(){
    var school_id = $('#school_sel').val();
    getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
        // console.log(res);
        if(res.status){
            let allDep = [];
            $.each(res.data, function(key, value) {
                allDep.push(value.dept_school_id+'~~'+value.dept_id);
            });
            if(school_id == ''){
                    let html = `<option value=''>Select Department</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
            }else{
                let html = `<option value=''>Select Department</option>`;
                html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
            /////////
            let htmlProg = `<option value=''>Select Program</option>`;
                $('select[name="program"]').html(htmlProg);
                htmlProg = `<option value=''>Select Degree Level</option>`;
                $('select[name="degree_level"]').html(htmlProg);
            action_onAllSchoolSelection(school_id);
            ////
        }
    },function(res){
        alert('Some Error found!');
    });
}
function on_session_change(session){
    // console.log(session.value);
}
function exportToPDF(tableId){
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    // console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
function editRow(mainId, session, school, dept, prog, studenttype, feetype, effFrom, effTo, tuitionFee, admissionFee, securityFee, medicalFee, credhrFee, activitychrges, degLevel, roadmap_session_sel, hostel_amt, semester_no, intakeFromSess, intakeToSess){

    // console.log('mianId->'+mainId);
    // console.log('session->'+session);
    // console.log('school->'+school);
    // console.log('dept->'+dept);
    // console.log('prog->'+prog);
    // console.log('studenttype->'+studenttype);
    // console.log('feetype->'+feetype);
    // console.log('effFrom->'+effFrom);
    // console.log('effTo->'+effTo);
    // console.log('tuitionFee->'+tuitionFee);
    // console.log('admissionFee->'+admissionFee);
    // console.log('securityFee->'+securityFee);
    // console.log('medicalFee->'+medicalFee);
    // console.log('credhrFee->'+credhrFee);

    $('#main-Id').val(mainId);
    $('#session_sel').val(session);
    if(roadmap_session_sel != 'null'){
        $('#roadmap_session_sel').val(roadmap_session_sel);
    }else{
        $('#roadmap_session_sel').val('');
    }
    if(semester_no != 'null'){
        $('#semester_sel').val(semester_no);
    }else{
        $('#semester_sel').val('');
    }
    if(intakeFromSess != 'null'){
        $('#intake_from_sel').val(intakeFromSess);
    }else{
        $('#intake_from_sel').val('');
    }
    if(intakeToSess != 'null'){
        $('#intake_to_sel').val(intakeToSess);
    }else{
        $('#intake_to_sel').val('');
    }

    $('#school_sel').val(school);
    $('#studentTypeOption').val(studenttype);
    $('#feeType').val(feetype);

    getDepartments();//this will set department dropdown menu
    setTimeout(function() {
        $('#department').val(dept);
        getPrograms();//this will set programs dropdown menu and degreelevel dropdown menu
        setTimeout(function() {
            $('#degree_level').val(degLevel);
            setProgramsAccordingly();
            setTimeout(function() {
                $('#program').val(school+'~~'+dept+'~~'+prog);
            }, 500);  // Adjust the delay as needed
        }, 500);  // Adjust the delay as needed
    }, 500);  // Adjust the delay as needed
    var eff_from_datePart = effFrom.split(' ')[0];
    $('#eff-from').val(eff_from_datePart);

    var eff_to_datePart = effTo.split(' ')[0];
    $('#eff-to').val(eff_to_datePart);

    $('#tutionAmt').val(tuitionFee);

    $('#medicalAmt').val(medicalFee);
    $('#hostelAmt').val(hostel_amt);
    $('#activitychargesAmt').val(activitychrges);

    if(studenttype != 'Existing'){
        $('#admissionAmt').val(admissionFee);
        $('#securityAmt').val(securityFee);
    }
    $('#credFee').val(credhrFee);

    // Populate first, then let the fee-type rules decide what is shown and
    // zero out whatever does not apply to this fee type.
    applyFeeTypeVisibility();

    $('html, body').animate({scrollTop: 0}, 'slow');

}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/masterFeeStructure/delete_feeData",
            data: {
                mainId: mainId
            },
            success: function(response) {
                $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadFee();
            },
            error: function(error) {
                // Handle the error
                // console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields(){
    $('#main-Id').val('');
    $('#session_sel').val('');
    $('#roadmap_session_sel').val('');
    $('#intake_from_sel').val('');
    $('#intake_to_sel').val('');
    $('#school_sel').val('');
    $('#studentTypeOption').val('');
    $('#feeType').val('');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');
    $('#eff-from').val('');
    $('#eff-to').val('');
    $('#tutionAmt').val('');
    $('#admissionAmt').val('');
    $('#securityAmt').val('');
    $('#medicalAmt').val('');
    $('#credFee').val('');
    $('#hostelAmt').val('');
    $('#activitychargesAmt').val('');
}
function action_onAllSchoolSelection(school_id){
    if(school_id != ''){
        school_ids_array = JSON.parse(school_id);
        if(Array.isArray(school_ids_array)){
            $('#department').prop('disabled', true);
            $('#program').prop('disabled', true);
            setTimeout(function() {
                $('#department').val($('#department option:nth-child(2)').val());
                getPrograms();
                setTimeout(function() {
                    $('#program').val($('#program option:nth-child(2)').val());
                }, 500);  // Adjust the delay as needed
            }, 500);  // Adjust the delay as needed
        }else{
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
        }
    }else{
        $('#department').prop('disabled', false);
        $('#program').prop('disabled', false);
    }

}
function action_onAllDepartmentSelection(dept_id){
    if(dept_id != ''){
        dept_ids_array = JSON.parse(dept_id);
        if(Array.isArray(dept_ids_array)){
            $('#program').prop('disabled', true);
            $('#program').val($('#program option:nth-child(2)').val());
        }else{
            $('#program').prop('disabled', false);
        }
    }else{
        $('#program').prop('disabled', false);
    }
    // console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    // console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    // console.log(distinctDegreeLevels);
    html='<option value="" >Select Degree Level</option>';
    $.each(distinctDegreeLevels,function( key, value ) {
                html += '<option value="'+value+'" >'+value+'</option>';
            });
    $('select[name="degree_level"]').html(html);
}
function setProgramsAccordingly(){
    let session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    let dept_id = $('#department').val();
    let degreeLevel = $('#degree_level').val();
    let degreeLevelwithArrow = ((degreeLevel != '')? '&#x2192;'+degreeLevel: '');
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&degreeLevel=${degreeLevel}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                    });
                    // let html = `<option value=''>Select Program</option>`;
                    html = `<option value='${JSON.stringify(allProg)}'>All Program `+degreeLevelwithArrow+`</option>`;
                    $.each(res.data, function(key, value) {
                    // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                    html += `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "course" select

                    $('#program option').filter(function() {
                        return $(this).text() === 'All Program';
                    }).prop('selected', true);
                    ////////
                    // action_onAllDepartmentSelection(dept_id);
                    ///////
                    // console.log($('#program').val());

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
function getFilterParams() {
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const intake_from = $('#intake_from_sel').val();
    const intake_to = $('#intake_to_sel').val();
    const studentTypeOption = $('#studentTypeOption').val();
    const feeType = $('#feeType').val();
    const department = $('#department').val();
    const degree_level = $('#degree_level').val();
    const program = $('#program').val();
    const tutionAmt = $('#tutionAmt').val();
    const admissionAmt = $('#admissionAmt').val();
    const securityAmt = $('#securityAmt').val();
    const medicalAmt = $('#medicalAmt').val();
    const credFee = $('#credFee').val();
    const activitychargesAmt = $('#activitychargesAmt').val();
    let filterParams =  {
        session_sel: session_sel,
        school_sel: school_sel,
        intake_from: intake_from,
        intake_to: intake_to,
        studentTypeOption: studentTypeOption,
        feeType: feeType,
        department: department,
        degree_level: degree_level,
        program: program,
        tutionAmt: tutionAmt,
        admissionAmt: admissionAmt,
        securityAmt: securityAmt,
        medicalAmt: medicalAmt,
        credFee: credFee,
        activitychargesAmt: activitychargesAmt
    };
    return filterParams;
}

</script>

@stop

