@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')
{{-- @dd("123"); --}}

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Original Button Design with Blinking Border */
    .btn-blink {
        background-color: transparent;
        /* Original light blue background */
        border: 3px solid #4454c3;
        /* Blue border */
        cursor: pointer;
        /* Pointer on hover */
        animation: border-blink-animation 1s infinite;
        /* Blinking border animation */
        transition: all 0.3s ease;
        /* Smooth transition for hover effect */
    }

    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #4454c3;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
        background-color: #ffffff;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713';
        /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }

    /* Custom checkbox style */
    .approve-select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #4454c3;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
        background-color: #ffffff;
    }

    /* Checked state */
    .approve-select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .approve-select-data:checked::after {
        content: '\2713';
        /* Unicode checkmark symbol */
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

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        margin-top: 5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }

    /* Blinking Border Animation */
    @keyframes border-blink-animation {

        0%,
        100% {
            border-color: #4454c3;
            /* Original blue border */
        }

        50% {
            border-color: transparent;
            /* Border becomes transparent */
        }
    }
</style>


<div class="row mt-4" style="margin:5px;">
    <div class="col-lg-12 col-md-12d-flex justify-content-center align-items-center">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> Update Test Slip Date </h3>
            </div>
            <input type="hidden" id="session">
            <input type="hidden" id="school">
            <input type="hidden" id="department">
            <input type="hidden" id="program">
            <input type="hidden" id="admsch">
            <input type="hidden" id="category">
            <div class="card-body">
                <form id="messageForm" method="post">


                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label"> Session</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="facultyWload_sess_id" name="sess_id" style="width:400px;" placeholder="Sessions"
                                class="easyui-combobox form-control" data-options="
                                valueField:'sess_id',
                                textField:'sess_name',
                                required:'true',
                                url: '<?= url('/sessions') ?>',
                                method:'GET',
                                onSelect: function(rec){
                                    var urlschool = '<?= url('/activeschools') ?>?sess_id='+rec.sess_id;
                                    $('#facultyWload_school_id').combobox('clear');
                                    $('#facultyWload_school_id').val('');
                                    $('#department_id').combobox('clear');
                                    $('#department').val('');
                                    $('#program_id').combobox('clear');
                                    $('#program').val('');
                                    $('#facultyWload_school_id').combobox('reload', urlschool);
                                    $('#session').val(rec.sess_id);
                                    getData();
                                } ">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">School</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="facultyWload_school_id" name="school_id" style="width:400px;"
                                class="easyui-combobox form-control" data-options="
                                valueField: 'school_id',
                                textField: 'school_name',
                                required:'true',
                                method:'get',
                                onSelect: function(rec){
                                    var departmentURL = '<?= url('/activedepartments') ?>?school_id='+rec.school_id+'&sess_id='+$('#facultyWload_sess_id').combobox('getValue');
                                    $('#department_id').combobox('clear');
                                    $('#department').val('');
                                    $('#program_id').combobox('clear');
                                    $('#program').val('');
                                    $('#department_id').combobox('reload', departmentURL);
                                    $('#school').val(rec.school_id);
                                    getData();
                                }
                            ">
                        </div>
                    </div>

                    <!----------------------------------------   DEPARTMENT   ---------------------------------------->
                    <div class="form-group row " style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Department</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="department_id" name="department_id" style="width:400px;"
                                class="easyui-combobox form-control" data-options="
                            valueField: 'dept_id',
                            textField: 'dept_name',
                            required:'true',
                            method:'get',
                            url:'',
                            onSelect: function(rec){
                            var dept_ID = rec.dept_id
                            var progURL = '<?= url('/activeprograms') ?>?co_status=E&dept_id='+dept_ID+'&sel_sess_id='+$('#facultyWload_sess_id').combobox('getValue');
                            $('#program_id').combobox('clear');
                            $('#program').val('');
                            $('#program_id').combobox('reload', progURL);
                         
                             $('#department').val(rec.dept_id);
                            getData();
                            }">
                        </div>
                    </div>
                    <!----------------------------------------   DEPARTMENT END   ---------------------------------------->

                    <!------------------ PROGRAM START ------------------>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Program</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="program_id" name="program_id" style="width:400px;"
                                class="easyui-combobox form-control" data-options="
                            textField: 'prog_name',
                            valueField: 'prog_id',
                            required:'true',                            
                            method:'get',
                            onSelect: function(rec){
                                var progID = rec.prog_id;
                                var admschURl = '<?= url('/adm_schedules') ?>?sess_id='+$('#facultyWload_sess_id').combobox('getValue')+'&prog_id='+progID;
                                $('#adm_sch').combobox('reload', admschURl);
                                $('#program').val(rec.prog_id);
                                getData();
                            }">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">Adm Schedules</label>
                        </div>
                        <div class="col-lg-8">
                            <input id="adm_sch" name="adm_sch" style="width:400px;" class="easyui-combobox form-control"
                                data-options="                            
                            textField: 'admission_sch_name',
                            valueField: 'admission_sch_id',
                            url: '<?= url('/adm_schedules') ?>',
                            required:'true',
                            method:'get',
                            onSelect: function(rec){
                                $('#admsch').val(rec.admission_sch_id);
                                getData();
                            }">
                        </div>
                    </div>
                    <!-- <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">No of Applicants</label>
                        </div>
                        <div class="col-lg-8">
                        <input class="easyui-textbox" name="total_applicants" id="total_applicants" style="width:200px;" placeholder="Enter total applicants">
                        </div>
                    </div> -->
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">New Test Slip Date</label>
                        </div>
                        <div class="col-lg-8">
                            <input class="easyui-datebox" name="selected_date" id="selected_date" style="width:200px;"
                                required editable="false">

                        </div>
                    </div>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">New Interview Date</label>
                        </div>
                        <div class="col-lg-8">
                            <input class="easyui-datebox" name="selected_int_date" id="selected_int_date"
                                style="width:200px;" required editable="false">

                        </div>
                    </div>
                    <div class="form-group row" style="margin-top:10px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">New Test City</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="test_city" id="test_city" class="form-control" style="width:200px;" required>
                                <option value="">Select City</option>
                                <option value="Lahore">Lahore</option>
                                <option value="Multan">Multan</option>
                                <option value="Karachi">Karachi</option>
                                <option value="Islamabad">Islamabad</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top:10px;">
                        <div class="col-lg-4" style="text-align: right">
                            <label class="col-form-label">New Admission Time</label>
                        </div>
                        <div class="col-lg-8">
                            <select name="selected_int_time" id="selected_int_time" class="form-control"
                                style="width:200px;" required>
                                <option value="">Select Time</option>
                                <option>09:00 AM</option>
                                <option>09:30 AM</option>
                                <option>10:00 AM</option>
                                <option>10:30 AM</option>
                                <option>11:00 AM</option>
                                <option>11:30 AM</option>
                                <option>12:00 PM</option>
                                <option>12:30 PM</option>
                                <option>01:00 PM</option>
                                <option>01:30 PM</option>
                                <option>02:00 PM</option>
                                <option>02:30 PM</option>
                                <option>03:00 PM</option>
                                <option>03:30 PM</option>
                                <option>04:00 PM</option>
                                <option>04:30 PM</option>
                                <option>05:00 PM</option>
                                <option>05:30 PM</option>
                                <option>06:00 PM</option>
                                <option>06:30 PM</option>
                                <option>07:00 PM</option>
                                <option>07:30 PM</option>
                                <option>08:00 PM</option>
                                <option>08:30 PM</option>
                                <option>09:00 PM</option>
                                <option>09:30 PM</option>
                                <option>10:00 PM</option>
                            </select>
                        </div>
                    </div>
                </form>




                <div class="form-group row" style="margin-bottom:0px;display:none">
                    <div class="col-lg-4" style="text-align: right">
                        <label class="col-form-label">Select Option</label>
                    </div>
                    <div class="col-lg-8" style="margin-top:7px; ">
                        <div class='row'>
                            <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" checked type="radio" name="data_option" id="addNewList"
                                    value="add_new_list">
                                <label class="form-check-label" for="addNewList">Add New List</label>
                            </div>
                            {{-- <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" type="radio" name="data_option" id="newData"
                                    value="new_data">
                                <label class="form-check-label" for="newData">New Data</label>
                            </div> --}}
                            <div class="col-md-2 form-check" style='text-align:center;'>
                                <input class="form-check-input" type="radio" name="data_option" id="oldData"
                                    value="old_data">
                                <label class="form-check-label" for="oldData">Old Data</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="display: flex;justify-content: center;">
                    <button type="button" id="saveButton" onclick="submitList()" class="btn btn-primary"> <i
                            class="fa fa-paper-plane"></i> Submit List</button>

                    {{-- filter and clear work start --}}

                    <!-- <button type="button" id="filterButton" onclick="filterData()" class="btn btn-info ml-2">
        <i class="fa fa-filter"></i> Filter
    </button>

    <button type="button" id="clearButton" onclick="clearFilters()" class="btn btn-secondary ml-2">
        <i class="fa fa-refresh"></i> Clear
    </button> -->

                    {{-- filter and clear work end --}}
                    <button type="button" id="approveButton" onclick="approveList()" class="btn btn-primary d-none"> <i
                            class="fa fa-paper-plane"></i> Approve</button>

                </div>
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing,
                    please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
            </div>
            <center>
                <div>
                    <table border="1" id="programTable" style="display:none">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Top Students Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be inserted here dynamically -->
                        </tbody>
                    </table>
                </div>
            </center>


            {{-- <div class="card-header" style="display:inline">

                <div style="float: right">
                    <!-- <span>
                    <button onclick="getData();" class="btn">Refersh Enrollment List</button>
                </span> -->
                </div>
            </div> --}}
            <div id='dTable' class='d-none newlist'></div>
            {{-- ///////Old Data//////////// --}}
            <div class="row d-none oldDataDiv" style="margin:10px;">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                            <table id="data-table-OldData" class="table display table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" class="checkbox approve-select-data"
                                                id="approve-select-all" value="" /></th>
                                        <th scope="col">Enrollment No</th>
                                        <th id="gpa-label">GPA</th>
                                        <th scope="col">Workload</th>
                                        <th scope="col">Total Earn Cr Hr</th>
                                        <th scope="col">Short of Req Cr Hr</th>
                                        <th scope="col">Session</th>
                                        <th scope="col">School</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Program</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ///////New Data//////////// --}}
            <div class="row d-none newDataDiv" style="margin:10px;">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                            <table id="data-table-NewData" class="table display table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Enrollment No</th>
                                        <th scope="col">GPA</th>
                                        <th scope="col">Session</th>
                                        <th scope="col">School</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Program</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">

            </div>
        </div>
    </div>
    @stop
    @section('javascript')

    <script type="text/javascript">

        function pdf() {

            sessionId = $('#facultyWload_sess_id').combobox('getValue');
            schoolId = $('#facultyWload_school_id').combobox('getValue');
            departmentId = $('#department_id').combobox('getValue');
            progId = $('#program_id').combobox('getValue');

            url = 'http://devstaff.bnu.edu.pk/HOD/exam_summary_report_grader.php?sessionId=' + sessionId + '&esr_school_id=' + schoolId + '&esr_prog_id=' + progId + '&esr_dept_id=' + departmentId;
            window.open(url, '_blank');
        }

        var baseUrl = window.location.protocol + "//" + window.location.host;


        $(document).ready(function () {



            $('input[name="data_option"]').change(function () {
                getData();
            });
        });
        $(document).on('change', '#select-all', function () {
            if ($(this).prop("checked") == true) {
                $('.select-data').prop('checked', true);
                console.log($('.select-data').val());

            } else {
                $('.select-data').prop('checked', false);
                console.log($('.select-data').val());
            }
        });
        $(document).on('change', '#approve-select-all', function () {
            if ($(this).prop("checked") == true) {
                $('.approve-select-data').prop('checked', true);
                console.log($('.approve-select-data').val());

            } else {
                $('.approve-select-data').prop('checked', false);
                console.log($('.approve-select-data').val());
            }
        });
        function getData() {
            var selectedOption = $('input[name="data_option"]:checked').val();

            if (selectedOption === "add_new_list") {
                // console.log("Add New List selected");
                getEnrollmentStatus();
                $('#approveButton').addClass('d-none');
                $('#saveButton').removeClass('d-none');

                // $('#facultyWload_sess_id').combobox('enable');
                // $('#facultyWload_school_id').combobox('enable');
                // $('#department_id').combobox('enable');
                // $('#program_id').combobox('enable');

            } else if (selectedOption === "old_data") {
                // console.log("Old Data selected");
                LoadOldData();
                $('#saveButton').addClass('d-none');
                $('#approveButton').removeClass('d-none');

                // $('#facultyWload_sess_id').combobox('disable');
                // $('#facultyWload_school_id').combobox('disable');
                // $('#department_id').combobox('disable');
                // $('#program_id').combobox('disable');

            } else if (selectedOption === "new_data") {
                // console.log("New Data selected");
                LoadNewData();
                $('#saveButton').addClass('d-none');
                $('#approveButton').addClass('d-none');
            }
        }
        function submitList() {
            var deanHonorArr = getIdsOfSelectedData();
            // console.log(JSON.stringify(deanHonorArr));
            if (deanHonorArr != undefined) {
                const selectedIds = [];
                $('.approve-select-data:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                const sessionId = $('#facultyWload_sess_id').combobox('getValue');
                const schoolId = $('#facultyWload_school_id').combobox('getValue');
                const departmentId = $('#department_id').combobox('getValue');
                const programId = $('#program_id').combobox('getValue');
                const admSchid = $('#adm_sch').combobox('getValue');
                const testCity = $('#test_city').val();
                // const total_applicants = $('#total_applicants').textbox('getValue');
                const selectedDateObj = $('#selected_date').datebox('getValue');
                const selectedIntDateObj = $('#selected_int_date').datebox('getValue');
                const selectedIntTime = $('#selected_int_time').val();
                const requestData = {
                    session: sessionId,
                    school: schoolId,
                    dept: departmentId,
                    program: programId,
                    admSch: admSchid,
                    testCity: testCity,
                    // total_applicants: total_applicants,
                    ids: selectedIds,
                    selectedDateObj: selectedDateObj,
                    selectedIntDateObj: selectedIntDateObj,
                    selectedIntTime: selectedIntTime
                };

                if (selectedIds.length === 0) {
                    alert('Please select at least one record to update.');
                    return;
                }


                $("#processing_message").show();
                fetch('submit-deanhonor-list2', {
                    method: 'POST',
                    body: JSON.stringify(requestData), // Wrap in an object
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('bilal ' + data);
                            // alert(data.messege);
                            $("#processing_message").hide();
                            $("#error_message").text('').hide();
                            $('#success_message').html(data.messege);
                            $('#success_message').fadeIn('slow');
                            $('#success_message').fadeOut(5000);
                            getEnrollmentStatus();
                        } else {
                            console.log(data);
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
                        console.log('error: ' + error)
                        $("#processing_message").hide();
                        console.error('There was an error!', error);
                    });
            }
        }

        function getEnrollmentStatus() {
            // Show the loading spinner
            $('.oldDataDiv').addClass('d-none');
            $('.newDataDiv').addClass('d-none');
            $('#loading-spinner').show();

            // Get selected session, school, department, and program values
            var sess = $('#session').val();
            var school = $('#school').val();
            var dept = $('#department').val();
            var prog = $('#program').val();
            var admschid = $('#admsch').val();

            // Send the GET request to fetch applicant information
            $.ajax({
                url: 'student-merit-list',  // Specify the correct URL here
                type: 'GET',
                data: {
                    session: sess,
                    school: school,
                    department: dept,
                    program: prog,
                    adm_sch: admschid,
                },
                success: function (res) {
                    // Hide the loading spinner once the data is loaded
                    $('#loading-spinner').hide();

                    // Log the response to check what is being returned
                    console.log('Response:', res);
                    // Extract values from response




                    // Create the info boxes

                    // Separate rows for upload_missing_document = 1 and upload_missing_document = 0
                    let trueRows = [];
                    let falseRows = [];
                    let sr = 1;

                    // Process rows and divide into true/false arrays
                    $(res.ressqlMeritList).each(function (index, row) {
                        var upload_missing_document = $.trim(row.upload_missing_document);
                        var buttonClass = upload_missing_document === '1' ? 'btn-blink' : '';

                        console.log(row);
                        var rowHTML = `
                    <tr>
                        <td><input type="checkbox" ${row.tickmark == 1 ? 'checked' : ''} class="checkbox select-data approve-select-data" name="honorList[]" id="${row.aai_id}" value="${row.aai_id}"/></td>
                        <td>${sr++}</td>
                        <td>${row.aai_id}</td>                     
                        <td>${row.api_name}</td>
                        <td>${row.prog_name}</td>
                        <td>${row.test_slip_date}</td>
                        <td>${row.admission_sch_test_date}</td>
                        <td>${row.aai_new_interview_date}</td>
                        <td>${row.admission_sch_interview_date}</td>
                        <td>${row.aai_new_admission_time}</td>
                        <td>${row.admission_sch_test_time}</td>
                        <td>${row.aai_test_city}</td>
                        <td>${row.aai_new_test_city}</td>
                    </tr>
                `;

                        // Add to trueRows if upload_missing_document is '1', otherwise add to falseRows
                        if (upload_missing_document === '1') {
                            trueRows.push(rowHTML); // Add to trueRows (1)
                        } else {
                            falseRows.push(rowHTML); // Add to falseRows (0)
                        }
                    });

                    // Combine trueRows (upload_missing_document = 1) first, then falseRows (upload_missing_document = 0)
                    var tableHTML = `
                <div class="table-responsive container-fluid">
                    
                    <br>
                    <table class="table card-table table-primary mb-0" id="studentTable" style="width:100% !important;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th><input type="checkbox" class="checkbox select-data" id="select-all" value=""/></th>
                                <th class="text-white">Sr #</th>
                                <th class="text-white">Applicant ID</th>                               
                                <th class="text-white">Name</th>
                                <th class="text-white">Program</th>
                                <th class="text-white">New Test Slip Date</th>
                                <th class="text-white">Admission Test Slip Date</th>
                                <th class="text-white">New Interview Date</th>
                                <th class="text-white">Admission Interview Date</th>
                                <th class="text-white">New Test Time</th>
                                <th class="text-white">Admission Test Time</th>
                                <th class="text-white">Test City</th>
                                <th class="text-white">New Test City</th>
                            </tr>
                        </thead>
                        <tbody id="noti-msg-tbody">
            `;

                    // Append trueRows first (those with upload_missing_document = 1)
                    tableHTML += trueRows.join('');

                    // Then append falseRows (those with upload_missing_document = 0)
                    tableHTML += falseRows.join('');

                    tableHTML += '</tbody></table></div>';


                    // Append the generated table HTML to the #dTable div
                    $('#dTable').html(tableHTML);
                    $('#dTable').removeClass('d-none');

                    // Initialize DataTable without sorting enabled
                    if ($.fn.DataTable.isDataTable('#studentTable')) {
                        $('#studentTable').DataTable().clear().destroy(); // Clear and destroy previous DataTable
                    }
                    $('#studentTable').DataTable({
                        "responsive": true,
                        "ordering": false, // Disable sorting to maintain the original order
                        "serverSide": false, //to download all data once
                        "dom": 'Blfrtip', // Adds the buttons to the DataTable
                        "pageLength": 100, // Default entries per page
                        "lengthMenu": [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "All"]],


                        "buttons": [
                            {
                                extend: 'excelHtml5',
                                title: 'Deans Honor List',
                                exportOptions: {
                                    columns: ':visible' // Export all visible columns
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                title: 'Deans Honor List',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Deans Honor List',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ]
                    });

                    // Log the count of rows with upload_missing_document = 1 and 0
                    console.log('Rows with upload_missing_document = 1:', trueRows.length);
                    console.log('Rows with upload_missing_document = 0:', falseRows.length);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                    $('#loading-spinner').hide();
                }
            });
        }

        function getIdsOfSelectedData() {
            var honorList = new Array();
            // $.each($("input[name='honorList[]']:checked"), function () {//get only checked rows
            $("input[name='honorList[]']").each(function () {//get all rows
                var data = JSON.parse(decodeURIComponent($(this).val())); // Parse value
                data.checked = $(this).prop("checked"); // Add checked property (true/false)
                honorList.push(data);

            });
            if (honorList.length == 0) {
                alert("Please select atleast one student.");
            } else {
                // console.log(honorList);
                return honorList;
            }
        }

        function filterData() {

            // alert("filterData() function called");


            const requestData = {
                session: $('#facultyWload_sess_id').combobox('getValue'),
                school: $('#facultyWload_school_id').combobox('getValue'),
                department: $('#department_id').combobox('getValue'),
                program: $('#program_id').combobox('getValue'),
                adm_sch: $('#adm_sch').combobox('getValue'),
                selected_date: $('#selected_date').datebox('getValue'),
                selected_int_date: $('#selected_int_date').datebox('getValue'),
                test_city: $('#test_city').val(),
                selected_int_time: $('#selected_int_time').val()
            };

            $('#processing_message').show();

            $.ajax({
                url: 'filter-test-slip-records',
                type: 'GET',
                data: requestData,
                success: function (res) {
                    $('#processing_message').hide();

                    // renderStudentTable(res.ressqlMeritList);

                    updateFilteredTable(res.ressqlMeritList);
                },
                error: function (xhr) {
                    $('#processing_message').hide();
                    $('#error_message').html('Something went wrong while filtering records.').show().fadeOut(5000);
                    console.log(xhr.responseText);
                }
            });
        }

        function updateFilteredTable(records) {
            var tbody = '';

            $.each(records, function (i, row) {

                tbody += `
        <tr>
            <td>
                <input type="checkbox"
                    class="checkbox select-data approve-select-data"
                     name="honorList[]"
                    value="${row.aai_id}">
            </td>

            <td>${i + 1}</td>
            <td>${row.aai_id}</td>
            <td>${row.api_name}</td>
            <td>${row.prog_name}</td>
            <td>${row.test_slip_date ?? ''}</td>
            <td>${row.admission_sch_test_date ?? ''}</td>
            <td>${row.aai_new_interview_date ?? ''}</td>
            <td>${row.admission_sch_interview_date ?? ''}</td>
            <td>${row.aai_new_admission_time ?? ''}</td>
            <td>${row.admission_sch_test_time ?? ''}</td>
            <td>${row.aai_test_city ?? ''}</td>
            <td>${row.aai_new_test_city ?? ''}</td>
        </tr>`;
            });

            $('#studentTable').DataTable().destroy();

            $('#studentTable tbody').html(tbody);

            $('#studentTable').DataTable({
                responsive: true,
                pageLength: 100,
                dom: 'Blfrtip',
                buttons: ['excel', 'pdf', 'print']
            });
        }


        function clearFilters() {
            var selectedSession = $('#facultyWload_sess_id').combobox('getValue');

            $('#facultyWload_school_id').combobox('clear');
            $('#department_id').combobox('clear');
            $('#program_id').combobox('clear');
            $('#adm_sch').combobox('clear');

            $('#school').val('');
            $('#department').val('');
            $('#program').val('');
            $('#admsch').val('');

            $('#selected_date').datebox('clear');
            $('#selected_int_date').datebox('clear');

            $('#test_city').val('');
            $('#selected_int_time').val('');

            $('#session').val(selectedSession);

            getEnrollmentStatus();
        }


    </script>
    @stop