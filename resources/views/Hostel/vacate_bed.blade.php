@extends('layouts.app')

@section('content')
    <!-- Include Toast Component -->
    @include('components.toast-notifications')
  
    <div class=" mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa fa-bed mr-2"></i>Manage Rooms and Beds</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="hostel-location-select" class="font-weight-bold"><i class="fa fa-map-marker mr-1"></i>Hostel Location:</label>
                            <select id="hostel-location-select" class="form-control">
                                <option value="">Select Location</option>
                                <option value="on_campus">on campus</option>
                                <option value="off_campus">off campus</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="hostel-type-select" class="font-weight-bold"><i class="fa fa-building-o mr-1"></i>Hostel Type:</label>
                            <select id="hostel-type-select" class="form-control" disabled>
                                <option value="">Select Location First</option>
                                @foreach($hostel_type as $type)
                                    <option value="{{$type->ht_id}}" data-location="{{$type->ht_location ?? 'on_campus'}}">{{ $type->ht_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label for="floor-select" class="font-weight-bold"><i class="fa fa-level-up mr-1"></i>Floor:</label>
                            <select id="floor-select" disabled class="form-control">
                                <option value="">Select Floor</option>
                                @foreach($floors_de as $floor)
                                    <option value="{{$floor->hf_id}}">{{ $floor->hf_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <div class="btn-group w-100" role="group">
                                <button id="add-room-button" class="btn btn-success btn-sm" disabled>
                            <i class="fa fa-plus"></i> Add Room
                        </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Row for Paid but not assigned section -->
                <div class="row align-items-end mt-4">
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <div class="d-flex align-items-center justify-content-start">
                                <label class="font-weight-bold mb-0 mr-2"><i class="fa fa-list mr-1"></i>Paid but not assigned (awaiting seat allocation):</label>
                                <div class="d-flex" role="group">
                                    <button id="show-male-students-btn" class="btn btn-outline-primary btn-sm mr-2">
                                        <i class="fa fa-male"></i> Male
                                        <span class="badge badge-info badge-pill ml-1" id="male-not-assigned-count">0</span>
                                    </button>
                                    <button id="show-female-students-btn" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-female"></i> Female
                                        <span class="badge badge-info badge-pill ml-1" id="female-not-assigned-count">0</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Building-wise Available Seats Display - Full Width -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div id="building-seats-display" class="border-0 bg-transparent" style="display: none;">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-primary"><i class="fa fa-building mr-2"></i><strong>Seats by Building/Floor</strong></h5>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary active" id="show-available-btn">Available Only</button>
                                        <button type="button" class="btn btn-outline-primary" id="show-total-btn">Total Capacity</button>
                                    </div>
                                </div>
                                <div id="building-seats-note" class="alert alert-info mt-3 py-2 px-3" style="display:none;"></div>
                            </div>
                            <div class="row" id="building-seats-breakdown">
                                <!-- Building breakdown will be populated here -->
                            </div>
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-muted"><i class="fa fa-bed mr-2"></i><strong>Total Seats</strong></h6>
                                    <span class="badge badge-primary badge-pill px-3 py-2" id="total-seats-count">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <h6 class="mb-0 text-muted"><i class="fa fa-user-times mr-2"></i><strong>Paid but not assigned (awaiting seat allocation)</strong></h6>
                                    <span class="badge badge-warning badge-pill px-3 py-2" id="paid-not-assigned-count">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <h6 class="mb-0 text-muted"><i class="fa fa-check-circle mr-2"></i><strong>Available now</strong></h6>
                                    <span class="badge badge-success badge-pill px-3 py-2" id="usable-now-count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <hr class="my-4">

                <div id="rooms-container" class="row mt-4">
                    <!-- Rooms will be dynamically rendered here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="roomModalTitle"><i class="fa fa-plus-circle mr-2"></i>Add New Room</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="room-id">
                    <input type="hidden" id="edit-mode" value="edit-room">
                    <div class="form-group">
                        <label for="room-number" class="font-weight-bold"><i class="fa fa-hashtag mr-1"></i>Room Number</label>
                        <input type="text" class="form-control form-control-lg" id="room-number" placeholder="Enter room number">
                    </div>
                    <div class="form-group">
                        <label for="room-capacity" class="font-weight-bold"><i class="fa fa-users mr-1"></i>Room Capacity</label>
                        <input type="number" class="form-control form-control-lg" id="room-capacity" placeholder="Enter room capacity">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold mb-2"><i class="fa fa-cogs mr-1"></i>Facilities</label>
                        <div class="border rounded p-3 facility-container bg-light" style="margin-left: 50px;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ac-availability">
                                        <label class="custom-control-label" for="ac-availability">AC</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="heater-availability">
                                        <label class="custom-control-label" for="heater-availability">Heater</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="refrigerator-availability">
                                        <label class="custom-control-label" for="refrigerator-availability">Refrigerator</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveRoomBtn"><i class="fa fa-save mr-1"></i>Save Room</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-question-circle mr-2"></i>Confirm Action</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage" class="lead text-center mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn"><i class="fa fa-check mr-1"></i>Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupy Bed Modal -->
    <div class="modal fade" id="occupyBedModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-bed mr-2"></i>Occupy Bed</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="occupyBedForm">
                <div class="modal-body">
                    <div class="text-center mb-3">
                            <i class="fa fa-bed text-primary fa-3x mb-2"></i>
                        <p class="lead">Enter student details to occupy this bed</p>
                    </div>
                        
                        <!-- Session Info -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-calendar mr-2"></i>
                                <div>
                                    <strong>Current Academic Session:</strong><br>
                                    <span>{{ $current_academic_session->sess_name ?? 'Not Available' }}</span>
                    </div>
                            </div>
                        </div>

                    <div class="form-group">
                            <label for="student-input" class="font-weight-bold">
                                <i class="fa fa-id-card mr-1"></i>Enrollment Number / Applicant ID
                            </label>
                            <input type="text" class="form-control form-control-lg" id="student-input" name="student_input" placeholder="Enter enrollment number or applicant ID" required>
                            <small class="form-text text-muted">
                                <i class="fa fa-info-circle mr-1"></i>
                                You can enter either enrollment number or applicant ID
                            </small>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="occupyBedBtn">
                            <i class="fa fa-bed mr-1"></i>Occupy Bed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Large Modal for Paid Students -->
    <div class="modal fade" id="paidStudentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 85%; width: 85%;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paidStudentsModalTitle">
                        <i class="fa fa-users mr-2"></i>Paid but not assigned (awaiting seat allocation)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark sticky-top">
                                <tr>
                                    <th width="10%">Sr#</th>
                                    <th width="30%">Enrollment No</th>
                                    <th width="40%">Student Name</th>
                                    <th width="15%">Room No</th>
                                    <th width="5%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="students-tbody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mr-auto">
                        <span class="badge badge-info" id="total-count-badge">Total: 0</span>
                    </div>
                    <button type="button" class="btn btn-success mr-2" id="export-excel-btn">
                        <i class="fa fa-file-excel-o mr-1"></i>Export to Excel
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {
            // Initialize variables
            const rooms = @json($rooms);
            const allFloors = @json($floors_de);
            const allHostelTypes = @json($hostel_type);
            let currentActionData = null;
            
            // Debug: Log initial data
            console.log('Initial Data Debug:', {
                totalRooms: rooms.length,
                totalFloors: allFloors.length,
                floors: allFloors.map(f => ({ id: f.hf_id, name: f.hf_name, hostelType: f.hf_hostel_type, newHostelType: f.hf_new_hostel_type })),
                rooms: rooms.slice(0, 5).map(r => ({ id: r.hr_id, roomNo: r.hr_room_no, floorId: r.hr_hf_id, hostelType: r.hr_ht_id, status: r.hr_status, bedCount: r.beds.length }))
            });

            // Initialize modals
            $('#addRoomModal, #confirmationModal, #occupyBedModal, #paidStudentsModal').modal({
                show: false
            });

            // Prefetch not-assigned counts for badges based on selected location and hostel type
            function fetchNotAssignedCount(gender, hostelTypeId, cb){
                if (!hostelTypeId) {
                    // Reset counts if no hostel type selected
                    if (gender === 'male'){
                        $('#male-not-assigned-count').text('0');
                        window.maleNotAssignedPaid = 0;
                    } else {
                        $('#female-not-assigned-count').text('0');
                        window.femaleNotAssignedPaid = 0;
                    }
                    if (typeof cb === 'function') cb(0);
                    return;
                }

                // Get selected hostel type's location from allHostelTypes
                const selectedHostelType = allHostelTypes.find(type => type.ht_id == hostelTypeId);
                const hostelLocation = selectedHostelType ? (selectedHostelType.ht_location || 'on_campus') : 'on_campus';
                
                $.ajax({
                    url: '/api/get-paid-students-by-gender?gender=' + gender + '&hostel_type_id=' + hostelTypeId + '&hostel_location=' + hostelLocation,
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}'
                    },
                    success: function(response){
                        let count = 0;
                        if (response && response.success && Array.isArray(response.students)){
                            count = response.students.filter(function(s){
                                return (s.room_status || '').toLowerCase() !== 'assigned';
                            }).length;
                        }
                        if (gender === 'male'){
                            $('#male-not-assigned-count').text(count);
                            window.maleNotAssignedPaid = count;
                        } else {
                            $('#female-not-assigned-count').text(count);
                            window.femaleNotAssignedPaid = count;
                        }
                        if (typeof cb === 'function') cb(count);
                    },
                    error: function(){
                        if (gender === 'male'){
                            $('#male-not-assigned-count').text('0');
                            window.maleNotAssignedPaid = 0;
                        } else {
                            $('#female-not-assigned-count').text('0');
                            window.femaleNotAssignedPaid = 0;
                        }
                        if (typeof cb === 'function') cb(0);
                    }
                });
            }

            // Initial badge updates - set to 0 until location and hostel type are selected
            $('#male-not-assigned-count').text('0');
            $('#female-not-assigned-count').text('0');
            window.maleNotAssignedPaid = 0;
            window.femaleNotAssignedPaid = 0;

            // Male students button handler
            $('#show-male-students-btn').click(function() {
                loadPaidStudents('male');
                $('#paidStudentsModal').modal('show');
            });

            // Female students button handler
            $('#show-female-students-btn').click(function() {
                loadPaidStudents('female');
                $('#paidStudentsModal').modal('show');
            });
            
            // Toggle between available and total capacity display
            $('#show-available-btn').click(function() {
                $(this).addClass('active');
                $('#show-total-btn').removeClass('active');
                updateBuildingDisplay('available');
            });
            
            $('#show-total-btn').click(function() {
                $(this).addClass('active');
                $('#show-available-btn').removeClass('active');
                updateBuildingDisplay('total');
            });

            // Hostel location change handler
            $('#hostel-location-select').change(function() {
                const selectedLocation = $(this).val();
                
                // Reset all dependent fields and counts
                $('#hostel-type-select').html('<option value="">Select Type</option>').prop('disabled', true);
                $('#floor-select').html('<option value="">Select Floor</option>').prop('disabled', true);
                $('#add-room-button').prop('disabled', true);
                $('#rooms-container').empty();
                $('#building-seats-display').hide();
                
                // Reset counts when location changes
                $('#male-not-assigned-count').text('0');
                $('#female-not-assigned-count').text('0');
                window.maleNotAssignedPaid = 0;
                window.femaleNotAssignedPaid = 0;
                
                if (selectedLocation) {
                    // Filter hostel types based on selected location from JSON data
                    // Handle both old 'out_of_campus' and new 'off_campus' values for backward compatibility
                    const filteredTypes = allHostelTypes.filter(function(type) {
                        const typeLocation = (type.ht_location || 'on_campus'); // Default to on_campus if not set
                        // Match if exact match OR if selecting 'off_campus' and type is 'out_of_campus' (old value)
                        if (selectedLocation === 'off_campus') {
                            return typeLocation === 'off_campus' || typeLocation === 'out_of_campus';
                        }
                        return typeLocation === selectedLocation;
                    });
                    
                    // Rebuild hostel type dropdown with filtered options
                    if (filteredTypes.length > 0) {
                        $('#hostel-type-select').html('<option value="">Select Type</option>');
                        filteredTypes.forEach(function(type) {
                            $('#hostel-type-select').append(
                                '<option value="' + type.ht_id + '" data-location="' + (type.ht_location || 'on_campus') + '">' + type.ht_name + '</option>'
                            );
                        });
                        $('#hostel-type-select').prop('disabled', false);
                    } else {
                        $('#hostel-type-select').html('<option value="">No hostel types available for this location</option>');
                    }
                } else {
                    // Reset to show all options when no location is selected
                    $('#hostel-type-select').html('<option value="">Select Location First</option>');
                    allHostelTypes.forEach(function(type) {
                        $('#hostel-type-select').append(
                            '<option value="' + type.ht_id + '" data-location="' + (type.ht_location || 'on_campus') + '">' + type.ht_name + '</option>'
                        );
                    });
                }
            });

            // Hostel type change handler
            $('#hostel-type-select').change(function() {
                const selectedHostelType = $(this).val();
       
                
                // Reset floor select and displays
                $('#floor-select').html('<option value="">Select a floor</option>');
                $('#floor-select').prop('disabled', true);
                $('#add-room-button').prop('disabled', true);
                $('#rooms-container').empty();
                $('#building-seats-display').hide();
                
                if (selectedHostelType) {
                    // Store current hostel type for later use
                    window.currentHostelType = selectedHostelType;
                    
                    // Fetch counts for Male and Female based on selected hostel type
                    fetchNotAssignedCount('male', selectedHostelType);
                    fetchNotAssignedCount('female', selectedHostelType);
                    
                    // Calculate and display building-wise seat breakdown
                    const buildingBreakdown = calculateBuildingSeatBreakdown(selectedHostelType);
                    const totalUsableNow = buildingBreakdown.totalUsableNow;
                    
                    // Populate building breakdown
                    $('#building-seats-breakdown').html(buildingBreakdown.html);
                    $('#total-seats-count').text(buildingBreakdown.totalAvailable);
                    $('#paid-not-assigned-count').text(buildingBreakdown.paidNotAssigned);
                    $('#usable-now-count').text(totalUsableNow);
                    $('#building-seats-note').hide();
                    $('#building-seats-display').show();
                    
                    // Filter floors based on selected hostel type
                    const filteredFloors = allFloors.filter(floor => 
                        floor.hf_new_hostel_type == selectedHostelType
                    );
                    
                    if (filteredFloors.length > 0) {
                        filteredFloors.forEach(function(floor) {
                            // Gender text based on hostel type
                            let genderText = '';
                            if (floor.hf_hostel_type == 1) {
                                genderText = ' (Boys)';
                            } else if (floor.hf_hostel_type == 2) {
                                genderText = ' (Girls)';
                            } else {
                                genderText = ' '; 
                            }
                            
                            $('#floor-select').append(
                                '<option value="' + floor.hf_id + '">' + floor.hf_name + genderText + '</option>'
                            );
                        });
                        $('#floor-select').prop('disabled', false);
                    } else {
                        $('#floor-select').html('<option value="">No floors available for this hostel type</option>');
                    }
                }
            });

            // Floor select change handler
            $('#floor-select').change(function() {
                const selectedFloor = $(this).val();
                const selectedHostelType = $('#hostel-type-select').val();
                
                
                renderRooms(selectedHostelType, selectedFloor);
                $('#add-room-button').prop('disabled', !selectedFloor);
            });

            // Add room button click handler
            $('#add-room-button').click(function() {
                // Reset modal for adding new room
                $('#roomModalTitle').html('<i class="fa fa-plus-circle mr-2"></i>Add New Room');
                $('#room-id').val('');
                $('#edit-mode').val('add-room');
                $('#room-number').val('').prop('readonly', false);
                $('#room-capacity').val('');
                $('#ac-availability').prop('checked', false);
                $('#heater-availability').prop('checked', false);
                $('#refrigerator-availability').prop('checked', false);
                $('#addRoomModal').modal('show');
            });


            // Edit room button click handler
            $(document).on('click', '.edit-room-button', function() {
                const roomId = $(this).data('room-id');
                const roomNo = $(this).data('room-no');
                const capacity = $(this).data('capacity');
                const acAvailability = $(this).data('ac') === 'YES';
                const heaterAvailability = $(this).data('heater') === 'YES';
                const refrigeratorAvailability = $(this).data('refrigerator') === 'YES';

                // Set modal for editing room
                $('#roomModalTitle').html('<i class="fa fa-edit mr-2"></i>Edit Room');
                $('#room-id').val(roomId);
                $('#edit-mode').val('edit-room');
                $('#room-number').val(roomNo).prop('readonly', true);
                $('#room-capacity').val(capacity);
                $('#ac-availability').prop('checked', acAvailability);
                $('#heater-availability').prop('checked', heaterAvailability);
                $('#refrigerator-availability').prop('checked', refrigeratorAvailability);
                $('#addRoomModal').modal('show');
            });

            // Save room button click handler
            $('#saveRoomBtn').click(function() {
                const roomId = $('#room-id').val();
                const editMode = $('#edit-mode').val();
                const roomNumber = $('#room-number').val();
                const roomCapacity = $('#room-capacity').val();
                const acAvailability = $('#ac-availability').is(":checked") ? 'YES' : 'NO';
                const heaterAvailability = $('#heater-availability').is(":checked") ? 'YES' : 'NO';
                const refrigeratorAvailability = $('#refrigerator-availability').is(":checked") ? 'YES' : 'NO';
                const selectedHostelType = $('#hostel-type-select').val();
                const selectedFloor = $('#floor-select').val();

                if (roomNumber && roomCapacity) {
                    const url = '{{ url('/add-room') }}';

                    const data = {
                        _token: '{{ csrf_token() }}',
                        room_number: roomNumber,
                        room_capacity: roomCapacity,
                        hostel_type: selectedHostelType,
                        floor: selectedFloor,
                        ac_availability: acAvailability,
                        heater_availability: heaterAvailability,
                        refrigerator_availability: refrigeratorAvailability
                    };

                    if (editMode === 'edit-room') {
                        data.room_id = roomId;
                    }

                    // Show loading spinner
                    $('#saveRoomBtn').html('<i class="fa fa-spinner fa-spin mr-1"></i>Saving...');
                    $('#saveRoomBtn').prop('disabled', true);

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            $('#addRoomModal').modal('hide');
                            showToast('success', editMode === 'edit-room' ? 'Room updated successfully!' : 'Room added successfully!');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function(error) {
                            $('#saveRoomBtn').html('<i class="fa fa-save mr-1"></i>Save Room');
                            $('#saveRoomBtn').prop('disabled', false);
                            showToast('error', error.responseJSON?.error || 'An error occurred.');
                        }
                    });
                } else {
                    showToast('error', 'Please fill out all required fields.');
                }
            });

            // Room status buttons click handler
            $(document).on('click', '.enable-room-button, .disable-room-button', function() {
                const action = $(this).data('action');
                const roomId = $(this).data('room-id');

                currentActionData = {
                    roomId: roomId,
                    action: action,
                    hostelType: $('#hostel-type-select').val(),
                    floorId: $('#floor-select').val()
                };

                const message = action === 'enable'
                    ? 'Are you sure you want to enable this room?'
                    : 'Are you sure you want to disable this room?';

                $('#confirmationMessage').text(message);
                $('#confirmActionBtn')
                    .text(action === 'enable' ? 'Enable' : 'Disable')
                    .removeClass('btn-outline-success btn-outline-danger btn-primary')
                    .addClass(action === 'enable' ? 'btn-success' : 'btn-danger');
                $('#confirmationModal').modal('show');
            });

            // Confirmation action handler
            $('#confirmActionBtn').click(function() {
                if (!currentActionData) return;

                const url = currentActionData.action === 'enable'
                    ? '{{ route("room_enable", "") }}/' + currentActionData.roomId
                    : '{{ route("room_disable", "") }}/' + currentActionData.roomId;

                // Show loading spinner
                const originalBtnText = $('#confirmActionBtn').html();
                $('#confirmActionBtn').html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
                $('#confirmActionBtn').prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#confirmationModal').modal('hide');
                        showToast('success', response.success);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(response) {
                        $('#confirmActionBtn').html(originalBtnText);
                        $('#confirmActionBtn').prop('disabled', false);
                        $('#confirmationModal').modal('hide');
                        showToast('error', response.responseJSON?.error || 'An error occurred.');
                    }
                });
            });

            // Bed click handler - handles both occupied and available beds
            $(document).on('click', '.bed', function() {
                const bedId = $(this).data('bed-id');
                const studentInfo = $(this).data('student') || '';
                const isOccupied = $(this).hasClass('btn-danger');

                if (isOccupied) {
                    // Handle occupied bed - show vacate confirmation
                    $('#confirmationModal').modal('show');
                    $('#confirmationMessage').html(`
                        <div class="text-center mb-3">
                            <i class="fa fa-bed text-danger fa-3x mb-2"></i>
                            <p>Are you sure you want to vacate this bed?</p>
                            ${studentInfo ? `<div class="alert alert-info"><strong>Student:</strong> ${studentInfo}</div>` : ''}
                        </div>
                    `);
                    $('#confirmActionBtn')
                        .html('<i class="fa fa-check mr-1"></i>Vacate')
                        .removeClass('btn-success btn-danger')
                        .addClass('btn-primary');

                    $('#confirmActionBtn').one('click', function() {
                        // Show loading spinner
                        $('#confirmActionBtn').html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
                        $('#confirmActionBtn').prop('disabled', true);

                        $.ajax({
                            url: '{{ url('/vacate-bed-status') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: bedId,
                                is_occupied: 'N'
                            },
                            success: function(response) {
                                $('#confirmationModal').modal('hide');
                                showToast('success', response.message || 'Bed vacated successfully.');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            },
                            error: function(error) {
                                $('#confirmActionBtn').html('<i class="fa fa-check mr-1"></i>Vacate');
                                $('#confirmActionBtn').prop('disabled', false);
                                showToast('error', error.responseJSON?.error || 'An error occurred while updating the bed status.');
                            }
                        });
                    });
                } else {
                    // Handle available bed - show occupy modal
                    window.currentBedId = bedId;
                    $('#student-input').val('');
                    $('#occupyBedModal').modal('show');
                }
            });

            // Occupy bed form submit handler
            $('#occupyBedForm').submit(function(e) {
                e.preventDefault();
                
                const bedId = window.currentBedId;
                const studentInput = $('#student-input').val().trim();

                // Validate input
                if (!studentInput) {
                    showToast('error', 'Please enter enrollment number/ applicant ID.');
                    return;
                }

                // Show loading spinner
                $('#occupyBedBtn').html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
                $('#occupyBedBtn').prop('disabled', true);

                const data = {
                    _token: '{{ csrf_token() }}',
                    bed_id: bedId,
                    student_input: studentInput
                };

                $.ajax({
                    url: '{{ url('/occupy-bed-status') }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#occupyBedModal').modal('hide');
                        showToast('success', response.message || 'Bed occupied successfully.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(error) {
                        $('#occupyBedBtn').html('<i class="fa fa-bed mr-1"></i>Occupy Bed');
                        $('#occupyBedBtn').prop('disabled', false);
                        showToast('error', error.responseJSON?.error || 'An error occurred while occupying the bed.');
                    }
                });
            });

            // Function to update building display based on mode
            function updateBuildingDisplay(mode) {
                if (window.currentHostelType) {
                    const buildingBreakdown = calculateBuildingSeatBreakdown(window.currentHostelType, mode);
                    $('#building-seats-breakdown').html(buildingBreakdown.html);
                    if (mode === 'available') {
                        $('#total-seats-count').text(buildingBreakdown.totalAvailable);
                        $('#paid-not-assigned-count').text(buildingBreakdown.paidNotAssigned);
                        $('#usable-now-count').text(buildingBreakdown.totalUsableNow);
                        $('#building-seats-note').hide();
                    } else {
                        $('#total-seats-count').text(buildingBreakdown.totalCapacity);
                        $('#building-seats-note').hide();
                    }
                }
            }

            // Function to calculate building-wise seat breakdown
            function calculateBuildingSeatBreakdown(hostelType, mode = 'available') {
                const filteredFloors = allFloors.filter(floor => 
                    floor.hf_new_hostel_type == hostelType
                );
                
                let totalSeats = 0;
                let totalAvailable = 0;
                let totalCapacity = 0;
                let breakdownHtml = '';
                
                filteredFloors.forEach(floor => {
                    // Calculate seats for this floor
                    const floorRooms = rooms.filter(room => 
                        room.hr_hf_id == floor.hf_id && room.hr_ht_id == hostelType
                    );
                    
                    let floorSeats = 0;
                    let totalBedsInFloor = 0;
                    let availableBedsInFloor = 0;
                    
                    floorRooms.forEach(room => {
                        if (room.hr_status !== 'D') { // Only count enabled rooms
                            room.beds.forEach(bed => {
                                totalBedsInFloor++;
                                if (bed.hb_is_occupied === 'N') { // Count only available beds
                                    floorSeats++;
                                    availableBedsInFloor++;
                                }
                            });
                        }
                    });
                    
                    // Determine paid-not-assigned counts by gender (hostelType 1=Male, 2=Female)
                    const paidNotAssigned = hostelType == 1 ? (window.maleNotAssignedPaid || 0) : (hostelType == 2 ? (window.femaleNotAssignedPaid || 0) : 0);
                    const adjustedAvailableForFloor = Math.max(0, availableBedsInFloor - Math.min(paidNotAssigned, availableBedsInFloor));

                    // Debug logging for 3rd FLOOR (Boys)
                    if (floor.hf_name && floor.hf_name.toLowerCase().includes('3rd') && floor.hf_hostel_type == 1) {
                        console.log('3rd FLOOR (Boys) Debug:', {
                            floorId: floor.hf_id,
                            floorName: floor.hf_name,
                            totalRooms: floorRooms.length,
                            totalBeds: totalBedsInFloor,
                            availableBeds: availableBedsInFloor,
                            paidNotAssigned: paidNotAssigned,
                            adjustedAvailableForFloor: adjustedAvailableForFloor,
                            rooms: floorRooms.map(r => ({
                                roomNo: r.hr_room_no,
                                capacity: r.hr_capacity,
                                status: r.hr_status,
                                statusText: r.hr_status === 'D' ? 'DISABLED' : 'ENABLED',
                                bedCount: r.beds.length,
                                availableBeds: r.beds.filter(b => b.hb_is_occupied === 'N').length,
                                occupiedBeds: r.beds.filter(b => b.hb_is_occupied === 'Y').length
                            }))
                        });
                    }
                    
                    // For available mode, sum adjusted available; for total mode, sum raw available
                    totalSeats += (mode === 'available' ? adjustedAvailableForFloor : floorSeats);
                    totalAvailable += availableBedsInFloor;
                    totalCapacity += totalBedsInFloor;
                    
                    // Add floor breakdown to HTML
                    const genderText = floor.hf_hostel_type == 1 ? ' (Boys)' : 
                                     floor.hf_hostel_type == 2 ? ' (Girls)' : '';
                    
                    // Determine icon and color based on gender
                    const genderIcon = floor.hf_hostel_type == 1 ? 'fa-male' : 
                                     floor.hf_hostel_type == 2 ? 'fa-female' : 'fa-building';
                    const genderColor = floor.hf_hostel_type == 1 ? 'text-primary' : 
                                      floor.hf_hostel_type == 2 ? 'text-danger' : 'text-secondary';
                    
                    if (mode === 'total') {
                        breakdownHtml += `
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-dark">
                                                    <i class="fa ${genderIcon} mr-2 ${genderColor} fa-lg"></i>${floor.hf_name}
                                                </h6>
                                                <small class="text-muted">${genderText}</small>
                                            </div>
                                            <span class="badge badge-primary badge-pill px-3 py-2">${totalBedsInFloor}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        breakdownHtml += `
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-dark">
                                                    <i class="fa ${genderIcon} mr-2 ${genderColor} fa-lg"></i>${floor.hf_name}
                                                </h6>
                                                <small class="text-muted">${genderText}</small>
                                            </div>
                                            <div class="text-right">
                                                <div class="badge badge-success badge-pill px-3 py-2 mb-1 d-block">${availableBedsInFloor} available</div>
                                                <div class="badge badge-light badge-pill px-3 py-2 d-block">${totalBedsInFloor} total</div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                
                const paidNotAssigned = hostelType == 1 ? (window.maleNotAssignedPaid || 0) : (hostelType == 2 ? (window.femaleNotAssignedPaid || 0) : 0);
                const totalUsableNow = Math.max(0, totalAvailable - paidNotAssigned);

                return {
                    totalSeats: totalSeats,
                    totalAvailable: totalAvailable,
                    paidNotAssigned: paidNotAssigned,
                    totalUsableNow: totalUsableNow,
                    totalCapacity: totalCapacity,
                    html: breakdownHtml
                };
            }

            // Function to calculate total available seats for a hostel type
            function calculateTotalAvailableSeats(hostelType) {
                let totalSeats = 0;
                const filteredRooms = rooms.filter(room => room.hr_ht_id == hostelType);
                
                filteredRooms.forEach(room => {
                    if (room.hr_status !== 'D') { // Only count enabled rooms
                        room.beds.forEach(bed => {
                            if (bed.hb_is_occupied === 'N') { // Count only available beds
                                totalSeats++;
                            }
                        });
                    }
                });
                
                return totalSeats;
            }
            
            // Function to calculate floor count for a hostel type
            function calculateFloorCount(hostelType) {
                const filteredFloors = allFloors.filter(floor => 
                    floor.hf_new_hostel_type == hostelType
                );
                return filteredFloors.length;
            }

            // Function to render rooms
            function renderRooms(hostelType, floorId) {
                $('#rooms-container').empty();

                const filteredRooms = rooms.filter(room =>
                    room.hr_hf_id == floorId && room.hr_ht_id == hostelType
                );
                console.log(filteredRooms);
                if (filteredRooms.length === 0) {
                    $('#rooms-container').html(`
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle fa-2x mb-3"></i>
                                <h4>No rooms found</h4>
                                <p>No rooms available for this hostel and floor combination.</p>
                            </div>
                        </div>
                    `);
                    return;
                }

                filteredRooms.forEach(room => {
                    // Generate badges for available facilities
                    let facilitiesBadges = '';
                    if (room.hr_ac_availability === 'YES') {
                        facilitiesBadges += '<span class="badge badge-info mr-1"><i class="fa fa-snowflake-o"></i> AC</span>';
                    }
                    if (room.hr_heater_availability === 'YES') {
                        facilitiesBadges += '<span class="badge badge-warning mr-1"><i class="fa fa-fire"></i> Heater</span>';
                    }
                    if (room.hr_refrigerator_availability === 'YES') {
                        facilitiesBadges += '<span class="badge badge-primary"><i class="fa fa-cube"></i> Refrigerator</span>';
                    }

                    const roomHtml = `
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm${room.hr_status === 'D' ? ' bg-light' : ''}" style="${room.hr_status === 'D' ? 'opacity: 0.6; position: relative;' : ''}">
                                ${room.hr_status === 'D' ? '<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,0.1) 10px, rgba(0,0,0,0.1) 20px); pointer-events: none; z-index: 1;"></div>' : ''}
                                <div class="card-header ${room.hr_status === 'D' ? 'bg-secondary text-white' : 'bg-light'}" style="${room.hr_status === 'D' ? 'position: relative; z-index: 2;' : ''}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0"><i class="fa fa-door-open mr-1"></i>Room ${room.hr_room_no}</h5>
                                        ${room.hr_status === 'D' ? '<span class="badge badge-warning">Disabled</span>' : ''}
                                    </div>
                                </div>
                                <div class="card-body" style="${room.hr_status === 'D' ? 'position: relative; z-index: 2;' : ''}">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="badge badge-light p-2"><i class="fa fa-users mr-1"></i>Capacity: ${room.hr_capacity}</span>
                                        <span class="badge badge-light p-2"><i class="fa fa-bed mr-1"></i>Beds: ${room.beds.length}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="badge badge-success p-2"><i class="fa fa-check-circle mr-1"></i>Available: ${room.beds.filter(bed => bed.hb_is_occupied === 'N').length}</span>
                                        <span class="badge badge-danger p-2"><i class="fa fa-times-circle mr-1"></i>Occupied: ${room.beds.filter(bed => bed.hb_is_occupied === 'Y').length}</span>
                                    </div>
                                    <div class="facilities-badges mb-3">
                                        ${facilitiesBadges || '<span class="badge badge-secondary">No facilities</span>'}
                                    </div>
                                    <div class="beds-container">
                                        <h6 class="text-muted mb-2 border-bottom pb-2">Bed Status</h6>
                                        ${room.beds.map(bed => `
                                            <div class="mb-2">
                                                <button class="btn btn-block ${bed.hb_is_occupied === 'Y' ? 'btn-danger' : 'btn-success'} bed bed-${bed.hb_bed_number}"
                                                    data-room="${room.hr_room_no}"
                                                    data-bed="${bed.hb_bed_number}"
                                                    data-bed-id="${bed.hb_id}"
                                                    data-student="${bed.hb_enrollment_no || ''}"
                                                    ${room.hr_status === 'D' ? 'disabled' : ''}>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span><i class="fa fa-bed mr-2"></i>Bed ${bed.hb_bed_number}</span>
                                                        <span class="badge ${bed.hb_is_occupied === 'Y' ? 'badge-light' : 'badge-light'}">
                                                            ${bed.hb_is_occupied === 'Y' ? 'Occupied' : 'Available'}
                                                        </span>
                                                    </div>
                                                    ${bed.hb_is_occupied === 'Y' ? `<small class="d-block mt-1 text-white">${bed.hb_enrollment_no || 'No Enrollment'}</small>` : ''}
                                                </button>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top" style="${room.hr_status === 'D' ? 'position: relative; z-index: 2;' : ''}">
                                    ${room.hr_status === 'D'
                                        ? `<button class="btn btn-success btn-block enable-room-button" data-room-id="${room.hr_id}" data-action="enable">
                                            <i class="fa fa-check-circle mr-2"></i>Enable Room
                                           </button>`
                                        : `<div class="d-flex justify-content-between">
                                            <button class="btn btn-outline-danger disable-room-button" data-room-id="${room.hr_id}" data-action="disable" style="width: 48%">
                                                <i class="fa fa-ban mr-2"></i>Disable
                                            </button>
                                            <button class="btn btn-outline-primary edit-room-button" data-room-id="${room.hr_id}"
                                                data-room-no="${room.hr_room_no}" data-capacity="${room.hr_capacity}"
                                                data-ac="${room.hr_ac_availability || 'NO'}" data-heater="${room.hr_heater_availability || 'NO'}"
                                                data-refrigerator="${room.hr_refrigerator_availability || 'NO'}" style="width: 48%">
                                                <i class="fa fa-edit mr-2"></i>Edit
                                            </button>
                                           </div>`}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#rooms-container').append(roomHtml);
                });
            }

            // Function to load paid students by gender
            function loadPaidStudents(gender) {
                const selectedHostelType = $('#hostel-type-select').val();
                
                if (!selectedHostelType) {
                    showToast('error', 'Please select a hostel type first.');
                    return;
                }
                
                // Get selected hostel type's location
                const selectedHostelTypeObj = allHostelTypes.find(type => type.ht_id == selectedHostelType);
                const hostelLocation = selectedHostelTypeObj ? (selectedHostelTypeObj.ht_location || 'on_campus') : 'on_campus';
                
                // Show loading state
                $('#students-tbody').html('<tr><td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
                
                // Update modal title
                const genderText = gender === 'male' ? 'Male' : 'Female';
                const locationText = (hostelLocation === 'off_campus' || hostelLocation === 'out_of_campus') ? ' (off campus)' : ' (on campus)';
                $('#paidStudentsModalTitle').html('<i class="fa fa-' + (gender === 'male' ? 'male' : 'female') + ' mr-2"></i>' + genderText + locationText + ' — Paid but not assigned (awaiting seat allocation)');
                
                // Make AJAX call to get paid students with location filter
                $.ajax({
                    url: '/api/get-paid-students-by-gender?gender=' + gender + '&hostel_type_id=' + selectedHostelType + '&hostel_location=' + hostelLocation,
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Filter: show only NOT ASSIGNED
                            const notAssigned = (response.students || []).filter(function(s){
                                return (s.room_status || '').toLowerCase() !== 'assigned';
                            });
                            populateStudentsTable(notAssigned);
                            $('#total-count-badge').text('Not Assigned: ' + notAssigned.length);
                            // Update header badges
                            if (gender === 'male') {
                                $('#male-not-assigned-count').text(notAssigned.length);
                            } else {
                                $('#female-not-assigned-count').text(notAssigned.length);
                            }
                        } else {
                            $('#students-tbody').html('<tr><td colspan="5" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                            $('#total-count-badge').text('Total: 0');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading paid students:', error);
                        $('#students-tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                        $('#total-count-badge').text('Total: 0');
                    }
                });
            }
            
            // Export to Excel functionality
            $('#export-excel-btn').click(function() {
                exportToExcel();
            });
            
            // Function to export table data to Excel
            function exportToExcel() {
                const table = document.getElementById('students-tbody');
                const rows = table.querySelectorAll('tr');
                
                if (rows.length === 0 || (rows.length === 1 && rows[0].textContent.includes('No paid students found'))) {
                    alert('No data to export');
                    return;
                }
                
                // Get current gender from modal title
                const modalTitle = $('#paidStudentsModalTitle').text();
                const gender = modalTitle.includes('Male') ? 'Male' : 'Female';
                
                // Create CSV content
                let csvContent = 'Sr#,Enrollment No,Student Name,Room No,Status\n';
                
                rows.forEach(function(row, index) {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 5) {
                        const srNo = cells[0].textContent.trim();
                        const enrollment = cells[1].textContent.trim();
                        const name = cells[2].textContent.trim();
                        const roomNo = cells[3].textContent.trim();
                        const status = cells[4].textContent.trim().replace(/\s+/g, ' '); // Clean up status text
                        
                        csvContent += `"${srNo}","${enrollment}","${name}","${roomNo}","${status}"\n`;
                    }
                });
                
                // Create and download file
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `${gender}_Paid_Students_List_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            
            // Function to populate students table
            function populateStudentsTable(students) {
                const tbody = $('#students-tbody');
                tbody.empty();
                
                if (students.length === 0) {
                    tbody.html('<tr><td colspan="5" class="text-center text-muted">No paid students found</td></tr>');
                    return;
                }
                
                students.forEach(function(student, index) {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${student.id || 'N/A'}</td>
                            <td>${student.name || 'N/A'}</td>
                            <td>${student.room_number || 'N/A'}</td>
                            <td>
                                ${student.room_status === 'Assigned' ? 
                                    '<span class="badge badge-success"><i class="fa fa-bed mr-1"></i>Assigned</span>' : 
                                    '<span class="badge badge-warning"><i class="fa fa-exclamation-triangle mr-1"></i>Not Assigned</span>'
                                }
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        });
    </script>
@endsection
