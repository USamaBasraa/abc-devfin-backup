@extends('layouts.app')
@section('title')
BNU CMS
@stop

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
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
        content: '\2713';
        /* Unicode checkmark symbol */
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

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        margin-top: 5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }

    #clear {
        background-color: #25a4fe;
        border-color: #25a4fe;

    }

    #clear:hover {
        background-color: #1b82cb;
        /* New color on hover */
        border-color: #1b82cb;
        /* Matching border color on hover */
    }

    .edit-btn,
    .delete-btn {
        width: 76px;
    }

    .parent-label {
        font-size: 1.25rem;
        /* Adjust size as needed */
        font-weight: bold;
    }
</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
    {{-- <div class="card-header" style="width:100%;"> --}}
        <form id="feeForm" method="POST" action="" style='padding:25px;'>
            @csrf
            <div class="row">
                <h3>Hostel Fee Structure Form:</h3>
            </div>

            <div class="row filter-row formDiv">
                <div class="form-group col-sm-2">
                    <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                    <label for="session_sel">Session</label>
                    <select name="session" id="session_sel" class="form-control">
                        <option value="">Select Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label for="session_type">Session Type</label>
                    <select name="session_type" id="session_type" class="form-control">
                        <option value="">Select Type</option>
                        <option value="Regular">Regular</option>
                        <option value="Non-Regular">Non-Regular</option>
                        <option value="Regular_outside">Regular Safari Campus</option>
                        <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                        <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                        <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                    </select>
                </div>
                <div class="form-group col-sm-2">
                    <label for="student_type">Student Type</label>
                    <select name="student_type" id="student_type" class="form-control">
                        <option value="">Select Type</option>
                        <option value="Existing">Existing</option>
                        <option value="New_Admission">New Applicant</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="studentType">Effective From</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                </div>
                <div class="form-group col-sm-3">
                    <label for="studentType">Effective To</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-sm-6">
                    <label class="parent-label">Hostel Fee Details</label>
                    <div class="row" style="margin-top:10px;">
                        <div class="form-group col-sm-6">
                            <label for="amount_per_day">Amount per day</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="amount_per_day"
                                id="amount_per_day" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="amount_per_month">Amount per month</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="amount_per_month"
                                id="amount_per_month" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="amount_per_semester">Amount per semester</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="amount_per_semester"
                                id="amount_per_semester" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="hostel_security_fee">Hostel Security Fee</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="hostel_security_fee"
                                id="hostel_security_fee" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <label class="parent-label">Facilities Fee Details</label>
                    <div class="row" style="margin-top:10px;">
                        <div class="form-group col-sm-6">
                            <label for="refrigerator_fee">Room Refrigerator Fee</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="refrigerator_fee"
                                id="refrigerator_fee" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="heater_fee">Room Electric Heater Fee</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="heater_fee"
                                id="heater_fee" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="ac_fee">Air Conditioner Fee</label>
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="ac_fee" id="ac_fee"
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>



            <div class="form-group col-sm-12" style="display: flex;justify-content: center; margin-top:20px;">
                <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                <button type="button" style="width: 150px;margin-left:50px;" id="filterButton"
                    class="btn btn-primary">Apply Filter</button>
                <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary"
                    onclick="refreshFields()">Clear</button>
            </div>
            <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing,
                please wait...</div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>


        {{--
    </div> --}}
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Session</th>
                                <th scope="col">Type</th>
                                <th scope="col">Student Type</th>
                                <th scope="col">Effective From</th>
                                <th scope="col">Effective To</th>
                                <th scope="col">Amount per day</th>
                                <th scope="col">Amount per month</th>
                                <th scope="col">Amount per semester</th>
                                <th scope="col">Hostel Security Fee</th>
                                <th scope="col">Refrigerator Fee</th>
                                <th scope="col">Electric Heater Fee</th>
                                <th scope="col">Air Conditioner Fee</th>
                                <th scope="col">Action</th>
                                <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" /></th>


                            </tr>
                        </thead>

                    </table>

                </div>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                    <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk
                        Delete</button>
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

    document.getElementById('saveButton').addEventListener('click', function () {
        var form = document.getElementById("feeForm");
        var formData = new FormData(form);
        // formData.append('school', $('#school_sel').val());
        // formData.append('department', $('#department').val());
        // formData.append('program', $('#program').val());
        // formData.append('session', $('#session_sel').val());
        // formData.append('roadmap_session', $('#roadmap_session_sel').val());
        // formData.append('student_type', $('#studentTypeOption').val());
        // formData.append('fee_type', $('#feeType').val());
        // formData.append('effective_from', $('#eff-from').val());
        // formData.append('effective_to', $('#eff-to').val());
        // formData.append('tution_amount', $('#tutionAmt').val());
        // formData.append('admission_amount', $('#admissionAmt').val());
        // formData.append('security_amount', $('#securityAmt').val());
        // formData.append('medical_amount', $('#medicalAmt').val());
        // formData.append('activity_charges_amount', $('#activitychargesAmt').val());
        // formData.append('per_credit_fee', $('#credFee').val());
        // formData.append('hostel_amount', $('#hostelAmt').val());
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
        fetch('addHostelFeeStructure', {
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
                console.error('There was an error!', error);
            });
    });

    var lastURL = '';
    $(document).ready(function () {
        LoadFee();
        $('#feeType').change(function () {
            if ($(this).val() == 'Hostel_fee') {
                $('.credFeeDiv').addClass('d-none');
                $('#credFee').val('');
            } else if ($(this).val() == 'Credit_hours') {
                $('.credFeeDiv').removeClass('d-none');
            } else {
                $('.credFeeDiv').addClass('d-none');
                $('#credFee').val('');

            }
        });
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
        $('#studentTypeOption').change(function () {
            if ($(this).val() != 'Existing') {
                $('.admissionFeeDiv').removeClass('d-none');
                $('.securityAmtDiv').removeClass('d-none');
            } else {
                $('.admissionFeeDiv').addClass('d-none');
                $('#admissionAmt').val('');
                $('.securityAmtDiv').addClass('d-none');
                $('#securityAmt').val('');

            }
        });
        $(document).on('change', '#select-all', function () {
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
        $(document).on('click', '#bulk-Del', function () {
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
        $(document).on('click', '#filterButton', function () {
            LoadFee();
        });
    });


</script>

{{--
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    function LoadFee() {
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
            ajax: {
                dataType: 'json',
                method: 'post',
                url: "/api/hostelFeeStructure/get_hostelFee_list",
                data: function (d) {
                    return $.extend({}, d, filterParams);
                }
            },
            columns: [
                { data: "sess_name", name: "sess_name" },
                {
                    data: "fhfs_session_type",
                    name: "fhfs_session_type",
                    render: function (data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                {
                    data: "fhfs_student_type",
                    name: "fhfs_student_type",

                },
                {
                    data: "fhfs_effective_from", name: "fhfs_effective_from",
                    render: function (data, type, row) {

                        const datePart = data.split(' ')[0];

                        return datePart;
                    }
                },
                {
                    data: "fhfs_effective_to", name: "fhfs_effective_to",
                    render: function (data, type, row) {

                        const datePart = data.split(' ')[0];

                        return datePart;
                    }
                },
                {
                    data: "fhfs_amount_per_day",
                    name: "fhfs_amount_per_day",

                },
                {
                    data: "fhfs_amount_per_month",
                    name: "fhfs_amount_per_month",

                },
                {
                    data: "fhfs_amount_per_semester",
                    name: "fhfs_amount_per_semester",

                },
                {
                    data: "fhfs_hostel_security_fee",
                    name: "fhfs_hostel_security_fee",


                },

                {
                    data: "fhfs_refrigerator_fee",
                    name: "fhfs_refrigerator_fee",

                },
                {
                    data: "fhfs_heater_fee",
                    name: "fhfs_heater_fee",

                },
                {
                    data: "fhfs_ac_fee",
                    name: "fhfs_ac_fee",

                },
                {
                    data: null,
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                },
                {
                    data: null,
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                },

            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        console.log(row);
                        var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="' + row["fhfs_id"] + '" onclick="editRow(`' + row["fhfs_id"] + '`,`' + row["fhfs_session"] + '`,`' + row["fhfs_effective_from"] + '`,`' + row["fhfs_effective_to"] + '`,`' + row["fhfs_amount_per_day"] + '`,`' + row["fhfs_amount_per_month"] + '`,`' + row["fhfs_amount_per_semester"] + '`,`' + row["fhfs_refrigerator_fee"] + '`,`' + row["fhfs_heater_fee"] + '`,`' + row["fhfs_ac_fee"] + '`,`' + row["fhfs_hostel_security_fee"] + '`,`' + row["fhfs_session_type"] + '`,`' + row["fhfs_student_type"] + '`)"><i class="fa fa-edit"></i> Edit</button>' +
                            '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                            '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows([' + row["fhfs_id"] + '])"><i class="fa fa-trash"></i> Delete</button></div>';
                        return button;

                    },
                    "width": "80px",
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 12,
                },
                {
                    render: function (data, type, row) {
                        var selectbtn = '<div style="margin-top:40px; display: flex; justify-content: center;">' +
                            '<input name="fee_ids[]" class="select-data" idData=' + row["fhfs_id"] +
                            ' value=' + row["fhfs_id"] + ' type="checkbox" >' +
                            '</div>';

                        return selectbtn;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 13,
                    searchable: false,
                    orderable: false
                },

                // {
                //     "width": "100px", "targets": 1
                // },
                // {
                //     "width": "5px", "targets": 14
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Hostel Fee Structure',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] // Include all columns except the delete button (index 9)
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Hostel Fee Structure',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] // Include all columns except the delete button (index 9)
                    }

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
                    title: 'Hostel Fee Structure',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] // Include all columns except the delete button (index 9)
                    }
                }
            ]
        });
    }

    function editRow(mainId, session, effFrom, effTo, amount_per_day, amount_per_month, amount_per_semester, refrigerator_fee, heater_fee, ac_fee, hostel_security_fee, sessionType, studentType) {

        // console.log('mainId -> ' + mainId);
        // console.log('session -> ' + session);
        // console.log('from -> ' + effFrom);
        // console.log('to -> ' + effTo);
        // console.log('amount_per_day -> ' + amount_per_day);
        // console.log('amount_per_month -> ' + amount_per_month);
        // console.log('amount_per_semester -> ' + amount_per_semester);
        // console.log('refrigerator_fee -> ' + refrigerator_fee);
        // console.log('heater_fee -> ' + heater_fee);
        // console.log('ac_fee -> ' + ac_fee);

        $('#main-Id').val(mainId);
        $('#hostel_security_fee').val(hostel_security_fee);
        $('#session_sel').val(session);
        $('#session_type').val(sessionType);
        $('#student_type').val(studentType);
        var eff_from_datePart = effFrom.split(' ')[0];
        $('#eff-from').val(eff_from_datePart);

        var eff_to_datePart = effTo.split(' ')[0];
        $('#eff-to').val(eff_to_datePart);

        $('#amount_per_day').val(amount_per_day);
        $('#amount_per_month').val(amount_per_month);
        $('#amount_per_semester').val(amount_per_semester);
        $('#refrigerator_fee').val(refrigerator_fee);
        $('#heater_fee').val(heater_fee);
        $('#ac_fee').val(ac_fee);

        focusHostelFeeForm();
    }

    function focusHostelFeeForm() {
        const form = document.getElementById('feeForm');
        const scrollContainer = document.querySelector('.app-content');

        if (!form) {
            return;
        }

        if (scrollContainer) {
            const containerTop = scrollContainer.getBoundingClientRect().top;
            const formTop = form.getBoundingClientRect().top;
            const scrollOffset = formTop - containerTop + scrollContainer.scrollTop - 20;

            scrollContainer.scrollTo({
                top: Math.max(0, scrollOffset),
                behavior: 'smooth'
            });
        } else {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        setTimeout(function () {
            $('#session_sel').trigger('focus');
        }, 350);
    }
    function deleteRows(mainId) {
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/api/hostelFeeStructure/delete_hostelfeeData",
                data: {
                    mainId: mainId
                },
                success: function (response) {
                    $('.select-data').prop('checked', false);
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function () {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadFee();
                },
                error: function (error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }
    function refreshFields() {
        $('#main-Id').val('');
        $('#session_sel').val('');
        $('#session_type').val('');
        $('#eff-from').val('');
        $('#eff-to').val('');
        $('#amount_per_day').val('');
        $('#amount_per_month').val('');
        $('#amount_per_semester').val('');
        $('#refrigerator_fee').val('');
        $('#heater_fee').val('');
        $('#ac_fee').val('');
        $('#hostel_security_fee').val('');
    }
    function getFilterParams() {
        const session_sel = $('#session_sel').val();
        const session_type = $('#session_type').val();
        const from = $('#eff-from').val();
        const to = $('#eff-to').val();
        const perday = $('#amount_per_day').val();
        const parmonth = $('#amount_per_month').val();
        const persemester = $('#amount_per_semester').val();
        const fridge = $('#refrigerator_fee').val();
        const heater = $('#heater_fee').val();
        const ac = $('#ac_fee').val();
        let filterParams = {
            session_sel: session_sel,
            session_type: session_type,
            student_type: $('#student_type').val(),
            eff_from: from,
            eff_to: to,
            amount_per_day: perday,
            amount_per_month: parmonth,
            amount_per_semester: persemester,
            refrigerator_fee: fridge,
            heater_fee: heater,
            ac_fee: ac
        };
        return filterParams;
    }

</script>

@stop
