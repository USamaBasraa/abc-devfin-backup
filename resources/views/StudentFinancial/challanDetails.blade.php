@extends('layouts.app')
<style>
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
</style>
@section('content')
    <div class="container-fluid" style="">
        <div class="card p-4">
            <div class="card-header">
                <h2 class="mb-4 text-left">Challan Details</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="session">Select Session:</label>
                        <select name="session" id="session" class="form-control">
                            <option value="" selected> All </option>
                           @foreach ($sessions as $session)
                                <option value="{{ $session->sc_session_id }}"
                                    {{ $session->sc_session_id == 165 ? 'selected' : '' }}>
                                    {{ $session->sess_name }}
                                </option>
                            @endforeach

                        </select>
                        <h6 data-error-for="sessionOption" class="validation-error pt-4"></h6>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="challan_type">Select Type:</label>
                       <select name="challan_type" id="challan_type" class="form-control">
                            <option value="">All</option>

                            @foreach ($mapped_challantype as $challanType => $challan_type_text)
                                <option value="{{ $challanType }}"
                                    {{ $challanType == 'Full_semester' ? 'selected' : '' }}>
                                    {{ $challan_type_text }}
                                </option>
                            @endforeach
                        </select>

                        <h6 data-error-for="challan_type" class="validation-error pt-4"></h6>
                    </div>
                    <div class="form-group col-md-3 d-none">
                        <label for="violationType">Select Offence Type:</label>
                        <select name="violationType" id="violationType" class="form-control"></select>
                        <h6 data-error-for="violationType" class="validation-error pt-4"></h6>
                    </div>
                    <div class="form-group col-md-3 d-none">
                        <div class="form-group" id="user_dropdown">
                            <label for="issuedBy">Issued By:</label>
                            <select style="width: 100%;" class="form-control  select2-show-search" id="issuedBy"
                                name="issuedBy">
                                <option value="">-- Select User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->employee_id }}">{{ $user->emp_ad_username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row align-items-end">

                        <!-- From Date -->
                        <div class="form-group col-md-2 my-2">
                            <label for="fromDate">From Date:</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control">
                        </div>

                        <!-- To Date -->
                        <div class="form-group col-md-2 my-2">
                            <label for="toDate">To Date:</label>
                            <input type="date" name="toDate" id="toDate" class="form-control">
                        </div>

                        <!-- Filter Button (right of date fields) -->
                        <div class="form-group col-md-2 my-2">
                            <button class="btn btn-primary w-100" id="filter-button">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>

                        <!-- EMPTY Flexible Space -->
                        <div class="col-md-3"></div>

                        <!-- Download Button (far right) -->
                        <div class="form-group col-md-3 my-2 d-flex justify-content-end">
                            <button class="btn btn-success" onclick="exportToExcel()">
                                <i class="fa fa-file-excel-o"></i> Download Summary
                            </button>
                        </div>

                    </div>

            </div>
            {{-- ///Data table --}}
            <div class="row filteredChallanData" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-tableChallan" class="table display" style="width:100%">
                            <thead style="background-color: #f2f2f2;">
                            <tr>
                                <th>Challan No</th>
                                <th>Enrollment No</th>
                                <th>Session Name</th>
                                <th>Invoice Amount</th>
                                <th>Challan Type</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Student Status</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        function LoadChallan() {
            $('#data-tableChallan').DataTable({
                scrollX: true, // Enable horizontal scrolling
                responsive: true,
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
                // pageLength: 10,
                lengthMenu: [
                    [100, 500, 1000, 5000],
                    [100, 500, 1000, 5000]
                ],
                order: [1, 'desc'],
                ajax: {
                    dataType: 'json',
                    method: 'post',
                    url: "/api/get-challans-data",
                    data: function(d) {
                        d.session = $('#session').val();
                        d.challan_type = $('#challan_type').val();
                        d.issuedBy = $('#issuedBy').val();
                        d.fromDate = $('#fromDate').val();
                        d.toDate = $('#toDate').val();
                    }
                },

                columns: [{
                        data: "challan_id"
                    },
                    {
                        data: "sc_enrollment_no"
                    },
                    {
                        data: "sess_name"
                    },
                    {
                        data: "invoiceamount"
                    },
                    {
                        data: "sc_challan_type"
                    },
                   {
                        data: "sc_challan_status",
                        render: function (data, type, row) {
                            if (data === "UNPAID") {
                                return '<span style="color:red; font-weight:bold;">' + data + '</span>';
                            } else if (data === "PAID") {
                                return '<span style="color:green; font-weight:bold;">' + data + '</span>';
                            }
                            return data; // default
                        }
                    },

                    {
                        data: "created_at"
                    },
                    {
                        data: "student_status"
                    },

                ],
                // columnDefs: [{
                //         render: function(data, type, row) {
                //             console.log(row);

                //             var button = '<button class="btn btn-primary btn-sm preview-btn" id="' + row[
                //                     "id"] + '" onclick="previewRow(`' + row["id"] +
                //             '`)"><i class="fa fa-eye" aria-hidden="true"></i> Preview</button>';

                //             return button;

                //         },
                //         // className: 'dt-body-center',
                //         // className: 'dt-head-center',
                //         targets: 12,
                //     },
                    // {
                    //     render: function (data, type, row) {
                    //             var   selectbtn = '<div><input name="fee_ids[]" class="select-data" value='+row["sai_id"]+' type="checkbox" checked></div>';

                    //         return selectbtn;

                    //     },
                    //     // className: 'dt-body-center',
                    //     // className: 'dt-head-center',
                    //     targets: 7,
                    //     searchable: false,
                    //     orderable: false
                    // },
                //     {
                //         "width": "10px",
                //         "targets": 0
                //     },
                //     {
                //         "width": "10px",
                //         "targets": 1
                //     },
                //     {
                //         "width": "150px",
                //         "targets": 3
                //     },
                //     {
                //         "width": "150px",
                //         "targets": 4
                //     }
                // ],
                autoWidth: false, // Ensures DataTable does not set its own width
                dom: 'Blfrtip', // Adds the buttons to the DataTable
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Challans',
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
                        title: 'Challans',
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
                        title: 'Challans',
                        // exportOptions: {
                        //     columns: function (idx, data, node) {
                        //         // Exclude columns by index
                        //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                        //     }
                        // }
                    }
                ]
            });
        }
        $(document).ready(function() {
            LoadChallan();
            $('#filter-button').on('click', function() {
                LoadChallan();
            });


            // var table = $('#challanTable').DataTable({
            //     dom: 'Blfrtip',
            //     buttons: [
            //         {
            //             extend: 'excelHtml5',
            //             text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
            //             title: 'Generated Challans Excel',
            //             exportOptions: {
            //                 columns: [0,1,2,3,4,5,6,7,8,9,10,11] // Adjust based on actual number of columns
            //             },
            //             className: 'ml-2 btn btn-primary'
            //         },
            //         {
            //             extend: 'pdfHtml5',
            //             text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
            //             title: 'Generated Challans PDF',
            //             exportOptions: {
            //                 columns: ':not(:last-child)'
            //             },
            //             className: 'ml-2 btn btn-primary'
            //         }
            //     ],
            //     responsive: true,
            //     ordering: true, // Enable sorting
            //     scrollX: true, // Enable horizontal scroll
            //     pageLength: 25, // Default rows per page
            //     lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, 'All']] // Custom menu
            //     // "language": {
            //     //     "lengthMenu": "Display _MENU_ records per page",
            //     //     "zeroRecords": "Nothing found - sorry",
            //     //     "info": "Showing page _PAGE_ of _PAGES_",
            //     //     "infoEmpty": "No records available",
            //     //     "infoFiltered": "(filtered from _MAX_ total records)"
            //     // }
            // });

            // // Filter table based on selected session
            // $('#session').on('change', function() {
            //     var selectedSessionText = $(this).find('option:selected').text();

            //     if (selectedSessionText === '--- Select Session ---') {
            //         // If no session selected, show all data
            //         table.column(2).search('').draw();
            //     } else {
            //         // Filter by selected session (column 2 is Session Name)
            //         table.column(2).search('^' + selectedSessionText + '$', true, false).draw();
            //     }
            // });

            // // Filter table based on selected category
            // $('#fineCategoryOption').on('change', function() {
            //     var selectedCategoryText = $(this).find('option:selected').text();

            //     if (selectedCategoryText === '--- Select Category ---') {
            //         // If no category selected, show all data
            //         table.column(3).search('').draw();
            //     } else {
            //         // Filter by selected category (column 3 is Category Name)
            //         table.column(3).search('^' + selectedCategoryText + '$', true, false).draw();
            //     }
            // });

            // // Filter table based on selected violation type
            // $('#violationType').on('change', function() {
            //     var selectedViolationText = $(this).find('option:selected').text();

            //     if (selectedViolationText === '--- Select Violation Type ---' || selectedViolationText === '') {
            //         // If no violation type selected, show all data
            //         table.column(4).search('').draw();
            //     } else {
            //         // Filter by selected violation type (column 4 is Violation Type)
            //         table.column(4).search('^' + selectedViolationText + '$', true, false).draw();
            //     }
            // });

            // // Filter table based on selected issued by user
            // $('#issuedBy').on('change', function() {
            //     var selectedIssuedByText = $(this).find('option:selected').text();

            //     if (selectedIssuedByText === '-- Select User --' || selectedIssuedByText === '') {
            //         // If no user selected, show all data
            //         table.column(10).search('').draw();
            //     } else {
            //         // Filter by selected issued by user (column 10 is Issued By)
            //         table.column(10).search('^' + selectedIssuedByText + '$', true, false).draw();
            //     }
            // });
        });

        // function getOffenceDetails(selectedValue) {
        //     var violationType = document.getElementById("violationType");
        //     violationType.value = '';


        //     // Adjust the URL by appending the selectedValue to it
        //     let url = `/check-violation-types?fineCategoryOption=${selectedValue}`;

        //     fetch(url)
        //         .then(response => response.json())
        //         .then(response => {
        //             if (response.success) {
        //                 violationType.innerHTML =
        //                     '<option value="" selected disabled>--- Select Violation Type ---</option>';
        //                 response.data.forEach(type => {
        //                     let option = document.createElement("option");
        //                     option.value = type.id;
        //                     option.textContent = type.fcd_violation_type;
        //                     violationType.appendChild(option);
        //                 });
        //             }

        //         });
        // }

        function exportToExcel() {
            const sessionElement = document.getElementById('session');
            const session = sessionElement.value; // Get the selected value
            const sessionText = sessionElement.options[sessionElement.selectedIndex].text; // Get the selected text
            const challan_type = document.getElementById('challan_type').value;
            // const offence = document.getElementById('violationType').value;
            // const issuedBy = document.getElementById('issuedBy').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            let url = "{{ config('app.app_url') }}";
            let link =
                `${url}/assets/cms/reports/challandetail_summary_excel.php?session=${encodeURIComponent(session)}&category=${encodeURIComponent(challan_type)}&fromDate=${encodeURIComponent(fromDate)}&toDate=${encodeURIComponent(toDate)}&sessionText=${encodeURIComponent(sessionText)}`;
            window.open(link, '_blank');
            event.preventDefault();
        }
    </script>
@stop
