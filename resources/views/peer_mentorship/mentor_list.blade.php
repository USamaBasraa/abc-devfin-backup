@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors List</title>
    <!-- Add Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Mentors List</h1>

        <!-- Table to display mentor details -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Enrollment</th>
                    <th>Full Name</th>
                    <th>Committee Remarks</th>
                    <th>Mentor Type</th>
                    <th>Course ID</th>
                    <th>Session ID</th>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Week Day</th>
                    <th>From Time</th>
                    <th>To Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mentors as $mentor)
                    <tr>
                        <td>{{ $mentor->m_enrollment }}</td>
                        <td>{{ $mentor->full_name }}</td>
                        <td>{{ $mentor->m_committee_remarks }}</td>
                        <td>{{ $mentor->m_mentor_type }}</td>
                        <td>{{ $mentor->md_co_id }}</td>
                        <td>{{ $mentor->pmt_sess_id }}</td>
                        <td>{{ $mentor->course_name }}</td>
                        <td>{{ $mentor->course_code }}</td>
                        <td>{{ $mentor->week_day }}</td>
                        <td>{{ $mentor->from_time }}</td>
                        <td>{{ $mentor->to_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Bootstrap JS for possible interactivity (e.g., tooltips, modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
