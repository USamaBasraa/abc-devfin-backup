@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')
@include('components.toast-notifications')
  <style>
    body {
      background: #f1f5fb;
      font-family: Arial, sans-serif;
    }
    .form-container {
      background: white;
      border-radius: 20px;
      padding: 30px;
      max-width: 1200px;
      margin: 50px auto;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #1d3f94;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .btn-submit {
      background-color: #007bff;
      color: white;
    }
    .btn-reset {
      background-color: #ffc107;
      color: black;
    }
    #roadmapTable th, #roadmapTable td {
        text-align: center;
        vertical-align: middle;
    }
    #success-message, #error-messages {
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
    }
  </style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid py-4">
    <ul class="nav nav-pills gap-2" id="quotationTabs2" role="tablist">
        <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 py-2" id="roadmapWiseOffering2-tab" data-toggle="tab" href="#roadmapWiseOffering2" role="tab">Roadmap Wise Offering</a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 py-2" id="studentWiseOffering2-tab" data-toggle="tab" href="#studentWiseOffering2" role="tab">Student Wise Offering</a>
        </li>
    </ul>
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="roadmapWiseOffering2" role="tabpanel">
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0 text-dark">Roadmap Wise Course Offering</h4>
                </div> 
                <form id="formCourse">
                    <input type="hidden" id="formId" name="formId">
                    <div class="form-row mb-3">
                        <div class="form-group col-md-4">
                            <label for="session">Session</label>
                            <select id="session" class="form-control">             
                                <option value="">Select Session</option>
                                @foreach($sessions as $row)
                                    <option value="{{ $row->sess_id }}"> 
                                        
                                        {{ $row->sess_name }}
                                    </option>
                                @endforeach          
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="session">Roadmap Session</label>
                            <select id="roadmap_session" class="form-control">             
                                <option value="">Select Session</option>
                                @foreach($sessions as $row)
                                    <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                @endforeach          
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="schools">Schools</label>
                            <select id="schools" name="schools" class="form-control select2">
                                <option value="">Select Degree</option>
                                @foreach($activeSchools as $row)
                                    <option value="{{ $row->school_id }}">{{ $row->school_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="departments">Departments</label>
                            <select id="departments" name="departments" class="form-control select2">
                                <option value="">Select Department</option>
                                @foreach($activeDeparments as $row)
                                    <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="programs">Program</label>
                            <select id="programs" name="programs" class="form-control select2">
                                <option value="">Select Program</option>
                                @foreach($activePrograms as $row)
                                    <option value="{{ $row->prog_id }}">{{ $row->prog_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="semester">Semester</label>
                            <select id="semester" class="form-control">
                                <option selected>Select Semester</option>
                                <option value="1">I</option>
                                <option value="2">II</option>
                                <option value="3">III</option>
                                <option value="4">IV</option>
                                <option value="5">V</option>
                                <option value="6">VI</option>
                                <option value="7">VII</option>
                                <option value="8">VIII</option>
                                <option value="9">IX</option>
                                <option value="10">X</option>
                                <option value="11">XI</option>
                                <option value="12">XII</option>
                                <option value="13">XIII</option>
                                <option value="14">XIV</option>
                                <option value="15">XV</option>
                                <option value="16">XVI</option>
                                <option value="17">XVII</option>
                                <option value="18">XVIII</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="course">Course</label>
                            <select id="course" name="course[]" class="form-control select2" multiple>
                                
                                <option value="">Select Course</option>

                                <!-- Static Electives -->
                                <optgroup label="Electives">
                                    <option value="123">Elective 1</option>
                                    <option value="456">Elective 2</option>
                                    <option value="789">Elective 3</option>
                                </optgroup>

                                <!-- Static Specializations -->
                                <optgroup label="Specializations">
                                    <option value="111">Specialization 1</option>
                                    <option value="222">Specialization 2</option>
                                    <option value="333">Specialization 3</option>
                                    <option value="444">Specialization 4</option>
                                </optgroup>

                                <!-- Dynamic Courses from DB -->
                                <optgroup label="Courses">
                                    @foreach($masterCourses as $row)
                                    <option value="{{ $row->course_id }}">
                                        {{ $row->course_code }} - {{ $row->course_cd_name }}
                                    </option>
                                    @endforeach
                                </optgroup>

                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">          
                        <div class="form-group col-md-4">
                            <label for="courseType">Course Type</label>
                            <select id="courseType" class="form-control">
                                <option value="" selected>Select Course Type</option>
                                <option value="1">Compulsory</option>
                                <option value="2">Elective</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="theoryHours">Theory Credit Hours</label>
                            <input type="number" id="theoryHours" class="form-control" min="0">
                        </div>
                        <!-- <div class="form-group col-md-2">
                            <label for="practicalHours">Practical Credit Hours</label>
                            <input type="number" id="practicalHours" class="form-control" min="0">
                        </div> -->
                    </div>
                    <div id="studentCount" class="mt-3" >
                        <label for="studentCountValue">Total Students</label>
                        <input type="text" id="studentCountValue" name="student_count" class="form-control" readonly>
                    </div>
                

                    <div id="sectionsDiv" class="mt-3">
                        <label>Sections</label>
                        <div id="sectionsContainer"></div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="addSectionBtn">+ Add Section</button>
                    </div>  
                    <div class="text-center">
                        <button type="button" onclick="sbtBtn()" id="submitBtn" class="btn btn-submit mr-2">Submit</button>
                        <button type="button" onclick="courseOfferedInsertion()" id="submitBtn" class="btn btn-info mr-2">Move Courses to Course Offering Page</button>
                        <!-- <button type="reset" class="btn btn-reset" id="formResetBtn">Reset</button> -->
                        <button type="button" id="newEntryBtn" class="btn btn-secondary" style="display: none;" onclick="resetToNewEntry()">New Entry</button>
                    </div>

                    <div class="mt-3" id="studentsDiv" style="display:none;">
                        <label>Assign Students to Section</label>
                        <div id="studentsContainer"></div>
                    </div>  

                    <!-- Student Modal -->
                    <div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document" style="max-width:75% !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Assign Students to Section</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>                    
                                <!-- scrollable body -->
                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                    <div id="studentsList">
                                        <em>Loading students...</em>
                                    </div>
                                </div>                    
                                <div class="modal-footer">
                                    <button type="button" id="saveStudentsBtn" class="btn btn-primary">Save Selection</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div id="error-messages" class="alert alert-danger" style="display:none;"></div>
                    <div id="success-message" class="alert alert-success" style="display: none;"></div>
                </form>
                <div class="container-fluid py-4">
                    <div class="card shadow-sm border-0">
                        <table id="roadmapTable" class="table table-bordered table-striped">
                            <thead class="">
                                <tr>
                                    <th>Session</th>
                                    <th>RoadMap Session</th>
                                    <th>School</th>
                                    <th>Department</th>
                                    <th>Program</th>
                                    <th>Semester</th>
                                    <th>Course</th>
                                    <th>Course Type</th>
                                    <th>Theory CH</th>
                                    <!-- <th>Practical CH</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="align-middle text-center"></tbody>
                                <!-- Data will be appended here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="studentWiseOffering2" role="tabpanel">
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header bg-light border-bottom">
                    <h4 class="mb-0 text-dark">Student Wise Course Offering</h4>
                </div>                
                <form id="st_formCourse">
                    <!-- Card Body -->
                    <div class="card-body">            
                        <input type="hidden" id="st_formId" name="st_formId">
                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label for="session">Active Session</label>
                                <select id="st_session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}" >                                
                                            {{ $row->sess_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            
                            
                            <div class="form-group col-md-4">
                                <label for="studentEnrolmentNO">Enrollment No</label>
                                <select id="studentEnrolmentNO" class="form-control select2">
                                    <option value="">Select Student</option>
                                    @foreach($resactiveStudents as $row)
                                        <option value="{{ $row->sai_enrollment_no }}">{{ $row->sai_enrollment_no }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <div class="form-group col-md-4">
                                <label for="roadmap_session">Roadmap Session</label>
                                <select id="st_roadmap_session" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>         
                                -->    
                            <div class="form-group col-md-4">                            
                                <button type="button" id="st_roadmap_session" onclick="loadsession()" style="margin-top: 28px;" class="btn btn-primary">
                                    Load Roadmap Session
                                </button>
                            </div>                              
                        </div>
                        
                        
                        <div id="error-messages" class="alert alert-danger mt-3" style="display:none;"></div>
                        <div id="success-message" class="alert alert-success mt-3" style="display:none;"></div>
                    </div>                 

                    <div class="form-row mb-3">
                        <div class="container-fluid py-4" id="sessionTableContainer">
                                                        
                        </div>   
                    </div>  



                    <!-- Card Footer -->
                    <div class="card-footer text-center bg-light border-top">
                            <span id="disclaimerMsg" style="color:red; font-weight:bold; display:block; margin-bottom:8px;display:none">
                            This is the main roadmap for this session. 
                            To create a separate roadmap for this student, please press Submit.
                        </span>
                        <button type="button" onclick="st_sbtBtn()" id="submitBtn" class="btn btn-success mr-2">Submit</button>
                        <button type="button" style="display:none" class="btn btn-primary" id="showBtn" data-toggle="modal" data-target="#courseModal">
                            Add new Roadmap Session Student Wise
                        </button>
                        <button type="button" id="st_newEntryBtn" class="btn btn-secondary" style="display:none;" onclick="resetToNewEntry()">New Entry</button>
                    </div>
                </form>           
                    
            </div>
            <!-- Button to Open Modal -->    
            <!-- Modal -->
            <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="courseModalLabel">Add New Roadmap Session Student Wise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="courseForm">
                            <div class="form-group">
                                <label for="activeSession">Active Session</label>
                                <input type="hidden" name="studentRollNo" id="studentRollNo" >
                                <select id="active_session_new" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $row)
                                        <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="courseName">Course Name</label>
                                    <select id="course_new" class="form-control select2">
                                    <option value="">Select Course</option>

                                    <!-- Static Electives -->
                                    <optgroup label="Electives">
                                        <option value="123">Elective 1</option>
                                        <option value="456">Elective 2</option>
                                        <option value="789">Elective 3</option>
                                        <option value="555">Elective 4</option>
                                        <option value="666">Elective 5</option>
                                        <option value="777">Elective 6</option>
                                    </optgroup>

                                    <!-- Static Specializations -->
                                    <optgroup label="Specializations">
                                        <option value="111">Specialization 1</option>
                                        <option value="222">Specialization 2</option>
                                        <option value="333">Specialization 3</option>
                                        <option value="444">Specialization 4</option>
                                    </optgroup>

                                    <optgroup label="Degree Electives">
                                        <option value="888">Degree Elective 1</option>
                                        <option value="118">Degree Elective 2</option>
                                        <option value="112">Degree Elective 3</option>
                                        <option value="113">Degree Elective 4</option>
                                        <option value="114">Degree Elective 5</option>
                                        <option value="115">Degree Elective 6</option>
                                        <option value="116">Degree Elective 7</option>
                                        <option value="117">Degree Elective 8</option>
                                    </optgroup>

                                    <optgroup label="Minor">
                                        <option value="119">Minor 1</option>
                                        <option value="120">Minor 2</option>
                                        <option value="121">Minor 3</option>
                                        <option value="122">Minor 4</option>                                   
                                    </optgroup>

                                    <!-- Dynamic Courses from DB -->
                                    <optgroup label="Courses">
                                        @foreach($masterCourses as $row)
                                        <option value="{{ $row->course_id }}">
                                            {{ $row->course_code }} - {{ $row->course_cd_name }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    <option value="">Select Course</option>
                                    @foreach($masterCourses as $row)
                                        <option value="{{ $row->course_id }}">{{ $row->course_code }} - {{ $row->course_cd_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <select id="semester_new" class="form-control">
                                    <option selected>Select Semester</option>
                                    <option value="1">I</option>
                                    <option value="2">II</option>
                                    <option value="3">III</option>
                                    <option value="4">IV</option>
                                    <option value="5">V</option>
                                    <option value="6">VI</option>
                                    <option value="7">VII</option>
                                    <option value="8">VIII</option>
                                    <option value="9">IX</option>
                                    <option value="10">X</option>
                                    <option value="11">XI</option>
                                    <option value="12">XII</option>
                                    <option value="13">XIII</option>
                                    <option value="14">XIV</option>
                                    <option value="15">XV</option>
                                    <option value="16">XVI</option>
                                    <option value="17">XVII</option>
                                    <option value="18">XVIII</option>
                                </select>
                            </div>                            
                            <div class="form-group">
                                <label for="course_offering_type">Course Offering Type</label>
                                <select id="course_offering_type_new" class="form-control select2">
                                    <option value="">Select Course Offering Type</option>                       
                                    <option value="1">Regular Course</option>
                                    <option value="2">Project</option>
                                    <option value="3">Thesis</option>
                                    <option value="4">Internship Course</option>                            
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courseType">Course Type</label>
                                <select id="courseType_new" class="form-control">
                                    <option value="" selected>Select Course Type</option>
                                    <option value="1">Compulsory</option>
                                    <option value="2">Elective</option>
                                </select>
                            </div>  

                            <div class="form-group">
                                <label for="theoryHours">Theory Credit Hours</label>
                                <input type="number" id="theoryHours_new" class="form-control" min="0">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveCourseBtn">Save</button>
                    </div>
                    </div>
                </div>
            </div>           
        </div>
    </div>
</div>


   
@stop
@section('javascript')
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">
function courseOfferedInsertion() {
    //  showToast('error', 'This feature is currently under development. Please proceed according to the roadmap.');
    // return false;
    // Disable button to prevent multiple clicks
    // let sessions = $('#sessions').val();
    let schoolID = $('#schools').val();
    $('#submitBtn').prop('disabled', true);

    $.ajax({
        url: '/courses-offered/insert', // Laravel route
        type: 'POST',
        data: {
            
            schoolID: schoolID, // pass dynamically if needed
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
        },
        success: function(response) {
            showToast('success', response.message);
            // alert(response.message);
            $('#submitBtn').prop('disabled', false);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Something went wrong!');
            $('#submitBtn').prop('disabled', false);
        }
    });
}    
$('#courseModal').on('show.bs.modal', function () {
    // Get value from #studentEnrolmentNO
    let enrolNo = $('#studentEnrolmentNO').val();
    // Set it into the Active Session field (or any other field)
    $('#studentRollNo').val(enrolNo);
});
$('#saveCourseBtn').on('click', function() {
    let studentRollNo          = $('#studentRollNo').val();
    let activeSession          = $('#active_session_new').val();
   
    let courseId               = $('#course_new').val();
    let semester               = $('#semester_new').val();
    let courseOfferingType     = $('#course_offering_type_new').val();
    let courseType_new         = $('#courseType_new').val();
    let theoryHours_new        = $('#theoryHours_new').val();

    // Example: Send via AJAX
    $.ajax({
        url: '/save-course', // your route
        type: 'POST',
        data: {
            student_roll_no: studentRollNo,
            active_session: activeSession,           
            course_id: courseId,
            semester: semester,
            course_offering_type: courseOfferingType,
            courseType_new: courseType_new,
            theoryHours_new: theoryHours_new,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showToast('success', 'Course saved successfully!');
           
            $('#courseModal').modal('hide');
            loadsession()
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Error saving course');
        }
    });
});

function loadsession(){ 
    $('#showBtn').hide();
    $('#disclaimerMsg').show();
    selectedCourses = [];
    let sessionId = $('#st_session').val(); 
    let enrollNo = $('#studentEnrolmentNO').val(); // if your input id is different, change it

    let documentLink = `/assets/cms/reports/weak_portal_academic_history_report.php?sel_enrollment_no=${enrollNo}&pt_sai_enroll_no=${enrollNo}`;

    if (sessionId == "") {
        $('#sessionTableContainer').html('');
        return;
    }

    $.ajax({
        url: "/get-session-data",
        type: "GET",
        data: {
            session_id: sessionId,
            enrollNo: enrollNo
        },
        success: function (response) {
            console.log(response);
            if (response.data.length === 0) {
                $('#sessionTableContainer').html('<p class="text-danger">No records found.</p>');
                return;
            }

            let html = '';   // ✅ FIX: added

            html += `
                <div class="row mb-4">
                    <!-- Academic History -->
                    <div class="col-md-6">
                        <h5 class="text-primary"><b>Academic History</b></h5>
                        <iframe 
                            src="${documentLink}" 
                            width="100%" 
                            height="500"
                            style="border:1px solid #ccc;border-radius:6px;">
                        </iframe>
                    </div>
                    <!-- Enrolled Courses -->
                    <div class="col-md-6">
                        <h5 class="text-primary"><b>Current Session Enrolled Courses</b></h5>
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Course Name</th>
                                    <th>Cr. Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    response.studentEnrollment && response.studentEnrollment.length > 0
                                        ? response.studentEnrollment.map((course, index) => `
                                            <tr>
                                                <td class="text-center">${index + 1}</td>
                                                <td>${course.course_name}</td>
                                                <td class="text-center">${course.sc_pc_course_cr}</td>
                                            </tr>
                                        `).join('')
                                        : `<tr><td colspan="3" class="text-center">No record found</td></tr>`
                                }
                            </tbody>
                                <!-- ✅ TOTAL CREDIT HOURS ROW -->
                                ${
                                    response.studentEnrollment && response.studentEnrollment.length > 0
                                    ? `
                                    <tfoot>
                                        <tr class="fw-bold text-center">
                                            <td colspan="2">Total Cr Hr</td>
                                            <td>
                                                ${
                                                    response.studentEnrollment.reduce((total, course) => {
                                                        return total + (parseFloat(course.sc_pc_course_cr) || 0);
                                                    }, 0)
                                                }
                                            </td>
                                        </tr>
                                    </tfoot>
                                    `
                                    : ''
                                }
                        </table>
                    </div>
                </div>
                <div class="row">
            `;
            let currentSess = null;
            let counter = 1;
            let hasExistingCourse = false; // flag to check if any row has rwf_sai_enrollment_no

            $.each(response.data, function (i, row) {
                if (row.rwf_sai_enrollment_no) {
                    hasExistingCourse = true;
                }
                // When session changes → start new table block
                if (currentSess !== row.sess_id) {

                    // Close previous table (except first)
                    if (currentSess !== null) {
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    // Reset counter
                    counter = 1;

                    // Update session ID
                    currentSess = row.sess_id;

                    // Open new column and table
                    html += `
                        <div class="col-md-6 mb-6">
                            <h5 class="text-primary"><b>${row.sess_name} (${row.sess_id})(${response.studentSessId})</b></h5>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Course</th>                               
                                        <th>Grades</th>  
                                        <th class='text-center'>
                                            Add Course <input type='checkbox' class='select-all-checkbox'>
                                        </th>
                                        ${ row.sess_id == response.studentSessId ? '' : `
                                        <th class='text-center'>
                                            Delete Course <input type='checkbox' class='delete-all-checkbox'>
                                        </th>
                                         `}
                                    </tr>
                                </thead>

                                <tbody>
                    `;
                }

                // Add row inside table
                html += `
                        <tr>
                            <td>${counter}</td>
                            <td>
                                ${
                                    row.course_cd_name && row.course_cd_name !== ""
                                        ? row.course_cd_name
                                        : (
                                            row.course_id == 123 ? "Elective 1" :
                                            row.course_id == 456 ? "Elective 2" :
                                            row.course_id == 789 ? "Elective 3" :
                                            row.course_id == 555 ? "Elective 4" :
                                            row.course_id == 666 ? "Elective 5" :
                                            row.course_id == 777 ? "Elective 6" :

                                            row.course_id == 888 ? "Degree Elective 1" :
                                            row.course_id == 118 ? "Degree Elective 2" :
                                            row.course_id == 112 ? "Degree Elective 3" :
                                            row.course_id == 113 ? "Degree Elective 4" :
                                            row.course_id == 114 ? "Degree Elective 5" :
                                            row.course_id == 115 ? "Degree Elective 6" :

                                            row.course_id == 111 ? "Specialization 1" :
                                            row.course_id == 222 ? "Specialization 2" :
                                            row.course_id == 333 ? "Specialization 3" :
                                            row.course_id == 444 ? "Specialization 4" :

                                            row.course_id == 119 ? "Minor 1" :
                                            row.course_id == 120 ? "Minor 2" :
                                            row.course_id == 121 ? "Minor 3" :
                                            row.course_id == 122 ? "Minor 4" :
                                            "N/A"
                                        )
                                }
                                (${row.course_id})
                                (${row.rwf_sai_enrollment_no ?? "-"})
                            </td>
                            <td>${row.grades ?? 'N/A'}</td>
                            <td class="text-center" ${ row.rwf_sai_enrollment_no ? 'style="display:none;"' : '' }>
                                <!-- Checkbox for selecting NEW courses -->
                                <input type="checkbox" class="session-checkbox"
                                    data-sessid="${row.sess_id}" 
                                    data-course="${row.course_cd_name}" 
                                    data-course-id="${row.course_id}" 
                                    data-student-session="${response.studentSessId}" 
                                    data-student-school="${response.studentSchoolId}"  
                                    data-student-dept="${response.studentDeptId}" 
                                    data-student-prog="${response.studentProgId}"                                 
                                >
                            </td>
                            ${ row.sess_id == response.studentSessId ? '' : `
                                <td class="text-center" ${ row.rwf_sai_enrollment_no ? '' : 'style="display:none;"' }>
                                    <!-- Checkbox for deleting EXISTING courses -->
                                    <input type="checkbox" class="delete-checkbox"
                                        data-rwf-id="${row.id}"
                                        data-enroll="${row.rwf_sai_enrollment_no}">
                                </td>
                            `}
                            ${ row.sess_id == response.studentSessId ? '' : `
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success add-row-btn">+</button>
                                </td>
                            `}
                        </tr>`;


                counter++;
            });

            // Close final table & row
            html += `
                        </tbody>
                    </table>
                    `;

                    if (hasExistingCourse) {
                        $('#showBtn').show();
                         $('#disclaimerMsg').hide();
                        
                        html += `
                        <button type="button" class="btn btn-danger btn-sm delete-selected-btn mt-2">
                            Delete Selected
                        </button>
                        `;
                    }
                    // $('#disclaimerMsg').show();

            html += `</div></div>`;

            $('#sessionTableContainer').html(html);
            updateHeaderVisibility();
            $('#sessionTableContainer').find('.select-all-checkbox').prop('checked', true).trigger('change');

        }
    });
}

// function updateHeaderVisibility() {
//     $('#sessionTableContainer table').each(function () {
//         let table = $(this);

//         // Add Course column
//         let addColumnVisible = false;
//         table.find('tbody tr').each(function () {
//             if ($(this).find('td:eq(2)').is(':visible')) {
//                 addColumnVisible = true;
//                 return false; // break loop
//             }
//         });
//         table.find('thead th:eq(2)').toggle(addColumnVisible);

//         // Delete Course column
//         let deleteColumnVisible = false;
//         table.find('tbody tr').each(function () {
//             if ($(this).find('td:eq(3)').is(':visible')) {
//                 deleteColumnVisible = true;
//                 return false; // break loop
//             }
//         });
//         table.find('thead th:eq(3)').toggle(deleteColumnVisible);
//     });
// }

function updateHeaderVisibility() {
    $('#sessionTableContainer table').each(function () {
        let table = $(this);

        // Add Course column
        let addColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(4)').is(':visible')) { 
                addColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(4)').toggle(addColumnVisible);

        // Delete Course column
        let deleteColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(5)').is(':visible')) {
                deleteColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(5)').toggle(deleteColumnVisible);

        // ⭐ NEW — Modify Grade column
        let modifyColumnVisible = false;
        table.find('tbody tr').each(function () {
            if ($(this).find('td:eq(3)').is(':visible')) { 
                modifyColumnVisible = true;
                return false;
            }
        });
        table.find('thead th:eq(3)').toggle(modifyColumnVisible);

    });
}


let deleteSelected = [];

$(document).on("change", ".delete-checkbox", function () {

    let rwfId = $(this).data("rwf-id");
    let enroll = $(this).data("enroll");

    if ($(this).is(":checked")) {
        deleteSelected.push({
            id: rwfId,
            enroll_no: enroll
        });
    } else {
        deleteSelected = deleteSelected.filter(item => item.id != rwfId);
    }

    console.log("Delete Array:", deleteSelected);
});


$(document).on("click", ".delete-selected-btn", function () {

    if (deleteSelected.length === 0) {
        alert("Please select courses to delete.");
        return;
    }

    if (!confirm("Are you sure you want to delete selected courses?")) return;

    $.ajax({
        url: "/delete-multiple-courses",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            rows: deleteSelected
        },
        success: function(response) {
            // alert("Selected courses deleted!");
            showToast('error', 'Selected courses deleted!');

            // Remove from UI
            deleteSelected.forEach(item => {
                $(`.delete-checkbox[data-rwf-id="${item.id}"]`).closest("tr").remove();
            });

            deleteSelected = [];
            loadsession();
        }
    });
});

$(document).on('change', '.select-all-checkbox', function() {
    let isChecked = $(this).is(':checked');
    // Find all checkboxes in the same table
    $(this).closest('table').find('.session-checkbox').prop('checked', isChecked).trigger('change');
});

$(document).on('change', '.delete-all-checkbox', function() {
    let isChecked = $(this).is(':checked');
    // Find all checkboxes in the same table
    $(this).closest('table').find('.delete-checkbox').prop('checked', isChecked).trigger('change');
});




let selectedCourses = [];

// Add new row
$(document).on('click', '.add-row-btn', function () {

    let table = $(this).closest('table');
    let tableBody = table.find('tbody');

    // Detect which header columns exist
    let hasAddColumn = table.find('thead th:contains("Add Course")').length > 0;
    let hasDeleteColumn = table.find('thead th:contains("Delete Course")').length > 0;

    $.ajax({
        url: "/get-courses-enrollment",
        type: "GET",
        success: function (courses) {          

            let options = `<option value="">Select Course</option>`;
            // Dynamic courses
            courses.forEach(function(course) {
                let name = course.course_cd_name;
                let label = "";

                if (!name) {
                    label = "-";
                }
                else {
                    label = name;
                }
                options += `<option value="${course.course_id}">${label}</option>`;
            });

            // Add static Electives
            options += `
                <optgroup label="Electives">
                    <option value="123">Elective 1</option>
                    <option value="456">Elective 2</option>
                    <option value="789">Elective 3</option>
                    <option value="555">Elective 4</option>
                    <option value="666">Elective 5</option>
                    <option value="777">Elective 6</option>
                </optgroup>
            `;

            // Add static Degree Electives
            options += `
                <optgroup label="Degree Electives">
                    <option value="888">Degree Elective 1</option>
                    <option value="118">Degree Elective 2</option>
                    <option value="112">Degree Elective 3</option>
                    <option value="113">Degree Elective 4</option>
                    <option value="114">Degree Elective 5</option>
                    <option value="115">Degree Elective 6</option>
                </optgroup>
            `;


            // Add static Specializations
            options += `
                <optgroup label="Specializations">
                    <option value="111">Specialization 1</option>
                    <option value="222">Specialization 2</option>
                    <option value="333">Specialization 3</option>
                    <option value="444">Specialization 4</option>
                </optgroup>
            `;

            // Add static Specializations
            options += `
                <optgroup label="Minor">
                    <option value="119">Minor 1</option>
                    <option value="120">Minor 2</option>
                    <option value="121">Minor 3</option>
                    <option value="122">Minor 4</option>                                   
                </optgroup>
            `;

            $("#course").html(options);


            // Build action columns
            let actionColumns = "";

            // ADD COURSE column exists → add semester dropdown
            if (hasAddColumn) {
                actionColumns += `
                    <td>
                        <select class="form-control semester-select">
                            <option value="">Select Semester</option>
                            <option value="1">I</option>
                            <option value="2">II</option>
                            <option value="3">III</option>
                            <option value="4">IV</option>
                            <option value="5">V</option>
                            <option value="6">VI</option>
                            <option value="7">VII</option>
                            <option value="8">VIII</option>
                            <option value="9">IX</option>
                            <option value="10">X</option>
                            <option value="11">XI</option>
                            <option value="12">XII</option>
                            <option value="13">XIII</option>
                            <option value="14">XIV</option>
                            <option value="15">XV</option>
                            <option value="16">XVI</option>
                            <option value="17">XVII</option>
                            <option value="18">XVIII</option>
                            
                        </select>
                    </td>
                `;
            }

            // DELETE COURSE column exists → add remove button
            if (hasDeleteColumn) {
                actionColumns += `
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn">−</button>
                    </td>
                `;
            }

            // Create final row
            let newRow = `
                <tr class="new-row">
                    <td></td>
                    <td>
                        <select class="form-control select2dynamic">
                            ${options}
                        </select>
                    </td>
                    ${actionColumns}
                </tr>
            `;

            tableBody.append(newRow);

            // Activate Select2
            tableBody.find(".select2dynamic").last().select2({
                placeholder: "Select Course",
                allowClear: true,
                width: '100%'
            });

            renumberRows(tableBody);
        }
    });
});

// Remove row
$(document).on('click', '.remove-row-btn', function () {
    let tableBody = $(this).closest('tbody');
    let row = $(this).closest('tr');

    // Get course_id and semester (or any other identifiers)
    let courseId = row.find('.select2dynamic').val();
    let courseName = row.find('.select2dynamic option:selected').text();
    let semester = row.find('.semester-select').val();

    // Find session_id from heading
    let sessionText = $(this).closest(".col-md-6").find("h5 b").text(); 
    let sessionId = sessionText.match(/\((\d+)\)/)[1]; // extract sess_id

    // Remove from array
    selectedCourses = selectedCourses.filter(item => {
        return !(item.sess_id == sessionId && item.course_id == courseId && item.semester == semester);
    });

    console.log("After remove:", selectedCourses);

    // Remove row from DOM
    row.remove();

    // Re-number rows
    renumberRows(tableBody);
});


function getRowKey(tr) {
    // Use tr.index() or a data attribute to uniquely identify the row
    return tr.index(); // unique per row
}

function updateRowData(tr) {
    let courseId = tr.find(".select2dynamic").val();
    let courseName = tr.find(".select2dynamic option:selected").text();
    let semester = tr.find(".semester-select").val();

    if (!courseId || !semester) {
        // If either is empty, remove the row from the array
        selectedCourses = selectedCourses.filter(item => item.row_key !== getRowKey(tr));
        return;
    }

    let sessionText = tr.closest(".col-md-6").find("h5 b").text(); 
    let sessId = sessionText.match(/\((\d+)\)/)[1];

    let checkbox = tr.closest("table").find(".session-checkbox").first();
    let studentSess = checkbox.data('student-session');
    let studentSchool = checkbox.data('student-school');
    let studentDept = checkbox.data('student-dept');
    let studentProg = checkbox.data('student-prog');

    let rowKey = getRowKey(tr);

    // Remove any previous entry for this row
    selectedCourses = selectedCourses.filter(item => item.row_key !== rowKey);

    // Push latest selected course & semester for this row
    selectedCourses.push({
        row_key: rowKey, // unique per row
        sess_id: sessId,
        course_id: courseId,
        course_name: courseName,
        semester: semester,
        grade: null,
        student_sess: studentSess,
        student_school: studentSchool,
        student_dept: studentDept,
        student_prog: studentProg
    });

    console.log("Selected Courses:", selectedCourses);
}


// Trigger on course change
$(document).on('change', '.select2dynamic', function() {
    
    updateRowData($(this).closest("tr"));
});

// Trigger on semester change
$(document).on('change', '.semester-select', function() {
   
    updateRowData($(this).closest("tr"));
});


// Handle session checkboxes
$(document).on("change", ".session-checkbox", function () {

    let tr = $(this).closest("tr");

    let courseId = $(this).data("course-id");
    let sessId = $(this).data("sessid");
    let studentSess = $(this).data("student-session");
    let studentSchool = $(this).data("student-school");
    let studentDept = $(this).data("student-dept");
    let studentProg = $(this).data("student-prog");

    // Determine course name EXACTLY as in loadsession()
    let courseName =
        $(this).data("course") && $(this).data("course") !== ""
        ? $(this).data("course")
        : (
            courseId == 123 ? "Elective 1" :
            courseId == 456 ? "Elective 2" :
            courseId == 789 ? "Elective 3" :
            courseId == 555 ? "Elective 4" :
            courseId == 666 ? "Elective 5" :
            courseId == 777 ? "Elective 6" :
            courseId == 888 ? "Degree Elective 1" :
            courseId == 118 ? "Degree Elective 2" :
            courseId == 112 ? "Degree Elective 3" :
            courseId == 113 ? "Degree Elective 4" :
            courseId == 114 ? "Degree Elective 5" :
            courseId == 115 ? "Degree Elective 6" :
            courseId == 111 ? "Specialization 1" :
            courseId == 222 ? "Specialization 2" :
            courseId == 333 ? "Specialization 3" :
            courseId == 444 ? "Specialization 4" :
            courseId == 119 ? "Minor 1" :
            courseId == 120 ? "Minor 2" :
            courseId == 121 ? "Minor 3" :
            courseId == 122 ? "Minor 4" :
            "N/A"
        );

    // If unchecked → remove
    if (!$(this).is(":checked")) {
        selectedCourses = selectedCourses.filter(item => item.course_id != courseId);
        return;
    }

    // Remove duplicates
    selectedCourses = selectedCourses.filter(item => item.course_id != courseId);

    // Push NEW record
    selectedCourses.push({
        sess_id: sessId,
        course_id: courseId,
        course_name: courseName,
        semester: null,
        grade: null,
        student_sess: studentSess,
        student_school: studentSchool,
        student_dept: studentDept,
        student_prog: studentProg
    });

    console.log("selectedCourses:", selectedCourses);
});


// Renumber rows
function renumberRows(tbody) {
    tbody.find('tr').each(function (i) {
        $(this).find('td:first').text(i + 1);
    });
}

$('#programs').on('change', function () {
    var progId = $(this).val();
    var rdmapsession = $('#roadmap_session').val();
    // alert(progId+'--'+rdmapsession);
    // return false;
    if (progId) {
        $.ajax({
            url: '/get-roadmap-sessions-students',
            type: 'GET',
            data: {
                program_id: progId,
                roadmap_session: rdmapsession,
                _token: '{{ csrf_token() }}' // CSRF token for Laravel
            },
            dataType: 'json',
            success: function (data) {
                $('#studentCountValue').val(data.count);
                // $('#studentCount').show();

                // $('#studentCount').html(
                //     '<div class="alert alert-info">Total Students: <strong>' + data.count + '</strong></div>'
                // );
            }
        });
    } else {
        $('#roadmap_session').empty().append('<option value="">Select Session</option>');
    }
});


$('#schools').on('change', function () {
    let schoolId = $('#schools').val();

    $('#departments').html('<option value="">Loading...</option>');

    if (schoolId) {
        $.ajax({
            url: '{{ url('/get_departments_by_school') }}',
            type: 'GET',
            data: { school_id: schoolId },
            success: function (data) {
                $('#departments').html('<option value="">Select Department</option>');
                $.each(data, function (key, dept) {
                    $('#departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch departments.');
                $('#departments').html('<option value="">Select Department</option>');
            }
        });
    } else {
        $('#departments').html('<option value="">Select Department</option>');
    }
});

$('#departments').on('change', function () {
    let deptId = $('#departments').val();
     var rdmapsession = $('#roadmap_session').val();

    $('#programs').html('<option value="">Loading...</option>');

    if (deptId) {
        $.ajax({
            url: '{{ url('/get_programs_by_school') }}',
        
            type: 'GET',
            data: { dept_id: deptId,rdmapsession: rdmapsession },
            success: function (data) {
                $('#programs').html('<option value="">Select Program</option>');
                $.each(data, function (key, prog) {
                    $('#programs').append(`<option value="${prog.prog_id}">${prog.prog_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to fetch programs.');
                $('#programs').html('<option value="">Select Programs</option>');
            }
        });
    } else {
        $('#programs').html('<option value="">Select Programs</option>');
    }
});

$(document).ready(function() {
    $('#schools').select2({
      placeholder: "Select School",
      allowClear: true
    });
      $('#studentEnrolmentNO').select2({
      placeholder: "Select Students",
      allowClear: true,
      width: '100%'
    });

    

    $('#course_new').select2({
      placeholder: "Select Course",
      allowClear: true
    });

    $('#st_schools').select2({
      placeholder: "Select School",
      allowClear: true
    });
     $('#course').select2({
      placeholder: "Select Course",
      allowClear: true
    });
    loadRoadmapData();


    $('#addSectionBtn').on('click', function () {
    var totalStudents = parseInt($('#studentCountValue').val()) || 0;
    if (totalStudents === 0) {
        alert("Please load the total student count first.");
        return;
    }

    var currentTotal = 0;
    $('#sectionsContainer').find('.assignedStudentsCount').each(function () {
        currentTotal += parseInt($(this).text()) || 0;
    });
    if (currentTotal >= totalStudents) {
        alert("You cannot exceed the total student count.");
        return;
    }

    // numeric index that the controller expects
    var idx = $('#sectionsContainer .sectionRow').length; // 0-based
    var sectionId = "section_" + (idx + 1);

    var sectionHtml = `
        <div class="row mb-2 sectionRow" id="${sectionId}" data-section-idx="${idx}">
            <div class="col-md-4">
                <select name="section_names[]" class="form-control" required>
                    <option value="">Select Section</option>
                    <option value="A">A</option><option value="B">B</option>
                    <option value="C">C</option><option value="D">D</option>
                    <option value="E">E</option><option value="F">F</option>
                    <option value="G">G</option><option value="H">H</option>
                    <option value="I">I</option><option value="J">J</option>
                    <option value="K">K</option><option value="L">L</option>
                    <option value="M">M</option><option value="N">N</option>
                    <option value="O">O</option><option value="P">P</option>
                    <option value="Q">Q</option><option value="R">R</option>
                    <option value="S">S</option><option value="T">T</option>
                    <option value="U">U</option><option value="V">V</option>
                    <option value="W">W</option><option value="X">X</option>
                    <option value="Y">Y</option><option value="Z">Z</option>
                </select>
            </div>
            <div class="col-md-5">
                <button type="button" class="btn btn-info assignStudentsBtn" data-section="${sectionId}">Assign Students</button>
                <span class="ml-2 text-success assignedStudentsCount">0</span> students assigned
                <div class="assignedStudentsList small text-muted"></div>

                <!-- hidden count per section (for course_section_count) -->
                <input type="hidden" name="section_students[]" class="sectionCountInput" value="0">

                <!-- container to hold many hidden inputs: students[IDX][] -->
                <div class="studentsHiddenContainer"></div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeSectionBtn">X</button>
            </div>
            </div>
        `;
        $('#sectionsContainer').append(sectionHtml);
    });


    let currentSectionId = null;
    $(document).on("click", ".assignStudentsBtn", function () {
        currentSectionId = $(this).data("section");
        loadStudents(currentSectionId);
        $("#studentModal").modal("show");
    });


    $("#saveStudentsBtn").click(function () {
    if (!currentSectionId) return;

    // gather ONLY selectable picks (ignore locked ones from other sections)
    let selected = [];
    $("#studentsTable tbody input[type=checkbox]:checked:not(:disabled)").each(function () {
        selected.push({
            roll_no: $(this).val(),
            name: $(this).data("name")
        });
    });

    // capacity check (optional but safer)
    let totalStudents = parseInt($('#studentCountValue').val()) || 0;
    let currentTotal = 0;
    $('#sectionsContainer .assignedStudentsCount').each(function () {
        currentTotal += parseInt($(this).text()) || 0;
    });
    const prevCount = parseInt($(`#${currentSectionId} .assignedStudentsCount`).text()) || 0;
    if ((currentTotal - prevCount) + selected.length > totalStudents) {
        alert("You cannot exceed the total student count.");
        return;
    }

    // update UI for this section
    const sectionRow = $(`#${currentSectionId}`);
    sectionRow.find(".assignedStudentsCount").text(selected.length);
    sectionRow.find(".assignedStudentsList").html(
        selected.map(s => `${s.name} (${s.roll_no})`).join(", ")
    );
    sectionRow.find(".sectionCountInput").val(selected.length);

    // rebuild hidden inputs: students[IDX][]
    const idx = sectionRow.data("section-idx");
    const container = sectionRow.find(".studentsHiddenContainer");
    container.empty();
    selected.forEach(s => {
        container.append(
            `<input type="hidden" name="students[${idx}][]" value="${s.roll_no}">`
        );
    });

    $("#studentModal").modal("hide");
});


    // AJAX to fetch students
    function loadStudents3(sectionId) {
        
        
        $("#studentsList").html("<em>Loading students...</em>");

        // read already-selected for this section to pre-check
        const already = $(`#${sectionId} .studentsHiddenContainer input[type=hidden]`)
            .map(function(){ return $(this).val(); }).get();

        $.ajax({
            url: "/get-students",
            type: "GET",
            dataType: "json",
            success: function (students) {
                let table = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Enrollment</th>
                                <th>School</th>
                                <th>Department</th>
                                <th>Program</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                students.forEach(function(student) {
                    const enr = student.sai_enrollment_no;
                    const checked = already.includes(enr) ? 'checked' : '';
                    table += `
                        <tr>
                            <td>
                                <input type="checkbox" value="${enr}" data-name="${student.spi_name}" ${checked}>
                            </td>
                            <td>${student.sai_enrollment_no}</td>
                            <td>${student.school_code}</td>
                            <td>${student.dept_name}</td>
                            <td>${student.prog_name}</td>
                        </tr>
                    `;
                });
                table += `</tbody></table>`;
                $("#studentsList").html(table);
            },
            error: function () {
                $("#studentsList").html("<span class='text-danger'>Failed to load students</span>");
            }
        });
    }

    function loadStudents(sectionId) {   
        let schoolId = $("#schools").val();
        let sessId = $("#roadmap_session").val();
        let progId = $("#programs").val();
        $("#studentsList").html("<em>Loading students...</em>");

        // Collect students already assigned in ALL sections
        const globallyAssigned = $(".studentsHiddenContainer input[type=hidden]")
            .map(function () { return $(this).val(); })
            .get();

        // Collect students assigned ONLY in this section
        const currentSectionAssigned = $(`#${sectionId} .studentsHiddenContainer input[type=hidden]`)
            .map(function () { return $(this).val(); })
            .get();

        $.ajax({
            url: "/get-students",
            type: "GET",
            data: {
                    school_id: schoolId,   // send school_id in request
                    sess_id: sessId,
                    prog_id: progId
                },
            dataType: "json",
            success: function (students) {
                let table = `
                    <table id="studentsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:5%; text-align:center;">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th style="width:5%;">Sr.No.</th>
                                <th style="width:12%;">Enrollment</th>
                                <th style="width:10%;">School</th>
                                <th style="width:25%;">Department</th>
                                <th style="width:28%;">Program</th>
                                <th style="width:10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
               
                students.forEach(function(student,index) {
                    const enr = student.sai_enrollment_no;
                    let checked = '';
                    let disabled = '';
                    

                    if (currentSectionAssigned.includes(enr)) {
                        // Student already in THIS section → check + enable
                        checked = 'checked';
                    } else if (globallyAssigned.includes(enr)) {
                        // Student assigned in ANOTHER section → lock
                        checked = 'checked';
                        disabled = 'disabled';
                    }
                    table += `
                        <tr>
                            <td>
                                <input type="checkbox" 
                                    value="${enr}" 
                                    data-name="${student.spi_name}" 
                                    ${checked} ${disabled}>
                            </td>
                            <td>${index + 1}</td>  
                            <td>${student.sai_enrollment_no}</td>
                            <td>${student.school_code}</td>
                            <td>${student.dept_name}</td>
                            <td>${student.prog_name}</td>
                            <td>${student.sai_student_status}</td>
                        </tr>
                    `;
                });

                table += `</tbody></table>`;
                $("#studentsList").html(table);

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable("#studentsTable")) {
                        $("#studentsTable").DataTable().destroy();
                    }
                    $("#studentsTable").DataTable({
                        autoWidth: false,
                        responsive: false,
                        scrollX: true,
                        pageLength: 50,
                        lengthMenu: [
                            [10, 20, 30, 50, -1], 
                            [10, 20, 30, 50, "All"]
                        ],                        
                        columnDefs: [
                            { targets: 0, width: "5%", className: "text-center", orderable: false },   
                            { targets: 1, width: "5%"},                            
                            { targets: 2, width: "12%" },
                            { targets: 3, width: "10%" },
                            { targets: 4, width: "25%" },
                            { targets: 5, width: "28%" },
                            { targets: 6, width: "10%" }
                        ]
                    }).columns.adjust();

                    // --- Select All checkbox logic ---
                    $('#selectAll').on('click', function () {
                        const isChecked = $(this).is(':checked');
                        $('#studentsTable tbody input[type="checkbox"]:not(:disabled)').prop('checked', isChecked);
                    });

                    $('#studentsTable tbody').on('change', 'input[type="checkbox"]', function () {
                        if (!this.checked) {
                            $('#selectAll').prop('checked', false);
                        } else {
                            if ($('#studentsTable tbody input[type="checkbox"]:not(:disabled)').length ===
                                $('#studentsTable tbody input[type="checkbox"]:not(:disabled):checked').length) {
                                $('#selectAll').prop('checked', true);
                            }
                        }
                    });
                }, 200);
            },
            error: function () {
                $("#studentsList").html("<span class='text-danger'>Failed to load students</span>");
            }
        });
    }    
    // Remove section
    $(document).on('click', '.removeSectionBtn', function () {
        $(this).closest('.sectionRow').remove();
    });

    // Example: When AJAX loads student count
    function loadStudentCount(count) {
        $('#studentCountValue').val(count);
        $('#studentCount').show();
    }


    // run when either session select changes
    $('#session, #roadmap_session').on('change', function () {


       // $('#departments').val('').trigger('change'); 
        
        let curText = $('#session option:selected').text().trim();
        let roadText = $('#roadmap_session option:selected').text().trim();

        // ensure both selected and not the placeholder
        if (!curText || curText.toLowerCase().includes('select') || !roadText || roadText.toLowerCase().includes('select')) {
            resetSemesterOptions();
            return;
        }

        let cur = parseSession(curText);       // { term: 'Fall'|'Spring', year: 2025 }
        let road = parseSession(roadText);
        if (!cur || !road) {
            console.warn('Session text not in expected format (e.g. "Fall 2025").');
            resetSemesterOptions();
            return;
        }

        // compute numeric indexes and semester number (inclusive)
        let curIndex  = cur.year * 2 + (cur.term.toLowerCase() === 'fall' ? 1 : 0);
        let roadIndex = road.year * 2 + (road.term.toLowerCase() === 'fall' ? 1 : 0);
        let diff = curIndex - roadIndex;

        if (diff < 0) {
            // roadmap is in future compared to current session — clear/disable
            alert('Roadmap session is later than selected session. Please choose valid sessions.');
            resetSemesterOptions();
            return;
        }

        let semesterNo = diff + 1;           // inclusive count
        semesterNo = Math.max(1, semesterNo); // min 1
        semesterNo = Math.min(8, semesterNo); // clamp to max 8 (adjust if you allow >8)

        // disable all options except the calculated one, then set value
        $('#semester option').each(function () {
            // keep the option enabled if it matches semesterNo, otherwise disable
            // $(this).prop('disabled', $(this).val() !== String(semesterNo));
        });

        $('#semester').val(String(semesterNo));
    });

    function parseSession(text) {
        // Accepts "Fall 2025", "Spring 2024", "Fall-2025" etc.
        text = text.replace('-', ' ').replace(/\s+/g, ' ').trim();
        let parts = text.split(' ');
        if (parts.length < 2) return null;
        let term = parts[0];
        let year = parseInt(parts[1], 10);
        if (!term || isNaN(year)) return null;
        return { term: term, year: year };
    }

    function resetSemesterOptions() {
        // Enable all and reset selection to placeholder (or keep current)
        $('#semester option').prop('disabled', false);
        $('#semester').val($('#semester option:first').val()); // assumes first is "Select Semester"
    }
});

function st_sbtBtn() {
  
    let sessionId = $('#st_session').val();
    let enrollNo = $('#studentEnrolmentNO').val();

    if (selectedCourses.length === 0) {
        alert("Please add at least one course.");
        return;
    }

    $.ajax({
        url: "/submit-session-courses",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            session_id: sessionId,
            enroll_no: enrollNo,
            courses: selectedCourses
        },
        success: function (res) {
            
            showToast('success', 'Saved Successfully');
            $('#disclaimerMsg').hide();
            selectedCourses = [];
            loadsession();
            console.log(res);
        }
    });
}


function sbtBtn(e) {
     event.preventDefault();

    // build students[][] from hidden inputs so controller gets students.$index
    let students = [];
    $("#sectionsContainer .sectionRow").each(function () {
        const idx = $(this).data("section-idx");
        const vals = $(this).find(".studentsHiddenContainer input[type=hidden]")
                      .map(function(){ return $(this).val(); }).get();
        students[idx] = vals; // array of enrollment nos for this section
    });

    let data = {     
        formId: $('#formId').val(),
        session_id: $('#session').val(),
        roadmap_session_id: $('#roadmap_session').val(),
        school_id: $('#schools').val(),
        department_id: $('#departments').val(),
        program_id: $('#programs').val(),
        semester_id: $('#semester').val(),
        course_id: $('#course').val(),
        course_type: $('#courseType').val(),
        theory_credit_hours: $('#theoryHours').val(),
        // practical_credit_hours: $('#practicalHours').val(),
          // parallel arrays by DOM order
        section_names: $("[name='section_names[]']").map(function(){ return $(this).val(); }).get(),
        section_students: $("input.sectionCountInput").map(function(){ return $(this).val(); }).get(),

        // <<< IMPORTANT
        students: students, // e.g. {0: ['ENR1','ENR2'], 1: ['ENR9']}
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: '{{ route("roadmap.store2") }}',
        method: 'POST',
        data: data,
        success: function (response) {
            // alert('Data saved successfully!');
            $('#error-messages').hide().html('');
            // Show success message
            $('#success-message')
            .html('<p>Submitted successfully!</p>')
            .show();
            $('form')[0].reset();
            setTimeout(function() {
                $('#success-message').fadeOut();
            }, 3000);
            loadRoadmapData(); // Refresh table with new data
        },
        error: function (xhr) {
            // alert('Something went wrong: ' + xhr.responseText);
            $('#success-message').hide().html('');
            let responseText = xhr.responseText || "";
            if (xhr.status === 422) { // Laravel validation error
                let errors = xhr.responseJSON.errors;

                // Get first error field and message
                let firstField = Object.keys(errors)[0];
                let firstMessage = errors[firstField][0];

                // Show message
                $('#error-messages').html('<p>' + firstMessage + '</p>').show();

                // Focus the corresponding input/select field
                let fieldElement = $('[name="' + firstField + '"]');
                if (fieldElement.length) {
                    fieldElement.focus();
                }
            }  else if (responseText.includes("ORA-00001")) {
                $('#error-messages').html('<p>Course already exists. Please choose another.</p>').show();
            } else {
                $('#error-messages').html('<p></p>').show();
            }
        }
    });
}


function loadRoadmapData() {
 

    
    if ($.fn.DataTable.isDataTable('#roadmapTable')) {
        $('#roadmapTable').DataTable().clear().destroy();
    }
    $('#roadmapTable').DataTable({
        processing: true,        
        responsive: true,
        pageLength: 11,
         lengthMenu: [
            [10, 20, 30, 50, -1], 
            [10, 20, 30, 50, "All"]
        ],
       
        ajax: {
            url: "{{ route('roadmap.data') }}", // Define this route in web.php
            type: 'GET',
           
        },
        columns: [
            { data: 'active_session_name' },
            { 
                data: 'roadmap_session_name',
                render: function(data, type, row) {
                    return data ? data : 'N/A';
                }
            },
            { data: 'school_code' },
            { data: 'dept_name' },
            { data: 'prog_name' },
            { data: 'semester_id' },
            {
                data: 'course_cd_name',
                render: function (data, type, row) {

                    // If normal course name exists → show it
                    if (data !== null && data !== "") {
                        return data;
                    }

                    // Otherwise use static mapping based on course_id
                    let id = row.course_id;   // your dropdown value

                    if (id == 123) return "Elective 1";
                    if (id == 456) return "Elective 2";
                    if (id == 789) return "Elective 3";
                    if (id == 555) return "Elective 4";
                    if (id == 666) return "Elective 5";
                    if (id == 777) return "Elective 6";

                    if (id == 888) return "Degree Elective 1";
                    if (id == 118) return "Degree Elective 2";
                    if (id == 112) return "Degree Elective 3";
                    if (id == 113) return "Degree Elective 4";
                    if (id == 114) return "Degree Elective 5";
                    if (id == 115) return "Degree Elective 6";

                    if (id == 111) return "Specialization 1";
                    if (id == 222) return "Specialization 2";
                    if (id == 333) return "Specialization 3";
                    if (id == 444) return "Specialization 4";

                    if (id == 119) return "Minor 1";
                    if (id == 120) return "Minor 2";
                    if (id == 121) return "Minor 3";
                    if (id == 122) return "Minor 4";

                    // Fallback
                    return "N/A";
                }
            },
            { data: 'ct_name' },
            { 
                data: 'theory_credit_hours',
                render: function(data, type, row) {
                    return data ? data : 'N/A';
                }
            },
           
            {
                data: 'id',
                render: function (data, type, row) {
                     return `
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" data-action="edit" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-action="delete" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ],
        // FIX ordering
        order: [[0, 'desc']],

        
        dom:
            "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center gap-3'B l>f>" +
            "rt" +
            "<'d-flex justify-content-between align-items-center mt-3'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm mr-2',
                title: 'Roadmap Course Offering',
                orientation: 'landscape',
                pageSize: { width: 1200, height: 500 },
                exportOptions: { columns: ':not(:last-child)' },
                
            }
        ]
    });
}

$(document).on('click', '.edit-btn, .delete-btn', function () {
    let id = $(this).data('id');
    let action = $(this).data('action');

    if (action === 'edit') {
        $('#session').focus();
        $.get('/roadmap-offering/' + id + '/edit2', function (response) {
            console.log(response);
            $('#formId').val(response.id);
            $('#session').val(response.active_session_id);
             $('#roadmap_session').val(response.rdmap_session_id);
            $('#schools').val(response.school_id).trigger('change');

            let departmentId = response.department_id;
            let programId = response.program_id;
            let courseId = response.course_id;

            // Step 2: Load departments
            $.ajax({
                url: '{{ url('/get_departments_by_school') }}',
                type: 'GET',
                data: { school_id: response.school_id },
                success: function (departments) {
                    $('#departments').html('<option value="">Select Department</option>');
                    $.each(departments, function (key, dept) {
                        $('#departments').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
                    });

                    $('#departments').val(departmentId); // select correct department

                    // Step 3: Load programs after department is set
                    $.ajax({
                        url: '{{ url('/get_programs_by_school') }}',
                        type: 'GET',
                        data: {
                            dept_id: departmentId,
                            rdmapsession: response.rdmap_session_id
                        },
                        success: function (programs) {
                            $('#programs').html('<option value="">Select Program</option>');
                            $.each(programs, function (key, prog) {
                                $('#programs').append(`<option value="${prog.prog_id}">${prog.prog_name}</option>`);
                            });

                            $('#programs').val(programId); // select correct program
                            $('#course').val(courseId);    // select correct course if already loaded
                            getStudentCount(programId, response.rdmap_session_id);
                        }
                    });
                }
            });
            $('#course').val(courseId).trigger('change');
            $('#semester').val(response.semester_id);
            $('#courseType').val(response.course_type).trigger('change');
            $('#theoryHours').val(response.theory_credit_hours);
            // $('#practicalHours').val(response.practical_credit_hours);

            // Clear old sections before adding new ones
            $('#sectionsContainer').html('');                   

            if (response.sections && response.sections.length > 0) {
                response.sections.forEach(function (sec, idx) {
                    let options = '<option value="">Select Section</option>';
                    for (let i = 65; i <= 90; i++) {
                        let letter = String.fromCharCode(i);
                        let selected = (letter === sec.course_section) ? 'selected' : '';
                        options += `<option value="${letter}" ${selected}>${letter}</option>`;
                    }

                    // Assigned students list (names/roll_no will be loaded later)
                    let assignedListHtml = "";
                    if (sec.assigned_students && sec.assigned_students.length > 0) {
                        assignedListHtml = sec.assigned_students.join(", ");
                    }

                    let sectionHtml = `
                        <div class="row mb-2 sectionRow" id="section-${idx}" data-section-idx="${idx}">
                            <div class="col-md-3">
                                <select name="section_names[]" class="form-control" required>
                                    ${options}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="section_students[]" value="${sec.course_section_count}" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-5 assignedStudentsList">${assignedListHtml}</div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger removeSectionBtn">X</button>
                            </div>
                            <div class="studentsHiddenContainer"></div>
                        </div>
                    `;

                    $('#sectionsContainer').append(sectionHtml);

                    // Add hidden inputs for assigned students
                    sec.assigned_students.forEach(function (roll_no) {
                        $(`#section-${idx} .studentsHiddenContainer`).append(
                            `<input type="hidden" name="students[${idx}][]" value="${roll_no}">`
                        );
                    });
                });
            }


            $('#submitBtn').text('Update').data('update-id', id);
            $('#newEntryBtn').show();
        });
    } 
    else if (action === 'delete') {
        if (confirm('Are you sure you want to delete this record?')) {
            $.post('/roadmap-offering-delete2/' + id, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function () {
                // alert('Deleted successfully!');
                $('#roadmapTable').DataTable().ajax.reload();
            })
            .fail(function () {
                alert('Error deleting record.');
            });
        }
    }
});

function getStudentCount(progId, rdmapsession) {
    if (progId) {
        $.ajax({
            url: '/get-roadmap-sessions-students',
            type: 'GET',
            data: {
                program_id: progId,
                roadmap_session: rdmapsession,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function (data) {
                $('#studentCountValue').val(data.count);
            }
        });
    } else {
        $('#studentCountValue').val('');
    }
}

function resetToNewEntry() {
    // Clear the form
    $('#formCourse')[0].reset();
    // Clear any saved ID and revert button to Submit
    $('#submitBtn')
        .text('Submit')
        .removeData('update-id');
    // Hide New Entry button
    $('#newEntryBtn').hide();
    $('#schools').val('').trigger('change');
    $('#departments').html('<option value="">Select Department</option>');
    $('#programs').html('<option value="">Select Program</option>');
    $('#course').val('').trigger('change');
    
}





</script>
@stop