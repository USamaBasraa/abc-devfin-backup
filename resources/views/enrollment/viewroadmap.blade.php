@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')

<style>
/* Wrap both tables and make columns equal height */
/* Light gray background for card */
.roadmap-col .card {
    background-color: #f9fafb; /* subtle gray */
    border: 1px solid #e1e4e8;
    border-radius: 6px;
}

/* Header bar styling */
.roadmap-col .card-header {
    background-color: #e6f4f1 !important;  /* soft green tint */
    border-bottom: 2px solid #2dbeb4;      /* green accent */
}

/* Table header styling */
.roadmap-table thead th {
    background-color: #eef2f7 !important;
    font-weight: bold;
    border-bottom: 2px solid #d2d6dc;
}

/* Darker alternating row colors */
.roadmap-table tbody tr:nth-child(odd) {
    background-color: #f2f4f7;  /* light gray */
}

.roadmap-table tbody tr:nth-child(even) {
    background-color: #ffffff; /* white */
}

/* Borders to define table cells clearly */
.roadmap-table td,
.roadmap-table th {
    border-color: #d1d5db !important;
    vertical-align: middle !important;
}
</style>

<div class="card">
    <div class="container-fluid py-4">
        <div class="card-header">
            <h3>Degree Roadmap Session</h3>
        </div>

        <div class="card-block">
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="session">Roadmap Session</label>
                    <select id="session" class="form-control">
                        <option value="">Select Session</option>
                        @foreach($sessions as $row)
                            <option value="{{ $row->sess_id }}" >
                            
                                {{ $row->sess_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                
                <div class="form-group col-md-3">
                    <label for="schools">Schools</label>
                    <select id="schools" name="schools" class="form-control select2">
                        <option value="">Select Degree</option>
                        @foreach($activeSchools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_code }}</option>
                        @endforeach
                    </select>
                </div>      
            
                <div class="form-group col-md-3">
                    <label for="departments">Departments</label>
                    <select id="departments" name="departments" class="form-control select2">
                        <option value="">Select Department</option>
                        @foreach($activeDeparments as $row)
                            <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="programs">Program</label>
                    <select id="programs" name="programs" class="form-control select2">
                        <option value="">Select Program</option>
                        @foreach($activePrograms as $row)
                            <option value="{{ $row->prog_id }}">{{ $row->prog_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>      

            <div class="form-row mb-3">
                <div class="container-fluid py-4" id="sessionTableContainer"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')

<script type="text/javascript">
$(document).ready(function() {
    loadsession();
});

function loadsession() {

    let sessionId = $('#session').val();
    let schoolId = $('#schools').val();

    let progId = $('#programs').val();

   
    $.ajax({
        url: "/get-session-data-prog",
        type: "GET",
        data: {
            session_id: sessionId,
            schoolId: schoolId,
            progId: progId
        },
        success: function (response) {
            console.log(response);

            if (response.data.length === 0) {
                $('#sessionTableContainer').html('<p class="text-danger">No records found.</p>');
                return;
            }

            let html = `
                <div class="row">
            `;

            let currentSess = null;
            let counter = 1;

            $.each(response.data, function(i, row) {

                if (currentSess !== row.sess_id) {

                    if (currentSess !== null) {
                        html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                        `;
                    }

                    currentSess = row.sess_id;
                    counter = 1;

                    html += `
                <div class="col-md-6 mb-4 roadmap-col">
                    <div class="card shadow-sm h-100">

                        <div class="card-header bg-light border-bottom">
                            <h5 class="text-primary mb-0"><b>${row.sess_name}</b></h5>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-striped mb-0 roadmap-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>Course</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                }

                html += `
                    <tr>
                        <td>${counter}</td>
                        <td>${getCourseName(row)}</td>
                    </tr>
                `;

                counter++;
            });

            html += `
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            `;

            $('#sessionTableContainer').html(html);
        }
    });
}

/* ========================
   COURSE NAME MAPPING
=========================*/

function getCourseName(row) {
    if (row.course_cd_name && row.course_cd_name !== "") {
        return row.course_cd_name;
    }

    let id = row.course_id;

    const courseMap = {
        123: "Elective 1",
        456: "Elective 2",
        789: "Elective 3",
        555: "Elective 4",
        666: "Elective 5",
        777: "Elective 6",

        888: "Degree Elective 1",
        118: "Degree Elective 2",
        112: "Degree Elective 3",
        113: "Degree Elective 4",
        114: "Degree Elective 5",
        115: "Degree Elective 6",

        111: "Specialization 1",
        222: "Specialization 2",
        333: "Specialization 3",
        444: "Specialization 4",

        119: "Minor 1",
        120: "Minor 2",
        121: "Minor 3",
        122: "Minor 4"
    };

    return courseMap[id] ?? "N/A";
}

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
     var rdmapsession = $('#session').val();

    $('#programs').html('<option value="">Loading...</option>');

    if (deptId) {
        $.ajax({
            url: '{{ url('/programs') }}',
        
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

$('#programs').on('change', function () {
   
    loadsession();
   
});
</script>

@endsection
