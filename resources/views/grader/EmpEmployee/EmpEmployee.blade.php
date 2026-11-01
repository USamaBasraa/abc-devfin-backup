@extends('layouts.app')
@section('content')
<style>
    .invisible {
        display: none;
    }

    .dropbtn {
        background-color: #04AA6D;
        color: white;
        padding: 10px;
        font-size: 16px;
        border: none;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #ddd;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: #3e8e41;
    }

    ul {
        margin: 20px;
    }

    .input-color {
        position: relative;
    }

    .input-color input {
        padding-left: 25px;
        border: 0px solid;
    }

    .input-color .color-box {
        width: 15px;
        height: 15px;
        display: inline-block;
        background-color: #ccc;
        position: absolute;
        left: 5px;
        top: 5px;
    }

    .challanDIV {
        height: 350px;
        display: inline-block;
        width: 100%;
        overflow: auto;
    }

    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            width: 450px;
            height: 40px;
        }
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<?php
$prefix = ['Dr.', 'Ms.', 'Mrs.', 'Miss.', 'Mr'];
$gender = ['Male', 'Female'];
$employeeTypes = '[
                    {
                        "emptype_id":1,
                        "emptype_name":"Staff"
                    },{
                        "emptype_id":2,
                        "emptype_name":"Faculty"
                    },{
                        "emptype_id":3,
                        "emptype_name":"Visiting Faculty"
                    },{
                        "emptype_id":4,
                        "emptype_name":"Dean"
                    },{
                        "emptype_id":5,
                        "emptype_name":"HOD"
                    },{
                        "emptype_id":6,
                        "emptype_name":"TA"
                    }
                ]';
$employeeTypes = json_decode($employeeTypes);
?>
<div class="card mr-4 ml-4">
    <div class="card-header" style="display:inline">
        <form method="post" enctype="multipart/form-data" id="EmpEmployeeForm">
            @csrf
            <div class="row">

                <h3 class="col-sm-8">Employee Management</h3>
                <div class="col-sm-4">
                    {{-- Add your buttons here --}}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-3">
                    <input name="employee_id" type="hidden" id="employee_id" value="" />
                    <label for="prefix">Prefix</label>
                    <select class="form-control" name="prefix" id="prefix">
                        <option value="">--Please Select Prefix--</option>
                        <option value="Dr.">Dr</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Miss.">Miss.</option>
                        <option value="Mr.">Mr.</option>
                    </select>
                    <span id="prefix-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="empFname">First Name</label>
                    <input type="text" name="emp_f_name" id="emp_f_name" class="form-control" />
                    <span id="emp_f_name-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="empLname">Last Name</label>
                    <input type="text" name="emp_l_name" id="emp_l_name" class="form-control" />
                    <span id="emp_l_name-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="school">School</label>
                    <select class="form-control" name="school" id="school">
                        <option value="">--Please Select School--</option>
                        @foreach ($school_data as $employee)
                            <option value="{{ $employee->school_id }}">{{ $employee->school_code }}</option>
                        @endforeach
                    </select>
                    <span id="school-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="department">Department</label>
                    <select class="form-control" name="department" id="department"></select>
                    <span id="department-error" class="error text-danger"></span>
                </div>
                @php
                    $showOther = false;
                    foreach ($obj_groups as $group) {
                        if ($group->group_id == 644) {
                            $showOther = true;
                            break;
                        }
                    }
                @endphp
                <div class="form-group col-sm-3">
                    <label for="designation">Designation</label>
                    <select class="form-control" name="designation" id="designation">
                        <option value="">--Please Select Designation--</option>
                        @foreach ($designation as $employee)
                            <option value="{{ $employee->designation }}">{{ $employee->designation }}</option>
                        @endforeach
                        @if ($showOther)
                            <option value="Other">Other</option>
                        @endif
                    </select>
                    <input type="text" id="other_designation" name="other_designation" class="form-control mt-2"
                        placeholder="Enter other designation" style="display:none;" />
                    <span id="designation-error" class="error text-danger"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="gender">Gender</label>
                    <select class="form-control" name="emp_gender" id="emp_gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>

                    </select>
                    <span id="emp_gender-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3" id="emp_contact_no_div">
                    <label for="emp_contact_no">Mobile Number</label>
                    <input type="tel" name="emp_contact_no" id="emp_contact_no" maxlength="12" class="form-control" />
                    <span id="emp_contact_no-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3">
                    <label for="emp_cnic">CNIC</label>
                    <input type="text" pattern="\d*" name="emp_cnic" id="emp_cnic" maxlength="13"
                        class="form-control" />
                    <span id="emp_cnic-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3" id="emp_email_div">
                    <label for="emp_email">Email</label>
                    <input type="email" name="emp_email" id="emp_email" class="form-control" />
                    <span id="emp_email-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3" id="emp_username_div">
                    <label for="emp_username">Username</label>
                    <input type="text" name="emp_username" id="emp_username" class="form-control" />
                    <input type="hidden" name="db_username" id="db_username" class="form-control" />
                    <span id="emp_username-error" class="error"></span>
                </div>
                <div class="form-group col-sm-3" style="display: none; position: relative;" id="div_hod">
                    <label for="hod">HOD Name</label>
                    <input type="text" name="hod" id="hod" class="form-control"
                        onkeyup="searchEmployee(this.value, 'hod')" autocomplete="off" />
                    <div id="hod_list" class="dropdown-content" style="width: 100%;"></div>
                </div>
                <div class="form-group col-sm-3" style="display: none; position: relative;" id="div_dean">
                    <label for="dean">Dean Name</label>
                    <input type="text" name="dean" id="dean" class="form-control"
                        onkeyup="searchEmployee(this.value, 'dean')" autocomplete="off" />
                    <div id="dean_list" class="dropdown-content" style="width: 100%;"></div>
                </div>
                @php
                    // dd(auth()->user()->group_id);
                    // $groupId = auth()->user()->group_id;
                    // $allowedGroupsIds = [644, 2, 181];
                    // if (in_array($groupId, $allowedGroupsIds)) {
                    echo '<div class="form-group col-sm-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label for="empType">Type</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <select class="form-control" name="empType" id="empType">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="">--Please Select Type--</option>';
                    foreach ($employeeTypes as $employee) {
                        echo '<option value="' . $employee->emptype_id . '">' . $employee->emptype_name . '</option>';
                    }
                    echo '</select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <span id="empType-error" class="error text-danger"></span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     </div>
                <div class="form-group col-sm-3">
    <label for="emp_image">Upload Image</label>
    <input type="file" 
           name="emp_image" 
           id="emp_image" 
           class="form-control" 
           accept="image/*" 
           onchange="validateAndPreview(this)" />

    <!-- Description -->
    <small class="text-muted d-block mt-1">
        Image size must not exceed 2MB. Allowed formats: JPG, JPEG, PNG.
    </small>

    <span id="emp_image-error" class="error text-danger"></span>

    <div id="image_preview_div" class="mt-2" style="display:none;">
        <img id="edit_image_preview" 
             src="" 
             style="width: 80px; height: 80px; border-radius: 5px; object-fit: cover; border: 1px solid #ddd;">
    </div>
</div>
                <div class="form-group col-sm-3">
                    <label for="hris_id">HRIS ID</label>
                    <input type="text" name="hris_id" id="hris_id" class="form-control" placeholder="Enter HRIS ID" />
                    <span id="hris_id-error" class="error text-danger"></span>
                </div>';
                    





                @endphp
                <div class="form-group col-sm-12">
                    <div style="float:left">
                        <button type="button" name="submitEmpEmployee" id="submitEmpEmployee" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Employee
                        </button>
                    </div>
                    <!-- <div style="float:center">
                        <button type="button" name="submitEmpEmployee" id="submitEmpEmployee" class="btn btn-success">
                            <i class="fa fa"></i> Print Card
                        </button>
                    </div> -->
                    <div style="float:right">
                        <button type="button" onclick="createTa()" class="btn btn-success">
                            <i class="fa fa-plus"></i> Create TA
                        </button>
                    </div>
                    <div id="success_msg" class="alert alert-success invisible" role="alert">

                    </div>
                </div>

            </div>
        </form>
    </div>

    <div class="card mr-4">
        <div class="card-header" style="display:inline">
            <!-- Your form code here -->
        </div>
        <div class="card-body">

            <div class="dt-responsive ">
                <table id="simpletable" class="table table-striped table-bordered dt-responsive">
                    <thead>
                        <tr>
                            <th class="tab-title">Employee Id</th>
                            <th class="tab-title">Photo</th>
                            <th class="tab-title">Prefix</th>
                            <th class="tab-title">Name</th>
                            <th class="tab-title">Contact / CNIC</th>
                            <th class="tab-title">Username / Email</th>
                            <th class="tab-title">Gender</th>
                            <th class="tab-title">School / Department</th>
                            <th class="tab-title">Type</th>
                            <th class="tab-title">HOD</th>
                            <th class="tab-title">Dean</th>
                            @php
                                // $groupId = auth()->user()->group_id;
                                // $allowedGroupsIds = [644, 2, 181,284];
                                // if (in_array($groupId, $allowedGroupsIds)) {


                                // if ($userId == 644 || $userId == 2) {
                                echo '<th class="tab-title">Action</th>'
                                // }
                            @endphp
                        </tr>
                    </thead>
                    <tbody>


                        @foreach ($SelectFaculty as $employee)
                            <tr>
                                <td>
                                    @if(isset($employee->employee_id) && $employee->employee_id)
                                        {{ $employee->employee_id }}     
                                    @endif  
                                </td>
                                <td>
                                    @if(isset($employee->emp_imagefile) && $employee->emp_imagefile)
                                        <img src="{{ url('staffimages/' . $employee->emp_imagefile) }}" onerror="this.src='{{ url('staffimages/' . ($employee->employee_id . '.jpg')) }}';this.onerror=null;" alt="User" style="width: 50px; height: 50px; border-radius: 5px; object-fit: cover;">
                                    @else
                                        <div style="width: 50px; height: 50px; border-radius: 5px; background: #eee; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $employee->prefix }}</td>
                                <td>
                                    {{ $employee->emp_f_name }} {{ $employee->emp_l_name }}
                                    <i style="font-size: 20px;"
                                        class="fa {{ $employee->emp_status === "D" ? "fa-ban text-white bg-danger p-1 rounded" : "" }}"></i>
                                    <br>
                                    <span class="text-info">

                                        {{ $employee->designation }}

                                    </span>
                                </td>


                                <td>
                                    {{ $employee->emp_contact_no }}
                                    <br>
                                    {{ $employee->emp_nic }}
                                </td>
                                <td>

                                    {{ $employee->emp_ad_username }}
                                    <br>
                                    <span class="text-info">

                                        {{ $employee->emp_email }}

                                    </span>


                                </td>
                                <td>{{ $employee->emp_gender }}

                                </td>
                                <td>
                                    {{ $employee->school_code }}
                                    <br>
                                    {{ $employee->dept_name }}
                                </td>
                                <td>
                                    @php
                                        $empTypeId = $employee->emp_type;
                                        $empTypeName = '';
                                        foreach ($employeeTypes as $type) {
                                            if ($type->emptype_id == $empTypeId) {
                                                $empTypeName = $type->emptype_name;
                                                break;
                                            }
                                        }
                                        echo $empTypeName;
                                    @endphp
                                </td>
                                <td>{{ $employee->hod ?? '-' }}</td>
                                <td>{{ $employee->dean ?? '-' }}</td>

                                @php


                                    // if (in_array($groupId, $allowedGroupsIds)) {
                                    echo '<td>
                                        <button class="btn btn-primary editEmpBtn" onclick="editEmployee(' . $employee->employee_id . ')">
                                            <i class="fa fa-edit p-2"></i> Edit
                                        </button>
                                        <a href="' . url('emp-employee-card/' . $employee->employee_id) . '" target="_blank" class="btn btn-info mt-1">
                                            <i class="fa fa-print p-2"></i> Print Card
                                        </a>
                                    </td>';




                                    // }
                                @endphp

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>




    </div>


    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                    <span style="text-color:white" id="header_modal"></span>
                    <p class="h5 modal-title" id="ModalTitle">Create Teacher Assisstant</p>
                    <button type="button" style="color:white" class="close" onclick="closeBtn()" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" style="text-align: left;">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <label for="prefix"><b>TA Name:</b></label>
                            <div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-10 col-sm-10 col-md-10 col-lg-10">
                                            <input class="form-control" type="text" id="txtStudentid"
                                                name="txtStudentid">
                                        </div>
                                        <div class="col-2 col-sm-2 col-md-2 col-lg-2">
                                            <button type="button" class="btn btn-success" onclick="getProfile()"><i
                                                    class="fa fa-search" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <span id="student_name"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <span id="school_name"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <span id="dept_name"></span>
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" onclick="create_TA()">Create TA</button>
                            <span id="successMsg" style="color:green"></span>
                            <span id="errorMsg" style="color:red"></span>
                        </div>
                    </div>
                </div>
            </div>


            @stop
            @section('javascript')
            <script type="text/javascript">
                document.getElementById("txtStudentid").addEventListener("keyup", function (event) {
                    if (event.key === "Enter") {
                        getProfile();
                    }
                });
                function closeBtn() {
                    $('#successMsg').html('');
                    $('#errorMsg').html('');
                    $('#student_name').html('');
                    $('#school_name').html('');
                    $('#dept_name').html('');
                    $("#myModal").modal("hide");
                    $('#txtStudentid').val('');
                }
                function getProfile() {
                    var rol_no = $('#txtStudentid').val().toUpperCase();
                    // if you want to show alert msg at the top write this (.message_div) as second parem in sendGetRequest
                    $.ajax({
                        url: '{{route("get-student-profile")}}/' + rol_no,
                        type: 'GET',
                        success: function (res) {
                            // Set a timeout to close the loader after two seconds
                            if (res.message == 1) {
                                $('#sch_type').removeAttr("disabled");
                                $('#sch_percentage').removeAttr("disabled");
                                $('#sch_details').removeAttr("disabled");
                                var tbody = '';
                                $(res.data).each(function (index, row) {
                                    $('#student_name').html('<b>Name: </b>' + row.spi_name);
                                    $('#school_name').html('<b>School: </b>' + row.school_name);
                                    $('#dept_name').html('<b>Department: </b>' + row.dept_name);
                                });
                            } else {
                                $('#student_name').html('<b>Kindly Enter Valid Enrollment No');
                                $('#school_name').html('');
                                $('#dept_name').html('');
                                $('#sch_type').attr('disabled', true);
                                $('#sch_percentage').attr('disabled', true);
                                $('#sch_details').attr('disabled', true);
                            }

                        },
                        error: function (xhr, status, error) {
                            // Request failed, handle the error
                            console.log("Error: " + error);
                        }
                    });
                }
                function setPrefix(selectedValue) {
                    // Your logic here based on the selected value
                    // alert("Selected Prefix: " + selectedValue);
                    if (selectedValue == 'Ms.' || selectedValue == 'Mrs.' || selectedValue == 'Miss.') {
                        $('#emp_gender').val('Female');
                    } else {
                        $('#emp_gender').val('Male');
                    }
                }
                function create_TA() {
                    var student_id = $('#txtStudentid').val();
                    // alert(student_id);return false;
                    $.ajax({
                        type: 'POST',
                        url: 'api/createTA',
                        data: {
                            studentId: student_id
                        },
                        success: function (data) {
                            //alert(data);
                            // $.messager.alert('Info','Teacher Assistant created successfully', 'info');
                            // refreshGridFaculty();
                            // return false;
                            //$('#show_rec').html(data);
                            $('#errorMsg').html('');
                            $('#successMsg').html('TA Enters Successfully');

                            setTimeout(function () {
                                $('#successMsg').html('');
                                $('#student_name').html('');
                                $('#school_name').html('');
                                $('#dept_name').html('');
                                $("#myModal").modal("hide");
                                $('#txtStudentid').val('');
                                // location.reload();
                            }, 2000);
                        },
                        error: function (xhr, status, error) {
                            // Error handling logic here
                            if (error) {
                                $('#errorMsg').html('TA already Exists');
                                setTimeout(function () {
                                    $('#errorMsg').html('');
                                    $('#student_name').html('');
                                    $('#school_name').html('');
                                    $('#dept_name').html('');
                                    $("#myModal").modal("hide");
                                    $('#txtStudentid').val('');
                                    //location.reload();
                                }, 2000);
                            }

                        }
                    });
                }

                function createTa() {
                    $("#myModal").modal("show");
                }
                $(document).ready(function () {
                    $('#simpletable').DataTable();
                    // getDesignation();

                    $('#designation').on('change', function () {
                        if ($(this).val() === 'Other') {
                            $('#other_designation').show().attr('required', true);
                        } else {
                            $('#other_designation').hide().val('').attr('required', false);
                        }
                    });
                    $('#school').on('change', function () {
                        // alert('kkkk')
                        var schoolId = $(this).val();
                        $('#department').empty();
                        $.ajax({
                            url: '/get-dept',
                            method: 'GET',
                            data: {
                                school_id: schoolId
                            },
                            success: function (departments) {
                                $.each(departments, function (index, department) {
                                    var option = $('<option></option>').val(department.dept_id).text(department.dept_name);
                                    $('#department').append(option);
                                });

                            },
                            error: function (xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    });




                    $("#submitEmpEmployee").click(function (e) {
                        e.preventDefault();
                        let form = document.getElementById("EmpEmployeeForm");
                        let data = new FormData(form);
                        if ($('#designation').val() === 'Other') {
                            const customDesignation = $('#other_designation').val();
                            data.set('designation', customDesignation);
                        }
                        var button = $(this);
                        button.prop('disabled', true);
                        var spinnerIcon = '<i class="fa fa-spinner fa-spin"></i>';
                        button.html(spinnerIcon + ' Updating');
                        $.ajax({
                            type: 'POST',
                            url: 'api/add-emp-employee',
                            data: data,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response.status) {
                                    setTimeout(function () {
                                        button.html('<i class="fa fa-check"></i> Updated');
                                        $('#success_msg').text(response.message);
                                        $('#success_msg').removeClass('invisible');

                                        setTimeout(function () {
                                            $('#success_msg').addClass('invisible');
                                            button.html('<i class="fa fa-plus"></i> Add Employee');
                                            button.prop('disabled', false); // Re-enable the button
                                        }, 2000);
                                        document.getElementById("EmpEmployeeForm").reset();
                                        $('#div_dean').hide();
                                        $('#div_hod').hide();
                                        $('#image_preview_div').hide();
                                        $('#edit_image_preview').attr('src', '');
                                        window.location.reload();
                                    }, 3000);

                                    // $('#simpletable').DataTable().clear().rows.add(response.SelectFaculty).draw();
                                    // location.reload();
                                } else {
                                    let emp_email = response.emp_email
                                    let emp_username = response.emp_username

                                    if (emp_email) {
                                        $('#emp_email-error').addClass('text-danger').text(emp_email);
                                    }
                                    if (emp_username) {
                                        $('#emp_username-error').addClass('text-danger').text(emp_username);
                                    }

                                    // displayValidationErrors(response.errors);
                                }

                                if (!response.status) {
                                    button.html('<i class="fa fa-plus"></i> Add Employee');
                                    button.prop('disabled', false); // Re-enable the button

                                }
                                setTimeout(function () {
                                    $('#emp_email-error').text('');
                                    $('#emp_username-error').text('');
                                }, 3000);
                            },
                            error: function (xhr) {
                                button.html('<i class="fa fa-plus"></i> Add Employee');
                                button.prop('disabled', false); // Re-enable the button
                                displayValidationErrors(xhr);
                            }

                        });

                    });
                });
                function displayValidationErrors(xhr) {
                    if (xhr.status == 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $("#" + key + "-error").text(value[0]);
                            $("#" + key + "-error").addClass('text-danger');
                            setTimeout(function () {
                                $("#" + key + "-error").text('');
                            }, 5000);
                        });
                    }
                }
                function updateEmpStatus(employee_id, emp_status) {
                    // alert(employee_id+"----"+emp_status);
                    var data = {
                        'employee_id': employee_id,
                        'emp_status': emp_status
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'api/update-status-employee',
                        data: data,
                        success: function (response) {

                            if (response.status)
                                window.location.reload();


                        },
                        error: function (xhr) {
                            // Handle error here
                        }
                    });

                }
                function editEmployee(employee_id) {
                    window.scrollTo(0, 0);
                    var data = {
                        'employee_id': employee_id
                    };
                    $.ajax({
                        type: 'POST',
                        url: 'api/get-emp-employee',
                        data: data,
                        success: function (response) {
                            const res = response[0];
                            $('#employee_id').val(res.employee_id);
                            $('#prefix').val(res.prefix);
                            $('#emp_f_name').val(res.emp_f_name);
                            $('#emp_l_name').val(res.emp_l_name);
                            $('#emp_contact_no').val(res.emp_contact_no);
                            $('#emp_gender').val(res.emp_gender);
                            $('#emp_email').val(res.emp_email);
                            $('#emp_cnic').val(res.emp_nic);
                            $('#emp_username').val(res.emp_ad_username);
                            // For Emp username from database
                            $('#db_username').val(res.emp_ad_username);
                            $('#empType').val(res.emp_type);
                            $('#school').val(res.emp_school_id);
                            $('#designation').val(res.designation);
                            $('#dean').val(res.dean);
                            $('#hod').val(res.hod);
                            $('#hris_id').val(res.hr_beams_id);
                            $('#div_dean').show();
                            $('#div_hod').show();

                            let imgFile = res.emp_imagefile || res.EMP_IMAGEFILE;
                            if (imgFile) {
                                let imgSrc = '{{ url("staffimages") }}/' + imgFile;
                                // Add a temporary image to check if it exists in staffimages
                                let tempImg = new Image();
                                tempImg.onload = function() {
                                    $('#edit_image_preview').attr('src', imgSrc);
                                };
                                tempImg.onerror = function() {
                                    $('#edit_image_preview').attr('src', '{{ url("staffimages") }}/' + res.employee_id + '.jpg');
                                };
                                tempImg.src = imgSrc;
                                $('#image_preview_div').show();
                            } else {
                                $('#image_preview_div').hide();
                                $('#edit_image_preview').attr('src', '');
                            }
                            // alert(res.emp_dept_id);

                            getDept(res.emp_school_id, res.emp_dept_id);
                            // $('#department').val(res.dept_id);

                            // $('#emp_contact_no_div').hide();
                            // $('#emp_email_div').hide();
                            // $('#emp_username_div').hide();

                        },
                        error: function (xhr) {
                            // Handle error here
                        }
                    });
                    document.getElementById('submitEmpEmployee').innerHTML = '<i class="fa fa-edit"></i> Update Employee';
                }


                function getDept(school_id, emp_dept_id) {

                    $('#department').empty();
                    $.ajax({
                        type: 'GET',
                        url: 'get-dept',
                        data: {
                            'school_id': school_id
                        },
                        success: function (departments) {
                            //console.log(response);
                            $.each(departments, function (index, department) {
                                var option = $('<option></option>').val(department.dept_id).text(department.dept_name);
                                $('#department').append(option);
                            });
                            $('#department').val(emp_dept_id);
                        },
                        error: function (xhr) {

                        }
                    });
                }


               function validateAndPreview(input) {
                    $('#emp_image-error').text('');
                    if (input.files && input.files[0]) {
                        var file = input.files[0];
                        var maxSize = 2 * 1024 * 1024; // 2MB
                        
                        if (file.size > maxSize) {
                            $('#emp_image-error').text('Image size exceeds 2MB limit.');
                            input.value = ''; // clear input
                            $('#image_preview_div').hide();
                            $('#edit_image_preview').attr('src', '');
                            return;
                        }

                        var validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!validTypes.includes(file.type)) {
                            $('#emp_image-error').text('Invalid file format. Only JPG, JPEG, and PNG are allowed.');
                            input.value = ''; // clear input
                            $('#image_preview_div').hide();
                            $('#edit_image_preview').attr('src', '');
                            return;
                        }

                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#edit_image_preview').attr('src', e.target.result);
                            $('#image_preview_div').show();
                        }
                        reader.readAsDataURL(file);
                    }
                }
            </script>
            <script>
                let searchTimeouts = {};

                function searchEmployee(query, type) {
                    clearTimeout(searchTimeouts[type]);

                    searchTimeouts[type] = setTimeout(() => {
                        if (query.length > 2) {
                            $.ajax({
                                url: "{{ route('search-employee') }}",
                                type: "GET",
                                data: {
                                    query: query
                                },
                                success: function (data) {
                                    var html = '';
                                    if (data.length > 0) {
                                        $.each(data, function (key, value) {
                                            html += '<a href="#" onclick="selectEmployee(\'' + value.username + '\', \'' + value.label + '\', \'' + type + '\')">' + value.label + '</a>';
                                        });
                                        $('#' + type + '_list').html(html).show();
                                    } else {
                                        $('#' + type + '_list').html('').hide();
                                    }
                                }
                            });
                        } else {
                            $('#' + type + '_list').html('').hide();
                        }
                    }, 500); // 500ms delay
                }

                function selectEmployee(username, displayName, type) {
                    $('#' + type).val(username);
                    $('#' + type).attr('data-display-name', displayName);
                    $('#' + type + '_list').html('').hide();
                    return false; // Prevent default anchor behavior
                }

                // Hide dropdown when clicking outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.dropdown-content').length && !$(e.target).is('input')) {
                        $('.dropdown-content').hide();
                    }
                });
            </script>

            @stop