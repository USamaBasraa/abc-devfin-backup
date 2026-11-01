@extends('layouts.app')

@php
    $today = date('Y-m-d');


    $filterSemesters = collect($students ?? [])
        ->pluck('semester_no')
        ->filter(function ($semester) {
            return $semester !== null && $semester !== '';
        })
        ->unique()
        ->sort()
        ->values();

    if ($filterSemesters->isEmpty()) {
        $filterSemesters = collect($semesters ?? []);
    }

    $filterSessions = collect($students ?? [])
        ->map(function ($student) {
            $sessionId = $student->sess_id
                ?? $student->session_id
                ?? $student->sai_sdpo_sess_id
                ?? $student->sai_enrollment_session_id
                ?? null;

            $sessionName = $student->sess_name
                ?? $student->session_name
                ?? $student->sai_enrollment_session_name
                ?? $sessionId;

            if (!$sessionId) {
                return null;
            }

            return (object) [
                'sess_id' => $sessionId,
                'sess_name' => $sessionName,
            ];
        })
        ->filter()
        ->unique('sess_id')
        ->values();

    if ($filterSessions->isEmpty()) {
        $filterSessions = collect($sessions ?? []);
    }
@endphp

<style>
    #example2_wrapper {
        width: 100%;
    }

    #enrolled_students_table_length {
        margin-top: -30px;
    }

    #enrolled_students_table_paginate {
        margin-top: -30px;
    }

    #example2,
    #example2_wrapper .dataTables_scrollHead table,
    #example2_wrapper .dataTables_scrollBody table,
    #example2_wrapper .dataTables_scrollFoot table {
        table-layout: fixed !important;
        border-collapse: collapse !important;
    }

    #example2_wrapper .dataTables_scrollHead,
    #example2_wrapper .dataTables_scrollBody,
    #example2_wrapper .dataTables_scrollFoot {
        width: 100% !important;
    }

    #example2_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
    }

    #example2_wrapper th,
    #example2_wrapper td {
        box-sizing: border-box !important;
        text-align: center;
        vertical-align: middle;
    }

    #example2_wrapper thead th,
    #example2_wrapper tfoot th {
        background-color: #ffff00;
        color: #000;
        font-size: 12px;
        white-space: normal !important;
        word-break: break-word;
    }

    #example2_wrapper tbody td {
        white-space: nowrap !important;
    }

    #example2_wrapper th:nth-child(1),
    #example2_wrapper td:nth-child(1) {
        min-width: 130px !important;
        width: 130px !important;
    }

    #example2_wrapper th:nth-child(2),
    #example2_wrapper td:nth-child(2) {
        min-width: 170px !important;
        width: 170px !important;
    }

    #example2_wrapper th:nth-child(3),
    #example2_wrapper td:nth-child(3) {
        min-width: 300px !important;
        width: 300px !important;
    }

    #example2_wrapper th:nth-child(4),
    #example2_wrapper td:nth-child(4) {
        min-width: 240px !important;
        width: 240px !important;
    }

    #example2_wrapper th:nth-child(5),
    #example2_wrapper td:nth-child(5) {
        min-width: 230px !important;
        width: 230px !important;
    }

    #example2_wrapper th:nth-child(6),
    #example2_wrapper td:nth-child(6) {
        min-width: 260px !important;
        width: 260px !important;
    }

    #example2_wrapper th:nth-child(7),
    #example2_wrapper td:nth-child(7) {
        min-width: 220px !important;
        width: 220px !important;
    }

    #example2_wrapper th:nth-child(8),
    #example2_wrapper td:nth-child(8) {
        min-width: 220px !important;
        width: 220px !important;
    }

    #example2_wrapper th:nth-child(9),
    #example2_wrapper td:nth-child(9) {
        min-width: 230px !important;
        width: 230px !important;
    }

    #example2_wrapper th:nth-child(10),
    #example2_wrapper td:nth-child(10) {
        min-width: 200px !important;
        width: 200px !important;
    }

    #example2_wrapper th:nth-child(11),
    #example2_wrapper td:nth-child(11) {
        min-width: 220px !important;
        width: 220px !important;
    }

    #example2_wrapper th:nth-child(12),
    #example2_wrapper td:nth-child(12) {
        min-width: 220px !important;
        width: 220px !important;
    }

    #example2_wrapper .dataTables_length,
    #example2_wrapper .dataTables_filter,
    #example2_wrapper .dataTables_info,
    #example2_wrapper .dataTables_paginate {
        position: relative;
        z-index: 10;
        overflow: visible !important;
    }

    .filter-action-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
</style>

@section('content')
<div class="container-fluid" style="margin-bottom:50px;">
    <div class="card p-4">
        <div class="card-header">
            <h2 class="mb-4 text-left">Enrolled Students Details</h2>
        </div>

        <div class="card-body">
            <div class="bs-stepper-content">
                <form id="enrolledStudentsFilterForm" onsubmit="return false;">
                    <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                        <div class="form-row">
                            <div class="form-group col-12 col-lg-3">
                                <label for="session">Session</label>
                                <select class="form-control" id="session" name="session">
                                    {{-- <option value="">Select Session</option> --}}
                                    @foreach($filterSessions as $session)
                                        <option value="{{ $session->sess_id }}" {{ (string) $session->sess_id === (string) ($sessionId ?? '') ? 'selected' : '' }}>
                                            {{ $session->sess_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="school">School</label>
                                <select class="form-control" id="school" name="school">
                                    <option value="">Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->school_id }}">
                                            {{ $school->school_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="department_id">Department</label>
                                <select class="form-control" id="department_id" name="department_id">
                                    <option value="">-- Select Department --</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="program">Program</label>
                                <select class="form-control" id="program" name="program">
                                    <option value="">Select Program</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-4">
                                <label for="semester_no">Semester No</label>
                                <select class="form-control" id="semester_no" name="semester_no">
                                    <option value="">Select Semester</option>
                                    @foreach($filterSemesters as $semester)
                                        <option value="{{ $semester }}">
                                            {{ $semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-4">
                                <label for="student_id">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID">
                            </div>

                            <div class="form-group col-12 col-lg-4">
                                <label for="registered_number">Student Enrollment</label>
                                <input type="text" class="form-control" id="registered_number" name="registered_number" placeholder="Enter Student Enrollment">
                            </div>

                            <div class="form-group col-12 col-lg-6">
                                <label for="from_date">From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date" max="{{ $today }}">
                            </div>

                            <div class="form-group col-12 col-lg-6">
                                <label for="to_date">To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date" max="{{ $today }}">
                            </div>

                            <div class="form-group col-12">
                                <div class="filter-action-wrapper">
                                    <button type="button" id="searchBtn" class="btn btn-primary px-4">
                                        Apply Filter
                                    </button>

                                    <button type="button" id="resetBtn" class="btn btn-secondary px-4">
                                        Reset
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <table id="enrolled_students_table" class="table table-striped table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Reg</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>Semester No of Selected Session</th>
                        <th>Number of Credit Hours Enrolled (Session)</th>
                        <th>Remaining Credit Hours (Degree)</th>
                        <th>Gross Amount (Selected Session)</th>
                        <th>Scholarships (Selected Session)</th>
                        <th>Arears (Selected Session)</th>
                        <th>Issue Date</th>
                        <th>Current Student Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach(($students ?? []) as $student)
                        <tr>
                            <td>{{ $student->student_id ?? '-' }}</td>
                            <td>{{ $student->student_reg ?? '-' }}</td>
                            <td>{{ $student->school_name ?? '-' }}</td>
                            <td>{{ $student->prog_name ?? '-' }}</td>
                            <td>{{ $student->semester_no ?? '-' }}</td>
                            <td>{{ $student->no_of_enrolled_cr_hr ?? 0 }}</td>
                            <td>{{ $student->remaining_cr_hr ?? 0 }}</td>
                            <td>{{ $student->gross_amount ?? 0 }}</td>
                            <td>{{ $student->scholarship_count ?? '-' }}</td>
                            <td>{{ $student->arrears ?? 0 }}</td>
                            <td>{{ $student->issue_date ?? '-' }}</td>
                            <td>{{ $student->sai_student_status ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Reg</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>Semester No of Selected Session</th>
                        <th>Number of Credit Hours Enrolled (Session)</th>
                        <th>Remaining Credit Hours (Degree)</th>
                        <th>Gross Amount (Selected Session)</th>
                        <th>Scholarships (Selected Session)</th>
                        <th>Arears (Selected Session)</th>
                        <th>Issue Date</th>
                        <th>Current Student Status</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function () {
        var tableId = '#enrolled_students_table';

        function getExportOptions() {
            return {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                modifier: {
                    page: 'all',
                    search: 'applied'
                },
                format: {
                    body: function (data) {
                        if (data === null || data === undefined) {
                            return '';
                        }

                        if (typeof data === 'string' && data.indexOf('<') !== -1) {
                            var tmp = document.createElement('div');
                            tmp.innerHTML = data;
                            return tmp.textContent || tmp.innerText || '';
                        }

                        return data;
                    }
                }
            };
        }

        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }

        var table = $(tableId).DataTable({
            scrollX: true,
            autoWidth: false,
            pageLength: 100,
            lengthMenu: [[100, 500, 5000, 10000], [100, 500, 5000, 10000]],
            dom: 'Bflrtip',
            language: {
                emptyTable: 'No enrolled students found'
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Enrolled Students Details',
                    exportOptions: getExportOptions()
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Enrolled Students Details',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: getExportOptions(),
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 6;
                        doc.styles.tableHeader.fontSize = 6;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [8, 20, 8, 10];
                    }
                },
                {
                    extend: 'print',
                    title: 'Enrolled Students Details',
                    exportOptions: getExportOptions(),
                    customize: function (win) {
                        var style = win.document.createElement('style');
                        style.innerHTML = '@page { size: landscape; margin: 5mm; } body { font-size: 7pt; } table { width: 100% !important; table-layout: fixed; } th, td { white-space: normal !important; word-wrap: break-word; padding: 2px; font-size: 7pt; }';
                        win.document.head.appendChild(style);
                    }
                }
            ]
        });

        var initialTableData = table.rows().data().toArray();

        function adjustTableColumns() {
            setTimeout(function () {
                table.columns.adjust().draw(false);
            }, 300);
        }

        function resetDependentDropdowns() {
            $('#department_id').empty().append('<option value="">-- Select Department --</option>');
            $('#program').empty().append('<option value="">Select Program</option>');
        }

        adjustTableColumns();

        $(window).on('resize', function () {
            table.columns.adjust();
        });

        $('#school').on('change', function () {
            let schoolId = $(this).val();
            let departmentSelect = $('#department_id');

            departmentSelect.empty();
            departmentSelect.append('<option value="">Loading...</option>');

            $('#program').empty().append('<option value="">Select Program</option>');

            if (schoolId) {
                $.ajax({
                    url: '/get-departments/' + schoolId,
                    type: 'GET',
                    success: function (data) {
                        departmentSelect.empty();
                        departmentSelect.append('<option value="">-- Select Department --</option>');

                        $.each(data, function (index, department) {
                            departmentSelect.append(
                                '<option value="' + department.dept_id + '">' + department.dept_name + '</option>'
                            );
                        });
                    },
                    error: function () {
                        departmentSelect.empty();
                        departmentSelect.append('<option value="">Department load nahi ho raha</option>');
                    }
                });
            } else {
                resetDependentDropdowns();
            }
        });

        $('#department_id').on('change', function () {
            let deptId = $(this).val();
            let programSelect = $('#program');

            programSelect.empty();
            programSelect.append('<option value="">Loading...</option>');

            if (deptId) {
                $.ajax({
                    url: '/get-programs/' + deptId,
                    type: 'GET',
                    success: function (data) {
                        programSelect.empty();
                        programSelect.append('<option value="">Select Program</option>');

                        $.each(data, function (index, program) {
                            programSelect.append(
                                '<option value="' + program.prog_id + '">' + program.prog_name + '</option>'
                            );
                        });
                    },
                    error: function () {
                        programSelect.empty();
                        programSelect.append('<option value="">Program load nahi ho raha</option>');
                    }
                });
            } else {
                programSelect.empty();
                programSelect.append('<option value="">Select Program</option>');
            }
        });

        $('#searchBtn').on('click', function () {
            let requestData = {
                _token: "{{ csrf_token() }}",
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                school: $('#school').val(),
                department_id: $('#department_id').val(),
                program: $('#program').val(),
                semester_no: $('#semester_no').val(),
                session: $('#session').val(),
                student_id: $('#student_id').val(),
                registered_number: $('#registered_number').val()
            };

            $.ajax({
                url: "{{ route('enrolled.students.filter') }}",
                type: "POST",
                data: requestData,

                beforeSend: function () {
                    $('#searchBtn').prop('disabled', true).text('Searching...');
                    $('#resetBtn').prop('disabled', true);
                },

                success: function (response) {
                    table.clear();

                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (index, student) {
                            table.row.add([
                                student.student_id ?? '-',
                                student.student_reg ?? '-',
                                student.school_name ?? '-',
                                student.prog_name ?? '-',
                                student.semester_no ?? '-',
                                student.no_of_enrolled_cr_hr ?? 0,
                                student.remaining_cr_hr ?? 0,
                                student.gross_amount ?? 0,
                                student.scholarship_count ?? '-',
                                student.arrears ?? 0,
                                student.issue_date ?? '-',
                                student.sai_student_status ?? '-'
                            ]);
                        });
                    }

                    table.draw();
                    adjustTableColumns();
                },

                error: function () {
                    alert('Data filter nahi ho raha');
                },

                complete: function () {
                    $('#searchBtn').prop('disabled', false).text('Apply Filter');
                    $('#resetBtn').prop('disabled', false);
                }
            });
        });

        $('#resetBtn').on('click', function () {
            $('#enrolledStudentsFilterForm')[0].reset();

            resetDependentDropdowns();

            table.clear();

            $.each(initialTableData, function (index, row) {
                table.row.add(row);
            });

            table.search('').columns().search('').draw();
            adjustTableColumns();
        });
    });
</script>
@stop


