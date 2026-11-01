@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables Bootstrap4 CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}"><!-- dataTables.bootstrap4.min-->

    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap4.min.css') }}"><!-- responsive.bootstrap4.min-->

    <!-- jQuery DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}"><!-- jquery.dataTables.min-->

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}"><!-- buttons.dataTables.min--> --}}
</head>
@section('content')
<style>
    .checkboxOptions{
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-left: 10px;
        margin-top: 30px;
    }

    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        margin-right: 15px;
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
        /* Change background color of the dropdown toggle button */
        .btn.dropdown-toggle.bs-placeholder.btn-light,
    .btn.dropdown-toggle.btn-light,
    .btn-light {
        background-color: white !important;
        color: black !important;
        border-color: #ced4da !important; /* Matching Bootstrap's default border color */
    }

    /* Change background color of the dropdown menu */
    .dropdown-menu {
        background-color: white !important;
    }

    /* Change text color of the dropdown items */
    .dropdown-item {
        color: black !important;
    }

    /* Optional: Change the hover state */
    .dropdown-item:hover {
        background-color: #f8f9fa !important;
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
        width:120px;
        padding:4px;
    }
    #modal-1 .modal-dialog {
        margin: 1.75rem auto;            /* Bootstrap's default: vertical space, horizontal auto = centered */
        display: flex;
        align-items: center;             /* For vertical centering (if modal-dialog-centered is used) */
        min-height: calc(100vh - 3.5rem);/* Ensures vertical centering even for tall modals */
    }

    #modal-1 .modal-content {
        width: 500px;
        max-width: 90vw;
        transition: width 0.3s;
        margin: 0 auto;                  /* Ensures .modal-content stays centered in .modal-dialog */
    }

    #modal-1 .modal-content.wide-modal {
        width: 900px;
        max-width: 98vw;
        margin: 0 auto;
    }


</style>
<div id="success-flag" class="success-flag"></div>

<div class="card">
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <form id="subcriptionForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Students Subscriptions:</h3></div>
                <div class="row filter-row" style="margin-right:10px;">

                    <div class="form-group col-sm-3">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            <option value="">Select Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="school_sel">Category Type:</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Select Category type</option>
                            <option value="Gym">Gym</option>
                            <option value="Hostel">Hostel</option>
                            <option value="Lab">Lab</option>
                        </select>
                    </div>
                     <div class="form-group col-sm-3 d-none">
                        <label for="usertype">User Type:</label>
                        <select name="user_type" id="user_type" class="form-control">
                            <option value="">Select User type</option>
                            <option value="Student">Student</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="plan">Plan Tenure:</label>
                        <select name="plan" id="plan" class="form-control">
                            <option value="">Select plan</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Semester">Semester</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="payment">Payment Status:</label>
                        <select name="payment_status" id="payment_status" class="form-control">
                            <option value="">Select Payment Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="payment">Subscription Status:</label>
                        <select name="subscription_status" id="subscription_status" class="form-control">
                            <option value="">Select Subscription Status</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Declined">Declined</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary" onclick="LoadSubscriptions()">Filter</button>
                    <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">Clear</button>
                </div>

        </form>
    </div>
    <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                {{-- <div class="card"> --}}
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display">
                            <thead>
                            <tr>
                                <th scope="col">User Id</th>
                                <th scope="col">Session</th>
                                <th scope="col">Name</th>
                                {{-- <th scope="col">User Type</th> --}}
                                {{-- <th scope="col">Category</th> --}}
                                {{-- <th scope="col">Fee</th> --}}
                                <th scope="col">Plan Type</th>
                                <th scope="col">Subscription Status</th>
                                <th scope="col">Activation Date</th>
                                <th scope="col">Expiry Date</th>
                                <th scope="col">Challan No</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Challan Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                {{-- </div> --}}
            </div>
    </div>
</div>
<div id="modal-1" class="modal fade" style="background-color: rgba(255, 255, 255, 0.2);" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ededed;">
                <h1 class="modal-title" style="font-size: 25px;">Action</h1>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-1" method="POST" action="" data-toggle="validator">
                    @csrf
                    <div class="row" style="margin-top: 12px;">
                        <input hidden id="main_id" name="main_id" value="">
                        <input hidden id="challan_status" name="challan_status" value="">
                        <input hidden id="plan_tenure" name="plan_tenure" value="">
                        <input hidden id="enrollment" name="userId_enrollment" value="">
                        <div class="col-md-4 text-left">
                            <label for="status-dropdown" class="col-form-label">Subscription Status:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <select class="form-control" id="subStatus-dropdown" name="status">


                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                        <button type="button" id="updateBtn" class="btn btn-primary">Update</button>
                    </div>
                </form>
                <div id="gym-fee-table-wrapper" style="display:none; margin-bottom: 16px;">
                    <table class="table table-bordered table-sm" id="gym-fee-table" style="margin-top: 20px;">
                        <thead style="background:#f6f6f6;">
                            <tr>
                                <th>Enrollment No</th>
                                <th>Challan No</th>
                                <th>Gym Fee</th>
                                <th>Discount Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Content will be injected dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="clear clear20"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissible" id="error-message-1" style="display:none">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



</div>
<div style="clear:both;"></div>
@stop

@section('javascript')
<script>
    $(document).ready(function(){
        LoadSubscriptions();
    });
    document.getElementById('updateBtn').addEventListener('click', function() {
        var form = document.getElementById("form-1");
        var formData = new FormData(form);
        fetch('update-subscription', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#main_Id').val('');
                $('#challan_status').val('');
                $('#plan_tenure').val('');
                $('#enrollment').val('');

                $("#error_message").text('').hide();
                $('#modal-1').modal('hide');
                $('#success-flag').text(data.msg);
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadSubscriptions();
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
            console.error('There was an error!', error);
        });
    });


</script>

<script>
    var userGroup = @json(auth()->user()->group_id);
    function LoadSubscriptions(){
        let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
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
            pageLength: 10,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/get-subscription-list",
                data: function(d) {
                        return $.extend({}, d, filterParams, {
                            _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                        });
                    }
                },
            columns: [
                {data: "user_id", "width": "100px"},
                {data: "sess_name"},
                {data: "student_full_name", "width": "100px"},
                // {data: "sp_user_type"},
                // {data: "sp_category"},
                // {data: "sp_pricing"},
                {data: "sp_plan_tenure"},
                {
                    data: "current_status",
                    render: function (data, type, row) {
                        if (data == 'Active') {
                            return '<span class="badge badge-success">Active</span>';
                        } else if(data == 'Deactive') {
                            return '<span class="badge badge-danger">Deactive</span>';
                        } else if(data == 'Expired') {
                            return '<span class="badge badge-danger">Expired</span>';
                        }else if(data == 'Declined') {
                            return '<span class="badge badge-danger">Declined</span>';
                        }
                        else{
                            return '<span class="badge badge-warning">Pending</span>';
                        }

                    }

                },
                {
                    data: "subscription_date",
                    render: function (data, type, row) {
                        if (data) {
                            // If expiration_date is present, format the date to 'DD-MM-YYYY'
                            return moment(data).format('YYYY-MM-DD');
                        } else {
                            // If expiration_date is null or empty, return "Pending" with a warning class
                            return 'NA';
                        }
                    }
                },
                {
                    data: "expiration_date",
                    render: function (data, type, row) {
                        if (data) {
                            // If expiration_date is present, format the date to 'DD-MM-YYYY'
                            return moment(data).format('YYYY-MM-DD');
                        } else {
                            // If expiration_date is null or empty, return "Pending" with a warning class
                            return 'NA';
                        }
                    }
                },
                {data: "challan_id"},
                {
                    data: null, // since we want access to the full row
                    render: function (data, type, row) {
                        let value;
                        if (row.sp_plan_tenure === 'Semester') {
                            value = row.sc_gym_fee;
                        } else {
                            value = row.sc_amount;
                        }
                        return 'Rs. ' + Number(value).toLocaleString();
                    }
                },

                {
                    data: "callback_paid_on",
                    render: function (data, type, row) {
                        if (data && row.sc_challan_status == 'PAID' && row.response_status == 'Success') {
                            // If sc_paid_on is present return "Paid"
                            return '<span class="badge badge-success">Paid</span>';

                        }else if(row.sc_challan_status == "CANCELLED"){
                            return '<span class="badge badge-danger">Cancelled</span>';
                        }else{
                             // If sc_paid_on is null or empty, return "Pending"
                            return '<span class="badge badge-danger">Unpaid</span>';
                        }
                    }
                }

            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        console.log(row);
                        console.log(userGroup);
                        if (row["callback_paid_on"] && row.sc_challan_status == 'PAID' && row.response_status == 'Success') {
                            var button = '<div><button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="showModal(`'+row["id"]+'`,`'+row["current_status"]+'`,`'+row["sp_plan_tenure"]+'`,`'+row["user_id"]+'`,`'+row["sc_challan_status"]+'`,`'+row["challan_id"]+'`,`'+row["sc_gym_fee"]+'`,`'+row["sc_amount"]+'`,`'+row["sa_sports_category"]+'`,`'+row["scholarship_name"]+'`)"><i class="fa fa-edit"></i> Action</button></div>';

                        } else if(row.sc_challan_status != 'CANCELLED'){
                        //    if (userGroup == 644) {
                                // Start the d-flex container
                                var button = `<div class="d-flex">`;
                                // var button = ``;

                                // Always include the Action button
                                // button += `
                                //     <button class="btn btn-primary btn-sm edit-btn"
                                //         onclick="updateChallanModal('${row["id"]}','${row["sc_amount"]}','${row["challan_id"]}','${row["sp_plan_tenure"]}')">
                                //         <i class="fa fa-money"></i> Action
                                //     </button>
                                // `;
                                button += ' <button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="showModal(`'+row["id"]+'`,`'+row["current_status"]+'`,`'+row["sp_plan_tenure"]+'`,`'+row["user_id"]+'`,`'+row["sc_challan_status"]+'`,`'+row["challan_id"]+'`,`'+row["sc_gym_fee"]+'`,`'+row["sc_amount"]+'`,`'+row["sa_sports_category"]+'`,`'+row["scholarship_name"]+'`)"><i class="fa fa-edit"></i> Action</button>';

                                // Conditionally add the Delete button, but still inside the same div
                                if (row["current_status"] == 'Declined') {
                                    button += `
                                        <button class="btn btn-danger btn-sm delete-btn"
                                            onclick="deleteRow('${row["id"]}','${row["current_status"]}','${row["sc_challan_status"]}')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    `;
                                }

                                // Close the d-flex container
                                button += `</div>`;
                            // }else {
                            //         var button = `
                            //             <div>
                            //                 <button class="btn btn-primary btn-sm edit-btn" disabled id="${row["id"]}"
                            //                     onclick="alert('Challan is Unpaid!')">
                            //                     <i class="fa fa-edit"></i> Action
                            //                 </button>
                            //             </div>
                            //         `;
                            //     }

                        }else{
                            button='';
                        }
                       return button;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 10,
                },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["fsc_id"]+' value='+row["fsc_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 8,
                //     searchable: false,
                //     orderable: false
                // },
                {
                    "width": "50px", "targets": 4
                },
                {
                    "width": "50px", "targets": 5
                },
                {
                    "width": "100px", "targets": 6
                },
                {
                    "width": "50px", "targets": 7
                },
                {
                    "width": "100px", "targets": 8
                },
                {
                    "width": "50px", "targets": 9
                },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Student Subscriptions',
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
                    title: 'Student Subscriptions',
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
                    title: 'Student Subscriptions',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
            ]
        });
    }
    function exportToExcel() {
        const table = document.getElementById('htmlTable');
        const rows = table.querySelectorAll('tr');

        let csvContent = "data:text/csv;charset=utf-8,";
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            const rowData = Array.from(cells).map(cell => cell.textContent.replace(',', ''));
            csvContent += rowData.join(',') + "\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'table.csv');
        document.body.appendChild(link);
        link.click();
    }
    function exportToExcel(tableId) {
        $(`#${tableId}`).tableExport({type:'xlsx', escape:'false'});
    }
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
    function editRow(mainId, category, usertype, plantenure, pricing, detail){
        // $('#main-Id').val(mainId);
        // $('#category').val(category);
        // $('#user_type').val(usertype);
        // $('#plan').val(plantenure);
        // $('#pricing').val(pricing);
        // $('#detail').val(detail);
        // $('html, body').animate({scrollTop: 0}, 'slow');
    }
    function deleteRow(mainId, current_status, challan_status){
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/delete-subscription",
                data: {
                    mainId: mainId,
                    current_status: current_status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadSubscriptions();

                },
                error: function(error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }
    function showModal(mainId, subscriptionStatus, plan_tenure, user_id, challan_status, challan_id, sc_gym_fee, sc_amount, sa_sports_category, scholarship_name){
        $('#main_id').val(mainId);
        $('#plan_tenure').val(plan_tenure);
        $('#enrollment').val(user_id);
        $('#challan_status').val(challan_status);
          // Prepare status options based on plan_tenure
        var options = '';
        if (plan_tenure === "Semester") {
            options += '<option value="" selected>Select Status</option>'+
                        '<option value="Declined">Decline</option>'+
                        '<option value="Deactive">Deactive</option>'+
                        '<option value="Pending">Pending</option>';
        } else {
            options += '<option value="" selected>Select Status</option>'+
                        '<option value="Deactive">Deactive</option>'+
                        '<option value="Active">Active</option>'+
                        '<option value="Pending">Pending</option>';
        }

        // Update the dropdown options
        var $dropdown = $('#subStatus-dropdown');
        $dropdown.html(options);

        // Set the selected value if provided
        if (subscriptionStatus) {
            $dropdown.val(subscriptionStatus);
        } else {
            $dropdown.prop('selectedIndex', 0);
        }
        // Check status and show/hide the table
        if (challan_status === 'UNPAID' && subscriptionStatus === 'Pending') {
            var sportsCategoryValid = sa_sports_category && sa_sports_category.trim() !== "" && sa_sports_category.trim().toLowerCase() !== "null";
            var scholarshipValid = scholarship_name && scholarship_name.trim() !== "" && scholarship_name.trim().toLowerCase() !== "null";

            // Choose which value to show
            var showCategory = sportsCategoryValid
                ? sa_sports_category
                : (scholarshipValid ? scholarship_name : "NA");

            var isCategoryPresent = sportsCategoryValid || scholarshipValid;

            // Build row HTML dynamically
            var row = `<tr>
                <td>${user_id}</td>
                <td>${challan_id}</td>
                  <td>${
                            plan_tenure === 'Semester' ? sc_gym_fee : sc_amount
                        }</td>
               <td>${showCategory}</td>
                <td>
                    <button class="btn btn-danger btn-sm"
                        ${isCategoryPresent ? `onclick="reviseChallan('${mainId}')"` : 'disabled'}
                    >
                        ${isCategoryPresent ? 'Revise Challan' : 'Action'}
                    </button>
                </td>
            </tr>`;
            $('#gym-fee-table tbody').html(row);
            $('#gym-fee-table-wrapper').show();
           $('#modal-1 .modal-content').addClass('wide-modal');
        } else {
            $('#gym-fee-table-wrapper').hide();
            $('#gym-fee-table tbody').html('');
            $('#modal-1 .modal-content').removeClass('wide-modal');
        }
        $('#modal-1').modal('show');
    }
    function refreshFields(){
        $('#main-Id').val('');
        $('#category').val('');
        $('#user_type').val('');
        $('#plan').val('');
        $('#pricing').val('');
        $('#detail').val('');
        $('#payment_status').val('');
        $('#subscription_status').val('');
    }
    function getFilterParams() {
        const session = $('#session_sel').val();
        const category = $('#category').val();
        const user_type = $('#user_type').val();
        const plan = $('#plan').val();
        const payment_status = $('#payment_status').val();
        const subscription_status = $('#subscription_status').val();
        let filterParams =  {
                            session: session,
                            category: category,
                            user_type: user_type,
                            plan: plan,
                            payment_status: payment_status,
                            subscription_status: subscription_status,
                        };
        return filterParams;
    }
    function reviseChallan(mainId) {
        console.log(mainId);

        fetch('update-subscription-challan', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ mainId: mainId })
        })
        .then(response => response.json())
        .then(data => {
            // Always clear previous errors
            $("#challan_error_message").text('').hide();

            if (data.success) {
                // Clear form fields if needed
                $('#subscription_id').val('');
                $('#challan_id').val('');
                $('#challan_amount').val('');

                // Hide the modal on success
                $('#modal-1').modal('hide');

                // Show a success message
                $('#success-flag').text(data.msg);
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds

                LoadSubscriptions();
            } else {
                // Handle different types of messages
                var message = "";
                if ('msg' in data) {
                    message = data.msg; // e.g., 'No changes made'
                } else if ('errors' in data) {
                    var errors = data.errors;
                    $.each(errors, function (i, obj) {
                        message += "<p>" + obj[0] + "</p>";
                    });
                }
                $("#processing_message").hide();
                $('#success_message').text('').hide();
                $('#challan_error_message').html(message).fadeIn('slow').delay(5000).fadeOut();
            }
        })
        .catch(error => {
            console.error('There was an error!', error);
            $('#challan_error_message').text('An unexpected error occurred.').fadeIn('slow').delay(5000).fadeOut();
        });
    }



    // function updateChallanModal(id, amount, challanId, plan_tenure){
    //     $('#subscription_id').val(id);
    //     $('#challan_id').val(challanId);
    //     $('#challan_amount').val(amount);
    //     $('#modal-challan-update').modal('show');
    // }
</script>

@stop

