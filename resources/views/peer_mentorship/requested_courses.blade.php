@extends('layouts.app') <!-- Adjust based on your layout -->

@section('content')
<style>
    .on-hold-row {
        background-color: #FFF3CD !important; /* light yellow */
    }
</style>

<div class="card">
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Requested Courses from <?=strtoupper($enrollment)?></h3>

    </div>
    <div class="card-body">
        @csrf
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>

                    <th>Faculty Name</th>
                    <th>Request Mentor</th>
                    <th>Reason</th>
                    <th>Assign Mentor</th>
                    <th colspan="2" style="text-align: center;">Action</th>
                    <th>On Hold</th>

                    <!-- <th>Dean Approval</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td>{{ $course->sc_course_code }}</td>
                        <td>{{ $course->sc_course_title }}</td>

                        <td>{{ $course->sc_faculty_name }}</td>
                        <td>
                            {{$course->mrd_student_request}}
                        </td>
                        <td>
                            {{ $course->mrd_student_request }}
                        </td>
                        <td>
                            {{ $course->mentor_id }}
                        </td>
                        <!-- <td>
                            {{ $course->mrd_dean_approval  }}
                        </td> -->
                        <td>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" onclick="viewMentors('{{ $course->sc_course_id }}','<?=$enrollment?>')">View Mentors</button>
                        </td>
                        <td>


                            {{-- <button type="submit" class="btn btn-primary">View Mentors</button> --}}
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" onclick="viewGrades('<?=$course->sc_co_id?>','<?=$course->sc_course_title?>','<?=$course->mrd_enrollment?>')">View Details
                            </button>
                        </td>

                        <td>
                            @if($course->mrd_status == 'H')
                                <button class="btn btn-success btn-sm" 
                                        onclick="removeHold('{{ $course->mrd_enrollment }}', '{{ $course->sc_course_id }}')"
                                        id="btn-{{ $course->sc_course_id }}">
                                    Remove Hold
                                </button>
                            @else
                                <button class="btn btn-warning btn-sm" 
                                        onclick="putOnHold('{{ $course->mrd_enrollment }}', '{{ $course->sc_course_id }}')"
                                        id="btn-{{ $course->sc_course_id }}">
                                    On Hold
                                </button>
                            @endif
                        </td>
                        
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Response Message -->
        <div id="responseMessage" class="mt-3"></div>
    </div>
</div>

<div class="modal" id="myModal" style="padding:0px !important;width: 100%; margin-left: 0px; margin-right: 0px;">
    <div class="modal-dialog modal-lg" style="padding: 0px !important;width: 100%;">
        <div class="modal-content" style=" width: 100%;">
        <!-- Modal Header -->

        <!-- Modal body -->

            <div id="loading" class="preloader3 loader-block">
                <div class="circ1"></div>
                <div class="circ2"></div>
                <div class="circ3"></div>
                <div class="circ4"></div>
            </div>
            <div id="marks_details"></div>

        </div>
    </div>
</div>


<div class="modal" id="myModal2" style="padding:0px !important;width: 100%; margin-left: 0px; margin-right: 0px;">
    <div class="modal-dialog modal-lg" style="padding: 0px !important;width: 100%;">
        <div class="modal-content" style=" width: 100%;">
            <div>
                <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-family:'Times New Roman', Times, serif;">Mentors List</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Enrollment</th>
                                <th>Full Name</th>
                                <th>Available Hours</th>
                            </tr>
                        </thead>
                        <tbody id="viewMentors">
                            <!-- Dynamic content will be appended here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('javascript')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

var student_enrollment = "<?php echo $enrollment; ?>";
var course_id =0;
var course_id = '';
var student_enrollment = '';

function viewMentors(courseId, studentRollNo) {
    course_id = courseId;
    student_enrollment = studentRollNo;

    $('#viewMentors').empty();

    $.ajax({
        url: '{{ url('/getCourseMentors') }}/' + courseId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.length > 0) {
                $.each(data, function(index, item) {
                    var row = `
                        <tr data-toggle="collapse" onclick="getMentorDetails('${item.md_enrollment}','${studentRollNo}')" data-target="#collapse-${item.md_enrollment}" aria-expanded="false" aria-controls="collapse-${item.md_enrollment}" style="cursor: pointer;">
                            <td>${item.md_enrollment}</td>
                            <td>${item.full_name}</td>
                            <td>${item.m_monthly_hours}</td>
                        </tr>

                        <tr id="collapse-${item.md_enrollment}" class="collapse">
                            <td colspan="3">
                                <div class="p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <div id="mentor-details${item.md_enrollment}">
                                                <h3>Mentor Availability</h3>
                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Action</th>
                                                            <th>Week Day</th>
                                                            <th>From Time</th>
                                                            <th>To Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="details-table-2${item.md_enrollment}"></tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div id="additional-info${item.md_enrollment}">
                                                <h3>${studentRollNo} Occupied Class</h3>
                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Week Day</th>
                                                            <th>From Time</th>
                                                            <th>To Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="details-table-3${item.md_enrollment}"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <div id="buttonDiv${item.md_enrollment}"></div>
                                        </div>
                                        <div class="col-6" style="display:none;">
                                            <div id="buttonDivTimetable${item.md_enrollment}"></div>
                                        </div>
                                    </div>

                                    <div class="row mt-2" style="display:none;">
                                        <input type="checkbox" id="toggleDiv-${item.md_enrollment}"/>
                                        <label for="toggleDiv-${item.md_enrollment}"> If no timetable matches, click here for custom timetable suggestion.</label>
                                    </div>

                                    <div class="row" id="secondDiv-${item.md_enrollment}" style="display:none;">
                                        <div class="col-12">
                                            <div class="accordion-body">
                                                <div id="weekdaysTimingContainer${item.md_enrollment}"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    `;
                    $('#viewMentors').append(row);
                });
            } else {
                $('#viewMentors').append(`
                    <tr><td colspan="5" class="text-center">No data found.</td></tr>
                `);
            }
        }
    });
}

$(document).on('change', '[id^="toggleDiv-"]', function () {
    const id = $(this).attr('id').split('-')[1];
    if ($(this).is(':checked')) {
        $(`#secondDiv-${id}`).show();
    } else {
        $(`#secondDiv-${id}`).hide();
    }
});


function putOnHold(enrollment, courseId) {
    if (!confirm("Are you sure you want to put this course on hold?")) {
        return;
    }

    $.ajax({
        url: "{{ url('/pm-cr-put-on-hold') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            enrollment: enrollment,
            course_id: courseId
        },
        success: function (response) {
            alert(response.message);
            location.reload();
        },
        error: function () {
            alert("Failed to update. Try again.");
        }
    });
}


function removeHold(enrollment, courseId) {

$.ajax({
    url: "{{ url('/pm-cr-remove-hold') }}",
    method: "POST",
    data: {
        _token: "{{ csrf_token() }}",
        enrollment: enrollment,
        course_id: courseId
    },
    success: function (response) {

        // Remove row color
        $("#row-" + courseId).removeClass("on-hold-row");

        // Change button back to On Hold
        $("#btn-" + courseId)
            .removeClass("btn-success")
            .addClass("btn-warning")
            .text("On Hold")
            .attr("onclick", "putOnHold('" + enrollment + "', '" + courseId + "')");

        alert(response.message);
    },
    error: function () {
        alert("Failed to remove hold. Try again.");
    }
});
}




function getMentorDetails(enrollment, stdRoll) {

    $.ajax({
        url: '/get-mentor-details',
        method: 'GET',
        data: {
            mentor_enrollment: enrollment,
            student_enrollment: stdRoll
        },

        success: function(data) {

            const detailsTable = $(`#details-table-2${enrollment}`);
            detailsTable.empty();

            const table3 = $(`#details-table-3${enrollment}`);
            table3.empty();

            $(`#weekdaysTimingContainer${enrollment}`).empty();
            $(`#buttonDiv${enrollment}`).empty();

            $.each(data.mentorDetails, function(index, detail) {

                let bgColor = 'green';
                let isBusy = false;

                $.each(data.studentSlots, function(i, detail3) {
                    if (detail.week_day === detail3.tt_subject_day) {
                        if (parseInt(detail.from_time_id) >= parseInt(detail3.timetable_from_time) &&
                            parseInt(detail.to_time_id) <= parseInt(detail3.timetable_to_time)) {
                            if (detail3.between_exists === 'Busy') {
                                bgColor = 'tomato';
                                isBusy = true;
                            }
                        }
                    }
                });

                const row = `
                    <tr>
                        <td>
                            <input type="radio" ${isBusy ? 'disabled' : ''} name="mentorOption-${enrollment}" value="${detail.week_day},${detail.from_time},${detail.to_time}">
                        </td>
                        <td style="background-color:${bgColor};">${detail.week_day}</td>
                        <td style="background-color:${bgColor};">${detail.from_time}</td>
                        <td style="background-color:${bgColor};">${detail.to_time}</td>
                    </tr>
                `;
                detailsTable.append(row);
            });

            $.each(data.studentSlots, function(index, detail3) {
                const row = `
                    <tr style="background-color:${
                        detail3.between_exists === 'Busy' ? 'tomato' :
                        detail3.between_exists === 'Partially Busy' ? '#FFFF99' :
                        'grey'
                    };">
                        <td>${detail3.tt_subject_day}</td>
                        <td>${detail3.tt_subject_time_from}</td>
                        <td>${detail3.tt_subject_time_to}</td>
                    </tr>
                `;
                table3.append(row);
            });

            const sendButton = `
                <button class="btn btn-primary mt-2" onclick="sendIntimation('${enrollment}')">
                    Send Intimation
                </button>
            `;
            $(`#buttonDiv${enrollment}`).append(sendButton);
        }
    });
}


function sendIntimation(enrollment) {
    const selectedValue = $(`input[name="mentorOption-${enrollment}"]:checked`).val();

    if (!selectedValue) {
        alert("Please select any timeslot.");
        return;
    }

    const [weekDay, fromTime, toTime] = selectedValue.split(',');

    $.ajax({
        url: '/send-intimation',
        method: 'POST',
        data: {
            _token : '{{ csrf_token() }}',
            course_id: course_id,
            mentor_enrollment: enrollment,
            student_enrollment: student_enrollment,
            week_day: weekDay,
            from_time: fromTime,
            to_time: toTime
        },
        success: function() {
            alert("Intimation Sent Successfully!");
            window.location.href = '/intro-session';
        },
        error: function() {
            alert("Failed to send. Try again.");
        }
    });
}

function openTimetable(){



    let studentRollNo = $('#stdRollNo').val();
    let getMentorDetailsRollNo = $('#mentorRollNo').val();
    const data = {
        enrollment: studentRollNo,
        mentorRoll: getMentorDetailsRollNo
    };

    // Perform AJAX POST request
    $.ajax({
        url: '/view-timetable-mentor', // Replace with your desired URL
        type: 'get',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            Authorization: 'Bearer {{ auth()->user()->api_token }}',
            Accept: 'application/json',
        },

        success: function(response) {
           // window.location.href = response.redirect_url;
           const url = '/view-timetable-mentor?enrollment=' + studentRollNo + '&mentorRoll=' + getMentorDetailsRollNo;
           window.open(url, '_blank'); // Opens the URL in a new tab
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error("Error occurred: " + error);
            alert('There was an error processing your request.');
        }
    });
}



function viewGrades(gh_co_id,gh_course_name,roll_no) {
    // alert('1230');
    // alert(gh_course_name);
    $('#loading').show()
    $('#marks_details').html('');
    var co_id = gh_co_id
    var course_name = gh_course_name
    $.ajax({
        type: 'get',
        url: '{{url('/marksdetailMentor')}}/'+co_id+'/'+roll_no,
        success: function(data){
            $('#loading').hide()
            $('#marks_details').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // alert("Status: " + textStatus); alert("Error: " + errorThrown);
            $('#loading').hide()
            $('#marks_details').html('No Record Found');
        }
    });
    }
</script>

@endsection
