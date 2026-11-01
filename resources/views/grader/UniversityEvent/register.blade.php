@extends('layouts.app', ['pageTitle' => 'BNU Event Registration'])
@section('content')
    <style>
        .msgDiv {
            display: flex;
            justify-content: center;
        }
    </style>
    @include('components.toast-notifications')
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add Event To BNU Calendar</h5>
                    </div>
                    <div class="card-body">
                        <form id="myForm">
                            @csrf
                            <div class="row">
                                <!-- General Information Section -->
                                <div class="col-12 mb-4">
                                    <h6 class="border-bottom pb-2 mb-3 text-primary font-weight-bold">
                                        <i class="fa fa-info-circle mr-2"></i> General Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Event Title</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light"><i class="fa fa-edit text-primary"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="event_title" id="title" placeholder="Enter Event Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Event Type</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light"><i class="fa fa-list-alt text-primary"></i></span>
                                                    </div>
                                                    <select class="form-control" name="event_type" id="type" required>
                                                        <option value="">Select Event Type</option>
                                                        <option value="all">All </option>
                                                        <option value="faculty">Faculty</option>
                                                        <option value="all_faculty">All Faculty</option>
                                                        <option value="all_student">All Student</option>
                                                        <option value="itrc">ITRC</option>
                                                        <option value="admin">Admin</option>
                                                        <option value="registrar">Registrar</option>
                                                        <option value="accounts">Accounts</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Description</label>
                                                <textarea class="form-control" name="event_description" id="description" rows="5" placeholder="Brief description of the event..." required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule Mode Toggle -->
                                <div class="col-12 mb-3">
                                    <div class="form-check ml-2 pl-4">
                                        <input type="checkbox" class="form-check-input" id="add_to_date_checkbox" name="is_multiple" style="transform: scale(1.2); margin-top: 0.3rem;">
                                        <label class="form-check-label text-danger font-weight-bold ml-2" for="add_to_date_checkbox">
                                            Switch to Multiple Dated Event Schedule
                                        </label>
                                    </div>
                                </div>

                                <!-- Single Schedule Section -->
                                <div class="col-12" id="single_schedule_card">
                                    <h6 class="border-bottom pb-2 mb-3 text-primary font-weight-bold">
                                        <i class="fa fa-calendar-day mr-2"></i> Single Event Schedule
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Date</label>
                                                <input type="date" class="form-control" name="event_date" id="event_date" min="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Start Time</label>
                                                <select class="form-control" name="event_start_time" id="start_time">
                                                    @foreach ($timeslots as $ts)
                                                        <option value="{{ $ts->ts_code }}"> {{ $ts->ts_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                            <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">End Time</label>
                                                <select class="form-control" name="event_end_time" id="end_time">
                                                    @foreach ($timeslots as $ts)
                                                        <option value="{{ $ts->ts_code }}"> {{ $ts->ts_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="mb-1 text-muted small">Building</label>
                                                <select class="form-control" name="event_venue_building" id="venue_building" onchange="loadRooms()">
                                                    <option disabled selected value=""> Select Building</option>
                                                    <option value="0"> Other</option>
                                                    @foreach ($buildings as $b)
                                                        <option value="{{ $b->building_id }}">{{ $b->building_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="mb-1 text-muted small">Available Room</label>
                                            <select class="form-control" name="event_venue_room" id="venue_room">
                                                <option disabled selected value=""> Select Room</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Date Batch Section -->
                                <div class="col-12 d-none" id="multiple_schedule_container">
                                    <h6 class="border-bottom pb-2 mb-3 text-primary font-weight-bold">
                                        <i class="fa fa-calendar-alt mr-2"></i> Multiple Dated Event Schedule
                                    </h6>
                                    
                                    <!-- Step 1: Manage Dates -->
                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            <label class="mb-1 text-muted small">1. Select Dates</label>
                                            <div class="input-group mb-2">
                                                <input type="date" class="form-control" id="batch_date_input" min="{{ date('Y-m-d') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary px-4" type="button" onclick="addDateToBatch()">
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">
                                                Pick a date and click 'Add'.
                                            </small>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="mb-1 text-muted small">Selected Dates:</label>
                                            <div id="selected_dates_container" class="p-2 bg-light rounded border d-flex flex-wrap align-items-center" style="min-height: 38px;">
                                                <span class="text-muted small pl-2 font-italic">No dates added yet.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Time & Building -->
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label class="mb-1 text-muted small">2. Define Time</label>
                                            <div class="d-flex">
                                                <select class="form-control mr-2" id="batch_start_time">
                                                    <option disabled selected>Start</option>
                                                    @foreach ($timeslots as $ts)
                                                        <option value="{{ $ts->ts_code }}"> {{ $ts->ts_name }}</option>
                                                    @endforeach
                                                </select>
                                                <select class="form-control" id="batch_end_time">
                                                    <option disabled selected>End</option>
                                                    @foreach ($timeslots as $ts)
                                                        <option value="{{ $ts->ts_code }}"> {{ $ts->ts_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="mb-1 text-muted small">3. Choose Venue</label>
                                            <select class="form-control" id="batch_building">
                                                <option disabled selected value=""> Select Building</option>
                                                <option value="0"> Other</option>
                                                @foreach ($buildings as $b)
                                                    <option value="{{ $b->building_id }}">{{ $b->building_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="padding-top: 24px;">
                                            <button type="button" class="btn btn-primary btn-block" id="btn_check_availability" onclick="checkBatchAvailability()">
                                                Check Availability
                                            </button>
                                        </div>
                                    </div>
                                        
                                    <!-- Step 3: Room Selection -->
                                    <div class="row mx-0 mb-4 p-3 bg-light border rounded align-items-center">
                                        <div class="col-md-8">
                                            <label class="mb-0 mr-3 text-muted small">
                                                4. Finalize Room:
                                            </label>
                                             <select class="form-control d-inline-block w-75 bg-white" id="batch_room">
                                                <option disabled selected value=""> Verify Availability First...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button type="button" class="btn btn-primary px-4" onclick="addBatchToSchedule()">
                                                <i class="fa fa-plus-circle mr-1"></i> Add to Event List
                                            </button>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 text-secondary small font-weight-bold text-uppercase">Finalized Schedule List</h6>
                                    <table class="table table-bordered table-sm bg-white text-center">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Venue</th>
                                                <th style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="schedule_table_body">
                                            <!-- Rows will be added here -->
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="schedule_json" id="schedule_json">
                                </div>
                            <div class="col-12 d-none" id="school-div">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <label>Select School</label>
                                    </div>
                                    @foreach ($schools as $school)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="schools[]" value="{{ $school->school_id }}"
                                                    class="form-check-input schools" id="{{ $school->school_id }}">
                                                <label class="form-check-label"
                                                    for="{{ $school->school_id }}">{{ $school->school_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                                <!-- Support Services Section -->
                                <div class="col-12 mb-3 mt-4">
                                    <h6 class="border-bottom pb-2 mb-3 text-primary font-weight-bold">
                                        <i class="fa fa-headset mr-2"></i> Support Services
                                    </h6>
                                    
                                    <div class="row">
                                        <!-- Admin Support -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check pl-4 mb-2">
                                                <input type="checkbox" class="form-check-input" id="add_admin_checkbox" name="add_admin_checkbox" style="transform: scale(1.2); margin-top: 0.2rem;">
                                                <label class="form-check-label ml-2" for="add_admin_checkbox">Admin Support</label>
                                            </div>
                                            <div id="to_admin_container" style="display: none;">
                                                <textarea class="form-control" name="to_admin_text" id="to_admin_text" rows="3" placeholder="Details for Admin..."></textarea>
                                            </div>
                                        </div>

                                        <!-- ITRC Support -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check pl-4 mb-2">
                                                <input type="checkbox" class="form-check-input" id="add_itrc_checkbox" name="add_itrc_checkbox" style="transform: scale(1.2); margin-top: 0.2rem;">
                                                <label class="form-check-label ml-2" for="add_itrc_checkbox">ITRC Support</label>
                                            </div>
                                            <div id="to_itrc_container" style="display: none;">
                                                <textarea class="form-control" name="to_itrc_text" id="to_itrc_text" rows="3" placeholder="Details for ITRC..."></textarea>
                                                <small class="form-text text-danger mt-2">
                                                    <i class="fa fa-info-circle mr-1"></i> Please ensure a test run with the ITRC team at least one day in advance.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Submit Buttons -->
                                <div class="col-12 mt-4 pt-3 border-top text-right">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="fa fa-save mr-2"></i> Submit Event
                                    </button>
                                    <div id="msg" class="alert msgDiv mt-3"></div>
                                </div>
                            </div>

                    </form>




                        <hr class="mt-5 mb-4">
                        
                        <!-- Event List Section -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                             <h5 class="font-weight-bold text-primary mb-0">
                                <i class="fa fa-list mr-2"></i> Event List
                            </h5>
                            <a href="{{ url('UniversityEvent/calendar') }}" class="btn btn-outline-primary btn-sm shadow-sm"> 
                                <i class="fa fa-calendar pr-1"></i> View Calendar
                            </a>
                        </div>
                    <table class="table table-striped table-hover" id="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Event Title</th>
                                <th scope="col">Event Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Venue</th>
                                <th scope="col">Admin Details</th>
                                <th scope="col">ITRC Details</th>
                                <th scope="col">Details</th>
                                <th scope="col">status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="event_table_body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="modal" tabindex="-1" id="Modal" style="top:25%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Other Events on the same date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="CloseModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Event Title</th>
                                <th scope="col">Time</th>
                                <th scope="col">Venue</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="CloseModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="ProceedFurther()">Proceed on</button>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
    <!-- Your other HTML content -->

    <!-- Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="eventDetailsBody">
                    <!-- Details will be populated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
@section('javascript')
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"
        integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script type="text/javascript">

            // Global Array to store user selected dates and schedule
            var batchDates = [];
            var scheduleList = [];

            document.getElementById('add_to_date_checkbox').addEventListener('change', function() {
                var isChecked = this.checked;
                
                if (isChecked) {
                    $('#single_schedule_card').addClass('d-none');
                    $('#multiple_schedule_container').removeClass('d-none');
                    
                    $('#event_date').prop('required', false);
                    $('#venue_building').prop('required', false);
                    $('#venue_room').prop('required', false);
                    $('#type').prop('required', false); 
                    
                } else {
                    $('#single_schedule_card').removeClass('d-none');
                    $('#multiple_schedule_container').addClass('d-none');
                    
                    // Reset All Multiple Schedule Data
                    scheduleList = []; 
                    $('#schedule_table_body').empty();
                    $('#schedule_json').val('');
                    
                    batchDates = [];
                    updateBatchDatesDisplay();
                    
                    // Reset Inputs
                    $('#batch_date_input').val('');
                    $('#batch_start_time').prop('selectedIndex', 0);
                    $('#batch_end_time').prop('selectedIndex', 0);
                    $('#batch_building').prop('selectedIndex', 0);
                    $('#batch_room').empty().append('<option disabled selected value=""> First click \'Verify Availability\' above</option>');
                }
            });

            function addDateToBatch() {
                var dateVal = $('#batch_date_input').val();
                if(!dateVal) return;
                
                if(batchDates.includes(dateVal)) {
                    alert('Date already added!');
                    return;
                }
                
                batchDates.push(dateVal);
                batchDates.sort(); // Keep sorted
                updateBatchDatesDisplay();
                $('#batch_date_input').val('');
                
                // STEP 1 COMPLETE: Enable Start Time
                $('#batch_start_time').prop('disabled', false);
                
                // Reset search
                $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
            }
            
            function removeBatchDate(date) {
                batchDates = batchDates.filter(d => d !== date);
                updateBatchDatesDisplay();
                 $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
            }
            
            function updateBatchDatesDisplay() {
                var container = $('#selected_dates_container');
                container.empty();
                
                if(batchDates.length === 0) {
                     container.append('<span class="text-muted small pl-2 font-italic">No dates added yet.</span>');
                     // RESET: If no dates, disable everything downstream
                    $('#batch_start_time').prop('disabled', true).val('');
                    $('#batch_end_time').prop('disabled', true).val('');
                    $('#batch_building').prop('disabled', true).val('');
                    $('#btn_check_availability').prop('disabled', true);
                     return;
                }
                
                batchDates.forEach(function(date) {
                    var badge = `<span class="badge badge-primary badge-pill m-1 px-3 py-2 shadow-sm" style="font-size: 0.9rem; font-weight: 500;">
                        ${date} <i class="fa fa-times ml-2" style="cursor:pointer; opacity: 0.7;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.7" onclick="removeBatchDate('${date}')"></i>
                    </span>`;
                    container.append(badge);
                });
            }

            // === FUNCTION: Check Room Availability ===
            // This function asks the server: "Which rooms are free for these dates/times?"
            function checkBatchAvailability() {
                // Step 1: Gather Inputs
                var building = $('#batch_building').val();
                var start = $('#batch_start_time').val();
                var end = $('#batch_end_time').val();
                
                // Step 2: Validate that all required fields are filled
                if(batchDates.length === 0) {
                    showToast('error', 'Please add at least one date.');
                    return;
                }
                if(!building) {
                    showToast('error', 'Please select a Venue Building.');
                    return;
                }
                if(!start || !end) {
                     showToast('error', 'Please select both Start and End times.');
                     return;
                }
                
                // Show Spinner and Disable Button
                var btn = $('#btn_check_availability');
                var originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Checking...');
                
                $.ajax({
                    type: "POST",
                    url: "/api/UniversityEvent/get-room-list",
                    headers: {
                        Authorization: 'Bearer {{ auth()->user()->api_token }}',
                        Accept: 'application/json'
                    },
                    data: {
                        dates: batchDates, // Send array
                        building_code: building,
                        from: start,
                        to: end
                    },
                    success: function(response) {
                        $('#batch_room').empty();
                        
                        if(response.length === 0) {
                             $('#batch_room').append('<option disabled selected>No rooms found</option>');
                             showToast('info', 'No rooms found for the selected criteria.');
                        } else {
                             $('#batch_room').append('<option disabled selected value="">Select Room</option>');
                             
                             response.forEach(function(room) {
                                 var isDisabled = room.is_free ? '' : 'disabled';
                                 // Completely removed style attribute to use browser default
                                 $('#batch_room').append(`<option value="${room.room_id}" ${isDisabled}>${room.display_text}</option>`);
                             });
                             // Success Toast REMOVED as requested. Spinner indicates completion.
                             // Trigger focus on room dropdown for better UX
                             $('#batch_room').focus();
                        }
                    }, 
                    error: function() {
                        showToast('error', 'Failed to check availability. Please try again.');
                    },
                    complete: function() {
                        // Restore Button State
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            }

            function addBatchToSchedule() {
                var roomVal = $('#batch_room').val();
                var startVal = $('#batch_start_time').val();
                var endVal = $('#batch_end_time').val();
                var buildingVal = $('#batch_building').val();

                // Validation steps
                if(batchDates.length === 0) {
                     showToast('error', 'No dates selected. Please add dates first.');
                     return;
                }
                if(!buildingVal) {
                     showToast('error', 'Please select a building.');
                     return;
                }
                if(!startVal || !endVal) {
                     showToast('error', 'Please select valid start and end times.');
                     return;
                }
                if(!roomVal) {
                    showToast('error', 'Please select a room from the dropdown.');
                   // Highlighting the input
                   $('#batch_room').focus();
                    return;
                }
                
                // Clean up the text to remove emojis and status for display in table
                var rawText = $("#batch_room option:selected").text();
                var roomText = rawText.replace('🟢 ', '').replace('🔴 ', '').replace(' (Available)', '').replace(' (Reserved)', '').trim();
                
                // Get readable text for display
                var startText = $("#batch_start_time option:selected").text().trim();
                var endText = $("#batch_end_time option:selected").text().trim();

                
                // Add all dates to main schedule list
                batchDates.forEach(function(date) {
                    var obj = {
                        date: date,
                        start_time: startVal,
                        end_time: endVal,
                        start_time_text: startText, // Store text for display
                        end_time_text: endText,     // Store text for display
                        building: buildingVal,
                        room: roomVal,
                        room_name: roomText
                    };
                    scheduleList.push(obj);
                });
                
                updateScheduleTable();
                
                // Clear batch selection to allow new batch
                batchDates = [];
                updateBatchDatesDisplay();
                $('#batch_room').empty().append('<option disabled selected value=""> Select Room (Check Availability First)</option>');
                showToast('success', 'Events successfully added to schedule list!');
            }

            function updateScheduleTable() {
                var tbody = $('#schedule_table_body');
                tbody.empty();
                
                scheduleList.forEach(function(item, index) {
                    var tr = `<tr>
                        <td>${item.date}</td>
                        <td>${item.start_time_text} - ${item.end_time_text}</td>
                        <td>${item.room_name}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm py-0" onclick="removeSchedule(${index})">X</button>
                        </td>
                    </tr>`;
                    tbody.append(tr);
                });

                // Update hidden input
                $('#schedule_json').val(JSON.stringify(scheduleList));
            }

            function removeSchedule(index) {
                scheduleList.splice(index, 1);
                updateScheduleTable();
            }





            // ---------------------------------------------------------
            // SIMPLE TIME VALIDATION LOGIC (For 1 Year Exp Developer)
            // ---------------------------------------------------------

            // Common function to handle time constraints
            function handleTimeRules(startSelector, endSelector) {
                var startSelect = $(startSelector);
                var endSelect = $(endSelector);
                
                // Initially disable End Time
                // endSelect.prop('disabled', true);

                startSelect.change(function() {
                    var startIndex = $(this).prop('selectedIndex'); // Get index (0, 1, 2...)
                    
                    if (startIndex === 0) {
                        endSelect.prop('disabled', true);
                        endSelect.val('');
                        // If Start is reset, disable downstream (Building)
                        if(startSelector === '#batch_start_time') {
                             $('#batch_building').prop('disabled', true).val('');
                             $('#btn_check_availability').prop('disabled', true);
                        }
                        return;
                    }

                    // Enable End Time
                    endSelect.prop('disabled', false);

                    // Loop through End Time options to disable invalid ones
                    endSelect.find('option').each(function(index) {
                        if (index <= startIndex) {
                            $(this).prop('disabled', true); // Disable past or equal times
                            $(this).css('background-color', '#f0f0f0'); // Visual grey background for disabled
                            $(this).css('color', '#999'); // Visual grey text
                        } else {
                            $(this).prop('disabled', false);
                            $(this).css('background-color', '#fff'); // White background for enabled
                            $(this).css('color', '#333'); // Normal text
                        }
                    });

                    // Logic: If previously selected End Time is now invalid, clear it
                    var currentEndIndex = endSelect.prop('selectedIndex');
                    if (currentEndIndex <= startIndex) {
                        endSelect.val(''); 
                    }
                    
                    // Reset room/building because time changed
                    if(startSelector === '#batch_start_time') {
                        // BATCH MODE: clear batch building + batch room and disable Check Availability
                        $('#batch_building').val('');
                        $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
                        $('#btn_check_availability').prop('disabled', true);
                    } else {
                        // SINGLE MODE: clear single venue building + venue room
                        $('#venue_building').val('');
                        $('#venue_room').empty().append('<option disabled selected value="">Select Room</option>');
                    }
                });

                // Validation when End Time is changed
                endSelect.change(function() {
                     var startIndex = startSelect.prop('selectedIndex');
                     var endIndex = $(this).prop('selectedIndex');

                     if(endIndex <= startIndex) {
                         showToast('error', 'End Time must be greater than Start Time');
                         $(this).val('');
                         // Disable downstream if invalid
                         if(startSelector === '#batch_start_time') {
                             $('#batch_building').prop('disabled', true).val('');
                             $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
                             $('#btn_check_availability').prop('disabled', true);
                         } else {
                             $('#venue_building').val('');
                             $('#venue_room').empty().append('<option disabled selected value="">Select Room</option>');
                         }
                         return;
                     }

                     // VALID END TIME SELECTED: reset building & room so user re-picks venue
                     if(startSelector === '#batch_start_time') {
                         $('#batch_building').prop('disabled', false).val('');
                         $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
                         $('#btn_check_availability').prop('disabled', true);
                     } else {
                         $('#venue_building').val('');
                         $('#venue_room').empty().append('<option disabled selected value="">Select Room</option>');
                     }
                });
            }

            // Apply logic to Single Schedule
            handleTimeRules('#start_time', '#end_time');

            // Apply logic to Multiple (Batch) Schedule
            handleTimeRules('#batch_start_time', '#batch_end_time');

            // Building Change Listener for Batch (Enable Check Button & Reset Room)
            $('#batch_building').change(function(){
                // Reset and Disable Room dropdown whenever building changes
                $('#batch_room').empty().append('<option disabled selected value=""> Verify Availability First...</option>');
                
                if($(this).val()) {
                    $('#btn_check_availability').prop('disabled', false);
                } else {
                    $('#btn_check_availability').prop('disabled', true);
                }
            });


        $(document).ready(function() {
            // Disable end times on load
            $('#end_time').prop('disabled', true);
            
            // disable all batch fields initially
            $('#batch_start_time').prop('disabled', true);
            $('#batch_end_time').prop('disabled', true);
            $('#batch_building').prop('disabled', true);
            $('#btn_check_availability').prop('disabled', true);

            var dataTable = $('#table').DataTable({
                order: [
                    [0, 'desc']
                ],
                "pageLength": 100,
            });

            loadEvents();
            $("#type").change(function() {
                if ($(this).val() == "faculty") {
                    $("#school-div").removeClass("d-none");
                } else {
                    $("#school-div").addClass("d-none");
                }
            })
            // $("#myForm").submit(function(event) {
            //     event.preventDefault();

            //     // -----------------------------------------------------------
            //     // FINAL SUBMISSION CHECK (Validation before Server)
            //     // -----------------------------------------------------------
            //     var isMultiple = $('#add_to_date_checkbox').is(':checked');
                
            //     if(isMultiple) {
            //         // Check if Schedule List is empty
            //         if(scheduleList.length === 0) {
            //             showToast('error', 'Please add at least one slot to the Event List first.', 'List Empty');
            //             // Scroll to the list area so they see it
            //             $('html, body').animate({
            //                 scrollTop: $("#selected_dates_container").offset().top - 100
            //             }, 500);
            //             return false; 
            //         }
            //     }

            //     if ($("#myForm").validate()) {
            //         var formData = $("#myForm").serialize();
            //         // console.log(formData);
            //         $.ajax({
            //             type: "POST",
            //             url: "/api/UniversityEvent/store",
            //             data: formData,
            //             headers: {
            //                 Authorization: 'Bearer {{ auth()->user()->api_token }}',
            //                 Accept: 'application/json'
            //             },
            //             success: function(response) {

            //                 // $(".msgDiv").removeClass("d-none");
            //                 // $("#msg").text(response.msg);
            //                 if (response.error == true) {
            //                     $('#msg').html(response.msg);
            //                     $("#msg").removeClass("text-success");
            //                     $("#msg").addClass("text-danger");
            //                     $('#msg').fadeIn('slow');
            //                     $('#msg').fadeOut(5000);

            //                 } else {
            //                     refreshFields();
            //                     $('#msg').html(response.msg);
            //                     $("#msg").removeClass("text-danger");
            //                     $("#msg").addClass("text-success");
            //                     $('#msg').fadeIn('slow');
            //                     $('#msg').fadeOut(5000);
            //                 }
            //                 loadEvents();
            //             },
            //             error: function(response, xhr, status, error) {
            //                 var data = response.responseJSON;
                            
            //                 if (data && 'errors' in data) {
            //                     var errors = data.errors;
            //                     $.each(errors, function (i, obj) {
            //                         // Show each error as a toast
            //                         showToast('error', obj[0]);
            //                     });
            //                 } else if(data && data.msg) {
            //                      showToast('error', data.msg);
            //                 } else {
            //                     showToast('error', 'An unknown error occurred.');
            //                 }
            //             }
            //         });
            //     }
            // });

            $("#myForm").submit(function(event) {
                event.preventDefault();

                // alert('test');
                // return false;

                // -----------------------------------------------------------
                // FINAL SUBMISSION CHECK (Validation before Server)
                // -----------------------------------------------------------
                var isMultiple = $('#add_to_date_checkbox').is(':checked');
                
                if(isMultiple) {
                    // Check if Schedule List is empty
                    if(scheduleList.length === 0) {
                        showToast('error', 'Please add at least one slot to the Event List first.', 'List Empty');
                        // Scroll to the list area so they see it
                        $('html, body').animate({
                            scrollTop: $("#selected_dates_container").offset().top - 100
                        }, 500);
                        return false; 
                    }
                }

                var form = document.getElementById('myForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }

                if (!isMultiple) {
                    if (!$('#event_date').val()) {
                        showToast('error', 'Please select an event date.');
                        return false;
                    }
                    if (!$('#venue_building').val()) {
                        showToast('error', 'Please select a venue building.');
                        return false;
                    }
                    if (!$('#venue_room').val()) {
                        showToast('error', 'Please select a venue room.');
                        return false;
                    }
                }

                var formData = $("#myForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/api/UniversityEvent/store",
                    data: formData,
                    headers: {
                        Authorization: 'Bearer {{ auth()->user()->api_token }}',
                        Accept: 'application/json'
                    },
                    success: function(response) {

                        if (response.error == true) {
                            $('#msg').html(response.msg);
                            $("#msg").removeClass("text-success");
                            $("#msg").addClass("text-danger");
                            $('#msg').fadeIn('slow');
                            $('#msg').fadeOut(5000);

                        } else {
                            refreshFields();
                            $('#msg').html(response.msg);
                            $("#msg").removeClass("text-danger");
                            $("#msg").addClass("text-success");
                            $('#msg').fadeIn('slow');
                            $('#msg').fadeOut(5000);
                        }
                        loadEvents();
                    },
                    error: function(response, xhr, status, error) {
                        var data = response.responseJSON;

                        if (data && 'errors' in data) {
                            var errors = data.errors;
                            $.each(errors, function (i, obj) {
                                showToast('error', obj[0]);
                            });
                        } else if(data && data.msg) {
                             showToast('error', data.msg);
                        } else {
                            showToast('error', 'An unknown error occurred.');
                        }
                    }
                });
            });



        });

        function ProceedFurther() {
            $("#myForm").submit();
            $("#Modal").fadeOut();
        }

        function CloseModal() {
            $("#Modal").fadeOut();
            $("#tbody").empty();
        }

        function addOptionsToRoomSelect() {
         // Clear existing options
        $("#venue_room").empty();
        // Append default option
        $("#venue_room").append('<option disabled selected value="">Select Room</option>');
        $("#venue_room").append('<option value="103">Slass Ground</option>');
        $("#venue_room").append('<option value="104">football ground</option>');
        $("#venue_room").append('<option value="105">cricket ground</option>');
        $("#venue_room").append('<option value="106">sartaj aziz ground</option>');

}

        function loadRooms() {


        if ($("#venue_building").val() == 0) {
              //  addOptionsToRoomSelect();
             //return false;
        }

        var toDate = '';
            if($('#to_event_date').val() != ''){
                toDate = $('#to_event_date').val();
            }else{
                toDate = 'undefined';
            }



            $.ajax({
                type: "POST",
                url: "/api/UniversityEvent/get-room-list",

                headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
                data: {
                    building_code: $("#venue_building").val(),
                    date: $("#event_date").val(),
                    todate: toDate,
                    to: $("#end_time").val(),
                    from: $("#start_time").val(),
                },
                success: function(response) {
                    $("#venue_room").empty();
                    // $("#venue_room").append("<option disabled selected>Select Room</option><option value='0'>Other</option>");
                    var rooms = [
                { room_id: '103', room_name: 'Slass Ground' },
                { room_id: '104', room_name: 'Football Ground' },
                { room_id: '105', room_name: 'Cricket Ground' },
                { room_id: '106', room_name: 'Sartaj Aziz Ground' }
            ];

                        if($("#venue_building").val()==0){
                            rooms.forEach(function(room) {
                                $("#venue_room").append("<option value='" + room.room_id + "'>" + room.room_name + " - Free</option>");
                            });
                        }
                        if(response[0].is_occupied_by){
                            alert('ss'+$("#venue_building").val());
                            rooms.forEach(function(room) {
                            var reserved = response.some(function(item) {
                                return item.room_id === room.room_id;
                            });

                            if (response[0].room_id === room.room_id) {
                                $("#venue_room").append("<option disabled value='" + room.room_id + "'>" + room.room_name + " - Reserved</option>");
                            } else {

                                $("#venue_room").append("<option value='" + room.room_id + "'>" + room.room_name + " - Free</option>");
                            }

                        });
                        }else{
                             // NEW LOGIC: Use unified response (is_free, display_text)
                             if(response.length > 0) {
                                 response.forEach(function(room) {
                                     var isDisabled = room.is_free ? '' : 'disabled';
                                     
                                     // Fallback for legacy response if specific keys missing (safety check)
                                     var text = room.display_text ? room.display_text : (room.is_free ? "🟢 " + room.room_name + " (Available)" : "🔴 " + room.room_name + " (Reserved)");
                                     
                                     // Completely removed style attribute to use browser default
                                     $("#venue_room").append(`<option value="${room.room_id}" ${isDisabled}>${text}</option>`);
                                });
                             } else {
                                $("#venue_room").append('<option disabled selected>No rooms found</option>');
                             }
                        }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

        function removeUnderscores(string) {
            return string.replace(/_/g, '');
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }



        function loadEvents() {
            $('#table').DataTable().clear().draw();
            $("#event_table_body").empty();


            $.ajax({
                type: "POST",
                url: "/api/UniversityEvent/list",

                headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
                success: function(response) {
                    var html;
                    response.forEach(function(item) {
                        var dateObject = new Date(item.event_date);
                        let dateData = createDateData(item.event_date, item.to_event_date);
                        var start_time = item.event_start_time;
                        var end_time = item.event_end_time;
                        start_time = start_time.split(' ')[1];
                        end_time = end_time.split(' ')[1];
                        var rawStatus = (item.status == null ? "pending" : item.status.toLowerCase());
                        var statusBadge;
                        
                        if(rawStatus === 'approved') {
                            statusBadge = `<span class="badge badge-success px-2 py-1">${capitalizeFirstLetter(rawStatus)}</span>`;
                        } else {
                            statusBadge = `<span class="badge badge-warning px-2 py-1 text-white">${capitalizeFirstLetter(rawStatus)}</span>`;
                        }

                        var newData = [
                            item.id,
                            item.event_title,
                            dateData,
                            item.start_time_slot + ' - ' + item.end_time_slot,
                            item.room_name,
                            item.admin_details,
                            item.itrc_details,
                            `<button type="button" onclick="displayDetailsModal('${item.event_description}')" class="btn btn-primary btn-sm view-details-btn" data-details="${item.event_description}" data-toggle="modal" data-target="#eventDetailsModal">  <i class="fa fa-eye pr-1"></i>Description</button>`,

                            statusBadge,
                            '<button class="btn btn-danger btn-sm btn-delete"> <i class="fa fa-trash pr-1"></i>Delete</button>',
                            capitalizeFirstLetter(removeUnderscores(item.event_type))

                        ];
                        $('#table').DataTable().row.add(newData);
                        // html = html + "<tr><td>" + item.id + "</td><td>" + item.event_title +
                        //     "</td><td>" + item.event_date.replace(" 00:00:00", "") + "</td><td>" +
                        //     item.start_time_slot + ' - ' + item.end_time_slot + "</td><td>" + item
                        //     .room_name + "</td><td>" +
                        // capitalizeFirstLetter(removeUnderscores(item.event_type)) + "</td></tr>";
                    });
                    $('#table').DataTable().draw();




                    // $("#event_table_body").append(html);



                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }


        function displayDetailsModal(details) {
    // alert('Button clicked');
    // console.log(details);
    document.getElementById('eventDetailsBody').innerHTML = details;
    document.getElementById('eventDetailsModal').style.display = 'block';
}



$('#table').on('click', '.btn-delete', function () {
    var data = $('#table').DataTable().row($(this).parents('tr')).data();
    var eventId = data[0];

    // Make an AJAX call to delete the event
    $.ajax({
        url: '/api/UniversityEvent/delete', // Replace with your actual delete endpoint
        type: 'DELETE',
        data: { eventId: eventId },
        headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
        success: function (response) {
            // Handle success, e.g., remove the row from the table
            $('#table').DataTable().row($(this).parents('tr')).remove().draw();
            window.location.reload();
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error('Error deleting event:', error);
        }
    });
});



document.getElementById('add_admin_checkbox').addEventListener('change', function() {
    const container = document.getElementById('to_admin_container');
    const input = document.getElementById('to_admin_text');

    if (this.checked) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
        input.value = ''; 
    }
});

document.getElementById('add_itrc_checkbox').addEventListener('change', function() {
    const container = document.getElementById('to_itrc_container');
    const input = document.getElementById('to_itrc_text');

    if (this.checked) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
        input.value = '';
    }
});

function getRoomNameById(roomId) {
    var rooms = [
                { room_id: '103', room_name: 'Slass Ground' },
                { room_id: '104', room_name: 'Football Ground' },
                { room_id: '105', room_name: 'Cricket Ground' },
                { room_id: '106', room_name: 'Sartaj Aziz Ground' }
            ];

    let filteredRooms = rooms.filter(function(room) {
        return room.room_id === roomId;
    });

    // If a matching room is found, return its name
    if (filteredRooms.length > 0) {
        return filteredRooms[0].room_name;
    } else {
        // If no matching room is found, return null or handle it as needed
        return null;
    }
}
function changeDateFormat(date){
    var dateString  = '';
    if(date != null && date != 'null' ){
         dateString = date.split(' ')[0];
        return dateString;
    }else{
        return  dateString;
    }
}
function createDateData(fromDate , toData){
    let from = changeDateFormat(fromDate);
    let to = changeDateFormat(toData);
    var date = '';
    if(from != '' && to != ''){
        date = from+' to '+ to;
    }else{
        date = from;
    }
    return date;
}
function refreshFields(){
    $('#title').val('');
    $('#description').val('');
    $('#type').val('');
    // Reset Checkbox
    $('#add_to_date_checkbox').prop('checked', false).trigger('change');
    
    $('#event_date').val('');
    $('#start_time').val('');
    $('#end_time').val('');
    $('#venue_building').val('');
    $('#venue_room').val('');
    
    // Reset Multi fields
    scheduleList = [];
    batchDates = [];
    updateBatchDatesDisplay();
    updateScheduleTable();
    
    $('#add_admin_checkbox').prop('checked', false);
    $('#add_itrc_checkbox').prop('checked', false);
    $('#to_admin_text').val('');
    $('#to_itrc_text').val('');
    $("#school-div").addClass("d-none");
    $('input[type="checkbox"]').prop('checked', false);
}



// Handle min and max date for to and from date selection



    </script>
@stop
