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

    .container {
        background: #ffffff;
        border-radius: 10px;
        padding: 20px 50px !important;
        /* top-bottom = 20px, left-right = 50px */
        max-width: 100%;
    }

    .form-label {
        color: #333;
        /* text color */
        padding: 6px 12px;
        font-weight: 700;
        /* border-left: 5px solid #007bff; accent strip on the left */
        border-radius: 4px;
        margin-bottom: 6px;
    }

   .section-heading {
    position: relative;
    padding: 10px 15px;
    margin-bottom: 25px;
    font-size: 1.05rem;
    color: #0d6efd; /* Bootstrap primary */
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #e6eff9; /* light gray-blue background */
    border-left: 4px solid #0d6efd; /* accent border */
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}


    /* Optional subtle background highlight for contrast between sections */
    .section-heading::before {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #0d6efd;
        border-radius: 2px;
    }

    /* Modern checkbox-style radios */
.styled-check {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.styled-check:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    margin-left: 4px;
    font-weight: 500;
    cursor: pointer;
}

.status-option {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.status-option:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    margin-left: 4px;
    font-weight: 500;
    cursor: pointer;
}

.donation-option {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.donation-option:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    margin-left: 4px;
    font-weight: 500;
    cursor: pointer;
}

</style>
@section('content')
    {{-- ---------------------- --}}
    <div id="success-flag" class="success-flag"></div>
    <div class="card" style="padding:35px;">
        <form id="donation_form" method="POST" action="">
            @csrf
            <div class="row main-heading ">
                    <div class="col-12">
                    <h3>Donation Record Workflow Form:</h3>
                </div>
            </div>

            <!-- 1. Event Details -->
            <h5 class="section-heading mt-3">Donor Information</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="f_donor_name" class="form-label">First Name:</label>
                    <input type="text" class="form-control" name="f_donor_name" id="f_donor_name" placeholder="Type First Name">
                    <span class="text-danger error-text f_donor_name_error"></span>
                </div>
                <div class="col-md-3">
                    <label for="l_donor_name" class="form-label">Last Name:</label>
                    <input type="text" class="form-control" name="l_donor_name" id="l_donor_name" placeholder="Type Last Name">
                    <span class="text-danger error-text l_donor_name_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_email" class="form-label">Donor Email:</label>
                    <input type="email" class="form-control" name="donor_email" id="donor_email" placeholder="example@gmail.com">
                    <span class="text-danger error-text donor_email_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_number" class="form-label">Donor Phone Number:</label>
                    <input type="text" class="form-control" name="donor_number" id="donor_number" placeholder="Type Donor Phone Number">
                    <span class="text-danger error-text donor_number_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_account" class="form-label">Donor Account Details:</label>
                    <input type="text" class="form-control" name="donor_account" id="donor_account" placeholder="Type Donor Account Details">
                    <span class="text-danger error-text donor_account_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_ntn" class="form-label">Donor NTN Number:</label>
                    <input type="text" class="form-control" name="donor_ntn" id="donor_ntn" placeholder="Type Donor NTN Number">
                    <span class="text-danger error-text donor_ntn_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_cnic" class="form-label">Donor CNIC:</label>
                    <input type="text" class="form-control" name="donor_cnic" id="donor_cnic" placeholder="Type Donor CNIC">
                    <span class="text-danger error-text donor_cnic_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="donor_address" class="form-label">Donor Address:</label>
                    <input type="text" class="form-control" name="donor_address" id="donor_address" placeholder="Type Donor Address">
                    <span class="text-danger error-text donor_address_error"></span>
                </div>
                <div class="col-md-3">
                    <label for="donor_country" class="form-label">Donor Country:</label>
                    <input type="text" class="form-control" name="donor_country" id="donor_country" placeholder="Type Donor Country">
                    <span class="text-danger error-text donor_country_error"></span>
                </div>
                <div class="col-md-3">
                    <label for="donor_city" class="form-label">Donor City:</label>
                    <input type="text" class="form-control" name="donor_city" id="donor_city" placeholder="Type Donor city">
                    <span class="text-danger error-text donor_city_error"></span>
                </div>
            </div>

            <!-- 2. Schedule -->
            <div>
                <h5 class="section-heading mt-4">Donation Category</h5>
                <div class="row g-3">
                <div class="col-md-6">
                    <label for="donor_session" class="form-label">Session:</label>
                   <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                            <option value="">Select Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    <span class="text-danger error-text session_error"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Type of Donation:</label>

                   <div class="form-check form-check-inline">
                        <input class="form-check-input donation-option" type="radio" name="donation_type"
                            id="donZakat" value="Zakat">
                        <label class="form-check-label" for="donZakat">Zakat</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input donation-option" type="radio" name="donation_type"
                            id="donScholarship" value="General Scholarship Fund">
                        <label class="form-check-label" for="donScholarship">General Scholarship Fund</label>
                    </div>
                         <span class="text-danger error-text donation_type_error"></span>
                </div>
            </div>
            <div>
                <h5 class="section-heading mt-4">Payment Details</h5>
                <div class="row g-3">
                <div class="col-md-6">
                    <label for="amount_donated" class="form-label">Amount Donated: <small class="text-muted">(Enter amount in PKR)</small></label>
                    <input type="text" class="form-control" name="amount_donated" id="amount_donated" placeholder="PKR">
                    <span class="text-danger error-text amount_donated_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="bank_deposited" class="form-label">Bank Deposited in:</label>
                    <input type="text" class="form-control" name="bank_deposited" id="bank_deposited" placeholder="Bank Deposited">
                    <span class="text-danger error-text bank_deposited_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="bank_branch" class="form-label">Bank Branch (if Applicable):</label>
                    <input type="text" class="form-control" name="bank_branch" id="bank_branch" placeholder="Bank Branch">
                    <span class="text-danger error-text bank_branch_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="date_deposit" class="form-label">Date of Deposit:</label>
                    <input type="date" class="form-control" name="date_deposit" id="date_deposit">
                    <span class="text-danger error-text date_deposit_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="E_EVENT_NAME" class="form-label">Deposit Slip / Proof Attached:</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input styled-check" type="radio" name="deposit_type"
                        id="optZakat" value="Zakat">
                        <label class="form-check-label" for="optZakat">Zakat</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input styled-check" type="radio" name="deposit_type"
                        id="optGeneralScholarship" value="General Scholarship Fund">
                        <label class="form-check-label" for="optGeneralScholarship">General Scholarship Fund</label>
                    </div>
                    <span class="text-danger error-text deposit_type_error"></span>

                </div>
            </div>
            <div>
                <h5 class="section-heading mt-4">Student-Specific Allocation (If Applicable)</h5>
                <div class="row g-3">
                <div class="col-md-6">
                    <label for="student_name" class="form-label">Student Name:</label>
                    <input type="text" class="form-control" name="student_name" id="student_name" placeholder="Student Name">
                    <span class="text-danger error-text student_name_error"></span>
                </div>

                <div class="col-md-6">
                    <label for="registration_number" class="form-label">Registration Number:</label>
                    <input type="text" class="form-control" name="registration_number" id="registration_number" placeholder="Type Registration Number">
                    <span class="text-danger error-text registration_number_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="student_program" class="form-label">Program / Department:</label>
                    <input type="text" class="form-control" name="student_program" id="student_program" placeholder="Type Program / Department">
                    <span class="text-danger error-text student_program_error"></span>
                </div>
                <div class="col-md-6">
                    <label for="student_session" class="form-label">Session:</label>
                     <select name="student_session" id="student_session" class="form-control" onchange="on_session_change(this);">
                            <option value="">Select Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    <span class="text-danger error-text student_session_error"></span>
                </div>
                <div class="col-md-12">
                    <label for="student_allocation" class="form-label">Purpose / Allocation Details:</label>
                    <input type="text" class="form-control" name="student_allocation" id="student_allocation" placeholder="Type Purpose / Allocation Details">
                    <span class="text-danger error-text student_allocation_error"></span>
                </div>
            </div>

            <div>
                    <h5 class="section-heading mt-4">Internal Verification & Record Update</h5>
                    <div class="row g-3">
                    <div class="col-md-6">
                        <label for="internal_verification" class="form-label">Verified By (Finance):</label>
                        <input type="text" class="form-control" name="internal_verification" id="internal_verification" placeholder="Verified By">
                        <span class="text-danger error-text internal_verification_error"></span>
                    </div>
                    <div class="col-md-6">
                        <label for="internal_date" class="form-label">Date of Verification:</label>
                        <input type="date" class="form-control" name="internal_date" id="internal_date">
                        <span class="text-danger error-text internal_date_error"></span>
                    </div>
                    <div class="col-md-12">
                        <label for="internal_remarks" class="form-label">Remarks (If Any):</label>
                        <input type="text" class="form-control" name="internal_remarks" id="internal_remarks" placeholder="Type Remarks">
                        <span class="text-danger error-text internal_remarks_error"></span>
                    </div>

                </div>
            </div>
            <div>
                <h5 class="section-heading mt-4">Final Status</h5>
                <div class="form-check form-check-inline">
                    <input class="form-check-input status-option" type="radio" name="final_status"
                        id="status_saved" value="Entry Saved in Records">
                    <label class="form-check-label" for="status_saved">Entry Saved in Records</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input status-option" type="radio" name="final_status"
                        id="status_updated" value="Updated in Scholarship Ledger">
                    <label class="form-check-label" for="status_updated">Updated in Scholarship Ledger</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input status-option" type="radio" name="final_status"
                        id="status_acknowledged" value="Acknowledgment Sent to Donor">
                    <label class="form-check-label" for="status_acknowledged">Acknowledgment Sent to Donor</label>
                </div>
                 <span class="text-danger error-text final_status_error"></span>

            </div>
            <!-- Hidden System Fields -->
            <input type="hidden" id='main-Id' name="mainId" value="">
            <div class="form-group col-sm-12 mt-5" style="display: flex;justify-content: center;">
                <button type="button" style="width: 150px;" id="saveButton"
                    class="btn btn-primary">Save</button>
                {{-- <button type="button" style="width: 150px;margin-left:50px;" id="filterButton"
                    class="btn btn-primary">Apply Filter</button> --}}
                <button type="button" style="width: 100px;margin-left:50px;" id="clear"
                    class="btn btn-secondary" onclick="refreshFields()">Clear</button>
            </div>
            <div id="processing_message" class="alert"
                style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>
    </div>

        <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card" style="padding:20px;">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">id</th>
                                    <th scope="col">Donor Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Amount
                                    <th scope="col">Donation Type</th>
                                    <th scope="col">Source</th>
                                    <th scope="col">Challan ID</th>
                                    <th scope="col">Challan Status</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>

                        </table>

                    </div>
                </div>
                {{-- <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                  <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
                </div>
            </div> --}}
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
        var form = document.getElementById("donation_form");
        var formData = new FormData(form);

        $("#processing_message").show();
        fetch('add-donation-record', {
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
                    LoadDonationList();
                } else {
                    // validation errors
                    $('span.error-text').text(''); // Clear old error messages

                    if ('error' in data) {
                        var errors = data.error; // { donor_name: [..], donor_email: [..], ... }
                        // console.log(errors);
                        $.each(errors, function (field, messages) {
                             console.log(field+'->'+messages);
                            // Requires <span class="donor_name_error error-text"></span> etc.
                            $('span.' + field + '_error').text(messages[0]);
                        });
                    }

                    $("#processing_message").hide();
                    $('#success_message').text('').hide();
                }
            })
            .catch(error => {
                $("#processing_message").hide();
                // console.error('There was an error!', error);
            });
    });

    var lastURL = '';
    $(document).ready(function() {
        LoadDonationList();
    $(document).on('click', '#filterButton', function() {
        LoadDonationList();
    });
    });
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script>
    function LoadDonationList() {
        // let filterParams = getFilterParams();
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
            order: [[0, 'desc']],
            ajax: {
                dataType: 'json',
                method: 'post',
                url: "/api/get_donation_list",
                // data: function(d) {
                //     return $.extend({}, d, filterParams);
                // }
            },
            columns: [
                {
                    data: "donation_id",
                    name: "donation_id",
                    visible: false,      // hide column
                    searchable: false
                },
                {
                    data: "first_name",
                    name: "first_name",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "email",
                    name: "email",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "phone",
                    name: "phone",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "amount",
                    name: "amount",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "donation_type",
                    name: "donation_type",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "source_type",
                    name: "source_type",
                    render: function (data, type, row) {
                        console.log(data)
                        if (!data) return "NA";

                        if (data.toLowerCase() === "donation_website") {
                            return "Donation Website";
                        }

                        if (data.toLowerCase() === "cms") {
                            return "Manual Entry";
                        }

                        return data; // fallback for any other type
                    }
                },
                {
                    data: "challan_id",
                    name: "challan_id",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "sc_challan_status",
                    name: "sc_challan_status",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                },
                {
                    data: "payment_method",
                    name: "payment_method",
                    render: function (data, type, row) {
                        return data ? data : "NA";
                    }
                }
            ],

            columnDefs: [{
                render: function (data, type, row) {


                    let buttons='';
                    // Convert row to JSON
                    let rowJson = JSON.stringify(row);

                    // Encode to Base64 so no quotes/brackets break onclick
                    let encoded = btoa(rowJson);
                        // Only show buttons if SOURCE_TYPE is 'cms'
                    if (row.source_type !== 'cms') {
                        buttons = `
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="btn btn-primary btn-sm edit-btn"
                                        onclick="editRow('${encoded}')">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-success btn-sm ml-2" onclick="exportToPDF(${row.donation_id})">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>

                                </div>
                            `;

                    }else{
                        buttons = `
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="btn btn-primary btn-sm edit-btn"
                                        onclick="editRow('${encoded}')">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                        <button class="btn btn-success btn-sm ml-2" onclick="exportToPDF(${row.donation_id})">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                        onclick="deleteRows(${row.donation_id})">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            `;

                    }


                    return buttons;
                },
                    targets: 10,
                },
                {
                    "width": "100px",
                    "targets": 1
                },
                 {
                    "width": "50px",
                    "targets": 2
                },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Donation List',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9] // exclude index 0 and 10
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Donation List',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9]
                    }

                },


                {
                    extend: 'print',
                    title: 'Donation List',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9]
                    }
                }
            ]
        });
    }
    function exportToPDF(donation_id) {
        let url = "{{ config('app.app_url') }}";
        var link = `${url}/assets/cms/reports/donation_record_report.php?id=${donation_id}`;
        window.open(link, '_blank');
        event.preventDefault();
    }
    function editRow(encodedRow) {

            // Decode Base64 → JSON
            let rowJson = atob(encodedRow);

            // Parse JSON
            let row = JSON.parse(rowJson);
            console.log(row.city);
            // // ==============================
            // // FULL CONSOLE LOG FOR VERIFICATION
            // // ==============================
            // console.log("===== DONATION RECORD DATA RECEIVED =====");
            // Object.entries(row).forEach(([key, value]) => {
            //     console.log(key + ":", value);
            // });
            // console.log("========================================");

            // Helper to clean null/'null'/undefined
            function clean(value) {
                if (value === null || value === undefined) return '';
                if (typeof value === 'string' && value.toLowerCase() === 'null') return '';
                return value;
            }
            $('#main-Id').val(clean(row.donation_id));

            
            $('#f_donor_name').val(row.first_name.trim());
            $('#l_donor_name').val(row.last_name.trim());

            $('#donor_email').val(clean(row.email));
            $('#donor_number').val(clean(row.phone));
            $('#donor_account').val(clean(row.donor_account));
            $('#donor_ntn').val(clean(row.donor_ntn));
            $('#donor_cnic').val(clean(row.cnic));
            $('#donor_address').val(clean(row.donor_address));
            $('#donor_country').val(clean(row.country));
            $('#donor_city').val(clean(row.city));

            $('#session_sel').val(clean(row.session_id));
            $('#amount_donated').val(clean(row.amount || row.amount_donated));

            $('#bank_deposited').val(clean(row.bank_deposited));
            $('#bank_branch').val(clean(row.bank_branch));

            $('#date_deposit').val(row.date_deposit ? row.date_deposit.substring(0, 10) : '');

            $('#student_name').val(clean(row.student_name));
            $('#registration_number').val(clean(row.enrollment_no || row.registration_number));
            $('#student_program').val(clean(row.student_program));
            $('#student_session').val(clean(row.student_session));
            $('#student_allocation').val(clean(row.student_allocation));

            $('#internal_verification').val(clean(row.internal_verification));
            $('#internal_date').val(row.internal_date ? row.internal_date.substring(0, 10) : '');
            $('#internal_remarks').val(clean(row.internal_remarks));

            $('#source_type').val(clean(row.source_type));
            $('#challan_id').val(clean(row.challan_id));
            $('#payment_method').val(clean(row.payment_method));

            // --- Set Donation Type Radio Button ---
            $('#donation_type').val(clean(row.donation_type));
            if (row.donation_type) {
                $("input[name='donation_type'][value='" + row.donation_type + "']").prop("checked", true);
            } else {
                $("input[name='donation_type']").prop("checked", false); // clear if empty
            }
            // Set Final Status radio button
            $('#final_status').val(clean(row.final_status));
            if (row.final_status) {
                $("input[name='final_status'][value='" + row.final_status + "']").prop("checked", true);
            } else {
                $("input[name='final_status']").prop("checked", false);
            }
            //Set Deposit Type radio button
            $('#deposit_type').val(clean(row.deposit_type));
            if (row.deposit_type) {
                $("input[name='deposit_type'][value='" + row.deposit_type + "']").prop("checked", true);
            } else {
                $("input[name='deposit_type']").prop("checked", false);
            }




            $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
    function deleteRows(mainId) {
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/api/delete_donation_rec",
                data: {
                    mainId: mainId
                },
                success: function(response) {
                    refreshFields();
                    $('.select-data').prop('checked', false);
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadDonationList();
                },
                error: function(error) {
                    // Handle the error
                    // console.error("Error:", error);
                }
            });
        } else {

        }

    }
    function refreshFields() {
        // Hidden ID
        $('#main-Id').val('');

        // Donor details
        $('#f_donor_name').val('');
        $('#l_donor_name').val('');
        $('#donor_email').val('');
        $('#donor_number').val('');
        $('#donor_account').val('');
        $('#donor_ntn').val('');
        $('#donor_cnic').val('');
        $('#donor_address').val('');
        $('#donor_country').val('');
        $('#donor_city').val('');

        // Session & amount
        $('#session_sel').val('');
        $('#amount_donated').val('');

        // Bank details
        $('#bank_deposited').val('');
        $('#bank_branch').val('');
        $('#date_deposit').val('');

        // Student info
        $('#student_name').val('');
        $('#registration_number').val('');
        $('#student_program').val('');
        $('#student_session').val('');
        $('#student_allocation').val('');

        // Internal verification
        $('#internal_verification').val('');
        $('#internal_date').val('');
        $('#internal_remarks').val('');

        // Extra fields
        $('#source_type').val('');
        $('#challan_id').val('');
        $('#payment_method').val('');

        // -------- Radio Buttons --------

        // Donation Type
        $("input[name='donation_type']").prop("checked", false);

        // Final Status
        $("input[name='final_status']").prop("checked", false);

        // Deposit Type
        $("input[name='deposit_type']").prop("checked", false);

    }
    // function getFilterParams() {
    //     const school_sel = $('#school_sel').val();
    //     const department = $('#department').val();
    //     const url = $('#E_URL').val();
    //     const status = $('#E_STATUS').val();
    //     const reg_fee = $('#E_REGISTRATION_FEE').val();
    //     const event_type = $('#E_EVENT_TYPE').val();
    //     let filterParams = {
    //         school_sel: school_sel,
    //         department: department,
    //         url: url,
    //         status: status,
    //         reg_fee: reg_fee,
    //         event_type: event_type
    //     };
    //     return filterParams;
    // }
</script>

@stop
