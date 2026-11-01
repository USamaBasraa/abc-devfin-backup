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
     /* Session card styles */
    .session-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 1rem;
    }

    .session-card:hover {
        transform: scale(1.02);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* General selected state */
    input[type="checkbox"]:checked+.session-card {
        border: 10px solid #0d6efd;
        background-color: #e7f1ff;
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.3);
        border-radius: 1rem;
    }

    /* Specific style for Before 2024 */
    input#before_2024:checked+.session-card {
        border-color: #074197;
        background-color: #e7f1ff;
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.3);
    }

    /* Specific style for After 2024 */
    input#after_2024:checked+.session-card {
        border-color: #198754;
        background-color: #e8f5e9;
        box-shadow: 0 0 15px rgba(25, 135, 84, 0.3);
    }

    /* Common tick styling for selected card */
    input[type="checkbox"]:checked+.session-card::after {
        content: "✔";
        position: absolute;
        top: 50%;
        left: 20px;
        /* move to left side */
        transform: translateY(-50%);
        font-size: 1.8rem;
        color: #28a745;
        /* decent green */
        font-weight: normal;
    }


    .session-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 120px;
        /* optional: ensure some height for visual centering */
        padding-left: 2.5rem;
        /* keep if using left tick */
    }
     .card .card-header form {
        width: 100%;
    }
    .after2024Fields {
        min-width: 100%;
    }
   .flag-tab {
        position: relative;
        background: #e9ecefa1;
        margin: 0 10px;
        border-radius: 0 0 10px 10px;
        font-weight: 500;
        color: #384372;
        padding: 10px 20px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .flag-tab:hover {
        background: #dee2e6;
        color: #000;
    }

   .flag-tab.active {
        background: #4454c3;
        color: #fff !important;
        font-weight: bold !important;
        font-size: 20px !important;
    }
    /* � Only active tab gets the arrow */
    .flag-tab.active::after {
        content: '';
        position: absolute;
        bottom: -14px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 15px 11px 0 11px;
        border-style: solid;
        border-color: #4454c3 transparent transparent transparent;
    }

    .flag-tab.active::after {
        border-color: #4454c3  transparent transparent transparent;
    }
</style>
@section('content')

<div class="card">
     <!-- Hanging Flag Tabs -->
   <ul class="nav nav-tabs card-header-tabs justify-content-center" style="background: #4454c3; border-bottom: none; margin-bottom:17px;">
        <li class="nav-item">
            <a class="nav-link flag-tab {{ request()->is('merit-scholarship-challan-handle') ? 'active' : '' }}"
            href="javascript:void(0)"
            onclick="switchTab('generate')">
            Generate Scholarship Challan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link flag-tab {{ request()->is('scholarship-challan-approval') ? 'active' : '' }}"
            href="javascript:void(0)"
            onclick="switchTab('approval')">
            Approve Scholarship Challan
            </a>
        </li>
    </ul>
    <div class="card-header" style="width:100%;">
        <form id="challanForm" method="POST" action="">
            @csrf
                {{-- <div class="row"><h3>Generate Scholarship Challan:</h3></div> --}}
                 <!-- Session Selection (Before/After 2024) -->
                <div class="row g-3 mb-4 justify-content-center">
                    <!-- Before 2024 Option -->
                    <div class="col-md-6">
                        <input type="checkbox" name="session_option" id="before_2024" value="before" class="d-none"
                            onchange="selectOnlyOne(this)">
                        <label for="before_2024" class="session-card card shadow-sm border rounded-4 text-center text-decoration-none">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-2">Before Session 2024</h5>
                                <p class="card-text mb-0">For students admitted before the 2024 session.</p>
                            </div>
                        </label>
                    </div>

                    <!-- After 2024 Option -->
                    <div class="col-md-6">
                        <input type="checkbox" name="session_option" id="after_2024" value="after" class="d-none"
                            onchange="selectOnlyOne(this)">
                        <label for="after_2024" class="session-card card shadow-sm border rounded-4 text-center text-decoration-none">
                            <div class="card-body">
                                <h5 class="card-title text-success mb-2">After Session 2024</h5>
                                <p class="card-text mb-0">For students admitted in 2024 and later.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="row filter-row" style="margin-right:10px;">
                    <div class="form-group col-sm-6">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            {{-- <option value="">Select Session</option> --}}
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
                        <label for="program">Program</label>
                        <select name="program" disabled id="program" class="form-control">
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="studentType">Student Type</label>
                        <select name="student_type" id="studentTypeOption"  onchange="onChangeStudentType(this)" class="form-control">
                            <option value="Existing">Existing</option>
                            <option value="New without Registration FALL">New without Registration FALL</option>
                            <option value="New without Registration SPRING">New without Registration SPRING</option>
                            <option value="New without Registration SUMMER">New without Registration SUMMER</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="studentStatus">Student Status</label>
                        <select name="student_status" id="student_status" class="form-control">
                            <option value="">Select status</option>
                            <option value="Active">Active</option>
                            <option value="Expected-Graduate">Expected Graduate</option>
                        </select>
                    </div>

                </div>
                <div class="row" style="margin-right:10px;">
                    <div class="form-group col-sm-4">
                        <label for="studentType">Due Date</label>
                    <input type="date" name="due_date" id="due-date" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="studentType">Valid Date</label>
                    <input type="date" name="valid_date" id="valid-date" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                    </div>


                    {{-- <div class="form-group col-sm-4">
                        <label for="studentType">Scholarship</label>
                    <select name="merit_base_scholarship" id="merit-base-scholarship" class="form-control">
                        <option value="">SelectScholarship</option>
                        <option value="merit_base_scholarship">Merit Base Scholarship</option>
                    </select>
                    </div> --}}

                </div>


                <div class="form-group col-sm-12">
                    <button type="button" id="saveButton" class="btn btn-primary"> <i class="fa fa-cogs"></i> Bulk Update Challan </button>
                    <div class="btn-group ml-2" role="group" aria-label="Filter Challan" id='filterBtn'>
                        <button type="button" class="btn btn-outline-info dropdown-toggle filterbutton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter"></i> Choose Filter
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" onclick="filterStudents()">Students</a>
                            {{-- <a class="dropdown-item" onclick="filterfeeAdjustments()">Surplus</a> --}}
                        </div>
                    </div>

                    <button type="button" style="width: 100px;margin-left:10px;" id="clear" class="btn btn-secondary" onclick="refreshFields()"><i class="fa fa-undo"></i> Clear</button>
                    {{-- <button type="button" onclick="getIdsOfSelectedData()" class="btn btn-primary">test checkbox</button> --}}
                </div>
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>

        </form>
    </div>
    <div class="row d-none filteredData" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col">Student Name</th>
                            <th scope="col">Enrollment No</th>
                            <th scope="col">Apply Id</th>
                            <th scope="col">Student Status</th>
                            <th scope="col">Program</th>
                            <th scope="col">CGPA</th>
                            <th scope="col">Scholarship Percentage</th>
                            <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" checked /></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
    document.getElementById('saveButton').addEventListener('click', function() {
        var studentData = getIdsOfSelectedData();
        if(studentData != undefined){
            var form = document.getElementById("challanForm");
            var formData = new FormData(form);
            formData.append('session', $('#session_sel').val());
            formData.append('school', $('#school_sel').val());
            formData.append('department', $('#department').val());
            formData.append('degree_level', $('#degree_level').val());
            formData.append('program', $('#program').val());
            formData.append('student_type', $('#studentTypeOption').val());
            formData.append('fee_type', $('#feeType').val());
            formData.append('student_status', $('#student_status').val());
            formData.append('due_date', $('#due-date').val());
            formData.append('valid_date', $('#valid-date').val());
            formData.append('visible_date', $('#visible-date').val());
            formData.append('second_installment_due_date', $('#second-dueDate').val());
            formData.append('third_installment_due_date', $('#third-dueDate').val());
            formData.append('second_installment_valid_date', $('#second-validDate').val());
            formData.append('third_installment_valid_date', $('#third-validDate').val());

            // Add student IDs and app IDs
            formData.append('students', JSON.stringify(studentData.map(s => s.id)));
            formData.append('app_ids', JSON.stringify(studentData.map(s => s.app_id)));

            $("#processing_message").show();
            fetch('submit-merit-scholarship-challan', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $("#processing_message").hide();
                    $("#error_message").text('').hide();
                    $('#success_message').html(data.message);
                    $('#success_message').fadeIn('slow');
                    $('#success_message').fadeOut(5000);
                    filterStudents();
                } else {
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
                }
            })
            .catch(error => {
                $("#processing_message").hide();
                console.error('There was an error!', error);
            });
        }
    });

    $(document).on('click', '#filterChallan-span', function() {

        let session_id =  $('#session_sel').val();
        let school_id = $('#school_sel').val();
        let department_id = $('#department').val();
        let degree_level = $('#degree_level').val();
        let program_id = $('#program').val();
        let studentType = $('#studentTypeOption').val();
        let feeType = $('#feeType').val();
        let studentStatus = $('#student_status').val();
        let unpaidCheckbox = $('#unpaidCheckbox').is(':checked') ? 1 : 0; // Check if checkbox is checked

        LoadChallan(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, unpaidCheckbox);
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
    function switchTab(tab) {
        if (tab === 'generate') {
            window.location.href = "{{ url('merit-scholarship-challan-handle') }}";
        } else if (tab === 'approval') {
            window.location.href = "{{ url('scholarship-challan-approval') }}";
        }
    }
    function selectOnlyOne(clicked) {
        // Uncheck all other checkboxes
        $('input[name="session_option"]').not(clicked).prop('checked', false);

        // Safely get the selected value (avoid undefined)
        let selectedValue = $('input[name="session_option"]:checked').val() || '';

        if (selectedValue) {
            // console.log("Selected session: " + selectedValue);
            handleSessionChange(selectedValue);
        } else {
            // console.log("No session selected");
        }
    }
   function onChangeStudentType(elem) {
        let selectedType = $(elem).val();  // use passed element instead of `this`
        // console.log(selectedType);

        if (selectedType && selectedType.includes("New without Registration")) {
            // alert(1);
            $("#student_status").val("").prop("disabled", true);
        } else {
            // alert(2);
            $("#student_status").val("").prop("disabled", false);
        }
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
    function getIdsOfSelectedData(){
        var student_data = [];
        $.each($("input[name='student_ids[]']:checked"), function () {
            var row = $(this).closest('tr');
            var app_id = row.find('td:eq(2)').text(); // Get Apply Id from the third column
            student_data.push({
                id: $(this).val(),
                app_id: app_id
            });
        });
        if (student_data.length == 0) {
            alert("Please select at least one student to generate challan.");
        } else {
            return student_data;
        }
    }


</script>
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

    //LoadStudents has same table as LoadStudentsWithoutRegistration
    function LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, cgpa, session_option){
        $('.filteredChallanData').addClass('d-none');
        $('.filteredSurplusData').addClass('d-none');
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
                method: 'get',
                url: "/get-student-meritscholarship-challan-approved",
                data: function(d) {
                    d.session_id = session_id;
                    d.school_id = school_id;
                    d.department_id = department_id;
                    d.program_id = program_id;
                    d.degree_level = degree_level;
                    d.studentType = studentType;
                    d.feeType = feeType;
                    d.studentStatus = studentStatus;
                    d.cgpa = cgpa;
                    d.session_option = session_option;
                }
            },
            columns: [
                {data: "student_name"},
                {
                    data: "enrollment_no",
                    render: function(data, type, row) {
                        return data == null ? "NA" : data;
                    }
                },
                {data: "aai_id"},
                {data: "student_status"},
                {data: "program_name"},
                {
                    data: "cal_cgpa",
                    render: function(data, type, row) {
                        return data == null ? "NA" : parseFloat(data).toFixed(2);
                    }
                },
                {data: "ssd_percentage"},
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        console.log(row.challan_approval_status);
                        if(row.challan_approval_status == 'No Challan'){
                            return '<div><input name="student_ids[]" class="select-data" value='+row["aai_id"]+' type="checkbox" checked></div>';
                        }else if(row.editable_flag == 'editable'){
                            return '<div style="display: flex; align-items: center; gap: 8px;">' +
                                        '<input name="student_ids[]" class="select-data" value="' + row["aai_id"] + '" type="checkbox" checked>' +
                                        '<span class="badge ' +
                                            (row.challan_approval_status == 'Approved' ? 'badge-success' : 'badge-info') + '">' +
                                            row.challan_approval_status +
                                        '</span>' +
                                    '</div>';

                        }else{
                        return '<span class="badge ' + (row.challan_approval_status == 'Approved' ? 'badge-success' : 'badge-info') + '">' + row.challan_approval_status + '</span>';

                        }
                    },
                    targets: 7,
                    searchable: false,
                    orderable: false
                },
                // {
                //     "width": "100px",
                //     "targets": [1, 2, 3, 4, 5]
                // }
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

                },function(res){
                    alert('Some Error found!');
        });
    }
    function refreshFields(){
        $('#session_sel').val('');
        $('#school_sel').val('');
        $('#studentTypeOption').val('Existing');
        $('#feeType').val('');
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
        $('.filterbutton').html('<i class="fa fa-filter"></i> Filter <span class="filter-bubble">Students</span>')
        let session_option = $('input[name="session_option"]:checked').val() || '';
        if(session_option == ''){
            alert('Please select a Session Range Before Or After');
            return;
        }
        let session_id =  $('#session_sel').val();
        let school_id = $('#school_sel').val();
        let department_id = $('#department').val();
        let degree_level = $('#degree_level').val();
        let program_id = $('#program').val();
        let studentType = $('#studentTypeOption').val();
        let feeType = $('#feeType').val();
        let studentStatus = $('#student_status').val();
        let cgpa = $('#cgpa').val();
        // console.log(studentType);
        LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus,cgpa, session_option);
    }

</script>

@stop

