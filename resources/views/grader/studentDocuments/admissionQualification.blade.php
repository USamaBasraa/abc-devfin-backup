@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')

<style>
    /* Figma-aligned presentation layer. Existing IDs, fields, data flow and JavaScript remain unchanged. */
    .student-doc-verification-page {
        min-height: calc(100vh - 20px);
        /* padding: 32px 26px 44px; */
        background: #f2f6f9;
        font-family: 'Montserrat', sans-serif;
    }

    .student-doc-verification-page .student-doc-shell {
        width: 100%;
        margin: 0;
    }

    .student-doc-verification-page .student-doc-card {
        margin: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    .student-doc-verification-page .student-doc-card > .card-header:first-child {
        padding: 0 0 18px 4px;
        border: 0;
        background: transparent;
        color: #0f172a;
    }

    .student-doc-verification-page .student-doc-card > .card-header:first-child .card-title {
        margin: 0;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.35;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .student-doc-verification-page .student-doc-card > .card-header:first-child .card-title::after {
        content: 'Verify applicant documents and admission records';
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.45;
        font-weight: 400;
        letter-spacing: 0;
    }

    .student-doc-verification-page .student-doc-card > .card-body {
        padding: 32px !important;
        border: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .student-doc-verification-page #messageForm {
        margin: 0;
    }

    .student-doc-verification-page #messageForm .student-doc-fields-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .student-doc-verification-page #messageForm .form-group.row {
        display: block;
        margin: 0 !important;
    }

    .student-doc-verification-page #messageForm .form-group.row > [class*='col-lg-'] {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        padding: 0;
        text-align: left !important;
    }

    .student-doc-verification-page #messageForm label {
        display: block;
        margin: 0 0 7px;
        padding: 0;
        color: #475569;
        font-size: 13px;
        line-height: 1.4;
        font-weight: 600;
    }

    .student-doc-verification-page #messageForm .form-select {
        width: 100% !important;
        height: 44px;
        min-height: 44px;
        padding: 9px 38px 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #fff;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.5;
        box-shadow: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .student-doc-verification-page #messageForm .form-select:focus {
        border-color: #17675f;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.10);
    }

    /* Preserve the original empty structural header/footer without adding visual space. */
    .student-doc-verification-page .student-doc-card > .student-doc-spacer-header,
    .student-doc-verification-page .student-doc-card > .card-footer {
        min-height: 0;
        height: 0;
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent;
    }

    .student-doc-verification-page #dTable:not(:empty) {
        margin-top: 24px;
        padding: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .student-doc-verification-page #dTable > .table-responsive.container {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
        overflow-x: auto;
    }

    .student-doc-verification-page #dTable .card-title {
        margin: 0;
        padding: 22px 24px 16px;
        color: #0f172a;
        font-size: 18px;
        line-height: 1.35;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }

    .student-doc-verification-page #dTable .card-title + br {
        display: none;
    }

    .student-doc-verification-page #studentTable,
    .student-doc-verification-page #studentTable.dataTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff;
        color: #0f172a;
        font-size: 13px;
    }

    .student-doc-verification-page #studentTable thead,
    .student-doc-verification-page #studentTable thead.bg-primary,
    .student-doc-verification-page #studentTable.table-primary thead {
        background: #17675f !important;
    }

    .student-doc-verification-page #studentTable thead th {
        padding: 12px 14px !important;
        border: 0 !important;
        border-bottom: 1px solid #17675f !important;
        background: #17675f !important;
        color: #fff !important;
        font-size: 11px !important;
        line-height: 1.35;
        font-weight: 600 !important;
        text-transform: uppercase;
        white-space: nowrap;
        vertical-align: middle;
    }

    .student-doc-verification-page #studentTable tbody td {
        padding: 13px 14px !important;
        border: 0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        vertical-align: middle;
    }

    .student-doc-verification-page #studentTable tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    .student-doc-verification-page #studentTable tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .student-doc-verification-page #studentTable .btn-outline-primary {
        min-height: 36px;
        padding: 7px 12px;
        border: 1px solid #17675f !important;
        border-radius: 8px;
        background: #fff !important;
        color: #17675f !important;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 600;
        box-shadow: none !important;
        white-space: nowrap;
    }

    .student-doc-verification-page #studentTable .btn-outline-primary:hover,
    .student-doc-verification-page #studentTable .btn-outline-primary:focus {
        background: #17675f !important;
        color: #fff !important;
    }

    /* Original missing-document visual indicator retained, recolored to the Figma palette. */
    .student-doc-verification-page .btn-blink {
        background-color: transparent;
        border: 2px solid #17675f !important;
        cursor: pointer;
        animation: border-blink-animation 1s infinite;
        transition: all 0.3s ease;
    }

    @keyframes border-blink-animation {
        0%, 100% { border-color: #17675f; }
        50% { border-color: transparent; }
    }

    /* DataTables controls */
    .student-doc-verification-page #studentTable_wrapper {
        width: 100%;
        padding: 18px 24px 22px;
    }

    .student-doc-verification-page #studentTable_wrapper .row {
        margin-left: 0;
        margin-right: 0;
    }

    .student-doc-verification-page #studentTable_wrapper .row > [class*='col-'] {
        padding-left: 0;
        padding-right: 0;
    }

    .student-doc-verification-page #studentTable_wrapper .dataTables_length label,
    .student-doc-verification-page #studentTable_wrapper .dataTables_filter label,
    .student-doc-verification-page #studentTable_wrapper .dataTables_info {
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
    }

    .student-doc-verification-page #studentTable_wrapper .dataTables_filter input,
    .student-doc-verification-page #studentTable_wrapper .dataTables_length select {
        height: 38px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #0f172a;
        box-shadow: none;
    }

    .student-doc-verification-page #studentTable_wrapper .dataTables_filter input {
        min-width: 240px;
        padding: 8px 12px;
        margin-left: 8px;
    }

    .student-doc-verification-page #studentTable_wrapper .dataTables_paginate .paginate_button,
    .student-doc-verification-page #studentTable_wrapper .pagination .page-link {
        min-width: 34px;
        height: 34px;
        margin-left: 4px;
        padding: 7px 10px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 7px !important;
        background: #fff !important;
        color: #475569 !important;
        font-size: 12px;
        box-shadow: none !important;
    }

    .student-doc-verification-page #studentTable_wrapper .dataTables_paginate .paginate_button.current,
    .student-doc-verification-page #studentTable_wrapper .dataTables_paginate .paginate_button:hover,
    .student-doc-verification-page #studentTable_wrapper .pagination .page-item.active .page-link,
    .student-doc-verification-page #studentTable_wrapper .pagination .page-link:hover {
        border-color: #17675f !important;
        background: #17675f !important;
        color: #fff !important;
    }

    @media (max-width: 991.98px) {
        .student-doc-verification-page {
            padding: 24px 16px 34px;
        }

        .student-doc-verification-page #messageForm .student-doc-fields-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .student-doc-verification-page .student-doc-card > .card-body {
            padding: 22px !important;
        }
    }

    @media (max-width: 575.98px) {
        .student-doc-verification-page {
            padding: 20px 12px 28px;
        }

        .student-doc-verification-page .student-doc-card > .card-body {
            padding: 18px !important;
        }

        .student-doc-verification-page #studentTable_wrapper .dataTables_filter input {
            width: 100%;
            min-width: 0;
            margin: 8px 0 0;
        }
    }

    /* Pagination - Figma style */
.student-doc-verification-page #studentTable_wrapper .dataTables_paginate .pagination {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}

/* Outer LI ko bilkul plain rakho */
.student-doc-verification-page #studentTable_wrapper .dataTables_paginate .paginate_button,
.student-doc-verification-page #studentTable_wrapper .pagination .page-item {
    padding: 0 !important;
    margin: 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

/* Sirf actual link/button style hoga */
.student-doc-verification-page #studentTable_wrapper .pagination .page-link {
    min-width: 40px;
    height: 40px;
    padding: 0 12px !important;
    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #475569 !important;

    font-size: 13px;
    font-weight: 500;
    line-height: 1;
    box-shadow: none !important;
}

/* Active page */
.student-doc-verification-page #studentTable_wrapper .pagination .page-item.active .page-link {
    background: #17675f !important;
    border-color: #17675f !important;
    color: #ffffff !important;
}

/* Hover */
.student-doc-verification-page #studentTable_wrapper .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #17675f !important;
}

/* Disabled */
.student-doc-verification-page #studentTable_wrapper .pagination .page-item.disabled .page-link {
    background: #f8fafc !important;
    color: #94a3b8 !important;
    cursor: default;
}
</style>

<div class="student-doc-verification-page">
    <div class="row student-doc-shell">
        <div class="col-lg-12 col-md-12 p-0">
            <div class="card student-doc-card">
                <div class="card-header">
                    <h3 class="card-title"> Student Documents Verification </h3>
                </div>

                <input type="hidden" id="session">
                <input type="hidden" id="school">
                <input type="hidden" id="department">
                <input type="hidden" id="program">

                <div class="card-body pb-2">
                    <form id="messageForm" method="post">
                        <div class="student-doc-fields-grid">
                            <!-- Enrollment Session Dropdown -->
                            <div class="form-group row" style="margin-bottom:0px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">Enrollment Session</label>
                                </div>
                                <div class="col-lg-9">
                                    <select id="facultyWload_sess_id" name="sess_id" class="form-select" required>
                                        <option value="">Select Session</option>
                                    </select>
                                </div>
                            </div>

                            <!-- School Dropdown -->
                            <div class="form-group row" style="margin-bottom:0px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">School</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="facultyWload_school_id" name="school_id" class="form-select" required>
                                        <option value="">Select School</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Department Dropdown -->
                            <div class="form-group row" style="margin-bottom:0px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">Department</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="department_id" name="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Program Dropdown -->
                            <div class="form-group row" style="margin-bottom:0px;">
                                <div class="col-lg-3" style="text-align: right">
                                    <label class="col-form-label">Program</label>
                                </div>
                                <div class="col-lg-8">
                                    <select id="program_id" name="program_id" class="form-select" required>
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-header student-doc-spacer-header" style="display:inline">
                    <div style="float: right">
                    </div>
                </div>

                <div id='dTable'></div>

                <div class="card-footer">
                </div>
            </div>
        </div>
    </div>
</div>
@stop



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



<script type="text/javascript">



    var baseUrl = window.location.protocol + "//" + window.location.host;

    // Load Sessions and Schools on page load
    $(document).ready(function () {
        loadSessions();
        loadSchools();

        // Function to load sessions
        function loadSessions() {
            $.ajax({
                url: '/api/get-academic-sessions',
                type: 'GET',
                data: {
                    api_token: '{{ auth()->user()->api_token }}'
                },
                success: function (data) {
                    var select = $('#facultyWload_sess_id');
                    select.empty().append('<option value="">Select Session</option>');
                    data.forEach(function (item) {
                        select.append($('<option></option>')
                            .attr('value', item.sess_id)
                            .text(item.sess_name));
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error loading sessions:', error);
                }
            });
        }

        // Function to load schools
        function loadSchools() {
            $.ajax({
                url: '/api/get-academic-sessions-schools',
                type: 'GET',
                data: {
                    api_token: '{{ auth()->user()->api_token }}'
                },
                success: function (data) {
                    var select = $('#facultyWload_school_id');
                    select.empty().append('<option value="">Select School</option>');
                    data.forEach(function (item) {
                        select.append($('<option></option>')
                            .attr('value', item.school_id)
                            .text(item.school_name));
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error loading schools:', error);
                }
            });
        }

        // Session Change Event
        $('#facultyWload_sess_id').change(function () {
            var sessId = $(this).val();
            $('#session').val(sessId);

            // Clear dependent dropdowns
            $('#facultyWload_school_id').empty().append('<option value="">Select School</option>');
            $('#department_id').empty().append('<option value="">Select Department</option>');
            $('#program_id').empty().append('<option value="">Select Program</option>');

            // Load schools
            if (sessId) {
                $.ajax({
                    url: '/api/get-academic-sessions-schools',
                    type: 'GET',
                    data: { sess_id: sessId, api_token: '{{ auth()->user()->api_token }}' },
                    success: function (data) {
                        var select = $('#facultyWload_school_id');
                        data.forEach(function (item) {
                            select.append($('<option></option>')
                                .attr('value', item.school_id)
                                .text(item.school_name));
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading schools:', error);
                    }
                });
            }
            getEnrollmentStatus();
        });

        // School Change Event
        $('#facultyWload_school_id').change(function () {
            var schoolId = $(this).val();
            $('#school').val(schoolId);

            console.log('School selected:', schoolId); // Debug log

            // Clear dependent dropdowns
            $('#department_id').empty().append('<option value="">Select Department</option>');
            $('#program_id').empty().append('<option value="">Select Program</option>');

            // Load departments using only school_id
            if (schoolId) {
                console.log('Loading departments for school:', schoolId); // Debug log
                $.ajax({
                    url: '/api/get-academic-sessions-departments/' + schoolId,
                    type: 'GET',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}'
                    },
                    success: function (data) {
                        console.log('Departments loaded:', data); // Debug log
                        var select = $('#department_id');
                        data.forEach(function (item) {
                            select.append($('<option></option>')
                                .attr('value', item.dept_id)
                                .text(item.dept_name));
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading departments:', error);
                        console.error('Response:', xhr.responseText); // Debug log
                    }
                });
            }
            getEnrollmentStatus();
        });

        // Department Change Event
        $('#department_id').change(function () {
            var deptId = $(this).val();
            var schoolId = $('#facultyWload_school_id').val();
            $('#department').val(deptId);

            console.log('Department selected:', deptId); // Debug log
            console.log('School ID for programs:', schoolId); // Debug log

            // Clear program dropdown
            $('#program_id').empty().append('<option value="">Select Program</option>');

            // Load programs using dept_id and school_id
            if (deptId) {
                console.log('Loading programs for dept:', deptId, 'and school:', schoolId); // Debug log
                $.ajax({
                    url: '/api/get-academic-sessions-programs/' + schoolId + '/' + deptId,
                    type: 'GET',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}'
                    },
                    success: function (data) {
                        console.log('Programs loaded:', data); // Debug log
                        var select = $('#program_id');
                        data.forEach(function (item) {
                            select.append($('<option></option>')
                                .attr('value', item.prog_id)
                                .text(item.prog_name));
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading programs:', error);
                        console.error('Response:', xhr.responseText); // Debug log
                    }
                });
            }
            getEnrollmentStatus();
        });

        // Program Change Event
        $('#program_id').change(function () {
            $('#program').val($(this).val());
            getEnrollmentStatus();
        });


        function getEnrollmentStatus() {
            // Show the loading spinner
            $('#loading-spinner').show();

            // Get selected session, school, department, and program values
            var sess = $('#session').val();
            var school = $('#school').val();
            var dept = $('#department').val();
            var prog = $('#program').val();

            // Send the GET request to fetch applicant information
            $.ajax({
                url: '<?= url('/api/get-applicant-admission-information') ?>',
                type: 'GET',
                data: {
                    session: sess,
                    school: school,
                    department: dept,
                    program: prog,
                    api_token: '{{ auth()->user()->api_token }}'
                },
                success: function (res) {
                    // Hide the loading spinner once the data is loaded
                    $('#loading-spinner').hide();

                    // Log the response to check what is being returned
                    console.log('Response:', res);

                    // Separate rows for upload_missing_document = 1 and upload_missing_document = 0
                    let trueRows = [];
                    let falseRows = [];

                    // Process rows and divide into true/false arrays
                    $(res).each(function (index, row) {
                        var upload_missing_document = $.trim(row.upload_missing_document);
                        var buttonClass = upload_missing_document === '1' ? 'btn-blink' : '';


                        var progId = row.cms_prog_id ? row.cms_prog_id : row.aai_sdpo_prog_id;
                        var rowHTML = `
                    <tr  mb-2">
                        <td>${row.api_id}</td>
                        <td>${row.aai_id}</td>
                        <td>${row.cms_sai_enrollment_no}</td>
                        <td>${row.uemail}</td>
                        <td>${row.api_name}</td>
                        <td>${row.aai_programid}</td>
                        <td>${row.api_nic}</td>
            <td><button class="btn btn-outline-primary ${buttonClass}" onclick="verifyDocs(${row.api_id}, ${progId}, ${row.aai_sdpo_sess_id})"><i class="fa fa-check-circle"></i> Verify Docs</button></td>
                    </tr>
                `;

                        // Add to trueRows if upload_missing_document is '1', otherwise add to falseRows
                        if (upload_missing_document === '1') {
                            trueRows.push(rowHTML); // Add to trueRows (1)
                        } else {
                            falseRows.push(rowHTML); // Add to falseRows (0)
                        }
                    });

                    // Combine trueRows (upload_missing_document = 1) first, then falseRows (upload_missing_document = 0)
                    var tableHTML = `
                <div class="table-responsive container">
                    <h3 class="card-title">Student Documents Verification List</h3>
                    <br>
                    <table class="table card-table table-primary mb-0" id="studentTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-white">Profile ID</th>
                                <th class="text-white">Applicant ID</th>
                                <th class="text-white">Enrollment No#</th>
                                <th class="text-white">Email</th>
                                <th class="text-white">Name</th>
                                <th class="text-white">Program</th>
                                <th class="text-white">Student CNIC</th>
                                <th class="text-white">Verify Docs</th>
                            </tr>
                        </thead>
                        <tbody id="noti-msg-tbody">
            `;

                    // Append trueRows first (those with upload_missing_document = 1)
                    tableHTML += trueRows.join('');

                    // Then append falseRows (those with upload_missing_document = 0)
                    tableHTML += falseRows.join('');

                    tableHTML += '</tbody></table></div>';

                    // Append the generated table HTML to the #dTable div
                    $('#dTable').html(tableHTML);

                    // Initialize DataTable without sorting enabled
                    if ($.fn.DataTable.isDataTable('#studentTable')) {
                        $('#studentTable').DataTable().clear().destroy(); // Clear and destroy previous DataTable
                    }
                    $('#studentTable').DataTable({
                        "responsive": true,
                        "ordering": false // Disable sorting to maintain the original order
                    });

                    // Log the count of rows with upload_missing_document = 1 and 0
                    console.log('Rows with upload_missing_document = 1:', trueRows.length);
                    console.log('Rows with upload_missing_document = 0:', falseRows.length);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                    $('#loading-spinner').hide();
                }
            });
        }


    });


    function verifyDocs(apiId, progId, sessId) {

        // Construct the new URL
        var newLink = baseUrl + "/doc-verifications?api_id=" + apiId + "&prog_id=" + progId + "&sess_id=" + sessId;

        // Open the link in a new tab
        window.open(newLink, "_blank");
    }

</script>