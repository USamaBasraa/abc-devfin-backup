@extends('layouts.app') <!-- Adjust based on your layout -->

@section('content')

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
                    <th colspan="2" style="text-align: center;">Action</th>
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
    function viewMentors(courseId,studentRollNo) {
       // alert(studentRollNo);
       course_id = courseId;
        $('#viewMentors').empty(); 
        $.ajax({
            url: '{{ url('/getCourseMentors') }}/'+courseId, 
            type: 'GET', // Use the appropriate HTTP method (GET, POST, etc.)
            dataType: 'json', // Assuming the server returns JSON data
            success: function(data) {
             
                // Check if data is not empty
                if (data.length > 0) {
                    // Loop through the data and append rows to the table body
                    $.each(data, function(index, item) {
                        var row = `
                            <tr data-toggle="collapse" onclick="getMentorDetails('${item.md_enrollment}')" data-target="#collapse-${item.md_enrollment}" aria-expanded="false" aria-controls="collapse-${item.md_enrollment}" style="cursor: pointer;">
                                <td>${item.md_enrollment}</td>
                                <td>${item.full_name}</td>
                            </tr>
                            <!-- Collapsible Details Row -->
                            <tr id="collapse-${item.md_enrollment}" class="collapse">
                                <td colspan="2">
                                    <div class="p-3">
                                        <div class="row">
                                            <!-- First Column -->
                                            <div class="col-6">
                                                <div id="mentor-details${item.md_enrollment}">
                                                    <h2>Mentor Availability</h2>
                                                    <table class="table table-striped table-hover table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Week Day</th>
                                                                <th>From Time</th>
                                                                <th>To Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="details-table-2${item.md_enrollment}">
                                                            <!-- Mentor details rows will be dynamically added here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Second Column -->
                                            <div class="col-6">
                                                <div id="additional-info${item.md_enrollment}">
                                                    <h2>${studentRollNo} Availability</h2>
                                                    <table class="table table-striped table-hover table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Week Day</th>
                                                                <th>From Time</th>
                                                                <th>To Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="details-table-3${item.md_enrollment}">
                                                            <!-- Mentor details rows will be dynamically added here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        `;
                        // Append the row to the table body
                        $('#viewMentors').append(row);
                    });
                } else {
                    // If no data is found, append "No data found" row
                    var noDataRow = `
                        <tr>
                            <td colspan="5" class="text-center">No data found.</td>
                        </tr>
                    `;
                    $('#viewMentors').append(noDataRow);
                }
            },
            error: function(xhr, status, error) {
                // Handle error if the request fails
                console.error("Error fetching data: " + error);
            }
        });
    }  

    function getMentorDetails(enrollment) {
      //  alert(courseId);
    $.ajax({
        url: '/get-mentor-details',
        method: 'GET',
        data: { mentor_enrollment: enrollment,
                student_enrollment: student_enrollment


         },
        success: function(data) {
            // Handle mentor details table
            const detailsTable = $(`#details-table-2${enrollment}`);
            detailsTable.empty(); // Clear previous details
            // alert(data.mentorDetails[0].week_day);
            // Populate mentor details table
            $.each(data.mentorDetails, function(index, detail) {
                const row = `
                    <tr>
                        <td>${detail.week_day}</td>
                        <td>${detail.from_time}</td>
                        <td>${detail.to_time}</td>
                    </tr>
                `;
                detailsTable.append(row);
            });

            $(`#mentor-details${enrollment}`).show(); // Show the mentor details section

           // Handle timetable slots table
const detailsTable3 = $(`#details-table-3${enrollment}`);
detailsTable3.empty(); // Clear previous details

// Get the days from mentorDetails for comparison
const mentorDays = data.mentorDetails.map(detail => detail.week_day);

// Populate timetable slots table with available slots that match both day and time range
$.each(data.studentSlots, function(index, detail3) {
    // Check for a match in day
    //const isDayMatching = mentorDays.includes(detail3.tt_subject_day);

    // Check for a match in day and time range
    const isSlotMatching = data.mentorDetails.some(detail => {
        // Parse times as integers for comparison
        const mentorFrom = parseInt(detail.from_time, 10);
        const mentorTo = parseInt(detail.to_time, 10);
        const subjectFrom = parseInt(detail3.tt_subject_time_from, 10);
        const subjectTo = parseInt(detail3.tt_subject_time_to, 10);

        // Check if time ranges overlap and days match
        return (
            detail.week_day === detail3.tt_subject_day 

        
            // detail.week_day === detail3.tt_subject_day && // Match day
            // (mentorFrom == subjectTo || mentorFrom == subjectFrom) // No overlap
        );
    });

    // Append rows that match both the day and time range
    if (isSlotMatching) {
        const row = `
            <tr>
                <td>${detail3.tt_subject_day}</td>
                <td>${detail3.tt_subject_time_from}</td>
                <td>${detail3.tt_subject_time_to}</td>
            </tr>
        `;
        detailsTable3.append(row);
    }
});

$(`#additional-info${enrollment}`).show(); // Show additional information section

            // Add a button to send the selected slot intimation
            const sendButton = `
                <button id="send-intimation-btn-${enrollment}" class="btn btn-primary">
                    Send Intimation
                </button>
            `;
            detailsTable3.after(sendButton);

            // Handle send intimation button click
            $(`#send-intimation-btn-${enrollment}`).on('click', function() {
               // const selectedSlot = $(`input[name="slotSelection${enrollment}"]:checked`).val();
                //  alert(student_enrollment,enrollment);
                // alert(course_id);
                //  alert(enrollment);
                // if (!selectedSlot) {
                //     alert('Please select a slot before sending intimation.');
                //     return;
                // }

                // Send the selected slot via AJAX
                $.ajax({
                    url: '/send-intimation',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Include CSRF token
                        mentor_enrollment: enrollment,
                        student_enrollment: student_enrollment,
                        course_id: course_id
                        // slot: selectedSlot

                    },
                    success: function(response) {
                        alert('Intimation sent successfully!');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error sending slot intimation:', error);
                        alert('Failed to send intimation. Please try again.');
                    }
                });
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching mentor details:', error);
            alert('Failed to load mentor details. Please try again later.');
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
