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
    .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
    }
    #filterChallan {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    #unpaidCheckbox {
        margin: 0;
        transform: scale(1.2); /* Adjust size for better visibility */
    }

    #filterChallan label {
        margin: 0;
        font-size: 14px; /* Adjust the label text size */
    }
    .dropdown-menu.show {
        display: block;
        position: absolute;
        transform: translate3d(0, 38px, 0);
    }
    #studentTypeOption option:disabled {
        background: #e0e0e0 !important;
        color: #777 !important;
    }
    /* Modal container */
    .custom-modal {
        border-radius: 8px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 25px rgba(0,0,0,0.25);
    }

    /* Header shaded strip */
    .custom-modal-header {
        background: #4454c3;
        padding: 15px 20px;
        border-bottom: 1px solid #ccc;
    }

    /* Header title */
    .custom-modal-header .modal-title {
        font-weight: 600;
        color: #ffff;
    }

    /* Close button styling */
    .custom-close {
        font-size: 28px;
        color: #333 !important;
        opacity: 0.7;
    }
    .custom-close:hover {
        opacity: 1;
    }

    /* Modal body spacing */
    .custom-modal .modal-body {
        padding: 25px;
    }

    /* Buttons spacing & shape */
    .custom-modal .btn {
        border-radius: 4px;
    }

    /* Make secondary button slightly shaded */
    .btn-secondary {
        background-color: #e5e5e5;
        color: #444;
    }
    .btn-secondary:hover {
        background-color: #d2d2d2;
    }

    /* File input full-width look */
    .custom-modal input[type="file"] {
        padding: 8px;
        border: 1px solid #ccc;
        background: #fafafa;
    }

    /* Danger alert improved */
    #error-message-1 {
        border-radius: 4px;
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


</style>
@section('content')
<div id="success-flag" class="success-flag"></div>

<div class="card" style="padding:40px;">
    <form id="challanForm" method="POST" action="">
        @csrf
            <div class="row"><h3>Convocation and Gown Fee Challan Generation:</h3></div>

            <div class="row filter-row" style="margin-right:10px;">
                <div class="form-group col-sm-3">
                    <label for="session_sel">Session</label>
                    <select name="session" id="session_sel" class="form-control">
                        {{-- <option value="">Select Session</option> --}}
                        @foreach ($sessions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-4">
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
                <div class="form-group col-sm-4">
                    <label for="department">Department</label>
                    <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="course">Degree Level</label>
                    <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <label for="program">Program</label>
                    <select name="program" disabled id="program" class="form-control">
                    </select>
                </div>
                {{-- <div class="form-group col-sm-2 d-none">
                    <label for="scholrshipType">Scholarship Type</label>
                    <select name="scholrshipType" id="scholrshipType" class="form-control">
                            <option value="">--Select Scholarship--</option>
                        @foreach ($recScholarship_Type as $row)
                            <option value="{{$row->scholarship_id}}">{{ $row->scholarship_name }}</option>
                        @endforeach
                    </select>
                </div> --}}
                {{-- <div class="form-group col-sm-2 d-none">
                    <label for="studentType">Student Type</label>
                    <select name="student_type" id="studentTypeOption" class="form-control">
                        <option value="Existing">Existing</option>
                        <option value="New without Registration FALL">New without Registration FALL</option>
                        <option value="New without Registration SPRING">New without Registration SPRING</option>
                        <option value="New without Registration SUMMER">New without Registration SUMMER</option>
                        <option value="New Admission FALL">New Admission FALL</option>
                        <option value="New Admission SPRING">New Admission SPRING</option>
                        <option value="New Admission SUMMER">New Admission SUMMER</option>
                    </select>
                </div> --}}
                {{-- <div class="form-group col-sm-2 d-none">
                    <label for="studentType">Fee Type</label>
                    <select name="fee_type" id="feeType" class="form-control">
                        <option value="">Select fee type</option>
                        <option value="Credit_hours">Credit hour wise</option>
                        <option value="Credit_hours_Summer_Course">Credit hour wise Summer Course</option>
                    </select>
                </div> --}}
                {{-- <input type="text" hidden name="creditHourRange" value='' id="creditHourRange" class="form-control"> --}}

                {{-- <div class="form-group col-sm-3 d-none">
                    <label for="roadmap_session_sel">Road Map Session</label>
                    <select name="roadmap_session" id="roadmap_session_sel" class="form-control">
                        <option value="">Select Road Map Session</option>
                        @foreach ($sessions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="form-group col-sm-4">
                    <label for="studentStatus">Student Status</label>
                    <select name="student_status" id="student_status" class="form-control">
                        <option value="Expected-Graduate">Expected-Graduate</option>
                        <option value="Active">Active</option>
                    </select>
                </div>
            </div>
            <hr style="border-top: 1px solid #ccc;">

            <div class="row" style="margin-right:10px;">
                {{-- <div class="form-group col-sm-3 d-none">
                    <label for="semester_sel">Semester</label>
                    <select name="semester_sel" id="semester_sel" class="form-control">
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $key => $value)
                        <option value="{{$value->semester_id}}">{{ $value->semester_name }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="form-group col-sm-2">
                    <label for="amount">Challan Amount</label>
                    <input type="number" name="challan_amount" id="challan_amount" class="form-control">

                </div>
                <div class="form-group col-sm-2">
                     <label for="action_type">Action</label>
                    <select name="action_type" id="action_type" class="form-control">
                        <option value="create">Create</option>
                        <option value="edit">Edit</option>
                        <option value="cancel">Cancel</option>
                    </select>
                </div>
                <div class="form-group col-sm-5">
                    <label for="remarks">Remarks</label>
                    <input type="text" name="remarks" id="remarks" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-right:10px;">
                <div class="form-group col-sm-2">
                    <label for="studentType">Due Date</label>
                <input type="date" name="due_date" id="due-date" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                <div class="form-group col-sm-2">
                    <label for="studentType">Valid Date</label>
                <input type="date" name="valid_date" id="valid-date" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                <div class="form-group col-sm-2">
                    <label for="studentType">Visible Date</label>
                <input type="date" name="visible_date" id="visible-date" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
            </div>
            {{-- <div class="row" style="margin-right:10px;">
                <div class="form-group col-sm-6">
                    <label for="studentType">2nd Installment Due Date</label>
                <input type="date" name="second_installment_due_date" id="second-dueDate"  min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Valid">2nd Installment Valid Date</label>
                <input type="date" name="second_installment_valid_date" disabled id="second-validDate"  min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>

            </div>
            <div class="row" style="margin-right:10px;">
                <div class="form-group col-sm-6">
                    <label for="studentType">3rd Installment Due Date</label>
                <input type="date" name="third_installment_due_date" id="third-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Valid">3rd Installment Valid Date</label>
                <input type="date" name="third_installment_valid_date" disabled id="third-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                </div>
            </div> --}}
            <div class="form-group col-sm-12">
                <button type="button" id="saveButton" class="btn btn-primary"> <i class="fa fa-cogs"></i> Generate Bulk Challan</button>
                <div class="btn-group ml-2" role="group" id="filterBtnDiv">
                    <button type="button" id="filterButton" class="btn btn-outline-info filterbutton" onclick="filterStudents();">
                        <i class="fa fa-filter"></i>Filter Students
                    </button>


                    {{-- <div class="btn-group ml-2" role="group" aria-label="Filter Challan" id='filterBtn'>
                    <button type="button" class="btn btn-outline-info dropdown-toggle filterbutton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter"></i> Choose Filter
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" onclick="filterStudents()">Students</a>
                        <a class="dropdown-item" onclick="filterfeeAdjustments()">Surplus</a>
                    </div>
                </div> --}}
                {{-- <button type="button" id="filterChallan" class="btn btn-primary" style="margin-left: 10px;">Show Generated Challans</button> --}}
                <button type="button" id="filterChallan" class="btn btn-primary" style="margin-left: 10px;">
                    <span id="filterChallan-span">Show Generated Challans</span>
                    <div style="display: inline-flex; align-items: center; margin-left: 10px;">
                        <input type="checkbox" id="unpaidCheckbox" style="margin-right: 5px;">
                        <label for="unpaidCheckbox" style="margin: 0; color: white;">Unpaid</label>
                    </div>
                </button>
                <button type="button" style="width: 100px;margin-left:10px;" id="clear" class="btn btn-secondary" onclick="refreshFields()"><i class="fa fa-undo"></i> Clear</button>

            </div>
            <div class="col-xs-12">
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal-import"><i class="fa fa-upload"></i> Import Student Data </button>
                </div>
            <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>

    </form>

</div>


{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">
    <div class="row d-none filteredChallanData" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-tableChallan" class="table display" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col">Challan No</th>
                            <th scope="col">Enrollment No</th>
                            <th scope="col">Apply Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Program</th>
                            <th scope="col">Issue Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Valid Date</th>
                            <th scope="col">Invoice</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-none creditHrdData" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="credithr_data-table" class="table display" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col">Student Name</th>
                            <th scope="col">Enrollment No</th>
                            <th scope="col">Admission Apply Id</th>
                            <th scope="col">Session</th>
                            <th scope="col">Roadmap</th>
                            <th scope="col">Student Status</th>
                            <th scope="col">Allowed CreditHours</th>
                            <th scope="col">Earned CreditHours</th>
                            <th scope="col">Enrolled CreditHours</th>
                            <th scope="col">Repeat CreditHours</th>
                            <th scope="col">Current Semester</th>
                            <th scope="col">Allowed Semester</th>
                            <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" checked /></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row d-none filteredData" style="margin:10px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                <table id="data-table" class="table display" style="width:100%">
                    <thead>
                    <tr>
                        <th scope="col">Student Apply Id</th>
                        <th scope="col">Enrollment No</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Security</th>
                        <th scope="col">Program</th>
                        <th scope="col">Challan no</th>
                        <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" checked /></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1 -->
<div id="modal-import" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">

        <div class="modal-content custom-modal">

            <div class="modal-header custom-modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-upload"></i> Import Students Security Deposit Data
                </h4>
                <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="import-form" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Select Excel File:</label>
                        <input type="file" class="form-control" name="file_name" required>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" id="import-btn" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Import Data
                        </button>
                        <a href="{{ asset('public/assets/docs/Student-Data-Format.xlsx') }}"
                        class="btn btn-secondary" download>
                        <i class="fa fa-download"></i> Download Sample File
                        </a>
                    </div>
                </form>


                <div class="alert alert-danger alert-dismissible mt-3"
                     id="error-message-1" style="display:none">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <span></span>
                </div>

            </div>

        </div>

    </div>
</div>


<div style="clear:both;"></div>
@stop
@section('javascript')
<script>


document.getElementById('saveButton').addEventListener('click', function() {
    studentIdsArr = getIdsOfSelectedData();
    console.log(studentIdsArr.ids);
    console.log(studentIdsArr.main_ids);

    // console.log('studentaRR->'+studentIdsArr);
    if(studentIdsArr.ids != undefined){
        var form = document.getElementById("challanForm");
        var formData = new FormData(form);
        formData.append('session', $('#session_sel').val());
        formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('degree_level', $('#degree_level').val());
        formData.append('program', $('#program').val());
        //formData.append('student_type', $('#studentTypeOption').val());
        //formData.append('fee_type', $('#feeType').val());
        //formData.append('creditHourRange', $('#creditHourRange').val());
        formData.append('student_status', $('#student_status').val());
        formData.append('due_date', $('#due-date').val());
        formData.append('valid_date', $('#valid-date').val());
        formData.append('visible_date', $('#visible-date').val());
        //formData.append('second_installment_due_date', $('#second-dueDate').val());
        //formData.append('third_installment_due_date', $('#third-dueDate').val());
        //formData.append('second_installment_valid_date', $('#second-validDate').val());
        //formData.append('third_installment_valid_date', $('#third-validDate').val());
        formData.append('students', studentIdsArr.ids);
        formData.append('main_ids', studentIdsArr.main_ids);

        console.log(formData);
        $("#processing_message").show();
        fetch('add-convocation-challan', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // console.log('if: '+data);
                $("#processing_message").hide();
                $("#error_message").text('').hide();
                $('#success_message').html('Challans generated successfully!');
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(5000);
            } else {
                // console.log(data);
                var message = "";
                if ('messege' in data) {
                    message = data.messege;
                } else if ('errors' in data) {
                    var errors = data.errors;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(5000);
                filterStudents();
            }
        })
        .catch(error => {
            // console.log('error: '+error)
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    }
});
// $(document).on('click', '#filterStudents', function() {
//     let session_id =  $('#session_sel').val();
//     let school_id = $('#school_sel').val();
//     let department_id = $('#department').val();
//     let degree_level = $('#degree_level').val();
//     let program_id = $('#program').val();
//     let studentType = $('#studentTypeOption').val();
//     let feeType = $('#feeType').val();
//     let studentStatus = $('#student_status').val();
//     // console.log(studentType);
//     if(studentType == 'New without Registration'){
//         LoadStudentsWithoutRegistration(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus);
//     }else{
//         LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus);
//     }

// });

$(document).on('click', '#filterChallan-span', function() {

    let session_id =  $('#session_sel').val();
    let school_id = $('#school_sel').val();
    let department_id = $('#department').val();
    let degree_level = $('#degree_level').val();
    let program_id = $('#program').val();
    // let studentType = $('#studentTypeOption').val();
    // let feeType = $('#feeType').val();
    let studentStatus = $('#student_status').val();
    let unpaidCheckbox = $('#unpaidCheckbox').is(':checked') ? 1 : 0; // Check if checkbox is checked

    LoadChallan(session_id, school_id, department_id, program_id, degree_level, studentStatus, unpaidCheckbox);
});
$(document).on('change', '#second-dueDate', function() {
    // console.log($(this).val());
    $('#third-dueDate').attr('min',$(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#second-validDate').attr('min',$(this).val());
    $('#second-validDate').prop('disabled', false);
});

$(document).on('change', '#third-dueDate', function() {
    // console.log($(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#third-validDate').prop('disabled', false);
});

$(document).on('change', '#select-all', function() {
    if ($(this).prop("checked") == true) {
        $('.select-data').prop('checked', true);
        // console.log($('.select-data').val());

    } else {
        $('.select-data').prop('checked', false);
        // console.log($('.select-data').val());
    }
});
    var lastURL = '';


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
                            html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                            });
                            $('select[name="program"]').html(html); // Update "program" select
                        }

                        ////////
                        action_onAllDepartmentSelection(dept_id);
                        ///////

                    } else {
                        console.error('Error fetching programs:', res.message); // Log error message
                        // Optionally display an error message to the user
                    }

                },function(res){
                    alert('Some Error found!');
        });
        // console.log(url);
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
    // function getIdsOfSelectedData(){
    //     var student_ids = new Array();
    //    $.each($("input[name='student_ids[]']:checked"), function () {
    //     students.push({
    //         id: $(this).val(),
    //         creditHr: $(this).data('credithr')
    //         });
    //     });
    //     if (student_ids.length == 0) {
    //         alert("Please select atleast one student to generate challan.");
    //     } else {
    //         // console.log(student_ids);
    //         return student_ids;
    //     }
    // }
    function getIdsOfSelectedData() {
        var student_ids = [];
        var main_ids = [];

        $.each($("input[name='student_ids[]']:checked"), function () {
            let studentId = $(this).val();
            let id = $(this).data('main-id') || null;
            let appId = $(this).data('app-id') || null;
            let enrollment = $(this).data('enrollment') || null;

            student_ids.push(studentId);
            main_ids.push(id);

        });

        return {
            ids: student_ids,
            main_ids: main_ids
        };
    }



</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script>

function exportToPDF(tableId) {
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
//LoadCreditHr_students has same table as LoadStudentsWithoutRegistration and LoadStudents
// function LoadCreditHr_students(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType, students_credithr){
//     $('.filteredChallanData').addClass('d-none');
//     // $('.filteredSurplusData').addClass('d-none');
//     $('.filteredData').addClass('d-none');
//     $('.creditHrdData').removeClass('d-none');
//     $('#credithr_data-table').DataTable({
//         scrollX: true, // Enable horizontal scrolling
//         responsive:true,
//         responsive: true,
//        fixedHeader: {
//             header: true,
//             headerOffset: 70   // adjust if your page has a fixed navbar
//         },
//         scrollCollapse: false,
//         processing: true,
//         serverSide: false, //download all data
//         select: false,
//         searching: true,
//         destroy: true,
//         lengthChange: true,
//         paging: true,
//         // pageLength: 100,
//         lengthMenu: [ [50, 500, 1000, 5000], [50, 500, 1000, 5000] ],
//         order: [1, 'desc'],
//         ajax: {
//             dataType: 'json',
//             method: 'post',
//             url: "/api/generate-fee-challan/get_student_list",
//             data: function(d) {
//                 d.session_id = session_id;
//                 d.school_id = school_id;
//                 d.department_id = department_id;
//                 d.program_id = program_id;
//                 d.degree_level = degree_level;
//                 d.studentType = studentType;
//                 d.feeType = feeType;
//                 d.studentStatus = studentStatus;
//                 d.scholrshipType = scholrshipType;
//                 d.students_credithr = students_credithr;
//             }
//         },
//       columns: [
//             { data: "sai_id" },                       // Student Name
//             { data: "fscd_regno" },                     // Enrollment No
//             { data: "fscd_app_id" },                    // Admission Apply Id
//             { data: "enrollment_session" },             // Session
//             { data: "roadmap_session" },                // Roadmap
//             { data: "fscd_std_status" },                // Student Status
//             {
//                 data: "fscd_std_allowed_credits",       // Allowed Credit Hours
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             },
//             {
//                 data: "fscd_std_earned_credits",        // Earned Credit Hours
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             },
//             {
//                 data: "fscd_std_enrolled_credits",      // Enrolled Credit Hours
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             },
//             {
//                 data: "fscd_std_repeat_credits",        // Repeat Credit Hours
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             },
//             {
//                 data: "fscd_std_current_semester",      // Current Semester
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             },
//             {
//                 data: "fscd_std_allowed_semester",      // Allowed Semester
//                 render: function(data) {
//                     return data ? data : "NA";
//                 }
//             }
//         ],

//         columnDefs: [
//             // {
//             //     render: function (data, type, row) {
//             //         console.log(row);
//             //         var button = '<button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="editRow(`'+row["id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`)">Edit</button>'+
//             //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
//             //                                 '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows(['+row["id"]+'])">Delete</button>';
//             //         return button;

//             //     },
//             //     // className: 'dt-body-center',
//             //     // className: 'dt-head-center',
//             //     targets: 12,
//             // },
//             {
//                 render: function (data, type, row) {
//                         var   selectbtn = '<div><input name="student_ids[]" class="select-data" value='+row["sai_id"]+' data-main-id='+row["ssd_id"]+' data-enrollment='+row["sai_enrollment_no"]+' data-app-id='+row["aai_id"]+' type="checkbox" checked></div>';

//                     return selectbtn;

//                 },
//                 // className: 'dt-body-center',
//                 // className: 'dt-head-center',
//                 targets: 12,
//                 searchable: false,
//                 orderable: false
//             },

//             {
//                 "width": "100px", "targets": 1
//             }

//         ],
//          createdRow: function(row, data, dataIndex) {
//             // Apply background colors based on credit_flag
//             if (data.credit_flag === 'less_than') {
//                 $(row).css('background-color', '#ffe5e5'); // light red
//             } else if (data.credit_flag === 'greater_than') {
//                 $(row).css('background-color', '#e5f7e5'); // light green
//             } else if (data.credit_flag === 'in_between') {
//                 $(row).css('background-color', '#e5f1ff'); // light blue
//             }
//         },
//         autoWidth: false, // Ensures DataTable does not set its own width
//             dom: 'Blfrtip', // Adds the buttons to the DataTable
//             buttons: [
//                 {
//                     extend: 'excelHtml5',
//                     title: 'Challan to be generated students',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         exclude columns 12 and 13 (0-based index)
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'pdfHtml5',
//                     title: 'Challan to be generated students',
//                     orientation: 'landscape',
//                     pageSize: 'A4',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'print',
//                     title: 'Challan to be generated students',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 // {
//                 //     text: 'Export All Data (PDF)', // Custom button text
//                 //     action: function(e, dt, button, config) {
//                 //         // Call your custom function
//                 //         pdfExportFunction(dt, config);
//                 //     }
//                 // },
//             ]
//     });
// }
//LoadStudents has same table as LoadStudentsWithoutRegistration
function LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType, roadmap){
    $('.filteredChallanData').addClass('d-none');
    // $('.filteredSurplusData').addClass('d-none');
    $('.creditHrdData').addClass('d-none');
    $('.filteredData').removeClass('d-none');
    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false, //download all data
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        // pageLength: 100,
        lengthMenu: [ [50, 500, 1000, 5000], [50, 500, 1000, 5000] ],
        order: [1, 'desc'],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: "/api/get-securityDeposit-student-list",
            data: function(d) {
                d.session_id = session_id;
                d.school_id = school_id;
                d.department_id = department_id;
                d.program_id = program_id;
                d.degree_level = degree_level;
                d.studentType = studentType;
                d.feeType = feeType;
                d.studentStatus = studentStatus;
                d.scholrshipType = scholrshipType;
                d.roadmap = roadmap;
            }
        },
        columns: [
            {data: "aai_id"},
            {data: "sai_enrollment_no"},
            {data: "spi_name"},
            {
                data: "ssd_security_amount",
                render: function(data, type, row) {
                    return data == null ? "NA" : data;
                }
            },
            {data: "prog_name"},
            {
                data: "challan_id",
                render: function(data, type, row) {
                    return data == null ? "NA" : data;
                }
            },

        ],
        columnDefs: [
            // {
            //     render: function (data, type, row) {
            //         console.log(row);
            //         var button = '<button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="editRow(`'+row["id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`)">Edit</button>'+
            //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
            //                                 '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows(['+row["id"]+'])">Delete</button>';
            //         return button;

            //     },
            //     // className: 'dt-body-center',
            //     // className: 'dt-head-center',
            //     targets: 12,
            // },
            {
                render: function (data, type, row) {
                        var   selectbtn = '<div><input name="student_ids[]" class="select-data" value='+row["sai_id"]+' data-main-id='+row["ssd_id"]+' data-enrollment='+row["sai_enrollment_no"]+' data-app-id='+row["aai_id"]+'  type="checkbox" checked></div>';

                    return selectbtn;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 6,
                searchable: false,
                orderable: false
            },

            {
                "width": "500px", "targets": 4
            }

        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Challan to be generated students',
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
                    title: 'Challan to be generated students',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'print',
                    title: 'Challan to be generated students',
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
            ]
    });
}
//LoadStudentsWithoutRegistration has same table as LoadStudents
// function LoadStudentsWithoutRegistration(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType){
//     $('.filteredChallanData').addClass('d-none');
//     // $('.filteredSurplusData').addClass('d-none');
//     $('.creditHrdData').addClass('d-none');
//     $('.filteredData').removeClass('d-none');
//     $('#data-table').DataTable({
//         scrollX: true, // Enable horizontal scrolling
//         responsive:true,
//         responsive: true,
//         fixedHeader: false,
//         scrollCollapse: false,
//         processing: true,
//         serverSide: false, //download all data
//         select: false,
//         searching: true,
//         destroy: true,
//         lengthChange: true,
//         paging: true,
//         // pageLength: 100,
//         lengthMenu: [ [50, 500, 1000, 5000], [50, 500, 1000, 5000] ],
//         order: [1, 'desc'],
//         ajax: {
//             dataType: 'json',
//             method: 'post',
//             url: "/api/generate-fee-challan/get_student_list",
//             data: function(d) {
//                 d.session_id = session_id;
//                 d.school_id = school_id;
//                 d.department_id = department_id;
//                 d.program_id = program_id;
//                 d.degree_level = degree_level;
//                 d.studentType = studentType;
//                 d.feeType = feeType;
//                 d.studentStatus = studentStatus;
//                 d.scholrshipType = scholrshipType;
//             }
//         },
//         columns: [
//             {data: "api_name"},
//             {
//                 data: "cms_sai_enrollment_no",
//                 render: function(data, type, row) {
//                     return data == null ? "NA" : data;
//                 }
//             },
//             {data: "aai_id"},
//             {data: "aai_status"},
//             // {data: "sai_programid"},
//             // {data: "sai_degreecode"},
//             // {data: "sess_name"},
//             // {data: "school_name"},
//             // {data: "dept_name"},
//             {data: "prog_name"},
//             {
//                 data: "challan_count",
//                 render: function(data, type, row) {
//                     return data == 0 ? "No Challan" : "Challan Generated";
//                 }
//             }

//         ],
//         columnDefs: [
//             // {
//             //     render: function (data, type, row) {
//             //         console.log(row);
//             //         var button = '<button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="editRow(`'+row["id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`)">Edit</button>'+
//             //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
//             //                                 '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows(['+row["id"]+'])">Delete</button>';
//             //         return button;

//             //     },
//             //     // className: 'dt-body-center',
//             //     // className: 'dt-head-center',
//             //     targets: 12,
//             // },
//             {
//                 render: function (data, type, row) {
//                         var   selectbtn = '<div><input name="student_ids[]" class="select-data" value='+row["sai_id"]+' data-main-id='+row["ssd_id"]+' data-enrollment='+row["sai_enrollment_no"]+' data-app-id='+row["aai_id"]+'  type="checkbox" checked></div>';

//                     return selectbtn;

//                 },
//                 // className: 'dt-body-center',
//                 // className: 'dt-head-center',
//                 targets: 6,
//                 searchable: false,
//                 orderable: false
//             },

//             {
//                 "width": "100px", "targets": 1
//             }

//         ],
//         autoWidth: false, // Ensures DataTable does not set its own width
//             dom: 'Blfrtip', // Adds the buttons to the DataTable
//             buttons: [
//                 {
//                     extend: 'excelHtml5',
//                     title: 'Challan to be generated students',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         exclude columns 12 and 13 (0-based index)
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'pdfHtml5',
//                     title: 'Challan to be generated students',
//                     orientation: 'landscape',
//                     pageSize: 'A4',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'print',
//                     title: 'Challan to be generated students',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 // {
//                 //     text: 'Export All Data (PDF)', // Custom button text
//                 //     action: function(e, dt, button, config) {
//                 //         // Call your custom function
//                 //         pdfExportFunction(dt, config);
//                 //     }
//                 // },
//             ]
//     });
// }
//Show generated Challans
function LoadChallan(session_id, school_id, department_id, program_id, degree_level, studentStatus, unpaidCheckbox){
    $('.filteredData').addClass('d-none');
    $('.filteredSurplusData').addClass('d-none');
    $('.creditHrdData').addClass('d-none');
    $('.filteredChallanData').removeClass('d-none');
    $('#data-tableChallan').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: true,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        // pageLength: 10,
        lengthMenu: [ [100, 500, 1000,5000], [100, 500, 1000,5000] ],
        order: [1, 'desc'],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: "/api/generate-convocationfee-challan/get_challan_list",
            data: function(d) {
                d.session_id = session_id;
                d.school_id = school_id;
                d.department_id = department_id;
                d.program_id = program_id;
                d.degree_level = degree_level;
                d.studentStatus = studentStatus;
                d.unpaidCheckbox = unpaidCheckbox;
            }
        },

        columns: [
            {data: "id"},
            {data: "sc_enrollment_no"},
            {data: "sc_app_id"},
            {data: "sc_studentname"},
            // {data: "sess_name"},
            // {data: "school_name"},
            // {data: "dept_name"},
            {data: "prog_name"},
            {data: "sc_issu_date"},
            {data: "sc_due_date"},
            {data: "sc_valid_date"},
            {data: "invoiceamount"},
            {data: "sc_challan_status"}
        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    // console.log(row);

                    var button = '<button class="btn btn-primary btn-sm preview-btn" id="'+row["id"]+'" onclick="previewRow(`'+row["id"]+'`)"><i class="fa fa-eye" aria-hidden="true"></i> Preview</button>';

                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 10,
            },
            // {
            //     render: function (data, type, row) {
            //             var   selectbtn = '<div><input name="fee_ids[]" class="select-data" value='+row["sai_id"]+' type="checkbox" checked></div>';

            //         return selectbtn;

            //     },
            //     // className: 'dt-body-center',
            //     // className: 'dt-head-center',
            //     targets: 7,
            //     searchable: false,
            //     orderable: false
            // },
            {
                "width": "10px", "targets": 0
            },
            {
                "width": "10px", "targets": 1
            },
            {
                "width": "150px", "targets": 3
            },
            {
                "width": "150px", "targets": 4
            }
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Challans',
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
                    title: 'Challans',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'print',
                    title: 'Challans',
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
//Show surplus
// function LoadfeeAdjustedStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus){
//     $('.filteredChallanData').addClass('d-none');
//     $('.filteredData').addClass('d-none');
//     $('.creditHrdData').addClass('d-none');
//     $('.filteredSurplusData').removeClass('d-none');
//     $('#data-tableSurplusData').DataTable({
//         scrollX: true, // Enable horizontal scrolling
//         responsive:true,
//         responsive: true,
//         fixedHeader: false,
//         scrollCollapse: false,
//         processing: true,
//         serverSide: false, //download all data
//         select: false,
//         searching: true,
//         destroy: true,
//         lengthChange: true,
//         paging: true,
//         // pageLength: 100,
//         lengthMenu: [ [50, 500, 1000, 5000], [50, 500, 1000, 5000] ],
//         order: [1, 'desc'],
//         ajax: {
//             dataType: 'json',
//             method: 'post',
//             url: "/api/generate-fee-challan/get_feeAdjusted_list",
//             // data: function(d) {
//             //     d.session_id = session_id;
//             //     d.school_id = school_id;
//             //     d.department_id = department_id;
//             //     d.program_id = program_id;
//             //     d.degree_level = degree_level;
//             //     d.studentType = studentType;
//             //     d.feeType = feeType;
//             //     d.studentStatus = studentStatus;
//             // }
//         },
//         columns: [
//             {
//                 data: "ffa_sc_challan_no",
//                 render: function(data, type, row) {
//                     return data ? data : 'NA';
//                 }
//             },
//             {
//                 data: "ffa_enrollment_no",
//                 render: function(data, type, row) {
//                     return (data && data !== 'null') ? data : 'NA';
//                 }
//             },
//             {
//                 data: "ffa_app_id",
//                 render: function(data, type, row) {
//                     return (data && data !== 'null') ? data : 'NA';
//                 }
//             },
//             {data: "ffac_title"},
//             {data: "name"},
//             {data: "ffa_surplus_amount"},
//             {
//                 data: "ffa_amount_status",
//                 render: function(data, type, row) {
//                     if (data === 'Paid') {
//                         return `<span class="badge badge-success">${data}</span>`;
//                     }else if(data === 'Partial Paid'){
//                         return `<span class="badge badge-warning">${data}</span>`;
//                     }else{
//                         return `<span class="badge badge-danger">${data}</span>`;
//                     }
//                 }
//             },
//             {
//                 data: "ffa_remarks",
//                 render: function(data, type, row) {
//                     return data ? data : 'NA';
//                 }
//             },
//         ],
//         // columnDefs: [
//             // {
//             //     render: function (data, type, row) {
//             //         console.log(row);
//             //         var button = '<button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="editRow(`'+row["id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`)">Edit</button>'+
//             //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
//             //                                 '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows(['+row["id"]+'])">Delete</button>';
//             //         return button;

//             //     },
//             //     // className: 'dt-body-center',
//             //     // className: 'dt-head-center',
//             //     targets: 12,
//             // },
//             //     {
//             //         render: function (data, type, row) {
//             //                 var   selectbtn = '<div><input name="student_ids[]" class="select-data" value='+row["sai_id"]+' type="checkbox" checked></div>';

//             //             return selectbtn;

//             //         },
//             //         // className: 'dt-body-center',
//             //         // className: 'dt-head-center',
//             //         targets: 6,
//             //         searchable: false,
//             //         orderable: false
//             //     },

//             //     {
//             //         "width": "100px", "targets": 1
//             //     }

//             // ],
//         autoWidth: false, // Ensures DataTable does not set its own width
//             dom: 'Blfrtip', // Adds the buttons to the DataTable
//             buttons: [
//                 {
//                     extend: 'excelHtml5',
//                     title: 'Surplus',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         exclude columns 12 and 13 (0-based index)
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'pdfHtml5',
//                     title: 'Surplus',
//                     orientation: 'landscape',
//                     pageSize: 'A4',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 {
//                     extend: 'print',
//                     title: 'Surplus',
//                     // exportOptions: {
//                     //     columns: function (idx, data, node) {
//                     //         // Exclude columns by index
//                     //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
//                     //     }
//                     // }
//                 },
//                 //     {
//                 //     text: 'Export All Data (PDF)', // Custom button text
//                 //     action: function(e, dt, button, config) {
//                 //         // Call your custom function
//                 //         pdfExportFunction(dt, config);
//                 //     }
//                 // },
//             ]
//         });
// }

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
function previewRowold(challan_id) {
    var url = '/api/generate-fee-challan/print_challan?challanNo=' + challan_id;
    window.open(url, '_blank');
}
function previewRow(challan_id){


    var link = 'https://staff.bnu.edu.pk/chalanfin.php?challan_id='+challan_id;

    setTimeout(function(){
            $('.hidegif').hide();
            window.open(
                link,
                '_blank' // <- This is what makes it open in a new window.
                );
            // $('#pdfIcon').show();
        }, 1000);
    // var user_id = $("#staff_for_filter").val()
    //url = '/assets/cms/reports/online_payment_list.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate,
    // url = '/assets/cms/reports/online_payment_list.php',
    // window.open(url, '_blank');

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
                    console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

                if (degreeLevel === 'Postgraduate' || degreeLevel === 'Graduate' || degreeLevel === 'Doctorate' ) {

                    $('#studentTypeOption').val('Existing');

                    $('#studentTypeOption option').each(function () {
                        if ($(this).val() !== 'Existing') {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });

                } else {
                    // Enable all options back
                    $('#studentTypeOption option').prop('disabled', false);
                }


            },function(res){
                alert('Some Error found!');
    });
}
function refreshFields(){
    $('#session_sel').val('');
    $('#school_sel').val('');
    $('#studentTypeOption').val('Existing');
    $('#feeType').val('');
    $('#creditHourRange').val('');
    $('#student_status').val('');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');

    $('#due-date').val('');
    $('#valid-date').val('');
    $('#visible-date').val('');
    $('#second-dueDate').val('');
    $('#second-validDate').val('');
    $('#third-dueDate').val('');
    $('#third-validDate').val('');
}
function filterStudents() {
    // $('.filterbutton').text('Filter Students')
    // $('.filterbutton').html('<i class="fa fa-filter"></i> Filter <span class="filter-bubble">'+displayMsg+'</span>')
    // $('.dropdown-menu').removeClass('show');
    // $('#creditHourRange').val(students_credithr);
    let students_credithr='';

    let session_id =  $('#session_sel').val();
    let roadmap =  $('#roadmap_session_sel').val();
    let school_id = $('#school_sel').val();
    let department_id = $('#department').val();
    let degree_level = $('#degree_level').val();
    let program_id = $('#program').val();
    let scholrshipType = $('#scholrshipType').val();
    let studentType = $('#studentTypeOption').val();
    let feeType = $('#feeType').val();
    let studentStatus = $('#student_status').val();
    // console.log(feeType);
    // if(feeType == 'Credit_hours' || feeType =='Credit_hours_Summer_Course'){
    //     LoadCreditHr_students(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType, students_credithr);
    // }else{
    //     if(studentType.includes('New without Registration')){
    //         LoadStudentsWithoutRegistration(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType);
    //     }else{
            LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType, roadmap);
        // }
    // }
}
$(document).ready(function () {
    filterStudents();
    $("#import-form").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('import-student-security-data') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,

            beforeSend: function () {
                $("#import-btn").html('<i class="fa fa-spinner fa-spin"></i> Importing...');
                $("#import-btn").prop("disabled", true);
            },

            success: function (response) {
                $("#import-btn").prop("disabled", false).html('Import Data');

                // Bootstrap success alert
                $('#success-flag').text('Data Imported Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds

                // Close modal
                $("#modal-import").modal("hide");
                filterStudents();
                // Optional: reload table
                // table.ajax.reload();
            },

            error: function (xhr) {
                $("#import-btn").prop("disabled", false).html('Import Data');

                let msg = xhr.responseJSON?.message || "Something went wrong!";

                $("#import-alert").html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> ${msg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
            }
        });

    });


});

</script>

@stop

