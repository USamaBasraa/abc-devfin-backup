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
    #clear_dates{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear_dates:hover {
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
    .edit-btn, .delete-btn{
        width:76px;
    }
</style>
@section('content')

<div class="card">
    <div class="card-header" style="width:100%;">
        <form id="InstallmentForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Installment Form (Admission):</h3></div>
                <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                <div class="row filter-row" style="margin-right:10px;">
                    <div class="form-group col-sm-6">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
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
                    {{-- <div class="form-group col-sm-6">
                        <label for="course">Degree Level</label>
                        <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                        </select>
                    </div> --}}
                    <div class="form-group col-sm-6">
                        <label for="program">Program</label>
                        <select name="program" disabled id="program" class="form-control">
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="program">Student Type</label>
                        <select name="student_type" id="student_type" class="form-control">
                            <option value="">Select type</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="admission">Admission Type</label>
                        <select name="admission_type" id="admission_type" class="form-control">
                            <option value="">Select type</option>
                            <option value="early">Early Round</option>
                            <option value="regular">Regular Round</option>
                            <option value="rolling">Rolling</option>
                        </select>
                    </div>

                <table class="table table-bordered" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>Installment No</th>
                            <th>Due Date</th>
                            <th>Valid Date</th>
                            {{-- <th>Visible Date</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1st Installment</td>
                            <td>
                                <input type="date" name="first_installment_due_date" id="first-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td>
                            <td>
                                <input type="date" name="first_installment_valid_date" id="first-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                            </td>
                            {{-- <td>
                                <input type="date" name="first_installment_visible_date" id="first-visibleDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td> --}}
                        </tr>
                        <tr>
                            <td>2nd Installment</td>
                            <td>
                                <input type="date" name="second_installment_due_date" id="second-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td>
                            <td>
                                <input type="date" name="second_installment_valid_date" id="second-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                            </td>
                            {{-- <td>
                                <input type="date" name="second_installment_visible_date" id="second-visibleDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td> --}}
                        </tr>
                        <tr>
                            <td>3rd Installment</td>
                            <td>
                                <input type="date" name="third_installment_due_date" id="third-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td>
                            <td>
                                <input type="date" name="third_installment_valid_date" id="third-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                            </td>
                            {{-- <td>
                                <input type="date" name="third_installment_visible_date" id="third-visibleDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td> --}}
                        </tr>
                         <tr>
                            <td>4th Installment</td>
                            <td>
                                <input type="date" name="forth_installment_due_date" id="forth-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td>
                            <td>
                                <input type="date" name="forth_installment_valid_date" id="forth-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                            </td>
                            {{-- <td>
                                <input type="date" name="forth_installment_visible_date" id="forth-visibleDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td> --}}
                        </tr>
                         <tr>
                            <td>5th Installment</td>
                            <td>
                                <input type="date" name="fifth_installment_due_date" id="fifth-dueDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td>
                            <td>
                                <input type="date" name="fifth_installment_valid_date" id="fifth-validDate" min="<?php echo date('Y-m-d'); ?>" class="form-control" disabled>
                            </td>
                            {{-- <td>
                                <input type="date" name="fifth_installment_visible_date" id="fifth-visibleDate" min="<?php echo date('Y-m-d'); ?>" class="form-control">
                            </td> --}}
                        </tr>
                    </tbody>
                </table>
                <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                    <button type="button" id="saveButton" class="btn btn-primary">Save</button>
                    <button type="button" style="width: 150px;margin-left:10px;" onclick="LoadData()" id="filterButton" class="btn btn-primary">Apply Filter</button>
                    <button type="button" style="width: 108px;margin-left:10px;" id="clear" class="btn btn-secondary" onclick="refreshFields()"><i class="fa fa-undo"></i> Clear All</button>
                    <button type="button" style="width: 128px;margin-left:10px;" id="clear_dates" class="btn btn-secondary" onclick="refreshDatesFields()"><i class="fa fa-undo"></i> Clear Dates</button>

                </div>
                <div id="processing_message" class="alert" style="display: none; background-color: #4454c36e; width: 100%;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none; width: 100%;"></div>
                <div id="success_message" class="alert alert-success" style="width: 100%; display: none;"></div>

        </form>
    </div>
</div>




{{-- --------------------------- --}}
    <div class="row filteredData" style="margin:10px;">
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
                            <th scope="col">First Due Date</th>
                            <th scope="col">First Valid Date</th>
                            <th scope="col">Second Due Date</th>
                            <th scope="col">Second Valid Date</th>
                            <th scope="col">Third Due Date</th>
                            <th scope="col">Third Valid Date</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
   $(document).ready(function() {
        // Load the data table
        LoadData();
    });
document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("InstallmentForm");
        var formData = new FormData(form);
        formData.append('session', $('#session_sel').val());
        formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('program', $('#program').val());
        // formData.append('student_type', $('#student_type').val());
        formData.append('first_installment_due_date', $('#first-dueDate').val());
        formData.append('first_installment_valid_date', $('#first-validDate').val());
        formData.append('second_installment_due_date', $('#second-dueDate').val());
        formData.append('second_installment_valid_date', $('#second-validDate').val());
        formData.append('third_installment_due_date', $('#third-dueDate').val());
        formData.append('third_installment_valid_date', $('#third-validDate').val());
        formData.append('forth_installment_due_date', $('#forth-dueDate').val());
        formData.append('forth_installment_valid_date', $('#forth-validDate').val());
        formData.append('fifth_installment_due_date', $('#fifth-dueDate').val());
        formData.append('fifth_installment_valid_date', $('#fifth-validDate').val());

        console.log(formData);
        $("#processing_message").show();
        fetch('add-fee-installment-admission', {
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
                    LoadData();
                } else {
                    // console.log(data);
                    var message = "";
                    if ('msg' in data) {
                        message = data.msg;
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
            console.log('error: '+error)
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    // }
});
$(document).on('change', '#first-dueDate', function() {
    console.log($(this).val());
    $('#second-dueDate').attr('min',$(this).val());
    $('#second-validDate').attr('min',$(this).val());
    $('#third-dueDate').attr('min',$(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#first-validDate').attr('min',$(this).val());
    $('#first-validDate').prop('disabled', false);
});
$(document).on('change', '#second-dueDate', function() {
    console.log($(this).val());
    $('#third-dueDate').attr('min',$(this).val());
    $('#third-validDate').attr('min',$(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#second-validDate').attr('min',$(this).val());
    $('#second-validDate').prop('disabled', false);
});
$(document).on('change', '#third-dueDate', function() {
    console.log($(this).val());
    $('#forth-dueDate').attr('min',$(this).val());
    $('#forth-validDate').attr('min',$(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#third-validDate').attr('min',$(this).val());
    $('#third-validDate').prop('disabled', false);
});
$(document).on('change', '#forth-dueDate', function() {
    console.log($(this).val());
    $('#fifth-dueDate').attr('min',$(this).val());
    $('#fifth-validDate').attr('min',$(this).val());

    $('#forth-validDate').attr('min',$(this).val());
    $('#forth-validDate').prop('disabled', false);
});
$(document).on('change', '#fifth-dueDate', function() {
    console.log($(this).val());

    $('#fifth-validDate').attr('min',$(this).val());
    $('#fifth-validDate').prop('disabled', false);
});

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
                        console.log(res.data);
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
        console.log(url);
    }
    function getDepartments(){
        var school_id = $('#school_sel').val();
        getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
            console.log(res);
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


</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

<script>

function LoadData(){
    let filterParams = getFilterParams();
    console.log(filterParams);
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
        order: [1, 'desc'],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: "/api/get-installment-dates-admission",
            data: function(d) {
                    return $.extend({}, d, filterParams);
                }
        },
        columns: [
            {data: "sess_name"},
            {data: "school_name", width: "120px"},
            {data: "dept_name"},
            {data: "prog_name"},
            {
                data: "fci_first_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part

                },
                 width: "90px"

            },
            {
                data: "fci_first_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_second_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_second_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_third_duedate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            },
            {
                data: "fci_third_validdate",
                render: function(data) {
                    return data ? data.split(' ')[0] : ''; // Extract only the date part
                },
                 width: "90px"
            }


        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    console.log(row);
                    var button = '<div>' +
                                    '<button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["fci_id"]+'" onclick="editRow(`'+row["fci_id"]+'`,`'+row["fci_session_id"]+'`,`'+row["fci_school_id"]+'`,`'+row["fci_dept_id"]+'`,`'+row["fci_prog_id"]+'`,`'+row["fci_first_duedate"]+'`,`'+row["fci_first_validdate"]+'`,`'+row["fci_second_duedate"]+'`,`'+row["fci_second_validdate"]+'`,`'+row["fci_third_duedate"]+'`,`'+row["fci_third_validdate"]+'`,`'+row["fci_fourth_duedate"]+'`,`'+row["fci_fourth_validdate"]+'`,`'+row["fci_fifth_duedate"]+'`,`'+row["fci_fifth_validdate"]+'`,`'+row["fci_student_type"]+'`,`'+row["fci_admission_type"]+'`)">' +
                                        '<i class="fa fa-edit"></i> Edit' +
                                    '</button>' +
                                    '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                                    '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["fci_id"]+'])">' +
                                        '<i class="fa fa-trash"></i> Delete' +
                                    '</button>' +
                                '</div>';
                    // var button = '<div>' +

                    //                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["fci_id"]+'])">' +
                    //                     '<i class="fa fa-trash"></i> Delete' +
                    //                 '</button>' +
                    //             '</div>';
                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 10,
            },

        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Installments Dates',
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
                    title: 'Installments Dates',
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
                    title: 'Installments Dates',
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
    console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    console.log(distinctDegreeLevels);
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
                    console.log(res.data);
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
                    console.log($('#program').val());

                } else {
                    console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
function editRow(fci_id, fci_session_id, fci_school_id, fci_dept_id, fci_prog_id, fci_first_duedate, fci_first_validdate, fci_second_duedate, fci_second_validdate, fci_third_duedate, fci_third_validdate, fci_forth_duedate, fci_forth_validdate, fci_fifth_duedate, fci_fifth_validdate, studentType, admissionType){

    console.log('fci_id -> ' + fci_id);
    console.log('fci_session_id -> ' + fci_session_id);
    console.log('fci_school_id -> ' + fci_school_id);
    console.log('fci_dept_id -> ' + fci_dept_id);
    console.log('fci_prog_id -> ' + fci_prog_id);
    console.log('fci_first_duedate -> ' + fci_first_duedate);
    console.log('fci_first_validdate -> ' + fci_first_validdate);
    console.log('fci_second_duedate -> ' + fci_second_duedate);
    console.log('fci_second_validdate -> ' + fci_second_validdate);
    console.log('fci_third_duedate -> ' + fci_third_duedate);
    console.log('fci_third_validdate -> ' + fci_third_validdate);
    console.log('fci_forth_duedate -> ' + fci_forth_duedate);
    console.log('fci_forth_validdate -> ' + fci_forth_validdate);
    console.log('fci_fifth_duedate -> ' + fci_fifth_duedate);
    console.log('fci_fifth_validdate -> ' + fci_fifth_validdate);

    $('#main-Id').val(fci_id);
    $('#session_sel').val(fci_session_id);
    $('#school_sel').val(fci_school_id);
    $('#student_type').val(studentType);
    $('#admission_type').val(admissionType);
    getDepartments();//this will set department dropdown menu
    setTimeout(function() {
        $('#department').val(fci_dept_id);
        getPrograms();//this will set programs dropdown menu and degreelevel dropdown menu
        setTimeout(function() {
            // setProgramsAccordingly();
            setTimeout(function() {
                $('#program').val(fci_school_id+'~~'+fci_dept_id+'~~'+fci_prog_id);
            }, 500);  // Adjust the delay as needed
        }, 500);  // Adjust the delay as needed
    }, 500);  // Adjust the delay as needed

    // Set installment dates
    $('#first-dueDate').val(fci_first_duedate.split(' ')[0]); // Set first due date
    $('#first-validDate').val(fci_first_validdate.split(' ')[0]); // Set first valid date

    $('#second-dueDate').val(fci_second_duedate.split(' ')[0]); // Set second due date
    $('#second-validDate').val(fci_second_validdate.split(' ')[0]); // Set second valid date

    $('#third-dueDate').val(fci_third_duedate.split(' ')[0]); // Set third due date
    $('#third-validDate').val(fci_third_validdate.split(' ')[0]); // Set third valid date

    $('#forth-dueDate').val(fci_forth_duedate.split(' ')[0]); // Set third due date
    $('#forth-validDate').val(fci_forth_validdate.split(' ')[0]); // Set third valid date

    $('#fifth-dueDate').val(fci_fifth_duedate.split(' ')[0]); // Set third due date
    $('#fifth-validDate').val(fci_fifth_validdate.split(' ')[0]); // Set third valid date

    // Enable fields if necessary
    $('#first-validDate').prop('disabled', false);
    $('#second-validDate').prop('disabled', false);
    $('#third-validDate').prop('disabled', false);
    $('#forth-validDate').prop('disabled', false);
    $('#fifth-validDate').prop('disabled', false);

    // Scroll to the form (optional)
    $('html, body').animate({scrollTop: 0}, 'slow');
    // $('html, body').animate({ scrollTop: $('#InstallmentForm').offset().top }, 'slow');

}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/delete-installment-dates-admission",
            data: {
                mainId: mainId
            },
            success: function(response) {
                // $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadData();
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
    $('#session_sel').val('');
    $('#school_sel').val('');
    $('#department').val('');
    $('#program').val('');
    $('#student_type').val('');
    $('#admission_type').val('');

    $('#first-dueDate').val('');
    $('#first-validDate').val('');
    // $('#first-visibleDate').val('');

    $('#second-dueDate').val('');
    $('#second-validDate').val('');
    // $('#second-visibleDate').val('');

    $('#third-dueDate').val('');
    $('#third-validDate').val('');
    // $('#third-visibleDate').val('');

    $('#forth-dueDate').val('');
    $('#forth-validDate').val('');

    $('#fifth-dueDate').val('');
    $('#fifth-validDate').val('');
}
function refreshDatesFields(){

    $('#first-dueDate').val('');
    $('#first-validDate').val('');
    // $('#first-visibleDate').val('');

    $('#second-dueDate').val('');
    $('#second-validDate').val('');
    // $('#second-visibleDate').val('');

    $('#third-dueDate').val('');
    $('#third-validDate').val('');
    // $('#third-visibleDate').val('');

    $('#forth-dueDate').val('');
    $('#forth-validDate').val('');

    $('#fifth-dueDate').val('');
    $('#fifth-validDate').val('');
}
function getFilterParams() {
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const department = $('#department').val();
    const program = $('#program').val();
    const student_type = $('#student_type').val();
    const admission_type = $('#admission_type').val();
    const first_dueDate = $('#first-dueDate').val();
    const first_validDate = $('#first-validDate').val();
    // const first_visibleDate = $('#first-visibleDate').val();
    const second_dueDate = $('#second-dueDate').val();
    const second_validDate = $('#second-validDate').val();
    // const second_visibleDate = $('#second-visibleDate').val();
    const third_dueDate = $('#third-dueDate').val();
    const third_validDate = $('#third-validDate').val();
    // const third_visibleDate = $('#third-visibleDate').val();
    const forth_dueDate = $('#forth-dueDate').val();
    const forth_validDate = $('#forth-validDate').val();

    const fifth_dueDate = $('#fifth-dueDate').val();
    const fifth_validDate = $('#fifth-validDate').val();
    let filterParams =  {
        session_sel: session_sel,
        school_sel: school_sel,
        department: department,
        program: program,
        student_type: student_type,
        admission_type: admission_type,
        first_dueDate: first_dueDate,
        first_validDate: first_validDate,
        second_dueDate: second_dueDate,
        second_validDate: second_validDate,
        third_dueDate: third_dueDate,
        third_validDate: third_validDate,
        forth_dueDate: forth_dueDate,
        forth_validDate: forth_validDate,
        fifth_dueDate: fifth_dueDate,
        fifth_validDate: fifth_validDate,
    };
    return filterParams;
}
</script>

@stop

