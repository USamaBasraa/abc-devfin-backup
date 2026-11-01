@extends('layouts.app')

@section('title')
    BNU CMS
@stop
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
{{-- <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
 <!-- DataTables CSS -->
 <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
 <!-- DataTables Responsive CSS -->
 <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
 <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}

<style>
.invisible { display: none; }
.success-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .success-flag.show {
        right: 0px;
    }
    .error-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #f44336; /* Red color for error */
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .error-flag.show {
        right: 0px;
    }
    .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
        margin-top:5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }

    /* Filter Form Styling */
    .filter-form-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
    }

    .filter-form-container .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .filter-form-container label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
        display: block;
        background: transparent;
        padding: 0;
    }

    .filter-form-container .form-control,
    .filter-form-container .custom-select,
    .filter-form-container select,
    .filter-form-container input[type="text"],
    .filter-form-container input[type="date"] {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #fff;
        width: 100% !important;
        max-width: 100%;
        box-sizing: border-box;
        display: block;
    }

    .filter-form-container .form-control:focus,
    .filter-form-container .custom-select:focus,
    .filter-form-container select:focus,
    .filter-form-container input[type="text"]:focus,
    .filter-form-container input[type="date"]:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        outline: none;
        background-color: #fff;
    }

    .filter-form-container .form-control:hover,
    .filter-form-container .custom-select:hover,
    .filter-form-container select:hover,
    .filter-form-container input[type="text"]:hover,
    .filter-form-container input[type="date"]:hover {
        border-color: #007bff;
    }

    /* Ensure Select2 dropdowns match width */
    .filter-form-container .select2-container {
        width: 100% !important;
        max-width: 100%;
    }

    .filter-form-container .select2-container .select2-selection {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        padding: 10px 14px;
        min-height: 46px;
    }

    .filter-btn-container {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #dee2e6;
    }

    .filter-btn-container .btn-primary {
        padding: 12px 40px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .filter-btn-container .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    }

    .filter-btn-container .btn-primary:active {
        transform: translateY(0);
    }

    .filter-btn-container .btn-primary i {
        margin-right: 8px;
    }

    #hostel_scholarship:hover {
        background-color: #e8f0fe;
        border-color: #6c757d;
        transform: scale(1.1);
    }

    #hostel_scholarship:checked {
        background-color: #e8f0fe;
        border-color: #6c757d;
        transform: scale(1.1);
    }

    /* Action Section Styling */
    .action-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        margin-top: 30px;
        border: 1px solid #dee2e6;
    }

    .action-section h4 {
        font-size: 22px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    .action-section .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .action-section .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .action-section .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }

    .action-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-btns .btn {
        padding: 10px 24px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
        border: none;
    }

    .action-btns .btn-primary {
        background: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    .action-btns .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
    }

    .action-btns .btn-danger {
        background: #dc3545;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .action-btns .btn-danger:hover {
        background: #c82333;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    @media (max-width: 767px) {
        .action-btns {
            flex-direction: column;
        }
        .action-btns .btn {
            width: 100%;
        }
    }
    /* Bigger checkbox styling */
    .approve-select-data {
        width: 20px;
        height: 20px;
        cursor: pointer;

        /* Optional modern styling */
        accent-color: #28a745; /* green tick color */
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        border-radius: 4px;
    }

    /* Optional hover effect */
    .approve-select-data:hover {
        box-shadow: 0 3px 8px rgba(0,0,0,0.4);
    }

    .preview-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 2px 6px rgba(0,0,0,0.2); /* Shadow */
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .preview-btn:hover {
        background-color: #0056b3;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }


    /* DataTable Pagination Styling */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 20px;
        text-align: center;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 8px 16px;
        margin: 0 4px;
        min-width: 40px;
        text-align: center;
        text-decoration: none;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background-color: #fff;
        color: #007bff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background-color: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
        box-shadow: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        font-weight: 600;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }

    /* Responsive pagination */
    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            font-size: 13px;
            min-width: 35px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px;
        }

        .dataTables_wrapper .dataTables_info {
            text-align: center;
            padding-top: 10px;
            font-size: 13px;
        }
    }


</style>
<?php
// print_r($Scholarship_Type);exit;
// $sch_type = array("Need Base","Merit Base","BSS Emp","BSS Emp Ward","BNU Emp","BNU Emp Ward","Nest","UMISAA","SHEHLA Siagol");
?>
<div id="success-flag" class="success-flag"></div>
<div id="error-flag" class="error-flag"></div>
<div class="card" style="background: white !important; border: none !important; box-shadow: none !important; margin-bottom: 0px !important;">
  <div class="card-header" style="display:inline">
    <div style="float:left">
        <h3 class="card-title">Bulk Challan Update & Cancellation</h3>
    </div>
  </div>
  <div class="card-body" style="background: white !important; border-radius: 10px; padding: 30px;">
    <div class='row'>
        <div class="col-md-2 col-lg-3"></div>
        <div class='col-md-8 col-lg-6 mx-auto'>
            <div>
            <form id="studentRecord" method="post" onsubmit="event.preventDefault(); autoSearch(this);">
                @csrf
                <div class="form-group row align-items-center">
                    <label for="academic_session" class="col-md-2 col-form-label">Session</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search" name="academic_session" id="academic_session" data-placeholder="Select Session">
                            <option value="">Select Session</option>
                        @foreach ($academic_session as $row)
                            <option value="{{ $row->sess_id }}"
                                {{ $row->sess_id == 165 ? 'selected' : '' }}>
                                {{ $row->sess_name }}
                            </option>
                        @endforeach

                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="facultyWload_school_id" class="col-md-2 col-form-label">School</label>
                     <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="facultyWload_school_id"
                                id="facultyWload_school_id"
                                data-placeholder="Select School">
                            <option value="">Select School</option>
                        </select>
                     </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="department_id"  class="col-md-2 col-form-label">Department</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="department_id"
                                id="department_id">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="program_id" class="col-md-2 col-form-label">Program</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="program_id"
                                id="program_id">
                            <option value="">Select Program</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="admission_type" class="col-md-2 col-form-label">Student Type</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="admission_type"
                                id="admission_type"
                                data-placeholder="Select Admission Type">
                            <option value="">Select Admission Type</option>
                            <option value="Existing">Existing</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center" id="sub_admission_row" style="display:none;">
                    <label for="admission_category" class="col-md-2 col-form-label">Admission Category</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="admission_category"
                                id="admission_category">
                            <option value="">Select Admission Category</option>
                            <option value="Early">Early</option>
                            <option value="Regular">Regular</option>
                            <option value="Rolling">Rolling</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sc_enrollment_no" class="col-md-2 col-form-label">Enrollment No</label>
                    <div class="col-md-10">
                        <input type="text"
                            class="form-control"
                            name="sc_enrollment_no"
                            id="sc_enrollment_no"
                            placeholder="Enter Enrollment No">
                        <div class="invalid-feedback">
                            Please enter a Type.
                        </div>
                    </div>
                </div>



                <div class="form-group row align-items-center">
                    <label for="sc_challan_type" class="col-md-2 col-form-label">Challan Type</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search" onchange="session_enrollment_change()" name="sc_challan_type" id="sc_challan_type" data-placeholder="Select Challan Type">
                            <option value="">Select Challan Type</option>
                            @foreach ($challan_type as $row)
                                <!-- @php
                                    $allowed_users = ['saqib.jamil', 'esha.abid', 'asif_nadeem', 'ahmad.arfeen'];
                                    $is_allowed_user = in_array(auth()->user()->username, $allowed_users);
                                @endphp -->
                                <option value="{{$row->sc_challan_type}}">{{ $row->sc_challan_type }}</option>

                            @endforeach
                        </select>
                    </div>
                    <div class="invalid-feedback">
                        Please select a Type.
                    </div>
                </div>

                <div class="form-group row align-items-center" id="regular_dropdown_row" style="display:none;">
                    <label for="regular_category" class="col-md-2 col-form-label">Hostel Type</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="regular_category"
                                id="regular_category">
                            <option value="">Select Type</option>
                            <option value="regular">Regular</option>
                            <option value="non-regular">Non-Regular</option>
                            <option value="regular_outside">Regular-Outside</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center" id="hostel_dropdown_row" style="display:none;">
                    <label for="hostel_category" class="col-md-2 col-form-label">Hostel Category</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="hostel_category"
                                id="hostel_category">
                            <option value="">Select Hostel</option>
                            <option value="BH">Boys Hostel</option>
                            <option value="GH">Girls Hostel</option>
                            <option value="Hostel_other_utilities">Hostel Other Utilities</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center" id="utilities_dropdown_row" style="display:none;">
                    <label for="utilities_category" class="col-md-2 col-form-label">Utilities</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search"
                                name="utilities_category"
                                id="utilities_category">
                            <option value="">Select Utility</option>
                            <option value="Conditioner charges">Conditioner</option>
                            <option value="Refrigerator charges">Refrigerator</option>
                            <option value="Electric heater charges">Heater</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row align-items-center" style="display:none" id="fineDiv">
                    <label for="fine_category" class="col-md-2 col-form-label">Fine Category</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search" name="fine_category" id="fine_category" data-placeholder="Select Challan Type">
                            <option value="">Select Fine Category</option>
                            @foreach ($resfineCateg as $row)
                                <!-- @php
                                    $allowed_users = ['saqib.jamil', 'esha.abid', 'asif_nadeem', 'ahmad.arfeen'];
                                    $is_allowed_user = in_array(auth()->user()->username, $allowed_users);
                                @endphp -->
                                <option value="{{$row->id}}">{{ $row->fc_category_name }}</option>

                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please select a Type.
                        </div>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sc_challan_status" class="col-md-2 col-form-label">Challan Status Type</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control select2-show-search" name="sc_challan_status" id="sc_challan_status" data-placeholder="Select Challan Status">
                            <option value="">Select Challan Status</option>
                            <option value="PAID">Paid</option>
                            <option value="UNPAID">Unpaid</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a Type.
                        </div>
                    </div>
                </div>

                <!-- From Date -->
                <div class="form-group row align-items-center">
                    <label for="from_date" class="col-md-2 col-form-label">From Date</label>
                    <div class="col-md-10">
                        <input type="date" class="form-control" name="from_date" id="from_date" value="{{ $row->from_date ?? '' }}">
                        <div class="invalid-feedback">
                            Please select a From Date.
                        </div>
                    </div>
                </div>

                <!-- To Date -->
                <div class="form-group row align-items-center">
                    <label for="to_date" class="col-md-2 col-form-label">To Date</label>
                    <div class="col-md-10">
                        <input type="date" class="form-control" name="to_date" id="to_date" value="{{ $row->to_date ?? '' }}">
                        <div class="invalid-feedback">
                            Please select a To Date.
                        </div>
                    </div>
                </div>

            </form>
            </div>
        </div>
        <div class="col-md-2 col-lg-3"></div>
        {{-- <div class='col-md-6'>
            <table class="table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="student_label"></td>
                        <td style='font-size: 15px;' id="student_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="school_label"></td>
                        <td style='font-size: 15px;' id="school_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="dept_label"></td>
                        <td style='font-size: 15px;' id="dept_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="prog_label"></td>
                        <td style='font-size: 15px;' id="prog_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="status_label"></td>
                        <td style='font-size: 15px;' id="st_status"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="cgpa_label"></td>
                        <td style='font-size: 15px;' id="st_cgpa"></td>
                    </tr>
                </tbody>
            </table>
        </div> --}}
    </div>
    <div class="filter-btn-container">
        <div style="text-align:center;">
            <button type="button" class="btn btn-primary" onclick="submitForm()"> <i class="fa fa-paper-plane"></i> Filter</button>
        </div>
        <span id="successMsg"></span>
    </div>
    <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
    <div id="error_message" class="alert alert-danger" style="display: none;"></div>
    <div id="success_message" class="alert alert-success" style="display: none;"></div>

    <div class="action-section">
        <h4>Action</h4>
        <div class="row align-items-end">
            <!-- From Date -->
            <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" name="due_date" id="due_date">
                <div class="invalid-feedback">
                    Please select a From Date.
                </div>
            </div>

            <!-- To Date -->
            <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
                <label for="valid_date" class="form-label">Valid Date</label>
                <input type="date" class="form-control" name="valid_date" id="valid_date">
                <div class="invalid-feedback">
                    Please select a To Date.
                </div>
            </div>

            <!-- Remarks -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <label for="sc_remarks" class="form-label">Remarks</label>
                <input type="text" class="form-control" name="sc_remarks" id="sc_remarks" placeholder="Enter remarks">
            </div>

            <!-- Buttons -->
            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                <div class="action-btns">
                    <button type="button" class="btn btn-primary" onclick="filterSchlrDetail()">Update Challan Dates</button>
                    <button type="button" class="btn btn-danger" onclick="cancelChallanBulk()">Cancel Bulk Challan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="action-section">
        <h4>Change Hostel Type</h4>
        <div class="row align-items-end">

            <!-- Hostel Type -->
            <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
                <label for="from_hostel_type" class="form-label">From Hostel Type <span style="color: red;">*</span></label>
                <select class="form-control" name="from_hostel_type" id="from_hostel_type" required>
                    <option value="">Select Type</option>
                    <option value="Regular">Regular</option>
                    <option value="Non-Regular">Non-Regular</option>
                    <option value="Regular_outside">Regular Safari Campus</option>
                    <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                    <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                    <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                </select>
                <div class="invalid-feedback">
                    Please select a Hostel Type.
                </div>
            </div>


              <!-- Hostel Type -->
            <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
                <label for="to_hostel_type" class="form-label">To Hostel Type <span style="color: red;">*</span></label>
                <select class="form-control" name="to_hostel_type" id="to_hostel_type" required>
                    <option value="">Select Type</option>
                    <option value="Regular">Regular</option>
                    <option value="Non-Regular">Non-Regular</option>
                    <option value="Regular_outside">Regular Safari Campus</option>
                    <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                    <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                    <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                    
                </select>
                <div class="invalid-feedback">
                    Please select a Hostel Type.
                </div>
            </div>

            <!-- Remarks -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <label for="hostel_sc_remarks" class="form-label">Remarks <span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="hostel_sc_remarks" id="hostel_sc_remarks" placeholder="Enter remarks" required>
            </div>

            <!-- Buttons -->
            <div class="col-lg-1 col-md-6 col-sm-12 mb-3">
                <div class="action-btns">
                    <button type="button" id="updateHostelTypeBtn" class="btn btn-primary">
                        Update Hostel Type
                    </button>

                </div>
            </div>
        </div>
    </div>


    <div class="row" style="margin-top:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card" style="padding: 20px">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>

                            <th scope="col">Sr No.</th>
                            <th scope="col">Challan</th>
                            <th scope="col">App Id</th>
                            <th scope="col">Enrollment NO</th>
                            <th scope="col">Name</th>
                            <th scope="col">Course Name</th>
                            <th scope="col">School</th>
                            <th scope="col">Department</th>
                            <th scope="col">Program</th>
                            <th scope="col">Student Type</th>
                            <th scope="col">Admission Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Challan Type</th>
                            <th scope="col">Hostel Type</th>
                            <th scope="col">Hostel Category</th>
                            <th scope="col">Hostel Utilities</th>
                            <!-- <th scope="col">Hostel Details</th> -->
                            <th scope="col">Challan Status</th>
                            <th scope="col">Fine Category</th>
                            <th scope="col">Studnet Status</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Registration Fee</th>
                            <th scope="col">Preview</th>
                            <th><input type="checkbox" class="checkbox approve-select-data" id="approve-select-all" value=""/></th>

                          </tr>
                        </thead>
                      </table>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa fa-receipt"></i> Challan Receipt
        </h5>
        <!-- Bootstrap 4 close button -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="receipt-container" id="receiptContent">
          <!-- Receipt will be populated here -->
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="printReceipt()">
          <i class="fa fa-print"></i> Print
        </button>
      </div>

    </div>
  </div>
</div>


          <?php $count = 1; ?>


  </div>
</div>
@stop

@section('javascript')
{{-- commenting the js version because auto complete is not compaitble with it --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script> --}}

<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script>
    var totalAwardedAmount_hostel = 0;
    var totalAwardedPercent_hostel = 0;
    var permitted_amount = 0;
    $(document).ready(function(){
        // filterSchlrDetail();
        // $('#hostel_scholarship').change(function () {
        //     session_enrollment_change();

        // });
        LoadSchlrshipDetail('165', '', '', '','','','','','');
        loadSchools();
    });
</script>
<script>
    function getStatusBadge(status) {
        const statusLower = (status || '').toLowerCase().trim();
        if (statusLower === 'paid') {
            return `<span class="status-paid">PAID</span>`;
        } else if (statusLower === 'unpaid') {
            return `<span class="status-unpaid">UNPAID</span>`;
        }
        return status;
    }

  function previewRecord(button) {
    const rowDataAttr = button.getAttribute('data-row');
    if (!rowDataAttr) return console.error('No data-row attribute found.');

    let data;
    try {
        data = JSON.parse(rowDataAttr);
    } catch (e) {
        return console.error('Invalid JSON:', e);
    }
    if (['Hostel_Fee', 'Full_semester'].includes(data.sc_challan_type)) {
        discount = ``;
    } else {
        discount = `<div class="col-sm-6"><strong>Discount:</strong> ${data.sc_discount || '0%'}</div>`;

    }
    console.log('Full Row Data:', data);

    const challanType = (data.sc_challan_type || '').toLowerCase();
    const isDonation = challanType === 'donation';
    const showFineCategory = ['shortcourse', 'fine', 'misc_fee'].includes(challanType);
    const challanStatus = getStatusBadge(data.sc_challan_status || 'Unknown');

    const receiptHTML = `
        <div class="receipt-header text-center bg-primary text-white p-3 mb-3">
            <h5 class="mb-0">CHALLAN RECEIPT</h5>
        </div>

        <div class="container">
            <h6 class="font-weight-bold text-primary mb-2 text-center">Student Details</h6>
            <div class="row">
                <div class="col-sm-6"><strong>Name:</strong> ${data.sc_studentname || 'N/A'}</div>
                <div class="col-sm-6"><strong>Enrollment No:</strong> ${data.sc_enrollment_no || 'N/A'}</div>
                <div class="col-sm-6"><strong>App ID:</strong> ${data.sc_app_id || 'N/A'}</div>
                ${isDonation ? `
                    <div class="col-sm-6"><strong>Email:</strong> ${data.email || 'N/A'}</div>
                    <div class="col-sm-6"><strong>Phone:</strong> ${data.phone || 'N/A'}</div>
                ` : `
                    <div class="col-sm-6"><strong>School:</strong> ${data.school_name || 'N/A'}</div>
                    <div class="col-sm-6"><strong>Department:</strong> ${data.dept_name || 'N/A'}</div>
                    <div class="col-sm-6"><strong>Program:</strong> ${data.prog_name || 'N/A'}</div>
                `}
            </div>

            <hr>

            <h6 class="font-weight-bold text-primary mb-2 text-center">Challan Details</h6>
            <div class="row">
                <div class="col-sm-6"><strong>Challan No:</strong> ${data.id || 'N/A'}</div>
                <div class="col-sm-6"><strong>Challan Type:</strong> ${data.sc_challan_type || 'N/A'}</div>
                <div class="col-sm-6"><strong>Challan Issue Date:</strong> ${formatDate(data.sc_issu_date)}</div>
                <div class="col-sm-6"><strong>Challan Paid Date:</strong> ${formatDate(data.callback_paid_on)}</div>
                ${showFineCategory ? `<div class="col-sm-6"><strong>Category:</strong> ${data.categ_name || 'None'}</div>` : ''}
                ${isDonation ? `
                    <div class="col-sm-6"><strong>Donation Type:</strong> ${data.donation_type || 'N/A'}</div>
                    <div class="col-sm-6"><strong>Donation Affiliation:</strong> ${data.affiliation || 'N/A'}</div>
                ` : 
                    discount
                }
                <div class="col-sm-6"><strong>Amount:</strong> Rs. ${data.invoiceamount || '0'}</div>
                <div class="col-sm-6"><strong>Status:</strong> ${challanStatus}</div>
            </div>

            <hr>
            <div class="text-center text-muted small">
                <p>Print this receipt for your records</p>
            </div>
        </div>
    `;

    $('#receiptContent').html(receiptHTML);
    $('#previewModal').modal('show');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    if (isNaN(date)) return 'N/A';

    const day = String(date.getDate()).padStart(2, '0');
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
}

function getStatusBadge(status) {
    const s = status.toLowerCase();
    if (s === 'paid') return '<span class="badge badge-success">PAID</span>';
    if (s === 'unpaid') return '<span class="badge badge-danger">UNPAID</span>';
    return `<span class="badge badge-secondary">${status}</span>`;
}

function printReceipt() {
    const receiptContent = document.getElementById('receiptContent').innerHTML;

    const printWindow = window.open('', '', 'height=800,width=1000');

    printWindow.document.write(`
        <html>
        <head>
            <title>Challan Receipt</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

            <style>
                body {
                    background-color: #fff;
                    padding: 20px;
                    font-size: 14px;
                }
                .receipt-header {
                    background-color: #007bff !important;
                    color: #fff !important;
                    text-align: center;
                    padding: 10px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .font-weight-bold.text-primary {
                    color: #007bff !important;
                    text-align: center;
                }
                .status-paid {
                    background-color: #28a745;
                    color: #fff;
                    padding: 3px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                }
                .status-unpaid {
                    background-color: #dc3545;
                    color: #fff;
                    padding: 3px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                }
                .container {
                    max-width: 800px;
                    margin: auto;
                }
                hr {
                    border-top: 1px solid #ccc;
                }
                .row {
                    display: flex;
                    flex-wrap: wrap;
                    margin-bottom: 10px;
                }
                .col-sm-6 {
                    flex: 0 0 50%;
                    max-width: 50%;
                    padding: 5px 10px;
                    box-sizing: border-box;
                }
                @media print {
                    .btn, .modal-footer {
                        display: none !important;
                    }
                }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);

    printWindow.document.close();

    // ✅ Wait for content to fully load before printing
    printWindow.onload = function () {
        printWindow.focus();
        printWindow.print();

        // ✅ Close the print window automatically after printing (or cancel)
        printWindow.onafterprint = function () {
            printWindow.close();
            // $('#previewModal').modal('hide'); // Close the preview modal
        };
    };
}



</script>
<script>
$(document).ready(function () {
    $('#admission_type').on('change', function () {
        let selected = $(this).val();

        if (selected.includes("New Admission")) {
            $('#sub_admission_row').show();
            $('#admission_category').val("").trigger("change"); // ✅ reset every time it's shown
        } else {
            $('#sub_admission_row').hide();
            $('#admission_category').val("").trigger("change"); // ✅ reset every time it's hidden
        }
    });
});
$(document).ready(function () {
    // Step 1: Check challan type
    $('#sc_challan_type').on('change', function () {
        let selected = $(this).val();

        if (selected === "Hostel_Fee") {
            $('#regular_dropdown_row').show();   // show Regular/Non-Regular dropdown
            $('#regular_category').val("").trigger("change");

            // reset others
            $('#hostel_dropdown_row').hide();
            $('#utilities_dropdown_row').hide();
            $('#hostel_category').val("").trigger("change");
            $('#utilities_category').val("").trigger("change");
        } else {
            // hide all if not Hostel_Fee
            $('#regular_dropdown_row').hide();
            $('#hostel_dropdown_row').hide();
            $('#utilities_dropdown_row').hide();

            $('#regular_category').val("").trigger("change");
            $('#hostel_category').val("").trigger("change");
            $('#utilities_category').val("").trigger("change");
        }
    });

    // Step 2: On selecting Regular / Non-Regular
    $('#regular_category').on('change', function () {
        let selected = $(this).val();

        if (selected) {
            $('#hostel_dropdown_row').show();   // show BH/GH/Utilities
            $('#hostel_category').val("").trigger("change");

            $('#utilities_dropdown_row').hide();
            $('#utilities_category').val("").trigger("change");
        } else {
            $('#hostel_dropdown_row').hide();
            $('#utilities_dropdown_row').hide();

            $('#hostel_category').val("").trigger("change");
            $('#utilities_category').val("").trigger("change");
        }
    });

    // Step 3: On selecting Hostel category (BH/GH/Utilities)
    $('#hostel_category').on('change', function () {
        let selected = $(this).val();

        if (selected === "Hostel_other_utilities") {
            $('#utilities_dropdown_row').show();
            $('#utilities_category').val("").trigger("change");
        } else {
            $('#utilities_dropdown_row').hide();
            $('#utilities_category').val("").trigger("change");
        }
    });
});
</script>
<script type="text/javascript">

    // $(document).ready(function() {
    //     session_enrollment_change(); // Trigger on page load in case a value is preselected
    // });
$(document).on('change', '#approve-select-all', function() {
    if ($(this).prop("checked") == true) {
        $('.approve-select-data').prop('checked', true);
        console.log($('.approve-select-data').val());

    } else {
        $('.approve-select-data').prop('checked', false);
        console.log($('.approve-select-data').val());
    }
});
function loadSchools() {
    $.ajax({
        url: '/api/get-academic-sessions-schools',
        type: 'GET',
        data: {
            api_token: '{{ auth()->user()->api_token }}'
        },
        success: function(data) {
            var select = $('#facultyWload_school_id');
            select.empty().append('<option value="">Select School</option>');
            data.forEach(function(item) {
                select.append($('<option></option>')
                    .attr('value', item.school_id)
                    .text(item.school_name));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading schools:', error);
        }
    });
}
$('#facultyWload_school_id').change(function() {
    var schoolId = $(this).val();
    console.log('School selected:', schoolId);

    // Reset dependent dropdowns
    $('#department_id').empty().append('<option value="">Select Department</option>');
    $('#program_id').empty().append('<option value="">Select Program</option>');

    if(schoolId) {
        $.ajax({
            url: '/api/get-academic-sessions-departments/' + schoolId,
            type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
            success: function(data) {
                console.log('Departments loaded:', data);
                var select = $('#department_id');
                data.forEach(function(item) {
                    select.append(
                        $('<option></option>')
                            .attr('value', item.dept_id)
                            .text(item.dept_name)
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading departments:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }
});
$('#department_id').change(function() {
    var deptId = $(this).val();
    var schoolId = $('#facultyWload_school_id').val();
    console.log('Department selected:', deptId, ' | School:', schoolId);

    // Reset program dropdown
    $('#program_id').empty().append('<option value="">Select Program</option>');

    if(deptId) {
        $.ajax({
            url: '/api/get-academic-sessions-programs/' + schoolId + '/' + deptId,
            type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
            success: function(data) {
                console.log('Programs loaded:', data);
                var select = $('#program_id');
                data.forEach(function(item) {
                    select.append(
                        $('<option></option>')
                            .attr('value', item.prog_id)
                            .text(item.prog_name)
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading programs:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }
});
$('#program_id').change(function() {
    var programId = $(this).val();
    var deptId = $('#department_id').val();
    var schoolId = $('#facultyWload_school_id').val();
    var schoolName = $('#facultyWload_school_id option:selected').text();
    var deptName   = $('#department_id option:selected').text();
    var progName   = $('#program_id option:selected').text();

    console.log('Program selected:', progName, ' | Department:', deptName, ' | School:', schoolName);
});

function autoSearch(ele){
   $('#filter_message').hide();
   var bi_form = document.querySelector('#filterForm');
   var fd      = new FormData(bi_form);

   if( (fd.get('username') == '') && (fd.get('accesstion_user') == '') ){
       $('#filter_message').show();
       $('#filter_message').html('No Record Found!');
       return false;
   }

   $('#user-search-btn').html('Searching...');

   sendPostRequest('library/search-by-filter',function(res){
       $('#user-search-btn').html('Load Detail');
       if(res.username){
           var username = res.username;
           $('#sai_enrollment_no').val(username);
           $('#sisuserUsername').val(username);
           createProfileView(res);
           loadUserBookIssuedHistory(username);
       }else{

           $('#filter_message').show();
           $('#filter_message').html('No Record Found!');
       }
   },fd,function(error){
       $('#user-search-btn').html('Load Detail');
    //    console.log('error : ',error);
   });
   return false;
}
function getProfile(){
    var studentType = $('#studentType').val();
    var session = $('#academic_session').val();
    var rol_no = $('#sai_enrollment_no').val();
    // if you want to show alert msg at the top write this (.message_div) as second parem in sendGetRequest
   $.ajax({
       url: '{{route("get-student-profile")}}/'+rol_no+'/'+studentType+'/'+session,
       type: 'GET',
       success: function(res) {
            // console.log(res);
           // Set a timeout to close the loader after two seconds
           if(res.message == 1){
                $('#sch_type').removeAttr("disabled");
                $('#sch_percentage').removeAttr("disabled");
                $('#sch_amount').removeAttr("disabled");
                $('#sch_arrear').removeAttr("disabled");
                $('#sch_details').removeAttr("disabled");
                var tbodyTRhtml = '';
                $(res.data).each(function(index,row){

                    if(row.aai_id){
                        $('#sch_aai_id').val(row.aai_id);
                    }
                    if(row.arrears){
                        $('#sch_arrear').val(row.arrears);
                    }


                    if(row.school_name){
                        $('#student_label').html('Name:');
                        $('#student_name').html(row.student_name);
                    }else{
                        $('#student_label').html('');
                        $('#student_name').html('');
                    }

                    if(row.school_name){
                        $('#school_label').html('School:');
                        $('#school_name').html(row.school_name);
                    }else{
                        $('#school_label').html('');
                        $('#school_name').html('');
                    }

                    if(row.dept_name){
                    $('#dept_label').html('Department:');
                    $('#dept_name').html(row.dept_name);
                    }else{
                        $('#dept_label').html('');
                        $('#dept_name').html('');
                    }

                    if(row.prog_name){
                        $('#prog_label').html('Program:');
                        $('#prog_name').html(row.prog_name);
                    }else{
                        $('#prog_label').html('');
                        $('#prog_name').html('');
                    }

                    if(row.sai_student_status){
                        $('#status_label').html('Status:');
                        $('#st_status').html(row.sai_student_status);
                    }else{
                        $('#status_label').html('');
                        $('#st_status').html('');
                    }
                    if(row.cgpa){
                        $('#cgpa_label').html('CGPA:');
                        $('#st_cgpa').html(row.cgpa);
                    }else{
                        $('#cgpa_label').html('');
                        $('#st_cgpa').html('');
                    }

                });
            }else{


                $('#student_name').html('<b style="color: red;">Kindly enter valid ' + $('.searchBy').text().replace('#', 'No.') + '</b>');
                $('#school_name').html('');
                $('#dept_name').html('');
                $('#prog_name').html('');
                $('#st_status').html('');
                $('#st_cgpa').html('');
                $('#student_label').html('');
                $('#school_label').html('');
                $('#dept_label').html('');
                $('#prog_label').html('');
                $('#status_label').html('');
                $('#cgpa_label').html('');

                $('#sch_type').attr('disabled', true);
                $('#sch_percentage').attr('disabled', true);
                $('#sch_amount').attr('disabled', true);
                $('#sch_arrear').attr('disabled', true);
                $('#sch_details').attr('disabled', true);
            }
       },
       error: function(xhr, status, error) {
            $('#error-flag').text('Error');
            $('#error-flag').addClass('show');
            setTimeout(function() {
                $('#error-flag').removeClass('show');
            }, 3000); // Hide after 3 seconds
           // Request failed, handle the error
        //    console.log("Error: " + error);
       }
   });

}
function submitForm() {


    var academic_session = $('#academic_session').val();
    var sc_challan_type = $('#sc_challan_type').val();
    var sc_fine_category = $('#fine_category').val();
    var sc_challan_status = $('#sc_challan_status').val();
    var sc_enrollment_no = $('#sc_enrollment_no').val();
    var from_date = $('#from_date').val();

    var school_id = $('#facultyWload_school_id').val();
    var dept_id = $('#department_id').val();
    var prog_id = $('#program_id').val();
    var schoolName = $('#facultyWload_school_id option:selected').text();
    var deptName   = $('#department_id option:selected').text();
    var progName   = $('#program_id option:selected').text();

    var dateObj = new Date(from_date);

    var day = String(dateObj.getDate()).padStart(2, '0');
    var month = dateObj.toLocaleString('en-US', { month: 'short' }).toUpperCase(); // "MAY"
    var year = String(dateObj.getFullYear()).slice(-2); // "25"

    var formattedfromDate = `${day}-${month}-${year}`;

    var to_date = $('#to_date').val();


    var todateObj = new Date(to_date);

    var day = String(todateObj.getDate()).padStart(2, '0');
    var month = todateObj.toLocaleString('en-US', { month: 'short' }).toUpperCase(); // "MAY"
    var year = String(todateObj.getFullYear()).slice(-2); // "25"

    var formattedtoDate = `${day}-${month}-${year}`;

    var admission_type = $('#admission_type').val();
    var admission_category = $('#admission_category').val(); // ✅ Early / Regular / Rolling
    var hostel_category = $('#hostel_category').val();       // ✅ All / Boys Hostel / Girls Hostel
    var utilities_category = $('#utilities_category').val();
    var hostel_type = $('#regular_category').val();

    console.log("Admission Category:", admission_category);
    console.log("Hostel Category:", hostel_category);
    console.log("Utilities Category:", utilities_category);
    console.log("Hostel Type:", hostel_type);
    //console.log(formattedDate); // "08-MAY-25"

    LoadSchlrshipDetail(academic_session, sc_challan_type, sc_challan_status,sc_enrollment_no,formattedfromDate,formattedtoDate,from_date,to_date,sc_fine_category, school_id, dept_id, prog_id, admission_type, schoolName, deptName, progName, admission_category, hostel_category, utilities_category, hostel_type);
}


function LoadSchlrshipDetail(academic_session, sc_challan_type, sc_challan_status,sc_enrollment_no,formattedfromDate,formattedtoDate,from_date,to_date,sc_fine_category, school_id, dept_id, prog_id, admission_type, schoolName, deptName, progName, admission_category, hostel_category, utilities_category, hostel_type){
        // let filterParams = studentType;
        // console.log(filterParams);
        // $('#studentType').val();
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false,
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,

            lengthMenu: [10, 25, 50, 100, 500, 1000, 5000],
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'get',
                url:  "/api/get-student-challan-data",
                data: function(d) {
                    d.academic_session = academic_session;
                    d.sc_challan_type = sc_challan_type;
                    d.sc_challan_status = sc_challan_status;
                    d.sc_enrollment_no = sc_enrollment_no;
                    d.formattedfromDate = formattedfromDate;
                    d.formattedtoDate = formattedtoDate;
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.sc_fine_category = sc_fine_category;
                    d.school_id = school_id;
                    d.dept_id = dept_id;
                    d.prog_id = prog_id;
                    d.admission_type = admission_type;
                    d.admission_category = admission_category;
                    d.hostel_category = hostel_category;
                    d.utilities_category = utilities_category;
                    d.hostel_type = hostel_type;
                },
            },
            columns: [
                // {
                //     data: null, // This will be used for the serial number
                //     render: function (data, type, row, meta) {
                //         return meta.row + 1; // meta.row is the zero-based row index
                //     },
                //     orderable: false, // Disable ordering on the serial number column
                //     searchable: false // Disable searching on the serial number column
                // },  id,sc_app_id,invoiceamount,sc_challan_type

                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Sr. No. (starts from 1)
                    },

                },
                {data: "id"},
                {data: "sc_app_id"},
                {data: "sc_enrollment_no"},
                {data: "sc_studentname"},
                {
                    data: "course_name",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "school_name",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "dept_name",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "prog_name",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "sc_student_status",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "sc_admission_round",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {data: "invoiceamount"},
                {data: "sc_challan_type"},
                {
                    data: "sc_session_type",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "sc_hostel_category",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "sc_hostel_fee_details",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                // {
                //     data: "sc_hostel_fee_details",
                //     render: function(data, type, row) {
                //         return data ? data : 'N/A';
                //     }
                // },
                // {
                //     data: "filtered_fee_detail",
                //     render: function(data, type, row) {
                //         return data ? data : 'N/A';
                //     }
                // },
               {
                    data: "sc_challan_status",
                    render: function (data, type, row) {

                        if (data === 'PAID') {
                            return `<span style="color: green; font-weight: bold;">${data}</span>`;
                        } else {
                            return `<span style="color: red; font-weight: bold;">${data}</span>`;
                        }

                    }
                },

                {data: "categ_name"},
                {
                    data: "student_status",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "sc_discount",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "ffs_admission_amt",
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="preview-btn"
                                data-row='${JSON.stringify(row).replace(/'/g, "&apos;")}'
                                onclick="previewRecord(this)">
                                <i class="fa fa-eye"></i> Preview
                            </button>
                        `;
                    }
                },

                {
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        var challan_status = row.sc_challan_status;
                        var selectbtn = '';
                         var checked = '';
                        // if(challan_status != 'PAID'){
                            checked = row.status === 'E' ? 'checked' : ''; // Check if status is 'E'
                            selectbtn = `<div>
                                                <input name="approved_honorList[]" class="approve-select-data" value="${row.id}" type="checkbox" ${checked} data-id="${row.id}" data-enroll="${row.sc_enrollment_no}" data-sc_app_id="${row.sc_app_id}" data-challan_status="${row.sc_challan_status}">
                                            </div>`;
                           
                        // }
                         return selectbtn;
                        
                    }
                },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
           dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Challans',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
                        modifier: {
                            page: 'current'   // ⭐ only visible rows
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Challans',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
                        modifier: {
                            page: 'current'   // ⭐ only visible rows
                        }
                    }
                },
                {
                    extend: 'print',
                    title: 'Challans',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
                        modifier: {
                            page: 'current'   // ⭐ only visible rows
                        }
                    }
                }
            ],
        createdRow: function (row, data, dataIndex) {
            if (data.sc_challan_status === 'PAID') {
                // Light green for PAID
                $(row).css('background-color', '#dcf5e2');
            } else {
                // Light red/pink for all other statuses
                $(row).css('background-color', '#f0ddde');
            }

        },



        });
}


$('#sai_enrollment_no').autoComplete({
    resolver: 'custom',
    autoSelect: false,
    formatResult: function (item) {
        return {
            value: 'hello ap',
            text: item.text + "[" + item.value + "] ",
            // html: [
            // 		$('<img>').attr('src', item.icon).css("height", 18), ' ',
            // 		item.text
            // 	]
        };
    },
    select : function (evt, item){
        // console.log('Hello Ahmed',item);
    },
    events: {
        search: function (qry, callback) {
            var studentType = $('#studentType').val();
            // console.log(qry);
            // let's do a custom ajax call
            $.ajax(
                '<?=url('api/library/search-user-for-suggestion')?>',
                {
                    data: { 'qry': qry, 'studentType': studentType}
                }
            ).done(function (res) {
                // console.log(res);
                callback(res)
            });
        }

    }
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    $('#sai_enrollment_no').val(item.value);
});
function session_enrollment_change(){
    // alert('123');
        var challanType = $('#sc_challan_type').val();
        // alert(challanType);
        if (challanType === 'Fine') {
            $('#fineDiv').show(); // Show Fine Category row
        } else {
            $('#fineDiv').hide();
            $('#fine_category').val('');

        }
    let session_id = $('#academic_session').val();
    let student_id = $('#sai_enrollment_no').val();
    let scholarship_id = $('#sch_type').val();

    if ($('#hostel_scholarship').is(':checked')) {
        if(session_id && student_id && scholarship_id){
            getHostelScholrshp_student(session_id, student_id, scholarship_id);
            $('#sch_percentage').val('');
            $('.hotelSch-amount').removeClass('d-none');
            $('#sch_percentage').prop('readonly', true);
        }else{
            $('#hostel_scholarship').prop('checked', false);
            alert('Please select Session, Scholarship Type and enter Student Id')
        }

    } else {
        $('.hotelSch-amount').addClass('d-none');
        $('#sch_amount').val('');
        $('#sch_percentage').prop('readonly', false);
    }
    $('#sch_amount').val('');
}

// function downloadExcel() {
//     // Select the table element
//     const table = document.querySelector('.table');

//     // Clone the table to avoid modifying the original table
//     const clonedTable = table.cloneNode(true);

//     // Remove the rightmost column (ACTION)
//     const rows = clonedTable.querySelectorAll('tr');
//     rows.forEach(row => {
//         const lastCellIndex = row.cells.length - 1; // Index of the last cell
//         row.deleteCell(lastCellIndex); // Remove the last cell (rightmost column)
//     });

//     // Convert the modified table to an Excel workbook
//     const wb = XLSX.utils.table_to_book(clonedTable);

//     // Download the Excel file
//     XLSX.writeFile(wb, 'student-record.xlsx');
// }
function verifyCgpa(element){
    // let schlrshipId = element.value;
    // let selectedIndex = element.selectedIndex;
    // let selectedText = element.options[selectedIndex].text;
    // if(schlrshipId == 1 || schlrshipId == 2){//Needbase ID is 1, Meritbase ID is 2
    //     // console.log("Selected Scholarship ID:", schlrshipId);
    //     // console.log("Selected Scholarship Type:", selectedText);
    //     var rol_no = $('#sai_enrollment_no').val();
    //     var session = $('#academic_session').val();
    //     $.ajax({
    //         url: '{{route("verify-student-cgpa")}}/'+rol_no+'/'+session,
    //         type: 'GET',
    //         success: function(res) {
    //             // Set a timeout to close the loader after two seconds
    //             // if(res.message == 1){
    //             //         $('#sch_type').removeAttr("disabled");
    //             //         $('#sch_percentage').removeAttr("disabled");
    //             //         $('#sch_details').removeAttr("disabled");
    //             //         var tbody = '';
    //             //         $(res.data).each(function(index,row){
    //             //             $('#student_name').html('<b>Name: </b>'+row.spi_name);
    //             //             $('#school_name').html('<b>School: </b>'+row.school_name);
    //             //             $('#dept_name').html('<b>Department: </b>'+row.dept_name);
    //             //         });
    //             //     }else{
    //             //         $('#student_name').html('<b>Kindly Enter Valid Enrollment No');
    //             //         $('#school_name').html('');
    //             //         $('#dept_name').html('');
    //             //         $('#sch_type').attr('disabled', true);
    //             //         $('#sch_percentage').attr('disabled', true);
    //             //         $('#sch_details').attr('disabled', true);
    //             //     }

    //         },
    //         error: function(xhr, status, error) {
    //             // Request failed, handle the error
    //             console.log("Error: " + error);
    //         }
    //     });
    // }

}
// function getFilterParams() {
//     const studentType = $('.filterbutton').attr('studentType');

//     let filterParams =  {
//         studentType: studentType,

//     };
//     return filterParams;
// }

function cancelChallanBulk(){

    const selectedIds = [];
    const selectedRecords = [];

    $('.approve-select-data:checked').each(function () {
        selectedRecords.push({
            id: $(this).data('id'),
            sc_enrollment_no: $(this).data('enroll'),
            sc_app_id: $(this).data('sc_app_id'),
            challan_status: $(this).data('challan_status')
        });
    });

    const sc_remarks = $('#sc_remarks').val();


    if (!sc_remarks) {
        alert('Please enter Remarks.');
        return;
    }

    // // Get form values
    // const due_date = $('#due_date').val();
    // const valid_date = $('#valid_date').val();
    // const sc_remarks = $('#sc_remarks').val();

    // if (selectedIds.length === 0) {
    //     alert('Please select at least one record to update.');
    //     return;
    // }

    // if (!from_date || !valid_date) {
    //     alert('Please select both From Date and To Date.');
    //     return;
    // }

    // Send AJAX request to backend
    $.ajax({
        url: '/student-record/student-cancel-challan-bulk',
        method: 'POST',
        data: {
            records: selectedRecords,
            sc_remarks: sc_remarks,
            // due_date: due_date,
            // valid_date: valid_date,
            // sc_remarks: sc_remarks,
            _token: $('meta[name="csrf-token"]').attr('content') // if using blade
        },
        success: function (response) {
            alert('Records updated successfully!');
            $('#data-table').DataTable().ajax.reload(); // Reload table
        },
        error: function (xhr) {
            alert('Error updating records');
        }
    });

}
function filterSchlrDetail() {
    // Get selected IDs
    const selectedIds = [];
    $('.approve-select-data:checked').each(function () {
        if ($(this).data('challan_status') !== 'PAID') {
            selectedIds.push($(this).val());
        }
    });
     

    // Get form values
    const due_date = $('#due_date').val();
    const valid_date = $('#valid_date').val();
    const sc_remarks = $('#sc_remarks').val();

    if (selectedIds.length === 0) {
        alert('Please select at least one record to update (Paid challans can not be edited).');
        return;
    }

    if (!sc_remarks) {
        alert('Please enter Remarks.');
        return;
    }

    // Send AJAX request to backend
    $.ajax({
        url: '/student-record/update-student-challan-dates',
        method: 'POST',
        data: {
            ids: selectedIds,
            due_date: due_date,
            valid_date: valid_date,
            sc_remarks: sc_remarks,
            _token: $('meta[name="csrf-token"]').attr('content') // if using blade
        },
        success: function (response) {
            alert('Records updated successfully!');
            $('#data-table').DataTable().ajax.reload(); // Reload table
        },
        error: function (xhr) {
            alert('Error updating records');
        }
    });
}


/////////////////////////
// Hostel Type Change Functionality
/////////////////////////

// Filter table by From Hostel Type dropdown
$(document).on('change', '#from_hostel_type', function () {
    var selectedType = $(this).val();
    var table = $('#data-table').DataTable();

    // Column 13 is "Hostel Type" column
    var hostelTypeColumnIndex = 13;

    if (selectedType === "") {
        // Clear filter if no type selected
        table.column(hostelTypeColumnIndex).search("").draw();
        console.log("Hostel type filter cleared");
    } else {
        // Apply filter with exact match using regex
        table.column(hostelTypeColumnIndex).search("^" + selectedType + "$", true, false).draw();
        console.log("Filtering by hostel type:", selectedType);
    }
});

function getSelectedIds() {
    let ids = [];
    $(".approve-select-data:checked").each(function () {
        if ($(this).data('challan_status') !== 'PAID') {
            ids.push($(this).val());
        }
    });
    return ids;
}

$(document).on("click", "#updateHostelTypeBtn", function () {

    let fromType = $("#from_hostel_type").val();
    let toType = $("#to_hostel_type").val();
    let remarks = $("#hostel_sc_remarks").val();
    let selectedIds = getSelectedIds();

    if (selectedIds.length === 0) {
        alert("Please select at least one record! (Paid challans can not be edited).");
        return;
    }
    if (fromType === "") {
        alert("Please select From Hostel Type!");
        return;
    }
    if (toType === "") {
        alert("Please select To Hostel Type!");
        return;
    }
    if (remarks === "" || remarks.trim() === "") {
        alert("Please enter Remarks!");
        return;
    }

    // Disable button to prevent double clicks
    $("#updateHostelTypeBtn").prop('disabled', true).text('Updating...');

    $.ajax({
        url: "/api/update-hostel-type",
        method: "POST",
        data: {
            ids: selectedIds,
            from_type: fromType,
            to_type: toType,
            remarks: remarks,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.success) {
                alert(res.message + "\nUpdated Records: " + res.updated_count);

                // Clear selections and reset form
                $("#from_hostel_type").val("").trigger("change");
                $("#to_hostel_type").val("").trigger("change");
                $("#hostel_sc_remarks").val("");

                // Reload table
                $('#data-table').DataTable().ajax.reload(null, false);
            } else {
                alert("Error: " + res.message);
            }
        },
        error: function (xhr) {
            let errorMsg = "Error updating hostel type!";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert(errorMsg);
        },
        complete: function () {
            // Re-enable button
            $("#updateHostelTypeBtn").prop('disabled', false).text('Update Hostel Type');
        }
    });
});

</script>
@stop

