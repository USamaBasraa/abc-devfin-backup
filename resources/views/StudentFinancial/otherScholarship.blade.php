@extends('layouts.app')

@section('title', 'Create Other Scholarship')

@section('content')
<style>
    .focused-border {
        border: 3px solid #007bff; /* Blue border for focus */
        border-radius: 5px;
        outline: none; /* Remove default focus outline */
        transition: border 0.5s ease-in-out;
    }
    .custom-card-header {
        background-color: #ffffff; /* Neutral background */
        color: #4454c3; /* Theme color for text and icons */
        text-align: center; /* Center alignment */
        font-weight: bold; /* Bold text */
        padding: 1rem; /* Vertical padding */
        font-size: 1.2rem; /* Font size */
        border-bottom: 3px solid #4454c3 !important; /* Ensure the border is present */
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important; /* Subtle shadow */
    }









    /* Table wrapper hidden until DataTables init completes */
#table-container .table-responsive {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}
#table-container .table-responsive.table-ready {
    opacity: 1;
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

.dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
    margin-top:5px;
}

.dataTables_wrapper .dt-buttons {
    margin-right: 15px;
}
#scholarshipsTable thead th,
#allScholarshipsTable thead th,
#studentScholarshipsTable thead th {
    background-color: #f2f2f2;   /* light grey */
    color: #333;                /* readable dark text */
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
                    <h3>Other Scholarship</h3>
                </div>
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="studentEnrollmentNo">Student Id / Enrollment No#</label>
                        <div class="input-group mb-3" style="max-width: 400px; margin: auto;">
                            <!-- Input Field -->
                            <input
                                type="text"
                                class="form-control"
                                id="studentEnrollment"
                                name="studentEnrollment"
                                placeholder="Enter student Id / enrollment number"
                                onkeydown="handleEnterKey(event)"
                                aria-label="Student Enrollment Number"
                                style="border-right: none;">

                            <!-- Search Button with Icon -->
                            <button
                                class="btn btn-primary"
                                type="button"
                                id="searchButton"
                                onclick="searchStudent()"
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
  const scholarshipsData = @json($scholarshipsData);

    function getStudentField(data, ...keys) {
        if (!data || typeof data !== 'object') {
            return null;
        }

        for (const key of keys) {
            const variants = [key, key.toUpperCase(), key.toLowerCase()];
            for (const variant of variants) {
                if (data[variant] !== undefined && data[variant] !== null && data[variant] !== '') {
                    return data[variant];
                }
            }
        }

        return null;
    }

    function isStudentProfileLoaded(studentRec) {
        if (!studentRec || typeof studentRec !== 'object' || Array.isArray(studentRec)) {
            return false;
        }

        if (studentRec.usernameError || studentRec.success === false) {
            return false;
        }

        return !!(
            getStudentField(studentRec, 'aai_id') ||
            getStudentField(studentRec, 'username', 'sai_enrollment_no', 'cms_sai_enrollment_no')
        );
    }

    $(document).ready(function () {
        makeRowTableHtml(scholarshipsData, false);
    });

    function submitScholarshipForm() {
    // Disable the button and show spinner
    var button = $('#submitButton'); // Assuming your button has the ID 'submitButton'
    button.prop('disabled', true); // Disable button
    button.html('<span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span> Submitting...'); // Show spinner

    // Manually get values from the form fields
    var admission_fee = $('#admission_fee').val();
    var security_fee = $('#security_fee').val();
    var activity_charges = $('#activity_charges').val();
    var medical_insurance = $('#medical_insurance').val();
    var stipend_amount = $('#stipend_amount').val();
    var updateId = $('#updateId').val();
    // var stipend = $('#stipend').val();

    var formData = {
        updateId: updateId,
        admission_fee: admission_fee,
        security_fee: security_fee,
        activity_charges: activity_charges,
        medical_insurance: medical_insurance,
        stipend_amount: stipend_amount,
        sessionId: $('#sessionId').val(),
        scholarshipId: $('#scholarshipId').val(),
        // Arrears are recorded per challan category — required by the controller.
        challan_category: $('#challan_category').val(),
        fos_app_id: $('#fos_app_id').val(),
        fos_enrollment_no: $('#fos_enrollment_no').val(),
        _token: $('meta[name="csrf-token"]').attr('content')  // CSRF Token
    };

    // Send the form data via AJAX to submit the scholarship
    $.ajax({
            url: '/finance/submit-other-scholarship', // Your endpoint
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF Token
            },
            success: function(response) {
                console.log("AJAX Success Response: ", response);

                // Re-enable the button and reset text
                button.prop('disabled', false);
                button.html('Submitted'); // Reset button text

                if (response.status === 'success') {
                    setTimeout(searchStudent, 2000);
                    showNotification(response.message || 'Scholarship successfully submitted!', 'success', 'notificationDiv');
                    document.getElementById('scholarshipForm').reset();
                    $('html, body').animate({ scrollTop: 600 }, { duration: 900, easing: 'swing' });
                }
                else if (response.status === 'error') {
                    showNotification(response.message, 'danger', 'notificationDiv');
                } else {
                    showNotification('Unexpected response from the server.', 'danger', 'notificationDiv');
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: ", textStatus, errorThrown);

            // Re-enable the button and reset text
            button.prop('disabled', false);
            button.html('Submit'); // Reset button text

            // Check if the error status is validation error (422)
            if (jqXHR.status === 422) {
                var errors = jqXHR.responseJSON.errors;
                var errorMessages = '';

                // Loop through each error and accumulate the messages
                $.each(errors, function(field, messages) {
                    $.each(messages, function(index, message) {
                        errorMessages += '<p>' + message + '</p>'; // Collect each error message
                    });
                });

                // Show all error messages in a single notification
                showNotification(errorMessages, 'danger', 'notificationDiv');
            } else {
                // General error notification
                showNotification('An error occurred while submitting the scholarship. Please try again.', 'danger', 'notificationDiv');
            }
        },




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
        icon = '<i class="fa fa-check-circle"></i>';  // Success icon
    } else if (type === 'danger') {
        icon = '<i class="fa fa-times-circle"></i>';  // Error icon
    } else if (type === 'warning') {
        icon = '<i class="fa fa-exclamation-triangle"></i>';  // Warning icon
    } else if (type === 'info') {
        icon = '<i class="fa fa-info-circle"></i>';  // Info icon
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
    alertElement.slideDown(500, function () {
        // Apply a bounce effect after the slide-in animation
        alertElement.effect("bounce", { times: 2, distance: 10 }, 500);

        // Hide the notification after 8 seconds with a fade-out animation
        setTimeout(function () {
            alertElement.fadeOut(500, function () {
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
            url: '/finance/other-scholarship-student-rec',
            type: 'get',
            // The URL is identical for the same student, so without this the browser
            // replays the cached response and a row just submitted does not appear
            // until a hard refresh. Never cache the arrears list.
            cache: false,
            data: {
                studentEnrollment: enrollmentNo
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(allData) {
                // console.log(allData.studentRec);
                // Handle success response

                let getScholarshipsByEnrollmentNo = allData.getScholarshipsByEnrollmentNo;

                // Fix the sessions object to convert it into an array of { id, name }
                let sessionsArray = Object.keys(allData.sessions).map(key => ({
                    id: key,
                    name: allData.sessions[key]
                }));
                sessionsArray.sort((a, b) => a.id - b.id);

                // Use scholarships directly as it is already an array
                let scholarshipsArray = allData.scholarships;
                const studentFound = isStudentProfileLoaded(allData.studentRec);

                makeFormHtml(sessionsArray, scholarshipsArray);
                makeProfileHtml(allData.studentRec);
                makeRowTableHtml(getScholarshipsByEnrollmentNo, studentFound);


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
            const appId = getStudentField(data, 'aai_id');
            const enrollmentNo = getStudentField(data, 'username', 'sai_enrollment_no', 'cms_sai_enrollment_no');
            const studentName = getStudentField(data, 'spi_name', 'api_name') || 'N/A';
            const studentStatus = getStudentField(data, 'sai_student_status', 'aai_status') || 'N/A';
            const studentEmail = getStudentField(data, 'spi_email', 'spi_personal_email', 'api_email') || 'N/A';
            const studentMobile = getStudentField(data, 'spi_mobile', 'api_mobile') || 'N/A';
            const schoolName = getStudentField(data, 'school_name') || 'N/A';
            const deptName = getStudentField(data, 'dept_name') || 'N/A';
            const progName = getStudentField(data, 'prog_name') || 'N/A';
            const apiImageName = getStudentField(data, 'api_imagename');

            $('#st_school_id').val(getStudentField(data, 'school_id'));
            $('#st_dept_id').val(getStudentField(data, 'dept_id'));
            $('#st_prog_id').val(getStudentField(data, 'prog_id'));
            $('#fos_app_id').val(appId);
            $('#fos_enrollment_no').val(enrollmentNo);

            let imageUrl = apiImageName
                ? `https://admission.bnu.edu.pk/images/${apiImageName}`
                : `https://cms.bnu.edu.pk/students_pics/${String(enrollmentNo || '').toUpperCase()}.jpg`;

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
                            <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${studentName}</h6>
                            <span class="badge bg-info text-white" style="font-size: 0.8rem;">${studentStatus}</span>
                        </div>

                        <!-- Information Section -->
                        <div class="col-md-10">
                            <div class="row">

                                ${generateInfoBox('fa-id-card', 'Enrollment No / App Id:', `${enrollmentNo || 'NA'} / ${appId || 'NA'}`)}

                                ${generateInfoBox('fa-envelope', 'Email:', studentEmail)}
                                ${generateInfoBox('fa-phone', 'Mobile:', studentMobile)}
                                ${generateInfoBox('fa-building', 'School:', schoolName)}
                                ${generateInfoBox('fa-university', 'Department:', deptName)}
                                ${generateInfoBox('fa-book', 'Program:', progName)}
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



// Challan categories that can carry arrears. additional_Full_semester is excluded
// — it has no arrears. Mirrors ScholarshipAssignController::ARREARS_CHALLAN_CATEGORIES
// and the arrears-applicable categories in StudentRecordController.
var ARREARS_CHALLAN_CATEGORIES = [
    { value: 'Full_semester',       label: 'Full Semester' },
    { value: 'Credit_hours',        label: 'Credit Hour' },
    { value: 'Short/Summer_Course', label: 'Short / Summer Course' }
];

// Rows written before FOS_CHALLAN_CATEGORY existed are Full Semester arrears.
function challanCategoryLabel(value) {
    var v = value || 'Full_semester';
    for (var i = 0; i < ARREARS_CHALLAN_CATEGORIES.length; i++) {
        if (ARREARS_CHALLAN_CATEGORIES[i].value === v) { return ARREARS_CHALLAN_CATEGORIES[i].label; }
    }
    return v;
}

    function makeFormHtml(sessions, scholarships) {
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
<form id="scholarshipForm">
    @csrf
    <input type="hidden" id="fos_app_id" name="fos_app_id" value="">
    <input type="hidden" id="fos_enrollment_no" name="fos_enrollment_no" value="">
    <input type="hidden" id="updateId" name="updateId" value="">

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

        <!-- Scholarship Field -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="scholarshipId" class="font-weight-bold">Scholarship</label>
                <select id="scholarshipId" name="scholarshipId" class="form-control">
                    <option value="">All Scholarships</option>
                    ${scholarships.length > 0
                        ? scholarships.map(scholarship => `<option value="${scholarship.scholarship_id}">${scholarship.scholarship_name}</option>`).join('')
                        : '<option value="">No Scholarships Available</option>'
                    }
                </select>
            </div>
        </div>

        <!-- Challan Category: arrears are recorded PER challan category, so the same
             scholarship can carry different arrears on a Full Semester vs a Credit
             Hour challan. Only the arrears-applicable categories are offered. -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="challan_category" class="font-weight-bold">Challan Category</label>
                <select id="challan_category" name="challan_category" class="form-control">
                    ${ARREARS_CHALLAN_CATEGORIES.map(function(c) {
                        return '<option value="' + c.value + '">' + c.label + '</option>';
                    }).join('')}
                </select>
            </div>
        </div>

        <!-- Input Fields -->
        ${['admission_fee', 'security_fee', 'activity_charges', 'medical_insurance', 'stipend_amount']
            .map(field => `
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="${field}" class="font-weight-bold">${field.replace(/fos_/g, '').replace(/_/g, ' ').toUpperCase()}</label>
                        <input type="number" step="0.01" id="${field}" name="${field}" class="form-control" placeholder="Enter ${field.replace(/_/g, ' ')}" oninput="validateInput(this)">
                    </div>
                </div>
            `).join('')}

        <!-- Submit Button in the Same Row -->
      <div class="col-md-4 mt-3 d-flex align-items-center">
   <button type="button" class="btn btn-primary btn-md" id="submitButton" onclick="submitScholarshipForm()">
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




function clearScholarshipsTableSavedState() {
    Object.keys(localStorage).forEach(function (key) {
        if (key.indexOf('DataTables_') === 0 && (
            key.indexOf('scholarshipsTable') !== -1 ||
            key.indexOf('allScholarshipsTable') !== -1 ||
            key.indexOf('studentScholarshipsTable') !== -1
        )) {
            localStorage.removeItem(key);
        }
    });
}

function makeRowTableHtml(data, showActions = false) {

    const tableContainer = document.getElementById('table-container');
    const tableId = showActions ? 'studentScholarshipsTable' : 'allScholarshipsTable';

    // Destroy DataTable before replacing DOM (otherwise state cannot be cleared)
    ['#allScholarshipsTable', '#studentScholarshipsTable', '#scholarshipsTable'].forEach(function (selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            const existingTable = $(selector).DataTable();
            existingTable.state.clear();
            existingTable.destroy();
        }
    });
    clearScholarshipsTableSavedState();

    tableContainer.innerHTML = '';

    const rows = Array.isArray(data) ? data : Object.values(data || {});

    if (rows.length === 0 && !showActions) {
        console.error("No data available to display.");
        return;
    }

    let tableHtml = `
        <div class="table-responsive">
            <table id="${tableId}" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>`;

    if (showActions) {
        tableHtml += `<th class="text-nowrap">Actions</th>`;
    }

    tableHtml += `
                        <th>Student Id / Enrollment No</th>
                        <th>Session ID</th>
                        <th>Scholarship Type</th>
                        <th>Challan Category</th>
                        <th>Admission Fee</th>
                        <th>Security Fee</th>
                        <th>Activity Charges</th>
                        <th>Medical Insurance</th>
                        <th>Stipend</th>
                    </tr>
                </thead>
                <tbody>`;

    if (rows.length === 0) {
        const colspan = showActions ? 11 : 10;
        tableHtml += `<tr><td colspan="${colspan}" class="text-center text-muted">No scholarship records for this student.</td></tr>`;
    } else {
        rows.forEach(row => {
            const rowId = row.id ?? row.ID;
            tableHtml += `
            <tr>
                <td>${rowId ?? ''}</td>`;

            if (showActions) {
                tableHtml += `
                <td class="text-nowrap">
                    <button type="button" class="btn btn-warning btn-sm edit-scholarship-btn"
                        data-id="${rowId}">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                </td>`;
            }

            tableHtml += `
                <td>${row.fos_enrollment_no ? row.fos_enrollment_no : row.fos_app_id}</td>
                <td>${row.sess_code}</td>
                <td>${row.scholarship_name}</td>
                <td>${challanCategoryLabel(row.fos_challan_category)}</td>
                <td>${row.fos_admission_fee}</td>
                <td>${row.fos_security_fee}</td>
                <td>${row.fos_activity_charges}</td>
                <td>${row.fos_medical_insurance}</td>
                <td>${row.fos_stipend}</td>
            </tr>`;
        });
    }

    tableHtml += `
                </tbody>
            </table>
        </div>`;

    tableContainer.innerHTML = tableHtml;

    const table = $('#' + tableId).DataTable({
        dom: 'Blfrtip',

        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        serverSide: false,
        scrollX: true,
        responsive: false,
        autoWidth: false,

        searching: true,
        ordering: true,
        info: true,
        stateSave: false,

        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Scholarship',
                exportOptions: showActions ? { columns: ':not(.edit-col)' } : {}
            },
            {
                extend: 'pdfHtml5',
                title: 'Scholarship',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: showActions ? { columns: ':not(.edit-col)' } : {}
            },
            {
                extend: 'print',
                title: 'Scholarship',
                exportOptions: showActions ? { columns: ':not(.edit-col)' } : {}
            }
        ],

        columnDefs: showActions ? [
            { targets: 1, className: 'edit-col text-nowrap', orderable: false, searchable: false }
        ] : [],

        initComplete: function () {
            this.api().columns.adjust().draw(false);
            tableContainer.querySelector('.table-responsive').classList.add('table-ready');
        },

        drawCallback: function () {
            $('.dataTables_paginate').fadeIn(500);
        }
    });

    $(tableContainer).off('click', '.edit-scholarship-btn').on('click', '.edit-scholarship-btn', function () {
        editScholarship($(this).data('id'));
    });

    return table;
}




function editScholarship(scholarshipId) {
    $.ajax({
        url: '/finance/edit-other-scholarship/' + scholarshipId, // Your endpoint to fetch scholarship details
        type: 'GET',
        cache: false,   // otherwise an edited row loads its pre-edit values
        success: function(response) {
            if (response.status === 'success') {
                // Populate the form with fetched scholarship data
                $('#updateId').val(response.data.id);
                // NULL (pre-FOS_CHALLAN_CATEGORY rows) -> Full Semester.
                $('#challan_category').val(response.data.fos_challan_category || 'Full_semester');
                $('#admission_fee').val(response.data.fos_admission_fee);
                $('#security_fee').val(response.data.fos_security_fee);
                $('#activity_charges').val(response.data.fos_activity_charges);
                $('#medical_insurance').val(response.data.fos_medical_insurance);
                $('#stipend_amount').val(response.data.fos_stipend);
                $('#sessionId').val(response.data.fos_sess_id);
                $('#scholarshipId').val(response.data.fos_scholarship_type);
                $('#fos_app_id').val(response.data.fos_app_id);
                $('#fos_enrollment_no').val(response.data.fos_enrollment_no);

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
