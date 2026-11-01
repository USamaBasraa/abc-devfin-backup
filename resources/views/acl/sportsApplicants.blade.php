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
</style>
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <div class="card-header" style="display:inline">
            <div style="float:left">
                <h2 class="card-title">Add Sports / Scholarship Students</h2>
            </div>
          </div>
        <form id="searchForm" method="post" style="margin-top:30px;">
            <div class="row justify-content-center">
                <div class="col-6 col-sm-12 col-md-6">
                    {{-- <label for="challan" style="margin-right: 10px; white-space: nowrap;">Search by Username / Enrollment no</label> --}}
                    <div class="input-group">
                        <input placeholder="Search by Enrollment no" autocomplete="off" class="form-control" type="text" name="user_name" id="username" onkeypress="enterPressUserSearch()">
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
        <div id="dataDiv"   style="margin-top:20px;">
        </div>
    </div>
    <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display">
                            <thead>
                            <tr>
                                <th scope="col">Session</th>
                                <th scope="col">Enrollment No.</th>
                                <th scope="col">Student Name</th>
                                <th scope="col">Student Category</th>
                                <th scope="col">Sports Category</th>
                                <th scope="col">Scholarship Category</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
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
        LoadSportsApplicants();
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
                $('#main-Id').val('');
                $('#pricing').val('');
                $('#detail').val('');
                // refreshFields();
                $("#error_message").text('').hide();
                $('#success_message').html(data.msg);
                $('#success_message').fadeIn('slow');
                $('#success_message').fadeOut(5000);
                LoadSportsApplicants();
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
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script>
    var sessions = @json($sessions);
    var scholarships = @json($scholarships);
     $('#username').autoComplete({
        resolver: 'custom',
        autoSelect: false,
        formatResult: function (item) {
            return {
                value: item.value,
                text: item.text + " [" + item.value + "] "
            };
        },
        select: function (evt, item) {
            console.log('Selected User:', item);
        },
        events: {
            search: function (searchParam, callback) {

                $.ajax({
                    url: '/auto-search-student',
                    type: 'GET',
                    data: { 'searchParam': searchParam },
                    success: function (res) {
                        console.log('Search results:', res);
                        callback(res);
                    },
                    error: function (xhr) {
                        console.error('Search error:', xhr.responseText);
                    }
                });
            }
        }
    });
    function enterPressUserSearch() {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission if it's inside a form
            getProfile();
        }

    }
    function getProfile(){
        var username = $('#username').val();
        $.ajax({
            url: '/search-student',
            type: "POST",
            data: {
                    username:username,
                },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    makeDataHtml(res.profileData);
                    $("#rfid_status").val(res.profileData.rfid_status);
                    $("#searchError_Usermessage").text('').hide();
                }
                else{
                    // $('#success_message').text('').hide();
                    document.getElementById('dataDiv').innerHTML = '';
                    $('#searchError_Usermessage').html('No Data found');
                    $('#searchError_Usermessage').fadeIn('slow');
                    $('#searchError_Usermessage').fadeOut(5000);
                }
            },
            error: function (xhr, status, error) {
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
            let sessions_options = ''; // Initialize the options string
            sessions.forEach(function(session) {
                sessions_options += '<option value="' + session.sess_id + '">' + session.sess_name + '</option>';
            });
            let scholarship_options = ''; // Initialize the options string
            scholarships.forEach(function(scholarship) {
                scholarship_options += '<option value="' + scholarship.scholarship_id + '">' + scholarship.scholarship_name + '</option>';
            });
            let lettersHtml = `
                <div class="container-fluid" style=" background: #f6f6ff; padding: 30px; border-radius: 15px;">
                     <h3 class="text-center mb-5">Student Profile</h3>
                   <table class="table">
                        <tbody>
                            <tr>
                                <td><strong>Student Name:</strong></td>
                                <td>${profileData.spi_name}</td>
                                <td><strong>Enrollment No:</strong></td>
                                <td>${profileData.sai_enrollment_no}</td>
                            </tr>
                            <tr>
                                 <td><strong>School:</strong></td>
                                <td>${profileData.school_name}</td>
                                <td><strong>Department:</strong></td>
                                <td>${profileData.dept_name}</td>
                                <td><strong>Program:</strong></td>
                                <td>${profileData.prog_name}</td>
                               </tr>
                        </tbody>
                    </table>
                    <form id="sportForm" style="margin-top: 30px;">
                        @csrf
                        <div class="row justify-content-center align-items-center">
                             <div class="form-group col-md-4 col-sm-3 col-12">
                                 <label for="student">Student Category</label>
                                <select name="student-category" id="student-category" onchange="subCategoryDropDown()" class="form-control">
                                    <option value="">Select Student category</option>
                                        <option value="Sports">Sports</option>
                                        <option value="Scholarship">Scholarship</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-3 col-12">
                                 <label for="session_sel">Session</label>
                                <select name="session" id="session_sel" class="form-control">
                                    <option value="">Select Session</option>
                                    `+sessions_options+`
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-3 col-12 sportsDropdown d-none">
                                <label for="status">Sport Category:</label>
                                <select name="sport-category" id="sport-category" class="form-control">
                                     <option value="">Select Sports Category</option>
                                    <option value="Swimming">Swimming</option>
                                    <option value="Cricket">Cricket</option>
                                    <option value="Soccer">Soccer</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Volleyball">Volleyball</option>
                                    <option value="Table Tennis">Table Tennis</option>
                                    <option value="Chess">Chess</option>
                                    <option value="Athletics">Athletics</option>
                                    <option value="Throwball">Throwball</option>
                                    <option value="Boxing">Boxing</option>
                                    <option value="Tennis">Tennis</option>
                                    <option value="Squash">Squash</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-3 col-12 scholashipDropdown d-none">
                                <label for="status">Scholarship:</label>
                                <select name="scholarship-category" id="scholarship-category" class="form-control">
                                     <option value="">Select Scholarship</option>
                                    `+scholarship_options+`
                                </select>
                            </div>

                        </div>
                        <input type="text" hidden name="mainId" id="main-Id" value="" class="form-control">
                        <input type="text" hidden name="enrolment-no" id="enrolment-no" value="${profileData.sai_enrollment_no}" class="form-control">
                        <input type="text" hidden name="school" value="${profileData.sai_sdpo_school_id}" class="form-control">
                        <input type="text" hidden name="dept" value="${profileData.sai_sdpo_dept_id}" class="form-control">
                        <input type="text" hidden name="prog" value="${profileData.sai_sdpo_prog_id}" class="form-control">
                        <input type="text" hidden name="student-name" id="student-name" value="${profileData.spi_name}" class="form-control">
                        <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                            <button type="button" class="btn btn-primary" id="submitSportForm" onclick="submitSportsForm()">Save</button>
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
    function subCategoryDropDown(){
       var studentCateg =  $('#student-category').val();
       if(studentCateg == 'Sports'){
        $('.scholashipDropdown').addClass('d-none');
        $('#scholarship-categorydown').val('');
        $('.sportsDropdown').removeClass('d-none');
       }else{
        $('.sportsDropdown').addClass('d-none');
        $('#sport-category').val('');
        $('.scholashipDropdown').removeClass('d-none');
       }
    }
    function submitSportsForm() {
        var enrollment_no = $('#enrolment-no').val();
       // Call checkChallan with a callback function
        checkChallan(enrollment_no, function(challans) {
            console.log(challans);

            if (challans > 0) {
                if (!confirm("There are existing unpaid gym challans against this student. Do you want to proceed and update challan?")) {
                    return;  // If user does not confirm, stop further execution
                }
            }
            // Serialize form data
            var formData = $('#sportForm').serialize();
            // Make an AJAX request
            // console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/add-sports-applicant',
                data: formData,
                success: function(res) {
                    $("#error_message_rfid").text('').hide();
                    $('#success_message_rfid').html(res.msg);
                    $('#success_message_rfid').fadeIn('slow');
                    $('#success_message_rfid').fadeOut(5000);
                    LoadSportsApplicants();

                },
                error: function(res, xhr, status, error) {
                    var data = res.responseJSON;
                    var message = "";
                    if ('msg' in data) {
                        message = data.msg;
                    } else if ('errors' in data) {
                        var errors = data.errors;
                        $.each(errors, function (i, obj) {
                            message += "<p>" + obj[0] + "</p>";
                        });
                    }
                    $('#success_message_rfid').text('').hide();
                    $('#error_message_rfid').html(message);
                    $('#error_message_rfid').fadeIn('slow');
                    $('#error_message_rfid').fadeOut(5000);
                }
            });
        });
    }
    function LoadSportsApplicants(){
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
                url:  "/get-sport-applicant-list",
                data: function(d) {
                        // Include the CSRF token in the request
                        d._token = $('meta[name="csrf-token"]').attr('content')
                    }
                },
            columns: [
                {data: "sess_name"},
                {data: "sa_enrolment_no"},
                {data: "sa_student_name"},
                {data: "sa_student_category"},
                {
                    data: "sa_sports_category",
                    render: function (data, type, row) {
                        if (data) {
                            return data;
                        } else {
                            return 'NA';
                        }
                    }
                },
                {
                    data: "scholarship_name",
                    render: function (data, type, row) {
                        if (data) {
                            return data;
                        } else {
                            return 'NA';
                        }
                    }
                },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        // console.log(row);
                        // var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:5px;" id="'+row["sa_id"]+'" onclick="editRow(`'+row["sa_id"]+'`,`'+row["sa_sess_id"]+'`,`'+row["sa_sports_category"]+'`,`'+row["sa_student_name"]+'`,`'+row["sa_enrolment_no"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                        //                             '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                        //                         '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRow('+row["sa_id"]+')"><i class="fa fa-trash"></i> Delete</button></div>';
                        var button = '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRow(`'+row["sa_id"]+'`,`'+row["sa_enrolment_no"]+'`)"><i class="fa fa-trash"></i> Delete</button></div>';
                                                return button;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 6,
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

                // {
                //     "width": "100px",
                //     "targets": 2
                // },
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
                    title: 'Sports Applicants',
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
                    title: 'Sports Applicants',
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
                    title: 'Sports Applicants',
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
    // function exportToExcel() {
    //     const table = document.getElementById('htmlTable');
    //     const rows = table.querySelectorAll('tr');

    //     let csvContent = "data:text/csv;charset=utf-8,";
    //     rows.forEach(row => {
    //         const cells = row.querySelectorAll('td, th');
    //         const rowData = Array.from(cells).map(cell => cell.textContent.replace(',', ''));
    //         csvContent += rowData.join(',') + "\n";
    //     });

    //     const encodedUri = encodeURI(csvContent);
    //     const link = document.createElement('a');
    //     link.setAttribute('href', encodedUri);
    //     link.setAttribute('download', 'table.csv');
    //     document.body.appendChild(link);
    //     link.click();
    // }
    // function exportToExcel(tableId) {
    //     $(`#${tableId}`).tableExport({type:'xlsx', escape:'false'});
    // }
    // function exportToPDF(tableId) {
    //     try {
    //         const doc = new jsPDF();
    //         doc.autoTable({ html: `#${tableId}` });
    //         doc.save('table.pdf');
    //     } catch (error) {
    //         console.error('Error exporting to PDF:', error);
    //         alert('An error occurred while exporting to PDF. Please try again.');
    //     }
    // }
    // function editRow(mainId, session, sportCategory, name, rollno){
    //     $('#main-Id').val(mainId);
    //     $('#session_sel').val(session);
    //     $('#sport-category').val(sportCategory);
    //     $('#enrolment-no').val(rollno);
    //     $('#student-name').val(name);

    //     $('html, body').animate({scrollTop: 0}, 'slow');
    // }
    function deleteRow(mainId, enrollmentNo){
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/delete-sport-applicant",
                data: {
                    mainId: mainId,
                    enrollmentNo: enrollmentNo,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadSportsApplicants();

                },
                error: function(error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }
    // function refreshFields(){
    //     $('#main-Id').val('');
    //     $('#category').val('');
    //     $('#user_type').val('');
    //     $('#plan').val('');
    //     $('#pricing').val('');
    //     $('#detail').val('');
    // }
    function checkChallan(enrollment_no, callback){
        $.ajax({
            type: "POST",
            url: "/check-challan",
            data: {
                enrollment_no: enrollment_no,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let noOfchallans = response.data;
                callback(noOfchallans);  // Call the callback function
            },
            error: function(error) {
                // Handle the error
                console.error("Error:", error);
                callback(null);  // In case of error, you can pass null or handle differently
            }
        });
    }
</script>

@stop

