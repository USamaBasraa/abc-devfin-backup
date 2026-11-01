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

</style>
@section('content')
<div class="card" style="padding: 35px;">
    <form id="challanForm" method="POST" action="">
        @csrf

        <div class="row">
            <h3>Enrollment Summary Report:</h3>
        </div>

        <div class="row">
            <!-- Student ID / Enrollment No. -->
            {{-- <div class="form-group col-sm-6">
                <label for="studentEnrollment">Student ID / Enrollment No.</label>
                <div style="display: flex; align-items: center;">
                    <input
                        placeholder=""
                        class="form-control"
                        type="text"
                        name="studentEnrollment"
                        id="studentEnrollment"
                        onkeypress="handleEnterPress(event)">

                    <button onclick='search("enrollment")'
                            type="button"
                            class="btn btn-info"
                            style="margin-left: 8px;">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </div> --}}

        </div>

        <div class="row g-3">


            <!-- Session -->
            <div class="form-group col-sm-6">
                <label for="session_sel">Session</label>
               <select name="session" id="session_sel" class="form-control">
                    <?php foreach ($sessions as $key => $value) { ?>
                        <option value="<?= $key ?>" <?= ($key == 165 ? 'selected' : '') ?>>
                            <?= $value ?>
                        </option>
                    <?php } ?>
                </select>

            </div>

            <!-- School -->
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
                    <option value="[{{ implode(',', $allSchools) }}]">All School</option>
                    @foreach ($schools as $row)
                        <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div class="form-group col-sm-6">
                <label for="department">Department</label>
                <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                </select>
            </div>

            <!-- Degree Level -->
            <div class="form-group col-sm-6">
                <label for="course">Degree Level</label>
                <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                </select>
            </div>

            <!-- Program -->
            <div class="form-group col-sm-6">
                <label for="program">Program</label>
                <select name="program" disabled id="program" class="form-control">
                </select>
            </div>

            <!-- Scholarship Type (Hidden) -->
            <div class="form-group col-sm-2 d-none">
                <label for="scholrshipType">Scholarship Type</label>
                <select name="scholrshipType" id="scholrshipType" class="form-control">
                    <option value="">--Select Scholarship--</option>
                    @foreach ($recScholarship_Type as $row)
                        <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons Row -->
            <div class="form-group col-sm-12">

                <div class="btn-group ml-2" role="group" id="filterBtnDiv">
                    <button type="button" id="filterButton" class="btn btn-primary filterbutton" onclick="filterStudents();">
                        <i class="fa fa-filter"></i> Filter Students
                    </button>
                </div>

                <button type="button" style="width: 100px; margin-left: 10px;"
                    id="clear" class="btn btn-secondary" onclick="refreshFields()">
                    <i class="fa fa-undo"></i> Clear
                </button>

            </div>

            <!-- Messages -->
            <div id="processing_message" class="alert" style="display: none; background-color: #4454c36e;">
                Processing, please wait...
            </div>

            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>

        </div> <!-- /.row g-3 -->

    </form>
</div> <!-- /.card -->
<div class="card" style="margin-top:10px;">
    <div class="row filteredData" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                    <thead>
                    <tr>
                        <th scope="col">Student Name</th>
                        <th scope="col">Enrollment No</th>
                        <th scope="col">Apply Id</th>
                        <th scope="col">Allowed Credit Hours</th>
                        <th scope="col">Earned Credit Hours</th>
                        <th scope="col">Remaining Credit Hours</th>
                        <th scope="col">Enrolled Credit Hours</th>
                        <th scope="col">Semester</th>
                        <th scope="col">Accumulative Challan Amount</th>
                        <th scope="col">Accumulative Challan Arrears</th>
                        <th scope="col">Accumulative Scholarship Percentage</th>
                        {{-- <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" checked /></th> --}}
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
{{-- --------------------------- --}}


<div style="clear:both;"></div>
@stop
@section('javascript')
<script>


document.getElementById('saveButton').addEventListener('click', function() {
    studentIdsArr = getIdsOfSelectedData();
    console.log(studentIdsArr.ids);
    console.log(studentIdsArr.fscd_ids);

    // console.log('studentaRR->'+studentIdsArr);
    if(studentIdsArr.ids != undefined){
        var form = document.getElementById("challanForm");
        var formData = new FormData(form);
        formData.append('session', $('#session_sel').val());
        formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('degree_level', $('#degree_level').val());
        formData.append('program', $('#program').val());
        formData.append('student_type', $('#studentTypeOption').val());
        formData.append('fee_type', $('#feeType').val());
        formData.append('creditHourRange', $('#creditHourRange').val());
        formData.append('student_status', $('#student_status').val());
        formData.append('due_date', $('#due-date').val());
        formData.append('valid_date', $('#valid-date').val());
        formData.append('visible_date', $('#visible-date').val());
        formData.append('second_installment_due_date', $('#second-dueDate').val());
        formData.append('third_installment_due_date', $('#third-dueDate').val());
        formData.append('second_installment_valid_date', $('#second-validDate').val());
        formData.append('third_installment_valid_date', $('#third-validDate').val());
        formData.append('students', studentIdsArr.ids);
        formData.append('fscd_ids', studentIdsArr.fscd_ids);

        // console.log(formData);
        $("#processing_message").show();
        fetch('add-credit-challan', {
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
            }
        })
        .catch(error => {
            // console.log('error: '+error)
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
    function getIdsOfSelectedData() {
        var student_ids = [];
        var fscd_ids = [];

        $.each($("input[name='student_ids[]']:checked"), function () {
            let studentId = $(this).val();
            let fscdId = $(this).data('fscd-id') || null;
            let appId = $(this).data('app-id') || null;
            let enrollment = $(this).data('enrollment') || null;

            student_ids.push(studentId);
            fscd_ids.push(fscdId);

        });

        return {
            ids: student_ids,
            fscd_ids: fscd_ids
        };
    }
    function search(searchBy){
    $('#ledgerData').removeClass('d-none');
    enrollment = searchBy
    session_sel = $('#session_sel').val();
    effective_from = $('#eff-from').val();
    effective_to = $('#eff-to').val();
    challanType = $('input.challanType:checked').val();
    let beforeF2024 = document.getElementById('beforeF2024');

    let studentImageDiv = document.getElementById('studentImage');
    let htmlToAppend = `<img src="https://cms.bnu.edu.pk/students_pics/${enrollment.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:70px; height:70px;">`;
    studentImageDiv.innerHTML = htmlToAppend;

    }


    let studentImageDiv = document.getElementById('studentImage');
    let htmlToAppend = `<img src="https://cms.bnu.edu.pk/students_pics/${enrollment.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:70px; height:70px;">`;
    studentImageDiv.innerHTML = htmlToAppend;



</script>

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

function LoadStudents(session_id, school_id, department_id, program_id, degree_level, studentType, feeType, studentStatus, scholrshipType){

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
            url: "/api/enrollment-summary/get_student_list",
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
            }
        },
        columns: [
            {data: "spi_name"},
            {data: "sai_enrollment_no"},
            {data: "fscd_app_id"},
            {data: "fscd_std_allowed_credits"},
            {data: "fscd_std_earned_credits"},
            {
                data: null,
                render: function (data, type, row) {
                    var allowed = Number(row.fscd_std_allowed_credits);
                    var earned = Number(row.fscd_std_earned_credits);
                    var remaining = allowed - earned;
                    return remaining < 0 ? 0 : remaining;
                }
            },
            {data: "fscd_std_enrolled_credits"},
            {data: "fscd_std_current_semester"},
            {data: "fscd_sum_challan_amount"},
            {data: "fscd_sum_scholarship_percent"},
            {data: "fscd_sum_challan_arrears"},


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
            // {
            //     render: function (data, type, row) {
            //             var   selectbtn = '<div><input name="student_ids[]" class="select-data" value='+row["sai_id"]+' type="checkbox" checked></div>';

            //         return selectbtn;

            //     },
            //     // className: 'dt-body-center',
            //     // className: 'dt-head-center',
            //     targets: 6,
            //     searchable: false,
            //     orderable: false
            // },

            // {
            //     "width": "100px", "targets": 1
            // }

        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Enrollment Report',
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
                    title: 'Enrollment Report',
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
                    title: 'Enrollment Report',
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
//             url: "/api/generate-creditfee-challan/get_feeAdjusted_list",
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
    var url = '/api/generate-creditfee-challan/print_challan?challanNo=' + challan_id;
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
    students_credithr='';

    let session_id =  $('#session_sel').val();
    let school_id = $('#school_sel').val();
    let department_id = $('#department').val();
    let degree_level = $('#degree_level').val();
    let program_id = $('#program').val();
    // console.log(feeType);
    LoadStudents(session_id, school_id, department_id, program_id, degree_level);

}
$(document).ready(function () {
    filterStudents();
});


</script>

@stop

