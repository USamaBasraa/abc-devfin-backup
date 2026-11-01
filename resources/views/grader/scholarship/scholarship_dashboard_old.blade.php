@extends('layouts.app')

@section('content')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    .dashboard-page h6 {
        font-weight: 600;
    }
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .bar {
        width: 12%;
        border-radius: 6px;
    }
    .progress-circle {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(#198754 41%, #e9ecef 0);
        margin: 0 auto;
    }
    .progress-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
        font-size: 20px;
    }
    .form-floating label i {
        color: #198754;
    }
    .form-floating select {
        border-radius: 10px;
    }
    .stat-card .bg-opacity-10 {
        transition: background 0.3s ease;
    }
    .stat-card .bg-opacity-10:hover {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 10px 10px rgba(0,0,0,0.05);
    }
    .child-card {
        cursor: pointer;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .selected-child-card {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 10px 10px rgba(0,0,0,0.05);
    }
    /* scholarship table */
    .card h6 {
        font-weight: 600;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .table td, .table th {
        padding: 0.75rem;
    }
    /* Center checkbox */
    .table td.text-center {
        vertical-align: middle;
    }

   /* Center checkbox perfectly */
    .check-cell {
        position: relative;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        padding: 0; /* clean look */
    }

    /* Make checkbox centered, larger, and remove Bootstrap margins */
    .check-cell .styled-checkbox {
        position: relative;
        transform: scale(1.5);
        accent-color: var(--bs-primary);
        margin: 0;
        cursor: pointer;
    }

    /* Optional: hover feedback */
    .check-cell:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    /* multi select */
    /* Allow multiple selected items to wrap instead of hide */
    .select2-selection--multiple {
        height: auto !important;
        min-height: 38px;
        overflow: visible !important;
        display: flex;
        flex-wrap: wrap;
    }
    /* Keep fixed height with scroll */
    .select2-selection--multiple {
        max-height: 80px !important;
        overflow-y: auto !important;
    }
    /* Container for selected scholarship bubbles */
#selected-scholarships {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem; /* adds nice space between bubbles */
    margin-top: 0.5rem;
}

/* Individual bubble (pill) style */
#selected-scholarships .badge.rounded-pill {
    background-color: var(--bs-primary);
    color: #fff;
    font-size: 0.85rem;
    padding: 0.45rem 0.75rem;
    display: flex;
    align-items: center;
    border-radius: 50px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}

/* Cross icon styling */
#selected-scholarships .badge.rounded-pill i {
    margin-left: 6px;
    cursor: pointer;
    font-size: 0.8rem;
    opacity: 0.85;
    transition: color 0.2s, opacity 0.2s;
}
/* Active Card */
/* Hover effect for cross */
#selected-scholarships .badge.rounded-pill i:hover {
    color: #ffc107;
    opacity: 1;
}
.stat-card-selectable {
    cursor: pointer;
    transition: all 0.3s ease;
}

.stat-card-selectable:hover {
    transform: translateY(-3px);
}
.active-card {
    border: 2px solid #198754;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background-color: #2dce8a;
    color: #fff;
}
/* Wider and taller modal */
.modal-xxl {
  max-width: 95% !important;  /* Almost full-screen width */
}

.modal-dialog-scrollable .modal-content {
  max-height: 90vh;           /* Limit to 90% of viewport height */
  overflow-y: auto;
}


</style>

<div class="container-fluid py-4 dashboard-page">
   <div class="card shadow-sm mb-4 border-0">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
            <h3 class="fw-bold mb-1">Scholarship Dashboard</h3>
            </div>
            </div>
             <!-- Filters Row -->
            <div class="row g-3 mb-3 p-4">
                <div class="col-md-2">
                    <div class="form-floating">
                        <label for="sessionSelect"><i class="bi bi-building me-1"></i>Session</label>
                        <select class="form-select form-control" id="session-select" name="session">
                            {{-- <option value={{ $sessionIds }} selected>All</option> --}}
                            @foreach($academic_session as $session)
                            <option value={{ $session->sess_id }} >{{ $session->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 d-none">
                    <div class="form-floating">
                        <label for="student-type"><i class="bi bi-calendar-event me-1"></i> Student Type</label>
                        <select class="form-select form-control" id="student-type-select" name="student_type">
                                <option value="Existing" selected>Existing Students</option>
                                <option value="New">New Students</option>
                        </select>

                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column">
                <div class="flex-grow-1">
                    <!-- Your filter fields go here -->
                </div>

                <button class="btn btn-outline-info d-flex align-items-center justify-content-center w-100 mt-auto" id="apply-filters-btn">
                    <i class="fa fa-filter me-2"></i> Apply Filters
                </button>
            </div>


            </div>
        </div>

    </div>
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card stat-card-selectable active-card  h-100" id="scholarship_card">
                <div class="card-body">
                    <h4>Total Scholarships Awarded</h4>
                    <h2 class="fw-bold mb-0" id="total-awarded">-</h2>
                    <small class='description'><i class="fa fa-arrow-up "></i>Includes both academic and hostel scholarships.</small>
                    <!-- Sub-cards section -->
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="p-2  child-card selected-child-card bg-opacity-10 academic-total rounded text-center" type_attribute="academic">
                                <h6 class="mb-1">Academic</h6>
                                <h4 class="fw-bold  mb-0 academic-total" id="academic-total-scholarships">-</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 child-card bg-opacity-10 hostel-total rounded text-center" type_attribute="hostel">
                                <h6 class="mb-1">Hostel</h6>
                                <h4 class="fw-bold  mb-0 hostel-total" id="hostel-total-scholarships">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-card-selectable h-100" id="holder_card">
                <div class="card-body">
                    <h4>Total Scholarship Holders</h4>
                    <h2 class="fw-bold mb-0" id="total-holders">-</h2>
                    <small class='description text-success'><i class="fa fa-arrow-up"></i>Students with one or multiple scholarship awards.</small>
                    <!-- Sub-cards section -->
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="p-2 child-card bg-opacity-10 academic-total rounded text-center" type_attribute="academic">
                                <h6 class="mb-1">Academic</h6>
                                <h4 class="fw-bold mb-0 academic-total" id="academic-total-students">-</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 child-card bg-opacity-10 hostel-total rounded text-center" type_attribute="hostel">
                                <h6 class="mb-1">Hostel</h6>
                                <h4 class="fw-bold mb-0 hostel-total" id="hostel-total-students">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-card-selectable h-100" id="onAlert_card">
                <div class="card-body">
                    <h4>Scholarship Holders On Alert</h4>
                    <h2 class="fw-bold mb-0" id="on-alert">-</h2>
                    <small class="description text-success"><i class="fa fa-arrow-up"></i>Students at verge of loosing scholarship</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Middle Section -->
   <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-12">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Scholarship select -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <label for="sch_type"><i class="bi bi-building me-1"></i>Scholarships</label>
                                <select class="custom-select form-control select2-show-search"
                                        name="sch_type[]"
                                        id="sch_type"
                                        multiple="multiple"
                                        data-placeholder="All Scholarships"
                                        style="width:100%;">
                                        <option value="">All Scholarships</option>
                                </select>
                                {{-- Hidden option sets for Acadenic --}}
                                <select class="academicOptions d-none">
                                    @foreach($scholarships as $row)
                                        @if(strtolower($row->scholarship_desc) == 'academic')
                                            <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                {{-- Hidden option sets for Hostel --}}
                                <select class="hostelOptions d-none">
                                    @foreach($scholarships as $row)
                                        @if(strtolower($row->scholarship_desc) == 'hostel')
                                            <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                                <input type="hidden" id="session-fixed" name="session-fixed" value="">
                                <input type="hidden" id="studentType-fixed" name="studentType-fixed" value="">
                                <input type="hidden" id="dataType-fixed" name="dataType-fixed" value="">
                                <input type="hidden" id="scholarshipType-fixed" name="scholarshipType-fixed" value="">
                        </div>

                        <!-- Selected tags area -->
                        <div class="col-md-8">
                            <div id="selected-scholarships" class="d-flex flex-wrap gap-2 mt-3 mt-md-0"></div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-primary btn-outline-info btn-sm me-2" id="applyScholarshipFilter">
                            <i class="bi bi-funnel"></i> Apply Filter
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilterBtn">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-8">
            <div class="card h-100 shadow-sm ">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">School Wise Segregation</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    {{-- <th scope="col" style="width: 50px;">Select</th> --}}
                                    <th scope="col">School</th>
                                    <th scope="col">Code</th>
                                    <th scope="col" class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody id="school-wise-table">
                                <tr>
                                    {{-- <td class="check-cell" data-school="School of Business">
                                        <input type="checkbox" class="form-check-input school-check styled-checkbox" value="School of Business">
                                    </td> --}}
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    {{-- <td class="check-cell" data-school="School of Engineering">
                                        <input type="checkbox" class="form-check-input school-check styled-checkbox" value="School of Engineering">
                                    </td> --}}
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    {{-- <td class="check-cell" data-school="School of IT">
                                        <input type="checkbox" class="form-check-input school-check styled-checkbox" value="School of IT">
                                    </td> --}}
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <!-- Placeholder for dynamically loaded tables -->
            <div id="department-tables" class="mt-4"></div>
        </div>
        <!-- Project List -->
        <div class="col-md-12 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    {{-- <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Project</h6>
                        <button class="btn btn-sm btn-outline-dark"><i class="bi bi-plus-lg"></i> New</button>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item">Develop API Endpoints <span class="float-end text-muted">Due: Nov 29, 2024</span></li>
                        <li class="list-group-item">Onboarding Flow <span class="float-end text-muted">Due: Nov 30, 2024</span></li>
                        <li class="list-group-item">Build Dashboard <span class="float-end text-muted">Due: Dec 01, 2024</span></li>
                        <li class="list-group-item">Optimize Page Load <span class="float-end text-muted">Due: Dec 05, 2024</span></li>
                        <li class="list-group-item">Cross-Browser Testing <span class="float-end text-muted">Due: Dec 06, 2024</span></li>
                    </ul> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="row g-3">
        <!-- Team Collaboration -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">

{{-- School count Details MODAL --}}
<div class="modal fade" id="schoolDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-primary">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitle">� Scholarship Details - <span id="schoolName">Institute of Psychology</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- ================= Summary Overview ================= -->
        <div class="card border border-primary shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold text-primary mb-3">Summary Overview</h6>

            <table id="overview-table" class="table table-sm table-bordered align-middle text-center mb-0">
              <thead class="table-primary">
                <tr>
                  <th>Total Scholarships Awarded</th>
                  <th>Total Scholarhip Holders</th>
                  <th>Single Scholarship Holders</th>
                  <th>Multiple Scholarship Holders</th>
                </tr>
              </thead>
              <tbody>
                    {{-- dynamic rows will be appended here --}}
              </tbody>
            </table>
          </div>
        </div>

        <!-- ================= Deduplicated Students List ================= -->
        <div class="card border border-primary shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold text-primary mb-0">Scholarship Holders</h6>
            </div>

            <div class="studentsTableclass table-responsive">
              <table id="studentsTable" class="table table-striped table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Enrollment No</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>CGPA</th>
                    <th>Scholarships</th>
                  </tr>
                </thead>
                <tbody>
                    <!-- Dynamic student rows will be appended here -->
                </tbody>
              </table>
            </div>

            <!-- Lazy loading placeholder -->
            <div id="loadingIndicator" class="text-center py-3 d-none">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2 mb-0 text-muted">Loading more students...</p>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>


@stop
@section('javascript')

<script>
    function populateDashboard() {
        let session = $('#session-select').val();
        let studentType = $('#student-type-select').val();
        if (!session || !studentType) {
            alert('Please select both session and student type before applying filters.');
            return;
        }

        $.ajax({
            url: "{{ route('getDashboardData') }}",
            type: "GET",
            data: {
                session: session,
                student_type: studentType
            },
            beforeSend: function() {
                $('#apply-filters-btn')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');
            },
            success: function(response) {
                console.log('Dashboard Data:', response);

                // Example if your API returns data like:
                // { total_awarded: 50, total_holders: 25, on_alert: 8, pending_projects: 4 }
                if (response) {
                    $('#total-awarded').text(response.scholarshipCount[0].total_scholarships ?? 0);
                    $('#academic-total-scholarships').text(response.scholarshipCount[0].academic_scholarship ?? 0);
                    $('#hostel-total-scholarships').text(response.scholarshipCount[0].hostel_scholarship ?? 0);

                    $('#total-holders').text(response.studentCount[0].total_scholarships_students ?? 0);
                    $('#academic-total-students').text(response.studentCount[0].academic_scholarship_students ?? 0);
                    $('#hostel-total-students').text(response.studentCount[0].hostel_scholarship_students ?? 0);
                    $('#on-alert').text(response.on_alert ?? 0);
                    //setting fixed hidden inputs
                    setFilterParams();

                }
            },
            error: function(xhr) {
                console.error('Error fetching dashboard data:', xhr);
                alert('Failed to load dashboard data.');
            },
            complete: function() {
                $('#apply-filters-btn')
                    .prop('disabled', false)
                    .html('<i class="fa fa-filter me-2"></i> Apply Filters');
            }
        });
    }
    // Function to create "bubbles" for selected scholarships
    function updateSelectedBubbles() {
        const selected = $('#sch_type').select2('data');
        const container = $('#selected-scholarships');
        container.empty();

       if (selected.length === 0) {
            container.append(`
                <span class="badge rounded-pill bg-light text-primary border">
                   Note: Filter data by specific scholarships or view the overall summary.
                </span>
            `);
            return;
        }


        selected.forEach(item => {
            const bubble = $(`
                <span class="badge rounded-pill bg-primary text-white px-3 py-2 d-flex align-items-center">
                    ${item.text}
                    <i class="bi bi-x ms-2 remove-bubble" style="cursor:pointer;font-size:0.8rem;"></i>
                </span>
            `);

            // Remove on clicking the "x"
            bubble.find('.remove-bubble').on('click', function () {
                const valueToRemove = item.id;
                const currentValues = $('#sch_type').val() || [];
                const newValues = currentValues.filter(v => v !== valueToRemove);
                $('#sch_type').val(newValues).trigger('change');
                updateSelectedBubbles();
            });

            container.append(bubble);
        });
    }
    function get_schoolwiseScholarship_count(){
        let filterParams = getFilterParams();
        $.ajax({
            url: "{{ route('getSchoolwiseCounts') }}",
            type: "GET",
            data:  filterParams,
            beforeSend: function() {
                $('#applyScholarshipFilter')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');
            },
            success: function(response) {
                if (response.status) {
                    console.log('schoolwise_scholarshipCount Data:', response.data);
                    renderSchoolwiseTable(response.data);
                } else {
                    alert(response.message || 'Something went wrong while fetching data.');
                }
            },
            error: function(xhr) {
                console.error('Error fetching dashboard data:', xhr);
                let msg = 'Failed to load dashboard data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            },
            complete: function() {
                $('#applyScholarshipFilter')
                    .prop('disabled', false)
                    .html('<i class="fa fa-filter me-2"></i> Apply Filters');
            }
        });
    }
    function setFilterParams(){
        $('#session-fixed').val($('#session-select').val());
        $('#studentType-fixed').val($('#student-type-select').val());
        $('#dataType-fixed').val($('.stat-card.active-card').attr('id'));//id of the selected main card
        $('#scholarshipType-fixed').val( $('.stat-card.active-card').find('.child-card.selected-child-card').attr('type_attribute'));//select main card's selected child card
    }
    function getFilterParams() {
        let session = $('#session-fixed').val();
        let studentType = $('#studentType-fixed').val();
        let dataType = $('#dataType-fixed').val();
        let schType = $('#scholarshipType-fixed').val();
        // Get selected scholarship IDs (array)
        let selectedScholarships = $('#sch_type').val() || []; // [] if none selected
        // Build and return parameters object
        let filterParams = {
            session: session,
            studentType: studentType,
            dataType: dataType,
            schType: schType,
            scholarships: selectedScholarships
        };

        return filterParams;
    }
    function renderSchoolwiseTable(data) {
        let html = '';
        let selectedScholarships = $('#sch_type').val() || []; // [] if none selected
        let schType = $('#scholarshipType-fixed').val();
        // console.log(selectedScholarships);
        data.forEach(item => {
            html += `
                <tr>

                    <td class="text-center align-middle">${item.school_name}</td>
                    <td class="text-center align-middle">${item.school_code}</td>
                    <td class="text-center align-middle fw-semibold">
                        <a href="#"
                        class="text-primary school-count-link"
                        data-school-id="${item.school_id}"
                        data-school-name="${item.school_name}">
                        ${item.total}
                        </a>
                    </td>
                </tr>
            `;
        });

        // Inject into table body
        $('#school-wise-table').html(html);
    }
    function renderSchoolDetailsModal(data) {
        if (!data || data.length === 0) {
            $('#modalBody').html('<div class="text-center text-muted p-4">No data found for this selection.</div>');
            $('#schoolDetailsModal').modal('show');
            return;
        }

        // --- Summary calculation ---
        let scholarshipcount = data[0].total_scholarships_awarded_to_school;
        let uniqueStudents = data.length;
        let singleHolders = data.filter(s => s.scholarship_label !== 'MULTIPLE_SCHOLARSHIP_HOLDER').length;
        let multipleHolders = data.filter(s => s.scholarship_label === 'MULTIPLE_SCHOLARSHIP_HOLDER').length;

        // --- Update Summary Table ---
        let $overviewTbody = $('#overview-table tbody');
        $overviewTbody.empty().append(`
            <tr>
                <td><span class="fw-semibold text-dark">${scholarshipcount}</span></td>
                <td><span class="fw-semibold text-dark">${uniqueStudents}</span></td>
                <td><span class="fw-semibold text-success">${singleHolders}</span></td>
                <td><span class="fw-semibold text-warning">${multipleHolders}</span></td>
            </tr>
        `);

        // --- Students Table ---
        let $studentsTable = $('#studentsTable');
        let $studenttbody = $studentsTable.find('tbody');
        $studenttbody.empty();

        // Destroy previous DataTable if exists
        if ($.fn.DataTable.isDataTable('#studentsTable')) {
            $studentsTable.DataTable().clear().destroy();
        }

        // Append new rows
        data.forEach((item, i) => {
            $studenttbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.enrollment_no}</td>
                    <td>${item.student_name}</td>
                    <td>${item.dept_name}</td>
                    <td>${item.prog_name}</td>
                    <td>${item.cgpa ?? '-'}</td>
                    <td>${item.scholarships_list}</td>
                </tr>
            `);
        });

        // --- Initialize DataTable AFTER modal is visible ---
        $('#schoolDetailsModal')
            .off('shown.bs.modal') // avoid multiple bindings
            .on('shown.bs.modal', function () {
                $('#studentsTable').DataTable({
                    pageLength: 10,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    language: {
                        paginate: {
                            previous: "<i class='bi bi-chevron-left'></i>",
                            next: "<i class='bi bi-chevron-right'></i>"
                        }
                    }
                });
            });

        // --- Show Modal ---
        $('#schoolDetailsModal').modal('show');
    }

    function loadScholarships() {
         let type = $('.selected-child-card').attr('type_attribute');

        // Select box reference
        let select = $('#sch_type');
        select.empty().append('<option value=""></option>');

        if (type === 'academic') {
            select.append($('.academicOptions').html());
        } else if (type === 'hostel') {
            select.append($('.hostelOptions').html());
        }
        updateSelectedBubbles();//reset the bubbles
    }
$(document).ready(function() {
    populateDashboard(); // Initial load of main cards
    // Initialize Select2
    $('#sch_type').select2({
        placeholder: 'All',
        allowClear: false,
        closeOnSelect: false ,//keep dropdown open on selection
        width: '100%'
    });
    loadScholarships(); // Load initial scholarships in select box based on default selection of main cards

    // Card selection logic
    $('.stat-card-selectable').on('click', function() {
        // Remove active styling from all cards
        $('.stat-card-selectable').removeClass('active-card');
        $('.stat-card-selectable').find('.description').addClass('text-success');

        // Apply to clicked one
        $(this).addClass('active-card');
        $(this).find('.description').removeClass('text-success');
        // $('.child-card').removeClass('selected-child-card');//unselect  all childcard
        // $(this).find('.child-card').first().addClass('selected-child-card'); // select first child card of the selected main card
        setFilterParams();
    });
    $('.child-card').on('click', function() {
        // Remove selection from all
        $('.child-card').removeClass('selected-child-card');
        // Add selection to the clicked one
        $(this).addClass('selected-child-card');
        loadScholarships(); // Load scholarships based on selected child card

    });
    // Update bubbles whenever selection changes
    $('#sch_type').on('change', function () {
        updateSelectedBubbles();
    });

    // Apply Scholarship filter
    $('#applyScholarshipFilter').on('click', function () {
        get_schoolwiseScholarship_count(); // your existing function
    });

    // Clear filter
    $('#clearFilterBtn').on('click', function () {
       loadScholarships(); // Reset scholarships
    });
    $('#apply-filters-btn').on('click', function() {
        populateDashboard();
    });
    // Initialize DataTable only once when modal opens
    $('#schoolDetailsModal').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#studentsTable')) {
            $('#studentsTable').DataTable({
                pageLength: 10,        // show 10 per page
                lengthChange: false,   // hide "Show X entries"
                searching: true,       // allow search
                ordering: true,        // allow sorting
                info: true,            // show "Showing 1–10 of ..."
                language: {
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                }
            });
        }
    });
    // Delegate click event since rows are dynamic
    $(document).on('click', '.school-count-link', function(e) {
        e.preventDefault();
        let filterParams = getFilterParams(); // returns object like { session: [...], studentType: ..., schType: ... }

        // add school_id directly to that object
        filterParams.school_id = $(this).data('school-id');
        let schoolName =  $(this).data('school-name');

        // Optional: show loading spinner inside modal
        $('#modalTitle').text(`Scholarships for ${schoolName}`);

        $.ajax({
            url: "{{ route('getSchoolCountDetails') }}",
            type: 'GET',
            data: filterParams,
            beforeSend: function() {
                $('#schoolDetailsModal').modal('show');
                $('#modal-body').html(`
                    <div class="text-center p-5">
                        <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                        <p>Loading details...</p>
                    </div>
                `);
            },
            success: function(res) {
                if (res.status && res.data && res.data.length > 0) {
                    renderSchoolDetailsModal(res.data);
                } else {
                    // Handle backend validation or empty results
                    let msg = res.message
                        ? `<div class="text-danger text-center p-4">${res.message}</div>`
                        : `<div class="text-center text-muted p-4">No data found for this selection.</div>`;
                    $('#modal-body').html(msg);
                }
            },
            error: function(xhr) {
                console.error('Error fetching school details:', xhr);
                let msg = 'Failed to load data.';

                // Show detailed backend error message if available
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                $('#modal-body').html(`
                    <div class="text-danger text-center p-4">${msg}</div>
                `);
            }
        });

    });

});


</script>
@stop
