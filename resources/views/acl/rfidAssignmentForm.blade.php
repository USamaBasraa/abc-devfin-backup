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
        .checkboxOptions {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-left: 10px;
            margin-top: 30px;
        }

        input[type=checkbox],
        input[type=radio] {
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
            border-color: #ced4da !important;
            /* Matching Bootstrap's default border color */
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
        .dataTables_wrapper {
            width: 90% !important;

        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons,
        .dataTables_wrapper {
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

        .group-assigned .form-check-input {
            width: 22px;
            height: 22px;
            cursor: pointer;
        }

        .group-assigned .form-check-label {
            font-size: 1rem;
            margin-left: 4px;
        }

        .modal-title {
            font-size: 25px !important;
            /* adjust as needed */
            font-weight: bold;
        }
    </style>
    <div id="success-flag" class="success-flag"></div>
    <div class="card">
        <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
            <div class="card-header" style="display:inline">
                <div style="float:left">
                    <h2 class="card-title">Setup RFID</h2>
                </div>
            </div>
            <form id="searchForm" method="post" style="margin-top:30px;">
                <div class="row justify-content-center">
                    <div class="col-6 col-sm-12 col-md-6">
                        {{-- <label for="challan" style="margin-right: 10px; white-space: nowrap;">Search by Username / Enrollment no</label> --}}
                        <div class="input-group">
                            <input placeholder="Search by Username / Enrollment no" autocomplete="off" class="form-control"
                                type="text" name="user_name" id="username" onkeypress="enterPressUserSearch()">
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center mt-2">
                    <div class="col-auto">
                        <button onclick='getProfile()' type="button" class="btn btn-info">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <small class="searchUser-disclaimer"></small>
                <div id="searchError_Usermessage" class="alert alert-danger" style="display:none; margin-top:10px;"></div>


            </form>
            <div id="dataDiv" style="margin-top:20px;">
            </div>
        </div>
        <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>RFID Machine Groups</h3>
                </div>
            </div>
            <div class="row g-4 d-flex justify-content-center">
                <div class="col-md-2">
                    <select class="form-control" name="machine_group" id="machine_group" onchange="LoadMachines()">
                        <option value="">-- Select Event --</option>
                        @foreach ($groups as $row)
                            <option value="{{ $row->ag_id }}">{{ $row->ag_group_name }} Machines</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3 class="group_heading"></h3>
                    </div>
                </div>
                <div class="table-responsive d-flex justify-content-center">
                    <table id="data-table" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th scope="col">Sr. No.</th>
                                <th scope="col">Machine IP</th>
                                <th scope="col">Description</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>
    <!-- ✅ Student Search Modal -->
    <div class="modal fade" id="studentSearchModal" tabindex="-1" aria-labelledby="studentSearchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="modal-title fs-1 w-100 d-flex justify-content-between align-items-center"
                        id="studentSearchModalLabel">
                        <span id="modalHeadingText"></span>
                        <span id="modalHeadingIp" class="ms-auto"></span>
                    </h2>
                </div>


                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Search Bar -->
                    <div class="row justify-content-center mb-3">
                        <div class="col-12 col-md-8">
                            <div class="input-group">
                                <input type="text" id="user_id" name="user_id" class="form-control"
                                    placeholder="Search by Enrollment no / Employee Id " autocomplete="off"
                                    onkeypress="enterPressUserSearch_fromMachine()">
                                <button type="button" class="btn btn-info" onclick="getProfile_fromMachine()">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                               <!-- Error / Loading message -->
                           <div class="text-center mt-2">
                                <!-- Error message -->
                                <span id="searchError" class="text-danger" style="display: none;"></span>

                                <!-- Loading spinner -->
                                <span id="searchLoading" class="text-info" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i> Loading, please wait...
                                </span>
                            </div>


                        </div>
                    </div>
                    <form id="acl-form" method="POST" action="" data-toggle="validator">
                    @csrf
                        <!-- Student Info -->
                        <div class="row g-3">
                            <input type="text" hidden id="machine_ip" name="machine_ip">
                            <input type="text" hidden id="machine_id" name="machine_id">
                            <input type="text" hidden id="group" name="group">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">User Id</label>
                                <input type="text" id="user_machine_id" name="user_machine_id" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">User Name</label>
                                <input type="text" id="user_machine_name" name="user_machine_name" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">-- Select Gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="unknown">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">User Status</label>
                                <select id="user_machine_status" name="user_machine_status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="normal">Active</option>
                                    <option value="blackList">Blocked</option>
                                    <option value="visitor">Visitor</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="validity_start" class="form-label">Validity Start</label>
                                        <input type="datetime-local" id="validity_start" name="validity_start" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="validity_end" class="form-label">Validity End</label>
                                        <input type="datetime-local" id="validity_end" name="validity_end" class="form-control">
                                    </div>
                                    <div class="col-md-4 d-flex justify-content-center align-items-center">
                                        <div class="d-flex flex-column">
                                            <div class="form-check mb-2" style="margin-top: 8px;">
                                                <input class="form-check-input" type="checkbox" id="time_effective" name="time_effective"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="Either validity period is effective or not">
                                                <label class="form-check-label fw-bold" for="time_effective">
                                                    Validity Effective
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="verify_user_only" name="verify_user_only"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="Grant No Access Only take attendance">
                                                <label class="form-check-label fw-bold" for="verify_user_only">
                                                    Verify Only (No Access)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 mt-5" style="display: flex;justify-content: center;">
                           <div class="btn btn-primary d-inline-flex align-items-center" id="saveButton" style="cursor: pointer;">
                                <span>Update</span>

                            </div>


                        </div>
                        <!-- Error / Loading message -->
                        <div class="text-center mt-2">
                            <!-- Error message -->
                            <span id="update_response" style="display: none;"></span>

                            <!-- Loading spinner -->
                            <span id="updateLoading" class="text-info" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Loading, please wait...
                            </span>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div style="clear:both;"></div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            LoadMachines();
        });
        document.getElementById('saveButton').addEventListener('click', function() {
            var form = document.getElementById("acl-form");
            var formData = new FormData(form);
            // Append converted datetime values
            formData.append("user_id", $("#user_id").val());
            formData.append("validity_start", fromDatetimeLocalFormat($("#validity_start").val()));
            formData.append("validity_end", fromDatetimeLocalFormat($("#validity_end").val()));
             // Clear old messages
            $('#update_response').empty();
            // Show loader before sending
            $('#updateLoading').show();
            fetch('update-user-machine', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                   // Hide loader when response is received
                    $('#updateLoading').hide();

                    if (data.results && Array.isArray(data.results)) {
                        data.results.forEach(function(result) {
                            let message = '';
                            if (!result.success) {
                                message = `
                                    <div class="alert alert-danger py-1 px-2 my-1" role="alert"
                                        style="background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 4px;">
                                        ${result.msg}
                                    </div>`;
                            }
                            $('#update_response').append(message);
                        });

                        $('#update_response').show();
                    } else {
                        $('#update_response').html('<div class="text-danger">Unexpected response format.</div>');
                    }
                })
                .catch(error => {
                    $('#updateLoading').hide(); // stop loading
                    $('#update_response').html('<div class="text-danger">There was an error! Please try again.</div>');
                    console.error('There was an error!', error);
                });
        });
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })
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
    <script src="<?= asset('js/bootstrap-autocomplete.js') ?>"></script>
    <script>
        $('#username, #user_id').autoComplete({
            resolver: 'custom',
            autoSelect: false,
            formatResult: function(item) {
                return {
                    value: item.value,
                    text: item.text + " [" + item.value + "] "
                };
            },
            select: function(evt, item) {
                console.log('Selected User:', item);
            },
            events: {
                search: function(searchParam, callback) {

                    $.ajax({
                        url: '/auto-search',
                        type: 'GET',
                        data: {
                            'searchParam': searchParam
                        },
                        success: function(res) {
                            console.log('Search results:', res);
                            callback(res);
                        },
                        error: function(xhr) {
                            console.error('Search error:', xhr.responseText);
                        }
                    });
                }
            }
        });
        $('#studentSearchModal').on('hidden.bs.modal', function() {
            // Clear input fields when modal is closed
            $('#searchError').hide().text('');
            $('#searchLoading').hide();
            $('#update_response').empty();
            $('#updateLoading').hide();

            refreshModalData();
        });

        function LoadMachines() {
            let filterParams = getFilterParams();
            let groupName = $('#machine_group').val() ?
                $('#machine_group option:selected').text() :
                'All Machines';

            $('.group_heading').text(groupName);
            // console.log(filterParams);
            let srNo = 1;
            $('#data-table').DataTable({
                // scrollX: true, // Enable horizontal scrolling
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
                pageLength: 100,
                // initComplete: function() {
                //     $('#data-table').css('width', '100%');
                // },
                ajax: {
                    dataType: 'json',
                    method: 'post',
                    url: "/api/get_machine_list",
                    data: function(d) {
                        return $.extend({}, d, filterParams);
                    }
                },
                columns: [{
                        data: null,
                        name: "sr_no",
                        render: function() {
                            return srNo++;
                        }
                    },
                    {
                        data: "agd_machine_ip",
                        name: "agd_machine_ip"
                    },
                     {
                        data: "agd_machine_description",
                        name: "agd_machine_description",
                         render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    {
                        data: "agd_machine_status",
                        name: "agd_machine_status",
                        render: function(data, type, row) {
                            // Map values to display text
                            switch (data) {
                                case 'E':
                                    return 'Active';
                                case 'D':
                                    return 'In active';
                                default:
                                    return data; // fallback to the original value if unmatched
                            }
                        }

                    },




                ],
                drawCallback: function() {
                    // Reset counter when table is redrawn
                    srNo = 1;
                    // Force table full width
                    // $('#data-table').css('width', '100%');
                    // $('#data-table').DataTable().columns.adjust();
                },
                columnDefs: [{
                        render: function(data, type, row) {
                            // console.log(row);
                            var button = '<div><button class="btn btn-primary btn-sm edit-btn" id="' + row["agd_id"] + '" onclick="openModal(`' + row["agd_id"] + '`,`' + row["agd_machine_ip"] + '`,`' + row["ag_group_name"] + '`)"><i class="fa fa-edit"></i> Action</button>';
                            // +
                            //     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                            // '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows('+row["agd_id"]+')"><i class="fa fa-trash"></i> Delete</button></div>';
                            return button;

                        },
                        // className: 'dt-body-center',
                        // className: 'dt-head-center',
                        targets: 4,
                    },
                    // {
                    //     render: function (data, type, row) {
                    //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                    //         return selectbtn;

                    //     },
                    //     // className: 'dt-body-center',
                    //     // className: 'dt-head-center',
                    //     targets: 14,
                    //     searchable: false,
                    //     orderable: false
                    // },

                    // {
                    //     "width": "100px", "targets": 1
                    // },
                ],
                autoWidth: false, // Ensures DataTable does not set its own width
                dom: 'Blfrtip', // Adds the buttons to the DataTable
                buttons: [{
                        extend: 'excelHtml5',
                        title: function() {
                            return $('.group_heading').text() || 'All Machines';
                        }
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
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: '',
                        customize: function(doc) {
                            let dynamicTitle = $('.group_heading').text() || 'All Machines';
                            doc.info.title = dynamicTitle;
                            doc.content[0].text = dynamicTitle;
                            doc.content[0].alignment = 'center';
                            doc.content[0].fontSize = 16;
                        }

                    },


                    {
                        extend: 'print',
                        title: function() {
                            return $('.group_heading').text() || 'All Machines';
                        }
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

        function openModal(id, ip, group) {
            // Example: log or set data into hidden fields
            console.log("Student ID:", id, "IP:", ip,  "group:", group);

            $('#modalHeadingText').text(group + ' Machine');
            $('#modalHeadingIp').text(ip);
            let user_id = $('#username').val(); // Clear previous input
            document.getElementById('user_id').value = user_id; //setting same userid in modal as searched in main form
            document.getElementById('machine_ip').value = ip;
            document.getElementById('machine_id').value = id;
            document.getElementById('group').value = group;

            // Show the Bootstrap modal
            var myModal = new bootstrap.Modal(document.getElementById('studentSearchModal'));
            myModal.show();
        }

        function enterPressUserSearch() {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission if it's inside a form
                getProfile();
            }

        }

        function getProfile() {
            var username = $('#username').val();
            $.ajax({
                url: '/search-user',
                type: "POST",
                data: {
                    username: username,
                },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        makeDataHtml(res.profileData);
                        $("#rfid_status").val(res.profileData.rfid_status);
                        $("#searchError_Usermessage").text('').hide();
                    } else {
                        // $('#success_message').text('').hide();
                        document.getElementById('dataDiv').innerHTML = '';
                        $('#searchError_Usermessage').html('No Data found');
                        $('#searchError_Usermessage').fadeIn('slow');
                        $('#searchError_Usermessage').fadeOut(5000);
                    }
                },
                error: function(xhr, status, error) {
                    if (typeof errorCallback === 'function') {
                        errorCallback(xhr, status, error);
                    }
                }
            });
        }

        function enterPressUserSearch_fromMachine() {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission if it's inside a form
                getProfile_fromMachine();
            }

        }

        function getProfile_fromMachine() {
            var user_id = $('#user_id').val();
            var machine_ip = $('#machine_ip').val();
            var machine_id = $('#machine_id').val();
            $.ajax({
                url: '/search-user-machine',
                type: "POST",
                data: {
                    user_id: user_id,
                    machine_ip: machine_ip,
                    machine_id: machine_id,
                },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Show loading spinner, hide error
                    $('#searchLoading').show();
                    $('#searchError').hide().text('');
                },
                success: function(res) {
                    console.log(res);
                    $('#searchLoading').hide(); // stop loading
                    if (res.success) {
                        console.log(res.profileData);
                        setModalData(res.profileData);
                    } else {
                        $('#searchError')
                            .html(res.msg)
                            .fadeIn('slow')
                            .delay(3000)
                            .fadeOut('slow');
                    }
                },
                error: function(xhr, status, error) {
                    $('#searchLoading').hide(); // stop loading
                    $('#searchError')
                        .text('An error occurred. Please try again.')
                        .fadeIn('slow')
                        .delay(5000)
                        .fadeOut('slow');

                    if (typeof errorCallback === 'function') {
                        errorCallback(xhr, status, error);
                    }
                }
            });
        }

        function makeDataHtml(profileData) {
            let dataDiv = document.getElementById('dataDiv');
            dataDiv.innerHTML = ''; // Clear previous content

            let sr = 1;
            console.log(profileData);
            let optionHtml = '';
            // adjustmentData.forEach(option => {
            //     optionHtml += `<option value="${option.ffac_id}">${option.ffac_title}</option>`;
            // });
            if (profileData) {
                let lettersHtml = `
                <div class="container-fluid" style=" background: #f6f6ff; padding: 30px; border-radius: 15px;">
                     <h3 class="text-center mb-5">User Profile</h3>
                    <div class="row align-items-center">
                        <!-- Enrollment Number -->
                        <div class="col-md-3 col-sm-3 col-12">

                            <p class="lead text-muted mb-0" style="font-size: 16px;">
                                <strong class="d-inline">Full Name:</strong> <span class="d-inline">${profileData.user_full_name}</span>
                            </p>
                        </div>

                         <div class="col-md-3 col-sm-2 col-12">
                            <p class="lead text-muted mb-0" style="font-size: 16px;">
                                <strong class="d-inline">Email:</strong> <span>${profileData.email}</span>
                            </p>
                        </div>

                        <div class="col-md-2 col-sm-2 col-12">
                                <p class="lead text-muted mb-0" style="font-size: 16px;">
                                    <strong class="d-inline">User Type:</strong> <span>${profileData.user_type}</span>
                                </p>
                        </div>

                         <div class="col-md-4 col-sm-2 col-12">
                                <p class="lead text-muted mb-0" style="font-size: 16px;">
                                    <strong class="d-inline">Department:</strong> <span>${profileData.dept}</span>
                                </p>
                        </div>
                    </div>
                   <form id="rfidForm" style="margin-top: 30px;">
                        @csrf
                        <div class="row align-items-center">
                            <div class="form-group col-md-3 col-sm-3 col-12">
                                <label for="rfid">RFID:</label>
                                <input type="text" name="rfid" id="rfid" class="form-control"
                                    value="${profileData.rfid ? profileData.rfid : ''}">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-12">
                                <label for="status">RFID Status:</label>
                                <select name="rfid_status" id="rfid_status" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-6">
                                <label for="descrip">Description:</label>
                                <textarea name="description" id="description" class="form-control" rows="2">
                                    ${profileData.rfid_description ? profileData.rfid_description : ''}
                                </textarea>
                            </div>
                        </div>

                      <!-- ✅ New Group Assigned Section -->
                        <div class="row mt-3 group-assigned">
                            <div class="form-group col-12">
                                <label class="d-block fw-bold mb-2">Group Assigned:</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="gate" name="acl_gate" value="1"
                                        ${profileData.gate_group && profileData.gate_group == 1 ? 'checked' : ''}>
                                    <label class="form-check-label" for="gate">Gate</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="gym" name="acl_gym" value="1"
                                        ${profileData.gym_group && profileData.gym_group == 1 ? 'checked' : ''}>
                                    <label class="form-check-label" for="gym">Gym</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="hostel" name="acl_hostel" value="1"
                                        ${profileData.hostel_group && profileData.hostel_group == 1 ? 'checked' : ''}>
                                    <label class="form-check-label" for="hostel">Hostel</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="lab" name="acl_lab" value="1"
                                        ${profileData.lab_group && profileData.lab_group == 1 ? 'checked' : ''}>
                                    <label class="form-check-label" for="lab">Lab</label>
                                </div>
                            </div>
                        </div>
                        <!-- ✅ End Group Assigned Section -->

                        <input type="text" hidden name="sisId" value="${profileData.id}" class="form-control">
                        <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                            <button type="button" class="btn btn-primary" id="submitrfidForm" onclick="submitRfidSetup()">Save</button>
                        </div>
                    </form>

                    <div id="error_message_rfid" class="alert alert-danger" style="display: none;"></div>
                    <div id="success_message_rfid" class="alert alert-success" style="display: none;"></div>

                </div>

                `;
                dataDiv.innerHTML = lettersHtml;
            } else {
                // Optionally display a message when there is no data
                dataDiv.innerHTML = '<p>No data available.</p>';
            }
        }

        function submitRfidSetup() {
            // Serialize form data
            var formData = $('#rfidForm').serialize();
            // Make an AJAX request
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/add-rfid',
                data: formData,
                success: function(res) {
                    $("#error_message_rfid").text('').hide();
                    $('#success_message_rfid').html(res.msg);
                    $('#success_message_rfid').fadeIn('slow');
                    $('#success_message_rfid').fadeOut(5000);
                    // loadFeeAdjustments();

                },
                error: function(res, xhr, status, error) {
                    var data = res.responseJSON;
                    var message = "";
                    if ('msg' in data) {
                        message = data.msg;
                    } else if ('errors' in data) {
                        var errors = data.errors;
                        $.each(errors, function(i, obj) {
                            message += "<p>" + obj[0] + "</p>";
                        });
                    }
                    $('#success_message_rfid').text('').hide();
                    $('#error_message_rfid').html(message);
                    $('#error_message_rfid').fadeIn('slow');
                    $('#error_message_rfid').fadeOut(5000);
                }
            });
        }

        function getFilterParams() {
            const machine_group = $('#machine_group').val();

            let filterParams = {
                machine_group: machine_group,

            };
            return filterParams;
        }

        function setModalData(profileData) {
            $("#user_machine_id").val(profileData.employeeNo);
            $("#user_machine_name").val(profileData.name);
            $("#gender").val(profileData.gender);
            $("#user_machine_status").val(profileData.userType);
            $("#validity_start").val(toDatetimeLocalFormat(profileData.Valid.beginTime));
            $("#validity_end").val(toDatetimeLocalFormat(profileData.Valid.endTime));
            $("#time_effective").prop("checked", profileData.Valid.enable === true);
            $("#verify_user_only").prop("checked", profileData.onlyVerify === true);


            // console.log(fromDatetimeLocalFormat($("#validity_start").val()));
            // console.log(fromDatetimeLocalFormat($("#validity_end").val()));
        }

        function refreshModalData() {
            $("#user_machine_id").val('');
            $("#user_machine_name").val('');
            $("#gender").val('');
            $("#user_machine_status").val('');
            $("#validity").val('');
            $("#time_effective").val('');
        }
        // Convert "2024-11-13T14:05:05" → "2024-11-13T14:05"
        function toDatetimeLocalFormat(dateTimeStr) {
            if (!dateTimeStr) return "";
            let d = new Date(dateTimeStr);
            let pad = (n) => (n < 10 ? "0" + n : n);

            return (
                d.getFullYear() + "-" +
                pad(d.getMonth() + 1) + "-" +
                pad(d.getDate()) + "T" +
                pad(d.getHours()) + ":" +
                pad(d.getMinutes())
            );
        }

        // Convert back: "2024-11-13T14:05" → "2024-11-13T14:05:00"
        function fromDatetimeLocalFormat(localStr) {
            if (!localStr) return "";
            return localStr + ":00";
        }
    </script>

@stop
