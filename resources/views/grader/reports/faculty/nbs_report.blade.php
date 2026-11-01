@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- DataTables JS -->
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script> -->

<!-- Add custom CSS for theme-consistent styling -->
    <style>
        .theme-bg {
            background-color: #1548a3 !important;
            color: white !important;
        }
        
        .theme-text {
            color: #1548a3 !important;
        }
        
        .theme-border {
            border-color: #1548a3 !important;
        }
        
        .theme-btn {
            color: #1548a3 !important; 
            border: 1px solid #1548a3 !important;
            background-color: transparent !important;
            transition: all 0.3s !important;
        }
        
        .theme-btn:hover {
            background-image: linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%) !important;
        }
        
        .validation-alert {
            border-left: 4px solid #1548a3 !important;
            background-color: #f8f9fa !important;
            color: #333 !important;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            border: none !important;
        }
        
        .card-header {
            border-bottom: 2px solid #1548a3 !important;
        }
        
        .table thead th {
            border-bottom: 2px solid #1548a3 !important;
        }
        
        .btn-group .btn {
            margin-right: 5px;
        }
        
        /* Animation for filter actions */
        @keyframes highlight {
            0% { background-color: transparent; }
            50% { background-color: rgba(21, 72, 163, 0.1); }
            100% { background-color: transparent; }
        }
        
        .highlight-animation {
            animation: highlight 1s ease-in-out;
        }
    </style>
</head>
<body>
<div style="width:500px; margin:10px auto;">
	<div class="easyui-panel" title="Scholarship Report" style="padding:20px;">
	<form method="post" action="scholarship_detail_report.php" target="_blank" id="scholarshipForm">
		<table style="margin-left:auto; margin-right:auto;">
        	<tr>
            	<td align="left">Session : </td>
           	 	<td align="left">
            		    <input id="ad_sess_id" name="ad_sess_id" style="width:300px;" placeholder="Sessions"  class="easyui-combobox form-control" data-options="
                   valueField:'sess_id',
                   textField:'sess_name',
                   url: '<?= url('/sessions') ?>',
                   method:'GET',
                   onSelect: function(rec){
                    var urlcourse = '<?=url('/course_offering_exam')?>?sess_id='+rec.sess_id;
                        <!-- $('#ca_course_id_name').combobox('reload', urlcourse);    -->
                   } ">
				</td>
        	</tr>
      		<tr>
            	<td align="left">School : </td>
            	<td align="left">
                	  <input id="ad_school_id" name="ad_school_id" style="width:300px;"  class="easyui-combobox form-control" data-options="
                    valueField: 'school_id',
                    textField: 'school_name',
                    required:'true',
                    method:'get',
                    url: '<?= url('/schools') ?>',
                    onSelect: function(rec){
                        var urladmschId = '<?=url('/adm_schedules')?>?school_id='+rec.school_id+'&sess_id='+ $('#ad_sess_id').combobox('getValue');
                        $('#admission_sch').combobox('reload', urladmschId);    
                    }">
            </td>
            <tr>
            <td align="left">Admission Schedules : </td>
            <td align="left">
                <input id="admission_sch" name="admission_sch" style="width:300px;" 
                class="easyui-combobox" data-options="
                    method:'get',
                   valueField:'admission_sch_id',
                   textField :'admission_sch_name',
                   url: '<?= url('/adm_schedules') ?>',
                    onSelect: function(rec){
                       
                    }">
            </td>
        </tr>

            <tr>
    <td align="left">Scholarship:</td>
    <td align="left">
        <input id="scholarship_type" name="scholarship_type" style="width:300px;" class="easyui-combobox" data-options="
            valueField: 'scholarship_type',
            textField: 'scholarship_name',
            data: [
                {
                    'scholarship_type': 'api_needbase_scholarship',
                    'scholarship_name': 'Need Base Scholarship'
                }
            ]
        ">
    </td>
</tr>
<!-- ,
                {
                    'scholarship_type': 'api_transgenderbase',
                    'scholarship_name': 'Transgender Scholarship'
                },
                {
                    'scholarship_type': 'api_deansbase',
                    'scholarship_name': 'Deans Scholarship'
                },
                {
                    'scholarship_type': 'api_sportsbase',
                    'scholarship_name': 'Sports Scholarship'
                },
                {
                    'scholarship_type': 'api_bnuinstitutionalpatronage',
                    'scholarship_name': 'BNU Institutional Support '
                },
                {
                    'scholarship_type': 'api_vcscholarship',
                    'scholarship_name': 'VC Scholarship'
                } -->
<tr>
    <td align="left">Scholarship Filter</td>
    <td align="left">
        <input id="scholarship_filter_type" name="scholarship_filter_type" style="width:300px;" class="easyui-combobox" data-options="
            valueField: 'scholarship_filter_type',
            textField: 'scholarship_filter_name',
            data: [
                {
                    'scholarship_filter_type': '',
                    'scholarship_filter_name': 'All'
                },
                {
                    'scholarship_filter_type': 'Yes',
                    'scholarship_filter_name': 'Yes'
                },
                {
                    'scholarship_filter_type': 'No',
                    'scholarship_filter_name': 'No'
                }
            ]
        ">
    </td>
</tr>
<tr style="display:none">
    <td align="left">Selected Student</td>
    <td align="left">
        <input id="selected_filter_type" name="selected_filter_type" style="width:300px;" class="easyui-combobox" data-options="
            valueField: 'selected_filter_type',
            textField: 'selected_filter_name',
            data: [
                {
                    'selected_filter_type': '',
                    'selected_filter_name': 'All'
                },
                {
                    'selected_filter_type': 'Yes',
                    'selected_filter_name': 'Yes'
                },
                {
                    'selected_filter_type': 'No',
                    'selected_filter_name': 'No'
                }
            ]
        ">
    </td>
</tr>

<tr style="display:none">
    <td align="left">Fee Status</td>
    <td align="left">
        <input id="status_filter_type" name="status_filter_type" style="width:300px;" class="easyui-combobox" data-options="
            valueField: 'status_filter_type',
            textField: 'status_filter_name',
            data: [
                {
                    'status_filter_type': '',
                    'status_filter_name': 'All'
                },
                {
                    'status_filter_type': 'Verified Students',
                    'status_filter_name': 'Verified Students'
                },
                {
                    'status_filter_type': 'Non Verified Students',
                    'status_filter_name': 'Non Verified Students'
                }
              
            ]
        ">
    </td>
</tr>



        <!-- <tr>
            <td align="left">Department : </td>
            <td align="left">
                <input id="ad_dept_id" name="ad_dept_id" style="width:300px;"
                	class="easyui-combobox" data-options="
                   			valueField:'dept_id',
                   			textField:'dept_name',
                   			url: '../admission/departments.json.php',
                   			onSelect: function(rec){
                        		$('#ad_prog_id').combobox('clear');
      								var urlprog = '../admission/programs.json.php?sdpo_sess_id='+$('#ad_sess_id').combobox('getValue')+'&sdpo_school_id='+$('#ad_school_id').combobox('getValue')+'&sdpo_dept_id='+rec.dept_id;
                        		$('#ad_prog_id').combobox('reload', urlprog);

                   }">
            </td>
        </tr> -->
        <!-- <tr>
            <td align="left">Program : </td>
            <td align="left">
                <input id="ad_prog_id" name="ad_prog_id" style="width:300px;"
                class="easyui-combobox" data-options="
                   		valueField:'prog_id',
                   		textField :'prog_title',
                   		url: '../admission/programs.json.php',
                  	 	onSelect: function(rec){
                   }">
            </td>
        </tr> -->
        <tr>
    <td colspan="2" align="center"> 
        <button type="submit" 
            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
            <i class="fa fa-file-excel"></i> Generate Excel
        </button>    

        <button type="button" onclick="applyScholarshipFilter()" 
            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
            <i class="fa fa-filter"></i> Apply Filter
        </button>

        <button type="button" onclick="resetFilters()" 
            style="color: #1548a3; border: 1px solid #1548a3; background-color: transparent;"
            onmouseover="this.style.backgroundImage='linear-gradient(to bottom, #EFF5FF 0%, #E0ECFF 100%)'; this.style.color='#1548a3';"
            onmouseout="this.style.backgroundImage='none'; this.style.color='#1548a3';"
            class="btn">
            <i class="fa fa-sync"></i> Reset
        </button>
    </td>
</tr>




      </table>
	</form>


     <!-- Add validation alert div with close button -->
 <div id="validationAlert" class="alert validation-alert" role="alert" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-exclamation-circle theme-text mr-2"></i> 
                <span id="validationMessage"></span>
            </div>
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
 


    </div>
            </div>
<!-- Bootstrap 4 CSS -->


<div class="container mt-4" style="max-width: 2000px !important">
   
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 theme-text">
                    <i class="fa fa-graduation-cap mr-2"></i> Scholarship Applicants List
                </h4>
                <div id="filterStatus" class="small text-muted" style="display: none;">
                    <i class="fa fa-filter mr-1"></i> Filters applied
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="scholarshipTable" class="table table-bordered table-striped table-hover text-center display responsive nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-left"><i class="fa fa-user mr-1"></i> Full Name</th>
                            <th scope="col"><i class="fa fa-id-card mr-1"></i> Applicant ID</th>
                            <th scope="col"><i class="fa fa-envelope mr-1"></i> Email</th>
                            <th scope="col"><i class="fa fa-university mr-1"></i> School</th>
                            <th scope="col"><i class="fa fa-book mr-1"></i> Program</th>
                            <th scope="col"><i class="fa fa-percentage mr-1"></i> Percentage</th>
                            <th scope="col"><i class="fa fa-comment mr-1"></i> Remarks</th>
                            <th scope="col"><i class="fa fa-save mr-1"></i> Save</th>
                            <th scope="col"><i class="fa fa-cogs mr-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows will be dynamically injected by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script>
// Initialize DataTable
let scholarshipTable;

function applyScholarshipFilter() {

    // Hide any previous validation messages
    $('#validationAlert').hide();
    
    // Check required fields
    var sessionId = $('#ad_sess_id').combobox('getValue');
    var admissionSch = $('#admission_sch').combobox('getValue');
    var scholarshipType = $('#scholarship_type').combobox('getValue');
    var scholarshipFilterType = $('#scholarship_filter_type').combobox('getValue');

    // Validate required fields
    if (!sessionId || sessionId === '') {
        showValidationMessage('Please select a Session');
        return;
    }
    
    if (!admissionSch || admissionSch === '') {
        showValidationMessage('Please select an Admission Schedule');
        return;
    }
    
    if (!scholarshipType || scholarshipType === '') {
        showValidationMessage('Please select a Scholarship Type');
        return;
    }
    
    if (!scholarshipFilterType || scholarshipFilterType === '') {
        showValidationMessage('Please select a Scholarship Filter');
        return;
    }

    // Show loading overlay or spinner
    if (scholarshipTable) {
        scholarshipTable.destroy();
    }

    $('#scholarshipTable').addClass('loading');
    $('#filterStatus').show(); // Show the filter status indicator
    
    const data = {
        ad_sess_id: sessionId,
        ad_school_id: $('#ad_school_id').combobox('getValue'),
        admission_sch: admissionSch,
        scholarship_type: scholarshipType,
        scholarship_filter_type: scholarshipFilterType,
        selected_filter_type: $('#selected_filter_type').combobox('getValue'),
        status_filter_type: $('#status_filter_type').combobox('getValue')
    };

    $.ajax({
        url: '/reports/scholarship_criteria_report',
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            $('#scholarshipTable').removeClass('loading');
            
            // Process the data to match DataTables format
            if (response && response.status && Array.isArray(response.data)) {
                const processedData = processDataForTable(response.data, data.scholarship_type);
                initializeDataTable(processedData,sessionId);
            } else {
                // Handle error or empty response
                initializeDataTable([]);
                console.error("Invalid response format:", response);
            }
        },
        error: function(xhr, status, error) {
            $('#scholarshipTable').removeClass('loading');
            initializeDataTable([]);
            console.error("AJAX Error:", error);
            if (xhr.responseText) {
                console.error("Server response:", xhr.responseText);
            }
        }
    });
}

// Process data for DataTable format
function processDataForTable(data, scholarshipType) {
    const sessionId = $('#ad_sess_id').combobox('getValue');
    // alert(scholarshipType);
    
    return data.map(function(applicant) {
        // Create action buttons with icons
        const actionButtons = `
            <div class="btn-group" role="group">
                <button class="btn btn-sm theme-btn download-btn" 
                    data-applicant-id="${applicant.aai_id || ''}"
                    data-scholarship-type="${scholarshipType || ''}"
                    data-session-id="${sessionId || ''}">
                    <i class="fa fa-file-excel mr-1"></i> Excel
                </button>
               
            </div>`;
        percentageInput = `
            <div style="display: flex; align-items: center; gap: 5px;">
                <input type="number" value="${applicant.percentage || ''}"  class="form-control form-control-sm percentage-input"
                    data-applicant-id="${applicant.aai_id}" placeholder="%" style="width: 70px;" />
                <i class="fa fa-check-circle text-success tick-icon" 
                    style="display: none; font-size: 16px;"></i>
            </div>`;
        remarksInput = `
            <input type="text" value="${applicant.remarks || ''}"  class="form-control form-control-sm remarks-input" 
                data-applicant-id="${applicant.aai_id}" placeholder="Remarks" style="width: 150px;" />`;

        const saveButton = `<button class="btn btn-sm btn-success save-btn" 
                        data-applicant-id="${applicant.aai_id}">
                        <i class="fa fa-save"></i> Save
                    </button>`;
        
        // Return formatted row data
        return [
            applicant.api_name || '',
            applicant.aai_id || '',
            applicant.api_email || '',
            applicant.school_code || '',
            applicant.aai_programid || '',
            percentageInput,
            remarksInput,
            saveButton,
            actionButtons
        ];
    });
}

// Initialize DataTable with processed data
function initializeDataTable(data,sessionId) {
    scholarshipTable = $('#scholarshipTable').DataTable({
        data: data,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table",
            paginate: {
                first: "First",
                last: "Last", 
                next: "Next",
                previous: "Previous"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { responsivePriority: 1, targets: [0, 1, 5] }, // Name, ID and action are most important
            { responsivePriority: 2, targets: [2] },       // Email is next
            { responsivePriority: 3, targets: [3, 4] }     // School and Program are least important
        ]
    });

     // Handle Save button click
     $('#scholarshipTable').on('click', '.save-btn', function () {
        const applicantId = $(this).data('applicant-id');
        const percentage = $(`.percentage-input[data-applicant-id="${applicantId}"]`).val();
        const remarks = $(`.remarks-input[data-applicant-id="${applicantId}"]`).val();

        $.ajax({
            url: '/save-scholarship-input',
            method: 'POST',
            data: {
                applicant_id: applicantId,
                percentage: percentage,
                sessionId:sessionId,
                remarks: remarks,
                _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            },
            success: function (response) {
                 // Find the input and its parent container
                const input = $(`.percentage-input[data-applicant-id="${applicantId}"]`);
                const tickIcon = input.siblings('.tick-icon');

                // Show the tick icon
                tickIcon.show();

                // Optional: hide again after 2 seconds
                setTimeout(() => tickIcon.hide(), 2000);
                // applyScholarshipFilter();
            },
            error: function () {
                alert('Failed to save');
            }
        });
    });
    
    // Add event listener for download buttons
    $('#scholarshipTable').on('click', '.download-btn', function() {
        const applicantId = $(this).data('applicant-id');
        const scholarshipType = $(this).data('scholarship-type');
        const sessionId = $(this).data('session-id');
        
        // Redirect to scholarship_criteria_excel.php with all three parameters
        window.open(`https://staff.bnu.edu.pk/HOD/scholarship_criteria_excel.php?applicant_id=${applicantId}&scholarship_type=${scholarshipType}&ad_sess_id=${sessionId}`, '_blank');
    });
    
    // View button event handler
    // $('#scholarshipTable').on('click', '.view-btn', function() {
    //     const applicantId = $(this).data('applicant-id');
    //     const scholarshipType = $(this).data('scholarship-type');
        
    //     // Show applicant details in a modal or new page
    //     window.open(`applicant_details.php?applicant_id=${applicantId}&scholarship_type=${scholarshipType}`, '_blank');
    // });
    
    // Print button event handler
    // $('#scholarshipTable').on('click', '.print-btn', function() {
    //     const applicantId = $(this).data('applicant-id');
        
    //     // Generate a print-friendly version
    //     window.open(`scholarship_print.php?applicant_id=${applicantId}`, '_blank');
    // });
}

// Add some custom CSS for loading state
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }
        .loading:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin-top: -20px;
            margin-left: -20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `)
    .appendTo('head');

// Initialize when document is ready
$(document).ready(function() {
    // You might want to automatically load data when page loads
    // applyScholarshipFilter();
    
    // Apply theme styling to buttons
    $('.btn').addClass('theme-btn');
    
    // Add nice transition effect to all buttons
    $('.btn').on('mouseenter', function() {
        $(this).css('transform', 'translateY(-2px)');
    }).on('mouseleave', function() {
        $(this).css('transform', 'translateY(0)');
    });
    
    // Close alert when clicking the close button
    $(document).on('click', '.alert .close', function() {
        $(this).parent().parent().hide();
    });
});

// Add JavaScript function for reset button
function resetFilters() {
    // Clear all combobox selections
    $('#ad_sess_id').combobox('clear');
    $('#ad_school_id').combobox('clear');
    $('#admission_sch').combobox('clear');
    $('#scholarship_type').combobox('clear');
    $('#scholarship_filter_type').combobox('clear');
    $('#selected_filter_type').combobox('clear');
    $('#status_filter_type').combobox('clear');
    
    // Clear the table if it exists
    if (scholarshipTable) {
        scholarshipTable.clear().draw();
    }
}

// Add this function for exporting all data
function exportTableToExcel() {
    // Get current filter parameters
    const data = {
        ad_sess_id: $('#ad_sess_id').combobox('getValue'),
        ad_school_id: $('#ad_school_id').combobox('getValue'),
        admission_sch: $('#admission_sch').combobox('getValue'),
        scholarship_type: $('#scholarship_type').combobox('getValue'),
        scholarship_filter_type: $('#scholarship_filter_type').combobox('getValue'),
        selected_filter_type: $('#selected_filter_type').combobox('getValue'),
        status_filter_type: $('#status_filter_type').combobox('getValue'),
        export_all: 'true'
    };
    
    // Redirect to a page that will generate the Excel file with all applicants
    window.open('scholarship_export_all.php?' + $.param(data), '_blank');
}

// Function to display validation message in the alert div
function showValidationMessage(message) {
    $('#validationMessage').text(message);
    $('#validationAlert').fadeIn();
    
    // Highlight the form panel
    $('.easyui-panel').addClass('highlight-animation');
    setTimeout(function() {
        $('.easyui-panel').removeClass('highlight-animation');
    }, 1000);
    
    // Scroll to the validation message
    $('html, body').animate({
        scrollTop: $('#validationAlert').offset().top - 100
    }, 200);
}

</script>
@stop

