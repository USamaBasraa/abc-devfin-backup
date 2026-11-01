    @extends('layouts.app')
    @section('title')
        BNU CMS - Student Profile
    @stop

    @section('content')


    @include('components.toast-notifications')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12 mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0 text-primary">
                        <i class="fa fa-user-graduate mr-2"></i>Need Based Scholarship Profile Management
                    </h2>
                </div>
                
            </div>
        </div>
    </div>

    @if($applicantData)
        <!-- Student Information Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header" style="background: linear-gradient(135deg, #4285f4, #5b9bf8); color: white;">
                        <h5 class="mb-0">
                            <i class="fa fa-info-circle mr-2"></i>Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="studentForm">
                            @csrf
                            <input type="hidden" name="aai_id" value="{{ $applicantData->aai_id }}">
                            
                            <div class="row">
                                @foreach($applicantData as $key => $value)
                                @php
                                    $hiddenFields = [
                                        'is_editable', 
                                        'session_id', 
                                        'aai_id', 
                                        'enrollment_no', 
                                        'id', 
                                        'api_id', 
                                        'registration_no', 
                                        'sai_enrollment_no', 
                                        'current_semester',
                                        'sess_id',
                                        'admin_ro_approval',
                                        'remarks_by_admin_ro_approval',
                                        'approved_by_admin_ro_approval',
                                        'admin_ro_approved_at',
                                        'guardian_mobile_no',
                                        'school_name',
                                        'institution_name',
                                        'degree_certificate',
                                        'marks',
                                        'semester_number',
                                        'current_semester',
                                        'grades',
                                        'fax_no',
                                        'already_availed_nbs_scholarship',
                                        'currently_receiving_nbs_scholarship',
                                        'financial_status_changed',
                                        'other_house_details'

                                    ];
                                @endphp

                                @if(in_array($key, $hiddenFields))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @else
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <label class="form-label text-muted small">
                                            <i class="fa fa-tag mr-1"></i>{{ ucwords(str_replace('_', ' ', $key)) }}
                                        </label>
                                        <input type="text" name="{{ $key }}" class="form-control form-control-sm" value="{{ $value }}">
                                    </div>
                                @endif
                             @endforeach
                             
                            </div>
                            
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-primary" id="saveBtn">
                                    <i class="fa fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle mr-2"></i>No data found for this student.
                </div>
            </div>
        </div>
    @endif



{{-- ✅ Check Data Conditions --}}
@php
$hasSiblingData = !empty($resaqlSiblings) && collect($resaqlSiblings)->filter(function($s){
    return !empty(trim($s->sibling_name));
})->count() > 0;

$hasEmployedData = !empty($ressqlEmployed) && collect($ressqlEmployed)->filter(function($e){
    return !empty(trim($e->employed_name));
})->count() > 0;

$hasUnemployedData = !empty($ressqlunEmployed) && collect($ressqlunEmployed)->filter(function($u){
    return !empty(trim($u->unemployed_name));
})->count() > 0;

$hasTransportData = !empty($ressqltransport) && collect($ressqltransport)->filter(function($t){
    return !empty(trim($t->vehicle_type));
})->count() > 0;
@endphp

{{-- ✅ Sibling Section --}}
@if($hasSiblingData)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fa fa-users mr-2"></i>Siblings Information
                </h5>
            </div>
            <div class="card-body">
                <form id="siblingForm">
                    @csrf
                    <input type="hidden" name="aai_id" value="{{ $applicantData->aai_id }}">
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fa fa-user mr-1"></i>Name</th>
                                    <th><i class="fa fa-birthday-cake mr-1"></i>Age</th>
                                    <th><i class="fa fa-heart mr-1"></i>Marital Status</th>
                                    <th><i class="fa fa-graduation-cap mr-1"></i>School/College</th>
                                    <th><i class="fa fa-money mr-1"></i>Fee</th>
                                    <th><i class="fa fa-gift mr-1"></i>Scholarship</th>
                                    <th><i class="fa fa-hand-holding-usd mr-1"></i>Parent Contribution</th>
                                    <th><i class="fa fa-cogs mr-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resaqlSiblings as $sibling)
                                    <tr>
                                        <td><input type="text" name="sibling_name[]" class="form-control form-control-sm" value="{{ $sibling->sibling_name }}"></td>
                                        <td><input type="text" name="sibling_age[]" class="form-control form-control-sm" value="{{ $sibling->sibling_age }}"></td>
                                        <td><input type="text" name="sibling_marital_status[]" class="form-control form-control-sm" value="{{ $sibling->sibling_marital_status }}"></td>
                                        <td><input type="text" name="school_college_name[]" class="form-control form-control-sm" value="{{ $sibling->school_college_name }}"></td>
                                        <td><input type="text" name="annual_fee[]" class="form-control form-control-sm" value="{{ $sibling->annual_fee }}"></td>
                                        <td><input type="text" name="annual_scholarship[]" class="form-control form-control-sm" value="{{ $sibling->annual_scholarship }}"></td>
                                        <td><input type="text" name="annual_parents_contribution[]" class="form-control form-control-sm" value="{{ $sibling->annual_parents_contribution }}"></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm delete-row" 
                                                onclick="deleteRow('siblings', {{ $sibling->id }}, {{ $applicantData->aai_id }}, this)"
                                                disabled
                                                title="Delete functionality temporarily disabled">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <input type="hidden" name="sibling_id[]" value="{{ $sibling->id }}">
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-success" id="saveSiblingsBtn">
                            <i class="fa fa-save mr-2"></i>Save Siblings Info
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ✅ Employed Section --}}
@if($hasEmployedData)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #00bcd4, #26c6da); color: white;">
                <h5 class="mb-0"><i class="fa fa-briefcase mr-2"></i>Employed / Working / Business</h5>
            </div>
            <div class="card-body">
                <form id="employedForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fa fa-user mr-1"></i>Name</th>
                                    <th><i class="fa fa-birthday-cake mr-1"></i>Age</th>
                                    <th><i class="fa fa-heart mr-1"></i>Marital Status</th>
                                    <th><i class="fa fa-cogs mr-1"></i>Profession</th>
                                    <th><i class="fa fa-money mr-1"></i>Annual Income</th>
                                    <th><i class="fa fa-hand-holding-usd mr-1"></i>Annual Contribution</th>
                                    <th><i class="fa fa-cogs mr-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ressqlEmployed as $employed)
                                <tr>
                                    <td>
                                        <input type="hidden" name="employed_id[]" value="{{ $employed->id }}">
                                        <input type="text" class="form-control form-control-sm" name="employed_name[]" value="{{ $employed->employed_name }}">
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" name="employed_age[]" value="{{ $employed->employed_age }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="employeed_marital_status[]" value="{{ $employed->employeed_marital_status }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="profession[]" value="{{ $employed->profession }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="annual_income[]" value="{{ $employed->annual_income }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="annual_contribution[]" value="{{ $employed->annual_contribution }}"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-row" 
                                            onclick="deleteRow('employed', {{ $employed->id }}, {{ $applicantData->aai_id }}, this)"
                                            disabled
                                            title="Delete functionality temporarily disabled">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-info" id="saveEmployedBtn">
                            <i class="fa fa-save mr-2"></i>Save Employed Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ✅ Unemployed Section --}}
@if($hasUnemployedData)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #ff9800, #ffb74d); color: white;">
                <h5 class="mb-0"><i class="fa fa-user-times mr-2"></i>Unemployed Siblings</h5>
            </div>
            <div class="card-body">
                <form id="unemployedForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fa fa-user mr-1"></i>Name</th>
                                    <th><i class="fa fa-birthday-cake mr-1"></i>Age</th>
                                    <th><i class="fa fa-heart mr-1"></i>Marital Status</th>
                                    <th><i class="fa fa-question-circle mr-1"></i>Reason of Unemployment</th>
                                    <th><i class="fa fa-cogs mr-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ressqlunEmployed as $unemployed)
                                <tr>
                                    <td>
                                        <input type="hidden" name="unemployed_id[]" value="{{ $unemployed->id }}">
                                        <input type="text" class="form-control form-control-sm" name="unemployed_name[]" value="{{ $unemployed->unemployed_name }}">
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" name="unemployed_age[]" value="{{ $unemployed->unemployed_age }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="unemployed_marital_status[]" value="{{ $unemployed->unemployed_marital_status }}"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="unemployment_reason[]" value="{{ $unemployed->unemployment_reason }}"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-row" 
                                            onclick="deleteRow('unemployed', {{ $unemployed->id }}, {{ $applicantData->aai_id }}, this)"
                                            disabled
                                            title="Delete functionality temporarily disabled">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-warning" id="saveUnemployedBtn">
                            <i class="fa fa-save mr-2"></i>Save Unemployed Siblings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ✅ Transport Section --}}
@if($hasTransportData)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #9e9e9e, #bdbdbd); color: white;">
                <h5 class="mb-0"><i class="fa fa-car mr-2"></i>Transport Information</h5>
            </div>
            <div class="card-body">
                <form id="transportForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th><i class="fa fa-car mr-1"></i>Vehicle Type</th>
                                    <th><i class="fa fa-cog mr-1"></i>Vehicle Model</th>
                                    <th><i class="fa fa-calendar mr-1"></i>Vehicle Year</th>
                                    <th><i class="fa fa-gas-pump mr-1"></i>Fuel Cost</th>
                                    <th><i class="fa fa-cogs mr-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ressqltransport as $transport)
                                <tr>
                                    <td>
                                        <input type="hidden" name="transport_id[]" value="{{ $transport->id }}">
                                        <input type="text" class="form-control form-control-sm" name="vehicle_type[]" value="{{ $transport->vehicle_type }}">
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm" name="vehicle_model[]" value="{{ $transport->vehicle_model }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="vehicle_year[]" value="{{ $transport->vehicle_year }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="vehicle_fuel_cost[]" value="{{ $transport->vehicle_fuel_cost }}"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-row" 
                                            onclick="deleteRow('transport', {{ $transport->id }}, {{ $applicantData->aai_id }}, this)"
                                            disabled
                                            title="Delete functionality temporarily disabled">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-secondary" id="saveTransportBtn">
                            <i class="fa fa-save mr-2"></i>Save Transport Info
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif



</div>

@stop

{{-- jQuery CDN (Required) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Add loading states to buttons
    function setButtonLoading(buttonId, isLoading) {
        const btn = $(buttonId);
        if (isLoading) {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Saving...');
        } else {
            btn.prop('disabled', false);
        }
    }

    // Student form save
    $('#saveBtn').on('click', function () {
        setButtonLoading('#saveBtn', true);
        const formData = $('#studentForm').serialize();

        $.ajax({
            url: '/api/nbs-form-update-profile-data',
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
            },
            success: function (response) {
                showToast('success', 'Success!', response.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                    showToast('danger', 'Error!', 'Something went wrong while saving student data.');
            },
            complete: function() {
                setButtonLoading('#saveBtn', false);
            }
        });
    });

    // Siblings form save
    $('#saveSiblingsBtn').on('click', function () {
        setButtonLoading('#saveSiblingsBtn', true);
        const formData = $('#siblingForm').serialize();
        
        $.ajax({
            url: '/api/nbs-form-update-siblings',
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Accept": "application/json",
                "Authorization": 'Bearer {{ auth()->user()->api_token }}'
            },
            success: function (response) {
                showToast('success', 'Success!', response.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function () {
                showToast('danger', 'Error!', 'Failed to update siblings information.');
            },
            complete: function() {
                setButtonLoading('#saveSiblingsBtn', false);
            }
        });
    });

    // Employed form save
    $('#saveEmployedBtn').on('click', function () {
        setButtonLoading('#saveEmployedBtn', true);
        let formData = $('#employedForm').serialize();

        $.ajax({
            url: '/api/nbs-form-update-employed',
            type: 'POST',
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Accept": "application/json",
                "Authorization": 'Bearer {{ auth()->user()->api_token }}'
            },
            success: function (res) {
                showToast('success', 'Success!', res.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function () {
                showToast('danger', 'Error!', 'Something went wrong while saving employed data.');
            },
            complete: function() {
                setButtonLoading('#saveEmployedBtn', false);
            }
        });
    });

    // Unemployed form save
    $('#saveUnemployedBtn').on('click', function () {
        setButtonLoading('#saveUnemployedBtn', true);
        const formData = $('#unemployedForm').serialize();

        $.ajax({
            url: '/api/nbs-form-update-unemployed',
            type: 'POST',
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Accept": "application/json",
                "Authorization": 'Bearer {{ auth()->user()->api_token }}'
            },
            success: function (response) {
                showToast('success', 'Success!', response.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function () {
                showToast('danger', 'Error!', 'Failed to update unemployed data.');
            },
            complete: function() {
                setButtonLoading('#saveUnemployedBtn', false);
            }
        });
    });

    // Transport form save
    $('#saveTransportBtn').on('click', function () {
        setButtonLoading('#saveTransportBtn', true);
        const formData = $('#transportForm').serialize();

        $.ajax({
            url: '/api/nbs-form-update-transport',
            type: 'POST',
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Accept": "application/json",
                "Authorization": 'Bearer {{ auth()->user()->api_token }}'
            },
            success: function (response) {
                showToast('success', 'Success!', response.message);
                setTimeout(() => location.reload(), 1500);
            },
            error: function () {
                showToast('danger', 'Error!', 'Failed to update transport data.');
            },
            complete: function() {
                setButtonLoading('#saveTransportBtn', false);
            }
        });
    });

   
});


function deleteRow(sectionName, rowId, applicantId, element) {
    // Get the table body element
    const tbody = $(element).closest('tbody');
    const rowCount = tbody.find('tr').length;

    // Don't allow deletion if only one row
    if (rowCount <= 1) {
        showToast('warning', 'Warning!', 'Cannot delete the only record in this section');
        return;
    }

    if (!confirm('Are you sure you want to delete this record?')) {
        return;
    }

    $.ajax({
        url: '/api/nbs-form-delete-section-row',
        method: 'DELETE',
        data: {
            section_name: sectionName,
            row_id: rowId,
            applicant_id: applicantId
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            if (response.status) {
                // Remove the row
                $(element).closest('tr').fadeOut(400, function() {
                    $(this).remove();
                    
                    // After removal, check if we should disable the remaining delete button
                    const remainingRows = tbody.find('tr').length;
                    if (remainingRows <= 1) {
                        tbody.find('.delete-row').prop('disabled', true)
                            .attr('title', 'Cannot delete the only record');
                    }
                });
                showToast('success', 'Success!', 'Record deleted successfully');
            } else {
                showToast('error', 'Error!', response.message || 'Failed to delete record');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to delete record';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast('error', 'Error!', errorMessage);
        }
    });
}
</script>

<style>
/* Add this to hide all delete buttons */
.delete-row {
    display: none !important;
}
</style>


