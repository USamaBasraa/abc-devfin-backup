@extends('layouts.app')

@section('title')
  BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
<style>
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
        .edit-btn, .delete-btn{
            width:76px;
        }
        .error-flag {
            position: fixed;
            top: 170px;
            right: -300px;
            background-color: #f44336; /* Red color for error */
            color: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: right 0.5s;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }
        .error-flag.show {
            right: 0px;
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
        .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
    }
    /* //INFO BOXES */
    /* Shared Info Box */
    .info-box {
        height: 100%;
    }

    .info-inner {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8f9fc;
        padding: 14px;
        border-radius: 10px;
        border: 1px solid #e3e6f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }

    .info-inner:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        border-color: #0d6efd;
    }

    .info-inner i {
        font-size: 1.2rem;
        color: #0d6efd;
        min-width: 22px;
        text-align: center;
    }

    .info-inner small {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .info-inner strong {
        font-size: 0.9rem;
        color: #212529;
        font-weight: 600;
    }

    /* Grid breathing room (optional) */
    .student-info-grid,
    .challan-info-grid {
        margin-left: 0;
        margin-right: 0;
    }
/* LOADER STYLING */
/* Page Loader Overlay */
.page-loader {
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(3px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Loader Content */
.loader-content {
    text-align: center;
    padding: 24px 32px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

/* Smooth appearance */
.page-loader.show {
    display: flex !important;
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

</style>
</head>

@section('content')
<div id="success-flag" class="success-flag"></div>
<div id="error-flag" class="error-flag"></div>
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <div class="row"><h3>Middleware Challan Rectification:</h3></div>
      <form id="searchForm" class="my-3">
            <div class="row align-items-end">
                <!-- Challan No Input -->
                <div class="col-4 col-md-">
                    <label for="studentChallan" class="form-label">Search Challan No.</label>
                    <div class="input-group">
                        <input
                            placeholder="Enter Challan number"
                            class="form-control"
                            type="text"
                            name="studentChallanNo"
                            id="studentChallan"
                            onblur="getVersions()"
                            onkeypress="enterPressGetVersion()"
                        >
                    </div>
                    <small class="searchChallan-disclaimer form-text"></small>
                    <div id="searchError_challanmessage" class="alert alert-danger mt-2" style="display:none;"></div>
                </div>

                <!-- Version Dropdown -->
                <div class="col-4 col-md-4">
                    <label for="version-dropdown" class="form-label">Select Version</label>
                    <div class="input-group">
                        <select class="form-control" id="version-dropdown" name="version"></select>
                    </div>
                    <small class="searchChallan-disclaimer form-text"></small>
                    <div id="searchError_versionmessage" class="alert alert-danger mt-2" style="display:none;"></div>
                </div>

                <!-- Search Button -->
                <div class="col-4 col-md-4 d-grid">
                    <button onclick="getVersionData()" disabled id='searchButton' type="button" class="btn btn-info">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
        <div id="profileDiv"></div>

        <div id="challanInfoDiv" class="d-none"></div>

        <div id="scholarshipInfoDiv"></div>

        <div class="modal fade" id="editScholarshipModal" tabindex="-1" role="dialog" aria-labelledby="editScholarshipModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <form id="editScholarshipForm">
                    <div class="modal-header">
                    <h5 class="modal-title" id="editScholarshipModalLabel">Edit Scholarship</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-main-id" name="id">
                        <input type="hidden" id="edit-version" name="version">
                        <input type="hidden" id="edit-challan-no" name="challan_no">
                        <input type="hidden" id="edit-scholarship-id" name="scholarship_id">
                        <div class="form-group">
                            <label for="edit-percentage">Discount Percentage</label>
                            <input type="number" class="form-control" id="edit-percentage" name="percentage" min="0" max="100" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-amount">Discounted Amount</label>
                            <input type="number" class="form-control" id="edit-amount" name="amount" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <div id="scholarship-error" class="alert alert-danger d-none"></div>
                    </div>
                </form>
                </div>
            </div>
        </div>
</div>
<!-- Global Page Loader -->
<div id="pageLoader" class="page-loader d-none">
    <div class="loader-content">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <div class="fw-semibold text-primary">Loading data, please wait…</div>
    </div>
</div>
@stop

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

@section('javascript')
<script>
     var globalChallanData = '';
     var globalAdjustmentData = '';
$(document).ready(function(){
    $('#modal-1').on('hidden.bs.modal', function (e) {
        $('#form-1')[0].reset();
    });
    $('#editScholarshipForm').on('submit', function(e) {
        e.preventDefault();
        let mainId = $('#edit-main-id').val();
        let version = $('#edit-version').val();
        let challan_no = $('#edit-challan-no').val();
        let percentage = $('#edit-percentage').val();
        let amount = $('#edit-amount').val();
        let scholarship_id = $('#edit-scholarship-id').val();

        $.ajax({
            type: "POST",
            url: "/api/challan-rectification/edit-scholarhsip",
            data: {
                id: mainId,
                version: version,
                challan_no: challan_no,
                percentage: percentage,
                amount: amount,
                scholarship_id: scholarship_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editScholarshipModal').modal('hide');
                $('#success-flag').text('Saved Successfully');
                $('#success-flag').addClass('show');
                getVersionData();
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000);
                // LoadFee(); // or reload scholarship table as needed
            },
            error: function(xhr) {
                let msg = "Could not save changes. Please try again.";
                if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }
                $('#scholarship-error').text(msg).removeClass('d-none');
            }

        });
    });
});
function enterPressGetVersion() {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        getVersions();
    }

}
function getVersions() {
    let challan_no = $('#studentChallan').val();
    let version = $('#version-dropdown').val();

    showLoader();

    fetch('/api/challan-rectification/get-challan-ver', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            challan_no: challan_no,
            version: version
        })
    })
    .then(res => res.json())
    .then(({ success, challanData }) => {

        if (success) {
            let $dropdown = $('#version-dropdown');
            $dropdown.empty();

            challanData.sort((a, b) => {
                let vA = isNaN(a.version) ? a.version : Number(a.version);
                let vB = isNaN(b.version) ? b.version : Number(b.version);
                return vA > vB ? 1 : -1;
            });

            let options = '';
            challanData.forEach(item => {
                if (item.version && item.challan_no) {
                    options += `<option value="${item.challan_no}-${item.version}">
                        Version ${item.version}
                    </option>`;
                }
            });

            $dropdown.html(options);
            $('#searchButton').prop('disabled', false);
            $("#error_message").hide();

        } else {
            $('#searchError_challanmessage')
                .text('No data found against challan no.')
                .fadeIn().fadeOut(5000);
        }
    })
    .catch(() => {
        alert('Something went wrong while loading versions.');
    })
    .finally(() => {
        hideLoader();
    });
}
function getVersionData() {
    let challan_no = $('#studentChallan').val();
    let version = $('#version-dropdown').val();

    showLoader();

    fetch('/api/challan-rectification/get-version-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            challan_no: challan_no,
            version: version
        })
    })
    .then(res => res.json())
    .then(({ success, challanData, scholarshipData }) => {

        if (success) {
            makeProfileHtml(challanData);
            makeChallanHtml(challanData);
            renderScholarshipTable(scholarshipData);
            $("#error_message").hide();
        } else {
            $('#searchError_challanmessage')
                .text('No data found against challan no.')
                .fadeIn().fadeOut(5000);
        }
    })
    .catch(() => {
        alert('Something went wrong while loading challan data.');
    })
    .finally(() => {
        hideLoader();
    });
}
function search(searchBy) {
    // loadFeeAdjustments();
    $('.filterbutton').html('<i class="fa fa-filter"></i> Filter <span class="filter-bubble">ALL</span>')
    var valid = true;
    valid = validateSearchBar(searchBy, valid);//this function will make valid variable false if fields are empty
    if(valid){
        let form = document.getElementById('searchForm');
        let data = new FormData(form);
        let challNo = $('#studentChallan').val();
        let enrollmentNo = $('#studentEnrollment').val();
        if(searchBy == 'challan'){
            $('#data-tableAdjustment').DataTable().search($('#studentChallan').val()).draw();
            fetch('/api/feeadjustment/get-challan-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, challanData, adjustmentData }) => { // Correct destructuring syntax
                if (success) {
                    //console.log(success);
                    makeChallanDataHtml(challanData, adjustmentData);
                    $("#error_message").text('').hide();
                }
                else{
                 //   console.log(success);
                    // $('#success_message').text('').hide();
                        $('#searchError_challanmessage').html('No Data found against challan no. '+challNo);
                        $('#searchError_challanmessage').fadeIn('slow');
                        $('#searchError_challanmessage').fadeOut(5000);
                // const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
                // const message = usernameError;
                // const alertHTML = createAlert(notificationType, message);
                // dataDiv.innerHTML = alertHTML;
                    // return false;
                }
            })
        }else if(searchBy == 'enrollment'){

            $('#data-tableAdjustment').DataTable().search($('#studentEnrollment').val()).draw();
            fetch('/api/feeadjustment/get-enrollment-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, enrollmentData, adjustmentData, newAdjustmentData }) => { // Correct destructuring syntax
                if (success) {
                   console.log(enrollmentData);
                   /////////////
                   const checkbox = document.getElementById('adjustmentCheckbox');
                    if (checkbox.checked) {
                        addNewFeeAdjustment(adjustmentData, newAdjustmentData);
                    } else {
                        makeEnrollmentDataHtml(enrollmentData, adjustmentData);
                        $("#error_message").text('').hide();
                    }
                    ////////

                }
                else{
                   // console.log(success);
                    // $('#success_message').text('').hide();
                        $('#searchError_enrollmessage').html('The student is not active against this enrollment number. '+enrollmentNo);
                        $('#searchError_enrollmessage').fadeIn('slow');
                        $('#searchError_enrollmessage').fadeOut(5000);
                // const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
                // const message = usernameError;
                // const alertHTML = createAlert(notificationType, message);
                // dataDiv.innerHTML = alertHTML;
                    // return false;
                }
            })
        }

    }

}
function makeProfileHtml(data) {
    const dataDiv = document.getElementById('profileDiv');

    if (!dataDiv) {
        console.error('profileDiv not found in DOM');
        return;
    }

    dataDiv.innerHTML = '';

    if (data && data.usernameError) {
        dataDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i>
                ${data.usernameError}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        return;
    }

    if (!data) {
        dataDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i>
                No data available
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        return;
    }

    /* ---------- Image Logic ---------- */
    let imageUrl = data.api_imagename
        ? `https://admission.bnu.edu.pk/images/${data.api_imagename}`
        : `https://cms.bnu.edu.pk/students_pics/${(data.regno || '').toUpperCase()}.jpg`;

    /* ---------- Profile Card ---------- */
    let profileHtml = `
    <div class="card shadow-sm border-0">
        <div class="card-header custom-card-header d-flex align-items-center p-2">
            <i class="fa fa-user mr-2" style="font-size:1.3rem;"></i>
            <span class="h4 mb-0">Student Information</span>
        </div>

        <div class="card-body p-3">
            <div class="row align-items-start">

                <!-- Student Image -->
                <div class="col-md-2 text-center mb-3">
                    <img
                        src="${imageUrl}"
                        alt="Student Image"
                        class="img-fluid rounded-circle shadow-sm mb-2"
                        style="width:90px;height:90px;object-fit:cover;"
                        onerror="this.src='https://cms.bnu.edu.pk/students_pics/default.jpg';"
                    >
                    <h6 class="text-uppercase fw-bold text-primary mb-0">
                        ${toTitleCase(data.studentname) || 'N/A'}
                    </h6>
                </div>

                <!-- Info Grid -->
                <div class="col-md-10">
                    <div class="row g-3 student-info-grid">
                        ${generateStudentInfoBox('fa-id-card', 'Applicant ID', data.app_id || 'N/A')}
                        ${generateStudentInfoBox('fa-id-badge', 'Enrollment No', data.regno || 'N/A')}
                        ${generateStudentInfoBox('fa-user', 'Student Name', toTitleCase(data.studentname) || 'N/A')}
                        ${generateStudentInfoBox('fa-user', 'Father Name', toTitleCase(data.fathername) || 'N/A')}
                        ${generateStudentInfoBox('fa-calendar', 'Session Code', data.sess_code || 'N/A')}
                        ${generateStudentInfoBox('fa-building', 'School', data.schl_name || 'N/A')}
                        ${generateStudentInfoBox('fa-university', 'Department', data.dept_name || 'N/A')}
                        ${generateStudentInfoBox('fa-book', 'Program', data.prog_name || 'N/A')}
                    </div>
                </div>

            </div>
        </div>

    </div>`;

    dataDiv.innerHTML = profileHtml;
}
function makeChallanHtml(challanData) {
    console.log(challanData);
    const container = document.getElementById('challanInfoDiv');
    if (!container) {
        console.error('challanInfoDiv not found in DOM');
        return;
    }
    let val = $('#version-dropdown').val();   // "44729-0"
    let version = val.split('-')[1];

    container.classList.remove('d-none');
    container.innerHTML = '';

    if (!challanData) {
        container.innerHTML = `
            <div class="alert alert-danger">No challan data available</div>`;
        return;
    }

    const createdAt = challanData.created_at
        ? challanData.created_at.split(' ')[0]
        : 'N/A';

    const staffUrl = "{{ config('app.staff_url') }}";
    const challanUrl = `${staffUrl}/rectifiedChalanfin.php?challan_id=${challanData.challan_no}&version=${version}`;


    const html = `
        <div class="card shadow-sm border-0 mt-4">

            <!-- Card Header -->
            <div class="card-header custom-card-header d-flex align-items-center p-2">
                <i class="fa fa-money mr-2" style="font-size:1.3rem;"></i>
                <span class="h4 mb-0">Challan Information</span>
                 <!-- Preview Button -->
                <div class="d-flex justify-content-end mb-3" style="margin:10px;">
                    <a href="${challanUrl}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fa fa-eye me-1"></i> Preview
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">

                <!-- Info Grid -->
                <div class="row g-3 challan-info-grid">
                    ${generateChallanInfoBox('fa-money', 'Challan No', challanData.challan_no ?? 'N/A')}
                    ${generateChallanInfoBox('fa-id-badge', 'Enrollment No', challanData.regno ?? 'N/A')}
                    ${generateChallanInfoBox('fa-id-card', 'Applicant ID', challanData.app_id ?? 'N/A')}
                    ${generateChallanInfoBox('fa-money', 'Total Amount', challanData.invoice_amount ?? 'N/A')}
                    ${generateChallanInfoBox('fa-info-circle', 'Challan Type', challanData.challan_type ?? 'N/A')}
                    ${generateChallanInfoBox('fa-info-circle', 'Status', challanData.challan_status ?? 'N/A')}
                    ${generateChallanInfoBox('fa-calendar', 'Created At', createdAt)}
                </div>

            </div>

        </div>`;

    container.innerHTML = html;
}
function renderScholarshipTable(scholarshipData) {
    let container = document.getElementById('scholarshipInfoDiv');
    container.innerHTML = ''; // Clear old content

    if (!Array.isArray(scholarshipData) || scholarshipData.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info my-2">No scholarship info found.</div>
        `;
        return;
    }

    let html = `
        <div class="card mt-3 shadow-sm border-0">
            <div class="card-header custom-card-header d-flex align-items-center p-2">
                <i class="fa fa-graduation-cap mr-2" style="font-size: 1.5rem;"></i>
                <span class="h4 mb-0">Scholarship Info</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Scholarship Name</th>
                                <th>Discount Percentage</th>
                                <th>Discounted Amount</th>
                                <th>Date</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

    scholarshipData.forEach((item, idx) => {
        let entryDate = item.entry_datetime ? item.entry_datetime.split(' ')[0] : '';
        let id = item.id || '';
        let version = item.version || '';
        let challan_no = item.challan_no || '';

        html += `
            <tr>
                <td>${idx + 1}</td>
                <td>${item.scholarship_name || 'N/A'}</td>
                <td>${
                    item.fsd_discount_percentage !== null &&
                    item.fsd_discount_percentage !== undefined
                        ? (parseFloat(item.fsd_discount_percentage) * 100).toFixed(2)

                        : 'N/A'
                }</td>

                <td>${item.fsd_discounted_amount || 'N/A'}</td>
                <td>${entryDate}</td>
                <td>
                    <button class="btn btn-sm btn-warning"
                    onclick="editScholarhship(
                        '${id}',
                        '${version}',
                        '${challan_no}',
                        '${
                            item.fsd_discount_percentage !== null &&
                            item.fsd_discount_percentage !== undefined
                                ? (parseFloat(item.fsd_discount_percentage) * 100).toFixed(2)

                                : ''
                        }',
                        '${item.fsd_discounted_amount}',
                        '${item.fsd_scholarship_type}'
                    )">
                <i class="fa fa-edit"></i> Edit
                </button>

                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;

                    container.innerHTML = html;
}
function generateStudentInfoBox(icon, label, value) {
    return `
    <div class="col-lg-3 col-md-4 col-sm-6" style="margin-top:15px;">
        <div class="info-box h-100">
            <div class="info-inner h-100">
                <i class="fa ${icon}"></i>
                <div>
                    <small>${label}</small>
                    <strong>${value}</strong>
                </div>
            </div>
        </div>
    </div>`;
}
function generateChallanInfoBox(icon, label, value) {
    return `
    <div class="col-lg-3 col-md-4 col-sm-6" style="margin-top:15px;">
        <div class="info-box h-100">
            <div class="info-inner h-100 align-items-start">
                <i class="fa ${icon}"></i>
                <div>
                    <small>${label}</small>
                    <strong>${value}</strong>
                </div>
            </div>
        </div>
    </div>`;
}
function toTitleCase(str) {
    if (!str) return 'N/A';
    return str
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}
function editScholarhship(id, version, challan_no, percentage, amount, scholarship_id) {
    // Fill the modal fields
    // console.log(percentage);
    // console.log(amount);
    $('#scholarship-error').text('').addClass('d-none');
    $('#edit-main-id').val(id);
    $('#edit-version').val(version);
    $('#edit-challan-no').val(challan_no);
    $('#edit-percentage').val(percentage);
    $('#edit-amount').val(amount);
    $('#edit-scholarship-id').val(scholarship_id);

    // Show the modal
    $('#editScholarshipModal').modal('show');
}
function showLoader() {
    $('#pageLoader').removeClass('d-none').addClass('show');
}
function hideLoader() {
    $('#pageLoader').removeClass('show').addClass('d-none');
}


</script>
@stop
