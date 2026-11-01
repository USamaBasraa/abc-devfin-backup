@extends('layouts.app')
{{-- @dd("working1234"); --}}
@section('title')
    BNU CMS
@stop

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
</head>
<style>
    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713';
        /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }

    .disclaimer {
        color: #4c62ff;
    }

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

    .dt-button {
        background-color: #4454c3;
        color: #4454c3;
        border: none;
        padding: 5px 10px;
        margin: 5px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        margin-top: 5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }

    #clear {
        background-color: #25a4fe;
        border-color: #25a4fe;

    }

    #clear:hover {
        background-color: #1b82cb;
        /* New color on hover */
        border-color: #1b82cb;
        /* Matching border color on hover */
    }

    .edit-btn,
    .delete-btn {
        width: 76px;
    }
    .section-heading {
    position: relative;
    padding: 10px 15px;
    margin-bottom: 25px;
    font-size: 1.05rem;
    color: #0d6efd; /* Bootstrap primary */
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #e6eff9; /* light gray-blue background */
    border-left: 4px solid #0d6efd; /* accent border */
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}


    /* Optional subtle background highlight for contrast between sections */
    .section-heading::before {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #0d6efd;
        border-radius: 2px;
    }

</style>
@section('content')

    {{-- ---------------------- --}}
    <div id="success-flag" class="success-flag"></div>
    <div class="card">
        <div class="card-header" style="width:100%;">
            <form id="policyForm" method="POST" action="">
                @csrf
                <div class="row">
                    <h3>Scholarship Policy:</h3>
                </div>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                         <div class="col-12">
                            <h5 class="fw-bold border-bottom section-heading pb-2 text-primary">Basic Information</h5>
                        </div>
                        <div class="row g-4">
                            <!-- Hidden Field -->
                            <input type="hidden" name="mainId" id="main-Id" value="">
                            <!-- Scholarship Filters -->
                            <div class="col-12">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="scholarship_category" class="form-label fw-semibold">
                                            Scholarship Category
                                        </label>
                                        <select name="scholarship_category" id="scholarship_category" class="form-select form-control"
                                            onchange="updateScholarshipsDropdown()">
                                            <option value="">Select Scholarship Category</option>
                                            <option value="internal">BNU</option>
                                            <option value="external">External</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="scholarship_type" class="form-label fw-semibold">
                                            Scholarship Type
                                        </label>
                                        <select name="scholarship_type" id="scholarship_type" class="form-select form-control"
                                            onchange="updateScholarshipsDropdown()">
                                            <option value="">Select Scholarship Type</option>
                                            <option value="academic">Semester</option>
                                            <option value="hostel">Hostel</option>
                                            <option value="stipend">Stipend</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="scholarship_id" class="form-label fw-semibold">
                                            Scholarship
                                        </label>
                                        <select name="scholarship" id="scholarship_id" class="form-select form-control">
                                            <option value="">Select Scholarships</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sponsor_body" class="form-label fw-semibold">
                                            Sponsor Body
                                        </label>
                                        <select name="sponsor_body" id="sponsor_body" class="form-select form-control">
                                            <option value="">Select Sponsor Body</option>
                                            <option value="Bank of Punjab">Bank of Punjab</option>
                                            <option value="Rah e Falahel">Rah e Falahel</option>
                                            <option value="OTF Special - Hostel & Mess">OTF Special - Hostel & Mess</option>
                                            <option value="Shahaab Rasheed">Shahaab Rasheed</option>
                                            <option value="Sufiyan Yusufi">Sufiyan Yusufi</option>
                                            <option value="Hussain Aslam">Hussain Aslam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="form-group col-sm-4">
                                <label for="school_sel">School</label>
                                <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                                    @php
                                        $allSchools = [];
                                        foreach ($schools as $value) {
                                            $allSchools[] = $value['school_id'];
                                        }
                                    @endphp
                                        <option value="">Select School</option>
                                        <option value="[{{implode(',', $allSchools) }}]">All School</option>
                                    @foreach ($schools as $row)
                                        <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="department">Department</label>
                                <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="course">Degree Level</label>
                                <select name="degree_level" id="degree_level" class="form-control" onchange="setProgramsAccordingly();">
                                    </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="course">Program</label>
                                <select name="program" disabled id="program" class="form-control">
                                </select>
                            </div>
                            <div class="form-group col-sm-6" style='margin-top:30px;'>
                                <label for="type">Roadmap Session</label>
                                <div class="form-check form-check-inline" style="margin-left: 40px;">
                                    <input class="form-check-input" type="radio" name="roadmap" id="roadmap" value="old">
                                    <label class="form-check-label" for="roadmap">Before Fall 2024</label>
                                </div>
                                <div class="form-check form-check-inline" style="margin-left: 40px;">
                                    <input class="form-check-input" type="radio" name="roadmap" id="roadmap" value="new">
                                    <label class="form-check-label" for="roadmap">Fall 2024</label>
                                </div>
                                <span id="roadmap-error" class="error text-danger"></span>
                            </div>
                            <div class="form-group col-sm-6 roadmap_session_sel d-none">
                                <label for="session_sel">Roadmap Session</label>
                                <select name="roadmap_session_sel" id="roadmap_session_sel" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach ($sessions as $key => $value)
                                    <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- ====================== -->
                        <!-- � ELIGIBILITY SECTION -->
                        <!-- ====================== -->
                            <div class="col-12">
                                <h5 class="fw-bold border-bottom section-heading pb-2 text-primary">Eligibility Criteria</h5>
                            </div>
                            {{-- <div class="form-group row mt-4"> --}}
                                <!-- CGPA Range -->
                                {{-- <div class="form-group col-md-4">
                                    <label class="form-label fw-semibold">Define CGPA Range
                                        <small class="text-muted">(Min and Max included)</small>
                                    </label>
                                    <div class="row g-2 align-items-center">
                                        <div class="col-3">
                                            <input type="text" value="CGPA" class="form-control text-center bg-light" readonly>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group">
                                                <span class="input-group-text">Min</span>
                                                <input type="text" name="cgpa_min" id="cgpaFrom" class="form-control"
                                                    maxlength="4" min="1" max="4"
                                                    oninput="validateCGPAInput(event)"
                                                    onkeypress="return /[0-9.]/i.test(event.key)">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group">
                                                <span class="input-group-text">Max</span>
                                                <input type="text" name="cgpa_max" id="cgpaTo" class="form-control"
                                                    maxlength="4" min="1" max="4"
                                                    oninput="validateCGPAInput(event)"
                                                    onkeypress="return /[0-9.]/i.test(event.key)">
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-danger cgpaRange-disclaimer"></small>
                                </div> --}}
                                <!-- Minimum CGPA limit -->
                            <div class="form-group row mt-4 align-items-center">
                                <!-- Minimum CGPA Limit -->
                                <div class="col-md-2 mt-md-0 mt-3">
                                    <label for="cgpa_min_limit" class="form-label fw-semibold invisible">CGPA Threshold</label>
                                    {{-- <div class="input-group"> --}}
                                        <label for="limit" class="form-label fw-semibold">CGPA Threshold</label>
                                        <input type="text" name="cgpa_min_limit" id="cgpa_min_limit" class="form-control"
                                            maxlength="4" min="0" max="4"
                                            placeholder="e.g., 2.5"
                                            oninput="validateCGPAInput(event)"
                                            onkeypress="return /[0-9.]/i.test(event.key)">
                                    {{-- </div> --}}
                                    <small class="text-muted ms-1">Student must maintain at least this CGPA</small>
                                </div>

                                <!-- Alert CGPA Limit -->
                                <div class="col-md-2 mt-md-0 mt-3">
                                    <label for="cgpa_alert_limit" class="form-label fw-semibold invisible">Alert CGPA Limit</label>
                                    {{-- <div class="input-group"> --}}
                                        {{-- <span class="input-group-text text-primary">Alert Limit</span> --}}
                                        <label for="alertlimit" class="form-label fw-semibold">Alert CGPA Limit</label>
                                        <input type="text" name="cgpa_alert_limit" id="cgpa_alert_limit" class="form-control"
                                            maxlength="4" min="0" max="4"
                                            placeholder="e.g., 2.0"
                                            oninput="validateCGPAInput(event)"
                                            onkeypress="return /[0-9.]/i.test(event.key)">
                                    {{-- </div> --}}
                                    <small class="text-muted ms-1">Alert triggered if CGPA drops below this</small>
                                </div>


                                 <div class="col-md-2 mt-3">
                                    <label for="gender" class="form-label fw-semibold">Gender (Optional)</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="transgender">Transgender</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mt-3">
                                    <label for="no_of_scholarships" class="form-label fw-semibold">No. of Scholarships</label>
                                    <input type="number" name="no_of_scholarships" id="no_of_scholarships" class="form-control"
                                        placeholder="e.g. 4">
                                </div>

                                <div class="col-md-2 mt-3">
                                    <label for="age_restriction" class="form-label fw-semibold">Age Restriction (Years)</label>
                                    <input type="number" name="age_restriction" id="age_restriction" class="form-control"
                                        placeholder="e.g. 22">
                                </div>

                                <div class="col-md-2 mt-3">
                                    <label for="attendance_requirement" class="form-label fw-semibold">Attendance (%)</label>
                                    <input type="number" name="attendance_requirement" id="attendance_requirement" class="form-control"
                                        placeholder="e.g. 75">
                                </div>

                                 <div class="col-md-2 mt-3">
                                    <label for="domicile" class="form-label fw-semibold">Domicile</label>
                                    <select name="domicile" id="domicile" class="form-control">
                                        <option value="">Select Domicile</option>
                                        <option value="Punjab">Punjab</option>
                                        <option value="Blochistan">Blochistan</option>
                                    </select>
                                </div>
                            </div>
                        <!-- ====================== -->
                        <!-- � FINANCIAL SECTION -->
                        <!-- ====================== -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="fw-bold border-bottom section-heading pb-2 text-primary">Financial Coverage</h5>
                            </div>
                            <div class="row g-4">
                               <!-- Security Fee -->
                                <div class="col-md-3 mt-3">
                                    <label for="security_fee_percent" class="form-label fw-semibold">Security Fee</label>
                                    <div class="input-group">
                                        <input type="number" name="security_fee_percent" id="security_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="security_fee_amount" id="security_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="security_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                                <!-- Admission Fee -->
                                <div class="col-md-3 mt-3">
                                    <label for="admission_fee_percent" class="form-label fw-semibold">Admission Fee</label>
                                    <div class="input-group">
                                        <input type="number" name="admission_fee_percent" id="admission_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="admission_fee_amount" id="admission_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="admission_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                                <!-- Tuition Fee -->
                                <div class="col-md-3 mt-3">
                                    <label for="tuition_fee_percent" class="form-label fw-semibold">Tuition Fee</label>
                                    <div class="input-group">
                                        <input type="number" name="tuition_fee_percent" id="tuition_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="tuition_fee_amount" id="tuition_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="tuition_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                                <!-- Activity/Medical Fee -->
                                <div class="col-md-3 mt-3">
                                    <label for="activity_fee_percent" class="form-label fw-semibold">Activity/Medical Fee</label>
                                    <div class="input-group">
                                        <input type="number" name="activity_fee_percent" id="activity_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="activity_fee_amount" id="activity_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="activity_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                                <!-- Hostel Coverage -->
                                <div class="col-md-3 mt-3">
                                    <label for="hostel_coverage_percent" class="form-label fw-semibold">Hostel Coverage</label>
                                    <div class="input-group">
                                        <input type="number" name="hostel_coverage_percent" id="hostel_coverage_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="hostel_coverage_amount" id="hostel_coverage_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="hostel_coverage" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                               <!-- Stipend -->
                                <div class="col-md-3 mt-3">
                                    <label for="stipend_percent" class="form-label fw-semibold">Stipend</label>
                                    <div class="input-group">
                                        <input type="number" name="stipend_percent" id="stipend_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number"name="stipend_amount" id="stipend_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="stipend" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>


                               <!-- Additional Credit Hour Fee -->
                                <div class="col-md-3 mt-3">
                                    <label for="additional_credit_hour_fee_percent" class="form-label fw-semibold">Additional Credit Hours</label>
                                    <div class="input-group">
                                        <input type="number" name="additional_credit_hour_fee_percent" id="additional_credit_hour_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="additional_credit_hour_fee_amount" id="additional_credit_hour_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="additional_credit_hour_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>


                                <!-- Other Incidental Fees -->
                                <div class="col-md-3 mt-3">
                                    <label for="other_incidental_fee_percent" class="form-label fw-semibold">Other Incidental Fees</label>
                                    <div class="input-group">
                                        <input type="number" name="other_incidental_fee_percent" id="other_incidental_fee_percent" class="form-control fee-percent-field" placeholder="Enter percentage">
                                        <input type="number" name="other_incidental_fee_amount" id="other_incidental_fee_amount" class="form-control fee-amount-field d-none" placeholder="Enter amount">
                                        <button type="button" class="btn btn-outline-primary toggle-unit" data-group="other_incidental_fee" data-mode="percent">%</button>
                                    </div>
                                    <small class="text-muted unit-label">Currently entering in %</small>
                                </div>

                            </div>
                        </div>

                        <!-- ====================== -->
                        <!-- � RENEWAL SECTION -->
                        <!-- ====================== -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="fw-bold border-bottom section-heading pb-2 text-primary">Renewal & Invoice Details</h5>
                            </div>

                            <div class="col-md-4">
                                <label for="invoice_terms" class="form-label fw-semibold">Invoice Terms</label>
                                <select name="invoice_terms" id="invoice_terms" class="form-control">
                                    <option value="">Select Terms</option>
                                    <option value="Semester Wise">Semester Wise</option>
                                    <option value="Year Wise">Year Wise</option>
                                    <option value="Complete Program">Complete Program</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="contract_tenure" class="form-label fw-semibold">Contract Tenure</label>
                                <select name="contract_tenure" id="contract_tenure" class="form-control">
                                    <option value="">Select Tenure</option>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                    <option value="4">4 Years</option>
                                    <option value="5">5 Years</option>
                                    <option value="6">6 Years</option>
                                    <option value="7">7 Years</option>
                                    <option value="8">8 Years</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                               <label for="renewel_terms" class="form-label fw-semibold">Renewel Terms</label>
                                <select name="renewel_terms" id="renewel_terms" class="form-control">
                                    <option value="">Select Renewel</option>
                                    <option value="auto">Automatically Renewed Based on CGPA</option>
                                    <option value="contractual">As per Contract</option>
                                </select>
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="form_submission_deadline" class="form-label fw-semibold">Form Submission Deadline</label>
                                <input type="text" name="form_submission_deadline" id="form_submission_deadline" class="form-control"
                                    placeholder="e.g., 1st Week of March">
                            </div> --}}

                            {{-- <div class="col-md-12">
                                <label for="renewal_requirements" class="form-label fw-semibold">Renewal Requirements</label>
                                <textarea name="renewal_requirements" id="renewal_requirements" class="form-control" rows="2"
                                    placeholder="e.g., Maintain CGPA 2.5 and 75% attendance"></textarea>
                            </div> --}}
                        </div>

                        <!-- ====================== -->
                        <!-- � ADMIN SECTION -->
                        <!-- ====================== -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="fw-bold border-bottom section-heading pb-2 text-primary">Administrative Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label for="focal_person" class="form-label fw-semibold">Focal Person</label>
                                <input type="text" name="focal_person" id="focal_person" class="form-control"
                                    placeholder="e.g., Madam Registrar">
                            </div>

                            <div class="col-md-4">
                                <label for="payment_release_tenure" class="form-label fw-semibold">Payment Release Tenure</label>
                                <input type="text" name="payment_release_tenure" id="payment_release_tenure" class="form-control"
                                    placeholder="e.g., Within 10 working days after invoice verification">
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="approval_required" class="form-label fw-semibold">Approval Required</label>
                                <select name="approval_required" id="approval_required" class="form-select">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div> --}}

                            <div class="col-md-12">
                                <label for="remarks" class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="2"
                                    placeholder="Additional comments or conditions"></textarea>
                            </div>
                        </div>





                        <div class="col-md-3  mt-5">

                        </div>
                        <!-- Effective Dates (Hidden by default) -->
                        <div class="col-md-3 d-none">
                            <label for="eff-from" class="form-label fw-semibold">Effective From</label>
                            <input type="date" name="effective_from" id="eff-from" class="form-control">
                        </div>

                        <div class="col-md-3 d-none">
                            <label for="eff-to" class="form-label fw-semibold">Effective To</label>
                            <input type="date" name="effective_to" id="eff-to" class="form-control">
                        </div>
                    </div>
                    </div>
                </div>

                <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                    {{-- <button type="button" style="width: 150px;margin-left:50px;" id="filterButton"
                        class="btn btn-primary">Apply Filter</button> --}}
                    <button type="button" style="width: 100px;margin-left:50px;" id="clear"
                        class="btn btn-secondary" onclick="refreshFields()">Clear</button>
                </div>
                {{-- <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing,
                    please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div> --}}
                <div id="processing_message" class="alert alert-info" style="display:none;">Processing...</div>
                <div id="success_message" class="alert alert-success" style="display:none;"></div>
                <div id="error_message" class="alert alert-danger" style="display:none;"></div>
                {{-- <div id="success_message" class="alert alert-success" style="display: none;"></div> --}}
            </form>


        </div>
        <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display">
                            <thead>
                                <tr>
                                    <th scope="col">Scholarship Name</th>          <!-- scholarship_name -->
                                    <th scope="col">Sponsor Body</th>              <!-- fsp_sponsor_body -->
                                    {{-- <th scope="col">School</th>                    <!-- fsp_school_id -->
                                    <th scope="col">Department</th>                <!-- fsp_department_id -->
                                    <th scope="col">Program</th>                   <!-- fsp_program_id -->
                                    <th scope="col">Degree Level</th>              <!-- fsp_degree_level --> --}}
                                    <th scope="col">Gender</th>                    <!-- fsp_gender -->
                                    <th scope="col">CGPA (Min – Alert)</th>        <!-- fsp_cgpa_min_limit / fsp_cgpa_alert_limit -->
                                    <th scope="col">Tuition Fee Coverage</th>      <!-- fsp_tuition_fee_percent / fsp_tuition_fee_amount -->
                                    <th scope="col">Stipend</th>                   <!-- fsp_stipend_percent / fsp_stipend_amount -->
                                    <th scope="col">Invoice Terms</th>             <!-- fsp_invoice_terms -->
                                    <th scope="col">Contract Tenure</th>           <!-- fsp_contract_tenure -->
                                    <th scope="col">Renewal Terms</th>             <!-- fsp_renewal_terms -->
                                    <th scope="col">Focal Person</th>              <!-- fsp_focal_person -->
                                    <th scope="col">Remarks</th>                   <!-- fsp_remarks -->
                                    <th scope="col">Action</th>                    <!-- Edit/Delete -->
                                </tr>
                            </thead>

                        </table>

                    </div>
                </div>
                {{-- <div class="d-flex justify-content-end mt-2">
                    <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                        <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk
                            Delete</button>
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="card" style="margin-top:10px;">
        </div>
        <div style="clear:both;"></div>
    @stop
    @section('javascript')
<script>
    function validateCGPAInput(event) {
        let value = event.target.value;
        if (value === '') return;
        let num = parseFloat(value);
        if (isNaN(num) || num < 0 || num > 4) {
            event.target.value = '';
            alert('Please enter a valid CGPA between 0.00 and 4.00');
        }
    }
    let scholarships = @json($scholarships);

    function LoadScholarships() {
        // let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true,
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
            order: [1, 'desc'],
            ajax: {
                dataType: 'json',
                method: 'post',
                url: "/api/get_scholarshipPolicy_list",
                // data: function(d) {
                //     return $.extend({}, d, filterParams);
                // }
            },
          columns: [
                { data: "scholarship_name" }, // Column 0 - Scholarship
                { data: "fsp_sponsor_body" }, // Column 1 - Sponsor Body
                // { data: "fsp_school_id" },    // Column 2 - School
                // { data: "fsp_department_id" },// Column 3 - Department
                // { data: "fsp_program_id" },   // Column 4 - Program
                // { data: "fsp_degree_level" }, // Column 5 - Degree Level
                { data: "fsp_gender" },       // Column 6 - Gender
                { 
                    data: null,               // Column 7 - CGPA Range
                    render: function(data, type, row) {
                        return row.fsp_cgpa_min_limit + " - " + row.fsp_cgpa_alert_limit;
                    }
                },
                { 
                    data: null,               // Column 8 - Tuition Fee Coverage (shows % or amount)
                    render: function(data, type, row) {
                        if (row.fsp_tuition_fee_percent)
                            return row.fsp_tuition_fee_percent + "%";
                        else if (row.fsp_tuition_fee_amount)
                            return "Rs. " + row.fsp_tuition_fee_amount;
                        else
                            return "-";
                    }
                },
                { 
                    data: null,               // Column 9 - Stipend (shows % or amount)
                    render: function(data, type, row) {
                        if (row.fsp_stipend_percent)
                            return row.fsp_stipend_percent + "%";
                        else if (row.fsp_stipend_amount)
                            return "Rs. " + row.fsp_stipend_amount;
                        else
                            return "-";
                    }
                },
                { data: "fsp_invoice_terms" },    // Column 10 - Invoice Terms
                { data: "fsp_contract_tenure" },  // Column 11 - Contract Tenure
                { data: "fsp_renewal_terms" },    // Column 12 - Renewal Terms
                { data: "fsp_focal_person" },     // Column 13 - Focal Person
              
                { data: "fsp_remarks" },          // Column 15 - Remarks

               {
                    data: null, // Column for Edit/Delete buttons
                    render: function(data, type, row) {
                        const rowData = encodeURIComponent(JSON.stringify(row)); // safely encode the full row

                        return `
                                <div class="d-flex flex-column align-items-start">
                                    <button class="btn btn-primary btn-sm edit-btn mb-2"
                                        onclick="editRow('${rowData}')">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        onclick="deleteRows(${row.fsp_id})">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            `;



                    }
                }

                // { 
                //     data: null,                   // Column 17 - Select Checkbox
                //     render: function(data, type, row) {
                //         return `<input type="checkbox" class="checkbox select-data" value="${row.fsp_id}">`;
                //     }
                // }
            ],
            // columnDefs: [{
            //         targets: 8,
            //         orderable: false,
            //         searchable: false
            //     },
            //     {
            //         targets: 9,
            //         orderable: false,
            //         searchable: false
            //     }
            // ],
            autoWidth: false,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Scholarship Policy'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Scholarship Policy',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Scholarship Policy'
                },
                // {
                //     text: 'Export All Data (PDF)',
                //     action: function(e, dt, button, config) {
                //         pdfExportFunction(dt, config);
                //     }
                // }
            ]
        });
    }

    function updateScholarshipsDropdown() {
        let category = $('#scholarship_category').val();
        let type = $('#scholarship_type').val();
        let dropdown = $('#scholarship_id');

        dropdown.empty().append('<option value="">Select Scholarships</option>');

        let list = [];

        if (category && type) {
            // Both selected → match both
            list = scholarships.filter(s =>
                s.scholarship_category === category && s.scholarship_desc === type
            );
        } else if (category) {
            // Only category selected
            list = scholarships.filter(s =>
                s.scholarship_category === category
            );
        } else if (type) {
            // Only type selected
            list = scholarships.filter(s =>
                s.scholarship_desc === type
            );
        } else {
            // None selected → show all (optional)
            list = scholarships;
        }

        list.forEach(s => {
            dropdown.append(`<option value="${s.scholarship_id}">${s.scholarship_name}</option>`);
        });
    }
    function getRequest(endPoint, successBlock, failBlock) {
        $.ajax({
            url: "{{ url('/') }}" + endPoint,
            type: "GET",
            dataType: "json",
            success: successBlock
        }).fail(failBlock);
    }

    function getPrograms() {
        const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
        const dept_id = $('#department').val();
        // console.log(session_id, dept_id);
        const url =
        `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
        getRequest(url, function(res) {
            if (res.status) {
                console.log(res.data);
                let allProg = [];
                let degreeLevels = [];
                $.each(res.data, function(key, value) {
                    allProg.push(value.prog_school_id + '~~' + value.prog_dept_id + '~~' + value
                        .prog_id);
                    degreeLevels.push(value.prog_degree_level);
                });
                populateDegreeLevel(degreeLevels);
                if (dept_id == '') {
                    let html = `<option value=''>Select Program</option>`;
                    $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html +=
                            `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "program" select
                } else {
                    let html = `<option value=''>Select Program</option>`;
                    html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                    $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html +=
                            `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "course" select
                }
                ////////
                action_onAllDepartmentSelection(dept_id);
                ///////

            } else {
                console.error('Error fetching programs:', res.message); // Log error message
                // Optionally display an error message to the user
            }

        }, function(res) {
            alert('Some Error found!');
        });
        // console.log(url);
    }

    function getDepartments() {
        var school_id = $('#school_sel').val();
        getRequest('/api/getDepartmentsFeeStructureForm/get?school_id=' + school_id, function(res) {
            console.log(res);
            if (res.status) {
                let allDep = [];
                $.each(res.data, function(key, value) {
                    allDep.push(value.dept_school_id + '~~' + value.dept_id);
                });
                if (school_id == '') {
                    let html = `<option value=''>Select Department</option>`;
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.dept_id + '" >' + value.dept_name +
                            '</option>';
                    });
                    $('select[name="department"]').html(html);
                } else {
                    let html = `<option value=''>Select Department</option>`;
                    html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                    $.each(res.data, function(key, value) {
                        html += '<option value="' + value.dept_id + '" >' + value.dept_name +
                            '</option>';
                    });
                    $('select[name="department"]').html(html);
                }
                /////////
                let htmlProg = `<option value=''>Select Program</option>`;
                $('select[name="program"]').html(htmlProg);
                htmlProg = `<option value=''>Select Degree Level</option>`;
                $('select[name="degree_level"]').html(htmlProg);
                action_onAllSchoolSelection(school_id);
                ////
            }
        }, function(res) {
            alert('Some Error found!');
        });
    }
    function editRow(rowData) {
        const row = JSON.parse(decodeURIComponent(rowData));

        // Now you can access everything:
        console.log(row); 

        $('#main-Id').val(row.fsp_id);
        $('#scholarship_category').val(row.scholarship_category).trigger('change');
        $('#scholarship_type').val(row.scholarship_desc).trigger('change');
        $('#scholarship_id').val(row.fsp_scholarship_id);
        $('#sponsor_body').val(row.fsp_sponsor_body);
        if (!row.fsp_school_id) {
            let firstVal = $('#school_sel option:eq(1)').val(); // 2nd option (index 1)
            $('#school_sel').val(firstVal).trigger('change');
        } else {
            $('#school_sel').val(row.fsp_school_id);
        }

        getDepartments(); //this will set department dropdown menu
        setTimeout(function() {
            $('#department').val(row.fsp_department_id);
            getPrograms(); //this will set programs dropdown menu and degreelevel dropdown menu
            setTimeout(function() {
                $('#degree_level').val(row.fsp_degree_level);
                setProgramsAccordingly();
                setTimeout(function() {
                    $('#program').val(row.fsp_school_id + '~~' + row.fsp_department_id + '~~' + row.fsp_program_id);
                }, 500); // Adjust the delay as needed
            }, 500); // Adjust the delay as needed
        }, 500); // Adjust the delay as needed

        $('#cgpa_min_limit').val(row.fsp_cgpa_min_limit);
        $('#cgpa_alert_limit').val(row.fsp_cgpa_alert_limit);
        $('#gender').val(row.fsp_gender);
        $('#no_of_scholarships').val(row.fsp_no_of_scholarships);
        $('#age_restriction').val(row.fsp_age_restriction);
        $('#attendance_requirement').val(row.fsp_attendance_requirement);
        $('#domicile').val(row.fsp_domicile);
        $('input[name="roadmap"][value="' + row.fsp_roadmap + '"]').prop('checked', true).trigger('change');

        // Helper function to handle each fee section
        function setFeeField(group, percentValue, amountValue) {
            const percentField = $(`#${group}_percent`);
            const amountField = $(`#${group}_amount`);
            const toggleBtn = $(`.toggle-unit[data-group="${group}"]`);
            const label = percentField.closest('.col-md-3').find('.unit-label');

            if (percentValue && percentValue !== '0' && percentValue !== null) {
                // Set percent value, hide amount
                percentField.val(percentValue);
                amountField.val('').addClass('d-none');
                percentField.removeClass('d-none');
                toggleBtn.text('%').data('mode', 'percent');
                label.text('Currently entering in %');
            } else if (amountValue && amountValue !== '0' && amountValue !== null) {
                // Set amount value, hide percent
                amountField.val(amountValue);
                percentField.val('').addClass('d-none');
                amountField.removeClass('d-none');
                toggleBtn.text('Rs.').data('mode', 'amount');
                label.text('Currently entering in Rs.');
            } else {
                // Default state
                percentField.val('').removeClass('d-none');
                amountField.val('').addClass('d-none');
                toggleBtn.text('%').data('mode', 'percent');
                label.text('Currently entering in %');
            }
        }

        // Apply helper for all fee groups
        setFeeField('security_fee', row.fsp_security_fee_percent, row.fsp_security_fee_amount);
        setFeeField('admission_fee', row.fsp_admission_fee_percent, row.fsp_admission_fee_amount);
        setFeeField('tuition_fee', row.fsp_tuition_fee_percent, row.fsp_tuition_fee_amount);
        setFeeField('activity_fee', row.fsp_activity_fee_percent, row.fsp_activity_fee_amount);
        setFeeField('hostel_coverage', row.fsp_hostel_coverage_percent, row.fsp_hostel_coverage_amount);
        setFeeField('stipend', row.fsp_stipend_percent, row.fsp_stipend_amount);
        setFeeField('additional_credit_hour_fee', row.fsp_additional_credit_hour_percent, row.fsp_additional_credit_hour_amount);
        setFeeField('other_incidental_fee', row.fsp_other_incidental_fee_percent, row.fsp_other_incidental_fee_amount);

        // You can now continue setting the remaining (non-fee) fields normally...
        $('#invoice_terms').val(row.fsp_invoice_terms);
        $('#contract_tenure').val(row.fsp_contract_tenure);
        $('#renewel_terms').val(row.fsp_renewal_terms);
        $('#focal_person').val(row.fsp_focal_person);
        $('#payment_release_tenure').val(row.fsp_payment_release_tenure);
        $('#remarks').val(row.fsp_remarks);

        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    }


    function deleteRows(mainId) {
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/api/delete_scholarshipPolicy",
                data: {
                    mainId: mainId
                },
                success: function(response) {
                    $('.select-data').prop('checked', false);
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadScholarships();

                },
                error: function(error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }

    function refreshFields() {
        // Basic identifiers and selectors
        $('#main-Id').val('');
        $('#scholarship_category').val('').trigger('change');
        $('#scholarship_type').val('').trigger('change');
        $('#scholarship_id').val('');
        $('#sponsor_body').val('');
        $('#school_sel').val('');
        $('#department').val('');
        $('#degree_level').val('');
        $('#program').val('');
        $('#cgpa_min_limit').val('');
        $('#cgpa_alert_limit').val('');
        $('#gender').val('');
        $('#no_of_scholarships').val('');
        $('#age_restriction').val('');
        $('#attendance_requirement').val('');
        $('#domicile').val('');

        // Reset roadmap radio buttons
        $('input[name="roadmap"]').prop('checked', false);

        // Reset fee fields (percent/amount toggles)
        const feeGroups = [
            'security_fee',
            'admission_fee',
            'tuition_fee',
            'activity_fee',
            'hostel_coverage',
            'stipend',
            'additional_credit_hour_fee',
            'other_incidental_fee'
        ];

        feeGroups.forEach(group => {
            const percentField = $(`#${group}_percent`);
            const amountField = $(`#${group}_amount`);
            const toggleBtn = $(`.toggle-unit[data-group="${group}"]`);
            const label = percentField.closest('.col-md-3').find('.unit-label');

            // Reset to default: percent mode
            percentField.val('').removeClass('d-none');
            amountField.val('').addClass('d-none');
            toggleBtn.text('%').data('mode', 'percent');
            label.text('Currently entering in %');
        });

        // Reset remaining non-fee fields
        $('#invoice_terms').val('');
        $('#contract_tenure').val('');
        $('#renewel_terms').val('');
        $('#focal_person').val('');
        $('#payment_release_tenure').val('');
        $('#remarks').val('');

        // Optionally scroll to top (for UX consistency)
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }



    function validatePercentageInput(event) {
        const input = event.target;
        if (input.value !== '') {
            const value = parseInt(input.value, 10);
            if (value < 1 || value > 100) {
                $('.percent-disclaimer').text('Enter a value between 1 and 100');
                $('.percent-disclaimer').css('color', 'red');
                // input.setCustomValidity('Please enter a value between 1 and 100');
            } else {
                $('.percent-disclaimer').text('');
            }
        }
    }

    // function validateCGPAInput(event) {
    //     const input = event.target;
    //     if (input.value !== '') {
    //         const value = parseFloat(input.value);
    //         console.log(value);
    //         if (value < 1 || value > 4) {
    //             $('.cgpaRange-disclaimer').text('Enter CGPA between 1 and 4');
    //             $('.cgpaRange-disclaimer').css('color', 'red');
    //             // input.setCustomValidity('Please enter a value between 1 and 100');
    //         } else {
    //             $('.cgpaRange-disclaimer').text('');
    //         }
    //     }
    // }

    function action_onAllSchoolSelection(school_id) {
        if (school_id != '') {
            school_ids_array = JSON.parse(school_id);
            if (Array.isArray(school_ids_array)) {
                $('#department').prop('disabled', true);
                $('#program').prop('disabled', true);
                // $('#degree_level').prop('disabled', true);
                setTimeout(function() {
                    $('#department').val($('#department option:nth-child(2)').val());
                    getPrograms();
                    setTimeout(function() {
                        $('#program').val($('#program option:nth-child(2)').val());
                    }, 500); // Adjust the delay as needed
                }, 500); // Adjust the delay as needed
            } else {
                $('#department').prop('disabled', false);
                $('#program').prop('disabled', false);
                // $('#degree_level').prop('disabled', false);
            }
        } else {
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
            // $('#degree_level').prop('disabled', false);
        }

    }

    function action_onAllDepartmentSelection(dept_id) {
        if (dept_id != '') {
            dept_ids_array = JSON.parse(dept_id);
            if (Array.isArray(dept_ids_array)) {
                $('#program').prop('disabled', true);
                // $('#degree_level').prop('disabled', true);
                $('#program').val($('#program option:nth-child(2)').val());
            } else {
                $('#program').prop('disabled', false);
                // $('#degree_level').prop('disabled', false);
            }
        } else {
            $('#program').prop('disabled', false);
            // $('#degree_level').prop('disabled', false);
        }
        console.log($('#program').val());

    }

    function populateDegreeLevel(degreeLevels) {
        console.log(degreeLevels);
        let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
        let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
        console.log(distinctDegreeLevels);
        html = '<option value="" >Select Degree Level</option>';
        $.each(distinctDegreeLevels, function(key, value) {
            html += '<option value="' + value + '" >' + value + '</option>';
        });
        $('select[name="degree_level"]').html(html);
    }

    function setProgramsAccordingly() {
        let session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
        let dept_id = $('#department').val();
        let degreeLevel = $('#degree_level').val();
        let degreeLevelwithArrow = ((degreeLevel != '') ? '&#x2192;' + degreeLevel : '');
        const url =
        `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&degreeLevel=${degreeLevel}`; // Template literals for URL
        getRequest(url, function(res) {
            if (res.status) {
                console.log(res.data);
                let allProg = [];
                $.each(res.data, function(key, value) {
                    allProg.push(value.prog_school_id + '~~' + value.prog_dept_id + '~~' + value
                        .prog_id);
                });
                // let html = `<option value=''>Select Program</option>`;
                html = `<option value='${JSON.stringify(allProg)}'>All Program ` + degreeLevelwithArrow +
                    `</option>`;
                $.each(res.data, function(key, value) {
                    // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                    html +=
                        `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                });
                $('select[name="program"]').html(html); // Update "course" select

                $('#program option').filter(function() {
                    return $(this).text() === 'All Program';
                }).prop('selected', true);
                ////////
                // action_onAllDepartmentSelection(dept_id);
                ///////
                console.log($('#program').val());

            } else {
                console.error('Error fetching programs:', res.message); // Log error message
                // Optionally display an error message to the user
            }

        }, function(res) {
            alert('Some Error found!');
        });
    }
    // function getFilterParams() {
    //     const validity_type = $('.validity-type').val();
    //     const year_sel = $('#year_sel').val();
    //     const session_sel = $('#session_sel').val();
    //     const school_sel = $('#school_sel').val();
    //     const scholarship_id = $('#scholarship_id').val();
    //     const percent = $('#percent').val();
    //     const department = $('#department').val();
    //     const degree_level = $('#degree_level').val();
    //     const roadmap = $('#roadmap:checked').val();
    //     const program = $('#program').val();
    //     const cgpaFrom = $('#cgpaFrom').val();
    //     const cgpaTo = $('#cgpaTo').val();

    //     let filterParams = {
    //         validity_type: validity_type,
    //         year_sel: year_sel,
    //         session_sel: session_sel,
    //         school_sel: school_sel,
    //         scholarship_id: scholarship_id,
    //         percent: percent,
    //         department: department,
    //         degree_level: degree_level,
    //         roadmap: roadmap,
    //         program: program,
    //         cgpaFrom: cgpaFrom,
    //         cgpaTo: cgpaTo
    //     };

    //     return filterParams;
    // }
    // function setCgpaValue(cgpa, relation){
    //     console.log(cgpa, relation)
    //     cgpa = parseFloat(cgpa);
    //     if(relation == 'greater_than' || relation == 'less_than'){
    //         return cgpa;
    //     }else if(relation == 'greater_or_equal'){
    //         cgpa = parseFloat((cgpa - 0.01).toFixed(2));
    //         return cgpa;
    //     }else if(relation == 'less_or_equal'){
    //         cgpa = parseFloat((cgpa + 0.01).toFixed(2));
    //         return cgpa;
    //     }
    // }

    // function setCgpaFromValue(cgpa,relation){
    //     cgpa = parseFloat(cgpa);
    //     cgpa = parseFloat((cgpa - 0.01).toFixed(2));
    //     return cgpa;
    // }
    // function setCgpaToValue(cgpa,relation){
    //     cgpa = parseFloat(cgpa);
    //     cgpa = parseFloat((cgpa + 0.01).toFixed(2));
    //     return cgpa;
    // }

    document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("policyForm");
        var formData = new FormData(form);
        formData.append('school', $('#school_sel').val());
        formData.append('department', $('#department').val());
        formData.append('program', $('#program').val());

        $("#processing_message").show();
        fetch('add-scholarship-policy', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(response => response.json())

            .then(data => {
                if (data.success) {
                    refreshFields();
                    $("#processing_message").hide();
                    $("#error_message").text('').hide();
                    $('#success_message').html(data.msg);
                    $('#success_message').fadeIn('slow');
                    $('#success_message').fadeOut(5000);
                    LoadScholarships();
                } else {
                    console.log(data);
                    var message = "";
                    if ('msg' in data) {
                        message = data.msg;
                    } else if ('errors' in data) {
                        var errors = data.errors;
                        $.each(errors, function(i, obj) {
                            message += "<p>" + obj[0] + "</p>";
                        });
                    }
                    $("#processing_message").hide();
                    $('#success_message').text('').hide();
                    $('#error_message').html(message);
                    $('#error_message').fadeIn('slow');
                    $('#error_message').fadeOut(5000);
                }
            })
            .catch(error => {
                $("#processing_message").hide();
                console.error('There was an error!', error);
            });
    });

    $(document).ready(function() {
        LoadScholarships();
        // $('#feeType').change(function() {
        //     if ($(this).val() == 'Credit_hours') {
        //         $('.credFeeDiv').removeClass('d-none');
        //     } else {
        //         $('.credFeeDiv').addClass('d-none');
        //         $('#credFee').val('');

        //     }
        // });
        // $(document).on('change', '#select-all', function() {
        //     if ($(this).prop("checked") == true) {
        //         $('.select-data').prop('checked', true);
        //         console.log($('.select-data').val());
        //         // $('#bulk-Del').prop('disabled', false);

        //     } else {
        //         $('.select-data').prop('checked', false);
        //         console.log($('.select-data').val());
        //         // $('#bulk-Del').prop('disabled', true);
        //     }
        // });
        // $(document).on('click', '#bulk-Del', function() {
        //     var fee_ids = new Array();
        //     $.each($("input[name='fee_ids[]']:checked"), function() {
        //         fee_ids.push($(this).val());
        //     });
        //     if (fee_ids.length == 0) {
        //         alert("Please select atleast one row to delete.");
        //     } else {
        //         deleteRows(fee_ids);
        //     }
        // });
        // $(document).on('change', '.validity-type', function() {
        //     selectedValue = $(this).attr('id');
        //     if (selectedValue == 'session-wise') {
        //         $('.session_sel_div').removeClass('d-none'); //show session selection div
        //         $('.validity-type').val('session'); //setting validityType value
        //         $('.year_sel_div').addClass('d-none'); //hiding thd year fields
        //         $('#year_sel').val(''); //emptying the from date

        //     } else if (selectedValue == 'annual-wise') {
        //         $('.year_sel_div').removeClass('d-none'); //show validity dates field
        //         $('.validity-type').val('annual'); //setting validityType value
        //         $('.session_sel_div').addClass('d-none'); //hide session selection div

        //         $('#session_sel').val(''); //emptying the sesstion selection
        //     }
        // });
        $(document).on('click', '#filterButton', function() {
            LoadScholarships();
        });
        // Input restrictions
        $(document).on('input', '.fee-percent-field', function () {
            let val = parseFloat($(this).val());
            if (val < 0) $(this).val(0);
            if (val > 100) $(this).val(100);
        });

        $(document).on('input', '.fee-amount-field', function () {
            let val = parseFloat($(this).val());
            if (val < 0) $(this).val(0);
        });
        $('.toggle-unit').on('click', function () {
            const $btn = $(this);
            const group = $btn.data('group');

            const $percentField = $('#' + group + '_percent');
            const $amountField = $('#' + group + '_amount');
            const $label = $btn.closest('.col-md-3').find('.unit-label');

            // Toggle between modes
            if ($btn.data('mode') === 'percent') {
                // Switch to amount mode
                $btn.data('mode', 'amount').text('Rs');
                $label.text('Currently entering in Amount (Rs)');

                // Show amount field, hide percent field
                $percentField.addClass('d-none').val('');
                $amountField.removeClass('d-none').val('');

            } else {
                // Switch to percent mode
                $btn.data('mode', 'percent').text('%');
                $label.text('Currently entering in %');

                // Show percent field, hide amount field
                $amountField.addClass('d-none').val('');
                $percentField.removeClass('d-none').val('');
            }
        });

    });


</script>

    @stop
