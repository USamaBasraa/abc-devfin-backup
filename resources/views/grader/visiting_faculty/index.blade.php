@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')
{{-- @dd("123"); --}}

<style>
/* Figma-aligned Visiting Faculty presentation layer.
   Existing field IDs, onchange handlers, form action flow and JavaScript remain unchanged. */
.visiting-faculty-page {
    width: 100%;
    min-height: calc(100vh - 20px);
    margin: 0 !important;
    /* padding: 30px 26px 44px; */
    background: #f2f6f9;
    color: #252525;
    font-family: 'Montserrat', sans-serif;
}

.visiting-faculty-page .vf-page-heading {
    margin: 0 0 20px !important;
    padding: 0 !important;
}

.visiting-faculty-page .vf-page-heading > a {
    display: block !important;
    width: auto !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    color: #252525 !important;
    font-size: 20px !important;
    line-height: 1.35 !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    box-shadow: none !important;
    pointer-events: none;
}

.visiting-faculty-page .vf-subtitle {
    display: block;
    margin-top: 2px;
    color: #252525;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 400;
}

.visiting-faculty-page #new_record_form {
    margin: 0;
}

.visiting-faculty-page #new_record_form > .row,
.visiting-faculty-page #new_record_form > .row > .col {
    margin: 0;
    padding: 0;
}

.visiting-faculty-page #multiCollapseExample1 {
    display: block !important;
}

.visiting-faculty-page .vf-form-card {
    margin: 0 !important;
    padding: 30px 27px 20px !important;
    border: 0 !important;
    border-radius: 20px !important;
    background: #fff !important;
    box-shadow: none !important;
}

.visiting-faculty-page .vf-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 24px;
    row-gap: 26px;
}

.visiting-faculty-page .vf-field {
    min-width: 0;
}

.visiting-faculty-page .vf-field label {
    display: block !important;
    width: 100%;
    max-width: none;
    margin: 0 0 8px !important;
    padding: 0 !important;
    color: #111827;
    font-size: 14px;
    line-height: 1.5;
    font-weight: 600;
}

.visiting-faculty-page .vf-field .form-control,
.visiting-faculty-page .vf-field .select2-container .select2-selection--single {
    width: 100%;
    height: 48px !important;
    min-height: 48px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #252525 !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px !important;
    font-weight: 400;
    box-shadow: none !important;
}

.visiting-faculty-page .vf-field .form-control {
    padding: 12px 16px !important;
}

.visiting-faculty-page .vf-field .form-control::placeholder {
    color: #9ca3af;
    opacity: 1;
}

.visiting-faculty-page .vf-field .form-control:focus,
.visiting-faculty-page .vf-field .select2-container--focus .select2-selection--single,
.visiting-faculty-page .vf-field .select2-container--open .select2-selection--single {
    border-color: #17675f !important;
    box-shadow: 0 0 0 3px rgba(23,103,95,.08) !important;
    outline: none;
}

.visiting-faculty-page .vf-field .select2-container {
    width: 100% !important;
}

.visiting-faculty-page .vf-field .select2-container .select2-selection--single .select2-selection__rendered {
    height: 46px;
    padding: 0 38px 0 16px !important;
    color: #252525 !important;
    font-size: 14px;
    line-height: 46px !important;
}

.visiting-faculty-page .vf-field .select2-container .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af !important;
}

.visiting-faculty-page .vf-field .select2-container .select2-selection--single .select2-selection__arrow {
    top: 1px !important;
    right: 10px !important;
    width: 24px !important;
    height: 46px !important;
}

.visiting-faculty-page .vf-qualification-stack {
    display: grid;
    gap: 8px;
}

.visiting-faculty-page #validationMessage {
    display: block;
    min-height: 16px;
    margin-top: 5px;
    font-size: 11px;
    line-height: 1.35;
}

.visiting-faculty-page .vf-actions {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-top: 28px;
}

.visiting-faculty-page .vf-actions .btn-success {
    min-width: 117px;
    height: 50px;
    margin: 0;
    padding: 0 24px;
    border: 1px solid #17675f !important;
    border-radius: 8px !important;
    background: #17675f !important;
    color: #fff !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 600;
    box-shadow: none !important;
}

.visiting-faculty-page .vf-actions .btn-success:hover,
.visiting-faculty-page .vf-actions .btn-success:focus {
    border-color: #12564f !important;
    background: #12564f !important;
}

.visiting-faculty-page .vf-list-section {
    margin: 50px 0 0 !important;
}

.visiting-faculty-page .vf-list-section > [class*='col-'] {
    padding: 0;
}

.visiting-faculty-page .vf-list-card {
    margin: 0;
    border: 0;
    border-radius: 10px;
    background: transparent;
    box-shadow: none;
}

.visiting-faculty-page .vf-list-toolbar {
    display: flex;
    min-height: 50px !important;
    align-items: center;
    justify-content: flex-end;
    margin: 0 0 20px;
    padding: 0 !important;
    border: 0;
    background: transparent;
}

.visiting-faculty-page .vf-list-toolbar .card-title {
    float: none;
    margin: 0;
}

.visiting-faculty-page .vf-list-toolbar .btn {
    width: 73px;
    height: 40px;
    padding: 0;
    border: 1px solid #17675f !important;
    border-radius: 10px !important;
    background: #17675f !important;
    color: #fff !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 500;
    box-shadow: none !important;
}

.visiting-faculty-page .vf-list-toolbar .btn:hover,
.visiting-faculty-page .vf-list-toolbar .btn:focus {
    background: #12564f !important;
    color: #fff !important;
}

.visiting-faculty-page .vf-table-body {
    padding: 0 !important;
    border-radius: 10px;
    background: #fff;
    overflow-x: auto;
    overflow-y: visible;
}

.visiting-faculty-page .vf-table-body .table {
    width: 100%;
    min-width: 1540px;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    color: #252525;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
}

.visiting-faculty-page .vf-table-body .table thead {
    background: #17675f;
}

.visiting-faculty-page .vf-table-body .table thead th {
    height: 52px;
    padding: 14px 16px !important;
    border: 0 !important;
    background: #17675f !important;
    color: #fff !important;
    font-size: 13px;
    line-height: 1.35;
    font-weight: 500;
    white-space: nowrap;
    vertical-align: middle;
}

.visiting-faculty-page .vf-table-body .table thead th:first-child {
    border-top-left-radius: 10px;
}

.visiting-faculty-page .vf-table-body .table thead th:last-child {
    border-top-right-radius: 10px;
}

.visiting-faculty-page .vf-table-body .table tbody td {
    min-height: 54px;
    padding: 14px 16px !important;
    border: 0 !important;
    border-bottom: 1px solid #eef1f3 !important;
    background: #fff;
    color: #252525;
    line-height: 1.45;
    vertical-align: middle;
    white-space: normal;
}

.visiting-faculty-page .vf-table-body .table tbody tr:nth-child(even) td {
    background: #f5f5f5;
}

.visiting-faculty-page .vf-table-body .table tbody tr:last-child td {
    border-bottom: 0 !important;
}

@media (max-width: 991.98px) {
    .visiting-faculty-page {
        padding: 24px 18px 34px;
    }

    .visiting-faculty-page .vf-form-card {
        padding: 24px 20px 20px !important;
    }

    .visiting-faculty-page .vf-grid {
        grid-template-columns: 1fr;
        row-gap: 20px;
    }
}

@media (max-width: 575.98px) {
    .visiting-faculty-page {
        padding: 20px 12px 28px;
    }

    .visiting-faculty-page .vf-page-heading > a {
        font-size: 18px !important;
    }

    .visiting-faculty-page .vf-form-card {
        padding: 18px 14px !important;
        border-radius: 14px !important;
    }

    .visiting-faculty-page .vf-actions .btn-success {
        width: 100%;
    }
}
</style>

<div class="visiting-faculty-page">
    <p class="mb-0 pb-0 vf-page-heading">
        <a class="btn btn-primary d-flex align-items-center justify-content-start" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" style="
    text-align: justify;
">
            Visiting Faculty
        </a>
        {{-- <span class="vf-subtitle">Expert Minds</span> --}}
    </p>

    <form id="new_record_form">
        <div class="row">
            <div class="col">
                <div class="collapse show" id="multiCollapseExample1">
                    <div class="card card-body form-group m-0 vf-form-card">
                        <div class="vf-grid">
                            <div class="vf-field">
                                <label for="name">Visiting Faculty Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter Name" required>
                            </div>

                            <div class="vf-field">
                                <label for="cnic">CNIC</label>
                                <input type="text" oninput="validateCNIC()" class="form-control" minlength="13" maxlength="15" id="cnic" placeholder="Enter CNIC" required>
                                <small class="mb-0 pb-0" id="validationMessage"></small>
                            </div>

                            <div class="vf-field">
                                <label for="qualification">Terminal Qualification</label>
                                <div class="vf-qualification-stack">
                                    <input type="text" class="form-control" id="qualification" placeholder="Enter Qualification" required>
                                    <input type="text" class="form-control" id="qualification2" placeholder="Enter Qualification" required>
                                </div>
                            </div>

                            <div class="vf-field">
                                <label for="institute">Institute of Terminal Qualification</label>
                                <input type="text" class="form-control" id="institute" placeholder="Select Institute" required>
                            </div>

                            <div class="vf-field">
                                <label for="school">School</label>
                                <select class="form-control select2" id="school" onchange="getDepartments()">
                                    <option value="null" selected>Select School</option>
                                    @foreach($schools as $school)
                                    <option value='{{$school->school_id}}'>{{$school->school_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="vf-field">
                                <label for="department">Department</label>
                                <select class="form-control select2" onchange="getProgram()" aria-label="Default select example" name="department" id="department">
                                    <option selected>Select Department</option>
                                </select>
                            </div>

                            <div class="vf-field">
                                <label for="program">Academic Program</label>
                                <select class="form-control select2" id="program" onchange="getCourses()">
                                    <option selected>Select Program</option>
                                </select>
                            </div>

                            <div class="vf-field">
                                <label for="ige_course_id">Course</label>
                                <select class="form-control select2" id="ige_course_id">
                                    <option selected>Select Course</option>
                                </select>
                            </div>

                            <div class="vf-field">
                                <label for="course_code">Course Code</label>
                                <input type="text" class="form-control" id="course_code" placeholder="Enter Code" required>
                            </div>

                            <div class="vf-field">
                                <label for="credit_hours">Credit Hours</label>
                                <input type="number" class="form-control" id="credit_hours" placeholder="Enter Hours" required>
                            </div>

                            <div class="vf-field">
                                <label for="required_teaching_hours">Required Teaching Hours</label>
                                <input type="number" class="form-control" id="required_teaching_hours" placeholder="Enter Hours" required>
                            </div>

                            <div class="vf-field">
                                <label for="completed_teaching_hours">Completed Teaching Hours</label>
                                <input type="number" class="form-control" id="completed_teaching_hours" placeholder="Enter Hours" required>
                            </div>

                            <div class="vf-field">
                                <label for="contact_no">Contact Number</label>
                                <input type="number" class="form-control" id="contact_no" placeholder="Enter Number" required>
                            </div>

                            <div class="vf-field">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter Email" required>
                            </div>
                        </div>

                        <div class="vf-actions">
                            <button type="button" class="btn btn-success" onclick="SaveRecord()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row vf-list-section">
        <div class="col-lg-12 col-md-12">
            <div class="card vf-list-card">
                <div class="card-header mt-0 pt-0 mb-0 pb-0 vf-list-toolbar">
                    <h3 class="card-title">
                        <button class="btn btn-outline-info btn-sm" onclick="getPDF()">PDF</button>
                    </h3>
                </div>
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0 vf-table-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">CNIC</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Qualification</th>
                                <th scope="col">Institute</th>
                                <th scope="col">School</th>
                                <th scope="col">Department</th>
                                <th scope="col">Academic Program</th>
                                <th scope="col">Course</th>
                                <th scope="col">Credit Hrs</th>
                                <th scope="col">Required T. Hrs</th>
                                <th scope="col">Completed T. Hrs</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
    @section('javascript')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
		LoadFacultyList();



	});
function SaveRecord() {

	let data = {
	    name: $("#name").val(),
	    cnic: $("#cnic").val(),
	    qualification:  $("#qualification").val() + ', ' +$("#qualification2").val() ,
	    SCHOOL_ID: $("#school").val(),
	    DEPT_ID:  $("#department").val(),
	    PROG_ID:  $("#program").val(),
	    course_id:  $("#ige_course_id").val(),
	    credit_hr:  $("#credit_hours").val(),
	    required_teaching_hours:  $("#required_teaching_hours").val(),
	    completed_teaching_hours:  $("#completed_teaching_hours").val(),
        email: $("#email").val(),
        contact_no: $("#contact_no").val(),
        course_code: $("#course_code").val(),
        institute: $("#institute").val(),

	};

	//   $("#new_record_form").validate({
    //         rules: {
    //             username: "required",
    //             password: "required"
    //         },
    //         messages: {
    //             username: "Please enter your username",
    //             password: "Please enter your password"
    //         },
    //         submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: "/api/visiting_faculty/store",
                    data: data,
                    success: function(response) {
                    // Check if response is an array

                    // Handle the successful response from the server
                        console.log(response);
                        LoadFacultyList();

	                },
                    error: function(error) {
                    // Handle the error
                        console.error("Error:", error);
                    }

	        });
        //     return false;
        // }

		//    });


}

function validateCNIC() {
      const cnicInput = document.getElementById('cnic');
      const validationMessage = document.getElementById('validationMessage');
      const cnicRegex = /^[0-9]{5}-[0-9]{7}-[0-9]$/;

      if (cnicRegex.test(cnicInput.value)) {
        validationMessage.textContent = 'Valid CNIC number';
        validationMessage.style.color = 'green';
      } else {
        validationMessage.textContent = 'Invalid CNIC number';
        validationMessage.style.color = 'red';
      }
}
function getDepartments(){
	    if($("#school").val() != "null"){
		    	var data = {
		    		school_id : $("#school").val()
		    	};

		    $.ajax({
	    type: "POST",
	    url: "/api/visiting_faculty/get_departments",
	    data: data,
	    success: function(response) {
		$("#department").empty();

		// Check if response is an array
		if (Array.isArray(response)) {
		$("#department").append("<option value='null' selected>Select Department</option>");
		    response.forEach(function(item, index) {
		        $("#department").append("<option value='" + item.dept_id + "'>" + item.dept_name + "</option>");
		    });
		} else {
		    console.error("Error: Response is not an array");
		}

		// Handle the successful response from the server
		console.log(response);
	    },
	    error: function(error) {
		// Handle the error
		console.error("Error:", error);
	    }
    });

    	}
}
function LoadFacultyList(){

        $.ajax({
    type: "POST",
    url: "/api/visiting_faculty/get_faculty_list",
    success: function(response) {
    $("#tbody").empty();

    // Check if response is an array
    if (Array.isArray(response)) {

        response.forEach(function(item, index) {
            $("#tbody").append('<tr><td>'+(index+1)+'</td><td>'+item.faculty_name+'</td><td>'+item.cnic+'</td><td>'+item.vf_email + '<br>'+item.vf_contact_no+'</td><td>' +item.terminal_qualification+'</td><td>'+item.vf_institute_of_terminal_degree+'</td><td>'+item.school_name+'</td><td>'+item.dept_name+'</td><td>'+item.prog_name+'</td><td>'+ item.vf_course_code +' - '+item.course_name+'</td><td>'+item.credit_hours+'</td><td>'+item.required_teaching_hours+'</td><td>'+item.completed_teaching_hours+'</td></tr>');
        });
    } else {
        console.error("Error: Response is not an array");
    }

    // Handle the successful response from the server
    console.log(response);
    },
    error: function(error) {
    // Handle the error
    console.error("Error:", error);
    }
    });



}

  function getCourses(){
	    if($("#program").val() != "null"){
		    	var data = {
		    		prog_id : $("#program").val()
		    	};

		    $.ajax({
	    type: "POST",
	    url: "/api/visiting_faculty/get_courses",
	    data: data,
	    success: function(response) {
		$("#ige_course_id").empty();

		// Check if response is an array
		if (Array.isArray(response)) {
		$("#ige_course_id").append("<option value='null' selected>Select Course</option>");
		    response.forEach(function(item, index) {
                if(item.course_id != null){
                    $("#ige_course_id").append("<option value='" + item.course_id + "'>" + item.course_name + "</option>");
                }
		    });
		} else {
		    console.error("Error: Response is not an array");
		}

		// Handle the successful response from the server
		console.log(response);
	    },
	    error: function(error) {
		// Handle the error
		console.error("Error:", error);
	    }
});

    	}
    }
  function getProgram(){
	    if($("#department").val() != "null"){
		    	var data = {
		    		dept_id : $("#department").val()
		    	};

		    $.ajax({
	    type: "POST",
	    url: "/api/visiting_faculty/get_programs",
	    data: data,
	    success: function(response) {
		$("#program").empty();

		// Check if response is an array
		if (Array.isArray(response)) {
		$("#program").append("<option value='null' selected>Select Program</option>");
		    response.forEach(function(item, index) {
		        $("#program").append("<option value='" + item.prog_id + "'>" + item.prog_name + "</option>");
		    });
		} else {
		    console.error("Error: Response is not an array");
		}

		// Handle the successful response from the server
		console.log(response);
	    },
	    error: function(error) {
		// Handle the error
		console.error("Error:", error);
	    }
});

    	}
    }
function getStudentCourses(){


    var data = {
        show_all_courses : 1,
        sel_sess_id: $('#session').val(),

    };
    fetch('/api/exam/getSessionCourses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
    },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(dat => {
        // console.log(dat);
        $('#ige_course_id').empty();
        if (dat.length === 0) {
            // If no records found, add a disabled option

            $('#ige_course_id').append('<option value="default" disabled selected>No records found</option>');
        } else {
            // If there are records, add options to the select tag
            $('#ige_course_id').empty();
            $('#ige_course_id').append('<option value="default">Select Course</option>');

            // Loop through the data and add options
            dat.forEach(item => {
                $('#ige_course_id').append('<option value="' + item.co_id + '">' + item.course_code + ' | ' + item.course_cd_name +  ' | ' + item.co_course_cr  + ' | ' +item.co_section + '</option>');
            });
        }
    })
    .catch(error => {
        // Handle errors here
        // console.error('Error:', error);
    });
}

function getStudentCoursesDetails(){

    var data = {
        sel_co_id : $("#ige_course_id").val(),
        sel_sess_id: $('#session').val(),

    };
    fetch('/api/exam/get-course-details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
    },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(dat => {
        // console.log(dat);
        if (dat.length === 0) {
            // If no records found, add a disabled option
            $('#tbody').empty();

            $('#ige_course_id').append('<option value="default" disabled selected>No records found</option>');
        } else {
            // If there are records, add options to the select tag
            $('#tbody').empty();


            // Loop through the data and add options
            dat.forEach((item,index) => {
                html = '<tr><td>'+ (parseInt(index)+ parseInt(1)) + '</td><td>'+ item.sc_sai_enrollment_no+'</td><td>'+ item.spi_name+'</td><td>'+ item.sc_pc_course_cr+'</td><td>'+ item.gh_grade+'</td><td>';

                if( item.gh_teacher_fsubmit == 1){
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";
                }else{
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";

                }

                if( item.gh_hod_fsubmit == 1){
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";

                }else{
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";

                }

                if( item.gh_dean_fsubmit == 1){
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";

                }else{
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";
                }

                if( item.gh_exam_fsubmit == 1){
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok_disabled.png'></td><td>";
                }else{
                    html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";
                }
                html += item.std_fee_details+'</td><td>';
                console.log(item.sai_pf_remarks);
                doc_status_details = "";
                if((item.alevel_doc_status_details>0) || (item.olevel_doc_status_details>0)){
                    doc_status_details = " (";
                    if(item.alevel_doc_status_details>0){
                        doc_status_details = doc_status_details + item.alevel_doc_status_details  + " A , ";

                    }
                    if(item.olevel_doc_status_details>0){
                        doc_status_details = doc_status_details + item.olevel_doc_status_details  + " O , ";
                    }
                    doc_status_details = doc_status_details + " Levels Result Missing ) ";
                }
                // final_document_status 	= item.sai_pf_remarks + " " + doc_status_main + " " +doc_status_details;
                // final_document_status 	= item.sai_pf_remarks + " " +doc_status_details;
                        final_document_status 	= doc_status_details;

                if((doc_status_details=='') ){
                        final_doc_status ="OK";
                }else{
                        final_doc_status =final_document_status;
                }

                html +=  final_doc_status;
                html +='</td><td></tr>';
                $('#tbody').append(html);
            });
        }
    })
    .catch(error => {
        // Handle errors here
        // console.error('Error:', error);
    });
}


function getPDF(){
url = 'assets/cms/reports/visiting_faculty_list.php';
    window.open(url, '_blank');

}
</script>
@stop

