<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="studentModalLabel">Enrolled Courses</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div id="studentModalBody">
                    <!-- Course data and buttons will be appended here dynamically -->
                </div>
                <div id="NotificationDiv"></div> <!-- Notifications will appear here -->
            </div>
        </div>
    </div>
</div>



<script>

function handleStudentEnrollment(enrollment_no) {
    // Show a loading spinner in the modal body
    $('#studentModalBody').html(`
        <div class="text-center">
            <span class="spinner-border text-primary" role="status"></span>
            <p>Please wait, loading courses...</p>
        </div>
    `);

    // Show the modal while fetching data
    $('#studentModal').modal('show');

    // AJAX request to get enrolled courses
    $.ajax({
        url: '/reports/get-current-enrolled-student-courses',
        method: 'GET',
        data: { enrollment_no: enrollment_no },
        success: function (response) {
            if (response.length > 0) {
                // Clear the loading spinner
                $('#studentModalBody').empty();

                // Build the table of courses
                let table = `<table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>SR No</th>
                                        <th>Course Title</th>
                                        <th>Faculty Name</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody style="max-height: 500px; overflow-y: auto;"  >`;


                    response.forEach((course, index) => {
                            table += `
                                <tr class="course-row"
                                    data-co-id="${course.co_id}"
                                    data-enrollment-no="${enrollment_no}"

                                ">
                                    <td>${index + 1}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="toggle-course text-primary font-weight-bold">
                                            ${course.course_title}
                                        </a>
                                    </td>
                                    <td>${course.faculty_name}</td>
                                    <td>
                                        <input type="checkbox"
                                            value="${course.course_id}"
                                            data-course-id="${course.course_id}"
                                            class="courseCheckbox">
                                    </td>
                                </tr>

                                <tr class="course-detail-row d-none">
                                    <td colspan="4">
                                        <div class="card shadow-sm">
                                            <div class="card-body text-center">
                                                <span class="spinner-border text-primary"></span>
                                                <p class="mt-2 mb-0">Loading marks sheet...</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });



                table += `</tbody></table>`;

                // Add form controls to the modal body
                const formControls = `
                    <div class="form-group mt-3">
                        <label for="remarks">Remarks:</label>
                        <textarea id="remarks" class="form-control" rows="3" placeholder="Enter remarks here..." required></textarea>
                    </div>
                    <div class="text-right mt-3 mb-1">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitEnrollmentRequest('${enrollment_no}')">
                            <i class="fa fa-paper-plane"></i> Request for Mentor
                        </button>
                    </div>
                `;

                $('#studentModalBody').append(table + formControls);
            } else {
                // Handle no courses found
                showNotification('No enrolled courses found.', 'info', 'NotificationDiv');
            }
        },
        error: function () {
            showNotification('An error occurred while fetching course attendance.', 'danger', 'NotificationDiv');
        }
    });
}

$(document).on('click', '.toggle-course', function () {

    const parentRow = $(this).closest('tr');
    const detailRow = parentRow.next('.course-detail-row');
    const coId = parentRow.data('co-id');
    const enrollmentNo = parentRow.data('enrollment-no');

    // Close all other expanded rows
    $('.course-detail-row').not(detailRow).addClass('d-none');
        $('.course-detail-row.d-none .card-body').html(`
            <div class="text-center">
                <span class="spinner-border text-primary"></span>
                <p class="mt-2 mb-0">Loading marks sheet...</p>
            </div>
        `);


    // Toggle current row
    if (!detailRow.hasClass('d-none')) {
        detailRow.addClass('d-none');
        return;
    }

    detailRow.removeClass('d-none');

    // API call to fetch marks sheet
    $.ajax({
        url: '/reports/get-course-marks-sheet',
        method: 'GET',
        data: { co_id: coId,

            enrollment_no: enrollmentNo
        },
            success: function (response) {

                let html = `

                        <div class="card-header bg-light font-weight-bold">
                            Marks Sheet
                        </div>
                        <div class="card-body p-2">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Assessment</th>
                                        <th>Obtained</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                const marks = response.marks_details || [];

                if (marks.length > 0) {
                    marks.forEach(row => {
                        html += `
                            <tr>
                                <td>${row.assessment_name ?? row.assessment_id}</td>
                                <td>${row.obtained_marks ?? '-'}</td>
                                <td>${row.total_marks ?? '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                No marks available
                            </td>
                        </tr>
                    `;
                }

                html += `
                                </tbody>
                            </table>
                        </div>

                `;

                detailRow.find('.card-body').html(html);
            },

        error: function () {
            detailRow.find('.card-body').html(`
                <div class="alert alert-danger mb-0">
                    Failed to load marks sheet.
                </div>
            `);
        }
    });
});



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



function submitEnrollmentRequest(enrollment_no) {
    const selectedCourses = [];
    const remarks = $('#remarks').val();
    const requestButton = $('.btn-primary[onclick="submitEnrollmentRequest(\'' + enrollment_no + '\')"]');

    // Collect selected course IDs
    $('.courseCheckbox:checked').each(function () {
        selectedCourses.push({
            co_id: $(this).val(), // `co_id` from value attribute
            course_id: $(this).data('course-id') // `course_id` from data attribute
        });
    });

    // Validate input
    if (selectedCourses.length === 0) {
        showNotification('Please select at least one course.', 'warning', 'NotificationDiv');
        return;
    }

    if (!remarks.trim()) {
        showNotification('Remarks are required.', 'warning', 'NotificationDiv');
        return;
    }

    // Disable the button and show spinner
    requestButton.prop('disabled', true).html(`
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Requesting...
    `);

    // AJAX request to submit selected courses and remarks
    $.ajax({
        url: '/peer-mentorship-submit-courses', // Endpoint for submission
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for Laravel
            enrollment_no: enrollment_no,
            course_ids: selectedCourses,
            remarks: remarks,
        },
        success: function (response) {
            // If submission is successful
            showNotification(response.message || 'Request submitted successfully.', 'success', 'NotificationDiv');
            setTimeout(function() {
            $('#studentModal').modal('hide');
            $('#NotificationDiv').html('');
             }, 3000); // 1000 milliseconds = 1 second

        },
        error: function (xhr, status, error) {
            // If there is an error in submission
            console.error('Submission failed:', error);
            showNotification('An error occurred while submitting the request.', 'danger', 'NotificationDiv');
        },
        complete: function () {
            // Re-enable the button and restore original text
            requestButton.prop('disabled', false).html(`
                <i class="fa fa-paper-plane"></i> Request for Mentor
            `);
        },
    });
}







</script>
