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

    {{-- DATEPICKER STYLE --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    {{-- <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css"> --}}
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
        width:76px;
        padding:4px;
    }
       /* Hide the calendar grid to show only month and year selection */
    .ui-datepicker-calendar, .ui-datepicker-current {
        display: none;
    }
    .ui-datepicker-header {
        width:256px;
    }
    /* .ui-datepicker-title {
        width:256px;
    } */
    .ui-datepicker-title {
        width:190px;
    }
    .ui-datepicker select.ui-datepicker-month,
    .ui-datepicker select.ui-datepicker-year {
        width: 50%; /* You can increase this value for more width */
        padding: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .filter-row {
        margin-right: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .ui-datepicker-buttonpane{
        display:flex;
        justify-content: center;
    }
    .ui-datepicker-close {
        background-color: #4454c3; /* Set background color */
        color: #ffffff; /* Text color */
        border: none; /* Remove default border */
        border-radius: 8px; /* Round corners */
        padding: 5px 10px; /* Padding for better look */
        cursor: pointer; /* Change cursor to pointer */
    }

        
</style>
<div id="success-flag" class="success-flag"></div>
<div class="card">
   
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <form id="subcriptionForm" method="POST" action="">
            @csrf
            <div class="row"><h3>Filter Faculty/Staff Subscriptions:</h3></div>
            <div class="row filter-row" style="margin-right:10px;">

                <div class="form-group col-sm-3 datepicker-container">
                    <label for="datepicker">Select Month and Year:</label>
                    <input type="text" id="datepicker" name='month' class='form-control' placeholder="MM-YYYY">
                </div>
                <div class="form-group col-sm-3">
                    <label for="status">Subscription Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
                    <div class="row"><h3>Faculty/Staff Subscriptions:</h3></div>
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display">
                            <thead>
                            <tr>
                                <th scope="col">Serial No.</th>
                                <th scope="col">User Id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Subscription Start</th>
                                <th scope="col">Subscription Expiry</th>
                                <th scope="col">Subscription Status</th>
                              
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
    <div class="modal-dialog" style="top: 40%;transform: translateY(-40%)">
        <!-- Modal content-->
        <div class="modal-content" style="width:500px;">
            <div class="modal-header" style="background-color:#ededed;">
                <h1 class="modal-title" style="font-size: 25px;">Action</h1>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-1" method="POST" action="" data-toggle="validator">
                    @csrf
                    <div class="row" style="margin-top: 12px;">
                        <input hidden id="main_id" name="main_id" value="">
                        <input hidden id="plan_tenure" name="plan_tenure" value="">
                        <div class="col-md-4 text-left">
                            <label for="status-dropdown" class="col-form-label">Subscription Status:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <select class="selectpicker form-control" id="subStatus-dropdown" name="status">
                                <option value="" disabled selected hidden>Select Status</option>
                                <option value="Deactive">Deactive</option>
                                <option value="Active">Active</option>
                                {{-- <option value="Partial Paid"></option> --}}
                            </select>
                        </div>
                    </div>
                    {{-- <div class="row" style="margin-top: 12px;">
                        <div class="col-md-3 text-left">
                            <label for="ajdustAmount" class="col-form-label">Amount:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" name="amount" id="amount" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:12px;">
                                <label for="remarks">Remarks:</label>
                                <textarea class="form-control" value="" id="status_remarks" name="status_remarks" rows="4"></textarea>
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                        <button type="button" id="updateBtn" class="btn btn-primary">Update</button>
                    </div>
                </form>
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
        // Initialize datepicker with month and year only
        $("#datepicker").datepicker({
                dateFormat: "mm-yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker("setDate", new Date(year, month, 1));
                }
            });
             // Adjust the Datepicker UI for month and year selection only
        $(document).on("focus", "#datepicker", function() {
            $(".ui-datepicker-calendar").hide();
        });
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
                $("#error_message").text('').hide();
                // $('#success_message').html(data.msg);
                // $('#success_message').fadeIn('slow');
                // $('#success_message').fadeOut(5000);
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
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- jquery.dataTables.min.js --}}
{{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}
{{-- dataTables.buttons.min.js --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script> --}}
{{-- jszip.min --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
{{-- buttons.html5.min --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script> --}}
{{-- buttons.print.min --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script> --}}
{{-- pdfmake.min --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> --}}
{{-- vfs_fonts --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

{{-- LOCAL FILES --}}
{{-- jQuery DataTables JS --}}
{{-- <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script> --}}

{{-- DataTables Buttons JS --}}
{{-- <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script> --}}

{{-- JSZip JS --}}
{{-- <script src="{{ asset('assets/js/jszip.min.js') }}"></script> --}}

{{-- Buttons HTML5 JS --}}
{{-- <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script> --}}

{{-- Buttons Print JS --}}
{{-- <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script> --}}

{{-- pdfmake JS --}}
{{-- <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script> --}}

{{-- VFS Fonts JS --}}
{{-- <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script> --}}

<script>
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
            pageLength: 50,
            order: [1, 'desc'],
             // Custom length menu
             lengthMenu: [
                [50, 100, 250, -1], // Page lengths
                ['50', '100', '250', 'Show all'] // Labels
            ],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/get-staffSubscription-list",
                data: function(d) {
                        return $.extend({}, d, filterParams, {
                            _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                        });
                    }
                },
            columns: [
                {
                    data: null, // Set data to null to allow custom rendering
                    orderable: false, // Disable sorting on this column
                    searchable: false, // Disable searching on this column
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Serial number starts from 1
                    }
                },
                {data: "user_id"},
                {data: "student_full_name"},
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
                {
                    data: "subscription_status",
                    render: function (data, type, row) {
                        if (data == 'inactive') {
                            // If sc_paid_on is null or empty, return "Pending"
                            return '<span class="badge badge-warning">Inactive</span>';
                        } else {
                            // If sc_paid_on is present return "Paid"
                            return '<span class="badge badge-success">Active</span>';
                        }
                    }
                }

            ],
            columnDefs: [
                // {
                //     render: function (data, type, row) {
                //         console.log(row);
                //         if (row["callback_paid_on"] == null) {
                //             var button = '<div><button class="btn btn-primary btn-sm edit-btn" disabled id="'+row["id"]+'" onclick="alert(`Challan is Unpaid!`)"><i class="fa fa-edit"></i> Action</button>';
                //         } else {
                //             var button = '<div><button class="btn btn-primary btn-sm edit-btn" id="'+row["id"]+'" onclick="showModal(`'+row["id"]+'`,`'+row["current_status"]+'`,`'+row["sp_plan_tenure"]+'`)"><i class="fa fa-edit"></i> Action</button>';
                //         }

                //        return button;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 5,
                // },
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
                    "width": "75px", "targets": 0
                },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Faculty/Staff Subscriptions',
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
                    title: 'Faculty/Staff Subscriptions',
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
                    title: 'Faculty/Staff Subscriptions',
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
    function deleteRow(mainId){
        // var confirmation = window.confirm("Are you sure you want to delete?");
        // if (confirmation) {
        //     $.ajax({
        //         type: "POST",
        //         url: "/delete-subscriptionPlan",
        //         data: {
        //             mainId: mainId,
        //             _token: $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(response) {
        //             $('#success-flag').text('Deleted Successfully');
        //             $('#success-flag').addClass('show');
        //             setTimeout(function() {
        //                 $('#success-flag').removeClass('show');
        //             }, 3000); // Hide after 3 seconds
        //             LoadSubscriptions();

        //         },
        //         error: function(error) {
        //             // Handle the error
        //             console.error("Error:", error);
        //         }
        //     });
        // } else {

        // }

    }
    function showModal(mainId, subscriptionStatus, plan_tenure){
        $('#main_id').val(mainId);
        $('#plan_tenure').val(plan_tenure);
        $('#subStatus-dropdown').val(subscriptionStatus);

        $('#modal-1').modal('show');
    }
    function refreshFields(){
        $('#status').val('');
        $('#datepicker').val('');
    }
    function getFilterParams() {
        // const session = $('#session_sel').val();
        // const category = $('#category').val();
        const status = $('#status').val();
        const month = $('#datepicker').val();
        let filterParams =  {
                            // session: session,
                            // category: category,
                            status: status,
                            month: month
                        };
        return filterParams;
    }
</script>

@stop

