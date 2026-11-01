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
    .disclaimer{
        color:#4c62ff;
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
</style>
@section('content')

{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
<div class="card-header" style="width:100%;">


<form id="feeForm" method="POST" action="">
    @csrf
        <div class="row"><h3>Scholarship Criteria:</h3></div>

        <div class="row filter-row formDiv">
            <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
            <div class="form-group col-sm-6" style="margin: 30px auto;">
                {{-- <label for="type">Type</label> --}}
                {{-- <div class="form-check form-check-inline" style="margin-left: 40px;">
                  <input class="form-check-input" checked type="radio" name="scholarship_tenure_type" id="scholarship_tenure" value="sessionWise">
                  <label class="form-check-label" for="scholarship_tenure_type">Session Wise</label>
                </div>
                <div class="form-check form-check-inline" style="margin-left: 40px;">
                  <input class="form-check-input" type="radio" name="scholarship_tenure_type" id="scholarship_tenure" value="yearly">
                  <label class="form-check-label" for="scholarship_tenure_type">Yearly</label>
                </div>
                <span id="fee_Type-error" class="error text-danger"></span> --}}
                <div class='row'>
                    <div class='col-md-4'>
                        <label for="schlrshp-type">Scholarship Type</label>
                    </div>
                    <div class='col-md-4'>
                        <div class="checkboxOptions">
                            <input type="radio" checked class="validity-type" id="session-wise" name="validity-type" value="session">
                            <label for="session-wise">Session wise</label>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div  class="checkboxOptions">
                            <input type="radio" class="validity-type" id="annual-wise" name="validity-type" value="">
                            <label for="annual-wise">Year wise</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-6 session_sel_div">
                <label for="session_sel">Session</label>
                <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                    <option value="">Select Session</option>
                    @foreach ($sessions as $key => $value)
                    <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                    @endforeach
                </select>
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
            <div class="form-group col-sm-6" style='margin-top:30px;'>
                <label for="type">Roadmap Session</label>
                <div class="form-check form-check-inline" style="margin-left: 40px;">
                    <input class="form-check-input" type="radio" name="roadmap" id="roadmap" value="old">
                    <label class="form-check-label" for="roadmap">Before Fall 2024</label>
                </div>
                <div class="form-check form-check-inline" style="margin-left: 40px;">
                    <input class="form-check-input" type="radio" name="roadmap" id="roadmap" value="new">
                    <label class="form-check-label" for="roadmap">Fall 2024</label>
                </div>
                <span id="roadmap-error" class="error text-danger"></span>
            </div>
            <div class="form-group col-sm-6 roadmap_session_sel d-none">
                <label for="session_sel">Roadmap Session</label>
                <select name="roadmap_session_sel" id="roadmap_session_sel" class="form-control">
                    <option value="">Select Session</option>
                    @foreach ($sessions as $key => $value)
                    <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="scholarship_sel">Scholarship</label>
                <select name="scholarship" id="scholarship_id" class="form-control">
                    <option value="">Select Scholarships</option>
                    @foreach ($scholarships as $row)
                    <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- //////////////////////////////////////// --}}

            <div class="form-group col-sm-6">
                <label for="cgpa_sel">Define CGPA range</label>
                <small class="disclaimer"> (Min and Max values are included in the range)</small>
                <div class="row">
                    <div class="form-group col-md-2">
                        <input type="text" value="CGPA" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Min</span>
                            </div>
                            <input type="text"  min="1" max="4" maxlength="4" oninput="validateCGPAInput(event)" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_min" id="cgpaFrom" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Max</span>
                            </div>
                            <input type="text"  min="1" max="4" maxlength="4" oninput="validateCGPAInput(event)" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_max" id="cgpaTo" class="form-control">
                        </div>
                    </div>
                    <small class="cgpaRange-disclaimer"></small>
                </div>
            </div>

            {{-- /////////////////////////////////////////////////// --}}

            {{-- <div class="form-group col-sm-6">
                <label for="cgpa_sel">Define CGPA range</label>
                <div class="row">
                    <div class="form-group col-md-2">
                        <input type="text" value="CGPA" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_from" id="cgpaFrom" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <select name="relation" id="cgpaFromRelation" class="form-control">
                            <option value="greater_than">&lt;</option> <!-- Greater than (>) -->
                            <option value="greater_or_equal">&le;</option> <!-- Greater than or equal to (≥) -->
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_from" id="cgpaFrom" class="form-control">
                    </div>
                </div>
            </div> --}}

            {{-- //////////////////////////////////////////////// --}}

            {{-- <div class="form-group col-sm-6" style="margin-top: 28px;">
                <div class="row">
                    <div class="form-group col-md-2">
                        <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_from" id="cgpaFrom" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <select name="relation" id="cgpaFromRelation" class="form-control">
                            <option value="greater_than">&lt;</option> <!-- Greater than (>) -->
                            <option value="greater_or_equal">&le;</option> <!-- Greater than or equal to (≥) -->
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" value="CGPA" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <select name="relation" id="cgpaToRelation" class="form-control">
                            <option value="less_than">&lt;</option> <!-- Greater than (>) -->
                            <option value="less_or_equal">&le;</option> <!-- Greater than or equal to (≥) -->
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" onkeypress="return /[0-9.]/i.test(event.key)" name="cgpa_to" id="cgpaTo" class="form-control">
                    </div>
                </div>
            </div> --}}
                <div class="form-group col-sm-3">
                    <label for="cgpaPercent">Percentage</label>
                    <div class="input-group">
                        <input type="text" min="1" max="100" oninput="validatePercentageInput(event)" onkeypress="return /[0-9.]/i.test(event.key)" name="percentage" id="percent" class="form-control" maxlength="3">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="percent-disclaimer"></small>
                    </div>
                </div>

                <div class="form-group col-sm-3 d-none">
                    <label for="studentType">Effective From</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                </div>
                <div class="form-group col-sm-3 d-none">
                    <label for="studentType">Effective To</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                </div>

        </div>
        <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
            <button type="button"  style="width: 150px;"  id="saveButton" class="btn btn-primary">Save</button>
            <button type="button" style="width: 150px;margin-left:50px;" id="filterButton" class="btn btn-primary">Apply Filter</button>
            <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">Clear</button>
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
                    <table id="data-table" class="table display">
                        <thead>
                          <tr>
                            {{-- <th scope="col">Sr.No</th> --}}
                            <th scope="col">Scholarship</th>
                            <th scope="col">Session</th>
                            <th scope="col">Roadmap Session</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Program</th>
                            <th scope="col">CGPA Range</th>
                            <th scope="col">Percentage</th>
                            {{-- <th scope="col">Effective From</th>
                            <th scope="col">Effective To</th> --}}
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
    formData.append('school', $('#school_sel').val());
    formData.append('department', $('#department').val());
    formData.append('program', $('#program').val());
    formData.append('session', $('#session_sel').val());
    formData.append('degree_level', $('#degree_level').val());
    formData.append('academic_year', $('#year_sel').val());
    formData.append('scholarship', $('#scholarship_id').val());
    formData.append('percentage', $('#percent').val());
    formData.append('cgpa_min', $('#cgpaFrom').val());
    formData.append('cgpa_max', $('#cgpaTo').val());
    formData.append('effective_from', $('#eff-from').val());
    formData.append('effective_to', $('#eff-to').val());
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
    fetch('add-scholarship-criteria', {
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
            LoadScholarships();
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
$(document).ready(function(){
    LoadScholarships();
    $('#feeType').change(function() {
        if($(this).val() == 'Credit_hours'){
            $('.credFeeDiv').removeClass('d-none');
        }else{
            $('.credFeeDiv').addClass('d-none');
            $('#credFee').val('');

        }
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
        LoadScholarships();
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

<script>
function LoadScholarships(){
    let filterParams = getFilterParams();
    console.log(filterParams);
    $('#data-table').DataTable({
        scrollX: true,
        responsive: true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false, //to download all data
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 10,
        order: [1, 'desc'],
        ajax: {
            dataType: 'json',
            method: 'post',
            url: "/api/master-scholarship-criteria/get_scholarshipCriteria_list",
            data: function(d) {
                return $.extend({}, d, filterParams);
            }
        },
        columns: [
            {data: "scholarship_name"}, // Column 0
            {data: "sess_name"}, // Column 1
            {data: "fsc_roadmap_session_name"}, // Column 2
            {data: "school_name"}, // Column 3
            {data: "dept_name"}, // Column 4
            {data: "prog_name"}, // Column 5
            {data: null, // Column 6 - CGPA range
                render: function(data, type, row) {
                    return row.fsc_cgpa_from + ' - ' + row.fsc_cgpa_to;
                }
            },
            {data: null, // Column 7 - Percentage
                render: function(data, type, row) {
                    return row.fsc_percentage + '%';
                }
            },
            {data: null, // Column 8 - Edit/Delete buttons (rendered dynamically)
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;"
                            id="${row.fsc_id}" onclick="editRow('${row.fsc_id}', '${row.fsc_session_id}', '${row.fsc_school_id}', '${row.fsc_dept_id}', '${row.fsc_prog_id}', '${row.fsc_cgpa_from}', '${row.fsc_cgpa_to}', '${row.fsc_eff_from}', '${row.fsc_eff_to}', '${row.fsc_percentage}', '${row.fsc_scholarship_id}', '${row.fsc_degree_level}', '${row.fsc_roadmap_session_id}', '${row.fsc_roadmap}')">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>
                        <button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows([${row.fsc_id}])">
                            <i class="fa fa-trash"></i> Delete
                        </button>`;
                }
            },
            {data: null, // Column 9 - Checkbox (rendered dynamically)
                render: function(data, type, row) {
                    return `
                        <div style="margin-top:40px;">
                            <input name="fee_ids[]" class="select-data" idData="${row.fsc_id}" value="${row.fsc_id}" type="checkbox">
                        </div>`;
                }
            }
        ],
        columnDefs: [
            {
                targets: 8,
                orderable: false,
                searchable: false
            },
            {
                targets: 9,
                orderable: false,
                searchable: false
            }
        ],
        autoWidth: false,
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Scholarship Criteria'
            },
            {
                extend: 'pdfHtml5',
                title: 'Scholarship Criteria',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                title: 'Scholarship Criteria'
            },
            // {
            //     text: 'Export All Data (PDF)',
            //     action: function(e, dt, button, config) {
            //         pdfExportFunction(dt, config);
            //     }
            // }
        ]
    });
}




// function pdfExportFunction(dt, config) {
//     // Fetch all data
//     $.ajax({
//         url: "/api/master-scholarship-criteria/get_scholarshipCriteria_list",
//         method: 'POST',
//         success: function(response) {
//             const data = response.data;

//             // Define the table columns and rows
//             const columns = [
//                 'Scholarship', 'Session', 'School', 'Department', 'Program', 'CGPA Range', 'Percentage'
//             ];

//             // Create rows with validation
//             const rows = data.map((row, index) => {
//                 const rowData = [
//                     row.scholarship_name || 'N/A',
//                     row.sess_name || 'N/A',
//                     row.school_name || 'N/A',
//                     row.dept_name || 'N/A',
//                     row.prog_name || 'N/A',
//                     `${row.fsc_cgpa_from || 'N/A'} - ${row.fsc_cgpa_to || 'N/A'}`,
//                     `${row.fsc_percentage || 'N/A'}%`
//                 ];

//                 // Log each row data to check its structure
//                 if (rowData.length !== columns.length) {
//                     console.error(`Row ${index} does not match columns length`, rowData);
//                 }

//                 return rowData;
//             });

//             // Check if any row has undefined cells
//             rows.forEach((row, index) => {
//                 if (row.length !== columns.length) {
//                     console.error(`Row ${index} does not match columns length`, row);
//                 }
//             });

//             // Define equal column widths with more flexibility
//             const numColumns = columns.length;
//             const columnWidth = (690 / numColumns); // Adjust the total width if necessary
//             const columnWidths = Array(numColumns).fill(columnWidth);

//             // Define the document definition for pdfmake
//             const docDefinition = {
//                 content: [
//                     {
//                         text: 'Master Fee Structure',
//                         style: 'header'
//                     },
//                     {
//                         table: {
//                             headerRows: 1,
//                             widths: columnWidths, // Equal widths for all columns
//                             body: [
//                                 columns.map(col => ({ text: col, style: 'tableHeader' })), // Apply the tableHeader style
//                                 ...rows
//                             ]
//                         },
//                         layout: {
//                             fillColor: function(rowIndex) {
//                                 return rowIndex === 0 ? '#f3f3f3' : null; // Header row background color
//                             },
//                             hLineColor: function(i) {
//                                 return i === 0 ? 'black' : '#ddd'; // Header border color
//                             },
//                             vLineColor: function(i) {
//                                 return i === 0 ? 'black' : '#ddd'; // Border color
//                             },
//                             paddingLeft: function() { return 5; }, // Add padding to the left
//                             paddingRight: function() { return 5; } // Add padding to the right
//                         }
//                     }
//                 ],
//                 styles: {
//                     header: {
//                         fontSize: 12, // Adjust font size for the header
//                         bold: true,
//                         margin: [0, 0, 0, 10],
//                         alignment: 'center' // Center align the header text
//                     },
//                     tableHeader: {
//                         fontSize: 10, // Adjust font size for the table header
//                         bold: true,
//                         fillColor: '#f3f3f3', // Background color for table header
//                         alignment: 'left' // Align text to the left
//                     },
//                     table: {
//                         fontSize: 8 // Adjust font size for the table
//                     }
//                 },
//                 pageOrientation: 'landscape',
//                 pageSize: 'A4',
//                 pageMargins: [10, 10, 10, 10] // Adjust margins if necessary
//             };

//             // Create and download the PDF document
//             pdfMake.createPdf(docDefinition).download('scholarshipCriteria.pdf');
//         },
//         error: function(err) {
//             console.error('Error fetching data:', err);
//         }
//     });
// }





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
function exportToPDF(tableId){
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
function editRow(mainId, session, school, dept, prog, cgpaFrom, cgpaTo, effFrom, effTo, percentage, scholarship, degLevel,roadmap_session, roadmap){


    $('#main-Id').val(mainId);
    $('#session_sel').val(session);
    $('#school_sel').val(school);
    $('#program').val(prog);
    $('#roadmap_session_sel').val(roadmap_session);
    $('#scholarship_id').val(scholarship);
    $('#percent').val(percentage);
    $('#cgpaFrom').val(cgpaFrom);
    $('#cgpaTo').val(cgpaTo);
    $('input[name="roadmap"][value="' + roadmap + '"]').prop('checked', true).trigger('change');

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

    $('html, body').animate({scrollTop: 0}, 'slow');

}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/master-scholarship-criteria/delete_scholarshipCriteria",
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
                LoadScholarships();

            },
            error: function(error) {
                // Handle the error
                console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields(){
    $('#main-Id').val('');
    $('#session-wise').prop('checked', true);
        $('.session_sel_div').removeClass('d-none');//show session selection div
        $('.validity-type').val('session');//setting validityType value
        $('.year_sel_div').addClass('d-none'); //hiding thd year fields
        $('#year_sel').val('');//emptying the from date
    $('#session_sel').val('');
    $('#school_sel').val('');
    $('#scholarship_id').val('');
    $('#percent').val('');
    $('#department').val('');
    $('#degree_level').val('');
    $('#program').val('');
    $('#roadmap_session_sel').val('');
    $('input[name="roadmap"]').prop('checked', false);
    $('#cgpaFrom').val('');
    $('#cgpaTo').val('');
    $('#eff-from').val('');
    $('#eff-to').val('');

}
function validatePercentageInput(event) {
    const input = event.target;
    if (input.value !== '') {
        const value = parseInt(input.value, 10);
        if (value < 1 || value > 100) {
            $('.percent-disclaimer').text('Enter a value between 1 and 100');
            $('.percent-disclaimer').css('color', 'red');
            // input.setCustomValidity('Please enter a value between 1 and 100');
        } else {
            $('.percent-disclaimer').text('');
        }
    }
}
function validateCGPAInput(event) {
    const input = event.target;
    if (input.value !== '') {
        const value = parseFloat(input.value);
        console.log(value);
        if (value < 1 || value > 4) {
            $('.cgpaRange-disclaimer').text('Enter CGPA between 1 and 4');
            $('.cgpaRange-disclaimer').css('color', 'red');
            // input.setCustomValidity('Please enter a value between 1 and 100');
        } else {
            $('.cgpaRange-disclaimer').text('');
        }
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
function getFilterParams() {
    const validity_type = $('.validity-type').val();
    const year_sel = $('#year_sel').val();
    const session_sel = $('#session_sel').val();
    const school_sel = $('#school_sel').val();
    const scholarship_id = $('#scholarship_id').val();
    const percent = $('#percent').val();
    const department = $('#department').val();
    const degree_level = $('#degree_level').val();
    const roadmap = $('#roadmap:checked').val();
    const program = $('#program').val();
    const cgpaFrom = $('#cgpaFrom').val();
    const cgpaTo = $('#cgpaTo').val();

    let filterParams = {
        validity_type: validity_type,
        year_sel: year_sel,
        session_sel: session_sel,
        school_sel: school_sel,
        scholarship_id: scholarship_id,
        percent: percent,
        department: department,
        degree_level: degree_level,
        roadmap: roadmap,
        program: program,
        cgpaFrom: cgpaFrom,
        cgpaTo: cgpaTo
    };

    return filterParams;
}
// function setCgpaValue(cgpa, relation){
//     console.log(cgpa, relation)
//     cgpa = parseFloat(cgpa);
//     if(relation == 'greater_than' || relation == 'less_than'){
//         return cgpa;
//     }else if(relation == 'greater_or_equal'){
//         cgpa = parseFloat((cgpa - 0.01).toFixed(2));
//         return cgpa;
//     }else if(relation == 'less_or_equal'){
//         cgpa = parseFloat((cgpa + 0.01).toFixed(2));
//         return cgpa;
//     }
// }

// function setCgpaFromValue(cgpa,relation){
//     cgpa = parseFloat(cgpa);
//     cgpa = parseFloat((cgpa - 0.01).toFixed(2));
//     return cgpa;
// }
// function setCgpaToValue(cgpa,relation){
//     cgpa = parseFloat(cgpa);
//     cgpa = parseFloat((cgpa + 0.01).toFixed(2));
//     return cgpa;
// }
</script>

@stop

