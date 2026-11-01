@extends('layouts.app')
{{-- @dd("working"); --}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    div.dataTables_wrapper div.dataTables_filter {
        margin-left: auto;
        text-align: right;
        display: flex;
        align-items: center;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 250px;
        max-width: 100%;
    }
</style>

@section('title', 'Course Offering Report')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fa fa-book"></i> Course Offering Report
            </h4>
        </div>

        <div class="card-body">
            <!-- Filter Section -->
            <form method="GET" action="{{ route('course-offering-report.index') }}">
                <div class="form-row">
                    <!-- Session -->
                    <div class="form-group col-md-3">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            <option value="">All Sessions</option>
                            @foreach ($sessions as $key => $session)
                                <option value="{{ $key }}" {{ $filters['user_session'] == $key ? 'selected' : '' }}>
                                    {{ $session }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- School -->
                    <div class="form-group col-md-3">
                        <label for="school_sel">School</label>
                        <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                            <option value="">All Schools</option>
                            @foreach ($schools as $row)
                                <option value="{{ $row->school_id }}" {{ $filters['school'] == $row->school_id ? 'selected' : '' }}>
                                    {{ $row->school_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="form-group col-md-3">
                        <label for="department">Department</label>
                        <select name="department" id="department" class="form-control">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->dept_id }}" {{ $filters['department'] == $dept->dept_id ? 'selected' : '' }}>
                                    {{ $dept->dept_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="form-group col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <!-- Clear Button -->
                <div class="text-right mb-3">
                    <a href="{{ route('course-offering-report.index') }}" class="btn btn-secondary">
                        <i class="fa fa-redo"></i> Clear
                    </a>
                </div>
            </form>

            <hr>

            <!-- Results Section -->
            @if(count($rec) > 0)
          
                <div class="table-responsive">
                    <table id="courseOfferingTable" class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:5%;background: #4454c3 !important;color: white;">#</th>
                                <th style="width:20%;background: #4454c3 !important;color: white;">Course Name</th>
                                <th style="width:20%;background: #4454c3 !important;color: white;">Teacher Name</th>
                                <th style="width:15%;background:#4454c3 !important;color:white;">Status</th>
                                <th style="width:15%;background: #4454c3 !important;color: white;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rec as $key => $offering)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $offering->course_name ?? 'N/A' }}</td>
                                    <td>{{ $offering->teacher_name ?? 'Unassigned' }}</td>
                                    <td class="text-center">
                                        @if($offering->content_status == 1 && !empty($offering->file_path))
                                            <span class="badge badge-success">Submitted</span>
                                        @else
                                            <span class="badge badge-danger">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($offering->content_status == 1 && !empty($offering->file_path))
                                            <a href="{{ $offering->file_path }}" target="_blank" class="btn btn-success btn-sm">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fa fa-lock"></i> No File
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-info-circle"></i> No Records Found!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
function getDepartments() {
    var schoolId = $('#school_sel').val();
    var deptSelect = $('#department');
    deptSelect.html('<option>Loading...</option>');

    if (!schoolId) {
        deptSelect.html('<option value="">All Departments</option>');
        return;
    }

    $.get('/get-departments/' + schoolId, function(res) {
        var html = '<option value="">All Departments</option>';
        if(res && res.length > 0) {
            res.forEach(function(dept){
                html += '<option value="'+dept.dept_id+'">'+dept.dept_name+'</option>';
            });
        }
        deptSelect.html(html);
    }).fail(function() {
        deptSelect.html('<option value="">All Departments</option>');
    });
}

$(document).ready(function() {
    // Initialize DataTables
    $('#courseOfferingTable').DataTable({
        dom: '<"row align-items-center mb-3"<"col-md-6 d-flex align-items-center"B><"col-md-6 d-flex justify-content-end"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Course Offering Report',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Course Offering Report',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                orientation: 'portrait',
                pageSize: 'A4'
            }
        ],
        pageLength: 20,
        lengthMenu: [[20, 25, 50, 100, -1], [20, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _TOTAL_ total records)",
            zeroRecords: "No matching records found"
        },
        columnDefs: [
            { orderable: false, targets: 3 }
        ],
        responsive: true
    });
});
</script>
@endsection
