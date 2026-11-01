@extends('layouts.app')

@section('title')
BNU CMS - Manual Scholarship Challan
@stop

@section('content')
<style>
    .manual-card { border: 0; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .section-title { color: #4454c3; border-bottom: 2px solid #4454c3; padding-bottom: 8px; }
    .student-box { background: #f7f8fd; border: 1px solid #e3e6f5; border-radius: 8px; }
    .amount-card { background: #f8fbff; border: 1px solid #dce8f5; border-radius: 8px; }
    .required-star { color: #dc3545; }
    .field-error { display: block; font-size: 12px; margin-top: 3px; }
    .scholarship-option { border: 1px solid #e1e5ef; border-radius: 6px; padding: 9px; height: 100%; }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #e8ecf5;
    }
    .btn-create-challan {
        min-width: 220px;
        padding: 12px 22px;
        font-weight: 600;
        letter-spacing: .2px;
        border: 0;
        border-radius: 8px;
        color: #fff;
        background: linear-gradient(135deg, #3b49b5 0%, #4454c3 55%, #5a69d6 100%);
        box-shadow: 0 8px 18px rgba(68, 84, 195, .28);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .btn-create-challan:hover,
    .btn-create-challan:focus {
        color: #fff;
        filter: brightness(1.04);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(68, 84, 195, .34);
    }
    .btn-create-challan:disabled {
        opacity: .75;
        transform: none;
        box-shadow: none;
    }
    .btn-create-challan i { margin-right: 8px; }
    .btn-clear-form {
        min-width: 120px;
        padding: 11px 18px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #d7dbe8;
        color: #5c6478;
        background: #fff;
    }
    .btn-clear-form:hover {
        border-color: #4454c3;
        color: #4454c3;
        background: #f7f8fd;
    }

    .generated-challan-card {
        margin: 18px 0 8px;
        border: 1px solid #d7e7d9;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 24px rgba(26, 95, 52, .08);
    }
    .generated-challan-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        background: linear-gradient(135deg, #1f7a45 0%, #28a745 100%);
        color: #fff;
    }
    .generated-challan-card__header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .generated-challan-card__icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.18);
        flex-shrink: 0;
    }
    .generated-challan-card__title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.2;
    }
    .generated-challan-card__subtitle {
        margin: 2px 0 0;
        font-size: 12px;
        opacity: .92;
    }
    .generated-challan-card__badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        background: rgba(255,255,255,.18);
        white-space: nowrap;
    }
    .generated-challan-card__body { padding: 16px 18px 18px; }
    .generated-challan-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 18px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #d9e5db;
        font-size: 13px;
        color: #4a5568;
    }
    .generated-challan-meta strong { color: #1f2937; }
    .generated-challan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
    }
    .generated-challan-item {
        background: #f6faf7;
        border: 1px solid #e2efe5;
        border-radius: 8px;
        padding: 12px 14px;
    }
    .generated-challan-item--total {
        background: #eef8f1;
        border-color: #cfe8d6;
    }
    .generated-challan-item small {
        display: block;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 4px;
    }
    .generated-challan-item strong {
        display: block;
        color: #163824;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.2;
    }
    .generated-challan-item--total strong { color: #1f7a45; }
</style>

<div class="container-fluid">
    <div class="card manual-card">
        <div class="card-body">
            <h3 class="mb-4">Manual Scholarship Challan</h3>

            <form id="manualScholarshipForm" autocomplete="off" novalidate>
                @csrf

                <div class="row align-items-end">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Enrollment No# or Applicant ID <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="studentEnrollment" placeholder="Enter Enrollment No / Applicant ID">
                        <span class="text-danger field-error" id="studentEnrollment-error"></span>
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" class="btn btn-primary" id="searchStudentBtn">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <div id="studentInfoCard" class="student-box p-3 mb-4 d-none">
                    <div class="row">
                        <div class="col-md-3"><small>Student Name</small><div class="font-weight-bold" id="showStudentName"></div></div>
                        <div class="col-md-3"><small>Enrollment / Applicant ID</small><div class="font-weight-bold" id="showEnrollment"></div></div>
                        <div class="col-md-3"><small>School</small><div class="font-weight-bold" id="showSchool"></div></div>
                        <div class="col-md-3"><small>Department</small><div class="font-weight-bold" id="showDepartment"></div></div>
                        <div class="col-md-3 mt-3"><small>Program</small><div class="font-weight-bold" id="showProgram"></div></div>
                        <div class="col-md-3 mt-3"><small>Email</small><div class="font-weight-bold" id="showEmail"></div></div>
                        <div class="col-md-3 mt-3"><small>Mobile</small><div class="font-weight-bold" id="showMobile"></div></div>
                        <div class="col-md-3 mt-3"><small>Current Status</small><div class="font-weight-bold" id="showStatus"></div></div>
                    </div>
                </div>

                <input type="hidden" name="student_input" id="student_input">
                <input type="hidden" name="enrollment_no" id="enrollment_no">
                <input type="hidden" name="st_aai_id" id="st_aai_id">
                <input type="hidden" name="st_school_id" id="st_school_id">
                <input type="hidden" name="st_dept_id" id="st_dept_id">
                <input type="hidden" name="st_prog_id" id="st_prog_id">
                <input type="hidden" name="student_name" id="student_name">
                <input type="hidden" name="father_name" id="father_name">
                <input type="hidden" name="guardian_cnic" id="guardian_cnic">

                <h5 class="section-title">Challan Information</h5>
                <div class="row mt-3">
                    <div class="form-group col-md-4">
                        <label>Session <span class="required-star">*</span></label>
                        <select class="form-control" name="session_id" id="session_id">
                            <option value="">Select Session</option>
                            @foreach($sessions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger field-error" id="session_id-error"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Fee Type <span class="required-star">*</span></label>
                        <select class="form-control" name="fee_type" id="fee_type">
                            <option value="">Select Fee Type</option>
                            <option value="Hostel_Fee">Hostel Fee</option>
                            <option value="Hostel_other_utilities">Hostel Other Utilities</option>
                        </select>
                        <span class="text-danger field-error" id="fee_type-error"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Student Status</label>
                        <select class="form-control" name="student_status" id="student_status">
                            <option value="">Select Student Status</option>
                            <option value="Active">Active</option>
                            <option value="Expected-Graduate">Expected Graduate</option>
                        </select>
                        <span class="text-danger field-error" id="student_status-error"></span>
                    </div>
                    {{-- Hostel fee types: Student Type is not shown (utilities use Hostel Student Type). --}}
                    <div class="form-group col-md-4 d-none" id="studentTypeGroup">
                        <label>Student Type <span class="required-star">*</span></label>
                        <select class="form-control" name="student_type" id="student_type">
                            <option value="">Select Student Type</option>
                            <option value="Existing" selected>Existing</option>
                            <option value="New Admission FALL">New Admission FALL</option>
                            <option value="New Admission SPRING">New Admission SPRING</option>
                            <option value="New Admission SUMMER">New Admission SUMMER</option>
                        </select>
                        <span class="text-danger field-error" id="student_type-error"></span>
                    </div>
                </div>

                <div id="hostelUtilitiesSection" class="d-none">
                    <h5 class="section-title mt-3">Hostel Other Utilities</h5>
                    <div class="row mt-3">
                        <div class="form-group col-md-4">
                            <label>Session Type</label>
                            <select class="form-control" name="sess_type" id="sess_type">
                                <option value="">Select Type</option>
                                <option value="Regular">Regular</option>
                                <option value="Non-Regular">Non-Regular</option>
                                <option value="Regular_outside">Regular Safari Campus</option>
                                <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                                <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                                <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                            </select>
                            <span class="text-danger field-error" id="sess_type-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Hostel Student Type</label>
                            <select class="form-control" name="hostel_student_type" id="hostel_student_type">
                                <option value="">Select Type</option>
                                <option value="Existing">Existing</option>
                                <option value="New_Admission">New Applicant</option>
                            </select>
                            <span class="text-danger field-error" id="hostel_student_type-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="hostel_days_utilities">No. of Days</label>
                            <input type="number" min="0" step="1" value="0"
                                   class="form-control integer-only"
                                   name="hostel_days" id="hostel_days_utilities"
                                   inputmode="numeric" pattern="[0-9]*"
                                   placeholder="Enter No of Days">
                            <span class="text-danger field-error" id="hostel_days_utilities-error"></span>
                        </div>
                    </div>
                    <label class="font-weight-bold">Utility amounts <span class="required-star">*</span></label>
                    <small class="d-block text-muted mb-2">Check the utility and enter its amount. Challan will be generated against these amounts.</small>
                    <div class="row mt-2">
                        <div class="form-group col-md-4">
                            <label for="hostel_refrigerator_fee">Room Refrigerator</label>
                            <div class="input-group">
                                <input type="number" min="0" step="0.01" value="0"
                                       class="form-control hostel-utility-amount"
                                       name="hostel_refrigerator_fee" id="hostel_refrigerator_fee">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox" class="hostel-utility"
                                               name="refrigerator_checkbox" value="on" id="refrigerator_checkbox"
                                               title="Include Refrigerator">
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger field-error" id="hostel_refrigerator_fee-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="hostel_conditioner_fee">Air Conditioner</label>
                            <div class="input-group">
                                <input type="number" min="0" step="0.01" value="0"
                                       class="form-control hostel-utility-amount"
                                       name="hostel_conditioner_fee" id="hostel_conditioner_fee">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox" class="hostel-utility"
                                               name="conditioner_checkbox" value="on" id="conditioner_checkbox"
                                               title="Include Air Conditioner">
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger field-error" id="hostel_conditioner_fee-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="hostel_electric_fee">Electric Heater</label>
                            <div class="input-group">
                                <input type="number" min="0" step="0.01" value="0"
                                       class="form-control hostel-utility-amount"
                                       name="hostel_electric_fee" id="hostel_electric_fee">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox" class="hostel-utility"
                                               name="electric_checkbox" value="on" id="electric_checkbox"
                                               title="Include Electric Heater">
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger field-error" id="hostel_electric_fee-error"></span>
                        </div>
                    </div>
                    <span class="text-danger field-error" id="hostel_utilities-error"></span>
                </div>

                <div id="manualFeeAmountsSection">
                <h5 class="section-title mt-3">Hostel Fee Amount</h5>
                <div class="row mt-3">
                    <div class="form-group col-md-4">
                        <label>Session Type</label>
                        <select class="form-control" name="sess_type" id="sess_type_hostel_fee">
                            <option value="">Select Type</option>
                            <option value="Regular">Regular</option>
                            <option value="Non-Regular">Non-Regular</option>
                            <option value="Regular_outside">Regular Safari Campus</option>
                            <option value="Non-Regular_outside">Non-Regular Safari Campus</option>
                            <option value="Regular_outside_bedian">Regular Bedian Campus</option>
                            <option value="Non-Regular_outside_bedian">Non-Regular Bedian Campus</option>
                        </select>
                        <span class="text-danger field-error" id="sess_type_hostel_fee-error"></span>
                    </div>
                    @foreach($feeFields as $name => $definition)
                        <div class="form-group col-md-4">
                            <label>
                                {{ $definition['label'] }}
                                @if($name === 'hostel_fee')
                                    <span class="required-star">*</span>
                                @endif
                            </label>
                            <input type="number" min="0" step="1" value="0"
                                   class="form-control fee-input integer-only"
                                   name="{{ $name }}" id="{{ $name }}"
                                   inputmode="numeric" pattern="[0-9]*">
                            <span class="text-danger field-error" id="{{ $name }}-error"></span>
                        </div>
                    @endforeach
                    <div class="form-group col-md-4">
                        <label for="hostel_days">No. of Days</label>
                        <input type="number" min="0" step="1" value="0"
                                class="form-control integer-only"
                               name="hostel_days" id="hostel_days"
                               placeholder="Enter No of Days">
                        <span class="text-danger field-error" id="hostel_days-error"></span>
                    </div>
                </div>
                </div>

                <div class="d-none" id="scholarshipArrearsSection">
                    <h5 class="section-title mt-3">Hostel Scholarship</h5>
                    <p class="text-muted mb-2">
                        Applied on Hostel Fee only (same as Record Management).
                        Invoice = (Hostel Fee − Scholarship) + Security − Arrears.
                    </p>
                    <input type="hidden" name="scholarship_columns[]" value="hostel_fee" id="auto_hostel_scholarship_column">
                    <div class="row mt-3">
                        <div class="form-group col-md-4">
                            <label>Hostel Scholarship Type</label>
                            <select class="form-control" name="scholarship_type_id" id="scholarship_type_id">
                                <option value="">No Scholarship</option>
                                @foreach($scholarships as $scholarship)
                                    <option value="{{ $scholarship->scholarship_id }}">{{ $scholarship->scholarship_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger field-error" id="scholarship_type_id-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Scholarship %</label>
                            <input type="number" min="0" max="100" step="0.01" value=""
                                   class="form-control" name="scholarship_percentage" id="scholarship_percentage"
                                   placeholder="e.g. 50">
                            <span class="text-danger field-error" id="scholarship_percentage-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Scholarship Amount</label>
                            <input type="number" min="0" step="0.01" value="0" readonly
                                   class="form-control" name="scholarship_amount_display" id="scholarship_amount_display">
                            <small class="text-muted">Auto: Hostel Fee × Percentage</small>
                        </div>
                    </div>

                    <h5 class="section-title mt-4">Hostel Scholarship Arrears</h5>
                    <div class="row mt-3">
                        <div class="form-group col-md-4">
                            <label>Arrears Amount</label>
                            <input type="number" min="0" step="0.01" value="0"
                                   class="form-control" name="arrears_amount" id="arrears_amount">
                            <small class="text-muted">Stored in SSHD_ARREAR and deducted from invoice (procedure formula).</small>
                            <span class="text-danger field-error" id="arrears_amount-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Arrears Scholarship Type</label>
                            <select class="form-control" name="arrears_scholarship_type_id" id="arrears_scholarship_type_id">
                                <option value="">Same as main / Select</option>
                                @foreach($scholarships as $scholarship)
                                    <option value="{{ $scholarship->scholarship_id }}">{{ $scholarship->scholarship_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Required only when arrears &gt; 0 and different from main type.</small>
                            <span class="text-danger field-error" id="arrears_scholarship_type_id-error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="section-title mt-4">Dates and Installment Settings</h5>
                <div class="row mt-3">
                    <div class="form-group col-md-3">
                        <label>Due Date <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="due_date" id="due_date">
                        <span class="text-danger field-error" id="due_date-error"></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Validate Date <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="valid_date" id="valid_date">
                        <span class="text-danger field-error" id="valid_date-error"></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Visible Date</label>
                        <input type="date" class="form-control" name="visible_date" id="visible_date">
                    </div>
                    <!-- <div class="form-group col-md-3">
                        <label>No. of Installments</label>
                        <select class="form-control" name="no_of_installments" id="no_of_installments">
                            <option value="">No Installments</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Installment Allowed <span class="required-star">*</span></label>
                        <select class="form-control" name="installment_allowed" id="installment_allowed">
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div> -->
                    {{-- UI hidden; still post default so validation passes --}}
                    <input type="hidden" name="installment_allowed" id="installment_allowed" value="NO">
                    <div class="form-group col-md-9">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="2" maxlength="1200"></textarea>
                    </div>
                </div>

                <div class="amount-card p-3 mb-4" id="scholarshipAmountCard">
                    <div class="row text-center">
                        <div class="col-md"><small>Gross Amount</small><h4 id="grossAmount">0.00</h4></div>
                        <div class="col-md"><small>Scholarship Base</small><h4 id="scholarshipBase">0.00</h4></div>
                        <div class="col-md"><small>Main Scholarship (-)</small><h4 class="text-danger" id="scholarshipAmount">0.00</h4></div>
                        <div class="col-md"><small>Arrears (-)</small><h4 class="text-danger" id="arrearsDeduction">0.00</h4></div>
                        <div class="col-md"><small>Total Deduction</small><h4 class="text-danger" id="totalDeduction">0.00</h4></div>
                        <div class="col-md"><small>Invoice Amount</small><h3 class="text-success" id="invoiceAmount">0.00</h3></div>
                    </div>
                    <div class="alert alert-info mb-0 mt-2">
                        Hostel Fee − Scholarship + Security − Arrears = Invoice (same as hostel challan procedure).
                    </div>
                </div>

                <div class="amount-card p-3 mb-4 d-none" id="hostelUtilitiesAmountCard">
                    <div class="row text-center">
                        <div class="col-md"><small>Refrigerator</small><h4 id="hostelFridgeAmount">0.00</h4></div>
                        <div class="col-md"><small>Air Conditioner</small><h4 id="hostelAcAmount">0.00</h4></div>
                        <div class="col-md"><small>Electric Heater</small><h4 id="hostelHeaterAmount">0.00</h4></div>
                        <div class="col-md"><small>Invoice Amount</small><h3 class="text-success" id="hostelInvoiceAmount">0.00</h3></div>
                    </div>
                    <div class="alert alert-info mb-0 mt-2">
                        Only checked utility amounts are totaled for the hostel utilities challan.
                    </div>
                </div>

                <div id="formMessage"></div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-create-challan" id="saveBtn">
                        <i class="fa fa-file-invoice"></i> Create Invoice / Challan
                    </button>
                    <button type="button" class="btn btn-clear-form" id="resetBtn">
                        <i class="fa fa-undo"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
(function () {
    const searchUrl = @json(route('finance.manual-scholarship-challan.search'));
    const storeUrl = @json(route('finance.manual-scholarship-challan.store'));

    function money(value) {
        return Number(value || 0).toFixed(2);
    }

    function clearErrors() {
        $('.field-error').text('');
        $('.is-invalid').removeClass('is-invalid');
    }

    function showErrors(errors) {
        Object.keys(errors || {}).forEach(function (key) {
            const cleanKey = key.replace(/\./g, '_');
            const input = $('[name="' + key + '"]:not(:disabled)');
            input.addClass('is-invalid');
            $('#' + cleanKey + '-error').text(errors[key][0]);

            // Hostel Fee uses a second Session Type control with a different id.
            if (key === 'sess_type') {
                $('#sess_type_hostel_fee-error').text(errors[key][0]);
                $('#sess_type_hostel_fee').addClass('is-invalid');
            }

            if (key === 'hostel_days') {
                $('#hostel_days_utilities-error').text(errors[key][0]);
                $('#hostel_days_utilities').addClass('is-invalid');
            }

            if (key.indexOf('scholarship_columns') === 0) {
                $('#scholarship_columns-error').text(errors[key][0]);
            }
        });
    }

    function formatDate(date) {
        return date.toISOString().slice(0, 10);
    }

    function setDefaultDates() {
        const today = new Date();
        const valid = new Date();
        valid.setDate(valid.getDate() + 7);
        $('#due_date, #visible_date').val(formatDate(today));
        $('#valid_date').val(formatDate(valid));
    }

    function calculateScholarshipInvoice() {
        // Hostel fee challan: same formula as fin_new_std_hostel_chln_gen
        // invoice = (hostel_fee - scholarship) + security - arrears
        const hostelFee = parseFloat($('#hostel_fee').val()) || 0;
        const securityFee = parseFloat($('#security_fee').val()) || 0;
        const gross = hostelFee + securityFee;

        let percentage = parseFloat($('#scholarship_percentage').val()) || 0;
        percentage = Math.min(100, Math.max(0, percentage));

        const hasScholarshipType = !!$('#scholarship_type_id').val();
        const scholarshipBase = hasScholarshipType && percentage > 0 ? hostelFee : 0;
        const scholarship = scholarshipBase * percentage / 100;
        const arrears = Math.max(0, parseFloat($('#arrears_amount').val()) || 0);
        const totalDeduction = scholarship + arrears;
        const invoice = Math.max(0, (hostelFee - scholarship) + securityFee - arrears);

        $('#scholarship_amount_display').val(money(scholarship));
        $('#grossAmount').text(money(gross));
        $('#scholarshipBase').text(money(scholarshipBase));
        $('#scholarshipAmount').text(money(scholarship));
        $('#arrearsDeduction').text(money(arrears));
        $('#totalDeduction').text(money(totalDeduction));
        $('#invoiceAmount').text(money(invoice));
    }

    function calculateHostelUtilitiesInvoice() {
        const fridgeOn = $('#refrigerator_checkbox').is(':checked');
        const acOn = $('#conditioner_checkbox').is(':checked');
        const heaterOn = $('#electric_checkbox').is(':checked');

        const fridge = fridgeOn ? (parseFloat($('#hostel_refrigerator_fee').val()) || 0) : 0;
        const ac = acOn ? (parseFloat($('#hostel_conditioner_fee').val()) || 0) : 0;
        const heater = heaterOn ? (parseFloat($('#hostel_electric_fee').val()) || 0) : 0;
        const invoice = fridge + ac + heater;

        $('#hostelFridgeAmount').text(money(fridge));
        $('#hostelAcAmount').text(money(ac));
        $('#hostelHeaterAmount').text(money(heater));
        $('#hostelInvoiceAmount').text(money(invoice));
    }

    function calculateInvoice() {
        if ($('#fee_type').val() === 'Hostel_other_utilities') {
            calculateHostelUtilitiesInvoice();
        } else {
            calculateScholarshipInvoice();
        }
    }

    function resetStudent() {
        $('#studentInfoCard').addClass('d-none');
        $('#student_input, #enrollment_no, #st_aai_id, #st_school_id, #st_dept_id, #st_prog_id, #student_name, #father_name, #guardian_cnic').val('');
    }

    $('#searchStudentBtn').on('click', function () {
        clearErrors();
        resetStudent();

        const input = $.trim($('#studentEnrollment').val());
        if (!input) {
            $('#studentEnrollment-error').text('Enrollment No or Applicant ID is required.');
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching');

        $.ajax({
            url: searchUrl,
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                studentEnrollment: input
            },
            success: function (response) {
                const s = response.student;

                $('#student_input').val(input);
                $('#enrollment_no').val(s.enrollment_no || '');
                $('#st_aai_id').val(s.aai_id || '');
                $('#st_school_id').val(s.school_id || '');
                $('#st_dept_id').val(s.dept_id || '');
                $('#st_prog_id').val(s.prog_id || '');
                $('#student_name').val(s.student_name || '');
                $('#father_name').val(s.father_name || '');
                $('#guardian_cnic').val(s.guardian_cnic || '');

                $('#showStudentName').text(s.student_name || 'N/A');
                $('#showEnrollment').text((s.enrollment_no || 'N/A') + ' / ' + (s.aai_id || 'N/A'));
                $('#showSchool').text(s.school_name || 'N/A');
                $('#showDepartment').text(s.dept_name || 'N/A');
                $('#showProgram').text(s.prog_name || 'N/A');
                $('#showEmail').text(s.email || 'N/A');
                $('#showMobile').text(s.mobile || 'N/A');
                $('#showStatus').text(s.student_status || 'N/A');
                $('#student_status').val(s.student_status || '');

                $('#studentInfoCard').removeClass('d-none');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                $('#studentEnrollment-error').text(response.message || 'Student could not be found.');
            },
            complete: function () {
                button.prop('disabled', false).html('<i class="fa fa-search"></i> Search');
            }
        });
    });

    $('#studentEnrollment').on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            $('#searchStudentBtn').click();
        }
    });

    $(document).on('keydown', '.integer-only', function (event) {
        if (['.', ',', 'e', 'E', '+', '-'].includes(event.key)) {
            event.preventDefault();
        }
    });

    $(document).on('input', '.integer-only', function () {
        const cleaned = String(this.value || '').replace(/[^\d]/g, '');
        if (this.value !== cleaned) {
            this.value = cleaned;
        }
    });

    $(document).on('paste', '.integer-only', function (event) {
        event.preventDefault();
        const pasted = (event.originalEvent.clipboardData || window.clipboardData).getData('text') || '';
        const digits = pasted.replace(/[^\d]/g, '');
        const input = this;
        const start = input.selectionStart || 0;
        const end = input.selectionEnd || 0;
        const next = input.value.slice(0, start) + digits + input.value.slice(end);
        input.value = next.replace(/[^\d]/g, '');
        $(input).trigger('input');
    });

    $(document).on(
        'input change',
        '.fee-input, #scholarship_percentage, #scholarship_type_id, #arrears_amount, #arrears_scholarship_type_id, .hostel-utility-amount, .hostel-utility',
        calculateInvoice
    );

    function syncHostelStudentTypeToChallan() {
        // Hostel UI hides Student Type; keep SC_STUDENT_STATUS populated.
        const feeType = $('#fee_type').val();
        if (feeType === 'Hostel_other_utilities') {
            const hostelType = $('#hostel_student_type').val();
            if (hostelType === 'New_Admission') {
                $('#student_type').val('New Admission FALL');
            } else if (hostelType === 'Existing') {
                $('#student_type').val('Existing');
            } else if (!$('#student_type').val()) {
                $('#student_type').val('Existing');
            }
            return;
        }

        if (feeType === 'Hostel_Fee' && !$('#student_type').val()) {
            $('#student_type').val('Existing');
        }
    }

    function updateFeeTypeFields() {
        const feeType = $('#fee_type').val();
        const isHostelUtilities = feeType === 'Hostel_other_utilities';
        const isHostelFee = feeType === 'Hostel_Fee';
        const isHostel = isHostelFee || isHostelUtilities;

        // Hostel: Session + Fee Type first; Student Type not shown.
        $('#studentTypeGroup').toggleClass('d-none', isHostel);

        $('#hostelUtilitiesSection').toggleClass('d-none', !isHostelUtilities);
        $('#manualFeeAmountsSection').toggleClass('d-none', isHostelUtilities);
        $('#scholarshipArrearsSection').toggleClass('d-none', !isHostelFee);
        $('#scholarshipAmountCard').toggleClass('d-none', isHostelUtilities);
        $('#hostelUtilitiesAmountCard').toggleClass('d-none', !isHostelUtilities);

        // Hidden controls are disabled so browser native validation never
        // tries to focus an invalid field that is not visible (not focusable).
        $('#scholarshipArrearsSection').find('input, select, textarea').prop('disabled', !isHostelFee);
        $('#hostelUtilitiesSection').find('input, select, textarea').prop('disabled', !isHostelUtilities);
        $('#manualFeeAmountsSection').find('input, select, textarea').prop('disabled', isHostelUtilities);
        // Keep student_type enabled (hidden) so it still posts with the form.
        $('#student_type').prop('disabled', false);

        if (!isHostelUtilities) {
            $('#hostel_utilities-error').text('');
        }

        syncHostelStudentTypeToChallan();
        calculateInvoice();
    }

    $('#fee_type').on('change', updateFeeTypeFields);
    $('#hostel_student_type').on('change', syncHostelStudentTypeToChallan);

    $('#manualScholarshipForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();
        $('#formMessage').html('');
        syncHostelStudentTypeToChallan();

        if (!$('#st_aai_id').val()) {
            $('#studentEnrollment-error').text('Search and select a valid student first.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        const button = $('#saveBtn');
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');

        function generatedChallanCard(options) {
            const itemsHtml = (options.items || []).map(function (item) {
                return '' +
                    '<div class="generated-challan-item' + (item.total ? ' generated-challan-item--total' : '') + '">' +
                        '<small>' + item.label + '</small>' +
                        '<strong>' + item.value + '</strong>' +
                    '</div>';
            }).join('');

            return '' +
                '<div class="generated-challan-card">' +
                    '<div class="generated-challan-card__header">' +
                        '<div class="generated-challan-card__header-left">' +
                            '<span class="generated-challan-card__icon"><i class="fa fa-check"></i></span>' +
                            '<div>' +
                                '<h5 class="generated-challan-card__title">Generated Challan</h5>' +
                                '<p class="generated-challan-card__subtitle">' + (options.message || 'Challan created successfully.') + '</p>' +
                            '</div>' +
                        '</div>' +
                        '<span class="generated-challan-card__badge">' + (options.badge || 'Success') + '</span>' +
                    '</div>' +
                    '<div class="generated-challan-card__body">' +
                        '<div class="generated-challan-meta">' +
                            '<span>Challan ID: <strong>' + (options.challanId || 'Generated') + '</strong></span>' +
                            '<span>Type: <strong>' + (options.typeLabel || '-') + '</strong></span>' +
                        '</div>' +
                        '<div class="generated-challan-grid">' + itemsHtml + '</div>' +
                    '</div>' +
                '</div>';
        }

        $.ajax({
            url: storeUrl,
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                const d = response.data || {};
                let successHtml;

                if (d.mode === 'hostel_utilities') {
                    successHtml = generatedChallanCard({
                        message: response.message,
                        badge: 'Hostel Utilities',
                        challanId: d.challan_id,
                        typeLabel: 'Hostel Other Utilities',
                        items: [
                            { label: 'Refrigerator', value: money(d.refrigerator_fee) },
                            { label: 'Air Conditioner', value: money(d.conditioner_fee) },
                            { label: 'Electric Heater', value: money(d.electric_fee) },
                            { label: 'No. of Days', value: d.hostel_days || 0 },
                            { label: 'Invoice Amount', value: money(d.invoice_amount), total: true }
                        ]
                    });
                } else {
                    successHtml = generatedChallanCard({
                        message: response.message,
                        badge: 'Hostel Fee',
                        challanId: d.challan_id,
                        typeLabel: d.challan_type || 'Hostel Fee',
                        items: [
                            { label: 'Hostel Fee', value: money(d.hostel_fee) },
                            { label: 'Hostel Security', value: money(d.security_fee) },
                            { label: 'No. of Days', value: d.hostel_days || 0 },
                            { label: 'Scholarship (-)', value: money(d.scholarship_amount) },
                            { label: 'Arrears (-)', value: money(d.arrears_amount) },
                            { label: 'Invoice Amount', value: money(d.invoice_amount), total: true }
                        ]
                    });
                }

                document.getElementById('manualScholarshipForm').reset();
                $('#studentEnrollment').val('');
                resetStudent();
                clearErrors();
                $('.fee-input').val('0');
                $('#scholarship_percentage, #arrears_amount').val('0');
                $('#hostel_days, #hostel_days_utilities').val('0');
                $('.hostel-utility-amount').val('0');
                $('.hostel-utility').prop('checked', false);
                $('#formMessage').html(successHtml);
                setDefaultDates();
                updateFeeTypeFields();
                calculateInvoice();
                const resultTop = document.getElementById('formMessage');
                if (resultTop) {
                    resultTop.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                if (xhr.status === 422) {
                    showErrors(response.errors || {});
                    $('#formMessage').html('<div class="alert alert-danger mb-0">Please correct the highlighted fields.</div>');
                } else {
                    $('#formMessage').html('<div class="alert alert-danger mb-0">' + (response.message || 'An error occurred.') + '</div>');
                }
            },
            complete: function () {
                button.prop('disabled', false).html('<i class="fa fa-file-invoice"></i> Create Invoice / Challan');
            }
        });
    });

    $('#resetBtn').on('click', function () {
        document.getElementById('manualScholarshipForm').reset();
        $('#studentEnrollment').val('');
        resetStudent();
        clearErrors();
        $('#formMessage').html('');
        $('.fee-input').val('0');
        $('#scholarship_percentage, #arrears_amount').val('0');
        $('#hostel_days, #hostel_days_utilities').val('0');
        $('.hostel-utility-amount').val('0');
        $('.hostel-utility').prop('checked', false);
        setDefaultDates();
        updateFeeTypeFields();
        calculateInvoice();
    });

    setDefaultDates();
    updateFeeTypeFields();
    calculateInvoice();
})();
</script>
@stop
