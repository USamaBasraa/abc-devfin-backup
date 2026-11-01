@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gradebook for Enrollment No: {{ $enrollment_no }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="mentoringForm" action="{{ route('gradebook.markMentored') }}" method="POST">
        @csrf
        <input type="hidden" name="enrollment_no" value="{{ $enrollment_no }}">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Session Name</th>
                    <th>Course</th>
                    <th>Grade</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gradebook as $key => $record)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $record->session_name }}</td>
                        <td>{{ $record->course }}</td>
                        <td>{{ $record->grade }}</td>
                        <td>
                            <button type="button" onclick="getCoId('{{ $record->gh_co_id }}')" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#mentorModal">
                                Allocate 
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mentor Modal -->
        <div class="modal fade" id="mentorModal" tabindex="-1" aria-labelledby="mentorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mentorModalLabel">Mark as Mentored</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="course_id" name="course_id">
                        <div class="mb-3">
                            <label for="mentoringType" class="form-label">Mentoring Type</label>
                            <select name="mentoring_type" id="mentoringType" class="form-select">
                                <option value="academic">Academic</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="accordion" id="weekdaysAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Add Weekdays and Timings
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#weekdaysAccordion">
                                    <div class="accordion-body">
                                        <div id="weekdaysTimingContainer">
                                            <!-- Dynamic rows will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-primary mt-3" id="addRow">Add Weekday and Time</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('javascript')
<script type="text/javascript">
// Function to set the course ID and open the modal
function getCoId(id) {
    document.getElementById('course_id').value = id;
    document.getElementById('mentorModal').modal = true;
}

// Add an event listener to dynamically add rows for weekdays timing
document.getElementById('addRow').addEventListener('click', function () {
    const container = document.getElementById('weekdaysTimingContainer');
    const courseId = document.getElementById('course_id').value;

    // Create a new row
    const row = document.createElement('div');
    row.className = 'row mb-3 align-items-center';

    // Add the HTML content for the row
    row.innerHTML = `
        <div class="col-md-4">
            <select name="weekdays[]" class="form-control" required>
                <option value="">Select Weekday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
                <option value="7">Sunday</option>
            </select>
        </div>


        <div class="col-md-4">
            <select name="fromtiming[]" class="form-control" required>
                <option value="">Select From Time</option>
                @foreach ($ressqlTimeslots as $row)
                    <option value="{{ $row->ts_id }}">{{ $row->ts_name }}</option>
                @endforeach
            </select>
            <select name="totiming[]" class="form-control" required>
                <option value="">Select To Time</option>
                @foreach ($ressqlTimeslots as $row)
                    <option value="{{ $row->ts_id }}">{{ $row->ts_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-danger remove-row">Remove</button>
        </div>
    `;

    // Append the new row to the container
    container.appendChild(row);
});

// Event delegation to handle dynamic "Remove" buttons
document.getElementById('weekdaysTimingContainer').addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-row')) {
        e.target.closest('.row').remove();
    }
});
</script>
@endsection
