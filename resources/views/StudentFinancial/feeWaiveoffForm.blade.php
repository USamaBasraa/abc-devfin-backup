@extends('layouts.app')

@section('title', 'Create Other Scholarship')

@section('content')
    <style>
        .focused-border {
            border: 3px solid #007bff;
            /* Blue border for focus */
            border-radius: 5px;
            outline: none;
            /* Remove default focus outline */
            transition: border 0.5s ease-in-out;
        }

        .custom-card-header {
            background-color: #ffffff;
            /* Neutral background */
            color: #4454c3;
            /* Theme color for text and icons */
            text-align: center;
            /* Center alignment */
            font-weight: bold;
            /* Bold text */
            padding: 1rem;
            /* Vertical padding */
            font-size: 1.2rem;
            /* Font size */
            border-bottom: 3px solid #4454c3 !important;
            /* Ensure the border is present */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important;
            /* Subtle shadow */
        }









        /* Initial hidden state for the table */
        #scholarshipsTable {
            display: none;
        }

        /* Fade-in animation for the search box */
        .dataTables_filter input {
            animation: fadeIn 1s ease-in-out;
        }

        /* Pagination fade-in animation */
        .dataTables_paginate {
            display: none;
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

        #scholarshipsTable thead th {
            background-color: #f2f2f2;
            /* light grey */
            color: #333;
            /* readable dark text */
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }

        /* Keyframes for fade-in animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>


    <div class="container-fluid">
        <div class="card p-4 pt-4">
            <form id="singleChallanForm">
                <div class="row">
                    <h3>Fee Waive Off Form</h3>
                </div>
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="studentEnrollmentNo">Student Id / Enrollment No#</label>
                        <div class="input-group mb-3" style="max-width: 400px; margin: auto;">
                            <!-- Input Field -->
                            <input type="text" class="form-control" id="studentEnrollment" name="studentEnrollment"
                                placeholder="Enter student Id / enrollment number" onkeydown="handleEnterKey(event)"
                                aria-label="Student Enrollment Number" style="border-right: none;">

                            <!-- Search Button with Icon -->
                            <button class="btn btn-primary" type="button" id="searchButton" onclick="searchStudent()"
                                style="border-left: none;">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                        <span id="studentEnrollment-error" class="error text-danger"></span>
                    </div>
                </div>
            </form>


            <div class="row">
                <div class="container">
                    <div id="profileDiv"></div>
                    <div id="formContainer"></div>

                </div>
            </div>



            <div id="table-container"></div>

        </div>
    </div>



@endsection


@section('javascript')


    {{-- <script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script> --}}


    <script>
        const waiverData = @json($waiverData);
        const firstload = @json($firstload);
        $(document).ready(function() {
            // Call the function with data from PHP
            makeRowTableHtml(waiverData, firstload);

        });

        function submitWaiverForm() {
            // Disable the button and show spinner
            var button = $('#submitButton'); // Assuming your button has the ID 'submitButton'
            button.prop('disabled', true); // Disable button
            button.html(
                '<span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span> Submitting...'
            ); // Show spinner
            const form = document.getElementById('WaiverForm');
            const formData = new FormData(form);
            $.ajax({
                url: 'submit-fee-waive', // Your endpoint
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token
                },
                success: function(response) {
                    button.prop('disabled', false);
                    button.html('Submit');

                    if (response.success === true) {
                        setTimeout(searchStudent, 2000);

                        showNotification(
                            response.msg || 'Fee waiver processed successfully!',
                            'success',
                            'notificationDiv'
                        );

                        document.getElementById('WaiverForm').reset();

                        $('html, body').animate({
                            scrollTop: 600
                        }, {
                            duration: 900,
                            easing: 'swing'
                        });

                    } else {
                        showNotification(
                            response.msg || 'Operation failed.',
                            'danger',
                            'notificationDiv'
                        );
                    }
                },

                error: function(jqXHR, textStatus, errorThrown) {
                    button.prop('disabled', false);
                    button.html('Submit');

                    // Check for validation error
                    if (jqXHR.status === 422) {
                        var errors = jqXHR.responseJSON.errors;
                        var errorMessages = '';

                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorMessages += '<p>' + message + '</p>';
                            });
                        });

                        showNotification(errorMessages, 'danger', 'notificationDiv');
                    } else {
                        showNotification(
                            'An error occurred while submitting the fee waiver. Please try again.',
                            'danger',
                            'notificationDiv'
                        );
                    }
                }






            });
        }
        function showNotification(message, type, targetDivId) {
            // Clear any existing notifications
            $('#' + targetDivId).empty();

            // Determine the alert class and icon based on the type (success, danger, info, etc.)
            var alertClass = 'alert-' + type;
            var icon = '';

            // Set the icon and background color based on the type
            if (type === 'success') {
                icon = '<i class="fa fa-check-circle"></i>'; // Success icon
            } else if (type === 'danger') {
                icon = '<i class="fa fa-times-circle"></i>'; // Error icon
            } else if (type === 'warning') {
                icon = '<i class="fa fa-exclamation-triangle"></i>'; // Warning icon
            } else if (type === 'info') {
                icon = '<i class="fa fa-info-circle"></i>'; // Info icon
            }

            // Create the alert HTML dynamically with icon
            var alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="display:none;">
                    <strong>${icon} ${message}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            // Append the alert to the specified div
            var alertElement = $(alertHtml);
            $('#' + targetDivId).append(alertElement);

            // Slide in the notification from the top
            alertElement.slideDown(500, function() {
                // Apply a bounce effect after the slide-in animation
                alertElement.effect("bounce", {
                    times: 2,
                    distance: 10
                }, 500);

                // Hide the notification after 8 seconds with a fade-out animation
                setTimeout(function() {
                    alertElement.fadeOut(500, function() {
                        alertElement.remove(); // Remove the alert from the DOM
                    });
                }, 5000);
            });
        }
        // Function to handle Enter key press
        function handleEnterKey(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent default form submission
                searchStudent(); // Call the searchStudent function
            }
        }
        // Main search function
        function searchStudent() {
            var enrollmentNo = $('#studentEnrollment').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            // Clear any previous error message
            $('#studentEnrollment-error').text('');
            // Validate the input
            if (enrollmentNo.trim() === '') {
                $('#studentEnrollment-error').text('Please enter an enrollment number.');
                return;
            }

            // Send the AJAX request
            $.ajax({
                url: '/fee-waive-student-rec',
                type: 'get',
                data: {
                    studentEnrollment: enrollmentNo
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(allData) {
                    // console.log(allData.studentRec);
                    // Handle success response

                    let getWaiverByEnrollmentNo = allData.getWaiverByEnrollmentNo;
                    let challanTypes = allData.challanTypes;

                    // Fix the sessions object to convert it into an array of { id, name }
                    let sessionsArray = Object.keys(allData.sessions).map(key => ({
                        id: key,
                        name: allData.sessions[key]
                    }));
                    sessionsArray.sort((a, b) => a.id - b.id);

                    // Use scholarships directly as it is already an array
                    let scholarshipsArray = allData.scholarships;
                    makeFormHtml(sessionsArray, scholarshipsArray, challanTypes);

                    makeProfileHtml(allData.studentRec);
                    makeRowTableHtml(getWaiverByEnrollmentNo);


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle error response
                    console.error(textStatus, errorThrown);
                    $('#studentEnrollment-error').text('An error occurred while searching. Please try again.');
                }
            });
        }
        function makeProfileHtml(data) {
            console.log(data);
            let dataDiv = document.getElementById('profileDiv');
            dataDiv.innerHTML = ''; // Clear previous content

            if (data && data.usernameError) {
                // Show error if usernameError exists


                let errorHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    ${data.usernameError}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                dataDiv.innerHTML = errorHtml;
            } else if (data) {
                // Generate Profile Card
                $('#st_school_id').val(data.school_id);
                $('#st_dept_id').val(data.dept_id);
                $('#st_prog_id').val(data.prog_id);
                $('#fos_app_id').val(data.aai_id);
                $('#fos_enrollment_no').val(data.username);

                let imageUrl = data.api_imagename ?
                    `https://admission.bnu.edu.pk/images/${data.api_imagename}` :
                    `https://cms.bnu.edu.pk/students_pics/${data.username.toUpperCase()}.jpg`;

                let profileHtml = `
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header custom-card-header d-flex align-items-center p-2">
                    <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                    <span class="h6 mb-0">Student Info</span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <!-- Student Image -->
                        <div class="col-md-2 text-center mb-2">
                            <img
                                src="${imageUrl}"
                                alt="Student Image"
                                class="img-fluid rounded-circle shadow-sm mb-2"
                                style="width: 80px; height: 80px;">
                            <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${data.spi_name || 'N/A'}</h6>
                            <span class="badge bg-info text-white" style="font-size: 0.8rem;">${data.sai_student_status || 'N/A'}</span>
                        </div>

                        <!-- Information Section -->
                        <div class="col-md-10">
                            <div class="row">

                                ${generateInfoBox('fa-id-card', 'Enrollment No / App Id:', `${data.username || 'NA'} / ${data.aai_id || 'NA'}`)}

                                ${generateInfoBox('fa-envelope', 'Email:', data.spi_email || 'N/A')}
                                ${generateInfoBox('fa-phone', 'Mobile:', data.spi_mobile || 'N/A')}
                                ${generateInfoBox('fa-building', 'School:', data.school_name || 'N/A')}
                                ${generateInfoBox('fa-university', 'Department:', data.dept_name || 'N/A')}
                                ${generateInfoBox('fa-book', 'Program:', data.prog_name || 'N/A')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

                dataDiv.innerHTML = profileHtml;
            } else {
                // Show a generic error message if data does not meet the criteria
                let noDataHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                No data available
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
                dataDiv.innerHTML = noDataHtml;
            }
        }
        // Helper Function to Generate Compact Info Boxes
        function generateInfoBox(iconClass, label, value) {
            return `
            <div class="col-md-4 mb-3">
                <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
                    <i class="fa ${iconClass} mr-3 text-muted" style="font-size: 1.2rem;"></i>
                    <div>
                        <span class="text-muted d-block" style="font-size: 0.85rem;">${label}</span>
                        <strong style="font-size: 0.9rem;">${value}</strong>
                    </div>
                </div>
            </div>`;
        }
        function makeFormHtml(sessions, scholarships, challanTypes) {
            // Log the data for debugging
            //  console.log(sessions);
            // console.log(scholarships);

            // Ensure sessions and scholarships are arrays
            if (!Array.isArray(sessions) || !Array.isArray(scholarships)) {
                console.error("sessions or scholarships are not valid arrays!");
                return;
            }



            // HTML structure
            let htmlContent = `
                    <form id="WaiverForm">
                        @csrf
                        <input type="hidden" id="fos_app_id" name="fos_app_id" value="">
                        <input type="hidden" id="fos_enrollment_no" name="fos_enrollment_no" value="">
                        <input type="hidden" id="updateId" name="updateId" value="">
                        <!-- Card Header -->
                        <div class="card-header custom-card-header d-flex align-items-center p-2 mb-3">
                            <i class="fa fa-money mr-2" style="font-size: 1.5rem;"></i>
                            <span class="h6 mb-0">Waiver Details</span>
                        </div>
                        <div class="row">
                            <!-- Session Field -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sessionId" class="font-weight-bold">Session</label>
                                    <select id="sessionId" name="sessionId" class="form-control">
                                        <option value="">All Sessions</option>
                                        ${sessions.length > 0
                                            ? sessions.sort((a, b) => b.id - a.id) // Sorting in descending order by id
                                                .map(session => `<option value="${session.id}">${session.name}</option>`)
                                                .join('')
                                            : '<option value="">No Sessions Available</option>'
                                        }
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hostelite_type" class="font-weight-bold">Hostel Student Type</label>
                                    <select id="hostelite_type" name="hostelite_type" class="form-control">
                                        <option value="">Select Hostel Student Type</option>
                                        <option value="Existing">Existing</option>
                                        <option value="New_Admission">New Applicant</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="challanType" class="font-weight-bold">Challan Type</label>
                                    <select id="challanType" name="challanType" class="form-control">
                                        <option value="">Select Challan Type</option>
                                        ${
                                            Object.keys(challanTypes).length > 0
                                                ? Object.entries(challanTypes)
                                                    .map(([value, label]) =>
                                                        `<option value="${value}">${label}</option>`
                                                    )
                                                    .join('')
                                                : '<option value="">No Challan Types Available</option>'
                                        }
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="waive_fee_type" class="font-weight-bold">Waive Fee Type</label>
                                    <select id="waive_fee_type" name="waive_fee_type" class="form-control">
                                        <option value="">Select Fee Type</option>
                                        <option value="security">Security Fee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="remarks" class="font-weight-bold">Remarks</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Enter Remarks">
                                </div>
                            </div>
                        </div>
                       <!-- Section heading (strip) -->
                        <div class="card-header custom-card-header d-flex align-items-center p-2 mb-3">
                            <i class="fa fa-money mr-2" style="font-size: 1.5rem;"></i>
                            <span class="h6 mb-0">Waived Amounts</span>
                        </div>

                        <div class="row">

                            <!-- University Waiver Amount -->
                            <div class="col-md-4">
                                <div class="form-group" style="margin-top: 15px;">
                                    <label for="uni-waiver_amount" class="font-weight-bold">University Waiver Amount</label>
                                    <input type="text" id="uni-waiver_amount" name="uni_waiver_amount" class="form-control" placeholder="Enter University Waiver Amount" oninput="validateInput(this)">
                                </div>
                            </div>

                            <!-- Scholarship (Dropdown + Waiver Amount grouped) -->
                            <div class="col-md-8">
                                <div class="card border p-3 mb-2" style="background: #4555c314;">
                                    <div class="row">
                                        <!-- Scholarship Dropdown -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="scholarshipId" class="font-weight-bold">Scholarship</label>
                                                <select
                                                    class="custom-select form-control select2-show-search"
                                                    id="scholarshipId"
                                                    name="scholarshipId"
                                                    data-placeholder="Select Scholarship Type"
                                                >
                                                    <option value="">None</option>
                                                    ${
                                                        scholarships.length > 0
                                                            ? scholarships.map(scholarship =>
                                                                `<option value="${scholarship.scholarship_id}" data-category="${scholarship.scholarship_desc ? scholarship.scholarship_desc.toLowerCase() : ''}">
                                                                        ${scholarship.scholarship_name}
                                                                    </option>`
                                                            ).join('')
                                                            : '<option value="">No Scholarships Available</option>'
                                                    }
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Scholarship Waiver Amount -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="schlrshp-waiver_amount" class="font-weight-bold">Scholarship Waiver Amount</label>
                                                <input type="text" id="schlrshp-waiver_amount" name="schlrshp_waiver_amount" class="form-control" placeholder="Enter Scholarship Waiver Amount" oninput="validateInput(this)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-4 mt-3 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary btn-md" id="submitButton" onclick="submitWaiverForm()">
                                    <i class="fa fa-paper-plane"></i> Submit
                                </button>
                            </div>
                        </div>



                        <div id="notificationDiv" class='mt-2 mb-2'></div>
                    </form>



                    `;

            // Append the generated form to the container
            let formContainer = document.getElementById('formContainer');
            formContainer.innerHTML = htmlContent;
            $('#scholarshipId').select2({
                placeholder: 'Select Scholarship Type',
                allowClear: true,
                width: 'resolve' // or '412px' for exact width if you want
            });

            // Attach event listener for form submission
        }
        // Input validation function: Allow only numbers and a single dot with two decimal places
        function validateInput(input) {
            input.value = input.value
                .replace(/[^0-9.]/g, '') // Remove all non-numeric and non-dot characters
                .replace(/(\..*?)\..*/g, '$1') // Allow only one dot
                .replace(/^0+(\d)/, '$1') // Remove leading zeros
                .replace(/(\.\d{2})\d+/, '$1'); // Restrict to two decimal places
        }
        function makeRowTableHtml(data, firstload) {
            console.log('TEST DATA FOR TABLE:');
            console.log(data);
            const tableContainer = document.getElementById('table-container');
            tableContainer.innerHTML = '';

            if (!data || data.length === 0) {
                console.error("No data available to display.");
                return;
            }

            let tableHtml = `
            <div class="table-responsive">
                <table id="scholarshipsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Id / Enrollment No</th>
                            <th>Session</th>

                            <th>Challan Type</th>
                            <th>Hostel Student Type</th>
                            <th>Fee Type Waived</th>
                            <th>Amount Waived off</th>

                            <th>Scholarship Type</th>
                            <th>Scholarship Amount</th>`;

                if (firstload != 1) {
                    tableHtml += `<th>Actions</th>`;
                }

                tableHtml += `
                        </tr>
                    </thead>
                    <tbody>`;

                data.forEach(row => {
                    tableHtml += `
                <tr>
                    <td>${row.fwd_id}</td>
                    <td>${row.fwd_enrollment_no ? row.fwd_enrollment_no : row.fwd_app_id}</td>
                    <td>${row.sess_code}</td>
                    <td>${row.fwd_challan_type}</td>
                    <td>${row.fwd_hostelite_type}</td>
                    <td>${row.fwd_waive_fee_type}</td>
                    <td>${row.fwd_uni_waiver_amount ? row.fwd_uni_waiver_amount : 'NA'}</td>
                    <td>${row.scholarship_name ? row.scholarship_name : 'N/A'}</td>
                    <td>${row.fwd_schlrship_waiver_amount ? row.fwd_schlrship_waiver_amount : 'NA'}</td>`;

                    if (firstload != 1) {
                        tableHtml += `
                    <td>
                        <button type="button" class="btn btn-warning btn-sm"
                            onclick="editwaiver(${row.fwd_id})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </td>`;
                    }

                    tableHtml += `</tr>`;
                });

                tableHtml += `
                    </tbody>
                </table>
            </div>`;

            tableContainer.innerHTML = tableHtml;

            // Destroy existing instance safely
            if ($.fn.DataTable.isDataTable('#scholarshipsTable')) {
                $('#scholarshipsTable').DataTable().destroy();
            }

            $('#scholarshipsTable').DataTable({
                dom: 'Blfrtip',

                paging: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                serverSide: false,

                searching: true,
                ordering: true,
                info: true,
                stateSave: true,

                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Waived Fee',
                        // exportOptions: {
                        //     columns: ':not(:last-child)' // EXCLUDE Actions safely
                        // }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Waived Fee',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        // exportOptions: {
                        //     columns: ':not(:last-child)'
                        // }
                    },
                    {
                        extend: 'print',
                        title: 'Waived Fee',
                        // exportOptions: {
                        //     columns: ':not(:last-child)'
                        // }
                    }
                ],

                initComplete: function() {
                    $('#scholarshipsTable').fadeIn(1000);
                },

                drawCallback: function() {
                    $('.dataTables_paginate').fadeIn(500);
                }
            });
        }
        function editwaiver(id) {
            $.ajax({
                url: '/edit-fee-waiver/' +
                    id, // Your endpoint to fetch scholarship details
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        // Populate the form with fetched scholarship data
                        $('#updateId').val(response.data.fwd_id);
                        // $('#fos_enrollment_no').val(response.data.fwd_enrollment_no);
                        // $('#fos_app_id').val(response.data.fwd_app_id);
                        $('#sessionId').val(response.data.fwd_session_id);
                        $('#hostelite_type').val(response.data.fwd_hostelite_type);
                        $('#challanType').val(response.data.fwd_challan_type);
                        $('#waive_fee_type').val(response.data.fwd_waive_fee_type);
                        $('#remarks').val(response.data.fwd_remarks);
                        $('#uni-waiver_amount').val(response.data.fwd_uni_waiver_amount);
                        $('#scholarshipId').val(response.data.fwd_scholarship_id);
                        $('#schlrshp-waiver_amount').val(response.data.fwd_schlrship_waiver_amount);
                        $('#scholarshipId').trigger('change'); // To update select2 UI
                        $('html, body').animate({
                            scrollTop: 300 // Scroll 200px down from the top
                        }, 500); // Scroll duration in milliseconds (500ms for smooth scroll)

                    } else {
                        alert('Failed to load scholarship details');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: ", textStatus, errorThrown);
                    alert('An error occurred while fetching scholarship details. Please try again.');
                }
            });
        }
    </script>

@stop
