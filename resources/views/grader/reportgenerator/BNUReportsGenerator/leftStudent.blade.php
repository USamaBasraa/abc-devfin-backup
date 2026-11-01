
<style>

.active-row {
    background-color: #81d4fa     !important; /* Light green for active row */
    font-weight: bold; /* Optional: make the text bold */
}

.expanded-content {
    display: none;
}

.expanded-content.show {
    display: table-row;  /* Show the expanded content */
}


</style>

<!-- Modal HTML Structure -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 80%;"> <!-- Adjusted width to 90% -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Student Left Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Canvas for line chart -->
                <canvas id="lineChart" width="400" height="200"></canvas> <!-- Updated the canvas id -->

                <div id='leftStudentMonthlyDiv'></div>
                <div id='leftStudentMonthlyDivData'></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>











<!-- Modal for Students Analysis -->
<div class="modal fade" id="transferredToCMSModal" tabindex="-1" role="dialog" aria-labelledby="transferredToCMSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferredToCMSModalLabel">Analysis of Student Enrollment Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Canvas for Chart -->
                <canvas id="transferredToCMSChart" width="400" height="200"></canvas>
                <!-- Table Content -->
                <div id="transferredToCMSContent" style="margin-top: 20px;">
                    <!-- Dynamic content will be loaded here -->
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>





<script>
function fetchStudentDetails(studentId, column, value) {
    const sessionId = document.getElementById('roadmap_session').value;
    const selectedDate = document.getElementById('selected_date').value;
    // const departmentId = document.getElementById('department').value;
    // const schoolId = document.getElementById('school').value;
    const schoolId = 123;


// Prepare the data payload
    const requestData = {
        studentId: studentId,
        sessionId: sessionId,
        selectedDate: selectedDate,
        schoolId: schoolId,
    };

    // Add dynamic column and value
    if (column && value !== undefined) {
        requestData[column] = value;
    }



    $.ajax({
        url: '/left-student-admission-data',
        method: 'GET',
       data: requestData,
        success: function(response) {
            if (response.status) {
                const labels = response.studentData.map(school => school.school_code); // X-axis labels
                const dataPoints = response.studentData.map(school => parseInt(school.left_students)); // Y-axis value
                const dataPointsDefer = response.studentData.map(school => parseInt(school.defer_students));
                const dataPointsstuck_Off = response.studentData.map(school => parseInt(school.stuck_off));
                const dataPointsnotJoined = response.studentData.map(school => parseInt(school.not_joined));
                // Show the modal
                $('#myModal').modal('show');
                // Render the line chart
                renderLineChart(labels, dataPoints,dataPointsDefer,dataPointsstuck_Off,dataPointsnotJoined);
                renderStaticStudentTable(response.studentData,response.studentLeftData);
            } else {
                alert('Failed to retrieve student data.');
            }
        },
        error: function() {
            alert('Failed to fetch data. Please try again.');
        }
    });
}

// Function to render line chart using Chart.js
function renderLineChart(labels, dataPoints,dataPointsDefer,dataPointsstuck_Off,dataPointsnotJoined) {
    const ctx = document.getElementById('lineChart').getContext('2d');

    // Destroy the existing chart if it exists
    if (window.myLineChart) {
        window.myLineChart.destroy();
    }

    // Create a new line chart
    window.myLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, // X-axis: school codes
            datasets: [{
                label: 'Left Students',
                data: dataPoints, // Y-axis: left students
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Line color fill
                borderColor: 'rgba(75, 192, 192, 1)', // Line color
                borderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            },
            {
                label: 'Deferred Students',
                data: dataPointsDefer, // Y-axis data for deferred students
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // Line color fill for Deferred Students
                borderColor: 'rgba(255, 99, 132, 1)', // Line color for Deferred Students
                borderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }, {
                label: 'Struck off Students',
                data: dataPointsstuck_Off, // Y-axis data for deferred students
                backgroundColor: 'rgba(153, 255, 153, 0.2)', // Light green fill
                borderColor: 'rgba(153, 255, 153, 1)', // Light green border
                borderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }, {
                label: 'Not Joined Students',
                data: dataPointsnotJoined, // Y-axis data for deferred students
                backgroundColor: 'rgba(255, 206, 86, 0.2)', // Light yellow fill
                borderColor: 'rgba(255, 206, 86, 1)', // Yellow border
                borderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            plugins: {
                // Enable data labels on the chart
                datalabels: {
                    anchor: 'end', // Position of the label
                    align: 'end', // Alignment of the label
                    color: 'rgba(75, 192, 192, 1)', // Label text color
                    font: {
                        weight: 'bold',
                        size: 12 // Font size for the labels
                    },
                    formatter: (value) => {
                        // return value; // Display the value as the label
                        return ''; l
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'School Codes' // X-axis title
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Left Students / Deferred Students' // Y-axis title
                    },
                    beginAtZero: true // Start y-axis at 0
                }
            }
        },
        plugins: [ChartDataLabels] // Register the data labels plugin
    });
}




function appendStudentData(data) {
    if (data.studentData.length > 0) {
        const tableWrapper = document.createElement('div');
        tableWrapper.className = 'table-responsive';
        tableWrapper.style.marginTop = '130px';

        const table = document.createElement('table');
        table.className = 'table table-bordered';

        const tbody = document.createElement('tbody');

        let totalSum = 0;
        let totalApplicants = data.studentData[0].total_applicants ?? 0;

        delete data.studentData[0].total_summary;
        delete data.studentData[0].total_applicants;

        data.studentData.forEach(student => {
            const row = document.createElement('tr');

            let headers = Object.keys(data.studentData[0]);
            if (headers.includes('after_cms') || headers.includes('before_cms')) {
                headers = headers.filter(header => header !== 'left_students');
            }

            if (headers.includes('bnu_registeration_assigned_id')) {
                headers = headers.filter(header => header !== 'bnu_registeration_assigned_id');
                headers.push('bnu_registeration_assigned_id');
            }



            headers.forEach(header => {
                const th = document.createElement('th');
                const td = document.createElement('td');

                let humanReadableHeader = header
                    .replace(/_/g, ' ')
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');

                if (header === 'before_cms') {
                    humanReadableHeader = 'Students left before Cut-off Date';
                } else if (header === 'after_cms') {
                    humanReadableHeader = 'Students left after Cut-off Date';
                } else if (header === 'bnu_registeration_assigned_id') {
                    humanReadableHeader = 'BNU Registeration Assigned ID';
                }
                else if (header === 'admission_fee_paid_rejected') {
                    humanReadableHeader = 'Admission Fee Paid (Rejected)';
                }
                else if (header === 'selected_but_fee_not_paid') {
                    humanReadableHeader = 'Selected (Fee Not Paid)';
                }

                th.textContent = humanReadableHeader;
                th.className = 'text-left';

                const valueRow = document.createElement('tr');
                const headerCell = document.createElement('td');
                headerCell.textContent = humanReadableHeader;
                headerCell.className = 'text-left';
                valueRow.appendChild(headerCell);

                if (['left_students', 'after_cms', 'before_cms', 'bnu_registeration_assigned_id'].includes(header)) {
                    const textNode = document.createTextNode(student[header]);

                    const iconButton = document.createElement('button');
                    iconButton.className = 'btn btn-outline-warning btn-sm ml-4';
                    iconButton.innerHTML = '<i class="fa fa-eye"></i>';

                    iconButton.addEventListener('click', function () {
                        if (header === 'bnu_registeration_assigned_id') {
                            const dynamicColumn = header;
                            const dynamicValue = student[header];
                            handleTransferredToCMS(student.id, dynamicColumn, dynamicValue);
                        } else {
                            const dynamicColumn = header;
                            const dynamicValue = student[header];
                            fetchStudentDetails(student.id, dynamicColumn, dynamicValue);
                        }
                    });

                    const container = document.createElement('div');
                    container.className = 'salign-items-right';
                    container.appendChild(iconButton);
                    container.appendChild(textNode);
                    td.appendChild(container);

                    const value = parseInt(student[header]) || 0;
                    totalSum += value;
                } else {
                    td.textContent = student[header];
                    const value = parseInt(student[header]);
                    if (!isNaN(value)) {
                        totalSum += value;
                    }
                }

                td.className = 'text-right';
                valueRow.appendChild(td);
                tbody.appendChild(valueRow);
            });
        });

        const totalRow = document.createElement('tr');
        const totalLabelCell = document.createElement('td');
        // totalLabelCell.textContent = 'Total Applications';
        // totalLabelCell.className = 'text-left';
        // totalLabelCell.style.fontWeight = 'bold';
        // totalRow.appendChild(totalLabelCell);

        const totalCell = document.createElement('td');
        totalCell.textContent = totalSum;
        totalCell.className = 'text-right';
        // totalRow.appendChild(totalCell);

        const applicantsRow = document.createElement('tr');
        const applicantsLabelCell = document.createElement('td');
        // applicantsLabelCell.textContent = 'Total Profiles';
        // applicantsLabelCell.className = 'text-left';
        // applicantsLabelCell.style.fontWeight = 'bold';
        // applicantsRow.appendChild(applicantsLabelCell);

        const applicantsCell = document.createElement('td');
        applicantsCell.textContent = totalApplicants;
        applicantsCell.className = 'text-right';
        // applicantsRow.appendChild(applicantsCell);

        if (tbody.firstChild) {
            tbody.insertBefore(applicantsRow, tbody.firstChild);
            tbody.insertBefore(totalRow, tbody.firstChild);
        } else {
            tbody.appendChild(totalRow);
            tbody.appendChild(applicantsRow);
        }

        table.appendChild(tbody);
        tableWrapper.appendChild(table);

        const dataDiv = document.getElementById('tabularDataDiv');
        dataDiv.innerHTML = '';
        dataDiv.appendChild(tableWrapper);
    } else {
        console.log('No data to display or status is false.');
    }
}

function handleTransferredToCMS(studentId, column, value) {
    const sessionId = document.getElementById('roadmap_session').value;
    const selectedDate = document.getElementById('selected_date').value;

    console.log(`Handling transferred to CMS for Student ID: ${studentId}, Value: ${value}`);

    // Prepare the data payload
    const requestData = {
        studentId: studentId,
        sessionId: sessionId,
        selectedDate: selectedDate
    };

    // Add dynamic column and value
    if (column && value !== undefined) {
        requestData[column] = value;
    }

    // Show the modal and set a loading message
    $('#transferredToCMSModal').modal('show');
    const modalContent = document.getElementById('transferredToCMSContent');
    const chartCanvas = document.getElementById('transferredToCMSChart');
    modalContent.innerHTML = '<p>Loading...</p>';

    // Perform AJAX request
    $.ajax({
        url: '/students-transferred-to-cms-admission-data',
        method: 'GET',
        data: requestData,
        success: function (response) {
            if (response.length > 0) {
                // Extract data for chart and table
                const labels = response.map(row => row.school_name);
                const dataTotal = response.map(row => Number(row.total_students));
                const dataEnrolled = response.map(row => Number(row.enrolled_students));
                const dataNotEnrolled = response.map(row => Number(row.not_enrolled));

                // Build a bar chart
                const ctx = chartCanvas.getContext('2d');
                if (window.transferredChart) {
                    window.transferredChart.destroy();
                }
                window.transferredChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Students',
                                data: dataTotal,
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Enrolled Students',
                                data: dataEnrolled,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Not Enrolled Students',
                                data: dataNotEnrolled,
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Student Enrollment Comparison'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'School Names'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Number of Students'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Build a dynamic table
                let totalStudents = 0;
                let totalEnrolled = 0;
                let totalNotEnrolled = 0;

                let tableHTML = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>School Name</th>
                                <th>Total Students</th>
                                <th>Enrolled Students</th>
                                <th>Not Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Populate the table with data
                response.forEach((row, index) => {
                    totalStudents += Number(row.total_students);
                    totalEnrolled += Number(row.enrolled_students);
                    totalNotEnrolled += Number(row.not_enrolled);

                    tableHTML += `
                    <tr class="expandable-enrollment-row" data-index="${index}" data-school-id="${row.school_id}"

                        style="cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#f0f8ff';"
                        onmouseout="this.style.backgroundColor='';"
                        >


                            <td>${index + 1}</td>
                           <td style="text-decoration: underline;">${row.school_name}</td>

                            <td>${row.total_students}</td>
                            <td>${row.enrolled_students}</td>
                            <td>${row.not_enrolled}</td>
                        </tr>

                         <tr class="expanded-enrollment-content" data-index="${index}" style="display: none;">
                                <td colspan="8">
                                <div class="nested-enrollment-table-container text-left"
                                style="
                                    max-height: 200px;
                                    overflow-y: auto;

                            padding: 5px;
                            background: linear-gradient(to bottom, #f9f9fc, #ffffff);
                            color: #495057;
                            font-family: 'Arial', sans-serif;
                            font-size: 14px;
                            line-height: 1.6;

                        ">
                                Loading details...
                            </div>
                        </td>
                    </tr>




                    `;
                });

                // Add the total row
                tableHTML += `
                    <tr style="font-weight: bold;">
                        <td colspan="2">Total</td>
                        <td>${totalStudents}</td>
                        <td>${totalEnrolled}</td>
                        <td>${totalNotEnrolled}</td>
                    </tr>
                `;

                tableHTML += `
                        </tbody>
                    </table>
                `;

                // Inject the table into the modal content
                modalContent.innerHTML = tableHTML;



// Add click listeners for expandable rows after the table is rendered
const rows = document.querySelectorAll('.expandable-enrollment-row');
rows.forEach((row) => {
    row.addEventListener('click', function () {
        fetchAndRenderTableEnrollmentStatus(this);
    });
});


            } else {
                modalContent.innerHTML = '<p class="text-danger">No data available.</p>';
            }
        },
        error: function () {
            modalContent.innerHTML = '<p class="text-danger">Failed to load data. Please try again.</p>';
        }
    });
}


function fetchAndRenderTableEnrollmentStatus(row) {
    const schoolId = row.getAttribute('data-school-id'); // Get the school ID
    const index = row.getAttribute('data-index'); // Get the row's index
    const expandedRow = document.querySelector(`.expanded-enrollment-content[data-index="${index}"]`);
    const sessionId = document.getElementById('roadmap_session').value;

    // Prepare data payload
    const requestData = {
        school_id: schoolId,
        sessionId: sessionId,
    };

    // Remove 'active' class from all rows and reset background color
    document.querySelectorAll('tr').forEach((tr) => {
        tr.classList.remove('active-row');
    });

    // Collapse other expanded rows
    document.querySelectorAll('.expanded-enrollment-content').forEach((row) => {
        row.style.display = 'none'; // Collapse other rows
    });

    // Check if the current row is already expanded (toggle behavior)
    if (expandedRow.style.display === '' || expandedRow.style.display === 'none') {
        // expandedRow.style.display = ''; // Show the current expanded row


        expandedRow.style.display = 'table-row'; // Show the current expanded row

        // Add 'active' class to the clicked row
        row.classList.add('active-row');


        // Make an AJAX request to fetch additional data
        $.ajax({
            url: '/enrollment-status-student-admission-school-wise-data',
            method: 'GET',
            data: requestData, // Pass data to the server
            success: function (response) {
                const nestedTableContainer = expandedRow.querySelector('.nested-enrollment-table-container');
                let nestedTableHTML = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Prog Name</th>
                                <th>Total Students</th>
                                <th>Enrolled Students</th>
                                <th>Not Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Loop through the response data to populate the nested table
                response.data.forEach((detail, index) => {
                    nestedTableHTML += `
                        <tr data-index="${index}"
                            onmouseover="this.style.backgroundColor='#e9f7fd';"
                            onmouseout="this.style.backgroundColor='';">
                            <td>${detail.dept_name || 'N/A'}</td>
                            <td>${detail.prog_name || 'N/A'}</td>
                            <td>${detail.total_students || 0}</td>
                            <td>${detail.enrolled_students || 0}</td>
                            <td>${detail.not_enrolled || 0}</td>
                        </tr>
                    `;
                });

                nestedTableHTML += `
                        </tbody>
                    </table>
                `;

                  // Set the nested table HTML
                  nestedTableContainer.innerHTML = nestedTableHTML;
                nestedTableContainer.style.maxHeight = '200px'; // Adjust height as needed
                nestedTableContainer.style.overflowY = 'auto';
            },
            error: function (xhr, status, error) {
                console.error("Error fetching expanded data:", error);
                const nestedTableContainer = expandedRow.querySelector('.nested-enrollment-table-container');
                nestedTableContainer.innerHTML = `<p class="text-danger">Error loading data.</p>`;
            }
        });
    } else {
        expandedRow.style.display = 'none'; // Hide the current expanded row
        row.classList.remove('active-row'); // Remove 'active' class
    }
}




function renderStaticStudentTable(studentData,studentLeftData) {

    let totalStudentsSum = 0;
    let leftStudentsSum = 0;
    let deferStudentsSum = 0;
    let deferstuck_Off = 0;
    let defernotJoined = 0;
    let activeStudentsSum = 0;

    // Accumulate the sums using forEach
    studentData.forEach((student) => {
        totalStudentsSum += parseInt(student.total_students) || 0;
        leftStudentsSum += parseInt(student.left_students) || 0;
        deferStudentsSum += parseInt(student.defer_students) || 0;
        deferstuck_Off += parseInt(student.stuck_off) || 0;
        defernotJoined += parseInt(student.not_joined) || 0;
        activeStudentsSum += parseInt(student.active_students) || 0;
    });
    // Create the table structure
    let tableHTML = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center"><b>SCHOOL CODE</b></th>
                        <th class="text-center"><b>Total Students</b></th>
                        <th class="text-center"><b>Left (Enrolled/Not Enrolled)</b></th>
        <th class="text-center"><b>Deferred (Enrolled/Not Enrolled)</b></th>
                         <th class="text-center"><b>Struck Off Students</b></th>
                         <th class="text-center"><b>Not Joined Students</b></th>
                        <th class="text-center"><b>Active Students</b></th>

                    </tr>
                </thead>
                <tbody>
    `;

// Sort the studentData array by school_id
studentData.sort((a, b) => {
    if (a.school_id < b.school_id) return -1;
    if (a.school_id > b.school_id) return 1;
    return 0;
});
    // Use forEach to dynamically add rows based on the data in studentData
    studentData.forEach((student, index) => {
        tableHTML += `
            <tr class="expandable-row" data-index="${index}"   data-school-id="${student.school_id}"

            style="cursor: pointer;"
            onmouseover="this.style.backgroundColor='#f0f8ff';"
            onmouseout="this.style.backgroundColor='';"
            >
              <td style="text-align: left; text-decoration: underline;">${student.school_code}</td>
                <td class="text-center">${student.total_students || 'N/A'}</td>
               <td class="text-center">${student["left(enrolled/not enrolled)"] || 'N/A'}</td>
            <td class="text-center">${student["defer(enrolled/not enrolled)"] || 'N/A'}</td>
                <td class="text-center">${student.stuck_off}</td>
                <td class="text-center">${student.not_joined}</td>
                <td class="text-center">${student.active_students}</td>
            </tr>
         <tr class="expanded-content" data-index="${index}" style="display: none;">
            <td colspan="10">
                <div class="nested-table-container text-left"
            style="
                max-height: 200px;
                overflow-y: auto;

                padding: 5px;
                background: linear-gradient(to bottom, #f9f9fc, #ffffff);
                color: #495057;
                font-family: 'Arial', sans-serif;
                font-size: 14px;
                line-height: 1.6;

     ">
            Loading details...
        </div>
    </td>
</tr>


        `;
    });
    // Close the table structure
    // Add the footer row with the sums
    tableHTML += `
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"><b>Total</b></th>
                        <th class="text-center">${totalStudentsSum}</th>
                        <th class="text-center">${leftStudentsSum}</th>
                        <th class="text-center">${deferStudentsSum}</th>
                         <th class="text-center">${deferstuck_Off}</th>
                          <th class="text-center">${defernotJoined}</th>
                        <th class="text-center">${activeStudentsSum}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    // Append the generated table to the 'leftStudentMonthlyDiv'
    const dataDiv = document.getElementById('leftStudentMonthlyDiv');
    dataDiv.innerHTML = tableHTML;


    // Add click listeners for expandable rows
    const rows = document.querySelectorAll('.expandable-row');
    rows.forEach((row) => {
        row.addEventListener('click', function () {
            fetchAndRenderTableSchoolWise(this);
        });
    });


// ---------------------

let tableHTML2 = `
    <div class="card-header">
        <h5 class="modal-title">Monthly Details of Students Who Left (Excluding Deferred Status and Struck-Off Status)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center"><b>Month</b></th>
                    <th class="text-center"><b>MDSVAD</b></th>
                    <th class="text-center"><b>SMC</b></th>
                    <th class="text-center"><b>SCIT</b></th>
                    <th class="text-center"><b>SE</b></th>
                    <th class="text-center"><b>IP</b></th>
                    <th class="text-center"><b>RHSA</b></th>
                    <th class="text-center"><b>SMS</b></th>
                    <th class="text-center"><b>SLASS</b></th>
                </tr>
            </thead>
            <tbody>
`;

let sumMDSVAD = 0;
let sumSMC = 0;
let sumSCIT = 0;
let sumSE = 0;
let sumIP = 0;
let sumRHSA = 0;
let sumSMS = 0;
let sumSLASS = 0;

studentLeftData.forEach((student2) => {
    tableHTML2 += `
        <tr>
            <td class="text-center">${student2.change_month}</td>
            <td class="text-center">${student2.school_1}</td>
            <td class="text-center">${student2.school_4}</td>
            <td class="text-center">${student2.school_5}</td>
            <td class="text-center">${student2.school_6}</td>
            <td class="text-center">${student2.school_7}</td>
            <td class="text-center">${student2.school_2}</td>
            <td class="text-center">${student2.school_21}</td>
            <td class="text-center">${student2.school_3}</td>
        </tr>
    `;

    // Add the values to the sums
    sumMDSVAD += parseInt(student2.school_1) || 0;
    sumSMC += parseInt(student2.school_4) || 0;
    sumSCIT += parseInt(student2.school_5) || 0;
    sumSE += parseInt(student2.school_6) || 0;
    sumIP += parseInt(student2.school_7) || 0;
    sumRHSA += parseInt(student2.school_2) || 0;
    sumSMS += parseInt(student2.school_21) || 0;
    sumSLASS += parseInt(student2.school_3) || 0;
});

// Add the footer row with the sums
tableHTML2 += `
    <tr>
        <td class="text-center"><b>Total</b></td>
        <td class="text-center"><b>${sumMDSVAD}</b></td>
        <td class="text-center"><b>${sumSMC}</b></td>
        <td class="text-center"><b>${sumSCIT}</b></td>
        <td class="text-center"><b>${sumSE}</b></td>
        <td class="text-center"><b>${sumIP}</b></td>
        <td class="text-center"><b>${sumRHSA}</b></td>
        <td class="text-center"><b>${sumSMS}</b></td>
        <td class="text-center"><b>${sumSLASS}</b></td>
    </tr>
`;

// Close the table structure
tableHTML2 += `
            </tbody>
        </table>
    </div>
`;

// Append the generated table to the 'leftStudentMonthlyDiv'
const dataDiv2 = document.getElementById('leftStudentMonthlyDivData');  
dataDiv2.innerHTML = tableHTML2;



}




function fetchAndRenderTableSchoolWise(row) {
    const schoolId = row.getAttribute('data-school-id'); // Get the school ID
    const index = row.getAttribute('data-index'); // Get the row's index
    const expandedRow = document.querySelector(`.expanded-content[data-index="${index}"]`);
    const sessionId = document.getElementById('roadmap_session').value;

    // Prepare data payload
    const requestData = {
        school_id: schoolId,
        sessionId: sessionId,
    };

    // Remove 'active' class from all rows and reset background color
    document.querySelectorAll('tr').forEach((tr) => {
        tr.classList.remove('active-row');
    });

    // Collapse other expanded rows
    document.querySelectorAll('.expanded-content').forEach((row) => {
        if (row !== expandedRow) {
            row.style.display = 'none'; // Hide other rows
        }
    });

    // Check if the current row is already expanded
    if (expandedRow.style.display === '' || expandedRow.style.display === 'none') {
        expandedRow.style.display = 'table-row'; // Show the current expanded row

        // Add 'active' class to the clicked row
        row.classList.add('active-row');

        // Make an AJAX request to fetch additional data
        $.ajax({
            url: '/student-enrollment-status-school-wise-data',
            method: 'GET',
            data: requestData, // Pass data to the server
            success: function (response) {
                const nestedTableContainer = expandedRow.querySelector('.nested-table-container');
                let nestedTableHTML = `
                    <table class="table table-bordered table-striped table-hover" id="schoolWiseTable">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-left">Department Name</th>
                                <th class="text-left">Program</th>
                                <th class="text-left">Total Students</th>
                                <th class="text-left">Left Students</th>
                                <th class="text-left">Deferred Students</th>
                                <th class="text-left">Struck Off Students</th>
                                <th class="text-left">Not Joined Students</th>
                                <th class="text-left">Active Students</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Loop through the response data to populate the nested table
                response.data.forEach((detail, index) => {
                    nestedTableHTML += `
                        <tr data-index="${index}"
                            onmouseover="this.style.backgroundColor='#e9f7fd';"
                            onmouseout="this.style.backgroundColor='';"
                            style="cursor: pointer;">
                            <td class="text-center">${detail.dept_name}</td>
                            <td class="text-center">${detail.prog_name}</td>
                            <td class="text-center">${detail.total_students || 'N/A'}</td>
                            <td class="text-center">${detail.left_students}</td>
                            <td class="text-center">${detail.defer_students}</td>
                            <td class="text-center">${detail.stuck_off}</td>
                            <td class="text-center">${detail.not_joined}</td>
                            <td class="text-center">${detail.active_students}</td>
                        </tr>
                    `;
                });

                nestedTableHTML += `
                        </tbody>
                    </table>
                `;

                // Set the nested table HTML
                nestedTableContainer.innerHTML = nestedTableHTML;
                nestedTableContainer.style.maxHeight = '200px'; // Adjust height as needed
                nestedTableContainer.style.overflowY = 'auto';
            },
            error: function (xhr, status, error) {
                console.error("Error fetching expanded data:", error);
                const nestedTableContainer = expandedRow.querySelector('.nested-table-container');
                nestedTableContainer.innerHTML = `<p class="text-danger">Error loading data.</p>`;
            }
        });
    } else {
        expandedRow.style.display = 'none'; // Hide the current expanded row
        row.classList.remove('active-row'); // Remove 'active' class
    }
}





</script>
