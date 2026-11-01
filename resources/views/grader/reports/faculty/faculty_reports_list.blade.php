@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
 
<?php

// dd(auth()->user());
?>

    <div class="card">
        <div class="card-header" style="width:100%;">

        <form action="" id="filterFormOne">
            @csrf
    <div class="row">
        <h3>Summary Report:</h3>
    </div>

    <div class="row">
        <div class="form-group col-sm-3">
            <label for="session_sel">Session</label>
            <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                <option value="">All Sessions</option>
                @foreach ($sessions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="school_sel">School</label>
            <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                <option value="">All Schools</option>
                @foreach ($schools as $row)
                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="department">Department</label>
            <select name="department" id="department" class="form-control" onchange="getTeacher();">
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="teacher">Instructor</label>
            <select name="teacher" id="teacher" class="form-control" onchange="getCourses();">
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="course">Course</label>
            <select name="course" id="course" class="form-control">
            </select>
        </div>
      <div class="form-group col-sm-3">
    <label for="report_type_selector">Report Type</label>
    <select name="report_type" id="report_type_selector" class="form-control">
        <option value="" disabled selected>Select Report Type</option>
        <option value="old">Old</option>
        <option value="new">New</option>
        <option value="f2025onward">F2025 Onwards</option>
    </select>
</div>

</form>
        <div class="form-group col-sm-6">



 <!-- Original Buttons -->
 <div id="original_buttons">
    <button class="btn btn-primary" id="getResultBtn" style="margin-top: 25px; margin-right: 10px;" type="button" onclick="getResultsByProgDept();">
        <i class="fa fa-file-text-o"></i> <span id="getResultBtnText">Get Result</span>
        <i id="getResultSpinner" class="fa fa-spinner fa-spin" style="display: none; margin-left: 5px;"></i>
    </button>
    <button class="btn btn-primary" id="getPDFBtn" style="margin-top: 25px;" onclick="getPDF();">
        <i class="fa fa-file-pdf-o"></i> <span id="getPDFBtnText">GET PDF</span>
        <i id="getPDFSpinner" class="fa fa-spinner fa-spin" style="display: none; margin-left: 5px;"></i>
    </button>
</div>

<!-- New Buttons -->
<div id="new_buttons" style="display: none; margin-top: 25px;">
    <button class="btn btn-primary" style="margin-right: 10px;" type="button" onclick="downloadCourseWise();">
        <i class="fa fa-download"></i> Course-wise
    </button>
    <button class="btn btn-primary" type="button" onclick="downloadFacultyWise();">
        <i class="fa fa-download"></i> Faculty-wise
    </button>
</div>










{{--
        <div class="form-group d-flex justify-content-start">
    <button class="btn btn-primary" style="margin-top: 25px; margin-right: 10px;" type="button" onclick="getResultsByProgDept();">
        <i class="fa fa-file-text-o"></i> Get Result
    </button>
    <button class="btn btn-primary" style="margin-top: 25px;" onclick="getPDF();">
        <i class="fa fa-file-pdf-o"></i> GET PDF
    </button>
</div> --}}


</div>


<div id="notificationDiv"></div>






        </div>


    </div>

    <hr>
    <h3>Analysis of Faculty Evaluation Report:</h3>
    <div class="row pb-4 pl-4">


        <div class="form-group col-sm-3">
            <label for="session_sel1">Session</label>
            <select name="session" id="session_sel1" class="form-control" onchange="on_session_change(this);">
                <option value="">All Sessions</option>
                @foreach ($sessions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="school_sel1">School</label>
            <select name="school" id="school_sel1" class="form-control" onchange="getDepartments();">
                <option value="">All Schools</option>
                @foreach ($schools as $row)
                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-sm-3">
            <label for="department1">Department</label>
            <select name="department" id="department1" class="form-control">
            </select>
        </div>
        <div style="margin-top: 25px;">
        <button type="button" class="btn btn-outline-primary" onclick="getSchoolwiseReport();">
    <i class="fa fa-bar-chart"></i> <span id="buttonText">School Wise</span>
    <i id="spinnerIcon" class="fa fa-spinner fa-spin" style="display: none;"></i>
</button>
<button type="button" class="btn btn-outline-primary" onclick="getDepartmentwiseReport();">
    <i class="fa fa-bar-chart"></i> <span id="buttonText2">Department Wise</span>
    <i id="spinnerIcon2" class="fa fa-spinner fa-spin" style="display: none;"></i>
</button>
</div>

    </div>

    <!-- <div style="display:none">
        <div class="form-group col-sm-3">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program">
            </select>
        </div>
    </div> -->



            <!-- <form action="" id="filterByTeacher" style="display:none;">
                <div class="row">
                    <h3>Filter By: Teacher</h3>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="teacher_input">Teacher</label>
                        <input type="input" name="teacher_input" id="teacher_input" value="" placeholder="Teacher"
                            class="form-control">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="tid_input">Hidden T-ID</label>
                        <input type="input" name="tid_input" id="tid_input" value="" placeholder="Teacher ID"
                            class="form-control">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button class="btn primary" style="margin-top: 25px;" type="button"
                            onclick="getResultsByTeacher();">Get Result</button>

                    </div>

                </div>

            </form> -->

            <div class="table-responsive">
            <div id="faculty_evaluation_report">

            </div>


        </div>


        </div>




        {{-- --------------------------- --}}







        {{-- --------------------------- --}}

        <div class="card" style="margin-top:10px;">
            <div class="card-header">
                <h3>Result</h3>


            </div>
            <!-- Old Table (for sessions below 165) -->
            <div class="card-body" id="facultyReport">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Sr.No</th>
                            <th scope="col">Name of Faculty</th>
                            <!-- <th scope="col">Course Code</th> -->
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Cr.Hrs</th>
                            <th scope="col">Total No of Students</th>
                            <th scope="col">No of Evaluators</th>
                            <th scope="col">Quality of Teaching</th>
                            <th scope="col">Course Evaluation</th>
                            <th scope="col">Exams & Assessment</th>
                            <th scope="col">Overall Rating%</th>
                            <th scope="col">Comments</th>
                            <!-- <th scope="col">Re Chance</th> -->
                            <!-- <th scope="col">Student's Contribution</th> -->
                        </tr>
                    </thead>
                    <tbody id="result-body">
                        <tr>
                            <td colspan="12" style="text-align:center;">No Filter Apply Yet!</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- New Table for F2025 Onwards (for session 165 and above) -->
            <div class="card-body" id="facultyReportF2025" style="display: none;">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Sr.No</th>
                            <th scope="col">Name of Faculty</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Cr.Hrs</th>
                            <th scope="col">Total No of Students</th>
                            <th scope="col">No of Evaluators</th>
                            <th scope="col">Academic Core Functions</th>
                            <th scope="col">Academic Support Functions</th>
                            <th scope="col">Overall Rating</th>
                            <th scope="col">Students Attendance</th>
                            <th scope="col">Comments</th>
                        </tr>
                    </thead>
                    <tbody id="result-body-f2025">
                        <tr>
                            <td colspan="12" style="text-align:center;">No Filter Apply Yet!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="clear:both;"></div>
@stop
@section('javascript')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
   <!-- Include html2canvas library -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Include SheetJS (XLSX) library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- Include FileSaver.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<!-- Include the main tableExport.js library -->
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-style/0.9.1/xlsx-style.full.min.js"></script>






    <script>




// Handle report type change - show/hide buttons based on selected type
document.getElementById('report_type_selector').addEventListener('change', function () {
    const selectedReportType = this.value;
    const sessionSelect = document.getElementById('session_sel');
    const sessionId = parseInt(sessionSelect.value) || 0;
    
    // Rule 1: For 'old' or 'f2025onward' - always show Get Result button
    if (selectedReportType === 'old' || selectedReportType === 'f2025onward') {
        toggleButtons(true, false);
    }
    // Rule 2: For 'new' report type
    else if (selectedReportType === 'new') {
        // If session is below 165, show Get Result button
        if (sessionId < 165) {
            toggleButtons(true, false);
        }
        // Otherwise show Course-wise/Faculty-wise buttons
        else {
            toggleButtons(false, true);
        }
    }
});




function downloadCourseWise() {
    // Get selected values
    const course = document.getElementById('course').value;
    const reportType = document.getElementById('report_type_selector').value;
    const sessionSelect = document.getElementById('session_sel');
    const sessionText = sessionSelect.options[sessionSelect.selectedIndex].text;



    // Check if both fields are selected
    if (!course || !reportType) {



        showNotification(
            'Please select both a course and a report type.',
            'danger',
            'notificationDiv'
        );


        return;
    }
 
    // Construct the URL with the selected parameters
    const baseUrl = window.location.origin;
    const courseWiseUrl = `${baseUrl}/course-wise-download-faculty-feedback-report?course=${course}&report_type=${reportType}&sessionName=${sessionText}`;

    // Open the constructed URL in a new tab
    window.open(courseWiseUrl, '_blank');
}



    function downloadFacultyWise() {
    // Get selected values
    const session = document.getElementById('session_sel').value;
    const school = document.getElementById('school_sel').value;
    const department = document.getElementById('department').value;
    const reportType = document.getElementById('report_type_selector').value;
    const teacher = document.getElementById('teacher').value;

    // Validate required fields
    // if (!session || !school || !department || !reportType) {
    if (!session || !school || !reportType) {
        // Show an error notification
        showNotification(
            'Please select session, school, department, and report type.',
            'danger',
            'notificationDiv'
        );
        return;
    }

    // Construct the URL with the selected parameters
    const baseUrl = window.location.origin;
    const facultyWiseUrl = `${baseUrl}/faculty-wise-download-faculty-feedback-report?session=${session}&school=${school}&department=${department}&report_type=${reportType}&teacher=${teacher}`;

    // Open the constructed URL in a new tab
    window.open(facultyWiseUrl, '_blank');


}








        var lastURL = '';

        // https://github.com/biggora/bootstrap-ajax-typeahead

        // Helper function: Get report type from URL
        function getReportTypeFromUrl(url) {
            const urlParams = new URLSearchParams(url.split('?')[1]);
            return urlParams.get('report_type') || '';
        }

        // Helper function: Show/hide tables based on report type
        function toggleResultTables(isF2025) {
            if (isF2025) {
                $('#facultyReport').hide();      // Hide old table
                $('#facultyReportF2025').show(); // Show new table
            } else {
                $('#facultyReport').show();       // Show old table
                $('#facultyReportF2025').hide();  // Hide new table
            }
        }

        // Main function: Get results and display in appropriate table
        function getResults(url, successCallback, errorCallback) {
            lastURL = url;
            
            // Check report type from URL
            const reportType = getReportTypeFromUrl(url);
            const isF2025 = (reportType === 'f2025onward');
            
            // Show/hide appropriate table
            toggleResultTables(isF2025);
            
            // Show loading message in appropriate table
            if (isF2025) {
                $('#result-body-f2025').html('<tr><td colspan="12" style="text-align:center;">Loading...</td></tr>');
            } else {
                $('#result-body').html('<tr><td colspan="12" style="text-align:center;">Loading...</td></tr>');
            }
            
            getRequest(url, function(res) {
                if (res.status) {
                    if (isF2025) {
                        // Display F2025 Onwards data
                        displayF2025Results(res.data);
                    } else {
                        // Display Old/New data
                        displayOldResults(res.data);
                    }
                    
                    // Call success callback if provided
                    if (typeof successCallback === 'function') {
                        successCallback();
                    }
                } else {
                    // Show error toast
                    if (typeof showToast !== 'undefined') {
                        showToast('error', res.message || 'Failed to load results. Please try again.', 'Error');
                    }
                    
                    // Call error callback if provided
                    if (typeof errorCallback === 'function') {
                        errorCallback();
                    }
                }
            }, function(res) {
                // Show error toast
                if (typeof showToast !== 'undefined') {
                    showToast('error', 'An error occurred while fetching results. Please check your connection and try again.', 'Connection Error');
                } else {
                    alert('Some Error found!');
                }
                
                // Call error callback if provided
                if (typeof errorCallback === 'function') {
                    errorCallback();
                }
            });
        }

        // Function: Display F2025 Onwards results (Core, Support, Overall)
        function displayF2025Results(data) {
            var html = '';
            $.each(data, function(key, value) {
                var srno = parseInt(key + 1);
                var co_id = value.fc_co_id;
                
                // Check if feedback is submitted
                if (value.feedback_submitted == 0) {
                    html += '<tr>';
                    html += '<td>' + srno + '</td>';
                    html += '<td>' + (value.teacher_name || 'N/A') + '</td>';
                    html += '<td>' + (value.course_code || 'N/A') + '</td>';
                    html += '<td>' + (value.course_name || 'N/A') + '</td>';
                    html += '<td>' + (value.co_course_cr || 'N/A') + '</td>';
                    html += '<td>' + (value.total_students || 0) + '</td>';
                    html += '<td colspan="6">Student Feedback Not Submitted Yet</td>';
                    html += '</tr>';
                } else if (value.total_students != 0) {  
                    html += '<tr>';
                    html += '<td>' + srno + '</td>';
                    html += '<td>' + (value.teacher_name || 'N/A') + '</td>';
                    html += '<td>' + (value.course_code || 'N/A') + '</td>';
                    html += '<td>' + (value.course_name || 'N/A') + '</td>';
                    html += '<td>' + (value.co_course_cr || 'N/A') + '</td>';
                    html += '<td>' + (value.total_students || 0) + '</td>';
                    var currentSessionId = $('#session_sel').val();
                    if (currentSessionId >= 165) {
                         html += '<td>' + (value.feedback_submitted || 0) + '</td>';
                    } else {
                         html += '<td>' + (value.new_feedback_submitted || value.feedback_submitted || 0) + '</td>';
                    }
                    html += '<td>' + (value.core_summary || 'N/A') + '</td>';
                    html += '<td>' + (value.support_summary || 'N/A') + '</td>';
                    html += '<td>' + (value.overall_summary || 'N/A') + '</td>';
                    html += '<td>' + (value.co_attendance_percentage || 'N/A') + '</td>';
                    html += '<td><a style="color:black" href="{{ route('faculty_comments_show') }}/' + co_id + '">Comments</a></td>';
                    html += '</tr>';
                }
            });
            
            if (html != '') {
                $('#result-body-f2025').html(html);
            } else {
                $('#result-body-f2025').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
            }
        }

        // Function: Display Old/New results (Quality, Course Evaluation, etc.)
        function displayOldResults(data) {
            var html = '';
            $.each(data, function(key, value) {
                var srno = parseInt(key + 1);
                var co_id = value.fc_co_id;
                
                if (value.feedback_submitted == 0) {
                    html += '<tr>';
                    html += '<td>' + srno + '</td>';
                    html += '<td>' + (value.teacher_name || 'N/A') + '</td>';
                    html += '<td>' + (value.course_name || 'N/A') + '</td>';
                    html += '<td>' + (value.co_course_cr || 'N/A') + '</td>';
                    html += '<td>' + (value.total_students || 0) + '</td>';
                    html += '<td colspan="5">Student Feedback Not Submitted Yet</td>';
                    html += '</tr>';
                }
                if (value.feedback_submitted != 0 && value.total_students != 0) {
                    html += '<tr>';
                    html += '<td>' + srno + '</td>';
                    html += '<td>' + (value.teacher_name || 'N/A') + '</td>';
                    html += '<td>' + (value.course_name || 'N/A') + '</td>';
                    html += '<td>' + (value.co_course_cr || 'N/A') + '</td>';
                    html += '<td>' + (value.total_students || 0) + '</td>';
                    html += '<td>' + (value.feedback_submitted || 0) + '</td>';
                    html += '<td>' + (value.quality_of_teaching || 'N/A') + '</td>';
                    html += '<td>' + (value.course_evaluation || 'N/A') + '</td>';
                    html += '<td>' + (value.exam_and_assessment || 'N/A') + '</td>';
                    html += '<td>' + (value.overall_rating || 'N/A') + '</td>';
                    html += '<td><a style="color:black" href="{{ route('faculty_comments_show') }}/' + co_id + '">Comments</a></td>';
                    html += '</tr>';
                }
            });
            
            if (html != '') {
                $('#result-body').html(html);
            } else {
                $('#result-body').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
            }
        }


        function getRequest(endPoint, successBlock, failBlock) {
            $.ajax({
                url: "{{ url('/') }}" + endPoint,
                type: "GET",
                dataType: "json",
                success: successBlock
            }).fail(failBlock);
        }

        // Helper function: Show or hide an option in dropdown
        function toggleOption(option, show) {
            if (!option) return;
            if (show) {
                option.style.display = '';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        }

        // Helper function: Show or hide button groups
        function toggleButtons(showOriginal, showNew) {
            const originalButtons = document.getElementById('original_buttons');
            const newButtons = document.getElementById('new_buttons');
            
            if (originalButtons) {
                originalButtons.style.display = showOriginal ? 'block' : 'none';
            }
            if (newButtons) {
                newButtons.style.display = showNew ? 'block' : 'none';
            }
        }

        // Main function: Handle session change and update report type options
        function on_session_change(session) {
            console.log(session.value);
            
            // Get all elements we need
            const reportTypeSelector = document.getElementById('report_type_selector');
            const f2025Option = reportTypeSelector.querySelector('option[value="f2025onward"]');
            const oldOption = reportTypeSelector.querySelector('option[value="old"]');
            const newOption = reportTypeSelector.querySelector('option[value="new"]');
            
            // Get session ID (convert to number)
            const sessionId = parseInt(session.value) || 0;
            
            // Rule 1: If session is 165 or above, show only F2025 Onwards
            if (sessionId >= 165) {
                toggleOption(f2025Option, true);   // Show F2025 Onwards
                toggleOption(oldOption, false);    // Hide Old
                toggleOption(newOption, false);     // Hide New
                
                reportTypeSelector.value = 'f2025onward';  // Auto-select F2025
                toggleButtons(true, false);  // Show Get Result button
            }
            // Rule 2: If session is below 165, show only Old and New
            else if (sessionId > 0) {
                toggleOption(f2025Option, false);  // Hide F2025 Onwards
                toggleOption(oldOption, true);      // Show Old
                toggleOption(newOption, true);      // Show New
                
                // If F2025 was selected, clear it
                if (reportTypeSelector.value === 'f2025onward') {
                    reportTypeSelector.value = '';
                }
                
                toggleButtons(true, false);  // Show Get Result button
                reportTypeSelector.dispatchEvent(new Event('change'));
            }
            // Rule 3: If no session selected, show all options
            else {
                toggleOption(f2025Option, true);
                toggleOption(oldOption, true);
                toggleOption(newOption, true);
            }
        }

        function getDepartments() {
            var school_id = $('#school_sel').val();
            getRequest('/department/get?school_id=' + school_id, function(res) {
                if (res.status) {
                    var html = '<option value="">All Dept</option>';
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.dept_id + '" >' + value.dept_name + '</option>';
                    });
                    $('select[name="department"]').html(html);
                    getTeacher();
                }
            }, function(res) {
                alert('Some Error found!');
            });
        }

        function getTeacher() {
            let session_id = $('#session_sel').val();
            let dept_id = $('#department').val();
            let school_id = $('#school_sel').val();
            var url = '/teacher/get?depID=' + dept_id + '&session_sel=' + session_id + '&school_id=' + school_id;
            getRequest(url, function(res) {
                if (res.status) {
                    var html = '<option value="">All Teacher</option>';
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.id + '" >' + value.name + '</option>';
                    });
                    $('select[name="teacher"]').html(html);
                }
            }, function(res) {
                alert('Some Error found!');
            });

            getCourses();
        }

        function getPrograms() {
            var dept_id = $('#department').val();
            getRequest('/program/get?dept_id=' + dept_id, function(res) {
                if (res.status) {
                    var html = '<option value="">All Prog</option>';
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.prog_id + '" >' + value.prog_name + '</option>';
                    });
                    $('select[name="program"]').html(html);
                }
            }, function(res) {
                alert('Some Error found!');
            });
        }

        function getPDF() {
            //var lastURL = lastURL.replace("/reports/faculty", " ");
            //var lastURL='/reports/faculty';
            //alert(lastURL+'&result-for=print');
            //return false;
            // Check if lastURL exists
            if (!lastURL || lastURL === '') {
                if (typeof showToast !== 'undefined') {
                    showToast('error', 'Please get results first before generating PDF.', 'Error');
                } else {
                    alert('Please get results first before generating PDF.');
                }
                return false;
            }
            
            // Show spinner
            const pdfBtn = document.getElementById('getPDFBtn');
            const pdfBtnText = document.getElementById('getPDFBtnText');
            const pdfSpinner = document.getElementById('getPDFSpinner');
            
            if (pdfBtn && pdfBtnText && pdfSpinner) {
                pdfBtn.disabled = true;
                pdfBtnText.textContent = 'Generating PDF...';
                pdfSpinner.style.display = 'inline-block';
            }
            
            // Open PDF in new window
            window.open(lastURL + '&result-for=print', '_blank');
            
            // Hide spinner after a delay (PDF generation takes time)
            setTimeout(function() {
                if (pdfBtn && pdfBtnText && pdfSpinner) {
                    pdfBtn.disabled = false;
                    pdfBtnText.textContent = 'GET PDF';
                    pdfSpinner.style.display = 'none';
                }
                
                // PDF will open in browser, no success message needed
            }, 2000);

        }

        function getResultsByTeacher() {
            $('#facultyReport').show()
            $('#faculty_evaluation_report').html('')
            var sess = $('#session_sel').val();
            // if(sess == ''){
            //     alert('Please Select Session First!'); return false;
            // }
            var tID = $('#tid_input').val();
            if (tID == '') {
                alert('Please Select Teacher First!');
                return false;
            }
            var url = '/reports/faculty/get-results?tid=' + tID + '&session=' + sess;
            getResults(url);
        }

        function getResultsByProgDept() {
            // Show spinner
            const resultBtn = document.getElementById('getResultBtn');
            const resultBtnText = document.getElementById('getResultBtnText');
            const resultSpinner = document.getElementById('getResultSpinner');
            
            if (resultBtn && resultBtnText && resultSpinner) {
                resultBtn.disabled = true;
                resultBtnText.textContent = 'Loading...';
                resultSpinner.style.display = 'inline-block';
            }
            
            $('#facultyReport').show()
            $('#faculty_evaluation_report').html('')
            var session = $('#session_sel').val();
            var school = $('#school_sel').val();
            var dept = $('#department').val();
            var prog = $('#program').val();
            
            // Validation
            if ((session == '') && (school == '') && (dept == '') && (prog == '')) {
                // Hide spinner
                if (resultBtn && resultBtnText && resultSpinner) {
                    resultBtn.disabled = false;
                    resultBtnText.textContent = 'Get Result';
                    resultSpinner.style.display = 'none';
                }
                
                // Show error toast
                if (typeof showToast !== 'undefined') {
                    showToast('error', 'Please select at least one filter criteria (Session, School, Department, or Program).', 'Validation Error');
                } else {
                    alert('Please select at least one filter criteria.');
                }
                return false;
            }

            console.log("Session:", session);
            console.log("School:", school);
            console.log("Department:", dept);
            console.log("Program:", prog);

            var data = $('#filterFormOne').serialize();
            var url = '/reports/faculty/get-results?' + data;
            
            // Call getResults with callback to hide spinner
            getResults(url, function() {
                // Hide spinner on success
                if (resultBtn && resultBtnText && resultSpinner) {
                    resultBtn.disabled = false;
                    resultBtnText.textContent = 'Get Result';
                    resultSpinner.style.display = 'none';
                }
            }, function() {
                // Hide spinner on error
                if (resultBtn && resultBtnText && resultSpinner) {
                    resultBtn.disabled = false;
                    resultBtnText.textContent = 'Get Result';
                    resultSpinner.style.display = 'none';
                }
            });
        }


        function getCourses() {
            var teacher_id = $('#teacher').val();
            var sess_id = $('#session_sel').val();
            var dept_id = $('#department').val();


            getRequest('/teacher_courses/get?id=' + teacher_id + '&sess_id=' + sess_id + '&dept_id=' + dept_id, function(
                res) {
                if (res.status) {
                    var html = '<option value="">All Courses</option>';
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.co_id + '" >' + value.course_code + ' | ' + value
                            .course_cd_name + ' (' + value.co_section + ') </option>';
                    });
                    $('select[name="course"]').html(html);
                }
            }, function(res) {
                alert('Some Error found!');
            });
        }


        $(document).ready(function() {
            // Initialize report type based on selected session on page load
            const sessionSelect = document.getElementById('session_sel');
            if (sessionSelect && sessionSelect.value) {
                on_session_change(sessionSelect);
            }
            
            $('#teacher_input').typeahead({
                onSelect: function(item) {
                    console.log(item);
                    $('#tid_input').val(item.value);
                },
                ajax: {
                    url: "{{ url('/teacher/get') }}",
                    timeout: 500,
                    displayField: "name",
                    triggerLength: 2,
                    method: "get",
                    loadingClass: "loading-circle",
                    preDispatch: function(query) {
                        //showLoadingMask(true);
                        return {
                            search: query
                        }
                    },
                    preProcess: function(data) {
                        // showLoadingMask(false);
                        if (data.success === false) {
                            // Hide the list, there was some error
                            return false;
                        }
                        // We good!
                        return data.data;
                    }
                }
            });
        });





///////////////////////////////// Faculty Evaluation report School wise  ///////////////////////////////////////////



function getSchoolwiseReport() {
        $('#facultyReport').hide();
        $('#faculty_evaluation_report').html('');

        // Show spinner and change button text
        $('#spinnerIcon').show();
        $('#buttonText').text('Report Generating...');

        let session = $('#session_sel1').val();
        let school = $('#school_sel1').val();

        if (!session) {
            let notificationText = 'Please select a session.';
            alert(notificationText);
            // Hide spinner and revert button text if session is not selected
            $('#spinnerIcon').hide();
            $('#buttonText').text('School Wise');
            return;
        }

        let url = '/reports/faculty/get-results?session=' + session;
        if (school) {
            url += '&school=' + school;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                // Hide spinner and revert button text
                $('#spinnerIcon').hide();
                $('#buttonText').text('School Wise');

                // Process the response data
                reportCreation(res.data);
            },
            error: function(xhr, status, error) {
                // Hide spinner and revert button text
                $('#spinnerIcon').hide();
                $('#buttonText').text('School Wise');

                // Handle the error response
                console.error("Error: " + error);
            }
        });
    }


        function reportCreation(data) {
            const reportData = {
                school: [],
                total_faculty: [], // Array to store total faculty count for each school
                '5.00 - 4.00': [],
                '4.00 - 3.00': [],
                '3.00 - 2.00': [],
                '2.00 - 1.00': [],
                '1.00 - 0.00': []
            };

            // Populate school array
            data.forEach(item => {
                if (!reportData.school.includes(item.school_code)) {
                    reportData.school.push(item.school_code);
                    reportData.total_faculty.push(0); // Initialize count for the school
                }
            });

            // Initialize count arrays for each range
            const schoolCount = reportData.school.length;
            reportData['5.00 - 4.00'] = Array(schoolCount).fill(0);
            reportData['4.00 - 3.00'] = Array(schoolCount).fill(0);
            reportData['3.00 - 2.00'] = Array(schoolCount).fill(0);
            reportData['2.00 - 1.00'] = Array(schoolCount).fill(0);
            reportData['1.00 - 0.00'] = Array(schoolCount).fill(0);

            // Update counts
            data.forEach(item => {
                // Increment total faculty count for the school
                const schoolIndex = reportData.school.indexOf(item.school_code);
                reportData.total_faculty[schoolIndex]++;

                // Determine the evaluation range and update counts
                const evaluation = parseFloat(item.course_evaluation);
                if (!isNaN(evaluation)) {
                    if (evaluation >= 4.00 && evaluation <= 5.00) {
                        reportData['5.00 - 4.00'][schoolIndex]++;
                    } else if (evaluation >= 3.00 && evaluation < 4.00) {
                        reportData['4.00 - 3.00'][schoolIndex]++;
                    } else if (evaluation >= 2.00 && evaluation < 3.00) {
                        reportData['3.00 - 2.00'][schoolIndex]++;
                    } else if (evaluation >= 1.00 && evaluation < 2.00) {
                        reportData['2.00 - 1.00'][schoolIndex]++;
                    } else if (evaluation >= 0.00 && evaluation < 1.00) {
                        reportData['1.00 - 0.00'][schoolIndex]++;
                    }
                }
            });

            console.log(reportData);
            const facultyEvaluationReportDiv = document.getElementById('faculty_evaluation_report');
            facultyEvaluationReportDiv.innerHTML = makeTableHtml(reportData);
        }

        function makeTableHtml(reportData) {
    const color = 'lightblue';

    // Calculate the sum of each column
    const sumTotalFaculty = reportData.total_faculty.reduce((acc, curr) => acc + curr, 0);
    const sum5to4 = reportData['5.00 - 4.00'].reduce((acc, curr) => acc + curr, 0);
    const sum4to3 = reportData['4.00 - 3.00'].reduce((acc, curr) => acc + curr, 0);
    const sum3to2 = reportData['3.00 - 2.00'].reduce((acc, curr) => acc + curr, 0);
    const sum2to1 = reportData['2.00 - 1.00'].reduce((acc, curr) => acc + curr, 0);
    const sum1to0 = reportData['1.00 - 0.00'].reduce((acc, curr) => acc + curr, 0);

    // Generate the school options for the dropdown
    const schoolOptions = reportData.school.map(school => `<option value="${school}">${school}</option>`).join('');

    const tableRows = reportData.school.map((school, index) => `
        <tr class="text-center school-row" data-school="${school}">
            <td><strong>${school}</strong></td>
            <td>${reportData.total_faculty[index]}</td>
            <td>
                <div>${reportData['5.00 - 4.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['5.00 - 4.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['4.00 - 3.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['4.00 - 3.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['3.00 - 2.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['3.00 - 2.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['2.00 - 1.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['2.00 - 1.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['1.00 - 0.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['1.00 - 0.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
        </tr>
    `).join('');

    // <div class='col-6 m-2'>
    //             <select name="schoolFilter" id="schoolFilter" class="form-control" onchange="schoolFilterChange(this);">
    //                 <option value="">All Schools</option>
    //                 ${schoolOptions}
    //             </select>
    //         </div>





    var select = document.getElementById('session_sel1');
    var sessionText = select.options[select.selectedIndex].text;







    // let sessionText = select.options[select.selectedIndex].text;
    // sessionText=document.getElementById('session_sel1').innerText;


    const tableHtml = `


        <button class="btn btn-primary btn-sm ml-2" onclick="exportToPDF('exportTable')">
            <i class="fa fa-file-pdf-o"></i> Export to PDF
            </button>

              <button class="btn btn-primary btn-sm ml-2" onclick="exportToExcel('exportTable')">
            <i class="fa fa-file-excel-o"></i> Export to Excel
            </button>




 <div class="mt-4">
        <div class="row h-100">
            <!-- First Column: Centered Text -->
            <div class="col-sm-4 d-flex align-items-center justify-content-center">
                <!-- Content for the first column, if any -->
            </div>
            <!-- Second Column: Centered Text -->
            <div class="col-sm-4 text-center">
                <h4>Analysis of Faculty Evaluation</h4>
                <h5>Session: <span id="sessionText">${sessionText}</span></h5>

            </div>
            <!-- Third Column: Logo Image Aligned to the Right -->
            <div class="col-sm-4 d-flex align-items-center justify-content-center">
                <img src="{{ asset('images/bg/bnu_logo.png') }}" alt="Logo" height="100" width="100">
            </div>
        </div>
    </div>




        <table class="table table-bordered text-center" id="exportTable">


            <thead style="background-color: #1E90FF;">
                <strong>
                    <tr>
                        <th><h5>School</h5></th>
                        <th><h5>Total Faculty</h5></th>
                        <th><h5>5.00 - 4.00</h5></th>
                        <th><h5>4.00 - 3.00</h5></th>
                        <th><h5>3.00 - 2.00</h5></th>
                        <th><h5>2.00 - 1.00</h5></th>
                        <th><h5>1.00 - 0.00</h5></th>
                    </tr>
                </strong>
            </thead>
            <tbody>
                ${tableRows}
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>${sumTotalFaculty}</strong></td>
                    <td><strong>${sum5to4}</strong></td>
                    <td><strong>${sum4to3}</strong></td>
                    <td><strong>${sum3to2}</strong></td>
                    <td><strong>${sum2to1}</strong></td>
                    <td><strong>${sum1to0}</strong></td>
                </tr>
                <tr style="background-color: ${color};">
                    <td></td>
                    <td><strong>Total Percentage %</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum5to4 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum4to3 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum3to2 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum2to1 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum1to0 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                </tr>
            </tbody>
        </table>
    `;

    return tableHtml;
}

// Filter function
function schoolFilterChange(selectElement) {
    const selectedSchool = selectElement.value;
    const rows = document.querySelectorAll('.school-row');

    rows.forEach(row => {
        if (selectedSchool === "" || row.dataset.school === selectedSchool) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}




        // function exportToExcel() {
        //     const table = document.getElementById('htmlTable');
        //     const rows = table.querySelectorAll('tr');

        //     let csvContent = "data:text/csv;charset=utf-8,";
        //     rows.forEach(row => {
        //         const cells = row.querySelectorAll('td, th');
        //         const rowData = Array.from(cells).map(cell => cell.textContent.replace(',', ''));
        //         csvContent += rowData.join(',') + "\n";
        //     });

        //     const encodedUri = encodeURI(csvContent);
        //     const link = document.createElement('a');
        //     link.setAttribute('href', encodedUri);
        //     link.setAttribute('download', 'table.csv');
        //     document.body.appendChild(link);
        //     link.click();
        // }

        // function exportToExcel(tableId) {
        //     $(`#${tableId}`).tableExport({
        //         type: 'xlsx',
        //         escape: 'false'
        //     });
        // }

        // function exportToPDF(tableId) {
        //   try {
        //     const doc = new jsPDF();
        //     doc.autoTable({ html: `#${tableId}` });
        //     doc.save('table.pdf');
        //   } catch (error) {
        //     console.error('Error exporting to PDF:', error);
        //     alert('An error occurred while exporting to PDF. Please try again.');
        //   }
        // }



        function exportToExcel(tableId) {
    let wb = XLSX.utils.book_new(); // Create a new workbook
    let ws_data = []; // Initialize worksheet data array

    let originalTable = document.getElementById(tableId);
    let rows = originalTable.querySelectorAll('tr');
    // Process header row
    let headerRow = [];
    let headerStyles = []; // Array to hold header cell styles
    rows[0].querySelectorAll('th').forEach((th, index) => {
        headerRow.push(th.innerText.trim()); // Original header
        if (index > 1) { // Skip first two headers (School and Total Faculty)
            headerRow.push(`${th.innerText.trim()} (%)`); // New percentage header
        }

        // Apply styles to header row
        headerStyles.push({
            fill: { fgColor: { rgb: "4F81BD" } }, // Modern blue color
            font: { sz: 12, bold: true, color: { rgb: "FFFFFF" } } // Font size (12), bold, white text
        });
    });
    ws_data.push(headerRow); // Add the processed header row to the worksheet data
    // Process body rows
    rows.forEach((row, rowIndex) => {
        if (rowIndex === 0) return; // Skip header row

        let rowData = [];
        row.querySelectorAll('td').forEach((cell, cellIndex) => {
            // Check if the cell contains count and percentage
            let divs = cell.querySelectorAll('div');
            if (divs.length === 2) {
                rowData.push(divs[0].innerText.trim()); // Count
                rowData.push(divs[1].innerText.trim()); // Percentage
            } else {
                rowData.push(cell.innerText.trim()); // Single value
            }
        });
        ws_data.push(rowData);
    });
    // Initialize arrays to store new total and percentage rows
    let newTotalRow = [];
    let newPercentageRow = [];
    // Process each row to extract data
    rows.forEach((row, rowIndex) => {
        // Check if the row is the old Total row
        if (rowIndex === rows.length - 2) {
            row.querySelectorAll('td').forEach((cell, cellIndex) => {
                newTotalRow.push(cell.innerText.trim());
                if (cellIndex > 1) {
                    newTotalRow.push(""); // Add empty column for percentage
                }
            });
        }
        // Check if the row is the old Total Percentage row
        if (rowIndex === rows.length - 1) {
            row.querySelectorAll('td').forEach((cell, cellIndex) => {
                newPercentageRow.push(cell.innerText.trim());
                if (cellIndex > 1) {
                    newPercentageRow.push(""); // Add empty column for percentage
                }
            });
        }
    });
    // Replace old rows with new rows in ws_data
    ws_data = ws_data.filter((_, index) => index < rows.length - 2); // Remove old rows
    // Add new rows to ws_data
    ws_data.push(newTotalRow);
    ws_data.push(newPercentageRow);
    // Convert array of arrays to worksheet with styling
    let ws = XLSX.utils.aoa_to_sheet(ws_data); // Convert array of arrays to worksheet
    // Apply header styles
    ws['!cols'] = ws_data[0].map(() => ({ wpx: 150 })); // Set column widths
    ws['!rows'] = ws_data.map((_, index) => (index === 0 ? { hpt: 30 } : {})); // Set row heights
    // Apply header styles to the first row
    for (let col = 0; col < ws_data[0].length; col++) {
        let cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
        if (!ws[cellAddress]) ws[cellAddress] = {}; // Ensure cell exists
        ws[cellAddress].s = headerStyles[col];
    }
    // Append worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1'); // Append worksheet to workbook
    XLSX.writeFile(wb, 'ExportedTable.xlsx'); // Export workbook to Excel file
}


///////////////////////////////// Faculty Evaluation report Department wise  ///////////////////////////////////////////



function getDepartmentwiseReport() {
        $('#facultyReport').hide();
        $('#faculty_evaluation_report').html('');

        // Show spinner and change button text
        $('#spinnerIcon2').show();
        $('#buttonText2').text('Report Generating...');

        let session = $('#session_sel1').val();
        if (!session) {
            let notificationText = 'Please select a session.';
            alert(notificationText);
            // Hide spinner and revert button text if session is not selected
            $('#spinnerIcon2').hide();
            $('#buttonText2').text('Department Wise');
            return;
        }

        let school = $('#school_sel1').val();
        let dept = $('#department1').val();

        let url = '/reports/faculty/get-results?session=' + session;

        if (school) {
            url += '&school=' + school;
        }

        if (dept) {
            url += '&department=' + dept;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                // Hide spinner and revert button text
                $('#spinnerIcon2').hide();
                $('#buttonText2').text('Department Wise');

                // Process the response data
                departmentReportCreation(res.data);
            },
            error: function(xhr, status, error) {
                // Hide spinner and revert button text
                $('#spinnerIcon2').hide();
                $('#buttonText2').text('Department Wise');

                // Handle the error response
                console.error("Error: " + error);
            }
        });
    }


function departmentReportCreation(data) {
    const reportData = {
        department: [],
        total_faculty: [],
        '5.00 - 4.00': [],
        '4.00 - 3.00': [],
        '3.00 - 2.00': [],
        '2.00 - 1.00': [],
        '1.00 - 0.00': []
    };

    // Populate department array
    data.forEach(item => {
        if (!reportData.department.includes(item.dept_name)) {
            reportData.department.push(item.dept_name);
            reportData.total_faculty.push(0); // Initialize count for the department
        }
    });

    // Initialize count arrays for each range
    const departmentCount = reportData.department.length;
    reportData['5.00 - 4.00'] = Array(departmentCount).fill(0);
    reportData['4.00 - 3.00'] = Array(departmentCount).fill(0);
    reportData['3.00 - 2.00'] = Array(departmentCount).fill(0);
    reportData['2.00 - 1.00'] = Array(departmentCount).fill(0);
    reportData['1.00 - 0.00'] = Array(departmentCount).fill(0);

    // Update counts
    data.forEach(item => {
        // Increment total faculty count for the department
        const deptIndex = reportData.department.indexOf(item.dept_name);
        reportData.total_faculty[deptIndex]++;

        // Determine the evaluation range and update counts
        const evaluation = parseFloat(item.course_evaluation);
        if (!isNaN(evaluation)) {
            if (evaluation >= 4.00 && evaluation <= 5.00) {
                reportData['5.00 - 4.00'][deptIndex]++;
            } else if (evaluation >= 3.00 && evaluation < 4.00) {
                reportData['4.00 - 3.00'][deptIndex]++;
            } else if (evaluation >= 2.00 && evaluation < 3.00) {
                reportData['3.00 - 2.00'][deptIndex]++;
            } else if (evaluation >= 1.00 && evaluation < 2.00) {
                reportData['2.00 - 1.00'][deptIndex]++;
            } else if (evaluation >= 0.00 && evaluation < 1.00) {
                reportData['1.00 - 0.00'][deptIndex]++;
            }
        }
    });

    console.log(reportData);
    const facultyEvaluationReportDiv = document.getElementById('faculty_evaluation_report');
    facultyEvaluationReportDiv.innerHTML = makeDepartmentTableHtml(reportData);
}

function makeDepartmentTableHtml(reportData) {
    const color = 'lightblue';

    // Calculate the sum of each column
    const sumTotalFaculty = reportData.total_faculty.reduce((acc, curr) => acc + curr, 0);
    const sum5to4 = reportData['5.00 - 4.00'].reduce((acc, curr) => acc + curr, 0);
    const sum4to3 = reportData['4.00 - 3.00'].reduce((acc, curr) => acc + curr, 0);
    const sum3to2 = reportData['3.00 - 2.00'].reduce((acc, curr) => acc + curr, 0);
    const sum2to1 = reportData['2.00 - 1.00'].reduce((acc, curr) => acc + curr, 0);
    const sum1to0 = reportData['1.00 - 0.00'].reduce((acc, curr) => acc + curr, 0);

    // Generate the department options for the dropdown
    const departmentOptions = reportData.department.map(department => `<option value="${department}">${department}</option>`).join('');

    const tableRows = reportData.department.map((department, index) => `
        <tr class="text-center department-row" data-department="${department}">
            <td><strong>${department}</strong></td>
            <td>${reportData.total_faculty[index]}</td>
            <td>
                <div>${reportData['5.00 - 4.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['5.00 - 4.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['4.00 - 3.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['4.00 - 3.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['3.00 - 2.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['3.00 - 2.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['2.00 - 1.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['2.00 - 1.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
            <td>
                <div>${reportData['1.00 - 0.00'][index] || 0}</div>
                <div style="background-color: ${color};">
                    ${((reportData['1.00 - 0.00'][index] || 0) / reportData.total_faculty[index] * 100).toFixed(2)}%
                </div>
            </td>
        </tr>
    `).join('');


    // <div class='col-6 m-2'>
    //             <select name="departmentFilter" id="departmentFilter" class="form-control" onchange="departmentFilterChange(this);">
    //                 <option value="">All Departments</option>
    //                 ${departmentOptions}
    //             </select>
    //         </div>



    var select = document.getElementById('session_sel1');
    var sessionText = select.options[select.selectedIndex].text;




    // let sessionText = select.options[select.selectedIndex].text;
    // sessionText=document.getElementById('session_sel1').innerText;


    const tableHtml = `

        <button class="btn btn-primary btn-sm ml-2" onclick="exportToPDF('exportTable')">
            <i class="fa fa-file-pdf-o"></i> Export to PDF
            </button>

              <button class="btn btn-primary btn-sm ml-2" onclick="exportToExcel('exportTable')">
            <i class="fa fa-file-excel-o"></i> Export to Excel
            </button>



        <div class="mt-4">
                <div class="row h-100">
                    <!-- First Column: Centered Text -->
                    <div class="col-sm-4 d-flex align-items-center justify-content-center">
                        <!-- Content for the first column, if any -->
                    </div>
                    <!-- Second Column: Centered Text -->
                    <div class="col-sm-4 text-center">
                        <h4>Analysis of Faculty Evaluation</h4>
                        <h5>Session: <span id="sessionText">${sessionText}</span></h5>

                    </div>
                    <!-- Third Column: Logo Image Aligned to the Right -->
                    <div class="col-sm-4 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('images/bg/bnu_logo.png') }}" alt="Logo" height="100" width="100">
                    </div>
                </div>
            </div>






        <table class="table table-bordered text-center" id="exportTable">


            <thead style="background-color: #1E90FF;">
                <strong>
                    <tr>
                        <th><h5>School</h5></th>
                        <th><h5>Total Faculty</h5></th>
                        <th><h5>5.00 - 4.00</h5></th>
                        <th><h5>4.00 - 3.00</h5></th>
                        <th><h5>3.00 - 2.00</h5></th>
                        <th><h5>2.00 - 1.00</h5></th>
                        <th><h5>1.00 - 0.00</h5></th>
                    </tr>
                </strong>
            </thead>
            <tbody>
                ${tableRows}
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>${sumTotalFaculty}</strong></td>
                    <td><strong>${sum5to4}</strong></td>
                    <td><strong>${sum4to3}</strong></td>
                    <td><strong>${sum3to2}</strong></td>
                    <td><strong>${sum2to1}</strong></td>
                    <td><strong>${sum1to0}</strong></td>
                </tr>
                <tr style="background-color: ${color};">
                    <td></td>
                    <td><strong>Total Percentage %</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum5to4 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum4to3 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum3to2 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum2to1 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                    <td><strong>${(sumTotalFaculty !== 0 ? ((sum1to0 / sumTotalFaculty) * 100).toFixed(2) : 0)}%</strong></td>
                </tr>
            </tbody>
        </table>
    `;

    return tableHtml;
}

// Filter function
function departmentFilterChange(selectElement) {
    const selectedDepartment = selectElement.value;
    const rows = document.querySelectorAll('.department-row');

    rows.forEach(row => {
        if (selectedDepartment === "" || row.dataset.department === selectedDepartment) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

















    </script>




    <script>



async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    // const pdf = new jsPDF('p', 'pt', 'a4');
    const pdf = new jsPDF('p', 'pt', 'legal');

    const facultyReport = document.getElementById('faculty_evaluation_report');
    const clone = facultyReport.cloneNode(true);

    // Remove all buttons from the clone
    const buttons = clone.querySelectorAll('button');
    buttons.forEach(button => button.remove());

    // Hide the cloned element (will be used for rendering only)
    clone.style.position = 'absolute';
    clone.style.left = '-9999px';
    document.body.appendChild(clone);

    await html2canvas(clone, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdf.internal.pageSize.getWidth();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
    });

    document.body.removeChild(clone);

    pdf.save('faculty_evaluation_report.pdf');
}


function showNotification(message, type, targetDivId, options = {}) {
    // Default options for notification
    const defaultOptions = {
        duration: 5000, // Duration to display the notification (in ms)
        bounceEffect: true, // Enable/disable bounce effect
        slideInSpeed: 500, // Speed of slide-in animation
        fadeOutSpeed: 500, // Speed of fade-out animation
    };

    // Merge default options with user-provided options
    const settings = { ...defaultOptions, ...options };

    // Clear any existing notifications in the target container
    $('#' + targetDivId).empty();

    // Determine the alert class and icon based on the type
    const alertClass = `alert-${type}`;
    let icon = '';

    // Set the icon based on the type
    switch (type) {
        case 'success':
            icon = '<i class="fa fa-check-circle"></i>';
            break;
        case 'danger':
            icon = '<i class="fa fa-times-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fa fa-exclamation-triangle"></i>';
            break;
        case 'info':
            icon = '<i class="fa fa-info-circle"></i>';
            break;
        default:
            icon = ''; // No icon for unknown types
            break;
    }

    // Create the notification HTML
    const notificationHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="display:none; transform: translateY(-20px); opacity: 0;">
            <strong>${icon} ${message}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Append the notification to the target container
    const notificationElement = $(notificationHtml);
    $('#' + targetDivId).append(notificationElement);

    // Apply slide-in and optional bounce effect using CSS animations
    notificationElement.css({ display: 'block', transition: `transform ${settings.slideInSpeed}ms, opacity ${settings.slideInSpeed}ms` });
    setTimeout(() => {
        notificationElement.css({ transform: 'translateY(0)', opacity: 1 });

        if (settings.bounceEffect) {
            setTimeout(() => {
                notificationElement.css({ transform: 'translateY(-10px)' });
                setTimeout(() => {
                    notificationElement.css({ transform: 'translateY(0)' });
                }, 150); // Bounce back
            }, settings.slideInSpeed);
        }

        // Automatically hide the notification after the specified duration
        setTimeout(() => {
            notificationElement.css({ transition: `opacity ${settings.fadeOutSpeed}ms`, opacity: 0 });
            setTimeout(() => {
                notificationElement.remove(); // Remove the notification from the DOM
            }, settings.fadeOutSpeed);
        }, settings.duration);
    }, 50); // Delay for rendering
}







    </script>

    <!-- Include Toast Notifications Component -->
    @include('components.toast-notifications')

@stop
