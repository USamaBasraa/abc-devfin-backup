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
        width:76px;
    }
    .sp-detail-column {
        width: 300px;       /* Adjust the width as needed */
        white-space: normal; /* Allows text to wrap within the fixed width */
        text-align: justify; /* Justifies the text */
    }
</style>
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <form id="subcriptionForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Create Subscription Plan:</h3></div>
                <div class="row filter-row" style="margin-right:10px;">
                    <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                    <div class="form-group col-sm-4">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            <option value="">Select Session</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="school_sel">Category Type:</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Select Category type</option>
                            <option value="Gym">Gym</option>
                            <option value="Hostel">Hostel</option>
                            <option value="Lab">Lab</option>
                        </select>
                    </div>
                     <div class="form-group col-sm-4">
                        <label for="usertype">User Type:</label>
                        <select name="user_type" id="user_type" class="form-control">
                            <option value="">Select User type</option>
                            <option value="Student">Student</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="plan">Plan Tenure:</label>
                        <select name="plan" id="plan" class="form-control">
                            <option value="">Select plan</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Semester">Semester</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="price">Fee for Regular Students:</label>
                        <input type="number"  onkeypress="return /[0-9.]/i.test(event.key)" name="regular-pricing" id="regular-pricing" class="form-control">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="price">Fee for Sports Students:</label>
                        <input type="number"  onkeypress="return /[0-9.]/i.test(event.key)" name="sports-pricing" id="sports-pricing" class="form-control">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="discount">
                            Discount: <small class="text-muted" style="font-size: smaller;">(Discount will be applied to regular fee only)</small>
                        </label>
                        <input type="number" onkeypress="return /[0-9.]/i.test(event.key)" name="discount" id="discount" class="form-control">
                    </div>
                    <div class="form-group col-sm-4 date">
                        <label for="start">Semester Start Date:</label>
                        <input type="date" name="semester-start" id="semester-start" class="form-control">
                    </div>
                    <div class="form-group col-sm-4 date">
                        <label for="end">Semester End Date:</label>
                        <input type="date" name="semester-end" id="semester-end" class="form-control">
                    </div>
                    <div class="form-group col-sm-8">
                        <label for="detail">Plan Details:</label>
                        <textarea name="detail" id="detail" class="form-control" style="width: 100%;" rows="3"></textarea>
                    </div>

                    {{-- <div class="form-group col-sm-6 d-none" style="display: flex; flex-direction: row;">
                        <label for="schlrshp-type">Scholarship Type</label>
                        <div class="checkboxOptions">
                            <input type="radio" class="validity-type" id="session-wise" name="validity-type" value="session">
                            <label for="session-wise">Session wise</label>
                        </div>
                        <div  class="checkboxOptions">
                            <input type="radio" class="validity-type" id="annual-wise" name="validity-type" value="">
                            <label for="annual-wise">Annual wise</label>
                        </div>
                    </div> --}}
                    {{-- <div class="form-group sessions_select col-sm-6">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            <option value="">Select Sessions</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    {{-- <div class="form-group validity-date col-sm-6 d-none">
                        <label for="studentType">Effective From</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                    </div>
                    <div class="form-group validity-date col-sm-6 d-none">
                        <label for="studentType">Effective To</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                    </div> --}}
                </div>
                <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                    <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshAllFields()">Clear</button>
                </div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>
    </div>
    <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                {{-- <div class="card"> --}}
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display">
                            <thead>
                            <tr>
                                <th scope="col">Session</th>
                                <th scope="col">Category</th>
                                <th scope="col">User</th>
                                <th scope="col">Plan Tenure</th>
                                <th scope="col">Regular Pricing</th>
                                <th scope="col">Sports Pricing</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Start</th>
                                <th scope="col">End</th>
                                <th scope="col">Details</th>
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



</div>
<div style="clear:both;"></div>
@stop

@section('javascript')
<script>
    $(document).ready(function(){
        LoadSubscriptionPlans();
    });
    document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("subcriptionForm");
        var formData = new FormData(form);
        fetch('add-subscription-plan', {
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
                $("#error_message").text('').hide();
                $('#success_message').html(data.msg);
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(5000);
                LoadSubscriptionPlans();
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
    function LoadSubscriptionPlans(){
        // let filterParams = getFilterParams();
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
                url:  "/get-subscriptionPlan-list",
                data: function(d) {
                        // Include the CSRF token in the request
                        d._token = $('meta[name="csrf-token"]').attr('content')
                    }
                },
            columns: [
                {data: "sess_name"},
                {data: "sp_category"},
                {data: "sp_user_type"},
                {data: "sp_plan_tenure"},
                {data: "sp_pricing"},
                { 
                    data: "sp_sports_pricing",
                    render: function(data, type, row) {
                        return data ? data : 'NA';  
                    }
                },
                { 
                    data: "sp_discount",
                    render: function(data, type, row) {
                        return data ? data : 'NA';  
                    }
                },
                { 
                    data: "sp_sem_start",
                    render: function(data, type, row) {
                        return data ? data.split(' ')[0] : ''; 
                    }
                },
                { 
                    data: "sp_sem_end",
                    render: function(data, type, row) {
                        return data ? data.split(' ')[0] : ''; 
                    }
                },
                { 
                    data: "sp_detail",
                    className: "text-justify sp-detail-column"  // Apply custom class for styling
                }
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        console.log(row);
                        var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:5px;" id="'+row["sp_id"]+'" onclick="editRow(`'+row["sp_id"]+'`,`'+row["sp_category"]+'`,`'+row["sp_user_type"]+'`,`'+row["sp_plan_tenure"]+'`,`'+row["sp_pricing"]+'`,`'+row["sp_detail"]+'`,`'+row["sp_discount"]+'`,`'+row["sp_sem_start"]+'`,`'+row["sp_sem_end"]+'`,`'+row["sp_session"]+'`,`'+row["sp_sports_pricing"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                                                    '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                                                '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRow('+row["sp_id"]+')"><i class="fa fa-trash"></i> Delete</button></div>';
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
                    "width": "100px",
                    "targets": 3
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
                    title: 'Subscription Plans',
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
                    title: 'Subscription Plans',
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
                    title: 'Subscription Plans',
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
    function editRow(mainId, category, usertype, plantenure, pricing, detail, discount, start, end, session, sportsPricing){
        $('#main-Id').val(mainId);
        $('#session_sel').val(session);
        $('#category').val(category);
        $('#user_type').val(usertype);
        $('#plan').val(plantenure);
        $('#regular-pricing').val(pricing);
        $('#sports-pricing').val(sportsPricing);
        $('#detail').val(detail);
        $('#discount').val(discount);

        var startDate = start.split(' ')[0];
        $('#semester-start').val(startDate);

        var endDate = end.split(' ')[0];
        $('#semester-end').val(endDate);

      
        
        $('html, body').animate({scrollTop: 0}, 'slow');
    }
    function deleteRow(mainId){
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/delete-subscriptionPlan",
                data: {
                    mainId: mainId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadSubscriptionPlans();

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
        // $('#category').val('');
        // $('#user_type').val('');
        // $('#plan').val('');
        $('#discount').val('');
        $('#regular-pricing').val('');
        $('#sports-pricing').val('');
        $('#detail').val('');
    }
    function refreshAllFields(){
        $('#main-Id').val('');
        $('#session_sel').val('');
        $('#category').val('');
        $('#user_type').val('');
        $('#plan').val('');
        $('#discount').val('');
        $('#regular-pricing').val('');
        $('#sports-pricing').val('');
        $('#semester-start').val('');
        $('#semester-end').val('');
        $('#detail').val('');
    }
</script>

@stop

