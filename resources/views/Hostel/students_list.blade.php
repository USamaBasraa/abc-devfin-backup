@extends('layouts.app') {{-- Or your main layout --}}

@section('content')
<head>
    <title>Export to Excel</title>

    {{-- Bootstrap 4 dependencies --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<div class="container">
    <h3 class="mb-4">Hostel Students List</h3>

    {{-- Tabs --}}
    <ul class="nav nav-tabs" id="hostelTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="male-tab" data-toggle="tab" href="#male" role="tab">
                Male Students
                <span class="badge badge-primary">{{ count($maleStudents) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="female-tab" data-toggle="tab" href="#female" role="tab">
                Female Students
                <span class="badge badge-danger">{{ count($femaleStudents) }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="hostelTabsContent">
        {{-- Male Students Tab --}}
        <div class="tab-pane fade show active" id="male" role="tabpanel">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Enrollment No</th>
                        <th>Room No</th>
                        <th>Floor No</th>
                        <th>Student Name</th>
                        <th>Hostel Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maleStudents as $student)
                        <tr>
                            <td>{{ $student->hb_enrollment_no }}</td>
                            <td>{{ $student->hr_room_no }}</td>
                            <td>{{ $student->hf_name }}</td>
                            <td>{{ $student->spi_name }}</td>
                            <td>{{ $student->sc_challan_status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No male students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Female Students Tab --}}
        <div class="tab-pane fade" id="female" role="tabpanel">
            <table class="table table-bordered table-striped">
                <thead class="table-danger">
                    <tr>
                        <th>Enrollment No</th>
                        <th>Room No</th>
                        <th>Floor No</th>
                        <th>Student Name</th>
                        <th>Hostel Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($femaleStudents as $student)
                        <tr>
                            <td>{{ $student->hb_enrollment_no }}</td>
                            <td>{{ $student->hr_room_no }}</td>
                            <td>{{ $student->hf_name }}</td>
                            <td>{{ $student->spi_name }}</td>
                            <td>{{ $student->sc_challan_status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No female students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
