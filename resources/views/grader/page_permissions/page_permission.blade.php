@extends('layouts.app')
@section('title') BNU CMS - Page Permissions @endsection




@include('components.toast-notifications')

@section('content')

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: none;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.pages-list {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fff;
    padding: 15px;
    
    /* Modern scrollbar styling */
    scrollbar-width: thin;
    scrollbar-color: #007bff #f8f9fa;
}

/* Webkit browsers (Chrome, Safari, Edge) */
.pages-list::-webkit-scrollbar {
    width: 8px;
}

.pages-list::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 10px;
    margin: 5px;
}

.pages-list::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 10px;
    border: 2px solid #f8f9fa;
}

.pages-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.pages-list::-webkit-scrollbar-corner {
    background: #f8f9fa;
}

.page-list-item {
    padding: 10px 12px;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 6px;
    margin-bottom: 2px;
}

.page-list-item:last-child {
    border-bottom: none;
}

.page-list-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.page-list-item.assigned {
    background: linear-gradient(135deg, #f0f8f0 0%, #e8f5e8 100%);
    border-left: 3px solid #28a745;
    box-shadow: 0 1px 3px rgba(40, 167, 69, 0.1);
}

.page-list-item.assigned:hover {
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
    transform: translateX(2px);
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.15);
}

.page-list-item.assigned .page-checkbox:checked {
    background-color: #28a745;
    border-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
}

.page-name {
    font-weight: 500;
    color: #495057;
    font-size: 0.9rem;
    letter-spacing: 0.2px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.page-checkbox {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: 3px;
    transition: all 0.2s ease;
}

.page-checkbox:checked {
    background-color: #007bff;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.page-checkbox:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.form-check {
    display: flex;
    align-items: center;
    margin-bottom: 0;
    padding: 8px 0;
}

.form-check-label {
    flex: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #495057;
    transition: color 0.2s ease;
}

.form-check-label:hover {
    color: #007bff;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 2px;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Modern Radio Button Styling */
.modern-radio-container {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.modern-radio-option {
    flex: 1;
    position: relative;
}

.modern-radio-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.modern-radio-card {
    position: relative;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.modern-radio-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 10px;
    z-index: 0;
}

.modern-radio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
    border-color: #007bff;
}

.modern-radio-input:checked + .modern-radio-card {
    border-color: #007bff;
    background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
    transform: translateY(-2px);
}

.modern-radio-input:checked + .modern-radio-card::before {
    opacity: 0.05;
}

.radio-icon {
    position: relative;
    z-index: 1;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.modern-radio-input:checked + .modern-radio-card .radio-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.radio-icon i {
    color: white;
    font-size: 24px;
    transition: all 0.3s ease;
}

.radio-content {
    position: relative;
    z-index: 1;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.radio-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 1rem;
    transition: color 0.3s ease;
}

.modern-radio-input:checked + .modern-radio-card .radio-title {
    color: #007bff;
    font-weight: 700;
}

.radio-description {
    color: #6c757d;
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.4;
    transition: color 0.3s ease;
}

.modern-radio-input:checked + .modern-radio-card .radio-description {
    color: #495057;
}

.radio-checkmark {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 25px;
    height: 25px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 2;
}

.modern-radio-input:checked + .modern-radio-card .radio-checkmark {
    opacity: 1;
    transform: scale(1);
}

.radio-checkmark i {
    color: white;
    font-size: 12px;
    font-weight: bold;
}

/* Hover effects */
.modern-radio-card:hover .radio-icon {
    transform: scale(1.05);
}

.modern-radio-input:checked + .modern-radio-card:hover .radio-icon {
    transform: scale(1.15);
}

/* Responsive design */
@media (max-width: 768px) {
    .modern-radio-container {
        flex-direction: column;
        gap: 10px;
    }
    
    .modern-radio-card {
        padding: 15px;
    }
    
    .radio-icon {
        width: 50px;
        height: 50px;
        margin-bottom: 10px;
    }
    
    .radio-icon i {
        font-size: 20px;
    }
    
    .radio-title {
        font-size: 0.9rem;
    }
    
    .radio-description {
        font-size: 0.8rem;
    }
}

/* Animation for initial load */
.modern-radio-option {
    animation: fadeInUp 0.6s ease forwards;
}

.modern-radio-option:nth-child(2) {
    animation-delay: 0.1s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Read-only permission styling */
.readonly-permission {
    opacity: 0.7;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-left: 3px solid #6c757d !important;
    position: relative;
}

.readonly-permission::after {
    content: '🔒';
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 12px;
    opacity: 0.6;
}

.readonly-permission .page-checkbox:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.readonly-permission .form-check-label {
    cursor: not-allowed;
    color: #6c757d;
}

.readonly-permission:hover {
    transform: none !important;
    box-shadow: none !important;
}

.readonly-permission .page-name {
    color: #6c757d;
    font-style: italic;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-shield-alt me-2"></i> Page Permissions Management
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle mr-2"></i> Please fix the following errors:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <!-- Group Selection -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-users me-2 text-primary"></i> Assign Page Rights to Group
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Method 1: Search User -->
                            <div class="form-group mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="fa fa-search me-1"></i> Method 1: Search User (Auto-Detect Group)
                                </label>
                                <select class="form-control select2-show-search" id="user_id" name="user_id" style="width: 100%;">
                                    <option value="">-- Search by User Name --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->username }}" data-group-id="{{ $user->group_id }}" data-group-name="{{ $user->group_name }}">
                                            {{ $user->full_name }} - [{{ $user->group_name }}] - {{ $user->emp_email ?? 'No Email' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fa fa-lightbulb me-1"></i> Don't know the group? Search for a user and we'll auto-detect their group
                                </small>
                            </div>
                            
                            <!-- Divider -->
                            <div class="text-center my-3">
                                <span class="badge bg-primary text-white px-3 py-2">
                                    <i class="fa fa-arrow-down me-1"></i> OR <i class="fa fa-arrow-down ms-1"></i>
                                </span>
                            </div>
                            
                            <!-- Method 2: Direct Group Selection -->
                            <div class="form-group">
                                <label for="group_id" class="form-label">
                                    <i class="fa fa-users me-1"></i> Method 2: Direct Group Selection <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2-show-search" id="group_id" name="group_id" style="width: 100%;">
                                    <option value="">-- Select Group Directly --</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->group_id }}" data-group-name="{{ $group->group_name }}">
                                            <i class="fa fa-users"></i> {{ $group->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fa fa-info-circle me-1"></i> Know the group? Select it directly for quick access
                                </small>
                            </div>
                            
                            <!-- Assignment Type Radio Buttons -->
                            <div class="form-group mt-3">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fa fa-cogs me-1"></i> Assignment Type
                                </label>
                                <div class="modern-radio-container">
                                    <div class="modern-radio-option" data-value="group">
                                        <input class="modern-radio-input" type="radio" name="assignment_type" id="group_wise" value="group" checked>
                                        <div class="modern-radio-card">
                                            <div class="radio-icon">
                                                <i class="fa fa-users"></i>
                                            </div>
                                            <div class="radio-content">
                                                <h6 class="radio-title">Group-wise</h6>
                                                <p class="radio-description">Assign permissions to entire group</p>
                                            </div>
                                            <div class="radio-checkmark">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modern-radio-option" data-value="user">
                                        <input class="modern-radio-input" type="radio" name="assignment_type" id="user_wise" value="user">
                                        <div class="modern-radio-card">
                                            <div class="radio-icon">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="radio-content">
                                                <h6 class="radio-title">Single User</h6>
                                                <p class="radio-description">Assign permissions to specific user</p>
                                            </div>
                                            <div class="radio-checkmark">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="fa fa-info-circle me-1"></i> Choose whether to assign permissions to the entire group or to a specific user
                                </small>
                            </div>

                            <div id="group-info" class="mt-3">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fa fa-info-circle me-1"></i> Group Information
                                    </h6>
                                    <p class="mb-0" id="selected-group-name">No group selected</p>
                                    <small class="text-muted">
                                        <i class="fa fa-clock me-1"></i> 
                                        <span id="permission-count">0 assigned, 0 selected</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-cogs me-2 text-primary"></i> Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" id="select-all">
                                    <i class="fa fa-check-square me-1"></i> Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="deselect-all">
                                    <i class="fa fa-square me-1"></i> Deselect All
                                </button>
                                <button type="button" class="btn btn-success" id="save-permissions">
                                    <i class="fa fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages Row -->
            <div class="row mt-3" id="messages-row" style="display: none;">
                <div class="col-12">
                    <div id="dynamic-messages"></div>
                </div>
            </div>

            <!-- Pages List Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-file-alt me-2 text-primary"></i> Page Permissions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="pages-container">
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-3x mb-3"></i>
                                    <h5>Choose Your Method</h5>
                                    <p>Either search for a user to auto-detect their group, or select a group directly</p>
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

@push('page-scripts')
<script>
$(document).ready(function() {
    var selectedGroupId = null;
    var currentPermissions = [];
    var allPages = [];



    // Initialize group info on page load
    updateGroupInfo();

    // Load pages data
    function loadPages() {
        $.ajax({
            url: '{{ url("api/page-permissions/pages") }}',
            type: 'GET',
            data: {
                api_token: '{{ auth()->user()->api_token }}'
            },
            beforeSend: function() {
                $('#pages-container').html('<div class="text-center py-5"><div class="loading-spinner"></div><p class="mt-3">Loading pages...</p></div>');
            },
            success: function(response) {
                allPages = response.data || [];
                if (selectedGroupId) {
                   
                    loadGroupPermissions(selectedGroupId);
                } else {
                    renderPagesList();
                }
            },
            error: function(xhr) {
                console.error('Error loading pages:', xhr);
                $('#pages-container').html('<div class="alert alert-danger">Failed to load pages</div>');
            }
        });
    }

    // Load permissions for selected group
    function loadGroupPermissions(groupId) {
     
        if (!groupId) return;

        $('#pages-container').html('<div class="text-center py-5"><div class="loading-spinner"></div><p class="mt-3">Loading permissions...</p></div>');

        // Get assignment type and username
        var assignmentType = $('input[name="assignment_type"]:checked').val();
        var username = $('#user_id').val() || null;

        $.ajax({
            url: '{{ url("api/page-permissions/group-pages") }}',
            type: 'GET',
            data: { 
                group_id: groupId, 
                assignment_type: assignmentType,
                username: username,
                api_token: '{{ auth()->user()->api_token }}' 
            },
            success: function(response) {
              
                currentPermissions = response || [];
                renderPagesList();
                updateGroupInfo();
            },
            error: function(xhr) {
                console.error('Error loading permissions:', xhr);
                $('#pages-container').html('<div class="alert alert-danger">Failed to load permissions</div>');
            }
        });
    }

    // Render pages list with checkboxes
    function renderPagesList() {
    
        if (!selectedGroupId) {
            $('#pages-container').html(`
                <div class="text-center text-muted py-5">
                    <i class="fa fa-users fa-3x mb-3"></i>
                    <h5>Select a Group</h5>
                    <p>Choose a group from the left panel to manage page permissions</p>
                </div>
            `);
            return;
        }

        if (!allPages.length) {
            $('#pages-container').html('<div class="alert alert-warning">No pages found</div>');
            return;
        }

        var assignmentType = $('input[name="assignment_type"]:checked').val();
        var username = $('#user_id').val();

        var html = '<div class="pages-list">';
        html += '<div class="row">';
        
        allPages.forEach(function(page, index) {
            var isAssigned = currentPermissions.some(function(perm) {
                return perm.gd_page_id == page.page_id && perm.gd_allowed == 'True';
            });

            // Check if this permission is read-only (for single user assignment)
            var isReadOnly = false;
            var readOnlyReason = '';
            
            if (assignmentType === 'user' && username) {
                // For single user assignment, check if permission comes from group (gd_allowed_username is NULL)
                var groupPermission = currentPermissions.find(function(perm) {
                    return perm.gd_page_id == page.page_id && perm.gd_allowed == 'True' && !perm.gd_allowed_username;
                });
                
                if (groupPermission) {
                    isReadOnly = true;
                    readOnlyReason = ' (Already Assigned to Group)';
                }
            }

            // Start new row every 3 items
            if (index % 3 === 0) {
                html += '</div><div class="row">';
            }

            var readOnlyAttr = isReadOnly ? 'disabled' : '';
            var readOnlyClass = isReadOnly ? 'readonly-permission' : '';

            html += `
                <div class="col-md-4">
                    <div class="page-list-item ${isAssigned ? 'assigned' : ''} ${readOnlyClass}">
                        <div class="form-check">
                            <input class="form-check-input page-checkbox" type="checkbox" 
                                   id="page_${page.page_id}" value="${page.page_id}"
                                   ${isAssigned ? 'checked' : ''} ${readOnlyAttr}>
                            <label class="form-check-label" for="page_${page.page_id}">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-folder me-2 mr-2 mt-1 text-info" style="font-size: 15px; opacity: 0.8;"></i>
                                    <span class="page-name">${page.page_name || 'Unnamed Page'}</span>
                                    ${readOnlyReason ? '<small class="text-muted ms-2">' + readOnlyReason + '</small>' : ''}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div></div>';
        $('#pages-container').html(html);
    }

    // Update group information
    function updateGroupInfo() {
        if (!selectedGroupId) {
            $('#selected-group-name').text('No group selected');
            $('#permission-count').text('0 assigned, 0 selected');
            return;
        }

        var selectedOption = $('#group_id option:selected');
        var groupName = selectedOption.data('group-name') || selectedOption.text();
        var permissionCount = currentPermissions.length;
        var selectedCount = $('.page-checkbox:checked').length;
        var assignmentType = $('input[name="assignment_type"]:checked').val();
        var username = $('#user_id').val();
        
        var assignmentText = assignmentType === 'user' ? 
            `Single User (${username || 'No user selected'})` : 
            'Group-wise';

        $('#selected-group-name').text(groupName);
        $('#permission-count').text(permissionCount + ' assigned, ' + selectedCount + ' selected - ' + assignmentText);
    }

    // User selection change - automatically select group
    $('#user_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var groupId = selectedOption.data('group-id');
        var groupName = selectedOption.data('group-name');
        
        if (groupId) {
            // Set the group dropdown to the user's group
            $('#group_id').val(groupId).trigger('change');
            
            // Show a small notification
            showAlert(`✅ Group auto-detected: ${groupName}`, 'success');
        } else {
            // Clear group selection if no user selected
            $('#group_id').val('').trigger('change');
        }
    });

    // Group selection change
    $('#group_id').on('change', function() {
        selectedGroupId = $(this).val();
        
        // Check if the selected group matches the user's group
        var selectedUserOption = $('#user_id option:selected');
        var userGroupId = selectedUserOption.data('group-id');
        
        // If user is selected and group doesn't match, clear user selection
        if (selectedUserOption.val() && userGroupId && userGroupId != selectedGroupId) {
            $('#user_id').val('').trigger('change');
            showAlert('🔄 User selection cleared - you chose a different group manually', 'info');
        }
        
        // Immediately update group info
        updateGroupInfo();
        
        if (selectedGroupId) {
            if (allPages.length > 0) {
                loadGroupPermissions(selectedGroupId);
            } else {
                loadPages();
            }
        } else {
            renderPagesList();
        }
    });

    // Assignment type radio button change
    $('input[name="assignment_type"]').on('change', function() {
        if (selectedGroupId && allPages.length > 0) {
            loadGroupPermissions(selectedGroupId);
            showAlert('🔄 Reloading permissions for new assignment type', 'info');
        }
    });

    // Modern radio button click handling
    $('.modern-radio-card').on('click', function() {
        var radioInput = $(this).siblings('.modern-radio-input');
        radioInput.prop('checked', true).trigger('change');
    });

    // Select all pages
    $('#select-all').on('click', function() {
        $('.page-checkbox:not(:disabled)').prop('checked', true).trigger('change');
        updateGroupInfo();
    });

    // Deselect all pages
    $('#deselect-all').on('click', function() {
        $('.page-checkbox:not(:disabled)').prop('checked', false).trigger('change');
        updateGroupInfo();
    });

    // Save permissions
    $('#save-permissions').on('click', function() {
        if (!selectedGroupId) {
            alert('Please select a group first');
            return;
        }

        var assignmentType = $('input[name="assignment_type"]:checked').val();
        var username = $('#user_id').val() || null;

        // Validate user selection for user-wise assignment
        if (assignmentType === 'user' && !username) {
            showAlert('⚠️ Please select a user for single user assignment', 'error');
            return;
        }

        // Get selected pages (excluding read-only ones)
        var selectedPages = [];
        $('.page-checkbox:checked:not(:disabled)').each(function() {
            selectedPages.push($(this).val());
        });

      

        var btn = $(this);
        var originalText = btn.html();
        btn.html('<div class="loading-spinner"></div> Saving...').prop('disabled', true);

        $.ajax({
            url: '{{ url("api/page-permissions/store") }}',
            type: 'POST',
            data: {
                group_id: selectedGroupId,
                assignment_type: assignmentType,
                username: username,
                page_ids: selectedPages,
                _token: '{{ csrf_token() }}',
                api_token: '{{ auth()->user()->api_token }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Reload permissions
                    loadGroupPermissions(selectedGroupId);
                    showAlert(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert(response.message || 'Failed to update permissions', 'error');
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update permissions';
                showAlert(message, 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Checkbox change event
    $(document).on('change', '.page-checkbox', function() {
        var pageItem = $(this).closest('.page-list-item');
        
        // Don't allow changes to read-only permissions
        if ($(this).is(':disabled')) {
            return;
        }
        
        if ($(this).is(':checked')) {
            pageItem.addClass('assigned');
        } else {
            pageItem.removeClass('assigned');
        }
        // Update the count display
        updateGroupInfo();
    });

    // Show alert function
    function showAlert(message, type) {
        var alertClass, icon;
        
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                icon = 'check-circle';
                break;
            case 'error':
                alertClass = 'alert-danger';
                icon = 'exclamation-triangle';
                break;
            case 'info':
                alertClass = 'alert-info';
                icon = 'info-circle';
                break;
            default:
                alertClass = 'alert-info';
                icon = 'info-circle';
        }
        
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fa fa-${icon} mr-2"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#dynamic-messages').html(alertHtml);
        $('#messages-row').show();
        
        setTimeout(function() {
            $('#messages-row').fadeOut();
        }, 5000);
    }

    // Auto-hide only success/error alerts, not group info
    setTimeout(function() {
        $('.alert:not(#group-info .alert)').fadeOut();
    }, 5000);

    // Initial load
    loadPages();
});
</script>
@endpush