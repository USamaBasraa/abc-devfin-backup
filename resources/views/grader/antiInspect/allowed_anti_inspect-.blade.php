    @extends('layouts.app')
   
    @section('content')
    @include('components.toast-notifications')
    
    <?php 
    // Check if user has group_id 644 for admin access
    $hasAccessPermission = auth()->check() && auth()->user()->group_id == 644;
    ?>

    @if(!$hasAccessPermission)
    <!-- Access Denied Message -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white py-3">
                    <h4 class="mb-0"><i class="fa fa-exclamation-triangle mr-2"></i>Access Denied</h4>
                </div>
                <div class="card-body py-4 text-center">
                    <div class="mb-4">
                        <i class="fa fa-lock" style="font-size: 64px; color: #dc3545;"></i>
                    </div>
                    <h5>You do not have permission to access this page.</h5>
                    <p class="text-muted">This page is restricted to administrators only.</p>
                    <a href="{{ url('/') }}" class="btn btn-info mt-3">
                        <i class="fa fa-home mr-1"></i> Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    
    <!-- Add custom CSS -->
    <style>
        .card {
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 8px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .main-card-header {
            background: #4285f4;
            position: relative;
            overflow: hidden;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .main-card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(45deg);
        }
        .bg-light {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .card-header.bg-light {
            background-color: #f1f3f4 !important;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4285f4;
            box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
        }
        .btn-primary {
            background-color: #4285f4;
            border-color: #4285f4;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #1a73e8;
            border-color: #1a73e8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-secondary {
            background-color: #5f6368;
            border-color: #5f6368;
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            background-color: #494c50;
            border-color: #494c50;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead th {
            border-top: none;
            font-weight: 600;
            color: #5f6368;
        }
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 30px;
        }
        .badge.bg-success {
            background-color: #34a853 !important;
        }
        .badge.bg-danger {
            background-color: #ea4335 !important;
        }
        .badge.bg-primary {
            background-color: #4285f4 !important;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #5f6368;
        }
        .animated-icon {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .loader {
            display: none;
            width: 100%;
            height: 4px;
            position: relative;
            overflow: hidden;
            background-color: #f3f3f3;
            border-radius: 20px;
            margin: 10px 0;
        }
        .loader:before {
            content: "";
            position: absolute;
            left: -50%;
            width: 50%;
            height: 100%;
            background-color: #4285f4;
            animation: loading 1.5s infinite;
            border-radius: 20px;
        }
        @keyframes loading {
            0% { left: -50%; }
            100% { left: 100%; }
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px 10px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px 10px;
        }
        .status-filter-container {
            padding: 15px;
            background-color: #f1f3f4;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card-title {
            position: relative;
            display: inline-block;
            color: #202124;
        }
        .card-title:after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background-color: #4285f4;
            bottom: -8px;
            left: 0;
        }
        
        /* Google-style alert */
        .alert-info-google {
            background-color: #e8f0fe;
            border: none;
            border-radius: 8px;
            color: #174ea6;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .alert-info-google h5 {
            color: #1a73e8;
            font-weight: 500;
        }
        .alert-info-google p {
            color: #3c4043;
        }
        .alert-info-google .badge {
            font-size: 0.85em;
        }
    </style>
    
    <div class="">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header main-card-header text-white py-3">
                        <h4 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Inspection Access Management</h4>
                    </div>
                    
                    <div class="card-body py-4">
                        <div id="alert-container"></div>
                        <div id="loader" class="loader"></div>
                        
                        <!-- Page description -->
                        <div class="alert-info-google mb-4">
                            <h5 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Purpose of this Page</h5>
                            <p class="mb-0">This management page allows administrators to control which users are authorized to use browser inspection tools (right-click, developer mode, element inspection, etc.) within the application. Only users registered here with <span class="badge bg-success">Enabled</span> status will be able to access these developer tools.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0 card-title" id="form-title">Add New User</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="user-form" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" id="record_id" value="">
                                            
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">
                                                    <i class="fas fa-user text-primary mr-1"></i> User ID
                                                </label>
                                                <input type="text" class="form-control" id="user_id" name="user_id" required maxlength="40" placeholder="Enter user ID">
                                                <span class="text-danger small" id="user_id_error"></span>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="reason" class="form-label">
                                                    <i class="fas fa-comment-alt text-primary mr-1"></i> Reason
                                                </label>
                                                <textarea class="form-control" id="reason" name="reason" rows="3" required maxlength="255" placeholder="Enter reason for allowing inspection"></textarea>
                                                <span class="text-danger small" id="reason_error"></span>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="status" class="form-label">
                                                    <i class="fas fa-toggle-on text-primary mr-1"></i> Status
                                                </label>
                                                <select class="form-control" id="status" name="status" style="height: 38px; width: 100%;">
                                                    <option value="E">Enable</option>
                                                    <option value="D">Disable</option>
                                                </select>
                                                <span class="text-danger small" id="status_error"></span>
                                            </div>
                                            
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                                    <i class="fas fa-save mr-1"></i> Save
                                                </button>
                                                <button type="button" class="btn btn-secondary mt-2" id="cancelBtn" style="display:none;">
                                                    <i class="fas fa-times mr-1"></i> Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0 card-title"><i class="fas fa-list mr-2"></i>Allowed Users List</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Status Filter -->
                                        <div class="status-filter-container">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <label for="status-filter" class="form-label fw-bold mb-md-0">
                                                        <i class="fas fa-filter text-primary mr-1"></i> Filter:
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <select id="status-filter" class="form-control" style="width: 100%;">
                                                        <option value="">All (Show Everyone)</option>
                                                        <option value="Enabled">Enabled Only</option>
                                                        <option value="Disabled">Disabled Only</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                                    <span class="badge bg-success" id="filtered-count">0</span> records found
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="users-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>User ID</th>
                                                        <th>Reason</th>
                                                        <th>Status</th>
                                                        <th>Created Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="users-table-body">
                                                    @foreach($allowedUsers as $user)
                                                    <tr data-id="{{ $user->id }}">
                                                        <td>{{ $user->user_id }}</td>
                                                        <td>{{ $user->reason }}</td>
                                                        <td>
                                                            @if($user->status == 'E')
                                                                <span class="badge bg-success text-white">Enabled</span>
                                                            @else
                                                                <span class="badge bg-danger text-white">Disabled</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('Y-m-d H:i', strtotime($user->created_at)) }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-info edit-btn" 
                                                                    data-id="{{ $user->id }}"
                                                                    data-user_id="{{ $user->user_id }}"
                                                                    data-reason="{{ $user->reason }}"
                                                                    data-status="{{ $user->status }}"
                                                                    data-bs-toggle="tooltip" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger delete-btn" data-id="{{ $user->id }}" data-bs-toggle="tooltip" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
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
    
    <!-- Add CSS link for DataTables and Font Awesome -->
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Add JS for DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('user-form');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const formTitle = document.getElementById('form-title');
            const alertContainer = document.getElementById('alert-container');
            const usersTableBody = document.getElementById('users-table-body');
            const statusFilter = document.getElementById('status-filter');
            const loader = document.getElementById('loader');
            const filteredCount = document.getElementById('filtered-count');
            
            // Initialize tooltips
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            // Initialize DataTable
            const dataTable = $('#users-table').DataTable({
                responsive: true,
                order: [[3, 'desc']], // Sort by created date by default
                language: {
                    search: "<i class='fas fa-search'></i> Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "<i class='fas fa-angle-double-left'></i>",
                        previous: "<i class='fas fa-angle-left'></i>",
                        next: "<i class='fas fa-angle-right'></i>",
                        last: "<i class='fas fa-angle-double-right'></i>"
                    }
                },
                drawCallback: function(settings) {
                    // Update filtered count
                    filteredCount.textContent = settings._iDisplayLength;
                    
                    // Re-apply tooltips after table redraw
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                }
            });
            
            // Add custom status filter
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const selectedStatus = statusFilter.value;
                const status = data[2]; // The status column is at index 2
                
                // If no filter is selected, show all rows
                if (!selectedStatus) {
                    return true;
                }
                
                // Check if the row's status contains the selected status text
                return status.includes(selectedStatus);
            });
            
            // Apply filter when status filter changes
            statusFilter.addEventListener('change', function() {
                dataTable.draw();
                updateFilteredCount();
            });
            
            // Update filtered count
            function updateFilteredCount() {
                filteredCount.textContent = dataTable.rows({search:'applied'}).count();
            }
            
            // Initially update filtered count
            updateFilteredCount();
            
            // Custom toast notification function for enhanced error messages
            function showEnhancedToast(type, message) {
                // Create a container for the toast if it doesn't exist
                const toastContainer = document.querySelector('.toast-container') || 
                    (() => {
                        const container = document.createElement('div');
                        container.className = 'toast-container';
                        document.body.appendChild(container);
                        return container;
                    })();
                
                // Create the toast element
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                
                // Set icon based on type
                const icon = type === 'success' ? '✓' : 
                            type === 'error' ? '✕' : 
                            type === 'warning' ? '⚠' : 
                            type === 'info' ? 'ℹ' : '';
                
                // Set title based on type
                const title = type === 'success' ? 'Success!' : 
                            type === 'error' ? 'Error!' : 
                            type === 'warning' ? 'Warning!' : 
                            type === 'info' ? 'Information' : '';
                
                // Build toast HTML - note we're directly setting innerHTML to allow HTML in the message
                toast.innerHTML = `
                    <div class="toast-icon">${icon}</div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                `;
                
                // Add to container
                toastContainer.appendChild(toast);
                
                // Auto remove after 6 seconds (6000ms)
                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }, 6000);
            }
            
            // Show toast notifications if session has success or error
            @if(session('success'))
                showToast('success', '{{ session('success') }}');
            @endif
            
            @if(session('error'))
                showEnhancedToast('error', '{{ session('error') }}');
            @endif
            
            // Form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous error messages
                clearErrors();
                
                // Show loader
                loader.style.display = 'block';
                
                // Disable submit button to prevent multiple submissions
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
                
                const formData = new FormData(form);
                
                fetch('{{ route('store.allowed.inspect.user') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loader
                    loader.style.display = 'none';
                    
                    // Re-enable submit button
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                    
                    if (data.success) {
                        // Show toast notification
                        showToast('success', data.message);
                        
                        // Reset form
                        resetForm();
                        
                        // Refresh table with new data
                        refreshTable(data.data);
                    } else {
                        // Show error toast with more detailed message
                        let errorMessage = data.message || 'An error occurred. Please try again.';
                        
                        // Highlight if this is a duplicate user error (contains "already in the allowed list")
                        if (errorMessage.includes('already in the allowed list')) {
                            // Format the message with status highlighted
                            const parts = errorMessage.split("status '");
                            if (parts.length > 1) {
                                const statusPart = parts[1].split("'")[0];
                                const statusClass = statusPart === 'Enabled' ? 'success' : 'danger';
                                errorMessage = `${parts[0]}status '<span class="badge bg-${statusClass} text-white">${statusPart}</span>'. Please edit the existing record.`;
                            }
                            errorMessage = `<strong>Duplicate User:</strong> ${errorMessage}`;
                        }
                        
                        showEnhancedToast('error', errorMessage);
                        
                        // Show validation errors if any
                        if (data.errors) {
                            displayErrors(data.errors);
                        }
                    }
                })
                .catch(error => {
                    // Hide loader
                    loader.style.display = 'none';
                    
                    // Re-enable submit button
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                    
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while processing your request.');
                });
            });
            
            // Edit button click handler
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                    const button = e.target.classList.contains('edit-btn') ? 
                        e.target : e.target.closest('.edit-btn');
                        
                    const id = button.getAttribute('data-id');
                    const userId = button.getAttribute('data-user_id');
                    const reason = button.getAttribute('data-reason');
                    const status = button.getAttribute('data-status');
                    
                    document.getElementById('record_id').value = id;
                    document.getElementById('user_id').value = userId;
                    document.getElementById('reason').value = reason;
                    document.getElementById('status').value = status;
                    
                    formTitle.textContent = 'Update User';
                    formTitle.innerHTML = '<i class="fas fa-user-edit animated-icon mr-1"></i> Update User';
                    saveBtn.innerHTML = '<i class="fas fa-save mr-1 mb-1"></i> Update';
                    cancelBtn.style.display = 'block';
                    
                    // Add a highlight effect to the form card
                    const formCard = form.closest('.card');
                    formCard.style.boxShadow = '0 0 0 3px rgba(58, 87, 232, 0.25)';
                    setTimeout(() => {
                        formCard.style.boxShadow = '';
                    }, 2000);
                    
                    // Scroll to form on mobile
                    if (window.innerWidth < 768) {
                        window.scrollTo({
                            top: form.offsetTop - 20,
                            behavior: 'smooth'
                        });
                    }
                }
            });
            
            // Delete button click handler
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
                    if (confirm('Are you sure you want to delete this user?')) {
                        const button = e.target.classList.contains('delete-btn') ? 
                            e.target : e.target.closest('.delete-btn');
                            
                        const id = button.getAttribute('data-id');
                        const row = $(`tr[data-id="${id}"]`);
                        
                        // Show loader
                        loader.style.display = 'block';
                        
                        // Disable button
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        
                        fetch(`/delete-allowed-inspect-user/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Hide loader
                            loader.style.display = 'none';
                            
                            if (data.success) {
                                // Show success toast
                                showToast('success', data.message || 'User removed successfully');
                                
                                // Remove row from DataTable and redraw
                                dataTable.row(row).remove().draw(false);
                                
                                // Update filtered count
                                updateFilteredCount();
                            } else {
                                // Show error toast
                                showToast('error', data.message || 'Failed to delete user');
                                
                                // Re-enable button
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-trash"></i>';
                            }
                        })
                        .catch(error => {
                            // Hide loader
                            loader.style.display = 'none';
                            
                            // Re-enable button
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-trash"></i>';
                            
                            console.error('Error:', error);
                            showToast('error', 'An error occurred while processing your request.');
                        });
                    }
                }
            });
            
            // Cancel button click handler
            cancelBtn.addEventListener('click', function() {
                resetForm();
            });
            
            // Helper functions
            function resetForm() {
                form.reset();
                document.getElementById('record_id').value = '';
                formTitle.innerHTML = '<i class="fas fa-user-plus mr-1"></i> Add New User';
                saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                cancelBtn.style.display = 'none';
                clearErrors();
            }
            
            function clearErrors() {
                document.getElementById('user_id_error').textContent = '';
                document.getElementById('reason_error').textContent = '';
                document.getElementById('status_error').textContent = '';
            }
            
            function displayErrors(errors) {
                if (errors.user_id) {
                    document.getElementById('user_id_error').textContent = errors.user_id[0];
                }
                if (errors.reason) {
                    document.getElementById('reason_error').textContent = errors.reason[0];
                }
                if (errors.status) {
                    document.getElementById('status_error').textContent = errors.status[0];
                }
            }
            
            function refreshTable(users) {
                // Clear DataTable and maintain state
                dataTable.clear();
                
                // Save current state
                const currentFilter = statusFilter.value;
                const currentPage = dataTable.page();
                
                // Add new data 
                users.forEach(user => {
                    const statusBadge = user.status === 'E' ? 
                        '<span class="badge bg-success text-white">Enabled</span>' : 
                        '<span class="badge bg-danger text-white">Disabled</span>';
                    
                    // Format date for display
                    const createdDate = new Date(user.created_at);
                    const formattedDate = createdDate.getFullYear() + '-' + 
                        String(createdDate.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(createdDate.getDate()).padStart(2, '0') + ' ' + 
                        String(createdDate.getHours()).padStart(2, '0') + ':' + 
                        String(createdDate.getMinutes()).padStart(2, '0');
                    
                    // Add row to DataTable
                    const newRow = dataTable.row.add([
                        user.user_id,
                        user.reason,
                        statusBadge,
                        formattedDate,
                        `<div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-info edit-btn" 
                                data-id="${user.id}"
                                data-user_id="${user.user_id}"
                                data-reason="${user.reason.replace(/"/g, '&quot;')}"
                                data-status="${user.status}"
                                data-bs-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger delete-btn" 
                                data-id="${user.id}"
                                data-bs-toggle="tooltip" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`
                    ]);
                });
                
                // Redraw the table
                dataTable.draw();
                
                // Re-add data attributes to rows
                $('#users-table tbody tr').each(function(index) {
                    if (index < users.length) {
                        $(this).attr('data-id', users[index].id);
                    }
                });
                
                // Re-apply the filter and restore page if possible
                statusFilter.value = currentFilter;
                dataTable.draw();
                
                if (currentPage < dataTable.page.info().pages) {
                    dataTable.page(currentPage).draw('page');
                }
                
                // Update filtered count
                updateFilteredCount();
                
                // Re-initialize tooltips
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            }
        });
    </script>
    @endif
    @endsection 

    

