@extends('layouts.app')

@section('title')
BNU CMS
@stop

@section('content')


<style>
    /* CSS for styling */
    select:required {
        border-color: #dc3545;
    }

    select:required:focus {
        border-color: #dc3545;
    }

    .focused-border {
        border: 2px solid #007bff;
        border-radius: 6px;
        outline: none;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.2);
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .focused-border:focus {
        border-color: #0056b3;
        box-shadow: 0 6px 8px rgba(0, 86, 179, 0.3);
    }

    input[type="checkbox"] {
        margin-left: 10px;
        vertical-align: middle;
    }

    .custom-card-header {
        background-color: #ffffff;
        color: #4454c3;
        text-align: center;
        font-weight: bold;
        padding: 1rem;
        font-size: 1.2rem;
        border-bottom: 3px solid #4454c3 !important;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important;
    }

    .hidden {
        display: none;
    }

    #searchButton {
        display: flex;
        align-items: center;
        padding-left: 12px;
        padding-right: 12px;
        border-left: none;
    }

    #searchButton i,
    #searchButton span {
        transition: all 0.3s ease;
    }

    #searchButton:hover i {
        transform: scale(1.5);
        margin-right: 5px;
    }

    .clear-icon {
        cursor: pointer;
    }

    .clear-icon .input-group-text {
        padding: 0.25rem;
    }

    .clear-icon:hover {
        color: red;
    }



    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>










<div class="container-fluid">
    <!-- Profile Card -->
    <div class="card p-4 pt-4">
        <form id="studentProfileForm">

            @csrf
            <div class="row">
                <input type="hidden" name="verifyEdit" id="verifyEdit" value="">
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <!-- <div class="form-group col-md-4">
                    <label for="school_sel" class="fw-bold">School</label>
                    <select name="school" id="school_sel" class="form-control shadow-sm rounded"
                        onchange="getDepartments();">
                        <option value="">All Schools</option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="department" class="fw-bold">Department</label>
                    <select name="department" id="department" class="form-control shadow-sm rounded"
                        onchange="getPrograms();">
                    </select>
                </div>


                    <div class="form-group col-md-4">
                    <label for="program" class="fw-bold">Program</label>
                    <select name="program" id="Program" class="form-control shadow-sm rounded">
                    </select>
                </div> -->



                <div class="form-group col-md-5">
                    <label for="studentEnrollment" class="form-label font-weight-bold text-dark">
                        Search Student<span class="text-danger">*</span>
                    </label>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="studentEnrollment" name="studentEnrollment"
                            placeholder="Search Reg No, Name, or CNIC" aria-label="Student Registration Number"
                            onkeydown="handleEnterKey(event)" onkeyup="liveSearch(this.value)"
                            style="background: linear-gradient(135deg, #fcfcfc, #e0f7fa); border: 2px solid #ced4da; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 12px; transition: box-shadow 0.2s ease, border-color 0.2s ease;"
                            onmouseover="this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)'; this.style.borderColor='#007bff';"
                            onmouseout="this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#ced4da';">

                        <button class="btn btn-primary ml-1" type="button" id="searchButton" onclick="searchStudent()">
                            <i class="fa fa-search"></i> Search
                        </button>

                        <button type="button" id="clearButton" class="btn btn-outline-secondary ml-2" title="Clear All"
                            onclick="clearAllData()">
                            <i class="fa fa-times"></i> Clear
                        </button>



                        <div id="searchResults" class="dropdown-menu w-100"
                            style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 999;">
                        </div>

                    </div>
                    <span id="studentEnrollment-error" class="error text-danger"></span>
                </div>
            </div>

            <div class="row">
                <div class="container">
                    <div id="profileDiv"></div>
                </div>
            </div>


            <input type="hidden" name="st_school_id" id="st_school_id">
            <input type="hidden" name="st_dept_id" id="st_dept_id">
            <input type="hidden" name="st_prog_id" id="st_prog_id">
            <input type="hidden" name="st_aai_id" id="st_aai_id">
            <h2 id="successMsg" class="text-success"></h2>
        </form>
    </div>

    <!-- Widgets Section -->
    <div class="row mt-4" id="widgetsContainer">
        <!-- Widgets will be appended here dynamically -->
    </div>

</div>


<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modalBody" style="max-height: 100vh; overflow-y: auto;">
                <!-- Content will be dynamically inserted -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script>
    window.APP_URL = "{{ config('app.APP_URL') }}"; // Set APP_URL globally in JavaScript
</script>
<script>





    function clearAllData() {
        // Clear the search input
        $('#studentEnrollment').val('');

        // Clear any error messages
        $('#studentEnrollment-error').text('');

        // Hide search results dropdown
        $('#searchResults').hide();

        // Clear the profile div (student info)
        $('#profileDiv').empty();

        // Clear the widgets container
        $('#widgetsContainer').empty();

        // Clear hidden fields
        $('#st_school_id').val('');
        $('#st_dept_id').val('');
        $('#st_prog_id').val('');
        $('#st_aai_id').val('');

        // Clear success message
        $('#successMsg').text('');

        // Focus back to search input
        $('#studentEnrollment').focus();
    }











    $(document).on("click", function (event) {
        // Agar click #searchResults ya search field ke andar nahi hai to hide kar do
        if (!$(event.target).closest("#searchResults, #studentEnrollment").length) {
            $("#searchResults").hide();
        }
    });



    function getDepartments() {
        var school_id = $('#school_sel').val();
        $("#searchResults").hide();
        $.ajax({
            url: '/department/get',
            type: 'GET',
            data: { school_id: school_id },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    var html = '<option value="">All Dept</option>';
                    $.each(res.data, function (key, value) {
                        html += '<option value="' + value.dept_id + '">' + value.dept_name + '</option>';
                    });
                    $('select[name="department"]').html(html);

                }
            },
            error: function () {
                alert('Some error occurred!');
            }
        });
    }

    function getPrograms() {
        var department_id = $('#department').val();
        $("#searchResults").hide();
        $.ajax({
            url: '/program/get',
            type: 'GET',
            data: { dept_id: department_id },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    var html = '<option value="">All Programs</option>';
                    $.each(res.data, function (key, value) {
                        html += '<option value="' + value.prog_id + '">' + value.prog_name + '</option>';
                    });
                    $('select[name="program"]').html(html);

                }
            },
            error: function () {
                alert('Some error occurred while fetching programs!');
            }
        });
    }



    function handleEnterKey(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchStudent();
        }
    }




    function searchStudent() {
        var enrollmentNo = $('#studentEnrollment').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $('#studentEnrollment-error').text('');
        if (enrollmentNo.trim() === '') {
            $('#studentEnrollment-error').text('Please enter an enrollment number.');
            return;
        }

        $.ajax({
            url: '/finance/search-student-challans-available',
            type: 'POST',
            data: { studentEnrollment: enrollmentNo },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (allData) {
                makeProfileHtml(allData.studentRec);
                fetchWidgetData(enrollmentNo) // Generate Widgets dynamically
                $("#searchResults").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
                $('#studentEnrollment-error').text('An error occurred while searching. Please try again.');
            }
        });
    }

    function makeProfileHtml(data) {
        let dataDiv = document.getElementById('profileDiv');
        dataDiv.innerHTML = '';

        if (data && data.usernameError) {
            let errorHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            ${data.usernameError}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
            dataDiv.innerHTML = errorHtml;
        } else if (data && data.username) {
            $('#st_school_id').val(data.school_id);
            $('#st_dept_id').val(data.dept_id);
            $('#st_prog_id').val(data.prog_id);
            $('#st_aai_id').val(data.aai_id);

            let imageUrl = data.api_imagename
                ? `https://admission.bnu.edu.pk/images/${data.api_imagename}`
                : `https://cms.bnu.edu.pk/students_pics/${data.username.toUpperCase()}.jpg`;

            let profileHtml = `
        <div shadow-sm border-0">
            <div class="card-header custom-card-header d-flex align-items-center p-2">
                <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                <span class="h6 mb-0">Student Info</span>
            </div>
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center mb-2">
                        <img
                            src="${imageUrl}"
                            alt="Student Image"
                            class="img-fluid rounded-circle shadow-sm mb-2"
                            style="width: 80px; height: 80px;">
                        <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${data.spi_name || 'N/A'}</h6>
                        <div class="mt-2 text-center">
                            <div class="mb-1">
                                <span class="badge bg-info text-white p-1" style="font-size: 0.75rem;">Status: ${data.sai_student_status || 'N/A'}</span>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark p-1" style="font-size: 0.75rem;">Semester: ${data.semester_status || 'N/A'}</span>
                            </div>
                        </div>

                        ${data.sai_student_status === 'Left' && data.student_status_datetime ? `
                            <div class="text-muted small mt-1">
                                <strong>Left On:</strong> ${data.student_status_datetime}
                            </div>
                        ` : ''}

                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            ${generateInfoBox('fa-id-card', 'Registration No:', data.username || 'N/A')}
                            ${generateEmailInfoBox(data.spi_email, data.spi_personal_email)}
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
        }
    }

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

    function generateEmailInfoBox(spiEmail, spiPersonalEmail) {
        // Convert emails to lowercase
        const officialEmail = spiEmail ? spiEmail.toLowerCase() : null;
        const personalEmail = spiPersonalEmail ? spiPersonalEmail.toLowerCase() : null;

        // Determine which emails to show
        const hasSpiEmail = officialEmail && officialEmail !== 'N/A';
        const hasPersonalEmail = personalEmail && personalEmail !== 'N/A';

        let emailContent = '';

        if (hasSpiEmail && hasPersonalEmail) {
            // Both emails exist - show them stacked
            emailContent = `
            <span class="text-muted d-block" style="font-size: 0.85rem;">Email Addresses:</span>
            <div style="font-size: 0.85rem;">
                <div class="mb-1">
                    <strong style="color: #007bff;">Official:</strong> ${officialEmail}
                </div>
                <div>
                    <strong style="color: #28a745;">Personal:</strong> ${personalEmail}
                </div>
            </div>`;
        } else if (hasSpiEmail) {
            // Only official email exists
            emailContent = `
            <span class="text-muted d-block" style="font-size: 0.85rem;">Email:</span>
            <strong style="font-size: 0.9rem;">${officialEmail}</strong>`;
        } else if (hasPersonalEmail) {
            // Only personal email exists
            emailContent = `
            <span class="text-muted d-block" style="font-size: 0.85rem;">Email:</span>
            <strong style="font-size: 0.9rem;">${personalEmail}</strong>`;
        } else {
            // No emails exist
            emailContent = `
            <span class="text-muted d-block" style="font-size: 0.85rem;">Email:</span>
            <strong style="font-size: 0.9rem;">N/A</strong>`;
        }

        return `
    <div class="col-md-4 mb-3">
        <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
            <i class="fa fa-envelope mr-3 text-muted" style="font-size: 1.2rem;"></i>
            <div style="flex: 1;">
                ${emailContent}
            </div>
        </div>
    </div>`;
    }








    function generateWidgetsHtml(widgets) {
        const widgetsContainer = document.getElementById('widgetsContainer');
        widgetsContainer.innerHTML = ''; // Clear the container before adding new widgets

        widgets.forEach(widget => {
            // Check if the widget ID is either 'disciplineCommittee' or 'counselings' to disable
            const isDisabled = widget.id === 'disciplineCommittee' || widget.id === 'counselings';

            const widgetHtml = `
            <div class="col-xl-4 col-lg-6 col-md-12">
                <div class="card" 
                    onclick="${isDisabled ? '' : `openModal('${widget.id}')`}" 
                    style="cursor: ${isDisabled ? 'not-allowed' : 'pointer'}; transition: all 0.3s ease-in-out; ${isDisabled ? 'background-color: #e0e0e0; opacity: 0.6; color: #a0a0a0;' : ''}"
                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'; this.style.backgroundColor='#f8f9fa';"
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.1)'; this.style.backgroundColor='white';">
                    <div class="card-body">
                        <i class="${widget.icon} card-custom-icon icon-dropshadow ${widget.color} fs-60"></i>
                        <p class="mb-1">${widget.title}</p>
                        <h2 class="mb-1 font-weight-bold">${widget.value}</h2>
                        <span class="mb-1 text-muted">${widget.details}</span>
                    </div>
                </div>
            </div>`;

            widgetsContainer.innerHTML += widgetHtml;
        });
    }












    // function generateWidgetsHtml(widgets) {
    //     const widgetsContainer = document.getElementById('widgetsContainer');
    //     widgetsContainer.innerHTML = ''; // Clear the container before adding new widgets

    //     widgets.forEach(widget => {

    //         const widgetHtml = `
    //             <div class="col-xl-4 col-lg-6 col-md-12">
    //                 <div class="card" 
    //                     onclick="openModal('${widget.id}')"
    //                     style="cursor: pointer; transition: all 0.3s ease-in-out;"
    //                     onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'; this.style.backgroundColor='#f8f9fa';"
    //                     onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.1)'; this.style.backgroundColor='white';">
    //                     <div class="card-body">
    //                         <i class="${widget.icon} card-custom-icon icon-dropshadow ${widget.color} fs-60"></i>
    //                         <p class="mb-1">${widget.title}</p>
    //                         <h2 class="mb-1 font-weight-bold">${widget.value}</h2>
    //                         <span class="mb-1 text-muted">${widget.details}</span>
    //                     </div>
    //                 </div>
    //             </div>`;
    //         widgetsContainer.innerHTML += widgetHtml;
    //     });
    // }




    function fetchWidgetData(enrollment) {
        const authToken = "{{ auth()->user()->api_token }}";
        const modalBody = $('#modalBody');
        $.ajax({
            url: '/fetch-all-student-widget-link',
            type: 'GET',
            data: {
                enrollment: enrollment,
            },
            headers: {
                Authorization: `Bearer ${authToken}`
            },
            dataType: 'json',
            success: function (response) {

                generateWidgetsHtml(response)


            },
            error: function () {
                // Handle AJAX error
                modalBody.html(`
                <div class="text-danger text-center">
                    <p>Failed to load data.</p>
                </div>
            `);
            }
        });
    }



    function openModal(widgetId) {
        const enrollment = $('#studentEnrollment').val(); // Get the enrollment input value
        const modalBody = $('#modalBody');
        const modal = new bootstrap.Modal(document.getElementById('infoModal'));

        if (!enrollment) {
            modalBody.html('<p class="text-danger">Please enter an enrollment number.</p>');
            modal.show();
            return;
        }

        // Show loading spinner
        modalBody.html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"></span>
            </div>
            <p>Loading data...</p>
        </div>
    `);
        modal.show();

        // Call the AJAX function
        fetchSingleWidgetData(widgetId, enrollment, modalBody);
    }

    function fetchSingleWidgetData(widgetId, enrollment, modalBody) {
        const authToken = "{{ auth()->user()->api_token }}";

        $.ajax({
            url: '/api/fetch-single-student-widget-link',
            type: 'GET',
            data: { widgetId, enrollment },
            headers: { Authorization: `Bearer ${authToken}` },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    widgetHandlers(response.widgetId, response.data, modalBody);
                } else {
                    modalBody.html(`
                    <div class="text-danger text-center">
                        <p>${response.message || "No data found."}</p>
                    </div>
                `);
                }
            },
            error: function () {
                modalBody.html(`
                <div class="text-danger text-center">
                    <p>Failed to load data.</p>
                </div>
            `);
            }
        });
    }




    function widgetHandlers(widgetId, data, modalBody) {
        const widgetFunctions = {
            warningLetters: warningLetterWidget,
            feeChallans: renderFeeChallans,
            fines: renderFineChallans,
            courseEnrollment: renderEnrolledCourses,
            academicHistory: fetchAcademicHistory,
            scholarship: renderScholarship,
            attendance: renderAttendanceTable,
            gymSubscription: renderGymSubscriptions,
            libraryBooks: renderLibraryBooks
        };

        // Define headings for each widget
        const widgetHeadings = {
            warningLetters: "Warning Letters",
            feeChallans: "Fee Challans",
            fines: "Fines",
            courseEnrollment: "Course Enrollment",
            academicHistory: "Academic History",
            scholarship: "Scholarship",
            attendance: "Attendance",
            gymSubscription: "Gym",
            libraryBooks: "Library"
        };

        const headingText = widgetHeadings[widgetId] || "Details";
        $('#infoModalLabel').text(headingText);


        if (widgetFunctions[widgetId]) {
            widgetFunctions[widgetId](data, modalBody);
        } else {
            modalBody.html(`
            <div class="text-danger text-center">
                <p>Unsupported widget: ${widgetId}</p>
            </div>
        `);
        }
    }

    function warningLetterWidget(data, modalBody) {
        if (!data.length) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No warning letters found.</p>
            </div>
        `);
            return;
        }

        let lettersHtml = `
        
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                         <th scope="col">Sr. No</th> 
                        <th scope="col">Warning Type</th>
                        <th scope="col">Time</th>
                        <th scope="col">Warning Given By</th>
                        <th scope="col">Actions</th> <!-- Changed Actions column to View button -->
                    </tr>
                </thead>
                <tbody>
                    ${data.map((element, index) => {
            // Determine if the row should be marked as deleted
            const isDeleted = element.status === 'D';

            // Create the file link once and reuse it
            const fileLink = element.swl_filelink
                ? element.swl_filelink
                : "https://student.bnu.edu.pk/unsatisfactory-academic-performance";

            const fileName = element.warning_letter_name ? element.warning_letter_name : element.swl_filename;

            return `
                        <tr data-id="${element.id}" data-enrollment="${element.swl_enrollment_no}" style="${isDeleted ? 'text-decoration: line-through; color: gray; opacity: 0.7;' : ''}">
                            <td>${index + 1}</td>
                            <td>${fileName}</td>
                            <td>${new Date(element.entry_timestamp).toLocaleString()}</td>
                            <td>${element.full_name}</td>
                            <td>
                                <a href="${fileLink}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        `;
        }).join('')}
                </tbody>
            </table>
        </div>
    `;

        modalBody.html(lettersHtml);
    }




    function renderFeeChallans(data, modalBody) {
        // Ensure data is always an array
        const challans = Array.isArray(data) ? data : [];

        // Define staff URL once (Laravel config value)
        let staffUrl = "{{ config('app.staff_url') }}";
        const generateChallanUrl = `${staffUrl}/chalanfin.php?challan_id=`;

        if (challans.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No fee challans found.</p>
            </div>
        `);
            return;
        }

        let challansHtml = `
        <div style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                     <th scope="col">Sr. No</th> 
                        <th scope="col">ID</th>
                        <th scope="col">Session</th>
                        <th scope="col">Challan Type</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Print</th>
                    </tr>
                </thead>
                <tbody>
                    ${challans.map((challan, index) => {
            // Determine Status with Bootstrap Badges
            let statusBadge;
            switch (challan.sc_challan_status) {
                case 'PAID':
                    statusBadge = '<span class="badge badge-success">Paid</span>';
                    break;
                case 'UNPAID':
                    statusBadge = '<span class="badge badge-danger">Unpaid</span>';
                    break;
                case 'CANCELLED':
                    statusBadge = '<span class="badge badge-secondary">Cancelled</span>';
                    break;
                default:
                    statusBadge = '<span class="badge badge-dark">Unknown</span>';
            }

            return `
                        <tr>
                            <td>${index + 1}</td> 

                            <td>${challan.challan_no || 'N/A'}</td>
                            <td>${challan.sess_code || 'N/A'}</td>
                            <td>${challan.sc_challan_type || 'N/A'}</td>
                            <td>${challan.invoiceamount || 'N/A'}/-</td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="${generateChallanUrl}${challan.id}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fa fa-print"></i> Print
                                </a>
                            </td>
                        </tr>
                        `;
        }).join('')}
                </tbody>
            </table>
        </div>
    `;

        modalBody.html(challansHtml);
    }


    function renderFineChallans(data, modalBody) {
        // Ensure data is always an array
        const challans = data?.original?.student_challan || [];

        // Define staff URL once (Laravel config value)
        let staffUrl = "{{ config('app.staff_url') }}";
        const generateChallanUrl = `${staffUrl}/chalanfin.php?challan_id=`;

        if (challans.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No fine challans found.</p>
            </div>
        `);
            return;
        }

        let challansHtml = `
        <div style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Sr. No</th> <!-- Added Sr. No Column -->
                        <th scope="col">ID</th>
                        <th scope="col">Session</th>
                        <th scope="col">Category</th>
                        <th scope="col">Type</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Print</th>
                    </tr>
                </thead>
                <tbody>
                    ${challans.map((challan, index) => {
            // Determine Status with Bootstrap Badges
            let statusBadge;
            switch (challan.sc_challan_status) {
                case 'PAID':
                    statusBadge = '<span class="badge badge-success">Paid</span>';
                    break;
                case 'UNPAID':
                    statusBadge = '<span class="badge badge-danger">Unpaid</span>';
                    break;
                case 'CANCELLED':
                    statusBadge = '<span class="badge badge-secondary">Cancelled</span>';
                    break;
                default:
                    statusBadge = '<span class="badge badge-dark">Unknown</span>';
            }

            return `
                        <tr>
                            <td>${index + 1}</td> <!-- Sr. No Column -->
                            <td>${challan.id || 'N/A'}</td>
                            <td>${challan.sess_name || 'N/A'}</td>
                            <td>${challan.category_name || 'N/A'}</td>
                            <td>${challan.violation_type || 'N/A'}</td>
                            <td>${challan.sc_amount || '0.00'}/-</td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="${generateChallanUrl}${challan.id}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fa fa-print"></i> Print
                                </a>
                            </td>
                        </tr>
                        `;
        }).join('')}
                </tbody>
            </table>
        </div>
    `;

        modalBody.html(challansHtml);
    }



    function renderEnrolledCourses(data, modalBody) {


        const courses = data || [];
        const challans = data?.original?.student_challan || [];
        if (courses.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No enrolled courses found.</p>
            </div>
        `);
            return;
        }

        let coursesHtml = `
        <div style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Sr. No</th>
                        <th scope="col">Course Title</th>
                        <th scope="col">Faculty Name</th>
                        <th scope="col">Course Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${courses.map((course, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${course.course_title || 'N/A'}</td>
                            <td>${course.teachername || 'N/A'}</td>
                            <td>${getStatusBadge(course.sc_status)}</td> 
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

        modalBody.html(coursesHtml);
    }
    // function fetchAcademicHistory(data, modalBody) {
    // createHtml=`
    // <iframe class="embed-responsive-item" id="modalIframe1" ></iframe>
    // `;
    // modalBody.html(createHtml)
    // let appUrl = "{{ config('app.APP_URL') }}";
    // const documentLink = `${appUrl}/assets/cms/reports/student_transcripts_report_weak.php?ah_sai_enroll_no=${data}`;
    //     // Set the iframe source
    //     $('#modalIframe1').attr('src', documentLink);
    //     // Attach a 'load' event handler to hide the loading spinner once the content is loaded

    // }
    // function fetchAcademicHistory(data, modalBody) {
    //     if (!data) {
    //         modalBody.html(`<div class="text-danger text-center"><p>Invalid student enrollment number.</p></div>`);
    //         return;
    //     }

    //     // Use globally defined Laravel APP_URL

    //     let appUrl = "{{ config('app.app_url') }}";
    //     // Construct the document link
    //     const documentLink = `${appUrl}assets/cms/reports/academic_history_report.php?ah_sai_enroll_no=${(data)}`;

    //     console.log("Generated URL:", documentLink); // Debugging: See if URL is correct

    //     modalBody.html(`
    //         <div class="text-center" id="loadingSpinner">
    //             <div class="spinner-border text-primary"></div>
    //             <p>Loading academic history...</p>
    //         </div>
    //         <iframe class="embed-responsive-item" id="modalIframe1" style="width:100%; height:500px; border:none; display:none;"></iframe>
    //     `);

    //     let iframe = $('#modalIframe1');
    //     iframe.attr('src', documentLink);

    //     iframe.on('load', function () {
    //         $('#loadingSpinner').remove();
    //         $(this).show();
    //     });
    // }
    function fetchAcademicHistory(data) {
        if (!data) {
            alert('Invalid student enrollment number.');
            return;
        }

        let appUrl = "{{ config('app.app_url') }}";
        const documentLink = `${appUrl}/assets/cms/reports/academic_history_report.php?ah_sai_enroll_no=${data}`;
        // const documentLink = `http://devstaff.bnacademic_history_report.php?ah_sai_enroll_no=${data}`;

        // Instantly hide the modal (forcefully)
        $('#infoModal').modal('hide').removeClass('show').css('display', 'none');
        $('.modal-backdrop').remove(); // remove dark backdrop instantly
        $('body').removeClass('modal-open'); // remove scroll lock

        // Open new tab immediately
        window.open(documentLink, '_blank');
    }


    function renderScholarship(data, modalBody) {
        if (!data || data.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No scholarship details found.</p>
            </div>
        `);
            return;
        }


        let scholarshipHtml = `
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Session</th>
                    <th>Scholarship Name</th>
                    <th>Discount Percentage</th>
                    <th>Discount Amount</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                ${data.map((scholarship) => `
                    <tr>
                        <td>${scholarship.sess_code || 'N/A'}</td>
                        <td>${scholarship.scholarship_name || 'N/A'}</td>
                        <td>${scholarship.fsd_discount_percentage || 0}%</td>
                        <td>${scholarship.fsd_discounted_amount || '0'}/-</td>
                        <td>${scholarship.scholarship_type || 'N/A'}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;




        modalBody.html(scholarshipHtml);

    }
    function fetchCourseAttendance(enrollmentNo) {
        const authToken = "{{ auth()->user()->api_token }}"; // Laravel authentication token
        const modalBody = $('#modalBody');

        $.ajax({
            url: '/api/get-course-attendance',  // Ensure this route is defined in Laravel
            type: 'GET',
            data: { enrollmentNo: enrollmentNo },
            headers: { Authorization: `Bearer ${authToken}` },
            dataType: 'json',
            success: function (response) {
                renderAttendanceTable(response, modalBody);
            },
            error: function () {
                modalBody.html(`
                <div class="text-danger text-center">
                    <p>Failed to load attendance data.</p>
                </div>
            `);
            }
        });
    }






    function renderAttendanceTable(data, modalBody) {
        if (!data || data.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No attendance records found.</p>
            </div>
        `);
            return;
        }

        // Convert sess_id to a number for correct comparison
        let latestSessionId = Math.max(...data.map(record => Number(record.sess_id)));

        // Separate latest semester courses and old semester courses
        let latestSemesterCourses = data.filter(record => Number(record.sess_id) === latestSessionId);
        let oldSemesterCourses = data.filter(record => Number(record.sess_id) !== latestSessionId);

        // Function to generate table rows with updated button color logic
        function generateTableRows(records, isLatest) {
            return records.map((record, index) => {
                let attendance = record.attendance_percentage !== null ? parseFloat(record.attendance_percentage) : null;

                // If attendance is less than 75%, change the button background color to light red
                let buttonClass = attendance !== null && attendance < 75
                    ? 'btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center'
                    : 'btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center';

                let buttonStyle = attendance !== null && attendance < 75
                    ? 'background-color: #ffebee; color: #c62828; border-color: #c62828;' // Light red bg with red text
                    : '';

                return `
                <tr ${isLatest ? 'style="background-color: #e8f5e9;"' : ''}>
                    <td>${index + 1}</td>
                    <td>${record.sess_name || 'N/A'}</td>
                    <td>${record.course_title || 'N/A'}</td>
                    <td>
                        <a target="_blank" 
                           href="/assets/cms/reports/attendance_details_studentwise_report.php?adcr_co_id=${record.sc_co_id}&adcr_enrollment_no=${record.sc_sai_enrollment_no}" 
                           class="${buttonClass}" style="width: 130px; font-weight: bold; border-radius: 5px; ${buttonStyle}">
                           <i class="fa fa-eye mr-1"></i> 
                            ${attendance !== null ? attendance.toFixed(2) + "%" : "N/A"}
                        </a>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // Table structure with updated styles
        let attendanceHtml = `
        <div class="card shadow-sm p-3" style="border-radius: 10px;">
            <div class="table-responsive">
                <table class="table table-bordered" style="border-radius: 8px; overflow: hidden;">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Sr#</th>
                            <th scope="col">Session</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Latest Semester (Full Background Green) -->
                        ${latestSemesterCourses.length > 0 ? `<tr style="background-color: #e8f5e9; color: #2e7d32; font-weight: bold;"><td colspan="4">Latest Semester Courses</td></tr>` : ''}
                        ${generateTableRows(latestSemesterCourses, true)}

                        <!-- Old Semester (Only Title Background Light Beige) -->
                        ${oldSemesterCourses.length > 0 ? `<tr style=" font-weight: bold;"><td colspan="4">Previous Semester Courses</td></tr>` : ''}
                        ${generateTableRows(oldSemesterCourses, false)}
                    </tbody>
                </table>
            </div>
        </div>
    `;

        modalBody.html(attendanceHtml);
    }











    function renderGymSubscriptions(data, modalBody) {
        if (!data || data.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No Gym subscriptions found.</p>
            </div>
        `);
            return;
        }

        let gymHtml = `
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Sr#</th>
                    <th scope="col">Session</th>
                    <th scope="col">Subscription Date</th>
                    <th scope="col">Expiration Date</th>
                    <th scope="col">Plan Tenure</th>
                    <th scope="col">Challan Status</th>
                </tr>
            </thead>
            <tbody>
                ${data.map((record, index) => `
                    <tr>
                        <td>${index + 1}</td>
                      <td>${record.sess_code || 'N/A'}</td>
                    <td>${record.subscription_date || 'N/A'}</td>
                    <td>${record.expiration_date || 'N/A'}</td>
                    <td>${record.sp_plan_tenure || 'N/A'}</td>
                    <td>
                        <span class="badge ${record.challan_status === 'PAID' ? 'badge-success' :
                (record.challan_status === 'UNPAID' ? 'badge-danger' :
                    (record.challan_status === 'CANCELLED' ? 'badge-secondary' : 'badge-light'))
            }">
                            ${record.challan_status || 'N/A'}
                        </span>
                    </td>

                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

        modalBody.html(gymHtml);
    }







    function renderLibraryBooks(data, modalBody) {
        if (!data || data.length === 0) {
            modalBody.html(`
            <div class="text-warning text-center">
                <p>No Library books found.</p>
            </div>
        `);
            return;
        }

        let libraryHtml = `
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Sr#</th>
                    <th scope="col">Accession No.</th>
                    <th scope="col">Book Title</th>
                    <th scope="col">Issue Date</th>
                    <th scope="col">Days</th>
                    <th scope="col">Return Date</th>
                    <th scope="col">Late Days</th>
                    <th scope="col">Total Fine</th>
                    <th scope="col">Fine Status</th>
                    <th scope="col">Book Status</th>
                
                </tr>
            </thead>
            <tbody>
                ${data.map((record, index) => {
            let rowClass = '';  // Default class for normal rows

            // Apply 'table-danger' for overdue books (yellow background and red text)
            if (record.overdue_status == 1) {
                rowClass = 'table-danger'; // Apply Bootstrap danger class
            }

            // Handle Fine Logic
            let hasFine = (record.lc_fine_total && record.lc_fine_total > 0);

            let lateDaysDisplay = hasFine ? (record.lc_total_late_days || 'N/A') : 'N/A';
            let totalFineDisplay = hasFine ? record.lc_fine_total : 'N/A';
            let fineStatusDisplay = hasFine && record.lc_fine_status
                ? `<span class="badge ${record.lc_fine_status === 'Paid' ? 'badge-success' : 'badge-danger'}">${record.lc_fine_status}</span>`
                : 'N/A';


            return `
                        <tr class="${rowClass}">
                            <td>${index + 1}</td>
                            <td>${record.lc_accession_no || 'N/A'}</td>
                            <td>${record.library_title || 'N/A'}</td>
                            <td>${record.lc_issue_date || 'N/A'}</td>
                            <td>${record.lc_issue_no_of_days || 'N/A'}</td>
                            <td>${record.lc_return_date || 'N/A'}</td>
                            
                            <td>${lateDaysDisplay}</td>
                            <td>${totalFineDisplay}</td>
                            <td>${fineStatusDisplay}</td>

                           <td>${record.lc_book_status || 'N/A'}</td> 
                        </tr>
                    `;
        }).join('')}
            </tbody>
        </table>
    `;

        modalBody.html(libraryHtml);
    }




    function getStatusBadge(status) {
        let badgeClass = 'badge-light';  // Default badge class
        let badgeText = status;          // Default badge text as the status itself

        if (status.includes("Convocated")) {
            badgeClass = 'badge-success';
            badgeText = 'Convocated';
        } else {
            switch (status) {
                case 'A':
                case 'Active':
                    badgeClass = 'badge-success';
                    badgeText = 'Active';
                    break;

                case 'Expected-Graduate':
                    badgeClass = 'badge-info';
                    badgeText = 'Expected Graduate';
                    break;

                case 'Course-Completed':
                    badgeClass = 'badge-success';
                    badgeText = 'Course Completed';
                    break;

                case 'Defer':
                    badgeClass = 'badge-warning';
                    badgeText = 'Defer';
                    break;

                case 'Struck off':
                case 'Stuck-Off':
                    badgeClass = 'badge-danger';
                    badgeText = 'Struck Off';
                    break;

                case 'Not Joined':
                    badgeClass = 'badge-danger';
                    badgeText = 'Not Joined';
                    break;

                case 'Certifcate Completed':
                    badgeClass = 'badge-info';
                    badgeText = 'Certificate Completed';
                    break;

                case 'Left':
                    badgeClass = 'badge-dark';
                    badgeText = 'Left';
                    break;

                case 'DisApprove':
                    badgeClass = 'badge-danger';
                    badgeText = 'Disapproved';
                    break;

                case 'Block':
                    badgeClass = 'badge-secondary';
                    badgeText = 'Blocked';
                    break;

                case 'Withdrawal':
                    badgeClass = 'badge-warning';
                    badgeText = 'Withdrawn';
                    break;

                case 'Approve':
                    badgeClass = 'badge-primary';
                    badgeText = 'Approved';
                    break;
                case 'Expired':
                    badgeClass = 'badge-danger';
                    badgeText = 'Expired';
                    break;

                case 'Pending':
                    badgeClass = 'badge-warning';
                    badgeText = 'Pending';
                    break;
                default:
                    badgeClass = 'badge-light';
                    badgeText = 'Unknown';
                    break;
            }
        }

        // Return the constructed badge HTML
        return `<span   style="width: 100px;"    class="badge ${badgeClass}">${badgeText}</span>`;
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Function to get URL parameters
        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
        // Auto-fill input if parameter exists
        const enrollmentInput = document.getElementById("studentEnrollment");
        const searchButton = document.getElementById("searchButton");
        const enrollmentNo = getURLParameter("sai_enrollment_no");

        if (enrollmentNo && enrollmentNo.trim() !== "") {
            enrollmentInput.value = enrollmentNo; // Auto-fill input field
            searchStudent(); // Auto-trigger search if a value exists
        }
        // Attach Enter key listener
        enrollmentInput.addEventListener("keydown", handleEnterKey);
    });
    // Function to open the student profile in a new tab
    function openProfileInNewTab(url) {
        window.open(url, "_blank").focus();
    }


    function liveSearch(query) {
        if (query.length < 2) {
            $("#searchResults").hide();
            return;
        }

        let school_id = $("#school_sel").val();
        let dept_id = $("#department").val();
        let prog_id = $("#Program").val();

        $.ajax({
            url: "/live-search-student",
            type: "GET",
            data: {
                query: query,
                school_id: school_id,
                dept_id: dept_id,
                prog_id: prog_id
            },
            success: function (data) {
                let dropdown = $("#searchResults");
                dropdown.empty().show();

                if (data.length === 0) {
                    dropdown.append('<div class="dropdown-item text-muted">No results found</div>');
                } else {
                    data.forEach(function (item) {
                        let imageUrl = `https://cms.bnu.edu.pk/students_pics/${item.enrollment_no.toUpperCase()}.jpg`;
                        let defaultImage = `{{ asset('images/noimage.png') }}`; // Laravel asset helper

                        dropdown.append(
                            `<div class="dropdown-item d-flex align-items-center" onclick="selectStudent('${item.enrollment_no}')">
                            <img src="${imageUrl}" onerror="this.src='${defaultImage}';" 
                                class="rounded-circle border" width="40" height="40" style="margin-right: 12px;">
                            <div>
                                <strong class="text-dark">${item.student_name}</strong> <br>
                                <small class="text-muted">
                                    <span class="badge bg-info text-white">${item.enrollment_no}</span> 
                                    | ${item.spi_nic ? item.spi_nic : '<span class="text-danger">N/A</span>'}
                                </small>
                            </div>
                        </div>`
                        );

                    });
                }
            },
            error: function () {
                console.error("Search error.");
            },
        });
    }

    function selectStudent(enrollmentNo) {
        $("#studentEnrollment").val(enrollmentNo);
        $("#searchResults").hide();
        searchStudent(); // Call the search function after selection
    }



</script>
@stop