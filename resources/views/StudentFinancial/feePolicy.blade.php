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
    .checkbox-container {
        display: flex;
        align-items: center;
        height: 100%; /* Ensure it takes up the full height of the td */
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
        /* margin: auto 0; */
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
    .button-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 16px; /* Adjust as needed */
    }

    .button-container .edit-btn,
    .button-container .delete-btn {
        margin-top: 0;
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
</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card-header" style="width:100%;">

        <form id="feePolicyFrom" method="POST" action="">
            @csrf

            <div class="row">
                <h3>Enrollment Policy:</h3>
            </div>

            <div class="row filter-row">
                <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                <div class="form-group col-sm-6" style="margin: 30px auto;">
                    <div class='row'>
                        <div class='col-md-4'>
                            <label for="schlrshp-type">Tenure Type</label>
                        </div>
                        <div class='col-md-4'>
                            <div class="checkboxOptions">
                                <input type="radio" checked class="validity-type" id="session-wise" name="validity-type"
                                    value="session">
                                <label for="session-wise">Session wise</label>
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class="checkboxOptions">
                                <input type="radio" class="validity-type" id="annual-wise" name="validity-type"
                                    value="">
                                <label for="annual-wise">Year wise</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-6 session_sel_div">
                    <label for="session_sel">Session</label>
                    <select id="session_sel" name="session" class="form-control" onchange="on_session_change(this);">
                        <option value="">Select Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span id="session_sel-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-sm-6 year_sel_div d-none">
                    <label for="year_sel">Academic Year</label>
                    <select name="academic_year" id="year_sel" class="form-control" onchange="on_session_change(this);">
                        <option value="">Select Academic Year</option>
                        @foreach ($academicYears as $key => $value)
                            <option value="{{ $value->ay_id }}">{{ $value->ay_name }}</option>
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
            </div>
            <div class="row filter-row">
                <div class="form-group col-sm-6">
                    <label for="type">Type</label>
                    <div class="form-check form-check-inline" style="margin-left: 40px;">
                        <input class="form-check-input" type="radio" name="fee_Type" id="fee_Type" value="cr_wise">
                        <label class="form-check-label" for="fee_Type">Cr. Wise</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 40px;">
                        <input class="form-check-input" type="radio" name="fee_Type" id="fee_Type" value="full_semester">
                        <label class="form-check-label" for="fee_Type">Full Semester</label>
                    </div>
                    <span id="fee_Type-error" class="error text-danger"></span>
                </div>

                <div class="form-group col-sm-6 d-flex align-items-center">
                    <label for="range">Credit Hour Range:</label>
                    <div class="input-group ms-2">
                        <input type="text" min="0" class="form-control" id="min_range" name="min_range"
                            placeholder="Min" oninput="validateFloatInput(this)">
                        <div class="input-group-text">-</div>
                        <input type="text" min="0" class="form-control" id="max_range" name="max_range"
                            placeholder="Max" oninput="validateFloatInput(this)">
                    </div>
                    <span id="min_range-error" class="error text-danger float-end"></span>
                    <span id="max_range-error" class="error text-danger float-end"></span>
                </div>

            </div>
             <div class="row filter-row">
                <div class="form-group col-sm-4">
                    <label for="course">Semester</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $row)
                            <option value="{{ $row->semester_id }}">{{ $row->semester_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                <button type="button"  style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                <button type="button" style="width: 150px;margin-left:50px;" id="filterButton" class="btn btn-primary">Apply Filter</button>
                <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">Clear</button>
            </div>
            <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>
        <h5 id="successMsg" class"mt-3" style="color:green; margin-left:10px;"></h5>
        <h5 id="errorMsg" class"mt-3" style="color:red; margin-left:10px;"></h5>
    </div>
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display">
                        <thead>
                          <tr>
                            <th scope="col">Session</th>
                            <th scope="col">Type</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Degree Level</th>
                            <th scope="col">Program</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Min Range</th>
                            <th scope="col">Max Range</th>
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

    <div id="showDataInTable" class="table-responsive"></div>
    </div>

@stop

@section('javascript')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}
<script>
$(document).ready(function() {
    loadFeePolicy();
    document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("feePolicyFrom");
        var formData = new FormData(form);
        // formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('program', $('#program').val());

        ////////////////////////
        $("#processing_message").show();
        fetch('/finance/submit-fee-policy', {
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
                loadFeePolicy();
            } else {
                console.log(data);
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
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    });
    $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            console.log($('.select-data').val());
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
            deleteRows(fee_ids);
        }
    });
    $(document).on('change', '.validity-type', function() {
        selectedValue = $(this).attr('id');
        if(selectedValue == 'session-wise'){
            $('.session_sel_div').removeClass('d-none');//show session selection div
            $('.validity-type').val('session');//setting validityType value
            $('.year_sel_div').addClass('d-none'); //hiding thd year fields
            $('#year_sel').val('');//emptying the from date

        }else if(selectedValue == 'annual-wise'){
            $('.year_sel_div').removeClass('d-none');//show validity dates field
            $('.validity-type').val('annual');//setting validityType value
            $('.session_sel_div').addClass('d-none');//hide session selection div
            $('#session_sel').val('');//emptying the sesstion selection
        }
    });
    $(document).on('click', '#filterButton', function() {
        loadFeePolicy();
    });


});
function getRequest(endPoint,successBlock,failBlock){
    $.ajax({
        url: "{{url('/')}}"+endPoint,
        type: "GET",
        dataType: "json",
        success:successBlock
    }).fail(failBlock);
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
function on_session_change(session){
    console.log(session.value);
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
                        html += `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
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
function deleteRows(mainId){
    // console.log(mainId);
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "DELETE",
            url: "/finance/delete-fee-policy/",
            data: {
                mainId: mainId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            success: function(response) {
                $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                loadFeePolicy();

            },
            error: function(error) {
                // Handle the error
                console.error("Error:", error);
            }
        });
    } else {

    }

}
function displayValidationErrors(xhr) {
    if (xhr.status == 422) {
        var errors = xhr.responseJSON.errors;
        $.each(errors, function(key, value) {
            $("#" + key + "-error").html('<i class="fa fa-info-circle"></i> ' + value[0]);
            $("#" + key + "-error").addClass('text-danger');
            setTimeout(function() {
                $("#" + key + "-error").text('');
            }, 5000);
        });
    }
}
function loadFeePolicy(){
    let filterParams = getFilterParams();
    console.log(filterParams);
    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        // serverSide: true,
        serverSide: false, // All data is loaded to the client
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [1, 'desc'],
        ajax:{
            dataType: 'json',
            method: 'post',
            url:  '/finance/get-fee-policy',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            data: function(d) {
                    return $.extend({}, d, filterParams);
                }

            },
        columns: [
            {data: "sess_name"},
            {data: "ffs_type"},
            {data: "school_name"},
            {data: "dept_name"},
            {data: "ffp_degree_level"},
            {data: "prog_name"},
           { 
                data: "semester_name",
                render: function (data, type, row) {
                    return data ? data : 'NA';
                }
            },
            {data: "ffp_min_cr"},
            {data: "ffp_max_cr"}
        ],
        columnDefs: [
            {
                render: function (data, type, row) {
                    console.log(row);
                    // var button = '<button class="btn btn-primary mb-1 btn-sm" style="margin-top:16px;" id="'+row["ffp_id"]+'" onclick="editRec(`'+row["ffp_id"]+'`,`'+row["ffp_sess_id"]+'`,`'+row["ffp_school_id"]+'`,`'+row["ffp_dept_id"]+'`,`'+row["ffp_degree_level"]+'`,`'+row["ffp_prog_id"]+'`,`'+row["ffs_type"]+'`,`'+row["ffp_criteria"]+'`,`'+row["ffp_min_cr"]+'`,`'+row["ffp_max_cr"]+'`)"><i class="fa fa-edit"></i>Edit</button>'+
                    //                             '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                    //                         '<button class="btn btn-danger mb-1 delete-btn btn-sm" style="margin-top:5px;" onclick="deleteRows(['+row["ffp_id"]+'])"><i class="fa fa-trash"></i>Delete</button>';
                    var button = '<div class="button-container" style="margin-top:5px;">'+
                                        '<button class="btn btn-primary mb-1 btn-sm edit-btn" id="'+row["ffp_id"]+'" onclick="editRec(`'+row["ffp_id"]+'`,`'+row["ffp_sess_id"]+'`,`'+row["ffp_school_id"]+'`,`'+row["ffp_dept_id"]+'`,`'+row["ffp_degree_level"]+'`,`'+row["ffp_prog_id"]+'`,`'+row["ffs_type"]+'`,`'+row["ffp_criteria"]+'`,`'+row["ffp_min_cr"]+'`,`'+row["ffp_max_cr"]+'`,`'+row["ffp_semester"]+'`)">'+
                                            '<i class="fa fa-edit"></i>Edit'+
                                        '</button>'+
                                        '<button class="btn btn-danger mb-1 delete-btn btn-sm" onclick="deleteRows(['+row["ffp_id"]+'])">'+
                                            '<i class="fa fa-trash"></i>Delete'+
                                        '</button>'+
                                    '</div>';
                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 9,
            },
            {
                    render: function (data, type, row) {
                            var   selectbtn = '<div class="checkbox-container">'+
                                                    '<input name="fee_ids[]" class="select-data" idData='+row["ffp_id"]+' value='+row["ffp_id"]+' type="checkbox" >'+
                                                '</div>';

                        return selectbtn;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 10,
                    searchable: false,
                    orderable: false
                },
            {
                "width": "100px", "targets": 2
            },
            // {
            //     "width": "50px", "targets": 6
            // },
            // {
            //     "width": "10px", "targets": 8
            // },
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
        dom: 'Blfrtip', // Adds the buttons to the DataTable
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Enrollment Policy',

                exportOptions: {
                        modifier: {
                            search: 'applied', // Include filtered rows
                            order: 'applied',  // Include the current sort order
                            page: 'all'        // Include all rows, not just the visible ones
                        }
                    }
            },
            {
                extend: 'pdfHtml5',
                title: 'Enrollment Policy',
                orientation: 'landscape',

                pageSize: 'A4',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied',
                        page: 'all'
                    }
                }
            },
            {
                extend: 'print',
                title: 'Enrollment Policy',
            },

        ]
    });
}
function editRec(ffp_id, ffp_sess_id, ffp_school_id, ffp_dept_id, ffp_degree_level, ffp_prog_id, ffs_type, ffp_criteria,min, max,semester) {
    console.log(min);
    console.log(max);
    console.log(ffp_id);
    console.log(ffp_prog_id);
    console.log(semester);

    $('#main-Id').val(ffp_id);
    // $('#edit_id').val(ffp_id);
    $('#session_sel').val(ffp_sess_id);
    $('#school_sel').val(ffp_school_id);
    getDepartments();//this will set department dropdown menu
    setTimeout(function() {
        $('#department').val(ffp_dept_id);
        getPrograms();//this will set programs dropdown menu and degreelevel dropdown menu
        setTimeout(function() {
            $('#degree_level').val(ffp_degree_level);
            setProgramsAccordingly();
            setTimeout(function() {
                $('#program').val(ffp_school_id+'~~'+ffp_dept_id+'~~'+ffp_prog_id);
            }, 500);  // Adjust the delay as needed
        }, 500);  // Adjust the delay as needed
    }, 500);  // Adjust the delay as needed




    // Assuming 'fee_Type' is the ID of a radio button group
    $('input[name="fee_Type"][value="' + ffs_type + '"]').prop('checked', true).trigger('change');

    // Adjusting min_range and max_range based on ffp_criteria
    var ffp_criteria = ffp_criteria.split('-');
    $('#min_range').val(min);
    $('#max_range').val(max);
    $('#semester').val((semester === null || semester === 'null') ? '' : semester);

    $('html, body').animate({scrollTop: 0}, 'slow');
}
function validateFloatInput(input) {
    let value = input.value;
    let validValue = value.match(/^\d*\.?\d{0,1}$/);
    if (validValue) {
        input.value = validValue[0];
    } else {
        input.value = input.value.slice(0, -1);
    }
}
function action_onAllSchoolSelection(school_id){
    if(school_id != ''){
        school_ids_array = JSON.parse(school_id);
        if(Array.isArray(school_ids_array)){
            $('#department').prop('disabled', true);
            $('#program').prop('disabled', true);
            // $('#degree_level').prop('disabled', true);
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
            // $('#degree_level').prop('disabled', false);
        }
    }else{
        $('#department').prop('disabled', false);
        $('#program').prop('disabled', false);
        // $('#degree_level').prop('disabled', false);
    }

}
function action_onAllDepartmentSelection(dept_id){
    if(dept_id != ''){
        dept_ids_array = JSON.parse(dept_id);
        if(Array.isArray(dept_ids_array)){
            $('#program').prop('disabled', true);
            // $('#degree_level').prop('disabled', true);
            $('#program').val($('#program option:nth-child(2)').val());
        }else{
            $('#program').prop('disabled', false);
            // $('#degree_level').prop('disabled', false);
        }
    }else{
        $('#program').prop('disabled', false);
        // $('#degree_level').prop('disabled', false);
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
function refreshFields(){
    $('#feePolicyFrom')[0].reset(); // Reset the form
    $('#main-Id').val('');

    $('#session_sel').val('');
    $('.session_sel_div').removeClass('d-none');//show session selection div
    $('.validity-type').val('session');//setting validityType value
    $('.year_sel_div').addClass('d-none'); //hiding thd year fields
    $('#year_sel').val('');//emptying the year

    $('#school_sel').val('');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');
    $('#semester').val('');
}
function getFilterParams() {
    const validity_type = $('.validity-type:checked').val();
    const year_sel = $('#year_sel').val();
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const department = $('#department').val();
    const degree_level = $('#degree_level').val();
    const program = $('#program').val();
    const fee_Type = $('#fee_Type:checked').val();
    const min_range = $('#min_range').val();
    const max_range = $('#max_range').val();
    const semester = $('#semester').val();

    let filterParams = {
        validity_type: validity_type,
        year_sel: year_sel,
        session_sel: session_sel,
        school_sel: school_sel,
        department: department,
        degree_level: degree_level,
        program: program,
        fee_Type: fee_Type,
        semester: semester,
        min_range: min_range,
        max_range: max_range
    };

    return filterParams;
}
    </script>
@stop
