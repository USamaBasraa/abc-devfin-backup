@extends('layouts.app')

@section('content')
    <!-- Include Toast Component -->
    @include('components.toast-notifications')

    <style>
        .table-hover tbody tr:hover .btn-outline-primary,
        .table-hover tbody tr:hover .btn-outline-warning,
        .table-hover tbody tr:hover .btn-outline-success,
        .table-hover tbody tr:hover .btn-outline-danger {
            color: inherit;
            background-color: #fff !important;
        }
        .table-hover tbody tr:hover .btn-outline-primary { color: #4555C3 !important; border-color: #4555C3 !important; }
        .table-hover tbody tr:hover .btn-outline-warning { color: #856404 !important; border-color: #ffc107 !important; }
        .table-hover tbody tr:hover .btn-outline-success { color: #28a745 !important; border-color: #28a745 !important; }
        .table-hover tbody tr:hover .btn-outline-danger { color: #dc3545 !important; border-color: #dc3545 !important; }
        .row-disabled { opacity: 0.65; }
        .action-btns .btn { margin-right: 4px; margin-bottom: 4px; }
    </style>
  
    <div class="mt-4">
        <!-- Overlay Card -->
        <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 12px; overflow: hidden;">
            <!-- Card Header with Light Background -->
            <div class="card-header" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-bottom: 2px solid #4555C3; padding: 20px 25px;">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0" style="font-weight: 600; letter-spacing: -0.5px; color: #4555C3;">
                        <i class="fa fa-building mr-2"></i>Hostel Type & Floor Management
                    </h4>
                    <span class="badge" style="background: #4555C3; color: #fff; font-size: 12px; padding: 6px 12px; border-radius: 6px;">
                        <i class="fa fa-cog mr-1"></i>Management Panel
                    </span>
                </div>
            </div>

            <!-- Card Body with Padding -->
            <div class="card-body" style="padding: 30px;">
                <!-- Hostel Type Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mb-3" style="background: #ffffff; border-radius: 10px;">
                            <div class="card-body p-4">
                                <h5 class="text-primary mb-4" style="font-weight: 600; border-bottom: 2px solid #4555C3; padding-bottom: 10px;">
                                    <i class="fa fa-building-o mr-2"></i>Manage Hostel Types
                                </h5>
                                <div class="border rounded p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border: 1px solid #e0e5f0 !important;">
                            <form id="hostelTypeForm">
                                <input type="hidden" id="ht-id" name="ht_id" value="">
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ht-name" class="font-weight-bold"><i class="fa fa-tag mr-1"></i>Hostel Type Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="ht-name" name="ht_name" placeholder="e.g., Boys Hostel, Girls Hostel" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ht-gender" class="font-weight-bold"><i class="fa fa-venus-mars mr-1"></i>Gender <span class="text-danger">*</span></label>
                                            <select class="form-control" id="ht-gender" name="ht_hostel_type" required>
                                                <option value="">Select Gender</option>
                                                <option value="1">Boys</option>
                                                <option value="2">Girls</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ht-location" class="font-weight-bold"><i class="fa fa-map-marker mr-1"></i>Location <span class="text-danger">*</span></label>
                                            <select class="form-control" id="ht-location" name="ht_location" required>
                                                <option value="">Select Location</option>
                                                <option value="on_campus">on campus</option>
                                                <option value="off_campus">off campus</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ht-status" class="font-weight-bold"><i class="fa fa-toggle-on mr-1"></i>Status</label>
                                            <select class="form-control" id="ht-status" name="ht_status">
                                                <option value="E">Active</option>
                                                <option value="D">Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">&nbsp;</label>
                                            <div class="d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" id="saveHostelTypeBtn">
                                                    <i class="fa fa-save mr-1"></i>Save Hostel Type
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="resetHostelTypeBtn">
                                                    <i class="fa fa-refresh mr-1"></i>Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Hostel Types List -->
                            <div class="mt-3">
                                <h6 class="font-weight-bold mb-2">Existing Hostel Types:</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead class="thead-light" style="background: linear-gradient(135deg, #f8f9ff 0%, #e9ecef 100%); border-bottom: 2px solid #4555C3;">
                                            <tr>
                                                <th style="color: #4555C3; font-weight: 600;">ID</th>
                                                <th style="color: #4555C3; font-weight: 600;">Hostel Type Name</th>
                                                <th style="color: #4555C3; font-weight: 600;">Gender</th>
                                                <th style="color: #4555C3; font-weight: 600;">Location</th>
                                                <th style="color: #4555C3; font-weight: 600;">Status</th>
                                                <th style="color: #4555C3; font-weight: 600;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="hostelTypesTableBody">
                                            @foreach($hostelTypes as $type)
                                                <tr class="{{ ($type->ht_status ?? 'E') !== 'E' ? 'row-disabled' : '' }}">
                                                    <td>{{ $type->ht_id }}</td>
                                                    <td>{{ $type->ht_name }}</td>
                                                    <td>
                                                        @if($type->ht_hostel_type == 1)
                                                            <span class="badge badge-info"><i class="fa fa-mars mr-1"></i>Boys</span>
                                                        @elseif($type->ht_hostel_type == 2)
                                                            <span class="badge badge-pink" style="background-color: #e91e63; color: white;"><i class="fa fa-venus mr-1"></i>Girls</span>
                                                        @else
                                                            <span class="badge badge-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($type->ht_location == 'off_campus' || $type->ht_location == 'out_of_campus')
                                                            <span class="badge badge-warning">off campus</span>
                                                        @else
                                                            <span class="badge badge-success">on campus</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(($type->ht_status ?? 'E') === 'E')
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Disabled</span>
                                                        @endif
                                                    </td>
                                                    <td class="action-btns text-nowrap">
                                                        <button type="button" class="btn btn-sm btn-outline-primary edit-hostel-type-btn" 
                                                            data-id="{{ $type->ht_id }}" 
                                                            data-name="{{ $type->ht_name }}" 
                                                            data-location="{{ $type->ht_location ?? 'on_campus' }}"
                                                            data-gender="{{ $type->ht_hostel_type ?? '' }}"
                                                            data-status="{{ $type->ht_status ?? 'E' }}">
                                                            <i class="fa fa-edit mr-1"></i>Edit
                                                        </button>
                                                        @if(($type->ht_status ?? 'E') === 'E')
                                                            <button type="button" class="btn btn-sm btn-outline-warning toggle-hostel-type-btn" data-id="{{ $type->ht_id }}" data-status="E">
                                                                <i class="fa fa-ban mr-1"></i>Disable
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-success toggle-hostel-type-btn" data-id="{{ $type->ht_id }}" data-status="D">
                                                                <i class="fa fa-check mr-1"></i>Enable
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-hostel-type-btn" data-id="{{ $type->ht_id }}" data-name="{{ $type->ht_name }}">
                                                            <i class="fa fa-trash mr-1"></i>Delete
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
                </div>

                </div>
                </div>
                </div>

                <!-- Floor Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mb-3" style="background: #ffffff; border-radius: 10px;">
                            <div class="card-body p-4">
                                <h5 class="text-primary mb-4" style="font-weight: 600; border-bottom: 2px solid #4555C3; padding-bottom: 10px;">
                                    <i class="fa fa-level-up mr-2"></i>Manage Floors
                                </h5>
                                <div class="border rounded p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border: 1px solid #e0e5f0 !important;">
                            <form id="floorForm">
                                <input type="hidden" id="hf-id" name="hf_id" value="">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="floor-ht-id" class="font-weight-bold"><i class="fa fa-building mr-1"></i>Hostel Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="floor-ht-id" name="ht_id" required>
                                                <option value="">Select Hostel Type</option>
                                                @foreach($hostelTypes as $type)
                                                    <option value="{{ $type->ht_id }}" data-location="{{ $type->ht_location ?? 'on_campus' }}">
                                                        {{ $type->ht_name }}
                                                        @if($type->ht_location == 'off_campus' || $type->ht_location == 'out_of_campus')
                                                            (off campus)
                                                        @endif
                                                        @if(($type->ht_status ?? 'E') !== 'E')
                                                            (Disabled)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Select the hostel this floor belongs to</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="hf-name" class="font-weight-bold"><i class="fa fa-hashtag mr-1"></i>Floor Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="hf-name" name="hf_name" placeholder="e.g., Ground Floor, 1st Floor" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="hf-status" class="font-weight-bold"><i class="fa fa-toggle-on mr-1"></i>Status</label>
                                            <select class="form-control" id="hf-status" name="hf_status">
                                                <option value="E">Active</option>
                                                <option value="D">Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">&nbsp;</label>
                                            <div class="d-flex">
                                                <button type="submit" class="btn btn-primary mr-2" id="saveFloorBtn">
                                                    <i class="fa fa-save mr-1"></i>Save Floor
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="resetFloorBtn">
                                                    <i class="fa fa-refresh mr-1"></i>Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Floors List -->
                            <div class="mt-3">
                                <h6 class="font-weight-bold mb-2">Existing Floors:</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead class="thead-light" style="background: linear-gradient(135deg, #f8f9ff 0%, #e9ecef 100%); border-bottom: 2px solid #4555C3;">
                                            <tr>
                                                <th style="color: #4555C3; font-weight: 600;">ID</th>
                                                <th style="color: #4555C3; font-weight: 600;">Floor Name</th>
                                                <th style="color: #4555C3; font-weight: 600;">Hostel Type</th>
                                                <th style="color: #4555C3; font-weight: 600;">Status</th>
                                                <th style="color: #4555C3; font-weight: 600;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="floorsTableBody">
                                            @foreach($floors as $floor)
                                                @php
                                                    $hostelType = $hostelTypes->firstWhere('ht_id', $floor->hf_new_hostel_type);
                                                    $hostelTypeName = $hostelType ? $hostelType->ht_name : 'N/A';
                                                @endphp
                                                <tr class="{{ ($floor->hf_status ?? 'E') !== 'E' ? 'row-disabled' : '' }}">
                                                    <td>{{ $floor->hf_id }}</td>
                                                    <td>{{ $floor->hf_name }}</td>
                                                    <td>{{ $hostelTypeName }}</td>
                                                    <td>
                                                        @if(($floor->hf_status ?? 'E') === 'E')
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Disabled</span>
                                                        @endif
                                                    </td>
                                                    <td class="action-btns text-nowrap">
                                                        <button type="button" class="btn btn-sm btn-outline-primary edit-floor-btn" 
                                                            data-id="{{ $floor->hf_id }}" 
                                                            data-name="{{ $floor->hf_name }}" 
                                                            data-new-hostel-type="{{ $floor->hf_new_hostel_type }}"
                                                            data-status="{{ $floor->hf_status ?? 'E' }}">
                                                            <i class="fa fa-edit mr-1"></i>Edit
                                                        </button>
                                                        @if(($floor->hf_status ?? 'E') === 'E')
                                                            <button type="button" class="btn btn-sm btn-outline-warning toggle-floor-btn" data-id="{{ $floor->hf_id }}" data-status="E">
                                                                <i class="fa fa-ban mr-1"></i>Disable
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-success toggle-floor-btn" data-id="{{ $floor->hf_id }}" data-status="D">
                                                                <i class="fa fa-check mr-1"></i>Enable
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-floor-btn" data-id="{{ $floor->hf_id }}" data-name="{{ $floor->hf_name }}">
                                                            <i class="fa fa-trash mr-1"></i>Delete
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
                </div>
            </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // ============================================
            // HOSTEL TYPE FORM HANDLING
            // ============================================

            // Handle Hostel Type Form Submit
            $('#hostelTypeForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: '{{ csrf_token() }}',
                    ht_id: $('#ht-id').val(),
                    ht_name: $('#ht-name').val(),
                    ht_location: $('#ht-location').val(),
                    ht_hostel_type: $('#ht-gender').val(),
                    ht_status: $('#ht-status').val()
                };

                // Show loading state
                const btn = $('#saveHostelTypeBtn');
                const originalHtml = btn.html();
                btn.html('<i class="fa fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

                $.ajax({
                    url: '{{ url("/hostel-management/save-hostel-type") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showToast('error', response.message || 'An error occurred.');
                            btn.html(originalHtml).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                        showToast('error', errorMsg);
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // Reset Hostel Type Form
            $('#resetHostelTypeBtn').on('click', function() {
                $('#ht-id').val('');
                $('#ht-name').val('');
                $('#ht-location').val('');
                $('#ht-gender').val('');
                $('#ht-status').val('E');
                $('#saveHostelTypeBtn').html('<i class="fa fa-save mr-1"></i>Save Hostel Type');
            });

            // Edit Hostel Type Button
            $(document).on('click', '.edit-hostel-type-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = $(this);
                const id = btn.attr('data-id');
                const name = btn.attr('data-name');
                let location = btn.attr('data-location') || 'on_campus';
                const gender = btn.attr('data-gender') || '';
                const status = btn.attr('data-status') || 'E';
                
                if (location === 'out_of_campus') {
                    location = 'off_campus';
                }

                $('#ht-id').val(id || '');
                $('#ht-name').val(name || '');
                $('#ht-location').val(location || 'on_campus');
                $('#ht-gender').val(gender || '');
                $('#ht-status').val(status || 'E');
                
                // Update button text
                $('#saveHostelTypeBtn').html('<i class="fa fa-edit mr-1"></i>Update Hostel Type');
                
                // Visual feedback
                btn.addClass('btn-success').removeClass('btn-outline-primary');
                setTimeout(function() {
                    btn.removeClass('btn-success').addClass('btn-outline-primary');
                }, 1000);

                // Scroll to form (with null check)
                const hostelTypeForm = $('#hostelTypeForm');
                if (hostelTypeForm.length && hostelTypeForm.offset()) {
                    $('html, body').animate({
                        scrollTop: hostelTypeForm.offset().top - 150
                    }, 600);
                    
                    // Highlight the form with animation
                    hostelTypeForm.css({
                        'border': '2px solid #4555C3',
                        'border-radius': '8px',
                        'padding': '15px',
                        'background-color': '#f8f9ff',
                        'transition': 'all 0.3s ease'
                    });
                    
                    setTimeout(function() {
                        hostelTypeForm.css({
                            'border': '',
                            'border-radius': '',
                            'padding': '',
                            'background-color': '',
                            'transition': ''
                        });
                    }, 3000);
                }
            });

            // ============================================
            // FLOOR FORM HANDLING
            // ============================================

            // Handle Floor Form Submit
            $('#floorForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: '{{ csrf_token() }}',
                    hf_id: $('#hf-id').val(),
                    hf_name: $('#hf-name').val(),
                    ht_id: $('#floor-ht-id').val(),
                    hf_status: $('#hf-status').val()
                };

                // Show loading state
                const btn = $('#saveFloorBtn');
                const originalHtml = btn.html();
                btn.html('<i class="fa fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

                $.ajax({
                    url: '{{ url("/hostel-management/save-floor") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showToast('error', response.message || 'An error occurred.');
                            btn.html(originalHtml).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                        showToast('error', errorMsg);
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // Reset Floor Form
            $('#resetFloorBtn').on('click', function() {
                $('#hf-id').val('');
                $('#hf-name').val('');
                $('#floor-ht-id').val('');
                $('#hf-status').val('E');
                $('#saveFloorBtn').html('<i class="fa fa-save mr-1"></i>Save Floor');
            });

            // Edit Floor Button
            $(document).on('click', '.edit-floor-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = $(this);
                const id = btn.attr('data-id');
                const name = btn.attr('data-name');
                const newHostelType = btn.attr('data-new-hostel-type');
                const status = btn.attr('data-status') || 'E';

                $('#hf-id').val(id || '');
                $('#hf-name').val(name || '');
                $('#floor-ht-id').val(newHostelType || '');
                $('#hf-status').val(status || 'E');
                
                // Update button text
                $('#saveFloorBtn').html('<i class="fa fa-edit mr-1"></i>Update Floor');
                
                // Visual feedback
                btn.addClass('btn-success').removeClass('btn-outline-primary');
                setTimeout(function() {
                    btn.removeClass('btn-success').addClass('btn-outline-primary');
                }, 1000);

                // Scroll to form (with null check)
                const floorForm = $('#floorForm');
                if (floorForm.length && floorForm.offset()) {
                    $('html, body').animate({
                        scrollTop: floorForm.offset().top - 150
                    }, 600);
                    
                    // Highlight the form with animation
                    floorForm.css({
                        'border': '2px solid #4555C3',
                        'border-radius': '8px',
                        'padding': '15px',
                        'background-color': '#f8f9ff',
                        'transition': 'all 0.3s ease'
                    });
                    
                    setTimeout(function() {
                        floorForm.css({
                            'border': '',
                            'border-radius': '',
                            'padding': '',
                            'background-color': '',
                            'transition': ''
                        });
                    }, 3000);
                }
            });

            function postHostelAction(url, successMessage) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message || successMessage);
                            setTimeout(function() { window.location.reload(); }, 800);
                        } else {
                            showToast('error', response.message || 'Action failed.');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Action failed. Please try again.';
                        showToast('error', errorMsg);
                    }
                });
            }

            $(document).on('click', '.toggle-hostel-type-btn', function() {
                const id = $(this).attr('data-id');
                const currentStatus = $(this).attr('data-status');
                const action = currentStatus === 'E' ? 'disable' : 'enable';
                if (!confirm('Are you sure you want to ' + action + ' this hostel type?')) {
                    return;
                }
                postHostelAction('{{ url('hostel-management/toggle-hostel-type-status') }}/' + id);
            });

            $(document).on('click', '.delete-hostel-type-btn', function() {
                const id = $(this).attr('data-id');
                const name = $(this).attr('data-name');
                if (!confirm('Delete hostel type "' + name + '"? This cannot be undone.')) {
                    return;
                }
                postHostelAction('{{ url('hostel-management/delete-hostel-type') }}/' + id);
            });

            $(document).on('click', '.toggle-floor-btn', function() {
                const id = $(this).attr('data-id');
                const currentStatus = $(this).attr('data-status');
                const action = currentStatus === 'E' ? 'disable' : 'enable';
                if (!confirm('Are you sure you want to ' + action + ' this floor?')) {
                    return;
                }
                postHostelAction('{{ url('hostel-management/toggle-floor-status') }}/' + id);
            });

            $(document).on('click', '.delete-floor-btn', function() {
                const id = $(this).attr('data-id');
                const name = $(this).attr('data-name');
                if (!confirm('Delete floor "' + name + '"? This cannot be undone.')) {
                    return;
                }
                postHostelAction('{{ url('hostel-management/delete-floor') }}/' + id);
            });
        });
    </script>
@endsection
