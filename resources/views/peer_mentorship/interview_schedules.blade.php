@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Interview Schedules</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Enrollment No</th>
                <th>Date</th>
                <th>Interview Time</th>
                <th>Status</th>
                <th width="180">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
            <tr>
                <td>{{ $schedule->id }}</td>
                <td>{{ $schedule->is_enrollment_no }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->is_interview_date)->format('d-M-y') }}</td>
                {{-- <td>{{ $schedule->is_interview_date }}</td> --}}
                <td>{{ $schedule->is_interview_time }}</td>
                <td>
                    @if($schedule->interviewed ?? false)
                        <span class="badge bg-success">Interviewed</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td>
                    @if(!($schedule->interviewed ?? false))
                        <form action="{{ route('interview_schedules.mark', $schedule->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                Mark as Interviewed
                            </button>
                        </form>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled>Done</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No schedules found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
