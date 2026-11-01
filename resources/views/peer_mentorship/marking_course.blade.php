@extends('layouts.app')

@section('content')
<div class="container">
    {{-- <h1>Gradebook for Enrollment No: {{ $enrollment_no }}</h1> --}}
    <h1>Gradebook</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="mentoringForm" method="POST">
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
                            <input type="checkbox" class="course-checkbox" value="{{ $record->gh_co_id }}" name="course_id[]">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Button to Open Modal -->
        <button type="button" class="btn btn-primary" onclick="openMentorModal()">Mark as Mentored</button>
        <!-- <button type="button" class="btn btn-primary" onclick="">Mark as Interviewed</button> -->

        <!-- Mentor Modal -->
        <div class="modal fade" id="mentorModal" tabindex="-1" aria-labelledby="mentorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mentorModalLabel">Mentor Timetable </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Selecte time Slots</h5>
                        <ul id="selectedCoursesList" class="list-group mb-3" style="display: none;">
                            <!-- List of selected courses will be dynamically populated -->
                        </ul>
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
                                            <!-- Dynamic rows for time slots will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-primary mt-3" id="addRow">Add Weekday and Time</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="hourse" class="form-label">Monthly Hours </label>
                            <input type="number" name="hourse" id="hourse" class="form-control" value="8" placeholder="Hourse per month">
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks">
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <!-- <button type="button" class="btn btn-secondary" onclick="closeMentorModal()" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-success" id="saveChangesButton">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Load Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle with Popper -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('javascript')
<script type="text/javascript">
// Open the modal with selected courses
// function closeMentorModal() {
    // mentorModal.hide();
//     $('#mentorModal').hide();
// }
function openMentorModal() {
    const selectedCourses = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);
    const selectedCoursesList = document.getElementById('selectedCoursesList');

    selectedCoursesList.innerHTML = '';
    if (selectedCourses.length === 0) {
        alert('Please select at least one course.');
        return;
    }

    selectedCourses.forEach(courseId => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.textContent = `Course ID: ${courseId}`;
        selectedCoursesList.appendChild(li);
    });

    const mentorModal = new bootstrap.Modal(document.getElementById('mentorModal'));
    mentorModal.show();
}

// Add a new row for weekdays and timings
document.getElementById('addRow').addEventListener('click', function () {
    const container = document.getElementById('weekdaysTimingContainer');

    const row = document.createElement('div');
    row.className = 'row mb-3 align-items-center';

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

    container.appendChild(row);
});

// Remove dynamically added rows
document.getElementById('weekdaysTimingContainer').addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-row')) {
        e.target.closest('.row').remove();
    }
});

// Handle form submission via AJAX
document.getElementById('saveChangesButton').addEventListener('click', function () {
    const form = document.getElementById('mentoringForm');
    const formData = new FormData(form);

    fetch('{{ route('makeMentor') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message in the modal
            const modalBody = document.querySelector('#mentorModal .modal-body');
            modalBody.innerHTML = `
                <div class="alert alert-success">Data saved successfully.</div>
            `;

            // Hide the modal after a delay
            setTimeout(() => {
                // const mentorModal = bootstrap.Modal.getInstance(document.getElementById('mentorModal'));
                // mentorModal.hide();
                window.location.href = '/mentors_list';
            }, 550); // 1.5-second delay before redirect
        } else {
            const modalBody = document.querySelector('#mentorModal .modal-body');
            modalBody.innerHTML = `
                <div class="alert alert-danger">An error occurred while saving data.</div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const modalBody = document.querySelector('#mentorModal .modal-body');
        modalBody.innerHTML = `
            <div class="alert alert-danger">An unexpected error occurred.</div>
        `;
    });
    // .then(data => {
    //     if (data.success) {
    //         alert('Data saved successfully.');
    //         const mentorModal = bootstrap.Modal.getInstance(document.getElementById('mentorModal'));
    //         mentorModal.hide();
    //         // Optionally, refresh the page or update the UI
    //     } else {
    //         alert('An error occurred while saving data.');
    //     }
    // })
    // .catch(error => {
    //     console.error('Error:', error);
    //     alert('An error occurred while saving data.');
    // });
});

// function closeMentorModal() {
//     myModal.hide();
// }
</script>
@endsection
