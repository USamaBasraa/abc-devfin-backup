@extends('layouts.app')
@section('title') BNU CMS @stop

@section('content')
<style>
    body {
        background: #f1f5fb;
        font-family: Arial, sans-serif;
    }
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 1200px;
        margin: 30px auto;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        color: #1d3f94;
        font-weight: bold;
        margin-bottom: 25px;
    }
    .form-title {
        font-size: 20px;
        font-weight: bold;
        color: #1d3f94;
        margin-bottom: 20px;
        border-bottom: 2px solid #e3e3e3;
        padding-bottom: 10px;
    }
    .form-group label {
        font-weight: 600;
    }
    .table-responsive {
        margin: 30px auto;
        max-width: 95%;
    }
    #roadmapTable th, #roadmapTable td {
        text-align: center;
        vertical-align: middle;
    }
    #roadmapTable th {
        background-color: #1d3f94;
        color: white;
    }
    #roadmapTable tbody tr:hover {
        background-color: #f0f8ff;
    }
    .btn-submit {
        background-color: #007bff;
        color: white;
    }
    .btn-reset {
        background-color: #ffc107;
        color: black;
    }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="form-container">
    <div class="form-title">Filter Roadmap Course Data</div>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="session">Session</label>
            <select id="session" class="form-control">
                <option value="">Select Session</option>
                @foreach($sessions as $row)
                    <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="roadmap_session">Roadmap Session</label>
            <select id="roadmap_session" class="form-control">
                <option value="">Select Session</option>
                @foreach($sessions as $row)
                    <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="schools">School</label>
            <select id="schools" name="schools" class="form-control select2">
                <option value="">Select School</option>
                @foreach($activeSchools as $row)
                    <option value="{{ $row->school_id }}">{{ $row->school_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="departments">Department</label>
            <select id="departments" name="departments" class="form-control select2">
                <option value="">Select Department</option>
                @foreach($activeDeparments as $row)
                    <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="programs">Program</label>
            <select id="programs" name="programs" class="form-control select2">
                <option value="">Select Program</option>
                @foreach($activePrograms as $row)
                    <option value="{{ $row->prog_id }}">{{ $row->prog_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="roadmapTable" class="table table-bordered table-hover display">
        <thead>
            <tr>
                <th>Session</th>
                <th>RoadMap Session</th>
                <th>School</th>
                <th>Department</th>
                <th>Program</th>
                <th>Course Name</th>
                <th>Course Offered</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded dynamically -->
        </tbody>
    </table>
</div>
@stop

@section('javascript')
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>


$('#schools').on('change', function () {
    let schoolId = $('#schools').val();

    $('#departments').html('<option value="">Loading...</option>');

    if (schoolId) {
        $.ajax({
            url: '{{ url('/get_departments_by_school') }}',
            type: 'GET',
            data: { school_id: schoolId },
            success: function (data) {
                $('#departments').html('<option value="">Select Department</option>');
                $.each(data, function (key, dept) {
                    $('#departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch departments.');
                $('#departments').html('<option value="">Select Department</option>');
            }
        });
    } else {
        $('#departments').html('<option value="">Select Department</option>');
    }
});

$('#departments').on('change', function () {
    let deptId = $('#departments').val();
     var rdmapsession = $('#roadmap_session').val();

    $('#programs').html('<option value="">Loading...</option>');

    if (deptId) {
        $.ajax({
            url: '{{ url('/get_programs_by_school') }}',
        
            type: 'GET',
            data: { dept_id: deptId,rdmapsession: rdmapsession },
            success: function (data) {
                $('#programs').html('<option value="">Select Program</option>');
                $.each(data, function (key, prog) {
                    $('#programs').append(`<option value="${prog.prog_id}">${prog.prog_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch programs.');
                $('#programs').html('<option value="">Select Programs</option>');
            }
        });
    } else {
        $('#programs').html('<option value="">Select Programs</option>');
    }
});

$(document).ready(function () {
    loadRoadmapData();
});

$('#session, #roadmap_session, #schools, #departments, #programs').change(function () {
    loadRoadmapData();
});

function loadRoadmapData() {
    if ($.fn.DataTable.isDataTable('#roadmapTable')) {
        $('#roadmapTable').DataTable().clear().destroy();
    }

    var session_id = $('#session').val();
    var roadmap_session_id = $('#roadmap_session').val();
    var school_id = $('#schools').val();
    var dept_id = $('#departments').val();
    var prog_id = $('#programs').val();

    $('#roadmapTable').DataTable({
        processing: true,
        // serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 20, 30,500,1000, 5000], [10, 20, 30,500,1000, 5000]],
        language: {
            search: "Filter records:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        },
        ajax: {
            url: "{{ route('roadmap.verifydata') }}",
            type: 'GET',
            data: {
                session_id: session_id,
                roadmap_session_id: roadmap_session_id,
                school_id: school_id,
                dept_id: dept_id,
                prog_id: prog_id
            }
        },
        columns: [
            { data: 'active_session' },
            { data: 'roadmap_session' },
            { data: 'school_code' },
            { data: 'dept_name' },
            { data: 'prog_name' },
            { data: 'course_name' },
            {
                data: 'course_offered',
                createdCell: function (td, cellData) {
                    if (cellData === 'No') {
                        $(td).css({
                            'background-color': 'red',
                            'color': 'white',
                            'font-weight': 'bold'
                        });
                    }
                }
            }
        ],
        dom: 'Blfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
}
</script>
@stop
