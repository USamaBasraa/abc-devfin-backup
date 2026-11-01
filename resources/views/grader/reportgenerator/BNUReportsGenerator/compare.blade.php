@extends('layouts.app')

@section('content')
<div class="container">
  
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Admission Data Comparison</h5>
        </div>
        <div class="card-body">
            <form action="#" id='myForm'>  <!-- Specify method for form submission -->
                @csrf  <!-- Include CSRF token for form submission -->

                <div class="row">

                    <!-- Roadmap Session -->
                    <div class="form-group col-md-4">
                        <label for="roadmap_session" class="d-flex justify-content-between align-items-center">
                            <span>Road Map Session:</span>
                            <span class="input-group-append clear-icon" id="clear-roadmap-session" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="roadmap_session" id="roadmap_session" class="form-control select2" required>
                            <option value="" disabled selected>-- Select Road Map Session --</option>
                            @foreach($academic_session as $session)
                                <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- School 1 -->
                    <div class="form-group col-md-4">
                        <label for="school_1" class="d-flex justify-content-between align-items-center">
                            <span>School 1:</span>
                            <span class="input-group-append clear-icon" id="clear-school-1" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="school_1" id="school_1" class="form-control select2">
                            <option value="" disabled selected>-- Select School 1 --</option>
                            @foreach($recSchool as $school)
                                <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- School 2 -->
                    <div class="form-group col-md-4">
                        <label for="school_2" class="d-flex justify-content-between align-items-center">
                            <span>School 2:</span>
                            <span class="input-group-append clear-icon" id="clear-school-2" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="school_2" id="school_2" class="form-control select2">
                            <option value="" disabled selected>-- Select School 2 --</option>
                            @foreach($recSchool as $school)
                                <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="form-group col-md-4">
                        <label for="department" class="d-flex justify-content-between align-items-center">
                            <span>Department:</span>
                            <span class="input-group-append clear-icon" id="clear-department" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="department" id="department" class="form-control select2" disabled>
                            <option value="" disabled selected>-- Select Department --</option>
                        </select>
                    </div>

                    <!-- Fee Status -->
                    <div class="form-group col-md-4">
                        <label for="pf_status" class="d-flex justify-content-between align-items-center">
                            <span>Fee Status:</span>
                            <span class="input-group-append clear-icon" id="clear-pf-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="pf_status" id="pf_status" class="form-control select2">
                            <option value="" disabled selected>-- Select Fee Status --</option>
                            @foreach($distinctPfStatus as $status)
                                <option value="{{ $status->aai_pf_status }}">{{ $status->aai_pf_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Qualification Status -->
                    <div class="form-group col-md-4">
                        <label for="qualification_status" class="d-flex justify-content-between align-items-center">
                            <span>Qualification Status:</span>
                            <span class="input-group-append clear-icon" id="clear-qualification-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="qualification_status" id="qualification_status" class="form-control select2">
                            <option value="" disabled selected>-- Select Qualification Status --</option>
                            @foreach($distinctQualificationStatus as $status)
                                <option value="{{ $status->aai_qualification_status }}">{{ $status->aai_qualification_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student Status -->
                    <div class="form-group col-md-4">
                        <label for="status" class="d-flex justify-content-between align-items-center">
                            <span>Student Status:</span>
                            <span class="input-group-append clear-icon" id="clear-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="status" id="status" class="form-control select2">
                            <option value="" disabled selected>-- Select Student Status --</option>
                            @foreach($distinctStatus as $status)
                                <option value="{{ $status->aai_status }}">{{ $status->aai_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Students Moved to CMS -->
                    <div class="form-group col-md-4">
                        <label for="students_moved_to_cms" class="d-flex justify-content-between align-items-center">
                            <span>Students Moved to CMS:</span>
                            <span class="input-group-append clear-icon" id="students_moved_to_cms-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="students_moved_to_cms" id="students_moved_to_cms" class="form-control select2">
                            <option value="" disabled selected>-- Select Option --</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>

                    <!-- CMS Student Status -->
                    <div class="form-group col-md-4">
                        <label for="cms_student_status" class="d-flex justify-content-between align-items-center">
                            <span>CMS Student Status:</span>
                            <span class="input-group-append clear-icon" id="clear-cms_student_status-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="cms_student_status" id="cms_student_status" class="form-control select2">
                            <option value="" disabled selected>-- Select Status --</option>
                            @foreach($studentStatus as $status)
                                <option value="{{ $status->sai_student_status }}">{{ $status->sai_student_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CMS Fee Status -->
                    <div class="form-group col-md-4">
                        <label for="cms_fee_status" class="d-flex justify-content-between align-items-center">
                            <span>CMS Fee Status:</span>
                            <span class="input-group-append clear-icon" id="cms_fee_status-status" style="display:none;">
                                <span class="input-group-text">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </label>
                        <select name="cms_fee_status" id="cms_fee_status" class="form-control select2">
                            <option value="" disabled selected>-- Select Option --</option>
                            <option value="Partially Paid">Partially Paid</option>
                            <option value="Full Paid">Full Paid</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="form-group col-md-3 d-flex justify-content-left align-items-center" style="height: 100px;">
                        <a href="{{ route('get-compare-data') }}" id="filterStudentsButton" class="btn btn-outline-primary" title="Compare Schools">
                            <i class="fa fa-exchange"></i> Compare Schools
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Include your custom scripts here for dynamic behavior or functionality
</script>
@endsection
