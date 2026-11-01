@extends('layouts.app')

@section('title')
    BNU CMS
@stop
@section('content')

    <div class="row mt-2 ml-3 mr-3">
        <div class="col-12">
            <div class="card e-panel">
                <div class="card-header">
                    <h3 align="center">Library Room Allocation Requests</h3> <br>
                </div>
                
                <!-- Room Availability Cards Row -->
                <div class="row mx-3 mb-3">
                    <div class="col-12">
                        <h5 class="mb-3" style="color: #3e5871; font-weight: 600;">
                            <i class="fa fa-home mr-2"></i>Room Availability for Today
                        </h5>
                        <div class="row" id="room-cards-container">
                            @if(isset($roomsWithAvailability) && count($roomsWithAvailability) > 0)
                                @foreach($roomsWithAvailability as $room)
                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <div class="card room-availability-card" style="border: 2px solid #e9ecef; border-radius: 10px; transition: all 0.3s ease; cursor: pointer;" 
                                             onclick="showRoomAvailability({{ $room['id'] }}, '{{ $room['title'] }}')">
                                            <div class="card-body text-center p-3">
                                                <div class="room-icon mb-2">
                                                    <i class="fa fa-home fa-2x" style="color: #3e5871;"></i>
                                                </div>
                                                <h6 class="card-title mb-2" style="color: #2C3E50; font-weight: 600; font-size: 14px;">
                                                    {{ $room['title'] }}
                                                </h6>
                                                <p class="text-muted mb-2" style="font-size: 12px;">
                                                    <i class="fa fa-users mr-1"></i>{{ $room['capacity'] }} persons
                                                </p>
                                                
                                                <!-- Availability Badge -->
                                                <div class="availability-badge mb-2">
                                                    @if($room['available_slots'] > 0)
                                                        <span class="badge badge-success" style="background-color: #28a745; font-size: 11px; padding: 6px 10px;">
                                                            <i class="fa fa-check-circle mr-1"></i>{{ $room['available_slots'] }} Available
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger" style="background-color: #dc3545; font-size: 11px; padding: 6px 10px;">
                                                            <i class="fa fa-times-circle mr-1"></i>Fully Booked
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Progress Bar -->
                                                <div class="availability-progress">
                                                    <div class="progress" style="height: 6px; border-radius: 3px;">
                                                        @php
                                                            $percentage = ($room['available_slots'] / $room['total_slots']) * 100;
                                                        @endphp
                                                        <div class="progress-bar 
                                                            @if($percentage > 50) bg-success
                                                            @elseif($percentage > 25) bg-warning
                                                            @else bg-danger
                                                            @endif" 
                                                             role="progressbar" 
                                                             style="width: {{ $percentage }}%; border-radius: 3px;">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted mt-1" style="font-size: 10px;">
                                                        {{ $room['available_slots'] }}/{{ $room['total_slots'] }} slots
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center py-4">
                                    <i class="fa fa-home fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No rooms available</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <input type="hidden" id="taskStatus" value="0">
                <input type="hidden" id="studentStatus" value="1">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                        <div class="tabs-menu" style="padding:10px;">
                            <!-- Tabs -->
                            <ul class="nav panel-tabs">
                                <li class=""><a class="active" href="#lectures" onclick="status('0')" id="setclasspending"
                                        data-toggle="tab">
                                        <i class="fa fa-clock-o"></i> Pending <span id="pendingSpinner" class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;"><span class="sr-only">Loading...</span></span>
                                    </a></li>
                                <li><a href="#material" onclick="status('1')" id="setclasscompleted" data-toggle="tab">
                                        <i class="fa fa-check-circle"></i> Completed <span id="completedSpinner" class="spinner-border spinner-border-sm ml-1" role="status" style="display: none;"><span class="sr-only">Loading...</span></span>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <h5 class="ml-5 mt-3 mb-0 pb-0" id="subtitle"></h5>
                <div class="card-body">
                    <div class="row" id="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Room</th>
                                        <th scope="col">Reserved Date</th>
                                        <th scope="col">Reserved Time</th>
                                        <th scope="col">Requested On</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>

                                </thead>
                                <tbody id="table-body">
                                    {{-- @foreach ($res as $index => $i)
                                    <tr>
                                        <td>{{$i->group_id}}</td>
                                        <td>{{$i->title . ' ( Persons = ' .$i->count.' )'}}</td>
                                        <td>{{\Carbon\Carbon::parse($i->reserved_for)->format('d-M-Y')}}</td>
                                        <td>{{ \Carbon\Carbon::createFromTime($i->start_time, 0, 0)->format('h:i:s') . ' - ' . \Carbon\Carbon::createFromTime($i->end_time, 0, 0)->format('h:i:s')}}</td>

                                        <td>
                                            <div class="form-check form-switch">
                                                <button class="btn btn-sm btn-success" type="button" onclick="updateStatus({{$i->group_id.','.$i->is_approved}})">View</button>
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="" {{$i->is_approved ? 'checked':'' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="libraryRoom" role="dialog" aria-labelledby="libraryRoomLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <input type="hidden" id="id">
                    <h5 class="modal-title" id="libraryRoomLabel">Room Reservation Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="libraryRoomBody">
                    <div class="text-center text-success" id="msg-div">

                        <h5 id="msg-body"></h5>
                    </div>
                    <div id="modal-loading" class="text-center my-2" style="display:none;">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <small class="text-muted ml-2">Loading reservation details...</small>
                    </div>
                    <div class="table-responsive" id="modal-table-wrapper">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Request #</th>
                                    <th scope="col">Enrollment</th>
                                </tr>
                            </thead>
                            <tbody id="libraryRoomBody-table-body"></tbody>
                        </table>
                    </div>
                    <div id="existing-remarks" class="alert alert-info" style="display: none;">
                        <h6><i class="fa fa-comment"></i> <span id="remarks-label-text">Previous Remarks:</span></h6>
                        <p id="remarks-text"></p>
                    </div>
                    <div class="form-group" id="remarks-input-group">
                        <label for="remarks"><strong>Remarks:</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="remarks" placeholder="Enter remarks (required for pending requests)" maxlength="1500" rows="4"></textarea>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-danger" id="remarks-error" style="display: none;">Remarks are required</small>
                            <small class="text-muted ml-auto">
                                <span id="remarks-counter">0</span> / 1500 characters
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" id="disapprove"
                        onclick="updateStatus(0)" disabled>Disapprove</button>
                    <button type="button" class="btn btn-primary" id="approve" onclick="updateStatus(1)" disabled>Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Availability Modal -->
    <div class="modal fade" tabindex="-1" id="roomAvailabilityModal" role="dialog" aria-labelledby="roomAvailabilityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="roomAvailabilityModalLabel">
                        <i class="fa fa-calendar-check mr-2"></i>Room Availability Details
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 15px;">
                    <!-- Loading State -->
                    <div id="availability-loading" class="text-center py-3">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted mb-0">Loading room availability...</p>
                    </div>

                    <!-- Room Info Header -->
                    <div id="room-info-header" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 id="room-title" class="mb-1" style="color: #3e5871; font-weight: 600;"></h6>
                                <small class="text-muted">
                                    <i class="fa fa-users mr-1"></i>Capacity: <span id="room-capacity"></span> persons | 
                                    <i class="fa fa-calendar mr-1"></i>Date: <span id="availability-date"></span>
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="d-flex justify-content-end">
                                    <span class="badge badge-success mr-2">
                                        <i class="fa fa-check-circle mr-1"></i><span id="available-count">0</span> Available
                                    </span>
                                    <span class="badge badge-danger">
                                        <i class="fa fa-times-circle mr-1"></i><span id="reserved-count">0</span> Reserved
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots Table -->
                    <div id="time-slots-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Time Slot</th>
                                        <th>Status</th>
                                        <th>Group ID</th>
                                        <th>Reserved By</th>
                                    </tr>
                                </thead>
                                <tbody id="time-slots-table-body">
                                    <!-- Time slots will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="availability-error" class="text-center py-3" style="display: none;">
                        <i class="fa fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <h6 class="text-warning mb-2">Error Loading Availability</h6>
                        <p class="text-muted mb-2" id="error-message">Unable to load room availability details.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="loadRoomAvailability()">
                            <i class="fa fa-refresh mr-1"></i>Try Again
                        </button>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="refreshAvailability()">
                        <i class="fa fa-refresh mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    @include('components.toast-notifications')
    <style>
        /* Lazy Loading Styles */
        .lazy-load {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
        }
        
        .lazy-load.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        .load-more-section {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .loading-spinner.show {
            display: inline-block;
        }
        
        /* Loading States */
        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .message-loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .message-loading i {
            animation: spin 1s linear infinite;
        }
        
        /* Tab Loading States */
        .nav-tabs .nav-link.loading {
            position: relative;
        }
        
        .nav-tabs .nav-link.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 10px;
            width: 12px;
            height: 12px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translateY(-50%);
        }
        
        /* Status Icons */
        .status-pending {
            color: #ffc107;
        }
        
        .status-approved {
            color: #28a745;
        }
        
        .status-rejected {
            color: #dc3545;
        }
        
        /* Button Icons */
        .btn-success i {
            margin-right: 5px;
        }
        
        /* Table Row Hover Effects */
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        /* Animation Keyframes */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        /* Modern Loading Spinner */
        .modern-loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Remarks Validation */
        #remarks.is-invalid {
            border-color: #dc3545;
        }
        
        #remarks.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        #remarks-counter {
            font-weight: 600;
            color: #007bff;
        }

        /* Room Availability Cards Styles - Bootstrap 4 Only */
        .room-availability-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .room-availability-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .room-icon {
            transition: all 0.3s ease;
        }

        .room-availability-card:hover .room-icon i {
            transform: scale(1.1);
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            getCateg($('#taskStatus').val());
            
            // Character counter for remarks
            $('#remarks').on('input', function() {
                var length = $(this).val().length;
                $('#remarks-counter').text(length);
                
                // Hide error message when user starts typing
                if (length > 0) {
                    $('#remarks-error').hide();
                    $('#remarks').removeClass('is-invalid');
                }
            });
        });

        function closeModal() {
            // Reset all modal fields
            $("#remarks").val('');
            $("#remarks-counter").text('0');
            $("#remarks-error").hide();
            $("#remarks").removeClass('is-invalid');
            $("#libraryRoomBody-table-body").empty();
            $("#existing-remarks").hide();
            $('#modal-loading').hide();
            $('#modal-table-wrapper').show();
            
            $('#libraryRoom').modal('hide');
        }

        function updateStatus(status) {
            // Validate remarks for ALL requests (both approve and reject)
            var remarks = $("#remarks").val().trim();
            var currentStatus = $('#taskStatus').val();
            
            // Only validate for pending tab (completed tab doesn't show the form)
            if (currentStatus == '0') {
                if (remarks === '') {
                    $('#remarks-error').show();
                    $('#remarks').addClass('is-invalid');
                    $('#remarks').focus();
                    return false;
                }
            }
            
            // Show loading state on buttons
            var approveBtn = $("#approve");
            var disapproveBtn = $("#disapprove");

            if (status == 1) {
                approveBtn.html('<i class="fa fa-spinner fa-spin"></i> Approving...').prop('disabled', true);
                disapproveBtn.prop('disabled', true);
            } else {
                disapproveBtn.html('<i class="fa fa-spinner fa-spin"></i> Disapproving...').prop('disabled', true);
                approveBtn.prop('disabled', true);
            }
            $.ajax({
                url: '/api/library/update-room-allocation',
                type: 'POST', // HTTP method (GET, POST, PUT, DELETE, etc.)
                dataType: 'json',
                data: {
                    is_approved: status,
                    group_id: $("#id").val(),
                    remarks: remarks  // Use the already trimmed value
                },
                success: function(response) {
                    // Reset button states
                    approveBtn.html('Approve').prop('disabled', false);
                    disapproveBtn.html('Disapprove').prop('disabled', false);
                    
                    // Show toast notification based on action
                    var toastType = response.status == 1 ? 'success' : 'warning';
                    var toastTitle = response.status == 1 ? 'Approved!' : 'Rejected!';
                    showToast(toastType, response.message, toastTitle);
                    
                    // Close modal and refresh data
                    setTimeout(() => {
                        $('#libraryRoom').modal('hide');
                        $('#remarks').val('');
                        $('#remarks-counter').text('0');
                        getCateg($('#taskStatus').val());
                    }, 1500);

                },
                error: function(xhr, status, error) {
                    // Reset button states on error
                    approveBtn.html('Approve').prop('disabled', false);
                    disapproveBtn.html('Disapprove').prop('disabled', false);
                    
                    // Handle validation errors
                    var errorMessage = 'Failed to update reservation status. Please try again.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Show validation errors if available
                        if (xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.remarks) {
                                $('#remarks-error').text(errors.remarks[0]).show();
                                $('#remarks').addClass('is-invalid').focus();
                            }
                        }
                    }
                    
                    // Show error toast
                    showToast('error', errorMessage, 'Error!');
                }
            });


        }

        function status(val) {
            $('#taskStatus').val(val);
            
            // Show loading spinner on the active tab
            if (val == '0') {
                $('#pendingSpinner').show();
                $('#completedSpinner').hide();
                $('#setclasspending').addClass('loading');
                $('#setclasscompleted').removeClass('loading');
            } else {
                $('#completedSpinner').show();
                $('#pendingSpinner').hide();
                $('#setclasscompleted').addClass('loading');
                $('#setclasspending').removeClass('loading');
            }
            
            getCateg(val);
        }

        function pad(number, length) {
            var str = '' + number;
            while (str.length < length) {
                str = '0' + str;
            }
            return str;
        }

        function formatHours(hour) {
            // Ensure the hour is within the range 0-23
            hour = Math.max(0, Math.min(23, hour));

            // Add leading zero if hour is less than 10
            var formattedHour = (hour < 10 ? "0" : "") + hour;

            // Return the formatted time string
            return formattedHour + ":00";
        }

        function getCateg(sel) {
            let sr = 0;
            $("#table-body").empty();
            $("#load-more-content").remove(); // Clear any existing load more content
            
            $.ajax({
                url: '/api/library/room-allocation-list',
                type: 'POST',
                dataType: 'json',
                data: {
                    status: sel,
                    page: 1,
                    per_page: 100,
                    force_refresh: true  // Always get fresh data, not from cache
                },
                success: function(response) {
                    // Hide loading spinners
                    $('#pendingSpinner').hide();
                    $('#completedSpinner').hide();
                    $('#setclasspending').removeClass('loading');
                    $('#setclasscompleted').removeClass('loading');
                    var html = '';
                    var pagination = response.pagination || {};
                    var displayData = response.data || response;
                    
                    // Handle Laravel pagination response
                    if (response.pagination) {
                        var totalRecords = pagination.total || 0;
                        var hasMoreData = pagination.has_more_pages || false;
                        
                        // Store pagination data for lazy loading
                        window.currentPagination = pagination;
                        window.currentFilters = { status: sel };
                        
                        // Check if no data found
                        if (displayData.length === 0) {
                            html = '<tr><td colspan="7" class="text-center p-4"><i class="fa fa-inbox fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No records found</h5><p class="text-muted">There are no ' + (sel == '0' ? 'pending' : 'completed') + ' room allocation requests at this time.</p></td></tr>';
                            $("#subtitle").text('Showing 0 of 0 records');
                        } else {
                        
                        displayData.forEach(function(item) {
                        console.log(item);
                        var dateObject = new Date(item.reserved_for);
                        sr++;

                            // Determine button text and class based on status and tab
                            var buttonHtml = '';
                            if (sel == '0') {
                                // Pending tab - show View button for approval
                                buttonHtml = '<button class="btn btn-success btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                            } else {
                                // Completed tab - show different actions based on status
                                if (item.is_approved == 1) {
                                    buttonHtml = '<button class="btn btn-info btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-check-circle"></i> View Approved</button>';
                                } else if (item.is_approved == 2) {
                                    buttonHtml = '<button class="btn btn-warning btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-times-circle"></i> View Rejected</button>';
                                } else {
                                    buttonHtml = '<button class="btn btn-secondary btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                                }
                            }

                            // Create status badge
                            var statusBadge = '';
                            if (item.is_approved == 1) {
                                statusBadge = '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</span>';
                            } else if (item.is_approved == 2) {
                                statusBadge = '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Rejected</span>';
                            } else {
                                statusBadge = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>';
                            }

                            html += "<tr class='lazy-load'><td>" + sr + "</td><td><a href='javascript:void(0)' onclick='showRoomAvailability(" + item.room_id + ", \"" + item.title + "\")' style='color: #3e5871; text-decoration: none; font-weight: 500;'><i class='fa fa-home mr-1'></i>" + item.title + "</a></td><td>" + new Date(item.reserved_for).toLocaleDateString()  + "</td><td>" + formatHours(item.start_time) + ' - ' + formatHours(item.end_time) + "</td><td>" +
        new Date(item.entry_date_time).toLocaleString() + "</td><td>" + statusBadge + "</td><td>" + buttonHtml + "</td></tr>";
                        });
                        
                        // Add load more section if there's more data
                        if (hasMoreData) {
                            var currentShowing = pagination.to || displayData.length;
                            var remainingCount = totalRecords - currentShowing;
                            
                            html += '<tr id="load-more-row"><td colspan="7" class="load-more-section">';
                            html += '<button class="btn btn-outline-primary btn-sm" onclick="loadMoreDataLibrary()">';
                            html += '<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)';
                            html += '</button>';
                            html += '<div class="loading-spinner mt-2">';
                            html += '<i class="fa fa-spinner fa-spin"></i> Loading more records...';
                            html += '</div>';
                            html += '</td></tr>';
                        }
                        
                        // Update subtitle with pagination info
                        $("#subtitle").text('Showing ' + pagination.to + ' of ' + pagination.total + ' records');
                        }
                    } else {
                        // Fallback for non-paginated response
                        displayData.forEach(function(item) {
                            console.log(item);
                            var dateObject = new Date(item.reserved_for);
                            sr++;
                            
                            // Determine button text and class based on status and tab
                            var buttonHtml = '';
                            if (sel == '0') {
                                // Pending tab - show View button for approval
                                buttonHtml = '<button class="btn btn-success btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                            } else {
                                // Completed tab - show different actions based on status
                                if (item.is_approved == 1) {
                                    buttonHtml = '<button class="btn btn-info btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-check-circle"></i> View Approved</button>';
                                } else if (item.is_approved == 2) {
                                    buttonHtml = '<button class="btn btn-warning btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-times-circle"></i> View Rejected</button>';
                                } else {
                                    buttonHtml = '<button class="btn btn-secondary btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                                }
                            }

                            // Create status badge
                            var statusBadge = '';
                            if (item.is_approved == 1) {
                                statusBadge = '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</span>';
                            } else if (item.is_approved == 2) {
                                statusBadge = '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Rejected</span>';
                            } else {
                                statusBadge = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>';
                            }

                            html += "<tr class='lazy-load'><td>" + sr + "</td><td><a href='javascript:void(0)' onclick='showRoomAvailability(" + item.room_id + ", \"" + item.title + "\")' style='color: #3e5871; text-decoration: none; font-weight: 500;'><i class='fa fa-home mr-1'></i>" + item.title + "</a></td><td>" + new Date(item.reserved_for).toLocaleDateString()  + "</td><td>" + formatHours(item.start_time) + ' - ' + formatHours(item.end_time) + "</td><td>" +
        new Date(item.entry_date_time).toLocaleString() + "</td><td>" + statusBadge + "</td><td>" + buttonHtml + "</td></tr>";
                        });
                    }
                    
                    $("#table-body").append(html);
                    
                    // Apply lazy loading to table rows
                    setTimeout(function() {
                        $('.lazy-load').addClass('loaded');
                    }, 100);
                    
                    // Store pagination data for Laravel-based loading
                    if (response.pagination) {
                        window.paginationData = {
                            current_page: pagination.current_page || 1,
                            total: pagination.total || 0,
                            per_page: pagination.per_page || 100,
                            has_more_pages: pagination.has_more_pages || false,
                            current_showing: pagination.to || displayData.length,
                            filters: { status: sel }
                        };
                        
                        window.isLoadingMore = false;
                        
                        // Setup scroll-based lazy loading for Laravel pagination
                        setupScrollLazyLoadingLibrary();
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    // Hide loading spinners on error as well
                    $('#pendingSpinner').hide();
                    $('#completedSpinner').hide();
                    $('#setclasspending').removeClass('loading');
                    $('#setclasscompleted').removeClass('loading');
                    
                    // Show error message in table
                    var errorHtml = '<tr><td colspan="7" class="text-center p-4 text-danger"><i class="fa fa-exclamation-triangle fa-3x mb-3"></i><br><h5>Error Loading Data</h5><p>Failed to load room allocation requests. Please refresh the page and try again.</p></td></tr>';
                    $("#table-body").html(errorHtml);
                    $("#subtitle").text('Error loading records');
                    
                    // Show toast notification
                    showToast('error', 'Failed to load room allocation requests. Please try again.', 'Error!');
                }
            });
        }

        function viewRoomReservation(id) {
            $("#remarks").val('');
            $("#remarks-counter").text('0');
            $("#remarks-error").hide();
            $("#remarks").removeClass('is-invalid');
            $("#id").val(id);
            $("#msg-div").addClass("d-none");
            $("#libraryRoomBody-table-body").empty();
            $("#existing-remarks").hide();
            let srNumber = 0;
            // Show modal immediately with loading state
            $('#modal-loading').show();
            $('#modal-table-wrapper').hide();
            $('#approve, #disapprove').prop('disabled', true);
            
            // Check if we're in completed tab and hide action buttons
            var currentStatus = $('#taskStatus').val();
            if (currentStatus == '1') {
                // Completed tab - hide action buttons and entire remarks input group
                $('#approve, #disapprove').hide();
                $('#remarks-input-group').hide();
            } else {
                // Pending tab - show action buttons and remarks input group
                $('#approve, #disapprove').show();
                $('#remarks-input-group').show();
            }
            
            $('#libraryRoom').modal('show');
            $.ajax({
                url: '/api/library/allocation-details',
                type: 'POST', // HTTP method (GET, POST, PUT, DELETE, etc.)
                dataType: 'json',
                data: {
                    group_id: id
                },
                success: function(response) {
                    var html = '';
                    var existingRemarks = '';
                    var updatedByUser = '';
                    console.log('Modal response:', response); // Debug log
                    
                    // Check if response is empty
                    if (!response || response.length === 0) {
                        html = '<tr><td colspan="2" class="text-center text-muted p-3">No reservation details found</td></tr>';
                        $("#libraryRoomBody-table-body").append(html);
                        $('#modal-loading').hide();
                        $('#modal-table-wrapper').show();
                        return;
                    }
                    
                    response.forEach(function(item) {
                        srNumber++;
                        var dateObject = new Date(item.reserved_for);
                        
                        // Get remarks and updated_by from first item (they should all be the same for a group)
                        if (srNumber === 1) {
                            if (item.remarks) {
                                existingRemarks = item.remarks;
                            }
                            if (item.updated_by) {
                                updatedByUser = item.updated_by;
                            }
                        }

                        // Check what fields are available and use appropriate field names
                        var enrollment = item.enrollment_no || item.enrollment || item.student_enrollment || item.student_id || 'N/A';
                        var reservedBy = item.reserved_by || item.created_by || item.user_id || item.created_by_user || '';
                        var isGroupLeader = enrollment === reservedBy;

                        // If no enrollment found, show available data for debugging
                        if (enrollment === 'N/A') {
                            console.log('Available fields in item:', Object.keys(item));
                            enrollment = item.id || 'ID: ' + (item.reservation_id || 'Unknown');
                        }

                        html += `
    <tr>
        <td>${srNumber}</td>
        <td class="${isGroupLeader ? 'bg-primary text-white' : 'group-member'}">
            ${enrollment} - ${isGroupLeader ? '(Group Leader)' : '(Group Member)'}
        </td>
    </tr>`;
                    });
                    
                    $("#libraryRoomBody-table-body").append(html);
                    
                    // Show existing remarks based on tab
                    var currentStatus = $('#taskStatus').val();
                    if (existingRemarks) {
                        if (currentStatus == '1') {
                            // Completed tab - show "Remarks given by: username"
                            if (updatedByUser) {
                                $('#remarks-label-text').text('Remarks given by: ' + updatedByUser);
                            } else {
                                $('#remarks-label-text').text('Remarks:');
                            }
                            $('#remarks-text').text(existingRemarks);
                            $('#existing-remarks').show();
                        } else {
                            // Pending tab - show as "Previous Remarks" (if any from partial processing)
                            $('#remarks-label-text').text('Previous Remarks:');
                            $('#remarks-text').text(existingRemarks);
                            $('#existing-remarks').show();
                        }
                    } else {
                        // No remarks - hide the section
                        $('#existing-remarks').hide();
                    }
                    
                    $('#modal-loading').hide();
                    $('#modal-table-wrapper').show();
                    $('#approve, #disapprove').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    $('#modal-loading').hide();
                    $('#modal-table-wrapper').show();
                    $('#approve, #disapprove').prop('disabled', false);
                    
                    // Show error message to user
                    var errorMsg = 'Failed to load reservation details. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showToast('error', errorMsg, 'Error!');
                }
            });
        }


        // Setup Scroll-based Lazy Loading for Library Room Allocation
        function setupScrollLazyLoadingLibrary() {
            // Remove any existing scroll listeners
            $(window).off('scroll.librarylazyload');
            
            // Add new scroll listener
            $(window).on('scroll.librarylazyload', function() {
                if (window.isLoadingMore) return; // Prevent multiple calls
                
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                var documentHeight = $(document).height();
                
                // Load more when user scrolls within 200px of the bottom
                if (scrollTop + windowHeight >= documentHeight - 200) {
                    if (window.paginationData && window.paginationData.has_more_pages) {
                        $('#load-more-row .btn').hide();
                        $('#load-more-row .loading-spinner').addClass('show');
                        loadMoreDataLibraryScroll();
                    }
                }
            });
        }

        // Library-based Load More Data Functions
        function loadMoreDataLibrary() {
            if (!window.paginationData || window.isLoadingMore) return;
            
            var loadMoreBtn = $('button[onclick*="loadMoreDataLibrary"]');
            loadMoreBtn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            loadMoreBtn.prop('disabled', true);
            
            loadMoreDataLibraryScroll();
        }

        function loadMoreDataLibraryScroll() {
            if (!window.paginationData || window.isLoadingMore || !window.paginationData.has_more_pages) return;
            
            window.isLoadingMore = true;
            var nextPage = window.paginationData.current_page + 1;
            var filters = window.paginationData.filters;
            
            $.ajax({
                url: '/api/library/room-allocation-list',
                type: 'POST',
                dataType: 'json',
                data: {
                    status: filters.status,
                    page: nextPage,
                    per_page: window.paginationData.per_page,
                    force_refresh: true  // Get fresh data on pagination too
                },
                success: function(response) {
                    var pagination = response.pagination || {};
                    var displayData = response.data || response;
                    
                    console.log('Load More Response:', response);
                    console.log('Pagination:', pagination);
                    
                    var additionalHTML = '';
                    var sr_no = window.paginationData.current_showing;
                    
                    displayData.forEach(function(item) {
                        sr_no++;
                        console.log(item);
                        
                        // Determine button text and class based on status and tab
                        var buttonHtml = '';
                        if (filters.status == '0') {
                            // Pending tab - show View button for approval
                            buttonHtml = '<button class="btn btn-success btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                        } else {
                            // Completed tab - show different actions based on status
                            if (item.is_approved == 1) {
                                buttonHtml = '<button class="btn btn-info btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-check-circle"></i> View Approved</button>';
                            } else if (item.is_approved == 2) {
                                buttonHtml = '<button class="btn btn-warning btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-times-circle"></i> View Rejected</button>';
                            } else {
                                buttonHtml = '<button class="btn btn-secondary btn-sm" onclick="viewRoomReservation(' + item.group_id + ')"><i class="fa fa-eye"></i> View</button>';
                            }
                        }
                        
                        // Create status badge for load more
                        var statusBadge = '';
                        if (item.is_approved == 1) {
                            statusBadge = '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Approved</span>';
                        } else if (item.is_approved == 2) {
                            statusBadge = '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Rejected</span>';
                        } else {
                            statusBadge = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>';
                        }
                        
                        additionalHTML += "<tr class='lazy-load'><td>" + sr_no + "</td><td><a href='javascript:void(0)' onclick='showRoomAvailability(" + item.room_id + ", \"" + item.title + "\")' style='color: #3e5871; text-decoration: none; font-weight: 500;'><i class='fa fa-home mr-1'></i>" + item.title + "</a></td><td>" + new Date(item.reserved_for).toLocaleDateString()  + "</td><td>" + formatHours(item.start_time) + ' - ' + formatHours(item.end_time) + "</td><td>" +
        new Date(item.entry_date_time).toLocaleString() + "</td><td>" + statusBadge + "</td><td>" + buttonHtml + "</td></tr>";
                    });
                    
                    // Insert new rows before the load more row
                    $('#load-more-row').before(additionalHTML);
                    
                    // Update pagination data
                    window.paginationData = {
                        current_page: pagination.current_page,
                        total: pagination.total,
                        per_page: pagination.per_page,
                        has_more_pages: pagination.has_more_pages,
                        current_showing: pagination.to,
                        filters: filters
                    };
                    
                    // Update load more button or remove it
                    var remainingCount = pagination.total - pagination.to;
                    if (pagination.has_more_pages) {
                        $('#load-more-row .btn').html('<i class="fa fa-plus-circle"></i> Load More (' + remainingCount + ' remaining)');
                        $('#load-more-row .btn').show();
                        $('#load-more-row .loading-spinner').removeClass('show');
                    } else {
                        $('#load-more-row').html('<td colspan="7" class="text-center p-2 border-top"><small class="text-muted">Showing all ' + pagination.total + ' records</small></td>');
                        $(window).off('scroll.librarylazyload'); // Remove scroll listener
                    }
                    
                    // Update subtitle
                    $("#subtitle").text('Showing ' + pagination.to + ' of ' + pagination.total + ' records');
                    
                    // Apply lazy loading to new rows
                    setTimeout(function() {
                        $('.lazy-load').addClass('loaded');
                    }, 100);
                    
                    window.isLoadingMore = false;
                },
                error: function(xhr, status, error) {
                    window.isLoadingMore = false;
                    $('#load-more-row .btn').show();
                    $('#load-more-row .loading-spinner').removeClass('show');
                    $('button[onclick*="loadMoreDataLibrary"]').html('<i class="fa fa-plus-circle"></i> Load More').prop('disabled', false);
                    console.error('Load More Error:', status, error);
                }
            });
        }

        // Room Availability Modal Functions
        let currentRoomId = null;
        let currentDate = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD

        function showRoomAvailability(roomId, roomTitle) {
            currentRoomId = roomId;
            
            // Show modal with loading state
            $('#roomAvailabilityModal').modal('show');
            $('#availability-loading').show();
            $('#room-info-header').hide();
            $('#time-slots-container').hide();
            $('#availability-error').hide();
            
            // Set modal title with room name
            $('#roomAvailabilityModalLabel').html('<i class="fa fa-calendar-check mr-2"></i>Room Availability - ' + roomTitle);
            
            // Load availability data
            loadRoomAvailability();
        }

        function loadRoomAvailability() {
            if (!currentRoomId) return;
            
            $('#availability-loading').show();
            $('#room-info-header').hide();
            $('#time-slots-container').hide();
            $('#availability-error').hide();
            
            $.ajax({
                url: '/api/library/room-availability',
                type: 'POST',
                dataType: 'json',
                data: {
                    room_id: currentRoomId,
                    date: currentDate
                },
                success: function(response) {
                    $('#availability-loading').hide();
                    
                    if (response.error) {
                        showAvailabilityError(response.message);
                        return;
                    }
                    
                    displayRoomAvailability(response);
                },
                error: function(xhr, status, error) {
                    $('#availability-loading').hide();
                    let errorMessage = 'Unable to load room availability. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showAvailabilityError(errorMessage);
                }
            });
        }

        function displayRoomAvailability(data) {
            // Update room info
            $('#room-title').text(data.room.title);
            $('#room-capacity').text(data.room.capacity);
            $('#availability-date').text(formatDate(data.date));
            
            // Update summary counts
            $('#available-count').text(data.summary.available_slots);
            $('#reserved-count').text(data.summary.reserved_slots);
            $('#total-count').text(data.summary.total_slots);
            
            // Generate time slots table rows
            let timeSlotsHtml = '';
            data.time_slots.forEach(function(slot) {
                let statusBadge = slot.is_reserved ? 
                    '<span class="badge badge-danger"><i class="fa fa-times-circle mr-1"></i>Reserved</span>' : 
                    '<span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i>Available</span>';
                
                let groupId = slot.is_reserved && slot.reservation_details ? 
                    `<span class="badge badge-secondary">${slot.reservation_details.group_id}</span>` : 
                    '<span class="text-muted">-</span>';
                
                let reservedBy = slot.is_reserved && slot.reservation_details ? 
                    `${slot.reservation_details.reserved_by} (${slot.reservation_details.group_members_count} members)` : 
                    '<span class="text-muted">-</span>';
                
                timeSlotsHtml += `
                    <tr class="${slot.is_reserved ? 'table-danger' : 'table-success'}">
                        <td><strong>${slot.time_display}</strong></td>
                        <td>${statusBadge}</td>
                        <td>${groupId}</td>
                        <td>${reservedBy}</td>
                    </tr>
                `;
            });
            
            $('#time-slots-table-body').html(timeSlotsHtml);
            
            // Show the content
            $('#room-info-header').show();
            $('#time-slots-container').show();
        }

        function showAvailabilityError(message) {
            $('#error-message').text(message);
            $('#availability-error').show();
        }

        function refreshAvailability() {
            loadRoomAvailability();
        }

        function formatDate(dateString) {
            try {
                // Handle different date formats
                let date;
                if (dateString.includes('-')) {
                    // Format: DD-MON-YY or DD-MON-YYYY
                    let parts = dateString.split('-');
                    if (parts.length === 3) {
                        let day = parts[0];
                        let month = parts[1];
                        let year = parts[2];
                        
                        // Convert month name to number
                        const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 
                                          'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                        let monthNum = monthNames.indexOf(month.toUpperCase());
                        
                        if (monthNum !== -1) {
                            // Handle 2-digit years
                            if (year.length === 2) {
                                year = '20' + year;
                            }
                            date = new Date(year, monthNum, day);
                        }
                    }
                }
                
                if (!date || isNaN(date.getTime())) {
                    // Fallback to current date
                    date = new Date();
                }
                
                return date.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString; // Return original if formatting fails
            }
        }

    </script>
@stop
