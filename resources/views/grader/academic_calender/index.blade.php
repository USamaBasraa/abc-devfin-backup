@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ isset($editData) ? 'Edit' : 'Add' }} Academic Year</h4>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ isset($editData) ? url('api/academic_session_update', $editData->id) : url('api/academic_session_store') }}"
                  id="eventForm">
                @csrf
                @if(isset($editData))
                    @method('POST')
                @endif

                <div class="form-group">
                    <label class="required">Academic Year</label>
                    <select name="academic_year_id" id="academic_year" class="form-control" required>
                        <option value="">-- Select Academic Year --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->ay_id }}"
                                @if((isset($editData) && $editData->academic_year_id == $year->ay_id) || old('academic_year_id') == $year->ay_id)
                                    selected
                                @endif>
                                {{ $year->ay_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="required">Session</label>
                    <select name="session_id" id="session_id" class="form-control" required>
                        <option value="">-- Select Session --</option>
                        @if(isset($editData) && $sessions)
                            @foreach($sessions as $session)
                                <option value="{{ $session->sess_id }}" {{ $editData->session_id == $session->sess_id ? 'selected' : '' }}>
                                    {{ $session->sess_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="calendar_id">Select Calendar</label>
                    <select id="calendar_id" name="calendar_id" class="form-control" required>
                        <option value="">-- Select Calendar --</option>
                        @foreach($calendars as $calendar)
                            <option value="{{ $calendar->c_id }}">{{ $calendar->c_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="calendar-details-section" style="display: none;">
                    <h5 class="mt-4">Calendar Events & Dates</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Event Title</th>
                                <th>Pick Start Date</th>
                                <th>Pick End Date</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-details-body">
                            {{-- Populated by jQuery --}}
                        </tbody>
                    </table>

                </div>
                <div class="form-group d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">
                        {{ isset($editData) ? 'Update Event' : 'Save Event' }}
                    </button>
                    @if(isset($editData))
                        <a href="{{ url('api/academic_session_store') }}" class="btn btn-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Events Table --}}
    <div class="card shadow-sm">
        {{-- <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Academic Session Events</h5>
        </div> --}}
@php
    // Group by description + session
    $groupedEvents = $details->groupBy(function ($item) {
        return $item->description . '||' . $item->session_name;
    });
@endphp

@forelse($groupedEvents as $groupKey => $events)
    @php
        [$description, $session] = explode('||', $groupKey);
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ $description ?? 'No Description' }}
                <small class="text-muted">– Session: {{ $session ?? 'N/A' }}</small>
            </h5>
            <a  class="btn btn-sm btn-light">
                Generate PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Academic Year</th>
                            <th>Session</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            {{-- <th>Description</th> --}}
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $d)
                            <tr>
                                <td>{{ $d->year_name ?? 'N/A' }}</td>
                                <td>{{ $d->session_name ?? 'N/A' }}</td>
                                <td>{{ $d->event_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($d->from_date)->format('d-m-Y') }}</td>
                                <td>{{ $d->end_date }}</td>
                                {{-- <td>{{ \Illuminate\Support\Str::limit($d->description, 50) }}</td> --}}
                                <td class="d-flex">
                                   
                                   
                                   
                                    <button type="button" class="btn btn-info btn-sm mr-2 edit-date-btn"
                                            data-event-id="{{ $d->id }}"
                                            data-event-name="{{ $d->event_name }}"
                                            data-old-start-date="{{ \Carbon\Carbon::parse($d->from_date)->format('Y-m-d') }}"
                                            data-old-end-date="{{$d->end_date }}">
                                                Edit Date
                                    </button>
                            
                                   
                                   
                                    {{-- <button type="button" class="btn btn-info btn-sm mr-2 edit-date-btn"
                                            data-event-id="{{ $d->id }}"
                                            data-event-name="{{ $d->event_name }}"
                                            data-old-date="{{ \Carbon\Carbon::parse($d->from_date)->format('Y-m-d') }}">
                                        Edit Date
                                    </button> --}}
                                    
                                    <button type="button" onclick="deleteRec({{ $d->id }})" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info text-center">No events found</div>
@endforelse


    </div>
</div>
<script>


</script>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#eventForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(form[0]); // includes all form inputs

            // Validate From/End date if needed
            let fromDate = new Date($('#from_date').val());
            let endDate = new Date($('#end_date').val());
            if ($('#end_date').val() && endDate < fromDate) {
                Swal.fire('Invalid Date', 'End date cannot be before start date', 'warning');
                return false;
            }

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
                },
                success: function (response) {
                    Swal.fire('Success', 'Event saved successfully!', 'success').then(() => {
                        window.location.href = '/academic-session'; // or your desired route
                    });
                },
                error: function (xhr) {
                    let msg = 'Something went wrong!';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    form.find('button[type="submit"]').prop('disabled', false).text('{{ isset($editData) ? "Update Event" : "Save Event" }}');
                }
            });
        });
    });
    </script>

<script>
    $(document).ready(function () {
        $('#calendar_id').on('change', function () {
            let calendarId = $(this).val();
            if (calendarId) {
                $.ajax({
                    url: 'api/get-calendar-details/' + calendarId,
                    type: 'GET',
                    success: function (data) {
                        let html = '';
                        data.forEach(function (item) {
                            html += `
                                <tr>
                                    <td>${item.cd_title}</td>
                                    <td><input type="date" name="start_dates[${item.cd_id}]" class="form-control" required></td>
                                    <td><input type="date" name="end_dates[${item.cd_id}]" class="form-control" ></td>

                                </tr>
                            `;
                        });
                        $('#calendar-details-body').html(html);
                        $('#calendar-details-section').slideDown();
                    },
                    error: function () {
                        alert('Error loading calendar details');
                        $('#calendar-details-body').html('');
                        $('#calendar-details-section').hide();
                    }
                });
            } else {
                $('#calendar-details-body').html('');
                $('#calendar-details-section').hide();
            }
        });

        // Academic year change triggers session load
        $('#academic_year').on('change', function () {
            let yearId = $(this).val();
            let $sessionSelect = $('#session_id');

            $sessionSelect.prop('disabled', true).html('<option value="">Loading...</option>');

            if (yearId) {
                $.ajax({
                    url: '{{ url("api/get-sessions") }}',
                    type: 'POST',
                    data: {
                        academic_year_id: yearId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $sessionSelect.html('<option value="">-- Select Session --</option>');
                        $.each(data, function (key, session) {
                            $sessionSelect.append('<option value="' + session.sess_id + '">' + session.sess_name + '</option>');
                        });

                        @if(isset($editData))
                            $sessionSelect.val('{{ $editData->session_id }}');
                        @endif

                        @if(old('session_id'))
                            $sessionSelect.val('{{ old('session_id') }}');
                        @endif
                    },
                    error: function () {
                        $sessionSelect.html('<option value="">Error loading sessions</option>');
                    },
                    complete: function () {
                        $sessionSelect.prop('disabled', false);
                    }
                });
            } else {
                $sessionSelect.html('<option value="">-- Select Session --</option>');
                $sessionSelect.prop('disabled', false);
            }
        });

        @if(isset($editData) || old('academic_year_id'))
            $('#academic_year').trigger('change');
        @endif
    });

    $(document).on('click', '.edit-date-btn', function () {
    const eventId = $(this).data('event-id');
    const eventName = $(this).data('event-name');
    const oldStartDate = $(this).data('old-start-date');
    const oldEndDate = $(this).data('old-end-date');

    Swal.fire({
        title: `Edit Dates for "${eventName}"`,
        html: `
           
           
            <input type="date" id="newStartDate" class="swal2-input" value="${oldStartDate}">
             <p>Old Start Date: <strong>${oldStartDate}</strong></p>
            <br>
            
            <input type="date" id="newEndDate" class="swal2-input" value="${oldEndDate}">
            <p>Old End Date: <strong>${oldEndDate}</strong></p>
        `,
        confirmButtonText: 'Update Dates',
        showCancelButton: true,
        preConfirm: () => {
            const newStartDate = $('#newStartDate').val();
            const newEndDate = $('#newEndDate').val();
            if (!newStartDate ) {
                Swal.showValidationMessage('Please select start  date');
                return false;
            }
            if (newEndDate) {
            if (newStartDate > newEndDate) {
                Swal.showValidationMessage('Start date cannot be after end date');
                return false;
            }
        }
            return { newStartDate, newEndDate };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/update-date/${eventId}`, // Your backend route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    start_date: result.value.newStartDate,
                    end_date: result.value.newEndDate
                },
                success: function (response) {
                    Swal.fire('Updated!', 'Dates have been updated.', 'success')
                        .then(() => location.reload());
                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
});

function deleteRec(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the event.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/api/academic-session-delete', // Ensure this matches your Laravel route
                type: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire('Deleted!', 'Event deleted successfully.', 'success').then(() => {
                        window.location.href = '/academic-session'; // Redirect or refresh
                    });
                },
                error: function (xhr) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}


</script>
<style>
    .required:after {
        content: " *";
        color: red;
    }
    .form-group label {
        font-weight: 500;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endsection
