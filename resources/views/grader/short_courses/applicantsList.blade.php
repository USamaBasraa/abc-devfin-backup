@extends('layouts.app')
@section('title') BNU CMS @stop
@section('content')
<style>
.light-green-row {
    background-color: lightgreen;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- DataTables Buttons JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <p class="mb-0 pb-0" >
        <a class="btn btn-primary  d-flex align-items-center justify-content-start" style="width: 100%;background:#fff ; color:#424e79 !important; line-height: 1.2;
        text-transform: capitalize;
        font-weight: 600;
        letter-spacing: .05rem;
        font-size: 1.1rem;
        border: none;
        " role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
    		Applicants
        </a>
    </p>
    <form id="filterForm">
        <div class="row">
            <div class="col">
            <div class="collapse show" id="multiCollapseExample1">
                <div class="card card-body form-group  m-0 pt-1 pb-2">
                    <div class="form-group row">
                        {{-- <label for="exampleInputPassword1" class="col-md-2 form-label mt-2 d-flex flex-row justify-content-start">Schools </label> --}}
                        {{-- <div class="col-md-4">
                            <select name="school" id="school-sel" class="form-control">
                                <option value="">Select School</option>
                                @foreach ($schools as $row)
                                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                @endforeach
                                <option value="22">Office of Special Initiatives</option>
                            </select>
                        </div> --}}
                        <div class="col-md-4">
                            <select name="school" id="course-sel" class="form-control">
                                <option value="">Select Course</option>
                                @foreach ($courses as $row)
                                    <option value="{{ $row->id }}">{{ $row->course_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-success" id="filterApplicants">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel2()">
                                <i class="fa fa-download"></i> Download Excel 
                            </button>
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
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display nowrap">
                        <thead>
                        <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Course Title</th>
                                <th>Discount</th>
                                <th>Discount Status</th>
                                <th>Status</th>
                                <th>Uploaded Image</th>
                                <th>Approval</th>
                                <th>Percentage %</th>
                                <th>Download</th>
                                <th>Paid Mark</th>
                            </tr>
                        </thead>
                      </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="discountModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="discountModalLabel">Discount Modal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Email</th>
                    <th scope="col">Course</th>
                    <th scope="col">Discount</th>
                    </tr>
                </thead>
                <tbody id="tbodyEmail">
                </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    @stop
    @section('javascript')
    <script>
    function downloadExcel2(){     
        let course_id = $('#course-sel').val();
            if (course_id) {
                encodedCourseID = encodeURIComponent(course_id);
            } else {
                encodedCourseID ='';
            }   
        url = 'https://staff.bnu.edu.pk/HOD/short_course_list_excel.php?course_id=' + encodedCourseID;
        window.open(url, '_blank');
    }
    $(document).on('click', '#filterApplicants', function() {
            let school_id = $('#school-sel').val();
            let course_id = $('#course-sel').val();
            LoadApplicantList(school_id, course_id);
        });

        $(document).ready(function() {
            var school_id = '';
            var course_id = '';
            LoadApplicantList(school_id, course_id);
        });

        function LoadApplicantList(school_id, course_id) {
            var authUsername = "{{ e(auth()->user()->username) }}";
            var authGroupId = "{{ e(auth()->user()->group_id) }}";

            $('#data-table').DataTable({
                scrollX: false, // Enable horizontal scrolling
                fixedHeader: true, // Keep headers fixed while scrolling
                scrollCollapse: true,
                responsive: true,
                processing: true,
                serverSide: true,
                select: false,
                searching: true,
                destroy: true,
                lengthChange: true,
                paging: true,
                pageLength: 100,
                order: [1, 'desc'],
                // dom: 'Bfrtip', // Include buttons in the UI
                // buttons: [
                //     {
                //         extend: 'excelHtml5',
                //         text: 'Export to Excel',
                //         className: 'btn btn-success',
                //         exportOptions: {
                //             columns: ':visible' // Export only visible columns
                //         }
                //     }
                // ],
                ajax: {
                    dataType: 'json',
                    method: 'post',
                    url: "/api/short_courses/get_applicants_list",
                    data: function(d) {
                        d.school_id = school_id;
                        d.course_id = course_id;
                    }
                },
                columns: [
                    { data: "aplicantname" },
                    { data: "email" },
                    { data: "mobile" },
                    { data: "course_title" },
                    { data: "discount" },
                    { data: "discount_status" },
                    { data: "challan_field" },
                    { data: "uploadimage" },
                    { data: "discount_status" },
                    { data: "discount_percentage" },
                    { data: "course_id" }, // Assuming the course_id is used for generating the PDF download link
                    // { data: "fee_status" } // Assuming the course_id is used for generating the PDF download link
                ],
                columnDefs: [
                    {
                        render: function (data, type, row) {

                            if (row['challan_field'] != null || row['fee_status'] != null ) {
                                // console.log(row['challan_field']+1);
                                var paidBtn = `<span class="badge badge-success"><i class="fa fa-check-circle" aria-hidden="true"></i> PAID</span>
                                            <button id="paidButton${row['abc']}" type="button"
                                                onclick="btnPaid('${row['abc']}', '${row['id']}')"
                                                class="btn btn-info">
                                                <i class="fa fa-check" aria-hidden="true"></i> Mark Unpaid
                                            </button>
                                            `;


                            } else {
                                // console.log(row['challan_field']+2);
                                    var paidBtn = `
                                    <button id="paidButton${row['abc']}" type="button"
                                            onclick="btnPaid('${row['abc']}', '${row['id']}', '${row['challan_field'] === '' ? 'Unpaid' : 'Paid'}')"
                                            class="btn btn-info">
                                        <i class="fa fa-check" aria-hidden="true"></i> Mark Paid
                                    </button>
                                    `;

                            }
                            return paidBtn;
                        },
                        targets: 6,
                    },
                    {
                        render: function (data, type, row) {
                            if (row['uploadimage'] == null) {
                                return 'Not Uploaded';
                            } else {
                                return '<a href="/images/' + row['uploadimage'] + '" download>Download Image</a>';
                            }
                        },
                        targets: 7,
                    },
                    {
                        render: function (data, type, row) {
                            var buttonClass = row['discount_status'] == 1 ? 'btn-success' : 'btn-primary';
                            var buttonText = row['discount_status'] == 1 ? 'Approved' : 'Approval';
                            var approveBtn = '<button id="approvalButton' + row['abc'] + '" type="button" onclick="approval(`' + row['abc'] + '`,`' + row['id'] + '`)" class="btn ' + buttonClass + '">' + buttonText + '</button>';
                            return approveBtn;
                        },
                        targets: 8,
                    },
                    {
                        render: function (data, type, row) {
                        let viewButton = '';
                        if (row['discount'] === 'Three People in One Program' || row['discount'] === 'Five People in One Program') {
                            viewButton = `
                                <button class="btn btn-success ml-3 viewDiscountBtn" data-toggle="modal" data-abc="${row['id']}" data-target="#discountModal">
                                    <i class="fa fa-users" aria-hidden="true"></i> View Group
                                </button>`;
                        } else {
                            viewButton = `
                                <button class="btn btn-success ml-3">
                                    <i class="fa fa-user" aria-hidden="true"></i> Single Applicant
                                </button>`;
                        }

                        const delButton = (authUsername === 'aizaz.hussain' || authGroupId === 644) ? `
                            <button type="button" class="btn btn-danger ml-3" onclick="deleteItem(${row['id']},${row['course_id']})">
                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                            </button>` : '';

                        const dis_percent = row['discount_percentage'] !== null ? row['discount_percentage'] : '';
                        const discountPercent = `
                            <div class="btn-group" role="group">
                                <input type="text" value="${dis_percent}" id="discountFee${row['abc']}" data-id="${row['abc']}" class="form-control inp-abc" style="width: 100px;">
                                ${viewButton}
                                ${delButton}
                            </div>`;

                        return discountPercent;
                    },

                        targets: 9,
                    },
                    {
                        render: function (data, type, row) {
                            return `
                            <a href="/assets/cms/reports/short_course_report.php?row_email=${row['email']}&row_courseId=${row['course_id']}" download>
                            <button style="margin-left: 17px; border-radius: 9px;" class="btn btn-success">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download PDF
                            </button>
                            </a>
                            `;
                        },
                        targets: 10,
                    }
                ],
            });
        }



$(document).on('blur', '.inp-abc', function() {
    var value = $(this).val();
    var itemId = $(this).data("id"); // Retrieve the value of the data-id attribute
        console.log('Input value:', value+itemId);
    $.ajax({
        type: "POST",
        url: "/api/short_courses/updateDiscount?discountfee="+value+"&id="+itemId,
        success: function(response) {

        },
        error: function(error) {
        // Handle the error
        console.error("Error:", error);
        }
    });
});
$(document).on('click', '.viewDiscountBtn', function() {
    var applicantid = $(this).data('abc');
    $.ajax({
        type: "POST",
        url: "/api/short_courses/getEmail?getEmail="+applicantid,
        success: function(response) {
            $("#tbodyEmail").empty();
            if (Array.isArray(response)) {
                console.log(response);
                response.forEach(function(item, index) {
                    var buttonClass = item.discount_status == 1 ? 'btn-success' : 'btn-primary';
                    var buttonText = item.discount_status == 1 ? 'Approved' : 'Approval';
                    if (item.discount_status === 0) {
                        buttonText = 'Approval';
                        buttonClass = 'btn-primary';
                    }

                    var html = `
                        <tr class="${item.chkprofile > 0 ? 'light-green-row' : ''}">
                            <td>${index + 1}</td>
                            <td>${item.applicant_email}</td>
                            <td>${item.course_title}</td>
                            <td>${item.chkprofile > 0 ? `<input type="text" value="${item.discount_percentage !== null ? item.discount_percentage : ''}" id="indDiscount" onblur="updateIndDis(${item.id})" class="form-control" style="width: 70px;">` : ''}</td>
                            <td>${item.chkprofile > 0 ? `<button id="approvalButtonind${item.id}" type="button" onclick="indapproval(${item.id})" class="btn ${buttonClass}">${buttonText}</button>` : ''}</td>
                        </tr>`;

                    $("#tbodyEmail").append(html);
                });
            } else {
                console.error("Error: Response is not an array");
            }
        },
        error: function(error) {
            console.error("Error:", error);
        }
    });
});

function updateIndDis(id){
    var indDiscount = $('#indDiscount').val();
        $.ajax({
            type: "POST",
            url: "/api/short_courses/updateIndDiscount?id="+id+"&discount="+indDiscount,
            success: function(response) {

            console.log(response);


            },
            error: function(error) {
            // Handle the error
            console.error("Error:", error);
            }
    });
}
function indapproval(id){

        $.ajax({
            type: "POST",
            url: "/api/short_courses/updateIndapproval?id="+id,
            success: function(response) {

            console.log(response);
            if(response == 1){
                $('#approvalButtonind'+id).removeClass('btn-primary').addClass('btn-success').text('Approved');

            }else{
                $('#approvalButtonind'+id).removeClass('btn-success').addClass('btn-primary').text('Approval');

            }
			 setTimeout(function() {
                window.location.reload();
            }, 2000);

            },
            error: function(error) {
            // Handle the error
            console.error("Error:", error);
            }
    });
}

function approval(id, applicant_id,btntype) {
    $.ajax({
        type: "POST",
        url: "/api/short_courses/approval",
        data: {
            id: id,
            applicant_id: applicant_id,
            btntype: btntype,
        },
        success: function(response) {
            console.log(response);
            if (response == 1) {
                $('#approvalButton' + id).removeClass('btn-primary').addClass('btn-success').text('Approved');
            } else {
                $('#approvalButton' + id).removeClass('btn-success').addClass('btn-primary').text('Approval');
            }
			 setTimeout(function() {
                window.location.reload();
            }, 2000);
        },
        error: function(xhr, status, error) {
            // Handle the error
            console.error("Error:", error);
        }
    });
}
function btnPaid(id, applicant_id,btntype) {
    $.ajax({
        type: "POST",
        url: "/api/short_courses/paidMark",
        data: {
            id: id,
            applicant_id: applicant_id,
            btntype: btntype,
        },
        success: function(response) {
            console.log(response);
            if (response == 1) {
                $('#approvalButton' + id).removeClass('btn-primary').addClass('btn-success').text('Approved');
            } else {
                $('#approvalButton' + id).removeClass('btn-success').addClass('btn-primary').text('Approval');
            } 
              setTimeout(function() {
                window.location.reload();
            }, 2000);
        },
        error: function(xhr, status, error) {
            // Handle the error
            console.error("Error:", error);
        }
    });
}


//   function getCourses(){
// 	    if($("#program").val() != "null"){
// 		    	var data = {
// 		    		prog_id : $("#program").val()
// 		    	};

// 		    $.ajax({
// 	    type: "POST",
// 	    url: "/api/visiting_faculty/get_courses",
// 	    data: data,
// 	    success: function(response) {
// 		$("#ige_course_id").empty();

// 		// Check if response is an array
// 		if (Array.isArray(response)) {
// 		$("#ige_course_id").append("<option value='null' selected>Select Course</option>");
// 		    response.forEach(function(item, index) {
//                 if(item.course_id != null){
//                     $("#ige_course_id").append("<option value='" + item.course_id + "'>" + item.course_name + "</option>");
//                 }
// 		    });
// 		} else {
// 		    console.error("Error: Response is not an array");
// 		}

// 		// Handle the successful response from the server
// 		console.log(response);
// 	    },
// 	    error: function(error) {
// 		// Handle the error
// 		console.error("Error:", error);
// 	    }
// });

//     	}
//     }
//   function getProgram(){
// 	    if($("#department").val() != "null"){
// 		    	var data = {
// 		    		dept_id : $("#department").val()
// 		    	};

// 		    $.ajax({
// 	    type: "POST",
// 	    url: "/api/visiting_faculty/get_programs",
// 	    data: data,
// 	    success: function(response) {
// 		$("#program").empty();

// 		// Check if response is an array
// 		if (Array.isArray(response)) {
// 		$("#program").append("<option value='null' selected>Select Program</option>");
// 		    response.forEach(function(item, index) {
// 		        $("#program").append("<option value='" + item.prog_id + "'>" + item.prog_name + "</option>");
// 		    });
// 		} else {
// 		    console.error("Error: Response is not an array");
// 		}

// 		// Handle the successful response from the server
// 		console.log(response);
// 	    },
// 	    error: function(error) {
// 		// Handle the error
// 		console.error("Error:", error);
// 	    }
// });

//     	}
//     }
// function getStudentCourses(){


//     var data = {
//         show_all_courses : 1,
//         sel_sess_id: $('#session').val(),

//     };
//     fetch('/api/exam/getSessionCourses', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//     },
//         body: JSON.stringify(data)
//     })
//     .then(response => response.json())
//     .then(dat => {
//         // console.log(dat);
//         $('#ige_course_id').empty();
//         if (dat.length === 0) {
//             // If no records found, add a disabled option

//             $('#ige_course_id').append('<option value="default" disabled selected>No records found</option>');
//         } else {
//             // If there are records, add options to the select tag
//             $('#ige_course_id').empty();
//             $('#ige_course_id').append('<option value="default">Select Course</option>');

//             // Loop through the data and add options
//             dat.forEach(item => {
//                 $('#ige_course_id').append('<option value="' + item.co_id + '">' + item.course_code + ' | ' + item.course_cd_name +  ' | ' + item.co_course_cr  + ' | ' +item.co_section + '</option>');
//             });
//         }
//     })
//     .catch(error => {
//         // Handle errors here
//         // console.error('Error:', error);
//     });
// }

// function getStudentCoursesDetails(){

//     var data = {
//         sel_co_id : $("#ige_course_id").val(),
//         sel_sess_id: $('#session').val(),

//     };
//     fetch('/api/exam/get-course-details', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json'
//     },
//         body: JSON.stringify(data)
//     })
//     .then(response => response.json())
//     .then(dat => {
//         // console.log(dat);
//         if (dat.length === 0) {
//             // If no records found, add a disabled option
//             $('#tbody').empty();

//             $('#ige_course_id').append('<option value="default" disabled selected>No records found</option>');
//         } else {
//             // If there are records, add options to the select tag
//             $('#tbody').empty();


//             // Loop through the data and add options
//             dat.forEach((item,index) => {
//                 html = '<tr><td>'+ (parseInt(index)+ parseInt(1)) + '</td><td>'+ item.sc_sai_enrollment_no+'</td><td>'+ item.spi_name+'</td><td>'+ item.sc_pc_course_cr+'</td><td>'+ item.gh_grade+'</td><td>';

//                 if( item.gh_teacher_fsubmit == 1){
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";
//                 }else{
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";

//                 }

//                 if( item.gh_hod_fsubmit == 1){
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";

//                 }else{
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";

//                 }

//                 if( item.gh_dean_fsubmit == 1){
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok.png'></td><td>";

//                 }else{
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";
//                 }

//                 if( item.gh_exam_fsubmit == 1){
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/ok_disabled.png'></td><td>";
//                 }else{
//                     html += "<img src='http://stafftest.bnu.edu.pk/lib/jquery-easyui-1.4/themes/icons/no.png'></td><td>";
//                 }
//                 html += item.std_fee_details+'</td><td>';
//                 console.log(item.sai_pf_remarks);
//                 doc_status_details = "";
//                 if((item.alevel_doc_status_details>0) || (item.olevel_doc_status_details>0)){
//                     doc_status_details = " (";
//                     if(item.alevel_doc_status_details>0){
//                         doc_status_details = doc_status_details + item.alevel_doc_status_details  + " A , ";

//                     }
//                     if(item.olevel_doc_status_details>0){
//                         doc_status_details = doc_status_details + item.olevel_doc_status_details  + " O , ";
//                     }
//                     doc_status_details = doc_status_details + " Levels Result Missing ) ";
//                 }
//                 // final_document_status 	= item.sai_pf_remarks + " " + doc_status_main + " " +doc_status_details;
//                 // final_document_status 	= item.sai_pf_remarks + " " +doc_status_details;
//                         final_document_status 	= doc_status_details;

//                 if((doc_status_details=='') ){
//                         final_doc_status ="OK";
//                 }else{
//                         final_doc_status =final_document_status;
//                 }

//                 html +=  final_doc_status;
//                 html +='</td><td></tr>';
//                 $('#tbody').append(html);
//             });
//         }
//     })
//     .catch(error => {
//         // Handle errors here
//         // console.error('Error:', error);
//     });
// }


function getPDF(){
url = 'assets/cms/reports/visiting_faculty_list.php';
    window.open(url, '_blank');

}


function deleteItem(id,courseID) {
    // Show spinner for the specific delete button
    $(`#delButton${id}`).html('<div class="spinner-border spinner-border-sm" role="status"></div>');

    // Disable the delete button temporarily
    $(`#delButton${id}`).prop('disabled', true);

    // AJAX request to delete record
    $.ajax({
        type: "POST",
        url: "/api/short_courses/delete_applicant",
        data: {
            applicant_id: id,
            course_id: courseID
        },
        success: function(response) {
            // Enable the delete button
            $(`#delButton${id}`).prop('disabled', false);
            // Restore the trash icon for the delete button
            $(`#delButton${id}`).html('<i class="fa fa-trash"></i>');

            // Reload page or update UI as needed
            location.reload();
        },
        error: function(error) {
            // Enable the delete button
            $(`#delButton${id}`).prop('disabled', false);
            // Restore the trash icon for the delete button
            $(`#delButton${id}`).html('<i class="fa fa-trash"></i>');

            // Alert error
            alert("Error: " + error.responseText);
        }
    });
}


</script>
@stop

