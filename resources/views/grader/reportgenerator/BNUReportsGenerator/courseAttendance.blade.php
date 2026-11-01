


<!-- Modal HTML Structure -->
<div class="modal fade" id="courseAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="courseAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Updated with modal-lg for a larger modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseAttendanceModalLabel">Student Left Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Canvas for line chart -->
                <canvas id="courseAttendanceLineChart" width="400" height="200"></canvas> <!-- Updated the canvas id -->

                <div id='courseAttendanceMonthlyDiv'></div>
                <div id='courseAttendanceMonthlyDivData'></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>



$(document).on('click', '#exportExcelBtn', function () {
    // Get table element
    var table = document.querySelector("#courseDetailsModal table");

    // Convert table to SheetJS workbook
    var workbook = XLSX.utils.table_to_book(table, {sheet: "Absent Students"});
    
    // Generate Excel file and download
    XLSX.writeFile(workbook, "Program-Wise-Absent-Students.xlsx");
});
function getCourseAttendance(formData) {



    if (!$('#report_type').val()) {
            showNotification('Please select a Report Type.', 'warning', 'NotificationDiv');
            return false;  // Stop further execution
        }
        let academicSession = $('#academic_session').val();
        let roadmapSession = $('#roadmap_session').val();
        if (!academicSession && !roadmapSession) {
            showNotification('Please select at least one session type (Academic or Roadmap).', 'warning', 'NotificationDiv');
            return false;  // Stop further execution
        }


    showLoadingSpinner('#tableDataDiv');

    // Clear the content of tableDataDiv and reset any existing charts
    $('#tableDataDiv').html('');
    $('#fetchSummaryDataDiv').html('');
    resetMyPieChartParentDiv();

    $.ajax({
        url: '/course-attendance-report', // Correct endpoint
        type: 'GET',
        data: {
            formData
        },
        success: function(response) {

            const allLeftStudentsZero = response.every(item => item.left_students === "0");
            if (allLeftStudentsZero) {
            showNotification('Data Not Found!', 'warning', 'NotificationDiv');
            return false;
        }
            $('html, body').animate({
                scrollTop: $('#tableDataDiv').offset().top - 80
            }, 1000);

            // Prepare data for the charts and table
            let schoolNames = [];
            let leftStudents = [];

            var sortedData = response.sort((a, b) => Number(a.school_id) - Number(b.school_id));
            sortedData.forEach(item => {
                schoolNames.push(item.school_code);
                leftStudents.push(item.left_students);
            });

            let chartContainerHtml = `
                <div class="d-flex align-items-center" style="height: 100%;">
                    <div class="row w-100">
                        <div class="col-md-6 d-flex justify-content-center" style="height: 450px; width: 450px;">
                <canvas id="attendancePieChart" style="width: 100%; height: 100%;"></canvas>
            </div>
                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                <canvas id="attendanceBarChart"></canvas>
            </div>

                    </div>
                </div>
            `;




            $('#tableDataDiv').append(chartContainerHtml);



            // Render both charts
            renderBarChart(schoolNames, leftStudents);
            renderPieChart(schoolNames, leftStudents);

            // Generate the table HTML
            let tableHtml = `
                <table id="studentsTable" class="table table-striped table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>School Name</th>
                            <th>Total Absents (Equal or More than Allowed Absents)</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            const totalLeftStudents = sortedData.reduce((sum, item) => sum + parseInt(item.left_students || 0), 0);
            sortedData.forEach(item => {
            const isDisabled = item.left_students == 0 ? 'disabled' : '';
            const buttonClass = item.left_students == 0 ? 'btn-default' : 'btn-outline-warning';

            tableHtml += `
                <tr>
                    <td>${escapeHtml(item.school_name)}</td>
                    <td>${item.left_students}</td>
                    <td>
                        <button class="btn ${buttonClass}" ${isDisabled} ${isDisabled ? 'aria-disabled="true"' : ''} onclick="viewDetails('${escapeHtml(item.school_id)}')">
                            <i class="fa fa-eye mr-2"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

// Add the Total row at the end
tableHtml += `
    <tr>
        <td><strong>Total</strong></td>
        <td><strong>${totalLeftStudents}</strong></td>
        <td></td>
    </tr>
`;





            tableHtml += `</tbody></table>`;
            $('#tableDataDiv').append(tableHtml);

            // Initialize DataTable
            // $('#studentsTable').DataTable({
            //     "order": [],
            //     "pageLength": 20

            // });
        },
        error: function(xhr, status, error) {
            // Handle error (e.g., show notification)
            showNotification('An error occurred while fetching course attendance.', 'danger', 'NotificationDiv');
        }
    });
}

// Function to render the bar chart
function renderBarChart(schoolNames, leftStudents) {
    var ctx = document.getElementById('attendanceBarChart').getContext('2d');


    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: schoolNames,
            datasets: [{
                label: 'Absent Students',
                data: leftStudents,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Absent Students'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'School Name'
                    }
                }
            }
        }
    });
}

// Function to render the pie chart
function renderPieChart(schoolNames, leftStudents) {
    var ctx = document.getElementById('attendancePieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: schoolNames,
            datasets: [{
                label: 'Students Left',
                data: leftStudents,
                backgroundColor: backgroundColors.slice(0, schoolNames.length), // Use predefined colors
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 20,
                                padding: 20,
                                font: {
                                    size: 14,
                                    family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                                },
                            },
                        },
                        tooltip: {
                            // callbacks: {
                            //     label: function(tooltipItem) {
                            //         const label = tooltipItem.label || '';
                            //         const value = tooltipItem.raw;
                            //          //return `${label}: ${value}`;
                            //         return `${label}`;
                            //     }
                            // }
                        },
                        title: {

                            display: true,
                            // text: `School-Wise Course Withdrawal Status`,
                            text: `School-Wise Course Absents Status`,
                            font: {
                                family: 'Arial',
                                size: 18,
                                // weight: 'bold'
                            }
                        }
                    }
        },
        plugins: [ChartDataLabels],
    });
}


function viewDetails(school_id) {
    let roadmapSession = $('#roadmap_session').val();
    let academicSession = $('#academic_session').val();

    $.ajax({
        url: '/reports-generator-school-students',
        type: 'GET',
        data: {
            school_id: school_id,
            roadmapSession: roadmapSession,
            academicSession: academicSession
        },
        success: function(response) {
            console.log(response);
            $('#courseDetailsModal').remove();

            if (response.length > 0) {
                // Process the data for the pie chart
                const programData = {};
                response.forEach(item => {
                    programData[item.prog_name] = (programData[item.prog_name] || 0) + 1;
                });

                const progNames = Object.keys(programData);
                const totalStudents = Object.values(programData);
                // <h5 class="modal-title" id="courseDetailsModalLabel">Program-wise Withdrawn Students</h5>
                // Create the modal with a compact pie chart and table
                 
                const modalHtml = `
                    <div class="modal fade" id="courseDetailsModal" tabindex="-1" role="dialog" aria-labelledby="courseDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="courseDetailsModalLabel">Program-wise Absent Students</h5>                                    
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <!-- Pie Chart -->
                                    <div class="row" style="height: 250px;">
                                        <div class="col-md-10 d-flex justify-content-center align-items-center">
                                            <canvas id="programPieChart" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-center align-items-center">
                                            <!-- Placeholder for additional content -->
                                        </div>
                                    </div>
                                    <div style="float:right">
                                        <button type="button" class="btn btn-success" id="exportExcelBtn">Export to Excel</button>
                                    </div>
                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="margin: 0; padding: 0; font-size: 14px;">
                                            <thead>
                                                <tr>
                                                    <th>Sr</th>
                                                    <th>Enrollment No</th>
                                                    <th>Program Name</th>    
                                                    <th>Mobile #</th>                                           
                                                    <th>Absent Courses</th>
                                                    <th>Student Status</th>
                                                    <th>Profile</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${response.map((student, index) => `
                                                    <tr>
                                                        <td>${index + 1}</td>
                                                        <td>
                                                            <a href="#" class="enrollmentNo" style="text-decoration: underline; color: #008080;" data-enrollment-no="${student.sai_enrollment_no}">
                                                                ${student.sai_enrollment_no}
                                                            </a>
                                                        </td>
                                                        <td>${student.prog_name}</td>
                                                        <td>${student.spi_mobile}</td>
                                                        <td>${student.total_withdrawn}/${student.total_courses}</td>
                                                        <td>${getStatusBadge(student.sai_student_status)}</td>
                                                    <td>
                                                            <?php 
                                                                if (auth()->user()->group_id == 644 || auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique'): 
                                                            ?>
                                                                <button class="btn btn-outline-info" 
                                                                    onclick="openProfileInNewTab('/student-profile-rec', '${student.sai_enrollment_no}')">
                                                                    <i class="fa fa-eye"></i> View
                                                                </button>
                                                            <?php 
                                                                else:
                                                            ?>
                                                                <!-- You can add an alternative button or message here for the 'else' case -->
                                                                <button class="btn btn-outline-secondary" disabled>
                                                                    <i class="fa fa-eye-slash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>

                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Append the modal HTML to the body
                $('body').append(modalHtml);

                // Open the modal
                $('#courseDetailsModal').modal('show');

                // Remove the modal from DOM when it's closed
                $('#courseDetailsModal').on('hidden.bs.modal', function () {
                    $(this).remove();
                });

                // Render the pie chart inside the modal
                const ctx = document.getElementById('programPieChart').getContext('2d');
                const backgroundColors = [
                    '#FF9F9A', '#B39DDB', '#FFD166', '#82CFFD', '#FFE082',
                    '#A5D6A7', '#FFAB91', '#DCE775', '#FFCC80', '#CE93D8',
                    '#FF8A80', '#F4A261', '#76C7C0', '#A1887F', '#F48FB1'
                ];
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: progNames,
                        datasets: [{
                            data: totalStudents,
                            backgroundColor: backgroundColors.slice(0, progNames.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 25,
                                    padding: 10,
                                    font: {
                                        size: 11,
                                        family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                                    },
                                    textAlign: 'start',
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw; // Show only the count value
                                    }
                                }
                            },
                            datalabels: {
                                color: '#000',
                                font: {
                                    size: 10,
                                    family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                                },
                                formatter: function(value, context) {
                                    return value; // Display only the count in each slice
                                }
                            }
                        },
                        layout: {
                            padding: { right: 50 }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                // Handle enrollment no clicks to load course details
                $('.enrollmentNo').on('click', function(event) {
                    event.preventDefault();
                    const enrollmentNo = $(this).data('enrollment-no');

                    // Check if course details are already loaded
                    const row = $(this).closest('tr');
                    if (row.next().hasClass('course-details-row')) {
                        row.next().remove(); // Remove if already loaded
                    } else {
                        loadCourseDetails(enrollmentNo, row);
                    }
                });
            } else {
                // If no data is found, show an alert
                const alertHtml = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        No students found for the selected session.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('#tabularDataDiv').prepend(alertHtml);
                $('html, body').animate({ scrollTop: $('#tabularDataDiv').offset().top }, 1000);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
}




// Function to load course details dynamically when an enrollment number is clicked
function loadCourseDetails(enrollmentNo, row) {
    // let sessionId = $('#roadmap_session').val() ?? $('#academic_session').val();

    let sessionId = $('#academic_session').val();


    // Add a loading indicator inside the table row
    var loadingRow = `
        <tr class="course-details-row">
            <td colspan="5">Loading course details...</td>
        </tr>
    `;
    row.after(loadingRow); // Insert loading row below the clicked row

    $.ajax({
        url: '/reports-generator-enrolled-courses', // Replace with your URL endpoint for course details
        type: 'GET',
        data: {
            enrollment_no: enrollmentNo,
            session_id: sessionId // Include session ID here
        },
        success: function(response) {
            // Construct the course details table row
            const courseDetailsHtml = `
                <tr class="course-details-row">
                    <td colspan="8">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Course Title</th>                                    
                                    <th>Total Classes</th>
                                    <th>Total Presents</th>
                                    <th>Total Absents</th>
                                    <th>Allowed Absents</th>
                                    <th>Faculty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                ${response.map((course, index) => {
                                    // Calculate absents and highlight condition
                                    const totalAbsents = (course.total_classes ?? 0) - (course.total_presents ?? 0);
                                    const allowedAbsents = course.allowed_absents ?? 0;
                                    const isOverHalfAbsent = totalAbsents >= (allowedAbsents / 2);
                                    // Determine row color class
                                    const rowClass = isOverHalfAbsent 
                                        ? 'table-danger' // or 'table-danger' for red
                                        : '';
                                    return `
                                        <tr class="${rowClass}">
                                            <td>${index + 1}</td>
                                            <td>${course.course_title}</td>
                                             <td>${course.total_classes}</td>
                                              <td>${course.total_presents}</td>
                                               <td>${course.total_absents}</td>
                                                <td>${course.allowed_absents}</td>
                                            <td>${course.teachername ?? 'N/A'}</td>
                                            <td>${getStatusBadge(course.sc_status)}</td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </td>
                </tr>
            `;

            // Replace the loading row with the actual course details row
            row.next().replaceWith(courseDetailsHtml);
        },
        error: function(xhr, status, error) {
            console.error("Error loading course details: ", status, error);
            row.next().html('<td colspan="5">Error loading course details.</td>');
        }
    });
}






function getStatusBadge(status) {
    let badgeClass = 'badge-light';  // Default badge class
    let badgeText = status;          // Default badge text as the status itself

    if (status.includes("Convocated")) {
        badgeClass = 'badge-success';
        badgeText = 'Convocated';
    } else {
        switch(status) {
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

            default:
                badgeClass = 'badge-light';
                badgeText = 'Unknown';
                break;
        }
    }

    // Return the constructed badge HTML
    return `<span   style="width: 100px;"    class="badge ${badgeClass}">${badgeText}</span>`;
}



function escapeHtml(str) {
    // A simple escape function to avoid XSS vulnerabilities
    return str.replace(/[&<>"']/g, (match) => {
        const escapeMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        };
        return escapeMap[match];
    });
}


function openProfileInNewTab(baseUrl, enrollmentNo) {
    if (enrollmentNo && enrollmentNo.trim() !== "") {
        let fullUrl = `${baseUrl}?sai_enrollment_no=${encodeURIComponent(enrollmentNo)}`;
        window.open(fullUrl, "_blank").focus();
    } else {
        alert("Enrollment number is missing!");
    }
}

</script>
