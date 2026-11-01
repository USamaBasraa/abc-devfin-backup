@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        <div align="center"><h4>Enrolled Courses List</h4></div>
    </div>

    <div class="card-block">

        {{-- 🔹 Input Section for Enrollment No and Day --}}
        <div class="mb-3 text-center">
            <input type="text" id="enrollment_no" class="form-control d-inline-block" placeholder="Enter Enrollment No" style="width: 250px;">

            <select id="day_filter" class="form-control d-inline-block" style="width: 200px;">
                <option value="">Select Day (optional)</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>

            <button id="loadCoursesBtn" class="btn btn-primary">Load Courses</button>
        </div>

        {{-- 🔹 Table Container --}}
        <div class="dt-responsive table-responsive" id="enrolled_course_table" style="min-height: 300px;">
            <p class="text-center text-muted">Please enter an enrollment number and optionally select a day to view courses.</p>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
$(document).ready(function() {

    // 🔹 When button is clicked, fetch courses
    $('#loadCoursesBtn').click(function() {
        let enrollmentNo = $('#enrollment_no').val().trim();
        let selectedDay  = $('#day_filter').val();

        if (enrollmentNo === '') {
            alert('Please enter an enrollment number.');
            return;
        }

        $('#enrolled_course_table').html('<p class="text-center text-muted">Loading...</p>');

        // Build query parameters
        let queryParams = '?enrollment_no=' + encodeURIComponent(enrollmentNo);
        if (selectedDay) {
            queryParams += '&day=' + encodeURIComponent(selectedDay);
        }

        $.ajax({
            method: "GET",
            url: "{{ url('/api/get-student-enrolled-courses') }}" + queryParams,
            headers: { "Authorization": 'Bearer {{ auth()->user()->api_token }}' }
        }).done(function(res) {
            if (res.status) {
                let notiBody  = '<table id="simpletable" class="table table-bordered table-hover table-responsive wrap">';
                notiBody     += '<thead class="table-info"><tr>'
                              + '<th>Sr</th><th>Session</th><th>Course Name</th><th>Section</th><th>Credit Hr.</th>'
                              + '<th>Teacher Name</th><th>Status</th><th>Day</th><th>Time From</th><th>Time To</th>'
                              + '<th>Building</th><th>Room</th></tr></thead><tbody>';

                let srno = 1, parent_co_id = 0;
                let dayFilter = $('#day_filter').val(); // get selected day

$(res.data).each(function(index, val) {
    notiBody += '<tr>';

    // If filtering by day, skip rowspan logic (render all columns)
    if (dayFilter) {
        notiBody += '<td>' + srno + '</td>';
        notiBody += '<td>' + val.sess_name + '</td>';
        notiBody += '<td>' + val.course_cd_name + '</td>';
        notiBody += '<td>' + val.sc_pc_section + '</td>';
        notiBody += '<td>' + val.sc_pc_course_cr + '</td>';
        notiBody += '<td>' + val.teachername + '</td>';
        notiBody += '<td>' + val.sc_status_show + '</td>';
        srno++;
    } else {
        // original rowspan logic for full view
        if (parent_co_id != val.sc_co_id) {
            notiBody += '<td rowspan="' + val.total_rows + '">' + srno + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.sess_name + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.course_cd_name + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.sc_pc_section + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.sc_pc_course_cr + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.teachername + '</td>';
            notiBody += '<td rowspan="' + val.total_rows + '">' + val.sc_status_show + '</td>';
            parent_co_id = val.sc_co_id;
            srno++;
        }
    }

    // Always show these columns
    notiBody += '<td>' + val.tt_subject_day + '</td>';
    notiBody += '<td>' + val.tt_subject_time_from + '</td>';
    notiBody += '<td>' + val.tt_subject_time_to + '</td>';
    notiBody += '<td>' + val.building_name + '</td>';
    notiBody += '<td>' + val.room_name + '</td>';
    notiBody += '</tr>';
});

                notiBody += '</tbody></table>';
                $('#enrolled_course_table').html(notiBody);
            } else {
                $('#enrolled_course_table').html('<p class="text-center text-danger">'+res.message+'</p>');
            }
        }).fail(function(err) {
            console.log(err);
            $('#enrolled_course_table').html('<p class="text-center text-danger">Error fetching data.</p>');
        });
    });

    // 🔹 Allow pressing Enter to trigger the same action
    $('#enrollment_no').keypress(function(e) {
        if (e.which == 13) {
            $('#loadCoursesBtn').click();
        }
    });

});
</script>
@endsection
