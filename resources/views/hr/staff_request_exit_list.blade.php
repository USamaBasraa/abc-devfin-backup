@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<!-- resources/views/hr/staff_request_exit_list.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Employee Exit Request List</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Employee Exit Request List</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="accepted-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="accepted" aria-selected="false">Accepted</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>EMPLOYEE USERNAME</th>
                            <th>Employee Name</th>
                            <th>Entry Date</th>
                            <th>Designation</th>
                            <th>Interview</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending as $employee)
                        <tr>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->employee_name }}</td>
                            <td>{{ $employee->entry_date }}</td>
                            <td>{{ $employee->designation }}</td>
                            <td>
                                <a class="btn btn-primary" href="https://staff.bnu.edu.pk/HOD/staffExitForm.php?id={{ $employee->employee_id }}" target="_blank">View PDF</a>
                            </td>
                            <td>
                                <form action="{{ url('/employees-exit/accept', $employee->employee_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Accept Received</button>
                                </form>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Employee UserName</th>
                            <th>Employee Name</th>
                            <th>Entry Date</th>
                            <th>Designation</th>
                            <th>Received by HR</th>
                            <th>Interview</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accepted as $employee)
                            <tr>
                                <td>{{ $employee->employee_id }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->entry_date }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td>{{ $employee->recived_by_hr }}</td>
                                <td>
                                <a class="btn btn-primary" href="https://staff.bnu.edu.pk/HOD/staffExitForm.php?id={{ $employee->employee_id }}" target="_blank">View PDF</a>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>





@stop

