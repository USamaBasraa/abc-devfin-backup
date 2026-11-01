@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')

    <p class="mb-0 pb-0" >
        <a class="btn btn-primary  d-flex align-items-center justify-content-start" style="width: 100%;background:#fff ; color:#424e79 !important; line-height: 1.2;
        text-transform: capitalize;
        font-weight: 600;
        letter-spacing: .05rem;
        font-size: 1.1rem;
        border: none;
        " role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
    		Short Course :
        </a>
    </p>
    <form id="new_record_form">
        <div class="row">
            <div class="col">
            <div class="collapse show" id="multiCollapseExample1">
                <div class="card card-body form-group  m-0 pt-1 pb-2">
                    <div class="form-group row">
                        <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start">Schools </label>
                        <div class="col-md-4">
                            <select name="school" id="school-sel" class="form-control">
                                <option value="">Select School</option>
                                @foreach ($schools as $row)
                                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                @endforeach
                                <option value="22">Office of Special Initiatives</option>
                            </select>
                        </div>
                            <div class=" catgry-box d-none">

                                <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start ">Category </label>
                            </div>
                            <div class="col-md-4  catgry-box d-none">
                                <select name="category" id="sel-catgry" class="form-control">
                                    <option value="">Select Category</option>
                                    <option value="Summer School">Summer School</option>
                                    <option value="Success Lab">Success Lab</option>
                                    <option value="Direct / Face to Face trainings">Direct / Face to Face trainings</option>
                                    <option value="Online Training">Online Training</option>
                                    <option value="Blended Training">Blended Training</option>
                                </select>
                            </div>
                    </div>
                    <div class="form-group row">
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start">Course Title </label>
                        <div class="col-md-4">
                                <input type="text" class="form-control" onkeypress="return /[A-Za-z0-9,:\/() ,.~`!@#$%^&*_=+?<>-]/.test(event.key)" id="course-title" placeholder="Enter title" required>
                        </div>
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Course Fee </label>
                        <div class="col-md-4">
                                <input type="text" class="form-control" onkeypress="return /[0-9]/i.test(event.key)" minlength="3" maxlength="5" id="course-fee" placeholder="Enter Course Fee" required>
                        </div>

                    </div>

                    <div class="form-group row">
                        <label for="course-description" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Course description </label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" onkeypress="return /[A-Za-z0-9,:\/() ,.~`!@#$%^&*_=+?<>-]/.test(event.key)" id="course-description" placeholder="Enter description" required>
                        </div>

                         <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Course Code </label>
                        <div class="col-md-4">
                                <input type="text" class="form-control" minlength="1" maxlength="5" id="course-code" placeholder="Enter Course Code" required>
                        </div>
                    </div>
                    <div class="form-group row ">
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start">Instructor Name </label>
                        <div class="col-md-4">
                                <input type="text" class="form-control" onkeypress="return /[A-Z, ]/i.test(event.key)" id="instructor-name" placeholder="Instructor Name" required>
                        </div>
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Credit Hours </label>
                        <div class="col-md-4">
                                <input type="text" class="form-control" onkeypress="return /[0-9,.]/i.test(event.key)" minlength="1" maxlength="3" id="credit-hours" placeholder="Enter Credit Hours" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Last Date to Apply</label>
                        <div class="col-md-4">
                            <input type="date" name="effective_from" id="due-date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start" >Valid Date</label>
                        <div class="col-md-4">
                            <input type="date" name="effective_from" id="valid-date" class="form-control">
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <div class="row">
                                        <label for="effective_from" class="col-sm-6 col-form-label">Due Date</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="effective_from" id="eff-from" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="row">
                                        <label for="valid_date" class="col-sm-6 col-form-label">Valid Date</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="valid_date" id="valid-date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="form-group row text-center">
                        <div class="col-12">
                        <button type="button" class="btn btn-success"   onclick="SaveRecord()">Submit</button>
                        </div>
                    </div>
                </div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
            </div>
            </div>


        </div>
    </form>

    <div class="row" style="margin-top:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                {{-- <div class="card-header mt-0 pt-0 mb-0 pb-0" style="min-height: 2.5rem">
                    <h3 class="card-title">
                        <button class="btn btn-outline-info btn-sm" onclick="getPDF()">PDF</button>
                    </h3>
                </div> --}}
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0" id="coursesTable">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">School</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Details</th>
                            <th scope="col">Course Fee</th>
                            <th scope="col">Credit Hours</th>
                            <th scope="col">Instructor</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody id="tbody">
                         </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
    @stop
    @section('javascript')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
		LoadShortCoursesList();
        $('#due-date').change(function() {
            console.log($(this).val());
            $('#valid-date').attr('min',$(this).val());
        });
	});
    $(document).on('change', '#school-sel', function() {
        selectedValue = $(this).val();
        if(selectedValue == '22')
            $('.catgry-box').removeClass('d-none');
        else{
            $('#sel-catgry').val('');
            $('.catgry-box').addClass('d-none');
        }
    });
function SaveRecord() {

	let data = {
        mainId: $("#main-Id").val(),
	    school: $("#school-sel").val(),
	    category: $("#sel-catgry").val(),
	    course_fee: $("#course-fee").val(),
	    course_title: $("#course-title").val(),
	    course_code: $("#course-code").val(),
	    credit_hours:  $("#credit-hours").val(),
	    instructor_name: $("#instructor-name").val(),
	    course_description: $("#course-description").val(),
	    due_date: $("#due-date").val(),
	    valid_date: $("#valid-date").val()

	};
    $.ajax({
        type: "POST",
        url: "api/short_courses/store",
        data: data,
        success: function(response) {
            console.log(response);
            $('#main-Id').val('');
            refreshFields();
            $("#error_message").text('').hide();
            $('#success_message').html(response.msg);
            $('#success_message').fadeIn('slow');
            $('#success_message').fadeOut(5000);
            LoadShortCoursesList();

        },
        error: function (res, status, xhr, form) {
                var message = "";
                var data = res.responseJSON.errors;
                $.each(data, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });

                $('#success_message').text('').hide();
                $('#error_message').html(message);
                $('#error_message').fadeIn('slow');
                $('#error_message').fadeOut(5000);
            }
        // error: function(error) {
        //     console.log(error);
        //     // var errorMessage = xhr.responseJSON.message;
        //     // $("#error_message").text(errorMessage).show();
        //     console.error("Error:", error);
        // }

    });
}
function LoadShortCoursesList(){

        $.ajax({
    type: "POST",
    url: "/api/short_courses/get_short_courses_list",
    success: function(response) {
    $("#tbody").empty();

    // Check if response is an array
    if (Array.isArray(response)) {
        console.log(response);
        response.forEach(function(item, index) {
            var schoolName = item.school_name != null ? item.school_name : 'Office of Special Initiatives';//this is because ORIP is not present in Schools table and when we apply left join with school it brings null in school  name
            // var category = item.category != null ? item.categoey : 'N/A';
            $("#tbody").append('<tr>'+
                        '<td>'+(index+1)+'</td>'+
                        '<td>'+item.course_title+'</td>'+
                        '<td>'+schoolName+'</td>'+
                        '<td>'+item.course_code+'</td>'+
                        '<td>'+item.course_description+'</td>'+
                        '<td>'+item.course_fee+'</td>'+
                        '<td>'+item.credit_hours +'</td>'+
                        '<td>'+item.instructor_name+'</td>'+
                        '<td style="display: flex; align-items: center;">'+
                            '<button class="btn btn-primary btn-sm edit-btn" onclick="editRow(`'+item.offer_school_id+'`,`'+item.course_title+'`,`'+item.course_description+'`,`'+item.course_fee+'`,`'+item.course_code+'`,`'+item.credit_hours+'`,`'+item.instructor_name+'`,`'+item.category+'`,`'+item.id+'`,`'+item.due_date+'`,`'+item.valid_date+'`)">Edit</button>' +
                            '<span style="margin-left: 10px; margin-right: 10px;">&nbsp;</span>' +
                            '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRow(`'+item.id+'`)">Delete</button>' +
                        '</td>'+
                    '</tr>');
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
function getPDF(){
url = 'assets/cms/reports/visiting_faculty_list.php';
    window.open(url, '_blank');

}
function editRow(schoolID, title,course_description, fee, code, credHr, intructor, category, mainId, due_date, valid_date){

    console.log('mainId->'+mainId);
    console.log('schoolID->'+schoolID);
    console.log('title->'+title);
    console.log('fee->'+fee);
    console.log('code->'+code);
    console.log('credHr->'+credHr);
    console.log('intructor->'+intructor);
    console.log('category->'+category);
    console.log('category->'+typeof category);
    console.log('dueDate->'+ due_date);
    console.log('validDate->'+ valid_date);
    let dueDate =   dateTime_setting(due_date);
    let validDate =   dateTime_setting(valid_date);

    $('#main-Id').val(mainId);
    $('#school-sel').val(schoolID);
    $('#course-title').val(title);
    $('#course-fee').val(fee);
    $('#course-description').val(course_description);
    $('#course-code').val(code);
    $('#credit-hours').val(credHr);
    $('#instructor-name').val(intructor);
    $('#due-date').val(dueDate);
    $('#valid-date').val(validDate);
    $('#valid-date').attr('min', dueDate);



    if(category != 'null'){
        $('#sel-catgry').val(category);
        $('.catgry-box').removeClass('d-none');
    }else{
        $('.catgry-box').addClass('d-none');
        $('#sel-catgry').val('');
    }
    $('html, body').animate({scrollTop: 0}, 'slow');

}
function deleteRow(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/short_courses/delete_course",
            data: {
                mainId: mainId
            },
            success: function(response) {
                LoadShortCoursesList();

            },
            error: function(error) {
                // Handle the error
                console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields(){
    $('#main-Id').val('');
    $('#school-sel').val('');
    $('#course-title').val('');
    $('#course-fee').val('');
    $('#course-code').val('');
    $('#credit-hours').val('');
    $('#course-description').val('');
    $('#instructor-name').val('');
    $('.catgry-box').addClass('d-none');
    $('#sel-catgry').val('');
    $('#due-date').val('');
    $('#valid-date').val('');
}
function dateTime_setting(date){
    console.log(date);
    if(date != 'null'){
        let timeAdjustedDateString = date.replace(/(\d{2}:\d{2}:\d{2})$/, '11:59:00');
        // console.log('timeAdjustedDateString->'+ timeAdjustedDateString);
        let timeAdjustedDate_date = new Date(timeAdjustedDateString);
        // console.log('timeAdjustedDate_date->'+ timeAdjustedDate_date);
        let formattedDate = timeAdjustedDate_date.toISOString().split('T')[0];
        // console.log('formattedDate->'+ formattedDate);
        return formattedDate;
    }

}
</script>
@stop

