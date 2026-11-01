@extends('layouts.app')

@section('title')
  BNU CMS
@stop





@section('content')



<div class="container-fluid">
    <div class="card p-4 pt-4">

    <div class="card-header bg-primary   mb-3 text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Information</h5>
           
</div>

    <form id="searchStudentForm">
            @csrf
            <div class="row">
                <div class="col-6 col-sm-12 col-md-6">
                    <div class="input-group">
                        <input placeholder="Enter student enrollment number" class="form-control" type="text" name="studentEnrollment" id="categoryName">
                        <div class="input-group-append">
                            <button onclick='searchStudent()' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>


        <div id="dataDiv"   style="margin-top:20px;">
        </div>


</div>

</div>












@stop

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

@section('javascript')
<script>

function searchStudent(){
    let form = document.getElementById('searchStudentForm');
    let data = new FormData(form);
    data.append('getAllRec', '0'); // Append getAllRec to form data
    let successMsg=document.getElementById('successMessage');

    fetch(`/reports/get-student-rec`, {
        method: 'POST', // Using POST method
        body: data
    })
    .then(response => response.json())
    .then(response=>{
        if (response.success) {
            makeDataHtml(response.StudentData);
            renderPsychiatristComments(response.psychiatristComments)
        }
    })
}


function makeDataHtml(data) {
    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content

    if (data) {
        let lettersHtml = `
        <div class="container-fluid">
            <div class="card shadow-sm my-4">

       


                <div class="card-body ">
                    <div class="row align-items-center">
                        <!-- Enrollment Number -->
                        <div class="col-md-3 col-sm-6 col-12 text-left mb-3">
                            <p class="lead text-muted mb-0">
                                <strong>Enrollment Number:</strong> <span class="text-uppercase">${data.username}</span>
                            </p>
                        </div>
                        <!-- Status -->
                        <div class="col-md-3 col-sm-6 col-12 text-left mb-3">
                            <p class="lead text-muted mb-0">
                                <strong>Status:</strong> <br> <span class="badge bg-info text-white text-uppercase">${data.sai_student_status}</span>
                            </p>
                        </div>
                        <!-- Email -->
                        <div class="col-md-3 col-sm-6 col-12 text-left mb-3">
                            <p class="lead text-muted mb-0">
                                <strong>Email:</strong> <br> <span>${data.email}</span>
                            </p>
                        </div>
                        <!-- SPI Name and Student Image -->
                        <div class="col-md-3 col-sm-6 col-12 text-center mb-3">
                            <h4 class="text-uppercase fw-bold text-primary mt-4">${data.spi_name}</h4>
                            <img src="https://cms.bnu.edu.pk/students_pics/${data.spi_enrollment_no.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:100px; height:100px;">
                        </div>
                    </div>

                    <form class="mt-3" id="sendPsychiatristForm">
                        @csrf
                        <input type="hidden" id="sendPsychiatristTo" name="sendPsychiatristTo" value="${data.username}">
                        
                 

                        <div class="form-group col-sm-10">
                            <h4 for="sendPsychiatrist">Remarks </h4>
                            <textarea class="form-control" id="sendPsychiatrist" name="sendPsychiatrist" placeholder="Enter remarks here..." rows="10" required></textarea>
                            <span id="sendPsychiatrist-error" class="error text-danger"></span>
                        </div>
                    </form>

                    <button type="button" onclick="sendPsychiatristNotification()" class="btn btn-primary">Submit</button>
                    <h2 id="successMessage" class="text-success text-center my-4"></h2>
                     <hr>

                    <div class="list-group mt-4"   style="max-height: 500px; overflow-y: auto;" id="psychiatristsCommentsDiv">
                    </div>
                </div>
            </div>
        </div>`;

        // Append the generated HTML content to the 'dataDiv' element
        showWarningLetter.innerHTML = lettersHtml;
    } else {
        // Optionally display a message when there is no data
        showWarningLetter.innerHTML = '<p class="text-center text-muted">No data available.</p>';
    }
}

function sendPsychiatristNotification() {
    let form = document.getElementById('sendPsychiatristForm');
    let data = new FormData(form);
    data.append('getAllRec', '0'); // Append getAllRec to form data
    fetch(`/reports/send-psychiatrist-student-comment`, {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();
            renderPsychiatristComments(data.getComments);
            console.log('Success:', data.message);
        } else {
            displayValidationErrors(data.errors);
            console.error('Error:', data.errors);
        }
    })
    .catch((error) => {
        displayValidationErrors(error);
        console.error('Error:', error);
    });
}

function renderPsychiatristComments(data) {
    let psychiatristsCommentsDiv = document.getElementById('psychiatristsCommentsDiv');
    psychiatristsCommentsDiv.innerHTML = '';

    if (data && data.length > 0) {
        const commentsHtml = data.map((element) => {
            // Check if emp_ad_username is 'sadia.nawaz' and set dept_name accordingly
            let deptName = element.dept_name;
            if (element.emp_ad_username === 'sadia.nawaz') {
                deptName = 'Department of Counseling';
            }

            return `
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title"><strong>${deptName}</strong></h6>
                            <small class="text-muted">
                                <i class="fa fa-clock-o"></i> ${new Date(element.created_at).toLocaleString()}
                            </small>
                        </div>
                        <h6 class="card-text mt-2">Treatment:</h6>
                        <p>${element.message}</p>
                    </div>
                </div>
            `;
        });

        psychiatristsCommentsDiv.innerHTML = commentsHtml.join('');
    } else {
        psychiatristsCommentsDiv.innerHTML = `
            <div class="alert alert-warning text-center" role="alert">
                No Record Found
            </div>
        `;
    }
}



function getPsychiatristComments(enrollmentNo) {
    const formData = new FormData();
 
    fetch(`/reports/get-psychiatrist-student-comment/${enrollmentNo}`, {
        method: 'GET',

    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        renderPsychiatristComments(data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
function displayValidationErrors(xhr){
    if (xhr.status == 422) {
        var errors = xhr.responseJSON.errors;
        $.each(errors, function(key, value) {
            $("#" + key + "-error").html('<i class="fa fa-info-circle"></i> ' + value[0]);
            $("#" + key + "-error").addClass('text-danger');
            setTimeout(function(){
                $("#" + key + "-error").text('');
            }, 5000);
        });
    }
}
</script>
@stop
