@extends('layouts.app')

<style>
/* Custom styles for a compact card layout */
.card {
    border-radius: 10px;
}

.card-body {
    padding: 1rem; /* Reduced padding */
}

.img-fluid.rounded-circle {
    border: 2px solid #007bff;
    width: 120px; /* Smaller image size */
    height: 120px;
}

.icon-text {
    font-size: 0.9rem; /* Smaller font for labels */
}

.shadow-sm {
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.info-box {
    padding: 0.75rem; /* Reduced padding */
    font-size: 0.9rem; /* Reduced font size */
    margin-bottom: 0.5rem; /* Reduced spacing between info boxes */
}

.custom-card-header {
    background-color: #ffffff; /* Neutral background */
    color: #4454c3; /* Theme color for text and icons */
    text-align: center; /* Center alignment */
    font-weight: bold; /* Bold text */
    padding: 1rem; /* Vertical padding */
    font-size: 1.2rem; /* Font size */
    border-bottom: 3px solid #4454c3 !important; /* Ensure the border is present */
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important; /* Subtle shadow */

}

.custom-card-header .icon {
    color: #4454c3; /* Theme color for icons */
    font-size: 1.5rem; /* Slightly larger icons */
    margin-right: 0.5rem; /* Space between icon and text */
}


</style>

@section('content')
    <div class="container my-4">


        <div class="card shadow-sm border-0">
            <!-- Card Header -->
            <div class="card-header custom-card-header">
                <i class="fa fa-user mr-2" style="color: #4454c3; font-size: 1.5rem;"></i> <!-- Icon with Slightly Bigger Size -->
                <span>Student Info</span>
            </div>


            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Student Image and Name -->
                    <div class="col-md-3 text-center mb-2">
                        <img src="https://cms.bnu.edu.pk/students_pics/{{ strtoupper($studentProfile->enrollment_no) }}.jpg"
                             alt="Student Image"
                             class="img-fluid rounded-circle mb-2">
                        <h5 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1rem;">{{ $studentProfile->student_name }}</h5>
                        <div class="mt-2">
                             <div class="mb-1">
                                <span class="badge badge-info pl-2 pr-2 pt-1 pb-1" style="font-size: 0.75rem;">Status: {{ $studentProfile->student_status }}</span>
                            </div>
                            <div>
                                <span class="badge badge-warning pl-2 pr-2 pt-1 pb-1" style="font-size: 0.75rem;">Semester: {{ $studentProfile->semester_status ?? 'N/A' }}</span>
                            </div>
                        </div>

                    </div>

                    <!-- Information Section -->
                    <div class="col-md-9">
                        <div class="row">
                            <!-- Enrollment Number -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-id-card mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Enrollment No:</span>
                                    <strong>{{ $studentProfile->enrollment_no }}</strong>
                                </div>
                            </div>

                            <!-- Father's Name -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-user mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Father's Name:</span>
                                    <strong>{{ $studentProfile->father_name }}</strong>
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-phone mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Mobile:</span>
                                    <strong>{{ $studentProfile->mobile_number }}</strong>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-envelope mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Email:</span>
                                    <strong>{{ $studentProfile->email }}</strong>
                                </div>
                            </div>

                            <!-- School Name -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-building mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">School:</span>
                                    <strong>{{ $studentProfile->school_name }}</strong>
                                </div>
                            </div>

                            <!-- Department Name -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-university mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Department:</span>
                                    <strong>{{ $studentProfile->department_name }}</strong>
                                </div>
                            </div>

                            <!-- Program Name -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-book mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Program:</span>
                                    <strong>{{ $studentProfile->program_name }}</strong>
                                </div>
                            </div>

                            <!-- Degree Level -->
                            <div class="col-sm-6 info-box bg-light rounded d-flex align-items-center">
                                <i class="fa fa-graduation-cap mr-2 text-muted"></i>
                                <div>
                                    <span class="icon-text text-muted">Degree Level:</span>
                                    <strong>{{ $studentProfile->degree_level }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<div class="row">
  <!-- Challan Information Card -->
<div class="col-md-6">
    <div class="card shadow" style="height: 300px; overflow-y: auto;">

        <!-- Card Header -->
        <div class="card-header custom-card-header">
            <span class="h5"> <i class="fa fa-credit-card mr-2"></i> Challan Information</span>
        </div>

        <!-- Card Body -->
        <div class="card-body"  id="challanTableBody">
            @if(count($challans) === 0)
                <div class="alert alert-info text-center w-100">
                    <i class="fa fa-info-circle mr-2"></i>No any challan available yet.
                </div>
            @else
            <div class="table-responsive w-100">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="position: sticky; top: 0; background-color: #fff;">Session</th>
                            <th style="position: sticky; top: 0; background-color: #fff;">Type</th>
                            <th style="position: sticky; top: 0; background-color: #fff;">Invoice</th>
                        
                  
                            <th style="position: sticky; top: 0; background-color: #fff;">Challan Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($challans as $challan)
                        <tr>
                            <td>{{ $challan->sess_name }}</td>
                            <td>{{ $challan->sc_challan_type }}</td>
                            <td>{{ number_format($challan->invoiceamount, 2) }}</td>
                     
                            <td>
                                <span style="width:100px;" class="badge badge-{{ $challan->sc_challan_status == 'UNPAID' ? 'danger' : ($challan->sc_challan_status == 'PAID' ? 'success' : 'secondary') }}">
                                    {{ $challan->sc_challan_status == 'UNPAID' ? 'Unpaid' : ($challan->sc_challan_status == 'PAID' ? 'Paid' : $challan->sc_challan_status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

    <!-- Warning Letters Card -->
    <div class="col-md-6">
    <div class="card shadow" style="height: 300px; overflow-y: auto;">
            <!-- Card Header -->
            <div class="card-header custom-card-header">

                <span class="h5">    <i class="fa fa-exclamation-triangle mr-2"></i>       Warning Letters</span>
            </div>

            <!-- Card Body -->
            <div class="card-body " style="overflow-y: auto;">
                @if(count($warningLetters) > 0)
                    <div class="table-responsive w-100">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col">Warning Type</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Warning Given By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warningLetters as $element)
                                <tr style="{{ $element->status == 'D' ? 'text-decoration: line-through; color: gray;' : '' }}">
                                    <td>{{ $element->warning_letter_name ?? $element->swl_filename }}</td>
                                    <td>{{ \Carbon\Carbon::parse($element->entry_timestamp)->format('M d, Y, h:i A') }}</td>
                                    <td>{{ $element->full_name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center w-100">
                        <i class="fa fa-info-circle mr-2"></i>No any warning letter found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">



<div class="col-md-6">

    <div class="card shadow" style="max-height: 500px; overflow-y: auto;">
        <!-- Card Header -->
        <div class="card-header custom-card-header" style="position: sticky; top: 0; background-color: #fff; z-index: 10;">
            <i class="fa fa-graduation-cap mr-2"></i>Grading
        </div>
        


        <!-- Card Body -->
        <div class="card-body">
            @if($enrollmentNo)  {{-- Assume $enrollmentNo is passed to the view --}}
                <iframe src="/assets/cms/reports/student_transcripts_report_weak.php?sel_enrollment_no={{ $enrollmentNo }}&pt_sai_enroll_no={{ $enrollmentNo }}"
                        style="width:100%; height:600px;" frameborder="0">
                    This browser does not support PDFs. Please download the PDF to view it: <a href="/assets/cms/reports/student_transcripts_report_weak.php?sel_enrollment_no={{ $enrollmentNo }}&pt_sai_enroll_no={{ $enrollmentNo }}">Download PDF</a>.
                </iframe>
            @else
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i>No enrollment number provided.
                </div>
            @endif
        </div>
    </div>
</div>






{{-- attendance  --}}
<div class="col-md-6">

    <div class="card shadow" style="max-height: 500px; overflow-y: auto;">
        <!-- Card Header -->
        <div class="card-header custom-card-header" style="position: sticky; top: 0; background-color: #fff; z-index: 10;">
            <i class="fa fa-graduation-cap mr-2"></i>Attendance
        </div>


        <div class="card-body">
            @if(isset($attendanceDetails) && count($attendanceDetails) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Course Name</th>
                                <th>Faculty</th>
                                <th>Total Classes Held</th>
                                <th>Total Presents</th>
                                <th>Absents</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceDetails as $attendance)
                                <tr>
                                    <td>{{ $attendance->course_name }}</td>
                                    <td>{{ $attendance->faculty }}</td>
                                    <td class="text-right">{{ $attendance->total_classes }}</td>
                                    <td class="text-right">{{ $attendance->total_presents }}</td>
                                    <td class="text-right">{{ $attendance->absents }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i>No attendance data found for the given enrollment number.
                </div>
            @endif
        </div>

    </div>

</div>


<div class="col-md-12">

        <div class="card shadow" style="max-height: 500px; overflow-y: auto;">
                <div class="container">
                <h3 class="mb-4">Timetable</h3>

                @if($timetable->isEmpty())
                    <p>No courses found for the selected enrollment number.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course Code Name</th>
                                <th>Teacher Name</th>
                                <th>Subject Day</th>
                                <th>Subject Time From</th>
                                <th>Subject Time To</th>
                                <th>Enrollment No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timetable as $course)
                            <tr>
                                <td>{{ $course->course_cd_name }}</td>
                                <td>{{ $course->teachername }}</td>
                                <td>{{ $course->tt_subject_day }}</td>
                                <td>{{ $course->tt_subject_time_from }}</td>
                                <td>{{ $course->tt_subject_time_to }}</td>
                                <td>{{ $course->sc_sai_enrollment_no }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

      </div>
  </div>





</div>







</div>




    </div>
@endsection




<script>

window.onload = function() {
        var element = document.getElementById('challanTableBody');
        element.scrollTop = element.scrollHeight;
    };


   $(document).ready(function() {
    $('#challanTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "pageLength": 2  // This limits the table to display only 2 rows per page.
    });
});
</script>
