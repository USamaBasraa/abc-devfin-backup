


<style>



.table-container {
    margin: 20px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    padding:50px;
}



.table th, .table td {
    padding: 12px;
    border-bottom: 1px solid #eaeaea;
}

.table tbody tr:hover {
    background: rgba(0, 123, 255, 0.1);
    transition: 0.3s ease-in-out;
}

.toggle-btn {
    background: none;
    border: none;
    font-size: 14px;
    cursor: pointer;
    color: #007bff;
    transition: 0.2s ease-in-out;
}

.toggle-btn:hover {
    color: #0056b3;
}

.parent-row {
    background: #f8f9fa;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
}

.child-row {
    background: #fff;
}

.expanded .toggle-btn {
    color: #ff5722 !important;
}


.toggle-btn { cursor: pointer; font-weight: bold; margin-right: 5px; }
        .node-icon { margin-right: 5px; }
        .parent-row { background-color: #f8f9fa; }

        .nav-tabs .nav-item .nav-link {
        font-weight: bold;
        color: #333; /* Dark text */
        padding: 10px 20px;
        transition: 0.3s ease-in-out;
    }





/* ✅ Fix Hover White-on-White Issue */
.nav-tabs .nav-item .nav-link:hover {
    background: #e7f1ff !important; /* Soft Light Blue */
    color: #0056b3 !important; /* Darker Blue for contrast */
    border-color: #0056b3 !important; 
    font-size: 1rem !important; 
    font-weight: 600 !important; 
}

/* ✅ Active Tab Design */
.nav-tabs .nav-item .nav-link.active {
    background: #0056b3 !important; /* Darker Blue */
    color: white !important;
    border-color: #004494 !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
}

/* ✅ Rounded Tabs & Smooth Hover */
.nav-tabs {
    border-bottom: 2px solid #007bff;
    font-size: 1rem !important;
    font-weight: 600 !important;
}

/* ✅ Adjust Padding & Spacing */
.nav-tabs .nav-link {
    border-radius: 5px 5px 0 0;
    padding: 10px 18px !important;
    transition: all 0.3s ease-in-out;
}

/* ✅ Optional: Better Tab Border */
.nav-tabs .nav-item {
    margin-bottom: -2px;
}

/* ✅ Light Blue Background for Content Area */
.tab-content {
    
    padding: 15px;
    border-top: none;
}

/* ✅ Light Blue Background for Active Tab */
.nav-tabs .nav-item .nav-link.active {
    background: #e7f1ff !important;
    color: #0056b3 !important;
    border-color: #0056b3 !important;
}


#nestedChart {
    
    max-width: 100%;
    height: auto; 
}

/* 🔹 Adjust Chart for Mobile Screens */
@media screen and (max-width: 768px) {
    #nestedChart {
        max-width: 90%;
        height: 250px;  /* Smaller height for mobile */
    }
}

/* 🔹 Adjust Chart for Laptop/Desktop */
@media screen and (min-width: 1024px) {
    #nestedChart {
        max-width: 800px; /* Large screens */
        height: 500px;  /* Adjust for laptop */
    }
}


</style>












<script>
    $(document).ready(function() {

        let schoolName='';
        let sessionName='';
        let activeSchoolId = localStorage.getItem('activeSchoolId') || null;
        let myPieChart; // Declare myPieChart in the higher scope

        // Common function to handle AJAX requests
        function sendAjaxRequest(url, method, data, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: data,
                success: successCallback,
                error: errorCallback
            });
        }

        // Common function to serialize form data
        function getFormData(formId) {
            return $(`#${formId}`).serialize();
        }



        // Handle success response
        function handleSuccessResponse(response) {
            if (response.status) {
                const responseCopy1 = JSON.parse(JSON.stringify(response));
                const responseCopy2 = JSON.parse(JSON.stringify(response));

                fetchSummaryData(responseCopy1);
                // generatePieChart(responseCopy1.studentData);
                // appendStudentData(responseCopy2)
                $('#tableDataDiv').find('.spinner-border').parent().remove();
            }
            $('#tableDataDiv').find('.spinner-border').parent().remove();
        }

        // Error callback for the AJAX request
        function handleErrorResponse(xhr, status, error) {
            console.error(xhr.responseText);
            alert('An error occurred while fetching data. Please try again.');
            $('#tableDataDiv').find('.spinner-border').parent().remove();
        }

        // Function to handle filter action
        function handleFilterAction(schoolId = null) {
            if (!$('#report_type').val()) {
            showNotification('Please select a Report Type.', 'warning', 'NotificationDiv');
            return false;  // Prevents further execution
        }

        let roadmapSession = $('#roadmap_session').val();
        if (!roadmapSession) {
            showNotification('Please select a Roadmap Session.', 'warning', 'NotificationDiv');
            return false;  // Prevents further execution
        }



            $('#schoolsButtonDiv').html('');
            $('#myPieChart').replaceWith('<canvas id="myPieChart" width="150" height="150"></canvas>');

            showLoadingSpinner('#tableDataDiv');

            let formData = {
                roadmapSession: $('#roadmap_session').val(),
                academicSession: $('#academic_session').val(),
                selectedDate: $('#selected_date').val(),
                reportType: $('#report_type').val(),
                schoolId: $('#school_id').val(),
                departmentId: $('#department_id').val(),
                programId: $('#program_id').val()
            };
            if (schoolId) {
                formData.schoolFilter = schoolId;
            }


            sendAjaxRequest('{{ route("student-admission-data") }}', 'GET', formData, handleSuccessResponse, handleErrorResponse);
        }

        function capitalizeWords(str) {
            return str.split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function generatePieChart(data) {
            // Check if data and studentData exist and are valid
            if (!data || !Array.isArray(data) || data.length === 0) {
                console.error("Invalid data format", data);
                return; // Exit if the structure is not valid
            }


            if ($('#myPieChart').length) { // Checks if the element exists
            let ctx = document.getElementById('myPieChart').getContext('2d');
            // Continue with your chart generation logic here
        } else {
            console.error('Element #myPieChart not found.');
        }




            $('html, body').animate({
            scrollTop: $('#myPieChartParentDiv').offset().top - 20
            }, 1000);


            // Prepare data dynamically based on the first student record
            const pieLabels = [];
            const pieDataValues = [];
            const backgroundColors = [
                '#FF9F9A',  // Soft coral
                '#B39DDB',  // Muted lavender
                '#FFD166',  // Soft golden yellow
                '#82CFFD',  // Light sky blue
                '#FFE082',  // Light amber
                '#A5D6A7',  // Soft green
                '#FFAB91',  // Warm peach
                '#DCE775',  // Soft lime green
                '#FFCC80',  // Warm orange
                '#CE93D8',  // Light purple
                '#FF8A80',  // Soft salmon red
                '#F4A261',  // Soft earthy orange
                '#76C7C0',  // Light teal
                '#A1887F',  // Muted brown
                '#F48FB1',  // Soft pink
            ];





            // Assuming we want to create the pie chart from the first item in studentData
            const student = data[0];
            delete student.total_summary;
            delete student.total_applicants;
            for (const [key, value] of Object.entries(student)) {


                if (key === 'before_cms' || key === 'after_cms') {
                    continue; // Skip these keys
                }



                let formattedLabel;

                    // Custom Label Logic
            switch(key) {
                case 'bnu_registration_id':
                    formattedLabel = 'BNU Registration ID';  // Custom label for student_status
                    break;
                case 'admission_fee_paid_rejected':
                    formattedLabel = 'Admission Fee Paid (Rejected)';  // Custom label for admission_fee
                    break;
                case 'selected_but_fee_not_paid':
                    formattedLabel = 'Selected (Fee Not Paid)';  // Custom label for program_type
                    break;
                default:
                    // Default formatting (if no custom label is found)
                    formattedLabel = capitalizeWords(key.replace(/_/g, ' ').replace('status', 'Status'));
            }




                
                const numericValue = parseInt(value, 10);
                // pieLabels.push(`${formattedLabel}: ${numericValue}`);

                pieLabels.push(`${formattedLabel}`);
                pieDataValues.push(numericValue);
            }

            // Prepare the pieData structure
            const pieData = {
                labels: pieLabels,
                datasets: [
                    {
                      //  label: 'Dataset 1',
                        data: pieDataValues,
                        backgroundColor: backgroundColors.slice(0, pieLabels.length), // Limit colors to labels count
                        hoverOffset: 4
                    }
                    // {
                    //     label: 'Dataset 2',
                    //     data: pieDataValues,
                    //     backgroundColor: backgroundColors.slice(0, pieLabels.length), // Limit colors to labels count
                    //     hoverOffset: 4
                    // }
            ]
            };

            // Check if canvas exists
            const canvasElement = document.getElementById('myPieChart');
            if (!canvasElement) {
                console.error('Canvas element not found');
                return; // Exit if canvas is not found
            }

            const ctx = canvasElement.getContext('2d');

            // Destroy any existing chart to avoid errors
            if (myPieChart) {
                myPieChart.destroy();
            }

            // Create the new pie chart
            myPieChart = new Chart(ctx, {
                type: 'pie',
                data: pieData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'left',
                            labels: {
                                boxWidth: 20,
                                padding: 20,
                                font: {
                                    size: 13,
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
                            text: `Applicant Details${sessionName ? ` for Session ${sessionName}` : ''}`,

                            font: {
                                family: 'Arial',
                                size: 30,
                                // weight: 'bold'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels],
            });
        }

        $('#filterStudentsButton').on('click', function() {

       console.log("Button clicked, checking report type...");
    if (!$('#report_type').val()) {
        console.log("No report type selected, showing notification.");
        showNotification('Please select a report type first.', 'warning', 'NotificationDiv');
        return false;
    }
    console.log("Report type selected, proceeding...");
     // Collect the form values and create an object
        let formData = {
        roadmapSession: $('#roadmap_session').val(),
        academicSession: $('#academic_session').val(),
        reportType: $('#report_type').val()
    };

    // Check if reportType is 'admission' or 'course_attendance' and call respective functions
    if (formData.reportType === 'admission') {
        handleFilterAction();
    } else if (formData.reportType === 'course_attendance') {

        getCourseAttendance(formData);
    }
    else if (formData.reportType === 'course_attendance_withdraw') {

        getCourseAttendance2(formData);
    }

    // Show/hide Generate Grand Summary Report button
    updateGrandSummaryButtonVisibility();

});

// Function to update Grand Summary Report button visibility
function updateGrandSummaryButtonVisibility() {
    const reportType = $('#report_type').val();
    const roadmapSession = $('#roadmap_session').val();
    
    if (reportType && roadmapSession) {
        $('#generateGrandSummaryBtn').show();
    } else {
        $('#generateGrandSummaryBtn').hide();
    }
}

// Handle Generate Grand Summary Report button click
$('#generateGrandSummaryBtn').on('click', function() {
    const roadmapSession = $('#roadmap_session').val();
    
    if (!roadmapSession) {
        showNotification('Please select a Roadmap Session first.', 'warning', 'NotificationDiv');
        return false;
    }
    
    // Show professional loading state
    showPDFLoadingState();
    
    // Create form data
    const formData = {
        roadmapSession: roadmapSession,
        _token: '{{ csrf_token() }}'
    };
    
    // Send AJAX request to generate PDF
    $.ajax({
        url: '{{ route("generate-grand-summary-report") }}',
        method: 'POST',
        data: formData,
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            // Create blob URL and open in new window
            const blob = new Blob([data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
            
            // Clean up
            window.URL.revokeObjectURL(url);
            
            showNotification('Grand Summary Report generated successfully!', 'success', 'NotificationDiv');
            
            // Keep loader visible for 2 more seconds after success
            setTimeout(function() {
                hidePDFLoadingState();
            }, 2000);
        },
        error: function(xhr, status, error) {
            console.error('Error generating PDF:', error);
            showNotification('Error generating PDF. Please try again.', 'error', 'NotificationDiv');
            
            // Keep loader visible for 2 more seconds after error
            setTimeout(function() {
                hidePDFLoadingState();
            }, 2000);
        },
        complete: function() {
            // Don't hide loading state immediately - let success/error handle it
        }
    });
});

// Update button visibility when form fields change
$('#report_type, #roadmap_session').on('change', function() {
    updateGrandSummaryButtonVisibility();
});




function fetchSummaryData(data) {
    const record = data.studentData[0];

    // ✅ **Tabs + Content Generate**
    let html = `
       <div class="container mt-4">
            <ul class="nav nav-tabs" id="summaryTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="hierarchy-tab" data-toggle="tab" href="#hierarchicalView">
                        &#128193; Hierarchical View
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="table-tab" data-toggle="tab" href="#tabularView">
                        &#128202; Tabular View
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="chart-tab" data-toggle="tab" href="#graphicalView">
                        &#128200; Graphical View
                    </a>
                </li>
            </ul>


            <div class="tab-content mt-3">
                <div id="hierarchicalView" class="tab-pane fade show active"></div>
                <div id="tabularView" class="tab-pane fade"></div>
                <div id="graphicalView" class="tab-pane fade">
                
                    <canvas id="nestedChart"></canvas>
                </div>
            </div>
        </div>
    `;

    $("#fetchSummaryDataDiv").html(html);



    let leftStudentsNode = { 
        id: "c14", 
        name: "Total Left", 
        value: Number(record.left_students) + Number(record.deferred_students || 0), 
        icon: "🚶", 
        subParts: [
            { id: "c14a", name: "Left Students", value: Number(record.left_students), icon: "🚶", subParts: [] },
            { id: "c14b", name: "Defer", value: Number(record.deferred_students || 0), icon: "⏸️", subParts: [] }
        ]
    };
 
// Check if Before CMS & After CMS exist


if (record.before_cms !== undefined && record.after_cms !== undefined) {
    leftStudentsNode = {
        id: "c14", 
        name: "Total Left", 
        value: Number(record.before_cms) + Number(record.after_cms), 
        icon: "", 
        subParts: [
            { id: "c14a", name: "Left Students", value: Number(record.before_cms), icon: "➡️", subParts: [] },
            { id: "c14b", name: "Left Students After Cut-off Date", value: Number(record.after_cms), icon: "➡️", subParts: [] }
        ]
    };
}


    // ✅ **Data Structure with Icons**
    const summaryData = [
        { id: "p1", name: "Number of individuals who registered on the Admission Portal", value: Number(record.registered_on_portal), icon: "👤", subParts: [] },
        { id: "p2", name: "Total Number of Applications", value: Number(record.total_applied_applicants), icon: "📄", subParts: [
            { id: "c1", name: "Admission Form Not Submitted", value: Number(record.incomplete_form_submission), icon: "❌", subParts: [] },
            { id: "c1a", name: "Admission Form Not Submitted but fee paid", value: Number(record.fee_verified_not_final_submit), icon: "❌", subParts: [] },
            { id: "c2", name: "Admission Form Submitted", value: Number(record.completed_form_submission), icon: "", subParts: [
                { id: "c3", name: "Admission Fee Not Paid", value: Number(record.processing_fee_not_verified), icon: "❌", subParts: [] },
                { id: "c4", name: "Admission Fee Paid", value: Number(record.processing_fee_verified), icon: "", subParts: [
                    { id: "c5", name: "Documents Incomplete for Admission Test", value: Number(record.documents_not_accepted), icon: "❌", subParts: [] },
                    { id: "c6", name: "Documents Compelete for Admission Test", value: Number(record.documents_accepted), icon: "", subParts: [
                        { id: "c7", name: "Applicants Not Appeared For Admission Test", value: Number(record.applicants_not_appeared_for_test), icon: "❌", subParts: [] },
                        { id: "c8", name: "Applicants Appeared For Admission Test", value: Number(record.applicants_appeared_for_test), icon: "", subParts: [
                            { id: "c9", name: "Applicants Rejected", value: Number(record.students_status_rejected), icon: "❌", subParts: [] },
                            { id: "c10", name: "Admission Offered", value: Number(record.admission_offered), icon: "", subParts: [
                                { id: "c11", name: "Applicants Declined Offer", value: Number(record.fee_not_paid), icon: "❌", subParts: [] },
                                { id: "c12", name: "Applicants Accepted Offer", value: Number(record.fee_paid), icon: "", subParts: [
                                    { id: "c13", name: "BNU Registeration Assigned ID", value: Number(record.students_moved_to_cms), icon: "🔁", subParts: [], excludeFromSum: true },
                                    leftStudentsNode,  // ✅ Dynamically Adding Left Students or CMS Transition

                                    // { id: "c14", name: "Left Students", value: Number(record.left_students), icon: "🚶", subParts: [] },
                                    { id: "c15", name: "Final Status (Active Students)", value: Number(record.active_students), icon: "✔️", subParts: [] },
                                        { id: "c13a", name: "No. of Current Students (Paid / Partial Paid / Unpaid)", value: Number(record.current_partial_full_paid), icon: "💰", subParts: [] },
                                        { id: "c13a", name: "Status Active with Cancelled Challan", value: Number(record.challan_not_exists_but_active), icon: "💰", subParts: [] },
                                    // { id: "c16", name: "Final Status", value: Number(record.active_students), icon: "✔️", subParts: [] }
                                ] }
                            ] }
                        ] }
                    ] }
                ] }
            ] }
        ] }
    ];



    // ✅ **Function to Calculate Sum**
    function calculateSum(node) {
        if (!node.subParts || node.subParts.length === 0) return node.value;

        let sum = node.subParts.reduce((acc, child) => {
            if (child.name !== "BNU Registeration Assigned ID") {  // ✅ Exclude from Sum
                return acc + calculateSum(child);
            }
            return acc;
        }, 0);

        node.childSum = sum;
        return sum;
    }

    // ✅ **Calculate Sum for All Parent Nodes**
    summaryData.forEach(node => calculateSum(node));

    // ✅ **Generate Hierarchical View**
    function generateHierarchicalView() {
        function generateTableRows(data, level = 0, parentId = "") {
            let rowsHtml = "";
            data.forEach((item, index) => {
                let rowId = parentId ? `${parentId}-${index}` : `row-${index}`;
                let hasChildren = item.subParts.length > 0;
                let icon = item.icon ? `<span class="node-icon">${item.icon}</span>` : "";
                let toggleIcon = hasChildren ? `<span class="toggle-btn text-primary" data-target="${rowId}">➕</span>` : "";

                rowsHtml += `
                    <tr id="${rowId}" class="${hasChildren ? "parent-row" : "child-row"}" style="display:${parentId ? "none" : "table-row"};">
                        <td style="padding-left: ${level * 15}px;">
                            ${toggleIcon} ${icon} ${item.name}
                        </td>
                        <td class="text-center">${item.value}</td>
                          
                    </tr>
                `;
                // <td class="text-center">${hasChildren ? item.childSum : "-"}</td>
                if (hasChildren) {
                    rowsHtml += generateTableRows(item.subParts, level + 1, rowId);
                }
            });
            return rowsHtml;
        }
        // <th class='text-center'>Aggregated Sum</th>
        return `
            <div class="table-container pl-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Summary Section</th>
                            <th class='text-center'>Value</th>
                      
                        </tr>
                    </thead>
                    <tbody>
                        ${generateTableRows(summaryData)}
                    </tbody>
                </table>
            </div>
        `;
    }


    function generateTabularView() {
    function calculateSum(node) {
        if (!node.subParts || node.subParts.length === 0) return node.value;

        let sum = node.subParts.reduce((acc, child) => {
            if (child.name !== "BNU Registeration Assigned ID") { // ✅ Exclude CMS Applicants from Sum
                return acc + calculateSum(child);
            }
            return acc;
        }, 0);

        node.childSum = sum;
        return sum;
    }

    // ✅ **Calculate Sum for All Parent Nodes**
    summaryData.forEach(node => calculateSum(node));

    function generateTabularRows(data, level = 0) {
    let rowsHtml = "";
    data.forEach(item => {
        let hasChildren = item.subParts.length > 0;

        // ✅ Apply background color to specific rows
        let specialRowStyle = "";
        if (["BNU Registeration Assigned ID", "Total Left", "Defer"].includes(item.name)) {
            specialRowStyle = "background-color: #e7f1ff !important; color: #155724 !important;"; // Soft Green
        }

        // ✅ Make "Final Status (Active Students)" bold
        let fontWeight = item.name === "Final Status (Active Students)" ? "bold" : hasChildren ? "600" : "400";

        rowsHtml += `
            <tr style="${specialRowStyle}">
                <td style="padding-left: ${level * 0}px; font-weight: ${fontWeight}; color: ${hasChildren ? '#2C3E50' : '#495057'};">
                    ${item.name}
                </td>
                <td class="text-center" style="font-weight: ${fontWeight};">
                    ${item.value}
                </td>
            </tr>
        `;

        if (hasChildren) {
            rowsHtml += generateTabularRows(item.subParts, level + 1);
        }
    });
    return rowsHtml;
}

    // <th class="text-center" style="color: #212529; font-weight: 600;">Aggregated Sum</th>

    return `
        <div class="table-responsive">
            <table class="table table-hover shadow-sm">
                <thead>
                    <tr style="background: #F1F3F5; border-bottom: 2px solid #DEE2E6;">
                        <th style="color: #212529; font-weight: 600; text-transform: uppercase;">Summary Section</th>
                        <th class="text-center" style="color: #212529; font-weight: 600;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    ${generateTabularRows(summaryData)}
                </tbody>
            </table>
        </div>
    `;
}




function calculateAggregations(data) {
    data.forEach(node => {
        if (node.subParts.length > 0) {
            let sum = 0;

            node.subParts.forEach(child => {
                if (child.name !== "BNU Registeration Assigned ID" && child.name !== "Active Students") {
                    sum += child.value;
                }
            });

            node.childSum = sum > 0 ? sum : "-"; // ✅ Agar sum 0 hai to "-"
        }

        // ✅ Recursive Call for Nested Levels
        if (node.subParts.length > 0) {
            calculateAggregations(node.subParts);
        }
    });
}



    $("#hierarchicalView").html(generateHierarchicalView());
    $("#tabularView").html(generateTabularView());




  // ✅ Expand/Collapse Logic with Nested State Tracking
$(".toggle-btn").click(function () {
    let target = $(this).data("target");
    let icon = $(this).text();

    if (icon === "➕") {
        $(this).text("➖").removeClass("text-primary").addClass("text-danger"); // ✅ Change Icon to Minus
        
        // ✅ **Sirf Direct Children Expand Hongay**
        $(`tr[id^='${target}-']`).each(function () {
            let directChild = $(this).attr("id").split("-").length === target.split("-").length + 1;
            if (directChild) {
                $(this).fadeIn(200);
            }
        });

    } else {
        $(this).text("➕").removeClass("text-danger").addClass("text-primary"); // ✅ Change Icon to Plus

        // ✅ **Saare Nested Children Hide Hongay & Icons Reset**
        $(`tr[id^='${target}-']`).each(function () {
            $(this).fadeOut(200);
            $(this).find(".toggle-btn").text("➕").removeClass("text-danger").addClass("text-primary"); // ✅ Reset Inner Icons
        });
    }
});










const recordValues = [
    record.incomplete_form_submission,
    record.fee_verified_not_final_submit,
    record.processing_fee_not_verified,
    record.documents_not_accepted,
    record.applicants_not_appeared_for_test,
    record.students_status_rejected,
    record.fee_not_paid,
    record.left_students,
    record.deferred_students || 0,
    record.active_students
];


// Check for before_cms and after_cms
if (record.before_cms !== undefined && record.after_cms !== undefined) {
    recordValues.splice(6, 1); // Remove Left Students (index 6)
    recordValues.push(record.before_cms, record.after_cms);
}



// Convert all values to BigInt & sum them up
const totalSum = recordValues.map(value => BigInt(value)).reduce((acc, val) => acc + val, BigInt(0));
const totalSumNumber = Number(totalSum);




// ✅ Labels
const parentLabel = "Total Applications"; // Outer circle (total sum)
let childLabels = [
    "Admission Form Not Submitted",
    "Admission Form Not Submitted but fee paid",
    "Admission Fee Not Paid",
    "Documents Incomplete for Admission Test",
    "Applicants Not Appeared For Admission Test",
    "Applicants Rejected",
    "Applicants Declined Offer",
    "Left Students",
    "Defer",
    "Active Students"
];

if (record.before_cms !== undefined && record.after_cms !== undefined) {
    childLabels.splice(6, 1); // Remove Left Students label
    childLabels.push("Left Students", "Left Students After Cut-off Date");
}






// // ✅ Convert all values to Number for Chart.js
// const childData = recordValues.map(value => Number(value));
// const parentData = [totalSumNumber]; // Only total in the parent dataset


// Convert values for Chart.js
const childData = recordValues.map(value => Number(value));
const parentData = [totalSumNumber];


const parentBackgroundColor = ["#81C784"]; // **Muted Fresh Green (Subtle & Professional)**

let childBackgroundColors = [
    "#64B5F6", "#4DB6AC", "#FF7043", "#81C784", "#FFB74D", "#BA68C8", "#FFF176", "#E57373", "#A5D6A7", "#FF9800"
];

if (record.before_cms !== undefined && record.after_cms !== undefined) {
    childBackgroundColors.splice(6, 1); // Remove Left Students color
    childBackgroundColors.push("#42A5F5", "#FFA726"); // Colors for Before CMS & After CMS
}



let nestedChartInstance = null;
  
function generateNestedChart() {
    if (nestedChartInstance !== null) {
        nestedChartInstance.destroy();
    }

    var ctx = document.getElementById("nestedChart").getContext("2d");

    nestedChartInstance = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: [parentLabel, ...childLabels], 
            datasets: [
                { 
                    data: parentData,
                    backgroundColor: parentBackgroundColor,
                    hoverOffset: 12,
                    borderWidth: 3,
                    circumference: 360, 
                    rotation: 0
                },
                { 
                    data: childData,
                    backgroundColor: childBackgroundColors,
                    hoverOffset: 12,
                    borderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,  // ✅ Chart will adjust to screen size
            maintainAspectRatio: false,  // ✅ Allows custom height & width
            cutout: "40%", 
            spacing: 0,

            layout: {
                padding: 10
            },
            plugins: {
                legend: {
                    display: true,
                    position: "right",
                    labels: {
                        font: { size: 14 },
                        color: "#333",
                        generateLabels: function (chart) {
                            let labels = chart.data.labels;
                            let colors = [...parentBackgroundColor, ...childBackgroundColors]; 
                            return labels.map((label, index) => {
                                let value = chart.data.datasets[1] && chart.data.datasets[1].data[index - 1] !== undefined
                                    ? chart.data.datasets[1].data[index - 1]
                                    : totalSumNumber;

                                return {
                                    text: `${label} (\u00A0${value}\u00A0)`, // Adds spacing around the number
                                    fillStyle: colors[index],
                                    strokeStyle: colors[index],
                                    hidden: false
                                };
                            });
                        }

                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function (tooltipItem) {
                            let datasetIndex = tooltipItem.datasetIndex;
                            let index = tooltipItem.dataIndex;
                            let label = datasetIndex === 0 ? parentLabel : childLabels[index - 1]; 
                            let value = datasetIndex === 0 ? totalSumNumber : childData[index - 1];
                            return `${label}: ${value}`;
                        }
                    }
                },
                datalabels: {
    anchor: "center",
    align: "center",
    color: "#FFF",
    backgroundColor: "rgba(0,0,0,0.7)",
    borderRadius: 5,
    font: (ctx) => {
        // Check if the dataset is the parent (index 0) and apply larger font
        return ctx.datasetIndex === 0 ? { size: 14, weight: "bold" } : { size: 9 };
    },
    formatter: (value, ctx) => {
        return value > 0 ? value : "";
    },
    clamp: false,
    clip: false,
    padding: 4,
    textStrokeColor: "black",
    textStrokeWidth: 2
}

            }
        },
        plugins: [ChartDataLabels]
    });
}







    // ✅ Call Pie Chart Function When Graphical Tab is Clicked
    $("#chart-tab").on("shown.bs.tab", function () {
        generateNestedChart();
    });


}








    });









</script>
