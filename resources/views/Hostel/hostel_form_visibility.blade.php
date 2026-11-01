@extends('layouts.app')

@section('content')
    <!-- Include Toast Component -->
    @include('components.toast-notifications')

<style>
    /* Fix Select2 dropdown overflow issue */
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
    .card-body {
        overflow: visible !important;
    }
    .form-group {
        overflow: visible !important;
    }
    
    /* Fix Select2 multiple selection overflow */
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        max-height: 120px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 5px;
        margin-bottom: 5px;
        margin-right: 5px;
        word-break: break-word;
        white-space: normal;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding-bottom: 5px;
    }
</style>

<div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fa fa-calendar-alt mr-2"></i>
                    <span id="formTitle">Add Hostel Form Visibility </span>
                </h5>
            </div>

            <div class="card-body">
                <form id="hostelVisibilityForm" method="POST" action="{{ route('submit-hostel-form-visibility') }}">
                    @csrf
                    <input type="hidden" name="id" id="recordId">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Visibility Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" name="start_date" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Visibility End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" name="end_date" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="session">Session <span class="text-danger">*</span></label>
                                <select class="form-control @error('session') is-invalid @enderror" 
                                    id="session" name="session" required>
                                    <option value="">Select Session</option>
                                    @foreach ($session as $s)
                                        <option value="{{ $s->sess_id }}">{{ $s->sess_name }}</option>
                                    @endforeach
                                </select>
                                @error('session')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="visibility_type">Visibility Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('visibility_type') is-invalid @enderror" 
                                    id="visibility_type" name="visibility_type" required>
                                    <option value="">Select Visibility Type</option>
                                    <option value="admission_applicants">Admission Applicants</option>
                                    <option value="regular_students">Regular Students</option>
                                    <option value="already_existing_hostel_students">Current Hostel Students</option>
                                </select>
                                @error('visibility_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="position: relative;">
                                <label for="hostel_type_id">Select Hostels <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('hostel_type_id') is-invalid @enderror" 
                                    id="hostel_type_id" name="hostel_type_id[]" multiple required>
                                    <option value="0">All Hostels</option>
                                    @foreach ($hostelTypes as $hostelType)
                                        @php
                                            // Simple way to show location (same as reference file)
                                            $locationText = 'on campus';
                                            if ($hostelType->ht_location == 'off_campus' || $hostelType->ht_location == 'out_of_campus') {
                                                $locationText = 'off campus';
                                            }
                                        @endphp
                                        <option value="{{ $hostelType->ht_id }}">{{ $hostelType->ht_name }} ({{ $locationText }})</option>
                                    @endforeach
                                </select>
                                @error('hostel_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Type to search or select multiple hostels</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date">Challan Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                    id="due_date" name="due_date" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_date">Challan Valid Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('valid_date') is-invalid @enderror" 
                                    id="valid_date" name="valid_date" required>
                                @error('valid_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                    id="remarks" name="remarks" rows="3"></textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fa fa-save mr-2"></i>Submit
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" id="resetBtn">
                            <i class="fa fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fa fa-list mr-2"></i>Hostel Form Visibility Periods
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="visibilityTable">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Visibility Type</th>
                                <th>Hostel Type</th>
                                <th>Visibility Start Date</th>
                                <th>Visibility End Date</th>
                                <th> Challan Due Date</th>
                                <th> Challan Valid Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $record)
                                <tr>
                                    <td>{{ $record->sess_name }}</td>
                                    <td>
                                        @php
                                            // Simple way to display visibility type
                                            $visibilityType = $record->visibility_type ?? 'N/A';
                                            if ($visibilityType == 'admission_applicants') {
                                                $visibilityType = 'Admission Applicants';
                                            } elseif ($visibilityType == 'regular_students') {
                                                $visibilityType = 'Regular Students';
                                            } elseif ($visibilityType == 'already_existing_hostel_students') {
                                                $visibilityType = 'Current Hostel Students';
                                            } else {
                                                $visibilityType = ucfirst(str_replace('_', ' ', $visibilityType));
                                            }
                                        @endphp
                                        {{ $visibilityType }}
                                    </td>
                                    <td>
                                        @if($record->hostel_type_id == 0 || empty($record->hostel_type_id))
                                            All Hostels
                                        @else
                                            {{ $record->hostel_type_name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $record->start_date }}</td>
                                    <td>{{ $record->end_date }}</td>
                                    <td>{{ $record->due_date }}</td>
                                    <td>{{ $record->valid_date }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm edit-record"
                                            data-id="{{ $record->id }}"
                                            data-start-date="{{ $record->start_date }}"
                                            data-end-date="{{ $record->end_date }}"
                                            data-session="{{ $record->sess_id }}"
                                            data-visibility-type="{{ $record->visibility_type }}"
                                            data-hostel-type-id="{{ $record->hostel_type_id ?? '0' }}"
                                            data-due-date="{{ $record->due_date }}"
                                            data-valid-date="{{ $record->valid_date }}"
                                            data-remarks="{{ $record->remarks }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-record"
                                            data-id="{{ $record->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Show toast notifications from session flash messages
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
    // Initialize Select2 only on hostel dropdown (not on visibility_type)
    $('#hostel_type_id').select2({
        dropdownParent: $('#hostelVisibilityForm').closest('.card-body'),
        width: '100%',
        closeOnSelect: false
    });
    
    // Handle "All Hostels" selection logic
    $('#hostel_type_id').on('select2:select', function(e) {
        var selectedData = e.params.data;
        var selectedId = String(selectedData.id);
        var currentValues = $(this).val();
        var self = $(this);
        
        // If "All Hostels" (0) is selected, clear all other selections immediately
        if (selectedId === '0') {
            setTimeout(function() {
                self.val(['0']).trigger('change');
            }, 100);
            return;
        }
        
        // If any hostel other than "All Hostels" is selected, remove "All Hostels" from selection
        if (selectedId !== '0') {
            if (currentValues && (currentValues.includes('0') || currentValues.includes(0))) {
                var newValues = [];
                for (var i = 0; i < currentValues.length; i++) {
                    var val = String(currentValues[i]);
                    if (val !== '0') {
                        newValues.push(currentValues[i]);
                    }
                }
                setTimeout(function() {
                    self.val(newValues).trigger('change');
                }, 100);
            }
        }
    });
    
    // Initialize DataTable
    $('#visibilityTable').DataTable({
        responsive: true,
        order: [[0, 'desc']]
    });



    function formatDate(dateStr) {
    if (!dateStr) return '';
    return dateStr.split(' ')[0]; // "2025-06-17 00:00:00" → "2025-06-17"
}

    // Handle Edit Record (use event delegation for DataTable)
    $(document).on('click', '.edit-record', function() {
        const record = $(this).data();
        
        // Set record id
        $('#recordId').val(record.id);
       
        // Set dates
        $('#start_date').val(formatDate(record.startDate));
        $('#end_date').val(formatDate(record.endDate));
        $('#due_date').val(formatDate(record.dueDate));
        $('#valid_date').val(formatDate(record.validDate));

        // Set dropdowns
        $('#session').val(record.session);
        $('#visibility_type').val(record.visibilityType);
        
        // Set hostel type ids (can be comma-separated string like "1,2,3")
        var hostelTypeId = record.hostelTypeId;
        
        // Clear all selections first
        $('#hostel_type_id').val(null).trigger('change');
        
        // If empty or 0, select "All Hostels"
        if (!hostelTypeId || hostelTypeId == '' || hostelTypeId == '0') {
            $('#hostel_type_id').val(['0']).trigger('change');
        } else {
            // Split comma-separated values and select each option
            var hostelIds = hostelTypeId.split(',');
            var selectedIds = [];
            for (var i = 0; i < hostelIds.length; i++) {
                var id = hostelIds[i].trim();
                if (id != '' && id != '0') {
                    selectedIds.push(id);
                }
            }
            if (selectedIds.length > 0) {
                $('#hostel_type_id').val(selectedIds).trigger('change');
            } else {
                $('#hostel_type_id').val(['0']).trigger('change');
            }
        }
     
        // Set remarks
        $('#remarks').val(record.remarks);
        
        // Change form title and button
        $('#formTitle').text('Update Hostel Form Visibility Period');
        $('#submitBtn').html('<i class="fa fa-save mr-2"></i>Update');
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $("#hostelVisibilityForm").offset().top - 20
        }, 500);
    });

    // Handle Delete Record (use event delegation for DataTable)
    $(document).on('click', '.delete-record', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: `{{ route('delete-hostel-form-visibility') }}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message || 'Record deleted successfully');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('error', response.message || 'Error deleting record');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error deleting record. Please try again.';
                    showToast('error', errorMsg);
                }
            });
        }
    });

    // Handle Reset Form
    $('#resetBtn').click(function() {
        $('#hostelVisibilityForm')[0].reset();
        $('#recordId').val('');
        $('#visibility_type').val('');
        // Reset hostel dropdown to "All Hostels"
        $('#hostel_type_id').val(['0']).trigger('change');
        $('#formTitle').text('Add Hostel Form Visibility Period');
        $('#submitBtn').html('<i class="fa fa-save mr-2"></i>Submit');
    });

    // Form Validation
    $('#hostelVisibilityForm').submit(function(e) {
        // Check if hostel_type_id is selected
        var selectedHostels = $('#hostel_type_id').val();
        if (!selectedHostels || selectedHostels.length === 0) {
            showToast('error', 'Please select at least one hostel');
            e.preventDefault();
            return false;
        }
        
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        const dueDate = new Date($('#due_date').val());
        const validDate = new Date($('#valid_date').val());

        if (endDate < startDate) {
            showToast('error', 'End date cannot be earlier than start date');
            e.preventDefault();
            return false;
        }

        if (dueDate < startDate) {
            showToast('error', 'Due date cannot be earlier than start date');
            e.preventDefault();
            return false;
        }

        if (validDate < startDate) {
            showToast('error', 'Valid date cannot be earlier than start date');
            e.preventDefault();
            return false;
        }

        return true;
    });
});
</script>
